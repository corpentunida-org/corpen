<x-base-layout>
    {{-- Título Principal del Dashboard --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-light text-primary mb-0">
            <i class="feather-bar-chart-2 me-2"></i>Dashboard de Soportes
        </h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportDashboard">
                <i class="feather-download"></i> <span class="d-none d-md-inline">Exportar PDF</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshDashboard">
                <i class="feather-refresh-cw"></i> <span class="d-none d-md-inline">Actualizar</span>
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="viewOptions" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="feather-settings"></i> <span class="d-none d-md-inline">Vista</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="viewOptions">
                    <li><a class="dropdown-item active" href="#" data-view="standard">Estándar</a></li>
                    <li><a class="dropdown-item" href="#" data-view="compact">Compacta</a></li>
                    <li><a class="dropdown-item" href="#" data-view="detailed">Detallada</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Alertas mejoradas con diseño y un ícono más llamativo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center glassmorphism-alert" role="alert">
            <i class="feather-check-circle me-2 fs-5"></i>
            <strong class="me-2">¡Éxito!</strong>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Buscador rápido de soportes con Autocompletado --}}
    <div class="card shadow-sm mb-4 glassmorphism-card fade-in-up sticky-top" style="top: 70px; z-index: 1020;">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-6 position-relative">
                    <div class="input-group">
                        <span class="input-group-text pastel-input"><i class="feather-search"></i></span>
                        <input type="text" class="form-control pastel-input" id="quickSearch" placeholder="Buscar soporte por ID, descripción o cliente...">
                        <div id="searchResults" class="position-absolute w-100 bg-white rounded shadow-sm mt-1" style="z-index: 1050; display: none;"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select pastel-input" id="priorityFilter">
                        <option value="">Todas las prioridades</option>
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select pastel-input" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="1">Sin Asignar</option>
                        <option value="2">En Proceso</option>
                        <option value="3">En Revisión</option>
                        <option value="4">Cerrado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌟 Filtros de Fechas Mejorados (Date Range Picker Style) 🌟 --}}
    <div class="card shadow-sm mb-4 glassmorphism-card fade-in-up sticky-top" style="top: 150px; z-index: 1019;">
        <div class="card-header border-bottom p-3 bg-light bg-opacity-50">
            <h6 class="mb-0 fw-semibold">
                <i class="feather-calendar me-2 text-primary"></i>Selección de Rango
                <small class="text-muted fw-normal ms-3 d-none d-md-inline">Estadísticas filtradas por fecha</small>
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-sm-5 col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Inicio</label>
                    <input type="date" id="filterStartDate" class="form-control form-control-sm pastel-input" value="{{ request('start_date') ?? now()->subMonths(6)->format('Y-m-d') }}">
                </div>
                <div class="col-sm-5 col-md-3">
                    <label class="form-label fw-semibold text-muted small mb-1">Fecha Fin</label>
                    <input type="date" id="filterEndDate" class="form-control form-control-sm pastel-input" value="{{ request('end_date') ?? now()->format('Y-m-d') }}">
                </div>
                <div class="col-sm-2 col-md-2">
                    <button type="button" class="btn btn-sm btn-primary w-100 pastel-btn-gradient" id="applyDateFilters">
                        <i class="feather-search"></i> <span class="d-none d-lg-inline">Aplicar</span>
                    </button>
                </div>
                <div class="col-sm-2 col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 pastel-btn-light" id="clearDateFilters">
                        <i class="feather-refresh-cw"></i> <span class="d-none d-lg-inline">Restablecer</span>
                    </button>
                </div>
                <div class="col-sm-12 col-md-2">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary w-100 dropdown-toggle" type="button" id="presetDates" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="feather-clock"></i> <span class="d-none d-md-inline">Períodos</span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="presetDates">
                            <li><a class="dropdown-item preset-date" href="#" data-days="7">Últimos 7 días</a></li>
                            <li><a class="dropdown-item preset-date" href="#" data-days="30">Últimos 30 días</a></li>
                            <li><a class="dropdown-item preset-date" href="#" data-days="90">Últimos 3 meses</a></li>
                            <li><a class="dropdown-item preset-date" href="#" data-days="365">Último año</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inicio Tarjetas Dashboard: Diseño Zen / Glassmorphism Mejorado --}}
    <style>
        /* 1. Estilos Base y Glassmorphism - Extra Pequeño (XS) */
        .zen-card-xs {
            background-color: rgba(255, 255, 255, 0.08); 
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease; 
            overflow: hidden; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        /* Efecto Hover */
        .zen-card-xs:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), 0 0 15px -3px var(--hover-color, #8c8c8c); 
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* 2. Micro-interacción: Porcentaje en Círculo (Neumorphism) - Extra Pequeño */
        .circular-progress-xs {
            width: 36px;
            height: 36px;
            background: conic-gradient(
                var(--progress-color, #007bff) var(--progress-value, 0%),
                rgba(150, 150, 150, 0.2) var(--progress-value, 0%)
            );
            padding: 2px; 
        }

        .circular-progress-inner-xs {
            width: 32px;
            height: 32px;
            background-color: var(--card-bg, #ffffff);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); 
        }

        /* 4. Estilos de Texto */
        .zen-title-xs {
            font-weight: 600;
            opacity: 0.8;
            letter-spacing: 0.5px; 
            font-size: 0.65rem;
            white-space: nowrap; 
        }

        .zen-metric-xs {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
            margin-top: 0; 
            margin-bottom: 0.25rem;
        }
        
        /* Pequeños textos de tendencia */
        .zen-trend-xs {
            font-size: 0.65rem !important;
            white-space: nowrap;
        }
        
        /* Configuración de color de fondo */
        :root {
            --card-bg: #f8f9fa;
        }
    </style>

    <div class="row mb-0"> 
        
        {{-- Tarjeta 1: Volumen Total "TODOS"--}}
        <div class="col-lg col-md-6 mb-3"> 
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.1s; --hover-color: rgba(0, 123, 255, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Cantidad total de tickets de soporte registrados durante el periodo seleccionado.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-primary text-uppercase mb-1 zen-title-xs">
                                <i class="feather-life-buoy me-1"></i> Volumen Total
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $totalTickets ?? 0 }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-success me-1 zen-trend-xs">
                                    <i class="feather-trending-up"></i> +12%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-message-square text-primary opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 2: Tareas Pendientes (Tickets Abiertos) --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $total = ($totalTickets ?? 0) > 0 ? $totalTickets : 1;
                $openTicketsValue = $openTickets ?? 0;
                $openPercent = ($openTicketsValue / $total) * 100;
                $openProgressColor = ($openPercent > 20) ? '#dc3545' : (($openPercent > 5) ? '#ffc107' : '#007bff'); 
                $openTrendPercent = 5;
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.2s; --hover-color: {{ $openProgressColor }};"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Número de tickets que están actualmente Abiertos o en Progreso. Representa la carga de trabajo inmediata del equipo.">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-warning text-uppercase mb-1 zen-title-xs">
                                <i class="feather-clock me-1"></i> Sin asignación
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $openTicketsValue }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-danger me-1 zen-trend-xs">
                                    <i class="feather-trending-up"></i> +{{ $openTrendPercent }}%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        
                        <div class="circular-progress-xs rounded-circle d-flex align-items-center justify-content-center" 
                            style="--progress-color: {{ $openProgressColor }}; --progress-value: {{ $openPercent }}%;">
                            <div class="circular-progress-inner-xs rounded-circle d-flex align-items-center justify-content-center">
                                <span class="fw-bold" style="font-size: 0.55rem; color: {{ $openProgressColor }};">{{ round($openPercent) }}%</span> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 3: Tickets En Proceso --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $inProgressTicketsValue = $inProgressTickets ?? 0;
                $inProgressPercent = ($inProgressTicketsValue / $total) * 100;
                $inProgressProgressColor = ($inProgressPercent > 30) ? '#dc3545' : (($inProgressPercent > 15) ? '#ffc107' : '#007bff'); 
                $inProgressTrendPercent = -2;
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.25s; --hover-color: {{ $inProgressProgressColor }};"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Número de tickets que están actualmente en proceso de resolución.">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-info text-uppercase mb-1 zen-title-xs">
                                <i class="feather-loader me-1"></i> En Proceso
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $inProgressTicketsValue }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-success me-1 zen-trend-xs">
                                    <i class="feather-trending-down"></i> {{ abs($inProgressTrendPercent) }}%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        
                        <div class="circular-progress-xs rounded-circle d-flex align-items-center justify-content-center" 
                            style="--progress-color: {{ $inProgressProgressColor }}; --progress-value: {{ $inProgressPercent }}%;">
                            <div class="circular-progress-inner-xs rounded-circle d-flex align-items-center justify-content-center">
                                <span class="fw-bold" style="font-size: 0.55rem; color: {{ $inProgressProgressColor }};">{{ round($inProgressPercent) }}%</span> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 4: Tickets Revision --}}
        <div class="col-lg col-md-6 mb-3"> 
            @php
                $closedTicketsValue = $closedTickets ?? 0;
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.3s; --hover-color: rgba(40, 167, 69, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Cantidad de tickets que fueron marcados como Resueltos o Cerrados en el periodo, indicando la productividad.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-success text-uppercase mb-1 zen-title-xs">
                                <i class="feather-check-square me-1"></i> Revisión
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $closedTicketsValue }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-success me-1 zen-trend-xs">
                                    <i class="feather-trending-up"></i> +18%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-check-circle text-success opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 5: Tiempo Medio de Resolución (TMR) --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $avgTime = $avgResolutionTime ?? 'N/A';
                $avgResolutionTimeTrend = -8;
                $trendIcon = ($avgResolutionTimeTrend < 0) ? 'feather-trending-down' : 'feather-trending-up'; 
                $trendColor = ($avgResolutionTimeTrend < 0) ? 'text-success' : 'text-danger'; 
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.4s; --hover-color: rgba(23, 162, 184, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Tiempo promedio que toma cerrar un ticket, desde su creación hasta su resolución final. Una métrica más baja es mejor.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-info text-uppercase mb-1 zen-title-xs">
                                <i class="feather-activity me-1"></i> TMR (Cierre)
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $avgTime }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="{{ $trendColor }} me-1 zen-trend-xs">
                                    <i class="{{ $trendIcon }}"></i> {{ abs($avgResolutionTimeTrend) }}%
                                </small>
                                <small class="text-muted zen-trend-xs">mejora vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-zap text-info opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 6: Tiempo de Primera Respuesta --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $firstResponseTime = $avgFirstResponseTime ?? 'N/A';
                $firstResponseTimeTrend = -5;
                $trendIcon = ($firstResponseTimeTrend < 0) ? 'feather-trending-down' : 'feather-trending-up'; 
                $trendColor = ($firstResponseTimeTrend < 0) ? 'text-success' : 'text-danger'; 
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.45s; --hover-color: rgba(23, 162, 184, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Tiempo promedio que toma dar la primera respuesta a un ticket, desde su creación. Una métrica más baja es mejor.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-info text-uppercase mb-1 zen-title-xs">
                                <i class="feather-message-circle me-1"></i> T. Primera Respuesta
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $firstResponseTime }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="{{ $trendColor }} me-1 zen-trend-xs">
                                    <i class="{{ $trendIcon }}"></i> {{ abs($firstResponseTimeTrend) }}%
                                </small>
                                <small class="text-muted zen-trend-xs">mejora vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-message-circle text-info opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 7: Tickets Escalados --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $escalatedTicketsValue = $escalatedTickets ?? 0;
                $escalatedPercent = ($escalatedTicketsValue / $total) * 100;
                $escalatedProgressColor = ($escalatedPercent > 15) ? '#dc3545' : (($escalatedPercent > 5) ? '#ffc107' : '#007bff'); 
                $escalatedTrendPercent = 3;
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.5s; --hover-color: {{ $escalatedProgressColor }};"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Número de tickets que han sido escalados a un nivel superior de soporte.">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-warning text-uppercase mb-1 zen-title-xs">
                                <i class="feather-arrow-up-circle me-1"></i> Escalados
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $escalatedTicketsValue }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-danger me-1 zen-trend-xs">
                                    <i class="feather-trending-up"></i> +{{ $escalatedTrendPercent }}%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        
                        <div class="circular-progress-xs rounded-circle d-flex align-items-center justify-content-center" 
                            style="--progress-color: {{ $escalatedProgressColor }}; --progress-value: {{ $escalatedPercent }}%;">
                            <div class="circular-progress-inner-xs rounded-circle d-flex align-items-center justify-content-center">
                                <span class="fw-bold" style="font-size: 0.55rem; color: {{ $escalatedProgressColor }};">{{ round($escalatedPercent) }}%</span> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 8: Tasa de Escalado --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $escalationRateValue = $escalationRate ?? 0;
                $escalationRateTrend = -2;
                $trendIcon = ($escalationRateTrend < 0) ? 'feather-trending-down' : 'feather-trending-up'; 
                $trendColor = ($escalationRateTrend < 0) ? 'text-success' : 'text-danger'; 
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.55s; --hover-color: rgba(23, 162, 184, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Porcentaje de tickets que han sido escalados respecto al total. Una métrica más baja es mejor.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-info text-uppercase mb-1 zen-title-xs">
                                <i class="feather-percent me-1"></i> Tasa de Escalado
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $escalationRateValue }}%</h2>
                            <div class="d-flex align-items-center">
                                <small class="{{ $trendColor }} me-1 zen-trend-xs">
                                    <i class="{{ $trendIcon }}"></i> {{ abs($escalationRateTrend) }}%
                                </small>
                                <small class="text-muted zen-trend-xs">mejora vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-trending-down text-info opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 9: Tickets Resueltos sin Escalado --}}
        <div class="col-lg col-md-6 mb-3">
            @php
                $resolvedWithoutEscalationValue = $resolvedWithoutEscalation ?? 0;
                $resolvedWithoutEscalationPercent = ($resolvedWithoutEscalationValue / $total) * 100;
                $resolvedWithoutEscalationTrend = 8;
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up" 
                style="animation-delay: 0.6s; --hover-color: rgba(40, 167, 69, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Número de tickets que fueron resueltos sin necesidad de escalar. Una métrica más alta es mejor.">
                <div class="card-body p-3"> 
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-success text-uppercase mb-1 zen-title-xs">
                                <i class="feather-check-circle me-1"></i> Resueltos sin Escalado
                            </p>
                            <h2 class="text-dark zen-metric-xs">{{ $resolvedWithoutEscalationValue }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-success me-1 zen-trend-xs">
                                    <i class="feather-trending-up"></i> +{{ $resolvedWithoutEscalationTrend }}%
                                </small>
                                <small class="text-muted zen-trend-xs">vs. mes anterior</small>
                            </div>
                        </div>
                        <i class="feather-check-circle text-success opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tarjeta 10: Cumplimiento SLA (Acuerdo de Nivel de Servicio) --}}
        <div class="col-lg col-md-6 mb-3"> 
            @php
                $slaCompliance = 98.5;
                $slaColor = ($slaCompliance < 95) ? '#dc3545' : '#28a745'; 
                $borderColorClass = $slaCompliance < 95 ? 'border-danger' : 'border-success';
            @endphp
            <div class="zen-card-xs shadow-sm fade-in-up border-start border-3 {{ $borderColorClass }}" 
                style="animation-delay: 0.65s; --hover-color: rgba(40, 167, 69, 0.7);"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Porcentaje de tickets resueltos dentro del tiempo límite establecido por el Acuerdo de Nivel de Servicio (SLA). Mide la calidad del servicio.">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-{{ $slaColor == '#dc3545' ? 'danger' : 'success' }} text-uppercase mb-1 zen-title-xs">
                                <i class="feather-target me-1"></i> Cumplimiento SLA
                            </p>
                            <h2 class="text-dark zen-metric-xs" style="color: {{ $slaColor }}!important;">{{ number_format($slaCompliance, 1) }}%</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-muted zen-trend-xs">Tasa de resolución a tiempo</small>
                            </div>
                        </div>
                        <i class="feather-shield text-{{ $slaColor == '#dc3545' ? 'danger' : 'success' }} opacity-25" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                }
            });
        });
    </script>

    {{-- Fin Tarjetas --}}

    {{-- Tabs para organizar mejor la información --}}
    <ul class="nav nav-tabs mb-4 fade-in-up sticky-top" id="dashboardTabs" role="tablist" style="animation-delay: 0.7s; top: 230px; z-index: 1018;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="charts-tab" data-bs-toggle="tab" data-bs-target="#charts" type="button" role="tab" aria-controls="charts" aria-selected="true">
                <i class="feather-bar-chart me-2"></i>Gráficos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab" aria-controls="recent" aria-selected="false">
                <i class="feather-clock me-2"></i>Actividad Reciente
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab" aria-controls="performance" aria-selected="false">
                <i class="feather-users me-2"></i>Rendimiento
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="dashboardTabsContent">
        {{-- Tab de Gráficos --}}
        <div class="tab-pane fade show active" id="charts" role="tabpanel" aria-labelledby="charts-tab">
            {{-- Gráficos con Altura Fija para Control de UX --}}
            <div class="row">
                
                {{-- Gráfico 1: Soportes Creados por Mes (Línea) --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.7s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-trending-up me-2"></i>Soportes Creados por Mes</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart1Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart1Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorMesChart" data-type="line">Línea</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorMesChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorMesChart" data-type="area">Área</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorMesChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                   <canvas id="ticketsPorMesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 2: Distribución por Estado (Doughnut) --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.8s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-pie-chart me-2"></i>Distribución por Estado</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart2Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart2Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorEstadoChart" data-type="doughnut">Donut</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorEstadoChart" data-type="pie">Pastel</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorEstadoChart" data-type="polarArea">Polar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorEstadoChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="ticketsPorEstadoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 3: Soportes por Tipo --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 0.9s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-list me-2"></i>Soportes por Tipo</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart3Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart3Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorTipoChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorTipoChart" data-type="horizontalBar">Barras Horizontales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorTipoChart" data-type="radar">Radar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorTipoChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="ticketsPorTipoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 4: Soportes por Prioridad --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.0s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-alert-circle me-2"></i>Soportes por Prioridad</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart4Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart4Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorPrioridadChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorPrioridadChart" data-type="horizontalBar">Barras Horizontales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorPrioridadChart" data-type="pie">Pastel</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorPrioridadChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="ticketsPorPrioridadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 5: Soportes por Área --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.1s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-grid me-2"></i>Soportes por Área</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart5Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart5Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorAreaChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorAreaChart" data-type="horizontalBar">Barras Horizontales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorAreaChart" data-type="radar">Radar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorAreaChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="ticketsPorAreaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 6: Distribución por Día de la Semana --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.2s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-calendar me-2"></i>Distribución por Día de la Semana</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart6Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart6Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorDiaSemanaChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorDiaSemanaChart" data-type="line">Línea</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="ticketsPorDiaSemanaChart" data-type="radar">Radar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="ticketsPorDiaSemanaChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="ticketsPorDiaSemanaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 7: Tiempo de Resolución por Prioridad --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.3s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-clock me-2"></i>Tiempo de Resolución por Prioridad</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart7Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart7Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="tiempoResolucionPrioridadChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="tiempoResolucionPrioridadChart" data-type="horizontalBar">Barras Horizontales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="tiempoResolucionPrioridadChart" data-type="line">Línea</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="tiempoResolucionPrioridadChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="tiempoResolucionPrioridadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 8: Tasa de Cumplimiento SLA por Prioridad --}}
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.4s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-target me-2"></i>Cumplimiento SLA por Prioridad</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart8Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart8Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="slaPrioridadChart" data-type="bar">Barras</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="slaPrioridadChart" data-type="line">Línea</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="slaPrioridadChart" data-type="radar">Radar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="slaPrioridadChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="slaPrioridadChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 9: Rendimiento por Agente --}}
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm glassmorphism-card chart-container fade-in-up" style="animation-delay: 1.5s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-semibold text-primary mb-0"><i class="feather-users me-2"></i>Top Agentes por Soportes Cerrados</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="chart9Options" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="feather-more-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chart9Options">
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="rendimientoAgenteChart" data-type="bar">Barras Verticales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="rendimientoAgenteChart" data-type="horizontalBar">Barras Horizontales</a></li>
                                        <li><a class="dropdown-item chart-type" href="#" data-chart="rendimientoAgenteChart" data-type="radar">Radar</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item export-chart" href="#" data-chart="rendimientoAgenteChart">Exportar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-canvas-wrapper" style="height: 300px;">
                                <canvas id="rendimientoAgenteChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tab de Actividad Reciente --}}
        <div class="tab-pane fade" id="recent" role="tabpanel" aria-labelledby="recent-tab">
            <div class="card shadow-sm glassmorphism-card fade-in-up" style="animation-delay: 0.7s;">
                <div class="card-body">
                    <h5 class="card-title fw-semibold text-primary mb-4"><i class="feather-activity me-2"></i>Actividad Reciente</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Creado por</th>
                                    <th>Asignado a</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actividadReciente as $soporte)
                                    <tr>
                                        <td>{{ $soporte->id }}</td>
                                        <td>{{ Str::limit($soporte->detalles_soporte, 50) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $soporte->estadoSoporte->color ?? 'secondary' }}">
                                                {{ $soporte->estadoSoporte->nombre ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>
                                        <td>{{ $soporte->maeTercero->nombre ?? 'N/A' }}</td>
                                        <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tab de Rendimiento --}}
        <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm glassmorphism-card fade-in-up" style="animation-delay: 0.7s;">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold text-primary mb-4"><i class="feather-award me-2"></i>Top Agentes del Mes</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Agente</th>
                                            <th class="text-center">Tickets Cerrados</th>
                                            <th class="text-center">Tiempo Promedio de Resolución</th>
                                            <th class="text-center">Tasa de Cumplimiento SLA</th>
                                            <th class="text-center">Satisfacción</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                        @foreach($topAgentes as $agente)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2">
                                                            <span class="avatar-title bg-primary bg-opacity-10 text-primary fw-bold">
                                                                {{ substr($agente->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <div>{{ $agente->nom_ter }}</div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $agente->total_cerrados }}</td>
                                                <td class="text-center">{{ round($agente->tiempo_promedio, 1) }} hrs</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="progress me-2" style="width: 100px; height: 10px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, round((100 - ($agente->tiempo_promedio / 24) * 100), 1)) }}%"></div>
                                                        </div>
                                                        <span>{{ min(100, round((100 - ($agente->tiempo_promedio / 24) * 100), 1)) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="feather-star {{ $i <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de actividad reciente (collapsible) --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div class="toast-container">
            <div id="activityToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header glassmorphism-card">
                    <i class="feather-bell text-primary me-2"></i>
                    <strong class="me-auto">Nueva Actividad</strong>
                    <small>Hace un momento</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body glassmorphism-card" id="toastBody">
                    <!-- El contenido se llenará dinámicamente -->
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
            
            .avatar-xs {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
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
            
            /* Estilos para la tabla de actividad reciente */
            .timeline {
                position: relative;
                padding-left: 30px;
            }
            
            .timeline::before {
                content: '';
                position: absolute;
                left: 7px;
                top: 0;
                height: 100%;
                width: 2px;
                background: rgba(0, 0, 0, 0.1);
            }
            
            .timeline-item {
                position: relative;
                padding-bottom: 20px;
            }
            
            .timeline-item::before {
                content: '';
                position: absolute;
                left: -23px;
                top: 5px;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: white;
                border: 2px solid #a8dadc;
            }
            
            .timeline-item-icon {
                position: absolute;
                left: -30px;
                top: 0;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
            }
            
            /* Estilos para las pestañas */
            .nav-tabs {
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }
            
            .nav-tabs .nav-link {
                border: none;
                color: #6c757d;
                background: transparent;
                border-radius: 10px 10px 0 0;
                padding: 8px 16px;
                margin-right: 5px;
                transition: all 0.3s ease;
            }
            
            .nav-tabs .nav-link:hover {
                color: #457b9d;
                background: rgba(255, 255, 255, 0.5);
            }
            
            .nav-tabs .nav-link.active {
                color: #457b9d;
                background: rgba(255, 255, 255, 0.7);
                border-bottom: 3px solid #457b9d;
            }
            
            /* Estilos para el indicador de carga */
            .loader {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(255,255,255,.3);
                border-radius: 50%;
                border-top-color: #457b9d;
                animation: spin 1s ease-in-out infinite;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            /* Estilos para las vistas */
            .view-compact .card-body {
                padding: 1rem;
            }
            
            .view-detailed .card-body {
                padding: 1.5rem;
            }
            
            /* Estilos para los badges de estado */
            .status-badge {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 1rem;
            }
            
            .status-open {
                background-color: rgba(255, 184, 108, 0.2);
                color: #e67e22;
            }
            
            .status-in-progress {
                background-color: rgba(168, 218, 220, 0.2);
                color: #457b9d;
            }
            
            .status-closed {
                background-color: rgba(152, 216, 200, 0.2);
                color: #27ae60;
            }

            /* MEJORA: Estilos para el autocompletado */
            #searchResults {
                max-height: 200px;
                overflow-y: auto;
            }
            #searchResults .search-result-item {
                padding: 10px 15px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
            }
            #searchResults .search-result-item:hover {
                background-color: #f8f9fa;
            }
            #searchResults .search-result-item:last-child {
                border-bottom: none;
            }
        </style>
    @endpush

    @push('scripts')
        {{-- MEJORA: Añadimos librerías para exportación a PDF --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                
                // Configuración global para Chart.js
                Chart.defaults.font.family = 'Inter, sans-serif';
                Chart.defaults.color = '#495057';
                
                // Paleta de colores pastel más sofisticada
                const pastelColors = {
                    primary: 'rgba(69, 123, 157, 0.8)',  // Azul-Gris suave
                    secondary: 'rgba(168, 218, 220, 0.8)', // Azul Celeste
                    warning: 'rgba(255, 184, 108, 0.8)',   // Naranja suave
                    success: 'rgba(152, 216, 200, 0.8)',   // Verde Menta
                    info: 'rgba(29, 53, 87, 0.8)', // Azul Marino suave
                    danger: 'rgba(231, 76, 60, 0.8)' // Rojo para contrastar
                };

                // --- Gráfico 1: Soportes por Mes (Línea para tendencia, más dinámico) ---
                const ctx1 = document.getElementById('ticketsPorMesChart');
                if (ctx1) {
                    const ticketsPorMesChart = new Chart(ctx1.getContext('2d'), {
                        type: 'line', 
                        data: {
                            labels: {!! json_encode($labelsMes ?? []) !!},
                            datasets: [{
                                label: 'Soportes Creados',
                                data: {!! json_encode($dataMes ?? []) !!},
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
                }

                // --- Gráfico 2: Soportes por Estado (Doughnut) ---
                const ctx2 = document.getElementById('ticketsPorEstadoChart');
                if (ctx2) {
                    const ticketsPorEstadoChart = new Chart(ctx2.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($labelsEstado ?? []) !!},
                            datasets: [{
                                label: 'Cantidad',
                                data: {!! json_encode($dataEstado ?? []) !!},
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
                }

                // --- Gráfico 3: Soportes por Tipo ---
                const ctx3 = document.getElementById('ticketsPorTipoChart');
                if (ctx3) {
                    const ticketsPorTipoChart = new Chart(ctx3.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsTipo ?? []) !!},
                            datasets: [{
                                label: 'Cantidad',
                                data: {!! json_encode($dataTipo ?? []) !!},
                                backgroundColor: pastelColors.primary, 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                }
                            }
                        }
                    });
                }

                // --- Gráfico 4: Soportes por Prioridad ---
                const ctx4 = document.getElementById('ticketsPorPrioridadChart');
                if (ctx4) {
                    const ticketsPorPrioridadChart = new Chart(ctx4.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsPrioridad ?? []) !!},
                            datasets: [{
                                label: 'Cantidad',
                                data: {!! json_encode($dataPrioridad ?? []) !!},
                                backgroundColor: [
                                    pastelColors.danger, // Alta
                                    pastelColors.warning, // Media
                                    pastelColors.success, // Baja
                                ], 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                }
                            }
                        }
                    });
                }

                // --- Gráfico 5: Soportes por Área ---
                const ctx5 = document.getElementById('ticketsPorAreaChart');
                if (ctx5) {
                    const ticketsPorAreaChart = new Chart(ctx5.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsArea ?? []) !!},
                            datasets: [{
                                label: 'Cantidad',
                                data: {!! json_encode($dataArea ?? []) !!},
                                backgroundColor: pastelColors.info, 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                }
                            }
                        }
                    });
                }

                // --- Gráfico 6: Distribución por Día de la Semana ---
                const ctx6 = document.getElementById('ticketsPorDiaSemanaChart');
                if (ctx6) {
                    const ticketsPorDiaSemanaChart = new Chart(ctx6.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsDiaSemana ?? []) !!},
                            datasets: [{
                                label: 'Cantidad',
                                data: {!! json_encode($dataDiaSemana ?? []) !!},
                                backgroundColor: pastelColors.secondary, 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                }
                            }
                        }
                    });
                }

                // --- Gráfico 7: Tiempo de Resolución por Prioridad ---
                const ctx7 = document.getElementById('tiempoResolucionPrioridadChart');
                if (ctx7) {
                    const tiempoResolucionPrioridadChart = new Chart(ctx7.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsTiempoPrioridad ?? []) !!},
                            datasets: [{
                                label: 'Tiempo Promedio (horas)',
                                data: {!! json_encode($dataTiempoPrioridad ?? []) !!},
                                backgroundColor: [
                                    pastelColors.danger, // Alta
                                    pastelColors.warning, // Media
                                    pastelColors.success, // Baja
                                ], 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                }
                            }
                        }
                    });
                }

                // --- Gráfico 8: Tasa de Cumplimiento SLA por Prioridad ---
                const ctx8 = document.getElementById('slaPrioridadChart');
                if (ctx8) {
                    const slaPrioridadChart = new Chart(ctx8.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsSlaPrioridad ?? []) !!},
                            datasets: [{
                                label: 'Cumplimiento SLA (%)',
                                data: {!! json_encode($dataSlaPrioridad ?? []) !!},
                                backgroundColor: [
                                    pastelColors.danger, // Alta
                                    pastelColors.warning, // Media
                                    pastelColors.success, // Baja
                                ], 
                                borderColor: '#fff',
                                borderWidth: 1,
                                borderRadius: 4,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                                    ticks: { stepSize: 10 }
                                },
                                x: { grid: { display: false } }
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
                }

                // --- Gráfico 9: Rendimiento por Agente (Barras Horizontales) ---
                const ctx9 = document.getElementById('rendimientoAgenteChart');
                if (ctx9) {
                    const rendimientoAgenteChart = new Chart(ctx9.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($labelsAgente ?? []) !!},
                            datasets: [{
                                label: 'Tickets Cerrados',
                                data: {!! json_encode($dataAgente ?? []) !!},
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
                }

                // Almacenar referencias a los gráficos para actualizarlos más tarde
                const charts = {
                    ticketsPorMesChart,
                    ticketsPorEstadoChart,
                    ticketsPorTipoChart,
                    ticketsPorPrioridadChart,
                    ticketsPorAreaChart,
                    ticketsPorDiaSemanaChart,
                    tiempoResolucionPrioridadChart,
                    slaPrioridadChart,
                    rendimientoAgenteChart
                };

                // --- MEJORA: Función para actualizar gráficos dinámicamente ---
                function updateCharts() {
                    const startDate = document.getElementById('filterStartDate').value;
                    const endDate = document.getElementById('filterEndDate').value;
                    const priority = document.getElementById('priorityFilter').value;
                    const status = document.getElementById('statusFilter').value;

                    // Mostrar indicador de carga
                    const refreshBtn = document.getElementById('refreshDashboard');
                    const applyBtn = document.getElementById('applyDateFilters');
                    const originalContent = refreshBtn.innerHTML;
                    const originalApplyContent = applyBtn.innerHTML;
                    
                    refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...';
                    refreshBtn.disabled = true;
                    
                    applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aplicando...';
                    applyBtn.disabled = true;

                    // Construir URL con parámetros - CORREGIDO
                    let url = "{{ route('soportes.estadisticas.data') }}";
                    const params = new URLSearchParams();
                    
                    if (startDate) params.append('start_date', startDate);
                    if (endDate) params.append('end_date', endDate);
                    if (priority) params.append('priority', priority);
                    if (status) params.append('status', status);
                    
                    if (params.toString()) {
                        url += '?' + params.toString();
                    }

                    console.log('URL de solicitud:', url); // Para depuración

                    // Hacer llamada a la API
                    fetch(url)
                    .then(async response => {
                            
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor: ' + response.status);
                            }
                            console.log('Respuesta recibida:', response); // Para depuración
                            return response.json();
                        })
                        .then(data => {
                            console.log('Datos recibidos:', data); // Para depuración
                            
                            // Actualizar datos de los gráficos
                            if (charts.ticketsPorMesChart) {
                                charts.ticketsPorMesChart.data.labels = data.labelsMes || [];
                                charts.ticketsPorMesChart.data.datasets[0].data = data.dataMes || [];
                                charts.ticketsPorMesChart.update();
                            }

                            if (charts.ticketsPorEstadoChart) {
                                charts.ticketsPorEstadoChart.data.labels = data.labelsEstado || [];
                                charts.ticketsPorEstadoChart.data.datasets[0].data = data.dataEstado || [];
                                charts.ticketsPorEstadoChart.update();
                            }

                            if (charts.ticketsPorTipoChart) {
                                charts.ticketsPorTipoChart.data.labels = data.labelsTipo || [];
                                charts.ticketsPorTipoChart.data.datasets[0].data = data.dataTipo || [];
                                charts.ticketsPorTipoChart.update();
                            }

                            if (charts.ticketsPorPrioridadChart) {
                                charts.ticketsPorPrioridadChart.data.labels = data.labelsPrioridad || [];
                                charts.ticketsPorPrioridadChart.data.datasets[0].data = data.dataPrioridad || [];
                                charts.ticketsPorPrioridadChart.update();
                            }

                            if (charts.ticketsPorAreaChart) {
                                charts.ticketsPorAreaChart.data.labels = data.labelsArea || [];
                                charts.ticketsPorAreaChart.data.datasets[0].data = data.dataArea || [];
                                charts.ticketsPorAreaChart.update();
                            }

                            if (charts.ticketsPorDiaSemanaChart) {
                                charts.ticketsPorDiaSemanaChart.data.labels = data.labelsDiaSemana || [];
                                charts.ticketsPorDiaSemanaChart.data.datasets[0].data = data.dataDiaSemana || [];
                                charts.ticketsPorDiaSemanaChart.update();
                            }

                            if (charts.tiempoResolucionPrioridadChart) {
                                charts.tiempoResolucionPrioridadChart.data.labels = data.labelsTiempoPrioridad || [];
                                charts.tiempoResolucionPrioridadChart.data.datasets[0].data = data.dataTiempoPrioridad || [];
                                charts.tiempoResolucionPrioridadChart.update();
                            }

                            if (charts.slaPrioridadChart) {
                                charts.slaPrioridadChart.data.labels = data.labelsSlaPrioridad || [];
                                charts.slaPrioridadChart.data.datasets[0].data = data.dataSlaPrioridad || [];
                                charts.slaPrioridadChart.update();
                            }

                            if (charts.rendimientoAgenteChart) {
                                charts.rendimientoAgenteChart.data.labels = data.labelsAgente || [];
                                charts.rendimientoAgenteChart.data.datasets[0].data = data.dataAgente || [];
                                charts.rendimientoAgenteChart.update();
                            }
                            
                            // Actualizar KPIs
                            updateKPIs(data);
                            
                            // Restaurar los botones
                            refreshBtn.innerHTML = originalContent;
                            refreshBtn.disabled = false;
                            
                            applyBtn.innerHTML = originalApplyContent;
                            applyBtn.disabled = false;
                            
                            // Mostrar notificación de éxito
                            showToast(
                                'Datos actualizados',
                                'Los filtros se han aplicado correctamente.',
                                'bg-success text-white'
                            );
                        })
                        .catch(error => {
                            console.error('Error en get dashboard los datos:', error.message);
                            // Mostrar mensaje de error
                            showToast(
                                'Error al actualizar',
                                'No se pudieron cargar los datos filtrados. Por favor, inténtalo de nuevo.',
                                'bg-danger text-white'
                            );
                            
                            // Restaurar los botones
                            refreshBtn.innerHTML = originalContent;
                            refreshBtn.disabled = false;
                            
                            applyBtn.innerHTML = originalApplyContent;
                            applyBtn.disabled = false;
                        });
                }

                // Función para actualizar los KPIs
                function updateKPIs(data) {
                    // Actualizar KPIs con los nuevos datos
                    const totalTicketsElement = document.querySelector('[data-bs-title="Cantidad total de tickets de soporte registrados durante el periodo seleccionado."] .zen-metric-xs');
                    if (totalTicketsElement) totalTicketsElement.textContent = data.totalTickets || 0;
                    
                    const openTicketsElement = document.querySelector('[data-bs-title="Número de tickets que están actualmente Abiertos o en Progreso. Representa la carga de trabajo inmediata del equipo."] .zen-metric-xs');
                    if (openTicketsElement) openTicketsElement.textContent = data.openTickets || 0;
                    
                    const inProgressTicketsElement = document.querySelector('[data-bs-title="Número de tickets que están actualmente en proceso de resolución."] .zen-metric-xs');
                    if (inProgressTicketsElement) inProgressTicketsElement.textContent = data.inProgressTickets || 0;
                    
                    const closedTicketsElement = document.querySelector('[data-bs-title="Cantidad de tickets que fueron marcados como Resueltos o Cerrados en el periodo, indicando la productividad."] .zen-metric-xs');
                    if (closedTicketsElement) closedTicketsElement.textContent = data.closedTickets || 0;
                    
                    const avgResolutionTimeElement = document.querySelector('[data-bs-title="Tiempo promedio que toma cerrar un ticket, desde su creación hasta su resolución final. Una métrica más baja es mejor."] .zen-metric-xs');
                    if (avgResolutionTimeElement) avgResolutionTimeElement.textContent = data.avgResolutionTime || 'N/A';
                    
                    const avgFirstResponseTimeElement = document.querySelector('[data-bs-title="Tiempo promedio que toma dar la primera respuesta a un ticket, desde su creación. Una métrica más baja es mejor."] .zen-metric-xs');
                    if (avgFirstResponseTimeElement) avgFirstResponseTimeElement.textContent = data.avgFirstResponseTime || 'N/A';
                    
                    const escalatedTicketsElement = document.querySelector('[data-bs-title="Número de tickets que han sido escalados a un nivel superior de soporte."] .zen-metric-xs');
                    if (escalatedTicketsElement) escalatedTicketsElement.textContent = data.escalatedTickets || 0;
                    
                    const escalationRateElement = document.querySelector('[data-bs-title="Porcentaje de tickets que han sido escalados respecto al total. Una métrica más baja es mejor."] .zen-metric-xs');
                    if (escalationRateElement) escalationRateElement.textContent = (data.escalationRate || 0) + '%';
                    
                    const resolvedWithoutEscalationElement = document.querySelector('[data-bs-title="Número de tickets que fueron resueltos sin necesidad de escalar. Una métrica más alta es mejor."] .zen-metric-xs');
                    if (resolvedWithoutEscalationElement) resolvedWithoutEscalationElement.textContent = data.resolvedWithoutEscalation || 0;
                    
                    // Actualizar porcentajes en los círculos de progreso
                    updateProgressCircles(data);
                }

                // Función para actualizar los círculos de progreso
                function updateProgressCircles(data) {
                    const total = data.totalTickets > 0 ? data.totalTickets : 1;
                    
                    // Actualizar círculo de tickets abiertos
                    const openPercent = (data.openTickets / total) * 100;
                    const openProgressCircle = document.querySelector('[data-bs-title="Número de tickets que están actualmente Abiertos o en Progreso. Representa la carga de trabajo inmediata del equipo."] .circular-progress-xs');
                    if (openProgressCircle) {
                        openProgressCircle.style.setProperty('--progress-value', openPercent + '%');
                        const openProgressInner = openProgressCircle.querySelector('.circular-progress-inner-xs span');
                        if (openProgressInner) {
                            openProgressInner.textContent = Math.round(openPercent) + '%';
                        }
                    }
                    
                    // Actualizar círculo de tickets en proceso
                    const inProgressPercent = (data.inProgressTickets / total) * 100;
                    const inProgressProgressCircle = document.querySelector('[data-bs-title="Número de tickets que están actualmente en proceso de resolución."] .circular-progress-xs');
                    if (inProgressProgressCircle) {
                        inProgressProgressCircle.style.setProperty('--progress-value', inProgressPercent + '%');
                        const inProgressInner = inProgressProgressCircle.querySelector('.circular-progress-inner-xs span');
                        if (inProgressInner) {
                            inProgressInner.textContent = Math.round(inProgressPercent) + '%';
                        }
                    }
                    
                    // Actualizar círculo de tickets escalados
                    const escalatedPercent = (data.escalatedTickets / total) * 100;
                    const escalatedProgressCircle = document.querySelector('[data-bs-title="Número de tickets que han sido escalados a un nivel superior de soporte."] .circular-progress-xs');
                    if (escalatedProgressCircle) {
                        escalatedProgressCircle.style.setProperty('--progress-value', escalatedPercent + '%');
                        const escalatedInner = escalatedProgressCircle.querySelector('.circular-progress-inner-xs span');
                        if (escalatedInner) {
                            escalatedInner.textContent = Math.round(escalatedPercent) + '%';
                        }
                    }
                }

                // --- Lógica para filtros de fecha ---
                function applyDateFilters() {
                    updateCharts(); // Usamos la nueva función para actualizar
                }

                // Asignar eventos a los botones
                const applyDateFiltersBtn = document.getElementById('applyDateFilters');
                if (applyDateFiltersBtn) {
                    applyDateFiltersBtn.addEventListener('click', applyDateFilters);
                }

                const clearDateFiltersBtn = document.getElementById('clearDateFilters');
                if (clearDateFiltersBtn) {
                    clearDateFiltersBtn.addEventListener('click', () => {
                        // Limpiar filtros y actualizar
                        document.getElementById('filterStartDate').value = '';
                        document.getElementById('filterEndDate').value = '';
                        document.getElementById('priorityFilter').value = '';
                        document.getElementById('statusFilter').value = '';
                        updateCharts();
                    });
                }
                
                // --- Lógica para fechas predefinidas ---
                document.querySelectorAll('.preset-date').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const days = parseInt(this.getAttribute('data-days'));
                        const endDate = new Date();
                        const startDate = new Date();
                        startDate.setDate(endDate.getDate() - days);
                        
                        document.getElementById('filterStartDate').value = startDate.toISOString().split('T')[0];
                        document.getElementById('filterEndDate').value = endDate.toISOString().split('T')[0];
                        
                        applyDateFilters();
                    });
                });
                
                // --- Lógica para cambiar tipo de gráfico ---
                document.querySelectorAll('.chart-type').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const chartId = this.getAttribute('data-chart');
                        const newType = this.getAttribute('data-type');
                        
                        let chart;
                        switch(chartId) {
                            case 'ticketsPorMesChart':
                                chart = charts.ticketsPorMesChart;
                                break;
                            case 'ticketsPorEstadoChart':
                                chart = charts.ticketsPorEstadoChart;
                                break;
                            case 'ticketsPorTipoChart':
                                chart = charts.ticketsPorTipoChart;
                                break;
                            case 'ticketsPorPrioridadChart':
                                chart = charts.ticketsPorPrioridadChart;
                                break;
                            case 'ticketsPorAreaChart':
                                chart = charts.ticketsPorAreaChart;
                                break;
                            case 'ticketsPorDiaSemanaChart':
                                chart = charts.ticketsPorDiaSemanaChart;
                                break;
                            case 'tiempoResolucionPrioridadChart':
                                chart = charts.tiempoResolucionPrioridadChart;
                                break;
                            case 'slaPrioridadChart':
                                chart = charts.slaPrioridadChart;
                                break;
                            case 'rendimientoAgenteChart':
                                chart = charts.rendimientoAgenteChart;
                                break;
                        }
                        
                        if (chart) {
                            chart.config.type = newType;
                            chart.update();
                        }
                    });
                });
                
                // --- Lógica para exportar gráfico ---
                document.querySelectorAll('.export-chart').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const chartId = this.getAttribute('data-chart');
                        
                        let chart;
                        switch(chartId) {
                            case 'ticketsPorMesChart':
                                chart = charts.ticketsPorMesChart;
                                break;
                            case 'ticketsPorEstadoChart':
                                chart = charts.ticketsPorEstadoChart;
                                break;
                            case 'ticketsPorTipoChart':
                                chart = charts.ticketsPorTipoChart;
                                break;
                            case 'ticketsPorPrioridadChart':
                                chart = charts.ticketsPorPrioridadChart;
                                break;
                            case 'ticketsPorAreaChart':
                                chart = charts.ticketsPorAreaChart;
                                break;
                            case 'ticketsPorDiaSemanaChart':
                                chart = charts.ticketsPorDiaSemanaChart;
                                break;
                            case 'tiempoResolucionPrioridadChart':
                                chart = charts.tiempoResolucionPrioridadChart;
                                break;
                            case 'slaPrioridadChart':
                                chart = charts.slaPrioridadChart;
                                break;
                            case 'rendimientoAgenteChart':
                                chart = charts.rendimientoAgenteChart;
                                break;
                        }
                        
                        if (chart) {
                            const url = chart.toBase64Image();
                            const link = document.createElement('a');
                            link.download = chartId + '.png';
                            link.href = url;
                            link.click();
                        }
                    });
                });
                
                // --- Lógica para cambiar vista ---
                document.querySelectorAll('[data-view]').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const view = this.getAttribute('data-view');
                        
                        // Actualizar clases activas
                        document.querySelectorAll('[data-view]').forEach(el => {
                            el.classList.remove('active');
                        });
                        this.classList.add('active');
                        
                        // Actualizar vista
                        document.body.classList.remove('view-compact', 'view-detailed');
                        if (view === 'compact') {
                            document.body.classList.add('view-compact');
                        } else if (view === 'detailed') {
                            document.body.classList.add('view-detailed');
                        }
                    });
                });
                
                // --- MEJORA: Lógica para el buscador rápido con autocompletado y debouncing ---
                const searchInput = document.getElementById('quickSearch');
                const searchResults = document.getElementById('searchResults');
                let debounceTimer;

                if (searchInput && searchResults) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        
                        // Limpiar el temporizador anterior
                        clearTimeout(debounceTimer);
                        
                        if (searchTerm.length > 2) {
                            // Establecer un nuevo temporizador
                            debounceTimer = setTimeout(() => {
                                // Aquí harías una llamada fetch a tu backend para obtener sugerencias
                                // Ejemplo:
                                fetch(`/api/search?q=${searchTerm}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        // Limpiar resultados anteriores
                                        searchResults.innerHTML = '';
                                        
                                        if (data.length > 0) {
                                            // Mostrar contenedor de resultados
                                            searchResults.style.display = 'block';
                                            
                                            // Añadir nuevos resultados
                                            data.forEach(item => {
                                                const resultItem = document.createElement('div');
                                                resultItem.className = 'search-result-item';
                                                resultItem.innerHTML = `
                                                    <strong>${item.id}</strong> - ${item.description} (${item.client})
                                                `;
                                                resultItem.addEventListener('click', () => {
                                                    // Redirigir a la página del ticket o hacer lo que necesites
                                                    window.location.href = `/soportes/${item.id}`;
                                                });
                                                searchResults.appendChild(resultItem);
                                            });
                                        } else {
                                            searchResults.style.display = 'none';
                                        }
                                    });
                            }, 300); // Esperar 300ms después de que el usuario deje de escribir
                        } else {
                            searchResults.style.display = 'none';
                        }
                    });

                    // Ocultar resultados al hacer clic fuera del buscador
                    document.addEventListener('click', function(e) {
                        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                            searchResults.style.display = 'none';
                        }
                    });
                }
                
                // --- Lógica para filtros adicionales ---
                const priorityFilter = document.getElementById('priorityFilter');
                if (priorityFilter) {
                    priorityFilter.addEventListener('change', function(e) {
                        updateCharts(); // Usamos la nueva función para actualizar
                    });
                }
                
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', function(e) {
                        updateCharts(); // Usamos la nueva función para actualizar
                    });
                }
                
                // --- Lógica para actualizar dashboard ---
                const refreshDashboardBtn = document.getElementById('refreshDashboard');
                if (refreshDashboardBtn) {
                    refreshDashboardBtn.addEventListener('click', function() {
                        updateCharts();
                    });
                }
                
                // --- MEJORA: Lógica para exportar el dashboard a PDF ---
                const exportDashboardBtn = document.getElementById('exportDashboard');
                if (exportDashboardBtn) {
                    exportDashboardBtn.addEventListener('click', function() {
                        const { jsPDF } = window.jspdf;
                        
                        // Mostrar indicador de carga
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exportando...';
                        this.disabled = true;
                        
                        // Usar html2canvas para capturar el dashboard
                        html2canvas(document.getElementById('dashboardTabsContent'), {
                            scale: 0.5, // Reducir la escala para que el PDF no sea demasiado grande
                            useCORS: true, // Permitir cargar imágenes de otros dominios
                            logging: false // Desactivar logs para mejorar el rendimiento
                        }).then(canvas => {
                            const imgData = canvas.toDataURL('image/png');
                            const pdf = new jsPDF('l', 'mm', 'a4'); // Orientación horizontal, tamaño A4
                            
                            const imgWidth = 280; // Ancho de la imagen en el PDF
                            const pageHeight = 200; // Altura de la página en el PDF
                            let imgHeight = canvas.height * imgWidth / canvas.width;
                            let heightLeft = imgHeight;
                            let position = 10; // Posición inicial en el PDF
                            
                            // Añadir la imagen al PDF
                            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                            
                            // Si la imagen es más alta que la página, añadir nuevas páginas
                            while (heightLeft >= 0) {
                                position = heightLeft - imgHeight;
                                pdf.addPage();
                                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                                heightLeft -= pageHeight;
                            }
                            
                            // Guardar el PDF
                            pdf.save('dashboard.pdf');
                            
                            // Restaurar el botón
                            this.innerHTML = '<i class="feather-download"></i> <span class="d-none d-md-inline">Exportar PDF</span>';
                            this.disabled = false;
                        });
                    });
                }
                
                // --- Lógica para la actividad reciente ---
                function loadRecentActivity() {
                    const timeline = document.getElementById('recentActivity');
                    
                    if (!timeline) return;
                    
                    // Simulación de datos de actividad reciente
                    const activities = [
                        {
                            icon: 'feather-plus-circle',
                            iconBg: 'bg-success bg-opacity-10',
                            iconColor: 'text-success',
                            title: 'Nuevo soporte creado',
                            description: 'Ticket #1234 - Problema con la impresora',
                            time: 'Hace 5 minutos',
                            user: 'Juan Pérez'
                        },
                        {
                            icon: 'feather-check-circle',
                            iconBg: 'bg-primary bg-opacity-10',
                            iconColor: 'text-primary',
                            title: 'Soporte cerrado',
                            description: 'Ticket #1232 - Error de conexión',
                            time: 'Hace 15 minutos',
                            user: 'María García'
                        },
                        {
                            icon: 'feather-user-plus',
                            iconBg: 'bg-warning bg-opacity-10',
                            iconColor: 'text-warning',
                            title: 'Soporte escalado',
                            description: 'Ticket #1230 - Requiere intervención técnica',
                            time: 'Hace 30 minutos',
                            user: 'Pedro López'
                        },
                        {
                            icon: 'feather-message-circle',
                            iconBg: 'bg-info bg-opacity-10',
                            iconColor: 'text-info',
                            title: 'Nueva observación',
                            description: 'Ticket #1228 - Cliente solicita actualización',
                            time: 'Hace 1 hora',
                            user: 'Ana Martínez'
                        }
                    ];
                    
                    timeline.innerHTML = '';
                    
                    activities.forEach(activity => {
                        const item = document.createElement('div');
                        item.className = 'timeline-item';
                        item.innerHTML = `
                            <div class="timeline-item-icon ${activity.iconBg}">
                                <i class="feather ${activity.icon} ${activity.iconColor}"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-1">${activity.title}</h6>
                                <p class="text-muted mb-1">${activity.description}</p>
                                <small class="text-muted">${activity.time} - ${activity.user}</small>
                            </div>
                        `;
                        timeline.appendChild(item);
                    });
                }
                
                // Cargar actividad reciente cuando se cambia a la pestaña correspondiente
                const recentTab = document.getElementById('recent-tab');
                if (recentTab) {
                    recentTab.addEventListener('shown.bs.tab', function() {
                        loadRecentActivity();
                    });
                }
                
                // --- Lógica para notificaciones toast ---
                function showToast(title, message, type = 'info') {
                    const toastEl = document.getElementById('activityToast');
                    const toastBody = document.getElementById('toastBody');
                    
                    if (!toastEl || !toastBody) return;
                    
                    // Actualizar contenido
                    toastBody.innerHTML = `<strong>${title}</strong><p class="mb-0">${message}</p>`;
                    
                    // Actualizar tipo
                    toastEl.className = `toast ${type}`;
                    
                    // Mostrar toast
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
                
                // Simulación de notificación después de 5 segundos
                setTimeout(() => {
                    showToast(
                        'Nuevo soporte asignado',
                        'Ticket #1235 ha sido asignado a ti',
                        'bg-info text-white'
                    );
                }, 5000);
                
                // Inicializar efectos de entrada después de que el DOM esté listo
                document.querySelectorAll('.fade-in-up').forEach(function(element) { 
                    element.style.opacity = '1'; // El CSS lo maneja, solo para asegurar que se muestre después de la animación
                });
            });
        </script>
    @endpush
</x-base-layout>