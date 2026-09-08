<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class StoreRetiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_retiro' => ['required', 'date', 'before_or_equal:today'],
            'observaciones' => ['required', 'string'],
        ];
    }
}
