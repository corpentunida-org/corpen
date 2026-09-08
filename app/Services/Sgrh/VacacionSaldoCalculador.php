<?php

namespace App\Services\Sgrh;

use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionPolitica;
use App\Models\Sgrh\VacacionSolicitud;

/**
 * El saldo de vacaciones se calcula siempre en vivo, nunca se guarda un total materializado
 * (mismo criterio que Contrato::estaVencido o Empleado::fecha_ingreso) — así nunca queda
 * desincronizado. Lo único persistido son los ajustes manuales de RRHH (ver
 * sgrh_vacacion_saldo_ajustes), que no se pueden derivar de ninguna fórmula.
 */
class VacacionSaldoCalculador
{
    /**
     * Días causados por antigüedad según la política vigente, prorrateados por meses completos
     * de servicio. Null si la antigüedad no está determinada (contrato Indefinido sin
     * fecha_inicio) — quien llama debe tratar ese caso aparte (ver VacacionValidador).
     */
    public function diasCausados(Empleado $empleado, ?VacacionPolitica $politica = null): ?float
    {
        $politica ??= VacacionPolitica::vigente();
        $meses = $empleado->antiguedad_en_meses;

        if ($meses === null || $politica === null) {
            return null;
        }

        $dias = floor($meses * $politica->dias_por_anio / 12);

        if ($politica->max_dias_acumulables !== null) {
            $dias = min($dias, $politica->max_dias_acumulables);
        }

        return (float) $dias;
    }

    /**
     * Días ya comprometidos en solicitudes aprobadas (individuales o generadas por una
     * colectiva obligatoria) — cuentan sin importar si la fecha ya pasó o es futura, porque el
     * saldo ya está descontado desde el momento de la aprobación.
     */
    public function diasTomados(Empleado $empleado): float
    {
        return (float) VacacionSolicitud::where('empleado_id', $empleado->id)
            ->aprobadas()
            ->sum('dias_habiles');
    }

    public function diasAjustados(Empleado $empleado): float
    {
        return (float) $empleado->vacacionAjustes()->sum('dias');
    }

    /**
     * Saldo actual del colaborador. Null si la antigüedad no está determinada — distinto de 0:
     * "sin dato" no es lo mismo que "sin días disponibles", y el llamador (vista, validador)
     * debe mostrarlo/tratarlo distinto.
     */
    public function saldoActual(Empleado $empleado, ?VacacionPolitica $politica = null): ?float
    {
        $causados = $this->diasCausados($empleado, $politica);

        if ($causados === null) {
            return null;
        }

        return $causados - $this->diasTomados($empleado) + $this->diasAjustados($empleado);
    }
}
