{{-- resources/views/inventario/activos/alertas.blade.php · INV-ALT-v5.3-COLORGROUPS --}}
<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $totalVencidosSafe  = (int) ($totalVencidos ?? 0);
        $totalPorVencerSafe = (int) ($totalPorVencer ?? 0);
        $totalAlertasGlobal = $totalVencidosSafe + $totalPorVencerSafe;
        $porcVencidas       = $totalAlertasGlobal > 0 ? round(($totalVencidosSafe / $totalAlertasGlobal) * 100) : 0;
    @endphp

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

        /* KPIs y Tarjeta de Gráfico */
        .corp-kpi {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 14px 18px;
            transition: border-color .15s ease;
        }
        .corp-kpi:hover {
            border-color: #cbd5e1;
        }
        .corp-kpi-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--corp-muted);
        }
        .corp-kpi-value {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
        }
        .corp-bod-avatar {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--corp-accent-soft);
            color: var(--corp-accent);
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .corp-chart-card {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 14px 18px;
            height: 100%;
            min-height: 170px;
            display: flex;
            flex-direction: column;
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
            transition: all .15s ease;
        }
        .corp-input:focus, .corp-select:focus {
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        /* Tabla ejecutiva */
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
            white-space: nowrap;
        }
        .corp-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #334155;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .activo-link {
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
            transition: color .15s ease;
        }
        .activo-link:hover {
            color: var(--corp-accent);
            text-decoration: underline;
        }

        /* Badges de alerta */
        .badge-vencida {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .badge-por-vencer {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Paginación */
        .pagination {
            margin-bottom: 0;
        }
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
                                <li class="breadcrumb-item text-muted">Técnico y Alertas</li>
                                <li class="breadcrumb-item active fw-semibold text-danger" aria-current="page">Alertas de Garantía</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Alertas de Garantía y Vigencia Técnica
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Control de vigencia técnica y vencimientos de cobertura para los equipos de la organización.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('inventario.activos.index') }}"
                           class="btn btn-outline-secondary fw-semibold px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-arrow-left me-1"></i> Volver a Activos
                        </a>
                    </div>
                </div>

                {{-- Barra Minimalista de Accesos Rápidos con Color por Enfoque --}}
                <div class="corp-quicknav" aria-label="Accesos rápidos de inventario">
                    {{-- GRUPO 1: PRINCIPAL / VISTAS CORE (Azul claro) --}}
                    <a href="{{ route('inventario.tablero') }}" class="corp-nav-pill pill-core" title="Vista Principal">
                        <i class="bi bi-speedometer2"></i> Tablero
                    </a>
                    <a href="{{ route('inventario.activos.index') }}" class="corp-nav-pill pill-core" title="Vista Principal">
                        <i class="bi bi-laptop"></i> Activos
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
                    <a href="{{ route('inventario.activos.alertas') }}" class="corp-nav-pill pill-audit active" title="Control y Seguimiento">
                        <i class="bi bi-exclamation-triangle-fill"></i> Alertas
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

            {{-- Mostrar Dashboard solo si hay alertas en total o búsqueda activa --}}
            @if($totalVencidosSafe > 0 || $totalPorVencerSafe > 0 || request('search') || request('estado_garantia'))

                {{-- 2. DASHBOARD DE KPIs + GRÁFICO DE DONA --}}
                <div class="row g-3 mb-3 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="row g-3 h-100">
                            <div class="col-12 col-sm-6">
                                <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #dc2626;">
                                    <div>
                                        <div class="corp-kpi-label mb-1">Garantías Vencidas</div>
                                        <div class="corp-kpi-value text-danger">{{ $totalVencidosSafe }}</div>
                                        <div class="text-muted mt-1" style="font-size: 11.5px;">
                                            {{ $porcVencidas }}% del total de alertas
                                        </div>
                                    </div>
                                    <div class="corp-bod-avatar" style="background: #fef2f2; color: #dc2626; border-color: #fecaca;">
                                        <i class="bi bi-shield-x"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #d97706;">
                                    <div>
                                        <div class="corp-kpi-label mb-1">Por Vencer (menos de 30 días)</div>
                                        <div class="corp-kpi-value" style="color: #d97706;">{{ $totalPorVencerSafe }}</div>
                                        <div class="text-muted mt-1" style="font-size: 11.5px;">
                                            Requieren revisión preventiva
                                        </div>
                                    </div>
                                    <div class="corp-bod-avatar" style="background: #fffbeb; color: #d97706; border-color: #fde68a;">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="corp-kpi d-flex justify-content-between align-items-center py-2 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark" style="font-size: 11px;">RESUMEN</span>
                                        <span class="text-secondary" style="font-size: 12.5px;">
                                            Total de equipos monitoreados con alerta activa: <strong class="text-dark">{{ $totalAlertasGlobal }}</strong>
                                        </span>
                                    </div>
                                    <span class="text-muted d-none d-sm-inline" style="font-size: 11.5px;">
                                        Corte al {{ now()->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="corp-chart-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="corp-kpi-label">Distribución de Vigencia</span>
                                <span class="text-muted" style="font-size: 11px;">Vencidas vs. Por vencer</span>
                            </div>
                            <div style="position: relative; height: 135px; width: 100%;">
                                <canvas id="alertasChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. PANEL DE FILTROS + TABLA DE ALERTAS --}}
                <div class="corp-panel">
                    <form method="GET" action="{{ route('inventario.activos.alertas') }}" class="corp-filter-bar">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5 col-lg-4">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                    Buscar Equipo / Código
                                </label>
                                <div class="position-relative">
                                    <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                    <input type="text"
                                           name="search"
                                           class="form-control corp-input ps-4"
                                           placeholder="Ej. PC-001, Dell, Portátil..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                    Estado de Garantía
                                </label>
                                <select name="estado_garantia" class="form-select corp-select">
                                    <option value="">Todas las alertas</option>
                                    <option value="vencido" {{ request('estado_garantia') == 'vencido' ? 'selected' : '' }}>Solo Vencidas</option>
                                    <option value="por_vencer" {{ request('estado_garantia') == 'por_vencer' ? 'selected' : '' }}>Próximas a vencer</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3 col-lg-5 d-flex justify-content-md-end gap-2">
                                <a href="{{ route('inventario.activos.alertas') }}"
                                   class="btn btn-light border fw-semibold px-3"
                                   style="font-size: 13px; border-radius: 8px;"
                                   title="Limpiar Filtros">
                                    <i class="bi bi-eraser me-1"></i> Limpiar
                                </a>
                                <button type="submit"
                                        class="btn btn-primary fw-semibold px-4"
                                        style="font-size: 13px; border-radius: 8px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                    <i class="bi bi-funnel me-1"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table corp-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Activo</th>
                                    <th style="width: 140px;">Código</th>
                                    <th style="width: 155px;">Fecha Fin Garantía</th>
                                    <th style="width: 155px;">Estado Alerta</th>
                                    <th style="width: 165px;">Tiempo</th>
                                    <th style="width: 165px;">Estado Físico</th>
                                    <th class="text-center pe-3" style="width: 110px;">Expediente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alertas as $activo)
                                    @php
                                        $fechaGarantia = \Carbon\Carbon::parse($activo->fecha_fin_garantia);
                                        $isVencido     = $fechaGarantia->isPast();
                                        $dias          = now()->diffInDays($fechaGarantia, false);
                                        $diasAbs       = abs((int) $dias);
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('inventario.activos.show', $activo->id) }}" class="activo-link" title="Ver hoja de vida">
                                                {{ $activo->nombre }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="corp-code">{{ $activo->codigo_activo }}</span>
                                        </td>
                                        <td class="text-secondary fw-semibold">
                                            {{ $fechaGarantia->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($isVencido)
                                                <span class="badge-vencida">
                                                    <i class="bi bi-x-circle-fill"></i> Vencida
                                                </span>
                                            @else
                                                <span class="badge-por-vencer">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Por Vencer
                                                </span>
                                            @endif
                                        </td>
                                        <td class="font-monospace fw-semibold {{ $isVencido ? 'text-danger' : 'text-warning' }}" style="font-size: 12.5px;">
                                            @if($isVencido)
                                                Hace {{ $diasAbs }} días
                                            @else
                                                Faltan {{ $diasAbs }} días
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border" style="font-size: 11.5px;">
                                                {{ $activo->estado->nombre ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <a href="{{ route('inventario.activos.show', $activo->id) }}"
                                               class="btn btn-sm btn-light border text-dark fw-semibold px-2 py-1"
                                               style="font-size: 12px; border-radius: 7px;"
                                               title="Abrir expediente">
                                                <i class="bi bi-folder2-open text-primary me-1"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-search d-block mb-1" style="font-size: 20px;"></i>
                                            No se encontraron resultados para los filtros aplicados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($alertas, 'hasPages') && $alertas->hasPages())
                        <div class="p-3 border-top bg-white d-flex justify-content-center">
                            {{ $alertas->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            @else
                {{-- ESTADO VACÍO GLOBAL --}}
                <div class="text-center py-5 bg-white border rounded-3" style="border-color: #e2e8f0 !important;">
                    <div class="corp-bod-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 26px; background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">¡Todo bajo control!</h5>
                    <p class="text-secondary mb-0" style="font-size: 13.5px;">
                        No se encontraron equipos con garantías vencidas o próximas a vencer.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Script del Gráfico de Dona Chart.js --}}
    @if($totalVencidosSafe > 0 || $totalPorVencerSafe > 0)
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var canvas = document.getElementById('alertasChart');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');

            var dataVencidos = {{ $totalVencidosSafe }};
            var dataPorVencer = {{ $totalPorVencerSafe }};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Vencidas', 'Por Vencer'],
                    datasets: [{
                        data: [dataVencidos, dataPorVencer],
                        backgroundColor: [
                            'rgba(220, 38, 38, 0.82)',
                            'rgba(217, 119, 6, 0.82)'
                        ],
                        borderColor: [
                            '#dc2626',
                            '#d97706'
                        ],
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { family: 'Inter, system-ui, sans-serif', size: 12, weight: '600' },
                                color: '#334155',
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 14
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            bodyFont: { family: 'Inter, system-ui, sans-serif', size: 12 }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</x-base-layout>
