{{-- Espera: $area (null en creación), $cargos --}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-grid me-1 text-primary"></i>Nombre
        </label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $area->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-user-check me-1 text-primary"></i>Cargo responsable (jefe de área)
        </label>
        <select name="cargo_responsable_id" class="form-select @error('cargo_responsable_id') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($cargos as $cargo)
                <option value="{{ $cargo->id }}" @selected((string) old('cargo_responsable_id', $area->cargo_responsable_id ?? '') === (string) $cargo->id)>
                    {{ $cargo->nombre }}
                </option>
            @endforeach
        </select>
        @error('cargo_responsable_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-activity me-1 text-primary"></i>Estado
        </label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                   @checked(old('activo', $area->activo ?? true))>
            <label class="form-check-label" for="activo">Activa</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-message-square me-1 text-primary"></i>Descripción
        </label>
        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
