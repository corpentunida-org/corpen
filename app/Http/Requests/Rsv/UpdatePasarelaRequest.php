<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasarelaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Obtenemos el ID de la ruta para ignorarlo en el unique
        $id = $this->route('pasarela') ?? $this->route('id');

        return [
            'nombre' => 'sometimes|required|string|max:255|unique:rsv_pasarelas,nombre,' . $id,
            'icono'  => 'nullable|string|max:50',
            'color'  => 'nullable|string|max:20',
        ];
    }
}
