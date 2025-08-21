<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'nullable' permite que el campo esté vacío. Si se envía un archivo, se valida.
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5MB
            'fecha_subida' => 'required|date',
            'observaciones' => 'nullable|string',
            'cre_creditos_id' => 'required|integer|exists:cre_creditos,id',
            'cre_tipo_documentos_id' => 'required|integer|exists:cre_tipo_documentos,id',
            'id_unico_documento' => 'nullable|string|max:255',
        ];
    }
}