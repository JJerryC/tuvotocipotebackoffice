<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Planilla;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use App\Helpers\ApiResponseHelper;

class CandidateApiController extends Controller
{
    use ApiResponseHelper;

    public function __construct()
    {
        $this->middleware('check-api-key');
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'error'   => 'Not Found',
                    'message' => 'Recurso no encontrado',
                ], 404);
            }

            return response()->json([
                'error'   => 'Internal Error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return parent::render($request, $e);
    }

    public function all(): JsonResponse
    {
        return response()->json(
            Candidate::with([
                'entidad', 'party', 'nomina',
                'departamento', 'municipio',
                'cargo', 'sexo'
            ])->get()
        );
    }

    // MÉTODOS CON findOrFail cambiados a findOrJson404

    public function show(int $id): JsonResponse
    {
        $candidate = $this->findOrJson404(Candidate::class, $id, [
            'entidad', 'party', 'nomina',
            'departamento', 'municipio',
            'cargo', 'sexo'
        ]);
        if ($candidate instanceof \Illuminate\Http\JsonResponse) return $candidate;
        return response()->json($candidate);
    }

    public function propuestas(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, [], ['id', 'propuestas']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json($c);
    }

    public function fotografia(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, [], ['id', 'fotografia', 'fotografia_original']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'fotografia' => $c->fotografia ? asset('storage/' . $c->fotografia) : null,
            'fotografia_original' => $c->fotografia_original,
        ]);
    }

    public function datosGenerales(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['sexo'], [
            'id', 'numero_identidad', 'primer_nombre', 'segundo_nombre',
            'primer_apellido', 'segundo_apellido', 'posicion',
            'sexo_id', 'reeleccion', 'independiente'
        ]);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'nombre_completo' => $c->nombre_completo,
            'numero_identidad' => $c->numero_identidad,
            'posicion' => $c->posicion,
            'sexo' => $c->sexo->description,
            'reeleccion' => $c->reeleccion,
            'independiente' => $c->independiente,
        ]);
    }

    public function ubicacion(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['departamento', 'municipio'], ['id', 'departamento_id', 'municipio_id']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'departamento' => [
                'id' => $c->departamento->id,
                'name' => $c->departamento->name,
            ],
            'municipio' => [
                'id' => $c->municipio->id,
                'name' => $c->municipio->name,
            ],
        ]);
    }

    public function sexo(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['sexo'], ['id', 'sexo_id']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'sexo' => [
                'id' => $c->sexo->id,
                'descripcion' => $c->sexo->description,
            ],
        ]);
    }

    public function cargo(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['cargo'], ['id', 'cargo_id']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'cargo' => [
                'id' => $c->cargo->id,
                'name' => $c->cargo->name,
            ],
        ]);
    }

    public function partido(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['party'], ['id', 'party_id', 'independiente']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'independiente' => $c->independiente,
            'partido' => $c->party ? [
                'id' => $c->party->id,
                'name' => $c->party->name,
            ] : null,
        ]);
    }

    public function entidad(int $id): JsonResponse
    {
        $c = $this->findOrJson404(Candidate::class, $id, ['entidad'], ['id', 'entidad_id']);
        if ($c instanceof \Illuminate\Http\JsonResponse) return $c;
        return response()->json([
            'id' => $c->id,
            'entidad' => $c->entidad ? [
                'id' => $c->entidad->id,
                'name' => $c->entidad->name,
            ] : null,
        ]);
    }

    public function foto(int $id): JsonResponse
    {
        $p = $this->findOrJson404(Planilla::class, $id, [], ['id', 'nombre', 'foto']);
        if ($p instanceof \Illuminate\Http\JsonResponse) return $p;
        return response()->json([
            'id' => $p->id,
            'nombre' => $p->nombre,
            'foto' => $p->fotografia ? asset('storage/' . $p->fotografia) : null,
        ]);
    }

    public function planilla(int $id): JsonResponse
    {
        $planilla = $this->findOrJson404(Planilla::class, $id, ['cargo', 'departamento', 'municipio']);
        if ($planilla instanceof \Illuminate\Http\JsonResponse) return $planilla;
        return response()->json($planilla);
    }

    public function planillaFoto(int $id): JsonResponse
    {
        $p = $this->findOrJson404(Planilla::class, $id, [], ['id', 'nombre', 'foto']);
        if ($p instanceof \Illuminate\Http\JsonResponse) return $p;
        return response()->json([
            'id' => $p->id,
            'nombre' => $p->nombre,
            'foto' => $p->foto,
            'url' => $p->foto ? asset('storage/' . $p->foto) : null,
        ]);
    }

    public function planillaDatosGenerales(int $id): JsonResponse
    {
        $p = $this->findOrJson404(Planilla::class, $id, ['cargo'], ['id', 'nombre', 'cargo_id']);
        if ($p instanceof \Illuminate\Http\JsonResponse) return $p;
        return response()->json([
            'id' => $p->id,
            'nombre' => $p->nombre,
            'cargo' => [
                'id' => $p->cargo->id,
                'name' => $p->cargo->name,
            ],
        ]);
    }

    public function planillaUbicacion(int $id): JsonResponse
    {
        $p = $this->findOrJson404(Planilla::class, $id, ['departamento', 'municipio'], ['id', 'departamento_id', 'municipio_id']);
        if ($p instanceof \Illuminate\Http\JsonResponse) return $p;
        return response()->json([
            'id' => $p->id,
            'departamento' => $p->departamento ? ['id' => $p->departamento->id, 'name' => $p->departamento->name] : null,
            'municipio' => $p->municipio ? ['id' => $p->municipio->id, 'name' => $p->municipio->name] : null,
        ]);
    }

    public function candidatosByPlanillaId(int $id): JsonResponse
    {
        $planilla = $this->findOrJson404(Planilla::class, $id, ['candidates.entidad', 'candidates.party', 'candidates.cargo']);
        if ($planilla instanceof \Illuminate\Http\JsonResponse) return $planilla;
        return response()->json([
            'planilla_id' => $planilla->id,
            'nombre' => $planilla->nombre,
            'candidatos' => $planilla->candidates,
        ]);
    }

    // MÉTODOS CON firstOrFail cambiados a first() + 404 JSON

    public function showByNumeroIdentidad(string $numero): JsonResponse
    {
        $candidate = Candidate::with([
            'entidad', 'party', 'nomina',
            'departamento', 'municipio',
            'cargo', 'sexo'
        ])->where('numero_identidad', $numero)->first();

        if (!$candidate) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json($candidate);
    }

    public function propuestasByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::select(['id', 'propuestas'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json($c);
    }

    public function fotografiaByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::select(['id', 'fotografia', 'fotografia_original'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'fotografia' => $c->fotografia ? asset('storage/' . $c->fotografia) : null,
            'fotografia_original' => $c->fotografia_original,
        ]);
    }

    public function datosGeneralesByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('sexo')
            ->select([
                'id', 'numero_identidad', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'posicion',
                'sexo_id', 'reeleccion', 'independiente'
            ])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'nombre_completo' => $c->nombre_completo,
            'numero_identidad' => $c->numero_identidad,
            'posicion' => $c->posicion,
            'sexo' => $c->sexo->description,
            'reeleccion' => $c->reeleccion,
            'independiente' => $c->independiente,
        ]);
    }

    public function ubicacionByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with(['departamento', 'municipio'])
            ->select(['id', 'departamento_id', 'municipio_id'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'departamento' => [
                'id' => $c->departamento->id,
                'name' => $c->departamento->name,
            ],
            'municipio' => [
                'id' => $c->municipio->id,
                'name' => $c->municipio->name,
            ],
        ]);
    }

    public function sexoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('sexo')
            ->select(['id', 'sexo_id'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'sexo' => [
                'id' => $c->sexo->id,
                'descripcion' => $c->sexo->description,
            ],
        ]);
    }

    public function cargoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('cargo')
            ->select(['id', 'cargo_id'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'cargo' => [
                'id' => $c->cargo->id,
                'name' => $c->cargo->name,
            ],
        ]);
    }

    public function partidoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('party')
            ->select(['id', 'party_id', 'independiente'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'independiente' => $c->independiente,
            'partido' => $c->party ? [
                'id' => $c->party->id,
                'name' => $c->party->name,
            ] : null,
        ]);
    }

    public function entidadByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('entidad')
            ->select(['id', 'entidad_id'])
            ->where('numero_identidad', $numero)
            ->first();

        if (!$c) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Recurso no encontrado',
            ], 404);
        }

        return response()->json([
            'id' => $c->id,
            'entidad' => $c->entidad ? [
                'id' => $c->entidad->id,
                'name' => $c->entidad->name,
            ] : null,
        ]);
    }

/* ──────────────────── BLOQUES ESPECÍFICOS POR NOMBRE (LIKE) ─────────────────── */

public function showByNombre(string $nombre): JsonResponse
{
    return response()->json(
        Candidate::with(['entidad','party','nomina','departamento','municipio','cargo','sexo'])
            ->whereRaw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) LIKE ?", ["%{$nombre}%"])
            ->orWhere('primer_nombre', 'like', "%{$nombre}%")
            ->orWhere('primer_apellido', 'like', "%{$nombre}%")
            ->get()
    );
}

public function propuestasByNombre(string $nombre): JsonResponse
{
    $c = Candidate::select(['id','propuestas'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($c);
}

public function fotografiaByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::select(['id','primer_nombre','primer_apellido','fotografia','fotografia_original'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

        $candidatos->transform(function($c) {
            $c->url = $c->fotografia ? asset('storage/' . $c->fotografia) : null;
            return $c;
        });

    return response()->json($candidatos);
}

public function datosGeneralesByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with('sexo')
        ->select(['id','numero_identidad','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido','posicion','sexo_id','reeleccion','independiente'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'               => $c->id,
            'nombre_completo'  => $c->nombre_completo,
            'numero_identidad' => $c->numero_identidad,
            'posicion'         => $c->posicion,
            'sexo'             => $c->sexo->description,
            'reeleccion'       => $c->reeleccion,
            'independiente'    => $c->independiente,
        ];
    }));
}

public function ubicacionByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with(['departamento','municipio'])
        ->select(['id','departamento_id','municipio_id'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'          => $c->id,
            'departamento'=> $c->departamento ? ['id'=>$c->departamento->id,'name'=>$c->departamento->name] : null,
            'municipio'   => $c->municipio ? ['id'=>$c->municipio->id,'name'=>$c->municipio->name] : null,
        ];
    }));
}

public function sexoByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with('sexo')
        ->select(['id','sexo_id'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'   => $c->id,
            'sexo' => ['id'=>$c->sexo->id,'descripcion'=>$c->sexo->description],
        ];
    }));
}

public function cargoByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with('cargo')
        ->select(['id','cargo_id'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'    => $c->id,
            'cargo' => ['id'=>$c->cargo->id,'name'=>$c->cargo->name],
        ];
    }));
}

public function partidoByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with('party')
        ->select(['id','party_id','independiente'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'            => $c->id,
            'independiente' => $c->independiente,
            'partido'       => $c->party ? ['id'=>$c->party->id,'name'=>$c->party->name] : null,
        ];
    }));
}

public function entidadByNombre(string $nombre): JsonResponse
{
    $candidatos = Candidate::with('entidad')
        ->select(['id','entidad_id'])
        ->where('primer_nombre','like',"%{$nombre}%")
        ->orWhere('primer_apellido','like',"%{$nombre}%")
        ->get();

    return response()->json($candidatos->map(function($c){
        return [
            'id'      => $c->id,
            'entidad' => $c->entidad ? ['id'=>$c->entidad->id,'name'=>$c->entidad->name] : null,
        ];
    }));
}

// Lista todas las planillas con relaciones
public function planillas(): JsonResponse
{
    return response()->json(
        Planilla::with(['cargo','departamento','municipio'])
                ->get()
    );
}

// Búsqueda LIKE por nombre: todas las planillas que contengan $texto
public function planillasByNombre(string $texto): JsonResponse
{
    return response()->json(
        Planilla::with(['cargo','departamento','municipio'])
                ->where('nombre', 'like', "%{$texto}%")
                ->orderBy('nombre')
                ->get()
    );
}

// Fotos + URLs de planillas buscadas por nombre
public function planillasFotosByNombre(string $texto): JsonResponse
{
    $list = Planilla::select(['id','nombre','foto'])
             ->where('nombre','like',"%{$texto}%")
             ->orderBy('nombre')
             ->get();

    return response()->json($list);
}

}
