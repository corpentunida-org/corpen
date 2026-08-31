<x-base-layout>
    <style>
        /* Paleta Minimalista Corporativa */
        :root {
            --corp-primary: #2563eb;
            --corp-primary-light: #eff6ff;
            --corp-success: #16a34a;
            --corp-success-light: #f0fdf4;
            --corp-dark: #1e293b;
            --corp-gray: #64748b;
            --corp-gray-light: #f8fafc;
            --corp-border: #e2e8f0;
        }

        .bg-pastel-primary { background-color: var(--corp-primary-light) !important; color: var(--corp-primary) !important; }
        .bg-pastel-success { background-color: var(--corp-success-light) !important; color: var(--corp-success) !important; }
        .bg-pastel-secondary { background-color: var(--corp-gray-light) !important; color: var(--corp-gray) !important; }

        .card-custom { border-radius: 16px; background: #ffffff; border: 1px solid var(--corp-border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

        .btn-custom-primary { background-color: var(--corp-primary); color: white; border: none; font-weight: 500; transition: all 0.2s; }
        .btn-custom-primary:hover { background-color: #1d4ed8; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }

        .btn-custom-outline { background-color: white; color: var(--corp-dark); border: 1px solid var(--corp-border); font-weight: 500; transition: all 0.2s; }
        .btn-custom-outline:hover { background-color: var(--corp-gray-light); border-color: #cbd5e1; }

        .nav-tabs-custom { border-bottom: 1px solid var(--corp-border); gap: 1rem; padding: 0 1rem; }
        .nav-tabs-custom .nav-link { border: none; color: var(--corp-gray); font-weight: 500; padding: 1rem 0.5rem; border-bottom: 2px solid transparent; transition: all 0.2s; background: transparent; }
        .nav-tabs-custom .nav-link:hover { color: var(--corp-dark); }
        .nav-tabs-custom .nav-link.active { color: var(--corp-primary); border-bottom: 2px solid var(--corp-primary); }

        .input-custom { background-color: var(--corp-gray-light); border: 1px solid transparent; transition: all 0.2s; }
        .input-custom:focus { background-color: white; border-color: var(--corp-primary); box-shadow: 0 0 0 3px var(--corp-primary-light); }

        /* Estilos Acordeón Lotes */
        .accordion-item-custom { border: 1px solid var(--corp-border); border-radius: 12px !important; margin-bottom: 1rem; overflow: hidden; transition: all 0.2s; }
        .accordion-item-custom:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.03); border-color: #cbd5e1; }
        .accordion-button-custom { background-color: white; color: var(--corp-dark); font-weight: 500; padding: 1.25rem 1.5rem; box-shadow: none !important; }
        .accordion-button-custom:not(.collapsed) { background-color: var(--corp-gray-light); color: var(--corp-dark); border-bottom: 1px solid var(--corp-border); }

        .table-sticky-header th { position: sticky; top: 0; background-color: var(--corp-gray-light) !important; z-index: 2; border-bottom: 1px solid var(--corp-border); font-size: 0.75rem; font-weight: 600; color: var(--corp-gray); letter-spacing: 0.05em; text-transform: uppercase; }
        .table-custom td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--corp-border); font-size: 0.875rem; vertical-align: middle; }

        .badge-custom { padding: 0.4rem 0.75rem; font-weight: 500; font-size: 0.75rem; border-radius: 6px; }

        .list-group-custom .list-group-item { padding: 1rem 1.25rem; border-color: var(--corp-border); transition: background-color 0.2s; }
        .list-group-custom .list-group-item:hover { background-color: var(--corp-gray-light); }

        .btn-action-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: var(--corp-gray); transition: all 0.2s; background: transparent; border: 1px solid transparent; }
        .btn-action-icon:hover { background-color: white; color: var(--corp-primary); border-color: var(--corp-border); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>

    <div class="app-container py-4">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-4 gap-3">
            <div class="d-flex align-items-center">
                <div class="symbol-label bg-pastel-primary me-4 flex-shrink-0" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; border-radius: 14px;">
                    <i class="fas fa-shield-alt fs-3"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold m-0 text-dark">Bitácora de Auditoría</h1>
                    <p class="text-muted mt-1 mb-0 fs-7">Trazabilidad Absoluta del Motor SIA</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-custom-outline rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEvento">
                    <i class="fas fa-bolt me-2 text-muted"></i>Tipo Evento
                </button>
                <button type="button" class="btn btn-custom-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalOrigen">
                    <i class="fas fa-project-diagram me-2"></i>Nuevo Origen
                </button>
            </div>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success border-0 bg-pastel-success rounded-3 px-4 py-3 mb-4 d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-5"></i> <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 px-4 py-3 mb-4 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-5"></i> <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card card-custom border-0 overflow-hidden">
            <div class="card-header bg-white pt-2 pb-0 border-0">
                <ul class="nav nav-tabs nav-tabs-custom" id="auditTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-logs">
                            <i class="fas fa-list-ul me-2 opacity-75"></i>Historial Transaccional
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-catalogos">
                            <i class="fas fa-sliders-h me-2 opacity-75"></i>Configuración de Catálogos
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 bg-pastel-secondary bg-opacity-50">
                <div class="tab-content">

                    {{-- TAB 1: LOGS AGRUPADOS POR ACORDEÓN --}}
                    <div class="tab-pane fade show active" id="tab-logs">

                        {{-- BARRA DE BÚSQUEDA Y FILTROS OPTIMIZADOS --}}
                        <form action="{{ route('certificados.auditoria.index') }}" method="GET" class="card card-custom p-3 mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="fas fa-hashtag"></i></span>
                                        <input type="number" name="bloque" class="form-control input-custom border-start-0 ps-0" placeholder="Número de Lote" value="{{ request('bloque') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="fas fa-bolt"></i></span>
                                        <select name="evento_id" class="form-select input-custom border-start-0 ps-0">
                                            <option value="">Todos los eventos</option>
                                            @foreach($eventos as $ev)
                                                <option value="{{ $ev->id }}" {{ request('evento_id') == $ev->id ? 'selected' : '' }}>{{ $ev->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3"><i class="fas fa-network-wired"></i></span>
                                        <input type="text" name="ip" class="form-control input-custom border-start-0 ps-0" placeholder="Dirección IP" value="{{ request('ip') }}">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark w-100 fw-medium rounded-3">Filtrar</button>
                                    @if(request()->hasAny(['bloque', 'evento_id', 'ip']) && (request('bloque') != '' || request('evento_id') != '' || request('ip') != ''))
                                        <a href="{{ route('certificados.auditoria.index') }}" class="btn btn-light border rounded-3 px-3 text-muted" title="Limpiar filtros">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        {{-- CONTENEDOR DE LOTES (ACORDEÓN) --}}
                        <div class="accordion accordion-flush" id="accordionLotes">
                            @forelse($bloquesPaginados as $bloqueObj)
                                @php
                                    $bloque = $bloqueObj->numero_bloque;
                                    $grupoLogs = $logsAgrupados->get($bloque, collect());
                                @endphp

                                <div class="accordion-item accordion-item-custom">
                                    <h2 class="accordion-header" id="heading-{{ $bloque }}">
                                        <button class="accordion-button accordion-button-custom collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $bloque }}">
                                            <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                                <div class="d-flex align-items-center gap-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-pastel-primary rounded-2 p-2 me-3 text-center" style="width: 40px;">
                                                            <i class="fas fa-layer-group"></i>
                                                        </div>
                                                        <div>
                                                            <span class="d-block fs-8 text-muted fw-semibold text-uppercase letter-spacing-1 mb-1">Identificador Lote</span>
                                                            <span class="fw-bold text-dark fs-6">API-{{ str_pad($bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="border-start ps-4 py-1 d-none d-md-block">
                                                        <span class="d-block fs-8 text-muted fw-semibold mb-1">Volumen</span>
                                                        <span class="badge bg-pastel-secondary text-dark rounded-pill px-3">{{ $grupoLogs->count() }} registros</span>
                                                    </div>
                                                </div>
                                                <div class="text-end d-none d-sm-block">
                                                    <span class="d-block fs-8 text-muted fw-semibold mb-1">Fecha de Ejecución</span>
                                                    <span class="text-dark fs-7"><i class="far fa-calendar-alt me-2 text-muted"></i>{{ $grupoLogs->first() ? $grupoLogs->first()->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>

                                    <div id="collapse-{{ $bloque }}" class="accordion-collapse collapse" data-bs-parent="#accordionLotes">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive bg-white" style="max-height: 450px; overflow-y: auto;">
                                                <table class="table table-custom mb-0">
                                                    <thead class="table-sticky-header">
                                                        <tr>
                                                            <th class="ps-4">Marca de Tiempo</th>
                                                            <th>Actor / Origen IP</th>
                                                            <th>Clasificación del Evento</th>
                                                            <th class="pe-4" style="min-width: 280px;">Payload de Ejecución</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($grupoLogs as $log)
                                                        <tr>
                                                            <td class="ps-4">
                                                                <span class="d-block fw-medium text-dark">{{ $log->created_at->format('d M Y') }}</span>
                                                                <span class="text-muted fs-8">{{ $log->created_at->format('H:i:s.v') }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="d-block fw-medium text-dark">{{ $log->usuario->name ?? 'Motor Automático SIA' }}</span>
                                                                <span class="text-muted fs-8"><i class="fas fa-network-wired me-1 opacity-50"></i>{{ $log->ip ?? 'N/A' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-custom bg-pastel-primary mb-1">{{ $log->eventoAuditoria->nombre ?? 'ID: '.$log->id_car_sia_eventos_auditoria }}</span>
                                                                <div class="text-muted fs-8 mt-1"><span class="fw-semibold text-dark">Vía:</span> {{ $log->origenEvento->nombre ?? 'Desconocido' }}</div>
                                                            </td>
                                                            <td class="pe-4">
                                                                @if($log->detalles_ejecucion && (is_array($log->detalles_ejecucion) || is_object($log->detalles_ejecucion)))
                                                                    <div class="bg-pastel-secondary rounded-3 p-3 font-monospace fs-8 text-dark" style="max-height: 120px; overflow-y: auto;">
                                                                        @foreach($log->detalles_ejecucion as $key => $value)
                                                                            <div class="mb-1">
                                                                                <span class="text-muted fw-bold">{{ $key }}:</span>
                                                                                <span>{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted fs-8 fst-italic">No se adjuntó payload para este evento.</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-4 text-muted">Sin transacciones registradas en este lote.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="card card-custom p-5 text-center">
                                    <div class="bg-pastel-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 64px; height: 64px;">
                                        <i class="fas fa-search fs-4 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No se encontraron resultados</h6>
                                    <p class="text-muted fs-7 mb-0">No existen lotes que coincidan con los criterios de búsqueda actuales.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- PAGINACIÓN A NIVEL DE LOTES --}}
                        @if($bloquesPaginados->hasPages())
                            <div class="mt-4 d-flex justify-content-end">
                                {{ $bloquesPaginados->links() }}
                            </div>
                        @endif

                    </div>

                    {{-- TAB 2: CATÁLOGOS TÉCNICOS --}}
                    <div class="tab-pane fade" id="tab-catalogos">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card card-custom h-100 border-0">
                                    <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                                        <div class="bg-pastel-primary rounded-2 p-2 me-3">
                                            <i class="fas fa-project-diagram text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark m-0">Orígenes de Datos Autorizados</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush list-group-custom">
                                            @foreach($origenes as $origen)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-medium text-dark d-block">{{ $origen->nombre }}</span>
                                                        <span class="text-muted fs-8">Identificador del sistema: #{{ $origen->id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-action-icon edit-origen-btn" data-id="{{ $origen->id }}" data-nombre="{{ $origen->nombre }}" title="Modificar Origen">
                                                        <i class="fas fa-pen fs-8"></i>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card card-custom h-100 border-0">
                                    <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                                        <div class="bg-pastel-secondary rounded-2 p-2 me-3">
                                            <i class="fas fa-bolt text-dark"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark m-0">Catálogo de Eventos Auditables</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush list-group-custom">
                                            @foreach($eventos as $evento)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-medium text-dark d-block">{{ $evento->nombre }}</span>
                                                        <span class="text-muted fs-8">Identificador del sistema: #{{ $evento->id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-action-icon edit-evento-btn" data-id="{{ $evento->id }}" data-nombre="{{ $evento->nombre }}" title="Modificar Evento">
                                                        <i class="fas fa-pen fs-8"></i>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODALES CORPORATIVOS --}}
    {{-- ========================================== --}}

    {{-- Modal Crear Origen --}}
    <div class="modal fade" id="modalOrigen" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.auditoria.store_origen') }}" method="POST" class="modal-content card-custom border-0 shadow">
                @csrf
                <div class="modal-body p-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    <div class="bg-pastel-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto mt-2" style="width: 56px; height: 56px;">
                        <i class="fas fa-project-diagram fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-4">Nuevo Origen</h5>
                    <div class="text-start mb-4">
                        <label class="form-label fw-medium text-muted fs-8 mb-1">Nombre Técnico</label>
                        <input type="text" name="nombre" class="form-control input-custom rounded-3 py-2" placeholder="Ej: API Gateway, Cronjob..." required>
                    </div>
                    <button type="submit" class="btn btn-custom-primary w-100 rounded-3 py-2 fw-medium">Registrar Origen</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Crear Evento --}}
    <div class="modal fade" id="modalEvento" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.auditoria.store_evento') }}" method="POST" class="modal-content card-custom border-0 shadow">
                @csrf
                <div class="modal-body p-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    <div class="bg-pastel-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto mt-2" style="width: 56px; height: 56px;">
                        <i class="fas fa-bolt text-dark fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-4">Nuevo Evento</h5>
                    <div class="text-start mb-4">
                        <label class="form-label fw-medium text-muted fs-8 mb-1">Descripción del Evento</label>
                        <input type="text" name="nombre" class="form-control input-custom rounded-3 py-2" placeholder="Ej: Actualización Masiva..." required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-3 py-2 fw-medium">Registrar Evento</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR Origen --}}
    <div class="modal fade" id="modalEditOrigen" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form id="formEditOrigen" method="POST" class="modal-content card-custom border-0 shadow">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    <div class="bg-pastel-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto mt-2" style="width: 56px; height: 56px;">
                        <i class="fas fa-pen fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-4">Modificar Origen</h5>
                    <div class="text-start mb-4">
                        <label class="form-label fw-medium text-muted fs-8 mb-1">Nombre Técnico Actual</label>
                        <input type="text" name="nombre" id="edit_origen_nombre" class="form-control input-custom rounded-3 py-2 fw-medium text-dark" required>
                    </div>
                    <button type="submit" class="btn btn-custom-primary w-100 rounded-3 py-2 fw-medium">Aplicar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR Evento --}}
    <div class="modal fade" id="modalEditEvento" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form id="formEditEvento" method="POST" class="modal-content card-custom border-0 shadow">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    <div class="bg-pastel-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto mt-2" style="width: 56px; height: 56px;">
                        <i class="fas fa-pen text-dark fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-4">Modificar Evento</h5>
                    <div class="text-start mb-4">
                        <label class="form-label fw-medium text-muted fs-8 mb-1">Descripción del Evento Actual</label>
                        <input type="text" name="nombre" id="edit_evento_nombre" class="form-control input-custom rounded-3 py-2 fw-medium text-dark" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-3 py-2 fw-medium">Aplicar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Configuración general de SweetAlert corporativo
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'card-custom' }
            });

            // Lógica Dinámica para Editar Orígenes
            document.querySelectorAll('.edit-origen-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let nombre = this.getAttribute('data-nombre');

                    document.getElementById('edit_origen_nombre').value = nombre;
                    document.getElementById('formEditOrigen').action = "{{ url('certificados/auditoria/origen') }}/" + id;

                    new bootstrap.Modal(document.getElementById('modalEditOrigen')).show();
                });
            });

            // Lógica Dinámica para Editar Eventos
            document.querySelectorAll('.edit-evento-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let nombre = this.getAttribute('data-nombre');

                    document.getElementById('edit_evento_nombre').value = nombre;
                    document.getElementById('formEditEvento').action = "{{ url('certificados/auditoria/evento') }}/" + id;

                    new bootstrap.Modal(document.getElementById('modalEditEvento')).show();
                });
            });
        });
    </script>
</x-base-layout>
