<x-base-layout>
    <div class="app-container py-5 flex-grow-1 d-flex flex-column" style="min-height: 90vh; background-color: #f8f9fa;">
        
        {{-- Barra Superior de Herramientas y Filtros --}}
        <div class="bg-white p-4 rounded-3 shadow-sm border border-gray-300 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            
            <div class="d-flex align-items-center">
                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-4 shadow-sm rounded-circle">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bolder m-0 text-dark fs-3"><i class="fas fa-columns text-primary me-2"></i>Mesa de Conciliación</h3>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-light-success text-success fw-bold">Modo de Cruce Activo</span>
                    </div>
                </div>
            </div>

            {{-- Filtro Global (Mes) controlado por JS --}}
            <div class="d-flex align-items-center bg-light p-2 rounded border border-gray-200">
                <label for="filtroMes" class="fw-bold text-gray-700 me-3 ms-2 fs-7 text-uppercase ls-1">Mes a Conciliar:</label>
                <input type="month" id="filtroMes" class="form-control form-control-sm border-gray-300 shadow-none fw-bold text-primary" style="width: 150px;">
                <button type="button" id="btnLimpiarFiltro" class="btn btn-sm btn-icon btn-light-danger ms-2" title="Limpiar Filtro">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Botón Conciliación Automática --}}
            <form action="{{ route('contabilidad.extractos.conciliacion-automatica') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-success fw-bolder fs-6 px-4 shadow-sm" {{ $extractosPendientes->count() == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-robot me-2"></i> Auto-Conciliar
                </button>
            </form>
        </div>

        {{-- Contenedor de las Dos Hojas de Cálculo --}}
        <div class="row g-3 flex-grow-1">
            
            {{-- ======================================================= --}}
            {{-- LADO IZQUIERDO: EXTRACTO DEL BANCO (HOJA 1)             --}}
            {{-- ======================================================= --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1" style="border-radius: 4px; overflow: hidden;">
                    
                    {{-- Pestaña / Header --}}
                    <div class="d-flex justify-content-between align-items-center bg-gray-100 border-bottom border-gray-300 px-3 py-2">
                        <div class="sheet-tab active fw-bold text-primary">
                            <i class="fas fa-university me-1"></i> Banco (Pendientes)
                        </div>
                        <div class="input-group input-group-sm w-200px">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search fs-9 text-muted"></i></span>
                            <input type="text" id="buscarBanco" class="form-control border-start-0 ps-0 fs-8 shadow-none" placeholder="Buscar monto/ref...">
                        </div>
                    </div>

                    {{-- Tabla Estilo Google Sheets --}}
                    <div class="table-responsive flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
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
                                    <tr class="item-extracto cursor-pointer transition-3ms" 
                                        data-id="{{ $movimiento->id_transaccion }}" 
                                        data-mes="{{ $movimiento->fecha_movimiento->format('Y-m') }}">
                                        
                                        <td class="col-index">{{ $index + 1 }}</td>
                                        <td class="text-center text-gray-700">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                                        
                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $movimiento->descripcion_banco }} | Ref: {{ $movimiento->hash_transaccion }}">
                                            <span class="text-dark fw-bold d-block text-truncate">{{ $movimiento->descripcion_banco }}</span>
                                            <span class="text-muted fs-9 font-monospace">Ref: {{ substr($movimiento->hash_transaccion, 0, 15) }}...</span>
                                        </td>
                                        
                                        <td class="text-end fw-bolder text-success">
                                            ${{ number_format($movimiento->valor_ingreso, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-muted fs-7 italic">
                                            <i class="fas fa-check-double text-success mb-2 fs-2 d-block opacity-50"></i>
                                            Sin movimientos pendientes.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer / Barra de Estado --}}
                    <div class="bg-gray-100 border-top border-gray-300 px-3 py-1 text-muted fs-9 d-flex justify-content-between">
                        <span id="contadorBanco">Mostrando {{ $extractosPendientes->count() }} filas</span>
                        <span>Selecciona una fila para vincular</span>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- LADO DERECHO: SOPORTES DE CARTERA (HOJA 2)              --}}
            {{-- ======================================================= --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1" style="border-radius: 4px; overflow: hidden;">
                    
                    {{-- Pestaña / Header --}}
                    <div class="d-flex justify-content-between align-items-center bg-gray-100 border-bottom border-gray-300 px-3 py-2">
                        <div class="sheet-tab active fw-bold text-info">
                            <i class="fas fa-receipt me-1"></i> Cartera (Sin Cruzar)
                        </div>
                        <div class="input-group input-group-sm w-200px">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search fs-9 text-muted"></i></span>
                            <input type="text" id="buscarCartera" class="form-control border-start-0 ps-0 fs-8 shadow-none" placeholder="Buscar monto/tercero...">
                        </div>
                    </div>

                    {{-- Tabla Estilo Google Sheets --}}
                    <div class="table-responsive flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
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
                                @php $contadorCartera = 0; @endphp
                                @foreach($comprobantesCartera as $comprobante)
                                    @if($comprobante->estado != 'conciliado')
                                        @php 
                                            $contadorCartera++; 
                                            // Formateamos la fecha para el filtro YYYY-MM
                                            $mesCartera = \Carbon\Carbon::parse($comprobante->fecha_pago)->format('Y-m');
                                        @endphp
                                        <tr class="item-cartera transition-3ms" data-mes="{{ $mesCartera }}">
                                            
                                            <td class="col-index">{{ $contadorCartera }}</td>
                                            
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
                                                ${{ number_format($comprobante->monto_pagado, 0, ',', '.') }}
                                            </td>
                                            
                                            <td class="text-center p-1">
                                                <button type="button" class="btn btn-sm btn-outline-info w-100 py-1 px-2 fw-bolder fs-9 rounded-1 btn-vincular" 
                                                        data-comprobante-id="{{ $comprobante->id }}">
                                                    VINCULAR
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($contadorCartera == 0)
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-muted fs-7 italic">
                                            <i class="fas fa-folder-open text-info mb-2 fs-2 d-block opacity-50"></i>
                                            No hay soportes pendientes.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Footer / Barra de Estado --}}
                    <div class="bg-gray-100 border-top border-gray-300 px-3 py-1 text-muted fs-9 d-flex justify-content-between">
                        <span id="contadorCarteraTexto">Mostrando {{ $contadorCartera }} filas</span>
                        <span>Esperando cruce manual...</span>
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
            
            // ==========================================
            // 1. LÓGICA DE FILTROS (Mes y Buscadores)
            // ==========================================
            const filtroMes = document.getElementById('filtroMes');
            const btnLimpiar = document.getElementById('btnLimpiarFiltro');
            const inputBanco = document.getElementById('buscarBanco');
            const inputCartera = document.getElementById('buscarCartera');

            function aplicarFiltros() {
                const mesSeleccionado = filtroMes.value; // Formato "YYYY-MM"
                const textoBanco = inputBanco.value.toLowerCase().replace(/\./g, '');
                const textoCartera = inputCartera.value.toLowerCase().replace(/\./g, '');

                let countBanco = 0;
                let countCartera = 0;

                // Filtrar Tabla Banco
                document.querySelectorAll('#tablaBanco tbody tr.item-extracto').forEach(row => {
                    const rowMes = row.getAttribute('data-mes');
                    const rowText = row.textContent.toLowerCase().replace(/\./g, '');
                    
                    const cumpleMes = (mesSeleccionado === '' || rowMes === mesSeleccionado);
                    const cumpleTexto = (rowText.includes(textoBanco));

                    if (cumpleMes && cumpleTexto) {
                        row.style.display = '';
                        countBanco++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Filtrar Tabla Cartera
                document.querySelectorAll('#tablaCartera tbody tr.item-cartera').forEach(row => {
                    const rowMes = row.getAttribute('data-mes');
                    const rowText = row.textContent.toLowerCase().replace(/\./g, '');
                    
                    const cumpleMes = (mesSeleccionado === '' || rowMes === mesSeleccionado);
                    const cumpleTexto = (rowText.includes(textoCartera));

                    if (cumpleMes && cumpleTexto) {
                        row.style.display = '';
                        countCartera++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Actualizar contadores
                document.getElementById('contadorBanco').textContent = `Mostrando ${countBanco} filas`;
                document.getElementById('contadorCarteraTexto').textContent = `Mostrando ${countCartera} filas`;
            }

            // Event Listeners de Filtros
            filtroMes.addEventListener('change', aplicarFiltros);
            inputBanco.addEventListener('keyup', aplicarFiltros);
            inputCartera.addEventListener('keyup', aplicarFiltros);
            
            btnLimpiar.addEventListener('click', () => {
                filtroMes.value = '';
                inputBanco.value = '';
                inputCartera.value = '';
                aplicarFiltros();
            });


            // ==========================================
            // 2. LÓGICA DE SELECCIÓN Y VINCULACIÓN
            // ==========================================
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
            background-color: #fff; border-bottom: 3px solid currentColor; border-top-left-radius: 4px; border-top-right-radius: 4px;
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
    </style>
</x-base-layout>