@php
    $isEdit = isset($empleado);
@endphp

<form action="{{ $isEdit ? route('archivo.empleado.update', $empleado->id) : route('archivo.empleado.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="cedula" class="form-label">Cédula</label>
        <input type="text" 
               class="form-control @error('cedula') is-invalid @enderror" 
               id="cedula" 
               name="cedula" 
               value="{{ old('cedula', $empleado->cedula ?? '') }}" 
               required>
        @error('cedula')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="apellido1" class="form-label">Primer Apellido</label>
        <input type="text" 
               class="form-control @error('apellido1') is-invalid @enderror" 
               id="apellido1" 
               name="apellido1" 
               value="{{ old('apellido1', $empleado->apellido1 ?? '') }}" 
               required>
        @error('apellido1')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="apellido2" class="form-label">Segundo Apellido</label>
        <input type="text" 
               class="form-control @error('apellido2') is-invalid @enderror" 
               id="apellido2" 
               name="apellido2" 
               value="{{ old('apellido2', $empleado->apellido2 ?? '') }}">
        @error('apellido2')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="nombre1" class="form-label">Primer Nombre</label>
        <input type="text" 
               class="form-control @error('nombre1') is-invalid @enderror" 
               id="nombre1" 
               name="nombre1" 
               value="{{ old('nombre1', $empleado->nombre1 ?? '') }}" 
               required>
        @error('nombre1')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="nombre2" class="form-label">Segundo Nombre</label>
        <input type="text" 
               class="form-control @error('nombre2') is-invalid @enderror" 
               id="nombre2" 
               name="nombre2" 
               value="{{ old('nombre2', $empleado->nombre2 ?? '') }}">
        @error('nombre2')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="nacimiento" class="form-label">Fecha de Nacimiento</label>
        <input type="date" 
               class="form-control @error('nacimiento') is-invalid @enderror" 
               id="nacimiento" 
               name="nacimiento" 
               value="{{ old('nacimiento', optional($empleado)->nacimiento instanceof \Illuminate\Support\Carbon ? $empleado->nacimiento->format('Y-m-d') : '') }}"
        @error('nacimiento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="lugar" class="form-label">Lugar</label>
        <input type="text" 
               class="form-control @error('lugar') is-invalid @enderror" 
               id="lugar" 
               name="lugar" 
               value="{{ old('lugar', $empleado->lugar ?? '') }}">
        @error('lugar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="sexo" class="form-label">Sexo</label>
        <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
            <option value="" {{ old('sexo', $empleado->sexo ?? '') == '' ? 'selected' : '' }}>Seleccione</option>
            <option value="M" {{ old('sexo', $empleado->sexo ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', $empleado->sexo ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
        </select>
        @error('sexo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="correo_personal" class="form-label">Correo Personal</label>
        <input type="email" 
               class="form-control @error('correo_personal') is-invalid @enderror" 
               id="correo_personal" 
               name="correo_personal" 
               value="{{ old('correo_personal', $empleado->correo_personal ?? '') }}">
        @error('correo_personal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="celular_personal" class="form-label">Celular Personal</label>
        <input type="text" 
               class="form-control @error('celular_personal') is-invalid @enderror" 
               id="celular_personal" 
               name="celular_personal" 
               value="{{ old('celular_personal', $empleado->celular_personal ?? '') }}">
        @error('celular_personal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="celular_acudiente" class="form-label">Celular Acudiente</label>
        <input type="text" 
               class="form-control @error('celular_acudiente') is-invalid @enderror" 
               id="celular_acudiente" 
               name="celular_acudiente" 
               value="{{ old('celular_acudiente', $empleado->celular_acudiente ?? '') }}">
        @error('celular_acudiente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-{{ $isEdit ? 'primary' : 'success' }}">
        {{ $isEdit ? 'Actualizar' : 'Guardar' }}
    </button>

    <a href="{{ route('archivo.empleado.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
</form>
