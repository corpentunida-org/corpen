{{-- resources/views/inventario/movimientos/create.blade.php · INV-MOV-CREATE-v5.3-COLORGROUPS --}}
<x-base-layout>
    @php
        $usuariosJson = $usuarios->map(fn($u) => ['id' => $u->id, 'text' => $u->name])->values()->toJson();
        $totalActivosDisp = collect($activosDisponibles ?? [])->count();
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
            display: flex;
            flex-direction: column;
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
            flex-grow: 1;
        }

        /* Formularios corporativos */
        .corp-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }
        .corp-input, .corp-select, .corp-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px;
            background-color: #ffffff;
            color: var(--corp-ink);
            transition: all .15s ease;
        }
        .corp-input:focus, .corp-select:focus, .corp-textarea:focus {
            outline: none;
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }
        .corp-input-readonly {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            font-weight: 700;
            border-color: #e2e8f0 !important;
        }

        /* Indicador de estado */
        .visual-state-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            display: inline-block;
            transition: all .2s ease;
        }

        /* Segmentos de filtro estilo SaaS */
        .corp-segmented {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 9px;
            display: inline-flex;
            gap: 2px;
            overflow-x: auto;
            max-width: 100%;
        }
        .corp-segmented .filter-tab {
            border: none;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: transparent;
            padding: 5px 12px;
            white-space: nowrap;
            cursor: pointer;
            transition: all .15s ease;
        }
        .corp-segmented .filter-tab:hover {
            color: var(--corp-ink);
        }
        .corp-segmented .filter-tab.active {
            background: #ffffff;
            color: var(--corp-ink);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.1);
        }

        /* Lista de activos seleccionables */
        .list-container {
            max-height: 540px;
            overflow-y: auto;
            border: 1px solid var(--corp-border);
            border-radius: 10px;
            background: #ffffff;
        }
        .item-row {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color .15s ease;
            cursor: pointer;
            margin: 0;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-row:hover {
            background: #f8fafc;
        }
        .item-row.selected {
            background: #eff6ff;
            border-left: 4px solid #1d4ed8;
        }

        .chk {
            width: 17px;
            height: 17px;
            margin-right: 14px;
            accent-color: #1d4ed8;
            cursor: pointer;
            flex-shrink: 0;
        }

        .hidden-by-filter {
            display: none !important;
        }

        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 2px 7px;
            border-radius: 5px;
        }

        .status-pill {
            font-size: 10.5px;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 8px;
            border: 1px solid transparent;
        }
        .st-disp { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
        .st-asig { background: #fffbeb; color: #d97706; border-color: #fde68a; }
        .st-mant { background: #eff6ff; color: #1d4ed8; border-color: #dbeafe; }

        /* Autocompletado de funcionarios */
        .autocomplete-wrapper {
            position: relative;
        }
        .autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-top: 4px;
            max-height: 210px;
            overflow-y: auto;
            z-index: 60;
            display: none;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.12);
        }
        .autocomplete-item {
            padding: 9px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }
        .autocomplete-item:last-child {
            border-bottom: none;
        }
        .autocomplete-item:hover {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .clear-btn {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            display: none;
            font-size: 13px;
        }
        .clear-btn:hover {
            color: #ef4444;
        }
    </style>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

    <div class="container-fluid py-2 px-2 px-md-3">
        <div class="corp-shell">

            <form action="{{ route('inventario.movimientos.store') }}" method="POST" id="movimientoForm">
                @csrf

                {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
                <div class="corp-header mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <nav aria-label="breadcrumb" class="mb-1">
                                <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                    <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                    <li class="breadcrumb-item text-muted">Operaciones</li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Crear Acta / Movimiento</li>
                                </ol>
                            </nav>
                            <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                                Registrar Acta de Movimiento / Asignación
                            </h4>
                            <p class="text-secondary mb-0" style="font-size: 13.5px;">
                                Genera actas de entrega, traslado o devolución vinculando uno o múltiples activos a un funcionario responsable.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inventario.movimientos.index') }}"
                               class="btn btn-outline-secondary fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-arrow-left me-1"></i> Bitácora
                            </a>
                            <button type="submit"
                                    class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                    style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-check2-circle me-1"></i> Finalizar y Registrar
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
                        <a href="{{ route('inventario.movimientos.create') }}" class="corp-nav-pill pill-ops active" title="Operaciones">
                            <i class="bi bi-file-earmark-arrow-up-fill"></i> + Acta / Mov.
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

                {{-- 2. DISEÑO DIVIDIDO: CONFIGURACIÓN (IZQ) + SELECCIÓN DE ACTIVOS (DER) --}}
                <div class="row g-3 align-items-start">

                    {{-- PANEL IZQUIERDO: CONFIGURACIÓN DEL ACTA --}}
                    <div class="col-12 col-lg-4">
                        <div class="corp-panel sticky-top" style="top: 16px;">
                            <div class="corp-panel-head">
                                <h6 class="corp-panel-title">
                                    <i class="bi bi-file-earmark-text text-primary"></i> Configuración del Acta
                                </h6>
                            </div>
                            <div class="corp-panel-body">

                                <div class="mb-3">
                                    <label class="corp-label">Código Único (Automático)</label>
                                    <input type="text" name="codigo_acta" class="corp-input corp-input-readonly font-monospace" value="ACT-{{ date('Ymd-His') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label text-primary">Bodega de Origen (Filtro Visual)</label>
                                    <select id="bodega_select" class="corp-select" style="border-color: #93c5fd; background-color: #f8fbff;">
                                        <option value="">Seleccione Bodega para filtrar...</option>
                                        @foreach($bodegas as $bodega)
                                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label">Tipo de Movimiento (Estado Resultante) <span class="text-danger">*</span></label>
                                    <select name="id_InvTiposRegistros" id="tipo_movimiento_select" class="corp-select" required disabled>
                                        <option value="">Primero seleccione una bodega...</option>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo->id }}" data-bodega="{{ $tipo->id_bodega }}">
                                                {{ $tipo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 p-2 px-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                                    <span class="corp-label mb-1">Indicador de Estado Resultante</span>
                                    <span id="visual_estado" class="visual-state-badge">Esperando selección...</span>
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label">Funcionario Responsable / Destino <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrapper">
                                        <div class="position-relative">
                                            <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                            <input type="text" id="search_funcionario" class="corp-input ps-4 pe-4" placeholder="Escribe para buscar funcionario..." autocomplete="off">
                                            <span class="clear-btn" id="clear_funcionario" title="Limpiar selección"><i class="bi bi-x-lg"></i></span>
                                        </div>
                                        <input type="hidden" name="id_usersAsignado" id="id_usersAsignado" required>
                                        <div id="list_funcionario" class="autocomplete-list"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label">Observaciones Generales</label>
                                    <textarea name="observacion_general" class="corp-textarea" rows="3" placeholder="Detalles del acta o motivo de devolución..."></textarea>
                                </div>

                                <button type="submit"
                                        class="btn btn-primary fw-bold w-100 py-2 shadow-sm"
                                        style="font-size: 13.5px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Finalizar y Registrar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- PANEL DERECHO: SELECCIÓN DE ACTIVOS --}}
                    <div class="col-12 col-lg-8">
                        <div class="corp-panel">
                            <div class="corp-panel-head">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h6 class="corp-panel-title">
                                        <i class="bi bi-cpu text-primary"></i> Base Global de Activos
                                    </h6>
                                    <span id="counter_badge" class="badge text-bg-primary" style="font-size: 11.5px; padding: 5px 10px; border-radius: 7px;">
                                        0 seleccionados
                                    </span>
                                    <span class="text-muted" style="font-size: 11.5px;">({{ $totalActivosDisp }} disponibles en lista)</span>
                                </div>
                                <button type="button"
                                        id="btn_select_all"
                                        class="btn btn-sm btn-light border fw-semibold text-primary px-3"
                                        style="font-size: 12px; border-radius: 8px;">
                                    <i class="bi bi-check2-all me-1"></i> Seleccionar Todos los Visibles
                                </button>
                            </div>

                            <div class="corp-panel-body" style="background: #fbfcfd;">

                                {{-- Filtros segmentados + Buscador --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <div class="corp-segmented" id="quickFilters" role="group">
                                        <button type="button" class="filter-tab active" data-filter="all">Todos</button>
                                        <button type="button" class="filter-tab" data-filter="disponible">Solo Disponibles</button>
                                        <button type="button" class="filter-tab" data-filter="asignado">Equipos Asignados</button>
                                        <button type="button" class="filter-tab" data-filter="otros">Otros (Mant., etc)</button>
                                    </div>

                                    <div class="position-relative flex-grow-1" style="max-width: 360px;">
                                        <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                        <input type="text" id="searchInput" class="corp-input ps-4" placeholder="Buscar nombre, código, serial o custodio...">
                                    </div>
                                </div>

                                {{-- Lista de Activos --}}
                                <div class="list-container" id="activosList">
                                    @forelse($activosDisponibles as $activo)
                                        @php
                                            $nombreEstado = strtolower($activo->estado->nombre ?? '');
                                            $claseEstado = match(true) {
                                                str_contains($nombreEstado, 'disponible') => 'st-disp',
                                                str_contains($nombreEstado, 'asignado') => 'st-asig',
                                                default => 'st-mant',
                                            };
                                            $categoriaFiltro = match(true) {
                                                str_contains($nombreEstado, 'disponible') => 'disponible',
                                                str_contains($nombreEstado, 'asignado') => 'asignado',
                                                default => 'otros',
                                            };
                                        @endphp

                                        <label class="item-row asset-item"
                                               data-bodega-id="{{ $activo->referencia->id_InvBodegas ?? '' }}"
                                               data-categoria="{{ $categoriaFiltro }}">

                                            <input type="checkbox" name="activos_seleccionados[]" value="{{ $activo->id }}" class="chk">

                                            <div class="flex-grow-1 min-w-0 pe-2">
                                                <div class="d-flex align-items-center flex-wrap gap-1">
                                                    <span class="fw-bold text-dark" style="font-size: 14px;">{{ $activo->nombre }}</span>
                                                    <span class="status-pill {{ $claseEstado }}">{{ $activo->estado->nombre ?? 'N/A' }}</span>
                                                </div>
                                                <div class="text-muted mt-1" style="font-size: 12px;">
                                                    Cod: <span class="corp-code">{{ $activo->codigo_activo }}</span>
                                                    <span class="mx-1">·</span>
                                                    S/N: <span class="font-monospace text-dark">{{ $activo->serial ?? 'N/A' }}</span>
                                                </div>

                                                @if($activo->usuarioAsignado)
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 11px; font-weight: 600;">
                                                            <i class="bi bi-person-fill text-warning me-1"></i>En poder de: {{ $activo->usuarioAsignado->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="text-end flex-shrink-0">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px; font-weight: 700;">
                                                    {{ $activo->referencia->marca->nombre ?? 'N/A' }}
                                                </span>
                                                <small class="d-block mt-1 text-muted" style="font-size: 11px;">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $activo->referencia->bodega->nombre ?? 'Bodega N/A' }}
                                                </small>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-box-seam d-block mb-2 opacity-50" style="font-size: 32px;"></i>
                                            <div class="fw-semibold">No hay activos registrados en el sistema.</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. LÓGICA DEL BUSCADOR DE FUNCIONARIOS ---
            const dataUsuarios = {!! $usuariosJson !!};
            const inputFuncionario = document.getElementById('search_funcionario');
            const hiddenId = document.getElementById('id_usersAsignado');
            const listContainer = document.getElementById('list_funcionario');
            const clearBtn = document.getElementById('clear_funcionario');

            inputFuncionario.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                listContainer.innerHTML = '';
                hiddenId.value = '';

                if (val.length === 0) {
                    listContainer.style.display = 'none';
                    clearBtn.style.display = 'none';
                    return;
                }

                clearBtn.style.display = 'block';
                const matches = dataUsuarios.filter(item => item.text.toLowerCase().includes(val)).slice(0, 10);

                if (matches.length > 0) {
                    matches.forEach(match => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = match.text;

                        div.addEventListener('click', function() {
                            inputFuncionario.value = match.text;
                            hiddenId.value = match.id;
                            listContainer.style.display = 'none';
                        });
                        listContainer.appendChild(div);
                    });
                    listContainer.style.display = 'block';
                } else {
                    listContainer.innerHTML = '<div class="autocomplete-item" style="color: #ef4444; pointer-events: none;">No se encontraron resultados...</div>';
                    listContainer.style.display = 'block';
                }
            });

            clearBtn.addEventListener('click', function() {
                inputFuncionario.value = '';
                hiddenId.value = '';
                listContainer.style.display = 'none';
                this.style.display = 'none';
                inputFuncionario.focus();
            });

            document.addEventListener('click', function(e) {
                if (e.target !== inputFuncionario) {
                    listContainer.style.display = 'none';
                }
            });

            document.getElementById('movimientoForm').addEventListener('submit', function(e) {
                if(!hiddenId.value) {
                    e.preventDefault();
                    alert('⚠️ Por favor, busca y selecciona un Funcionario Responsable del listado desplegable.');
                    inputFuncionario.focus();
                    return;
                }

                const checkedCount = document.querySelectorAll('.chk:checked').length;
                if(checkedCount === 0) {
                    e.preventDefault();
                    alert('⚠️ Debes seleccionar al menos un equipo de la lista de la derecha para registrar el acta.');
                }
            });

            // --- 2. LÓGICA DE FILTROS Y SELECCIÓN MÚLTIPLE ---
            const bodegaSelect = document.getElementById('bodega_select');
            const tipoSelect = document.getElementById('tipo_movimiento_select');
            const visualEstado = document.getElementById('visual_estado');
            const assetItems = document.querySelectorAll('.asset-item');
            const searchInput = document.getElementById('searchInput');
            const filterTabs = document.querySelectorAll('.filter-tab');
            const counterBadge = document.getElementById('counter_badge');

            const masterTipoOptions = Array.from(tipoSelect.options).filter(opt => opt.value !== "");

            function updateSelectionVisuals() {
                const checkedBoxes = document.querySelectorAll('.chk:checked');
                counterBadge.textContent = checkedBoxes.length + (checkedBoxes.length === 1 ? ' seleccionado' : ' seleccionados');

                assetItems.forEach(item => {
                    const checkbox = item.querySelector('.chk');
                    if (checkbox.checked) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                });
            }

            document.querySelectorAll('.chk').forEach(chk => {
                chk.addEventListener('change', updateSelectionVisuals);
            });

            bodegaSelect.addEventListener('change', function() {
                const selectedBodegaId = this.value;

                tipoSelect.innerHTML = '<option value="">Seleccione el tipo...</option>';
                if (selectedBodegaId) {
                    tipoSelect.disabled = false;
                    const filteredTypes = masterTipoOptions.filter(opt => opt.getAttribute('data-bodega') == selectedBodegaId);

                    if(filteredTypes.length > 0) {
                        filteredTypes.forEach(opt => tipoSelect.appendChild(opt.cloneNode(true)));
                    } else {
                        tipoSelect.innerHTML = '<option value="">Sin estados para esta bodega</option>';
                    }
                } else {
                    tipoSelect.disabled = true;
                }

                applyAllFilters();
                tipoSelect.dispatchEvent(new Event('change'));
            });

            tipoSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedText = this.options[this.selectedIndex].text;
                    const cleanText = selectedText.toLowerCase();

                    visualEstado.textContent = selectedText.toUpperCase();

                    if (cleanText.includes('disponible') || cleanText.includes('entregado')) {
                        visualEstado.style.background = '#ecfdf5'; visualEstado.style.color = '#059669'; visualEstado.style.borderColor = '#a7f3d0';
                    } else if (cleanText.includes('asignado') || cleanText.includes('prestamo')) {
                        visualEstado.style.background = '#fffbeb'; visualEstado.style.color = '#d97706'; visualEstado.style.borderColor = '#fde68a';
                    } else if (cleanText.includes('baja') || cleanText.includes('dañado')) {
                        visualEstado.style.background = '#fef2f2'; visualEstado.style.color = '#dc2626'; visualEstado.style.borderColor = '#fecaca';
                    } else {
                        visualEstado.style.background = '#eff6ff'; visualEstado.style.color = '#1d4ed8'; visualEstado.style.borderColor = '#dbeafe';
                    }
                } else {
                    visualEstado.textContent = 'ESPERANDO SELECCIÓN...';
                    visualEstado.style.background = '#f1f5f9'; visualEstado.style.color = '#475569'; visualEstado.style.borderColor = '#e2e8f0';
                }
            });

            let currentTabFilter = 'all';

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentTabFilter = this.getAttribute('data-filter');
                    applyAllFilters();
                });
            });

            searchInput.addEventListener('input', applyAllFilters);

            function applyAllFilters() {
                const selectedBodegaId = bodegaSelect.value;
                const searchTerm = searchInput.value.toLowerCase();

                assetItems.forEach(item => {
                    const itemBodegaId = item.getAttribute('data-bodega-id');
                    const itemCategoria = item.getAttribute('data-categoria');
                    const textContent = item.textContent.toLowerCase();

                    let isVisible = true;

                    if (selectedBodegaId && itemBodegaId != selectedBodegaId) isVisible = false;
                    if (currentTabFilter !== 'all' && itemCategoria !== currentTabFilter) isVisible = false;
                    if (searchTerm && !textContent.includes(searchTerm)) isVisible = false;

                    if (isVisible) {
                        item.classList.remove('hidden-by-filter');
                        item.style.display = 'flex';
                    } else {
                        item.classList.add('hidden-by-filter');
                        item.style.display = 'none';
                    }
                });
            }

            document.getElementById('btn_select_all').addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll('.asset-item:not(.hidden-by-filter) .chk');
                const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);

                visibleCheckboxes.forEach(cb => {
                    cb.checked = !allChecked;
                });

                this.innerHTML = allChecked
                    ? '<i class="bi bi-check2-all me-1"></i> Seleccionar Todos los Visibles'
                    : '<i class="bi bi-x-square me-1"></i> Desmarcar Visibles';
                updateSelectionVisuals();
            });
        });
    </script>
</x-base-layout>
