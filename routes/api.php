<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CandidateApiController;


Route::middleware(['check-api-key', 'throttle:candidates-api'])->group(function () {
    //PRUEBA JJ luego borrar
    Route::get('ping', function () {
        return response()->json(['message' => 'pong']);
    });

   /* Bloques completos */
    Route::get('candidates',          [CandidateApiController::class, 'all']);
    Route::get('candidates/{id}',     [CandidateApiController::class, 'show']);

    /* Bloques específicos */
    Route::get('candidates/{id}/propuestas',      [CandidateApiController::class, 'propuestas']);
    Route::get('candidates/{id}/fotografia',      [CandidateApiController::class, 'fotografia']);
    Route::get('candidates/{id}/datos-generales', [CandidateApiController::class, 'datosGenerales']);
    Route::get('candidates/{id}/ubicacion',       [CandidateApiController::class, 'ubicacion']);
    Route::get('candidates/{id}/sexo',            [CandidateApiController::class, 'sexo']);
    Route::get('candidates/{id}/cargo',           [CandidateApiController::class, 'cargo']);
    Route::get('candidates/{id}/partido',         [CandidateApiController::class, 'partido']);
    Route::get('candidates/{id}/entidad',         [CandidateApiController::class, 'entidad']);

    Route::get('identidad/{numero}',               [CandidateApiController::class, 'showByNumeroIdentidad']);
    Route::get('identidad/{numero}/propuestas',    [CandidateApiController::class, 'propuestasByNumeroIdentidad']);
    Route::get('identidad/{numero}/fotografia',    [CandidateApiController::class, 'fotografiaByNumeroIdentidad']);
    Route::get('identidad/{numero}/datos',         [CandidateApiController::class, 'datosGeneralesByNumeroIdentidad']);
    Route::get('identidad/{numero}/ubicacion',     [CandidateApiController::class, 'ubicacionByNumeroIdentidad']);
    Route::get('identidad/{numero}/sexo',          [CandidateApiController::class, 'sexoByNumeroIdentidad']);
    Route::get('identidad/{numero}/cargo',         [CandidateApiController::class, 'cargoByNumeroIdentidad']);
    Route::get('identidad/{numero}/partido',       [CandidateApiController::class, 'partidoByNumeroIdentidad']);
    Route::get('identidad/{numero}/entidad',       [CandidateApiController::class, 'entidadByNumeroIdentidad']);
});
