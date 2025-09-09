<x-base-layout>
    <div class="container">
        <h1 class="title">Registrar Nueva Tarea</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> Por favor corrige los errores del formulario.
            </div>
        @endif

        <form action="{{ route('flujo.tasks.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="titulo">Título de la Tarea</label>
                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" class="form-control">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-group">
                <label for="workflow_id">Workflow</label>
                <select name="workflow_id" id="workflow_id" class="form-control">
                    <option value="">Seleccione Workflow</option>
                    @foreach($workflows as $workflow)
                        <option value="{{ $workflow->id }}" {{ old('workflow_id') == $workflow->id ? 'selected' : '' }}>
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
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en-proceso" {{ old('estado') == 'en-proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="revisado" {{ old('estado') == 'revisado' ? 'selected' : '' }}>Revisado</option>
                    <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completada</option>
                </select>
            </div>

            <div class="form-group">
                <label for="prioridad">Prioridad</label>
                <select name="prioridad" id="prioridad" class="form-control">
                    <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>

            <button type="submit" class="btn-create btn-create-action">
                <i class="fas fa-plus"></i> Guardar Tarea
            </button>
            <a href="{{ route('flujo.tablero') }}" class="btn-clear">Cancelar</a>
        </form>
    </div>
</x-base-layout>
