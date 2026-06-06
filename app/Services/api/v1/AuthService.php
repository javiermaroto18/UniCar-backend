<?php

namespace App\Services\api\v1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    // Registro de usuario
    public function register(array $data): array
    {
        // Validación de datos
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), 
        ]);

        $user->assignRole('student');

        $token = $user->createToken('api-token-v1')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // Login de usuario
    public function login(array $data): array
    {
        // Si el usuario no existe o la contraseña (hasheada) no coincide, lanzamos excepción
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Las credenciales no son correctas.'],]);
        }

        $token = $user->createToken('api-token-v1')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // Logout de usuario
    public function logout(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }

    // Logout de usuario en todos los dispositivos
    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }
}