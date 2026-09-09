<x-base-layout>
    <style>
        /* Paleta Pastel Personalizada */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-danger { background-color: #ffebee !important; color: #c62828 !important; border: none; }

        /* Estilos Base y Tarjetas */
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }

        /* Botones Animados */
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }

        /* Pestañas (Tabs) con Scroll Horizontal Suave para Móviles */
        .nav-tabs-custom {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch; /* Efecto suave en iOS */
            scrollbar-width: none; /* Ocultar scrollbar Firefox */
            border-bottom: none;
            padding-bottom: 2px;
        }
        .nav-tabs-custom::-webkit-scrollbar {
            display: none; /* Ocultar scrollbar Chrome/Safari/Edge */
        }
        .nav-tabs-custom .nav-link {
            border: none; color: #616161; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; transition: all 0.3s;
            white-space: nowrap; /* Evita que el texto de la pestaña se rompa en 2 líneas */
        }
        .nav-tabs-custom .nav-link.active { color: #0052cc; background: transparent; border-bottom: 3px solid #0052cc; }

        /* Mejora visual de tablas en Móvil */
        .table-responsive {
            border-radius: 12px;
        }
    </style>

    <div class="app-container py-4 px-3 px-md-4">

        {{-- Encabezado Responsivo --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3 mb-4">
            <div>
                <h1 class="h3 h2-md fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Configuración Central</h1>
                <p class="text-muted mt-1 mb-0 small">Parámetros Core JSONB y Catálogos Base del Motor</p>
            </div>
            <button type="button" class="btn btn-light shadow-sm rounded-pill px-4 py-2 fw-bold text-muted w-100 w-md-auto" onclick="location.reload();">
                <i class="fas fa-sync-alt me-2"></i> Recargar
            </button>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Contenedor Principal con Pestañas --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white pt-3 pb-0 border-bottom px-2 px-md-4" style="border-radius: 20px 20px 0 0;">
                <ul class="nav nav-tabs nav-tabs-custom" id="configTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-core" type="button"><i class="fas fa-cogs me-2"></i> PARÁMETROS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-acciones" type="button"><i class="fas fa-calendar-times me-2"></i> ACCIONES</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-estados" type="button"><i class="fas fa-tags me-2"></i> ESTADOS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tipos" type="button"><i class="fas fa-layer-group me-2"></i> TIPOS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-alertas" type="button"><i class="fas fa-bell me-2"></i> ALERTAS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-origenes" type="button"><i class="fas fa-sitemap me-2"></i> ORÍGENES AUDITORÍA</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-auditoria" type="button"><i class="fas fa-user-secret me-2"></i> EVENTOS AUDITORÍA</button></li>
                </ul>
            </div>

            <div class="card-body p-3 p-md-4">
                <div class="tab-content">

                    {{-- TAB 1: PARÁMETROS CORE --}}
                    <div class="tab-pane fade show active" id="tab-core">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Reglas Estratégicas y Parámetros</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalConfig">
                                <i class="fas fa-plus me-2"></i> Nueva Regla
                            </button>
                        </div>

                        <div class="table-responsive pb-2">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px; min-width: 850px;">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 py-3" style="width: 25%;">Clasificación y Límites</th>
                                        <th class="border-0 py-3" style="width: 25%;">Acción y Frecuencia</th>
                                        <th class="border-0 py-3" style="width: 35%;">Activadores Automáticos</th>
                                        <th class="pe-4 border-0 py-3 text-center" style="width: 15%;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($configuraciones as $config)
                                        @php
                                            $p = $config->parametros ?? [];
                                            $claseMora = strtolower($p['clasificacion_mora'] ?? 'desconocido');

                                            $colorBadge = match($claseMora) {
                                                'bueno' => 'bg-pastel-success text-success border-success',
                                                'regular' => 'bg-pastel-info text-info border-info',
                                                'atencion_especial' => 'bg-pastel-warning text-dark border-warning',
                                                'restringido' => 'bg-pastel-danger text-danger border-danger',
                                                'irregular' => 'bg-dark text-white border-dark',
                                                default => 'bg-light text-secondary border-secondary'
                                            };
                                        @endphp
                                    <tr class="bg-white shadow-sm" style="border-radius: 12px;">

                                        <td class="ps-4 py-3" style="border-radius: 12px 0 0 12px;">
                                            <div class="mb-2">
                                                <span class="badge {{ $colorBadge }} border border-opacity-25 px-3 py-2 rounded-pill text-uppercase fw-bold" style="letter-spacing: 0.5px;">
                                                    <i class="fas fa-tag me-1"></i> {{ str_replace('_', ' ', $claseMora) }}
                                                </span>
                                            </div>
                                            <div class="text-muted d-flex flex-wrap gap-2" style="font-size: 0.75rem;">
                                                <span><i class="fas fa-calendar-times text-secondary me-1"></i> Max Mora: <strong class="text-dark">{{ $p['mora_dias_max'] ?? '0' }}</strong></span>
                                                <span><i class="fas fa-hand-holding-heart text-secondary me-1"></i> Gracia: <strong class="text-dark">{{ $p['dias_gracia'] ?? '0' }}</strong></span>
                                            </div>
                                        </td>

                                        <td class="py-3">
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem; white-space: normal;">
                                                <i class="fas fa-bolt text-warning me-1"></i> {{ $config->accionVencimiento->nombre ?? 'Sin acción definida' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.8rem;">
                                                Alerta: <span class="badge bg-light text-dark border fw-bold">{{ $config->frecuencia_recordatorio_dias ?? '0' }} días</span>
                                            </div>
                                        </td>

                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-2">
                                                @if(isset($p['requiere_accion']) && $p['requiere_accion'])
                                                    <span class="badge bg-light text-dark border"><i class="fas fa-exclamation-circle text-warning me-1"></i> Acción Restrictiva</span>
                                                @endif
                                                @if(isset($p['bloqueo_automatico']) && $p['bloqueo_automatico'])
                                                    <span class="badge bg-pastel-danger border border-danger border-opacity-25"><i class="fas fa-ban me-1"></i> Bloqueo Cupo</span>
                                                @endif
                                                @if(isset($p['notificacion_gerencia']) && $p['notificacion_gerencia'])
                                                    <span class="badge bg-pastel-primary border border-primary border-opacity-25"><i class="fas fa-user-tie me-1"></i> Notifica Gerencia</span>
                                                @endif
                                                @if(isset($p['incluir_historico_3_anos']) && $p['incluir_historico_3_anos'])
                                                    <span class="badge bg-light text-secondary border"><i class="fas fa-history me-1"></i> Histórico 3A</span>
                                                @endif
                                                @if(empty($p['requiere_accion']) && empty($p['bloqueo_automatico']) && empty($p['notificacion_gerencia']))
                                                    <span class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-minus"></i> Sin activadores especiales</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- ESTADO CON BOTÓN INTEGRADO (RESPONSIVO) --}}
                                        <td class="pe-4 py-3 text-center" style="border-radius: 0 12px 12px 0;">
                                            @if($config->trashed())
                                                <span class="badge bg-pastel-secondary text-secondary px-2 py-1 rounded-1 d-inline-block"><i class="fas fa-archive me-1"></i> Archivado</span>
                                            @else
                                                <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center gap-2">
                                                    @if($config->estado_activo)
                                                        <span class="badge bg-pastel-success text-success px-2 py-1 rounded-1 w-100"><i class="fas fa-check-circle me-1"></i> Activo</span>
                                                    @else
                                                        <span class="badge bg-pastel-danger text-danger px-2 py-1 rounded-1 w-100"><i class="fas fa-times-circle me-1"></i> Inactivo</span>
                                                    @endif

                                                    <form action="{{ route('certificados.config.toggle_estado', $config->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm {{ $config->estado_activo ? 'text-danger' : 'text-success' }}" title="Alternar Estado" style="width: 32px; height: 32px; padding: 0;">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 bg-white shadow-sm" style="border-radius: 12px;">
                                            <i class="fas fa-sliders-h fs-1 text-muted opacity-25 mb-3"></i>
                                            <h6 class="fw-bold text-dark">Sin Configuración Core</h6>
                                            <p class="text-muted small mb-0">No se han definido reglas estratégicas de vencimiento.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: ACCIONES DE VENCIMIENTO --}}
                    <div class="tab-pane fade" id="tab-acciones">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Acciones</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalAccion">
                                <i class="fas fa-plus me-2"></i> Nueva Acción
                            </button>
                        </div>
                        <div class="table-responsive pb-2">
                            <table class="table table-hover align-middle mb-0" style="min-width: 400px;">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Nombre de la Acción</th>
                                        <th>Estado Actual</th>
                                        <th class="text-end pe-4">Alternar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($acciones as $accion)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $accion->nombre }}</td>
                                        <td>
                                            @if($accion->estado) <span class="badge bg-pastel-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> Activa</span>
                                            @else <span class="badge bg-pastel-secondary px-3 py-2 rounded-pill"><i class="fas fa-times-circle me-1"></i> Inactiva</span> @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('certificados.config.toggle_accion', $accion->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-icon rounded-circle shadow-sm {{ $accion->estado ? 'btn-light-danger text-danger' : 'btn-light-success text-success' }}" title="Cambiar Estado" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay acciones registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: ESTADOS --}}
                    <div class="tab-pane fade" id="tab-estados">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Estados</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalEstado">
                                <i class="fas fa-plus me-2"></i> Nuevo Estado
                            </button>
                        </div>
                        <div class="row g-3">
                            @forelse($estados as $estado)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="bg-light rounded-4 p-3 border d-flex align-items-center h-100">
                                    <i class="fas fa-tag text-primary me-3 fs-4 opacity-50"></i>
                                    <span class="fw-bold text-dark">{{ $estado->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay estados registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 4: TIPOS --}}
                    <div class="tab-pane fade" id="tab-tipos">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Tipologías</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalTipo">
                                <i class="fas fa-plus me-2"></i> Nuevo Tipo
                            </button>
                        </div>
                        <div class="table-responsive pb-2">
                            <table class="table table-hover align-middle mb-0" style="min-width: 450px;">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr><th class="ps-4 py-3">Nombre</th><th>Estructura Radicado</th><th>Estado</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($tipos as $tipo)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $tipo->nombre }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $tipo->estructura_radicado }}</span></td>
                                        <td>
                                            @if($tipo->estado) <span class="badge bg-pastel-success px-3 py-2 rounded-pill">Activo</span>
                                            @else <span class="badge bg-pastel-secondary px-3 py-2 rounded-pill">Inactivo</span> @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay tipos registrados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 5: TIPOS DE ALERTA --}}
                    <div class="tab-pane fade" id="tab-alertas">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Tipos de Alerta</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalTipoAlerta">
                                <i class="fas fa-plus me-2"></i> Nuevo Tipo
                            </button>
                        </div>
                        <div class="row g-3">
                            @forelse($tiposAlerta as $alerta)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="bg-pastel-warning rounded-4 p-3 d-flex align-items-center h-100">
                                    <i class="fas fa-bell text-warning me-3 fs-4"></i>
                                    <span class="fw-bold text-dark">{{ $alerta->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay tipos de alerta registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 6: ORÍGENES DE EVENTO (AUDITORÍA) --}}
                    <div class="tab-pane fade" id="tab-origenes">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Orígenes de Evento (Auditoría)</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalOrigenEvento">
                                <i class="fas fa-plus me-2"></i> Nuevo Origen
                            </button>
                        </div>
                        <div class="row g-3">
                            @forelse($origenesEvento as $origen)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="bg-pastel-info rounded-4 p-3 d-flex align-items-center h-100">
                                    <i class="fas fa-sitemap text-info me-3 fs-4"></i>
                                    <span class="fw-bold text-dark">{{ $origen->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay orígenes registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 7: EVENTOS DE AUDITORÍA --}}
                    <div class="tab-pane fade" id="tab-auditoria">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                            <h5 class="fw-bold text-dark m-0">Eventos de Auditoría</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#modalEventoAuditoria">
                                <i class="fas fa-plus me-2"></i> Nuevo Evento
                            </button>
                        </div>
                        <div class="row g-3">
                            @forelse($eventosAuditoria as $evento)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="bg-pastel-danger rounded-4 p-3 d-flex align-items-center h-100">
                                    <i class="fas fa-user-secret text-danger me-3 fs-4"></i>
                                    <span class="fw-bold text-dark">{{ $evento->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay eventos registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         MODALES
         ========================================================================= --}}

    {{-- Modal Regla Core --}}
    <div class="modal fade" id="modalConfig" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('certificados.config.store') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold"><i class="fas fa-sliders-h text-primary me-2"></i> Configurar Regla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">1. Parámetros Generales</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold text-dark">Acción a Ejecutar</label>
                            <select name="id_car_sia_acciones_vencimiento" class="form-select bg-light border-0 shadow-sm" required>
                                <option value="">Seleccione la acción estratégica...</option>
                                @foreach($acciones as $accion)
                                    <option value="{{ $accion->id }}">{{ $accion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-dark">Frecuencia (Días)</label>
                            <input type="number" name="frecuencia_recordatorio_dias" class="form-control bg-light border-0 shadow-sm" placeholder="Ej. 15">
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">2. Lógica Matemática (JSONB)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-dark">Días Max. Mora</label>
                            <input type="number" name="mora_dias_max" class="form-control bg-light border-0 shadow-sm" placeholder="Ej. 30, 60" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-dark">Días de Gracia</label>
                            <input type="number" name="dias_gracia" class="form-control bg-light border-0 shadow-sm" value="0" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold text-dark">Clasificación</label>
                            <select name="clasificacion_mora" class="form-select bg-light border-0 shadow-sm" required>
                                <option value="bueno">Bueno</option>
                                <option value="regular">Regular</option>
                                <option value="atencion_especial">Atención Especial</option>
                                <option value="restringido">Restringido</option>
                                <option value="irregular">Irregular / Castigado</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">3. Activadores Automáticos</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center h-100">
                                <input class="form-check-input m-0 me-3 flex-shrink-0" type="checkbox" role="switch" name="requiere_accion" id="sw1">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer w-100" for="sw1">Requiere Acción Restrictiva</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center h-100">
                                <input class="form-check-input m-0 me-3 flex-shrink-0" type="checkbox" role="switch" name="incluir_historico_3_anos" id="sw2" checked>
                                <label class="form-check-label fw-semibold text-dark cursor-pointer w-100" for="sw2">Incluir Histórico 3 Años</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch p-3 bg-pastel-danger rounded-3 border border-danger border-opacity-25 d-flex align-items-center h-100">
                                <input class="form-check-input m-0 me-3 flex-shrink-0" type="checkbox" role="switch" name="bloqueo_automatico" id="sw3">
                                <label class="form-check-label fw-bold text-danger cursor-pointer w-100" for="sw3">Ejecutar Bloqueo Automático</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch p-3 bg-pastel-warning rounded-3 border border-warning border-opacity-25 d-flex align-items-center h-100">
                                <input class="form-check-input m-0 me-3 flex-shrink-0" type="checkbox" role="switch" name="notificacion_gerencia" id="sw4">
                                <label class="form-check-label fw-bold text-dark cursor-pointer w-100" for="sw4">Notificar a Gerencia</label>
                            </div>
                        </div>

                        {{-- INTEGRACIÓN DEL SWITCH DE ESTADO ACTIVO --}}
                        <div class="col-12 mt-3">
                            <div class="form-check form-switch p-3 bg-pastel-success rounded-3 border border-success border-opacity-25 d-flex align-items-center h-100">
                                <input class="form-check-input m-0 me-3 flex-shrink-0" type="checkbox" role="switch" name="estado_activo" id="sw_estado" checked>
                                <label class="form-check-label fw-bold text-success cursor-pointer w-100" for="sw_estado">Crear como Regla Activa (Vigente)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark">Observación de Fase</label>
                        <textarea name="observacion_fase" rows="2" class="form-control bg-light border-0 shadow-sm" placeholder="Ej. Fase comercial. Mora temprana por iliquidez..."></textarea>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm w-100 w-sm-auto mb-2 mb-sm-0" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-sm-auto">Guardar Regla</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Acción de Vencimiento --}}
    <div class="modal fade" id="modalAccion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_accion') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-calendar-times text-primary me-2"></i> Crear Acción de Vencimiento</h5>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Nombre de la Acción</label>
                        <input type="text" name="nombre" class="form-control bg-light border-0" placeholder="Ej: Notificar Pre-jurídico" required>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold w-100 w-sm-auto">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Estado --}}
    <div class="modal fade" id="modalEstado" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_estado') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-tag text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Estado</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-4 text-center" placeholder="Nombre del Estado" required>
                    <button type="submit" class="btn btn-pastel-primary w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tipo --}}
    <div class="modal fade" id="modalTipo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_tipo') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-layer-group text-primary me-2"></i> Crear Tipo</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Nombre</label>
                        <input type="text" name="nombre" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Estructura Radicado</label>
                        <input type="text" name="estructura_radicado" class="form-control bg-light border-0" placeholder="Ej: RAD-YYYY-0000" required>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold w-100 w-sm-auto">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tipo Alerta --}}
    <div class="modal fade" id="modalTipoAlerta" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_tipo_alerta') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-bell text-warning fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Tipo de Alerta</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-4 text-center" placeholder="Nombre (Ej. Cobro Prejurídico)" required>
                    <button type="submit" class="btn btn-pastel-primary w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Origen Evento --}}
    <div class="modal fade" id="modalOrigenEvento" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_origen_evento') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-sitemap text-info fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Origen de Evento</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-4 text-center" placeholder="Ej: Interfaz Web, API, Cron" required>
                    <button type="submit" class="btn btn-pastel-primary w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Evento Auditoría --}}
    <div class="modal fade" id="modalEventoAuditoria" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.catalogos.store_evento_auditoria') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-user-secret text-danger fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Evento Auditoría</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-4 text-center" placeholder="Ej: Creación, Modificación, Borrado" required>
                    <button type="submit" class="btn btn-pastel-primary w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

</x-base-layout>
