{{-- recursos/views/archivo/gdotipodocumento/_form.blade.php --}}

<div class="row g-4">
    {{-- Campo: Nombre del Tipo de Documento --}}
    <div class="col-12">
        <div class="form-group">
            <label for="nombre" class="form-label small text-uppercase fw-bold text-muted mb-2 ls-1">
                <i class="bi bi-type me-1"></i> Identificador del Tipo
            </label>
            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                <span class="input-group-text bg-white border-0 text-primary">
                    <i class="bi bi-pencil-square"></i>
                </span>
                <input type="text"
                       name="nombre"
                       id="nombre"
                       class="form-control border-0 ps-0 @error('nombre') is-invalid @enderror"
                       placeholder="Ej: Acta de Inicio, Contrato Laboral..."
                       value="{{ old('nombre', $tipoDocumento->nombre ?? '') }}"
                       required
                       style="font-size: 1rem;">
            </div>
            
            @error('nombre')
                <div class="text-danger small mt-2 d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                </div>
            @else
                <div class="form-text text-muted mt-2 ps-1">
                    Este nombre aparecerá en las búsquedas de archivos de empleados.
                </div>
            @enderror
        </div>
    </div>

    {{-- Campo: Categoría del Documento --}}
    <div class="col-12">
        <div class="form-group">
            <label for="categoria_documento_id" class="form-label small text-uppercase fw-bold text-muted mb-2 ls-1">
                <i class="bi bi-layers me-1"></i> Categoría Raíz
            </label>
            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                <span class="input-group-text bg-white border-0 text-primary">
                    <i class="bi bi-collection"></i>
                </span>
                <select name="categoria_documento_id"
                        id="categoria_documento_id"
                        class="form-select border-0 ps-0 @error('categoria_documento_id') is-invalid @enderror"
                        required
                        style="font-size: 1rem; cursor: pointer;">
                    <option value="" disabled {{ old('categoria_documento_id', $tipoDocumento->categoria_documento_id) ? '' : 'selected' }}>
                        Seleccione el grupo al que pertenece...
                    </option>
                    
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" 
                                {{ old('categoria_documento_id', $tipoDocumento->categoria_documento_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('categoria_documento_id')
                <div class="text-danger small mt-2 d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                </div>
            @else
                <div class="form-text text-muted mt-2 ps-1">
                    Define la ubicación lógica dentro del archivo general.
                </div>
            @enderror
        </div>
    </div>
</div>

<style>
    /* Estilo para espaciado de letras en etiquetas corporativas */
    .ls-1 { letter-spacing: 0.5px; }

    /* Mejora del foco en el grupo de entrada */
    .input-group:focus-within {
        border-color: #6366f1 !important; /* Indigo moderno */
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }

    /* Quitar el borde azul de los inputs individuales al enfocarlos */
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        background-color: transparent;
    }

    /* Estilo suave para los iconos del input-group */
    .input-group-text {
        font-size: 1.1rem;
    }
</style>