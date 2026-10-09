<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoInmuebleRequest extends FormRequest
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
     * Convierte el checkbox HTML de 'active' en un valor booleano procesable.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? 1 : 0,
        ]);
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:255', 'unique:rsv_tipo_inmueble,nombre'], // Evita nombres duplicados
            'descripcion' => ['nullable', 'string', 'max:500'],
            'active'      => ['required', 'boolean'],
        ];
    }

    /**
     * Mensajes de error personalizados para mejor UX.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de inmueble es obligatorio.',
            'nombre.unique'   => 'Este tipo de inmueble ya existe en el sistema.',
        ];
    }
}
