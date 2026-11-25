{{-- resources/views/inventario/tablero/index.blade.php · INV-TAB-v5.3-COLORGROUPS --}}
<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $pctNoAsignados = $totalActivos > 0 ? round(($noAsignados / $totalActivos) * 100) : 0;
        $pctReparacion  = $totalActivos > 0 ? round(($enReparacion / $totalActivos) * 100) : 0;
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

        /* Tarjetas KPI Interactivas */
        .corp-kpi-link {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 14px 16px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
            height: 100%;
        }
        a.corp-kpi-link:hover {
            transform: translateY(-2px);
            border-color: #93c5fd;
            box-shadow: 0 10px 20px -8px rgba(15, 23, 42, 0.1);
            color: inherit;
        }
        .corp-kpi-link.active-card {
            border-color: var(--corp-accent);
            background-color: #f8fbff;
            box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.15);
        }
        .corp-kpi-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--corp-muted);
        }
        .corp-kpi-value {
            font-size: 21px;
            font-weight: 800;
            color: var(--corp-ink);
            line-height: 1.2;
        }
        .corp-bod-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--corp-accent-soft);
            color: var(--corp-accent);
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        /* Paneles corporativos */
        .corp-panel {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .corp-panel-head {
            background: #f8fafc;
            border-bottom: 1px solid var(--corp-border);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .corp-panel-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--corp-ink);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        /* Tabla ejecutiva */
        .corp-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
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
        }
        .corp-table tbody td {
            font-size: 13px;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11.5px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 2px 8px;
            border-radius: 6px;
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
                                <li class="breadcrumb-item text-muted">Inteligencia de Negocios</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Tablero de Control</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Tablero Ejecutivo de Activos
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Resumen en tiempo real del estado del inventario. Selecciona una tarjeta KPI para inspeccionar el detalle de equipos.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('inventario.activos.alertas') }}"
                           class="btn btn-outline-danger fw-semibold px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-bell-fill me-1"></i> Alertas
                        </a>
                        <a href="{{ route('inventario.movimientos.create') }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Movimiento
                        </a>
                    </div>
                </div>

                {{-- Barra Minimalista de Accesos Rápidos con Color por Enfoque --}}
                <div class="corp-quicknav" aria-label="Accesos rápidos de inventario">
                    {{-- GRUPO 1: PRINCIPAL / VISTAS CORE (Azul claro) --}}
                    <a href="{{ route('inventario.tablero') }}" class="corp-nav-pill pill-core active" title="Vista Principal">
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

            {{-- 2. TARJETAS KPI INTERACTIVAS (5 COLUMNAS EN DESKTOP) --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-3">
                <div class="col">
                    <a href="?filtro=todos"
                       class="corp-kpi-link {{ request('filtro') == 'todos' || !request()->has('filtro') ? 'active-card' : '' }}"
                       title="Ver todos los equipos">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="corp-kpi-label">Total Equipos</span>
                            <div class="corp-bod-avatar">
                                <i class="bi bi-pc-display"></i>
                            </div>
                        </div>
                        <div>
                            <div class="corp-kpi-value font-monospace">{{ $totalActivos }}</div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Clic para listar todos</div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <div class="corp-kpi-link" style="cursor: default;">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="corp-kpi-label">Valor Inventario</span>
                            <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <div class="corp-kpi-value text-success font-monospace text-truncate" style="font-size: 19px;" title="${{ number_format($valorInventario, 2) }}">
                                ${{ number_format($valorInventario, 2) }}
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Valoración acumulada</div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <a href="?filtro=asignados"
                       class="corp-kpi-link {{ request('filtro') == 'asignados' ? 'active-card' : '' }}"
                       title="Ver lista de equipos asignados">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="corp-kpi-label">Asignados</span>
                            <div class="corp-bod-avatar" style="background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe;">
                                <i class="bi bi-person-check"></i>
                            </div>
                        </div>
                        <div>
                            <div class="corp-kpi-value font-monospace">
                                {{ $asignados }}
                                <span class="text-muted fw-semibold" style="font-size: 13px;">({{ $porcentajeAsignados }}%)</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">En custodia de usuarios</div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="?filtro=no_asignados"
                       class="corp-kpi-link {{ request('filtro') == 'no_asignados' ? 'active-card' : '' }}"
                       title="Ver lista de equipos no asignados / en bodega">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="corp-kpi-label">No Asignados</span>
                            <div class="corp-bod-avatar" style="background: #ecfeff; color: #0891b2; border-color: #a5f3fc;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                        <div>
                            <div class="corp-kpi-value font-monospace" style="color: #0891b2;">
                                {{ $noAsignados }}
                                <span class="text-muted fw-semibold" style="font-size: 13px;">({{ $pctNoAsignados }}%)</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Disponibles en bodega</div>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="?filtro=reparacion"
                       class="corp-kpi-link {{ request('filtro') == 'reparacion' ? 'active-card' : '' }}"
                       title="Ver lista de equipos en reparación">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="corp-kpi-label">En Reparación</span>
                            <div class="corp-bod-avatar" style="background: #fffbeb; color: #d97706; border-color: #fde68a;">
                                <i class="bi bi-tools"></i>
                            </div>
                        </div>
                        <div>
                            <div class="corp-kpi-value font-monospace" style="color: #d97706;">
                                {{ $enReparacion }}
                                <span class="text-muted fw-semibold" style="font-size: 13px;">({{ $pctReparacion }}%)</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Soporte técnico activo</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- 3. SECCIÓN DINÁMICA: TABLA DE DETALLE AL PULSAR UNA TARJETA KPI --}}
            @if($listaDetalle)
                <div class="corp-panel mb-3" id="seccion-detalle" style="border-color: #93c5fd; box-shadow: 0 8px 20px -8px rgba(29, 78, 216, 0.18);">
                    <div class="corp-panel-head" style="background: #eff6ff; border-bottom-color: #dbeafe;">
                        <h6 class="corp-panel-title text-primary">
                            <i class="bi bi-list-columns-reverse"></i> {{ $tituloDetalle }}
                        </h6>
                        <a href="{{ url()->current() }}"
                           class="btn btn-sm btn-outline-danger fw-semibold px-2 py-1"
                           style="font-size: 12px; border-radius: 7px;">
                            <i class="bi bi-x-circle me-1"></i> Cerrar detalle
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table corp-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width: 160px;">Código Activo</th>
                                    <th>Nombre del Equipo</th>
                                    <th style="width: 170px;">Estado Actual</th>
                                    <th style="width: 145px;">Fecha Registro</th>
                                    <th class="text-end pe-3" style="width: 110px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listaDetalle as $activo)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="corp-code">{{ $activo->codigo_activo }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $activo->nombre }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border" style="font-size: 11.5px;">
                                                {{ $activo->estado->nombre ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-secondary fw-semibold">
                                            {{ $activo->created_at ? $activo->created_at->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('inventario.activos.show', $activo->id) }}"
                                               class="btn btn-sm btn-light border text-dark fw-semibold px-2 py-1"
                                               style="font-size: 12px; border-radius: 7px;"
                                               title="Ver Hoja de Vida">
                                                <i class="bi bi-eye text-primary me-1"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No se encontraron activos en este estado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($listaDetalle->hasPages())
                        <div class="p-3 border-top bg-white d-flex justify-content-center">
                            {{ $listaDetalle->links() }}
                        </div>
                    @endif
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const seccionDetalle = document.getElementById('seccion-detalle');
                        if (seccionDetalle) {
                            seccionDetalle.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                </script>
            @endif

            {{-- 4. GRÁFICO DE DISTRIBUCIÓN + MOVIMIENTOS RECIENTES --}}
            <div class="row g-3 align-items-stretch">
                <div class="col-12 col-lg-4">
                    <div class="corp-panel h-100 d-flex flex-column">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-pie-chart text-primary"></i> Distribución de Estados
                            </h6>
                        </div>
                        <div class="p-3 flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 270px; position: relative;">
                            <canvas id="activosChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="corp-panel h-100 d-flex flex-column">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-clock-history text-primary"></i> Movimientos Recientes
                            </h6>
                            <a href="{{ route('inventario.movimientos.index') }}"
                               class="text-primary fw-bold text-decoration-none"
                               style="font-size: 12px;">
                                Ver Historial Completo <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="table-responsive flex-grow-1">
                            <table class="table corp-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3" style="width: 145px;">Acta</th>
                                        <th>Tipo Registro</th>
                                        <th>Responsable</th>
                                        <th class="text-end pe-3" style="width: 130px;">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimosMovimientos as $mov)
                                        <tr>
                                            <td class="ps-3">
                                                <span class="corp-code">{{ $mov->codigo_acta ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($mov->tipoRegistro)
                                                    @if($mov->tipoRegistro->id == 1)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11.5px;">
                                                            {{ $mov->tipoRegistro->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-dark border" style="font-size: 11.5px;">
                                                            {{ $mov->tipoRegistro->nombre }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-muted border" style="font-size: 11.5px;">Desconocido</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <i class="bi bi-person-circle text-muted me-1"></i>{{ $mov->responsable->name ?? 'Sistema' }}
                                            </td>
                                            <td class="text-end pe-3 text-secondary font-monospace" style="font-size: 12.5px;">
                                                {{ $mov->created_at ? $mov->created_at->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox d-block mb-1" style="font-size: 22px;"></i>
                                                No hay movimientos recientes registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('activosChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const etiquetas = {!! json_encode($labelsGrafica ?? []) !!};
            const valores = {!! json_encode($dataGrafica ?? []) !!};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        data: valores,
                        backgroundColor: [
                            'rgba(29, 78, 216, 0.82)',
                            'rgba(5, 150, 105, 0.82)',
                            'rgba(217, 119, 6, 0.82)',
                            'rgba(220, 38, 38, 0.82)',
                            'rgba(109, 40, 217, 0.82)',
                            'rgba(8, 145, 178, 0.82)',
                            'rgba(100, 116, 139, 0.82)'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 14,
                                usePointStyle: true,
                                font: { family: 'Inter, system-ui, sans-serif', size: 11.5, weight: '600' },
                                color: '#334155',
                                boxWidth: 8
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
</x-base-layout>
