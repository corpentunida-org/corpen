<x-base-layout>
    <div class="app-container py-5" style="background-color: #f8f9fa;">
        
        {{-- Barra de Título Estilo Documento --}}
        <div class="d-flex align-items-center mb-5 px-3">
            <div class="symbol symbol-40px me-3">
                <div class="symbol-label bg-success text-white shadow-sm">
                    <i class="fas fa-file-excel text-white fs-4"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold m-0 text-dark fs-4">Extractos_Bancarios_Siasoft.gsheet</h3>
                <div class="d-flex align-items-center gap-3 fs-9 mt-1">
                    <span class="text-muted">Directorio: Contabilidad / Extractos</span>
                    <span class="badge badge-light-success text-success fw-bold px-2 py-1">DATOS PROTEGIDOS</span>
                </div>
            </div>
            
            {{-- Controles y Botones Superiores --}}
            <div class="ms-auto d-flex align-items-center gap-3">
                {{-- Buscador JS (Se mantiene tu lógica) --}}
                <div class="input-group input-group-sm border border-gray-300 rounded bg-white shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search fs-9 text-muted"></i></span>
                    <input type="text" id="tableSearch" class="form-control border-0 ps-0 fs-8 w-250px" placeholder="Buscar ref, tercero, descripción o monto...">
                </div>

                {{-- Botones de Acción que estaban en el Sidebar --}}
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-primary fw-bold px-4 rounded-1 shadow-sm">
                    <i class="fas fa-cloud-upload-alt me-1"></i> Subir Extracto
                </a>
                
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-sm btn-dark fw-bold px-4 rounded-1 shadow-sm">
                    <i class="fas fa-compress-arrows-alt me-1"></i> Conciliación
                </a>
            </div>
        </div>

        {{-- Contenedor de la Hoja --}}
        <div class="bg-white shadow-sm border border-gray-300 mx-3" style="border-radius: 0px; overflow: hidden;">
            
            {{-- Pestañas de la Hoja --}}
            <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                <div class="sheet-tab active">
                    <i class="fas fa-table me-2 fs-9"></i> Movimientos Generales
                </div>
            </div>

            <div class="table-responsive" id="resizable-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr>
                            <th class="col-index"></th>
                            <th>FECHA</th>
                            <th>CUENTA / BANCO</th>
                            <th>CÉDULA/NIT</th>
                            <th>NOMBRE TERCERO</th>
                            <th style="width: 250px;">DESCRIPCIÓN BANCO</th>
                            <th>MONTO INGRESO</th>
                            <th>HASH TRANSACCIÓN</th>
                            <th>ESTADO</th>
                            <th style="text-align: center; width: 60px;">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        <tr class="searchable-row">
                            <td class="col-index">{{ $index + 1 }}</td>
                            
                            <td class="text-center">
                                <span class="fw-bold text-dark">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</span>
                            </td>
                            
                            <td>
                                <span class="text-dark fw-bold d-block">{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}</span>
                                <span class="text-muted font-monospace fs-9">{{ $movimiento->cuentaBancaria->numero_cuenta ?? '' }}</span>
                            </td>
                            
                            <td class="font-monospace text-gray-700">
                                {{ $movimiento->referencia_cedula ?? '---' }}
                            </td>
                            
                            <td class="text-uppercase text-gray-800 fw-bold">
                                {{ $movimiento->referencia_nombre ?? '---' }}
                            </td>

                            <td class="text-gray-700" title="{{ $movimiento->descripcion_banco }}">
                                {{ $movimiento->descripcion_banco }}
                            </td>

                            <td class="text-end fw-bold text-success pe-4">
                                $ {{ number_format($movimiento->valor_ingreso, 0, ',', '.') }}
                            </td>

                            <td class="font-monospace fs-10 text-muted" style="max-width: 150px;">
                                {{ $movimiento->hash_transaccion }}
                            </td>

                            <td class="text-center p-0">
                                @if($movimiento->estado_conciliacion == 'Conciliado_Auto' || $movimiento->estado_conciliacion == 'Conciliado_Manual')
                                    <div class="gs-badge gs-success">CONCILIADO</div>
                                @elseif($movimiento->estado_conciliacion == 'Anulado')
                                    <div class="gs-badge gs-danger">ANULADO</div>
                                @else
                                    <div class="gs-badge gs-warning">PENDIENTE</div>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('contabilidad.extractos.show', $movimiento->id_transaccion) }}" class="text-primary text-decoration-none" title="Inspeccionar">
                                    <i class="fas fa-external-link-alt fs-9"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-10 text-muted fs-8 italic">No hay movimientos bancarios registrados. Sube un extracto para comenzar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Barra de Estado Estilo Excel (Reemplaza el resumen del sidebar viejo) --}}
            <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                <div>
                    <i class="fas fa-check-circle text-success me-1"></i> Listo
                </div>
                <div class="d-flex gap-4">
                    <span>Total Registros: <strong>{{ $extractos->count() }}</strong></span>
                    <span>Pendientes: <strong class="text-warning">{{ $extractos->where('estado_conciliacion', 'Pendiente')->count() }}</strong></span>
                    <span>Conciliados: <strong class="text-success">{{ $extractos->whereIn('estado_conciliacion', ['Conciliado_Auto', 'Conciliado_Manual'])->count() }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Roboto', 'Arial', sans-serif !important; 
        }

        /* TABLA ESTILO GOOGLE SHEETS */
        .table-gsheets {
            width: auto; 
            min-width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: #3c4043;
            table-layout: auto; 
        }

        .table-gsheets thead th {
            background-color: #f8f9fa;
            border: 1px solid #dadce0;
            padding: 8px 12px;
            font-weight: 500;
            color: #5f6368;
            text-align: left;
            position: relative; 
            white-space: nowrap;
            user-select: none;
        }

        /* Tirador para redimensionar */
        .resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            cursor: col-resize;
            user-select: none;
            height: 100%;
            z-index: 1;
        }
        .resizer:hover, .resizer.resizing { border-right: 2px solid #188038; }

        .table-gsheets tbody td {
            border: 1px solid #dadce0;
            padding: 4px 12px;
            height: 32px;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-gsheets tbody tr:hover { background-color: #e8f0fe !important; }

        .col-index {
            background-color: #f8f9fa;
            text-align: center !important;
            min-width: 40px;
            color: #5f6368;
            font-weight: bold;
            border-right: 2px solid #dadce0 !important;
        }

        .sheet-tab {
            padding: 8px 16px; font-size: 11px; font-weight: 500; color: #5f6368;
            text-decoration: none; border-right: 1px solid #dadce0; background-color: #f1f3f4;
        }
        .sheet-tab.active {
            background-color: #fff; color: #188038; border-bottom: 3px solid #188038;
        }

        .gs-badge {
            width: 100%; height: 31px; display: flex; align-items: center;
            justify-content: center; font-weight: 700; font-size: 9px;
        }
        .gs-success { background-color: #e6f4ea; color: #137333; }
        .gs-warning { background-color: #fef7e0; color: #b06000; }
        .gs-danger { background-color: #fce8e6; color: #c5221f; }
    </style>

    <script>
        // 1. Lógica de Redimensionamiento de Columnas
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('main-table');
            const cols = table.querySelectorAll('th');

            cols.forEach((col) => {
                const resizer = document.createElement('div');
                resizer.classList.add('resizer');
                col.appendChild(resizer);

                let x = 0;
                let w = 0;

                const mouseDownHandler = function (e) {
                    x = e.clientX;
                    const styles = window.getComputedStyle(col);
                    w = parseInt(styles.width, 10);

                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                    resizer.classList.add('resizing');
                };

                const mouseMoveHandler = function (e) {
                    const dx = e.clientX - x;
                    col.style.width = `${w + dx}px`;
                    table.style.tableLayout = 'fixed';
                };

                const mouseUpHandler = function () {
                    document.removeEventListener('mousemove', mouseMoveHandler);
                    document.removeEventListener('mouseup', mouseUpHandler);
                    resizer.classList.remove('resizing');
                };

                resizer.addEventListener('mousedown', mouseDownHandler);
            });
        });

        // 2. Buscador en tiempo real
        document.getElementById("tableSearch").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#extractosTable .searchable-row");

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
    @endpush
</x-base-layout>