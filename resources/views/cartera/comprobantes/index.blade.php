<x-base-layout>
    <div class="app-container py-5">
        
        <div class="row g-4">
            <div class="col-lg-9">
                
                {{-- Cabecera y Filtros Rápidos --}}
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap">
                    <div>
                        <h3 class="fw-bolder m-0 text-dark fs-2">Soportes de Pago</h3>
                        <p class="text-muted m-0 fs-6">Bandeja de entrada y gestión de recibos</p>
                    </div>
                    
                    {{-- Buscador Backend (Reemplaza al de jQuery para soportar miles de registros) --}}
                    <form action="{{ route('cartera.comprobantes.index') }}" method="GET" class="position-relative mt-3 mt-md-0" style="min-width: 300px;">
                        <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 1rem;"></i>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control rounded-pill border-light bg-white shadow-sm fs-6" style="padding-left: 2.5rem;" placeholder="Buscar por código o ID...">
                        @if(request('estado'))
                            <input type="hidden" name="estado" value="{{ request('estado') }}">
                        @endif
                    </form>
                </div>

                {{-- Navegación de Estados (Tabs estilo documental) --}}
                <div class="card border-0 bg-transparent mb-4">
                    <ul class="nav nav-pills nav-pills-custom mb-3 gap-2">
                        <li class="nav-item">
                            <a class="nav-link btn btn-color-gray-600 btn-active-primary rounded-pill px-4 py-2 {{ !request('estado') ? 'active shadow-sm' : 'bg-white shadow-xs' }}" 
                               href="{{ route('cartera.comprobantes.index') }}">
                                <i class="fas fa-layer-group me-2"></i> Todos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-color-gray-600 btn-active-warning rounded-pill px-4 py-2 {{ request('estado') == 'pendiente' ? 'active shadow-sm' : 'bg-white shadow-xs' }}" 
                               href="{{ route('cartera.comprobantes.index', ['estado' => 'pendiente', 'buscar' => request('buscar')]) }}">
                                <i class="fas fa-clock me-2"></i> Pendientes de Revisión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-color-gray-600 btn-active-success rounded-pill px-4 py-2 {{ request('estado') == 'conciliado' ? 'active shadow-sm' : 'bg-white shadow-xs' }}" 
                               href="{{ route('cartera.comprobantes.index', ['estado' => 'conciliado', 'buscar' => request('buscar')]) }}">
                                <i class="fas fa-check-double me-2"></i> Conciliados
                            </a>
                        </li>
                    </ul>
                </div>
                    
                {{-- Tabla Principal --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table align-middle gs-0 gy-4 mb-0 table-hover">
                            <thead class="bg-light">
                                <tr class="fw-bolder text-gray-600 fs-7 text-uppercase ls-1">
                                    <th class="ps-4 rounded-start">Código / Tercero</th>
                                    <th>Monto</th>
                                    <th>Fecha Pago</th>
                                    <th>Estado</th>
                                    <th>Soporte Físico</th>
                                    <th class="text-end pe-4 rounded-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @forelse($comprobantes as $comprobante)
                                {{-- Borde izquierdo visual para identificar rápido los pendientes --}}
                                <tr class="transition-3ms border-bottom border-light {{ $comprobante->estado == 'pendiente' ? 'border-start border-4 border-warning' : '' }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px me-3">
                                                <div class="symbol-label bg-light text-dark rounded-circle fw-bolder">
                                                    <i class="fas fa-building text-gray-500"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder mb-1 fs-5">#{{ $comprobante->cod_ter_MaeTerceros }}</span>
                                                <span class="text-muted fs-8">ID CRM: {{ $comprobante->id_interaction }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bolder fs-5">${{ number_format($comprobante->monto_pagado, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        {{-- Intento de formatear la fecha entera (ej: 20260414) a algo legible --}}
                                        @php
                                            $fechaStr = (string)$comprobante->fecha_pago;
                                            $fechaFormateada = strlen($fechaStr) == 8 
                                                ? \Carbon\Carbon::createFromFormat('Ymd', $fechaStr)->format('d M Y') 
                                                : $fechaStr;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-calendar-alt text-gray-400 me-2"></i>
                                            <span class="text-gray-800 fw-bold">{{ $fechaFormateada }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($comprobante->estado == 'conciliado')
                                            <span class="badge badge-light-success fw-bolder px-3 py-2">Conciliado</span>
                                        @elseif($comprobante->estado == 'rechazado')
                                            <span class="badge badge-light-danger fw-bolder px-3 py-2">Rechazado</span>
                                        @else
                                            <span class="badge badge-light-warning fw-bolder px-3 py-2">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $comprobante->url_archivo }}" target="_blank" class="btn btn-sm btn-light btn-active-light-primary px-3 py-2 shadow-xs d-inline-flex align-items-center">
                                            <i class="fas fa-file-pdf text-danger me-2"></i> Ver Soporte
                                        </a>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('cartera.comprobantes.show', $comprobante->id) }}" class="btn btn-sm btn-icon btn-light-info btn-active-info shadow-xs me-1" title="Revisar y Conciliar">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <form action="{{ route('cartera.comprobantes.destroy', $comprobante->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-active-danger shadow-xs btn-delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-15">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-inbox text-gray-300 fa-4x mb-4"></i>
                                            <h4 class="text-dark fw-bolder">Bandeja Vacía</h4>
                                            <span class="text-muted fs-6">No se encontraron comprobantes con los filtros actuales.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación de Laravel (Crucial para rendimiento) --}}
                    @if($comprobantes->hasPages())
                        <div class="card-footer bg-white border-top border-light p-4 d-flex justify-content-center">
                            {{ $comprobantes->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar de Acciones --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm bg-white p-5 mb-4 position-sticky" style="top: 2rem; border-radius: 12px;">
                    <a href="{{ route('cartera.comprobantes.create') }}" class="btn btn-primary w-100 rounded-pill fs-5 fw-bolder py-3 shadow-sm mb-4">
                        <i class="fas fa-cloud-upload-alt me-2"></i> Subir Soporte
                    </a>
                    
                    <div class="separator separator-dashed my-4 border-gray-300"></div>

                    <div class="d-flex flex-column gap-2">
                        <h6 class="text-uppercase text-muted fw-bolder ls-1 fs-8 mb-3">Herramientas</h6>
                        <button onclick="window.print()" class="btn btn-clean btn-active-light-primary text-start fs-6 rounded ps-4 py-3 text-dark fw-bold">
                            <i class="fas fa-print me-3 text-gray-500"></i> Imprimir Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .transition-3ms { transition: all 0.2s ease-in-out; }
        .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important; }
        .ls-1 { letter-spacing: 0.05em; }
        .btn-clean { background-color: transparent; border: none; color: #3f4254; }
        .btn-clean:hover { color: #181C32; background-color: #f3f6f9; }
        .table-hover tbody tr:hover { background-color: #f8f9fa !important; }
        
        /* Estilos para los tabs */
        .nav-pills-custom .nav-link { font-weight: 600; color: #7e8299; transition: all 0.3s ease; }
        .nav-pills-custom .nav-link.active { color: #fff; }
        .badge { letter-spacing: 0.5px; }
    </style>
    <script>
        // El buscador por teclado de JS fue eliminado a favor del buscador Backend 
        // para asegurar que escanee toda la base de datos y no solo la primera página.

        $(document).on('click', '.btn-delete', function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: '¿Confirmar eliminación?',
                text: 'El archivo físico en S3 también será borrado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d9214e',
                cancelButtonColor: '#f1f1f4',
                confirmButtonText: 'Sí, eliminar permanentemente',
                cancelButtonText: '<span style="color:#7e8299">Cancelar</span>',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fs-6',
                    cancelButton: 'btn btn-light rounded-pill px-4 py-2 fs-6'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    </script>
    @endpush
</x-base-layout>