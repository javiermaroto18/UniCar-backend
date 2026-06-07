<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Panel de administración (Inertia + Vue 3, autenticación por sesión web)
|--------------------------------------------------------------------------
| Convive con la API REST (/api/v1, tokens Sanctum) sin interferir: el panel
| usa el guard 'web' (cookie de sesión) y exige el rol 'admin' de Spatie.
*/
Route::prefix('admin')->group(function () {
    // Acceso (público). 'login' es el nombre que usa el middleware Authenticate.
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Zona protegida: requiere sesión iniciada y rol admin
    Route::middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Gestión de usuarios
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Gestión de viajes
        Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
        Route::patch('/trips/{trip}/cancel', [TripController::class, 'cancel'])->name('trips.cancel');

        // Gestión de reservas
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        // Gestión de vehículos
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::patch('/vehicles/{vehicle}/toggle', [VehicleController::class, 'toggle'])->name('vehicles.toggle');
    });
});
