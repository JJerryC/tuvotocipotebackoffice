<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CandidateApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('check-api-key');
    }

    /* ─────────────────────────  BLOQUES COMPLETOS  ───────────────────────── */

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
        $url = $c->fotografia ? Storage::disk('public')->url($c->fotografia) : null;

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
}
