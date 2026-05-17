<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\vehicle\StoreVehicleRequest;
use App\Http\Resources\api\v1\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // GET /api/v1/vehicles/me
    public function index(Request $request)
    {
        $vehicles = Vehicle::where('user_id', $request->user()->id)->get();
        return VehicleResource::collection($vehicles);
    }

    // POST /api/v1/vehicles
    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'brand_model' => $data['brand'] . ' ' . $data['model'],
            'license_plate' => $data['license_plate'],
            'is_frequent' => $data['is_frequent'] ?? false, 
        ]);

        if (! $user->is_verified_driver) {
            $user->update(['is_verified_driver' => true]);
        }

        return new VehicleResource($vehicle);
    }
}