<x-base-layout>
    <style>
        :root {
            /* Paleta Pastel Minimalista UX */
            --primary-pastel: #eef2ff;
            --primary-text: #4f46e5;
            --primary-hover: #e0e7ff;
            
            --secondary-pastel: #f1f5f9;
            --secondary-text: #475569;
            
            --accent-pastel: #f0fdf4; /* Verde pastel para ingresos */
            --accent-text: #16a34a;
            
            --danger-pastel: #fef2f2; /* Rojo pastel para alertas */
            --danger-text: #dc2626;

            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        .container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', system-ui, sans-serif; color: var(--slate-900); background-color: #fdfdfd; }
        
        /* ==========================================
           HEADER & ACTIONS
           ========================================== */
        .header-section { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; flex-wrap: wrap; }
        .page-title { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.025em; margin: 0; line-height: 1; color: var(--slate-900); }
        .counter-badge { background: white; color: var(--slate-600); padding: 6px 14px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; border: 1px solid var(--slate-200); display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .counter-dot { width: 8px; height: 8px; background: var(--primary-text); border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(79, 70, 229, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }

        /* ==========================================
           BARRA DE FILTROS MINIMALISTA
           ========================================== */
        .filter-section { background: #fff; padding: 24px; border-radius: 24px; border: 1px solid var(--slate-100); margin-bottom: 35px; display: flex; gap: 18px; flex-wrap: wrap; align-items: flex-end; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05); }
        .filter-group { display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 200px; }
        .filter-label { font-size: 0.7rem; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.1em; }
        .filter-input, .filter-select { padding: 12px 16px; border: 1px solid var(--slate-100); border-radius: 14px; font-size: 0.9rem; background: var(--slate-50); transition: all 0.2s ease; color: var(--slate-700); width: 100%; }
        .filter-input:focus, .filter-select:focus { outline: none; border-color: var(--primary-text); background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05); }
        
        /* Deshabilitado Soft */
        .filter-select:disabled { background: #f8fafc; cursor: not-allowed; opacity: 0.5; color: #cbd5e1; border: 1px dashed var(--slate-200); }

        /* Contenedor de la tabla y efectos de carga */
        #table-container { transition: all 0.3s ease; position: relative; }
        .loading { opacity: 0.5; pointer-events: none; filter: grayscale(1); }

        /* ==========================================
           TABLE CARD MINIMAL
           ========================================== */
        .t-card { background: #fff; border-radius: 28px; border: 1px solid var(--slate-100); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04); overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; table-layout: auto; }
        .t-min th { background: #fafafa; text-align: left; padding: 18px 24px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; border-bottom: 1px solid var(--slate-50); letter-spacing: 0.05em; }
        .t-min tr { transition: background 0.2s; }
        .t-min tr:hover { background-color: #fcfdfe; }
        .t-min td { padding: 20px 24px; border-bottom: 1px solid var(--slate-50); vertical-align: middle; }

        /* Pills Pastel */
        .pill { padding: 6px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .st-ok { background: #ecfdf5; color: #059669; }
        .st-busy { background: #eff6ff; color: #2563eb; }
        .st-bad { background: #fef2f2; color: #dc2626; }
        .st-fix { background: #fffbeb; color: #d97706; }
        
        .warranty-tag { font-size: 0.7rem; padding: 4px 10px; border-radius: 8px; background: #f1f5f9; color: #475569; font-weight: 700; }
        .expired { background: #fff1f2; color: #e11d48; }

        /* ==========================================
           BOTONES UX PASTEL
           ========================================== */
        .btn-action { display: inline-flex; align-items: center; justify-content: center; height: 42px; border-radius: 14px; border: 1px solid transparent; transition: all 0.2s ease; cursor: pointer; text-decoration: none; padding: 0 18px; font-size: 0.85rem; font-weight: 700; gap: 8px; }
        
        /* Botón PDF - Azul Pastel */
        .btn-pdf-pastel { background-color: var(--primary-pastel); color: var(--primary-text); }
        .btn-pdf-pastel:hover { background-color: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1); }
        
        /* Botón Registro - Verde Pastel */
        .btn-success-pastel { background-color: var(--accent-pastel); color: var(--accent-text); }
        .btn-success-pastel:hover { background-color: #dcfce7; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(22, 163, 74, 0.1); }

        /* Botón Tabla - Gris/White Minimal */
        .btn-table { background-color: #fff; border: 1px solid var(--slate-200); color: var(--slate-600); height: 36px; padding: 0 12px; border-radius: 10px; }
        .btn-table:hover { background-color: var(--slate-900); color: #fff; border-color: var(--slate-900); }
        
        .btn-clear-filters { color: var(--danger-text); font-size: 0.8rem; font-weight: 700; text-decoration: none; display: none; margin-left: auto; padding: 8px 12px; border-radius: 10px; background: var(--danger-pastel); }

        /* ==========================================
           ESTILOS IMPRESIÓN
           ========================================== */
        .print-only-header { display: none; text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { background: white !important; }
            nav, header, footer, .filter-section, .btn-action, .actions-wrapper, .t-min th:last-child, .t-min td:last-child, .pagination { display: none !important; }
            .print-only-header { display: block; }
            .header-section { display: none; }
            .container { padding: 0 !important; margin: 0 !important; }
            .t-card { box-shadow: none !important; border: 1px solid #eee !important; border-radius: 0 !important; }
        }
    </style>

    <div class="container">
        
        <div class="print-only-header">
            <h1 style="margin:0; font-size: 24px; color: #0f172a;">REPORTE DE INVENTARIO FÍSICO</h1>
            <p style="margin:8px 0 0 0; font-size: 14px; color: #64748b;">Fecha de consulta: {{ date('d/m/Y h:i A') }}</p>
            <p style="margin:4px 0 0 0; font-size: 12px; color: #94a3b8;">Registros encontrados: {{ $activos->total() }}</p>
        </div>

        <div class="header-section">
            <div>
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary-text); text-transform: uppercase; letter-spacing: 0.2em; display: block; margin-bottom: 10px; opacity: 0.8;">
                    Sistema de Gestión de Activos
                </span>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <h1 class="page-title">Almacén de Activos</h1>
                    <div class="counter-badge">
                        <span class="counter-dot"></span>
                        <span id="total-count">{{ $activos->total() }}</span> registros
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="#" id="btn-generate-pdf" class="btn-action btn-pdf-pastel">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Exportar Reporte
                </a>
                <a href="{{ route('inventario.compras.create') }}" class="btn-action btn-success-pastel">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Ingreso
                </a>
            </div>
        </div>

        <form id="live-search-form" action="{{ route('inventario.activos.index') }}" method="GET" class="filter-section">
            
            <div class="filter-group" style="flex: 2.5;">
                <label class="filter-label">Búsqueda Inteligente</label>
                <input type="text" name="search" class="filter-input" placeholder="Buscar por placa, serial, nombre..." value="{{ request('search') }}" autocomplete="off">
            </div>

            <div class="filter-group">
                <label class="filter-label">Bodega / Sede</label>
                <select name="bodega_id" id="bodega_id" class="filter-select">
                    <option value="">-- Todas las Bodegas --</option>
                    @foreach($bodegas as $bodega)
                        <option value="{{ $bodega->id }}" {{ request('bodega_id') == $bodega->id ? 'selected' : '' }}>
                            {{ $bodega->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Estado</label>
                <select name="estado_id" id="estado_id" class="filter-select" {{ empty(request('bodega_id')) ? 'disabled' : '' }}>
                    <option value="">
                        {{ empty(request('bodega_id')) ? 'Elija Bodega...' : '-- Todos los Estados --' }}
                    </option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ request('estado_id') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Marca</label>
                <select name="marca_id" class="filter-select">
                    <option value="">-- Todas --</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Categoría</label>
                <select name="subgrupo_id" class="filter-select">
                    <option value="">-- Todas --</option>
                    @foreach($subgrupos as $subgrupo)
                        <option value="{{ $subgrupo->id }}" {{ request('subgrupo_id') == $subgrupo->id ? 'selected' : '' }}>{{ $subgrupo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <a href="{{ route('inventario.activos.index') }}" id="btn-reset" class="btn-clear-filters" style="{{ request()->hasAny(['search', 'bodega_id', 'estado_id']) ? 'display: flex;' : 'display: none;' }}">
                ✖ Limpiar
            </a>
        </form>

        <div id="table-container">
            <div class="t-card">
                <table class="t-min">
                    <thead>
                        <tr>
                            <th>Descripción / Marca</th>
                            <th>Identificación</th>
                            <th>Ubicación Actual</th>
                            <th>Estado</th>
                            <th>Garantía</th>
                            <th>Responsable</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $activo)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900); font-size: 0.95rem;">{{ $activo->nombre }}</div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px;">
                                    <span style="font-size: 0.65rem; color: var(--primary-text); background: var(--primary-pastel); padding: 2px 8px; border-radius: 6px; font-weight: 800;">
                                        {{ $activo->marca->nombre ?? 'N/A' }}
                                    </span>
                                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 500;">
                                        {{ $activo->subgrupo->nombre ?? '' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary-text); font-size: 0.9rem;">
                                    {{ $activo->codigo_activo }}
                                </div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;">
                                    S/N: <span style="color: #475569; font-weight: 600;">{{ $activo->serial ?? 'S/R' }}</span>
                                </div>
                            </td>

                            <td>
                                <div style="font-size: 0.85rem; font-weight: 600; color: #334155;">
                                    {{ $activo->referencia->bodega->nombre ?? 'Sin Bodega' }}
                                </div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">
                                    {{ $activo->municipio->nombre ?? 'Local' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $statusClass = match($activo->id_Estado) {
                                        1 => 'st-ok', 
                                        2 => 'st-busy', 
                                        3 => 'st-bad', 
                                        4 => 'st-fix', 
                                        default => 'st-busy'
                                    };
                                @endphp
                                <span class="pill {{ $statusClass }}">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                    {{ $activo->estado->nombre ?? 'N/D' }}
                                </span>
                            </td>

                            <td>
                                @if($activo->fecha_fin_garantia)
                                    <div class="warranty-tag {{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->isPast() ? 'expired' : '' }}">
                                        {{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span style="color: #e2e8f0; font-size: 0.65rem; font-weight: 800;">---</span>
                                @endif
                            </td>

                            <td>
                                @if($activo->usuarioAsignado)
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 26px; height: 26px; border-radius: 9px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; border: 1px solid #e2e8f0;">
                                            {{ strtoupper(substr($activo->usuarioAsignado->name, 0, 2)) }}
                                        </div>
                                        <div style="font-size: 0.8rem; font-weight: 600; color: #475569;">{{ $activo->usuarioAsignado->name }}</div>
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-weight: 600; font-size: 0.7rem; letter-spacing: 0.05em;">DISPONIBLE</span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <div class="actions-wrapper">
                                    <a href="{{ route('inventario.activos.show', $activo->id) }}" class="btn-action btn-table" title="Ver Hoja de Vida">
                                        Detalle
                                    </a>
                                    <a href="{{ route('inventario.activos.edit', $activo->id) }}" class="btn-action btn-table" style="padding: 0 10px;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div style="opacity: 0.5; margin-bottom: 10px;">📭</div>
                                    <h3 style="margin: 0; color: #94a3b8; font-size: 1.1rem;">No hay activos para mostrar</h3>
                                    <p style="margin: 5px 0 0 0; color: #cbd5e1; font-size: 0.9rem;">Ajusta los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($activos->hasPages())
                    <div style="padding: 24px; border-top: 1px solid #f8fafc; background: #fff;" class="pagination">
                        {{ $activos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Lógica de Exportación PDF con parámetros actuales
        document.getElementById('btn-generate-pdf').addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('live-search-form');
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData).toString();
            window.location.href = "{{ route('inventario.activos.pdf') }}?" + searchParams;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('live-search-form');
            const tableContainer = document.getElementById('table-container');
            const bodegaSelect = document.getElementById('bodega_id');
            const estadoSelect = document.getElementById('estado_id');
            const countDisplay = document.getElementById('total-count');
            const btnReset = document.getElementById('btn-reset');
            let timeout = null;

            /**
             * Función de refresco Ajax para filtros
             */
            function fetchResults() {
                const url = new URL(form.action);
                const formData = new FormData(form);
                url.search = new URLSearchParams(formData).toString();

                // Manejo visual del botón de reset
                const filtersActive = Array.from(formData.values()).some(val => val !== '');
                btnReset.style.display = filtersActive ? 'flex' : 'none';

                tableContainer.classList.add('loading');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Actualización de la tabla
                    tableContainer.innerHTML = doc.getElementById('table-container').innerHTML;
                    
                    // Sincronización del contador dinámico
                    countDisplay.innerText = doc.getElementById('total-count').innerText;
                    
                    // Actualización del select de estados (Anidación)
                    const newEstadoSelect = doc.getElementById('estado_id');
                    estadoSelect.innerHTML = newEstadoSelect.innerHTML;
                    estadoSelect.disabled = newEstadoSelect.disabled;

                    tableContainer.classList.remove('loading');
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error("Error en la petición Ajax:", error);
                    tableContainer.classList.remove('loading');
                });
            }

            // Escucha para búsqueda por texto (Debounce de 450ms)
            form.querySelector('input[name="search"]').addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 450); 
            });

            // Escucha para cambio de bodega
            bodegaSelect.addEventListener('change', () => {
                estadoSelect.value = ""; // Limpieza de estado al cambiar bodega
                fetchResults();
            });

            // Escucha para el resto de selectores
            form.querySelectorAll('select:not(#bodega_id)').forEach(el => {
                el.addEventListener('change', fetchResults);
            });

            // Prevención de envío tradicional del formulario
            form.addEventListener('submit', (e) => e.preventDefault());
        });
    </script>
</x-base-layout>