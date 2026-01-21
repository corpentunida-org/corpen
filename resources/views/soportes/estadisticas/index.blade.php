<x-base-layout>
    {{-- 🎨 ESTILOS VISUALES "MODERN SAAS PROFESSIONAL" --}}
    <style>
        :root {
            /* --- Paleta Corporativa Moderna --- */
            --bg-body: #f3f4f6;          /* Gris muy suave para fondo */
            --bg-card: #ffffff;          /* Blanco puro */
            --bg-panel: #f9fafb;         /* Blanco humo para paneles */
            
            --text-main: #111827;        /* Negro suave casi gris */
            --text-muted: #6b7280;       /* Gris medio */
            --text-light: #9ca3af;       /* Gris claro */

            /* Colores de Marca (Serios) */
            --primary: #2563eb;          /* Azul Real - Acción principal */
            --primary-dark: #1d4ed8;     /* Azul oscuro - Hover */
            --primary-light: #eff6ff;    /* Fondo azul muy claro */
            
            /* Estados Semánticos Refinados */
            --success: #10b981;          /* Esmeralda */
            --warning: #f59e0b;          /* Ámbar */
            --danger: #ef4444;           /* Rojo Corporativo */
            --info: #3b82f6;             /* Azul información */
            --purple: #8b5cf6;           /* Violeta moderno */
            --dark: #1f2937;             /* Gris oscuro */
            
            /* UI Elements */
            --radius: 8px;               /* Bordes menos redondeados = más serio */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --border-color: #e5e7eb;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* --- CONTENEDORES & TARJETAS --- */
        .card-box {
            background: var(--bg-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            height: 100%;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card-box:hover {
            box-shadow: var(--shadow-md);
        }

        /* --- HEADER & TITULOS --- */
        h2, h5, h6 { letter-spacing: -0.025em; }
        .page-header-title { font-size: 1.5rem; font-weight: 700; color: var(--text-main); }
        .section-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; margin-bottom: 1rem; display: block; }

        /* --- FILTROS (BARRA DE CONTROL) --- */
        .filter-panel {
            background: var(--bg-card);
            padding: 1.25rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .input-group-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            display: block;
        }

        .form-control-modern {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.6rem 0.8rem;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.2s;
            color: var(--text-main);
        }
        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
            outline: none;
        }

        /* --- BOTONES DE FECHA (PRESETS) --- */
        .preset-container {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            padding-top: 0.5rem;
            border-top: 1px dashed var(--border-color);
            margin-top: 0.5rem;
        }
        .btn-date-preset {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: var(--text-muted);
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-date-preset:hover { background: #f3f4f6; color: var(--text-main); }
        .btn-date-preset.active {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
        }

        /* --- KPIS (Innovadores & Limpios) --- */
        .kpi-box {
            background: white;
            padding: 1.25rem;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .kpi-box:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }
        .kpi-box.active-filter {
            background: linear-gradient(to bottom right, #ffffff, #f0f7ff);
            border: 1px solid var(--primary);
            box-shadow: 0 0 0 1px var(--primary);
        }
        
        .kpi-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }
        .kpi-title { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.02em; }
        .kpi-icon { font-size: 1.2rem; color: var(--text-light); transition: color 0.3s; }
        .kpi-box:hover .kpi-icon { color: var(--primary); }
        
        .kpi-number { font-size: 1.75rem; font-weight: 700; color: var(--text-main); line-height: 1.2; letter-spacing: -0.03em; }
        .kpi-trend { font-size: 0.75rem; margin-top: 5px; display: inline-flex; align-items: center; gap: 4px; }
        
        /* Variaciones KPI sutiles */
        .kpi-highlight .kpi-number { color: var(--primary); }
        
        /* --- TABLA (Clean Corporate) --- */
        .table-container {
            max-height: 550px;
            overflow-y: auto;
            border-radius: 0 0 var(--radius) var(--radius);
        }
        .easy-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .easy-table th { 
            background: #f9fafb; 
            color: var(--text-muted); 
            font-weight: 600; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            position: sticky; top: 0; z-index: 10;
            backdrop-filter: blur(8px);
            background: rgba(249, 250, 251, 0.95);
        }
        .easy-table td { 
            padding: 0.85rem 1rem; 
            border-bottom: 1px solid var(--border-color); 
            font-size: 0.875rem; 
            color: var(--text-main); 
            vertical-align: middle; 
            background: white;
        }
        .easy-table tr:last-child td { border-bottom: none; }
        .easy-table tr:hover td { background-color: #f9fafb; }

        /* --- BOTONES ACCIÓN --- */
        .btn-action {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem;
            cursor: pointer; transition: all 0.2s; border: 1px solid transparent;
        }
        .btn-primary-action {
            background: var(--primary); color: white;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.3);
        }
        .btn-primary-action:hover { background: var(--primary-dark); transform: translateY(-1px); }
        
        .btn-secondary-action {
            background: white; border-color: #d1d5db; color: var(--text-main);
        }
        .btn-secondary-action:hover { background: #f9fafb; border-color: #9ca3af; }

        .btn-icon-only { padding: 0.4rem; border-radius: 4px; color: var(--text-muted); }
        .btn-icon-only:hover { background: #f3f4f6; color: var(--primary); }

        /* --- NAV TABS (Underline Style) --- */
        .nav-tabs-simple {
            display: flex; gap: 2rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0;
        }
        .tab-btn {
            background: transparent; border: none; 
            padding: 0.75rem 0; 
            font-weight: 500; color: var(--text-muted); 
            cursor: pointer; transition: all 0.3s;
            border-bottom: 2px solid transparent;
            font-size: 0.9rem;
            display: flex; align-items: center; gap: 6px;
        }
        .tab-btn:hover { color: var(--primary); }
        .tab-btn.active { 
            color: var(--primary); 
            border-bottom-color: var(--primary); 
            font-weight: 600;
        }
        
        .content-area { display: none; animation: fadeIn 0.3s ease-out; }
        .content-area.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* Badges & Status */
        .badge-status { 
            padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;
            display: inline-block;
        }
        .badge-gray { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        
        /* Loader */
        .loader-overlay { position: fixed; inset: 0; background: rgba(255,255,255,0.8); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        
        /* Toast */
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9990; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast-alert { 
            background: white; padding: 1rem; border-radius: 6px; box-shadow: var(--shadow-lg); 
            border-left: 4px solid var(--dark); min-width: 320px; animation: slideInRight 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        .toast-success { border-left-color: var(--success); }
        .toast-error { border-left-color: var(--danger); }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Avatar */
        .avatar-circle {
            width: 24px; height: 24px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 0.7rem; font-weight: 700; color: white;
        }

        /* --- 🖨️ ESTILOS EXCLUSIVOS MODO PDF/REPORTE --- */
        body.printing-mode {
            background-color: white !important;
            padding: 0 !important;
        }
        body.printing-mode .filter-panel,
        body.printing-mode .nav-tabs-simple,
        body.printing-mode .btn-action,
        body.printing-mode .toast-container,
        body.printing-mode .btn-icon-only {
            display: none !important;
        }
        body.printing-mode .content-area {
            display: block !important; /* Muestra todas las pestañas */
            margin-bottom: 3rem;
            animation: none !important;
        }
        body.printing-mode .table-container {
            max-height: none !important; /* Expande la tabla */
            overflow: visible !important;
        }
        body.printing-mode .card-box, 
        body.printing-mode .kpi-box {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            break-inside: avoid; /* Evita que las tarjetas se corten entre páginas */
        }
        body.printing-mode .page-header-title {
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
    </style>

    {{-- WRAPPER PRINCIPAL PARA PDF --}}
    <div id="dashboard-container">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="page-header-title mb-2">Informe de Soportes</h2>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="feather-activity text-success"></i>
                    <span>Datos actualizados en tiempo real</span>
                    <span class="mx-1">•</span>
                    <span id="dateRangeLabel">Periodo Actual</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-action btn-secondary-action" id="refreshDashboard">
                    <i class="feather-refresh-cw"></i> Actualizar
                </button>
                <button class="btn-action btn-primary-action" id="exportDashboard">
                    <i class="feather-download-cloud"></i> Exportar Reporte
                </button>
            </div>
        </div>

        {{-- NAVEGACIÓN (TABS) --}}
        <div class="nav-tabs-simple">
            <button class="tab-btn active" data-target="graficos">
                <i class="feather-pie-chart"></i> Vista General
            </button>
            <button class="tab-btn" data-target="listado">
                <i class="feather-database"></i> Registros Detallados
            </button>
            <button class="tab-btn" data-target="rendimiento">
                <i class="feather-trending-up"></i> Métricas de Equipo
            </button>
        </div>

        {{-- 🔍 FILTROS (BARRA DE CONTROL) --}}
        <div class="filter-panel">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="input-group-label">Desde</label>
                    <input type="date" id="filterStartDate" class="form-control-modern" value="{{ $startDate->format('Y-m-d') }}" data-default="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="input-group-label">Hasta</label>
                    <input type="date" id="filterEndDate" class="form-control-modern" value="{{ $endDate->format('Y-m-d') }}" data-default="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="input-group-label">Prioridad</label>
                    <select id="priorityFilter" class="form-control-modern">
                        <option value="">Todas las prioridades</option>
                        @foreach($labelsPrioridad as $key => $label)
                            <option value="{{ $key + 1 }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="input-group-label">Estado</label>
                    <select id="statusFilter" class="form-control-modern">
                        <option value="">Todos los estados</option>
                        <option value="1">Sin Asignar</option>
                        <option value="2">En Proceso</option>
                        <option value="3">En Revisión</option>
                        <option value="4">Cerrado</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 d-flex align-items-end gap-2">
                    <button id="applyDateFilters" class="btn-action btn-primary-action w-100">Filtrar</button>
                    <button id="clearFiltersBtn" class="btn-action btn-secondary-action" title="Limpiar"><i class="feather-x"></i></button>
                </div>
            </div>

            <div class="preset-container">
                <span class="text-muted small me-2" style="font-size: 0.75rem;">Accesos rápidos:</span>
                <button class="btn-date-preset" data-days="7">7 Días</button>
                <button class="btn-date-preset" data-days="30">Este Mes</button>
                <button class="btn-date-preset" data-days="90">Este Trimestre</button>
                <button class="btn-date-preset" data-days="365">Año Actual</button>
            </div>
        </div>

        {{-- SECCIÓN 1: GRÁFICOS Y KPIS --}}
        <div id="section-graficos" class="content-area active">
            
            {{-- FILA 1: KPIs PRINCIPALES (Diseño Limpio) --}}
            <span class="section-label">Métricas de Volumen</span>
            <div class="row g-3 mb-5">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box active-filter" onclick="filterKpi('')" id="card-total">
                        <div class="kpi-header">
                            <span class="kpi-title">Total</span>
                            <i class="feather-layers kpi-icon"></i>
                        </div>
                        <div class="kpi-number" id="totalTicketsMetric">{{ $totalTickets }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box" onclick="filterKpi('1')" id="card-status-1">
                        <div class="kpi-header">
                            <span class="kpi-title">Pendientes</span>
                            <i class="feather-alert-circle kpi-icon text-warning"></i>
                        </div>
                        <div class="kpi-number" id="openTicketsMetric">{{ $openTickets }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box" onclick="filterKpi('2')" id="card-status-2">
                        <div class="kpi-header">
                            <span class="kpi-title">En Proceso</span>
                            <i class="feather-loader kpi-icon text-info"></i>
                        </div>
                        <div class="kpi-number" id="inProgressTicketsMetric">{{ $inProgressTickets }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box" onclick="filterKpi('3')" id="card-status-3">
                        <div class="kpi-header">
                            <span class="kpi-title">Revisión</span>
                            <i class="feather-eye kpi-icon text-purple"></i>
                        </div>
                        <div class="kpi-number" id="revisionTicketsMetric">{{ $revisionTickets ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box" onclick="filterKpi('4')" id="card-status-4">
                        <div class="kpi-header">
                            <span class="kpi-title">Cerrados</span>
                            <i class="feather-check-circle kpi-icon text-success"></i>
                        </div>
                        <div class="kpi-number" id="closedTicketsMetric">{{ $closedTickets }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="kpi-box kpi-highlight">
                        <div class="kpi-header">
                            <span class="kpi-title">Efectividad</span>
                            <i class="feather-zap kpi-icon"></i>
                        </div>
                        <div class="kpi-number"><span id="firstResponseRateMetric">{{ $firstResponseRate ?? 0 }}</span><small style="font-size: 1rem;">%</small></div>
                    </div>
                </div>
            </div>

            {{-- FILA 2: CALIDAD Y TIEMPOS --}}
            <span class="section-label">Indicadores de Calidad (SLA & Tiempos)</span>
            <div class="row g-3 mb-4">
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">TIEMPO RESOLUCIÓN</div>
                        <div class="fs-4 fw-bold text-dark" id="avgResolutionTimeMetric">{{ $avgResolutionTime }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">SLA CUMPLIDO</div>
                        <div class="fs-4 fw-bold text-success"><span id="slaComplianceMetric">{{ $slaCompliance ?? 0 }}</span>%</div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">1RA RESPUESTA</div>
                        <div class="fs-4 fw-bold text-dark" id="avgFirstResponseTimeMetric">{{ $avgFirstResponseTime ?? '0 hrs' }}</div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">REAPERTURAS</div>
                        <div class="fs-4 fw-bold text-warning"><span id="reopenRateMetric">{{ $reopenRate ?? 0 }}</span>%</div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">CSAT (1-5)</div>
                        <div class="fs-4 fw-bold text-primary"><span id="csatScoreMetric">{{ $csatScore ?? 0 }}</span></div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="card-box p-3 text-center">
                        <div class="text-muted small fw-bold mb-1">ESCALACIÓN</div>
                        <div class="fs-4 fw-bold text-danger"><span id="escalationRateMetric">{{ $escalationRate ?? 0 }}</span>%</div>
                    </div>
                </div>
            </div>

            {{-- GRÁFICOS PRINCIPALES --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card-box p-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold m-0 text-dark">Tendencia de Tickets</h5>
                            <button class="btn-icon-only"><i class="feather-more-horizontal"></i></button>
                        </div>
                        <div style="height: 320px;"><canvas id="ticketsPorMesChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-box p-4">
                        <h5 class="fw-bold mb-4 text-dark">Distribución por Estado</h5>
                        <div style="height: 250px; display: flex; justify-content: center;"><canvas id="ticketsPorEstadoChart"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- 📊 CATEGORIZACIÓN --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card-box p-4">
                        <h6 class="section-label text-center">Por Tipo</h6>
                        <div style="height: 200px;"><canvas id="ticketsPorTipoChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-box p-4">
                        <h6 class="section-label text-center">Por Prioridad</h6>
                        <div style="height: 200px;"><canvas id="ticketsPorPrioridadChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-box p-4">
                        <h6 class="section-label text-center">Por Área Funcional</h6>
                        <div style="height: 200px;"><canvas id="ticketsPorAreaChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: LISTADO COMPLETO (CRUD) --}}
        <div id="section-listado" class="content-area">
            <div class="card-box">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <h5 class="fw-bold mb-1">Bitácora de Soportes</h5>
                        <p class="text-muted small m-0">Visualizando registros detallados</p>
                    </div>
                    <button class="btn-action btn-secondary-action btn-sm" onclick="exportTableToCSV('actividad-table')">
                        <i class="feather-file-text me-1"></i> CSV
                    </button>
                </div>
                <div class="table-container">
                    <table class="easy-table" id="actividad-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Folio</th>
                                <th>Descripción / Asunto</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Asignado A</th>
                                <th>Fecha Creación</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-soportes">
                            @foreach($actividadReciente as $soporte)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-primary">#{{ $soporte->id }}</td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">{{ Str::limit($soporte->detalles_soporte, 50) }}</div>
                                </td>
                                <td>
                                    <span class="badge-status badge-gray">{{ $soporte->estadoSoporte->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge-status badge-gray">{{ $soporte->prioridad->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($soporte->scpUsuarioAsignado)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle" style="background-color: var(--primary);">
                                                {{ substr($soporte->scpUsuarioAsignado->name ?? ($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'U'), 0, 1) }}
                                            </div>
                                            <span class="small fw-medium">{{ $soporte->scpUsuarioAsignado->name ?? ($soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Usuario') }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">-- Sin Asignar --</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $soporte->created_at->format('d M, Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('soportes.soportes.show', $soporte->id) }}" class="btn-icon-only" title="Ver Detalles">
                                        <i class="feather-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-2 text-center text-muted small bg-light border-top" id="table-footer-info">
                    Registros mostrados: {{ count($actividadReciente) }}
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: RENDIMIENTO --}}
        <div id="section-rendimiento" class="content-area">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-box p-4">
                        <h5 class="fw-bold mb-4">Métricas de Calidad (Radar)</h5>
                        <div style="height: 350px;"><canvas id="kpiCalidadChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-box h-100">
                        <div class="p-4 border-bottom bg-light">
                            <h5 class="fw-bold mb-0">Top Agentes (Resolución)</h5>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="easy-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Agente</th>
                                        <th class="text-center">Resueltos</th>
                                        <th class="pe-4">Rendimiento Relativo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topAgentes as $agente)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $agente->name ?? $agente->nom_ter }}</td>
                                        <td class="text-center fw-bold">{{ $agente->total_cerrados }}</td>
                                        <td class="pe-4">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $agente->total_cerrados * 2) }}%;"></div>
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
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card-box p-4">
                        <h5 class="fw-bold mb-4">Rendimiento Comparativo por Área</h5>
                        <div style="height: 300px;"><canvas id="rendimientoAreaChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- End Dashboard Container --}}

    {{-- LOADER --}}
    <div class="loader-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <h6 class="fw-bold" id="loaderText">Procesando datos...</h6>
        </div>
    </div>

    {{-- TOAST CONTAINER --}}
    <div class="toast-container" id="toastContainer"></div>

    @push('scripts')
    {{-- LIBRERÍAS EXTERNAS: Chart.js y html2pdf.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        let updateDashboard, filterKpi, exportTableToCSV;

        document.addEventListener('DOMContentLoaded', function () {
            
            // --- Configuración Profesional Chart.js ---
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#6b7280';
            Chart.defaults.scale.grid.color = '#f3f4f6';
            Chart.defaults.plugins.tooltip.backgroundColor = '#1f2937';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 6;
            
            const colors = { 
                primary: '#2563eb', secondary: '#64748b', info: '#3b82f6', 
                success: '#10b981', warning: '#f59e0b', danger: '#ef4444', purple: '#8b5cf6'
            };

            let chartMes, chartEstado, chartTipo, chartPrioridad, chartArea, chartKPI, chartRadarArea;

            function renderCharts(data) {
                [chartMes, chartEstado, chartTipo, chartPrioridad, chartArea, chartKPI, chartRadarArea].forEach(c => c && c.destroy());

                // 1. Line Chart
                chartMes = new Chart(document.getElementById('ticketsPorMesChart'), {
                    type: 'line',
                    data: { 
                        labels: data.labelsMes, 
                        datasets: [{ 
                            label: 'Tickets Nuevos', 
                            data: data.dataMes, 
                            borderColor: colors.primary, 
                            backgroundColor: 'rgba(37, 99, 235, 0.05)', 
                            fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3
                        }] 
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
                });

                // 2. Doughnut Chart (Estados)
                chartEstado = new Chart(document.getElementById('ticketsPorEstadoChart'), {
                    type: 'doughnut',
                    data: { 
                        labels: data.labelsEstado, 
                        datasets: [{ 
                            data: data.dataEstado, 
                            backgroundColor: [colors.warning, colors.info, colors.purple, colors.success], 
                            borderWidth: 0, hoverOffset: 4
                        }] 
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, padding: 20 } } } }
                });

                // 3. Tipo
                if(document.getElementById('ticketsPorTipoChart') && data.ticketsByType) {
                    chartTipo = new Chart(document.getElementById('ticketsPorTipoChart'), {
                        type: 'doughnut',
                        data: { labels: data.ticketsByType.map(i=>i.label), datasets: [{ data: data.ticketsByType.map(i=>i.data), backgroundColor: [colors.primary, colors.info, colors.success, colors.warning, colors.danger], borderWidth: 0 }] },
                        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false } } }
                    });
                }
                
                // 4. Prioridad
                if(document.getElementById('ticketsPorPrioridadChart') && data.ticketsByPriority) {
                    chartPrioridad = new Chart(document.getElementById('ticketsPorPrioridadChart'), {
                        type: 'bar',
                        data: { labels: data.ticketsByPriority.map(i=>i.label), datasets: [{ label:'Volumen', data: data.ticketsByPriority.map(i=>i.data), backgroundColor: [colors.danger, colors.warning, colors.success], borderRadius: 4, barThickness: 20 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { display: false } } }
                    });
                }

                // 5. Area
                if(document.getElementById('ticketsPorAreaChart') && data.labelsArea) {
                    chartArea = new Chart(document.getElementById('ticketsPorAreaChart'), {
                        type: 'bar',
                        data: { labels: data.labelsArea, datasets: [{ label:'Total', data: data.dataArea, backgroundColor: colors.secondary, borderRadius: 4, barThickness: 15 }] },
                        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { grid: { display: false } } } }
                    });
                }

                // 6. Radar KPIs
                if(document.getElementById('kpiCalidadChart')) {
                    chartKPI = new Chart(document.getElementById('kpiCalidadChart'), {
                        type: 'radar',
                        data: {
                            labels: ['SLA %', 'CSAT', 'Resp. 1er Cont.', 'Sin Reapertura', 'Sin Escalación'],
                            datasets: [{ label: 'Desempeño', data: [ data.slaCompliance||0, (data.csatScore||0)*20, data.firstResponseRate||0, Math.max(0,100-(data.reopenRate||0)), Math.max(0,100-(data.escalationRate||0)) ], backgroundColor: 'rgba(37, 99, 235, 0.2)', borderColor: colors.primary, borderWidth: 2 }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, scales: { r: { beginAtZero: true, max: 100, ticks: { display: false }, grid: { color: '#e5e7eb' } } } }
                    });
                }

                // 7. Radar Area
                if(document.getElementById('rendimientoAreaChart') && data.labelsArea) {
                    chartRadarArea = new Chart(document.getElementById('rendimientoAreaChart'), {
                        type: 'radar',
                        data: { labels: data.labelsArea, datasets: [{ label: 'Resueltos', data: data.dataArea, backgroundColor: 'rgba(16, 185, 129, 0.2)', borderColor: colors.success }] },
                        options: { responsive: true, maintainAspectRatio: false, scales: { r: { grid: { color: '#e5e7eb' }, ticks: { display: false } } } }
                    });
                }
            }

            // Datos Iniciales
            const initialData = {
                labelsMes: {!! json_encode($labelsMes) !!}, dataMes: {!! json_encode($dataMes) !!},
                labelsEstado: {!! json_encode($labelsEstado) !!}, dataEstado: {!! json_encode($dataEstado) !!},
                labelsArea: {!! json_encode($labelsArea) !!}, dataArea: {!! json_encode($dataArea) !!},
                ticketsByType: {!! json_encode($ticketsByType ?? []) !!},
                ticketsByPriority: {!! json_encode($ticketsByPriority ?? []) !!},
                slaCompliance: {{ $slaCompliance ?? 0 }}, csatScore: {{ $csatScore ?? 0 }}, firstResponseRate: {{ $firstResponseRate ?? 0 }},
                reopenRate: {{ $reopenRate ?? 0 }}, escalationRate: {{ $escalationRate ?? 0 }}
            };
            renderCharts(initialData);

            // --- Lógica de Interfaz ---
            
            // Tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.content-area').forEach(s => s.classList.remove('active'));
                    document.getElementById('section-'+this.dataset.target).classList.add('active');
                });
            });

            // Toast
            function showToast(msg, type='success') {
                const id = 't-'+Date.now();
                const icon = type === 'success' ? 'check-circle text-success' : 'alert-triangle text-danger';
                const html = `<div id="${id}" class="toast-alert toast-${type}"><i class="feather-${icon} fs-5"></i><div><div class="fw-bold text-dark" style="font-size:0.9rem;">${type==='success'?'Operación Exitosa':'Atención'}</div><div class="small text-muted">${msg}</div></div></div>`;
                document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);
                setTimeout(()=>{ const el=document.getElementById(id); if(el) { el.style.opacity='0'; setTimeout(()=>el.remove(),300); } }, 4000);
            }

            // --- Lógica de Datos ---
            const loader = document.getElementById('loadingOverlay');
            
            updateDashboard = function() {
                const start = document.getElementById('filterStartDate').value;
                const end = document.getElementById('filterEndDate').value;
                if(start > end) return showToast("La fecha de inicio no puede ser mayor al final", "error");

                document.getElementById('loaderText').innerText = "Procesando datos...";
                loader.style.display = 'flex';
                const params = new URLSearchParams({
                    start_date: start, end_date: end,
                    priority: document.getElementById('priorityFilter').value,
                    status: document.getElementById('statusFilter').value
                });

                fetch("{{ route('soportes.estadisticas.data') }}?" + params.toString())
                    .then(res => res.json())
                    .then(data => {
                        const kpiMap = {
                            'totalTicketsMetric': data.totalTickets, 'openTicketsMetric': data.openTickets,
                            'inProgressTicketsMetric': data.inProgressTickets, 'revisionTicketsMetric': data.revisionTickets,
                            'closedTicketsMetric': data.closedTickets, 'firstResponseRateMetric': data.firstResponseRate,
                            'avgResolutionTimeMetric': data.avgResolutionTime, 'slaComplianceMetric': data.slaCompliance,
                            'avgFirstResponseTimeMetric': data.avgFirstResponseTime, 'reopenRateMetric': data.reopenRate,
                            'csatScoreMetric': data.csatScore, 'escalationRateMetric': data.escalationRate
                        };
                        for (const [id, val] of Object.entries(kpiMap)) {
                            const el = document.getElementById(id); if(el) el.innerText = val !== undefined ? val : 0;
                        }

                        const tbody = document.getElementById('table-body-soportes');
                        tbody.innerHTML = '';
                        if(data.actividadReciente && data.actividadReciente.length) {
                            data.actividadReciente.forEach(item => {
                                const initial = item.asignado_nombre ? item.asignado_nombre.charAt(0) : 'U';
                                const row = `<tr><td class="ps-4 fw-bold font-monospace text-primary">#${item.id}</td><td><div class="fw-bold text-dark mb-1">${(item.detalles_soporte||'').substring(0,50)}...</div></td><td><span class="badge-status badge-gray">${item.estado_nombre}</span></td><td><span class="badge-status badge-gray">${item.prioridad_nombre}</span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-circle" style="background-color: var(--primary);">${initial}</div><span class="small fw-medium">${item.asignado_nombre || 'Sin asignar'}</span></div></td><td class="text-muted small">${item.created_at_formatted}</td><td class="text-end pe-4"><a href="/soportes/soportes/${item.id}" class="btn-icon-only"><i class="feather-arrow-right"></i></a></td></tr>`;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                            document.getElementById('table-footer-info').innerText = `Registros mostrados: ${data.actividadReciente.length}`;
                        } else {
                            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">No se encontraron datos en este periodo</td></tr>`;
                            document.getElementById('table-footer-info').innerText = `Registros mostrados: 0`;
                        }

                        renderCharts(data);
                        showToast('Dashboard actualizado correctamente');
                    })
                    .catch(err => { console.error(err); showToast('Error de conexión al servidor', 'error'); })
                    .finally(() => loader.style.display = 'none');
            };

            filterKpi = function(status) {
                document.getElementById('statusFilter').value = status;
                document.querySelectorAll('.kpi-box').forEach(c => c.classList.remove('active-filter'));
                const map = {'':'card-total', '1':'card-status-1', '2':'card-status-2', '3':'card-status-3', '4':'card-status-4'};
                if(map[status]) document.getElementById(map[status]).classList.add('active-filter');
                updateDashboard();
            }

            document.getElementById('applyDateFilters').addEventListener('click', updateDashboard);
            document.getElementById('refreshDashboard').addEventListener('click', updateDashboard);
            
            document.getElementById('clearFiltersBtn').addEventListener('click', () => {
                document.getElementById('priorityFilter').value = "";
                document.getElementById('statusFilter').value = "";
                const s = document.getElementById('filterStartDate'); s.value = s.dataset.default;
                const e = document.getElementById('filterEndDate'); e.value = e.dataset.default;
                filterKpi('');
            });

            document.querySelectorAll('.btn-date-preset').forEach(btn => {
                btn.addEventListener('click', function() {
                    const d = this.dataset.days;
                    const end = new Date();
                    const start = new Date();
                    start.setDate(end.getDate() - d);
                    document.getElementById('filterEndDate').value = end.toISOString().split('T')[0];
                    document.getElementById('filterStartDate').value = start.toISOString().split('T')[0];
                    document.querySelectorAll('.btn-date-preset').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    updateDashboard();
                });
            });

            // Exportar CSV
            exportTableToCSV = function(id) {
                let csv = [];
                let rows = document.querySelectorAll('#'+id+' tr');
                for (let i=0; i<rows.length; i++) {
                    let row = [], cols = rows[i].querySelectorAll("td, th");
                    for (let j=0; j<cols.length-1; j++) { 
                        let txt = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                        row.push('"' + txt.replace(/"/g, '""') + '"');
                    }
                    csv.push(row.join(","));
                }
                const blob = new Blob([csv.join("\n")], {type: "text/csv"});
                const link = document.createElement("a");
                link.download = "reporte_soportes_" + new Date().toISOString().slice(0,10) + ".csv";
                link.href = window.URL.createObjectURL(blob);
                link.click();
            }

            // -------------------------------------------------------------
            // 🖨️ LÓGICA DE EXPORTACIÓN A PDF (HTML2PDF)
            // -------------------------------------------------------------
            document.getElementById('exportDashboard').addEventListener('click', function() {
                const element = document.body; // Tomamos todo el body para simplificar la estructura
                const loader = document.getElementById('loadingOverlay');
                const loaderText = document.getElementById('loaderText');
                
                // 1. Mostrar Loader
                loaderText.innerText = "Generando PDF Profesional...";
                loader.style.display = 'flex';

                // 2. Activar "Modo Reporte" (CSS se encarga de mostrar todo y ocultar filtros)
                element.classList.add('printing-mode');

                // Pequeña pausa para que Chart.js o el DOM se estabilicen antes de la foto
                setTimeout(() => {
                    const opt = {
                        margin:       [5, 5], // Márgenes pequeños (mm)
                        filename:     'Reporte_Gestion_Soportes.pdf',
                        image:        { type: 'jpeg', quality: 0.98 }, // Alta calidad
                        html2canvas:  { scale: 2, useCORS: true, scrollY: 0 }, // Escala x2 para nitidez
                        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }, // Landscape para que quepan las tablas
                        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
                    };

                    // 3. Generar PDF
                    html2pdf().set(opt).from(document.getElementById('dashboard-container')).save().then(() => {
                        // 4. Restaurar vista normal
                        element.classList.remove('printing-mode');
                        loader.style.display = 'none';
                        showToast('Reporte PDF descargado con éxito');
                        
                        // Truco para redibujar la vista correcta de pestañas
                        const activeTab = document.querySelector('.tab-btn.active');
                        if(activeTab) activeTab.click(); 
                    });
                }, 500);
            });
        });
    </script>
    @endpush
</x-base-layout>