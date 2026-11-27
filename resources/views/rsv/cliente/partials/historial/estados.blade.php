{{-- partials/historial/estados.blade.php · Cliente · Auditoría Reactiva de Estados sin Reload (HI-EST-v2-DBG) --}}
<div id="sheet-historial-estados-cliente">

    {{-- HI-EST-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="hiEstDbgToggle" title="Debug Historial Estados (HI-EST-v2-DBG)">
        <span class="hi-est-dbg-led" id="hiEstDbgLed"></span>DBG<span class="hi-est-dbg-badge" id="hiEstDbgBadge">0</span>
    </div>
    <div id="hiEstDbgPanel" role="dialog" aria-label="Panel de debug de Historial de Estados">
        <div class="hi-est-dbg-header" id="hiEstDbgHeader">
            <span>DEBUG · Historial Estados · HI-EST-v2-DBG</span>
            <span class="hi-est-dbg-controls">
                <button type="button" class="hi-est-dbg-btn" id="hiEstDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="hi-est-dbg-btn" id="hiEstDbgCopy">Copiar</button>
                <button type="button" class="hi-est-dbg-btn" id="hiEstDbgClear">Limpiar</button>
                <button type="button" class="hi-est-dbg-btn" id="hiEstDbgClose">✕</button>
            </span>
        </div>
        <div id="hiEstDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $ridFiltro = request('reserva_id') ?? request('id_reserva') ?? '';
        $heErrores = [];

        // 1. Aprovechar $historiales si viene inyectado desde ClientePortalController@historialEstadosFragmento
        $movs = (isset($historiales) && $historiales instanceof \Illuminate\Support\Collection) ? $historiales : collect();
        $misReservas = collect();

        try {
            if ($uid) {
                $misReservas = \App\Models\Rsv\Reserva::with(['inmueble:id,name,city', 'status'])
                    ->where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->get();
            }

            if ($movs->isEmpty() && $misReservas->isNotEmpty()) {
                $misIds = $misReservas->pluck('id')->all();
                $movs = \App\Models\Rsv\HistorialEstado::whereIn('id_rsv_reservas', $misIds)
                    ->orderByDesc('created_at')
                    ->limit(100)
                    ->get();
            }

            if ($movs->isNotEmpty()) {
                $relacionesDeseadas = [];
                foreach (['reserva.inmueble', 'reserva.status', 'estadoNuevo', 'estadoAnterior', 'statusNuevo', 'statusAnterior', 'user'] as $rel) {
                    if (method_exists(\App\Models\Rsv\HistorialEstado::class, explode('.', $rel)[0])) {
                        $relacionesDeseadas[] = $rel;
                    }
                }
                if (!empty($relacionesDeseadas)) {
                    $movs->loadMissing($relacionesDeseadas);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.histEst: ' . $e->getMessage());
            $heErrores[] = $e->getMessage();
        }

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.historial.estados.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'cancela') || str_contains($s, 'anula') || str_contains($s, 'rechaz') || str_contains($s, 'incumpl') || str_contains($s, 'no show')) return 'danger';
            if (str_contains($s, 'hosped') || str_contains($s, 'curso')) return 'success';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob')) return 'success';
            return 'secondary';
        };

        // Helper para extraer nombre de estado nuevo y anterior sin importar cómo se llame la relación en el modelo
        $resolverEstados = function ($h) {
            $nuevoObj = $h->estadoNuevo ?? $h->statusNuevo ?? $h->status ?? null;
            $antObj   = $h->estadoAnterior ?? $h->statusAnterior ?? null;

            $nuevoNombre = optional($nuevoObj)->name
                ?? optional($nuevoObj)->nombre
                ?? ($h->estado_nuevo ?? ($h->id_rsv_statuses_nuevo ? '#' . $h->id_rsv_statuses_nuevo : 'Actualizado'));

            $antNombre = optional($antObj)->name
                ?? optional($antObj)->nombre
                ?? ($h->estado_anterior ?? ($h->id_rsv_statuses_anterior ? '#' . $h->id_rsv_statuses_anterior : 'Inicial'));

            return [$antNombre, $nuevoNombre];
        };

        // Conteo por categoría de estado destino
        $conteos = [
            'all'     => $movs->count(),
            'success' => 0,
            'warning' => 0,
            'primary' => 0,
            'danger'  => 0,
        ];

        foreach ($movs as $itemH) {
            [, $nomNuevo] = $resolverEstados($itemH);
            $g = $pillSt($nomNuevo);
            if (isset($conteos[$g])) {
                $conteos[$g]++;
            }
        }

        // Filtros por ESTADO destino del movimiento (catálogo rsv_statuses, en orden de id), con su color
        $estadosRsv = collect();
        try {
            $estadosRsv = \App\Models\Rsv\Status::orderBy('id')->get(['id', 'name', 'color_hex']);
        } catch (\Throwable $e) {
            $heErrores[] = $e->getMessage();
        }
        $conteosEstado = $movs->countBy(fn ($x) => (string) ($x->id_rsv_statuses_nuevo ?? ''));

        $heDbgData = [
            'version'     => 'HI-EST-v2-DBG',
            'controlador' => 'ClientePortalController@historialEstadosFragmento',
            'vista'       => 'partials/historial/estados.blade.php',
            'movimientos' => $movs->count(),
            'reservas'    => $misReservas->count(),
            'conteos'     => $conteos,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $heErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliHistEstRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="historial-estados"
         data-total="{{ $movs->count() }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:13px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:11px; color:var(--ink-3); line-height:1.4; }

            /* ── BARRA DE FILTROS Y SELECTOR ── */
            .he-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .he-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .he-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .he-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .he-chip-count {
                font-size:10px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .he-filter-chip.active .he-chip-count { background:var(--accent); color:#fff; }
            /* Chips por estado: color del catálogo rsv_statuses (--st-col) */
            .he-st-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--st-col); margin-right:4px; vertical-align:middle; }
            .he-filter-chip.he-chip-estado.active { background:color-mix(in srgb, var(--st-col) 14%, #fff); color:color-mix(in srgb, var(--st-col) 70%, #000); border-color:color-mix(in srgb, var(--st-col) 40%, #fff); }
            .he-filter-chip.he-chip-estado.active .he-chip-count { background:color-mix(in srgb, var(--st-col) 75%, #000); color:#fff; }
            .he-filter-chip[data-filter="danger"].active { background:#fef2f2; color:#b91c1c; border-color:#fee2e2; }
            .he-filter-chip[data-filter="danger"].active .he-chip-count { background:#b91c1c; color:#fff; }
            .he-filter-chip[data-filter="warning"].active { background:#fffbeb; color:#b45309; border-color:#fef3c7; }
            .he-filter-chip[data-filter="warning"].active .he-chip-count { background:#b45309; color:#fff; }
            .he-filter-chip[data-filter="primary"].active { background:#eff6ff; color:#1d4ed8; border-color:#dbeafe; }
            .he-filter-chip[data-filter="primary"].active .he-chip-count { background:#1d4ed8; color:#fff; }

            .he-input-ctrl {
                font-size:11px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 9px; outline:0; transition:all .15s;
            }
            .he-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); }

            /* ── TARJETA / FILA DE AUDITORÍA DE ESTADO ── */
            .he-card {
                background:#fff; border:1px solid var(--grid); border-radius:8px;
                margin-bottom:6px; transition:all .15s ease; overflow:hidden;
            }
            .he-card:hover { border-color:var(--accent-bd); background:#fafdfc; }
            .he-row {
                padding:9px 12px; display:flex; align-items:center;
                justify-content:space-between; gap:10px; cursor:pointer;
            }
            .he-detail {
                display:none; padding:10px 14px; background:var(--head);
                border-top:1px dashed var(--grid-2); font-size:11px;
            }
            .he-card.open { border-color:var(--accent-bd); }
            .he-card.open .he-detail { display:block; }
            .he-card.open .he-chevron { transform:rotate(90deg); color:var(--accent); }
            .he-chevron { font-size:14.5px; color:var(--ink-4); transition:transform .15s; }

            .he-icon-box {
                width:32px; height:32px; border-radius:8px; display:flex;
                align-items:center; justify-content:center; font-size:12px;
                flex-shrink:0; border:1px solid;
            }
            .he-icon-box.st-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .he-icon-box.st-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .he-icon-box.st-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .he-icon-box.st-primary   { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .he-icon-box.st-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:10px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary   { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .he-btn-mini {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:10.5px; font-weight:700; padding:3px 9px; border-radius:5px;
                cursor:pointer; display:inline-flex; align-items:center; gap:4px;
                transition:all .15s; text-decoration:none;
            }
            .he-btn-mini:hover { background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #hiEstDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:10.5px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #hiEstDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .hi-est-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .hi-est-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:hiEstDbgBlink 1s steps(2,start) infinite; }
            @keyframes hiEstDbgBlink { 50% { opacity:.35; } }
            .hi-est-dbg-badge { display:none; color:#f87171; font-size:10.5px; font-weight:700; }
            #hiEstDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .hi-est-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:11.5px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .hi-est-dbg-controls { display:flex; gap:4px; }
            .hi-est-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:11.5px; padding:2px 7px; cursor:pointer; }
            .hi-est-dbg-btn:hover { background:#475569; }
            #hiEstDbgBody { overflow-y:auto; padding:6px 8px; font-size:12.5px; line-height:1.55; }
            .hi-est-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .hi-est-dbg-time { color:#64748b; margin-right:4px; }
            .hi-est-dbg-info { color:#93c5fd; } .hi-est-dbg-ok { color:#86efac; } .hi-est-dbg-warn { color:#fde68a; } .hi-est-dbg-err { color:#fca5a5; } .hi-est-dbg-net { color:#c4b5fd; } .hi-est-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA + CHIPS DE CATEGORÍA + FILTRO POR RESERVA Y BUSCADOR --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="he-filter-bar">
                <span class="val me-2" style="font-size:14.5px;">
                    <i class="bi bi-flag-fill text-success me-1"></i> Auditoría de Estados
                </span>
                <button type="button" class="he-filter-chip active" data-filter="all">
                    Todos <span class="he-chip-count" id="heCntAll">{{ $conteos['all'] }}</span>
                </button>
                @foreach($estadosRsv as $est)
                    @php $colEst = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $est->color_hex) ? $est->color_hex : '#64748b'; @endphp
                    <button type="button" class="he-filter-chip he-chip-estado" data-filter="st-{{ $est->id }}" style="--st-col: {{ $colEst }};">
                        <span class="he-st-dot"></span>{{ $est->name }}
                        <span class="he-chip-count" data-cnt-st="{{ $est->id }}">{{ $conteosEstado[(string) $est->id] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($misReservas->isNotEmpty())
                    <select id="heSelectReserva" class="he-input-ctrl" style="max-width:220px; cursor:pointer;">
                        <option value="">Todas las reservas ({{ $misReservas->count() }})</option>
                        @foreach($misReservas as $mr)
                            @php
                                $mrSt = optional($mr->status)->name ?? 'Pendiente';
                            @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    data-estado="{{ $mrSt }}"
                                    {{ (string) $ridFiltro === (string) $mr->id ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} [{{ $mrSt }}]
                            </option>
                        @endforeach
                    </select>
                @endif
                <input type="text" id="heSearchInput" class="he-input-ctrl" style="width:155px;" placeholder="Buscar estado o #cód…">
            </div>
        </div>

        {{-- LISTADO CRONOLÓGICO DE CAMBIOS DE ESTADO --}}
        <div id="heEstadosList">
            @forelse($movs as $h)
                @php
                    [$antNombre, $nuevoNombre] = $resolverEstados($h);
                    $grpNuevo = $pillSt($nuevoNombre);
                    $grpAnt   = $pillSt($antNombre);

                    $resObj     = $h->reserva;
                    $resId      = $resObj->id ?? $h->id_rsv_reservas ?? $h->reserva_id ?? null;
                    $resCod     = $resObj->codigo_reserva ?? ($resId ? '#' . $resId : 'S/C');
                    $inmNombre  = optional($resObj?->inmueble)->name ?? 'Reserva';
                    $estadoActReserva = optional($resObj?->status)->name ?? $nuevoNombre;
                    $grpActReserva    = $pillSt($estadoActReserva);

                    $autorNombre = optional($h->user)->name ?? 'Sistema RSV';
                    $comentario  = trim((string) ($h->comentario ?? $h->motivo ?? $h->observacion ?? ''));
                    $fechaFmt    = optional($h->created_at)->format('d/m/Y h:i A') ?? '—';
                    $haceTiempo  = $h->created_at ? $h->created_at->diffForHumans() : '';

                    $iconoBox = match($grpNuevo) {
                        'success' => 'bi-check2-circle',
                        'warning' => 'bi-hourglass-split',
                        'danger'  => 'bi-x-octagon',
                        'primary' => 'bi-flag-fill',
                        default   => 'bi-arrow-left-right',
                    };
                @endphp

                <div class="he-card"
                     id="heCard_{{ $h->id }}"
                     data-hist-id="{{ $h->id }}"
                     data-reserva-id="{{ $resId }}"
                     data-reserva-cod="{{ $resCod }}"
                     data-st-group="{{ $grpNuevo }}"
                     data-st-id="{{ $h->id_rsv_statuses_nuevo }}"
                     data-search="{{ mb_strtolower($resCod . ' ' . $inmNombre . ' ' . $antNombre . ' ' . $nuevoNombre . ' ' . $comentario . ' ' . $autorNombre) }}">

                    <div class="he-row js-he-toggle-row">
                        <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                            <div class="he-icon-box st-{{ $grpNuevo }}">
                                <i class="bi {{ $iconoBox }}"></i>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val text-truncate">{{ $inmNombre }}</span>
                                    <span class="sub">#{{ $resCod }}</span>
                                    <span class="pill pill-{{ $grpAnt }}">{{ $antNombre }}</span>
                                    <i class="bi bi-arrow-right text-muted" style="font-size:11.5px;"></i>
                                    <span class="pill pill-{{ $grpNuevo }}">{{ $nuevoNombre }}</span>
                                </div>
                                <div class="sub text-truncate mt-1">
                                    <i class="bi bi-clock me-1"></i>{{ $fechaFmt }}
                                    @if($haceTiempo) <span class="mx-1">·</span> {{ $haceTiempo }} @endif
                                    @if($comentario !== '')
                                        <span class="mx-1">·</span> <span class="fst-italic">"{{ \Illuminate\Support\Str::limit($comentario, 65) }}"</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="sub d-none d-md-inline">Estado actual:</span>
                            <span class="pill pill-{{ $grpActReserva }} d-none d-sm-inline-flex" title="Estado vigente de la reserva #{{ $resCod }}">
                                {{ $estadoActReserva }}
                            </span>
                            <i class="bi bi-chevron-right he-chevron"></i>
                        </div>
                    </div>

                    {{-- Panel desplegable con el detalle de auditoría y salto a la reserva --}}
                    <div class="he-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="mb-1">
                                    <span class="lbl me-1">Transición:</span>
                                    <span class="val">{{ $antNombre }} → {{ $nuevoNombre }}</span>
                                    <span class="mx-2 text-muted">|</span>
                                    <span class="lbl me-1">Estado vigente de la reserva:</span>
                                    <span class="pill pill-{{ $grpActReserva }}">{{ $estadoActReserva }}</span>
                                </div>
                                <div class="sub">
                                    <b>Registrado por:</b> {{ $autorNombre }} · <b>Fecha exacta:</b> {{ $fechaFmt }}
                                </div>
                                @if($comentario !== '')
                                    <div class="mt-2 p-2 bg-white border rounded sub" style="color:var(--ink-2);">
                                        <i class="bi bi-chat-left-quote text-success me-1"></i> <b>Observación:</b> {{ $comentario }}
                                    </div>
                                @endif
                            </div>

                            @if($resId)
                                <div class="d-flex gap-2">
                                    <button type="button"
                                            class="he-btn-mini js-he-ver-reserva"
                                            data-reserva-id="{{ $resId }}"
                                            data-reserva-cod="{{ $resCod }}">
                                        <i class="bi bi-box-arrow-up-right"></i> Ver en Mis Reservas
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                    <i class="bi bi-flag text-muted d-block mb-2" style="font-size:22px;"></i>
                    <span class="sub d-block">Sin movimientos de estado registrados todavía.</span>
                </div>
            @endforelse

            @if($movs->isNotEmpty())
                <div id="heEmptyFilterMsg" class="text-center py-4 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                    <span class="sub d-block">No hay cambios de estado que coincidan con el filtro seleccionado.</span>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL HI-EST-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.HiEstDbg && window.HiEstDbg.alive && window.HiEstDbg.alive())
        ? window.HiEstDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-historial-estados-cliente') || document.body;
                if (!$('hiEstDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'hiEstDbgToggle'; t.title = 'Debug Historial Estados (HI-EST-v2-DBG)';
                    t.innerHTML = '<span class="hi-est-dbg-led" id="hiEstDbgLed"></span>DBG<span class="hi-est-dbg-badge" id="hiEstDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('hiEstDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'hiEstDbgPanel';
                    p.innerHTML = '<div class="hi-est-dbg-header" id="hiEstDbgHeader">' +
                        '<span>DEBUG · Historial Estados · HI-EST-v2-DBG</span>' +
                        '<span class="hi-est-dbg-controls">' +
                        '<button type="button" class="hi-est-dbg-btn" id="hiEstDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="hi-est-dbg-btn" id="hiEstDbgCopy">Copiar</button>' +
                        '<button type="button" class="hi-est-dbg-btn" id="hiEstDbgClear">Limpiar</button>' +
                        '<button type="button" class="hi-est-dbg-btn" id="hiEstDbgClose">✕</button>' +
                        '</span></div><div id="hiEstDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle', 'hiIniDbgToggle', 'hiEndDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('hiEstDbgToggle'), p = $('hiEstDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('hiEstDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'hi-est-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="hi-est-dbg-time">[' + hora() + ']</span><span class="hi-est-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('hiEstDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('hiEstDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('hiEstDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('hiEstDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('hiEstDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('hiEstDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('hiEstDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('hiEstDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('hiEstDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Historial Estados HI-EST-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('hiEstDbgToggle'); }
            };
            window.HiEstDbg = api;
            return api;
        })();

    DBG.ensure();
    var hedbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#hiEstDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#hiEstDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function actualizarContadoresEstados() {
        var selRes = document.getElementById('heSelectReserva')?.value || '';
        var cards = document.querySelectorAll('#heEstadosList .he-card');
        var total = 0, porEstado = {};

        cards.forEach(function (c) {
            if (selRes && c.getAttribute('data-reserva-id') !== selRes) return;
            total++;
            var sid = c.getAttribute('data-st-id') || '';
            porEstado[sid] = (porEstado[sid] || 0) + 1;
        });

        var elAll = document.getElementById('heCntAll');
        if (elAll) elAll.textContent = total;
        document.querySelectorAll('#cliHistEstRoot [data-cnt-st]').forEach(function (el) {
            el.textContent = porEstado[el.getAttribute('data-cnt-st')] || 0;
        });
    }

    function aplicarFiltrosEstados(logAccion) {
        var chipActivo = document.querySelector('#cliHistEstRoot .he-filter-chip.active');
        var grupo  = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var selRes = document.getElementById('heSelectReserva')?.value || '';
        var q      = (document.getElementById('heSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#heEstadosList .he-card').forEach(function (card) {
            var matchRes   = !selRes || (card.getAttribute('data-reserva-id') === selRes);
            var matchGrupo = (grupo === 'all') || ('st-' + (card.getAttribute('data-st-id') || '') === grupo);
            var matchTexto = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar    = matchRes && matchGrupo && matchTexto;
            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('heEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            hedbg('Filtro aplicado · grupo="' + grupo + '" · reserva="' + (selRes || 'todas') + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO SIN RECARGAR PÁGINA ═══
    window.hiEstReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliHistEstRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'historial-estados';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (reservaIdOpcional) urlObj.searchParams.set('reserva_id', reservaIdOpcional);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        hedbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                hedbg('Error HTTP ' + res.status + ' al refrescar Historial Estados (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliHistEstRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                actualizarContadoresEstados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                hedbg('Historial de Estados actualizado (' + res.ms + 'ms) · registros=' + tot, 'ok');
            } else {
                hedbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliHistEstRoot', 'warn');
            }
        })
        .catch(function (err) {
            hedbg('Excepción recargando Historial Estados: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsHistEstadosClienteV2Listo) {
        window.__jsHistEstadosClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#hiEstDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#hiEstDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#hiEstDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#hiEstDbgCopy'))    { e.stopPropagation(); DBG.copy(); hedbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#hiEstDbgRefresh')) {
                e.stopPropagation();
                window.hiEstReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // Expandir/colapsar detalle de un cambio de estado
            var rowToggle = t.closest('.js-he-toggle-row');
            if (rowToggle) {
                var card = rowToggle.closest('.he-card');
                if (card) {
                    var abrir = !card.classList.contains('open');
                    card.classList.toggle('open');
                    hedbg('Click: ' + (abrir ? 'Expandir' : 'Colapsar') + ' auditoría #' +
                          (card.getAttribute('data-hist-id') || '?') + ' (reserva #' + (card.getAttribute('data-reserva-cod') || '?') + ')', 'ev');
                }
                return;
            }

            // Saltar directamente a la reserva en Mis Reservas -> Activas y Pasadas
            var btnVerRes = t.closest('.js-he-ver-reserva');
            if (btnVerRes) {
                e.preventDefault();
                var rid = btnVerRes.getAttribute('data-reserva-id') || '';
                var cod = btnVerRes.getAttribute('data-reserva-cod') || '';
                hedbg('Click: Ver reserva #' + cod + ' (id=' + rid + ') en Mis Reservas', 'ev');

                if (window.apReloadFragment && rid) {
                    window.apReloadFragment(rid, 'salto-desde-historial-estados');
                }
                var tabParent = document.querySelector('[data-bs-target="#cli-reservas"]');
                if (tabParent) tabParent.click();
                setTimeout(function () {
                    var tabChild = document.querySelector('[data-bs-target="#mr-sub-activas"]');
                    if (tabChild) tabChild.click();
                }, 120);
                return;
            }

            // Filtro por chip de estado
            var chip = t.closest('.he-filter-chip');
            if (chip && chip.closest('#cliHistEstRoot')) {
                document.querySelectorAll('#cliHistEstRoot .he-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosEstados(true);
            }
        });

        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || t.id !== 'heSelectReserva') return;
            var opt = t.options[t.selectedIndex];
            var cod = opt ? (opt.getAttribute('data-cod') || 'Todas') : 'Todas';
            var est = opt ? (opt.getAttribute('data-estado') || '') : '';
            hedbg('Selector Reserva: #' + cod + (est ? ' · estado=[' + est + ']' : ''), 'ev');
            actualizarContadoresEstados();
            aplicarFiltrosEstados(true);
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'heSearchInput') {
                aplicarFiltrosEstados(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosEstados(true);
                }, 350);
            }
        });
    }

    actualizarContadoresEstados();
    if (document.getElementById('heSelectReserva')?.value) {
        aplicarFiltrosEstados(false);
    }

    var SRV = @json($heDbgData);
    hedbg('HI-EST-v2-DBG activo · movimientos=' + (SRV ? SRV.movimientos : 0) +
          (SRV && SRV.conteos ? ' [conf=' + SRV.conteos.success + ', pend=' + SRV.conteos.warning + ', fin=' + SRV.conteos.primary + ', canc=' + SRV.conteos.danger + ']' : ''), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { hedbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
