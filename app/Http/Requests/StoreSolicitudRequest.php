<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Solicitud
            'cod_ter' => 'required|integer|exists:MaeTerceros,cod_ter',
            'tipo_cred' => 'required|in:LI,HIP',
            'vr_soli' => 'required|numeric|min:0',
            'tipo_cuota' => 'required|in:FIJA,VARIABLE',
            'plazo' => 'required|integer|min:1',
            'destino' => 'required|string|max:1000',
            'tiene_credito_actual' => 'nullable|boolean',
            'cual_credito_actual' => 'nullable|required_if:tiene_credito_actual,1|string|max:255',
            'protec_dato' => 'accepted',
            'formulario_firmado' => 'required|file|mimes:pdf,jpeg,jpg,png|max:10240',

            // Datos personales (MaeTerceros) — opcionales, se guardan si se diligencian
            'whatsapp' => 'nullable|string|max:20',
            'cel' => 'nullable|string|max:20',
            'dir' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'depa' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'fec_ing' => 'nullable|date',
            'fec_minis' => 'nullable|date',
            'peso' => 'nullable|numeric|min:0|max:500',
            'estatura' => 'nullable|numeric|min:0|max:3',
            'eps' => 'nullable|string|max:100',
            'detalle_enfermedades' => 'nullable|string|max:2000',
            'nom_conyug' => 'nullable|string|max:150',
            'id_conyuge' => 'nullable|string|max:20',
            'cel_conyu' => 'nullable|string|max:20',
            'num_hijos' => 'nullable|integer|min:0',
            'personas_cargo' => 'nullable|integer|min:0',
            'tipo_vivienda' => 'nullable|in:propia,pastoral',
            'congregacion_paga_servicios' => 'nullable|boolean',
            'congregacion_paga_arriendo' => 'nullable|boolean',
            'congregacion_paga_otros' => 'nullable|string|max:255',

            // Ingresos / Egresos (desglose por categoría)
            'ingresos' => 'nullable|array',
            'ingresos.*' => 'nullable|numeric|min:0',
            'egresos' => 'nullable|array',
            'egresos.*' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'protec_dato.accepted' => 'Debes autorizar la consulta en centrales de riesgo para continuar.',
            'formulario_firmado.required' => 'Debes adjuntar el formulario firmado (PDF o foto).',
            'cual_credito_actual.required_if' => 'Indica cuál crédito tiene actualmente.',
        ];
    }
}
