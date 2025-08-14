<form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="mt-4">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- Grupo de campos con disposición vertical para máxima legibilidad --}}
    <div class="d-flex flex-column gap-3">

        {{-- Campo Empleado con Floating Label --}}
        <div class="form-floating">
            <select name="empleado_id" id="empleado_id" class="form-select" required>
                <option value="" disabled selected>Abre para seleccionar...</option>
                @foreach($empleados as $empleado)
                    <option value="{{ $empleado->cedula }}" 
                        {{ old('empleado_id', $doc->empleado_id ?? '') == $empleado->cedula ? 'selected' : '' }}>
                        {{ $empleado->nombre1 }} {{ $empleado->nombre2 }} {{ $empleado->apellido1 }} {{ $empleado->apellido2 }}
                    </option>
                @endforeach
            </select>
            <label for="empleado_id">Empleado</label>
        </div>

        {{-- Campo Tipo de Documento con Floating Label --}}
        <div class="form-floating">
            <select name="tipo_documento_id" id="tipo_documento_id" class="form-select" required>
                <option value="" disabled selected>Abre para seleccionar...</option>
                @foreach($tiposDocumento as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_documento_id', $doc->tipo_documento_id ?? '') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
            <label for="tipo_documento_id">Tipo de Documento</label>
        </div>
        
        {{-- Campo Fecha con Floating Label --}}
        <div class="form-floating">
            <input type="date" name="fecha_subida" id="fecha_subida" class="form-control" placeholder="Fecha de Subida"
                   value="{{ old('fecha_subida', isset($doc->fecha_subida) ? optional($doc->fecha_subida)->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
            <label for="fecha_subida">Fecha de Subida</label>
        </div>

        {{-- Campo de Archivo: Un diseño integrado y limpio --}}
        <div class="form-floating">
            <input type="text" id="archivo-display" class="form-control" placeholder="Archivo" readonly style="cursor: pointer;">
            <label for="archivo-display">Archivo</label>
            {{-- El input real está oculto, pero hace todo el trabajo --}}
            <input type="file" name="archivo" id="archivo-real" class="visually-hidden" {{ $method === 'POST' ? 'required' : '' }}>
        </div>

        {{-- Información del archivo actual, sutil y contextual --}}
        @if($method !== 'POST' && !empty($doc->ruta_archivo))
            <div class="text-center text-muted mt-n2">
                <small>
                    <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="text-decoration-none">
                       <i class="bi bi-paperclip"></i> Ver archivo actual
                    </a>
                </small>
            </div>
        @endif
    </div>

    {{-- Separador y botones de acción --}}
    <hr class="my-4">
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('archivo.gdodocsempleados.index') }}" class="btn btn-light rounded-pill px-4 py-2">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">
            <i class="bi bi-check-lg me-1"></i> {{ $method === 'POST' ? 'Guardar' : 'Actualizar' }}
        </button>
    </div>
</form>