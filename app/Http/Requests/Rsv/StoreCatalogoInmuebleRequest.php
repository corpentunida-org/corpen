<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la creación de un nuevo Catálogo de Inmueble.
 * Centraliza las reglas de validación y mensajes de error, manteniendo el controlador limpio.
 */
class StoreCatalogoInmuebleRequest extends FormRequest
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
     * Ideal para formatear datos, como convertir el valor de un checkbox HTML a un booleano real.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? 1 : 0,
        ]);
    }

    /**
     * Obtiene las reglas de validación que se aplicarán a la petición.
     */
    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'city'             => ['required', 'string', 'max:255'],
            'ubicacion'        => ['nullable', 'string', 'max:500'],
            'capacidad_maxima' => ['required', 'integer', 'min:1'],

            // CORREGIDO: Apuntando a la tabla correcta con tu prefijo
            'tipo_inmueble_id' => ['required', 'integer', 'exists:rsv_tipo_inmueble,id'],

            'active'           => ['required', 'boolean'],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'name.required'             => 'El nombre del inmueble es obligatorio.',
            'city.required'             => 'La ciudad es obligatoria.',
            'capacidad_maxima.required' => 'Debes especificar la capacidad máxima.',
            'capacidad_maxima.min'      => 'La capacidad máxima debe ser de al menos 1 persona.',
            'tipo_inmueble_id.required' => 'Debes seleccionar un tipo de inmueble.',
            'tipo_inmueble_id.exists'   => 'El tipo de inmueble seleccionado no es válido o no existe.',
        ];
    }
}
