<x-base-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #4f46e5;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        .show-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
        }

        /* Header */
        .show-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .project-title-area h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0.5rem 0 0 0;
            letter-spacing: -0.02em;
        }

        .project-meta {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            align-items: center;
        }

        .badge-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-status { background: #e0e7ff; color: #4338ca; }
        .badge-active { background: #d1fae5; color: #065f46; }

        /* Grid */
        .show-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) { .show-grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
        }

        /* Tareas */
        .task-row-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            margin: 0 -0.5rem;
            border-radius: 12px;
            border-bottom: 1px solid var(--border);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .task-row-link:last-child { border-bottom: none; }
        .task-row-link:hover {
            background-color: #f1f5f9;
            transform: translateX(8px);
        }
        .task-info h4 { font-size: 0.95rem; margin: 0; font-weight: 600; }
        .task-status { font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 8px; background: #f8fafc; border: 1px solid var(--border); text-transform: uppercase;}

        /* Historial Estilo Timeline */
        .history-list { position: relative; padding-left: 1.5rem; }
        .history-list::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 2px;
            background: var(--border);
        }
        .history-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .history-item::after {
            content: '';
            position: absolute;
            left: calc(-1.5rem - 4px); top: 5px;
            width: 10px; height: 10px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 2px solid var(--primary);
        }
        .history-content {
            font-size: 0.85rem;
            line-height: 1.4;
        }
        .history-time { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; display: block; }
        .history-user { font-weight: 600; color: var(--text-main); }
        .change-tag { font-family: monospace; font-size: 0.75rem; padding: 2px 5px; background: #f1f5f9; border-radius: 4px; color: var(--primary); }

        /* General UI */
        .info-list { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        .info-item label { display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
        .info-item p { margin: 0; font-size: 0.95rem; font-weight: 500; }
        .json-view { background: #1e293b; color: #a5f3fc; padding: 1rem; border-radius: 12px; font-family: 'Fira Code', monospace; font-size: 0.8rem; overflow-x: auto; max-height: 300px; }
        .btn-modern { padding: 10px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-back { background: white; border: 1px solid var(--border); color: var(--text-muted); }
        .btn-edit { background: var(--primary); color: white; border: none; }
        .btn-ghost-modern { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; }
    </style>

    <div class="show-container">
        <header class="show-header">
            <div class="project-title-area">
                <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1rem;">
                    <a href="{{ route('flujo.workflows.index') }}" class="btn-modern btn-back">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                    <a href="{{ route('flujo.tablero') }}" class="btn-ghost-modern">
                        <i class="fas fa-home"></i> <span>Inicio</span>
                    </a>
                </div>
                <h1>{{ $workflow->nombre }}</h1>
                <div class="project-meta">
                    <span class="badge-pill badge-status"><i class="fas fa-layer-group"></i> {{ ucfirst($workflow->estado) }}</span>
                    <span class="badge-pill badge-active" style="{{ $workflow->activo ? '' : 'background:#fee2e2; color:#991b1b' }}">
                        <i class="fas {{ $workflow->activo ? 'fa-check' : 'fa-times' }}"></i> {{ $workflow->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span style="font-size: 0.8rem; color: var(--text-muted)">ID: #{{ $workflow->id }}</span>
                </div>
            </div>
            <div class="btn-actions">
                <a href="{{ route('flujo.workflows.edit', $workflow) }}" class="btn-modern btn-edit">
                    <i class="fas fa-edit"></i> Editar Proyecto
                </a>
            </div>
        </header>

        <div class="show-grid">
            <div class="main-column">
                <div class="card">
                    <div class="card-title"><i class="fas fa-align-left" style="color: var(--primary)"></i> Descripción</div>
                    <p style="line-height: 1.6; color: var(--text-main); margin: 0;">
                        {{ $workflow->descripcion ?: 'Sin descripción detallada.' }}
                    </p>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-tasks" style="color: var(--success)"></i> Tareas
                        <span style="margin-left:auto; font-size: 0.7rem; background: #f1f5f9; padding: 2px 8px; border-radius: 10px;">{{ $workflow->tasks->count() }} total</span>
                    </div>
                    <div class="tasks-list">
                        @forelse($workflow->tasks as $task)
                            <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="task-row-link">
                                <div class="task-info">
                                    <h4>{{ $task->titulo }}</h4>
                                    <span>Creada el {{ $task->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="task-status">{{ $task->estado }}</div>
                                    <i class="fas fa-chevron-right" style="color: var(--border); font-size: 0.8rem;"></i>
                                </div>
                            </a>
                        @empty
                            <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem;">No hay tareas.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-history" style="color: var(--warning)"></i> Historial de Actividad
                    </div>
                    <div class="history-list">
                        @php
                            // Obtenemos los historiales de todas las tareas de este workflow
                            $workflowHistories = \App\Models\Flujo\TaskHistory::whereIn('task_id', $workflow->tasks->pluck('id'))
                                                ->with(['task', 'user'])
                                                ->latest()
                                                ->get();
                        @endphp

                        @forelse($workflowHistories as $history)
                            <div class="history-item">
                                <span class="history-time">{{ $history->created_at->diffForHumans() }}</span>
                                <div class="history-content">
                                    <span class="history-user">{{ $history->user->name ?? 'Sistema' }}</span> 
                                    cambió el estado de <strong>{{ $history->task->titulo }}</strong>
                                    de <span class="change-tag">{{ $history->estado_anterior }}</span> 
                                    a <span class="change-tag">{{ $history->estado_nuevo }}</span>
                                </div>
                            </div>
                        @empty
                            <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem;">No hay registros de actividad.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="side-column">
                <div class="card">
                    <div class="card-title"><i class="fas fa-info-circle"></i> Información General</div>
                    <div class="info-list">
                        <div class="info-item">
                            <label>Prioridad</label>
                            <p><i class="fas fa-flag" style="color: {{ $workflow->prioridad == 'crítica' ? 'var(--danger)' : 'var(--warning)' }}"></i> {{ ucfirst($workflow->prioridad) }}</p>
                        </div>
                        <div class="info-item">
                            <label>Responsable</label>
                            <p><i class="far fa-user-circle"></i> {{ $workflow->asignado?->name ?: 'No asignado' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Cronograma</label>
                            <p style="font-size: 0.85rem;">{{ $workflow->fecha_inicio?->format('d M, Y') ?? 'S/N' }} — {{ $workflow->fecha_fin?->format('d M, Y') ?? 'S/N' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Autor</label>
                            <p><i class="fas fa-pen-nib"></i> {{ $workflow->creator?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title"><i class="fas fa-code"></i> Configuración JSON</div>
                    <pre class="json-view">{{ json_encode($workflow->configuracion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}' }}</pre>
                    <button onclick="copyJson()" style="margin-top: 10px; background: none; border: none; color: var(--primary); font-size: 0.75rem; font-weight: 700; cursor: pointer; padding: 0;">
                        <i class="far fa-copy"></i> Copiar JSON
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyJson() {
            const jsonText = document.querySelector('.json-view').innerText;
            navigator.clipboard.writeText(jsonText).then(() => {
                alert('JSON copiado al portapapeles');
            });
        }
    </script>
</x-base-layout>