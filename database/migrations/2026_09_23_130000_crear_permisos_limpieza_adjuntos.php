<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Dos permisos separados a propósito, no uno solo:
 *  - admin.adjuntos.index: solo VER el resumen de cuántos soportes hay por año (de solo
 *    lectura, sin riesgo).
 *  - admin.adjuntos.limpiar: poder BORRAR de verdad los archivos de un año en S3 (irreversible).
 * Separarlos permite, más adelante, dar acceso de consulta a alguien sin darle poder de borrar
 * nada. Aditiva: se asignan solo a superadmin y admindesarrollo, los mismos que ya administran
 * el resto de configuración global del sistema — nadie más pierde ni gana nada más.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['admin.adjuntos.index', 'admin.adjuntos.limpiar'] as $permiso) {
            if (!DB::table('permissions')->where('name', $permiso)->exists()) {
                DB::table('permissions')->insert([
                    'name' => $permiso,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $permisoIds = DB::table('permissions')
            ->whereIn('name', ['admin.adjuntos.index', 'admin.adjuntos.limpiar'])
            ->pluck('id', 'name');

        $roleIds = DB::table('roles')->whereIn('name', ['superadmin', 'admindesarrollo'])->pluck('id');

        foreach ($roleIds as $rid) {
            foreach ($permisoIds as $pid) {
                if (!DB::table('role_has_permissions')->where('role_id', $rid)->where('permission_id', $pid)->exists()) {
                    DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $rid]);
                }
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $permisoIds = DB::table('permissions')
            ->whereIn('name', ['admin.adjuntos.index', 'admin.adjuntos.limpiar'])
            ->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permisoIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permisoIds)->delete();
        DB::table('permissions')->whereIn('id', $permisoIds)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
