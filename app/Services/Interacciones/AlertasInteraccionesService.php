<?php

namespace App\Services\Interacciones;

use App\Mail\Interacciones\InteraccionesEscalacionPosponerMail;
use App\Models\Interacciones\IntAlertaConfig;
use App\Models\Interacciones\IntAlertaDecision;
use App\Models\Interacciones\IntAlertaEscalacion;
use App\Models\Interacciones\IntAlertaOmitido;
use App\Models\Interacciones\IntSeguimiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Centraliza las consultas que usan las alertas de Interacciones (campanita en pantalla, correo
 * diario de vencidas, informe semanal e inactividad por área) — todas comparten la misma
 * definición de "vencida" y la misma forma de listar quién pertenece a cada área, para no repetir
 * (ni desalinear con el tiempo) la misma lógica en cuatro sitios distintos.
 */
class AlertasInteraccionesService
{
    /**
     * Base compartida entre vencidas y pendientes: el último seguimiento de cada interacción
     * (el más reciente, vía el subquery MAX(id) agrupado por id_interaction — así una fecha
     * vieja ya superada por una gestión más nueva no sigue contando) de una interacción que
     * todavía no se cerró en éxito, del agente o asignado indicado. $vencida decide cuál mitad:
     * true = next_action_date ya pasó (vencida), false = sin fecha o con fecha aún futura
     * (pendiente, pero a tiempo). Misma definición exacta que InteractionController@reportPdf
     * usa para "Acciones Vencidas"/"Pendientes", pero aquí sin acotar por rango de fechas de la
     * interacción: importa cualquiera que siga abierta ahora, sin importar cuándo se creó.
     */
    private function queryBase(int $userId, bool $vencida)
    {
        $query = IntSeguimiento::whereIn('id', DB::table('int_seguimiento')->select(DB::raw('MAX(id)'))->groupBy('id_interaction'))
            ->whereHas('interaction', function ($q) use ($userId) {
                $q->where(fn ($q2) => $q2->where('agent_id', $userId)->orWhere('id_user_asignacion', $userId))
                    ->whereHas('outcomeRelation', fn ($q2) => $q2->where('estado', '!=', 1)->orWhereNull('estado'));
            });

        if ($vencida) {
            $query->whereNotNull('next_action_date')->where('next_action_date', '<', Carbon::now());
        } else {
            $query->where(fn ($q) => $q->whereNull('next_action_date')->orWhere('next_action_date', '>=', Carbon::now()));
        }

        return $query;
    }

    /** Vencidas del usuario ahora mismo, con el detalle para listarlas (campanita, correo). */
    public function vencidasDeUsuario(int $userId): Collection
    {
        return $this->queryBase($userId, vencida: true)
            ->with(['interaction.client', 'nextAction'])
            ->orderBy('next_action_date')
            ->get();
    }

    /** Cuántas vencidas tiene el usuario ahora mismo — para la campanita (badge/contador). */
    public function contarVencidasDeUsuario(int $userId): int
    {
        return $this->queryBase($userId, vencida: true)->count();
    }

    /**
     * Pendientes "a tiempo": interacciones aún abiertas cuya próxima acción no ha vencido (o no
     * tiene fecha puesta todavía) — el mismo universo que la tarjeta "Pendientes" del Informe,
     * menos las que ya están vencidas (esas van en la otra pestaña).
     */
    public function pendientesDeUsuario(int $userId): Collection
    {
        return $this->queryBase($userId, vencida: false)
            ->with(['interaction.client', 'nextAction'])
            ->orderByRaw('next_action_date IS NULL, next_action_date ASC')
            ->get();
    }

    /** Cuántas pendientes (no vencidas) tiene el usuario ahora mismo. */
    public function contarPendientesDeUsuario(int $userId): int
    {
        return $this->queryBase($userId, vencida: false)->count();
    }

    /**
     * Todos los usuarios activos (ni bloqueados ni borrados) con acceso al módulo de
     * Interacciones (permiso menu.interacciones), sin importar el área — usado para el correo
     * diario de vencidas, que aplica a cualquier agente del módulo.
     */
    public function usuariosConAccesoInteracciones(): Collection
    {
        // Excluye a quien esté en la lista de "omitir correos" (Admin → Configuración de
        // Alertas → Agentes Omitidos) — este es el único correo que le llega directo al propio
        // agente, los demás (informe semanal, inactividad, escalación) van a los admon.
        $omitidosCorreo = IntAlertaOmitido::idsOmitidos('correo');

        return User::whereNull('deleted_at')
            ->where(fn ($q) => $q->where('bloqueado', false)->orWhereNull('bloqueado'))
            ->whereHas('permissions', fn ($q) => $q->where('name', 'menu.interacciones'))
            ->whereNotIn('id', $omitidosCorreo)
            ->get();
    }

    /**
     * Áreas que realmente usan Interacciones — roles con área asignada que tienen el permiso
     * menu.interacciones (ver conversación: no todas las áreas de la ERP usan este módulo).
     */
    public function areasConInteracciones(): array
    {
        return DB::table('roles')
            ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('permissions.name', 'menu.interacciones')
            ->whereNotNull('roles.area')
            ->distinct()
            ->pluck('roles.area')
            ->toArray();
    }

    /**
     * Usuarios activos que pertenecen a esa área (cualquier rol de esa área, admon o users) y
     * tienen acceso a Interacciones — el universo completo de agentes del área, se hayan
     * registrado interacciones o no (clave para que el informe semanal y la alerta de
     * inactividad muestren también a quien no registró nada, no solo a quien sí aparece en
     * la consulta de interacciones).
     */
    public function usuariosDelArea(string $area): Collection
    {
        $userIds = DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->where('roles.area', $area)
            ->distinct()
            ->pluck('actions.user_id');

        return User::whereIn('id', $userIds)
            ->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('bloqueado', false)->orWhereNull('bloqueado'))
            ->whereHas('permissions', fn ($q) => $q->where('name', 'menu.interacciones'))
            ->orderBy('name')
            ->get();
    }

    /**
     * Administradores del área — cualquier usuario activo con un rol "*admon" de esa área
     * (carteraadmon, segurosadmon, etc., ver roles en BD). Son los destinatarios del informe
     * semanal y de la alerta de inactividad.
     */
    public function adminsDelArea(string $area): Collection
    {
        $userIds = DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->where('roles.area', $area)
            ->where('roles.name', 'LIKE', '%admon')
            ->distinct()
            ->pluck('actions.user_id');

        return User::whereIn('id', $userIds)
            ->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('bloqueado', false)->orWhereNull('bloqueado'))
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Interacciones que el usuario registró un día calendario concreto — base de la alerta de
     * inactividad (0 ese día) y del informe semanal (total de la semana).
     */
    public function interaccionesDelUsuarioEntre(int $userId, Carbon $desde, Carbon $hasta): int
    {
        return DB::table('interactions')
            ->where('agent_id', $userId)
            ->whereBetween('interaction_date', [$desde, $hasta])
            ->count();
    }

    /**
     * Área del usuario — mismo criterio que InteractionController@areaDelUsuario (prefiere un
     * rol que SÍ tenga área sobre uno que no, para los perfiles legado con más de uno).
     */
    public function areaDelUsuario(int $userId): ?string
    {
        return DB::table('actions')
            ->join('roles', 'roles.id', '=', 'actions.role_id')
            ->where('actions.user_id', $userId)
            ->orderByRaw('roles.area is null')
            ->value('roles.area');
    }

    /**
     * Registra la decisión del día (responder/posponer) cuando el aviso de cada 3 horas fuerza
     * a elegir — una fila por usuario y día: "responder" en cualquier momento del día gana y
     * queda así el resto del día (no se puede "des-responder" posponiendo después); "posponer"
     * solo se guarda si todavía no hay un "responder" registrado hoy. Después revisa si esto
     * completa una racha de días seguidos posponiendo y, si corresponde, avisa al admon del
     * área — devuelve el estado para que la pantalla pueda informarlo.
     *
     * @return array{streak: int, escalado: bool}
     */
    public function registrarDecision(int $userId, string $decision): array
    {
        $hoy = Carbon::today();

        $existente = IntAlertaDecision::where('user_id', $userId)->whereDate('fecha', $hoy)->first();

        if ($decision === 'responder') {
            if ($existente) {
                $existente->update(['decision' => 'responder']);
            } else {
                IntAlertaDecision::create(['user_id' => $userId, 'fecha' => $hoy, 'decision' => 'responder']);
            }
            // Responder rompe la racha de inmediato, no hace falta seguir revisando escalación.
            return ['streak' => 0, 'escalado' => false];
        }

        // decision === 'posponer': no pisa un "responder" que ya exista hoy.
        if (! $existente) {
            IntAlertaDecision::create(['user_id' => $userId, 'fecha' => $hoy, 'decision' => 'posponer']);
        } elseif ($existente->decision === 'responder') {
            return ['streak' => 0, 'escalado' => false];
        }

        $streak = $this->streakPosponerConsecutivo($userId, $hoy);
        $escalado = false;

        // Umbral configurable (Admin → Configuración de Alertas de Interacciones, antes era un
        // 3 fijo). Avisa al llegar al umbral, y de nuevo cada tantos días adicionales si la
        // persona sigue sin responder — sin repetir el aviso más de una vez el mismo día.
        $umbral = max(1, IntAlertaConfig::actual()->dias_posponer_para_escalar);
        if ($streak >= $umbral && $streak % $umbral === 0) {
            $yaEscalado = IntAlertaEscalacion::where('user_id', $userId)->whereDate('fecha', $hoy)->exists();
            if (! $yaEscalado) {
                $escalado = $this->escalarPosponerReiterado($userId, $streak, $hoy);
            }
        }

        return ['streak' => $streak, 'escalado' => $escalado];
    }

    /** Cuántos días seguidos (terminando en $hasta) el usuario solo pospuso, sin responder. */
    private function streakPosponerConsecutivo(int $userId, Carbon $hasta): int
    {
        $decisiones = IntAlertaDecision::where('user_id', $userId)
            ->where('fecha', '<=', $hasta->toDateString())
            ->orderByDesc('fecha')
            ->limit(60) // margen de sobra; una racha real de meses sería un caso aparte
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

    /** Envía el correo de escalación al/los admon del área del usuario y deja constancia. */
    private function escalarPosponerReiterado(int $userId, int $diasConsecutivos, Carbon $hoy): bool
    {
        $usuario = User::find($userId);
        $area = $this->areaDelUsuario($userId);

        if (! $usuario || ! $area) {
            return false;
        }

        $admins = $this->adminsDelArea($area);
        if ($admins->isEmpty()) {
            return false;
        }

        Mail::to($admins->pluck('email')->all())
            ->send(new InteraccionesEscalacionPosponerMail($usuario, $area, $diasConsecutivos));

        IntAlertaEscalacion::create(['user_id' => $userId, 'fecha' => $hoy, 'dias_consecutivos' => $diasConsecutivos]);

        return true;
    }
}
