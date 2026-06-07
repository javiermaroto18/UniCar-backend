<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices para acelerar las consultas más frecuentes.
     */
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // El listado público filtra por status y ordena/filtra por departure_time
            $table->index(['status', 'departure_time'], 'trips_status_departure_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            // El panel filtra reservas por estado
            $table->index('status', 'bookings_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex('trips_status_departure_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
        });
    }
};
