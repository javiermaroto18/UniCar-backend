<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\api\v1\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/v1/users
    public function index(Request $request)
    {
        // Verificar si el usuario autenticado es administrador
        /*if (!$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                    'data' => []
                ]
            ], 403);
        }*/

        $users = User::paginate(15);        
        return UserResource::collection($users);
    }
}