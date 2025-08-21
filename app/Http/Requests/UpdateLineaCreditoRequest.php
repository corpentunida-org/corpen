<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLineaCreditoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lineaCreditoId = $this->route('lineas_credito')->id;

        return [
            'nombre' => 'required|string|max:255|unique:cre_lineas_creditos,nombre,' . $lineaCreditoId,
            'cuenta' => 'required|integer',
            'tasa_interes' => 'required|numeric|min:0|max:100',
            'plazo_minimo' => 'required|integer|min:1',
            'plazo_maximo' => 'required|integer|min:1|gte:plazo_minimo',
            'edad_minima' => 'required|integer|min:18',
            'edad_maxima' => 'required|integer|min:18|gte:edad_minima',
            'fecha_apertura' => 'required|date',
            'fecha_cierre' => 'nullable|date|after_or_equal:fecha_apertura',
            'observacion' => 'nullable|string',
            'cre_garantias_id' => 'required|integer|exists:cre_garantias,id',
            'cre_tipos_creditos_id' => 'required|integer|exists:cre_tipos_creditos,id',
        ];
    }
}