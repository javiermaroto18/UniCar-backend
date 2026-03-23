<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'passenger_id',
        'seats_booked',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'seats_booked' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    // Relación inversa con Trip
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // Relación inversa con pasajero (User)
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }
}
