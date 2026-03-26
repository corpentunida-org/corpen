@csrf
<div class="row g-4">
    {{-- Campo: Nombre del Tipo --}}
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">
                Nombre de la Categoría
            </label>
            <div class="input-group input-group-lg border rounded-3 overflow-hidden custom-input-group @error('name') is-invalid border-danger @enderror">
                <span class="input-group-text bg-light border-0 px-3 text-warning">
                    <i class="feather-tag"></i>
                </span>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control border-0 ps-2 bg-white @error('name') is-invalid @enderror" 
                       value="{{ old('name', $type->name ?? '') }}" 
                       placeholder="Ej: Reclamo, Venta, Soporte Técnico..." 
                       required
                       style="box-shadow: none;">
            </div>
            
            @error('name')
                <div class="invalid-feedback d-block mt-2 animate__animated animate__fadeInLeft">
                    <i class="feather-alert-circle me-1 small"></i> {{ $message }}
                </div>
            @else
                <small class="form-text text-muted mt-2 d-block opacity-75">
                    Define un nombre claro para categorizar las interacciones.
                </small>
            @enderror
        </div>
    </div>

    {{-- Divisor sutil --}}
    <div class="col-12 py-2">
        <hr class="opacity-10">
    </div>
</div>

<style>
    .custom-input-group { transition: all 0.2s ease; border: 1.5px solid #eee !important; }
    .custom-input-group:focus-within { border-color: #ffc107 !important; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1); }
    .custom-input-group.is-invalid { border-color: #dc3545 !important; }
    .fs-xs { font-size: 0.7rem; }
</style>