<x-base-layout>
    {{-- 1. MODAL DE CARGA REFORZADO --}}
    <div id="loading-overlay" style="display: none; position: fixed; inset: 0; background: rgba(255, 255, 255, 0.98); z-index: 10000; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(8px); transition: all 0.5s ease;">
        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem; border-width: 0.20em;" role="status"></div>
        <h2 class="fw-bold text-dark fs-2 mb-1">Sincronizando Siasoft</h2>
        <p class="text-muted fs-6 mb-5">Validando integridad de movimientos bancarios...</p>
    </div>

    <div class="app-container py-5" style="background-color: #f1f4f9;">
        
        {{-- 2. HEADER DE CONTROL --}}
        <div class="d-flex align-items-center mb-6 px-4">
            <div class="symbol symbol-45px me-4">
                <div class="symbol-label bg-dark shadow-sm">
                    <i class="fas fa-shield-alt text-success fs-2"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h3 class="fw-bold m-0 text-gray-900 fs-3 tracking-tight">EXTRATOS BANCÁRIOS <span class="text-primary fw-normal ms-2 fs-7">v1.0 Corpe</span></h3>
                <div class="d-flex align-items-center gap-3 fs-10 mt-1">
                    <span class="text-muted fw-bold text-uppercase">Contabilidad / Auditoría / Movimientos</span>
                    <span class="badge badge-light-success fw-bold px-2 py-1 fs-10">CIFRADO AES-256</span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-white border-gray-300 fw-bold px-4 h-35px d-flex align-items-center">
                    <i class="fas fa-upload me-2 fs-9 text-primary"></i> IMPORTAR
                </a>
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-sm btn-dark fw-bold px-4 h-35px d-flex align-items-center">
                    <i class="fas fa-project-diagram me-2 fs-9 text-success"></i> MESA DE IDENTIFICACIÓN
                </a>
            </div>
        </div>

        {{-- 3. PANEL DE FILTRADO MAESTRO --}}
        <form method="GET" action="{{ route('contabilidad.extractos.index') }}" id="form-master-filter" class="bg-white p-5 mx-4 mb-6 shadow-sm border border-gray-200" style="border-radius: 8px;">
            <div class="row g-4 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fs-9 fw-bold text-gray-600 text-uppercase mb-2">Ciclo Contable</label>
                    <input type="month" name="periodo" value="{{ $periodo }}" class="form-control form-control-sm form-control-solid fw-bold h-35px">
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-9 fw-bold text-gray-600 text-uppercase mb-2">Estado</label>
                    <select name="estado" class="form-select form-select-sm form-select-solid fw-bold h-35px">
                        <option value="">TODOS</option>
                        <option value="Pendiente" {{ (isset($estado) && $estado == 'Pendiente') ? 'selected' : '' }}>PENDIENTES</option>
                        <option value="Conciliados" {{ (isset($estado) && $estado == 'Conciliados') ? 'selected' : '' }}>CONCILIADOS</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-9 fw-bold text-gray-600 text-uppercase mb-2">Cuenta Origen</label>
                    <select name="banco_id" class="form-select form-select-sm form-select-solid h-35px">
                        <option value="">CONSOLIDADO GLOBAL</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->banco }} - {{ substr($cuenta->numero_cuenta, -4) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fs-9 fw-bold text-gray-600 text-uppercase mb-2">Búsqueda de Alta Precisión</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid h-35px fs-8" placeholder="Hash, Cédula o Valor...">
                        <button type="submit" class="btn btn-secondary fw-bold px-6">EJECUTAR</button>
                        <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-light-danger btn-icon w-35px h-35px border-gray-200">
                            <i class="fas fa-sync fs-9"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- 4. GRID DE DATOS PROFESIONAL --}}
        <div class="bg-white shadow-sm border border-gray-300 mx-4 d-flex flex-column" style="border-radius: 4px; overflow: hidden; min-height: 650px;">
            
            <div class="d-flex bg-light border-bottom border-gray-200">
                <div class="px-6 py-3 bg-white border-end border-gray-300 fw-bolder fs-10 text-primary text-uppercase tracking-widest">
                    REGISTROS <span class="badge badge-light-info ms-2">{{ $extractos->total() }}</span>
                </div>
            </div>

            <div class="table-responsive flex-grow-1" id="table-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr class="header-titles">
                            <th class="col-index">#</th>
                            <th style="width: 100px;">FECHA</th>
                            <th style="width: 180px;">ENTIDAD</th>
                            <th style="width: 120px;">ID FISCAL</th>
                            <th style="width: 140px;">REF. OFICINA</th>
                            <th style="width: 180px;">LÍNEA / PRODUCTO</th> {{-- NUEVA COLUMNA --}}
                            <th style="width: 100px;">TIPO</th>
                            <th style="width: 140px;" class="text-end">IMPORTE</th>
                            <th style="width: 110px;" class="text-center">ESTADO</th>
                            <th style="width: 45px;" class="text-center">SOP</th>
                            <th style="width: 45px;" class="text-center">INS</th>
                        </tr>
                        <tr class="filter-row">
                            <td class="col-index"></td>
                            <td><input type="text" class="column-filter"></td>
                            <td><input type="text" class="column-filter"></td>
                            <td><input type="text" class="column-filter"></td>
                            <td><input type="text" class="column-filter"></td>
                            <td><input type="text" class="column-filter"></td> {{-- FILTRO LÍNEA --}}
                            <td><input type="text" class="column-filter"></td>
                            <td><input type="text" class="column-filter text-end"></td>
                            <td colspan="3" class="bg-light"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        @php
                            $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion'])
                                ->whereJsonContains('id_transaccion_bancaria', $movimiento->id_transaccion)
                                ->orWhereJsonContains('id_transaccion_bancaria', (string) $movimiento->id_transaccion)
                                ->first();
                            
                            // CONSTRUCCIÓN DEL POPOVER CON TODO EL FILLABLE
                            $popoverContent = "
                                <div class='p-3' style='min-width: 350px;'>
                                    <div class='mb-2 border-bottom pb-2 d-flex justify-content-between align-items-center'>
                                        <strong class='text-primary fs-7 text-uppercase'>Detalle del Registro</strong>
                                        <span class='badge badge-light-dark fs-10'>ID: {$movimiento->id_transaccion}</span>
                                    </div>
                                    <div class='row g-2'>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>Tercero (Mae)</div>
                                            <div class='fs-9 fw-bold'>".($comprobante->cod_ter_MaeTerceros ?? 'N/A')."</div>
                                        </div>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>Monto Pagado</div>
                                            <div class='fs-9 fw-bold text-success'>$ ".number_format($comprobante->monto_pagado ?? 0, 2)."</div>
                                        </div>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>Interacción ID</div>
                                            <div class='fs-9 fw-bold'>".($comprobante->id_interaction ?? 'N/A')."</div>
                                        </div>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>Usuario Procesador</div>
                                            <div class='fs-9 fw-bold'>".($comprobante->user->name ?? 'SYSTEM')."</div>
                                        </div>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>PR / CCO</div>
                                            <div class='fs-9 fw-bold'>".($comprobante->pr ?? '0')." / ".($comprobante->cco ?? '0')."</div>
                                        </div>
                                        <div class='col-6'>
                                            <div class='fs-10 text-muted text-uppercase fw-bold'>N° Cuota</div>
                                            <div class='fs-9 fw-bold text-primary'>".($comprobante->numero_cuota ?? 'N/A')."</div>
                                        </div>
                                    </div>
                                    <div class='mt-3 p-2 bg-light rounded'>
                                        <div class='text-muted fs-10 mb-1 fw-bold text-uppercase'>Token Temporal</div>
                                        <code class='text-dark fs-10'>".($comprobante->temp_token ?? '---')."</code>
                                    </div>
                                    <div class='mt-2'>
                                        <div class='text-muted fs-10 mb-1 fw-bold text-uppercase'>Observación Técnica</div>
                                        <div class='fs-9 italic text-gray-700'>".($comprobante->observacion ?? 'Sin notas')."</div>
                                    </div>
                                    <div class='mt-3 border-top pt-2'>
                                        <div class='text-muted fs-10 mb-1 fw-bold text-uppercase'>Hash de Seguridad</div>
                                        <code class='text-dark fs-10 cursor-pointer copy-hash' title='Click para copiar'>{$movimiento->hash_transaccion}</code>
                                    </div>
                                </div>";
                        @endphp

                        <tr class="data-row" 
                            data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" 
                            data-bs-html="true" data-bs-content="{{ $popoverContent }}">
                            
                            <td class="col-index">{{ $extractos->firstItem() + $index }}</td>
                            <td class="fw-bold text-dark font-monospace fs-9">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-800 fs-9">{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}</span>
                                    <span class="text-muted fs-10 font-monospace">****{{ substr($movimiento->cuentaBancaria->numero_cuenta, -4) }}</span>
                                </div>
                            </td>
                            <td class="font-monospace text-gray-600 fw-bold fs-9">{{ $movimiento->referencia_cedula ?? '---' }}</td>
                            <td class="text-uppercase text-gray-600 fs-9">{{ $movimiento->referencia_oficina ?? '---' }}</td>
                            
                            {{-- COLUMNA LÍNEA --}}
                            <td class="fw-bold text-primary fs-9 text-truncate" style="max-width: 180px;">
                                {{ $comprobante?->obligacion?->nombre ?? 'NO VINCULADO' }}
                            </td>

                            <td class="text-muted fs-10 fw-bold text-uppercase">{{ $comprobante?->tipo_pago ?? 'EXTRACTO' }}</td>
                            <td class="text-end fw-bold text-dark pe-4 fs-8">$ {{ number_format($movimiento->valor_ingreso, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @php
                                    $isConciliado = str_contains($movimiento->estado_conciliacion, 'Conciliado');
                                @endphp
                                <span class="gs-pill {{ $isConciliado ? 'pill-success' : 'pill-warning' }}">
                                    {{ $isConciliado ? 'CONCILIADO' : 'PENDIENTE' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($comprobante && $comprobante->ruta_archivo != '#')
                                    <a href="{{ $comprobante->ruta_archivo }}" target="_blank" class="text-primary"><i class="fas fa-file-pdf fs-5"></i></a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('contabilidad.extractos.show', $movimiento->id_transaccion) }}" class="text-gray-400 hover-primary"><i class="fas fa-external-link-alt fs-10"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-20 text-muted fw-bold">NO SE ENCONTRARON MOVIMIENTOS</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. FOOTER --}}
            <div class="bg-dark p-4 d-flex justify-content-between align-items-center text-white">
                <div class="d-flex align-items-center gap-5 fs-10 fw-bold text-gray-500">
                    <span>VISIBLES: <strong id="visible-count" class="text-white">{{ $extractos->count() }}</strong> / {{ $extractos->total() }}</span>
                    <span id="js-filter-active" style="display:none" class="text-warning"><i class="fas fa-filter me-1"></i> FILTRO DINÁMICO ACTIVO</span>
                </div>
                <div class="pagination-dark">
                    {{ $extractos->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .table-gsheets { width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; }
        .table-gsheets thead th { background: #f8f9fa; border: 1px solid #e2e8f0; padding: 10px 12px; font-weight: 800; color: #475569; position: sticky; top: 0; z-index: 20; text-transform: uppercase; }
        .table-gsheets tbody td { border: 1px solid #e2e8f0; padding: 8px 12px; vertical-align: middle; transition: background 0.1s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .table-gsheets tbody tr:hover { background-color: #f1f5f9 !important; outline: 1px solid #3b82f6; z-index: 5; position: relative; }
        .col-index { width: 45px; text-align: center !important; background: #f1f5f9; color: #64748b; font-weight: 800; border-right: 2px solid #e2e8f0 !important; }
        .gs-pill { padding: 3px 8px; border-radius: 4px; font-size: 9px; font-weight: 800; display: inline-block; min-width: 85px; text-align: center; }
        .pill-success { background: #dcfce7; color: #15803d; }
        .pill-warning { background: #fef3c7; color: #b45309; }
        .column-filter { width: 100%; border: 1px solid #cbd5e0; border-radius: 3px; font-size: 9px; padding: 3px 6px; }
        .pagination-dark .page-link { background: #1e293b; border-color: #334155; color: #94a3b8; font-size: 9px; padding: 4px 8px; }
        .pagination-dark .page-item.active .page-link { background: #3b82f6; border-color: #3b82f6; color: white; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            const popoverInstances = popoverTriggerList.map(el => new bootstrap.Popover(el, { container: 'body', delay: { "show": 200, "hide": 100 } }));

            // Filtrado JS
            const rows = document.querySelectorAll('#main-table tbody tr.data-row');
            const filters = document.querySelectorAll('.column-filter');
            const countLabel = document.getElementById('visible-count');
            const filterIndicator = document.getElementById('js-filter-active');

            filters.forEach((filter) => {
                filter.addEventListener('keyup', () => {
                    popoverInstances.forEach(p => p.hide());
                    let visibleCount = 0;
                    let active = false;

                    rows.forEach(row => {
                        let isMatch = true;
                        filters.forEach((f, i) => {
                            let val = f.value.toLowerCase();
                            if(val) {
                                active = true;
                                if (!row.cells[i+1].innerText.toLowerCase().includes(val)) isMatch = false;
                            }
                        });
                        row.style.display = isMatch ? '' : 'none';
                        if(isMatch) visibleCount++;
                    });
                    countLabel.innerText = visibleCount;
                    filterIndicator.style.display = active ? 'inline-block' : 'none';
                });
            });

            // Resizer de columnas
            const table = document.getElementById('main-table');
            table.querySelectorAll('th').forEach(th => {
                const resizer = document.createElement('div');
                resizer.classList.add('resizer');
                resizer.style.cssText = "position:absolute;right:0;top:0;width:4px;cursor:col-resize;height:100%;";
                th.appendChild(resizer);
                let x = 0, w = 0;
                const md = (e) => {
                    x = e.clientX; w = parseInt(window.getComputedStyle(th).width, 10);
                    document.addEventListener('mousemove', mm); document.addEventListener('mouseup', mu);
                };
                const mm = (e) => { th.style.width = `${w + (e.clientX - x)}px`; };
                const mu = () => { document.removeEventListener('mousemove', mm); document.removeEventListener('mouseup', mu); };
                resizer.addEventListener('mousedown', md);
            });
        });
    </script>
    @endpush
</x-base-layout>