<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            // Datos conductor
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); //ID del conductor (usuario)
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            
            // Datos del viaje
            $table->string('origin'); // Lugar de origen
            $table->string('destination'); // Lugar de destino
            $table->dateTime('departure_time'); // Hora de salida

            // Datos adicionales
            $table->integer('seats_total');
            $table->integer('seats_available');

            $table->decimal('price_per_seat', 8, 2);
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
