<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaccionFinancieraRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_rsv_reservas'    => 'required|exists:rsv_reservas,id',
            'id_rsv_pasarela'    => 'nullable|exists:rsv_pasarelas,id',
            'monto'              => 'required|numeric|min:0',
            'moneda'             => 'nullable|string|max:10',
            'estado_pago'        => 'nullable|string|max:50',
            'metodo_pago'        => 'nullable|string|max:100',
            'referencia_externa' => 'nullable|string|max:255',
            'soporte_pago'       => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }
}
