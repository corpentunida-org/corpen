<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloqueoCalendarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // <-- Cambiar a true
    }

    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'required|integer|exists:rsv_catalogo_inmueble,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:500',
        ];
    }
}
