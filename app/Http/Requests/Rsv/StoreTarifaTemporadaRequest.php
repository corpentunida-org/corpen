<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la creación de una nueva Tarifa de Temporada.
 */
class StoreTarifaTemporadaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true; // Permitimos el acceso (puedes agregar políticas de seguridad aquí después)
    }

    /**
     * Prepara los datos antes de ser validados.
     * Convierte el checkbox HTML de 'active' en un valor booleano procesable.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? 1 : 0,
        ]);
    }

    /**
     * Obtiene las reglas de validación que se aplicarán a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => ['required', 'exists:rsv_catalogo_inmueble,id'],
            'nombre_temporada'         => ['required', 'string', 'max:255'],
            'fecha_inicio'             => ['required', 'date'],
            'fecha_fin'                => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'precio_noche'             => ['required', 'numeric', 'min:0'],
            'precio_fin_semana'        => ['required', 'numeric', 'min:0'],
            'precio_minimo_reserva'    => ['required', 'numeric', 'min:0'],
            'dias_maximos'             => ['nullable', 'integer', 'min:1'],
            'active'                   => ['required', 'boolean'], // Validamos el estado que pre-procesamos
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

    /**
     * Mensajes de error personalizados (Opcional, mejora la UX).
     */
    public function messages(): array
    {
        return [
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'precio_noche.min'         => 'El precio por noche no puede ser negativo.',
        ];
    }
}
