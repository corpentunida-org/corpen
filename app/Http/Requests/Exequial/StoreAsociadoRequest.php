<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Antes ComaeExCliController::store() no validaba nada — leía $request->documentId,
 * $request->plan, etc. directo, así que un formulario incompleto pasaba silenciosamente y
 * terminaba en un registro local a medio llenar o en un error confuso de la API externa.
 */
class StoreAsociadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documentId' => ['required', 'string', 'max:20'],
            'observaciones' => ['required', 'string'],
            'discount' => ['required', 'numeric'],
            'plan' => ['nullable', 'string', 'max:10'],
        ];
    }
}
