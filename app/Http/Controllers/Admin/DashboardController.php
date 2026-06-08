<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Inertia\Inertia;

class DashboardController extends Controller
{
    // GET /admin — panel principal con estadísticas
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'trips' => Trip::count(),
                'bookings' => Booking::count(),
                'vehicles' => Vehicle::where('is_active', true)->count(),
            ],
            'tripsByStatus' => [
                'scheduled' => Trip::where('status', 'scheduled')->count(),
                'completed' => Trip::where('status', 'completed')->count(),
                'cancelled' => Trip::where('status', 'cancelled')->count(),
            ],
            'bookingsByStatus' => [
                'pending' => Booking::where('status', 'pending')->count(),
                'paid' => Booking::where('status', 'paid')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
            ],
            // Últimos viajes publicados para una vista rápida de actividad
            'recentTrips' => Trip::with('driver:id,name')
                ->latest()
                ->take(6)
                ->get()
                ->map(fn (Trip $t) => [
                    'id' => $t->id,
                    'route' => $t->origin . ' → ' . $t->destination,
                    'driver' => $t->driver?->name,
                    'status' => $t->status,
                    'departure_time' => $t->departure_time,
                ]),
        ]);
    }
}
