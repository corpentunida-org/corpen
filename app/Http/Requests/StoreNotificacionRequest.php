<?php

namespace App\Http\Requests;

use App\Enums\NotificacionEstadoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreNotificacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'canal' => 'required|string|max:255',
            // Valida que el estado enviado sea uno de los casos definidos en nuestro Enum.
            'estado' => ['required', new Enum(NotificacionEstadoEnum::class)],
            'cre_creditos_id' => 'required|integer|exists:cre_creditos,id',
            'mae_terceros_cedula' => 'required|string|exists:mae_terceros,cedula',
        ];
    }
}