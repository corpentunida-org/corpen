<x-base-layout>

    {{-- 1. ESTILOS REFINADOS --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            :root { --card-border: #f0f0f2; --text-main: #2d3748; }
            .card-modern { border: 1px solid var(--card-border); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden; }
            .search-input { border-radius: 8px 0 0 8px !important; border: 1px solid #e2e8f0; border-right: none; }
            .search-btn { border-radius: 0 8px 8px 0 !important; border: 1px solid #e2e8f0; border-left: none; background: #fff; color: #a0aec0; }
            .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #718096; border-top: none; padding-top: 15px; padding-bottom: 15px; }
            .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
            .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
            .animate-fade-up { animation: fadeUp 0.4s ease-out forwards; }
            .avatar-circle { width: 32px; height: 32px; background-color: #f1f5f9; color: #475569; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; border: 1px solid #e2e8f0; }
            .btn-export { background-color: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; }
            .btn-export:hover { background-color: #f8fafc; color: #1e293b; }
            @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    @endpush

    {{-- 2. HEADER ESTRATÉGICO --}}
    <div class="row align-items-center mb-4 animate-fade-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Cargos</h3>
            <p class="text-muted small mb-0">Gestión de estructura organizacional y perfiles de rol.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
            {{-- BOTÓN EXPORTAR CSV --}}
            <a href="{{ route('archivo.cargo.export.csv', request()->query()) }}" class="btn btn-export px-3 shadow-sm d-inline-flex align-items-center">
                <i class="bi bi-download me-2"></i>Exportar CSV
            </a>
            <a href="{{ route('archivo.cargo.create') }}" class="btn btn-primary px-4 fw-600 shadow-sm border-0 d-inline-flex align-items-center" style="border-radius: 8px;">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Cargo
            </a>
        </div>
    </div>

    {{-- 3. FILTROS Y TABLA --}}
    <div class="card card-modern animate-fade-up" style="animation-delay: 0.1s;">
        <div class="card-header bg-white py-4 border-0">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('archivo.cargo.index') }}" id="search-form">
                        <div class="input-group" style="max-width: 450px;">
                            <input type="search" 
                                name="search" 
                                class="form-control search-input py-2 ps-3" 
                                placeholder="Buscar cargo, área o colaborador..." 
                                value="{{ request('search') }}">
                            <button class="btn search-btn px-3" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        
                        @if(request('search'))
                            <div class="mt-2">
                                <a href="{{ route('archivo.cargo.index') }}" class="text-decoration-none small text-danger fw-medium">
                                    <i class="bi bi-x-circle-fill me-1"></i>Limpiar búsqueda
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
                
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge bg-light text-dark border fw-normal p-2">
                        <span class="text-muted">Total:</span> {{ $cargos->total() }} cargos
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nombre y Detalle</th>
                        <th>Área Responsable</th>
                        <th>Colaborador Asignado</th>
                        <th class="text-center">Manual</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cargos as $cargo)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $cargo->nombre_cargo }}</div>
                                <div class="text-muted small d-flex align-items-center">
                                    <span class="badge bg-light text-muted border-0 p-0 me-2">ID-{{ str_pad($cargo->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    @if($cargo->telefono_corporativo)
                                        <span class="text-light me-2">|</span>
                                        <i class="bi bi-phone me-1"></i>{{ $cargo->telefono_corporativo }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-white text-dark fw-medium px-2 py-1 border shadow-sm" style="border-radius: 6px;">
                                    {{ $cargo->gdoArea->nombre ?? 'Sin área' }}
                                </span>
                            </td>
                            <td>
                                @if($cargo->empleado)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($cargo->empleado->nombre1, 0, 1)) }}{{ strtoupper(substr($cargo->empleado->apellido1, 0, 1)) }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="small fw-semibold text-dark" style="line-height: 1.2;">{{ $cargo->empleado->nombre_completo }}</span>
                                            <span class="text-muted" style="font-size: 0.7rem;">C.C. {{ $cargo->empleado->cedula }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center text-muted opacity-75">
                                        <div class="avatar-circle me-2 bg-light">
                                            <i class="bi bi-person text-muted" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <span class="small fst-italic">Vacante</span>
                                    </div>
                                @endif
                            </td>
                            {{-- COLUMNA MANUAL ACTUALIZADA PARA AWS S3 --}}
                            <td class="text-center">
                                @if ($cargo->manual_funciones)
                                    <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" 
                                    target="_blank" 
                                    class="text-primary btn-action btn-light border" 
                                    data-bs-toggle="tooltip" 
                                    title="Ver Manual de Funciones (AWS S3)">
                                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                    </a>
                                @else
                                    <span class="text-muted opacity-50" data-bs-toggle="tooltip" title="Sin manual asignado">
                                        <i class="bi bi-file-earmark-x fs-5"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($cargo->estado)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2" style="border-radius: 20px;">
                                        <span class="status-dot bg-success"></span>Activo
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2" style="border-radius: 20px;">
                                        <span class="status-dot bg-secondary"></span>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('archivo.cargo.show', $cargo->id) }}" class="btn btn-action btn-light border" title="Ver detalles"><i class="bi bi-eye text-primary"></i></a>
                                    <a href="{{ route('archivo.cargo.edit', $cargo->id) }}" class="btn btn-action btn-light border" title="Editar"><i class="bi bi-pencil text-dark"></i></a>
                                    <form action="{{ route('archivo.cargo.destroy', $cargo->id) }}" method="POST" class="d-inline swal-confirm-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-light border" title="Eliminar"><i class="bi bi-trash text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="opacity-25 mb-3" alt="Sin datos">
                                <h6 class="text-muted fw-normal">No se encontraron cargos registrados</h6>
                                <a href="{{ route('archivo.cargo.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Ver todos los registros</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($cargos->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Mostrando {{ $cargos->firstItem() }} a {{ $cargos->lastItem() }} de {{ $cargos->total() }} registros
                    </div>
                    <div>
                        {{ $cargos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Configuración Toastr
                toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right" };
                @if (session('success')) toastr.success("{{ session('success') }}"); @endif
                @if (session('error')) toastr.error("{{ session('error') }}"); @endif

                // Inicializar Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });

                // Confirmación de eliminación
                $('.swal-confirm-form').on('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>