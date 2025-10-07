<x-base-layout>
    {{-- ========================================================================= --}}
    {{-- ✅ MENSAJES DE ALERTA (ÉXITO/ERROR) --}}
    {{-- ========================================================================= --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-pill text-center mx-5 mt-3 py-2 alert-dismissible fade show" role="alert" id="success-alert">
            <i class="feather-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-pill text-center mx-5 mt-3 py-2 alert-dismissible fade show" role="alert" id="error-alert">
            <i class="feather-x-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- ⏳ LOADING OVERLAY --}}
    {{-- ========================================================================= --}}
    <div id="loading-overlay" class="loading-overlay d-none">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Procesando...</p>
        </div>
    </div>

    <div class="container-fluid py-4">
        {{-- ========================================================================= --}}
        {{-- 📊 ENCABEZADO Y ESTADÍSTICAS --}}
        {{-- ========================================================================= --}}
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 text-primary-emphasis me-3">
                        <i class="bi bi-chat-dots me-2"></i>Interacciones
                    </h4>
                    <div class="d-flex gap-2">
                        @if($interactions->total() > 0)
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                <i class="bi bi-graph-up me-1"></i>
                                {{ $interactions->total() }} registros
                            </span>
                        @endif
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2" id="selected-count" style="display: none;">
                            <i class="bi bi-check2-square me-1"></i>
                            <span id="selected-number">0</span> seleccionados
                        </span>
                    </div>
                </div>

                {{-- Estadísticas rápidas --}}
                <div class="row mt-3">
                    <div class="col-auto">
                        <div class="d-flex gap-3">
                            <div class="stat-item">
                                <span class="text-muted small">Hoy:</span>
                                <span class="fw-bold text-success">{{ $todayInteractions ?? 0 }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="text-muted small">Esta semana:</span>
                                <span class="fw-bold text-info">{{ $weekInteractions ?? 0 }}</span>
                            </div>
                            <div class="stat-item">
                                <span class="text-muted small">Exitosas:</span>
                                <span class="fw-bold text-primary">{{ $successfulInteractions ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ⚡ ACCIONES GLOBALES --}}
            {{-- ========================================================================= --}}
            <div class="col-lg-4">
                <div class="d-flex gap-2 justify-content-lg-end">
                    {{-- Acciones rápidas --}}
                    <div class="btn-group" role="group" aria-label="Acciones rápidas">
                        <button type="button" class="btn btn-outline-secondary" id="refresh-btn" data-bs-toggle="tooltip" title="Actualizar datos (Ctrl+R)">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="toggle-view" data-bs-toggle="tooltip" title="Cambiar vista (Ctrl+V)">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="fullscreen-btn" data-bs-toggle="tooltip" title="Pantalla completa (F11)">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    </div>

                    {{-- Acciones principales --}}
                    <a href="{{ route('interactions.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm" data-bs-toggle="tooltip" title="Nueva interacción (Ctrl+N)">
                        <i class="bi bi-plus-lg me-2"></i> Nueva Interacción
                    </a>

                    {{-- Menú de exportación --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" title="Opciones de exportación">
                            <i class="bi bi-box-arrow-up me-2"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-download me-1"></i>Exportar datos
                                </h6>
                            </li>
                            <li><button class="dropdown-item export-btn" data-format="excel"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel (.xlsx)</button></li>
                            <li><button class="dropdown-item export-btn" data-format="csv"><i class="bi bi-filetype-csv me-2 text-info"></i> CSV</button></li>
                            <li><button class="dropdown-item export-btn" data-format="pdf"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" id="print-btn"><i class="bi bi-printer me-2 text-primary"></i> Imprimir</button></li>
                            <li><button class="dropdown-item" id="share-btn"><i class="bi bi-share me-2 text-warning"></i> Compartir</button></li>
                        </ul>
                    </div>

                    {{-- Acciones múltiples (aparece cuando hay selecciones) --}}
                    <div class="dropdown" id="bulk-actions" style="display: none;">
                        <button class="btn btn-warning dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i> Acciones
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><button class="dropdown-item bulk-action-btn" data-action="delete"><i class="bi bi-trash me-2 text-danger"></i> Eliminar seleccionadas</button></li>
                            <li><button class="dropdown-item bulk-action-btn" data-action="export"><i class="bi bi-download me-2 text-primary"></i> Exportar seleccionadas</button></li>
                            <li><button class="dropdown-item bulk-action-btn" data-action="assign"><i class="bi bi-person-plus me-2 text-info"></i> Asignar agente</button></li>
                            <li><button class="dropdown-item bulk-action-btn" data-action="status"><i class="bi bi-arrow-repeat me-2 text-warning"></i> Cambiar estado</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- 🔍 FILTROS AVANZADOS --}}
        {{-- ========================================================================= --}}
        <div class="card shadow-sm mb-4" id="filters-card">
            <div class="card-header bg-gradient-light d-flex justify-content-between align-items-center p-3" id="filterHeader">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0 text-secondary-emphasis me-3">
                        <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                            <i class="bi bi-funnel me-2"></i> Filtros Avanzados
                        </button>
                    </h6>
                    <div class="active-filters d-flex gap-1" id="active-filters">
                        {{-- Los filtros activos aparecerán aquí dinámicamente --}}
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-sm btn-outline-secondary" id="save-filter-btn" data-bs-toggle="tooltip" title="Guardar filtros">
                        <i class="bi bi-bookmark"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="reset-filters-btn" data-bs-toggle="tooltip" title="Limpiar todos los filtros">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="filtersCollapse">
                <div class="card-body">
                    <form action="{{ route('interactions.index') }}" method="GET" id="filters-form">
                        {{-- Filtros básicos --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="bi bi-search me-1"></i>Búsqueda
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="q" id="search" value="{{ request('q') }}"
                                           class="form-control border-0 rounded-end shadow-sm"
                                           placeholder="Cliente, agente o descripción..."
                                           aria-label="Buscar"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary border-0" type="button" id="clear-search" style="display: none;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="form-text">Buscar en cliente, agente, descripción o notas</div>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Tipo
                                </label>
                                <select name="type" id="type" class="form-select border-0 shadow-sm">
                                    <option value="">Todos los tipos</option>
                                    <option value="Llamada" {{ request('type') == 'Llamada' ? 'selected' : '' }}>📞 Llamada</option>
                                    <option value="Correo" {{ request('type') == 'Correo' ? 'selected' : '' }}>📧 Correo</option>
                                    <option value="Reunión" {{ request('type') == 'Reunión' ? 'selected' : '' }}>🤝 Reunión</option>
                                    <option value="WhatsApp" {{ request('type') == 'WhatsApp' ? 'selected' : '' }}>💬 WhatsApp</option>
                                    <option value="SMS" {{ request('type') == 'SMS' ? 'selected' : '' }}>📱 SMS</option>
                                    <option value="Chat" {{ request('type') == 'Chat' ? 'selected' : '' }}>💭 Chat</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="outcome" class="form-label">
                                    <i class="bi bi-flag me-1"></i>Resultado
                                </label>
                                <select name="outcome" id="outcome" class="form-select border-0 shadow-sm">
                                    <option value="">Todos los resultados</option>
                                    <option value="Exitoso" {{ request('outcome') == 'Exitoso' ? 'selected' : '' }}>✅ Exitoso</option>
                                    <option value="Pendiente" {{ request('outcome') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="Fallido" {{ request('outcome') == 'Fallido' ? 'selected' : '' }}>❌ Fallido</option>
                                    <option value="Seguimiento" {{ request('outcome') == 'Seguimiento' ? 'selected' : '' }}>🔄 Seguimiento</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="from" class="form-label">
                                    <i class="bi bi-calendar-event me-1"></i>Desde
                                </label>
                                <input type="date" name="from" id="from" value="{{ request('from') }}"
                                       class="form-control border-0 shadow-sm">
                            </div>
                            <div class="col-md-2">
                                <label for="to" class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>Hasta
                                </label>
                                <input type="date" name="to" id="to" value="{{ request('to') }}"
                                       class="form-control border-0 shadow-sm">
                            </div>
                        </div>

                        {{-- Filtros avanzados adicionales --}}
                        <div class="row g-3 mb-3" id="advanced-filters">
                            <div class="col-md-3">
                                <label for="agent" class="form-label">
                                    <i class="bi bi-person me-1"></i>Agente
                                </label>
                                <select name="agent" id="agent" class="form-select border-0 shadow-sm">
                                    <option value="">Todos los agentes</option>
                                    @foreach($agents ?? [] as $agent)
                                        <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Prioridad
                                </label>
                                <select name="priority" id="priority" class="form-select border-0 shadow-sm">
                                    <option value="">Todas las prioridades</option>
                                    <option value="Alta" {{ request('priority') == 'Alta' ? 'selected' : '' }}>🔴 Alta</option>
                                    <option value="Media" {{ request('priority') == 'Media' ? 'selected' : '' }}>🟡 Media</option>
                                    <option value="Baja" {{ request('priority') == 'Baja' ? 'selected' : '' }}>🟢 Baja</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="duration_min" class="form-label">
                                    <i class="bi bi-clock me-1"></i>Duración (min)
                                </label>
                                <div class="input-group">
                                    <input type="number" name="duration_min" id="duration_min" value="{{ request('duration_min') }}"
                                           class="form-control border-0 shadow-sm" placeholder="Min" min="0">
                                    <span class="input-group-text bg-light border-0">-</span>
                                    <input type="number" name="duration_max" id="duration_max" value="{{ request('duration_max') }}"
                                           class="form-control border-0 shadow-sm" placeholder="Max" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">
                                    <i class="bi bi-sort-down me-1"></i>Ordenar por
                                </label>
                                <select name="sort" id="sort" class="form-select border-0 shadow-sm">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más recientes</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                                    <option value="client" {{ request('sort') == 'client' ? 'selected' : '' }}>Por cliente</option>
                                    <option value="agent" {{ request('sort') == 'agent' ? 'selected' : '' }}>Por agente</option>
                                    <option value="type" {{ request('sort') == 'type' ? 'selected' : '' }}>Por tipo</option>
                                    <option value="outcome" {{ request('sort') == 'outcome' ? 'selected' : '' }}>Por resultado</option>
                                </select>
                            </div>
                        </div>

                        {{-- Filtros de fecha rápidos --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 me-1"></i>Filtros rápidos de fecha
                                </label>
                                <div class="btn-group" role="group" aria-label="Filtros de fecha">
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="today">Hoy</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="yesterday">Ayer</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="week">Esta semana</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="month">Este mes</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="quarter">Este trimestre</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-filter" data-period="year">Este año</button>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de acción de filtros --}}
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary shadow-sm" id="apply-filters-btn">
                                        <i class="bi bi-funnel me-2"></i> Aplicar Filtros
                                    </button>
                                    <a href="{{ route('interactions.index') }}" class="btn btn-outline-secondary shadow-sm">
                                        <i class="bi bi-x-lg me-2"></i> Limpiar
                                    </a>
                                    <button type="button" class="btn btn-outline-info shadow-sm" id="auto-refresh-btn" data-bs-toggle="tooltip" title="Activar actualización automática">
                                        <i class="bi bi-arrow-repeat me-2"></i> Auto-actualizar
                                    </button>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="real-time-filter" checked>
                                    <label class="form-check-label" for="real-time-filter">
                                        Filtrado en tiempo real
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- 👁️ CONTROLES DE VISTA Y SELECCIÓN --}}
        {{-- ========================================================================= --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label" for="select-all">
                            Seleccionar todo
                        </label>
                    </div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Vista">
                        <input type="radio" class="btn-check" name="view-mode" id="table-view" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="table-view" data-bs-toggle="tooltip" title="Vista de tabla">
                            <i class="bi bi-table"></i>
                        </label>
                        <input type="radio" class="btn-check" name="view-mode" id="card-view" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="card-view" data-bs-toggle="tooltip" title="Vista de tarjetas">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </label>
                        <input type="radio" class="btn-check" name="view-mode" id="list-view" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="list-view" data-bs-toggle="tooltip" title="Vista de lista">
                            <i class="bi bi-list"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end align-items-center gap-2">
                    <span class="text-muted small">Mostrar:</span>
                    <select class="form-select form-select-sm w-auto" id="per-page">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-muted small">por página</span>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- 📋 CONTENIDO PRINCIPAL (LISTADO DE INTERACCIONES) --}}
        {{-- ========================================================================= --}}
        <div id="interactions-container">
            @forelse ($interactions as $interaction)
                {{-- Vista de tarjetas para móvil (mejorada) --}}
                <div class="card shadow-sm mb-3 d-lg-none interaction-card" data-interaction-id="{{ $interaction->id }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="form-check me-2">
                                    <input class="form-check-input interaction-checkbox" type="checkbox" value="{{ $interaction->id }}">
                                </div>
                                <h6 class="mb-0 text-primary-emphasis">
                                    <i class="bi bi-hash"></i>{{ $interaction->id }}
                                </h6>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="{{ route('interactions.show', $interaction) }}"><i class="bi bi-eye me-2 text-primary"></i> Ver detalles</a></li>
                                    <li><a class="dropdown-item" href="{{ route('interactions.edit', $interaction) }}"><i class="bi bi-pencil me-2 text-info"></i> Editar</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateInteraction({{ $interaction->id }})"><i class="bi bi-files me-2 text-warning"></i> Duplicar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger delete-interaction" href="#" data-id="{{ $interaction->id }}"><i class="bi bi-trash me-2"></i> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    @if($interaction->client)
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($interaction->client->apl1, 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong class="text-primary">{{ $interaction->client->apl1 }}</strong>
                                            <br><small class="text-muted">{{ $interaction->client->cod_ter }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Agente:</small>
                                <div class="fw-medium">{{ $interaction->agent->name ?? 'Sin asignar' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Fecha:</small>
                                <div class="fw-medium">{{ $interaction->interaction_date->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Tipo:</small>
                                <div>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1">
                                        {{ $interaction->interaction_type }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Resultado:</small>
                                <div>
                                    @php
                                        $outcomeBadges = [
                                            'Exitoso' => 'success',
                                            'Pendiente' => 'warning',
                                            'Fallido' => 'danger',
                                            'Seguimiento' => 'info'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $outcomeBadges[$interaction->outcome] ?? 'light' }} text-{{ $outcomeBadges[$interaction->outcome] ?? 'secondary' }}-emphasis rounded-pill px-2 py-1">
                                        {{ $interaction->outcome }}
                                    </span>
                                </div>
                            </div>
                            @if($interaction->notes)
                                <div class="col-12 mt-2">
                                    <small class="text-muted">Notas:</small>
                                    <div class="text-truncate small" style="max-height: 40px;">
                                        {{ Str::limit($interaction->notes, 100) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        {{-- Indicadores adicionales --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <div class="d-flex gap-1">
                                @if($interaction->has_attachments ?? false)
                                    <span class="badge bg-info-subtle text-info-emphasis" data-bs-toggle="tooltip" title="Tiene archivos adjuntos">
                                        <i class="bi bi-paperclip"></i>
                                    </span>
                                @endif
                                @if($interaction->is_urgent ?? false)
                                    <span class="badge bg-danger-subtle text-danger-emphasis" data-bs-toggle="tooltip" title="Urgente">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </span>
                                @endif
                                @if($interaction->follow_up_date && $interaction->follow_up_date->isFuture())
                                    <span class="badge bg-warning-subtle text-warning-emphasis" data-bs-toggle="tooltip" title="Seguimiento programado">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                {{ $interaction->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Estado vacío mejorado --}}
                <div class="card card-body text-center py-5 empty-state">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-3">No se encontraron interacciones</h5>
                    <p class="text-muted">No hay interacciones que coincidan con los criterios de búsqueda.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('interactions.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Limpiar filtros
                        </a>
                        <a href="{{ route('interactions.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i> Crear primera interacción
                        </a>
                    </div>
                </div>
            @endforelse
            {{-- Vista de tabla para desktop (mejorada) --}}
            @if($interactions->count() > 0)
                <div class="card shadow-sm d-none d-lg-block" id="table-container">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 interaction-table">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th scope="col" class="py-3 ps-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all-table">
                                            </div>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="id">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">#</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="client">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">Cliente</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="agent">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">Agente</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="date">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">Fecha</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="type">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">Tipo</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 sortable" data-sort="outcome">
                                            <a href="#" class="text-decoration-none text-dark d-flex align-items-center">
                                                <span class="small text-uppercase">Resultado</span>
                                                <i class="bi bi-arrow-down-up ms-1 small text-muted"></i>
                                            </a>
                                        </th>
                                        <th scope="col" class="py-3 small text-uppercase">Indicadores</th>
                                        <th scope="col" class="py-3 pe-4 small text-uppercase text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($interactions as $interaction)
                                        <tr data-interaction-id="{{ $interaction->id }}" class="interaction-row">
                                            <td class="ps-4">
                                                <div class="form-check">
                                                    <input class="form-check-input interaction-checkbox" type="checkbox" value="{{ $interaction->id }}">
                                                </div>
                                            </td>
                                            <td class="fw-medium">{{ $interaction->id }}</td>
                                            <td>
                                                @if($interaction->client)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm flex-shrink-0 me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                            {{ strtoupper(substr($interaction->client->apl1, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <p class="fw-bold mb-0 text-truncate client-name" style="max-width: 150px;">
                                                                {{ $interaction->client->apl1 }}
                                                            </p>
                                                            <span class="text-muted small">{{ $interaction->client->cod_ter }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($interaction->agent)
                                                        <div class="avatar avatar-xs bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            {{ strtoupper(substr($interaction->agent->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    {{ $interaction->agent->name ?? 'Sin asignar' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $interaction->interaction_date->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $interaction->interaction_date->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1">
                                                    {{ $interaction->interaction_type }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $outcomeBadges = [
                                                        'Exitoso' => 'success',
                                                        'Pendiente' => 'warning',
                                                        'Fallido' => 'danger',
                                                        'Seguimiento' => 'info'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $outcomeBadges[$interaction->outcome] ?? 'light' }} text-{{ $outcomeBadges[$interaction->outcome] ?? 'secondary' }}-emphasis rounded-pill px-2 py-1">
                                                    {{ $interaction->outcome }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($interaction->has_attachments ?? false)
                                                        <span class="badge bg-info-subtle text-info-emphasis rounded-pill" data-bs-toggle="tooltip" title="Tiene archivos adjuntos">
                                                            <i class="bi bi-paperclip"></i>
                                                        </span>
                                                    @endif
                                                    @if($interaction->is_urgent ?? false)
                                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill" data-bs-toggle="tooltip" title="Urgente">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                        </span>
                                                    @endif
                                                    @if($interaction->follow_up_date && $interaction->follow_up_date->isFuture())
                                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill" data-bs-toggle="tooltip" title="Seguimiento: {{ $interaction->follow_up_date->format('d/m/Y') }}">
                                                            <i class="bi bi-calendar-event"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li><a class="dropdown-item" href="{{ route('interactions.show', $interaction) }}"><i class="bi bi-eye me-2 text-primary"></i> Ver detalles</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('interactions.edit', $interaction) }}"><i class="bi bi-pencil me-2 text-info"></i> Editar</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="duplicateInteraction({{ $interaction->id }})"><i class="bi bi-files me-2 text-warning"></i> Duplicar</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="scheduleFollowUp({{ $interaction->id }})"><i class="bi bi-calendar-plus me-2 text-success"></i> Programar seguimiento</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger delete-interaction" href="#" data-id="{{ $interaction->id }}"><i class="bi bi-trash me-2"></i> Eliminar</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{-- 🔄 Paginación mejorada --}}
        @if ($interactions->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Mostrando {{ $interactions->firstItem() }}-{{ $interactions->lastItem() }} de {{ $interactions->total() }} resultados
                </div>
                <div>
                    {{ $interactions->appends(request()->except('page'))->links('components.maestras.congregaciones.pagination') }}
                </div>
            </div>
        @endif
    </div>
    {{-- Modal para acciones masivas --}}
    <div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionModalLabel">Acción masiva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bulkActionModalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmBulkAction">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal para guardar filtros --}}
    <div class="modal fade" id="saveFilterModal" tabindex="-1" aria-labelledby="saveFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveFilterModalLabel">Guardar filtros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="saveFilterForm">
                        <div class="mb-3">
                            <label for="filterName" class="form-label">Nombre del filtro</label>
                            <input type="text" class="form-control" id="filterName" required>
                        </div>
                        <div class="mb-3">
                            <label for="filterDescription" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="filterDescription" rows="2"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="makeDefault">
                            <label class="form-check-label" for="makeDefault">
                                Usar como filtro por defecto
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveFilter">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/interactions.css') }}">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/interactions.js') }}"></script>
    @endpush
</x-base-layout>