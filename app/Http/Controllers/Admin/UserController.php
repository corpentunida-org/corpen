<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Action;
use App\Models\auditoria;
use App\Models\Permisos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;



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
        $users = User::where('type','!=', 'ASOCIADO')->paginate(4);
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
                'user_id' => $user->id,
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

    public function inventario($id)
    {
        return 'Inventario';
    }

    public function registerAsociado ()
    {
        return view('auth.registerAsociado');
    }

    public function consumirEndpoint( $nid )
    {
        $url = "https://www.siasoftapp.com:7006/api/Pastors"; // URL del endpoint
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6ImFkbWluQGdtYWlsLmNvbSIsImp0aSI6IjNjNTZjOTU1LTZiNDktNDhlZi04NjVjLWQ1MzViOTNkMjllMCIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWUiOiJBZG1pbiIsIlVzZXJJZCI6IjEiLCJtYWlsIjoiYWRtaW5AZ21haWwuY29tIiwiVXNlcnJvbGUiOiJBZG1pbiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFkbWluIiwiZXhwIjoxNzQ2MTkzNzk1LCJpc3MiOiJteWFwcCIsImF1ZCI6Im15YXBwIn0.Cs5UU7RLmFQWsJg444rZTL2QtpRXS4cZEI_8jtzbUSw";

        // Realizar la solicitud GET
        $response = Http::withToken($token)
            ->get($url, ['DocumentId' => $nid]); // Agregar parámetros a la URL

        // Verificar si la llamada fue exitosa
        if ($response->successful()) {
            // Obtener JSON de respuesta
            $json = $response->json();
            return response()->json([
                'status' => 'success',
                'data' => $json, // JSON de respuesta
            ], 200);
        } else {
            // Manejar el error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al consumir el endpoint',
                'error' => $response->body(), // Mostrar el error recibido
                'code' => $response->status(),
            ], $response->status());
        }
    }

    public function validarAsociado( Request $request )
    {
        $nid = $request->input('nid'); // Obtiene el 'nid' del request
        $asociado = $this->consumirEndpoint($nid); // Consume el endpoint
        $birthdate = $request->input('fecha'); // Obtiene la fecha del request

        // Asegúrate de convertirlo a un array si es un objeto (por seguridad)
        $asociadoArray = is_array($asociado) ? $asociado : (array) $asociado;

        //print_r($asociadoArray);
        if ($asociadoArray['original']['status'] == "success") {
            $birthdate = $asociadoArray['original']['data']['birthdate'];
            $formattedBirthdate = Carbon::parse($birthdate)->format('Y-m-d');

            if ($formattedBirthdate != $request->input('fecha')) {
                return Redirect::back()->withErrors(['Los datos proporcionados no coinciden con nuestros registros. Por favor, comuníquese con nuestro soporte técnico para recibir asistencia.']);
            }

            return view('auth.registerAsociado', compact('nid', 'birthdate', 'asociadoArray'));

        } else {
            return Redirect::back()->withErrors(['Los datos proporcionados no coinciden con nuestros registros. Por favor, comuníquese con nuestro soporte técnico para recibir asistencia.']);
        }
    }

    public function validarAsociadoCreate( )
    {
        return view('auth.validarAsociado');
    }


}
