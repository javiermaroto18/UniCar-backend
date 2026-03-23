<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            
            // Campos para el Front
            'avatar' => $this->avatar,
            'notification_email' => $this->notification_email,
            'is_verified_driver' => $this->is_verified_driver,
            
            // Convertimos a fecha "YYYY-MM-DD"
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
