{{-- resources/views/inventario/compras/create.blade.php · INV-COM-CREATE-v5.3-COLORGROUPS --}}
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

        /* Tarjetas de sección del formulario */
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

        /* Inputs y etiquetas corporativas */
        .corp-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }
        .corp-input, .corp-select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px;
            background-color: #ffffff;
            color: var(--corp-ink);
            transition: all .15s ease;
        }
        .corp-input:focus, .corp-select:focus {
            outline: none;
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }
        .corp-input-readonly {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            cursor: not-allowed;
            border-color: #e2e8f0 !important;
        }
        .input-money {
            text-align: right;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        /* Tabla dinámica de ítems */
        .corp-items-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 920px;
        }
        .corp-items-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid var(--corp-border);
            padding: 10px 10px;
            text-align: left;
        }
        .corp-items-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-items-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .corp-items-table .corp-input {
            padding: 6px 10px;
            font-size: 13px;
        }

        .btn-remove-row {
            color: #ef4444;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 7px;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .btn-remove-row:hover:not(:disabled) {
            background: #fee2e2;
            color: #b91c1c;
        }
        .btn-remove-row:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .btn-add-row {
            background: #f8fafc;
            color: var(--corp-ink);
            border: 1px dashed #94a3b8;
            padding: 9px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            transition: all .15s ease;
        }
        .btn-add-row:hover {
            background: #eff6ff;
            border-color: var(--corp-accent);
            color: var(--corp-accent);
        }

        /* Resumen financiero y totales */
        .corp-totals-box {
            background: #f8fafc;
            border: 1px solid var(--corp-border);
            border-radius: 10px;
            padding: 16px 20px;
            min-width: 340px;
        }
        .corp-total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }
        .corp-total-line label {
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }
        .corp-total-line .corp-input {
            width: 155px;
            padding: 5px 10px;
            font-size: 13px;
        }
        .corp-grand-total {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 20px;
            font-weight: 800;
            color: var(--corp-accent);
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

        /* Modal overlay */
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
            border-radius: 14px;
            border: 1px solid var(--corp-border);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.15);
            overflow: hidden;
        }
    </style>

    <div class="container-fluid py-2 px-2 px-md-3">
        <div class="corp-shell">

            {{-- ALERTAS DE VALIDACIÓN --}}
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

            {{-- PLANTILLA ÚNICA DATALIST PARA PROVEEDORES --}}
            <datalist id="proveedoresDataList">
                @foreach($proveedores as $proveedor)
                    <option data-id="{{ $proveedor->cod_ter }}" value="{{ $proveedor->cod_ter }} - {{ $proveedor->nom_ter }} {{ $proveedor->razon_soc ? '- ' . $proveedor->razon_soc : '' }}"></option>
                @endforeach
            </datalist>

            {{-- PLANTILLA ÚNICA DATALIST PARA PRODUCTOS --}}
            <datalist id="referenciasDataList">
                @foreach($referencias as $ref)
                    <option data-id="{{ $ref->id }}" value="{{ $ref->referencia }} | Marca: {{ $ref->marca->nombre ?? 'N/A' }} | Bodega: {{ $ref->bodega->nombre ?? 'N/A' }} | SG: {{ $ref->subgrupo->nombre ?? 'N/A' }}"></option>
                @endforeach
            </datalist>

            <form action="{{ route('inventario.compras.store') }}" method="POST" enctype="multipart/form-data" id="formCompra">
                @csrf

                {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
                <div class="corp-header mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <nav aria-label="breadcrumb" class="mb-1">
                                <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                    <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                    <li class="breadcrumb-item text-muted">Operaciones</li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Registrar Compra</li>
                                </ol>
                            </nav>
                            <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                                Nueva Compra e Ingreso a Inventario
                            </h4>
                            <p class="text-secondary mb-0" style="font-size: 13.5px;">
                                Registra facturas de proveedores, asociando referencias, cantidades, costos unitarios y soportes contables.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inventario.compras.index') }}"
                               class="btn btn-outline-secondary fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit"
                                    class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                    style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-check2-circle me-1"></i> Guardar e Ingresar
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
                        <a href="{{ route('inventario.compras.create') }}" class="corp-nav-pill pill-ops active" title="Operaciones">
                            <i class="bi bi-cart-plus-fill"></i> + Compra
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

                {{-- 2. DATOS DE FACTURACIÓN Y AUDITORÍA --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-receipt text-primary"></i> Datos de Facturación y Proveedor
                        </h6>
                        <span class="badge bg-light text-secondary border" style="font-size: 11.5px;">
                            <i class="bi bi-person-check me-1 text-primary"></i> Registrado por: <strong class="text-dark">{{ auth()->user()->name ?? 'Usuario Sistema' }}</strong>
                        </span>
                    </div>

                    <div class="corp-panel-body">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label class="corp-label">Proveedor (Buscador por NIT o Nombre) <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bi bi-search text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 13.5px;"></i>
                                    <input list="proveedoresDataList"
                                           class="corp-input proveedor-search ps-4"
                                           placeholder="Escriba el NIT o nombre del proveedor..."
                                           autocomplete="off"
                                           required>
                                    <input type="hidden" name="cod_ter_proveedor" class="proveedor-id" required>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Número de Factura <span class="text-danger">*</span></label>
                                <input type="text" name="numero_factura" class="corp-input" placeholder="Ej: F-2026-001" value="{{ old('numero_factura') }}" required>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Fecha de Emisión <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_factura" class="corp-input" value="{{ old('fecha_factura') }}" required>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Núm. Doc. Interno</label>
                                <input type="text" name="num_doc_interno" class="corp-input" placeholder="Opcional" value="{{ old('num_doc_interno') }}">
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Número de Egreso</label>
                                <input type="number" name="numero_egreso" class="corp-input" placeholder="Ej: 1024" value="{{ old('numero_egreso') }}">
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Método de Pago <span class="text-danger">*</span></label>
                                <select name="id_InvMetodos" class="corp-select" required>
                                    <option value="">Seleccione un método...</option>
                                    @foreach($metodos as $metodo)
                                        <option value="{{ $metodo->id }}" {{ old('id_InvMetodos') == $metodo->id ? 'selected' : '' }}>
                                            {{ $metodo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Soporte de Factura (PDF / Imagen)</label>
                                <input type="file" name="eg_archivo" class="corp-input" style="padding: 6px 10px;" accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <input type="hidden" name="total_pago" id="total_pago_hidden" value="0">
                        </div>
                    </div>
                </div>

                {{-- 3. DETALLE DE ITEMS DINÁMICOS Y COSTOS --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-box-seam text-primary"></i> Detalle de Ítems y Costos (Ingreso a Stock)
                        </h6>
                        <button type="button" class="btn btn-sm btn-success fw-semibold px-3" id="btnOpenModalRef" style="font-size: 12.5px; border-radius: 8px;">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto / Referencia
                        </button>
                    </div>

                    <div class="corp-panel-body">
                        <div class="table-responsive border rounded-3 mb-3" style="border-color: #e2e8f0 !important;">
                            <table class="corp-items-table" id="tablaItems">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;" class="text-center">#</th>
                                        <th style="width: 34%;">Producto / Referencia <span class="text-danger">*</span></th>
                                        <th style="width: 18%;">Detalle (Opcional)</th>
                                        <th style="width: 11%;">Cantidad <span class="text-danger">*</span></th>
                                        <th style="width: 16%;">Costo Unit. <span class="text-danger">*</span></th>
                                        <th style="width: 16%;">Subtotal</th>
                                        <th style="width: 50px;" class="text-center"><i class="bi bi-trash"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr>
                                        <td class="row-num text-center fw-bold text-muted">1</td>
                                        <td>
                                            <input list="referenciasDataList" class="corp-input item-ref-search" placeholder="Escriba o seleccione..." autocomplete="off" required>
                                            <input type="hidden" name="detalles[0][invReferencias_id]" class="item-ref-id" required>
                                        </td>
                                        <td>
                                            <input type="text" name="detalles[0][detalle]" class="corp-input item-detalle" placeholder="Ej: Lote A, Talla M...">
                                        </td>
                                        <td>
                                            <input type="number" name="detalles[0][cantidad]" class="corp-input item-qty" value="1" min="0.01" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="text" class="corp-input item-price-display input-money" placeholder="0" required>
                                            <input type="hidden" name="detalles[0][precio]" class="item-price-hidden" value="0">
                                        </td>
                                        <td>
                                            <input type="text" class="corp-input item-subtotal corp-input-readonly input-money" value="0" disabled>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-remove-row" disabled title="Eliminar fila">
                                                <i class="bi bi-x-lg" style="font-size: 12px;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-3 align-items-start">
                            <div class="col-12 col-lg-7">
                                <button type="button" class="btn-add-row" id="btnAddRow">
                                    <i class="bi bi-plus-circle"></i> Agregar otra fila de producto
                                </button>
                                <div class="text-muted mt-2" style="font-size: 12px;">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Selecciona siempre la referencia desde la lista desplegable para asegurar su vinculación con la bodega y subgrupo.
                                </div>
                            </div>

                            <div class="col-12 col-lg-5 d-flex justify-content-lg-end">
                                <div class="corp-totals-box w-100">
                                    <div class="corp-total-line">
                                        <label>Subtotal Productos:</label>
                                        <input type="text" id="suma_productos_display" class="corp-input corp-input-readonly input-money" value="0" disabled>
                                    </div>
                                    <div class="corp-total-line">
                                        <label>IVA Total ($):</label>
                                        <input type="text" class="corp-input extra-cost-display input-money" value="0">
                                        <input type="hidden" name="costo_iva" id="costo_iva" class="extra-cost-hidden" value="0">
                                    </div>
                                    <div class="corp-total-line">
                                        <label>Otros Costos / Varios ($):</label>
                                        <input type="text" class="corp-input extra-cost-display input-money" value="0">
                                        <input type="hidden" name="costo_varios" id="costo_varios" class="extra-cost-hidden" value="0">
                                    </div>
                                    <hr style="border-color: #cbd5e1; margin: 10px 0;">
                                    <div class="corp-total-line mb-0">
                                        <label class="fw-bold text-dark" style="font-size: 13px;">TOTAL FACTURA:</label>
                                        <div class="corp-grand-total" id="display_grand_total">$0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. PIE DE ACCIONES --}}
                <div class="d-flex justify-content-end gap-2 pb-3">
                    <a href="{{ route('inventario.compras.index') }}"
                       class="btn btn-outline-secondary fw-semibold px-4 py-2"
                       style="font-size: 13.5px; border-radius: 9px;">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                            style="font-size: 13.5px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                        <i class="bi bi-check2-circle me-1"></i> Guardar e Ingresar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PARA CREAR NUEVA REFERENCIA --}}
    <div id="modalReferencia" class="modal-overlay" style="display: none;">
        <div class="modal-card-corp">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <div class="corp-bod-avatar">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">Crear Nuevo Producto / Referencia</h6>
                        <div class="text-muted" style="font-size: 11.5px;">Se agregará y seleccionará automáticamente en la compra</div>
                    </div>
                </div>
                <button type="button" class="btn-close" id="closeModal" aria-label="Cerrar"></button>
            </div>

            <div class="p-4">
                <div class="alert alert-danger py-2 px-3 mb-3" id="modalErrors" style="display: none; font-size: 12.5px; border-radius: 8px;"></div>

                <div class="mb-3">
                    <label class="corp-label">Nombre del Producto / Referencia <span class="text-danger">*</span></label>
                    <input type="text" id="ref_referencia" class="corp-input" placeholder="Ej: Portátil Latitude 5430">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="corp-label">Subgrupo <span class="text-danger">*</span></label>
                        <select id="ref_id_InvSubGrupos" class="corp-select" required>
                            <option value="">Seleccione un subgrupo...</option>
                            @foreach($subgrupos as $sg)
                                <option value="{{ $sg->id }}">{{ $sg->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="corp-label">Marca <span class="text-danger">*</span></label>
                        <select id="ref_id_InvMarcas" class="corp-select" required>
                            <option value="">Seleccione una marca...</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="corp-label">Bodega Destino <span class="text-danger">*</span></label>
                    <select id="ref_id_InvBodegas" class="corp-select" required>
                        <option value="">Seleccione una bodega...</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-1">
                    <label class="corp-label">Detalle (Opcional)</label>
                    <input type="text" id="ref_detalle" class="corp-input" placeholder="Ej: Core i7, 16GB RAM, SSD 512GB">
                </div>
            </div>

            <div class="px-4 py-3 bg-light border-top d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary fw-semibold px-3" id="cancelModal" style="font-size: 13px;">Cancelar</button>
                <button type="button" class="btn btn-primary fw-semibold px-4" id="btnSaveRefAjax" style="font-size: 13px; background-color: #1d4ed8; border-color: #1d4ed8;">
                    Guardar Producto
                </button>
            </div>
        </div>
    </div>

    {{-- Librerías JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function () {

            // =========================================================
            // LÓGICA DE DICCIONARIO PARA DATALIST (Cero Lag)
            // =========================================================
            let dictProveedores = {};
            $('#proveedoresDataList option').each(function () {
                dictProveedores[$(this).val()] = $(this).attr('data-id');
            });

            let dictReferencias = {};
            $('#referenciasDataList option').each(function () {
                dictReferencias[$(this).val()] = $(this).attr('data-id');
            });

            // Evento instantáneo para Proveedores
            $(document).on('input change', '.proveedor-search', function () {
                let val = $(this).val();
                let hiddenInput = $(this).closest('div').find('.proveedor-id');

                if (dictProveedores.hasOwnProperty(val)) {
                    hiddenInput.val(dictProveedores[val]);
                    $(this).css('border-color', '#cbd5e1');
                } else {
                    hiddenInput.val('');
                    if (val) $(this).css('border-color', '#ef4444');
                }
            });

            // Evento instantáneo para Referencias / Productos
            $(document).on('input change', '.item-ref-search', function () {
                let val = $(this).val();
                let hiddenInput = $(this).siblings('.item-ref-id');

                if (dictReferencias.hasOwnProperty(val)) {
                    hiddenInput.val(dictReferencias[val]);
                    $(this).css('border-color', '#cbd5e1');
                } else {
                    hiddenInput.val('');
                    if (val) $(this).css('border-color', '#ef4444');
                }
            });

            // =========================================================
            // VALIDACIÓN ESTRICTA AL ENVIAR EL FORMULARIO
            // =========================================================
            $('#formCompra').on('submit', function (e) {
                let isValid = true;
                let errorMsg = [];

                if (!$('.proveedor-id').val()) {
                    isValid = false;
                    $('.proveedor-search').css('border-color', '#ef4444');
                    errorMsg.push('- Debes seleccionar un PROVEEDOR válido de la lista desplegable.');
                }

                $('.item-ref-id').each(function (index) {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).siblings('.item-ref-search').css('border-color', '#ef4444');
                        errorMsg.push(`- El producto de la FILA ${index + 1} no es válido. Escógelo de la lista.`);
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('FALTAN DATOS O HAY ERRORES:\n\n' + errorMsg.join('\n'));
                }
            });

            // ==========================================
            // LÓGICA TABLA DINÁMICA Y TOTALES
            // ==========================================
            let rowCount = 1;

            function formatCurrency(value) {
                let num = parseFloat(value);
                if (isNaN(num)) return '';
                return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
            }

            function parseCurrency(value) {
                if (!value) return 0;
                let cleanValue = value.toString().replace(/\./g, '').replace(/,/g, '.');
                let parsed = parseFloat(cleanValue);
                return isNaN(parsed) ? 0 : parsed;
            }

            function calculateTotals() {
                let subtotalProductos = 0;
                $('#itemsBody tr').each(function () {
                    let qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    let price = parseFloat($(this).find('.item-price-hidden').val()) || 0;
                    let rowSubtotal = qty * price;
                    $(this).find('.item-subtotal').val(formatCurrency(rowSubtotal));
                    subtotalProductos += rowSubtotal;
                });

                $('#suma_productos_display').val(formatCurrency(subtotalProductos));
                let iva = parseFloat($('#costo_iva').val()) || 0;
                let varios = parseFloat($('#costo_varios').val()) || 0;
                let grandTotal = subtotalProductos + iva + varios;

                $('#display_grand_total').text('$' + formatCurrency(grandTotal));
                $('#total_pago_hidden').val(grandTotal.toFixed(2));
            }

            $(document).on('keyup', '.item-price-display, .extra-cost-display', function () {
                let rawValue = $(this).val();
                let numberValue = parseCurrency(rawValue);
                let hiddenTarget = $(this).hasClass('item-price-display') ? '.item-price-hidden' : '.extra-cost-hidden';
                $(this).siblings(hiddenTarget).val(numberValue);

                if (!rawValue.endsWith(',') && !rawValue.endsWith('.')) {
                    $(this).val(formatCurrency(numberValue));
                }
                calculateTotals();
            });

            $(document).on('input', '.item-qty', calculateTotals);

            // AGREGAR FILA
            $('#btnAddRow').click(function () {
                let newRow = `
                    <tr>
                        <td class="row-num text-center fw-bold text-muted">${rowCount + 1}</td>
                        <td>
                            <input list="referenciasDataList" class="corp-input item-ref-search" placeholder="Escriba o seleccione..." autocomplete="off" required>
                            <input type="hidden" name="detalles[${rowCount}][invReferencias_id]" class="item-ref-id" required>
                        </td>
                        <td>
                            <input type="text" name="detalles[${rowCount}][detalle]" class="corp-input item-detalle" placeholder="Ej: Lote A, Talla M...">
                        </td>
                        <td>
                            <input type="number" name="detalles[${rowCount}][cantidad]" class="corp-input item-qty" value="1" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="corp-input item-price-display input-money" placeholder="0" required>
                            <input type="hidden" name="detalles[${rowCount}][precio]" class="item-price-hidden" value="0">
                        </td>
                        <td>
                            <input type="text" class="corp-input item-subtotal corp-input-readonly input-money" value="0" disabled>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-remove-row" title="Eliminar fila">
                                <i class="bi bi-x-lg" style="font-size: 12px;"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#itemsBody').append(newRow);

                rowCount++;
                updateRowNumbers();
                calculateTotals();
            });

            $(document).on('click', '.btn-remove-row', function () {
                if ($('#itemsBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowNumbers();
                    calculateTotals();
                }
            });

            function updateRowNumbers() {
                $('#itemsBody tr').each(function (index) {
                    $(this).find('.row-num').text(index + 1);
                    $(this).find('.item-ref-id').attr('name', `detalles[${index}][invReferencias_id]`);
                    $(this).find('.item-detalle').attr('name', `detalles[${index}][detalle]`);
                    $(this).find('.item-qty').attr('name', `detalles[${index}][cantidad]`);
                    $(this).find('.item-price-hidden').attr('name', `detalles[${index}][precio]`);

                    if (index === 0 && $('#itemsBody tr').length === 1) {
                        $(this).find('.btn-remove-row').prop('disabled', true);
                    } else {
                        $(this).find('.btn-remove-row').prop('disabled', false);
                    }
                });
            }
            calculateTotals();

            // ==========================================
            // MODAL Y GUARDADO AJAX
            // ==========================================
            $('#btnOpenModalRef').click(function () {
                $('#modalReferencia').fadeIn('fast');
            });

            $('#closeModal, #cancelModal').click(function () {
                $('#modalReferencia').fadeOut('fast');
                $('#ref_referencia').val('');
                $('#ref_detalle').val('');
                $('#ref_id_InvSubGrupos').val('');
                $('#ref_id_InvBodegas').val('');
                $('#ref_id_InvMarcas').val('');
                $('#modalErrors').hide();
            });

            $('#btnSaveRefAjax').click(function () {
                let referencia = $('#ref_referencia').val().trim();
                let detalle = $('#ref_detalle').val().trim();
                let id_InvSubGrupos = $('#ref_id_InvSubGrupos').val();
                let id_InvBodegas = $('#ref_id_InvBodegas').val();
                let id_InvMarcas = $('#ref_id_InvMarcas').val();
                let btn = $(this);

                if (!referencia) return $('#modalErrors').text('La referencia es obligatoria.').show();
                if (!id_InvSubGrupos) return $('#modalErrors').text('Debe seleccionar un subgrupo.').show();
                if (!id_InvMarcas) return $('#modalErrors').text('Debe seleccionar una marca.').show();
                if (!id_InvBodegas) return $('#modalErrors').text('Debe seleccionar una bodega.').show();

                btn.prop('disabled', true).text('Guardando...');
                $('#modalErrors').hide();

                $.ajax({
                    url: '{{ route("inventario.referencias.ajax") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        referencia: referencia,
                        detalle: detalle,
                        id_InvSubGrupos: id_InvSubGrupos,
                        id_InvBodegas: id_InvBodegas,
                        id_InvMarcas: id_InvMarcas
                    },
                    success: function (response) {
                        if (response.success) {
                            let newRefId = response.referencia.id;
                            let newRefName = response.referencia.referencia;
                            let nomSubgrupo = $('#ref_id_InvSubGrupos option:selected').text();
                            let nomBodega = $('#ref_id_InvBodegas option:selected').text();
                            let nomMarca = $('#ref_id_InvMarcas option:selected').text();

                            let optionValue = `${newRefName} | Marca: ${nomMarca} | Bodega: ${nomBodega} | SG: ${nomSubgrupo}`;

                            let newOptionHTML = `<option data-id="${newRefId}" value="${optionValue}"></option>`;
                            $('#referenciasDataList').append(newOptionHTML);

                            dictReferencias[optionValue] = newRefId;

                            let lastRowSearch = $('#itemsBody tr:last-child .item-ref-search');
                            let lastRowHidden = $('#itemsBody tr:last-child .item-ref-id');
                            lastRowSearch.val(optionValue).css('border-color', '#cbd5e1');
                            lastRowHidden.val(newRefId);

                            $('#modalReferencia').fadeOut('fast');
                            $('#ref_referencia, #ref_detalle, #ref_id_InvSubGrupos, #ref_id_InvBodegas, #ref_id_InvMarcas').val('');
                            $('#modalErrors').hide();
                            btn.prop('disabled', false).text('Guardar Producto');

                            alert('Producto creado y vinculado con éxito.');
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = 'Ocurrió un error al guardar.';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                        $('#modalErrors').text(errorMsg).show();
                        btn.prop('disabled', false).text('Guardar Producto');
                    }
                });
            });
        });
    </script>
</x-base-layout>
