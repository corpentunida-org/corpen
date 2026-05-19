<x-base-layout>
    {{-- 1. OVERLAY DE CARGA --}}
    <div id="loading-overlay" class="d-none" style="position: fixed; inset: 0; background: rgba(255, 255, 255, 0.85); z-index: 10000; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(4px); transition: opacity 0.3s ease;">
        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem; border-width: 0.15em;" role="status"></div>
        <h3 class="fw-bolder text-dark tracking-tight">Procesando...</h3>
    </div>

    <div class="app-container py-6" style="background-color: #fafbfc; min-height: 100vh;">
        
        {{-- 2. HEADER DE CONTROL --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-6 px-5">
            <div class="d-flex align-items-center gap-4">
                <div class="bg-white p-3 rounded-circle shadow-sm border border-gray-200 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-file-invoice-dollar text-primary fs-3"></i>
                </div>
                <div>
                    <h2 class="fw-bolder text-gray-900 mb-0 tracking-tight fs-2">Extractos Bancarios</h2>
                    <div class="text-muted fs-8 fw-semibold mt-1 d-flex align-items-center gap-2">
                        <span>Auditoría y Movimientos</span>
                        <span class="bullet bullet-dot bg-gray-400 h-4px w-4px"></span>
                        <span class="text-success fw-bold"><i class="fas fa-lock fs-10 me-1"></i> S3 Seguro</span>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3 mt-4 mt-sm-0">
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-light fw-bolder px-5 py-2 shadow-sm fs-8 text-gray-700 hover-elevate-up">
                    <i class="fas fa-cloud-upload-alt me-2 text-primary"></i> Importar Datos
                </a>
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-dark fw-bolder px-5 py-2 shadow-sm fs-8 hover-elevate-up">
                    <i class="fas fa-compress-arrows-alt me-2 text-white"></i> Mesa de Conciliación
                </a>
            </div>
        </div>

        {{-- 3. TOOLBAR DE FILTRADO --}}
        <div class="mx-5 mb-5">
            <form method="GET" action="{{ route('contabilidad.extractos.index') }}" id="form-master-filter" class="bg-white p-2 rounded-3 shadow-sm border border-gray-200 d-flex flex-wrap align-items-center gap-2">
                
                <div class="input-group input-group-sm w-auto flex-grow-1" style="min-width: 200px;">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid bg-transparent border-0 fs-8 fw-semibold" placeholder="Buscar Hash, Cédula o Valor...">
                </div>

                <div class="vr bg-gray-300 mx-2 d-none d-md-block" style="width: 1px; height: 25px;"></div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- SWITCH DE RASTREO GLOBAL RESTAURADO --}}
                    <div class="form-check form-switch form-check-custom form-check-solid form-check-sm" data-bs-toggle="tooltip" title="Ignorar el mes y buscar en toda la base">
                        <input class="form-check-input" type="checkbox" name="is_global" value="1" id="isGlobalCheck" {{ isset($is_global) && $is_global ? 'checked' : '' }} />
                        <label class="form-check-label text-gray-700 fs-9 fw-bold text-uppercase" for="isGlobalCheck">
                            Global
                        </label>
                    </div>

                    <input type="month" name="periodo" id="periodoInput" value="{{ $periodo }}" class="form-control form-control-sm form-control-solid bg-light border-0 fs-9 fw-bold text-muted" style="width: 130px; cursor: pointer;" {{ isset($is_global) && $is_global ? 'disabled' : '' }}>
                    
                    <select name="estado" class="form-select form-select-sm form-select-solid bg-light border-0 fs-9 fw-bold text-muted" style="width: 140px; cursor: pointer;">
                        <option value="">Cualquier Estado</option>
                        <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="Conciliados" {{ $estado == 'Conciliados' ? 'selected' : '' }}>Conciliados</option>
                    </select>

                    <select name="banco_id" class="form-select form-select-sm form-select-solid bg-light border-0 fs-9 fw-bold text-muted" style="width: 160px; cursor: pointer;">
                        <option value="">Todas las Cuentas</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->banco }} (...{{ substr($cuenta->numero_cuenta, -4) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-1 ms-auto">
                    <button type="submit" class="btn btn-sm btn-primary fw-bolder px-4 fs-9">Filtrar</button>
                    @if($search || $estado || $banco_id || (isset($is_global) && $is_global))
                        <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Limpiar Filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 4. GRID DE DATOS --}}
        <div class="bg-white shadow-sm border border-gray-200 mx-5 d-flex flex-column" style="border-radius: 8px; overflow: hidden; min-height: 65vh;">
            
            <div class="d-flex justify-content-between align-items-center px-4 py-2 bg-white border-bottom border-gray-200">
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bolder fs-9 text-gray-500 tracking-widest text-uppercase">
                        Registros Encontrados <span class="badge badge-circle badge-light-primary ms-1 fs-10 w-20px h-20px">{{ $extractos->total() }}</span>
                    </div>
                    <div id="js-filter-active" class="d-none animate__animated animate__fadeIn">
                        <span class="badge badge-light-warning fs-10 fw-bold"><i class="fas fa-filter fs-10 me-1 text-warning"></i> Búsqueda local activa</span>
                    </div>
                </div>
                <div class="fs-10 text-muted font-monospace">Página {{ $extractos->currentPage() }} de {{ $extractos->lastPage() }}</div>
            </div>

            <div class="table-responsive flex-grow-1 custom-scrollbar" id="table-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr class="header-titles">
                            <th class="col-index">#</th>
                            <th style="width: 90px;">Fecha</th>
                            <th style="width: 150px;">Entidad Bancaria</th>
                            <th style="width: 120px;">Ref. Cliente</th>
                            <th style="width: 130px;">Oficina / Origen</th>
                            <th style="width: 180px;">Línea Afectada</th>
                            <th style="width: 100px;">Tipología</th>
                            <th style="width: 120px;" class="text-end">Monto ($)</th>
                            <th style="width: 100px;" class="text-center">Estado</th>
                            <th style="width: 60px;" class="text-center">Soporte</th>
                        </tr>
                        <tr class="filter-row">
                            <td class="col-index bg-light"></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter" placeholder="Filtrar..."></td>
                            <td><input type="text" class="column-filter text-end" placeholder="Filtrar..."></td>
                            <td colspan="2" class="bg-light"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        @php
                            $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion'])
                                ->whereJsonContains('id_transaccion_bancaria', $movimiento->id_transaccion)
                                ->orWhereJsonContains('id_transaccion_bancaria', (string) $movimiento->id_transaccion)
                                ->first();
                            
                            $isConciliado = str_contains($movimiento->estado_conciliacion, 'Conciliado');
                            $montoFormat = number_format($movimiento->valor_ingreso, 0, ',', '.');
                            $fechaFormat = $movimiento->fecha_movimiento->format('d/m/Y');
                        @endphp

                        <tr class="data-row cursor-pointer" onclick="toggleDetails(this, {{ $movimiento->id_transaccion }})">
                            <td class="col-index text-muted">{{ $extractos->firstItem() + $index }}</td>
                            <td class="font-monospace fs-9 text-gray-700">{{ $fechaFormat }}</td>
                            <td>
                                <div class="fw-bold text-gray-800 fs-9 text-truncate" title="{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}">
                                    {{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}
                                </div>
                                <div class="text-muted fs-11 font-monospace">CTA: *{{ substr($movimiento->cuentaBancaria->numero_cuenta ?? '0000', -4) }}</div>
                            </td>
                            <td class="font-monospace fw-semibold text-primary fs-9">{{ $movimiento->referencia_cedula ?? '---' }}</td>
                            <td class="text-gray-600 fs-9 text-truncate" title="{{ $movimiento->referencia_oficina }}">{{ $movimiento->referencia_oficina ?? '---' }}</td>
                            <td class="fs-9 text-truncate text-gray-700" title="{{ $comprobante?->obligacion?->nombre }}">
                                {{ $comprobante?->obligacion?->nombre ?? '---' }}
                            </td>
                            <td class="text-muted fs-10 text-uppercase fw-semibold">{{ $comprobante?->tipo_pago ?? 'EXTRACTO' }}</td>
                            <td class="text-end fw-bolder text-gray-900 fs-9">{{ $montoFormat }}</td>
                            <td class="text-center">
                                <span class="badge {{ $isConciliado ? 'badge-light-success' : 'badge-light-warning' }} fs-10 fw-bolder px-2 py-1">
                                    {{ $isConciliado ? 'CONCILIADO' : 'PENDIENTE' }}
                                </span>
                            </td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @if($comprobante && $comprobante->url_archivo !== '#')
                                    <button type="button" 
                                            onclick="abrirModalSoporte('{{ $comprobante->url_archivo }}', 'Soporte del Tercero: {{ $comprobante->cod_ter_MaeTerceros ?? 'N/A' }}')" 
                                            class="btn btn-icon btn-sm btn-light-primary hover-elevate-up shadow-sm" 
                                            data-bs-toggle="tooltip" 
                                            title="Visualizar Soporte">
                                        <i class="fas fa-file-invoice fs-5"></i>
                                    </button>
                                @else
                                    <span class="text-gray-300 fs-8 fw-bold" data-bs-toggle="tooltip" title="No hay soporte vinculado">-</span>
                                @endif
                            </td>
                        </tr>
                        
                        {{-- FILA OCULTA CON LOS DETALLES --}}
                        <tr class="detail-row d-none" id="detail-{{ $movimiento->id_transaccion }}">
                            <td colspan="10" class="p-0 border-0">
                                <div class="bg-light-primary border-bottom border-primary border-opacity-25 px-6 py-4 shadow-inner">
                                    <div class="row g-4">
                                        <div class="col-md-3">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">Hash de Transacción</div>
                                            <div class="d-flex align-items-center">
                                                <code class="fs-10 text-dark bg-white px-2 py-1 rounded border">{{ $movimiento->hash_transaccion }}</code>
                                                <button class="btn btn-sm btn-icon btn-active-color-primary ms-1 h-20px w-20px" onclick="copyToClipboard('{{ $movimiento->hash_transaccion }}')" title="Copiar Hash"><i class="fas fa-copy fs-10"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">Tercero Validado</div>
                                            <div class="fs-9 fw-bold text-gray-800">{{ $comprobante->cod_ter_MaeTerceros ?? 'Sin procesar' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">PR / CCO</div>
                                            <div class="fs-9 fw-bold text-gray-800">{{ $comprobante->pr ?? '0' }} / {{ $comprobante->cco ?? '0' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">Cuota N°</div>
                                            <div class="fs-9 fw-bold text-gray-800">{{ $comprobante->numero_cuota ?? '---' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">Usuario Gestor</div>
                                            <div class="fs-9 fw-bold text-gray-800">
                                                <i class="fas fa-user-circle text-muted me-1"></i> {{ $comprobante->user->name ?? 'Sistema' }}
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="fs-11 text-uppercase text-muted fw-bold mb-1">Observaciones</div>
                                            <div class="fs-9 text-gray-700 fst-italic">{{ $comprobante->observacion ?? 'Sin observaciones registradas.' }}</div>
                                        </div>
                                        <div class="col-12 mt-2 d-flex justify-content-end">
                                            <a href="{{ route('contabilidad.extractos.show', $movimiento->id_transaccion) }}" class="btn btn-sm btn-primary fs-10 fw-bolder px-4 py-2">
                                                Abrir Ficha Completa <i class="fas fa-arrow-right ms-1 fs-10"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-15">
                                <i class="fas fa-search-minus fs-1 text-gray-300 mb-3 d-block"></i>
                                <span class="text-muted fw-bold fs-7">No se encontraron movimientos.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. PAGINACIÓN --}}
            <div class="border-top border-gray-200 px-5 py-3 bg-white d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted fs-9 fw-semibold">
                    Mostrando <span class="text-gray-900 fw-bolder">{{ $extractos->firstItem() ?? 0 }}</span> a <span class="text-gray-900 fw-bolder">{{ $extractos->lastItem() ?? 0 }}</span> de <span class="text-gray-900 fw-bolder">{{ $extractos->total() }}</span> registros
                </div>
                <div class="custom-pagination">
                    {{ $extractos->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        {{-- MODAL VISOR DE SOPORTES CORREGIDO --}}
        <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="modal-header bg-light-primary border-bottom border-primary border-opacity-10 py-3">
                        <h5 class="modal-title fw-bolder text-gray-800 d-flex align-items-center gap-2">
                            <i class="fas fa-file-pdf text-primary fs-3"></i> 
                            <span id="visorSoporteTitulo">Soporte de Pago</span>
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" id="btnDescargarSoporte" target="_blank" class="btn btn-sm btn-icon btn-active-light-primary text-gray-600" data-bs-toggle="tooltip" title="Abrir en pestaña nueva">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0 bg-dark" style="height: 75vh;">
                        {{-- Iframe directo, sin bloqueos JS cruzados --}}
                        <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0 bg-white"></iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <style>
        :root {
            --gs-border: #e2e8f0;
            --gs-header-bg: #f8fafc;
            --gs-hover-bg: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .table-gsheets { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12px; table-layout: fixed; }
        .table-gsheets thead { position: sticky; top: 0; z-index: 20; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .table-gsheets thead th { background: var(--gs-header-bg); border-bottom: 1px solid var(--gs-border); border-right: 1px solid var(--gs-border); padding: 10px 12px; font-weight: 700; color: #475569; text-transform: capitalize; font-size: 11px; }
        .table-gsheets tbody td { border-bottom: 1px solid var(--gs-border); border-right: 1px solid var(--gs-border); padding: 8px 12px; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease; }
        .table-gsheets tbody tr.data-row:hover td { background-color: var(--gs-hover-bg); }
        .col-index { width: 40px; text-align: center !important; background: var(--gs-header-bg); color: #94a3b8; font-weight: 600; font-size: 10px; }
        .filter-row td { padding: 4px 6px !important; background: var(--gs-header-bg); }
        .column-filter { width: 100%; border: 1px solid transparent; background: transparent; border-radius: 4px; font-size: 10px; padding: 4px 8px; color: #475569; transition: all 0.2s; }
        .column-filter:focus, .column-filter:hover { background: white; border-color: #cbd5e1; outline: none; }
        .shadow-inner { box-shadow: inset 0 3px 6px -4px rgba(0,0,0,0.1); }
        .custom-pagination .pagination { margin-bottom: 0; gap: 4px; }
        .custom-pagination .page-link { border: none; color: #64748b; background: transparent; border-radius: 6px; font-size: 11px; font-weight: 600; padding: 6px 12px; }
        .custom-pagination .page-link:hover { background: #f1f5f9; color: #0f172a; }
        .custom-pagination .page-item.active .page-link { background: #eff6ff; color: #2563eb; font-weight: 700; }
        .custom-pagination .page-item.disabled .page-link { color: #cbd5e1; }
    </style>

    <script>
        let modalVisor;

        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar el modal de Bootstrap
            const modalEl = document.getElementById('modalVisorSoporte');
            if (modalEl) {
                modalVisor = new bootstrap.Modal(modalEl);
                modalEl.addEventListener('hidden.bs.modal', function () {
                    // Limpiar el src para que no quede el PDF anterior en memoria
                    document.getElementById('visorSoporteFrame').src = '';
                });
            }

            // Activar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Lógica para que el checkbox "Global" deshabilite el campo del mes automáticamente
            const isGlobalCheck = document.getElementById('isGlobalCheck');
            const periodoInput = document.getElementById('periodoInput');
            
            if(isGlobalCheck && periodoInput) {
                isGlobalCheck.addEventListener('change', function() {
                    periodoInput.disabled = this.checked;
                    if(this.checked) {
                        periodoInput.value = ''; // Limpiar el mes si se activa global
                    }
                });
            }

            // Filtrado Local
            const rows = document.querySelectorAll('#main-table tbody tr.data-row');
            const filters = document.querySelectorAll('.column-filter');
            const filterIndicator = document.getElementById('js-filter-active');

            filters.forEach((filter, index) => {
                filter.addEventListener('input', () => {
                    let active = false;

                    rows.forEach(row => {
                        let isMatch = true;
                        
                        filters.forEach((f, i) => {
                            let val = f.value.toLowerCase().trim();
                            if(val) {
                                active = true;
                                let cellText = row.cells[i + 1].innerText.toLowerCase();
                                if (!cellText.includes(val)) {
                                    isMatch = false;
                                }
                            }
                        });

                        const detailRow = row.nextElementSibling;
                        
                        if (isMatch) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                            if (detailRow && detailRow.classList.contains('detail-row')) {
                                detailRow.classList.add('d-none');
                            }
                        }
                    });

                    if (active) {
                        filterIndicator.classList.remove('d-none');
                    } else {
                        filterIndicator.classList.add('d-none');
                    }
                });
            });

            // Enviar formulario al cambiar Selects
            const masterForm = document.getElementById('form-master-filter');
            const masterSelects = masterForm.querySelectorAll('select');
            masterSelects.forEach(select => {
                select.addEventListener('change', () => {
                    document.getElementById('loading-overlay').classList.remove('d-none');
                    document.getElementById('loading-overlay').style.display = 'flex';
                    masterForm.submit();
                });
            });
        });

        // Abrir Modal de Soporte Corregido (Sin bloqueos de Onload)
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') {
                alert('El enlace al documento no es válido o ha expirado.');
                return;
            }
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            
            // Asignar la URL directamente al iframe renderiza el archivo nativamente
            document.getElementById('visorSoporteFrame').src = url;
            modalVisor.show();
        };

        // Expandir detalles
        function toggleDetails(rowElement, id) {
            const detailRow = document.getElementById('detail-' + id);
            const isHidden = detailRow.classList.contains('d-none');
            
            if (isHidden) {
                detailRow.classList.remove('d-none');
                detailRow.classList.add('animate__animated', 'animate__fadeIn', 'animate__faster');
                rowElement.style.backgroundColor = '#f8fafc';
            } else {
                detailRow.classList.add('d-none');
                rowElement.style.backgroundColor = '';
            }
        }

        // Copiar Hash
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') {
                    toastr.success('Hash copiado al portapapeles');
                } else {
                    alert('Copiado: ' + text);
                }
            });
        }
    </script>
    @endpush
</x-base-layout>