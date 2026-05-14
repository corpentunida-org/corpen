<x-base-layout>
    {{-- 1. MODAL DE CARGA REFORZADO --}}
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.98); z-index: 10000; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(8px); transition: all 0.5s ease;">
        <div class="spinner-border text-primary mb-4" style="width: 5rem; height: 5rem; border-width: 0.25em;" role="status"></div>
        <h2 class="fw-bolder text-dark fs-1 mb-2">Procesando Inteligencia de Datos...</h2>
        <p class="text-muted fs-5 mb-5 fw-bold">Sincronizando con el servidor de seguridad Siasoft</p>
        
        <div class="bg-white border border-primary border-dashed rounded-3 px-8 py-6 text-center shadow-lg">
            <span class="fs-4 fw-bold text-gray-600 d-block mb-2 text-uppercase ls-1">Estado del Canal</span>
            <div class="display-4 fw-bolder text-primary">
                <span id="countdown-timer">READY</span>
            </div>
            <div class="progress h-6px w-200px mt-4 bg-light-primary">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="app-container py-5" style="background-color: #f1f4f9;">
        
        {{-- 2. HEADER DE CONTROL EMPRESARIAL --}}
        <div class="d-flex align-items-center mb-8 px-4">
            <div class="symbol symbol-50px me-5">
                <div class="symbol-label bg-dark text-white shadow-lg">
                    <i class="fas fa-shield-alt text-success fs-1"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h3 class="fw-bolder m-0 text-gray-900 fs-2 ls-n1">Consola de Extractos Bancarios <span class="text-primary fw-normal ms-2">v4.0 Alpha</span></h3>
                <div class="d-flex align-items-center gap-4 fs-9 mt-1">
                    <span class="text-muted fw-bold text-uppercase"><i class="fas fa-folder-open me-1"></i> Contabilidad / Master / Movimientos</span>
                    <span class="badge badge-success fw-bolder px-3 py-1 shadow-sm"><i class="fas fa-lock me-1 text-white fs-10"></i> CIFRADO AES-256</span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-lg-flex flex-column text-end me-3">
                    <span class="text-muted fs-10 fw-bold text-uppercase">Último Acceso</span>
                    <span class="text-dark fs-9 fw-bolder">{{ date('d M Y, h:i A') }}</span>
                </div>
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-primary fw-bolder px-6 shadow-sm rounded-1 h-40px d-flex align-items-center">
                    <i class="fas fa-cloud-upload-alt me-2"></i> IMPORTAR DATA
                </a>
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-sm btn-dark fw-bolder px-6 shadow-sm rounded-1 h-40px d-flex align-items-center">
                    <i class="fas fa-project-diagram me-2"></i> MESA DE CONTROL
                </a>
            </div>
        </div>

        {{-- 3. PANEL DE FILTRADO MAESTRO --}}
        <form method="GET" action="{{ route('contabilidad.extractos.index') }}" id="form-master-filter" class="bg-white p-6 mx-4 mb-6 shadow-sm border border-gray-200" style="border-radius: 12px;">
            <div class="row g-5 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fs-8 fw-bolder text-gray-700 text-uppercase mb-3">Ciclo Contable</label>
                    <input type="month" name="periodo" id="inputPeriodo" value="{{ $periodo }}" class="form-control form-control-solid fw-bold border-gray-300 h-45px">
                </div>

                <div class="col-md-2">
                    <label class="form-label fs-8 fw-bolder text-gray-700 text-uppercase mb-3">Estado Operativo</label>
                    <select name="estado" class="form-select form-select-solid fw-bold h-45px">
                        <option value="">TODOS LOS REGISTROS</option>
                        <option value="Pendiente" {{ (isset($estado) && $estado == 'Pendiente') ? 'selected' : '' }}>PENDIENTES</option>
                        <option value="Conciliados" {{ (isset($estado) && $estado == 'Conciliados') ? 'selected' : '' }}>CONCILIADOS</option>
                        <option value="Anulado" {{ (isset($estado) && $estado == 'Anulado') ? 'selected' : '' }}>ANULADOS</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fs-8 fw-bolder text-gray-700 text-uppercase mb-3">Fuente de Origen (Cuenta)</label>
                    <select name="banco_id" class="form-select form-select-solid h-45px">
                        <option value="">CONSOLIDADO GLOBAL</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->banco }} - ****{{ substr($cuenta->numero_cuenta, -4) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fs-8 fw-bolder text-gray-700 text-uppercase mb-3">Parámetro de Auditoría (Cédula, Valor, Hash)</label>
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid border-gray-300 h-45px" placeholder="Búsqueda técnica de alta precisión...">
                        <button type="submit" class="btn btn-secondary fw-bolder px-8 h-45px text-uppercase">EJECUTAR</button>
                        <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-light-danger btn-icon h-45px w-45px border-gray-300" title="Reiniciar">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top border-gray-100 d-flex justify-content-between align-items-center">
                <div class="form-check form-switch d-flex align-items-center gap-3 m-0 p-0">
                    <input class="form-check-input m-0 cursor-pointer w-45px h-22px" type="checkbox" role="switch" id="switchGlobal" name="is_global" value="1" {{ isset($is_global) && $is_global ? 'checked' : '' }}>
                    <label class="form-check-label fs-8 fw-bolder text-gray-800 cursor-pointer ms-2 text-uppercase" for="switchGlobal">
                        MODO AUDITORÍA HISTÓRICA <span class="text-muted fw-normal ms-2 italic">(Omite filtros temporales para rastreo profundo)</span>
                    </label>
                </div>
                <div id="filter-summary" class="fs-10 fw-bold text-muted text-uppercase">
                    Filtros activos: <span class="text-primary" id="active-filters-count">0</span>
                </div>
            </div>
        </form>

        {{-- 4. CONTENEDOR TIPO HOJA DE CÁLCULO PROFESIONAL --}}
        <div class="bg-white shadow-lg border border-gray-300 mx-4 d-flex flex-column" style="border-radius: 4px; overflow: hidden; min-height: 650px;">
            
            <div class="d-flex bg-gray-200 border-bottom border-gray-300">
                <div class="sheet-tab active px-8 py-3">
                    <i class="fas fa-table me-2 fs-10"></i> RAW_DATA_STREAM
                </div>
                <div class="ms-auto d-flex align-items-center px-6">
                    <span class="text-dark fs-10 fw-bolder letter-spacing-1 text-uppercase">
                        <span class="bullet bullet-dot bg-success h-8px w-8px me-2"></span>Sincronización en Tiempo Real Activa
                    </span>
                </div>
            </div>

            <div class="table-responsive flex-grow-1" id="table-container" style="max-height: 70vh; overflow-y: auto;">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr class="header-titles">
                            <th class="col-index">#</th>
                            <th style="width: 140px;">TIMESTAMP</th>
                            <th style="width: 220px;">ENTIDAD / CUENTA</th>
                            <th style="width: 140px;">IDENTIFICACIÓN</th>
                            <th style="width: 160px;">REF. OFICINA</th>
                            <th style="width: 140px;">PROCESO</th>
                            <th>OBSERVACIONES TÉCNICAS</th>
                            <th style="width: 160px;" class="text-end pe-8">IMPORTE</th>
                            <th style="width: 140px;" class="text-center">ESTADO</th>
                            <th style="width: 60px;" class="text-center">SOP</th>
                            <th style="width: 50px;" class="text-center">INS</th>
                        </tr>
                        <tr class="filter-row">
                            <td class="col-index bg-gray-300"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td><input type="text" class="column-filter text-end pe-5" onkeydown="if(event.keyCode==13) return false;"></td>
                            <td colspan="3" class="bg-gray-100"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        @php
                            $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion'])
                                ->whereJsonContains('id_transaccion_bancaria', $movimiento->id_transaccion)
                                ->orWhereJsonContains('id_transaccion_bancaria', (string) $movimiento->id_transaccion)
                                ->first();
                            
                            $popoverContent = "
                                <div class='p-3' style='min-width: 350px;'>
                                    <div class='mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center'>
                                        <strong class='text-primary text-uppercase fs-7'>Auditoría de Registro</strong>
                                        <span class='badge badge-light-dark fs-10'>ID: {$movimiento->id_transaccion}</span>
                                    </div>
                                    <div class='mb-2'><strong>FECHA Y HORA:</strong> <span class='text-dark fw-bolder'>{$movimiento->fecha_movimiento->format('d/m/Y H:i:s')}</span></div>
                                    <div class='mb-2'><strong>FUENTE:</strong> {$movimiento->cuentaBancaria->banco}</div>
                                    <div class='mb-2'><strong>CUENTA:</strong> <span class='font-monospace'>{$movimiento->cuentaBancaria->numero_cuenta}</span></div>
                                    <div class='mb-2'><strong>VALOR:</strong> <span class='text-success fw-bold'>$ " . number_format($movimiento->valor_ingreso, 2) . "</span></div>
                                    <div class='mt-3 p-2 bg-light rounded'>
                                        <div class='text-muted fs-10 mb-1 fw-bold text-uppercase'>Hash de Seguridad (Click para copiar)</div>
                                        <code class='text-dark fs-10 cursor-pointer copy-hash' title='Click para copiar'>{$movimiento->hash_transaccion}</code>
                                    </div>";
                            
                            if($comprobante) {
                                $popoverContent .= "
                                    <div class='mt-4 border-top pt-3'><strong class='text-danger text-uppercase fs-8'>Vinculación Cartera</strong></div>
                                    <div class='mt-2 mb-1'><strong>Analista:</strong> " . ($comprobante->user->name ?? 'SYSTEM') . "</div>
                                    <div class='mb-1'><strong>Línea:</strong> " . ($comprobante->obligacion->nombre ?? 'N/A') . "</div>
                                    <div class='mb-0'><strong>Obs:</strong> <span class='italic text-gray-700'>{$comprobante->observacion}</span></div>";
                            }
                            $popoverContent .= "</div>";
                        @endphp

                        <tr class="data-row" 
                            data-bs-toggle="popover" 
                            data-bs-trigger="hover" 
                            data-bs-placement="right" 
                            data-bs-html="true" 
                            data-bs-boundary="viewport"
                            data-bs-offset="[0,10]"
                            data-bs-content="{{ $popoverContent }}">
                            
                            <td class="col-index">{{ $extractos->firstItem() + $index }}</td>
                            <td class="text-center fw-bolder text-dark font-monospace">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bolder text-gray-800">{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}</span>
                                    <span class="text-muted fs-10 font-monospace ls-n1">****{{ substr($movimiento->cuentaBancaria->numero_cuenta, -4) }}</span>
                                </div>
                            </td>
                            <td class="font-monospace text-gray-700 fw-bold">{{ $movimiento->referencia_cedula ?? '---' }}</td>
                            <td class="text-uppercase text-gray-700 fw-bold">{{ $movimiento->referencia_oficina ?? '---' }}</td>
                            <td class="text-uppercase text-muted fs-10 fw-bolder">{{ $comprobante?->tipo_pago ?? 'EXTRACTO' }}</td>
                            <td class="text-truncate text-gray-600" style="max-width: 250px;">{{ $comprobante?->observacion ?? 'Sin descripción vinculada' }}</td>
                            <td class="text-end fw-bolder text-success pe-8 fs-7">$ {{ number_format($movimiento->valor_ingreso, 2, ',', '.') }}</td>
                            <td class="text-center">
                                @if($movimiento->estado_conciliacion == 'Conciliado_Auto' || $movimiento->estado_conciliacion == 'Conciliado_Manual')
                                    <span class="gs-status status-success">CONCILIADO</span>
                                @elseif($movimiento->estado_conciliacion == 'Anulado')
                                    <span class="gs-status status-danger">ANULADO</span>
                                @else
                                    <span class="gs-status status-warning">PENDIENTE</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($comprobante && $comprobante->url_archivo != '#')
                                    <a href="{{ $comprobante->url_archivo }}" target="_blank" class="text-primary hover-scale" title="Ver Recibo S3">
                                        <i class="fas fa-file-pdf fs-4"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('contabilidad.extractos.show', $movimiento->id_transaccion) }}" class="text-gray-400 hover-primary">
                                    <i class="fas fa-external-link-alt fs-10"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-20 bg-light-danger border-dashed border-danger">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-database fs-3x mb-5 text-danger opacity-25"></i>
                                    <span class="fw-bolder fs-4 text-gray-800">CERO REGISTROS DETECTADOS</span>
                                    <p class="text-muted fs-7 mt-2">No existen coincidencias para los parámetros actuales de auditoría.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. FOOTER CON ESTADÍSTICAS EN TIEMPO REAL --}}
            <div class="bg-dark border-top border-gray-300 p-4 d-flex justify-content-between align-items-center text-white">
                <div class="d-flex align-items-center gap-6 fs-10 fw-bold">
                    <span><i class="fas fa-server me-2 text-success"></i> ENGINE: <strong class="text-white">SIASOFT-X64</strong></span>
                    <span>|</span>
                    <span>VISIBLES: <strong id="visible-count" class="text-success">{{ $extractos->count() }}</strong> / {{ $extractos->total() }}</span>
                    <span>|</span>
                    <span id="js-filter-active" style="display:none" class="text-warning text-uppercase ls-1"><i class="fas fa-filter me-1"></i> Filtro Activo</span>
                </div>
                
                <div class="d-flex align-items-center pagination-dark">
                    {{ $extractos->links('pagination::bootstrap-4') }}
                </div>

                <div class="text-end fs-10 fw-bolder text-uppercase text-gray-500">
                    Sincronización: <span class="text-white">{{ date('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        /* CORE REFINADO - ESTILO EXCEL ENTERPRISE */
        .table-gsheets { width: auto; min-width: 100%; border-collapse: collapse; font-size: 11px; color: #2d3436; table-layout: fixed; }
        .table-gsheets thead th { background-color: #f8f9fa; border: 1px solid #dadce0; padding: 12px 14px; font-weight: 800; color: #444; position: sticky; top: 0; z-index: 100; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-gsheets tbody td { border: 1px solid #dadce0; padding: 8px 14px; height: 42px; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: background 0.15s ease; }
        .table-gsheets tbody tr:hover { background-color: #e8f0fe !important; outline: 1px solid #1a73e8; z-index: 50; }
        
        .col-index { background-color: #f1f3f4; text-align: center !important; min-width: 50px; color: #5f6368; font-weight: 900; border-right: 2px solid #dadce0 !important; }
        .sheet-tab { cursor: default; border-right: 1px solid #dadce0; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #636e72; background-color: #dfe6e9; }
        .sheet-tab.active { background-color: #fff; color: #1e8449; border-top: 4px solid #1e8449; }

        /* STATUS BADGES DE ALTA VISIBILIDAD */
        .gs-status { padding: 5px 12px; border-radius: 4px; font-weight: 900; font-size: 8.5px; display: inline-block; min-width: 95px; text-align: center; box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1); }
        .status-success { background-color: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .status-warning { background-color: #feebc8; color: #744210; border: 1px solid #fbd38d; }
        .status-danger { background-color: #fed7d7; color: #822727; border: 1px solid #feb2b2; }

        /* FILTROS INTEGRADOS */
        .filter-row td { padding: 5px 8px !important; background-color: #f1f3f4 !important; }
        .column-filter { width: 100%; height: 28px !important; font-size: 10.5px !important; border-radius: 4px !important; border: 1px solid #cbd5e0 !important; text-align: left; padding: 0 10px; font-weight: 600; color: #2d3748; }
        .column-filter:focus { border-color: #4299e1 !important; box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15) !important; outline: none; background: #fff; }

        /* POPOVER FIX & DESIGN (IMPORTANTE) */
        .popover { pointer-events: none !important; border: 0 !important; box-shadow: 0 15px 50px rgba(0,0,0,0.2) !important; border-radius: 8px !important; z-index: 1060 !important; }
        .popover .copy-hash { pointer-events: auto !important; }
        .popover-header { display: none; }
        
        /* UTILIDADES */
        .italic { font-style: italic; }
        .ls-1 { letter-spacing: 1px; }
        .resizer { position: absolute; top: 0; right: 0; width: 6px; cursor: col-resize; height: 100%; z-index: 101; }
        .resizer:hover { border-right: 3px solid #188038; }
        .pagination-dark .page-link { background: #2d3436; border-color: #444; color: #fff; font-size: 10px; }
        .pagination-dark .page-item.active .page-link { background: #1e8449; border-color: #1e8449; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. GESTOR DE POPOVERS (FAIL-SAFE)
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            const popoverInstances = popoverTriggerList.map(function (el) {
                return new bootstrap.Popover(el, {
                    container: 'body',
                    delay: { "show": 150, "hide": 100 },
                    offset: [0, 8]
                });
            });

            // Función crítica: Ocultar todos los popovers antes de cualquier cambio visual
            function forceHidePopovers() {
                popoverInstances.forEach(p => p.hide());
            }

            // 2. FILTRADO DINÁMICO REFORZADO (JS)
            const rows = document.querySelectorAll('#main-table tbody tr.data-row');
            const filters = document.querySelectorAll('.column-filter');
            const countLabel = document.getElementById('visible-count');
            const filterIndicator = document.getElementById('js-filter-active');

            filters.forEach((filter) => {
                filter.addEventListener('keyup', () => {
                    forceHidePopovers(); // Cerramos popovers para que no se queden flotando
                    
                    let visibleCount = 0;
                    let anyFilterActive = false;

                    rows.forEach(row => {
                        let isMatch = true;
                        filters.forEach((f, i) => {
                            let val = f.value.toLowerCase();
                            if(val) anyFilterActive = true;
                            let cellText = row.cells[i + 1].innerText.toLowerCase();
                            if (val && !cellText.includes(val)) isMatch = false;
                        });
                        row.style.display = isMatch ? '' : 'none';
                        if(isMatch) visibleCount++;
                    });

                    countLabel.innerText = visibleCount;
                    filterIndicator.style.display = anyFilterActive ? 'inline-block' : 'none';
                });
            });

            // 3. SISTEMA DE PORTAPAPELES (HASH)
            document.addEventListener('click', function(e) {
                if(e.target && e.target.classList.contains('copy-hash')) {
                    const text = e.target.innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = e.target.innerText;
                        e.target.innerText = "¡COPIADO!";
                        e.target.classList.add('text-success');
                        setTimeout(() => {
                            e.target.innerText = originalText;
                            e.target.classList.remove('text-success');
                        }, 1000);
                    });
                }
            });

            // 4. RESIZER DE COLUMNAS (ALTA PRECISIÓN)
            const table = document.getElementById('main-table');
            const cols = table.querySelectorAll('th');
            cols.forEach((col) => {
                const resizer = document.createElement('div');
                resizer.classList.add('resizer');
                col.appendChild(resizer);
                let x = 0, w = 0;
                
                const mouseDownHandler = (e) => {
                    forceHidePopovers();
                    x = e.clientX;
                    w = parseInt(window.getComputedStyle(col).width, 10);
                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                    col.classList.add('bg-light-primary');
                };

                const mouseMoveHandler = (e) => {
                    const dx = e.clientX - x;
                    col.style.width = `${w + dx}px`;
                    table.style.tableLayout = 'fixed';
                };

                const mouseUpHandler = () => {
                    document.removeEventListener('mousemove', mouseMoveHandler);
                    document.removeEventListener('mouseup', mouseUpHandler);
                    col.classList.remove('bg-light-primary');
                };

                resizer.addEventListener('mousedown', mouseDownHandler);
            });

            // 5. AUTO-HIDE DEL OVERLAY (PREVENCIÓN DE BLOQUEO)
            // Si después de 10 segundos el overlay sigue, se quita (fail-safe)
            setTimeout(() => {
                const overlay = document.getElementById('loading-overlay');
                if(overlay && overlay.style.display !== 'none') {
                    // overlay.style.display = 'none'; // Desactivado por defecto, habilitar si hay problemas de carga
                }
            }, 10000);

            // Escuchar el envío del formulario para mostrar el loading
            document.getElementById('form-master-filter').addEventListener('submit', () => {
                document.getElementById('loading-overlay').style.display = 'flex';
            });
        });
    </script>
    @endpush
</x-base-layout>