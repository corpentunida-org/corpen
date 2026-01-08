{{-- resources/views/flujo/componentes/tasks-card.blade.php --}}
<div class="corporate-task-card">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap');

        .corporate-task-card { 
            background: #fff; 
            padding: 20px; 
            font-family: 'Inter', sans-serif; 
            border-radius: 16px;
        }
        
        /* Toolbar Superior con simetría */
        .task-toolbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: stretch; 
            margin-bottom: 25px; 
            gap: 12px;
            height: 46px;
        }
        
        .task-search-wrapper { 
            position: relative; 
            flex: 1; 
            max-width: 450px; 
            display: flex;
            align-items: center;
        }
        .task-search-wrapper i { 
            position: absolute; 
            left: 16px; 
            color: #94a3b8; 
            font-size: 0.9rem; 
            z-index: 2;
        }
        .task-search-wrapper input { 
            width: 100%; 
            height: 100%;
            padding: 0 15px 0 45px; 
            border: 1px solid #e2e8f0; 
            border-radius: 12px; 
            font-size: 0.9rem; 
            outline: none; 
            background: #f8fafc;
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }
        .task-search-wrapper input:focus { 
            border-color: #4f46e5; 
            background: #fff; 
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08); 
        }

        .btn-add-task { 
            background: #0f172a; 
            color: #fff; 
            padding: 0 20px; 
            border-radius: 12px; 
            font-size: 0.85rem; 
            font-weight: 700; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            transition: 0.3s;
            white-space: nowrap;
        }
        .btn-add-task:hover { background: #000; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

        /* Tabla Estilizada - Visible en Desktop */
        .task-table-minimal { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .task-table-minimal th { 
            text-align: left; 
            padding: 12px 15px; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 0.08em; 
            color: #64748b; 
            border-bottom: 2px solid #f1f5f9;
            font-weight: 800;
        }
        .task-table-minimal td { padding: 16px 15px; font-size: 0.9rem; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
        .task-table-minimal tbody tr:hover { background: #fcfdfe; }

        /* Estados */
        .st-pill { 
            padding: 5px 12px; 
            border-radius: 8px; 
            font-size: 0.7rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            display: inline-flex;
            letter-spacing: 0.02em;
        }
        .st-completado { background: #dcfce7; color: #15803d; }
        .st-en_proceso { background: #e0f2fe; color: #0369a1; }
        .st-pendiente { background: #fef3c7; color: #b45309; }
        .st-revisado { background: #ede9fe; color: #5b21b6; }

        /* Prioridades */
        .prio-text { font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 6px; }
        .prio-alta { color: #ef4444; }
        .prio-media { color: #f59e0b; }
        .prio-baja { color: #10b981; }

        /* Responsable */
        .resp-cell { display: flex; align-items: center; gap: 10px; }
        .resp-avatar { 
            width: 28px; height: 28px; border-radius: 50%; 
            background: #4f46e5; color: #fff; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 0.7rem; font-weight: 800; 
            border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Acciones */
        .btn-act-icon { 
            color: #94a3b8; 
            margin-right: 12px; 
            transition: 0.2s; 
            font-size: 1rem;
        }
        .btn-act-icon:hover { color: #4f46e5; transform: scale(1.1); }

        .sub-info { display: block; font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top: 4px; }
        
        /* --- MÓVIL (Adaptive Cards) --- */
        .mobile-tasks-container { display: none; flex-direction: column; gap: 12px; }

        @media (max-width: 900px) {
            .task-table-minimal { display: none; } /* Ocultar tabla */
            .mobile-tasks-container { display: flex; } /* Mostrar tarjetas */
            
            .task-toolbar { flex-direction: column; height: auto; align-items: stretch; }
            .task-search-wrapper { max-width: 100%; height: 48px; }
            .btn-add-task { height: 48px; justify-content: center; }

            .task-mobile-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                transition: 0.2s;
            }
            .task-mobile-card:active { background: #f8fafc; transform: scale(0.98); }
            
            .m-task-header { display: flex; justify-content: space-between; align-items: flex-start; }
            .m-task-id { font-family: monospace; font-weight: 700; color: #94a3b8; font-size: 0.8rem; }
            .m-task-title { font-weight: 700; font-size: 1rem; color: #1e293b; margin-top: 4px; display: block; }
            
            .m-task-details { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
            .m-detail-item label { display: block; font-size: 0.65rem; text-transform: uppercase; color: #94a3b8; font-weight: 800; margin-bottom: 4px; }
            
            .m-task-actions { 
                display: flex; 
                gap: 10px; 
                margin-top: 12px; 
                border-top: 1px solid #f1f5f9; 
                padding-top: 12px;
            }
            .m-btn-action { 
                flex: 1; 
                text-align: center; 
                padding: 10px; 
                border-radius: 10px; 
                background: #f1f5f9; 
                color: #475569; 
                font-weight: 700; 
                text-decoration: none; 
                font-size: 0.8rem; 
            }
            .m-btn-action:first-child { background: #eef2ff; color: #4f46e5; }
        }
    </style>

    <div class="task-toolbar">
        <form action="{{ route('flujo.tasks.index') }}" method="GET" class="task-search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Buscar por título o responsable..." value="{{ request('search') }}">
        </form>
        <a href="{{ route('flujo.tasks.create') }}" class="btn-add-task">
            <i class="fas fa-plus"></i> Nueva Unidad
        </a>
    </div>

    {{-- Vista Desktop --}}
    <table class="task-table-minimal">
        <thead>
            <tr>
                <th width="50">Ref</th>
                <th>Unidad y Workflow</th>
                <th>Estado Operativo</th>
                <th>Prioridad y Plazo</th>
                <th>Responsable</th>
                <th width="100">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td style="font-family: monospace; font-weight: 700; color: #94a3b8;">#{{ $task->id }}</td>
                <td>
                    <div style="font-weight: 700;">{{ $task->titulo }}</div>
                    <div class="sub-info">
                        <i class="fas fa-project-diagram" style="font-size: 0.7rem; color: #4f46e5;"></i> 
                        {{ $task->workflow->nombre ?? 'Sin Workflow' }}
                    </div>
                </td>
                <td>
                    <span class="st-pill st-{{ $task->estado }}">
                        {{ str_replace('_', ' ', $task->estado) }}
                    </span>
                </td>
                <td>
                    <div class="prio-text prio-{{ $task->prioridad }}">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i> {{ ucfirst($task->prioridad) }}
                    </div>
                    <div class="sub-info">
                        <i class="far fa-calendar-alt"></i> 
                        {{ $task->fecha_limite ? $task->fecha_limite->format('d M, Y') : 'S/F' }}
                    </div>
                </td>
                <td>
                    <div class="resp-cell">
                        <div class="resp-avatar">{{ substr($task->user->name ?? '?', 0, 1) }}</div>
                        <span style="font-weight: 600;">{{ explode(' ', $task->user->name ?? 'N/A')[0] }}</span>
                    </div>
                </td>
                <td>
                    <a href="{{ route('flujo.tasks.show', $task->id) }}" class="btn-act-icon" title="Consultar"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="btn-act-icon" title="Editar"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 60px; color: #94a3b8;">
                    <i class="fas fa-tasks" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.2;"></i>
                    No hay unidades de trabajo registradas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Vista Móvil (Cards) --}}
    <div class="mobile-tasks-container">
        @forelse($tasks as $task)
        <div class="task-mobile-card">
            <div class="m-task-header">
                <div>
                    <span class="m-task-id">#{{ $task->id }}</span>
                    <span class="m-task-title">{{ $task->titulo }}</span>
                </div>
                <span class="st-pill st-{{ $task->estado }}">
                    {{ str_replace('_', ' ', $task->estado) }}
                </span>
            </div>
            
            <div class="m-task-details">
                <div class="m-detail-item">
                    <label>Prioridad</label>
                    <div class="prio-text prio-{{ $task->prioridad }}">
                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i> {{ ucfirst($task->prioridad) }}
                    </div>
                </div>
                <div class="m-detail-item">
                    <label>Plazo</label>
                    <span style="font-size: 0.8rem; font-weight: 600;">{{ $task->fecha_limite ? $task->fecha_limite->format('d/m/y') : 'S/F' }}</span>
                </div>
                <div class="m-detail-item">
                    <label>Workflow</label>
                    <span style="font-size: 0.75rem; color: #4f46e5; font-weight: 600;">{{ Str::limit($task->workflow->nombre ?? 'N/A', 15) }}</span>
                </div>
                <div class="m-detail-item">
                    <label>Responsable</label>
                    <div class="resp-cell">
                        <div class="resp-avatar" style="width: 20px; height: 20px; font-size: 0.5rem;">{{ substr($task->user->name ?? '?', 0, 1) }}</div>
                        <span style="font-size: 0.8rem; font-weight: 600;">{{ explode(' ', $task->user->name ?? 'N/A')[0] }}</span>
                    </div>
                </div>
            </div>

            <div class="m-task-actions">
                <a href="{{ route('flujo.tasks.show', $task->id) }}" class="m-btn-action">Detalles</a>
                <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="m-btn-action">Editar</a>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            No hay tareas disponibles.
        </div>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $tasks->links() }}
    </div>
</div>