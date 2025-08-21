<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipoDocumentoId = $this->route('tipo_documento')->id;

        return [
            'nombre' => 'required|string|max:255|unique:cre_tipo_documentos,nombre,' . $tipoDocumentoId,
            'cre_etapas_id' => 'required|integer|exists:cre_etapas,id',
        ];
    }
}