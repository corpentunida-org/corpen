<?php

namespace App\Services\Sgrh;

use App\Models\Sgrh\FestivoAjuste;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Festivos colombianos: se calculan por algoritmo (no se cargan a mano año por año), y RRHH
 * puede ajustarlos puntualmente vía sgrh_festivos_ajustes — agregar una fecha adicional (ej.
 * un festivo local/empresarial) o excluir una que el cálculo automático marcó pero no debe
 * aplicar. El resultado final SIEMPRE es: calculados automáticamente, más los 'agregado', menos
 * los 'excluido'.
 */
class FestivoColombiaCalculador
{
    /**
     * Fechas festivas de Colombia para un año dado, ya con los ajustes de RRHH aplicados.
     *
     * @return Collection<int, Carbon>
     */
    public function festivosDelAnio(int $anio): Collection
    {
        $calculados = $this->calcularAutomaticos($anio);

        $agregados = FestivoAjuste::where('tipo', 'agregado')
            ->whereYear('fecha', $anio)
            ->pluck('fecha');

        $excluidos = FestivoAjuste::where('tipo', 'excluido')
            ->whereYear('fecha', $anio)
            ->pluck('fecha')
            ->map(fn ($f) => $f->format('Y-m-d'))
            ->all();

        return $calculados
            ->reject(fn (Carbon $fecha) => in_array($fecha->format('Y-m-d'), $excluidos, true))
            ->merge($agregados)
            ->unique(fn (Carbon $fecha) => $fecha->format('Y-m-d'))
            ->values();
    }

    public function esFestivo(Carbon $fecha): bool
    {
        return $this->festivosDelAnio($fecha->year)->contains(
            fn (Carbon $festivo) => $festivo->isSameDay($fecha)
        );
    }

    /**
     * Festivos fijos, festivos que la Ley 51 de 1983 ("Ley Emiliani") traslada al lunes
     * siguiente si no caen en lunes, y los que dependen de la fecha de Pascua (Jueves y
     * Viernes Santo no se trasladan; Ascensión/Corpus Christi/Sagrado Corazón sí).
     *
     * @return Collection<int, Carbon>
     */
    private function calcularAutomaticos(int $anio): Collection
    {
        $pascua = $this->domingoDePascua($anio);

        $fijos = [
            Carbon::create($anio, 1, 1),   // Año Nuevo
            Carbon::create($anio, 5, 1),   // Día del Trabajo
            Carbon::create($anio, 7, 20),  // Independencia
            Carbon::create($anio, 8, 7),   // Batalla de Boyacá
            Carbon::create($anio, 12, 8),  // Inmaculada Concepción
            Carbon::create($anio, 12, 25), // Navidad
            $pascua->copy()->subDays(3),   // Jueves Santo
            $pascua->copy()->subDays(2),   // Viernes Santo
        ];

        $trasladables = [
            Carbon::create($anio, 1, 6),    // Reyes Magos
            Carbon::create($anio, 3, 19),   // San José
            $pascua->copy()->addDays(43),   // Ascensión del Señor
            $pascua->copy()->addDays(64),   // Corpus Christi
            $pascua->copy()->addDays(71),   // Sagrado Corazón
            Carbon::create($anio, 6, 29),   // San Pedro y San Pablo
            Carbon::create($anio, 8, 15),   // Asunción de la Virgen
            Carbon::create($anio, 10, 12),  // Día de la Raza
            Carbon::create($anio, 11, 1),   // Todos los Santos
            Carbon::create($anio, 11, 11),  // Independencia de Cartagena
        ];

        $trasladados = array_map(fn (Carbon $f) => $this->trasladarALunes($f), $trasladables);

        return collect(array_merge($fijos, $trasladados))->sort()->values();
    }

    private function trasladarALunes(Carbon $fecha): Carbon
    {
        return $fecha->isMonday() ? $fecha : $fecha->copy()->next(Carbon::MONDAY);
    }

    /**
     * Algoritmo de Meeus/Jones/Butcher para la fecha del domingo de Pascua (calendario
     * gregoriano) — de aquí se derivan todos los festivos móviles de Colombia.
     */
    private function domingoDePascua(int $anio): Carbon
    {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $numero = $h + $l - 7 * $m + 114;
        $mes = intdiv($numero, 31);
        $dia = ($numero % 31) + 1;

        return Carbon::create($anio, $mes, $dia);
    }
}
