<x-base-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
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
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --radius-lg: 18px;
            --radius-xl: 24px;
        }

        body { background-color: #f3f4f6; margin: 0; padding: 0; }
        
        .profile-container { 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 0 15px;
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
        }

        /* Header Adaptable */
        .profile-header { 
            background: white; 
            padding: 25px; 
            border-radius: var(--radius-xl) var(--radius-xl) 0 0; 
            border: 1px solid var(--slate-200); 
            border-bottom: none;
            display: flex; 
            flex-direction: column;
            gap: 20px;
            box-shadow: var(--shadow-sm);
        }

        @media (min-width: 768px) {
            .profile-header {
                padding: 35px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .profile-container { margin: 40px auto; }
        }

        .ph-title h1 { 
            font-size: 1.5rem; 
            font-weight: 800; 
            margin: 0; 
            color: var(--slate-900); 
            letter-spacing: -0.5px; 
            line-height: 1.2;
        }
        
        @media (min-width: 768px) { .ph-title h1 { font-size: 2.2rem; } }

        .ph-title p { 
            color: var(--slate-500); 
            margin: 10px 0 0 0; 
            font-weight: 500; 
            font-size: 0.9rem; 
            display: flex; 
            flex-wrap: wrap;
            gap: 15px; 
        }

        .header-actions {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        @media (min-width: 768px) { .header-actions { width: auto; } }

        .btn-action {
            flex: 1;
            text-align: center;
            text-decoration: none; 
            padding: 12px 20px; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        /* Layout Principal */
        .grid-layout { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
        }

        @media (min-width: 1024px) {
            .grid-layout { grid-template-columns: 350px 1fr; gap: 25px; }
        }

        /* Tarjetas */
        .info-card { 
            background: white; 
            border: 1px solid var(--slate-200); 
            border-radius: var(--radius-lg); 
            padding: 20px; 
            box-shadow: var(--shadow-sm); 
            margin-bottom: 20px;
        }

        .card-section-title { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-size: 0.8rem; 
            font-weight: 800; 
            color: var(--slate-900); 
            margin-bottom: 20px;
            text-transform: uppercase; 
            letter-spacing: 1px; 
            border-bottom: 2px solid var(--slate-50);
            padding-bottom: 10px;
        }

        .detail-row { margin-bottom: 18px; display: flex; flex-direction: column; gap: 4px; }
        .dr-label { font-size: 0.65rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.5px; }
        .dr-value { font-size: 0.95rem; font-weight: 600; color: var(--slate-800); word-break: break-word; }
        
        /* Grid de detalles técnicos responsive */
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 50px;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; border: 1px solid transparent;
        }
        .status-active { background: #ecfdf5; color: #059669; border-color: #10b98133; }
        .status-warning { background: #fffbeb; color: #d97706; border-color: #f59e0b33; }

        /* Módulo de Auditoría */
        .audit-card { background: var(--slate-900); color: white; border: none; }
        .audit-card .dr-label { color: var(--slate-500); }
        .audit-card .dr-value { color: white; }
        
        .btn-inspect-purchase {
            background: var(--primary); color: white; border: none; padding: 14px;
            border-radius: 12px; font-size: 0.85rem; font-weight: 800; cursor: pointer;
            display: flex; align-items: center; gap: 10px; transition: 0.3s; width: 100%; justify-content: center;
            margin-top: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .btn-inspect-purchase:hover { background: var(--primary-hover); transform: translateY(-2px); }

        .btn-view-document {
            background: var(--slate-100); color: var(--slate-900); border: 1px solid var(--slate-200);
            padding: 12px; border-radius: 12px; text-decoration: none; display: flex;
            align-items: center; justify-content: center; gap: 10px; font-weight: 700; font-size: 0.85rem;
            margin-top: 8px; transition: 0.2s;
        }
        .btn-view-document:hover { background: white; border-color: var(--primary); color: var(--primary); }

        /* Timeline */
        .timeline-container { position: relative; padding-left: 30px; margin-top: 10px; }
        .timeline-container::before { content: ''; position: absolute; left: 8px; top: 0; width: 2px; height: 100%; background: var(--slate-100); }
        
        .timeline-item { position: relative; margin-bottom: 30px; }
        .timeline-item::before { 
            content: ''; position: absolute; left: -28px; top: 4px; width: 14px; height: 14px; 
            background: white; border: 3px solid var(--primary); border-radius: 50%; z-index: 2;
        }
        .tl-date { font-size: 0.7rem; font-weight: 800; color: var(--slate-400); text-transform: uppercase; margin-bottom: 6px; }
        .tl-content { background: var(--slate-50); padding: 15px; border-radius: 14px; border: 1px solid var(--slate-200); }
        .tl-title { font-weight: 800; font-size: 0.95rem; color: var(--slate-900); }
        
        /* Modal Responsive */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); display: flex;
            align-items: center; justify-content: center; z-index: 1000; padding: 15px;
        }
        .modal-content {
            background: #fff; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;
            border-radius: var(--radius-xl); padding: 25px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        
        @media (min-width: 768px) { .modal-content { padding: 35px; } }

        .mini-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .mini-table td { padding: 12px 0; border-bottom: 1px solid var(--slate-100); font-size: 0.9rem; }
        .mini-table .label { font-weight: 800; color: var(--slate-400); text-transform: uppercase; font-size: 0.6rem; }
        .mini-table .value { font-weight: 700; color: var(--slate-900); text-align: right; }
    </style>

    <div class="profile-container">
        {{-- HEADER PRINCIPAL --}}
        <div class="profile-header">
            <div class="ph-title">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <span class="status-pill status-active">
                        <svg width="6" height="6" viewBox="0 0 10 10" style="margin-right: 6px;"><circle cx="5" cy="5" r="5" fill="currentColor"/></svg>
                        {{ $activo->estado->nombre ?? 'ESTADO N/A' }}
                    </span>
                    <span class="status-pill status-warning">PLACA: {{ $activo->codigo_activo }}</span>
                </div>
                <h1>{{ $activo->nombre }}</h1>
                <p>
                    <span><strong>SERIAL:</strong> {{ $activo->serial ?: 'NO REGISTRA' }}</span>
                    <span><strong>ID:</strong> #{{ $activo->id }}</span>
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('inventario.activos.edit', $activo->id) }}" class="btn-action" style="background: var(--slate-900); color: white;">Configuración</a>
                <a href="{{ route('inventario.activos.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--slate-600);">Inventario de Activos</a>
            </div>
        </div>

        <div class="grid-layout">
            {{-- BARRA LATERAL --}}
            <div class="side-bar">
                <div class="info-card audit-card">
                    <div class="card-section-title" style="color: var(--slate-400); border-color: rgba(255,255,255,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        Auditoría de Adquisición
                    </div>
                    <div class="detail-row">
                        <span class="dr-label">ID Detalle de Compra</span>
                        <span class="dr-value" id="val_detalle_compra_id" style="font-family: monospace; font-size: 1.1rem;">{{ $activo->id_InvDetalleCompras ?? 'MANUAL' }}</span>
                    </div>
                    @if($activo->id_InvDetalleCompras)
                        <button type="button" id="btnInspectPurchase" class="btn-inspect-purchase">
                            🔍 Inspeccionar Factura
                        </button>
                    @endif
                </div>

                <div class="info-card">
                    <div class="card-section-title">Documentación</div>
                    <div class="detail-row">
                        <span class="dr-label">Hoja de Vida</span>
                        @if($activo->hoja_vida)
                            <a href="{{ $activo->hoja_vida }}" target="_blank" class="btn-view-document">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                Ver Documento
                            </a>
                        @else
                            <span class="dr-value" style="color: var(--slate-400); font-style: italic; font-size: 0.85rem;">No cargada</span>
                        @endif
                    </div>

                    @if($activo->id_InvDetalleCompras && $activo->detalleCompra && $activo->detalleCompra->id_InvCompras)
                    <div class="detail-row" style="margin-top: 10px;">
                        <span class="dr-label">Factura Original</span>
                        <a href="{{ route('inventario.compras.archivo', $activo->detalleCompra->id_InvCompras) }}" target="_blank" class="btn-view-document">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line></svg>
                            Archivo de Factura
                        </a>
                    </div>
                    @endif
                </div>

                <div class="info-card">
                    <div class="card-section-title">Ubicación y Custodia</div>
                    <div class="detail-row">
                        <span class="dr-label">Custodio Actual</span>
                        <span class="dr-value">{{ $activo->usuarioAsignado->name ?? 'DISPONIBLE EN BODEGA' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="dr-label">Sede / Municipio</span>
                        <span class="dr-value">{{ $activo->municipio->nombre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- CONTENIDO PRINCIPAL --}}
            <div class="main-content">
                <div class="info-card">
                    <div class="card-section-title">Expediente Técnico e Información</div>
                    
                    <div class="tech-grid">
                        <div class="detail-row">
                            <span class="dr-label">Marca</span>
                            <span class="dr-value">{{ $activo->marca->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Referencia</span>
                            <span class="dr-value">{{ $activo->referencia->referencia ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Subgrupo</span>
                            <span class="dr-value">{{ $activo->subgrupo->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Unidad</span>
                            <span class="dr-value">{{ $activo->unidad_medida ?? 'UNIDAD' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Garantía</span>
                            <span class="dr-value" style="{{ $activo->fecha_fin_garantia && $activo->fecha_fin_garantia->isPast() ? 'color: var(--danger);' : 'color: var(--success);' }}">
                                {{ $activo->fecha_fin_garantia ? $activo->fecha_fin_garantia->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Vida Útil</span>
                            <span class="dr-value">{{ $activo->vida_util_meses ?? 0 }} Meses</span>
                        </div>
                    </div>

                    <div style="background: var(--slate-50); padding: 20px; border-radius: 16px; border-left: 4px solid var(--primary);">
                        <span class="dr-label">Descripción Detallada</span>
                        <p style="margin: 8px 0 0 0; color: var(--slate-600); line-height: 1.6; font-size: 0.95rem;">
                            {{ $activo->descripcion ?: 'Sin observaciones técnicas registradas.' }}
                        </p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-section-title">Bitácora de Movimientos</div>
                    <div class="timeline-container">
                        @forelse($activo->movimientos as $mov)
                            <div class="timeline-item">
                                <div class="tl-date">{{ $mov->created_at->format('d M Y - h:i A') }}</div>
                                <div class="tl-content">
                                    <div class="tl-title">{{ $mov->tipoRegistro->nombre ?? 'Movimiento' }} - Acta #{{ $mov->codigo_acta ?? 'S/N' }}</div>
                                    <div style="font-size: 0.85rem; color: var(--slate-600); margin-top: 5px;">
                                        Responsable: <strong>{{ $mov->responsable->name ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 20px; color: var(--slate-400);">
                                <p style="font-size: 0.9rem;">Sin movimientos registrados.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="info-card" style="background: var(--slate-50); border: 1px dashed var(--slate-300); margin-bottom: 0;">
                    <div class="card-section-title" style="color: var(--slate-500);">Auditoría de Sistema</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div class="detail-row">
                            <span class="dr-label">Creado por</span>
                            <span class="dr-value" style="font-size: 0.85rem;">{{ $activo->usuarioRegistro->name ?? 'SISTEMA' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="dr-label">Fecha Creación</span>
                            <span class="dr-value" style="font-size: 0.85rem;">{{ $activo->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="modalPurchase" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0; color: var(--slate-900); font-weight: 800; font-size: 1.2rem;">Detalles de Compra</h3>
                    <span id="modal_purchase_label" style="font-size: 0.65rem; font-weight: 800; color: var(--primary); text-transform: uppercase;"></span>
                </div>
                <button type="button" class="close-modal-btn" id="btnCloseIcon" style="background: none; border: none; cursor: pointer; color: var(--slate-400);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            
            <table class="mini-table">
                <tr><td class="label">Factura</td> <td class="value" id="view_master" style="color: var(--primary);"></td></tr>
                <tr><td class="label">Cant.</td> <td class="value" id="view_cant"></td></tr>
                <tr><td class="label">Costo U.</td> <td class="value" id="view_precio" style="color: var(--success);"></td></tr>
                <tr><td class="label">Subtotal</td> <td class="value" id="view_subtotal" style="font-weight: 800;"></td></tr>
                <tr><td class="label">Referencia</td> <td class="value" id="view_ref"></td></tr>
            </table>

            <div style="margin-top: 20px; padding: 15px; background: var(--slate-50); border-radius: 12px;">
                <span class="dr-label" style="margin-bottom: 5px; display: block;">Notas</span>
                <p id="view_obs" style="font-size: 0.85rem; color: var(--slate-600); margin: 0;"></p>
            </div>

            <div id="fileContainer" style="margin-top: 15px;"></div>

            <button type="button" id="btnCloseModalPurchase" style="width: 100%; background: var(--slate-900); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 20px;">Cerrar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btnInspectPurchase').click(function() {
                let id = $('#val_detalle_compra_id').text().trim();
                if(!id || id === 'MANUAL') return;

                $('#modalPurchase').fadeIn('fast').css('display', 'flex');
                $('#modal_purchase_label').text('Registro #' + id);

                $.ajax({
                    url: `/inventario/detalle-compra/ajax/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if(res.success) {
                            let d = res.data;
                            $('#view_cant').text(d.cantidades);
                            $('#view_precio').text('$' + parseFloat(d.precio_unitario).toLocaleString());
                            $('#view_subtotal').text('$' + parseFloat(d.sub_total).toLocaleString());
                            $('#view_ref').text(d.referencia?.referencia || 'SIN REF');
                            $('#view_master').text(d.compra?.numero_factura || 'S/N');
                            $('#view_obs').text(d.detalle || 'Sin notas.');

                            if(d.id_InvCompras) {
                                $('#fileContainer').html(`
                                    <a href="/inventario/compras/${d.id_InvCompras}/archivo" target="_blank" class="btn-view-document" style="background: var(--slate-900); color: white; border: none;">
                                        Ver Factura Original
                                    </a>
                                `);
                            }
                        }
                    },
                    error: function() {
                        alert('Error al obtener datos.');
                        $('#modalPurchase').fadeOut('fast');
                    }
                });
            });

            $('#btnCloseModalPurchase, #btnCloseIcon').click(function() {
                $('#modalPurchase').fadeOut('fast');
            });

            // Cerrar modal al hacer clic fuera
            $(window).click(function(e) {
                if ($(e.target).hasClass('modal-overlay')) {
                    $('#modalPurchase').fadeOut('fast');
                }
            });
        });
    </script>
</x-base-layout>