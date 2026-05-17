<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\BookingController;
use App\Http\Controllers\api\v1\VehicleController;
use App\Http\Controllers\api\v1\TripController;
use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas de autenticación publicas
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas por autenticación
    Route::middleware('auth:sanctum')->group(function () {
        
        // Ruta para obtener los datos del usuario autenticado
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']); // Actualización de perfil

        // Rutas para la gestión de usuarios (solo para administradores)
        Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');

        // Gestión de sesión
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // Rutas para la gestión de vehículos
        Route::get('/vehicles/me', [VehicleController::class, 'index']);
        Route::post('/vehicles', [VehicleController::class, 'store']);

        // Rutas para la gestión de viajes
        Route::get('/trips', [TripController::class, 'index']);
        Route::get('/trips/me', [TripController::class, 'myTrips']);
        Route::get('/trips/{id}', [TripController::class, 'show']);
        Route::post('/trips', [TripController::class, 'store']);
        Route::put('/trips/{id}', [TripController::class, 'update']);
        Route::patch('/trips/{id}/cancel', [TripController::class, 'cancel']);

        // Rutas para la gestión de reservas
        Route::get('/bookings/me', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
        
    });
});