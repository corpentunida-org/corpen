<x-base-layout>
    {{-- Título Principal del Dashboard --}}
    <h2 class="mb-4 fw-light text-primary border-bottom pb-2">
        <i class="feather-bar-chart-2 me-2"></i>Dashboard de Soportes
    </h2>

    {{-- Alertas mejoradas con diseño y un ícono más llamativo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center glassmorphism-alert" role="alert">
            <i class="feather-check-circle me-2 fs-5"></i>
            <strong class="me-2">¡Éxito!</strong>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🌟 Filtros de Fechas Mejorados (Date Range Picker Style) 🌟 --}}
    <div class="card shadow-sm mb-4 glassmorphism-card fade-in-up">
        <div class="card-header border-bottom p-3 bg-light bg-opacity-50">
            <h6 class="mb-0 fw-semibold">
                <i class="feather-calendar me-2 text-primary"></i>Selección de Rango
                <small class="text-muted fw-normal ms-3 d-none d-md-inline">Estadísticas filtradas por fecha</small>
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-sm-5 col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Inicio</label>
                    <input type="date" id="filterStartDate" class="form-control form-control-sm pastel-input" value="{{ request('start_date') ?? now()->subMonths(6)->format('Y-m-d') }}">
                </div>
                <div class="col-sm-5 col-md-4">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Fin</label>
                    <input type="date" id="filterEndDate" class="form-control form-control-sm pastel-input" value="{{ request('end_date') ?? now()->format('Y-m-d') }}">
                </div>
                <div class="col-sm-2 col-md-2">
                    <button type="button" class="btn btn-sm btn-primary w-100 pastel-btn-gradient" id="applyDateFilters">
                        <i class="feather-search"></i> <span class="d-none d-lg-inline">Aplicar</span>
                    </button>
                </div>
                <div class="col-sm-12 col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 pastel-btn-light" id="clearDateFilters">
                        <i class="feather-refresh-cw"></i> <span class="d-none d-lg-inline">Restablecer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de estadísticas (KPIs) Mejoradas (4 Columnas) --}}
    <div class="row mb-4">
        
        {{-- Total de Soportes --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glassmorphism-card kpi-card border-start border-primary border-4 fade-in-up" style="animation-delay: 0.1s;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-semibold">Total Soportes</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ $totalTickets ?? 0 }}</h2>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle flex-shrink-0 align-self-center">
                            <i class="feather-life-buoy text-primary fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Todos los soportes en el rango.</small>
                </div>
            </div>
        </div>
        
        {{-- Soportes Abiertos --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glassmorphism-card kpi-card border-start border-warning border-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-semibold">Abiertos</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ $openTickets ?? 0 }}</h2>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle flex-shrink-0 align-self-center">
                            <i class="feather-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    {{-- Mini-gráfico/Indicador de progreso simulado --}}
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ (($openTickets ?? 0) / (($totalTickets ?? 1) > 0 ? $totalTickets : 1)) * 100 }}%" aria-valuenow="{{ $openTickets ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $totalTickets ?? 0 }}"></div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Soportes Cerrados --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glassmorphism-card kpi-card border-start border-success border-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-semibold">Cerrados</p>
                            <h2 class="mb-0 fw-bold text-success">{{ $closedTickets ?? 0 }}</h2>
                        </div>
                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle flex-shrink-0 align-self-center">
                            <i class="feather-check-square text-success fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">¡Objetivo de eficiencia alcanzado!</small>
                </div>
            </div>
        </div>

        {{-- Tiempo Promedio de Resolución --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card glassmorphism-card kpi-card border-start border-info border-4 fade-in-up" style="animation-delay: 0.4s;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase mb-1 small fw-semibold">T. Prom. Resolución</p>
                            <h2 class="mb-0 fw-bold text-info">{{ $avgResolutionTime ?? 'N/A' }}</h2>
                        </div>
                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle flex-shrink-0 align-self-center">
                            <i class="feather-activity text-info fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Tiempo promedio en el período.</small>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Gráficos con Altura Fija para Control de UX (Sección de Cuadrícula 2x2) --}}
    <div class="row">
        
        {{-- Gráfico 1: Soportes Creados por Mes (Línea) --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-primary"><i class="feather-trending-up me-2"></i>Soportes Creados por Mes</h5>
                    <div class="chart-canvas-wrapper" style="height: 300px;">
                           <canvas id="ticketsPorMesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico 2: Distribución por Estado (Doughnut) --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.6s;">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-primary"><i class="feather-pie-chart me-2"></i>Distribución por Estado</h5>
                    <div class="chart-canvas-wrapper" style="height: 300px;">
                        <canvas id="ticketsPorEstadoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico 3: Soportes por Categoría (Placeholder Original) --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.7s;">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-primary"><i class="feather-list me-2"></i>Soportes por Categoría</h5>
                    <div class="chart-canvas-wrapper" style="height: 300px; display: flex; align-items: center; justify-content: center; color: #adb5bd;">
                         <p class="mb-0 fw-bold text-secondary">
                            <i class="feather-server me-1"></i> Aquí iría el gráfico de Categorías.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- NUEVO Gráfico 4: Rendimiento por Agente (Barras Horizontales) --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.8s;">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-primary"><i class="feather-users me-2"></i>Top Agentes por Soportes Cerrados</h5>
                    <div class="chart-canvas-wrapper" style="height: 300px;">
                        <canvas id="rendimientoAgenteChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* COLORES PASTELES SUAVES Y HERMOSOS */
            body {
                /* Fondo un poco más vibrante para el glassmorphism */
                background: linear-gradient(135deg, #f0f4f8 0%, #dce4ee 100%);
                min-height: 100vh;
                font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                transition: all 0.3s ease;
                color: #2c3e50; /* Color principal más oscuro para contraste */
            }
            
            /* Glassmorphism */
            .glassmorphism-card {
                background: rgba(255, 255, 255, 0.5); /* Más transparencia para más "vidrio" */
                backdrop-filter: blur(15px); /* Más blur */
                border-radius: 16px; /* Más redondeado */
                border: 1px solid rgba(255, 255, 255, 0.3); /* Borde más visible */
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15); /* Sombra más pronunciada */
            }
            
            .glassmorphism-alert {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(8px);
                border-radius: 10px;
                border: 1px solid rgba(255, 255, 255, 0.5);
                color: #155724; /* Color de texto específico para éxito */
            }
            
            /* Botones pasteles SUAVES (Añadido un ligero degradado) */
            .pastel-btn-gradient {
                background: linear-gradient(135deg, #a8dadc, #457b9d) !important; /* Degradado de colores pastel más frescos */
                color: white !important;
                border: none;
                border-radius: 25px; /* Más redondo */
                padding: 8px 20px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(69, 123, 157, 0.3);
            }
            
            .pastel-btn-gradient:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(69, 123, 157, 0.4);
            }

            .pastel-btn-light {
                background: rgba(255, 255, 255, 0.7) !important;
                color: #495057 !important;
                border: 1px solid #ced4da;
                border-radius: 18px;
                padding: 6px 14px;
                transition: all 0.3s ease;
            }

            /* Selects e inputs pasteles SUAVES */
            .pastel-input {
                background: rgba(255, 255, 255, 0.9) !important; /* Más opaco para mejor legibilidad */
                border: 1px solid #ced4da;
                border-radius: 10px;
                transition: all 0.3s ease;
                color: #2c3e50 !important;
                font-size: 0.9rem;
            }
            
            .pastel-input:focus {
                background: #ffffff !important;
                border-color: #a8dadc;
                box-shadow: 0 0 0 0.2rem rgba(168, 218, 220, 0.5);
            }

            /* Contenedor de Gráfico con Altura Fija */
            .chart-canvas-wrapper {
                position: relative;
                width: 100%;
                /* Altura controlada, elimina el style="height:..." del canvas */
            }

            /* Mejoras visuales para las KPI Cards */
            .kpi-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                cursor: default;
            }
            .kpi-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            .avatar-sm {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Efectos de entrada */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .fade-in-up {
                animation: fadeInUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; /* Curva más dinámica */
                opacity: 0; /* Asegurar que inicie oculto */
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                
                // Configuración global para Chart.js
                Chart.defaults.font.family = 'Inter, sans-serif';
                Chart.defaults.color = '#495057';
                
                // Paleta de colores pastel más sofisticada
                const pastelColors = {
                    primary: 'rgba(69, 123, 157, 0.8)',  // Azul-Gris suave
                    secondary: 'rgba(168, 218, 220, 0.8)', // Azul Celeste
                    warning: 'rgba(255, 184, 108, 0.8)',   // Naranja suave
                    success: 'rgba(152, 216, 200, 0.8)',   // Verde Menta
                    info: 'rgba(29, 53, 87, 0.8)', // Azul Marino suave
                    danger: 'rgba(231, 76, 60, 0.8)' // Rojo para contrastar
                };

                // --- Gráfico 1: Soportes por Mes (Línea para tendencia, más dinámico) ---
                const ctx1 = document.getElementById('ticketsPorMesChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'line', 
                    data: {
                        labels: @json($labelsMes ?? []),
                        datasets: [{
                            label: 'Soportes Creados',
                            data: @json($dataMes ?? []),
                            backgroundColor: pastelColors.primary.replace('0.8', '0.2'), // Fondo suave
                            borderColor: pastelColors.primary.replace('0.8', '1'),
                            pointBackgroundColor: pastelColors.primary.replace('0.8', '1'),
                            pointBorderColor: '#fff',
                            borderWidth: 2,
                            tension: 0.4, // Curva suave
                            fill: true // Relleno de área
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // CLAVE para controlar la altura
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                                ticks: { stepSize: 1 }
                            },
                            x: { grid: { display: false } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                padding: 10, borderWidth: 1, backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#2c3e50', bodyColor: '#2c3e50',
                                borderColor: pastelColors.primary.replace('0.8', '0.5'),
                            }
                        }
                    }
                });

                // --- Gráfico 2: Soportes por Estado (Doughnut) ---
                const ctx2 = document.getElementById('ticketsPorEstadoChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: @json($labelsEstado ?? []),
                        datasets: [{
                            label: 'Cantidad',
                            data: @json($dataEstado ?? []),
                            backgroundColor: [
                                pastelColors.warning.replace('0.8', '0.9'), // Abiertos (Advertencia)
                                pastelColors.success.replace('0.8', '0.9'), // Cerrados (Éxito)
                                pastelColors.secondary.replace('0.8', '0.9'), // Otros
                                pastelColors.info.replace('0.8', '0.9'), // Otros
                                pastelColors.danger.replace('0.8', '0.9'), // Otros
                            ],
                            borderColor: '#fff',
                            borderWidth: 3, // Borde más grueso para separar
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // CLAVE para controlar la altura
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 20, usePointStyle: true }
                            },
                            tooltip: {
                                padding: 10, borderWidth: 1, backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#2c3e50', bodyColor: '#2c3e50',
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                            }
                        }
                    }
                });

                // --- NUEVO Gráfico 4: Rendimiento por Agente (Barras Horizontales) ---
                const ctx4 = document.getElementById('rendimientoAgenteChart').getContext('2d');
                // Se asume que el controlador pasa $labelsAgente y $dataAgente
                new Chart(ctx4, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsAgente ?? ['Agente A', 'Agente B', 'Agente C']),
                        datasets: [{
                            label: 'Tickets Cerrados',
                            data: @json($dataAgente ?? [25, 20, 15]),
                            backgroundColor: [
                                pastelColors.primary, 
                                pastelColors.success, 
                                pastelColors.info, 
                                pastelColors.warning, 
                                pastelColors.secondary
                            ], 
                            borderColor: '#fff',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Hace las barras horizontales
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                                ticks: { stepSize: 1 }
                            },
                            y: { grid: { display: false } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                padding: 10, borderWidth: 1, backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#2c3e50', bodyColor: '#2c3e50',
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                            }
                        }
                    }
                });

                // --- Lógica para filtros de fecha ---
                function applyDateFilters() {
                    const startDate = document.getElementById('filterStartDate').value;
                    const endDate = document.getElementById('filterEndDate').value;
                    const params = new URLSearchParams(window.location.search);
                    
                    if (startDate) params.set('start_date', startDate);
                    else params.delete('start_date');

                    if (endDate) params.set('end_date', endDate);
                    else params.delete('end_date');

                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                }

                document.getElementById('applyDateFilters').addEventListener('click', applyDateFilters);
                document.getElementById('clearDateFilters').addEventListener('click', () => {
                    window.location.href = window.location.pathname; // Recargar sin parámetros
                });

                // Inicializar efectos de entrada después de que el DOM esté listo
                document.querySelectorAll('.fade-in-up').forEach(function(element) { 
                    element.style.opacity = '1'; // El CSS lo maneja, solo para asegurar que se muestre después de la animación
                });
            });
        </script>
    @endpush
</x-base-layout>