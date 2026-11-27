<?php

namespace Database\Seeders;

use App\Services\Rsv\RsvAcceso;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permisos y roles del módulo RSV (reservas nuevo). Idempotente: se puede correr varias veces.
 *
 * Permisos y perfiles definidos en App\Services\Rsv\RsvAcceso (fuente única):
 *   rsv.admin · rsv.contabilidad · rsv.soporte · rsv.hospedador (fase 2, sin permisos aún)
 * El asociado no necesita nada: cualquier usuario autenticado opera solo sobre SUS reservas.
 *
 * rsv.reservas.excepcion: permite crear una reserva que supera la política "Rsv. Máx/Año"
 * (máximo de reservas por asociado en 365 días), con justificación obligatoria auditada.
 *
 * Asignar a una persona: un rol (p. ej. $user->assignRole('rsv.contabilidad')) o permisos
 * directos sueltos desde la Matriz de Permisos.
 *
 *   php artisan db:seed --class=RsvPermissionsSeeder
 */
class RsvPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RsvAcceso::ROLES as $rol => $permisos) {
            foreach ($permisos as $p) {
                Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            }
            Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($permisos);
        }

        // Perfil del ASOCIADO del módulo nuevo: solo ve "RSV → Portal Cliente" en el menú. No lleva
        // permisos rsv.*: el asociado opera únicamente sobre sus reservas (validado en controladores).
        Permission::firstOrCreate(['name' => 'menu.rsv', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'asociadosv02', 'guard_name' => 'web'], ['area' => 'asociado'])
            ->syncPermissions(['menu.rsv']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
