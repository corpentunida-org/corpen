<?php

namespace App\Http\Requests\Sgrh;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_ter' => 'required|integer|exists:MaeTerceros,cod_ter|unique:sgrh_empleados,cod_ter',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'nullable|in:activo,inactivo,retirado',
            'eps' => 'nullable|string|max:255',
            'arl' => 'nullable|string|max:255',
            'fondo_pension' => 'nullable|string|max:255',
            'tipo_sangre' => 'nullable|string|max:5',
            'contacto_emergencia_nombre' => 'nullable|string|max:255',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cod_ter.required' => 'Debes buscar e identificar el tercero antes de guardar.',
            'cod_ter.exists' => 'El tercero indicado no existe en el maestro de terceros.',
            'cod_ter.unique' => 'Este tercero ya está registrado como colaborador.',
        ];
    }
}
