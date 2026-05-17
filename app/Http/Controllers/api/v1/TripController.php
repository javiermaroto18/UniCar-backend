<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\trip\StoreTripRequest;
use App\Http\Resources\api\v1\TripResource;
use App\Models\Booking;
use App\Http\Requests\api\v1\trip\UpdateTripRequest;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Exceptions\ForbiddenException;
use App\Exceptions\BadRequestException;

class TripController extends Controller
{
    // GET /api/v1/trips
    public function index(Request $request)
    {
        $query = Trip::query();

        // Solo viajes activos y que aún no hayan salido
        $query->where('status', 'scheduled')->where('departure_time', '>', now());

        // Filtro de Origen
        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%' . $request->origin . '%');
        }

        // Filtro de Destino
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        // Filtro de Fecha
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        // Filtro de Plazas mínimas requeridas
        if ($request->filled('seats')) {
            $query->where('seats_available', '>=', $request->seats);
        }

        $trips = $query->orderBy('departure_time', 'asc')->paginate(15);
        return TripResource::collection($trips);
    }

    // POST /api/v1/trips
    public function store(StoreTripRequest $request)
    {
        $user = $request->user();
        
        // Bloqueamos a los usuarios que no son conductores verificados
        if (!$user->is_verified_driver) {
            throw new ForbiddenException('No puedes publicar un viaje. Por favor, registra un vehículo primero.', 'FORBIDDEN');
        }

        $data = $request->validated();

        $trip = Trip::create([
            'driver_id' => $user->id,
            'vehicle_id' => $data['vehicle_id'],
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'departure_time' => $data['departure_time'],
            'seats_total' => $data['seats_total'],
            'seats_available' => $data['seats_total'],
            'price_per_seat' => $data['price_per_seat'],
            'status' => 'scheduled', 
        ]);

        return new TripResource($trip);
    }   

    // GET /api/v1/trips/{id}
    public function show($id)
    {
        // findOrFail ya lanza ModelNotFoundException, que tu bootstrap/app.php captura como 404
        $trip = Trip::findOrFail($id);
        return new TripResource($trip);
    }

    // PUT /api/v1/trips/{id}
    public function update(UpdateTripRequest $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $user = $request->user();

        // Validamos propiedad del viaje
        if ($trip->driver_id !== $user->id) {
            throw new ForbiddenException('No puedes editar un viaje que no es tuyo.', 'FORBIDDEN');
        }

        // Solo editamos viajes sin reservas
        if ($trip->seats_available < $trip->seats_total) {
            throw new ForbiddenException('No puedes editar este viaje porque ya hay pasajeros con reservas confirmadas.', 'TRIP_HAS_BOOKINGS');
        }

        $trip->update($request->validated());
        return new TripResource($trip);
    }

    // PATCH /api/v1/trips/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $user = $request->user();

        // Validar propiedad
        if ($trip->driver_id !== $user->id) {
            throw new ForbiddenException('No puedes cancelar un viaje que no es tuyo.', 'FORBIDDEN');
        }

        // Validar estado
        if ($trip->status === 'cancelled') {
            throw new BadRequestException('El viaje ya estaba cancelado.', 'BAD_REQUEST');
        }
        if ($trip->status === 'completed') {
            throw new BadRequestException('No puedes cancelar un viaje que ya ha sido completado.', 'BAD_REQUEST');
        }

        // Límite de 3 horas
        if (now()->addHours(3)->isAfter($trip->departure_time)) {
            throw new ForbiddenException('No puedes cancelar el viaje a menos de 3 horas de la salida.', 'TIME_LIMIT_EXCEEDED');
        }

        $trip->update(['status' => 'cancelled']);

        // Cancelación en cascada
        $bookings = Booking::where('trip_id', $trip->id)->get();
        foreach($bookings as $booking){
            $booking->update(['status' => 'cancelled']);
            // TODO: Enviar emails en Fase 2
        }
        
        return response()->json([
            'success' => true,
            'data' => new TripResource($trip)
        ]);
    }
}