<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StorePasarelaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:rsv_pasarelas,nombre',
            'icono'  => 'nullable|string|max:50',
            'color'  => 'nullable|string|max:20',
        ];
    }
}
