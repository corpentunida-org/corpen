<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Action;
use App\Models\auditoria;
use App\Models\Permisos;
use App\Models\Maestras\MaeTerceros;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\Admin\PermisosPorRolService;

class UserController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        // La columna de auditoría admite 255 caracteres: un texto más largo haría fallar toda la operación.
        $auditoriaController->create(mb_substr($accion, 0, 250), "ADMINISTRACIÓN");
    }

    /**
     * Antes solo mostraba empleados (type=null) — los 1.101 usuarios "ASOCIADO" (nacidos del
     * autorregistro del portal de Reservas) no tenían NINGUNA pantalla admin donde verse, ni
     * fecha de registro, ni forma de bloquearlos o eliminarlos. Un mismo listado con pestañas
     * cubre ambos grupos; "asociados" pagina porque son >1000 filas.
     */
    public function index(Request $request)
    {
        $tipo = $request->input('tipo') === 'asociados' ? 'asociados' : 'empleados';
        $busqueda = trim((string) $request->input('buscar', ''));

        $query = User::query()->when(
            $tipo === 'asociados',
            fn ($q) => $q->where('type', 'ASOCIADO'),
            fn ($q) => $q->whereNull('type'),
        );

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->where('name', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%")
                    ->orWhere('nid', 'like', "%{$busqueda}%");
            });
        }

        // Empleados: se muestra su perfil y área, se cargan de una vez para no consultar por fila.
        if ($tipo === 'empleados') {
            $query->with('actions.role');
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $totalEmpleados = User::whereNull('type')->count();
        $totalAsociados = User::where('type', 'ASOCIADO')->count();

        return view('admin.users.index', compact('users', 'tipo', 'busqueda', 'totalEmpleados', 'totalAsociados'));
    }

    public function edit(User $user)
    {
        // Antes se asumía que un asociado nunca tiene roles/permisos (es un perfil de
        // autoservicio del portal de Reservas, no un colaborador interno) y se le mostraba una
        // vista sin matriz de permisos. Resultó ser falso: hay asociados con permisos directos y
        // roles asignados de verdad (ej. gestión de Reservas) que quedaban invisibles e
        // imposibles de tocar desde aquí. La matriz de roles/permisos ahora se calcula y se
        // muestra igual para los dos tipos — solo cambia la plantilla (edit vs edit-asociado)
        // por los campos de identidad que sí son distintos.
        $roles = Role::all();
        $user->load('actions.role', 'permissions');
        // Por usuario_id, no por nombre: el nombre guardado en Auditoria es el que tenía el
        // usuario al momento de cada acción, así que alguien renombrado (ej. "admin" -> nombre
        // real) perdía sus registros viejos al filtrar por el nombre actual.
        $acciones = $count = auditoria::where('usuario_id', $user->id)->count();
        $fecha = auditoria::where('usuario_id', $user->id)->orderBy('fechaRegistro', 'desc')->first();

        // Permisos por rol: se combinan dos fuentes porque conviven dos esquemas de asignación.
        // El legado (columna permissions.role_id, un permiso "pertenece" a un solo rol) es el
        // único que consultaba este método antes. Los permisos asignados desde la Matriz de
        // Permisos (admin.roles.matriz) usan el pivote role_has_permissions en cambio, así que
        // un rol nuevo (ej. los de sgrh.vacacion.*) nunca aparecía aquí aunque sí estuviera
        // asignado al rol — Permisos::where('role_id', ...) nunca los encontraba. Se mezclan
        // ambas fuentes (sin duplicar) para no perder los permisos legado existentes.
        $permisosPorRol = collect();
        foreach ($user->actions as $action) {
            $role = $action->role;
            if ($role) {
                $permisosLegado = Permisos::where('role_id', $role->id)->get();
                $permisosPorRol->put($role->id, $permisosLegado->merge($role->permissions)->unique('id'));
            }
        }

        $permisosAsignados = \DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->pluck('permission_id')
            ->toArray();

        // Para "Copiar perfil": lista de otros usuarios del MISMO tipo de los que se puede
        // clonar acceso (un rol de empleado no tiene sentido copiado a un asociado y viceversa).
        $usuarios = User::where('type', $user->type)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Perfiles de cada usuario de referencia (una sola consulta): la pantalla avisa cuáles
        // se asignarían antes de copiar.
        $rolesPorUsuario = DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->whereIn('actions.user_id', $usuarios->pluck('id'))
            ->orderBy('roles.name')
            ->get(['actions.user_id', 'roles.id as role_id', 'roles.name'])
            ->groupBy('user_id')
            ->map(fn ($rs) => $rs->map(fn ($r) => ['id' => $r->role_id, 'name' => strtoupper($r->name)])->values());

        if ($user->type === 'ASOCIADO') {
            return view('admin.users.edit-asociado', compact('user', 'roles', 'permisosPorRol', 'permisosAsignados', 'usuarios', 'rolesPorUsuario'));
        }

        return view('admin.users.edit', compact('user', 'roles', 'acciones', 'fecha', 'permisosPorRol', 'permisosAsignados', 'usuarios', 'rolesPorUsuario'));
    }

    /**
     * "Copiar perfil": asigna al usuario editado el MISMO perfil que tiene un usuario de referencia
     * (ej. un compañero del área). Un usuario tiene un solo perfil, así que reemplaza el que
     * tuviera; los permisos y menús llegan solos desde el perfil.
     */
    public function copiarPermisos(Request $request, User $user)
    {
        $request->validate([
            'usuario_referencia_id' => 'required|exists:users,id',
        ]);

        // 'different:user' no sirve aquí: compararía contra un campo del request llamado
        // 'user', que no existe (el usuario editado es el modelo enlazado por ruta, no un
        // input) — la regla nunca se cumplía y dejaba pasar la autorreferencia sin bloquearla.
        // Se compara directo contra el modelo.
        if ((int) $request->usuario_referencia_id === $user->id) {
            return back()->with('error', 'No puedes copiar el acceso de un usuario hacia sí mismo.');
        }

        $origen = User::with('actions')->findOrFail($request->usuario_referencia_id);

        // Regla: un solo perfil por usuario. Solo se puede copiar de alguien que tenga
        // exactamente uno (los casos antiguos con varios se deben resolver a mano).
        $rolesOrigen = $origen->actions->pluck('role_id')->unique()->values();
        if ($rolesOrigen->count() !== 1) {
            return back()->with('error', $rolesOrigen->isEmpty()
                ? "{$origen->name} no tiene ningún perfil asignado, no hay nada que copiar."
                : "{$origen->name} tiene varios perfiles (caso antiguo) y cada usuario debe tener uno solo. Elige otro compañero o asigna el perfil manualmente.");
        }

        $roleId = (int) $rolesOrigen->first();
        $nombrePerfil = strtoupper(Role::find($roleId)->name);
        $reemplazados = app(PermisosPorRolService::class)->asignarPerfilUnico($user->id, $roleId);

        $this->auditoria("Se copió el perfil {$nombrePerfil} de " . \App\Http\Controllers\AuditoriaController::refUsuario($origen) . " al usuario " . \App\Http\Controllers\AuditoriaController::refUsuario($user)
            . ($reemplazados ? '; reemplazó: ' . implode(', ', $reemplazados) : ''));

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', "Se asignó el perfil {$nombrePerfil}, copiado de {$origen->name}."
                . ($reemplazados ? ' Reemplazó: ' . strtoupper(implode(', ', $reemplazados)) . '.' : '')
                . ' Sus permisos se aplicaron automáticamente.');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * AJAX: confirma en vivo, mientras el admin escribe la cédula en "Crear Usuario", si esa
     * cédula ya existe como tercero (y de quién es) — antes de llegar al submit.
     */
    public function buscarTerceroPorCedula(Request $request)
    {
        $cedula = trim((string) $request->query('cedula', ''));
        if ($cedula === '') {
            return response()->json(['encontrado' => false]);
        }

        $tercero = MaeTerceros::where('cod_ter', $cedula)->first(['cod_ter', 'nom_ter']);
        // Al editar, excluir_usuario_id es el propio usuario: si no se excluye, la cédula que ya
        // tiene ese mismo registro sale como "ya tiene usuario" al consultarla sobre sí misma.
        $yaTieneUsuario = User::where('nid', $cedula)
            ->when($request->filled('excluir_usuario_id'), fn ($q) => $q->where('id', '!=', $request->query('excluir_usuario_id')))
            ->exists();

        return response()->json([
            'encontrado' => (bool) $tercero,
            'nombre' => $tercero->nom_ter ?? null,
            'ya_tiene_usuario' => $yaTieneUsuario,
        ]);
    }

    /**
     * El nombre del usuario viene de Terceros, no de lo que se escriba en el formulario (evita
     * que el nombre de la cuenta y el de Terceros queden distintos). Se usa en store() y en
     * update(). Si el tercero existe pero no tiene nombre cargado (dato incompleto en Terceros),
     * cae al nombre que trae el formulario en vez de guardar un usuario sin nombre.
     */
    private function nombreDesdeTercero(string $cedula, ?string $nombreFormulario): string
    {
        $nombreTercero = MaeTerceros::where('cod_ter', $cedula)->value('nom_ter');

        return strtoupper(trim($nombreTercero ?: (string) $nombreFormulario));
    }

    public function store(Request $request)
    {
        $users = User::paginate(4);

        // La cédula queda obligatoria y ligada a Terceros: antes esta pantalla no pedía cédula
        // (así nacieron los 13+ empleados activos sin nid que encontramos esta sesión), y sin
        // 'exists:MaeTerceros' se podía crear una cuenta para una cédula inventada o mal
        // digitada, sin ningún tercero real detrás. unique:users,nid sin distinción de tipo:
        // dos cuentas (asociado o no) con la misma cédula es exactamente lo que causó las 73
        // cédulas duplicadas que se limpiaron este mismo mes.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'pass' => ['required', 'string', 'min:8'],
            'nid' => ['required', 'string', 'max:20', 'unique:users,nid', 'exists:MaeTerceros,cod_ter'],
        ], [
            'email.unique' => 'El correo ya está registrado.',
            'nid.unique' => 'Ya existe un usuario con esta cédula.',
            'nid.exists' => 'Esta cédula no está registrada en Terceros. Verifica el número, o créala primero en Maestras → Terceros.',
        ]);

        $user = User::create([
            // El nombre lo trae Terceros (nombreDesdeTercero), no lo que se haya escrito en el
            // campo: evita que el nombre de la cuenta quede distinto al de Terceros.
            'name' => $this->nombreDesdeTercero($request->input('nid'), $request->input('name')),
            'email' => $request->input('email'),
            'nid' => $request->input('nid'),
            'password' => bcrypt($request->input('pass')),
        ]);
        // Un solo perfil por usuario (si llegara una lista, se toma el primero).
        $rol = collect((array) $request->input('rol', []))->filter()->first();
        if ($rol && Role::whereKey($rol)->exists()) {
            app(PermisosPorRolService::class)->asignarPerfilUnico($user->id, (int) $rol);
        }
        if (!$user) {
            return redirect()->route('admin.users.index', compact('users'))->with('error', 'No se pudo crear el usuario');
        }
        $emailuser = explode('@', $user->email);
        $accion = "add usuario app " . \App\Http\Controllers\AuditoriaController::refUsuario($user);
        $this->auditoria($accion);
        return redirect()->route('admin.users.index', compact('users'))->with('success', 'Usuario creado con éxito');
    }

    public function update(Request $request, User $user)
    {
        if ($user->type === 'ASOCIADO') {
            return $this->updateAsociado($request, $user);
        }

        // Cédula obligatoria y ligada a Terceros, igual que al crear: así se puede completar la
        // de los empleados que quedaron sin ella (13 activos a la fecha), y el nombre pasa a
        // venir de Terceros en vez de lo que se escriba en el campo.
        $request->validate([
            'nid' => ['required', 'string', 'max:20', 'unique:users,nid,' . $user->id, 'exists:MaeTerceros,cod_ter'],
        ], [
            'nid.unique' => 'Ya existe un usuario con esta cédula.',
            'nid.exists' => 'Esta cédula no está registrada en Terceros. Verifica el número, o créala primero en Maestras → Terceros.',
        ]);

        $update = [];
        $nombreTercero = $this->nombreDesdeTercero($request->input('nid'), $request->input('name'));
        if ($nombreTercero !== $user->name) {
            $update['name'] = $nombreTercero;
        }
        if ($request->input('nid') !== $user->nid) {
            $update['nid'] = $request->input('nid');
        }
        // El campo de correo sí está en el formulario (Datos Personales) pero nunca se
        // procesaba aquí — se editaba en pantalla y se descartaba en silencio al guardar.
        if ($request->filled('email') && $request->input('email') !== $user->email) {
            $request->validate(['email' => ['email', 'max:255', 'unique:users,email,' . $user->id]]);
            $update['email'] = $request->input('email');
        }
        if ($request->input('telefono') != null && $request->input('telefono') !== $user->telefono) {
            $update['telefono'] = $request->input('telefono');
        }
        if ($request->input('pass') != null) {
            $update['password'] = bcrypt($request->input('pass'));
            // Solo tiene sentido junto con una contraseña nueva — si no se está cambiando la
            // clave en este guardado, la casilla no debería poder tocar la marca existente.
            $update['debe_cambiar_password'] = $request->boolean('forzar_cambio_password');
        }
        if(!empty($update)){
            $user->update($update);
            $this->auditoria('Se actualizó el usuario ' . \App\Http\Controllers\AuditoriaController::refUsuario($user));
        }

        $this->sincronizarRolesYPermisos($request, $user);

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Usuario actualizado correctamente.');
    }

    private function permisos_rol($role)
    {
        //$permisos = Permisos::where('role_id', $role)->get();
        $permisos = DB::table('role_has_permissions')->where('role_id', $role)->get();
        return $permisos;
    }

    /**
     * Compartido entre update() (empleado) y updateAsociado(). Los permisos ya NO se asignan por
     * persona: provienen únicamente de los roles (perfiles/grupos) que la persona tiene, y esos
     * roles se editan en la Matriz de Permisos. Aquí solo se puede VINCULAR un rol; cualquier
     * 'permissions[]' que llegue en el request se ignora a propósito (aunque alguien arme el POST
     * a mano). Después se recalcula model_has_permissions desde los roles (PermisosPorRolService).
     * 'rolnuevo' es opcional (el select no siempre trae una opción seleccionable si el usuario no
     * tenía ningún rol previo).
     */
    private function sincronizarRolesYPermisos(Request $request, User $user): void
    {
        // Un solo perfil por usuario: asignar uno REEMPLAZA el que tuviera.
        if ($request->filled('rolnuevo') && Role::whereKey($request->rolnuevo)->exists()) {
            $reemplazados = app(PermisosPorRolService::class)->asignarPerfilUnico($user->id, (int) $request->rolnuevo);
            $this->auditoria('Se asignó el perfil ' . strtoupper(Role::find($request->rolnuevo)->name) . ' al usuario ' . \App\Http\Controllers\AuditoriaController::refUsuario($user)
                . ($reemplazados ? '; reemplazó: ' . implode(', ', $reemplazados) : ''));
            return;
        }

        app(PermisosPorRolService::class)->sincronizarUsuario($user->id);
    }

    /**
     * Actualización del perfil de un ASOCIADO: los campos de identidad son más simples que los
     * de empleado (sin cargo/áreas/etc.), pero SÍ puede tener roles/permisos reales que
     * administrar (ver docblock de edit()) — por eso también llama a
     * sincronizarRolesYPermisos().
     */
    public function updateAsociado(Request $request, User $user)
    {
        abort_unless($user->type === 'ASOCIADO', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telefono' => ['nullable', 'string', 'max:14'],
            'pass' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $update = [
            'name' => strtoupper($validated['name']),
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
        ];

        if (!empty($validated['pass'])) {
            $update['password'] = bcrypt($validated['pass']);
            $update['debe_cambiar_password'] = $request->boolean('forzar_cambio_password');
        }

        $user->update($update);
        $this->auditoria('Se actualizó el usuario (asociado) ' . \App\Http\Controllers\AuditoriaController::refUsuario($user));

        $this->sincronizarRolesYPermisos($request, $user);

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Bloquear/desbloquear: impide o restaura el login sin borrar nada (ver
     * Fortify::authenticateUsing en FortifyServiceProvider, que es quien realmente hace cumplir
     * esta marca al iniciar sesión). Antes no existía ninguna forma de restringir acceso a un
     * usuario sin eliminarlo por completo.
     */
    public function bloquear(User $user)
    {
        $user->update(['bloqueado' => true]);
        $this->auditoria("Se bloqueó el acceso del usuario " . \App\Http\Controllers\AuditoriaController::refUsuario($user));

        return back()->with('success', "{$user->name} fue bloqueado. Ya no podrá iniciar sesión.");
    }

    public function desbloquear(User $user)
    {
        $user->update(['bloqueado' => false]);
        $this->auditoria("Se desbloqueó el acceso del usuario " . \App\Http\Controllers\AuditoriaController::refUsuario($user));

        return back()->with('success', "{$user->name} fue desbloqueado.");
    }

    /**
     * "Eliminar" es soft-delete (ver migración 2026_09_16_160000 y SoftDeletes en el modelo
     * User) — placeholder de destroy() nunca había existido, aunque la ruta ya estaba
     * registrada por Route::resource(): visitarla antes tiraba un error de método no definido.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($this->tieneHistorial($user)) {
            return back()->with('error', "{$user->name} tiene actividad registrada en el sistema (auditoría, gestiones, reservas u otros módulos) y no se puede eliminar por trazabilidad. Usa \"Bloquear\" en su lugar.");
        }

        $nombre = $user->name;
        $user->delete();
        $this->auditoria("Se eliminó el usuario " . \App\Http\Controllers\AuditoriaController::refUsuario($user));

        return redirect()->route('admin.users.index', ['tipo' => $user->type === 'ASOCIADO' ? 'asociados' : 'empleados'])
            ->with('success', "{$nombre} fue eliminado.");
    }

    public function show(Request $request)
    {
        $query = $request->input('query');
        $users = User::whereNull('type')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->paginate(5);
        return view('admin.users.index', compact('users'));
    }

    public function inventario($id)
    {
        return 'Inventario';
    }

    public function registerAsociado()
    {
        return view('auth.registerAsociado');
    }

    public function consumirEndpoint($nid)
    {
        $url = config('services.api_produccion.url') . "/api/Pastors"; // URL del endpoint
        $token = config('services.api_produccion.token');

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

    public function validarAsociado(Request $request)
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
            return redirect()->route('validar.asociado.form')->withErrors(['Los datos proporcionados no coinciden con nuestros registros. Por favor, comuníquese con soporte técnico.']);
            //eturn Redirect::back()->withErrors(['Los datos proporcionados no coinciden con nuestros registros. Por favor, comuníquese con nuestro soporte técnico para recibir asistencia.']);
        }
    }

    public function validarAsociadoCreate()
    {
        return view('auth.validarAsociado');
    }

    /**
     * Mismas fuentes de "actividad real" que ya usa InformeUsoController para saber quién usa la
     * app — reutilizadas aquí para decidir si un usuario puede eliminarse. Si tiene cualquier
     * rastro en alguna de estas tablas, eliminarlo (aunque sea soft-delete) rompería
     * trazabilidad real: gestiones de cartera, reservas hechas, tickets de soporte, tareas de
     * proyectos, etc. — se bloquea la acción y se sugiere bloquear el acceso en su lugar.
     */
    private function tieneHistorial(User $user): bool
    {
        $id = $user->id;

        return DB::table('Auditoria')->where('usuario_id', $id)->exists()
            || DB::table('sesiones_usuario')->where('user_id', $id)->exists()
            || DB::table('res_reservas')->where('user_id', $id)->exists()
            || DB::table('interactions')->where('agent_id', $id)->exists()
            || DB::table('car_comprobantes_pagos')->where('id_user', $id)->exists()
            || DB::table('car_sia_operaciones_logs')->where('id_user', $id)->exists()
            || DB::table('scp_soportes')->where('id_users', $id)->exists()
            || DB::table('scp_observaciones')->where('id_users', $id)->exists()
            || DB::table('wor_tasks')->where('user_id', $id)->exists()
            || DB::table('wor_task_comments')->where('user_id', $id)->exists()
            || DB::table('wor_task_histories')->where('cambiado_por', $id)->exists();
    }


}
