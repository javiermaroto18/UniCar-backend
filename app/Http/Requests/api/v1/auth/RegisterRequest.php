<?php

namespace App\Http\Requests\api\v1\auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            // Validamos las características del email y aseguramos que sea único en la tabla users
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'email', 
                'unique:users,email',
                // Permitimos solo correos de dominios específicos
                'ends_with:@ucm.es,@upm.es,@urjc.es,@alumnos.upm.es' 
            ],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
