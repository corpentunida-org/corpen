<x-base-layout>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            :root {
                --glass-bg: rgba(255, 255, 255, 0.9);
                --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            }

            /* Estética de Tarjetas */
            .card-modern {
                border: none;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
                transition: transform 0.3s ease;
            }

            /* Tabla Estilizada */
            .table-modern thead th {
                background-color: #f8fafc;
                text-transform: uppercase;
                font-size: 0.7rem;
                letter-spacing: 0.05em;
                color: #64748b;
                border-top: none;
                padding: 1.25rem 1rem;
            }

            .table-modern tbody tr {
                transition: all 0.2s ease;
            }

            .table-modern tbody tr:hover {
                background-color: #f1f5f9 !important;
                transform: scale(1.002);
            }

            /* Badges Personalizados */
            .badge-dot {
                display: inline-flex;
                align-items: center;
                padding: 0.5em 1em;
                font-weight: 500;
                border-radius: 30px;
            }

            .dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }

            /* Acciones */
            .action-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #64748b;
                background: #f8fafc;
                transition: all 0.2s ease;
                border: 1px solid #e2e8f0;
            }

            .action-icon:hover {
                background: #fff;
                color: #4338ca;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                border-color: #4338ca;
            }

            .btn-create {
                background: var(--primary-gradient);
                color: white;
                border: none;
                box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            }

            /* Animaciones */
            .animate-in { animation: fadeInUp 0.5s ease backwards; }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endpush

    {{-- HEADER CON ESTADÍSTICAS RÁPIDAS --}}
    <div class="row mb-4 align-items-end animate-in">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Estructura Organizacional</h2>
            <p class="text-muted mb-0">Gestiona las áreas y departamentos de la compañía.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('archivo.area.create') }}" class="btn btn-create px-4 py-2 rounded-4 fw-bold swal-confirm" 
               data-swal-title="Nueva Área">
                <i class="bi bi-plus-circle-fill me-2"></i>Registrar Área
            </a>
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA TIPO NAVEGACIÓN --}}
    <div class="card card-modern mb-4 animate-in" style="animation-delay: 0.1s;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('archivo.area.index') }}" class="row g-2">
                <div class="col-md-8">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control bg-light border-0 ps-0" 
                               placeholder="Filtrar por nombre o descripción..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="text-muted small">Total: <strong>{{ $areas->total() }}</strong> Áreas</span>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO DE ÁREAS --}}
    <div class="card card-modern animate-in" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Información del Área</th>
                        <th>Jefe de Área</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-area bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="bi bi-building fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $area->nombre }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 300px;">
                                            {{ $area->descripcion ?? 'Sin descripción disponible' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($area->jefeCargo)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-badge me-2 text-indigo"></i>
                                        <span class="small fw-medium">{{ $area->jefeCargo->nombre_cargo }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted fw-normal border">No definido</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($area->estado === 'activo')
                                    <span class="badge-dot bg-success-subtle text-success">
                                        <span class="dot bg-success"></span>Activo
                                    </span>
                                @else
                                    <span class="badge-dot bg-danger-subtle text-danger">
                                        <span class="dot bg-danger"></span>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('archivo.area.show', $area->id) }}" class="action-icon" data-bs-toggle="tooltip" title="Ver ficha">
                                        <i class="bi bi-layout-text-sidebar-reverse"></i>
                                    </a>
                                    <a href="{{ route('archivo.area.edit', $area->id) }}" class="action-icon swal-confirm" 
                                       data-bs-toggle="tooltip" title="Editar área" 
                                       data-swal-title="¿Editar departamento?">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <!-- 
                                    <form action="{{ route('archivo.area.destroy', $area->id) }}" method="POST" class="d-inline swal-confirm-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-icon text-danger border-danger-subtle" 
                                                data-bs-toggle="tooltip" title="Eliminar registro">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                     -->
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3" alt="Sin datos">
                                <h5 class="text-muted fw-bold">El organigrama está vacío</h5>
                                <p class="small text-muted">No se encontraron áreas que coincidan con los criterios.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($areas->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">Mostrando registros del {{ $areas->firstItem() }} al {{ $areas->lastItem() }}</p>
                    {{ $areas->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- SCRIPTS SE MANTIENEN IGUAL --}}
</x-base-layout>