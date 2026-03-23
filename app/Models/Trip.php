<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'origin',
        'destination',
        'departure_time',
        'seats_total',
        'seats_available',
        'price_per_seat',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'seats_total' => 'integer',
            'seats_available' => 'integer',
            'price_per_seat' => 'decimal:2',
        ];
    }

    // Relación inversacon conductor (User)
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Relación inversa con Vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // 1:N -> Entre viaje y reservas
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
