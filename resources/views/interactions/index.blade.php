<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- 📌 Encabezado --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="feather-grid me-2"></i> Interacciones
                </h5>
                <a href="{{ route('interactions.create') }}" class="btn btn-success btnCrear d-flex align-items-center gap-2">
                    <i class="feather-plus"></i> <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- 📌 Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('interactions.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded overflow-hidden">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="feather-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Buscar por cliente, agente, tipo, resultado o notas..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="feather-arrow-right"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- 📊 Tabla tipo Excel --}}
            <div class="table-responsive excel-grid-wrapper">
                <table class="table excel-grid table-hover mb-0 align-middle small">
                    <thead class="table-light sticky-top shadow-sm">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Agente</th>
                            <th>Canal</th>
                            <th>Tipo</th>
                            <th>Duración</th>
                            <th>Fecha</th>
                            <th>Resultado</th>
                            <th>Próx. Acción</th>
                            <th>Notas</th>
                            <th>Adjuntos</th>
                            <th>URL</th>
                            <th>Relacionado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($interactions as $interaction)
                            @php
                                $attachments = $interaction->attachment_urls ?? [];
                                $attachmentsCount = is_array($attachments) ? count($attachments) : 0;
                            @endphp
                            <tr>
                                <td>{{ $interaction->id }}</td>
                                <td>{{ $interaction->client->nom_ter ?? $interaction->client->nombre ?? '—' }}</td>
                                <td>{{ $interaction->client->cod_ter ?? $interaction->client_id ?? '—' }}</td>
                                <td>{{ $interaction->client->cel ?? '—' }}</td>
                                <td>{{ $interaction->agent->name ?? 'Sin asignar' }}</td>
                                <td>{{ $interaction->interaction_channel ?? '—' }}</td>
                                <td>{{ $interaction->interaction_type ?? '—' }}</td>
                                <td>{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}</td>
                                <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>
                                    @php
                                        $outcome = $interaction->outcome ?? '';
                                        $badge = [
                                            'Exitoso' => 'success',
                                            'Pendiente' => 'warning',
                                            'Fallido' => 'danger',
                                            'Seguimiento' => 'info'
                                        ][$outcome] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }} rounded-pill">
                                        {{ $outcome ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if($interaction->next_action_date || $interaction->next_action_type)
                                        <div>{{ optional($interaction->next_action_date)->format('d/m/Y H:i') ?? '—' }}</div>
                                        <div class="text-muted small">{{ $interaction->next_action_type ?? '—' }}</div>
                                        @if($interaction->next_action_notes)
                                            <div class="text-truncate small" data-bs-toggle="tooltip" title="{{ $interaction->next_action_notes }}">
                                                {{ \Illuminate\Support\Str::limit($interaction->next_action_notes, 80) }}
                                            </div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($interaction->notes)
                                        <div class="text-truncate" data-bs-toggle="tooltip" title="{{ $interaction->notes }}">
                                            {{ \Illuminate\Support\Str::limit($interaction->notes, 100) }}
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($attachmentsCount > 0)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info text-dark">{{ $attachmentsCount }}</span>
                                            <div class="dropdown">
                                                <a href="#" role="button" data-bs-toggle="dropdown">
                                                    <i class="feather-download-cloud"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    @foreach($attachments as $idx => $att)
                                                        <li>
                                                            <a href="{{ $att }}" target="_blank" class="dropdown-item small">
                                                                Archivo {{ $idx + 1 }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($interaction->interaction_url)
                                        <a href="{{ $interaction->interaction_url }}" target="_blank" data-bs-toggle="tooltip" title="Abrir URL">
                                            <i class="feather-external-link"></i>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($interaction->parent_interaction_id)
                                        <a href="{{ route('interactions.show', $interaction->parent_interaction_id) }}">
                                            #{{ $interaction->parent_interaction_id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            <i class="feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item btnVer" href="{{ route('interactions.show', $interaction->id) }}">
                                                    <i class="feather-eye me-2"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btnEditar" href="{{ route('interactions.edit', $interaction->id) }}">
                                                    <i class="feather-edit-3 me-2"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('interactions.destroy', $interaction->id) }}" method="POST" class="formEliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="feather-trash-2 me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center text-muted py-4">
                                    <i class="feather-info me-1"></i> No hay registros disponibles
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $interactions->appends(request()->except('page'))->links('components.maestras.congregaciones.pagination') }}
            </div>
        </div>
    </div>

    {{-- 📌 Estilos tipo Excel --}}
    <style>
        .excel-grid-wrapper {
            max-height: 70vh;
            overflow-y: auto;
            overflow-x: auto;
        }

        .excel-grid {
            border-collapse: collapse;
            white-space: nowrap;
            width: 100%;
            table-layout: auto;
        }

        .excel-grid th,
        .excel-grid td {
            border: 1px solid #d1d1d1;
            padding: 6px 8px;
            min-width: 120px;
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .excel-grid thead th {
            background: #f3f4f6;
            font-weight: 600;
            text-align: left;
            position: sticky;
            top: 0;
            cursor: col-resize;
            z-index: 2;
        }

        .excel-grid tr:hover {
            background: #eef5ff;
        }

        .excel-grid td {
            background: #fff;
        }

        .excel-grid th.resizing,
        .excel-grid td.resizing {
            user-select: none;
        }
    </style>

    {{-- 📌 Script de redimensionamiento tipo Excel --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = document.querySelector('.excel-grid');
                const ths = table.querySelectorAll('th');

                ths.forEach(th => {
                    th.style.position = 'relative';
                    const resizer = document.createElement('div');
                    resizer.style.width = '5px';
                    resizer.style.height = '100%';
                    resizer.style.position = 'absolute';
                    resizer.style.top = '0';
                    resizer.style.right = '0';
                    resizer.style.cursor = 'col-resize';
                    resizer.style.userSelect = 'none';
                    resizer.style.background = 'transparent';
                    th.appendChild(resizer);

                    let startX, startWidth;

                    resizer.addEventListener('mousedown', function (e) {
                        startX = e.pageX;
                        startWidth = parseInt(document.defaultView.getComputedStyle(th).width, 10);
                        document.documentElement.addEventListener('mousemove', doDrag);
                        document.documentElement.addEventListener('mouseup', stopDrag);
                        th.classList.add('resizing');
                    });

                    function doDrag(e) {
                        th.style.width = (startWidth + e.pageX - startX) + 'px';
                    }

                    function stopDrag() {
                        document.documentElement.removeEventListener('mousemove', doDrag);
                        document.documentElement.removeEventListener('mouseup', stopDrag);
                        th.classList.remove('resizing');
                    }
                });

                // Tooltip Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) {
                    new bootstrap.Tooltip(el);
                });

                // Confirmaciones con SweetAlert
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
                        showClass: { popup: 'animate__animated animate__zoomIn' },
                        hideClass: { popup: 'animate__animated animate__zoomOut' }
                    }).then((result) => {
                        if (result.isConfirmed) callback();
                    });
                }

                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        confirmarAccion('¿Crear una nueva interacción?', 'Serás redirigido al formulario de creación.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnVer').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        confirmarAccion('¿Ver detalles de la interacción?', 'Serás redirigido a la vista detallada.', 'info', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        confirmarAccion('¿Editar esta interacción?', 'Serás redirigido al formulario de edición.', 'question', () => {
                            window.location.href = btn.getAttribute('href');
                        });
                    });
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', e => {
                        e.preventDefault();
                        confirmarAccion('¿Eliminar esta interacción?', 'Esta acción no se puede deshacer.', 'warning', () => {
                            form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-base-layout>
