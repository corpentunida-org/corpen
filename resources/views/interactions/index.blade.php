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
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="feather-download me-1"></i>Exportar
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="exportExcel"><i
                                        class="feather-file-text me-1"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#" id="exportPDF"><i
                                        class="feather-file me-1"></i>PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportCSV"><i
                                        class="feather-file-text me-1"></i>CSV</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('interactions.create') }}"
                        class="btn btn-success pastel-btn-gradient btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nueva Interacción</span>
                    </a>
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
                
                {{-- TAB TODOS --}}
                <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable excel-table" id="tablaPrincipal">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($interactions as $interaction)
                                    @php
                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                    @endphp

                                    <tr class="table table-hover">
                                        <td class="py-2">
                                            <a class="interaction-id fw-bold text-decoration-none pastel-link" href="#"
                                                data-id="{{ $interaction->id }}"
                                                data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                                data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                                data-area="{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '—' }}" 
                                                data-cargo="{{ $interaction->agent->cargoRelation->nombre_cargo ?? '—' }}"
                                                data-linea="{{ $interaction->lineaDeObligacion?->nombre ?? '—' }}"
                                                data-area-asig="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                data-duracion="{{ $interaction->duration ? $interaction->duration . ' min' : '—' }}"
                                                data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                                data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                            @if ($interaction->attachment_urls)
                                                <a href="{{ $interaction->getFile($interaction->attachment_urls) }}" target="_blank"
                                                    class="ms-2 text-info" title="Ver archivo adjunto">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->agent->name ?? '—' }}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}</span>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                {{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}
                                            </p>
                                        </td>

                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->client->nom_ter ?? '—' }}</span>
                                            <p class="d-flex gap-3 fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                CC: <span class="m-0 fw-semibold">{{ $interaction->client_id }}</span>
                                                <span>{{ $interaction->client->distrito->NOM_DIST ?? '' }}</span>
                                            </p>
                                        </td>

                                        <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}</td>

                                        <td><span class="small">{{ $interaction->channel?->name ?? '—' }}</span></td>

                                        <td>
                                            <span class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                {{ $interaction->outcomeRelation?->name ?? '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($interaction->next_action_date)
                                                <div class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }} text-truncate-1-line">
                                                    {{ $interaction->client->nom_ter ?? '' }}
                                                </div>
                                                <div class="fs-12 text-muted text-truncate-1-line">
                                                    {{ optional($interaction->next_action_date)->format('H:i') }}
                                                    @if ($interaction->nextAction)
                                                        ({{ $interaction->nextAction->name }})
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="{{ route('interactions.show', $interaction->id) }}">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="feather-inbox empty-icon"></i>
                                                    <h6 class="mt-2">No hay interacciones registradas</h6>
                                                    <p class="text-muted">Aún no se han creado interacciones o no coinciden con los filtros.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        {{-- Paginación de Laravel para TAB TODOS --}}
                        <div class="mt-3">
                            {{ $interactions->links() }}
                        </div>
                    </div>
                </div>

                {{-- TAB EXITOSOS --}}
                <div class="tab-pane fade" id="tab-success" role="tabpanel" aria-labelledby="tab-success-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable excel-table" id="tablaExitosos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['successful'] as $interaction)
                                    @php
                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                    @endphp

                                    <tr class="table table-hover">
                                        <td class="py-2">
                                            <a class="interaction-id fw-bold text-decoration-none pastel-link" href="#"
                                                data-id="{{ $interaction->id }}"
                                                data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                                data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                                data-area="{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '—' }}" 
                                                data-cargo="{{ $interaction->agent->cargoRelation->nombre_cargo ?? '—' }}"
                                                data-linea="{{ $interaction->lineaDeObligacion?->nombre ?? '—' }}"
                                                data-area-asig="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                data-duracion="{{ $interaction->duration ? $interaction->duration . ' min' : '—' }}"
                                                data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                                data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->agent->name ?? '—' }}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}</span>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">{{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}</p>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->client->nom_ter ?? '—' }}</span>
                                            <p class="d-flex gap-3 fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                CC: <span class="m-0 fw-semibold">{{ $interaction->client_id }}</span>
                                            </p>
                                        </td>
                                        <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}</td>
                                        <td><span class="small">{{ $interaction->channel?->name ?? '—' }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                {{ $interaction->outcomeRelation?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($interaction->next_action_date)
                                                <div class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }} text-truncate-1-line">{{ $interaction->client->nom_ter ?? '' }}</div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="{{ route('interactions.show', $interaction->id) }}"><i class="feather-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="feather-check-circle empty-icon text-success"></i>
                                                    <h6 class="mt-2">No hay interacciones exitosas</h6>
                                                    <p class="text-muted">No se encontraron interacciones con resultado exitoso.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB PENDIENTES --}}
                <div class="tab-pane fade" id="tab-pending" role="tabpanel" aria-labelledby="tab-pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable excel-table" id="tablaPendientes">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['pending'] as $interaction)
                                    @php
                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                    @endphp

                                    <tr class="table table-hover">
                                        <td class="py-2">
                                            <a class="interaction-id fw-bold text-decoration-none pastel-link" href="#"
                                                data-id="{{ $interaction->id }}"
                                                data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                                data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                                data-area="{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '—' }}" 
                                                data-cargo="{{ $interaction->agent->cargoRelation->nombre_cargo ?? '—' }}"
                                                data-linea="{{ $interaction->lineaDeObligacion?->nombre ?? '—' }}"
                                                data-area-asig="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                data-duracion="{{ $interaction->duration ? $interaction->duration . ' min' : '—' }}"
                                                data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                                data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->agent->name ?? '—' }}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}</span>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">{{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}</p>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->client->nom_ter ?? '—' }}</span>
                                            <p class="d-flex gap-3 fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                CC: <span class="m-0 fw-semibold">{{ $interaction->client_id }}</span>
                                            </p>
                                        </td>
                                        <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}</td>
                                        <td><span class="small">{{ $interaction->channel?->name ?? '—' }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                {{ $interaction->outcomeRelation?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($interaction->next_action_date)
                                                <div class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }} text-truncate-1-line">{{ $interaction->client->nom_ter ?? '' }}</div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="{{ route('interactions.show', $interaction->id) }}"><i class="feather-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="feather-clock empty-icon text-warning"></i>
                                                    <h6 class="mt-2">No hay interacciones pendientes</h6>
                                                    <p class="text-muted">¡Todo al día! No se encontraron interacciones en estado pendiente.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB HOY --}}
                <div class="tab-pane fade" id="tab-today" role="tabpanel" aria-labelledby="tab-today-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable excel-table" id="tablaHoy">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['today'] as $interaction)
                                    @php
                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                    @endphp

                                    <tr class="table table-hover">
                                        <td class="py-2">
                                            <a class="interaction-id fw-bold text-decoration-none pastel-link" href="#"
                                                data-id="{{ $interaction->id }}"
                                                data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                                data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                                data-area="{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '—' }}" 
                                                data-cargo="{{ $interaction->agent->cargoRelation->nombre_cargo ?? '—' }}"
                                                data-linea="{{ $interaction->lineaDeObligacion?->nombre ?? '—' }}"
                                                data-area-asig="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                data-duracion="{{ $interaction->duration ? $interaction->duration . ' min' : '—' }}"
                                                data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                                data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->agent->name ?? '—' }}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}</span>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">{{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}</p>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->client->nom_ter ?? '—' }}</span>
                                            <p class="d-flex gap-3 fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                CC: <span class="m-0 fw-semibold">{{ $interaction->client_id }}</span>
                                            </p>
                                        </td>
                                        <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}</td>
                                        <td><span class="small">{{ $interaction->channel?->name ?? '—' }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                {{ $interaction->outcomeRelation?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($interaction->next_action_date)
                                                <div class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }} text-truncate-1-line">{{ $interaction->client->nom_ter ?? '' }}</div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="{{ route('interactions.show', $interaction->id) }}"><i class="feather-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="feather-calendar empty-icon text-primary"></i>
                                                    <h6 class="mt-2">No hay interacciones hoy</h6>
                                                    <p class="text-muted">Aún no se han registrado interacciones en el día de hoy.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB VENCIDOS --}}
                <div class="tab-pane fade" id="tab-overdue" role="tabpanel" aria-labelledby="tab-overdue-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable excel-table" id="tablaVencidos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Usuario</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['overdue'] as $interaction)
                                    @php
                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                    @endphp

                                    <tr class="table table-hover">
                                        <td class="py-2">
                                            <a class="interaction-id fw-bold text-decoration-none pastel-link" href="#"
                                                data-id="{{ $interaction->id }}"
                                                data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                                data-cliente="{{ $interaction->client->nom_ter ?? '—' }}"
                                                data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                                data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                                data-area="{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '—' }}" 
                                                data-cargo="{{ $interaction->agent->cargoRelation->nombre_cargo ?? '—' }}"
                                                data-linea="{{ $interaction->lineaDeObligacion?->nombre ?? '—' }}"
                                                data-area-asig="{{ $interaction->usuarioAsignado?->name ?? '—' }}"
                                                data-outcome="{{ $interaction->outcomeRelation?->name ?? '—' }}"
                                                data-duracion="{{ $interaction->duration ? $interaction->duration . ' min' : '—' }}"
                                                data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                                data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                                data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                                data-url="{{ $interaction->interaction_url }}"
                                                data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                                data-edit-url="{{ route('interactions.edit', $interaction->id) }}"
                                                data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->agent->name ?? '—' }}</span>
                                            <span class="badge bg-soft-primary text-primary ms-2">{{ $interaction->agent->cargoRelation->gdoArea->nombre ?? '' }}</span>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">{{ $interaction->agent->cargoRelation->nombre_cargo ?? '' }}</p>
                                        </td>
                                        <td>
                                            <span class="fw-semibold mb-1">{{ $interaction->client->nom_ter ?? '—' }}</span>
                                            <p class="d-flex gap-3 fs-12 text-muted text-truncate-1-line tickets-sort-desc">
                                                CC: <span class="m-0 fw-semibold">{{ $interaction->client_id }}</span>
                                            </p>
                                        </td>
                                        <td>{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}</td>
                                        <td><span class="small">{{ $interaction->channel?->name ?? '—' }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }} text-{{ $priorityColors[$interaction->outcome] ?? 'secondary' }}">
                                                {{ $interaction->outcomeRelation?->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($interaction->next_action_date)
                                                <div class="fs-12 {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }} text-truncate-1-line">{{ $interaction->client->nom_ter ?? '' }}</div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-sm btn-light" title="Ver detalles" href="{{ route('interactions.show', $interaction->id) }}"><i class="feather-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="feather-smile empty-icon text-success"></i>
                                                    <h6 class="mt-2">¡Genial! No hay interacciones vencidas</h6>
                                                    <p class="text-muted">Estás al día con todos tus seguimientos pendientes.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal mejorado (Se mantiene igual pero compactado para brevedad) --}}
    <div class="modal fade" id="detalleInteractionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glassmorphism-modal">
                <div class="modal-header pastel-modal-header">
                    <h5 class="modal-title d-flex align-items-center"><i class="feather-message-circle me-2"></i> Detalles de la Interacción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h6 class="fw-bold text-primary mb-1" id="modal-id"></h6>
                            <p class="text-muted mb-0" id="modal-fecha"></p>
                        </div>
                        <div class="col-md-4 text-end"><div id="modal-resultado" class="d-inline-block"></div></div>
                    </div>
                    {{-- Tarjetas del Modal --}}
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2"><h6 class="mb-0 fw-semibold">Información General</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Cliente</small><span id="modal-cliente"></span></div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Agente</small><span id="modal-agent"></span></div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Canal</small><span id="modal-canal"></span></div>
                                <div class="col-md-6 mb-3"><small class="text-muted d-block">Tipo</small><span id="modal-tipo"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="card pastel-card">
                        <div class="card-header pastel-card-header py-2"><h6 class="mb-0 fw-semibold">Notas</h6></div>
                        <div class="card-body"><p id="modal-notas"></p></div>
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
            /* Tus estilos anteriores se mantienen intactos */
            .glassmorphism-card { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.18); box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.1); }
            .glassmorphism-alert { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(5px); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.3); }
            .glassmorphism-modal { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 10px 25px rgba(50, 50, 93, 0.1), 0 3px 10px rgba(0, 0, 0, 0.05); }
            .pastel-btn-gradient { background: linear-gradient(135deg, #E8F5E8, #E6F3FF) !important; color: #2c3e50 !important; border: none; border-radius: 20px; padding: 6px 16px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(152, 216, 200, 0.2); }
            .pastel-btn-gradient:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(152, 216, 200, 0.3); }
            .pastel-btn-light { background: rgba(255, 255, 255, 0.7) !important; color: #2c3e50 !important; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 16px; padding: 4px 12px; transition: all 0.3s ease; font-weight: 500; }
            .pastel-select, .pastel-input { background: rgba(255, 255, 255, 0.7) !important; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; transition: all 0.3s ease; font-weight: 500; color: #2c3e50 !important; font-size: 0.875rem; }
            .pastel-select:focus, .pastel-input:focus { background: rgba(255, 255, 255, 0.9); border-color: #E6F3FF; box-shadow: 0 0 0 0.15rem rgba(230, 243, 255, 0.25); }
            .pastel-tabs .nav-link { color: #2c3e50 !important; background: rgba(255, 255, 255, 0.5); border-radius: 12px 12px 0 0; margin-right: 4px; transition: all 0.3s ease; font-weight: 500; padding: 8px 16px; font-size: 0.875rem; }
            .pastel-tabs .nav-link.active { background: rgba(255, 255, 255, 0.9); font-weight: 700; }
            .pastel-thead { background: rgba(255, 255, 255, 0.5); }
            .pastel-modal-header { background: linear-gradient(135deg, #E6F3FF, #F8E8FF) !important; color: #2c3e50 !important; border-radius: 16px 16px 0 0; border: none; }
            .pastel-modal-footer { background: rgba(255, 255, 255, 0.5); border-radius: 0 0 16px 16px; border: none; }
            .pastel-card { background: rgba(255, 255, 255, 0.3); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; }
            .pastel-card-header { background: rgba(255, 255, 255, 0.5); border-radius: 12px 12px 0 0; border: none; }
            .pastel-link { color: #5C7CFA !important; transition: all 0.2s ease; font-weight: 600; font-size: 0.875rem; }
            .pastel-link:hover { color: #4C63D2; transform: scale(1.02); }
            .empty-state { padding: 1.5rem; text-align: center; }
            .empty-icon { font-size: 2.5rem; color: #85929e; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentTable = null;
                let currentInteractionId = null;

                function initializeDataTable(tableId) {
                    if (currentTable) {
                        currentTable.destroy();
                        currentTable = null;
                    }

                    let isServerPaginated = (tableId === '#tablaPrincipal');

                    currentTable = $(tableId).DataTable({
                        dom: 'Bfrtip',
                        paging: !isServerPaginated,
                        info: !isServerPaginated,
                        buttons: [
                            { extend: 'excelHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>Excel' },
                            { extend: 'pdfHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file me-1"></i>PDF' },
                            { extend: 'csvHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>CSV' }
                        ],
                        pageLength: 20,
                        order: [[0, 'desc']],
                        language: {
                            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                        }
                    });
                }

                setTimeout(function() { initializeDataTable('#tablaPrincipal'); }, 300);

                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabElement) {
                    tabElement.addEventListener('shown.bs.tab', function(event) {
                        let targetTableId = null;
                        const targetPaneId = event.target.getAttribute('data-bs-target');

                        if (targetPaneId === '#tab-all') targetTableId = '#tablaPrincipal';
                        else if (targetPaneId === '#tab-success') targetTableId = '#tablaExitosos';
                        else if (targetPaneId === '#tab-pending') targetTableId = '#tablaPendientes';
                        else if (targetPaneId === '#tab-today') targetTableId = '#tablaHoy';
                        else if (targetPaneId === '#tab-overdue') targetTableId = '#tablaVencidos';

                        if (targetTableId) { setTimeout(function() { initializeDataTable(targetTableId); }, 150); }
                    });
                });

                // ==========================================
                // LÓGICA DE FILTROS ACTUALIZADA (TIEMPO REAL)
                // ==========================================
                function applyFilters() {
                    const searchParams = new URLSearchParams();
                    
                    // Solo tomamos los 3 valores que nos importan
                    const searchVal = $('#filterSearch').val();
                    const startDateVal = $('#filterFechaInicio').val();
                    const endDateVal = $('#filterFechaFin').val();

                    // Mapeamos a los nombres EXACTOS del Controller
                    if (searchVal) searchParams.set('search', searchVal);
                    if (startDateVal) searchParams.set('start_date', startDateVal);
                    if (endDateVal) searchParams.set('end_date', endDateVal);
                    
                    // Disparamos la búsqueda. Esto recarga la página y el backend hace el resto
                    window.location.href = `${window.location.pathname}?${searchParams.toString()}`;
                }

                $('#applyFilters').on('click', applyFilters);
                $('#clearFilters').on('click', () => window.location.href = window.location.pathname);
                
                // Atajos de teclado para aplicar filtros presionando "Enter"
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && (e.target.id === 'filterSearch' || e.target.id === 'filterFechaInicio' || e.target.id === 'filterFechaFin')) {
                        e.preventDefault();
                        applyFilters();
                    }
                });

                // Lógica del modal...
                document.addEventListener('click', function(e) {
                    const interactionIdLink = e.target.closest('.interaction-id');
                    if (interactionIdLink) {
                        e.preventDefault();
                        const el = interactionIdLink;
                        document.getElementById('modal-id').textContent = `RADICADO ${el.dataset.id}`;
                        document.getElementById('modal-fecha').textContent = el.dataset.fecha;
                        document.getElementById('modal-cliente').textContent = el.dataset.cliente;
                        document.getElementById('modal-agent').textContent = el.dataset.agent;
                        document.getElementById('modal-notas').textContent = el.dataset.notas;
                        
                        new bootstrap.Modal(document.getElementById('detalleInteractionModal')).show();
                    }
                });

                // Exportaciones...
                $('#exportExcel').on('click', () => { if (currentTable) currentTable.button(0).trigger(); });
                $('#exportPDF').on('click', () => { if (currentTable) currentTable.button(1).trigger(); });
                $('#exportCSV').on('click', () => { if (currentTable) currentTable.button(2).trigger(); });
            });
        </script>
    @endpush
</x-base-layout>