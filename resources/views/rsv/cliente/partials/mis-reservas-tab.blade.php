{{-- partials/mis-reservas-tab.blade.php · Cliente · INICIO DASHBOARD Reactivo sin Reload (MR-INI-v2-DBG) --}}
<div id="sheet-mis-reservas-inicio">

    {{-- MR-INI-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="mrIniDbgToggle" title="Debug Inicio Reservas (MR-INI-v2-DBG)">
        <span class="mr-ini-dbg-led" id="mrIniDbgLed"></span>DBG<span class="mr-ini-dbg-badge" id="mrIniDbgBadge">0</span>
    </div>
    <div id="mrIniDbgPanel" role="dialog" aria-label="Panel de debug Inicio Reservas">
        <div class="mr-ini-dbg-header" id="mrIniDbgHeader">
            <span>DEBUG · Inicio Reservas · MR-INI-v2-DBG</span>
            <span class="mr-ini-dbg-controls">
                <button type="button" class="mr-ini-dbg-btn" id="mrIniDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="mr-ini-dbg-btn" id="mrIniDbgCopy">Copiar</button>
                <button type="button" class="mr-ini-dbg-btn" id="mrIniDbgClear">Limpiar</button>
                <button type="button" class="mr-ini-dbg-btn" id="mrIniDbgClose">✕</button>
            </span>
        </div>
        <div id="mrIniDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $ridSel = request('reserva_id') ?? request('id_reserva');
        $mrErrores = [];

        // 1. Aprovechar las colecciones enviadas por ClientePortalController@misReservasFragmento
        $colTodas   = (isset($todas) && $todas instanceof \Illuminate\Support\Collection) ? $todas : collect();
        $colActivas = (isset($activas) && $activas instanceof \Illuminate\Support\Collection) ? $activas : collect();
        $colPasadas = (isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection) ? $pasadas : collect();

        // Fallback si se carga inicialmente vía @include sin pasar por misReservasFragmento()
        if ($colTodas->isEmpty() && $colActivas->isEmpty() && $colPasadas->isEmpty() && $uid) {
            try {
                $colTodas = \App\Models\Rsv\Reserva::with(['inmueble', 'status', 'huespedes'])
                    ->where('id_user', $uid)
                    ->orderByDesc('fecha_inicio')
                    ->limit(30)
                    ->get();

                $colActivas = $colTodas->filter(function ($r) {
                    $s = mb_strtolower(optional($r->status)->name ?? '');
                    return !str_contains($s, 'cancela') && !str_contains($s, 'anula') && !str_contains($s, 'final');
                })->values();

                $colPasadas = $colTodas->reject(function ($r) {
                    $s = mb_strtolower(optional($r->status)->name ?? '');
                    return !str_contains($s, 'cancela') && !str_contains($s, 'anula') && !str_contains($s, 'final');
                })->values();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.mis-reservas-ini: ' . $e->getMessage());
                $mrErrores[] = $e->getMessage();
            }
        } else {
            if ($colTodas->isEmpty()) {
                $colTodas = $colActivas->concat($colPasadas)->unique('id')->values();
            }
            try {
                if ($colTodas->isNotEmpty()) {
                    $colTodas->loadMissing(['inmueble', 'status', 'huespedes']);
                }
            } catch (\Throwable $e) {}
        }

        // Determinar la reserva destacada / próxima para vincular acciones rápidas
        $proximaReserva = null;
        if ($ridSel && is_numeric($ridSel)) {
            $proximaReserva = $colTodas->firstWhere('id', (int) $ridSel);
        }
        if (!$proximaReserva) {
            $proximaReserva = $colActivas->first() ?? $colTodas->first();
        }

        $proxId     = $proximaReserva ? $proximaReserva->id : null;
        $proxCod    = $proximaReserva ? $proximaReserva->codigo_reserva : null;
        $proxInm    = optional($proximaReserva?->inmueble)->name ?? null;
        $proxFi     = ($proximaReserva && $proximaReserva->fecha_inicio) ? \Carbon\Carbon::parse($proximaReserva->fecha_inicio)->format('d/m/Y') : null;
        $proxCupo   = ($proximaReserva && $proximaReserva->relationLoaded('huespedes')) ? $proximaReserva->huespedes->count() : 0;
        $proxCapMax = (int) (optional($proximaReserva?->inmueble)->capacidad_maxima ?? 6);

        // Detección de ruta del controlador de fragmentos (ClientePortalController@misReservasFragmento)
        $rutaFragmento = '';
        foreach (['rsv.cliente.mis-reservas.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $mrIniDbg = [
            'version'       => 'MR-INI-v2-DBG',
            'controlador'   => 'ClientePortalController@misReservasFragmento',
            'vista'         => 'partials/mis-reservas-tab.blade.php',
            'render'        => now()->format('H:i:s'),
            'todas'         => $colTodas->count(),
            'activas'       => $colActivas->count(),
            'pasadas'       => $colPasadas->count(),
            'proxima_id'    => $proxId,
            'proxima_cod'   => $proxCod,
            'proxima_inm'   => $proxInm,
            'frag_url'      => $rutaFragmento ?: 'fallback location',
            'errores'       => $mrErrores,
        ];
    @endphp

    <div id="cliMisResIniRoot"
         class="reservas-soft"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="mis-reservas"
         data-todas="{{ $colTodas->count() }}"
         data-activas="{{ $colActivas->count() }}"
         data-pasadas="{{ $colPasadas->count() }}"
         data-proxima-id="{{ $proxId ?? '' }}"
         data-proxima-cod="{{ $proxCod ?? '' }}">

        <style>
            /* ── VARIABLES Y ESTILOS BASE ── */
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }

            .reservas-soft .val { font-size: 11px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
            .reservas-soft .sub { font-size: 9px; color: var(--ink-3); line-height: 1.5; }

            /* ── 1. HERO BANNER PREMIUM DASHBOARD ── */
            .mr-hero-pro {
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
            .mr-hero-pro::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
                background-size: 20px 20px;
                pointer-events: none; z-index: 1; opacity: 0.5;
            }
            .mr-hero-pro .hero-watermark {
                position: absolute; right: -10%; bottom: -40%; font-size: 18rem;
                opacity: 0.03; color: #ffffff; pointer-events: none; z-index: 0;
                transform: rotate(-10deg);
            }

            .mr-hero-content { position: relative; z-index: 2; max-width: 700px; }
            .mr-hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; font-size: 9px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase;}
            .mr-hero-title { font-size: 26px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.2);}
            .mr-hero-subtitle { font-size: 12px; opacity: 0.85; font-weight: 400; line-height: 1.6; margin-bottom: 24px;}

            .mr-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

            .btn-mr-primary { background: #fff; color: #0f172a; border: none; font-size: 10px; font-weight: 800; padding: 12px 24px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 4px 12px rgba(255,255,255,0.1); }
            .btn-mr-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(255,255,255,0.2); background: #f8fafc; }

            .btn-mr-outline { background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 10px; font-weight: 700; padding: 12px 24px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; backdrop-filter: blur(4px); }
            .btn-mr-outline:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

            /* ── 2. MINI-TARJETAS ESTADÍSTICAS CLICABLES ── */
            .mr-stat-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%;
                transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
                cursor: pointer;
            }
            .mr-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.04); }
            .mr-stat-icon {
                width: 48px; height: 48px; border-radius: 12px; background: var(--head);
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; border: 1px solid var(--grid); flex-shrink: 0;
            }

            /* ── 3. TARJETAS DE ACCESO RÁPIDO (MÓDULOS) ── */
            .mr-module-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 24px; display: flex; flex-direction: column; align-items: flex-start;
                height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,0.02); cursor: pointer;
            }
            .mr-module-card::before {
                content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
                background: var(--grid-2); transition: background 0.3s;
            }
            .mr-module-card:hover { border-color: #cbd5e1; box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-3px);}
            .mr-module-card:hover::before { background: var(--accent); }

            .mr-module-icon { font-size: 24px; color: var(--accent); margin-bottom: 16px; }
            .mr-module-title { font-size: 14px; font-weight: 800; color: var(--ink-2); margin-bottom: 8px; letter-spacing: -0.3px;}
            .mr-module-desc { font-size: 10px; color: var(--ink-3); line-height: 1.5; margin-bottom: 24px; flex-grow: 1; }

            .mr-link-btn { color: var(--accent); font-size: 10px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; background: none; border: none; padding: 0; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;}
            .mr-link-btn:hover { color: #2e5c43; gap: 8px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #mrIniDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #mrIniDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .mr-ini-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .mr-ini-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: mrIniDbgBlink 1s steps(2,start) infinite; }
            @keyframes mrIniDbgBlink { 50% { opacity: .35; } }
            .mr-ini-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #mrIniDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .mr-ini-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .mr-ini-dbg-controls { display: flex; gap: 4px; }
            .mr-ini-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .mr-ini-dbg-btn:hover { background: #475569; }
            #mrIniDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .mr-ini-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .mr-ini-dbg-time { color: #64748b; margin-right: 4px; }
            .mr-ini-dbg-info { color: #93c5fd; } .mr-ini-dbg-ok { color: #86efac; }
            .mr-ini-dbg-warn { color: #fde68a; } .mr-ini-dbg-err { color: #fca5a5; }
            .mr-ini-dbg-net  { color: #c4b5fd; } .mr-ini-dbg-ev  { color: #f9a8d4; }
        </style>

        <div class="row g-3">

            {{-- [JERARQUÍA 1] HERO BANNER CENTRAL --}}
            <div class="col-12">
                <div class="mr-hero-pro">
                    <i class="bi bi-briefcase-fill hero-watermark"></i>

                    <div class="mr-hero-content">
                        <span class="mr-hero-badge">
                            <i class="bi bi-person-circle me-1"></i> Panel de Control
                            @if($proxCod) · Reserva Activa #{{ $proxCod }} @endif
                        </span>
                        <h2 class="mr-hero-title">Gestión Integral de tu Estadía</h2>
                        <p class="mr-hero-subtitle">
                            Supervisa el estado de tus operaciones, planifica tu próximo destino y mantén actualizada la lista de las personas que viajan contigo, todo en un solo lugar.
                        </p>

                        <div class="mr-hero-actions">
                            <button type="button" class="btn-mr-primary js-mr-ini-nav"
                                    data-target-tab="#mr-sub-activas"
                                    data-nav-name="Activas y Pasadas (Hero)"
                                    data-reserva-id="{{ $proxId }}">
                                <i class="bi bi-clock-history"></i> Ver Mi Historial ({{ $colTodas->count() }})
                            </button>
                            <button type="button" class="btn-mr-outline js-mr-ini-nav"
                                    data-target-tab="#mr-sub-itinerario"
                                    data-nav-name="Itinerario (Hero)"
                                    data-reserva-id="{{ $proxId }}">
                                <i class="bi bi-map"></i> Mi Próximo Viaje @if($proxFi) ({{ $proxFi }}) @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 2] 3 MINI-TARJETAS ESTADÍSTICAS HORIZONTALES CON MÉTRICAS REALES --}}
            <div class="col-12 col-md-4">
                <div class="mr-stat-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-activas"
                     data-nav-name="Stat Card · Reservas Activas"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-stat-icon" style="color: #10b981; background: #ecfdf5; border-color: #d1fae5;"><i class="bi bi-check2-all"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Estado ({{ $colTodas->count() }} total)</div>
                        <div class="val mt-1" style="font-size: 13px;">{{ $colActivas->count() }} Activa(s) · {{ $colPasadas->count() }} Pasada(s)</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="mr-stat-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-itinerario"
                     data-nav-name="Stat Card · Guías de Viaje"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-stat-icon" style="color: #3b82f6; background: #eff6ff; border-color: #dbeafe;"><i class="bi bi-signpost-2"></i></div>
                    <div style="min-width:0;">
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Próximo Destino</div>
                        <div class="val mt-1 text-truncate" style="font-size: 13px;">{{ $proxInm ?? 'Guías de Viaje' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="mr-stat-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-huespedes"
                     data-nav-name="Stat Card · Control de Ingreso"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-stat-icon" style="color: #8b5cf6; background: #f5f3ff; border-color: #ede9fe;"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Acompañantes</div>
                        <div class="val mt-1" style="font-size: 13px;">
                            @if($proximaReserva)
                                Cupo {{ $proxCupo }}/{{ $proxCapMax }} registrados
                            @else
                                Control de Ingreso
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 3] 3 TARJETAS DE MÓDULOS DE ACCESO RÁPIDO --}}
            <div class="col-12 col-md-4 mt-2">
                <div class="mr-module-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-activas"
                     data-nav-name="Módulo · Activas y Pasadas"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-module-icon"><i class="bi bi-calendar2-week"></i></div>
                    <div class="mr-module-title">Activas y Pasadas</div>
                    <div class="mr-module-desc">
                        Consulta las propiedades que has visitado, verifica pagos pendientes y revisa el estado de tus reservas activas o finalizadas.
                    </div>
                    <button type="button" class="mr-link-btn">
                        Entrar al Historial <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-2">
                <div class="mr-module-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-itinerario"
                     data-nav-name="Módulo · Itinerario Activo"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-module-icon"><i class="bi bi-geo-alt"></i></div>
                    <div class="mr-module-title">Itinerario Activo</div>
                    <div class="mr-module-desc">
                        Accede a la logística de tu próxima reserva: fechas, horarios de llegada, mapa de ubicación e instrucciones de acceso.
                    </div>
                    <button type="button" class="mr-link-btn">
                        Ver Guía de Viaje <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-2">
                <div class="mr-module-card js-mr-ini-nav"
                     data-target-tab="#mr-sub-huespedes"
                     data-nav-name="Módulo · Lista de Huéspedes"
                     data-reserva-id="{{ $proxId }}">
                    <div class="mr-module-icon"><i class="bi bi-people"></i></div>
                    <div class="mr-module-title">Lista de Huéspedes</div>
                    <div class="mr-module-desc">
                        Registra y mantén actualizada la información de tus acompañantes antes del viaje para garantizar un check-in ágil.
                    </div>
                    <button type="button" class="mr-link-btn">
                        Gestionar Huéspedes <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL MR-INI-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.MrIniDbg && window.MrIniDbg.alive && window.MrIniDbg.alive())
        ? window.MrIniDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-mis-reservas-inicio') || document.body;
                if (!$('mrIniDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'mrIniDbgToggle'; t.title = 'Debug Inicio Reservas (MR-INI-v2-DBG)';
                    t.innerHTML = '<span class="mr-ini-dbg-led" id="mrIniDbgLed"></span>DBG<span class="mr-ini-dbg-badge" id="mrIniDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('mrIniDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'mrIniDbgPanel';
                    p.innerHTML = '<div class="mr-ini-dbg-header" id="mrIniDbgHeader">' +
                        '<span>DEBUG · Inicio Reservas · MR-INI-v2-DBG</span>' +
                        '<span class="mr-ini-dbg-controls">' +
                        '<button type="button" class="mr-ini-dbg-btn" id="mrIniDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="mr-ini-dbg-btn" id="mrIniDbgCopy">Copiar</button>' +
                        '<button type="button" class="mr-ini-dbg-btn" id="mrIniDbgClear">Limpiar</button>' +
                        '<button type="button" class="mr-ini-dbg-btn" id="mrIniDbgClose">✕</button>' +
                        '</span></div><div id="mrIniDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('mrIniDbgToggle'), p = $('mrIniDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('mrIniDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'mr-ini-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="mr-ini-dbg-time">[' + hora() + ']</span><span class="mr-ini-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('mrIniDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('mrIniDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('mrIniDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('mrIniDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('mrIniDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('mrIniDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('mrIniDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('mrIniDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('mrIniDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Inicio Reservas MR-INI-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('mrIniDbgToggle'); }
            };
            window.MrIniDbg = api;
            return api;
        })();

    DBG.ensure();
    var mrdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#mrIniDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#mrIniDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO VÍA ClientePortalController@misReservasFragmento (SIN RECARGAR PÁGINA) ═══
    window.mrIniReloadFragment = function (motivo) {
        var root = document.getElementById('cliMisResIniRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'mis-reservas';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        mrdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                mrdbg('Error HTTP ' + res.status + ' al refrescar Inicio (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliMisResIniRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tod = nuevoRoot.getAttribute('data-todas') || '0';
                var act = nuevoRoot.getAttribute('data-activas') || '0';
                var pas = nuevoRoot.getAttribute('data-pasadas') || '0';
                var prx = nuevoRoot.getAttribute('data-proxima-cod') || 'ninguna';
                mrdbg('Fragmento actualizado (' + res.ms + 'ms) · todas=' + tod + ' (activas=' + act + ', pasadas=' + pas + ') · próxima=#' + prx, 'ok');
            } else {
                mrdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliMisResIniRoot', 'warn');
            }
        })
        .catch(function (err) {
            mrdbg('Excepción recargando Inicio: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // API pública compatible con las llamadas de las sub-pestañas
    window.mrTabApi = {
        logTab: function (tabName) {
            mrdbg('Navegación de sub-pestaña → ' + tabName, 'ev');
        }
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsMisReservasInicioV2Listo) {
        window.__jsMisReservasInicioV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#mrIniDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#mrIniDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#mrIniDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#mrIniDbgCopy'))    { e.stopPropagation(); DBG.copy(); mrdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#mrIniDbgRefresh')) {
                e.stopPropagation();
                window.mrIniReloadFragment('manual-btn-dbg');
                return;
            }

            // Navegación reactiva desde Hero, Stat Cards y Module Cards hacia las sub-pestañas
            var navEl = t.closest('.js-mr-ini-nav');
            if (navEl) {
                e.preventDefault();
                var targetTab = navEl.getAttribute('data-target-tab') || '';
                var navName   = navEl.getAttribute('data-nav-name') || targetTab;
                var resId     = navEl.getAttribute('data-reserva-id') || '';

                mrdbg('Click: ' + navName + ' → abrir ' + targetTab + (resId ? ' (reserva_id=' + resId + ')' : ''), 'ev');

                // Sincronizar en segundo plano el fragmento destino con la reserva activa
                if (targetTab === '#mr-sub-activas' && window.apReloadFragment) {
                    window.apReloadFragment(resId || null, 'nav-desde-inicio');
                } else if (targetTab === '#mr-sub-itinerario' && window.itReloadFragment && resId) {
                    window.itReloadFragment(resId, 'nav-desde-inicio');
                } else if (targetTab === '#mr-sub-huespedes' && window.huReloadFragment && resId) {
                    window.huReloadFragment(resId, 'nav-desde-inicio');
                }

                var btnTab = document.querySelector('[data-bs-target="' + targetTab + '"]');
                if (btnTab) {
                    btnTab.click();
                } else {
                    mrdbg('Aviso: No se encontró el botón de sub-pestaña para ' + targetTab, 'warn');
                }
            }
        });

        // Escuchar cambios de pestañas de Bootstrap en el ecosistema de Mis Reservas
        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) DBG.ensure();
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target.indexOf('#mr-sub-') === 0) {
                mrdbg('Tab activa mostrada: ' + target, 'info');
                if (target === '#mr-sub-inicio') {
                    window.mrIniReloadFragment('tab-focus-inicio');
                }
            }
        });
    }

    var SRVDBG = @json($mrIniDbg);
    mrdbg('MR-INI-v2-DBG activo · todas=' + (SRVDBG ? SRVDBG.todas : 0) +
          ' (activas=' + (SRVDBG ? SRVDBG.activas : 0) + ', pasadas=' + (SRVDBG ? SRVDBG.pasadas : 0) + ')' +
          ' · próxima=' + (SRVDBG && SRVDBG.proxima_cod ? '#' + SRVDBG.proxima_cod : 'ninguna'), 'ok');
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { mrdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
