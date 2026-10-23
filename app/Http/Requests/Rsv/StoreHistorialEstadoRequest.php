<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreHistorialEstadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_rsv_reservas'          => 'required|exists:rsv_reservas,id',
            'id_rsv_statuses_anterior' => 'nullable|exists:rsv_statuses,id',
            'id_rsv_status_nuevo'      => 'required|exists:rsv_statuses,id',
            'id_user'                  => 'required|integer',
            'comentario'               => 'nullable|string',
        ];
    }
}