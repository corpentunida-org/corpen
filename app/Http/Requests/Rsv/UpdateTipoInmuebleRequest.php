<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoInmuebleRequest extends FormRequest
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
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        // Obtenemos el ID del tipo de inmueble que se está actualizando desde la ruta
        // para permitir que mantenga su propio nombre sin lanzar error de "unique"
        $tipoInmuebleId = $this->route('tipoInmueble') ? $this->route('tipoInmueble')->id : null;

        return [
            'nombre'      => ['sometimes', 'required', 'string', 'max:255', Rule::unique('rsv_tipo_inmueble', 'nombre')->ignore($tipoInmuebleId)],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'active'      => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de inmueble no puede quedar vacío.',
            'nombre.unique'   => 'El nombre ingresado ya está siendo utilizado por otro tipo de inmueble.',
        ];
    }
}
