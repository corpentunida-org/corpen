<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagareRequest extends FormRequest
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
        $pagareId = $this->route('pagare')->id;

        return [
            'id_unico_documento' => 'required|string|max:255|unique:cre_pagares,id_unico_documento,' . $pagareId,
            'cre_credito_id' => 'required|integer|exists:cre_creditos,id|unique:cre_pagares,cre_credito_id,' . $pagareId,
            'valor_capital' => 'required|numeric|min:0.01',
            'tasa_interes_nominal' => 'required|numeric|min:0|max:100',
            'tasa_interes_mora' => 'required|numeric|min:0|max:100',
            'numero_cuotas' => 'required|integer|min:1',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'lugar_firma' => 'required|string|max:255',
            'estado' => 'required|string|max:50',
        ];
    }
}