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
            display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px;
        }
        .sheets-header h1 {
            font-size: 20px; font-weight: normal; margin: 0 0 4px 0; color: #202124; display: flex; align-items: center; gap: 8px;
        }
        .btn-sheets-primary {
            background-color: #1a73e8; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-sheets-primary:hover { background-color: #1b66c9; box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); }

        /* Dashboard Widgets */
        .dashboard-grid { display: grid; grid-template-columns: 250px 1fr; gap: 15px; margin-bottom: 15px; }
        .summary-column { display: flex; flex-direction: column; gap: 15px; }
        .summary-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; transition: box-shadow 0.2s ease; }
        .summary-card:hover { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .summary-card h3 { margin: 0 0 5px 0; font-size: 12px; color: #5f6368; text-transform: uppercase; }
        .summary-card .value { margin: 0; font-size: 24px; color: #1a73e8; font-weight: normal; }
        .chart-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; position: relative; height: 200px; }

        /* Dashboard / Panel de Filtros */
        .filter-dashboard { background-color: #f8f9fa; border: 1px solid #ccc; border-bottom: none; padding: 10px 15px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 11px; font-weight: bold; color: #5f6368; text-transform: uppercase; }
        .filter-input { height: 28px; padding: 0 8px; border: 1px solid #ccc; border-radius: 2px; font-size: 13px; color: #202124; background-color: #fff; min-width: 150px; transition: border-color 0.2s ease; }
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

        /* Celdas Pastel de Estado */
        .cell-success { background-color: #ceead6; color: #0d652d; border-radius: 2px; padding: 2px 6px; font-size: 12px; }
        .cell-warning { background-color: #fef0db; color: #b06000; border-radius: 2px; padding: 2px 6px; font-size: 12px; }

        /* Utilidades y Acciones */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-muted { color: #5f6368; font-size: 11px; }
        .font-mono { font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; font-weight: bold;}
        
        .sheets-actions { display: flex; justify-content: flex-end; gap: 10px; align-items: center; }
        .action-icon { color: #5f6368; font-size: 15px; text-decoration: none; cursor: pointer; border: none; background: transparent; padding: 0; transition: color 0.2s ease; }
        .action-icon:hover { color: #1a73e8; }
        .icon-upload:hover { color: #047857; } /* Verde para subir */
        .icon-view:hover { color: #b91c1c; } /* Rojo para PDF */

        /* Alertas Sheets */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .alert-sheets { padding: 8px 12px; border-radius: 4px; font-size: 13px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; animation: fadeIn 0.3s ease-out; }
        .alert-success { background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
        .alert-error { background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; }
    </style>

    <div class="sheets-wrapper">

        {{-- Sistema de Alertas --}}
        @if(session('error'))
            <div class="alert-sheets alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert-sheets alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        
        <div class="sheets-header">
            <div>
                <h1><i class="bi bi-journal-check" style="font-size: 18px; color: #1a73e8;"></i> Bitácora de Movimientos</h1>
            </div>
            <a href="{{ route('inventario.movimientos.create') }}" class="btn-sheets-primary">
                <i class="bi bi-plus-lg"></i> Crear Nuevo Movimiento
            </a>
        </div>

        {{-- Dashboard Widgets --}}
        <div class="dashboard-grid">
            <div class="summary-column">
                <div class="summary-card">
                    <h3>Total Movimientos (Filtrados)</h3>
                    <p class="value">{{ $totalRegistros ?? 0 }}</p>
                </div>
                <div class="summary-card">
                    <h3>Actas Resguardadas (S3)</h3>
                    <p class="value" style="color: #0d652d;">{{ $totalFirmadas ?? 0 }}</p>
                </div>
            </div>
            <div class="chart-card">
                <canvas id="movimientosChart"></canvas>
            </div>
        </div>

        {{-- Formulario Dashboard / Filtros --}}
        <form method="GET" action="{{ route('inventario.movimientos.index') }}" class="filter-dashboard">
            <div class="filter-group">
                <label>Buscar Código o Responsable</label>
                <input type="text" name="search" class="filter-input" placeholder="Ej. ACT-001..." value="{{ request('search') }}">
            </div>
            
            <div class="filter-group">
                <label>Tipo de Registro</label>
                <input type="text" name="tipo" class="filter-input" placeholder="Ej. Entrada, Salida..." value="{{ request('tipo') }}">
            </div>

            <div class="filter-group">
                <label>Fecha Desde</label>
                <input type="date" name="fecha_inicio" class="filter-input" value="{{ request('fecha_inicio') }}">
            </div>

            <div class="filter-group">
                <label>Fecha Hasta</label>
                <input type="date" name="fecha_fin" class="filter-input" value="{{ request('fecha_fin') }}">
            </div>

            <div style="display: flex; gap: 8px; margin-left: auto;">
                <a href="{{ route('inventario.movimientos.index') }}" class="btn-filter" title="Limpiar Filtros">
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
                        <th style="width: 130px;">CÓDIGO ACTA</th>
                        <th style="width: 180px;">TIPO REGISTRO</th>
                        <th style="width: 200px;">RESPONSABLE</th>
                        <th style="width: 120px;">FECHA</th>
                        <th style="width: 150px;">ESTADO FIRMA</th>
                        <th style="width: 150px;">CREADO POR</th>
                        <th style="width: 100px;" class="text-right">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    <tr>
                        <td class="font-mono" style="color: #1a73e8;">{{ $mov->codigo_acta }}</td>
                        <td>{{ $mov->tipoRegistro->nombre ?? 'N/A' }}</td>
                        <td>{{ $mov->responsable->name ?? 'N/A' }}</td>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        
                        {{-- Indicador de Auditoría (Badge visual) --}}
                        <td>
                            @if($mov->acta_archivo)
                                <span class="cell-success" title="Documento legal resguardado en S3"><i class="bi bi-shield-check"></i> Segura</span>
                            @else
                                <span class="cell-warning" title="Falta subir el documento con firmas físicas"><i class="bi bi-hourglass-split"></i> Pendiente</span>
                            @endif
                        </td>
                        
                        <td class="text-muted">{{ $mov->creador->name ?? 'Sistema' }}</td>
                        
                        <td>
                            <div class="sheets-actions">
                                {{-- Descargar Original siempre disponible --}}
                                <a href="{{ route('inventario.movimientos.pdf', $mov->id) }}" class="action-icon" title="Imprimir acta original">
                                    <i class="bi bi-printer"></i>
                                </a>

                                {{-- Lógica Condicional para el Archivo Firmado --}}
                                @if($mov->acta_archivo)
                                    {{-- Ver PDF en S3 --}}
                                    <a href="{{$mov->getFile($mov->acta_archivo)}}" target="_blank" class="action-icon icon-view" title="Ver documento digitalizado">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                @else
                                    {{-- Subir PDF (Formulario oculto activado por un input file) --}}
                                    <form action="{{ route('inventario.movimientos.upload', $mov->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('POST')
                                        <label class="action-icon icon-upload" title="Subir el acta escaneada" style="cursor: pointer;">
                                            <i class="bi bi-cloud-arrow-up-fill"></i>
                                            <input type="file" name="acta_pdf" accept="application/pdf" style="display: none;" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #5f6368;">
                            No se encontraron movimientos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($movimientos->hasPages())
            <div style="margin-top: 10px; font-size: 13px;">
                {{ $movimientos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Script del Gráfico Chart.js --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const labelsFechas = @json($chartLabels ?? []);
            const dataTotales = @json($chartData ?? []);

            const ctx = document.getElementById('movimientosChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelsFechas,
                    datasets: [{
                        label: 'Movimientos',
                        data: dataTotales,
                        // UX Update: Color pastel con borde definido
                        backgroundColor: 'rgba(26, 115, 232, 0.4)', // Azul claro/pastel
                        borderColor: 'rgba(26, 115, 232, 1)',       // Borde azul sólido
                        borderWidth: 1,
                        borderRadius: 3,
                        // Color al pasar el mouse por encima (hover)
                        hoverBackgroundColor: 'rgba(26, 115, 232, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(32, 33, 36, 0.9)',
                            padding: 10,
                            titleFont: { family: 'Arial', size: 12 },
                            bodyFont: { family: 'Arial', size: 12 },
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 10, family: 'Arial' }, color: '#5f6368' },
                            grid: { color: '#f1f3f4', drawBorder: false }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, family: 'Arial' }, color: '#5f6368' }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                }
            });
        });
    </script>
</x-base-layout>