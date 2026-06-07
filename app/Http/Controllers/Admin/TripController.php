<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TripController extends Controller
{
    // GET /admin/trips — listado con filtro por estado y búsqueda
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $trips = Trip::query()
            ->with('driver:id,name')
            ->withCount('bookings')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            })
            ->orderByDesc('departure_time')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Trip $t) => [
                'id' => $t->id,
                'driver' => $t->driver?->name,
                'origin' => $t->origin,
                'destination' => $t->destination,
                'departure_time' => $t->departure_time,
                'seats_total' => $t->seats_total,
                'seats_available' => $t->seats_available,
                'price_per_seat' => $t->price_per_seat,
                'status' => $t->status,
                'bookings_count' => $t->bookings_count,
            ]);

        return Inertia::render('Admin/Trips/Index', [
            'trips' => $trips,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    // PATCH /admin/trips/{trip}/cancel — cancelar viaje y sus reservas en cascada
    public function cancel(Trip $trip)
    {
        if ($trip->status === 'cancelled') {
            return back()->with('error', 'El viaje ya estaba cancelado.');
        }
        if ($trip->status === 'completed') {
            return back()->with('error', 'No se puede cancelar un viaje completado.');
        }

        $trip->update(['status' => 'cancelled']);

        Booking::where('trip_id', $trip->id)
            ->where('status', '!=', 'cancelled')
            ->update(['status' => 'cancelled']);

        return back()->with('success', 'Viaje cancelado y reservas asociadas anuladas.');
    }
}
