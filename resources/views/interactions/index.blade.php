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
                <a href="{{ route('interactions.create') }}" class="btn btn-success d-flex align-items-center gap-2">
                    <i class="feather-plus"></i> <span>Crear Nuevo</span>
                </a>
            </div>

            {{-- 📌 Buscador --}}
            <div class="px-4 pb-4">
                <form action="{{ route('interactions.index') }}" method="GET">
                    <div class="input-group shadow-sm rounded overflow-hidden">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Buscar por cliente, agente, tipo, resultado o notas..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="feather-arrow-right d-none d-sm-inline me-1"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            {{-- 📊 Tabla Super Simplificada --}}
            <div class="table-responsive excel-grid-wrapper">
                <table class="table excel-grid table-hover mb-0 align-middle small">
                    <thead class="table-light sticky-top shadow-sm">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Cédula</th>
                            <th>Teléfono</th>
                            <th>Resultado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($interactions as $interaction)
                            @php
                                $attachments = $interaction->attachment_urls ?? [];
                                $outcome = $interaction->outcome ?? '';
                                $badgeClass = ['Exitoso' => 'success', 'Pendiente' => 'warning', 'Fallido' => 'danger', 'Seguimiento' => 'info'][$outcome] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    {{-- El ID sigue siendo el enlace que abre el modal con TODOS los datos --}}
                                    <a href="#" class="fw-bold" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#interactionDetailModal"
                                       {{-- Datos básicos --}}
                                       data-id="{{ $interaction->id }}"
                                       data-client-name="{{ $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A' }}"
                                       data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                       data-channel="{{ $interaction->interaction_channel ?? '—' }}"
                                       data-type="{{ $interaction->interaction_type ?? '—' }}"
                                       data-duration="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
                                       data-date="{{ optional($interaction->interaction_date)->format('d/m/Y h:i A') ?? '—' }}"
                                       data-notes="{{ $interaction->notes ?? 'Sin notas.' }}"
                                       {{-- Campos que estaban en la tabla y ahora solo están en el modal --}}
                                       data-outcome="{{ $outcome ?: '—' }}"
                                       data-outcome-badge="{{ $badgeClass }}"
                                       data-url="{{ $interaction->interaction_url }}"
                                       data-related-id="{{ $interaction->parent_interaction_id }}"
                                       data-related-url="{{ $interaction->parent_interaction_id ? route('interactions.show', $interaction->parent_interaction_id) : '' }}"
                                       data-attachments='{{ json_encode($attachments) }}'
                                       {{-- Datos para Próx. Acción --}}
                                       data-next-action-date="{{ optional($interaction->next_action_date)->format('d/m/Y h:i A') }}"
                                       data-next-action-type="{{ $interaction->next_action_type }}"
                                       {{-- URLs para botones del modal --}}
                                       data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                       data-show-url="{{ route('interactions.show', $interaction->id) }}"
                                    >
                                        #{{ $interaction->id }}
                                    </a>
                                </td>
                                <td>{{ $interaction->client->nom_ter ?? $interaction->client->nombre ?? '—' }}</td>
                                <td>{{ $interaction->client->cod_ter ?? $interaction->client_id ?? '—' }}</td>
                                <td>{{ $interaction->client->cel ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $badgeClass }} rounded-pill">{{ $outcome ?: '—' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Ver Página Completa"><i class="feather-eye"></i></a>
                                        <a href="{{ route('interactions.edit', $interaction->id) }}" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Editar"><i class="feather-edit-3"></i></a>
                                        <form action="{{ route('interactions.destroy', $interaction->id) }}" method="POST" class="formEliminar d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" data-bs-toggle="tooltip" title="Eliminar"><i class="feather-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Se ajusta el colspan a 6 columnas --}}
                                <td colspan="6" class="text-center text-muted py-4"><i class="feather-info me-1"></i> No hay registros disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($interactions->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $interactions->appends(request()->except('page'))->links() }}</div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="interactionDetailModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><i class="feather-file-text me-2"></i> Detalles de la Interacción: <span class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6><i class="feather-info me-2 text-primary"></i>Información General</h6>
                            <dl class="row small">
                                <dt class="col-sm-4">Agente:</dt><dd class="col-sm-8" id="modalAgent"></dd>
                                <dt class="col-sm-4">Fecha:</dt><dd class="col-sm-8" id="modalDate"></dd>
                                <dt class="col-sm-4">Canal:</dt><dd class="col-sm-8" id="modalChannel"></dd>
                                <dt class="col-sm-4">Tipo:</dt><dd class="col-sm-8" id="modalType"></dd>
                                <dt class="col-sm-4">Duración:</dt><dd class="col-sm-8" id="modalDuration"></dd>
                                <dt class="col-sm-4">Resultado:</dt><dd class="col-sm-8" id="modalOutcome"></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="feather-activity me-2 text-primary"></i>Datos Adicionales</h6>
                            <dl class="row small">
                                <dt class="col-sm-4">URL:</dt><dd class="col-sm-8" id="modalUrl"></dd>
                                <dt class="col-sm-4">Relacionado:</dt><dd class="col-sm-8" id="modalRelated"></dd>
                            </dl>
                            <h6 class="mt-3"><i class="feather-clock me-2 text-primary"></i>Próxima Acción</h6>
                            <p class="small" id="modalNextAction"></p>
                        </div>
                    </div>
                    <hr>
                    <h6><i class="feather-edit me-2 text-primary"></i>Notas</h6>
                    <p class="small bg-light p-3 rounded" id="modalNotes" style="white-space: pre-wrap;"></p>
                    
                    <hr>
                    <h6><i class="feather-paperclip me-2 text-primary"></i>Archivos Adjuntos</h6>
                    <div id="modalAttachmentsList" class="small"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modalShowUrl" class="btn btn-outline-secondary btn-sm"><i class="feather-eye me-1"></i> Ver Página Completa</a>
                    <a href="#" id="modalEditUrl" class="btn btn-primary btn-sm"><i class="feather-edit-3 me-1"></i> Editar</a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Estilos (Sin cambios) --}}
    <style>
        .excel-grid-wrapper { max-height: 70vh; overflow: auto; }
        .excel-grid { border-collapse: collapse; white-space: nowrap; width: 100%; table-layout: auto; }
        .excel-grid th, .excel-grid td { border: 1px solid #dee2e6; padding: 6px 10px; min-width: 120px; max-width: 400px; overflow: hidden; text-overflow: ellipsis; }
        .excel-grid thead th { background: #f8f9fa; font-weight: 600; text-align: left; position: sticky; top: 0; z-index: 2; }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?', text: "¡Esta acción no se puede deshacer!", icon: 'warning',
                            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, ¡eliminar!', cancelButtonText: 'Cancelar'
                        }).then((result) => { if (result.isConfirmed) { this.submit(); } });
                    });
                });

                // SCRIPT PARA POBLAR EL MODAL (SIN CAMBIOS)
                const interactionDetailModal = document.getElementById('interactionDetailModal');
                interactionDetailModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const modal = interactionDetailModal;

                    const getData = (attr) => button.getAttribute(`data-${attr}`);
                    
                    modal.querySelector('#modalTitle span').textContent = `#${getData('id')} (${getData('client-name')})`;
                    modal.querySelector('#modalAgent').textContent = getData('agent');
                    modal.querySelector('#modalDate').textContent = getData('date');
                    modal.querySelector('#modalChannel').textContent = getData('channel');
                    modal.querySelector('#modalType').textContent = getData('type');
                    modal.querySelector('#modalDuration').textContent = getData('duration');
                    modal.querySelector('#modalNotes').textContent = getData('notes');
                    modal.querySelector('#modalEditUrl').href = getData('edit-url');
                    modal.querySelector('#modalShowUrl').href = getData('show-url');

                    modal.querySelector('#modalOutcome').innerHTML = `<span class="badge bg-${getData('outcome-badge')} rounded-pill">${getData('outcome')}</span>`;
                    
                    const url = getData('url');
                    modal.querySelector('#modalUrl').innerHTML = url ? `<a href="${url}" target="_blank">Abrir enlace <i class="feather-external-link small"></i></a>` : '—';

                    const relatedId = getData('related-id');
                    const relatedUrl = getData('related-url');
                    modal.querySelector('#modalRelated').innerHTML = relatedId ? `<a href="${relatedUrl}">#${relatedId}</a>` : '—';

                    const nextActionDate = getData('next-action-date');
                    const nextActionType = getData('next-action-type');
                    const nextActionEl = modal.querySelector('#modalNextAction');
                    if (nextActionDate || nextActionType) {
                        nextActionEl.innerHTML = `<strong>Fecha:</strong> ${nextActionDate || 'No definida'}<br><strong>Acción:</strong> ${nextActionType || 'No definida'}`;
                    } else {
                        nextActionEl.textContent = 'No hay próxima acción programada.';
                    }

                    const attachmentsList = modal.querySelector('#modalAttachmentsList');
                    const attachments = JSON.parse(getData('attachments'));
                    attachmentsList.innerHTML = '';
                    if (attachments && attachments.length > 0) {
                        const list = document.createElement('ul');
                        list.className = 'list-unstyled';
                        attachments.forEach((fileUrl, index) => {
                            const listItem = document.createElement('li');
                            const link = document.createElement('a');
                            link.href = fileUrl;
                            link.target = '_blank';
                            link.className = 'd-flex align-items-center gap-2 mb-1';
                            link.innerHTML = `<i class="feather-download-cloud"></i> Archivo ${index + 1}`;
                            listItem.appendChild(link);
                            list.appendChild(listItem);
                        });
                        attachmentsList.appendChild(list);
                    } else {
                        attachmentsList.innerHTML = '<p class="text-muted mb-0">No hay archivos adjuntos.</p>';
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>