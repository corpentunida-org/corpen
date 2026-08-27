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

class RoleController extends Controller
{   
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "ADMINISTRACIÓN");
    }

    public function index()
    {
        $roles = Role::with(['permissions', 'permissionsRole'])->get();
        return view('admin.roles.index', compact('roles'));
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

        $permisosPorModulo = Permission::orderBy('name')->get()
            ->groupBy(fn($permiso) => explode('.', $permiso->name)[0] ?? 'otros');

        return view('admin.roles.matriz', compact('roles', 'permisosPorModulo'));
    }

    public function store(Request $request)
    {
        $role = Role::create([
            'name' => strtolower($request->input('namerole')),
            'guard_name' => 'web',
        ]);
        if (!$role) {
            return redirect()->back()->with('error', 'No se pudo crear el rol');
        }
        return redirect()->back()->with('success', 'Rol creado con éxito');
    }

    public function destroy(Request $request, $idUser)
    {
        $user = User::find($idUser);
        $role = Role::find($request->rol);
        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }
        DB::table('actions')->where('user_id', $user->id)->where('role_id', $request->rol)->delete();
        $this->auditoria("Se eliminó rol ". $role->name ." al usuario " . $user->email);
        return redirect()->back()->with('success', 'Rol eliminado correctamente');
    }

    public function update(Request $request, Role $role)
    {
        //$role = Role::find($request->input('roleid'));
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

        return redirect()->back()->with('success', 'Permisos actualizados al rol correctamente.');
    }
}
