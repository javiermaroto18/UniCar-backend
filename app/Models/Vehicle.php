<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_model',
        'license_plate',
        'is_frequent',
    ];

    protected function casts(): array
    {
        return [
            'is_frequent' => 'boolean',
        ];
    }

    // Relación inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1:N -> Entre vehículo y viajes
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
