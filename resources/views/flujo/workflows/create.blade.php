<x-base-layout>
    <div class="container">
        <h1>Crear Nuevo Workflow</h1>

        @if ($errors->any())
            <div style="color:red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('flujo.workflows.store') }}" method="POST">
            @csrf

            <label>Nombre:</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}"><br><br>

            <label>Descripción:</label>
            <textarea name="descripcion">{{ old('descripcion') }}</textarea><br><br>

            <label>Creador:</label>
            <select name="creado_por">
                <option value="">-- Seleccione --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('creado_por') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select><br><br>

            <button type="submit">Guardar</button>
            <a href="{{ route('flujo.workflows.index') }}">Cancelar</a>
        </form>
    </div>
</x-base-layout>
