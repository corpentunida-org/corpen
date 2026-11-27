{{-- resources/views/inventario/catalogos/bodegas/index.blade.php · INV-BOD-v5.3.1-COLORGROUPS --}}
<x-base-layout>
    @php
        $bodegasCol     = collect($bodegas ?? []);
        $bodTotal       = $bodegasCol->count();
        $bodConDesc     = $bodegasCol->filter(fn ($b) => !empty(trim($b->descripcion ?? '')))->count();
        $bodSinDesc     = max(0, $bodTotal - $bodConDesc);
        $porcDoc        = $bodTotal > 0 ? round(($bodConDesc / $bodTotal) * 100) : 0;
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

        /* KPIs compactos */
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
            color: var(--corp-ink);
            line-height: 1.2;
        }

        /* Barra de herramientas y búsqueda */
        .corp-toolbar {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 10px 16px;
        }
        .corp-search-input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px 8px 36px;
            background-color: var(--corp-bg);
            transition: all .15s ease;
        }
        .corp-search-input:focus {
            background-color: #ffffff;
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        /* Segmentos de filtro estilo SaaS */
        .corp-segmented {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 9px;
            display: inline-flex;
            gap: 2px;
        }
        .corp-segmented .btn {
            border: none;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            padding: 5px 12px;
            transition: all .15s ease;
        }
        .corp-segmented .btn.active {
            background: #ffffff;
            color: var(--corp-ink);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.1);
        }

        /* Tarjetas de bodega */
        .corp-bod-card {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 18px;
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
            position: relative;
        }
        .corp-bod-card:hover {
            transform: translateY(-2px);
            border-color: #93c5fd;
            box-shadow: 0 12px 24px -10px rgba(15, 23, 42, 0.1);
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
        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: .03em;
        }
        .corp-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
        }

        /* Panel colapsable de creación rápida */
        .corp-quick-drawer {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 14px 18px;
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
                                <li class="breadcrumb-item text-muted">Configuración</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Bodegas / Sedes</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Gestión de Bodegas y Centros de Almacenamiento
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Directorio maestro de ubicaciones físicas habilitadas para recepción, custodia y despacho de inventario.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-outline-secondary fw-semibold px-3 py-2"
                                data-bs-toggle="collapse"
                                data-bs-target="#quickCreateCollapse"
                                aria-expanded="false"
                                style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-lightning-charge me-1 text-warning"></i>
                        </button>
                        <button type="button"
                                class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#createBodegaModal"
                                style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Bodega
                        </button>
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
                    <a href="{{ route('inventario.activos.alertas') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                        <i class="bi bi-exclamation-triangle"></i> Alertas
                    </a>

                    <span class="corp-nav-sep"></span>

                    {{-- GRUPO 4: CONFIGURACIÓN / CATÁLOGOS (Púrpura suave) --}}
                    <a href="{{ route('inventario.bodegas.index') }}" class="corp-nav-pill pill-conf active" title="Configuración">
                        <i class="bi bi-geo-alt-fill"></i> Bodegas
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

                {{-- Drawer Colapsable para Registro Rápido sin saturar la vista --}}
                <div class="collapse mt-3" id="quickCreateCollapse">
                    <div class="corp-quick-drawer">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark" style="font-size: 12.5px;">
                                <i class="bi bi-plus-square-dotted text-primary me-1"></i> Registro Express de Bodega
                            </span>
                            <span class="text-muted" style="font-size: 11.5px;">Se habilitará inmediatamente en el catálogo</span>
                        </div>
                        <form action="{{ route('inventario.bodegas.store') }}" method="POST" class="row g-2 align-items-center">
                            @csrf
                            <div class="col-12 col-md-4">
                                <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Nombre (Ej: Bodega Central Norte)" required style="font-size: 13px; padding: 7px 10px;">
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" name="descripcion" class="form-control form-control-sm" placeholder="Ubicación física o tipo de mercancía resguardada..." required style="font-size: 13px; padding: 7px 10px;">
                            </div>
                            <div class="col-12 col-md-2 d-grid">
                                <button type="submit" class="btn btn-sm btn-dark fw-semibold" style="font-size: 13px; padding: 7px 12px;">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 2. INDICADORES EJECUTIVOS (KPIs) --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Total Bodegas</div>
                            <div class="corp-kpi-value">{{ $bodTotal }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Ubicaciones activas</div>
                        </div>
                        <div class="corp-bod-avatar">
                            <i class="bi bi-buildings"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Con Ficha Técnica</div>
                            <div class="corp-kpi-value text-success">{{ $bodConDesc }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">{{ $porcDoc }}% documentadas</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Sin Especificación</div>
                            <div class="corp-kpi-value {{ $bodSinDesc > 0 ? 'text-warning' : 'text-dark' }}">{{ $bodSinDesc }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Pendientes de detalle</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #fffbeb; color: #d97706; border-color: #fde68a;">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Estado Operativo</div>
                            <div class="corp-kpi-value" style="font-size: 16px; padding-top: 4px;">Sincronizado</div>
                            <div class="text-success mt-1 fw-semibold" style="font-size: 11.5px;">
                                <i class="bi bi-check2-all me-1"></i>Listo para movimientos
                            </div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #f1f5f9; color: #334155; border-color: #e2e8f0;">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. BARRA DE BÚSQUEDA, FILTROS Y CAMBIO DE VISTA --}}
            <div class="corp-toolbar mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    {{-- Buscador --}}
                    <div class="position-relative flex-grow-1" style="max-width: 480px;">
                        <i class="bi bi-search text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px;"></i>
                        <input type="text"
                               id="bodSearchInput"
                               class="form-control corp-search-input"
                               placeholder="Buscar por código (BOD-01), nombre o ubicación...">
                    </div>

                    {{-- Controles derechos: Filtros segmentados + Selector Tarjetas/Tabla --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="corp-segmented" role="group" aria-label="Filtros de bodega">
                            <button type="button" class="btn js-bod-filter active" data-filter="all">
                                Todas <span class="text-muted ms-1">({{ $bodTotal }})</span>
                            </button>
                            <button type="button" class="btn js-bod-filter" data-filter="con-desc">
                                Documentadas <span class="text-muted ms-1">({{ $bodConDesc }})</span>
                            </button>
                            @if($bodSinDesc > 0)
                                <button type="button" class="btn js-bod-filter" data-filter="sin-desc">
                                    Sin detalle <span class="text-muted ms-1">({{ $bodSinDesc }})</span>
                                </button>
                            @endif
                        </div>

                        <div class="btn-group" role="group" aria-label="Modo de visualización">
                            <button type="button" class="btn btn-sm btn-outline-secondary active js-view-toggle" data-view="grid" title="Vista de tarjetas">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary js-view-toggle" data-view="list" title="Vista de tabla ejecutiva">
                                <i class="bi bi-list-ul"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. CONTENIDO PRINCIPAL: TARJETAS VISUALES / TABLA EJECUTIVA --}}
            @if($bodTotal > 0)
                {{-- MODO TARJETAS --}}
                <div class="row g-3" id="bodGridView">
                    @foreach($bodegasCol as $bodega)
                        @php
                            $tieneDesc = !empty(trim($bodega->descripcion ?? ''));
                            $codigoBod = 'BOD-' . str_pad($bodega->id, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 col-xxl-3 js-bod-item"
                             data-has-desc="{{ $tieneDesc ? '1' : '0' }}"
                             data-search="{{ strtolower($codigoBod . ' ' . $bodega->id . ' ' . $bodega->nombre . ' ' . ($bodega->descripcion ?? '')) }}">
                            <div class="corp-bod-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="corp-code">{{ $codigoBod }}</span>
                                        <span class="d-inline-flex align-items-center gap-2 text-success fw-semibold" style="font-size: 11.5px;">
                                            <span class="corp-status-dot"></span> Operativa
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="corp-bod-avatar">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $bodega->nombre }}" style="font-size: 15.5px;">
                                                {{ $bodega->nombre }}
                                            </h6>
                                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                @if($tieneDesc)
                                                    {{ $bodega->descripcion }}
                                                @else
                                                    <span class="text-muted fst-italic">Sin descripción o ubicación física registrada.</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-3 mt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                                    <span class="text-muted" style="font-size: 11.5px;">
                                        Ref. Sistema: <span class="fw-semibold text-dark">#{{ $bodega->id }}</span>
                                    </span>
                                    <button type="button"
                                            class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-bod-edit"
                                            style="font-size: 12.5px; border-radius: 7px;"
                                            data-id="{{ $bodega->id }}"
                                            data-nombre="{{ $bodega->nombre }}"
                                            data-descripcion="{{ $bodega->descripcion ?? '' }}">
                                        <i class="bi bi-pencil me-1 text-primary"></i> Configurar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- MODO TABLA EJECUTIVA --}}
                <div class="bg-white border rounded-3 overflow-hidden d-none" id="bodListView" style="border-color: #e2e8f0 !important;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                            <thead style="background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: .04em;">
                                <tr>
                                    <th class="ps-3" style="width: 110px;">Código</th>
                                    <th style="width: 260px;">Nombre de Bodega</th>
                                    <th>Descripción / Ubicación</th>
                                    <th class="text-center" style="width: 120px;">Estado</th>
                                    <th class="text-end pe-3" style="width: 130px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bodegasCol as $bodega)
                                    @php
                                        $tieneDesc = !empty(trim($bodega->descripcion ?? ''));
                                        $codigoBod = 'BOD-' . str_pad($bodega->id, 2, '0', STR_PAD_LEFT);
                                    @endphp
                                    <tr class="js-bod-item"
                                        data-has-desc="{{ $tieneDesc ? '1' : '0' }}"
                                        data-search="{{ strtolower($codigoBod . ' ' . $bodega->id . ' ' . $bodega->nombre . ' ' . ($bodega->descripcion ?? '')) }}">
                                        <td class="ps-3"><span class="corp-code">{{ $codigoBod }}</span></td>
                                        <td class="fw-bold text-dark">{{ $bodega->nombre }}</td>
                                        <td class="text-secondary">
                                            {{ $tieneDesc ? $bodega->descripcion : 'Sin descripción registrada' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="d-inline-flex align-items-center gap-2 text-success fw-semibold" style="font-size: 12px;">
                                                <span class="corp-status-dot"></span> Operativa
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button"
                                                    class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-bod-edit"
                                                    style="font-size: 12px;"
                                                    data-id="{{ $bodega->id }}"
                                                    data-nombre="{{ $bodega->nombre }}"
                                                    data-descripcion="{{ $bodega->descripcion ?? '' }}">
                                                <i class="bi bi-pencil me-1 text-primary"></i> Configurar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Estado vacío de búsqueda --}}
                <div id="bodEmptyFilter" class="text-center py-5 bg-white border rounded-3 d-none mt-3">
                    <div class="corp-bod-avatar mx-auto mb-3" style="width: 52px; height: 52px; font-size: 22px;">
                        <i class="bi bi-search"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Sin coincidencias en el catálogo</h6>
                    <p class="text-secondary mb-0" style="font-size: 13px;">
                        No hay bodegas que coincidan con los criterios de búsqueda o filtro aplicados.
                    </p>
                </div>
            @else
                <div class="text-center py-5 bg-white border rounded-3">
                    <div class="corp-bod-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No existen bodegas registradas</h5>
                    <p class="text-secondary mb-3" style="font-size: 13.5px;">
                        Configura la primera ubicación física para comenzar a gestionar las existencias de inventario.
                    </p>
                    <button type="button" class="btn btn-primary fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#createBodegaModal" style="background-color: #1d4ed8; border-color: #1d4ed8;">
                        <i class="bi bi-plus-lg me-1"></i> Registrar Primera Bodega
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL CREAR BODEGA --}}
    <div class="modal fade" id="createBodegaModal" tabindex="-1" aria-labelledby="createBodegaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="corp-bod-avatar" style="width: 36px; height: 36px; font-size: 16px;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="createBodegaModalLabel">Registrar Nueva Bodega</h6>
                            <div class="text-muted" style="font-size: 11.5px;">Inventario · Catálogo de ubicaciones</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('inventario.bodegas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="createNombre" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Nombre de la bodega <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="createNombre" name="nombre" class="form-control" placeholder="Ej: Bodega Central - Nivel 1" required style="font-size: 13.5px;">
                        </div>
                        <div class="mb-1">
                            <label for="createDescripcion" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Descripción y ubicación física <span class="text-danger">*</span>
                            </label>
                            <textarea id="createDescripcion" name="descripcion" class="form-control" rows="3" placeholder="Indica dirección, piso, estanterías o tipo de mercancía..." required style="font-size: 13.5px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal" style="font-size: 13px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            Guardar Bodega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR BODEGA --}}
    <div class="modal fade" id="editBodegaModal" tabindex="-1" aria-labelledby="editBodegaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="corp-bod-avatar" style="width: 36px; height: 36px; font-size: 16px;">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="modal-title fw-bold text-dark mb-0" id="editBodegaModalLabel">Configurar Bodega</h6>
                                <span class="corp-code" id="editModalBadge">BOD-00</span>
                            </div>
                            <div class="text-muted" style="font-size: 11.5px;">Actualización de parámetros de ubicación</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="editForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editId" name="id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Nombre de la bodega <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="editNombre" name="nombre" class="form-control" required style="font-size: 13.5px;">
                        </div>
                        <div class="mb-1">
                            <label for="editDescripcion" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Descripción y ubicación física <span class="text-danger">*</span>
                            </label>
                            <textarea id="editDescripcion" name="descripcion" class="form-control" rows="3" required style="font-size: 13.5px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal" style="font-size: 13px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var currentFilter = 'all';
            var currentView = 'grid';

            function applyFilters() {
                var searchInput = document.getElementById('bodSearchInput');
                var term = searchInput ? searchInput.value.trim().toLowerCase() : '';
                var gridItems = document.querySelectorAll('#bodGridView .js-bod-item');
                var listItems = document.querySelectorAll('#bodListView .js-bod-item');
                var visibleCount = 0;

                gridItems.forEach(function (item, idx) {
                    var hay = item.getAttribute('data-search') || '';
                    var hasDesc = item.getAttribute('data-has-desc') === '1';

                    var matchesText = !term || hay.indexOf(term) !== -1;
                    var matchesTab = (currentFilter === 'all') ||
                                     (currentFilter === 'con-desc' && hasDesc) ||
                                     (currentFilter === 'sin-desc' && !hasDesc);

                    var show = matchesText && matchesTab;
                    item.classList.toggle('d-none', !show);
                    if (listItems[idx]) {
                        listItems[idx].classList.toggle('d-none', !show);
                    }
                    if (show) visibleCount++;
                });

                var emptyBox = document.getElementById('bodEmptyFilter');
                if (emptyBox) {
                    emptyBox.classList.toggle('d-none', visibleCount > 0 || gridItems.length === 0);
                }
            }

            document.addEventListener('click', function (e) {
                var editBtn = e.target && e.target.closest ? e.target.closest('.js-bod-edit') : null;
                if (editBtn) {
                    var id = editBtn.getAttribute('data-id');
                    var code = 'BOD-' + String(id).padStart(2, '0');
                    document.getElementById('editId').value = id;
                    document.getElementById('editNombre').value = editBtn.getAttribute('data-nombre') || '';
                    document.getElementById('editDescripcion').value = editBtn.getAttribute('data-descripcion') || '';
                    document.getElementById('editModalBadge').textContent = code;
                    document.getElementById('editForm').action = '/inventario/bodegas/' + id;

                    var modalEl = document.getElementById('editBodegaModal');
                    if (window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                    return;
                }

                var filterBtn = e.target && e.target.closest ? e.target.closest('.js-bod-filter') : null;
                if (filterBtn) {
                    document.querySelectorAll('.js-bod-filter').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    filterBtn.classList.add('active');
                    currentFilter = filterBtn.getAttribute('data-filter') || 'all';
                    applyFilters();
                    return;
                }

                var viewBtn = e.target && e.target.closest ? e.target.closest('.js-view-toggle') : null;
                if (viewBtn) {
                    var targetView = viewBtn.getAttribute('data-view');
                    if (targetView === currentView) return;
                    currentView = targetView;

                    document.querySelectorAll('.js-view-toggle').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    viewBtn.classList.add('active');

                    var gridEl = document.getElementById('bodGridView');
                    var listEl = document.getElementById('bodListView');
                    if (gridEl && listEl) {
                        gridEl.classList.toggle('d-none', currentView !== 'grid');
                        listEl.classList.toggle('d-none', currentView !== 'list');
                    }
                }
            });

            var searchInput = document.getElementById('bodSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }
        })();
    </script>
</x-base-layout>
