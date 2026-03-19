<x-base-layout>
    {{-- Alertas mejoradas con diseño --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center glassmorphism-alert"
            role="alert">
            <i class="feather-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center glassmorphism-alert"
            role="alert">
            <i class="feather-alert-circle me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Encabezado de la página --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="feather-pie-chart me-2"></i>Informe de Interacciones</h4>
            <p class="text-muted mb-0 small">Dashboard analítico y métricas de rendimiento (Daytrack)</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
                <i class="feather-printer me-1"></i>Imprimir
            </button>
        </div>
    </div>

    {{-- Filtros: Rango de Fechas para el Informe --}}
    <div class="card shadow-sm mb-4 glassmorphism-card">
        <div class="d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
            <div>
                <h6 class="mb-0 fw-semibold">
                    <i class="feather-calendar me-2"></i>Filtro de Período
                </h6>
                <small class="text-muted">Analiza las interacciones en un rango de tiempo específico</small>
            </div>
            <button type="button" class="btn btn-sm btn-light" id="clearFilters">
                <i class="feather-refresh-cw me-1"></i>Mes Actual
            </button>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('interactions.report') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Inicio</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" name="start_date" id="filterFechaInicio" class="form-control pastel-input"
                            value="{{ request('start_date') }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Fin</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-calendar"></i></span>
                        <input type="date" name="end_date" id="filterFechaFin" class="form-control pastel-input"
                            value="{{ request('end_date') }}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-primary w-100 pastel-btn-gradient">
                        <i class="feather-filter me-1"></i>Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tarjetas de Métricas Principales (KPIs) --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card shadow-sm h-100 glassmorphism-card pastel-card border-0" style="border-left: 4px solid #4a90e2 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle mb-3" style="width: 50px; height: 50px;">
                        <i class="feather-inbox fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Total Interacciones</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 glassmorphism-card pastel-card border-0" style="border-left: 4px solid #2ecc71 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-success text-success rounded-circle mb-3" style="width: 50px; height: 50px;">
                        <i class="feather-check-circle fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Gestiones Exitosas</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['successful'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 glassmorphism-card pastel-card border-0" style="border-left: 4px solid #f39c12 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-warning text-warning rounded-circle mb-3" style="width: 50px; height: 50px;">
                        <i class="feather-clock fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Interacciones Pendientes</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 glassmorphism-card pastel-card border-0" style="border-left: 4px solid #e74c3c !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-danger text-danger rounded-circle mb-3" style="width: 50px; height: 50px;">
                        <i class="feather-alert-triangle fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Acciones Vencidas</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['overdue'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row mb-4 g-3">
        {{-- Gráfico de Canales --}}
        <div class="col-md-6">
            <div class="card shadow-sm glassmorphism-card"> {{-- Quité el h-100 --}}
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-bar-chart-2 me-2"></i>Interacciones por Canal</h6>
                </div>
                <div class="card-body">
                    {{-- WRAPPER MÁGICO: Controla la altura exacta y evita que se estire --}}
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartCanales"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Resultados --}}
        <div class="col-md-6">
            <div class="card shadow-sm glassmorphism-card"> {{-- Quité el h-100 --}}
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-pie-chart me-2"></i>Distribución de Resultados</h6>
                </div>
                <div class="card-body">
                    {{-- WRAPPER MÁGICO: Controla la altura exacta y evita que se estire --}}
                    <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="chartResultados"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Cargamos Chart.js para los gráficos --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Configuración Global para Chart.js para combinar con colores pastel
                Chart.defaults.color = '#6c757d';
                Chart.defaults.font.family = "'Inter', sans-serif";

                // 1. Gráfico de Barras (Canales)
                const ctxCanales = document.getElementById('chartCanales').getContext('2d');
                new Chart(ctxCanales, {
                    type: 'bar',
                    data: {
                        labels: ['Llamada', 'WhatsApp', 'Email', 'Presencial', 'Redes Sociales'], // Esto se reemplazará luego con datos dinámicos
                        datasets: [{
                            label: 'Cantidad de Interacciones',
                            data: [12, 19, 3, 5, 2], // Datos de prueba
                            backgroundColor: 'rgba(74, 144, 226, 0.6)',
                            borderColor: 'rgba(74, 144, 226, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                // 2. Gráfico de Dona (Resultados)
                const ctxResultados = document.getElementById('chartResultados').getContext('2d');
                new Chart(ctxResultados, {
                    type: 'doughnut',
                    data: {
                        labels: ['Exitoso', 'Pendiente', 'No Contactado', 'Rechazado'], // Datos de prueba
                        datasets: [{
                            data: [50, 25, 15, 10], // Datos de prueba
                            backgroundColor: [
                                'rgba(46, 204, 113, 0.7)',  // Verde pastel
                                'rgba(243, 156, 18, 0.7)',  // Naranja pastel
                                'rgba(149, 165, 166, 0.7)', // Gris pastel
                                'rgba(231, 76, 60, 0.7)'    // Rojo pastel
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            });
        </script>
    @endpush

</x-base-layout>