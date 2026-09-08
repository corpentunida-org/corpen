<?php

namespace App\Services\Sgrh;

use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionColectiva;
use App\Models\Sgrh\VacacionPolitica;
use App\Models\Sgrh\VacacionSolicitud;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Reglas semánticas de una solicitud de vacaciones — cruzan Empleado, VacacionSolicitud y
 * VacacionColectiva, por eso viven aquí y no en un FormRequest (que no tiene forma limpia de
 * acceder al saldo calculado ni a los decretos activos). El FormRequest de la solicitud solo
 * valida lo sintáctico (formato/presencia de fechas); esto valida el resto antes de guardar.
 */
class VacacionValidador
{
    public function __construct(
        private VacacionSaldoCalculador $saldoCalculador,
        private FestivoColombiaCalculador $festivoCalculador,
    ) {
    }

    /**
     * Días hábiles entre dos fechas, ambas inclusive: excluye sábados, domingos y los festivos
     * colombianos de FestivoColombiaCalculador (calculados por año, con los ajustes de RRHH ya
     * aplicados).
     */
    public function calcularDiasHabiles(Carbon $inicio, Carbon $fin): float
    {
        $dias = 0;
        $cursor = $inicio->copy();
        $festivosPorAnio = [];

        while ($cursor->lte($fin)) {
            $festivosPorAnio[$cursor->year] ??= $this->festivoCalculador->festivosDelAnio($cursor->year)
                ->map(fn (Carbon $f) => $f->format('Y-m-d'))
                ->all();

            if (!$cursor->isWeekend() && !in_array($cursor->format('Y-m-d'), $festivosPorAnio[$cursor->year], true)) {
                $dias++;
            }
            $cursor->addDay();
        }

        return (float) $dias;
    }

    /**
     * Lanza ValidationException si la solicitud no cumple alguna regla de negocio. $esAdelantada
     * y $autorizadaPorRrhhId ya vienen resueltos por el controller (dependen de si quien arma la
     * solicitud tiene el permiso sgrh.vacacion.solicitud.rrhh).
     */
    public function validar(
        Empleado $empleado,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        float $diasHabiles,
        bool $esAdelantada,
        ?int $autorizadaPorRrhhId,
        ?int $ignorarSolicitudId = null,
    ): void {
        if ($fechaInicio->isPast()) {
            throw ValidationException::withMessages(['fecha_inicio' => 'La fecha de inicio debe ser futura.']);
        }

        if ($fechaFin->lt($fechaInicio)) {
            throw ValidationException::withMessages(['fecha_fin' => 'La fecha de fin no puede ser anterior a la de inicio.']);
        }

        if ($empleado->antiguedad_en_meses === null) {
            throw ValidationException::withMessages([
                'empleado_id' => 'La antigüedad de este colaborador no está determinada (sin fecha de inicio de contrato registrada). Contacta a RRHH.',
            ]);
        }

        $solapaPropia = VacacionSolicitud::where('empleado_id', $empleado->id)
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->when($ignorarSolicitudId, fn ($q) => $q->where('id', '!=', $ignorarSolicitudId))
            ->solapaCon($fechaInicio, $fechaFin)
            ->exists();

        if ($solapaPropia) {
            throw ValidationException::withMessages(['fecha_inicio' => 'Ya tienes una solicitud pendiente o aprobada que se cruza con estas fechas.']);
        }

        $bloqueo = VacacionColectiva::where('tipo', 'bloqueo')
            ->where('estado', 'activa')
            ->where('fecha_inicio', '<=', $fechaFin)
            ->where('fecha_fin', '>=', $fechaInicio)
            ->get()
            ->first(fn (VacacionColectiva $c) => $c->aplicaA($empleado));

        if ($bloqueo) {
            throw ValidationException::withMessages([
                'fecha_inicio' => "No se pueden solicitar vacaciones en este rango: {$bloqueo->descripcion} ({$bloqueo->fecha_inicio->format('d/m/Y')} a {$bloqueo->fecha_fin->format('d/m/Y')}).",
            ]);
        }

        $politica = VacacionPolitica::vigente();

        if (!$esAdelantada) {
            $saldo = $this->saldoCalculador->saldoActual($empleado, $politica);

            if ($saldo !== null && $saldo < $diasHabiles) {
                throw ValidationException::withMessages([
                    'dias_habiles' => "Saldo insuficiente: disponible {$saldo} día(s), solicitados {$diasHabiles}. Si aplica, RRHH puede autorizar un adelanto.",
                ]);
            }

            return;
        }

        if (!$politica?->permite_adelanto) {
            throw ValidationException::withMessages(['es_adelantada' => 'La política vigente no permite vacaciones adelantadas.']);
        }

        if (!$autorizadaPorRrhhId) {
            throw ValidationException::withMessages(['es_adelantada' => 'Una vacación adelantada requiere autorización explícita de RRHH.']);
        }

        if ($politica->max_dias_adelanto !== null) {
            $saldo = $this->saldoCalculador->saldoActual($empleado, $politica) ?? 0;
            $saldoResultante = $saldo - $diasHabiles;

            if ($saldoResultante < -$politica->max_dias_adelanto) {
                throw ValidationException::withMessages([
                    'dias_habiles' => "Este adelanto dejaría un saldo negativo de {$saldoResultante} días, por encima del máximo permitido ({$politica->max_dias_adelanto}).",
                ]);
            }
        }
    }
}
