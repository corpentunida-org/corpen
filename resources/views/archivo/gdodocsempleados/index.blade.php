<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            {{-- Encabezado y botón Crear --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Documentos de Empleados</h5>
                <a href="{{ route('archivo.gdodocsempleados.create') }}" class="btn btn-success btnCrear">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span>Nuevo Documento</span>
                </a>
            </div>

            {{-- Buscador --}}
            <div class="px-4 pb-4">
                <form method="GET" action="{{ route('archivo.gdodocsempleados.index') }}">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="search" name="search" class="form-control border-start-0" placeholder="Buscar por empleado o tipo documento..." value="{{ $search ?? '' }}">
                        <button class="btn btn-outline-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            {{-- Tabla de Documentos --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small">
                    <thead class="table-light">
                        <tr class="border-top">
                            <th>Empleado</th>
                            <th>Tipo Documento</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gdodocsempleados as $doc)
                            <tr>
                                <td>
                                    @if($doc->empleado)
                                        {{ $doc->empleado->apellido1 }} {{ $doc->empleado->apellido2 }} {{ $doc->empleado->nombre1 }} {{ $doc->empleado->nombre2 }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $doc->tipoDocumento->nombre ?? '—' }}</td>
                                <td>
                                    @if($doc->ruta_archivo)
                                        <a href="{{ route('gdodocsempleados.ver', $doc->id) }}" target="_blank" class="btn btn-sm btn-primary">Ver</a>
                                        <a href="{{ route('gdodocsempleados.download', $doc->id) }}" class="btn btn-sm btn-success">Descargar</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $doc->fecha_subida ? $doc->fecha_subida->format('d/m/Y') : '—' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('archivo.gdodocsempleados.edit', $doc->id) }}">
                                                    <i class="bi bi-pencil-square me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('archivo.gdodocsempleados.destroy', $doc->id) }}" method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash-fill me-3"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No se encontraron documentos de empleados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($gdodocsempleados->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $gdodocsempleados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                        showClass: { popup: 'animate__animated animate__fadeIn' },
                        hideClass: { popup: 'animate__animated animate__fadeOut' }
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Crear un nuevo documento?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Editar este documento?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este documento?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            });
        </script>
    @endpush
</x-base-layout>
