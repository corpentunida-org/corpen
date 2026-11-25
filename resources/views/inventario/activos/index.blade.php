{{-- resources/views/inventario/activos/index.blade.php · INV-ACT-INDEX-v5.3-COLORGROUPS --}}
<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Sistema de Diseño Corporativo & UX Full-Width */
        :root {
            --corp-ink: #0f172a;
            --corp-muted: #64748b;
            --corp-border: #e2e8f0;
            --corp-surface: #ffffff;
            --corp-bg: #f8fafc;
            --corp-accent: #1d4ed8;
            --corp-accent-soft: #eff6ff;

            /* Colores de estado */
            --success-green: #059669;
            --success-green-soft: #ecfdf5;
            --danger-red: #dc2626;
            --danger-red-soft: #fef2f2;
            --warning-yellow: #d97706;
            --warning-yellow-soft: #fffbeb;
        }

        .corp-shell {
            width: 100%;
            max-width: 100%;
            margin: 0;
            color: var(--corp-ink);
        }

        /* Cabecera ejecutiva */
        .corp-header {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        /* Barra minimalista de accesos rápidos por grupo de enfoque */
        .corp-quicknav {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            padding-top: 12px;
            margin-top: 12px;
            border-top: 1px solid #f1f5f9;
            scrollbar-width: none;
        }
        .corp-quicknav::-webkit-scrollbar {
            display: none;
        }
        .corp-nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: all .15s ease;
        }
        .corp-nav-pill i {
            font-size: 13px;
        }
        .corp-nav-sep {
            width: 1px;
            height: 18px;
            background: #e2e8f0;
            margin: 0 2px;
            flex-shrink: 0;
        }

        /* 1. Grupo Principal / Vistas Core (Azul claro) */
        .pill-core {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }
        .pill-core:hover {
            background: #dbeafe;
            color: #1e40af;
            border-color: #bfdbfe;
        }
        .pill-core.active {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
        }

        /* 2. Grupo Operaciones / Altas Rápidas (Verde esmeralda claro) */
        .pill-ops {
            background: #ecfdf5;
            color: #047857;
            border-color: #d1fae5;
        }
        .pill-ops:hover {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        .pill-ops.active {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }

        /* 3. Grupo Control / Bitácoras y Alertas (Ámbar cálido claro) */
        .pill-audit {
            background: #fffbeb;
            color: #b45309;
            border-color: #fef3c7;
        }
        .pill-audit:hover {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }
        .pill-audit.active {
            background: #d97706;
            color: #ffffff;
            border-color: #d97706;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
        }

        /* 4. Grupo Configuración / Catálogos Maestros (Púrpura suave) */
        .pill-conf {
            background: #f5f3ff;
            color: #6d28d9;
            border-color: #ede9fe;
        }
        .pill-conf:hover {
            background: #ede9fe;
            color: #5b21b6;
            border-color: #ddd6fe;
        }
        .pill-conf.active {
            background: #7c3aed;
            color: #ffffff;
            border-color: #7c3aed;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);
        }

        /* Tarjetas de Gráficos Interactivos */
        .corp-chart-card {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            height: 230px;
            display: flex;
            flex-direction: column;
        }
        .corp-chart-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--corp-muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .corp-chart-hint {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: none;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .chart-container {
            position: relative;
            flex: 1 1 auto;
            width: 100%;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Panel principal y barra de filtros */
        .corp-panel {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .corp-filter-bar {
            background: #f8fafc;
            border-bottom: 1px solid var(--corp-border);
            padding: 12px 16px;
        }
        .corp-input, .corp-select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            padding: 7px 11px;
            background-color: #ffffff;
            color: var(--corp-ink);
            transition: all .15s ease;
        }
        .corp-input:focus, .corp-select:focus {
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
            outline: none;
        }

        /* Tabla de Activos */
        #table-container {
            position: relative;
            overflow-x: auto;
        }
        .corp-table {
            width: 100%;
            border-collapse: collapse;
        }
        .corp-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid var(--corp-border);
            padding: 10px 14px;
            white-space: nowrap;
        }
        .corp-table tbody td {
            font-size: 13px;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-table tbody tr:hover td:not(.row-header) {
            background-color: #f8fafc !important;
        }
        .corp-table th.row-header, .corp-table td.row-header {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 11.5px;
            text-align: center;
            width: 48px;
            border-right: 1px solid #f1f5f9;
            user-select: none;
        }

        /* Tonalidades sutiles de fila */
        .row-assigned td:not(.row-header) { background-color: #fafcff; }
        .row-issue td:not(.row-header) { background-color: #fffafa; }

        /* Skeleton Loading */
        .loading .corp-table { opacity: 0.45; pointer-events: none; }
        .loading::after {
            content: "";
            position: absolute;
            top: 38px; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.75), transparent);
            background-size: 200% 100%;
            animation: loading-skeleton 1.4s infinite;
            z-index: 20;
            pointer-events: none;
        }
        @keyframes loading-skeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Badges y códigos */
        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 2px 7px;
            border-radius: 6px;
            display: inline-block;
        }
        .cat-tag {
            font-size: 11px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }

        .st-ok { background: var(--success-green-soft); color: var(--success-green); border-color: #a7f3d0; }
        .st-ok .status-dot { background: var(--success-green); }

        .st-busy { background: var(--corp-accent-soft); color: var(--corp-accent); border-color: #dbeafe; }
        .st-busy .status-dot { background: var(--corp-accent); }

        .st-bad { background: var(--danger-red-soft); color: var(--danger-red); border-color: #fecaca; }
        .st-bad .status-dot { background: var(--danger-red); }

        .st-fix { background: var(--warning-yellow-soft); color: var(--warning-yellow); border-color: #fde68a; }
        .st-fix .status-dot { background: var(--warning-yellow); }

        /* Paginación */
        .pagination { margin-bottom: 0; }
        .pagination .page-link {
            font-size: 12.5px;
            color: #334155;
            border-color: #e2e8f0;
        }
        .pagination .page-item.active .page-link {
            background-color: #0f172a;
            border-color: #0f172a;
            color: #ffffff;
        }

        @media print {
            .corp-quicknav, .dashboard-section, .corp-filter-bar, .toolbar-actions, .pagination-container, th:last-child, td:last-child { display: none !important; }
            .corp-panel { border: none !important; box-shadow: none !important; }
        }
    </style>

    <div class="container-fluid py-2 px-2 px-md-3">
        <div class="corp-shell">

            {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
            <div class="corp-header mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <nav aria-label="breadcrumb" class="mb-1">
                            <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                <li class="breadcrumb-item text-muted">Almacén y Gestión</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Almacén de Activos</li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0" style="letter-spacing: -0.02em; color: #0f172a;">
                                Almacén de Activos
                            </h4>
                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-2" style="font-size: 11.5px; padding: 5px 10px;">
                                <span style="width: 7px; height: 7px; border-radius: 50%; background: #1d4ed8; display: inline-block;"></span>
                                <span id="total-count">{{ $activos->total() }}</span> registros
                            </span>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Control centralizado de equipos, ubicación en bodega, condición física y asignación a responsables.
                        </p>
                    </div>

                    <div class="toolbar-actions d-flex align-items-center gap-2 flex-wrap">
                        <a href="#"
                           id="btn-generate-pdf"
                           class="btn btn-light border fw-semibold text-dark px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;"
                           title="Descargar como PDF">
                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                        </a>
                        <a href="#"
                           id="btn-generate-excel"
                           class="btn btn-light border fw-semibold text-dark px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;"
                           title="Descargar como Excel">
                            <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                        </a>
                        <a href="{{ route('inventario.compras.create') }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Ingreso
                        </a>
                    </div>
                </div>

                {{-- Barra Minimalista de Accesos Rápidos con Color por Enfoque --}}
                <div class="corp-quicknav" aria-label="Accesos rápidos de inventario">
                    {{-- GRUPO 1: PRINCIPAL / VISTAS CORE (Azul claro) --}}
                    <a href="{{ route('inventario.tablero') }}" class="corp-nav-pill pill-core" title="Vista Principal">
                        <i class="bi bi-speedometer2"></i> Tablero
                    </a>
                    <a href="{{ route('inventario.activos.index') }}" class="corp-nav-pill pill-core active" title="Vista Principal">
                        <i class="bi bi-laptop-fill"></i> Activos
                    </a>

                    <span class="corp-nav-sep"></span>

                    {{-- GRUPO 2: OPERACIONES / ALTAS (Verde esmeralda claro) --}}
                    <a href="{{ route('inventario.compras.create') }}" class="corp-nav-pill pill-ops" title="Operaciones">
                        <i class="bi bi-cart-plus"></i> + Compra
                    </a>
                    <a href="{{ route('inventario.movimientos.create') }}" class="corp-nav-pill pill-ops" title="Operaciones">
                        <i class="bi bi-file-earmark-arrow-up"></i> + Acta / Mov.
                    </a>
                    <a href="{{ route('inventario.mantenimientos.create') }}" class="corp-nav-pill pill-ops" title="Operaciones">
                        <i class="bi bi-wrench-adjustable"></i> + Mantenimiento
                    </a>

                    <span class="corp-nav-sep"></span>

                    {{-- GRUPO 3: CONTROL / BITÁCORAS Y ALERTAS (Ámbar cálido claro) --}}
                    <a href="{{ route('inventario.movimientos.index') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                        <i class="bi bi-arrow-left-right"></i> Movimientos
                    </a>
                    <a href="{{ route('inventario.compras.index') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                        <i class="bi bi-receipt"></i> Facturas
                    </a>
                    <a href="{{ route('inventario.activos.alertas') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                        <i class="bi bi-exclamation-triangle"></i> Alertas
                    </a>

                    <span class="corp-nav-sep"></span>

                    {{-- GRUPO 4: CONFIGURACIÓN / CATÁLOGOS (Púrpura suave) --}}
                    <a href="{{ route('inventario.bodegas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-geo-alt"></i> Bodegas
                    </a>
                    <a href="{{ route('inventario.clasificacion.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-diagram-3"></i> Clasificación
                    </a>
                    <a href="{{ route('inventario.marcas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-tags"></i> Marcas
                    </a>
                    <a href="{{ route('inventario.metodos-pago.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-credit-card"></i> Métodos Pago
                    </a>
                </div>
            </div>

            {{-- 2. DASHBOARD DE GRÁFICOS INTERACTIVOS --}}
            <div class="dashboard-section row g-3 mb-3">
                <div class="col-12 col-lg-4">
                    <div class="corp-chart-card">
                        <div class="corp-chart-title">
                            <span><i class="bi bi-heart-pulse text-primary me-1"></i> Estado Físico</span>
                            <span class="corp-chart-hint">Clic para filtrar</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartEstados"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="corp-chart-card">
                        <div class="corp-chart-title">
                            <span><i class="bi bi-geo-alt text-primary me-1"></i> Distribución por Bodega</span>
                            <span class="corp-chart-hint">Clic para filtrar</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartBodegas"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="corp-chart-card">
                        <div class="corp-chart-title">
                            <span><i class="bi bi-diagram-3 text-primary me-1"></i> Top Categorías</span>
                            <span class="corp-chart-hint">Clic para filtrar</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="chartCategorias"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PANEL DE FILTROS AJAX + TABLA DE ACTIVOS --}}
            <div class="corp-panel">
                <form id="live-search-form" action="{{ route('inventario.activos.index') }}" method="GET" class="corp-filter-bar">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Búsqueda Inteligente
                            </label>
                            <div class="position-relative">
                                <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                <input type="text"
                                       name="search"
                                       class="form-control corp-input ps-4"
                                       placeholder="Placa, serial, nombre o responsable..."
                                       value="{{ request('search') }}"
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="col-12 col-sm-4 col-lg-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Ubicación
                            </label>
                            <select name="bodega_id" id="bodega_id" class="form-select corp-select">
                                <option value="">Todas las Bodegas</option>
                                @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}" {{ request('bodega_id') == $bodega->id ? 'selected' : '' }}>{{ $bodega->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-4 col-lg-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Condición
                            </label>
                            <select name="estado_id" id="estado_id" class="form-select corp-select">
                                <option value="">Todos los Estados</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ request('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-4 col-lg-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Clasificación
                            </label>
                            <select name="subgrupo_id" id="subgrupo_id" class="form-select corp-select">
                                <option value="">Todas</option>
                                @foreach($subgrupos as $subgrupo)
                                    <option value="{{ $subgrupo->id }}" {{ request('subgrupo_id') == $subgrupo->id ? 'selected' : '' }}>{{ $subgrupo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-lg-2 d-flex justify-content-lg-end">
                            <a href="javascript:void(0)"
                               id="btn-reset"
                               class="btn btn-outline-danger fw-semibold w-100"
                               style="font-size: 12.5px; border-radius: 8px; {{ request()->hasAny(['search', 'bodega_id', 'estado_id', 'subgrupo_id']) ? 'display: inline-flex; align-items: center; justify-content: center;' : 'display: none;' }}">
                                <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                            </a>
                        </div>
                    </div>
                </form>

                <div id="table-container">
                    <table class="table corp-table mb-0">
                        <thead>
                            <tr>
                                <th class="row-header">Nº</th>
                                <th>Descripción y Categoría</th>
                                <th>Identificadores</th>
                                <th>Sede / Bodega</th>
                                <th>Estado</th>
                                <th>Asignación</th>
                                <th style="width: 100px; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activos as $index => $activo)
                                @php
                                    $rowClass = 'row-available';
                                    if(in_array($activo->id_Estado, [3, 4])) $rowClass = 'row-issue';
                                    elseif($activo->usuarioAsignado) $rowClass = 'row-assigned';

                                    $statusClass = match($activo->id_Estado) {
                                        1 => 'st-ok',
                                        2 => 'st-busy',
                                        3 => 'st-bad',
                                        4 => 'st-fix',
                                        default => 'st-busy'
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="row-header">{{ $activos->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $activo->nombre }}</div>
                                        <span class="cat-tag">{{ $activo->subgrupo->nombre ?? 'Sin categoría' }}</span>
                                    </td>
                                    <td>
                                        <div class="corp-code mb-1">{{ $activo->codigo_activo }}</div>
                                        <div class="text-muted" style="font-size: 11.5px;">
                                            SN: <span class="font-monospace">{{ $activo->serial ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-secondary fw-semibold">
                                        {{ $activo->referencia->bodega->nombre ?? 'Sin Bodega' }}
                                    </td>
                                    <td>
                                        <div class="status-pill {{ $statusClass }}">
                                            <span class="status-dot"></span>
                                            {{ $activo->estado->nombre ?? 'N/D' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($activo->usuarioAsignado)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                     style="width: 24px; height: 24px; background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; font-size: 11px; flex-shrink: 0;">
                                                    {{ strtoupper(substr($activo->usuarioAsignado->name, 0, 1)) }}
                                                </div>
                                                <span class="text-dark fw-semibold" style="font-size: 12.5px;">{{ $activo->usuarioAsignado->name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px; font-weight: 700;">
                                                DISPONIBLE
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('inventario.activos.show', $activo->id) }}"
                                               class="btn btn-sm btn-light border text-dark"
                                               style="padding: 3px 8px;"
                                               title="Ver Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('inventario.activos.edit', $activo->id) }}"
                                               class="btn btn-sm btn-light border text-primary"
                                               style="padding: 3px 8px;"
                                               title="Editar Activo">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox d-block mb-1" style="font-size: 26px;"></i>
                                        <div class="fw-bold text-dark">Sin registros</div>
                                        <div style="font-size: 12.5px;">No hay activos que coincidan con los filtros aplicados.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($activos->hasPages())
                    <div class="pagination-container p-3 border-top bg-white d-flex justify-content-center">
                        {{ $activos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // 1. CONFIGURACIÓN DEL FILTRO AJAX
            // ==========================================
            const form = document.getElementById('live-search-form');
            const tableContainer = document.getElementById('table-container');
            const btnReset = document.getElementById('btn-reset');
            let timeout = null;

            function fetchResults() {
                const url = new URL(form.action);
                const formData = new FormData(form);
                url.search = new URLSearchParams(formData).toString();

                const filtersActive = Array.from(formData.values()).some(val => val !== '');
                btnReset.style.display = filtersActive ? 'inline-flex' : 'none';
                if (filtersActive) {
                    btnReset.style.alignItems = 'center';
                    btnReset.style.justifyContent = 'center';
                }

                tableContainer.classList.add('loading');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    tableContainer.innerHTML = doc.getElementById('table-container').innerHTML;
                    document.getElementById('total-count').innerText = doc.getElementById('total-count').innerText;

                    tableContainer.classList.remove('loading');
                    window.history.pushState({}, '', url);
                })
                .catch(err => {
                    console.error("Error cargando datos:", err);
                    tableContainer.classList.remove('loading');
                });
            }

            form.querySelectorAll('input, select').forEach(el => {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchResults, el.tagName === 'INPUT' ? 400 : 0);
                });
            });

            btnReset.addEventListener('click', () => {
                form.reset();
                form.querySelectorAll('select').forEach(s => s.value = '');
                fetchResults();
            });

            form.addEventListener('submit', (e) => e.preventDefault());


            // ==========================================
            // 2. INICIALIZACIÓN DE GRÁFICOS (CHART.JS)
            // ==========================================
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#64748b';

            const tooltipConfig = {
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 6,
                titleFont: { size: 12, weight: '600' },
                bodyFont: { size: 12 },
                displayColors: true,
                boxPadding: 4
            };

            const rawEstados = @json($statsEstados ?? []);
            const rawBodegas = @json($statsBodegas ?? []);
            const rawCategorias = @json($statsCategorias ?? []);

            // --- GRÁFICO 1: ESTADOS ---
            if(document.getElementById('chartEstados') && rawEstados.length > 0) {
                const ctxEstados = document.getElementById('chartEstados').getContext('2d');
                new Chart(ctxEstados, {
                    type: 'doughnut',
                    data: {
                        labels: rawEstados.map(e => e.label),
                        datasets: [{
                            data: rawEstados.map(e => e.count),
                            backgroundColor: rawEstados.map(e => e.color),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4,
                            cutout: '70%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: 8 },
                        plugins: {
                            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
                            tooltip: tooltipConfig
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('estado_id').value = rawEstados[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // --- GRÁFICO 2: BODEGAS ---
            if(document.getElementById('chartBodegas') && rawBodegas.length > 0) {
                const ctxBodegas = document.getElementById('chartBodegas').getContext('2d');
                new Chart(ctxBodegas, {
                    type: 'bar',
                    data: {
                        labels: rawBodegas.map(b => b.label ?? ('Bodega ' + b.id)),
                        datasets: [{
                            label: 'Activos',
                            data: rawBodegas.map(b => b.count),
                            backgroundColor: '#1d4ed8',
                            borderRadius: 4,
                            barThickness: 'flex',
                            maxBarThickness: 22
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: { top: 6, bottom: 6, right: 12 } },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: {
                            x: { display: false, grid: { display: false } },
                            y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('bodega_id').value = rawBodegas[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // --- GRÁFICO 3: CATEGORÍAS ---
            if(document.getElementById('chartCategorias') && rawCategorias.length > 0) {
                const ctxCategorias = document.getElementById('chartCategorias').getContext('2d');
                new Chart(ctxCategorias, {
                    type: 'bar',
                    data: {
                        labels: rawCategorias.map(c => c.label),
                        datasets: [{
                            data: rawCategorias.map(c => c.count),
                            backgroundColor: rawCategorias.map(c => c.color),
                            borderRadius: 4,
                            maxBarThickness: 28
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: { top: 8 } },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: {
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 45 } },
                            y: { display: false, grid: { display: false } }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('subgrupo_id').value = rawCategorias[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // Botones de Exportación
            document.getElementById('btn-generate-pdf').addEventListener('click', function(e) {
                e.preventDefault();
                const searchParams = new URLSearchParams(new FormData(form)).toString();
                window.location.href = "{{ route('inventario.activos.pdf') }}?" + searchParams;
            });

            document.getElementById('btn-generate-excel').addEventListener('click', function(e) {
                e.preventDefault();
                const searchParams = new URLSearchParams(new FormData(form)).toString();
                window.location.href = "{{ route('inventario.activos.excel') }}?" + searchParams;
            });
        });
    </script>
</x-base-layout>
