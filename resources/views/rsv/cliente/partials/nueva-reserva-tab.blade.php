{{-- partials/nueva-reserva-tab.blade.php · Cliente · Dashboard y Contenedor Reactivo sin Reload (NR-v2-DBG) --}}
<div id="sheet-nueva-reserva">

    {{-- NR-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="nrDbgToggle" title="Debug Nueva Reserva (NR-v2-DBG)">
        <span class="nr-dbg-led" id="nrDbgLed"></span>DBG<span class="nr-dbg-badge" id="nrDbgBadge">0</span>
    </div>
    <div id="nrDbgPanel" role="dialog" aria-label="Panel de debug de Nueva Reserva">
        <div class="nr-dbg-header" id="nrDbgHeader">
            <span>DEBUG · Nueva Reserva · NR-v2-DBG</span>
            <span class="nr-dbg-controls">
                <button type="button" class="nr-dbg-btn" id="nrDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
                <button type="button" class="nr-dbg-btn" id="nrDbgCopy">Copiar</button>
                <button type="button" class="nr-dbg-btn" id="nrDbgClear">Limpiar</button>
                <button type="button" class="nr-dbg-btn" id="nrDbgClose">✕</button>
            </span>
        </div>
        <div id="nrDbgBody"></div>
    </div>

    @php
        $nrErrores = [];
        $rutaFragmento = '';
        foreach (['rsv.cliente.nueva-reserva.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $nrDbg = [
            'version'  => 'NR-v2-DBG',
            'vista'    => 'partials/nueva-reserva-tab.blade.php',
            'render'   => now()->format('H:i:s'),
            'frag_url' => $rutaFragmento ?: 'fallback location',
            'errores'  => $nrErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border tab-content" id="nuevaReservaSubContent"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="nueva-reserva"
         style="border-color:#f1f5f9 !important;">

        <style>
            /* ── VARIABLES Y ESTILOS BASE ── */
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }

            .reservas-soft .val { font-size:11px; color:var(--ink-2); font-weight:700; letter-spacing: -0.2px; }
            .reservas-soft .sub { font-size:9px; color:var(--ink-3); line-height: 1.5; }

            /* ── 1. HERO BANNER PREMIUM ── */
            .rsv-hero-pro {
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
            .rsv-hero-pro::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
                background-size: 20px 20px;
                pointer-events: none; z-index: 1; opacity: 0.5;
            }
            .rsv-hero-pro .hero-watermark {
                position: absolute; right: -5%; bottom: -30%; font-size: 18rem;
                opacity: 0.03; color: #ffffff; pointer-events: none; z-index: 0;
                transform: rotate(-10deg);
            }

            .rsv-hero-content { position: relative; z-index: 2; max-width: 700px; }
            .rsv-hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; font-size: 9px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase;}
            .rsv-hero-title { font-size: 26px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
            .rsv-hero-subtitle { font-size: 12px; opacity: 0.85; font-weight: 400; line-height: 1.6; margin-bottom: 24px;}

            .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

            /* ── BOTONES DEL HERO ── */
            .btn-hero-primary {
                background: #fff; color: #0f172a; border: none;
                font-size: 10px; font-weight: 800; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                box-shadow: 0 4px 12px rgba(255,255,255,0.1);
            }
            .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(255,255,255,0.2); background: #f8fafc; }

            .btn-hero-secondary {
                background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2);
                font-size: 10px; font-weight: 700; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s ease; backdrop-filter: blur(4px);
            }
            .btn-hero-secondary:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

            /* ── 2. MINI-TARJETAS ESTADÍSTICAS (3 COLUMNAS) ── */
            .rsv-stat-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%;
                transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
                cursor: pointer;
            }
            .rsv-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.04); }
            .rsv-stat-icon {
                width: 48px; height: 48px; border-radius: 12px; background: var(--head);
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; border: 1px solid var(--grid); flex-shrink: 0;
            }

            /* ── 3. TARJETAS DE PROCESO (2 COLUMNAS) ── */
            .rsv-process-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 24px; display: flex; flex-direction: column; align-items: flex-start;
                height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            }
            .rsv-process-card::before {
                content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
                background: var(--grid-2); transition: background 0.3s;
            }
            .rsv-process-card:hover { border-color: #cbd5e1; box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-3px);}
            .rsv-process-card:hover::before { background: var(--accent); }

            .rsv-step-number {
                width: 36px; height: 36px; background: var(--head); border: 1px solid var(--grid-2);
                border-radius: 8px; display: flex; align-items: center; justify-content: center;
                font-size: 14px; font-weight: 800; color: var(--accent); flex-shrink: 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02); margin-bottom: 16px;
            }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #nrDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #nrDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .nr-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .nr-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: nrDbgBlink 1s steps(2,start) infinite; }
            @keyframes nrDbgBlink { 50% { opacity: .35; } }
            .nr-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #nrDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .nr-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .nr-dbg-controls { display: flex; gap: 4px; }
            .nr-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .nr-dbg-btn:hover { background: #475569; }
            #nrDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .nr-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .nr-dbg-time { color: #64748b; margin-right: 4px; }
            .nr-dbg-info { color: #93c5fd; } .nr-dbg-ok { color: #86efac; }
            .nr-dbg-warn { color: #fde68a; } .nr-dbg-err { color: #fca5a5; }
            .nr-dbg-net  { color: #c4b5fd; } .nr-dbg-ev  { color: #f9a8d4; }
        </style>

        {{-- ========================================================= --}}
        {{-- SUB-TAB 1: INICIO (LA VITRINA)                            --}}
        {{-- ========================================================= --}}
        <div class="tab-pane fade show active" id="nr-sub-inicio" role="tabpanel">
            <div class="row g-3">

                {{-- [JERARQUÍA 1] HERO BANNER CENTRAL --}}
                <div class="col-12">
                    <div class="rsv-hero-pro">
                        <i class="bi bi-houses hero-watermark"></i>
                        <div class="rsv-hero-content">
                            <span class="rsv-hero-badge"><i class="bi bi-compass me-1"></i> Explora Destinos</span>
                            <h2 class="rsv-hero-title">Tu descanso, a un clic de distancia</h2>
                            <p class="rsv-hero-subtitle">Explora nuestro catálogo premium de apartamentos y casas de descanso. Hemos diseñado un proceso rápido y seguro para que disfrutes de tu estadía sin preocupaciones.</p>

                            <div class="hero-actions">
                                <button type="button" class="btn-hero-primary js-nr-nav-btn" data-target-tab="#nr-sub-buscador" data-nav-name="Explorar Propiedades (Hero)">
                                    <i class="bi bi-search"></i> Explorar Propiedades
                                </button>
                                <button type="button" class="btn-hero-secondary js-nr-nav-mis-reservas" data-nav-name="Mis Reservas (Hero)">
                                    <i class="bi bi-journal-bookmark"></i> Mis Reservas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- [JERARQUÍA 2] 3 MINI-TARJETAS ESTADÍSTICAS HORIZONTALES (CLICABLES) --}}
                <div class="col-12 col-md-4">
                    <div class="rsv-stat-card js-nr-nav-btn" data-target-tab="#nr-sub-buscador" data-nav-name="Stat Card · Destinos Exclusivos">
                        <div class="rsv-stat-icon" style="color: #3b82f6; background: #eff6ff; border-color: #dbeafe;"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Destinos Exclusivos</div>
                            <div class="sub mt-1">Santa Marta, Armenia y más.</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="rsv-stat-card js-nr-nav-btn" data-target-tab="#nr-sub-buscador" data-nav-name="Stat Card · Reserva Segura">
                        <div class="rsv-stat-icon" style="color: #10b981; background: #ecfdf5; border-color: #d1fae5;"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Reserva Segura</div>
                            <div class="sub mt-1">Bloqueo inmediato online.</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="rsv-stat-card js-nr-nav-btn" data-target-tab="#nr-sub-buscador" data-nav-name="Stat Card · Estadía Gratuita">
                        <div class="rsv-stat-icon" style="color: #8b5cf6; background: #f5f3ff; border-color: #ede9fe;"><i class="bi bi-house-heart"></i></div>
                        <div>
                            <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Estadía Gratuita</div>
                            <div class="sub mt-1">Pago exclusivo de aseo y adm.</div>
                        </div>
                    </div>
                </div>

                {{-- [JERARQUÍA 3] 2 TARJETAS DE PROCESO DETALLADO --}}
                <div class="col-12 col-md-6 mt-4">
                    <div class="rsv-process-card">
                        <div class="rsv-step-number">1</div>
                        <div class="val mb-2" style="font-size: 14px;">¿Cómo funciona la reserva?</div>
                        <div class="sub" style="font-size: 10px;">
                            Selecciona la propiedad, elige tus fechas en el calendario y la cantidad de huéspedes. Al finalizar el checkout, las fechas quedarán pre-reservadas a tu nombre automáticamente, evitando que alguien más las tome.
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-4">
                    <div class="rsv-process-card">
                        <div class="rsv-step-number">2</div>
                        <div class="val mb-2" style="font-size: 14px;">Confirmación y Pago</div>
                        <div class="sub" style="font-size: 10px;">
                            Dispones de <b style="color: var(--ink-2);">3 días hábiles</b> para realizar el pago correspondiente a la cuota de administración y cargar tu comprobante. Una vez validado, tu reserva quedará 100% confirmada para tu disfrute.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- SUB-TAB 2: BUSCADOR (LLAMADO AL ARCHIVO NIVEL 3)          --}}
        {{-- ========================================================= --}}
        <div class="tab-pane fade" id="nr-sub-buscador" role="tabpanel">
            @if(view()->exists('rsv.cliente.partials.nueva-reserva.buscador'))
                @include('rsv.cliente.partials.nueva-reserva.buscador')
            @else
                <div class="text-center py-5">
                    <i class="bi bi-hourglass-split mb-2 d-block" style="font-size:24px; color:var(--ink-4);"></i>
                    <span class="val" style="font-size: 12px;">Buscador (Próximamente)</span>
                </div>
            @endif
        </div>

        {{-- ========================================================= --}}
        {{-- SUB-TAB 3: CHECKOUT (LLAMADO AL ARCHIVO NIVEL 3)          --}}
        {{-- ========================================================= --}}
        <div class="tab-pane fade" id="nr-sub-checkout" role="tabpanel">
            @if(view()->exists('rsv.cliente.partials.nueva-reserva.checkout'))
                @include('rsv.cliente.partials.nueva-reserva.checkout')
            @else
                <div class="text-center py-5">
                    <i class="bi bi-hourglass-split mb-2 d-block" style="font-size:24px; color:var(--ink-4);"></i>
                    <span class="val" style="font-size: 12px;">Checkout (Próximamente)</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL NR-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.NrDbg && window.NrDbg.alive && window.NrDbg.alive())
        ? window.NrDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-nueva-reserva') || document.body;
                if (!$('nrDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'nrDbgToggle'; t.title = 'Debug Nueva Reserva (NR-v2-DBG)';
                    t.innerHTML = '<span class="nr-dbg-led" id="nrDbgLed"></span>DBG<span class="nr-dbg-badge" id="nrDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('nrDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'nrDbgPanel';
                    p.innerHTML = '<div class="nr-dbg-header" id="nrDbgHeader">' +
                        '<span>DEBUG · Nueva Reserva · NR-v2-DBG</span>' +
                        '<span class="nr-dbg-controls">' +
                        '<button type="button" class="nr-dbg-btn" id="nrDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="nr-dbg-btn" id="nrDbgCopy">Copiar</button>' +
                        '<button type="button" class="nr-dbg-btn" id="nrDbgClear">Limpiar</button>' +
                        '<button type="button" class="nr-dbg-btn" id="nrDbgClose">✕</button>' +
                        '</span></div><div id="nrDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('nrDbgToggle'), p = $('nrDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('nrDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'nr-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="nr-dbg-time">[' + hora() + ']</span><span class="nr-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('nrDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('nrDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('nrDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('nrDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('nrDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('nrDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('nrDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('nrDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('nrDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Nueva Reserva NR-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('nrDbgToggle'); }
            };
            window.NrDbg = api;
            return api;
        })();

    DBG.ensure();
    var nrdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#nrDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#nrDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO DE NUEVA RESERVA SIN RECARGAR PÁGINA ═══
    window.nrReloadFragment = function (motivo) {
        var root = document.getElementById('nuevaReservaSubContent');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'nueva-reserva';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        nrdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                nrdbg('Error HTTP ' + res.status + ' al refrescar Nueva Reserva (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('nuevaReservaSubContent');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                nrdbg('Fragmento de Nueva Reserva actualizado (' + res.ms + 'ms)', 'ok');
            } else {
                nrdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #nuevaReservaSubContent', 'warn');
            }
        })
        .catch(function (err) {
            nrdbg('Excepción recargando Nueva Reserva: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsNuevaReservaV2Listo) {
        window.__jsNuevaReservaV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#nrDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#nrDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#nrDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#nrDbgCopy'))    { e.stopPropagation(); DBG.copy(); nrdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#nrDbgRefresh')) {
                e.stopPropagation();
                window.nrReloadFragment('manual-btn-dbg');
                return;
            }

            // Navegación interna hacia el Buscador u otras sub-pestañas
            var navBtn = t.closest('.js-nr-nav-btn');
            if (navBtn) {
                e.preventDefault();
                var target = navBtn.getAttribute('data-target-tab') || '#nr-sub-buscador';
                var name   = navBtn.getAttribute('data-nav-name') || target;
                nrdbg('Click: Navegación interna → ' + name + ' (' + target + ')', 'ev');

                var tabEl = document.querySelector('[data-bs-target="' + target + '"]');
                if (tabEl) tabEl.click();
                return;
            }

            // Botón Mis Reservas (salto de Nivel 1)
            var navMis = t.closest('.js-nr-nav-mis-reservas');
            if (navMis) {
                e.preventDefault();
                nrdbg('Click: Salto rápido a pestaña Mis Reservas', 'ev');
                var tabMis = document.querySelector('[data-bs-target="#cli-reservas"]');
                if (tabMis) tabMis.click();
            }
        });

        // Trazabilidad de cambios de sub-pestaña Bootstrap
        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) DBG.ensure();
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target.indexOf('#nr-sub-') === 0) {
                nrdbg('Sub-pestaña de Nueva Reserva mostrada: ' + target, 'info');
            }
        });
    }

    var SRVDBG = @json($nrDbg);
    nrdbg('NR-v2-DBG activo · conectado a ' + (SRVDBG ? SRVDBG.frag_url : 'fragmento'), 'ok');
})();
</script>
