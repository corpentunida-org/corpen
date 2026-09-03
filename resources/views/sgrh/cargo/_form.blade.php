{{-- Espera: $cargo (null en creación), $areas, $jornadas, $cargos --}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-briefcase me-1 text-primary"></i>Nombre
        </label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $cargo->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-grid me-1 text-primary"></i>Área
        </label>
        <select name="sgrh_area_id" class="form-select @error('sgrh_area_id') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}" @selected((string) old('sgrh_area_id', $cargo->sgrh_area_id ?? '') === (string) $area->id)>
                    {{ $area->nombre }}
                </option>
            @endforeach
        </select>
        @error('sgrh_area_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-clock me-1 text-primary"></i>Jornada
        </label>
        <select name="jornada" class="form-select @error('jornada') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($jornadas as $jornada)
                <option value="{{ $jornada }}" @selected(old('jornada', $cargo->jornada ?? '') === $jornada)>
                    {{ $jornada }}
                </option>
            @endforeach
        </select>
        @error('jornada')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-activity me-1 text-primary"></i>Estado
        </label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                   @checked(old('activo', $cargo->activo ?? true))>
            <label class="form-check-label" for="activo">Activo</label>
        </div>
    </div>
    <div class="col-12">
        <hr class="my-1">
        <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: .04em;">Cadena de aprobación</p>
        <p class="text-muted small mb-0">Preparación para las aprobaciones de permisos y vacaciones (uso futuro).</p>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-user-check me-1 text-primary"></i>Jefe inmediato
        </label>
        <select name="jefe_inmediato_id" class="form-select @error('jefe_inmediato_id') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($cargos as $c)
                <option value="{{ $c->id }}" @selected((string) old('jefe_inmediato_id', $cargo->jefe_inmediato_id ?? '') === (string) $c->id)>
                    {{ $c->nombre }}
                </option>
            @endforeach
        </select>
        @error('jefe_inmediato_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-user-check me-1 text-primary"></i>Director
        </label>
        <select name="director_id" class="form-select @error('director_id') is-invalid @enderror">
            <option value="">Sin definir</option>
            @foreach ($cargos as $c)
                <option value="{{ $c->id }}" @selected((string) old('director_id', $cargo->director_id ?? '') === (string) $c->id)>
                    {{ $c->nombre }}
                </option>
            @endforeach
        </select>
        @error('director_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-message-square me-1 text-primary"></i>Observaciones
        </label>
        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones', $cargo->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
