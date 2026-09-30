<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * La sección "Informes y Analítica" (InformeController, agregada por sistemas/reservas después
 * de la migración crear_permisos_de_certificados) quedó sin permiso propio — su bloque de rutas
 * se insertó por accidente DENTRO del grupo candirect:certificados.auditoria.index (artefacto del
 * merge), así que hoy depende del permiso de Auditoría en vez de tener el suyo. Mismo criterio
 * que las otras 5 secciones: un permiso por ítem del menú.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permiso = 'certificados.informes.index';

        if (! DB::table('permissions')->where('name', $permiso)->exists()) {
            DB::table('permissions')->insert([
                'name' => $permiso,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permisoId = DB::table('permissions')->where('name', $permiso)->value('id');

        $roleIds = DB::table('roles')
            ->whereIn('name', ['superadmin', 'certificados', 'carteraadmon', 'admindesarrollo', 'administraciónadmon'])
            ->pluck('id');

        foreach ($roleIds as $rid) {
            if (! DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $permisoId)->exists()) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permisoId, 'role_id' => $rid]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $permisoId = DB::table('permissions')->where('name', 'certificados.informes.index')->value('id');
        if ($permisoId) {
            DB::table('role_has_permissions')->where('permission_id', $permisoId)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permisoId)->delete();
            DB::table('permissions')->where('id', $permisoId)->delete();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
