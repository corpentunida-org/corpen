@csrf
<div class="row g-4">
    <div class="col-md-6">
        <label for="nombre" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Nombre</label>
        <input type="text" name="nombre" id="nombre"
               class="form-control form-control-lg @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $lineas_credito->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="cuenta" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Cuenta</label>
        <input type="number" name="cuenta" id="cuenta"
               class="form-control form-control-lg @error('cuenta') is-invalid @enderror"
               value="{{ old('cuenta', $lineas_credito->cuenta ?? '') }}" required>
        @error('cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="cre_tipos_creditos_id" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Tipo de Crédito</label>
        <select name="cre_tipos_creditos_id" id="cre_tipos_creditos_id"
                class="form-select form-select-lg @error('cre_tipos_creditos_id') is-invalid @enderror" required>
            <option value="" disabled {{ old('cre_tipos_creditos_id', $lineas_credito->cre_tipos_creditos_id ?? '') ? '' : 'selected' }}>Selecciona...</option>
            @foreach ($tiposCredito as $tipo)
                <option value="{{ $tipo->id }}" @selected(old('cre_tipos_creditos_id', $lineas_credito->cre_tipos_creditos_id ?? '') == $tipo->id)>{{ $tipo->nombre }}</option>
            @endforeach
        </select>
        @error('cre_tipos_creditos_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="cre_garantias_id" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Garantía</label>
        <select name="cre_garantias_id" id="cre_garantias_id"
                class="form-select form-select-lg @error('cre_garantias_id') is-invalid @enderror" required>
            <option value="" disabled {{ old('cre_garantias_id', $lineas_credito->cre_garantias_id ?? '') ? '' : 'selected' }}>Selecciona...</option>
            @foreach ($garantias as $garantia)
                <option value="{{ $garantia->id }}" @selected(old('cre_garantias_id', $lineas_credito->cre_garantias_id ?? '') == $garantia->id)>{{ $garantia->nombre }}</option>
            @endforeach
        </select>
        @error('cre_garantias_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="tasa_interes" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Tasa de Interés (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="tasa_interes" id="tasa_interes"
               class="form-control form-control-lg @error('tasa_interes') is-invalid @enderror"
               value="{{ old('tasa_interes', $lineas_credito->tasa_interes ?? '') }}" required>
        @error('tasa_interes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="plazo_minimo" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Plazo Mínimo (meses)</label>
        <input type="number" min="1" name="plazo_minimo" id="plazo_minimo"
               class="form-control form-control-lg @error('plazo_minimo') is-invalid @enderror"
               value="{{ old('plazo_minimo', $lineas_credito->plazo_minimo ?? '') }}" required>
        @error('plazo_minimo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="plazo_maximo" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Plazo Máximo (meses)</label>
        <input type="number" min="1" name="plazo_maximo" id="plazo_maximo"
               class="form-control form-control-lg @error('plazo_maximo') is-invalid @enderror"
               value="{{ old('plazo_maximo', $lineas_credito->plazo_maximo ?? '') }}" required>
        @error('plazo_maximo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="edad_minima" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Edad Mínima</label>
        <input type="number" min="18" name="edad_minima" id="edad_minima"
               class="form-control form-control-lg @error('edad_minima') is-invalid @enderror"
               value="{{ old('edad_minima', $lineas_credito->edad_minima ?? '') }}" required>
        @error('edad_minima') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label for="edad_maxima" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Edad Máxima</label>
        <input type="number" min="18" name="edad_maxima" id="edad_maxima"
               class="form-control form-control-lg @error('edad_maxima') is-invalid @enderror"
               value="{{ old('edad_maxima', $lineas_credito->edad_maxima ?? '') }}" required>
        @error('edad_maxima') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label for="fecha_apertura" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Fecha Apertura</label>
        <input type="date" name="fecha_apertura" id="fecha_apertura"
               class="form-control form-control-lg @error('fecha_apertura') is-invalid @enderror"
               value="{{ old('fecha_apertura', optional($lineas_credito->fecha_apertura ?? null)->format('Y-m-d')) }}" required>
        @error('fecha_apertura') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label for="fecha_cierre" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Fecha Cierre (opcional)</label>
        <input type="date" name="fecha_cierre" id="fecha_cierre"
               class="form-control form-control-lg @error('fecha_cierre') is-invalid @enderror"
               value="{{ old('fecha_cierre', optional($lineas_credito->fecha_cierre ?? null)->format('Y-m-d')) }}">
        @error('fecha_cierre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="form-text text-muted">Déjalo vacío si sigue vigente.</small>
    </div>

    <div class="col-12">
        <label for="observacion" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block">Observación</label>
        <textarea name="observacion" id="observacion" rows="3"
                  class="form-control @error('observacion') is-invalid @enderror">{{ old('observacion', $lineas_credito->observacion ?? '') }}</textarea>
        @error('observacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 py-2"><hr class="opacity-10"></div>

    <div class="col-12">
        <div class="d-flex align-items-center justify-content-end gap-3">
            <a href="{{ route('lineas_credito.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">Cancelar</a>
            <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm">
                <i class="feather-save me-2"></i>{{ $buttonText ?? 'Guardar' }}
            </button>
        </div>
    </div>
</div>
