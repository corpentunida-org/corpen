{{-- resources/views/inventario/activos/show.blade.php · INV-ACT-SHOW-v6.0-TRACE360 --}}
<x-base-layout>
    @php
        $detCompra   = $activo->detalleCompra ?? $activo->invDetalleCompra ?? null;
        $compra      = $detCompra->compra ?? $detCompra->invCompra ?? $activo->compra ?? null;
        $metodoPago  = $compra->metodo->nombre ?? $compra->invMetodo->nombre ?? null;
        $proveedor   = $compra->proveedor->nom_ter ?? $compra->proveedor->razon_soc ?? $compra->cod_ter_proveedor ?? null;

        $ref         = $activo->referencia ?? $activo->invReferencia ?? null;
        $bodega      = $ref->bodega ?? $ref->invBodega ?? $activo->bodega ?? null;
        $marca       = $activo->marca->nombre ?? $ref->marca->nombre ?? 'N/A';
        $subgrupoObj = $activo->subgrupo ?? $activo->invSubgrupo ?? $ref->subgrupo ?? null;
        $subgrupo    = $subgrupoObj->nombre ?? 'N/A';
        $grupo       = $subgrupoObj->grupo->nombre ?? $subgrupoObj->invGrupo->nombre ?? '—';
        $linea       = $subgrupoObj->grupo->linea->nombre ?? $subgrupoObj->linea->nombre ?? $subgrupoObj->invLinea->nombre ?? '—';
        $tipoCat     = $subgrupoObj->grupo->linea->tipo->nombre ?? $subgrupoObj->linea->tipo->nombre ?? $subgrupoObj->tipo->nombre ?? $subgrupoObj->invTipo->nombre ?? '—';

        $movimientosCol = collect($activo->movimientos ?? $activo->invMovimientos ?? []);
        $mantsCol       = collect(
            $activo->mantenimientos
                ?? $activo->invMantenimientos
                ?? ($mantenimientos ?? [])
        );

        // Construimos la cronología unificada de trazabilidad (Movimientos + Mantenimientos + Compra)
        $timelineEventos = collect();

        foreach ($movimientosCol as $mov) {
            $fechaRaw = $mov->created_at ?? $mov->fecha ?? now();
            $ts       = \Carbon\Carbon::parse($fechaRaw)->timestamp;
            $detallesMov = collect($mov->detalles ?? $mov->movimientoDetalles ?? $mov->invMovimientoDetalles ?? []);
            $detalleItem = $detallesMov->first(function ($d) use ($activo) {
                return (int) ($d->id_InvActivos ?? $d->inv_activo_id ?? $d->activo_id ?? 0) === (int) $activo->id;
            }) ?? $detallesMov->first();

            $timelineEventos->push([
                'categoria'   => 'movimiento',
                'ts'          => $ts,
                'fecha_texto' => \Carbon\Carbon::parse($fechaRaw)->format('d M Y - h:i A'),
                'titulo'      => $mov->tipoRegistro->nombre ?? $mov->tipo->nombre ?? 'Movimiento de Activo',
                'codigo'      => $mov->codigo_acta ? ('Acta #' . $mov->codigo_acta) : ('MOV-' . ($mov->id ?? '')),
                'responsable' => $mov->responsable->name ?? $mov->usuarioAsignado->name ?? 'N/A',
                'creador'     => $mov->creador->name ?? $mov->usuarioRegistro->name ?? 'Sistema',
                'observacion' => $detalleItem->observacion ?? $detalleItem->detalle ?? $mov->observacion_general ?? $mov->observaciones ?? '',
                'firmada'     => !empty($mov->acta_archivo),
                'pdf_url'     => !empty($mov->id) && \Illuminate\Support\Facades\Route::has('inventario.movimientos.pdf')
                                    ? route('inventario.movimientos.pdf', $mov->id)
                                    : null,
                'archivo_url' => !empty($mov->acta_archivo) && method_exists($mov, 'getFile')
                                    ? $mov->getFile($mov->acta_archivo)
                                    : null,
            ]);
        }

        foreach ($mantsCol as $mant) {
            $fechaRaw = $mant->created_at ?? $mant->fecha ?? now();
            $ts       = \Carbon\Carbon::parse($fechaRaw)->timestamp;
            $timelineEventos->push([
                'categoria'   => 'mantenimiento',
                'ts'          => $ts,
                'fecha_texto' => \Carbon\Carbon::parse($fechaRaw)->format('d M Y - h:i A'),
                'titulo'      => 'Servicio Técnico / Mantenimiento',
                'codigo'      => 'MNT-' . str_pad($mant->id ?? 0, 4, '0', STR_PAD_LEFT),
                'responsable' => $mant->asignado->name ?? $mant->responsable->name ?? 'Soporte Técnico',
                'creador'     => $mant->creador->name ?? $mant->tecnico->name ?? 'Sistema',
                'observacion' => $mant->detalle ?? $mant->observaciones ?? 'Intervención registrada',
                'costo'       => (float) ($mant->costo_mantenimiento ?? $mant->costo ?? 0),
                'archivo_url' => !empty($mant->acta) && method_exists($mant, 'getFile')
                                    ? $mant->getFile($mant->acta)
                                    : null,
            ]);
        }

        if ($detCompra || $compra) {
            $fechaCompraRaw = $compra->fecha_factura ?? $detCompra->created_at ?? $activo->created_at ?? now();
            $tsCompra       = \Carbon\Carbon::parse($fechaCompraRaw)->timestamp;
            $timelineEventos->push([
                'categoria'   => 'compra',
                'ts'          => $tsCompra,
                'fecha_texto' => \Carbon\Carbon::parse($fechaCompraRaw)->format('d M Y'),
                'titulo'      => 'Ingreso por Factura de Compra',
                'codigo'      => 'FAC #' . ($compra->numero_factura ?? ($detCompra->id_InvCompras ?? 'S/N')),
                'responsable' => $proveedor ?? 'Proveedor de Inventario',
                'creador'     => $compra->usuarioRegistro->name ?? 'Almacén',
                'observacion' => $detCompra->detalle ?? ('Cant.: ' . ($detCompra->cantidades ?? 1) . ' · Costo U.: $' . number_format((float) ($detCompra->precio_unitario ?? 0), 2)),
                'costo'       => (float) ($detCompra->sub_total ?? $compra->total_pago ?? 0),
                'compra_id'   => $compra->id ?? ($detCompra->id_InvCompras ?? null),
            ]);
        }

        $timelineOrdenada = $timelineEventos->sortByDesc('ts')->values();
        $costoTotalMant   = (float) $mantsCol->sum(fn ($m) => (float) ($m->costo_mantenimiento ?? $m->costo ?? 0));
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

        /* Cajas de metadatos */
        .corp-meta-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 11px 14px;
            height: 100%;
        }
        .corp-meta-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--corp-muted);
            display: block;
            margin-bottom: 3px;
        }
        .corp-meta-value {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--corp-ink);
            word-break: break-word;
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

        .corp-bod-avatar {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--corp-accent-soft);
            color: var(--corp-accent);
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Tarjeta oscura de auditoría de adquisición */
        .corp-audit-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        /* Timeline de trazabilidad */
        .corp-timeline {
            position: relative;
            padding-left: 26px;
            margin: 4px 0 0 0;
        }
        .corp-timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: #e2e8f0;
        }
        .corp-tl-item {
            position: relative;
            margin-bottom: 14px;
        }
        .corp-tl-item:last-child {
            margin-bottom: 0;
        }
        .corp-tl-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 6px;
            width: 12px;
            height: 12px;
            background: #ffffff;
            border: 3px solid var(--corp-accent);
            border-radius: 50%;
            z-index: 2;
        }
        .corp-tl-item.tl-mant::before {
            border-color: #059669;
        }
        .corp-tl-item.tl-compra::before {
            border-color: #d97706;
        }
        .corp-tl-date {
            font-size: 11px;
            font-weight: 700;
            color: var(--corp-muted);
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 4px;
        }
        .corp-tl-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
        }

        /* Árbol de clasificación */
        .corp-tree-step {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 8px 12px;
        }

        /* Modal corporativo */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1060;
            padding: 16px;
        }
        .modal-card-corp {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 14px;
            border: 1px solid var(--corp-border);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.15);
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }
        .mini-table td {
            padding: 9px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .mini-table .label {
            font-weight: 700;
            color: var(--corp-muted);
            width: 38%;
            font-size: 11.5px;
            text-transform: uppercase;
        }
        .mini-table .value {
            font-weight: 700;
            color: var(--corp-ink);
            text-align: right;
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
                                <li class="breadcrumb-item text-muted">Almacén de Activos</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $activo->codigo_activo }}</li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0" style="letter-spacing: -0.02em; color: #0f172a;">
                                {{ $activo->nombre }}
                            </h4>
                            <span class="corp-code">PLACA: {{ $activo->codigo_activo }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i>{{ $activo->estado->nombre ?? 'ESTADO N/A' }}
                            </span>
                            @if($bodega)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $bodega->nombre }}
                                </span>
                            @endif
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 13px;">
                            <span class="me-3"><strong>SERIAL:</strong> <span class="font-monospace">{{ $activo->serial ?: 'NO REGISTRA' }}</span></span>
                            <span class="me-3"><strong>ID SISTEMA:</strong> <span class="font-monospace">#{{ $activo->id }}</span></span>
                            <span><strong>EVENTOS TRAZADOS:</strong> <span class="font-monospace fw-bold text-dark">{{ $timelineOrdenada->count() }}</span></span>
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('inventario.activos.index') }}"
                           class="btn btn-outline-secondary fw-semibold px-3 py-2"
                           style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-arrow-left me-1"></i> Inventario de Activos
                        </a>
                        @if(\Illuminate\Support\Facades\Route::has('inventario.mantenimientos.create'))
                            <a href="{{ route('inventario.mantenimientos.create') }}"
                               class="btn btn-outline-success fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-wrench-adjustable me-1"></i> + Mantenimiento
                            </a>
                        @endif
                        <a href="{{ route('inventario.activos.edit', $activo->id) }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-sliders me-1"></i> Configuración / Editar
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
                    <a href="/inventario/estados" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-sliders"></i> Estado
                    </a>
                </div>
            </div>

            {{-- 2. CUERPO PRINCIPAL EN 2 COLUMNAS --}}
            <div class="row g-3">

                {{-- COLUMNA IZQUIERDA: AUDITORÍA, COMPRA, DOCUMENTACIÓN Y CUSTODIA --}}
                <div class="col-12 col-lg-4 col-xl-3">

                    {{-- Tarjeta Auditoría de Adquisición y Factura --}}
                    <div class="corp-audit-card mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.12) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff text-info"></i>
                                <span class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: .05em; color: #cbd5e1;">
                                    Origen y Facturación
                                </span>
                            </div>
                            <span class="badge bg-info-subtle text-dark font-monospace" style="font-size: 10.5px;">
                                {{ $activo->id_InvDetalleCompras ?? 'MANUAL' }}
                            </span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; font-weight: 700;">
                                    Factura
                                </div>
                                <div class="fw-bold font-monospace text-white mt-0.5" style="font-size: 14px;">
                                    {{ $compra->numero_factura ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; font-weight: 700;">
                                    Costo Unitario
                                </div>
                                <div class="fw-bold font-monospace text-success mt-0.5" style="font-size: 14px;">
                                    {{ isset($detCompra->precio_unitario) ? ('$' . number_format((float) $detCompra->precio_unitario, 0, ',', '.')) : 'N/A' }}
                                </div>
                            </div>
                            @if($proveedor || $metodoPago)
                                <div class="col-12 pt-1">
                                    <div style="font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; font-weight: 700;">
                                        Proveedor / Pago
                                    </div>
                                    <div class="fw-semibold text-light mt-0.5" style="font-size: 12px;">
                                        {{ $proveedor ?? 'Proveedor' }}{{ $metodoPago ? (' · ' . $metodoPago) : '' }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-none" id="val_detalle_compra_id">{{ $activo->id_InvDetalleCompras ?? 'MANUAL' }}</div>

                        @if($activo->id_InvDetalleCompras)
                            <button type="button" id="btnInspectPurchase" class="btn btn-primary fw-semibold w-100" style="font-size: 12.5px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-search me-1"></i> Inspeccionar Detalle Compra
                            </button>
                        @endif
                    </div>

                    {{-- Documentación y Soportes --}}
                    <div class="corp-panel mb-3">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-folder2-open text-primary"></i> Documentación
                            </h6>
                        </div>
                        <div class="corp-panel-body">
                            <div class="mb-3">
                                <span class="corp-meta-label">Hoja de Vida</span>
                                @if($activo->hoja_vida)
                                    <a href="{{ $activo->hoja_vida }}" target="_blank" class="btn btn-sm btn-light border fw-semibold text-primary w-100 mt-1" style="font-size: 12.5px; border-radius: 8px;">
                                        <i class="bi bi-file-earmark-text me-1"></i> Ver Documento
                                    </a>
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 13px;">No cargada</span>
                                @endif
                            </div>

                            @php $compraIdDoc = $compra->id ?? ($detCompra->id_InvCompras ?? null); @endphp
                            @if($compraIdDoc)
                                <div class="pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                    <span class="corp-meta-label">Factura Original</span>
                                    <a href="{{ route('inventario.compras.archivo', $compraIdDoc) }}" target="_blank" class="btn btn-sm btn-light border fw-semibold text-dark w-100 mt-1" style="font-size: 12.5px; border-radius: 8px;">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Archivo de Factura
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Ubicación, Bodega y Custodia --}}
                    <div class="corp-panel">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-person-badge text-primary"></i> Ubicación y Custodia
                            </h6>
                        </div>
                        <div class="corp-panel-body">
                            <div class="corp-meta-box mb-2">
                                <span class="corp-meta-label">Custodio Actual</span>
                                <span class="corp-meta-value" style="font-size: 13.5px;">
                                    {{ $activo->usuarioAsignado->name ?? 'DISPONIBLE EN BODEGA' }}
                                </span>
                            </div>
                            <div class="corp-meta-box mb-2">
                                <span class="corp-meta-label">Bodega / Almacén</span>
                                <span class="corp-meta-value" style="font-size: 13.5px;">
                                    {{ $bodega->nombre ?? 'Sin Bodega' }}
                                </span>
                            </div>
                            <div class="corp-meta-box">
                                <span class="corp-meta-label">Sede / Municipio</span>
                                <span class="corp-meta-value" style="font-size: 13.5px;">
                                    {{ $activo->municipio->nombre ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: EXPEDIENTE TÉCNICO, CLASIFICACIÓN, TRAZABILIDAD Y MANTENIMIENTOS --}}
                <div class="col-12 col-lg-8 col-xl-9">

                    {{-- Expediente Técnico + Jerarquía de Clasificación --}}
                    <div class="corp-panel mb-3">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-cpu text-primary"></i> Expediente Técnico y Clasificación
                            </h6>
                            <span class="text-muted" style="font-size: 11.5px;">
                                Referencia: <strong class="text-dark">{{ $ref->referencia ?? 'N/A' }}</strong>
                            </span>
                        </div>
                        <div class="corp-panel-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Marca</span>
                                        <span class="corp-meta-value">{{ $marca }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Referencia</span>
                                        <span class="corp-meta-value">{{ $ref->referencia ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Unidad de Medida</span>
                                        <span class="corp-meta-value">{{ $activo->unidad_medida ?? 'UNIDAD' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Fin de Garantía</span>
                                        <span class="corp-meta-value font-monospace {{ $activo->fecha_fin_garantia && $activo->fecha_fin_garantia->isPast() ? 'text-danger' : 'text-success' }}">
                                            {{ $activo->fecha_fin_garantia ? $activo->fecha_fin_garantia->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Vida Útil</span>
                                        <span class="corp-meta-value">{{ $activo->vida_util_meses ?? 0 }} Meses</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Costo Mantenimientos</span>
                                        <span class="corp-meta-value font-monospace text-dark">
                                            ${{ number_format($costoTotalMant, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Cadena Jerárquica: Tipo -> Línea -> Grupo -> Subgrupo --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md-3">
                                    <div class="corp-tree-step">
                                        <span class="corp-meta-label">Tipo</span>
                                        <div class="fw-bold text-primary" style="font-size: 12.5px;">{{ $tipoCat }}</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="corp-tree-step">
                                        <span class="corp-meta-label">Línea</span>
                                        <div class="fw-bold text-success" style="font-size: 12.5px;">{{ $linea }}</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="corp-tree-step">
                                        <span class="corp-meta-label">Grupo</span>
                                        <div class="fw-bold" style="font-size: 12.5px; color: #6d28d9;">{{ $grupo }}</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="corp-tree-step">
                                        <span class="corp-meta-label">Subgrupo</span>
                                        <div class="fw-bold text-dark" style="font-size: 12.5px;">{{ $subgrupo }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 rounded-3 border-start border-4 border-primary" style="background: #f8fafc;">
                                <span class="corp-meta-label">Descripción Detallada</span>
                                <p class="text-secondary mb-0 mt-1" style="font-size: 13.5px; line-height: 1.55;">
                                    {{ $activo->descripcion ?: 'Sin observaciones técnicas registradas.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Trazabilidad Integral: Movimientos, Mantenimientos y Compras --}}
                    <div class="corp-panel mb-3">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-clock-history text-primary"></i> Trazabilidad Integral del Activo
                            </h6>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de evento">
                                    <button type="button" class="btn btn-outline-secondary active js-tl-filter" data-cat="all">
                                        Todos ({{ $timelineOrdenada->count() }})
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary js-tl-filter" data-cat="movimiento">
                                        Movimientos ({{ $movimientosCol->count() }})
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary js-tl-filter" data-cat="mantenimiento">
                                        Mantenimientos ({{ $mantsCol->count() }})
                                    </button>
                                </div>
                                <input type="text" id="tlSearchInput" class="form-control form-control-sm" style="width: 190px;" placeholder="Filtrar historial...">
                            </div>
                        </div>
                        <div class="corp-panel-body">
                            <div class="corp-timeline" id="traceTimelineList">
                                @forelse($timelineOrdenada as $ev)
                                    @php
                                        $catClass = match($ev['categoria']) {
                                            'mantenimiento' => 'tl-mant',
                                            'compra'        => 'tl-compra',
                                            default         => 'tl-mov',
                                        };
                                        $badgeClass = match($ev['categoria']) {
                                            'mantenimiento' => 'bg-success-subtle text-success border border-success-subtle',
                                            'compra'        => 'bg-warning-subtle text-dark border border-warning-subtle',
                                            default         => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        };
                                        $searchHay = strtolower($ev['titulo'] . ' ' . $ev['codigo'] . ' ' . $ev['responsable'] . ' ' . $ev['creador'] . ' ' . $ev['observacion']);
                                    @endphp
                                    <div class="corp-tl-item {{ $catClass }} js-tl-item"
                                         data-cat="{{ $ev['categoria'] }}"
                                         data-search="{{ $searchHay }}">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <span class="corp-tl-date mb-0">{{ $ev['fecha_texto'] }}</span>
                                            <span class="badge {{ $badgeClass }}" style="font-size: 10.5px; text-transform: uppercase;">
                                                {{ $ev['categoria'] }}
                                            </span>
                                        </div>
                                        <div class="corp-tl-box">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                <div>
                                                    <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $ev['titulo'] }}</span>
                                                    <span class="corp-code ms-1">{{ $ev['codigo'] }}</span>
                                                    @if($ev['categoria'] === 'movimiento')
                                                        <span class="badge ms-1 {{ $ev['firmada'] ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-dark border border-warning-subtle' }}" style="font-size: 10.5px;">
                                                            {{ $ev['firmada'] ? 'Acta Firmada' : 'Firma Pendiente' }}
                                                        </span>
                                                    @endif
                                                    @if(isset($ev['costo']) && $ev['costo'] > 0)
                                                        <span class="badge bg-light text-dark border font-monospace ms-1" style="font-size: 11px;">
                                                            ${{ number_format($ev['costo'], 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="d-inline-flex gap-1">
                                                    @if(!empty($ev['pdf_url']))
                                                        <a href="{{ $ev['pdf_url'] }}" target="_blank" class="btn btn-sm btn-light border text-dark py-0.5 px-2" style="font-size: 11.5px;" title="Imprimir acta">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                    @endif
                                                    @if(!empty($ev['archivo_url']))
                                                        <a href="{{ $ev['archivo_url'] }}" target="_blank" class="btn btn-sm btn-light border text-danger py-0.5 px-2" style="font-size: 11.5px;" title="Ver soporte">
                                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-secondary mt-1" style="font-size: 12.5px;">
                                                {{ $ev['categoria'] === 'compra' ? 'Proveedor' : 'Responsable' }}:
                                                <strong class="text-dark">{{ $ev['responsable'] }}</strong>
                                                <span class="text-muted ms-2" style="font-size: 11.5px;">· Reg.: {{ $ev['creador'] }}</span>
                                            </div>

                                            @if(!empty($ev['observacion']))
                                                <div class="text-secondary mt-1 pt-1 border-top" style="font-size: 12px; border-color: #e2e8f0 !important;">
                                                    {{ $ev['observacion'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3 text-muted" style="font-size: 13px;">
                                        Sin eventos de trazabilidad registrados para este activo.
                                    </div>
                                @endforelse
                            </div>

                            <div id="tlEmptyFilter" class="text-center py-3 text-muted d-none" style="font-size: 13px;">
                                No hay eventos que coincidan con el filtro seleccionado.
                            </div>
                        </div>
                    </div>

                    {{-- Auditoría de Sistema --}}
                    <div class="corp-panel">
                        <div class="corp-panel-head">
                            <h6 class="corp-panel-title">
                                <i class="bi bi-shield-check text-primary"></i> Auditoría de Sistema
                            </h6>
                        </div>
                        <div class="corp-panel-body">
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Creado por</span>
                                        <span class="corp-meta-value" style="font-size: 13px;">{{ $activo->usuarioRegistro->name ?? 'SISTEMA' }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="corp-meta-box">
                                        <span class="corp-meta-label">Fecha Creación</span>
                                        <span class="corp-meta-value font-monospace" style="font-size: 13px;">{{ $activo->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INSPECCIÓN DE FACTURA --}}
    <div id="modalPurchase" class="modal-overlay" style="display: none;">
        <div class="modal-card-corp">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <div class="corp-bod-avatar">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Detalles de Compra</h6>
                        <span id="modal_purchase_label" class="text-primary fw-bold" style="font-size: 11px; text-transform: uppercase;"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" id="btnCloseIcon" aria-label="Cerrar"></button>
            </div>

            <div class="p-4">
                <table class="mini-table">
                    <tr><td class="label">Factura</td> <td class="value text-primary font-monospace" id="view_master"></td></tr>
                    <tr><td class="label">Cant.</td> <td class="value" id="view_cant"></td></tr>
                    <tr><td class="label">Costo U.</td> <td class="value text-success font-monospace" id="view_precio"></td></tr>
                    <tr><td class="label">Subtotal</td> <td class="value font-monospace" id="view_subtotal"></td></tr>
                    <tr><td class="label">Referencia</td> <td class="value" id="view_ref"></td></tr>
                </table>

                <div class="mt-3 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <span class="corp-meta-label mb-1">Notas</span>
                    <p id="view_obs" class="text-secondary mb-0" style="font-size: 13px;"></p>
                </div>

                <div id="fileContainer" class="mt-3"></div>
            </div>

            <div class="px-4 py-3 bg-light border-top">
                <button type="button" id="btnCloseModalPurchase" class="btn btn-dark fw-semibold w-100" style="font-size: 13px;">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            var activeCat = 'all';

            function filterTimeline() {
                var term = ($('#tlSearchInput').val() || '').trim().toLowerCase();
                var visible = 0;

                $('.js-tl-item').each(function() {
                    var cat = $(this).data('cat');
                    var hay = String($(this).data('search') || '');
                    var matchCat = (activeCat === 'all') || (cat === activeCat);
                    var matchText = !term || hay.indexOf(term) !== -1;

                    if (matchCat && matchText) {
                        $(this).show();
                        visible++;
                    } else {
                        $(this).hide();
                    }
                });

                $('#tlEmptyFilter').toggleClass('d-none', visible > 0);
            }

            $('.js-tl-filter').on('click', function() {
                $('.js-tl-filter').removeClass('active');
                $(this).addClass('active');
                activeCat = $(this).data('cat');
                filterTimeline();
            });

            $('#tlSearchInput').on('input', filterTimeline);

            $('#btnInspectPurchase').click(function() {
                let id = $('#val_detalle_compra_id').text().trim();
                if(!id || id === 'MANUAL') return;

                $('#modalPurchase').fadeIn('fast').css('display', 'flex');
                $('#modal_purchase_label').text('Registro #' + id);

                $.ajax({
                    url: `/inventario/detalle-compra/ajax/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if(res.success) {
                            let d = res.data;
                            $('#view_cant').text(d.cantidades);
                            $('#view_precio').text('$' + parseFloat(d.precio_unitario).toLocaleString());
                            $('#view_subtotal').text('$' + parseFloat(d.sub_total).toLocaleString());
                            $('#view_ref').text(d.referencia?.referencia || 'SIN REF');
                            $('#view_master').text(d.compra?.numero_factura || 'S/N');
                            $('#view_obs').text(d.detalle || 'Sin notas.');

                            if(d.id_InvCompras) {
                                $('#fileContainer').html(`
                                    <a href="/inventario/compras/${d.id_InvCompras}/archivo" target="_blank" class="btn btn-dark fw-semibold w-100" style="font-size: 13px; border-radius: 8px;">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Ver Factura Original
                                    </a>
                                `);
                            }
                        }
                    },
                    error: function() {
                        alert('Error al obtener datos.');
                        $('#modalPurchase').fadeOut('fast');
                    }
                });
            });

            $('#btnCloseModalPurchase, #btnCloseIcon').click(function() {
                $('#modalPurchase').fadeOut('fast');
            });

            $(window).click(function(e) {
                if ($(e.target).hasClass('modal-overlay')) {
                    $('#modalPurchase').fadeOut('fast');
                }
            });
        });
    </script>
</x-base-layout>
