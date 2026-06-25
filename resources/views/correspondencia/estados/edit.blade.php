<x-base-layout>
    <div class="app-container py-4">
        
        {{-- ENLACE DE RETORNO --}}
        <div class="mb-4">
            <a href="{{ route('correspondencia.estados.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 hover-opacity-75 transition-all">
                <i class="fas fa-arrow-left"></i> Regresar al listado de estados
            </a>
        </div>

        {{-- TARJETA PRINCIPAL DEL FORMULARIO --}}
        <div class="card border-0 shadow-sm mx-auto rounded-4 overflow-hidden" style="max-width: 650px;">
            
            {{-- ENCABEZADO DE LA TARJETA --}}
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h1 class="h4 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 38px; height: 38px; background-color: #e3f2fd;">
                        <i class="fas fa-edit fs-6" style="color: #1976d2;"></i>
                    </div>
                    Editar Estado
                </h1>
                <p class="text-muted small mb-0 mt-2">Modificando los parámetros y el ciclo de vida de: <strong class="text-dark">{{ $estado->nombre }}</strong></p>
            </div>

            {{-- CUERPO DEL FORMULARIO --}}
            <div class="card-body p-4">
                
                {{-- SECCIÓN DE ALERTA PARA ERRORES DE BASE DE DATOS --}}
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 small d-flex align-items-center gap-2 mb-4 shadow-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                <form id="mainForm" action="{{ route('correspondencia.estados.update', $estado) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- 1. NOMBRE DEL ESTADO --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-2">
                            Nombre del Estado <span class="text-danger">*</span>
                        </label>
                        <div class="input-group rounded-3 shadow-sm @error('nombre') has-validation @enderror">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-tag small"></i>
                            </span>
                            <input type="text" name="nombre" class="form-control border-start-0 ps-2 rounded-end-3 @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $estado->nombre) }}" placeholder="Ej: Radicado, En Revisión..." maxlength="100" required>
                            @error('nombre') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>

                    {{-- 2. ÁREA ASIGNADA / RESPONSABLE --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-2">
                            Área Responsable <span class="text-danger">*</span>
                        </label>
                        <div class="input-group rounded-3 shadow-sm @error('id_area') has-validation @enderror">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fas fa-building small"></i>
                            </span>
                            <select name="id_area" class="form-select border-start-0 ps-2 rounded-end-3 @error('id_area') is-invalid @enderror" required style="cursor: pointer;">
                                <option value="" disabled>Seleccione el sector o área encargado...</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('id_area', $estado->id_area) == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_area') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>

                    {{-- 3. DESCRIPCIÓN OPERATIVA --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Descripción</label>
                        <textarea name="descripcion" class="form-control rounded-3 shadow-sm @error('descripcion') is-invalid @enderror" 
                                  rows="3" placeholder="Explique de manera clara qué función o trazabilidad cumple este estado..." maxlength="255">{{ old('descripcion', $estado->descripcion) }}</textarea>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="text-muted small" style="font-size: 0.75rem;">Definición útil para la UI y reportes.</span>
                            <span class="text-muted small" style="font-size: 0.75rem;">Máx. 255 caract.</span>
                        </div>
                        @error('descripcion') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- 4. CONTROL DE ESTADO OPERATIVO (SWITCH ULTRA UX) --}}
                    <div class="mb-4 p-3 rounded-3 border d-flex align-items-center justify-content-between transition-all hover-bg-light" style="background-color: #f8fafc;">
                        <div class="pe-3">
                            <label class="form-check-label fw-bold text-dark d-block mb-0" style="cursor: pointer;" for="switchActivo">
                                Estado Habilitado / Activo
                            </label>
                            <span class="text-muted small d-block mt-0.5" style="font-size: 0.8rem;">Indica si estará disponible de inmediato para ser asignado en flujos de trabajo.</span>
                        </div>
                        <div class="form-check form-switch m-0 fs-4">
                            <input class="form-check-input" type="checkbox" name="activo" id="switchActivo" value="1" {{ old('activo', $estado->activo) ? 'checked' : '' }} style="cursor: pointer;">
                        </div>
                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="{{ route('correspondencia.estados.index') }}" class="btn btn-light border rounded-pill px-4 fw-semibold order-2 order-sm-1">
                            Cancelar
                        </a>
                        <button type="submit" id="submitBtn" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm order-1 order-sm-2 d-flex align-items-center justify-content-center gap-2" style="background-color: #1976d2; border-color: #1976d2;">
                            <i class="fas fa-save" id="btnIcon"></i>
                            <span id="btnText">Actualizar Estado</span>
                            <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ESTILOS EXCLUSIVOS ADICIONALES --}}
    @push('styles')
    <style>
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-opacity-75:hover { opacity: 0.75; }
        .hover-bg-light:hover { background-color: #f1f5f9 !important; }
        
        /* Ajuste fino para la consistencia visual de los input-groups */
        .input-group .form-control:focus, .input-group .form-select:focus {
            z-index: 3;
            box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.25);
            border-color: #1976d2;
        }
        
        /* Estilo del Switch alineado al tema general */
        .form-switch .form-check-input:checked {
            background-color: #2e7d32; /* Verde éxito para indicar que está activo */
            border-color: #2e7d32;
        }
        .form-switch .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }
    </style>
    @endpush

    {{-- SCRIPTS DE CONTROL DE ENVÍO --}}
    @push('scripts')
    <script>
        document.getElementById('mainForm').onsubmit = function() {
            const btn = document.getElementById('submitBtn');
            const icon = document.getElementById('btnIcon');
            const spinner = document.getElementById('btnSpinner');
            const txt = document.getElementById('btnText');
            
            // Bloqueo preventivo de doble envío
            btn.classList.add('disabled');
            btn.style.pointerEvents = 'none';
            if(icon) icon.classList.add('d-none');
            txt.innerText = "Actualizando...";
            spinner.classList.remove('d-none');
            
            return true;
        };
    </script>
    @endpush
</x-base-layout>