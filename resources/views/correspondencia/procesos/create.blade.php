<x-base-layout>
    <style>
        :root {
            --brand-color: #0f172a;
            --brand-surface: #ffffff;
            --accent-primary: #2563eb;
            --accent-hover: #1d4ed8;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-subtle: #f1f5f9;
            --border-main: #e2e8f0;
            --focus-ring: rgba(37, 99, 235, 0.15);
        }

        body { background-color: #f8fafc; font-family: 'Inter', system-ui, sans-serif; color: var(--text-main); }

        /* Contenedores y Tarjetas */
        .card-ux {
            background: var(--brand-surface);
            border: 1px solid var(--border-main);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        /* Tipografía */
        .text-overline { font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); font-weight: 700; }
        
        /* Formularios UX */
        .form-label-ux { font-weight: 600; color: var(--text-main); font-size: 0.875rem; margin-bottom: 0.5rem; }
        .form-label-ux .required { color: #ef4444; margin-left: 2px; }
        
        .input-group-ux {
            display: flex; align-items: center; background: #fff;
            border: 1px solid var(--border-main); border-radius: 8px;
            transition: all 0.2s ease; overflow: hidden;
        }
        .input-group-ux:focus-within {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px var(--focus-ring);
        }
        .input-group-ux .icon-wrapper {
            padding: 0.6rem 1rem; color: var(--text-muted); background: #f8fafc;
            border-right: 1px solid var(--border-main);
        }
        .input-group-ux input, .input-group-ux select, .input-group-ux textarea {
            border: none; box-shadow: none; padding: 0.6rem 1rem; width: 100%;
            background: transparent; outline: none; color: var(--text-main);
        }
        .input-group-ux input:focus, .input-group-ux select:focus, .input-group-ux textarea:focus {
            outline: none; box-shadow: none; background: transparent;
        }

        /* Stepper Visual */
        .stepper-container { position: relative; margin-bottom: 3rem; z-index: 1; }
        .step-line {
            position: absolute; top: 20px; left: 15%; right: 15%; height: 2px;
            background: var(--border-main); z-index: -1;
        }
        .step-item { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background: transparent; }
        .step-circle {
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem; transition: all 0.3s ease;
        }
        .step-circle.active { background: var(--accent-primary); color: white; box-shadow: 0 0 0 4px var(--focus-ring); }
        .step-circle.pending { background: #fff; border: 2px solid var(--border-main); color: var(--text-muted); }
        
        /* Botones */
        .btn-ux { border-radius: 8px; font-weight: 600; padding: 0.6rem 1.5rem; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-ux-primary { background: var(--accent-primary); color: white; border: 1px solid var(--accent-primary); }
        .btn-ux-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); transform: translateY(-1px); color: white; }
        .btn-ux-secondary { background: #fff; color: var(--text-main); border: 1px solid var(--border-main); }
        .btn-ux-secondary:hover { background: #f1f5f9; }

        /* Switch Custom */
        .switch-ux { width: 44px; height: 24px; }
        .switch-ux:checked { background-color: #10b981; border-color: #10b981; }

        /* Bloque de Archivos */
        .file-block { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 1.5rem; transition: border-color 0.2s; }
        .file-block:hover { border-color: var(--accent-primary); }
    </style>

    <div class="container-fluid py-5 px-lg-5 max-w-7xl mx-auto">
        
        <div class="mb-5 text-center">
            <h2 class="fw-bold text-dark mb-2">Nueva Instancia de Proceso</h2>
            <p class="text-muted">Configura los datos iniciales y requerimientos del proceso.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                
                <div class="stepper-container d-flex justify-content-between px-5">
                    <div class="step-line"></div>
                    <div class="step-item">
                        <div class="step-circle active">1</div>
                        <span class="fw-bold text-dark" style="font-size: 0.875rem;">Definir Datos</span>
                    </div>
                    <div class="step-item opacity-75">
                        <div class="step-circle pending">2</div>
                        <span class="fw-medium text-muted" style="font-size: 0.875rem;">Asignar Equipo</span>
                    </div>
                </div>

                <div class="card-ux p-4 p-md-5">
                    <form action="{{ route('correspondencia.procesos.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8fafc; border: 1px solid var(--border-main);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px; border: 1px solid var(--border-main);">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-overline">Iniciado por</div>
                                            <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Sesión Activa
                                    </span>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25 my-4">

                            <div class="col-12">
                                <label class="form-label-ux">Título del Proceso <span class="required">*</span></label>
                                <div class="input-group-ux">
                                    <div class="icon-wrapper"><i class="fas fa-tag"></i></div>
                                    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Revisión de Contrato Trimestral" required>
                                </div>
                                @error('nombre') <div class="text-danger small mt-1"><i class="fas fa-info-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label-ux">Flujo de Trabajo Base <span class="required">*</span></label>
                                <div class="input-group-ux">
                                    <div class="icon-wrapper"><i class="fas fa-project-diagram"></i></div>
                                    <select name="flujo_id" required>
                                        <option value="" selected disabled>Seleccione el esquema de trabajo...</option>
                                        @foreach($flujos as $f)
                                            <option value="{{ $f->id }}" {{ old('flujo_id') == $f->id ? 'selected' : '' }}>
                                                {{ $f->nombre }} {{ $f->area ? '('.$f->area->nombre.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('flujo_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-ux">Días de Respuesta</label>
                                <div class="input-group-ux">
                                    <div class="icon-wrapper"><i class="fas fa-stopwatch"></i></div>
                                    <input type="number" name="tiempo_respuesta_dias" value="{{ old('tiempo_respuesta_dias', 0) }}" min="0" placeholder="Ej: 5">
                                </div>
                                @error('tiempo_respuesta_dias') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-ux">Asunto o Detalle Inicial</label>
                                <div class="input-group-ux" style="align-items: flex-start;">
                                    <div class="icon-wrapper" style="height: 100%; border-right: none; background: transparent;"><i class="fas fa-align-left mt-2"></i></div>
                                    <textarea name="detalle" rows="3" placeholder="Describa el propósito o contexto de este proceso..." style="resize: none; padding-left: 0;">{{ old('detalle') }}</textarea>
                                </div>
                                @error('detalle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-2">
                                <div class="file-block">
                                    <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row mb-3 gap-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="fas fa-file-signature text-primary"></i> Documentos Requeridos
                                            </h6>
                                            <p class="text-muted small mb-0">Define cuántos archivos son obligatorios para completar este proceso.</p>
                                        </div>
                                        <div class="input-group-ux" style="width: 140px; flex-shrink: 0;">
                                            <div class="icon-wrapper py-1 px-2"><i class="fas fa-hashtag"></i></div>
                                            <input type="number" id="cantidad_archivos_create" name="numero_archivos" min="0" value="{{ old('numero_archivos', 0) }}" class="text-center fw-bold py-1">
                                        </div>
                                    </div>

                                    <div id="contenedor_archivos_create" class="row g-3 mt-2"></div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="border: 1px solid var(--border-main);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded bg-light text-success d-flex align-items-center justify-content-center fs-5" style="width: 40px; height: 40px;">
                                            <i class="fas fa-power-off"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">Activar inmediatamente</div>
                                            <div class="text-muted small">El proceso estará disponible en el panel al guardarlo.</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input type="hidden" name="activo" value="0">
                                        <input class="form-check-input switch-ux" type="checkbox" name="activo" value="1" id="activo" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-ux btn-ux-secondary">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-ux btn-ux-primary">
                                        Siguiente Paso <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputCantidad = document.getElementById('cantidad_archivos_create');
            const contenedor = document.getElementById('contenedor_archivos_create');
            
            // Recuperar archivos previos si hubo error de validación
            let oldFiles = @json(old('tipos_archivos', []));
            if(!Array.isArray(oldFiles)) oldFiles = [];

            function renderArchivos(cantidad) {
                contenedor.innerHTML = '';
                
                if(cantidad === 0) {
                    contenedor.innerHTML = '<div class="col-12 text-center text-muted small py-2"><i class="fas fa-info-circle me-1"></i> No se requieren archivos obligatorios.</div>';
                    return;
                }

                for(let i = 0; i < cantidad; i++) {
                    let val = oldFiles[i] || '';
                    let col = document.createElement('div');
                    col.className = 'col-md-6';
                    col.innerHTML = `
                        <label class="form-label-ux text-muted small">Nombre del Archivo ${i+1}</label>
                        <div class="input-group-ux" style="border-radius: 6px;">
                            <div class="icon-wrapper py-1 px-2" style="background: #fff;"><i class="far fa-file-alt text-primary"></i></div>
                            <input type="text" name="tipos_archivos[]" class="py-1" placeholder="Ej: Contrato Firmado..." value="${val}" required>
                        </div>
                    `;
                    contenedor.appendChild(col);
                }
            }

            if(inputCantidad) {
                inputCantidad.addEventListener('input', function() {
                    let cant = parseInt(this.value) || 0;
                    if(cant < 0) { cant = 0; this.value = 0; }
                    if(cant > 20) { cant = 20; this.value = 20; } // Límite razonable
                    renderArchivos(cant);
                });
                // Renderizado inicial
                renderArchivos(parseInt(inputCantidad.value) || 0);
            }
        });
    </script>
</x-base-layout>