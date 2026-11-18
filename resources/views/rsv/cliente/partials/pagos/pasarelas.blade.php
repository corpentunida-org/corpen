{{-- partials/pagos/pasarelas.blade.php · Cliente · Directorio Reactivo de Pasarelas y Medios de Pago sin Reload (PG-PAS-v2-DBG) --}}
<div id="sheet-pagos-pasarelas-cliente">

    {{-- PG-PAS-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="pgPasDbgToggle" title="Debug Pasarelas de Pago (PG-PAS-v2-DBG)">
        <span class="pg-pas-dbg-led" id="pgPasDbgLed"></span>DBG<span class="pg-pas-dbg-badge" id="pgPasDbgBadge">0</span>
    </div>
    <div id="pgPasDbgPanel" role="dialog" aria-label="Panel de debug de Pasarelas de Pago">
        <div class="pg-pas-dbg-header" id="pgPasDbgHeader">
            <span>DEBUG · Pasarelas de Pago · PG-PAS-v2-DBG</span>
            <span class="pg-pas-dbg-controls">
                <button type="button" class="pg-pas-dbg-btn" id="pgPasDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="pg-pas-dbg-btn" id="pgPasDbgCopy">Copiar</button>
                <button type="button" class="pg-pas-dbg-btn" id="pgPasDbgClear">Limpiar</button>
                <button type="button" class="pg-pas-dbg-btn" id="pgPasDbgClose">✕</button>
            </span>
        </div>
        <div id="pgPasDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $pgPasErrores = [];

        // 1. Aprovechar $pasarelas si viene inyectada desde ClientePortalController@pagosPasarelasFragmento
        $listaPasarelas = (isset($pasarelas) && $pasarelas instanceof \Illuminate\Support\Collection)
            ? $pasarelas
            : collect();

        if ($listaPasarelas->isEmpty()) {
            try {
                if (class_exists(\App\Models\Rsv\Pasarela::class)) {
                    $modeloPas = new \App\Models\Rsv\Pasarela;
                    $tablaPas  = $modeloPas->getTable();
                    $colNom    = \Illuminate\Support\Facades\Schema::hasColumn($tablaPas, 'nombre')
                        ? 'nombre'
                        : (\Illuminate\Support\Facades\Schema::hasColumn($tablaPas, 'name') ? 'name' : 'id');
                    $listaPasarelas = \App\Models\Rsv\Pasarela::orderBy($colNom)->get();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.pagos.pasarelas: ' . $e->getMessage());
                $pgPasErrores[] = $e->getMessage();
            }
        }

        // 2. Conteo y monto de transacciones del usuario por cada pasarela
        $statsPorPasarela = [];
        try {
            if ($uid && class_exists(\App\Models\Rsv\TransaccionFinanciera::class) && class_exists(\App\Models\Rsv\Reserva::class)) {
                $idsRes = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id')->all();
                if (!empty($idsRes)) {
                    $tablaTx = (new \App\Models\Rsv\TransaccionFinanciera)->getTable();
                    $colFkPas = null;
                    foreach (['id_rsv_pasarelas', 'id_rsv_pasarela', 'pasarela_id'] as $cTry) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn($tablaTx, $cTry)) {
                            $colFkPas = $cTry;
                            break;
                        }
                    }
                    if ($colFkPas) {
                        $txUser = \App\Models\Rsv\TransaccionFinanciera::whereIn('id_rsv_reservas', $idsRes)->get();
                        foreach ($txUser as $txItem) {
                            $pid = $txItem->{$colFkPas} ?? null;
                            if (!$pid) continue;
                            if (!isset($statsPorPasarela[$pid])) {
                                $statsPorPasarela[$pid] = ['cnt' => 0, 'monto' => 0.0];
                            }
                            $statsPorPasarela[$pid]['cnt']++;
                            $statsPorPasarela[$pid]['monto'] += (float) ($txItem->monto ?? 0);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}

        // 3. Ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.pagos.pasarelas.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $resolverActiva = function ($p) {
            foreach (['activa', 'activo', 'active', 'habilitada', 'enabled', 'estado'] as $col) {
                if (isset($p->{$col})) {
                    $val = $p->{$col};
                    if (is_bool($val) || is_numeric($val)) return (bool) $val;
                    $str = mb_strtolower((string) $val);
                    return !str_contains($str, 'inactiv') && !str_contains($str, '0');
                }
            }
            return true;
        };

        $conteos = [
            'all'      => $listaPasarelas->count(),
            'activa'   => 0,
            'inactiva' => 0,
        ];

        foreach ($listaPasarelas as $pItem) {
            if ($resolverActiva($pItem)) {
                $conteos['activa']++;
            } else {
                $conteos['inactiva']++;
            }
        }

        $pgPasDbgData = [
            'version'     => 'PG-PAS-v2-DBG',
            'controlador' => 'ClientePortalController@pagosPasarelasFragmento',
            'vista'       => 'partials/pagos/pasarelas.blade.php',
            'pasarelas'   => $listaPasarelas->count(),
            'conteos'     => $conteos,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $pgPasErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliPasarelasRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="pagos-pasarelas"
         data-total="{{ $listaPasarelas->count() }}"
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
            .pp-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .pp-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .pp-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .pp-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .pp-chip-count {
                font-size:7.5px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .pp-filter-chip.active .pp-chip-count { background:var(--accent); color:#fff; }

            .pp-input-ctrl {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 10px; outline:0; width:175px; transition:all .15s;
            }
            .pp-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); width:210px; }

            /* ── TARJETA DE PASARELA / MEDIO DE PAGO ── */
            .pp-card {
                background:#fff; border:1px solid var(--grid-2); border-radius:9px;
                padding:12px 14px; height:100%; display:flex; flex-direction:column;
                justify-content:space-between; gap:10px; transition:all .18s ease;
                position:relative; overflow:hidden;
            }
            .pp-card:hover {
                border-color:var(--accent-bd); background:#fafdfc;
                transform:translateY(-2px); box-shadow:0 6px 14px rgba(15,23,42,0.04);
            }
            .pp-avatar {
                width:34px; height:34px; border-radius:8px; background:var(--accent-bg);
                border:1px solid var(--accent-bd); color:var(--accent);
                display:flex; align-items:center; justify-content:center;
                font-size:14px; flex-shrink:0;
            }
            .pp-avatar.is-inactive {
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

            .pp-meta-box {
                background:var(--head); border:1px solid var(--grid);
                border-radius:6px; padding:6px 9px; font-size:8px; color:var(--ink);
            }

            .pp-btn-action {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:8px; font-weight:700; padding:4px 10px; border-radius:6px;
                cursor:pointer; display:inline-flex; align-items:center; justify-content:center;
                gap:5px; transition:all .15s; text-decoration:none; width:100%;
            }
            .pp-btn-action:hover {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd);
            }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #pgPasDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #pgPasDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .pg-pas-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .pg-pas-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:pgPasDbgBlink 1s steps(2,start) infinite; }
            @keyframes pgPasDbgBlink { 50% { opacity:.35; } }
            .pg-pas-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #pgPasDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .pg-pas-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .pg-pas-dbg-controls { display:flex; gap:4px; }
            .pg-pas-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .pg-pas-dbg-btn:hover { background:#475569; }
            #pgPasDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .pg-pas-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .pg-pas-dbg-time { color:#64748b; margin-right:4px; }
            .pg-pas-dbg-info { color:#93c5fd; } .pg-pas-dbg-ok { color:#86efac; } .pg-pas-dbg-warn { color:#fde68a; } .pg-pas-dbg-err { color:#fca5a5; } .pg-pas-dbg-net { color:#c4b5fd; } .pg-pas-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA + FILTROS CON CONTEO REAL + BUSCADOR EN VIVO --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="pp-filter-bar">
                <span class="val me-2" style="font-size:11px;">
                    <i class="bi bi-credit-card-2-front-fill text-success me-1"></i> Pasarelas y Medios de Pago
                </span>
                <button type="button" class="pp-filter-chip active" data-filter="all">
                    Todas <span class="pp-chip-count" id="ppCntAll">{{ $conteos['all'] }}</span>
                </button>
                <button type="button" class="pp-filter-chip" data-filter="activa">
                    Autorizadas <span class="pp-chip-count" id="ppCntActiva">{{ $conteos['activa'] }}</span>
                </button>
                @if($conteos['inactiva'] > 0)
                    <button type="button" class="pp-filter-chip" data-filter="inactiva">
                        Inactivas <span class="pp-chip-count" id="ppCntInactiva">{{ $conteos['inactiva'] }}</span>
                    </button>
                @endif
            </div>

            <input type="text" id="ppSearchInput" class="pp-input-ctrl" placeholder="Buscar pasarela o medio…">
        </div>

        <div class="sub mb-3">
            Todos los pagos realizados a través de los canales autorizados quedan registrados con soporte verificable en tu expediente de reserva.
        </div>

        {{-- CUADRÍCULA DE PASARELAS --}}
        <div class="row g-2" id="ppPasarelasGrid">
            @forelse($listaPasarelas as $p)
                @php
                    $nombrePas = $p->nombre ?? $p->name ?? ('Pasarela #' . $p->id);
                    $descPas   = trim((string) ($p->descripcion ?? $p->description ?? $p->instrucciones ?? $p->detalle ?? ''));
                    $cuentaPas = trim((string) ($p->cuenta ?? $p->numero_cuenta ?? $p->convenio ?? $p->codigo ?? ''));
                    $colorPas  = trim((string) ($p->color ?? ''));
                    $esActiva  = $resolverActiva($p);
                    $stGrp     = $esActiva ? 'activa' : 'inactiva';

                    $statP     = $statsPorPasarela[$p->id] ?? ['cnt' => 0, 'monto' => 0.0];
                    $txCnt     = (int) $statP['cnt'];
                    $txMonto   = (float) $statP['monto'];

                    $nomLower  = mb_strtolower($nombrePas);
                    $iconoPas  = 'bi-credit-card';
                    if (str_contains($nomLower, 'pse') || str_contains($nomLower, 'banco') || str_contains($nomLower, 'transf') || str_contains($nomLower, 'consig')) {
                        $iconoPas = 'bi-bank';
                    } elseif (str_contains($nomLower, 'nequi') || str_contains($nomLower, 'davi') || str_contains($nomLower, 'billetera') || str_contains($nomLower, 'movil')) {
                        $iconoPas = 'bi-phone-fill';
                    } elseif (str_contains($nomLower, 'wompi') || str_contains($nomLower, 'epayco') || str_contains($nomLower, 'ecollect') || str_contains($nomLower, 'online')) {
                        $iconoPas = 'bi-globe2';
                    } elseif (str_contains($nomLower, 'efectivo') || str_contains($nomLower, 'caja') || str_contains($nomLower, 'sede')) {
                        $iconoPas = 'bi-cash-coin';
                    }

                    if ($descPas === '') {
                        $descPas = 'Canal oficial autorizado para el recaudo y validación de aportes de administración y aseo.';
                    }
                @endphp

                <div class="col-12 col-sm-6 col-lg-4 js-pp-item"
                     data-pas-id="{{ $p->id }}"
                     data-pas-name="{{ $nombrePas }}"
                     data-st-group="{{ $stGrp }}"
                     data-search="{{ mb_strtolower($nombrePas . ' ' . $descPas . ' ' . $cuentaPas) }}">
                    <div class="pp-card" @if($colorPas) style="border-top: 3px solid {{ $colorPas }};" @endif>
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                    <div class="pp-avatar {{ $esActiva ? '' : 'is-inactive' }}">
                                        <i class="bi {{ $iconoPas }}"></i>
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="val text-truncate">{{ $nombrePas }}</div>
                                        <div class="sub">
                                            <i class="bi bi-shield-check text-success me-1"></i>Medio verificado
                                        </div>
                                    </div>
                                </div>
                                <span class="pill {{ $esActiva ? 'pill-success' : 'pill-secondary' }}">
                                    {{ $esActiva ? 'Autorizada' : 'Inactiva' }}
                                </span>
                            </div>

                            <div class="sub mb-2" style="font-size:8.5px; color:var(--ink);">
                                {{ $descPas }}
                            </div>

                            @if($cuentaPas !== '')
                                <div class="pp-meta-box mb-2 d-flex justify-content-between align-items-center">
                                    <span><b class="text-uppercase" style="font-size:7px;">Ref / Cuenta:</b> {{ $cuentaPas }}</span>
                                    <button type="button"
                                            class="btn btn-link p-0 text-decoration-none js-pp-copy-ref"
                                            data-copy="{{ $cuentaPas }}"
                                            style="font-size:8px; color:var(--accent); font-weight:700;"
                                            title="Copiar referencia">
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                </div>
                            @endif

                            <div class="pp-meta-box d-flex justify-content-between align-items-center">
                                <span>Tus pagos en este canal:</span>
                                <b>{{ $txCnt }} ({{ '$' . number_format($txMonto, 0) }})</b>
                            </div>
                        </div>

                        <div class="pt-2 border-top" style="border-color:var(--grid) !important;">
                            <button type="button"
                                    class="pp-btn-action js-pp-go-transacciones"
                                    data-pas-id="{{ $p->id }}"
                                    data-pas-name="{{ $nombrePas }}">
                                <i class="bi bi-receipt"></i> Ver mis pagos con {{ \Illuminate\Support\Str::limit($nombrePas, 18) }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                        <i class="bi bi-credit-card text-muted d-block mb-2" style="font-size:22px;"></i>
                        <span class="sub d-block">Sin pasarelas de pago configuradas todavía.</span>
                    </div>
                </div>
            @endforelse
        </div>

        @if($listaPasarelas->isNotEmpty())
            <div id="ppEmptyFilterMsg" class="text-center py-4 mt-2 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                <span class="sub d-block">No hay pasarelas de pago que coincidan con tu búsqueda.</span>
            </div>
        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL PG-PAS-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.PgPasDbg && window.PgPasDbg.alive && window.PgPasDbg.alive())
        ? window.PgPasDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-pagos-pasarelas-cliente') || document.body;
                if (!$('pgPasDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'pgPasDbgToggle'; t.title = 'Debug Pasarelas de Pago (PG-PAS-v2-DBG)';
                    t.innerHTML = '<span class="pg-pas-dbg-led" id="pgPasDbgLed"></span>DBG<span class="pg-pas-dbg-badge" id="pgPasDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('pgPasDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'pgPasDbgPanel';
                    p.innerHTML = '<div class="pg-pas-dbg-header" id="pgPasDbgHeader">' +
                        '<span>DEBUG · Pasarelas de Pago · PG-PAS-v2-DBG</span>' +
                        '<span class="pg-pas-dbg-controls">' +
                        '<button type="button" class="pg-pas-dbg-btn" id="pgPasDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="pg-pas-dbg-btn" id="pgPasDbgCopy">Copiar</button>' +
                        '<button type="button" class="pg-pas-dbg-btn" id="pgPasDbgClear">Limpiar</button>' +
                        '<button type="button" class="pg-pas-dbg-btn" id="pgPasDbgClose">✕</button>' +
                        '</span></div><div id="pgPasDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = [
                    'psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle',
                    'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle',
                    'hiIniDbgToggle', 'hiEstDbgToggle', 'hiEndDbgToggle',
                    'chIniDbgToggle', 'chMsgDbgToggle', 'chRecDbgToggle',
                    'pgIniDbgToggle', 'pgTxDbgToggle', 'pgRevDbgToggle'
                ];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('pgPasDbgToggle'), p = $('pgPasDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('pgPasDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'pg-pas-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="pg-pas-dbg-time">[' + hora() + ']</span><span class="pg-pas-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('pgPasDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('pgPasDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('pgPasDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('pgPasDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('pgPasDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('pgPasDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('pgPasDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('pgPasDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('pgPasDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Pasarelas PG-PAS-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('pgPasDbgToggle'); }
            };
            window.PgPasDbg = api;
            return api;
        })();

    DBG.ensure();
    var pgpasdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#pgPasDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#pgPasDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function aplicarFiltrosPasarelas(logAccion) {
        var chipActivo = document.querySelector('#cliPasarelasRoot .pp-filter-chip.active');
        var grupo = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var q = (document.getElementById('ppSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#ppPasarelasGrid .js-pp-item').forEach(function (item) {
            var matchGrupo = (grupo === 'all') || (item.getAttribute('data-st-group') === grupo);
            var matchTexto = !q || (item.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar = matchGrupo && matchTexto;
            item.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('ppEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            pgpasdbg('Filtro aplicado · estado="' + grupo + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE PASARELAS SIN RECARGAR PÁGINA ═══
    window.pgPasReloadFragment = function (motivo) {
        var root = document.getElementById('cliPasarelasRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'pagos-pasarelas';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        pgpasdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                pgpasdbg('Error HTTP ' + res.status + ' al refrescar Pasarelas (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliPasarelasRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                pgpasdbg('Pasarelas actualizadas (' + res.ms + 'ms) · total=' + tot, 'ok');
            } else {
                pgpasdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliPasarelasRoot', 'warn');
            }
        })
        .catch(function (err) {
            pgpasdbg('Excepción recargando Pasarelas: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsPagosPasarelasClienteV2Listo) {
        window.__jsPagosPasarelasClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#pgPasDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#pgPasDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#pgPasDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#pgPasDbgCopy'))    { e.stopPropagation(); DBG.copy(); pgpasdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#pgPasDbgRefresh')) {
                e.stopPropagation();
                window.pgPasReloadFragment('manual-btn-dbg');
                return;
            }

            // Copiar número de cuenta / convenio al portapapeles
            var copyBtn = t.closest('.js-pp-copy-ref');
            if (copyBtn) {
                e.preventDefault();
                var valCopy = copyBtn.getAttribute('data-copy') || '';
                if (valCopy && navigator.clipboard) {
                    navigator.clipboard.writeText(valCopy);
                    var oldHtml = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="bi bi-check2"></i> ¡Copiado!';
                    setTimeout(function () { copyBtn.innerHTML = oldHtml; }, 1400);
                    pgpasdbg('Referencia copiada al portapapeles: ' + valCopy, 'ok');
                }
                return;
            }

            // Saltar a la sub-pestaña de Transacciones filtrando por esta pasarela
            var goTxBtn = t.closest('.js-pp-go-transacciones');
            if (goTxBtn) {
                e.preventDefault();
                var pasName = goTxBtn.getAttribute('data-pas-name') || '';
                pgpasdbg('Click: Ver transacciones de "' + pasName + '" → abrir #pg-sub-transacciones', 'ev');

                var tabTx = document.querySelector('[data-bs-target="#pg-sub-transacciones"]');
                if (tabTx) tabTx.click();

                setTimeout(function () {
                    var txSearch = document.getElementById('ptSearchInput');
                    if (txSearch && pasName) {
                        txSearch.value = pasName;
                        txSearch.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, 150);
                return;
            }

            // Filtro por chip
            var chip = t.closest('.pp-filter-chip');
            if (chip && chip.closest('#cliPasarelasRoot')) {
                document.querySelectorAll('#cliPasarelasRoot .pp-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosPasarelas(true);
            }
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'ppSearchInput') {
                aplicarFiltrosPasarelas(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosPasarelas(true);
                }, 350);
            }
        });
    }

    var SRV = @json($pgPasDbgData);
    pgpasdbg('PG-PAS-v2-DBG activo · pasarelas=' + (SRV ? SRV.pasarelas : 0) +
             (SRV && SRV.conteos ? ' [autorizadas=' + SRV.conteos.activa + ', inactivas=' + SRV.conteos.inactiva + ']' : ''), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { pgpasdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
