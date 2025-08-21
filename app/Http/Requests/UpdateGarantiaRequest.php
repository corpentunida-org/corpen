<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGarantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $garantiaId = $this->route('garantia')->id;

        return [
            'nombre' => 'required|string|max:255|unique:cre_garantias,nombre,' . $garantiaId,
        ];
    }
}