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
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
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
        <div class="card-body p-2">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Búsqueda</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-search"></i></span>
                        <input type="text" id="filterSearch" class="form-control pastel-input" placeholder="Código, nombre, razón social..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Tipo</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-tag"></i></span>
                        <select id="filterType" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach(App\Models\Maestras\MaeTipo::all() as $tipo)
                                <option value="{{ $tipo->id }}" {{ request('type_filter') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Congregación</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-users"></i></span>
                        <select id="filterCongregation" class="form-select pastel-select">
                            <option value="">Todas</option>
                            @foreach(App\Models\Maestras\Congregacion::all() as $congregacion)
                                <option value="{{ $congregacion->id }}" {{ request('congregation_filter') == $congregacion->id ? 'selected' : '' }}>{{ $congregacion->nom_con }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Distrito</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-map-pin"></i></span>
                        <select id="filterDistrict" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach(App\Models\Maestras\maeDistritos::all() as $distrito)
                                <option value="{{ $distrito->id }}" {{ request('district_filter') == $distrito->id ? 'selected' : '' }}>{{ $distrito->nom_dis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Estado</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-check-square"></i></span>
                        <select id="filterStatus" class="form-select pastel-select">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('status_filter') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('status_filter') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Ciudad</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-map"></i></span>
                        <input type="text" id="filterCity" class="form-control pastel-input" placeholder="Ciudad..." value="{{ request('city_filter') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Tipo de Persona</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-user"></i></span>
                        <select id="filterPersonType" class="form-select pastel-select">
                            <option value="">Todos</option>
                            <option value="N" {{ request('person_type_filter') == 'N' ? 'selected' : '' }}>Natural</option>
                            <option value="J" {{ request('person_type_filter') == 'J' ? 'selected' : '' }}>Jurídica</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="resultCount">Mostrando todos los resultados</span>
                        </small>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="applyFilters">
                                <i class="feather-filter me-1"></i>Aplicar Filtros
                            </button>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-view="table">
                                    <i class="feather-list"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-view="cards">
                                    <i class="feather-grid"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="card glassmorphism-card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Terceros</h6>
                            <h3 class="mb-0">{{ $terceros->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="feather-users text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card glassmorphism-card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Activos</h6>
                            <h3 class="mb-0">{{ $terceros->where(function($q) { return $q->estado == 1 || $q->estado === 'A'; })->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="feather-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card glassmorphism-card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Inactivos</h6>
                            <h3 class="mb-0">{{ $terceros->where(function($q) { return $q->estado != 1 && $q->estado !== 'A'; })->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="feather-x-circle text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card glassmorphism-card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Nuevos este mes</h6>
                            <h3 class="mb-0">{{ $terceros->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="feather-trending-up text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla mejorada --}}
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Gestión de Terceros</h5>
                    <p class="text-muted mb-0 small">Administra y monitorea todos los terceros registrados</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                        <i class="feather-download me-1"></i>Exportar
                    </button>
                    <a href="{{ route('maestras.terceros.create') }}" class="btn btn-success pastel-btn-gradient btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nuevo Tercero</span>
                    </a>
                </div>
            </div>

            {{-- Tabs por estado mejorados --}}
            <ul class="nav nav-tabs mb-3 pastel-tabs" id="tercerosTabs" role="tablist">
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
                            {{ $terceros->count() }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-active-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-active"
                            type="button"
                            role="tab"
                            aria-controls="tab-active"
                            aria-selected="false">
                        <i class="feather-check-circle me-2"></i>
                        ACTIVOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #E8F5E8 !important; color: #2E7D32 !important;">                                
                            {{ $terceros->where(function($q) { return $q->estado == 1 || $q->estado === 'A'; })->count() }}   
                        </span>                        
                    </button>
                </li>
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-inactive-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-inactive"
                            type="button"
                            role="tab"
                            aria-controls="tab-inactive"
                            aria-selected="false">
                        <i class="feather-x-circle me-2"></i>
                        INACTIVOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #FFF8E1 !important; color: #F57C00 !important;">                                
                            {{ $terceros->where(function($q) { return $q->estado != 1 && $q->estado !== 'A'; })->count() }}   
                        </span>                        
                    </button>
                </li>
            </ul>

            {{-- Contenido --}}
            <div class="tab-content" id="tercerosTabsContent">
                {{-- TAB todos --}}
                <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle interactionTable pastel-table excel-table" id="tablaPrincipal">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-1">ID</th>
                                    <th class="py-1">Código</th>
                                    <th class="py-1">Nombre / Razón Social</th>
                                    <th class="py-1">Contacto</th>
                                    <th class="py-1">Tipo</th>
                                    <th class="py-1">Congregación</th>
                                    <th class="py-1">Distrito</th>
                                    <th class="py-1">Estado</th>
                                    <th class="py-1 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($terceros as $tercero)
                                    @php
                                        $isActive = $tercero->estado == 1 || $tercero->estado === 'A';
                                        $statusBadge = $isActive ? 
                                            ['Activo', 'success'] : 
                                            ['Inactivo', 'danger'];
                                        
                                        $clientName = $tercero->nom_ter ?? ($tercero->razon_soc ?? 'N/A');
                                        $clientId = $tercero->cod_ter ?? 'N/A';
                                        $clientPhone = $tercero->tel ?? ($tercero->cel ?? 'N/A');
                                        $clientEmail = $tercero->email ?? 'N/A';
                                        $type = $tercero->maeTipos->nom_ter ?? 'Sin definir';
                                        $congregacion = $tercero->congregaciones->nom_con ?? 'Sin definir';
                                        $distrito = $tercero->distrito->nom_dis ?? 'Sin definir';
                                    @endphp

                                    <tr class="table-row-hover pastel-row excel-row" data-status="{{ $isActive ? 'activo' : 'inactivo' }}">
                                        {{-- ID --}}
                                        <td class="py-1">
                                            <a href="javascript:void(0)"
                                               class="tercero-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $tercero->id }}"
                                               data-codigo="{{ $tercero->cod_ter }}"
                                               data-nombre="{{ $clientName }}"
                                               data-telefono="{{ $clientPhone }}"
                                               data-email="{{ $clientEmail }}"
                                               data-tipo="{{ $type }}"
                                               data-congregacion="{{ $congregacion }}"
                                               data-distrito="{{ $distrito }}"
                                               data-estado="{{ $statusBadge[0] }}"
                                               data-direccion="{{ $tercero->dir ?? 'Sin dirección' }}"
                                               data-ciudad="{{ $tercero->ciudad ?? 'Sin ciudad' }}"
                                               data-fecha-creacion="{{ optional($tercero->created_at)->format('d/m/Y H:i') }}"
                                               data-fecha-actualizacion="{{ optional($tercero->updated_at)->format('d/m/Y H:i') }}"
                                               data-show-url="{{ route('maestras.terceros.show', $tercero->cod_ter) }}"
                                               data-edit-url="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}"
                                               data-delete-url="{{ route('maestras.terceros.destroy', $tercero->cod_ter) }}">
                                                #{{ $tercero->id }}
                                            </a>
                                        </td>

                                        {{-- Código --}}
                                        <td class="py-1">
                                            <span class="small">{{ $tercero->cod_ter }}</span>
                                        </td>

                                        {{-- Nombre / Razón Social --}}
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small fw-medium">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $tercero->cod_ter }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Contacto --}}
                                        <td class="py-1">
                                            <div class="d-flex flex-column">
                                                @if ($clientPhone !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-phone text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small">{{ $clientPhone }}</span>
                                                    </div>
                                                @endif
                                                @if ($clientEmail !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-mail text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small text-truncate" style="max-width: 150px;">{{ $clientEmail }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Tipo --}}
                                        <td class="py-1">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $type }}
                                            </span>
                                        </td>

                                        {{-- Congregación --}}
                                        <td class="py-1">
                                            <span style="background-color: #E8F5E8 !important; color: #2E7D32 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #C8E6C9; display: inline-block;">
                                                {{ $congregacion }}
                                            </span>
                                        </td>

                                        {{-- Distrito --}}
                                        <td class="py-1">
                                            <span style="background-color: #FFF8E1 !important; color: #F57C00 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #FFE0B2; display: inline-block;">
                                                {{ $distrito }}
                                            </span>
                                        </td>

                                        {{-- Estado --}}
                                        <td class="py-1">
                                            <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                 @if($isActive)
                                                     background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                 @else
                                                     background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                 @endif
                                                 padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                <span>{{ $statusBadge[0] }}</span>
                                            </div>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnVer" href="{{ route('maestras.terceros.show', $tercero->cod_ter) }}">
                                                            <i class="feather-eye me-2 text-primary"></i>
                                                            Ver detalles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnEditar" href="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}">
                                                            <i class="feather-edit-3 me-2 text-warning"></i>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('maestras.terceros.generarPdf', $tercero->cod_ter) }}">
                                                            <i class="feather-file-text me-2 text-info"></i>
                                                            Generar PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center btnCambiarEstado" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-estado="{{ $isActive ? 'inactivo' : 'activo' }}">
                                                            <i class="feather-refresh-cw me-2 text-info"></i>
                                                            Cambiar estado
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center text-danger btnEliminar" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-nombre="{{ $clientName }}">
                                                            <i class="feather-trash-2 me-2"></i>
                                                            Eliminar
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-users empty-icon"></i>
                                                <h6 class="mt-2">No hay terceros registrados</h6>
                                                <p class="text-muted">Comienza creando un nuevo tercero</p>
                                                <a href="{{ route('maestras.terceros.create') }}" class="btn btn-sm pastel-btn-gradient">
                                                    <i class="feather-plus me-1"></i> Crear Tercero
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación --}}
                    @if($terceros->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Mostrando {{ $terceros->firstItem() }} a {{ $terceros->lastItem() }} de {{ $terceros->total() }} registros
                            </div>
                            {{ $terceros->links() }}
                        </div>
                    @endif
                </div>
                {{-- fin tab Todos --}}
                
                {{-- TAB Activos --}}
                <div class="tab-pane fade" id="tab-active" role="tabpanel" aria-labelledby="tab-active-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaActivos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-1">ID</th>
                                    <th class="py-1">Código</th>
                                    <th class="py-1">Nombre / Razón Social</th>
                                    <th class="py-1">Contacto</th>
                                    <th class="py-1">Tipo</th>
                                    <th class="py-1">Congregación</th>
                                    <th class="py-1">Distrito</th>
                                    <th class="py-1">Estado</th>
                                    <th class="py-1 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($terceros->where(function($q) { return $q->estado == 1 || $q->estado === 'A'; }) as $tercero)
                                    @php
                                        $isActive = true;
                                        $statusBadge = ['Activo', 'success'];
                                        
                                        $clientName = $tercero->nom_ter ?? ($tercero->razon_soc ?? 'N/A');
                                        $clientId = $tercero->cod_ter ?? 'N/A';
                                        $clientPhone = $tercero->tel ?? ($tercero->cel ?? 'N/A');
                                        $clientEmail = $tercero->email ?? 'N/A';
                                        $type = $tercero->maeTipos->nom_ter ?? 'Sin definir';
                                        $congregacion = $tercero->congregaciones->nom_con ?? 'Sin definir';
                                        $distrito = $tercero->distrito->nom_dis ?? 'Sin definir';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-status="activo">
                                        <td class="py-1">
                                            <a href="javascript:void(0)"
                                               class="tercero-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $tercero->id }}"
                                               data-codigo="{{ $tercero->cod_ter }}"
                                               data-nombre="{{ $clientName }}"
                                               data-telefono="{{ $clientPhone }}"
                                               data-email="{{ $clientEmail }}"
                                               data-tipo="{{ $type }}"
                                               data-congregacion="{{ $congregacion }}"
                                               data-distrito="{{ $distrito }}"
                                               data-estado="{{ $statusBadge[0] }}"
                                               data-direccion="{{ $tercero->dir ?? 'Sin dirección' }}"
                                               data-ciudad="{{ $tercero->ciudad ?? 'Sin ciudad' }}"
                                               data-fecha-creacion="{{ optional($tercero->created_at)->format('d/m/Y H:i') }}"
                                               data-fecha-actualizacion="{{ optional($tercero->updated_at)->format('d/m/Y H:i') }}"
                                               data-show-url="{{ route('maestras.terceros.show', $tercero->cod_ter) }}"
                                               data-edit-url="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}"
                                               data-delete-url="{{ route('maestras.terceros.destroy', $tercero->cod_ter) }}">
                                                #{{ $tercero->id }}
                                            </a>
                                        </td>
                                        <td class="py-1">
                                            <span class="small">{{ $tercero->cod_ter }}</span>
                                        </td>
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small fw-medium">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $tercero->cod_ter }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <div class="d-flex flex-column">
                                                @if ($clientPhone !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-phone text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small">{{ $clientPhone }}</span>
                                                    </div>
                                                @endif
                                                @if ($clientEmail !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-mail text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small text-truncate" style="max-width: 150px;">{{ $clientEmail }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $type }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #E8F5E8 !important; color: #2E7D32 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #C8E6C9; display: inline-block;">
                                                {{ $congregacion }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #FFF8E1 !important; color: #F57C00 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #FFE0B2; display: inline-block;">
                                                {{ $distrito }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <div style="background-color: #E8F5E8 !important; color: #2E7D32 !important; border-radius: 8px; font-weight: 500; font-size: 0.6rem; border: 1px solid #C8E6C9 !important; padding: 2px 6px; text-align: center;">
                                                Activo
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnVer" href="{{ route('maestras.terceros.show', $tercero->cod_ter) }}">
                                                            <i class="feather-eye me-2 text-primary"></i>
                                                            Ver detalles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnEditar" href="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}">
                                                            <i class="feather-edit-3 me-2 text-warning"></i>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('maestras.terceros.generarPdf', $tercero->cod_ter) }}">
                                                            <i class="feather-file-text me-2 text-info"></i>
                                                            Generar PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center btnCambiarEstado" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-estado="inactivo">
                                                            <i class="feather-refresh-cw me-2 text-info"></i>
                                                            Cambiar estado
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center text-danger btnEliminar" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-nombre="{{ $clientName }}">
                                                            <i class="feather-trash-2 me-2"></i>
                                                            Eliminar
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-check-circle empty-icon"></i>
                                                <h6 class="mt-2">No hay terceros activos</h6>
                                                <p class="text-muted">No se encontraron terceros con estado activo</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- TAB Inactivos --}}
                <div class="tab-pane fade" id="tab-inactive" role="tabpanel" aria-labelledby="tab-inactive-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center interactionTable pastel-table excel-table" id="tablaInactivos">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-1">ID</th>
                                    <th class="py-1">Código</th>
                                    <th class="py-1">Nombre / Razón Social</th>
                                    <th class="py-1">Contacto</th>
                                    <th class="py-1">Tipo</th>
                                    <th class="py-1">Congregación</th>
                                    <th class="py-1">Distrito</th>
                                    <th class="py-1">Estado</th>
                                    <th class="py-1 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($terceros->where(function($q) { return $q->estado != 1 && $q->estado !== 'A'; }) as $tercero)
                                    @php
                                        $isActive = false;
                                        $statusBadge = ['Inactivo', 'danger'];
                                        
                                        $clientName = $tercero->nom_ter ?? ($tercero->razon_soc ?? 'N/A');
                                        $clientId = $tercero->cod_ter ?? 'N/A';
                                        $clientPhone = $tercero->tel ?? ($tercero->cel ?? 'N/A');
                                        $clientEmail = $tercero->email ?? 'N/A';
                                        $type = $tercero->maeTipos->nom_ter ?? 'Sin definir';
                                        $congregacion = $tercero->congregaciones->nom_con ?? 'Sin definir';
                                        $distrito = $tercero->distrito->nom_dis ?? 'Sin definir';
                                    @endphp
                                    <tr class="table-row-hover pastel-row excel-row" data-status="inactivo">
                                        <td class="py-1">
                                            <a href="javascript:void(0)"
                                               class="tercero-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $tercero->id }}"
                                               data-codigo="{{ $tercero->cod_ter }}"
                                               data-nombre="{{ $clientName }}"
                                               data-telefono="{{ $clientPhone }}"
                                               data-email="{{ $clientEmail }}"
                                               data-tipo="{{ $type }}"
                                               data-congregacion="{{ $congregacion }}"
                                               data-distrito="{{ $distrito }}"
                                               data-estado="{{ $statusBadge[0] }}"
                                               data-direccion="{{ $tercero->dir ?? 'Sin dirección' }}"
                                               data-ciudad="{{ $tercero->ciudad ?? 'Sin ciudad' }}"
                                               data-fecha-creacion="{{ optional($tercero->created_at)->format('d/m/Y H:i') }}"
                                               data-fecha-actualizacion="{{ optional($tercero->updated_at)->format('d/m/Y H:i') }}"
                                               data-show-url="{{ route('maestras.terceros.show', $tercero->cod_ter) }}"
                                               data-edit-url="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}"
                                               data-delete-url="{{ route('maestras.terceros.destroy', $tercero->cod_ter) }}">
                                                #{{ $tercero->id }}
                                            </a>
                                        </td>
                                        <td class="py-1">
                                            <span class="small">{{ $tercero->cod_ter }}</span>
                                        </td>
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="small fw-medium">{{ $clientName }}</div>
                                                    <small class="text-muted">{{ $tercero->cod_ter }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <div class="d-flex flex-column">
                                                @if ($clientPhone !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-phone text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small">{{ $clientPhone }}</span>
                                                    </div>
                                                @endif
                                                @if ($clientEmail !== 'N/A')
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-mail text-muted me-1" style="font-size: 0.7rem;"></i>
                                                        <span class="small text-truncate" style="max-width: 150px;">{{ $clientEmail }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $type }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #E8F5E8 !important; color: #2E7D32 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #C8E6C9; display: inline-block;">
                                                {{ $congregacion }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <span style="background-color: #FFF8E1 !important; color: #F57C00 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #FFE0B2; display: inline-block;">
                                                {{ $distrito }}
                                            </span>
                                        </td>
                                        <td class="py-1">
                                            <div style="background-color: #FFD6E0 !important; color: #D63384 !important; border-radius: 8px; font-weight: 500; font-size: 0.6rem; border: 1px solid #FFB3C1 !important; padding: 2px 6px; text-align: center;">
                                                Inactivo
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnVer" href="{{ route('maestras.terceros.show', $tercero->cod_ter) }}">
                                                            <i class="feather-eye me-2 text-primary"></i>
                                                            Ver detalles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btnEditar" href="{{ route('maestras.terceros.edit', $tercero->cod_ter) }}">
                                                            <i class="feather-edit-3 me-2 text-warning"></i>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('maestras.terceros.generarPdf', $tercero->cod_ter) }}">
                                                            <i class="feather-file-text me-2 text-info"></i>
                                                            Generar PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center btnCambiarEstado" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-estado="activo">
                                                            <i class="feather-refresh-cw me-2 text-info"></i>
                                                            Cambiar estado
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center text-danger btnEliminar" 
                                                                data-id="{{ $tercero->cod_ter }}" 
                                                                data-nombre="{{ $clientName }}">
                                                            <i class="feather-trash-2 me-2"></i>
                                                            Eliminar
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-x-circle empty-icon"></i>
                                                <h6 class="mt-2">No hay terceros inactivos</h6>
                                                <p class="text-muted">No se encontraron terceros con estado inactivo</p>
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
        <div class="card-body p-2">
            <h6 class="fw-semibold mb-2">
                <i class="feather-palette me-2"></i>Leyenda de Estados
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Activo</span>
                    </div>
                    <small class="text-muted">Tercero disponible</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <span>Inactivo</span>
                    </div>
                    <small class="text-muted">Tercero no disponible</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal mejorado --}}
    <div class="modal fade" id="detalleTerceroModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glassmorphism-modal">
                <div class="modal-header pastel-modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="feather-users me-2"></i>
                        Detalles del Tercero
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <h6 class="fw-bold text-primary mb-1" id="modal-id"></h6>
                            <p class="text-muted mb-0" id="modal-codigo"></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div id="modal-estado" class="d-inline-block"></div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información General</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Nombre / Razón Social</small>
                                    <span id="modal-nombre"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Tipo</small>
                                    <span id="modal-tipo"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Congregación</small>
                                    <span id="modal-congregacion"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Distrito</small>
                                    <span id="modal-distrito"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Teléfono</small>
                                    <span id="modal-telefono"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Correo Electrónico</small>
                                    <span id="modal-email"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información de Ubicación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Dirección</small>
                                    <span id="modal-direccion"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Ciudad</small>
                                    <span id="modal-ciudad"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Información del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Fecha de Creación</small>
                                    <span id="modal-fecha-creacion"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Última Actualización</small>
                                    <span id="modal-fecha-actualizacion"></span>
                                </div>
                            </div>
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
                width: 16px;
                height: 16px;
                font-size: 0.6rem;
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
            .excel-row[data-status="activo"] {
                border-left: 2px solid #C8E6C9;
            }
            
            .excel-row[data-status="inactivo"] {
                border-left: 2px solid #FFB3C1;
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
                padding: 6px 12px;
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
                padding: 4px 8px !important;
                background: rgba(255, 255, 255, 0.5);
            }
            
            .excel-table td {
                vertical-align: middle;
                border: none;
                padding: 2px 8px !important;
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
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Librerías necesarias -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let currentTable = null;
                let currentTerceroId = null;

                // =================================================================
                // == LÓGICA DE DATATABLES CORREGIDA ==
                // =================================================================

                /**
                * Inicializa o reinicializa DataTables en una tabla específica.
                * Ahora comprueba si la tabla tiene datos antes de inicializar.
                * @param {string} tableId - El ID de la tabla a inicializar (ej. '#tablaPrincipal').
                */
                function initializeDataTable(tableId) {
                    // Si ya existe una instancia de DataTables de una pestaña anterior, la destruimos.
                    if (currentTable) {
                        // Importante: solo destruimos la tabla si es diferente a la que queremos inicializar ahora.
                        if (currentTable.settings()[0].nTable.id !== tableId.replace('#', '')) {
                            currentTable.destroy();
                            currentTable = null;
                        }
                    }

                    const $table = $(tableId);

                    // Comprobación crucial: ¿La tabla tiene datos reales o solo el mensaje de "vacío"?
                    // Contamos las filas que NO tienen un 'colspan' en su primera celda.
                    const hasDataRows = $table.find('tbody tr').filter(function() {
                        return $(this).find('td:first').attr('colspan') === undefined;
                    }).length > 0;

                    if (hasDataRows) {
                        // Si hay datos, destruimos la instancia anterior (si existe) e inicializamos DataTables.
                        if (currentTable) {
                            currentTable.destroy();
                        }
                        
                        currentTable = $table.DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                { extend: 'excelHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>Excel' },
                                { extend: 'pdfHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file me-1"></i>PDF' },
                                { extend: 'print', className: 'btn btn-sm pastel-btn-light', text: '<i class="feather-printer me-1"></i>Imprimir' }
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
                            },
                            autoWidth: false,
                            columnDefs: [
                                { width: "5%", targets: 0 },  // ID
                                { width: "10%", targets: 1 }, // Código
                                { width: "20%", targets: 2 }, // Nombre / Razón Social
                                { width: "15%", targets: 3 }, // Contacto
                                { width: "10%", targets: 4 }, // Tipo
                                { width: "12%", targets: 5 }, // Congregación
                                { width: "12%", targets: 6 }, // Distrito
                                { width: "8%", targets: 7 }, // Estado
                                { width: "8%", targets: 8, orderable: false } // Acciones
                            ]
                        });

                    } else {
                        // Si no hay datos (solo la fila de "vacío"), no hacemos nada.
                        // Esto evita el error de "Incorrect column count".
                        // Nos aseguramos de que no quede una instancia de una tabla anterior.
                        if (currentTable) {
                            currentTable.destroy();
                            currentTable = null;
                        }
                        console.log(`Tabla ${tableId} está vacía. No se inicializará DataTables.`);
                        // Actualizamos el contador para que sea coherente
                        document.getElementById('resultCount').textContent = 'Mostrando 0 de 0 resultados';
                    }
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
                        } else if (targetPaneId === '#tab-active') {
                            targetTableId = '#tablaActivos';
                        } else if (targetPaneId === '#tab-inactive') {
                            targetTableId = '#tablaInactivos';
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
                // == MANEJO CORREGIDO DEL MODAL CON DELEGACIÓN DE EVENTOS ==
                // =================================================================

                // Función para manejar el clic y mostrar los datos
                function handleTerceroClick(event) {
                    // Previene el comportamiento por defecto del enlace
                    event.preventDefault();
                    // Detiene la propagación del evento
                    event.stopPropagation();

                    // Obtenemos el elemento que fue clickeado
                    const el = event.target;
                    const data = el.dataset;

                    // --- LÓGICA PARA ABRIR EL MODAL PRINCIPAL ---
                    document.getElementById('modal-id').textContent = `TERCERO #${data.id}`;
                    document.getElementById('modal-codigo').textContent = `Código: ${data.codigo}`;
                    document.getElementById('modal-nombre').textContent = data.nombre;
                    document.getElementById('modal-telefono').textContent = data.telefono;
                    document.getElementById('modal-email').textContent = data.email;
                    document.getElementById('modal-tipo').textContent = data.tipo;
                    document.getElementById('modal-congregacion').textContent = data.congregacion;
                    document.getElementById('modal-distrito').textContent = data.distrito;
                    document.getElementById('modal-direccion').textContent = data.direccion;
                    document.getElementById('modal-ciudad').textContent = data.ciudad;
                    document.getElementById('modal-fecha-creacion').textContent = data.fechaCreacion;
                    document.getElementById('modal-fecha-actualizacion').textContent = data.fechaActualizacion;

                    const estado = data.estado;
                    let estadoStyle = '';
                    switch(estado) {
                        case 'Activo': estadoStyle = 'background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;'; break;
                        case 'Inactivo': estadoStyle = 'background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;'; break;
                        default: estadoStyle = 'background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;';
                    }
                    document.getElementById('modal-estado').innerHTML = `<div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; ${estadoStyle} padding: 2px 6px; display: flex; align-items: center; justify-content: center;"><span>${estado}</span></div>`;
                    
                    // Asigna la función al botón de editar del modal
                    const editButton = document.getElementById('editFromModal');
                    if (editButton) {
                        editButton.onclick = function() { window.location.href = data.editUrl; };
                    }

                    // Muestra el modal principal
                    const modalElement = document.getElementById('detalleTerceroModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }

                // Adjuntamos el listener al contenedor de las pestañas.
                // Este contenedor no es modificado por DataTables, por lo que el listener siempre funcionará.
                const tabContent = document.getElementById('tercerosTabsContent');
                if (tabContent) {
                    tabContent.addEventListener('click', function(event) {
                        // Usamos .matches() para comprobar si el clic fue en un elemento con la clase 'tercero-id'
                        if (event.target.matches('.tercero-id')) {
                            handleTerceroClick(event);
                        }
                    });
                }

                // Función para aplicar filtros
                function applyFilters() {
                    const searchParams = new URLSearchParams();
                    if ($('#filterSearch').val()) searchParams.set('search', $('#filterSearch').val());
                    if ($('#filterType').val()) searchParams.set('type_filter', $('#filterType').val());
                    if ($('#filterCongregation').val()) searchParams.set('congregation_filter', $('#filterCongregation').val());
                    if ($('#filterDistrict').val()) searchParams.set('district_filter', $('#filterDistrict').val());
                    if ($('#filterStatus').val()) searchParams.set('status_filter', $('#filterStatus').val());
                    if ($('#filterCity').val()) searchParams.set('city_filter', $('#filterCity').val());
                    if ($('#filterPersonType').val()) searchParams.set('person_type_filter', $('#filterPersonType').val());
                    window.location.href = `${window.location.pathname}?${searchParams.toString()}`;
                }

                // Eventos para los filtros
                $('#applyFilters').on('click', applyFilters);
                $('#clearFilters').on('click', () => window.location.href = window.location.pathname);
                $('#exportBtn').on('click', () => { if(currentTable) currentTable.button(0).trigger(); });

                // SweetAlert — confirmaciones
                function confirmarAccion(titulo, texto, icono, callback, confirmColor = '#E6F3FF') {
                    Swal.fire({ 
                        title: titulo, 
                        text: texto, 
                        icon: icono, 
                        showCancelButton: true, 
                        confirmButtonColor: confirmColor, 
                        cancelButtonColor: '#F8E8FF', 
                        confirmButtonText: 'Sí, continuar', 
                        cancelButtonText: 'Cancelar', 
                        background: 'rgba(255, 255, 255, 0.9)', 
                        backdrop: 'rgba(0, 0, 0, 0.1)' 
                    }).then((result) => { 
                        if (result.isConfirmed) callback(); 
                    });
                }
                
                document.querySelectorAll('.btnCrear').forEach(btn => { 
                    btn.addEventListener('click', function (e) { 
                        e.preventDefault(); 
                        confirmarAccion('¿Crear un nuevo tercero?', 'Serás redirigido al formulario de creación.', 'info', () => { 
                            window.location.href = btn.getAttribute('href'); 
                        }); 
                    }); 
                });
                
                // Botón Cambiar Estado
                document.querySelectorAll('.btnCambiarEstado').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        const nuevoEstado = this.getAttribute('data-estado');
                        
                        confirmarAccion(
                            `¿Cambiar estado a ${nuevoEstado}?`, 
                            `El tercero pasará a estado ${nuevoEstado}.`, 
                            'question', 
                            () => {
                                // Aquí deberías hacer una llamada AJAX para cambiar el estado
                                // Por ahora, solo mostramos una notificación
                                Swal.fire({
                                    title: 'Estado actualizado',
                                    text: `El tercero ahora está ${nuevoEstado}`,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Recargar la página para ver los cambios
                                    window.location.reload();
                                });
                            },
                            '#17a2b8'
                        );
                    });
                });

                // Botón Eliminar
                document.querySelectorAll('.btnEliminar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        
                        Swal.fire({
                            title: '¿Estás seguro?',
                            html: `
                                <div class="text-start">
                                    <p>Vas a eliminar al tercero:</p>
                                    <div class="alert alert-danger">
                                        <strong>${nombre}</strong>
                                    </div>
                                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                            showClass: { 
                                popup: 'animate__animated animate__fadeInDown' 
                            },
                            hideClass: { 
                                popup: 'animate__animated animate__fadeOutUp' 
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Crear un formulario para enviar la solicitud DELETE
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/maestras/terceros/${id}`;
                                
                                // Añadir token CSRF
                                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                if (csrfToken) {
                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = '_token';
                                    csrfInput.value = csrfToken.getAttribute('content');
                                    form.appendChild(csrfInput);
                                }
                                
                                // Añadir método DELETE
                                const methodInput = document.createElement('input');
                                methodInput.type = 'hidden';
                                methodInput.name = '_method';
                                methodInput.value = 'DELETE';
                                form.appendChild(methodInput);
                                
                                // Enviar el formulario
                                document.body.appendChild(form);
                                form.submit();
                                
                                // Mostrar notificación de éxito
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: 'El tercero ha sido eliminado correctamente.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        });
                    });
                });

                // Atajos de teclado
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') { 
                        e.preventDefault(); 
                        $('#filterSearch').focus(); 
                    }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'n') { 
                        e.preventDefault(); 
                        window.location.href = "{{ route('maestras.terceros.create') }}"; 
                    }
                    if (e.key === 'Enter' && (e.target.id === 'filterSearch' || e.target.classList.contains('pastel-select'))) { 
                        e.preventDefault(); 
                        applyFilters(); 
                    }
                });

                // Efectos de entrada
                $('.excel-row').each(function(index) { 
                    $(this).css({ 'opacity': '0', 'transform': 'translateY(5px)' }); 
                    setTimeout(() => { 
                        $(this).animate({ 'opacity': '1', 'transform': 'translateY(0)' }, 200); 
                    }, index * 20); 
                });
                
                $('.glassmorphism-card').each(function(index) { 
                    $(this).addClass('fade-in-up'); 
                    $(this).css('animation-delay', `${index * 0.05}s`); 
                });
            });
        </script>
    @endpush
</x-base-layout>