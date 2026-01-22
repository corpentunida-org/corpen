<x-base-layout>
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .search-input { padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 25px; width: 300px; }
        
        .t-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; }
        .t-min th { background: #f8fafc; text-align: left; padding: 18px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .t-min td { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; font-size: 0.9rem; }
        
        .pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .st-ok { background: #dcfce7; color: #166534; }
        .st-busy { background: #f1f5f9; color: #475569; }
        .st-bad { background: #fef2f2; color: #b91c1c; }
        .st-fix { background: #fffbeb; color: #b45309; }

        .btn-view { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; color: #0f172a; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: 0.2s; }
        .btn-view:hover { background: #0f172a; color: #fff; }
    </style>

    <div class="container">
        <div class="top-bar">
            <div>
                <span style="font-size: 0.7rem; font-weight: 800; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.1em;">Inventario Maestro</span>
                <h1 class="page-title">Almacén de Activos</h1>
            </div>
            <form action="" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Buscar placa, serial o nombre..." value="{{ request('search') }}">
            </form>
        </div>

        <div class="t-card">
            <table class="t-min">
                <thead>
                    <tr>
                        <th>Activo</th>
                        <th>Identificación</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Asignado A</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activos as $activo)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $activo->nombre }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">{{ $activo->marca->nombre }}</div>
                        </td>
                        <td>
                            <div style="font-weight:700;">{{ $activo->codigo_activo }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">S/N: {{ $activo->serial }}</div>
                        </td>
                        <td>{{ $activo->bodega->nombre }}</td>
                        <td>
                            @php
                                $class = match($activo->id_Estado) {
                                    1 => 'st-ok', 2 => 'st-busy', 3 => 'st-bad', 4 => 'st-fix', default => 'st-busy'
                                };
                            @endphp
                            <span class="pill {{ $class }}">{{ $activo->estado->nombre }}</span>
                        </td>
                        <td style="font-size: 0.85rem;">
                            {{ $activo->usuarioAsignado ? $activo->usuarioAsignado->name : '-- En Bodega --' }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('inventario.activos.show', $activo->id) }}" class="btn-view">Hoja de Vida</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 20px;">{{ $activos->links() }}</div>
        </div>
    </div>
</x-base-layout>