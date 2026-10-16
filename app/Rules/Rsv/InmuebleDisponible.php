<?php

namespace App\Rules\Rsv;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Rsv\Reserva;
use Carbon\Carbon;

class InmuebleDisponible implements ValidationRule
{
    /**
     * @param int|string $inmuebleId ID del inmueble a validar
     * @param string|Carbon|null $fechaInicio Fecha de inicio/check-in
     * @param string|Carbon|null $fechaFin Fecha de fin/check-out
     * @param int|string|null $reservaIdIgnore ID de la reserva actual (para ignorarse en edicion)
     * @param array $estadosInactivos IDs de estados que cancelan la reserva (ej: [4, 5] -> Cancelada, Rechazada)
     */
    public function __construct(
        protected int|string $inmuebleId,
        protected string|Carbon|null $fechaInicio,
        protected string|Carbon|null $fechaFin,
        protected int|string|null $reservaIdIgnore = null,
        protected array $estadosInactivos = []
    ) {}

    /**
     * Ejecuta la regla de validación.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si faltan parámetros clave, permitimos que las reglas 'required' o 'date' respondan primero
        if (empty($this->inmuebleId) || empty($this->fechaInicio) || empty($this->fechaFin)) {
            return;
        }

        try {
            $inicio = Carbon::parse($this->fechaInicio);
            $fin = Carbon::parse($this->fechaFin);
        } catch (\Throwable $e) {
            return; // Formato invalido manejado por 'date'
        }

        // Si la fecha de inicio es mayor o igual a la de fin, lo maneja la regla 'after:fecha_inicio'
        if ($inicio->greaterThanOrEqualTo($fin)) {
            return;
        }

        /*
         * Criterio de Solapamiento Turístico/Hotelero:
         * Hay cruce cuando: (Nueva_Entrada < Reserva_Existente_Salida) Y (Nueva_Salida > Reserva_Existente_Entrada)
         * Esto permite que si la reserva A sale el 10 de Mayo, la reserva B pueda ingresar el mismo 10 de Mayo.
         */
        $existeSolapamiento = Reserva::where('id_rsv_catalogo_inmueble', $this->inmuebleId)
            ->when(!empty($this->estadosInactivos), function ($q) {
                $q->whereNotIn('id_rsv_statuses', $this->estadosInactivos);
            })
            ->when(!empty($this->reservaIdIgnore), function ($q) {
                $q->where('id', '!=', $this->reservaIdIgnore);
            })
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('fecha_inicio', '<', $fin)
                  ->where('fecha_fin', '>', $inicio);
            })
            ->exists();

        if ($existeSolapamiento) {
            $fail('El inmueble no se encuentra disponible para el rango de fechas seleccionado.');
        }
    }
}
