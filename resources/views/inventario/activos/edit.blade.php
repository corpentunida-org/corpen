<x-base-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        body { background-color: #f3f4f6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', system-ui, sans-serif; }

        /* Header Estilo Expediente */
        .cv-header { 
            background: white; padding: 30px; border-radius: 20px 20px 0 0; 
            border: 1px solid var(--slate-200); border-bottom: none;
            display: flex; justify-content: space-between; align-items: center;
        }
        .asset-badge {
            background: var(--primary-soft); color: var(--primary);
            padding: 8px 16px; border-radius: 10px; font-weight: 800; font-size: 0.9rem;
            text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(79, 70, 229, 0.2);
        }

        /* Contenedor Principal */
        .form-content { 
            background: white; padding: 10px 30px 30px 30px; 
            border-radius: 0 0 20px 20px; border: 1px solid var(--slate-200);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }

        .panel-section { 
            padding: 25px; border: 1px solid var(--slate-100); border-radius: 16px; 
            margin-bottom: 25px; transition: 0.3s;
        }
        .panel-section:hover { border-color: var(--slate-200); background: var(--slate-50); }

        .panel-title { 
            display: flex; align-items: center; gap: 10px; font-size: 1rem; 
            font-weight: 800; color: var(--slate-900); margin-bottom: 20px;
            border-bottom: 2px solid var(--slate-100); padding-bottom: 10px;
        }

        .input-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .full-row { grid-column: 1 / -1; }

        .field-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 0.75rem; font-weight: 800; color: var(--slate-600); text-transform: uppercase; letter-spacing: 0.5px; }
        
        input, select, textarea { 
            padding: 12px 14px; border-radius: 10px; border: 1.5px solid var(--slate-200); 
            font-size: 0.95rem; font-weight: 500; color: var(--slate-900); transition: all 0.2s;
            background: #fff; width: 100%; box-sizing: border-box;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft); outline: none; }

        .input-inherited {
            background-color: var(--slate-100) !important;
            color: var(--slate-600) !important;
            cursor: not-allowed;
            border: 1.5px dashed var(--slate-200);
            font-weight: 600;
        }

        /* Modales */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); display: flex;
            align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content {
            background: #fff; width: 100%; max-width: 500px;
            border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Botones Especiales */
        .btn-modal-trigger {
            padding: 8px 14px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; 
            cursor: pointer; border: none; transition: 0.2s; color: white; display: flex; align-items: center; gap: 6px;
        }
        .btn-new-ref { background: var(--success); }
        .btn-edit-ref { background: var(--warning); display: none; } 
        .btn-view-purchase { 
            background: var(--slate-700); 
            margin-top: 5px;
            width: fit-content;
            padding: 6px 12px;
        }
        .btn-view-purchase:hover { background: var(--slate-900); transform: translateY(-1px); }

        .action-bar { 
            position: sticky; bottom: 20px; background: rgba(15, 23, 42, 0.95); 
            backdrop-filter: blur(10px); padding: 15px 30px; border-radius: 15px;
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);
            z-index: 100;
        }
        .btn-submit { 
            background: var(--primary); color: white; border: none; padding: 12px 35px; 
            border-radius: 10px; font-weight: 700; cursor: pointer;
        }

        /* Tabla Minimalista Modal Compra */
        .mini-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .mini-table td { padding: 10px; border-bottom: 1px solid var(--slate-100); font-size: 0.9rem; }
        .mini-table .label { font-weight: 800; color: var(--slate-500); width: 40%; }
        .mini-table .value { font-weight: 600; color: var(--slate-900); }
    </style>

    <div class="container">
        
        {{-- DATALIST: REFERENCIAS --}}
        <datalist id="referenciasDataList">
            @foreach($referencias as $ref)
                <option data-id="{{ $ref->id }}" 
                        data-pure-name="{{ $ref->referencia }}"
                        data-marca-nom="{{ $ref->marca->nombre ?? 'N/A' }}"
                        data-sub-nom="{{ $ref->subgrupo->nombre ?? 'N/A' }}"
                        data-bodega-nom="{{ $ref->bodega->nombre ?? 'N/A' }}"
                        value="{{ $ref->referencia }} | Marca: {{ $ref->marca->nombre ?? 'N/A' }} | Bodega: {{ $ref->bodega->nombre ?? 'N/A' }}">
                </option>
            @endforeach
        </datalist>

        {{-- DATALIST: MUNICIPIOS (NUEVO) --}}
        <datalist id="municipiosDataList">
            @foreach($municipios as $muni)
                <option data-id="{{ $muni->id }}" value="{{ $muni->nombre }}"></option>
            @endforeach
        </datalist>

        {{-- DATALIST: USUARIOS (NUEVO) --}}
        <datalist id="usuariosDataList">
            @foreach($usuarios as $user)
                <option data-id="{{ $user->id }}" value="{{ $user->name }}"></option>
            @endforeach
        </datalist>

        <div class="cv-header">
            <div>
                <h1 style="font-size: 1.6rem; font-weight: 900; margin: 0;">Expediente Técnico del Activo</h1>
                <p style="margin: 0; color: var(--slate-600); font-weight: 500;">Hoja de Vida e Historial de Adquisición</p>
            </div>
            <div style="text-align: right;">
                <div class="asset-badge">PLACA: {{ $activo->codigo_activo }}</div>
                <p style="font-size: 0.7rem; color: var(--slate-400); margin-top: 5px; font-weight: 700;">ID SISTEMA: #{{ $activo->id }}</p>
            </div>
        </div>

        <form action="{{ route('inventario.activos.update', $activo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-content">
                
                {{-- SECCIÓN 1: IDENTIDAD --}}
                <div class="panel-section">
                    <div class="panel-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        1. Identidad del Activo
                    </div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label>Código de Placa</label>
                            <input type="text" name="codigo_activo" value="{{ old('codigo_activo', $activo->codigo_activo) }}" required>
                        </div>
                        <div class="field-group">
                            <label>Nombre del Activo</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $activo->nombre) }}" required>
                        </div>
                        <div class="field-group">
                            <label>Serial de Fábrica</label>
                            <input type="text" name="serial" value="{{ old('serial', $activo->serial) }}" placeholder="N/A">
                        </div>
                        <div class="field-group full-row">
                            <label>Descripción y Observaciones Técnicas</label>
                            <textarea name="descripcion" rows="3">{{ old('descripcion', $activo->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: CLASIFICACIÓN --}}
                <div class="panel-section">
                    <div class="panel-title" style="justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line></svg>
                            2. Clasificación Técnica
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-modal-trigger btn-edit-ref" id="btnEditRef">✏️ Editar Referencia</button>
                            <button type="button" class="btn-modal-trigger btn-new-ref" id="btnNewRef">+ Crear Referencia</button>
                        </div>
                    </div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label style="color: var(--primary);">Buscador de Referencia *</label>
                            <input list="referenciasDataList" id="search_referencia" 
                                   value="{{ $activo->referencia->referencia ?? '' }} | Marca: {{ $activo->marca->nombre ?? 'N/A' }} | Bodega: {{ $activo->bodega->nombre ?? 'N/A' }}"
                                   placeholder="Escriba para buscar...">
                            <input type="hidden" name="invReferencias_id" id="hidden_referencia_id" value="{{ $activo->invReferencias_id }}">
                        </div>
                        <div class="field-group">
                            <label>Marca (Automática)</label>
                            <input type="text" id="display_marca" class="input-inherited" value="{{ $activo->marca->nombre ?? 'N/A' }}" readonly tabindex="-1">
                        </div>
                        <div class="field-group">
                            <label>Subgrupo / Categoría</label>
                            <input type="text" id="display_subgrupo" class="input-inherited" value="{{ $activo->subgrupo->nombre ?? 'N/A' }}" readonly tabindex="-1">
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 3: CICLO DE VIDA, GARANTÍAS Y COMPRA --}}
                <div class="panel-section">
                    <div class="panel-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        3. Ciclo de Vida y Adquisición
                    </div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label>Inicio de Garantía</label>
                            <input type="date" name="fecha_inicio_garantia" value="{{ old('fecha_inicio_garantia', $activo->fecha_inicio_garantia ? $activo->fecha_inicio_garantia->format('Y-m-d') : '') }}">
                        </div>
                        <div class="field-group">
                            <label>Fin de Garantía</label>
                            <input type="date" name="fecha_fin_garantia" value="{{ old('fecha_fin_garantia', $activo->fecha_fin_garantia ? $activo->fecha_fin_garantia->format('Y-m-d') : '') }}">
                        </div>
                        <div class="field-group">
                            <label>Vida Útil (Meses)</label>
                            <input type="number" name="vida_util_meses" value="{{ old('vida_util_meses', $activo->vida_util_meses) }}">
                        </div>
                        <div class="field-group">
                            <label>Unidad de Medida</label>
                            <input type="text" name="unidad_medida" value="{{ old('unidad_medida', $activo->unidad_medida) }}" placeholder="UND, MTS, PZA...">
                        </div>
                        <div class="field-group">
                            <label>Documento Hoja de Vida</label>
                            <input type="text" name="hoja_vida" value="{{ old('hoja_vida', $activo->hoja_vida) }}" placeholder="Enlace o ruta de archivo">
                        </div>
                        <div class="field-group">
                            <label>ID Detalle de Compra</label>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <input type="text" name="id_InvDetalleCompras" id="id_InvDetalleCompras" 
                                       value="{{ old('id_InvDetalleCompras', $activo->id_InvDetalleCompras) }}" 
                                       class="input-inherited" readonly tabindex="-1">
                                
                                @if($activo->id_InvDetalleCompras)
                                    <button type="button" id="btnOpenModalCompra" class="btn-modal-trigger btn-view-purchase">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        Ver Datos de Compra
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 4: GESTIÓN (REDISEÑADA CON BUSCADORES) --}}
                <div class="panel-section">
                    <div class="panel-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        4. Control de Estado y Ubicación
                    </div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label>Estado Actual</label>
                            <select name="id_Estado" required>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id }}" {{ $activo->id_Estado == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- BUSCADOR UBICACIÓN --}}
                        <div class="field-group">
                            <label style="color: var(--primary);">Ubicación / Sede (Buscador)</label>
                            <input list="municipiosDataList" id="search_municipio" 
                                   value="{{ $activo->municipio->nombre ?? '' }}" 
                                   placeholder="Escriba municipio o sede...">
                            <input type="hidden" name="id_MaeMunicipios" id="hidden_municipio_id" value="{{ $activo->id_MaeMunicipios }}">
                        </div>

                        {{-- BUSCADOR RESPONSABLE --}}
                        <div class="field-group">
                            <label style="color: var(--primary);">Responsable Asignado (Buscador)</label>
                            <input list="usuariosDataList" id="search_usuario" 
                                   value="{{ $activo->usuarioAsignado->name ?? '' }}" 
                                   placeholder="Escriba nombre de usuario...">
                            <input type="hidden" name="id_ultimo_usuario_asignado" id="hidden_usuario_id" value="{{ $activo->id_ultimo_usuario_asignado }}">
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 5: AUDITORÍA --}}
                <div class="panel-section" style="margin-bottom: 0; background: var(--slate-50);">
                    <div class="panel-title">5. Auditoría del Registro</div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label>Registrado por</label>
                            <input type="text" class="input-inherited" value="{{ $activo->usuarioRegistro->name ?? 'Sistema' }}" readonly>
                            <input type="hidden" name="id_usersRegistro" value="{{ $activo->id_usersRegistro }}">
                        </div>
                        <div class="field-group">
                            <label>Fecha Creación</label>
                            <input type="text" class="input-inherited" value="{{ $activo->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>
                        <div class="field-group">
                            <label>Última Modificación</label>
                            <input type="text" class="input-inherited" value="{{ $activo->updated_at->format('d/m/Y H:i') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('inventario.activos.index') }}" style="color: white; text-decoration: none; font-weight: 600;">← Cancelar</a>
                <button type="submit" class="btn-submit">💾 Actualizar Expediente Completo</button>
            </div>
        </form>
    </div>

    {{-- MODALES (REFERENCIAS Y COMPRA) --}}
    <div id="modalRef" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 id="modalTitle">Referencia Maestra</h3>
            <hr style="border: 0; border-top: 1px solid var(--slate-100); margin-bottom: 20px;">
            <input type="hidden" id="modal_ref_id">
            <div class="field-group">
                <label>Nombre *</label>
                <input type="text" id="modal_ref_nombre">
            </div>
            <div class="input-grid" style="grid-template-columns: 1fr 1fr; margin-top: 15px;">
                <div class="field-group">
                    <label>Marca *</label>
                    <select id="modal_ref_marca">
                        @foreach($marcas as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label>Subgrupo *</label>
                    <select id="modal_ref_subgrupo">
                        @foreach($subgrupos as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top: 30px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" id="btnCloseModal" style="background: var(--slate-100); border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Cerrar</button>
                <button type="button" id="btnSaveRefAjax" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Guardar</button>
            </div>
        </div>
    </div>

    <div id="modalCompra" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="max-width: 450px;">
            <h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Datos de Adquisición
            </h3>
            <hr style="border: 0; border-top: 1px solid var(--slate-100);">
            <table class="mini-table">
                <tr><td class="label">ID Registro:</td> <td class="value" id="view_compra_id"></td></tr>
                <tr><td class="label">Cantidades:</td> <td class="value" id="view_compra_cant"></td></tr>
                <tr><td class="label">Precio Unit:</td> <td class="value" id="view_compra_precio" style="color: var(--success);"></td></tr>
                <tr><td class="label">Total Item:</td> <td class="value" id="view_compra_subtotal"></td></tr>
                <tr><td class="label">Referencia:</td> <td class="value" id="view_compra_ref"></td></tr>
                <tr><td class="label" style="vertical-align: top;">Detalle:</td> <td class="value" id="view_compra_detalle"></td></tr>
            </table>
            <div style="margin-top: 25px;">
                <button type="button" id="btnCloseModalCompra" style="width: 100%; background: var(--slate-900); color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 700;">Cerrar Consulta</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            
            // DICCIONARIOS GLOBALES
            let dictRefs = {};
            let dictMuni = {};
            let dictUser = {};

            // 1. CARGAR DICCIONARIOS DESDE DATALISTS
            function initDictionaries() {
                // Referencias
                $('#referenciasDataList option').each(function() {
                    dictRefs[$(this).val()] = {
                        id: $(this).attr('data-id'),
                        marcaNom: $(this).attr('data-marca-nom'),
                        subNom: $(this).attr('data-sub-nom')
                    };
                });
                // Municipios
                $('#municipiosDataList option').each(function() {
                    dictMuni[$(this).val()] = $(this).attr('data-id');
                });
                // Usuarios
                $('#usuariosDataList option').each(function() {
                    dictUser[$(this).val()] = $(this).attr('data-id');
                });

                // Toggle Botón Editar Ref
                let idRef = $('#hidden_referencia_id').val();
                if (idRef) $('#btnEditRef').fadeIn(); else $('#btnEditRef').fadeOut();
            }
            initDictionaries();

            // 2. LISTENERS DE BUSCADORES

            // Buscador Referencias
            $(document).on('input change', '#search_referencia', function() {
                let val = $(this).val();
                if (dictRefs[val]) {
                    $('#hidden_referencia_id').val(dictRefs[val].id);
                    $('#display_marca').val(dictRefs[val].marcaNom);
                    $('#display_subgrupo').val(dictRefs[val].subNom);
                    $(this).css('border-color', 'var(--slate-200)');
                } else {
                    $('#hidden_referencia_id').val('');
                    if(val) $(this).css('border-color', 'var(--danger)');
                }
                initDictionaries(); 
            });

            // Buscador Municipios
            $(document).on('input change', '#search_municipio', function() {
                let val = $(this).val();
                if (dictMuni[val]) {
                    $('#hidden_municipio_id').val(dictMuni[val]);
                    $(this).css('border-color', 'var(--slate-200)');
                } else {
                    $('#hidden_municipio_id').val('');
                    if(val) $(this).css('border-color', 'var(--danger)');
                }
            });

            // Buscador Usuarios
            $(document).on('input change', '#search_usuario', function() {
                let val = $(this).val();
                if (dictUser[val]) {
                    $('#hidden_usuario_id').val(dictUser[val]);
                    $(this).css('border-color', 'var(--slate-200)');
                } else {
                    $('#hidden_usuario_id').val('');
                    if(val) $(this).css('border-color', 'var(--danger)');
                }
            });

            // 3. LOGICA MODALES
            $('#btnNewRef').click(() => $('#modalRef').fadeIn('fast'));
            $('#btnCloseModal').click(() => $('#modalRef').fadeOut('fast'));

            $('#btnOpenModalCompra').click(function() {
                let id = $('#id_InvDetalleCompras').val();
                if(!id) return;
                $('#modalCompra').fadeIn('fast');
                $.ajax({
                    url: `/inventario/detalle-compra/ajax/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if(res.success) {
                            let d = res.data;
                            $('#view_compra_id').text('#' + d.id);
                            $('#view_compra_cant').text(d.cantidades);
                            $('#view_compra_precio').text('$' + parseFloat(d.precio_unitario).toLocaleString());
                            $('#view_compra_subtotal').text('$' + parseFloat(d.sub_total).toLocaleString());
                            $('#view_compra_ref').text(d.referencia?.referencia || 'N/A');
                            $('#view_compra_detalle').text(d.detalle || 'Sin observaciones');
                        }
                    },
                    error: () => alert('Error al consultar los datos de compra.')
                });
            });

            $('#btnCloseModalCompra').click(() => $('#modalCompra').fadeOut('fast'));
        });
    </script>
</x-base-layout>