<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // El campo del formulario se llamará 'archivo', no 'ruta_archivo'.
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // Max 5MB
            'fecha_subida' => 'required|date',
            'observaciones' => 'nullable|string',
            'cre_creditos_id' => 'required|integer|exists:cre_creditos,id',
            'cre_tipo_documentos_id' => 'required|integer|exists:cre_tipo_documentos,id',
            'id_unico_documento' => 'nullable|string|max:255',
        ];
    }
}