{{-- ============================================================================
     RSV · ADMIN · FINANZAS · TRANSACCIONES · TRX-v1-DBG
     Parcial: resources/views/rsv/admin/partials/finanzas/transacciones.blade.php

     · DISEÑO UNIFICADO: estética "reservas-soft" (variables CSS, tabla
       compacta, btn-mini/btn-green, count-badge, xls-search y modales
       Bootstrap modal-sm idénticos al resto de pestañas del admin).
     · CRUD AJAX sin recargar: alta / edición / anulación lógica por fetch
       (estado_pago = Anulado, nunca borra · igual que el controlador);
       tras guardar refresca SOLO #sheet-transacciones (todas las copias).
     · MULTI-COPIA: la hoja puede aparecer más de una vez en la página;
       los id de modales se "unican" por copia (-c2, -c3…) y el refresco
       AJAX reemplaza todas las copias con el fragmento fresco.
     · RUTAS RESILIENTES: resueltas con route() y candidatos por si el
       nombre del resource cambia; si no hay ruta, la acción se oculta.
     · PANEL DE DEBUG (CAMBIO TRX-v1 · diseño TIP-v1.3 aprobado): pestaña
       terminal "DBG" de 16px fija SIEMPRE en la esquina inferior-derecha
       (right:0 · bottom:0, radius 3px solo superior-izquierdo) con LED de
       estado (verde = ok · rojo pulsante + conteo = errores) y panel cajón
       de 380px alineado al mismo borde. La regla exacta de distancias está
       documentada como "SPEC DE DISTANCIAS" dentro del CSS, y el panel
       esquiva a los toggles hermanos de las otras vistas midiendo píxeles
       reales (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Exclusivo de esta vista: el toggle y el panel nacen y mueren con el
       parcial (hermanos de #sheet-transacciones, fuera del contenedor que
       intercambia el refresco AJAX, así que sobreviven a crear/editar/
       anular) y el panel deduplica copias: un solo debug por vista.
       Telemetría que reporta: totales del servidor, página y desglose de
       estados, envíos AJAX (crear/editar/anular) con método real (PUT/
       DELETE por spoofing), errores 422 campo a campo, refresco del
       fragmento con tiempos, copias detectadas y apertura de detalle.
     ============================================================================ --}}
@php
    $listadoTransacciones = $transacciones ?? ($inmueble->transacciones ?? collect());

    $trxRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };

    $trxStore = $trxRuta([ 'rsv.transacciones-financieras.store', 'rsv.transacciones.store', 'rsv.transaccion-financiera.store', 'rsv.transaccionesfinancieras.store' ]);
    $trxBase = $trxRuta([ 'rsv.transacciones-financieras.index', 'rsv.transacciones.index', 'rsv.transaccion-financiera.index', 'rsv.transaccionesfinancieras.index' ]);

    try { $trxPasarelas = $pasarelas ?? \App\Models\Rsv\Pasarela::orderBy('nombre')->get(); }
    catch (\Throwable $e) { $trxPasarelas = collect(); }

    $trxEstados = ['Pendiente', 'Aprobado', 'Rechazado', 'Anulado'];
    $trxTotal = method_exists($listadoTransacciones, 'total') ? $listadoTransacciones->total() : $listadoTransacciones->count();

    /* ── Colector del panel de debug (TRX-v1-DBG) · via al panel vía la directiva json de Blade ── */
    $trxFragmento = \Illuminate\Support\Facades\Route::has('rsv.admin.transacciones.fragmento')
        ? route('rsv.admin.transacciones.fragmento') : null;

    $trxDbgSoporte = 0;
    $trxDbgEstados = [];
    foreach ($listadoTransacciones as $trxDbgItem) {
        if (!empty($trxDbgItem->soporte_pago) && !empty($trxDbgItem->url_soporte_pago) && $trxDbgItem->url_soporte_pago !== '#') { $trxDbgSoporte++; }
        $trxDbgClave = strtolower((string) ($trxDbgItem->estado_pago ?? 'pendiente'));
        $trxDbgEstados[$trxDbgClave] = ($trxDbgEstados[$trxDbgClave] ?? 0) + 1;
    }

    $trxDbg = [
        'version'       => 'TRX-v1-DBG',
        'vista'         => 'partials/finanzas/transacciones.blade.php',
        'render'        => now()->format('H:i:s'),
        'transacciones' => $trxTotal,
        'en_pagina'     => $listadoTransacciones->count(),
        'pagina'        => method_exists($listadoTransacciones, 'currentPage') ? $listadoTransacciones->currentPage() : 1,
        'por_pagina'    => method_exists($listadoTransacciones, 'perPage') ? $listadoTransacciones->perPage() : 'todo',
        'pasarelas'     => $trxPasarelas->count(),
        'con_soporte'   => $trxDbgSoporte,
        'estados'       => $trxDbgEstados,
        'editable'      => (bool) ($trxStore || $trxBase),
        'rutas'         => [
            'store'     => $trxStore     ?: 'NO definida',
            'index'     => $trxBase      ?: 'NO definida',
            'fragmento' => $trxFragmento ?: 'NO definida',
        ],
        'errores'       => [],
    ];
@endphp

{{-- TRX-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano de la hoja: vive y muere con el parcial · NO viaja en el refresco AJAX interno) --}}
<div id="trxDbgToggle" title="Debug Transacciones (TRX-v1-DBG)"><span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span></div>
<div id="trxDbgPanel" role="dialog" aria-label="Panel de debug de Transacciones Financieras">
    <div class="trx-dbg-header" id="trxDbgHeader">
        <span>DEBUG · Finanzas · Transacciones · TRX-v1-DBG</span>
        <span class="trx-dbg-controls">
            <button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>
        </span>
    </div>
    <div id="trxDbgBody"></div>
</div>

<div id="sheet-transacciones" class="sheet-pane">

    <div class="reservas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
            .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
            .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
            .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
            .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
            .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
            .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
            .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
            .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }
            .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
            .reservas-soft .st-warn { background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px; color: #c98a85; font-size: 8px; font-weight: 600; padding: 6px 9px; }
            .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 5px; font-family: Calibri, "Segoe UI", sans-serif; font-size: 9.5px; color: #64748b; }
            .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px; }
            .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .ps-err-msg { font-size: 7.5px; color: #c98a85; }

            @keyframes destelloFila { 0%,100% { background-color: transparent; } 35% { background-color: rgba(61,122,92,.16); } }
            .reservas-soft tr.fila-nueva > td { animation: destelloFila 1.5s ease; }

            /* ═════════ SPEC DE DISTANCIAS · TRX-v1-DBG (tab terminal TIP-v1.3) ═════════
               TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la esquina
                           inferior-derecha (nunca sube: con 16px de alto cabe bajo
                           cualquier otro toggle sin tocarlo)
                         · altura fija 16px · padding lateral 8px
                         · radius 3px solo sup-izq · borde 1px arriba/izq
                         · monospace 8px · LED 5px (verde ok / rojo pulsante = error)
               PANEL     · right:0px · bottom = max(20, vecino + 4px) medido en px
                           reales con getBoundingClientRect (los toggles ocultos 0×0
                           no cuentan) · (20 = 16 de tab + 4 de aire mínimo)
                         · ancho 380px · radius 6px 0 0 0 (cajón pegado al borde) */
            #trxDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #trxDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .trx-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .trx-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: trxDbgBlink 1s steps(2,start) infinite; }
            @keyframes trxDbgBlink { 50% { opacity: .35; } }
            .trx-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #trxDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
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

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-wallet2 me-1" style="font-size: 8px; color: #5d8a70;"></i> Transacciones Financieras
            </h6>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span class="count-badge" title="Transacciones registradas (total del contexto)">
                    <b>{{ $trxTotal }}</b> transacción(es)
                </span>
                <input type="text" id="trxSearch" class="xls-search" placeholder="Buscar transacción…" autocomplete="off" style="width: 170px;">
                @if($trxStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#trxModalCreate">
                        <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Transacción
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 860px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 25px;">#</th>
                        <th>Referencia</th>
                        <th>Reserva</th>
                        <th>Pasarela / Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Soporte</th>
                        <th class="text-center" style="width: 130px;">·</th>
                    </tr>
                </thead>
                <tbody id="tbodyTransacciones">

                    @forelse($listadoTransacciones as $trx)
                        @php
                            $estado    = strtolower($trx->estado_pago ?? 'pendiente');
                            $colorEst  = match($estado) {
                                'aprobado', 'completado', 'pagado'  => '#a3cfb6',
                                'rechazado', 'cancelado', 'anulado' => '#c98a85',
                                default                             => '#f5d38c',
                            };
                            $conSoporte = !empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#';
                            $trxBuscar = strtolower(trim(
                                ($trx->referencia_externa ?? '') . ' ' .
                                ($trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? '') . ' ' .
                                ($trx->pasarela->nombre ?? $trx->pasarela->name ?? '') . ' ' .
                                ($trx->metodo_pago ?? '') . ' ' .
                                ($trx->estado_pago ?? '')
                            ));
                        @endphp

                        <tr class="trx-row" data-id="{{ $trx->id }}" data-trx-search="{{ $trxBuscar }}">
                            <td class="text-center sub">{{ $loop->iteration }}</td>
                            <td class="strong">
                                {{ $trx->referencia_externa ?? 'N/A' }}
                                <div class="sub">ID #{{ $trx->id }}</div>
                            </td>
                            <td>#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</td>
                            <td>
                                <i class="bi bi-shield-check me-1" style="font-size: 7.5px; color: #a3cfb6;"></i>{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Pasarela Directa' }}
                                <div class="sub">{{ $trx->metodo_pago ?? 'General' }}</div>
                            </td>
                            <td class="text-end strong">
                                ${{ number_format((float) ($trx->monto ?? 0), 2) }}
                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                            </td>
                            <td class="strong">
                                <span class="st-dot" style="background: {{ $colorEst }};"></span> {{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}
                            </td>
                            <td class="sub">
                                {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="text-center">
                                @if($conSoporte)
                                    <a href="{{ $trx->url_soporte_pago }}" target="_blank" rel="noopener noreferrer" style="font-size: 8.5px; font-weight: 600; color: #5d8a70; text-decoration: none;">
                                        <i class="bi bi-file-earmark-text me-1"></i>Ver
                                    </a>
                                @else
                                    <span style="color: #d4dae2;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-mini js-ver-trx"
                                        data-id="{{ $trx->id }}"
                                        data-referencia="{{ $trx->referencia_externa ?? 'N/A' }}"
                                        data-reserva="#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}"
                                        data-pasarela="{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Directa' }}"
                                        data-metodo="{{ $trx->metodo_pago ?? 'N/A' }}"
                                        data-monto="{{ number_format((float) ($trx->monto ?? 0), 2) }}"
                                        data-moneda="{{ $trx->moneda ?? 'COP' }}"
                                        data-estado="{{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}"
                                        data-fecha="{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i:s') : 'N/A' }}"
                                        data-soporte="{{ $conSoporte ? 1 : 0 }}"
                                        data-soporte-url="{{ $trx->url_soporte_pago ?? '#' }}">Ver</button>
                                @if($trxBase)
                                    <button type="button" class="btn-mini" data-bs-toggle="modal" data-bs-target="#trxEditModal-{{ $trx->id }}">Editar</button>
                                    <button type="button" class="btn-mini" style="color:#c98a85;" data-bs-toggle="modal" data-bs-target="#trxDelModal-{{ $trx->id }}">Del</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-inbox me-1"></i> No hay transacciones financieras registradas en este momento.
                            </td>
                        </tr>
                    @endforelse

                    <tr class="trx-sin-res" style="display:none;">
                        <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-search me-1"></i> Ninguna transacción coincide con la búsqueda.
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

        {{-- MODAL CREAR --}}
        @if($trxStore)
        <div class="modal fade reservas-soft-modal" id="trxModalCreate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Nueva Transacción</h6>
                    </div>
                    <form action="{{ $trxStore }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="st-warn mb-2 d-none ps-err-general"></div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Reserva (ID) <span style="color:#c98a85;">*</span></label>
                                    <input type="number" min="1" class="form-control form-control-sm" name="id_rsv_reservas" required placeholder="Ej: 15">
                                    <div class="ps-err-msg" data-error-for="id_rsv_reservas"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Monto <span style="color:#c98a85;">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required placeholder="0.00">
                                    <div class="ps-err-msg" data-error-for="monto"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Pasarela</label>
                                    <select class="form-select form-select-sm" name="id_rsv_pasarela">
                                        <option value="">— Directo (auto) —</option>
                                        @foreach($trxPasarelas as $gp)
                                            <option value="{{ $gp->id }}">{{ $gp->nombre ?? $gp->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="id_rsv_pasarela"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Método</label>
                                    <input type="text" class="form-control form-control-sm" name="metodo_pago" maxlength="60" placeholder="Transferencia, Efectivo…">
                                    <div class="ps-err-msg" data-error-for="metodo_pago"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select form-select-sm" name="estado_pago">
                                        @foreach($trxEstados as $est)
                                            <option value="{{ $est }}" {{ $est === 'Pendiente' ? 'selected' : '' }}>{{ strtoupper($est) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="estado_pago"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Moneda</label>
                                    <input type="text" class="form-control form-control-sm" name="moneda" maxlength="5" value="COP">
                                    <div class="ps-err-msg" data-error-for="moneda"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Referencia externa</label>
                                    <input type="text" class="form-control form-control-sm" name="referencia_externa" maxlength="100" placeholder="Nequi 1234, Credi…">
                                    <div class="ps-err-msg" data-error-for="referencia_externa"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Soporte de pago</label>
                                    <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                    <div class="sub">Opcional · imagen o PDF</div>
                                    <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- MODALES EDITAR Y ELIMINAR POR FILA --}}
        @foreach($listadoTransacciones as $trx)
            @if($trxBase)
            <div class="modal fade reservas-soft-modal" id="trxEditModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                            <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Editar #{{ $trx->id }}</h6>
                        </div>
                        <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data" data-trx-id="{{ $trx->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-3">
                                <div class="st-warn mb-2 d-none ps-err-general"></div>
                                <div class="mb-2" style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; color: #64748b;">
                                    Reserva <b>#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</b> · Creada {{ $trx->created_at ? $trx->created_at->format('d/m/Y') : '—' }}
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label">Monto <span style="color:#c98a85;">*</span></label>
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
                                                <option value="{{ $gp->id }}" {{ $trx->id_rsv_pasarela == $gp->id ? 'selected' : '' }}>{{ $gp->nombre ?? $gp->name }}</option>
                                            @endforeach
                                            @if($trx->id_rsv_pasarela && !$trxPasarelas->contains('id', $trx->id_rsv_pasarela))
                                                <option value="{{ $trx->id_rsv_pasarela }}" selected>#{{ $trx->id_rsv_pasarela }}</option>
                                            @endif
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
                                        <label class="form-label">Reemplazar soporte</label>
                                        <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                        <div class="sub">Opcional · deja vacío para conservar el actual</div>
                                        <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade reservas-soft-modal" id="trxDelModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-body p-3 text-center">
                            <i class="bi bi-slash-circle" style="font-size: 1.2rem; color: #c98a85;"></i>
                            <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Anular transacción</h6>
                            <p class="mb-2" style="font-size: 8.5px; color: #94a3b8;">
                                Se marcará <b style="color: #334155;">#{{ $trx->referencia_externa ?? $trx->id }}</b> por <b style="color: #334155;">${{ number_format((float) ($trx->monto ?? 0), 2) }}</b> como <b>ANULADA</b>.
                            </p>
                            <div class="st-warn mb-2 d-none ps-err-general"></div>

                            @if(strtolower((string) ($trx->estado_pago ?? '')) === 'anulado')
                                <div class="st-warn mb-2">
                                    <i class="bi bi-lock-fill me-1"></i> Esta transacción ya está ANULADA.
                                </div>
                                <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled style="font-size: 11px; background: #fcfdfe; color: #d4dae2; border: 1px solid #f1f5f9; cursor: not-allowed;">
                                    <i class="bi bi-slash-circle"></i> Anular
                                </button>
                            @else
                                <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" data-trx-id="{{ $trx->id }}" onsubmit="if(!confirm('¿Anular esta transacción?')){event.stopPropagation();return false;}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm py-1 px-2 w-100" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;">
                                        <i class="bi bi-slash-circle"></i> Anular definitivamente
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

    </div>
</div>

{{-- MODAL DETALLE COMPARTIDO (REDUCIDO A DISEÑO RESERVAS-SOFT) --}}
<div class="modal fade reservas-soft-modal" id="modalTransaccionDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Detalle de transacción</h6>
            </div>
            <div class="modal-body p-3">
                <div style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; color: #64748b; margin-bottom: 8px;">
                    Referencia <b id="fdRef" style="color:#334155;">—</b><br>
                    <span id="fdId">ID interno #—</span>
                </div>
                <table class="table table-sm table-borderless mb-0" style="font-size: 8px;">
                    <tr><td class="text-uppercase fw-bold text-muted" style="width:70px;">Reserva</td><td id="fdReserva" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Pasarela</td><td id="fdPasarela" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Método</td><td id="fdMetodo" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Monto</td><td id="fdMonto" class="strong text-end" style="color:#3d7a5c;">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Estado</td><td id="fdEstado" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Fecha</td><td id="fdFecha" class="text-end text-muted">—</td></tr>
                </table>
                <div id="fdSoporteCaja" class="mt-2 text-center" style="border-top: 1px solid #f1f5f9; padding-top: 8px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    if (window.__jsTransaccionesListo) return;
    window.__jsTransaccionesListo = true;

    var URL_FRAGMENTO = '{{ Route::has("rsv.admin.transacciones.fragmento") ? route("rsv.admin.transacciones.fragmento") : "" }}';
    var PAGINA_ACTUAL = 1;

    /* ═══ 0) PANEL DE DEBUG LOCAL TRX-v1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no interfiere con el resto del script.
       Exclusiva de esta vista: los nodos son HTML del parcial (hermanos de
       #sheet-transacciones, FUERA del contenedor que intercambia el refresco
       AJAX, así que sobreviven a crear/editar/anular) y el respaldo solo
       re-crea DENTRO de la primera hoja montada. Cero document.body.
       Como la hoja puede renderizarse en varias copias, el panel también
       DEDUPLICA nodos sobrantes: un solo debug por vista. */
    var DBG = (window.TrxDbg && window.TrxDbg.alive && window.TrxDbg.alive())
        ? window.TrxDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven como hermanos de #sheet-transacciones y sobreviven al
                   refresco AJAX interno), se re-crean DENTRO de la primera
                   hoja. Cero document.body (exclusividad total). */
                var rootDbg = document.getElementById('sheet-transacciones');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                /* Dedup multi-copia: la hoja puede existir varias veces en la
                   página; el panel de debug debe ser ÚNICO (se conserva el 1º) */
                var dups = document.querySelectorAll('#trxDbgToggle');
                for (var d = 1; d < dups.length; d++) { if (dups[d].parentNode) { dups[d].parentNode.removeChild(dups[d]); } }
                dups = document.querySelectorAll('#trxDbgPanel');
                for (var d2 = 1; d2 < dups.length; d2++) { if (dups[d2].parentNode) { dups[d2].parentNode.removeChild(dups[d2]); } }
                if (!$('trxDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'trxDbgToggle';
                    t.title = 'Debug Transacciones (TRX-v1-DBG)';
                    t.innerHTML = '<span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('trxDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'trxDbgPanel';
                    p.innerHTML = '<div class="trx-dbg-header" id="trxDbgHeader">' +
                        '<span>DEBUG · Finanzas · Transacciones · TRX-v1-DBG</span>' +
                        '<span class="trx-dbg-controls">' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>' +
                        '</span></div><div id="trxDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* TRX-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('trxDbgToggle'), p = $('trxDbgPanel');
                if (t) { t.style.bottom = '0px'; } // SIEMPRE pegada abajo
                if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; } // 16 tab + 4 aire
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
                    var txt = '=== DEBUG transacciones TRX-v1-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        }).catch(function () {
                            api.log('no se pudo copiar (permisos del navegador)', 'warn');
                        });
                    } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('trxDbgToggle'); }
            };
            window.TrxDbg = api;
            return api;
        })();
    if (DBG) { DBG.ensure(); } // re-apila, deduplica y re-crea nodos si algún swap externo los reemplazó
    var trxdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    /* Botones del panel por DELEGACIÓN en document (el guard __jsTransaccionesListo
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__trxDbgDocHandler) { document.removeEventListener('click', window.__trxDbgDocHandler); }
    window.__trxDbgDocHandler = function (e) {
        if (!DBG) { return; }
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#trxDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#trxDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#trxDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#trxDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__trxDbgDocHandler);
    if (window.__trxDbgDblHandler) { document.removeEventListener('dblclick', window.__trxDbgDblHandler); }
    window.__trxDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#trxDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__trxDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($trxDbg);
    if (DBG) {
        trxdbg('TRX-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con el parcial)', 'ok');
        trxdbg('init · total=' + (SRVDBG ? SRVDBG.transacciones : '?') + ' transacción(es) · en página=' + (SRVDBG ? SRVDBG.en_pagina : '?') +
            ' · página=' + (SRVDBG ? SRVDBG.pagina : '?') + '/' + (SRVDBG ? SRVDBG.por_pagina : '?') +
            ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : '?') + ' · editable=' + !!(SRVDBG && SRVDBG.editable), 'info');
        if (SRVDBG && SRVDBG.estados) {
            var _ek = Object.keys(SRVDBG.estados), _partes = [];
            for (var _ei = 0; _ei < _ek.length; _ei++) { _partes.push(_ek[_ei] + '=' + SRVDBG.estados[_ek[_ei]]); }
            if (_partes.length) { trxdbg('estados · ' + _partes.join(' · '), 'debug'); }
        }
        if (SRVDBG && typeof SRVDBG.con_soporte !== 'undefined') {
            trxdbg('soporte · ' + SRVDBG.con_soporte + ' de ' + SRVDBG.en_pagina + ' en página con soporte adjunto', 'debug');
        }
        if (SRVDBG && SRVDBG.rutas) {
            var _rk = Object.keys(SRVDBG.rutas);
            for (var _ri = 0; _ri < _rk.length; _ri++) {
                if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                    trxdbg('ruta no definida: rsv…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
                }
            }
            trxdbg('Rutas · store=' + SRVDBG.rutas.store + ' · index=' + SRVDBG.rutas.index + ' · fragmento=' + SRVDBG.rutas.fragmento, 'debug');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                trxdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }
    }

    function cerrarBruto(el) {
        el.classList.remove('show'); el.style.display = 'none'; el.setAttribute('aria-hidden', 'true'); el.removeAttribute('aria-modal');
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); }); document.body.classList.remove('modal-open');
    }
    function ocultarModal(el) {
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) { window.bootstrap.Modal.getOrCreateInstance(el).hide(); }
        setTimeout(function () { if (el.classList.contains('show')) cerrarBruto(el); }, 700);
    }
    function limpiarResiduos() {
        if (document.querySelectorAll('.modal.show').length === 0) {
            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); }); document.body.classList.remove('modal-open');
        }
    }
    function unicarModales() {
        var hojas = document.querySelectorAll('#sheet-transacciones');
        if (hojas.length > 1 && window.TrxDbg && window.TrxDbg.log) { window.TrxDbg.log('multi-copia · ' + hojas.length + ' hojas #sheet-transacciones · unificando ids de modales', 'warn'); }
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
        if (!window.bootstrap || !window.bootstrap.Toast) { window.alert(mensaje); return; }
        var div = document.createElement('div');
        div.className = 'toast align-items-center text-bg-' + (tipo === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed m-3';
        div.setAttribute('role', 'alert'); div.style.cssText = 'bottom:0;right:0;z-index:2100;';
        div.innerHTML = '<div class="d-flex"><div class="toast-body">' + mensaje + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>';
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
        if (!g) { window.alert(msg); return; }
        g.textContent = msg; g.classList.remove('d-none');
    }
    function resaltarFila(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-transacciones tr[data-id="' + id + '"]').forEach(function (fila) {
            fila.classList.add('fila-nueva');
            setTimeout(function () { fila.classList.remove('fila-nueva'); }, 1600);
        });
    }
    function urlFragmento(pagina) {
        if (!URL_FRAGMENTO) return '';
        var qs = new URLSearchParams();
        var inm = new URLSearchParams(window.location.search).get('inmueble_id');
        if (inm) qs.set('inmueble_id', inm);
        var pg = pagina || PAGINA_ACTUAL || 1;
        if (pg > 1) qs.set('page_finanzas', pg);
        var s = qs.toString();
        return URL_FRAGMENTO + (s ? '?' + s : '');
    }
    function refrescarTabla(idResaltar, pagina) {
        var url = urlFragmento(pagina);
        if (!url) { trxdbg('refresco · sin fragmento → recarga nativa', 'warn'); window.location.reload(); return; }
        var __t0 = Date.now();
        trxdbg('refresco · fetch fragmento (página ' + (pagina || PAGINA_ACTUAL) + ')…', 'net');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                var temp = document.createElement('div'); temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-transacciones');
                var copias = document.querySelectorAll('#sheet-transacciones');
                if (!nuevo || copias.length === 0) { trxdbg('refresco · fragmento sin hoja → recarga nativa', 'warn'); window.location.reload(); return; }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                unicarModales(); aplicarFiltro(); if (idResaltar) resaltarFila(idResaltar); limpiarResiduos();
                if (DBG && DBG.ensure) { DBG.ensure(); }
                trxdbg('fragmento refrescado en ' + (Date.now() - __t0) + 'ms · copias actualizadas=' + copias.length + (idResaltar ? ' · fila #' + idResaltar + ' resaltada' : ''), 'ok');
            })
            .catch(function () { trxdbg('refresco falló · fallback recarga nativa', 'err'); window.location.reload(); });
    }
    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]'); var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }
        var tokIn = form.querySelector('input[name="_token"]'); var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';
        var mIn = form.querySelector('input[name="_method"]');
        var metodoReal = (mIn && mIn.value) ? String(mIn.value).toUpperCase() : 'POST';
        trxdbg('AJAX · ' + metodoReal + ' ' + form.getAttribute('action') + (metodoReal !== 'POST' ? ' (spoofing _method)' : ''), 'net');
        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, json: j }; }).catch(function () { return { ok: r.ok, status: r.status, json: null }; }); })
        .then(function (res) {
            if (res.ok && res.json && res.json.success) {
                var d = res.json.data || {};
                var idFila = d.id || (d.data && d.data.id) || form.getAttribute('data-trx-id') || null;
                ocultarModal(form.closest('.modal'));
                trxdbg('AJAX OK · ' + metodoReal + ' · ' + (res.json.message || 'guardado'), 'ok');
                trxdbg('refresco del fragmento programado (350ms)', 'debug');
                avisar(res.json.message || 'Guardado correctamente', 'success');
                setTimeout(function () { refrescarTabla(idFila); }, 350);
            } else if (res.status === 422 && res.json && res.json.errors) {
                trxdbg('AJAX 422 · validación falló · ' + Object.keys(res.json.errors).length + ' campo(s) con error', 'err');
                pintarErrores(form, res.json.errors);
            }
            else {
                trxdbg('AJAX falló · ' + ((res.json && res.json.message) || 'sin mensaje del servidor'), 'err');
                errorGeneral(form, (res.json && res.json.message) || 'No se pudo completar la operación.');
            }
        })
        .catch(function () { trxdbg('AJAX error de conexión', 'err'); errorGeneral(form, 'Error de conexión. Intenta de nuevo.'); })
        .finally(function () { if (btn) { btn.disabled = false; btn.innerHTML = txt; } });
    }
    function aplicarFiltro(inp) {
        inp = inp || document.getElementById('trxSearch');
        if (!inp) return;
        var val = (inp.value || '').toLowerCase().trim();
        var visibles = 0;
        document.querySelectorAll('#sheet-transacciones tr[data-trx-search]').forEach(function (tr) {
            var mostrar = !val || (tr.getAttribute('data-trx-search') || '').indexOf(val) !== -1;
            tr.style.display = mostrar ? '' : 'none';
            if(mostrar) visibles++;
        });
        document.querySelectorAll('#sheet-transacciones tr.trx-sin-res').forEach(function (tr) {
            tr.style.display = (val && visibles === 0) ? '' : 'none';
        });
    }
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.classList || !form.classList.contains('js-ajax-trx')) return;
        e.preventDefault(); enviarFormulario(form);
    });
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'trxSearch') aplicarFiltro(e.target);
    });
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var a = e.target.closest('#sheet-transacciones .pagination a');
        if (!a || !a.getAttribute('href')) return;
        e.preventDefault();
        try {
            var u = new URL(a.getAttribute('href'), window.location.origin);
            var pg = parseInt(u.searchParams.get('page_finanzas') || u.searchParams.get('page') || '1', 10);
            PAGINA_ACTUAL = isNaN(pg) ? 1 : pg;
            trxdbg('paginación → página ' + PAGINA_ACTUAL, 'net');
            refrescarTabla(null, PAGINA_ACTUAL);
        } catch (err) { trxdbg('paginación · URL inválida → navegación nativa', 'warn'); window.location.href = a.getAttribute('href'); }
    });
    document.addEventListener('show.bs.modal', function (e) {
        var m = e.target;
        if (!m || !m.closest || !m.closest('#sheet-transacciones')) return;
        trxdbg('modal · ' + (m.id || 'sin id') + (m.id.indexOf('trxEditModal') === 0 ? ' · modo editar' : (m.id.indexOf('trxDelModal') === 0 ? ' · anulación' : (m.id === 'trxModalCreate' ? ' · modo crear' : ''))), 'info');
        m.querySelectorAll('.ps-err-general').forEach(function (g) { g.classList.add('d-none'); g.textContent = ''; });
        m.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
        m.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
    });
    function iniciar() {
        var p = parseInt(new URLSearchParams(window.location.search).get('page_finanzas') || '1', 10);
        PAGINA_ACTUAL = isNaN(p) ? 1 : Math.max(1, p);
        unicarModales(); limpiarResiduos(); aplicarFiltro();
        trxdbg('DOM listo · página=' + PAGINA_ACTUAL + ' · copias de la hoja=' + document.querySelectorAll('#sheet-transacciones').length + ' · búsqueda activa #trxSearch', 'debug');
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>

<script>
(function () {
    'use strict';
    if (window.__jsTrxDetalleListo) return;
    window.__jsTrxDetalleListo = true;

    function abrirDetalle(btn) {
        var d = btn.dataset;
        if (window.TrxDbg && window.TrxDbg.log) { window.TrxDbg.log('detalle · transacción #' + (d.id || '—') + ' · ' + (d.referencia || 'N/A') + ' · $' + (d.monto || '0.00') + ' ' + (d.moneda || 'COP') + ' · ' + (d.estado || ''), 'info'); }
        function txt(id, val) { var el = document.getElementById(id); if (el) el.textContent = val || '—'; }

        txt('fdRef', d.referencia);
        txt('fdId', 'ID interno #' + (d.id || '—'));
        txt('fdReserva', d.reserva);
        txt('fdPasarela', d.pasarela);
        txt('fdMetodo', d.metodo);
        txt('fdMonto', '$' + (d.monto || '0.00') + ' ' + (d.moneda || 'COP'));
        txt('fdFecha', d.fecha);

        var est = document.getElementById('fdEstado');
        if (est) {
            est.textContent = d.estado || 'PENDIENTE';
            var n = (d.estado || '').toLowerCase();
            est.style.color = (n === 'aprobado' || n === 'completado' || n === 'pagado') ? '#3d7a5c' :
                              (n === 'rechazado' || n === 'anulado' || n === 'cancelado') ? '#c98a85' : '#b45309';
        }

        var caja = document.getElementById('fdSoporteCaja');
        if (caja) {
            caja.innerHTML = '';
            if (d.soporte === '1' && d.soporteUrl && d.soporteUrl !== '#') {
                var a = document.createElement('a');
                a.href = d.soporteUrl; a.target = '_blank'; a.rel = 'noopener noreferrer';
                a.className = 'btn-mini';
                a.innerHTML = '<i class="bi bi-file-earmark-text"></i> Ver soporte adjunto';
                caja.appendChild(a);
            } else {
                var vacio = document.createElement('span');
                vacio.style.cssText = 'font-size: 7.5px; color: #94a3b8; font-weight: 600;';
                vacio.textContent = 'Sin soporte de pago adjunto';
                caja.appendChild(vacio);
            }
        }

        var modal = document.getElementById('modalTransaccionDetalle');
        if (!modal) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) {
            window.bootstrap.Modal.getOrCreateInstance(modal).show();
        } else {
            modal.classList.add('show'); modal.style.display = 'block'; modal.removeAttribute('aria-hidden');
            var bd = document.createElement('div'); bd.className = 'modal-backdrop show';
            document.body.appendChild(bd); document.body.classList.add('modal-open');
        }
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('.js-ver-trx') : null;
        if (btn) { abrirDetalle(btn); return; }

        if (!(window.bootstrap && window.bootstrap.Modal)) {
            var dis = e.target.closest ? e.target.closest('[data-bs-dismiss="modal"]') : null;
            if (dis) {
                var m = dis.closest('.modal');
                if (m) {
                    m.classList.remove('show'); m.style.display = 'none'; m.setAttribute('aria-hidden', 'true');
                    document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                    document.body.classList.remove('modal-open');
                }
            }
        }
    });
})();
</script>
