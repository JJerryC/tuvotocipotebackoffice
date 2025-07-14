<?php

namespace App\Http\Controllers;

use App\Models\{Candidate, Party, Entidad, Departamento, Municipio, Cargo, Sexo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal
     */
    public function index()
    {
        $estadisticas = $this->getEstadisticasGenerales();
        $candidatosCarrusel = $this->getCandidatosCarrusel();
        $datosGraficos = $this->getDatosGraficos();

        return view('dashboard.index', compact('estadisticas', 'candidatosCarrusel', 'datosGraficos'));
    }

    /**
     * Vista de candidatos con filtros
     */
    public function candidatos(Request $request)
    {
        $query = Candidate::with(['party', 'entidad', 'departamento', 'municipio', 'cargo', 'sexo']);

        // Aplicar filtros
        if ($request->filled('tipo_candidato')) {
            $query->where('tipo_candidato', $request->tipo_candidato);
        }

        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->departamento_id);
        }

        if ($request->filled('municipio_id')) {
            $query->where('municipio_id', $request->municipio_id);
        }

        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }

        if ($request->filled('independiente')) {
            $query->where('independiente', $request->independiente);
        }

        if ($request->filled('perfil_completo')) {
            $query->where('perfil_completo', $request->perfil_completo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('primer_nombre', 'like', "%{$search}%")
                  ->orWhere('primer_apellido', 'like', "%{$search}%")
                  ->orWhere('numero_identidad', 'like', "%{$search}%");
            });
        }

        $candidatos = $query->paginate(12);

        // Datos para filtros
        $partidos = Party::orderBy('name')->get();
        $departamentos = Departamento::orderBy('name')->get();
        $municipios = collect();

        if ($request->filled('departamento_id')) {
            $municipios = Municipio::where('departamento_id', $request->departamento_id)
                                  ->orderBy('name')->get();
        }

        return view('dashboard.candidatos', compact('candidatos', 'partidos', 'departamentos', 'municipios'));
    }

    /**
     * Reportería avanzada
     */
    public function reporteria()
    {
        $estadisticas = $this->getEstadisticasDetalladas();
        $evolucionRegistros = $this->getEvolucionRegistros();
        $mapaCalor = $this->getMapaCalor();

        return view('dashboard.reporteria', compact('estadisticas', 'evolucionRegistros', 'mapaCalor'));
    }

    /**
     * Mapa electoral interactivo
     */
    public function mapa()
    {
        $candidatosPorDepartamento = Candidate::select('departamento_id', 'tipo_candidato', DB::raw('count(*) as total'))
            ->whereNotNull('departamento_id')
            ->with('departamento')
            ->groupBy('departamento_id', 'tipo_candidato')
            ->get()
            ->groupBy('departamento.name');

        return view('dashboard.mapa', compact('candidatosPorDepartamento'));
    }

    /**
     * API: Obtener municipios por departamento
     */
    public function getMunicipios($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)
                              ->orderBy('name')
                              ->get(['id', 'name']);

        return response()->json($municipios);
    }

    /**
     * API: Estadísticas en tiempo real
     */
    public function getStats()
    {
        return response()->json($this->getEstadisticasGenerales());
    }

    /**
     * Estadísticas generales para el dashboard
     */
    private function getEstadisticasGenerales()
    {
        $totalCandidatos = Candidate::count();

        return [
            'total_candidatos' => $totalCandidatos,
            'candidatos_presidenciales' => Candidate::where('tipo_candidato', 'presidencial')->count(),
            'candidatos_diputados' => Candidate::where('tipo_candidato', 'diputado')->count(),
            'candidatos_alcaldes' => Candidate::where('tipo_candidato', 'alcalde')->count(),
            'total_partidos' => Party::count(),
            'candidatos_independientes' => Candidate::where('independiente', true)->count(),
            'perfiles_completos' => Candidate::where('perfil_completo', true)->count(),
            'perfiles_incompletos' => Candidate::where('perfil_completo', false)->count(),
            'mujeres' => Candidate::where('genero', 'femenino')->count(),
            'hombres' => Candidate::where('genero', 'masculino')->count(),
            'porcentaje_completado_promedio' => round(Candidate::avg('porcentaje_completado') ?? 0, 2),
        ];
    }

    /**
     * Candidatos para el carrusel
     */
    private function getCandidatosCarrusel()
    {
        return Candidate::with(['party', 'entidad', 'departamento', 'cargo'])
            ->where('perfil_completo', true)
            ->whereNotNull('fotografia')
            ->whereNotNull('propuestas')
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    /**
     * Datos para gráficos
     */
    private function getDatosGraficos()
    {
        return [
            'candidatos_por_tipo' => Candidate::select('tipo_candidato', DB::raw('count(*) as total'))
                ->whereNotNull('tipo_candidato')
                ->groupBy('tipo_candidato')
                ->get(),

            'candidatos_por_departamento' => Candidate::select('departamentos.name as departamento', DB::raw('count(*) as total'))
                ->join('departamentos', 'candidates.departamento_id', '=', 'departamentos.id')
                ->groupBy('departamentos.id', 'departamentos.name')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),

            'candidatos_por_genero' => Candidate::select('genero', DB::raw('count(*) as total'))
                ->whereNotNull('genero')
                ->groupBy('genero')
                ->get(),

            'candidatos_por_partido' => Candidate::select('parties.name as partido', DB::raw('count(*) as total'))
                ->join('parties', 'candidates.party_id', '=', 'parties.id')
                ->groupBy('parties.id', 'parties.name')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Estadísticas detalladas para reportería
     */
    private function getEstadisticasDetalladas()
    {
        return [
            'por_departamento' => Candidate::select('departamentos.name as departamento', 'tipo_candidato', DB::raw('count(*) as total'))
                ->join('departamentos', 'candidates.departamento_id', '=', 'departamentos.id')
                ->groupBy('departamentos.id', 'departamentos.name', 'tipo_candidato')
                ->get(),

            'por_partido' => Party::with('candidates')->get()->map(function($partido) {
                return [
                    'partido' => $partido,
                    'estadisticas' => $partido->getEstadisticas()
                ];
            }),

            'completitud_perfiles' => Candidate::select(
                DB::raw('CASE
                    WHEN porcentaje_completado >= 80 THEN "Completo"
                    WHEN porcentaje_completado >= 50 THEN "Parcial"
                    ELSE "Incompleto"
                END as estado'),
                DB::raw('count(*) as total')
            )->groupBy('estado')->get(),
        ];
    }

    /**
     * Evolución de registros en el tiempo
     */
    private function getEvolucionRegistros()
    {
        return Candidate::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();
    }

    /**
     * Datos para mapa de calor
     */
    private function getMapaCalor()
    {
        return Candidate::select('departamentos.name as departamento', DB::raw('count(*) as intensidad'))
            ->join('departamentos', 'candidates.departamento_id', '=', 'departamentos.id')
            ->groupBy('departamentos.id', 'departamentos.name')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->departamento => $item->intensidad];
            });
    }

    /**
     * Actualizar datos automáticamente
     */
    public function actualizarDatosAutomaticos()
    {
        $candidatos = Candidate::all();

        foreach ($candidatos as $candidato) {
            $candidato->actualizarCamposAutomaticos();
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos actualizados correctamente',
            'candidatos_actualizados' => $candidatos->count()
        ]);
    }
}
