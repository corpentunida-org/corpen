<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagareRequest extends FormRequest
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
        return [
            'id_unico_documento' => 'required|string|max:255|unique:cre_pagares',
            // Valida que el crédito no tenga ya un pagaré asociado.
            'cre_credito_id' => 'required|integer|exists:cre_creditos,id|unique:cre_pagares,cre_credito_id',
            'valor_capital' => 'required|numeric|min:0.01',
            'tasa_interes_nominal' => 'required|numeric|min:0|max:100',
            'tasa_interes_mora' => 'required|numeric|min:0|max:100',
            'numero_cuotas' => 'required|integer|min:1',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'lugar_firma' => 'required|string|max:255',
            'estado' => 'required|string|max:50', // Considera usar un Enum para los estados.
        ];
    }
}