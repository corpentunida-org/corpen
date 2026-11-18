{{-- partials/pagos/transacciones.blade.php · Cliente · Historial y Registro Reactivo con Barra Minimalista de Envío + Inserción Instantánea (PG-TX-v2.3-DBG) --}}
<div id="sheet-pagos-transacciones-cliente">

    {{-- PG-TX-v2.3-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="pgTxDbgToggle" title="Debug Transacciones Cliente (PG-TX-v2.3-DBG)">
        <span class="pg-tx-dbg-led" id="pgTxDbgLed"></span>DBG<span class="pg-tx-dbg-badge" id="pgTxDbgBadge">0</span>
    </div>
    <div id="pgTxDbgPanel" role="dialog" aria-label="Panel de debug de Transacciones">
        <div class="pg-tx-dbg-header" id="pgTxDbgHeader">
            <span>DEBUG · Transacciones Cliente · PG-TX-v2.3-DBG</span>
            <span class="pg-tx-dbg-controls">
                <button type="button" class="pg-tx-dbg-btn" id="pgTxDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
                <button type="button" class="pg-tx-dbg-btn" id="pgTxDbgCopy">Copiar</button>
                <button type="button" class="pg-tx-dbg-btn" id="pgTxDbgClear">Limpiar</button>
                <button type="button" class="pg-tx-dbg-btn" id="pgTxDbgClose">✕</button>
            </span>
        </div>
        <div id="pgTxDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $ridFiltro = request('reserva_id') ?? request('id_reserva') ?? '';
        $pgTxErrores = [];

        // 1. Consulta defensiva autocontenida (no falla aunque el controlador no inyecte variables)
        $txs = collect();
        if (isset($transacciones)) {
            if ($transacciones instanceof \Illuminate\Pagination\AbstractPaginator) {
                $txs = collect($transacciones->items());
            } elseif ($transacciones instanceof \Illuminate\Support\Collection) {
                $txs = $transacciones;
            }
        }

        $misReservas = collect();
        $pasarelasActivas = collect();

        try {
            if ($uid) {
                $misReservas = \App\Models\Rsv\Reserva::with([
                        'inmueble.multimedia',
                        'inmueble.tipoInmueble',
                        'status',
                        'huespedes',
                        'transacciones',
                    ])
                    ->where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->get();
            }

            if ($txs->isEmpty() && $misReservas->isNotEmpty() && class_exists(\App\Models\Rsv\TransaccionFinanciera::class)) {
                $misIds = $misReservas->pluck('id')->all();
                $txs = \App\Models\Rsv\TransaccionFinanciera::whereIn('id_rsv_reservas', $misIds)
                    ->orderByDesc('created_at')
                    ->limit(100)
                    ->get();
            }

            if ($txs->isNotEmpty() && class_exists(\App\Models\Rsv\TransaccionFinanciera::class)) {
                $relTx = [];
                foreach (['reserva.inmueble', 'reserva.status', 'pasarela'] as $rTry) {
                    if (method_exists(\App\Models\Rsv\TransaccionFinanciera::class, explode('.', $rTry)[0])) {
                        $relTx[] = $rTry;
                    }
                }
                if (!empty($relTx)) {
                    $txs->loadMissing($relTx);
                }
            }

            if (class_exists(\App\Models\Rsv\Pasarela::class)) {
                $pasarelasActivas = \App\Models\Rsv\Pasarela::all();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.pagos.transacciones: ' . $e->getMessage());
            $pgTxErrores[] = $e->getMessage();
        }

        $reservasPagables = $misReservas->reject(function ($r) {
            $st = mb_strtolower((string) (optional($r->status)->name ?? ''));
            return str_contains($st, 'cancela') || str_contains($st, 'anula') || str_contains($st, 'rechaz');
        })->values();

        $pillTx = function ($estadoRaw) {
            $s = mb_strtolower((string) $estadoRaw);
            if (str_contains($s, 'anulad') || str_contains($s, 'cancel') || str_contains($s, 'rechaz') || str_contains($s, 'fallid')) return 'danger';
            if (str_contains($s, 'complet') || str_contains($s, 'aprob') || str_contains($s, 'pagad') || str_contains($s, 'ok') || str_contains($s, 'exitos')) return 'success';
            if (str_contains($s, 'pendien') || str_contains($s, 'verif') || str_contains($s, 'proces')) return 'warning';
            return 'secondary';
        };

        $conteos = [
            'all'     => $txs->count(),
            'success' => 0,
            'warning' => 0,
            'danger'  => 0,
        ];

        $sumPagado    = 0.0;
        $sumPendiente = 0.0;
        $sumAnulado   = 0.0;

        foreach ($txs as $tItem) {
            $estItem = $tItem->estado_pago ?? $tItem->estado ?? 'Pendiente';
            $grpItem = $pillTx($estItem);
            $mtoItem = (float) ($tItem->monto ?? 0);

            if (isset($conteos[$grpItem])) {
                $conteos[$grpItem]++;
            }

            if ($grpItem === 'success') {
                $sumPagado += $mtoItem;
            } elseif ($grpItem === 'danger') {
                $sumAnulado += $mtoItem;
            } else {
                $sumPendiente += $mtoItem;
            }
        }

        if (isset($resumen) && is_array($resumen)) {
            $sumPagado    = (float) ($resumen['pagado'] ?? $sumPagado);
            $sumPendiente = (float) ($resumen['pendiente'] ?? $sumPendiente);
            $sumAnulado   = (float) ($resumen['anulado'] ?? $sumAnulado);
        }

        $rutaFragmento = '';
        foreach (['rsv.cliente.fragmento', 'rsv.cliente.pagos.transacciones.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $rutaPago = '';
        foreach (['rsv.transacciones.store-ajax', 'rsv.transacciones.store', 'rsv.cliente.transacciones.store', 'transacciones.store'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaPago = route($rn);
                break;
            }
        }

        $pasarelaDefaultId = optional($pasarelasActivas->first())->id ?? 1;

        $pgTxDbgData = [
            'version'       => 'PG-TX-v2.3-DBG',
            'controlador'   => 'ClientePortalController@pagosTransaccionesFragmento',
            'vista'         => 'partials/pagos/transacciones.blade.php',
            'transacciones' => $txs->count(),
            'conteos'       => $conteos,
            'pagado'        => $sumPagado,
            'pendiente'     => $sumPendiente,
            'anulado'       => $sumAnulado,
            'frag_url'      => $rutaFragmento ?: 'fallback location',
            'store_url'     => $rutaPago ?: 'NO definida',
            'errores'       => $pgTxErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliPagosRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="pagos-transacciones"
         data-total="{{ $txs->count() }}"
         data-pagado="{{ $sumPagado }}"
         data-pendiente="{{ $sumPendiente }}"
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

            /* ── FRANJA RESUMEN FINANCIERO ── */
            .pt-summary-strip {
                display:grid; grid-template-columns:repeat(auto-fit, minmax(145px, 1fr));
                gap:8px; margin-bottom:12px;
            }
            .pt-summary-box {
                background:var(--head); border:1px solid var(--grid-2);
                border-radius:8px; padding:8px 12px; display:flex;
                align-items:center; justify-content:space-between;
            }

            /* ── BARRA DE FILTROS Y BUSCADOR ── */
            .pt-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .pt-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .pt-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .pt-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .pt-chip-count {
                font-size:7.5px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .pt-filter-chip.active .pt-chip-count { background:var(--accent); color:#fff; }
            .pt-filter-chip[data-filter="warning"].active { background:#fffbeb; color:#b45309; border-color:#fef3c7; }
            .pt-filter-chip[data-filter="warning"].active .pt-chip-count { background:#b45309; color:#fff; }
            .pt-filter-chip[data-filter="danger"].active { background:#fef2f2; color:#b91c1c; border-color:#fee2e2; }
            .pt-filter-chip[data-filter="danger"].active .pt-chip-count { background:#b91c1c; color:#fff; }

            .pt-input-ctrl {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 9px; outline:0; transition:all .15s;
            }
            .pt-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); }

            .pt-btn-accent {
                height:27px; padding:0 11px; border-radius:6px; border:1px solid var(--accent);
                background:var(--accent); color:#fff; font-size:8.5px; font-weight:700;
                display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all .15s;
            }
            .pt-btn-accent:hover:not(:disabled) { background:#2e5c43; border-color:#2e5c43; }
            .pt-btn-accent:disabled { opacity:.6; cursor:not-allowed; }

            /* ── BARRA MINIMALISTA DE ENVÍO DE FACTURA / COMPROBANTE ── */
            .pt-sending-bar {
                background:#0f172a; color:#f8fafc; border-radius:9px;
                padding:10px 14px; margin-bottom:12px; border:1px solid #1e293b;
                box-shadow:0 8px 20px rgba(15,23,42,0.12); overflow:hidden; position:relative;
                transition:all .25s ease;
            }
            .pt-sending-bar.is-ok {
                background:#f0fdf4; color:#14532d; border-color:#bbf7d0;
                box-shadow:0 4px 12px rgba(22,101,52,0.08);
            }
            .pt-sending-track {
                height:3px; width:100%; background:rgba(255,255,255,0.12);
                border-radius:3px; overflow:hidden; margin-top:7px;
            }
            .pt-sending-bar.is-ok .pt-sending-track { background:#dcfce7; }
            .pt-sending-fill {
                height:100%; width:35%; background:#10b981; border-radius:3px;
                animation:ptSlideBar 1.1s ease-in-out infinite alternate;
            }
            .pt-sending-bar.is-ok .pt-sending-fill {
                width:100%; animation:none; background:#16a34a; transition:width .25s ease;
            }
            @keyframes ptSlideBar {
                0%   { transform:translateX(-15%); width:30%; }
                100% { transform:translateX(210%); width:45%; }
            }

            /* ── FICHA RESALTADA DE LA RESERVA DESTINO DEL SOPORTE ── */
            .pt-target-reserva-card {
                background:linear-gradient(135deg, #f0f7f2 0%, #ffffff 100%);
                border:2px solid var(--accent);
                border-radius:10px;
                padding:12px 14px;
                margin-bottom:12px;
                box-shadow:0 6px 18px rgba(61,122,92,0.10);
                position:relative;
            }
            .pt-target-badge {
                display:inline-flex; align-items:center; gap:5px;
                background:var(--accent); color:#fff; font-size:7.5px; font-weight:800;
                padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:.4px;
            }
            .pt-target-thumb {
                width:56px; height:56px; border-radius:8px; overflow:hidden;
                border:1px solid var(--accent-bd); background:#fff; flex-shrink:0;
                display:flex; align-items:center; justify-content:center; color:var(--accent);
            }
            .pt-target-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
            .pt-target-grid {
                display:grid; grid-template-columns:repeat(auto-fit, minmax(125px, 1fr));
                gap:6px; margin-top:10px; padding-top:10px; border-top:1px dashed var(--accent-bd);
            }
            .pt-target-metric {
                background:#ffffff; border:1px solid var(--accent-bd);
                border-radius:6px; padding:5px 8px;
            }

            /* ── ZONA DRAG & DROP + CTRL+V + PREVIEW MINIATURA (IMAGEN / PDF) ── */
            .pt-dropzone {
                border:1.5px dashed var(--ink-4); border-radius:8px; background:#fff;
                padding:10px 12px; cursor:pointer; transition:all .18s ease;
                display:flex; align-items:center; justify-content:space-between; gap:10px;
                min-height:54px; position:relative;
            }
            .pt-dropzone:hover, .pt-dropzone.is-dragover {
                border-color:var(--accent); background:var(--accent-bg);
            }
            .pt-dropzone.has-file {
                border-style:solid; border-color:var(--accent-bd); background:#f8fcf9;
            }
            .pt-preview-wrap {
                display:flex; align-items:center; gap:10px; min-width:0; flex:1;
            }
            .pt-thumb-box {
                width:44px; height:44px; border-radius:6px; border:1px solid var(--grid-2);
                background:var(--head); overflow:hidden; flex-shrink:0;
                display:flex; align-items:center; justify-content:center; position:relative;
            }
            .pt-thumb-box img {
                width:100%; height:100%; object-fit:cover; display:block;
            }
            .pt-kbd {
                display:inline-block; padding:1px 5px; font-size:7.5px; font-weight:700;
                color:var(--ink-2); background:var(--grid); border:1px solid var(--grid-2);
                border-radius:4px; font-family:ui-monospace, monospace;
            }

            /* ── MINIATURA EN FILA DE TRANSACCIÓN ── */
            .pt-row-thumb {
                width:28px; height:28px; border-radius:5px; border:1px solid var(--grid-2);
                object-fit:cover; cursor:pointer; transition:transform .15s;
            }
            .pt-row-thumb:hover { transform:scale(1.08); border-color:var(--accent); }

            /* ── TARJETAS / FILAS DE TRANSACCIÓN ── */
            .pt-card {
                background:#fff; border:1px solid var(--grid); border-radius:8px;
                margin-bottom:6px; transition:all .25s ease; overflow:hidden;
            }
            .pt-card:hover { border-color:var(--accent-bd); background:#fafdfc; }
            .pt-card.is-target-reserva {
                border-left:3.5px solid var(--accent) !important;
                background:#f9fdfb;
            }
            .pt-card.is-just-created {
                border:1.5px solid #10b981 !important;
                background:#ecfdf5 !important;
                box-shadow:0 6px 16px rgba(16,185,129,0.12);
            }
            .pt-card.is-anulada .pt-monto { text-decoration:line-through; color:var(--ink-3); }
            .pt-row {
                padding:9px 12px; display:flex; align-items:center;
                justify-content:space-between; gap:10px; flex-wrap:wrap;
            }

            .pt-icon-box {
                width:32px; height:32px; border-radius:8px; display:flex;
                align-items:center; justify-content:center; font-size:12px;
                flex-shrink:0; border:1px solid;
            }
            .pt-icon-box.st-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .pt-icon-box.st-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .pt-icon-box.st-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .pt-icon-box.st-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary   { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .pt-btn-mini {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:8px; font-weight:700; padding:3px 9px; border-radius:5px;
                cursor:pointer; display:inline-flex; align-items:center; gap:4px;
                transition:all .15s; text-decoration:none;
            }
            .pt-btn-mini:hover { background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); text-decoration:none; }

            .pt-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:7px 10px; font-size:8.5px; margin-bottom:10px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #pgTxDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #pgTxDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .pg-tx-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .pg-tx-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:pgTxDbgBlink 1s steps(2,start) infinite; }
            @keyframes pgTxDbgBlink { 50% { opacity:.35; } }
            .pg-tx-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #pgTxDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .pg-tx-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .pg-tx-dbg-controls { display:flex; gap:4px; }
            .pg-tx-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .pg-tx-dbg-btn:hover { background:#475569; }
            #pgTxDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .pg-tx-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .pg-tx-dbg-time { color:#64748b; margin-right:4px; }
            .pg-tx-dbg-info { color:#93c5fd; } .pg-tx-dbg-ok { color:#86efac; } .pg-tx-dbg-warn { color:#fde68a; } .pg-tx-dbg-err { color:#fca5a5; } .pg-tx-dbg-net { color:#c4b5fd; } .pg-tx-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA + CHIPS DE ESTADO CON CONTEO REAL + SELECTOR DE RESERVA + BUSCADOR + BOTÓN REGISTRAR PAGO --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="pt-filter-bar">
                <span class="val me-2" style="font-size:11px;">
                    <i class="bi bi-receipt-cutoff text-success me-1"></i> Mis Pagos y Comprobantes
                </span>
                <button type="button" class="pt-filter-chip active" data-filter="all">
                    Todos <span class="pt-chip-count" id="ptCntAll">{{ $conteos['all'] }}</span>
                </button>
                <button type="button" class="pt-filter-chip" data-filter="success">
                    Aprobados <span class="pt-chip-count" id="ptCntSuccess">{{ $conteos['success'] }}</span>
                </button>
                <button type="button" class="pt-filter-chip" data-filter="warning">
                    Pendientes <span class="pt-chip-count" id="ptCntWarning">{{ $conteos['warning'] }}</span>
                </button>
                <button type="button" class="pt-filter-chip" data-filter="danger">
                    Anulados <span class="pt-chip-count" id="ptCntDanger">{{ $conteos['danger'] }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($misReservas->isNotEmpty())
                    <select id="ptSelectReserva" class="pt-input-ctrl" style="max-width:210px; cursor:pointer;">
                        <option value="">Todas las reservas ({{ $misReservas->count() }})</option>
                        @foreach($misReservas as $mr)
                            @php $mrSt = optional($mr->status)->name ?? 'Pendiente'; @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    data-estado="{{ $mrSt }}"
                                    {{ (string) $ridFiltro === (string) $mr->id ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} [{{ $mrSt }}]
                            </option>
                        @endforeach
                    </select>
                @endif

                <input type="text" id="ptSearchInput" class="pt-input-ctrl" style="width:150px;" placeholder="Buscar ref o canal…">

                @if($rutaPago && $reservasPagables->isNotEmpty())
                    <button type="button" class="pt-btn-accent" id="ptBtnTogglePago">
                        <i class="bi bi-plus-circle"></i> Registrar Pago
                    </button>
                @endif
            </div>
        </div>

        {{-- RESUMEN DE TOTALES (ABONADO / EN VERIFICACIÓN / ANULADO) --}}
        <div class="pt-summary-strip">
            <div class="pt-summary-box">
                <div>
                    <div class="lbl">Abonado Aprobado</div>
                    <div class="val" style="color:#15803d; font-size:12px;">$<span id="ptSumPagadoLbl">{{ number_format($sumPagado, 0) }}</span> <span class="sub">COP</span></div>
                </div>
                <i class="bi bi-check-circle-fill text-success" style="font-size:15px;"></i>
            </div>
            <div class="pt-summary-box">
                <div>
                    <div class="lbl">En Verificación</div>
                    <div class="val" id="ptSumPendienteWrap" data-raw="{{ $sumPendiente }}" style="color:#b45309; font-size:12px;">$<span id="ptSumPendienteLbl">{{ number_format($sumPendiente, 0) }}</span> <span class="sub">COP</span></div>
                </div>
                <i class="bi bi-hourglass-split text-warning" style="font-size:15px;"></i>
            </div>
            <div class="pt-summary-box">
                <div>
                    <div class="lbl">Anulado / Rechazado</div>
                    <div class="val" style="color:#64748b; font-size:12px;">$<span id="ptSumAnuladoLbl">{{ number_format($sumAnulado, 0) }}</span> <span class="sub">COP</span></div>
                </div>
                <i class="bi bi-slash-circle text-secondary" style="font-size:15px;"></i>
            </div>
        </div>

        {{-- BARRA MINIMALISTA ANIMADA DE ENVÍO DE COMPROBANTE / FACTURA --}}
        <div id="ptSendingBar" class="pt-sending-bar d-none">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2" style="font-size:9px; font-weight:700;">
                    <i class="bi bi-cloud-arrow-up-fill text-success" id="ptSendingIcon" style="font-size:12px;"></i>
                    <span id="ptSendingText">Enviando soporte de pago…</span>
                </div>
                <span style="font-size:8px; opacity:.8;" id="ptSendingSub">Procesando</span>
            </div>
            <div class="pt-sending-track">
                <div class="pt-sending-fill"></div>
            </div>
        </div>

        <div id="ptMainAlert" class="d-none"></div>

        {{-- FORMULARIO DESPLEGABLE CON FICHA DESTACADA DE LA RESERVA + DRAG & DROP + CTRL+V --}}
        @if($rutaPago && $reservasPagables->isNotEmpty())
            @php
                $primeraPag = $reservasPagables->first();
            @endphp
            <div id="ptNewPagoBox" class="p-3 mb-3 rounded-3 border d-none" style="background:var(--head); border-color:var(--accent-bd) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="val" style="color:var(--accent);">
                        <i class="bi bi-cloud-upload me-1"></i> Reportar Pago y Adjuntar Soporte
                    </span>
                    <button type="button" class="btn-close" style="font-size:8px;" id="ptBtnClosePago"></button>
                </div>

                {{-- FICHA RESALTADA DE LA RESERVA A LA QUE SE LE ESTÁ CARGANDO EL SOPORTE --}}
                <div class="pt-target-reserva-card" id="ptTargetReservaCard">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                            <div class="pt-target-thumb" id="ptTargetThumb">
                                <i class="bi bi-building" style="font-size:20px;"></i>
                            </div>
                            <div style="min-width:0; flex:1;">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="pt-target-badge">
                                        <i class="bi bi-check2-square"></i> Reserva Destino del Soporte
                                    </span>
                                    <span class="pill pill-warning" id="ptTargetStatusPill">—</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                    <span class="val" id="ptTargetHotelName" style="font-size:13px; color:#0f5132;">—</span>
                                    <span class="val" id="ptTargetCodigo" style="font-size:11px; color:var(--accent);">#—</span>
                                </div>
                                <div class="sub mt-1" id="ptTargetUbicacion" style="color:var(--ink-2); font-weight:600;">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>—
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-target-grid">
                        <div class="pt-target-metric">
                            <div class="lbl">Check-in (Llegada)</div>
                            <div class="val" id="ptTargetCheckin" style="font-size:10.5px;">—</div>
                        </div>
                        <div class="pt-target-metric">
                            <div class="lbl">Check-out (Salida)</div>
                            <div class="val" id="ptTargetCheckout" style="font-size:10.5px;">—</div>
                        </div>
                        <div class="pt-target-metric">
                            <div class="lbl">Estancia y Cupo</div>
                            <div class="val" id="ptTargetEstancia" style="font-size:10.5px;">—</div>
                        </div>
                        <div class="pt-target-metric">
                            <div class="lbl">Valor Reserva / Abonado</div>
                            <div class="val" id="ptTargetTotales" style="font-size:10.5px;">—</div>
                        </div>
                        <div class="pt-target-metric" style="background:#f0fdf4; border-color:#86efac;">
                            <div class="lbl" style="color:#166534;">Saldo Pendiente</div>
                            <div class="val" id="ptTargetSaldo" style="font-size:11px; color:#15803d;">—</div>
                        </div>
                    </div>
                </div>

                <form id="cliPagoForm" action="{{ $rutaPago }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_ajax" value="1">
                    <input type="hidden" name="id_user" value="{{ $uid }}">
                    <input type="hidden" name="estado_pago" value="Pendiente">

                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="lbl d-block mb-1">Cambiar Reserva a Pagar *</label>
                            <select name="id_rsv_reservas" id="ptFormSelectReserva" class="form-select form-select-sm" style="font-size:9px; font-weight:700; border-color:var(--accent);" required>
                                @foreach($reservasPagables as $rPag)
                                    @php
                                        $totR = (float) ($rPag->monto_total ?? 0);
                                        $pagR = 0.0;
                                        if ($rPag->relationLoaded('transacciones')) {
                                            foreach ($rPag->transacciones as $txR) {
                                                $estR = mb_strtolower((string) ($txR->estado_pago ?? ''));
                                                if (!str_contains($estR, 'anulad') && !str_contains($estR, 'rechaz') && !str_contains($estR, 'cancel')) {
                                                    $pagR += (float) ($txR->monto ?? 0);
                                                }
                                            }
                                        }
                                        $saldoSug = max(0, $totR - $pagR);
                                        $stR = optional($rPag->status)->name ?? 'Pendiente';
                                        $inmR = $rPag->inmueble;
                                        $thumbR = ($inmR && $inmR->relationLoaded('multimedia') && $inmR->multimedia->first())
                                            ? $inmR->multimedia->first()->url_archivo
                                            : '';
                                        $tipoInmR = optional($inmR?->tipoInmueble)->nombre ?? 'Propiedad';
                                        $ciudadR = $inmR->city ?? 'Ubicación registrada';
                                        $dirR    = $inmR->ubicacion ?? '';
                                        $fiCarb  = $rPag->fecha_inicio ? \Carbon\Carbon::parse($rPag->fecha_inicio) : null;
                                        $ffCarb  = $rPag->fecha_fin ? \Carbon\Carbon::parse($rPag->fecha_fin) : null;
                                        $nochesR = ($fiCarb && $ffCarb) ? max(1, (int) $fiCarb->diffInDays($ffCarb)) : 1;
                                        $huesCnt = $rPag->relationLoaded('huespedes') ? $rPag->huespedes->count() : 1;
                                        $capMaxR = (int) ($inmR->capacidad_maxima ?? 6);
                                    @endphp
                                    <option value="{{ $rPag->id }}"
                                            data-cod="{{ $rPag->codigo_reserva }}"
                                            data-hotel="{{ $inmR->name ?? 'Inmueble' }}"
                                            data-tipo="{{ $tipoInmR }}"
                                            data-ciudad="{{ $ciudadR }}"
                                            data-direccion="{{ $dirR }}"
                                            data-thumb="{{ $thumbR }}"
                                            data-estado="{{ $stR }}"
                                            data-fi="{{ $fiCarb ? $fiCarb->format('d/m/Y') : '—' }}"
                                            data-ff="{{ $ffCarb ? $ffCarb->format('d/m/Y') : '—' }}"
                                            data-noches="{{ $nochesR }}"
                                            data-huespedes="{{ $huesCnt }}/{{ $capMaxR }}"
                                            data-total="{{ $totR }}"
                                            data-abonado="{{ $pagR }}"
                                            data-saldo="{{ $saldoSug }}"
                                            data-sugerido="{{ $saldoSug > 0 ? $saldoSug : $totR }}">
                                        #{{ $rPag->codigo_reserva }} · {{ $inmR->name ?? 'Inmueble' }} ({{ $fiCarb ? $fiCarb->format('d/m/y') : '—' }} al {{ $ffCarb ? $ffCarb->format('d/m/y') : '—' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id_rsv_reserva" id="ptHiddenIdReserva" value="{{ optional($primeraPag)->id }}">
                            <input type="hidden" name="reserva_id" id="ptHiddenReservaId" value="{{ optional($primeraPag)->id }}">
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="lbl d-block mb-1">Monto (COP) *</label>
                            <input type="number" step="0.01" min="1" name="monto" id="ptInputMonto"
                                   class="form-control form-control-sm" style="font-size:9px; font-weight:700;"
                                   value="{{ (float) (optional($primeraPag)->monto_total ?? 0) }}"
                                   required>
                            <input type="hidden" name="moneda" value="COP">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="lbl d-block mb-1">Pasarela / Canal *</label>
                            @if($pasarelasActivas->isNotEmpty())
                                <select name="id_rsv_pasarelas" id="ptSelectPasarela" class="form-select form-select-sm" style="font-size:9px;" required>
                                    @foreach($pasarelasActivas as $pasOpt)
                                        @php $nomPasOpt = $pasOpt->nombre ?? $pasOpt->name ?? ('Canal #' . $pasOpt->id); @endphp
                                        <option value="{{ $pasOpt->id }}" data-nombre="{{ $nomPasOpt }}">{{ $nomPasOpt }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id_rsv_pasarela" id="ptHiddenIdPasarela" value="{{ $pasarelaDefaultId }}">
                                <input type="hidden" name="metodo_pago" id="ptHiddenMetodoPago" value="{{ optional($pasarelasActivas->first())->nombre ?? 'Transferencia' }}">
                            @else
                                <input type="hidden" name="id_rsv_pasarelas" value="1">
                                <input type="hidden" name="id_rsv_pasarela" value="1">
                                <input type="text" name="metodo_pago" class="form-control form-control-sm" style="font-size:9px;" placeholder="Transferencia, PSE…" value="Transferencia" required>
                            @endif
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="lbl d-block mb-1">Referencia / Comprobante</label>
                            <input type="text" name="referencia_externa" class="form-control form-control-sm" style="font-size:9px;" placeholder="N° de recibo o CUS…" maxlength="80">
                        </div>

                        {{-- ZONA DRAG & DROP + CTRL+V + MINIATURA EN PEQUEÑO --}}
                        <div class="col-12 col-md-9 mt-2">
                            <label class="lbl d-block mb-1">
                                Soporte de Pago (Imagen o PDF) · Arrastra aquí, haz clic o pega con <span class="pt-kbd">Ctrl + V</span>
                            </label>
                            <input type="file" name="soporte_pago" id="ptInputFile" class="d-none" accept="image/*,application/pdf">

                            <div class="pt-dropzone" id="ptDropzone" tabindex="0" title="Haz clic, arrastra un archivo o presiona Ctrl+V para pegar captura">
                                {{-- Estado vacío --}}
                                <div class="d-flex align-items-center gap-2" id="ptDropEmpty">
                                    <div class="pt-thumb-box">
                                        <i class="bi bi-cloud-arrow-up text-success" style="font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div class="val" style="font-size:9px;">Arrastra tu comprobante aquí, haz clic para buscar o pega con <span class="pt-kbd">Ctrl+V</span></div>
                                        <div class="sub" style="font-size:7.5px;">Se cargará directamente a la reserva resaltada arriba (PNG, JPG, WEBP o PDF)</div>
                                    </div>
                                </div>

                                {{-- Estado con archivo cargado (miniatura de imagen o PDF) --}}
                                <div class="pt-preview-wrap d-none" id="ptDropPreview">
                                    <div class="pt-thumb-box" id="ptThumbContainer"></div>
                                    <div style="min-width:0; flex:1;">
                                        <div class="val text-truncate" id="ptFileName" style="font-size:9px;">archivo</div>
                                        <div class="sub" id="ptFileMeta" style="font-size:7.5px;">—</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="pt-btn-mini" id="ptBtnViewPreview" title="Previsualizar archivo cargado">
                                            <i class="bi bi-eye" id="ptBtnViewIcon"></i> <span id="ptBtnViewText">Ver</span>
                                        </button>
                                        <button type="button" class="pt-btn-mini text-danger" id="ptBtnClearFile" title="Quitar archivo">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 mt-2">
                            <button type="submit" class="pt-btn-accent w-100 justify-content-center" id="ptBtnSubmitPago" style="height:54px;">
                                <i class="bi bi-check2-circle"></i> Enviar Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        {{-- LISTADO DE TRANSACCIONES (MUESTRA SOLO EL BOTÓN DEL ARCHIVO QUE REALMENTE SE CARGÓ) --}}
        <div id="ptTransaccionesList">
            @forelse($txs as $tx)
                @php
                    $edo       = $tx->estado_pago ?? $tx->estado ?? 'Pendiente';
                    $grpTx     = $pillTx($edo);
                    $esAnulada = ($grpTx === 'danger');

                    $resObj    = $tx->reserva ?? $misReservas->firstWhere('id', $tx->id_rsv_reservas);
                    $resId     = $resObj->id ?? $tx->id_rsv_reservas ?? $tx->reserva_id ?? null;
                    $resCod    = $resObj->codigo_reserva ?? ($resId ? '#' . $resId : 'S/C');
                    $inmObj    = $resObj?->inmueble;
                    $inmNom    = optional($inmObj)->name ?? 'Reserva';
                    $inmCity   = optional($inmObj)->city ?? '';
                    $resFi     = ($resObj && $resObj->fecha_inicio) ? \Carbon\Carbon::parse($resObj->fecha_inicio)->format('d/m/Y') : null;
                    $resFf     = ($resObj && $resObj->fecha_fin) ? \Carbon\Carbon::parse($resObj->fecha_fin)->format('d/m/Y') : null;
                    $resEstado = optional($resObj?->status)->name ?? 'Registrada';
                    $resEstGrp = $pillTx($resEstado);

                    $pasObj    = $tx->pasarela;
                    $pasNom    = optional($pasObj)->nombre ?? optional($pasObj)->name ?? ($tx->metodo_pago ?? 'Directo');
                    $refExt    = trim((string) ($tx->referencia_externa ?? ''));

                    $rawSoporte = trim((string) ($tx->url_soporte_pago ?? $tx->soporte_pago ?? ''));
                    $tieneSoporte = ($rawSoporte !== '' && $rawSoporte !== '#');
                    $pathSop = $tieneSoporte ? (parse_url($rawSoporte, PHP_URL_PATH) ?? $rawSoporte) : '';
                    $esPdfSop = $tieneSoporte && preg_match('/\.pdf$/i', $pathSop);
                    $esImgSop = $tieneSoporte && !$esPdfSop && preg_match('/\.(jpe?g|png|webp|gif|bmp|svg)$/i', $pathSop);

                    $fechaFmt  = optional($tx->created_at)->format('d/m/Y h:i A') ?? '—';
                    $montoFmt  = number_format((float) ($tx->monto ?? 0), 0);
                    $moneda    = $tx->moneda ?? 'COP';

                    $iconoTx = match($grpTx) {
                        'success' => 'bi-check2-circle',
                        'warning' => 'bi-hourglass-split',
                        'danger'  => 'bi-x-octagon',
                        default   => 'bi-receipt',
                    };
                @endphp

                <div class="pt-card {{ $esAnulada ? 'is-anulada' : '' }}"
                     id="ptCard_{{ $tx->id }}"
                     data-tx-id="{{ $tx->id }}"
                     data-reserva-id="{{ $resId }}"
                     data-reserva-cod="{{ $resCod }}"
                     data-st-group="{{ $grpTx }}"
                     data-search="{{ mb_strtolower($resCod . ' ' . $inmNom . ' ' . $inmCity . ' ' . $pasNom . ' ' . $refExt . ' ' . $edo . ' ' . $montoFmt) }}">

                    <div class="pt-row">
                        <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                            <div class="pt-icon-box st-{{ $grpTx }}">
                                <i class="bi {{ $iconoTx }}"></i>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val pt-monto" style="font-size:11px;">${{ $montoFmt }} <span class="sub">{{ $moneda }}</span></span>
                                    <span class="pill pill-{{ $grpTx }}">{{ $edo }}</span>
                                    <span class="val text-truncate">{{ $inmNom }}</span>
                                    @if($inmCity) <span class="sub">({{ $inmCity }})</span> @endif
                                    <span class="sub fw-bold" style="color:var(--accent);">#{{ $resCod }}</span>
                                    @if($resFi && $resFf)
                                        <span class="sub"><i class="bi bi-calendar3 me-1"></i>{{ $resFi }} – {{ $resFf }}</span>
                                    @endif
                                    <span class="pill pill-{{ $resEstGrp }} d-none d-md-inline-flex">
                                        Reserva: {{ $resEstado }}
                                    </span>
                                </div>

                                <div class="sub text-truncate mt-1">
                                    <i class="bi bi-credit-card me-1"></i><b>{{ $pasNom }}</b>
                                    @if($refExt !== '')
                                        <span class="mx-1">·</span> Ref: <b>{{ $refExt }}</b>
                                    @endif
                                    <span class="mx-1">·</span> {{ $fechaFmt }}
                                </div>
                            </div>
                        </div>

                        {{-- DERECHA: SOLO MUESTRA EL SOPORTE QUE REALMENTE FUE CARGADO (IMAGEN O PDF) --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            @if($tieneSoporte)
                                @if($esImgSop)
                                    <img src="{{ $rawSoporte }}" alt="Soporte" class="pt-row-thumb js-pt-preview-url"
                                         data-url="{{ $rawSoporte }}" data-type="image"
                                         data-title="Imagen de Soporte · Reserva #{{ $resCod }} ({{ $inmNom }})"
                                         title="Ver imagen cargada">
                                    <button type="button"
                                            class="pt-btn-mini js-pt-preview-url"
                                            data-url="{{ $rawSoporte }}"
                                            data-type="image"
                                            data-title="Imagen de Soporte · Reserva #{{ $resCod }} ({{ $inmNom }})">
                                        <i class="bi bi-file-earmark-image text-success"></i> Imagen
                                    </button>
                                @elseif($esPdfSop)
                                    <button type="button"
                                            class="pt-btn-mini js-pt-preview-url"
                                            data-url="{{ $rawSoporte }}"
                                            data-type="pdf"
                                            data-title="PDF de Soporte · Reserva #{{ $resCod }} ({{ $inmNom }})">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                                    </button>
                                @else
                                    <button type="button"
                                            class="pt-btn-mini js-pt-preview-url"
                                            data-url="{{ $rawSoporte }}"
                                            data-type="auto"
                                            data-title="Soporte · Reserva #{{ $resCod }} ({{ $inmNom }})">
                                        <i class="bi bi-paperclip text-success"></i> Archivo
                                    </button>
                                @endif
                            @else
                                <span class="sub fst-italic" style="font-size:7.5px;">Sin soporte adjunto</span>
                            @endif

                            @if($resId)
                                <button type="button"
                                        class="pt-btn-mini js-pt-ver-reserva"
                                        data-reserva-id="{{ $resId }}"
                                        data-reserva-cod="{{ $resCod }}"
                                        title="Ver expediente de la reserva #{{ $resCod }}">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light border rounded-3 js-pt-empty-initial" style="border-style:dashed !important;">
                    <i class="bi bi-receipt text-muted d-block mb-2" style="font-size:22px;"></i>
                    <span class="sub d-block">Aún no tienes pagos registrados en tus reservas.</span>
                </div>
            @endforelse
        </div>

        @if($txs->isNotEmpty())
            <div id="ptEmptyFilterMsg" class="text-center py-4 mt-2 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                <span class="sub d-block">No hay transacciones que coincidan con el filtro seleccionado.</span>
            </div>
        @endif

        {{-- MODAL LIGERO PARA PREVISUALIZAR EL ARCHIVO CARGADO (IMAGEN O PDF) --}}
        <div class="modal fade" id="ptModalPreviewSoporte" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content reservas-soft" style="border:none; border-radius:10px; overflow:hidden;">
                    <div class="modal-header bg-white border-bottom py-2 px-3">
                        <span class="val" id="ptModalPreviewTitle"><i class="bi bi-eye me-1 text-success"></i> Vista Previa del Soporte</span>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" target="_blank" id="ptModalPreviewOpenExt" class="pt-btn-mini">
                                <i class="bi bi-box-arrow-up-right"></i> Abrir original
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:10px;"></button>
                        </div>
                    </div>
                    <div class="modal-body p-2 bg-light text-center" id="ptModalPreviewBody" style="min-height:320px; display:flex; align-items:center; justify-content:center;">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL PG-TX-v2.3-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.PgTxDbg && window.PgTxDbg.alive && window.PgTxDbg.alive())
        ? window.PgTxDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-pagos-transacciones-cliente') || document.body;
                if (!$('pgTxDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'pgTxDbgToggle'; t.title = 'Debug Transacciones Cliente (PG-TX-v2.3-DBG)';
                    t.innerHTML = '<span class="pg-tx-dbg-led" id="pgTxDbgLed"></span>DBG<span class="pg-tx-dbg-badge" id="pgTxDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('pgTxDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'pgTxDbgPanel';
                    p.innerHTML = '<div class="pg-tx-dbg-header" id="pgTxDbgHeader">' +
                        '<span>DEBUG · Transacciones Cliente · PG-TX-v2.3-DBG</span>' +
                        '<span class="pg-tx-dbg-controls">' +
                        '<button type="button" class="pg-tx-dbg-btn" id="pgTxDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="pg-tx-dbg-btn" id="pgTxDbgCopy">Copiar</button>' +
                        '<button type="button" class="pg-tx-dbg-btn" id="pgTxDbgClear">Limpiar</button>' +
                        '<button type="button" class="pg-tx-dbg-btn" id="pgTxDbgClose">✕</button>' +
                        '</span></div><div id="pgTxDbgBody"></div>';
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
                    'pgIniDbgToggle', 'pgPasDbgToggle', 'pgRevDbgToggle'
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
                var t = $('pgTxDbgToggle'), p = $('pgTxDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('pgTxDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'pg-tx-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="pg-tx-dbg-time">[' + hora() + ']</span><span class="pg-tx-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('pgTxDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('pgTxDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('pgTxDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('pgTxDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('pgTxDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('pgTxDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('pgTxDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('pgTxDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('pgTxDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Transacciones PG-TX-v2.3-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('pgTxDbgToggle'); }
            };
            window.PgTxDbg = api;
            return api;
        })();

    DBG.ensure();
    var pgtxdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#pgTxDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#pgTxDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function fmtCop(num) {
        var n = parseFloat(num || 0);
        return '$' + Math.round(n).toLocaleString('es-CO') + ' COP';
    }

    function escaparHtml(str) {
        return String(str || '').replace(/[<>&"]/g, function (c) {
            return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : (c === '&' ? '&amp;' : '&quot;'));
        });
    }

    // ═══ PINTAR FICHA RESALTADA DE LA RESERVA A LA QUE SE CARGARÁ EL SOPORTE ═══
    function sincronizarFichaReservaObjetivo() {
        var sel = document.getElementById('ptFormSelectReserva');
        if (!sel || !sel.options.length) return;

        var opt = sel.options[sel.selectedIndex];
        if (!opt) return;

        var resId  = opt.value || '';
        var cod    = opt.getAttribute('data-cod') || '—';
        var hotel  = opt.getAttribute('data-hotel') || 'Propiedad';
        var tipo   = opt.getAttribute('data-tipo') || '';
        var ciudad = opt.getAttribute('data-ciudad') || '';
        var dir    = opt.getAttribute('data-direccion') || '';
        var thumb  = opt.getAttribute('data-thumb') || '';
        var estado = opt.getAttribute('data-estado') || 'Pendiente';
        var fi     = opt.getAttribute('data-fi') || '—';
        var ff     = opt.getAttribute('data-ff') || '—';
        var noches = opt.getAttribute('data-noches') || '1';
        var huesp  = opt.getAttribute('data-huespedes') || '1';
        var total  = opt.getAttribute('data-total') || '0';
        var abon   = opt.getAttribute('data-abonado') || '0';
        var saldo  = opt.getAttribute('data-saldo') || '0';
        var sug    = opt.getAttribute('data-sugerido') || total;

        var elThumb  = document.getElementById('ptTargetThumb');
        var elHotel  = document.getElementById('ptTargetHotelName');
        var elCod    = document.getElementById('ptTargetCodigo');
        var elUbic   = document.getElementById('ptTargetUbicacion');
        var elSt     = document.getElementById('ptTargetStatusPill');
        var elFi     = document.getElementById('ptTargetCheckin');
        var elFf     = document.getElementById('ptTargetCheckout');
        var elEst    = document.getElementById('ptTargetEstancia');
        var elTot    = document.getElementById('ptTargetTotales');
        var elSaldo  = document.getElementById('ptTargetSaldo');

        if (elThumb) {
            elThumb.innerHTML = thumb
                ? '<img src="' + thumb + '" alt="' + escaparHtml(hotel) + '">'
                : '<i class="bi bi-building" style="font-size:20px;"></i>';
        }
        if (elHotel) elHotel.textContent = hotel + (tipo ? ' (' + tipo + ')' : '');
        if (elCod)   elCod.textContent = 'Reserva #' + cod;
        if (elUbic) {
            elUbic.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i>' +
                escaparHtml(ciudad + (dir ? ' · ' + dir : ''));
        }
        if (elSt)    elSt.textContent = 'Estado: ' + estado;
        if (elFi)    elFi.innerHTML = '<i class="bi bi-calendar-check text-success me-1"></i>' + escaparHtml(fi) + ' <span class="sub">(3:00 PM)</span>';
        if (elFf)    elFf.innerHTML = '<i class="bi bi-calendar-x text-danger me-1"></i>' + escaparHtml(ff) + ' <span class="sub">(1:00 PM)</span>';
        if (elEst)   elEst.textContent = noches + ' noche(s) · ' + huesp + ' huésped(es)';
        if (elTot)   elTot.textContent = fmtCop(total) + ' (Abonado: ' + fmtCop(abon) + ')';
        if (elSaldo) elSaldo.textContent = fmtCop(saldo);

        var h1   = document.getElementById('ptHiddenIdReserva');
        var h2   = document.getElementById('ptHiddenReservaId');
        var inpM = document.getElementById('ptInputMonto');
        if (h1) h1.value = resId;
        if (h2) h2.value = resId;
        if (inpM && sug) inpM.value = sug;

        document.querySelectorAll('#ptTransaccionesList .pt-card').forEach(function (c) {
            c.classList.toggle('is-target-reserva', c.getAttribute('data-reserva-id') === resId);
        });
    }

    // ═══ GESTIÓN DE ARCHIVO (DRAG & DROP / CLIC / CTRL+V / MINIATURA IMAGEN O PDF) ═══
    var currentObjectUrl = null;

    function formatBytes(bytes) {
        if (!bytes || bytes <= 0) return '0 KB';
        var kb = bytes / 1024;
        return kb < 1024 ? Math.round(kb) + ' KB' : (kb / 1024).toFixed(2) + ' MB';
    }

    function asignarArchivoAlInput(file, origen) {
        if (!file) return;
        var inp = document.getElementById('ptInputFile');
        var dz  = document.getElementById('ptDropzone');
        var emp = document.getElementById('ptDropEmpty');
        var prv = document.getElementById('ptDropPreview');
        var thb = document.getElementById('ptThumbContainer');
        var fnm = document.getElementById('ptFileName');
        var fmt = document.getElementById('ptFileMeta');
        var vIc = document.getElementById('ptBtnViewIcon');
        var vTx = document.getElementById('ptBtnViewText');
        if (!inp || !dz || !emp || !prv || !thb) return;

        var mime = (file.type || '').toLowerCase();
        var esImg = mime.indexOf('image/') === 0 || /\.(jpe?g|png|webp|gif)$/i.test(file.name || '');
        var esPdf = mime === 'application/pdf' || /\.pdf$/i.test(file.name || '');

        if (!esImg && !esPdf) {
            pgtxdbg('Archivo rechazado (' + (file.name || 'sin-nombre') + '): solo imágenes o PDF', 'warn');
            alert('Por favor selecciona una imagen (JPG, PNG, WEBP) o un archivo PDF.');
            return;
        }

        try {
            var dt = new DataTransfer();
            dt.items.add(file);
            inp.files = dt.files;
        } catch (e) {}

        currentObjectUrl = URL.createObjectURL(file);

        dz.classList.add('has-file');
        emp.classList.add('d-none');
        prv.classList.remove('d-none');

        var nombreMostrar = file.name || (esPdf ? 'comprobante.pdf' : 'captura-pegada.png');
        if (fnm) fnm.textContent = nombreMostrar;
        if (fmt) fmt.textContent = (esPdf ? 'Documento PDF' : 'Imagen') + ' · ' + formatBytes(file.size) + ' · vía ' + origen;

        if (esImg) {
            thb.innerHTML = '<img src="' + currentObjectUrl + '" alt="Miniatura">';
            if (vIc) vIc.className = 'bi bi-file-earmark-image text-success';
            if (vTx) vTx.textContent = 'Ver Imagen';
        } else {
            thb.innerHTML = '<div class="d-flex flex-column align-items-center justify-content-center text-danger" style="line-height:1;">' +
                '<i class="bi bi-file-earmark-pdf-fill" style="font-size:18px;"></i>' +
                '<span style="font-size:6.5px;font-weight:800;">PDF</span></div>';
            if (vIc) vIc.className = 'bi bi-file-earmark-pdf text-danger';
            if (vTx) vTx.textContent = 'Ver PDF';
        }

        dz.setAttribute('data-preview-url', currentObjectUrl);
        dz.setAttribute('data-preview-type', esPdf ? 'pdf' : 'image');
        dz.setAttribute('data-preview-title', nombreMostrar);

        pgtxdbg('Soporte cargado vía ' + origen + ': "' + nombreMostrar + '" (' + formatBytes(file.size) + ')', 'ok');
    }

    function limpiarArchivoSeleccionado(revocarUrl) {
        var inp = document.getElementById('ptInputFile');
        var dz  = document.getElementById('ptDropzone');
        var emp = document.getElementById('ptDropEmpty');
        var prv = document.getElementById('ptDropPreview');
        var thb = document.getElementById('ptThumbContainer');
        if (inp) inp.value = '';
        if (revocarUrl && currentObjectUrl) {
            try { URL.revokeObjectURL(currentObjectUrl); } catch (e) {}
        }
        currentObjectUrl = null;
        if (dz) {
            dz.classList.remove('has-file');
            dz.removeAttribute('data-preview-url');
        }
        if (emp) emp.classList.remove('d-none');
        if (prv) prv.classList.add('d-none');
        if (thb) thb.innerHTML = '';
    }

    function abrirModalVistaPrevia(url, tipo, titulo) {
        if (!url) return;
        var modalEl = document.getElementById('ptModalPreviewSoporte');
        var bodyEl  = document.getElementById('ptModalPreviewBody');
        var titleEl = document.getElementById('ptModalPreviewTitle');
        var extLink = document.getElementById('ptModalPreviewOpenExt');
        if (!modalEl || !bodyEl) {
            window.open(url, '_blank');
            return;
        }

        if (titleEl) titleEl.textContent = titulo || 'Vista Previa del Soporte';
        if (extLink) extLink.href = url;

        var esPdf = (tipo === 'pdf') || (tipo === 'auto' && /\.pdf($|\?)/i.test(url));
        if (esPdf) {
            bodyEl.innerHTML = '<iframe src="' + url + '" style="width:100%;height:460px;border:0;border-radius:6px;background:#fff;"></iframe>';
        } else {
            bodyEl.innerHTML = '<img src="' + url + '" alt="Soporte" style="max-width:100%;max-height:460px;object-fit:contain;border-radius:6px;">';
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
        pgtxdbg('Vista previa abierta: ' + (titulo || url), 'ev');
    }

    function actualizarContadoresTx() {
        var selRes = document.getElementById('ptSelectReserva')?.value || '';
        var cards = document.querySelectorAll('#ptTransaccionesList .pt-card');
        var counts = { all: 0, success: 0, warning: 0, danger: 0 };

        cards.forEach(function (c) {
            if (selRes && c.getAttribute('data-reserva-id') !== selRes) return;
            counts.all++;
            var grp = c.getAttribute('data-st-group') || '';
            if (Object.prototype.hasOwnProperty.call(counts, grp)) {
                counts[grp]++;
            }
        });

        var mapIds = {
            all: 'ptCntAll',
            success: 'ptCntSuccess',
            warning: 'ptCntWarning',
            danger: 'ptCntDanger'
        };
        Object.keys(mapIds).forEach(function (k) {
            var el = document.getElementById(mapIds[k]);
            if (el) el.textContent = counts[k];
        });
    }

    function aplicarFiltrosTx(logAccion) {
        var chipActivo = document.querySelector('#cliPagosRoot .pt-filter-chip.active');
        var grupo  = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var selRes = document.getElementById('ptSelectReserva')?.value || '';
        var q      = (document.getElementById('ptSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#ptTransaccionesList .pt-card').forEach(function (card) {
            var matchRes   = !selRes || (card.getAttribute('data-reserva-id') === selRes);
            var matchGrupo = (grupo === 'all') || (card.getAttribute('data-st-group') === grupo);
            var matchTexto = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar    = matchRes && matchGrupo && matchTexto;
            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('ptEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            pgtxdbg('Filtro aplicado · estado="' + grupo + '" · reserva="' + (selRes || 'todas') + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ INSERCIÓN INSTANTÁNEA EN LA LISTA TRAS ENVIAR EL COMPROBANTE (SIN DEPENDER DEL GET FRAGMENTO) ═══
    function insertarFilaTransaccionOptimista(meta) {
        var list = document.getElementById('ptTransaccionesList');
        if (!list) return;

        var emptyInit = list.querySelector('.js-pt-empty-initial');
        if (emptyInit) emptyInit.remove();

        var montoFmt = Math.round(parseFloat(meta.monto || 0)).toLocaleString('es-CO');
        var soporteHtml = '';

        if (meta.previewUrl) {
            var tituloMod = escaparHtml((meta.previewType === 'pdf' ? 'PDF de Soporte' : 'Imagen de Soporte') + ' · Reserva #' + meta.cod + ' (' + meta.hotel + ')');
            if (meta.previewType === 'image') {
                soporteHtml =
                    '<img src="' + meta.previewUrl + '" alt="Soporte" class="pt-row-thumb js-pt-preview-url" ' +
                         'data-url="' + meta.previewUrl + '" data-type="image" data-title="' + tituloMod + '" title="Ver imagen cargada">' +
                    '<button type="button" class="pt-btn-mini js-pt-preview-url" ' +
                            'data-url="' + meta.previewUrl + '" data-type="image" data-title="' + tituloMod + '">' +
                        '<i class="bi bi-file-earmark-image text-success"></i> Imagen' +
                    '</button>';
            } else if (meta.previewType === 'pdf') {
                soporteHtml =
                    '<button type="button" class="pt-btn-mini js-pt-preview-url" ' +
                            'data-url="' + meta.previewUrl + '" data-type="pdf" data-title="' + tituloMod + '">' +
                        '<i class="bi bi-file-earmark-pdf text-danger"></i> PDF' +
                    '</button>';
            }
        } else {
            soporteHtml = '<span class="sub fst-italic" style="font-size:7.5px;">Sin soporte adjunto</span>';
        }

        var card = document.createElement('div');
        card.className = 'pt-card is-just-created is-target-reserva';
        card.setAttribute('data-reserva-id', meta.resId);
        card.setAttribute('data-reserva-cod', meta.cod);
        card.setAttribute('data-st-group', 'warning');
        card.setAttribute('data-search', (meta.cod + ' ' + meta.hotel + ' ' + meta.ciudad + ' ' + meta.pasarela + ' ' + meta.ref + ' pendiente ' + montoFmt).toLowerCase());

        card.innerHTML =
            '<div class="pt-row">' +
                '<div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">' +
                    '<div class="pt-icon-box st-warning"><i class="bi bi-hourglass-split"></i></div>' +
                    '<div style="min-width:0; flex:1;">' +
                        '<div class="d-flex align-items-center gap-2 flex-wrap">' +
                            '<span class="val pt-monto" style="font-size:11px;">$' + montoFmt + ' <span class="sub">COP</span></span>' +
                            '<span class="pill pill-warning">Pendiente</span>' +
                            '<span class="val text-truncate">' + escaparHtml(meta.hotel) + '</span>' +
                            (meta.ciudad ? '<span class="sub">(' + escaparHtml(meta.ciudad) + ')</span>' : '') +
                            '<span class="sub fw-bold" style="color:var(--accent);">#' + escaparHtml(meta.cod) + '</span>' +
                            '<span class="sub"><i class="bi bi-calendar3 me-1"></i>' + escaparHtml(meta.fi) + ' – ' + escaparHtml(meta.ff) + '</span>' +
                        '</div>' +
                        '<div class="sub text-truncate mt-1">' +
                            '<i class="bi bi-credit-card me-1"></i><b>' + escaparHtml(meta.pasarela) + '</b>' +
                            (meta.ref ? '<span class="mx-1">·</span> Ref: <b>' + escaparHtml(meta.ref) + '</b>' : '') +
                            '<span class="mx-1">·</span> Recién enviado' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-2 flex-shrink-0">' +
                    soporteHtml +
                    '<button type="button" class="pt-btn-mini js-pt-ver-reserva" data-reserva-id="' + escaparHtml(meta.resId) + '" data-reserva-cod="' + escaparHtml(meta.cod) + '" title="Ver expediente de la reserva #' + escaparHtml(meta.cod) + '">' +
                        '<i class="bi bi-box-arrow-up-right"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';

        list.insertBefore(card, list.firstChild);

        // Actualizar sumatoria de "En Verificación" y contadores de chips
        var wrapPend = document.getElementById('ptSumPendienteWrap');
        var lblPend  = document.getElementById('ptSumPendienteLbl');
        if (wrapPend && lblPend) {
            var nuevoPend = parseFloat(wrapPend.getAttribute('data-raw') || '0') + parseFloat(meta.monto || 0);
            wrapPend.setAttribute('data-raw', nuevoPend);
            lblPend.textContent = Math.round(nuevoPend).toLocaleString('es-CO');
        }

        actualizarContadoresTx();
        setTimeout(function () { card.classList.remove('is-just-created'); }, 4000);
    }

    // ═══ RECARGA ASÍNCRONA SILENCIOSA DEL FRAGMENTO ═══
    window.pgTxReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliPagosRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'pagos-transacciones';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (reservaIdOpcional) urlObj.searchParams.set('reserva_id', reservaIdOpcional);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        if (motivo !== 'silent-post-pago') {
            pgtxdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');
        }

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
            if (!res.ok) return;
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliPagosRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                actualizarContadoresTx();
                sincronizarFichaReservaObjetivo();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                pgtxdbg('Transacciones sincronizadas desde servidor (' + res.ms + 'ms) · total=' + tot, 'ok');
            }
        })
        .catch(function () {});
    };

    if (!window.__jsPagosTransaccionesClienteV23Listo) {
        window.__jsPagosTransaccionesClienteV23Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#pgTxDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#pgTxDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#pgTxDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#pgTxDbgCopy'))    { e.stopPropagation(); DBG.copy(); pgtxdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#pgTxDbgRefresh')) {
                e.stopPropagation();
                window.pgTxReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            if (t.closest('#ptBtnTogglePago')) {
                var box = document.getElementById('ptNewPagoBox');
                if (box) {
                    var abriendo = box.classList.contains('d-none');
                    box.classList.toggle('d-none');
                    if (abriendo) sincronizarFichaReservaObjetivo();
                    pgtxdbg('Click: ' + (abriendo ? 'Abrir' : 'Cerrar') + ' formulario Registrar Pago', 'ev');
                }
                return;
            }
            if (t.closest('#ptBtnClosePago')) {
                var boxC = document.getElementById('ptNewPagoBox');
                if (boxC) boxC.classList.add('d-none');
                pgtxdbg('Click: Cerrar formulario Registrar Pago', 'ev');
                return;
            }

            if (t.closest('#ptBtnClearFile')) {
                e.preventDefault();
                e.stopPropagation();
                limpiarArchivoSeleccionado(true);
                pgtxdbg('Soporte adjunto removido por el usuario', 'info');
                return;
            }

            if (t.closest('#ptBtnViewPreview')) {
                e.preventDefault();
                e.stopPropagation();
                var dzP = document.getElementById('ptDropzone');
                if (dzP) {
                    abrirModalVistaPrevia(
                        dzP.getAttribute('data-preview-url'),
                        dzP.getAttribute('data-preview-type'),
                        dzP.getAttribute('data-preview-title')
                    );
                }
                return;
            }

            var dzClick = t.closest('#ptDropzone');
            if (dzClick) {
                var inpF = document.getElementById('ptInputFile');
                if (inpF) inpF.click();
                return;
            }

            var prvBtn = t.closest('.js-pt-preview-url');
            if (prvBtn) {
                e.preventDefault();
                abrirModalVistaPrevia(
                    prvBtn.getAttribute('data-url'),
                    prvBtn.getAttribute('data-type') || 'auto',
                    prvBtn.getAttribute('data-title') || 'Soporte de Pago'
                );
                return;
            }

            var btnVerRes = t.closest('.js-pt-ver-reserva');
            if (btnVerRes) {
                e.preventDefault();
                var rid = btnVerRes.getAttribute('data-reserva-id') || '';
                var cod = btnVerRes.getAttribute('data-reserva-cod') || '';
                pgtxdbg('Click: Ver reserva #' + cod + ' (id=' + rid + ') en Mis Reservas', 'ev');

                if (window.apReloadFragment && rid) {
                    window.apReloadFragment(rid, 'salto-desde-transacciones');
                }
                var tabParent = document.querySelector('[data-bs-target="#cli-reservas"]');
                if (tabParent) tabParent.click();
                setTimeout(function () {
                    var tabChild = document.querySelector('[data-bs-target="#mr-sub-activas"]');
                    if (tabChild) tabChild.click();
                }, 120);
                return;
            }

            var chip = t.closest('.pt-filter-chip');
            if (chip && chip.closest('#cliPagosRoot')) {
                document.querySelectorAll('#cliPagosRoot .pt-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosTx(true);
            }
        });

        ['dragenter', 'dragover'].forEach(function (evName) {
            document.addEventListener(evName, function (e) {
                var dz = e.target && e.target.closest ? e.target.closest('#ptDropzone') : null;
                if (!dz) return;
                e.preventDefault();
                e.stopPropagation();
                dz.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (evName) {
            document.addEventListener(evName, function (e) {
                var dz = e.target && e.target.closest ? e.target.closest('#ptDropzone') : null;
                if (!dz) return;
                e.preventDefault();
                e.stopPropagation();
                dz.classList.remove('is-dragover');
                if (evName === 'drop' && e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    asignarArchivoAlInput(e.dataTransfer.files[0], 'Drag & Drop');
                }
            });
        });

        document.addEventListener('paste', function (e) {
            var root = document.getElementById('cliPagosRoot');
            if (!root || !root.offsetParent) return;

            var items = (e.clipboardData || (e.originalEvent && e.originalEvent.clipboardData))?.items;
            if (!items) return;

            for (var i = 0; i < items.length; i++) {
                if (items[i].kind === 'file') {
                    var blob = items[i].getAsFile();
                    if (blob) {
                        var box = document.getElementById('ptNewPagoBox');
                        if (box && box.classList.contains('d-none')) {
                            box.classList.remove('d-none');
                            sincronizarFichaReservaObjetivo();
                        }
                        var ext = (blob.type === 'application/pdf') ? '.pdf' : '.png';
                        var namedFile = new File([blob], 'comprobante-pegado-' + Date.now() + ext, { type: blob.type || 'image/png' });
                        asignarArchivoAlInput(namedFile, 'Ctrl+V');
                        e.preventDefault();
                        break;
                    }
                }
            }
        });

        document.addEventListener('change', function (e) {
            var t = e.target; if (!t) return;

            if (t.id === 'ptInputFile' && t.files && t.files.length > 0) {
                asignarArchivoAlInput(t.files[0], 'Explorador');
                return;
            }

            if (t.id === 'ptSelectReserva') {
                var opt = t.options[t.selectedIndex];
                var cod = opt ? (opt.getAttribute('data-cod') || 'Todas') : 'Todas';
                var est = opt ? (opt.getAttribute('data-estado') || '') : '';
                pgtxdbg('Selector Filtro Reserva: #' + cod + (est ? ' · estado=[' + est + ']' : ''), 'ev');

                if (t.value) {
                    var formSel = document.getElementById('ptFormSelectReserva');
                    if (formSel && formSel.querySelector('option[value="' + t.value + '"]')) {
                        formSel.value = t.value;
                        sincronizarFichaReservaObjetivo();
                    }
                }

                actualizarContadoresTx();
                aplicarFiltrosTx(true);
                return;
            }

            if (t.id === 'ptFormSelectReserva') {
                sincronizarFichaReservaObjetivo();
                var optF = t.options[t.selectedIndex];
                pgtxdbg('Reserva destino para soporte cambiada a #' + (optF ? optF.getAttribute('data-cod') : t.value) +
                        ' · Hotel: "' + (optF ? optF.getAttribute('data-hotel') : '') + '"', 'ev');
                return;
            }

            if (t.id === 'ptSelectPasarela') {
                var optP = t.options[t.selectedIndex];
                var nomP = optP ? (optP.getAttribute('data-nombre') || '') : '';
                var hPas = document.getElementById('ptHiddenIdPasarela');
                var hMet = document.getElementById('ptHiddenMetodoPago');
                if (hPas) hPas.value = t.value;
                if (hMet && nomP) hMet.value = nomP;
                pgtxdbg('Formulario Pago: seleccionada pasarela "' + nomP + '" (id=' + t.value + ')', 'ev');
            }
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'ptSearchInput') {
                aplicarFiltrosTx(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosTx(true);
                }, 350);
            }
        });

        // ═══ ENVÍO AJAX CON BARRA MINIMALISTA ANIMADA E INSERCIÓN INSTANTÁNEA ═══
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'cliPagoForm') return;
            e.preventDefault();

            var btn      = document.getElementById('ptBtnSubmitPago');
            var alertBox = document.getElementById('ptMainAlert');
            var sendBar  = document.getElementById('ptSendingBar');
            var sendTxt  = document.getElementById('ptSendingText');
            var sendSub  = document.getElementById('ptSendingSub');
            var sendIco  = document.getElementById('ptSendingIcon');
            var formBox  = document.getElementById('ptNewPagoBox');
            var dz       = document.getElementById('ptDropzone');

            var selRes = document.getElementById('ptFormSelectReserva');
            var optRes = (selRes && selRes.options.length) ? selRes.options[selRes.selectedIndex] : null;
            var selPas = document.getElementById('ptSelectPasarela');
            var optPas = (selPas && selPas.options.length) ? selPas.options[selPas.selectedIndex] : null;

            var metaEnvio = {
                resId:       optRes ? optRes.value : (form.querySelector('[name="id_rsv_reservas"]')?.value || ''),
                cod:         optRes ? (optRes.getAttribute('data-cod') || '?') : '?',
                hotel:       optRes ? (optRes.getAttribute('data-hotel') || 'Propiedad') : 'Propiedad',
                ciudad:      optRes ? (optRes.getAttribute('data-ciudad') || '') : '',
                fi:          optRes ? (optRes.getAttribute('data-fi') || '—') : '—',
                ff:          optRes ? (optRes.getAttribute('data-ff') || '—') : '—',
                monto:       form.querySelector('[name="monto"]')?.value || '0',
                pasarela:    optPas ? (optPas.getAttribute('data-nombre') || 'Directo') : (form.querySelector('[name="metodo_pago"]')?.value || 'Directo'),
                ref:         form.querySelector('[name="referencia_externa"]')?.value || '',
                previewUrl:  dz ? (dz.getAttribute('data-preview-url') || '') : '',
                previewType: dz ? (dz.getAttribute('data-preview-type') || '') : ''
            };

            var oldHtml = btn ? btn.innerHTML : '';
            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Enviando…'; }

            // Mostrar barra minimalista de envío
            if (sendBar) {
                sendBar.classList.remove('d-none', 'is-ok');
                if (sendIco) sendIco.className = 'bi bi-cloud-arrow-up-fill text-info';
                if (sendTxt) sendTxt.textContent = 'Enviando comprobante · Reserva #' + metaEnvio.cod + ' (' + metaEnvio.hotel + ') · ' + fmtCop(metaEnvio.monto);
                if (sendSub) sendSub.textContent = 'Subiendo soporte…';
            }

            var fd = new FormData(form);
            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            pgtxdbg('POST Pago (reserva=#' + metaEnvio.cod + ' · id=' + metaEnvio.resId + ' · monto=$' + metaEnvio.monto + ') → ' + form.getAttribute('action'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json, text/html'
                },
                body: fd
            })
            .then(function (r) {
                var ms = Math.round(performance.now() - t0);
                var ct = (r.headers.get('content-type') || '').toLowerCase();
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { ok: r.ok, status: r.status, ms: ms, json: j, isHtml: false }; });
                }
                return r.text().then(function () { return { ok: r.ok, status: r.status, ms: ms, json: { success: r.ok }, isHtml: true }; });
            })
            .then(function (res) {
                var j = res.json || {};
                if (res.ok && (res.isHtml || j.success !== false)) {
                    pgtxdbg('Pago registrado con éxito · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');

                    // Si el backend devolvió la URL definitiva del archivo guardado, usarla
                    var srvUrl = (j.transaccion && (j.transaccion.url_soporte_pago || j.transaccion.soporte_pago)) || j.url_soporte_pago || '';
                    if (srvUrl) metaEnvio.previewUrl = srvUrl;

                    // 1. Transformar la barra minimalista a estado completado y ocultar el formulario
                    if (sendBar) {
                        sendBar.classList.add('is-ok');
                        if (sendIco) sendIco.className = 'bi bi-check-circle-fill text-success';
                        if (sendTxt) sendTxt.textContent = '¡Comprobante enviado! · Reserva #' + metaEnvio.cod + ' (' + metaEnvio.hotel + ') · ' + fmtCop(metaEnvio.monto);
                        if (sendSub) sendSub.textContent = 'En verificación';
                        setTimeout(function () { sendBar.classList.add('d-none'); }, 5000);
                    }
                    if (formBox) formBox.classList.add('d-none');

                    // 2. Insertar inmediatamente la transacción en la lista (conservamos el blob URL para que pueda ver su PDF o Imagen al instante)
                    insertarFilaTransaccionOptimista(metaEnvio);
                    limpiarArchivoSeleccionado(false);
                    var refInput = form.querySelector('[name="referencia_externa"]');
                    if (refInput) refInput.value = '';

                    if (window.pgIniReloadFragment) window.pgIniReloadFragment('post-pago');
                    if (window.apReloadFragment) window.apReloadFragment(null, 'post-pago');
                    return;
                }

                if (sendBar) sendBar.classList.add('d-none');
                pgtxdbg('Error al registrar pago (HTTP ' + res.status + ' · ' + res.ms + 'ms): ' + (j.message || 'Verifica los campos'), 'err');
                var listaErrores = [];
                if (j.errors && typeof j.errors === 'object') {
                    Object.keys(j.errors).forEach(function (campo) {
                        [].concat(j.errors[campo]).forEach(function (m) {
                            listaErrores.push('<li><b>' + campo + ':</b> ' + m + '</li>');
                            pgtxdbg('422 [' + campo + ']: ' + m, 'err');
                        });
                    });
                }
                if (alertBox) {
                    alertBox.className = 'pt-alert-err';
                    alertBox.innerHTML = '<b>' + (j.message || 'No se pudo registrar el pago') + '</b>' +
                        (listaErrores.length ? '<ul class="mb-0 mt-1 ps-3">' + listaErrores.join('') + '</ul>' : '');
                }
            })
            .catch(function (err) {
                if (sendBar) sendBar.classList.add('d-none');
                pgtxdbg('Error de red al registrar pago: ' + (err && err.message ? err.message : 'red'), 'err');
                if (alertBox) {
                    alertBox.className = 'pt-alert-err';
                    alertBox.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Error de conexión al enviar el pago.';
                }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
            });
        });
    }

    actualizarContadoresTx();
    sincronizarFichaReservaObjetivo();
    if (document.getElementById('ptSelectReserva')?.value) {
        aplicarFiltrosTx(false);
    }

    var SRV = @json($pgTxDbgData);
    pgtxdbg('PG-TX-v2.3-DBG activo · tx=' + (SRV ? SRV.transacciones : 0) +
            ' · pagado=$' + (SRV ? SRV.pagado : 0) +
            ' · pendiente=$' + (SRV ? SRV.pendiente : 0) +
            ' · Barra Minimalista + Ficha Destacada listas', 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { pgtxdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
