<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class StoreReafiliacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'observacion_reafiliacion' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'observacion_reafiliacion.required' => 'La observación de reafiliación es obligatoria.',
        ];
    }
}
