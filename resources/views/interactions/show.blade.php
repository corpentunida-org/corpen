<x-base-layout>
    <style>
        /* === Sistema de Diseño Pastel Pro === */
        :root {
            --p-blue: #A2D2FF;
            --p-blue-light: #BDE0FE;
            --p-green: #C7F9CC;
            --p-red: #FFC7B2;
            --p-yellow: #FFECB3;
            --p-purple: #E0C3FC;
            --text-main: #2D3436;
            --text-muted: #636E72;
            --bg-body: #F4F7F9;
            --card-radius: 18px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
        }

        .glass-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        /* Timeline de Seguimientos */
        .followup-timeline {
            position: relative;
            padding-left: 45px;
        }

        .followup-timeline::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--p-blue-light);
            border-radius: 3px;
        }

        .followup-node {
            position: relative;
            margin-bottom: 30px;
        }

        .followup-marker {
            position: absolute;
            left: -37px;
            top: 5px;
            width: 22px;
            height: 22px;
            background: #fff;
            border: 5px solid var(--p-blue);
            border-radius: 50%;
            z-index: 2;
        }

        .followup-body {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #f1f3f5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        /* Estilos de Datos */
        .data-label {
            font-size: 0.75rem;
            text-uppercase: uppercase;
            color: var(--text-muted);
            font-weight: 800;
            display: block;
            margin-bottom: 3px;
        }

        .data-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .badge-soft {
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
            display: inline-block;
        }

        .caller-box {
            background: var(--p-blue-light);
            border-radius: 12px;
            padding: 15px;
            border: 1px dashed var(--p-blue);
        }

        .avatar-init {
            width: 45px;
            height: 45px;
            background: var(--p-purple);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }

        /* Modal Custom */
        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .form-control-pastel {
            border: none;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
        }

        .form-control-pastel:focus {
            background: #fff;
            box-shadow: 0 0 0 3px var(--p-blue-light);
        }

        /* === Adaptación de Select2 al tema Pastel Pro === */
        .select2-container--default .select2-selection--single {
            background-color: #f8f9fa;
            border: none;
            border-radius: 10px;
            height: 44px;
            /* Misma altura que form-control-pastel */
            padding: 6px 5px;
            transition: all 0.2s ease;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-main);
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 10px;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            background: #fff;
            box-shadow: 0 0 0 3px var(--p-blue-light);
        }

        .select2-dropdown {
            border: 1px solid var(--p-blue-light);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 8px;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: var(--p-blue) !important;
            color: #fff !important;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.index') }}"
                                class="text-decoration-none">CRM</a></li>
                        <li class="breadcrumb-item active">Interacción #{{ $interaction->id }}</li>
                    </ol>
                </nav>
                <h1 class="fw-bold h2 mb-0">Gestión de Interacción</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('interactions.index') }}" class="btn btn-white shadow-sm border-0 rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Regresar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">

                <div class="glass-card p-4">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="mb-3 fw-bold"><i class="fas fa-id-card text-primary me-2"></i>Datos del Titular
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle flex-shrink-0 fw-bold shadow-sm"
                                    style="width: 60px; height: 60px; font-size: 1.4rem;">
                                    {{ substr($interaction->client->nom_ter ?? 'C', 0, 1) }}
                                </div>

                                <div>
                                    <h4 class="fw-bold mb-1 text-primary">
                                        {{ $interaction->client->nom_ter ?? 'Sin Nombre' }}</h4>

                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mt-1"
                                        style="font-size: 0.9rem;">
                                        <span>Documento <strong
                                                class="text-dark">{{ $interaction->client->cod_ter }}</strong></span>

                                        <span class="border-start ps-3">
                                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                            Distrito <strong
                                                class="text-dark">{{ $interaction->client?->distrito?->NOM_DIST ?? 'Sin Distrito' }}</strong>
                                        </span>

                                        <span class="border-start ps-3">
                                            <i class="feather-tag me-1 text-primary"></i>
                                            Línea <strong class="text-dark">
                                                @foreach ($interaction->lineas_detalle as $linea)
                                                    <span class="badge bg-primary">
                                                        ID: {{ $linea->id }} - {{ $linea->nombre }}
                                                    </span>
                                                @endforeach
                                            </strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="caller-box">
                                <span class="data-label text-primary">Persona que Inicia el Contacto</span>
                                <div class="fw-bold text-dark">{{ $interaction->nombre_quien_llama ?? 'El Titular' }}
                                </div>
                                <div class="d-flex flex-column gap-1 mt-1 small text-muted">
                                    <span><i class="fas fa-users me-2"></i>Relación:
                                        <strong>{{ ucfirst($interaction->parentesco_quien_llama ?? 'Titular') }}</strong></span>
                                    <span><i class="fas fa-phone-alt me-2"></i>Tel:
                                        {{ $interaction->celular_quien_llama ?? 'N/A' }}</span>


                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 opacity-50">
                        </div>

                        <div class="col-md-3">
                            <span class="data-label">Canal</span>
                            <div class="data-value">{{ $interaction->channel->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="data-label">Motivo</span>
                            <div class="data-value">{{ $interaction->type->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="data-label">Duración</span>
                            <div class="data-value">{{ floor($interaction->duration / 60) }}m
                                {{ $interaction->duration % 60 }}s</div>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <span class="badge-soft" style="background: var(--p-green); color: #1e4620;">
                                {{ $interaction->outcomeRelation->name ?? 'Finalizado' }}
                            </span>
                        </div>

                        <div class="col-12 mt-4">
                            <span class="data-label">Notas Iniciales de la Gestión</span>
                            <div class="p-3 bg-light rounded-3 mb-0"
                                style="border-left: 4px solid var(--p-blue); font-style: italic;">
                                "{{ $interaction->notes }}"
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="fas fa-stream text-warning me-2"></i>Línea de Tiempo de
                            Seguimiento</h5>
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalSeguimiento">
                            <i class="fas fa-plus-circle me-2"></i>Registrar Gestión
                        </button>
                    </div>

                    @if ($interaction->seguimientos->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-light mb-3"></i>
                            <p class="text-muted">No se han registrado seguimientos posteriores.</p>
                        </div>
                    @else
                        <div class="followup-timeline">
                            @foreach ($interaction->seguimientos as $seguimiento)
                                <div class="followup-node">
                                    <div class="followup-marker"></div>
                                    <div class="followup-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <span class="badge-soft"
                                                    style="background: var(--p-blue-light); color: #004085;">
                                                    <i class="fas fa-check me-1"></i>
                                                    {{ $seguimiento->outcome->name ?? 'Gestión Realizada' }}
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold small">
                                                    {{ $seguimiento->created_at->format('d/m/Y') }}</div>
                                                <small
                                                    class="text-muted">{{ $seguimiento->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <span class="data-label">Descripción del avance:</span>
                                            <p class="mb-0 text-secondary" style="font-size: 0.95rem;">
                                                {{ $seguimiento->next_action_notes }}
                                            </p>
                                        </div>

                                        <div class="row g-3 pt-3 border-top mt-2">
                                            <div class="col-sm-6">
                                                <span class="data-label">Gestionado por:</span>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-init"
                                                        style="width: 24px; height: 24px; font-size: 0.6rem;">
                                                        {{ substr($seguimiento->creator->name ?? 'S', 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="small fw-bold">{{ $seguimiento->creator->name ?? 'Sistema' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 text-sm-end">
                                                <span class="data-label">Asignado a:</span>
                                                <div class="d-flex align-items-center gap-2 justify-content-sm-end">
                                                    <span
                                                        class="small fw-bold text-primary">{{ $seguimiento->assignedUser->name ?? 'Sin asignar' }}</span>
                                                    <i class="fas fa-user-tag text-muted small"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($seguimiento->next_action_date)
                                            <div class="mt-3 p-2 rounded-3 d-flex align-items-center justify-content-between"
                                                style="background: var(--p-yellow); border: 1px solid #ffe69c;">
                                                <div class="small fw-bold"><i
                                                        class="fas fa-calendar-alt me-2"></i>Agenda:
                                                    {{ $seguimiento->nextAction->name ?? 'Acción' }}</div>
                                                <div class="small badge bg-white text-dark border">
                                                    {{ $seguimiento->next_action_date->format('d/m/Y') }}</div>
                                            </div>
                                        @endif

                                        @if (!empty($seguimiento->attachment_urls))
                                            <div class="mt-3">
                                                <a href="{{ $interaction->getFile($seguimiento->attachment_urls) }}"
                                                    target="_blank" class="btn btn-sm btn-light border py-1">
                                                    <i class="fas fa-paperclip me-1 text-primary"></i> Ver Anexo
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-user-shield me-2 text-primary"></i>Responsabilidad</h6>
                    <div class="mb-4">
                        <span class="data-label">Usuario Asignado (Analista)</span>
                        <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3">
                            <div class="avatar-init" style="background: var(--p-blue);">
                                {{ substr($interaction->usuarioAsignado->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="data-value text-truncate">
                                {{ $interaction->usuarioAsignado->name ?? 'Sin Asignación' }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="data-label">Línea de Obligación</span>
                        <div class="data-value text-primary">
                            <i
                                class="fas fa-file-invoice-dollar me-2"></i>{{ $interaction->lineaDeObligacion->linea ?? 'General' }}
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4 text-center">
                    <h6 class="fw-bold mb-3"><i class="fas fa-file-pdf me-2 text-danger"></i>Soporte Adjunto</h6>

                    @php
                        // Buscamos el primer seguimiento que tenga un archivo adjunto
                        $seguimientoConArchivo = $interaction->seguimientos
                            ->whereNotNull('attachment_urls')
                            ->where('attachment_urls', '!=', '[]')
                            ->first();
                    @endphp

                    @if ($seguimientoConArchivo && !empty($seguimientoConArchivo->attachment_urls))
                        <div class="d-grid">
                            <a href="{{ $interaction->getFile($seguimientoConArchivo->attachment_urls) }}"
                                target="_blank" class="btn btn-light border py-2 rounded-pill shadow-sm">
                                Abrir Soporte
                            </a>
                        </div>
                    @else
                        <div class="py-3 border border-dashed rounded text-muted small">Sin archivos adjuntos.</div>
                    @endif
                </div>

                <div class="glass-card p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2 text-success"></i>Actividad Semanal</h6>
                    <div style="height: 180px;"><canvas id="agentChart"></canvas></div>
                </div>
                {{-- ======================================================== --}}
                {{-- 🟢 NUEVO: SOPORTE DE PAGO DE CARTERA (SOLO SI EXISTE) --}}
                {{-- ======================================================== --}}
                @if ($interaction->comprobantes->isNotEmpty())
                    <div class="glass-card p-4 text-center mt-4"
                        style="border-top: 4px solid var(--p-green); border-radius: 12px;">
                        <h6 class="fw-bold mb-4 text-dark text-uppercase ls-1 fs-7">
                            <i class="fas fa-file-invoice-dollar me-2 text-success fs-5"></i>Soporte de Pago Vinculado
                        </h6>

                        @foreach ($interaction->comprobantes as $comprobante)
                            <div
                                class="bg-light p-4 rounded-3 mb-4 text-start border border-success border-opacity-25 shadow-sm position-relative overflow-hidden">

                                {{-- Decoración de fondo opcional --}}
                                <div class="position-absolute top-0 end-0 opacity-5 pt-3 pe-3">
                                    <i class="fas fa-check-circle fa-4x text-success"></i>
                                </div>

                                {{-- Fecha y Monto --}}
                                <div
                                    class="d-flex justify-content-between align-items-center mb-1 position-relative z-index-1">
                                    <span class="fw-bold text-success text-uppercase fs-9 ls-1">Monto Reportado</span>
                                    <span class="badge bg-white text-dark border shadow-xs fs-9 py-2 px-3">
                                        <i class="far fa-calendar-alt text-muted me-1"></i>
                                        {{ date('d/m/Y', strtotime($comprobante->fecha_pago)) }}
                                    </span>
                                </div>
                                <div class="fs-1 fw-bolder text-dark mb-4 position-relative z-index-1">
                                    ${{ number_format($comprobante->monto_pagado, 2, ',', '.') }}
                                </div>

                                {{-- Bloque del Banco --}}
                                <div
                                    class="d-flex align-items-center mb-4 p-3 bg-white rounded border border-gray-200 shadow-xs">
                                    <div class="d-flex align-items-center justify-content-center bg-light-primary text-primary rounded-circle me-3"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-university fs-6"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fw-bold fs-9 text-uppercase ls-1">Banco Destino</span>
                                        <span class="text-dark fw-bolder fs-7 text-truncate"
                                            style="max-width: 200px;">
                                            @if ($comprobante->id_banco)
                                                {{ optional($comprobante->banco)->banco ?? 'ID: ' . $comprobante->id_banco }}
                                            @else
                                                <span class="text-muted italic">No registrado</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Bloque Contable (Cuota, PR, CCO) --}}
                                <div class="row g-2 mb-4">
                                    <div class="col-4">
                                        <div
                                            class="border border-dashed border-gray-300 rounded p-2 text-center bg-white h-100 d-flex flex-column justify-content-center">
                                            <span
                                                class="text-muted fs-9 fw-bold d-block text-uppercase mb-1">Cuota</span>
                                            <span
                                                class="text-dark fw-bolder fs-7">{{ $comprobante->numero_cuota ?? '--' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="border border-dashed border-gray-300 rounded p-2 text-center bg-white h-100 d-flex flex-column justify-content-center">
                                            <span class="text-muted fs-9 fw-bold d-block text-uppercase mb-1">PR</span>
                                            <span
                                                class="text-dark fw-bolder fs-7">{{ $comprobante->pr ?? '--' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="border border-dashed border-gray-300 rounded p-2 text-center bg-white h-100 d-flex flex-column justify-content-center">
                                            <span
                                                class="text-muted fs-9 fw-bold d-block text-uppercase mb-1">CCO</span>
                                            <span
                                                class="text-dark fw-bolder fs-7">{{ $comprobante->cco ?? '--' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botón de Acción --}}
                                <div class="d-grid mt-2">
                                    @if ($comprobante->ruta_archivo)
                                        <a href="{{ $comprobante->url_archivo }}" target="_blank"
                                            class="btn btn-success border-0 py-3 rounded-pill shadow-sm fw-bolder fs-7 transition-all">
                                            <i class="fas fa-external-link-alt me-2"></i>Ver Recibo Completo
                                        </a>
                                    @else
                                        <button disabled
                                            class="btn btn-light text-muted border-0 py-3 rounded-pill fw-bolder fs-7">
                                            <i class="fas fa-file-excel me-2"></i>Sin Archivo Adjunto
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                {{-- ======================================================== --}}            
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i>Nuevo
                        Seguimiento</h5>
                    <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('interactions.seguimientos.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_interaction" value="{{ $interaction->id }}">
                    <input type="hidden" name="agent_id" value="{{ auth()->id() }}">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="data-label">Resultado <span class="text-danger">*</span></label>
                                <select name="outcome" class="form-select form-control-pastel" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($outcomes as $out)
                                        <option value="{{ $out->id }}">{{ $out->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="data-label">Asignar a <span class="text-danger">*</span></label>
                                <select name="id_user_asignacion" id="select_asignacion"
                                    class="form-select form-control-pastel" required style="width: 100%;">
                                    <option value="{{ $interaction->id_user_asignacion }}" selected>Mantener actual
                                        ({{ $interaction->usuarioAsignado->name ?? 'Usuario' }})</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="data-label">Notas del Seguimiento <span
                                        class="text-danger">*</span></label>
                                <textarea name="next_action_notes" class="form-control form-control-pastel" rows="3"
                                    placeholder="¿Qué se habló en esta nueva gestión?" required></textarea>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: var(--p-yellow);">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="data-label">Tipo Próxima Acción</label>
                                            <select name="next_action_type" class="form-select border-0">
                                                <option value="">Sin agenda</option>
                                                @foreach ($nextActions as $na)
                                                    <option value="{{ $na->id }}">{{ $na->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="data-label">Fecha Próxima Acción</label>
                                            <input type="datetime-local" name="next_action_date"
                                                class="form-control border-0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="data-label">
                                    Soporte (Opcional)
                                    <span class="text-primary text-lowercase fw-normal ms-1"
                                        style="font-size: 0.65rem;">(Soporta Ctrl+V)</span>
                                </label>
                                <div class="position-relative">
                                    <input type="file" name="attachment" id="attachment"
                                        class="form-control form-control-pastel" accept=".pdf,image/*">
                                    <div id="file-feedback" class="small mt-1 text-primary d-none fw-bold"
                                        style="font-size: 0.75rem;">
                                        <i class="fas fa-check-circle me-1"></i> <span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="data-label">URL / Link</label>
                                <input type="url" name="interaction_url" class="form-control form-control-pastel"
                                    placeholder="https://...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Guardar
                            Gestión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('agentChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    data: @json($totals),
                    borderColor: '#A2D2FF',
                    backgroundColor: 'rgba(162, 210, 255, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display: false
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const inputAttachment = document.getElementById('attachment');
            const feedbackDiv = document.getElementById('file-feedback');
            const feedbackText = feedbackDiv.querySelector('span');

            // =================================================================
            // LÓGICA PARA PEGAR ARCHIVOS CON CTRL+V (PORTAPAPELES)
            // =================================================================
            document.addEventListener('paste', function(e) {
                // Solo ejecutamos si el modal de seguimiento está abierto
                if (!$('#modalSeguimiento').hasClass('show')) return;

                let pastedFiles = e.clipboardData.files;
                if (pastedFiles.length === 0) return; // Si pegan texto, lo ignoramos

                // Creamos un DataTransfer para asignar el archivo al input
                const dt = new DataTransfer();
                dt.items.add(pastedFiles[0]); // Tomamos la primera imagen
                inputAttachment.files = dt.files;

                let nombreArchivo = pastedFiles[0].name || 'captura_pegada.png';

                // Feedback visual pastel
                inputAttachment.style.backgroundColor = 'var(--p-blue-light)';
                inputAttachment.style.boxShadow = '0 0 0 3px var(--p-blue-light)';
                feedbackText.textContent = nombreArchivo;
                feedbackDiv.classList.remove('d-none');
            });

            // =================================================================
            // FEEDBACK VISUAL SI SELECCIONAN MANUALMENTE
            // =================================================================
            inputAttachment.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    this.style.backgroundColor = 'var(--p-blue-light)';
                    this.style.boxShadow = '0 0 0 3px var(--p-blue-light)';
                    feedbackText.textContent = e.target.files[0].name;
                    feedbackDiv.classList.remove('d-none');
                } else {
                    // Si cancelan la selección, reseteamos estilos
                    this.style.backgroundColor = '';
                    this.style.boxShadow = '';
                    feedbackDiv.classList.add('d-none');
                }
            });

            $(document).ready(function() {
                // Inicializamos el buscador cuando el modal termina de abrirse
                $('#modalSeguimiento').on('shown.bs.modal', function() {
                    $('#select_asignacion').select2({
                        dropdownParent: $(
                        '#modalSeguimiento'), // Vital para que funcione en modales
                        placeholder: 'Buscar usuario...',
                        width: '100%',
                        language: {
                            noResults: function() {
                                return "No se encontraron usuarios";
                            }
                        }
                    });
                });

                // Opcional: Destruir la instancia cuando se cierra el modal para evitar bugs visuales si se abre varias veces
                $('#modalSeguimiento').on('hidden.bs.modal', function() {
                    if ($('#select_asignacion').hasClass("select2-hidden-accessible")) {
                        $('#select_asignacion').select2('destroy');
                    }
                });
            });
        });
    </script>
</x-base-layout>
