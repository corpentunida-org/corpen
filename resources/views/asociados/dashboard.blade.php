<x-base-layout>
    <div class="task-index-wrapper bg-light" style="min-height: 100vh;">
        <!-- ==========================================
             HEADER DEL COMMAND CENTER
             ========================================== -->
        <header class="index-header mb-4 pb-3 border-bottom">
            <div class="header-titles">
                <span class="system-tag text-primary fw-bold"><i class="fas fa-chart-line me-1"></i> BI & Data Analytics</span>
                <h1 class="main-title text-dark fw-bolder">Tablero Estratégico ECM</h1>
                <p class="main-subtitle text-muted">Métricas ministeriales, control topográfico y auditoría en tiempo real.</p>
            </div>
            <div class="header-actions">
                <button class="btn-ghost-corporate me-2 shadow-sm" onclick="window.print()">
                    <i class="fas fa-print text-primary"></i> <span>Exportar Reporte</span>
                </button>
                <a href="{{ route('asociados.maestro.create') }}" class="btn-corporate-black shadow-sm">
                    <i class="fas fa-folder-plus me-1"></i> <span>Nuevo Expediente</span>
                </a>
            </div>
        </header>

        <!-- ==========================================
             FILA 1: KPIs GLOBALES (WIDGETS ENLAZADOS)
             ========================================== -->
        <div class="row g-3 mb-4">
            <!-- Total Registros -->
            <div class="col">
                <a href="{{ route('asociados.maestro.index', ['filter' => 'total']) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-primary h-100 shadow-sm">
                        <div class="widget-icon bg-primary-light text-primary"><i class="fas fa-users"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $total }}</h3>
                            <span class="widget-title text-muted">Población Total</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Activos -->
            <div class="col">
                <a href="{{ route('asociados.maestro.index', ['filter' => 'activos']) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-success h-100 shadow-sm">
                        <div class="widget-icon bg-success-light text-success"><i class="fas fa-user-check"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $estadosPastor['Activo'] ?? 0 }}</h3>
                            <span class="widget-title text-muted">Pastores Activos</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Suspendidos/Retirados -->
            <div class="col">
                <a href="{{ route('asociados.maestro.index', ['filter' => 'inactivos']) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-danger h-100 shadow-sm">
                        <div class="widget-icon bg-danger-light text-danger"><i class="fas fa-user-times"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ ($estadosPastor['Retirado'] ?? 0) + ($estadosPastor['Suspendido'] ?? 0) }}</h3>
                            <span class="widget-title text-muted">Inactivos/Retirados</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Validados ECM -->
            <div class="col">
                <a href="{{ route('asociados.ecm.index', ['filter' => 'ecm']) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-info h-100 shadow-sm">
                        <div class="widget-icon bg-info-light text-info"><i class="fas fa-check-double"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $ecmPipeline['validados'] }}</h3>
                            <span class="widget-title text-muted">Validados ECM</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Físicos Pendientes -->
            <div class="col">
                <a href="{{ route('asociados.ecm.index', ['filter' => 'pendientes']) }}" class="text-decoration-none d-block h-100">
                    <div class="dash-widget border-bottom-warning h-100 shadow-sm">
                        <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-folder-minus"></i></div>
                        <div class="widget-details">
                            <h3 class="widget-number text-dark">{{ $pendientesRadicado }}</h3>
                            <span class="widget-title text-muted">Sin Radicar</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- ==========================================
             FILA 2: MOTOR GRÁFICO Y EMBUDO ECM
             ========================================== -->
        <div class="row g-4 mb-4">
            <!-- Tarta 1: Estatus Ministerial -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold text-uppercase text-muted small"><i class="fas fa-chart-pie me-2 text-primary"></i> Estatus Ministerial</h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2 d-flex justify-content-center align-items-center" style="position: relative; height: 280px;">
                        @if(empty($estadosPastor))
                            <span class="text-muted small">Sin datos suficientes</span>
                        @else
                            <canvas id="ministerialChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tarta 2: Demografía (Estado Civil) -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold text-uppercase text-muted small"><i class="fas fa-chart-doughnut me-2 text-info"></i> Demografía</h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2 d-flex justify-content-center align-items-center" style="position: relative; height: 280px;">
                        @if(empty($estadosCiviles))
                            <span class="text-muted small">Sin datos suficientes</span>
                        @else
                            <canvas id="demografiaChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Embudo ECM (Barras de Progreso) -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h6 class="fw-bold text-uppercase text-muted small"><i class="fas fa-filter me-2 text-success"></i> Trazabilidad Documental</h6>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $pEscaneado = $total > 0 ? round(($ecmPipeline['escaneados'] / $total) * 100) : 0;
                            $pCargado   = $total > 0 ? round(($ecmPipeline['cargados'] / $total) * 100) : 0;
                            $pValidado  = $total > 0 ? round(($ecmPipeline['validados'] / $total) * 100) : 0;
                        @endphp
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-dark small"><i class="fas fa-print text-muted me-1"></i> 1. Escaneados</span>
                                <span class="fw-bold text-secondary small">{{ $ecmPipeline['escaneados'] }} ({{ $pEscaneado }}%)</span>
                            </div>
                            <div class="progress shadow-none" style="height: 12px; background-color: #f1f3f5;">
                                <div class="progress-bar bg-secondary" style="width: {{ $pEscaneado }}%"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-dark small"><i class="fas fa-cloud-upload-alt text-primary me-1"></i> 2. Plataforma ECM</span>
                                <span class="fw-bold text-primary small">{{ $ecmPipeline['cargados'] }} ({{ $pCargado }}%)</span>
                            </div>
                            <div class="progress shadow-none" style="height: 12px; background-color: #f1f3f5;">
                                <div class="progress-bar bg-primary" style="width: {{ $pCargado }}%"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-dark small"><i class="fas fa-shield-check text-success me-1"></i> 3. Validados</span>
                                <span class="fw-bold text-success small">{{ $ecmPipeline['validados'] }} ({{ $pValidado }}%)</span>
                            </div>
                            <div class="progress shadow-none" style="height: 12px; background-color: #f1f3f5;">
                                <div class="progress-bar bg-success" style="width: {{ $pValidado }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             FILA 3: AUDITORÍA RECIENTE CON HOVER PREVIEW
             ========================================== -->
        <div class="card shadow-sm border-0 mb-5 rounded-3">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-uppercase text-muted small m-0"><i class="fas fa-history me-2 text-danger"></i> Últimos Ingresos al Repositorio</h6>
                <a href="{{ route('asociados.maestro.index') }}" class="btn btn-sm btn-outline-dark" style="font-size: 0.75rem;">Explorar Directorio</a>
            </div>
            <div class="card-body p-0">
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
                            @forelse($ultimosIngresos as $asociado)
                                <!-- FILA CON DATA-ATTRIBUTES PARA EL HOVER CARD -->
                                <tr class="row-hover-archive" style="cursor: pointer;"
                                    data-ubicacion="{{ $asociado->ubicacion_carpeta ?? 'No asignada' }}"
                                    data-caja="{{ $asociado->numero_caja ?? 'N/A' }}"
                                    data-folios="{{ $asociado->cantidad_folios ?? '0' }}"
                                    data-fecha="{{ $asociado->fecha_ingreso_archivo ? \Carbon\Carbon::parse($asociado->fecha_ingreso_archivo)->format('d/m/Y') : 'No registrada' }}"
                                    data-conservacion="{{ $asociado->estado_conservacion ?? 'No evaluado' }}"
                                    data-custodia="{{ $asociado->custodia_actual ?? 'Archivo Central' }}"
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
                                        No hay registros recientes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         TARJETA FLOTANTE (HOVER PREVIEW CARD)
         ========================================== -->
    <div id="archive-preview-card" class="archive-preview-card shadow-lg border-0" style="display: none;">
        <div class="preview-header bg-dark text-white p-3 rounded-top d-flex align-items-center">
            <i class="fas fa-box-archive fa-2x me-3 text-info"></i>
            <div>
                <span class="preview-tag text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px; color: #adb5bd;">Expediente Físico</span>
                <h6 id="p-nombre" class="preview-title m-0 fw-bold">Nombre Asociado</h6>
            </div>
        </div>
        <div class="preview-body p-3 bg-white rounded-bottom">
            <div class="preview-grid d-grid gap-3 mb-3" style="grid-template-columns: 1fr 1fr;">
                <div class="p-item">
                    <span class="p-label d-block text-muted small"><i class="fas fa-folder me-1 text-warning"></i> Ubicación</span>
                    <strong id="p-ubicacion" class="text-dark">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label d-block text-muted small"><i class="fas fa-box me-1 text-warning"></i> N° Caja</span>
                    <strong id="p-caja" class="text-dark">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label d-block text-muted small"><i class="fas fa-file-lines me-1 text-primary"></i> Folios</span>
                    <strong id="p-folios" class="text-dark">--</strong>
                </div>
                <div class="p-item">
                    <span class="p-label d-block text-muted small"><i class="fas fa-calendar-day me-1 text-primary"></i> Ingreso</span>
                    <strong id="p-fecha" class="text-dark">--</strong>
                </div>
            </div>
            <hr class="text-muted opacity-25 my-2">
            <div class="mb-2 d-flex justify-content-between align-items-center">
                <span class="p-label text-muted small"><i class="fas fa-shield-heart me-1"></i> Conservación</span>
                <div id="p-conservacion" class="badge">--</div>
            </div>
            <div class="mb-2 d-flex justify-content-between align-items-center">
                <span class="p-label text-muted small"><i class="fas fa-user-shield me-1"></i> Custodia</span>
                <strong id="p-custodia" class="text-dark small">--</strong>
            </div>
            <div class="preview-notes bg-light p-2 rounded mt-2 border">
                <span class="p-label text-muted small fw-bold d-block mb-1">Observaciones:</span>
                <p id="p-observaciones" class="m-0 small text-dark fst-italic">--</p>
            </div>
        </div>
    </div>

    @include('asociados.partials.styles')

    <!-- ESTILOS EXCLUSIVOS PARA LA TARJETA PREVIEW -->
    <style>
        .archive-preview-card {
            position: absolute;
            z-index: 9999;
            width: 320px;
            background: #fff;
            border-radius: 8px;
            pointer-events: none; /* Crucial para que no parpadee al mover el ratón */
            transition: opacity 0.15s ease-in-out;
        }
    </style>

    <!-- ==========================================
         SCRIPTS: CHART.JS & PREVIEW CARD
         ========================================== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. INICIALIZACIÓN DE GRÁFICAS (CHART.JS)
            Chart.defaults.font.family = "'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
            Chart.defaults.color = '#6c757d';

            const ministerialData = @json($estadosPastor);
            if(Object.keys(ministerialData).length > 0) {
                new Chart(document.getElementById('ministerialChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(ministerialData),
                        datasets: [{
                            data: Object.values(ministerialData),
                            backgroundColor: ['#198754', '#ffc107', '#dc3545', '#6c757d', '#0d6efd'],
                            borderWidth: 2,
                            hoverOffset: 6
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } } }
                });
            }

            const demografiaData = @json($estadosCiviles);
            if(Object.keys(demografiaData).length > 0) {
                new Chart(document.getElementById('demografiaChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: Object.keys(demografiaData).map(l => l.toUpperCase()),
                        datasets: [{
                            data: Object.values(demografiaData),
                            backgroundColor: ['#0d6efd', '#0dcaf0', '#6f42c1', '#d63384', '#fd7e14'],
                            borderWidth: 2,
                            hoverOffset: 6
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } } }
                });
            }

            // 2. LÓGICA DE LA TARJETA PREVIEW (HOVER UX)
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
                    pConservacion.textContent = conservacion.toUpperCase();
                    
                    // Clases dinámicas de color según estado
                    pConservacion.className = 'badge fw-bold p-1 px-2 ';
                    if(conservacion.toLowerCase().includes('buen') || conservacion.toLowerCase().includes('óptim')) {
                        pConservacion.classList.add('bg-success-light', 'text-success', 'border', 'border-success');
                    } else if(conservacion.toLowerCase().includes('regular') || conservacion.toLowerCase().includes('escas')) {
                        pConservacion.classList.add('bg-warning-light', 'text-warning', 'border', 'border-warning');
                    } else {
                        pConservacion.classList.add('bg-danger-light', 'text-danger', 'border', 'border-danger');
                    }

                    previewCard.style.display = 'block';
                    previewCard.style.opacity = '1';
                });

                row.addEventListener('mousemove', function (e) {
                    // Posicionamiento dinámico al lado del cursor para un look nativo
                    const cardWidth = previewCard.offsetWidth;
                    const cardHeight = previewCard.offsetHeight;
                    
                    // Lógica para no desbordar pantalla
                    let leftPos = e.clientX + 20;
                    if (leftPos + cardWidth > window.innerWidth) leftPos = e.clientX - cardWidth - 20;

                    let topPos = e.clientY + window.scrollY - (cardHeight / 2);
                    if (e.clientY + cardHeight > window.innerHeight) topPos = e.clientY + window.scrollY - cardHeight + 20;

                    previewCard.style.left = leftPos + 'px';
                    previewCard.style.top = topPos + 'px';
                });

                row.addEventListener('mouseleave', function () {
                    previewCard.style.opacity = '0';
                    setTimeout(() => { if (previewCard.style.opacity === '0') previewCard.style.display = 'none'; }, 150);
                });
            });
        });
    </script>
</x-base-layout>