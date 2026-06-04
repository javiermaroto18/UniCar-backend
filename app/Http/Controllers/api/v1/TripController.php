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
        $query = Trip::select('trips.*')
                     ->join('users', 'trips.driver_id', '=', 'users.id');

        // Solo viajes activos y que aún no hayan salido
        $query->where('trips.status', 'scheduled')->where('trips.departure_time', '>', now());

        // Filtro de Origen
        if ($request->filled('origin')) {
            $query->where('trips.origin', 'like', '%' . $request->origin . '%');
        }

        // Filtro de Destino
        if ($request->filled('destination')) {
            $query->where('trips.destination', 'like', '%' . $request->destination . '%');
        }

        // Filtro de Fecha
        if ($request->filled('date')) {
            $query->whereDate('trips.departure_time', $request->date);
        }

        // Filtro de Plazas mínimas requeridas
        if ($request->filled('seats')) {
            $query->where('trips.seats_available', '>=', $request->seats);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Buscamos en origen, destino y nombre del conductor
            $query->where(function($q) use ($searchTerm) {
                $q->where('trips.origin', 'like', '%' . $searchTerm . '%')
                  ->orWhere('trips.destination', 'like', '%' . $searchTerm . '%')
                  ->orWhere('users.name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ordenación dinamica
        $filterOption = $request->input('filter', 'date_asc'); // 'date_asc' por defecto

        switch ($filterOption) {
            case 'price_asc':
                $query->orderBy('trips.price_per_seat', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('users.name', 'asc');
                break;
            case 'date_asc':
            default:
                $query->orderBy('trips.departure_time', 'asc');
                break;
        }

        $trips = $query->paginate(6);
        return TripResource::collection($trips)->response()->getData(true);
    }

    // GET /api/v1/trips/me
    public function myTrips(Request $request)
    {
        $user = $request->user();
        
        // Obtenemos todos los viajes de este conductor ordenados por fecha
        $trips = Trip::where('driver_id', $user->id)
                     ->orderBy('departure_time', 'desc')
                     ->get();

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
        // findOrFail ya lanza ModelNotFoundException
        $trip = Trip::findOrFail($id);
        $trip = Trip::with(['driver', 'bookings.passenger'])->findOrFail($id);
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
            // Enviar emails en Fase 2
        }
        
        return response()->json([
            'success' => true,
            'data' => new TripResource($trip)
        ]);
    }
}