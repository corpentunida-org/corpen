<div class="row g-4">
    {{-- Campo: Nombre --}}
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="text-uppercase fs-xs fw-bold text-muted mb-2 d-block" style="letter-spacing: 1px;">
                Nombre del Canal
            </label>
            <div class="input-group input-group-lg border rounded-3 overflow-hidden custom-input-group @error('name') is-invalid border-danger @enderror">
                <span class="input-group-text bg-light border-0 px-3 text-primary">
                    <i class="feather-type"></i>
                </span>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control border-0 ps-2 bg-white @error('name') is-invalid @enderror" 
                       value="{{ old('name', $channel->name ?? '') }}" 
                       placeholder="Ej: WhatsApp Business, Email Directo..." 
                       required
                       style="box-shadow: none;">
            </div>
            
            @error('name')
                <div class="invalid-feedback d-block mt-2 animate__animated animate__fadeInLeft">
                    <i class="feather-alert-circle me-1 small"></i> {{ $message }}
                </div>
            @else
                <small class="form-text text-muted mt-2 d-block opacity-75">
                    Utiliza un nombre corto y descriptivo para el canal.
                </small>
            @enderror
        </div>
    </div>

    {{-- Divisor sutil --}}
    <div class="col-12 py-2">
        <hr class="opacity-10">
    </div>

    {{-- Botones de Acción --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-end gap-3">
            <a href="{{ route('interactions.channels.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">
                Cancelar
            </a>
            <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm d-flex align-items-center gap-2">
                <i class="feather-save"></i> 
                <span>{{ $buttonText }}</span>
            </button>
        </div>
    </div>
</div>

<style>
    /* Estilo para el grupo de entrada enfocado */
    .custom-input-group {
        transition: all 0.2s ease;
        border: 1.5px solid #eee !important;
    }
    
    .custom-input-group:focus-within {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    .custom-input-group.is-invalid {
        border-color: #dc3545 !important;
    }

    .form-control::placeholder {
        color: #adb5bd;
        font-size: 0.95rem;
    }

    .fs-xs { font-size: 0.7rem; }
</style>