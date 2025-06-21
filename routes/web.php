<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

// Redirige la ruta raíz al login
Route::redirect('/', '/login');

// Rutas de autenticación (login, registro, logout…)
Auth::routes();

// Agrupa bajo autenticación todas las rutas que necesiten estar protegidas
Route::middleware('auth')->group(function () {
    // Dashboard o home tras login
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});
