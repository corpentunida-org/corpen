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
            // 'estado' no se acepta aquí: todo colaborador nuevo nace 'inactivo' (no puede
            // haber colaboradores activos sin contrato, y a esta altura aún no tiene ninguno) —
            // ver EmpleadoController::store().
            'cod_ter' => 'required|integer|exists:MaeTerceros,cod_ter|unique:sgrh_empleados,cod_ter',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo' => 'nullable|string|max:50',
            'ext_corporativo' => 'nullable|string|max:20',
            'correo_corporativo' => 'nullable|email|max:255',
            'gmail_corporativo' => 'nullable|email|max:255',
            'eps' => 'nullable|string|max:255',
            'arl' => 'nullable|string|max:255',
            'fondo_pension' => 'nullable|string|max:255',
            'fondo_pension_2' => 'nullable|string|max:255',
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
