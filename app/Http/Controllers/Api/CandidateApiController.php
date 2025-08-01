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

class CandidateApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('check-api-key');
    }

    /* ─────────────────────────  BLOQUES COMPLETOS  ───────────────────────── */

public function render($request, Throwable $e)
{
    // Si es una petición API (route prefix /api)
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
                'entidad','party','nomina',
                'departamento','municipio',
                'cargo','sexo'
            ])->get()
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            Candidate::with([
                'entidad','party','nomina',
                'departamento','municipio',
                'cargo','sexo'
            ])->findOrFail($id)
        );
    }

    /* ─────────────────────────  BLOQUES ESPECÍFICOS  ─────────────────────── */

    public function propuestas(int $id): JsonResponse
    {
        $c = Candidate::select(['id','propuestas'])->findOrFail($id);
        return response()->json($c);
    }

    public function fotografia(int $id): JsonResponse
    {
        $c   = Candidate::select(['id','fotografia','fotografia_original'])->findOrFail($id);
        $url = $c->fotografia ? Storage::url($c->fotografia) : null;

        return response()->json([
            'id'                 => $c->id,
            'fotografia'         => $c->fotografia,
            'fotografia_original'=> $c->fotografia_original,
            'url'                => $url,
        ]);
    }

    /* 🚩 NUEVOS ENDPOINTS */

/* ── Datos generales rápidos (nombre, identidad, posición, reelección) ── */
    public function datosGenerales(int $id): JsonResponse
    {
        $c = Candidate::with(['sexo'])
              ->select([
                  'id','numero_identidad','primer_nombre','segundo_nombre',
                  'primer_apellido','segundo_apellido','posicion',
                  'sexo_id','reeleccion','independiente'
              ])->findOrFail($id);

        return response()->json([
            'id'               => $c->id,
            'nombre_completo'  => $c->nombre_completo,
            'numero_identidad' => $c->numero_identidad,
            'posicion'         => $c->posicion,
            'sexo'             => $c->sexo->description,
            'reeleccion'       => $c->reeleccion,
            'independiente'    => $c->independiente,
        ]);
    }

/* ── Ubicación (departamento + municipio) ── */
    public function ubicacion(int $id): JsonResponse
    {
        $c = Candidate::with(['departamento','municipio'])
              ->select(['id','departamento_id','municipio_id'])
              ->findOrFail($id);

        return response()->json([
            'id'          => $c->id,
            'departamento'=> [
                'id'   => $c->departamento->id,
                'name' => $c->departamento->name,
            ],
            'municipio'   => [
                'id'   => $c->municipio->id,
                'name' => $c->municipio->name,
            ],
        ]);
    }

/* ── Sexo ── */
    public function sexo(int $id): JsonResponse
    {
        $c = Candidate::with('sexo')->select(['id','sexo_id'])->findOrFail($id);
        return response()->json([
            'id'   => $c->id,
            'sexo' => [
                'id'         => $c->sexo->id,
                'descripcion'=> $c->sexo->description,
            ],
        ]);
    }

/* ── Cargo ── */
    public function cargo(int $id): JsonResponse
    {
        $c = Candidate::with('cargo')->select(['id','cargo_id'])->findOrFail($id);
        return response()->json([
            'id'    => $c->id,
            'cargo' => [
                'id'   => $c->cargo->id,
                'name' => $c->cargo->name,
            ],
        ]);
    }

/* ── Partido (o indicador de independiente) ── */
    public function partido(int $id): JsonResponse
    {
        $c = Candidate::with('party')
              ->select(['id','party_id','independiente'])
              ->findOrFail($id);

        return response()->json([
            'id'            => $c->id,
            'independiente' => $c->independiente,
            'partido'       => $c->party ? [
                'id'   => $c->party->id,
                'name' => $c->party->name,
            ] : null,
        ]);
    }

/* ── Entidad ── */
    public function entidad(int $id): JsonResponse
    {
        $c = Candidate::with('entidad')->select(['id','entidad_id'])->findOrFail($id);
        return response()->json([
            'id'      => $c->id,
            'entidad' => $c->entidad ? [
                'id'   => $c->entidad->id,
                'name' => $c->entidad->name,
            ] : null,
        ]);
    }

        /* ──────────────────── BLOQUES ESPECÍFICOS POR NÚMERO_IDENTIDAD ─────────────────── */

    public function showByNumeroIdentidad(string $numero): JsonResponse
    {
        return response()->json(
            Candidate::with([
                'entidad','party','nomina',
                'departamento','municipio',
                'cargo','sexo'
            ])
            ->where('numero_identidad', $numero)
            ->firstOrFail()
        );
    }

    public function propuestasByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::select(['id','propuestas'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();
        return response()->json($c);
    }

    public function fotografiaByNumeroIdentidad(string $numero): JsonResponse
    {
        $c   = Candidate::select(['id','fotografia','fotografia_original'])
                ->where('numero_identidad', $numero)
                ->firstOrFail();
        $url = $c->fotografia
            ? Storage::url($c->fotografia)
            : null;

        return response()->json([
            'id'                 => $c->id,
            'fotografia'         => $c->fotografia,
            'fotografia_original'=> $c->fotografia_original,
            'url'                => $url,
        ]);
    }

    public function datosGeneralesByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('sexo')
              ->select([
                  'id','numero_identidad','primer_nombre','segundo_nombre',
                  'primer_apellido','segundo_apellido','posicion',
                  'sexo_id','reeleccion','independiente'
              ])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'               => $c->id,
            'nombre_completo'  => $c->nombre_completo,
            'numero_identidad' => $c->numero_identidad,
            'posicion'         => $c->posicion,
            'sexo'             => $c->sexo->description,
            'reeleccion'       => $c->reeleccion,
            'independiente'    => $c->independiente,
        ]);
    }

    public function ubicacionByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with(['departamento','municipio'])
              ->select(['id','departamento_id','municipio_id'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'          => $c->id,
            'departamento'=> [
                'id'   => $c->departamento->id,
                'name' => $c->departamento->name,
            ],
            'municipio'   => [
                'id'   => $c->municipio->id,
                'name' => $c->municipio->name,
            ],
        ]);
    }

    public function sexoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('sexo')
              ->select(['id','sexo_id'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'   => $c->id,
            'sexo' => [
                'id'         => $c->sexo->id,
                'descripcion'=> $c->sexo->description,
            ],
        ]);
    }

    public function cargoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('cargo')
              ->select(['id','cargo_id'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'    => $c->id,
            'cargo' => [
                'id'   => $c->cargo->id,
                'name' => $c->cargo->name,
            ],
        ]);
    }

    public function partidoByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('party')
              ->select(['id','party_id','independiente'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'            => $c->id,
            'independiente' => $c->independiente,
            'partido'       => $c->party ? [
                'id'   => $c->party->id,
                'name' => $c->party->name,
            ] : null,
        ]);
    }

    public function entidadByNumeroIdentidad(string $numero): JsonResponse
    {
        $c = Candidate::with('entidad')
              ->select(['id','entidad_id'])
              ->where('numero_identidad', $numero)
              ->firstOrFail();

        return response()->json([
            'id'      => $c->id,
            'entidad' => $c->entidad ? [
                'id'   => $c->entidad->id,
                'name' => $c->entidad->name,
            ] : null,
        ]);
    }

    /* ── FOTO POR ID ── */
public function foto(int $id): JsonResponse
{
    $p = Planilla::select(['id','nombre','foto'])->findOrFail($id);
    $url = $p->foto ? Storage::url($p->foto) : null;

    return response()->json([
        'id'     => $p->id,
        'nombre' => $p->nombre,
        'foto'   => $p->foto,
        'url'    => $url,
    ]);
}

/* ── FOTO POR NOMBRE DE PLANILLA ── */
public function fotoByNombre(string $nombre): JsonResponse
{
    $p = Planilla::select(['id','nombre','foto'])
        ->where('nombre', 'like', strtoupper($nombre))
        ->firstOrFail();

    $url = $p->foto ? Storage::url($p->foto) : null;

    return response()->json([
        'id'     => $p->id,
        'nombre' => $p->nombre,
        'foto'   => $p->foto,
        'url'    => $url,
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
            $c->url = $c->fotografia ? Storage::url($c->fotografia) : null;
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

// Planilla completa por ID
public function planilla(int $id): JsonResponse
{
    return response()->json(
        Planilla::with(['cargo','departamento','municipio'])
                ->findOrFail($id)
    );
}

// Solo foto + URL pública de una planilla por ID
public function planillaFoto(int $id): JsonResponse
{
    $p = Planilla::select(['id','nombre','foto'])->findOrFail($id);
    $url = $p->foto ? Storage::url($p->foto) : null;

    return response()->json([
        'id'     => $p->id,
        'nombre' => $p->nombre,
        'foto'   => $p->foto,
        'url'    => $url,
    ]);
}

// Datos generales (nombre + cargo) por ID
public function planillaDatosGenerales(int $id): JsonResponse
{
    $p = Planilla::with('cargo')
           ->select(['id','nombre','cargo_id'])
           ->findOrFail($id);

    return response()->json([
        'id'     => $p->id,
        'nombre' => $p->nombre,
        'cargo'  => [
            'id'   => $p->cargo->id,
            'name' => $p->cargo->name,
        ],
    ]);
}

// Ubicación (departamento + municipio) por ID
public function planillaUbicacion(int $id): JsonResponse
{
    $p = Planilla::with(['departamento','municipio'])
           ->select(['id','departamento_id','municipio_id'])
           ->findOrFail($id);

    return response()->json([
        'id'          => $p->id,
        'departamento'=> $p->departamento
                           ? ['id'=>$p->departamento->id,'name'=>$p->departamento->name]
                           : null,
        'municipio'   => $p->municipio
                           ? ['id'=>$p->municipio->id,'name'=>$p->municipio->name]
                           : null,
    ]);
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
             ->get()
            ->transform(function($p) {
                $p->url = $p->foto ? Storage::url($p->foto) : null;
                return $p;
            });

    return response()->json($list);
}


}
