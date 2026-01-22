<x-base-layout>
    <style>
        .wrapper { font-family: 'Inter', sans-serif; max-width: 1200px; margin: 0 auto; padding: 30px; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .btn-new { background: #0f172a; color: #fff; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
        .data-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
        .table-list { width: 100%; border-collapse: collapse; }
        .table-list th { text-align: left; padding: 18px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .table-list td { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .action-icon { color: #64748b; margin-left: 10px; font-size: 1.1rem; }
    </style>

    <div class="wrapper">
        <div class="header">
            <div>
                <h1 class="title">Historial de Facturas</h1>
                <p style="margin: 5px 0 0 0; color: #64748b;">Registro de adquisiciones y entradas al almacén.</p>
            </div>
            <a href="{{ route('inventario.compras.create') }}" class="btn-new"><i class="bi bi-plus-lg"></i> Registrar Compra</a>
        </div>

        <div class="data-card">
            <table class="table-list">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Fecha</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Registrado Por</th>
                        <th style="text-align: right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                    <tr>
                        <td style="font-weight: 700;">{{ $compra->numero_factura }}</td>
                        <td>{{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d/m/Y') }}</td>
                        <td>{{ $compra->metodo->nombre }}</td>
                        <td style="font-family: monospace;">${{ number_format($compra->total_pago, 2) }}</td>
                        <td style="font-size: 0.85rem; color: #64748b;">{{ $compra->usuarioRegistro->name }}</td>
                        <td style="text-align: right">
                            <a href="{{ route('inventario.compras.show', $compra->id) }}" class="action-icon" title="Ver Detalle"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('inventario.compras.descargar', $compra->id) }}" class="action-icon" title="Descargar PDF"><i class="bi bi-file-earmark-arrow-down"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; padding: 30px;">No hay compras registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding: 20px;">
                {{ $compras->links() }}
            </div>
        </div>
    </div>
</x-base-layout>