<x-base-layout>
    <div class="app-container py-5" style="background-color: #f8f9fa;">
        
        {{-- Barra de Título Estilo Documento --}}
        <div class="d-flex align-items-center mb-4 px-3">
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
            
            {{-- Botones de Acción Superiores --}}
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-primary fw-bold px-4 rounded-1 shadow-sm">
                    <i class="fas fa-cloud-upload-alt me-1"></i> Subir Extracto
                </a>
                
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-sm btn-dark fw-bold px-4 rounded-1 shadow-sm">
                    <i class="fas fa-compress-arrows-alt me-1"></i> Mesa Conciliación
                </a>
            </div>
        </div>

        {{-- BARRA DE FILTROS BACKEND (ESTILO BARRA DE FÓRMULAS) --}}
        <form method="GET" action="{{ route('contabilidad.extractos.index') }}" class="bg-white p-3 border border-gray-300 mx-3 mb-3 d-flex flex-wrap gap-3 align-items-end shadow-sm" style="border-radius: 4px;">
            
            {{-- Filtro Obligatorio: Mes y Año --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Periodo (Año/Mes) *</label>
                <input type="month" name="periodo" value="{{ $periodo }}" class="form-control form-control-sm border-gray-300 fw-bold text-primary" required>
            </div>

            {{-- Filtro Opcional: Banco --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Cuenta / Banco</label>
                <select name="banco_id" class="form-select form-select-sm border-gray-300" style="min-width: 180px;">
                    <option value="">Todas las cuentas...</option>
                    @foreach($cuentas as $cuenta)
                        <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                            {{ $cuenta->banco }} - {{ substr($cuenta->numero_cuenta, -4) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Opcional: Distrito --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Distrito (Opcional)</label>
                <select name="distrito" class="form-select form-select-sm border-gray-300" style="min-width: 150px;">
                    <option value="">Todos...</option>
                    @foreach($distritos as $dist)
                        <option value="{{ $dist }}" {{ $distrito == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Opcional: Búsqueda Global --}}
            <div class="flex-grow-1">
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Búsqueda Exacta</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-gray-300" placeholder="Buscar cédula, referencia, nombre o monto...">
                </div>
            </div>

            {{-- Botones de Filtrado --}}
            <div>
                <button type="submit" class="btn btn-sm btn-success fw-bold px-4">
                    <i class="fas fa-filter me-1"></i> Filtrar Base
                </button>
                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-sm btn-light-danger btn-icon ms-1" title="Limpiar Filtros">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>

        {{-- Contenedor de la Hoja --}}
        <div class="bg-white shadow-sm border border-gray-300 mx-3 d-flex flex-column" style="border-radius: 0px; overflow: hidden; min-height: 500px;">
            
            {{-- Pestañas de la Hoja --}}
            <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                <div class="sheet-tab active">
                    <i class="fas fa-table me-2 fs-9"></i> Movimientos_{{ str_replace('-', '', $periodo) }}
                </div>
            </div>

            <div class="table-responsive flex-grow-1" id="resizable-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr>
                            <th class="col-index"></th>
                            <th>FECHA</th>
                            <th>CUENTA / BANCO</th>
                            <th>CÉDULA/NIT</th>
                            <th>NOMBRE TERCERO</th>
                            <th>DISTRITO</th>
                            <th style="width: 250px;">DESCRIPCIÓN BANCO</th>
                            <th>MONTO INGRESO</th>
                            <th>HASH TRANSACCIÓN</th>
                            <th>ESTADO</th>
                            <th style="text-align: center; width: 60px;">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        <tr>
                            <td class="col-index">{{ $extractos->firstItem() + $index }}</td>
                            
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

                            <td class="text-uppercase text-gray-600">
                                {{ $movimiento->referencia_distrito ?? '---' }}
                            </td>

                            <td class="text-gray-700" title="{{ $movimiento->descripcion_banco }}">
                                {{ $movimiento->descripcion_banco }}
                            </td>

                            <td class="text-end fw-bold text-success pe-4">
                                $ {{ number_format($movimiento->valor_ingreso, 2, ',', '.') }}
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
                            <td colspan="11" class="text-center py-10 text-muted fs-8 italic">
                                <i class="fas fa-search mb-3 fs-1 text-gray-400 d-block"></i>
                                No se encontraron registros para los filtros seleccionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación y Barra de Estado Inferior --}}
            <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2"></i> 
                    Consulta Exitosa
                </div>
                
                {{-- Renderizado de la paginación de Laravel --}}
                <div class="d-flex align-items-center">
                    {{ $extractos->links('pagination::bootstrap-4') }}
                </div>

                <div class="d-flex gap-4">
                    <span>Total en BD (este mes): <strong class="text-dark">{{ $extractos->total() }}</strong></span>
                    <span>Mostrando: <strong class="text-dark">{{ $extractos->firstItem() ?? 0 }} - {{ $extractos->lastItem() ?? 0 }}</strong></span>
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
        
        /* Ajuste Paginación */
        .pagination { margin-bottom: 0 !important; }
        .page-link { padding: 0.2rem 0.5rem; font-size: 0.8rem; }
    </style>

    <script>
        // Lógica de Redimensionamiento de Columnas
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
    </script>
    @endpush
</x-base-layout>