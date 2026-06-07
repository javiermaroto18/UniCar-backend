<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    // GET /admin/bookings — listado con filtro por estado
    public function index(Request $request)
    {
        $status = $request->input('status', '');

        $bookings = Booking::query()
            ->with(['passenger:id,name', 'trip:id,origin,destination,departure_time'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Booking $b) => [
                'id' => $b->id,
                'passenger' => $b->passenger?->name,
                'trip' => $b->trip
                    ? $b->trip->origin . ' → ' . $b->trip->destination
                    : '—',
                'departure_time' => $b->trip?->departure_time,
                'seats_booked' => $b->seats_booked,
                'total_price' => $b->total_price,
                'status' => $b->status,
                'created_at' => $b->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => ['status' => $status],
        ]);
    }

    // PATCH /admin/bookings/{booking}/cancel — cancelar reserva y liberar plaza
    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'La reserva ya estaba cancelada.');
        }

        $booking->update(['status' => 'cancelled']);

        if ($booking->trip) {
            $booking->trip->increment('seats_available', $booking->seats_booked);
        }

        return back()->with('success', 'Reserva cancelada y plaza liberada.');
    }
}
