<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Listado de Soportes</h5>
                <a href="{{ route('soportes.soportes.create') }}" class="btn btn-success btnCrear">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nuevo Soporte</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo / Sub-Tipo</th>
                            <th>Detalles</th>
                            <th>Prioridad</th>
                            <th>Estado Actual</th>
                            <th>Creado Por</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soportes as $soporte)
                            <tr>
                                {{-- Celda ID con atributos para el modal --}}
                                <td>
                                    <a href="javascript:void(0)" 
                                       class="soporte-id fw-bold text-decoration-underline"
                                       data-id="{{ $soporte->id }}"
                                       data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                       data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                       data-detalles="{{ $soporte->detalles_soporte }}"
                                       data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                       data-estado="{{ $soporte->estadoActual->nombre ?? 'Pendiente' }}"
                                       data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                       data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}">
                                        {{ $soporte->id }}
                                    </a>
                                </td>
                                <td>
                                    {{ $soporte->tipo->nombre ?? 'N/A' }}
                                    @if ($soporte->subTipo)
                                        <br><small class="text-muted">{{ $soporte->subTipo->nombre }}</small>
                                    @endif
                                </td>
                                {{-- Tooltip para detalles --}}
                                <td title="{{ $soporte->detalles_soporte }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip">
                                    {{ Str::limit($soporte->detalles_soporte, 50) }}
                                </td>
                                <td>{{ $soporte->prioridad->nombre ?? 'N/A' }}</td>
                                @php
                                    $ultimaObservacion = $soporte->observaciones->first();
                                @endphp

                                <td>
                                    {{ $ultimaObservacion?->estado?->nombre ?? 'Pendiente' }}
                                </td>

                                <td>
                                    {{ $ultimaObservacion?->scpUsuarioAsignado?->maeTercero->nom_ter ?? 'Sin asignar' }}
                                </td>

                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('soportes.soportes.show', $soporte) }}">
                                                    <i class="feather-eye me-3"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('soportes.soportes.edit', $soporte) }}">
                                                    <i class="feather-edit-3 me-3"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('soportes.soportes.destroy', $soporte) }}" method="POST" class="formEliminar">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item btnEliminar">
                                                        <i class="feather-trash-2 me-3"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay soportes para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2">
                    {{ $soportes->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal dinámico --}}
    <div class="modal fade" id="detalleSoporteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Detalles del Soporte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9" id="modal-id"></dd>

                        <dt class="col-sm-3">Tipo</dt>
                        <dd class="col-sm-9" id="modal-tipo"></dd>

                        <dt class="col-sm-3">Sub-Tipo</dt>
                        <dd class="col-sm-9" id="modal-subtipo"></dd>

                        <dt class="col-sm-3">Detalles</dt>
                        <dd class="col-sm-9" id="modal-detalles"></dd>

                        <dt class="col-sm-3">Prioridad</dt>
                        <dd class="col-sm-9" id="modal-prioridad"></dd>

                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9" id="modal-estado"></dd>

                        <dt class="col-sm-3">Asignado</dt>
                        <dd class="col-sm-9" id="modal-creado"></dd>

                        <dt class="col-sm-3">Fecha Creación</dt>
                        <dd class="col-sm-9" id="modal-fecha"></dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Tooltip gris opaco */
        .custom-tooltip .tooltip-inner {
            background-color: rgba(108, 117, 125, 0.7);
            color: #fff;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.85rem;
            max-width: 300px;
            word-wrap: break-word;
        }
    </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function (el) {
                    return new bootstrap.Tooltip(el)
                });

                // Modal dinámico
                document.querySelectorAll('.soporte-id').forEach(el => {
                    el.addEventListener('click', function () {
                        document.getElementById('modal-id').textContent = this.dataset.id;
                        document.getElementById('modal-tipo').textContent = this.dataset.tipo;
                        document.getElementById('modal-subtipo').textContent = this.dataset.subtipo;
                        document.getElementById('modal-detalles').textContent = this.dataset.detalles;
                        document.getElementById('modal-prioridad').textContent = this.dataset.prioridad;
                        document.getElementById('modal-estado').textContent = this.dataset.estado;
                        document.getElementById('modal-creado').textContent = this.dataset.creado;
                        document.getElementById('modal-fecha').textContent = this.dataset.fecha;
                        new bootstrap.Modal(document.getElementById('detalleSoporteModal')).show();
                    });
                });

                // SweetAlert confirmaciones
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Crear un nuevo Soporte?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Editar este Soporte?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar este Soporte?', 'Esta acción eliminará el soporte y todas sus observaciones relacionadas. Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
