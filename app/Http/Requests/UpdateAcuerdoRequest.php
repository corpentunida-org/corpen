<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\AcuerdoEstadoEnum;
use Illuminate\Validation\Rules\Enum;

class UpdateAcuerdoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID del acuerdo que estamos editando desde la ruta (ej: /acuerdos/5/edit)
        $acuerdoId = $this->route('acuerdo')->id;

        return [
            // Le decimos a la regla 'unique' que ignore el registro con el ID actual.
            'numero_acuerdo'    => 'required|string|max:255|unique:car_acuerdos,numero_acuerdo,' . $acuerdoId,
            'fecha_acuerdo'     => 'required|date',
            'estado'            => ['required', new Enum(AcuerdoEstadoEnum::class)],
            'dias_mora_inicial' => 'required|integer|min:0',
            'intereses_corrientes_acuerdo' => 'required|numeric|min:0',
            'intereses_mora_acuerdo' => 'required|numeric|min:0',
            'gastos_cobranza'   => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string',
            'cre_creditos_id'   => 'required|integer|exists:cre_creditos,id',
        ];
    }
}