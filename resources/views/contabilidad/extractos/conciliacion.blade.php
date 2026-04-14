<x-base-layout>
    <div class="app-container py-5 flex-grow-1 d-flex flex-column" style="min-height: 90vh;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bolder m-0 text-dark fs-2">Área de Conciliación</h3>
                    <p class="text-muted m-0 fs-6">Cruza los pagos recibidos con los reportados por cartera</p>
                </div>
            </div>
            <button class="btn btn-success rounded-pill fw-bolder fs-5 px-4 shadow-sm">
                <i class="fas fa-magic me-2"></i> Ejecutar Conciliación Automática
            </button>
        </div>

        <div class="row g-4 flex-grow-1">
            {{-- LADO IZQUIERDO: EXTRACTO DEL BANCO (DATOS REALES) --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="card border-0 shadow-sm bg-white flex-grow-1" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bolder m-0 text-primary"><i class="fas fa-university me-2"></i> Movimientos Banco</h4>
                        <span class="badge bg-light-warning text-warning fw-bolder px-3 py-2 fs-6">
                            {{ $extractosPendientes->count() }} Pendientes
                        </span>
                    </div>
                    <div class="card-body p-0 bg-light" style="overflow-y: auto; max-height: 600px;">
                        
                        @forelse($extractosPendientes as $movimiento)
                            <div class="p-4 border-bottom border-white cursor-pointer hover-bg-white transition-3ms">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="fw-bolder text-dark fs-5">
                                        {{ $movimiento->cuentaBancaria->banco ?? 'Banco Desconocido' }} 
                                        <small class="text-muted fw-normal fs-7 ms-2"><i class="far fa-clock me-1"></i>{{ $movimiento->fecha_movimiento->diffForHumans() }}</small>
                                    </span>
                                    <span class="fw-bolder text-success fs-4">+${{ number_format($movimiento->valor_ingreso, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-gray-700 m-0 fs-6">{{ $movimiento->descripcion_banco }}</p>
                                <div class="mt-2 text-muted font-monospace fs-8">Ref: {{ $movimiento->hash_transaccion }}</div>
                            </div>
                        @empty
                            <div class="p-5 text-center mt-5">
                                <i class="fas fa-check-double text-success fa-4x mb-3 opacity-50"></i>
                                <h5 class="fw-bolder text-dark">¡Todo al día!</h5>
                                <p class="text-muted fs-6">No hay movimientos bancarios pendientes por conciliar.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

            {{-- LADO DERECHO: SOPORTES DE CARTERA (DATOS REALES) --}}
            <div class="col-lg-6 d-flex flex-column">
                <div class="card border-0 shadow-sm bg-white flex-grow-1" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bolder m-0 text-info"><i class="fas fa-receipt me-2"></i> Soportes Cartera</h4>
                        <div class="position-relative" style="min-width: 200px;">
                            <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 1rem;"></i>
                            <input type="text" id="buscarCartera" class="form-control rounded-pill bg-light border-0 shadow-xs" style="padding-left: 2.5rem;" placeholder="Buscar valor...">
                        </div>
                    </div>
                    <div class="card-body p-0 bg-light" style="overflow-y: auto; max-height: 600px;" id="listaCartera">
                        
                        @forelse($comprobantesCartera as $comprobante)
                            <div class="p-4 border-bottom border-white cursor-pointer hover-bg-white transition-3ms border-start border-4 border-transparent hover-border-info item-cartera">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="fw-bolder text-dark fs-5">Tercero: #{{ $comprobante->cod_ter_MaeTerceros }}</span>
                                    <span class="fw-bolder text-dark fs-4">${{ number_format($comprobante->monto_pagado, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <i class="fas fa-file-invoice text-muted me-2"></i>
                                    <span class="text-gray-600 fs-7 text-truncate" style="max-width: 180px;">{{ basename($comprobante->ruta_archivo) }}</span>
                                    
                                    {{-- El botón para vincular más adelante --}}
                                    <button class="btn btn-sm btn-outline-info rounded-pill ms-auto py-1 px-3 fw-bold">Vincular</button>
                                </div>
                                <div class="mt-2 text-muted font-monospace fs-8">Fecha Ingreso: {{ $comprobante->fecha_pago }}</div>
                            </div>
                        @empty
                            <div class="p-5 text-center mt-5">
                                <i class="fas fa-folder-open text-info fa-4x mb-3 opacity-50"></i>
                                <h5 class="fw-bolder text-dark">Sin registros</h5>
                                <p class="text-muted fs-6">No hay comprobantes de pago cargados en el sistema.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Buscador rápido para el lado derecho (Cartera)
        $("#buscarCartera").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#listaCartera .item-cartera").filter(function() {
                // Quitamos el signo peso y puntos para que pueda buscar el número limpio también
                var texto = $(this).text().toLowerCase().replace(/\./g, '');
                $(this).toggle(texto.indexOf(value) > -1)
            });
        });
    </script>
    @endpush

    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .transition-3ms { transition: all 0.2s ease-in-out; }
        .cursor-pointer { cursor: pointer; }
        .hover-bg-white:hover { background-color: #ffffff !important; box-shadow: 0 .125rem .25rem rgba(0,0,0,.05); }
        .border-transparent { border-color: transparent !important; }
        .hover-border-info:hover { border-color: #0dcaf0 !important; }
    </style>
</x-base-layout>