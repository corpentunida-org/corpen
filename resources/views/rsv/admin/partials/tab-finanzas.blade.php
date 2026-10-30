{{-- resources/views/rsv/admin/partials/tab-finanzas.blade.php --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- FINANZAS · VISTA RESUMEN (SOLO LECTURA) · Transacciones + Pasarelas       --}}
{{-- Aquí NI se crea NI se edita: el alta/edición vive en TransaccionFinanciera --}}
{{-- Controller y PasarelaController. Esta vista solo consulta y muestra.      --}}
{{-- ESTILO: reservas-soft · idéntico al patrón de partials/reservas/status.blade.php --}}
{{-- NOTA: se retiró la simulación de hoja de cálculo (FINANZAS.XLSX, barra fx --}}
{{-- y pestañas). Los modales viven FUERA de la tabla (HTML válido) y los      --}}
{{-- estilos van inline (compatible con finanzasFragmento por AJAX).           --}}
{{--                                                                           --}}
{{-- · PANEL DE DEBUG (CAMBIO FIN-v1-1 · diseño TIP-v1.3 aprobado): pestaña    --}}
{{--   terminal "DBG" de 16px fija SIEMPRE en la esquina inferior-derecha      --}}
{{--   (right:0 · bottom:0, radius 3px solo superior-izquierdo) con LED de     --}}
{{--   estado (verde = ok · rojo pulsante + conteo = errores) y panel cajón    --}}
{{--   de 380px alineado al mismo borde. La regla exacta de distancias está    --}}
{{--   documentada como "SPEC DE DISTANCIAS" dentro del CSS, y el panel        --}}
{{--   esquiva a los toggles hermanos de las otras vistas midiendo píxeles    --}}
{{--   reales (getBoundingClientRect · bottom = max(20, vecino + 4)).          --}}
{{--   Exclusivo de esta vista: el toggle y el panel viven junto a #finRoot    --}}
{{--   (nacen y mueren con el fragmento). Telemetría que reporta: dataset      --}}
{{--   cargado (transacciones página/total, suma de página, distribución de    --}}
{{--   estados, soportes, pasarelas, paginación), filtros aplicados (búsqueda  --}}
{{--   y estado con conteo y suma en vivo), aperturas del modal de detalle y   --}}
{{--   errores capturados del servidor.                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

{{-- FIN-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano de #finRoot: vive y muere con el fragmento) --}}
<div id="finDbgToggle" title="Debug Finanzas (FIN-v1-DBG)"><span class="fin-dbg-led" id="finDbgLed"></span>DBG<span class="fin-dbg-badge" id="finDbgBadge">0</span></div>
<div id="finDbgPanel" role="dialog" aria-label="Panel de debug de Finanzas">
    <div class="fin-dbg-header" id="finDbgHeader">
        <span>DEBUG · Finanzas · FIN-v1-DBG</span>
        <span class="fin-dbg-controls">
            <button type="button" class="fin-dbg-btn" id="finDbgCopy">Copiar</button>
            <button type="button" class="fin-dbg-btn" id="finDbgClear">Limpiar</button>
            <button type="button" class="fin-dbg-btn" id="finDbgClose">✕</button>
        </span>
    </div>
    <div id="finDbgBody"></div>
</div>

<div class="finanzas-soft p-3 bg-white rounded-3 border" id="finRoot" style="border-color: #f1f5f9 !important;">

<style>
    /* ═══ BASE — mismas variables del panel (idénticas a reservas-soft) ═══ */
    .finanzas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .finanzas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .finanzas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .finanzas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .finanzas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .finanzas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .finanzas-soft .num    { font-variant-numeric: tabular-nums; }
    .finanzas-soft .sub    { font-size: 7.5px; line-height: 1.25; color: var(--ink-4); margin-top: .5px; }

    /* ═══ CONTROLES (mismas clases del ejemplo) ═══ */
    .finanzas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .finanzas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .finanzas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; text-decoration: none; }
    .finanzas-soft .btn-green:hover { background: #e3f1e9; color: #34684e; }
    .finanzas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
    .finanzas-soft .xls-search:focus { border-color: #c8d6cd; }
    .finanzas-soft .fin-select { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; cursor: pointer; }
    .finanzas-soft .fin-select:focus { border-color: #c8d6cd; }
    .finanzas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .finanzas-soft .count-badge b { color: var(--accent); }
    .finanzas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
    .finanzas-soft .link-cell { color: var(--accent); text-decoration: none; font-weight: 600; font-size: 8px; }
    .finanzas-soft .link-cell:hover { text-decoration: underline; }

    /* ═══ CHIPS PASTEL (solo modal detalle) ═══ */
    .finanzas-soft .chip { display: inline-flex; align-items: center; gap: 4px; padding: 0 7px; border-radius: 8px; font-size: 7.5px; font-weight: 600; border: 1px solid; line-height: 13px; }
    .finanzas-soft .chip-green { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
    .finanzas-soft .chip-red   { color:#b06a6a; background:#fdf4f4; border-color:#f0dcdc; }
    .finanzas-soft .chip-amber { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }

    /* ═══ MODAL DETALLE (mismo estilo suave del ejemplo) ═══ */
    .finanzas-soft .row-item { display: flex; justify-content: space-between; gap: 8px; padding: 3px 0; border-bottom: 1px dashed var(--grid); font-size: 8.5px; }
    .finanzas-soft .row-item:last-child { border-bottom: 0; }
    .finanzas-soft .row-item .lbl { color: var(--ink-4); }
    .finanzas-soft .row-item .val { color: var(--ink-2); font-weight: 600; font-variant-numeric: tabular-nums; }

    /* ═════════ SPEC DE DISTANCIAS · FIN-v1-DBG (tab terminal TIP-v1.3) ═════════
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
    #finDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    #finDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
    .fin-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .fin-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: finDbgBlink 1s steps(2,start) infinite; }
    @keyframes finDbgBlink { 50% { opacity: .35; } }
    .fin-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
    #finDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
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

@php
    /* ── Dataset blindado: la vista nunca explota si falta un dato ──
       $transacciones llega paginado (10/page, page_finanzas) desde
       AdminDashboardController (método index) / finanzasFragmento, con
       reserva.inmueble + pasarela precargados ( eager loading ).      */
    $finTrx = $transacciones ?? collect();
    $finPas = collect($pasarelas ?? []);

    /* Total global (respeta el paginador) y suma de la página visible */
    $finTotalTrx   = method_exists($finTrx, 'total') ? $finTrx->total() : $finTrx->count();
    $finSumaPagina = 0;
    foreach ($finTrx as $finT) { $finSumaPagina += (float) ($finT->monto ?? 0); }

    /* Normalización de estado → [clave filtro, color dot, clase chip] */
    $finEstado = function ($trx): array {
        $raw = strtolower(trim((string) ($trx->estado_pago ?? '')));
        if (in_array($raw, ['aprobado', 'completado', 'pagado'], true)) {
            return ['aprobado', '#5d8a70', 'chip-green'];
        }
        if (in_array($raw, ['rechazado', 'cancelado', 'anulado', 'declinado', 'fallido'], true)) {
            return ['rechazado', '#c08484', 'chip-red'];
        }
        return ['pendiente', '#b08a3e', 'chip-amber'];
    };

    /* URL de soporte confiable (ignora vacíos y '#') */
    $finSoporteOk = function ($trx): bool {
        return !empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#';
    };

    /* ── Colector del panel de debug (FIN-v1-DBG) · se serializa al panel como JSON ── */
    $finDist = ['aprobado' => 0, 'pendiente' => 0, 'rechazado' => 0];
    $finConSoporte = 0;
    foreach ($finTrx as $finT2) {
        $finPar = $finEstado($finT2);
        $finDist[$finPar[0]] = ($finDist[$finPar[0]] ?? 0) + 1;
        if ($finSoporteOk($finT2)) { $finConSoporte++; }
    }

    $finDbg = [
        'version'      => 'FIN-v1-DBG',
        'vista'        => 'partials/tab-finanzas.blade.php',
        'render'       => now()->format('H:i:s'),
        'solo_lectura' => true,
        'trx_pagina'   => $finTrx->count(),
        'trx_total'    => $finTotalTrx,
        'suma_pagina'  => number_format($finSumaPagina, 2, '.', ''),
        'pasarelas'    => $finPas->count(),
        'estados'      => $finDist,
        'con_soporte'  => $finConSoporte,
        'paginada'     => (method_exists($finTrx, 'hasPages') && $finTrx->hasPages()) ? 'sí' : 'no',
        'errores'      => [],
    ];
@endphp

    <!-- ═══ ENCABEZADO · Resumen financiero (solo lectura) ═══ -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-wallet2 me-1" style="font-size: 8px; color: #5d8a70;"></i> Finanzas
            <span class="sub" style="margin: 0 0 0 6px; display: inline;">· Resumen de recaudos y pasarelas</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b id="statVisibles">{{ $finTrx->count() }}</b> transacciones</span>
            <span class="count-badge">Σ <b id="statSuma">${{ number_format($finSumaPagina, 2) }}</b></span>
            <select id="filtro_estado_pago" class="fin-select" aria-label="Filtrar por estado" style="width: 128px;">
                <option value="">Todos los estados</option>
                <option value="aprobado">Aprobado</option>
                <option value="pendiente">Pendiente</option>
                <option value="rechazado">Rechazado</option>
            </select>
            <input type="text" id="search_transaccion" class="xls-search" placeholder="Buscar referencia, reserva…" autocomplete="off" aria-label="Buscar transacción">
        </div>
    </div>

    <!-- ═══ SECCIÓN 1 · TRANSACCIONES ═══ -->
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 720px;">
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Reserva</th>
                    <th>Pasarela / Método</th>
                    <th class="text-end">Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-center">Soporte</th>
                    <th class="text-center" style="width: 70px;">·</th>
                </tr>
            </thead>
            <tbody id="tbodyTransacciones">

                @forelse($finTrx as $trx)
                    @php
                        [$estKey, $estColor, $estChip] = $finEstado($trx);
                        $pasNombre     = data_get($trx, 'pasarela.nombre') ?? data_get($trx, 'pasarela.name') ?? 'Pasarela Directa';
                        $pasIcono      = data_get($trx, 'pasarela.icono');
                        $pasColor      = data_get($trx, 'pasarela.color');
                        $finHaySoporte = $finSoporteOk($trx);
                        $finInmueble   = data_get($trx, 'reserva.inmueble.name');
                    @endphp

                    <tr class="trx-row" data-estado="{{ $estKey }}">

                        <!-- Referencia -->
                        <td class="strong">
                            {{ $trx->referencia_externa ?? 'N/A' }}
                            <div class="sub">ID #{{ $trx->id }}</div>
                        </td>

                        <!-- Reserva (+ inmueble, ya viene precargado) -->
                        <td>
                            #{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}
                            @if($finInmueble)
                                <div class="sub">{{ $finInmueble }}</div>
                            @endif
                        </td>

                        <!-- Pasarela / Método -->
                        <td>
                            <i class="bi {{ $pasIcono ?: 'bi-shield-check' }} me-1" style="font-size: 7.5px; color: {{ $pasColor ?: '#a3cfb6' }};"></i>{{ $pasNombre }}
                            <div class="sub">{{ $trx->metodo_pago ?? 'General' }}</div>
                        </td>

                        <!-- Monto -->
                        <td class="text-end num strong" data-monto="{{ (float) ($trx->monto ?? 0) }}">
                            ${{ number_format((float) ($trx->monto ?? 0), 2) }}
                            <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                        </td>

                        <!-- Estado -->
                        <td>
                            <span class="st-dot" style="background: {{ $estColor }};"></span> {{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}
                        </td>

                        <!-- Fecha -->
                        <td class="num" style="color: var(--ink-4);">
                            {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                        </td>

                        <!-- Soporte -->
                        <td class="text-center">
                            @if($finHaySoporte)
                                <a href="{{ $trx->url_soporte_pago }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="link-cell" title="Ver soporte de pago">
                                    <i class="bi bi-file-earmark-text me-1"></i>Ver
                                </a>
                            @else
                                <span style="color: var(--ink-4);">—</span>
                            @endif
                        </td>

                        <!-- Acciones (solo ver detalle: aquí no se edita) -->
                        <td class="text-center">
                            <button type="button" class="btn-mini"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTransaccion{{ $trx->id }}">Ver</button>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-inbox me-1"></i> No hay transacciones financieras registradas en este momento.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Paginación (el controlador envía 10/page con page_finanzas) -->
    @if(method_exists($finTrx, 'hasPages') && $finTrx->hasPages())
        <div class="pt-1 d-flex justify-content-center">
            @include('rsv.components.pagination', ['paginator' => $finTrx])
        </div>
    @endif

    <!-- ═══ SECCIÓN 2 · PASARELAS (catálogo · solo lectura) ═══ -->
    <div class="d-flex justify-content-between align-items-center mt-3 mb-2 flex-wrap"
         style="gap: 8px; padding-top: 10px; border-top: 1px dashed var(--grid-2);">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-credit-card-2-front me-1" style="font-size: 8px; color: #5d8a70;"></i> Pasarelas de Pago
            <span class="sub" style="margin: 0 0 0 6px; display: inline;">· Catálogo global</span>
        </h6>
        <span class="count-badge"><b>{{ $finPas->count() }}</b> pasarelas</span>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 460px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pasarela</th>
                    <th>Configuración / Ícono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>

                @forelse($finPas as $pasarela)
                    <tr>
                        <td class="num" style="color: var(--ink-4);">#{{ $pasarela->id }}</td>
                        <td class="strong">
                            <i class="bi {{ $pasarela->icono ?: 'bi-credit-card' }} me-1"
                               style="font-size: 7.5px; color: {{ $pasarela->color ?: '#a9cfe8' }};"></i>
                            {{ $pasarela->nombre ?? $pasarela->name ?? 'Pasarela' }}
                        </td>
                        <td style="color: var(--ink-3);">
                            {{ $pasarela->icono ?: 'bi-credit-card' }} · {{ $pasarela->color ?: 'Predeterminado' }}
                        </td>
                        <td>
                            <span class="st-dot" style="background: #5d8a70;"></span> ACTIVA
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-credit-card-2-front me-1"></i> No hay pasarelas de pago configuradas en el sistema.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- ═══ MODALES DETALLE · Transacción (solo lectura, FUERA de la tabla) ═══ -->
    @foreach($finTrx as $trx)
        @php
            [$estKey, $estColor, $estChip] = $finEstado($trx);
            $finHaySoporte = $finSoporteOk($trx);
            $finInmueble   = data_get($trx, 'reserva.inmueble.name');
        @endphp

        <div class="modal fade" id="modalTransaccion{{ $trx->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">

                    <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                        <div>
                            <div class="sub" style="margin: 0; letter-spacing: .05em; text-transform: uppercase;">
                                Transacción #{{ $trx->id }}
                            </div>
                            <h6 class="fw-bold mb-0 mt-0.5" style="font-size: 0.95rem;">Detalle de transacción</h6>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    </div>

                    <div class="modal-body p-3">
                        <div class="text-center mb-2">
                            <div class="num strong" style="font-size: 14px;">
                                ${{ number_format((float) ($trx->monto ?? 0), 2) }}
                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="chip {{ $estChip }}">{{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}</span>
                            </div>
                        </div>

                        <div class="mb-2" style="border: 1px solid var(--grid); border-radius: 4px; padding: 8px 10px;">
                            <div class="row-item">
                                <span class="lbl">Referencia</span>
                                <span class="val">{{ $trx->referencia_externa ?? 'N/A' }}</span>
                            </div>
                            <div class="row-item">
                                <span class="lbl">Reserva</span>
                                <span class="val">#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</span>
                            </div>
                            @if($finInmueble)
                                <div class="row-item">
                                    <span class="lbl">Inmueble</span>
                                    <span class="val">{{ $finInmueble }}</span>
                                </div>
                            @endif
                            <div class="row-item">
                                <span class="lbl">Pasarela</span>
                                <span class="val">{{ data_get($trx, 'pasarela.nombre') ?? data_get($trx, 'pasarela.name') ?? 'Directa' }}</span>
                            </div>
                            <div class="row-item">
                                <span class="lbl">Método</span>
                                <span class="val">{{ $trx->metodo_pago ?? 'N/A' }}</span>
                            </div>
                            <div class="row-item">
                                <span class="lbl">Fecha</span>
                                <span class="val">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i:s') : 'N/A' }}</span>
                            </div>
                        </div>

                        @if($finHaySoporte)
                            <div class="d-flex align-items-center justify-content-between"
                                 style="background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 4px; padding: 7px 10px;">
                                <div>
                                    <div style="font-size: 8.5px; font-weight: 600; color: var(--accent);">Soporte disponible</div>
                                    <div class="sub" style="margin: 0;">El enlace tiene una vigencia temporal.</div>
                                </div>
                                <a href="{{ $trx->url_soporte_pago }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="btn-green text-decoration-none d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-box-arrow-up-right" style="font-size: 8px;"></i>Abrir
                                </a>
                            </div>
                        @else
                            <div style="border: 1px solid var(--grid); border-radius: 4px; padding: 7px 10px; font-size: 8.5px; color: var(--ink-4); background: var(--head);">
                                <i class="bi bi-file-earmark-x me-1"></i>
                                Esta transacción no tiene un soporte de pago asociado.
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

</div>

<script>
    (function () {
        'use strict';

        /* ═══ 0) PANEL DE DEBUG LOCAL FIN-v1-DBG (diseño TIP-v1.3 aprobado) ═══
           IIFE propia con getElementById: no toca el querySelector del panel.
           Exclusiva de esta vista: los nodos viven como hermanos de #finRoot
           (nacen y mueren con el fragmento) y el respaldo solo re-crea DENTRO
           de #finRoot. Nada se cuelga de document.body. */
        var DBG = (window.FinDbg && window.FinDbg.alive && window.FinDbg.alive())
            ? window.FinDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarNodos() {
                    /* Respaldo: si un swap externo borrara los nodos, se
                       re-crean DENTRO de #finRoot. Cero document.body. */
                    var rootDbg = document.getElementById('finRoot');
                    if (!rootDbg) { return; } // sin vista montada → cero nodos
                    if (!$('finDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'finDbgToggle';
                        t.title = 'Debug Finanzas (FIN-v1-DBG)';
                        t.innerHTML = '<span class="fin-dbg-led" id="finDbgLed"></span>DBG<span class="fin-dbg-badge" id="finDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('finDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'finDbgPanel';
                        p.innerHTML = '<div class="fin-dbg-header" id="finDbgHeader">' +
                            '<span>DEBUG · Finanzas · FIN-v1-DBG</span>' +
                            '<span class="fin-dbg-controls">' +
                            '<button type="button" class="fin-dbg-btn" id="finDbgCopy">Copiar</button>' +
                            '<button type="button" class="fin-dbg-btn" id="finDbgClear">Limpiar</button>' +
                            '<button type="button" class="fin-dbg-btn" id="finDbgClose">✕</button>' +
                            '</span></div><div id="finDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* FIN-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca
                   se apila: con 16px de alto cabe bajo cualquier otro toggle
                   sin tocarlo). Solo el PANEL esquiva a los vecinos de las
                   otras vistas: bottom = max(20, vecino + 4) en px reales. */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle']; // serie completa de hermanos
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                        var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('finDbgToggle'), p = $('finDbgPanel');
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
                        var txt = '=== DEBUG finanzas FIN-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                    alive: function () { return !!$('finDbgToggle'); }
                };
                window.FinDbg = api;
                return api;
            })();
        if (!DBG) { return; }

        /* Handlers de documento UNA sola vez (el guard __finDbgDocBound
           garantiza que se registran una única vez; sobreviven a los swaps) */
        if (!window.__finDbgDocBound) {
            window.__finDbgDocBound = true;
            window.__finDbgDocHandler = function (e) {
                var t = e.target;
                if (!t || !t.closest) { return; }
                if (t.closest('#finDbgToggle')) { DBG.togglePanel(); return; }
                if (t.closest('#finDbgClose')) { e.stopPropagation(); DBG.close(); return; }
                if (t.closest('#finDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
                if (t.closest('#finDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
            };
            document.addEventListener('click', window.__finDbgDocHandler);
            window.__finDbgDblHandler = function (e) {
                if (e.target && e.target.closest && e.target.closest('#finDbgHeader')) { DBG.close(); }
            };
            document.addEventListener('dblclick', window.__finDbgDblHandler);

            /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
               toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
            document.addEventListener('shown.bs.tab', function () {
                if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
            });

            /* Telemetría de modales por delegación (sobrevive a los swaps) */
            document.addEventListener('show.bs.modal', function (e) {
                var idm = (e.target && e.target.id) ? e.target.id : '';
                if (idm.indexOf('modalTransaccion') === 0) {
                    DBG.log('modal · detalle de transacción ' + idm.replace('modalTransaccion', '#') + ' abierto (solo lectura)', 'info');
                }
            });
        }

        DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
        var finDbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

        /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
        var SRVDBG = @json($finDbg);
        finDbg('FIN-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con la vista)', 'ok');
        finDbg('init · transacciones página=' + (SRVDBG ? SRVDBG.trx_pagina : '?') + ' · total=' + (SRVDBG ? SRVDBG.trx_total : '?') +
            ' · Σ página=$' + (SRVDBG ? SRVDBG.suma_pagina : '?') + ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : '?') +
            ' · solo lectura (aquí no se crea ni se edita)', 'info');
        if (SRVDBG && SRVDBG.estados) {
            finDbg('estados · aprobado=' + SRVDBG.estados.aprobado + ' · pendiente=' + SRVDBG.estados.pendiente +
                ' · rechazado=' + SRVDBG.estados.rechazado + ' · con soporte=' + (SRVDBG.con_soporte || 0) +
                ' de ' + (SRVDBG.trx_pagina || 0) + ' de la página', 'debug');
        }
        finDbg('paginación · ' + (SRVDBG && SRVDBG.paginada === 'sí' ? 'activa (el controlador envía 10/page)' : 'no paginada en esta carga'), 'debug');
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                finDbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        function finInit() {
            var root = document.getElementById('finRoot');
            if (!root) return;

            var search = document.getElementById('search_transaccion');
            var filtro = document.getElementById('filtro_estado_pago');
            var tbody  = document.getElementById('tbodyTransacciones');
            var vis    = document.getElementById('statVisibles');
            var sum    = document.getElementById('statSuma');

            function fmt(n) {
                return '$' + Number(n || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            /* ── Búsqueda + filtro de estado · Σ y contador en vivo ── */
            function aplicarFiltros() {
                if (!tbody) return;

                var q   = (search && search.value) ? search.value.toLowerCase().trim() : '';
                var est = (filtro && filtro.value) ? filtro.value : '';
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

                if (vis) vis.textContent = v;
                if (sum) sum.textContent = fmt(s);
            }

            /* FIN-v1-DBG · telemetría de filtros (búsqueda con debounce de
               400ms para no spamear el panel mientras se teclea) */
            var finFiltroTmr = null;
            function finLogFiltros(prefijo) {
                finDbg('filtro · ' + prefijo + ' · ' + (vis ? vis.textContent : '?') +
                    ' visible(s) · Σ ' + (sum ? sum.textContent : '?'), 'info');
            }

            if (search) search.addEventListener('input', function () {
                aplicarFiltros();
                clearTimeout(finFiltroTmr);
                finFiltroTmr = setTimeout(function () {
                    finLogFiltros('búsqueda "' + (search.value || '').trim() + '"');
                }, 400);
            });
            if (filtro) filtro.addEventListener('change', function () {
                aplicarFiltros();
                finLogFiltros('estado=' + (filtro.value || 'todos'));
            });

            finDbg('finanzas · panel listo · ' + (tbody ? tbody.querySelectorAll('.trx-row').length : 0) +
                ' fila(s) en página · búsqueda + filtro de estado activos', 'debug');
        }

        /* Compatible con carga normal Y con fragmentos AJAX
           (AdminDashboardController · finanzasFragmento): si el DOM ya
           está listo, inicializa de inmediato sin esperar un evento. */
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', finInit);
        } else {
            finInit();
        }
    })();
</script>
