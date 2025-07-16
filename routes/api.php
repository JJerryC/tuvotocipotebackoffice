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
});
