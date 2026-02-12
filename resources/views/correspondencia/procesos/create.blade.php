<x-base-layout>
    <div class="app-container py-4">
        {{-- Encabezado de la página --}}
        <header class="main-header mb-4">
            <div class="header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.procesos.index') }}" class="text-decoration-none">Procesos</a></li>
                        <li class="breadcrumb-item active">Iniciar Nuevo</li>
                    </ol>
                </nav>
                <h1 class="page-title h3 fw-bold">Configuración de Instancia</h1>
            </div>
        </header>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Guía visual del flujo --}}
                <div class="d-flex justify-content-between mb-4 position-relative px-5">
                    <div class="text-center" style="z-index: 2;">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px; margin: 0 auto;">1</div>
                        <small class="fw-bold d-block mt-2">Definir Datos</small>
                    </div>
                    <div class="text-center text-muted" style="z-index: 2;">
                        <div class="rounded-circle bg-white border text-muted d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin: 0 auto;">2</div>
                        <small class="d-block mt-2">Asignar Equipo</small>
                    </div>
                    {{-- Línea conectora --}}
                    <div class="position-absolute top-50 start-0 end-0 translate-middle-y bg-light" style="height: 2px; z-index: 1; margin: 0 80px;"></div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-5">
                        <form action="{{ route('correspondencia.procesos.store') }}" method="POST">
                            @csrf
                            
                            <div class="row g-4">
                                {{-- Sección: Responsable (Automático) --}}
                                <div class="col-md-12">
                                    <div class="p-3 rounded-4 border-dashed d-flex align-items-center bg-light">
                                        <div class="avatar-circle bg-white text-primary shadow-sm me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <label class="text-muted small fw-bold text-uppercase d-block mb-0" style="font-size: 0.7rem; letter-spacing: 1px;">Iniciado por</label>
                                            <span class="fw-bold text-dark">{{ auth()->user()->name }}</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge rounded-pill bg-white text-success border border-success-subtle px-3">
                                                <i class="fas fa-user-check me-1"></i> Sesión Activa
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Nombre del Proceso --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark">Título o Nombre del Proceso <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                        <input type="text" name="nombre" 
                                               class="form-control border-start-0 ps-0 @error('nombre') is-invalid @enderror" 
                                               placeholder="Ej: Revisión de Contrato Trimestral" 
                                               value="{{ old('nombre') }}" required>
                                    </div>
                                    @error('nombre') 
                                        <div class="text-danger small mt-1">{{ $message }}</div> 
                                    @enderror
                                </div>

                                {{-- Selección de Flujo --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark">Flujo de Trabajo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-project-diagram text-muted"></i></span>
                                        <select name="flujo_id" class="form-select border-start-0 ps-0 @error('flujo_id') is-invalid @enderror" required>
                                            <option value="" selected disabled>Seleccione el esquema de trabajo...</option>
                                            @foreach($flujos as $f)
                                                <option value="{{ $f->id }}" {{ old('flujo_id') == $f->id ? 'selected' : '' }}>
                                                    {{ $f->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('flujo_id') 
                                        <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div> 
                                    @enderror
                                    <div class="form-text">El flujo define los pasos predeterminados que seguirá este proceso.</div>
                                </div>

                                {{-- Detalle --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark">Asunto / Detalle Inicial</label>
                                    <textarea name="detalle" 
                                              class="form-control @error('detalle') is-invalid @enderror" 
                                              rows="4" 
                                              placeholder="Describa el propósito de este proceso específico..."
                                              style="resize: none; border-radius: 12px;">{{ old('detalle') }}</textarea>
                                    @error('detalle') 
                                        <div class="text-danger small mt-1">{{ $message }}</div> 
                                    @enderror
                                </div>

                                {{-- CAMPO: Activo (Switch Estilizado) --}}
                                <div class="col-md-12">
                                    <div class="p-3 rounded-4 border bg-light d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box bg-white shadow-sm rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-toggle-on text-primary"></i>
                                            </div>
                                            <div>
                                                <label class="fw-bold text-dark mb-0 d-block" for="activo">Estado de la Instancia</label>
                                                <small class="text-muted">Habilitar inmediatamente al crear</small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input type="hidden" name="activo" value="0">
                                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" style="width: 3em; height: 1.5em; cursor: pointer;" checked>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botonera --}}
                                <div class="col-md-12 pt-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-light px-4 fw-bold border" style="border-radius: 10px;">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius: 10px; background: linear-gradient(45deg, #4f46e5, #6366f1);">
                                            Siguiente: Asignar Equipo <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tips informativos --}}
                <div class="mt-4 p-3 bg-soft-primary rounded-4 d-flex align-items-start">
                    <i class="fas fa-info-circle text-primary mt-1 me-3"></i>
                    <p class="small text-muted mb-0">
                        <strong>Nota:</strong> Al hacer clic en "Siguiente", los datos se guardarán y pasará a la pantalla de asignación de usuarios para definir quiénes participarán en el flujo.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-dashed { border: 2px dashed #e2e8f0; }
        .bg-soft-primary { background-color: #f0f4ff; }
        .form-select:focus, .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
        }
        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
    </style>
</x-base-layout>