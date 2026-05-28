<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">ECM - Módulo de Asociados</span>
                <h1 class="main-title">Directorio de Expedientes</h1>
                <p class="main-subtitle">Gestión centralizada de información pastoral y documental</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.create') }}" class="btn-corporate-black">
                    <i class="fas fa-plus"></i> <span>Aperturar Expediente</span>
                </a>
            </div>
        </header>

        <div class="row g-3 mb-4">
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'total', 'page' => null]) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-primary h-100 {{ request('filter') == 'total' || !request('filter') ? 'shadow-md bg-light' : '' }}">
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
                    <div class="dash-widget border-bottom-success h-100 {{ request('filter') == 'activos' ? 'shadow-md bg-light' : '' }}">
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
                    <div class="dash-widget border-bottom-danger h-100 {{ request('filter') == 'inactivos' ? 'shadow-md bg-light' : '' }}">
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
                    <div class="dash-widget border-bottom-info h-100 {{ request('filter') == 'ecm' ? 'shadow-md bg-light' : '' }}">
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
                    <div class="dash-widget border-bottom-warning h-100 {{ request('filter') == 'pendientes' ? 'shadow-md bg-light' : '' }}">
                        <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-folder-minus"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $pendientesArchivo }}</h3>
                            <span class="widget-title text-muted">Sin Radicar</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3 bg-white rounded-3 border">
                <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                    @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Buscar por cédula, nombre, distrito o ciudad..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn-corporate-black w-100 justify-content-center">Buscar</button>
                        @if(request('search') || request('filter'))
                            <a href="{{ url()->current() }}" class="btn-ghost-corporate w-100 justify-content-center text-decoration-none">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="show-card-corp">
            <div class="show-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" id="table-asociados">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Cédula</th>
                                <th>Nombre Completo</th>
                                <th>Distrito / Ciudad</th>
                                <th>Rol Ministerial</th>
                                <th>Estado Expediente</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asociados as $asociado)
                                <tr class="row-hover-archive" 
                                    data-ubicacion="{{ $asociado->ubicacion_carpeta ?? 'No asignada' }}"
                                    data-caja="{{ $asociado->numero_caja ?? 'N/A' }}"
                                    data-folios="{{ $asociado->cantidad_folios ?? '0' }}"
                                    data-fecha="{{ $asociado->fecha_ingreso_archivo ? \Carbon\Carbon::parse($asociado->fecha_ingreso_archivo)->format('d/m/Y') : 'No registrada' }}"
                                    data-conservacion="{{ $asociado->estado_conservacion ?? 'No evaluado' }}"
                                    data-custodia="{{ $asociado->custodia_actual ?? 'No definida' }}"
                                    data-observaciones="{{ $asociado->observaciones_archivo ?? 'Sin observaciones adicionales.' }}"
                                    data-nombre="{{ $asociado->nombre_completo }}">
                                    
                                    <td class="ps-4 fw-bold text-primary">{{ $asociado->cedula }}</td>
                                    <td class="fw-bold text-dark">{{ $asociado->nombre_completo }}</td>
                                    <td>
                                        {{ $asociado->distrito_actual ?? 'N/A' }} <br>
                                        <small class="text-muted">{{ $asociado->ciudad_distrito ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $asociado->estado_pastor ?? 'No definido' }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge-large {{ $asociado->estado == 'Activo' ? 'st-completed' : 'st-danger' }}" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;">
                                            <i class="fas fa-circle"></i> {{ $asociado->estado ?? 'Activo' }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('asociados.maestro.show', $asociado->id) }}" class="btn btn-sm btn-light border" title="Auditar Expediente">
                                                <i class="fas fa-folder-open text-primary"></i>
                                            </a>
                                            <a href="{{ route('asociados.maestro.edit', $asociado->id) }}" class="btn btn-sm btn-light border" title="Editar">
                                                <i class="fas fa-pen text-warning"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fs-1 text-light mb-3 d-block"></i>
                                        No se encontraron expedientes con los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($asociados->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $asociados->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="archive-preview-card" class="archive-preview-card" style="display: none;">
        <div class="preview-header">
            <i class="fas fa-box-archive text-primary-corporate"></i>
            <div>
                <span class="preview-tag">Expediente Físico</span>
                <h5 id="p-nombre" class="preview-title">Nombre Asociado</h5>
            </div>
        </div>
        <div class="preview-body">
            <div class="preview-grid">
                <div class="p-item">
                    <span class="p-label"><i class="fas fa-folder me-1"></i> Ubicación</span>
                    <strong id="p-ubicacion">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label"><i class="fas fa-box me-1"></i> N° Caja</span>
                    <strong id="p-caja">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label"><i class="fas fa-file-lines me-1"></i> Folios</span>
                    <strong id="p-folios">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label"><i class="fas fa-calendar-day me-1"></i> Ingreso</span>
                    <strong id="p-fecha">--</strong>
                </div>
            </div>
            <div class="preview-divider"></div>
            <div class="mb-2">
                <span class="p-label"><i class="fas fa-shield-heart me-1"></i> Conservación</span>
                <div id="p-conservacion" class="mt-1">--</div>
            </div>
            <div class="mb-2">
                <span class="p-label"><i class="fas fa-user-shield me-1"></i> Custodia Actual</span>
                <strong id="p-custodia" class="text-dark d-block">--</strong>
            </div>
            <div class="preview-notes">
                <span class="p-label">Observaciones de Archivo</span>
                <p id="p-observaciones">--</p>
            </div>
        </div>
    </div>

    @include('asociados.partials.styles')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const previewCard = document.getElementById('archive-preview-card');
            const tableRows = document.querySelectorAll('.row-hover-archive');

            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function (e) {
                    // Cargar los datos dinámicamente desde los data attributes
                    document.getElementById('p-nombre').textContent = this.getAttribute('data-nombre');
                    document.getElementById('p-ubicacion').textContent = this.getAttribute('data-ubicacion');
                    document.getElementById('p-caja').textContent = this.getAttribute('data-caja');
                    document.getElementById('p-folios').textContent = this.getAttribute('data-folios');
                    document.getElementById('p-fecha').textContent = this.getAttribute('data-fecha');
                    document.getElementById('p-custodia').textContent = this.getAttribute('data-custodia');
                    document.getElementById('p-observaciones').textContent = this.getAttribute('data-observaciones');

                    // Estilo dinámico para el estado de conservación
                    const conservacion = this.getAttribute('data-conservacion');
                    const pConservacion = document.getElementById('p-conservacion');
                    pConservacion.textContent = conservacion;
                    
                    // Clases dinámicas de color según estado
                    pConservacion.className = 'badge font-weight-bold p-1 px-2 ';
                    if(conservacion.toLowerCase().includes('buen') || conservacion.toLowerCase().includes('óptim')) {
                        pConservacion.classList.add('bg-success-light', 'text-success', 'border', 'border-success');
                    } else if(conservacion.toLowerCase().includes('regular') || conservacion.toLowerCase().includes('escas')) {
                        pConservacion.classList.add('bg-warning-light', 'text-warning', 'border', 'border-warning');
                    } else {
                        pConservacion.classList.add('bg-danger-light', 'text-danger', 'border', 'border-danger');
                    }

                    previewCard.style.display = 'block';
                });

                row.addEventListener('mousemove', function (e) {
                    // Posicionamiento dinámico al lado del cursor para un look nativo
                    const cardWidth = previewCard.offsetWidth;
                    const cardHeight = previewCard.offsetHeight;
                    
                    // Evitamos que la tarjeta se salga por el borde derecho de la pantalla
                    let leftPos = e.clientX + 20;
                    if (leftPos + cardWidth > window.innerWidth) {
                        leftPos = e.clientX - cardWidth - 20;
                    }

                    // Evitamos que se salga por el borde inferior
                    let topPos = e.clientY + window.scrollY - (cardHeight / 2);
                    if (e.clientY + cardHeight > window.innerHeight) {
                        topPos = e.clientY + window.scrollY - cardHeight + 20;
                    }

                    previewCard.style.left = leftPos + 'px';
                    previewCard.style.top = topPos + 'px';
                });

                row.addEventListener('mouseleave', function () {
                    previewCard.style.display = 'none';
                });
            });
        });
    </script>
</x-base-layout>