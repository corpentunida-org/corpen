<x-base-layout>
    <div class="app-container py-5">
        
        {{-- Header superior --}}
        <div class="d-flex align-items-center mb-5">
            <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bolder m-0 text-dark fs-2">Detalle del Movimiento</h3>
                <p class="text-muted m-0 fs-6">Inspección de la transacción bancaria #{{ $extracto->id_transaccion }}</p>
            </div>
            
            <div class="ms-auto">
                @php
                    $estadoConfig = match($extracto->estado_conciliacion) {
                        'Conciliado_Auto'   => ['color' => 'success', 'icon' => 'fa-robot'],
                        'Conciliado_Manual' => ['color' => 'primary', 'icon' => 'fa-user-check'],
                        'Pendiente'         => ['color' => 'warning', 'icon' => 'fa-clock'],
                        'Anulado'           => ['color' => 'danger',  'icon' => 'fa-times-circle'],
                        default             => ['color' => 'secondary', 'icon' => 'fa-question']
                    };
                @endphp
                <span class="badge bg-{{ $estadoConfig['color'] }} fs-5 px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas {{ $estadoConfig['icon'] }} text-white me-2"></i>{{ str_replace('_', ' ', $extracto->estado_conciliacion) }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: Datos principales --}}
            <div class="col-lg-8">
                
                {{-- Tarjeta 1: Información Financiera --}}
                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-bottom border-light p-4">
                        <h4 class="fw-bolder m-0 text-dark fs-4"><i class="fas fa-money-check-alt text-success me-2"></i>Información Financiera</h4>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Monto de Ingreso</span>
                                <span class="text-success fw-bolder fs-1 lh-1">${{ number_format($extracto->valor_ingreso, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Fecha del Movimiento</span>
                                <span class="text-dark fw-bold fs-4">
                                    <i class="far fa-calendar-alt text-muted me-1"></i> {{ $extracto->fecha_movimiento->format('d \d\e F, Y - H:i') }}
                                </span>
                            </div>
                            <div class="col-12 mt-4">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Descripción Reportada por el Banco</span>
                                <div class="bg-light p-3 rounded text-dark fs-5">
                                    {{ $extracto->descripcion_banco }}
                                </div>
                            </div>
                            <div class="col-12">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Hash / ID Único de Transacción</span>
                                <code class="bg-light p-2 rounded text-dark fs-6 d-block text-break border border-light">
                                    <i class="fas fa-fingerprint text-muted me-2"></i>{{ $extracto->hash_transaccion }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta 2: Detalles de Referencia (Tus nuevos campos) --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-bottom border-light p-4">
                        <h4 class="fw-bolder m-0 text-dark fs-4"><i class="fas fa-user-tag text-primary me-2"></i>Detalles de Referencia</h4>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Nombre de Referencia</span>
                                <span class="text-dark fw-bolder fs-5">{{ $extracto->referencia_nombre ?? 'No registrado' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Documento / Cédula / NIT</span>
                                <span class="text-dark fw-bolder fs-5">{{ $extracto->referencia_cedula ?? 'No registrado' }}</span>
                            </div>
                            <div class="col-md-6 mt-4">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Oficina de Origen</span>
                                <span class="text-dark fw-bolder fs-5"><i class="fas fa-building text-muted me-1"></i> {{ $extracto->referencia_oficina ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mt-4">
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Distrito / Ciudad</span>
                                <span class="text-dark fw-bolder fs-5"><i class="fas fa-map-marker-alt text-muted me-1"></i> {{ $extracto->referencia_distrito ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- COLUMNA DERECHA: Banco y Acciones --}}
            <div class="col-lg-4">
                {{-- Tarjeta Banco --}}
                <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <h5 class="fw-bolder mb-4 text-dark fs-4 pb-3 border-bottom border-light">
                            <i class="fas fa-university text-info me-2"></i> Cuenta Destino
                        </h5>
                        
                        <div class="mb-4">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Entidad Bancaria</span>
                            <span class="text-dark fw-bolder fs-4">{{ $extracto->cuentaBancaria->banco ?? 'Cuenta Eliminada' }}</span>
                        </div>
                        
                        <div class="mb-0">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Número de Cuenta</span>
                            <span class="text-dark fw-bolder font-monospace fs-5 bg-light px-2 py-1 rounded">
                                {{ $extracto->cuentaBancaria->numero_cuenta ?? '---' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta Acciones / Peligro --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <h5 class="fw-bolder mb-4 text-danger fs-4 pb-3 border-bottom border-light">
                            <i class="fas fa-exclamation-triangle me-2"></i> Zona de Peligro
                        </h5>
                        
                        <p class="text-muted fs-7 mb-4">Eliminar este extracto removerá el registro de la base de datos, pero si el pago ya fue conciliado, podría generar inconsistencias en cartera.</p>
                        
                        <form action="{{ route('contabilidad.extractos.destroy', $extracto->id_transaccion) }}" method="POST" id="formDeleteExtracto">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-light-danger w-100 fw-bolder rounded-pill py-3" onclick="confirmDelete()">
                                <i class="fas fa-trash-alt me-2"></i> Eliminar Movimiento
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .ls-1 { letter-spacing: 0.05em; }
        .text-break { word-wrap: break-word; overflow-wrap: break-word; }
    </style>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer y borrará el movimiento bancario permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteExtracto').submit();
                }
            })
        }
    </script>
    @endpush
</x-base-layout>