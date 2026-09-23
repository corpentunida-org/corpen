<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Mismo criterio que interacciones.listado.area (ver esa migración): interacciones.informes.
 * todosagentes es global (cualquier agente, cualquier área). interacciones.informes.area es la
 * versión acotada — el informe se calcula solo con los agentes cuyo perfil tiene la misma área
 * que el suyo (ver InteractionController::alcanceInformes()).
 *
 * Aditiva: no le quita nada a nadie. Se asigna a los mismos roles que ya tienen
 * interacciones.listado.area (segurosadmon, sgrhusers), para que quien ya puede navegar la
 * agenda de su área también pueda ver el informe agregado de esa misma área.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('permissions')->where('name', 'interacciones.informes.area')->exists()) {
            DB::table('permissions')->insert([
                'name' => 'interacciones.informes.area',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $pid = DB::table('permissions')->where('name', 'interacciones.informes.area')->value('id');

        $rolesDestino = DB::table('roles')
            ->whereIn('id', function ($q) {
                $q->select('role_id')->from('role_has_permissions')
                    ->whereIn('permission_id', function ($q2) {
                        $q2->select('id')->from('permissions')->where('name', 'interacciones.listado.area');
                    });
            })
            ->pluck('id');

        foreach ($rolesDestino as $rid) {
            if (!DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $pid)->exists()) {
                DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $rid]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $pid = DB::table('permissions')->where('name', 'interacciones.informes.area')->value('id');
        if ($pid) {
            DB::table('role_has_permissions')->where('permission_id', $pid)->delete();
            DB::table('model_has_permissions')->where('permission_id', $pid)->delete();
            DB::table('permissions')->where('id', $pid)->delete();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
