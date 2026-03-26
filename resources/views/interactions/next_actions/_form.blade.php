<div class="row g-4 px-md-2">
    {{-- Campo: Nombre --}}
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">
                Descripción de la Acción
            </label>
            <div class="input-group input-group-lg border rounded-3 overflow-hidden custom-input-group @error('name') is-invalid border-danger @enderror">
                <span class="input-group-text bg-light border-0 px-3 text-indigo">
                    <i class="feather-zap"></i>
                </span>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control border-0 ps-2 bg-white @error('name') is-invalid @enderror" 
                       value="{{ old('name', $nextAction->name ?? '') }}" 
                       placeholder="Ej: Llamada de seguimiento, Enviar propuesta..." 
                       required
                       style="box-shadow: none;">
            </div>
            
            @error('name')
                <div class="invalid-feedback d-block mt-2 animate__animated animate__fadeInLeft">
                    <i class="feather-alert-circle me-1 small"></i> {{ $message }}
                </div>
            @else
                <small class="form-text text-muted mt-2 d-block opacity-75">
                    Define un nombre claro para la tarea que se programará.
                </small>
            @enderror
        </div>
    </div>

    {{-- Divisor --}}
    <div class="col-12 py-2">
        <hr class="opacity-10">
    </div>

    {{-- Botones --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-end gap-3">
            <a href="{{ route('interactions.next_actions.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">
                Cancelar
            </a>
            <button type="submit" class="btn btn-indigo btn-lg rounded-pill px-5 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-save"></i> 
                <span>{{ $buttonText ?? 'Guardar Acción' }}</span>
            </button>
        </div>
    </div>
</div>

<style>
    .text-indigo { color: #6610f2; }
    .btn-indigo { background-color: #6610f2; color: white; border: none; }
    .btn-indigo:hover { background-color: #520dc2; color: white; }
    .custom-input-group { transition: all 0.2s ease; border: 1.5px solid #eee !important; }
    .custom-input-group:focus-within { border-color: #6610f2 !important; box-shadow: 0 0 0 4px rgba(102, 16, 242, 0.1); }
</style>