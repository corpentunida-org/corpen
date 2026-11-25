{{-- resources/views/inventario/activos/edit.blade.php · INV-ACT-EDIT-v5.3-COLORGROUPS --}}
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

        /* Etiquetas e inputs */
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
            color: #64748b !important;
            cursor: not-allowed;
            border: 1px dashed #cbd5e1 !important;
            font-weight: 600;
        }

        .corp-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 3px 9px;
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

        /* Modales corporativos */
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
            max-width: 520px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 14px;
            border: 1px solid var(--corp-border);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.15);
        }

        /* Tabla minimalista modal compra */
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
            font-size: 12px;
            text-transform: uppercase;
        }
        .mini-table .value {
            font-weight: 600;
            color: var(--corp-ink);
        }
    </style>

    <div class="container-fluid py-2 px-2 px-md-3">
        <div class="corp-shell">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert" style="border-radius: 10px; font-size: 13px;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div class="flex-grow-1">
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                </div>
            @endif

            {{-- DATALISTS --}}
            <datalist id="referenciasDataList">
                @foreach($referencias as $ref)
                    <option data-id="{{ $ref->id }}" data-pure-name="{{ $ref->referencia }}"
                        data-marca-nom="{{ $ref->marca->nombre ?? 'N/A' }}" data-marca-id="{{ $ref->id_MaeMarcas }}"
                        data-sub-nom="{{ $ref->subgrupo->nombre ?? 'N/A' }}" data-sub-id="{{ $ref->id_MaeSubgrupo }}"
                        data-bodega-nom="{{ $ref->bodega->nombre ?? 'N/A' }}" data-bodega-id="{{ $ref->id_InvBodegas ?? '' }}"
                        data-detalle="{{ $ref->detalle ?? '' }}"
                        value="{{ $ref->referencia }} | Marca: {{ $ref->marca->nombre ?? 'N/A' }} | Bodega: {{ $ref->bodega->nombre ?? 'N/A' }}">
                    </option>
                @endforeach
            </datalist>

            <datalist id="municipiosDataList">
                @foreach($municipios as $muni)
                    <option data-id="{{ $muni->id }}" value="{{ $muni->nombre }}"></option>
                @endforeach
            </datalist>

            <datalist id="usuariosDataList">
                @foreach($usuarios as $user)
                    <option data-id="{{ $user->id }}" value="{{ $user->name }}"></option>
                @endforeach
            </datalist>

            <form action="{{ route('inventario.activos.update', $activo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
                <div class="corp-header mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <nav aria-label="breadcrumb" class="mb-1">
                                <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                    <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                    <li class="breadcrumb-item text-muted">Almacén de Activos</li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Editar {{ $activo->codigo_activo }}</li>
                                </ol>
                            </nav>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h4 class="fw-bold mb-0" style="letter-spacing: -0.02em; color: #0f172a;">
                                    Expediente Técnico del Activo
                                </h4>
                                <span class="corp-code">MARQUILLA: {{ $activo->codigo_activo }}</span>
                                <span class="badge bg-light text-secondary border" style="font-size: 11px;">ID #{{ $activo->id }}</span>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: 13.5px;">
                                Hoja de vida, clasificación técnica, cobertura de garantía y asignación operativa.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inventario.activos.show', $activo->id) }}"
                               class="btn btn-outline-secondary fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-arrow-left me-1"></i> Volver al Expediente
                            </a>
                            <button type="submit"
                                    class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                    style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-check2-circle me-1"></i> Actualizar Expediente
                            </button>
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
                    </div>
                </div>

                {{-- SECCIÓN 1: IDENTIDAD DEL ACTIVO --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-upc-scan text-primary"></i> 1. Identidad del Activo
                        </h6>
                    </div>
                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Código de Marquilla <span class="text-danger">*</span></label>
                                <input type="text" name="codigo_activo" class="corp-input font-monospace fw-bold"
                                       value="{{ old('codigo_activo', $activo->codigo_activo) }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Nombre del Activo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="corp-input"
                                       value="{{ old('nombre', $activo->nombre) }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Serial de Fábrica</label>
                                <input type="text" name="serial" class="corp-input font-monospace"
                                       value="{{ old('serial', $activo->serial) }}" placeholder="N/A">
                            </div>
                            <div class="col-12">
                                <label class="corp-label">Descripción y Observaciones Técnicas</label>
                                <textarea name="descripcion" class="corp-textarea" rows="3">{{ old('descripcion', $activo->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: CLASIFICACIÓN TÉCNICA --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-diagram-3 text-primary"></i> 2. Clasificación Técnica
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning text-dark fw-semibold px-3"
                                    id="btnEditRef"
                                    style="display: none; font-size: 12px; border-radius: 8px; background: #fffbeb; border-color: #fcd34d;">
                                <i class="bi bi-pencil-square me-1"></i> Editar Referencia
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-success fw-semibold px-3"
                                    id="btnNewRef"
                                    style="font-size: 12px; border-radius: 8px;">
                                <i class="bi bi-plus-lg me-1"></i> Crear Referencia
                            </button>
                        </div>
                    </div>
                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label class="corp-label text-primary">Buscador de Referencia <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                    <input list="referenciasDataList" id="search_referencia" class="corp-input ps-4"
                                           value="{{ $activo->referencia->referencia ?? '' }} | Marca: {{ $activo->marca->nombre ?? 'N/A' }} | Bodega: {{ $activo->bodega->nombre ?? 'N/A' }}"
                                           placeholder="Escriba para buscar...">
                                    <input type="hidden" name="invReferencias_id" id="hidden_referencia_id"
                                           value="{{ $activo->invReferencias_id }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Marca (Automática)</label>
                                <input type="text" id="display_marca" class="corp-input corp-input-readonly"
                                       value="{{ $activo->marca->nombre ?? 'N/A' }}" readonly tabindex="-1">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Subgrupo / Categoría</label>
                                <input type="text" id="display_subgrupo" class="corp-input corp-input-readonly"
                                       value="{{ $activo->subgrupo->nombre ?? 'N/A' }}" readonly tabindex="-1">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 3: CICLO DE VIDA, GARANTÍAS Y ADQUISICIÓN --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-shield-check text-primary"></i> 3. Ciclo de Vida y Adquisición
                        </h6>
                    </div>
                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Inicio de Garantía</label>
                                <input type="date" name="fecha_inicio_garantia" class="corp-input"
                                       value="{{ old('fecha_inicio_garantia', $activo->fecha_inicio_garantia ? $activo->fecha_inicio_garantia->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Fin de Garantía</label>
                                <input type="date" name="fecha_fin_garantia" class="corp-input"
                                       value="{{ old('fecha_fin_garantia', $activo->fecha_fin_garantia ? $activo->fecha_fin_garantia->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Vida Útil (Meses)</label>
                                <input type="number" name="vida_util_meses" class="corp-input"
                                       value="{{ old('vida_util_meses', $activo->vida_util_meses) }}">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Unidad de Medida</label>
                                <input type="text" name="unidad_medida" class="corp-input"
                                       value="{{ old('unidad_medida', $activo->unidad_medida) }}"
                                       placeholder="UND, MTS, PZA...">
                            </div>

                            {{-- DOCUMENTO HOJA DE VIDA --}}
                            <div class="col-12 col-lg-8">
                                <label class="corp-label">Documento Hoja de Vida (Enlace)</label>
                                <div class="d-flex gap-2">
                                    <input type="text" name="hoja_vida" class="corp-input"
                                           value="{{ old('hoja_vida', $activo->hoja_vida) }}"
                                           placeholder="Ej: https://drive.google.com/...">
                                    @if($activo->hoja_vida)
                                        <a href="{{ $activo->hoja_vida }}" target="_blank"
                                           class="btn btn-light border fw-semibold text-primary px-3 text-nowrap d-inline-flex align-items-center"
                                           style="font-size: 13px; border-radius: 8px;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Ver Link
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- ID DETALLE DE COMPRA --}}
                            <div class="col-12 col-lg-4">
                                <label class="corp-label">ID Detalle de Compra</label>
                                <div class="d-flex gap-2">
                                    <input type="text" name="id_InvDetalleCompras" id="id_InvDetalleCompras"
                                           value="{{ old('id_InvDetalleCompras', $activo->id_InvDetalleCompras) }}"
                                           class="corp-input corp-input-readonly" readonly tabindex="-1">

                                    @if($activo->id_InvDetalleCompras)
                                        <button type="button" id="btnOpenModalCompra"
                                                class="btn btn-dark fw-semibold px-3 text-nowrap"
                                                style="font-size: 12.5px; border-radius: 8px;">
                                            <i class="bi bi-receipt me-1"></i> Ver Compra
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- FACTURA / SOPORTE ADJUNTO --}}
                            <div class="col-12">
                                <div class="p-3 rounded-3 border" style="background: #f8fafc; border-style: dashed !important; border-color: #cbd5e1 !important;">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                        <label class="corp-label mb-0 text-dark">Soporte de Factura (inv_compras)</label>
                                        @if($activo->detalleCompra && $activo->detalleCompra->id_InvCompras)
                                            <a href="{{ route('inventario.compras.archivo', $activo->detalleCompra->id_InvCompras) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-light border fw-semibold text-primary px-3"
                                               style="font-size: 12px; border-radius: 7px;">
                                                <i class="bi bi-file-earmark-text me-1"></i> Ver Documento Actual
                                            </a>
                                        @endif
                                    </div>
                                    <input type="file" name="eg_archivo" class="corp-input" style="padding: 6px 10px;" accept=".pdf,.doc,.docx,.jpg,.png">
                                    <div class="text-muted mt-1" style="font-size: 11.5px;">
                                        Seleccione un archivo solo si desea actualizar el soporte asociado en <strong>inv_compras</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 4: CONTROL DE ESTADO Y UBICACIÓN --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-geo-alt text-primary"></i> 4. Control de Estado y Ubicación
                        </h6>
                    </div>
                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Bodega (Filtro de Estados)</label>
                                <select id="bodega_estado_select" class="corp-select">
                                    <option value="">Mostrar todos los estados...</option>
                                    @foreach($bodegas as $bodega)
                                        <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Estado Actual <span class="text-danger">*</span></label>
                                <select name="id_Estado" id="estado_select" class="corp-select" required>
                                    <option value="">Seleccione un estado...</option>
                                    @foreach($estados as $est)
                                        <option value="{{ $est->id }}" data-bodega="{{ $est->id_bodega }}" {{ $activo->id_Estado == $est->id ? 'selected' : '' }}>
                                            {{ $est->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label text-primary">Ubicación / Sede</label>
                                <input list="municipiosDataList" id="search_municipio" class="corp-input"
                                       value="{{ $activo->municipio->nombre ?? '' }}"
                                       placeholder="Escriba municipio o sede...">
                                <input type="hidden" name="id_MaeMunicipios" id="hidden_municipio_id"
                                       value="{{ $activo->id_MaeMunicipios }}">
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label text-primary">Responsable Asignado</label>
                                <input list="usuariosDataList" id="search_usuario" class="corp-input"
                                       value="{{ $activo->usuarioAsignado->name ?? '' }}"
                                       placeholder="Escriba nombre de usuario...">
                                <input type="hidden" name="id_ultimo_usuario_asignado" id="hidden_usuario_id"
                                       value="{{ $activo->id_ultimo_usuario_asignado }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 5: AUDITORÍA DEL REGISTRO --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-clock-history text-primary"></i> 5. Auditoría del Registro
                        </h6>
                    </div>
                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Registrado por</label>
                                <input type="text" class="corp-input corp-input-readonly"
                                       value="{{ $activo->usuarioRegistro->name ?? 'Sistema' }}" readonly>
                                <input type="hidden" name="id_usersRegistro" value="{{ $activo->id_usersRegistro }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Fecha Creación</label>
                                <input type="text" class="corp-input corp-input-readonly font-monospace"
                                       value="{{ $activo->created_at->format('d/m/Y H:i') }}" readonly>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="corp-label">Última Modificación</label>
                                <input type="text" class="corp-input corp-input-readonly font-monospace"
                                       value="{{ $activo->updated_at->format('d/m/Y H:i') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PIE DE ACCIONES --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3">
                    <a href="{{ route('inventario.activos.show', $activo->id) }}"
                       class="btn btn-outline-secondary fw-semibold px-4 py-2"
                       style="font-size: 13.5px; border-radius: 9px;">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Expediente
                    </a>
                    <button type="submit"
                            class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                            style="font-size: 13.5px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                        <i class="bi bi-check2-circle me-1"></i> Actualizar Expediente Completo
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL REFERENCIA (CREAR / EDITAR) --}}
    <div id="modalRef" class="modal-overlay" style="display: none;">
        <div class="modal-card-corp">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <div class="corp-bod-avatar">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" id="modalTitle" style="font-size: 14.5px;">Editar Referencia Actual</h6>
                        <div class="text-muted" style="font-size: 11.5px;">Catálogo maestro de referencias técnicas</div>
                    </div>
                </div>
                <button type="button" class="btn-close" id="btnCloseModalTop" aria-label="Cerrar"></button>
            </div>

            <div class="p-4">
                <input type="hidden" id="modal_ref_id">

                <div class="mb-3">
                    <label class="corp-label">Nombre de Referencia <span class="text-danger">*</span></label>
                    <input type="text" id="modal_ref_nombre" class="corp-input" placeholder="Ej: Latitude 5430">
                </div>

                <div class="mb-3">
                    <label class="corp-label">Detalle / Descripción <span class="text-danger">*</span></label>
                    <input type="text" id="modal_ref_detalle" class="corp-input" placeholder="Agregue un detalle o '-' si no tiene">
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="corp-label">Marca <span class="text-danger">*</span></label>
                        <select id="modal_ref_marca" class="corp-select">
                            <option value="">Seleccione...</option>
                            @foreach($marcas as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="corp-label">Subgrupo <span class="text-danger">*</span></label>
                        <select id="modal_ref_subgrupo" class="corp-select">
                            <option value="">Seleccione...</option>
                            @foreach($subgrupos as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="corp-label">Bodega <span class="text-danger">*</span></label>
                        <select id="ref_id_InvBodegas" class="corp-select" required>
                            <option value="">Seleccione una bodega...</option>
                            @foreach($bodegas as $bodega)
                                <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-light border-top d-flex justify-content-end gap-2">
                <button type="button" id="btnCloseModal" class="btn btn-outline-secondary fw-semibold px-3" style="font-size: 13px;">Cancelar</button>
                <button type="button" id="btnSaveRefAjax" class="btn btn-primary fw-semibold px-4" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">Guardar</button>
            </div>
        </div>
    </div>

    {{-- MODAL CONSULTA COMPRA --}}
    <div id="modalCompra" class="modal-overlay" style="display: none;">
        <div class="modal-card-corp">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <div class="corp-bod-avatar">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Datos de Adquisición</h6>
                        <div class="text-muted" style="font-size: 11.5px;">Detalle contable vinculado al activo</div>
                    </div>
                </div>
                <button type="button" class="btn-close" id="btnCloseModalCompraTop" aria-label="Cerrar"></button>
            </div>

            <div class="p-4">
                <table class="mini-table">
                    <tr>
                        <td class="label">ID Registro:</td>
                        <td class="value font-monospace" id="view_compra_id"></td>
                    </tr>
                    <tr>
                        <td class="label">Cantidades:</td>
                        <td class="value" id="view_compra_cant"></td>
                    </tr>
                    <tr>
                        <td class="label">Precio Unit:</td>
                        <td class="value text-success font-monospace" id="view_compra_precio"></td>
                    </tr>
                    <tr>
                        <td class="label">Total Item:</td>
                        <td class="value font-monospace" id="view_compra_subtotal"></td>
                    </tr>
                    <tr>
                        <td class="label">Referencia:</td>
                        <td class="value" id="view_compra_ref"></td>
                    </tr>
                    <tr>
                        <td class="label" style="vertical-align: top;">Detalle:</td>
                        <td class="value" id="view_compra_detalle"></td>
                    </tr>
                </table>

                <div id="fileContainer"></div>
            </div>

            <div class="px-4 py-3 bg-light border-top">
                <button type="button" id="btnCloseModalCompra" class="btn btn-dark fw-semibold w-100" style="font-size: 13px;">
                    Cerrar Consulta
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {

            let dictRefs = {};
            let dictMuni = {};
            let dictUser = {};
            let dictionariesLoaded = false;

            let initialRefId = $('#hidden_referencia_id').val();
            if (initialRefId) $('#btnEditRef').css('display', 'inline-flex'); else $('#btnEditRef').css('display', 'none');

            function loadDictionaries() {
                if (dictionariesLoaded) return;

                requestAnimationFrame(() => {
                    $('#referenciasDataList option').each(function () {
                        dictRefs[$(this).val()] = {
                            id: $(this).attr('data-id'),
                            pureName: $(this).attr('data-pure-name'),
                            marcaNom: $(this).attr('data-marca-nom'),
                            marcaId: $(this).attr('data-marca-id'),
                            subNom: $(this).attr('data-sub-nom'),
                            subId: $(this).attr('data-sub-id'),
                            bodegaId: $(this).attr('data-bodega-id'),
                            detalle: $(this).attr('data-detalle')
                        };
                    });
                    $('#municipiosDataList option').each(function () { dictMuni[$(this).val()] = $(this).attr('data-id'); });
                    $('#usuariosDataList option').each(function () { dictUser[$(this).val()] = $(this).attr('data-id'); });

                    dictionariesLoaded = true;
                });
            }

            $('#search_referencia, #search_municipio, #search_usuario, #btnEditRef').on('focus click', function () {
                loadDictionaries();
            });

            $(document).on('input change', '#search_referencia', function () {
                loadDictionaries();

                let val = $(this).val();

                setTimeout(() => {
                    if (dictRefs[val]) {
                        $('#hidden_referencia_id').val(dictRefs[val].id);
                        $('#display_marca').val(dictRefs[val].marcaNom);
                        $('#display_subgrupo').val(dictRefs[val].subNom);
                        $(this).css('border-color', '#cbd5e1');
                        $('#btnEditRef').css('display', 'inline-flex');
                    } else {
                        $('#hidden_referencia_id').val('');
                        if (val) $(this).css('border-color', '#ef4444');
                        $('#btnEditRef').css('display', 'none');
                    }
                }, 10);
            });

            $(document).on('input change', '#search_municipio', function () {
                loadDictionaries();
                setTimeout(() => {
                    let val = $(this).val();
                    if (dictMuni[val]) { $('#hidden_municipio_id').val(dictMuni[val]); $(this).css('border-color', '#cbd5e1'); }
                    else { $('#hidden_municipio_id').val(''); if (val) $(this).css('border-color', '#ef4444'); }
                }, 10);
            });

            $(document).on('input change', '#search_usuario', function () {
                loadDictionaries();
                setTimeout(() => {
                    let val = $(this).val();
                    if (dictUser[val]) { $('#hidden_usuario_id').val(dictUser[val]); $(this).css('border-color', '#cbd5e1'); }
                    else { $('#hidden_usuario_id').val(''); if (val) $(this).css('border-color', '#ef4444'); }
                }, 10);
            });

            // MODAL REFERENCIAS: CREAR NUEVA
            $('#btnNewRef').click(function () {
                $('#modal_ref_id').val('');
                $('#modal_ref_nombre').val('');
                $('#modal_ref_detalle').val('');
                $('#modal_ref_marca').val('');
                $('#modal_ref_subgrupo').val('');
                $('#ref_id_InvBodegas').val('');

                $('#modalTitle').text('Crear Nueva Referencia');
                $('#modalRef').fadeIn('fast').css('display', 'flex');
            });

            // MODAL REFERENCIAS: EDITAR ACTUAL
            $('#btnEditRef').click(function () {
                loadDictionaries();

                setTimeout(() => {
                    let searchVal = $('#search_referencia').val();
                    let refData = dictRefs[searchVal];

                    if (!refData || !refData.id) return alert('Seleccione una referencia válida primero.');

                    $('#modalTitle').text('Editar Referencia Actual');
                    $('#modal_ref_id').val(refData.id);
                    $('#modal_ref_nombre').val(refData.pureName);
                    $('#modal_ref_detalle').val(refData.detalle);
                    $('#modal_ref_marca').val(refData.marcaId);
                    $('#modal_ref_subgrupo').val(refData.subId);
                    $('#ref_id_InvBodegas').val(refData.bodegaId);

                    $('#modalRef').fadeIn('fast').css('display', 'flex');
                }, 10);
            });

            $('#btnCloseModal, #btnCloseModalTop').click(() => $('#modalRef').fadeOut('fast'));

            // BOTÓN GUARDAR AJAX
            $('#btnSaveRefAjax').click(function () {
                let id = $('#modal_ref_id').val();
                let isUpdate = id !== '';

                let valMarca = $('#modal_ref_marca').val();
                let valSubgrupo = $('#modal_ref_subgrupo').val();
                let valBodega = $('#ref_id_InvBodegas').val();
                let valRef = $('#modal_ref_nombre').val();
                let valDet = $('#modal_ref_detalle').val();

                if (!valRef || !valDet || !valMarca || !valSubgrupo || !valBodega) {
                    return alert('Por favor, selecciona opciones en todos los combos del modal.');
                }

                let data = {
                    _token: '{{ csrf_token() }}',
                    referencia: valRef,
                    detalle: valDet,
                    id_InvMarcas: valMarca,
                    id_InvSubGrupos: valSubgrupo,
                    id_InvBodegas: valBodega
                };

                let url = isUpdate ? `/inventario/referencias/ajax/${id}` : '/inventario/referencias/ajax';
                let method = isUpdate ? 'PUT' : 'POST';

                let $btn = $(this);
                $btn.prop('disabled', true).text('Guardando...');

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function (res) {
                        if (res.success) {
                            alert(isUpdate ? 'Referencia actualizada con éxito.' : 'Referencia creada con éxito.');
                            $('#modalRef').fadeOut('fast');
                            location.reload();
                        } else {
                            alert('Error: ' + (res.message || 'No se pudo procesar.'));
                            $btn.prop('disabled', false).text('Guardar');
                        }
                    },
                    error: function (err) {
                        console.error("ERROR DEL SERVIDOR:", err);
                        if (err.status === 422) {
                            let errors = err.responseJSON.errors;
                            let msg = '';
                            for (let key in errors) {
                                msg += errors[key][0] + '\n';
                            }
                            alert('Errores de validación:\n\n' + msg);
                        } else {
                            alert('Ocurrió un error en el servidor. Revisa la consola.');
                        }
                        $btn.prop('disabled', false).text('Guardar');
                    }
                });
            });

            // MODAL COMPRAS
            $('#btnOpenModalCompra').click(function () {
                let id = $('#id_InvDetalleCompras').val();
                if (!id) return;

                $('#modalCompra').fadeIn('fast').css('display', 'flex');

                $.ajax({
                    url: `/inventario/detalle-compra/ajax/${id}`,
                    type: 'GET',
                    success: function (res) {
                        if (res.success) {
                            let d = res.data;
                            $('#view_compra_id').text('#' + d.id);
                            $('#view_compra_cant').text(d.cantidades);
                            $('#view_compra_precio').text('$' + parseFloat(d.precio_unitario).toLocaleString());
                            $('#view_compra_subtotal').text('$' + parseFloat(d.sub_total).toLocaleString());
                            $('#view_compra_ref').text(d.referencia?.referencia || 'N/A');
                            $('#view_compra_detalle').text(d.detalle || 'Sin observaciones');

                            if (d.id_InvCompras) {
                                $('#fileContainer').html(`
                                    <a href="/inventario/compras/${d.id_InvCompras}/archivo" target="_blank"
                                       class="btn btn-light border fw-semibold text-primary w-100 mt-3"
                                       style="font-size: 13px; border-radius: 8px;">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Ver Factura Adjunta
                                    </a>
                                `);
                            } else {
                                $('#fileContainer').empty();
                            }
                        }
                    },
                    error: () => alert('Error al consultar los datos de compra.')
                });
            });

            $('#btnCloseModalCompra, #btnCloseModalCompraTop').click(() => $('#modalCompra').fadeOut('fast'));

            $('.modal-overlay').click(function (e) {
                if (e.target === this) { $(this).fadeOut('fast'); }
            });

            // =========================================================
            // LÓGICA DE ANIDAMIENTO: BODEGA -> ESTADO (SECCIÓN 4)
            // =========================================================
            let $bodegaSelect = $('#bodega_estado_select');
            let $estadoSelect = $('#estado_select');
            let todasOpcionesEstado = $estadoSelect.find('option').clone();

            function filtrarEstados(bodegaId, estadoIdASeleccionar = null) {
                $estadoSelect.empty();

                if (bodegaId) {
                    let opcionesFiltradas = todasOpcionesEstado.filter(function () {
                        return $(this).val() === "" || $(this).attr('data-bodega') == bodegaId;
                    });
                    $estadoSelect.append(opcionesFiltradas);
                } else {
                    $estadoSelect.append(todasOpcionesEstado);
                }

                if (estadoIdASeleccionar) {
                    $estadoSelect.val(estadoIdASeleccionar);
                } else {
                    $estadoSelect.val('');
                }
            }

            $bodegaSelect.on('change', function () {
                filtrarEstados($(this).val());
            });

            let estadoActualOption = todasOpcionesEstado.filter(':selected');

            if (estadoActualOption.length > 0 && estadoActualOption.val() !== "") {
                let bodegaDelEstadoActual = estadoActualOption.attr('data-bodega');

                if (bodegaDelEstadoActual) {
                    $bodegaSelect.val(bodegaDelEstadoActual);
                    filtrarEstados(bodegaDelEstadoActual, estadoActualOption.val());
                }
            }
        });
    </script>
</x-base-layout>
