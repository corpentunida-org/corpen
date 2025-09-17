<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Action;
use App\Models\Permisos;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions','permissionsRole'])->get();
        return view('admin.roles.index',compact('roles'));
    }

    public function store(Request $request){
        $role = Role::create([
            'name' => $request->input('namerole'),
            'guard_name' => 'web'
        ]);
        if (!$role) {
            return redirect()->back()->with('error', 'No se pudo crear el rol');
        }
        return redirect()->back()->with('success', 'Rol creado con éxito');
    }

    public function destroy(Request $request, $idUser){
        
    }

    public function update(Request $request, Role $role){
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
