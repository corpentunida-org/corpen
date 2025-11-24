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
        return redirect()->back()->with('success', 'Permisos actualizados al rol correctamente.');
    }
}
