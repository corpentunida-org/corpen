{{-- partials/historial-tab.blade.php · Cliente · INICIO Historial Reactivo sin Reload (HI-INI-v1-DBG) --}}
<div id="sheet-historial-inicio">

    {{-- HI-INI-v1-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="hiIniDbgToggle" title="Debug Inicio Historial (HI-INI-v1-DBG)">
        <span class="hi-ini-dbg-led" id="hiIniDbgLed"></span>DBG<span class="hi-ini-dbg-badge" id="hiIniDbgBadge">0</span>
    </div>
    <div id="hiIniDbgPanel" role="dialog" aria-label="Panel de debug Inicio Historial">
        <div class="hi-ini-dbg-header" id="hiIniDbgHeader">
            <span>DEBUG · Inicio Historial · HI-INI-v1-DBG</span>
            <span class="hi-ini-dbg-controls">
                <button type="button" class="hi-ini-dbg-btn" id="hiIniDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="hi-ini-dbg-btn" id="hiIniDbgCopy">Copiar</button>
                <button type="button" class="hi-ini-dbg-btn" id="hiIniDbgClear">Limpiar</button>
                <button type="button" class="hi-ini-dbg-btn" id="hiIniDbgClose">✕</button>
            </span>
        </div>
        <div id="hiIniDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $hiErrores = [];

        $colHistoriales = (isset($historiales) && $historiales instanceof \Illuminate\Support\Collection) ? $historiales : collect();
        $colEndosos    = (isset($endosos) && $endosos instanceof \Illuminate\Support\Collection) ? $endosos : collect();

        // Fallback si se carga inicialmente vía @include sin pasar por el controlador principal
        if ($colHistoriales->isEmpty() && $colEndosos->isEmpty() && $uid) {
            try {
                $idsRes = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id')->all();
                if (!empty($idsRes)) {
                    if (class_exists(\App\Models\Rsv\HistorialEstado::class)) {
                        $colHistoriales = \App\Models\Rsv\HistorialEstado::whereIn('id_rsv_reservas', $idsRes)
                            ->orderByDesc('created_at')
                            ->take(50)
                            ->get();
                    }
                    if (class_exists(\App\Models\Rsv\HistorialEndoso::class)) {
                        $colEndosos = \App\Models\Rsv\HistorialEndoso::whereIn('id_rsv_reservas', $idsRes)
                            ->orderByDesc('created_at')
                            ->take(50)
                            ->get();
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.historial-ini: ' . $e->getMessage());
                $hiErrores[] = $e->getMessage();
            }
        }

        $totalMovimientos = $colHistoriales->count() + $colEndosos->count();

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.historial.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $hiIniDbg = [
            'version'     => 'HI-INI-v1-DBG',
            'controlador' => 'ClientePortalController@historialFragmento',
            'vista'       => 'partials/historial-tab.blade.php',
            'render'      => now()->format('H:i:s'),
            'estados'     => $colHistoriales->count(),
            'endosos'     => $colEndosos->count(),
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $hiErrores,
        ];
    @endphp

    <div id="cliHistorialIniRoot"
         class="reservas-soft"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="historial"
         data-total="{{ $totalMovimientos }}"
         data-estados="{{ $colHistoriales->count() }}"
         data-endosos="{{ $colEndosos->count() }}">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }

            .reservas-soft .val { font-size: 11px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
            .reservas-soft .sub { font-size: 9px; color: var(--ink-3); line-height: 1.5; }

            /* ── HERO BANNER HISTORIAL ── */
            .hi-hero-pro {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                border-radius: 12px;
                padding: 40px 30px;
                position: relative;
                overflow: hidden;
                color: #ffffff;
                box-shadow: 0 8px 30px rgba(15, 23, 42, 0.15);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
            }
            .hi-hero-pro::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
                background-size: 20px 20px;
                pointer-events: none; z-index: 1; opacity: 0.5;
            }
            .hi-hero-pro .hero-watermark {
                position: absolute; right: -5%; bottom: -30%; font-size: 18rem;
                opacity: 0.03; color: #ffffff; pointer-events: none; z-index: 0;
                transform: rotate(-10deg);
            }

            .hi-hero-content { position: relative; z-index: 2; max-width: 700px; }
            .hi-hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; font-size: 9px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase;}
            .hi-hero-title { font-size: 26px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
            .hi-hero-subtitle { font-size: 12px; opacity: 0.85; font-weight: 400; line-height: 1.6; margin-bottom: 24px;}

            .hi-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

            .btn-hi-primary {
                background: #fff; color: #0f172a; border: none;
                font-size: 10px; font-weight: 800; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                box-shadow: 0 4px 12px rgba(255,255,255,0.1);
            }
            .btn-hi-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(255,255,255,0.2); background: #f8fafc; }

            .btn-hi-outline {
                background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2);
                font-size: 10px; font-weight: 700; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s ease; backdrop-filter: blur(4px);
            }
            .btn-hi-outline:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

            /* ── MINI-TARJETAS ESTADÍSTICAS ── */
            .hi-stat-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%;
                transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
                cursor: pointer;
            }
            .hi-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.04); }
            .hi-stat-icon {
                width: 48px; height: 48px; border-radius: 12px; background: var(--head);
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; border: 1px solid var(--grid); flex-shrink: 0;
            }

            /* ── TARJETAS DE MÓDULOS DE ACCESO RÁPIDO ── */
            .hi-module-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 24px; display: flex; flex-direction: column; align-items: flex-start;
                height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,0.02); cursor: pointer;
            }
            .hi-module-card::before {
                content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
                background: var(--grid-2); transition: background 0.3s;
            }
            .hi-module-card:hover { border-color: #cbd5e1; box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-3px);}
            .hi-module-card:hover::before { background: var(--accent); }

            .hi-module-icon { font-size: 24px; color: var(--accent); margin-bottom: 16px; }
            .hi-module-title { font-size: 14px; font-weight: 800; color: var(--ink-2); margin-bottom: 8px; letter-spacing: -0.3px;}
            .hi-module-desc { font-size: 10px; color: var(--ink-3); line-height: 1.5; margin-bottom: 24px; flex-grow: 1; }

            .hi-link-btn { color: var(--accent); font-size: 10px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; background: none; border: none; padding: 0; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;}
            .hi-link-btn:hover { color: #2e5c43; gap: 8px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #hiIniDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #hiIniDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .hi-ini-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .hi-ini-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: hiIniDbgBlink 1s steps(2,start) infinite; }
            @keyframes hiIniDbgBlink { 50% { opacity: .35; } }
            .hi-ini-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #hiIniDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .hi-ini-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .hi-ini-dbg-controls { display: flex; gap: 4px; }
            .hi-ini-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .hi-ini-dbg-btn:hover { background: #475569; }
            #hiIniDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .hi-ini-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .hi-ini-dbg-time { color: #64748b; margin-right: 4px; }
            .hi-ini-dbg-info { color: #93c5fd; } .hi-ini-dbg-ok { color: #86efac; }
            .hi-ini-dbg-warn { color: #fde68a; } .hi-ini-dbg-err { color: #fca5a5; }
            .hi-ini-dbg-net  { color: #c4b5fd; } .hi-ini-dbg-ev  { color: #f9a8d4; }
        </style>

        <div class="row g-3">

            {{-- [JERARQUÍA 1] HERO BANNER HISTORIAL --}}
            <div class="col-12">
                <div class="hi-hero-pro">
                    <i class="bi bi-clock-history hero-watermark"></i>
                    <div class="hi-hero-content">
                        <span class="hi-hero-badge"><i class="bi bi-shield-check me-1"></i> Auditoría y Transparencia</span>
                        <h2 class="hi-hero-title">Registro de Actividad y Endosos</h2>
                        <p class="hi-hero-subtitle">Consulta de forma detallada todos los movimientos, cambios de estado, auditorías de reserva y cesiones de derechos (endosos) realizados en tu cuenta de asociado.</p>

                        <div class="hi-hero-actions">
                            <button type="button" class="btn-hi-primary js-hi-ini-nav" data-target-tab="#hi-sub-estados" data-nav-name="Ver Estados (Hero)">
                                <i class="bi bi-flag"></i> Ver Estados ({{ $colHistoriales->count() }})
                            </button>
                            <button type="button" class="btn-hi-outline js-hi-ini-nav" data-target-tab="#hi-sub-endosos" data-nav-name="Ver Endosos (Hero)">
                                <i class="bi bi-arrow-left-right"></i> Ver Endosos ({{ $colEndosos->count() }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 2] 2 MINI-TARJETAS ESTADÍSTICAS HORIZONTALES (CLICABLES) --}}
            <div class="col-12 col-md-6">
                <div class="hi-stat-card js-hi-ini-nav" data-target-tab="#hi-sub-estados" data-nav-name="Stat Card · Estados">
                    <div class="hi-stat-icon" style="color: #3b82f6; background: #eff6ff; border-color: #dbeafe;"><i class="bi bi-flag"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Historial de Estados</div>
                        <div class="sub mt-1"><b>{{ $colHistoriales->count() }}</b> registro(s) de cambios de estatus.</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="hi-stat-card js-hi-ini-nav" data-target-tab="#hi-sub-endosos" data-nav-name="Stat Card · Endosos">
                    <div class="hi-stat-icon" style="color: #10b981; background: #ecfdf5; border-color: #d1fae5;"><i class="bi bi-arrow-left-right"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Endosos y Cesiones</div>
                        <div class="sub mt-1"><b>{{ $colEndosos->count() }}</b> registro(s) de endosos asociados.</div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 3] 2 TARJETAS DE MÓDULOS DE ACCESO RÁPIDO --}}
            <div class="col-12 col-md-6 mt-2">
                <div class="hi-module-card js-hi-ini-nav" data-target-tab="#hi-sub-estados" data-nav-name="Módulo · Estados">
                    <div class="hi-module-icon"><i class="bi bi-list-check"></i></div>
                    <div class="hi-module-title">Auditoría de Estados</div>
                    <div class="hi-module-desc">
                        Revisa el ciclo de vida completo de cada una de tus reservas, desde su creación inicial hasta su aprobación, rechazo o finalización.
                    </div>
                    <button type="button" class="hi-link-btn">
                        Ver Auditoría de Estados <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 mt-2">
                <div class="hi-module-card js-hi-ini-nav" data-target-tab="#hi-sub-endosos" data-nav-name="Módulo · Endosos">
                    <div class="hi-module-icon"><i class="bi bi-people"></i></div>
                    <div class="hi-module-title">Gestión de Endosos</div>
                    <div class="hi-module-desc">
                        Consulta el historial de cesiones o traspasos de titularidad autorizados para el disfrute de las propiedades del sistema.
                    </div>
                    <button type="button" class="hi-link-btn">
                        Ver Historial de Endosos <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL HI-INI-v1-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.HiIniDbg && window.HiIniDbg.alive && window.HiIniDbg.alive())
        ? window.HiIniDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-historial-inicio') || document.body;
                if (!$('hiIniDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'hiIniDbgToggle'; t.title = 'Debug Inicio Historial (HI-INI-v1-DBG)';
                    t.innerHTML = '<span class="hi-ini-dbg-led" id="hiIniDbgLed"></span>DBG<span class="hi-ini-dbg-badge" id="hiIniDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('hiIniDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'hiIniDbgPanel';
                    p.innerHTML = '<div class="hi-ini-dbg-header" id="hiIniDbgHeader">' +
                        '<span>DEBUG · Inicio Historial · HI-INI-v1-DBG</span>' +
                        '<span class="hi-ini-dbg-controls">' +
                        '<button type="button" class="hi-ini-dbg-btn" id="hiIniDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="hi-ini-dbg-btn" id="hiIniDbgCopy">Copiar</button>' +
                        '<button type="button" class="hi-ini-dbg-btn" id="hiIniDbgClear">Limpiar</button>' +
                        '<button type="button" class="hi-ini-dbg-btn" id="hiIniDbgClose">✕</button>' +
                        '</span></div><div id="hiIniDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('hiIniDbgToggle'), p = $('hiIniDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('hiIniDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'hi-ini-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="hi-ini-dbg-time">[' + hora() + ']</span><span class="hi-ini-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('hiIniDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('hiIniDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('hiIniDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('hiIniDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('hiIniDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('hiIniDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('hiIniDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('hiIniDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('hiIniDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Inicio Historial HI-INI-v1-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('hiIniDbgToggle'); }
            };
            window.HiIniDbg = api;
            return api;
        })();

    DBG.ensure();
    var hiinidbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#hiIniDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#hiIniDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO DE HISTORIAL SIN RECARGAR PÁGINA ═══
    window.hiIniReloadFragment = function (motivo) {
        var root = document.getElementById('cliHistorialIniRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'historial';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        hiinidbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                hiinidbg('Error HTTP ' + res.status + ' al refrescar Inicio Historial (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliHistorialIniRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                hiinidbg('Fragmento de Inicio Historial actualizado (' + res.ms + 'ms) · total=' + tot, 'ok');
            } else {
                hiinidbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliHistorialIniRoot', 'warn');
            }
        })
        .catch(function (err) {
            hiinidbg('Excepción recargando Inicio Historial: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsHistorialInicioListo) {
        window.__jsHistorialInicioListo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#hiIniDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#hiIniDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#hiIniDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#hiIniDbgCopy'))    { e.stopPropagation(); DBG.copy(); hiinidbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#hiIniDbgRefresh')) {
                e.stopPropagation();
                window.hiIniReloadFragment('manual-btn-dbg');
                return;
            }

            // Navegación interactiva hacia las sub-pestañas de Historial (Estados / Endosos)
            var navEl = t.closest('.js-hi-ini-nav');
            if (navEl) {
                e.preventDefault();
                var targetTab = navEl.getAttribute('data-target-tab') || '';
                var navName   = navEl.getAttribute('data-nav-name') || targetTab;

                hiinidbg('Click: ' + navName + ' → abrir ' + targetTab, 'ev');

                var btnTab = document.querySelector('[data-bs-target="' + targetTab + '"]');
                if (btnTab) {
                    btnTab.click();
                } else {
                    hiinidbg('Aviso: No se encontró el botón de sub-pestaña para ' + targetTab, 'warn');
                }
            }
        });

        // Escuchar cambios de pestañas Bootstrap en el ecosistema de Historial
        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) DBG.ensure();
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target.indexOf('#hi-sub-') === 0) {
                hiinidbg('Sub-pestaña de Historial mostrada: ' + target, 'info');
                if (target === '#hi-sub-inicio') {
                    window.hiIniReloadFragment('tab-focus-inicio');
                }
            }
        });
    }

    var SRVDBG = @json($hiIniDbg);
    hiinidbg('HI-INI-v1-DBG activo · estados=' + (SRVDBG ? SRVDBG.estados : 0) + ' · endosos=' + (SRVDBG ? SRVDBG.endosos : 0), 'ok');
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { hiinidbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
