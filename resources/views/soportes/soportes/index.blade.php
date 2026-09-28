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
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Área</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-layers"></i></span>
                        <select id="filterArea" class="form-select pastel-select">
                            <option value="">Todas</option>
                            @foreach($opcionesArea as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Prioridad</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-flag"></i></span>
                        <select id="filterPrioridad" class="form-select pastel-select">
                            <option value="">Todas</option>
                            @foreach($opcionesPrioridad as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
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
                            @foreach($opcionesUsuario as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Asignados a</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-user-check"></i></span>
                        <select id="filterAsignado" class="form-select pastel-select">
                            <option value="">Todos</option>
                            @foreach($opcionesAsignado as $asignado)
                                <option value="{{ $asignado }}">{{ $asignado }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
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
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3 gap-2">
                <div>
                    <h5 class="fw-bold mb-1">{{ ($esVistaPersonal ?? false) ? 'Mis Soportes' : 'Listado de Soportes' }}</h5>
                    <p class="text-muted mb-0 small">
                        {{ ($esVistaPersonal ?? false) ? 'Soportes creados por ti o asignados a ti' : 'Gestiona y monitorea todos los soportes del sistema' }}
                    </p>
                </div>
                <div class="w-100" style="max-width: 260px;">
                    <select id="buscadorRapidoSoporte" class="form-select form-select-sm w-100"></select>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill flex-lg-grow-0 text-nowrap" id="exportBtn">
                        <i class="feather-download me-1"></i>Exportar
                    </button>
                    <a href="{{ route('soportes.soportes.create') }}" class="btn btn-success pastel-btn-gradient btnCrear flex-fill flex-lg-grow-0 text-nowrap">
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
                            {{ $totalTodos }}
                        </span>
                    </button>
                </li>
                @endcandirect
                @foreach ($categorias as $nombreCategoria => $totalCategoria)
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
                        <button class="nav-link pastel-tab @if($nombreCategoria === $categoriaActivaPorDefecto)active @endif"
                                id="tab-{{ $indice }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-{{ $indice }}"
                                type="button"
                                role="tab"
                                aria-controls="tab-{{ $indice }}"
                                aria-selected="{{ $nombreCategoria === $categoriaActivaPorDefecto ? 'true' : 'false' }}">
                            <i class="{{ $icono }} me-2"></i>
                            {{ $nombreCategoria }}
                            <span class="badge pastel-badge-globito ms-1" style="background-color: {{ $colorPastel }} !important; color: {{ $colorTexto }} !important;">
                                {{ $totalCategoria }}
                            </span>
                        </button>
                    </li>
                    @endcandirect
                @endforeach
            </ul>

            {{-- Contenido — cada tabla se llena por AJAX (server-side), no con filas ya
                 renderizadas: se inicializa solo cuando su pestaña se muestra por primera vez
                 (ver script más abajo), tanto por rendimiento como porque DataTables calcula mal
                 el ancho de una tabla si se inicializa oculta dentro de una pestaña de Bootstrap. --}}
            <div class="tab-content" id="soporteTabsContent">
                {{-- TAB TODOS --}}
                <div class="tab-pane fade" id="tab-1" role="tabpanel" aria-labelledby="tab-1-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle soporteTable pastel-table excel-table" id="tablaPrincipal" data-tab="">
                            @include('soportes.soportes._tabla_thead')
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                {{-- fin tab Todos --}}
                @foreach ($categorias as $nombreCategoria => $totalCategoria)
                    @php
                        $indice = $loop->index + 2;
                        $permiso = 'soporte.lista.' . strtolower($nombreCategoria);
                    @endphp
                    @candirect($permiso)
                    <div class="tab-pane fade @if($nombreCategoria === $categoriaActivaPorDefecto)show active @endif"
                         id="tab-{{ $indice }}"
                         role="tabpanel"
                         aria-labelledby="tab-{{ $indice }}-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle soporteTable pastel-table excel-table" data-tab="{{ $nombreCategoria }}">
                                @include('soportes.soportes._tabla_thead')
                                <tbody></tbody>
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
                                    <small class="text-muted d-block">Área del Creador</small>
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
                // == BUSCADOR RÁPIDO (Select2 AJAX) — va directo al ticket elegido ==
                // =================================================================
                $('#buscadorRapidoSoporte').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Buscar por # o texto...',
                    allowClear: true,
                    minimumInputLength: 1,
                    ajax: {
                        url: @json(route('soportes.buscar.rapido')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => data,
                    }
                }).on('select2:select', function (e) {
                    const id = e.params.data.id;
                    if (id) {
                        window.location.href = @json(route('soportes.soportes.show', ':id')).replace(':id', id);
                    }
                });

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

                // =================================================================
                // == TABLAS SERVER-SIDE (una por pestaña) ==
                // =================================================================
                // Antes: TODAS las filas de TODAS las pestañas viajaban en el HTML inicial y
                // DataTables paginaba/filtraba en el navegador (con 202 registros ya eran 222
                // consultas y toda esa HTML de más, especialmente pesado en celular). Ahora cada
                // tabla pide su propia página al servidor, y encima cada tabla se inicializa
                // SOLO cuando su pestaña se muestra por primera vez — DataTables calcula mal el
                // ancho de una tabla inicializada mientras su pestaña está oculta.
                const urlListado = window.location.pathname; // misma ruta que index() / misSoportes(), ambas soportan AJAX

                // =================================================================
                // == EXPORTAR TODO LO FILTRADO (no solo la página visible) ==
                // =================================================================
                // Patrón oficial de DataTables para "exportar todo" con serverSide=true: pide
                // TODA la data filtrada (length=-1, el backend lo entiende como "sin límite",
                // ver ScpSoporteController), dispara la exportación real sobre eso, y vuelve a la
                // paginación normal — sin esto, Excel/PDF/Imprimir solo sacarían la página
                // actual (ej. 20 filas) en vez de todo lo que el filtro está mostrando.
                function exportarTodoFiltrado(nombreExtend) {
                    return function (e, dt, button, config) {
                        const self = this;
                        dt.one('preXhr', function () {
                            dt.page.len(-1);
                        });
                        dt.one('draw', function () {
                            $.fn.dataTable.ext.buttons[nombreExtend].action.call(self, e, dt, button, config);
                            dt.one('preXhr', function () {
                                setTimeout(function () {
                                    dt.page.len(20);
                                    dt.draw();
                                }, 0);
                            });
                        });
                        dt.draw();
                    };
                }

                function initTablaSoporte($tabla) {
                    if (!$tabla.length || $.fn.dataTable.isDataTable($tabla[0])) {
                        return $tabla.length ? $tabla.DataTable() : null;
                    }

                    const tab = $tabla.data('tab') || '';
                    let t;

                    try {
                        t = $tabla.DataTable({
                        serverSide: true,
                        processing: true,
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excelHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file-text me-1"></i>Excel', action: exportarTodoFiltrado('excelHtml5') },
                            { extend: 'pdfHtml5', className: 'btn btn-sm pastel-btn-gradient', text: '<i class="feather-file me-1"></i>PDF', action: exportarTodoFiltrado('pdfHtml5') },
                            { extend: 'print', className: 'btn btn-sm pastel-btn-light', text: '<i class="feather-printer me-1"></i>Imprimir', action: exportarTodoFiltrado('print') }
                        ],
                        pageLength: 20,
                        // Ninguna columna es "orderable" (son HTML ya armado en el servidor, no
                        // texto plano ordenable) y el backend siempre ordena por fecha de
                        // creación descendente sin importar lo que mande el cliente — por eso se
                        // desactiva el ordenamiento de la UI en vez de declarar un order[] que
                        // apunte a una columna no ordenable (eso rompía la inicialización de la
                        // tabla por completo: se veían los encabezados pero nunca cargaba ni una
                        // fila, ni al hacer clic en ninguna pestaña).
                        ordering: false,
                        ajax: {
                            url: urlListado,
                            data: function (d) {
                                d.tab = tab;
                                d.area = $('#filterArea').val();
                                d.prioridad = $('#filterPrioridad').val();
                                d.usuario = $('#filterUsuario').val();
                                d.asignado = $('#filterAsignado').val();
                                d.fecha = $('#filterFecha').val();
                            },
                            error: function (xhr, status, error) {
                                console.error('Error cargando soportes:', status, error, xhr.responseText);
                            }
                        },
                        columns: [
                            { data: 'id_col' },
                            { data: 'fecha_col' },
                            { data: 'creado_col' },
                            { data: 'area_col' },
                            { data: 'categoria_col' },
                            { data: 'tipo_col' },
                            { data: 'prioridad_col' },
                            { data: 'descripcion_col' },
                            { data: 'asignado_col' },
                            { data: 'estado_col' },
                            { data: 'acciones_col' },
                        ],
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
                        drawCallback: function () {
                            updateResultCount();
                            // Reactivar tooltips en las filas que acaban de llegar por AJAX
                            [].slice.call(this.api().table().container().querySelectorAll('[data-bs-toggle="tooltip"]'))
                                .forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover focus', delay: { show: 300, hide: 100 } }));
                        }
                        });
                    } catch (err) {
                        console.error('No se pudo inicializar la tabla de soportes (tab="' + tab + '"):', err);
                        return null;
                    }

                    tables.push(t);
                    return t;
                }

                // Ojo con las exportaciones (Excel/PDF/Imprimir): con serverSide=true, DataTables
                // exporta por defecto lo que hay en la página ACTUAL visible (ej. 20 filas), no
                // todo el listado filtrado — es el comportamiento esperado de DataTables en modo
                // servidor, distinto a como se comportaba antes (todo ya estaba en el DOM).

                // Inicializar solo la tabla de la pestaña que arranca visible.
                initTablaSoporte($('.tab-pane.show.active .soporteTable').first());

                // Inicializar cada tabla la primera vez que su pestaña se muestra.
                document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tabBtn => {
                    tabBtn.addEventListener('shown.bs.tab', function () {
                        const destino = this.getAttribute('data-bs-target');
                        initTablaSoporte($(destino + ' .soporteTable'));
                        setTimeout(updateResultCount, 100);
                    });
                });

                // Colores pastel en las opciones del filtro de prioridad (no depende de datos, se
                // hace una sola vez al cargar).
                $('#filterPrioridad option').each(function () {
                    const value = $(this).val();
                    let bgColor = { 'Alta': '#FFD6E0', 'Media': '#FFF4E6', 'Baja': '#E6F3FF' }[value];
                    if (bgColor) {
                        $(this).css({ 'background': bgColor, 'color': '#2c3e50', 'font-weight': '600' });
                    }
                });

                // Actualizar contador de resultados (de la pestaña visible)
                function updateResultCount() {
                    const $activa = $('.tab-pane.active .soporteTable, .tab-pane.show .soporteTable').first();
                    if (!$activa.length || !$.fn.dataTable.isDataTable($activa[0])) return;
                    const info = $activa.DataTable().page.info();
                    document.getElementById('resultCount').textContent =
                        `Mostrando ${info.recordsDisplay ? (info.end - info.start) : 0} de ${info.recordsTotal} resultados` +
                        (info.recordsTotal !== info.recordsDisplay ? ` (${info.recordsDisplay} filtrados)` : '');
                }

                // Recargar TODAS las tablas ya inicializadas (las que aún no se han abierto se
                // inicializan solas cuando se muestren, y ya toman el filtro actual desde el ajax.data).
                function recargarTablas() {
                    tables.forEach(t => t.ajax.reload(null, false));
                    setTimeout(updateResultCount, 200);
                }

                $('#filterArea, #filterPrioridad, #filterUsuario, #filterAsignado, #filterFecha').on('change', recargarTablas);

                // Limpiar filtros
                $('#clearFilters').on('click', function() {
                    $('#filterArea, #filterPrioridad, #filterUsuario, #filterAsignado, #filterFecha').val('');
                    recargarTablas();
                });

                // Cambiar vista (tabla/tarjetas)
                $('[data-view]').on('click', function() {
                    $('[data-view]').removeClass('active');
                    $(this).addClass('active');
                    // Aquí iría la lógica para cambiar entre vista de tabla y tarjetas
                });

                // Botón exportar
                $('#exportBtn').on('click', function() {
                    const $activa = $('.tab-pane.active .soporteTable, .tab-pane.show .soporteTable').first();
                    if ($activa.length && $.fn.dataTable.isDataTable($activa[0])) {
                        $activa.DataTable().button(0).trigger();
                    }
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

                // Inicializar contador
                updateResultCount();

                // Efecto de entrada para las cards
                $('.glassmorphism-card').each(function(index) {
                    $(this).addClass('fade-in-up');
                    $(this).css('animation-delay', `${index * 0.05}s`);
                });
            });
        </script>
    @endpush
</x-base-layout>