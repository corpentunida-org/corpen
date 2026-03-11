@php
    /** @var int|null $idAreaAgente */
    /** @var int|null $idCargoAgente */
@endphp

<style>
    /* =========================================================
       VARIABLES GLOBALES (Diseño Minimalista y Plano)
       ========================================================= */
    :root {
        --crm-primary: #111827; /* Gris muy oscuro, casi negro para acentos */
        --crm-primary-light: #f9fafb;
        --crm-primary-hover: #374151;
        --crm-success: #059669;
        --crm-warning: #D97706;
        --crm-danger: #DC2626;
        
        --text-main: #111827;
        --text-muted: #6b7280;
        
        --bg-main: #ffffff;
        --bg-card: #ffffff;
        
        --border-color: #e5e7eb;
        --border-hover: #d1d5db;
        
        /* Cero sombras para un look minimalista 100% flat */
        --shadow-sm: none;
        --shadow-md: none;
        --shadow-lg: none;
        
        --transition-fast: all 0.2s ease;
        --transition-smooth: all 0.3s ease;
    }

    body {
        background-color: var(--bg-main);
        color: var(--text-main);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* =========================================================
       NAVEGACIÓN POR PESTAÑAS (Estilo Texto Subrayado)
       ========================================================= */
    .tab-navigation {
        display: flex;
        gap: 2rem;
        padding: 0 1rem;
        background-color: transparent;
        border-bottom: 1px solid var(--border-color);
        overflow-x: auto;
        scrollbar-width: none;
    }

    .tab-navigation::-webkit-scrollbar {
        display: none;
    }

    .tab-button {
        background: transparent;
        border: none;
        padding: 1rem 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        transition: var(--transition-fast);
        position: relative;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .tab-button::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: transparent;
        transition: var(--transition-fast);
    }

    .tab-button:hover:not(.active) {
        color: var(--text-main);
    }

    .tab-button.active {
        color: var(--crm-primary);
        font-weight: 600;
    }

    .tab-button.active::after {
        background-color: var(--crm-primary);
    }

    .tab-button.active i {
        color: var(--crm-primary);
    }

    /* =========================================================
       CONTENIDO Y ANIMACIONES DE PESTAÑAS
       ========================================================= */
    .form-content {
        padding: 1.5rem 0;
    }

    .tab-panel {
        display: none;
        animation: fadeIn 0.3s ease-in forwards;
    }

    .tab-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* =========================================================
       TARJETAS Y CONTENEDORES (Minimalistas, sin sombra)
       ========================================================= */
    .category-container {
        margin-bottom: 1.5rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
    }

    .category-header {
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color);
    }

    .category-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* =========================================================
       FORMULARIOS Y UX INPUTS (Líneas finas)
       ========================================================= */
    .form-label {
        font-weight: 500;
        color: var(--text-main);
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 0.6rem 0.8rem;
        font-size: 0.95rem;
        color: var(--text-main);
        background-color: var(--bg-card);
        transition: var(--transition-fast);
        box-shadow: none !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--crm-primary);
        background-color: var(--bg-card);
        box-shadow: 0 0 0 1px var(--crm-primary) !important;
        outline: none;
    }

    .required-field::after {
        content: "*";
        color: var(--text-muted);
        margin-left: 4px;
    }

    /* =========================================================
       TARJETAS VISUALES INTERACTIVAS (Radio/Checks flat)
       ========================================================= */
    .visual-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .visual-card:hover {
        border-color: var(--border-hover);
    }

    .btn-check:checked + .visual-card {
        border-color: var(--crm-primary);
        background-color: var(--crm-primary-light);
    }

    .btn-check:checked + .visual-card .card-icon {
        color: var(--crm-primary) !important;
    }

    .check-badge {
        transition: var(--transition-fast);
        opacity: 0;
    }

    .btn-check:checked + label .check-badge {
        opacity: 1;
    }

    /* Etiquetas/Tags Inteligentes */
    .smart-tag {
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        background: var(--bg-card);
        border-radius: 4px;
    }
    
    .btn-check:checked + .smart-tag {
        background-color: var(--crm-primary);
        color: white;
        border-color: var(--crm-primary);
    }

    /* =========================================================
       LÍNEA DE TIEMPO (TIMELINE) Limpia
       ========================================================= */
    .timeline-container {
        border-left: 1px solid var(--border-color);
        margin-left: 15px;
        padding-left: 20px;
        position: relative;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 2px solid var(--crm-primary);
        z-index: 2;
    }

    /* =========================================================
       UTILIDADES, ERRORES Y OVERLAYS
       ========================================================= */
    .input-error, .select2-error + .select2-container .select2-selection {
        border-color: var(--crm-danger) !important;
    }

    #loading-overlay {
        background: rgba(255, 255, 255, 0.9);
        transition: opacity 0.3s ease;
    }

    /* =========================================================
       ZONA DE ARRASTRE (DROPZONE)
       ========================================================= */
    .drop-zone-pro {
        border: 1px dashed var(--border-hover);
        background: var(--bg-card);
        transition: var(--transition-fast);
    }
    
    .drop-zone-pro:hover {
        border-color: var(--crm-primary);
        background: var(--crm-primary-light);
    }

    /* Sobreescribiendo botones Bootstrap para que sean flat */
    .btn {
        border-radius: 4px;
        font-weight: 500;
        box-shadow: none !important;
    }
    .btn-outline-primary {
        color: var(--crm-primary);
        border-color: var(--border-color);
    }
    .btn-outline-primary:hover {
        background-color: var(--crm-primary-light);
        color: var(--crm-primary);
        border-color: var(--crm-primary);
    }
    .btn-primary {
        background-color: var(--crm-primary);
        border-color: var(--crm-primary);
        color: white;
    }
    .btn-primary:hover {
        background-color: var(--crm-primary-hover);
        border-color: var(--crm-primary-hover);
    }
</style>

<form id="interaction-form" action="{{ $modoEdicion ? route('interactions.update', $interaction->id) : route('interactions.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($modoEdicion)
        @method('PUT')
    @endif

    <div class="bg-white border-bottom p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="text-dark">
                <i class="bi bi-headset fs-2"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;">
                    {{ $modoEdicion ? 'Editar Interacción #'.$interaction->id : 'Registro de Seguimiento Diario' }}
                </h4>
                <p class="text-muted small mb-0">
                    <i class="bi bi-clock me-1"></i> Sesión iniciada • Completa la gestión
                </p>
            </div>
        </div>
        
        @if ($modoEdicion && $interaction->id)
            <div>
                <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-outline-secondary px-3 py-2 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-eye"></i> Ver Detalles
                </a>
            </div>
        @endif
    </div>

    <div class="tab-navigation px-4">
        <button type="button" class="tab-button active" data-tab="principal">
            <i class="bi bi-info-circle"></i> Principal
        </button>
        <button type="button" class="tab-button" data-tab="resultado">
            <i class="bi bi-check2-circle"></i> Resultado
        </button>
    </div>

    <div class="progress mt-0 mb-4" style="height: 2px; border-radius: 0; background-color: var(--border-color);">
        <div id="form-progress" class="progress-bar bg-dark" role="progressbar" style="width: 50%; transition: width 0.3s ease;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="form-content position-relative px-4">
        
        <div class="tab-panel active" id="principal-tab">
            
            <div class="category-container mb-4">
                <div class="category-header p-3 d-flex justify-content-between align-items-center cursor-pointer"
                    data-bs-toggle="collapse" data-bs-target="#originCollapse" aria-expanded="true"
                    aria-controls="originCollapse" style="user-select: none;">
                    <h5 class="category-title mb-0">
                        Origen del Contacto
                    </h5>
                    <i class="bi bi-chevron-down text-muted accordion-arrow transition-all"></i>
                </div>

                <div class="collapse show" id="originCollapse">
                    <div class="category-content p-4">
                        <label class="form-label text-muted text-uppercase mb-3">¿Quién inicia la interacción?</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="caller_type" id="caller_client" value="client" checked>
                                <label class="visual-card d-flex flex-row align-items-center p-3 gap-3 position-relative h-100" for="caller_client">
                                    <i class="bi bi-person card-icon mb-0 fs-3 text-muted"></i>
                                    <div class="text-start flex-grow-1">
                                        <div class="fw-bold mb-0 text-dark">Titular</div>
                                        <small class="text-muted">Cliente registrado</small>
                                    </div>
                                    <i class="bi bi-check L check-badge fs-5 position-absolute end-0 me-3 text-dark" style="opacity: 0;"></i>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="caller_type" id="caller_third" value="third_party">
                                <label class="visual-card d-flex flex-row align-items-center p-3 gap-3 position-relative h-100" for="caller_third">
                                    <i class="bi bi-people card-icon mb-0 fs-3 text-muted"></i>
                                    <div class="text-start flex-grow-1">
                                        <div class="fw-bold mb-0 text-dark">Tercero</div>
                                        <small class="text-muted">Familiar o autorizado</small>
                                    </div>
                                    <i class="bi bi-check check-badge fs-5 position-absolute end-0 me-3 text-dark" style="opacity: 0;"></i>
                                </label>
                            </div>
                        </div>

                        <div id="third-party-fields" class="mt-4 p-4 bg-light border" style="display: none; border-radius: 4px;">
                            <h6 class="text-dark fw-bold small text-uppercase mb-3">Datos de quien llama</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nombre Completo <span class="text-muted">*</span></label>
                                    <input type="text" class="form-control" id="nombre_quien_llama" name="nombre_quien_llama" placeholder="Ej. Juan Pérez">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Identificación <span class="text-muted">*</span></label>
                                    <input type="text" class="form-control" id="cedula_quien_llama" name="cedula_quien_llama" placeholder="Número de documento">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono <span class="text-muted">*</span></label>
                                    <input type="text" class="form-control" id="celular_quien_llama" name="celular_quien_llama" placeholder="Número de contacto">
                                </div>
                                <div class="col-12 pt-2" id="container-parentezco">
                                    <label class="form-label mb-2">Relación con el titular: <span class="text-muted">*</span></label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach (['familiar' => 'Familiar', 'amigo' => 'Amigo', 'representante' => 'Representante', 'otro' => 'Otro'] as $v => $l)
                                            <input type="radio" class="btn-check" name="parentezco_quien_llama" id="rel_{{ $v }}" value="{{ $v }}">
                                            <label class="smart-tag py-1 px-3 small cursor-pointer" for="rel_{{ $v }}">{{ $l }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="category-container mb-4 p-4">
                <div class="row align-items-start g-4">
                    <div class="col-md-8">
                        <label for="client_id" class="form-label required-field text-uppercase text-muted mb-2">
                            Buscar Cliente <span class="text-muted">*</span>
                        </label>
                        <select class="form-select select2" id="client_id" name="client_id" required>
                            <option value="">Buscar por nombre, documento o código...</option>
                            @if ($modoEdicion && $interaction->client_id)
                                <option value="{{ $interaction->client_id }}" selected>{{ $interaction->client->nom_ter }}</option>
                            @endif
                        </select>
                        <div id="error-client-msg" class="text-danger small mt-2" style="display:none;">
                            Debes seleccionar un cliente.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-white border p-3 d-flex flex-column justify-content-center h-100" style="border-radius: 4px;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="lh-sm">
                                    <div class="small text-muted" style="font-size: 0.70rem;">AGENTE RESPONSABLE</div>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 150px;" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                                </div>
                            </div>
                            
                            <div class="border-top pt-2 mt-1 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.70rem;">DURACIÓN</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <span id="timer-indicator" class="me-2" style="width: 8px; height: 8px; background-color: #333; border-radius: 50%;"></span>
                                        <div class="fw-bold text-dark font-monospace fs-5 lh-1" id="timer">00:00</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" id="btn-timer-toggle" title="Pausar/Reanudar">
                                        <i class="bi bi-pause"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" id="btn-timer-reset" title="Reiniciar">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
                        <input type="hidden" name="duration" id="duration" value="0">
                    </div>

                    <div id="client-info-card" class="col-12 mt-4" style="display:none;">
                        <div class="border p-4 bg-light" style="border-radius: 4px; border-left: 3px solid var(--text-main) !important;">
                            <div class="row align-items-center" id="client-info-content">
                                <div class="col-md-5 border-end border-secondary border-opacity-10">
                                    <div class="d-flex align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1" id="info-nombre">Nombre Cliente</h6>
                                            <div class="text-muted small">ID: <span id="info-id" class="text-dark">-</span></div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="smart-tag px-2 py-1 small border"><span id="info-categoria">Cat</span></span>
                                        <span class="smart-tag px-2 py-1 small border"><span id="info-distrito">Zona</span></span>
                                    </div>
                                </div>
                                <div class="col-md-7 ps-md-4 mt-3 mt-md-0">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block mb-0">Email</label>
                                            <span class="text-dark small" id="info-email">...</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="small text-muted d-block mb-0">Teléfono</label>
                                            <span class="text-dark small" id="info-telefono">...</span>
                                        </div>
                                        <div class="col-12">
                                            <label class="small text-muted d-block mb-0">Dirección</label>
                                            <span class="text-dark small" id="info-direccion">...</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-2 pt-2 border-top">
                                        <a id="btn-editar-cliente" href="#" target="_blank" class="btn btn-sm btn-light border text-dark">Editar</a>
                                        <a id="btn-ver-cliente" href="#" target="_blank" class="btn btn-sm btn-dark">Ver Ficha</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="category-container mb-4">
                <div class="category-header p-3 d-flex justify-content-between align-items-center cursor-pointer"
                    data-bs-toggle="collapse" data-bs-target="#historyCollapse" aria-expanded="false"
                    aria-controls="historyCollapse" style="user-select: none;">
                    <h5 class="category-title mb-0">
                        Historial del Cliente
                    </h5>
                    <i class="bi bi-chevron-down text-muted accordion-arrow transition-all"></i>
                </div>

                <div class="collapse" id="historyCollapse">
                    <div class="category-content p-4">
                        <div id="history-content" class="position-relative bg-white border p-3" style="border-radius: 4px;">
                            <div class="timeline-container">
                                <div id="interaction-history-list" class="history-list pe-2" style="max-height: 300px; overflow-y: auto; min-height: 100px;">
                                    
                                    <div id="history-loading-skeleton" class="py-3 px-2 text-muted small">
                                        Cargando historial...
                                    </div>

                                    <div id="history-empty-state" class="text-center py-4 d-none">
                                        <p class="text-muted small mb-0">No se encontraron interacciones previas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="category-container mb-4">
                <div class="category-header p-3 d-flex justify-content-between align-items-center cursor-pointer"
                    data-bs-toggle="collapse" data-bs-target="#paramCollapse" aria-expanded="true"
                    aria-controls="paramCollapse" style="user-select: none;">
                    <h5 class="category-title mb-0">
                        Parametrización
                    </h5>
                    <i class="bi bi-chevron-down text-muted accordion-arrow transition-all"></i>
                </div>

                <div class="collapse show" id="paramCollapse">
                    <div class="category-content p-4">
                        
                        <div class="mb-4" id="container-channel">
                            <label class="form-label text-uppercase text-muted mb-2 d-block">
                                Canal de Entrada <span class="text-muted">*</span>
                            </label>
                            <div class="grid-gallery d-flex flex-wrap gap-2">
                                @foreach ($channels as $channel)
                                    <div class="position-relative">
                                        <input type="radio" class="btn-check" name="interaction_channel" id="ch_{{ $channel->id }}" value="{{ $channel->id }}"
                                            {{ old('interaction_channel', $interaction->interaction_channel ?? '') == $channel->id ? 'checked' : '' }} required>
                                        <label class="smart-tag py-1 px-3 border cursor-pointer text-center transition-all" for="ch_{{ $channel->id }}">
                                            <span class="small">{{ $channel->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('interaction_channel') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-2" id="container-type">
                            <label class="form-label text-uppercase text-muted mb-2 d-block">
                                Motivo / Tipificación <span class="text-muted">*</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($types as $type)
                                    <input type="radio" class="btn-check" name="interaction_type" id="tp_{{ $type->id }}" value="{{ $type->id }}"
                                        {{ old('interaction_type', $interaction->interaction_type ?? '') == $type->id ? 'checked' : '' }} required>
                                    <label class="smart-tag py-1 px-3 border cursor-pointer transition-all small" for="tp_{{ $type->id }}">
                                        {{ $type->name }}
                                    </label>
                                @endforeach
                            </div>
                            @error('interaction_type') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="category-container mb-4">
                <div class="category-header p-3">
                    <h5 class="category-title mb-1">Asignación y Contexto</h5>
                </div>

                <div class="category-content p-4">
                    <div class="mb-4">
                        <label class="form-label text-muted mb-3 d-block">¿Quién es el responsable de esta gestión?</label>
                        
                        <div class="d-flex gap-3 mb-4">
                            <div>
                                <input type="radio" class="btn-check" name="handled_by_agent" id="handled_by_me" value="yes" checked>
                                <label class="btn btn-outline-dark px-4 py-2" for="handled_by_me">
                                    Yo me encargo
                                </label>
                            </div>
                            <div>
                                <input type="radio" class="btn-check" name="handled_by_agent" id="handled_by_other" value="no">
                                <label class="btn btn-outline-secondary px-4 py-2" for="handled_by_other">
                                    Delegar a otro
                                </label>
                            </div>
                        </div>

                        <div id="panel-me">
                            <div class="bg-light border px-3 py-2 rounded">
                                <p class="text-dark small mb-0">Asignado a ti: 
                                    <strong>{{ $idAreaAgente ? $areas[$idAreaAgente] ?? 'Área' : 'Sin Área' }}</strong> - 
                                    <strong>{{ $idCargoAgente ? $cargos[$idCargoAgente] ?? 'Cargo' : 'Sin Cargo' }}</strong>
                                </p>
                            </div>
                        </div>

                        <div id="panel-other" style="display: none;">
                            <div class="bg-white border rounded p-3">
                                <label for="id_user_asignacion" class="form-label small">Buscar Usuario <span class="text-muted">*</span></label>
                                <select class="form-select" id="id_user_asignacion" name="id_user_asignacion" required>
                                    <option value="">Buscar usuario por nombre o correo...</option>
                                    @if(isset($interaction) && $interaction->usuarioAsignado)
                                        <option value="{{ $interaction->id_user_asignacion }}" selected>
                                            {{ $interaction->usuarioAsignado->name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <label for="id_linea_de_obligacion" class="form-label text-uppercase text-muted mb-2">Línea de Obligación <span class="text-muted">*</span></label>
                            <select class="form-select select2" id="id_linea_de_obligacion" name="id_linea_de_obligacion" required>
                                <option value="">Selecciona la línea...</option>
                                @if (isset($lineasCredito)) @foreach ($lineasCredito as $id => $nombre) <option value="{{ $id }}">{{ $nombre }}</option> @endforeach @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end pt-2">
                <button type="button" class="btn btn-dark px-4 py-2" id="btn-siguiente-paso">
                    Continuar
                </button>
            </div>

        </div>

        <div class="tab-panel" id="resultado-tab">
            
            <div class="category-container mb-4 p-4">
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Cierre de Gestión</h5>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="border h-100 bg-white" style="border-radius: 4px;">
                            <div class="p-4">
                                <label class="form-label text-muted text-uppercase mb-3">
                                    Resultado de Interacción <span class="text-muted">*</span>
                                </label>
                                <div class="row g-2">
                                    @foreach ($outcomes as $outcome)
                                        <div class="col-sm-6">
                                            <input type="radio" class="btn-check outcome-radio" name="outcome" id="outcome_{{ $outcome->id }}" value="{{ $outcome->id }}"
                                                data-requires-planning="{{ in_array($outcome->id, [2]) ? 'true' : 'false' }}"
                                                {{ old('outcome', $interaction->outcome ?? '') == $outcome->id ? 'checked' : '' }} required>
                                            <label class="btn btn-outline-secondary w-100 text-start bg-white small" for="outcome_{{ $outcome->id }}">
                                                {{ $outcome->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('outcome')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6" id="planning-section" style="display:none;">
                        <div class="border h-100 bg-light" style="border-radius: 4px;">
                            <div class="p-4">
                                <div class="mb-3 border-bottom pb-2">
                                    <h6 class="text-dark mb-0">Agenda de Seguimiento</h6>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted">Acción a realizar</label>
                                        <div class="row g-2">
                                            @foreach ($nextActions as $action)
                                                <div class="col-6 col-sm-4">
                                                    <input type="radio" class="btn-check" name="next_action_type" id="action_{{ $action->id }}" value="{{ $action->id }}"
                                                        {{ old('next_action_type', $interaction->next_action_type ?? '') == $action->id ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-dark w-100 border small text-truncate" for="action_{{ $action->id }}" title="{{ $action->name }}">
                                                        {{ $action->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @error('next_action_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="next_action_date" class="form-label small text-muted">Fecha y Hora Programada</label>
                                    <input type="datetime-local" class="form-control @error('next_action_date') is-invalid @enderror"
                                        id="next_action_date" name="next_action_date" value="{{ old('next_action_date', $interaction->next_action_date ?? '') }}">
                                    
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addDays(1)">Mañana</button>
                                        <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addDays(3)">En 3 días</button>
                                        <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addDays(7)">En 1 sem</button>
                                    </div>
                                    @error('next_action_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group mb-0">
                                    <label for="next_action_notes" class="form-label small text-muted">Instrucciones</label>
                                    <textarea class="form-control bg-white @error('next_action_notes') is-invalid @enderror"
                                        id="next_action_notes" name="next_action_notes" rows="2" style="resize: vertical;"
                                        placeholder="Ej. Llamar para confirmar..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="category-container mb-4 p-4">
                <div class="mb-4 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Documentación de Soporte (Opcional)</h5>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label text-muted d-block">Archivo Físico</label>
                        <div class="drop-zone-pro position-relative bg-light p-4 text-center cursor-pointer" id="drop-zone" style="border-radius: 4px;">    
                            <input type="file" id="attachment" name="attachment" class="drop-zone-input position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer @error('attachment') is-invalid @enderror" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" onchange="handleFileSelect(this)">
                            <div class="dz-message d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;" id="upload-icon-wrapper">
                                <h6 class="text-dark mb-1" id="file-label">Adjuntar archivo</h6>
                                <p class="text-muted small mb-0">PDF, Word, Excel, Img (Max 10MB)</p>
                            </div>
                        </div>

                        @error('attachment')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <div id="image-preview-wrapper" class="mt-3 text-center" style="display: none;">
                            <img id="img-preview-element" class="img-fluid border p-1" style="max-height: 100px; object-fit: contain;" alt="Vista Previa">
                        </div>

                        @if ($modoEdicion && $interaction->attachment_urls)
                            <div class="mt-3">
                                <label class="form-label small text-dark">Archivo Guardado</label>
                                <div class="d-flex align-items-center p-2 bg-light border">
                                    <div class="flex-grow-1 overflow-hidden me-2">
                                        <div class="text-dark text-truncate small">{{ basename($interaction->attachment_urls) }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('interactions.download', basename($interaction->attachment_urls)) }}" class="btn btn-sm btn-light border" target="_blank">Descargar</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label text-muted d-block">Enlace Externo (URL)</label>
                        <input type="url" class="form-control @error('interaction_url') is-invalid @enderror" id="interaction_url" name="interaction_url" placeholder="https://..." value="{{ old('interaction_url', $interaction->interaction_url ?? '') }}">
                        @error('interaction_url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="category-container mb-4">
                <div class="category-header p-3 bg-light d-flex justify-content-between align-items-center">
                    <h5 class="category-title mb-0">Vincular a Caso Anterior</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-history" title="Recargar lista">
                        Actualizar
                    </button>
                </div>

                <div class="category-content p-0">
                    
                    <div id="no-client-selected" class="text-center py-4 bg-white">
                        <p class="text-muted small mb-0">Busca un cliente en la pestaña "Principal" para cargar su historial.</p>
                    </div>

                    <div id="history-section" style="display:none;">
                        <input type="hidden" id="parent_interaction_id" name="parent_interaction_id" value="">

                        <div class="p-3 bg-white border-bottom">
                            <div id="selected-parent-info" class="bg-light border p-2 d-flex align-items-center justify-content-between" style="display:none; border-radius: 4px;">
                                <div class="small">
                                    <strong class="text-dark">Escalando de:</strong> <span id="selected-parent-text" class="text-muted">...</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-light border text-danger" id="clear-parent-selection">
                                    Desvincular
                                </button>
                            </div>
                        </div>

                        <div id="history-content" class="p-3 bg-white">
                            <div id="interaction-history-list-bottom" class="history-list pe-2" style="max-height: 250px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <label class="form-label text-muted d-block">
                        Notas Finales <span class="text-muted">*</span>
                    </label>
                    <textarea class="form-control bg-white" name="notes" id="notes" rows="4"
                        placeholder="Resumen de la interacción..." required>{{ old('notes', $interaction->notes ?? '') }}</textarea>
                    @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between pt-3 border-top">
                <button type="button" class="btn btn-light border px-4" onclick="document.querySelector('[data-tab=\'principal\']').click()">
                    Volver
                </button>
                
                <div class="d-flex gap-2">
                    <button id="clear-draft" type="button" class="btn btn-light border text-danger px-3">
                        Borrar Borrador
                    </button>

                    <button type="submit" id="btn-submit-interaccion" class="btn btn-dark px-4">
                        {{ $modoEdicion ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>

        </div>

        <div id="loading-overlay" class="d-none" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="spinner-border text-dark" role="status"></div>
            <div class="mt-2 small text-dark">Procesando...</div>
        </div>

        <div id="ajax-loader" class="ajax-loader" aria-hidden="true" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.9);z-index:9999;align-items:center;justify-content:center;">
            <div class="text-center p-4">
                <div class="spinner-border text-dark mb-2" role="status"></div>
                <div class="small text-muted">Consultando base de datos...</div>
            </div>
        </div>

    </div>
</form> 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
// Exponer función global para calcular fechas automáticas de la agenda
window.addDays = function(days) {
    const date = new Date();
    // Sumar los días solicitados
    date.setDate(date.getDate() + days);
    
    // Ajustar a la zona horaria local para el input datetime-local
    const tzOffset = (new Date()).getTimezoneOffset() * 60000;
    const localISOTime = (new Date(date - tzOffset)).toISOString().slice(0, 16);
    
    // Asignar el valor al input y limpiar posibles errores visuales
    const $dateInput = $('#next_action_date');
    $dateInput.val(localISOTime).trigger('change');
    $dateInput.removeClass('is-invalid input-error').addClass('is-valid');
};

/**
 * =========================================================================
 * APP DE INTERACCIONES CRM
 * Arquitectura Modular Escalable
 * =========================================================================
 */
const CRMApp = (function($) {
    'use strict';

    // =====================================================================
    // 1. CONFIGURACIÓN Y SELECTORES (DOM Cache)
    // =====================================================================
    const CONFIG = {
        storageKey: 'interaction_form_draft_v1',
        urls: {
            searchClients: @json(route('interactions.search-clients') ?? ''),
            // La ruta cliente show se genera dinámicamente en HistoryManager
        }
    };

    const DOM = {
        form: '#interaction-form',
        loader: '#ajax-loader',
        overlay: '#loading-overlay',
        timer: '#timer',
        durationInp: '#duration',
        durationDisp: '#duration-display',
        startTimeInp: '#start_time',
        
        // Pestañas y Navegación
        btnSiguiente1: '#btn-siguiente-paso',
        btnSiguiente2: '#btn-paso2-siguiente',
        btnSubmit: '#btn-submit-interaccion',
        
        // Clientes y Terceros
        clientSelect: '#client_id',
        clientCard: '#client-info-card',
        thirdFields: '#third-party-fields',
        thirdPartyCheck: '#caller_third',
        
        // Paso 2 (Responsabilidad)
        handledByMe: '#handled_by_me',
        handledByOther: '#handled_by_other',
        panelMe: '#panel-me',
        panelOther: '#panel-other',
        
        // Selects Paso 2
        areaSelect: '#id_area_de_asignacion',
        cargoSelect: '#id_cargo_asignacion',
        lineaSelect: '#id_linea_de_obligacion',
        distritoSelect: '#id_distrito_interaccion',
        
        // Sincronización
        btnSyncFrom: '#sync-from-client-btn',
        btnSyncTo: '#sync-to-client-btn',
        
        // Historial y Planificación
        outcome: '#outcome',
        planningSection: '#planning-section',
        historySection: '#history-section',
        historyList: '#interaction-history-list',
        parentIdInp: '#parent_interaction_id'
    };

    let state = {
        startTimeInterval: null,
        startTime: null,
        // INYECTADO: Variables para la pausa del cronómetro
        accumulatedTime: 0,
        isPaused: false
    };

    // =====================================================================
    // 2. MÓDULO DE INTERFAZ (UI Helpers)
    // =====================================================================
    const UI = {
        showLoader: () => $(DOM.loader).show(),
        hideLoader: () => $(DOM.loader).hide(),
        
        markError: function(element, isWrapper = false) {
            const $el = $(element);
            if (isWrapper || $el.hasClass('grid-gallery') || $el.hasClass('bg-light')) {
                $el.addClass(isWrapper ? 'sync-wrapper-error' : 'container-error');
            } else if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').addClass('select2-error');
            } else {
                $el.addClass('input-error is-invalid').removeClass('is-valid');
            }
        },

        cleanError: function(element, isWrapper = false) {
            const $el = $(element);
            if (isWrapper || $el.hasClass('grid-gallery') || $el.hasClass('bg-light')) {
                $el.removeClass('sync-wrapper-error container-error');
            } else if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').removeClass('select2-error');
            } else {
                $el.removeClass('input-error is-invalid').addClass('is-valid');
            }
        },

        showTab: function(tabId) {
            $('.tab-button').removeClass('active');
            $(`.tab-button[data-tab="${tabId}"]`).addClass('active');
            $('.tab-panel').removeClass('active');
            $(`#${tabId}-tab`).addClass('active');

            // INYECTADO: Animación y cálculo de la Barra de Progreso
            const progress = tabId === 'principal' ? 50 : 100;
            $('#form-progress').css('width', progress + '%').attr('aria-valuenow', progress);

            if (tabId === 'resultado') HistoryManager.updateVisibility();
            if (typeof updateProgress === 'function') updateProgress();
            DraftManager.save();
        },

        handleFileSelect: function(input) {
            const label = document.getElementById('file-label');
            const iconWrapper = document.getElementById('upload-icon-wrapper');
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewImg = document.getElementById('img-preview-element');
            const dropZone = document.getElementById('drop-zone');

            if (input.files && input.files[0]) {
                label.innerText = input.files[0].name;
                label.classList.add('text-success');
                dropZone.style.borderColor = '#059669';
                dropZone.style.backgroundColor = '#f0fff4';

                if (input.files[0].type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewImg.src = e.target.result;
                        previewWrapper.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewWrapper.style.display = 'none';
                }
            } else {
                label.innerText = 'Adjuntar archivo';
                label.classList.remove('text-success');
                dropZone.style.borderColor = '#d1d5db';
                dropZone.style.backgroundColor = '#f9fafb';
                previewWrapper.style.display = 'none';
            }
        }
    };

    // =====================================================================
    // 3. MÓDULO DE CLIENTES Y TERCEROS
    // =====================================================================
    const ClientManager = {
        toggleThirdParty: function() {
            const isThird = $(DOM.thirdPartyCheck).is(':checked');
            $(DOM.thirdFields).toggle(isThird);

            if (!isThird) {
                // Limpiar errores
                ['#nombre_quien_llama', '#cedula_quien_llama', '#celular_quien_llama'].forEach(s => UI.cleanError(s));
                UI.cleanError('#container-parentezco', true);
            }
        },

        generateAvatar: function(name) {
            const colors = ['#6B9BD1', '#7FA9D3', '#93B7D5', '#A7C5D7', '#BBD3D9'];
            const initials = name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2);
            const colorIndex = name.charCodeAt(0) % colors.length;
            return { initials: initials || '—', color: colors[colorIndex] };
        },

        updateClientCard: function(clientId) {
            if (!clientId) {
                $(DOM.clientCard).fadeOut();
                $(DOM.historySection).fadeOut();
                Timer.reset();
                return;
            }

            UI.cleanError(DOM.clientSelect);
            $('#error-client-msg').hide();
            Timer.start();
            UI.showLoader();

            // Cargar datos
            const url = @json(route('interactions.cliente.show', ['cod_ter' => ':cod_ter']) ?? '').replace(':cod_ter', clientId);
            
            $(DOM.historyList).html('<div class="text-center py-4 small"><div class="spinner-border spinner-border-sm text-dark"></div> Cargando datos...</div>');

            $.ajax({ url: url, type: 'GET', dataType: 'json', timeout: 15000 })
                .done(data => {
                    if (data.error) throw new Error(data.error);

                    const avatar = ClientManager.generateAvatar(data.nom_ter || '');
                    $('#info-nombre').text(data.nom_ter || 'No registrado');
                    $('#info-id').text(`${data.cod_ter || 'N/A'}`);
                    $('#info-distrito').text(data.distrito?.NOM_DIST || 'No registrado');
                    $('#info-categoria').text(data.maeTipos?.nombre || 'No registrado');
                    $('#info-email').text(data.email || 'No registrado');
                    $('#info-telefono').text(data.tel1 || 'No registrado');
                    $('#info-direccion').text(data.dir || 'No registrado');
                    
                    $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`);
                    $('#btn-ver-cliente').attr('href', `/maestras/terceros/${data.cod_ter}`);

                    HistoryManager.renderFull(data.history);
                    $(DOM.clientCard).fadeIn();
                    ClientManager.toggleThirdParty();
                    toastr.success('Información cargada');
                })
                .fail(HistoryManager.handleAjaxError)
                .always(() => UI.hideLoader());
        }
    };

    // =====================================================================
    // 4. MÓDULO DEL CRONÓMETRO (MODIFICADO PARA PAUSA/REANUDAR)
    // =====================================================================
    const Timer = {
        start: function(resume = false) {
            if (!resume) {
                this.stop();
                state.startTime = new Date();
                state.accumulatedTime = 0;
                $(DOM.startTimeInp).val(state.startTime.toISOString());
            } else {
                state.startTime = new Date(); // Resetear el tiempo base para el nuevo segmento
            }
            
            state.isPaused = false;
            $('#timer-indicator').css({'background-color': '#059669'});
            $('#btn-timer-toggle').html('<i class="bi bi-pause"></i>').attr('title', 'Pausar');
            
            state.startTimeInterval = setInterval(() => {
                if (!state.isPaused) {
                    const now = new Date();
                    const diff = Math.round((now - state.startTime) / 1000) + state.accumulatedTime;
                    this.updateDisplay(diff);
                }
            }, 1000);
        },
        pause: function() {
            if (state.isPaused) {
                this.start(true); // Reanudar
            } else {
                state.isPaused = true;
                clearInterval(state.startTimeInterval);
                const now = new Date();
                state.accumulatedTime += Math.round((now - state.startTime) / 1000);
                
                $('#timer-indicator').css({'background-color': '#D97706'});
                $('#btn-timer-toggle').html('<i class="bi bi-play"></i>').attr('title', 'Reanudar');
            }
        },
        stop: function() {
            if (state.startTimeInterval) clearInterval(state.startTimeInterval);
            state.startTimeInterval = null;
        },
        reset: function() {
            this.stop();
            state.startTime = null;
            state.accumulatedTime = 0;
            state.isPaused = false;
            $(DOM.startTimeInp).val('');
            this.updateDisplay(0);
            $('#timer-indicator').css({'background-color': '#333'});
            $('#btn-timer-toggle').html('<i class="bi bi-pause"></i>').attr('title', 'Pausar');
        },
        updateDisplay: function(seconds) {
            $(DOM.durationInp).val(seconds);
            $(DOM.durationDisp).val(`${seconds} segundos`);
            
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            $(DOM.timer).text(`${mins < 10 ? '0'+mins : mins}:${secs < 10 ? '0'+secs : secs}`);
        }
    };

    // =====================================================================
    // 5. MÓDULO DE HISTORIAL Y ESCALAMIENTO
    // =====================================================================
    const HistoryManager = {
        updateVisibility: function() {
            if ($(DOM.clientSelect).val()) {
                $('#no-client-selected').hide();
                $(DOM.historySection).show();
            } else {
                $('#no-client-selected').show();
                $(DOM.historySection).hide();
            }
        },

        renderFull: function(historyData) {
            const $list = $(DOM.historyList);
            const $listBottom = $('#interaction-history-list-bottom');
            
            $list.empty();
            if($listBottom.length) $listBottom.empty();

            if (historyData && historyData.length > 0) {
                const html = historyData.map(item => this.buildCardHtml(item)).join('');
                $list.html(html);
                if($listBottom.length) $listBottom.html(html);
                
                $(DOM.historySection).fadeIn();
                this.restoreParentSelection();
            } else {
                const emptyHtml = `<div class="text-center py-4 text-muted small"><p>No hay interacciones previas.</p></div>`;
                $list.html(emptyHtml);
                if($listBottom.length) $listBottom.html(emptyHtml);
            }
        },

        buildCardHtml: function(item) {
            const url = `/interactions/${item.id}/show`;
            const isSuccess = ['Exitoso', 'Resuelto', 'Cerrado', 'Venta', 'Finalizado'].includes(item.outcome);
            const isPending = ['Pendiente', 'Seguimiento', 'En Proceso', 'Abierto'].includes(item.outcome);
            const badgeColor = isSuccess ? '#059669' : (isPending ? '#D97706' : '#6b7280');
            
            const safeNotes = item.notes ? $('<div>').text(item.notes).html() : '<em>Sin notas.</em>';

            return `
                <div class="history-item mb-3 timeline-item" id="history-item-${item.id}">
                    <div class="border" style="border-radius: 4px;">
                        <div class="bg-light d-flex justify-content-between align-items-center py-2 px-3 collapsed" 
                             data-bs-toggle="collapse" data-bs-target="#collapseInteraction-${item.id}" style="cursor:pointer">
                            <div class="d-flex align-items-center gap-2 small">
                                <strong>ID: ${item.id}</strong>
                                <span style="color: ${badgeColor}; font-weight: bold;">[${item.outcome}]</span>
                                <span class="text-muted border-start ps-2">${item.type}</span>
                            </div>
                            <small class="text-muted">${item.date}</small>
                        </div>
                        <div id="collapseInteraction-${item.id}" class="collapse">
                            <div class="p-3 bg-white border-top">
                                <div class="mb-2">
                                    <div class="small text-dark">${safeNotes}</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-end pt-2 border-top mt-2">
                                    <div class="small text-muted">Gestor: <strong>${item.agent}</strong></div>
                                    <div class="d-flex gap-2">
                                        <a href="${url}" target="_blank" class="text-primary small text-decoration-none">Ver caso</a>
                                        <a href="javascript:void(0)" class="text-dark small text-decoration-none fw-bold btn-escalate" 
                                           data-id="${item.id}" data-info="${item.type} - ${item.date}">Vincular</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        selectParent: function(id, info) {
            $(DOM.parentIdInp).val(id);
            $('#selected-parent-text').text(info);
            $('#selected-parent-info').fadeIn();
            
            $('.history-item > div').removeClass('border-dark');
            $(`.history-list #history-item-${id} > div`).addClass('border-dark');
            
            DraftManager.save();
            toastr.info('Interacción vinculada');
        },

        clearParent: function() {
            $(DOM.parentIdInp).val('');
            $('#selected-parent-info').fadeOut();
            $('.history-item > div').removeClass('border-dark');
            DraftManager.save();
        },

        restoreParentSelection: function() {
            const parentId = $(DOM.parentIdInp).val();
            if (parentId) {
                const $items = $(`.history-list #history-item-${parentId} > div`);
                if ($items.length) {
                    $items.addClass('border-dark');
                    $('#selected-parent-info').show();
                }
            }
        },

        handleAjaxError: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            const msg = xhr.responseJSON?.error || (status === 'timeout' ? 'Timeout.' : 'Error de conexión.');
            Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#111827' });
        }
    };

    // =====================================================================
    // 6. MÓDULO DE BORRADORES (Auto-Guardado)
    // =====================================================================
    const DraftManager = {
        debounceTimer: null,
        save: function() {
            try {
                const data = {};
                $(DOM.form).find('input,textarea,select').each(function() {
                    if (!this.name || this.type === 'file') return;
                    if (this.type === 'checkbox' || this.type === 'radio') {
                        if (this.checked) data[this.name] = this.value;
                    } else {
                        data[this.name] = $(this).val();
                    }
                });
                data.activeTab = $('.tab-button.active').data('tab');
                localStorage.setItem(CONFIG.storageKey, JSON.stringify(data));
            } catch (e) { console.warn('Borrador no guardado', e); }
        },

        debouncedSave: function() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.save(), 700);
        },

        load: function() {
            const raw = localStorage.getItem(CONFIG.storageKey);
            if (!raw) return;
            try {
                const data = JSON.parse(raw);
                Object.keys(data).forEach(name => {
                    if (name === 'activeTab') return;
                    const $el = $(DOM.form).find(`[name="${name}"]`);
                    if ($el.is('select')) $el.val(data[name]).trigger('change');
                    else if ($el.length) $el.val(data[name]);
                });

                if (data.start_time && data.client_id) {
                    state.startTime = new Date(data.start_time);
                    Timer.start(); // Retoma cronómetro
                }

                if (data.activeTab) UI.showTab(data.activeTab);
            } catch(e) {}
        },

        clear: function() {
            localStorage.removeItem(CONFIG.storageKey);
        }
    };

    // =====================================================================
    // 7. MÓDULO DE VALIDACIÓN Y SINCRONIZACIÓN (Form Validator)
    // =====================================================================
    const Validation = {
        toggleResponsibility: function() {
            UI.cleanError(DOM.areaSelect);
            UI.cleanError(DOM.cargoSelect);

            if ($(DOM.handledByMe).is(':checked')) {
                $(DOM.panelMe).show();
                $(DOM.panelOther).hide();
            } else {
                $(DOM.panelMe).hide();
                $(DOM.panelOther).show();
            }
        },

        validateStep1: function() {
            let isValid = true;
            let firstErr = null;

            // 1. Cliente
            if (!$(DOM.clientSelect).val()) {
                UI.markError(DOM.clientSelect);
                $('#error-client-msg').show();
                isValid = false;
                firstErr = firstErr || $(DOM.clientSelect).parent();
            }

            // 2. Tercero
            if ($(DOM.thirdPartyCheck).is(':checked')) {
                ['#nombre_quien_llama', '#cedula_quien_llama', '#celular_quien_llama'].forEach(id => {
                    if (!$(id).val().trim()) { UI.markError(id); isValid = false; firstErr = firstErr || $(id); }
                });
                if (!$('input[name="parentezco_quien_llama"]:checked').length) {
                    UI.markError('#container-parentezco', true);
                    isValid = false; firstErr = firstErr || $('#container-parentezco');
                }
            }

            // 3. Canal y Tipo
            if (!$('input[name="interaction_channel"]:checked').length) {
                UI.markError('#container-channel .grid-gallery', true);
                isValid = false; firstErr = firstErr || $('#container-channel');
            }
            if (!$('input[name="interaction_type"]:checked').length) {
                UI.markError('#container-type', true);
                isValid = false; firstErr = firstErr || $('#container-type');
            }
            
            // 4. Asignación (Linea Obligación)
            if (!$(DOM.lineaSelect).val()) { 
                UI.markError(DOM.lineaSelect); 
                isValid = false; 
                firstErr = firstErr || $(DOM.lineaSelect).parent(); 
            }

            // 5. Asignación a Otro Usuario
            if ($(DOM.handledByOther).is(':checked')) {
                if (!$('#id_user_asignacion').val()) { 
                    UI.markError('#id_user_asignacion'); 
                    isValid = false; 
                    firstErr = firstErr || $('#id_user_asignacion').parent(); 
                }
            }

            return { isValid, firstErr };
        },
        
        submitForm: function(e) {
            e.preventDefault();
            Timer.stop(); // Fija la duración final

            // Validar HTML5 Nativo
            if (!$(DOM.form)[0].checkValidity()) {
                $(DOM.form)[0].reportValidity();
                return;
            }

            Swal.fire({
                title: 'Confirmar guardado',
                text: "¿Deseas guardar la interacción ahora?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#111827',
                cancelButtonColor: '#d1d5db'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-overlay').removeClass('d-none');
                    $(DOM.btnSubmit).prop('disabled', true).text('Guardando...');
                    DraftManager.clear();
                    $(DOM.form).off('submit').submit();
                } else {
                    Timer.start(true); // Si cancela, reanuda el cronómetro
                }
            });
        }
    };

    const SyncManager = {
        syncFromClient: function() {
            const clientId = $(DOM.clientSelect).val();
            if (!clientId) return Swal.fire('Aviso', 'Selecciona un cliente.', 'warning');
            
            const btn = $(DOM.btnSyncFrom);
            const ogHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span>');

            setTimeout(() => {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Datos cargados', showConfirmButton: false, timer: 3000 });
                btn.html(ogHtml);
            }, 800);
        },
        syncToClient: function() {
            const clientId = $(DOM.clientSelect).val();
            const distId = $(DOM.distritoSelect).val();
            
            if (!clientId) return Swal.fire('Aviso', 'Falta cliente', 'warning');
            if (!distId) return Swal.fire('Aviso', 'Selecciona dato a guardar', 'warning');

            Swal.fire({
                title: 'Actualizar', text: '¿Guardar este dato permanentemente en el cliente?',
                icon: 'question', showCancelButton: true, confirmButtonText: 'Sí', confirmButtonColor: '#111827'
            }).then(res => {
                if(res.isConfirmed) {
                    const btn = $(DOM.btnSyncTo);
                    const ogHtml = btn.html();
                    btn.html('<span class="spinner-border spinner-border-sm"></span>');
                    setTimeout(() => {
                        Swal.fire('Guardado', 'Actualizado con éxito', 'success');
                        btn.html(ogHtml);
                    }, 1000);
                }
            });
        }
    };

    // =====================================================================
    // 8. INICIALIZADOR Y EVENT BINDING
    // =====================================================================
    function initPlugins() {
        // 1. Select2 básicos
        $('.select2').select2({ theme: 'bootstrap-5', dropdownParent: $('body') });
        
        // 2. Buscador de Clientes
        $(DOM.clientSelect).select2({
            theme: 'bootstrap-5', placeholder: 'Buscar...', allowClear: true, minimumInputLength: 2,
            ajax: {
                url: CONFIG.urls.searchClients, dataType: 'json', delay: 250,
                data: p => ({ q: p.term, page: p.page || 1 }),
                processResults: d => ({ results: d.results.map(i => ({ id: i.cod_ter, text: `${i.cod_ter} - ${i.apl1} ${i.apl2} ${i.nom1} ${i.nom2}` })), pagination: { more: d.pagination.more }})
            }
        });

        // 3. Buscador de Usuarios para Delegar
        $('#id_user_asignacion').select2({
            theme: 'bootstrap-5', placeholder: 'Buscar usuario...', allowClear: true, minimumInputLength: 3,
            ajax: {
                url: '/interactions/search-users', 
                dataType: 'json', delay: 250,
                data: p => ({ q: p.term, page: p.page || 1 }),
                processResults: d => ({ results: d.results, pagination: { more: d.pagination.more }})
            },
            language: {
                inputTooShort: function() { return 'Ingresa 3 letras...'; },
                noResults: function() { return 'No hay usuarios.'; },
                searching: function() { return 'Buscando...'; }
            }
        });
        
        toastr.options = { "positionClass": "toast-bottom-right", "timeOut": "2500" };
    }

    function bindEvents() {
        // Navegación Tabs
        $('.tab-button').on('click', function() { UI.showTab($(this).data('tab')); });
        
        // Auto-Guardado y Limpieza Visual
        $(DOM.form).on('input change', 'input, textarea, select', function() {
            UI.cleanError(this);
            DraftManager.debouncedSave();
        });
        
        // Select2 Limpieza visual
        $(DOM.form).on('select2:select', '.select2', function() { UI.cleanError(this); });
        
        // Eventos Paso 1 y Continuar
        $(DOM.clientSelect).on('change', function() { ClientManager.updateClientCard($(this).val()); });
        $('input[name="caller_type"]').on('change', ClientManager.toggleThirdParty);
        $(DOM.btnSiguiente1).on('click', e => { 
            e.preventDefault(); 
            const v = Validation.validateStep1(); 
            v.isValid ? UI.showTab('resultado') : v.firstErr[0].scrollIntoView({behavior: 'smooth', block: 'center'}); 
        });
        
        // Eventos Asignación y Sync
        $(DOM.handledByMe + ', ' + DOM.handledByOther).on('change', Validation.toggleResponsibility);
        $(DOM.btnSyncFrom).on('click', SyncManager.syncFromClient);
        $(DOM.btnSyncTo).on('click', SyncManager.syncToClient);
        
        // Cronómetro
        $('#btn-timer-toggle').on('click', () => Timer.pause());
        $('#btn-timer-reset').on('click', () => Timer.reset());

        // Limpieza de radios complejos
        const radioNames = ['interaction_channel', 'interaction_type', 'parentezco_quien_llama'];
        radioNames.forEach(name => {
            $(`input[name="${name}"]`).on('change', function() {
                const map = { interaction_channel: '#container-channel', interaction_type: '#container-type', parentezco_quien_llama: '#container-parentezco' };
                UI.cleanError($(map[name]).find('.grid-gallery, .bg-light')[0] || map[name], true);
            });
        });

        // Eventos Historial / Planificación
        $('input[name="outcome"]').on('change', function() {
            const val = $(this).next('label').text().trim().toLowerCase();
            const requiresPlanning = $(this).data('requires-planning') === true || $(this).data('requires-planning') === 'true';
            
            if (requiresPlanning || val.includes('pendiente') || val.includes('no contesta') || val.includes('seguimiento')) {
                $('#planning-section').slideDown();
            } else {
                $('#planning-section').slideUp();
            }
        });

        $(document).on('click', '.btn-escalate', function() {
            HistoryManager.selectParent($(this).data('id'), $(this).data('info'));
        });
        $('#clear-parent-selection').on('click', HistoryManager.clearParent);
        $('#refresh-history').on('click', () => ClientManager.updateClientCard($(DOM.clientSelect).val()));
        
        // Submit
        $(DOM.form).on('submit', Validation.submitForm);

        // Limpiar Borrador Botón
        $('#clear-draft').on('click', () => {
            Swal.fire({title: '¿Limpiar?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#111827'}).then(r => {
                if(r.isConfirmed) { DraftManager.clear(); location.reload(); }
            });
        });
    }

    // Método Público de Inicialización
    function init() {
        initPlugins();
        bindEvents();
        Validation.toggleResponsibility();
        DraftManager.load();
        
        // Disparar triggers iniciales
        if(!$(DOM.clientSelect).val()) Timer.reset();
        $('#notes').trigger('input');
        $('input[name="outcome"]:checked').trigger('change');
    }

    // Exportar API Pública
    return {
        init,
        showTab: UI.showTab,
        handleFileSelect: UI.handleFileSelect,
        selectParentInteraction: HistoryManager.selectParent,
        clearParentSelection: HistoryManager.clearParent
    };

})(jQuery);

/**
 * =========================================================================
 * ARRANQUE DE LA APLICACIÓN
 * =========================================================================
 */
document.addEventListener("DOMContentLoaded", CRMApp.init);

// Exponer métodos globales
window.showTab = CRMApp.showTab;
window.handleFileSelect = CRMApp.handleFileSelect;
window.selectParentInteraction = CRMApp.selectParentInteraction;
window.clearParentSelection = CRMApp.clearParentSelection;
</script>