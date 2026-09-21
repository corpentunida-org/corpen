<?php

namespace App\Console\Commands;

use App\Services\Admin\PermisosPorRolService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;

/**
 * Revisa que perfiles y permisos sigan la estructura (ver la guía: /guia-permisos). Se corre
 * antes y después de cada despliegue que toque permisos. Solo lee, salvo por las opciones
 * --crear-menus (crea los permisos menu.* que falten) y --sincronizar (recalcula los permisos
 * de las personas a partir de sus perfiles).
 */
class AuditarPermisos extends Command
{
    protected $signature = 'permisos:auditar {--crear-menus : Crea el permiso menu.<modulo> de los archivos de menú que no lo tengan} {--sincronizar : Recalcula los permisos de los usuarios desde sus perfiles}';

    protected $description = 'Verifica la estructura de perfiles y permisos (un perfil por usuario, permisos en perfiles, menús con permiso)';

    public function handle(): int
    {
        $problemas = 0;

        // 1. Módulos de menú sin permiso menu.<modulo>
        $modulos = collect(File::files(resource_path('views/layouts/actions')))
            ->map(fn ($f) => str_replace('.blade.php', '', $f->getFilename()))->sort()->values();
        $existentes = DB::table('permissions')->where('name', 'like', 'menu.%')->pluck('name')->all();
        $sinPermiso = $modulos->filter(fn ($m) => !in_array('menu.' . $m, $existentes))->values();
        if ($sinPermiso->isNotEmpty()) {
            if ($this->option('crear-menus')) {
                foreach ($sinPermiso as $m) {
                    DB::table('permissions')->insert(['name' => 'menu.' . $m, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
                    $this->info("Creado permiso menu.{$m} (asígnalo a los perfiles en la Matriz).");
                }
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            } else {
                $problemas += $sinPermiso->count();
                $this->error('Módulos de menú SIN permiso menu.*: ' . $sinPermiso->implode(', ') . '  (corre con --crear-menus)');
            }
        }

        // 2. Permisos menu.* sin archivo de menú
        $huerfanos = collect($existentes)->map(fn ($n) => substr($n, 5))->reject(fn ($m) => $modulos->contains($m));
        if ($huerfanos->isNotEmpty()) {
            $problemas += $huerfanos->count();
            $this->warn('Permisos menu.* sin archivo de menú: ' . $huerfanos->implode(', '));
        }

        // 3. Permisos que no están en ningún perfil
        $sinPerfil = DB::table('permissions')->where('name', '!=', '')
            ->whereNotIn('id', DB::table('role_has_permissions')->select('permission_id'))->pluck('name');
        if ($sinPerfil->isNotEmpty()) {
            $problemas += $sinPerfil->count();
            $this->warn("Permisos que NO están en ningún perfil ({$sinPerfil->count()}): " . $sinPerfil->implode(', '));
        }

        // 4. Nombres que no siguen modulo.recurso.accion (los antiguos no se tocan; solo se avisa)
        $malNombrados = DB::table('permissions')->where('name', '!=', '')->pluck('name')
            ->reject(fn ($n) => str_starts_with($n, 'menu.') || preg_match('/^[a-z][a-z0-9_]*(\.[a-z0-9_]+){1,3}$/', $n));
        if ($malNombrados->isNotEmpty()) {
            $this->line('<comment>Nombres fuera del formato modulo.recurso.accion (antiguos, revisar):</comment> ' . $malNombrados->implode(', '));
        }

        // 5. Perfiles sin permisos
        $vacios = DB::table('roles')->whereNotIn('id', DB::table('role_has_permissions')->select('role_id'))->pluck('name');
        if ($vacios->isNotEmpty()) {
            $this->line('<comment>Perfiles sin ningún permiso:</comment> ' . $vacios->implode(', '));
        }

        // 6. Usuarios activos con más de un perfil (regla: uno solo)
        $multi = DB::table('actions')
            ->join('users', 'users.id', '=', 'actions.user_id')->whereNull('users.deleted_at')
            ->groupBy('users.id', 'users.name')->havingRaw('count(distinct actions.role_id) > 1')
            ->select('users.id', 'users.name', DB::raw('count(distinct actions.role_id) as n'))->get();
        if ($multi->isNotEmpty()) {
            $problemas += $multi->count();
            $this->warn("Usuarios con MÁS de un perfil ({$multi->count()}), la regla es uno solo:");
            foreach ($multi as $u) {
                $this->line("   #{$u->id} {$u->name} ({$u->n} perfiles)");
            }
        }

        // 7. Coherencia: permisos de cada usuario == unión de los de sus perfiles
        if ($this->option('sincronizar')) {
            $r = app(PermisosPorRolService::class)->sincronizarTodos();
            $this->info("Sincronizado: {$r['usuarios']} usuario(s), +{$r['agregados']} / -{$r['quitados']}.");
        }
        $sinc = app(PermisosPorRolService::class);
        $ids = DB::table('users')->whereNull('deleted_at')->whereIn('id', DB::table('actions')->select('user_id'))->pluck('id')->all();
        $desfasados = $this->desfasados($ids);
        if ($desfasados > 0) {
            $problemas += $desfasados;
            $this->error("Usuarios cuyos permisos NO coinciden con los de su perfil: {$desfasados}  (corre con --sincronizar)");
        }

        $problemas === 0
            ? $this->info('OK: perfiles y permisos siguen la estructura.')
            : $this->error("Hay {$problemas} punto(s) por resolver.");

        return $problemas === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function desfasados(array $ids): int
    {
        $porRol = [];
        foreach (DB::table('role_has_permissions')->get(['role_id', 'permission_id']) as $r) {
            $porRol[$r->role_id][$r->permission_id] = true;
        }
        $roles = [];
        foreach (DB::table('actions')->whereIn('user_id', $ids)->get(['user_id', 'role_id']) as $a) {
            $roles[$a->user_id][] = $a->role_id;
        }
        $reales = [];
        foreach (DB::table('model_has_permissions')->where('model_type', 'App\Models\User')->whereIn('model_id', $ids)->get(['model_id', 'permission_id']) as $d) {
            $reales[$d->model_id][$d->permission_id] = true;
        }
        $n = 0;
        foreach ($ids as $uid) {
            $esperado = [];
            foreach ($roles[$uid] ?? [] as $rid) {
                foreach ($porRol[$rid] ?? [] as $pid => $_) {
                    $esperado[$pid] = true;
                }
            }
            if (array_diff_key($esperado, $reales[$uid] ?? []) || array_diff_key($reales[$uid] ?? [], $esperado)) {
                $n++;
            }
        }
        return $n;
    }
}
