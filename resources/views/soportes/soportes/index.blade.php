<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
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

            {{-- Tabs por categoría --}}
            <ul class="nav nav-tabs mb-3" id="soporteTabs" role="tablist">
                @foreach ($categorias as $nombreCategoria => <<<<<<< HEAD
                    <li class="nav-item" role="presentation">
=======
                    @php 
                        $permiso = str_replace(' ', '', strtolower($nombreCategoria));     
                    @endphp
                    @can('soporte.lista.' . $permiso)
                    <li class="nav-item" role="presentation">                    
>>>>>>> origin/vanessa
                        <button class="nav-link @if($loop->first) active @endif"
                                id="tab-{{ $loop->index }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-{{ $loop->index }}"
                                type="button"
                                role="tab"
                                aria-controls="tab-{{ $loop->index }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $nombreCategoria }}
                            <span class="badge bg-secondary">{{ $soportesCategoria->count() }}</span>
                        </button>
                    </li> 
                    @endcan                   
foreach
            </ul>

            <div class="tab-content" id="soporteTabsContent">
                @foreach ($categorias as $nombreCategoria => $soportesCategoria)
                @php 
                    $permiso = str_replace(' ', '', strtolower($nombreCategoria));     
                @endphp
                    @can('soporte.lista.' . $permiso)
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="tab-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-{{ $loop->index }}-tab">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle small" style="font-size: 0.875rem;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha Creado</th>
                                        <th>Creado Por</th>
                                        <th>Área</th>
                                        <th>Módulo</th>
                                        <th>Prioridad</th>
                                        <th>Descripción</th>
                                        <th>Fecha Update</th>
                                        <th>Asignado</th>
                                        <th>Escalado</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soportesCategoria as $soporte)
                                        @php
                                            $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0)"
                                                   class="soporte-id fw-bold text-decoration-underline"
                                                   data-id="{{ $soporte->id }}"
                                                   data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                                   data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                                   data-area="{{ $areaModal }}"
                                                   data-subtipo="{{ $soporte->subTipo->nombre ?? '' }}"
                                                   data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                                   data-detalles="{{ $soporte->detalles_soporte }}"
                                                   data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                                   data-asignado="{{ $soporte->maeTercero->nom_ter ?? 'Sin asignar' }}"
                                                   data-escalado="{{ $soporte->usuario_escalado ?? 'Sin escalar' }}"
                                                   data-estado="{{ $soporte->estado ?? 'Pendiente' }}">
                                                    {{ $soporte->id }}
                                                </a>
                                            </td>
                                            <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>
                                            <td>{{ $soporte->cargo->gdoArea->nombre ?? 'Soporte' }}</td>
                                            <td>{{ $soporte->subTipo->nombre ?? '' }}</td>
                                            <td>{{ $soporte->prioridad->nombre ?? 'N/A' }}</td>
                                            <td title="{{ $soporte->detalles_soporte }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip">
                                                {{ Str::limit($soporte->detalles_soporte, 50) }}
                                            </td>
                                            <td>{{ $soporte->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $soporte->maeTercero->nom_ter ?? 'Sin asignar' }}</td>
                                            <td>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}</td>
                                            <td>{{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}</td>
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
                                            <td colspan="12" class="text-center">No hay soportes en esta categoría.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endcan
                      </tbody>
                            </table>
                        </div>
                    </div>
  </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
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
                        <dt class="col-sm-3">Ticket</dt>
                        <dd class="col-sm-9" id="modal-id"></dd>
                        <dt class="col-sm-3">Fecha Creado</dt>
                        <dd class="col-sm-9" id="modal-fecha"></dd>
                        <dt class="col-sm-3">Creado Por</dt>
                        <dd class="col-sm-9" id="modal-creado"></dd>
                        <dt class="col-sm-3">Área</dt>
                        <dd class="col-sm-9" id="modal-area"></dd>
                        <dt class="col-sm-3">Módulo</dt>
                        <dd class="col-sm-9" id="modal-tipo-subtipo"></dd>
                        <dt class="col-sm-3">Prioridad</dt>
                        <dd class="col-sm-9" id="modal-prioridad"></dd>
                        <dt class="col-sm-3">Descripción</dt>
                        <dd class="col-sm-9" id="modal-detalles"></dd>
                        <dt class="col-sm-3">Fecha Update</dt>
                        <dd class="col-sm-9" id="modal-fecha-update"></dd>
                        <dt class="col-sm-3">Asignado</dt>
                        <dd class="col-sm-9" id="modal-asignado"></dd>
                        <dt class="col-sm-3">Escalado</dt>
                        <dd class="col-sm-9" id="modal-escalado"></dd>
                        <dt class="col-sm-3">Estado</dt>
                        <dd class="col-sm-9" id="modal-estado"></dd>
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
                // Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

                // Modal dinámico
                document.querySelectorAll('.soporte-id').forEach(el => {
                    el.addEventListener('click', function () {
                        document.getElementById('modal-id').textContent = this.dataset.id;
                        document.getElementById('modal-fecha').textContent = this.dataset.fecha;
                        document.getElementById('modal-creado').textContent = this.dataset.creado;
                        document.getElementById('modal-area').textContent = this.dataset.area ?? 'Soporte';
                        document.getElementById('modal-tipo-subtipo').textContent = this.dataset.subtipo ?? 'N/A';
                        document.getElementById('modal-prioridad').textContent = this.dataset.prioridad;
                        document.getElementById('modal-detalles').textContent = this.dataset.detalles;
                        document.getElementById('modal-fecha-update').textContent = this.dataset.updated ?? this.dataset.fecha;
                        document.getElementById('modal-asignado').textContent = this.dataset.asignado ?? 'Sin asignar';
                        document.getElementById('modal-escalado').textContent = this.dataset.escalado ?? 'Sin escalar';
                        document.getElementById('modal-estado').textContent = this.dataset.estado;
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
                    }).then((result) => { if(result.isConfirmed) callback(); });
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
