<?php

namespace App\Http\Controllers;

use App\Models\auditoria;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    // Cuenta de sistemas/desarrollo (Miguel Torres): concentra picos de miles de registros por
    // cargas masivas/correcciones de datos en un solo día, no trabajo operativo de un
    // funcionario. Se marca aparte en el filtro para no confundirla con actividad de negocio.
    private const USUARIO_ID_SISTEMAS = 4;

    /**
     * Cómo nombrar a una persona en el texto de la auditoría: por CÉDULA, nunca por el id interno
     * de la tabla (que no significa nada para quien lee el registro). Sin cédula cae al correo.
     */
    public static function refUsuario($usuarioOId): string
    {
        $u = $usuarioOId instanceof \App\Models\User ? $usuarioOId : \App\Models\User::withTrashed()->find($usuarioOId);
        if (!$u) {
            return 'usuario sin registro';
        }
        return trim($u->name) . ' (' . ($u->nid ? 'C.C. ' . $u->nid : $u->email) . ')';
    }

    /**
     * Tercero (titular, beneficiario, asegurado, cliente…) por nombre y cédula: "NOMBRE (C.C. 123)".
     * El nombre sale de MaeTerceros; si no está allí se usa `$nombre` (el que trae el formulario) y,
     * si tampoco hay, solo "C.C. 123".
     */
    public static function refTercero($cedula, ?string $nombre = null): string
    {
        $cedula = trim((string) $cedula);
        if ($cedula === '') {
            return 'tercero sin cédula';
        }
        $nombreTercero = \Illuminate\Support\Facades\DB::table('MaeTerceros')->where('cod_ter', $cedula)->value('nom_ter');
        $nombre = trim((string) ($nombreTercero ?: $nombre));
        return ($nombre !== '' ? mb_strtoupper($nombre) . ' ' : '') . '(C.C. ' . $cedula . ')';
    }

    /** Colaborador de Recursos Humanos (Sgrh): su cédula es el cod_ter del empleado. */
    public static function refColaborador($empleadoOId): string
    {
        $codTer = $empleadoOId instanceof \App\Models\Sgrh\Empleado
            ? $empleadoOId->cod_ter
            : \Illuminate\Support\Facades\DB::table('sgrh_empleados')->where('id', $empleadoOId)->value('cod_ter');
        return $codTer ? 'C.C. ' . $codTer : 'colaborador sin cédula';
    }
    public function create($action,$area){
        $now = Carbon::now();
        auditoria::create([
            'fechaRegistro' => $now->toDateString(),
            'horaRegistro' => $now->toTimeString(),
            'usuario'=> Auth::user()->name,
            'usuario_id'=> Auth::user()->id,
            'accion'=> $action,
            'area'=> $area
        ]);
    }

    public function index(Request $request){
        $registros = auditoria::query()
            ->when($request->filled('usuario_id'), fn ($q) => $q->where('usuario_id', $request->input('usuario_id')))
            ->when($request->filled('area'), fn ($q) => $q->where('area', $request->input('area')))
            ->when($request->filled('fecha_desde'), fn ($q) => $q->whereDate('fechaRegistro', '>=', $request->input('fecha_desde')))
            ->when($request->filled('fecha_hasta'), fn ($q) => $q->whereDate('fechaRegistro', '<=', $request->input('fecha_hasta')))
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->withQueryString();

        // Nombre + id, tomados de la propia tabla de auditoría (no de la tabla de usuarios) para
        // que el filtro liste exactamente a quienes tienen actividad registrada. Se toma el
        // nombre de su registro más reciente (no un DISTINCT usuario_id+usuario) porque algunos
        // usuarios cambiaron de nombre en el tiempo (ej. usuario_id=4 pasó de "admin" a "MIGUEL
        // TORRES") y eso duplicaría la misma persona en el filtro.
        $ultimoRegistroPorUsuario = auditoria::selectRaw('MAX(id) as id')->groupBy('usuario_id');
        $usuarios = auditoria::select('usuario_id', 'usuario')
            ->whereIn('id', $ultimoRegistroPorUsuario)
            ->get()
            ->map(function ($u) {
                $u->label = $u->usuario_id == self::USUARIO_ID_SISTEMAS
                    ? strtoupper($u->usuario) . ' (Sistemas / Desarrollo)'
                    : strtoupper($u->usuario);
                return $u;
            })
            ->sortBy('label');
        $areas = auditoria::select('area')->distinct()->orderBy('area')->pluck('area');

        // Cédula de quien hizo cada acción (una sola consulta para la página).
        $cedulas = \App\Models\User::withTrashed()->whereIn('id', $registros->pluck('usuario_id')->unique())->pluck('nid', 'id');

        return view('admin.users.usuarios', compact('registros', 'usuarios', 'areas', 'cedulas'));
    }
}
