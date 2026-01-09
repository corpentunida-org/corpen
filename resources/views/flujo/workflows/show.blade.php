<x-base-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap');

        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --bg-body: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }

        .show-container {
            max-width: 1250px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Header Mejorado */
        .show-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .project-title-area h1 {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 0.75rem 0 0 0;
            letter-spacing: -0.04em;
            color: var(--text-main);
        }

        .project-meta {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            align-items: center;
        }

        .badge-pill {
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-status { background: var(--primary-light); color: var(--primary); border: 1px solid #c7d2fe; }
        .badge-active { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-inactive { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Grid Layout */
        .show-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 1024px) { .show-grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            transition: box-shadow 0.3s ease;
        }

        .card:hover { box-shadow: var(--shadow); }

        .card-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-main);
        }

        /* Task List Estilo Moderno */
        .tasks-list { display: flex; flex-direction: column; gap: 12px; }

        .task-row-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid var(--border);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .task-row-link:hover {
            background-color: #ffffff;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .task-info h4 { font-size: 1rem; margin: 0 0 4px 0; font-weight: 700; color: var(--text-main); }
        .task-info span { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }
        
        .task-status { 
            font-size: 0.65rem; 
            font-weight: 800; 
            padding: 5px 12px; 
            border-radius: 8px; 
            background: white; 
            border: 1px solid var(--border); 
            text-transform: uppercase;
            color: var(--text-muted);
        }

        /* Timeline de Actividad */
        .history-list { position: relative; padding-left: 2rem; }
        .history-list::before {
            content: '';
            position: absolute;
            left: 7px; top: 0; bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .history-item { position: relative; padding-bottom: 2rem; }
        .history-item::after {
            content: '';
            position: absolute;
            left: calc(-2rem + 2px); top: 6px;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid white;
            box-shadow: 0 0 0 1px var(--primary);
            z-index: 2;
        }

        .history-content {
            font-size: 0.9rem;
            line-height: 1.5;
            color: var(--text-muted);
        }

        .history-time { font-size: 0.75rem; font-weight: 700; color: var(--primary); margin-bottom: 6px; display: block; }
        .history-user { font-weight: 700; color: var(--text-main); }
        .change-tag { font-family: 'Fira Code', monospace; font-size: 0.7rem; padding: 3px 6px; background: #f1f5f9; border-radius: 6px; color: var(--primary); font-weight: 600; border: 1px solid var(--border); }

        /* Sidebar Info */
        .info-list { display: flex; flex-direction: column; gap: 1.25rem; }
        .info-item { padding: 12px; border-radius: 12px; transition: background 0.2s; }
        .info-item:hover { background: #f8fafc; }
        .info-item label { display: block; font-size: 0.65rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em; }
        .info-item p { margin: 0; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }

        .json-view { 
            background: #0f172a; 
            color: #38bdf8; 
            padding: 1.25rem; 
            border-radius: 14px; 
            font-family: 'Fira Code', monospace; 
            font-size: 0.8rem; 
            line-height: 1.6;
            overflow-x: auto; 
            max-height: 350px;
            border: 1px solid #1e293b;
        }

        /* Buttons */
        .btn-modern { 
            padding: 12px 20px; 
            border-radius: 12px; 
            font-size: 0.85rem; 
            font-weight: 700; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            transition: all 0.3s ease; 
        }

        .btn-back { background: white; border: 1px solid var(--border); color: var(--text-muted); }
        .btn-back:hover { background: #f8fafc; color: var(--text-main); border-color: var(--text-muted); }

        .btn-edit { background: var(--text-main); color: white; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn-edit:hover { background: #000; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .btn-success-modern { 
            background: var(--success); 
            color: white; 
            border: none; 
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-success-modern:hover { background: #059669; transform: translateY(-1px); }

        .btn-ghost-modern { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            color: var(--text-muted); 
            text-decoration: none; 
            font-size: 0.85rem; 
            font-weight: 700; 
            padding: 8px 12px;
            border-radius: 10px;
            transition: 0.2s;
        }
        .btn-ghost-modern:hover { background: white; color: var(--primary); }

        .copy-btn {
            margin-top: 12px;
            background: var(--primary-light);
            border: 1px solid #c7d2fe;
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            transition: 0.2s;
        }
        .copy-btn:hover { background: var(--primary); color: white; }
    </style>

    <div class="show-container">
        <header class="show-header">
            <div class="project-title-area">
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <a href="{{ route('flujo.workflows.index') }}" class="btn-modern btn-back">
                        <i class="fas fa-chevron-left"></i> Listado General
                    </a>
                    <a href="{{ route('flujo.tablero') }}" class="btn-ghost-modern">
                        <i class="fas fa-th-large"></i> <span>Tablero Principal</span>
                    </a>
                </div>
                <span class="system-tag" style="color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em;">Expediente de Proyecto</span>
                <h1>{{ $workflow->nombre }}</h1>
                <div class="project-meta">
                    <span class="badge-pill badge-status"><i class="fas fa-layer-group"></i> {{ str_replace('_', ' ', ucfirst($workflow->estado)) }}</span>
                    <span class="badge-pill {{ $workflow->activo ? 'badge-active' : 'badge-inactive' }}">
                        <i class="fas {{ $workflow->activo ? 'fa-check-circle' : 'fa-pause-circle' }}"></i> {{ $workflow->activo ? 'Flujo Activo' : 'Flujo En Pausa' }}
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-left: 8px;">REF: #{{ $workflow->id }}</span>
                </div>
            </div>
            <div class="btn-actions">
                <a href="{{ route('flujo.workflows.edit', $workflow) }}" class="btn-modern btn-edit">
                    <i class="fas fa-pen-nib"></i> Modificar Workflow
                </a>
            </div>
        </header>

        <div class="show-grid">
            <div class="main-column">
                <div class="card">
                    <div class="card-title"><i class="fas fa-align-left" style="color: var(--primary)"></i> Descripción Técnica</div>
                    <p style="line-height: 1.7; color: #475569; margin: 0; font-size: 0.95rem;">
                        {{ $workflow->descripcion ?: 'Este workflow no cuenta con una descripción técnica registrada actualmente.' }}
                    </p>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-list-check" style="color: var(--success)"></i> Unidades de Trabajo
                        <div style="margin-left:auto; display: flex; align-items: center; gap: 15px;">
                            {{-- BOTÓN SOLICITADO: Crear tarea para este proyecto --}}
                            <a href="{{ route('flujo.tasks.create', ['workflow_id' => $workflow->id]) }}" class="btn-success-modern">
                                <i class="fas fa-plus"></i> Crear tarea a este proyecto
                            </a>
                            <span style="font-size: 0.65rem; background: var(--primary-light); color: var(--primary); padding: 4px 10px; border-radius: 99px; font-weight: 800;">{{ $workflow->tasks->count() }} ASIGNADAS</span>
                        </div>
                    </div>
                    <div class="tasks-list">
                        @forelse($workflow->tasks as $task)
                            <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="task-row-link">
                                <div class="task-info">
                                    <h4>{{ $task->titulo }}</h4>
                                    <span><i class="far fa-calendar-alt"></i> Registrada el {{ $task->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1.25rem;">
                                    <div class="task-status">{{ ucfirst($task->estado) }}</div>
                                    <i class="fas fa-arrow-right" style="color: var(--primary); font-size: 0.8rem; opacity: 0.5;"></i>
                                </div>
                            </a>
                        @empty
                            <div style="text-align: center; padding: 2rem;">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: var(--border); margin-bottom: 1rem; display: block;"></i>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">No hay tareas vinculadas a este workflow.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-stream" style="color: var(--warning)"></i> Trazabilidad de Estados
                    </div>
                    <div class="history-list">
                        @php
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
                                    actualizó la unidad <strong>{{ $history->task->titulo }}</strong> 
                                    pasando de <span class="change-tag">{{ str_replace('_', ' ', $history->estado_anterior) }}</span> 
                                    a <span class="change-tag" style="background: var(--primary-light)">{{ str_replace('_', ' ', $history->estado_nuevo) }}</span>
                                </div>
                            </div>
                        @empty
                            <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 1rem 0;">Sin registros de actividad reciente.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="side-column">
                <div class="card" style="border-top: 4px solid var(--primary);">
                    <div class="card-title"><i class="fas fa-database"></i> Atributos</div>
                    <div class="info-list">
                        <div class="info-item">
                            <label>Prioridad del Flujo</label>
                            <p>
                                <i class="fas fa-circle" style="font-size: 0.6rem; color: {{ $workflow->prioridad == 'crítica' ? 'var(--danger)' : ($workflow->prioridad == 'alta' ? 'var(--warning)' : 'var(--success)') }}"></i>
                                {{ ucfirst($workflow->prioridad) }}
                            </p>
                        </div>
                        <div class="info-item">
                            <label>Líder Responsable</label>
                            <p><i class="far fa-user-circle" style="color: var(--primary)"></i> {{ $workflow->asignado?->name ?: 'Personal no asignado' }}</p>
                        </div>
                        <div class="info-item">
                            <label>Ventana de Tiempo</label>
                            <p style="font-size: 0.85rem; letter-spacing: -0.01em;">
                                <i class="far fa-calendar-alt"></i>
                                {{ $workflow->fecha_inicio?->format('d M') ?? 'Inicia S/N' }} — {{ $workflow->fecha_fin?->format('d M, Y') ?? 'Finaliza S/N' }}
                            </p>
                        </div>
                        <div class="info-item">
                            <label>Origen de Datos</label>
                            <p><i class="fas fa-fingerprint"></i> Creado por {{ $workflow->creator?->name ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title"><i class="fas fa-laptop-code"></i> Payload de Configuración</div>
                    <pre class="json-view" id="jsonPayload">{{ json_encode($workflow->configuracion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}' }}</pre>
                    <button class="copy-btn" onclick="copyJson()">
                        <i class="far fa-copy"></i> Copiar objeto JSON
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyJson() {
            const jsonText = document.getElementById('jsonPayload').innerText;
            navigator.clipboard.writeText(jsonText).then(() => {
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
                btn.style.background = 'var(--success)';
                btn.style.color = 'white';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = 'var(--primary-light)';
                    btn.style.color = 'var(--primary)';
                }, 2000);
            });
        }
    </script>
</x-base-layout>