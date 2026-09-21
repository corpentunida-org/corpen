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

        // Áreas: un perfil cuyo nombre empieza por el de otro más corto pertenece a esa área
        // (asociadosadmon -> asociado, carteraadmon -> cartera). Los demás quedan solos.
        $nombres = $roles->map(fn ($r) => mb_strtolower($r->name));
        $areaDe = fn (string $n) => $nombres->filter(fn ($b) => mb_strlen($b) >= 4 && str_starts_with($n, $b))
            ->sortBy(fn ($b) => mb_strlen($b))->first() ?? $n;
        $grupos = $roles->groupBy(fn ($r) => $areaDe(mb_strtolower($r->name)))
            ->map(fn ($rs, $area) => ['area' => $area, 'roles' => $rs->values()])
            ->sortKeys()->values();
        $indice = $roles->values()->pluck('id')->flip(); // id de perfil -> índice único (ids HTML)

        return view('admin.roles.matriz', compact('roles', 'permisosPorModulo', 'grupos', 'indice'));
    }

    public function guia()
    {
        return view('admin.roles.guia');
    }

    public function store(Request $request)
    {
        $request->merge(['namerole' => mb_strtolower(trim((string) $request->input('namerole')))]);
        $request->validate(
            ['namerole' => ['required', 'string', 'max:100', 'unique:roles,name']],
            [
                'namerole.required' => 'Escribe el nombre del perfil.',
                'namerole.unique' => 'Ya existe un perfil con ese nombre.',
                'namerole.max' => 'El nombre del perfil es demasiado largo (máximo 100 caracteres).',
            ]
        );

        $role = Role::create(['name' => $request->input('namerole'), 'guard_name' => 'web']);
        $this->auditoria("Se creó el perfil (rol) " . $role->name);

        // El perfil nace sin permisos: se lleva a la Matriz con el perfil ya desplegado para
        // que se le asignen ahí.
        return redirect()
            ->route('admin.roles.matriz', ['rol' => $role->id])
            ->with('success', 'Perfil "' . strtoupper($role->name) . '" creado. Marca abajo los permisos que debe tener.');
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
