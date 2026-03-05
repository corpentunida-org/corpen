<x-base-layout>
    <style>
        /* Variables y tipografía */
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', system-ui, sans-serif; color: #0f172a; }
        
        /* Encabezado */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
        .btn-new { background: #0f172a; color: #fff; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2); }
        .btn-new:hover { background: #334155; transform: translateY(-2px); box-shadow: 0 6px 10px -1px rgba(15, 23, 42, 0.3); }
        
        /* Alertas (Flash Messages) */
        .alert { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; font-size: 0.95rem; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        
        /* Tabla y Tarjeta */
        .table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03); }
        .table-responsive { overflow-x: auto; width: 100%; }
        .t-min { width: 100%; border-collapse: collapse; white-space: nowrap; }
        .t-min th { background: #f8fafc; text-align: left; padding: 18px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; }
        .t-min td { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; vertical-align: middle; transition: background 0.2s; }
        .t-min tbody tr:hover td { background: #f8fafc; } /* Efecto hover en filas */
        
        /* Textos auxiliares */
        .text-muted { color: #64748b; font-size: 0.8rem; display: block; margin-top: 4px; transition: color 0.2s; }
        .cost-badge { background: #f1f5f9; padding: 6px 10px; border-radius: 6px; font-family: 'Fira Code', monospace; font-weight: 700; color: #0f172a; }

        /* ENLACE DEL ACTIVO (NUEVO) */
        .activo-link { text-decoration: none; display: inline-block; }
        .activo-link b { color: #4f46e5; transition: color 0.2s; }
        .activo-link:hover b { color: #312e81; text-decoration: underline; }
        .activo-link:hover .text-muted { color: #4f46e5; }

        /* Botones de acción y subida de archivos (UX Mejorada) */
        .btn-link { display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; color: #334155; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: 1px solid #cbd5e1; transition: all 0.2s; }
        .btn-link:hover { background: #e2e8f0; color: #0f172a; border-color: #94a3b8; }
        
        .upload-inline { display: flex; align-items: center; gap: 8px; }
        
        /* Ocultamos el feo input file nativo */
        input[type="file"] { display: none; }
        
        /* Estilizamos el label para que parezca un botón */
        .custom-file-label { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 12px; background: #f8fafc; border: 1px dashed #94a3b8; border-radius: 8px; font-size: 0.8rem; font-weight: 500; color: #64748b; cursor: pointer; transition: all 0.2s; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .custom-file-label:hover { background: #f1f5f9; border-color: #64748b; color: #334155; }
        .custom-file-label.has-file { background: #e0e7ff; border-color: #6366f1; color: #4338ca; border-style: solid; font-weight: 600; }
        
        .btn-upload { background: #10b981; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: all 0.2s; display: none; /* Se oculta hasta que haya archivo */ }
        .btn-upload.show { display: inline-block; animation: fadeIn 0.3s ease; }
        .btn-upload:hover { background: #059669; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(2px); } to { opacity: 1; transform: translateY(0); } }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
        .empty-state span { display: block; font-size: 3rem; margin-bottom: 15px; }

        /* --- ESTILOS DEL MODAL DE CARGA (LOADING) --- */
        .loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none; /* Oculto por defecto */
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .loading-overlay.active { display: flex; animation: fadeIn 0.3s ease; }
        
        .loading-card {
            background: #fff;
            padding: 40px 50px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 90%;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e2e8f0;
            border-top: 5px solid #6366f1; /* Color principal */
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px auto;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .loading-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0 0 8px 0; }
        .loading-text { font-size: 0.9rem; color: #64748b; margin: 0; }
    </style>

    <div class="loading-overlay" id="loadingModal">
        <div class="loading-card">
            <div class="spinner"></div>
            <h3 class="loading-title">Subiendo documento...</h3>
            <p class="loading-text">Por favor no cierres esta ventana.</p>
        </div>
    </div>

    <div class="wrapper">
        
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="header">
            <h1 class="title">Historial Técnico</h1>
            <a href="{{ route('inventario.mantenimientos.create') }}" class="btn-new">
                + Registrar Servicio
            </a>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="t-min">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Activo</th>
                            <th>Detalle</th>
                            <th>Costo</th>
                            <th>Técnico / Reg.</th>
                            <th>Soporte (Factura)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $mant)
                        <tr>
                            <td>
                                <strong>{{ $mant->created_at->format('d/m/Y') }}</strong>
                                <span class="text-muted">{{ $mant->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('inventario.activos.show', $mant->activo->id) }}" class="activo-link" title="Ver hoja de vida del activo">
                                    <b>{{ $mant->activo->nombre }}</b><br>
                                    <span class="text-muted">{{ $mant->activo->codigo_activo }}</span>
                                </a>
                            </td>
                            <td>
                                <span style="display: inline-block; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $mant->detalle }}">
                                    {{ $mant->detalle }}
                                </span>
                            </td>
                            <td>
                                <span class="cost-badge">${{ number_format($mant->costo_mantenimiento, 2) }}</span>
                            </td>
                            <td>
                                {{ $mant->creador->name ?? 'Sistema' }}
                            </td>
                            
                            <td>
                                @if($mant->acta)
                                    <a href="{{ $mant->getFile($mant->acta) }}" target="_blank" class="btn-link">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        Ver Documento
                                    </a>
                                @else
                                    <form action="{{ route('inventario.mantenimientos.upload', $mant->id) }}" method="POST" enctype="multipart/form-data" class="upload-inline" onsubmit="showLoadingModal()">
                                        @csrf
                                        
                                        <label for="file-{{ $mant->id }}" class="custom-file-label" id="label-{{ $mant->id }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                            Adjuntar
                                        </label>
                                        
                                        <input type="file" name="acta_archivo" id="file-{{ $mant->id }}" accept=".pdf,.jpg,.jpeg,.png" required onchange="handleFileSelect(this, '{{ $mant->id }}')">
                                        
                                        <button type="submit" class="btn-upload" id="btn-{{ $mant->id }}">
                                            Subir 🚀
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span>📭</span>
                                    <h3>No hay mantenimientos registrados</h3>
                                    <p>Los servicios técnicos que registres aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($mantenimientos->hasPages())
                <div style="padding: 20px; border-top: 1px solid #e2e8f0;">
                    {{ $mantenimientos->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function handleFileSelect(input, id) {
            const label = document.getElementById('label-' + id);
            const btn = document.getElementById('btn-' + id);
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                label.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> ${fileName}`;
                label.classList.add('has-file');
                btn.classList.add('show');
            } else {
                label.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> Adjuntar`;
                label.classList.remove('has-file');
                btn.classList.remove('show');
            }
        }

        // --- FUNCIÓN PARA ACTIVAR EL MODAL ---
        function showLoadingModal() {
            document.getElementById('loadingModal').classList.add('active');
            const btns = document.querySelectorAll('.btn-upload');
            btns.forEach(btn => btn.style.pointerEvents = 'none');
        }
    </script>
</x-base-layout>