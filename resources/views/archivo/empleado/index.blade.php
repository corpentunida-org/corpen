<x-base-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Listado de Empleados</h4>
        <a href="{{ route('archivo.empleado.create') }}" class="btn btn-success">Nuevo Empleado</a>
    </div>

    <form method="GET" action="{{ route('archivo.empleado.index') }}" class="mb-3">
        <div class="input-group">
            <input type="search" name="search" class="form-control" placeholder="Buscar por cédula o nombre..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    @if ($empleados->count())
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->cedula }}</td>
                    <td>{{ $empleado->apellido1 }} {{ $empleado->apellido2 }} {{ $empleado->nombre1 }} {{ $empleado->nombre2 }}</td>
                    <td>{{ $empleado->correo_personal }}</td>
                    <td>{{ $empleado->celular_personal }}</td>
                    <td>
                        <a href="{{ route('archivo.empleado.show', $empleado->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('archivo.empleado.edit', $empleado->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('archivo.empleado.destroy', $empleado->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este empleado?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $empleados->links() }}

    @else
        <p>No se encontraron empleados.</p>
    @endif
</x-base-layout>
