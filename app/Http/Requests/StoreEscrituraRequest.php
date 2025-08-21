<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEscrituraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_unico_documento' => 'required|string|max:255|unique:cre_escrituras',
            'cre_credito_id' => 'required|integer|exists:cre_creditos,id',
            'numero_notaria' => 'required|integer',
            'ciudad_notaria' => 'required|string|max:255',
            'folio_matricula_inmobiliaria' => 'nullable|string|max:255',
            'oficina_registro_instrumentos' => 'required|string|max:255',
            'fecha_constitucion' => 'required|date',
            'fecha_registro' => 'nullable|date|after_or_equal:fecha_constitucion',
            'valor_gravamen' => 'required|numeric|min:0',
            'estado' => 'required|string|max:50', // Sugerencia: Considera usar un Enum aquí también.
        ];
    }
}

#### Archivo para Actualizar (`app/Http/Requests/UpdateEscrituraRequest.php`)


