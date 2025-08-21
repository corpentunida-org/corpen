<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtapaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        // Obtenemos el ID de la etapa que se está editando desde la ruta.
        $etapaId = $this->route('etapa')->id;

        return [
            // La regla 'unique' debe ignorar el nombre de la etapa actual.
            'nombre' => 'required|string|max:255|unique:cre_etapas,nombre,' . $etapaId,
            'descripcion' => 'nullable|string|max:1000',
        ];
    }
}