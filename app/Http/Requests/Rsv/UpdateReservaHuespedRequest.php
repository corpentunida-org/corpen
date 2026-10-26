<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaHuespedRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_rsv_reservas'  => 'sometimes|exists:rsv_reservas,id',
            'nombres'          => 'nullable|string|max:255',
            'nombre'           => 'nullable|string|max:255',
            'apellidos'        => 'sometimes|required|string|max:255',
            'tipo_documento'   => 'nullable|string|max:50',
            'numero_documento' => 'nullable|string|max:50',
            'es_principal'     => 'nullable|boolean',
            'es_titular'       => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalizar los campos opcionales al actualizar
        $data = [];
        
        if ($this->has('nombre') || $this->has('nombres')) {
            $data['nombre'] = $this->input('nombre') ?? $this->input('nombres');
        }

        if ($this->has('es_titular') || $this->has('es_principal')) {
            $data['es_titular'] = $this->boolean('es_titular') || $this->boolean('es_principal');
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}