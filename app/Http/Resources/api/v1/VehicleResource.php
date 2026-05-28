<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'brand_model' => $this->brand_model,
            'license_plate' => $this->license_plate,
            'is_frequent' => (bool) $this->is_frequent,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
