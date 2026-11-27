{{-- resources/views/inventario/movimientos/index.blade.php · INV-MOV-INDEX-v5.3-COLORGROUPS --}}
<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $totalRegSafe      = (int) ($totalRegistros ?? 0);
        $totalFirmadasSafe = (int) ($totalFirmadas ?? 0);
        $totalPendientes   = max(0, $totalRegSafe - $totalFirmadasSafe);
        $porcResguardo     = $totalRegSafe > 0 ? round(($totalFirmadasSafe / $totalRegSafe) * 100) : 0;
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
        .corp-input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            padding: 7px 11px;
            background-color: #ffffff;
            transition: all .15s ease;
        }
        .corp-input:focus {
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
            outline: none;
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
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Badges de auditoría de firma */
        .badge-segura {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .badge-pendiente {
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

    {{-- MENSAJES FLASH --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
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
                                <li class="breadcrumb-item text-muted">Almacén y Gestión</li>
                                <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Bitácora de Movimientos</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                            Bitácora de Movimientos y Actas
                        </h4>
                        <p class="text-secondary mb-0" style="font-size: 13.5px;">
                            Trazabilidad de asignaciones, traslados, entregas de activos y custodia documental de actas firmadas.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('inventario.movimientos.create') }}"
                           class="btn btn-primary fw-semibold px-3 py-2 shadow-sm"
                           style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                            <i class="bi bi-plus-lg me-1"></i> Crear Nuevo Movimiento
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
                    <a href="{{ route('inventario.movimientos.index') }}" class="corp-nav-pill pill-audit active" title="Control y Seguimiento">
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

            {{-- 2. DASHBOARD DE KPIs + GRÁFICO DE MOVIMIENTOS --}}
            <div class="row g-3 mb-3 align-items-stretch">
                <div class="col-12 col-lg-6">
                    <div class="row g-3 h-100">
                        <div class="col-12 col-sm-6">
                            <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #1d4ed8;">
                                <div>
                                    <div class="corp-kpi-label mb-1">Total Movimientos</div>
                                    <div class="corp-kpi-value text-primary font-monospace">{{ $totalRegSafe }}</div>
                                    <div class="text-muted mt-1" style="font-size: 11.5px;">
                                        Registros filtrados en vista
                                    </div>
                                </div>
                                <div class="corp-bod-avatar">
                                    <i class="bi bi-journal-check"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="corp-kpi h-100 d-flex justify-content-between align-items-center" style="border-left: 4px solid #059669;">
                                <div>
                                    <div class="corp-kpi-label mb-1">Actas Resguardadas (S3)</div>
                                    <div class="corp-kpi-value text-success font-monospace">{{ $totalFirmadasSafe }}</div>
                                    <div class="text-muted mt-1" style="font-size: 11.5px;">
                                        {{ $porcResguardo }}% con soporte firmado
                                    </div>
                                </div>
                                <div class="corp-bod-avatar" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="corp-kpi d-flex justify-content-between align-items-center py-2 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 11px;">AUDITORÍA</span>
                                    <span class="text-secondary" style="font-size: 12.5px;">
                                        Actas pendientes por cargar firma digitalizada: <strong class="text-dark font-monospace">{{ $totalPendientes }}</strong>
                                    </span>
                                </div>
                                <span class="text-muted d-none d-sm-inline" style="font-size: 11.5px;">
                                    Almacenamiento S3
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="corp-chart-card">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="corp-kpi-label">Frecuencia de Movimientos</span>
                            <span class="text-muted" style="font-size: 11px;">Actas generadas por fecha</span>
                        </div>
                        <div style="position: relative; height: 140px; width: 100%;">
                            <canvas id="movimientosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PANEL DE FILTROS + TABLA DE MOVIMIENTOS --}}
            <div class="corp-panel">
                <form method="GET" action="{{ route('inventario.movimientos.index') }}" class="corp-filter-bar">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Buscar Código o Responsable
                            </label>
                            <div class="position-relative">
                                <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                <input type="text"
                                       name="search"
                                       class="form-control corp-input ps-4"
                                       placeholder="Ej. ACT-001..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-3 col-lg-2">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                Tipo de Registro
                            </label>
                            <input type="text"
                                   name="tipo"
                                   class="form-control corp-input"
                                   placeholder="Ej. Entrada, Salida..."
                                   value="{{ request('tipo') }}">
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

                        <div class="col-12 col-md-12 col-lg-3 d-flex justify-content-lg-end gap-2">
                            <a href="{{ route('inventario.movimientos.index') }}"
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
                                <th class="ps-3" style="width: 145px;">Código Acta</th>
                                <th style="width: 190px;">Tipo Registro</th>
                                <th>Responsable</th>
                                <th style="width: 150px;">Fecha</th>
                                <th style="width: 145px;">Estado Firma</th>
                                <th style="width: 165px;">Creado Por</th>
                                <th class="text-end pe-3" style="width: 130px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movimientos as $mov)
                                <tr>
                                    <td class="ps-3">
                                        <span class="corp-code">{{ $mov->codigo_acta }}</span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        {{ $mov->tipoRegistro->nombre ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                 style="width: 24px; height: 24px; background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; font-size: 11px; flex-shrink: 0;">
                                                {{ strtoupper(substr($mov->responsable->name ?? 'N', 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold text-dark">{{ $mov->responsable->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-secondary font-monospace" style="font-size: 12.5px;">
                                        {{ $mov->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($mov->acta_archivo)
                                            <span class="badge-segura" title="Documento legal resguardado en S3">
                                                <i class="bi bi-shield-check"></i> Segura
                                            </span>
                                        @else
                                            <span class="badge-pendiente" title="Falta subir el documento con firmas físicas">
                                                <i class="bi bi-hourglass-split"></i> Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 12.5px;">
                                            <i class="bi bi-person-circle me-1"></i>{{ $mov->creador->name ?? 'Sistema' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            {{-- Imprimir acta original --}}
                                            <a href="{{ route('inventario.movimientos.pdf', $mov->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-light border text-dark"
                                               style="padding: 3px 8px;"
                                               title="Imprimir acta original">
                                                <i class="bi bi-printer"></i>
                                            </a>

                                            @if($mov->acta_archivo)
                                                {{-- Ver PDF firmado en S3 --}}
                                                <a href="{{ $mov->getFile($mov->acta_archivo) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-light border text-danger"
                                                   style="padding: 3px 8px;"
                                                   title="Ver documento digitalizado">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </a>
                                            @else
                                                {{-- Subir PDF firmado --}}
                                                <form action="{{ route('inventario.movimientos.upload', $mov->id) }}" method="POST" enctype="multipart/form-data" class="m-0 d-inline">
                                                    @csrf
                                                    @method('POST')
                                                    <label class="btn btn-sm btn-light border text-success mb-0"
                                                           style="padding: 3px 8px; cursor: pointer;"
                                                           title="Subir el acta escaneada (PDF)">
                                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                                        <input type="file" name="acta_pdf" accept="application/pdf" class="d-none" onchange="this.form.submit()">
                                                    </label>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x d-block mb-1" style="font-size: 24px;"></i>
                                        No se encontraron movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($movimientos, 'hasPages') && $movimientos->hasPages())
                    <div class="p-3 border-top bg-white d-flex justify-content-center">
                        {{ $movimientos->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Script del Gráfico Chart.js --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const canvas = document.getElementById('movimientosChart');
            if (!canvas) return;

            const labelsFechas = @json($chartLabels ?? []);
            const dataTotales  = @json($chartData ?? []);

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelsFechas,
                    datasets: [{
                        label: 'Movimientos',
                        data: dataTotales,
                        backgroundColor: 'rgba(29, 78, 216, 0.78)',
                        borderColor: '#1d4ed8',
                        borderWidth: 1,
                        borderRadius: 5,
                        maxBarThickness: 32,
                        hoverBackgroundColor: '#1d4ed8'
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
                            titleFont: { family: 'Inter, system-ui, sans-serif', size: 12 },
                            bodyFont: { family: 'Inter, system-ui, sans-serif', size: 12 },
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 10 }, color: '#64748b' },
                            grid: { color: '#f1f5f9', drawBorder: false }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10 }, color: '#64748b' }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            });
        });
    </script>
</x-base-layout>
