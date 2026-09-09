<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCorpentunidaCrmConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:255'],
            'client_id' => ['required', 'string', 'max:255'],
            // Vacío = no se cambia el secreto ya guardado (se muestra enmascarado, nunca en texto plano).
            'client_secret' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'La URL base es obligatoria.',
            'url.url' => 'La URL no tiene un formato válido (debe incluir https://).',
            'client_id.required' => 'El Client ID es obligatorio.',
        ];
    }
}
