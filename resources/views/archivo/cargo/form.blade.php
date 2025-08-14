{{-- 
    Este formulario está organizado en secciones temáticas usando fieldsets.
    Cada sección tiene un encabezado claro (legend) para guiar al usuario.
    Se utiliza una mezcla de columnas para optimizar el espacio sin sacrificar la legibilidad.
--}}

{{-- Sección 1: Identificación del Cargo --}}
<fieldset class="mb-4">
    <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
        <i class="bi bi-briefcase text-primary me-2"></i> Identificación del Cargo
    </legend>
    <div class="row g-3">
        <div class="col-md-9">
            <div class="form-floating">
                <input type="text" name="nombre_cargo" id="nombre_cargo" class="form-control" placeholder="Nombre del cargo" value="{{ old('nombre_cargo', $cargo->nombre_cargo ?? '') }}" required>
                <label for="nombre_cargo">Nombre del Cargo</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <select name="estado" id="estado" class="form-select">
                    <option value="1" {{ old('estado', $cargo->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $cargo->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                <label for="estado">Estado</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select name="GDO_area_id" id="GDO_area_id" class="form-select" required>
                    <option value="" disabled selected>Selecciona un área...</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('GDO_area_id', $cargo->GDO_area_id ?? '') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
                <label for="GDO_area_id">Área Funcional</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select name="GDO_empleados_cedula" id="GDO_empleados_cedula" class="form-select">
                    <option value="">(Ningún empleado asignado)</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->cedula }}" {{ old('GDO_empleados_cedula', $cargo->GDO_empleados_cedula ?? '') == $empleado->cedula ? 'selected' : '' }}>{{ $empleado->nombre_completo }}</option>
                    @endforeach
                </select>
                <label for="GDO_empleados_cedula">Empleado Asignado</label>
            </div>
        </div>
    </div>
</fieldset>

{{-- Sección 2: Detalles Salariales y Jornada --}}
<fieldset class="mb-4">
    <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
        <i class="bi bi-cash-coin text-success me-2"></i> Detalles Salariales y Jornada
    </legend>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="number" step="0.01" name="salario_base" id="salario_base" class="form-control" placeholder="Salario base" value="{{ old('salario_base', $cargo->salario_base ?? '') }}">
                <label for="salario_base">Salario Base ($)</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" name="jornada" id="jornada" class="form-control" placeholder="Jornada" value="{{ old('jornada', $cargo->jornada ?? '') }}">
                <label for="jornada">Jornada</label>
            </div>
        </div>
    </div>
</fieldset>

{{-- Sección 3: Información de Contacto --}}
<fieldset class="mb-4">
    <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
        <i class="bi bi-telephone text-info me-2"></i> Información de Contacto
    </legend>
    <div class="row g-3">
        <div class="col-md-4"><div class="form-floating"><input type="text" name="telefono_corporativo" id="telefono_corporativo" class="form-control" placeholder="Teléfono" value="{{ old('telefono_corporativo', $cargo->telefono_corporativo ?? '') }}"><label for="telefono_corporativo">Teléfono</label></div></div>
        <div class="col-md-4"><div class="form-floating"><input type="text" name="celular_corporativo" id="celular_corporativo" class="form-control" placeholder="Celular" value="{{ old('celular_corporativo', $cargo->celular_corporativo ?? '') }}"><label for="celular_corporativo">Celular</label></div></div>
        <div class="col-md-4"><div class="form-floating"><input type="text" name="ext_corporativo" id="ext_corporativo" class="form-control" placeholder="Extensión" value="{{ old('ext_corporativo', $cargo->ext_corporativo ?? '') }}"><label for="ext_corporativo">Extensión</label></div></div>
        <div class="col-md-6"><div class="form-floating"><input type="email" name="correo_corporativo" id="correo_corporativo" class="form-control" placeholder="Correo" value="{{ old('correo_corporativo', $cargo->correo_corporativo ?? '') }}"><label for="correo_corporativo">Correo Corporativo</label></div></div>
        <div class="col-md-6"><div class="form-floating"><input type="email" name="gmail_corporativo" id="gmail_corporativo" class="form-control" placeholder="Gmail" value="{{ old('gmail_corporativo', $cargo->gmail_corporativo ?? '') }}"><label for="gmail_corporativo">Gmail Corporativo</label></div></div>
    </div>
</fieldset>

{{-- Sección 4: Documentación y Observaciones --}}
<fieldset>
    <legend class="fs-6 fw-bold border-bottom pb-2 mb-3">
        <i class="bi bi-journal-text text-secondary me-2"></i> Documentación y Observaciones
    </legend>
    <div class="row g-3">
        <div class="col-md-12">
            <label for="manual_funciones" class="form-label">Manual de funciones (PDF)</label>
            <input type="file" name="manual_funciones" id="manual_funciones" class="form-control" accept=".pdf">
            @if (isset($cargo) && $cargo->manual_funciones)
                <small class="text-muted mt-1 d-block">Archivo actual: 
                    <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Ver Manual</a>
                </small>
            @endif
        </div>
        <div class="col-md-12">
            <div class="form-floating">
                <textarea name="observacion" id="observacion" class="form-control" placeholder="Observaciones" style="height: 100px">{{ old('observacion', $cargo->observacion ?? '') }}</textarea>
                <label for="observacion">Observaciones</label>
            </div>
        </div>
    </div>
</fieldset>