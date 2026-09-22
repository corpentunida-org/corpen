<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * interacciones.listado.todos es global: quien lo tiene ve las interacciones de TODOS los
 * agentes, de todas las áreas. Con varias áreas ya activas (Cartera, Seguros...) eso es
 * demasiado para un supervisor de un área — un segurosadmon no debería ver las llamadas de
 * cobranza de Cartera. interacciones.listado.area es la versión acotada: ve/filtra solo entre
 * los agentes cuyo perfil tiene la misma área que el suyo (ver InteractionController@index).
 *
 * Aditiva: no le quita nada a nadie. Solo se asigna aquí a segurosadmon, que hoy no tiene
 * ninguno de los dos permisos (no puede ver el trabajo de su equipo). Los perfiles que ya tenían
 * el permiso global (carteraadmon, admindesarrollo, superadmin, administraciónadmon) se dejan
 * exactamente igual — decidir si alguno debe bajar a "solo su área" es aparte.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('permissions')->where('name', 'interacciones.listado.area')->exists()) {
            DB::table('permissions')->insert([
                'name' => 'interacciones.listado.area',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $pid = DB::table('permissions')->where('name', 'interacciones.listado.area')->value('id');

        $rid = DB::table('roles')->where('name', 'segurosadmon')->value('id');
        if ($rid && !DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $pid)->exists()) {
            DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $rid]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $pid = DB::table('permissions')->where('name', 'interacciones.listado.area')->value('id');
        if ($pid) {
            DB::table('role_has_permissions')->where('permission_id', $pid)->delete();
            DB::table('model_has_permissions')->where('permission_id', $pid)->delete();
            DB::table('permissions')->where('id', $pid)->delete();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
