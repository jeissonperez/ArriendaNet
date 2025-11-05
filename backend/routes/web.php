<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ContratosController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\SolicitudesController;
use App\Http\Controllers\CuentasCobroController;

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Ruta del dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas de contratos
    Route::resource('contratos', ContratosController::class);

    // Rutas de pagos
    Route::resource('pagos', PagosController::class);

    // Rutas de solicitudes de mantenimiento
    Route::resource('solicitudes', SolicitudesController::class);

    // Rutas de cuentas de cobro
    Route::resource('cuentas-cobro', CuentasCobroController::class);
});

Route::get('/', function () {
    // Redirigir a la ruta 'dashboard' para que se aplique el middleware 'auth'
    return redirect()->route('dashboard');
});
