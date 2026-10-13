<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la actualización de un recurso multimedia existente.
 */
class UpdateInmuebleMultimediaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos antes de ser validados.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->expectsJson()) {
            $this->merge([
                'es_portada' => $this->has('es_portada') ? 1 : 0,
            ]);
        }
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => ['sometimes', 'required', 'exists:rsv_catalogo_inmueble,id'],
            'url_archivo'              => ['sometimes', 'nullable', 'file', 'mimes:jpeg,png,jpg,webp,mp4,mov,avi', 'max:20480'],
            'tipo_multimedia'          => ['sometimes', 'required', 'string', 'max:50'],
            'orden'                    => ['sometimes', 'required', 'integer'],
            'es_portada'               => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'inmueble',
            'url_archivo'              => 'archivo multimedia',
            'tipo_multimedia'          => 'tipo de multimedia',
            'orden'                    => 'orden de visualización',
        ];
    }
}
