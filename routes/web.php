<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CandidateImportController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\NominaController;

// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación (login, logout, etc.)
Auth::routes();

// Rutas protegidas por auth
Route::middleware('auth')->group(function () {

    // Página principal después del login
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Importación de candidatos (no requieren permisos extra)
    Route::get('/candidates/import', [CandidateImportController::class, 'index'])->name('candidates.import');
    Route::post('/candidates/preview', [CandidateImportController::class, 'preview'])->name('candidates.preview');
    Route::post('/candidates/import-confirm', [CandidateImportController::class, 'import'])->name('candidates.import.confirm');
    Route::post('/candidates/start-import', [CandidateImportController::class, 'startImport'])->name('candidates.start-import');
    Route::get('/candidates/import-progress/{sessionId}', [CandidateImportController::class, 'getImportProgress'])->name('candidates.import-progress');
    Route::post('/candidates/import-batch/{sessionId}', [CandidateImportController::class, 'importBatch'])->name('candidates.import-batch');
    Route::post('/candidates/clear-database', [CandidateImportController::class, 'clearDatabase'])->name('candidates.clear-database');

    // Rutas protegidas con permiso específico (Spatie)
    Route::middleware('can:manage candidates')->group(function () {
        Route::resource('candidates', CandidateController::class);
    });

    //Rutas protegidas para Excel
    Route::middleware(['auth', 'can:view confidential candidates'])->get('/candidates/export-confidential', [CandidateController::class, 'exportConfidential'])->name('candidates.export-confidential');

    // Solo para usuarios con rol admin (Spatie)
    Route::middleware('role:admin')->group(function () {
        // Rutas para usuarios
        Route::resource('users', UserController::class)
              ->only(['index', 'create', 'store', 'edit', 'update']);

        // Rutas para roles
        Route::get ('roles',                 [RoleController::class, 'index'])->name('roles.index');
        Route::get ('roles/create',          [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles',                 [RoleController::class, 'store'])->name('roles.store');
        Route::get ('roles/{role}/edit',     [RoleController::class, 'edit'])->name('roles.edit');
        Route::put ('roles/{role}',          [RoleController::class, 'update'])->name('roles.update');
    });

    // Rutas generales (solo requieren estar autenticado)
    Route::resource('parties', PartyController::class);
    Route::resource('entidades', EntidadController::class)->parameters([
        'entidades' => 'entidad' // parámetro personalizado
    ]);
    Route::resource('nominas', NominaController::class);
    Route::resource('cargos', CargoController::class);
});
