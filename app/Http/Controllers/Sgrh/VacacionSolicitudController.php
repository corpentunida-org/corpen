<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sgrh\StoreVacacionSolicitudRequest;
use App\Mail\VacacionSolicitudCreadaMail;
use App\Mail\VacacionSolicitudResueltaMail;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\VacacionColectiva;
use App\Models\Sgrh\VacacionPolitica;
use App\Models\Sgrh\VacacionSolicitud;
use App\Services\Sgrh\VacacionAprobadorResolver;
use App\Services\Sgrh\VacacionSaldoCalculador;
use App\Services\Sgrh\VacacionValidador;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VacacionSolicitudController extends Controller
{
    public function __construct(
        private VacacionAprobadorResolver $resolver,
        private VacacionValidador $validador,
        private VacacionSaldoCalculador $calculador,
    ) {
    }

    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    /**
     * Empleado de Sgrh del usuario logueado — requerido para el autoservicio (solicitar, ver
     * saldo propio). Si no hay coincidencia de correo, no puede autoservirse.
     */
    private function empleadoActual(): ?Empleado
    {
        return $this->resolver->empleadoDeUsuario(Auth::user());
    }

    public function create()
    {
        $empleado = $this->empleadoActual();

        if (!$empleado) {
            return back()->with('error', 'Tu cuenta no está vinculada a ningún colaborador de RR. HH. (el correo de tu usuario no coincide con ningún correo corporativo registrado). Contacta a RRHH.');
        }

        $politica = VacacionPolitica::vigente();

        return view('sgrh.vacacion.solicitud.create', [
            'empleado' => $empleado,
            'saldo' => $this->calculador->saldoActual($empleado, $politica),
            'politica' => $politica,
        ]);
    }

    public function store(StoreVacacionSolicitudRequest $request)
    {
        $validated = $request->validated();

        // Un colaborador normal solo solicita para sí mismo; solo con el permiso de respaldo de
        // RRHH se puede armar una solicitud a nombre de otro colaborador (ej. una adelantada que
        // RRHH misma autoriza).
        $puedeActuarComoRrhh = Auth::user()->can('sgrh.vacacion.solicitud.rrhh');
        $empleado = ($puedeActuarComoRrhh && !empty($validated['empleado_id']))
            ? Empleado::findOrFail($validated['empleado_id'])
            : $this->empleadoActual();

        if (!$empleado) {
            return back()->withInput()->with('error', 'Tu cuenta no está vinculada a ningún colaborador de RR. HH. Contacta a RRHH.');
        }

        $fechaInicio = Carbon::parse($validated['fecha_inicio']);
        $fechaFin = Carbon::parse($validated['fecha_fin']);
        $diasHabiles = $this->validador->calcularDiasHabiles($fechaInicio, $fechaFin);
        $esAdelantada = $puedeActuarComoRrhh && $request->boolean('es_adelantada');
        $autorizadaPorRrhhId = $esAdelantada ? Auth::id() : null;

        $this->validador->validar($empleado, $fechaInicio, $fechaFin, $diasHabiles, $esAdelantada, $autorizadaPorRrhhId);

        $solicitud = VacacionSolicitud::create([
            'empleado_id' => $empleado->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'dias_habiles' => (string) $diasHabiles,
            'tipo' => 'individual',
            'estado' => 'pendiente',
            'es_adelantada' => $esAdelantada,
            'autorizada_por_rrhh_id' => $autorizadaPorRrhhId,
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        $this->auditoria("Solicitud de vacaciones #{$solicitud->id} creada por colaborador #{$empleado->id} ({$fechaInicio->format('d/m/Y')} a {$fechaFin->format('d/m/Y')})");

        $this->notificarNuevaSolicitud($solicitud);

        return redirect()->route('sgrh.vacacion.solicitud.mis')->with('success', 'Solicitud de vacaciones enviada correctamente.');
    }

    public function mis()
    {
        $empleado = $this->empleadoActual();

        if (!$empleado) {
            return back()->with('error', 'Tu cuenta no está vinculada a ningún colaborador de RR. HH.');
        }

        $solicitudes = $empleado->vacacionSolicitudes()->paginate(20);

        return view('sgrh.vacacion.solicitud.mis', compact('solicitudes', 'empleado'));
    }

    /**
     * Solicitudes bajo la responsabilidad del usuario: su equipo directo (jefe), extendido
     * (director), o todas si tiene visión global de RRHH — ver
     * VacacionAprobadorResolver::solicitudesVisiblesPara().
     */
    public function aprobaciones(Request $request)
    {
        $query = $this->resolver->solicitudesVisiblesPara(Auth::user())->with('empleado.tercero');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            $query->pendientes();
        }

        $solicitudes = $query->latest('fecha_inicio')->paginate(20)->appends($request->query());

        return view('sgrh.vacacion.solicitud.aprobaciones', compact('solicitudes'));
    }

    public function show(VacacionSolicitud $solicitud)
    {
        $solicitud->load('empleado.tercero', 'empleado.contratoActivo.cargo', 'aprobador', 'autorizadaPorRrhh', 'vacacionColectiva');

        $politica = VacacionPolitica::vigente();

        return view('sgrh.vacacion.solicitud.show', [
            'solicitud' => $solicitud,
            'puedeResolver' => $solicitud->estado === 'pendiente' && $this->resolver->puedeResolver(Auth::user(), $solicitud),
            'saldoActual' => $this->calculador->saldoActual($solicitud->empleado, $politica),
        ]);
    }

    /**
     * Actualiza con `where('estado', 'pendiente')` para blindar la condición de carrera entre
     * dos aprobadores simultáneos (ej. jefe y director resolviendo a la vez): si otro ya la
     * resolvió, `update()` afecta 0 filas y se informa en vez de duplicar la resolución.
     */
    public function aprobar(VacacionSolicitud $solicitud)
    {
        if (!$this->resolver->puedeResolver(Auth::user(), $solicitud)) {
            abort(403, 'No tienes autorización para resolver esta solicitud.');
        }

        $rol = $this->rolDeResolucion(Auth::user(), $solicitud);

        $actualizadas = VacacionSolicitud::where('id', $solicitud->id)->where('estado', 'pendiente')->update([
            'estado' => 'aprobada',
            'aprobador_user_id' => Auth::id(),
            'rol_aprobador' => $rol,
            'fecha_resolucion' => now(),
        ]);

        if ($actualizadas === 0) {
            return back()->with('error', 'Esta solicitud ya había sido resuelta por otra persona.');
        }

        $this->auditoria("Solicitud de vacaciones #{$solicitud->id} aprobada por usuario #" . Auth::id() . " como {$rol}");

        $this->notificarResolucion($solicitud->fresh());

        return back()->with('success', 'Solicitud aprobada correctamente.');
    }

    public function rechazar(Request $request, VacacionSolicitud $solicitud)
    {
        if (!$this->resolver->puedeResolver(Auth::user(), $solicitud)) {
            abort(403, 'No tienes autorización para resolver esta solicitud.');
        }

        $validated = $request->validate(['motivo_rechazo' => 'required|string|max:500']);
        $rol = $this->rolDeResolucion(Auth::user(), $solicitud);

        $actualizadas = VacacionSolicitud::where('id', $solicitud->id)->where('estado', 'pendiente')->update([
            'estado' => 'rechazada',
            'aprobador_user_id' => Auth::id(),
            'rol_aprobador' => $rol,
            'fecha_resolucion' => now(),
            'motivo_rechazo' => $validated['motivo_rechazo'],
        ]);

        if ($actualizadas === 0) {
            return back()->with('error', 'Esta solicitud ya había sido resuelta por otra persona.');
        }

        $this->auditoria("Solicitud de vacaciones #{$solicitud->id} rechazada por usuario #" . Auth::id() . " como {$rol}: {$validated['motivo_rechazo']}");

        $this->notificarResolucion($solicitud->fresh());

        return back()->with('success', 'Solicitud rechazada.');
    }

    /**
     * El propio colaborador cancela una solicitud suya que aún no ha pasado — libera el saldo
     * si estaba aprobada (VacacionSaldoCalculador::diasTomados() solo cuenta 'aprobada').
     */
    public function cancelar(VacacionSolicitud $solicitud)
    {
        $empleado = $this->empleadoActual();

        if (!$empleado || $solicitud->empleado_id !== $empleado->id) {
            abort(403);
        }

        if (!in_array($solicitud->estado, ['pendiente', 'aprobada'], true) || $solicitud->fecha_inicio->isPast()) {
            return back()->with('error', 'Esta solicitud ya no se puede cancelar.');
        }

        $solicitud->update(['estado' => 'cancelada']);

        $this->auditoria("Solicitud de vacaciones #{$solicitud->id} cancelada por el propio colaborador #{$empleado->id}");

        return back()->with('success', 'Solicitud cancelada.');
    }

    /**
     * Borrado real de un registro erróneo — reservado a admin (sgrh.vacacion.solicitud.destroy),
     * igual criterio que sgrh.contrato.destroy.
     */
    public function destroy(VacacionSolicitud $solicitud)
    {
        $empleadoId = $solicitud->empleado_id;
        $solicitud->delete();

        $this->auditoria("Solicitud de vacaciones #{$solicitud->id} eliminada (colaborador #{$empleadoId})");

        return back()->with('success', 'Solicitud eliminada.');
    }

    /**
     * Panel de alertas de RRHH, calculado en vivo — no depende de que el scheduler corra (hoy
     * no está activo en este proyecto, ver Kernel.php).
     */
    public function alertas()
    {
        $represadas = VacacionSolicitud::with('empleado.tercero')
            ->pendientes()
            ->where('created_at', '<=', now()->subDays(5))
            ->orderBy('created_at')
            ->get();

        $colectivasProximas = VacacionColectiva::where('estado', 'activa')
            ->whereBetween('fecha_inicio', [now(), now()->addDays(30)])
            ->orderBy('fecha_inicio')
            ->get();

        $politica = VacacionPolitica::vigente();
        $saldoNegativo = Empleado::where('estado', 'activo')->get()
            ->map(fn (Empleado $e) => ['empleado' => $e, 'saldo' => $this->calculador->saldoActual($e, $politica)])
            ->filter(fn ($item) => $item['saldo'] !== null && $item['saldo'] < 0)
            ->values();

        return view('sgrh.vacacion.alertas.index', compact('represadas', 'colectivasProximas', 'saldoNegativo'));
    }

    private function rolDeResolucion($user, VacacionSolicitud $solicitud): string
    {
        $empleadoUser = $this->resolver->empleadoDeUsuario($user);

        if ($empleadoUser) {
            $jefe = $this->resolver->jefeInmediatoDe($solicitud->empleado);
            if ($jefe && $jefe->id === $empleadoUser->id) {
                return 'jefe_inmediato';
            }

            if ($this->resolver->equipoExtendidoDe($empleadoUser)->contains('id', $solicitud->empleado_id)) {
                return 'director';
            }
        }

        return 'rrhh';
    }

    /**
     * El correo es "best effort": un fallo de envío (SMTP caído, MAIL_FROM_ADDRESS sin
     * configurar, etc.) no debe tumbar la operación que ya se guardó correctamente en la BD —
     * la solicitud/aprobación queda igual visible en la bandeja correspondiente sin depender
     * del correo.
     */
    private function notificarNuevaSolicitud(VacacionSolicitud $solicitud): void
    {
        $jefe = $this->resolver->jefeInmediatoDe($solicitud->empleado);
        $destinatario = $jefe?->user;

        if (!$destinatario) {
            // Sin jefe inmediato resoluble: no hay a quién avisar puntualmente por correo — el
            // caso queda igual visible en la bandeja de RRHH vía solicitudesVisiblesPara().
            return;
        }

        try {
            Mail::to($destinatario->email)->send(new VacacionSolicitudCreadaMail($solicitud));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function notificarResolucion(VacacionSolicitud $solicitud): void
    {
        $destinatario = $solicitud->empleado->user;

        if (!$destinatario) {
            return;
        }

        try {
            Mail::to($destinatario->email)->send(new VacacionSolicitudResueltaMail($solicitud));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
