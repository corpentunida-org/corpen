{{-- resources/views/inventario/compras/index.blade.php · INV-COM-v5.3-COLORGROUPS --}}
<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $totalMontoSafe     = (float) ($totalMontoFiltrado ?? 0);
        $totalRegistrosSafe = (int) ($totalRegistrosFiltrados ?? 0);
        $promedioCompra     = $totalRegistrosSafe > 0 ? ($totalMontoSafe / $totalRegistrosSafe) : 0;
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
            font-size: 22px;
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
            min-height: 175px;
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

        /* Badges de Método de Pago */
        .badge-efectivo {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .badge-tarjeta {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .badge-cheque {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .badge-default {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 600;
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
                                <li class="breadcrumb-item text-muted">Almacén y Gestión</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Historial de Facturas</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Historial de Compras y Facturación
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Registro maestro de adquisiciones, soportes contables, egresos y proveedores asociados al inventario.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('inventario.compras.create') }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Registrar Compra
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
                    <a href="{{ route('inventario.compras.index') }}" class="corp-nav-pill pill-audit active" title="Control y Seguimiento">
                        <i class="bi bi-receipt-cutoff"></i> Facturas
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

            {{-- 2. DASHBOARD DE KPIs + GRÁFICO DE TENDENCIA --}}
            <div class="row g-3 mb-3 align-items-stretch">
                <div class="col-12 col-lg-6">
                    <div class="row g-3 h-100">
                        <div class="col-12 col-sm-6">
                            <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #1d4ed8;">
                                <div class="min-w-0">
                                    <div class="corp-kpi-label mb-1">Total Gastado (Filtrado)</div>
                                    <div class="corp-kpi-value text-primary font-monospace text-truncate" style="font-size: 20px;" title="${{ number_format($totalMontoSafe, 2) }}">
                                        ${{ number_format($totalMontoSafe, 2) }}
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 11.5px;">
                                        Inversión acumulada
                                    </div>
                                </div>
                                <div class="corp-bod-avatar">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #059669;">
                                <div>
                                    <div class="corp-kpi-label mb-1">Total Registros</div>
                                    <div class="corp-kpi-value text-dark">{{ $totalRegistrosSafe }}</div>
                                    <div class="text-muted mt-1" style="font-size: 11.5px;">
                                        Facturas contabilizadas
                                    </div>
                                </div>
                                <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="corp-kpi d-flex justify-content-between align-items-center py-2 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark" style="font-size: 11px;">PROMEDIO</span>
                                    <span class="text-secondary" style="font-size: 12.5px;">
                                        Ticket promedio por compra filtrada: <strong class="text-dark font-monospace">${{ number_format($promedioCompra, 2) }}</strong>
                                    </span>
                                </div>
                                <span class="text-muted d-none d-sm-inline" style="font-size: 11.5px;">
                                    Corte al {{ now()->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="corp-chart-card">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="corp-kpi-label">Evolución del Gasto por Fecha</span>
                            <span class="text-muted" style="font-size: 11px;">Comportamiento histórico ($)</span>
                        </div>
                        <div style="position: relative; height: 140px; width: 100%;">
                            <canvas id="comprasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PANEL DE FILTROS + TABLA DE COMPRAS --}}
            <div class="corp-panel">
                <form method="GET" action="{{ route('inventario.compras.index') }}" class="corp-filter-bar">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Buscar Proveedor / Factura
                            </label>
                            <div class="position-relative">
                                <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                <input type="text"
                                       name="search"
                                       class="form-control corp-input ps-4"
                                       placeholder="Ej. 90900943..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Fecha Desde
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control corp-input" value="{{ request('fecha_inicio') }}">
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Fecha Hasta
                            </label>
                            <input type="date" name="fecha_fin" class="form-control corp-input" value="{{ request('fecha_fin') }}">
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Método de Pago
                            </label>
                            <select name="metodo_pago" class="form-select corp-select">
                                <option value="">Todos</option>
                                <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ request('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 col-lg-3 d-flex justify-content-md-end gap-2">
                            <a href="{{ route('inventario.compras.index') }}"
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
                                <th class="ps-3" style="width: 145px;">N° Factura</th>
                                <th>Proveedor</th>
                                <th style="width: 115px;">Fecha</th>
                                <th style="width: 155px;">Método Pago</th>
                                <th class="text-center" style="width: 105px;">Egreso</th>
                                <th class="text-end" style="width: 140px;">Total</th>
                                <th style="width: 165px;">Registrado Por</th>
                                <th class="text-end pe-3" style="width: 145px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compras as $compra)
                                <tr>
                                    <td class="ps-3">
                                        <span class="corp-code">{{ $compra->numero_factura }}</span>
                                        @if($compra->num_doc_interno)
                                            <div class="text-muted mt-1" style="font-size: 11px;">Doc: {{ $compra->num_doc_interno }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $compra->proveedor->nom_ter ?? 'Proveedor No Encontrado' }}</div>
                                        <div class="text-muted" style="font-size: 11.5px;">NIT/Cód: {{ $compra->cod_ter_proveedor }}</div>
                                    </td>
                                    <td class="text-secondary fw-semibold">
                                        {{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $metodo = strtolower($compra->metodo->nombre ?? '');
                                            $claseColor = 'badge-default';
                                            if (str_contains($metodo, 'efectivo')) $claseColor = 'badge-efectivo';
                                            elseif (str_contains($metodo, 'tarjeta')) $claseColor = 'badge-tarjeta';
                                            elseif (str_contains($metodo, 'cheque')) $claseColor = 'badge-cheque';
                                        @endphp
                                        <span class="{{ $claseColor }}">{{ $compra->metodo->nombre ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($compra->numero_egreso)
                                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 11.5px;">
                                                {{ $compra->numero_egreso }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-dark">
                                        ${{ number_format($compra->total_pago, 2) }}
                                    </td>
                                    <td>
                                        <span class="text-secondary" style="font-size: 12.5px;">
                                            <i class="bi bi-person-circle me-1 text-muted"></i>{{ $compra->usuarioRegistro->name ?? 'Sistema' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('inventario.compras.show', $compra->id) }}"
                                               class="btn btn-sm btn-light border text-dark"
                                               style="padding: 3px 8px;"
                                               title="Ver Detalle">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('inventario.compras.edit', $compra->id) }}"
                                               class="btn btn-sm btn-light border text-dark"
                                               style="padding: 3px 8px;"
                                               title="Editar Compra">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($compra->eg_archivo)
                                                <a href="{{ route('inventario.compras.archivo', $compra->id) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-light border text-dark"
                                                   style="padding: 3px 8px;"
                                                   title="Ver Soporte Adjunto">
                                                    <i class="bi bi-paperclip text-success"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('inventario.compras.descargar', $compra->id) }}"
                                               class="btn btn-sm btn-light border text-dark"
                                               style="padding: 3px 8px;"
                                               title="Descargar PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-receipt d-block mb-1" style="font-size: 24px;"></i>
                                        No hay compras registradas que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($compras, 'hasPages') && $compras->hasPages())
                    <div class="p-3 border-top bg-white d-flex justify-content-center">
                        {{ $compras->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Script del Gráfico --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var canvas = document.getElementById('comprasChart');
            if (!canvas) return;

            var labelsFechas = @json($chartLabels ?? []);
            var dataTotales  = @json($chartData ?? []);

            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsFechas,
                    datasets: [{
                        label: 'Gasto por Fecha ($)',
                        data: dataTotales,
                        backgroundColor: 'rgba(29, 78, 216, 0.12)',
                        borderColor: '#1d4ed8',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#1d4ed8',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.25
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            bodyFont: { family: 'Inter, system-ui, sans-serif', size: 12 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 10 }, color: '#64748b' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#64748b' }
                        }
                    }
                }
            });
        });
    </script>
</x-base-layout>
