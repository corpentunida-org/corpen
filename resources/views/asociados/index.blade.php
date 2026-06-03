<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">ECM - Módulo de Asociados</span>
                <h1 class="main-title">Directorio de Expedientes</h1>
                <p class="main-subtitle">Gestión centralizada de información pastoral y documental</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.create') }}" class="btn-corporate-black shadow-sm">
                    <i class="fas fa-plus me-1"></i> <span>Aperturar Expediente</span>
                </a>
            </div>
        </header>

        {{-- WIDGETS DE ESTADÍSTICAS --}}
        <div class="row g-3 mb-4">
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'total', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-primary h-100 {{ request('filter') == 'total' || !request('filter') ? 'shadow-md bg-light' : 'hover-elevate' }}">
                        <div class="widget-icon bg-primary-light text-primary"><i class="fas fa-users"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $totalExpedientes }}</h3>
                            <span class="widget-title text-muted">Total</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'activos', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-success h-100 {{ request('filter') == 'activos' ? 'shadow-md bg-light' : 'hover-elevate' }}">
                        <div class="widget-icon bg-success-light text-success"><i class="fas fa-user-check"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $expedientesActivos }}</h3>
                            <span class="widget-title text-muted">Activos</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'inactivos', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-danger h-100 {{ request('filter') == 'inactivos' ? 'shadow-md bg-light' : 'hover-elevate' }}">
                        <div class="widget-icon bg-danger-light text-danger"><i class="fas fa-user-times"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $expedientesInactivos }}</h3>
                            <span class="widget-title text-muted">Inactivos</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'ecm', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-info h-100 {{ request('filter') == 'ecm' ? 'shadow-md bg-light' : 'hover-elevate' }}">
                        <div class="widget-icon bg-info-light text-info"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $digitalizadosEcm }}</h3>
                            <span class="widget-title text-muted">Cargados ECM</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'pendientes', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-warning h-100 {{ request('filter') == 'pendientes' ? 'shadow-md bg-light' : 'hover-elevate' }}">
                        <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-folder-minus"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $pendientesArchivo }}</h3>
                            <span class="widget-title text-muted">Sin Radicar</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- BUSCADOR --}}
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3 bg-white rounded-3 border">
                <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                    @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Buscar por cédula, nombre, distrito o ciudad..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn-corporate-black w-100 justify-content-center">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        @if(request('search') || request('filter'))
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center text-decoration-none">
                                <i class="fas fa-eraser me-1"></i> Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ALERTA DE ÉXITO --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0 bg-success text-white" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- TABLA DE RESULTADOS --}}
        <div class="show-card-corp shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="show-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" id="table-asociados">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4 text-secondary fw-bold">Cédula</th>
                                <th class="text-secondary fw-bold">Nombre Completo</th>
                                <th class="text-secondary fw-bold">Distrito / Ciudad</th>
                                <th class="text-secondary fw-bold">Rol Ministerial</th>
                                <th class="text-secondary fw-bold">Estado</th>
                                <th class="text-center pe-4 text-secondary fw-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($asociados as $asociado)
                                <tr data-ubicacion="{{ $asociado->ubicacion_carpeta ?? 'No asignada' }}"
                                    data-caja="{{ $asociado->numero_caja ?? 'N/A' }}"
                                    data-folios="{{ $asociado->cantidad_folios ?? '0' }}"
                                    data-fecha="{{ $asociado->fecha_ingreso_archivo ? \Carbon\Carbon::parse($asociado->fecha_ingreso_archivo)->format('d/m/Y') : 'No registrada' }}"
                                    data-conservacion="{{ $asociado->estado_conservacion ?? 'No evaluado' }}"
                                    data-custodia="{{ $asociado->custodia_actual ?? 'No definida' }}"
                                    data-observaciones="{{ $asociado->observaciones_archivo ?? 'Sin observaciones adicionales.' }}"
                                    data-nombre="{{ $asociado->nombre_completo }}">
                                    
                                    {{-- CÉDULA: Enlace hacia el ECM si existe --}}
                                    <td class="ps-4 fw-bold">
                                        @if(!empty($asociado->ubicacion_ecm_link))
                                            <a href="{{ $asociado->ubicacion_ecm_link }}" target="_blank" class="text-primary text-decoration-none" title="Abrir expediente en ECM">
                                                <i class="fas fa-external-link-alt me-1 small opacity-75"></i>{{ $asociado->cedula }}
                                            </a>
                                        @else
                                            <span class="text-dark" title="Sin enlace ECM">{{ $asociado->cedula }}</span>
                                        @endif
                                    </td>
                                    
                                    {{-- NOMBRE COMPLETO --}}
                                    <td class="fw-bold text-dark nombre-trigger" style="cursor: pointer; position: relative;">
                                        <span title="Ver detalles del archivo físico">{{ $asociado->nombre_completo }}</span>
                                        <i class="fas fa-info-circle text-primary opacity-50 ms-1 small"></i>
                                    </td>
                                    
                                    {{-- DISTRITO Y CIUDAD (Llamando a la relación) --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            {{-- Se asume que en el modelo MaeAsociado tienes métodos distrito() y ciudad() --}}
                                            <span class="text-dark fw-semibold">{{ $asociado->distrito?->NOM_DIST ?? $asociado->distrito_actual ?? 'N/A' }}</span>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1 opacity-50"></i>{{ $asociado->ciudad?->nombre ?? $asociado->ciudad_distrito ?? 'Sin ciudad' }}</small>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $asociado->estado_pastor ?? 'No definido' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge-large {{ $asociado->estado == 'Activo' ? 'st-completed' : 'st-danger' }}" style="padding: 0.35rem 0.8rem; font-size: 0.75rem;">
                                            <i class="fas fa-circle" style="font-size: 0.5rem; vertical-align: middle; margin-right: 3px;"></i> {{ $asociado->estado ?? 'Activo' }}
                                        </span>
                                    </td>
                                    
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('asociados.maestro.show', $asociado->id) }}" class="btn btn-sm btn-light border hover-bg-primary" title="Auditar Expediente">
                                                <i class="fas fa-folder-open text-primary"></i>
                                            </a>
                                            <a href="{{ route('asociados.maestro.edit', $asociado->id) }}" class="btn btn-sm btn-light border hover-bg-warning" title="Editar">
                                                <i class="fas fa-pen text-warning"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted bg-light">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="fas fa-folder-open fs-1 text-secondary mb-3 opacity-50"></i>
                                            <h5 class="fw-bold">No hay resultados</h5>
                                            <p class="mb-0">No se encontraron expedientes con los filtros aplicados.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($asociados->hasPages())
                <div class="card-footer bg-light border-top p-3 d-flex justify-content-center">
                    {{ $asociados->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL PREVIEW --}}
    <div id="archive-preview-card" class="archive-preview-card shadow-lg border-0" style="display: none; position: absolute; z-index: 1050; background: #ffffff; border-radius: 12px; padding: 20px; width: 320px; transition: opacity 0.2s ease;">
        <div class="preview-header mb-3 border-bottom pb-3 d-flex align-items-start">
            <div class="bg-primary-light p-2 rounded me-3 text-primary">
                <i class="fas fa-box-archive fs-4"></i>
            </div>
            <div>
                <span class="badge bg-dark mb-1 px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">UBICACIÓN FÍSICA</span>
                <h6 id="p-nombre" class="m-0 fw-bold text-dark lh-sm">Nombre Asociado</h6>
            </div>
        </div>
        <div class="preview-body" style="font-size: 0.85rem;">
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem;"><i class="fas fa-folder me-1"></i> Ubicación</span>
                    <strong id="p-ubicacion" class="text-dark">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem;"><i class="fas fa-box me-1"></i> N° Caja</span>
                    <strong id="p-caja" class="text-dark">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem;"><i class="fas fa-file-lines me-1"></i> Folios</span>
                    <strong id="p-folios" class="text-dark">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem;"><i class="fas fa-calendar-day me-1"></i> Ingreso</span>
                    <strong id="p-fecha" class="text-dark">--</strong>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded border">
                <div>
                    <span class="text-muted d-block text-uppercase mb-1" style="font-size: 0.7rem;"><i class="fas fa-shield-heart me-1"></i> Conservación</span>
                    <div id="p-conservacion" class="fw-bold">--</div>
                </div>
                <div class="text-end">
                    <span class="text-muted d-block text-uppercase mb-1" style="font-size: 0.7rem;"><i class="fas fa-user-shield me-1"></i> Custodia</span>
                    <strong id="p-custodia" class="text-dark">--</strong>
                </div>
            </div>

            <div class="mt-2">
                <span class="text-muted fw-bold d-block mb-1 text-uppercase" style="font-size: 0.7rem;"><i class="fas fa-comment-dots me-1"></i> Observaciones:</span>
                <p id="p-observaciones" class="m-0 text-secondary fst-italic lh-sm bg-light p-2 rounded border-start border-3 border-secondary">--</p>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .hover-bg-primary:hover i { color: #fff !important; }
        .hover-bg-primary:hover { background-color: #0d6efd !important; border-color: #0d6efd !important; }
        .hover-bg-warning:hover i { color: #fff !important; }
        .hover-bg-warning:hover { background-color: #ffc107 !important; border-color: #ffc107 !important; }
    </style>
    @endpush

    @include('asociados.partials.styles')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const previewCard = document.getElementById('archive-preview-card');
            const nombreTriggers = document.querySelectorAll('.nombre-trigger');

            nombreTriggers.forEach(trigger => {
                trigger.addEventListener('mouseenter', function (e) {
                    const row = this.closest('tr');

                    // Llenar datos dinámicos
                    document.getElementById('p-nombre').textContent = row.getAttribute('data-nombre');
                    document.getElementById('p-ubicacion').textContent = row.getAttribute('data-ubicacion');
                    document.getElementById('p-caja').textContent = row.getAttribute('data-caja');
                    document.getElementById('p-folios').textContent = row.getAttribute('data-folios');
                    document.getElementById('p-fecha').textContent = row.getAttribute('data-fecha');
                    document.getElementById('p-custodia').textContent = row.getAttribute('data-custodia');
                    document.getElementById('p-observaciones').textContent = row.getAttribute('data-observaciones');

                    const conservacion = row.getAttribute('data-conservacion');
                    const pConservacion = document.getElementById('p-conservacion');
                    pConservacion.textContent = conservacion;
                    
                    pConservacion.className = 'badge font-weight-bold px-2 py-1 ';
                    if(conservacion.toLowerCase().includes('buen') || conservacion.toLowerCase().includes('óptim')) {
                        pConservacion.classList.add('bg-success', 'text-white');
                    } else if(conservacion.toLowerCase().includes('regular') || conservacion.toLowerCase().includes('escas')) {
                        pConservacion.classList.add('bg-warning', 'text-dark');
                    } else {
                        pConservacion.classList.add('bg-danger', 'text-white');
                    }

                    previewCard.style.display = 'block';
                    previewCard.style.opacity = '1';
                });

                trigger.addEventListener('mousemove', function (e) {
                    const cardWidth = previewCard.offsetWidth;
                    const cardHeight = previewCard.offsetHeight;
                    
                    let leftPos = e.clientX + 20;
                    if (leftPos + cardWidth > window.innerWidth) {
                        leftPos = e.clientX - cardWidth - 20;
                    }

                    let topPos = e.clientY + window.scrollY - (cardHeight / 2);
                    if (e.clientY + cardHeight > window.innerHeight) {
                        topPos = e.clientY + window.scrollY - cardHeight + 20;
                    }

                    previewCard.style.left = leftPos + 'px';
                    previewCard.style.top = topPos + 'px';
                });

                trigger.addEventListener('mouseleave', function () {
                    previewCard.style.display = 'none';
                    previewCard.style.opacity = '0';
                });
            });
        });
    </script>
</x-base-layout>