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
                        <span class="badge bg-light-success text-success fw-bold"><i class="fas fa-sync fa-spin me-1"></i> Modo de Cruce Activo</span>
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

        {{-- ========================================================== --}}
        {{-- PANEL DE FILTRADO MAESTRO UNIFICADO                        --}}
        {{-- ========================================================== --}}
        <form method="GET" action="{{ route('contabilidad.extractos.conciliacion') }}" class="bg-white p-4 mx-3 mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #e4e6ef;">
            
            <div class="row g-3 align-items-end">
                
                {{-- Filtro de Mes --}}
                <div class="col-md-3">
                    <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Periodo a Conciliar</label>
                    <input type="month" name="periodo" id="inputPeriodo" value="{{ $periodo }}" class="form-control border-gray-300 fw-bold text-primary" style="transition: 0.3s;">
                </div>

                {{-- Filtro de Cuenta (Aplica al lado izquierdo) --}}
                <div class="col-md-4">
                    <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Cuenta Bancaria</label>
                    <select name="banco_id" class="form-select border-gray-300">
                        <option value="">Todas las cuentas...</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->banco }} - {{ substr($cuenta->numero_cuenta, -4) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Búsqueda de Texto y Botones --}}
                <div class="col-md-5">
                    <label class="form-label fs-9 fw-bolder text-muted text-uppercase mb-1">Búsqueda Libre (Cédula, Monto, Referencia)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-gray-300"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control border-gray-300" placeholder="Buscar coincidencia exacta...">
                        <button type="submit" class="btn fw-bold px-4" style="background-color: #e1f0fa; color: #008be1; border: 1px solid #b9dfff;">Filtrar</button>
                        <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-light px-3 border-gray-300" title="Limpiar todo">
                            <i class="fas fa-times text-danger"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Switch para Modo Global --}}
            <div class="mt-4 pt-3 border-top border-gray-200 d-flex justify-content-between align-items-center">
                <div class="form-check form-switch d-flex align-items-center gap-2 m-0 p-0">
                    <input class="form-check-input m-0 cursor-pointer" type="checkbox" role="switch" id="switchGlobal" name="is_global" value="1" {{ isset($is_global) && $is_global ? 'checked' : '' }} style="height: 22px; width: 44px;">
                    <label class="form-check-label fs-8 fw-bold text-dark cursor-pointer ms-2" for="switchGlobal">
                        Rastreo Global Histórico 
                        <span class="text-muted fw-normal ms-1">(Ignora el mes y busca huérfanos en toda la base de datos)</span>
                    </label>
                </div>

                @if(isset($is_global) && $is_global)
                    <span class="badge rounded-pill fs-10 px-3 py-2" style="background-color: #fff4f4; color: #d9214e; border: 1px solid #ffe2e5;">
                        <i class="fas fa-globe me-1"></i> Mostrando historial completo
                    </span>
                @endif
            </div>
        </form>

        {{-- Script para opacar el selector de mes cuando el Rastreo Global está encendido --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const switchGlobal = document.getElementById('switchGlobal');
                const inputPeriodo = document.getElementById('inputPeriodo');

                if(switchGlobal && inputPeriodo) {
                    function togglePeriodo() {
                        if(switchGlobal.checked) {
                            inputPeriodo.style.opacity = '0.4';
                            inputPeriodo.style.pointerEvents = 'none';
                            // Modificamos el título de las tablas por UX
                            document.querySelectorAll('.sheet-tab i').forEach(el => {
                                el.parentElement.innerHTML = el.parentElement.innerHTML.replace(/\d{6}/, 'HISTÓRICO');
                            });
                        } else {
                            inputPeriodo.style.opacity = '1';
                            inputPeriodo.style.pointerEvents = 'auto';
                        }
                    }

                    switchGlobal.addEventListener('change', togglePeriodo);
                    togglePeriodo(); // Ejecutar al cargar la página
                }
            });
        </script>
        {{-- ========================================================== --}}

        {{-- Contenedor de las Dos Hojas de Cálculo --}}
        <div class="row g-3 flex-grow-1 mx-1">
            
            {{-- ======================================================= --}}
            {{-- LADO IZQUIERDO: EXTRACTO DEL BANCO (HOJA 1)             --}}
            {{-- ======================================================= --}}
            <div class="col-lg-5 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1 rounded-3 overflow-hidden">
                    
                    <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                        <div class="sheet-tab active fw-bold text-primary">
                            <i class="fas fa-university me-1"></i> Extractos Bancarios ({{ str_replace('-', '', $periodo) }})
                        </div>
                    </div>

                    <div class="table-responsive flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table-gsheets table-hover w-100" id="tablaBanco">
                            <thead class="sticky-top bg-white shadow-xs">
                                <tr>
                                    <th class="col-index" style="width: 40px;">#</th>
                                    <th style="width: 90px;">FECHA</th>
                                    <th>ORIGEN / DETALLE BANCARIO</th>
                                    <th class="text-end" style="width: 120px;">INGRESO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extractosPendientes as $index => $movimiento)
                                    <tr class="item-extracto cursor-pointer transition-3ms" data-id="{{ $movimiento->id_transaccion }}">
                                        
                                        <td class="col-index">{{ $extractosPendientes->firstItem() + $index }}</td>
                                        
                                        <td class="text-center text-gray-700">
                                            <span class="fw-bold d-block">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</span>
                                            <span class="text-muted fs-10">{{ $movimiento->fecha_movimiento->format('H:i') }}</span>
                                        </td>
                                        
                                        <td class="text-truncate" style="max-width: 200px;" title="Hash: {{ $movimiento->hash_transaccion }}">
                                            <span class="text-dark fw-bold d-block text-truncate">
                                                <i class="far fa-id-card text-muted me-1"></i> C.C: {{ $movimiento->referencia_cedula ?? 'N/A' }}
                                            </span>
                                            <span class="text-muted fs-9 d-block text-truncate">
                                                <i class="fas fa-hashtag text-muted me-1"></i> Ref: {{ $movimiento->referencia_oficina ?? '---' }}
                                            </span>
                                            <span class="text-primary fs-9 fw-bold d-block text-truncate">
                                                <i class="fas fa-building text-muted me-1"></i> {{ optional($movimiento->cuentaBancaria)->banco ?? 'Banco No Asignado' }}
                                            </span>
                                        </td>
                                        
                                        <td class="text-end fw-bolder text-success fs-6 align-middle">
                                            ${{ number_format($movimiento->valor_ingreso, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-muted fs-7 italic">
                                            <div class="d-flex flex-column align-items-center justify-content-center p-5">
                                                <i class="fas fa-check-circle text-success mb-3 fs-1 opacity-50"></i>
                                                <span>No hay movimientos bancarios pendientes para estos filtros.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                        <div class="d-flex align-items-center">
                            {{ $extractosPendientes->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <span>Total Bancos: <strong class="text-dark">{{ $extractosPendientes->total() }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- LADO DERECHO: SOPORTES DE CARTERA (HOJA 2)              --}}
            {{-- ======================================================= --}}
            <div class="col-lg-7 d-flex flex-column">
                <div class="bg-white shadow-sm border border-gray-300 d-flex flex-column flex-grow-1 rounded-3 overflow-hidden">
                    
                    <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                        <div class="sheet-tab active fw-bold text-info">
                            <i class="fas fa-receipt me-1"></i> Pagos Registrados en Cartera ({{ str_replace('-', '', $periodo) }})
                        </div>
                    </div>

                    <div class="table-responsive flex-grow-1" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table-gsheets table-hover w-100" id="tablaCartera">
                            <thead class="sticky-top bg-white shadow-xs">
                                <tr>
                                    <th class="col-index" style="width: 40px;">#</th>
                                    <th style="width: 85px;">FECHA</th>
                                    <th>CLIENTE Y DETALLE DE PAGO</th>
                                    <th class="text-end" style="width: 110px;">VALOR</th>
                                    <th class="text-center" style="width: 100px;">ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comprobantesCartera as $index => $comprobante)
                                    <tr class="item-cartera transition-3ms">
                                        <td class="col-index align-middle">{{ $comprobantesCartera->firstItem() + $index }}</td>
                                        
                                        <td class="text-center text-gray-700 align-middle">
                                            @php
                                                $f_str = (string) $comprobante->fecha_pago;
                                                $fecha_dmy = $f_str;
                                                $fecha_hora = '';
                                                
                                                try {
                                                    if (strlen($f_str) === 14) {
                                                        $c = \Carbon\Carbon::createFromFormat('YmdHis', $f_str);
                                                        $fecha_dmy = $c->format('d/m/Y');
                                                        $fecha_hora = $c->format('H:i');
                                                    } elseif (strlen($f_str) === 8) {
                                                        $c = \Carbon\Carbon::createFromFormat('Ymd', $f_str);
                                                        $fecha_dmy = $c->format('d/m/Y');
                                                    } else {
                                                        $c = \Carbon\Carbon::parse($f_str);
                                                        $fecha_dmy = $c->format('d/m/Y');
                                                    }
                                                } catch (\Exception $e) {
                                                    $fecha_dmy = $f_str;
                                                }
                                            @endphp
                                            <span class="fw-bold d-block">{{ $fecha_dmy }}</span>
                                            @if($fecha_hora)
                                                <span class="text-muted fs-10">{{ $fecha_hora }}</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-truncate" style="max-width: 250px;">
                                            {{-- Información del Cliente --}}
                                            <span class="text-dark fw-bold d-block text-truncate" title="{{ optional($comprobante->tercero)->nombre_completo ?? 'Tercero ID: '.$comprobante->cod_ter_MaeTerceros }}">
                                                <i class="fas fa-user-circle text-muted me-1"></i> 
                                                {{ optional($comprobante->tercero)->nombre_completo ?? 'C.C: '.$comprobante->cod_ter_MaeTerceros }}
                                            </span>
                                            
                                            {{-- Detalle de la Obligación y Cuota --}}
                                            <span class="text-muted fs-9 d-block text-truncate">
                                                <i class="fas fa-briefcase text-muted me-1"></i> 
                                                Obligación: <span class="fw-bold">{{ optional($comprobante->obligacion)->nombre ?? 'N/A' }}</span> 
                                                | Cuota: <span class="badge bg-light text-dark border">{{ $comprobante->numero_cuota ?? '-' }}</span>
                                            </span>

                                            {{-- Documento / Enlace al S3 --}}
                                            <span class="fs-9 d-flex align-items-center mt-1">
                                                @if($comprobante->url_archivo != '#')
                                                    <a href="{{ $comprobante->url_archivo }}" target="_blank" class="text-info text-decoration-none border border-info rounded px-1" title="Ver Soporte Original">
                                                        <i class="fas fa-external-link-alt me-1"></i> Ver Recibo Subido
                                                    </a>
                                                @else
                                                    <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Sin archivo</span>
                                                @endif
                                                
                                                @if($comprobante->tipo_pago)
                                                    <span class="ms-2 badge bg-light-primary text-primary">{{ $comprobante->tipo_pago }}</span>
                                                @endif

                                                {{-- Badge con Tooltip HTML para mostrar Banco y Usuario --}}
                                                <span class="ms-2 badge bg-light text-dark border cursor-pointer" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-html="true" 
                                                    title="<div class='text-start p-1' style='min-width: 160px;'>
                                                                <div class='mb-2'>
                                                                    <i class='fas fa-university text-primary me-1'></i> <strong class='text-white'>Banco Destino:</strong> <br> 
                                                                    <span class='text-light'>{{ optional($comprobante->banco)->banco ?? 'No especificado' }}</span>
                                                                </div>
                                                                <div>
                                                                    <i class='fas fa-user-edit text-success me-1'></i> <strong class='text-white'>Subido por:</strong> <br> 
                                                                    <span class='text-light'>{{ optional($comprobante->user)->name ?? (optional($comprobante->user)->nombres ?? 'Desconocido') }}</span>
                                                                </div>
                                                            </div>">
                                                    <i class="fas fa-info-circle text-info me-1"></i> Más detalles
                                                </span>
                                            </span>
                                        </td>
                                        
                                        <td class="text-end fw-bolder text-dark fs-6 align-middle">
                                            ${{ number_format($comprobante->monto_pagado, 2, ',', '.') }}
                                        </td>
                                        
                                        <td class="text-center p-2 align-middle">
                                            <button type="button" class="btn btn-sm btn-outline-info w-100 py-2 px-2 fw-bolder fs-9 rounded-2 btn-vincular shadow-sm" 
                                                    data-comprobante-id="{{ $comprobante->id }}" title="Cruzar con el Banco seleccionado">
                                                <i class="fas fa-link me-1"></i> CRUZAR
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-muted fs-7 italic">
                                            <div class="d-flex flex-column align-items-center justify-content-center p-5">
                                                <i class="fas fa-folder-open text-muted mb-3 fs-1 opacity-50"></i>
                                                <span>No hay soportes de cartera pendientes en este periodo.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                        <div class="d-flex align-items-center">
                            {{ $comprobantesCartera->links('pagination::bootstrap-4') }}
                        </div>
                        <div>
                            <span>Total Cartera: <strong class="text-dark">{{ $comprobantesCartera->total() }}</strong></span>
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
            
            // Inicializar Tooltips de Bootstrap para permitir HTML en el hover
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            let selectedExtractoId = null;

            // Seleccionar fila del banco
            $('.item-extracto').on('click', function() {
                $('.item-extracto').removeClass('selected-row');
                $(this).addClass('selected-row');
                selectedExtractoId = $(this).data('id');
                
                // Efecto visual para indicar que estamos listos para cruzar
                $('.btn-vincular').removeClass('btn-outline-info').addClass('btn-info text-white shadow');
            });

            // Botón Cruzar
            $('.btn-vincular').on('click', function() {
                if(!selectedExtractoId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selecciona un movimiento',
                        text: 'Primero debes hacer clic en una fila del "Extracto Bancario" (Lado Izquierdo) para vincularlo con este pago.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let idComprobante = $(this).data('comprobante-id');

                Swal.fire({
                    title: '¿Confirmar Cruce?',
                    html: "Estás a punto de conciliar el comprobante de cartera con el movimiento bancario seleccionado. <br><br><b>Esta acción los sacará de esta lista.</b>",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#188038',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="fas fa-check-double me-1"></i> Sí, Conciliar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#manual_id_transaccion').val(selectedExtractoId);
                        $('#manual_id_comprobante').val(idComprobante);
                        
                        // Mostrar loader antes de enviar
                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Conciliando registros',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });
                        
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
            width: auto; min-width: 100%; border-collapse: collapse; font-size: 11.5px; color: #3c4043; table-layout: fixed;
        }
        .table-gsheets thead th {
            background-color: #f8f9fa; border: 1px solid #dadce0; padding: 8px 10px; font-weight: bold; color: #5f6368; 
            text-align: left; white-space: nowrap; z-index: 2; text-transform: uppercase; letter-spacing: 0.5px; font-size: 10px;
        }
        .table-gsheets tbody td {
            border: 1px solid #dadce0; padding: 6px 10px; min-height: 40px; vertical-align: middle; 
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .col-index {
            background-color: #f8f9fa; text-align: center !important; color: #5f6368; font-weight: bold; border-right: 2px solid #dadce0 !important;
        }
        .sheet-tab {
            padding: 8px 18px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block;
        }
        .sheet-tab.active {
            background-color: #fff; border-bottom: 3px solid currentColor; border-top-left-radius: 8px; border-top-right-radius: 8px;
        }
        
        /* EFECTOS INTERACTIVOS */
        .transition-3ms { transition: all 0.2s ease-in-out; }
        .table-hover tbody tr:hover td { background-color: #f1f3f4; cursor: pointer; }
        
        /* ESTADO DE FILA SELECCIONADA (ESTILO EXCEL/BANCO) */
        .selected-row td {
            background-color: #e8f0fe !important; 
            border-top: 2px solid #1a73e8 !important; 
            border-bottom: 2px solid #1a73e8 !important;
        }
        .selected-row .col-index {
            background-color: #1a73e8 !important;
            color: white !important;
            border-right: none !important;
        }

        /* Ajuste Paginación Minimalista */
        .pagination { margin-bottom: 0 !important; }
        .page-link { padding: 0.2rem 0.6rem; font-size: 0.85rem; border-color: #dadce0; color: #5f6368; }
        .page-item.active .page-link { background-color: #1a73e8; border-color: #1a73e8; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    </style>
</x-base-layout>