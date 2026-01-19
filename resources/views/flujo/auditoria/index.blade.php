<x-base-layout>
    {{-- 1. Librerías Externas --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <div class="app-container" id="audit-container">
        
        {{-- Header Ejecutivo --}}
        <header class="main-header">
            <div class="header-info">
                <div class="header-pretitle">Governance & Compliance</div>
                <h1 class="text-title">Auditoría Global</h1>
                <p class="subtitle">Centro de control y trazabilidad de operaciones</p>
            </div>
            
            {{-- Botones de Acción (Estilo Píldora Flotante) --}}
            <div class="header-actions-group">
                {{-- Botón PDF --}}
                <a href="{{ route('flujo.auditoria.pdf', request()->query()) }}" target="_blank" class="action-btn btn-pdf" title="Descargar reporte filtrado">
                    <span class="icon-wrapper"><i class="fas fa-file-pdf"></i></span>
                    <span class="text">Exportar Informe</span>
                </a>

                {{-- Separador Vertical --}}
                <div class="v-separator"></div>

                {{-- Botón Volver --}}
                <a href="{{ route('flujo.workflows.index') }}" class="action-btn btn-back" title="Volver al listado">
                    <i class="fas fa-chevron-left"></i>
                    <span class="text">Proyectos</span>
                </a>
            </div>
        </header>

        {{-- Dashboard de Métricas (Filtros Rápidos) --}}
        <div class="metrics-grid-wrapper">
            <div class="metrics-grid">
                {{-- Total --}}
                <div class="metric-pill total filter-pill {{ !request('tipo_evento') ? 'active' : '' }}" onclick="resetTypeFilter()">
                    <span class="m-label">Actividad Total</span>
                    <span class="m-value">{{ $stats['total'] }}</span>
                </div>
                
                {{-- Comentarios --}}
                <div class="metric-pill comentario filter-pill {{ request('tipo_evento') == 'comentario' ? 'active' : '' }}" onclick="setTypeFilter('comentario')">
                    <span class="m-dot dot-comentario"></span>
                    <span class="m-label">Feedback & Notas</span>
                    <span class="m-value">{{ $stats['comments'] }}</span>
                </div>

                {{-- Historial --}}
                <div class="metric-pill historial filter-pill {{ request('tipo_evento') == 'historial' ? 'active' : '' }}" onclick="setTypeFilter('historial')">
                    <span class="m-dot dot-historial"></span>
                    <span class="m-label">Cambios de Estado</span>
                    <span class="m-value">{{ $stats['history'] }}</span>
                </div>
            </div>
        </div>

        {{-- SECCIÓN DE INSIGHTS (Gráficas Interactivas) --}}
        <div class="insights-section">
            <div class="insights-header">
                <h2>Análisis de Operaciones</h2>
                <span class="insight-badge">Interactúa con las gráficas para filtrar</span>
            </div>
            
            {{-- Gráfica 1: Distribución de Eventos --}}
            <div class="insight-card chart-container-wrapper">
                <div class="card-head">
                    <h3>Distribución</h3>
                    <p class="chart-subtitle">Tipo de Actividad</p>
                    <div class="chart-legend-custom" id="distribution-legend"></div>
                </div>
                <div class="chart-container">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
            
            {{-- Gráfica 2: Top Usuarios --}}
            <div class="insight-card chart-container-wrapper" style="flex: 2;">
                <div class="card-head">
                    <h3>Usuarios Más Activos</h3>
                    <p class="chart-subtitle">Volumen de operaciones (Click en barra para filtrar)</p>
                </div>
                <div class="chart-container">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Toolbar con Buscador y Filtros Avanzados --}}
        <div class="toolbar-area">
            <div class="search-and-filter-wrapper">
                <form action="{{ route('flujo.auditoria.index') }}" method="GET" class="neo-search-form" id="main-search-form">
                    {{-- Inputs Ocultos controlados por JS/Gráficas --}}
                    <input type="hidden" name="tipo_evento" id="hidden-type" value="{{ request('tipo_evento') }}">
                    <input type="hidden" name="user_id" id="hidden-user" value="{{ request('user_id') }}">
                    <input type="hidden" name="workflow_id" id="hidden-workflow" value="{{ request('workflow_id') }}">
                    
                    <div class="search-inner">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="live-search" placeholder="Buscar en comentarios, tareas o estados..." value="{{ request('search') }}" autocomplete="off" />
                            @if(request()->filled('search'))
                                <button type="button" class="search-clear-btn" id="clear-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        <div class="v-divider"></div>
                        <button type="button" class="btn-filter-toggle" id="advance-filter-trigger">
                            <i class="fas fa-sliders-h"></i>
                            <span class="d-none-mobile">Filtros</span>
                            <span class="filter-badge" id="active-filters-count">0</span>
                        </button>
                    </div>
                </form>

                {{-- Popover de Filtros Avanzados --}}
                <div class="advanced-filter-popover" id="advanced-popover">
                    <div class="popover-header">
                        <div class="popover-title">Filtrado Avanzado</div>
                        <button type="button" class="popover-close" id="close-popover">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="accordion-container">
                        
                        {{-- Filtro: Usuario --}}
                        <div class="acc-item active">
                            <div class="acc-header"><span>Usuario</span><i class="fas fa-chevron-down"></i></div>
                            <div class="acc-content" style="overflow: visible;">
                                <select class="filter-select-neo" id="user-opt" placeholder="Buscar usuario...">
                                    <option value="">Todos los usuarios</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Filtro: Proyecto --}}
                        <div class="acc-item active">
                            <div class="acc-header"><span>Proyecto</span><i class="fas fa-chevron-down"></i></div>
                            <div class="acc-content" style="overflow: visible;">
                                <select class="filter-select-neo" id="workflow-opt" placeholder="Buscar proyecto...">
                                    <option value="">Todos los proyectos</option>
                                    @foreach($workflows as $wf)
                                        <option value="{{ $wf->id }}" {{ request('workflow_id') == $wf->id ? 'selected' : '' }}>{{ $wf->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="popover-footer">
                        <button type="button" class="btn-reset-filters" id="reset-filters">
                            <i class="fas fa-undo"></i> Restablecer
                        </button>
                        <button type="button" class="btn-apply-filters" id="apply-advanced">
                            <i class="fas fa-check"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>

                {{-- Indicador de búsqueda activa --}}
                @if(request()->anyFilled(['search', 'user_id', 'workflow_id', 'tipo_evento']))
                    <div class="search-indicator">
                        <div class="search-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Filtros activos</span>
                        </div>
                        <a href="{{ route('flujo.auditoria.index') }}" class="clear-all-filters">
                            <i class="fas fa-times"></i> Limpiar todo
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Timeline Minimalista (Lista de Resultados) --}}
        <div class="timeline-clean">
            @forelse($events as $index => $event)
                <div class="t-row {{ $event->tipo_evento == 'historial' ? 'type-historial' : 'type-comentario' }}" style="animation-delay: {{ $index * 0.05 }}s">
                    <div class="t-dot"></div>
                    
                    <div class="t-card-neo">
                        <div class="t-head">
                            <div class="user-info">
                                <div class="avatar-circle">
                                    {{ substr($event->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="meta-text">
                                    <span class="u-name">{{ $event->user->name ?? 'Sistema' }}</span>
                                    
                                    {{-- CONTEXTO: PROYECTO > TAREA --}}
                                    <div class="u-context">
                                        @if($event->tipo_evento === 'comentario') comentó @else actualizó @endif en:
                                        
                                        <a href="{{ route('flujo.workflows.show', $event->task->workflow_id ?? 0) }}" class="project-badge">
                                            <i class="fas fa-folder-open" style="font-size: 0.7rem;"></i>
                                            {{ Str::limit($event->task->workflow->nombre ?? 'Sin Proyecto', 20) }}
                                        </a>
                                        
                                        <i class="fas fa-chevron-right" style="font-size: 0.6rem; color:#cbd5e1;"></i>
                                        
                                        <span class="task-link">
                                            {{ Str::limit($event->task->titulo ?? 'Tarea eliminada', 30) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span class="time-badge">
                                {{ $event->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Contenido --}}
                        @if($event->tipo_evento === 'comentario')
                            <div class="content-comment">
                                {!! nl2br(e($event->comentario)) !!}
                                
                                {{-- LÓGICA DE SOPORTE INTEGRADA --}}
                                @if($event->soporte)
                                    <div style="margin-top: 15px;">
                                        {{-- 1. Si es Imagen --}}
                                        @if($event->es_imagen)
                                            <a href="{{ $event->soporte_url }}" target="_blank" class="media-preview" title="Clic para ampliar imagen">
                                                <img src="{{ $event->soporte_url }}" alt="Soporte visual">
                                                <div class="media-overlay">
                                                    <i class="fas fa-search-plus"></i> Ver Imagen
                                                </div>
                                            </a>
                                        
                                        {{-- 2. Si es Documento --}}
                                        @else
                                            <a href="{{ $event->soporte_url }}" target="_blank" class="attach-chip">
                                                <i class="fas fa-paperclip"></i> 
                                                <span>Ver Archivo Adjunto</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        @elseif($event->tipo_evento === 'historial')
                            <div class="status-flow">
                                <span class="pill-status ps-old">{{ str_replace('_', ' ', $event->estado_anterior) }}</span>
                                <i class="fas fa-arrow-right" style="color:#d1d5db; font-size:0.8rem;"></i>
                                <span class="pill-status ps-new">{{ str_replace('_', ' ', $event->estado_nuevo) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state-modern">
                    <i class="far fa-folder-open empty-icon"></i>
                    <h3 style="margin:0; font-size:1.1rem; color:#1e293b;">Sin registros encontrados</h3>
                    <p style="color:#94a3b8; font-size:0.9rem;">Intenta ajustar los filtros de búsqueda.</p>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" style="margin-top: 40px;">
            {{ $events->links() }}
        </div>
    </div>

    {{-- ESTILOS CSS --}}
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --c-accent: #6366f1;
            --radius-md: 16px;
            --radius-sm: 8px;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        body { background-color: var(--bg-body); color: var(--text-main); font-family: 'Plus Jakarta Sans', sans-serif; }
        .app-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        /* --- Header --- */
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .text-title { font-family: 'Outfit'; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; margin: 0; color: #1e293b; }
        .header-pretitle { font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em; }
        .subtitle { color: var(--text-muted); font-size: 0.95rem; margin-top: 5px; }

        /* --- Actions Group (New!) --- */
        .header-actions-group {
            display: flex; align-items: center; background: white; padding: 6px; border-radius: 14px;
            border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03); gap: 4px;
        }
        .action-btn {
            display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 10px;
            text-decoration: none; font-size: 0.85rem; font-weight: 600; color: #475569;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        /* Botón PDF */
        .btn-pdf .icon-wrapper {
            display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;
            background-color: #fef2f2; border-radius: 6px; color: #ef4444; font-size: 0.8rem; transition: 0.2s;
        }
        .btn-pdf:hover { background-color: #f8fafc; color: #0f172a; }
        .btn-pdf:hover .icon-wrapper { background-color: #ef4444; color: white; transform: scale(1.1); }
        /* Botón Back */
        .btn-back:hover { background-color: #f1f5f9; color: #0f172a; transform: translateX(-2px); }
        /* Separador */
        .v-separator { width: 1px; height: 20px; background-color: #e2e8f0; margin: 0 4px; }

        /* --- Métricas (Pills) --- */
        .metrics-grid-wrapper { margin-bottom: 30px; overflow-x: auto; }
        .metrics-grid { display: flex; gap: 15px; }
        .metric-pill { 
            background: white; padding: 15px 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color);
            cursor: pointer; transition: 0.2s; display: flex; flex-direction: column; gap: 5px; min-width: 140px;
            box-shadow: var(--shadow-subtle);
        }
        .metric-pill:hover { transform: translateY(-2px); border-color: var(--primary); }
        .metric-pill.active { background: var(--primary); border-color: var(--primary); color: white; }
        .metric-pill.active .m-label, .metric-pill.active .m-value { color: white; }
        .m-label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
        .m-value { font-family: 'Outfit'; font-size: 1.5rem; font-weight: 700; }
        .m-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-bottom: 5px; }
        .dot-comentario { background: #3b82f6; } 
        .dot-historial { background: #f59e0b; }

        /* --- Charts Section --- */
        .insights-section { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 40px; }
        .insights-header { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .insights-header h2 { font-family: 'Outfit'; font-size: 1.3rem; font-weight: 700; margin: 0; }
        .insight-badge { font-size: 0.8rem; background: #e0e7ff; color: var(--primary); padding: 4px 10px; border-radius: 20px; font-weight: 600; }
        
        .insight-card { 
            background: white; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; 
            flex: 1; min-width: 300px; box-shadow: var(--shadow-subtle); position: relative;
        }
        .chart-container { height: 200px; width: 100%; position: relative; }
        .card-head h3 { margin: 0 0 5px 0; font-size: 1rem; font-weight: 700; }
        .chart-subtitle { margin: 0 0 15px 0; font-size: 0.8rem; color: var(--text-muted); }

        /* --- Toolbar & Filters --- */
        .toolbar-area { position: sticky; top: 10px; z-index: 90; margin-bottom: 40px; }
        .search-and-filter-wrapper { max-width: 600px; margin: 0 auto; }
        .search-inner { 
            display: flex; align-items: center; background: rgba(255,255,255,0.9); 
            backdrop-filter: blur(10px); border-radius: 50px; 
            border: 1px solid var(--border-color); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); 
            height: 55px; padding: 0 5px;
        }
        .search-input-group { flex: 1; display: flex; align-items: center; padding-left: 20px; }
        .search-input-group i { color: #94a3b8; margin-right: 10px; }
        .search-input-group input { border: none; background: transparent; width: 100%; outline: none; font-size: 0.95rem; color: var(--text-main); }
        .btn-filter-toggle { 
            background: var(--primary); color: white; border: none; height: 40px; padding: 0 20px; 
            border-radius: 40px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 0.9rem;
            transition: 0.2s; margin-right: 5px;
        }
        .btn-filter-toggle:hover { background: #4338ca; }
        .filter-badge { background: white; color: var(--primary); font-size: 0.7rem; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        /* --- Timeline Minimalista --- */
        .timeline-clean { position: relative; padding-left: 30px; }
        .timeline-clean::before { content: ''; position: absolute; left: 0; top: 15px; bottom: 0; width: 2px; background: #e2e8f0; border-radius: 2px; }
        
        .t-row { position: relative; padding-bottom: 40px; opacity: 0; animation: fadeIn 0.5s ease forwards; }
        .t-dot { 
            position: absolute; left: -34px; top: 24px; width: 10px; height: 10px; 
            background: white; border: 2px solid var(--c-accent); border-radius: 50%; z-index: 2; box-shadow: 0 0 0 4px #f8fafc; 
        }
        .type-historial .t-dot { border-color: #f59e0b; }

        .t-card-neo { 
            background: white; border-radius: 16px; border: 1px solid rgba(226, 232, 240, 0.7); 
            padding: 24px; transition: 0.3s; position: relative;
        }
        .t-card-neo:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); border-color: rgba(99, 102, 241, 0.3); }

        .t-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .user-info { display: flex; gap: 12px; }
        .avatar-circle { 
            width: 40px; height: 40px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); 
            color: var(--text-main); border-radius: 12px; display: flex; align-items: center; justify-content: center; 
            font-weight: 700; font-size: 0.9rem; border: 1px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }
        .meta-text { display: flex; flex-direction: column; }
        .u-name { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }
        .u-context { font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 3px; }
        .project-badge { 
            display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; color: #475569; 
            padding: 2px 8px; border-radius: 6px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.2s;
        }
        .project-badge:hover { border-color: var(--primary); color: var(--primary); }
        .task-link { font-weight: 600; color: var(--text-main); }
        .time-badge { font-size: 0.75rem; color: #94a3b8; font-weight: 600; background: #f8fafc; padding: 4px 10px; border-radius: 20px; }

        /* Contenido */
        .content-comment { 
            position: relative; background: #fafafa; padding: 15px 20px; border-radius: 12px; 
            color: #334155; line-height: 1.6; font-size: 0.95rem; margin-left: 52px; border-left: 3px solid var(--c-accent);
        }
        
        /* ESTILOS NUEVOS PARA SOPORTES */
        .attach-chip { 
            display: inline-flex; align-items: center; gap: 8px; 
            background: white; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px;
            color: var(--c-accent); font-size: 0.85rem; text-decoration: none; font-weight: 600; 
            transition: all 0.2s;
        }
        .attach-chip:hover { border-color: var(--c-accent); background: #eef2ff; transform: translateY(-1px); }
        
        .media-preview {
            display: block; max-width: 250px; border-radius: 10px; overflow: hidden; position: relative;
            border: 1px solid #e2e8f0;
        }
        .media-preview img { width: 100%; height: auto; display: block; transition: transform 0.3s; }
        .media-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s; color: white; font-weight: 600; gap: 5px;
        }
        .media-preview:hover .media-overlay { opacity: 1; }
        .media-preview:hover img { transform: scale(1.05); }

        .status-flow { 
            display: flex; align-items: center; gap: 12px; background: #fffbeb; 
            border: 1px dashed #fcd34d; padding: 10px 16px; border-radius: 10px; width: fit-content; margin-left: 52px; 
        }
        .pill-status { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; }
        .ps-old { color: #94a3b8; text-decoration: line-through; }
        .ps-new { color: #b45309; }

        /* Popover Styles */
        .advanced-filter-popover { 
            position: absolute; top: calc(100% + 15px); left: 0; width: 100%; background: white; 
            border-radius: var(--radius-md); box-shadow: var(--shadow-hover); z-index: 1000; border: 1px solid var(--border-color); 
            display: none; animation: fadeIn 0.2s;
        }
        .advanced-filter-popover.show { display: block; }
        .popover-header { padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-weight: 700; }
        .popover-close { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.1rem; }
        .accordion-container { padding: 10px; }
        .acc-item { margin-bottom: 10px; }
        .acc-header { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; display: block; }
        .popover-footer { padding: 15px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; gap: 10px; }
        .btn-reset-filters { background: white; border: 1px solid #e2e8f0; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #64748b; }
        .btn-apply-filters { background: var(--primary); border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: white; flex: 1; }

        /* Chart Custom Legend */
        .chart-legend-custom { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .legend-item { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
        .legend-color { width: 10px; height: 10px; border-radius: 2px; }

        .search-indicator { background: #eff6ff; padding: 10px 15px; margin-top: 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #1e40af; }
        .clear-all-filters { color: #1e40af; font-weight: 700; text-decoration: none; }

        /* Tom Select Fixes */
        .ts-control { border-radius: 8px !important; border-color: #e2e8f0 !important; padding: 10px !important; font-size: 0.9rem; }
        .ts-dropdown { z-index: 1100 !important; border-radius: 8px; box-shadow: var(--shadow-hover); }

        @keyframes fadeIn { to { opacity: 1; } }
        @media (max-width: 768px) {
            .t-head { flex-direction: column; gap: 10px; }
            .content-comment, .status-flow { margin-left: 0; }
            .insights-section { flex-direction: column; }
            .header-actions-group { width: 100%; justify-content: space-between; margin-top: 10px; }
            .action-btn .text { display: none; } /* En móvil ocultar texto de botones */
            .btn-pdf .text { display: block; } /* Mostrar texto solo para PDF */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        // Funciones Globales para los filtros de las métricas
        function setTypeFilter(type) {
            document.getElementById('hidden-type').value = type;
            document.getElementById('main-search-form').submit();
        }
        function resetTypeFilter() {
            document.getElementById('hidden-type').value = '';
            document.getElementById('main-search-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. Inicializar Selects ---
            new TomSelect('#user-opt', { create: false, plugins: ['dropdown_input'], dropdownParent: 'body' });
            new TomSelect('#workflow-opt', { create: false, plugins: ['dropdown_input'], dropdownParent: 'body' });

            // --- 2. Datos de Gráficas (Desde PHP) ---
            const chartData = {
                type: {
                    labels: ['Comentarios', 'Historial'],
                    keys: ['comentario', 'historial'],
                    data: [{{ $stats['comments'] }}, {{ $stats['history'] }}],
                    colors: ['#3b82f6', '#f59e0b']
                },
                users: {
                    labels: {!! json_encode($stats['usersData']['labels']) !!},
                    data: {!! json_encode($stats['usersData']['data']) !!},
                    ids: {!! json_encode($stats['usersData']['ids']) !!}
                }
            };

            // --- 3. Configuración de Gráficas ---
            
            // A) Donut Chart: Distribución por Tipo
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            
            // Leyenda HTML personalizada
            const legendContainer = document.getElementById('distribution-legend');
            chartData.type.labels.forEach((label, i) => {
                legendContainer.innerHTML += `
                    <div class="legend-item">
                        <div class="legend-color" style="background: ${chartData.type.colors[i]}"></div>
                        <span>${label}: ${chartData.type.data[i]}</span>
                    </div>`;
            });

            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.type.labels,
                    datasets: [{
                        data: chartData.type.data,
                        backgroundColor: chartData.type.colors,
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    onHover: (e, el) => e.native.target.style.cursor = el[0] ? 'pointer' : 'default',
                    onClick: (e, el) => {
                        if(el.length > 0) {
                            const key = chartData.type.keys[el[0].index];
                            setTypeFilter(key);
                        }
                    }
                }
            });

            // B) Bar Chart: Top Usuarios
            const userCtx = document.getElementById('userChart').getContext('2d');
            new Chart(userCtx, {
                type: 'bar',
                data: {
                    labels: chartData.users.labels,
                    datasets: [{
                        data: chartData.users.data,
                        backgroundColor: '#4f46e5',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false }, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    },
                    onHover: (e, el) => e.native.target.style.cursor = el[0] ? 'pointer' : 'default',
                    onClick: (e, el) => {
                        if(el.length > 0) {
                            const userId = chartData.users.ids[el[0].index];
                            document.getElementById('hidden-user').value = userId;
                            document.getElementById('main-search-form').submit();
                        }
                    }
                }
            });

            // --- 4. Lógica de UI del Popover ---
            const trigger = document.getElementById('advance-filter-trigger');
            const popover = document.getElementById('advanced-popover');
            const closeBtn = document.getElementById('close-popover');
            const resetBtn = document.getElementById('reset-filters');
            const applyBtn = document.getElementById('apply-advanced');
            const form = document.getElementById('main-search-form');

            // Toggle Popover
            trigger.addEventListener('click', (e) => { e.stopPropagation(); popover.classList.toggle('show'); });
            closeBtn.addEventListener('click', (e) => { e.stopPropagation(); popover.classList.remove('show'); });
            
            // Cerrar al hacer click fuera
            document.addEventListener('click', (e) => {
                if (!popover.contains(e.target) && e.target !== trigger) {
                    if (!e.target.closest('.ts-dropdown') && !e.target.closest('.ts-wrapper')) {
                        popover.classList.remove('show');
                    }
                }
            });

            // Aplicar Filtros Avanzados
            applyBtn.addEventListener('click', () => {
                document.getElementById('hidden-user').value = document.getElementById('user-opt').value;
                document.getElementById('hidden-workflow').value = document.getElementById('workflow-opt').value;
                form.submit();
            });

            // Restablecer Filtros
            resetBtn.addEventListener('click', () => {
                document.getElementById('hidden-user').value = '';
                document.getElementById('hidden-workflow').value = '';
                document.getElementById('hidden-type').value = '';
                document.getElementById('live-search').value = '';
                form.submit();
            });

            // Limpiar buscador simple
            const clearSearch = document.getElementById('clear-search');
            if(clearSearch) {
                clearSearch.addEventListener('click', () => {
                    document.getElementById('live-search').value = '';
                    form.submit();
                });
            }
        });
    </script>
</x-base-layout>