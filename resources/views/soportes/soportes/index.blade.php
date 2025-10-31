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
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Área</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-layers"></i></span>
                        <select id="filterArea" class="form-select pastel-select">
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
                    <label class="form-label fw-semibold text-muted small mb-1">Prioridad</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-flag"></i></span>
                        <select id="filterPrioridad" class="form-select pastel-select">
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
                    <label class="form-label fw-semibold text-muted small mb-1">Usuario</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-user"></i></span>
                        <select id="filterUsuario" class="form-select pastel-select">
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
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Creación</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" id="filterFecha" class="form-control pastel-input" />
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
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Listado de Soportes</h5>
                    <p class="text-muted mb-0 small">Gestiona y monitorea todos los soportes del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                        <i class="feather-download me-1"></i>Exportar
                    </button>
                    <a href="{{ route('soportes.soportes.create') }}" class="btn btn-success pastel-btn-gradient btnCrear">
                        <i class="feather-plus me-2"></i>
                        <span>Crear Nuevo Soporte</span>
                    </a>
                </div>
            </div>

            {{-- Tabs por categoría mejorados --}}
            <ul class="nav nav-tabs mb-3 pastel-tabs" id="soporteTabs" role="tablist">
                @candirect('soporte.lista.todo')    
                <li class="nav-item" role="presentation">                  
                    <button class="nav-link pastel-tab"
                            id="tab-1-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-1"
                            type="button"
                            role="tab"
                            aria-controls="tab-1"
                            aria-selected="false">
                        <i class="feather-inbox me-2"></i>
                        TODOS
                        <span class="badge pastel-badge-globito ms-1" style="background-color: #F0F8FF !important; color: #4682B4 !important;">                                
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
                        
                        // Asignar colores pastel únicos para cada categoría
                        $colorPastel = match(strtolower($nombreCategoria)) {
                            'soporte' => '#F8E8FF',
                            'sistemas' => '#E6F3FF',
                            'infraestructura' => '#E8F5E8',
                            'redes' => '#FFF4E6',
                            'desarrollo' => '#FCE4EC',
                            default => '#F3E5F5'
                        };
                        
                        $colorTexto = match(strtolower($nombreCategoria)) {
                            'soporte' => '#6B5B95',
                            'sistemas' => '#5C7CFA',
                            'infraestructura' => '#2E7D32',
                            'redes' => '#FF9800',
                            'desarrollo' => '#C2185B',
                            default => '#9C27B0'
                        };
                    @endphp
                    @candirect($permiso)   
                    <li class="nav-item" role="presentation">                  
                        <button class="nav-link pastel-tab @if($loop->first) active @endif"
                                id="tab-{{ $indice }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-{{ $indice }}"
                                type="button"
                                role="tab"
                                aria-controls="tab-{{ $indice }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <i class="{{ $icono }} me-2"></i>
                            {{ $nombreCategoria }}
                            <span class="badge pastel-badge-globito ms-1" style="background-color: {{ $colorPastel }} !important; color: {{ $colorTexto }} !important;">
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
                        <table class="table table-hover align-middle soporteTable pastel-table excel-table" id="tablaPrincipal">
                            <thead class="table-light pastel-thead">
                                <tr>
                                    <th class="py-1">ID</th>
                                    <th class="py-1">Fecha</th>
                                    <th class="py-1">Creado Por</th>
                                    <th class="py-1">Área</th>
                                    <th class="py-1">Categoría</th>
                                    <th class="py-1">Tipo</th>
                                    <th class="py-1">Prioridad</th>
                                    <th class="py-1">Descripción</th>
                                    <th class="py-1">Asignado</th>
                                    <th class="py-1">Estado</th>
                                    <th class="py-1 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($soportes as $soporte)
                                    @php
                                        $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                        $prioridadNombre = $soporte->prioridad->nombre ?? '';
                                        $prioridadIcono = '';
                                        
                                        // Sistema de colores pasteles SUAVES para prioridades
                                        switch($prioridadNombre) {
                                            case 'Alta':
                                                $prioridadIcono = 'feather-alert-triangle';
                                                break;
                                            case 'Media':
                                                $prioridadIcono = 'feather-alert-circle';
                                                break;
                                            case 'Baja':
                                                $prioridadIcono = 'feather-info';
                                                break;
                                            default:
                                                $prioridadIcono = 'feather-help-circle';
                                        }
                                        
                                        $estadoNombre = $soporte->estadoSoporte->nombre ?? '';
                                        $estadoIcono = '';
                                        
                                        switch($estadoNombre) {
                                            case 'Pendiente':
                                                $estadoIcono = 'feather-clock';
                                                break;
                                            case 'En Proceso':
                                                $estadoIcono = 'feather-loader';
                                                break;
                                            case 'Cerrado':
                                                $estadoIcono = 'feather-check-circle';
                                                break;
                                            default:
                                                $estadoIcono = 'feather-help-circle';
                                        }
                                    @endphp
                                    @if(auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente'))
                                    <tr class="table-row-hover pastel-row excel-row" data-prioridad="{{ $prioridadNombre }}">
                                        {{-- ID --}}
                                        <td class="py-1">
                                            <a href="javascript:void(0)"
                                               class="soporte-id fw-bold text-decoration-none pastel-link"
                                               data-id="{{ $soporte->id }}"
                                               data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                               data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                               data-area="{{ $areaModal }}"
                                               data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                               data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                               data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                               data-prioridad="{{ $prioridadNombre }}"
                                               data-detalles="{{ $soporte->detalles_soporte }}"
                                               data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                               data-maetercero="{{ $soporte->maeTercero->nom_ter ?? 'N/A' }}"
                                               data-escalado="{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}"
                                               data-estado="{{ $estadoNombre }}">
                                                #{{ $soporte->id }}
                                            </a>
                                        </td>

                                        {{-- Fecha Creado --}}
                                        <td class="py-1">
                                            <div class="d-flex flex-column">
                                                <span class="small">{{ $soporte->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $soporte->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>

                                        {{-- Creado Por --}}
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-1">
                                                    {{ strtoupper(substr($soporte->usuario->name ?? 'N/A', 0, 1)) }}
                                                </div>
                                                <span class="small">{{ $soporte->usuario->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>

                                        {{-- Área - COLORES PASTELES SUAVES --}}
                                        <td class="py-1">
                                            <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                {{ $areaModal }}
                                            </span>
                                        </td>

                                        {{-- Categoría --}}
                                        <td class="py-1 small">{{ $soporte->categoria->nombre ?? 'N/A' }}</td>

                                        {{-- Tipo --}}
                                        <td class="py-1 small">{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                        {{-- Prioridad con colores pasteles SUAVES --}}
                                        <td class="py-1">
                                            <div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                 @if($prioridadNombre === 'Alta')
                                                     background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                 @elseif($prioridadNombre === 'Media')
                                                     background-color: #FFF4E6 !important; color: #FF9800 !important; border: 1px solid #FFE0B2 !important;
                                                 @elseif($prioridadNombre === 'Baja')
                                                     background-color: #E6F3FF !important; color: #5C7CFA !important; border: 1px solid #C5D9FF !important;
                                                 @else
                                                     background-color: #F3E5F5 !important; color: #9C27B0 !important; border: 1px solid #E1BEE7 !important;
                                                 @endif
                                                 padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="feather {{ $prioridadIcono }}" style="font-size: 8px; margin-right: 2px;"></i>
                                                <span>{{ $prioridadNombre }}</span>
                                            </div>
                                        </td>

                                        {{-- Descripción --}}
                                        <td class="py-1">
                                            <div class="description-cell-excel" 
                                                 title="{{ $soporte->detalles_soporte }}" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top">
                                                <span class="small">{{ Str::limit($soporte->detalles_soporte, 35) }}</span>
                                            </div>
                                        </td>

                                        {{-- Asignado --}}
                                        <td class="py-1">
                                            <div class="d-flex align-items-center">
                                                @if($soporte->scpUsuarioAsignado)
                                                    <div class="avatar-xs-excel pastel-avatar-success text-white rounded-circle d-flex align-items-center justify-content-center me-1">
                                                        {{ strtoupper(substr($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A', 0, 1)) }}
                                                    </div>
                                                    <span class="small">{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted small">Sin asignar</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Estado con colores pasteles SUAVES --}}
                                        <td class="py-1">
                                            <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                 @if($estadoNombre === 'Pendiente')
                                                     background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;
                                                 @elseif($estadoNombre === 'En Proceso')
                                                     background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;
                                                 @elseif($estadoNombre === 'Cerrado')
                                                     background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                 @else
                                                     background-color: #FCE4EC !important; color: #C2185B !important; border: 1px solid #F8BBD0 !important;
                                                 @endif
                                                 padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                <i class="feather {{ $estadoIcono }}" style="font-size: 8px; margin-right: 2px;"></i>
                                                <span>{{ $estadoNombre }}</span>
                                            </div>
                                        </td>

                                        {{-- Acciones - DROPUP SIMPLE --}}
                                        <td class="text-end">
                                            <div>
                                                <button type="button"
                                                        class="btn btn-sm btn-light"
                                                        style="padding: 2px 6px; font-size: 0.7rem;"
                                                        onclick="window.location.href='{{ route('soportes.soportes.show', ['scpSoporte' => $soporte->id]) }}'">
                                                    <i class="feather-eye me-1"></i> Ver
                                                </button>
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
                                                <a href="{{ route('soportes.soportes.create') }}" class="btn btn-sm pastel-btn-gradient">
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
                            <table class="table table-hover align-middle soporteTable pastel-table excel-table">
                                <thead class="table-light pastel-thead">
                                    <tr>
                                        <th class="py-1">ID</th>
                                        <th class="py-1">Fecha</th>
                                        <th class="py-1">Creado Por</th>
                                        <th class="py-1">Área</th>
                                        <th class="py-1">Categoría</th>
                                        <th class="py-1">Tipo</th>
                                        <th class="py-1">Prioridad</th>
                                        <th class="py-1">Descripción</th>
                                        <th class="py-1">Asignado</th>
                                        <th class="py-1">Estado</th>
                                        <th class="py-1 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>   
                                    @forelse ($soportesCategoria as $soporte)
                                        @php
                                            $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                            $prioridadNombre = $soporte->prioridad->nombre ?? '';
                                            $prioridadIcono = '';
                                            
                                            // Sistema de colores pasteles SUAVES para prioridades
                                            switch($prioridadNombre) {
                                                case 'Alta':
                                                    $prioridadIcono = 'feather-alert-triangle';
                                                    break;
                                                case 'Media':
                                                    $prioridadIcono = 'feather-alert-circle';
                                                    break;
                                                case 'Baja':
                                                    $prioridadIcono = 'feather-info';
                                                    break;
                                                default:
                                                    $prioridadIcono = 'feather-help-circle';
                                            }
                                            
                                            $estadoNombre = $soporte->estadoSoporte->nombre ?? '';
                                            $estadoIcono = '';
                                            
                                            switch($estadoNombre) {
                                                case 'Pendiente':
                                                    $estadoIcono = 'feather-clock';
                                                    break;
                                                case 'En Proceso':
                                                    $estadoIcono = 'feather-loader';
                                                    break;
                                                case 'Cerrado':
                                                    $estadoIcono = 'feather-check-circle';
                                                    break;
                                                default:
                                                    $estadoIcono = 'feather-help-circle';
                                            }
                                        @endphp
                                        @if(auth()->user()->id == $soporte->usuario->id || auth()->user()->id === ($soporte->scpUsuarioAsignado?->usuario) || auth()->user()->hasDirectPermission('soporte.lista.agente'))                                        
                                        <tr class="table-row-hover pastel-row excel-row" data-prioridad="{{ $prioridadNombre }}">
                                            {{-- ID --}}
                                            <td class="py-1">
                                                <a href="javascript:void(0)"
                                                   class="soporte-id fw-bold text-decoration-none pastel-link"
                                                   data-id="{{ $soporte->id }}"
                                                   data-fecha="{{ $soporte->created_at->format('d/m/Y H:i') }}"
                                                   data-creado="{{ $soporte->usuario->name ?? 'N/A' }}"
                                                   data-area="{{ $areaModal }}"
                                                   data-categoria="{{ $soporte->categoria->nombre ?? 'N/A' }}"
                                                   data-tipo="{{ $soporte->tipo->nombre ?? 'N/A' }}"
                                                   data-subtipo="{{ $soporte->subTipo->nombre ?? 'N/A' }}"
                                                   data-prioridad="{{ $prioridadNombre }}"
                                                   data-detalles="{{ $soporte->detalles_soporte }}"
                                                   data-updated="{{ $soporte->updated_at->format('d/m/Y H:i') }}"
                                                   data-maetercero="{{ $soporte->maeTercero->nom_ter ?? 'N/A' }}"
                                                   data-escalado="{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}"
                                                   data-estado="{{ $estadoNombre }}">
                                                    #{{ $soporte->id }}
                                                </a>
                                            </td>

                                            {{-- Fecha Creado --}}
                                            <td class="py-1">
                                                <div class="d-flex flex-column">
                                                    <span class="small">{{ $soporte->created_at->format('d/m/Y') }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">{{ $soporte->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>

                                            {{-- Creado Por --}}
                                            <td class="py-1">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs-excel pastel-avatar-primary text-white rounded-circle d-flex align-items-center justify-content-center me-1">
                                                        {{ strtoupper(substr($soporte->usuario->name ?? 'N/A', 0, 1)) }}
                                                    </div>
                                                    <span class="small">{{ $soporte->usuario->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Área - COLORES PASTELES SUAVES --}}
                                            <td class="py-1">
                                                <span style="background-color: #F8E8FF !important; color: #6B5B95 !important; border-radius: 6px; padding: 1px 4px; font-size: 0.65rem; font-weight: 500; border: 1px solid #E8D8FF; display: inline-block;">
                                                    {{ $areaModal }}
                                                </span>
                                            </td>

                                            {{-- Categoría --}}
                                            <td class="py-1 small">{{ $soporte->categoria->nombre ?? 'N/A' }}</td>

                                            {{-- Tipo --}}
                                            <td class="py-1 small">{{ $soporte->tipo->nombre ?? 'N/A' }}</td>

                                            {{-- Prioridad con colores pasteles SUAVES --}}
                                            <td class="py-1">
                                                <div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                     @if($prioridadNombre === 'Alta')
                                                         background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;
                                                     @elseif($prioridadNombre === 'Media')
                                                         background-color: #FFF4E6 !important; color: #FF9800 !important; border: 1px solid #FFE0B2 !important;
                                                     @elseif($prioridadNombre === 'Baja')
                                                         background-color: #E6F3FF !important; color: #5C7CFA !important; border: 1px solid #C5D9FF !important;
                                                     @else
                                                         background-color: #F3E5F5 !important; color: #9C27B0 !important; border: 1px solid #E1BEE7 !important;
                                                     @endif
                                                     padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather {{ $prioridadIcono }}" style="font-size: 8px; margin-right: 2px;"></i>
                                                    <span>{{ $prioridadNombre }}</span>
                                                </div>
                                            </td>

                                            {{-- Descripción --}}
                                            <td class="py-1">
                                                <div class="description-cell-excel" 
                                                     title="{{ $soporte->detalles_soporte }}" 
                                                     data-bs-toggle="tooltip" 
                                                     data-bs-placement="top">
                                                    <span class="small">{{ Str::limit($soporte->detalles_soporte, 35) }}</span>
                                                </div>
                                            </td>

                                            {{-- Asignado --}}
                                            <td class="py-1">
                                                <div class="d-flex align-items-center">
                                                    @if($soporte->scpUsuarioAsignado)
                                                        <div class="avatar-xs-excel pastel-avatar-success text-white rounded-circle d-flex align-items-center justify-content-center me-1">
                                                            {{ strtoupper(substr($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A', 0, 1)) }}
                                                        </div>
                                                        <span class="small">{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'N/A' }}</span>
                                                    @else
                                                        <span class="text-muted small">Sin asignar</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Estado con colores pasteles SUAVES --}}
                                            <td class="py-1">
                                                <div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; 
                                                     @if($estadoNombre === 'Pendiente')
                                                         background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;
                                                     @elseif($estadoNombre === 'En Proceso')
                                                         background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;
                                                     @elseif($estadoNombre === 'Cerrado')
                                                         background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;
                                                     @else
                                                         background-color: #FCE4EC !important; color: #C2185B !important; border: 1px solid #F8BBD0 !important;
                                                     @endif
                                                     padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather {{ $estadoIcono }}" style="font-size: 8px; margin-right: 2px;"></i>
                                                    <span>{{ $estadoNombre }}</span>
                                                </div>
                                            </td>

                                            {{-- Acciones - DROPUP SIMPLE --}}
                                            <td class="text-end">
                                                <div>
                                                    <button type="button"
                                                            class="btn btn-sm btn-light"
                                                            style="padding: 2px 6px; font-size: 0.7rem;"
                                                            onclick="window.location.href='{{ route('soportes.soportes.show', ['scpSoporte' => $soporte->id]) }}'">
                                                        <i class="feather-eye me-1"></i> Ver
                                                    </button>
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

    <!-- Leyenda de colores pasteles -->
    <div class="card shadow-sm mb-3 glassmorphism-card">
        <div class="card-body p-2">
            <h6 class="fw-semibold mb-2">
                <i class="feather-palette me-2"></i>Leyenda de Prioridades
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <i class="feather-alert-triangle" style="font-size: 8px; margin-right: 2px;"></i>
                        <span>Alta</span>
                    </div>
                    <small class="text-muted">Urgente</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #FFF4E6 !important; color: #FF9800 !important; border: 1px solid #FFE0B2 !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <i class="feather-alert-circle" style="font-size: 8px; margin-right: 2px;"></i>
                        <span>Media</span>
                    </div>
                    <small class="text-muted">Importante</small>
                </div>
                <div class="d-flex align-items-center">
                    <div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; background-color: #E6F3FF !important; color: #5C7CFA !important; border: 1px solid #C5D9FF !important; padding: 2px 6px; display: flex; align-items: center; justify-content: center; margin-right: 6px;">
                        <i class="feather-info" style="font-size: 8px; margin-right: 2px;"></i>
                        <span>Baja</span>
                    </div>
                    <small class="text-muted">Normal</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal mejorado --}}
    <div class="modal fade" id="detalleSoporteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glassmorphism-modal">
                <div class="modal-header pastel-modal-header">
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
                                    <div id="modal-prioridad" class="d-inline-block"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Última Actualización</small>
                                    <span id="modal-fecha-update"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 pastel-card">
                        <div class="card-header pastel-card-header py-2">
                            <h6 class="mb-0 fw-semibold">Descripción</h6>
                        </div>
                        <div class="card-body">
                            <p id="modal-detalles"></p>
                        </div>
                    </div>
                    
                    <div class="card pastel-card">
                        <div class="card-header pastel-card-header py-2">
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
            
            /* Efectos especiales para filas según prioridad */
            .excel-row[data-prioridad="Alta"] {
                border-left: 2px solid #FFB3C1;
            }
            
            .excel-row[data-prioridad="Media"] {
                border-left: 2px solid #FFE0B2;
            }
            
            .excel-row[data-prioridad="Baja"] {
                border-left: 2px solid #C5D9FF;
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
                let tables = [];
                let currentSoporteId = null;

                // =================================================================
                // == MANEJO DEL MODAL PARA VER DETALLES RÁPIDOS ==
                // =================================================================
                document.addEventListener('click', function (e) {
                    const soporteIdLink = e.target.closest('.soporte-id');
                    
                    if (soporteIdLink) {
                        e.preventDefault();
                        
                        const el = soporteIdLink;
                        currentSoporteId = el.dataset.id;
                        
                        // Llenar el modal con los datos
                        document.getElementById('modal-id').textContent = `#${el.dataset.id}`;
                        document.getElementById('modal-fecha').textContent = el.dataset.fecha;
                        document.getElementById('modal-creado').textContent = el.dataset.creado;
                        document.getElementById('modal-area').textContent = el.dataset.area ?? 'Soporte';
                        document.getElementById('modal-categoria').textContent = el.dataset.categoria ?? 'N/A';
                        document.getElementById('modal-tipo-subtipo').textContent = 
                            (el.dataset.tipo ?? 'N/A') + ' / ' + (el.dataset.subtipo ?? 'N/A');
                        
                        // Aplicar colores de prioridad pastel SUAVE en el modal
                        document.getElementById('modal-prioridad').innerHTML = createPriorityBadge(el.dataset.prioridad);
                        
                        // Aplicar colores de estado pastel SUAVE en el modal
                        document.getElementById('modal-estado').innerHTML = createEstadoBadge(el.dataset.estado);
                        
                        document.getElementById('modal-detalles').textContent = el.dataset.detalles;
                        document.getElementById('modal-fecha-update').textContent = el.dataset.updated ?? el.dataset.fecha;
                        document.getElementById('modal-maeTercero').textContent = el.dataset.maetercero ?? 'N/A';
                        document.getElementById('modal-escalado').textContent = el.dataset.escalado ?? 'Sin asignar';

                        // Configurar botón de editar
                        document.getElementById('editFromModal').onclick = function() {
                            window.location.href = `/soportes/soportes/${currentSoporteId}/edit`;
                        };

                        new bootstrap.Modal(document.getElementById('detalleSoporteModal')).show();
                    }
                });

                // Función para crear badge de prioridad pastel SUAVE
                function createPriorityBadge(prioridad) {
                    let icono = '';
                    let estilo = '';
                    
                    switch(prioridad) {
                        case 'Alta':
                            icono = 'feather-alert-triangle';
                            estilo = 'background-color: #FFD6E0 !important; color: #D63384 !important; border: 1px solid #FFB3C1 !important;';
                            break;
                        case 'Media':
                            icono = 'feather-alert-circle';
                            estilo = 'background-color: #FFF4E6 !important; color: #FF9800 !important; border: 1px solid #FFE0B2 !important;';
                            break;
                        case 'Baja':
                            icono = 'feather-info';
                            estilo = 'background-color: #E6F3FF !important; color: #5C7CFA !important; border: 1px solid #C5D9FF !important;';
                            break;
                        default:
                            icono = 'feather-help-circle';
                            estilo = 'background-color: #F3E5F5 !important; color: #9C27B0 !important; border: 1px solid #E1BEE7 !important;';
                    }
                    
                    return `<div style="min-width: 50px; text-align: center; transition: all 0.2s ease; position: relative; overflow: hidden; border-radius: 8px; font-weight: 500; font-size: 0.6rem; ${estilo} padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather ${icono}" style="font-size: 8px; margin-right: 2px;"></i>
                        <span>${prioridad}</span>
                    </div>`;
                }
                
                // Función para crear badge de estado pastel SUAVE
                function createEstadoBadge(estado) {
                    let icono = '';
                    let estilo = '';
                    
                    switch(estado) {
                        case 'Pendiente':
                            icono = 'feather-clock';
                            estilo = 'background-color: #FFF8E1 !important; color: #F57C00 !important; border: 1px solid #FFECB3 !important;';
                            break;
                        case 'En Proceso':
                            icono = 'feather-loader';
                            estilo = 'background-color: #E1F5FE !important; color: #0288D1 !important; border: 1px solid #B3E5FC !important;';
                            break;
                        case 'Cerrado':
                            icono = 'feather-check-circle';
                            estilo = 'background-color: #E8F5E8 !important; color: #2E7D32 !important; border: 1px solid #C8E6C9 !important;';
                            break;
                        default:
                            icono = 'feather-help-circle';
                            estilo = 'background-color: #FCE4EC !important; color: #C2185B !important; border: 1px solid #F8BBD0 !important;';
                    }
                    
                    return `<div style="min-width: 60px; text-align: center; transition: all 0.2s ease; border-radius: 8px; font-weight: 500; font-size: 0.6rem; ${estilo} padding: 2px 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="feather ${icono}" style="font-size: 8px; margin-right: 2px;"></i>
                        <span>${estado}</span>
                    </div>`;
                }

                // Inicializar todas las tablas con clase .soporteTable
                $('.soporteTable').each(function () {
                    if (!$.fn.dataTable.isDataTable(this)) {
                        let t = $(this).DataTable({
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
                                paginate: {
                                    first: "Primero",
                                    last: "Último",
                                    next: "Siguiente",
                                    previous: "Anterior"
                                }
                            },
                            initComplete: function() {
                                // Añadir indicadores de color pastel SUAVE al filtro de prioridad
                                $('#filterPrioridad option').each(function() {
                                    const value = $(this).val();
                                    if (value) {
                                        let bgColor = '';
                                        switch(value) {
                                            case 'Alta': 
                                                bgColor = '#FFD6E0'; 
                                                break;
                                            case 'Media': 
                                                bgColor = '#FFF4E6'; 
                                                break;
                                            case 'Baja': 
                                                bgColor = '#E6F3FF'; 
                                                break;
                                        }
                                        if (bgColor) {
                                            $(this).css({
                                                'background': bgColor,
                                                'color': '#2c3e50',
                                                'font-weight': '600'
                                            });
                                        }
                                    }
                                });
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

                // SweetAlert — confirmaciones
                function confirmarAccion(titulo, texto, icono, callback) {
                    Swal.fire({
                        title: titulo,
                        text: texto,
                        icon: icono,
                        showCancelButton: true,
                        confirmButtonColor: '#E6F3FF',
                        cancelButtonColor: '#F8E8FF',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                        background: 'rgba(255, 255, 255, 0.9)',
                        backdrop: 'rgba(0, 0, 0, 0.1)'
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
                
                // Efecto de entrada para las filas
                $('.excel-row').each(function(index) {
                    $(this).css({
                        'opacity': '0',
                        'transform': 'translateY(5px)'
                    });
                    setTimeout(() => {
                        $(this).animate({
                            'opacity': '1',
                            'transform': 'translateY(0)'
                        }, 200);
                    }, index * 20);
                });
                
                // Efecto de entrada para las cards
                $('.glassmorphism-card').each(function(index) {
                    $(this).addClass('fade-in-up');
                    $(this).css('animation-delay', `${index * 0.05}s`);
                });
            });
        </script>
    @endpush
</x-base-layout>