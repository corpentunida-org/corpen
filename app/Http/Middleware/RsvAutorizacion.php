<?php

namespace App\Http\Middleware;

use App\Services\Rsv\RsvAcceso as A;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Módulo Rsv · autorización por ruta (aplicada a TODO el grupo /rsv).
 *
 * Mapa central "nombre de ruta → permiso". Lectura (GET/HEAD) y escritura pueden pedir permisos
 * distintos. Lo que no esté en el mapa se NIEGA (deny-by-default): una ruta nueva del panel queda
 * cerrada hasta que alguien la mapee aquí.
 *
 * Las rutas del portal (rsv.cliente.*) y los endpoints que comparten asociado y panel (crear
 * reserva, huéspedes, subir pago, mensajes, calificar, PDF) pasan, y su CONTROLADOR valida que la
 * reserva sea del usuario o que tenga el permiso correspondiente (RsvAcceso::titularOPuede).
 */
class RsvAutorizacion
{
    /** Endpoints compartidos: la titularidad se valida en el controlador. */
    private const COMPARTIDAS = [
        'rsv.reservas.store', 'rsv.reservas.store-ajax', 'rsv.reservas.pdf', 'rsv.reservas.fechas-ocupadas',
        'rsv.reserva-huespedes.store', 'rsv.reserva-huespedes.update', 'rsv.reserva-huespedes.destroy',
        'rsv.transacciones.store', 'rsv.mensajes.store', 'rsv.reviews.store',
    ];

    /** Prefijo de ruta → [permisos para leer, permisos para escribir] (basta con uno de la lista). */
    private const REGLAS = [
        'rsv.admin.'                 => [[A::PANEL], [A::PANEL]],
        'rsv.reservas.cambiar_estado' => [[A::RESERVAS_GESTIONAR], [A::RESERVAS_GESTIONAR]],
        'rsv.reservas.'              => [[A::RESERVAS_VER], [A::RESERVAS_GESTIONAR]],
        'rsv.reserva-huespedes.'     => [[A::RESERVAS_VER], [A::RESERVAS_GESTIONAR]],
        'rsv.itinerarios.'           => [[A::RESERVAS_VER], [A::RESERVAS_GESTIONAR]],
        'rsv.historial-endosos.'     => [[A::RESERVAS_VER], [A::RESERVAS_GESTIONAR]],
        'rsv.historial-estados.'     => [[A::RESERVAS_VER], [A::RESERVAS_GESTIONAR]],
        'rsv.inmuebles.'             => [[A::PANEL], [A::INMUEBLES]],
        'rsv.tipos-inmuebles.'       => [[A::PANEL], [A::INMUEBLES]],
        'rsv.inmueble-multimedia.'   => [[A::PANEL], [A::INMUEBLES]],
        'rsv.tarifas-temporadas.'    => [[A::PANEL], [A::INMUEBLES]],
        'rsv.bloqueos-calendario.'   => [[A::PANEL], [A::INMUEBLES]],
        'rsv.transacciones.'         => [[A::FINANZAS_VER], [A::PAGOS_VALIDAR]],
        'rsv.pasarelas.'             => [[A::FINANZAS_VER, A::CONFIG], [A::CONFIG]],
        'rsv.mensajes.'              => [[A::SOPORTES_RESPONDER], [A::SOPORTES_RESPONDER]],
        'rsv.soportes.'              => [[A::SOPORTES_RESPONDER], [A::SOPORTES_RESPONDER]],
        'rsv.reviews.'               => [[A::REVIEWS_VER], [A::CONFIG]],
        'rsv.statuses.'              => [[A::PANEL], [A::CONFIG]],
        'rsv.origen-reservas.'       => [[A::PANEL], [A::CONFIG]],
        'rsv.tipo-receptor.'         => [[A::PANEL], [A::CONFIG]],
        'rsv.audit-logs.'            => [[A::REPORTES_VER], [A::REPORTES_VER]],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $nombre = (string) optional($request->route())->getName();

        if (str_starts_with($nombre, 'rsv.cliente.') || in_array($nombre, self::COMPARTIDAS, true)) {
            return $next($request);
        }

        foreach (self::REGLAS as $prefijo => [$lectura, $escritura]) {
            if ($nombre === $prefijo || str_starts_with($nombre, $prefijo)) {
                $permisos = $request->isMethodSafe() ? $lectura : $escritura;
                if (A::puede(...$permisos)) {
                    return $next($request);
                }
                return $this->denegar($request, $nombre, $permisos);
            }
        }

        // No mapeada → cerrada
        return $this->denegar($request, $nombre, ['(ruta sin regla en RsvAutorizacion)']);
    }

    private function denegar(Request $request, string $nombre, array $permisos): Response
    {
        Log::warning('Rsv acceso denegado', [
            'user' => \Illuminate\Support\Facades\Auth::id(), 'ruta' => $nombre, 'metodo' => $request->method(), 'requiere' => $permisos,
        ]);
        $msg = 'No tienes permiso para esta sección del módulo de reservas.';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'ok' => false, 'message' => $msg], 403);
        }
        abort(403, $msg);
    }
}
