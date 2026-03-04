<x-base-layout>
    <style>
        .wrapper { max-width: 1300px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 10px; }
        
        .table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; }
        .t-min th { background: #f8fafc; text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .t-min td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; vertical-align: middle; }
        .t-min tr:hover { background-color: #f8fafc; }
        
        /* Etiquetas de Estado (Badges) */
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; text-transform: uppercase; }
        .badge-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-success { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }
        
        /* Botones de Acción */
        .btn-group { display: flex; gap: 8px; align-items: center; }
        .btn-icon { padding: 8px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        
        .btn-download { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .btn-download:hover { background: #e2e8f0; color: #0f172a; }
        
        .btn-upload { background: #eef2ff; color: #4f46e5; border: 1px dashed #a5b4fc; }
        .btn-upload:hover { background: #e0e7ff; border-style: solid; }
        
        .btn-view { background: #10b981; color: #fff; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); }
        .btn-view:hover { background: #059669; }

        .btn-create { background: #0f172a; color: #fff; font-size: 0.9rem; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; transition: 0.2s; box-shadow: 0 4px 6px rgba(15, 23, 42, 0.2); }
        .btn-create:hover { background: #334155; transform: translateY(-2px); }
    </style>

    {{-- Sistema de Alertas para éxito o error al subir el PDF --}}
    @if(session('error'))
        <div style="max-width: 1300px; margin: 0 auto 20px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 16px; border-radius: 12px; font-weight: 600;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div style="max-width: 1300px; margin: 0 auto 20px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 16px; border-radius: 12px; font-weight: 600;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="wrapper">
        <div class="header">
            <h1 class="title"><i class="bi bi-journal-check"></i> Bitácora de Movimientos</h1>
            <a href="{{ route('inventario.movimientos.create') }}" class="btn-create">
                + Crear Nuevo Movimiento
            </a>
        </div>

        <div class="table-card">
            <table class="t-min">
                <thead>
                    <tr>
                        <th>Código Acta</th>
                        <th>Detalles Generales</th>
                        <th>Fecha y Autor</th>
                        <th>Auditoría (Firma)</th>
                        <th>Gestión de Documentos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    <tr>
                        {{-- 1. Código del Acta --}}
                        <td style="font-family: monospace; font-weight: 800; font-size: 1rem; color: #1e293b;">
                            {{ $mov->codigo_acta }}
                        </td>
                        
                        {{-- 2. Detalles (Tipo y Responsable) --}}
                        <td>
                            <span style="font-weight: 700; color: #334155; display: block;">
                                {{ $mov->tipoRegistro->nombre ?? 'N/A' }}
                            </span>
                            <span style="font-size: 0.8rem; color: #64748b;">
                                Responsable: <b>{{ $mov->responsable->name ?? 'N/A' }}</b>
                            </span>
                        </td>
                        
                        {{-- 3. Fechas y Autor --}}
                        <td>
                            <div style="color: #0f172a; font-weight: 600;">{{ $mov->created_at->format('d/m/Y H:i') }}</div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                                Por: {{ $mov->creador->name ?? 'Sistema' }}
                            </div>
                        </td>

                        {{-- 4. Indicador de Auditoría (Badge visual) --}}
                        <td>
                            @if($mov->acta_archivo)
                                <span class="badge badge-success" title="Documento legal resguardado en S3">
                                    <i class="bi bi-shield-check"></i> Firmada y Segura
                                </span>
                            @else
                                <span class="badge badge-pending" title="Falta subir el documento con las firmas físicas">
                                    <i class="bi bi-hourglass-split"></i> Pendiente de Firma
                                </span>
                            @endif
                        </td>
                        
                        {{-- 5. Botones de Acción Interactivos --}}
                        <td>
                            <div class="btn-group">
                                {{-- Botón: Descargar Original siempre disponible --}}
                                <a href="{{ route('inventario.movimientos.pdf', $mov->id) }}" class="btn-icon btn-download" title="Imprimir acta original del sistema">
                                    <i class="bi bi-printer"></i>
                                </a>

                                {{-- Lógica Condicional para el Archivo Firmado --}}
                                @if($mov->acta_archivo)
                                    {{-- Si ya hay archivo: Botón para Verlo en AWS S3 --}}
                                    <a href="{{$mov->getFile($mov->acta_archivo)}}" target="_blank" class="btn-icon btn-view" title="Ver documento digitalizado">
                                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF Firmado
                                    </a>
                                @else
                                    {{-- Si NO hay archivo: Botón Mágico para Subirlo --}}
                                    <form action="{{ route('inventario.movimientos.upload', $mov->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
                                        @csrf
                                        @method('POST')
                                        <label class="btn-icon btn-upload" title="Subir el acta escaneada">
                                            <i class="bi bi-cloud-arrow-up"></i> Subir Escáner
                                            <input type="file" name="acta_pdf" accept="application/pdf" style="display: none;" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; color: #94a3b8;">
                            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            Aún no se ha registrado ningún movimiento de inventario.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Paginación --}}
            @if($movimientos->hasPages())
                <div style="padding: 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>