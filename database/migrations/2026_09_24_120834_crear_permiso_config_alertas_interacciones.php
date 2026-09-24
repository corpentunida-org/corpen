<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permiso para editar la configuración de las alertas de Interacciones (cada cuánto sale el
 * aviso forzado, días seguidos posponiendo antes de escalar, horarios de los correos). Aditiva:
 * mismo criterio que admin.adjuntos.* — solo superadmin y admindesarrollo, los que ya
 * administran el resto de configuración global del sistema.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permiso = 'admin.alertas_interacciones.config';

        if (!DB::table('permissions')->where('name', $permiso)->exists()) {
            DB::table('permissions')->insert([
                'name' => $permiso,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permisoId = DB::table('permissions')->where('name', $permiso)->value('id');
        $roleIds = DB::table('roles')->whereIn('name', ['superadmin', 'admindesarrollo'])->pluck('id');

        foreach ($roleIds as $rid) {
            if (!DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $permisoId)->exists()) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permisoId, 'role_id' => $rid]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $permisoId = DB::table('permissions')->where('name', 'admin.alertas_interacciones.config')->value('id');
        DB::table('role_has_permissions')->where('permission_id', $permisoId)->delete();
        DB::table('model_has_permissions')->where('permission_id', $permisoId)->delete();
        DB::table('permissions')->where('id', $permisoId)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
