<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Las reglas son las mismas que para crear en este caso.
        // Si 'pagare' o 'pr' fueran únicos, aquí modificaríamos la regla así:
        // 'pagare' => 'required|string|max:255|unique:cre_creditos,pagare,' . $this->route('credito')->id,
        return [
            'pr' => 'required|string|max:255',
            'pagare' => 'required|string|max:255',
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