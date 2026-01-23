<x-base-layout>
    <style>
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .btn-new { background: #0f172a; color: #fff; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; }
        
        .table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; }
        .t-min th { background: #f8fafc; text-align: left; padding: 18px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; }
        .t-min td { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; vertical-align: middle; }
    </style>

    <div class="wrapper">
        <div class="header">
            <h1 class="title">Historial Técnico</h1>
            <a href="{{ route('inventario.mantenimientos.create') }}" class="btn-new">Registrar Servicio</a>
        </div>

        <div class="table-card">
            <table class="t-min">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Activo</th>
                        <th>Detalle</th>
                        <th>Costo</th>
                        <th>Técnico / Reg.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mantenimientos as $mant)
                    <tr>
                        <td>{{ $mant->created_at->format('d/m/Y') }}</td>
                        <td>
                            <b>{{ $mant->activo->nombre }}</b><br>
                            <span style="font-size:0.8rem; color:#64748b;">{{ $mant->activo->codigo_activo }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($mant->detalle, 50) }}</td>
                        <td style="font-family: monospace; font-weight: 700;">${{ number_format($mant->costo_mantenimiento, 2) }}</td>
                        <td>{{ $mant->creador->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $mantenimientos->links() }}</div>
        </div>
    </div>
</x-base-layout>