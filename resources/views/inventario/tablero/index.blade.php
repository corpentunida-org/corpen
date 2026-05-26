<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sheets-wrapper { font-family: 'Arial', 'Roboto', sans-serif; width: 100%; max-width: 1300px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; color: #202124; }
        
        .welcome-bar { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        .system-tag { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #1a73e8; letter-spacing: 0.05em; display: block; margin-bottom: 4px; }
        .main-title { font-size: 24px; font-weight: normal; margin: 0 0 4px 0; color: #202124; display: flex; align-items: center; gap: 8px; }
        .main-subtitle { font-size: 13px; color: #5f6368; margin: 0; }
        
        .header-actions { display: flex; gap: 10px; }
        .btn-sheets-alert, .btn-sheets-primary { color: #fff; padding: 8px 16px; border-radius: 4px; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; border: none; }
        .btn-sheets-alert { background-color: #d93025; }
        .btn-sheets-alert:hover { background-color: #b31404; }
        .btn-sheets-primary { background-color: #1a73e8; }
        .btn-sheets-primary:hover { background-color: #1b66c9; box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); }

        /* KPIs Estilo Botón */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: #fff; border: 1px solid #dadce0; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; transition: all 0.2s; text-decoration: none; color: inherit; cursor: pointer; }
        .stat-card:hover { box-shadow: 0 4px 8px rgba(60,64,67,.1); border-color: #1a73e8; transform: translateY(-2px); }
        
        /* Clase para resaltar la tarjeta filtrada */
        .stat-card.active-card { border-color: #1a73e8; background-color: #f8fbff; box-shadow: 0 2px 6px rgba(26, 115, 232, 0.15); }
        
        .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .stat-label { font-size: 11px; font-weight: bold; color: #5f6368; text-transform: uppercase; transition: color 0.2s; }
        .stat-card:hover .stat-label { color: #1a73e8; }
        
        .icon-box { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .bg-blue-pastel { background-color: #e8f0fe; color: #1967d2; }
        .bg-green-pastel { background-color: #ceead6; color: #0d652d; }
        .bg-purple-pastel { background-color: #f3e8fd; color: #681da8; }
        .bg-orange-pastel { background-color: #fef0db; color: #b06000; }
        .bg-cyan-pastel { background-color: #e4f7fb; color: #00838f; }
        
        .stat-num { font-size: 22px; color: #202124; margin: 0; line-height: 1; }
        .font-mono { font-family: 'Consolas', 'Courier New', monospace; font-weight: bold; }
        .stat-pct { font-size: 14px; color: #5f6368; font-family: 'Arial', sans-serif; font-weight: normal; margin-left: 4px; }

        .dashboard-columns { display: grid; grid-template-columns: 1fr 2fr; gap: 15px; align-items: start; margin-bottom: 20px; }
        
        .card-stack { background: #fff; border: 1px solid #dadce0; border-radius: 8px; overflow: hidden; }
        .card-header { padding: 12px 15px; border-bottom: 1px solid #dadce0; display: flex; justify-content: space-between; align-items: center; background-color: #fff; }
        .card-title { font-size: 14px; font-weight: bold; color: #202124; display: flex; align-items: center; gap: 8px; margin: 0; }
        .card-link { font-size: 12px; font-weight: bold; color: #1a73e8; text-decoration: none; }
        .card-link:hover { text-decoration: underline; }
        .chart-container { position: relative; height: 260px; width: 100%; padding: 15px; background: #fff; }

        /* Tabla Sheets */
        .table-responsive { width: 100%; overflow-x: auto; }
        .sheets-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        .sheets-table th { background-color: #e8f0fe; border-right: 1px solid #dadce0; border-bottom: 1px solid #dadce0; padding: 8px 12px; font-size: 11px; font-weight: bold; color: #1967d2; text-align: left; text-transform: uppercase; }
        .sheets-table td { border-right: 1px solid #dadce0; border-bottom: 1px solid #dadce0; padding: 8px 12px; font-size: 12px; color: #000; vertical-align: middle; }
        .sheets-table th:last-child, .sheets-table td:last-child { border-right: none; }
        .sheets-table tbody tr:hover { background-color: #f1f3f4; }
        
        .cell-success { background-color: #ceead6; color: #0d652d; border-radius: 2px; padding: 2px 6px; font-size: 11px; }
        .cell-default { background-color: #f1f3f4; color: #3c4043; border-radius: 2px; padding: 2px 6px; font-size: 11px; }
        
        .btn-close { color: #d93025; text-decoration: none; font-size: 12px; font-weight: bold; display: flex; align-items: center; gap: 4px; }
        .btn-close:hover { text-decoration: underline; }

        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 992px) { .dashboard-columns { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="sheets-wrapper">
        <div class="welcome-bar">
            <div>
                <span class="system-tag">Inteligencia de Negocios</span>
                <h1 class="main-title"><i class="bi bi-pie-chart" style="color: #1a73e8;"></i> Tablero de Activos</h1>
                <p class="main-subtitle">Resumen ejecutivo del estado del inventario. Selecciona una tarjeta para ver detalles.</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('inventario.movimientos.index') }}" class="btn-sheets-primary"><i class="bi bi-plus-lg"></i> Nuevo Movimiento</a>
                <a href="{{ route('inventario.activos.alertas') }}" class="btn-sheets-alert"><i class="bi bi-bell-fill"></i> Alertas</a>
            </div>
        </div>

        {{-- Lógica de porcentajes faltantes --}}
        @php
            $pctNoAsignados = $totalActivos > 0 ? round(($noAsignados / $totalActivos) * 100) : 0;
            $pctReparacion  = $totalActivos > 0 ? round(($enReparacion / $totalActivos) * 100) : 0;
        @endphp

        {{-- TARJETAS COMO ENLACES CON FILTROS --}}
        <div class="stats-grid">
            <a href="?filtro=todos" class="stat-card {{ request('filtro') == 'todos' || !request()->has('filtro') ? 'active-card' : '' }}" title="Ver todos los equipos">
                <div class="stat-header">
                    <span class="stat-label">Total Equipos</span>
                    <div class="icon-box bg-blue-pastel"><i class="bi bi-pc-display"></i></div>
                </div>
                <p class="stat-num font-mono">{{ $totalActivos }}</p>
            </a>
            
            <div class="stat-card" style="cursor: default;">
                <div class="stat-header">
                    <span class="stat-label">Valor Inventario</span>
                    <div class="icon-box bg-green-pastel"><i class="bi bi-cash-stack"></i></div>
                </div>
                <p class="stat-num font-mono" style="color: #137333;">${{ number_format($valorInventario, 2) }}</p>
            </div>
            
            <a href="?filtro=asignados" class="stat-card {{ request('filtro') == 'asignados' ? 'active-card' : '' }}" title="Ver lista de equipos asignados">
                <div class="stat-header">
                    <span class="stat-label">Asignados</span>
                    <div class="icon-box bg-purple-pastel"><i class="bi bi-person-check"></i></div>
                </div>
                <p class="stat-num font-mono">{{ $asignados }} <span class="stat-pct">({{ $porcentajeAsignados }}%)</span></p>
            </a>

            <a href="?filtro=no_asignados" class="stat-card {{ request('filtro') == 'no_asignados' ? 'active-card' : '' }}" title="Ver lista de equipos no asignados / en bodega">
                <div class="stat-header">
                    <span class="stat-label">No Asignados</span>
                    <div class="icon-box bg-cyan-pastel"><i class="bi bi-box-seam"></i></div>
                </div>
                <p class="stat-num font-mono" style="color: #00838f;">{{ $noAsignados }} <span class="stat-pct">({{ $pctNoAsignados }}%)</span></p>
            </a>
            
            <a href="?filtro=reparacion" class="stat-card {{ request('filtro') == 'reparacion' ? 'active-card' : '' }}" title="Ver lista de equipos en reparación">
                <div class="stat-header">
                    <span class="stat-label">En Reparación</span>
                    <div class="icon-box bg-orange-pastel"><i class="bi bi-tools"></i></div>
                </div>
                <p class="stat-num font-mono" style="color: #b06000;">{{ $enReparacion }} <span class="stat-pct">({{ $pctReparacion }}%)</span></p>
            </a>
        </div>

        {{-- SECCIÓN DINÁMICA: TABLA DE DETALLE --}}
        @if($listaDetalle)
        <div class="card-stack" id="seccion-detalle" style="border-color: #1a73e8; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(26, 115, 232, 0.15);">
            <div class="card-header" style="background-color: #e8f0fe;">
                <h3 class="card-title" style="color: #1967d2;">
                    <i class="bi bi-list-columns-reverse"></i> {{ $tituloDetalle }}
                </h3>
                <a href="{{ url()->current() }}" class="btn-close">
                    <i class="bi bi-x-circle-fill"></i> Cerrar
                </a>
            </div>
            <div class="table-responsive">
                <table class="sheets-table" style="border: none;">
                    <thead>
                        <tr>
                            <th style="border-left: none;">CÓDIGO ACTIVO</th>
                            <th>NOMBRE DEL EQUIPO</th>
                            <th>ESTADO ACTUAL</th>
                            <th>FECHA REGISTRO</th>
                            <th style="text-align: right; border-right: none;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listaDetalle as $activo)
                        <tr>
                            <td class="font-mono" style="border-left: none; color: #1a73e8;">{{ $activo->codigo_activo }}</td>
                            <td>{{ $activo->nombre }}</td>
                            <td><span class="cell-default">{{ $activo->estado->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $activo->created_at ? $activo->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td style="text-align: right; border-right: none;">
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" style="color: #5f6368; text-decoration: none;" title="Ver Hoja de Vida">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #5f6368; border-left: none; border-right: none;">
                                No se encontraron activos en este estado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($listaDetalle->hasPages())
                <div style="padding: 10px 15px; font-size: 13px;">
                    {{ $listaDetalle->links() }}
                </div>
            @endif
        </div>
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const seccionDetalle = document.getElementById('seccion-detalle');
                if (seccionDetalle) {
                    seccionDetalle.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        </script>
        @endif
        {{-- FIN SECCIÓN DINÁMICA --}}

        <div class="dashboard-columns">
            <div class="card-stack">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-diagram-3" style="color: #5f6368;"></i> Distribución de Estados</h3>
                </div>
                <div class="chart-container">
                    <canvas id="activosChart"></canvas>
                </div>
            </div>

            <div class="card-stack">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-clock-history" style="color: #5f6368;"></i> Movimientos Recientes</h3>
                    <a href="{{ route('inventario.movimientos.index') }}" class="card-link">Ver Historial &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="sheets-table" style="border: none;">
                        <thead>
                            <tr>
                                <th style="border-left: none;">ACTA</th>
                                <th>TIPO REGISTRO</th>
                                <th>RESPONSABLE</th>
                                <th style="border-right: none;">FECHA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosMovimientos as $mov)
                            <tr>
                                <td class="font-mono" style="color: #1a73e8; border-left: none;">{{ $mov->codigo_acta ?? 'N/A' }}</td>
                                <td>
                                    @if($mov->tipoRegistro)
                                        <span class="{{ $mov->tipoRegistro->id == 1 ? 'cell-success' : 'cell-default' }}">{{ $mov->tipoRegistro->nombre }}</span>
                                    @else
                                        <span class="cell-default">Desconocido</span>
                                    @endif
                                </td>
                                <td>{{ $mov->responsable->name ?? 'Sistema' }}</td>
                                <td style="border-right: none;">{{ $mov->created_at ? $mov->created_at->format('d/m/Y') : 'Sin fecha' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center; padding: 40px; color: #5f6368; border-left: none; border-right: none;">No hay movimientos recientes registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('activosChart').getContext('2d');
            const etiquetas = {!! json_encode($labelsGrafica ?? []) !!};
            const valores = {!! json_encode($dataGrafica ?? []) !!};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        data: valores,
                        backgroundColor: ['rgba(26, 115, 232, 0.7)', 'rgba(52, 168, 83, 0.7)', 'rgba(242, 153, 0, 0.7)', 'rgba(217, 48, 37, 0.7)', 'rgba(161, 66, 244, 0.7)', 'rgba(36, 193, 224, 0.7)', 'rgba(154, 160, 166, 0.7)'],
                        borderColor: ['rgba(26, 115, 232, 1)', 'rgba(52, 168, 83, 1)', 'rgba(242, 153, 0, 1)', 'rgba(217, 48, 37, 1)', 'rgba(161, 66, 244, 1)', 'rgba(36, 193, 224, 1)', 'rgba(154, 160, 166, 1)'],
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '65%',
                    plugins: { legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: { family: 'Arial', size: 11 }, color: '#5f6368', boxWidth: 8 } }, tooltip: { backgroundColor: 'rgba(32, 33, 36, 0.9)', padding: 10, bodyFont: { family: 'Arial', size: 12 } } }
                }
            });
        });
    </script>
</x-base-layout>