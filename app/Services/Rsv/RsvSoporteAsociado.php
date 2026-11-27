<?php

namespace App\Services\Rsv;

use App\Models\Rsv\Mensaje;
use App\Models\Rsv\Reserva;
use App\Models\Soportes\ScpSoporte;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Soporte del Portal Asociado dentro de cada reserva:
 *  - Hay agente en línea  → chat de la reserva (rsv_mensajes).
 *  - No hay agente        → se le ofrece crear un ticket en la plataforma Soportes (scp_soportes).
 *
 * "Agente en línea" = usuario con permiso rsv.soportes.responder que hizo alguna petición en los
 * últimos MINUTOS_EN_LINEA minutos (SESSION_DRIVER=database actualiza sessions.last_activity en
 * cada request; el panel admin manda un latido periódico para no "desconectarse" estando abierto).
 */
class RsvSoporteAsociado
{
    public const MINUTOS_EN_LINEA = 10;
    public const CARGO_ASOCIADO   = 'Asociado (portal de reservas)';
    public const SUBTIPO_RESERVAS = 'APP Corpentunida-Reservas';

    /** Usuarios con permiso de responder soporte RSV y actividad reciente (excluye a $excluirId). */
    public static function agentesEnLinea(?int $excluirId = null): Collection
    {
        try {
            $ids = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', now()->subMinutes(self::MINUTOS_EN_LINEA)->getTimestamp())
                ->when($excluirId, fn ($q) => $q->where('user_id', '!=', $excluirId))
                ->distinct()
                ->pluck('user_id');
        } catch (\Throwable $e) {
            return collect(); // sin tabla sessions (otro driver) = nadie en línea → se ofrece ticket
        }

        if ($ids->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $ids)->get()->filter(function (User $u) {
            try {
                return $u->can(RsvAcceso::SOPORTES_RESPONDER);
            } catch (\Throwable $e) {
                return false;
            }
        })->values();
    }

    public static function hayAgenteEnLinea(?int $excluirId = null): bool
    {
        return self::agentesEnLinea($excluirId)->isNotEmpty();
    }

    /**
     * Crea el ticket en scp_soportes. Con reserva: la referencia en el detalle y deja la nota en el
     * chat de la reserva. Sin reserva (pestaña Soporte del portal): consulta general del asociado.
     *
     * @throws \RuntimeException con mensaje apto para el asociado
     */
    public static function crearTicket(?Reserva $reserva, User $user, string $descripcion, ?UploadedFile $adjunto = null): ScpSoporte
    {
        $cargoId = DB::table('gdo_cargo')->where('nombre_cargo', self::CARGO_ASOCIADO)->value('id');
        $subtipo = DB::table('scp_sub_tipos')->where('nombre', self::SUBTIPO_RESERVAS)->first();
        $tipo    = $subtipo ? DB::table('scp_tipos')->where('id', $subtipo->scp_tipo_id)->first() : null;
        $prioId  = DB::table('scp_prioridads')->where('nombre', 'Media')->value('id');

        if (!$cargoId || !$subtipo || !$tipo || !$tipo->id_categoria || !$prioId) {
            Log::error('RSV ticket: catálogo incompleto', compact('cargoId', 'prioId') + ['subtipo' => $subtipo->id ?? null, 'tipo' => $tipo->id ?? null]);
            throw new \RuntimeException('La plataforma de tickets no está configurada para reservas. Contacta a Corpentunida por otro medio.');
        }

        $asociado = 'Asociado: ' . $user->name . ' (' . $user->email . ($user->nid ? ' · CC ' . $user->nid : '') . ")\n";
        if ($reserva) {
            $reserva->loadMissing('inmueble');
            $link = route('rsv.cliente.portal', ['tab' => 'mis-reservas', 'reserva_id' => $reserva->id]);
            $detalle = "[Portal Asociado · Reserva {$reserva->codigo_reserva}]\n" . $asociado
                . 'Alojamiento: ' . (optional($reserva->inmueble)->name ?? '—')
                . ' · ' . optional($reserva->fecha_inicio)->format('d/m/Y') . ' – ' . optional($reserva->fecha_fin)->format('d/m/Y') . "\n"
                . "Enlace: {$link}\n\n";
        } else {
            $detalle = "[Portal Asociado · Consulta general, sin reserva]\n" . $asociado . "\n";
        }
        $detalle .= trim($descripcion);

        $rutaAdjunto = null;
        if ($adjunto) {
            // Igual que ScpSoporteController@store: S3 privado, el módulo Soportes lo lee desde ahí.
            if (!config('filesystems.disks.s3.bucket')) {
                throw new \RuntimeException('Por ahora no es posible adjuntar archivos al ticket. Envíalo sin adjunto y descríbelo en el texto.');
            }
            try {
                $nombre = time() . '_' . Str::slug(pathinfo($adjunto->getClientOriginalName(), PATHINFO_FILENAME), '-') . '.' . $adjunto->getClientOriginalExtension();
                $rutaAdjunto = Storage::disk('s3')->putFileAs('corpentunida/soportes/usuario_' . $user->id . '/' . now()->format('Y/m'), $adjunto, $nombre);
            } catch (\Throwable $e) {
                Log::error('RSV ticket: no se pudo subir adjunto', ['err' => $e->getMessage(), 'reserva' => $reserva?->id]);
                throw new \RuntimeException('No se pudo subir el archivo adjunto. Intenta de nuevo o envía el ticket sin adjunto.');
            }
        }

        return DB::transaction(function () use ($reserva, $user, $detalle, $descripcion, $cargoId, $subtipo, $tipo, $prioId, $rutaAdjunto) {
            $ticket = ScpSoporte::create([
                'detalles_soporte' => $detalle,
                'timestam'         => now(),
                'id_gdo_cargo'     => $cargoId,
                'id_categoria'     => $tipo->id_categoria,
                'id_scp_tipo'      => $tipo->id,
                'id_scp_prioridad' => $prioId,
                'id_users'         => $user->id,
                'id_scp_sub_tipo'  => $subtipo->id,
                'estado'           => 1, // SinAsignar
                'soporte'          => $rutaAdjunto,
            ]);

            if ($reserva) {
                Mensaje::create([
                    'id_rsv_reservas'   => $reserva->id,
                    'id_user_remitente' => $user->id,
                    'tipo_mensaje'      => 'ticket',
                    'contenido'         => 'Ticket de soporte #' . $ticket->id . ': ' . Str::limit(trim($descripcion), 900),
                    'id_scp_soporte'    => $ticket->id,
                    // Queda atendido por la plataforma de tickets: no cuenta como "sin responder" en el chat
                    'leido_en'          => now(),
                ]);
            }

            return $ticket;
        });
    }
}
