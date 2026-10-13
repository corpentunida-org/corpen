<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la actualización de una Tarifa de Temporada existente.
 */
class UpdateTarifaTemporadaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos antes de ser validados.
     * Solo inyecta el 0 para el checkbox si es una petición web tradicional (formulario).
     */
    protected function prepareForValidation(): void
    {
        if (! $this->expectsJson()) {
            $this->merge([
                'active' => $this->has('active') ? 1 : 0,
            ]);
        }
    }

    /**
     * Reglas de validación. Usamos 'sometimes' para permitir envío parcial desde APIs.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => ['sometimes', 'required', 'exists:rsv_catalogo_inmueble,id'],
            'nombre_temporada'         => ['sometimes', 'required', 'string', 'max:255'],
            'fecha_inicio'             => ['sometimes', 'required', 'date'],
            'fecha_fin'                => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'precio_noche'             => ['sometimes', 'required', 'numeric', 'min:0'],
            'precio_fin_semana'        => ['sometimes', 'required', 'numeric', 'min:0'],
            'precio_minimo_reserva'    => ['sometimes', 'required', 'numeric', 'min:0'],
            'dias_maximos'             => ['sometimes', 'nullable', 'integer', 'min:1'],
            'active'                   => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Nombres legibles para los atributos en caso de error.
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
