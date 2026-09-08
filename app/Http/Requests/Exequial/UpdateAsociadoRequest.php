<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsociadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documentid' => ['required', 'string', 'max:20'],
            'dateInit' => ['nullable', 'string'],
            'codePlan' => ['nullable', 'string', 'max:10'],
            'discount' => ['required', 'numeric'],
            'observation' => ['required', 'string'],
        ];
    }
}
