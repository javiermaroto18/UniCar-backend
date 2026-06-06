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
            // Información del conductor
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'avatar' => $this->driver->avatar,
                'is_verified_driver' => $this->driver->is_verified_driver,
            ],
            'vehicle' => [
                'id' => $this->vehicle->id,
                'brand_model' => $this->vehicle->brand_model,
                'license_plate' => $this->vehicle->license_plate,
            ],
            'origin' => $this->origin,
            'destination' => $this->destination,
            'departure_time' => $this->departure_time,
            'seats_total' => $this->seats_total,
            'seats_available' => $this->seats_available,
            'price_per_seat' => $this->price_per_seat,
            'status' => $this->status,
            // Añadido para mostrar las reservas asociadas al viaje, con información del pasajero
            'bookings' => $this->whenLoaded('bookings'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
    