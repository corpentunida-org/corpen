<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre del área</label>
        <input type="text" name="nombre" id="nombre" class="form-control"
               value="{{ old('nombre', $area->nombre ?? '') }}">
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-select">
            <option value="activo" {{ old('estado', $area->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado', $area->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>

    <div class="col-md-12">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label for="GDO_cargo_id" class="form-label">Jefe del Área</label>
        <select name="GDO_cargo_id" id="GDO_cargo_id" class="form-select">
            <option value="">-- Seleccione un jefe --</option>
            @foreach($cargos as $cargo)
                <option value="{{ $cargo->id }}" 
                    {{ old('GDO_cargo_id', $area->GDO_cargo_id ?? '') == $cargo->id ? 'selected' : '' }}>
                    {{ $cargo->nombre_cargo }}
                </option>
            @endforeach
        </select>
    </div>
</div>
