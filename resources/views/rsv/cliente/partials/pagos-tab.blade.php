{{-- partials/pagos-tab.blade.php · Cliente · INICIO Ecosistema Financiero y Reseñas Reactivo sin Reload (PG-INI-v1-DBG) --}}
<div id="sheet-pagos-inicio">

    {{-- PG-INI-v1-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="pgIniDbgToggle" title="Debug Inicio Pagos (PG-INI-v1-DBG)">
        <span class="pg-ini-dbg-led" id="pgIniDbgLed"></span>DBG<span class="pg-ini-dbg-badge" id="pgIniDbgBadge">0</span>
    </div>
    <div id="pgIniDbgPanel" role="dialog" aria-label="Panel de debug Inicio Pagos">
        <div class="pg-ini-dbg-header" id="pgIniDbgHeader">
            <span>DEBUG · Inicio Pagos · PG-INI-v1-DBG</span>
            <span class="pg-ini-dbg-controls">
                <button type="button" class="pg-ini-dbg-btn" id="pgIniDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="pg-ini-dbg-btn" id="pgIniDbgCopy">Copiar</button>
                <button type="button" class="pg-ini-dbg-btn" id="pgIniDbgClear">Limpiar</button>
                <button type="button" class="pg-ini-dbg-btn" id="pgIniDbgClose">✕</button>
            </span>
        </div>
        <div id="pgIniDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $pgErrores = [];

        $colTransacciones = collect();
        if (isset($transacciones)) {
            if ($transacciones instanceof \Illuminate\Pagination\AbstractPaginator) {
                $colTransacciones = collect($transacciones->items());
            } elseif ($transacciones instanceof \Illuminate\Support\Collection) {
                $colTransacciones = $transacciones;
            }
        }

        $colPasarelas = (isset($pasarelas) && $pasarelas instanceof \Illuminate\Support\Collection) ? $pasarelas : collect();
        $colReviews   = (isset($reviews) && $reviews instanceof \Illuminate\Support\Collection) ? $reviews : collect();

        $montoPagado    = isset($resumen['pagado']) ? (float) $resumen['pagado'] : 0.0;
        $montoPendiente = isset($resumen['pendiente']) ? (float) $resumen['pendiente'] : 0.0;

        // Fallback si se carga inicialmente vía @include desde portal.blade.php sin pasar por ClientePortalController@pagosFragmento
        if ($colTransacciones->isEmpty() && $colPasarelas->isEmpty() && $uid) {
            try {
                $idsRes = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id')->all();

                if (!empty($idsRes) && class_exists(\App\Models\Rsv\TransaccionFinanciera::class)) {
                    $colTransacciones = \App\Models\Rsv\TransaccionFinanciera::whereIn('id_rsv_reservas', $idsRes)
                        ->orderByDesc('created_at')
                        ->take(100)
                        ->get();

                    foreach ($colTransacciones as $tx) {
                        $edo = mb_strtolower((string) ($tx->estado_pago ?? $tx->estado ?? ''));
                        $m   = (float) ($tx->monto ?? 0);
                        if (str_contains($edo, 'anulad') || str_contains($edo, 'cancel') || str_contains($edo, 'rechaz') || str_contains($edo, 'fallid')) {
                            continue;
                        }
                        if (str_contains($edo, 'complet') || str_contains($edo, 'aprob') || str_contains($edo, 'pagad') || str_contains($edo, 'ok')) {
                            $montoPagado += $m;
                        } else {
                            $montoPendiente += $m;
                        }
                    }
                }

                if (class_exists(\App\Models\Rsv\Pasarela::class)) {
                    $colPasarelas = \App\Models\Rsv\Pasarela::all();
                }

                if (class_exists(\App\Models\Rsv\Review::class)) {
                    $tablaRev = (new \App\Models\Rsv\Review)->getTable();
                    $colAutor = \Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user_autor')
                        ? 'id_user_autor'
                        : (\Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user') ? 'id_user' : 'user_id');
                    $colReviews = \App\Models\Rsv\Review::where($colAutor, $uid)->get();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.pagos-ini: ' . $e->getMessage());
                $pgErrores[] = $e->getMessage();
            }
        } elseif ($colReviews->isEmpty() && $uid && class_exists(\App\Models\Rsv\Review::class)) {
            try {
                $tablaRev = (new \App\Models\Rsv\Review)->getTable();
                $colAutor = \Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user_autor')
                    ? 'id_user_autor'
                    : (\Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user') ? 'id_user' : 'user_id');
                $colReviews = \App\Models\Rsv\Review::where($colAutor, $uid)->get();
            } catch (\Throwable $e) {}
        }

        $totalTransacciones = (isset($transacciones) && $transacciones instanceof \Illuminate\Pagination\LengthAwarePaginator)
            ? $transacciones->total()
            : $colTransacciones->count();
        $totalPasarelas = $colPasarelas->count();
        $totalReviews   = $colReviews->count();

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.pagos.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $pgIniDbg = [
            'version'       => 'PG-INI-v1-DBG',
            'controlador'   => 'ClientePortalController@pagosFragmento',
            'vista'         => 'partials/pagos-tab.blade.php',
            'render'        => now()->format('H:i:s'),
            'transacciones' => $totalTransacciones,
            'pasarelas'     => $totalPasarelas,
            'reviews'       => $totalReviews,
            'pagado'        => $montoPagado,
            'pendiente'     => $montoPendiente,
            'frag_url'      => $rutaFragmento ?: 'fallback location',
            'errores'       => $pgErrores,
        ];
    @endphp

    <div id="cliPagosIniRoot"
         class="reservas-soft"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="pagos"
         data-transacciones="{{ $totalTransacciones }}"
         data-pasarelas="{{ $totalPasarelas }}"
         data-reviews="{{ $totalReviews }}"
         data-pagado="{{ $montoPagado }}">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }

            .reservas-soft .val { font-size: 11px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
            .reservas-soft .sub { font-size: 9px; color: var(--ink-3); line-height: 1.5; }

            /* ── HERO BANNER PAGOS Y RESEÑAS ── */
            .pg-hero-pro {
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
            .pg-hero-pro::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
                background-size: 20px 20px;
                pointer-events: none; z-index: 1; opacity: 0.5;
            }
            .pg-hero-pro .hero-watermark {
                position: absolute; right: -5%; bottom: -30%; font-size: 18rem;
                opacity: 0.03; color: #ffffff; pointer-events: none; z-index: 0;
                transform: rotate(-10deg);
            }

            .pg-hero-content { position: relative; z-index: 2; max-width: 720px; }
            .pg-hero-badge { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; font-size: 9px; font-weight: 700; padding: 4px 10px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase;}
            .pg-hero-title { font-size: 26px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
            .pg-hero-subtitle { font-size: 12px; opacity: 0.85; font-weight: 400; line-height: 1.6; margin-bottom: 24px;}

            .pg-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }

            .btn-pg-primary {
                background: #fff; color: #0f172a; border: none;
                font-size: 10px; font-weight: 800; padding: 12px 22px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                box-shadow: 0 4px 12px rgba(255,255,255,0.1);
            }
            .btn-pg-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(255,255,255,0.2); background: #f8fafc; }

            .btn-pg-outline {
                background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.2);
                font-size: 10px; font-weight: 700; padding: 12px 20px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
                transition: all 0.3s ease; backdrop-filter: blur(4px);
            }
            .btn-pg-outline:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

            /* ── MINI-TARJETAS ESTADÍSTICAS ── */
            .pg-stat-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%;
                transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
                cursor: pointer;
            }
            .pg-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.04); }
            .pg-stat-icon {
                width: 48px; height: 48px; border-radius: 12px; background: var(--head);
                display: flex; align-items: center; justify-content: center;
                font-size: 20px; border: 1px solid var(--grid); flex-shrink: 0;
            }

            /* ── TARJETAS DE MÓDULOS DE ACCESO RÁPIDO ── */
            .pg-module-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
                padding: 24px; display: flex; flex-direction: column; align-items: flex-start;
                height: 100%; transition: all 0.3s ease; position: relative; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,0.02); cursor: pointer;
            }
            .pg-module-card::before {
                content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px;
                background: var(--grid-2); transition: background 0.3s;
            }
            .pg-module-card:hover { border-color: #cbd5e1; box-shadow: 0 8px 25px rgba(0,0,0,0.05); transform: translateY(-3px);}
            .pg-module-card:hover::before { background: var(--accent); }

            .pg-module-icon { font-size: 24px; color: var(--accent); margin-bottom: 16px; }
            .pg-module-title { font-size: 14px; font-weight: 800; color: var(--ink-2); margin-bottom: 8px; letter-spacing: -0.3px;}
            .pg-module-desc { font-size: 10px; color: var(--ink-3); line-height: 1.5; margin-bottom: 24px; flex-grow: 1; }

            .pg-link-btn { color: var(--accent); font-size: 10px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; background: none; border: none; padding: 0; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;}
            .pg-link-btn:hover { color: #2e5c43; gap: 8px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #pgIniDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #pgIniDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .pg-ini-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .pg-ini-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: pgIniDbgBlink 1s steps(2,start) infinite; }
            @keyframes pgIniDbgBlink { 50% { opacity: .35; } }
            .pg-ini-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #pgIniDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .pg-ini-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .pg-ini-dbg-controls { display: flex; gap: 4px; }
            .pg-ini-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .pg-ini-dbg-btn:hover { background: #475569; }
            #pgIniDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .pg-ini-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .pg-ini-dbg-time { color: #64748b; margin-right: 4px; }
            .pg-ini-dbg-info { color: #93c5fd; } .pg-ini-dbg-ok { color: #86efac; }
            .pg-ini-dbg-warn { color: #fde68a; } .pg-ini-dbg-err { color: #fca5a5; }
            .pg-ini-dbg-net  { color: #c4b5fd; } .pg-ini-dbg-ev  { color: #f9a8d4; }
        </style>

        <div class="row g-3">

            {{-- [JERARQUÍA 1] HERO BANNER FINANCIERO Y RESEÑAS --}}
            <div class="col-12">
                <div class="pg-hero-pro">
                    <i class="bi bi-wallet2 hero-watermark"></i>
                    <div class="pg-hero-content">
                        <span class="pg-hero-badge">
                            <i class="bi bi-shield-lock me-1"></i> Gestión Financiera y Calidad
                            @if($montoPagado > 0) · Abonado: ${{ number_format($montoPagado, 0) }} COP @endif
                        </span>
                        <h2 class="pg-hero-title">Control de Pagos, Pasarelas y Reseñas</h2>
                        <p class="pg-hero-subtitle">Administra tus comprobantes de aporte de administración, consulta los canales y pasarelas de pago autorizados y califica tu experiencia en cada propiedad visitada.</p>

                        <div class="pg-hero-actions">
                            <button type="button" class="btn-pg-primary js-pg-ini-nav" data-target-tab="#pg-sub-transacciones" data-nav-name="Ver Transacciones (Hero)">
                                <i class="bi bi-receipt"></i> Ver Transacciones ({{ $totalTransacciones }})
                            </button>
                            <button type="button" class="btn-pg-outline js-pg-ini-nav" data-target-tab="#pg-sub-pasarelas" data-nav-name="Ver Pasarelas (Hero)">
                                <i class="bi bi-credit-card"></i> Pasarelas ({{ $totalPasarelas }})
                            </button>
                            <button type="button" class="btn-pg-outline js-pg-ini-nav" data-target-tab="#pg-sub-reviews" data-nav-name="Ver Reseñas (Hero)">
                                <i class="bi bi-star-fill"></i> Mis Reseñas ({{ $totalReviews }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 2] 3 MINI-TARJETAS ESTADÍSTICAS HORIZONTALES (CLICABLES) --}}
            <div class="col-12 col-md-4">
                <div class="pg-stat-card js-pg-ini-nav" data-target-tab="#pg-sub-transacciones" data-nav-name="Stat Card · Transacciones">
                    <div class="pg-stat-icon" style="color: #10b981; background: #ecfdf5; border-color: #d1fae5;"><i class="bi bi-cash-stack"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Movimientos ({{ $totalTransacciones }})</div>
                        <div class="sub mt-1">
                            Pagado: <b>${{ number_format($montoPagado, 0) }}</b>
                            @if($montoPendiente > 0) · Pend: <b>${{ number_format($montoPendiente, 0) }}</b> @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="pg-stat-card js-pg-ini-nav" data-target-tab="#pg-sub-pasarelas" data-nav-name="Stat Card · Pasarelas">
                    <div class="pg-stat-icon" style="color: #3b82f6; background: #eff6ff; border-color: #dbeafe;"><i class="bi bi-credit-card-2-front"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Medios de Pago</div>
                        <div class="sub mt-1"><b>{{ $totalPasarelas }}</b> pasarela(s) o canal(es) activos.</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="pg-stat-card js-pg-ini-nav" data-target-tab="#pg-sub-reviews" data-nav-name="Stat Card · Reseñas">
                    <div class="pg-stat-icon" style="color: #f59e0b; background: #fffbeb; border-color: #fef3c7;"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <div class="val text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Calificaciones</div>
                        <div class="sub mt-1"><b>{{ $totalReviews }}</b> reseña(s) publicadas por ti.</div>
                    </div>
                </div>
            </div>

            {{-- [JERARQUÍA 3] 3 TARJETAS DE MÓDULOS DE ACCESO RÁPIDO --}}
            <div class="col-12 col-md-4 mt-2">
                <div class="pg-module-card js-pg-ini-nav" data-target-tab="#pg-sub-transacciones" data-nav-name="Módulo · Transacciones">
                    <div class="pg-module-icon"><i class="bi bi-receipt-cutoff"></i></div>
                    <div class="pg-module-title">Historial de Transacciones</div>
                    <div class="pg-module-desc">
                        Consulta tus abonos registrados, verifica el estado de validación de cada pago, descarga o adjunta soportes de transferencia por reserva.
                    </div>
                    <button type="button" class="pg-link-btn">
                        Ver Transacciones <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-2">
                <div class="pg-module-card js-pg-ini-nav" data-target-tab="#pg-sub-pasarelas" data-nav-name="Módulo · Pasarelas">
                    <div class="pg-module-icon"><i class="bi bi-bank"></i></div>
                    <div class="pg-module-title">Pasarelas y Cuentas</div>
                    <div class="pg-module-desc">
                        Revisa las pasarelas electrónicas, cuentas bancarias y canales oficiales habilitados para realizar el pago de tu aporte de administración.
                    </div>
                    <button type="button" class="pg-link-btn">
                        Consultar Pasarelas <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-2">
                <div class="pg-module-card js-pg-ini-nav" data-target-tab="#pg-sub-reviews" data-nav-name="Módulo · Reseñas">
                    <div class="pg-module-icon"><i class="bi bi-award"></i></div>
                    <div class="pg-module-title">Mis Reseñas y Evaluaciones</div>
                    <div class="pg-module-desc">
                        Evalúa el estado del inmueble, la limpieza y la atención recibida durante tu estadía para ayudarnos a mantener la excelencia del servicio.
                    </div>
                    <button type="button" class="pg-link-btn">
                        Gestionar Reseñas <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL PG-INI-v1-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.PgIniDbg && window.PgIniDbg.alive && window.PgIniDbg.alive())
        ? window.PgIniDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-pagos-inicio') || document.body;
                if (!$('pgIniDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'pgIniDbgToggle'; t.title = 'Debug Inicio Pagos (PG-INI-v1-DBG)';
                    t.innerHTML = '<span class="pg-ini-dbg-led" id="pgIniDbgLed"></span>DBG<span class="pg-ini-dbg-badge" id="pgIniDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('pgIniDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'pgIniDbgPanel';
                    p.innerHTML = '<div class="pg-ini-dbg-header" id="pgIniDbgHeader">' +
                        '<span>DEBUG · Inicio Pagos · PG-INI-v1-DBG</span>' +
                        '<span class="pg-ini-dbg-controls">' +
                        '<button type="button" class="pg-ini-dbg-btn" id="pgIniDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="pg-ini-dbg-btn" id="pgIniDbgCopy">Copiar</button>' +
                        '<button type="button" class="pg-ini-dbg-btn" id="pgIniDbgClear">Limpiar</button>' +
                        '<button type="button" class="pg-ini-dbg-btn" id="pgIniDbgClose">✕</button>' +
                        '</span></div><div id="pgIniDbgBody"></div>';
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
                    'pgTxDbgToggle', 'pgPasDbgToggle', 'pgRevDbgToggle'
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
                var t = $('pgIniDbgToggle'), p = $('pgIniDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('pgIniDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'pg-ini-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="pg-ini-dbg-time">[' + hora() + ']</span><span class="pg-ini-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('pgIniDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('pgIniDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('pgIniDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('pgIniDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('pgIniDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('pgIniDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('pgIniDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('pgIniDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('pgIniDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Inicio Pagos PG-INI-v1-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('pgIniDbgToggle'); }
            };
            window.PgIniDbg = api;
            return api;
        })();

    DBG.ensure();
    var pginidbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#pgIniDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#pgIniDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO DE INICIO PAGOS SIN RECARGAR PÁGINA ═══
    window.pgIniReloadFragment = function (motivo) {
        var root = document.getElementById('cliPagosIniRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'pagos';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        pginidbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                pginidbg('Error HTTP ' + res.status + ' al refrescar Inicio Pagos (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliPagosIniRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tx  = nuevoRoot.getAttribute('data-transacciones') || '0';
                var pas = nuevoRoot.getAttribute('data-pasarelas') || '0';
                var rev = nuevoRoot.getAttribute('data-reviews') || '0';
                pginidbg('Fragmento de Inicio Pagos actualizado (' + res.ms + 'ms) · tx=' + tx + ' · pasarelas=' + pas + ' · reseñas=' + rev, 'ok');
            } else {
                pginidbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliPagosIniRoot', 'warn');
            }
        })
        .catch(function (err) {
            pginidbg('Excepción recargando Inicio Pagos: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsPagosInicioListo) {
        window.__jsPagosInicioListo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#pgIniDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#pgIniDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#pgIniDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#pgIniDbgCopy'))    { e.stopPropagation(); DBG.copy(); pginidbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#pgIniDbgRefresh')) {
                e.stopPropagation();
                window.pgIniReloadFragment('manual-btn-dbg');
                return;
            }

            // Navegación interactiva hacia las sub-pestañas de Pagos (Transacciones / Pasarelas / Reseñas)
            var navEl = t.closest('.js-pg-ini-nav');
            if (navEl) {
                e.preventDefault();
                var targetTab = navEl.getAttribute('data-target-tab') || '';
                var navName   = navEl.getAttribute('data-nav-name') || targetTab;

                pginidbg('Click: ' + navName + ' → abrir ' + targetTab, 'ev');

                var btnTab = document.querySelector('[data-bs-target="' + targetTab + '"]');
                if (btnTab) {
                    btnTab.click();
                } else {
                    pginidbg('Aviso: No se encontró el botón de sub-pestaña para ' + targetTab, 'warn');
                }
            }
        });

        // Escuchar cambios de pestañas Bootstrap en el ecosistema de Pagos
        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) DBG.ensure();
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target.indexOf('#pg-sub-') === 0) {
                pginidbg('Sub-pestaña de Pagos mostrada: ' + target, 'info');
                if (target === '#pg-sub-inicio') {
                    window.pgIniReloadFragment('tab-focus-inicio');
                }
            }
        });
    }

    var SRVDBG = @json($pgIniDbg);
    pginidbg('PG-INI-v1-DBG activo · tx=' + (SRVDBG ? SRVDBG.transacciones : 0) +
             ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : 0) +
             ' · reseñas=' + (SRVDBG ? SRVDBG.reviews : 0), 'ok');
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { pginidbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
