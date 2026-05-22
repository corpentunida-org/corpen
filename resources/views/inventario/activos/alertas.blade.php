<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Contenedor principal estilo Sheets */
        .sheets-wrapper { font-family: 'Arial', 'Roboto', sans-serif; width: 100%; padding: 10px 15px; background-color: #fff; color: #000; }

        /* Cabecera */
        .sheets-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; }
        .sheets-header h1 { font-size: 20px; font-weight: normal; margin: 0 0 4px 0; color: #202124; display: flex; align-items: center; gap: 8px; }
        .sheets-subtitle { margin: 0; font-size: 13px; color: #5f6368; }

        /* Dashboard Widgets */
        .dashboard-grid { display: grid; grid-template-columns: 250px 1fr; gap: 15px; margin-bottom: 15px; }
        .summary-column { display: flex; flex-direction: column; gap: 15px; }
        .summary-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; transition: box-shadow 0.2s ease; }
        .summary-card:hover { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .summary-card h3 { margin: 0 0 5px 0; font-size: 12px; color: #5f6368; text-transform: uppercase; }
        .summary-card .value { margin: 0; font-size: 24px; font-weight: normal; }
        
        .val-danger { color: #d93025; } /* Rojo Google */
        .val-warning { color: #f29900; } /* Amarillo/Naranja Google */

        .chart-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; position: relative; height: 200px; display: flex; align-items: center; justify-content: center; }

        /* Panel de Filtros */
        .filter-dashboard { background-color: #f8f9fa; border: 1px solid #ccc; border-bottom: none; padding: 10px 15px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 11px; font-weight: bold; color: #5f6368; text-transform: uppercase; }
        .filter-input { height: 28px; padding: 0 8px; border: 1px solid #ccc; border-radius: 2px; font-size: 13px; color: #202124; background-color: #fff; min-width: 150px; }
        .filter-input:focus { outline: none; border-color: #1a73e8; box-shadow: inset 0 0 0 1px #1a73e8; }
        .btn-filter { background-color: #f1f3f4; color: #3c4043; border: 1px solid #ccc; height: 28px; padding: 0 15px; border-radius: 2px; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.2s ease; }
        .btn-filter:hover { background-color: #e8eaed; }

        /* Tabla estricta estilo Grid */
        .sheets-table-container { width: 100%; overflow-x: auto; border: 1px solid #ccc; }
        .sheets-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        .sheets-table th { background-color: #e8f0fe; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 8px; font-size: 12px; font-weight: bold; color: #1967d2; text-align: left; }
        .sheets-table td { border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 6px 8px; font-size: 13px; color: #000; vertical-align: middle; }
        .sheets-table th:last-child, .sheets-table td:last-child { border-right: none; }
        .sheets-table tbody tr:hover { background-color: #f1f3f4; }

        /* Estilos Internos de Tabla */
        .font-mono { font-family: 'Consolas', 'Courier New', monospace; font-size: 12px; }
        .activo-link { color: #1a73e8; text-decoration: none; font-weight: bold; }
        .activo-link:hover { text-decoration: underline; }
        
        /* Celdas Pastel de Alerta */
        .cell-danger { background-color: #fce8e6; color: #c5221f; border-radius: 2px; padding: 2px 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }
        .cell-warning { background-color: #fef0db; color: #b06000; border-radius: 2px; padding: 2px 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }

        /* Acciones */
        .action-icon { color: #5f6368; font-size: 15px; text-decoration: none; cursor: pointer; transition: color 0.2s; }
        .action-icon:hover { color: #1a73e8; }

        /* Empty State */
        .empty-state { text-align: center; padding: 40px 20px; color: #5f6368; }
        .empty-state i { font-size: 30px; color: #34a853; display: block; margin-bottom: 10px; }
    </style>

    <div class="sheets-wrapper">
        
        <div class="sheets-header">
            <div>
                <h1><i class="bi bi-shield-exclamation" style="color: #d93025;"></i> Alertas de Garantía</h1>
                <p class="sheets-subtitle">Control de vigencia técnica para los equipos de la organización.</p>
            </div>
            <a href="{{ route('inventario.activos.index') }}" class="btn-filter">
                <i class="bi bi-arrow-left"></i> Volver a Activos
            </a>
        </div>

        {{-- Mostrar Dashboard solo si hay alertas en total --}}
        @if($totalVencidos > 0 || $totalPorVencer > 0 || request('search'))
            
            <div class="dashboard-grid">
                <div class="summary-column">
                    <div class="summary-card">
                        <h3>Garantías Vencidas</h3>
                        <p class="value val-danger">{{ $totalVencidos }}</p>
                    </div>
                    <div class="summary-card">
                        <h3>Por Vencer (< 30 días)</h3>
                        <p class="value val-warning">{{ $totalPorVencer }}</p>
                    </div>
                </div>
                <div class="chart-card">
                    <canvas id="alertasChart"></canvas>
                </div>
            </div>

            {{-- Formulario Dashboard / Filtros --}}
            <form method="GET" action="{{ route('inventario.activos.alertas') }}" class="filter-dashboard">
                <div class="filter-group">
                    <label>Buscar Equipo / Código</label>
                    <input type="text" name="search" class="filter-input" placeholder="Ej. PC-001..." value="{{ request('search') }}">
                </div>

                <div class="filter-group">
                    <label>Estado de Garantía</label>
                    <select name="estado_garantia" class="filter-input">
                        <option value="">Todas las alertas</option>
                        <option value="vencido" {{ request('estado_garantia') == 'vencido' ? 'selected' : '' }}>Solo Vencidas</option>
                        <option value="por_vencer" {{ request('estado_garantia') == 'por_vencer' ? 'selected' : '' }}>Próximas a vencer</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px; margin-left: auto;">
                    <a href="{{ route('inventario.activos.alertas') }}" class="btn-filter" title="Limpiar Filtros">
                        <i class="bi bi-eraser"></i>
                    </a>
                    <button type="submit" class="btn-filter" style="background-color: #1a73e8; color: white; border: none;">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="sheets-table-container">
                <table class="sheets-table">
                    <thead>
                        <tr>
                            <th style="width: 250px;">ACTIVO</th>
                            <th style="width: 120px;">CÓDIGO</th>
                            <th style="width: 150px;">FECHA FIN GARANTÍA</th>
                            <th style="width: 150px;">ESTADO ALERTA</th>
                            <th style="width: 150px;">TIEMPO</th>
                            <th style="width: 150px;">ESTADO FÍSICO</th>
                            <th style="width: 80px;" style="text-align: center;">EXPEDIENTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alertas as $activo)
                            @php
                                $fechaGarantia = \Carbon\Carbon::parse($activo->fecha_fin_garantia);
                                $isVencido = $fechaGarantia->isPast();
                                $dias = now()->diffInDays($fechaGarantia, false);
                                $diasAbs = abs((int)$dias);
                            @endphp
                        <tr>
                            <td>
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" class="activo-link" title="Ver hoja de vida">
                                    {{ $activo->nombre }}
                                </a>
                            </td>
                            <td><span class="font-mono">{{ $activo->codigo_activo }}</span></td>
                            <td>{{ $fechaGarantia->format('d/m/Y') }}</td>
                            
                            {{-- Lógica de Color para la celda de estado --}}
                            <td>
                                @if($isVencido)
                                    <span class="cell-danger"><i class="bi bi-x-circle"></i> Vencida</span>
                                @else
                                    <span class="cell-warning"><i class="bi bi-exclamation-triangle"></i> Por Vencer</span>
                                @endif
                            </td>

                            {{-- Días restantes o pasados --}}
                            <td class="font-mono">
                                @if($isVencido)
                                    Hace {{ $diasAbs }} días
                                @else
                                    Faltan {{ $diasAbs }} días
                                @endif
                            </td>

                            <td>{{ $activo->estado->nombre ?? 'N/A' }}</td>
                            
                            <td style="text-align: center;">
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" class="action-icon" title="Abrir expediente">
                                    <i class="bi bi-folder2-open"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state" style="padding: 20px;">
                                    <p style="margin:0;">No se encontraron resultados para los filtros aplicados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($alertas->hasPages())
                <div style="margin-top: 10px; font-size: 13px;">
                    {{ $alertas->appends(request()->query())->links() }}
                </div>
            @endif

        @else
            {{-- ESTADO VACÍO GLOBAL (Cuando realmente no hay ninguna alerta en el sistema) --}}
            <div class="empty-state">
                <i class="bi bi-check-circle-fill"></i>
                <h3 style="color: #202124; font-size: 18px; margin-bottom: 5px;">¡Todo bajo control!</h3>
                <p>No se encontraron equipos con garantías vencidas o próximas a vencer.</p>
            </div>
        @endif
    </div>

    {{-- Script del Gráfico de Dona Chart.js --}}
    @if($totalVencidos > 0 || $totalPorVencer > 0)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('alertasChart').getContext('2d');
            
            const dataVencidos = {{ $totalVencidos }};
            const dataPorVencer = {{ $totalPorVencer }};

            new Chart(ctx, {
                type: 'doughnut', // Gráfico de dona (ideal para proporciones)
                data: {
                    labels: ['Vencidas', 'Por Vencer'],
                    datasets: [{
                        data: [dataVencidos, dataPorVencer],
                        backgroundColor: [
                            'rgba(217, 48, 37, 0.7)', // Rojo pastel Sheets
                            'rgba(242, 153, 0, 0.7)'  // Amarillo pastel Sheets
                        ],
                        borderColor: [
                            'rgba(217, 48, 37, 1)',
                            'rgba(242, 153, 0, 1)'
                        ],
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%', // Grosor de la dona
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { family: 'Arial', size: 11 },
                                color: '#5f6368',
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(32, 33, 36, 0.9)',
                            padding: 10,
                            bodyFont: { family: 'Arial', size: 12 }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</x-base-layout>