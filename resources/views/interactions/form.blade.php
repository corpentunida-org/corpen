@php
    $modoEdicion = isset($interaction) && $interaction->id;
@endphp

<style>
    :root {
        --pastel-blue: #E6F3FF;
        --pastel-purple: #F5EEFF;
        --pastel-pink: #FCE4EC;
        --pastel-green: #E8F5E8;
        --pastel-yellow: #FFF9E6;
        --pastel-gray: #F8F9FA;
        --pastel-lavender: #F5F0FF;
        --primary-color: #6B9BD1;
        --text-primary: #374151;
        --text-secondary: #6B7280;
        --border-color: #E5E7EB;
    }

    body {
        margin: 0;
        padding: 15px;
        background: linear-gradient(135deg, #FAFBFC 0%, #F3F4F6 100%);
    }

    .card {
        width: 100% !important;
        max-width: none !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .card-header {
        background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--pastel-lavender) 100%) !important;
        color: var(--text-primary) !important;
        padding: 1.25rem !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .progress-section {
        background: var(--pastel-blue);
        padding: 20px 32px;
        border-bottom: 1px solid var(--border-color);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .progress-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .progress-percentage {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary-color);
    }

    .progress-bar-container {
        height: 6px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #60A5FA, #3B82F6);
        border-radius: 3px;
        transition: width 0.6s ease;
        position: relative;
    }

    .progress-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .progress-message {
        font-size: 12px;
        color: var(--text-secondary);
        text-align: center;
    }

    .tab-navigation {
        display: flex;
        background: var(--pastel-gray);
        border-bottom: 1px solid var(--border-color);
    }

    .tab-button {
        flex: 1;
        padding: 20px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        transition: all 0.3s ease;
        position: relative;
    }

    .tab-button i {
        font-size: 20px;
    }

    .tab-button span {
        font-size: 12px;
        font-weight: 500;
    }

    .tab-button.active {
        color: var(--primary-color);
        background: white;
        border-bottom-color: var(--primary-color);
    }

    .tab-button:hover:not(.active) {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.5);
    }

    .form-content {
        padding: 1.5rem;
    }

    .tab-panel {
        display: none;
        animation: fadeIn 0.4s ease;
    }

    .tab-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .category-container {
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .category-header {
        background: linear-gradient(135deg, var(--pastel-gray) 0%, var(--pastel-blue) 20%);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .category-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
    }

    .category-icon {
        width: 32px;
        height: 32px;
        background: var(--primary-color);
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 16px;
    }

    .category-description {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 4px 0 0 44px;
    }

    .category-content {
        padding: 1.5rem;
    }

    .section-divider {
        position: relative;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .section-description {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(107, 155, 209, 0.25);
    }

    .form-floating label {
        color: var(--text-secondary);
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label,
    .form-floating>.form-select~label {
        color: var(--text-primary);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--primary-color) 100%);
        border: none;
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-color) 0%, #5A8AC1 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(107, 155, 209, 0.3);
    }

    .btn-light {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .btn-light:hover {
        background: var(--pastel-gray);
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline-primary:hover {
        background: var(--pastel-blue);
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }

    .btn-outline-secondary:hover {
        background: var(--pastel-gray);
        transform: translateY(-1px);
    }

    .card.border-0.bg-light {
        background: var(--pastel-gray) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 10px !important;
        margin-bottom: 1rem !important;
    }

    .client-avatar {
        background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--primary-color) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 50%;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 58px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(107, 155, 209, 0.25);
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .is-valid {
        border-color: #198754;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .form-content {
            padding: 1rem;
        }

        .category-content {
            padding: 1rem;
        }

        .tab-button span {
            display: none;
        }

        .tab-button {
            padding: 16px;
        }

        .d-flex.justify-content-between.mt-4 {
            flex-direction: column;
            gap: 1rem;
        }

        .d-flex.justify-content-between.mt-4>div {
            width: 100%;
        }

        .d-flex.justify-content-between.mt-4>div>button {
            width: 100%;
        }
    }
</style>

<div class="card shadow-lg border-0 rounded-3 overflow-hidden">
    <div class="card-header bg-gradient-primary text-white">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="icon-box bg-white bg-opacity-20 rounded-3 p-2 me-3">
                    <i class="bi bi-chat-dots fs-4"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-bold">{{ $modoEdicion ? 'Editar Interacción' : 'Nueva Interacción' }}</h4>
                <p class="mb-0 opacity-75 small">
                    {{ $modoEdicion ? 'Modifica la información de la interacción existente' : 'Completa el formulario para registrar una nueva interacción' }}
                </p>
            </div>
            @if ($modoEdicion && $interaction->id)
                <div class="flex-shrink-0">
                    <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-eye me-1"></i> Ver Detalles
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="progress-section">
        <div class="progress-header">
            <span class="progress-title">Progreso del formulario</span>
            <span class="progress-percentage" id="progress-percentage">0%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progress-bar" style="width: 0%"></div>
        </div>
        <div class="progress-message" id="progress-message">Comienza seleccionando un cliente</div>
    </div>

    <div class="tab-navigation">
        <button type="button" class="tab-button active" data-tab="principal">
            <i class="bi bi-info-circle"></i>
            <span>Información Principal</span>
        </button>
        <button type="button" class="tab-button" data-tab="adicional">
            <i class="bi bi-briefcase"></i>
            <span>Información Adicional</span>
        </button>
        <button type="button" class="tab-button" data-tab="resultado">
            <i class="bi bi-check-circle"></i>
            <span>Resultado y Planificación</span>
        </button>
        <button type="button" class="tab-button" data-tab="adjuntos">
            <i class="bi bi-paperclip"></i>
            <span>Adjuntos y Referencias</span>
        </button>
        <button type="button" class="tab-button" data-tab="historial">
            <i class="bi bi-clock-history"></i>
            <span>Historial</span>
        </button>
    </div>

    <div class="form-content">
        <form id="interaction-form"
            action="{{ $modoEdicion ? route('interactions.update', $interaction->id) : route('interactions.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if ($modoEdicion)
                @method('PUT')
            @endif

            <div class="tab-panel active" id="principal-tab">
                <div class="category-container">
                    <div class="category-header">
                        <h5 class="category-title">
                            <div class="category-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            Información Principal
                        </h5>
                        <p class="category-description">Datos esenciales de la interacción y el cliente</p>
                    </div>
                    <div class="category-content">
                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="bi bi-person-badge me-2 text-primary"></i>Información del Cliente
                            </h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <select class="form-select select2 @error('client_id') is-invalid @enderror"
                                        id="client_id" name="client_id" required>
                                        <option value="">Selecciona un cliente</option>
                                        @if ($modoEdicion && $interaction->client_id)
                                            <option value="{{ $interaction->client_id }}" selected>
                                                {{ $interaction->client->nom_ter }} ({{ $interaction->client_id }})
                                            </option>
                                        @endif
                                    </select>
                                    <label for="client_id">Cliente <span class="text-danger">*</span></label>
                                </div>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light"
                                        value="{{ auth()->user()->name }}" readonly>
                                    <label for="agent">Agente</label>
                                </div>
                                <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                                <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
                                <div class="form-text mt-1">
                                    <i class="bi bi-calendar me-1"></i> {{ now()->format('d/m/Y h:i A') }}
                                </div>
                            </div>
                        </div>

                        <div id="client-info-card" class="card border-0 bg-light mb-4" style="display:none;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    <h6 class="mb-0 fw-semibold">Información del Cliente</h6>
                                    <div class="ms-auto">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="toggle-client-info">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="client-info-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="client-avatar me-2" id="info-avatar"
                                                    style="width:40px;height:40px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                                </div>
                                                <div>
                                                    <div id="info-nombre" class="fw-semibold">—</div>
                                                    <div id="info-id" class="text-muted small">ID: —</div>
                                                </div>
                                            </div>
                                            <div class="info-item mb-2">
                                                <i class="bi bi-geo-alt text-muted me-2"></i>
                                                <span id="info-distrito">Cargando...</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <i class="bi bi-tag text-muted me-2"></i>
                                                <span id="info-categoria">Cargando...</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item mb-2">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                <span id="info-email">Cargando...</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                <span id="info-telefono">Cargando...</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <i class="bi bi-geo text-muted me-2"></i>
                                                <span id="info-direccion">Cargando...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a id="btn-editar-cliente" href="#"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil me-1"></i> Ver ficha completa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="bi bi-chat-dots me-2 text-primary"></i>Detalles de la Interacción
                            </h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select
                                        class="form-select select2 @error('interaction_channel') is-invalid @enderror"
                                        id="interaction_channel" name="interaction_channel" required>
                                        <option value="">Selecciona un canal</option>
                                        @foreach ($channels as $channel)
                                            <option value="{{ $channel->id }}"
                                                {{ old('interaction_channel', $interaction->interaction_channel ?? '') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="interaction_channel">Canal <span class="text-danger">*</span></label>
                                </div>
                                @error('interaction_channel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select select2 @error('interaction_type') is-invalid @enderror"
                                        id="interaction_type" name="interaction_type" required>
                                        <option value="">Selecciona un tipo</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('interaction_type', $interaction->interaction_type ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="interaction_type">Tipo <span class="text-danger">*</span></label>
                                </div>
                                @error('interaction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="duration-display"
                                        readonly
                                        value="{{ $modoEdicion && $interaction->duration ? $interaction->duration . ' segundos' : '0 segundos' }}">
                                    <label for="duration-display">Duración</label>
                                </div>
                                <input type="hidden" id="start_time" name="start_time"
                                    value="{{ old('start_time', $interaction->start_time ?? '') }}">
                                <input type="hidden" id="duration" name="duration"
                                    value="{{ old('duration', $interaction->duration ?? '') }}">
                                <div class="form-text mt-1">Se calcula automáticamente al seleccionar un cliente.</div>
                                @error('duration')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                        placeholder="Describe los detalles de la interacción..." required>{{ old('notes', $interaction->notes ?? '') }}</textarea>
                                    <label for="notes">Notas <span class="text-danger">*</span></label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="form-text">Añade aquí todos los detalles relevantes de la interacción.
                                    </div>
                                    <div class="form-text text-end" id="notes-counter">0/500 caracteres</div>
                                </div>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div></div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" onclick="showTab('adicional')">
                            Continuar <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="adicional-tab">
                <div class="category-container">
                    <div class="category-header">
                        <h5 class="category-title">
                            <div class="category-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            Información Adicional
                        </h5>
                        <p class="category-description">Datos complementarios del cliente y asignación</p>
                    </div>
                    <div class="category-content">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select select2 @error('id_area') is-invalid @enderror"
                                        id="id_area" name="id_area">
                                        <option value="">Selecciona un área</option>
                                        @if (isset($areas))
                                            @foreach ($areas as $id => $nombre)
                                                <option value="{{ $id }}"
                                                    {{ old('id_area', $interaction->id_area ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="id_area">Área</label>
                                </div>
                                @error('id_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select select2 @error('id_cargo') is-invalid @enderror"
                                        id="id_cargo" name="id_cargo">
                                        <option value="">Selecciona un cargo</option>
                                        @if (isset($cargos))
                                            @foreach ($cargos as $id => $nombre)
                                                <option value="{{ $id }}"
                                                    {{ old('id_cargo', $interaction->id_cargo ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="id_cargo">Cargo</label>
                                </div>
                                @error('id_cargo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select select2 @error('id_linea_de_obligacion') is-invalid @enderror"
                                        id="id_linea_de_obligacion" name="id_linea_de_obligacion">
                                        <option value="">Selecciona una línea</option>
                                        @if (isset($lineasCredito))
                                            @foreach ($lineasCredito as $id => $nombre)
                                                <option value="{{ $id }}"
                                                    {{ old('id_linea_de_obligacion', $interaction->id_linea_de_obligacion ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="id_linea_de_obligacion">Línea de Obligación</label>
                                </div>
                                @error('id_linea_de_obligacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select
                                        class="form-select select2 @error('id_area_de_asignacion') is-invalid @enderror"
                                        id="id_area_de_asignacion" name="id_area_de_asignacion">
                                        <option value="">Selecciona un área</option>
                                        @if (isset($areas))
                                            @foreach ($areas as $id => $nombre)
                                                <option value="{{ $id }}"
                                                    {{ old('id_area_de_asignacion', $interaction->id_area_de_asignacion ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="id_area_de_asignacion">Área de Asignación</label>
                                </div>
                                @error('id_area_de_asignacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select
                                        class="form-select select2 @error('id_distrito_interaccion') is-invalid @enderror"
                                        id="id_distrito_interaccion" name="id_distrito_interaccion">
                                        <option value="">Selecciona un distrito</option>
                                        @if (isset($distrito))
                                            @foreach ($distrito as $id => $nombre)
                                                <option value="{{ $id }}"
                                                    {{ old('id_distrito_interaccion', $interaction->id_distrito_interaccion ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="id_distrito_interaccion">Distrito de la Interacción</label>
                                </div>
                                @error('id_distrito_interaccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-light" onclick="showTab('principal')">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </button>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" onclick="showTab('resultado')">
                            Continuar <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="resultado-tab">
                <div class="category-container">
                    <div class="category-header">
                        <h5 class="category-title">
                            <div class="category-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            Resultado y Planificación
                        </h5>
                        <p class="category-description">Resultado de la interacción y próximas acciones</p>
                    </div>
                    <div class="category-content">
                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="bi bi-flag me-2 text-primary"></i>Resultado de la Interacción
                            </h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <select class="form-select select2 @error('outcome') is-invalid @enderror"
                                        id="outcome" name="outcome" required>
                                        <option value="">Selecciona un resultado</option>
                                        @foreach ($outcomes as $outcome)
                                            <option value="{{ $outcome->id }}"
                                                {{ old('outcome', $interaction->outcome ?? '') == $outcome->id ? 'selected' : '' }}>
                                                {{ $outcome->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="outcome">Resultado <span class="text-danger">*</span></label>
                                </div>
                                @error('outcome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>Planificación
                            </h6>
                        </div>

                        <div class="card border-0 bg-light mb-4" id="planning-section" style="display:none;">
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="datetime-local"
                                                class="form-control @error('next_action_date') is-invalid @enderror"
                                                id="next_action_date" name="next_action_date"
                                                value="{{ old('next_action_date', $interaction->next_action_date ?? '') }}">
                                            <label for="next_action_date">Próxima Acción</label>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                data-days="1">+1 día</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                data-days="3">+3 días</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                data-days="7">+1 sem</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                data-days="14">+2 sem</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                data-days="30">+1 mes</button>
                                        </div>
                                        @error('next_action_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select
                                                class="form-select select2 @error('next_action_type') is-invalid @enderror"
                                                id="next_action_type" name="next_action_type">
                                                <option value="">Selecciona un tipo</option>
                                                @foreach ($nextActions as $action)
                                                    <option value="{{ $action->id }}"
                                                        {{ old('next_action_type', $interaction->next_action_type ?? '') == $action->id ? 'selected' : '' }}>
                                                        {{ $action->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="next_action_type">Tipo de Acción</label>
                                        </div>
                                        @error('next_action_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('next_action_notes') is-invalid @enderror" id="next_action_notes"
                                                name="next_action_notes" rows="3" placeholder="Detalles sobre la próxima acción programada...">{{ old('next_action_notes', $interaction->next_action_notes ?? '') }}</textarea>
                                            <label for="next_action_notes">Notas sobre la Próxima Acción</label>
                                        </div>
                                        @error('next_action_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-light" onclick="showTab('adicional')">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </button>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" onclick="showTab('adjuntos')">
                            Continuar <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="adjuntos-tab">
                <div class="category-container">
                    <div class="category-header">
                        <h5 class="category-title">
                            <div class="category-icon">
                                <i class="bi bi-paperclip"></i>
                            </div>
                            Adjuntos y Referencias
                        </h5>
                        <p class="category-description">Archivos y enlaces relacionados con la interacción</p>
                    </div>
                    <div class="category-content">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file"
                                        class="form-control @error('attachments') is-invalid @enderror"
                                        id="attachments" name="attachments" multiple>
                                    <label for="attachments">Archivos Adjuntos</label>
                                </div>
                                <div class="form-text">Puedes adjuntar múltiples archivos (máx. 10MB por archivo)</div>
                                @if ($modoEdicion && $interaction->attachment_urls && count($interaction->attachment_urls))
                                    <div class="mt-2">
                                        <div class="small text-muted">Archivos existentes:</div>

                                        @if ($interaction->attachment_urls)
                                            <a href="{{ $novedad->getFile($interaction->attachment_urls) }}"
                                                target="_blank">Archivo</a>

                                            <div class="d-flex align-items-center mt-1">
                                                <i class="bi bi-file-earmark me-2"></i>
                                                <span class="me-auto">Archivo</span>
                                                <a href="{{ $novedad->getFile($interaction->attachment_urls) }}"
                                                    target="_blank">Archivo</a>

                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @error('attachments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="url"
                                        class="form-control @error('interaction_url') is-invalid @enderror"
                                        id="interaction_url" name="interaction_url" placeholder="https://ejemplo.com"
                                        value="{{ old('interaction_url', $interaction->interaction_url ?? '') }}">
                                    <label for="interaction_url">Enlace de Referencia</label>
                                </div>
                                @error('interaction_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-light" onclick="showTab('resultado')">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </button>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" onclick="showTab('historial')">
                            Continuar <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="historial-tab">
                <div class="category-container">
                    <div class="category-header">
                        <h5 class="category-title">
                            <div class="category-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            Historial de Interacciones
                        </h5>
                        <p class="category-description">Interacciones previas con este cliente</p>
                    </div>
                    <div class="category-content">
                        <div class="card border-0 bg-light mb-4" id="history-section" style="display:none;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    <h6 class="mb-0 fw-semibold">Historial Reciente</h6>
                                    <div class="ms-auto">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="toggle-history">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="history-content">
                                    <div id="interaction-history-list" class="history-list"
                                        style="max-height:220px; overflow-y:auto;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-light" onclick="showTab('adjuntos')">
                            <i class="bi bi-arrow-left"></i> Anterior
                        </button>
                    </div>
                    <div class="action-buttons">
                        <button id="clear-draft" type="button" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-trash me-1"></i> Borrar Borrador
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>
                            {{ $modoEdicion ? 'Actualizar Interacción' : 'Guardar Interacción' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="ajax-loader" class="ajax-loader" aria-hidden="true"
    style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="card text-center p-4">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mb-0">Cargando información del cliente...</p>
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).attr('placeholder');
                },
                dropdownParent: $('body')
            });

            $('#client_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar cliente...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('interactions.search-clients') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results.map(function(item) {
                                return {
                                    id: item.cod_ter,
                                    text: `${item.cod_ter} - ${item.apl1} ${item.apl2} ${item.nom1} ${item.nom2}`
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

            const $ajaxLoader = $('#ajax-loader');
            const $form = $('#interaction-form');
            const storageKey = 'interaction_form_draft_v1';

            let startTimeInterval = null;
            let startTime = null;

            function showLoader() {
                $ajaxLoader.show();
            }

            function hideLoader() {
                $ajaxLoader.hide();
            }

            toastr.options = {
                "positionClass": "toast-bottom-right",
                "timeOut": "2500",
                "progressBar": true,
            };

            $('.tab-button').on('click', function() {
                const tabId = $(this).data('tab');
                showTab(tabId);
                saveDraft();
            });

            window.showTab = function(tabId) {
                $('.tab-button').removeClass('active');
                $(`.tab-button[data-tab="${tabId}"]`).addClass('active');

                $('.tab-panel').removeClass('active');
                $(`#${tabId}-tab`).addClass('active');

                updateProgress();
            }

            function updateProgress() {
                const requiredFields = ['client_id', 'interaction_channel', 'interaction_type', 'notes', 'outcome'];
                let completed = 0;

                requiredFields.forEach(field => {
                    const value = $(`[name="${field}"]`).val();
                    if (value && value.trim() !== '') {
                        completed++;
                    }
                });

                const progress = Math.round((completed / requiredFields.length) * 100);

                $('#progress-bar').css('width', progress + '%');
                $('#progress-percentage').text(progress + '%');

                const messages = [
                    'Comienza seleccionando un cliente',
                    'Agrega los detalles de la interacción',
                    'Completa la información adicional',
                    'Define el resultado',
                    'Adjunta archivos y revisa el historial',
                    'Formulario completo'
                ];

                const messageIndex = Math.min(Math.floor(progress / 20), messages.length - 1);
                $('#progress-message').text(messages[messageIndex]);
            }

            function saveDraft() {
                try {
                    const data = {};
                    $form.find('input,textarea,select').each(function() {
                        const name = this.name;
                        if (!name) return;
                        if (this.type === 'file') return;
                        if (this.type === 'checkbox' || this.type === 'radio') {
                            if (this.checked) data[name] = this.value;
                        } else {
                            if ($(this).is('select[multiple]')) {
                                data[name] = $(this).val() || [];
                            } else {
                                data[name] = $(this).val();
                            }
                        }
                    });
                    data.activeTab = $('.tab-button.active').data('tab');
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    toastr.success('Borrador guardado automáticamente');
                } catch (e) {
                    console.warn('No se pudo guardar el borrador', e);
                }
            }

            function debounce(fn, wait) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            const saveDraftDebounced = debounce(saveDraft, 700);

            function loadDraft() {
                try {
                    const raw = localStorage.getItem(storageKey);
                    if (!raw) return;
                    const data = JSON.parse(raw);
                    Object.keys(data).forEach(name => {
                        if (name === 'activeTab') return;
                        const value = data[name];
                        const $el = $form.find(`[name="${name}"]`);
                        if (!$el.length) return;
                        if ($el.is('select[multiple]')) {
                            $el.val(value).trigger('change');
                        } else if ($el.is('select')) {
                            $el.val(value).trigger('change');
                        } else {
                            $el.val(value);
                        }
                    });

                    if (data.start_time && data.client_id) {
                        $('#start_time').val(data.start_time);
                        if (data.duration) {
                            $('#duration-display').val(`${data.duration} segundos`);
                        }

                        startTime = new Date(data.start_time);

                        startTimeInterval = setInterval(() => {
                            const now = new Date();
                            const durationSeconds = Math.round((now - startTime) / 1000);
                            $('#duration-display').val(`${durationSeconds} segundos`);
                            $('#duration').val(durationSeconds);
                        }, 1000);
                    }

                    if (data.activeTab) {
                        showTab(data.activeTab);
                    }

                    toastr.info('Borrador restaurado automáticamente');
                } catch (e) {
                    console.warn('No se pudo cargar el borrador', e);
                }
            }

            $form.on('input change', 'input, textarea, select', function() {
                updateProgress();
                saveDraftDebounced();
            });

            loadDraft();
            updateProgress();

            $('#clear-draft').on('click', function() {
                Swal.fire({
                    title: '¿Borrar el borrador guardado?',
                    text: "Esto eliminará el borrador localmente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, borrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(storageKey);
                        $form[0].reset();
                        $('.select2').val(null).trigger('change');
                        $('#planning-section').hide();
                        $('#client-info-card').hide();
                        $('#history-section').hide();
                        updateProgress();
                        toastr.success('Borrador eliminado');
                    }
                });
            });

            function validateField(el) {
                const $el = $(el);
                const name = $el.attr('name');

                if (!$el.prop('required')) {
                    if ($el.val() && $el.val().toString().trim() !== '') {
                        $el.removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $el.removeClass('is-valid is-invalid');
                    }
                    return true;
                }

                const val = $el.val();
                if (!val || val.toString().trim() === '') {
                    $el.removeClass('is-valid').addClass('is-invalid');
                    return false;
                }

                if ($el.attr('type') === 'url' && val) {
                    try {
                        new URL(val);
                    } catch (e) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                if ($el.attr('type') === 'number' && val) {
                    if (isNaN(val) || parseFloat(val) < 0) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                $el.removeClass('is-invalid').addClass('is-valid');
                return true;
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                if (startTimeInterval) {
                    clearInterval(startTimeInterval);
                    startTimeInterval = null;
                }

                const $startTimeField = $('#start_time');
                const $durationField = $('#duration');
                const $durationDisplay = $('#duration-display');

                if (startTime && $startTimeField.val()) {
                    const endTime = new Date();
                    const durationInSeconds = Math.round((endTime - startTime) / 1000);
                    $durationField.val(durationInSeconds);
                    $durationDisplay.val(`${durationInSeconds} segundos`);
                } else {
                    $durationField.val(0);
                    $durationDisplay.val('0 segundos');
                }

                let valid = true;

                $form.find('select[required], textarea[required], input[required]').each(function() {
                    const ok = validateField(this);
                    if (!ok) valid = false;
                });

                if (!valid) {
                    const $first = $form.find('.is-invalid').first();
                    $('html,body').animate({
                        scrollTop: $first.offset().top - 90
                    }, 350);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        text: 'Por favor corrige los campos marcados antes de enviar.',
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Confirmar envío',
                    text: "¿Deseas enviar la interacción ahora?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(storageKey);
                        showLoader();
                        $form.off('submit');
                        $form.submit();
                    }
                });
            });

            $('#client_id').on('change', function() {
                const cod_ter = $(this).val();
                const clientCard = $('#client-info-card');
                const historySection = $('#history-section');
                const historyList = $('#interaction-history-list');
                const $durationDisplay = $('#duration-display');
                const $startTimeField = $('#start_time');

                if (startTimeInterval) {
                    clearInterval(startTimeInterval);
                    startTimeInterval = null;
                }

                if (!cod_ter) {
                    clientCard.fadeOut();
                    historySection.fadeOut();
                    $durationDisplay.val('0 segundos');
                    $startTimeField.val('');
                    $('#duration').val(0);
                    updateProgress();
                    return;
                }

                startTime = new Date();
                $startTimeField.val(startTime.toISOString());
                $durationDisplay.val('0 segundos');
                $('#duration').val(0);

                startTimeInterval = setInterval(() => {
                    const now = new Date();
                    const durationSeconds = Math.round((now - startTime) / 1000);
                    $durationDisplay.val(`${durationSeconds} segundos`);
                    $('#duration').val(durationSeconds);
                }, 1000);

                showLoader();
                $.ajax({
                    url: `/interactions/cliente/${cod_ter}`,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 10000,
                }).done(function(data) {
                    const initials = ((data.nom1 || '').charAt(0) + (data.apl1 || '').charAt(0))
                        .toUpperCase();
                    $('#info-avatar').text(initials || '—');
                    $('#info-nombre').text(data.nom_ter ?? 'No registrado');
                    $('#info-id').text(`ID: ${data.cod_ter ?? 'N/A'}`);

                    $('#info-distrito').text(data.distrito?.NOM_DIST ?? 'No registrado');
                    $('#info-categoria').text(data.maeTipos?.nombre ?? 'No registrado');
                    $('#info-email').text(data.email ?? 'No registrado');
                    $('#info-telefono').text(data.tel1 ?? 'No registrado');
                    $('#info-direccion').text(data.dir ?? 'No registrado');
                    $('#btn-editar-cliente').attr('href',
                    `/maestras/terceros/${data.cod_ter}/edit`);

                    historyList.empty();
                    if (data.history && data.history.length) {
                        data.history.forEach(item => {
                            const html = `<div class="mb-2 pb-2 border-bottom">
                        <div class="fw-semibold">${item.date} - ${item.agent}</div>
                        <div class="text-muted small">${item.type} - ${item.outcome}</div>
                        <div class="text-muted small">${item.notes}</div>
                    </div>`;
                            historyList.append(html);
                        });
                        historySection.fadeIn();
                    } else {
                        historySection.fadeOut();
                    }

                    clientCard.fadeIn();
                    updateProgress();
                    toastr.success('Información del cliente cargada');
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo cargar el cliente',
                        text: 'Revisa la conexión o inténtalo más tarde.'
                    });
                    $('#client-info-card').fadeOut();
                    $('#history-section').fadeOut();
                }).always(function() {
                    hideLoader();
                });
            });

            const outcomeSelect = $('#outcome');
            const planningSection = $('#planning-section');

            function handleOutcomeChange() {
                const selectedOutcomeText = outcomeSelect.find("option:selected").text().trim();
                if (selectedOutcomeText === 'Pendiente' || selectedOutcomeText === 'No contesta' ||
                    selectedOutcomeText.toLowerCase().includes('pendiente')) {
                    planningSection.slideDown();
                } else {
                    planningSection.slideUp();
                }
                updateProgress();
            }

            outcomeSelect.on('change', handleOutcomeChange);
            handleOutcomeChange();

            $('.quick-date').on('click', function() {
                const daysToAdd = parseInt($(this).data('days')) || 0;
                const nextActionInput = $('#next_action_date');
                const targetDate = new Date();
                targetDate.setDate(targetDate.getDate() + daysToAdd);
                const year = targetDate.getFullYear();
                const month = String(targetDate.getMonth() + 1).padStart(2, '0');
                const day = String(targetDate.getDate()).padStart(2, '0');
                const hours = '09';
                const minutes = '00';
                const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                nextActionInput.val(formatted);
                updateProgress();
                saveDraftDebounced();
                toastr.info('Fecha establecida: ' + new Date(formatted).toLocaleString());
            });

            const $notes = $('#notes');
            const $notesCounter = $('#notes-counter');

            $notes.on('input', function() {
                const length = $(this).val().length;
                $notesCounter.text(`${length}/500 caracteres`);

                if (length > 500) {
                    $notesCounter.addClass('text-danger');
                } else {
                    $notesCounter.removeClass('text-danger');
                }
            });

            $notes.trigger('input');

            $('#toggle-client-info').on('click', function() {
                const $content = $('#client-info-content');
                const $icon = $(this).find('i');

                $content.slideToggle();
                $icon.toggleClass('bi-chevron-down bi-chevron-up');
            });

            $('#toggle-history').on('click', function() {
                const $content = $('#history-content');
                const $icon = $(this).find('i');

                $content.slideToggle();
                $icon.toggleClass('bi-chevron-down bi-chevron-up');
            });

            function generateAvatar(name) {
                const colors = ['#6B9BD1', '#7FA9D3', '#93B7D5', '#A7C5D7', '#BBD3D9'];
                const initials = name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2);
                const colorIndex = name.charCodeAt(0) % colors.length;

                return {
                    initials: initials || '—',
                    color: colors[colorIndex]
                };
            }

            $('#client_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                const avatarData = generateAvatar(selectedText);
                const $avatar = $('#info-avatar');

                $avatar.text(avatarData.initials);
                $avatar.css('background-color', avatarData.color);
            });

            $('#attachments').on('change', function() {
                const files = this.files;
                let fileNames = [];

                for (let i = 0; i < files.length; i++) {
                    fileNames.push(files[i].name);
                }

                if (fileNames.length > 0) {
                    toastr.info(`Archivos seleccionados: ${fileNames.join(', ')}`);
                }
            });

            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            $('#interaction_url').on('input', function() {
                const url = $(this).val();
                if (url && !isValidUrl(url)) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    saveDraft();
                    toastr.success('Borrador guardado');
                }

                if (e.altKey) {
                    switch (e.key) {
                        case '1':
                            showTab('principal');
                            break;
                        case '2':
                            showTab('adicional');
                            break;
                        case '3':
                            showTab('resultado');
                            break;
                        case '4':
                            showTab('adjuntos');
                            break;
                        case '5':
                            showTab('historial');
                            break;
                    }
                }
            });
        });
    </script>
@endpush