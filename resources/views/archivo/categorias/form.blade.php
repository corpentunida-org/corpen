{{-- Contenedor de Formulario Minimalista --}}
<div class="row g-4">
    <div class="col-12">
        {{-- Campo de Nombre con Estilo Floating Label (Opcional, pero muy Pro) --}}
        <div class="form-group mb-2">
            <label for="nombre" class="form-label small text-uppercase fw-bold text-muted mb-2">
                Nombre de la Categoría
            </label>
            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                <span class="input-group-text bg-white border-0 text-primary">
                    <i class="bi bi-tag-fill"></i>
                </span>
                <input type="text"
                       id="nombre"
                       name="nombre"
                       class="form-control border-0 ps-0 @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre', $categoria->nombre) }}"
                       placeholder="Ej: Contratos de Arrendamiento"
                       required
                       autofocus>
            </div>
            
            {{-- Feedback de Validación con estilo moderno --}}
            @error('nombre')
                <div class="text-danger small mt-2 d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                </div>
            @else
                <div id="nombreHelp" class="form-text text-muted mt-2 ps-1">
                    Usa nombres cortos y descriptivos para facilitar la búsqueda posterior.
                </div>
            @enderror
        </div>
    </div>

    {{-- Separador Sutil --}}
    <div class="col-12 py-2">
        <hr class="opacity-10">
    </div>

    {{-- Botones de Acción con Jerarquía Clara --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            {{-- Opción de Cancelar (Menos llamativa) --}}
            <a href="{{ route('archivo.categorias.index') }}" class="btn btn-link text-decoration-none text-muted fw-medium px-0">
                <i class="bi bi-x-lg me-1"></i> Descartar cambios
            </a>

            {{-- Botón de Acción Principal (Muy llamativo) --}}
            <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm transition-all hover-lift">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i>
                {{ $btnText ?? 'Guardar Categoría' }}
            </button>
        </div>
    </div>
</div>

<style>
    /* Efecto de elevación para el botón corporativo */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    /* Estilo para el input enfocado */
    .input-group:focus-within {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    }
    .form-control:focus {
        box-shadow: none;
    }
</style>
