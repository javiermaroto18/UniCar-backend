<?php

namespace App\Http\Requests\api\v1\vehicle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
            'brand' => ['sometimes', 'string', 'max:50'],
            'model' => ['sometimes', 'string', 'max:50'],
            // 'color' => ['sometimes', 'string', 'max:30'],
            'license_plate' => ['prohibited'], // No permitimos cambiar la matricula
            // 'license_plate' => ['sometimes', 'string', 'max:20', 'unique:vehicles,license_plate,' . $this->route('vehicle')->id], 
            'is_frequent' => ['sometimes', 'boolean'],
        ];
    }
}
