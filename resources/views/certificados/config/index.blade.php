<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-danger { background-color: #ffebee !important; color: #c62828 !important; border: none; }

        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }

        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }

        .nav-tabs-custom .nav-link {
            border: none; color: #616161; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; transition: all 0.3s;
        }
        .nav-tabs-custom .nav-link.active { color: #0052cc; background: transparent; border-bottom: 3px solid #0052cc; }
    </style>

    <div class="app-container py-4">

        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h1 class="h2 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Configuración Central</h1>
                <p class="text-muted mt-1 mb-0">Parámetros Core JSONB y Catálogos Base del Motor</p>
            </div>
            <button type="button" class="btn btn-light shadow-sm rounded-pill px-4 py-2 fw-bold text-muted" onclick="location.reload();">
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
            <div class="card-header bg-white pt-3 pb-0 border-bottom px-4" style="border-radius: 20px 20px 0 0;">
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

            <div class="card-body p-4">
                <div class="tab-content">

                    {{-- TAB 1: PARÁMETROS CORE --}}
                    <div class="tab-pane fade show active" id="tab-core">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Reglas y Parámetros Operativos</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfig">
                                <i class="fas fa-plus me-2"></i> Nueva Regla JSON
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 py-3">Acción de Vencimiento</th>
                                        <th class="border-0 py-3">Frecuencia (Días)</th>
                                        <th class="border-0 py-3">Parámetros (JSON)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($configuraciones as $config)
                                    <tr class="bg-white">
                                        <td class="ps-4 fw-bold text-dark border-bottom py-3">{{ $config->accionVencimiento->nombre ?? 'N/A' }}</td>
                                        <td class="border-bottom"><span class="badge bg-pastel-info px-3 py-2 rounded-pill">{{ $config->frecuencia_recordatorio_dias ?? 'Sin definir' }}</span></td>
                                        <td class="border-bottom"><code class="text-primary bg-pastel-primary px-2 py-1 rounded">{{ json_encode($config->parametros) }}</code></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay configuraciones core registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: ACCIONES DE VENCIMIENTO --}}
                    <div class="tab-pane fade" id="tab-acciones">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Acciones de Vencimiento</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAccion">
                                <i class="fas fa-plus me-2"></i> Nueva Acción
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">Nombre de la Acción</th>
                                        <th>Estado Actual</th>
                                        <th class="text-end pe-4">Alternar Estado</th>
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
                                                <button type="submit" class="btn btn-sm btn-icon rounded-circle shadow-sm {{ $accion->estado ? 'btn-light-danger text-danger' : 'btn-light-success text-success' }}" title="Cambiar Estado">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay acciones de vencimiento registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: ESTADOS --}}
                    <div class="tab-pane fade" id="tab-estados">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Estados</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEstado">
                                <i class="fas fa-plus me-2"></i> Nuevo Estado
                            </button>
                        </div>
                        <div class="row">
                            @forelse($estados as $estado)
                            <div class="col-md-3 mb-3">
                                <div class="bg-light rounded-4 p-3 border d-flex align-items-center">
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Tipologías</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTipo">
                                <i class="fas fa-plus me-2"></i> Nuevo Tipo
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Tipos de Alerta</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTipoAlerta">
                                <i class="fas fa-plus me-2"></i> Nuevo Tipo de Alerta
                            </button>
                        </div>
                        <div class="row">
                            @forelse($tiposAlerta as $alerta)
                            <div class="col-md-3 mb-3">
                                <div class="bg-pastel-warning rounded-4 p-3 d-flex align-items-center">
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Orígenes de Evento (Auditoría)</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalOrigenEvento">
                                <i class="fas fa-plus me-2"></i> Nuevo Origen
                            </button>
                        </div>
                        <div class="row">
                            @forelse($origenesEvento as $origen)
                            <div class="col-md-3 mb-3">
                                <div class="bg-pastel-info rounded-4 p-3 d-flex align-items-center">
                                    <i class="fas fa-sitemap text-info me-3 fs-4"></i>
                                    <span class="fw-bold text-dark">{{ $origen->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay orígenes de evento registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 7: EVENTOS DE AUDITORÍA --}}
                    <div class="tab-pane fade" id="tab-auditoria">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">Catálogo de Eventos de Auditoría</h5>
                            <button class="btn btn-pastel-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEventoAuditoria">
                                <i class="fas fa-plus me-2"></i> Nuevo Evento
                            </button>
                        </div>
                        <div class="row">
                            @forelse($eventosAuditoria as $evento)
                            <div class="col-md-3 mb-3">
                                <div class="bg-pastel-danger rounded-4 p-3 d-flex align-items-center">
                                    <i class="fas fa-user-secret text-danger me-3 fs-4"></i>
                                    <span class="fw-bold text-dark">{{ $evento->nombre }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><p class="text-center text-muted py-4">No hay eventos de auditoría registrados.</p></div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODALES --}}

    {{-- Modal Regla Core --}}
    <div class="modal fade" id="modalConfig" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.config.store') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold"><i class="fas fa-cog text-primary me-2"></i> Nueva Regla (JSON)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Acción de Vencimiento</label>
                        <select name="id_car_sia_acciones_vencimiento" class="form-select bg-light border-0" required>
                            <option value="">Seleccione...</option>
                            @foreach($acciones as $accion) <option value="{{ $accion->id }}">{{ $accion->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Frecuencia Recordatorio (Días)</label>
                        <input type="number" name="frecuencia_recordatorio_dias" class="form-control bg-light border-0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Parámetros Adicionales</label>
                        <textarea name="parametros" rows="3" class="form-control bg-light border-0 text-monospace fs-8" placeholder='{"clave": "valor"}'></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold">Guardar Regla</button>
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
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold">Guardar</button>
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
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-4 fw-bold">Guardar</button>
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
