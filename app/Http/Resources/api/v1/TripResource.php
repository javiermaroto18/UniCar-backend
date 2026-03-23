<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Devolvemos los campos del viaje, incluyendo información del conductor
            'id' => $this->id,
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'departure_time' => $this->departure_time,
            'seats_total' => $this->seats_total,
            'seats_available' => $this->seats_available,
            'price_per_seat' => $this->price_per_seat,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Información del conductor
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'avatar' => $this->driver->avatar,
            ]
        ];
    }
}
