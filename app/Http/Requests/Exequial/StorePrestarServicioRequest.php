<?php

namespace App\Http\Requests\Exequial;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Cubre las dos variantes del mismo formulario "Prestar Servicio": una para cuando fallece el
 * titular/pastor (asociados/show.blade.php, pastor=true) y otra para un beneficiario
 * (beneficiarios/show.blade.php, pastor=false). Solo se marcan required los campos que ambas
 * vistas realmente exigen en el HTML (lugarFallecimiento/fechaFallecimiento/horaFallecimiento);
 * el resto queda nullable porque en al menos una de las dos variantes no es obligatorio.
 */
class StorePrestarServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pastor' => ['required', 'in:true,false'],
            'cedulaTitular' => ['required', 'string', 'max:20'],
            'nameTitular' => ['required', 'string', 'max:150'],
            'nameBeneficiary' => ['required', 'string', 'max:150'],
            'cedulaFallecido' => ['required', 'string', 'max:20'],
            'lugarFallecimiento' => ['required', 'string', 'max:150'],
            'tipoMuerte' => ['required', 'in:Natural,Accidental,Suicidio,Homicidio,Indeterminada'],
            'ciudad_fallecimiento_id' => ['nullable', 'integer', 'exists:geo_ciudades,id_ciudad'],
            'fechaFallecimiento' => ['required', 'date'],
            'horaFallecimiento' => ['required'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'contacto2' => ['nullable', 'string', 'max:150'],
            'telefonoContacto' => ['nullable', 'string', 'max:30'],
            'telefonoContacto2' => ['nullable', 'string', 'max:30'],
            'genero' => ['nullable', 'in:F,M'],
            'estadonuevo' => ['nullable', 'in:1,2,3'],
            'parentesco' => ['nullable', 'string', 'max:10'],
            'fecNacFallecido' => ['nullable', 'date'],
            'dateInit' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric'],
            'codePlan' => ['nullable', 'string', 'max:10'],
        ];
    }
}
