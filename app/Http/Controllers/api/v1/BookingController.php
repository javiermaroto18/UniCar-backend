<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\booking\StoreBookingRequest;
use App\Http\Resources\api\v1\BookingResource;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ConflictException;
use App\Exceptions\UnprocessableEntityException;
use App\Exceptions\BadRequestException;

class BookingController extends Controller
{
    // GET /api/v1/bookings/me
    public function index(Request $request)
    {
        $user = $request->user();

        // Obtenemos las reservas del usuario autenticado, incluyendo los datos del viaje y el conductor
        $bookings = Booking::where('passenger_id', $user->id)
            ->with('trip.driver') 
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return BookingResource::collection($bookings);
    }

    // POST /api/v1/bookings
    public function store(StoreBookingRequest $request)
    {
        $user = $request->user();
        $trip = Trip::findOrFail($request->trip_id);
        
        // El conductor no puede reservar su propio viaje
        if($trip->driver_id == $user->id) {
            throw new ForbiddenException('No puedes reservar un viaje en el que eres el conductor', 'FORBIDDEN');
        }

        // No se puede reservar si ya tiene una reserva activa para ese viaje
        $existingBooking = Booking::where('trip_id', $trip->id)
            ->where('passenger_id', $user->id)
            ->whereIn('status', ['confirmed'])
            ->exists();

        if ($existingBooking) {
            throw new ConflictException('Ya tienes una reserva activa para este viaje', 'DUPLICATE_BOOKING');
        }

        // Control de plazas disponibles
        if ($trip->seats_available < 1) {
            throw new UnprocessableEntityException('No quedan plazas disponibles en este viaje.', 'UNPROCESSABLE_ENTITY');
        }

        // Creamos la reserva
        $booking = Booking::create([
            'trip_id' => $trip->id,
            'passenger_id' => $user->id,
            'seats_booked' => 1,
            'total_price' => $trip->price_per_seat,
            'status' => 'confirmed',
        ]);

        // Decrementamos las plazas disponibles del viaje
        $trip->decrement('seats_available', 1);
        return new BookingResource($booking);

    }

    // PATCH /api/v1/bookings/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $booking = Booking::with('trip')->findOrFail($id);

        // Validamos que la reserva pertenece al pasajero autenticado
        if ($booking->passenger_id !== $user->id) {
            throw new ForbiddenException('No puedes cancelar una reserva que no es tuya.', 'FORBIDDEN');
        }

        // Validamos que la reserva no esté ya cancelada
        if ($booking->status === 'cancelled') {
            throw new BadRequestException('La reserva ya estaba cancelada.', 'BAD_REQUEST');
        }

        // Validamos que la cancelación se realice al menos 3 horas antes de la salida del viaje
        if (now()->addHours(3)->isAfter($booking->trip->departure_time)) {
            throw new ForbiddenException('No puedes cancelar la reserva a menos de 3 horas de la salida. Si no te presentas, se aplicarán las sanciones correspondientes.', 'TIME_LIMIT_EXCEEDED');
        }

        // Cancelamos la reserva
        $booking->update(['status' => 'cancelled']);

        // Buscamos el viaje y le sumamos 1 a las plazas disponibles
        $trip = Trip::findOrFail($booking->trip_id);
        $trip->increment('seats_available', 1);

        // AQUI HAY QUE METER EL CODIGO PARA ENVIAR EMAIL DE CANCELACIÓN AL CONDUCTOR

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking)
        ]);
    }
}
