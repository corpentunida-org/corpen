<x-base-layout>
    <div class="task-index-wrapper">
        {{-- Encabezado Ejecutivo --}}
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Operaciones Administrativas</span>
                <h1 class="main-title">Control de Unidades de Trabajo</h1>
                <p class="main-subtitle">Gestión centralizada de asignaciones, plazos y estados operativos.</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('flujo.tablero') }}" class="btn-ghost-corporate">
                    <i class="fas fa-th-large"></i> <span class="d-none-mobile">Tablero</span>
                </a>
                <a href="{{ route('flujo.tasks.create') }}" class="btn-corporate-black">
                    <i class="fas fa-plus"></i> <span class="d-none-mobile">Nueva Tarea</span>
                </a>
            </div>
        </header>

        {{-- Toolbar de Filtrado Avanzado --}}
        <div class="index-toolbar">
            <form action="{{ route('flujo.tasks.index') }}" method="GET" id="taskFilterForm" class="filters-container-corp">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="liveSearchTasks" placeholder="Buscar por título o descripción..." value="{{ request('search') }}" autocomplete="off">
                </div>

                <div class="select-group-corp scroll-x-mobile">
                    {{-- Estilo mejorado para selectores --}}
                    <div class="custom-select-wrapper">
                        <select name="workflow_id" class="auto-filter-task filter-workflow-highlight">
                            <option value="">Cualquier Workflow</option>
                            @foreach($workflows as $workflow)
                                <option value="{{ $workflow->id }}" {{ request('workflow_id') == $workflow->id ? 'selected' : '' }}>
                                    {{ $workflow->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="custom-select-wrapper">
                        <select name="estado" class="auto-filter-task">
                            <option value="">Cualquier Estado</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>

                    <div class="custom-select-wrapper">
                        <select name="prioridad" class="auto-filter-task">
                            <option value="">Prioridad</option>
                            <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>
                </div>

                <a href="{{ route('flujo.tasks.index') }}" class="btn-reset-filters-corp" title="Limpiar todo">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </form>
        </div>

        {{-- Contenedor Maestro para AJAX --}}
        <div id="ajaxTaskContainer">
            @fragment('tasks-list')
            <div class="main-content-area">
                
                {{-- VISTA DESKTOP: Tabla Corporativa Actualizada --}}
                <div class="table-card-container d-none-mobile">
                    <table class="corporate-table">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Detalle de la Unidad</th>
                                <th>Workflow / Proyecto</th> 
                                <th>Estado Operativo</th>
                                <th>Prioridad</th>
                                <th>Responsable</th>
                                <th width="120" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr class="task-row">
                                    {{-- CLIC EN ID -> VER --}}
                                    <td>
                                        <a href="{{ route('flujo.tasks.show', $task->id) }}" class="id-link-badge" title="Ver Expediente">
                                            #{{ $task->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="task-info-cell">
                                            {{-- CLIC EN NOMBRE -> EDITAR --}}
                                            <a href="{{ route('flujo.tasks.edit', $task) }}" class="task-title-main action-edit-link" title="Editar Unidad">
                                                @if(strtolower($task->prioridad) == 'alta') <span class="status-dot-pulse"></span> @endif
                                                {{ $task->titulo }}
                                            </a>
                                            <span class="task-desc-sub">{{ Str::limit($task->descripcion, 55) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{-- INFORMACIÓN DE WORKFLOW CON REDIRECCIÓN --}}
                                        @if($task->workflow)
                                            <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="workflow-link-cell" title="Ir al Proyecto">
                                                <i class="fas fa-project-diagram"></i>
                                                <span class="workflow-name-text">{{ $task->workflow->nombre }}</span>
                                                <i class="fas fa-external-link-alt icon-mini"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusStyle = match(strtolower($task->estado)) {
                                                'pendiente' => 'st-pending',
                                                'en_proceso' => 'st-process',
                                                'completado', 'completada' => 'st-completed',
                                                'revisado' => 'st-review',
                                                default => 'st-default',
                                            };
                                        @endphp
                                        <span class="badge-status-corp {{ $statusStyle }}">
                                            {{ str_replace('_', ' ', ucfirst($task->estado)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="prio-indicator prio-{{ strtolower($task->prioridad) }}">
                                            <i class="fas fa-circle"></i> {{ ucfirst($task->prioridad) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-small">{{ substr($task->user->name ?? 'N', 0, 1) }}</div>
                                            <span>{{ explode(' ', $task->user->name ?? 'Sin asignar')[0] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="actions-group">
                                            <a href="{{ route('flujo.tasks.show', $task->id) }}" class="btn-icon-table" title="Consultar"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="btn-icon-table" title="Gestionar"><i class="fas fa-pen-nib"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state-container">
                                        <div class="empty-content">
                                            <i class="fas fa-folder-open"></i>
                                            <p>No se encontraron unidades de trabajo.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VISTA MÓVIL: Cards Intuitivas --}}
                <div class="mobile-tasks-grid d-only-mobile">
                    @forelse($tasks as $task)
                        <div class="mobile-task-card">
                            <div class="card-top">
                                <a href="{{ route('flujo.tasks.show', $task->id) }}" class="m-id-tag">ID #{{ $task->id }}</a>
                                <span class="badge-status-corp {{ $statusStyle ?? 'st-default' }}">{{ ucfirst($task->estado) }}</span>
                            </div>
                            <div class="card-mid">
                                <a href="{{ route('flujo.tasks.edit', $task) }}" class="m-task-title action-edit-link">{{ $task->titulo }}</a>
                                
                                {{-- CONTEXTO DE WORKFLOW EN MÓVIL CON LINK --}}
                                @if($task->workflow)
                                    <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="m-workflow-tag">
                                        <i class="fas fa-project-diagram"></i> {{ $task->workflow->nombre }}
                                    </a>
                                @endif
                                
                                <p class="m-task-desc">{{ Str::limit($task->descripcion, 90) }}</p>
                            </div>
                            <div class="card-bottom">
                                <div class="m-user">
                                    <div class="user-avatar-small">{{ substr($task->user->name ?? 'N', 0, 1) }}</div>
                                    <span>{{ $task->user->name ?? 'S/A' }}</span>
                                </div>
                                <span class="prio-indicator prio-{{ strtolower($task->prioridad) }}">
                                    <i class="fas fa-circle"></i> {{ ucfirst($task->prioridad) }}
                                </span>
                            </div>
                        </div>
                    @empty
                         <div class="empty-mobile">No hay registros.</div>
                    @endforelse
                </div>

            </div>

            <footer class="index-footer">
                <div class="pagination-info">
                    Mostrando <strong>{{ $tasks->firstItem() }}-{{ $tasks->lastItem() }}</strong> de {{ $tasks->total() }}
                </div>
                <div class="corporate-pagination">
                    {{ $tasks->links() }}
                </div>
            </footer>
            @endfragment
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --brand-dark: #0f172a;
            --brand-accent: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --white: #ffffff;
            --border-soft: #e2e8f0;
            --prio-high: #ef4444;
        }

        body { background-color: var(--bg-page); color: var(--text-main); }
        .task-index-wrapper { max-width: 1450px; margin: 30px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }

        /* Estilos de Workflow Link en Tabla */
        .workflow-link-cell { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-decoration: none;
            color: var(--text-main);
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .workflow-link-cell:hover { 
            background: white;
            border-color: var(--brand-accent);
            color: var(--brand-accent);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .workflow-name-text { font-size: 0.8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
        .icon-mini { font-size: 0.65rem; opacity: 0; transition: opacity 0.2s; }
        .workflow-link-cell:hover .icon-mini { opacity: 1; }

        /* Estilo Workflow en Móvil */
        .m-workflow-tag { display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: var(--brand-accent); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 10px; text-decoration: none; border: 1px solid #dbeafe; }

        /* Requerimiento: Clic en ID = Ver | Clic en Nombre = Editar */
        .id-link-badge { background: #f1f5f9; color: var(--text-main); font-weight: 800; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; transition: all 0.2s; border: 1px solid var(--border-soft); }
        .id-link-badge:hover { background: var(--brand-dark); color: white; border-color: var(--brand-dark); transform: translateY(-1px); }

        .action-edit-link { color: var(--brand-dark); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s; line-height: 1.2; }
        .action-edit-link:hover { color: var(--brand-accent); transform: translateX(3px); }

        /* Header UI */
        .index-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px; }
        .system-tag { font-size: 0.7rem; font-weight: 800; color: var(--brand-accent); text-transform: uppercase; letter-spacing: 0.05em; }
        .main-title { font-size: 2.25rem; font-weight: 800; color: var(--brand-dark); letter-spacing: -0.03em; margin: 5px 0; }
        .main-subtitle { color: var(--text-muted); font-size: 1rem; }

        /* FILTROS */
        .index-toolbar { background: white; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border-soft); margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .filters-container-corp { display: flex; gap: 15px; align-items: center; }
        .search-input-wrapper { position: relative; flex: 1.5; }
        .search-input-wrapper input { width: 100%; padding: 12px 12px 12px 45px; border-radius: 12px; border: 1px solid var(--border-soft); background: #fdfdfd; font-size: 0.9rem; transition: all 0.3s; }
        .search-input-wrapper input:focus { border-color: var(--brand-accent); background: white; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .search-input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; }

        .select-group-corp { display: flex; gap: 10px; flex: 2; }
        .custom-select-wrapper { flex: 1; position: relative; }
        .custom-select-wrapper select { width: 100%; appearance: none; padding: 11px 15px; border-radius: 12px; border: 1px solid var(--border-soft); background: white; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: var(--text-main); transition: border 0.2s; }
        .custom-select-wrapper select:hover { border-color: var(--brand-accent); }

        /* Skeleton Animation */
        .skeleton-row td { padding: 25px 20px; }
        .skeleton-box { height: 15px; background: linear-gradient(90deg, #f0f3f5 25%, #e6e9ef 50%, #f0f3f5 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Desktop Table */
        .table-card-container { background: white; border-radius: 16px; border: 1px solid var(--border-soft); box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
        .corporate-table { width: 100%; border-collapse: collapse; }
        .corporate-table th { background: #f8fafc; padding: 16px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-soft); }
        .corporate-table td { padding: 18px 20px; border-bottom: 1px solid var(--border-soft); transition: background 0.2s; }
        .task-row:hover { background-color: #f9fbff; }

        /* Badge Status */
        .badge-status-corp { padding: 5px 12px; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .st-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .st-process { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .st-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

        /* Pulse for High Priority */
        .status-dot-pulse { height: 8px; width: 8px; background-color: var(--prio-high); border-radius: 50%; display: inline-block; margin-right: 10px; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

        /* Buttons */
        .btn-corporate-black { background: var(--brand-dark); color: white; padding: 12px 22px; border-radius: 12px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .btn-corporate-black:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); background: #000; }
        .btn-reset-filters-corp { background: #f1f5f9; color: var(--text-muted); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: all 0.3s; }
        .btn-reset-filters-corp:hover { background: #fee2e2; color: #ef4444; transform: rotate(180deg); }

        /* Mobile View */
        .d-only-mobile { display: none; }
        @media (max-width: 992px) {
            .filters-container-corp { flex-direction: column; align-items: stretch; }
            .select-group-corp { flex-wrap: wrap; }
            .index-header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .header-actions { width: 100%; }
            .header-actions a { flex: 1; justify-content: center; }
        }

        @media (max-width: 768px) {
            .d-none-mobile { display: none; }
            .d-only-mobile { display: block; }
            .mobile-task-card { background: white; border: 1px solid var(--border-soft); border-radius: 20px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
            .m-task-title { font-size: 1.15rem; margin-bottom: 8px; }
            .card-top { display: flex; justify-content: space-between; margin-bottom: 15px; }
            .card-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearch = document.getElementById('liveSearchTasks');
            const filterForm = document.getElementById('taskFilterForm');
            const ajaxContainer = document.getElementById('ajaxTaskContainer');
            let debounceTimer;

            const showSkeleton = () => {
                const skeleton = `
                    <div class="table-card-container">
                        <table class="corporate-table">
                            <tbody>
                                ${Array(5).fill(`
                                    <tr class="skeleton-row">
                                        <td><div class="skeleton-box" style="width: 40px"></div></td>
                                        <td><div class="skeleton-box" style="width: 200px"></div></td>
                                        <td><div class="skeleton-box" style="width: 150px"></div></td>
                                        <td><div class="skeleton-box" style="width: 100px"></div></td>
                                        <td><div class="skeleton-box" style="width: 80px"></div></td>
                                        <td><div class="skeleton-box" style="width: 100px"></div></td>
                                        <td><div class="skeleton-box" style="width: 100px"></div></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                ajaxContainer.innerHTML = skeleton;
            };

            const performFilter = async () => {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                showSkeleton();

                try {
                    const url = `${window.location.pathname}?${params.toString()}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await response.text();
                    
                    setTimeout(() => {
                        ajaxContainer.innerHTML = html;
                        window.history.pushState({}, '', url);
                    }, 300);

                } catch (error) {
                    console.error('Error:', error);
                    ajaxContainer.innerHTML = '<div class="error-msg text-center p-4">Error de conexión. Reintente.</div>';
                }
            };

            liveSearch.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performFilter, 500);
            });

            document.querySelectorAll('.auto-filter-task').forEach(el => {
                el.addEventListener('change', performFilter);
            });
        });
    </script>
</x-base-layout>