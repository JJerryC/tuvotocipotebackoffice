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
use App\Http\Controllers\DashboardController;

use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Party;
use App\Models\Departamento;

// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación
Auth::routes();

// Rutas API públicas
Route::get('/api/partidos/{party}/entidades', function (Party $party) {
    return response()->json($party->entidades()->select('id', 'name')->get());
});

Route::get('/api/departamentos/{departamento}/municipios', function (Departamento $departamento) {
    return response()->json($departamento->municipios()->select('id', 'name')->get());
});

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

    // Página principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /**
     * DASHBOARD MODERNO
     */
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/candidatos', [DashboardController::class, 'candidatos'])->name('candidatos');
        Route::get('/reporteria', [DashboardController::class, 'reporteria'])->name('reporteria');
        Route::get('/mapa', [DashboardController::class, 'mapa'])->name('mapa');

        // APIs para el dashboard
        Route::get('/api/municipios/{departamento}', [DashboardController::class, 'getMunicipios'])->name('api.municipios');
        Route::get('/api/stats', [DashboardController::class, 'getStats'])->name('api.stats');
        Route::post('/api/actualizar-datos', [DashboardController::class, 'actualizarDatosAutomaticos'])->name('api.actualizar-datos');
    });

    /**
     * IMPORTACIÓN DE CANDIDATOS
     */
    Route::get('/candidates/import', [CandidateImportController::class, 'index'])->name('candidates.import');
    Route::post('/candidates/preview', [CandidateImportController::class, 'preview'])->name('candidates.preview');
    Route::post('/candidates/import-confirm', [CandidateImportController::class, 'import'])->name('candidates.import.confirm');
    Route::post('/candidates/start-import', [CandidateImportController::class, 'startImport'])->name('candidates.start-import');
    Route::get('/candidates/import-progress/{sessionId}', [CandidateImportController::class, 'getImportProgress'])->name('candidates.import-progress');
    Route::post('/candidates/import-batch/{sessionId}', [CandidateImportController::class, 'importBatch'])->name('candidates.import-batch');
    Route::post('/candidates/clear-database', [CandidateImportController::class, 'clearDatabase'])->name('candidates.clear-database');

    /**
     * CANDIDATOS
     */
    // Usando middleware 'can' para permisos
    Route::middleware('can:manage candidates')->group(function () {
        Route::resource('candidates', CandidateController::class);
    });

    // Exportación confidencial (permiso específico)
    Route::middleware('can:view confidential candidates')->get('/candidates/export-confidential', [CandidateController::class, 'exportConfidential'])->name('candidates.export-confidential');

    /**
     * USUARIOS (solo admin)
     */
    // Middleware 'role' para rol admin — asegúrate que lo tienes creado y registrado
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);

        // RUTAS PARA ROLES
        Route::get ('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get ('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get ('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put ('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    /**
     * CATÁLOGOS
     */
    Route::resource('parties', PartyController::class);
    Route::resource('entidades', EntidadController::class)->parameters([
        'entidades' => 'entidad'
    ]);
    Route::resource('nominas', NominaController::class);
    Route::resource('cargos', CargoController::class);
});
