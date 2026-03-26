<div class="row g-4">
    {{-- Campo: Nombre del Resultado --}}
    <div class="col-12">
        <div class="form-group px-md-2">
            <label for="name" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">
                Nombre del Resultado
            </label>
            <div class="input-group input-group-lg border rounded-3 overflow-hidden custom-input-group @error('name') is-invalid border-danger @enderror">
                <span class="input-group-text bg-light border-0 px-3 text-success">
                    <i class="feather-check-square"></i>
                </span>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control border-0 ps-2 bg-white @error('name') is-invalid @enderror" 
                       value="{{ old('name', $outcome->name ?? '') }}" 
                       placeholder="Ej: Contactado, No responde, Promesa de pago..." 
                       required
                       style="box-shadow: none;">
            </div>
            
            @error('name')
                <div class="invalid-feedback d-block mt-2 animate__animated animate__fadeInLeft">
                    <i class="feather-alert-circle me-1 small"></i> {{ $message }}
                </div>
            @else
                <small class="form-text text-muted mt-2 d-block opacity-75">
                    Este nombre aparecerá en los reportes de efectividad comercial.
                </small>
            @enderror
        </div>
    </div>

    {{-- Divisor --}}
    <div class="col-12 py-2 px-md-2">
        <hr class="opacity-10">
    </div>

    {{-- Botones de Acción --}}
    <div class="col-12 px-md-2">
        <div class="d-flex align-items-center justify-content-end gap-3">
            <a href="{{ route('interactions.outcomes.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">
                Cancelar
            </a>
            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-save"></i> 
                <span>{{ $buttonText ?? 'Guardar' }}</span>
            </button>
        </div>
    </div>
</div>

<style>
    .custom-input-group { transition: all 0.2s ease; border: 1.5px solid #eee !important; }
    .custom-input-group:focus-within { border-color: #198754 !important; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }
    .custom-input-group.is-invalid { border-color: #dc3545 !important; }
    .fs-xs { font-size: 0.7rem; }
</style>