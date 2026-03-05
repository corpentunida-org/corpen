<x-base-layout>
    <style>
        .wrap { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', system-ui, sans-serif; color: #0f172a; }
        
        /* Header & Titles */
        .title-section { margin-bottom: 30px; }
        .title-section h1 { font-size: 1.8rem; font-weight: 800; margin: 0; }
        .title-section p { color: #64748b; margin-top: 5px; }

        /* Banner de Resumen */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .icon-red { background: #fef2f2; color: #dc2626; }
        .icon-orange { background: #fff7ed; color: #ea580c; }
        .stat-data b { display: block; font-size: 1.2rem; }
        .stat-data span { font-size: 0.85rem; color: #64748b; }

        /* Tablas de Alertas */
        .card-alert { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 30px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .card-header { padding: 15px 24px; display: flex; align-items: center; gap: 10px; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        .bg-vencido { background: #fef2f2; color: #991b1b; }
        .bg-por-vencer { background: #fff7ed; color: #9a3412; }

        .table-alerts { width: 100%; border-collapse: collapse; }
        .table-alerts th { background: #f8fafc; text-align: left; padding: 12px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; }
        .table-alerts td { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .activo-link { text-decoration: none; color: #0f172a; font-weight: 600; }
        .activo-link:hover { color: #4f46e5; text-decoration: underline; }

        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-warning { background: #ffedd5; color: #ea580c; }

        /* Empty State */
        .empty-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 50px; text-align: center; }
        .empty-icon { font-size: 3rem; color: #10b981; margin-bottom: 15px; }
    </style>

    <div class="wrap">
        <div class="title-section">
            <h1>Alertas de Garantía</h1>
            <p>Control de vigencia técnica para los activos de la organización.</p>
        </div>

        @if($vencidos->isEmpty() && $porVencer->isEmpty())
            <div class="empty-card">
                <div class="empty-icon">✅</div>
                <h3>¡Todo bajo control!</h3>
                <p>No se encontraron equipos con garantías vencidas o próximas a vencer.</p>
                <a href="{{ route('inventario.activos.index') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600; margin-top: 10px; display: inline-block;">Volver al inventario general</a>
            </div>
        @else
            <div class="summary-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-red">🚩</div>
                    <div class="stat-data">
                        <b>{{ $vencidos->count() }}</b>
                        <span>Garantías Vencidas</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange">⏳</div>
                    <div class="stat-data">
                        <b>{{ $porVencer->count() }}</b>
                        <span>Por vencer (< 30 días)</span>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN: YA VENCIDAS --}}
            @if($vencidos->isNotEmpty())
            <div class="card-alert">
                <div class="card-header bg-vencido">
                    <span>🚫</span> Garantías Vencidas
                </div>
                <table class="table-alerts">
                    <thead>
                        <tr>
                            <th>Activo</th>
                            <th>Código</th>
                            <th>Venció el</th>
                            <th>Estado Actual</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vencidos as $activo)
                        <tr>
                            <td>
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" class="activo-link">
                                    {{ $activo->nombre }}
                                </a>
                            </td>
                            <td><code>{{ $activo->codigo_activo }}</code></td>
                            <td><span class="badge badge-danger">{{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->format('d/m/Y') }}</span></td>
                            <td>{{ $activo->estado->nombre ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" style="color: #64748b; font-size: 1.1rem;" title="Ver expediente">📂</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- SECCIÓN: POR VENCER --}}
            @if($porVencer->isNotEmpty())
            <div class="card-alert">
                <div class="card-header bg-por-vencer">
                    <span>⚠️</span> Próximas a Vencer
                </div>
                <table class="table-alerts">
                    <thead>
                        <tr>
                            <th>Activo</th>
                            <th>Código</th>
                            <th>Vence el</th>
                            <th>Días Restantes</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($porVencer as $activo)
                        @php
                            $dias = now()->diffInDays($activo->fecha_fin_garantia, false);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" class="activo-link">
                                    {{ $activo->nombre }}
                                </a>
                            </td>
                            <td><code>{{ $activo->codigo_activo }}</code></td>
                            <td><span class="badge badge-warning">{{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->format('d/m/Y') }}</span></td>
                            <td><strong>{{ $dias }} días</strong></td>
                            <td>
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" style="color: #64748b; font-size: 1.1rem;" title="Ver expediente">📂</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @endif
    </div>
</x-base-layout>