<x-base-layout>

    {{-- 1. SECCIÓN DE ESTILOS Y LIBRERÍAS PROFESIONALES --}}
    @push('styles')
        {{-- Toastr para notificaciones elegantes --}}
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
                font-size: 0.9rem;
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
            input[type="search"]::-webkit-search-decoration,
            input[type="search"]::-webkit-search-cancel-button,
            input[type="search"]::-webkit-search-results-button,
            input[type="search"]::-webkit-search-results-decoration {
                -webkit-appearance:none;
            }
        </style>
    @endpush

    {{-- 2. PANEL DE CONTROL --}}
    <div class="card card-friendly animate-on-load mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">
                {{-- Título --}}
                <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-folder2-open fs-2 text-primary me-3"></i>
                        <div>
                            <h4 class="fw-bold mb-0">Documentos de Empleados</h4>
                            <small class="text-muted">Gestiona, busca y administra los registros.</small>
                        </div>
                    </div>
                </div>
                {{-- Buscador --}}
                <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('archivo.gdodocsempleados.index') }}">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Buscar por empleado..." value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
                {{-- Botón de Crear --}}
                <div class="col-lg-3 col-md-6 text-md-end">
                    <a href="{{ route('archivo.gdodocsempleados.create') }}" 
                       class="btn btn-primary rounded-pill px-4 py-2 w-100 w-md-auto btn-hover-lift swal-confirm"
                       data-swal-title="¿Crear un nuevo documento?"
                       data-swal-text="Serás redirigido al formulario de creación."
                       data-swal-icon="info">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Documento
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
                            <th class="py-3 px-4">Tipo de Documento</th>
                            <th class="py-3 px-4">Fecha de Subida</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gdodocsempleados as $doc)
                            <tr>
                                {{-- Celda de Empleado con avatar --}}
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials me-3">
                                            {{ strtoupper(substr($doc->empleado->nombre1 ?? '?', 0, 1) . substr($doc->empleado->apellido1 ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $doc->empleado->nombre_completo ?? 'Empleado no encontrado' }}</div>
                                            <small class="text-muted">{{ $doc->empleado->cedula ?? 'Sin Cédula' }}</small>
                                        </div>
                                    </div>
                                </td>
                                {{-- Celda de Tipo de Documento --}}
                                <td class="px-4">{{ $doc->tipoDocumento->nombre ?? '—' }}</td>
                                {{-- Celda de Fecha --}}
                                <td class="px-4">{{ $doc->fecha_subida ? $doc->fecha_subida->format('d M, Y') : '—' }}</td>
                                {{-- Celda de Acciones con menú desplegable --}}
                                <td class="text-center px-4">
                                    <div class="btn-group">
                                        <a href="{{ route('gdodocsempleados.ver', $doc->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Ver Archivo"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('gdodocsempleados.download', $doc->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Descargar"><i class="bi bi-download"></i></a>
                                        <a href="{{ route('archivo.gdodocsempleados.edit', $doc->id) }}" 
                                           class="btn btn-sm btn-outline-secondary swal-confirm" 
                                           data-bs-toggle="tooltip" title="Editar"
                                           data-swal-title="¿Editar este registro?"
                                           data-swal-icon="question">
                                           <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('archivo.gdodocsempleados.destroy', $doc->id) }}" method="POST" class="d-inline swal-confirm-form">
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
                                <td colspan="5">
                                    <div class="text-center py-5 px-4">
                                        <i class="bi bi-cloud-drizzle display-1 text-muted mb-3"></i>
                                        <h4 class="fw-bold">No se encontraron resultados</h4>
                                        <p class="text-muted">Intenta ajustar tu búsqueda o crea el primer documento.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- 5. PAGINACIÓN --}}
            @if ($gdodocsempleados->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $gdodocsempleados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        {{-- Librerías requeridas --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- 6. JAVASCRIPT REFACTORIZADO Y PROFESIONAL --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Configuración global de Toastr
                toastr.options = {
                    "closeButton": true, "progressBar": true, "positionClass": "toast-bottom-right",
                };

                // Disparar Toastr si hay un mensaje de éxito en la sesión
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif
                
                // Inicializar todos los tooltips de la página
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
                });
                
                // Delegación de eventos para todas las confirmaciones con SweetAlert
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
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = swalConfirm.getAttribute('href');
                            }
                        });
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
                        }).then((result) => {
                            if (result.isConfirmed) {
                                swalConfirmForm.submit();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>