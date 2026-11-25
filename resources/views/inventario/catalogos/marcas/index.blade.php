{{-- resources/views/inventario/catalogos/marcas/index.blade.php · INV-MAR-v5.3.1-COLORGROUPS --}}
<x-base-layout>
    @php
        $marcasCol      = collect(method_exists($marcas, 'items') ? $marcas->items() : ($marcas ?? []));
        $marTotal       = method_exists($marcas, 'total') ? $marcas->total() : $marcasCol->count();
        $marConModelo   = $marcasCol->filter(fn ($m) => !empty(trim($m->modelo ?? '')))->count();
        $marConDesc     = $marcasCol->filter(fn ($m) => !empty(trim($m->descripcion ?? '')))->count();
        $marUnicas      = $marcasCol->pluck('nombre')->map(fn ($n) => mb_strtolower(trim($n)))->unique()->count();
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

        /* Tarjetas de marca */
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
        .corp-model-badge {
            font-size: 11px;
            font-weight: 600;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 2px 8px;
            border-radius: 6px;
        }

        /* Panel colapsable de creación rápida */
        .corp-quick-drawer {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 14px 18px;
        }

        /* Paginación corporativa */
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

    {{-- MENSAJES FLASH Y ERRORES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 340px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

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
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Gestión de Marcas</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Catálogo de Marcas y Modelos
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Directorio maestro de fabricantes, líneas de producto y referencias técnicas asociadas a los activos.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-outline-secondary fw-semibold px-3 py-2"
                                data-bs-toggle="collapse"
                                data-bs-target="#quickCreateCollapse"
                                aria-expanded="false"
                                style="font-size: 13px; border-radius: 9px;"
                                title="Registro rápido">
                            <i class="bi bi-lightning-charge me-1 text-warning"></i> Alta Rápida
                        </button>
                        <button type="button"
                                class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#createMarcaModal"
                                style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Marca
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
                    <a href="{{ route('inventario.bodegas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-geo-alt"></i> Bodegas
                    </a>
                    <a href="{{ route('inventario.clasificacion.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-diagram-3"></i> Clasificación
                    </a>
                    <a href="{{ route('inventario.marcas.index') }}" class="corp-nav-pill pill-conf active" title="Configuración">
                        <i class="bi bi-tags-fill"></i> Marcas
                    </a>
                    <a href="{{ route('inventario.metodos-pago.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-credit-card"></i> Métodos Pago
                    </a>
                </div>

                {{-- Drawer Colapsable para Registro Express --}}
                <div class="collapse mt-3" id="quickCreateCollapse">
                    <div class="corp-quick-drawer">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark" style="font-size: 12.5px;">
                                <i class="bi bi-plus-square-dotted text-primary me-1"></i> Registro Express de Marca / Modelo
                            </span>
                            <span class="text-muted" style="font-size: 11.5px;">Disponible de inmediato al registrar activos</span>
                        </div>
                        <form action="{{ route('inventario.marcas.store') }}" method="POST" class="row g-2 align-items-center">
                            @csrf
                            <div class="col-12 col-md-3">
                                <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Marca (Ej: Dell, HP, Lenovo) *" value="{{ old('nombre') }}" required style="font-size: 13px; padding: 7px 10px;">
                            </div>
                            <div class="col-12 col-md-3">
                                <input type="text" name="modelo" class="form-control form-control-sm" placeholder="Modelo (Ej: Latitude 5430)" value="{{ old('modelo') }}" style="font-size: 13px; padding: 7px 10px;">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="descripcion" class="form-control form-control-sm" placeholder="Especificación o nota técnica (opcional)..." value="{{ old('descripcion') }}" style="font-size: 13px; padding: 7px 10px;">
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
                            <div class="corp-kpi-label mb-1">Total Registros</div>
                            <div class="corp-kpi-value">{{ $marTotal }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Marcas / modelos activos</div>
                        </div>
                        <div class="corp-bod-avatar">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Fabricantes (Página)</div>
                            <div class="corp-kpi-value text-primary">{{ $marUnicas }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Marcas distintas visibles</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #eff6ff; color: #1d4ed8; border-color: #dbeafe;">
                            <i class="bi bi-award"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Con Modelo Específico</div>
                            <div class="corp-kpi-value text-success">{{ $marConModelo }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Referencia detallada</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                            <i class="bi bi-cpu"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Con Descripción</div>
                            <div class="corp-kpi-value text-dark">{{ $marConDesc }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Documentadas en vista</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #f1f5f9; color: #334155; border-color: #e2e8f0;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. BARRA DE BÚSQUEDA EN TIEMPO REAL, FILTROS Y CAMBIO DE VISTA --}}
            <div class="corp-toolbar mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    {{-- Buscador AJAX en tiempo real --}}
                    <div class="position-relative flex-grow-1" style="max-width: 480px;">
                        <i class="bi bi-search text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px;"></i>
                        <input type="text"
                               id="searchInput"
                               class="form-control corp-search-input"
                               placeholder="Buscar por marca, modelo o descripción...">
                    </div>

                    {{-- Controles derechos: Filtros rápidos + Selector Tarjetas/Tabla --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="corp-segmented" role="group" aria-label="Filtros de marcas">
                            <button type="button" class="btn js-mar-filter active" data-filter="all">
                                Todas
                            </button>
                            <button type="button" class="btn js-mar-filter" data-filter="con-modelo">
                                Con modelo
                            </button>
                            <button type="button" class="btn js-mar-filter" data-filter="sin-modelo">
                                Solo marca
                            </button>
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
            @if($marcasCol->count() > 0)
                {{-- MODO TARJETAS --}}
                <div class="row g-3" id="marGridView">
                    @foreach($marcasCol as $marca)
                        @php
                            $tieneModelo = !empty(trim($marca->modelo ?? ''));
                            $tieneDesc   = !empty(trim($marca->descripcion ?? ''));
                            $codigoMar   = 'MAR-' . str_pad($marca->id, 4, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 col-xxl-3 js-mar-item"
                             data-has-modelo="{{ $tieneModelo ? '1' : '0' }}">
                            <div class="corp-bod-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    {{-- Encabezado de tarjeta --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                        <span class="corp-code">{{ $codigoMar }}</span>
                                        @if($tieneModelo)
                                            <span class="corp-model-badge text-truncate" style="max-width: 155px;" title="{{ $marca->modelo }}">
                                                <i class="bi bi-cpu me-1"></i>{{ $marca->modelo }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 11px;">Marca general</span>
                                        @endif
                                    </div>

                                    {{-- Cuerpo principal --}}
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="corp-bod-avatar">
                                            <i class="bi bi-tag"></i>
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $marca->nombre }}" style="font-size: 15.5px;">
                                                {{ $marca->nombre }}
                                            </h6>
                                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                @if($tieneDesc)
                                                    {{ $marca->descripcion }}
                                                @else
                                                    <span class="text-muted fst-italic">Sin descripción adicional registrada.</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pie de tarjeta --}}
                                <div class="pt-3 mt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                                    <span class="text-muted" style="font-size: 11.5px;">
                                        ID: <span class="fw-semibold text-dark">{{ str_pad($marca->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </span>
                                    <button type="button"
                                            class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-mar-edit"
                                            style="font-size: 12.5px; border-radius: 7px;"
                                            data-id="{{ $marca->id }}"
                                            data-nombre="{{ $marca->nombre }}"
                                            data-modelo="{{ $marca->modelo ?? '' }}"
                                            data-descripcion="{{ $marca->descripcion ?? '' }}">
                                        <i class="bi bi-pencil me-1 text-primary"></i> Editar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- MODO TABLA EJECUTIVA --}}
                <div class="bg-white border rounded-3 overflow-hidden d-none" id="marListView" style="border-color: #e2e8f0 !important;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                            <thead style="background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: .04em;">
                                <tr>
                                    <th class="ps-3" style="width: 120px;">Código</th>
                                    <th style="width: 220px;">Marca</th>
                                    <th style="width: 200px;">Modelo</th>
                                    <th>Descripción</th>
                                    <th class="text-end pe-3" style="width: 120px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="marTableBody">
                                @foreach($marcasCol as $marca)
                                    @php
                                        $tieneModelo = !empty(trim($marca->modelo ?? ''));
                                        $tieneDesc   = !empty(trim($marca->descripcion ?? ''));
                                        $codigoMar   = 'MAR-' . str_pad($marca->id, 4, '0', STR_PAD_LEFT);
                                    @endphp
                                    <tr class="js-mar-item" data-has-modelo="{{ $tieneModelo ? '1' : '0' }}">
                                        <td class="ps-3"><span class="corp-code">{{ $codigoMar }}</span></td>
                                        <td class="fw-bold text-dark">{{ $marca->nombre }}</td>
                                        <td>
                                            @if($tieneModelo)
                                                <span class="corp-model-badge">{{ $marca->modelo }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary">
                                            {{ $tieneDesc ? $marca->descripcion : 'Sin descripción' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button"
                                                    class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-mar-edit"
                                                    style="font-size: 12px;"
                                                    data-id="{{ $marca->id }}"
                                                    data-nombre="{{ $marca->nombre }}"
                                                    data-modelo="{{ $marca->modelo ?? '' }}"
                                                    data-descripcion="{{ $marca->descripcion ?? '' }}">
                                                <i class="bi bi-pencil me-1 text-primary"></i> Editar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Estado vacío cuando no hay coincidencias --}}
                <div id="marEmptyFilter" class="text-center py-5 bg-white border rounded-3 d-none mt-3">
                    <div class="corp-bod-avatar mx-auto mb-3" style="width: 52px; height: 52px; font-size: 22px;">
                        <i class="bi bi-search"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No se encontraron resultados para tu búsqueda</h6>
                    <p class="text-secondary mb-0" style="font-size: 13px;">
                        Intenta con otro nombre de marca, modelo o limpia el filtro aplicado.
                    </p>
                </div>

                {{-- Paginación Laravel --}}
                @if(method_exists($marcas, 'hasPages') && $marcas->hasPages())
                    <div id="paginationWrapper" class="mt-3 p-3 bg-white border rounded-3 d-flex justify-content-center" style="border-color: #e2e8f0 !important;">
                        {{ $marcas->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 bg-white border rounded-3">
                    <div class="corp-bod-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                        <i class="bi bi-tags"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No hay registros de marcas todavía</h5>
                    <p class="text-secondary mb-3" style="font-size: 13.5px;">
                        Agrega la primera marca o modelo para asociarlo a los activos de tu inventario.
                    </p>
                    <button type="button" class="btn btn-primary fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#createMarcaModal" style="background-color: #1d4ed8; border-color: #1d4ed8;">
                        <i class="bi bi-plus-lg me-1"></i> Registrar Primera Marca
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL CREAR MARCA --}}
    <div class="modal fade" id="createMarcaModal" tabindex="-1" aria-labelledby="createMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="corp-bod-avatar" style="width: 36px; height: 36px; font-size: 16px;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="createMarcaModalLabel">Registrar Nueva Marca</h6>
                            <div class="text-muted" style="font-size: 11.5px;">Inventario · Catálogo de fabricantes</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('inventario.marcas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="createNombre" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Nombre de la Marca <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="createNombre" name="nombre" class="form-control" placeholder="Ej. Samsung, Dell, Lenovo..." value="{{ old('nombre') }}" required style="font-size: 13.5px;">
                        </div>
                        <div class="mb-3">
                            <label for="createModelo" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Modelo
                            </label>
                            <input type="text" id="createModelo" name="modelo" class="form-control" placeholder="Ej. Galaxy S23, Latitude 5420..." value="{{ old('modelo') }}" style="font-size: 13.5px;">
                        </div>
                        <div class="mb-1">
                            <label for="createDescripcion" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Descripción
                            </label>
                            <textarea id="createDescripcion" name="descripcion" class="form-control" rows="3" placeholder="Breve descripción o especificaciones (opcional)..." style="font-size: 13.5px;">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal" style="font-size: 13px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            Guardar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR MARCA --}}
    <div class="modal fade" id="editMarcaModal" tabindex="-1" aria-labelledby="editMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="corp-bod-avatar" style="width: 36px; height: 36px; font-size: 16px;">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="modal-title fw-bold text-dark mb-0" id="editMarcaModalLabel">Editar Registro</h6>
                                <span class="corp-code" id="editModalBadge">MAR-0000</span>
                            </div>
                            <div class="text-muted" style="font-size: 11.5px;">Actualización de marca y referencia</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="editForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Nombre de la Marca <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="editNombre" name="nombre" class="form-control" required style="font-size: 13.5px;">
                        </div>
                        <div class="mb-3">
                            <label for="editModelo" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Modelo
                            </label>
                            <input type="text" id="editModelo" name="modelo" class="form-control" style="font-size: 13.5px;">
                        </div>
                        <div class="mb-1">
                            <label for="editDescripcion" class="form-label fw-semibold text-dark" style="font-size: 13px;">
                                Descripción
                            </label>
                            <textarea id="editDescripcion" name="descripcion" class="form-control" rows="3" style="font-size: 13.5px;"></textarea>
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
            var searchTimeout = null;

            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function applyLocalFilter() {
                var gridItems = document.querySelectorAll('#marGridView .js-mar-item');
                var listItems = document.querySelectorAll('#marTableBody .js-mar-item');
                var visibleCount = 0;

                gridItems.forEach(function (item, idx) {
                    var hasModelo = item.getAttribute('data-has-modelo') === '1';
                    var show = (currentFilter === 'all') ||
                               (currentFilter === 'con-modelo' && hasModelo) ||
                               (currentFilter === 'sin-modelo' && !hasModelo);

                    item.classList.toggle('d-none', !show);
                    if (listItems[idx]) {
                        listItems[idx].classList.toggle('d-none', !show);
                    }
                    if (show) visibleCount++;
                });

                var emptyBox = document.getElementById('marEmptyFilter');
                if (emptyBox) {
                    emptyBox.classList.toggle('d-none', visibleCount > 0);
                }
            }

            function renderMarcas(items) {
                var gridView = document.getElementById('marGridView');
                var tableBody = document.getElementById('marTableBody');
                var emptyBox = document.getElementById('marEmptyFilter');

                if (!gridView || !tableBody) return;

                gridView.innerHTML = '';
                tableBody.innerHTML = '';

                if (!items || items.length === 0) {
                    if (emptyBox) emptyBox.classList.remove('d-none');
                    return;
                }
                if (emptyBox) emptyBox.classList.add('d-none');

                var gridHtml = '';
                var tableHtml = '';

                items.forEach(function (marca) {
                    var paddedId = String(marca.id).padStart(4, '0');
                    var code = 'MAR-' + paddedId;
                    var hasModelo = !!(marca.modelo && String(marca.modelo).trim());
                    var hasDesc = !!(marca.descripcion && String(marca.descripcion).trim());

                    var safeNombre = escapeHtml(marca.nombre);
                    var safeModelo = escapeHtml(marca.modelo || '');
                    var safeDesc   = escapeHtml(marca.descripcion || '');

                    var modeloHeaderBadge = hasModelo
                        ? `<span class="corp-model-badge text-truncate" style="max-width: 155px;" title="${safeModelo}"><i class="bi bi-cpu me-1"></i>${safeModelo}</span>`
                        : `<span class="text-muted" style="font-size: 11px;">Marca general</span>`;

                    var descText = hasDesc
                        ? safeDesc
                        : `<span class="text-muted fst-italic">Sin descripción adicional registrada.</span>`;

                    gridHtml += `
                        <div class="col-12 col-md-6 col-lg-4 col-xxl-3 js-mar-item" data-has-modelo="${hasModelo ? '1' : '0'}">
                            <div class="corp-bod-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                        <span class="corp-code">${code}</span>
                                        ${modeloHeaderBadge}
                                    </div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="corp-bod-avatar"><i class="bi bi-tag"></i></div>
                                        <div class="min-w-0 flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="${safeNombre}" style="font-size: 15.5px;">${safeNombre}</h6>
                                            <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${descText}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-3 mt-2 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                                    <span class="text-muted" style="font-size: 11.5px;">ID: <span class="fw-semibold text-dark">${paddedId}</span></span>
                                    <button type="button"
                                            class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-mar-edit"
                                            style="font-size: 12.5px; border-radius: 7px;"
                                            data-id="${marca.id}"
                                            data-nombre="${safeNombre}"
                                            data-modelo="${safeModelo}"
                                            data-descripcion="${safeDesc}">
                                        <i class="bi bi-pencil me-1 text-primary"></i> Editar
                                    </button>
                                </div>
                            </div>
                        </div>`;

                    tableHtml += `
                        <tr class="js-mar-item" data-has-modelo="${hasModelo ? '1' : '0'}">
                            <td class="ps-3"><span class="corp-code">${code}</span></td>
                            <td class="fw-bold text-dark">${safeNombre}</td>
                            <td>${hasModelo ? `<span class="corp-model-badge">${safeModelo}</span>` : `<span class="text-muted">—</span>`}</td>
                            <td class="text-secondary">${hasDesc ? safeDesc : 'Sin descripción'}</td>
                            <td class="text-end pe-3">
                                <button type="button"
                                        class="btn btn-sm btn-light border fw-semibold text-dark px-3 js-mar-edit"
                                        style="font-size: 12px;"
                                        data-id="${marca.id}"
                                        data-nombre="${safeNombre}"
                                        data-modelo="${safeModelo}"
                                        data-descripcion="${safeDesc}">
                                    <i class="bi bi-pencil me-1 text-primary"></i> Editar
                                </button>
                            </td>
                        </tr>`;
                });

                gridView.innerHTML = gridHtml;
                tableBody.innerHTML = tableHtml;
                applyLocalFilter();
            }

            // Delegación de clics (Modal edición, filtros y cambio de vista)
            document.addEventListener('click', function (e) {
                var editBtn = e.target && e.target.closest ? e.target.closest('.js-mar-edit') : null;
                if (editBtn) {
                    var id = editBtn.getAttribute('data-id');
                    var paddedId = String(id).padStart(4, '0');
                    document.getElementById('editNombre').value = editBtn.getAttribute('data-nombre') || '';
                    document.getElementById('editModelo').value = editBtn.getAttribute('data-modelo') || '';
                    document.getElementById('editDescripcion').value = editBtn.getAttribute('data-descripcion') || '';
                    document.getElementById('editModalBadge').textContent = 'MAR-' + paddedId;
                    document.getElementById('editForm').action = '/inventario/marcas/' + id;

                    var modalEl = document.getElementById('editMarcaModal');
                    if (window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                    return;
                }

                var filterBtn = e.target && e.target.closest ? e.target.closest('.js-mar-filter') : null;
                if (filterBtn) {
                    document.querySelectorAll('.js-mar-filter').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    filterBtn.classList.add('active');
                    currentFilter = filterBtn.getAttribute('data-filter') || 'all';
                    applyLocalFilter();
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

                    var gridEl = document.getElementById('marGridView');
                    var listEl = document.getElementById('marListView');
                    if (gridEl && listEl) {
                        gridEl.classList.toggle('d-none', currentView !== 'grid');
                        listEl.classList.toggle('d-none', currentView !== 'list');
                    }
                }
            });

            // Buscador en tiempo real contra el backend (AJAX JSON)
            var searchInput = document.getElementById('searchInput');
            var paginationWrapper = document.getElementById('paginationWrapper');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    var query = this.value.trim();

                    searchTimeout = setTimeout(function () {
                        fetch(`{{ route('inventario.marcas.index') }}?q=${encodeURIComponent(query)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            renderMarcas(data.items || []);
                            if (paginationWrapper) {
                                paginationWrapper.style.display = query.length > 0 ? 'none' : 'flex';
                            }
                        })
                        .catch(function (error) {
                            console.error('Error en la búsqueda:', error);
                        });
                    }, 300);
                });
            }
        })();
    </script>
</x-base-layout>
