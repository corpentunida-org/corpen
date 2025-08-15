@php
    // Definimos si estamos en modo de edición para usarlo fácilmente en el formulario.
    $isEdit = isset($empleado->id);
@endphp

{{-- IMPORTANTE: El atributo enctype es fundamental para que la subida de archivos funcione. --}}
<form action="{{ $isEdit ? route('archivo.empleado.update', $empleado->id) : route('archivo.empleado.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Sección 1: Información Personal --}}
    <fieldset class="mb-4">
        <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
            <i class="bi bi-person-badge text-primary me-2"></i> Información Personal
        </legend>

        {{-- NUEVO: Sección para la foto de perfil --}}
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-8">
                <label for="ubicacion_foto" class="form-label">Foto de Perfil (Opcional)</label>
                <input class="form-control @error('ubicacion_foto') is-invalid @enderror" type="file" id="ubicacion_foto" name="ubicacion_foto" accept="image/*">
                @error('ubicacion_foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Archivos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
            </div>
        {{-- Vista previa de la foto actual en modo edición --}}
        @if ($isEdit && $empleado->ubicacion_foto)
            <div class="col-md-4 text-center">
                {{-- LÍNEA CORREGIDA --}}
                <img src="{{ route('archivo.empleado.verFoto', $empleado->id) }}" alt="Foto actual" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                <small class="d-block text-muted mt-1">Foto Actual</small>
            </div>
        @endif
        </div>

        {{-- Campos de Cédula y Nombres --}}
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" placeholder="Cédula" value="{{ old('cedula', $empleado->cedula ?? '') }}" required {{ $isEdit ? 'readonly' : '' }}>
                    <label for="cedula">Cédula</label>
                    @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="nombre1" id="nombre1" class="form-control @error('nombre1') is-invalid @enderror" placeholder="Primer Nombre" value="{{ old('nombre1', $empleado->nombre1 ?? '') }}" required>
                    <label for="nombre1">Primer Nombre</label>
                    @error('nombre1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="nombre2" id="nombre2" class="form-control" placeholder="Segundo Nombre" value="{{ old('nombre2', $empleado->nombre2 ?? '') }}">
                    <label for="nombre2">Segundo Nombre</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="apellido1" id="apellido1" class="form-control @error('apellido1') is-invalid @enderror" placeholder="Primer Apellido" value="{{ old('apellido1', $empleado->apellido1 ?? '') }}" required>
                    <label for="apellido1">Primer Apellido</label>
                    @error('apellido1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="apellido2" id="apellido2" class="form-control" placeholder="Segundo Apellido" value="{{ old('apellido2', $empleado->apellido2 ?? '') }}">
                    <label for="apellido2">Segundo Apellido</label>
                </div>
            </div>
        </div>
    </fieldset>

    {{-- Sección 2: Datos Demográficos --}}
    <fieldset class="mb-4">
        <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
            <i class="bi bi-calendar-heart text-info me-2"></i> Datos Demográficos
        </legend>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="date" name="nacimiento" id="nacimiento" class="form-control @error('nacimiento') is-invalid @enderror" placeholder="Fecha de Nacimiento" value="{{ old('nacimiento', optional($empleado->nacimiento ?? null)->format('Y-m-d')) }}">
                    <label for="nacimiento">Fecha de Nacimiento</label>
                    @error('nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="lugar" id="lugar" class="form-control" placeholder="Lugar de Nacimiento" value="{{ old('lugar', $empleado->lugar ?? '') }}">
                    <label for="lugar">Lugar de Nacimiento</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                        <option value="" selected>Seleccione...</option>
                        <option value="M" {{ old('sexo', $empleado->sexo ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $empleado->sexo ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                    <label for="sexo">Sexo</label>
                    @error('sexo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </fieldset>

    {{-- Sección 3: Información de Contacto --}}
    <fieldset>
        <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
            <i class="bi bi-telephone-plus text-success me-2"></i> Información de Contacto
        </legend>
        <div class="row g-3">
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" name="correo_personal" id="correo_personal" class="form-control @error('correo_personal') is-invalid @enderror" placeholder="Correo Personal" value="{{ old('correo_personal', $empleado->correo_personal ?? '') }}">
                    <label for="correo_personal">Correo Personal</label>
                    @error('correo_personal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="celular_personal" id="celular_personal" class="form-control" placeholder="Celular Personal" value="{{ old('celular_personal', $empleado->celular_personal ?? '') }}">
                    <label for="celular_personal">Celular Personal</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="celular_acudiente" id="celular_acudiente" class="form-control" placeholder="Celular Acudiente" value="{{ old('celular_acudiente', $empleado->celular_acudiente ?? '') }}">
                    <label for="celular_acudiente">Celular Acudiente</label>
                </div>
            </div>
        </div>
    </fieldset>

    {{-- Separador y botones de acción --}}
    <hr class="my-4">
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('archivo.empleado.index') }}" class="btn btn-light rounded-pill px-4 py-2">Cancelar</a>
        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift">
            <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Actualizar' : 'Guardar' }} Empleado
        </button>
    </div>
</form>