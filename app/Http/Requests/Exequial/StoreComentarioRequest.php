<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class StoreComentarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipocomment' => ['required', 'string', 'max:50'],
            'observacioncomment' => ['required', 'string'],
        ];
    }
}
