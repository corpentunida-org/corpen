<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBloqueoCalendarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // <-- Cambiar a true
    }

    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'sometimes|required|integer|exists:rsv_catalogo_inmueble,id',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'motivo' => 'sometimes|required|string|max:500',
        ];
    }
}
