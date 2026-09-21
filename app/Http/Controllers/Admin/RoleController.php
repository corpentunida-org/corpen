<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Action;
use App\Models\Permisos;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Services\Admin\PermisosPorRolService;

class RoleController extends Controller
{   
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        // La columna de auditoría admite 255 caracteres: un texto más largo haría fallar toda la operación.
        $auditoriaController->create(mb_substr($accion, 0, 250), "ADMINISTRACIÓN");
    }

    public function index()
    {
        // La antigua pantalla de Gestión de Roles se unificó en la Matriz de Permisos (crear perfil,
        // crear permiso y asignar permisos, todo en un solo lugar). Se conserva la ruta por los enlaces y
        // permisos existentes (admin.roles.index).
        return redirect()->route('admin.roles.matriz');
    }

    /**
     * Pantalla nueva que reemplaza la limitación de la pantalla index(): ahí cada rol solo
     * mostraba los permisos "dueños" de ese rol (columna permissions.role_id). Aquí se listan
     * TODOS los permisos del sistema para TODOS los roles, agrupados por módulo (el prefijo
     * antes del primer punto, ej. "sgrh", "archivo"). Usa el mismo endpoint de guardado
     * (admin.roles.update) que ya existe — no se duplica lógica de asignación.
     */
    public function matriz()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        $permisosPorModulo = Permission::where("name", "!=", "")->orderBy('name')->get()
            ->groupBy(fn($permiso) => explode('.', $permiso->name)[0] ?? 'otros');

        // Áreas: tabla roles_areas (existen aunque no tengan perfiles) + la columna roles.area de cada perfil.
        $nombresAreas = DB::table('roles_areas')->pluck('nombre')
            ->merge($roles->map(fn ($r) => mb_strtolower($r->area ?: $r->name)))
            ->map(fn ($n) => mb_strtolower($n))->unique()->sort()->values();
        $idsAreas = DB::table('roles_areas')->pluck('id', 'nombre');
        $grupos = $nombresAreas->map(fn ($area) => [
            'area' => $area,
            'id' => $idsAreas[$area] ?? null,
            'roles' => $roles->filter(fn ($r) => mb_strtolower($r->area ?: $r->name) === $area)->values(),
        ]);
        $areas = $nombresAreas;
        $usuariosPorRol = DB::table('actions')->join('users', 'users.id', '=', 'actions.user_id')
            ->whereNull('users.deleted_at')->groupBy('actions.role_id')
            ->select('actions.role_id', DB::raw('count(distinct actions.user_id) as n'))->pluck('n', 'role_id');
        $indice = $roles->values()->pluck('id')->flip(); // id de perfil -> índice único (ids HTML)

        return view('admin.roles.matriz', compact('roles', 'permisosPorModulo', 'grupos', 'indice', 'areas', 'usuariosPorRol'));
    }

    public function guia()
    {
        return view('admin.roles.guia');
    }

    public function store(Request $request)
    {
        $request->merge([
            'namerole' => mb_strtolower(trim((string) $request->input('namerole'))),
            'area' => mb_strtolower(trim((string) $request->input('area'))),
        ]);
        $request->validate(
            ['namerole' => ['required', 'string', 'max:100', 'unique:roles,name'], 'area' => ['nullable', 'string', 'max:60']],
            [
                'namerole.required' => 'Escribe el nombre del perfil.',
                'namerole.unique' => 'Ya existe un perfil con ese nombre.',
                'namerole.max' => 'El nombre del perfil es demasiado largo (máximo 100 caracteres).',
            ]
        );

        // Sin área indicada, el perfil queda en su propia área (se puede cambiar luego en la Matriz).
        $area = $request->input('area') ?: $request->input('namerole');
        DB::table('roles_areas')->insertOrIgnore(['nombre' => $area, 'created_at' => now(), 'updated_at' => now()]);
        $role = Role::create([
            'name' => $request->input('namerole'),
            'area' => $area,
            'guard_name' => 'web',
        ]);
        $this->auditoria("Se creó el perfil (rol) " . $role->name);

        // El perfil nace sin permisos: se lleva a la Matriz con el perfil ya desplegado para
        // que se le asignen ahí.
        return redirect()
            ->route('admin.roles.matriz', ['rol' => $role->id])
            ->with('success', 'Perfil "' . strtoupper($role->name) . '" creado. Marca abajo los permisos que debe tener.');
    }

    /** Cambia el área a la que pertenece un perfil (solo agrupa en la Matriz; no cambia permisos). */
    public function actualizarArea(Request $request, Role $role)
    {
        $request->merge(['area' => mb_strtolower(trim((string) $request->input('area')))]);
        $request->validate(['area' => ['required', 'string', 'max:60']], ['area.required' => 'Escribe el área del perfil.', 'area.max' => 'El área es demasiado larga (máximo 60 caracteres).']);

        $anterior = $role->area ?: $role->name;
        DB::table('roles_areas')->insertOrIgnore(['nombre' => $request->input('area'), 'created_at' => now(), 'updated_at' => now()]);
        $role->update(['area' => $request->input('area')]);
        $this->auditoria("Perfil {$role->name}: área cambiada de {$anterior} a {$role->area}");

        return redirect()->route('admin.roles.matriz', ['rol' => $role->id])
            ->with('success', 'Área del perfil ' . strtoupper($role->name) . ' actualizada a ' . strtoupper($role->area) . '.');
    }

    /** Perfiles de los que dependen el código o el autorregistro: no se pueden eliminar. */
    private function perfilProtegido(Role $role): bool
    {
        return (int) $role->id === 13 || mb_strtolower($role->name) === 'asociado';
    }

    /** Usuarios ACTIVOS (no eliminados) que tienen este perfil asignado. */
    private function usuariosConPerfil(int $roleId)
    {
        return DB::table('actions')
            ->join('users', 'users.id', '=', 'actions.user_id')
            ->whereNull('users.deleted_at')
            ->where('actions.role_id', $roleId)
            ->select('users.id', 'users.name', 'users.nid', 'users.email')
            ->distinct()->get();
    }

    public function renombrar(Request $request, Role $role)
    {
        $request->merge(['nombre' => mb_strtolower(trim((string) $request->input('nombre')))]);
        $request->validate(
            ['nombre' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id]],
            ['nombre.required' => 'Escribe el nombre del perfil.', 'nombre.unique' => 'Ya existe un perfil con ese nombre.', 'nombre.max' => 'El nombre es demasiado largo (máximo 100 caracteres).']
        );

        $anterior = $role->name;
        if ($anterior === $request->input('nombre')) {
            return redirect()->route('admin.roles.matriz', ['rol' => $role->id]);
        }

        // Si el perfil estaba solo en un área con su mismo nombre, el área lo sigue.
        $areaPropia = mb_strtolower($role->area ?: $anterior) === mb_strtolower($anterior)
            && !DB::table('roles')->where('id', '!=', $role->id)->whereRaw('lower(coalesce(area, name)) = ?', [mb_strtolower($anterior)])->exists();

        $role->update(['name' => $request->input('nombre'), 'area' => $areaPropia ? $request->input('nombre') : $role->area]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->auditoria("Perfil renombrado de {$anterior} a {$role->name}");

        return redirect()->route('admin.roles.matriz', ['rol' => $role->id])
            ->with('success', 'Perfil renombrado: ' . strtoupper($anterior) . ' → ' . strtoupper($role->name) . '.');
    }

    public function eliminarPerfil(Role $role)
    {
        $volver = redirect()->route('admin.roles.matriz', ['rol' => $role->id]);

        if ($this->perfilProtegido($role)) {
            return $volver->with('error', 'El perfil ' . strtoupper($role->name) . ' lo usa el registro de asociados y no se puede eliminar.');
        }

        $usuarios = $this->usuariosConPerfil($role->id);
        if ($usuarios->isNotEmpty()) {
            $lista = $usuarios->take(5)->map(fn ($u) => \App\Http\Controllers\AuditoriaController::refUsuario(User::find($u->id)))->implode('; ');
            $mas = $usuarios->count() > 5 ? ' y ' . ($usuarios->count() - 5) . ' más' : '';
            return $volver->with('error', 'No se puede eliminar el perfil ' . strtoupper($role->name) . ': lo tienen asignado ' . $usuarios->count() . ' usuario(s) (' . $lista . $mas . '). Asígnales otro perfil primero.');
        }
        if (DB::table('model_has_roles')->where('role_id', $role->id)->exists()) {
            return $volver->with('error', 'No se puede eliminar el perfil ' . strtoupper($role->name) . ': aún está asignado a usuarios por el esquema de roles de Spatie.');
        }

        $nombre = $role->name;
        $permisos = DB::table('role_has_permissions')->where('role_id', $role->id)->count();
        DB::transaction(function () use ($role) {
            DB::table('actions')->where('role_id', $role->id)->delete();               // solo quedan filas de usuarios ya eliminados
            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
            DB::table('permissions')->where('role_id', $role->id)->update(['role_id' => null]); // dueño legado
            DB::table('roles')->where('id', $role->id)->delete();
        });
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->auditoria("Perfil eliminado: {$nombre} (tenía {$permisos} permiso(s) y ningún usuario asignado)");

        return redirect()->route('admin.roles.matriz')->with('success', 'Perfil ' . strtoupper($nombre) . ' eliminado.');
    }

    private function normalizarArea(Request $request, string $campo = 'area'): void
    {
        $request->merge([$campo => mb_strtolower(trim((string) $request->input($campo)))]);
    }

    public function crearArea(Request $request)
    {
        $this->normalizarArea($request);
        $request->validate(
            ['area' => ['required', 'string', 'max:60', 'unique:roles_areas,nombre']],
            ['area.required' => 'Escribe el nombre del área.', 'area.unique' => 'Ya existe un área con ese nombre.', 'area.max' => 'El nombre del área es demasiado largo (máximo 60 caracteres).']
        );
        DB::table('roles_areas')->insert(['nombre' => $request->area, 'created_at' => now(), 'updated_at' => now()]);
        $this->auditoria("Área creada: {$request->area}");

        return redirect()->route('admin.roles.matriz', ['area' => $request->area])
            ->with('success', 'Área ' . strtoupper($request->area) . ' creada. Agrégale perfiles desde su cabecera.');
    }

    public function renombrarArea(Request $request, int $id)
    {
        $actual = DB::table('roles_areas')->where('id', $id)->value('nombre');
        abort_unless($actual, 404);
        $this->normalizarArea($request);
        $request->validate(
            ['area' => ['required', 'string', 'max:60', 'unique:roles_areas,nombre,' . $id]],
            ['area.required' => 'Escribe el nombre del área.', 'area.unique' => 'Ya existe un área con ese nombre.', 'area.max' => 'El nombre del área es demasiado largo (máximo 60 caracteres).']
        );
        DB::transaction(function () use ($id, $actual, $request) {
            DB::table('roles_areas')->where('id', $id)->update(['nombre' => $request->area, 'updated_at' => now()]);
            DB::table('roles')->whereRaw('lower(coalesce(area, name)) = ?', [$actual])->update(['area' => $request->area]);
        });
        $this->auditoria("Área renombrada de {$actual} a {$request->area}");

        return redirect()->route('admin.roles.matriz', ['area' => $request->area])
            ->with('success', 'Área renombrada: ' . strtoupper($actual) . ' → ' . strtoupper($request->area) . '.');
    }

    public function eliminarArea(int $id)
    {
        $nombre = DB::table('roles_areas')->where('id', $id)->value('nombre');
        abort_unless($nombre, 404);
        $perfiles = DB::table('roles')->whereRaw('lower(coalesce(area, name)) = ?', [$nombre])->pluck('name');
        if ($perfiles->isNotEmpty()) {
            return redirect()->route('admin.roles.matriz', ['area' => $nombre])
                ->with('error', 'No se puede eliminar el área ' . strtoupper($nombre) . ': todavía tiene perfiles (' . strtoupper($perfiles->implode(', ')) . '). Muévelos a otra área o elimínalos primero.');
        }
        DB::table('roles_areas')->where('id', $id)->delete();
        $this->auditoria("Área eliminada: {$nombre}");

        return redirect()->route('admin.roles.matriz')->with('success', 'Área ' . strtoupper($nombre) . ' eliminada.');
    }

    public function destroy(Request $request, $idUser)
    {
        $user = User::find($idUser);
        $role = Role::find($request->rol);
        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }
        DB::table('actions')->where('user_id', $user->id)->where('role_id', $request->rol)->delete();
        // Los permisos del usuario provienen de sus roles: al quitar el rol se le quitan también
        // los permisos que solo ese rol le daba.
        app(PermisosPorRolService::class)->sincronizarUsuario($user->id);
        $this->auditoria("Se eliminó rol ". $role->name ." al usuario " . \App\Http\Controllers\AuditoriaController::refUsuario($user));
        return redirect()->back()->with('success', 'Rol eliminado correctamente');
    }

    public function update(Request $request, Role $role)
    {
        // Solo la Matriz de Permisos envía la lista COMPLETA de permisos del rol. La pantalla
        // legado (/roles) solo lista los permisos "propios" de cada rol y enviaba un subconjunto —
        // como este método quita todo lo que no venga en la lista, guardar desde allá borraba los
        // demás permisos del rol (y ahora, por la sincronización, también los de todos sus usuarios).
        if (!$request->boolean('desde_matriz')) {
            return redirect()->route('admin.roles.matriz')
                ->with('error', 'Los permisos de un rol se editan únicamente desde la Matriz de Permisos.');
        }

        $currentPermissions = $role->permissions()->pluck('id')->toArray();
        $permissions = $request->input('permissions', []);
        $permissionsToAdd = array_diff($permissions, $currentPermissions);
        $permissionsToRemove = array_diff($currentPermissions, $permissions);

        if (!empty($permissionsToAdd)) {
            $role->permissions()->attach($permissionsToAdd);
        }
        if (!empty($permissionsToRemove)) {
            $role->permissions()->detach($permissionsToRemove);
        }

        // attach()/detach() manipulan la tabla pivote directamente, sin pasar por los métodos
        // propios de Spatie (givePermissionTo/revokePermissionTo) — por eso NO invalidan la
        // caché de permisos de Spatie automáticamente. Sin esto, el cambio no se refleja en
        // can()/@can hasta que la caché expira sola (hasta 24h por defecto).
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Los permisos de cada usuario provienen de sus roles: se recalculan para TODOS los que
        // tienen este rol (ver PermisosPorRolService).
        $resumen = app(PermisosPorRolService::class)->sincronizarRol($role->id);
        $this->auditoria("Matriz: permisos del rol {$role->name} actualizados; {$resumen['usuarios']} usuario(s) sincronizados (+{$resumen['agregados']} / -{$resumen['quitados']})");

        return redirect()->back()->with('success', "Permisos actualizados al rol. Se aplicaron a {$resumen['usuarios']} usuario(s) con este rol.");
    }
}
