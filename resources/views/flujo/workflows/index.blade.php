<x-base-layout>
    <div class="container">
        <h1 class="titulo-principal">Listado de Workflows</h1>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="mensaje-exito">{{ session('success') }}</div>
        @endif

        {{-- Toolbar: botones a la izquierda, buscador a la derecha --}}
        <div class="toolbar">
            {{-- Botones --}}
            <div class="botones">
                <a href="{{ route('flujo.workflows.create') }}" class="btn btn-crear">
                    <i class="fas fa-plus"></i> Crear Workflow
                </a>
                <a href="{{ route('flujo.tablero') }}" class="btn btn-tablero">
                    <i class="fas fa-th-large"></i> Volver a Tablero
                </a>
            </div>

            {{-- Buscador --}}
            <form action="{{ route('flujo.workflows.index') }}" method="GET" class="buscador">
                <input type="text" name="search" placeholder="Buscar por nombre o creador"
                       value="{{ request('search') }}" />
                <button type="submit" class="btn btn-buscar">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="tabla-container">
            <table class="tabla-tareas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Creador</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workflows as $workflow)
                        <tr>
                            <td>{{ $workflow->id }}</td>
                            <td>{{ $workflow->nombre }}</td>
                            <td>{{ $workflow->creator?->name ?? 'N/A' }}</td>
                            <td>
                                <div class="acciones">
                                    <a href="{{ route('flujo.workflows.show', $workflow) }}" class="accion ver">Ver</a>
                                    <a href="{{ route('flujo.workflows.edit', $workflow) }}" class="accion editar">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sin-tareas">No hay workflows registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="paginador">
            {{ $workflows->links() }}
        </div>
    </div>

    {{-- ESTILOS --}}
    <style>
        .container { max-width: 1200px; margin: auto; padding: 20px; font-family: sans-serif; }
        .titulo-principal { font-size: 24px; color: #1E3A8A; margin-bottom: 20px; }
        .mensaje-exito { color: #059669; margin-bottom: 15px; }

        /* Toolbar */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .botones { display: flex; gap: 10px; flex-shrink: 0; }
        .buscador { display: flex; align-items: center; gap: 5px; flex-shrink: 0; min-width: 200px; }

        /* Buscador */
        .buscador input { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; width: 200px; }
        .buscador button { padding: 6px 12px; border-radius: 4px; background-color: #2563EB; color: #fff; border: none; cursor: pointer; }
        .buscador button:hover { background-color: #3B82F6; }

        /* Botones */
        .btn { padding: 6px 12px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
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

        /* Acciones */
        .acciones a { margin-right: 5px; text-decoration: none; color: #2563EB; }
        .acciones a:hover { transform: scale(1.1); transition: 0.2s; }
        .acciones .editar { color: #1D4ED8; }

        /* Mensaje sin workflows */
        .sin-tareas { text-align: center; color: #6B7280; font-style: italic; }

        /* Paginador */
        .paginador { margin-top: 20px; text-align: center; }
        .paginador li { display: inline-block; margin-right: 5px; }
        .paginador li a, .paginador li span { padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc; text-decoration: none; color: #1E3A8A; }
        .paginador li a:hover { background-color: #BFDBFE; border-color: #3B82F6; color: #1E40AF; }
        .paginador li.active span { background-color: #3B82F6; color: white; border-color: #3B82F6; }
    </style>
</x-base-layout>
