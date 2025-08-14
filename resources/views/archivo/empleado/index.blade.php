<x-base-layout>

    {{-- 1. SECCIÓN DE ESTILOS Y LIBRERÍAS --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            /* Estilo para avatares con iniciales */
            .avatar-initials {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: var(--bs-primary-bg-subtle);
                color: var(--bs-primary-text-emphasis);
                font-weight: 600;
                font-size: 1rem;
            }
            /* Consistencia en los botones de la tabla */
            .table-actions .btn {
                width: 35px;
                height: 35px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            /* Ocultar el spinner de los inputs de tipo search */
            input[type="search"]::-webkit-search-cancel-button { -webkit-appearance:none; }
        </style>
    @endpush

    {{-- 2. PANEL DE CONTROL (Título, Búsqueda y Acción Principal) --}}
    <div class="card card-friendly animate-on-load mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people-fill fs-2 text-primary me-3"></i>
                        <div>
                            <h4 class="fw-bold mb-0">Gestión de Empleados</h4>
                            <small class="text-muted">Administra el personal registrado.</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('archivo.empleado.index') }}">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Buscar por cédula o nombre..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3 col-md-6 text-md-end">
                    <a href="{{ route('archivo.empleado.create') }}" 
                       class="btn btn-primary rounded-pill px-4 py-2 w-100 w-md-auto btn-hover-lift swal-confirm"
                       data-swal-title="¿Registrar nuevo empleado?"
                       data-swal-text="Serás redirigido al formulario de creación."
                       data-swal-icon="info">
                        <i class="bi bi-person-plus-fill me-1"></i> Nuevo Empleado
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
                            <th class="py-3 px-4">Empleado</th>
                            <th class="py-3 px-4">Contacto</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empleados as $empleado)
                            <tr>
                                {{-- Celda de Empleado con avatar y nombre completo --}}
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials me-3">
                                            {{ strtoupper(substr($empleado->nombre1 ?? '?', 0, 1) . substr($empleado->apellido1 ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $empleado->nombre_completo }}</div>
                                            <small class="text-muted">C.C. {{ $empleado->cedula }}</small>
                                        </div>
                                    </div>
                                </td>
                                {{-- Celda de Contacto con iconos --}}
                                <td class="px-4">
                                    <div><i class="bi bi-envelope-fill text-muted me-2"></i>{{ $empleado->correo_personal ?? '—' }}</div>
                                    <div><i class="bi bi-phone-fill text-muted me-2"></i>{{ $empleado->celular_personal ?? '—' }}</div>
                                </td>
                                {{-- Celda de Acciones --}}
                                <td class="text-center px-4 table-actions">
                                    <div class="btn-group">
                                        <a href="{{ route('archivo.empleado.show', $empleado->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Ver Detalles"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('archivo.empleado.edit', $empleado->id) }}" 
                                           class="btn btn-sm btn-outline-secondary swal-confirm" 
                                           data-bs-toggle="tooltip" title="Editar"
                                           data-swal-title="¿Editar este empleado?"
                                           data-swal-icon="question">
                                           <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('archivo.empleado.destroy', $empleado->id) }}" method="POST" class="d-inline swal-confirm-form">
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
                            {{-- 4. ESTADO VACÍO INTELIGENTE --}}
                            <tr>
                                <td colspan="3">
                                    <div class="text-center py-5 px-4">
                                        <i class="bi bi-person-fill-slash display-1 text-muted mb-3"></i>
                                        <h4 class="fw-bold">No se encontraron empleados</h4>
                                        <p class="text-muted">Intenta ajustar tu búsqueda o registra al primer empleado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- 5. PAGINACIÓN --}}
            @if ($empleados->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $empleados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        {{-- Librerías requeridas --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- 6. JAVASCRIPT REUTILIZABLE Y DECLARATIVO --}}
        <script>
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