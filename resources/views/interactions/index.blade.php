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
                    <i class="feather-grid me-2"></i> Gestión de Interacciones
                    <small class="text-muted d-block mt-1 fw-normal">Visualiza, busca y gestiona todas las interacciones con tus clientes.</small>
                </h5>
                <a href="{{ route('interactions.create') }}" class="btn btn-primary d-flex align-items-center gap-2 animate__animated animate__pulse animate__infinite">
                    <i class="feather-plus-circle"></i> <span>Nueva Interacción</span>
                </a>
            </div>

            {{-- 📌 Buscador Principal y Filtros --}}
            <div class="px-4 pb-4">
                <form id="filterForm" action="{{ route('interactions.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded overflow-hidden mb-3">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0"
                               placeholder="Buscar por cliente, cédula, resultado o notas..."
                               value="{{ request('q') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="feather-filter d-none d-sm-inline me-1"></i> Filtrar
                        </button>
                        @if (request('q') || request('channel_filter') || request('type_filter') || request('outcome_filter') || request('interaction_date_filter'))
                            <a href="{{ route('interactions.index') }}" class="btn btn-outline-secondary">
                                <i class="feather-x-circle me-1"></i> Limpiar
                            </a>
                        @endif
                    </div>

                    {{-- 📦 Filtros Adicionales --}}
                    <div class="card p-3 bg-light">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Canal</label>
                                <select id="channel_filter" name="channel_filter" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel }}" {{ request('channel_filter') == $channel ? 'selected' : '' }}>{{ $channel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tipo</label>
                                <select id="type_filter" name="type_filter" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type_filter') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Resultado</label>
                                <select id="outcome_filter" name="outcome_filter" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($outcomes as $outcome)
                                        <option value="{{ $outcome }}" {{ request('outcome_filter') == $outcome ? 'selected' : '' }}>{{ $outcome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Fecha Interacción</label>
                                <input type="date" id="interaction_date_filter" name="interaction_date_filter" class="form-control form-control-sm" value="{{ request('interaction_date_filter') }}" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 📊 Tabla Actualizada --}}
            <div class="table-responsive excel-grid-wrapper">
                <table id="interactionsTable" class="table excel-grid table-hover mb-0 align-middle small">
                    <thead class="table-light sticky-top shadow-sm">
                        <tr>
                            <th style="width: 50px;" class="text-center"># ID</th>
                            <th style="width: 20%;">Cliente</th>
                            <th style="width: 150px;" data-bs-toggle="tooltip" title="Número de identificación del cliente">Cédula</th>
                            <th style="width: 120px;">Teléfono</th>
                            <th style="width: 160px;" data-bs-toggle="tooltip" title="Fecha y hora en que ocurrió la interacción">Fecha Interacción</th>
                            <th style="width: 160px;" data-bs-toggle="tooltip" title="Fecha y tipo de la próxima acción programada">Próxima Acción</th>
                            <th style="width: 120px;">Resultado</th>
                            <th style="width: 120px;" class="text-center">Acciones Rápidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($interactions as $interaction)
                            @php
                                $attachments = $interaction->attachment_urls ?? [];
                                $outcome = $interaction->outcomeRelation?->name ?? 'Sin definir';
                                $badgeClass = [
                                    'Exitoso' => 'success',
                                    'Pendiente' => 'warning',
                                    'Fallido' => 'danger',
                                    'Seguimiento' => 'info'
                                ][$outcome] ?? 'secondary';

                                // NOTA: El controlador carga 'nextAction' pero la vista usa columnas directas.
                                // Esto es correcto SI 'next_action_date' y 'next_action_type' son columnas en la tabla 'interactions'.
                                $nextActionDate = optional($interaction->next_action_date);
                                $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                $isToday = $nextActionDate->isToday();
                                $isFuture = $nextActionDate->isFuture();

                                // Se usa 'client->nom_ter' y 'client->cod_ter' basado en la lógica de búsqueda del controlador
                                $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A'; // Corregido a identificacion
                            @endphp

                            <tr>
                                <td class="text-center">
                                    <a href="#"
                                       class="fw-bold text-primary text-decoration-none"
                                       data-bs-toggle="modal"
                                       data-bs-target="#interactionDetailModal"
                                       data-id="{{ $interaction->id }}"
                                       data-client-name="{{ $clientName }}"
                                       data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                       data-channel="{{ $interaction->channel?->name ?? '—' }}"
                                       data-type="{{ $interaction->type?->name ?? '—' }}"
                                       data-duration="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
                                       data-date="{{ optional($interaction->interaction_date)->format('d/m/Y h:i A') ?? '—' }}"
                                       data-notes="{{ $interaction->notes ?? 'Sin notas.' }}"
                                       data-outcome="{{ $outcome }}"
                                       data-outcome-badge="{{ $badgeClass }}"
                                       data-url="{{ $interaction->interaction_url }}"
                                       data-related-id="{{ $interaction->parent_interaction_id }}"
                                       data-related-url="{{ $interaction->parent_interaction_id ? route('interactions.show', $interaction->parent_interaction_id) : '' }}"
                                       data-attachments='{{ json_encode($attachments) }}'
                                       data-next-action-date="{{ optional($interaction->next_action_date)->format('d/m/Y h:i A') }}"
                                       data-next-action-type="{{ $interaction->next_action_type }}"
                                       data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                       data-show-url="{{ route('interactions.show', $interaction->id) }}"
                                    >
                                        #{{ $interaction->client->cod_dist }}
                                    </a>
                                </td>
                                <td>{{ $clientName }}</td>
                                <td>
                                    <strong>{{ $clientId }}</strong>
                                    <br>
                                    <span class="text-muted small">{{ $clientName }}</span>
                                </td>
                                <td>
                                    <strong>{{ $interaction->client->cel ?? '—' }}</strong>
                                    <br>
                                    <span class="text-muted small">{{ $interaction->client->email ?? '—' }}</span>
                                </td>
                                <td data-order="{{ optional($interaction->interaction_date)->getTimestamp() }}">
                                    {{ optional($interaction->interaction_date)->format('d/m/Y h:i A') ?? '—' }}
                                </td>
                                <td>
                                    @if ($interaction->next_action_date)
                                        <span class="d-block {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }}">
                                            <i class="feather-calendar me-1"></i> {{ optional($interaction->next_action_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="d-block text-muted small">
                                            <i class="feather-clock me-1"></i> {{ optional($interaction->next_action_date)->format('h:i A') }}
                                            @if($interaction->next_action_type) ({{ $interaction->next_action_type }}) @endif
                                        </span>
                                    @else
                                        <span class="text-muted small">— Sin próxima acción —</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badgeClass }} rounded-pill px-2 py-1">
                                        {{ $outcome }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary border-0"
                                                data-bs-toggle="modal"
                                                data-bs-target="#interactionDetailModal"
                                                data-id="{{ $interaction->id }}"
                                                data-client-name="{{ $clientName }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-channel="{{ $interaction->channel?->name ?? '—' }}"
                                                data-type="{{ $interaction->type?->name ?? '—' }}"
                                                data-duration="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
                                                data-date="{{ optional($interaction->interaction_date)->format('d/m/Y h:i A') ?? '—' }}"
                                                data-notes="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-outcome="{{ $outcome }}"
                                                data-outcome-badge="{{ $badgeClass }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-related-id="{{ $interaction->parent_interaction_id }}"
                                                data-related-url="{{ $interaction->parent_interaction_id ? route('interactions.show', $interaction->parent_interaction_id) : '' }}"
                                                data-attachments='{{ json_encode($attachments) }}'
                                                data-next-action-date="{{ optional($interaction->next_action_date)->format('d/m/Y h:i A') }}"
                                                data-next-action-type="{{ $interaction->next_action_type }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}"
                                                title="Ver Detalles"
                                        ><i class="feather-info"></i></button>

                                        <a href="{{ route('interactions.edit', $interaction->id) }}" class="btn btn-sm btn-outline-warning border-0" title="Editar"><i class="feather-edit-3"></i></a>
                                        {{-- El formulario de eliminación está comentado, si se necesita, descomentar y asegurarse de tener SweetAlert2 configurado --}}
                                        {{-- <form action="{{ route('interactions.destroy', $interaction->id) }}" method="POST" class="formEliminar d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Eliminar"><i class="feather-trash-2"></i></button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="feather-info me-1"></i> No hay interacciones disponibles.
                                    <br>
                                    <a href="{{ route('interactions.create') }}" class="btn btn-sm btn-link mt-2"><i class="feather-plus-circle me-1"></i> Crear la primera interacción</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($interactions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{-- Esto asegura que los filtros se mantengan al cambiar de página --}}
                    {{ $interactions->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- 📦 Modal de Detalle --}}
    <div class="modal fade" id="interactionDetailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="feather-file-text me-2"></i> Detalles de Interacción:
                        <span class="text-primary fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="row g-3">
                        <div class="col-md-6 border-end pe-md-4">
                            <h6><i class="feather-user me-2 text-primary"></i>Datos Principales</h6>
                            <dl class="row small mb-2">
                                <dt class="col-sm-4 text-muted">Cliente:</dt><dd class="col-sm-8 fw-bold" id="modalClientName"></dd>
                                <dt class="col-sm-4 text-muted">Agente:</dt><dd class="col-sm-8" id="modalAgent"></dd>
                                <dt class="col-sm-4 text-muted">Fecha y Hora:</dt><dd class="col-sm-8" id="modalDate"></dd>
                                <dt class="col-sm-4 text-muted">Duración:</dt><dd class="col-sm-8" id="modalDuration"></dd>
                                <dt class="col-sm-4 text-muted">Resultado:</dt><dd class="col-sm-8" id="modalOutcome"></dd>
                            </dl>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <h6><i class="feather-settings me-2 text-primary"></i>Clasificación</h6>
                            <dl class="row small mb-2">
                                <dt class="col-sm-4 text-muted">Canal:</dt><dd class="col-sm-8" id="modalChannel"></dd>
                                <dt class="col-sm-4 text-muted">Tipo:</dt><dd class="col-sm-8" id="modalType"></dd>
                                <dt class="col-sm-4 text-muted">URL:</dt><dd class="col-sm-8" id="modalUrl"></dd>
                                <dt class="col-sm-4 text-muted">Relacionada con:</dt><dd class="col-sm-8" id="modalRelated"></dd>
                            </dl>
                        </div>
                    </div>
                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6><i class="feather-clock me-2 text-primary"></i>Próxima Acción</h6>
                            <p class="small bg-light p-2 rounded border" id="modalNextAction"></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="feather-paperclip me-2 text-primary"></i>Archivos Adjuntos</h6>
                            <div id="modalAttachmentsList" class="small bg-light p-2 rounded border"></div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h6><i class="feather-edit me-2 text-primary"></i>Notas de la Interacción</h6>
                    <p class="small bg-light p-3 rounded border" id="modalNotes" style="white-space: pre-wrap;"></p>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="#" id="modalShowUrl" class="btn btn-outline-secondary btn-sm"><i class="feather-eye me-1"></i> Ver Página Completa</a>
                        <a href="#" id="modalEditUrl" class="btn btn-primary btn-sm"><i class="feather-edit-3 me-1"></i> Editar Interacción</a>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 📘 Estilos --}}
    <style>
        .excel-grid-wrapper { max-height: 70vh; overflow-x: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; }
        .excel-grid { border-collapse: collapse; white-space: nowrap; width: 100%; table-layout: fixed; }
        .excel-grid th, .excel-grid td { border: 1px solid #eff2f7; padding: 6px 10px; overflow: hidden; text-overflow: ellipsis; }
        .excel-grid th { text-align: left; }
        .excel-grid thead th { background: #e9ecef; font-weight: 600; position: sticky; top: 0; z-index: 2; border-bottom: 2px solid #dee2e6; }
        .excel-grid tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
        .btn-outline-primary, .btn-outline-warning, .btn-outline-danger { --bs-btn-padding-y: .3rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem; }
        .animate__animated.animate__pulse.animate__infinite { animation-duration: 2s; }

        #interactionDetailModal dt { font-weight: 600; color: #6c757d; }
        #interactionDetailModal dd { margin-bottom: 0.25rem; }
        #interactionDetailModal .row.small > dt, #interactionDetailModal .row.small > dd { padding-top: 0.25rem; padding-bottom: 0.25rem; }
    </style>

    {{-- 📜 Script --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "¡Esta acción no se puede deshacer!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, ¡eliminar!',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => { if (result.isConfirmed) { this.submit(); } });
                    });
                });

                const interactionDetailModal = document.getElementById('interactionDetailModal');
                interactionDetailModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const modal = interactionDetailModal;
                    const getData = (attr) => button.getAttribute(`data-${attr}`);

                    modal.querySelector('#modalTitle span').textContent = `#${getData('id')} - ${getData('client-name')}`;
                    modal.querySelector('#modalClientName').textContent = getData('client-name');
                    modal.querySelector('#modalAgent').textContent = getData('agent');
                    modal.querySelector('#modalDate').textContent = getData('date');
                    modal.querySelector('#modalChannel').textContent = getData('channel');
                    modal.querySelector('#modalType').textContent = getData('type');
                    modal.querySelector('#modalDuration').textContent = getData('duration');
                    modal.querySelector('#modalNotes').textContent = getData('notes');
                    modal.querySelector('#modalEditUrl').href = getData('edit-url');
                    modal.querySelector('#modalShowUrl').href = getData('show-url');
                    modal.querySelector('#modalOutcome').innerHTML = `<span class="badge bg-${getData('outcome-badge')} rounded-pill px-2 py-1">${getData('outcome')}</span>`;

                    const url = getData('url');
                    modal.querySelector('#modalUrl').innerHTML = url && url !== 'null' ? `<a href="${url}" target="_blank">Abrir enlace <i class="feather-external-link small"></i></a>` : '— No hay URL';

                    const relatedId = getData('related-id');
                    const relatedUrl = getData('related-url');
                    modal.querySelector('#modalRelated').innerHTML = relatedId && relatedId !== 'null' ? `<a href="${relatedUrl}">Interacción #${relatedId}</a>` : '— No relacionada';

                    const nextActionDate = getData('next-action-date');
                    const nextActionType = getData('next-action-type');
                    const nextActionEl = modal.querySelector('#modalNextAction');
                    if (nextActionDate && nextActionDate !== 'null' || nextActionType && nextActionType !== 'null') {
                        const dateObj = nextActionDate ? new Date(nextActionDate.split(' ')[0].split('/').reverse().join('-') + ' ' + nextActionDate.split(' ')[1]) : null;
                        let dateClass = 'text-body';
                        let dateStatus = '';

                        if (dateObj) {
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            const actionDay = new Date(dateObj);
                            actionDay.setHours(0, 0, 0, 0);

                            if (actionDay < today) {
                                dateClass = 'text-danger fw-bold';
                                dateStatus = '(Vencida)';
                            } else if (actionDay.getTime() === today.getTime()) {
                                dateClass = 'text-warning fw-bold';
                                dateStatus = '(Hoy)';
                            } else {
                                dateClass = 'text-success fw-bold';
                                dateStatus = '(Próxima)';
                            }
                        }

                        nextActionEl.innerHTML = `
                            <p class="mb-1"><strong class="${dateClass}">Fecha:</strong> ${nextActionDate || 'No definida'} <span class="small">${dateStatus}</span></p>
                            <p class="mb-0"><strong>Tipo:</strong> ${nextActionType || 'No definida'}</p>
                        `;
                    } else {
                        nextActionEl.textContent = 'No hay próxima acción programada.';
                    }

                    const attachmentsList = modal.querySelector('#modalAttachmentsList');
                    const attachments = JSON.parse(getData('attachments') || '[]');
                    attachmentsList.innerHTML = '';
                    if (attachments && attachments.length > 0) {
                        const list = document.createElement('ul');
                        list.className = 'list-unstyled mb-0';
                        attachments.forEach((fileUrl, index) => {
                            const listItem = document.createElement('li');
                            const link = document.createElement('a');
                            link.href = fileUrl;
                            link.target = '_blank';
                            link.className = 'd-flex align-items-center gap-2 mb-1 text-decoration-none text-primary';
                            link.innerHTML = `<i class="feather-paperclip"></i> Adjunto ${index + 1}`;
                            listItem.appendChild(link);
                            list.appendChild(listItem);
                        });
                        attachmentsList.appendChild(list);
                    } else {
                        attachmentsList.innerHTML = '<p class="text-muted mb-0">No hay archivos adjuntos.</p>';
                    }
                });
            });

            $(document).ready(function() {
                if ($.fn.DataTable.isDataTable('#interactionsTable')) {
                    $('#interactionsTable').DataTable().destroy();
                }

                $('#interactionsTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excelHtml5', className: 'btn btn-success btn-sm me-1', text: '<i class="feather-file-text me-1"></i> Excel', title: 'Interacciones_{{ date("Y-m-d") }}' },
                        { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm me-1', text: '<i class="feather-file-text me-1"></i> PDF', title: 'Interacciones_{{ date("Y-m-d") }}' },
                        { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="feather-printer me-1"></i> Imprimir' }
                    ],
                    paging: false, // Deshabilitamos la paginación de DataTables para usar la de Laravel
                    ordering: true, // Permitimos ordenar por columnas (solo datos de la página actual)
                    info: false, // Ocultamos el "Mostrando X de Y"
                    searching: false, // Deshabilitamos la búsqueda de DataTables, usamos la del backend.
                    autoWidth: false,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
                });

                // Eventos para los filtros adicionales: submite el formulario al cambiar
                $('#channel_filter, #type_filter, #outcome_filter, #interaction_date_filter').on('change', function() {
                    $('#filterForm').submit();
                });
            });
        </script>

        {{-- ✅ EXPORTACIÓN: Excel, PDF, Imprimir --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        <script>
            $(document).ready(function() {
                if ($.fn.DataTable.isDataTable('#interactionsTable')) {
                    $('#interactionsTable').DataTable().destroy();
                }

                $('#interactionsTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excelHtml5', className: 'btn btn-success btn-sm me-1', text: '<i class="feather-file-text me-1"></i> Excel', title: 'Interacciones_{{ date("Y-m-d") }}' },
                        { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm me-1', text: '<i class="feather-file-text me-1"></i> PDF', title: 'Interacciones_{{ date("Y-m-d") }}' },
                        { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="feather-printer me-1"></i> Imprimir' }
                    ],
                    paging: false,
                    ordering: true,
                    info: false,
                    searching: false,
                    autoWidth: false,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
                });
            });
        </script>
    @endpush
</x-base-layout>