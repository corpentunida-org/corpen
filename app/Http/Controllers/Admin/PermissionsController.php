<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Action;
use App\Models\Permisos;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index()
    {
        
    }

    public function create(){
        return view('admin.permissions.create');
    }

    public function store(Request $request){
        $permiso = Permission::create([
            'name' => $request->permisoName,
            'guard_name' => 'web',
            'role_id' => $request->permisoRol,
        ]);
        if (!$permiso) {
            return redirect()->back()->with('error', 'No se pudo crear el permiso');
        }
        return redirect()->back()->with('success', 'Permiso creado con permiso');
    }


}