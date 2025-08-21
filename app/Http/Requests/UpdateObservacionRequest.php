<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateObservacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Opcional: Podrías añadir lógica para que solo el creador
        // de la observación o un administrador pueda editarla.
        // Ejemplo: return $this->user()->can('update', $this->route('observacion'));
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'asunto' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'observacion' => 'required|string',
            'cre_creditos_id' => 'required|integer|exists:cre_creditos,id',
        ];
    }
}