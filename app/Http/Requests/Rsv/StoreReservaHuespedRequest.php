<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaHuespedRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_rsv_reservas'  => 'required|exists:rsv_reservas,id',
            'nombres'          => 'required_without:nombre|nullable|string|max:255',
            'nombre'           => 'required_without:nombres|nullable|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'tipo_documento'   => 'nullable|string|max:50',
            'numero_documento' => 'nullable|string|max:50',
            'es_principal'     => 'nullable|boolean',
            'es_titular'       => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalizar nombres y titular de forma segura ante cualquier variante
        $this->merge([
            'nombre'     => $this->input('nombre') ?? $this->input('nombres'),
            'es_titular' => $this->boolean('es_titular') || $this->boolean('es_principal'),
        ]);
    }
}