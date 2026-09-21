<?php

use App\Services\Admin\PermisosPorRolService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;

/**
 * El menú lateral dejó de armarse por NOMBRE de rol y pasa a armarse por permisos: cada módulo
 * del menú (un archivo en resources/views/layouts/actions/) se muestra si la persona tiene el
 * permiso `menu.<archivo>` — que le llega desde su perfil (Matriz de Permisos).
 *
 * Para que nadie pierda su menú al desplegar, cada rol existente recibe el permiso de menú del
 * módulo que ya mostraba (rol "cartera" -> menu.cartera, etc.). Es aditivo e idempotente.
 * AsociadosAdmon recibe además todo lo del rol Asociado: con "un solo perfil por usuario" ya no
 * se le puede sumar el perfil Asociado aparte.
 */
return new class extends Migration
{
    public function up(): void
    {
        $modulos = collect(File::files(resource_path('views/layouts/actions')))
            ->map(fn ($f) => str_replace('.blade.php', '', $f->getFilename()))
            ->sort()->values();

        DB::transaction(function () use ($modulos) {
            $permisoDe = [];
            foreach ($modulos as $modulo) {
                $nombre = 'menu.' . $modulo;
                $id = DB::table('permissions')->where('name', $nombre)->where('guard_name', 'web')->value('id');
                if (!$id) {
                    $id = DB::table('permissions')->insertGetId([
                        'name' => $nombre, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
                $permisoDe[strtolower($modulo)] = $id;
            }

            // Cada rol recibe el menú del módulo que ya mostraba (mismo nombre, sin distinguir mayúsculas).
            foreach (DB::table('roles')->get(['id', 'name']) as $rol) {
                $pid = $permisoDe[strtolower($rol->name)] ?? null;
                if ($pid && !DB::table('role_has_permissions')->where('role_id', $rol->id)->where('permission_id', $pid)->exists()) {
                    DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $rol->id]);
                }
            }

            // AsociadosAdmon = todo lo de Asociado + lo propio.
            $asociado = DB::table('roles')->whereRaw('lower(name) = ?', ['asociado'])->value('id');
            $admon = DB::table('roles')->whereRaw('lower(name) = ?', ['asociadosadmon'])->value('id');
            if ($asociado && $admon) {
                $ya = DB::table('role_has_permissions')->where('role_id', $admon)->pluck('permission_id')->all();
                foreach (DB::table('role_has_permissions')->where('role_id', $asociado)->pluck('permission_id') as $pid) {
                    if (!in_array($pid, $ya)) {
                        DB::table('role_has_permissions')->insert(['permission_id' => $pid, 'role_id' => $admon]);
                    }
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(PermisosPorRolService::class)->sincronizarTodos();
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->where('name', 'like', 'menu.%')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
