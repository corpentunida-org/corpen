<?php

namespace App\Services\Sgrh;

use App\Models\Sgrh\Cargo;
use App\Models\Sgrh\Contrato;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionSolicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Resuelve la cadena Empleado -> Contrato activo -> Cargo -> jefe_inmediato_id/director_id ->
 * Contrato activo de ese cargo -> Empleado -> User, que hoy no existe en ningún lado del
 * código (Cargo::jefeInmediato()/director() están definidos pero sin consumidores). Se
 * concentra aquí porque la consumen 4 controladores distintos (solicitud, saldo, calendario,
 * alertas) y evita reimplementar la cadena en cada uno.
 */
class VacacionAprobadorResolver
{
    /**
     * Colaborador de Sgrh de la cuenta logueada, por coincidencia de correo (ver
     * Empleado::getUserAttribute() / User::getEmpleadoSgrhAttribute()).
     */
    public function empleadoDeUsuario(User $user): ?Empleado
    {
        return $user->empleado_sgrh;
    }

    public function jefeInmediatoDe(Empleado $empleado): ?Empleado
    {
        $cargo = Cargo::find($empleado->cargo_id);

        if (!$cargo?->jefe_inmediato_id) {
            return null;
        }

        return $this->empleadoDelCargo($cargo->jefe_inmediato_id);
    }

    public function directorDe(Empleado $empleado): ?Empleado
    {
        $cargo = Cargo::find($empleado->cargo_id);

        if (!$cargo?->director_id) {
            return null;
        }

        return $this->empleadoDelCargo($cargo->director_id);
    }

    /**
     * Empleados cuyo cargo reporta directamente (jefe_inmediato_id) al cargo de $jefe.
     */
    public function equipoDirectoDe(Empleado $jefe): Collection
    {
        $cargoJefe = Cargo::find($jefe->cargo_id);

        if (!$cargoJefe) {
            return collect();
        }

        $cargoIds = Cargo::where('jefe_inmediato_id', $cargoJefe->id)->pluck('id');

        return $this->empleadosDeCargos($cargoIds);
    }

    /**
     * Empleados cuyo cargo cuelga de $director: primero los que declaran director_id
     * directamente sobre su cargo (fuente primaria del campo); como respaldo, para cargos que
     * no tengan director_id poblado, se sube por jefe_inmediato_id (máximo 10 niveles, con
     * guarda anti-ciclo) hasta encontrar coincidencia — decisión confirmada con el usuario para
     * no depender de que RRHH pueble director_id en cada cargo intermedio del organigrama.
     */
    public function equipoExtendidoDe(Empleado $director): Collection
    {
        $cargoDirector = Cargo::find($director->cargo_id);

        if (!$cargoDirector) {
            return collect();
        }

        $cargosPorId = Cargo::all()->keyBy('id');
        $cargoIds = $cargosPorId->filter(
            fn (Cargo $cargo) => $this->cargoReportaADirector($cargo, $cargoDirector->id, $cargosPorId)
        )->pluck('id');

        return $this->empleadosDeCargos($cargoIds);
    }

    private function cargoReportaADirector(Cargo $cargo, int $cargoDirectorId, Collection $cargosPorId, int $profundidad = 0): bool
    {
        if ($cargo->director_id === $cargoDirectorId) {
            return true;
        }

        // Ya tiene su propio director explícito y es distinto: no sigue subiendo por esta rama.
        if ($cargo->director_id !== null) {
            return false;
        }

        if ($profundidad >= 10 || !$cargo->jefe_inmediato_id) {
            return false;
        }

        $jefe = $cargosPorId->get($cargo->jefe_inmediato_id);

        if (!$jefe) {
            return false;
        }

        return $this->cargoReportaADirector($jefe, $cargoDirectorId, $cargosPorId, $profundidad + 1);
    }

    /**
     * Quién puede resolver (aprobar/rechazar) esta solicitud concreta: el jefe inmediato exacto
     * del solicitante, el director en cuyo equipo extendido cae el solicitante, o un usuario con
     * el permiso de respaldo de RRHH cuando el cargo no tiene jefe inmediato o la solicitud es
     * adelantada. El primero que resuelve la deja cerrada — ver VacacionSolicitudController,
     * que debe actualizar con `->where('estado', 'pendiente')` para blindar la condición de
     * carrera entre dos aprobadores simultáneos.
     */
    public function puedeResolver(User $user, VacacionSolicitud $solicitud): bool
    {
        $solicitante = $solicitud->empleado;
        $empleadoUser = $this->empleadoDeUsuario($user);

        if ($empleadoUser) {
            $jefe = $this->jefeInmediatoDe($solicitante);
            if ($jefe && $jefe->id === $empleadoUser->id) {
                return true;
            }

            if ($this->equipoExtendidoDe($empleadoUser)->contains('id', $solicitante->id)) {
                return true;
            }
        }

        if (!$user->can('sgrh.vacacion.solicitud.rrhh')) {
            return false;
        }

        $cargoSolicitante = Cargo::find($solicitante->cargo_id);
        $sinJefeInmediato = !$cargoSolicitante?->jefe_inmediato_id;

        return $sinJefeInmediato || $solicitud->es_adelantada;
    }

    /**
     * Query base de solicitudes visibles para este usuario: todas si tiene visión global de
     * RRHH (sgrh.vacacion.saldo.index), o las de su equipo directo + extendido + (si tiene el
     * permiso de respaldo) las de cargos sin jefe inmediato asignado.
     */
    public function solicitudesVisiblesPara(User $user): Builder
    {
        if ($user->can('sgrh.vacacion.saldo.index')) {
            return VacacionSolicitud::query();
        }

        $empleadoUser = $this->empleadoDeUsuario($user);

        if (!$empleadoUser) {
            return VacacionSolicitud::whereRaw('1 = 0');
        }

        $empleadoIds = $this->equipoDirectoDe($empleadoUser)->pluck('id')
            ->merge($this->equipoExtendidoDe($empleadoUser)->pluck('id'));

        if ($user->can('sgrh.vacacion.solicitud.rrhh')) {
            $cargosSinJefe = Cargo::whereNull('jefe_inmediato_id')->pluck('id');
            $empleadoIds = $empleadoIds->merge($this->empleadosDeCargos($cargosSinJefe)->pluck('id'));
        }

        return VacacionSolicitud::whereIn('empleado_id', $empleadoIds->unique());
    }

    private function empleadoDelCargo(int $cargoId): ?Empleado
    {
        $contrato = Contrato::where('cargo_id', $cargoId)->where('estado', 'Activo')->first();

        return $contrato?->empleado;
    }

    private function empleadosDeCargos($cargoIds): Collection
    {
        $empleadoIds = Contrato::where('estado', 'Activo')->whereIn('cargo_id', $cargoIds)->pluck('empleado_id');

        return Empleado::whereIn('id', $empleadoIds)->get();
    }
}
