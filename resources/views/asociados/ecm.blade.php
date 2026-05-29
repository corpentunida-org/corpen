<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-server me-1"></i> ECM - Gestión Documental Avanzada</span>
                <h1 class="main-title">Control de Archivo Físico y Digital</h1>
                <p class="main-subtitle">Supervisión volumétrica, trazabilidad de carpetas y estatus de indexación en plataforma.</p>
            </div>
            <div class="header-actions">
                <button class="btn-ghost-corporate me-2" onclick="window.print()">
                    <i class="fas fa-print"></i> <span>Screenshot</span>
                </button>
                <a href="{{ route('asociados.maestro.index') }}" class="btn-corporate-black">
                    <i class="fas fa-address-book"></i> <span>Ver Directorio General</span>
                </a>
            </div>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dash-widget border-bottom-info">
                    <div class="widget-icon bg-info-light text-info"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Volumen Digitalizado (ECM)</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $digitalizadosEcm }}</h3>
                            <span class="badge bg-info text-dark shadow-sm">Indexados</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dash-widget border-bottom-success">
                    <div class="widget-icon bg-success-light text-success"><i class="fas fa-check-double"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Validados por Auditoría</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $validados }}</h3>
                            <span class="badge bg-success shadow-sm">Aprobados</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dash-widget border-bottom-warning">
                    <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-archive"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Sin Radicación Física</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $pendientesArchivo }}</h3>
                            <span class="badge bg-warning text-dark shadow-sm">Pendientes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3 bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center w-50">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Buscar por Radicado, Cédula o Nombre..." aria-label="Buscar">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm text-muted" style="width: 180px;">
                        <option value="">Estado Digitalización</option>
                        <option value="1">Completados</option>
                        <option value="0">En Proceso</option>
                    </select>
                    <button class="btn btn-sm btn-light border"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </div>
        </div>

        <div class="show-card-corp shadow-sm">
            <div class="show-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0 ecm-table">
                        <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4" style="width: 15%;">Radicado / ID</th>
                                <th style="width: 25%;">Titular del Expediente</th>
                                <th style="width: 20%;">Ubicación Topográfica</th>
                                <th style="width: 20%;">Progreso Digital (ECM)</th>
                                <th style="width: 15%;">Custodia</th>
                                <th class="text-center pe-4" style="width: 5%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asociados as $asociado)
                                @php
                                    // Cálculo lógico del progreso de digitalización
                                    $score = 0;
                                    if($asociado->escaneado) $score += 33;
                                    if($asociado->cargado_ecm) $score += 33;
                                    if($asociado->validado_archivo) $score += 34;
                                    
                                    $progressColor = $score == 100 ? 'bg-success' : ($score > 30 ? 'bg-warning' : 'bg-danger');
                                    
                                    // Generación de Iniciales para el Avatar
                                    $iniciales = strtoupper(substr($asociado->nombre1, 0, 1) . substr($asociado->apellido1, 0, 1));
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="radicado-icon me-2 {{ $asociado->radicado ? 'bg-light-primary text-primary' : 'bg-light-danger text-danger' }}">
                                                <i class="fas fa-barcode"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $asociado->radicado ?? 'SIN ASIGNAR' }}</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">Cód: #{{ str_pad($asociado->id, 5, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">{{ $iniciales }}</div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $asociado->nombre_completo }}</span>
                                                <small class="text-muted"><i class="far fa-id-card me-1"></i> {{ $asociado->cedula }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="location-box">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small"><i class="fas fa-box text-warning me-1"></i> Caja:</span>
                                                <strong class="text-dark">{{ $asociado->numero_caja ?? '--' }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small"><i class="fas fa-folder-open text-warning me-1"></i> Carpeta:</span>
                                                <strong class="text-dark">{{ $asociado->ubicacion_carpeta ?? '--' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-muted">Estatus</span>
                                            <span class="small fw-bold {{ 'text-'.str_replace('bg-','',$progressColor) }}">{{ $score }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;" data-bs-toggle="tooltip" title="Escaneado: {{ $asociado->escaneado ? 'Sí' : 'No' }} | Cargado: {{ $asociado->cargado_ecm ? 'Sí' : 'No' }} | Validado: {{ $asociado->validado_archivo ? 'Sí' : 'No' }}">
                                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $score }}%" aria-valuenow="{{ $score }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ $asociado->custodia_actual ?? 'Archivo Central' }}</span>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i> {{ $asociado->fecha_ingreso_archivo ? $asociado->fecha_ingreso_archivo->format('d M Y') : 'Sin fecha' }}</small>
                                    </td>
                                    
                                    <td class="text-center pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; line-height: 1;">
                                                <i class="fas fa-ellipsis-v text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 0.85rem;">
                                                <li><h6 class="dropdown-header">Acciones del Expediente</h6></li>
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('asociados.maestro.show', $asociado->id) }}">
                                                        <i class="fas fa-eye text-primary me-2"></i> Auditar Expediente
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('asociados.maestro.edit', $asociado->id) }}">
                                                        <i class="fas fa-pen text-warning me-2"></i> Editar Metadatos
                                                    </a>
                                                </li>
                                                @if($asociado->ubicacion_ecm_link)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ $asociado->ubicacion_ecm_link }}" target="_blank">
                                                        <i class="fas fa-external-link-alt text-info me-2"></i> Ver en Plataforma ECM
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                            <h5 class="text-dark fw-bold">Repositorio Vacío</h5>
                                            <p class="text-muted">No existen expedientes para gestionar en la bandeja actual.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($asociados->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Mostrando registros paginados del repositorio.</span>
                    {{ $asociados->links() }}
                </div>
            @endif
        </div>
    </div>
    @include('asociados.partials.styles')
    
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</x-base-layout>