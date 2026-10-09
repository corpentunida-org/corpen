<?php

namespace App\Http\Requests\Rsv;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la creación de un nuevo recurso multimedia (imágenes/videos)
 * para el catálogo de inmuebles.
 */
class StoreInmuebleMultimediaRequest extends FormRequest
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
        $this->merge([
            'es_portada' => $this->has('es_portada') ? 1 : 0,
            'orden'      => $this->input('orden', 0), // Valor por defecto 0 si no se envía
        ]);
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => ['required', 'exists:rsv_catalogo_inmueble,id'],
            'url_archivo'              => ['required', 'file', 'mimes:jpeg,png,jpg,webp,mp4,mov,avi', 'max:20480'], // Máx 20MB
            'tipo_multimedia'          => ['required', 'string', 'max:50'],
            'orden'                    => ['nullable', 'integer'],
            'es_portada'               => ['required', 'boolean'],
        ];
    }

    /**
     * Mensajes y atributos legibles.
     */
    public function attributes(): array
    {
        return [
            'id_rsv_catalogo_inmueble' => 'inmueble',
            'url_archivo'              => 'archivo multimedia',
            'tipo_multimedia'          => 'tipo de multimedia',
            'orden'                    => 'orden de visualización',
            'es_portada'               => 'es portada',
        ];
    }

    public function messages(): array
    {
        return [
            'url_archivo.max'   => 'El archivo no debe pesar más de 20MB.',
            'url_archivo.mimes' => 'El archivo debe ser una imagen (jpeg, png, webp) o video (mp4, mov, avi).',
        ];
    }
}
