{{-- partials/chat/receptores.blade.php · Cliente · Directorio Reactivo de Receptores y Canales sin Reload (CH-REC-v2-DBG) --}}
<div id="sheet-chat-receptores-cliente">

    {{-- CH-REC-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="chRecDbgToggle" title="Debug Receptores Soporte (CH-REC-v2-DBG)">
        <span class="ch-rec-dbg-led" id="chRecDbgLed"></span>DBG<span class="ch-rec-dbg-badge" id="chRecDbgBadge">0</span>
    </div>
    <div id="chRecDbgPanel" role="dialog" aria-label="Panel de debug de Receptores Soporte">
        <div class="ch-rec-dbg-header" id="chRecDbgHeader">
            <span>DEBUG · Receptores Soporte · CH-REC-v2-DBG</span>
            <span class="ch-rec-dbg-controls">
                <button type="button" class="ch-rec-dbg-btn" id="chRecDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="ch-rec-dbg-btn" id="chRecDbgCopy">Copiar</button>
                <button type="button" class="ch-rec-dbg-btn" id="chRecDbgClear">Limpiar</button>
                <button type="button" class="ch-rec-dbg-btn" id="chRecDbgClose">✕</button>
            </span>
        </div>
        <div id="chRecDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $chRecErrores = [];

        // 1. Aprovechar $tiposReceptor enviado por ClientePortalController@chatReceptoresFragmento
        $tipos = (isset($tiposReceptor) && $tiposReceptor instanceof \Illuminate\Support\Collection)
            ? $tiposReceptor
            : collect();

        if ($tipos->isEmpty()) {
            try {
                if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                    $modeloTr = new \App\Models\Rsv\TipoReceptor;
                    $tablaTr  = $modeloTr->getTable();
                    $colNom   = \Illuminate\Support\Facades\Schema::hasColumn($tablaTr, 'nombre')
                        ? 'nombre'
                        : (\Illuminate\Support\Facades\Schema::hasColumn($tablaTr, 'name') ? 'name' : 'id');
                    $tipos = \App\Models\Rsv\TipoReceptor::orderBy($colNom)->get();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.chat.receptores: ' . $e->getMessage());
                $chRecErrores[] = $e->getMessage();
            }
        }

        // Conteo de mensajes enviados a cada tipo de receptor en las reservas del cliente (si la columna existe)
        $conteoPorReceptor = [];
        try {
            if ($uid && class_exists(\App\Models\Rsv\Mensaje::class) && class_exists(\App\Models\Rsv\Reserva::class)) {
                $idsRes = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id')->all();
                if (!empty($idsRes)) {
                    $tablaMsg = (new \App\Models\Rsv\Mensaje)->getTable();
                    $colFkRec = null;
                    foreach (['id_rsv_tipo_receptor', 'id_rsv_tipos_receptor', 'tipo_receptor_id'] as $cTry) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn($tablaMsg, $cTry)) {
                            $colFkRec = $cTry;
                            break;
                        }
                    }
                    if ($colFkRec) {
                        $conteoPorReceptor = \App\Models\Rsv\Mensaje::whereIn('id_rsv_reservas', $idsRes)
                            ->selectRaw($colFkRec . ' as rec_id, COUNT(*) as total')
                            ->groupBy($colFkRec)
                            ->pluck('total', 'rec_id')
                            ->all();
                    }
                }
            }
        } catch (\Throwable $e) {}

        // Ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.chat.receptores.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $resolverEstadoActivo = function ($t) {
            foreach (['active', 'activo', 'activa', 'enabled', 'estado'] as $col) {
                if (isset($t->{$col})) {
                    $val = $t->{$col};
                    if (is_bool($val) || is_numeric($val)) return (bool) $val;
                    $str = mb_strtolower((string) $val);
                    return !str_contains($str, 'inactiv') && !str_contains($str, '0');
                }
            }
            return true;
        };

        $conteos = [
            'all'      => $tipos->count(),
            'activo'   => 0,
            'inactivo' => 0,
        ];

        foreach ($tipos as $tItem) {
            if ($resolverEstadoActivo($tItem)) {
                $conteos['activo']++;
            } else {
                $conteos['inactivo']++;
            }
        }

        $chRecDbgData = [
            'version'     => 'CH-REC-v2-DBG',
            'controlador' => 'ClientePortalController@chatReceptoresFragmento',
            'vista'       => 'partials/chat/receptores.blade.php',
            'receptores'  => $tipos->count(),
            'conteos'     => $conteos,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $chRecErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliRecepRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="chat-receptores"
         data-total="{{ $tipos->count() }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:10px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.4; }

            /* ── BARRA DE FILTROS Y BUSCADOR ── */
            .cr-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .cr-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .cr-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .cr-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .cr-chip-count {
                font-size:7.5px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .cr-filter-chip.active .cr-chip-count { background:var(--accent); color:#fff; }

            .cr-input-ctrl {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 10px; outline:0; width:175px; transition:all .15s;
            }
            .cr-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); width:205px; }

            /* ── TARJETAS DE CANAL / RECEPTOR ── */
            .cr-card {
                background:#fff; border:1px solid var(--grid-2); border-radius:9px;
                padding:12px 14px; height:100%; display:flex; flex-direction:column;
                justify-content:space-between; gap:10px; transition:all .18s ease;
            }
            .cr-card:hover {
                border-color:var(--accent-bd); background:#fafdfc;
                transform:translateY(-2px); box-shadow:0 6px 14px rgba(15,23,42,0.04);
            }
            .cr-avatar {
                width:34px; height:34px; border-radius:8px; background:var(--accent-bg);
                border:1px solid var(--accent-bd); color:var(--accent);
                display:flex; align-items:center; justify-content:center;
                font-size:14px; flex-shrink:0;
            }
            .cr-avatar.is-inactive {
                background:var(--head); border-color:var(--grid-2); color:var(--ink-3);
            }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .cr-btn-action {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:8px; font-weight:700; padding:4px 10px; border-radius:6px;
                cursor:pointer; display:inline-flex; align-items:center; justify-content:center;
                gap:5px; transition:all .15s; text-decoration:none; width:100%;
            }
            .cr-btn-action:hover {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd);
            }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #chRecDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #chRecDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .ch-rec-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .ch-rec-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:chRecDbgBlink 1s steps(2,start) infinite; }
            @keyframes chRecDbgBlink { 50% { opacity:.35; } }
            .ch-rec-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #chRecDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .ch-rec-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .ch-rec-dbg-controls { display:flex; gap:4px; }
            .ch-rec-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .ch-rec-dbg-btn:hover { background:#475569; }
            #chRecDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .ch-rec-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .ch-rec-dbg-time { color:#64748b; margin-right:4px; }
            .ch-rec-dbg-info { color:#93c5fd; } .ch-rec-dbg-ok { color:#86efac; } .ch-rec-dbg-warn { color:#fde68a; } .ch-rec-dbg-err { color:#fca5a5; } .ch-rec-dbg-net { color:#c4b5fd; } .ch-rec-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CREAR TICKET (plataforma Soportes): disponible siempre, con o sin reserva --}}
        @php
            $tkReservas = collect();
            try {
                $tkReservas = \App\Models\Rsv\Reserva::with('inmueble:id,name')
                    ->where('id_user', $uid)->orderByDesc('fecha_inicio')->limit(30)
                    ->get(['id', 'codigo_reserva', 'fecha_inicio', 'id_rsv_catalogo_inmueble']);
            } catch (\Throwable $e) {}
        @endphp
        <div class="cr-ticket-card mb-3" id="crTicketCard">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="cr-avatar" style="background:#eff6ff; color:#1d4ed8;"><i class="bi bi-ticket-perforated"></i></div>
                    <div>
                        <div class="val">¿Necesitas ayuda? Crea un ticket de soporte</div>
                        <div class="sub">Llega a la mesa de ayuda de Corpentunida; te contactaremos por correo o teléfono.</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary js-cr-tk-toggle" style="font-size:11px;">
                    <i class="bi bi-plus-circle me-1"></i> Crear ticket ahora
                </button>
            </div>

            <form id="crTicketForm" class="d-none mt-3" enctype="multipart/form-data" action="{{ route('rsv.cliente.soporte.ticket') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-12 col-md-5">
                        <label class="form-label mb-1" style="font-size:11px; font-weight:700;">¿Sobre qué reserva?</label>
                        <select name="id_rsv_reservas" class="form-select form-select-sm" style="font-size:11.5px;">
                            <option value="">Consulta general (sin reserva)</option>
                            @foreach($tkReservas as $tr)
                                <option value="{{ $tr->id }}">#{{ $tr->codigo_reserva }} · {{ optional($tr->inmueble)->name ?? 'Alojamiento' }} · {{ optional($tr->fecha_inicio)->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="form-label mb-1" style="font-size:11px; font-weight:700;">Adjunto (opcional · PDF, JPG o PNG, máx. 10 MB)</label>
                        <input type="file" name="adjunto" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" style="font-size:11px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1" style="font-size:11px; font-weight:700;">¿Qué necesitas?</label>
                        <textarea name="descripcion" class="form-control form-control-sm" rows="4" minlength="10" maxlength="2000" required
                                  style="font-size:11.5px;" placeholder="Describe el problema o la solicitud con el mayor detalle posible."></textarea>
                    </div>
                </div>
                <div id="crTicketAlert" class="d-none mt-2"></div>
                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;"><i class="bi bi-send me-1"></i> Enviar ticket</button>
                    <button type="button" class="btn btn-sm btn-light js-cr-tk-toggle" style="font-size:11px;">Cancelar</button>
                </div>
            </form>
        </div>
        <style>
            .cr-ticket-card { border:1px solid #bfdbfe; background:#f8fbff; border-radius:10px; padding:14px 16px; }
        </style>
        <script>
        (function () {
            if (window.__crTicketListo) return;
            window.__crTicketListo = true;
            document.addEventListener('click', function (e) {
                var b = e.target && e.target.closest && e.target.closest('.js-cr-tk-toggle');
                if (!b) return;
                var f = document.getElementById('crTicketForm'); if (!f) return;
                var abrir = f.classList.contains('d-none');
                f.classList.toggle('d-none', !abrir);
                var al = document.getElementById('crTicketAlert'); if (al && abrir) al.className = 'd-none';
                if (abrir) f.querySelector('textarea').focus();
            });
            document.addEventListener('submit', function (e) {
                var f = e.target;
                if (!f || f.id !== 'crTicketForm') return;
                e.preventDefault();
                var btn = f.querySelector('button[type="submit"]'), old = btn.innerHTML;
                var al = document.getElementById('crTicketAlert');
                al.className = 'd-none';
                btn.disabled = true; btn.innerHTML = 'Enviando…';
                var tok = f.querySelector('input[name="_token"]');
                fetch(f.getAttribute('action'), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': tok ? tok.value : '' },
                    body: new FormData(f)
                })
                .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, j: j }; }); })
                .then(function (res) {
                    if (res.ok && res.j.success) {
                        al.className = 'alert alert-success py-2 mt-2 mb-0';
                        al.style.fontSize = '11.5px';
                        al.textContent = res.j.message;
                        f.reset();
                        return;
                    }
                    var errs = [];
                    if (res.j.errors) Object.keys(res.j.errors).forEach(function (k) { errs = errs.concat(res.j.errors[k]); });
                    al.className = 'alert alert-danger py-2 mt-2 mb-0';
                    al.style.fontSize = '11.5px';
                    al.textContent = errs.length ? errs.join(' ') : (res.j.message || 'No se pudo crear el ticket.');
                })
                .catch(function () { al.className = 'alert alert-danger py-2 mt-2 mb-0'; al.textContent = 'Error de conexión. Intenta de nuevo.'; })
                .finally(function () { btn.disabled = false; btn.innerHTML = old; });
            });
        })();
        </script>

        {{-- CABECERA + FILTROS CON CONTEO REAL + BUSCADOR EN VIVO --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="cr-filter-bar">
                <span class="val me-2" style="font-size:11px;">
                    <i class="bi bi-person-badge-fill text-success me-1"></i> Canales y Receptores de Atención
                </span>
                <button type="button" class="cr-filter-chip active" data-filter="all">
                    Todos <span class="cr-chip-count" id="crCntAll">{{ $conteos['all'] }}</span>
                </button>
                <button type="button" class="cr-filter-chip" data-filter="activo">
                    Habilitados <span class="cr-chip-count" id="crCntActivo">{{ $conteos['activo'] }}</span>
                </button>
                @if($conteos['inactivo'] > 0)
                    <button type="button" class="cr-filter-chip" data-filter="inactivo">
                        Inactivos <span class="cr-chip-count" id="crCntInactivo">{{ $conteos['inactivo'] }}</span>
                    </button>
                @endif
            </div>

            <input type="text" id="crSearchInput" class="cr-input-ctrl" placeholder="Buscar canal o área…">
        </div>

        <div class="sub mb-3">
            Selecciona el área o destinatario con el que deseas comunicarte para abrir directamente la ventana de conversación.
        </div>

        {{-- CUADRÍCULA DE RECEPTORES --}}
        <div class="row g-2" id="crReceptoresGrid">
            @forelse($tipos as $t)
                @php
                    $nombreRec = $t->nombre ?? $t->name ?? $t->titulo ?? ('Canal #' . $t->id);
                    $descRec   = trim((string) ($t->descripcion ?? $t->description ?? $t->detalle ?? ''));
                    $esActivo  = $resolverEstadoActivo($t);
                    $stGrp     = $esActivo ? 'activo' : 'inactivo';
                    $msgCnt    = (int) ($conteoPorReceptor[$t->id] ?? 0);

                    $nomLower  = mb_strtolower($nombreRec);
                    $iconoRec  = 'bi-headset';
                    if (str_contains($nomLower, 'admin') || str_contains($nomLower, 'cartera') || str_contains($nomLower, 'pago')) {
                        $iconoRec = 'bi-building-check';
                    } elseif (str_contains($nomLower, 'inmueble') || str_contains($nomLower, 'recep') || str_contains($nomLower, 'encarg')) {
                        $iconoRec = 'bi-house-gear';
                    } elseif (str_contains($nomLower, 'soport') || str_contains($nomLower, 'siste') || str_contains($nomLower, 'tecnic')) {
                        $iconoRec = 'bi-shield-check';
                    }

                    if ($descRec === '') {
                        $descRec = 'Canal oficial de atención y recepción de solicitudes o calificaciones para ' . $nombreRec . '.';
                    }
                @endphp

                <div class="col-12 col-sm-6 col-lg-4 js-cr-item"
                     data-rec-id="{{ $t->id }}"
                     data-rec-name="{{ $nombreRec }}"
                     data-st-group="{{ $stGrp }}"
                     data-search="{{ mb_strtolower($nombreRec . ' ' . $descRec) }}">
                    <div class="cr-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                    <div class="cr-avatar {{ $esActivo ? '' : 'is-inactive' }}">
                                        <i class="bi {{ $iconoRec }}"></i>
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="val text-truncate">{{ $nombreRec }}</div>
                                        <div class="sub">Canal #{{ $t->id }} @if($msgCnt > 0) · {{ $msgCnt }} msj(s) @endif</div>
                                    </div>
                                </div>
                                <span class="pill {{ $esActivo ? 'pill-success' : 'pill-secondary' }}">
                                    {{ $esActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            <div class="sub" style="font-size:8.5px; color:var(--ink);">
                                {{ $descRec }}
                            </div>
                        </div>

                        <div class="pt-2 border-top" style="border-color:var(--grid) !important;">
                            <button type="button"
                                    class="cr-btn-action js-cr-write-btn"
                                    data-rec-id="{{ $t->id }}"
                                    data-rec-name="{{ $nombreRec }}">
                                <i class="bi bi-chat-dots"></i> Escribir a {{ \Illuminate\Support\Str::limit($nombreRec, 22) }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                        <i class="bi bi-person-badge text-muted d-block mb-2" style="font-size:22px;"></i>
                        <span class="sub d-block">Sin canales de atención configurados todavía.</span>
                    </div>
                </div>
            @endforelse
        </div>

        @if($tipos->isNotEmpty())
            <div id="crEmptyFilterMsg" class="text-center py-4 mt-2 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                <span class="sub d-block">No hay canales de atención que coincidan con tu búsqueda.</span>
            </div>
        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL CH-REC-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ChRecDbg && window.ChRecDbg.alive && window.ChRecDbg.alive())
        ? window.ChRecDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-chat-receptores-cliente') || document.body;
                if (!$('chRecDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'chRecDbgToggle'; t.title = 'Debug Receptores Soporte (CH-REC-v2-DBG)';
                    t.innerHTML = '<span class="ch-rec-dbg-led" id="chRecDbgLed"></span>DBG<span class="ch-rec-dbg-badge" id="chRecDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('chRecDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'chRecDbgPanel';
                    p.innerHTML = '<div class="ch-rec-dbg-header" id="chRecDbgHeader">' +
                        '<span>DEBUG · Receptores Soporte · CH-REC-v2-DBG</span>' +
                        '<span class="ch-rec-dbg-controls">' +
                        '<button type="button" class="ch-rec-dbg-btn" id="chRecDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ch-rec-dbg-btn" id="chRecDbgCopy">Copiar</button>' +
                        '<button type="button" class="ch-rec-dbg-btn" id="chRecDbgClear">Limpiar</button>' +
                        '<button type="button" class="ch-rec-dbg-btn" id="chRecDbgClose">✕</button>' +
                        '</span></div><div id="chRecDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle', 'hiIniDbgToggle', 'hiEstDbgToggle', 'hiEndDbgToggle', 'chIniDbgToggle', 'chMsgDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('chRecDbgToggle'), p = $('chRecDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('chRecDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ch-rec-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="ch-rec-dbg-time">[' + hora() + ']</span><span class="ch-rec-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('chRecDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('chRecDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('chRecDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('chRecDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('chRecDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('chRecDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('chRecDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('chRecDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('chRecDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Receptores Soporte CH-REC-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('chRecDbgToggle'); }
            };
            window.ChRecDbg = api;
            return api;
        })();

    DBG.ensure();
    var chrecdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#chRecDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#chRecDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function aplicarFiltrosReceptores(logAccion) {
        var chipActivo = document.querySelector('#cliRecepRoot .cr-filter-chip.active');
        var grupo = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var q = (document.getElementById('crSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#crReceptoresGrid .js-cr-item').forEach(function (item) {
            var matchGrupo = (grupo === 'all') || (item.getAttribute('data-st-group') === grupo);
            var matchTexto = !q || (item.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar = matchGrupo && matchTexto;
            item.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('crEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            chrecdbg('Filtro aplicado · estado="' + grupo + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE RECEPTORES SIN RECARGAR PÁGINA ═══
    window.chRecReloadFragment = function (motivo) {
        var root = document.getElementById('cliRecepRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'chat-receptores';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        chrecdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                chrecdbg('Error HTTP ' + res.status + ' al refrescar Receptores (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliRecepRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                chrecdbg('Directorio de Receptores actualizado (' + res.ms + 'ms) · canales=' + tot, 'ok');
            } else {
                chrecdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliRecepRoot', 'warn');
            }
        })
        .catch(function (err) {
            chrecdbg('Excepción recargando Receptores: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsChatReceptoresClienteV2Listo) {
        window.__jsChatReceptoresClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#chRecDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#chRecDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#chRecDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#chRecDbgCopy'))    { e.stopPropagation(); DBG.copy(); chrecdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#chRecDbgRefresh')) {
                e.stopPropagation();
                window.chRecReloadFragment('manual-btn-dbg');
                return;
            }

            // Botón "Escribir a..." → Abre sub-pestaña Mensajes y preselecciona ese receptor en el formulario de chat
            var writeBtn = t.closest('.js-cr-write-btn');
            if (writeBtn) {
                e.preventDefault();
                var recId   = writeBtn.getAttribute('data-rec-id') || '';
                var recName = writeBtn.getAttribute('data-rec-name') || 'Canal';
                chrecdbg('Click: Escribir al canal "' + recName + '" (id=' + recId + ') → abrir #ch-sub-mensajes', 'ev');

                var tabMsg = document.querySelector('[data-bs-target="#ch-sub-mensajes"]');
                if (tabMsg) tabMsg.click();

                setTimeout(function () {
                    var selRec = document.querySelector('#cliChatForm select[name="id_rsv_tipo_receptor"]');
                    if (selRec && recId) {
                        selRec.value = recId;
                    }
                    var inpMsg = document.getElementById('chInputContenido');
                    if (inpMsg) inpMsg.focus();
                }, 150);
                return;
            }

            // Filtro por chip
            var chip = t.closest('.cr-filter-chip');
            if (chip && chip.closest('#cliRecepRoot')) {
                document.querySelectorAll('#cliRecepRoot .cr-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosReceptores(true);
            }
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'crSearchInput') {
                aplicarFiltrosReceptores(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosReceptores(true);
                }, 350);
            }
        });
    }

    var SRV = @json($chRecDbgData);
    chrecdbg('CH-REC-v2-DBG activo · canales=' + (SRV ? SRV.receptores : 0) +
             (SRV && SRV.conteos ? ' [activos=' + SRV.conteos.activo + ', inactivos=' + SRV.conteos.inactivo + ']' : ''), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { chrecdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
