<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrestarServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'horaFallecimiento' => ['required'],
            'fechaFallecimiento' => ['required', 'date'],
            'lugarFallecimiento' => ['required', 'string', 'max:150'],
            'tipoMuerte' => ['required', 'in:Natural,Accidental,Suicidio,Homicidio,Indeterminada'],
            'ciudad_fallecimiento_id' => ['nullable', 'integer', 'exists:geo_ciudades,id_ciudad'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefonoContacto' => ['nullable', 'string', 'max:30'],
            'contacto2' => ['nullable', 'string', 'max:150'],
            'telefonoContacto2' => ['nullable', 'string', 'max:30'],
            // El <select> de municipio va con "disabled" en la vista (edit.blade.php:70), así
            // que el navegador NUNCA lo envía en el submit pese al "required" del HTML —
            // marcarlo required aquí rechazaría todas las actualizaciones.
            'municipioid' => ['nullable'],
        ];
    }
}
