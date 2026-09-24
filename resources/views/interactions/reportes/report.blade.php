<x-base-layout>
    {{-- Alertas mejoradas con diseño --}}
    <x-success />
    <x-error />

    {{-- Encabezado de la página --}}
    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="feather-pie-chart me-2"></i>Informe de Interacciones</h4>
            <p class="text-muted mb-0 small">Dashboard analítico y métricas de rendimiento (Daytrack)</p>
        </div>
            <div>
            
            <a href="{{ route('interactions.report.pdf', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm me-2 shadow-sm" style="border-radius: 8px;">
                <i class="feather-printer me-1"></i>Imprimir
            </a>
        </div>
    </div> --}}
    {{-- Imprimir siempre está disponible — reportPdf() aplica el mismo alcance que la pantalla
         (individual/área/todos, ver alcanceInformes), así que el PDF nunca trae más de lo que ya
         se está viendo aquí. Antes esto se ocultaba sin informes.area/.todosagentes, dejando a
         quien solo ve su propio informe sin poder imprimirlo. --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        <a href="{{ route('interactions.report.pdf', request()->all()) }}" target="_blank"
            class="btn btn-outline-secondary me-2 w-25 p-2"><i class="feather-printer me-1"></i>Imprimir</a>
    </div>

    {{-- El Informe abierto desde el menú es SIEMPRE personal — no tiene selector propio para
         verlo por área o de todos (eso vive en Auditoría, ver el botón "Ver Informe" allá). Si
         se llegó aquí desde Auditoría con un alcance más amplio ya elegido (modo=area/todos en
         la URL), se respeta y se avisa aquí, con un link de vuelta para ajustarlo. --}}
    @if (($modo ?? 'propias') !== 'propias')
        <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 py-2">
            <span class="fs-13">
                <i class="feather-info me-1"></i>
                Viendo el informe de <strong>{{ $modo === 'todos' ? 'Todos' : 'Mi Área' }}</strong> — heredado desde Auditoría.
            </span>
            <a href="{{ route('interactions.auditoria') }}" class="btn btn-sm btn-outline-secondary">
                Ajustar en Auditoría
            </a>
        </div>
    @endif

    {{-- Filtros Avanzados (MANUALES) --}}
    <div class="card mb-2">
        <div class="card-header py-2 px-3">
            <div>
                <h5 class="card-title">
                    <i class="feather-filter me-2 text-primary"></i>Filtros de Dashboard
                    </h6>
                    <small class="text-muted">Analiza las interacciones usando múltiples criterios</small>
            </div>
            <button type="button" class="btn btn-sm btn-light shadow-sm" id="clearFilters" style="border-radius: 6px;">
                <i class="feather-refresh-cw me-1"></i>Limpiar Filtros
            </button>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('interactions.report') }}" method="GET" class="row g-3 ">
                {{-- El alcance (modo/área/agente) ya no se elige aquí — se hereda tal cual de la
                     URL con la que se llegó (desde el menú siempre "propias"; desde Auditoría, lo
                     que allá se tenía seleccionado). Se conserva como campos ocultos para que los
                     demás filtros de este formulario no lo pierdan al enviarse. --}}
                <input type="hidden" name="modo" value="{{ $modo ?? 'propias' }}">
                <input type="hidden" name="agent_id" value="{{ request('agent_id') }}">
                <input type="hidden" name="area_id" value="{{ request('area_id') }}">

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Cliente</label>
                    <select name="client_id" class="form-select form-select-sm select2-search" data-placeholder="Todos">
                        <option value=""></option>
                        @foreach ($listClientes as $cliente)
                            <option value="{{ $cliente->cod_ter }}"
                                {{ ($filtroCliente ?? '') == $cliente->cod_ter ? 'selected' : '' }}>
                                {{ $cliente->nom_ter ?? 'Cliente ' . $cliente->cod_ter }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro: Mes — atajo para no tener que elegir Fecha Inicio/Fin a mano; al
                     elegir un mes completa esas dos fechas con su primer y último día (JS abajo).
                     Se puede seguir ajustando Fecha Inicio/Fin manualmente después si hace falta
                     un rango que no sea un mes completo. --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted mb-1">Mes</label>
                    <input type="month" name="mes" id="filtroMes" class="form-control"
                        value="{{ request('mes') }}" />
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted mb-1">Fecha Inicio</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="feather-calendar text-primary"></i></span>
                        <input type="date" name="start_date" id="filtroFechaInicio" class="form-control border-start-0 ps-0"
                            value="{{ $startDate ?? request('start_date') }}" />
                    </div>
                </div>

                {{-- Filtro: Fecha Fin --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted mb-1">Fecha Fin</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="feather-calendar text-primary"></i></span>
                        <input type="date" name="end_date" id="filtroFechaFin" class="form-control border-start-0 ps-0"
                            value="{{ $endDate ?? request('end_date') }}" />
                    </div>
                </div>

                {{-- Filtro: Distrito --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Distrito</label>
                    <select name="distrito_id" class="form-select form-select-sm select2-search"
                        data-placeholder="Todos">
                        <option value=""></option>
                        @foreach ($listDistritos as $distrito)
                            <option value="{{ $distrito->COD_DIST }}"
                                {{ ($filtroDistrito ?? '') == $distrito->COD_DIST ? 'selected' : '' }}>
                                {{ $distrito->NOM_DIST ?? 'Distrito ' . $distrito->COD_DIST }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro: Línea --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Línea</label>
                    <select name="linea_id" class="form-select form-select-sm select2-search" data-placeholder="Todas">
                        <option value=""></option>
                        @foreach ($listLineas as $linea)
                            <option value="{{ $linea->id }}"
                                {{ ($filtroLinea ?? '') == $linea->id ? 'selected' : '' }}>
                                {{ $linea->name ?? 'Línea ' . $linea->id }}
                            </option>
                        @endforeach
                    </select>
                </div>



                {{-- Botón Buscar Visible y Atractivo --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm"
                        style="border-radius: 8px; font-weight: 500;">
                        <i class="feather-search me-1"></i>Filtrar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tarjetas de Métricas Principales (KPIs) --}}
    <div class="row mb-2 g-3">
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 stat-card"
                style="border-radius: 12px; border-left: 4px solid #4a90e2 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle mb-3 shadow-sm"
                        style="width: 50px; height: 50px;">
                        <i class="feather-inbox fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Total Interacciones</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 stat-card"
                style="border-radius: 12px; border-left: 4px solid #2ecc71 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-success text-success rounded-circle mb-3 shadow-sm"
                        style="width: 50px; height: 50px;">
                        <i class="feather-check-circle fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Gestiones Exitosas</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['successful'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <a class="col-md-3"  href="#lineasCreditoChart">
            <div class="card shadow-sm h-100 border-0 stat-card"
                style="border-radius: 12px; border-left: 4px solid #f39c12 !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-warning text-warning rounded-circle mb-3 shadow-sm"
                        style="width: 50px; height: 50px;">
                        <i class="feather-clock fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Pendientes</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="#lineasCreditoChart">
            <div class="card shadow-sm h-100 border-0 stat-card"
                style="border-radius: 12px; border-left: 4px solid #e74c3c !important;">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-danger text-danger rounded-circle mb-3 shadow-sm"
                        style="width: 50px; height: 50px;">
                        <i class="feather-alert-triangle fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Acciones Vencidas</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['overdue'] ?? 0 }}</h3>
                </div>
            </div>
        </a>
    </div>

    {{-- Interacciones por Día — tendencia dentro del rango elegido. A diferencia de los
         comparativos entre agentes, esta sí aplica igual en el informe individual que en el de
         área/todos: en individual muestra el propio ritmo de trabajo, en área/todos el volumen
         del equipo. Fila completa (no a la mitad como las demás) porque una serie de tiempo
         necesita más ancho para leerse bien. --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold"><i class="feather-trending-up me-2 text-primary"></i>Interacciones por Día</h6>
                    @php $diasSinRegistro = $chartInteraccionesPorDia['dias_habiles_sin_registro'] ?? 0; @endphp
                    <span class="badge {{ $diasSinRegistro > 0 ? 'bg-soft-danger text-danger' : 'bg-soft-success text-success' }} fs-13 fw-semibold px-3 py-2">
                        <i class="feather-calendar me-1"></i>
                        {{ $diasSinRegistro }} día(s) hábil(es) sin registrar interacciones
                    </span>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="chartInteraccionesPorDia"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Indicadores por Área — solo aporta cuando el alcance cubre más de una (Ver: Mi área con
         listado.todos eligiendo "Todas", o Ver: Todos); con una sola área en alcance sale una
         sola barra, sigue siendo correcto. --}}
    @if (count($chartAreas['labels'] ?? []) > 0)
        <div class="row g-3 mb-0">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 12px;">
                    <div class="card-header py-3 border-bottom-0 bg-transparent">
                        <h6 class="mb-0 fw-bold"><i class="feather-grid me-2 text-indigo" style="color:#6610f2;"></i>Indicadores por Área</h6>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 220px; width: 100%;">
                            <canvas id="chartAreas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Primera Fila de Gráficos: Canales y Resultados --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-bar-chart-2 me-2 text-primary"></i>Interacciones por
                        Canal</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartCanales"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-pie-chart me-2 text-success"></i>Distribución de
                        Resultados</h6>
                </div>
                <div class="card-body">
                    <div
                        style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="chartResultados"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Segunda Fila de Gráficos: Agentes y Clientes --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-users me-2 text-info"></i>Top 5 Agentes (Interacciones)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartAgentes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-briefcase me-2 text-warning"></i>Top 5 Clientes</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartClientes"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tercera Fila de Gráficos: Distritos y Líneas de Crédito --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-map-pin me-2 text-danger"></i>Top 5 Distritos</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartDistritos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6" id="lineasCreditoChart">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-credit-card me-2 text-secondary"></i>Interacciones por
                        Línea Crédito</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartLineas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cuarta Fila de Gráficos: Seguimientos Agentes (NUEVA) --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-user-check me-2" style="color: #14b8a6;"></i>Top 5
                        Agentes (Seguimientos)</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartSeguimientosAgentes"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-bottom-0 bg-transparent">
                    <h6 class="mb-0 fw-bold"><i class="feather-user-check bi bi-bookmark-x me-2 text-warning"></i>Vencidas
                        y Pendientes</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chartVencidasPendientes"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar filtros
            document.getElementById('clearFilters').addEventListener('click', function() {
                window.location.href = "{{ route('interactions.report') }}";
            });

            // Filtro Mes: completa Fecha Inicio/Fin con el primer y último día del mes elegido
            // (no se manda "mes" al servidor para nada más, solo llena esos dos campos).
            const filtroMes = document.getElementById('filtroMes');
            if (filtroMes) {
                filtroMes.addEventListener('change', function () {
                    if (!this.value) return;
                    const [anio, mes] = this.value.split('-').map(Number);
                    const primerDia = new Date(anio, mes - 1, 1);
                    const ultimoDia = new Date(anio, mes, 0);
                    const aYMD = (d) => d.toISOString().slice(0, 10);
                    document.getElementById('filtroFechaInicio').value = aYMD(primerDia);
                    document.getElementById('filtroFechaFin').value = aYMD(ultimoDia);
                });
            }

            // --- CONFIGURACIÓN ESTÉTICA DE CHART.JS ---
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = '#718096';

            // Tooltips modernos estilo SaaS
            const elegantTooltip = {
                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                titleColor: '#2d3748',
                bodyColor: '#4a5568',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 12,
                boxPadding: 8,
                usePointStyle: true,
                cornerRadius: 8,
                titleFont: {
                    size: 13,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                }
            };

            // Función para crear degradados
            const createGradient = (ctx, colorStart, colorEnd) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            };

            // Escalas invisibles para un look limpio
            const cleanScales = {
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: '#f7fafc',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            };
            const cleanScalesHorizontal = {
                x: {
                    grid: {
                        color: '#f7fafc',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    }
                }
            };

            // Datos
            const canalesLabels = @json($chartCanales['labels'] ?? []);
            const canalesData = @json($chartCanales['data'] ?? []);
            const resultadosLabels = @json($chartResultados['labels'] ?? []);
            const resultadosData = @json($chartResultados['data'] ?? []);
            const agentesLabels = @json($chartAgentes['labels'] ?? []);
            const agentesData = @json($chartAgentes['data'] ?? []);
            const clientesLabels = @json($chartClientes['labels'] ?? []);
            const clientesData = @json($chartClientes['data'] ?? []);
            const lineasLabels = @json($chartLineas['labels'] ?? []);
            const lineasData = @json($chartLineas['data'] ?? []);
            const distritosLabels = @json($chartDistritos['labels'] ?? []);
            const distritosData = @json($chartDistritos['data'] ?? []);
            const seguimientosAgentesLabels = @json($chartSeguimientosAgentes['labels'] ?? []);
            const seguimientosAgentesData = @json($chartSeguimientosAgentes['data'] ?? []);
            const vencidasPendientesLabels = @json($chartAccionesAgentes['labels'] ?? []);
            const pendientesData = @json($chartAccionesAgentes['pendientes'] ?? []);
            const vencidasData = @json($chartAccionesAgentes['vencidas'] ?? []);
            const areasLabels = @json($chartAreas['labels'] ?? []);
            const areasData = @json($chartAreas['data'] ?? []);
            const porDiaLabels = @json($chartInteraccionesPorDia['labels'] ?? []);
            const porDiaData = @json($chartInteraccionesPorDia['data'] ?? []);

            // 0. Interacciones por Día (línea de tendencia) — aplica igual en individual que en
            // área/todos, por eso va primero, antes que los demás gráficos.
            const ctxPorDia = document.getElementById('chartInteraccionesPorDia').getContext('2d');
            new Chart(ctxPorDia, {
                type: 'line',
                data: {
                    labels: porDiaLabels,
                    datasets: [{
                        label: 'Interacciones',
                        data: porDiaData,
                        borderColor: 'rgba(52, 152, 219, 1)',
                        backgroundColor: createGradient(ctxPorDia, 'rgba(52, 152, 219, 0.35)', 'rgba(52, 152, 219, 0.02)'),
                        fill: true,
                        tension: 0.3,
                        pointRadius: porDiaLabels.length > 45 ? 0 : 3,
                        pointBackgroundColor: 'rgba(52, 152, 219, 1)',
                        pointHoverRadius: 5,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: elegantTooltip
                    },
                    scales: cleanScales
                }
            });

            // 1. Canales (Barras Verticales)
            const ctxCanales = document.getElementById('chartCanales').getContext('2d');
            new Chart(ctxCanales, {
                type: 'bar',
                data: {
                    labels: canalesLabels,
                    datasets: [{
                        label: 'Interacciones',
                        data: canalesData,
                        backgroundColor: createGradient(ctxCanales, 'rgba(74, 144, 226, 0.8)',
                            'rgba(74, 144, 226, 0.2)'),
                        hoverBackgroundColor: 'rgba(74, 144, 226, 1)',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: elegantTooltip
                    },
                    scales: cleanScales
                }
            });

            // 2. Resultados (Dona)
            const ctxResultados = document.getElementById('chartResultados').getContext('2d');
            new Chart(ctxResultados, {
                type: 'doughnut',
                data: {
                    labels: resultadosLabels,
                    datasets: [{
                        data: resultadosData,
                        backgroundColor: ['#4a90e2', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
                            '#95a5a6'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 20
                            }
                        },
                        tooltip: {
                            ...elegantTooltip,
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed;
                                    let total = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = total > 0 ? Math.round((value / total) * 100) +
                                        '%' : '0%';
                                    return ` ${context.label}: ${value} (${percentage})`;
                                }
                            }
                        }
                    }
                }
            });

            // Helper para Barras Horizontales
            const createHBar = (id, labels, data, colorStart, colorEnd) => {
                const ctx = document.getElementById(id).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: createGradient(ctx, colorStart, colorEnd),
                            hoverBackgroundColor: colorStart.replace('0.8)', '1)'),
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: elegantTooltip
                        },
                        scales: cleanScalesHorizontal
                    }
                });
            };

            // 3, 4, 5, 6, 7. Gráficos Horizontales
            createHBar('chartAgentes', agentesLabels, agentesData, 'rgba(155, 89, 182, 0.8)',
                'rgba(155, 89, 182, 0.2)');
            createHBar('chartClientes', clientesLabels, clientesData, 'rgba(52, 152, 219, 0.8)',
                'rgba(52, 152, 219, 0.2)');
            createHBar('chartDistritos', distritosLabels, distritosData, 'rgba(46, 204, 113, 0.8)',
                'rgba(46, 204, 113, 0.2)');
            createHBar('chartSeguimientosAgentes', seguimientosAgentesLabels, seguimientosAgentesData,
                'rgba(20, 184, 166, 0.8)', 'rgba(20, 184, 166, 0.2)');
            // Por Área — solo se crea si la tarjeta está en el DOM (la vista la oculta cuando el
            // alcance cubre una sola área/agente).
            if (document.getElementById('chartAreas')) {
                createHBar('chartAreas', areasLabels, areasData, 'rgba(102, 16, 242, 0.8)', 'rgba(102, 16, 242, 0.2)');
            }

            // Líneas de Crédito (Barras Verticales)
            const ctxLineas = document.getElementById('chartLineas').getContext('2d');
            new Chart(ctxLineas, {
                type: 'bar',
                data: {
                    labels: lineasLabels,
                    datasets: [{
                        data: lineasData,
                        backgroundColor: createGradient(ctxLineas, 'rgba(243, 156, 18, 0.8)',
                            'rgba(243, 156, 18, 0.2)'),
                        hoverBackgroundColor: 'rgba(243, 156, 18, 1)',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: elegantTooltip
                    },
                    scales: cleanScales
                }
            });

            // 8. Pendientes vs Vencidas por Agente
            const ctxVencidasPendientes = document.getElementById('chartVencidasPendientes').getContext('2d');

            new Chart(ctxVencidasPendientes, {
                type: 'bar',
                data: {
                    labels: vencidasPendientesLabels,
                    datasets: [{
                            label: 'Pendientes',
                            data: pendientesData,
                            backgroundColor: 'rgba(243,156,18,0.8)',
                            borderRadius: 6
                        },
                        {
                            label: 'Vencidas',
                            data: vencidasData,
                            backgroundColor: 'rgba(231,76,60,0.8)',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: elegantTooltip
                    },
                    scales: cleanScales
                }
            });
        });
    </script>

</x-base-layout>
