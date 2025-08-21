<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditoRequest extends FormRequest
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
            'pr' => 'required|string|max:255',
            'pagare' => 'required|string|max:255', // Asumiendo que es un número o código de pagaré
            'valor' => 'required|numeric|min:0',
            'cuotas' => 'required|integer|min:1',
            'fecha_desembolso' => 'required|date',
            'acuerdo' => 'required|boolean',
            'cre_estados_id' => 'required|integer|exists:cre_estados,id',
            'mae_terceros_cedula' => 'required|string|exists:mae_terceros,cedula',
            'cre_lineas_creditos_id' => 'required|integer|exists:cre_lineas_creditos,id',
        ];
    }
}