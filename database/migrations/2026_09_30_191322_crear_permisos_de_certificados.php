<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * El módulo Certificados solo tenía menu.certificados (visibilidad del menú, no controla nada
 * adentro) — cualquiera con acceso al menú podía usar las 5 secciones completas sin distinción.
 * Un permiso por sección del menú (mismo criterio que layouts/actions/certificados.blade.php),
 * para poder dar/quitar acceso a secciones puntuales por perfil más adelante:
 *  - certificados.operaciones.index: Matriz de Cartera y Cobros (Motor de Operaciones).
 *  - certificados.frontdesk.index: Atención y Clientes (portal de consulta en mostrador).
 *  - certificados.catalogos.index: Reglas y Parámetros — cubre tanto catalogos.* como config.*
 *    en routes/web.php, que son alias de la misma pantalla central (ConfiguracionController).
 *  - certificados.ingesta.index: Subir Excel / Archivos.
 *  - certificados.auditoria.index: Registro de Actividad.
 * Asignados de entrada a los mismos perfiles que ya tenían menu.certificados (superadmin,
 * certificados, carteraadmon, admindesarrollo, administraciónadmon) — aditivo, nadie pierde
 * acceso a lo que ya podía usar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permisos = [
            'certificados.operaciones.index',
            'certificados.frontdesk.index',
            'certificados.catalogos.index',
            'certificados.ingesta.index',
            'certificados.auditoria.index',
        ];

        foreach ($permisos as $permiso) {
            if (! DB::table('permissions')->where('name', $permiso)->exists()) {
                DB::table('permissions')->insert([
                    'name' => $permiso,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $permisoIds = DB::table('permissions')->whereIn('name', $permisos)->pluck('id', 'name');

        $roleIds = DB::table('roles')
            ->whereIn('name', ['superadmin', 'certificados', 'carteraadmon', 'admindesarrollo', 'administraciónadmon'])
            ->pluck('id');

        foreach ($roleIds as $rid) {
            foreach ($permisoIds as $pid) {
                if (! DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $pid)->exists()) {
                    DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $rid]);
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $permisos = [
            'certificados.operaciones.index',
            'certificados.frontdesk.index',
            'certificados.catalogos.index',
            'certificados.ingesta.index',
            'certificados.auditoria.index',
        ];

        $permisoIds = DB::table('permissions')->whereIn('name', $permisos)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permisoIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permisoIds)->delete();
        DB::table('permissions')->whereIn('id', $permisoIds)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
