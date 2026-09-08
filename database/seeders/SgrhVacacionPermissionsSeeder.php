<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Permisos del módulo de Vacaciones (Sgrh), mismo criterio que SgrhPermissionsSeeder: idempotente
 * (firstOrCreate), no duplica ni pisa nada si se corre de nuevo.
 */
class SgrhVacacionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'sgrh.vacacion.politica.index',
            'sgrh.vacacion.politica.store',
            'sgrh.vacacion.ajuste.store',
            'sgrh.vacacion.colectiva.index',
            'sgrh.vacacion.colectiva.store',
            'sgrh.vacacion.colectiva.destroy',
            // Ver saldo/historial de CUALQUIER colaborador — implica visión global de RRHH,
            // también usado por VacacionAprobadorResolver::solicitudesVisiblesPara() para
            // decidir quién ve todas las solicitudes en vez de solo su equipo.
            'sgrh.vacacion.saldo.index',
            // Mis solicitudes + las de mi equipo (vía el resolver) — se otorga junto con
            // solicitud.store a cualquier usuario con Empleado vinculado.
            'sgrh.vacacion.solicitud.index',
            'sgrh.vacacion.solicitud.store',
            // Aprobar/rechazar — se asigna a roles Jefe/Director y también a RRHH.
            'sgrh.vacacion.solicitud.resolver',
            // Respaldo: aprobar directo como RRHH cuando el cargo no tiene jefe inmediato, o
            // autorizar/crear una solicitud adelantada.
            'sgrh.vacacion.solicitud.rrhh',
            'sgrh.vacacion.solicitud.destroy',
            'sgrh.vacacion.calendario.index',
            'sgrh.vacacion.alertas.index',
            'sgrh.vacacion.festivo.index',
            'sgrh.vacacion.festivo.store',
            'sgrh.vacacion.festivo.destroy',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }
    }
}
