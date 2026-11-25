{{-- resources/views/inventario/catalogos/clasificacion/index.blade.php · INV-CLA-v5.3-COLORGROUPS --}}
<x-base-layout>
    @php
        $totalSubgrupos = method_exists($subgrupos, 'total') ? $subgrupos->total() : collect($subgrupos ?? [])->count();
        $totalGrupos    = collect($grupos_list ?? [])->count();
        $totalLineas    = collect($lineas_list ?? [])->count();
        $totalTipos     = collect($tipos_list ?? [])->count();
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

        /* Paneles principales */
        .corp-panel {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        /* Pestañas estilo SaaS Corporativo */
        .corp-tabs {
            background: #f8fafc;
            border-bottom: 1px solid var(--corp-border);
            padding: 6px 10px 0 10px;
            gap: 4px;
        }
        .corp-tabs .nav-link {
            color: #64748b;
            font-size: 12.5px;
            font-weight: 600;
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 8px 14px;
            transition: all .15s ease;
        }
        .corp-tabs .nav-link:hover {
            color: var(--corp-ink);
            background: rgba(241, 245, 249, 0.8);
        }
        .corp-tabs .nav-link.active {
            color: var(--corp-accent);
            background: #ffffff;
            border-color: var(--corp-border) var(--corp-border) #ffffff;
            font-weight: 700;
        }

        /* Formularios corporativos */
        .corp-label {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
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

        /* Bloques de catálogos base */
        .corp-cat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            transition: all .2s ease;
        }
        .corp-cat-box.editing {
            border: 1.5px solid #1d4ed8 !important;
            background-color: #eff6ff !important;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.08) !important;
        }

        /* Badges jerárquicos sutiles */
        .badge-tipo {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
            font-weight: 600;
            font-size: 11px;
        }
        .badge-linea {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 600;
            font-size: 11px;
        }
        .badge-grupo {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
            font-weight: 600;
            font-size: 11px;
        }
        .badge-uso {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            font-weight: 600;
            font-size: 11px;
        }

        /* Barra de filtros dentro de tablas */
        .corp-filter-bar {
            background: #f8fafc;
            border-bottom: 1px solid var(--corp-border);
            padding: 12px 16px;
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
            padding: 10px 12px;
        }
        .corp-table tbody td {
            font-size: 13px;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            color: var(--corp-ink);
        }
        .sortable.asc::after {
            content: " ↑";
            color: var(--corp-accent);
        }
        .sortable.desc::after {
            content: " ↓";
            color: var(--corp-accent);
        }

        /* Descripción truncada elegante */
        .description-col {
            width: 140px;
            max-width: 140px;
        }
        .description-link {
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12.5px;
            max-width: 130px;
        }
        .description-link:hover {
            color: var(--corp-accent);
        }
        .description-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            max-width: 105px;
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

    {{-- MENSAJES FLASH --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Clasificación Jerárquica</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Estructura y Clasificación de Inventario
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Administra la taxonomía maestra de activos mediante niveles de Tipo, Línea, Grupo y Subgrupo.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button"
                                onclick="resetMain(); resetCats();"
                                class="btn btn-outline-secondary fw-semibold px-3 py-2"
                                style="font-size: 13px; border-radius: 9px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar Formularios
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
                    <a href="{{ route('inventario.clasificacion.index') }}" class="corp-nav-pill pill-conf active" title="Configuración">
                        <i class="bi bi-diagram-3-fill"></i> Clasificación
                    </a>
                    <a href="{{ route('inventario.marcas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-tags"></i> Marcas
                    </a>
                    <a href="{{ route('inventario.metodos-pago.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                        <i class="bi bi-credit-card"></i> Métodos Pago
                    </a>
                </div>
            </div>

            {{-- 2. INDICADORES EJECUTIVOS (KPIs) --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Subgrupos</div>
                            <div class="corp-kpi-value">{{ $totalSubgrupos }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Categorías finales</div>
                        </div>
                        <div class="corp-bod-avatar">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Grupos</div>
                            <div class="corp-kpi-value" style="color: #6d28d9;">{{ $totalGrupos }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Agrupaciones activas</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe;">
                            <i class="bi bi-collection"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Líneas</div>
                            <div class="corp-kpi-value text-success">{{ $totalLineas }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Ramas operativas</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                            <i class="bi bi-bezier2"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="corp-kpi h-100 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="corp-kpi-label mb-1">Tipos Contables</div>
                            <div class="corp-kpi-value text-dark">{{ $totalTipos }}</div>
                            <div class="text-muted mt-1" style="font-size: 11.5px;">Niveles raíz</div>
                        </div>
                        <div class="corp-bod-avatar" style="background: #f1f5f9; color: #334155; border-color: #e2e8f0;">
                            <i class="bi bi-layers"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. ÁREA DE TRABAJO: FORMULARIOS (IZQ) + AUDITORÍA (DER) --}}
            <div class="row g-3">

                {{-- COLUMNA IZQUIERDA: FORMULARIOS --}}
                <div class="col-lg-4 col-xl-3">
                    <div class="corp-panel sticky-top" style="top: 16px;">
                        <!-- PESTAÑAS -->
                        <ul class="nav nav-tabs corp-tabs" id="leftTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#form-main" type="button" role="tab" aria-controls="form-main" aria-selected="true">
                                    <i class="bi bi-link-45deg me-1"></i> Vincular
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cats-tab" data-bs-toggle="tab" data-bs-target="#form-cats" type="button" role="tab" aria-controls="form-cats" aria-selected="false">
                                    <i class="bi bi-folder-plus me-1"></i> Catálogos
                                </button>
                            </li>
                        </ul>

                        <!-- CONTENIDO DE PESTAÑAS -->
                        <div class="tab-content p-3" id="leftTabContent">

                            {{-- 1. FORMULARIO PRINCIPAL (SUBGRUPOS) --}}
                            <div class="tab-pane fade show active" id="form-main" role="tabpanel" aria-labelledby="main-tab">
                                <div class="mb-3 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0" id="title-main" style="font-size: 14.5px;">Nueva Vinculación</h6>
                                    <p class="text-muted mb-0" style="font-size: 12px;">Cree la categoría final uniendo los catálogos.</p>
                                </div>

                                <form id="formMain" action="{{ route('inventario.clasificacion.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="_method" id="methodMain" value="POST">

                                    <div class="mb-2">
                                        <label for="inpMainName" class="corp-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" id="inpMainName" class="form-control corp-input" required placeholder="Ej: Portátiles">
                                    </div>

                                    <div class="mb-2">
                                        <label for="inpMainDescripcion" class="corp-label">Descripción</label>
                                        <textarea name="descripcion" id="inpMainDescripcion" class="form-control corp-input" rows="2" placeholder="Descripción detallada del subgrupo..."></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label for="selMainTipo" class="corp-label">Tipo <span class="text-danger">*</span></label>
                                        <select name="id_InvTipos" id="selMainTipo" class="form-select corp-select" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label for="selMainLinea" class="corp-label">Línea <span class="text-danger">*</span></label>
                                        <select name="id_InvLineas" id="selMainLinea" class="form-select corp-select" required>
                                            <option value="">Seleccione primero un Tipo...</option>
                                            @foreach($lineas as $l)
                                                <option value="{{ $l->id }}" data-tipo="{{ $l->id_InvTipos }}">{{ $l->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="selMainGrupo" class="corp-label">Grupo <span class="text-danger">*</span></label>
                                        <select name="id_InvGrupos" id="selMainGrupo" class="form-select corp-select" required>
                                            <option value="">Seleccione primero una Línea...</option>
                                            @foreach($grupos as $g)
                                                <option value="{{ $g->id }}" data-linea="{{ $g->id_InvLineas }}">{{ $g->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary fw-semibold" id="btnMain" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                            Guardar Vinculación
                                        </button>
                                        <button type="button" onclick="resetMain()" class="btn btn-outline-secondary fw-semibold d-none" id="cancelMain" style="font-size: 13px;">
                                            Cancelar Edición
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- 2. FORMULARIOS CATÁLOGOS BASE --}}
                            <div class="tab-pane fade" id="form-cats" role="tabpanel" aria-labelledby="cats-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark m-0" style="font-size: 14px;">Catálogos Base</h6>
                                    <button type="button" onclick="resetCats()" class="btn btn-sm btn-light border fw-semibold" style="font-size: 11.5px;">
                                        Resetear
                                    </button>
                                </div>

                                {{-- Form TIPO --}}
                                <div class="corp-cat-box mb-3" id="box-tipo">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark" style="font-size: 11.5px; letter-spacing: .04em;">1. TIPO</span>
                                        <span class="badge badge-tipo" id="lbl-tipo">NUEVO</span>
                                    </div>
                                    <form id="formTipo" action="{{ route('inventario.clasificacion.tipo.store') }}" method="POST">
                                        @csrf <input type="hidden" name="_method" id="methodTipo" value="POST">
                                        <div class="mb-2">
                                            <input type="text" name="nombre" id="inpTipoName" class="form-control form-control-sm corp-input" placeholder="Nombre del tipo" required>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="descripcion" id="inpTipoDescripcion" class="form-control form-control-sm corp-input" rows="2" placeholder="Descripción del tipo..."></textarea>
                                        </div>
                                        <div class="d-grid">
                                            <button class="btn btn-sm btn-dark fw-semibold" type="submit" style="font-size: 12px;">Guardar Tipo</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Form LÍNEA --}}
                                <div class="corp-cat-box mb-3" id="box-linea">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark" style="font-size: 11.5px; letter-spacing: .04em;">2. LÍNEA</span>
                                        <span class="badge badge-linea" id="lbl-linea">NUEVO</span>
                                    </div>
                                    <form id="formLinea" action="{{ route('inventario.clasificacion.linea.store') }}" method="POST">
                                        @csrf <input type="hidden" name="_method" id="methodLinea" value="POST">
                                        <div class="mb-2">
                                            <select name="id_InvTipos" id="inpLineaParent" class="form-select form-select-sm corp-select" required>
                                                <option value="">Pertenece a Tipo...</option>
                                                @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="nombre" id="inpLineaName" class="form-control form-control-sm corp-input" placeholder="Nombre de la línea" required>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="descripcion" id="inpLineaDescripcion" class="form-control form-control-sm corp-input" rows="2" placeholder="Descripción de la línea..."></textarea>
                                        </div>
                                        <div class="d-grid">
                                            <button class="btn btn-sm btn-dark fw-semibold" type="submit" style="font-size: 12px;">Guardar Línea</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Form GRUPO --}}
                                <div class="corp-cat-box" id="box-grupo">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark" style="font-size: 11.5px; letter-spacing: .04em;">3. GRUPO</span>
                                        <span class="badge badge-grupo" id="lbl-grupo">NUEVO</span>
                                    </div>
                                    <form id="formGrupo" action="{{ route('inventario.clasificacion.grupo.store') }}" method="POST">
                                        @csrf <input type="hidden" name="_method" id="methodGrupo" value="POST">
                                        <div class="mb-2">
                                            <select name="id_InvLineas" id="inpGrupoParent" class="form-select form-select-sm corp-select" required>
                                                <option value="">Pertenece a Línea...</option>
                                                @foreach($lineas as $l) <option value="{{ $l->id }}">{{ $l->nombre }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="nombre" id="inpGrupoName" class="form-control form-control-sm corp-input" placeholder="Nombre del grupo" required>
                                        </div>
                                        <div class="mb-2">
                                            <textarea name="descripcion" id="inpGrupoDescripcion" class="form-control form-control-sm corp-input" rows="2" placeholder="Descripción del grupo..."></textarea>
                                        </div>
                                        <div class="d-grid">
                                            <button class="btn btn-sm btn-dark fw-semibold" type="submit" style="font-size: 12px;">Guardar Grupo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: LISTADOS DE AUDITORÍA --}}
                <div class="col-lg-8 col-xl-9">
                    <div class="corp-panel">
                        <!-- PESTAÑAS -->
                        <ul class="nav nav-tabs corp-tabs" id="rightTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="sub-tab" data-bs-toggle="tab" data-bs-target="#list-sub" type="button" role="tab" aria-controls="list-sub" aria-selected="true">
                                    Subgrupos <span class="text-muted ms-1">({{ $totalSubgrupos }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#list-grupos" type="button" role="tab" aria-controls="list-grupos" aria-selected="false">
                                    Grupos <span class="text-muted ms-1">({{ $totalGrupos }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="lineas-tab" data-bs-toggle="tab" data-bs-target="#list-lineas" type="button" role="tab" aria-controls="list-linea" aria-selected="false">
                                    Líneas <span class="text-muted ms-1">({{ $totalLineas }})</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tipos-tab" data-bs-toggle="tab" data-bs-target="#list-tipos" type="button" role="tab" aria-controls="list-tipos" aria-selected="false">
                                    Tipos <span class="text-muted ms-1">({{ $totalTipos }})</span>
                                </button>
                            </li>
                        </ul>

                        <!-- CONTENIDO DE PESTAÑAS -->
                        <div class="tab-content p-0" id="rightTabContent">

                            {{-- 1. TABLA SUBGRUPOS --}}
                            <div class="tab-pane fade show active" id="list-sub" role="tabpanel" aria-labelledby="sub-tab">
                                <!-- SECCIÓN DE FILTROS PARA SUBGRUPOS -->
                                <div class="corp-filter-bar">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-3">
                                            <div class="position-relative">
                                                <i class="bi bi-search text-muted position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12.5px;"></i>
                                                <input type="text" class="form-control corp-input ps-4" id="search-subgrupo" placeholder="Buscar subgrupo...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <select id="filter-tipo-sub" class="form-select corp-select">
                                                <option value="">Todos los Tipos</option>
                                                @foreach($tipos_list as $t)
                                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filter-linea-sub" class="form-select corp-select">
                                                <option value="">Todas las Líneas</option>
                                                @foreach($lineas_list as $l)
                                                <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select id="filter-grupo-sub" class="form-select corp-select">
                                                <option value="">Todos los Grupos</option>
                                                @foreach($grupos_list as $g)
                                                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button class="btn btn-light border fw-semibold" onclick="resetFilters('sub')" style="font-size: 12.5px; padding: 7px 10px;">
                                                <i class="bi bi-x-circle me-1"></i> Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div id="loading-sub" class="text-center p-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                    <table class="table corp-table mb-0" id="table-sub">
                                        <thead>
                                            <tr>
                                                <th class="sortable ps-3" onclick="sortTable('table-sub', 0)">Subgrupo</th>
                                                <th class="description-col">Descripción</th>
                                                <th class="sortable" onclick="sortTable('table-sub', 2)">Tipo</th>
                                                <th class="sortable" onclick="sortTable('table-sub', 3)">Línea</th>
                                                <th class="sortable" onclick="sortTable('table-sub', 4)">Grupo</th>
                                                <th class="text-end pe-3">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subgrupos as $s)
                                            <tr data-nombre="{{ strtolower($s->nombre) }}" data-tipo="{{ $s->id_InvTipos }}" data-linea="{{ $s->id_InvLineas }}" data-grupo="{{ $s->id_InvGrupos }}">
                                                <td class="ps-3 fw-bold text-dark">{{ $s->nombre }}</td>
                                                <td class="description-cell">
                                                    @if($s->descripcion)
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                                                           data-title="Descripción de {{ $s->nombre }}"
                                                           data-content="{{ $s->descripcion }}">
                                                            <span class="description-text">{{ $s->descripcion }}</span>
                                                            <i class="bi bi-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-tipo">{{ $s->tipo->nombre ?? '-' }}</span></td>
                                                <td><span class="badge badge-linea">{{ $s->linea->nombre ?? '-' }}</span></td>
                                                <td><span class="badge badge-grupo">{{ $s->grupo->nombre ?? '-' }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-light border text-dark" onclick='editMain(@json($s))' title="Editar" style="padding: 3px 8px;">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </button>
                                                        <form action="{{ route('inventario.clasificacion.destroy', $s->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar esta vinculación?');">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-light border text-danger" title="Eliminar" style="padding: 3px 8px;">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top bg-white" id="pagination-sub">
                                    <div class="d-flex justify-content-center">{{ $subgrupos->links() }}</div>
                                </div>
                            </div>

                            {{-- 2. TABLA GRUPOS --}}
                            <div class="tab-pane fade" id="list-grupos" role="tabpanel" aria-labelledby="grupos-tab">
                                <div class="corp-filter-bar">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <div class="position-relative">
                                                <i class="bi bi-search text-muted position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12.5px;"></i>
                                                <input type="text" class="form-control corp-input ps-4" id="search-grupo" placeholder="Buscar grupo...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <select id="filter-linea-grupo" class="form-select corp-select">
                                                <option value="">Todas las Líneas</option>
                                                @foreach($lineas_list as $l)
                                                <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button class="btn btn-light border fw-semibold" onclick="resetFilters('grupo')" style="font-size: 12.5px; padding: 7px 10px;">
                                                Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div id="loading-grupo" class="text-center p-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                    <table class="table corp-table mb-0" id="table-grupo">
                                        <thead>
                                            <tr>
                                                <th class="sortable ps-3" onclick="sortTable('table-grupo', 0)">Grupo</th>
                                                <th class="description-col">Descripción</th>
                                                <th class="sortable" onclick="sortTable('table-grupo', 2)">Pertenece a Línea</th>
                                                <th class="text-end pe-3">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grupos_list as $g)
                                            <tr data-nombre="{{ strtolower($g->nombre) }}" data-linea="{{ $g->id_InvLineas }}">
                                                <td class="ps-3 fw-bold text-dark">{{ $g->nombre }}</td>
                                                <td class="description-cell">
                                                    @if($g->descripcion)
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                                                           data-title="Descripción de {{ $g->nombre }}"
                                                           data-content="{{ $g->descripcion }}">
                                                            <span class="description-text">{{ $g->descripcion }}</span>
                                                            <i class="bi bi-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-linea">{{ $g->linea->nombre ?? 'Huérfano' }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-light border text-dark" onclick='editParam("grupo", @json($g))' title="Editar" style="padding: 3px 8px;">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </button>
                                                        @if($g->subgrupos_count > 0)
                                                            <button class="btn btn-sm btn-light border text-muted" title="En uso (no se puede borrar)" disabled style="padding: 3px 8px;">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        @else
                                                            <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $g->id, 'tipo' => 'grupo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar este grupo?');">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-sm btn-light border text-danger" title="Eliminar" style="padding: 3px 8px;">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- 3. TABLA LÍNEAS --}}
                            <div class="tab-pane fade" id="list-lineas" role="tabpanel" aria-labelledby="lineas-tab">
                                <div class="corp-filter-bar">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <div class="position-relative">
                                                <i class="bi bi-search text-muted position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12.5px;"></i>
                                                <input type="text" class="form-control corp-input ps-4" id="search-linea" placeholder="Buscar línea...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <select id="filter-tipo-linea" class="form-select corp-select">
                                                <option value="">Todos los Tipos</option>
                                                @foreach($tipos_list as $t)
                                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button class="btn btn-light border fw-semibold" onclick="resetFilters('linea')" style="font-size: 12.5px; padding: 7px 10px;">
                                                Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div id="loading-linea" class="text-center p-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                    <table class="table corp-table mb-0" id="table-linea">
                                        <thead>
                                            <tr>
                                                <th class="sortable ps-3" onclick="sortTable('table-linea', 0)">Línea</th>
                                                <th class="description-col">Descripción</th>
                                                <th class="sortable" onclick="sortTable('table-linea', 2)">Pertenece a Tipo</th>
                                                <th class="text-end pe-3">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lineas_list as $l)
                                            <tr data-nombre="{{ strtolower($l->nombre) }}" data-tipo="{{ $l->id_InvTipos }}">
                                                <td class="ps-3 fw-bold text-dark">{{ $l->nombre }}</td>
                                                <td class="description-cell">
                                                    @if($l->descripcion)
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                                                           data-title="Descripción de {{ $l->nombre }}"
                                                           data-content="{{ $l->descripcion }}">
                                                            <span class="description-text">{{ $l->descripcion }}</span>
                                                            <i class="bi bi-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge badge-tipo">{{ $l->tipo->nombre ?? 'Huérfano' }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-light border text-dark" onclick='editParam("linea", @json($l))' title="Editar" style="padding: 3px 8px;">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </button>
                                                        @if($l->grupos_count > 0 || $l->subgrupos_count > 0)
                                                            <button class="btn btn-sm btn-light border text-muted" title="En uso (no se puede borrar)" disabled style="padding: 3px 8px;">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        @else
                                                            <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $l->id, 'tipo' => 'linea']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar esta línea?');">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-sm btn-light border text-danger" title="Eliminar" style="padding: 3px 8px;">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- 4. TABLA TIPOS --}}
                            <div class="tab-pane fade" id="list-tipos" role="tabpanel" aria-labelledby="tipos-tab">
                                <div class="corp-filter-bar">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-10">
                                            <div class="position-relative">
                                                <i class="bi bi-search text-muted position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12.5px;"></i>
                                                <input type="text" class="form-control corp-input ps-4" id="search-tipo" placeholder="Buscar tipo contable...">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button class="btn btn-light border fw-semibold" onclick="resetFilters('tipo')" style="font-size: 12.5px; padding: 7px 10px;">
                                                Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div id="loading-tipo" class="text-center p-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                    <table class="table corp-table mb-0" id="table-tipo">
                                        <thead>
                                            <tr>
                                                <th class="sortable ps-3" onclick="sortTable('table-tipo', 0)">Tipo Contable</th>
                                                <th class="description-col">Descripción</th>
                                                <th class="sortable" onclick="sortTable('table-tipo', 2)">Uso</th>
                                                <th class="text-end pe-3">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tipos_list as $t)
                                            <tr data-nombre="{{ strtolower($t->nombre) }}">
                                                <td class="ps-3 fw-bold text-dark">{{ $t->nombre }}</td>
                                                <td class="description-cell">
                                                    @if($t->descripcion)
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal"
                                                           data-title="Descripción de {{ $t->nombre }}"
                                                           data-content="{{ $t->descripcion }}">
                                                            <span class="description-text">{{ $t->descripcion }}</span>
                                                            <i class="bi bi-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge badge-tipo">{{ $t->lineas_count }} Líneas</span>
                                                        <span class="badge badge-uso">{{ $t->subgrupos_count }} Subgrupos</span>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-light border text-dark" onclick='editParam("tipo", @json($t))' title="Editar" style="padding: 3px 8px;">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </button>
                                                        @if($t->lineas_count > 0 || $t->subgrupos_count > 0)
                                                            <button class="btn btn-sm btn-light border text-muted" title="En uso (no se puede borrar)" disabled style="padding: 3px 8px;">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        @else
                                                            <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $t->id, 'tipo' => 'tipo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar este tipo?');">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-sm btn-light border text-danger" title="Eliminar" style="padding: 3px 8px;">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
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
    </div>

    <!-- Modal para mostrar descripciones -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom px-4 py-3" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="corp-bod-avatar" style="width: 34px; height: 34px; font-size: 15px;">
                            <i class="bi bi-card-text"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="descriptionModalLabel">Descripción</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p id="descriptionContent" class="text-secondary mb-0" style="font-size: 13.5px; line-height: 1.55;"></p>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold px-3" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- LÓGICA JAVASCRIPT --}}
    <script>
        let filterTimeout;

        // --- 1. EDITAR VINCULACIÓN (PRINCIPAL) ---
        function editMain(data) {
            const mainTab = new bootstrap.Tab(document.getElementById('main-tab'));
            mainTab.show();

            document.getElementById('title-main').innerText = "Editando Subgrupo #" + data.id;
            document.getElementById('btnMain').innerHTML = 'Actualizar Vinculación';
            document.getElementById('btnMain').className = "btn btn-dark fw-semibold w-100";
            document.getElementById('cancelMain').className = "btn btn-outline-secondary fw-semibold w-100";

            const form = document.getElementById('formMain');
            form.action = "{{ url('inventario/clasificacion') }}/" + data.id;
            document.getElementById('methodMain').value = "PUT";

            document.getElementById('inpMainName').value = data.nombre;
            document.getElementById('inpMainDescripcion').value = data.descripcion || '';
            document.getElementById('selMainGrupo').value = data.id_InvGrupos;
            document.getElementById('selMainLinea').value = data.id_InvLineas;
            document.getElementById('selMainTipo').value = data.id_InvTipos;
        }

        function resetMain() {
            document.getElementById('title-main').innerText = "Nueva Vinculación";
            document.getElementById('btnMain').innerHTML = 'Guardar Vinculación';
            document.getElementById('btnMain').className = "btn btn-primary fw-semibold w-100";
            document.getElementById('btnMain').style.backgroundColor = "#1d4ed8";
            document.getElementById('btnMain').style.borderColor = "#1d4ed8";
            document.getElementById('cancelMain').className = "btn btn-outline-secondary fw-semibold w-100 d-none";

            const descripcionField = document.getElementById('inpMainDescripcion');
            descripcionField.value = '';
            descripcionField.dataset.originalValue = '';
            descripcionField.dataset.editing = 'false';

            document.getElementById('formMain').reset();
            document.getElementById('formMain').action = "{{ route('inventario.clasificacion.store') }}";
            document.getElementById('methodMain').value = "POST";
        }

        // --- 2. EDITAR CATÁLOGOS BASE (TIPO, LÍNEA, GRUPO) ---
        function editParam(type, data) {
            const catsTab = new bootstrap.Tab(document.getElementById('cats-tab'));
            catsTab.show();

            resetCats();

            const box = document.getElementById('box-' + type);
            box.classList.add('editing');
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.getElementById('lbl-' + type).innerText = "EDITANDO #" + data.id;

            const form = document.getElementById('form' + capitalize(type));
            form.action = "{{ url('inventario/clasificacion/update-') }}" + type + "/" + data.id;
            document.getElementById('method' + capitalize(type)).value = "PUT";

            document.getElementById('inp' + capitalize(type) + 'Name').value = data.nombre;

            const descField = document.getElementById('inp' + capitalize(type) + 'Descripcion');
            if (descField) {
                descField.value = data.descripcion || '';
            }

            if(type === 'linea') document.getElementById('inpLineaParent').value = data.id_InvTipos;
            if(type === 'grupo') document.getElementById('inpGrupoParent').value = data.id_InvLineas;
        }

        function resetCats() {
            ['tipo', 'linea', 'grupo'].forEach(t => {
                document.getElementById('box-' + t).classList.remove('editing');
                document.getElementById('lbl-' + t).innerText = "NUEVO";

                const form = document.getElementById('form' + capitalize(t));
                form.reset();
                form.action = "{{ url('inventario/clasificacion/store-') }}" + t;
                document.getElementById('method' + capitalize(t)).value = "POST";
            });
        }

        function capitalize(str) { return str.charAt(0).toUpperCase() + str.slice(1); }

        // --- 3. FILTRADO AJAX CON DEBOUNCE ---
        function applyFiltersAjax(type) {
            clearTimeout(filterTimeout);

            filterTimeout = setTimeout(() => {
                let params = {};
                const loadingEl = document.getElementById(`loading-${type}`);
                if (loadingEl) loadingEl.style.display = 'block';

                switch(type) {
                    case 'sub':
                        params.search_sub = document.getElementById('search-subgrupo').value;
                        params.filter_tipo_sub = document.getElementById('filter-tipo-sub').value;
                        params.filter_linea_sub = document.getElementById('filter-linea-sub').value;
                        params.filter_grupo_sub = document.getElementById('filter-grupo-sub').value;
                        break;
                    case 'grupo':
                        params.search_grupo = document.getElementById('search-grupo').value;
                        params.filter_linea_grupo = document.getElementById('filter-linea-grupo').value;
                        break;
                    case 'linea':
                        params.search_linea = document.getElementById('search-linea').value;
                        params.filter_tipo_linea = document.getElementById('filter-tipo-linea').value;
                        break;
                    case 'tipo':
                        params.search_tipo = document.getElementById('search-tipo').value;
                        break;
                }

                fetch(`{{ route('inventario.clasificacion.index') }}?${new URLSearchParams(params)}`)
                    .then(response => response.text())
                    .then(html => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        const newTable = tempDiv.querySelector(`#table-${type} tbody`);
                        const newPagination = tempDiv.querySelector(`#pagination-${type} .d-flex`);

                        const currentTable = document.querySelector(`#table-${type} tbody`);
                        if (currentTable && newTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                        }

                        const currentPagination = document.querySelector(`#pagination-${type} .d-flex`);
                        if (currentPagination && newPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                            assignPaginationEvents(type);
                        }

                        assignDescriptionEvents();
                    })
                    .catch(error => console.error('Error al filtrar:', error))
                    .finally(() => {
                        if (loadingEl) loadingEl.style.display = 'none';
                    });
            }, 300);
        }

        // --- 4. ASIGNAR EVENTOS A PAGINACIÓN ---
        function assignPaginationEvents(type) {
            const paginationLinks = document.querySelectorAll(`#pagination-${type} a`);
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const loadingEl = document.getElementById(`loading-${type}`);
                    if (loadingEl) loadingEl.style.display = 'block';

                    const url = this.getAttribute('href');

                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;

                            const newTable = tempDiv.querySelector(`#table-${type} tbody`);
                            const newPagination = tempDiv.querySelector(`#pagination-${type} .d-flex`);

                            const currentTable = document.querySelector(`#table-${type} tbody`);
                            if (currentTable && newTable) {
                                currentTable.innerHTML = newTable.innerHTML;
                            }

                            const currentPagination = document.querySelector(`#pagination-${type} .d-flex`);
                            if (currentPagination && newPagination) {
                                currentPagination.innerHTML = newPagination.innerHTML;
                                assignPaginationEvents(type);
                            }

                            assignDescriptionEvents();
                        })
                        .catch(error => console.error('Error en paginación:', error))
                        .finally(() => {
                            if (loadingEl) loadingEl.style.display = 'none';
                        });
                });
            });
        }

        function resetFilters(type) {
            let searchId, filterIds = [];

            switch(type) {
                case 'sub':
                    searchId = 'search-subgrupo';
                    filterIds = ['filter-tipo-sub', 'filter-linea-sub', 'filter-grupo-sub'];
                    break;
                case 'grupo':
                    searchId = 'search-grupo';
                    filterIds = ['filter-linea-grupo'];
                    break;
                case 'linea':
                    searchId = 'search-linea';
                    filterIds = ['filter-tipo-linea'];
                    break;
                case 'tipo':
                    searchId = 'search-tipo';
                    break;
            }

            document.getElementById(searchId).value = '';
            filterIds.forEach(id => {
                if (document.getElementById(id)) document.getElementById(id).value = '';
            });

            applyFiltersAjax(type);
        }

        // --- 5. FUNCIONES DE ORDENAMIENTO ---
        const sortDirections = {};

        function sortTable(tableId, columnIndex) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const headers = table.querySelectorAll('th');

            if (!sortDirections[tableId]) sortDirections[tableId] = {};

            const currentDirection = sortDirections[tableId][columnIndex] || 'asc';
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            sortDirections[tableId][columnIndex] = newDirection;

            headers.forEach((header, index) => {
                header.classList.remove('asc', 'desc');
                if (index === columnIndex) header.classList.add(newDirection);
            });

            const getCellValue = (row, colIndex) => {
                const cell = row.cells[colIndex];
                if (!cell) return '';
                return cell.textContent.trim().toLowerCase();
            };

            rows.sort((a, b) => {
                const aValue = getCellValue(a, columnIndex);
                const bValue = getCellValue(b, columnIndex);
                if (aValue < bValue) return newDirection === 'asc' ? -1 : 1;
                if (aValue > bValue) return newDirection === 'asc' ? 1 : -1;
                return 0;
            });

            rows.forEach((row) => tbody.appendChild(row));
        }

        // --- 6. MANEJO DE MODAL DE DESCRIPCIÓN ---
        function assignDescriptionEvents() {
            const descriptionLinks = document.querySelectorAll('.description-link');
            descriptionLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('descriptionModalLabel').textContent = this.getAttribute('data-title');
                    document.getElementById('descriptionContent').textContent = this.getAttribute('data-content');
                });
            });
        }

        // --- 7. INICIALIZACIÓN DE EVENTOS ---
        document.addEventListener('DOMContentLoaded', function() {
            const searchSubgrupo = document.getElementById('search-subgrupo');
            const filterTipoSub = document.getElementById('filter-tipo-sub');
            const filterLineaSub = document.getElementById('filter-linea-sub');
            const filterGrupoSub = document.getElementById('filter-grupo-sub');

            if (searchSubgrupo) searchSubgrupo.addEventListener('input', () => applyFiltersAjax('sub'));
            if (filterTipoSub) filterTipoSub.addEventListener('change', () => applyFiltersAjax('sub'));
            if (filterLineaSub) filterLineaSub.addEventListener('change', () => applyFiltersAjax('sub'));
            if (filterGrupoSub) filterGrupoSub.addEventListener('change', () => applyFiltersAjax('sub'));

            const searchGrupo = document.getElementById('search-grupo');
            const filterLineaGrupo = document.getElementById('filter-linea-grupo');
            if (searchGrupo) searchGrupo.addEventListener('input', () => applyFiltersAjax('grupo'));
            if (filterLineaGrupo) filterLineaGrupo.addEventListener('change', () => applyFiltersAjax('grupo'));

            const searchLinea = document.getElementById('search-linea');
            const filterTipoLinea = document.getElementById('filter-tipo-linea');
            if (searchLinea) searchLinea.addEventListener('input', () => applyFiltersAjax('linea'));
            if (filterTipoLinea) filterTipoLinea.addEventListener('change', () => applyFiltersAjax('linea'));

            const searchTipo = document.getElementById('search-tipo');
            if (searchTipo) searchTipo.addEventListener('input', () => applyFiltersAjax('tipo'));

            ['sub', 'grupo', 'linea', 'tipo'].forEach(type => assignPaginationEvents(type));
            assignDescriptionEvents();

            // Selects en cascada (TIPO -> LÍNEA -> GRUPO)
            const selMainTipo = document.getElementById('selMainTipo');
            const selMainLinea = document.getElementById('selMainLinea');
            const selMainGrupo = document.getElementById('selMainGrupo');

            if (selMainTipo && selMainLinea && selMainGrupo) {
                const allLineaOptions = Array.from(selMainLinea.querySelectorAll('option'));
                const allGrupoOptions = Array.from(selMainGrupo.querySelectorAll('option'));

                selMainTipo.addEventListener('change', function() {
                    const selectedTipoId = this.value;
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    const currentDescripcion = descripcionField ? descripcionField.value : '';

                    selMainLinea.innerHTML = '';
                    selMainLinea.readonly = true;

                    selMainGrupo.innerHTML = '<option value="">Seleccione primero una Línea...</option>';
                    selMainGrupo.readonly = true;

                    if (selectedTipoId === '') {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Seleccione primero un Tipo...';
                        selMainLinea.appendChild(option);
                    } else {
                        const filteredLineas = allLineaOptions.filter(option => {
                            return option.value === '' || option.dataset.tipo === selectedTipoId;
                        });
                        filteredLineas.forEach(option => selMainLinea.appendChild(option));
                        selMainLinea.readonly = false;
                    }

                    if (descripcionField && descripcionField.dataset.editing !== 'true') {
                        descripcionField.value = currentDescripcion;
                    }
                });

                selMainLinea.addEventListener('change', function() {
                    const selectedLineaId = this.value;
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    const currentDescripcion = descripcionField ? descripcionField.value : '';

                    selMainGrupo.innerHTML = '';

                    if (selectedLineaId === '') {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Seleccione primero una Línea...';
                        selMainGrupo.appendChild(option);
                        selMainGrupo.disabled = true;
                    } else {
                        const filteredGrupos = allGrupoOptions.filter(option => {
                            return option.value === '' || option.dataset.linea === selectedLineaId;
                        });
                        filteredGrupos.forEach(option => selMainGrupo.appendChild(option));
                        selMainGrupo.disabled = false;
                    }

                    if (descripcionField && descripcionField.dataset.editing !== 'true') {
                        descripcionField.value = currentDescripcion;
                    }
                });

                if (selMainTipo.value === '') {
                    selMainLinea.readonly = true;
                    selMainGrupo.readonly = true;
                }
            }

            const inpMainDescripcion = document.getElementById('inpMainDescripcion');
            if (inpMainDescripcion) {
                inpMainDescripcion.addEventListener('focus', function() {
                    this.dataset.currentValue = this.value;
                });
                inpMainDescripcion.addEventListener('blur', function() {
                    if (!this.value && this.dataset.currentValue) {
                        this.value = this.dataset.currentValue;
                    }
                });
                inpMainDescripcion.addEventListener('change', function() {
                    this.dataset.originalValue = this.value;
                });
            }

            const formMain = document.getElementById('formMain');
            if (formMain) {
                formMain.addEventListener('submit', function(e) {
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    if (descripcionField) {
                        if (!descripcionField.value && descripcionField.dataset.originalValue) {
                            descripcionField.value = descripcionField.dataset.originalValue;
                        }
                        descripcionField.dataset.originalValue = descripcionField.value;
                        descripcionField.dataset.editing = 'false';
                    }
                });
            }
        });
    </script>
</x-base-layout>
