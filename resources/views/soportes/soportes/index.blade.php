<x-base-layout>
    {{-- Alertas mejoradas con diseño --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="feather-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="feather-alert-circle me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filtros mejorados --}}
    <div class="card shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
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
                    <label class="form-label fw-semibold text-muted small">Área</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-layers"></i></span>
                        <select id="filterArea" class="form-select">
                            <option value="">Todas</option>
                            @foreach($soportes->pluck('cargo.gdoArea.nombre')->unique() as $area)
                                @if($area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Prioridad</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-flag"></i></span>
                        <select id="filterPrioridad" class="form-select">
                            <option value="">Todas</option>
                            @foreach($soportes->pluck('prioridad.nombre')->unique() as $p)
                                @if($p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Usuario</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-user"></i></span>
                        <select id="filterUsuario" class="form-select">
                            <option value="">Todos</option>
                            @foreach($soportes->pluck('usuario.name')->unique() as $u)
                                @if($u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">Fecha Creación</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" id="filterFecha" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span id="resultCount">Mostrando todos los resultados</span>
                        </small>
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

    {{-- Tabla mejorada --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Listado de Soportes</h5>
                    <p class="text-muted mb-0 small">Gestiona y monitorea todos los soportes del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                        <i class="feather-download me-1"></i>Exportar
                    </button>
                    <a href="{{ route('soportes.soportes.create') }}" class="btn btn-success btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nuevo Soporte</span>
                    </a>
                </div>
            </div>

            {{-- Tabs por categoría mejorados --}}
            <ul class="nav nav-tabs mb-4" id="soporteTabs" role="tablist">
                @candirect('soporte.lista.todo')    
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link"
                            id="tab-1-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-1"
                            type="button"
                            role="tab"
                            aria-controls="tab-1"
                            aria-selected="false">
                        <i class="feather-inbox me-2"></i>
                        TODOS
                        <span class="badge bg-secondary ms-1">                                
                            {{ $soportes->filter(function($soporte) {
                                return auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente');
                            })->count() }}   
                        </span>                        
                    </button>
                </li>  
                @endcandirect
                @foreach ($categorias as $nombreCategoria => $soportesCategoria)
                    @php
                        $indice = $loop->index + 2;
                        $permiso = 'soporte.lista.' . strtolower($nombreCategoria);
                        $icono = match(strtolower($nombreCategoria)) {
                            'soporte' => 'feather-help-circle',
                            'sistemas' => 'feather-server',
                            'infraestructura' => 'feather-hard-drive',
                            'redes' => 'feather-wifi',
                            'desarrollo' => 'feather-code',
                            default => 'feather-folder'
                        };
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
                            <i class="{{ $icono }} me-2"></i>
                            {{ $nombreCategoria }}
                            <span class="badge bg-secondary ms-1">
                                {{ $soportesCategoria->filter(function($soporte) {
                                    return auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente');
                                })->count() }}                                
                            </span>
                        </button>
                    </li> 
                    @endcandirect
                @endforeach
            </ul>

            {{-- Contenido --}}
            <div class="tab-content" id="soporteTabsContent">
                {{-- TAB todos --}}
                <div class="tab-pane fade" id="tab-1" role="tabpanel" aria-labelledby="tab-1-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle soporteTable" id="tablaPrincipal">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Creado Por</th>
                                    <th>Área</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Descripción</th>
                                    <th>Asignado</th>
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
                                        $estadoIcono = match($soporte->estadoSoporte->nombre ?? '') {
                                            'Pendiente' => 'feather-clock',
                                            'En Proceso' => 'feather-loader',
                                            'Cerrado' => 'feather-check-circle',
                                            default => 'feather-help-circle'
                                        };
                                    @endphp
                                    @if(auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente'))
                                    <tr class="table-row-hover">
                                        {{-- ID --}}
                                        <td>
                                            <a href="javascript:void(0)"
                                               class="soporte-id fw-bold text-decoration-none"
                                               data-id="{{ $soporte->id }}"
                                               data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                               data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                               data-area="{{ $areaModal }}"
                                               data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                               data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                               data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                               data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                               data-detalles="{{ $soporte->detalles_soporte }}"
                                               data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                               data-maetercero="{{ $soporte->maeTercero->nom_ter ?? 'N/A' }}"
                                               data-escalado="{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}"
                                               data-estado="{{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}">
                                                #{{ $soporte->id }}
                                            </a>
                                        </td>

                                        {{-- Fecha Creado --}}
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $soporte->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $soporte->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>

                                        {{-- Creado Por --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($soporte->usuario->name ?? 'N/A', 0, 1)) }}
                                                </div>
                                                <span>{{ $soporte->usuario->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>

                                        {{-- Área --}}
                                        <td><span class="badge bg-light text-dark">{{ $areaModal }}</span></td>

                                        {{-- Categoría --}}
                                        <td>{{ $soporte->categoria->nombre ?? 'N/A' }}</td>

                                        {{-- Tipo --}}
                                        <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                        {{-- Prioridad --}}
                                        <td>
                                            <span class="badge bg-{{ $prioridadColor }} d-flex align-items-center justify-content-center" style="width: 80px;">
                                                {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                            </span>
                                        </td>

                                        {{-- Descripción --}}
                                        <td>
                                            <div class="description-cell" 
                                                 title="{{ $soporte->detalles_soporte }}" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top">
                                                {{ Str::limit($soporte->detalles_soporte, 50) }}
                                            </div>
                                        </td>

                                        {{-- Asignado --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($soporte->scpUsuarioAsignado)
                                                    <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A', 0, 1)) }}
                                                    </div>
                                                    <span>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Estado --}}
                                        <td>
                                            <span class="badge bg-{{ $estadoColor }} d-flex align-items-center" style="width: 100px;">
                                                <i class="feather {{ $estadoIcono }} me-1" style="font-size: 12px;"></i>
                                                {{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}
                                            </span>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="feather-more-horizontal"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.soportes.show', $soporte) }}">
                                                            <i class="feather-eye me-2"></i> Ver
                                                        </a>
                                                    </li>
{{--                                                     <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.soportes.edit', $soporte) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('soportes.soportes.destroy', $soporte) }}" method="POST" class="formEliminar">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item btnEliminar text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="feather-inbox empty-icon"></i>
                                                <h6 class="mt-2">No hay soportes registrados</h6>
                                                <p class="text-muted">Comienza creando un nuevo soporte</p>
                                                <a href="{{ route('soportes.soportes.create') }}" class="btn btn-sm btn-primary">
                                                    <i class="feather-plus me-1"></i> Crear Soporte
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- fin tab Todos --}}
                @foreach ($categorias as $nombreCategoria => $soportesCategoria)
                @php 
                    $permiso = 'soporte.lista.' . strtolower($nombreCategoria);
                    $icono = match(strtolower($nombreCategoria)) {
                        'soporte' => 'feather-help-circle',
                        'sistemas' => 'feather-server',
                        'infraestructura' => 'feather-hard-drive',
                        'redes' => 'feather-wifi',
                        'desarrollo' => 'feather-code',
                        default => 'feather-folder'
                    };
                @endphp
                @candirect($permiso)   
                    <div class="tab-pane fade @if($loop->first) show active @endif" 
                         id="tab-{{ $loop->index + 2 }}" 
                         role="tabpanel" 
                         aria-labelledby="tab-{{ $loop->index + 2 }}-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle soporteTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Creado Por</th>
                                        <th>Área</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Prioridad</th>
                                        <th>Descripción</th>
                                        <th>Asignado</th>
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
                                            $estadoIcono = match($soporte->estadoSoporte->nombre ?? '') {
                                                'Pendiente' => 'feather-clock',
                                                'En Proceso' => 'feather-loader',
                                                'Cerrado' => 'feather-check-circle',
                                                default => 'feather-help-circle'
                                            };
                                        @endphp
                                        @if(auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente'))                                        
                                        <tr class="table-row-hover">
                                            {{-- ID --}}
                                            <td>
                                                <a href="javascript:void(0)"
                                                   class="soporte-id fw-bold text-decoration-none"
                                                   data-id="{{ $soporte->id }}"
                                                   data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                                   data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                                   data-area="{{ $areaModal }}"
                                                   data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                                   data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                                   data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                   data-prioridad="{{ $soporte->prioridad->nombre ?? 'N/A' }}"
                                                   data-detalles="{{ $soporte->detalles_soporte }}"
                                                   data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                                   data-maetercero="{{ $soporte->maeTercero->nom_ter ?? 'N/A' }}"
                                                   data-escalado="{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}"
                                                   data-estado="{{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}">
                                                    #{{ $soporte->id }}
                                                </a>
                                            </td>

                                            {{-- Fecha Creado --}}
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $soporte->created_at->format('d/m/Y') }}</span>
                                                    <small class="text-muted">{{ $soporte->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>

                                            {{-- Creado Por --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($soporte->usuario->name ?? 'N/A', 0, 1)) }}
                                                    </div>
                                                    <span>{{ $soporte->usuario->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Área --}}
                                            <td><span class="badge bg-light text-dark">{{ $areaModal }}</span></td>

                                            {{-- Categoría --}}
                                            <td>{{ $soporte->categoria->nombre ?? 'N/A' }}</td>

                                            {{-- Tipo --}}
                                            <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                            {{-- Prioridad --}}
                                            <td>
                                                <span class="badge bg-{{ $prioridadColor }} d-flex align-items-center justify-content-center" style="width: 80px;">
                                                    {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Descripción --}}
                                            <td>
                                                <div class="description-cell" 
                                                     title="{{ $soporte->detalles_soporte }}" 
                                                     data-bs-toggle="tooltip" 
                                                     data-bs-placement="top">
                                                    {{ Str::limit($soporte->detalles_soporte, 50) }}
                                                </div>
                                            </td>

                                            {{-- Asignado --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($soporte->scpUsuarioAsignado)
                                                        <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            {{ strtoupper(substr($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A', 0, 1)) }}
                                                        </div>
                                                        <span>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A' }}</span>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Estado --}}
                                            <td>
                                                <span class="badge bg-{{ $estadoColor }} d-flex align-items-center" style="width: 100px;">
                                                    <i class="feather {{ $estadoIcono }} me-1" style="font-size: 12px;"></i>
                                                    {{ $soporte->estadoSoporte->nombre ?? 'Pendiente' }}
                                                </span>
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('soportes.soportes.show', $soporte) }}">
                                                                <i class="feather-eye me-2"></i> Ver detalles
                                                            </a>
                                                        </li>
{{--                                                         <li>
                                                            <a class="dropdown-item btnEditar" href="{{ route('soportes.soportes.edit', $soporte) }}">
                                                                <i class="feather-edit-3 me-2"></i> Editar
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('soportes.soportes.destroy', $soporte) }}" method="POST" class="formEliminar">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="dropdown-item btnEliminar text-danger">
                                                                    <i class="feather-trash-2 me-2"></i> Eliminar
                                                                </button>
                                                            </form>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="{{ $icono }} empty-icon"></i>
                                                    <h6 class="mt-2">No hay soportes en {{ $nombreCategoria }}</h6>
                                                    <p class="text-muted">No se encontraron soportes en esta categoría</p>
                                                </div>
                                            </td>
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

    {{-- Modal mejorado --}}
    <div class="modal fade" id="detalleSoporteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="feather-help-circle me-2"></i>
                        Detalles del Soporte
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
                            <span class="badge bg-primary fs-6" id="modal-estado"></span>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-semibold">Información General</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Creado Por</small>
                                    <span id="modal-creado"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Área</small>
                                    <span id="modal-area"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Categoría</small>
                                    <span id="modal-categoria"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Módulo</small>
                                    <span id="modal-tipo-subtipo"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Prioridad</small>
                                    <span id="modal-prioridad"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Última Actualización</small>
                                    <span id="modal-fecha-update"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-semibold">Descripción</h6>
                        </div>
                        <div class="card-body">
                            <p id="modal-detalles"></p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-semibold">Asignación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Jefe de Área</small>
                                    <span id="modal-maeTercero"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Asignado a</small>
                                    <span id="modal-escalado"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="editFromModal">
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
            /* Estilos personalizados */
            .avatar-xs {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
                font-weight: 600;
            }
            
            .table-row-hover {
                transition: all 0.2s ease;
            }
            
            .table-row-hover:hover {
                background-color: #f8f9fa;
                transform: translateX(3px);
            }
            
            .description-cell {
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .empty-state {
                padding: 2rem;
                text-align: center;
            }
            
            .empty-icon {
                font-size: 3rem;
                color: #6c757d;
            }
            
            .custom-tooltip .tooltip-inner {
                background-color: rgba(33, 37, 41, 0.9);
                color: #fff;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 0.85rem;
                max-width: 300px;
                word-wrap: break-word;
            }
            
            .input-group-text {
                background-color: #f8f9fa;
                border-right: none;
            }
            
            .form-control, .form-select {
                border-left: none;
            }
            
            .form-control:focus, .form-select:focus {
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
                border-color: #86b7fe;
            }
            
            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }
            
            .nav-tabs .nav-link {
                font-weight: 500;
                padding: 0.75rem 1rem;
            }
            
            .nav-tabs .nav-link.active {
                font-weight: 600;
            }
            
            .dropdown-menu {
                border: 1px solid rgba(0,0,0,.125);
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
            }
            
            @media (max-width: 768px) {
                .table-responsive {
                    font-size: 0.875rem;
                }
                
                .btn-group-sm .btn {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
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
                let tables = [];
                let currentSoporteId = null;

                // Inicializar todas las tablas con clase .soporteTable
                $('.soporteTable').each(function () {
                    if (!$.fn.dataTable.isDataTable(this)) {
                        let t = $(this).DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                { extend: 'excelHtml5', className: 'btn btn-sm btn-success', text: '<i class="feather-file-text me-1"></i>Excel' },
                                { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="feather-file me-1"></i>PDF' },
                                { extend: 'print', className: 'btn btn-sm btn-secondary', text: '<i class="feather-printer me-1"></i>Imprimir' }
                            ],
                            pageLength: 10,
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
                                paginate: {
                                    first: "Primero",
                                    last: "Último",
                                    next: "Siguiente",
                                    previous: "Anterior"
                                }
                            }
                        });
                        tables.push(t);
                    } else {
                        tables.push($(this).DataTable());
                    }
                });

                // Función auxiliar para aplicar filtros en todas las tablas
                function aplicarFiltro(colIndex, value) {
                    tables.forEach(function (t) {
                        t.column(colIndex).search(value).draw();
                    });
                    updateResultCount();
                }

                // Actualizar contador de resultados
                function updateResultCount() {
                    const activeTable = $('.tab-pane.active .soporteTable').DataTable();
                    const info = activeTable.page.info();
                    document.getElementById('resultCount').textContent = 
                        `Mostrando ${info.recordsDisplay} de ${info.recordsTotal} resultados`;
                }

                // Filtros (área, prioridad, usuario, fecha)
                $('#filterArea').on('change', function () {
                    aplicarFiltro(3, this.value);
                });

                $('#filterPrioridad').on('change', function () {
                    aplicarFiltro(6, this.value);
                });

                $('#filterUsuario').on('change', function () {
                    aplicarFiltro(2, this.value);
                });

                $('#filterFecha').on('change', function () {
                    let val = this.value;
                    if (val) {
                        let partes = val.split("-");
                        let fechaFormateada = partes[2] + "/" + partes[1] + "/" + partes[0];
                        aplicarFiltro(1, fechaFormateada);
                    } else {
                        aplicarFiltro(1, "");
                    }
                });

                // Limpiar filtros
                $('#clearFilters').on('click', function() {
                    $('#filterArea, #filterPrioridad, #filterUsuario, #filterFecha').val('');
                    tables.forEach(function(t) {
                        t.search('').columns().search('').draw();
                    });
                    updateResultCount();
                });

                // Cambiar vista (tabla/tarjetas)
                $('[data-view]').on('click', function() {
                    $('[data-view]').removeClass('active');
                    $(this).addClass('active');
                    // Aquí iría la lógica para cambiar entre vista de tabla y tarjetas
                });

                // Botón exportar
                $('#exportBtn').on('click', function() {
                    const activeTable = $('.tab-pane.active .soporteTable').DataTable();
                    activeTable.button(0).trigger();
                });

                // Modal dinámico al hacer clic en .soporte-id
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('soporte-id')) {
                        const el = e.target;
                        currentSoporteId = el.dataset.id;
                        
                        // Llenar el modal con los datos
                        document.getElementById('modal-id').textContent = `#${el.dataset.id}`;
                        document.getElementById('modal-fecha').textContent = el.dataset.fecha;
                        document.getElementById('modal-creado').textContent = el.dataset.creado;
                        document.getElementById('modal-area').textContent = el.dataset.area ?? 'Soporte';
                        document.getElementById('modal-categoria').textContent = el.dataset.categoria ?? 'N/A';
                        document.getElementById('modal-tipo-subtipo').textContent = 
                            (el.dataset.tipo ?? 'N/A') + ' / ' + (el.dataset.subtipo ?? 'N/A');
                        document.getElementById('modal-prioridad').textContent = el.dataset.prioridad;
                        document.getElementById('modal-detalles').textContent = el.dataset.detalles;
                        document.getElementById('modal-fecha-update').textContent = el.dataset.updated ?? el.dataset.fecha;
                        document.getElementById('modal-maeTercero').textContent = el.dataset.maetercero ?? 'N/A';
                        document.getElementById('modal-escalado').textContent = el.dataset.escalado ?? 'Sin asignar';
                        document.getElementById('modal-estado').textContent = el.dataset.estado;

                        // Configurar botón de editar
                        document.getElementById('editFromModal').onclick = function() {
                            window.location.href = `/soportes/soportes/${currentSoporteId}/edit`;
                        };

                        new bootstrap.Modal(document.getElementById('detalleSoporteModal')).show();
                    }
                });

                // SweetAlert — confirmaciones
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => { 
                        if (result.isConfirmed) callback(); 
                    });
                }

                // Botón crear
                document.querySelectorAll('.btnCrear').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion(
                            '¿Crear un nuevo Soporte?', 
                            'Serás redirigido al formulario de creación.', 
                            'info', 
                            () => { window.location.href = btn.getAttribute('href'); }
                        );
                    });
                });

                // Botón editar
                document.querySelectorAll('.btnEditar').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        confirmarAccion(
                            '¿Editar este Soporte?', 
                            'Serás redirigido al formulario de edición.', 
                            'question', 
                            () => { window.location.href = btn.getAttribute('href'); }
                        );
                    });
                });

                // Formulario eliminar
                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        confirmarAccion(
                            '¿Eliminar este Soporte?',
                            'Esta acción eliminará el soporte y todas sus observaciones relacionadas. Esta acción no se puede deshacer.',
                            'warning',
                            () => { form.submit(); }
                        );
                    });
                });

                // Activar tooltips de Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (el) { 
                    return new bootstrap.Tooltip(el, {
                        trigger: 'hover focus',
                        delay: { show: 300, hide: 100 }
                    }); 
                });

                // Atajos de teclado
                document.addEventListener('keydown', function(e) {
                    // Ctrl/Cmd + K para búsqueda rápida
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        $('#filterArea').focus();
                    }
                    
                    // Ctrl/Cmd + N para nuevo soporte
                    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                        e.preventDefault();
                        window.location.href = "{{ route('soportes.soportes.create') }}";
                    }
                });

                // Actualizar contador de resultados al cambiar de pestaña
                document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('shown.bs.tab', function() {
                        setTimeout(updateResultCount, 100);
                    });
                });

                // Inicializar contador
                updateResultCount();
            });
        </script>
    @endpush
</x-base-layout> 