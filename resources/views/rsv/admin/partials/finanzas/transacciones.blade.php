{{-- ============================================================================
     RSV · ADMIN · FINANZAS · TRANSACCIONES · TRX-v3.3-DBG
     Parcial: resources/views/rsv/admin/partials/finanzas/transacciones.blade.php
     ============================================================================ --}}
@php
    $trxDbgErrores = [];

    /* 1. Contexto de inmueble (Modo Single vs Modo Global) */
    $trxInm = null;
    $trxInmId = $inmuebleId ?? null;
    if (isset($inmueble) && $inmueble) {
        $trxInm = $inmueble;
        $trxInmId = is_object($trxInm) ? ($trxInm->id ?? null) : $trxInm;
    } elseif (empty($trxInmId) && request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $trxInmId = request('inmueble_id');
    }
    if ($trxInmId === 'todos') {
        $trxInmId = null;
    }
    $trxGlobal = empty($trxInmId);

    /* 2. Filtro por rango de fechas (por defecto el mes en curso) + opción "todo=1" */
    $trxHoy = now();
    $trxVerTodo = request()->boolean('todo');
    $trxReqDesde = trim((string) request('fecha_desde', ''));
    $trxReqHasta = trim((string) request('fecha_hasta', ''));
    try { $trxDesde = $trxReqDesde !== '' ? \Carbon\Carbon::parse($trxReqDesde)->startOfDay() : $trxHoy->copy()->startOfMonth(); }
    catch (\Throwable $e) { $trxDesde = $trxHoy->copy()->startOfMonth(); }
    try { $trxHasta = $trxReqHasta !== '' ? \Carbon\Carbon::parse($trxReqHasta)->endOfDay() : $trxHoy->copy()->endOfMonth(); }
    catch (\Throwable $e) { $trxHasta = $trxHoy->copy()->endOfMonth(); }
    if ($trxHasta->lt($trxDesde)) {
        $trxTmp = $trxDesde;
        $trxDesde = $trxHasta->copy()->startOfDay();
        $trxHasta = $trxTmp->copy()->endOfDay();
    }
    $trxMesPorDefecto = ($trxReqDesde === '' && $trxReqHasta === '' && !$trxVerTodo);

    /* 3. Buscador instantáneo en servidor (término q) */
    $trxQ = trim((string) request('q', ''));
    if (function_exists('mb_strlen') && mb_strlen($trxQ) > 100) {
        $trxQ = trim(mb_substr($trxQ, 0, 100));
    }
    $trxQWhere = null;
    $trxQWhereSin = null;
    if ($trxQ !== '') {
        $trxQLike = '%' . $trxQ . '%';
        $trxQEsId = ctype_digit($trxQ) ? (int) $trxQ : null;
        $trxQWhere = function ($qq) use ($trxQLike, $trxQEsId) {
            $qq->where(function ($w) use ($trxQLike, $trxQEsId) {
                $w->where('referencia_externa', 'like', $trxQLike)
                    ->orWhere('metodo_pago', 'like', $trxQLike)
                    ->orWhere('estado_pago', 'like', $trxQLike)
                    ->orWhere('monto', 'like', $trxQLike);
                if ($trxQEsId !== null) { $w->orWhere('id', $trxQEsId); }
                $w->orWhereHas('reserva', function ($r) use ($trxQLike) { $r->where('codigo_reserva', 'like', $trxQLike); })
                  ->orWhereHas('pasarela', function ($p) use ($trxQLike) { $p->where('nombre', 'like', $trxQLike); });
            });
        };
        $trxQWhereSin = function ($qq) use ($trxQLike, $trxQEsId) {
            $qq->where(function ($w) use ($trxQLike, $trxQEsId) {
                $w->where('referencia_externa', 'like', $trxQLike)
                    ->orWhere('metodo_pago', 'like', $trxQLike)
                    ->orWhere('estado_pago', 'like', $trxQLike)
                    ->orWhere('monto', 'like', $trxQLike);
                if ($trxQEsId !== null) { $w->orWhere('id', $trxQEsId); }
                $w->orWhereHas('reserva', function ($r) use ($trxQLike) { $r->where('codigo_reserva', 'like', $trxQLike); });
            });
        };
    }

    /* 4. Consulta principal sobre TransaccionFinanciera (respeta page_finanzas y relaciones sin restringir FK) */
    $listadoTransacciones = null;
    $trxFuente = 'consulta propia (vista)';
    $trxModelo = class_exists('\App\Models\Rsv\TransaccionFinanciera') ? '\App\Models\Rsv\TransaccionFinanciera' : null;
    if (!$trxModelo) {
        foreach (['\\App\\Models\\Rsv\\RsvTransaccionFinanciera', '\\App\\Models\\Rsv\\TransaccionesFinancieras', '\\App\\Models\\TransaccionFinanciera', '\\App\\Models\\Rsv\\Transaccion'] as $trxClase) {
            if (class_exists($trxClase)) { $trxModelo = $trxClase; break; }
        }
    }

    $paginaFin = max(1, (int) (request('page_finanzas') ?? request('page') ?? 1));

    if ($trxModelo) {
        try {
            $qTrx = $trxModelo::query()->with(['reserva.inmueble', 'reserva.user', 'pasarela']);
            if (!$trxGlobal && is_numeric($trxInmId)) {
                $qTrx->whereHas('reserva', function ($qq) use ($trxInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $trxInmId);
                });
            }
            if (!$trxVerTodo) {
                $qTrx->whereBetween('created_at', [$trxDesde, $trxHasta]);
            }
            if ($trxQWhere) { $qTrx->where($trxQWhere); }
            $listadoTransacciones = $qTrx->orderByDesc('created_at')
                ->paginate(15, ['*'], 'page_finanzas', $paginaFin)
                ->withQueryString();
            $trxFuente = 'consulta propia (vista · ' . ($trxVerTodo ? 'histórico completo' : 'con fechas') . ($trxQ !== '' ? ' + búsqueda' : '') . ')';
        } catch (\Throwable $e) {
            $trxDbgErrores[] = 'consulta propia con fechas: ' . $e->getMessage();
            try {
                $qTrx2 = $trxModelo::query();
                if (!$trxGlobal && is_numeric($trxInmId)) {
                    $qTrx2->whereHas('reserva', function ($qq) use ($trxInmId) {
                        $qq->where('id_rsv_catalogo_inmueble', $trxInmId);
                    });
                }
                if (!$trxVerTodo) {
                    $qTrx2->whereBetween('created_at', [$trxDesde, $trxHasta]);
                }
                if ($trxQWhereSin) { $qTrx2->where($trxQWhereSin); }
                $listadoTransacciones = $qTrx2->orderByDesc('created_at')
                    ->paginate(15, ['*'], 'page_finanzas', $paginaFin)
                    ->withQueryString();
                $trxFuente = 'consulta propia (sin eager)';
            } catch (\Throwable $e2) {
                $trxDbgErrores[] = 'reintento sin eager: ' . $e2->getMessage();
                $listadoTransacciones = null;
            }
        }
    }

    if ($listadoTransacciones === null && isset($transacciones) && $transacciones !== null) {
        $listadoTransacciones = $transacciones;
        $trxFuente = 'controlador (fallback)';
    }
    if ($listadoTransacciones === null) {
        $listadoTransacciones = collect();
    }

    /* 5. Rutas y Catálogos Auxiliares (Pasarelas + Reservas para el selector de Nueva Transacción) */
    $trxRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };

    $trxStore = $trxRuta(['rsv.transacciones-financieras.store', 'rsv.transacciones.store', 'rsv.transaccion-financiera.store', 'rsv.transaccionesfinancieras.store']);
    $trxBase  = $trxRuta(['rsv.transacciones-financieras.index', 'rsv.transacciones.index', 'rsv.transaccion-financiera.index', 'rsv.transaccionesfinancieras.index']);

    try { $trxPasarelas = $pasarelas ?? \App\Models\Rsv\Pasarela::orderBy('nombre')->get(); }
    catch (\Throwable $e) { $trxPasarelas = collect(); }

    $trxReservasOpt = collect();
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $qResOpt = \App\Models\Rsv\Reserva::with(['inmueble', 'user', 'status'])
                ->when(!$trxGlobal && is_numeric($trxInmId), fn ($q) => $q->where('id_rsv_catalogo_inmueble', $trxInmId))
                ->orderByDesc('created_at')
                ->limit(150)
                ->get();
            $trxReservasOpt = $qResOpt;
        }
    } catch (\Throwable $eR) {}

    $trxEstados = ['Pendiente', 'Aprobado', 'Rechazado', 'Anulado'];
    $trxTotal   = method_exists($listadoTransacciones, 'total') ? $listadoTransacciones->total() : $listadoTransacciones->count();

    $trxFragmentoUrl = '';
    foreach (['rsv.admin.transacciones.fragmento', 'rsv.admin.finanzas.transacciones.fragmento', 'rsv.admin.dashboard'] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $trxFragmentoUrl = route($rn, ['inmueble_id' => $trxInmId ?? 'todos']);
            break;
        }
    }

    /* 6. Clasificador de soporte (PDF vs Imagen) y sumatorias de página */
    $trxInfoSoporte = function ($trx): array {
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

    $trxClasificarGrupo = function ($estRaw): string {
        $s = mb_strtolower(trim((string) $estRaw));
        if (in_array($s, ['aprobado', 'completado', 'pagado', 'ok'], true)) return 'aprobado';
        if (in_array($s, ['rechazado', 'cancelado', 'anulado', 'fallido'], true)) return 'anulado';
        return 'pendiente';
    };

    $trxDbgSoporte = 0;
    $trxDbgEstados = [];
    $trxGruposCnt  = ['all' => $listadoTransacciones->count(), 'aprobado' => 0, 'pendiente' => 0, 'anulado' => 0];
    $trxSumAprob   = 0.0;
    $trxSumPend    = 0.0;
    $trxSumAnul    = 0.0;

    foreach ($listadoTransacciones as $trxDbgItem) {
        $sopCheck = $trxInfoSoporte($trxDbgItem);
        if ($sopCheck['ok']) { $trxDbgSoporte++; }

        $trxDbgClave = mb_strtolower((string) ($trxDbgItem->estado_pago ?? 'pendiente'));
        $trxDbgEstados[$trxDbgClave] = ($trxDbgEstados[$trxDbgClave] ?? 0) + 1;

        $grp = $trxClasificarGrupo($trxDbgClave);
        $trxGruposCnt[$grp] = ($trxGruposCnt[$grp] ?? 0) + 1;
        $mto = (float) ($trxDbgItem->monto ?? 0);
        if ($grp === 'aprobado') $trxSumAprob += $mto;
        elseif ($grp === 'pendiente') $trxSumPend += $mto;
        else $trxSumAnul += $mto;
    }

    $trxSoftDeletes = false;
    if (!empty($trxModelo)) {
        try { $trxSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($trxModelo)); }
        catch (\Throwable $e) { $trxSoftDeletes = false; }
    }

    $trxDbg = [
        'version'       => 'TRX-v3.3-DBG',
        'vista'         => 'partials/finanzas/transacciones.blade.php',
        'render'        => now()->format('H:i:s'),
        'modo'          => $trxGlobal ? 'global' : ('inmueble #' . $trxInmId),
        'fuente_datos'  => $trxFuente,
        'rango_fechas'  => [
            'desde' => $trxVerTodo ? 'HISTÓRICO' : $trxDesde->format('Y-m-d'),
            'hasta' => $trxVerTodo ? 'HISTÓRICO' : $trxHasta->format('Y-m-d'),
            'modo'  => $trxVerTodo ? 'todo-el-historial' : ($trxMesPorDefecto ? 'mes-por-defecto' : 'personalizado'),
        ],
        'busqueda'      => ['q' => $trxQ !== '' ? $trxQ : null, 'ambito' => 'todas las páginas del rango'],
        'eliminacion_logica' => [
            'modelo_soft_deletes'  => $trxSoftDeletes,
            'anular_via'           => 'DELETE (spoofing) · estado_pago=Anulado + deleted_at',
            'modal_anular'         => true,
            'bloqueado_si_anulada' => true,
        ],
        'transacciones' => $trxTotal,
        'en_pagina'     => $listadoTransacciones->count(),
        'pagina'        => method_exists($listadoTransacciones, 'currentPage') ? $listadoTransacciones->currentPage() : 1,
        'por_pagina'    => method_exists($listadoTransacciones, 'perPage') ? $listadoTransacciones->perPage() : 'todo',
        'pasarelas'     => $trxPasarelas->count(),
        'con_soporte'   => $trxDbgSoporte,
        'estados'       => $trxDbgEstados,
        'editable'      => (bool) ($trxStore || $trxBase),
        'rutas'         => [
            'store'     => $trxStore        ?: 'NO definida',
            'index'     => $trxBase         ?: 'NO definida',
            'fragmento' => $trxFragmentoUrl ?: 'fallback URL actual',
        ],
        'errores'       => $trxDbgErrores,
    ];
@endphp

{{-- TRX-v3.3-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
<div id="trxDbgToggle" title="Debug Transacciones (TRX-v3.3-DBG)">
    <span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span>
</div>
<div id="trxDbgPanel" role="dialog" aria-label="Panel de debug de Transacciones Financieras">
    <div class="trx-dbg-header" id="trxDbgHeader">
        <span>DEBUG · Finanzas · Transacciones · TRX-v3.3-DBG</span>
        <span class="trx-dbg-controls">
            <button type="button" class="trx-dbg-btn" id="trxDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>
        </span>
    </div>
    <div id="trxDbgBody"></div>
</div>

<div id="sheet-transacciones" class="sheet-pane" data-frag="{{ $trxFragmentoUrl }}" data-inmueble-id="{{ $trxInmId ?? 'todos' }}">

    <div class="reservas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .table > :not(caption) > * > * { padding: 3px 7px; height: 22px; line-height: 1.15; vertical-align: middle; }
            .reservas-soft .table thead th { font-size: 8.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid-2) !important; }
            .reservas-soft .table tbody td { font-size: 8.5px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .reservas-soft .table tbody tr:hover > * { background-color: #f8fafc; --bs-table-accent-bg: transparent; }
            .reservas-soft .strong { color: var(--ink-2); font-weight: 700; }
            .reservas-soft .num { font-variant-numeric: tabular-nums; }
            .reservas-soft .sub { font-size: 7.5px; color: var(--ink-3); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2); font-family: inherit; font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 3px; transition: all .15s; }
            .reservas-soft .btn-mini:hover { color: var(--accent); border-color: var(--accent-bd); background: var(--accent-bg); }
            .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
            .reservas-soft .btn-green { background: var(--accent); color: #fff; border: 1px solid var(--accent); font-family: inherit; font-size: 8.5px; font-weight: 700; padding: 3px 10px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
            .reservas-soft .btn-green:hover { background: #2e5c43; border-color: #2e5c43; }
            .reservas-soft .xls-search { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 8px; box-shadow: none !important; outline: 0; min-width: 175px; }
            .reservas-soft .xls-search:focus { background: #fff; border-color: var(--accent-bd); }
            .reservas-soft .xls-fecha { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 4px; padding: 1px 6px; box-shadow: none !important; outline: 0; height: 23px; font-family: inherit; color-scheme: light; }
            .reservas-soft .xls-fecha:focus { background: #fff; border-color: var(--accent-bd); }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 2px 8px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }
            .reservas-soft .st-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; vertical-align: middle; margin-right: 3px; }
            .reservas-soft .st-warn { background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; color: #b91c1c; font-size: 8px; font-weight: 600; padding: 6px 9px; }

            /* ── CHIPS DE FILTRO POR ESTADO ── */
            .trx-chip-bar { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
            .trx-filter-chip {
                border: 1px solid var(--grid-2); background: #fff; color: var(--ink);
                font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
            }
            .trx-filter-chip:hover { border-color: var(--accent-bd); color: var(--ink-2); }
            .trx-filter-chip.active { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); font-weight: 700; }
            .trx-chip-cnt { font-size: 7px; font-weight: 700; padding: 0 5px; border-radius: 99px; background: var(--grid); color: var(--ink-2); }
            .trx-filter-chip.active .trx-chip-cnt { background: var(--accent); color: #fff; }

            .trx-row-thumb {
                width: 22px; height: 22px; border-radius: 4px; border: 1px solid var(--grid-2);
                object-fit: cover; cursor: pointer; vertical-align: middle; margin-right: 3px;
            }

            .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 8px; font-size: 9.5px; color: #64748b; }
            .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px; }
            .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .ps-err-msg { font-size: 7.5px; color: #b91c1c; font-weight: 600; }

            @keyframes destelloFila { 0%,100% { background-color: transparent; } 35% { background-color: rgba(16,185,129,.18); } }
            .reservas-soft tr.fila-nueva > td { animation: destelloFila 1.5s ease; }
            .reservas-soft tr.trx-anulada > td { opacity: .55; }
            .reservas-soft tr.trx-anulada .trx-monto-val { text-decoration: line-through; }

            @keyframes trxSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .reservas-soft .trx-spin { animation: trxSpin .8s linear infinite; }
            @keyframes trxPulseFade { 0% { background-color: #e3f2e8; } 100% { background-color: transparent; } }
            .reservas-soft.trx-pulse { animation: trxPulseFade .9s ease-out 1; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #trxDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
            #trxDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
            .trx-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .trx-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: trxDbgBlink 1s steps(2,start) infinite; }
            @keyframes trxDbgBlink { 50% { opacity: .35; } }
            .trx-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
            #trxDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
            .trx-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .trx-dbg-controls { display: flex; gap: 4px; }
            .trx-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .trx-dbg-btn:hover { background: #475569; }
            #trxDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .trx-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .trx-dbg-time { color: #64748b; margin-right: 4px; }
            .trx-dbg-info { color: #93c5fd; } .trx-dbg-ok { color: #86efac; }
            .trx-dbg-warn { color: #fde68a; } .trx-dbg-err { color: #fca5a5; }
            .trx-dbg-net { color: #c4b5fd; } .trx-dbg-debug { color: #94a3b8; }
        </style>

        {{-- ALERTAS --}}
        @if (session('success'))
            <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            </div>
        @endif

        <!-- HEADER FILA 1: TÍTULO + FILTRO DE FECHAS + ACCIONES -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h6 class="fw-bold mb-0" style="font-size: 10.5px; color: #334155;">
                    <i class="bi bi-receipt-cutoff text-success me-1"></i> Transacciones Financieras
                </h6>
                <span class="badge rounded-pill" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; font-family: ui-monospace, monospace;">
                    TRX-v3.3-DBG
                </span>
                @if(!$trxGlobal)
                    <span class="count-badge" title="Filtro activo: transacciones de un solo inmueble">
                        <i class="bi bi-building text-success"></i>
                        {{ is_object($trxInm) ? ($trxInm->name ?? ('Inmueble #' . $trxInmId)) : ('Inmueble #' . $trxInmId) }}
                    </span>
                @endif
                <span class="count-badge" title="Transacciones encontradas en el rango">
                    <b>{{ $trxTotal }}</b> registro(s)
                </span>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                <span class="count-badge" title="Rango de fechas activo">
                    <i class="bi bi-calendar3 text-success"></i>
                    {{ $trxVerTodo ? 'Histórico completo' : ($trxDesde->format('d/m/Y') . ' – ' . $trxHasta->format('d/m/Y')) }}
                </span>
                <input type="date" id="trxFechaDesde" class="xls-fecha" value="{{ $trxDesde->format('Y-m-d') }}" title="Desde" autocomplete="off">
                <input type="date" id="trxFechaHasta" class="xls-fecha" value="{{ $trxHasta->format('Y-m-d') }}" title="Hasta" autocomplete="off">
                <button type="button" id="trxAplicarFechas" class="btn-mini" title="Aplicar rango de fechas">
                    <i class="bi bi-funnel"></i> Aplicar
                </button>
                <button type="button" id="trxMesActual" class="btn-mini" title="Volver al mes actual">
                    <i class="bi bi-calendar-week"></i> Este mes
                </button>
                <button type="button" id="trxVerTodoBtn" class="btn-mini {{ $trxVerTodo ? 'border-success text-success' : '' }}" data-todo="{{ $trxVerTodo ? '1' : '0' }}" title="Ver todas las transacciones sin límite de mes">
                    <i class="bi bi-infinity"></i> Todo
                </button>
                <button type="button" id="trxRefresh" class="btn-mini" title="Recargar transacciones por AJAX">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
                <button type="button" id="trxExport" class="btn-mini" title="Exportar a Excel CSV">
                    <i class="bi bi-download"></i> CSV
                </button>
                @if($trxStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#trxModalCreate">
                        <i class="bi bi-plus-lg"></i> Nueva Transacción
                    </button>
                @endif
            </div>
        </div>

        <!-- HEADER FILA 2: CHIPS DE ESTADO + RESUMEN FINANCIERO DE PÁGINA + BUSCADOR EN VIVO -->
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap" style="gap: 8px; border-color: var(--grid) !important;">
            <div class="trx-chip-bar">
                <button type="button" class="trx-filter-chip active" data-grp="">
                    Todos <span class="trx-chip-cnt">{{ $trxGruposCnt['all'] }}</span>
                </button>
                <button type="button" class="trx-filter-chip" data-grp="aprobado">
                    <span class="st-dot" style="background:#15803d;"></span>Aprobados (${{ number_format($trxSumAprob, 0) }}) <span class="trx-chip-cnt">{{ $trxGruposCnt['aprobado'] }}</span>
                </button>
                <button type="button" class="trx-filter-chip" data-grp="pendiente">
                    <span class="st-dot" style="background:#b45309;"></span>Pendientes (${{ number_format($trxSumPend, 0) }}) <span class="trx-chip-cnt">{{ $trxGruposCnt['pendiente'] }}</span>
                </button>
                <button type="button" class="trx-filter-chip" data-grp="anulado">
                    <span class="st-dot" style="background:#b91c1c;"></span>Anulados/Rech. <span class="trx-chip-cnt">{{ $trxGruposCnt['anulado'] }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <input type="text" id="trxSearch" class="xls-search" placeholder="Buscar ref, #reserva, canal, monto…" value="{{ $trxQ }}" autocomplete="off" enterkeyhint="search">
            </div>
        </div>

        <!-- TABLA DE TRANSACCIONES -->
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 880px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 30px;">#</th>
                        <th>Referencia / ID</th>
                        <th>Reserva e Inmueble</th>
                        <th>Pasarela / Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Soporte Cargado</th>
                        <th class="text-end" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyTransacciones">

                    @forelse($listadoTransacciones as $trx)
                        @php
                            $estadoRaw = (string) ($trx->estado_pago ?? 'Pendiente');
                            $estadoLow = mb_strtolower($estadoRaw);
                            $grpEst    = $trxClasificarGrupo($estadoLow);
                            $esAnulada = ($grpEst === 'anulado');

                            $colorEst = match($grpEst) {
                                'aprobado' => '#15803d',
                                'anulado'  => '#b91c1c',
                                default    => '#b45309',
                            };

                            $sopInfo    = $trxInfoSoporte($trx);
                            $conSoporte = $sopInfo['ok'];
                            $codRes     = $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R';
                            $nomInm     = data_get($trx, 'reserva.inmueble.name') ?? '';
                            $nomUser    = data_get($trx, 'reserva.user.name') ?? '';
                            $pasNombre  = $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Pasarela Directa';

                            $trxBuscar = mb_strtolower(trim(
                                ($trx->referencia_externa ?? '') . ' ' .
                                ($trx->id ?? '') . ' ' .
                                $codRes . ' ' .
                                $nomInm . ' ' .
                                $nomUser . ' ' .
                                $pasNombre . ' ' .
                                ($trx->metodo_pago ?? '') . ' ' .
                                $estadoRaw . ' ' .
                                ($trx->monto ?? '')
                            ));
                        @endphp

                        <tr class="trx-row{{ $esAnulada ? ' trx-anulada' : '' }}"
                            data-id="{{ $trx->id }}"
                            data-grp="{{ $grpEst }}"
                            data-trx-search="{{ $trxBuscar }}">
                            <td class="text-center sub">{{ $loop->iteration }}</td>
                            <td class="strong">
                                {{ $trx->referencia_externa ?: 'SIN-REF' }}
                                <div class="sub">ID #{{ $trx->id }}</div>
                            </td>
                            <td>
                                <span class="strong" style="color: var(--accent);">#{{ $codRes }}</span>
                                @if($nomInm || $nomUser)
                                    <div class="sub text-truncate" style="max-width: 195px;" title="{{ $nomInm }} {{ $nomUser ? '· ' . $nomUser : '' }}">
                                        {{ $nomInm ?: $nomUser }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <i class="bi {{ $trx->pasarela->icono ?? 'bi-shield-check' }} me-1" style="font-size: 8.5px; color: {{ $trx->pasarela->color ?? '#3d7a5c' }};"></i>{{ $pasNombre }}
                                <div class="sub">{{ $trx->metodo_pago ?: 'General' }}</div>
                            </td>
                            <td class="text-end strong num trx-monto-val">
                                ${{ number_format((float) ($trx->monto ?? 0), 2) }}
                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                            </td>
                            <td class="strong">
                                <span class="st-dot" style="background: {{ $colorEst }};"></span>{{ strtoupper($estadoRaw) }}
                            </td>
                            <td class="sub num">
                                {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                            </td>

                            {{-- SOLO MUESTRA EL BOTÓN DE LO QUE REALMENTE SE CARGÓ (PDF O IMAGEN) --}}
                            <td class="text-center">
                                @if($conSoporte)
                                    @if($sopInfo['tipo'] === 'image')
                                        <img src="{{ $sopInfo['url'] }}" alt="Soporte"
                                             class="trx-row-thumb js-trx-preview-soporte"
                                             data-url="{{ $sopInfo['url'] }}"
                                             data-tipo="image"
                                             data-titulo="Imagen Soporte · TX #{{ $trx->id }} · Reserva #{{ $codRes }}">
                                        <button type="button"
                                                class="btn-mini js-trx-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="image"
                                                data-titulo="Imagen Soporte · TX #{{ $trx->id }} · Reserva #{{ $codRes }}">
                                            <i class="bi bi-file-earmark-image text-success"></i> Imagen
                                        </button>
                                    @elseif($sopInfo['tipo'] === 'pdf')
                                        <button type="button"
                                                class="btn-mini js-trx-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="pdf"
                                                data-titulo="PDF Soporte · TX #{{ $trx->id }} · Reserva #{{ $codRes }}">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn-mini js-trx-preview-soporte"
                                                data-url="{{ $sopInfo['url'] }}"
                                                data-tipo="auto"
                                                data-titulo="Soporte · TX #{{ $trx->id }} · Reserva #{{ $codRes }}">
                                            <i class="bi bi-paperclip text-primary"></i> Ver
                                        </button>
                                    @endif
                                @else
                                    <span class="sub fst-italic">Sin soporte</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <button type="button" class="btn-mini js-ver-trx"
                                        data-id="{{ $trx->id }}"
                                        data-referencia="{{ $trx->referencia_externa ?? 'N/A' }}"
                                        data-reserva="#{{ $codRes }}"
                                        data-inmueble="{{ $nomInm }}"
                                        data-pasarela="{{ $pasNombre }}"
                                        data-metodo="{{ $trx->metodo_pago ?? 'N/A' }}"
                                        data-monto="{{ number_format((float) ($trx->monto ?? 0), 2) }}"
                                        data-moneda="{{ $trx->moneda ?? 'COP' }}"
                                        data-estado="{{ strtoupper($estadoRaw) }}"
                                        data-fecha="{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i:s') : 'N/A' }}"
                                        data-soporte="{{ $conSoporte ? 1 : 0 }}"
                                        data-soporte-tipo="{{ $sopInfo['tipo'] }}"
                                        data-soporte-url="{{ $sopInfo['url'] ?: '#' }}">Ver</button>
                                @if($trxBase)
                                    <button type="button" class="btn-mini" data-bs-toggle="modal" data-bs-target="#trxEditModal-{{ $trx->id }}">Editar</button>
                                    <button type="button" class="btn-mini text-danger" data-bs-toggle="modal" data-bs-target="#trxDelModal-{{ $trx->id }}">Anular</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #94a3b8;">
                                <i class="bi bi-inbox d-block mb-1" style="font-size: 18px;"></i>
                                {{ $trxQ !== '' ? 'Ninguna transacción coincide con «' . $trxQ . '» en el rango seleccionado.' : 'No hay transacciones en el rango de fechas seleccionado. Puedes pulsar "Todo" para ver el histórico completo.' }}
                            </td>
                        </tr>
                    @endforelse

                    <tr class="trx-sin-res" style="display:none;">
                        <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #94a3b8;">
                            <i class="bi bi-search me-1"></i> Ninguna transacción coincide con el filtro activo.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        @if(method_exists($listadoTransacciones, 'links'))
            <div class="mt-2 text-center" style="font-size: 8.5px;">
                {{ $listadoTransacciones->appends(request()->query())->links() }}
            </div>
        @endif

        {{-- MODAL CREAR CON SELECTOR INTELIGENTE DE RESERVA --}}
        @if($trxStore)
        <div class="modal fade reservas-soft-modal" id="trxModalCreate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.92rem;">
                            <i class="bi bi-plus-circle text-success me-1"></i> Registrar Nueva Transacción
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    </div>
                    <form action="{{ $trxStore }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="st-warn mb-2 d-none ps-err-general"></div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Reserva Destino <span class="text-danger">*</span></label>
                                    @if($trxReservasOpt->isNotEmpty())
                                        <select class="form-select form-select-sm js-trx-select-reserva" name="id_rsv_reservas" required>
                                            <option value="">— Seleccione la reserva —</option>
                                            @foreach($trxReservasOpt as $rOpt)
                                                @php
                                                    $mTot = (float) ($rOpt->monto_total ?? 0);
                                                    $stNom = optional($rOpt->status)->name ?? 'Registrada';
                                                    $uNom  = optional($rOpt->user)->name ?? '';
                                                    $iNom  = optional($rOpt->inmueble)->name ?? '';
                                                @endphp
                                                <option value="{{ $rOpt->id }}" data-monto="{{ $mTot }}">
                                                    #{{ $rOpt->codigo_reserva ?? $rOpt->id }} · {{ $iNom }} {{ $uNom ? '· ' . $uNom : '' }} (${{ number_format($mTot, 0) }}) [{{ $stNom }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="number" min="1" class="form-control form-control-sm" name="id_rsv_reservas" required placeholder="ID de la Reserva (Ej: 278)">
                                    @endif
                                    <div class="ps-err-msg" data-error-for="id_rsv_reservas"></div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Monto <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm js-trx-input-monto" name="monto" required placeholder="0.00">
                                    <div class="ps-err-msg" data-error-for="monto"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Moneda</label>
                                    <input type="text" class="form-control form-control-sm" name="moneda" maxlength="5" value="COP">
                                    <div class="ps-err-msg" data-error-for="moneda"></div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Pasarela / Canal</label>
                                    <select class="form-select form-select-sm" name="id_rsv_pasarela">
                                        <option value="">— Directo (auto) —</option>
                                        @foreach($trxPasarelas as $gp)
                                            <option value="{{ $gp->id }}">{{ $gp->nombre ?? $gp->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="id_rsv_pasarela"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Método de pago</label>
                                    <input type="text" class="form-control form-control-sm" name="metodo_pago" maxlength="60" placeholder="Transferencia, PSE, Efectivo…">
                                    <div class="ps-err-msg" data-error-for="metodo_pago"></div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Estado Inicial</label>
                                    <select class="form-select form-select-sm" name="estado_pago">
                                        @foreach($trxEstados as $est)
                                            <option value="{{ $est }}" {{ $est === 'Aprobado' ? 'selected' : '' }}>{{ strtoupper($est) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="estado_pago"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Referencia externa / CUS</label>
                                    <input type="text" class="form-control form-control-sm" name="referencia_externa" maxlength="100" placeholder="N° comprobante…">
                                    <div class="ps-err-msg" data-error-for="referencia_externa"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Soporte de pago (Imagen o PDF)</label>
                                    <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                    <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Registrar Pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- MODALES EDITAR Y ANULAR POR FILA --}}
        @foreach($listadoTransacciones as $trx)
            @if($trxBase)
            <div class="modal fade reservas-soft-modal" id="trxEditModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.92rem;">Editar Transacción #{{ $trx->id }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        </div>
                        <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data" data-trx-id="{{ $trx->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-3">
                                <div class="st-warn mb-2 d-none ps-err-general"></div>
                                <div class="mb-2" style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 5px; padding: 6px 9px; font-size: 8.5px; color: #64748b;">
                                    Reserva <b>#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</b>
                                    @if(data_get($trx, 'reserva.inmueble.name')) · {{ data_get($trx, 'reserva.inmueble.name') }} @endif
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label">Monto <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required value="{{ $trx->monto }}">
                                        <div class="ps-err-msg" data-error-for="monto"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Moneda</label>
                                        <input type="text" class="form-control form-control-sm" name="moneda" maxlength="5" value="{{ $trx->moneda ?? 'COP' }}">
                                        <div class="ps-err-msg" data-error-for="moneda"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Pasarela</label>
                                        <select class="form-select form-select-sm" name="id_rsv_pasarela">
                                            @foreach($trxPasarelas as $gp)
                                                <option value="{{ $gp->id }}" {{ ($trx->id_rsv_pasarela ?? $trx->id_rsv_pasarelas) == $gp->id ? 'selected' : '' }}>{{ $gp->nombre ?? $gp->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="ps-err-msg" data-error-for="id_rsv_pasarela"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Método</label>
                                        <input type="text" class="form-control form-control-sm" name="metodo_pago" maxlength="60" value="{{ $trx->metodo_pago ?? '' }}">
                                        <div class="ps-err-msg" data-error-for="metodo_pago"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select form-select-sm" name="estado_pago">
                                            @foreach($trxEstados as $est)
                                                <option value="{{ $est }}" {{ strtolower($est) === strtolower((string) ($trx->estado_pago ?? 'Pendiente')) ? 'selected' : '' }}>{{ strtoupper($est) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="ps-err-msg" data-error-for="estado_pago"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Referencia</label>
                                        <input type="text" class="form-control form-control-sm" name="referencia_externa" maxlength="100" value="{{ $trx->referencia_externa ?? '' }}">
                                        <div class="ps-err-msg" data-error-for="referencia_externa"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Reemplazar soporte (Imagen o PDF)</label>
                                        <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                        <div class="sub">Deja vacío para conservar el soporte actual</div>
                                        <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade reservas-soft-modal" id="trxDelModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-body p-3 text-center">
                            <i class="bi bi-slash-circle text-danger" style="font-size: 1.3rem;"></i>
                            <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Anular transacción</h6>
                            <p class="mb-2" style="font-size: 8.5px; color: #64748b;">
                                Se marcará <b style="color: #334155;">#{{ $trx->referencia_externa ?? $trx->id }}</b> por <b style="color: #334155;">${{ number_format((float) ($trx->monto ?? 0), 2) }}</b> como <b>ANULADA</b>.
                            </p>
                            <div class="st-warn mb-2 d-none ps-err-general"></div>

                            @if(strtolower((string) ($trx->estado_pago ?? '')) === 'anulado')
                                <div class="st-warn mb-2">
                                    <i class="bi bi-lock-fill me-1"></i> Esta transacción ya está ANULADA.
                                </div>
                                <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled style="font-size: 10.5px; background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; cursor: not-allowed;">
                                    <i class="bi bi-slash-circle"></i> Anulada
                                </button>
                            @else
                                <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" data-trx-id="{{ $trx->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm py-1 px-2 w-100" style="font-size: 10.5px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700;">
                                        <i class="bi bi-slash-circle"></i> Confirmar anulación
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

    </div>
</div>

{{-- MODAL DETALLE COMPARTIDO --}}
<div class="modal fade reservas-soft-modal" id="modalTransaccionDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.92rem;">Detalle de transacción</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body p-3">
                <div style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 5px; padding: 6px 9px; font-size: 8.5px; color: #64748b; margin-bottom: 8px;">
                    Referencia <b id="fdRef" style="color:#334155;">—</b><br>
                    <span id="fdId">ID interno #—</span>
                </div>
                <table class="table table-sm table-borderless mb-0" style="font-size: 8.5px;">
                    <tr><td class="text-uppercase fw-bold text-muted" style="width:75px;">Reserva</td><td id="fdReserva" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Inmueble</td><td id="fdInmueble" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Pasarela</td><td id="fdPasarela" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Método</td><td id="fdMetodo" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Monto</td><td id="fdMonto" class="strong text-end" style="color:#3d7a5c;">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Estado</td><td id="fdEstado" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Fecha</td><td id="fdFecha" class="text-end text-muted">—</td></tr>
                </table>
                <div id="fdSoporteCaja" class="mt-2 text-center" style="border-top: 1px solid #f1f5f9; padding-top: 8px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 10.5px;" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL VISOR RÁPIDO DE SOPORTES (IMAGEN O PDF) --}}
<div class="modal fade" id="trxModalPreviewSoporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: 0; border-radius: 10px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <span class="fw-bold" style="font-size: 10.5px;" id="trxModalPreviewTitle">
                    <i class="bi bi-eye text-success me-1"></i> Vista Previa del Soporte
                </span>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" target="_blank" id="trxModalPreviewExt" class="btn btn-sm btn-outline-light py-0 px-2" style="font-size: 9px;">
                        <i class="bi bi-box-arrow-up-right"></i> Abrir en pestaña
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                </div>
            </div>
            <div class="modal-body p-2 bg-light text-center" id="trxModalPreviewBody" style="min-height: 360px; display: flex; align-items: center; justify-content: center;">
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    if (window.__jsTransaccionesV33Listo) return;
    window.__jsTransaccionesV33Listo = true;

    var TRX_FRAG_URL  = '{!! $trxFragmentoUrl ?? "" !!}';
    var PAGINA_ACTUAL = 1;
    var TRX_TODO      = {{ $trxVerTodo ? 'true' : 'false' }};
    var TRX_Q         = '';
    var TRX_Q_ENVIADO = '';
    var TRX_Q_SEQ     = 0;
    var TRX_Q_TIMER   = null;

    /* ═══ PANEL DE DEBUG LOCAL TRX-v3.3-DBG (diseño TIP-v1.3) ═══ */
    var DBG = (window.TrxDbg && window.TrxDbg.alive && window.TrxDbg.alive())
        ? window.TrxDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.getElementById('sheet-transacciones');
                if (!rootDbg) { return; }
                var dups = document.querySelectorAll('#trxDbgToggle');
                for (var d = 1; d < dups.length; d++) { if (dups[d].parentNode) { dups[d].parentNode.removeChild(dups[d]); } }
                dups = document.querySelectorAll('#trxDbgPanel');
                for (var d2 = 1; d2 < dups.length; d2++) { if (dups[d2].parentNode) { dups[d2].parentNode.removeChild(dups[d2]); } }
                if (!$('trxDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'trxDbgToggle';
                    t.title = 'Debug Transacciones (TRX-v3.3-DBG)';
                    t.innerHTML = '<span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('trxDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'trxDbgPanel';
                    p.innerHTML = '<div class="trx-dbg-header" id="trxDbgHeader">' +
                        '<span>DEBUG · Finanzas · Transacciones · TRX-v3.3-DBG</span>' +
                        '<span class="trx-dbg-controls">' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>' +
                        '</span></div><div id="trxDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle', 'finDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; }
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('trxDbgToggle'), p = $('trxDbgPanel');
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
                    var b = $('trxDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'trx-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="trx-dbg-time">[' + hora() + ']</span>' +
                        '<span class="trx-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('trxDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('trxDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('trxDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('trxDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('trxDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('trxDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('trxDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('trxDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('trxDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG transacciones TRX-v3.3-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        });
                    }
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('trxDbgToggle'); }
            };
            window.TrxDbg = api;
            return api;
        })();

    if (DBG) { DBG.ensure(); }
    var trxdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    var SRVDBG = @json($trxDbg);
    if (DBG) {
        trxdbg('TRX-v3.3-DBG activo · modo=' + (SRVDBG ? SRVDBG.modo : '?') + ' · fuente=' + (SRVDBG ? SRVDBG.fuente_datos : '?'), 'ok');
        trxdbg('init · total=' + (SRVDBG ? SRVDBG.transacciones : 0) + ' · en página=' + (SRVDBG ? SRVDBG.en_pagina : 0) +
            ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : 0) + ' · con soporte=' + (SRVDBG ? SRVDBG.con_soporte : 0), 'info');
        if (SRVDBG && SRVDBG.rango_fechas) {
            trxdbg('fechas · ' + SRVDBG.rango_fechas.desde + ' → ' + SRVDBG.rango_fechas.hasta + ' (' + SRVDBG.rango_fechas.modo + ')', 'debug');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            SRVDBG.errores.forEach(function (er) { trxdbg('⚠ SERVIDOR: ' + er, 'err'); });
        }
    }

    function cerrarBruto(el) {
        if (!el) return;
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
        el.removeAttribute('aria-modal');
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }
    function ocultarModal(el) {
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) {
            try { window.bootstrap.Modal.getOrCreateInstance(el).hide(); } catch (e) {}
        }
        cerrarBruto(el);
    }
    function limpiarResiduos() {
        document.querySelectorAll('.modal.show').forEach(function (m) { cerrarBruto(m); });
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }
    function unicarModales() {
        var hojas = document.querySelectorAll('#sheet-transacciones');
        hojas.forEach(function (hoja, idx) {
            if (idx === 0) return;
            hoja.querySelectorAll('.modal[id]').forEach(function (m) {
                if (m.id.indexOf('-c' + (idx + 1)) !== -1) return;
                var viejo = m.id; m.id = viejo + '-c' + (idx + 1);
                hoja.querySelectorAll('[data-bs-target="#' + viejo + '"]').forEach(function (b) { b.setAttribute('data-bs-target', '#' + m.id); });
            });
        });
    }

    function avisar(mensaje, tipo) {
        trxdbg((tipo === 'danger' ? 'aviso · ' : 'resultado · ') + mensaje, tipo === 'danger' ? 'err' : 'ok');
        if (!window.bootstrap || !window.bootstrap.Toast) { return; }
        var div = document.createElement('div');
        div.className = 'toast align-items-center text-bg-' + (tipo === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed m-3';
        div.setAttribute('role', 'alert'); div.style.cssText = 'bottom:18px;right:14px;z-index:2100;font-size:10px;';
        div.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">' + mensaje + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>';
        document.body.appendChild(div);
        div.addEventListener('hidden.bs.toast', function () { div.remove(); });
        window.bootstrap.Toast.getOrCreateInstance(div, { delay: 2600 }).show();
    }

    function pintarErrores(form, errores) {
        form.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
        form.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
        var g = form.querySelector('.ps-err-general');
        if (g) { g.classList.add('d-none'); g.textContent = ''; }
        var sobrantes = [];
        Object.keys(errores || {}).forEach(function (campo) {
            var msg = [].concat(errores[campo])[0];
            var caja = form.querySelector('[data-error-for="' + campo + '"]');
            if (caja) { caja.textContent = msg; } else { sobrantes.push(campo + ': ' + msg); }
            var input = form.querySelector('[name="' + campo + '"]');
            if (input) input.classList.add('is-invalid');
        });
        if (sobrantes.length) errorGeneral(form, sobrantes.join(' · '));
    }
    function errorGeneral(form, msg) {
        var g = form.querySelector('.ps-err-general');
        if (!g) { return; }
        g.textContent = msg; g.classList.remove('d-none');
    }
    function resaltarFila(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-transacciones tr[data-id="' + id + '"]').forEach(function (fila) {
            fila.classList.add('fila-nueva');
            setTimeout(function () { fila.classList.remove('fila-nueva'); }, 1600);
        });
    }

    function marcarFilaAnuladaEnDom(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-transacciones tr[data-id="' + id + '"]').forEach(function (tr) {
            tr.classList.add('trx-anulada');
            tr.setAttribute('data-grp', 'anulado');
            var tds = tr.querySelectorAll('td');
            if (tds[5]) {
                tds[5].innerHTML = '<span class="st-dot" style="background: #b91c1c;"></span>ANULADO';
            }
            var btnVer = tr.querySelector('.js-ver-trx');
            if (btnVer) { btnVer.dataset.estado = 'ANULADO'; }
        });
        aplicarFiltro();
    }

    function leerRangoFechas() {
        var d = document.querySelector('#sheet-transacciones #trxFechaDesde');
        var h = document.querySelector('#sheet-transacciones #trxFechaHasta');
        return { desde: d ? (d.value || '') : '', hasta: h ? (h.value || '') : '' };
    }
    function aplicarRangoFechas(origen) {
        TRX_TODO = false;
        PAGINA_ACTUAL = 1;
        var r = leerRangoFechas();
        trxdbg('fechas · ' + (origen || 'Aplicar') + ' · ' + (r.desde || 'inicio-mes') + ' → ' + (r.hasta || 'fin-mes'), 'net');
        refrescarTabla(null, 1);
    }
    function ponerMesActual() {
        TRX_TODO = false;
        var hoy = new Date(), y = hoy.getFullYear(), m = hoy.getMonth();
        function fm(n) { return (n < 10 ? '0' : '') + n; }
        var primero = y + '-' + fm(m + 1) + '-01';
        var ult = new Date(y, m + 1, 0);
        var fin = ult.getFullYear() + '-' + fm(ult.getMonth() + 1) + '-' + fm(ult.getDate());
        document.querySelectorAll('#sheet-transacciones #trxFechaDesde').forEach(function (i) { i.value = primero; });
        document.querySelectorAll('#sheet-transacciones #trxFechaHasta').forEach(function (i) { i.value = fin; });
        PAGINA_ACTUAL = 1;
        refrescarTabla(null, 1);
    }
    function alternarVerTodo() {
        TRX_TODO = !TRX_TODO;
        PAGINA_ACTUAL = 1;
        trxdbg('fechas · modo Histórico Completo=' + (TRX_TODO ? 'SÍ' : 'NO'), 'info');
        refrescarTabla(null, 1);
    }

    function urlFragmento(pagina) {
        var root = document.getElementById('sheet-transacciones');
        var fragAttr = (root && root.getAttribute('data-frag')) || TRX_FRAG_URL || window.location.pathname;
        var base = fragAttr.split('?')[0];
        var inmBase = new URLSearchParams(fragAttr.split('?')[1] || '').get('inmueble_id');
        var inmLoc  = new URLSearchParams(window.location.search).get('inmueble_id');
        var qs = new URLSearchParams();
        qs.set('inmueble_id', inmLoc || inmBase || (root && root.getAttribute('data-inmueble-id')) || 'todos');

        if (TRX_TODO) {
            qs.set('todo', '1');
        } else {
            var rango = leerRangoFechas();
            if (rango.desde) { qs.set('fecha_desde', rango.desde); }
            if (rango.hasta) { qs.set('fecha_hasta', rango.hasta); }
        }
        if (TRX_Q) { qs.set('q', TRX_Q); }
        var pg = pagina || PAGINA_ACTUAL || 1;
        if (pg > 1) qs.set('page_finanzas', pg);
        qs.set('_t', Date.now());
        return base + '?' + qs.toString();
    }

    function refrescarTabla(idResaltar, pagina) {
        TRX_Q_SEQ++;
        var url = urlFragmento(pagina);
        var __t0 = Date.now();
        trxdbg('GET Fragmento (página ' + (pagina || PAGINA_ACTUAL) + ') → ' + url, 'net');

        return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nuevo = doc.querySelector('#sheet-transacciones');
                var copias = document.querySelectorAll('#sheet-transacciones');
                if (!nuevo || copias.length === 0) {
                    trxdbg('Aviso: respuesta sin #sheet-transacciones', 'warn');
                    return;
                }
                limpiarResiduos();
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                if (TRX_Q) {
                    document.querySelectorAll('#sheet-transacciones #trxSearch').forEach(function (i2) { i2.value = TRX_Q; });
                }
                unicarModales();
                aplicarFiltro();
                if (idResaltar) resaltarFila(idResaltar);
                document.querySelectorAll('#sheet-transacciones > .reservas-soft').forEach(function (t) {
                    t.classList.remove('trx-pulse'); void t.offsetWidth; t.classList.add('trx-pulse');
                });
                if (DBG && DBG.ensure) { DBG.ensure(); }
                trxdbg('Transacciones actualizadas en ' + (Date.now() - __t0) + 'ms · copias=' + copias.length, 'ok');
            })
            .catch(function (err) {
                trxdbg('Error refrescando fragmento: ' + (err && err.message ? err.message : 'red'), 'err');
            });
    }
    window.trxReloadFragment = refrescarTabla;

    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]');
        var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }

        var tokIn = form.querySelector('input[name="_token"]');
        var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';
        var mIn   = form.querySelector('input[name="_method"]');
        var metodoReal = (mIn && mIn.value) ? String(mIn.value).toUpperCase() : 'POST';
        var idTrxTarget = form.getAttribute('data-trx-id') || null;

        trxdbg('AJAX · ' + metodoReal + ' ' + form.getAttribute('action'), 'net');
        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) {
            return r.text().then(function (raw) {
                var j = null;
                try { j = JSON.parse(raw); } catch (e) {}
                return { ok: r.ok, status: r.status, json: j };
            });
        })
        .then(function (res) {
            if (res.ok && (!res.json || res.json.success !== false)) {
                var d = (res.json && res.json.data) || {};
                var idFila = d.id || (d.data && d.data.id) || idTrxTarget || null;

                ocultarModal(form.closest('.modal'));

                if (metodoReal === 'DELETE' && idFila) {
                    marcarFilaAnuladaEnDom(idFila);
                }

                avisar((res.json && res.json.message) || (metodoReal === 'DELETE' ? 'Transacción anulada correctamente' : 'Guardado correctamente'), 'success');

                refrescarTabla(idFila, PAGINA_ACTUAL);
                if (typeof window.finTabReloadFragment === 'function') {
                    window.finTabReloadFragment(null, 'sync-desde-transacciones');
                }
            } else if (res.status === 422 && res.json && res.json.errors) {
                trxdbg('AJAX 422 · ' + Object.keys(res.json.errors).join(', '), 'err');
                pintarErrores(form, res.json.errors);
            } else {
                errorGeneral(form, (res.json && res.json.message) || 'No se pudo completar la operación.');
            }
        })
        .catch(function () {
            errorGeneral(form, 'Error de conexión. Intenta de nuevo.');
        })
        .finally(function () {
            if (btn) { btn.disabled = false; btn.innerHTML = txt; }
        });
    }

    function aplicarFiltro(inp) {
        inp = inp || document.getElementById('trxSearch');
        var chip = document.querySelector('#sheet-transacciones .trx-filter-chip.active');
        var grp  = chip ? (chip.getAttribute('data-grp') || '') : '';
        var val  = inp ? (inp.value || '').toLowerCase().trim() : '';
        var visibles = 0;

        document.querySelectorAll('#sheet-transacciones tr[data-trx-search]').forEach(function (tr) {
            var okQ = !val || (tr.getAttribute('data-trx-search') || '').indexOf(val) !== -1;
            var okG = !grp || (tr.getAttribute('data-grp') || '') === grp;
            var mostrar = okQ && okG;
            tr.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        document.querySelectorAll('#sheet-transacciones tr.trx-sin-res').forEach(function (tr) {
            tr.style.display = ((val || grp) && visibles === 0) ? '' : 'none';
        });
    }

    function buscarServidor(valor) {
        if (valor === TRX_Q_ENVIADO) { return; }
        TRX_Q_ENVIADO = valor; TRX_Q = valor;
        var seq = ++TRX_Q_SEQ;
        PAGINA_ACTUAL = 1;
        var url = urlFragmento(1);
        aplicarFiltro();
        if (!url) return;

        var __tq = Date.now();
        trxdbg('búsqueda en vivo · "' + (valor || '(vacía)') + '"…', 'net');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                if (seq !== TRX_Q_SEQ) { return; }
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nuevo = doc.querySelector('#sheet-transacciones');
                var copias = document.querySelectorAll('#sheet-transacciones');
                if (!nuevo || copias.length === 0) return;

                var activo = document.activeElement, idxCopia = -1, pos = valor.length;
                if (activo && activo.id === 'trxSearch') {
                    var hojas = Array.prototype.slice.call(document.querySelectorAll('#sheet-transacciones'));
                    idxCopia = hojas.indexOf(activo.closest('#sheet-transacciones'));
                    try { pos = (typeof activo.selectionStart === 'number') ? activo.selectionStart : valor.length; } catch (e1) {}
                }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                var hojasNuevas = document.querySelectorAll('#sheet-transacciones');
                hojasNuevas.forEach(function (h2) { var i2 = h2.querySelector('#trxSearch'); if (i2) { i2.value = valor; } });
                if (idxCopia >= 0 && hojasNuevas[idxCopia]) {
                    var inp2 = hojasNuevas[idxCopia].querySelector('#trxSearch');
                    if (inp2) { inp2.focus(); try { inp2.setSelectionRange(pos, pos); } catch (e2) {} }
                }
                unicarModales(); aplicarFiltro(); limpiarResiduos();
                if (DBG && DBG.ensure) { DBG.ensure(); }
                trxdbg('búsqueda "' + (valor || '(vacía)') + '" completada en ' + (Date.now() - __tq) + 'ms', 'ok');
            })
            .catch(function () {});
    }

    function abrirVisorSoporteTrx(url, tipo, titulo) {
        if (!url || url === '#') return;
        var modalEl = document.getElementById('trxModalPreviewSoporte');
        var bodyEl  = document.getElementById('trxModalPreviewBody');
        var titleEl = document.getElementById('trxModalPreviewTitle');
        var extEl   = document.getElementById('trxModalPreviewExt');
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
        trxdbg('Vista previa abierta: ' + (titulo || url), 'info');
    }

    function exportarTrx() {
        var vistos = {}; var filas = [];
        document.querySelectorAll('#sheet-transacciones tr[data-trx-search]').forEach(function (tr) {
            if (tr.style.display === 'none') return;
            var idFila = tr.getAttribute('data-id');
            if (idFila) { if (vistos[idFila]) return; vistos[idFila] = 1; }
            var b = tr.querySelector('.js-ver-trx');
            if (!b) return;
            var d = b.dataset;
            filas.push([d.referencia || 'N/A', 'ID ' + (d.id || '—'), d.reserva || '', d.inmueble || '', d.pasarela || '', d.metodo || '', d.monto || '0.00', d.moneda || 'COP', d.estado || '', d.fecha || '', (d.soporte === '1' ? 'SÍ' : 'NO')]);
        });
        if (!filas.length) {
            avisar('No hay transacciones visibles para exportar.', 'danger');
            return;
        }
        var celda = function (v) { return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"'; };
        var lineas = [['#', 'Referencia', 'ID interno', 'Reserva', 'Inmueble', 'Pasarela', 'Método', 'Monto', 'Moneda', 'Estado', 'Fecha', 'Soporte'].map(celda).join(';')];
        filas.forEach(function (r, i) { lineas.push(([i + 1].concat(r)).map(celda).join(';')); });
        var blob = new Blob(['\ufeff' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'transacciones_' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(a); a.click(); a.remove();
        avisar('Exportadas ' + filas.length + ' transacción(es) a CSV', 'success');
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.classList || !form.classList.contains('js-ajax-trx')) return;
        e.preventDefault();
        enviarFormulario(form);
    });

    document.addEventListener('input', function (e) {
        if (!e.target || e.target.id !== 'trxSearch') return;
        aplicarFiltro(e.target);
        var val = (e.target.value || '').toLowerCase().trim();
        if (TRX_Q_TIMER) { clearTimeout(TRX_Q_TIMER); }
        TRX_Q_TIMER = setTimeout(function () { TRX_Q_TIMER = null; buscarServidor(val); }, 320);
    });

    document.addEventListener('keydown', function (e) {
        if (!e.target || e.target.id !== 'trxSearch' || e.key !== 'Enter') return;
        e.preventDefault();
        if (TRX_Q_TIMER) { clearTimeout(TRX_Q_TIMER); TRX_Q_TIMER = null; }
        buscarServidor((e.target.value || '').toLowerCase().trim());
    });

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!t) return;
        if (t.id === 'trxFechaDesde' || t.id === 'trxFechaHasta') {
            aplicarRangoFechas('cambio de fecha');
            return;
        }
        if (t.classList && t.classList.contains('js-trx-select-reserva')) {
            var opt = t.options[t.selectedIndex];
            var mSug = opt ? opt.getAttribute('data-monto') : '';
            var form = t.closest('form');
            var inpM = form ? form.querySelector('.js-trx-input-monto') : null;
            if (inpM && mSug && parseFloat(mSug) > 0) {
                inpM.value = mSug;
            }
        }
    });

    document.addEventListener('click', function (e) {
        var t = e.target;
        if (!t || !t.closest) return;

        if (t.closest('#trxDbgToggle'))  { DBG.togglePanel(); return; }
        if (t.closest('#trxDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#trxDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#trxDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
        if (t.closest('#trxDbgRefresh')) { e.stopPropagation(); refrescarTabla(null, PAGINA_ACTUAL); return; }

        if (t.closest('#trxAplicarFechas')) { aplicarRangoFechas(); return; }
        if (t.closest('#trxMesActual'))     { ponerMesActual(); return; }
        if (t.closest('#trxVerTodoBtn'))    { alternarVerTodo(); return; }
        if (t.closest('#trxExport'))        { exportarTrx(); return; }

        var btnR = t.closest('#trxRefresh');
        if (btnR) {
            var ico = btnR.querySelector('i.bi');
            if (ico) ico.classList.add('trx-spin');
            refrescarTabla(null, PAGINA_ACTUAL);
            setTimeout(function () { if (ico) ico.classList.remove('trx-spin'); }, 900);
            return;
        }

        var chip = t.closest('.trx-filter-chip');
        if (chip && chip.closest('#sheet-transacciones')) {
            document.querySelectorAll('#sheet-transacciones .trx-filter-chip').forEach(function (c) { c.classList.remove('active'); });
            chip.classList.add('active');
            aplicarFiltro();
            return;
        }

        var btnPrv = t.closest('.js-trx-preview-soporte');
        if (btnPrv) {
            e.preventDefault();
            abrirVisorSoporteTrx(
                btnPrv.getAttribute('data-url'),
                btnPrv.getAttribute('data-tipo') || 'auto',
                btnPrv.getAttribute('data-titulo')
            );
            return;
        }

        var a = t.closest('#sheet-transacciones .pagination a');
        if (a && a.getAttribute('href')) {
            e.preventDefault();
            try {
                var u = new URL(a.getAttribute('href'), window.location.origin);
                var pg = parseInt(u.searchParams.get('page_finanzas') || u.searchParams.get('page') || '1', 10);
                PAGINA_ACTUAL = isNaN(pg) ? 1 : pg;
                refrescarTabla(null, PAGINA_ACTUAL);
            } catch (err) {}
            return;
        }

        var btnVer = t.closest('.js-ver-trx');
        if (btnVer) {
            var d = btnVer.dataset;
            var setTxt = function (id, val) { var el = document.getElementById(id); if (el) el.textContent = val || '—'; };
            setTxt('fdRef', d.referencia);
            setTxt('fdId', 'ID interno #' + (d.id || '—'));
            setTxt('fdReserva', d.reserva);
            setTxt('fdInmueble', d.inmueble || '—');
            setTxt('fdPasarela', d.pasarela);
            setTxt('fdMetodo', d.metodo);
            setTxt('fdMonto', '$' + (d.monto || '0.00') + ' ' + (d.moneda || 'COP'));
            setTxt('fdFecha', d.fecha);

            var est = document.getElementById('fdEstado');
            if (est) {
                est.textContent = d.estado || 'PENDIENTE';
                var n = (d.estado || '').toLowerCase();
                est.style.color = (n === 'aprobado' || n === 'completado' || n === 'pagado') ? '#15803d' :
                                  (n === 'rechazado' || n === 'anulado' || n === 'cancelado') ? '#b91c1c' : '#b45309';
            }

            var caja = document.getElementById('fdSoporteCaja');
            if (caja) {
                caja.innerHTML = '';
                if (d.soporte === '1' && d.soporteUrl && d.soporteUrl !== '#') {
                    var btnS = document.createElement('button');
                    btnS.type = 'button';
                    btnS.className = 'btn-mini js-trx-preview-soporte';
                    btnS.setAttribute('data-url', d.soporteUrl);
                    btnS.setAttribute('data-tipo', d.soporteTipo || 'auto');
                    btnS.setAttribute('data-titulo', 'Soporte · TX #' + (d.id || ''));
                    btnS.innerHTML = '<i class="bi bi-eye text-success"></i> Previsualizar soporte (' + (d.soporteTipo || 'archivo').toUpperCase() + ')';
                    caja.appendChild(btnS);
                } else {
                    caja.innerHTML = '<span style="font-size: 8px; color: #94a3b8;">Sin soporte de pago adjunto</span>';
                }
            }

            var modal = document.getElementById('modalTransaccionDetalle');
            if (modal && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        }
    });

    unicarModales();
    limpiarResiduos();
    aplicarFiltro();
})();
</script>
