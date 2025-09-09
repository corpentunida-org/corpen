<x-base-layout>
    <div class="container">
        <h1 class="titulo-principal">Panel de Control de Operaciones</h1>
<hr>
        <div class="card-grid">
            
            {{-- ================= Workflows Card ================= --}}
            <div class="card">
                <h2 class="card-title"><i class="fas fa-sitemap card-icon"></i> Gestión de Procesos</h2>
                <div class="card-content">
                    <div class="toolbar">
                        <form action="{{ route('flujo.workflows.index') }}" method="GET" class="buscador">
                            <input type="text" name="search" placeholder="Buscar proceso..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-buscar"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="{{ route('flujo.workflows.create') }}" class="btn btn-crear"><i class="fas fa-plus-circle"></i> Nuevo Proceso</a>
                    </div>
                    <div class="table-responsive">
                        <table class="tabla-elementos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Proceso</th>
                                    <th>Creador</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workflows as $workflow)
                                <tr>
                                    <td>{{ $workflow->id }}</td>
                                    <td>{{ $workflow->nombre }}</td>
                                    <td>{{ $workflow->creator->name ?? 'N/A' }}</td>
                                    <td class="acciones">
                                        <a href="{{ route('flujo.workflows.show', $workflow->id) }}" class="accion ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('flujo.workflows.edit', $workflow->id) }}" class="accion editar"><i class="fas fa-pencil-alt"></i></a>
                                        <form action="{{ route('flujo.workflows.destroy', $workflow->id) }}" method="POST" class="inline-form" onsubmit="return confirm('¿Confirmas la eliminación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="accion eliminar"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginacion">{{ $workflows->links() }}</div>
                    </div>
                </div>
            </div>
<hr>
            {{-- ================= Tasks Card ================= --}}
            <div class="card">
                <h2 class="card-title"><i class="fas fa-clipboard-list card-icon"></i> Tareas Pendientes</h2>
                <div class="card-content">
                    <div class="toolbar">
                        <form action="{{ route('flujo.tasks.index') }}" method="GET" class="buscador">
                            <input type="text" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-buscar"><i class="fas fa-search"></i></button>
                        </form>
                        <a href="{{ route('flujo.tasks.create') }}" class="btn btn-crear"><i class="fas fa-plus-circle"></i> Nueva Tarea</a>
                    </div>
                    <div class="table-responsive">
                        <table class="tabla-elementos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Estado</th>
                                    <th>Responsable</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr>
                                    <td>{{ $task->id }}</td>
                                    <td>{{ $task->titulo }}</td>
                                    <td><span class="estado estado-{{ $task->estado }}">{{ ucfirst($task->estado) }}</span></td>
                                    <td>{{ $task->user->name ?? 'N/A' }}</td>
                                    <td class="acciones">
                                        <a href="{{ route('flujo.tasks.show', $task->id) }}" class="accion ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="accion editar"><i class="fas fa-pencil-alt"></i></a>
                                        <form action="{{ route('flujo.tasks.destroy', $task->id) }}" method="POST" class="inline-form" onsubmit="return confirm('¿Confirmas la eliminación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="accion eliminar"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginacion">{{ $tasks->links() }}</div>
                    </div>
                </div>
            </div>
<hr>
            {{-- ================= Comments Card ================= --}}
            <div class="card">
                <h2 class="card-title"><i class="fas fa-comments card-icon"></i> Novedades y Comentarios</h2>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="tabla-elementos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ID T</th>
                                    <th>Tarea</th>
                                    <th>Comentario</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ $comment->task_id }}</td>
                                    <td>{{ $comment->task->titulo ?? 'N/A' }}</td>
                                    <td>{{ $comment->comentario }}</td>
                                    <td>{{ $comment->user->name ?? 'N/A' }}</td>
                                    <td>{{ $comment->created_at ? $comment->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginacion">{{ $comments->links() }}</div>
                    </div>
                </div>
            </div>
<hr>
            {{-- ================= Histories Card ================= --}}
            <div class="card">
                <h2 class="card-title"><i class="fas fa-history card-icon"></i> Auditoría de Tareas</h2>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="tabla-elementos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tarea</th>
                                    <th>Nuevo Estado</th>
                                    <th>Registrado Por</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($histories as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>{{ $history->task->titulo ?? 'N/A' }}</td>
                                    <td><span class="estado estado-{{ $history->estado_nuevo }}">{{ $history->estado_nuevo ?? '-' }}</span></td>
                                    <td>{{ $history->user->name ?? 'N/A' }}</td>
                                    <td>{{ $history->fecha_cambio ? \Carbon\Carbon::parse($history->fecha_cambio)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginacion">{{ $histories->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ===================== GRID 2x2 ===================== */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        @media (max-width: 1000px) {
            .card-grid { grid-template-columns: 1fr; }
        }

        /* ===================== TARJETAS ===================== */
        .card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08), 0 0 0 1px #cddbe7;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15), 0 0 0 1px #cddbe7;
        }

        .card-title { font-size: 1.6em; color: #005691; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #cddbe7; display: flex; align-items: center; font-weight: 600; }
        .card-icon { font-size: 1.2em; margin-right: 12px; color: #007bff; }

        /* ===================== TABLAS ===================== */
        .table-responsive { overflow-x: auto; overflow-y: hidden; border: 1px solid #cddbe7; border-radius: 15px; background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .tabla-elementos { width: 100%; border-collapse: collapse; font-size: 0.9em; table-layout: auto; min-width: 700px; }
        .tabla-elementos th, .tabla-elementos td { padding: 12px 15px; border-bottom: 1px solid #cddbe7; text-align: left; white-space: nowrap; }
        .tabla-elementos thead th { background-color: #f0f5fa; color: #005691; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; }
        .tabla-elementos tbody tr:hover { background-color: #f7fbff; }

        /* ===================== ACCIONES ===================== */
        .acciones { display: flex; gap: 6px; justify-content: flex-start; }
        .acciones .accion { display: flex; align-items: center; justify-content: center; border-radius: 50%; width: 34px; height: 34px; font-size: 1.05em; transition: all 0.2s; color: #6c757d; border: 1px solid transparent; text-decoration: none; }
        .acciones .accion:hover { transform: translateY(-2px) scale(1.05); background-color: #f0f5fa; border-color: #cddbe7; }
        .acciones .ver:hover { color: #005691; }
        .acciones .editar:hover { color: #ffc107; }
        .acciones .eliminar { background: none; border: none; color: #dc3545; cursor: pointer; }
        .acciones .eliminar:hover { background-color: #f8d7da; color: #c82333; }

        /* ===================== ESTADOS ===================== */
        .estado { display: inline-block; padding: 5px 10px; border-radius: 12px; font-weight: 500; font-size: 0.85em; text-transform: capitalize; border: 1px solid; white-space: nowrap; }
        .estado-pendiente { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        .estado-en_progreso { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .estado-completado { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .estado-cancelado { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }

        /* ===================== PAGINACION ===================== */
        .paginacion { margin-top: 15px; display: flex; justify-content: center; flex-wrap: wrap; gap: 5px; }
        .paginacion .page-link { padding: 6px 12px; margin: 0 3px; border-radius: 5px; border: 1px solid #cddbe7; color: #005691; text-decoration: none; }
        .paginacion .page-link:hover { background-color: #e6f0f7; }
        .paginacion .active .page-link { background-color: #005691; color: #fff; border-color: #005691; }
    </style>
</x-base-layout>
