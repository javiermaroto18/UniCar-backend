<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\booking\StoreBookingRequest;
use App\Http\Resources\api\v1\BookingResource;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingController extends Controller
{
    // POST /api/v1/bookings
    public function store(StoreBookingRequest $request)
    {
        $user = $request->user();
        $trip = Trip::findOrFail($request->trip_id);
        
        // El conductor no puede reservar su propio viaje
        if($trip->driver_id == $user->id) {
            return response()->json([
                'succes' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'No puedes reservar un viaje en el que eres el conductor',
                    'data' => []
                ]
            ], 403);
        }

        // No se puede reservar si ya tiene una reserva activa para ese viaje
        $existingBooking = Booking::where('trip_id', $trip->id)
            ->where('passenger_id', $user->id)
            ->whereIn('status', 'confirmed')
            ->exists();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DUPLICATE_BOOKING',
                    'message' => 'Ya tienes una reserva activa para este viaje',
                    'data' => []
                ]
            ], 409);
        }

        // Control de plazas y transaccion segura
        try{
            $booking = DB::transaction(function() use ($trip, $user){
                $lookedTrip = Trip::where('id', $trip->id)->lockForUpdate()->first();
                if($lookedTrip->available_seats < 1){
                    throw new Exception('No quedan plazas disponibles para este viaje');
                }

                // Creamos la reserva
                $newBooking = Booking::create([
                    'trip_id' => $trip->id,
                    'passenger_id' => $user->id,
                    'seats_booked' => 1,
                    'total_price' => $lookedTrip->price_per_seat,
                    'status' => 'confirmed',
                ]);

                // Restamos las plazas disponibles del viaje
                $lookedTrip->decrement('seats_available', 1);

                return $newBooking;
             });
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNPROCESSABLE_ENTITY',
                    'message' => $e->getMessage(),
                    'data' => []
                ]
            ], 422);
        }

    }
}
