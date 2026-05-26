<x-base-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Paleta General UX */
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            
            --text-main: #0f172a;
            --text-muted: #475569;
            --text-light: #94a3b8;
            
            --border-light: #e2e8f0;
            --border-focus: #cbd5e1;
            
            /* Colores de Google / Sheets */
            --primary-blue: #1a73e8;
            --primary-blue-soft: #e8f0fe;
            --success-green: #188038;
            --success-green-soft: #e6f4ea;
            --danger-red: #d93025;
            --danger-red-soft: #fce8e6;
            --warning-yellow: #f29900;
            --warning-yellow-soft: #fef7e0;
            
            /* Bordes y fondos exclusivos de la tabla tipo Sheets */
            --sheet-border: #cccccc;
            --sheet-header-bg: #f8f9fa;
            --sheet-text: #202124;
            
            /* Tonalidades de Filas de datos */
            --row-assigned: #f8fbff;
            --row-available: #ffffff;
            --row-issue: #fef2f2;
        }

        /* Reset & Tipografía base */
        .container { 
            max-width: 100%; 
            margin: 0 auto; 
            padding: 24px 32px; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            color: var(--text-main); 
            background-color: var(--bg-body); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        /* ==========================================
           HEADER & ACTIONS UX
           ========================================== */
        .header-section { 
            display: flex; justify-content: space-between; align-items: flex-end; 
            flex-wrap: wrap; gap: 20px;
        }
        .page-title { 
            font-size: 1.75rem; font-weight: 700; margin: 0; line-height: 1.2; 
            color: var(--text-main); letter-spacing: -0.025em;
        }
        .counter-badge { 
            background: var(--bg-surface); color: var(--text-muted); padding: 6px 14px; 
            border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
            border: 1px solid var(--border-light); display: inline-flex; 
            align-items: center; gap: 8px; margin-top: 8px;
        }
        .counter-dot { 
            width: 8px; height: 8px; background: var(--primary-blue); border-radius: 50%; 
        }

        /* Botones Premium */
        .toolbar-actions { display: flex; gap: 12px; align-items: center; }
        .btn-action { 
            display: inline-flex; align-items: center; justify-content: center; height: 38px; 
            border-radius: 8px; border: 1px solid var(--border-light); transition: all 0.2s; 
            cursor: pointer; text-decoration: none; padding: 0 16px; font-size: 0.85rem; 
            font-weight: 600; gap: 8px; background: var(--bg-surface); color: var(--text-muted);
        }
        .btn-action:hover { background: #f8fafc; color: var(--text-main); border-color: var(--border-focus); }
        .btn-primary { background: var(--primary-blue); color: #ffffff; border: none; }
        .btn-primary:hover { background: #1557b0; color: #ffffff; }
        
        .btn-icon-only { padding: 0 10px; border: none; background: transparent; color: var(--text-light); }
        .btn-icon-only:hover { background: var(--primary-blue-soft); color: var(--primary-blue); border-radius: 4px; }

        /* ==========================================
           DASHBOARD SECTION (Tarjetas Flotantes)
           ========================================== */
        .dashboard-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }
        .chart-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            height: 240px; 
            display: flex;
            flex-direction: column;
        }
        .chart-title {
            font-size: 0.75rem; font-weight: 700; color: var(--text-muted); 
            text-transform: uppercase; letter-spacing: 0.05em; 
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 12px;
        }
        .chart-instruction {
            font-size: 0.65rem; color: var(--text-light); font-weight: 500; 
            text-transform: none; background: var(--bg-body); padding: 4px 8px; border-radius: 6px;
        }
        /* ¡CORRECCIÓN DEL OVERFLOW DEL GRÁFICO AQUÍ! (min-height: 0) */
        .chart-container { 
            position: relative; 
            flex: 1 1 auto; 
            width: 100%; 
            min-height: 0; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ==========================================
           BARRA DE FILTROS MINIMAL
           ========================================== */
        .filter-section { 
            background: var(--bg-surface); padding: 16px 20px; 
            border: 1px solid var(--border-light); border-radius: 8px 8px 0 0; 
            border-bottom: none; display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 160px; }
        .filter-group.search-group { flex: 2; }
        .filter-label { font-size: 0.7rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }
        .filter-input, .filter-select { 
            padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 6px; 
            font-size: 0.85rem; background: var(--bg-body); color: var(--text-main); 
            width: 100%; outline: none;
        }
        .filter-input:focus, .filter-select:focus { 
            border-color: var(--primary-blue); background: var(--bg-surface);
            box-shadow: 0 0 0 2px var(--primary-blue-soft);
        }
        .filter-select:disabled { opacity: 0.6; cursor: not-allowed; }
        
        .btn-clear-filters { 
            color: var(--danger-red); font-size: 0.8rem; font-weight: 600; text-decoration: none; 
            display: none; align-items: center; gap: 6px; padding: 10px 14px; 
            border-radius: 6px; background: var(--danger-red-soft);
        }

        /* ==========================================
           TABLA DE DATOS (ESTILO GOOGLE SHEETS)
           ========================================== */
        #table-container { 
            flex: 1; 
            background: var(--bg-surface); 
            border: 1px solid var(--sheet-border); 
            border-radius: 0 0 8px 8px; 
            overflow: auto; 
            position: relative;
        }
        .t-sheet { 
            width: 100%; 
            border-collapse: collapse; 
            text-align: left; 
            /* Se usa Arial u otra tipografía de sistema para emular Sheets */
            font-family: 'Arial', sans-serif; 
            font-size: 0.85rem; 
        }
        
        /* Celdas generales: Todo tiene borde de cuadrícula */
        .t-sheet th, .t-sheet td {
            border: 1px solid var(--sheet-border);
            padding: 6px 12px;
            color: var(--sheet-text);
            vertical-align: middle;
        }

        /* Fila de Encabezados Principales */
        .t-sheet th { 
            background: var(--sheet-header-bg); 
            font-weight: 600; 
            font-size: 0.8rem; 
            position: sticky; 
            top: 0; 
            z-index: 10;
            box-shadow: 0 1px 0 var(--sheet-border); /* Asegura que el borde no se pierda al hacer scroll */
        }

        /* Columna de números al estilo 'Row Header' de Sheets */
        .t-sheet th.row-header, .t-sheet td.row-header {
            background: var(--sheet-header-bg);
            color: #5f6368;
            font-weight: 500;
            text-align: center;
            width: 45px;
            user-select: none;
        }

        /* Efecto Hover en las filas, igual que al seleccionar en Sheets */
        .t-sheet tr { transition: background-color 0.1s ease; }
        .t-sheet tr:hover td:not(.row-header) { background-color: var(--primary-blue-soft) !important; cursor: default; }

        /* Efecto Skeleton Carga */
        .loading .t-sheet { opacity: 0.4; pointer-events: none; }
        .loading::after {
            content: ""; position: absolute; top: 35px; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
            background-size: 200% 100%; animation: loading-skeleton 1.5s infinite;
            z-index: 20; pointer-events: none;
        }
        @keyframes loading-skeleton { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Tonalidades de fila según estado */
        .row-assigned td:not(.row-header) { background-color: var(--row-assigned); }
        .row-issue td:not(.row-header) { background-color: var(--row-issue); }

        /* Chips estilo Google Docs / Sheets dropdowns */
        .cat-tag { 
            font-size: 0.7rem; background: #f1f3f4; color: #3c4043; 
            padding: 2px 8px; border-radius: 12px; font-weight: 500; 
            display: inline-block;
        }
        .mono-code { 
            font-family: 'Consolas', monospace; 
            color: var(--primary-blue); font-weight: bold;
        }
        
        .status-pill { 
            display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; 
            border-radius: 12px; font-size: 0.75rem; font-weight: 600;
            border: 1px solid transparent;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        
        .st-ok { background: var(--success-green-soft); color: var(--success-green); border-color: #cce8d6; }
        .st-ok .status-dot { background: var(--success-green); }
        
        .st-busy { background: var(--primary-blue-soft); color: var(--primary-blue); border-color: #c2e0ff; }
        .st-busy .status-dot { background: var(--primary-blue); }
        
        .st-bad { background: var(--danger-red-soft); color: var(--danger-red); border-color: #fad2cf; }
        .st-bad .status-dot { background: var(--danger-red); }
        
        .st-fix { background: var(--warning-yellow-soft); color: var(--warning-yellow); border-color: #feefc3; }
        .st-fix .status-dot { background: var(--warning-yellow); }

        .pagination-container { padding: 12px 16px; background: var(--bg-surface); border: 1px solid var(--sheet-border); border-top: none; }

        /* Estado Vacío / Empty State */
        .empty-state { text-align: center; padding: 50px 20px !important; }
        .empty-icon { width: 50px; height: 50px; color: #dadce0; margin-bottom: 12px; }
        .empty-title { font-size: 1rem; font-weight: 600; color: var(--text-main); margin: 0 0 6px 0; }
        .empty-text { font-size: 0.85rem; color: var(--text-muted); margin: 0; }
        
        @media print {
            .dashboard-section, .filter-section, .toolbar-actions, .pagination-container, th:last-child, td:last-child { display: none !important; }
            .container { padding: 0 !important; background: white !important; }
            #table-container { border: none !important; box-shadow: none !important; }
        }
    </style>

    <div class="container">
        
        <div class="header-section">
            <div>
                <h1 class="page-title">Almacén de Activos</h1>
                <div class="counter-badge">
                    <span class="counter-dot"></span>
                    <span id="total-count">{{ $activos->total() }}</span> registros en total
                </div>
            </div>
            <div class="toolbar-actions">
                <a href="#" id="btn-generate-pdf" class="btn-action" title="Descargar como PDF">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </a>
                <a href="#" id="btn-generate-excel" class="btn-action" title="Descargar como Excel">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    Excel
                </a>
                <span style="width: 1px; height: 24px; background: var(--border-light); margin: 0 4px;"></span>
                <a href="{{ route('inventario.compras.create') }}" class="btn-action btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Ingreso
                </a>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="chart-card">
                <div class="chart-title">
                    Estado Físico
                    <span class="chart-instruction">Clic para filtrar</span>
                </div>
                <div class="chart-container">
                    <canvas id="chartEstados"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">
                    Distribución por Bodega
                    <span class="chart-instruction">Clic para filtrar</span>
                </div>
                <div class="chart-container">
                    <canvas id="chartBodegas"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">
                    Top Categorías
                    <span class="chart-instruction">Clic para filtrar</span>
                </div>
                <div class="chart-container">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        <form id="live-search-form" action="{{ route('inventario.activos.index') }}" method="GET" class="filter-section">
            <div class="filter-group search-group">
                <label class="filter-label">Búsqueda Inteligente</label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 12px; top: 10px; color: var(--text-light);" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" class="filter-input" style="padding-left: 36px;" placeholder="Placa, serial, nombre o responsable..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">Ubicación</label>
                <select name="bodega_id" id="bodega_id" class="filter-select">
                    <option value="">Todas las Bodegas</option>
                    @foreach($bodegas as $bodega)
                        <option value="{{ $bodega->id }}" {{ request('bodega_id') == $bodega->id ? 'selected' : '' }}>{{ $bodega->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Condición</label>
                <select name="estado_id" id="estado_id" class="filter-select">
                    <option value="">Todos los Estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ request('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Clasificación</label>
                <select name="subgrupo_id" id="subgrupo_id" class="filter-select">
                    <option value="">Todas</option>
                    @foreach($subgrupos as $subgrupo)
                        <option value="{{ $subgrupo->id }}" {{ request('subgrupo_id') == $subgrupo->id ? 'selected' : '' }}>{{ $subgrupo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <a href="javascript:void(0)" id="btn-reset" class="btn-clear-filters" style="{{ request()->hasAny(['search', 'bodega_id', 'estado_id', 'subgrupo_id']) ? 'display: flex;' : 'display: none;' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Limpiar
            </a>
        </form>

        <div id="table-container">
            <table class="t-sheet">
                <thead>
                    <tr>
                        <th class="row-header">Nº</th>
                        <th>Descripción y Categoría</th>
                        <th>Identificadores</th>
                        <th>Sede / Bodega</th>
                        <th>Estado</th>
                        <th>Asignación</th>
                        <th style="width: 80px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activos as $index => $activo)
                    @php
                        $rowClass = 'row-available';
                        if(in_array($activo->id_Estado, [3, 4])) $rowClass = 'row-issue';
                        elseif($activo->usuarioAsignado) $rowClass = 'row-assigned';

                        $statusClass = match($activo->id_Estado) {
                            1 => 'st-ok', 
                            2 => 'st-busy', 
                            3 => 'st-bad', 
                            4 => 'st-fix', 
                            default => 'st-busy'
                        };
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="row-header">{{ $activos->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight: bold; margin-bottom: 2px;">{{ $activo->nombre }}</div>
                            <span class="cat-tag">{{ $activo->subgrupo->nombre ?? 'Sin categoría' }}</span>
                        </td>
                        <td>
                            <div class="mono-code">{{ $activo->codigo_activo }}</div>
                            <div style="font-size: 0.75rem; color: #5f6368;">
                                SN: {{ $activo->serial ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            {{ $activo->referencia->bodega->nombre ?? 'Sin Bodega' }}
                        </td>
                        <td>
                            <div class="status-pill {{ $statusClass }}">
                                <span class="status-dot"></span>
                                {{ $activo->estado->nombre ?? 'N/D' }}
                            </div>
                        </td>
                        <td>
                            @if($activo->usuarioAsignado)
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #e8eaed; color: #3c4043; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: bold;">
                                        {{ strtoupper(substr($activo->usuarioAsignado->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $activo->usuarioAsignado->name }}</span>
                                </div>
                            @else
                                <span style="color: var(--success-green); font-weight: bold; font-size: 0.75rem;">DISPONIBLE</span>
                            @endif
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="{{ route('inventario.activos.show', $activo->id) }}" class="btn-action btn-icon-only" title="Ver Detalles">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            
                            <a href="{{ route('inventario.activos.edit', $activo->id) }}" class="btn-action btn-icon-only" title="Editar Activo" style="color: var(--primary-blue);">
                                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <h3 class="empty-title">Sin registros</h3>
                            <p class="empty-text">No hay activos que coincidan con los filtros aplicados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($activos->hasPages())
            <div class="pagination-container">
                {{ $activos->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 1. CONFIGURACIÓN DEL FILTRO AJAX
            // ==========================================
            const form = document.getElementById('live-search-form');
            const tableContainer = document.getElementById('table-container');
            const btnReset = document.getElementById('btn-reset');
            let timeout = null;

            function fetchResults() {
                const url = new URL(form.action);
                const formData = new FormData(form);
                url.search = new URLSearchParams(formData).toString();

                // Mostrar/ocultar botón limpiar
                const filtersActive = Array.from(formData.values()).some(val => val !== '');
                btnReset.style.display = filtersActive ? 'flex' : 'none';

                // Activar animación skeleton
                tableContainer.classList.add('loading');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    tableContainer.innerHTML = doc.getElementById('table-container').innerHTML;
                    document.getElementById('total-count').innerText = doc.getElementById('total-count').innerText;
                    
                    // Remover animación
                    tableContainer.classList.remove('loading');
                    window.history.pushState({}, '', url);
                })
                .catch(err => {
                    console.error("Error cargando datos:", err);
                    tableContainer.classList.remove('loading');
                });
            }

            form.querySelectorAll('input, select').forEach(el => {
                el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(fetchResults, el.tagName === 'INPUT' ? 400 : 0);
                });
            });

            btnReset.addEventListener('click', () => {
                form.reset();
                form.querySelectorAll('select').forEach(s => s.value = '');
                fetchResults();
            });

            form.addEventListener('submit', (e) => e.preventDefault());


            // ==========================================
            // 2. INICIALIZACIÓN DE GRÁFICOS (CHART.JS)
            // ==========================================
            
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#5f6368'; 
            
            const tooltipConfig = {
                backgroundColor: '#202124',
                padding: 10,
                cornerRadius: 4,
                titleFont: { size: 12, weight: '600' },
                bodyFont: { size: 12 },
                displayColors: true,
                boxPadding: 4
            };

            const rawEstados = @json($statsEstados ?? []);
            const rawBodegas = @json($statsBodegas ?? []);
            const rawCategorias = @json($statsCategorias ?? []);

            // --- GRÁFICO 1: ESTADOS (Doughnut adaptativo) ---
            if(document.getElementById('chartEstados') && rawEstados.length > 0) {
                const ctxEstados = document.getElementById('chartEstados').getContext('2d');
                new Chart(ctxEstados, {
                    type: 'doughnut',
                    data: {
                        labels: rawEstados.map(e => e.label),
                        datasets: [{
                            data: rawEstados.map(e => e.count),
                            backgroundColor: rawEstados.map(e => e.color),
                            borderWidth: 2, 
                            borderColor: '#ffffff',
                            hoverOffset: 4,
                            cutout: '70%' // Control del grosor
                        }]
                    },
                    options: {
                        responsive: true, 
                        maintainAspectRatio: false, // Ahora funciona gracias a min-height:0 en CSS
                        layout: { padding: 10 },
                        plugins: { 
                            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
                            tooltip: tooltipConfig
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('estado_id').value = rawEstados[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // --- GRÁFICO 2: BODEGAS (Bar Horizontal Minimal) ---
            if(document.getElementById('chartBodegas') && rawBodegas.length > 0) {
                const ctxBodegas = document.getElementById('chartBodegas').getContext('2d');
                new Chart(ctxBodegas, {
                    type: 'bar',
                    data: {
                        labels: rawBodegas.map(b => b.label ?? ('Bodega ' + b.id)),
                        datasets: [{
                            label: 'Activos',
                            data: rawBodegas.map(b => b.count),
                            backgroundColor: '#1a73e8',
                            borderRadius: 4,
                            barThickness: 'flex',
                            maxBarThickness: 24
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: { top: 10, bottom: 10, right: 15 } },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: { 
                            x: { display: false, grid: { display: false } }, 
                            y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11 } } } 
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('bodega_id').value = rawBodegas[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // --- GRÁFICO 3: CATEGORÍAS (Bar Vertical Clean) ---
            if(document.getElementById('chartCategorias') && rawCategorias.length > 0) {
                const ctxCategorias = document.getElementById('chartCategorias').getContext('2d');
                new Chart(ctxCategorias, {
                    type: 'bar',
                    data: {
                        labels: rawCategorias.map(c => c.label),
                        datasets: [{
                            data: rawCategorias.map(c => c.count),
                            backgroundColor: rawCategorias.map(c => c.color),
                            borderRadius: 4,
                            maxBarThickness: 30
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: { top: 10 } },
                        plugins: { legend: { display: false }, tooltip: tooltipConfig },
                        scales: { 
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 45 } }, 
                            y: { display: false, grid: { display: false } } 
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                document.getElementById('subgrupo_id').value = rawCategorias[index].id;
                                fetchResults();
                            }
                        }
                    }
                });
            }

            // Botones de Exportación
            document.getElementById('btn-generate-pdf').addEventListener('click', function(e) {
                e.preventDefault();
                const searchParams = new URLSearchParams(new FormData(form)).toString();
                window.location.href = "{{ route('inventario.activos.pdf') }}?" + searchParams;
            });
            
            document.getElementById('btn-generate-excel').addEventListener('click', function(e) {
                e.preventDefault();
                const searchParams = new URLSearchParams(new FormData(form)).toString();
                window.location.href = "{{ route('inventario.activos.excel') }}?" + searchParams;
            });
        });
    </script>
</x-base-layout>