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
    // GET /admin — panel principal con estadísticas básicas
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
        ]);
    }
}
