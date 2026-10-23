<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrigenReservaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('origen_reserva') ?? $this->route('id');

        return [
            'name'   => 'sometimes|required|string|max:255|unique:rsv_origen_reservas,name,' . $id,
            'active' => 'sometimes|boolean',
        ];
    }
}