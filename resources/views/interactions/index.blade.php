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
        // IDs para inicializar las tablas mediante AJAX
        // Orden pedido: Vencidos abre por defecto (el primero es el que nace "show active" más
        // abajo), luego Pendientes, Hoy, Próximos a vencer, Exitosos. El catálogo completo sin
        // filtrar ("Todos") vive aparte, en Auditoría (interactions.auditoria) — con filtros
        // finos de usuario/área/cliente/distrito/canal/motivo/resultado que no cabían aquí.
        $tabs = [
            'overdue' => 'tablaVencidos',
            'pending' => 'tablaPendientes',
            'today' => 'tablaHoy',
            'upcoming' => 'tablaProximos',
            'success' => 'tablaExitosos',
        ];
        // Vencidos/Próximos muestran la agenda de seguimiento (a quién se le prometió qué y
        // cuándo), no las columnas genéricas de canal/motivo — son la lista de trabajo del día.
        $tabsConAgenda = ['overdue', 'upcoming'];
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

            {{-- Buscar un cliente puntual: esto es un CRM, y el historial de un asociado debe
                 poder verse completo sin importar qué usuario lo atendió (ej. responder una
                 reclamación) — a diferencia de las pestañas de abajo, que son exclusivas de cada
                 usuario. Busca en TODO el historial (sin restricción de fecha ni de agente) y
                 muestra el resultado aquí mismo, sin salir de esta pantalla. --}}
            <hr class="my-3 opacity-25">
            <form id="formBuscarCliente" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold text-muted small mb-1">
                        <i class="feather-user me-1"></i>Buscar un cliente (todo su historial, lo haya atendido quien sea)
                    </label>
                    <input type="text" id="inputBuscarCliente" class="form-control form-control-sm pastel-input"
                        placeholder="Nombre o cédula del cliente">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i class="feather-search me-1"></i>Buscar cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resultados de "Buscar cliente" — oculto hasta que se busque algo --}}
    <div class="card shadow-sm mb-3 glassmorphism-card d-none" id="cardResultadosCliente">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-0 fw-bold"><i class="feather-user me-2"></i>Historial de <span id="clienteBuscadoLabel"></span></h6>
                    <p class="text-muted mb-0 small">Todas sus interacciones registradas, de cualquier agente.</p>
                </div>
                <button type="button" class="btn btn-sm btn-light" id="cerrarResultadosCliente">
                    <i class="feather-x me-1"></i>Cerrar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle w-100" id="tablaResultadosCliente">
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
                            <th class="d-none">Línea 1</th>
                            <th class="d-none">Línea 2</th>
                            <th class="d-none">Línea 3</th>
                            <th class="d-none">Línea 4</th>
                            <th class="d-none">Línea 5</th>
                            <th class="d-none">Resultado Export</th>
                            <th class="py-2 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tabla mejorada (AQUÍ VA EL CONTENEDOR DE PESTAÑAS) --}}
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Mis Interacciones</h5>
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
            
            {{-- Orden pedido: Vencidos primero (es lo mas urgente, abre aqui por defecto),
                 Pendientes, Hoy, Proximos a vencer, Todos. Exitosos no se pidió en el orden — se
                 deja al final en vez de quitarlo. --}}
            <ul class="nav nav-tabs mb-3 pastel-tabs" id="interactionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab active" id="tab-overdue-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-overdue" type="button" role="tab" aria-controls="tab-overdue"
                        aria-selected="true">
                        <i class="feather-alert-triangle me-2"></i>
                        VENCIDOS
                        <span class="badge bg-soft-danger text-danger" id="badge-overdue">
                            {{ $stats['overdue'] ?? 0 }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-pending-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending"
                        aria-selected="false">
                        <i class="feather-clock me-2"></i>
                        PENDIENTES
                        <span class="badge bg-soft-warning text-warning" id="badge-pending">
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
                        <span class="badge bg-soft-primary text-primary" id="badge-today">
                            {{ $stats['today'] }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-upcoming-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-upcoming" type="button" role="tab" aria-controls="tab-upcoming"
                        aria-selected="false">
                        <i class="feather-clock me-2"></i>
                        PRÓXIMOS A VENCER
                        <span class="badge bg-soft-info text-info" id="badge-upcoming">
                            {{ $stats['upcoming'] ?? 0 }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link pastel-tab" id="tab-success-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-success" type="button" role="tab" aria-controls="tab-success"
                        aria-selected="false" title="Solo tus interacciones exitosas, sin importar tu permiso de 'ver todos'.">
                        <i class="feather-check-circle me-2"></i>
                        MIS EXITOSOS
                        <span class="badge bg-soft-teal text-success">
                            {{ $stats['successful'] }}
                        </span>
                    </button>
                </li>
            </ul>
            <p class="text-muted fs-13 mb-3 d-none d-md-block">
                <i class="feather-info me-1"></i>"Vencidos" y "Próximos a vencer" son la agenda: a quién se le
                prometió una próxima acción y cuándo — <strong>según la gestión más reciente</strong> de cada
                interacción. "Próximos a vencer" mira los siguientes 3 días.
            </p>

            {{-- Contenido limpio de las tablas (DataTables llenará los tbody mediante AJAX) --}}
            <div class="tab-content" id="interactionTabsContent">
                @foreach ($tabs as $key => $tabId)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $key }}" role="tabpanel">
                        @if ($key === 'success')
                            {{-- Filtro propio de Exitosos (independiente del filtro general de arriba):
                                 son 18.000+ interacciones exitosas en todo el histórico, así que por
                                 defecto muestra solo el último mes; "Ver todos" lo quita. --}}
                            <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                                <div>
                                    <label class="form-label fw-semibold text-muted small mb-1">Desde</label>
                                    <input type="date" id="filterExitosoDesde" class="form-control form-control-sm pastel-input"
                                           value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                </div>
                                <div>
                                    <label class="form-label fw-semibold text-muted small mb-1">Hasta</label>
                                    <input type="date" id="filterExitosoHasta" class="form-control form-control-sm pastel-input"
                                           value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="verTodosExitosos">
                                    <i class="feather-list me-1"></i>Ver todos
                                </button>
                                <span class="text-muted fs-13" id="exitosoFiltroTexto">Mostrando el último mes.</span>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle w-100" id="{{ $tabId }}">
                                <thead class="table-light pastel-thead">
                                    @if (in_array($key, $tabsConAgenda))
                                        <tr>
                                            <th class="py-2">ID</th>
                                            <th class="py-2">Cliente</th>
                                            <th class="py-2">Asignado a</th>
                                            <th class="py-2">Próxima acción</th>
                                            <th class="py-2">Programada para</th>
                                            <th class="py-2">Notas</th>
                                            <th class="py-2 text-end">Acciones</th>
                                        </tr>
                                    @else
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
                                            {{-- Columnas ocultas para DataTables Excel Export --}}
                                            <th class="d-none">Línea 1</th>
                                            <th class="d-none">Línea 2</th>
                                            <th class="d-none">Línea 3</th>
                                            <th class="d-none">Línea 4</th>
                                            <th class="d-none">Línea 5</th>
                                            <th class="d-none">Resultado Export</th>

                                            <th class="py-2 text-end">Acciones</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    {{-- DataTables Server-Side AJAX insertará las filas aquí --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal de Detalles --}}
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
                        <div class="card-header pastel-card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">Historial de Seguimientos</h6>
                            <span class="badge bg-white text-primary" id="modal-seguimientos-count">0</span>
                        </div>
                        <div class="card-body py-2" style="max-height: 250px; overflow-y: auto;">
                            <div id="modal-seguimientos-container"></div>
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
            .dt-buttons { display: none !important; }
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
                const priorityColors = { '1': 'success', '2': 'warning', '3': 'danger' };

                function initializeDataTable(tableId, tabType) {
                    if (currentTable) {
                        currentTable.destroy();
                        $(tableId + ' tbody').empty();
                    }

                    let fechaHoy = new Date().toLocaleDateString('es-CO').replace(/\//g, '-');
                    let esAgenda = (tabType === 'overdue' || tabType === 'upcoming');

                    // Vencidos/Próximos son la agenda de seguimiento: a quién se le prometió qué
                    // y cuándo (ver InteractionController@index, bloque "proxima_*"). No tiene
                    // sentido mostrarles canal/motivo/línea de crédito ahí — lo urgente es la fecha
                    // y la nota, con acceso directo a registrar el siguiente seguimiento.
                    let columnasAgenda = [
                        {
                            data: 'id',
                            render: function(data, type, row) {
                                return `<a class="interaction-id fw-bold text-decoration-none pastel-link"
                                    href="#" data-id="${row.id}" data-fecha="${row.fecha}" data-cliente="${row.cliente_nombre}"
                                    data-client-id="${row.cliente_cc}" data-agent="${row.agente}" data-motivo="${row.motivo}"
                                    data-duracion="${row.duracion}" data-outcome="${row.resultado}" data-notas="${row.notas}"
                                    data-linea="${row.linea_1 || '—'}" data-asignado="${row.asignado}" data-llamante-nombre="${row.llamante_nombre}"
                                    data-llamante-cedula="${row.llamante_cedula}" data-llamante-celular="${row.llamante_celular}"
                                    data-llamante-parentesco="${row.llamante_parentesco}">
                                    #${row.id}
                                </a>`;
                            }
                        },
                        {
                            data: 'cliente_nombre',
                            render: function(data, type, row) {
                                return `<span class="fw-semibold mb-1">${row.cliente_nombre}</span>
                                        <p class="fs-12 fw-semibold mb-0">CC : ${row.cliente_cc}</p>`;
                            }
                        },
                        { data: 'asignado', render: data => `<span class="small">${data}</span>` },
                        {
                            data: 'proxima_accion',
                            render: data => data
                                ? `<span class="badge bg-soft-secondary text-dark">${data}</span>`
                                : '<span class="text-muted small">—</span>'
                        },
                        {
                            data: 'proxima_fecha',
                            render: function(data, type, row) {
                                if (!data) return '<span class="text-muted small">—</span>';
                                let color = row.proxima_vencida ? 'danger' : 'info';
                                return `<span class="small d-block">${data}</span>
                                        <span class="badge bg-soft-${color} text-${color}">${row.proxima_texto || ''}</span>`;
                            }
                        },
                        {
                            data: 'proxima_notas',
                            render: data => `<span class="small text-truncate-2-lines d-block" style="max-width: 280px;">${data || 'Sin notas.'}</span>`
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-end py-2',
                            render: function(data, type, row) {
                                let url = "{{ route('interactions.show', ':interaction') }}".replace(':interaction', row.id);
                                return `<div class="btn-group btn-group-sm">
                                            <a class="btn btn-sm btn-primary" title="Registrar seguimiento" href="${url}?seguimiento=1">
                                                <i class="feather-check-circle me-1"></i>Gestionar
                                            </a>
                                        </div>`;
                            }
                        }
                    ];

                    currentTable = $(tableId).DataTable({
                        serverSide: true,
                        processing: true,
                        deferRender: true,
                        ajax: {
                            url: window.location.pathname,
                            data: function(d) {
                                d.tab = tabType;
                                // Exitosos tiene su propio rango de fechas (por defecto el último
                                // mes), independiente del filtro general de arriba — así cambiar de
                                // pestaña nunca filtra "Todos" o "Vencidos" por accidente.
                                if (tabType === 'success') {
                                    d.start_date = $('#filterExitosoDesde').val();
                                    d.end_date = $('#filterExitosoHasta').val();
                                } else {
                                    d.start_date = $('#filterFechaInicio').val();
                                    d.end_date = $('#filterFechaFin').val();
                                }
                                // d.search.value is handled by DataTables automatically
                            }
                        },
                        columns: esAgenda ? columnasAgenda : [
                            { 
                                data: 'id',
                                render: function(data, type, row) {
                                    let html = `<a class="interaction-id fw-bold text-decoration-none pastel-link" 
                                        href="#" data-id="${row.id}" data-fecha="${row.fecha}" data-cliente="${row.cliente_nombre}" 
                                        data-client-id="${row.cliente_cc}" data-agent="${row.agente}" data-motivo="${row.motivo}" 
                                        data-duracion="${row.duracion}" data-outcome="${row.resultado}" data-notas="${row.notas}" 
                                        data-linea="${row.linea_1 || '—'}" data-asignado="${row.asignado}" data-llamante-nombre="${row.llamante_nombre}" 
                                        data-llamante-cedula="${row.llamante_cedula}" data-llamante-celular="${row.llamante_celular}" 
                                        data-llamante-parentesco="${row.llamante_parentesco}">
                                        #${row.id}
                                    </a>`;
                                    if (row.archivo) {
                                        html += `<a href="${row.archivo}" target="_blank" class="ms-2 text-info"><i class="fas fa-paperclip"></i></a>`;
                                    }
                                    return html;
                                }
                            },
                            {
                                data: 'agente',
                                render: function(data, type, row) {
                                    return `<span class="fw-semibold mb-1">${row.agente}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">${row.agente_area}</span>
                                            <p class="fs-12 text-muted mb-0">${row.agente_cargo}</p>`;
                                }
                            },
                            {
                                data: 'cliente_nombre',
                                render: function(data, type, row) {
                                    return `<span class="fw-semibold mb-1">${row.cliente_nombre}</span>
                                            <p class="fs-12 fw-semibold mb-0">CC : ${row.cliente_cc}</p>`;
                                }
                            },
                            { data: 'distrito', render: data => `<span class="small">${data}</span>` },
                            { data: 'fecha' },
                            { data: 'canal' },
                            { data: 'duracion' },
                            { data: 'motivo', render: data => `<span class="small text-dark fw-medium">${data}</span>` },
                            {
                                data: 'resultado',
                                render: function(data, type, row) {
                                    let lineasHtml = row.lineas_array.map(l => `<span style="display:block;">${l}</span>`).join('');
                                    let color = priorityColors[row.outcome_val] || 'secondary';
                                    return `<div class="small mb-1 text-truncate-2-lines text-dark">${lineasHtml}</div>
                                            <span class="badge bg-soft-${color} text-${color}">${row.resultado}</span>`;
                                }
                            },
                            { data: 'linea_1', visible: false },
                            { data: 'linea_2', visible: false },
                            { data: 'linea_3', visible: false },
                            { data: 'linea_4', visible: false },
                            { data: 'linea_5', visible: false },
                            { data: 'resultado', visible: false },
                            {
                                data: null,
                                orderable: false,
                                className: 'text-end py-2',
                                render: function(data, type, row) {
                                    let url = "{{ route('interactions.show', ':interaction') }}";
                                    url = url.replace(':interaction', row.id);
                                    return `<div class="btn-group btn-group-sm">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="${url}">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </div>`;
                                }
                            }
                        ],
                        dom: 'Bfrtip',
                        pageLength: 20,
                        order: [[0, 'desc']],
                        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                className: 'buttons-excel',
                                title: 'Reporte de Interacciones',
                                filename: 'Reporte_Interacciones_' + fechaHoy,
                                exportOptions: {
                                    columns: esAgenda ? ':visible' : [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                className: 'buttons-pdf',
                                title: 'Reporte de Interacciones',
                                filename: 'Reporte_Interacciones_' + fechaHoy,
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                exportOptions: {
                                    columns: esAgenda ? ':visible' : [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14]
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                className: 'buttons-csv',
                                filename: 'Reporte_Interacciones_' + fechaHoy,
                                exportOptions: {
                                    columns: esAgenda ? ':visible' : [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14]
                                }
                            }
                        ],
                        initComplete: function() {
                            $('#exportButtons').empty();
                            $(this.api().buttons().container()).appendTo('#exportButtons');
                        }
                    });
                }

                // Inicializar primera tabla (Vencidos: la pestaña que abre por defecto)
                setTimeout(() => initializeDataTable('#tablaVencidos', 'overdue'), 300);

                // Eventos de pestañas
                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabElement) {
                    tabElement.addEventListener('shown.bs.tab', function(event) {
                        const targetPaneId = event.target.getAttribute('data-bs-target');
                        if (targetPaneId === '#tab-success') initializeDataTable('#tablaExitosos', 'success');
                        else if (targetPaneId === '#tab-pending') initializeDataTable('#tablaPendientes', 'pending');
                        else if (targetPaneId === '#tab-today') initializeDataTable('#tablaHoy', 'today');
                        else if (targetPaneId === '#tab-overdue') initializeDataTable('#tablaVencidos', 'overdue');
                        else if (targetPaneId === '#tab-upcoming') initializeDataTable('#tablaProximos', 'upcoming');
                    });
                });

                // Buscar Cliente (CRM): historial completo, sin importar quién lo atendió — se
                // muestra aquí mismo, sin salir de la pantalla. Tabla aparte de las pestañas
                // (currentTable), initComplete-libre (sin botones de exportar, es una consulta
                // rápida puntual, no un reporte).
                let tablaCliente = null;
                $('#formBuscarCliente').on('submit', function(e) {
                    e.preventDefault();
                    const termino = $('#inputBuscarCliente').val().trim();
                    if (!termino) return;

                    $('#cardResultadosCliente').removeClass('d-none');
                    $('#clienteBuscadoLabel').text('"' + termino + '"');

                    if (tablaCliente) {
                        tablaCliente.ajax.url("{{ route('interactions.buscar-cliente') }}").load();
                        return;
                    }

                    tablaCliente = $('#tablaResultadosCliente').DataTable({
                        serverSide: true,
                        processing: true,
                        deferRender: true,
                        ajax: {
                            url: "{{ route('interactions.buscar-cliente') }}",
                            data: function(d) { d.cliente = $('#inputBuscarCliente').val().trim(); }
                        },
                        columns: [
                            {
                                data: 'id',
                                render: function(data, type, row) {
                                    let html = `<a class="interaction-id fw-bold text-decoration-none pastel-link"
                                        href="#" data-id="${row.id}" data-fecha="${row.fecha}" data-cliente="${row.cliente_nombre}"
                                        data-client-id="${row.cliente_cc}" data-agent="${row.agente}" data-motivo="${row.motivo}"
                                        data-duracion="${row.duracion}" data-outcome="${row.resultado}" data-notas="${row.notas}"
                                        data-linea="${row.linea_1 || '—'}" data-asignado="${row.asignado}" data-llamante-nombre="${row.llamante_nombre}"
                                        data-llamante-cedula="${row.llamante_cedula}" data-llamante-celular="${row.llamante_celular}"
                                        data-llamante-parentesco="${row.llamante_parentesco}">
                                        #${row.id}
                                    </a>`;
                                    if (row.archivo) {
                                        html += `<a href="${row.archivo}" target="_blank" class="ms-2 text-info"><i class="fas fa-paperclip"></i></a>`;
                                    }
                                    return html;
                                }
                            },
                            {
                                data: 'agente',
                                render: (data, type, row) => `<span class="fw-semibold mb-1">${row.agente}</span>
                                        <span class="badge bg-soft-primary text-primary ms-2">${row.agente_area}</span>
                                        <p class="fs-12 text-muted mb-0">${row.agente_cargo}</p>`
                            },
                            {
                                data: 'cliente_nombre',
                                render: (data, type, row) => `<span class="fw-semibold mb-1">${row.cliente_nombre}</span>
                                        <p class="fs-12 fw-semibold mb-0">CC : ${row.cliente_cc}</p>`
                            },
                            { data: 'distrito', render: data => `<span class="small">${data}</span>` },
                            { data: 'fecha' },
                            { data: 'canal' },
                            { data: 'duracion' },
                            { data: 'motivo', render: data => `<span class="small text-dark fw-medium">${data}</span>` },
                            {
                                data: 'resultado',
                                render: function(data, type, row) {
                                    let lineasHtml = row.lineas_array.map(l => `<span style="display:block;">${l}</span>`).join('');
                                    let color = priorityColors[row.outcome_val] || 'secondary';
                                    return `<div class="small mb-1 text-truncate-2-lines text-dark">${lineasHtml}</div>
                                            <span class="badge bg-soft-${color} text-${color}">${row.resultado}</span>`;
                                }
                            },
                            { data: 'linea_1', visible: false },
                            { data: 'linea_2', visible: false },
                            { data: 'linea_3', visible: false },
                            { data: 'linea_4', visible: false },
                            { data: 'linea_5', visible: false },
                            { data: 'resultado', visible: false },
                            {
                                data: null,
                                orderable: false,
                                className: 'text-end py-2',
                                render: function(data, type, row) {
                                    let url = "{{ route('interactions.show', ':interaction') }}".replace(':interaction', row.id);
                                    return `<div class="btn-group btn-group-sm">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="${url}">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </div>`;
                                }
                            }
                        ],
                        dom: 'rtip',
                        pageLength: 10,
                        order: [[0, 'desc']],
                        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
                    });
                });

                $('#cerrarResultadosCliente').on('click', function() {
                    $('#cardResultadosCliente').addClass('d-none');
                    $('#inputBuscarCliente').val('');
                });

                // Botones de filtro
                $('#applyFilters').on('click', function() {
                    if (currentTable) {
                        currentTable.search($('#filterSearch').val()).draw();
                    }
                });

                // Filtro propio de Exitosos: cambiar las fechas o pulsar "Ver todos" vuelve a
                // pedir los datos (currentTable.draw() relee los inputs vía ajax.data de arriba).
                // Solo importa mientras esa pestaña está activa, que es cuando estos campos son
                // visibles/alcanzables.
                $('#filterExitosoDesde, #filterExitosoHasta').on('change', function () {
                    $('#exitosoFiltroTexto').text('Filtrado por fecha.');
                    if (currentTable) currentTable.draw();
                });
                $('#verTodosExitosos').on('click', function () {
                    $('#filterExitosoDesde').val('');
                    $('#filterExitosoHasta').val('');
                    $('#exitosoFiltroTexto').text('Mostrando todo el histórico.');
                    if (currentTable) currentTable.draw();
                });

                $('#clearFilters').on('click', function() {
                    $('#filterSearch').val('');
                    $('#filterFechaInicio').val('');
                    $('#filterFechaFin').val('');
                    if (currentTable) {
                        currentTable.search('').draw();
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && (e.target.id === 'filterSearch' || e.target.id === 'filterFechaInicio' || e.target.id === 'filterFechaFin')) {
                        e.preventDefault();
                        if (currentTable) currentTable.search($('#filterSearch').val()).draw();
                    }
                });

                // Lógica del modal
                document.addEventListener('click', function(e) {
                    const interactionIdLink = e.target.closest('.interaction-id');

                    if (interactionIdLink) {
                        e.preventDefault();
                        const el = interactionIdLink.dataset;

                        document.getElementById('modal-id').textContent = `RADICADO #${el.id}`;
                        document.getElementById('modal-fecha').textContent = el.fecha;
                        document.getElementById('modal-cliente').textContent = el.cliente || '—';
                        document.getElementById('modal-client-id').textContent = el.clientId ? `(CC: ${el.clientId})` : '';
                        document.getElementById('modal-agent').textContent = el.agent || '—';
                        document.getElementById('modal-motivo').textContent = el.motivo || '—';
                        document.getElementById('modal-duracion').textContent = el.duracion || '—';
                        document.getElementById('modal-llamante-nombre').textContent = el.llamanteNombre || '—';
                        document.getElementById('modal-llamante-cedula').textContent = el.llamanteCedula || '—';
                        document.getElementById('modal-llamante-celular').textContent = el.llamanteCelular || '—';
                        document.getElementById('modal-llamante-parentesco').textContent = el.llamanteParentesco || '—';
                        document.getElementById('modal-linea').textContent = el.linea || '—';
                        document.getElementById('modal-outcome').textContent = el.outcome || '—';
                        document.getElementById('modal-asignado').textContent = el.asignado || '—';
                        document.getElementById('modal-notas').textContent = el.notas || 'Sin notas.';

                        const container = document.getElementById('modal-seguimientos-container');
                        const counter = document.getElementById('modal-seguimientos-count');

                        container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div><p class="small text-muted mt-2">Cargando historial...</p></div>';
                        counter.textContent = '...';

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
                                                    ${seg.archivo ? `<a href="${seg.archivo}" target="_blank" class="d-block text-primary mt-1"><i class="feather-paperclip me-1"></i>Ver soporte adjunto</a>` : ''}
                                                </div>
                                            </div>`;
                                    });
                                    html += '</div>';
                                    container.innerHTML = html;
                                } else {
                                    container.innerHTML = '<p class="text-muted small mb-0 py-4 text-center">No hay seguimientos registrados.</p>';
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                container.innerHTML = '<p class="text-danger small py-4 text-center">Error al cargar seguimientos.</p>';
                                counter.textContent = '0';
                            });

                        new bootstrap.Modal(document.getElementById('detalleInteractionModal')).show();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>