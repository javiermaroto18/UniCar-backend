<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\VehicleController;
use App\Http\Controllers\api\v1\TripController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas de autenticación publicas
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas por autenticación
    Route::middleware('auth:sanctum')->group(function () {
        
        // Ruta para obtener los datos del usuario autenticado
        Route::get('/me', [AuthController::class, 'me']);
        // Gestión de sesión
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // Rutas para la gestión de vehículos
        Route::get('/vehicles/me', [VehicleController::class, 'index']);
        Route::post('/vehicles', [VehicleController::class, 'store']);

        // Rutas para la gestión de viajes
        Route::get('/trips', [TripController::class, 'index']);
        Route::get('/trips/{id}', [TripController::class, 'show']);
        Route::post('/trips', [TripController::class, 'store']);
        Route::put('/trips/{id}', [TripController::class, 'update']);
        Route::patch('/trips/{id}/cancel', [TripController::class, 'cancel']);
    });
});