<x-base-layout>
    <div class="container">
        <h1 class="titulo-principal">Listado de Tareas</h1>

        {{-- Contenedor de botones y buscador --}}
        <div class="toolbar">
            {{-- Botones a la izquierda --}}
            <div class="botones">
                <a href="{{ route('flujo.tasks.create') }}" class="btn btn-crear">
                    <i class="fas fa-plus"></i> Crear
                </a>
                <a href="{{ route('flujo.tablero') }}" class="btn btn-tablero">
                    <i class="fas fa-th-large"></i> Volver a Tablero
                </a>
            </div>

            {{-- Buscador a la derecha --}}
            <form action="{{ route('flujo.tasks.index') }}" method="GET" class="buscador">
                <input type="text" name="search" placeholder="Buscar por título o descripción"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-buscar">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>

        {{-- Tabla estilo Excel con scroll --}}
        <div class="tabla-container">
            <table class="tabla-tareas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Responsable</th>
                        <th>Workflow</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->titulo }}</td>
                            <td>{{ $task->descripcion }}</td>
                            <td>
                                @php
                                    $estadoClass = match(strtolower($task->estado)) {
                                        'pendiente' => 'estado-pendiente',
                                        'en progreso' => 'estado-progreso',
                                        'completada' => 'estado-completada',
                                        'revisado' => 'estado-revisado',
                                        default => 'estado-default',
                                    };
                                @endphp
                                <span class="estado {{ $estadoClass }}">{{ ucfirst($task->estado) }}</span>
                            </td>
                            <td>{{ ucfirst($task->prioridad) }}</td>
                            <td>{{ $task->user->name ?? 'N/A' }}</td>
                            <td>{{ $task->workflow->nombre ?? 'Sin Workflow' }}</td>
                            <td>
                                <div class="acciones">
                                    <a href="{{ route('flujo.tasks.show', $task->id) }}" class="accion ver" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('flujo.tasks.edit', $task->id) }}" class="accion editar" title="Editar"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="sin-tareas">No se encontraron tareas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginador --}}
        <div class="paginador">
            {{ $tasks->links() }}
        </div>
    </div>

    {{-- ESTILOS --}}
    <style>
        .container { max-width: 1200px; margin: auto; padding: 20px; font-family: sans-serif; }
        .titulo-principal { color: #1E3A8A; font-size: 24px; margin-bottom: 20px; }

        /* Toolbar */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
        .botones { display: flex; gap: 10px; flex-shrink: 0; }
        .buscador { display: flex; align-items: center; flex-shrink: 0; }
        .buscador input { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; margin-right: 5px; width: 200px; max-width: 100%; }
        .buscador button { padding: 6px 12px; border-radius: 4px; border: none; background-color: #2563EB; color: white; cursor: pointer; }
        .buscador button:hover { background-color: #3B82F6; }

        /* Botones */
        .btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .btn-crear { background-color: #3B82F6; color: #fff; }
        .btn-crear:hover { background-color: #60A5FA; }
        .btn-tablero { background-color: #9CA3AF; color: #1E3A8A; }
        .btn-tablero:hover { background-color: #B0B7BF; }

        /* Tabla */
        .tabla-container { overflow-x: auto; border: 1px solid #ccc; border-radius: 4px; }
        .tabla-tareas { width: 100%; border-collapse: collapse; font-size: 14px; }
        .tabla-tareas th, .tabla-tareas td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        .tabla-tareas thead { background-color: #E5E7EB; color: #1E3A8A; }
        .tabla-tareas tbody tr:hover { background-color: #BFDBFE; }

        /* Estados */
        .estado { padding: 2px 6px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .estado-pendiente { background-color: #FEF3C7; color: #B45309; }
        .estado-progreso { background-color: #DBEAFE; color: #1E40AF; }
        .estado-completada { background-color: #D1D5DB; color: #111827; }
        .estado-revisado { background-color: #93C5FD; color: #1E3A8A; }
        .estado-default { background-color: #F3F4F6; color: #4B5563; }

        /* Acciones */
        .acciones a { margin-right: 5px; color: #2563EB; text-decoration: none; }
        .acciones a:hover { transform: scale(1.2); }

        /* Mensaje sin tareas */
        .sin-tareas { text-align: center; color: #6B7280; font-style: italic; }

        /* Paginador */
        .paginador { margin-top: 20px; text-align: center; }
        .paginador li { display: inline-block; margin-right: 5px; }
        .paginador li a, .paginador li span { padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc; text-decoration: none; color: #1E3A8A; }
        .paginador li a:hover { background-color: #BFDBFE; border-color: #3B82F6; color: #1E40AF; }
        .paginador li.active span { background-color: #3B82F6; color: white; border-color: #3B82F6; }
    </style>
</x-base-layout>
