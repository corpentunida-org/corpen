<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\AcuerdoEstadoEnum;
use Illuminate\Validation\Rules\Enum;

class StoreAcuerdoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Lo ponemos en 'true' para permitir que cualquier usuario autenticado
        // pueda intentar crear un acuerdo. Puedes añadir lógica de roles/permisos aquí.
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'numero_acuerdo'    => 'required|string|max:255|unique:car_acuerdos',
            'fecha_acuerdo'     => 'required|date',
            'estado'            => ['required', new Enum(AcuerdoEstadoEnum::class)],
            'dias_mora_inicial' => 'required|integer|min:0',
            'intereses_corrientes_acuerdo' => 'required|numeric|min:0',
            'intereses_mora_acuerdo' => 'required|numeric|min:0',
            'gastos_cobranza'   => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string',
            'cre_creditos_id'   => 'required|integer|exists:cre_creditos,id',
            // El 'user_id' no lo validamos aquí porque lo tomaremos del usuario logueado.
        ];
    }
}