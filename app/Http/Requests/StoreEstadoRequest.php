<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir que usuarios autenticados realicen la acción
    }

    public function rules(): array
    {
        return [
            // El nombre debe ser único en la tabla cre_estados.
            'nombre' => 'required|string|max:255|unique:cre_estados,nombre',
            // La etapa seleccionada debe existir en la tabla cre_etapas.
            'cre_etapas_id' => 'required|integer|exists:cre_etapas,id',
        ];
    }
}