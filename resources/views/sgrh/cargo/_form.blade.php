{{-- Espera: $cargo (null en creación), $areas, $jornadas --}}
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
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-dollar-sign me-1 text-primary"></i>Salario base
        </label>
        <input type="number" step="0.01" min="0" name="salario_base" class="form-control @error('salario_base') is-invalid @enderror"
               value="{{ old('salario_base', $cargo->salario_base ?? '') }}">
        @error('salario_base')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
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
    <div class="col-md-4">
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
        <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: .04em;">Contacto corporativo del cargo</p>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-phone me-1 text-primary"></i>Teléfono
        </label>
        <input type="text" name="telefono_corporativo" class="form-control @error('telefono_corporativo') is-invalid @enderror"
               value="{{ old('telefono_corporativo', $cargo->telefono_corporativo ?? '') }}">
        @error('telefono_corporativo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-smartphone me-1 text-primary"></i>Celular
        </label>
        <input type="text" name="celular_corporativo" class="form-control @error('celular_corporativo') is-invalid @enderror"
               value="{{ old('celular_corporativo', $cargo->celular_corporativo ?? '') }}">
        @error('celular_corporativo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-2">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-hash me-1 text-primary"></i>Ext.
        </label>
        <input type="text" name="ext_corporativo" class="form-control @error('ext_corporativo') is-invalid @enderror"
               value="{{ old('ext_corporativo', $cargo->ext_corporativo ?? '') }}">
        @error('ext_corporativo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-mail me-1 text-primary"></i>Correo corporativo
        </label>
        <input type="email" name="correo_corporativo" class="form-control @error('correo_corporativo') is-invalid @enderror"
               value="{{ old('correo_corporativo', $cargo->correo_corporativo ?? '') }}">
        @error('correo_corporativo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-mail me-1 text-primary"></i>Gmail corporativo
        </label>
        <input type="email" name="gmail_corporativo" class="form-control @error('gmail_corporativo') is-invalid @enderror"
               value="{{ old('gmail_corporativo', $cargo->gmail_corporativo ?? '') }}">
        @error('gmail_corporativo')
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
