<x-base-layout>

    {{-- 1. SECCIÓN DE ESTILOS Y LIBRERÍAS --}}
    @push('styles')
        {{-- Toastr para notificaciones elegantes --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <style>
            /* Estilo para consistencia en los botones de la tabla */
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

    {{-- 2. PANEL DE CONTROL (Título, Búsqueda y Acción Principal) --}}
    <div class="card card-friendly animate-on-load mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">
                {{-- Título --}}
                <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-tags fs-2 text-primary me-3"></i>
                        <div>
                            <h4 class="fw-bold mb-0">Tipos de Documento</h4>
                            <small class="text-muted">Administra las categorías de documentos.</small>
                        </div>
                    </div>
                </div>
                {{-- Buscador --}}
                <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                    <form method="GET" action="{{ route('archivo.gdotipodocumento.index') }}">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
                {{-- Botón de Crear --}}
                <div class="col-lg-3 col-md-6 text-md-end">
                    <a href="{{ route('archivo.gdotipodocumento.create') }}" 
                       class="btn btn-primary rounded-pill px-4 py-2 w-100 w-md-auto btn-hover-lift swal-confirm"
                       data-swal-title="¿Crear un nuevo tipo?"
                       data-swal-text="Serás redirigido al formulario de creación."
                       data-swal-icon="info">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Tipo
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
                            <th class="py-3 px-4">Nombre del Tipo</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr>
                                {{-- Celda de Nombre, más visual --}}
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text text-primary me-3"></i>
                                        <span class="fw-bold">{{ $tipo->nombre }}</span>
                                    </div>
                                </td>
                                {{-- Celda de Acciones, más directa y limpia --}}
                                <td class="text-center px-4 table-actions">
                                    <div class="btn-group">
                                        <a href="{{ route('archivo.gdotipodocumento.show', $tipo->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Ver Detalles"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('archivo.gdotipodocumento.edit', $tipo->id) }}" 
                                           class="btn btn-sm btn-outline-secondary swal-confirm" 
                                           data-bs-toggle="tooltip" title="Editar"
                                           data-swal-title="¿Editar este tipo?"
                                           data-swal-icon="question">
                                           <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('archivo.gdotipodocumento.destroy', $tipo->id) }}" method="POST" class="d-inline swal-confirm-form">
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
                                <td colspan="2">
                                    <div class="text-center py-5 px-4">
                                        <i class="bi bi-journal-x display-1 text-muted mb-3"></i>
                                        <h4 class="fw-bold">No se encontraron tipos de documento</h4>
                                        <p class="text-muted">Intenta ajustar tu búsqueda o crea el primer tipo de documento.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- 5. PAGINACIÓN --}}
            @if ($tipos->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $tipos->appends(request()->query())->links() }}
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
                
                // Delegación de eventos para confirmaciones de enlaces
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

                // Delegación de eventos para confirmaciones de formularios (Eliminar)
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