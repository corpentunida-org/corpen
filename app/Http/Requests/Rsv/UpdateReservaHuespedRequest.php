<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaHuespedRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombres'          => 'nullable|string|max:255',
            'nombre'           => 'nullable|string|max:255',
            'apellidos'        => 'sometimes|required|string|max:255',
            'tipo_documento'   => 'nullable|string|max:50',
            'numero_documento' => 'nullable|string|max:50',
            'es_principal'     => 'nullable|boolean',
            'es_titular'       => 'nullable|boolean',
        ];
    }
}