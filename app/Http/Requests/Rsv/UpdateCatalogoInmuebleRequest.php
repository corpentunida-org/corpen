<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la actualización de un Catálogo de Inmueble existente.
 */
class UpdateCatalogoInmuebleRequest extends FormRequest
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
     * Reglas de validación para la actualización.
     */
    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'city'             => ['required', 'string', 'max:255'],
            'ubicacion'        => ['nullable', 'string', 'max:500'], // Asegúrate de incluirlo aquí también
            'capacidad_maxima' => ['required', 'integer', 'min:1'],
            'tipo_inmueble_id' => ['nullable', 'integer', 'exists:rsv_tipo_inmueble,id'],
            'active'           => ['required', 'boolean'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'name.required'             => 'El nombre del inmueble no puede quedar vacío.',
            'city.required'             => 'La ciudad no puede quedar vacía.',
            'capacidad_maxima.min'      => 'La capacidad máxima debe ser de al menos 1 persona.',
            'tipo_inmueble_id.exists'   => 'El tipo de inmueble seleccionado no es válido en el sistema.',
        ];
    }
}
