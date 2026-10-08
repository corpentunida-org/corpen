{{-- resources/views/rsv/admin/partials/tab-finanzas.blade.php --}}
{{-- ESTILO: hoja de cálculo SUAVE · celdas 8px · encabezados 9px · tonos claros --}}

<div id="finRoot">

    <style>
        /* ═══ BASE — anclada a #finRoot (misma paleta del panel) ═══ */
        #finRoot {
            --grid:    #f1f5f9;
            --grid-2:  #e9eef4;
            --head:    #fafbfd;
            --ink:     #64748b;
            --ink-2:   #475569;
            --ink-3:   #94a3b8;
            --ink-4:   #b3bcc7;
            --accent:  #3d7a5c;
            --accent-bg: #f0f7f2;
            --accent-bd: #d5e7dc;
            font-family: Calibri, "Segoe UI", system-ui, sans-serif;
            font-size: 8px;
            line-height: 1.4;
            color: var(--ink);
        }

        #finRoot .strong { color: var(--ink-2); font-weight: 600; }
        #finRoot .num    { font-variant-numeric: tabular-nums; }
        #finRoot .sub    { font-size: 7.5px; line-height: 1.25; color: var(--ink-4); margin-top: .5px; }

        #finRoot .dot {
            display: inline-block; width: 5px; height: 5px;
            border-radius: 50%; background: currentColor;
            margin-right: 4px; vertical-align: middle; opacity: .75;
        }

        /* ═══ TABLA ═══ */
        #finRoot .xls-table { width: 100%; border-collapse: separate; border-spacing: 0; }

        #finRoot .xls-table th,
        #finRoot .xls-table td {
            border-right: 1px solid var(--grid);
            border-bottom: 1px solid var(--grid);
            padding: 2px 7px;
            height: 20px;
            vertical-align: middle;
            white-space: nowrap;
            background: #fff;
            font-size: 8px;              /* ← CELDAS 8px */
            font-weight: 400;
            color: var(--ink);
        }

        #finRoot .xls-table thead th {
            position: sticky; top: 0; z-index: 2;
            font-size: 9px;              /* ← ENCABEZADOS 9px */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ink-3);
            background: var(--head);
        }

        #finRoot .xls-table td.row-index {
            position: sticky; left: 0; z-index: 1;
            background: var(--head); color: #d4dae2;
            font-size: 7.5px; font-weight: 400; text-align: center;
            width: 22px; min-width: 22px; padding: 0;
        }

        #finRoot .xls-table tbody tr:hover td { background: #fafcfe; }
        #finRoot .xls-table tbody tr:hover td.row-index { background: #f3f6fa; }

        /* Celda seleccionada — verde clarito, nada de azul fuerte */
        #finRoot .cell-active {
            outline: 1px solid #a3cfb6;
            outline-offset: -1px;
            background: #f4faf6 !important;
        }

        /* ═══ BARRA FX ═══ */
        #finRoot .fx-ref {
            min-width: 52px;
            display: flex; align-items: center; justify-content: center;
            background: var(--head); border-right: 1px solid var(--grid);
            font-size: 8.5px; font-weight: 600; color: var(--ink-3);
        }
        #finRoot .fx-sym {
            width: 26px;
            display: flex; align-items: center; justify-content: center;
            background: var(--head); border-right: 1px solid var(--grid);
            font-style: italic; font-size: 8.5px; color: var(--ink-4);
        }

        #finRoot .fin-input {
            border: 0; box-shadow: none !important; outline: none;
            font-family: inherit; font-size: 9px; color: var(--ink-2);
            background: transparent; width: 100%;
        }
        #finRoot .fin-input::placeholder { color: #c3ccd6; }

        #finRoot .fin-select {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2);
            font-family: inherit; font-size: 9px; font-weight: 500;
            padding: 2px 8px; border-radius: 3px; cursor: pointer;
            box-shadow: none !important; outline: none;
        }
        #finRoot .fin-select:focus { border-color: #c8d6cd; }

        /* ═══ LINKS Y ACCIONES ═══ */
        #finRoot .link-cell {
            color: var(--accent); text-decoration: none;
            font-weight: 600; font-size: 8px;
        }
        #finRoot .link-cell:hover { text-decoration: underline; }

        #finRoot .btn-actions {
            border: 0; background: transparent; color: var(--ink-3);
            padding: 1px 4px; font-size: 8.5px; line-height: 1; border-radius: 3px;
        }
        #finRoot .btn-actions:hover { background: #eef2f6; color: var(--ink-2); }

        #finRoot .btn-mini {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
            font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px;
        }
        #finRoot .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }

        #finRoot .btn-green {
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid var(--accent-bd);
            font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;
        }
        #finRoot .btn-green:hover { background: #e3f1e9; color: #34684e; }

        /* ═══ CHIPS PASTEL ═══ */
        #finRoot .chip {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 0 7px; border-radius: 8px;
            font-size: 7.5px; font-weight: 600; border: 1px solid; line-height: 13px;
        }
        #finRoot .chip-green { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
        #finRoot .chip-red   { color:#b06a6a; background:#fdf4f4; border-color:#f0dcdc; }
        #finRoot .chip-amber { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }

        /* ═══ PESTAÑAS DE HOJAS ═══ */
        #finRoot .sheet-add {
            border: 0; background: transparent; width: 22px; height: 22px;
            color: var(--ink-4); font-size: 8px;
        }
        #finRoot .sheet-add:hover { background: #f1f5f9; }

        #finRoot .sheet-tab {
            border: 0; background: transparent;
            font-family: inherit;
            font-size: 9px; color: var(--ink-4);
            padding: 3px 12px; border-right: 1px solid var(--grid);
            height: 22px; display: inline-flex; align-items: center; gap: 5px;
        }
        #finRoot .sheet-tab i { font-size: 8.5px; opacity: .7; }
        #finRoot .sheet-tab:hover { background: #f7fafc; color: var(--ink-2); }
        #finRoot .sheet-tab.active {
            background: #fff; color: var(--ink-2); font-weight: 600;
            box-shadow: inset 0 -2px 0 #b9d4c5;
        }

        /* ═══ MODAL SUAVE ═══ */
        #finRoot .fin-modal .modal-content {
            border: 1px solid var(--grid-2); border-radius: 5px;
            box-shadow: 0 8px 30px rgba(15,23,42,.06);
            font-family: inherit; color: var(--ink);
        }
        #finRoot .fin-modal .modal-header { border-bottom: 1px solid var(--grid); padding: 9px 13px; }
        #finRoot .fin-modal .modal-body   { padding: 13px; }
        #finRoot .fin-modal .modal-footer { border-top: 1px solid var(--grid); padding: 9px 13px; }

        #finRoot .fin-modal .row-item {
            display: flex; justify-content: space-between; gap: 8px;
            padding: 3px 0; border-bottom: 1px dashed var(--grid);
            font-size: 8.5px;
        }
        #finRoot .fin-modal .row-item:last-child { border-bottom: 0; }
        #finRoot .fin-modal .row-item .lbl { color: var(--ink-4); }
        #finRoot .fin-modal .row-item .val { color: var(--ink-2); font-weight: 600; font-variant-numeric: tabular-nums; }
    </style>


    <!-- ═══ BARRA DE TÍTULO ═══ -->
    <div class="d-flex align-items-center justify-content-between px-2 py-1.5 border-bottom"
         style="background: var(--head); border-color: var(--grid) !important;">

        <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size: .8rem; color: #b9d4c5;"></i>
            <span class="strong" style="font-size: 9.5px;">FINANZAS.XLSX</span>
            <span class="sub" style="margin: 0;">Control de recaudos y pasarelas</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="sub d-none d-md-inline" style="margin: 0;">
                <i class="bi bi-clock-history me-1"></i>{{ now()->format('d/m/Y H:i') }}
            </span>
            <span style="font-size: 9px; font-weight: 600; color: #5d8a70;">
                Σ ${{ number_format(($transacciones ?? collect())->sum('monto'), 2) }}
            </span>
        </div>
    </div>


    <!-- ═══ BARRA FX (referencia + búsqueda + estado) ═══ -->
    <div class="d-flex align-items-stretch border-bottom bg-white" style="border-color: var(--grid) !important;">

        <div class="fx-ref"><span id="celda-activa">A1</span></div>
        <div class="fx-sym">fx</div>

        <div class="flex-grow-1 d-flex align-items-center px-2">
            <i class="bi bi-search me-2" style="font-size: 8.5px; color: #c3ccd6;"></i>
            <input type="text"
                   id="search_transaccion"
                   class="fin-input"
                   placeholder="Buscar por referencia…"
                   aria-label="Buscar transacción"
                   autocomplete="off">
        </div>

        <div class="d-flex align-items-center border-start px-2" style="border-color: var(--grid) !important;">
            <select id="filtro_estado_pago" class="fin-select" aria-label="Filtrar por estado" style="width: 130px;">
                <option value="">Todos los estados</option>
                <option value="aprobado">Aprobado</option>
                <option value="pendiente">Pendiente</option>
                <option value="rechazado">Rechazado</option>
            </select>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════ -->
    <!-- HOJA 1 · TRANSACCIONES                       -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="sheet-transacciones" class="sheet-pane">

        <div class="overflow-auto" style="max-height: 56vh;">

            <table class="xls-table" style="min-width: 860px;">

                <thead>
                    <tr>
                        <th style="width: 22px; min-width: 22px;"></th>
                        <th>Referencia</th>
                        <th>Reserva</th>
                        <th>Pasarela / Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Soporte</th>
                        <th class="text-end" style="padding-right: 8px;">·</th>
                    </tr>
                </thead>

                <tbody id="tbodyTransacciones">

                    @forelse($transacciones ?? [] as $trx)

                        @php
                            $estado = strtolower($trx->estado_pago ?? 'pendiente');

                            $dotClass = match($estado) {
                                'aprobado', 'completado', 'pagado' => 'dot-ok',
                                'rechazado', 'cancelado'           => 'dot-bad',
                                default                            => 'dot-warn',
                            };

                            $chipClass = match($estado) {
                                'aprobado', 'completado', 'pagado' => 'chip-green',
                                'rechazado', 'cancelado'           => 'chip-red',
                                default                            => 'chip-amber',
                            };
                        @endphp

                        <tr class="trx-row">

                            <td class="row-index">{{ $loop->iteration }}</td>


                            <!-- A · REFERENCIA -->
                            <td class="strong">
                                {{ $trx->referencia_externa ?? 'N/A' }}
                                <div class="sub">ID #{{ $trx->id }}</div>
                            </td>


                            <!-- B · RESERVA -->
                            <td>
                                #{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}
                            </td>


                            <!-- C · PASARELA / MÉTODO -->
                            <td>
                                <i class="bi bi-shield-check me-1" style="font-size: 7.5px; color: #a3cfb6;"></i>{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Pasarela Directa' }}
                                <div class="sub">{{ $trx->metodo_pago ?? 'General' }}</div>
                            </td>


                            <!-- D · MONTO -->
                            <td class="text-end num strong">
                                ${{ number_format($trx->monto, 2) }}
                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                            </td>


                            <!-- E · ESTADO -->
                            <td>
                                <span class="dot {{ $dotClass }}"></span>{{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}
                            </td>


                            <!-- F · FECHA -->
                            <td class="num" style="color: #a8b3c1;">
                                {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                            </td>


                            <!-- G · SOPORTE -->
                            <td class="text-center">
                                @if(!empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#')
                                    <a href="{{ $trx->url_soporte_pago }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="link-cell" title="Ver soporte de pago">
                                        <i class="bi bi-file-earmark-text me-1"></i>Ver
                                    </a>
                                @else
                                    <span style="color: #d4dae2;">—</span>
                                @endif
                            </td>


                            <!-- H · ACCIONES -->
                            <td class="text-end" style="padding-right: 8px;">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn-actions" title="Ver detalles"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalTransaccion{{ $trx->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    @if(!empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#')
                                        <a href="{{ $trx->url_soporte_pago }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="btn-actions" style="color: #5d8a70;" title="Abrir soporte">
                                            <i class="bi bi-paperclip"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                        </tr>


                        <!-- ═══ MODAL DETALLE (suave) ═══ -->
                        <div class="modal fade fin-modal" id="modalTransaccion{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <div>
                                            <div class="sub" style="margin: 0; letter-spacing: .05em; text-transform: uppercase; font-size: 7.5px;">
                                                Transacción #{{ $trx->id }}
                                            </div>
                                            <div class="strong mt-0.5" style="font-size: 10px;">
                                                Detalle de transacción
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                                    </div>

                                    <div class="modal-body">

                                        <!-- Monto central -->
                                        <div class="text-center mb-3">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-1 {{ $chipClass }}"
                                                 style="width: 36px; height: 36px;">
                                                <i class="bi bi-cash-stack" style="font-size: .9rem;"></i>
                                            </div>
                                            <div class="num strong" style="font-size: 14px;">
                                                ${{ number_format($trx->monto, 2) }}
                                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="chip {{ $chipClass }}">{{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}</span>
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="mb-3" style="border: 1px solid var(--grid); border-radius: 4px; padding: 8px 10px;">

                                            <div class="row-item">
                                                <span class="lbl">Referencia</span>
                                                <span class="val">{{ $trx->referencia_externa ?? 'N/A' }}</span>
                                            </div>

                                            <div class="row-item">
                                                <span class="lbl">Reserva</span>
                                                <span class="val">#{{ $trx->id_rsv_reservas ?? 'N/A' }}</span>
                                            </div>

                                            <div class="row-item">
                                                <span class="lbl">Pasarela</span>
                                                <span class="val">{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Directa' }}</span>
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

                                        <!-- Soporte -->
                                        <div class="sub mb-1" style="letter-spacing: .05em; text-transform: uppercase; font-size: 7.5px;">
                                            <i class="bi bi-paperclip me-1"></i>Soporte de pago
                                        </div>

                                        @if(!empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#')
                                            <div class="d-flex align-items-center justify-content-between"
                                                 style="background: #f0f7f2; border: 1px solid #d8e9de; border-radius: 4px; padding: 7px 10px;">

                                                <div>
                                                    <div style="font-size: 8.5px; font-weight: 600; color: #5d8a70;">Soporte disponible</div>
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

                                    <div class="modal-footer">
                                        <button type="button" class="btn-mini px-3" data-bs-dismiss="modal">Cerrar</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty

                        <tr>
                            <td class="row-index">1</td>
                            <td colspan="8" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-inbox me-1"></i>
                                No hay transacciones financieras registradas en este momento.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- PAGINACIÓN -->
        <div class="border-top bg-white px-2 py-1 d-flex justify-content-center" style="border-color: var(--grid) !important; font-size: 8.5px;">
            @include('rsv.components.pagination', ['paginator' => $transacciones ?? null])
        </div>

    </div>


    <!-- ═══════════════════════════════════════════ -->
    <!-- HOJA 2 · PASARELAS                           -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="sheet-pasarelas" class="sheet-pane d-none">

        <div class="d-flex align-items-center justify-content-between px-2 py-1 border-bottom"
             style="background: var(--head); border-color: var(--grid) !important; font-size: 8.5px;">

            <span style="color: #a8b3c1;">
                <i class="bi bi-credit-card-2-front me-1"></i>Métodos de cobro y pasarelas de pago
            </span>

            <button type="button" class="btn-green d-inline-flex align-items-center gap-1">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i>Nueva Pasarela
            </button>
        </div>


        <div class="overflow-auto" style="max-height: 56vh;">

            <table class="xls-table" style="min-width: 600px;">

                <thead>
                    <tr>
                        <th style="width: 22px; min-width: 22px;"></th>
                        <th>ID</th>
                        <th>Pasarela</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end" style="padding-right: 8px;">·</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pasarelas ?? [] as $pasarela)

                        <tr>
                            <td class="row-index">{{ $loop->iteration }}</td>

                            <td class="num" style="color: #a8b3c1;">#{{ $pasarela->id }}</td>

                            <td class="strong">
                                <i class="bi {{ $pasarela->icono ?? 'bi-credit-card' }} me-1" style="font-size: 7.5px; color: #a9cfe8;"></i>{{ $pasarela->nombre ?? $pasarela->name ?? 'Pasarela' }}
                            </td>

                            <td style="white-space: normal; min-width: 200px; color: var(--ink-3);">
                                {{ $pasarela->descripcion ?? 'Canal de cobro integrado para transacciones de reservas automáticas.' }}
                            </td>

                            <td>
                                <span class="dot dot-ok"></span>ACTIVA
                            </td>

                            <td class="text-end" style="padding-right: 8px;">
                                <button type="button" class="btn-actions" title="Editar configuración">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td class="row-index">1</td>
                            <td colspan="5" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-credit-card-2-front me-1"></i>
                                No hay pasarelas de pago configuradas en el sistema.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- ═══ PESTAÑAS DE HOJAS ═══ -->
    <div class="d-flex align-items-center border-top px-1" style="background: var(--head); border-color: var(--grid) !important; height: 22px;">

        <button type="button" class="sheet-add" title="Nueva hoja"><i class="bi bi-plus-lg"></i></button>

        <button type="button" class="sheet-tab active" data-sheet="transacciones">
            <i class="bi bi-wallet2"></i>Transacciones
        </button>

        <button type="button" class="sheet-tab" data-sheet="pasarelas">
            <i class="bi bi-credit-card-2-front"></i>Pasarelas
        </button>

    </div>


    <!-- ═══ BARRA DE ESTADO ═══ -->
    <div class="d-flex align-items-center justify-content-between border-top bg-white px-2"
         style="height: 18px; font-size: 8.5px; color: #c3ccd6; border-color: var(--grid) !important;">

        <span>Listo</span>

        <div class="d-flex gap-3">
            <span>Visibles: <b class="strong" id="statVisibles">{{ ($transacciones ?? collect())->count() }}</b></span>
            <span>Σ: <b class="strong" style="color: #5d8a70;" id="statSuma">${{ number_format(($transacciones ?? collect())->sum('monto'), 2) }}</b></span>
        </div>

    </div>

</div>


@push('style')
<style>
    /* Puntos de estado pastel (scoped) */
    #finRoot .dot-ok   { color: #5d8a70; }
    #finRoot .dot-bad  { color: #c08484; }
    #finRoot .dot-warn { color: #b08a3e; }
</style>
@endpush


<script>
    document.addEventListener('DOMContentLoaded', function () {

        var root  = document.getElementById('finRoot');
        if (!root) return;

        /* ── Cambio de hojas (scoped a #finRoot) ── */
        var tabs  = root.querySelectorAll('.sheet-tab');
        var panes = root.querySelectorAll('.sheet-pane');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');
                panes.forEach(function (p) { p.classList.add('d-none'); });
                var pane = root.querySelector('#sheet-' + tab.dataset.sheet);
                if (pane) pane.classList.remove('d-none');
            });
        });


        /* ── Selección de celda suave + referencia en fx ── */
        var letras = ['A','B','C','D','E','F','G','H','I','J'];
        var refBox = document.getElementById('celda-activa');

        root.querySelectorAll('.xls-table tbody tr').forEach(function (tr) {

            tr.addEventListener('click', function (e) {
                if (e.target.closest('a, button, input, select')) return;

                var td = e.target.closest('td');
                if (!td || td.classList.contains('row-index')) return;

                root.querySelectorAll('.cell-active').forEach(function (c) {
                    c.classList.remove('cell-active');
                });
                td.classList.add('cell-active');

                var tds  = Array.prototype.slice.call(tr.querySelectorAll('td'))
                    .filter(function (c) { return !c.classList.contains('row-index'); });
                var idx  = tds.indexOf(td);
                var fila = tr.querySelector('.row-index');

                if (refBox && idx > -1) {
                    refBox.textContent = (letras[idx] || '?') + (fila ? fila.textContent.trim() : '');
                }
            });

        });


        /* ── Búsqueda instantánea + Σ en vivo ── */
        var search  = document.getElementById('search_transaccion');
        var filtro  = document.getElementById('filtro_estado_pago');
        var tbody   = document.getElementById('tbodyTransacciones');
        var statVis = document.getElementById('statVisibles');
        var statSum = document.getElementById('statSuma');

        function fmt(n) {
            return '$' + Number(n).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function aplicarFiltros() {
            if (!tbody) return;

            var q    = (search && search.value) ? search.value.toLowerCase().trim() : '';
            var est  = (filtro && filtro.value) ? filtro.value.toLowerCase() : '';
            var vis  = 0, sum = 0;

            tbody.querySelectorAll('.trx-row').forEach(function (row) {
                var okTxt  = !q   || row.textContent.toLowerCase().includes(q);
                var okEst  = !est || row.textContent.toLowerCase().includes(est);
                var show   = okTxt && okEst;

                row.style.display = show ? '' : 'none';

                if (show) {
                    vis++;
                    var m = row.querySelector('[data-monto]');
                    if (m) sum += parseFloat(m.dataset.monto || 0);
                }
            });

            if (statVis) statVis.textContent = vis;
            if (statSum) statSum.textContent = fmt(sum);
        }

        /* Necesitamos el monto numérico: lo agregamos por data-attr si no existe */
        if (tbody) {
            tbody.querySelectorAll('.trx-row').forEach(function (row) {
                var celdaMonto = row.children[4]; /* D · MONTO */
                if (celdaMonto && !celdaMonto.dataset.monto) {
                    var raw = (celdaMonto.textContent.match(/[\d.,]+/) || ['0'])[0];
                    celdaMonto.dataset.monto = parseFloat(raw.replace(/\./g, '').replace(',', '.')) || 0;
                }
            });
        }

        if (search) search.addEventListener('input', aplicarFiltros);
        if (filtro) filtro.addEventListener('change', aplicarFiltros);

    });
</script>
