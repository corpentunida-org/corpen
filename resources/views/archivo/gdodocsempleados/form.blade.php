<form action="{{ $route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label for="empleado_id" class="form-label">Empleado</label>
        <select name="empleado_id" id="empleado_id" class="form-control">
            <option value="">Seleccione un empleado</option>
            @foreach($empleados as $empleado)
                <option value="{{ $empleado->cedula }}" 
                    {{ old('empleado_id', $doc->empleado_id ?? '') == $empleado->cedula ? 'selected' : '' }}>
                    {{ $empleado->nombre1 }} {{ $empleado->nombre2 }} {{ $empleado->apellido1 }} {{ $empleado->apellido2 }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="tipo_documento_id" class="form-label">Tipo Documento</label>
        <select name="tipo_documento_id" id="tipo_documento_id" class="form-control" required>
            <option value="">Seleccione un tipo</option>
            @foreach($tiposDocumento as $tipo)
                <option value="{{ $tipo->id }}" {{ old('tipo_documento_id', $doc->tipo_documento_id ?? '') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="archivo" class="form-label">Archivo</label>
        <input type="file" name="archivo" id="archivo" class="form-control">
        @if(!empty($doc->ruta_archivo))
            <p>Archivo actual: <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank">Ver archivo</a></p>
        @endif
    </div>

    <div class="mb-3">
        <label for="fecha_subida" class="form-label">Fecha de Subida</label>
        <input type="date" name="fecha_subida" id="fecha_subida" class="form-control"
            value="{{ old('fecha_subida', isset($doc->fecha_subida) ? $doc->fecha_subida->format('Y-m-d') : '') }}">

    </div>

    <button type="submit" class="btn btn-primary">{{ $method === 'POST' ? 'Guardar' : 'Actualizar' }}</button>
    <a href="{{ route('archivo.gdodocsempleados.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
