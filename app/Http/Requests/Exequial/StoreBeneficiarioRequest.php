<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cedulaAsociado' => ['required', 'string', 'max:20'],
            'documentid' => ['required', 'string', 'max:20'],
            'apellidos' => ['required', 'string', 'max:150'],
            'nombres' => ['required', 'string', 'max:150'],
            'codePar' => ['nullable', 'string', 'max:10'],
            'fechaNacimiento' => ['nullable', 'date'],
        ];
    }
}
