<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user) {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));;
    }

    public function create() {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function update(Request $request, User $user){
        $user->roles()->sync($request->roles); //sync añadir nuevos registros a la tabla intermedia
        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignó el rol correctamente');
    }
}