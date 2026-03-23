<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\trip\StoreTripRequest;
use App\Http\Resources\api\v1\TripResource;
use App\Models\Booking;
use App\Http\Requests\api\v1\trip\UpdateTripRequest;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // GET /api/v1/trips
    public function index(Request $request)
    {
        $query = Trip::query();

        // Reglas base obligatorias: Solo viajes activos y que aún no hayan salido
        $query -> where('status', 'active') -> where('departure_time', '>', now());

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
        // Bloqueamos a los usuarios que no son conductores verificados para que no puedan publicar viajes
        if (!$user->is_verified_driver) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'No puedes publicar un viaje. Por favor, registra un vehículo primero.',
                    'data' => []
                ]
            ], 403);
        }

        $data = $request->validated();

        // Creamos el viaje en la base de datos
        $trip = Trip::create([
            'driver_id' => $user->id,
            'vehicle_id' => $data['vehicle_id'],
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'departure_time' => $data['departure_time'],
            'seats_total' => $data['seats_total'],
            // Regla: Al crearlo, las plazas disponibles son iguales a las totales
            'seats_available' => $data['seats_total'],
            'price_per_seat' => $data['price_per_seat'],
            // Regla: El estado inicial siempre es activo
            'status' => 'active', 
        ]);

        return new TripResource($trip);
    }   

    // GET /api/v1/trips/{id}
    public function show($id)
    {
        // Buscamos el viaje por su ID. Si no existe, lanzará un error 404 automáticamente.
        $trip = Trip::findOrFail($id);
        return new TripResource($trip);
    }

    // PATCH /api/v1/trips/{id}
    public function update(UpdateTripRequest $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $user = $request->user();

        // Validamos que el viaje pertenece al conductor autenticado
        if ($trip->driver_id !== $user->id) {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => 'FORBIDDEN', 
                    'message' => 'No puedes editar un viaje que no es tuyo.', 
                    'data' => []
                ]
            ], 403);
        }

        // Solo editamos viajes que no tengan reservas confirmadas
        if ($trip->seats_available < $trip->seats_total) {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => 'FORBIDDEN', 
                    'message' => 'No puedes editar este viaje porque ya hay pasajeros con reservas confirmadas.', 
                    'data' => []
                ]
            ], 403);
        }

        $trip->update($request->validated());
        return new TripResource($trip);
    }

    // PATCH /api/v1/trips/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $user = $request->user();

        // Validar que el viaje pertenece al conductor autenticado
        if ($trip->driver_id !== $user->id) {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => 'FORBIDDEN', 
                    'message' => 'No puedes cancelar un viaje que no es tuyo.', 
                    'data' => []
                ]
            ], 403);
        }

        // Validamos el estado del viaje. Solo se pueden cancelar viajes activos.
        if ($trip->status === 'cancelled') {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => 'BAD_REQUEST', 
                    'message' => 'El viaje ya estaba cancelado.', 
                    'data' => []
                ]
            ], 400);
        }

        // Limite de 3 horas para cancelar el viaje. Si quedan menos de 3 horas para la salida, no se puede cancelar.
        if (now()->addHours(3)->isAfter($trip->departure_time)) {
            return response()->json([
                'success' => false, 
                'error' => [
                    'code' => 'TIME_LIMIT_EXCEEDED', 
                    'message' => 'No puedes cancelar el viaje a menos de 3 horas de la salida.', 
                    'data' => []
                ]
            ], 403);
        }

        // Cancelamos el viaje
        $trip->update(['status' => 'cancelled']);

        // Cancelar en cascada todas las reservas asociadas a este viaje
        $bookings = Booking::where('trip_id', $trip->id)->get();
        foreach($bookings as $booking){
            $booking->update(['status' => 'cancelled']);

            //AQUI HAY QUE METER EL CODIGO DE ENVIAR EMAILS DE INFORMACION DE LO QUE HA PASADO A LOS VIAJEROS
            //TAMBIEN HAY QUE METER EL METODO DE REFOUND DEL DINERO A LOS USUARIOS
        }
        
        return response()->json([
            'success' => true,
            'data' => new TripResource($trip)
        ]);
    }
}
