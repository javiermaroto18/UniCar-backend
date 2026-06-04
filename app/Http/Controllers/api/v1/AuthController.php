<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\auth\LoginRequest;
use App\Http\Requests\api\v1\auth\RegisterRequest;
use App\Http\Resources\api\v1\AuthResource;
use App\Http\Resources\api\v1\UserResource;
use App\Services\api\v1\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Exceptions\ConflictException;

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

    // Obtener el usuario autenticado
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    // Actualizar el perfil del usuario (Nombre, Email de notificaciones y Avatar)
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'notification_email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Máximo 2MB
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                // Extraemos la ruta relativa de la URL completa
                $oldPath = str_replace(url('storage') . '/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public'); //Almacenamos la foto en la carpeta de avatars dentro de \storage\app\public\avatars
            $user->avatar = url('storage/' . $path); // Asignamos la nueva URL al usuario
        }

        $user->name = $request->name;
        
        if ($request->has('notification_email')) {
            if (empty($request->notification_email) || $request->notification_email === 'null') {
                $user->notification_email = null;
            } else {
                $user->notification_email = $request->notification_email;
            }
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente.',
            'data' => new UserResource($user)
        ]);
    }

    // Actualizar las preferencias de viaje del usuario
    public function updatePreferences(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'preferences' => 'required|array',
        ]);

        $user->preferences = $request->preferences;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferencias actualizadas correctamente.',
            'data' => new UserResource($user)
        ]);
    }

    // Cambiar la contraseña del usuario, endpoint
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($request->new_password === $request->current_password) {
            throw new ConflictException('La contraseña actual es incorrecta.', 'INVALID_CURRENT_PASSWORD');
        }

        if (!password_verify($request->current_password, $user->password)) {
            throw new ConflictException('La contraseña actual es incorrecta.', 'INVALID_CURRENT_PASSWORD');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);

    }
}