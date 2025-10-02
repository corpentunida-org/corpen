<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0">Listado de Soportes</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('soportes.soportes.create') }}" class="btn btn-success btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nuevo Soporte</span>
                    </a>
                </div>
            </div>
            {{-- Tabs por categoría --}}
            <ul class="nav nav-tabs mb-3" id="soporteTabs" role="tablist">
                @candirect('soporte.lista.todo')    
                <li class="nav-item" role="presentation">                  
                        <button class="nav-link"
                                id="tab--tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-1"
                                type="button"
                                role="tab"
                                aria-controls="tab-1"
                                aria-selected="false">
                            TODOS
                            <span class="badge bg-secondary">{{ $soportes->count() }}</span>
                        </button>
                    </li>  
                    @endcandirect
               @foreach ($categorias as $nombreCategoria => $soportesCategoria)
                    @php
                        $indice = $loop->index + 2; // aquí forzamos que empiece desde 2
                        $permiso = 'soporte.lista.' . strtolower($nombreCategoria);
                    @endphp
                    @candirect($permiso)   
                    <li class="nav-item" role="presentation">                  
                        <button class="nav-link @if($loop->first) active @endif"
                                id="tab-{{ $indice }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-{{ $indice }}"
                                type="button"
                                role="tab"
                                aria-controls="tab-{{ $indice }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $nombreCategoria }}
                            <span class="badge bg-secondary">{{ $soportesCategoria->count() }}</span>
                        </button>
                    </li> 
                    @endcandirect
                @endforeach

            </ul>
            <div class="tab-content" id="soporteTabsContent">
                <div class="tab-pane fade"
                         id="tab-1" 
                         role="tabpanel" 
                         aria-labelledby="tab-1-tab">
                         {{-- 
                         ---
                         --}}
                         <table class="table table-hover table-bordered align-middle small soporteTable" 
                                   style="font-size: 0.875rem; width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha Creado</th>
                                        <th>Creado Por</th>
                                        <th>Área</th>
                                        <th>Categoria</th>
                                        <th>Tipo</th>
                                        <th>Modulo</th> {{-- SubTipo --}}
                                        <th>Prioridad</th>
                                        <th>Descripción</th>
                                        <th>Fecha Update</th>
                                        {{--<th>JEFE TI</th>  Asignado Inicialmente siempre Jefe --}}
                                        <th>Asignado</th> {{--  --}}
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soportes as $soporte)
                                        @php
                                            $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                            $prioridadColor = match($soporte->prioridad->nombre ?? '') {
                                                'Alta' => 'danger',
                                                'Media' => 'warning',
                                                'Baja' => 'success',
                                                default => 'secondary'
                                            };
                                            $estadoColor = match($soporte->estadoSoporte->nombre ?? '') {
                                                'Pendiente' => 'warning',
                                                'En Proceso' => 'info',
                                                'Cerrado' => 'success',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        @if(auth()->user()->id == $soporte->usuario->id) 
                                        <tr>
                                            {{-- ID --}}
                                            <td>
                                                <a href="javascript:void(0)"
                                                class="soporte-id fw-bold text-decoration-underline"
                                                data-id="{{ $soporte->id }}"
                                                data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                                data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                                data-area="{{ $areaModal }}"
                                                data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                                data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                                data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                data-modulo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                                data-detalles="{{ $soporte->detalles_soporte }}"
                                                data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                                data-escalado="{{ $soporte->usuario_escalado ?? 'Sin escalar' }}"
                                                data-estado="{{ $soporte->estado ?? 'Pendiente' }}">
                                                    {{ $soporte->id }}
                                                </a>
                                            </td>

                                            {{-- Fecha Creado --}}
                                            <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>

                                            {{-- Creado Por --}}
                                            <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>

                                            {{-- Área --}}
                                            <td>{{ $areaModal }}</td>

                                            {{-- Categoría --}}
                                            <td>{{ $soporte->categoria->nombre ?? 'N/A' }}</td>

                                            {{-- Tipo --}}
                                            <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                            {{-- SubTipo --}}
                                            <td>{{ $soporte->subTipo->nombre ?? 'N/A' }}</td>

                                            {{-- Prioridad --}}
                                            <td>
                                                <span class="badge bg-{{ $prioridadColor }}">
                                                    {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Descripción --}}
                                            <td title="{{ $soporte->detalles_soporte }}" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                data-bs-custom-class="custom-tooltip">
                                                {{ Str::limit($soporte->detalles_soporte, 50) }}
                                            </td>

                                            {{-- Fecha Update --}}
                                            <td>{{ $soporte->updated_at->format('d/m/Y H:i') }}</td>

                                            {{-- Asignado --}}
                                            <td>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}</td>

                                            {{-- Estado --}}
                                            <td>
                                                <span class="badge bg-{{ $estadoColor }}">
                                                    {{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}
                                                </span>
                                            </td>

                                            {{-- Acciones --}}
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
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center">No hay soportes en esta categoría.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                </div>
                @foreach ($categorias as $nombreCategoria => $soportesCategoria)
                @php 
                    $permiso = 'soporte.lista.' . strtolower($nombreCategoria);
                @endphp
                @candirect($permiso)   
                    <div class="tab-pane fade @if($loop->first) show active @endif" 
                         id="tab-{{ $loop->index + 2 }}" 
                         role="tabpanel" 
                         aria-labelledby="tab-{{ $loop->index + 2 }}-tab">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle small soporteTable" 
                                   style="font-size: 0.875rem; width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha Creado</th>
                                        <th>Creado Por</th>
                                        <th>Área</th>
                                        <th>Categoria</th>
                                        <th>Tipo</th>
                                        <th>Modulo</th> {{-- SubTipo --}}
                                        <th>Prioridad</th>
                                        <th>Descripción</th>
                                        <th>Fecha Update</th>
                                        {{--<th>JEFE TI</th>  Asignado Inicialmente siempre Jefe --}}
                                        <th>Asignado</th> {{--  --}}
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                    @forelse ($soportesCategoria as $soporte)
                                        @php
                                            $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                            $prioridadColor = match($soporte->prioridad->nombre ?? '') {
                                                'Alta' => 'danger',
                                                'Media' => 'warning',
                                                'Baja' => 'success',
                                                default => 'secondary'
                                            };
                                            $estadoColor = match($soporte->estadoSoporte->nombre ?? '') {
                                                'Pendiente' => 'warning',
                                                'En Proceso' => 'info',
                                                'Cerrado' => 'success',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        @if(auth()->user()->id == $soporte->usuario->id)                                        
                                        <tr>
                                            {{-- ID --}}
                                            <td>
                                                <a href="javascript:void(0)"
                                                class="soporte-id fw-bold text-decoration-underline"
                                                data-id="{{ $soporte->id }}"
                                                data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                                data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                                data-area="{{ $areaModal }}"
                                                data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                                data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                                data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                data-modulo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                                data-detalles="{{ $soporte->detalles_soporte }}"
                                                data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                                data-escalado="{{ $soporte->usuario_escalado ?? 'Sin escalar' }}"
                                                data-estado="{{ $soporte->estado ?? 'Pendiente' }}">
                                                    {{ $soporte->id }}
                                                </a>
                                            </td>

                                            {{-- Fecha Creado --}}
                                            <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>

                                            {{-- Creado Por --}}
                                            <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>

                                            {{-- Área --}}
                                            <td>{{ $areaModal }}</td>

                                            {{-- Categoría --}}
                                            <td>{{ $soporte->categoria->name ?? 'N/A' }}</td>

                                            {{-- Tipo --}}
                                            <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                            {{-- SubTipo --}}
                                            <td>{{ $soporte->subTipo->nombre ?? 'N/A' }}</td>

                                            {{-- Prioridad --}}
                                            <td>
                                                <span class="badge bg-{{ $prioridadColor }}">
                                                    {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Descripción --}}
                                            <td title="{{ $soporte->detalles_soporte }}" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                data-bs-custom-class="custom-tooltip">
                                                {{ Str::limit($soporte->detalles_soporte, 50) }}
                                            </td>

                                            {{-- Fecha Update --}}
                                            <td>{{ $soporte->updated_at->format('d/m/Y H:i') }}</td>

                                            {{-- Asignado --}}
                                            <td>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}</td>

                                            {{-- Estado --}}
                                            <td>
                                                <span class="badge bg-{{ $estadoColor }}">
                                                    {{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}
                                                </span>
                                            </td>

                                            {{-- Acciones --}}
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
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center">No hay soportes en esta categoría.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                    @endcandirect
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
                        <dt class="col-sm-3">Jefe TI</dt>
                        {{--<dd class="col-sm-9" id="modal-asignado"></dd>--}}
                        <dt class="col-sm-3">Asignado</dt>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>
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
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar DataTables en todas las tablas de soportes
                $('.soporteTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excelHtml5', className: 'btn btn-sm btn-success', text: 'Excel' },
                        { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: 'PDF' },
                        { extend: 'print', className: 'btn btn-sm btn-secondary', text: 'Imprimir' }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });

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




