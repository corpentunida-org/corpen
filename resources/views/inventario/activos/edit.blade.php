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
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        body { background-color: #f3f4f6; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: 'Inter', system-ui, sans-serif; }

        @media (min-width: 768px) {
            .container { padding: 40px 20px; }
        }

        /* Header Estilo Expediente */
        .cv-header { 
            background: white; padding: 20px; border-radius: 20px 20px 0 0; 
            border: 1px solid var(--slate-200); border-bottom: none;
            display: flex; flex-direction: column; gap: 15px;
        }
        @media (min-width: 768px) {
            .cv-header { flex-direction: row; justify-content: space-between; align-items: center; padding: 30px; }
        }

        .asset-badge {
            background: var(--primary-soft); color: var(--primary);
            padding: 8px 16px; border-radius: 10px; font-weight: 800; font-size: 0.9rem;
            text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(79, 70, 229, 0.2);
            display: inline-block;
        }

        /* Contenedor Principal */
        .form-content { 
            background: white; padding: 20px; 
            border-radius: 0 0 20px 20px; border: 1px solid var(--slate-200);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }
        @media (min-width: 768px) { .form-content { padding: 10px 30px 30px 30px; } }

        .panel-section { 
            padding: 20px; border: 1px solid var(--slate-100); border-radius: 16px; 
            margin-bottom: 25px; transition: 0.3s;
        }
        @media (min-width: 768px) { .panel-section { padding: 25px; } }
        .panel-section:hover { border-color: var(--slate-200); background: var(--slate-50); }

        .panel-title { 
            display: flex; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 1rem; 
            font-weight: 800; color: var(--slate-900); margin-bottom: 20px;
            border-bottom: 2px solid var(--slate-100); padding-bottom: 10px;
        }

        /* GRID ADAPTABLE PARA MÓVILES */
        .input-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
        }
        @media (min-width: 768px) { .input-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .input-grid { grid-template-columns: repeat(3, 1fr); } }
        
        .full-row { grid-column: 1 / -1; }

        .field-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 0.75rem; font-weight: 800; color: var(--slate-600); text-transform: uppercase; letter-spacing: 0.5px; }
        
        input, select, textarea { 
            padding: 12px 14px; border-radius: 10px; border: 1.5px solid var(--slate-200); 
            font-size: 0.95rem; font-weight: 500; color: var(--slate-900); transition: all 0.2s;
            background: #fff; width: 100%; box-sizing: border-box;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft); outline: none; }
        
        input[type="file"] { padding: 8px; font-size: 0.85rem; background: var(--slate-50); border: 1px dashed var(--slate-400); cursor: pointer; }

        .input-inherited {
            background-color: var(--slate-100) !important;
            color: var(--slate-600) !important;
            cursor: not-allowed;
            border: 1.5px dashed var(--slate-200);
            font-weight: 600;
        }

        /* Modales Responsivos */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(3px); display: flex;
            align-items: center; justify-content: center; z-index: 1000; padding: 15px;
        }
        .modal-content {
            background: #fff; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;
            border-radius: 16px; padding: 25px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Botones Especiales */
        .btn-modal-trigger {
            padding: 8px 14px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; 
            cursor: pointer; border: none; transition: 0.2s; color: white; display: flex; align-items: center; gap: 6px;
        }
        .btn-new-ref { background: var(--success); }
        .btn-edit-ref { background: var(--warning); display: none; color: var(--slate-900); } 
        .btn-view-purchase { 
            background: var(--slate-700); margin-top: 5px; width: fit-content; padding: 6px 12px;
        }
        .btn-view-purchase:hover { background: var(--slate-900); transform: translateY(-1px); }

        .btn-view-doc {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px;
            background: var(--slate-100); color: var(--primary); font-weight: 700; font-size: 0.85rem;
            border-radius: 8px; text-decoration: none; border: 1px solid var(--slate-200);
            transition: 0.2s; width: fit-content; white-space: nowrap;
        }
        .btn-view-doc:hover { background: white; border-color: var(--primary); }

        .action-bar { 
            position: sticky; bottom: 20px; background: rgba(15, 23, 42, 0.95); 
            backdrop-filter: blur(10px); padding: 15px 20px; border-radius: 15px;
            display: flex; flex-direction: column-reverse; justify-content: space-between; align-items: center; gap: 15px;
            margin-top: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); z-index: 100;
        }
        @media (min-width: 768px) {
            .action-bar { flex-direction: row; padding: 15px 30px; }
        }

        .btn-submit { 
            background: var(--primary); color: white; border: none; padding: 12px 30px; 
            border-radius: 10px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.2s;
        }
        .btn-submit:hover { background: #4338ca; }
        @media (min-width: 768px) { .btn-submit { width: auto; } }

        /* Tabla Minimalista Modal Compra */
        .mini-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .mini-table td { padding: 10px 0; border-bottom: 1px solid var(--slate-100); font-size: 0.9rem; }
        .mini-table .label { font-weight: 800; color: var(--slate-500); width: 40%; }
        .mini-table .value { font-weight: 600; color: var(--slate-900); }
    </style>

    <div class="container">
        
        <datalist id="referenciasDataList">
            @foreach($referencias as $ref)
                <option data-id="{{ $ref->id }}" 
                        data-pure-name="{{ $ref->referencia }}"
                        data-marca-nom="{{ $ref->marca->nombre ?? 'N/A' }}"
                        data-marca-id="{{ $ref->id_MaeMarcas }}"
                        data-sub-nom="{{ $ref->subgrupo->nombre ?? 'N/A' }}"
                        data-sub-id="{{ $ref->id_MaeSubgrupo }}"
                        data-bodega-nom="{{ $ref->bodega->nombre ?? 'N/A' }}"
                        data-bodega-id="{{ $ref->id_InvBodegas ?? '' }}" 
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

        <div class="cv-header">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 900; margin: 0; color: var(--slate-900);">Expediente Técnico del Activo</h1>
                <p style="margin: 5px 0 0 0; color: var(--slate-600); font-weight: 500; font-size: 0.9rem;">Hoja de Vida e Historial de Adquisición</p>
            </div>
            <div style="text-align: right;">
                <div class="asset-badge">MARQUILLA: {{ $activo->codigo_activo }}</div>
                <p style="font-size: 0.75rem; color: var(--slate-500); margin-top: 8px; font-weight: 700;">ID SISTEMA: #{{ $activo->id }}</p>
            </div>
        </div>

        {{-- Formulario habilitado para archivos --}}
        <form action="{{ route('inventario.activos.update', $activo->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-content">
                
                {{-- SECCIÓN 1: IDENTIDAD --}}
                <div class="panel-section">
                    <div class="panel-title">1. Identidad del Activo</div>
                    <div class="input-grid">
                        <div class="field-group">
                            <label>Código de Marquilla</label>
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
                        <div>2. Clasificación Técnica</div>
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
                    <div class="panel-title">3. Ciclo de Vida y Adquisición</div>
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
                        
                        {{-- CAMPO HOJA DE VIDA (ENLACE DE TEXTO RESTAURADO) --}}
                        <div class="field-group full-row">
                            <label>Documento Hoja de Vida (Enlace)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" name="hoja_vida" value="{{ old('hoja_vida', $activo->hoja_vida) }}" placeholder="Ej: https://drive.google.com/..." style="flex: 1;">
                                @if($activo->hoja_vida)
                                    <a href="{{ $activo->hoja_vida }}" target="_blank" class="btn-view-doc">
                                        📄 Ver Link
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="field-group">
                            <label>ID Detalle de Compra</label>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <input type="text" name="id_InvDetalleCompras" id="id_InvDetalleCompras" 
                                       value="{{ old('id_InvDetalleCompras', $activo->id_InvDetalleCompras) }}" 
                                       class="input-inherited" readonly tabindex="-1">
                                
                                @if($activo->id_InvDetalleCompras)
                                    <button type="button" id="btnOpenModalCompra" class="btn-modal-trigger btn-view-purchase">
                                        Ver Datos de Compra
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- CAMPO DEL ARCHIVO FÍSICO (eg_archivo) --}}
                        <div class="field-group full-row" style="background: var(--slate-50); padding: 15px; border-radius: 12px; border: 1px dashed var(--slate-300); margin-top: 10px;">
                            <label style="color: var(--slate-900);">Factura</label>
                            
                            @if($activo->detalleCompra && $activo->detalleCompra->id_InvCompras)
                                <div style="margin-bottom: 12px;">
                                    <a href="{{ route('inventario.compras.archivo', $activo->detalleCompra->id_InvCompras) }}" target="_blank" class="btn-view-doc" style="background: white; border-color: var(--primary);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        Ver Documento
                                    </a>
                                </div>
                            @endif

                            <input type="file" name="eg_archivo" accept=".pdf,.doc,.docx,.jpg,.png">
                            <small style="color: var(--slate-500); font-weight: 600; margin-top: 6px; display: block; line-height: 1.4;">
                                Seleccione un archivo solo si desea actualizar el documento en la tabla <b>inv_compras</b>.
                            </small>
                        </div>

                    </div>
                </div>

                {{-- SECCIÓN 4: GESTIÓN --}}
                <div class="panel-section">
                    <div class="panel-title">4. Control de Estado y Ubicación</div>
                    <div class="input-grid">
                        
                        {{-- NUEVO: Bodega de Origen (Filtro Visual) --}}
                        <div class="field-group">
                            <label>Bodega (Filtro de Estados)</label>
                            <select id="bodega_estado_select">
                                <option value="">Mostrar todos los estados...</option>
                                @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label>Estado Actual</label>
                            {{-- Se agregó id="estado_select" --}}
                            <select name="id_Estado" id="estado_select" required>
                                <option value="">Seleccione un estado...</option>
                                @foreach($estados as $est)
                                    {{-- Se agregó data-bodega --}}
                                    <option value="{{ $est->id }}" data-bodega="{{ $est->id_bodega }}" {{ $activo->id_Estado == $est->id ? 'selected' : '' }}>
                                        {{ $est->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="field-group">
                            <label style="color: var(--primary);">Ubicación / Sede</label>
                            <input list="municipiosDataList" id="search_municipio" 
                                   value="{{ $activo->municipio->nombre ?? '' }}" 
                                   placeholder="Escriba municipio o sede...">
                            <input type="hidden" name="id_MaeMunicipios" id="hidden_municipio_id" value="{{ $activo->id_MaeMunicipios }}">
                        </div>

                        <div class="field-group">
                            <label style="color: var(--primary);">Responsable Asignado</label>
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
                <a href="{{ route('inventario.activos.show', $activo->id) }}" style="color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;">← Volver al Expediente</a>
                <button type="submit" class="btn-submit">Actualizar Expediente Completo</button>
            </div>
        </form>
    </div>

    {{-- MODALES --}}
    <div id="modalRef" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-top:0; color: var(--slate-900);">Gestionar Referencia</h3>
            <hr style="border: 0; border-top: 1px solid var(--slate-100); margin-bottom: 20px;">
            <input type="hidden" id="modal_ref_id">
            
            <div class="field-group">
                <label>Nombre de Referencia *</label>
                <input type="text" id="modal_ref_nombre">
            </div>

            {{-- NUEVO: CAMPO DETALLE AÑADIDO AL HTML --}}
            <div class="field-group" style="margin-top: 15px;">
                <label>Detalle / Descripción *</label>
                <input type="text" id="modal_ref_detalle" placeholder="Agregue un detalle o '-' si no tiene">
            </div>

            <div class="input-grid" style="gap: 15px; margin-top: 15px;">
                <div class="field-group">
                    <label>Marca *</label>
                    <select id="modal_ref_marca">
                        <option value="">Seleccione...</option>
                        @foreach($marcas as $m) <option value="{{ $m->id }}">{{ $m->nombre }}</option> @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label>Subgrupo *</label>
                    <select id="modal_ref_subgrupo">
                        <option value="">Seleccione...</option>
                        @foreach($subgrupos as $s) <option value="{{ $s->id }}">{{ $s->nombre }}</option> @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label class="label">Bodega *</label>
                    <select id="ref_id_InvBodegas" required>
                        <option value="">Seleccione una bodega...</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" id="btnCloseModal" style="background: var(--slate-100); color: var(--slate-700); font-weight: 700; border: none; padding: 12px 20px; border-radius: 10px; cursor: pointer;">Cancelar</button>
                <button type="button" id="btnSaveRefAjax" style="background: var(--primary); color: white; font-weight: 700; border: none; padding: 12px 20px; border-radius: 10px; cursor: pointer;">Guardar</button>
            </div>
        </div>
    </div>

    <div id="modalCompra" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: var(--slate-900);">Datos de Adquisición</h3>
            <hr style="border: 0; border-top: 1px solid var(--slate-100);">
            <table class="mini-table">
                <tr><td class="label">ID Registro:</td> <td class="value" id="view_compra_id"></td></tr>
                <tr><td class="label">Cantidades:</td> <td class="value" id="view_compra_cant"></td></tr>
                <tr><td class="label">Precio Unit:</td> <td class="value" id="view_compra_precio" style="color: var(--success);"></td></tr>
                <tr><td class="label">Total Item:</td> <td class="value" id="view_compra_subtotal"></td></tr>
                <tr><td class="label">Referencia:</td> <td class="value" id="view_compra_ref"></td></tr>
                <tr><td class="label" style="vertical-align: top;">Detalle:</td> <td class="value" id="view_compra_detalle"></td></tr>
            </table>

            {{-- Contenedor donde se inyectará el botón para ver el archivo S3 --}}
            <div id="fileContainer"></div>

            <div style="margin-top: 25px;">
                <button type="button" id="btnCloseModalCompra" style="width: 100%; background: var(--slate-900); color: white; border: none; padding: 14px; border-radius: 12px; cursor: pointer; font-weight: 700;">Cerrar Consulta</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            
            let dictRefs = {};
            let dictMuni = {};
            let dictUser = {};
            let dictionariesLoaded = false;

            // OPTIMIZACIÓN: Solo leer el valor actual al inicio para el botón de editar
            let initialRefId = $('#hidden_referencia_id').val();
            if (initialRefId) $('#btnEditRef').css('display', 'inline-flex'); else $('#btnEditRef').css('display', 'none');

            // OPTIMIZACIÓN: Función para cargar diccionarios bajo demanda
            function loadDictionaries() {
                if (dictionariesLoaded) return;
                
                // Usar requestAnimationFrame para no bloquear la interfaz mientras el usuario escribe
                requestAnimationFrame(() => {
                    $('#referenciasDataList option').each(function() {
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
                    $('#municipiosDataList option').each(function() { dictMuni[$(this).val()] = $(this).attr('data-id'); });
                    $('#usuariosDataList option').each(function() { dictUser[$(this).val()] = $(this).attr('data-id'); });
                    
                    dictionariesLoaded = true;
                });
            }

            // OPTIMIZACIÓN: Solo cargar los diccionarios cuando el usuario interactúa con los inputs
            $('#search_referencia, #search_municipio, #search_usuario, #btnEditRef').on('focus click', function() {
                loadDictionaries();
            });

            $(document).on('input change', '#search_referencia', function() {
                loadDictionaries(); // Asegurar que estén cargados
                
                let val = $(this).val();
                
                // Pequeño timeout para permitir que el diccionario se cargue si fue la primera interacción
                setTimeout(() => {
                    if (dictRefs[val]) {
                        $('#hidden_referencia_id').val(dictRefs[val].id);
                        $('#display_marca').val(dictRefs[val].marcaNom);
                        $('#display_subgrupo').val(dictRefs[val].subNom);
                        $(this).css('border-color', 'var(--slate-200)');
                        $('#btnEditRef').css('display', 'inline-flex');
                    } else {
                        $('#hidden_referencia_id').val('');
                        if(val) $(this).css('border-color', 'var(--danger)');
                        $('#btnEditRef').css('display', 'none');
                    }
                }, 10);
            });

            $(document).on('input change', '#search_municipio', function() {
                loadDictionaries();
                setTimeout(() => {
                    let val = $(this).val();
                    if (dictMuni[val]) { $('#hidden_municipio_id').val(dictMuni[val]); $(this).css('border-color', 'var(--slate-200)'); } 
                    else { $('#hidden_municipio_id').val(''); if(val) $(this).css('border-color', 'var(--danger)'); }
                }, 10);
            });

            $(document).on('input change', '#search_usuario', function() {
                loadDictionaries();
                setTimeout(() => {
                    let val = $(this).val();
                    if (dictUser[val]) { $('#hidden_usuario_id').val(dictUser[val]); $(this).css('border-color', 'var(--slate-200)'); } 
                    else { $('#hidden_usuario_id').val(''); if(val) $(this).css('border-color', 'var(--danger)'); }
                }, 10);
            });

            // MODAL REFERENCIAS: CREAR NUEVA
            $('#btnNewRef').click(function() {
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
            $('#btnEditRef').click(function() {
                loadDictionaries();
                
                setTimeout(() => {
                    let searchVal = $('#search_referencia').val();
                    let refData = dictRefs[searchVal];
                    
                    if(!refData || !refData.id) return alert('Seleccione una referencia válida primero.');

                    $('#modal_ref_id').val(refData.id);
                    $('#modal_ref_nombre').val(refData.pureName);
                    $('#modal_ref_detalle').val(refData.detalle); 
                    $('#modal_ref_marca').val(refData.marcaId);
                    $('#modal_ref_subgrupo').val(refData.subId);
                    $('#ref_id_InvBodegas').val(refData.bodegaId); 
                    
                    $('#modalTitle').text('Editar Referencia Actual');
                    $('#modalRef').fadeIn('fast').css('display', 'flex');
                }, 10);
            });

            $('#btnCloseModal').click(() => $('#modalRef').fadeOut('fast'));

            // BOTÓN GUARDAR AJAX
            $('#btnSaveRefAjax').click(function() {
                let id = $('#modal_ref_id').val();
                let isUpdate = id !== '';
                
                let data = {
                    _token: '{{ csrf_token() }}',
                    referencia: $('#modal_ref_nombre').val(),
                    detalle: $('#modal_ref_detalle').val(), 
                    id_MaeMarcas: $('#modal_ref_marca').val(),
                    id_MaeSubgrupo: $('#modal_ref_subgrupo').val(),
                    id_InvBodegas: $('#ref_id_InvBodegas').val()
                };

                if(!data.referencia || !data.detalle || !data.id_MaeMarcas || !data.id_MaeSubgrupo || !data.id_InvBodegas) {
                    return alert('Por favor, completa todos los campos requeridos.');
                }

                let url = isUpdate ? `/inventario/referencias/ajax/${id}` : '/inventario/referencias/ajax';
                let method = isUpdate ? 'PUT' : 'POST';

                let $btn = $(this);
                $btn.prop('disabled', true).text('Guardando...');

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(res) {
                        if(res.success) {
                            alert(isUpdate ? 'Referencia actualizada con éxito.' : 'Referencia creada con éxito.');
                            $('#modalRef').fadeOut('fast');
                            location.reload(); 
                        } else {
                            alert('Error: ' + (res.message || 'No se pudo procesar la solicitud.'));
                            $btn.prop('disabled', false).text('Guardar');
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        alert('Ocurrió un error en el servidor. Revisa la consola o los logs de Laravel.');
                        $btn.prop('disabled', false).text('Guardar');
                    }
                });
            });

            // MODAL COMPRAS
            $('#btnOpenModalCompra').click(function() {
                let id = $('#id_InvDetalleCompras').val();
                if(!id) return;
                
                $('#modalCompra').fadeIn('fast').css('display', 'flex');
                
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

                            if(d.id_InvCompras) {
                                $('#fileContainer').html(`
                                    <a href="/inventario/compras/${d.id_InvCompras}/archivo" target="_blank" 
                                       style="display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--slate-100); color: var(--slate-900); padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 700; border: 1px solid var(--slate-200); margin-top: 15px;">
                                        Ver Factura
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

            $('#btnCloseModalCompra').click(() => $('#modalCompra').fadeOut('fast'));

            $('.modal-overlay').click(function(e) {
                if(e.target === this) { $(this).fadeOut('fast'); }
            });
            // =========================================================
            // LÓGICA DE ANIDAMIENTO: BODEGA -> ESTADO (SECCIÓN 4)
            // =========================================================
            let $bodegaSelect = $('#bodega_estado_select');
            let $estadoSelect = $('#estado_select');
            
            // 1. Guardamos una copia exacta de todas las opciones originales en memoria
            let todasOpcionesEstado = $estadoSelect.find('option').clone();

            function filtrarEstados(bodegaId, estadoIdASeleccionar = null) {
                $estadoSelect.empty(); // Limpiamos el select actual
                
                if (bodegaId) {
                    // Filtramos: dejamos la opción vacía y las que coincidan con la bodega
                    let opcionesFiltradas = todasOpcionesEstado.filter(function() {
                        return $(this).val() === "" || $(this).attr('data-bodega') == bodegaId;
                    });
                    $estadoSelect.append(opcionesFiltradas);
                } else {
                    // Si no elige bodega, mostramos todos los estados
                    $estadoSelect.append(todasOpcionesEstado);
                }

                // Si se pasó un estado para preseleccionar (en la carga inicial), lo aplicamos
                if (estadoIdASeleccionar) {
                    $estadoSelect.val(estadoIdASeleccionar);
                } else {
                    $estadoSelect.val(''); // Dejar en "Seleccione..."
                }
            }

            // 2. Evento: Al cambiar la bodega manualmente
            $bodegaSelect.on('change', function() {
                filtrarEstados($(this).val());
            });

            // 3. Ejecución Inicial (Al cargar la página):
            // Necesitamos detectar qué estado tiene guardado el activo actualmente
            // para auto-seleccionar la "Bodega" correspondiente a ese estado en el filtro visual.
            let estadoActualOption = todasOpcionesEstado.filter(':selected');
            
            if (estadoActualOption.length > 0 && estadoActualOption.val() !== "") {
                let bodegaDelEstadoActual = estadoActualOption.attr('data-bodega');
                
                if (bodegaDelEstadoActual) {
                    // Auto-seleccionamos la bodega en el filtro visual
                    $bodegaSelect.val(bodegaDelEstadoActual);
                    // Ejecutamos el filtro para ocultar los demás estados, pero manteniendo seleccionado el estado actual
                    filtrarEstados(bodegaDelEstadoActual, estadoActualOption.val());
                }
            }
            // =========================================================
        });
    </script>
</x-base-layout>