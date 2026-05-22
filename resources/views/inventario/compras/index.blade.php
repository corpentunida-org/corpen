<x-base-layout>
    {{-- Importamos Chart.js para los gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Contenedor principal */
        .sheets-wrapper {
            font-family: 'Arial', 'Roboto', sans-serif;
            width: 100%;
            padding: 10px 15px;
            background-color: #fff;
            color: #000;
        }

        /* Cabecera principal */
        .sheets-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 15px;
        }
        .sheets-header h1 {
            font-size: 20px;
            font-weight: normal;
            margin: 0 0 4px 0;
            color: #202124;
        }
        .btn-sheets-primary {
            background-color: #1a73e8;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
        }
        .btn-sheets-primary:hover {
            background-color: #1b66c9;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }

        /* ----- NUEVO: DASHBOARD WIDGETS ----- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .summary-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .summary-card {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 15px;
            background-color: #fff;
        }
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #5f6368;
            text-transform: uppercase;
        }
        .summary-card .value {
            margin: 0;
            font-size: 24px;
            color: #1a73e8; /* Azul Sheets */
            font-weight: normal;
        }
        .chart-card {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 15px;
            background-color: #fff;
            position: relative;
            height: 200px; /* Altura fija para el gráfico */
        }

        /* Dashboard / Panel de Filtros */
        .filter-dashboard {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-bottom: none;
            padding: 10px 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .filter-group label {
            font-size: 11px;
            font-weight: bold;
            color: #5f6368;
            text-transform: uppercase;
        }
        .filter-input {
            height: 28px;
            padding: 0 8px;
            border: 1px solid #ccc;
            border-radius: 2px;
            font-size: 13px;
            color: #202124;
            background-color: #fff;
            min-width: 150px;
        }
        .filter-input:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: inset 0 0 0 1px #1a73e8;
        }
        .btn-filter {
            background-color: #f1f3f4;
            color: #3c4043;
            border: 1px solid #ccc;
            height: 28px;
            padding: 0 15px;
            border-radius: 2px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-filter:hover { background-color: #e8eaed; }

        /* Tabla estricta estilo Grid */
        .sheets-table-container { width: 100%; overflow-x: auto; border: 1px solid #ccc; }
        .sheets-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        .sheets-table th {
            background-color: #e8f0fe;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            color: #1967d2;
            text-align: left;
        }
        .sheets-table td {
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 13px;
            color: #000;
            vertical-align: middle;
        }
        .sheets-table th:last-child, .sheets-table td:last-child { border-right: none; }
        .sheets-table tbody tr:hover { background-color: #f1f3f4; }

        /* Celdas con colores pasteles para Método de Pago */
        .cell-efectivo { background-color: #ceead6; color: #0d652d; border-radius: 2px; padding: 2px 6px; font-size: 12px; }
        .cell-tarjeta { background-color: #d2e3fc; color: #174ea6; border-radius: 2px; padding: 2px 6px; font-size: 12px; }
        .cell-cheque { background-color: #fef0db; color: #b06000; border-radius: 2px; padding: 2px 6px; font-size: 12px; }
        .cell-default { background-color: #f1f3f4; color: #3c4043; border-radius: 2px; padding: 2px 6px; font-size: 12px; }

        /* Utilidades */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-muted { color: #5f6368; font-size: 11px; }
        .font-mono { font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; }
        .sheets-actions { display: flex; justify-content: flex-end; gap: 10px; }
        .action-icon { color: #5f6368; font-size: 14px; text-decoration: none; }
        .action-icon:hover { color: #1a73e8; }
    </style>

    <div class="sheets-wrapper">
        
        <div class="sheets-header">
            <div>
                <h1>Historial de Compras</h1>
            </div>
            <a href="{{ route('inventario.compras.create') }}" class="btn-sheets-primary">
                <i class="bi bi-plus-lg"></i> Registrar Compra
            </a>
        </div>

        {{-- NUEVO: Dashboard Widgets --}}
        <div class="dashboard-grid">
            <div class="summary-column">
                <div class="summary-card">
                    <h3>Total Gastado (Filtrado)</h3>
                    <p class="value font-mono">${{ number_format($totalMontoFiltrado ?? 0, 2) }}</p>
                </div>
                <div class="summary-card">
                    <h3>Total Registros</h3>
                    <p class="value">{{ $totalRegistrosFiltrados ?? 0 }}</p>
                </div>
            </div>
            <div class="chart-card">
                <canvas id="comprasChart"></canvas>
            </div>
        </div>

        {{-- Formulario Dashboard / Filtros --}}
        <form method="GET" action="{{ route('inventario.compras.index') }}" class="filter-dashboard">
            <div class="filter-group">
                <label>Buscar Proveedor / Factura</label>
                <input type="text" name="search" class="filter-input" placeholder="Ej. 90900943" value="{{ request('search') }}">
            </div>
            
            <div class="filter-group">
                <label>Fecha Desde</label>
                <input type="date" name="fecha_inicio" class="filter-input" value="{{ request('fecha_inicio') }}">
            </div>

            <div class="filter-group">
                <label>Fecha Hasta</label>
                <input type="date" name="fecha_fin" class="filter-input" value="{{ request('fecha_fin') }}">
            </div>

            <div class="filter-group">
                <label>Método de Pago</label>
                <select name="metodo_pago" class="filter-input">
                    <option value="">Todos</option>
                    <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="tarjeta" {{ request('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                    <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px; margin-left: auto;">
                <a href="{{ route('inventario.compras.index') }}" class="btn-filter" title="Limpiar Filtros">
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
                        <th style="width: 120px;">N° FACTURA</th>
                        <th style="width: 250px;">PROVEEDOR</th>
                        <th style="width: 100px;">FECHA</th>
                        <th style="width: 150px;">MÉTODO PAGO</th>
                        <th style="width: 80px;" class="text-center">EGRESO</th> 
                        <th style="width: 120px;" class="text-right">TOTAL</th>
                        <th style="width: 150px;">REGISTRADO POR</th>
                        <th style="width: 100px;" class="text-right">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                    <tr>
                        <td>
                            <div>{{ $compra->numero_factura }}</div>
                            @if($compra->num_doc_interno)
                                <div class="text-muted">{{ $compra->num_doc_interno }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $compra->proveedor->nom_ter ?? 'Proveedor No Encontrado' }}</div>
                            <div class="text-muted">{{ $compra->cod_ter_proveedor }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($compra->fecha_factura)->format('d/m/Y') }}</td>
                        
                        <td>
                            @php
                                $metodo = strtolower($compra->metodo->nombre ?? '');
                                $claseColor = 'cell-default';
                                if(str_contains($metodo, 'efectivo')) $claseColor = 'cell-efectivo';
                                elseif(str_contains($metodo, 'tarjeta')) $claseColor = 'cell-tarjeta';
                                elseif(str_contains($metodo, 'cheque')) $claseColor = 'cell-cheque';
                            @endphp
                            <span class="{{ $claseColor }}">{{ $compra->metodo->nombre ?? 'N/A' }}</span>
                        </td>

                        <td class="text-center">{{ $compra->numero_egreso ?? '-' }}</td>
                        <td class="text-right font-mono">${{ number_format($compra->total_pago, 2) }}</td>
                        <td>{{ $compra->usuarioRegistro->name ?? 'Sistema' }}</td>
                        <td>
                            <div class="sheets-actions">
                                <a href="{{ route('inventario.compras.show', $compra->id) }}" class="action-icon" title="Ver Detalle"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('inventario.compras.edit', $compra->id) }}" class="action-icon" title="Editar Compra"><i class="bi bi-pencil"></i></a>
                                @if($compra->eg_archivo)
                                    <a href="{{ route('inventario.compras.archivo', $compra->id) }}" target="_blank" class="action-icon" title="Ver Soporte Adjunto"><i class="bi bi-paperclip"></i></a>
                                @endif
                                <a href="{{ route('inventario.compras.descargar', $compra->id) }}" class="action-icon" title="Descargar PDF"><i class="bi bi-download"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #5f6368;">
                            No hay compras registradas que coincidan con los filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($compras->hasPages())
            <div style="margin-top: 10px; font-size: 13px;">
                {{ $compras->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- NUEVO: Script del Gráfico --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Obtenemos los datos desde el controlador (pasados por PHP)
            const labelsFechas = @json($chartLabels ?? []);
            const dataTotales = @json($chartData ?? []);

            const ctx = document.getElementById('comprasChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', // 'bar' también queda bien
                data: {
                    labels: labelsFechas,
                    datasets: [{
                        label: 'Gasto por Fecha ($)',
                        data: dataTotales,
                        backgroundColor: 'rgba(26, 115, 232, 0.2)', // Azul claro sheets
                        borderColor: '#1a73e8', // Azul Sheets
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1a73e8',
                        pointRadius: 4,
                        fill: true, // Da un estilo de área sutil
                        tension: 0.2 // Curva ligera
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f3f4' },
                            ticks: { font: { size: 10 }, color: '#5f6368' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#5f6368' }
                        }
                    }
                }
            });
        });
    </script>
</x-base-layout>