<?php

namespace App\Http\Requests\Sgrh;

use Illuminate\Foundation\Http\FormRequest;

// No incluye 'cod_ter' ni 'estado': cod_ter es el enlace al tercero (no se reasigna desde
// aquí) y el cambio de estado tiene su propio flujo (EmpleadoController::updateEstado, con la
// lógica de fecha_retiro) para no duplicarla en dos sitios que puedan desincronizarse.
// Tampoco incluye 'fecha_ingreso', 'cargo_id' ni 'salario_asignado': el contrato es la única
// fuente de esos datos ahora (Empleado::getFechaIngresoAttribute()/getCargoIdAttribute()/
// getSalarioAsignadoAttribute() los derivan de contratoActivo) — se editan desde "Registrar
// contrato"/"Editar contrato", no aquí.
class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
}
