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
        // Solución a prueba de fallos DEFINITIVA:
        // Obtenemos el primer parámetro de la ruta dinámicamente,
        // así no importa si el router lo llamó 'tipos_inmueble', 'id' o 'tipo'.
        $routeParams = $this->route()->parameters();
        $param = reset($routeParams);

        // Extraemos el ID (ya sea que el router haya inyectado el Modelo completo o solo el ID numérico)
        $tipoInmuebleId = is_object($param) ? $param->id : $param;

        return [
            'nombre'      => [
                'sometimes',
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('rsv_tipo_inmueble', 'nombre')->ignore($tipoInmuebleId)
            ],
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
