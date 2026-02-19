<x-base-layout>
    <div class="app-container py-4">
        {{-- Navegación superior --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('correspondencia.procesos.index') }}" class="text-decoration-none">Procesos</a></li>
                <li class="breadcrumb-item active fw-bold text-dark">Editar Proceso #{{ $proceso->id }}</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                {{-- Encabezado del Formulario --}}
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box me-3" style="width: 55px; height: 55px; border-radius: 15px; display: flex; align-items: center; justify-content: center; background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-edit fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0 h4">Modificar Proceso</h3>
                        <p class="text-muted m-0">Actualice la información principal de la instancia.</p>
                    </div>
                </div>

                <form action="{{ route('correspondencia.procesos.update', $proceso) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Nombre del Proceso --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Título / Nombre del Proceso</label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $proceso->nombre) }}" required style="border-radius: 10px;">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Selección de Flujo --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Flujo de Trabajo</label>
                            <select name="flujo_id" class="form-select @error('flujo_id') is-invalid @enderror" style="border-radius: 10px;">
                                @foreach($flujos as $f)
                                    <option value="{{ $f->id }}" {{ (old('flujo_id', $proceso->flujo_id) == $f->id) ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('flujo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- SECCIÓN: Archivos Requeridos --}}
                        <div class="col-md-12">
                            <div class="p-4 rounded-4" style="background-color: #fafafa; border: 1px solid #e5e7eb;">
                                <h6 class="fw-bold mb-3 d-flex align-items-center text-primary">
                                    <i class="fas fa-file-signature me-2"></i> Configuración de Archivos Obligatorios
                                </h6>
                                <div class="row align-items-center mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">¿Cuántos archivos se deben subir?</label>
                                        <p class="small text-muted mb-2">Define la cantidad y asigna un nombre a cada documento que será requerido en este proceso.</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-sort-numeric-up text-primary"></i></span>
                                            <input type="number" id="cantidad_archivos_edit" name="numero_archivos" class="form-control border-start-0 fw-bold" min="0" value="{{ old('numero_archivos', $proceso->numero_archivos ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Contenedor donde aparecerán los inputs dinámicos --}}
                                <div id="contenedor_archivos_edit" class="row g-3">
                                    </div>
                            </div>
                        </div>

                        {{-- CAMPO: Estado (Switch de Activación) --}}
                        <div class="col-md-12">
                            <div class="p-3 rounded-4 border {{ $proceso->activo ? 'bg-light-success' : 'bg-light' }} d-flex align-items-center justify-content-between" 
                                 style="transition: background 0.3s ease; {{ $proceso->activo ? 'background-color: #f0fdf4; border-color: #bbf7d0 !important;' : '' }}">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-white shadow-sm rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas {{ $proceso->activo ? 'fa-check-circle text-success' : 'fa-pause-circle text-muted' }}"></i>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-dark mb-0 d-block" for="activo">Estado del Proceso</label>
                                        <small class="text-muted">{{ $proceso->activo ? 'Esta instancia está recibiendo documentos' : 'Esta instancia se encuentra pausada' }}</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input type="hidden" name="activo" value="0">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" 
                                           style="width: 3em; height: 1.5em; cursor: pointer;" 
                                           {{ old('activo', $proceso->activo) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- Detalle --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Detalle o Notas</label>
                            <textarea name="detalle" class="form-control" rows="4" style="border-radius: 12px; resize: none;" 
                                      placeholder="Agregue información adicional sobre este proceso...">{{ old('detalle', $proceso->detalle) }}</textarea>
                        </div>

                        {{-- Botonera --}}
                        <div class="col-md-12 pt-4 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-light px-4 border" style="border-radius: 10px;">
                                    Cancelar
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-outline-primary px-4" style="border-radius: 10px;">
                                        <i class="fas fa-users me-2"></i> Equipo
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 10px; background: #4f46e5; border: none; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);">
                                        <i class="fas fa-save me-2"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script para generación dinámica de inputs de archivos en edición --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputCantidad = document.getElementById('cantidad_archivos_edit');
            const contenedor = document.getElementById('contenedor_archivos_edit');
            
            // Recuperamos los archivos ya guardados en el proceso o los re-enviados (old)
            let existingFiles = @json(old('tipos_archivos', $proceso->tipos_archivos ?? []));
            
            // Asegurarse de que existingFiles sea siempre un array
            if(typeof existingFiles === 'string') {
                try { existingFiles = JSON.parse(existingFiles); } catch(e) { existingFiles = []; }
            }
            if(!Array.isArray(existingFiles)) existingFiles = [];

            function renderArchivos(cantidad) {
                contenedor.innerHTML = '';
                for(let i = 0; i < cantidad; i++) {
                    let val = existingFiles[i] || ''; // Asignar nombre si ya existe, si no vacío
                    let col = document.createElement('div');
                    col.className = 'col-md-6 mb-3';
                    col.innerHTML = `
                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Nombre del Archivo ${i+1}</label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-file-alt text-primary"></i></span>
                            <input type="text" name="tipos_archivos[]" class="form-control border-start-0" placeholder="Ej: Cédula, Contrato..." value="${val}" required>
                        </div>
                    `;
                    contenedor.appendChild(col);
                }
            }

            if(inputCantidad) {
                inputCantidad.addEventListener('input', function() {
                    let cant = parseInt(this.value) || 0;
                    if(cant < 0) { cant = 0; this.value = 0; }
                    renderArchivos(cant);
                });
                // Ejecutar inmediatamente con la cantidad actual en BD o Input
                renderArchivos(parseInt(inputCantidad.value) || 0);
            }
        });
    </script>

    <style>
        .form-check-input:checked {
            background-color: #10b981; /* Verde éxito para activo */
            border-color: #10b981;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1);
        }
        .bg-light-success { background-color: #f0fdf4; }
    </style>
</x-base-layout>