<x-base-layout>
    <style>
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; }
        .t-min th { background: #f8fafc; text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; }
        .t-min td { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .btn-acta { padding: 6px 12px; background: #eef2ff; color: #4f46e5; border-radius: 6px; font-size: 0.8rem; font-weight: 700; text-decoration: none; }
        .btn-acta:hover { background: #e0e7ff; }
    </style>

    <div class="wrapper">
        <div class="header">
            <h1 class="title">Bitácora de Movimientos</h1>
            <a href="{{ route('inventario.movimientos.create') }}" class="btn-acta" style="background:#000; color:#fff; font-size:0.9rem; padding:12px 20px;">+ Crear Nuevo Movimiento</a>
        </div>

        <div class="table-card">
            <table class="t-min">
                <thead>
                    <tr>
                        <th>Código Acta</th>
                        <th>Tipo</th>
                        <th>Responsable / Funcionario</th>
                        <th>Fecha</th>
                        <th>Registrado Por</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $mov)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700;">{{ $mov->codigo_acta }}</td>
                        <td>{{ $mov->tipoRegistro->nombre }}</td>
                        <td>{{ $mov->responsable->name }}</td>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td style="color: #64748b;">{{ $mov->creador->name ?? 'Sistema' }}</td>
                        <td>
                            <a href="{{ route('inventario.movimientos.pdf', $mov->id) }}" class="btn-acta">
                                <i class="bi bi-file-earmark-pdf"></i> Acta
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $movimientos->links() }}</div>
        </div>
    </div>
</x-base-layout>