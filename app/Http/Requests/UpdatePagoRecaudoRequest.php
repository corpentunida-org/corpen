<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagoRecaudoRequest extends FormRequest
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
            'cuota' => 'required|integer|min:1',
            'valor_pagado' => 'required|numeric|min:0.01',
            'rc' => 'required|string|max:255',
            'comprobante' => 'required|string|max:255',
            'cre_creditos_id' => 'required|integer|exists:cre_creditos,id',
        ];
    }
}