<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CandidateImportController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\NominaController;
use App\Models\Entidad;
use App\Models\Municipio;
use App\Models\Party;
use App\Models\Departamento;


// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación (login, registro, logout…)
Auth::routes();

Route::get('/api/partidos/{party}/entidades', function (Party $party) {
    // Retorna entidades relacionadas al partido
    return response()->json($party->entidades()->select('id', 'name')->get());
});

Route::get('/api/departamentos/{departamento}/municipios', function (Departamento $departamento) {
    // Retorna municipios relacionados al departamento
    return response()->json($departamento->municipios()->select('id', 'name')->get());
});

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

    // Protegidas con Spatie → solo usuarios con permiso "manage candidates"
    Route::resource('candidates', CandidateController::class)
      ->middleware(['auth', 'can:manage candidates']);


      Route::middleware(['auth', 'role:admin'])   // sólo admins
     ->resource('users', UserController::class)
     ->only(['index', 'edit', 'update']);   // no creamos ni borramos usuarios aquí

    Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get ('roles',                 [RoleController::class, 'index'])->name('roles.index');
    Route::get ('roles/{role}/edit',     [RoleController::class, 'edit'])->name('roles.edit');
    Route::put ('roles/{role}',          [RoleController::class, 'update'])->name('roles.update');
});

    Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get ('roles',              [RoleController::class, 'index' ])->name('roles.index');
    Route::get ('roles/create',       [RoleController::class, 'create'])->name('roles.create');   // 🔹 nuevo
    Route::post('roles',              [RoleController::class, 'store' ])->name('roles.store');    // 🔹 nuevo
    Route::get ('roles/{role}/edit',  [RoleController::class, 'edit'  ])->name('roles.edit');
    Route::put ('roles/{role}',       [RoleController::class, 'update'])->name('roles.update');
});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::resource('users', UserController::class)
          ->only(['index','create','store','edit','update']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('entidades', EntidadController::class)->parameters([
    'entidades' => 'entidad'  // aquí defines que el parámetro se llame {entidad}
]);
    Route::resource('nominas', NominaController::class);
    Route::resource('cargos', CargoController::class);
});

});
