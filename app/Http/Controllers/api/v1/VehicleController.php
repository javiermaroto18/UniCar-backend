<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\vehicle\StoreVehicleRequest;
use App\Http\Requests\api\v1\vehicle\UpdateVehicleRequest;
use App\Http\Resources\api\v1\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // GET /api/v1/vehicles/me
    public function index(Request $request)
    {
        $vehicles = Vehicle::where('user_id', $request->user()->id)
                           ->where('is_active', true)
                           ->get();
        return VehicleResource::collection($vehicles);
    }

    // POST /api/v1/vehicles
    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        
        if (isset($data['is_frequent']) && $data['is_frequent']) {
            Vehicle::where('user_id', $user->id)->update(['is_frequent' => false]); // Si este coche se marca como frecuente, quitamos la marca a los demás
        }

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'brand_model' => $data['brand'] . ' ' . $data['model'],
            'license_plate' => $data['license_plate'],
            'is_frequent' => $data['is_frequent'] ?? false, 
            'is_active' => true,
        ]);

        if (! $user->is_verified_driver) {
            $user->update(['is_verified_driver' => true]);
        }

        return new VehicleResource($vehicle);
    }

    // PUT /api/v1/vehicles/{id}
    public function update(UpdateVehicleRequest $request, $id) 
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validated();

        if (isset($data['is_frequent']) && $data['is_frequent']) {
            Vehicle::where('user_id', $user->id)
                   ->where('id', '!=', $vehicle->id)
                   ->update(['is_frequent' => false]);
        }

        $vehicle->update([
            'brand_model' => $data['brand'] . ' ' . $data['model'],
            'is_frequent' => $data['is_frequent'] ?? $vehicle->is_frequent,
        ]);

        return new VehicleResource($vehicle);
    }

    // DELETE /api/v1/vehicles/{id}
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);

        $vehicle->update([
            'is_active' => false,
            'is_frequent' => false // Por si era el frecuente, se lo quitamos
        ]);

        return response()->json(['message' => 'Vehículo desactivado correctamente'], 200);
    }
}