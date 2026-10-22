<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaccionFinancieraRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_rsv_reservas'    => 'sometimes|required|exists:rsv_reservas,id',
            'id_rsv_pasarela'    => 'sometimes|nullable|exists:rsv_pasarelas,id',
            'monto'              => 'sometimes|required|numeric|min:0',
            'moneda'             => 'sometimes|nullable|string|max:10',
            'estado_pago'        => 'sometimes|nullable|string|max:50',
            'metodo_pago'        => 'sometimes|nullable|string|max:100',
            'referencia_externa' => 'sometimes|nullable|string|max:255',
            'soporte_pago'       => 'sometimes|nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }
}
