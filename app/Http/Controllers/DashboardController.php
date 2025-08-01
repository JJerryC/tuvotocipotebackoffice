<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\Party;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Cargo;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard.index', [
            'estadisticas'       => $this->estadisticasGenerales(),
            'candidatosCarrusel' => $this->candidatosCarrusel(),
            'datosGraficos'      => $this->datosGraficos(),
        ]);
    }

public function candidatos(Request $r)
{
    $q = Candidate::with(['party','departamento','municipio','cargo','sexo']);

    if ($r->filled('cargo_id') && is_numeric($r->cargo_id)) {
        $q->where('cargo_id', $r->cargo_id);
    }

    if ($r->filled('departamento_id') && is_numeric($r->departamento_id)) {
        $q->where('departamento_id', $r->departamento_id);
    }

    if ($r->filled('municipio_id') && is_numeric($r->municipio_id)) {
        $q->where('municipio_id', $r->municipio_id);
    }

    if ($r->filled('party_id') && is_numeric($r->party_id)) {
        $q->where('party_id', $r->party_id);
    }

    if ($r->filled('sexo_id') && is_numeric($r->sexo_id)) {
        $q->where('sexo_id', $r->sexo_id);
    }

    if ($r->filled('reeleccion')) {
        $reeleccion = filter_var($r->reeleccion, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($reeleccion !== null) {
            $q->where('reeleccion', $reeleccion);
        }
    }

    if ($r->filled('search')) {
        $s = $r->search;
        $q->where(function ($w) use ($s) {
            $w->where('primer_nombre', 'like', "%$s%")
            ->orWhere('primer_apellido', 'like', "%$s%")
            ->orWhere('numero_identidad', 'like', "%$s%")
            ->orWhereHas('departamento', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%");
            })
            ->orWhereHas('municipio', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%");
            })
            ->orWhereHas('party', function ($q) use ($s) {
                $q->where('name', 'like', "%$s%");
            });
        });
    }

    return view('dashboard.candidatos', [
        'candidatos'    => $q->paginate(12),
        'partidos'      => Party::orderBy('name')->get(),
        'departamentos' => Departamento::orderBy('name')->get(),
        'municipios'    => $r->filled('departamento_id')
                             ? Municipio::where('departamento_id', $r->departamento_id)->orderBy('name')->get()
                             : collect(),
    ]);
}

public function reporteria()
{
    // Totales por tipo de candidato
    $candidatosPresidenciales = Candidate::whereIn('cargo_id', $this->cargosIds('Presidente'))->count();
    $candidatosDiputados = Candidate::whereIn('cargo_id', $this->cargosIds('Diputado'))->count();
    $candidatosAlcaldes = Candidate::whereIn('cargo_id', $this->cargosIds('Alcalde'))->count();
    $totalCandidatos = $candidatosPresidenciales + $candidatosDiputados + $candidatosAlcaldes;

    $estadisticasPorDepartamento = Candidate::selectRaw('departamentos.name as departamento, tipo_candidato, COUNT(*) as total')
        ->join('departamentos', 'candidates.departamento_id', '=', 'departamentos.id')
        ->groupBy('departamentos.name', 'tipo_candidato')
        ->get();

    if ($estadisticasPorDepartamento->isEmpty()) {
        $estadisticasPorDepartamento = collect([
            (object)[
                'departamento' => 'N/A',
                'tipo_candidato' => 'N/A',
                'total' => 1
            ]
        ]);
    }

    // Completitud de perfiles
    $completitud = Candidate::selectRaw("CASE 
            WHEN fotografia IS NULL OR fotografia = '' THEN 'Sin fotografía'
            WHEN propuestas IS NULL OR propuestas = '' THEN 'Sin propuestas'
            ELSE 'Perfil completo' END as estado, COUNT(*) as total")
        ->groupBy('estado')
        ->get();

    // Por partido
    $porPartido = Party::withCount(['candidates as estadisticas_total_candidatos' => function ($q) {
        $q->select(\DB::raw("count(*)"));
    }])->get()->map(function ($partido) {
        return [
            'partido' => $partido,
            'estadisticas' => [
                'total_candidatos' => $partido->estadisticas_total_candidatos
            ]
        ];
    });

    // Timeline para gráfico
    $evolucionRegistros = Candidate::selectRaw("DATE(created_at) as fecha, COUNT(*) as total")
        ->groupByRaw("DATE(created_at)")
        ->orderBy('fecha')
        ->get();

    return view('dashboard.reporteria', [
        'estadisticas' => [
            'candidatos_presidenciales' => $candidatosPresidenciales,
            'candidatos_diputados' => $candidatosDiputados,
            'candidatos_alcaldes' => $candidatosAlcaldes,
            'total_candidatos' => $totalCandidatos,
            'por_departamento' => collect($estadisticasPorDepartamento),
            'completitud_perfiles' => $completitud,
            'por_partido' => $porPartido,
        ],
        'evolucionRegistros' => $evolucionRegistros
    ]);
}

    public function mapa()
    {
        $data = Candidate::select('departamento_id','cargo_id',DB::raw('count(*) total'))
            ->whereNotNull('departamento_id')
            ->with('departamento')
            ->groupBy('departamento_id','cargo_id')
            ->get()
            ->groupBy('departamento.name');
        return view('dashboard.mapa', ['candidatosPorDepartamento'=>$data]);
    }

    public function getMunicipios($id)
    {
        return response()->json(
            Municipio::where('departamento_id',$id)->orderBy('name')->get(['id','name'])
        );
    }

    public function getStats()
    {
        return response()->json($this->estadisticasGenerales());
    }

private function cargosIds(string $nombre): array
{
    $normalizar = function ($texto) {
        $texto = strtolower($texto);
        $texto = preg_replace('/\s+/', ' ', $texto); // elimina espacios extras
        $texto = preg_replace('/[()]/', '', $texto); // elimina paréntesis
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $texto
        );
        return trim($texto);
    };

    $nombreNormalizado = $normalizar($nombre);

    $cargos = \App\Models\Cargo::all();

    $ids = [];

    foreach ($cargos as $cargo) {
        $nombreCargo = $normalizar($cargo->name);
        if (str_contains($nombreCargo, $nombreNormalizado)) {
            $ids[] = $cargo->id;
        }
    }

    return $ids;
}

    private function estadisticasGenerales(): array
    {
        $pres = $this->cargosIds('Presidente');
        $dip  = $this->cargosIds('Diputado');
        $alc  = $this->cargosIds('Alcalde');
        $total = Candidate::count();
        $completos = Candidate::whereNotNull('fotografia')
            ->whereNotNull('propuestas')
            ->count();

        return [
            'total_candidatos'          => $total,
            'candidatos_presidenciales' => Candidate::whereIn('cargo_id', $pres)->count(),
            'candidatos_diputados'      => Candidate::whereIn('cargo_id', $dip)->count(),
            'candidatos_alcaldes'       => Candidate::whereIn('cargo_id', $alc)->count(),
            'hombres'                   => Candidate::whereHas('sexo', fn($q) => $q->where('code', 'M'))->count(),
            'mujeres'                   => Candidate::whereHas('sexo', fn($q) => $q->where('code', 'F'))->count(),
            'perfiles_completos'        => $completos,
            'perfiles_incompletos'      => $total - $completos,
            'total_partidos'            => Party::count(),
        ];
    }

    private function candidatosCarrusel()
    {
        return Candidate::with(['party','departamento','cargo'])
            ->whereNotNull('fotografia')
            ->whereNotNull('propuestas')
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

private function datosGraficos(): array
{
    return [
        'candidatos_por_genero' => Candidate::select('sexos.description as genero', DB::raw('count(*) as total'))
            ->join('sexos','candidates.sexo_id','=','sexos.id')
            ->groupBy('sexos.id','sexos.description')
            ->get(),
        'candidatos_por_departamento' => Candidate::select('departamentos.name as departamento', DB::raw('count(*) as total'))
            ->join('departamentos','candidates.departamento_id','=','departamentos.id')
            ->groupBy('departamentos.id','departamentos.name')
            ->orderBy('total','desc')
            ->limit(10)
            ->get(),
    ];
}

    private function estadisticasDetalladas(): array
    {
        return [
            'por_departamento' => Candidate::select('departamentos.name as departamento','cargo_id',DB::raw('count(*) as total'))
                ->join('departamentos','candidates.departamento_id','=','departamentos.id')
                ->groupBy('departamentos.id','departamentos.name','cargo_id')
                ->get(),
            'por_partido' => Party::withCount('candidates')->get(),
            'completitud_perfiles' => collect([
                ['estado'=>'Completo','total'=>Candidate::whereNotNull('fotografia')->whereNotNull('propuestas')->count()],
                ['estado'=>'Incompleto','total'=>Candidate::whereNull('fotografia')->orWhereNull('propuestas')->count()],
            ]),
        ];
    }

    private function evolucionRegistros()
    {
        return Candidate::select(DB::raw('date(created_at) as fecha'), DB::raw('count(*) as total'))
            ->where('created_at','>=',Carbon::now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    }

    private function mapaCalor()
    {
        return Candidate::select('departamentos.name as departamento', DB::raw('count(*) as intensidad'))
            ->join('departamentos','candidates.departamento_id','=','departamentos.id')
            ->groupBy('departamentos.id','departamentos.name')
            ->get()
            ->mapWithKeys(fn($r)=>[$r->departamento=>$r->intensidad]);
    }

    public function actualizarDatosAutomaticos()
    {
        Candidate::all()->each->actualizarCamposAutomaticos();
        return response()->json(['success'=>true]);
    }
}