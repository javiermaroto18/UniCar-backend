<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Devolvemos el usuario autenticado y el token de acceso
            'user' => new UserResource($this['user']),
            'token' => $this['token'],
        ];
    }
}
