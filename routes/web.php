<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CandidateImportController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanillaController;
use App\Models\Municipio;

// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación (login, registro, logout…)
Auth::routes();

// Rutas que necesitan autenticación
Route::middleware('auth')->group(function () {

    // HOME
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // DASHBOARD
Route::prefix('dashboard')->name('dashboard.')
    ->middleware('can:view reports')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/candidatos', [DashboardController::class, 'candidatos'])->name('candidatos');
        Route::get('/reporteria', [DashboardController::class, 'reporteria'])->name('reporteria');
        Route::get('/mapa', [DashboardController::class, 'mapa'])->name('mapa');

        Route::get('/api/municipios/{departamento}', [DashboardController::class, 'getMunicipios'])->name('api.municipios');
        Route::get('/api/stats', [DashboardController::class, 'getStats'])->name('api.stats');
        Route::post('/api/actualizar-datos', [DashboardController::class, 'actualizarDatosAutomaticos'])->name('api.actualizar-datos');
    });

    // Rutas AJAX para selects dependientes (entidades por partido, municipios por departamento)
Route::middleware('auth')->group(function () {
    Route::get('/api/entidades/{party}', [CandidateController::class, 'getEntidadesByParty'])
        ->name('api.entidades.byParty');

    Route::get('/api/municipios/{departamento}', [CandidateController::class, 'getMunicipiosByDepartamento'])
        ->name('api.municipios.byDepartamento');

            // ✅ Nueva ruta para filtrar planillas
    Route::get('/api/planillas/filtrar', [PlanillaController::class, 'filtrar'])
        ->name('api.planillas.filtrar');
});


    // Rutas para importación de candidatos
    Route::get('/candidates/import', [CandidateImportController::class, 'index'])->name('candidates.import');
    Route::post('/candidates/preview', [CandidateImportController::class, 'preview'])->name('candidates.preview');
    Route::post('/candidates/import-confirm', [CandidateImportController::class, 'import'])->name('candidates.import.confirm');
    
    // Nuevas rutas para importación con progreso
    Route::post('/candidates/start-import', [CandidateImportController::class, 'startImport'])->name('candidates.start-import');
    Route::get('/candidates/import-progress/{sessionId}', [CandidateImportController::class, 'getImportProgress'])->name('candidates.import-progress');
    Route::post('/candidates/import-batch/{sessionId}', [CandidateImportController::class, 'importBatch'])->name('candidates.import-batch');

      Route::get('/candidates', [CandidateController::class, 'index'])
        ->middleware('can:view candidates')
        ->name('candidates.index');
        
    Route::get('/candidates/create', [CandidateController::class, 'create'])
        ->middleware('can:create candidates')
        ->name('candidates.create');
        
    Route::post('/candidates', [CandidateController::class, 'store'])
        ->middleware('can:create candidates')
        ->name('candidates.store');
        
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])
        ->middleware('can:view candidates')
        ->name('candidates.show');
        
    Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])
        ->middleware('can:edit candidates')
        ->name('candidates.edit');
        
    Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])
        ->middleware('can:edit candidates')
        ->name('candidates.update');
        
    Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])
        ->middleware('can:edit candidates')
        ->name('candidates.destroy');


    // USUARIOS
    Route::middleware('can:view users')->get('/users', [UserController::class, 'index'])->name('users.index');

    Route::middleware('can:create users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('can:edit users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ROLES
    Route::middleware('can:view roles')->get('/roles', [RoleController::class, 'index'])->name('roles.index');

    Route::middleware('can:create roles')->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('can:edit roles')->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::middleware('can:delete roles')->group(function () {
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // CATÁLOGOS (Mantenimiento)
    Route::middleware('can:view maintenance')->group(function () {
        Route::get('/parties', [PartyController::class, 'index'])->name('parties.index');
        Route::get('/entidades', [EntidadController::class, 'index'])->name('entidades.index');
        Route::get('/nominas', [NominaController::class, 'index'])->name('nominas.index');
        Route::get('/cargos', [CargoController::class, 'index'])->name('cargos.index');
        Route::get('/planillas', [PlanillaController::class, 'index'])->name('planillas.index');
    });

    Route::middleware('can:create maintenance')->group(function () {
        Route::get('/parties/create', [PartyController::class, 'create'])->name('parties.create');
        Route::post('/parties', [PartyController::class, 'store'])->name('parties.store');

        Route::get('/entidades/create', [EntidadController::class, 'create'])->name('entidades.create');
        Route::post('/entidades', [EntidadController::class, 'store'])->name('entidades.store');

        Route::get('/nominas/create', [NominaController::class, 'create'])->name('nominas.create');
        Route::post('/nominas', [NominaController::class, 'store'])->name('nominas.store');

        Route::get('/cargos/create', [CargoController::class, 'create'])->name('cargos.create');
        Route::post('/cargos', [CargoController::class, 'store'])->name('cargos.store');

        Route::get('/planillas/create', [PlanillaController::class, 'create'])->name('planillas.create');
        Route::post('/planillas', [PlanillaController::class, 'store'])->name('planillas.store');
    });

    Route::middleware('can:edit maintenance')->group(function () {
        // Partidos
        Route::get('/parties/{party}/edit', [PartyController::class, 'edit'])->name('parties.edit');
        Route::put('/parties/{party}', [PartyController::class, 'update'])->name('parties.update');
        Route::delete('/parties/{party}', [PartyController::class, 'destroy'])->name('parties.destroy');

        // Entidades
        Route::get('/entidades/{entidad}/edit', [EntidadController::class, 'edit'])->name('entidades.edit');
        Route::put('/entidades/{entidad}', [EntidadController::class, 'update'])->name('entidades.update');
        Route::delete('/entidades/{entidad}', [EntidadController::class, 'destroy'])->name('entidades.destroy');

        // Nominaciones
        Route::get('/nominas/{nomina}/edit', [NominaController::class, 'edit'])->name('nominas.edit');
        Route::put('/nominas/{nomina}', [NominaController::class, 'update'])->name('nominas.update');
        Route::delete('/nominas/{nomina}', [NominaController::class, 'destroy'])->name('nominas.destroy');

        // Cargos
        Route::get('/cargos/{cargo}/edit', [CargoController::class, 'edit'])->name('cargos.edit');
        Route::put('/cargos/{cargo}', [CargoController::class, 'update'])->name('cargos.update');
        Route::delete('/cargos/{cargo}', [CargoController::class, 'destroy'])->name('cargos.destroy');

        // Planillas
        Route::get('/planillas/{planilla}/edit', [PlanillaController::class, 'edit'])->name('planillas.edit');
        Route::put('/planillas/{planilla}', [PlanillaController::class, 'update'])->name('planillas.update');
        Route::delete('/planillas/{planilla}', [PlanillaController::class, 'destroy'])->name('planillas.destroy');
    });

    
});