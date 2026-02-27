<x-base-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .page-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .form-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }

        .back-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
        }

        .section-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-head {
            background: #f8fafc;
            padding: 15px 24px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-body {
            padding: 24px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #334155;
        }

        .input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: 0.2s;
            background: #fbfcfd;
        }

        .input:focus {
            outline: none;
            border-color: #0f172a;
            background: #fff;
        }

        .btn-submit {
            background: #000;
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #222;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        /* Estilos Select2 */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 41px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            background-color: #fbfcfd !important;
            padding: 0px 14px !important;
            display: flex !important;
            align-items: center !important;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a !important;
            padding-left: 0 !important;
            line-height: normal !important;
            width: 100%;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }

        .select2-container--open .select2-selection--single {
            border-color: #0f172a !important;
            background-color: #fff !important;
        }

        .input-readonly {
            background: #e2e8f0;
            color: #64748b;
            cursor: not-allowed;
            border-color: #cbd5e1;
        }

        /* --- NUEVO: ESTILOS RESULTADOS BUSCADOR SELECT2 --- */
        .select2-results__option--highlighted .ref-badge-bod {
            background: #cbd5e1 !important;
            color: #0f172a;
        }

        .select2-results__option--highlighted .ref-badge-sg {
            background: #cbd5e1 !important;
            color: #0f172a;
        }

        /* Estilos Tabla Dinámica Items */
        .table-responsive {
            overflow-x: auto;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            min-width: 900px;
        }

        .table-items th {
            background: #f1f5f9;
            padding: 12px;
            text-align: left;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-items td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-items .input {
            padding: 8px 10px;
        }

        .input-money {
            text-align: right;
        }

        .btn-remove-row {
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 5px;
            transition: 0.2s;
        }

        .btn-remove-row:hover {
            color: #b91c1c;
            transform: scale(1.1);
        }

        .btn-add-row {
            background: #f1f5f9;
            color: #0f172a;
            border: 1px dashed #cbd5e1;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            width: 100%;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-add-row:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
        }

        .totals-wrapper {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            width: 350px;
            align-items: center;
        }

        .total-row label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin: 0;
        }

        .total-row .input-small {
            width: 150px;
            text-align: right;
            padding: 6px 10px;
            font-family: monospace;
        }

        .grand-total {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Estilos del Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #0f172a;
        }

        .btn-close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            transition: 0.2s;
        }

        .btn-close-modal:hover {
            color: #ef4444;
        }

        .btn-modal-action {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-modal-action:hover {
            background: #059669;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-selection__rendered {
            line-height: 30px !important;
        }
    </style>

    <div class="page-wrap">
        @if ($errors->any())
            <div class="alert-error">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- PLANTILLA OCULTA PARA LAS OPCIONES DE REFERENCIAS (Optimiza la carga en nuevas filas) --}}
        <select id="dummy_select_referencias" style="display:none;">
            <option value="">Buscar producto, bodega o subgrupo...</option>
            @foreach($referencias as $ref)
                <option value="{{ $ref->id }}" data-subgrupo="{{ $ref->subgrupo->nombre ?? 'N/A' }}"
                    data-bodega="{{ $ref->bodega->nombre ?? 'N/A' }}">
                    {{ $ref->referencia }}
                </option>
            @endforeach
        </select>

        <form action="{{ route('inventario.compras.store') }}" method="POST" enctype="multipart/form-data"
            id="formCompra">
            @csrf
            <div class="form-header">
                <a href="{{ route('inventario.compras.index') }}" class="back-link"><i class="bi bi-arrow-left"></i>
                    Cancelar</a>
                <h1 class="form-title">Nueva Compra</h1>
            </div>

            {{-- REGISTRO DE USUARIO --}}
            <div class="section-card">
                <div class="section-head">
                    <div><i class="bi bi-person"></i> Registro del Sistema</div>
                </div>
                <div class="section-body">
                    <div>
                        <label class="label">Registrado por</label>
                        <input type="text" class="input input-readonly"
                            value="{{ auth()->user()->name ?? 'Usuario Sistema' }}" disabled>
                    </div>
                </div>
            </div>

            {{-- DATOS FACTURA --}}
            <div class="section-card">
                <div class="section-head">
                    <div><i class="bi bi-receipt"></i> Datos de Facturación</div>
                </div>
                <div class="section-body">
                    <div class="grid-2">
                        <div style="grid-column: span 2;">
                            <label class="label">Proveedor (Buscador) *</label>
                            <select name="cod_ter_proveedor" class="input select2-proveedor" required>
                                <option value="">Escriba para buscar proveedor...</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->cod_ter }}" {{ old('cod_ter_proveedor') == $proveedor->cod_ter ? 'selected' : '' }}>
                                        {{ $proveedor->cod_ter }} - {{ $proveedor->nom_ter }}
                                        {{ $proveedor->razon_soc ? ' - ' . $proveedor->razon_soc : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Número de Factura *</label>
                            <input type="text" name="numero_factura" class="input" placeholder="Ej: F-2024-001"
                                value="{{ old('numero_factura') }}" required>
                        </div>
                        <div>
                            <label class="label">Fecha de Emisión *</label>
                            <input type="date" name="fecha_factura" class="input" value="{{ old('fecha_factura') }}"
                                required>
                        </div>
                        <div>
                            <label class="label">Núm. Doc. Interno</label>
                            <input type="text" name="num_doc_interno" class="input" placeholder="Opcional"
                                value="{{ old('num_doc_interno') }}">
                        </div>
                        <div>
                            <label class="label">Número de Egreso</label>
                            <input type="number" name="numero_egreso" class="input" placeholder="Ej: 1024"
                                value="{{ old('numero_egreso') }}">
                        </div>
                        <div>
                            <label class="label">Método de Pago *</label>
                            <select name="id_InvMetodos" class="input" required>
                                <option value="">Seleccione un método...</option>
                                @foreach($metodos as $metodo)
                                    <option value="{{ $metodo->id }}" {{ old('id_InvMetodos') == $metodo->id ? 'selected' : '' }}>
                                        {{ $metodo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Adjuntar Archivo de Egreso / PDF (AWS S3)</label>
                            <input type="file" name="eg_archivo" class="input" style="padding: 7px;"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div style="grid-column: span 2;">
                            <input type="hidden" name="total_pago" id="total_pago_hidden" value="0">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ITEMS DINÁMICOS --}}
            <div class="section-card">
                <div class="section-head">
                    <div><i class="bi bi-box-seam"></i> Detalle de Items y Costos (Stock)</div>
                    <button type="button" class="btn-modal-action" id="btnOpenModalRef">
                        <i class="bi bi-plus-lg"></i> Nuevo Producto / Referencia
                    </button>
                </div>
                <div class="section-body">

                    <div class="table-responsive">
                        <table class="table-items" id="tablaItems">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 30%;">Producto / Referencia *</th>
                                    <th style="width: 15%;">Detalle (Opcional)</th>
                                    <th style="width: 10%;">Cantidad *</th>
                                    <th style="width: 17%;">Costo Unit. *</th>
                                    <th style="width: 18%;">Subtotal</th>
                                    <th style="width: 5%; text-align: center;"><i class="bi bi-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td class="row-num" style="font-weight: 700; color: #64748b;">1</td>
                                    <td>
                                        {{-- MODIFICADO: Eliminamos el datalist y el hidden, ahora usamos el select
                                        directamente --}}
                                        <select name="detalles[0][invReferencias_id]"
                                            class="input select2-referencia item-ref-id" required>
                                            <option value="">Buscar producto, bodega o subgrupo...</option>
                                            @foreach($referencias as $ref)
                                                <option value="{{ $ref->id }}"
                                                    data-subgrupo="{{ $ref->subgrupo->nombre ?? 'N/A' }}"
                                                    data-bodega="{{ $ref->bodega->nombre ?? 'N/A' }}">
                                                    {{ $ref->referencia }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="detalles[0][detalle]" class="input item-detalle"
                                            placeholder="Ej: Lote A, Talla M...">
                                    </td>
                                    <td>
                                        <input type="number" name="detalles[0][cantidad]" class="input item-qty"
                                            value="1" min="0.01" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="text" class="input item-price-display input-money" placeholder="0"
                                            required>
                                        <input type="hidden" name="detalles[0][precio]" class="item-price-hidden"
                                            value="0">
                                    </td>
                                    <td>
                                        <input type="text" class="input item-subtotal input-readonly input-money"
                                            value="0" disabled style="font-family: monospace;">
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn-remove-row" disabled style="opacity: 0.3;"><i
                                                class="bi bi-x-circle"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn-add-row" id="btnAddRow">
                        <i class="bi bi-plus-circle"></i> Agregar otra fila
                    </button>

                    <div class="totals-wrapper">
                        <div class="total-row">
                            <label>Subtotal Productos:</label>
                            <input type="text" id="suma_productos_display"
                                class="input input-small input-readonly input-money" value="0" disabled>
                        </div>
                        <div class="total-row">
                            <label>IVA Total ($):</label>
                            <input type="text" class="input input-small extra-cost-display input-money" value="0">
                            <input type="hidden" name="costo_iva" id="costo_iva" class="extra-cost-hidden" value="0">
                        </div>
                        <div class="total-row">
                            <label>Otros Costos / Varios ($):</label>
                            <input type="text" class="input input-small extra-cost-display input-money" value="0">
                            <input type="hidden" name="costo_varios" id="costo_varios" class="extra-cost-hidden"
                                value="0">
                        </div>
                        <hr style="width: 100%; border: 0; border-top: 1px solid #cbd5e1; margin: 5px 0;">
                        <div class="total-row">
                            <label style="color: #0f172a;">TOTAL FACTURA:</label>
                            <div class="grand-total" id="display_grand_total">$0</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn-submit">Guardar e Ingresar</button>
            </div>
        </form>
    </div>

    {{-- MODAL PARA CREAR NUEVA REFERENCIA --}}
    <div id="modalReferencia" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Crear Nuevo Producto / Referencia</h3>
                <button type="button" class="btn-close-modal" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert-error" id="modalErrors" style="display: none;"></div>
                <div>
                    <label class="label">Nombre del Producto *</label>
                    <input type="text" id="ref_referencia" class="input" placeholder="Ej: B XVR 45">
                </div>

                <div style="margin-top: 15px;">
                    <label class="label">Subgrupo *</label>
                    <select id="ref_id_InvSubGrupos" class="input" required>
                        <option value="">Seleccione un subgrupo...</option>
                        @foreach($subgrupos as $sg)
                            <option value="{{ $sg->id }}">{{ $sg->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-top: 15px;">
                    <label class="label">Bodega *</label>
                    <select id="ref_id_InvBodegas" class="input" required>
                        <option value="">Seleccione una bodega...</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-top: 15px;">
                    <label class="label">Detalle (Opcional)</label>
                    <input type="text" id="ref_detalle" class="input" placeholder="Ej: Marca Norma, 500 hojas">
                </div>
            </div>
            <div style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-add-row" style="width: auto; padding: 10px 20px;"
                    id="cancelModal">Cancelar</button>
                <button type="button" class="btn-submit" style="padding: 10px 20px;" id="btnSaveRefAjax">Guardar
                    Producto</button>
            </div>
        </div>
    </div>

    {{-- Librerías JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicializar Select2 Proveedor
            $('.select2-proveedor').select2({ placeholder: "Seleccione o busque un proveedor...", allowClear: true, width: '100%' });

            // ==========================================
            // LOGICA SELECT2 PERSONALIZADO PARA REFERENCIAS
            // ==========================================

            // Función para buscar por nombre, subgrupo o bodega
            function matchCustomReferencia(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;

                let term = params.term.toLowerCase();
                let text = data.text.toLowerCase();
                let subgrupo = $(data.element).data('subgrupo') ? $(data.element).data('subgrupo').toString().toLowerCase() : '';
                let bodega = $(data.element).data('bodega') ? $(data.element).data('bodega').toString().toLowerCase() : '';

                if (text.indexOf(term) > -1 || subgrupo.indexOf(term) > -1 || bodega.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }

            // Función para renderizar los 3 campos en la lista desplegable
            function formatResultReferencia(repo) {
                if (!repo.id) return repo.text; // Texto por defecto "Buscar..."
                if (repo.newTag) return `<span>Crear nueva referencia "${repo.text}" <i class="bi bi-plus-circle ms-1"></i> </span>`; // Cuando escriben algo nuevo

                let subgrupo = $(repo.element).data('subgrupo') || 'N/A';
                let bodega = $(repo.element).data('bodega') || 'N/A';

                return $(`
                    <div style="display: flex; flex-direction: column; padding: 2px 0;">
                        <span style="font-weight: 600; color: #0f172a;">${repo.text}</span>
                        <div style="display: flex; gap: 8px; font-size: 0.75rem; margin-top: 4px;">
                            <span class="ref-badge-bod" style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px;">Bod: ${bodega}</span>
                            <span class="ref-badge-sg" style="background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px;">SG: ${subgrupo}</span>
                        </div>
                    </div>
                `);
            }

            // Función para mostrar SÓLO LA REFERENCIA al seleccionar
            function formatSelectionReferencia(repo) {
                return repo.text;
            }

            let filaActivaParaModal = null;

            // Función reutilizable para inicializar Select2 de las Referencias
            function initSelect2Referencias(selector) {
                $(selector).select2({
                    allowClear: true,
                    width: '100%',
                    tags: true, // Esto permite al usuario tipear un nombre nuevo
                    dropdownParent: $('body'),
                    createTag: function (params) {
                        return { id: params.term, text: params.term, newTag: true };
                    },
                    matcher: matchCustomReferencia,
                    templateResult: formatResultReferencia,
                    templateSelection: formatSelectionReferencia,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                }).on('select2:select', function (e) {
                    let data = e.params.data;
                    // Si el usuario seleccionó "Crear nuevo" (el tag que tippeó)
                    if (data.newTag) {
                        filaActivaParaModal = $(this); // Guardamos quién disparó el modal
                        $('#ref_referencia').val(data.text);
                        $(this).val(null).trigger('change'); // Limpiamos el input para que no quede el texto roto si cancela
                        $('#modalReferencia').fadeIn('fast');
                    }
                });
            }

            // Inicializar la primera fila
            //initSelect2Referencias('.select2-referencia');
            $('.select2-referencia').each(function () {
                initSelect2Referencias(this);
            });

            // ==========================================
            // LOGICA TABLA DINÁMICA Y TOTALES
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

            // AGREGAR FILA (Modificado para inyectar Select2)
            $('#btnAddRow').click(function () {
                let opciones = $('#dummy_select_referencias').html(); // Obtenemos las opciones precargadas

                let newRow = `
                    <tr>
                        <td class="row-num" style="font-weight: 700; color: #64748b;">${rowCount + 1}</td>
                        <td>
                            <select name="detalles[${rowCount}][invReferencias_id]" class="input select2-referencia item-ref-id" required>
                                ${opciones}
                            </select>
                        </td>
                        <td>
                            <input type="text" name="detalles[${rowCount}][detalle]" class="input item-detalle" placeholder="Ej: Lote A, Talla M...">
                        </td>
                        <td>
                            <input type="number" name="detalles[${rowCount}][cantidad]" class="input item-qty" value="1" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <input type="text" class="input item-price-display input-money" placeholder="0" required>
                            <input type="hidden" name="detalles[${rowCount}][precio]" class="item-price-hidden" value="0">
                        </td>
                        <td>
                            <input type="text" class="input item-subtotal input-readonly input-money" value="0" disabled style="font-family: monospace;">
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-remove-row"><i class="bi bi-x-circle"></i></button>
                        </td>
                    </tr>
                `;
                $('#itemsBody').append(newRow);

                // Inicializamos Select2 en la nueva fila
                initSelect2Referencias($('#itemsBody tr:last-child .select2-referencia'));

                rowCount++;
                updateRowNumbers();
                calculateTotals();
            });

            $(document).on('click', '.btn-remove-row', function () {
                if ($('#itemsBody tr').length > 1) {
                    // Tenemos que destruir el select2 antes de remover el nodo
                    $(this).closest('tr').find('.select2-referencia').select2('destroy');
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
                        $(this).find('.btn-remove-row').prop('disabled', true).css('opacity', '0.3');
                    } else {
                        $(this).find('.btn-remove-row').prop('disabled', false).css('opacity', '1');
                    }
                });
            }
            calculateTotals();

            // ==========================================
            // MODAL Y GUARDADO AJAX
            // ==========================================

            $('#btnOpenModalRef').click(function () {
                filaActivaParaModal = null;
                $('#modalReferencia').fadeIn('fast');
            });

            $('#closeModal, #cancelModal').click(function () {
                filaActivaParaModal = null;
                $('#modalReferencia').fadeOut('fast');
                $('#ref_referencia').val('');
                $('#ref_detalle').val('');
                $('#ref_id_InvSubGrupos').val('');
                $('#ref_id_InvBodegas').val('');
                $('#modalErrors').hide();
            });

            $('#btnSaveRefAjax').click(function () {
                let referencia = $('#ref_referencia').val().trim();
                let detalle = $('#ref_detalle').val().trim();
                let id_InvSubGrupos = $('#ref_id_InvSubGrupos').val();
                let id_InvBodegas = $('#ref_id_InvBodegas').val();
                let btn = $(this);

                if (!referencia) return $('#modalErrors').text('La referencia es obligatoria.').show();
                if (!id_InvSubGrupos) return $('#modalErrors').text('Debe seleccionar un subgrupo.').show();
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
                        id_InvBodegas: id_InvBodegas
                    },
                    success: function (response) {
                        if (response.success) {
                            let newRefId = response.referencia.id;
                            let newRefName = response.referencia.referencia;
                            let nomSubgrupo = $('#ref_id_InvSubGrupos option:selected').text();
                            let nomBodega = $('#ref_id_InvBodegas option:selected').text();

                            // 1. Crear el nuevo "option" para inyectar
                            let newOptionHTML = `<option value="${newRefId}" data-subgrupo="${nomSubgrupo}" data-bodega="${nomBodega}">${newRefName}</option>`;

                            // 2. Agregarlo a TODAS las listas de Select2 activas en la tabla y a la plantilla oculta
                            $('.select2-referencia').append(newOptionHTML);
                            $('#dummy_select_referencias').append(newOptionHTML);

                            // 3. Seleccionar la nueva opción en la fila correspondiente
                            if (filaActivaParaModal) {
                                filaActivaParaModal.val(newRefId).trigger('change');
                                filaActivaParaModal = null;
                            } else {
                                $('#itemsBody tr:last-child').find('.select2-referencia').val(newRefId).trigger('change');
                            }

                            // 4. Limpiar y cerrar
                            $('#modalReferencia').fadeOut('fast');
                            $('#ref_referencia').val('');
                            $('#ref_detalle').val('');
                            $('#ref_id_InvSubGrupos').val('');
                            $('#ref_id_InvBodegas').val('');
                            $('#modalErrors').hide();
                            btn.prop('disabled', false).text('Guardar Producto');

                            alert('Producto creado con éxito.');
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