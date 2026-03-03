<x-base-layout>
    <style>
        .wrapper { font-family: 'Inter', sans-serif; max-width: 1200px; margin: 0 auto; padding: 30px; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .btn-new { background: #0f172a; color: #fff; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-new:hover { background: #1e293b; }
        
        .data-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .table-list { width: 100%; border-collapse: collapse; }
        .table-list th { text-align: left; padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; }
        .table-list td { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; vertical-align: middle; }
        .table-list tr:hover { background-color: #f8fafc; }
        
        .action-icon { color: #64748b; margin-left: 10px; font-size: 1.2rem; transition: 0.2s; display: inline-block; text-decoration: none; }
        .action-icon:hover { color: #0f172a; transform: scale(1.1); }
        .icon-aws { color: #ea580c; } /* Color naranja para destacar que es un adjunto */
        .icon-aws:hover { color: #c2410c; }
        .icon-edit { color: #2563eb; } /* Azul para editar */
        .icon-edit:hover { color: #1d4ed8; }
        
        .badge-metodo { background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .text-muted { font-size: 0.8rem; color: #64748b; margin-top: 4px; }
        
        /* Alertas */
        .alert-success { background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; }
    </style>

    <div class="wrapper">
        {{-- Mensaje de éxito al guardar --}}
        @if (session('success'))
            <div class="alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="header">
            <div>
                <h1 class="title">Historial de Compras</h1>
                <p style="margin: 5px 0 0 0; color: #64748b;">Registro de adquisiciones, proveedores y entradas al almacén.</p>
            </div>
            <a href="{{ route('inventario.compras.create') }}" class="btn-new"><i class="bi bi-plus-lg"></i> Registrar Compra</a>
        </div>

        <div class="data-card">
            <table class="table-list">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Método Pago</th>
                        <th>Egreso</th> <th>Total</th>
                        <th>Registrado Por</th>
                        <th style="text-align: right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                    <tr>
                        <td>
                            <div style="font-weight: 800; color: #0f172a;">{{ $compra->numero_factura }}</div>
                            @if($compra->num_doc_interno)
                                <div class="text-muted">Doc: {{ $compra->num_doc_interno }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #334155;">
                                {{ $compra->proveedor->nom_ter ?? 'Proveedor No Encontrado' }}
                            </div>
                            <div class="text-muted">NIT: {{ $compra->cod_ter_proveedor }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge-metodo">{{ $compra->metodo->nombre ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{-- Ajusta 'numero_egreso' al nombre real de tu columna en InvCompra --}}
                            <div style="font-weight: 600; color: #0f172a;">{{ $compra->numero_egreso ?? 'N/A' }}</div>
                        </td>
                        <td style="font-family: monospace; font-size: 1rem; font-weight: 600;">
                            ${{ number_format($compra->total_pago, 2) }}
                        </td>
                        <td class="text-muted">{{ $compra->usuarioRegistro->name ?? 'Sistema' }}</td>
                        <td style="text-align: right">
                            {{-- Ver Detalle --}}
                            <a href="{{ route('inventario.compras.show', $compra->id) }}" class="action-icon" title="Ver Detalle">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Editar --}}
                            <a href="{{ route('inventario.compras.edit', $compra->id) }}" class="action-icon icon-edit" title="Editar Compra">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            {{-- Ver Archivo Adjunto en AWS S3 (Solo si existe) --}}
                            @if($compra->eg_archivo)
                                <a href="{{ route('inventario.compras.archivo', $compra->id) }}" target="_blank" class="action-icon icon-aws" title="Ver Soporte Adjunto (AWS)">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @endif

                            {{-- Descargar PDF del sistema --}}
                            <a href="{{ route('inventario.compras.descargar', $compra->id) }}" class="action-icon" title="Descargar PDF">
                                <i class="bi bi-file-earmark-arrow-down"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            No hay compras registradas en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Paginación --}}
            <div style="padding: 20px; border-top: 1px solid #e2e8f0;">
                {{ $compras->links() }}
            </div>
        </div>
    </div>
</x-base-layout>