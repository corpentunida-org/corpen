{{-- resources/views/inventario/compras/edit.blade.php · INV-COM-EDIT-v5.3-COLORGROUPS --}}
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

        /* Paneles del formulario */
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

        /* Archivo adjunto */
        .file-status-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 8px;
            padding: 7px 10px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            font-size: 12px;
        }

        /* Tabla de ítems */
        .corp-items-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 880px;
        }
        .corp-items-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid var(--corp-border);
            padding: 10px 12px;
            text-align: left;
        }
        .corp-items-table tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .corp-items-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .corp-items-table .corp-input,
        .corp-items-table .corp-select {
            padding: 6px 10px;
            font-size: 13px;
        }

        /* Grupo input dinero */
        .input-money-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-money-group::before {
            content: '$';
            position: absolute;
            left: 11px;
            color: #94a3b8;
            font-weight: 700;
            font-size: 13px;
            z-index: 5;
        }
        .input-money-group .form-money {
            padding-left: 24px;
            text-align: right;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            color: var(--corp-ink);
        }

        .remove-row {
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
        .remove-row:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Resumen y Total */
        .corp-summary-footer {
            background: #f8fafc;
            border-top: 1px solid var(--corp-border);
            padding: 18px 20px;
        }
        .corp-total-card {
            background: #0f172a;
            color: #ffffff;
            border-radius: 12px;
            padding: 18px 22px;
            text-align: right;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }
        .corp-total-amount {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.15;
            display: block;
            color: #ffffff;
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

            <form action="{{ route('inventario.compras.update', $compra->id) }}" method="POST" enctype="multipart/form-data" id="compra-form">
                @csrf
                @method('PUT')

                {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
                <div class="corp-header mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <nav aria-label="breadcrumb" class="mb-1">
                                <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                    <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                    <li class="breadcrumb-item text-muted">Historial de Facturas</li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Editar Factura #{{ $compra->numero_factura }}</li>
                                </ol>
                            </nav>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h4 class="fw-bold mb-0" style="letter-spacing: -0.02em; color: #0f172a;">
                                    Editar Factura de Compra
                                </h4>
                                <span class="corp-code">#{{ $compra->numero_factura }}</span>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: 13.5px;">
                                Modifica los datos de cabecera, soportes adjuntos o líneas de ítems registrados en la factura.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inventario.compras.show', $compra->id) }}"
                               class="btn btn-outline-secondary fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-arrow-left me-1"></i> Regresar
                            </a>
                            <button type="submit"
                                    class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                    style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
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

                {{-- 2. SECCIÓN 1: INFORMACIÓN DE CABECERA --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-receipt-cutoff text-primary"></i> Información de Cabecera
                        </h6>
                        <span class="text-muted" style="font-size: 12px;">
                            ID Interno: <strong class="text-dark">#{{ $compra->id }}</strong>
                        </span>
                    </div>

                    <div class="corp-panel-body">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-lg-6">
                                <label class="corp-label">Proveedor <span class="text-danger">*</span></label>
                                <select name="cod_ter_proveedor" class="corp-select" required>
                                    @foreach($proveedores as $p)
                                        <option value="{{ $p->cod_ter }}" {{ $compra->cod_ter_proveedor == $p->cod_ter ? 'selected' : '' }}>
                                            {{ $p->nom_ter }} ({{ $p->cod_ter }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">N° de Factura <span class="text-danger">*</span></label>
                                <input type="text" name="numero_factura" value="{{ $compra->numero_factura }}" class="corp-input" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Fecha de Factura <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_factura" value="{{ $compra->fecha_factura->format('Y-m-d') }}" class="corp-input" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Método de Pago <span class="text-danger">*</span></label>
                                <select name="id_InvMetodos" class="corp-select" required>
                                    @foreach($metodos as $m)
                                        <option value="{{ $m->id }}" {{ $compra->id_InvMetodos == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Doc. Interno</label>
                                <input type="text" name="num_doc_interno" value="{{ $compra->num_doc_interno }}" class="corp-input">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Egreso N°</label>
                                <input type="text" name="numero_egreso" value="{{ $compra->numero_egreso }}" class="corp-input">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="corp-label">Actualizar Factura (Soporte)</label>
                                <input type="file" name="eg_archivo" class="corp-input" style="padding: 6px 10px; font-size: 12.5px;">

                                @if($compra->eg_archivo)
                                    <div class="file-status-box">
                                        <span class="text-success fw-semibold">
                                            <i class="bi bi-file-earmark-check-fill me-1"></i>Archivo cargado
                                        </span>
                                        <a href="{{ route('inventario.compras.archivo', $compra->id) }}" target="_blank" class="text-success fw-bold text-decoration-none">
                                            <i class="bi bi-eye-fill me-1"></i>Ver factura
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. SECCIÓN 2: DETALLES DE LA FACTURA --}}
                <div class="corp-panel mb-3">
                    <div class="corp-panel-head">
                        <h6 class="corp-panel-title">
                            <i class="bi bi-box-seam-fill text-primary"></i> Ítems de la Factura
                        </h6>
                        <button type="button" id="add-row" class="btn btn-sm btn-success fw-semibold px-3" style="font-size: 12.5px; border-radius: 8px;">
                            <i class="bi bi-plus-circle me-1"></i> Añadir Ítem
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="corp-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 55px;" class="text-center">#</th>
                                    <th>Referencia / Producto</th>
                                    <th style="width: 22%;">Detalle</th>
                                    <th style="width: 105px;" class="text-center">Cant.</th>
                                    <th style="width: 175px;" class="text-end">Precio Unit.</th>
                                    <th style="width: 165px;" class="text-end">Subtotal</th>
                                    <th style="width: 55px;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="details-body">
                                @foreach($compra->detalles->where('cantidades', '>', 0) as $index => $item)
                                    <tr class="item-row">
                                        <td class="text-center fw-bold text-muted">
                                            {{ $loop->iteration }}
                                            <input type="hidden" name="detalles[{{ $index }}][id]" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <select name="detalles[{{ $index }}][invReferencias_id]" class="corp-select" required>
                                                @foreach($referencias as $ref)
                                                    <option value="{{ $ref->id }}" {{ $item->invReferencias_id == $ref->id ? 'selected' : '' }}>
                                                        {{ $ref->subgrupo?->nombre }} — {{ $ref->referencia }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="detalles[{{ $index }}][detalle]" value="{{ $item->detalle }}" class="corp-input">
                                        </td>
                                        <td>
                                            <input type="number" name="detalles[{{ $index }}][cantidad]" value="{{ (float)$item->cantidades }}" class="corp-input input-qty text-center fw-bold" step="any">
                                        </td>
                                        <td>
                                            <div class="input-money-group">
                                                <input type="text" class="corp-input form-money currency-mask" value="{{ number_format($item->precio_unitario, 0, ',', '.') }}">
                                                <input type="hidden" name="detalles[{{ $index }}][precio]" value="{{ $item->precio_unitario }}">
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold font-monospace subtotal-cell" style="font-size: 13.5px;">
                                            ${{ number_format($item->sub_total, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="remove-row" title="Eliminar línea">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Resumen Financiero --}}
                    <div class="corp-summary-footer">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-7">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="corp-label">IVA Liquidado</label>
                                        <div class="input-money-group">
                                            <input type="text" class="corp-input form-money currency-mask" value="{{ number_format($extraCosts['iva'], 0, ',', '.') }}">
                                            <input type="hidden" name="costo_iva" id="costo_iva" value="{{ $extraCosts['iva'] }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="corp-label">Otros Gastos</label>
                                        <div class="input-money-group">
                                            <input type="text" class="corp-input form-money currency-mask" value="{{ number_format($extraCosts['varios'], 0, ',', '.') }}">
                                            <input type="hidden" name="costo_varios" id="costo_varios" value="{{ $extraCosts['varios'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-5">
                                <div class="corp-total-card">
                                    <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;">
                                        Total a Pagar
                                    </span>
                                    <span class="corp-total-amount" id="grand-total">${{ number_format($compra->total_pago, 0, ',', '.') }}</span>
                                    <input type="hidden" name="total_pago" id="total_pago_hidden" value="{{ $compra->total_pago }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. PIE DE ACCIONES --}}
                <div class="d-flex justify-content-end gap-2 pb-3">
                    <a href="{{ route('inventario.compras.show', $compra->id) }}"
                       class="btn btn-outline-secondary fw-semibold px-4 py-2"
                       style="font-size: 13.5px; border-radius: 9px;">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                            style="font-size: 13.5px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.getElementById('details-body');
            const fmt = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });

            function applyMask(e) {
                let value = e.target.value.replace(/\D/g, "");
                if (value === "") value = "0";
                const hiddenInput = e.target.nextElementSibling;
                hiddenInput.value = value;
                e.target.value = new Intl.NumberFormat('es-CO').format(value);
                calculate();
            }

            function calculate() {
                let itemsSubTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                    const price = parseFloat(row.querySelector('input[name*="[precio]"]').value) || 0;
                    const sub = qty * price;
                    row.querySelector('.subtotal-cell').textContent = fmt.format(sub);
                    itemsSubTotal += sub;
                });
                const iva = parseFloat(document.getElementById('costo_iva').value) || 0;
                const varios = parseFloat(document.getElementById('costo_varios').value) || 0;
                const grandTotal = itemsSubTotal + iva + varios;
                document.getElementById('grand-total').textContent = fmt.format(grandTotal);
                document.getElementById('total_pago_hidden').value = grandTotal;
            }

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('currency-mask')) applyMask(e);
                if (e.target.classList.contains('input-qty')) calculate();
            });

            document.getElementById('add-row').addEventListener('click', () => {
                const idx = Date.now();
                const rowHtml = `
                    <tr class="item-row" style="background: #ecfdf5; border-left: 4px solid #10b981;">
                        <td class="text-center fw-bold text-success">+ <input type="hidden" name="detalles[${idx}][id]" value=""></td>
                        <td>
                            <select name="detalles[${idx}][invReferencias_id]" class="corp-select" required>
                                <option value="">Seleccione...</option>
                                @foreach($referencias as $ref)
                                    <option value="{{ $ref->id }}">{{ $ref->subgrupo?->nombre }} — {{ $ref->referencia }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="detalles[${idx}][detalle]" class="corp-input"></td>
                        <td><input type="number" name="detalles[${idx}][cantidad]" value="1" class="corp-input input-qty text-center fw-bold" step="any"></td>
                        <td>
                            <div class="input-money-group">
                                <input type="text" class="corp-input form-money currency-mask" value="0">
                                <input type="hidden" name="detalles[${idx}][precio]" value="0">
                            </div>
                        </td>
                        <td class="text-end fw-bold font-monospace subtotal-cell" style="font-size: 13.5px;">$ 0</td>
                        <td class="text-center"><button type="button" class="remove-row" title="Eliminar línea"><i class="bi bi-trash3"></i></button></td>
                    </tr>`;
                body.insertAdjacentHTML('beforeend', rowHtml);
            });

            body.addEventListener('click', e => {
                const btn = e.target.closest('.remove-row');
                if (btn && confirm('¿Eliminar esta línea?')) {
                    const row = btn.closest('.item-row');
                    const idInput = row.querySelector('input[name*="[id]"]');
                    if (idInput && idInput.value) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'detalles_eliminados[]';
                        input.value = idInput.value;
                        document.getElementById('compra-form').appendChild(input);
                    }
                    row.remove();
                    calculate();
                }
            });

            calculate();
        });
    </script>
</x-base-layout>
