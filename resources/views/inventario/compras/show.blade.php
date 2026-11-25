{{-- resources/views/inventario/compras/show.blade.php · INV-COM-SHOW-v5.3-COLORGROUPS --}}
<x-base-layout>
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
        .corp-panel-body {
            padding: 18px 20px;
        }

        /* Metadatos y fichas de información */
        .corp-meta-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 12px 14px;
            height: 100%;
        }
        .corp-meta-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--corp-muted);
            display: block;
            margin-bottom: 4px;
        }
        .corp-meta-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--corp-ink);
        }

        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 2px 8px;
            border-radius: 6px;
        }

        /* Tabla ejecutiva */
        .corp-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
        }
        .corp-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid var(--corp-border);
            padding: 11px 14px;
            white-space: nowrap;
        }
        .corp-table tbody td {
            font-size: 13px;
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .row-adjustment {
            background-color: #fffbeb !important;
        }

        .help-cursor {
            cursor: help;
            border-bottom: 1px dotted #94a3b8;
            display: inline-block;
        }

        /* Pie de resumen financiero */
        .corp-summary-footer {
            background: #f8fafc;
            border-top: 1px solid var(--corp-border);
            padding: 16px 20px;
        }
        .corp-total-card {
            background: #0f172a;
            color: #ffffff;
            border-radius: 12px;
            padding: 16px 22px;
            text-align: right;
            min-width: 280px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }
        .corp-total-amount {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.15;
            display: block;
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
                                <li class="breadcrumb-item text-muted">Historial de Facturas</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Factura #{{ $compra->numero_factura }}</li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0" style="letter-spacing: -0.02em; color: #0f172a;">
                                Detalle de Compra
                            </h4>
                            <span class="corp-code">#{{ $compra->numero_factura }}</span>
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Comprobante de ingreso al inventario, datos fiscales del proveedor y desglose de artículos adquiridos.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('inventario.compras.index') }}"
                           class="btn btn-outline-secondary fw-semibold px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>

                        @if($compra->eg_archivo)
                            <a href="{{ route('inventario.compras.archivo', $compra->id) }}"
                               target="_blank"
                               class="btn btn-outline-warning text-dark fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px; background-color: #fffbeb; border-color: #fcd34d;">
                                <i class="bi bi-cloud-arrow-down me-1 text-warning"></i> Soporte (AWS)
                            </a>
                        @endif

                        <a href="{{ route('inventario.compras.edit', $compra->id) }}"
                           class="btn btn-light border fw-semibold text-dark px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-pencil me-1 text-primary"></i> Editar
                        </a>

                        <a href="{{ route('inventario.compras.descargar', $compra->id) }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF
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

            {{-- 2. TARJETAS SUPERIORES: PROVEEDOR & FACTURACIÓN EN PARALELO --}}
            <div class="row g-3 mb-3">
                {{-- INFORMACIÓN DEL PROVEEDOR --}}
                <div class="col-12 col-lg-5">
                    <div class="corp-panel h-100">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-building text-primary"></i> Información del Proveedor
                            </h6>
                        </div>
                        <div class="corp-panel-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Razón Social / Nombre</span>
                                        <span class="corp-meta-value">{{ $compra->proveedor->nom_ter ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">NIT / Documento</span>
                                        <span class="corp-meta-value font-monospace" style="font-size: 13px;">{{ $compra->cod_ter_proveedor }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Teléfono</span>
                                        <span class="corp-meta-value" style="font-size: 13px;">{{ $compra->proveedor->tel ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATOS DE FACTURACIÓN --}}
                <div class="col-12 col-lg-7">
                    <div class="corp-panel h-100">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-receipt text-primary"></i> Datos de Facturación
                            </h6>
                            <span class="text-muted" style="font-size: 11.5px;">
                                ID Registro: <strong class="text-dark">#{{ $compra->id }}</strong>
                            </span>
                        </div>
                        <div class="corp-panel-body">
                            <div class="row g-2">
                                <div class="col-6 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">N° de Factura</span>
                                        <span class="corp-meta-value text-primary font-monospace">{{ $compra->numero_factura }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Fecha de Emisión</span>
                                        <span class="corp-meta-value">{{ $compra->fecha_factura->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Método de Pago</span>
                                        <span class="corp-meta-value">{{ $compra->metodo->nombre ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">N° Egreso</span>
                                        <span class="corp-meta-value font-monospace" style="font-size: 13px;">{{ $compra->numero_egreso ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. ARTÍCULOS Y CARGOS DE LA COMPRA --}}
            <div class="corp-panel mb-3">
                <div class="corp-panel-head">
                    <h6 class="corp-panel-title">
                        <i class="bi bi-box-seam text-primary"></i> Artículos y Cargos de la Compra
                    </h6>
                    <span class="badge bg-light text-dark border" style="font-size: 11.5px;">
                        {{ $compra->detalles->count() }} {{ $compra->detalles->count() === 1 ? 'ítem registrado' : 'ítems registrados' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="corp-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 text-center" style="width: 55px;">#</th>
                                <th>Referencia / Producto</th>
                                <th>Detalle Registro</th>
                                <th class="text-center" style="width: 100px;">Cant.</th>
                                <th class="text-end" style="width: 165px;">Precio Unit.</th>
                                <th class="text-end pe-3" style="width: 175px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($compra->detalles as $index => $item)
                                <tr class="{{ $item->cantidades <= 0 ? 'row-adjustment' : '' }}">
                                    <td class="ps-3 text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="help-cursor" title="Detalle Técnico: {{ $item->referencia?->detalle ?? 'Sin especificación' }}">
                                            <div class="fw-bold text-dark text-uppercase" style="font-size: 12.5px;">
                                                {{ $item->referencia?->subgrupo?->nombre ?? 'CARGO ADICIONAL / AJUSTE' }}
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 11.5px;">
                                                @if($item->invReferencias_id)
                                                    <span class="badge bg-light text-dark border font-monospace me-1" style="font-size: 10.5px;">ID: {{ $item->invReferencias_id }}</span>
                                                    <strong class="text-dark">{{ $item->referencia?->referencia ?? 'S/R' }}</strong>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 10.5px;">Sin Referencia de Inventario</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary">{{ $item->detalle ?: '—' }}</td>
                                    <td class="text-center fw-bold text-dark">{{ $item->cantidades }}</td>
                                    <td class="text-end font-monospace text-secondary">
                                        ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end pe-3 font-monospace fw-bold text-dark" style="font-size: 13.5px;">
                                        ${{ number_format($item->sub_total, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox d-block mb-1" style="font-size: 22px;"></i>
                                        No se encontraron artículos asociados a esta compra.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pie de Totalización --}}
                <div class="corp-summary-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-muted" style="font-size: 12px;">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Las filas resaltadas en tono ámbar corresponden a cargos adicionales o ajustes contables (cantidad 0).
                        </div>

                        <div class="corp-total-card ms-auto">
                            <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;">
                                Valor Total Factura
                            </span>
                            <span class="corp-total-amount">${{ number_format($compra->total_pago, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
