<x-base-layout>
    <div class="app-container py-5">
        <div class="row g-4">
            {{-- Listado Principal --}}
            <div class="col-lg-9">
                <div class="card border-0 bg-transparent">
                    <div class="card-header bg-transparent p-0 pb-4 border-0 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="fw-bolder m-0 text-dark fs-2">Extractos Bancarios</h3>
                            <p class="text-muted m-0 fs-6">Historial de movimientos transaccionales</p>
                        </div>
                        <div class="position-relative mt-2 mt-md-0" style="min-width: 280px;">
                            <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 1rem;"></i>
                            <input type="text" id="tableSearch" class="form-control rounded-pill border-light bg-white shadow-xs fs-6" style="padding-left: 2.5rem;" placeholder="Buscar referencia o monto...">
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive bg-white rounded-3 shadow-sm">
                            <table class="table align-middle gs-0 gy-3 mb-0" id="extractosTable">
                                <thead>
                                    <tr class="fw-bolder text-gray-800 fs-6 text-uppercase ls-1 border-bottom border-light">
                                        <th class="ps-4 py-3">Fecha</th>
                                        <th class="py-3">Cuenta / Banco</th>
                                        <th class="py-3">Descripción</th>
                                        <th class="py-3 text-end">Monto Ingreso</th>
                                        <th class="py-3 text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-5">
                                    @forelse($extractos as $movimiento)
                                    <tr class="hover-bg-light transition-3ms border-bottom border-light">
                                        <td class="ps-4 py-3">
                                            <span class="text-dark fw-bold">{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</span>
                                            <span class="text-muted d-block fs-7">{{ $movimiento->fecha_movimiento->format('H:i') }}</span>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-dark fw-bold d-block">{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}</span>
                                            <span class="text-muted font-monospace fs-7">{{ $movimiento->cuentaBancaria->numero_cuenta ?? '' }}</span>
                                        </td>
                                        <td class="py-3" style="max-width: 250px;">
                                            <span class="text-gray-800 d-block text-truncate" title="{{ $movimiento->descripcion_banco }}">
                                                {{ $movimiento->descripcion_banco }}
                                            </span>
                                            <span class="text-muted font-monospace fs-8">Ref: {{ substr($movimiento->hash_transaccion, 0, 8) }}...</span>
                                        </td>
                                        <td class="py-3 text-end">
                                            <span class="text-success fw-bolder fs-4">${{ number_format($movimiento->valor_ingreso, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-3 text-center">
                                            @php
                                                $estadoConfig = match($movimiento->estado_conciliacion) {
                                                    'Conciliado_Auto'   => ['color' => 'success', 'icon' => 'fa-robot'],
                                                    'Conciliado_Manual' => ['color' => 'primary', 'icon' => 'fa-user-check'],
                                                    'Pendiente'         => ['color' => 'warning', 'icon' => 'fa-clock'],
                                                    'Anulado'           => ['color' => 'danger',  'icon' => 'fa-times-circle'],
                                                    default             => ['color' => 'secondary', 'icon' => 'fa-question']
                                                };
                                            @endphp
                                            <span class="badge bg-light-{{ $estadoConfig['color'] }} text-{{ $estadoConfig['color'] }} border-0 px-3 py-2 fw-bolder">
                                                <i class="fas {{ $estadoConfig['icon'] }} me-1"></i> {{ str_replace('_', ' ', $movimiento->estado_conciliacion) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-15 bg-white">
                                            <i class="fas fa-file-invoice-dollar text-gray-200 fa-4x mb-4"></i>
                                            <span class="text-dark fs-5 fw-bold d-block">No hay movimientos registrados</span>
                                            <p class="text-muted mt-2">Importa un extracto bancario para comenzar.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar de Acciones --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm bg-white p-5 mb-4" style="border-radius: 12px;">
                    <h5 class="fw-bolder text-dark mb-4 fs-4">Flujo de Trabajo</h5>
                    
                    <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-primary w-100 rounded-pill fs-5 fw-bolder py-3 shadow-sm mb-3">
                        <i class="fas fa-cloud-upload-alt me-2"></i> Subir Extracto
                    </a>
                    
                    <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-dark w-100 rounded-pill fs-5 fw-bolder py-3 shadow-sm mb-4">
                        <i class="fas fa-compress-arrows-alt me-2"></i> Conciliación
                    </a>

                    <div class="separator separator-dashed my-4 border-gray-300"></div>

                    <h6 class="text-muted fw-bold text-uppercase fs-7 ls-1 mb-3">Resumen Rápido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-gray-600 fw-bold">Pendientes:</span>
                        <span class="text-warning fw-bolder fs-5">{{ $extractos->where('estado_conciliacion', 'Pendiente')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-gray-600 fw-bold">Conciliados:</span>
                        <span class="text-success fw-bolder fs-5">{{ $extractos->whereIn('estado_conciliacion', ['Conciliado_Auto', 'Conciliado_Manual'])->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .transition-3ms { transition: all 0.2s ease-in-out; }
        .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important; }
        .ls-1 { letter-spacing: 0.05em; }
        .hover-bg-light:hover { background-color: #f8f9fa !important; }
        .table { margin-bottom: 0; }
        .table > :not(caption) > * > * { border-bottom-width: 1px; }
        .table tbody tr:last-child td { border-bottom: none; }
    </style>
    <script>
        $("#tableSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#extractosTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
    @endpush
</x-base-layout>