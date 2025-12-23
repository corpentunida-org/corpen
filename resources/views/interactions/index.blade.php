<x-base-layout>
    {{-- Alertas mejoradas con diseño --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center glassmorphism-alert" role="alert">
            <i class="feather-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center glassmorphism-alert" role="alert">
            <i class="feather-alert-circle me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros mejorados --}}
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
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Búsqueda</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-search"></i></span>
                        <input type="text" id="filterSearch" class="form-control pastel-input" placeholder="Cliente, cédula, notas..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Canal</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-message-circle"></i></span>
                        <select id="filterChannel" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($channels as $channel)
                                <option value="{{ $channel }}" {{ request('channel_filter') == $channel ? 'selected' : '' }}>{{ $channel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Tipo</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-tag"></i></span>
                        <select id="filterType" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type_filter') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Resultado</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-check-square"></i></span>
                        <select id="filterOutcome" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($outcomes as $outcome)
                                <option value="{{ $outcome }}" {{ request('outcome_filter') == $outcome ? 'selected' : '' }}>{{ $outcome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Área</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-layers"></i></span>
                        <select id="filterArea" class="form-select pastel-select">
                            <option value="">Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area_filter') == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Cargo</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-briefcase"></i></span>
                        <select id="filterCargo" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo }}" {{ request('cargo_filter') == $cargo ? 'selected' : '' }}>{{ $cargo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Línea de Obligación</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-credit-card"></i></span>
                        <select id="filterLinea" class="form-select pastel-select">
                            <option value="">Todas</option>
                            @foreach($lineas as $linea)
                                <option value="{{ $linea }}" {{ request('linea_filter') == $linea ? 'selected' : '' }}>{{ $linea }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" id="filterFecha" class="form-control pastel-input" value="{{ request('interaction_date_filter') }}" />
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Distrito</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-map-pin"></i></span>
                        <select id="filterDistrito" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($distrito as $distritoOption)
                                <option value="{{ $distritoOption }}" {{ request('distrito_filter') == $distritoOption ? 'selected' : '' }}>{{ $distritoOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Rango de Fechas</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" id="filterFechaInicio" class="form-control pastel-input" value="{{ request('start_date_filter') }}" />
                        <span class="input-group-text">a</span>
                        <input type="date" id="filterFechaFin" class="form-control pastel-input" value="{{ request('end_date_filter') }}" />
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Prioridad</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-flag"></i></span>
                        <select id="filterPriority" class="form-select pastel-select">
                            <option value="">Todas</option>
                            <option value="alta" {{ request('priority_filter') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="media" {{ request('priority_filter') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="baja" {{ request('priority_filter') == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="resultCount">Mostrando todos los resultados</span>
                        </small>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary me-2" id="applyFilters">
                                <i class="feather-filter me-1"></i>Aplicar Filtros
                            </button>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-view="table">
                                    <i class="feather-list"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-view="cards">
                                    <i class="feather-grid"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-view="kanban">
                                    <i class="feather-layout"></i>
                                </button>
                            </div>
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
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="feather-download me-1"></i>Exportar
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="exportExcel"><i class="feather-file-text me-1"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#" id="exportPDF"><i class="feather-file me-1"></i>PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportCSV"><i class="feather-file-text me-1"></i>CSV</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('interactions.create') }}" class="btn btn-success pastel-btn-gradient btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nueva Interacción</span>
                    </a>
                </div>
            </div>

            {{-- Tabs por estado mejorados --}}
            <ul class="nav nav-tabs mb-3 pastel-tabs" id="interactionTabs" role="tablist">
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab active"
                            id="tab-all-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-all"
                            type="button"
                            role="tab"
                            aria-controls="tab-all"
                            aria-selected="true">
                        <i class="feather-inbox me-2"></i>
                        TODOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #F0F8FF !important; color: #4682B4 !important;">                                
                            {{ $stats['total'] }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-success-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-success"
                            type="button"
                            role="tab"
                            aria-controls="tab-success"
                            aria-selected="false">
                        <i class="feather-check-circle me-2"></i>
                        EXITOSOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #E8F5E8 !important; color: #2E7D32 !important;">                                
                            {{ $stats['successful'] }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-pending-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-pending"
                            type="button"
                            role="tab"
                            aria-controls="tab-pending"
                            aria-selected="false">
                        <i class="feather-clock me-2"></i>
                        PENDIENTES
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #FFF8E1 !important; color: #F57C00 !important;">                                
                            {{ $stats['pending'] }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-today-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-today"
                            type="button"
                            role="tab"
                            aria-controls="tab-today"
                            aria-selected="false">
                        <i class="feather-calendar me-2"></i>
                        HOY
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #E6F3FF !important; color: #5C7CFA !important;">                                
                            {{ $stats['today'] }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-overdue-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-overdue"
                            type="button"
                            role="tab"
                            aria-controls="tab-overdue"
                            aria-selected="false">
                        <i class="feather-alert-triangle me-2"></i>
                        VENCIDOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #FFEBEE !important; color: #D32F2F !important;">                                
                            {{ $stats['overdue'] ?? 0 }}   
                        </span>                        
                    </button>
                </li>
            </ul>

            {{-- Contenido --}}
            <div class="tab-content" id="interactionTabsContent">
                {{-- TAB todos --}}
                <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable pastel-table excel-table" id="tablaPrincipal">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Prioridad</th>
                                    <th class="py-2">Distrito</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Área</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Línea</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($interactions as $interaction)
                                    @php
                                        $outcome = $interaction->outcomeRelation?->name ?? 'Sin definir';
                                        $badgeClass = [
                                            'Exitoso' => 'success',
                                            'Pendiente' => 'warning',
                                            'Fallido' => 'danger',
                                            'Seguimiento' => 'info'
                                        ][$outcome] ?? 'secondary';

                                        $nextActionDate = optional($interaction->next_action_date);
                                        $isPast = $nextActionDate->isPast() && !$nextActionDate->isToday();
                                        $isToday = $nextActionDate->isToday();
                                        $isFuture = $nextActionDate->isFuture();

                                        $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                        $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A';
                                        
                                        // Nuevos datos
                                        $areaName = $interaction->area?->nombre ?? '—';
                                        $cargoName = $interaction->cargo?->nombre_cargo ?? '—';
                                        $lineaName = $interaction->lineaDeObligacion?->nombre ?? '—';
                                        $areaAsigName = $interaction->areaDeAsignacion?->nombre ?? '—';
                                        
                                        // Prioridad
                                        $priority = $interaction->priority ?? 'media';
                                        $priorityBadge = [
                                            'alta' => 'danger',
                                            'media' => 'warning',
                                            'baja' => 'info'
                                        ][$priority] ?? 'secondary';
                                    @endphp

                                    <tr class="table-row-hover pastel-row excel-row" data-outcome="{{ $outcome }}" data-priority="{{ $priority }}">
                                        {{-- ID --}}
                                        <td class="py-2">
                                            <a href="javascript:void(0)"
                                            class="interaction-id fw-bold text-decoration-none pastel-link"
                                            data-id="{{ $interaction->id }}"
                                            data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                            data-cliente="{{ $clientName }}"
                                            data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                            data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                            data-area="{{ $areaName }}"
                                            data-cargo="{{ $cargoName }}"
                                            data-linea="{{ $lineaName }}"
                                            data-area-asig="{{ $areaAsigName }}"
                                            data-outcome="{{ $outcome }}"
                                            data-duracion="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
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
                                                <a href="{{ $interaction->getFile($interaction->attachment_urls) }}"
                                                target="_blank"
                                                class="ms-2 text-info"
                                                title="Ver archivo adjunto">
                                                        <i class="fas fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                        
                                        {{-- Prioridad --}}
                                        <td class="py-2">
                                            <span class="badge bg-{{ $priorityBadge }} badge-priority">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        
                                        {{-- Distrito --}}
                                        <td class="py-2">
                                            {{ $interaction->DistritoDeObligacion->NOM_DIST ?? ' ' }}
                                        </td>

                                        {{-- Fecha --}}
                                        <td class="py-2">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ optional($interaction->interaction_date)->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ optional($interaction->interaction_date)->format('H:i') }}</small>
                                            </div>
                                        </td>

                                        {{-- Cliente --}}
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $clientId }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Canal --}}
                                        <td class="py-2">
                                            <span class="small">{{ $interaction->channel?->name ?? '—' }}</span>
                                        </td>

                                        {{-- Área --}}
                                        <td class="py-2">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                    {{ $areaName }}
                                            </span>
                                        </td>

                                        {{-- Cargo --}}
                                        <td class="py-2">
                                            <span class="small">{{ $cargoName }}</span>
                                        </td>

                                        {{-- Línea --}}
                                        <td class="py-2">
                                            <span class="small">{{ $lineaName }}</span>
                                        </td>

                                        {{-- Resultado --}}
                                        <td class="py-2">
                                            <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                    @if($outcome === 'Exitoso')
                                                        background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                    @elseif($outcome === 'Pendiente')
                                                        background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;
                                                    @elseif($outcome === 'Fallido')
                                                        background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                    @else
                                                        background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;
                                                    @endif
                                                    padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                    <span>{{ $outcome }}</span>
                                            </div>
                                        </td>

                                        {{-- Próxima Acción --}}
                                        <td class="py-2">
                                            @if ($interaction->next_action_date)
                                                <div class="d-flex flex-column">
                                                    <span class="small {{ $isPast ? 'text-danger' : ($isToday ? 'text-warning' : 'text-success') }}">
                                                        {{ optional($interaction->next_action_date)->format('d/m/Y') }}
                                                    </span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">
                                                        {{ optional($interaction->next_action_date)->format('H:i') }}
                                                        @if($interaction->nextAction) ({{ $interaction->nextAction->name }}) @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Ver detalles"
                                                        onclick="window.location.href='{{ route('interactions.show', $interaction->id) }}'">
                                                    <i class="feather-eye"></i>
                                                </button>
                                                <!-- <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Editar"
                                                        onclick="window.location.href='{{ route('interactions.edit', $interaction->id) }}'">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $interaction->id }})">
                                                    <i class="feather-trash-2"></i>
                                                </button> -->
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- SOLUCIÓN: Mantener las 12 columnas para DataTables y centrar el mensaje en la última -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-inbox empty-icon"></i>
                                                <h6 class="mt-2">No hay interacciones registradas</h6>
                                                <p class="text-muted">Comienza creando una nueva interacción</p>
                                                <a href="{{ route('interactions.create') }}" class="btn btn-sm pastel-btn-gradient">
                                                    <i class="feather-plus me-1"></i> Crear Interacción
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- TAB Exitosos --}}
                <div class="tab-pane fade" id="tab-success" role="tabpanel" aria-labelledby="tab-success-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaExitosos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Prioridad</th>
                                    <th class="py-2">Distrito</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Área</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Línea</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['successful'] as $interaction)
                                    @php
                                        $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                        $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A';
                                        $areaName = $interaction->area?->nombre ?? '—';
                                        $cargoName = $interaction->cargo?->nombre_cargo ?? '—';
                                        $lineaName = $interaction->lineaDeObligacion?->nombre ?? '—';
                                        $areaAsigName = $interaction->areaDeAsignacion?->nombre ?? '—';
                                        
                                        // Prioridad
                                        $priority = $interaction->priority ?? 'media';
                                        $priorityBadge = [
                                            'alta' => 'danger',
                                            'media' => 'warning',
                                            'baja' => 'info'
                                        ][$priority] ?? 'secondary';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-priority="{{ $priority }}">
                                        <td class="py-2">
                                            <a href="{{ route('interactions.show', $interaction->id) }}" class="fw-bold text-decoration-none pastel-link">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $priorityBadge }} badge-priority">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            {{ $interaction->DistritoDeObligacion->NOM_DIST ?? ' ' }}
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ optional($interaction->interaction_date)->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ optional($interaction->interaction_date)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $clientId }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $interaction->channel?->name ?? '—' }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $areaName }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $cargoName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $lineaName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <div style="background-color: #E8F5E8 !important; color: #2E7D32 !important; border-radius: 8px; font-weight: 500; font-size: 0.6rem; border: 1px solid #C8E6C9 !important; padding: 2px 6px; text-align: center;">
                                                Exitoso
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            @if ($interaction->next_action_date)
                                                <div class="d-flex flex-column">
                                                    <span class="small">{{ optional($interaction->next_action_date)->format('d/m/Y') }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">
                                                        {{ optional($interaction->next_action_date)->format('H:i') }}
                                                        @if($interaction->nextAction) ({{ $interaction->nextAction->name }}) @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Ver detalles"
                                                        onclick="window.location.href='{{ route('interactions.show', $interaction->id) }}'">
                                                    <i class="feather-eye"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Editar"
                                                        onclick="window.location.href='{{ route('interactions.edit', $interaction->id) }}'">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $interaction->id }})">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- SOLUCIÓN: Mantener las 12 columnas para DataTables y centrar el mensaje en la última -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-check-circle empty-icon"></i>
                                                <h6 class="mt-2">No hay interacciones exitosas</h6>
                                                <p class="text-muted">No se encontraron interacciones con resultado exitoso</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- TAB Pendientes --}}
                <div class="tab-pane fade" id="tab-pending" role="tabpanel" aria-labelledby="tab-pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaPendientes">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Prioridad</th>
                                    <th class="py-2">Distrito</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Área</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Línea</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['pending'] as $interaction)
                                    @php
                                        $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                        $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A';
                                        $areaName = $interaction->area?->nombre ?? '—';
                                        $cargoName = $interaction->cargo?->nombre_cargo ?? '—';
                                        $lineaName = $interaction->lineaDeObligacion?->nombre ?? '—';
                                        $areaAsigName = $interaction->areaDeAsignacion?->nombre ?? '—';
                                        
                                        // Prioridad
                                        $priority = $interaction->priority ?? 'media';
                                        $priorityBadge = [
                                            'alta' => 'danger',
                                            'media' => 'warning',
                                            'baja' => 'info'
                                        ][$priority] ?? 'secondary';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-priority="{{ $priority }}">
                                        <td class="py-2">
                                            <a href="{{ route('interactions.show', $interaction->id) }}" class="fw-bold text-decoration-none pastel-link">
                                                #{{ $interaction->id }}
                                            </a>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $priorityBadge }} badge-priority">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            {{ $interaction->DistritoDeObligacion->NOM_DIST ?? ' ' }}
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ optional($interaction->interaction_date)->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ optional($interaction->interaction_date)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $clientId }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $interaction->channel?->name ?? '—' }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $areaName }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $cargoName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $lineaName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <div style="background-color: #FFF8E1 !important; color: #F57C00 !important; border-radius: 8px; font-weight: 500; font-size: 0.6rem; border: 1px solid #FFECB3 !important; padding: 2px 6px; text-align: center;">
                                                Pendiente
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            @if ($interaction->next_action_date)
                                                <div class="d-flex flex-column">
                                                    <span class="small">{{ optional($interaction->next_action_date)->format('d/m/Y') }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">
                                                        {{ optional($interaction->next_action_date)->format('H:i') }}
                                                        @if($interaction->nextAction) ({{ $interaction->nextAction->name }}) @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Ver detalles"
                                                        onclick="window.location.href='{{ route('interactions.show', $interaction->id) }}'">
                                                    <i class="feather-eye"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Editar"
                                                        onclick="window.location.href='{{ route('interactions.edit', $interaction->id) }}'">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $interaction->id }})">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- SOLUCIÓN: Mantener las 12 columnas para DataTables y centrar el mensaje en la última -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-clock empty-icon"></i>
                                                <h6 class="mt-2">No hay interacciones pendientes</h6>
                                                <p class="text-muted">No se encontraron interacciones con resultado pendiente</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- TAB Hoy --}}
                <div class="tab-pane fade" id="tab-today" role="tabpanel" aria-labelledby="tab-today-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaHoy">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Prioridad</th>
                                    <th class="py-2">Distrito</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Área</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Línea</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['today'] as $interaction)
                                    @php
                                        $outcome = $interaction->outcomeRelation?->name ?? 'Sin definir';
                                        $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                        $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A';
                                        $areaName = $interaction->area?->nombre ?? '—';
                                        $cargoName = $interaction->cargo?->nombre_cargo ?? '—';
                                        $lineaName = $interaction->lineaDeObligacion?->nombre ?? '—';
                                        $areaAsigName = $interaction->areaDeAsignacion?->nombre ?? '—';
                                        
                                        // Prioridad
                                        $priority = $interaction->priority ?? 'media';
                                        $priorityBadge = [
                                            'alta' => 'danger',
                                            'media' => 'warning',
                                            'baja' => 'info'
                                        ][$priority] ?? 'secondary';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-outcome="{{ $outcome }}" data-priority="{{ $priority }}">
                                        <td class="py-2">
                                            <a href="javascript:void(0)"
                                               class="interaction-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $interaction->id }}"
                                               data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                               data-cliente="{{ $clientName }}"
                                               data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                               data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                               data-area="{{ $areaName }}"
                                               data-cargo="{{ $cargoName }}"
                                               data-linea="{{ $lineaName }}"
                                               data-area-asig="{{ $areaAsigName }}"
                                               data-outcome="{{ $outcome }}"
                                               data-duracion="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
                                               data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                               data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                               data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                               data-url="{{ $interaction->interaction_url }}"
                                               data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                               data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                            @if ($interaction->attachment_urls)
                                                <a href="{{ $interaction->getFile($interaction->attachment_urls) }}"
                                                target="_blank"
                                                class="ms-2 text-info"
                                                title="Ver archivo adjunto">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $priorityBadge }} badge-priority">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            {{ $interaction->DistritoDeObligacion->NOM_DIST ?? ' ' }}
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ optional($interaction->interaction_date)->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ optional($interaction->interaction_date)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $clientId }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $interaction->channel?->name ?? '—' }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $areaName }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $cargoName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $lineaName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                 @if($outcome === 'Exitoso')
                                                     background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                 @elseif($outcome === 'Pendiente')
                                                     background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;
                                                 @elseif($outcome === 'Fallido')
                                                     background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                 @else
                                                     background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;
                                                 @endif
                                                 padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                <span>{{ $outcome }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            @if ($interaction->next_action_date)
                                                <div class="d-flex flex-column">
                                                    <span class="small">{{ optional($interaction->next_action_date)->format('d/m/Y') }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">
                                                        {{ optional($interaction->next_action_date)->format('H:i') }}
                                                        @if($interaction->nextAction) ({{ $interaction->nextAction->name }}) @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Ver detalles"
                                                        onclick="window.location.href='{{ route('interactions.show', $interaction->id) }}'">
                                                    <i class="feather-eye"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Editar"
                                                        onclick="window.location.href='{{ route('interactions.edit', $interaction->id) }}'">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $interaction->id }})">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- SOLUCIÓN: Mantener las 12 columnas para DataTables y centrar el mensaje en la última -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-calendar empty-icon"></i>
                                                <h6 class="mt-2">No hay interacciones de hoy</h6>
                                                <p class="text-muted">No se encontraron interacciones registradas hoy</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- TAB Vencidos --}}
                <div class="tab-pane fade" id="tab-overdue" role="tabpanel" aria-labelledby="tab-overdue-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaVencidos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-2">ID</th>
                                    <th class="py-2">Prioridad</th>
                                    <th class="py-2">Distrito</th>
                                    <th class="py-2">Fecha</th>
                                    <th class="py-2">Cliente</th>
                                    <th class="py-2">Canal</th>
                                    <th class="py-2">Área</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Línea</th>
                                    <th class="py-2">Resultado</th>
                                    <th class="py-2">Próxima Acción</th>
                                    <th class="py-2 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collectionsForTabs['overdue'] ?? [] as $interaction)
                                    @php
                                        $outcome = $interaction->outcomeRelation?->name ?? 'Sin definir';
                                        $clientName = $interaction->client->nom_ter ?? $interaction->client->nombre ?? 'N/A';
                                        $clientId = $interaction->client->cod_ter ?? $interaction->client->identificacion ?? 'N/A';
                                        $areaName = $interaction->area?->nombre ?? '—';
                                        $cargoName = $interaction->cargo?->nombre_cargo ?? '—';
                                        $lineaName = $interaction->lineaDeObligacion?->nombre ?? '—';
                                        $areaAsigName = $interaction->areaDeAsignacion?->nombre ?? '—';
                                        
                                        // Prioridad
                                        $priority = $interaction->priority ?? 'media';
                                        $priorityBadge = [
                                            'alta' => 'danger',
                                            'media' => 'warning',
                                            'baja' => 'info'
                                        ][$priority] ?? 'secondary';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-outcome="{{ $outcome }}" data-priority="{{ $priority }}">
                                        <td class="py-2">
                                            <a href="javascript:void(0)"
                                               class="interaction-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $interaction->id }}"
                                               data-fecha="{{ optional($interaction->interaction_date)->format('d/m/Y H:i') }}"
                                               data-cliente="{{ $clientName }}"
                                               data-canal="{{ $interaction->channel?->name ?? '—' }}"
                                               data-tipo="{{ $interaction->type?->name ?? '—' }}"
                                               data-area="{{ $areaName }}"
                                               data-cargo="{{ $cargoName }}"
                                               data-linea="{{ $lineaName }}"
                                               data-area-asig="{{ $areaAsigName }}"
                                               data-outcome="{{ $outcome }}"
                                               data-duracion="{{ $interaction->duration ? $interaction->duration . (is_numeric($interaction->duration) ? ' min' : '') : '—' }}"
                                               data-notas="{{ $interaction->notes ?? 'Sin notas.' }}"
                                               data-proxima-fecha="{{ optional($interaction->next_action_date)->format('d/m/Y H:i') }}"
                                               data-proxima-tipo="{{ $interaction->nextAction?->name ?? '—' }}"
                                               data-url="{{ $interaction->interaction_url }}"
                                               data-agent="{{ $interaction->agent->name ?? 'Sin asignar' }}"
                                               data-show-url="{{ route('interactions.show', $interaction->id) }}">
                                                #{{ $interaction->id }}
                                            </a>
                                            @if ($interaction->attachment_urls)
                                                <a href="{{ $interaction->getFile($interaction->attachment_urls) }}"
                                                target="_blank"
                                                class="ms-2 text-info"
                                                title="Ver archivo adjunto">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $priorityBadge }} badge-priority">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            {{ $interaction->DistritoDeObligacion->NOM_DIST ?? ' ' }}
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ optional($interaction->interaction_date)->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ optional($interaction->interaction_date)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $clientId }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $interaction->channel?->name ?? '—' }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $areaName }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $cargoName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <span class="small">{{ $lineaName }}</span>
                                        </td>
                                        <td class="py-2">
                                            <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                 @if($outcome === 'Exitoso')
                                                     background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                 @elseif($outcome === 'Pendiente')
                                                     background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;
                                                 @elseif($outcome === 'Fallido')
                                                     background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                 @else
                                                     background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;
                                                 @endif
                                                 padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                <span>{{ $outcome }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            @if ($interaction->next_action_date)
                                                <div class="d-flex flex-column">
                                                    <span class="small text-danger fw-bold">{{ optional($interaction->next_action_date)->format('d/m/Y') }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">
                                                        {{ optional($interaction->next_action_date)->format('H:i') }}
                                                        @if($interaction->nextAction) ({{ $interaction->nextAction->name }}) @endif
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Ver detalles"
                                                        onclick="window.location.href='{{ route('interactions.show', $interaction->id) }}'">
                                                    <i class="feather-eye"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Editar"
                                                        onclick="window.location.href='{{ route('interactions.edit', $interaction->id) }}'">
                                                    <i class="feather-edit"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        title="Eliminar"
                                                        onclick="confirmDelete({{ $interaction->id }})">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- SOLUCIÓN: Mantener las 12 columnas para DataTables y centrar el mensaje en la última -->
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-alert-triangle empty-icon"></i>
                                                <h6 class="mt-2">No hay interacciones vencidas</h6>
                                                <p class="text-muted">No se encontraron interacciones con fechas vencidas</p>
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

    <!-- Leyenda de colores pasteles -->
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-3">
            <h6 class="fw-semibold mb-2">
                <i class="feather-palette me-2"></i>Leyenda de Resultados
            </h6>
            <div class="d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Exitoso</span>
                    </div>
                    <small class="text-muted">Completado</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Pendiente</span>
                    </div>
                    <small class="text-muted">En espera</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Fallido</span>
                    </div>
                    <small class="text-muted">No completado</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Seguimiento</span>
                    </div>
                    <small class="text-muted">Requiere acción</small>
                </div>
            </div>
            <hr class="my-3">
            <h6 class="fw-semibold mb-2">
                <i class="feather-flag me-2"></i>Leyenda de Prioridades
            </h6>
            <div class="d-flex flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger badge-priority me-2">Alta</span>
                    <small class="text-muted">Requiere atención inmediata</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning badge-priority me-2">Media</span>
                    <small class="text-muted">Atención estándar</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-info badge-priority me-2">Baja</span>
                    <small class="text-muted">Sin urgencia</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal mejorado --}}
    <div class="modal fade" id="detalleInteractionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glassmorphism-modal">
                <div class="modal-header pastel-modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="feather-message-circle me-2"></i>
                        Detalles de la Interacción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h6 class="fw-bold text-primary mb-1" id="modal-id"></h6>
                            <p class="text-muted mb-0" id="modal-fecha"></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div id="modal-resultado" class="d-inline-block"></div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información General</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Cliente</small>
                                    <span id="modal-cliente"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Agente</small>
                                    <span id="modal-agent"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Canal</small>
                                    <span id="modal-canal"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Tipo</small>
                                    <span id="modal-tipo"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Duración</small>
                                    <span id="modal-duracion"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">URL</small>
                                    <span id="modal-url"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información Organizacional</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Área</small>
                                    <span id="modal-area"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Cargo</small>
                                    <span id="modal-cargo"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Línea de Obligación</small>
                                    <span id="modal-linea"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Área de Asignación</small>
                                    <span id="modal-area-asig"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Próxima Acción</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Fecha</small>
                                    <span id="modal-proxima-fecha"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Tipo</small>
                                    <span id="modal-proxima-tipo"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Notas</h6>
                        </div>
                        <div class="card-body">
                            <p id="modal-notas"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pastel-modal-footer">
                    <button type="button" class="btn pastel-btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn pastel-btn-gradient" id="editFromModal">
                        <i class="feather-edit-3 me-1"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>
        <style>
            /* COLORES PASTELES SUAVES Y HERMOSOS */
            
            body {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                transition: all 0.3s ease;
                color: #2c3e50 !important;
            }
            
            /* Glassmorphism */
            .glassmorphism-card {
                background: rgba(255, 255, 255, 0.25);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                border: 1px solid rgba(255, 255, 255, 0.18);
                box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.1);
            }
            
            .glassmorphism-alert {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(5px);
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            
            .glassmorphism-modal {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(15px);
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 10px 25px rgba(50, 50, 93, 0.1), 0 3px 10px rgba(0, 0, 0, 0.05);
            }
            
            /* Estilos personalizados pasteles - EXCEL STYLE */
            .avatar-xs-excel {
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
                font-weight: 600;
            }
            
            .pastel-avatar-primary {
                background: linear-gradient(135deg, #E6F3FF, #F8E8FF);
            }
            
            .pastel-avatar-success {
                background: linear-gradient(135deg, #E8F5E8, #F0FFF4);
            }
            
            .excel-row {
                transition: all 0.15s ease;
                border-radius: 4px;
                margin: 0;
                color: #2c3e50 !important;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            
            .excel-row:hover {
                background-color: rgba(255, 255, 255, 0.6);
                transform: translateX(1px);
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            }
            
            .excel-row:last-child {
                border-bottom: none;
            }
            
            .description-cell-excel {
                max-width: 120px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                color: #2c3e50 !important;
            }
            
            .empty-state {
                padding: 1.5rem;
                text-align: center;
            }
            
            .empty-icon {
                font-size: 2.5rem;
                color: #85929e;
            }
            
            /* Badges pasteles SUAVES */
            .pastel-badge {
                background: #F8E8FF !important;
                color: #6B5B95 !important;
                border-radius: 6px;
                padding: 2px 6px;
                font-size: 0.65rem;
                font-weight: 600;
            }
            
            /* Nuevo estilo para badges de globito en pestañas */
            .pastel-badge-globito {
                border-radius: 12px;
                padding: 3px 8px;
                font-size: 0.7rem;
                font-weight: 700;
                background: linear-gradient(135deg, #F8E8FF, #E6F3FF) !important;
                color: #6B5B95 !important;
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 20px;
                height: 20px;
            }
            
            .pastel-badge-globito:hover {
                transform: scale(1.1);
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
            
            /* Efectos especiales para filas según resultado */
            .excel-row[data-outcome="Exitoso"] {
                border-left: 2px solid #C8E6C9;
            }
            
            .excel-row[data-outcome="Pendiente"] {
                border-left: 2px solid #FFECB3;
            }
            
            .excel-row[data-outcome="Fallido"] {
                border-left: 2px solid #FFB3C1;
            }
            
            /* Efectos especiales para filas según prioridad */
            .excel-row[data-priority="alta"] {
                background-color: rgba(255, 235, 238, 0.2);
            }
            
            .excel-row[data-priority="media"] {
                background-color: rgba(255, 248, 225, 0.2);
            }
            
            .excel-row[data-priority="baja"] {
                background-color: rgba(227, 242, 253, 0.2);
            }
            
            /* Botones pasteles SUAVES */
            .pastel-btn-gradient {
                background: linear-gradient(135deg, #E8F5E8, #E6F3FF) !important;
                color: #2c3e50 !important;
                border: none;
                border-radius: 20px;
                padding: 6px 16px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(152, 216, 200, 0.2);
            }
            
            .pastel-btn-gradient:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(152, 216, 200, 0.3);
            }
            
            .pastel-btn-light {
                background: rgba(255, 255, 255, 0.7) !important;
                color: #2c3e50 !important;
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 16px;
                padding: 4px 12px;
                transition: all 0.3s ease;
                font-weight: 500;
            }
            
            .pastel-btn-light:hover {
                background: rgba(255, 255, 255, 0.9);
                transform: translateY(-1px);
            }
            
            /* Selects e inputs pasteles SUAVES */
            .pastel-select, .pastel-input {
                background: rgba(255, 255, 255, 0.7) !important;
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 8px;
                transition: all 0.3s ease;
                font-weight: 500;
                color: #2c3e50 !important;
                font-size: 0.875rem;
            }
            
            .pastel-select:focus, .pastel-input:focus {
                background: rgba(255, 255, 255, 0.9);
                border-color: #E6F3FF;
                box-shadow: 0 0 0 0.15rem rgba(230, 243, 255, 0.25);
            }
            
            /* Tabs pasteles SUAVES */
            .pastel-tabs .nav-link {
                color: #2c3e50 !important;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 12px 12px 0 0;
                margin-right: 4px;
                transition: all 0.3s ease;
                font-weight: 500;
                padding: 8px 16px;
                font-size: 0.875rem;
            }
            
            .pastel-tabs .nav-link.active {
                background: rgba(255, 255, 255, 0.9);
                color: #2c3e50 !important;
                font-weight: 700;
            }
            
            .pastel-tabs .nav-link:hover {
                background: rgba(255, 255, 255, 0.8);
            }
            
            /* Tabla estilo EXCEL */
            .excel-table {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 8px;
                overflow: hidden;
                font-size: 0.8rem;
                border-collapse: separate;
                border-spacing: 0;
            }
            
            .excel-table th {
                color: #2c3e50 !important;
                font-weight: 700;
                border: none;
                font-size: 0.75rem;
                padding: 8px 12px !important;
                background: rgba(255, 255, 255, 0.5);
            }
            
            .excel-table td {
                vertical-align: middle;
                border: none;
                padding: 6px 12px !important;
            }
            
            .pastel-thead {
                background: rgba(255, 255, 255, 0.5);
            }
            
            /* Modal pasteles SUAVES */
            .pastel-modal-header {
                background: linear-gradient(135deg, #E6F3FF, #F8E8FF) !important;
                color: #2c3e50 !important;
                border-radius: 16px 16px 0 0;
                border: none;
            }
            
            .pastel-modal-footer {
                background: rgba(255, 255, 255, 0.5);
                border-radius: 0 0 16px 16px;
                border: none;
            }
            
            .pastel-card {
                background: rgba(255, 255, 255, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 12px;
            }
            
            .pastel-card-header {
                background: rgba(255, 255, 255, 0.5);
                border-radius: 12px 12px 0 0;
                border: none;
            }
            
            /* Links pasteles SUAVES */
            .pastel-link {
                color: #5C7CFA !important;
                transition: all 0.2s ease;
                font-weight: 600;
                font-size: 0.875rem;
            }
            
            .pastel-link:hover {
                color: #4C63D2;
                transform: scale(1.02);
            }
            
            /* Badges de prioridad */
            .badge-priority {
                font-size: 0.65rem;
                padding: 3px 6px;
                border-radius: 6px;
                font-weight: 600;
            }
            
            /* Efectos de entrada */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .fade-in-up {
                animation: fadeInUp 0.4s ease-out;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .excel-table {
                    font-size: 0.75rem;
                }
                
                .btn-group-sm .btn {
                    padding: 0.2rem 0.4rem;
                    font-size: 0.7rem;
                }
                
                .pastel-tabs .nav-link {
                    padding: 6px 10px;
                    font-size: 0.75rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Librerías necesarias -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let currentTable = null;
                let currentInteractionId = null;

                // =================================================================
                // == LÓGICA DE DATATABLES CORREGIDA ==
                // =================================================================

                /**
                 * Inicializa o reinicializa DataTables en una tabla específica.
                 * @param {string} tableId - El ID de la tabla a inicializar (ej. '#tablaPrincipal').
                 */
                function initializeDataTable(tableId) {
                    // Si ya existe una instancia de DataTables, la destruimos para liberar memoria y evitar conflictos.
                    if (currentTable) {
                        currentTable.destroy();
                        currentTable = null;
                    }

                    // Inicializamos la nueva instancia de DataTables en la tabla especificada.
                    currentTable = $(tableId).DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excelHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>Excel' },
                            { extend: 'pdfHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file me-1"></i>PDF' },
                            { extend: 'csvHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>CSV' }
                        ],
                        pageLength: 20,
                        order: [[0, 'desc']],
                        language: {
                            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                            emptyTable: "No hay datos disponibles en la tabla",
                            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Mostrando 0 a 0 de 0 registros",
                            lengthMenu: "Mostrar _MENU_ registros",
                            loadingRecords: "Cargando...",
                            processing: "Procesando...",
                            search: "Buscar:",
                            zeroRecords: "No se encontraron resultados coincidentes",
                            paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
                        },
                        initComplete: function () {
                            // Una vez que la tabla se ha inicializado, actualizamos el contador de resultados.
                            updateResultCount();
                        }
                    });
                }

                /**
                 * Actualiza el texto del contador de resultados debajo de los filtros.
                 */
                function updateResultCount() {
                    if (currentTable) {
                        const info = currentTable.page.info();
                        document.getElementById('resultCount').textContent = 
                            `Mostrando ${info.recordsDisplay} de ${info.recordsTotal} resultados`;
                    }
                }

                // Inicializamos la tabla de la primera pestaña ("Todos") cuando la página carga.
                // Usamos un pequeño retraso para asegurar que el DOM esté completamente renderizado.
                setTimeout(function() {
                    initializeDataTable('#tablaPrincipal');
                }, 300);

                // Añadimos un event listener para el evento 'shown.bs.tab', que se dispara cuando una pestaña se muestra.
                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function (tabElement) {
                    tabElement.addEventListener('shown.bs.tab', function (event) {
                        let targetTableId = null;
                        const targetPaneId = event.target.getAttribute('data-bs-target');

                        // Determinamos qué tabla inicializar basándonos en la pestaña activa.
                        if (targetPaneId === '#tab-all') {
                            targetTableId = '#tablaPrincipal';
                        } else if (targetPaneId === '#tab-success') {
                            targetTableId = '#tablaExitosos';
                        } else if (targetPaneId === '#tab-pending') {
                            targetTableId = '#tablaPendientes';
                        } else if (targetPaneId === '#tab-today') {
                            targetTableId = '#tablaHoy';
                        } else if (targetPaneId === '#tab-overdue') {
                            targetTableId = '#tablaVencidos';
                        }

                        // Inicializamos la tabla de la pestaña activa con un pequeño retraso.
                        // Esto es CRUCIAL para darle tiempo al navegador a hacer visible el contenido de la pestaña.
                        if (targetTableId) {
                            setTimeout(function() {
                                initializeDataTable(targetTableId);
                            }, 150); // 150ms es un tiempo suficiente para la mayoría de los navegadores.
                        }
                    });
                });

                // =================================================================
                // == RESTO DE LA LÓGICA DE LA VISTA (Sin cambios relevantes) ==
                // =================================================================

                // Manejo del Modal para ver detalles rápidos
                document.addEventListener('click', function (e) {
                    const interactionIdLink = e.target.closest('.interaction-id');
                    if (interactionIdLink) {
                        e.preventDefault();
                        const el = interactionIdLink;
                        currentInteractionId = el.dataset.id;
                        // Llenar el modal con los datos (lógica existente)
                        document.getElementById('modal-id').textContent = `RADICADO ${el.dataset.id}`;
                        document.getElementById('modal-fecha').textContent = el.dataset.fecha;
                        document.getElementById('modal-cliente').textContent = el.dataset.cliente;
                        document.getElementById('modal-agent').textContent = el.dataset.agent;
                        document.getElementById('modal-canal').textContent = el.dataset.canal;
                        document.getElementById('modal-tipo').textContent = el.dataset.tipo;
                        document.getElementById('modal-duracion').textContent = el.dataset.duracion;
                        document.getElementById('modal-area').textContent = el.dataset.area;
                        document.getElementById('modal-cargo').textContent = el.dataset.cargo;
                        document.getElementById('modal-linea').textContent = el.dataset.linea;
                        document.getElementById('modal-area-asig').textContent = el.dataset['area-asig'];
                        document.getElementById('modal-proxima-fecha').textContent = el.dataset['proxima-fecha'] || '—';
                        document.getElementById('modal-proxima-tipo').textContent = el.dataset['proxima-tipo'] || '—';
                        document.getElementById('modal-notas').textContent = el.dataset.notas;
                        
                        const url = el.dataset.url;
                        document.getElementById('modal-url').innerHTML = url && url !== 'null' ? 
                            `<a href="${url}" target="_blank">Abrir enlace <i class="feather-external-link small"></i></a>` : '— No hay URL';
                        
                        const outcome = el.dataset.outcome;
                        let resultadoStyle = '';
                        switch(outcome) {
                            case 'Exitoso': resultadoStyle = 'background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;'; break;
                            case 'Pendiente': resultadoStyle = 'background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;'; break;
                            case 'Fallido': resultadoStyle = 'background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;'; break;
                            default: resultadoStyle = 'background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;';
                        }
                        document.getElementById('modal-resultado').innerHTML = `<div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; ${resultadoStyle} padding: 2px 6px; display: flex; align-items: center; justify-content: center;"><span>${outcome}</span></div>`;
                        document.getElementById('editFromModal').onclick = function() { window.location.href = el.dataset['edit-url']; };
                        new bootstrap.Modal(document.getElementById('detalleInteractionModal')).show();
                    }
                });

                // Función para aplicar filtros
                function applyFilters() {
                    const searchParams = new URLSearchParams();
                    if ($('#filterSearch').val()) searchParams.set('q', $('#filterSearch').val());
                    if ($('#filterChannel').val()) searchParams.set('channel_filter', $('#filterChannel').val());
                    if ($('#filterType').val()) searchParams.set('type_filter', $('#filterType').val());
                    if ($('#filterOutcome').val()) searchParams.set('outcome_filter', $('#filterOutcome').val());
                    if ($('#filterArea').val()) searchParams.set('area_filter', $('#filterArea').val());
                    if ($('#filterCargo').val()) searchParams.set('cargo_filter', $('#filterCargo').val());
                    if ($('#filterLinea').val()) searchParams.set('linea_filter', $('#filterLinea').val());
                    if ($('#filterDistrito').val()) searchParams.set('distrito_filter', $('#filterDistrito').val());
                    if ($('#filterFecha').val()) searchParams.set('interaction_date_filter', $('#filterFecha').val());
                    if ($('#filterFechaInicio').val()) searchParams.set('start_date_filter', $('#filterFechaInicio').val());
                    if ($('#filterFechaFin').val()) searchParams.set('end_date_filter', $('#filterFechaFin').val());
                    if ($('#filterPriority').val()) searchParams.set('priority_filter', $('#filterPriority').val());
                    window.location.href = `${window.location.pathname}?${searchParams.toString()}`;
                }

                // Eventos para los filtros
                $('#applyFilters').on('click', applyFilters);
                $('#clearFilters').on('click', () => window.location.href = window.location.pathname);
                $('#exportExcel').on('click', () => { if(currentTable) currentTable.button(0).trigger(); });
                $('#exportPDF').on('click', () => { if(currentTable) currentTable.button(1).trigger(); });
                $('#exportCSV').on('click', () => { if(currentTable) currentTable.button(2).trigger(); });

                // Función para confirmar eliminación
                function confirmDelete(id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción eliminará permanentemente la interacción y no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Crear un formulario temporal para enviar la solicitud de eliminación
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/interactions/${id}`;
                            
                            // Añadir token CSRF
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                            
                            // Añadir método DELETE
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';
                            form.appendChild(methodInput);
                            
                            // Enviar el formulario
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }

                // SweetAlert — confirmaciones
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({ title: titulo, text: texto, icon: icono, showCancelButton: true, confirmButtonColor: '#E6F3FF', cancelButtonColor: '#F8E8FF', confirmButtonText: 'Sí, continuar', cancelButtonText: 'Cancelar', background: 'rgba(255, 255, 255, 0.9)', backdrop: 'rgba(0, 0, 0, 0.1)' }).then((result) => { if (result.isConfirmed) callback(); });
                }
                document.querySelectorAll('.btnCrear').forEach(btn => { btn.addEventListener('click', function (e) { e.preventDefault(); confirmarAccion('¿Crear una nueva Interacción?', 'Serás redirigido al formulario de creación.', 'info', () => { window.location.href = btn.getAttribute('href'); }); }); });

                // Atajos de teclado
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); $('#filterSearch').focus(); }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'n') { e.preventDefault(); window.location.href = "{{ route('interactions.create') }}"; }
                    if (e.key === 'Enter' && (e.target.id === 'filterSearch' || e.target.classList.contains('pastel-select'))) { e.preventDefault(); applyFilters(); }
                });

                // Efectos de entrada
                $('.excel-row').each(function(index) { $(this).css({ 'opacity': '0', 'transform': 'translateY(5px)' }); setTimeout(() => { $(this).animate({ 'opacity': '1', 'transform': 'translateY(0)' }, 200); }, index * 20); });
                $('.glassmorphism-card').each(function(index) { $(this).addClass('fade-in-up'); $(this).css('animation-delay', `${index * 0.05}s`); });
            });
        </script>
    @endpush
</x-base-layout>