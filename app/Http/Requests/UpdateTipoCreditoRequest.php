<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoCreditoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipoCreditoId = $this->route('tipo_credito')->id;

        return [
            'nombre' => 'required|string|max:255|unique:cre_tipos_creditos,nombre,' . $tipoCreditoId,
        ];
    }
}