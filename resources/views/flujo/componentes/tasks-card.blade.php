{{-- resources/views/flujo/componentes/tasks-card.blade.php --}}
<div class="corporate-task-card">
    <style>
        .corporate-task-card { background: #fff; padding: 10px 5px; font-family: 'Inter', sans-serif; }
        
        /* Toolbar Superior */
        .task-toolbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            gap: 15px;
        }
        
        .task-search-wrapper { 
            position: relative; 
            flex: 1; 
            max-width: 400px; 
        }
        .task-search-wrapper i { 
            position: absolute; 
            left: 12px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #94a3b8; 
            font-size: 0.85rem; 
        }
        .task-search-wrapper input { 
            width: 100%; 
            padding: 8px 12px 8px 35px; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            font-size: 0.85rem; 
            outline: none; 
            background: #f8fafc;
            transition: 0.2s;
        }
        .task-search-wrapper input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.05); }

        .btn-add-task { 
            background: #0f172a; 
            color: #fff; 
            padding: 8px 16px; 
            border-radius: 8px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            transition: 0.2s;
        }
        .btn-add-task:hover { background: #000; transform: translateY(-1px); }

        /* Tabla Estilizada */
        .task-table-minimal { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .task-table-minimal th { 
            text-align: left; 
            padding: 12px 15px; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            color: #64748b; 
            border-bottom: 2px solid #f1f5f9;
        }
        .task-table-minimal td { padding: 14px 15px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
        .task-table-minimal tbody tr:hover { background: #fcfdfe; }

        /* Estados Corporativos */
        .st-pill { 
            padding: 4px 10px; 
            border-radius: 6px; 
            font-size: 0.7rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            display: inline-block;
        }
        .st-completado { background: #dcfce7; color: #15803d; }
        .st-en_proceso { background: #e0f2fe; color: #0369a1; }
        .st-pendiente { background: #fef3c7; color: #b45309; }
        .st-revisado { background: #ede9fe; color: #5b21b6; }

        /* Prioridades */
        .prio-text { font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .prio-alta { color: #ef4444; }
        .prio-media { color: #f59e0b; }
        .prio-baja { color: #10b981; }

        /* Responsable */
        .resp-cell { display: flex; align-items: center; gap: 8px; }
        .resp-avatar { 
            width: 24px; height: 24px; border-radius: 50%; 
            background: #e2e8f0; color: #475569; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 0.65rem; font-weight: 700; 
        }

        /* Acciones */
        .btn-act-icon { 
            color: #94a3b8; 
            margin-right: 8px; 
            transition: 0.2s; 
            font-size: 0.9rem;
        }
        .btn-act-icon:hover { color: #2563eb; }

        .pagination-wrap { margin-top: 15px; }
        
        /* Detalles secundarios en celdas */
        .sub-info { display: block; font-size: 0.75rem; color: #64748b; font-weight: 400; margin-top: 2px; }
    </style>

    <div class="task-toolbar">
        <form action="{{ route('flujo.tasks.index') }}" method="GET" class="task-search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Filtrar por título o responsable..." value="{{ request('search') }}">
        </form>
        <a href="{{ route('flujo.tasks.create') }}" class="btn-add-task">
            <i class="fas fa-plus"></i> Nueva Unidad
        </a>
    </div>

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
                <td style="font-family: monospace; font-weight: 600; color: #64748b;">#{{ $task->id }}</td>
                <td>
                    <div style="font-weight: 600;">{{ $task->titulo }}</div>
                    <div class="sub-info">
                        <i class="fas fa-project-diagram" style="font-size: 0.7rem;"></i> 
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
                        <span>{{ explode(' ', $task->user->name ?? 'N/A')[0] }}</span>
                    </div>
                </td>
                <td>
                    <a href="{{ route('flujo.tasks.show', $task->id) }}" class="btn-act-icon" title="Consultar"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="btn-act-icon" title="Editar"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 40px; color: #94a3b8; font-style: italic;">
                    No se han registrado unidades de trabajo bajo este criterio.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        {{ $tasks->links() }}
    </div>
</div>