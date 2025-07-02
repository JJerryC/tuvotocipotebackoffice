<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CandidateImportController;
use Illuminate\Support\Facades\Auth;

// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación (login, registro, logout…)
Auth::routes();

// Agrupa bajo autenticación todas las rutas que necesiten estar protegidas
Route::middleware('auth')->group(function () {
    // Dashboard o home tras login
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Rutas para importación de candidatos
    Route::get('/candidates/import', [CandidateImportController::class, 'index'])->name('candidates.import');
    Route::post('/candidates/preview', [CandidateImportController::class, 'preview'])->name('candidates.preview');
    Route::post('/candidates/import-confirm', [CandidateImportController::class, 'import'])->name('candidates.import.confirm');
    
    // Nuevas rutas para importación con progreso
    Route::post('/candidates/start-import', [CandidateImportController::class, 'startImport'])->name('candidates.start-import');
    Route::get('/candidates/import-progress/{sessionId}', [CandidateImportController::class, 'getImportProgress'])->name('candidates.import-progress');
    Route::post('/candidates/import-batch/{sessionId}', [CandidateImportController::class, 'importBatch'])->name('candidates.import-batch');
    
    // Ruta para limpiar base de datos
    Route::post('/candidates/clear-database', [CandidateImportController::class, 'clearDatabase'])->name('candidates.clear-database');
});
