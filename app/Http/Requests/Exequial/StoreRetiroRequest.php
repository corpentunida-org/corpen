<?php

namespace App\Http\Requests\Exequial;

use App\Models\Exequiales\ComaeExCli;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRetiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fechaAfiliacion = ComaeExCli::where('cod_cli', $this->route('cedula'))->value('fec_ing');

        $reglasFecha = [
            'required',
            'date',
            'before_or_equal:today',
            // "máximo 6 meses antes del registro"
            'after_or_equal:' . Carbon::now()->subMonths(6)->toDateString(),
        ];

        if ($fechaAfiliacion) {
            $reglasFecha[] = 'after:' . Carbon::parse($fechaAfiliacion)->toDateString();
        }

        return [
            'fecha_retiro' => $reglasFecha,
            'observaciones' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_retiro.required' => 'La fecha de retiro es obligatoria.',
            'fecha_retiro.before_or_equal' => 'La fecha de retiro no puede ser una fecha futura.',
            'fecha_retiro.after_or_equal' => 'La fecha de retiro no puede ser de hace más de 6 meses.',
            'fecha_retiro.after' => 'La fecha de retiro debe ser posterior a la fecha de afiliación del titular.',
            'observaciones.required' => 'Las observaciones son obligatorias.',
        ];
    }
}
