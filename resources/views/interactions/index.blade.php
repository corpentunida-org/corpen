<x-base-layout>
    {{-- Alertas mejoradas con diseño --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center glassmorphism-alert"
            role="alert">
            <i class="feather-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center glassmorphism-alert"
            role="alert">
            <i class="feather-alert-circle me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @php
        $tabs = [
            'all' => [
                'id' => 'tablaPrincipal',
                'data' => $interactions,
            ],
            'success' => [
                'id' => 'tablaExitosos',
                'data' => $collectionsForTabs['successful'],
            ],
            'pending' => [
                'id' => 'tablaPendientes',
                'data' => $collectionsForTabs['pending'],
            ],
            'today' => [
                'id' => 'tablaHoy',
                'data' => $collectionsForTabs['today'],
            ],
            'overdue' => [
                'id' => 'tablaVencidos',
                'data' => $collectionsForTabs['overdue'],
            ],
        ];
    @endphp
    {{-- Filtros: Solo Búsqueda y Rango de Fechas --}}
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
            <div>
                <h6 class="mb-0 fw-semibold">
                    <i class="feather-search me-2"></i>Buscar y Filtrar
                </h6>
                <small class="text-muted">Refina los resultados</small>
            </div>
            <button type="button" class="btn btn-sm btn-light" id="clearFilters">
                <i class="feather-refresh-cw me-1"></i>Restablecer
            </button>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small mb-1">Búsqueda General</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-search"></i></span>
                        <input type="text" id="filterSearch" class="form-control pastel-input"
                            placeholder="Cliente, cédula, agente, notas..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted small mb-1">Rango de Fechas (Interacción)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" id="filterFechaInicio" class="form-control pastel-input"
                            value="{{ request('start_date') }}" />
                        <span class="input-group-text">a</span>
                        <input type="date" id="filterFechaFin" class="form-control pastel-input"
                            value="{{ request('end_date') }}" />
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="resultCount">Mostrando resultados filtrados</span>
                        </small>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary me-2" id="applyFilters">
                                <i class="feather-filter me-1"></i>Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla mejorada --}}
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Listado de Interacciones</h5>
                    <p class="text-muted mb-0 small">Gestiona y monitorea todas las interacciones con clientes</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('interactions.create') }}" class="btn btn-success pastel-btn-gradient btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nueva Interacción</span>
                    </a>
                    <div id="exportButtons"></div>
                </div>
            </div>
            <ul class="nav nav-tabs mb-3 pastel-tabs" id="interactionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab active" id="tab-all-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-all" type="button" role="tab" aria-controls="tab-all"
                        aria-selected="true">
                        <i class="feather-inbox me-2"></i>
                        TODOS
                        <span class="badge bg-soft-teal text-teal">
                            {{ $stats['total'] }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-success-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-success" type="button" role="tab" aria-controls="tab-success"
                        aria-selected="false">
                        <i class="feather-check-circle me-2"></i>
                        EXITOSOS
                        <span class="badge bg-soft-teal text-success">
                            {{ $stats['successful'] }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-pending-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending"
                        aria-selected="false">
                        <i class="feather-clock me-2"></i>
                        PENDIENTES
                        <span class="badge bg-soft-warning text-warning">
                            {{ $stats['pending'] }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-today-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-today" type="button" role="tab" aria-controls="tab-today"
                        aria-selected="false">
                        <i class="feather-calendar me-2"></i>
                        HOY
                        <span class="badge bg-soft-primary text-primary">
                            {{ $stats['today'] }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-overdue-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-overdue" type="button" role="tab" aria-controls="tab-overdue"
                        aria-selected="false">
                        <i class="feather-alert-triangle me-2"></i>
                        VENCIDOS
                        <span class="badge bg-soft-danger text-danger">
                            {{ $stats['overdue'] ?? 0 }}
                        </span>
                    </button>
                </li>
            </ul>
            @php
                $priorityColors = [
                    '1' => 'success',
                    '2' => 'warning',
                    '3' => 'danger',
                ];
            @endphp
            {{-- Contenido --}}
            <div class="tab-content" id="interactionTabsContent">
                @foreach ($tabs as $key => $tab)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $key }}"
                        role="tabpanel">

                        <div class="table-responsive">

                            <table class="table table-sm table-hover align-middle interactionTable excel-table"
                                id="{{ $tab['id'] }}">

                                <thead class="table-light pastel-thead">
                                    <tr>
                                        <th class="py-2">ID</th>
                                        <th class="py-2">Usuario</th>
                                        <th class="py-2">Cliente</th>
                                        <th class="py-2">Distrito</th>
                                        <th class="py-2">Fecha</th>
                                        <th class="py-2">Canal</th>
                                        <th class="py-2">Tiempo</th>
                                        <th class="py-2">Motivo</th>
                                        <th class="py-2">Línea y Resultado</th>
                                        <th class="py-2 text-end">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($tab['data'] as $interaction)
                                        @php
                                            $nextActionDate = optional($interaction->next_action_date);
                                            $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                            $isToday = $nextActionDate->isToday();
                                        @endphp

                                        <tr class="table table-hover">
                                            <td>

                                                <a class="interaction-id fw-bold text-decoration-none pastel-link"
                                                    href="#" data-id="{{ $interaction->id }}"
                                                    data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                    data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                    data-client-id="{{ $interaction->client_id ?? '—' }}"
                                                    data-agent="{{ $interaction->agent->name ?? '—' }}"
                                                    data-motivo="{{ $interaction->type->name ?? 'N/A' }}"
                                                    data-duracion="{{ floor($interaction->duration / 60) }}m {{ $interaction->duration % 60 }}s"
                                                    data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                    data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                    data-linea="{{ !empty($nombresEncontrados) ? $nombresEncontrados[0] : '—' }}"
                                                    data-asignado="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                    data-llamante-nombre="{{ $interaction->nombre_quien_llama ?? '—' }}"
                                                    data-llamante-cedula="{{ $interaction->cedula_quien_llama ?? '—' }}"
                                                    data-llamante-celular="{{ $interaction->celular_quien_llama ?? '—' }}"
                                                    data-llamante-parentesco="{{ $interaction->parentesco_quien_llama ?? '—' }}">
                                                    #{{ $interaction->id }}
                                                </a>

                                                @if ($interaction->attachment_urls)
                                                    <a href="{{ $interaction->getFile($interaction->attachment_urls) }}"
                                                        target="_blank" class="ms-2 text-info"
                                                        title="Ver archivo adjunto">

                                                        <i class="fas fa-paperclip"></i>

                                                    </a>
                                                @endif

                                            </td>
                                            <td style="width: 13%">
                                                <span class="fw-semibold mb-1">
                                                    {{ $interaction->agent->name ?? '—' }}
                                                </span>
                                                <span class="badge bg-soft-primary text-primary ms-2">
                                                    {{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}
                                                </span>
                                                <p class="fs-12 text-muted">
                                                    {{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}
                                                </p>
                                            </td>

                                            <td style="width: 10%">
                                                <span class="fw-semibold mb-1">
                                                    {{ $interaction->client->nom_ter ?? '-' }}
                                                </span>
                                                <p class="fs-12 fw-semibold">
                                                    CC : {{ $interaction->client_id }}
                                                </p>
                                            </td>
                                            <td>
                                                <span class="small">
                                                    {{ $interaction->client->distrito->NOM_DIST ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                {{ $interaction->channel?->name ?? '—' }}
                                            </td>
                                            <td>
                                                {{ floor($interaction->duration / 60) }}m
                                                {{ $interaction->duration % 60 }}s
                                            </td>
                                            <td>
                                                <span class="small text-dark fw-medium">
                                                    {{ $interaction->type->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small mb-1 text-truncate-2-lines text-dark">
                                                    @if ($interaction->lineas_detalle->isNotEmpty())
                                                        @foreach ($interaction->lineas_detalle as $linea)
                                                            <span style="display:block;">{{ $linea->nombre }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <span
                                                    class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                    {{ $interaction->outcomeRelation?->name ?? ' ' }}
                                                </span>
                                            </td>

                                            {{-- <td>
                                                @if ($interaction->next_action_date)
                                                    <div
                                                        class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }}">
                                                        {{ $interaction->client->nom_ter ?? '' }}
                                                    </div>

                                                    <div class="fs-12 text-muted">

                                                        {{ optional($interaction->next_action_date)->format('H:i') }}

                                                        @if ($interaction->nextAction)
                                                            ({{ $interaction->nextAction->name }})
                                                        @endif
                                                    </div>                                                
                                                @endif
                                            </td> --}}

                                            <td class="text-end py-2">

                                                <div class="btn-group btn-group-sm">

                                                    <a class="btn btn-sm btn-light" title="Ver detalles"
                                                        href="{{ route('interactions.show', $interaction->id) }}">

                                                        <i class="feather-eye"></i>

                                                    </a>

                                                </div>

                                            </td>

                                        </tr>

                                    @empty
                                    @endforelse

                                </tbody>

                            </table>

                            @if ($key == 'all')
                                <div class="mt-3">

                                    {{ $interactions->links() }}

                                </div>
                            @endif

                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal de Detalles (Mantiene tu diseño Pastel/Glassmorphism) --}}
    <div class="modal fade" id="detalleInteractionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glassmorphism-modal">
                <div class="modal-header pastel-modal-header">
                    <h5 class="modal-title d-flex align-items-center"><i class="feather-message-circle me-2"></i>
                        Detalles de la Interacción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-primary mb-1" id="modal-id"></h6>
                            <p class="text-muted mb-0" id="modal-fecha"></p>
                        </div>
                    </div>

                    {{-- Tarjeta 1: Info General --}}
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información General</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Cliente</small><span
                                        id="modal-cliente"></span> <span class="text-muted small"
                                        id="modal-client-id"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Asesor</small><span
                                        id="modal-agent"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Motivo</small><span
                                        id="modal-motivo"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Duración</small><span
                                        id="modal-duracion"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Quien Llama --}}
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Datos de Quien Llama</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Nombre</small><span
                                        id="modal-llamante-nombre"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Cédula</small><span
                                        id="modal-llamante-cedula"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Celular</small><span
                                        id="modal-llamante-celular"></span></div>
                                <div class="col-md-3 my-2"><small class="text-muted d-block">Parentesco</small><span
                                        id="modal-llamante-parentesco"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 3: Gestión --}}
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Gestión</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-4 my-2"><small class="text-muted d-block">Línea de
                                        Obligación</small><span id="modal-linea"></span></div>
                                <div class="col-md-4 my-2"><small class="text-muted d-block">Resultado</small><span
                                        id="modal-outcome"></span></div>
                                <div class="col-md-4 my-2"><small class="text-muted d-block">Asignado a</small><span
                                        id="modal-asignado"></span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 4: Notas --}}
                    <div class="card pastel-card mb-0">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Notas</h6>
                        </div>
                        <div class="card-body py-2">
                            <p id="modal-notas" class="mb-0" style="white-space: pre-wrap; font-size: 0.9rem;"></p>
                        </div>
                    </div>

                    {{-- Tarjeta 5: Historial de Seguimientos --}}
                    <div class="card pastel-card mt-3 mb-0">
                        <div
                            class="card-header pastel-card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">Historial de Seguimientos</h6>
                            <span class="badge bg-white text-primary" id="modal-seguimientos-count">0</span>
                        </div>
                        <div class="card-body py-2" style="max-height: 250px; overflow-y: auto;">
                            <div id="modal-seguimientos-container">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pastel-modal-footer">
                    <button type="button" class="btn pastel-btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" />
        <style>
            /* Oculta los botones nativos de DataTables para usar solo tu Dropdown */
            .dt-buttons {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentTable = null;

                function initializeDataTable(tableId) {
                    if (currentTable) {
                        currentTable.destroy();
                        currentTable = null;
                    }
                    let isServerPaginated = (tableId === '#tablaPrincipal');
                    // Generamos una fecha para el nombre del archivo (Ej: 19-03-2026)
                    let fechaHoy = new Date().toLocaleDateString('es-CO').replace(/\//g, '-');
                    let nombrePersonalizado = 'Reporte_Interacciones_' + fechaHoy;
                    currentTable = $(tableId).DataTable({
                        dom: 'Bfrtip',
                        paging: !isServerPaginated,
                        info: !isServerPaginated,
                        buttons: [{
                                extend: 'excelHtml5',
                                className: 'buttons-excel',
                                title: 'Reporte de Interacciones',
                                filename: nombrePersonalizado,
                            },
                            {
                                extend: 'pdfHtml5',
                                className: 'buttons-pdf',
                                title: 'Reporte de Interacciones',
                                filename: nombrePersonalizado,
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                exportOptions: {
                                    modifier: {
                                        search: 'applied'
                                    }
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                className: 'buttons-csv',
                                filename: nombrePersonalizado,
                                exportOptions: {
                                    modifier: {
                                        search: 'applied'
                                    }
                                }
                            }
                        ],
                        pageLength: 20,
                        order: [
                            [0, 'desc']
                        ],
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                        },
                        initComplete: function() {
                            $('#exportButtons').empty();
                            $(this.api().buttons().container()).appendTo('#exportButtons');
                        }
                    });
                }

                setTimeout(function() {
                    initializeDataTable('#tablaPrincipal');
                }, 300);

                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabElement) {
                    tabElement.addEventListener('shown.bs.tab', function(event) {
                        let targetTableId = null;
                        const targetPaneId = event.target.getAttribute('data-bs-target');

                        if (targetPaneId === '#tab-all') targetTableId = '#tablaPrincipal';
                        else if (targetPaneId === '#tab-success') targetTableId = '#tablaExitosos';
                        else if (targetPaneId === '#tab-pending') targetTableId = '#tablaPendientes';
                        else if (targetPaneId === '#tab-today') targetTableId = '#tablaHoy';
                        else if (targetPaneId === '#tab-overdue') targetTableId = '#tablaVencidos';

                        if (targetTableId) {
                            setTimeout(function() {
                                initializeDataTable(targetTableId);
                            }, 150);
                        }
                    });
                });

                // Lógica de Filtros
                function applyFilters() {
                    const searchParams = new URLSearchParams();
                    const searchVal = $('#filterSearch').val();
                    const startDateVal = $('#filterFechaInicio').val();
                    const endDateVal = $('#filterFechaFin').val();

                    if (searchVal) searchParams.set('search', searchVal);
                    if (startDateVal) searchParams.set('start_date', startDateVal);
                    if (endDateVal) searchParams.set('end_date', endDateVal);

                    window.location.href = `${window.location.pathname}?${searchParams.toString()}`;
                }

                $('#applyFilters').on('click', applyFilters);
                $('#clearFilters').on('click', () => window.location.href = window.location.pathname);

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && (e.target.id === 'filterSearch' || e.target.id ===
                            'filterFechaInicio' || e.target.id === 'filterFechaFin')) {
                        e.preventDefault();
                        applyFilters();
                    }
                });

                // Lógica del modal con carga dinámica (AJAX)
                document.addEventListener('click', function(e) {
                    const interactionIdLink = e.target.closest('.interaction-id');

                    if (interactionIdLink) {
                        e.preventDefault();
                        const el = interactionIdLink.dataset;

                        // 1. Llenar datos básicos del modal
                        document.getElementById('modal-id').textContent = `RADICADO #${el.id}`;
                        document.getElementById('modal-fecha').textContent = el.fecha;
                        document.getElementById('modal-cliente').textContent = el.cliente || '—';
                        document.getElementById('modal-client-id').textContent = el.clientId ?
                            `(CC: ${el.clientId})` : '';
                        document.getElementById('modal-agent').textContent = el.agent || '—';
                        document.getElementById('modal-motivo').textContent = el.motivo || '—';
                        document.getElementById('modal-duracion').textContent = el.duracion || '—';
                        document.getElementById('modal-llamante-nombre').textContent = el.llamanteNombre || '—';
                        document.getElementById('modal-llamante-cedula').textContent = el.llamanteCedula || '—';
                        document.getElementById('modal-llamante-celular').textContent = el.llamanteCelular ||
                            '—';
                        document.getElementById('modal-llamante-parentesco').textContent = el
                            .llamanteParentesco || '—';
                        document.getElementById('modal-linea').textContent = el.linea || '—';
                        document.getElementById('modal-outcome').textContent = el.outcome || '—';
                        document.getElementById('modal-asignado').textContent = el.asignado || '—';
                        document.getElementById('modal-notas').textContent = el.notas || 'Sin notas.';

                        // 2. Cargar Seguimientos vía AJAX
                        const container = document.getElementById('modal-seguimientos-container');
                        const counter = document.getElementById('modal-seguimientos-count');

                        // Estado de carga
                        container.innerHTML =
                            '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div><p class="small text-muted mt-2">Cargando historial...</p></div>';
                        counter.textContent = '...';

                        // Petición al servidor (Ruta que agregamos al web.php)
                        fetch(`/interactions/${el.id}/seguimientos`)
                            .then(res => res.json())
                            .then(seguimientos => {
                                counter.textContent = seguimientos.length;
                                if (seguimientos.length > 0) {
                                    let html = '<div class="list-group list-group-flush">';
                                    seguimientos.forEach(seg => {
                                        html += `
                                            <div class="list-group-item px-0 bg-transparent border-bottom">
                                                <div class="d-flex w-100 justify-content-between mb-1">
                                                    <strong class="text-dark" style="font-size: 0.9rem;">${seg.resultado}</strong>
                                                    <small class="text-muted">${seg.fecha_creacion}</small>
                                                </div>
                                                <p class="mb-2 text-muted" style="font-size: 0.85rem;">${seg.notas}</p>
                                                <div class="bg-light p-2 rounded" style="font-size: 0.8rem;">
                                                    <span class="d-block text-secondary mb-1"><i class="feather-user me-1"></i> Agente: <strong>${seg.agente}</strong></span>
                                                    <span class="d-block text-secondary"><i class="feather-calendar me-1"></i> Próx. Acción: <strong>${seg.accion}</strong> (${seg.fecha_accion})</span>
                                                </div>
                                            </div>`;
                                    });
                                    html += '</div>';
                                    container.innerHTML = html;
                                } else {
                                    container.innerHTML =
                                        '<p class="text-muted small mb-0 py-4 text-center">No hay seguimientos registrados.</p>';
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                container.innerHTML =
                                    '<p class="text-danger small py-4 text-center">Error al cargar seguimientos.</p>';
                                counter.textContent = '0';
                            });

                        new bootstrap.Modal(document.getElementById('detalleInteractionModal')).show();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>
