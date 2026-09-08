<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cedula' => ['required', 'string', 'max:20'],
            'documentid' => ['required', 'string', 'max:20'],
            'names' => ['required', 'string', 'max:150'],
            'parentesco' => ['required', 'string', 'max:10'],
            'fechaNacimiento' => ['nullable', 'date'],
        ];
    }
}
