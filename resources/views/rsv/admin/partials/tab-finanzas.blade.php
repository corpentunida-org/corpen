{{-- resources/views/rsv/admin/partials/tab-finanzas.blade.php · FIN-v2.0-DBG --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- FINANZAS · CENTRO DE CONTROL Y RESUMEN FINANCIERO (SOLO LECTURA)          --}}
{{-- Conecta con las sub-pestañas operativas de Transacciones y Pasarelas.     --}}
{{-- Incluye Terminal Debug Local Persistente (TIP-v1.3 · FIN-v2.0-DBG).       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@php
    $finErrores  = [];
    $inmIdActual = isset($inmueble) && $inmueble
        ? $inmueble->id
        : ($inmuebleId ?? request('inmueble_id', 'todos') ?: 'todos');

    // 1. Dataset blindado con autoconsulta si el controlador no envió las colecciones
    $finTrx = $transacciones ?? null;
    $finPas = isset($pasarelas) ? collect($pasarelas) : collect();

    try {
        if (is_null($finTrx) && class_exists(\App\Models\Rsv\TransaccionFinanciera::class)) {
            $qTrx = \App\Models\Rsv\TransaccionFinanciera::with(['reserva.inmueble', 'pasarela'])
                ->orderByDesc('created_at');
            if ($inmIdActual !== 'todos' && is_numeric($inmIdActual)) {
                $qTrx->whereHas('reserva', fn ($q) => $q->where('id_rsv_catalogo_inmueble', (int) $inmIdActual));
            }
            $finTrx = $qTrx->paginate(10, ['*'], 'page_finanzas');
        } elseif ($finTrx instanceof \Illuminate\Support\Collection || $finTrx instanceof \Illuminate\Pagination\AbstractPaginator) {
            // Ya viene cargado
        } else {
            $finTrx = collect();
        }

        if ($finPas->isEmpty() && class_exists(\App\Models\Rsv\Pasarela::class)) {
            $finPas = \App\Models\Rsv\Pasarela::orderBy('nombre')->get();
        }
    } catch (\Throwable $eFin) {
        $finErrores[] = $eFin->getMessage();
        $finTrx = collect();
    }

    // 2. Normalización de estado → [clave filtro, color dot, clase chip, etiqueta]
    $finEstado = function ($trx): array {
        $raw = mb_strtolower(trim((string) ($trx->estado_pago ?? $trx->estado ?? '')));
        if (str_contains($raw, 'aprob') || str_contains($raw, 'complet') || str_contains($raw, 'pagad') || str_contains($raw, 'ok') || str_contains($raw, 'exitos')) {
            return ['aprobado', '#15803d', 'chip-green', 'APROBADO'];
        }
        if (str_contains($raw, 'rechaz') || str_contains($raw, 'cancel') || str_contains($raw, 'anulad') || str_contains($raw, 'declin') || str_contains($raw, 'fallid')) {
            return ['rechazado', '#b91c1c', 'chip-red', 'RECHAZADO'];
        }
        return ['pendiente', '#b45309', 'chip-amber', 'PENDIENTE'];
    };

    // 3. Verificación real de soporte adjunto (Imagen o PDF)
    $finInfoSoporte = function ($trx): array {
        $raw = trim((string) ($trx->url_soporte_pago ?? $trx->soporte_pago ?? ''));
        if ($raw === '' || $raw === '#') {
            return ['ok' => false, 'url' => '', 'tipo' => 'none'];
        }
        $path = parse_url($raw, PHP_URL_PATH) ?? $raw;
        if (preg_match('/\.pdf$/i', $path)) {
            return ['ok' => true, 'url' => $raw, 'tipo' => 'pdf'];
        }
        if (preg_match('/\.(jpe?g|png|webp|gif|bmp|svg)$/i', $path)) {
            return ['ok' => true, 'url' => $raw, 'tipo' => 'image'];
        }
        return ['ok' => true, 'url' => $raw, 'tipo' => 'file'];
    };

    // 4. Totales y distribución en la página + consulta global rápida por inmueble/sistema
    $finTotalTrx   = method_exists($finTrx, 'total') ? $finTrx->total() : $finTrx->count();
    $finSumaPagina = 0.0;
    $finSumAprob   = 0.0;
    $finSumPend    = 0.0;
    $finSumRech    = 0.0;
    $finDist       = ['aprobado' => 0, 'pendiente' => 0, 'rechazado' => 0];
    $finConSoporte = 0;

    foreach ($finTrx as $finT) {
        $m = (float) ($finT->monto ?? 0);
        [$kEst] = $finEstado($finT);
        $finDist[$kEst] = ($finDist[$kEst] ?? 0) + 1;

        if ($kEst === 'aprobado') {
            $finSumAprob += $m;
            $finSumaPagina += $m;
        } elseif ($kEst === 'pendiente') {
            $finSumPend += $m;
            $finSumaPagina += $m;
        } else {
            $finSumRech += $m;
        }

        $sop = $finInfoSoporte($finT);
        if ($sop['ok']) {
            $finConSoporte++;
        }
    }

    // Ruta del fragmento para recarga AJAX sin recargar navegador
    $rutaFragFinanzas = '';
    foreach (['rsv.admin.finanzas.fragmento', 'rsv.admin.dashboard'] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $rutaFragFinanzas = route($rn, ['inmueble_id' => $inmIdActual]);
            break;
        }
    }

    $finDbg = [
        'version'      => 'FIN-v2.0-DBG',
        'vista'        => 'partials/tab-finanzas.blade.php',
        'render'       => now()->format('H:i:s'),
        'inmueble_id'  => $inmIdActual,
        'frag_url'     => $rutaFragFinanzas,
        'solo_lectura' => true,
        'trx_pagina'   => $finTrx->count(),
        'trx_total'    => $finTotalTrx,
        'suma_pagina'  => number_format($finSumaPagina, 2, '.', ''),
        'aprobado'     => number_format($finSumAprob, 2, '.', ''),
        'pendiente'    => number_format($finSumPend, 2, '.', ''),
        'rechazado'    => number_format($finSumRech, 2, '.', ''),
        'pasarelas'    => $finPas->count(),
        'estados'      => $finDist,
        'con_soporte'  => $finConSoporte,
        'paginada'     => (method_exists($finTrx, 'hasPages') && $finTrx->hasPages()) ? 'sí' : 'no',
        'errores'      => $finErrores,
    ];
@endphp

<div id="sheet-finanzas-inicio">

    {{-- FIN-v2.0-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
    <div id="finDbgToggle" title="Debug Finanzas (FIN-v2.0-DBG)">
        <span class="fin-dbg-led" id="finDbgLed"></span>DBG<span class="fin-dbg-badge" id="finDbgBadge">0</span>
    </div>
    <div id="finDbgPanel" role="dialog" aria-label="Panel de debug de Finanzas">
        <div class="fin-dbg-header" id="finDbgHeader">
            <span>DEBUG · Finanzas (Inicio) · FIN-v2.0-DBG</span>
            <span class="fin-dbg-controls">
                <button type="button" class="fin-dbg-btn" id="finDbgRefresh" title="Refrescar fragmento desde servidor">↻ Frag</button>
                <button type="button" class="fin-dbg-btn" id="finDbgCopy">Copiar</button>
                <button type="button" class="fin-dbg-btn" id="finDbgClear">Limpiar</button>
                <button type="button" class="fin-dbg-btn" id="finDbgClose">✕</button>
            </span>
        </div>
        <div id="finDbgBody"></div>
    </div>

    <div class="finanzas-soft p-3 bg-white rounded-3 border"
         id="finRoot"
         data-frag="{{ $rutaFragFinanzas }}"
         data-inmueble-id="{{ $inmIdActual }}"
         data-total="{{ $finTotalTrx }}"
         style="border-color: #f1f5f9 !important;">

        <style>
            .finanzas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .finanzas-soft .lbl { font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); }
            .finanzas-soft .val { font-size: 10.5px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
            .finanzas-soft .sub { font-size: 8px; line-height: 1.35; color: var(--ink-3); }
            .finanzas-soft .strong { color: var(--ink-2); font-weight: 700; }
            .finanzas-soft .num { font-variant-numeric: tabular-nums; }

            /* ── HERO BANNER FINANCIERO ── */
            .fin-hero-pro {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                border-radius: 11px;
                padding: 20px 24px;
                position: relative;
                overflow: hidden;
                color: #ffffff;
                margin-bottom: 12px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
            }
            .fin-hero-pro .hero-watermark {
                position: absolute; right: -2%; bottom: -35%; font-size: 12rem;
                opacity: 0.04; color: #ffffff; pointer-events: none; transform: rotate(-10deg);
            }
            .fin-hero-btn-primary {
                background: #10b981; color: #0f172a; border: none;
                font-size: 9px; font-weight: 800; padding: 7px 14px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
                transition: all 0.18s ease;
            }
            .fin-hero-btn-primary:hover { background: #34d399; transform: translateY(-1px); }
            .fin-hero-btn-outline {
                background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.2);
                font-size: 9px; font-weight: 700; padding: 7px 13px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
                transition: all 0.18s ease;
            }
            .fin-hero-btn-outline:hover { background: rgba(255,255,255,0.16); border-color: #fff; color: #fff; }

            /* ── TARJETAS KPI FINANCIERAS ── */
            .fin-kpi-grid {
                display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px;
            }
            @media (max-width: 767px) { .fin-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
            .fin-kpi-card {
                background: #fff; border: 1px solid var(--grid-2); border-radius: 9px;
                padding: 10px 13px; display: flex; align-items: center; justify-content: space-between;
                gap: 10px; cursor: pointer; transition: all 0.16s ease;
            }
            .fin-kpi-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.04); }
            .fin-kpi-icon {
                width: 34px; height: 34px; border-radius: 8px; display: flex;
                align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; border: 1px solid;
            }

            /* ── TABLA Y CONTROLES ── */
            .finanzas-soft .table > :not(caption) > * > * { padding: 4px 8px; line-height: 1.2; vertical-align: middle; }
            .finanzas-soft .table thead th { font-size: 8.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid-2) !important; }
            .finanzas-soft .table tbody td { font-size: 8.5px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .finanzas-soft .table tbody tr:hover > * { background-color: #f8fafc; --bs-table-accent-bg: transparent; }

            .finanzas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2); font-family: inherit; font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; text-decoration: none; }
            .finanzas-soft .btn-mini:hover { color: var(--accent); border-color: var(--accent-bd); background: var(--accent-bg); }
            .finanzas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8px; font-weight: 700; padding: 2px 9px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; }
            .finanzas-soft .btn-green:hover { background: var(--accent); color: #fff; }

            .finanzas-soft .xls-search { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 9px; outline: 0; min-width: 190px; }
            .finanzas-soft .xls-search:focus { background: #fff; border-color: var(--accent-bd); }

            /* ── CHIPS DE FILTRO RÁPIDO ── */
            .fin-chip-bar { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
            .fin-filter-chip {
                border: 1px solid var(--grid-2); background: #fff; color: var(--ink);
                font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
            }
            .fin-filter-chip:hover { border-color: var(--accent-bd); color: var(--ink-2); }
            .fin-filter-chip.active { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); font-weight: 700; }
            .fin-chip-cnt { font-size: 7px; font-weight: 700; padding: 0 5px; border-radius: 99px; background: var(--grid); color: var(--ink-2); }
            .fin-filter-chip.active .fin-chip-cnt { background: var(--accent); color: #fff; }

            .finanzas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 700; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 2px 8px; }
            .finanzas-soft .count-badge b { color: var(--accent); }
            .finanzas-soft .st-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; vertical-align: middle; margin-right: 3px; }

            /* ── CHIPS PASTEL ── */
            .finanzas-soft .chip { display: inline-flex; align-items: center; gap: 4px; padding: 1px 7px; border-radius: 10px; font-size: 7.5px; font-weight: 700; border: 1px solid; line-height: 13px; text-transform: uppercase; }
            .finanzas-soft .chip-green { color:#15803d; background:#f0fdf4; border-color:#bbf7d0; }
            .finanzas-soft .chip-red   { color:#b91c1c; background:#fef2f2; border-color:#fecaca; }
            .finanzas-soft .chip-amber { color:#b45309; background:#fffbeb; border-color:#fde68a; }

            .finanzas-soft .row-item { display: flex; justify-content: space-between; gap: 8px; padding: 4px 0; border-bottom: 1px dashed var(--grid); font-size: 8.5px; }
            .finanzas-soft .row-item:last-child { border-bottom: 0; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #finDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
            #finDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
            .fin-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .fin-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: finDbgBlink 1s steps(2,start) infinite; }
            @keyframes finDbgBlink { 50% { opacity: .35; } }
            .fin-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
            #finDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
            .fin-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .fin-dbg-controls { display: flex; gap: 4px; }
            .fin-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .fin-dbg-btn:hover { background: #475569; }
            #finDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .fin-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .fin-dbg-time { color: #64748b; margin-right: 4px; }
            .fin-dbg-info { color: #93c5fd; } .fin-dbg-ok { color: #86efac; }
            .fin-dbg-warn { color: #fde68a; } .fin-dbg-err { color: #fca5a5; }
            .fin-dbg-net { color: #c4b5fd; } .fin-dbg-debug { color: #94a3b8; }
        </style>

        {{-- 1. HERO BANNER FINANCIERO CON ACCESO DIRECTO A SUB-PESTAÑAS --}}
        <div class="fin-hero-pro">
            <i class="bi bi-cash-coin hero-watermark"></i>
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative" style="z-index: 2;">
                <div style="max-width: 660px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-success text-white fw-bold" style="font-size: 7.5px; letter-spacing: .5px; text-transform: uppercase;">
                            <i class="bi bi-shield-check me-1"></i> Ecosistema Financiero RSV
                        </span>
                        <span class="badge rounded-pill" style="font-size: 7px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; font-family: ui-monospace, monospace;">
                            FIN-v2.0-DBG
                        </span>
                    </div>
                    <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">
                        Centro de Control de Recaudos, Comprobantes y Pasarelas
                    </h6>
                    <p class="mb-2 text-light opacity-75" style="font-size: 9.5px;">
                        Supervisa en tiempo real los abonos registrados, previsualiza soportes en PDF o imagen y navega a las sub-pestañas operativas para aprobar pagos o administrar pasarelas.
                    </p>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="fin-hero-btn-primary js-fin-go-subtab" data-sub="transacciones">
                            <i class="bi bi-receipt-cutoff"></i> Gestionar Transacciones ({{ $finTotalTrx }})
                        </button>
                        <button type="button" class="fin-hero-btn-outline js-fin-go-subtab" data-sub="pasarelas">
                            <i class="bi bi-credit-card-2-front"></i> Configurar Pasarelas ({{ $finPas->count() }})
                        </button>
                        <button type="button" class="fin-hero-btn-outline" id="finBtnHeroRefresh">
                            <i class="bi bi-arrow-clockwise"></i> Sincronizar
                        </button>
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="lbl text-light opacity-50">Volumen Visible en Página</div>
                    <div class="fw-bold text-success num" style="font-size: 19px; line-height: 1.15;" id="finHeroSuma">
                        ${{ number_format($finSumaPagina, 0) }} <span style="font-size: 9px; color: #94a3b8;">COP</span>
                    </div>
                    <div class="sub text-light opacity-75" style="font-size: 8px;">
                        {{ $finConSoporte }} de {{ $finTrx->count() }} con soporte adjunto
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TARJETAS KPI FINANCIERAS (CLICABLES PARA FILTRAR AL INSTANTE) --}}
        <div class="fin-kpi-grid">
            <div class="fin-kpi-card js-fin-kpi-filter" data-filter="aprobado" title="Clic para filtrar pagos Aprobados">
                <div>
                    <div class="lbl">Aprobado (Página)</div>
                    <div class="val num" style="font-size: 12.5px; color: #15803d;">${{ number_format($finSumAprob, 0) }}</div>
                    <div class="sub"><b>{{ $finDist['aprobado'] }}</b> pago(s) verificado(s)</div>
                </div>
                <div class="fin-kpi-icon" style="color:#15803d; background:#f0fdf4; border-color:#bbf7d0;">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>

            <div class="fin-kpi-card js-fin-kpi-filter" data-filter="pendiente" title="Clic para filtrar pagos Pendientes por verificar">
                <div>
                    <div class="lbl">En Verificación</div>
                    <div class="val num" style="font-size: 12.5px; color: #b45309;">${{ number_format($finSumPend, 0) }}</div>
                    <div class="sub"><b>{{ $finDist['pendiente'] }}</b> pendiente(s)</div>
                </div>
                <div class="fin-kpi-icon" style="color:#b45309; background:#fffbeb; border-color:#fde68a;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <div class="fin-kpi-card js-fin-kpi-filter" data-filter="rechazado" title="Clic para filtrar pagos Rechazados o Anulados">
                <div>
                    <div class="lbl">Anulado / Rechazado</div>
                    <div class="val num" style="font-size: 12.5px; color: #b91c1c;">${{ number_format($finSumRech, 0) }}</div>
                    <div class="sub"><b>{{ $finDist['rechazado'] }}</b> descartado(s)</div>
                </div>
                <div class="fin-kpi-icon" style="color:#b91c1c; background:#fef2f2; border-color:#fecaca;">
                    <i class="bi bi-x-octagon"></i>
                </div>
            </div>

            <div class="fin-kpi-card js-fin-go-subtab" data-sub="pasarelas" title="Ir al catálogo de Pasarelas">
                <div>
                    <div class="lbl">Medios y Pasarelas</div>
                    <div class="val num" style="font-size: 12.5px; color: #1d4ed8;">{{ $finPas->count() }} Canales</div>
                    <div class="sub"><b>{{ $finConSoporte }}</b> soporte(s) cargados</div>
                </div>
                <div class="fin-kpi-icon" style="color:#1d4ed8; background:#eff6ff; border-color:#bfdbfe;">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
            </div>
        </div>

        {{-- 3. BARRA DE FILTROS RÁPIDOS + BUSCADOR EN VIVO --}}
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap" style="gap: 8px; border-color: var(--grid) !important;">
            <div class="fin-chip-bar">
                <span class="strong me-1" style="font-size: 9.5px;">
                    <i class="bi bi-list-check text-success me-1"></i> Movimientos:
                </span>
                <button type="button" class="fin-filter-chip active" data-estado="">
                    Todos <span class="fin-chip-cnt">{{ $finTrx->count() }}</span>
                </button>
                <button type="button" class="fin-filter-chip" data-estado="aprobado">
                    <span class="st-dot" style="background:#15803d;"></span>Aprobados <span class="fin-chip-cnt">{{ $finDist['aprobado'] }}</span>
                </button>
                <button type="button" class="fin-filter-chip" data-estado="pendiente">
                    <span class="st-dot" style="background:#b45309;"></span>Pendientes <span class="fin-chip-cnt">{{ $finDist['pendiente'] }}</span>
                </button>
                <button type="button" class="fin-filter-chip" data-estado="rechazado">
                    <span class="st-dot" style="background:#b91c1c;"></span>Rechazados <span class="fin-chip-cnt">{{ $finDist['rechazado'] }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                <span class="count-badge"><b id="statVisibles">{{ $finTrx->count() }}</b> visible(s)</span>
                <span class="count-badge">Σ <b id="statSuma">${{ number_format($finSumaPagina, 0) }}</b></span>
                <input type="text" id="search_transaccion" class="xls-search" placeholder="Buscar referencia, #reserva, inmueble, canal…" autocomplete="off">
            </div>
        </div>

        {{-- 4. TABLA DE TRANSACCIONES (SOLO LECTURA + PREVIEW DE SOPORTE REAL) --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 740px;">
                <thead>
                    <tr>
                        <th>Referencia / ID</th>
                        <th>Reserva e Inmueble</th>
                        <th>Pasarela / Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Soporte Cargado</th>
                        <th class="text-end" style="width: 85px;">Acción</th>
                    </tr>
                </thead>
                <tbody id="tbodyTransacciones">
                    @forelse($finTrx as $trx)
                        @php
                            [$estKey, $estColor, $estChip, $estLabel] = $finEstado($trx);
                            $pasNombre  = data_get($trx, 'pasarela.nombre') ?? data_get($trx, 'pasarela.name') ?? 'Pasarela Directa';
                            $pasIcono   = data_get($trx, 'pasarela.icono') ?: 'bi-shield-check';
                            $pasColor   = data_get($trx, 'pasarela.color') ?: '#3d7a5c';
                            $finInmNom  = data_get($trx, 'reserva.inmueble.name') ?? 'Inmueble';
                            $finResCod  = data_get($trx, 'reserva.codigo_reserva') ?? $trx->id_rsv_reservas ?? 'S/R';
                            $sopInfo    = $finInfoSoporte($trx);
                        @endphp

                        <tr class="trx-row" data-estado="{{ $estKey }}">
                            <td class="strong">
                                {{ $trx->referencia_externa ?: 'SIN-REF' }}
                                <div class="sub">TX #{{ $trx->id }}</div>
                            </td>

                            <td>
                                <span class="strong" style="color: var(--accent);">#{{ $finResCod }}</span>
                                <div class="sub text-truncate" style="max-width: 190px;" title="{{ $finInmNom }}">{{ $finInmNom }}</div>
                            </td>

                            <td>
                                <span class="strong">
                                    <i class="bi {{ $pasIcono }} me-1" style="font-size: 8.5px; color: {{ $pasColor }};"></i>{{ $pasNombre }}
                                </span>
                                <div class="sub">{{ $trx->metodo_pago ?: 'Canal autorizado' }}</div>
                            </td>

                            <td class="text-end num strong" data-monto="{{ (float) ($trx->monto ?? 0) }}">
                                ${{ number_format((float) ($trx->monto ?? 0), 0) }}
                                <span class="sub">{{ $trx->moneda ?? 'COP' }}</span>
                            </td>

                            <td>
                                <span class="chip {{ $estChip }}">
                                    <span class="st-dot" style="background: {{ $estColor }};"></span>{{ strtoupper($trx->estado_pago ?? $estLabel) }}
                                </span>
                            </td>

                            <td class="num sub">
                                {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                            </td>

                            {{-- SOLO MUESTRA EL BOTÓN DEL ARCHIVO QUE REALMENTE SE CARGÓ (IMAGEN O PDF) --}}
                            <td class="text-center">
                                @if($sopInfo['ok'])
                                    @if($sopInfo['tipo'] === 'pdf')
                                        <button type="button"
                                                class="btn-mini js-fin-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="pdf"
                                                data-titulo="PDF Soporte · TX #{{ $trx->id }} · Reserva #{{ $finResCod }}">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                                        </button>
                                    @elseif($sopInfo['tipo'] === 'image')
                                        <button type="button"
                                                class="btn-mini js-fin-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="image"
                                                data-titulo="Imagen Soporte · TX #{{ $trx->id }} · Reserva #{{ $finResCod }}">
                                            <i class="bi bi-file-earmark-image text-success"></i> Imagen
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn-mini js-fin-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="auto"
                                                data-titulo="Soporte · TX #{{ $trx->id }} · Reserva #{{ $finResCod }}">
                                            <i class="bi bi-paperclip text-primary"></i> Soporte
                                        </button>
                                    @endif
                                @else
                                    <span class="sub fst-italic">Sin soporte</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <button type="button" class="btn-mini"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTransaccion{{ $trx->id }}">
                                    <i class="bi bi-eye"></i> Detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" style="font-size: 8.5px; color: #94a3b8;">
                                <i class="bi bi-inbox d-block mb-1" style="font-size: 18px;"></i>
                                No hay transacciones financieras registradas para este filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($finTrx, 'hasPages') && $finTrx->hasPages())
            <div class="pt-2 d-flex justify-content-center js-fin-pagination">
                @include('rsv.components.pagination', ['paginator' => $finTrx])
            </div>
        @endif

        {{-- 5. SECCIÓN 2 · CATÁLOGO DE PASARELAS DE PAGO --}}
        <div class="d-flex justify-content-between align-items-center mt-3 mb-2 pt-2 flex-wrap"
             style="gap: 8px; border-top: 1px dashed var(--grid-2);">
            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-credit-card-2-front text-success me-1"></i> Pasarelas y Canales de Recaudo
                <span class="sub ms-1">· Catálogo activo</span>
            </h6>
            <div class="d-flex align-items-center gap-2">
                <span class="count-badge"><b>{{ $finPas->count() }}</b> pasarela(s)</span>
                <button type="button" class="btn-mini js-fin-go-subtab" data-sub="pasarelas">
                    <i class="bi bi-gear"></i> Administrar Pasarelas
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 460px;">
                <thead>
                    <tr>
                        <th style="width: 65px;">ID</th>
                        <th>Pasarela / Canal</th>
                        <th>Configuración Visual</th>
                        <th style="width: 110px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finPas as $pasarela)
                        @php
                            $esActivaPas = !isset($pasarela->activa) || (bool) $pasarela->activa;
                        @endphp
                        <tr>
                            <td class="num sub">#{{ $pasarela->id }}</td>
                            <td class="strong">
                                <i class="bi {{ $pasarela->icono ?: 'bi-credit-card' }} me-1"
                                   style="font-size: 9px; color: {{ $pasarela->color ?: '#3d7a5c' }};"></i>
                                {{ $pasarela->nombre ?? $pasarela->name ?? 'Pasarela' }}
                            </td>
                            <td class="sub">
                                <code>{{ $pasarela->icono ?: 'bi-credit-card' }}</code> · {{ $pasarela->color ?: 'Color estándar' }}
                            </td>
                            <td>
                                <span class="chip {{ $esActivaPas ? 'chip-green' : 'chip-red' }}">
                                    <span class="st-dot" style="background: {{ $esActivaPas ? '#15803d' : '#b91c1c' }};"></span>
                                    {{ $esActivaPas ? 'ACTIVA' : 'INACTIVA' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #94a3b8;">
                                <i class="bi bi-credit-card-2-front me-1"></i> No hay pasarelas de pago configuradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 6. MODALES DETALLE DE CADA TRANSACCIÓN --}}
        @foreach($finTrx as $trx)
            @php
                [$estKey, $estColor, $estChip, $estLabel] = $finEstado($trx);
                $sopInfo    = $finInfoSoporte($trx);
                $finInmNom  = data_get($trx, 'reserva.inmueble.name');
                $finResCod  = data_get($trx, 'reserva.codigo_reserva') ?? $trx->id_rsv_reservas ?? 'S/R';
            @endphp

            <div class="modal fade" id="modalTransaccion{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 8px; font-size: 9.5px;">

                        <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                            <div>
                                <div class="sub" style="margin: 0; letter-spacing: .05em; text-transform: uppercase;">
                                    Transacción #{{ $trx->id }}
                                </div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.92rem;">Expediente de Pago</h6>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        </div>

                        <div class="modal-body p-3">
                            <div class="text-center mb-2">
                                <div class="num strong" style="font-size: 15px;">
                                    ${{ number_format((float) ($trx->monto ?? 0), 0) }}
                                    <span class="sub">{{ $trx->moneda ?? 'COP' }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="chip {{ $estChip }}">{{ strtoupper($trx->estado_pago ?? $estLabel) }}</span>
                                </div>
                            </div>

                            <div class="mb-2" style="border: 1px solid var(--grid-2); border-radius: 6px; padding: 8px 10px;">
                                <div class="row-item">
                                    <span class="lbl">Referencia</span>
                                    <span class="val">{{ $trx->referencia_externa ?: 'N/A' }}</span>
                                </div>
                                <div class="row-item">
                                    <span class="lbl">Reserva</span>
                                    <span class="val">#{{ $finResCod }}</span>
                                </div>
                                @if($finInmNom)
                                    <div class="row-item">
                                        <span class="lbl">Inmueble</span>
                                        <span class="val text-end">{{ $finInmNom }}</span>
                                    </div>
                                @endif
                                <div class="row-item">
                                    <span class="lbl">Pasarela</span>
                                    <span class="val">{{ data_get($trx, 'pasarela.nombre') ?? data_get($trx, 'pasarela.name') ?? 'Directa' }}</span>
                                </div>
                                <div class="row-item">
                                    <span class="lbl">Método</span>
                                    <span class="val">{{ $trx->metodo_pago ?: 'N/A' }}</span>
                                </div>
                                <div class="row-item">
                                    <span class="lbl">Fecha</span>
                                    <span class="val">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i:s') : 'N/A' }}</span>
                                </div>
                            </div>

                            @if($sopInfo['ok'])
                                <div class="d-flex align-items-center justify-content-between"
                                     style="background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 6px; padding: 7px 10px;">
                                    <div>
                                        <div style="font-size: 8.5px; font-weight: 700; color: var(--accent);">
                                            Soporte ({{ strtoupper($sopInfo['tipo']) }})
                                        </div>
                                        <div class="sub">Verificable en línea</div>
                                    </div>
                                    <a href="{{ $sopInfo['url'] }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="btn-green">
                                        <i class="bi bi-box-arrow-up-right"></i> Abrir
                                    </a>
                                </div>
                            @else
                                <div style="border: 1px solid var(--grid); border-radius: 6px; padding: 7px 10px; font-size: 8.5px; color: var(--ink-3); background: var(--head);">
                                    <i class="bi bi-file-earmark-x me-1"></i>
                                    Esta transacción no tiene soporte adjunto.
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer d-flex justify-content-between" style="border-top: 1px solid #f1f5f9; padding: 8px 14px;">
                            <button type="button" class="btn-mini js-fin-go-subtab" data-sub="transacciones" data-bs-dismiss="modal">
                                <i class="bi bi-pencil-square"></i> Ir a Gestionar
                            </button>
                            <button type="button" class="btn-mini" data-bs-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        {{-- MODAL VISOR RÁPIDO DE SOPORTES (IMAGEN O PDF) --}}
        <div class="modal fade" id="finModalPreviewSoporte" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border: 0; border-radius: 10px; overflow: hidden;">
                    <div class="modal-header bg-dark text-white py-2 px-3">
                        <span class="fw-bold" style="font-size: 10.5px;" id="finModalPreviewTitle">
                            <i class="bi bi-eye text-success me-1"></i> Vista Previa del Soporte
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" target="_blank" id="finModalPreviewExt" class="btn-mini">
                                <i class="bi bi-box-arrow-up-right"></i> Abrir original
                            </a>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        </div>
                    </div>
                    <div class="modal-body p-2 bg-light text-center" id="finModalPreviewBody" style="min-height: 340px; display: flex; align-items: center; justify-content: center;">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL FIN-v2.0-DBG (diseño TIP-v1.3) ═══ */
    var DBG = (window.FinDbg && window.FinDbg.alive && window.FinDbg.alive())
        ? window.FinDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function unicarDbg() {
                var ts = document.querySelectorAll('#finDbgToggle');
                for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                var ps = document.querySelectorAll('#finDbgPanel');
                for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
            }
            function asegurarNodos() {
                unicarDbg();
                var rootDbg = document.getElementById('sheet-finanzas-inicio') || document.getElementById('finRoot');
                if (!rootDbg) { return; }
                if (!$('finDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'finDbgToggle';
                    t.title = 'Debug Finanzas (FIN-v2.0-DBG)';
                    t.innerHTML = '<span class="fin-dbg-led" id="finDbgLed"></span>DBG<span class="fin-dbg-badge" id="finDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('finDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'finDbgPanel';
                    p.innerHTML = '<div class="fin-dbg-header" id="finDbgHeader">' +
                        '<span>DEBUG · Finanzas (Inicio) · FIN-v2.0-DBG</span>' +
                        '<span class="fin-dbg-controls">' +
                        '<button type="button" class="fin-dbg-btn" id="finDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="fin-dbg-btn" id="finDbgCopy">Copiar</button>' +
                        '<button type="button" class="fin-dbg-btn" id="finDbgClear">Limpiar</button>' +
                        '<button type="button" class="fin-dbg-btn" id="finDbgClose">✕</button>' +
                        '</span></div><div id="finDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; }
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('finDbgToggle'), p = $('finDbgPanel');
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
                    var b = $('finDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'fin-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="fin-dbg-time">[' + hora() + ']</span>' +
                        '<span class="fin-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('finDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('finDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('finDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('finDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('finDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('finDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('finDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('finDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('finDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG finanzas FIN-v2.0-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        });
                    }
                },
                unicar: unicarDbg,
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('finDbgToggle'); }
            };
            window.FinDbg = api;
            return api;
        })();

    DBG.ensure();
    var finDbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function fmtCop(n) {
        return '$' + Math.round(Number(n || 0)).toLocaleString('es-CO');
    }

    /* ═══ FILTRADO EN VIVO (BÚSQUEDA + CHIPS DE ESTADO) ═══ */
    function aplicarFiltrosFinanzas(origen) {
        var tbody = document.getElementById('tbodyTransacciones');
        if (!tbody) return;

        var search = document.getElementById('search_transaccion');
        var chipActivo = document.querySelector('#finRoot .fin-filter-chip.active');
        var q   = (search && search.value) ? search.value.toLowerCase().trim() : '';
        var est = chipActivo ? (chipActivo.getAttribute('data-estado') || '') : '';

        var v = 0, s = 0;
        tbody.querySelectorAll('.trx-row').forEach(function (row) {
            var okQ = !q   || row.textContent.toLowerCase().indexOf(q) !== -1;
            var okE = !est || row.getAttribute('data-estado') === est;
            var show = okQ && okE;

            row.style.display = show ? '' : 'none';
            if (show) {
                v++;
                var m = row.querySelector('[data-monto]');
                if (m) s += parseFloat(m.getAttribute('data-monto')) || 0;
            }
        });

        var vis = document.getElementById('statVisibles');
        var sum = document.getElementById('statSuma');
        if (vis) vis.textContent = v;
        if (sum) sum.textContent = fmtCop(s);

        if (origen) {
            finDbg('filtro (' + origen + ') · estado=' + (est || 'todos') + ' · q="' + q + '" · ' + v + ' visible(s) · Σ ' + fmtCop(s), 'info');
        }
    }

    /* ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE FINANZAS SIN RECARGAR PÁGINA ═══ */
    window.finTabReloadFragment = function (urlPersonalizada, motivo) {
        var root = document.getElementById('finRoot');
        if (!root) return Promise.resolve();

        var fragBase = urlPersonalizada || root.getAttribute('data-frag') || window.location.pathname;
        var inmId    = root.getAttribute('data-inmueble-id') || 'todos';

        var urlObj = new URL(fragBase, window.location.origin);
        if (!urlObj.searchParams.has('inmueble_id')) {
            urlObj.searchParams.set('inmueble_id', inmId);
        }
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        finDbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                finDbg('Error HTTP ' + res.status + ' al refrescar Finanzas (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('finRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                DBG.ensure();
                aplicarFiltrosFinanzas();
                finDbg('Resumen Financiero actualizado (' + res.ms + 'ms) · total=' + (nuevoRoot.getAttribute('data-total') || '0'), 'ok');
            }
        })
        .catch(function (err) {
            finDbg('Excepción recargando Finanzas: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    function navegarASubPestanaFinanzas(destino) {
        var selector = destino === 'pasarelas'
            ? '[data-bs-target="#fin-sub-pasarelas"], [data-bs-target="#single-fin-sub-pasarelas"]'
            : '[data-bs-target="#fin-sub-transacciones"], [data-bs-target="#single-fin-sub-transacciones"]';

        var btn = document.querySelector(selector);
        if (btn) {
            finDbg('Navegando a sub-pestaña de Finanzas: ' + destino, 'info');
            btn.click();
        } else {
            finDbg('No se encontró el botón para la sub-pestaña: ' + destino, 'warn');
        }
    }

    function abrirVisorSoporte(url, tipo, titulo) {
        if (!url) return;
        var modalEl = document.getElementById('finModalPreviewSoporte');
        var bodyEl  = document.getElementById('finModalPreviewBody');
        var titleEl = document.getElementById('finModalPreviewTitle');
        var extEl   = document.getElementById('finModalPreviewExt');
        if (!modalEl || !bodyEl) {
            window.open(url, '_blank');
            return;
        }

        if (titleEl) titleEl.innerHTML = '<i class="bi bi-eye text-success me-1"></i> ' + (titulo || 'Soporte de Pago');
        if (extEl)   extEl.href = url;

        var esPdf = (tipo === 'pdf') || (tipo === 'auto' && /\.pdf($|\?)/i.test(url));
        if (esPdf) {
            bodyEl.innerHTML = '<iframe src="' + url + '" style="width:100%;height:460px;border:0;border-radius:6px;background:#fff;"></iframe>';
        } else {
            bodyEl.innerHTML = '<img src="' + url + '" alt="Soporte" style="max-width:100%;max-height:460px;object-fit:contain;border-radius:6px;">';
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
        finDbg('Vista previa de soporte abierta: ' + (titulo || url), 'info');
    }

    if (!window.__finDbgDocBoundV2) {
        window.__finDbgDocBoundV2 = true;

        document.addEventListener('click', function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }

            if (t.closest('#finDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#finDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#finDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#finDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#finDbgRefresh') || t.closest('#finBtnHeroRefresh')) {
                e.stopPropagation();
                window.finTabReloadFragment(null, 'manual-refresh');
                return;
            }

            // Chips de filtro de estado
            var chip = t.closest('.fin-filter-chip');
            if (chip && chip.closest('#finRoot')) {
                document.querySelectorAll('#finRoot .fin-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosFinanzas('chip');
                return;
            }

            // Tarjetas KPI que filtran la tabla por estado
            var kpiFilter = t.closest('.js-fin-kpi-filter');
            if (kpiFilter && kpiFilter.closest('#finRoot')) {
                var estObj = kpiFilter.getAttribute('data-filter') || '';
                var targetChip = document.querySelector('#finRoot .fin-filter-chip[data-estado="' + estObj + '"]');
                if (targetChip) targetChip.click();
                return;
            }

            // Navegación a sub-pestañas operativas (Transacciones / Pasarelas)
            var btnSub = t.closest('.js-fin-go-subtab');
            if (btnSub && btnSub.closest('#finRoot')) {
                e.preventDefault();
                navegarASubPestanaFinanzas(btnSub.getAttribute('data-sub') || 'transacciones');
                return;
            }

            // Visor rápido de soporte (PDF o Imagen)
            var btnSop = t.closest('.js-fin-preview-soporte');
            if (btnSop && btnSop.closest('#finRoot')) {
                e.preventDefault();
                abrirVisorSoporte(
                    btnSop.getAttribute('data-url'),
                    btnSop.getAttribute('data-tipo') || 'auto',
                    btnSop.getAttribute('data-titulo')
                );
                return;
            }

            // Paginación AJAX dentro de #finRoot
            var pageLink = t.closest('.js-fin-pagination a');
            if (pageLink && pageLink.closest('#finRoot')) {
                var href = pageLink.getAttribute('href');
                if (href && href !== '#') {
                    e.preventDefault();
                    window.finTabReloadFragment(href, 'paginacion');
                }
            }
        });

        var finFiltroTmr = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'search_transaccion') {
                aplicarFiltrosFinanzas();
                clearTimeout(finFiltroTmr);
                finFiltroTmr = setTimeout(function () {
                    aplicarFiltrosFinanzas('búsqueda');
                }, 380);
            }
        });

        document.addEventListener('dblclick', function (e) {
            if (e.target && e.target.closest && e.target.closest('#finDbgHeader')) {
                DBG.close();
            }
        });

        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target === '#fin-sub-inicio' || target === '#single-fin-sub-inicio') {
                window.finTabReloadFragment(null, 'tab-focus');
            }
        });

        document.addEventListener('show.bs.modal', function (e) {
            var idm = (e.target && e.target.id) ? e.target.id : '';
            if (idm.indexOf('modalTransaccion') === 0) {
                DBG.log('modal · expediente de transacción ' + idm.replace('modalTransaccion', '#') + ' abierto', 'info');
            }
        });
    }

    var SRVDBG = @json($finDbg);
    finDbg('FIN-v2.0-DBG activo · tx página=' + (SRVDBG ? SRVDBG.trx_pagina : 0) +
           ' · total=' + (SRVDBG ? SRVDBG.trx_total : 0) +
           ' · Σ=$' + (SRVDBG ? SRVDBG.suma_pagina : '0') +
           ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : 0), 'ok');
    if (SRVDBG && SRVDBG.estados) {
        finDbg('distribución · aprobado=' + SRVDBG.estados.aprobado +
               ' · pendiente=' + SRVDBG.estados.pendiente +
               ' · rechazado=' + SRVDBG.estados.rechazado +
               ' · con soporte=' + (SRVDBG.con_soporte || 0), 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { finDbg('⚠ SERVIDOR: ' + er, 'err'); });
    }
})();
</script>
