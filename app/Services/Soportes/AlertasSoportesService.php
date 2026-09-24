<?php

namespace App\Services\Soportes;

use App\Mail\Soportes\SoportesEscalacionPosponerMail;
use App\Models\Soportes\ScpAlertaConfig;
use App\Models\Soportes\ScpAlertaDecision;
use App\Models\Soportes\ScpAlertaEscalacion;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpUsuario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * Réplica de AlertasInteraccionesService para Soportes — mismo mecanismo (aviso forzado,
 * decisión responder/posponer, racha de días seguidos posponiendo, escalación), adaptado a que
 * Soportes no tiene fecha límite (se usa "asignado a mí, sin cerrar" en vez de "vencida") ni una
 * estructura de admon por área (se escala a superadmin/admindesarrollo, ver conversación).
 */
class AlertasSoportesService
{
    /** El id de scp_usuarios que corresponde a este usuario de la app (users.id) — puede no existir. */
    private function scpUsuarioId(int $userId): ?int
    {
        return ScpUsuario::where('usuario', $userId)->value('id');
    }

    /** Soportes asignados a mí (usuario_escalado) que siguen sin cerrar (estado != 4). */
    public function pendientesDeUsuario(int $userId): Collection
    {
        $scpUsuarioId = $this->scpUsuarioId($userId);
        if (! $scpUsuarioId) {
            return collect();
        }

        return ScpSoporte::where('usuario_escalado', $scpUsuarioId)
            ->where('estado', '!=', 4)
            ->with(['estadoSoporte:id,nombre', 'prioridad:id,nombre'])
            ->orderByDesc('updated_at')
            ->get();
    }

    public function contarPendientesDeUsuario(int $userId): int
    {
        $scpUsuarioId = $this->scpUsuarioId($userId);
        if (! $scpUsuarioId) {
            return 0;
        }

        return ScpSoporte::where('usuario_escalado', $scpUsuarioId)->where('estado', '!=', 4)->count();
    }

    /**
     * Usuarios activos con rol superadmin o admindesarrollo — destinatarios de la escalación
     * (Soportes no tiene un "admon por área" al que avisar como sí tiene Interacciones).
     */
    public function usuariosGlobalAdmin(): Collection
    {
        return User::whereNull('deleted_at')
            ->where(fn ($q) => $q->where('bloqueado', false)->orWhereNull('bloqueado'))
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['superadmin', 'admindesarrollo']))
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Registra la decisión del día (responder/posponer) — misma lógica exacta que
     * AlertasInteraccionesService::registrarDecision(), ver comentarios allá para el detalle.
     *
     * @return array{streak: int, escalado: bool}
     */
    public function registrarDecision(int $userId, string $decision): array
    {
        $hoy = Carbon::today();
        $existente = ScpAlertaDecision::where('user_id', $userId)->whereDate('fecha', $hoy)->first();

        if ($decision === 'responder') {
            if ($existente) {
                $existente->update(['decision' => 'responder']);
            } else {
                ScpAlertaDecision::create(['user_id' => $userId, 'fecha' => $hoy, 'decision' => 'responder']);
            }

            return ['streak' => 0, 'escalado' => false];
        }

        if (! $existente) {
            ScpAlertaDecision::create(['user_id' => $userId, 'fecha' => $hoy, 'decision' => 'posponer']);
        } elseif ($existente->decision === 'responder') {
            return ['streak' => 0, 'escalado' => false];
        }

        $streak = $this->streakPosponerConsecutivo($userId, $hoy);
        $escalado = false;

        $umbral = max(1, ScpAlertaConfig::actual()->dias_posponer_para_escalar);
        if ($streak >= $umbral && $streak % $umbral === 0) {
            $yaEscalado = ScpAlertaEscalacion::where('user_id', $userId)->whereDate('fecha', $hoy)->exists();
            if (! $yaEscalado) {
                $escalado = $this->escalarPosponerReiterado($userId, $streak, $hoy);
            }
        }

        return ['streak' => $streak, 'escalado' => $escalado];
    }

    private function streakPosponerConsecutivo(int $userId, Carbon $hasta): int
    {
        $decisiones = ScpAlertaDecision::where('user_id', $userId)
            ->where('fecha', '<=', $hasta->toDateString())
            ->orderByDesc('fecha')
            ->limit(60)
            ->get()
            ->keyBy(fn ($d) => $d->fecha->toDateString());

        $streak = 0;
        $cursor = $hasta->copy();
        while (true) {
            $fila = $decisiones->get($cursor->toDateString());
            if (! $fila || $fila->decision !== 'posponer') {
                break;
            }
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    private function escalarPosponerReiterado(int $userId, int $diasConsecutivos, Carbon $hoy): bool
    {
        $usuario = User::find($userId);
        if (! $usuario) {
            return false;
        }

        $admins = $this->usuariosGlobalAdmin();
        if ($admins->isEmpty()) {
            return false;
        }

        Mail::to($admins->pluck('email')->all())
            ->send(new SoportesEscalacionPosponerMail($usuario, $diasConsecutivos));

        ScpAlertaEscalacion::create(['user_id' => $userId, 'fecha' => $hoy, 'dias_consecutivos' => $diasConsecutivos]);

        return true;
    }
}
