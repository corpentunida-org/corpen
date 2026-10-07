<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * maestras.terceros.update: hoy editar/actualizar un Tercero (GET edit / PUT update de
 * maestras.terceros) solo exige estar autenticado, sin permiso dedicado. Se crea este permiso
 * para gatear esas dos rutas específicamente (no todo el resource: index/create/show/destroy
 * quedan igual que antes, fuera de alcance de este cambio).
 *
 * Se asigna a quienes ya administraban Terceros desde Maestras (mismos roles de
 * maestras.terceros.importar) más los roles de Exequiales y Seguros que ahora tienen un botón
 * "Editar Tercero" en sus propias pantallas (exequial.asociados.update / seguros.poliza.update),
 * para que ese botón nuevo no quede inútil por falta de permiso. Aditiva: nadie pierde acceso.
 */
return new class extends Migration
{
    private string $permiso = 'maestras.terceros.update';

    private array $roles = [
        'superadmin',
        'maestras',
        'contabilidadadmon',
        'adminjunior',
        'admindesarrollo',
        'administraciónadmon',
        'administraciónusers',
        'exequial',
        'exequialesadmon',
        'segurosusers',
        'segurosadmon',
    ];

    public function up(): void
    {
        if (!DB::table('permissions')->where('name', $this->permiso)->exists()) {
            DB::table('permissions')->insert([
                'name' => $this->permiso,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permisoId = DB::table('permissions')->where('name', $this->permiso)->value('id');

        $roleIds = DB::table('roles')->whereIn('name', $this->roles)->pluck('id');

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
        $permisoId = DB::table('permissions')->where('name', $this->permiso)->value('id');
        if ($permisoId) {
            DB::table('role_has_permissions')->where('permission_id', $permisoId)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permisoId)->delete();
            DB::table('permissions')->where('id', $permisoId)->delete();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
