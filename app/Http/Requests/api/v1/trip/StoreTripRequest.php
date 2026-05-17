<?php

namespace App\Http\Requests\api\v1\trip;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validamos los campos necesarios para crear un viaje
            'vehicle_id' => ['required', 'integer'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_time' => ['required', 'date', 'after:now'], // Formato: YYYY-MM-DD HH:MM:SS y debe ser una fecha futura
            'seats_total' => ['required', 'integer', 'min:1', 'max:8'],
            'price_per_seat' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
