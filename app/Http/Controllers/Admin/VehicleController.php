<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    // GET /admin/vehicles — listado con búsqueda
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $vehicles = Vehicle::query()
            ->with('user:id,name')
            ->withCount('trips')
            ->when($search, function ($q) use ($search) {
                $q->where('brand_model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Vehicle $v) => [
                'id' => $v->id,
                'owner' => $v->user?->name,
                'brand_model' => $v->brand_model,
                'license_plate' => $v->license_plate,
                'is_frequent' => $v->is_frequent,
                'is_active' => $v->is_active,
                'trips_count' => $v->trips_count,
            ]);

        return Inertia::render('Admin/Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => ['search' => $search],
        ]);
    }

    // PATCH /admin/vehicles/{vehicle}/toggle — activar/desactivar vehículo
    public function toggle(Vehicle $vehicle)
    {
        $vehicle->update([
            'is_active' => ! $vehicle->is_active,
            'is_frequent' => $vehicle->is_active ? false : $vehicle->is_frequent,
        ]);

        return back()->with('success', 'Estado del vehículo actualizado.');
    }
}
