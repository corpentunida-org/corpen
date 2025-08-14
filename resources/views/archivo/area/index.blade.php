<x-base-layout>

    {{-- 1. SECCIÓN DE ESTILOS Y LIBRERÍAS --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            .table-actions .btn { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; }
            input[type="search"]::-webkit-search-cancel-button { -webkit-appearance:none; }
            .description-cell { max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        </style>
    @endpush

    {{-- 2. PANEL DE CONTROL --}}
    <div class="card card-friendly animate-on-load mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-diagram-3 fs-2 text-primary me-3"></i>
                        <div>
                            <h4 class="fw-bold mb-0">Gestión de Áreas</h4>
                            <small class="text-muted">Administra las áreas funcionales.</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('archivo.area.index') }}">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Buscar por nombre de área..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3 col-md-6 text-md-end">
                    <a href="{{ route('archivo.area.create') }}" 
                       class="btn btn-primary rounded-pill px-4 py-2 w-100 w-md-auto btn-hover-lift swal-confirm"
                       data-swal-title="¿Crear una nueva área?"
                       data-swal-text="Serás redirigido al formulario de creación."
                       data-swal-icon="info">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Área
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. ZONA DE DATOS --}}
    <div class="card card-friendly animate-on-load" style="animation-delay: 0.1s;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">Nombre del Área</th>
                            <th class="py-3 px-4">Descripción</th>
                            <th class="py-3 px-4">Jefe de Área</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $area)
                            <tr>
                                <td class="px-4"><span class="fw-bold">{{ $area->nombre }}</span></td>
                                <td class="px-4">
                                    <div class="description-cell" data-bs-toggle="tooltip" title="{{ $area->descripcion }}">
                                        {{ $area->descripcion ?? '—' }}
                                    </div>
                                </td>
                                
                                {{-- =============================================== --}}
                                {{--           CELDA CORREGIDA AQUÍ           --}}
                                {{-- =============================================== --}}
                                <td class="px-4">
                                    @if ($area->jefeCargo)
                                        {{ $area->jefeCargo->nombre_cargo }}
                                    @else
                                        <span class="text-muted fst-italic">Sin asignar</span>
                                    @endif
                                </td>
                                
                                <td class="text-center px-4">
                                    @if ($area->estado === 'activo')
                                        <span class="badge rounded-pill bg-success">Activo</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center px-4 table-actions">
                                    <div class="btn-group">
                                        <a href="{{ route('archivo.area.show', $area->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Ver Detalles"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('archivo.area.edit', $area->id) }}" 
                                           class="btn btn-sm btn-outline-secondary swal-confirm" 
                                           data-bs-toggle="tooltip" title="Editar"
                                           data-swal-title="¿Editar esta área?"
                                           data-swal-icon="question">
                                           <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('archivo.area.destroy', $area->id) }}" method="POST" class="d-inline swal-confirm-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="tooltip" title="Eliminar"
                                                    data-swal-title="¿Estás seguro de eliminar?"
                                                    data-swal-text="Esta acción no se puede deshacer."
                                                    data-swal-icon="warning">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5 px-4">
                                        <i class="bi bi-building-slash display-1 text-muted mb-3"></i>
                                        <h4 class="fw-bold">No se encontraron áreas</h4>
                                        <p class="text-muted">Intenta ajustar tu búsqueda o crea la primera área funcional.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($areas->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $areas->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // El JavaScript no necesita cambios, se mantiene intacto y funcional.
            document.addEventListener('DOMContentLoaded', function () {
                toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right", };
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
                
                document.body.addEventListener('click', function(e) {
                    const swalConfirm = e.target.closest('.swal-confirm');
                    if (swalConfirm) {
                        e.preventDefault();
                        const title = swalConfirm.dataset.swalTitle || '¿Estás seguro?';
                        const text = swalConfirm.dataset.swalText || '';
                        const icon = swalConfirm.dataset.swalIcon || 'question';
                        Swal.fire({
                            title: title, text: text, icon: icon, showCancelButton: true,
                            confirmButtonColor: '#0d6efd', cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, continuar', cancelButtonText: 'Cancelar',
                        }).then((result) => { if (result.isConfirmed) { window.location.href = swalConfirm.getAttribute('href'); } });
                    }
                });

                document.body.addEventListener('submit', function(e) {
                    const swalConfirmForm = e.target.closest('.swal-confirm-form');
                    if (swalConfirmForm) {
                        e.preventDefault();
                        const submitButton = swalConfirmForm.querySelector('[type="submit"]');
                        const title = submitButton.dataset.swalTitle || '¿Estás seguro?';
                        const text = submitButton.dataset.swalText || '';
                        const icon = submitButton.dataset.swalIcon || 'warning';
                        Swal.fire({
                            title: title, text: text, icon: icon, showCancelButton: true,
                            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                        }).then((result) => { if (result.isConfirmed) { swalConfirmForm.submit(); } });
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>