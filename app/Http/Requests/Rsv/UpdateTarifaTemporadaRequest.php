<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTarifaTemporadaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // <-- Debe estar en true para permitir el acceso
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'sometimes|required|exists:rsv_catalogo_inmueble,id',
            'nombre_temporada'         => 'sometimes|required|string|max:255',
            'fecha_inicio'             => 'sometimes|required|date',
            'fecha_fin'                => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'precio_noche'             => 'sometimes|required|numeric|min:0',
            'precio_fin_semana'        => 'sometimes|required|numeric|min:0',
            'precio_minimo_reserva'    => 'sometimes|required|numeric|min:0',
            'dias_maximos'             => 'sometimes|nullable|integer|min:1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'inmueble',
            'nombre_temporada'         => 'nombre de la temporada',
            'fecha_inicio'             => 'fecha de inicio',
            'fecha_fin'                => 'fecha de fin',
            'precio_noche'             => 'precio por noche',
            'precio_fin_semana'        => 'precio fin de semana',
            'precio_minimo_reserva'    => 'precio mínimo de reserva',
            'dias_maximos'             => 'días máximos',
        ];
    }
}
