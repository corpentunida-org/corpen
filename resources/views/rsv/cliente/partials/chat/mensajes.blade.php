{{-- partials/chat/mensajes.blade.php · Cliente · Soporte y Chat Reactivo por Reserva sin Reload (CH-MSG-v2-DBG) --}}
<div id="sheet-chat-mensajes-cliente">

    {{-- CH-MSG-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="chMsgDbgToggle" title="Debug Mensajes Soporte (CH-MSG-v2-DBG)">
        <span class="ch-msg-dbg-led" id="chMsgDbgLed"></span>DBG<span class="ch-msg-dbg-badge" id="chMsgDbgBadge">0</span>
    </div>
    <div id="chMsgDbgPanel" role="dialog" aria-label="Panel de debug de Mensajes Soporte">
        <div class="ch-msg-dbg-header" id="chMsgDbgHeader">
            <span>DEBUG · Mensajes Soporte · CH-MSG-v2-DBG</span>
            <span class="ch-msg-dbg-controls">
                <button type="button" class="ch-msg-dbg-btn" id="chMsgDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="ch-msg-dbg-btn" id="chMsgDbgCopy">Copiar</button>
                <button type="button" class="ch-msg-dbg-btn" id="chMsgDbgClear">Limpiar</button>
                <button type="button" class="ch-msg-dbg-btn" id="chMsgDbgClose">✕</button>
            </span>
        </div>
        <div id="chMsgDbgBody"></div>
    </div>

    @php
        $authUser = \Illuminate\Support\Facades\Auth::user();
        $uid = $authUser ? $authUser->id : null;
        $rid = request('reserva_id') ?? request('id_reserva');
        $chMsgErrores = [];

        // 1. Cargar listado de reservas del cliente para el selector superior
        $misReservas = collect();
        if ($uid) {
            try {
                $misReservas = \App\Models\Rsv\Reserva::with(['inmueble:id,name,city', 'status'])
                    ->where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.chat.reservas: ' . $e->getMessage());
                $chMsgErrores[] = 'Reservas: ' . $e->getMessage();
            }
        }

        // 2. Determinar la reserva activa seleccionada (o auto-seleccionar la más reciente no cancelada)
        $reserva = null;
        if ($rid && is_numeric($rid)) {
            $reserva = $misReservas->firstWhere('id', (int) $rid);
            if (!$reserva && $uid) {
                try {
                    $reserva = \App\Models\Rsv\Reserva::with(['inmueble:id,name,city', 'status'])
                        ->where('id_user', $uid)
                        ->find((int) $rid);
                } catch (\Throwable $e) {}
            }
        }

        if (!$reserva && $misReservas->isNotEmpty()) {
            $reserva = $misReservas->first(function ($r) {
                $stName = mb_strtolower(optional($r->status)->name ?? '');
                return !str_contains($stName, 'cancela') && !str_contains($stName, 'anula') && !str_contains($stName, 'rechaz');
            }) ?? $misReservas->first();
        }

        // 3. Cargar tipos de receptor (si vienen del controlador o directo del modelo)
        $receptoresList = (isset($tiposReceptor) && $tiposReceptor instanceof \Illuminate\Support\Collection)
            ? $tiposReceptor
            : collect();

        if ($receptoresList->isEmpty()) {
            try {
                if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                    $receptoresList = \App\Models\Rsv\TipoReceptor::all();
                }
            } catch (\Throwable $e) {}
        }

        // 4. Obtener los mensajes de la reserva seleccionada
        $mensajesReserva = collect();
        if ($reserva) {
            try {
                if (method_exists($reserva, 'mensajes')) {
                    $mensajesReserva = $reserva->mensajes()
                        ->orderBy('created_at', 'asc')
                        ->get();
                } elseif (isset($mensajes) && $mensajes instanceof \Illuminate\Support\Collection) {
                    $mensajesReserva = $mensajes->filter(function ($m) use ($reserva) {
                        $fk = $m->id_rsv_reservas ?? $m->id_rsv_reserva ?? $m->reserva_id ?? null;
                        return (int) $fk === (int) $reserva->id;
                    })->sortBy('created_at')->values();
                }
            } catch (\Throwable $e) {
                $chMsgErrores[] = 'Mensajes: ' . $e->getMessage();
            }
        }

        // Rutas de fragmento y de guardado de mensaje
        $rutaFragmento = '';
        foreach (['rsv.cliente.chat.mensajes.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $rutaSend = '';
        foreach (['rsv.mensajes.store-ajax', 'rsv.mensajes.store', 'rsv.cliente.mensajes.store', 'mensajes.store'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaSend = route($rn);
                break;
            }
        }

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'cancela') || str_contains($s, 'anula') || str_contains($s, 'rechaz')) return 'danger';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob')) return 'success';
            return 'secondary';
        };

        $stNombre = optional($reserva?->status)->name ?? 'Pendiente';
        $stGroup  = $reserva ? $pillSt($stNombre) : 'secondary';
        $inm      = $reserva?->inmueble;

        $chMsgDbgData = [
            'version'      => 'CH-MSG-v2-DBG',
            'controlador'  => 'ClientePortalController@chatMensajesFragmento',
            'vista'        => 'partials/chat/mensajes.blade.php',
            'reservas_tot' => $misReservas->count(),
            'reserva_id'   => $reserva ? $reserva->id : null,
            'reserva_cod'  => $reserva ? $reserva->codigo_reserva : null,
            'estado'       => $reserva ? $stNombre : null,
            'mensajes_cnt' => $mensajesReserva->count(),
            'receptores'   => $receptoresList->count(),
            'frag_url'     => $rutaFragmento ?: 'fallback location',
            'send_url'     => $rutaSend ?: 'NO definida',
            'errores'      => $chMsgErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliChatRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="chat-mensajes"
         data-reserva-id="{{ $reserva ? $reserva->id : '' }}"
         data-reserva-cod="{{ $reserva ? $reserva->codigo_reserva : '' }}"
         data-estado="{{ $reserva ? $stNombre : '' }}"
         data-mensajes="{{ $mensajesReserva->count() }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:10.5px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.4; }

            .ch-select-res {
                font-size:9px; font-weight:600; color:var(--ink-2); background:var(--head);
                border:1px solid var(--grid-2); border-radius:6px; padding:4px 10px;
                outline:0; cursor:pointer; max-width:320px;
            }
            .ch-select-res:focus { border-color:var(--accent-bd); background:#fff; }

            /* ── BANNER CONTEXTUAL DE ESTADO DE LA RESERVA ── */
            .ch-status-strip {
                border-radius:8px; padding:8px 12px; margin-bottom:10px;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:8px; border:1px solid; font-size:8.5px;
            }
            .ch-status-strip.st-success   { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }
            .ch-status-strip.st-warning   { background:#fffbeb; border-color:#fde68a; color:#92400e; }
            .ch-status-strip.st-danger    { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
            .ch-status-strip.st-primary   { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
            .ch-status-strip.st-secondary { background:#f8fafc; border-color:#e2e8f0; color:#475569; }

            /* ── CAJA DE CONVERSACIÓN (CHAT) ── */
            .ch-chat-viewport {
                height:310px; overflow-y:auto; padding:12px 14px;
                background:linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
                border:1px solid var(--grid-2); border-radius:10px;
                display:flex; flex-direction:column; gap:8px;
            }
            .ch-bubble {
                max-width:80%; padding:8px 12px; border-radius:10px;
                font-size:9px; line-height:1.45; position:relative;
                box-shadow:0 1px 3px rgba(15,23,42,0.05); word-break:break-word;
            }
            .ch-bubble-mine {
                align-self:flex-end; background:var(--accent-bg);
                border:1px solid var(--accent-bd); color:#14532d;
                border-bottom-right-radius:2px;
            }
            .ch-bubble-other {
                align-self:flex-start; background:#ffffff;
                border:1px solid var(--grid-2); color:var(--ink-2);
                border-bottom-left-radius:2px;
            }
            .ch-bubble-meta {
                display:flex; justify-content:space-between; align-items:center;
                gap:10px; margin-bottom:3px; font-size:7.5px;
            }
            .ch-bubble-time { font-size:7px; opacity:0.7; text-align:right; margin-top:3px; }

            /* ── BARRA DE ENVÍO DE MENSAJES ── */
            .ch-composer {
                background:var(--head); border:1px solid var(--grid-2);
                border-radius:10px; padding:8px 10px; margin-top:10px;
            }
            .ch-input-msg {
                font-size:9.5px; color:var(--ink-2); background:#fff;
                border:1px solid var(--grid-2); border-radius:20px;
                padding:6px 14px; outline:0; width:100%;
            }
            .ch-input-msg:focus { border-color:var(--accent-bd); }

            .ch-btn-send {
                height:30px; padding:0 14px; border-radius:20px; border:1px solid var(--accent);
                background:var(--accent); color:#fff; font-size:8.5px; font-weight:700;
                display:inline-flex; align-items:center; justify-content:center; gap:5px;
                cursor:pointer; transition:all .15s; flex-shrink:0;
            }
            .ch-btn-send:hover:not(:disabled) { background:#2e5c43; border-color:#2e5c43; }
            .ch-btn-send:disabled { opacity:.6; cursor:not-allowed; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary   { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .ch-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:6px 10px; font-size:8.5px; margin-top:8px; }
            .ch-alert-ok  { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:6px 10px; font-size:8.5px; margin-top:8px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #chMsgDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #chMsgDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .ch-msg-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .ch-msg-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:chMsgDbgBlink 1s steps(2,start) infinite; }
            @keyframes chMsgDbgBlink { 50% { opacity:.35; } }
            .ch-msg-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #chMsgDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .ch-msg-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .ch-msg-dbg-controls { display:flex; gap:4px; }
            .ch-msg-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .ch-msg-dbg-btn:hover { background:#475569; }
            #chMsgDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .ch-msg-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .ch-msg-dbg-time { color:#64748b; margin-right:4px; }
            .ch-msg-dbg-info { color:#93c5fd; } .ch-msg-dbg-ok { color:#86efac; } .ch-msg-dbg-warn { color:#fde68a; } .ch-msg-dbg-err { color:#fca5a5; } .ch-msg-dbg-net { color:#c4b5fd; } .ch-msg-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- BARRA SUPERIOR: TÍTULO + ESTADO DE LA SELECCIONADA + SELECTOR REACTIVO DE RESERVA --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="val" style="font-size:11px;">
                    <i class="bi bi-chat-dots-fill text-success me-1"></i> Soporte por Reserva
                </span>
                @if($reserva)
                    <span class="pill pill-{{ $stGroup }}">Estado: {{ $stNombre }}</span>
                    <span class="sub">(<span id="chMsgCountLbl">{{ $mensajesReserva->count() }}</span> mensaje(s))</span>
                @endif
            </div>

            @if($misReservas->isNotEmpty())
                <div class="d-flex align-items-center gap-2">
                    <span class="sub d-none d-sm-inline">Reserva:</span>
                    <select id="chSelectReserva" class="ch-select-res">
                        @foreach($misReservas as $mr)
                            @php
                                $mrFi = $mr->fecha_inicio ? \Carbon\Carbon::parse($mr->fecha_inicio)->format('d/m/y') : '—';
                                $mrSt = optional($mr->status)->name ?? 'Pendiente';
                            @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    data-estado="{{ $mrSt }}"
                                    {{ ($reserva && $reserva->id === $mr->id) ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} ({{ $mrFi }}) · [{{ $mrSt }}]
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if(!$reserva)
            <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                <i class="bi bi-chat-square-dots text-muted d-block mb-2" style="font-size:24px;"></i>
                <span class="sub d-block mb-2">Aún no tienes reservas registradas para abrir un canal de soporte.</span>
                <button type="button" class="ch-btn-send js-ch-go-nueva">
                    <i class="bi bi-plus-circle"></i> Crear Reserva
                </button>
            </div>
        @else
            @php
                $fi = $reserva->fecha_inicio ? \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') : '—';
                $ff = $reserva->fecha_fin ? \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') : '—';
            @endphp

            {{-- FRANJA DE VALIDACIÓN DE LA RESERVA SELECCIONADA --}}
            <div class="ch-status-strip st-{{ $stGroup }}">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-building"></i>
                    <strong>{{ $inm->name ?? 'Inmueble' }}</strong>
                    <span>· #{{ $reserva->codigo_reserva }}</span>
                    <span>· Fechas: {{ $fi }} al {{ $ff }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="pill pill-{{ $stGroup }}">{{ $stNombre }}</span>
                </div>
            </div>

            {{-- VENTANA DE CONVERSACIÓN --}}
            <div class="ch-chat-viewport" id="cliChatBox">
                @forelse($mensajesReserva as $m)
                    @php
                        $idRem = $m->id_user_remitente ?? $m->id_user ?? $m->user_id ?? null;
                        $mio   = ($uid && (string) $idRem === (string) $uid);
                        $autor = $mio
                            ? 'Tú'
                            : (optional($m->remitente ?? $m->emisor ?? $m->user ?? $m->autor)->name ?? 'Equipo RSV');
                        $receptorNom = optional($m->tipoReceptor ?? $m->receptor)->nombre
                            ?? optional($m->tipoReceptor ?? $m->receptor)->name
                            ?? null;
                        $textoMsg = $m->contenido ?? $m->mensaje ?? '';
                        $fechaMsg = optional($m->created_at)->format('d/m/Y h:i A') ?? '';
                    @endphp
                    <div class="ch-bubble {{ $mio ? 'ch-bubble-mine' : 'ch-bubble-other' }}">
                        <div class="ch-bubble-meta">
                            <strong>
                                <i class="bi {{ $mio ? 'bi-person-check-fill' : 'bi-headset' }} me-1"></i>{{ $autor }}
                            </strong>
                            @if($receptorNom)
                                <span class="badge bg-light text-secondary border" style="font-size:6.5px;">{{ $receptorNom }}</span>
                            @endif
                        </div>
                        <div>{{ $textoMsg }}</div>
                        @if(!empty($m->url_archivo))
                            <div class="mt-1">
                                <a href="{{ $m->url_archivo }}" target="_blank" style="font-size:7.5px; color:var(--accent); font-weight:700;">
                                    <i class="bi bi-paperclip"></i> Ver archivo adjunto
                                </a>
                            </div>
                        @endif
                        <div class="ch-bubble-time">{{ $fechaMsg }}</div>
                    </div>
                @empty
                    <div class="text-center my-auto py-4 bg-white border rounded-3 mx-auto px-3 js-ch-empty-msg" style="max-width:85%; border-style:dashed !important;">
                        <i class="bi bi-headset text-secondary d-block mb-1" style="font-size:18px;"></i>
                        <span class="val d-block">Canal de Soporte Directo · Reserva #{{ $reserva->codigo_reserva }}</span>
                        <span class="sub">Escribe abajo cualquier consulta sobre tu pago, llegada o requerimientos especiales.</span>
                    </div>
                @endforelse
            </div>

            <div id="chMainAlert" class="d-none"></div>

            {{-- COMPOSITOR DE MENSAJES (100% AJAX) --}}
            @if($rutaSend)
                <form id="cliChatForm" action="{{ $rutaSend }}" method="POST" class="ch-composer"
                      data-reserva-id="{{ $reserva->id }}"
                      data-reserva-cod="{{ $reserva->codigo_reserva }}">
                    @csrf
                    <input type="hidden" name="_ajax" value="1">
                    <input type="hidden" name="id_rsv_reservas" value="{{ $reserva->id }}">
                    <input type="hidden" name="id_rsv_reserva" value="{{ $reserva->id }}">
                    <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                    <input type="hidden" name="id_user_remitente" value="{{ $uid }}">
                    <input type="hidden" name="id_user_destinatario" value="{{ $uid }}">
                    <input type="hidden" name="id_user" value="{{ $uid }}">
                    <input type="hidden" name="user_id" value="{{ $uid }}">
                    <input type="hidden" name="tipo_mensaje" value="texto">

                    <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
                        @if($receptoresList->isNotEmpty())
                            <select name="id_rsv_tipo_receptor" class="ch-select-res" style="max-width:150px;" title="Área destinataria">
                                @foreach($receptoresList as $tr)
                                    <option value="{{ $tr->id }}">{{ $tr->nombre ?? $tr->name ?? ('Área #' . $tr->id) }}</option>
                                @endforeach
                            </select>
                        @endif

                        <input type="text"
                               name="contenido"
                               id="chInputContenido"
                               class="ch-input-msg"
                               placeholder="Escribe tu mensaje para la reserva #{{ $reserva->codigo_reserva }}…"
                               required
                               maxlength="1000"
                               autocomplete="off"
                               oninput="this.form.querySelector('[name=mensaje]').value = this.value">
                        <input type="hidden" name="mensaje" value="">

                        <button type="submit" class="ch-btn-send" id="chBtnSend">
                            <i class="bi bi-send-fill" style="font-size:9px;"></i>
                            <span>Enviar</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="ch-alert-err">
                    <i class="bi bi-exclamation-triangle me-1"></i> Ruta de envío de mensajes no configurada.
                </div>
            @endif
        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL CH-MSG-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ChMsgDbg && window.ChMsgDbg.alive && window.ChMsgDbg.alive())
        ? window.ChMsgDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-chat-mensajes-cliente') || document.body;
                if (!$('chMsgDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'chMsgDbgToggle'; t.title = 'Debug Mensajes Soporte (CH-MSG-v2-DBG)';
                    t.innerHTML = '<span class="ch-msg-dbg-led" id="chMsgDbgLed"></span>DBG<span class="ch-msg-dbg-badge" id="chMsgDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('chMsgDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'chMsgDbgPanel';
                    p.innerHTML = '<div class="ch-msg-dbg-header" id="chMsgDbgHeader">' +
                        '<span>DEBUG · Mensajes Soporte · CH-MSG-v2-DBG</span>' +
                        '<span class="ch-msg-dbg-controls">' +
                        '<button type="button" class="ch-msg-dbg-btn" id="chMsgDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ch-msg-dbg-btn" id="chMsgDbgCopy">Copiar</button>' +
                        '<button type="button" class="ch-msg-dbg-btn" id="chMsgDbgClear">Limpiar</button>' +
                        '<button type="button" class="ch-msg-dbg-btn" id="chMsgDbgClose">✕</button>' +
                        '</span></div><div id="chMsgDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle', 'hiIniDbgToggle', 'hiEstDbgToggle', 'hiEndDbgToggle', 'chIniDbgToggle', 'chRecDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('chMsgDbgToggle'), p = $('chMsgDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('chMsgDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ch-msg-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="ch-msg-dbg-time">[' + hora() + ']</span><span class="ch-msg-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('chMsgDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('chMsgDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('chMsgDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('chMsgDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('chMsgDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('chMsgDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('chMsgDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('chMsgDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('chMsgDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Mensajes Soporte CH-MSG-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('chMsgDbgToggle'); }
            };
            window.ChMsgDbg = api;
            return api;
        })();

    DBG.ensure();
    var chmsgdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#chMsgDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#chMsgDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function scrollChatAlFinal() {
        var box = document.getElementById('cliChatBox');
        if (box) box.scrollTop = box.scrollHeight;
    }
    scrollChatAlFinal();

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE MENSAJES SIN RECARGAR PÁGINA ═══
    window.chMsgReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliChatRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'chat-mensajes';
        var resId    = reservaIdOpcional || root.getAttribute('data-reserva-id') || '';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (resId) urlObj.searchParams.set('reserva_id', resId);
        urlObj.searchParams.set('_t', Date.now());

        // Sincronizar URL del navegador sin recargar
        try {
            var navUrl = new URL(window.location.href);
            if (resId) navUrl.searchParams.set('reserva_id', resId);
            window.history.replaceState({}, document.title, navUrl.toString());
        } catch (e) {}

        var t0 = performance.now();
        if (motivo !== 'auto-poll') {
            chmsgdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');
        }

        return fetch(urlObj.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            cache: 'no-store'
        })
        .then(function (r) {
            return r.text().then(function (html) {
                return { ok: r.ok, status: r.status, html: html, ms: Math.round(performance.now() - t0) };
            });
        })
        .then(function (res) {
            if (!res.ok) {
                chmsgdbg('Error HTTP ' + res.status + ' al refrescar Mensajes (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliChatRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                scrollChatAlFinal();
                if (motivo !== 'auto-poll') {
                    var cod = nuevoRoot.getAttribute('data-reserva-cod') || '?';
                    var est = nuevoRoot.getAttribute('data-estado') || '?';
                    var cnt = nuevoRoot.getAttribute('data-mensajes') || '0';
                    chmsgdbg('Chat actualizado (' + res.ms + 'ms) · reserva=#' + cod + ' · estado=[' + est + '] · mensajes=' + cnt, 'ok');
                }
            }
        })
        .catch(function (err) {
            if (motivo !== 'auto-poll') {
                chmsgdbg('Excepción recargando Mensajes: ' + (err && err.message ? err.message : 'red'), 'err');
            }
        });
    };

    if (!window.__jsChatMensajesClienteV2Listo) {
        window.__jsChatMensajesClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#chMsgDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#chMsgDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#chMsgDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#chMsgDbgCopy'))    { e.stopPropagation(); DBG.copy(); chmsgdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#chMsgDbgRefresh')) {
                e.stopPropagation();
                window.chMsgReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            if (t.closest('.js-ch-go-nueva')) {
                chmsgdbg('Click: Ir a Nueva Reserva desde estado vacío', 'ev');
                var tabNueva = document.querySelector('[data-bs-target="#cli-nueva"]');
                if (tabNueva) tabNueva.click();
            }
        });

        // Selector de Reserva (100% AJAX sin recargar página)
        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || t.id !== 'chSelectReserva') return;

            var opt = t.options[t.selectedIndex];
            var cod = opt ? opt.getAttribute('data-cod') : t.value;
            var est = opt ? opt.getAttribute('data-estado') : '';
            chmsgdbg('Selector: Cambio a reserva #' + cod + ' (id=' + t.value + ' · estado=[' + est + '])', 'ev');

            window.chMsgReloadFragment(t.value, 'cambio-reserva');
        });

        // Envío AJAX de Mensaje de Soporte
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'cliChatForm') return;
            e.preventDefault();

            var btn = document.getElementById('chBtnSend');
            var inp = document.getElementById('chInputContenido');
            var alertBox = document.getElementById('chMainAlert');
            var cod = form.getAttribute('data-reserva-cod') || '?';
            var resId = form.getAttribute('data-reserva-id') || '';
            var txt = inp ? inp.value.trim() : '';
            if (!txt) return;

            var hiddenMsg = form.querySelector('input[name="mensaje"]');
            if (hiddenMsg) hiddenMsg.value = txt;

            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            var oldBtnHtml = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = 'Enviando…'; }

            var fd = new FormData(form);
            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            chmsgdbg('POST Mensaje (reserva #' + cod + ') → ' + form.getAttribute('action'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json, text/html'
                },
                body: fd
            })
            .then(function (r) {
                var ms = Math.round(performance.now() - t0);
                var ct = (r.headers.get('content-type') || '').toLowerCase();
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { ok: r.ok, status: r.status, ms: ms, json: j, isHtml: false }; });
                }
                return r.text().then(function () { return { ok: r.ok, status: r.status, ms: ms, json: { success: r.ok }, isHtml: true }; });
            })
            .then(function (res) {
                var j = res.json || {};
                if (res.ok && (res.isHtml || j.success !== false)) {
                    chmsgdbg('Mensaje enviado con éxito en #' + cod + ' · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');

                    // Inserción optimista inmediata en la ventana de chat
                    var chatBox = document.getElementById('cliChatBox');
                    if (chatBox) {
                        var emptyEl = chatBox.querySelector('.js-ch-empty-msg');
                        if (emptyEl) emptyEl.remove();

                        var b = document.createElement('div');
                        b.className = 'ch-bubble ch-bubble-mine';
                        b.innerHTML = '<div class="ch-bubble-meta"><strong><i class="bi bi-person-check-fill me-1"></i>Tú</strong></div>' +
                                      '<div class="js-msg-text"></div>' +
                                      '<div class="ch-bubble-time">Ahora</div>';
                        b.querySelector('.js-msg-text').textContent = txt;
                        chatBox.appendChild(b);
                        scrollChatAlFinal();
                    }

                    if (inp) inp.value = '';
                    var cntLbl = document.getElementById('chMsgCountLbl');
                    if (cntLbl) cntLbl.textContent = parseInt(cntLbl.textContent || '0', 10) + 1;
                    return;
                }

                chmsgdbg('Error al enviar mensaje (HTTP ' + res.status + ' · ' + res.ms + 'ms): ' + (j.message || 'Verifica los campos'), 'err');
                if (alertBox) {
                    alertBox.className = 'ch-alert-err';
                    alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> ' + (j.message || 'No se pudo enviar el mensaje.');
                }
            })
            .catch(function (err) {
                chmsgdbg('Fallo de red al enviar mensaje: ' + (err && err.message ? err.message : 'red'), 'err');
                if (alertBox) {
                    alertBox.className = 'ch-alert-err';
                    alertBox.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Error de conexión al enviar el mensaje.';
                }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldBtnHtml; }
            });
        });

        // Auto-refresh silencioso cada 15s solo si la pestaña de mensajes está visible y el usuario no está escribiendo
        setInterval(function () {
            if (document.hidden || document.querySelector('.modal.show')) return;
            var root = document.getElementById('cliChatRoot');
            if (!root || !root.offsetParent) return;
            var ae = document.activeElement;
            if (ae && root.contains(ae) && (ae.tagName === 'INPUT' || ae.tagName === 'TEXTAREA' || ae.tagName === 'SELECT')) return;
            window.chMsgReloadFragment(null, 'auto-poll');
        }, 15000);
    }

    var SRV = @json($chMsgDbgData);
    chmsgdbg('CH-MSG-v2-DBG activo · reserva=' + (SRV && SRV.reserva_cod ? '#' + SRV.reserva_cod : 'ninguna') +
             ' · estado=[' + (SRV && SRV.estado ? SRV.estado : '-') + ']' +
             ' · mensajes=' + (SRV ? SRV.mensajes_cnt : 0), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { chmsgdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
