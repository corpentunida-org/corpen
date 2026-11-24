{{-- partials/chat-tab.blade.php · Cliente · INICIO Soporte Reactivo sin Reload (CH-INI-v1-DBG) --}}
<div id="sheet-chat-inicio">

    {{-- CH-INI-v1-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="chIniDbgToggle" title="Debug Inicio Soporte (CH-INI-v1-DBG)">
        <span class="ch-ini-dbg-led" id="chIniDbgLed"></span>DBG<span class="ch-ini-dbg-badge" id="chIniDbgBadge">0</span>
    </div>
    <div id="chIniDbgPanel" role="dialog" aria-label="Panel de debug Inicio Soporte">
        <div class="ch-ini-dbg-header" id="chIniDbgHeader">
            <span>DEBUG · Inicio Soporte · CH-INI-v1-DBG</span>
            <span class="ch-ini-dbg-controls">
                <button type="button" class="ch-ini-dbg-btn" id="chIniDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="ch-ini-dbg-btn" id="chIniDbgCopy">Copiar</button>
                <button type="button" class="ch-ini-dbg-btn" id="chIniDbgClear">Limpiar</button>
                <button type="button" class="ch-ini-dbg-btn" id="chIniDbgClose">✕</button>
            </span>
        </div>
        <div id="chIniDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $chErrores = [];

        $colMensajes   = (isset($mensajes) && $mensajes instanceof \Illuminate\Support\Collection) ? $mensajes : collect();
        $colReceptores = (isset($tiposReceptor) && $tiposReceptor instanceof \Illuminate\Support\Collection) ? $tiposReceptor : collect();
        $colReservas   = (isset($misReservas) && $misReservas instanceof \Illuminate\Support\Collection) ? $misReservas : collect();

        // Fallback si se carga inicialmente vía @include desde portal.blade.php sin pasar por ClientePortalController@chatFragmento
        if ($colMensajes->isEmpty() && $colReceptores->isEmpty() && $uid) {
            try {
                $resList = \App\Models\Rsv\Reserva::where('id_user', $uid)->orderByDesc('created_at')->get();
                $idsRes  = $resList->pluck('id')->all();

                if ($colReservas->isEmpty()) {
                    $colReservas = $resList;
                }

                if (!empty($idsRes) && class_exists(\App\Models\Rsv\Mensaje::class)) {
                    $colMensajes = \App\Models\Rsv\Mensaje::whereIn('id_rsv_reservas', $idsRes)
                        ->orderByDesc('created_at')
                        ->take(100)
                        ->get();
                }

                if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                    $colReceptores = \App\Models\Rsv\TipoReceptor::all();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.chat-ini: ' . $e->getMessage());
                $chErrores[] = $e->getMessage();
            }
        }

        $totalMensajes   = $colMensajes->count();
        $totalReceptores = $colReceptores->count();
        $totalReservas   = $colReservas->count();

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.chat.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $chIniDbg = [
            'version'     => 'CH-INI-v1-DBG',
            'controlador' => 'ClientePortalController@chatFragmento',
            'vista'       => 'partials/chat-tab.blade.php',
            'render'      => now()->format('H:i:s'),
            'mensajes'    => $totalMensajes,
            'receptores'  => $totalReceptores,
            'reservas'    => $totalReservas,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $chErrores,
        ];
    @endphp

    <div id="cliChatIniRoot"
         class="reservas-soft"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="chat"
         data-mensajes="{{ $totalMensajes }}"
         data-receptores="{{ $totalReceptores }}"
         data-reservas="{{ $totalReservas }}">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }

            .reservas-soft .val { font-size: 11px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
            .reservas-soft .sub { font-size: 9px; color: var(--ink-3); line-height: 1.5; }

            /* ── HERO BANNER SOPORTE / CHAT ── */
            .ch-hero-pro {
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
            .ch-hero-pro::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
                background-size: 20px 20px;
                pointer-events: none; z-index: 1; opacity: 0.5;
            }
            .ch-hero-pro .hero-watermark {
                position: absolute; right: -5%; bottom: -30%; font-size: 18rem;
                opacity: 0.03; color: #ffffff; pointer-events: none; z-index: 0;
                transform: rotate(-10deg);
            }

            .ch-hero-content { position: relative; z-index: 2; max-width: 700px; }
            .ch-hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; font-size: 9px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase;}
            .ch-hero-title { font-size: 26px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
            .ch-hero-subtitle { font-size: 12px; opacity: 0.85; font-weight: 400; line-height: 1.6; margin-bottom: 24px;}

            .ch-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

            .btn-ch-primary {
                background: #fff; color: #0f172a; border: none;
                font-size: 10px; font-weight: 800; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                box-shadow: 0 4px 12px rgba(255,255,255,0.1);
            }
            .btn-ch-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(255,255,255,0.2); background: #f8fafc; }

            .btn-ch-outline {
                background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2);
                font-size: 10px; font-weight: 700; padding: 12px 24px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s ease; backdrop-filter: blur(4px);
            }
            .btn-ch-outline:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

            /* ── MINI-TARJETAS ESTADÍSTICAS ── */
            .ch-stat-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%;
                transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
                cursor: pointer;
            }
            .ch-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.04); }
            .ch-stat-icon {
                width: 48px; height: 48px; border-radius: 12px; background: var(--head);
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; border: 1px solid var(--grid); flex-shrink: 0;
            }

            /* ── TARJETAS DE MÓDULOS DE ACCESO RÁPIDO ── */
            .ch-module-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 24px; display: flex; flex-direction: column; align-items: flex-start;
                height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,0.02); cursor: pointer;
            }
            .ch-module-card::before {
                content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
                background: var(--grid-2); transition: background 0.3s;
            }
            .ch-module-card:hover { border-color: #cbd5e1; box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-3px);}
            .ch-module-card:hover::before { background: var(--accent); }

            .ch-module-icon { font-size: 24px; color: var(--accent); margin-bottom: 16px; }
            .ch-module-title { font-size: 14px; font-weight: 800; color: var(--ink-2); margin-bottom: 8px; letter-spacing: -0.3px;}
            .ch-module-desc { font-size: 10px; color: var(--ink-3); line-height: 1.5; margin-bottom: 24px; flex-grow: 1; }

            .ch-link-btn { color: var(--accent); font-size: 10px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; background: none; border: none; padding: 0; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;}
            .ch-link-btn:hover { color: #2e5c43; gap: 8px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #chIniDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #chIniDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .ch-ini-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .ch-ini-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: chIniDbgBlink 1s steps(2,start) infinite; }
            @keyframes chIniDbgBlink { 50% { opacity: .35; } }
            .ch-ini-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #chIniDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .ch-ini-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .ch-ini-dbg-controls { display: flex; gap: 4px; }
            .ch-ini-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .ch-ini-dbg-btn:hover { background: #475569; }
            #chIniDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .ch-ini-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .ch-ini-dbg-time { color: #64748b; margin-right: 4px; }
            .ch-ini-dbg-info { color: #93c5fd; } .ch-ini-dbg-ok { color: #86efac; }
            .ch-ini-dbg-warn { color: #fde68a; } .ch-ini-dbg-err { color: #fca5a5; }
            .ch-ini-dbg-net  { color: #c4b5fd; } .ch-ini-dbg-ev  { color: #f9a8d4; }
        </style>

        <div class="row g-3">

            {{-- [JERARQUÍA 1] HERO BANNER SOPORTE / CHAT --}}
            <div class="col-12">
                <div class="ch-hero-pro">
                    <i class="bi bi-chat-dots hero-watermark"></i>
                    <div class="ch-hero-content">
                        <span class="ch-hero-badge"><i class="bi bi-headset me-1"></i> Atención al Asociado</span>
                        <h2 class="ch-hero-title">Centro de Soporte y Mensajería</h2>
                        <p class="ch-hero-subtitle">Comunícate directamente con la administración y los encargados de cada inmueble para resolver inquietudes sobre pagos, horarios de llegada, entrega de llaves o novedades de tu estadía.</p>

                        <div class="ch-hero-actions">
                            <button type="button" class="btn-ch-primary js-ch-ini-nav" data-target-tab="#ch-sub-mensajes" data-nav-name="Ver Mensajes (Hero)">
                                <i class="bi bi-chat-dots"></i> Ir a Mensajes ({{ $totalMensajes }})
                            </button>
                            <button type="button" class="btn-ch-outline js-ch-ini-nav" data-target-tab="#ch-sub-receptores" data-nav-name="Ver Receptores (Hero)">
                                <i class="bi bi-person-badge"></i> Ver Receptores ({{ $totalReceptores }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 2] 2 MINI-TARJETAS ESTADÍSTICAS HORIZONTALES (CLICABLES) --}}
            <div class="col-12 col-md-6">
                <div class="ch-stat-card js-ch-ini-nav" data-target-tab="#ch-sub-mensajes" data-nav-name="Stat Card · Mensajes">
                    <div class="ch-stat-icon" style="color: #3b82f6; background: #eff6ff; border-color: #dbeafe;"><i class="bi bi-chat-left-text"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Bandeja de Conversaciones</div>
                        <div class="sub mt-1"><b>{{ $totalMensajes }}</b> mensaje(s) asociados a tus reservas.</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="ch-stat-card js-ch-ini-nav" data-target-tab="#ch-sub-receptores" data-nav-name="Stat Card · Receptores">
                    <div class="ch-stat-icon" style="color: #10b981; background: #ecfdf5; border-color: #d1fae5;"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Canales y Receptores</div>
                        <div class="sub mt-1"><b>{{ $totalReceptores }}</b> área(s) o tipo(s) de receptor habilitados.</div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 3] 2 TARJETAS DE MÓDULOS DE ACCESO RÁPIDO --}}
            <div class="col-12 col-md-6 mt-2">
                <div class="ch-module-card js-ch-ini-nav" data-target-tab="#ch-sub-mensajes" data-nav-name="Módulo · Mensajes">
                    <div class="ch-module-icon"><i class="bi bi-chat-square-quote"></i></div>
                    <div class="ch-module-title">Historial de Mensajes</div>
                    <div class="ch-module-desc">
                        Consulta el hilo completo de conversación por cada reserva, envía nuevas solicitudes al equipo de soporte y haz seguimiento en tiempo real.
                    </div>
                    <button type="button" class="ch-link-btn">
                        Abrir Conversaciones <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 mt-2">
                <div class="ch-module-card js-ch-ini-nav" data-target-tab="#ch-sub-receptores" data-nav-name="Módulo · Receptores">
                    <div class="ch-module-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="ch-module-title">Directorio de Receptores</div>
                    <div class="ch-module-desc">
                        Identifica a qué área dirigir tus consultas o calificaciones (Administración, Recepción, Encargado de Inmueble o Soporte Técnico).
                    </div>
                    <button type="button" class="ch-link-btn">
                        Consultar Receptores <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL CH-INI-v1-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ChIniDbg && window.ChIniDbg.alive && window.ChIniDbg.alive())
        ? window.ChIniDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-chat-inicio') || document.body;
                if (!$('chIniDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'chIniDbgToggle'; t.title = 'Debug Inicio Soporte (CH-INI-v1-DBG)';
                    t.innerHTML = '<span class="ch-ini-dbg-led" id="chIniDbgLed"></span>DBG<span class="ch-ini-dbg-badge" id="chIniDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('chIniDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'chIniDbgPanel';
                    p.innerHTML = '<div class="ch-ini-dbg-header" id="chIniDbgHeader">' +
                        '<span>DEBUG · Inicio Soporte · CH-INI-v1-DBG</span>' +
                        '<span class="ch-ini-dbg-controls">' +
                        '<button type="button" class="ch-ini-dbg-btn" id="chIniDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ch-ini-dbg-btn" id="chIniDbgCopy">Copiar</button>' +
                        '<button type="button" class="ch-ini-dbg-btn" id="chIniDbgClear">Limpiar</button>' +
                        '<button type="button" class="ch-ini-dbg-btn" id="chIniDbgClose">✕</button>' +
                        '</span></div><div id="chIniDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle', 'hiIniDbgToggle', 'hiEstDbgToggle', 'hiEndDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('chIniDbgToggle'), p = $('chIniDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('chIniDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ch-ini-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="ch-ini-dbg-time">[' + hora() + ']</span><span class="ch-ini-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('chIniDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('chIniDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('chIniDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('chIniDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('chIniDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('chIniDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('chIniDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('chIniDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('chIniDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Inicio Soporte CH-INI-v1-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('chIniDbgToggle'); }
            };
            window.ChIniDbg = api;
            return api;
        })();

    DBG.ensure();
    var chinidbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#chIniDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#chIniDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO DE INICIO SOPORTE SIN RECARGAR PÁGINA ═══
    window.chIniReloadFragment = function (motivo) {
        var root = document.getElementById('cliChatIniRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'chat';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        chinidbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                chinidbg('Error HTTP ' + res.status + ' al refrescar Inicio Soporte (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliChatIniRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var msg = nuevoRoot.getAttribute('data-mensajes') || '0';
                var rec = nuevoRoot.getAttribute('data-receptores') || '0';
                chinidbg('Fragmento de Inicio Soporte actualizado (' + res.ms + 'ms) · mensajes=' + msg + ' · receptores=' + rec, 'ok');
            } else {
                chinidbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliChatIniRoot', 'warn');
            }
        })
        .catch(function (err) {
            chinidbg('Excepción recargando Inicio Soporte: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsChatInicioListo) {
        window.__jsChatInicioListo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#chIniDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#chIniDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#chIniDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#chIniDbgCopy'))    { e.stopPropagation(); DBG.copy(); chinidbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#chIniDbgRefresh')) {
                e.stopPropagation();
                window.chIniReloadFragment('manual-btn-dbg');
                return;
            }

            // Navegación interactiva hacia las sub-pestañas de Soporte (Mensajes / Receptores)
            var navEl = t.closest('.js-ch-ini-nav');
            if (navEl) {
                e.preventDefault();
                var targetTab = navEl.getAttribute('data-target-tab') || '';
                var navName   = navEl.getAttribute('data-nav-name') || targetTab;

                chinidbg('Click: ' + navName + ' → abrir ' + targetTab, 'ev');

                var btnTab = document.querySelector('[data-bs-target="' + targetTab + '"]');
                if (btnTab) {
                    btnTab.click();
                } else {
                    chinidbg('Aviso: No se encontró el botón de sub-pestaña para ' + targetTab, 'warn');
                }
            }
        });

        // Escuchar cambios de pestañas Bootstrap en el ecosistema de Soporte
        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) DBG.ensure();
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target.indexOf('#ch-sub-') === 0) {
                chinidbg('Sub-pestaña de Soporte mostrada: ' + target, 'info');
                if (target === '#ch-sub-inicio') {
                    window.chIniReloadFragment('tab-focus-inicio');
                }
            }
        });
    }

    var SRVDBG = @json($chIniDbg);
    chinidbg('CH-INI-v1-DBG activo · mensajes=' + (SRVDBG ? SRVDBG.mensajes : 0) + ' · receptores=' + (SRVDBG ? SRVDBG.receptores : 0), 'ok');
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { chinidbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
