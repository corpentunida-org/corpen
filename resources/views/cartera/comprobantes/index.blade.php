<x-base-layout>
    <div class="app-container py-5" style="background-color: #f8f9fa;">
        
        {{-- Barra de Título Estilo Documento --}}
        <div class="d-flex align-items-center mb-4 px-3">
            <div class="symbol symbol-40px me-3">
                <div class="symbol-label bg-success text-white shadow-sm">
                    <i class="fas fa-file-invoice text-white fs-4"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold m-0 text-dark fs-4">Soportes_Pago_Cartera_{{ str_replace('-', '', $periodo) }}.gsheet</h3>
                <div class="d-flex align-items-center gap-3 fs-9 mt-1">
                    <span class="text-muted">Archivo guardado en Drive</span>
                    <span class="badge badge-light-success text-success fw-bold px-2 py-1">SOLO LECTURA</span>
                </div>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('cartera.comprobantes.create') }}" class="btn btn-sm btn-primary fw-bold px-4 rounded-1 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Añadir fila
                </a>
            </div>
        </div>

        {{-- BARRA DE FILTROS BACKEND (ESTILO BARRA DE FÓRMULAS) --}}
        <form method="GET" action="{{ route('cartera.comprobantes.index') }}" class="bg-white p-3 border border-gray-300 mx-3 mb-3 d-flex flex-wrap gap-3 align-items-end shadow-sm" style="border-radius: 4px;">
            
            {{-- Mantenemos el estado actual si se está filtrando por pestaña --}}
            @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif

            {{-- Filtro Obligatorio: Mes y Año --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Periodo (Año/Mes) *</label>
                <input type="month" name="periodo" value="{{ $periodo }}" class="form-control form-control-sm border-gray-300 fw-bold text-primary" required>
            </div>

            {{-- Filtro Opcional: Búsqueda Global --}}
            <div class="flex-grow-1">
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Búsqueda General</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control border-gray-300" placeholder="Buscar cédula, monto, cuota, PR, CCO, tipo pago, observación...">
                </div>
            </div>

            {{-- Botones de Filtrado --}}
            <div>
                <button type="submit" class="btn btn-sm btn-success fw-bold px-4">
                    <i class="fas fa-filter me-1"></i> Consultar Base
                </button>
                <a href="{{ route('cartera.comprobantes.index') }}" class="btn btn-sm btn-light-danger btn-icon ms-1" title="Limpiar Filtros">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>

        {{-- Contenedor de la Hoja --}}
        <div class="bg-white shadow-sm border border-gray-300 mx-3 d-flex flex-column" style="border-radius: 0px; overflow: hidden; min-height: 500px;">
            
            {{-- Pestañas de la Hoja (Preservando los filtros actuales) --}}
            <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                <a href="{{ route('cartera.comprobantes.index', ['periodo' => $periodo, 'buscar' => request('buscar')]) }}" class="sheet-tab {{ !request('estado') ? 'active' : '' }}">
                    <i class="fas fa-table me-2 fs-9"></i> Todos los registros
                </a>
                <a href="{{ route('cartera.comprobantes.index', ['estado' => 'pendiente', 'periodo' => $periodo, 'buscar' => request('buscar')]) }}" class="sheet-tab {{ request('estado') == 'pendiente' ? 'active' : '' }}">
                    Pendientes
                </a>
                <a href="{{ route('cartera.comprobantes.index', ['estado' => 'conciliado', 'periodo' => $periodo, 'buscar' => request('buscar')]) }}" class="sheet-tab {{ request('estado') == 'conciliado' ? 'active' : '' }}">
                    Conciliados
                </a>
            </div>

            <div class="table-responsive flex-grow-1" id="resizable-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr>
                            <th class="col-index"></th>
                            <th>CÓDIGO TERCERO</th>
                            <th>NOMBRE TERCERO</th>
                            <th>OBLIGACIÓN</th> 
                            <th class="text-center">CUOTA</th>
                            <th class="text-center">PR</th>
                            <th class="text-center">CCO</th>
                            <th class="text-center">TIPO PAGO</th>
                            <th>AGENTE</th>
                            <th>MONTO PAGADO</th>
                            <th>FECHA PAGO</th>
                            <th>ID INTERACCIÓN</th>
                            <th>ID TRANSACCIÓN BANCARIA</th>
                            <th>BANCO DESTINO</th> 
                            <th>OBSERVACIÓN</th>
                            <th>HASH TRANSACCIÓN</th>
                            <th>ESTADO</th>
                            <th>SOPORTE</th>
                            <th style="text-align: center; width: 80px;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comprobantes as $index => $comprobante)
                        <tr>
                            <td class="col-index">{{ $comprobantes->firstItem() + $index }}</td>
                            
                            <td class="fw-bold text-center bg-light-soft">{{ $comprobante->cod_ter_MaeTerceros }}</td>
                            
                            <td class="text-uppercase text-gray-700">
                                {{ optional($comprobante->tercero)->nom_ter ?? '---' }}
                            </td>

                            <td class="text-gray-700" style="max-width: 150px; text-overflow: ellipsis; overflow: hidden;">
                                {{ optional($comprobante->obligacion)->nombre ?? '---' }}
                            </td>
                            
                            <td class="text-center font-monospace text-muted">{{ $comprobante->numero_cuota ?? '---' }}</td>
                            <td class="text-center font-monospace text-muted">{{ $comprobante->pr ?? '---' }}</td>
                            <td class="text-center font-monospace text-muted">{{ $comprobante->cco ?? '---' }}</td>

                            <td class="text-center">
                                <span class="badge badge-light-secondary fs-10 text-uppercase">{{ $comprobante->tipo_pago ?? '---' }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-15px me-2">
                                        <div class="symbol-label bg-light-primary text-primary fw-bold" style="font-size: 8px;">
                                            {{ substr(optional($comprobante->user)->name ?? 'A', 0, 1) }}
                                        </div>
                                    </div>
                                    {{ optional($comprobante->user)->nombre_corto ?? 'SISTEMA' }}
                                </div>
                            </td>

                            <td class="text-end fw-bold text-success pe-4">
                                $ {{ number_format($comprobante->monto_pagado, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @php
                                    $fechaStr = (string)$comprobante->fecha_pago;
                                    $f = strlen($fechaStr) == 8 ? \Carbon\Carbon::createFromFormat('Ymd', $fechaStr)->format('d/m/Y') : $fechaStr;
                                @endphp
                                {{ $f }}
                            </td>

                            <td class="text-center text-muted italic">#{{ $comprobante->id_interaction ?? '---' }}</td>

                            <td class="text-center fw-bold text-primary">{{ $comprobante->id_transaccion_bancaria ?? '---' }}</td>

                            <td class="text-gray-700" style="max-width: 150px; text-overflow: ellipsis; overflow: hidden;">
                                @if($comprobante->id_banco)
                                    {{ optional($comprobante->banco)->banco ?? 'ID: ' . $comprobante->id_banco }}
                                @else
                                    ---
                                @endif
                            </td>

                            <td class="text-muted italic" style="max-width: 200px; text-overflow: ellipsis; overflow: hidden;" title="{{ $comprobante->observacion }}">
                                {{ $comprobante->observacion ?? '---' }}
                            </td>

                            <td class="font-monospace fs-10 text-muted" style="max-width: 150px;">{{ $comprobante->hash_transaccion }}</td>

                            <td class="text-center p-0">
                                @if($comprobante->estado == 'conciliado')
                                    <div class="gs-badge gs-success">CONCILIADO</div>
                                @elseif($comprobante->estado == 'rechazado')
                                    <div class="gs-badge gs-danger">RECHAZADO</div>
                                @else
                                    <div class="gs-badge gs-warning">PENDIENTE</div>
                                @endif
                            </td>

                            <td>
                                @if($comprobante->ruta_archivo)
                                    <a href="{{ $comprobante->url_archivo }}" target="_blank" class="text-primary text-decoration-none fw-bold">
                                        <i class="fas fa-link me-1 fs-9"></i> abrir_doc
                                    </a>
                                @else
                                    <span class="text-muted italic fs-9">Sin doc</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('cartera.comprobantes.show', $comprobante->id) }}" class="text-gray-600"><i class="fas fa-eye fs-9"></i></a>
                                    <form action="{{ route('cartera.comprobantes.destroy', $comprobante->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="border-0 bg-transparent p-0 text-gray-400 btn-delete"><i class="fas fa-trash-alt fs-9"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="19" class="text-center py-10 text-muted fs-8 italic">
                                <i class="fas fa-search text-gray-400 mb-3 fs-1 d-block opacity-50"></i>
                                No hay datos para el periodo y filtros seleccionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Barra de Estado / Paginación --}}
            <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2"></i> 
                    Consulta Exitosa | Registros Totales: {{ $comprobantes->total() }}
                </div>
                <div class="gs-pagination d-flex align-items-center">
                    {{ $comprobantes->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .page-link { padding: 0.2rem 0.5rem; font-size: 0.8rem; border-color: #dadce0; color: #5f6368; }
        .page-item.active .page-link { background-color: #1a73e8; border-color: #1a73e8; }
    </style>

    <script>
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

        $(document).on('click', '.btn-delete', function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Eliminar fila?',
                text: "Esta acción no se puede deshacer",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c5221f',
                cancelButtonColor: '#5f6368',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    </script>
    @endpush
</x-base-layout>