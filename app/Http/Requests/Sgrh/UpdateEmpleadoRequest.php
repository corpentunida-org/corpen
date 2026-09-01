<?php

namespace App\Http\Requests\Sgrh;

use Illuminate\Foundation\Http\FormRequest;

// No incluye 'cod_ter' ni 'estado': cod_ter es el enlace al tercero (no se reasigna desde
// aquí) y el cambio de estado tiene su propio flujo (EmpleadoController::updateEstado, con la
// lógica de fecha_retiro) para no duplicarla en dos sitios que puedan desincronizarse.
class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_ingreso' => 'nullable|date',
            'cargo_id' => 'nullable|exists:sgrh_cargos,id',
            'salario_asignado' => 'nullable|numeric|min:0',
            'eps' => 'nullable|string|max:255',
            'arl' => 'nullable|string|max:255',
            'fondo_pension' => 'nullable|string|max:255',
            'tipo_sangre' => 'nullable|string|max:5',
            'contacto_emergencia_nombre' => 'nullable|string|max:255',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ];
    }
}
