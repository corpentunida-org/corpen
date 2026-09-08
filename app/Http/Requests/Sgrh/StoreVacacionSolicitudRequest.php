<?php

namespace App\Http\Requests\Sgrh;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Solo lo sintáctico (formato/presencia). Las reglas de negocio (saldo, solapes, bloqueos,
 * antigüedad) las resuelve VacacionValidador en el controller, porque cruzan otras tablas y
 * necesitan el Empleado del usuario logueado, no solo el payload del formulario.
 */
class StoreVacacionSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'observaciones' => 'nullable|string|max:1000',
            // Solo relevantes cuando quien arma la solicitud tiene el permiso de respaldo de
            // RRHH (ver VacacionSolicitudController::store()) — un colaborador normal no puede
            // enviar estos campos por su cuenta.
            'empleado_id' => 'nullable|exists:sgrh_empleados,id',
            'es_adelantada' => 'nullable|boolean',
        ];
    }
}
