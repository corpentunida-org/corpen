<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrigenReservaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Cambiado de 'name' a 'nombre'
            'nombre' => 'required|string|max:255|unique:rsv_origen_reservas,nombre',
        ];
    }
}