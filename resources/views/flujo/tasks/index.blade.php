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
                    <i class="fas fa-th-large"></i> Tablero
                </a>
                <a href="{{ route('flujo.tasks.create') }}" class="btn-corporate-black">
                    <i class="fas fa-plus"></i> Nueva Tarea
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

                <div class="select-group-corp">
                    {{-- Filtro de Estado --}}
                    <select name="estado" class="auto-filter-task">
                        <option value="">Cualquier Estado</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    </select>

                    {{-- Filtro de Prioridad --}}
                    <select name="prioridad" class="auto-filter-task">
                        <option value="">Toda Prioridad</option>
                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                    </select>

                    {{-- Filtro de Responsable --}}
                    <select name="user_id" class="auto-filter-task">
                        <option value="">Responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <a href="{{ route('flujo.tasks.index') }}" class="btn-reset-filters" title="Limpiar todo">
                    <i class="fas fa-times"></i>
                </a>
            </form>
        </div>

        {{-- Contenedor Maestro para AJAX --}}
        <div id="ajaxTaskContainer">
            @fragment('tasks-list')
            <div class="table-card-container">
                <table class="corporate-table">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Detalle de la Unidad</th>
                            <th>Estado Operativo</th>
                            <th>Prioridad</th>
                            <th>Responsable</th>
                            <th>Workflow Relacionado</th>
                            <th width="100" class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="text-id">#{{ $task->id }}</td>
                                <td>
                                    <div class="task-info-cell">
                                        <span class="task-title-main">{{ $task->titulo }}</span>
                                        <span class="task-desc-sub">{{ Str::limit($task->descripcion, 50) }}</span>
                                    </div>
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
                                        <span>{{ $task->user->name ?? 'Sin asignar' }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{-- COLUMNA ACTUALIZADA: Al dar clic redirige al show del proyecto --}}
                                    @if($task->workflow)
                                        <a href="{{ route('flujo.workflows.show', $task->workflow->id) }}" class="workflow-link-tag">
                                            <i class="fas fa-project-diagram"></i> {{ $task->workflow->nombre }}
                                        </a>
                                    @else
                                        <span class="workflow-tag-empty">General</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="actions-group">
                                        <a href="{{ route('flujo.tasks.show', $task->id) }}" class="btn-icon-table" title="Consultar"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('flujo.tasks.show', $task->id) }}" class="btn-icon-table" title="Gestionar"><i class="fas fa-pen"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state-row">
                                    <i class="fas fa-inbox"></i>
                                    <p>No se encontraron registros de trabajo bajo los criterios seleccionados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer con Paginación --}}
            <footer class="index-footer">
                <div class="pagination-info">
                    Mostrando {{ $tasks->firstItem() }} - {{ $tasks->lastItem() }} de {{ $tasks->total() }} registros
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
            --bg-page: #fafafa;
            --white: #ffffff;
            --border-soft: #e2e8f0;
        }

        .task-index-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 24px; font-family: 'Inter', sans-serif; color: var(--text-main); }

        /* HEADER */
        .index-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
        .system-tag { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: var(--brand-accent); letter-spacing: 0.1em; display: block; margin-bottom: 8px; }
        .main-title { font-size: 2rem; font-weight: 800; letter-spacing: -0.04em; margin: 0; color: var(--brand-dark); }
        .main-subtitle { font-size: 0.95rem; color: var(--text-muted); margin-top: 4px; }
        .header-actions { display: flex; gap: 12px; }

        /* TOOLBAR FILTROS */
        .index-toolbar { background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border-soft); margin-bottom: 24px; }
        .filters-container-corp { display: flex; gap: 12px; align-items: center; }
        .search-input-wrapper { position: relative; flex: 1; }
        .search-input-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; }
        .search-input-wrapper input { width: 100%; padding: 10px 10px 10px 40px; border-radius: 8px; border: 1px solid var(--border-soft); font-size: 0.9rem; outline: none; transition: 0.2s; }
        .search-input-wrapper input:focus { border-color: var(--brand-accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

        .select-group-corp { display: flex; gap: 8px; }
        .select-group-corp select { padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-soft); font-size: 0.85rem; background: #fff; outline: none; cursor: pointer; color: var(--text-main); }
        .btn-reset-filters { padding: 10px; color: var(--text-muted); transition: 0.2s; }
        .btn-reset-filters:hover { color: #ef4444; }

        /* TABLE */
        .table-card-container { background: var(--white); border-radius: 16px; border: 1px solid var(--border-soft); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .corporate-table { width: 100%; border-collapse: collapse; text-align: left; }
        .corporate-table th { background: #f8fafc; padding: 14px 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); border-bottom: 1px solid var(--border-soft); }
        .corporate-table td { padding: 16px 20px; font-size: 0.85rem; border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
        .corporate-table tbody tr:hover { background-color: #f9fafb; }

        /* WORKFLOW LINK */
        .workflow-link-tag { text-decoration: none; color: var(--brand-accent); background: rgba(37, 99, 235, 0.08); padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .workflow-link-tag:hover { background: var(--brand-accent); color: white; transform: translateY(-1px); }
        .workflow-tag-empty { color: var(--text-muted); font-size: 0.75rem; font-style: italic; }

        /* BADGES */
        .badge-status-corp { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .st-pending { background: #fef3c7; color: #b45309; }
        .st-process { background: #e0f2fe; color: #0369a1; }
        .st-completed { background: #dcfce7; color: #15803d; }
        .prio-indicator { font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .prio-indicator i { font-size: 6px; }
        .prio-alta { color: #ef4444; }
        .prio-media { color: #f59e0b; }
        .prio-baja { color: #10b981; }

        .btn-corporate-black { background: var(--brand-dark); color: var(--white); padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-ghost-corporate { background: var(--white); color: var(--text-main); padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; border: 1px solid var(--border-soft); }
        
        .index-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
        #ajaxTaskContainer { transition: opacity 0.3s ease; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearch = document.getElementById('liveSearchTasks');
            const filterForm = document.getElementById('taskFilterForm');
            const ajaxContainer = document.getElementById('ajaxTaskContainer');
            let debounceTimer;

            const performFilter = async () => {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                
                ajaxContainer.style.opacity = '0.5';

                try {
                    const url = `${window.location.pathname}?${params.toString()}`;
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    const html = await response.text();
                    ajaxContainer.innerHTML = html;
                    
                    window.history.pushState({}, '', url);
                } catch (error) {
                    console.error('Error al filtrar:', error);
                } finally {
                    ajaxContainer.style.opacity = '1';
                }
            };

            liveSearch.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performFilter, 400);
            });

            document.querySelectorAll('.auto-filter-task').forEach(el => {
                el.addEventListener('change', performFilter);
            });
        });
    </script>
</x-base-layout>