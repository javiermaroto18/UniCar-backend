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
        // Devolvemos los vehículos del usuario autenticado
        $vehicles = Vehicle::where('user_id', $request->user()->id)->get();

        return VehicleResource::collection($vehicles); // Formateamos la respuesta
    }

    // POST /api/v1/vehicles
    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user(); // Gracias a Sanctum, aquí ya tenemos el usuario autenticado

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'brand' => $data['brand'],
            'model' => $data['model'],
            'color' => $data['color'],
            'license_plate' => $data['license_plate'],
        ]);

        // Si el usuario no es un conductor verificado, lo marcamos como tal al registrar su primer vehículo
        if (! $user->is_verified_driver) {
            $user->update([
                'is_verified_driver' => true
            ]);
        }

        return new VehicleResource($vehicle);
    }
}