<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Action;
use App\Models\Permisos;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use App\Services\Admin\PermisosPorRolService;
use App\Http\Controllers\AuditoriaController;

class PermissionsController extends Controller
{
    public function index()
    {
        
    }

    public function create(){
        return view('admin.permissions.create');
    }

    /**
     * Estructura obligatoria (ver la guía: admin.guia.permisos):
     *  - nombre `modulo.recurso.accion` en minúsculas (ej. cartera.morosos.generarcarta);
     *  - un permiso solo sirve cuando pertenece a un PERFIL: se asigna a uno aquí mismo y queda
     *    ligado en la Matriz de Permisos (role_has_permissions), y de ahí les llega a las personas;
     *  - los permisos `menu.*` no se crean a mano: nacen del archivo del menú del módulo.
     */
    public function store(Request $request)
    {
        $request->merge(['permisoName' => mb_strtolower(trim((string) $request->permisoName))]);
        $request->validate([
            'permisoName' => [
                'required', 'max:100', 'unique:permissions,name',
                'regex:/^[a-z][a-z0-9_]*(\.[a-z0-9_]+){1,3}$/',
                fn ($attr, $value, $fail) => str_starts_with($value, 'menu.')
                    ? $fail('Los permisos "menu." no se crean a mano: se generan al agregar el archivo del menú del módulo (ver la guía).')
                    : null,
            ],
            'permisoRol' => 'required|exists:roles,id',
        ], [
            'permisoName.regex' => 'Usa el formato modulo.recurso.accion en minúsculas, sin espacios ni tildes (ej. cartera.morosos.generarcarta).',
            'permisoName.unique' => 'Ya existe un permiso con ese nombre.',
            'permisoRol.required' => 'Elige el perfil al que pertenece el permiso.',
        ]);

        $permiso = Permission::create([
            'name' => $request->permisoName,
            'guard_name' => 'web',
            'role_id' => $request->permisoRol, // columna legado: perfil "dueño" del permiso
        ]);

        // Queda asignado al perfil elegido en la Matriz y se aplica a las personas que lo tienen.
        DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permiso->id, 'role_id' => $request->permisoRol]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $resumen = app(PermisosPorRolService::class)->sincronizarRol((int) $request->permisoRol);
        $nombrePerfil = strtoupper((string) DB::table('roles')->where('id', $request->permisoRol)->value('name'));
        app(AuditoriaController::class)->create(mb_substr("Se creó el permiso {$permiso->name} y se asignó al perfil {$nombrePerfil} ({$resumen['usuarios']} usuario(s))", 0, 250), 'ADMINISTRACIÓN');

        return redirect()
            ->route('admin.roles.matriz', ['rol' => $request->permisoRol])
            ->with('success', "Permiso {$permiso->name} creado y asignado al perfil. Siguiente paso: en la Matriz márcalo también en los demás perfiles que lo necesiten, y úsalo en el código (candirect / @candirect). Guía: " . route('admin.guia.permisos'));
    }

}