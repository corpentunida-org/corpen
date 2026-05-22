<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Contenedor principal estilo Sheets */
        .sheets-wrapper { font-family: 'Arial', 'Roboto', sans-serif; width: 100%; padding: 10px 15px; background-color: #fff; color: #000; }

        /* Cabecera */
        .sheets-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; }
        .sheets-header h1 { font-size: 20px; font-weight: normal; margin: 0 0 4px 0; color: #202124; display: flex; align-items: center; gap: 8px; }
        .btn-sheets-primary { background-color: #1a73e8; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; transition: all 0.2s ease; }
        .btn-sheets-primary:hover { background-color: #1b66c9; box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); }

        /* Dashboard Widgets */
        .dashboard-grid { display: grid; grid-template-columns: 250px 1fr; gap: 15px; margin-bottom: 15px; }
        .summary-column { display: flex; flex-direction: column; gap: 15px; }
        .summary-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; transition: box-shadow 0.2s ease; }
        .summary-card:hover { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .summary-card h3 { margin: 0 0 5px 0; font-size: 12px; color: #5f6368; text-transform: uppercase; }
        .summary-card .value { margin: 0; font-size: 24px; color: #1a73e8; font-weight: normal; }
        .chart-card { border: 1px solid #ccc; border-radius: 4px; padding: 15px; background-color: #fff; position: relative; height: 200px; }

        /* Panel de Filtros */
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

        /* Estilos Internos de Tabla */
        .text-right { text-align: right !important; }
        .text-muted { color: #5f6368; font-size: 11px; }
        .font-mono { font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; font-weight: bold; }
        .activo-link { color: #1a73e8; text-decoration: none; font-weight: bold; }
        .activo-link:hover { text-decoration: underline; }
        
        /* Celdas Pastel */
        .cell-cost { background-color: #e6f4ea; color: #137333; border-radius: 2px; padding: 2px 6px; }

        /* Acciones y Subida de Archivos Compacta */
        .upload-inline { display: flex; align-items: center; gap: 6px; margin: 0; }
        input[type="file"] { display: none; }
        .custom-file-label { display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; background: #f1f3f4; border: 1px dashed #9aa0a6; border-radius: 2px; font-size: 11px; color: #5f6368; cursor: pointer; transition: all 0.2s; max-width: 120px; overflow: hidden; text-overflow: ellipsis; }
        .custom-file-label:hover { background: #e8eaed; color: #202124; }
        .custom-file-label.has-file { background: #e8f0fe; border-color: #1a73e8; color: #1a73e8; border-style: solid; }
        .btn-upload { background: #188038; color: white; border: none; padding: 4px 8px; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: bold; display: none; }
        .btn-upload.show { display: inline-block; animation: fadeIn 0.3s ease; }
        .btn-upload:hover { background: #137333; }
        
        .action-icon { color: #5f6368; font-size: 15px; text-decoration: none; cursor: pointer; border: none; background: transparent; padding: 0; transition: color 0.2s; }
        .action-icon:hover { color: #1a73e8; }
        .icon-view:hover { color: #d93025; } /* Rojo PDF Sheets */

        /* Alertas Sheets */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .alert-sheets { padding: 8px 12px; border-radius: 4px; font-size: 13px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; animation: fadeIn 0.3s ease-out; }
        .alert-success { background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
        .alert-error { background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; }

        /* Modal Loading (Manteniendo tu estilo original pero ajustado) */
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(32, 33, 36, 0.7); backdrop-filter: blur(2px); z-index: 9999; display: none; align-items: center; justify-content: center; flex-direction: column; }
        .loading-overlay.active { display: flex; animation: fadeIn 0.3s ease; }
        .loading-card { background: #fff; padding: 30px 40px; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .spinner { width: 40px; height: 40px; border: 4px solid #f1f3f4; border-top: 4px solid #1a73e8; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .loading-title { font-size: 16px; color: #202124; margin: 0 0 5px 0; }
        .loading-text { font-size: 13px; color: #5f6368; margin: 0; }
    </style>

    {{-- Modal Loading --}}
    <div class="loading-overlay" id="loadingModal">
        <div class="loading-card">
            <div class="spinner"></div>
            <h3 class="loading-title">Subiendo documento...</h3>
            <p class="loading-text">Por favor no cierres esta ventana.</p>
        </div>
    </div>

    <div class="sheets-wrapper">

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert-sheets alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-sheets alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif
        
        <div class="sheets-header">
            <div>
                <h1><i class="bi bi-tools" style="font-size: 18px; color: #1a73e8;"></i> Historial Técnico</h1>
            </div>
            <a href="{{ route('inventario.mantenimientos.create') }}" class="btn-sheets-primary">
                <i class="bi bi-plus-lg"></i> Registrar Servicio
            </a>
        </div>

        {{-- Dashboard Widgets --}}
        <div class="dashboard-grid">
            <div class="summary-column">
                <div class="summary-card">
                    <h3>Total Servicios (Filtrados)</h3>
                    <p class="value">{{ $totalRegistros ?? 0 }}</p>
                </div>
                <div class="summary-card">
                    <h3>Costo Total Invertido</h3>
                    <p class="value font-mono" style="color: #137333;">${{ number_format($totalCosto ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="chart-card">
                <canvas id="mantenimientosChart"></canvas>
            </div>
        </div>

        {{-- Formulario Dashboard / Filtros --}}
        <form method="GET" action="{{ route('inventario.mantenimientos.index') }}" class="filter-dashboard">
            <div class="filter-group">
                <label>Buscar Activo o Detalle</label>
                <input type="text" name="search" class="filter-input" placeholder="Ej. Computador..." value="{{ request('search') }}">
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
                <a href="{{ route('inventario.mantenimientos.index') }}" class="btn-filter" title="Limpiar Filtros">
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
                        <th style="width: 100px;">FECHA</th>
                        <th style="width: 250px;">ACTIVO</th>
                        <th style="width: 300px;">DETALLE DEL SERVICIO</th>
                        <th style="width: 120px;" class="text-right">COSTO</th>
                        <th style="width: 150px;">REGISTRADO POR</th>
                        <th style="width: 180px;">SOPORTE (FACTURA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimientos as $mant)
                    <tr>
                        <td>
                            <div>{{ $mant->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted">{{ $mant->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <a href="{{ route('inventario.activos.show', $mant->activo->id) }}" class="activo-link" title="Ver hoja de vida del activo">
                                {{ $mant->activo->nombre }}
                            </a>
                            <div class="text-muted">{{ $mant->activo->codigo_activo }}</div>
                        </td>
                        <td>
                            <span style="display: inline-block; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $mant->detalle }}">
                                {{ $mant->detalle }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="font-mono cell-cost">${{ number_format($mant->costo_mantenimiento, 2) }}</span>
                        </td>
                        <td>{{ $mant->creador->name ?? 'Sistema' }}</td>
                        
                        {{-- Columna de Archivos y Acciones adaptada a Sheets --}}
                        <td>
                            @if($mant->acta)
                                <a href="{{ $mant->getFile($mant->acta) }}" target="_blank" class="action-icon icon-view" title="Ver Factura / Soporte">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Ver Documento
                                </a>
                            @else
                                <form action="{{ route('inventario.mantenimientos.upload', $mant->id) }}" method="POST" enctype="multipart/form-data" class="upload-inline" onsubmit="showLoadingModal()">
                                    @csrf
                                    <label for="file-{{ $mant->id }}" class="custom-file-label" id="label-{{ $mant->id }}" title="Seleccionar PDF/Imagen">
                                        <i class="bi bi-paperclip"></i> Adjuntar
                                    </label>
                                    
                                    <input type="file" name="acta_archivo" id="file-{{ $mant->id }}" accept=".pdf,.jpg,.jpeg,.png" required onchange="handleFileSelect(this, '{{ $mant->id }}')">
                                    
                                    <button type="submit" class="btn-upload" id="btn-{{ $mant->id }}">
                                        Subir
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #5f6368;">
                            No hay mantenimientos registrados que coincidan con los filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mantenimientos->hasPages())
            <div style="margin-top: 10px; font-size: 13px;">
                {{ $mantenimientos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script>
        // Lógica de archivo personalizada adaptada al tamaño compacto
        function handleFileSelect(input, id) {
            const label = document.getElementById('label-' + id);
            const btn = document.getElementById('btn-' + id);
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                label.innerHTML = `<i class="bi bi-check2"></i> ${fileName}`;
                label.classList.add('has-file');
                btn.classList.add('show');
            } else {
                label.innerHTML = `<i class="bi bi-paperclip"></i> Adjuntar`;
                label.classList.remove('has-file');
                btn.classList.remove('show');
            }
        }

        function showLoadingModal() {
            document.getElementById('loadingModal').classList.add('active');
            const btns = document.querySelectorAll('.btn-upload');
            btns.forEach(btn => btn.style.pointerEvents = 'none');
        }

        // Gráfico Chart.js - Estilo Área para el Costo
        document.addEventListener("DOMContentLoaded", function() {
            const labelsFechas = @json($chartLabels ?? []);
            const dataCostos = @json($chartData ?? []);

            const ctx = document.getElementById('mantenimientosChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: labelsFechas,
                    datasets: [{
                        label: 'Inversión en Mantenimiento ($)',
                        data: dataCostos,
                        backgroundColor: 'rgba(52, 168, 83, 0.2)', // Verde pastel Sheets (Costo = Dinero)
                        borderColor: 'rgba(52, 168, 83, 1)',       // Borde Verde
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(52, 168, 83, 1)',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.3 // Suaviza la línea
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
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return ' Costo: $' + value.toLocaleString('en-US', {minimumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: { size: 10, family: 'Arial' }, color: '#5f6368' },
                            grid: { color: '#f1f3f4', drawBorder: false }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, family: 'Arial' }, color: '#5f6368' }
                        }
                    },
                    interaction: { mode: 'index', intersect: false }
                }
            });
        });
    </script>
</x-base-layout>