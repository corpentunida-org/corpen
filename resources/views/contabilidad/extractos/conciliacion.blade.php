<x-base-layout>
    <div class="app-container py-5 flex-grow-1 d-flex flex-column" style="min-height: 90vh; background-color: #f8f9fa;">
        
        {{-- Header Superior --}}
        <div class="d-flex align-items-center mb-4 px-3 justify-content-between">
            <div class="d-flex align-items-center">
                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bolder m-0 text-dark fs-3"><i class="fas fa-columns text-primary me-2"></i>Mesa de Conciliación</h3>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-light-success text-success fw-bold">Modo de Cruce Activo</span>
                    </div>
                </div>
            </div>

            {{-- Botón Conciliación Automática --}}
            <form action="{{ route('contabilidad.extractos.conciliacion-automatica') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-success fw-bolder fs-6 px-4 shadow-sm" {{ $extractosPendientes->count() == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-robot me-2"></i> Auto-Conciliar Mes Actual
                </button>
            </form>
        </div>

        {{-- BARRA DE FILTROS BACKEND MAESTRA --}}
        <form method="GET" action="{{ route('contabilidad.extractos.conciliacion') }}" class="bg-white p-3 border border-gray-300 mx-3 mb-3 d-flex flex-wrap gap-3 align-items-end shadow-sm" style="border-radius: 4px;">
            
            {{-- Filtro Obligatorio: Mes y Año --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Periodo a Conciliar *</label>
                <input type="month" name="periodo" value="{{ $periodo }}" class="form-control form-control-sm border-gray-300 fw-bold text-primary" required>
            </div>

            {{-- Filtro Opcional: Banco (Aplica a lado izquierdo) --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Filtrar Banco</label>
                <select name="banco_id" class="form-select form-select-sm border-gray-300" style="min-width: 150px;">
                    <option value="">Todas las cuentas...</option>
                    @foreach($cuentas as $cuenta)
                        <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                            {{ $cuenta->banco }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Opcional: Distrito (Aplica a lado izquierdo) --}}
            <div>
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Distrito Origen</label>
                <select name="distrito" class="form-select form-select-sm border-gray-300" style="min-width: 120px;">
                    <option value="">Todos...</option>
                    @foreach($distritos as $dist)
                        <option value="{{ $dist }}" {{ $distrito == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro Opcional: Búsqueda Global (Aplica a ambos lados) --}}
            <div class="flex-grow-1">
                <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Búsqueda General</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-gray-300" placeholder="Buscar valor exacto, ref, tercero...">
                </div>
            </div>

            {{-- Botones de Filtrado --}}
            <div>
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-4">
                    <i class="fas fa-filter me-1"></i> Consultar BD
                </button>
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-sm btn-light-danger btn-icon ms-1" title="Limpiar Filtros">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>

        {{-- Contenedor de las Dos Hojas de Cálculo --}}
        <div class="row g-3 flex-grow-1 mx-1">
            
            {{-- ======================================================= --}}
            {{-- LADO IZQUIERDO: EXTRACTO DEL BANCO (HOJA 1)             --}}
            {{-- ======================================================= --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1" style="border-radius: 0px; overflow: hidden;">
                    
                    <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                        <div class="sheet-tab active fw-bold text-primary">
                            <i class="fas fa-university me-1"></i> Banco_{{ str_replace('-', '', $periodo) }}
                        </div>
                    </div>

                    <div class="table-responsive flex-grow-1" style="max-height: 55vh; overflow-y: auto;">
                        <table class="table-gsheets table-hover w-100" id="tablaBanco">
                            <thead class="sticky-top bg-white shadow-xs">
                                <tr>
                                    <th class="col-index" style="width: 30px;">#</th>
                                    <th style="width: 85px;">FECHA</th>
                                    <th>DESCRIPCIÓN BANCO</th>
                                    <th class="text-end" style="width: 110px;">INGRESO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extractosPendientes as $index => $movimiento)
                                    <tr class="item-extracto cursor-pointer transition-3ms" data-id="{{ $movimiento->id_transaccion }}">
                                        
                                        <td class="col-index">{{ $extractosPendientes->firstItem() + $index }}</td>
                                        <td class="text-center text-gray-700">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                                        
                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $movimiento->descripcion_banco }} | Ref: {{ $movimiento->hash_transaccion }}">
                                            <span class="text-dark fw-bold d-block text-truncate">{{ $movimiento->descripcion_banco }}</span>
                                            <span class="text-muted fs-9 font-monospace">Ref: {{ substr($movimiento->hash_transaccion, 0, 15) }}...</span>
                                        </td>
                                        
                                        <td class="text-end fw-bolder text-success">
                                            ${{ number_format($movimiento->valor_ingreso, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-muted fs-7 italic">
                                            <i class="fas fa-search text-gray-400 mb-2 fs-2 d-block opacity-50"></i>
                                            Sin movimientos pendientes para estos filtros.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer Banco: Paginación y Estado --}}
                    <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                        <div class="d-flex align-items-center">
                            {{ $extractosPendientes->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <span>Total en BD: <strong>{{ $extractosPendientes->total() }}</strong></span> | Selecciona para vincular
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- LADO DERECHO: SOPORTES DE CARTERA (HOJA 2)              --}}
            {{-- ======================================================= --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1" style="border-radius: 0px; overflow: hidden;">
                    
                    <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                        <div class="sheet-tab active fw-bold text-info">
                            <i class="fas fa-receipt me-1"></i> Cartera_{{ str_replace('-', '', $periodo) }}
                        </div>
                    </div>

                    <div class="table-responsive flex-grow-1" style="max-height: 55vh; overflow-y: auto;">
                        <table class="table-gsheets table-hover w-100" id="tablaCartera">
                            <thead class="sticky-top bg-white shadow-xs">
                                <tr>
                                    <th class="col-index" style="width: 30px;">#</th>
                                    <th style="width: 85px;">FECHA</th>
                                    <th>DETALLE SOPORTE</th>
                                    <th class="text-end" style="width: 100px;">VALOR</th>
                                    <th class="text-center" style="width: 80px;">ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comprobantesCartera as $index => $comprobante)
                                    <tr class="item-cartera transition-3ms">
                                        <td class="col-index">{{ $comprobantesCartera->firstItem() + $index }}</td>
                                        
                                        <td class="text-center text-gray-700">
                                            {{ \Carbon\Carbon::parse($comprobante->fecha_pago)->format('d/m/Y') }}
                                        </td>
                                        
                                        <td class="text-truncate" style="max-width: 180px;" title="Archivo: {{ basename($comprobante->ruta_archivo) }}">
                                            <span class="text-dark fw-bold d-block">Tercero: #{{ $comprobante->cod_ter_MaeTerceros }}</span>
                                            <span class="text-muted fs-9 d-flex align-items-center">
                                                <i class="fas fa-file-invoice me-1"></i> {{ basename($comprobante->ruta_archivo) }}
                                            </span>
                                        </td>
                                        
                                        <td class="text-end fw-bolder text-dark">
                                            ${{ number_format($comprobante->monto_pagado, 2, ',', '.') }}
                                        </td>
                                        
                                        <td class="text-center p-1">
                                            <button type="button" class="btn btn-sm btn-outline-info w-100 py-1 px-2 fw-bolder fs-9 rounded-1 btn-vincular" 
                                                    data-comprobante-id="{{ $comprobante->id }}">
                                                VINCULAR
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-muted fs-7 italic">
                                            <i class="fas fa-search text-gray-400 mb-2 fs-2 d-block opacity-50"></i>
                                            No hay soportes de cartera pendientes.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer Cartera: Paginación y Estado --}}
                    <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                        <div class="d-flex align-items-center">
                            {{ $comprobantesCartera->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <span>Total en BD: <strong>{{ $comprobantesCartera->total() }}</strong></span> | Esperando cruce...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario Oculto para la Conciliación Manual --}}
    <form id="formConciliacionManual" action="{{ route('contabilidad.extractos.conciliacion-manual') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="id_transaccion" id="manual_id_transaccion">
        <input type="hidden" name="id_comprobante" id="manual_id_comprobante">
    </form>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let selectedExtractoId = null;

            // Seleccionar fila del banco
            $('.item-extracto').on('click', function() {
                // Remover clase de selección anterior
                $('.item-extracto').removeClass('selected-row');
                
                // Agregar clase a la nueva
                $(this).addClass('selected-row');
                
                // Guardar ID
                selectedExtractoId = $(this).data('id');
            });

            // Botón Vincular
            $('.btn-vincular').on('click', function() {
                if(!selectedExtractoId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Falta información',
                        text: 'Primero debes hacer clic en una fila de la tabla del Banco (Lado Izquierdo) para seleccionarla.'
                    });
                    return;
                }

                let idComprobante = $(this).data('comprobante-id');

                Swal.fire({
                    title: '¿Confirmar Vinculación?',
                    text: "Estás a punto de conciliar manualmente el pago de cartera con el movimiento bancario seleccionado.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#188038',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Sí, Conciliar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#manual_id_transaccion').val(selectedExtractoId);
                        $('#manual_id_comprobante').val(idComprobante);
                        $('#formConciliacionManual').submit();
                    }
                });
            });
        });
    </script>
    @endpush

    <style>
        /* ESTILOS HOJA DE CÁLCULO */
        .table-gsheets {
            width: auto; min-width: 100%; border-collapse: collapse; font-size: 11px; color: #3c4043; table-layout: fixed;
        }
        .table-gsheets thead th {
            background-color: #f8f9fa; border: 1px solid #dadce0; padding: 6px 10px; font-weight: bold; color: #5f6368; 
            text-align: left; white-space: nowrap; z-index: 2;
        }
        .table-gsheets tbody td {
            border: 1px solid #dadce0; padding: 4px 10px; height: 35px; vertical-align: middle; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .col-index {
            background-color: #f8f9fa; text-align: center !important; color: #5f6368; font-weight: bold; border-right: 2px solid #dadce0 !important;
        }
        .sheet-tab {
            padding: 6px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;
        }
        .sheet-tab.active {
            background-color: #fff; border-bottom: 3px solid currentColor; border-top-left-radius: 0px; border-top-right-radius: 0px;
        }
        
        /* EFECTOS INTERACTIVOS */
        .transition-3ms { transition: all 0.15s ease-in-out; }
        .table-hover tbody tr:hover td { background-color: #f1f3f4; cursor: pointer; }
        
        /* ESTADO DE FILA SELECCIONADA (ESTILO EXCEL) */
        .selected-row td {
            background-color: #e8f0fe !important; 
            border-top: 1px solid #1a73e8 !important; 
            border-bottom: 1px solid #1a73e8 !important;
        }
        .selected-row .col-index {
            background-color: #1a73e8 !important;
            color: white !important;
        }

        /* Ajuste Paginación Minimalista */
        .pagination { margin-bottom: 0 !important; }
        .page-link { padding: 0.2rem 0.5rem; font-size: 0.8rem; border-color: #dadce0; color: #5f6368; }
        .page-item.active .page-link { background-color: #1a73e8; border-color: #1a73e8; }
    </style>
</x-base-layout>