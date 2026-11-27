<?php

namespace App\Services\Rsv;

use App\Models\Rsv\Reserva;
use Illuminate\Support\Facades\Auth;

/**
 * Módulo Rsv · permisos y roles (fuente única).
 *
 * Se apoya en Spatie (permisos directos de la Matriz + permisos que vienen por ROL). Los roles
 * rsv.* agrupan los permisos por perfil; a una persona se le puede dar un rol y/o permisos sueltos.
 * El ASOCIADO no necesita permiso: cualquier usuario autenticado, limitado a SUS reservas.
 *
 * Los permisos y roles se crean con: php artisan db:seed --class=RsvPermissionsSeeder
 */
class RsvAcceso
{
    // ── Permisos ──────────────────────────────────────────────────────────────
    public const PANEL              = 'rsv.panel.acceder';        // entrar a /rsv/admin
    public const INMUEBLES          = 'rsv.inmuebles.gestionar';  // crear/editar inmuebles, tarifas, galería, bloqueos
    public const RESERVAS_VER       = 'rsv.reservas.ver';         // ver todas las reservas
    public const RESERVAS_CREAR_OTRO = 'rsv.reservas.crear_otro'; // reservar a nombre de un asociado
    public const RESERVAS_GESTIONAR = 'rsv.reservas.gestionar';   // cambiar estado, editar, huéspedes ajenos
    public const RESERVAS_CANCELAR  = 'rsv.reservas.cancelar';    // pasar una reserva a Cancelada
    public const RESERVAS_EXCEPCION = 'rsv.reservas.excepcion';   // superar el límite anual (con justificación)
    public const PAGOS_VALIDAR      = 'rsv.pagos.validar';        // aprobar / rechazar / anular pagos
    public const FINANZAS_VER       = 'rsv.finanzas.ver';         // ver transacciones de todos
    public const SOPORTES_RESPONDER = 'rsv.soportes.responder';   // responder el chat de soporte
    public const REVIEWS_VER        = 'rsv.reviews.ver';          // ver calificaciones
    public const REPORTES_VER       = 'rsv.reportes.ver';         // auditoría / informes
    public const CONFIG             = 'rsv.config.gestionar';     // estados, orígenes, pasarelas, tipos de receptor

    /** Roles del módulo → permisos. "Limitado" de la matriz se resuelve dejando fuera gestionar/cancelar. */
    public const ROLES = [
        'rsv.admin' => [
            self::PANEL, self::INMUEBLES, self::RESERVAS_VER, self::RESERVAS_CREAR_OTRO, self::RESERVAS_GESTIONAR,
            self::RESERVAS_CANCELAR, self::RESERVAS_EXCEPCION, self::PAGOS_VALIDAR, self::FINANZAS_VER,
            self::SOPORTES_RESPONDER, self::REVIEWS_VER, self::REPORTES_VER, self::CONFIG,
        ],
        'rsv.contabilidad' => [
            self::PANEL, self::RESERVAS_VER, self::PAGOS_VALIDAR, self::FINANZAS_VER, self::REPORTES_VER,
        ],
        'rsv.soporte' => [
            self::PANEL, self::RESERVAS_VER, self::RESERVAS_CREAR_OTRO, self::SOPORTES_RESPONDER, self::REVIEWS_VER,
        ],
        // Fase 2: verá solo los inmuebles asignados en rsv_usuario_inmueble. Por ahora sin permisos.
        'rsv.hospedador' => [],
    ];

    /** ¿El usuario autenticado tiene ALGUNO de los permisos (directo o por rol)? */
    public static function puede(string ...$permisos): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        foreach ($permisos as $p) {
            try {
                if ($user->can($p)) {
                    return true;
                }
            } catch (\Throwable $e) {
                // permiso inexistente (seeder sin correr) = no lo tiene
            }
        }
        return false;
    }

    /** ¿La reserva es del usuario autenticado? */
    public static function esTitular(Reserva|int|null $reserva): bool
    {
        if (!$reserva || !Auth::check()) {
            return false;
        }
        $reserva = $reserva instanceof Reserva ? $reserva : Reserva::find($reserva);
        return $reserva && (int) $reserva->id_user === (int) Auth::id();
    }

    /** Dueño de la reserva o con alguno de los permisos indicados. */
    public static function titularOPuede(Reserva|int|null $reserva, string ...$permisos): bool
    {
        return self::esTitular($reserva) || self::puede(...$permisos);
    }

    /** Respuesta JSON uniforme de acceso denegado. */
    public static function denegado(string $mensaje = 'No tienes permiso para realizar esta acción.')
    {
        return response()->json(['success' => false, 'ok' => false, 'message' => $mensaje], 403);
    }
}
