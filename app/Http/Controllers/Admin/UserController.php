<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Action;
use App\Models\auditoria;
use App\Models\Permisos;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, "ADMINISTRACIÓN");
    }

    public function index()
    {
        //$users = User::latest()->take(5)->get();
        $users = User::paginate(4);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('actions.role', 'permissions');
        $acciones = $count = auditoria::where('usuario', $user->name)->count();
        $fecha = auditoria::where('usuario', $user->name)->orderBy('fechaRegistro', 'desc')->first();

        $permisosUsuario = collect(); // Colección para almacenar los permisos del usuario

        //dd($user->actions);
        foreach ($user->actions as $action) {
            $role = $action->role;
            if ($role) {
                $permisos = Permisos::where('role_id', $role->id)->get();
                $permisosUsuario = $permisosUsuario->merge($permisos);
            }
        }

        $permisosAsignados = \DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->pluck('permission_id')
            ->toArray();


        return view('admin.users.edit', compact('user', 'roles', 'acciones', 'fecha', 'permisosUsuario', 'permisosAsignados'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $users = User::paginate(4);
        $existingUser = User::where('email', $request['email'])->first();
        if ($existingUser) {
            return redirect()->route('admin.users.index', compact('users'))->with('error', 'El correo ' . $request['email'] . ' ya está registrado');
        }
        $user = User::create([
            'name' => strtoupper($request['name']),
            'email' => $request['email'],
            'password' => bcrypt($request['pass']),
        ]);
        $roles = $request->input('rol');
        foreach ($roles as $role) {
            Action::create([
                'user_email' => $user->email,
                'role_id' => $role,
            ]);
            $permisos_rol = $this->permisos_rol($role);
            foreach ($permisos_rol as $p) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $p->permission_id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ]);
            }
        }
        if (!$user) {
            return redirect()->route('admin.users.index', compact('users'))->with('error', 'No se pudo crear el usuario');
        }
        $emailuser = explode('@', $user->email);
        $accion = "add usuario app  " . $emailuser[0];
        $this->auditoria($accion);
        return redirect()->route('admin.users.index', compact('users'))->with('success', 'Usuario creado con éxito');
    }

    public function update(Request $request, User $user)
    {
        $update = [
            'name' => strtoupper($request->input('name'))
        ];
        if ($request->input('pass') != null) {
            $update['password'] = bcrypt($request->input('pass'));
        }
        $success = $user->update($update);
        if ($success) {
            $currentPermissions = $user->permissions()->pluck('id')->toArray();
            $permissions = $request->input('permissions', []);
            $permissionsToAdd = array_diff($permissions, $currentPermissions);
            $permissionsToRemove = array_diff($currentPermissions, $permissions);
            if (!empty($permissionsToAdd)) {
                $user->permissions()->attach($permissionsToAdd, [
                    'model_type' => 'App\Models\User'
                ]);
            }
            if (!empty($permissionsToRemove)) {
                $user->permissions()->detach($permissionsToRemove);
            }
            return redirect()->route('admin.users.edit', $user->id)->with('success', 'Usuario actualizado correctamente.');
        }
        return redirect()->route('admin.users.edit', $user->id)->with('error', 'No se pudo actualizar el usuario. Intente mas tarde.');
    }

    private function permisos_rol($role)
    {
        //$permisos = Permisos::where('role_id', $role)->get();
        $permisos = DB::table('role_has_permissions')->where('role_id', $role)->get();
        return $permisos;
    }

    public function storeRole(Request $request)
    {
        $role = Role::create([
            'name' => strtoupper($request->input('namerole')),
            'guard_name' => 'web'
        ]);
        if (!$role) {
            return redirect()->back()->with('error', 'No se pudo crear el rol');
        }
        return redirect()->back()->with('success', 'Rol creado con éxito');
    }

    public function getPermissionsByRole(Request $request)
    {
        $permissions = Permisos::where('role_id', $request->role_id)->get(['id', 'name']);
        $assignedPermissions = DB::table('role_has_permissions')
            ->where('role_id', $request->role_id)
            ->pluck('permission_id')->toArray();
        return response()->json([
            'permissions' => $permissions,
            'assigned_permissions' => $assignedPermissions,
        ]);
    }
    public function updatePermissionsRole(Request $request)
    {
        $role = Role::find($request->input('roleid'));
        $currentPermissions = $role->permissions()->pluck('id')->toArray();
        $permissions = $request->input('permissions', []);

        // Permisos a agregar: aquellos que están en la solicitud pero no están en los permisos actuales
        $permissionsToAdd = array_diff($permissions, $currentPermissions);

        // Permisos a eliminar: aquellos que están en los permisos actuales pero no están en la solicitud
        $permissionsToRemove = array_diff($currentPermissions, $permissions);

        // Agregar los permisos nuevos
        if (!empty($permissionsToAdd)) {
            $role->permissions()->attach($permissionsToAdd);
        }

        // Eliminar los permisos que ya no están seleccionados
        if (!empty($permissionsToRemove)) {
            $role->permissions()->detach($permissionsToRemove);
        }

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Permisos actualizados al rol correctamente.');
    }

    public function inventario($id)
    {
        return 'Inventario';
    }

}
