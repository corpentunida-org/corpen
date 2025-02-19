<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Action;
use App\Models\auditoria;
use App\Models\Permisos;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index() {
        $users = User::latest()->take(5)->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user) {
        $roles = Role::all();
        $user->load('actions.role', 'permissions');
        $acciones = $count = auditoria::where('usuario', $user->name)->count();
        $fecha = auditoria::where('usuario', $user->name)->orderBy('fechaRegistro', 'desc') ->first();

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

    public function create() {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request) {
        $user = User::create([
            'name' => strtoupper($request['name']),
            'email' => $request['email'],
            'password' => bcrypt($request['pass']),
        ]);

        $rol = Action::create([
            'user_email' => $user->email,
            'role_id' => $request['rol'],
        ]);
        $users = User::latest()->take(5)->get();
        if (!$user || !$rol ) {
           return redirect()->route('admin.users.index', compact('users'))->with('error', 'No se pudo crear el usuario');
        }
        return redirect()->route('admin.users.index', compact('users'))->with('success', 'Usuario creado con éxito');
    }


    public function update(Request $request, User $user){
        $user->roles()->sync($request->roles);
        //return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignó el rol correctamente');
    }

    public function inventario( $id )
    {
        return 'Inventario';
    }

}
