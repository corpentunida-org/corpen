<x-base-layout>
    <style>
        :root { --exec-text: #0f172a; --exec-muted: #64748b; --exec-border: #e2e8f0; --exec-bg: #f8fafc; }
        .dashboard-wrapper { font-family: 'Inter', sans-serif; color: var(--exec-text); max-width: 1200px; margin: 0 auto; padding: 30px; }
        
        .welcome-bar { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
        .system-tag { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #4f46e5; letter-spacing: 0.1em; display: block; margin-bottom: 5px; }
        .main-title { font-size: 2rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.03em; }
        .main-subtitle { font-size: 0.95rem; color: var(--exec-muted); margin-top: 5px; }
        
        .btn-alert { background: #dc2626; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-alert:hover { background: #b91c1c; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 40px; border-bottom: 1px solid var(--exec-border); padding-bottom: 30px; }
        .stat-item { display: flex; flex-direction: column; }
        .stat-label { font-size: 0.7rem; font-weight: 700; color: var(--exec-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-num { font-size: 2.2rem; font-weight: 800; line-height: 1.1; margin-top: 5px; }

        .card-stack { background: #fff; border: 1px solid var(--exec-border); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
        .card-header { padding: 20px 30px; border-bottom: 1px solid var(--exec-border); display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .icon-sq { width: 36px; height: 36px; background: #eef2ff; color: #4f46e5; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        
        .table-min { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .table-min th { text-align: left; padding: 15px 30px; color: var(--exec-muted); font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid var(--exec-border); font-weight: 600; }
        .table-min td { padding: 15px 30px; border-bottom: 1px solid var(--exec-border); vertical-align: middle; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: inline-block; }
        .bg-green { background: #dcfce7; color: #166534; }
        .bg-gray { background: #f1f5f9; color: #475569; }
    </style>

    <div class="dashboard-wrapper">
        <div class="welcome-bar">
            <div>
                <span class="system-tag">Inteligencia de Negocios</span>
                <h1 class="main-title">Tablero de Activos</h1>
                <p class="main-subtitle">Resumen ejecutivo del estado del inventario.</p>
            </div>
            <div>
                <a href="{{ route('inventario.activos.alertas') }}" class="btn-alert">
                    <i class="bi bi-bell-fill"></i> Ver Alertas
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-label">Total Equipos</span>
                <span class="stat-num">{{ $totalActivos ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Valor Inventario</span>
                <span class="stat-num">$0.00</span> </div>
            <div class="stat-item">
                <span class="stat-label">Asignados</span>
                <span class="stat-num">{{ isset($totalActivos) && $totalActivos > 0 ? round(($activosPorEstado->where('id_Estado', 2)->count() / $totalActivos) * 100) : 0 }}%</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">En Reparación</span>
                <span class="stat-num" style="color: #d97706;">{{ $activosPorEstado->where('id_Estado', 4)->count() ?? 0 }}</span>
            </div>
        </div>

        <div class="card-stack">
            <div class="card-header">
                <div class="card-title">
                    <div class="icon-sq"><i class="bi bi-clock-history"></i></div>
                    <span>Movimientos Recientes</span>
                </div>
                <a href="{{ route('inventario.movimientos.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #4f46e5; text-decoration: none;">Ver Historial &rarr;</a>
            </div>
            <div style="overflow-x: auto;">
                <table class="table-min">
                    <thead>
                        <tr>
                            <th>Acta</th>
                            <th>Tipo</th>
                            <th>Responsable</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimosMovimientos ?? [] as $mov)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700;">{{ $mov->codigo_acta }}</td>
                            <td><span class="badge {{ $mov->tipoRegistro->id == 1 ? 'bg-green' : 'bg-gray' }}">{{ $mov->tipoRegistro->nombre }}</span></td>
                            <td>{{ $mov->responsable->name }}</td>
                            <td>{{ $mov->created_at->format('d M, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding: 30px; color: #94a3b8;">Sin movimientos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-base-layout>