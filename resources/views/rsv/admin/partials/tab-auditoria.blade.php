{{-- ============================================================================
     RSV · ADMIN · AUDITORÍA · TAB INICIO · AUD-v2.0-DBG
     Parcial: resources/views/rsv/admin/partials/auditoria-tab.blade.php
              (compatible también como rsv/admin/partials/tab-auditoria.blade.php)
     ============================================================================ --}}

@php
    $audErrores = [];
    $inmIdActual = $inmuebleId ?? request('inmueble_id', 'todos') ?: 'todos';

    $audContarTabla = function (string $audT) use (&$audErrores): int {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable($audT)) {
                return -1;
            }
            return (int) \Illuminate\Support\Facades\DB::table($audT)->count();
        } catch (\Throwable $audE) {
            $audErrores[] = $audT . ': ' . $audE->getMessage();
            return -2;
        }
    };

    $audFormatoEstado = function (int $n): string {
        if ($n === -1) return 'NO detectada';
        if ($n === -2) return 'error de lectura';
        return 'Sí · ' . $n . ' fila' . ($n === 1 ? '' : 's');
    };

    $cntAuditLogs = $audContarTabla('rsv_audit_logs');
    $cntOrigenes  = $audContarTabla('rsv_origen_reservas');
    $cntReceptor  = $audContarTabla('rsv_tipo_receptor');
    $cntStatuses  = $audContarTabla('rsv_statuses');
    $cntHistEst   = $audContarTabla('rsv_historial_estados');
    $cntPasarelas = $audContarTabla('rsv_pasarelas');

    // Métricas desglosadas de rsv_audit_logs (Cliente vs Admin, Visualización vs Gestión)
    $kpiAudHoy       = 0;
    $kpiAudCliente   = 0;
    $kpiAudAdmin     = 0;
    $kpiAudGestion   = 0;
    $kpiAudVistas    = 0;
    $ultimosEventos  = collect();

    try {
        if ($cntAuditLogs > 0 && class_exists(\App\Models\Rsv\AuditLog::class)) {
            $hoyStr = now()->toDateString();
            $kpiAudHoy     = \App\Models\Rsv\AuditLog::whereDate('created_at', $hoyStr)->count();
            $kpiAudCliente = \App\Models\Rsv\AuditLog::where('tabla_afectada', 'portal_cliente')->count();
            $kpiAudAdmin   = \App\Models\Rsv\AuditLog::where('tabla_afectada', 'panel_admin')->count();
            $kpiAudVistas  = \App\Models\Rsv\AuditLog::where('accion', 'like', 'VIEW%')->count();
            $kpiAudGestion = \App\Models\Rsv\AuditLog::whereIn('accion', ['CREATED', 'UPDATED', 'DELETED'])->count();

            $ultimosEventos = \App\Models\Rsv\AuditLog::with('user')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }
    } catch (\Throwable $eMetricas) {
        $audErrores[] = 'Métricas AuditLog: ' . $eMetricas->getMessage();
    }

    $rutaFragAud = '';
    foreach (['rsv.admin.auditoria.fragmento', 'rsv.admin.dashboard'] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $rutaFragAud = route($rn, ['inmueble_id' => $inmIdActual]);
            break;
        }
    }

    $audDbg = [
        'version'     => 'AUD-v2.0-DBG',
        'vista'       => 'partials/auditoria-tab.blade.php',
        'render'      => now()->format('H:i:s'),
        'inmueble_id' => $inmIdActual,
        'frag_url'    => $rutaFragAud,
        'kpis'        => [
            'total'   => max(0, $cntAuditLogs),
            'hoy'     => $kpiAudHoy,
            'cliente' => $kpiAudCliente,
            'admin'   => $kpiAudAdmin,
            'gestion' => $kpiAudGestion,
            'vistas'  => $kpiAudVistas,
        ],
        'tablas' => [
            'bitacora   · rsv_audit_logs'        => $audFormatoEstado($cntAuditLogs),
            'origenes   · rsv_origen_reservas'   => $audFormatoEstado($cntOrigenes),
            'receptores · rsv_tipo_receptor'     => $audFormatoEstado($cntReceptor),
            'estados    · rsv_statuses'          => $audFormatoEstado($cntStatuses),
            'historial  · rsv_historial_estados' => $audFormatoEstado($cntHistEst),
            'pasarelas  · rsv_pasarelas'         => $audFormatoEstado($cntPasarelas),
        ],
        'errores' => $audErrores,
    ];
@endphp

<div id="sheet-auditoria"
     class="reservas-soft"
     data-frag="{{ $rutaFragAud }}"
     data-inmueble-id="{{ $inmIdActual }}"
     data-total-logs="{{ max(0, $cntAuditLogs) }}">

    {{-- AUD-v2.0-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
    <div id="audDbgToggle" title="Debug Auditoría (AUD-v2.0-DBG)">
        <span class="aud-dbg-led" id="audDbgLed"></span>DBG<span class="aud-dbg-badge" id="audDbgBadge">0</span>
    </div>
    <div id="audDbgPanel" role="dialog" aria-label="Panel de debug de Auditoría">
        <div class="aud-dbg-header" id="audDbgHeader">
            <span>DEBUG · Auditoría (Tab) · AUD-v2.0-DBG</span>
            <span class="aud-dbg-controls">
                <button type="button" class="aud-dbg-btn" id="audDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
                <button type="button" class="aud-dbg-btn" id="audDbgCopy">Copiar</button>
                <button type="button" class="aud-dbg-btn" id="audDbgClear">Limpiar</button>
                <button type="button" class="aud-dbg-btn" id="audDbgClose">✕</button>
            </span>
        </div>
        <div id="audDbgBody"></div>
    </div>

    <style>
        .reservas-soft {
            --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
            --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
            --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
        }
        .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
        .reservas-soft .val { font-size:10.5px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
        .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.45; }

        /* ── HERO BANNER AUDITORÍA ── */
        .aud-hero-pro {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 12px;
            padding: 24px 26px;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14);
        }
        .aud-hero-pro .hero-watermark {
            position: absolute; right: -2%; bottom: -35%; font-size: 13rem;
            opacity: 0.04; color: #ffffff; pointer-events: none; transform: rotate(-10deg);
        }
        .aud-btn-primary {
            background: #f59e0b; color: #0f172a; border: none;
            font-size: 9.5px; font-weight: 800; padding: 8px 16px; border-radius: 6px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s ease;
        }
        .aud-btn-primary:hover { background: #fbbf24; transform: translateY(-1px); }
        .aud-btn-outline {
            background: rgba(255,255,255,0.07); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.2);
            font-size: 9px; font-weight: 700; padding: 8px 14px; border-radius: 6px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s ease;
        }
        .aud-btn-outline:hover { background: rgba(255,255,255,0.16); border-color: #fff; color: #fff; }

        /* ── TARJETAS KPI DE AUDITORÍA ── */
        .aud-stat-card {
            background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
            padding: 13px 15px; display: flex; align-items: center; gap: 12px; height: 100%;
            transition: all 0.18s ease; cursor: pointer;
        }
        .aud-stat-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 5px 14px rgba(15,23,42,0.04); }
        .aud-stat-icon {
            width: 38px; height: 38px; border-radius: 9px; display: flex;
            align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; border: 1px solid;
        }

        /* ── TARJETAS DE CATÁLOGOS Y RADAR EN VIVO ── */
        .aud-panel-card {
            background: #fff; border: 1px solid var(--grid-2); border-radius: 10px;
            padding: 16px; height: 100%; display: flex; flex-direction: column; justify-content: space-between;
        }
        .aud-cat-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 9px; border-radius: 7px; border: 1px solid var(--grid);
            background: var(--head); margin-bottom: 6px; gap: 8px;
        }
        .aud-cat-row:hover { border-color: var(--accent-bd); background: #fff; }

        .aud-feed-row {
            padding: 7px 10px; border-bottom: 1px solid var(--grid);
            display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;
        }
        .aud-feed-row:last-child { border-bottom: 0; }

        .aud-pill {
            display: inline-flex; align-items: center; padding: 2px 7px;
            border-radius: 10px; font-size: 7px; font-weight: 800; border: 1px solid;
            text-transform: uppercase; letter-spacing: .3px; white-space: nowrap;
        }
        .aud-pill-created { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
        .aud-pill-updated { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
        .aud-pill-deleted { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
        .aud-pill-view    { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }

        .aud-mini-btn {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2);
            font-size: 8px; font-weight: 700; padding: 3px 9px; border-radius: 5px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
        }
        .aud-mini-btn:hover { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); }

        /* ── PANEL DEBUG (TIP-v1.3) ── */
        #audDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
        #audDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
        .aud-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
        .aud-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: audDbgBlink 1s steps(2,start) infinite; }
        @keyframes audDbgBlink { 50% { opacity: .35; } }
        .aud-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
        #audDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        .aud-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
        .aud-dbg-controls { display: flex; gap: 4px; }
        .aud-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
        .aud-dbg-btn:hover { background: #475569; }
        #audDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
        .aud-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
        .aud-dbg-time { color: #64748b; margin-right: 4px; }
        .aud-dbg-info { color: #93c5fd; } .aud-dbg-ok { color: #86efac; }
        .aud-dbg-warn { color: #fde68a; } .aud-dbg-err { color: #fca5a5; }
        .aud-dbg-net { color: #c4b5fd; } .aud-dbg-debug { color: #94a3b8; }
    </style>

    <div class="row g-3">

        {{-- 1. HERO BANNER: BITÁCORA DEL SISTEMA (AUDIT LOGS) --}}
        <div class="col-12">
            <div class="aud-hero-pro">
                <i class="bi bi-shield-lock hero-watermark"></i>
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative" style="z-index: 2;">
                    <div style="max-width: 680px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 8px; letter-spacing: .5px; text-transform: uppercase;">
                                <i class="bi bi-radar me-1"></i> Trazabilidad 360° Activa
                            </span>
                            <span class="badge rounded-pill" id="audChipVer" style="font-size: 7.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; font-family: ui-monospace, monospace;">
                                AUD-v2.0-DBG
                            </span>
                        </div>
                        <h5 class="fw-bold text-white mb-1">
                            <i class="bi bi-shield-lock text-warning me-2"></i>Bitácora del Sistema (Audit Logs)
                        </h5>
                        <p class="small text-light opacity-75 mb-3" style="font-size: 10.5px;">
                            Registro inmutable en tiempo real de ingresos al portal, visualización de fragmentos y operaciones CRUD realizadas tanto por Asociados/Clientes como por Administradores (<code>rsv_audit_logs</code>).
                        </p>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="aud-btn-primary js-aud-open-logs">
                                <i class="bi bi-journal-code"></i> Explorar Bitácora Completa ({{ max(0, $cntAuditLogs) }})
                            </button>
                            <button type="button" class="aud-btn-outline" id="audBtnHeroRefresh">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar Radar
                            </button>
                        </div>
                    </div>

                    <div class="text-md-end">
                        <div class="lbl text-light opacity-50">Eventos Registrados Hoy</div>
                        <div class="fw-bold text-warning" style="font-size: 22px; line-height: 1.1;">{{ number_format($kpiAudHoy, 0) }}</div>
                        <div class="sub text-light opacity-75" style="font-size: 8px;">{{ now()->format('d/m/Y · h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TARJETAS KPI DE TELEMETRÍA DE AUDITORÍA (CLICABLES) --}}
        <div class="col-6 col-md-3">
            <div class="aud-stat-card js-aud-open-logs" title="Ver todos los registros en Audit Logs">
                <div class="aud-stat-icon" style="color:#3b82f6; background:#eff6ff; border-color:#dbeafe;">
                    <i class="bi bi-database-check"></i>
                </div>
                <div>
                    <div class="lbl">Total Bitácora</div>
                    <div class="val" style="font-size: 13px;">{{ number_format(max(0, $cntAuditLogs), 0) }}</div>
                    <div class="sub"><b>{{ $kpiAudHoy }}</b> hoy</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="aud-stat-card js-aud-open-logs" title="Actividad del Portal de Clientes">
                <div class="aud-stat-icon" style="color:#10b981; background:#ecfdf5; border-color:#d1fae5;">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <div class="lbl">Portal Asociado</div>
                    <div class="val" style="font-size: 13px;">{{ number_format($kpiAudCliente, 0) }}</div>
                    <div class="sub">ingresos y vistas</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="aud-stat-card js-aud-open-logs" title="Actividad del Panel Administrador">
                <div class="aud-stat-icon" style="color:#8b5cf6; background:#f5f3ff; border-color:#ede9fe;">
                    <i class="bi bi-sliders2"></i>
                </div>
                <div>
                    <div class="lbl">Panel Admin</div>
                    <div class="val" style="font-size: 13px;">{{ number_format($kpiAudAdmin, 0) }}</div>
                    <div class="sub">supervisión y filtros</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="aud-stat-card js-aud-open-logs" title="Creaciones, actualizaciones y eliminaciones en BD">
                <div class="aud-stat-icon" style="color:#f59e0b; background:#fffbeb; border-color:#fef3c7;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="lbl">Mutaciones CRUD</div>
                    <div class="val" style="font-size: 13px;">{{ number_format($kpiAudGestion, 0) }}</div>
                    <div class="sub"><b>{{ $kpiAudVistas }}</b> lecturas</div>
                </div>
            </div>
        </div>

        {{-- 3. CATÁLOGOS MAESTROS CON NAVEGACIÓN DIRECTA + RADAR DE ÚLTIMOS EVENTOS --}}
        <div class="col-12 col-lg-5">
            <div class="aud-panel-card">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color:var(--grid) !important;">
                        <span class="val" style="font-size: 11px;">
                            <i class="bi bi-Sliders text-success me-1"></i> Catálogos Maestros Monitoreados
                        </span>
                        <span class="aud-pill aud-pill-created">Salud OK</span>
                    </div>
                    <p class="sub mb-2">
                        Tablas maestras auditadas automáticamente por <code>AppServiceProvider</code>. Haz clic en <b>Configurar</b> para ir a su módulo:
                    </p>

                    <div class="aud-cat-row">
                        <div>
                            <div class="val" style="font-size: 9.5px;">Orígenes de Reserva</div>
                            <div class="sub"><code>rsv_origen_reservas</code> · {{ max(0, $cntOrigenes) }} registro(s)</div>
                        </div>
                        <button type="button" class="aud-mini-btn js-aud-go-cat" data-cat="origenes">
                            <i class="bi bi-gear"></i> Configurar
                        </button>
                    </div>

                    <div class="aud-cat-row">
                        <div>
                            <div class="val" style="font-size: 9.5px;">Estados Permitidos</div>
                            <div class="sub"><code>rsv_statuses</code> ({{ max(0, $cntStatuses) }}) · Historial ({{ max(0, $cntHistEst) }})</div>
                        </div>
                        <button type="button" class="aud-mini-btn js-aud-go-cat" data-cat="estados">
                            <i class="bi bi-flag"></i> Configurar
                        </button>
                    </div>

                    <div class="aud-cat-row">
                        <div>
                            <div class="val" style="font-size: 9.5px;">Pasarelas Financieras</div>
                            <div class="sub"><code>rsv_pasarelas</code> · {{ max(0, $cntPasarelas) }} canal(es)</div>
                        </div>
                        <button type="button" class="aud-mini-btn js-aud-go-cat" data-cat="pasarelas">
                            <i class="bi bi-credit-card"></i> Configurar
                        </button>
                    </div>

                    <div class="aud-cat-row mb-0">
                        <div>
                            <div class="val" style="font-size: 9.5px;">Tipos de Receptor (Soporte)</div>
                            <div class="sub"><code>rsv_tipo_receptor</code> · {{ max(0, $cntReceptor) }} área(s)</div>
                        </div>
                        <span class="aud-pill aud-pill-view">Activo</span>
                    </div>
                </div>

                <div class="pt-2 mt-2 border-top d-flex justify-content-between align-items-center" style="border-color:var(--grid) !important;">
                    <span class="sub">Inmutabilidad HTTP 403 activa en <code>update/destroy</code></span>
                    <button type="button" class="aud-mini-btn" id="audBtnConfigurar">
                        <i class="bi bi-sliders"></i> Ir a Estados
                    </button>
                </div>
            </div>
        </div>

        {{-- 4. RADAR EN VIVO: ÚLTIMOS 6 EVENTOS CAPTURADOS --}}
        <div class="col-12 col-lg-7">
            <div class="aud-panel-card">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color:var(--grid) !important;">
                        <span class="val" style="font-size: 11px;">
                            <i class="bi bi-activity text-warning me-1"></i> Últimos Eventos Capturados (Cliente y Admin)
                        </span>
                        <button type="button" class="aud-mini-btn js-aud-open-logs">
                            Ver todos ({{ max(0, $cntAuditLogs) }}) <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>

                    @forelse($ultimosEventos as $ev)
                        @php
                            $accStr = mb_strtoupper((string) ($ev->accion ?? 'INFO'));
                            $pillCls = 'aud-pill-view';
                            if (str_contains($accStr, 'CREAT')) $pillCls = 'aud-pill-created';
                            elseif (str_contains($accStr, 'UPDAT')) $pillCls = 'aud-pill-updated';
                            elseif (str_contains($accStr, 'DELET')) $pillCls = 'aud-pill-deleted';

                            $actorNom = optional($ev->user)->name
                                ?? ($ev->datos_nuevos['_meta_actor'] ?? null)
                                ?? ($ev->id_user ? 'Usuario #' . $ev->id_user : 'Sistema');
                            $moduloDesc = $ev->datos_nuevos['modulo'] ?? ($ev->tabla_afectada . ' #' . ($ev->registro_id ?? 0));
                        @endphp
                        <div class="aud-feed-row">
                            <div style="min-width: 0; flex: 1;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="aud-pill {{ $pillCls }}">{{ $accStr }}</span>
                                    <span class="val text-truncate" style="font-size: 9.5px;">{{ $actorNom }}</span>
                                    <span class="sub">· <code>{{ $ev->tabla_afectada }}</code></span>
                                    @if(!empty($ev->registro_id))
                                        <span class="sub">#{{ $ev->registro_id }}</span>
                                    @endif
                                </div>
                                <div class="sub text-truncate mt-1" style="color: var(--ink-2);">
                                    {{ $moduloDesc }}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="sub fw-bold">{{ optional($ev->created_at)->format('d/m H:i:s') }}</div>
                                <div class="sub" style="font-size: 7.5px;">IP: {{ $ev->ip_address ?? '127.0.0.1' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 bg-light border rounded-3 my-2" style="border-style: dashed !important;">
                            <i class="bi bi-shield-check text-muted d-block mb-1" style="font-size: 20px;"></i>
                            <span class="sub">Aún no hay eventos en la bitácora. Navega por las pestañas para ver la captura automática.</span>
                        </div>
                    @endforelse
                </div>

                <div class="pt-2 mt-2 border-top d-flex justify-content-between align-items-center" style="border-color:var(--grid) !important;">
                    <span class="sub">Captura activa: <code>VIEW_PORTAL</code>, <code>VIEW_DASHBOARD</code>, <code>VIEW_FRAGMENT</code>, <code>CREATED</code>, <code>UPDATED</code>, <code>DELETED</code></span>
                    <button type="button" class="aud-mini-btn js-aud-open-logs">
                        <i class="bi bi-table"></i> Abrir Tabla & CSV
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL AUD-v2.0-DBG (diseño TIP-v1.3) ═══ */
    var DBG = (window.AudDbg && window.AudDbg.alive && window.AudDbg.alive())
        ? window.AudDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function unicarDbg() {
                var ts = document.querySelectorAll('#audDbgToggle');
                for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                var ps = document.querySelectorAll('#audDbgPanel');
                for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
            }
            function asegurarNodos() {
                unicarDbg();
                var rootDbg = document.querySelector('#sheet-auditoria');
                if (!rootDbg) { return; }
                if (!$('audDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'audDbgToggle';
                    t.title = 'Debug Auditoría (AUD-v2.0-DBG)';
                    t.innerHTML = '<span class="aud-dbg-led" id="audDbgLed"></span>DBG<span class="aud-dbg-badge" id="audDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('audDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'audDbgPanel';
                    p.innerHTML = '<div class="aud-dbg-header" id="audDbgHeader">' +
                        '<span>DEBUG · Auditoría (Tab) · AUD-v2.0-DBG</span>' +
                        '<span class="aud-dbg-controls">' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgCopy">Copiar</button>' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgClear">Limpiar</button>' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgClose">✕</button>' +
                        '</span></div><div id="audDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'infDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; }
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('audDbgToggle'), p = $('audDbgPanel');
                if (t) { t.style.bottom = '0px'; }
                if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; }
            }
            function hora() {
                var d = new Date();
                function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('audDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'aud-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="aud-dbg-time">[' + hora() + ']</span>' +
                        '<span class="aud-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('audDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('audDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('audDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('audDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('audDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('audDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('audDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('audDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('audDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG auditoría AUD-v2.0-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        });
                    }
                },
                unicar: unicarDbg,
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('audDbgToggle'); }
            };
            window.AudDbg = api;
            return api;
        })();

    DBG.ensure();
    var auddbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE INICIO AUDITORÍA SIN RECARGAR PÁGINA ═══
    window.audTabReloadFragment = function (motivo) {
        var root = document.getElementById('sheet-auditoria');
        if (!root) return Promise.resolve();

        var fragUrl = root.getAttribute('data-frag') || window.location.pathname;
        var inmId   = root.getAttribute('data-inmueble-id') || 'todos';

        var urlObj = new URL(fragUrl, window.location.origin);
        urlObj.searchParams.set('inmueble_id', inmId);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        auddbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                auddbg('Error HTTP ' + res.status + ' al refrescar Auditoría (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('sheet-auditoria');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                DBG.ensure();
                var tot = nuevoRoot.getAttribute('data-total-logs') || '0';
                auddbg('Radar de Auditoría actualizado (' + res.ms + 'ms) · total logs=' + tot, 'ok');
            }
        })
        .catch(function (err) {
            auddbg('Excepción recargando Auditoría: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    function irASubPestanaLogs() {
        var btnLogs = document.querySelector('[data-bs-target="#aud-sub-logs"], [data-bs-target="#single-aud-sub-logs"]');
        if (btnLogs) {
            auddbg('Navegando a sub-pestaña Audit Logs', 'info');
            btnLogs.click();
        } else {
            auddbg('No se encontró el botón de sub-pestaña Audit Logs', 'warn');
        }
    }

    function irACatalogoMaestro(tipo) {
        var esGlobal = !!document.getElementById('globalAdminTabs');
        var mapa = {
            origenes:  esGlobal ? ['#global-reservas', '#res-sub-origenes']    : ['#reservas', '#single-res-sub-origenes'],
            estados:   esGlobal ? ['#global-reservas', '#res-sub-status']      : ['#reservas', '#single-res-sub-status'],
            pasarelas: esGlobal ? ['#global-finanzas', '#fin-sub-pasarelas']   : ['#finanzas', '#single-fin-sub-pasarelas']
        };
        var dest = mapa[tipo] || mapa.estados;
        auddbg('Navegando a Catálogo Maestro "' + tipo + '" → ' + dest.join(' → '), 'info');

        if (window.RsvAdmin && typeof window.RsvAdmin.go === 'function') {
            window.RsvAdmin.go(dest[0], dest[1]);
        } else {
            var pBtn = document.querySelector('[data-bs-target="' + dest[0] + '"]');
            if (pBtn) pBtn.click();
            setTimeout(function () {
                var cBtn = document.querySelector('[data-bs-target="' + dest[1] + '"]');
                if (cBtn) cBtn.click();
            }, 80);
        }
    }

    if (!window.__jsAuditoriaTabV2Listo) {
        window.__jsAuditoriaTabV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }

            if (t.closest('#audDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#audDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#audDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#audDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#audDbgRefresh') || t.closest('#audBtnHeroRefresh')) {
                e.stopPropagation();
                window.audTabReloadFragment('manual-refresh');
                return;
            }

            if (t.closest('.js-aud-open-logs')) {
                e.preventDefault();
                irASubPestanaLogs();
                return;
            }

            var btnCat = t.closest('.js-aud-go-cat');
            if (btnCat) {
                e.preventDefault();
                irACatalogoMaestro(btnCat.getAttribute('data-cat') || 'estados');
                return;
            }

            if (t.closest('#audBtnConfigurar')) {
                e.preventDefault();
                irACatalogoMaestro('estados');
            }
        });

        document.addEventListener('dblclick', function (e) {
            if (e.target && e.target.closest && e.target.closest('#audDbgHeader')) {
                DBG.close();
            }
        });

        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target === '#aud-sub-inicio' || target === '#single-aud-sub-inicio') {
                window.audTabReloadFragment('tab-focus');
            }
        });
    }

    var SRVDBG = @json($audDbg ?? null);
    auddbg('AUD-v2.0-DBG activo · logs=' + (SRVDBG && SRVDBG.kpis ? SRVDBG.kpis.total : 0) +
           ' (hoy=' + (SRVDBG && SRVDBG.kpis ? SRVDBG.kpis.hoy : 0) +
           ' · cliente=' + (SRVDBG && SRVDBG.kpis ? SRVDBG.kpis.cliente : 0) +
           ' · admin=' + (SRVDBG && SRVDBG.kpis ? SRVDBG.kpis.admin : 0) + ')', 'ok');
    if (SRVDBG && SRVDBG.tablas) {
        Object.keys(SRVDBG.tablas).forEach(function (k) {
            var st = String(SRVDBG.tablas[k]);
            auddbg('tabla · ' + k + ' → ' + st, st.indexOf('NO detectada') === 0 ? 'warn' : 'debug');
        });
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { auddbg('⚠ SERVIDOR: ' + er, 'err'); });
    }
})();
</script>
