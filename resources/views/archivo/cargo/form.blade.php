<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre_cargo" class="form-label">Nombre del cargo</label>
        <input type="text" name="nombre_cargo" id="nombre_cargo" class="form-control"
               value="{{ old('nombre_cargo', $cargo->nombre_cargo ?? '') }}">
    </div>

    <div class="col-md-3">
        <label for="salario_base" class="form-label">Salario base</label>
        <input type="number" step="0.01" name="salario_base" id="salario_base" class="form-control"
               value="{{ old('salario_base', $cargo->salario_base ?? '') }}">
    </div>

    <div class="col-md-3">
        <label for="jornada" class="form-label">Jornada</label>
        <input type="text" name="jornada" id="jornada" class="form-control"
               value="{{ old('jornada', $cargo->jornada ?? '') }}">
    </div>

    <div class="col-md-4">
        <label for="telefono_corporativo" class="form-label">Teléfono corporativo</label>
        <input type="text" name="telefono_corporativo" id="telefono_corporativo" class="form-control"
               value="{{ old('telefono_corporativo', $cargo->telefono_corporativo ?? '') }}">
    </div>

    <div class="col-md-4">
        <label for="celular_corporativo" class="form-label">Celular corporativo</label>
        <input type="text" name="celular_corporativo" id="celular_corporativo" class="form-control"
               value="{{ old('celular_corporativo', $cargo->celular_corporativo ?? '') }}">
    </div>

    <div class="col-md-4">
        <label for="ext_corporativo" class="form-label">Extensión</label>
        <input type="text" name="ext_corporativo" id="ext_corporativo" class="form-control"
               value="{{ old('ext_corporativo', $cargo->ext_corporativo ?? '') }}">
    </div>

    <div class="col-md-6">
        <label for="correo_corporativo" class="form-label">Correo corporativo</label>
        <input type="email" name="correo_corporativo" id="correo_corporativo" class="form-control"
               value="{{ old('correo_corporativo', $cargo->correo_corporativo ?? '') }}">
    </div>

    <div class="col-md-6">
        <label for="gmail_corporativo" class="form-label">Gmail corporativo</label>
        <input type="email" name="gmail_corporativo" id="gmail_corporativo" class="form-control"
               value="{{ old('gmail_corporativo', $cargo->gmail_corporativo ?? '') }}">
    </div>

    <div class="col-md-6">
        <label for="manual_funciones" class="form-label">Manual de funciones (PDF)</label>
        <input type="text" name="manual_funciones" id="manual_funciones" class="form-control"
               value="{{ old('manual_funciones', $cargo->manual_funciones ?? '') }}">
    </div>

    <div class="col-md-6">
        <label for="empleado_cedula" class="form-label">Cédula del empleado</label>
        <input type="text" name="empleado_cedula" id="empleado_cedula" class="form-control"
               value="{{ old('empleado_cedula', $cargo->empleado_cedula ?? '') }}">
    </div>

    <div class="col-md-3">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="1" {{ old('estado', $cargo->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('estado', $cargo->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>

    <div class="col-md-9">
        <label for="observacion" class="form-label">Observaciones</label>
        <textarea name="observacion" id="observacion" class="form-control" rows="3">{{ old('observacion', $cargo->observacion ?? '') }}</textarea>
    </div>
</div>
