<?php

namespace App\Http\Requests\api\v1\vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // Lo ponemos en true porque ya se gestiona en la ruta con Sanctum
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            // La matrícula debe ser única en la tabla vehicles
            'license_plate' => ['required', 'string', 'max:20', 'unique:vehicles,license_plate'], 
        ];
    }
}
