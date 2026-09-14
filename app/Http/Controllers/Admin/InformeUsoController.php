<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InformeUso\InformeUsoExport;
use App\Http\Controllers\Controller;
use App\Models\Interacciones\Interaction;
use App\Models\User;
use App\Models\UserSesion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Indicador de uso de la aplicación: qué área y qué usuario generan más actividad, y quién
 * tiene más tiempo activo. Combina varias fuentes de "actividad" que viven separadas en el
 * código: la tabla Auditoria (Exequiales, Seguros, Correspondencia, SGRH, Reservas, Cinco,
 * Admin), los logs propios de Certificados (car_sia_operaciones_logs), la tabla `interactions`
 * de Interacciones/gestión de cartera y `res_reservas` de Reservas — ninguna de estas tres
 * últimas escribe en Auditoria, así que sin ellas esas áreas aparecerían como si nadie las usara
 * (Interacciones en particular es, con enorme diferencia, la de mayor volumen real: ~17.000
 * registros contra ~3.500 del área que le sigue).
 *
 * `res_reservas` es un caso distinto de los otros dos: la creación de la reserva la hace
 * directamente el asociado (no un funcionario) desde un portal de autoservicio — de 190 personas
 * que reservaron, 188 son de tipo ASOCIADO, no personal interno (`users.type`). Por eso sí suma
 * al total del área (mide uso real del módulo) pero NO se agrega al ranking por usuario, que es
 * sobre actividad de funcionarios — mezclar ahí a los asociados haría parecer que algún feligrés
 * es "muy activo" cuando solo reservó un salón una vez.
 *
 * `car_comprobantes_pagos` (Cartera — registro de pagos, 6.151 filas y tampoco escribe en
 * Auditoria) sí es enteramente trabajo de funcionarios: sus 15 usuarios distintos son todos
 * personal interno, así que aquí no aplica la salvedad de Reservas y se suma completo al
 * ranking por usuario también.
 *
 * "Tiempo activo" tiene dos fuentes de naturaleza distinta, que no se deben sumar como si fueran
 * lo mismo:
 * - sesiones_usuario (login/logout, ver App\Listeners\Registrar*Sesion y
 *   App\Http\Middleware\RegistrarActividadUsuario): cobertura de toda la app, pero solo tiene
 *   datos desde que se desplegó esa función — no hay forma de reconstruir sesiones pasadas.
 * - `interactions.duration`: duración de cada llamada/gestión de cartera, ya poblada desde abril
 *   2026. Es un dato real e histórico, pero exclusivo de Interacciones (no cubre el resto de la
 *   app), así que se reporta aparte.
 */
class InformeUsoController extends Controller
{
    // Misma cuenta de sistemas/desarrollo marcada en AuditoriaController::USUARIO_ID_SISTEMAS:
    // sin esta marca saldría como "usuario más activo" por cargas masivas puntuales, no por
    // trabajo operativo — engañoso en un indicador que se va a presentar como tal.
    private const USUARIO_ID_SISTEMAS = 4;

    public function index(Request $request)
    {
        return view('admin.informe_uso.index', $this->datosInforme($request));
    }

    public function exportarExcel(Request $request)
    {
        $datos = $this->datosInforme($request);
        $nombre = 'Informe_Uso_' . $datos['desde'] . '_a_' . $datos['hasta'] . '.xlsx';

        return Excel::download(new InformeUsoExport($datos), $nombre);
    }

    public function exportarPdf(Request $request)
    {
        $datos = $this->datosInforme($request);

        $pdf = Pdf::loadView('admin.informe_uso.pdf', $datos)->setPaper('letter', 'landscape');

        return $pdf->download('Informe_Uso_' . $datos['desde'] . '_a_' . $datos['hasta'] . '.pdf');
    }

    /**
     * Datos del informe, compartidos por la vista web y las dos exportaciones — así el Excel/PDF
     * descargado es siempre exactamente lo que se ve en pantalla para el mismo rango de fechas.
     */
    private function datosInforme(Request $request): array
    {
        $desde = $request->filled('fecha_desde') ? $request->input('fecha_desde') : now()->startOfMonth()->toDateString();
        $hasta = $request->filled('fecha_hasta') ? $request->input('fecha_hasta') : now()->toDateString();

        $porArea = $this->usoPorArea($desde, $hasta);
        $porUsuario = $this->usoPorUsuario($desde, $hasta);
        $tiempoActivo = $this->tiempoActivoPorUsuario($desde, $hasta);
        $tiempoGestiones = $this->tiempoGestionesPorAgente($desde, $hasta);

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'porArea' => $porArea,
            'porUsuario' => $porUsuario,
            'tiempoActivo' => $tiempoActivo,
            'tiempoGestiones' => $tiempoGestiones,
            'areaTop' => $porArea->first(),
            'usuarioTop' => $porUsuario->first(),
            'tiempoActivoTop' => $tiempoActivo->first(),
            'tiempoGestionesTop' => $tiempoGestiones->first(),
        ];
    }

    /** Usado por la vista web y por el PDF (ver resources/views/admin/informe_uso/*.blade.php). */
    public static function formatoDuracion(int $segundos): string
    {
        $horas = intdiv($segundos, 3600);
        $minutos = intdiv($segundos % 3600, 60);

        return $horas > 0 ? "{$horas}h {$minutos}m" : "{$minutos}m";
    }

    private function usoPorArea(string $desde, string $hasta)
    {
        $porArea = DB::table('Auditoria')
            ->whereDate('fechaRegistro', '>=', $desde)
            ->whereDate('fechaRegistro', '<=', $hasta)
            ->select('area', DB::raw('count(*) as total'))
            ->groupBy('area')
            ->pluck('total', 'area');

        $totalCertificados = DB::table('car_sia_operaciones_logs')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->count();

        if ($totalCertificados > 0) {
            $porArea->put('CERTIFICADOS', ($porArea->get('CERTIFICADOS', 0)) + $totalCertificados);
        }

        $totalInteracciones = DB::table('interactions')
            ->whereDate('interaction_date', '>=', $desde)
            ->whereDate('interaction_date', '<=', $hasta)
            ->count();

        if ($totalInteracciones > 0) {
            $porArea->put('INTERACCIONES', ($porArea->get('INTERACCIONES', 0)) + $totalInteracciones);
        }

        $totalReservas = DB::table('res_reservas')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->count();

        if ($totalReservas > 0) {
            $porArea->put('RESERVAS', ($porArea->get('RESERVAS', 0)) + $totalReservas);
        }

        $totalCartera = DB::table('car_comprobantes_pagos')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->count();

        if ($totalCartera > 0) {
            $porArea->put('CARTERA', ($porArea->get('CARTERA', 0)) + $totalCartera);
        }

        $maximo = $porArea->max() ?: 1;

        return $porArea->sortDesc()->map(fn ($total, $area) => [
            'area' => $area,
            'total' => $total,
            'porcentaje' => (int) round(($total / $maximo) * 100),
        ])->values();
    }

    private function usoPorUsuario(string $desde, string $hasta)
    {
        $deAuditoria = DB::table('Auditoria')
            ->whereDate('fechaRegistro', '>=', $desde)
            ->whereDate('fechaRegistro', '<=', $hasta)
            ->whereNotNull('usuario_id')
            ->select('usuario_id', DB::raw('count(*) as total'))
            ->groupBy('usuario_id')
            ->pluck('total', 'usuario_id');

        $deCertificados = DB::table('car_sia_operaciones_logs')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->whereNotNull('id_user')
            ->select('id_user', DB::raw('count(*) as total'))
            ->groupBy('id_user')
            ->pluck('total', 'id_user');

        $deInteracciones = DB::table('interactions')
            ->whereDate('interaction_date', '>=', $desde)
            ->whereDate('interaction_date', '<=', $hasta)
            ->whereNotNull('agent_id')
            ->select('agent_id', DB::raw('count(*) as total'))
            ->groupBy('agent_id')
            ->pluck('total', 'agent_id');

        // Solo personal interno (users.type IS NULL, mismo filtro que Admin\UserController::index
        // usa para el directorio de "usuarios"): la mayoría de quienes reservan son asociados, no
        // funcionarios (ver docblock de la clase), y este ranking es de actividad de funcionarios.
        $deReservas = DB::table('res_reservas')
            ->join('users', 'users.id', '=', 'res_reservas.user_id')
            ->whereNull('users.type')
            ->whereDate('res_reservas.created_at', '>=', $desde)
            ->whereDate('res_reservas.created_at', '<=', $hasta)
            ->select('res_reservas.user_id', DB::raw('count(*) as total'))
            ->groupBy('res_reservas.user_id')
            ->pluck('total', 'user_id');

        $deCartera = DB::table('car_comprobantes_pagos')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->whereNotNull('id_user')
            ->select('id_user', DB::raw('count(*) as total'))
            ->groupBy('id_user')
            ->pluck('total', 'id_user');

        $totales = collect();
        foreach ($deAuditoria as $userId => $total) {
            $totales->put($userId, $totales->get($userId, 0) + $total);
        }
        foreach ($deCertificados as $userId => $total) {
            $totales->put($userId, $totales->get($userId, 0) + $total);
        }
        foreach ($deInteracciones as $userId => $total) {
            $totales->put($userId, $totales->get($userId, 0) + $total);
        }
        foreach ($deReservas as $userId => $total) {
            $totales->put($userId, $totales->get($userId, 0) + $total);
        }
        foreach ($deCartera as $userId => $total) {
            $totales->put($userId, $totales->get($userId, 0) + $total);
        }

        $nombres = User::whereIn('id', $totales->keys())->pluck('name', 'id');
        $maximo = $totales->max() ?: 1;

        return $totales->sortDesc()->map(fn ($total, $userId) => [
            'user_id' => $userId,
            'nombre' => $this->nombreParaReporte($userId, $nombres->get($userId, "Usuario #{$userId}")),
            'total' => $total,
            'porcentaje' => (int) round(($total / $maximo) * 100),
        ])->values();
    }

    private function tiempoActivoPorUsuario(string $desde, string $hasta)
    {
        $sesiones = UserSesion::whereDate('login_at', '>=', $desde)
            ->whereDate('login_at', '<=', $hasta)
            ->get();

        $porUsuario = $sesiones->groupBy('user_id')->map(function ($sesionesUsuario, $userId) {
            $segundosTotal = $sesionesUsuario->sum('duracion_segundos');

            return [
                'user_id' => $userId,
                'segundos_total' => $segundosTotal,
                'sesiones' => $sesionesUsuario->count(),
                'segundos_promedio' => (int) round($segundosTotal / max(1, $sesionesUsuario->count())),
            ];
        });

        $nombres = User::whereIn('id', $porUsuario->keys())->pluck('name', 'id');

        return $porUsuario->sortByDesc('segundos_total')->map(fn ($fila, $userId) => [
            ...$fila,
            'nombre' => $this->nombreParaReporte($userId, $nombres->get($userId, "Usuario #{$userId}")),
        ])->values();
    }

    /**
     * `interactions.duration` (segundos por llamada/gestión) sumado por agente — tiempo activo
     * real e histórico, pero solo de Interacciones (no cubre el resto de la app, ver el docblock
     * de la clase).
     */
    private function tiempoGestionesPorAgente(string $desde, string $hasta)
    {
        $porAgente = Interaction::whereDate('interaction_date', '>=', $desde)
            ->whereDate('interaction_date', '<=', $hasta)
            ->whereNotNull('agent_id')
            ->select('agent_id', DB::raw('count(*) as gestiones'), DB::raw('sum(duration) as segundos_total'))
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        $nombres = User::whereIn('id', $porAgente->keys())->pluck('name', 'id');

        return $porAgente->sortByDesc('segundos_total')->map(fn ($fila, $userId) => [
            'user_id' => $userId,
            'nombre' => $this->nombreParaReporte((int) $userId, $nombres->get($userId, "Usuario #{$userId}")),
            'gestiones' => $fila->gestiones,
            'segundos_total' => (int) $fila->segundos_total,
            'segundos_promedio' => (int) round($fila->segundos_total / max(1, $fila->gestiones)),
        ])->values();
    }

    private function nombreParaReporte(int $userId, string $nombre): string
    {
        return $userId === self::USUARIO_ID_SISTEMAS
            ? "{$nombre} (Sistemas / Desarrollo)"
            : $nombre;
    }
}
