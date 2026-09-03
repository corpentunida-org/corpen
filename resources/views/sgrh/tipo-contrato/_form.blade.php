{{-- Espera: $tipoContrato (null en creación) --}}
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-file-text me-1 text-primary"></i>Nombre
        </label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $tipoContrato->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
            <i class="feather-activity me-1 text-primary"></i>Estado
        </label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                   @checked(old('activo', $tipoContrato->activo ?? true))>
            <label class="form-check-label" for="activo">Activo</label>
        </div>
    </div>
</div>
