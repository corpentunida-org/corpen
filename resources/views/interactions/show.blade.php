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

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">

                <div class="glass-card p-4">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="section-title mb-3" style="font-weight: 700;"><i
                                    class="fas fa-id-card text-primary me-2"></i>Datos del Titular</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-init" style="width: 60px; height: 60px; font-size: 1.4rem;">
                                    {{ substr($interaction->client->nom_ter ?? 'C', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-primary">
                                        {{ $interaction->client->nom_ter ?? 'Sin Nombre' }}
                                    </h4>
                                    <span class="text-muted">Documento:
                                        <strong>{{ $interaction->client->cod_ter }}</strong></span>
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
                                {{ $interaction->duration % 60 }}s
                            </div>
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
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalSeguimiento">
                            <i class="fas fa-plus-circle me-2"></i>Registrar Gestión
                        </button>
                    </div>

                    @if($interaction->seguimientos->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-light mb-3"></i>
                            <p class="text-muted">No se han registrado seguimientos posteriores.</p>
                        </div>
                    @else
                        <div class="followup-timeline">
                            @foreach($interaction->seguimientos as $seguimiento)
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
                                                <div class="fw-bold small">{{ $seguimiento->created_at->format('d/m/Y') }}</div>
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

                                        @if($seguimiento->next_action_date)
                                            <div class="mt-3 p-2 rounded-3 d-flex align-items-center justify-content-between"
                                                style="background: var(--p-yellow); border: 1px solid #ffe69c;">
                                                <div class="small fw-bold"><i class="fas fa-calendar-alt me-2"></i>Agenda:
                                                    {{ $seguimiento->nextAction->name ?? 'Acción' }}
                                                </div>
                                                <div class="small badge bg-white text-dark border">
                                                    {{ $seguimiento->next_action_date->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        @endif

                                        @php
                                            $raw = $seguimiento->attachment_urls;
                                            $archivosTimeline = [];

                                            if (is_array($raw)) {
                                                // Si Laravel ya lo convirtió a arreglo por el $casts
                                                $archivosTimeline = $raw;
                                            } elseif (is_string($raw) && trim($raw) !== '') {
                                                // Si es un string, intentamos ver si es un JSON
                                                $decoded = json_decode($raw, true);
                                                if (is_array($decoded)) {
                                                    $archivosTimeline = $decoded;
                                                } else {
                                                    // Si falla el JSON, significa que es un texto normal con la ruta directa
                                                    $archivosTimeline = [$raw];
                                                }
                                            }
                                        @endphp

                                        @if(!empty($archivosTimeline) && count($archivosTimeline) > 0)
                                            <div class="mt-3">
                                                <a href="{{ $seguimiento->getFile($archivosTimeline[0]) }}" target="_blank"
                                                    class="btn btn-sm btn-light border py-1">
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
                                {{ $interaction->usuarioAsignado->name ?? 'Sin Asignación' }}
                            </div>
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
                    <h6 class="fw-bold mb-3"><i class="fas fa-file-pdf me-2 text-danger"></i>Soportes Adjuntos</h6>
                    @foreach($interaction->seguimientos as $seguimiento)
                        @if(!empty($seguimiento->attachment_urls))
                            <div class="d-grid">
                                <a href="{{ $interaction->getFile($seguimiento->attachment_urls) }}" target="_blank"
                                    class="btn btn-light border py-2 rounded-pill shadow-sm">
                                    Abrir Soporte
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="glass-card p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2 text-success"></i>Actividad Semanal</h6>
                    <div style="height: 180px;"><canvas id="agentChart"></canvas></div>
                </div>
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
                                    @foreach($outcomes as $out)
                                        <option value="{{ $out->id }}">{{ $out->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="data-label">Asignar a <span class="text-danger">*</span></label>
                                <select name="id_user_asignacion" class="form-select form-control-pastel" required>
                                    <option value="{{ $interaction->id_user_asignacion }}" selected>Mantener actual
                                        ({{ $interaction->usuarioAsignado->name ?? 'Usuario' }})</option>
                                    @foreach($users as $u)
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
                                                @foreach($nextActions as $na)
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
                                <label class="data-label">Soporte (Opcional)</label>
                                <input type="file" name="attachment" class="form-control form-control-pastel">
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    </script>
</x-base-layout>