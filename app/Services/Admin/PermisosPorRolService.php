<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Los permisos de un usuario provienen ÚNICAMENTE de sus roles (perfiles/grupos): permisos del
 * usuario = unión de lo que tienen sus roles en role_has_permissions, y se asignan desde la
 * Matriz de Permisos. Ya no hay permisos "sueltos" por persona.
 *
 * `candirect` (App\Http\Middleware\CanDirect) sigue verificando model_has_permissions — no se
 * tocó a propósito (cambiarlo alteraría el acceso de toda la app de golpe). En su lugar, esta
 * clase mantiene model_has_permissions como un reflejo calculado de los roles: cualquier cambio
 * en un rol o en la asignación de roles de un usuario debe terminar llamando aquí.
 *
 * Todo es por lotes (una consulta por tabla, inserts/deletes en bloque), no usuario por usuario:
 * un rol como "Asociado" tiene más de mil miembros.
 */
class PermisosPorRolService
{
    private const MODEL_TYPE = 'App\Models\User';

    /** @return array{usuarios:int, agregados:int, quitados:int} */
    public function sincronizarUsuarios(array $userIds): array
    {
        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        $resumen = ['usuarios' => count($userIds), 'agregados' => 0, 'quitados' => 0];
        if (!$userIds) {
            return $resumen;
        }

        $rolesPorUsuario = [];
        foreach (array_chunk($userIds, 1000) as $lote) {
            foreach (DB::table('actions')->whereIn('user_id', $lote)->get(['user_id', 'role_id']) as $a) {
                $rolesPorUsuario[$a->user_id][$a->role_id] = true;
            }
        }

        $permisosPorRol = [];
        foreach (DB::table('role_has_permissions')->get(['role_id', 'permission_id']) as $r) {
            $permisosPorRol[$r->role_id][$r->permission_id] = true;
        }

        $actuales = [];
        foreach (array_chunk($userIds, 1000) as $lote) {
            foreach (DB::table('model_has_permissions')->where('model_type', self::MODEL_TYPE)->whereIn('model_id', $lote)->get(['model_id', 'permission_id']) as $d) {
                $actuales[$d->model_id][$d->permission_id] = true;
            }
        }

        $aInsertar = [];
        $aQuitarPorPermiso = []; // permission_id => [user_id...]
        foreach ($userIds as $uid) {
            $objetivo = [];
            foreach (array_keys($rolesPorUsuario[$uid] ?? []) as $rid) {
                foreach (array_keys($permisosPorRol[$rid] ?? []) as $pid) {
                    $objetivo[$pid] = true;
                }
            }
            $actual = $actuales[$uid] ?? [];

            foreach (array_diff_key($objetivo, $actual) as $pid => $_) {
                $aInsertar[] = ['permission_id' => $pid, 'model_type' => self::MODEL_TYPE, 'model_id' => $uid];
            }
            foreach (array_diff_key($actual, $objetivo) as $pid => $_) {
                $aQuitarPorPermiso[$pid][] = $uid;
            }
        }

        DB::transaction(function () use ($aInsertar, $aQuitarPorPermiso, &$resumen) {
            foreach (array_chunk($aInsertar, 1000) as $lote) {
                DB::table('model_has_permissions')->insert($lote);
            }
            foreach ($aQuitarPorPermiso as $pid => $usuarios) {
                foreach (array_chunk($usuarios, 1000) as $lote) {
                    DB::table('model_has_permissions')
                        ->where('model_type', self::MODEL_TYPE)
                        ->where('permission_id', $pid)
                        ->whereIn('model_id', $lote)
                        ->delete();
                }
                $resumen['quitados'] += count($usuarios);
            }
            $resumen['agregados'] = count($aInsertar);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $resumen;
    }

    public function sincronizarUsuario(int $userId): array
    {
        return $this->sincronizarUsuarios([$userId]);
    }

    /**
     * Regla: cada usuario tiene UN solo perfil. Deja a la persona únicamente con `$roleId`
     * (quita cualquier otro perfil que tuviera) y recalcula sus permisos. Devuelve los nombres
     * de los perfiles que tenía antes y que se reemplazaron (vacío si ya tenía solo ese).
     *
     * @return string[]
     */
    public function asignarPerfilUnico(int $userId, int $roleId): array
    {
        $email = DB::table('users')->where('id', $userId)->value('email');

        $reemplazados = DB::transaction(function () use ($userId, $roleId, $email) {
            $otros = DB::table('actions')
                ->join('roles', 'roles.id', '=', 'actions.role_id')
                ->where('actions.user_id', $userId)
                ->where('actions.role_id', '!=', $roleId)
                ->pluck('roles.name')
                ->all();

            DB::table('actions')->where('user_id', $userId)->where('role_id', '!=', $roleId)->delete();

            if (!DB::table('actions')->where('user_id', $userId)->where('role_id', $roleId)->exists()) {
                DB::table('actions')->insert(['user_id' => $userId, 'user_email' => $email, 'role_id' => $roleId, 'created_at' => now(), 'updated_at' => now()]);
            }

            return $otros;
        });

        $this->sincronizarUsuario($userId);

        return $reemplazados;
    }
    /** Todos los usuarios que tienen este rol — se llama al guardar la Matriz de Permisos. */
    public function sincronizarRol(int $roleId): array
    {
        // Solo usuarios activos: se excluyen eliminados (soft-delete) y huérfanos, igual que en
        // sincronizarTodos().
        $ids = DB::table('actions')
            ->join('users', 'users.id', '=', 'actions.user_id')
            ->whereNull('users.deleted_at')
            ->where('actions.role_id', $roleId)
            ->distinct()
            ->pluck('actions.user_id')
            ->all();

        return $this->sincronizarUsuarios($ids);
    }

    /**
     * Todos los usuarios ACTIVOS con al menos un rol. Se excluyen los eliminados (soft-delete) y
     * los huérfanos (filas de actions/model_has_permissions de usuarios que ya no existen en
     * `users`, por borrados físicos antiguos): no inician sesión, y tocarlos no aporta nada.
     */
    public function sincronizarTodos(): array
    {
        $ids = DB::table('users')
            ->whereNull('deleted_at')
            ->whereIn('id', DB::table('actions')->select('user_id'))
            ->pluck('id')
            ->all();

        return $this->sincronizarUsuarios($ids);
    }
}
