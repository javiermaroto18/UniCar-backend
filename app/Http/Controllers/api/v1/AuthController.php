<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\api\v1\AuthResource;
use App\Services\api\v1\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Registrar un nuevo usuario
    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());
        return response()->json([
            'message' => 'Estudiante registrado con éxito.',
            'data' => new AuthResource($data),
        ], 201);
    }

    // Iniciar sesión y obtener token
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());
        return response()->json([
            'message' => 'Sesión iniciada correctamente.',
            'data' => new AuthResource($data),
        ], 200);
    }

    // Cerrar sesión del usuario actual (token actual)
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }

    // Cerrar sesión en todos los dispositivos 
    public function logoutAll(Request $request)
    {
        $this->authService->logoutAll($request->user());

        return response()->json([
            'message' => 'Sesión cerrada en todos los dispositivos de forma segura.',
        ], 200);
    }
}
