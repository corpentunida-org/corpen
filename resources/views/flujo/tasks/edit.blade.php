<x-base-layout>
    <div class="container">
        <h1 class="title">Editar Tarea</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> Por favor corrige los errores del formulario.
            </div>
        @endif

        {{-- FORMULARIO DE TAREA --}}
        <form action="{{ route('flujo.tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="titulo">Título de la Tarea</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $task->titulo) }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion', $task->descripcion) }}</textarea>
            </div>

            <div class="form-group">
                <label for="workflow_id">Workflow</label>
                <select name="workflow_id" id="workflow_id" class="form-control">
                    <option value="">Seleccione Workflow</option>
                    @foreach($workflows as $workflow)
                        <option value="{{ $workflow->id }}" {{ (old('workflow_id', $task->workflow_id) == $workflow->id) ? 'selected' : '' }}>
                            {{ $workflow->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">Responsable</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Seleccione Responsable</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (old('user_id', $task->user_id) == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="pendiente" {{ (old('estado', $task->estado) == 'pendiente') ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ (old('estado', $task->estado) == 'en_proceso') ? 'selected' : '' }}>En Proceso</option>
                    <option value="revisado" {{ (old('estado', $task->estado) == 'revisado') ? 'selected' : '' }}>Revisado</option>
                    <option value="completado" {{ (old('estado', $task->estado) == 'completado') ? 'selected' : '' }}>Completada</option>
                </select>
            </div>

            <div class="form-group">
                <label for="prioridad">Prioridad</label>
                <select name="prioridad" id="prioridad" class="form-control">
                    <option value="baja" {{ (old('prioridad', $task->prioridad) == 'baja') ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ (old('prioridad', $task->prioridad) == 'media') ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ (old('prioridad', $task->prioridad) == 'alta') ? 'selected' : '' }}>Alta</option>
                </select>
            </div>

            <button type="submit" class="btn-edit btn-edit-action">
                <i class="fas fa-edit"></i> Actualizar Tarea
            </button>
            <a href="{{ route('flujo.tablero') }}" class="btn-clear">Cancelar</a>
        </form>

        <hr>

        {{-- FORMULARIO DE COMENTARIOS --}}
        <h2 class="subtitle">Comentarios</h2>

        <form action="{{ route('flujo.comments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <div class="form-group">
                <label for="comentario">Agregar comentario</label>
                <textarea name="comentario" id="comentario" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn-create">💬 Guardar Comentario</button>
        </form>

        {{-- LISTADO DE COMENTARIOS --}}
        <h3 class="subtitle mt-4">Comentarios existentes</h3>
        @if($task->comments->isEmpty())
            <p>No hay comentarios aún.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Comentario</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task->comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->user->name ?? 'N/A' }}</td>
                            <td>{{ $comment->comentario }}</td>
                            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-base-layout>
