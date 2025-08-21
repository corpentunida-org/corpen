<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:cre_tipo_documentos,nombre',
            'cre_etapas_id' => 'required|integer|exists:cre_etapas,id',
        ];
    }
}