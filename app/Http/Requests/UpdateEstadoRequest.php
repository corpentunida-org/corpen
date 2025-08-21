<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID del estado que estamos editando desde la ruta.
        $estadoId = $this->route('estado')->id;

        return [
            // La regla 'unique' debe ignorar el registro actual.
            'nombre' => 'required|string|max:255|unique:cre_estados,nombre,' . $estadoId,
            'cre_etapas_id' => 'required|integer|exists:cre_etapas,id',
        ];
    }
}