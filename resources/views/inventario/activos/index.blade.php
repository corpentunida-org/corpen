<x-base-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        .container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', system-ui, sans-serif; color: var(--slate-900); }
        
        /* Header */
        .header-section { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
        .page-title { font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; margin-top: 5px; }
        
        /* Barra de Filtros */
        .filter-section { background: #fff; padding: 20px; border-radius: 16px; border: 1px solid var(--slate-200); margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 200px; }
        .filter-label { font-size: 0.8rem; font-weight: 700; color: var(--slate-700); text-transform: uppercase; letter-spacing: 0.05em; }
        .filter-input, .filter-select { padding: 12px 16px; border: 1px solid var(--slate-200); border-radius: 10px; font-size: 0.9rem; background: var(--slate-50); transition: all 0.2s; color: var(--slate-900); }
        .filter-input:focus, .filter-select:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        
        /* Contenedor de la tabla (Para el efecto de carga) */
        #table-container { transition: opacity 0.3s ease; }
        .loading { opacity: 0.4; pointer-events: none; }

        /* Table Styling */
        .t-card { background: #fff; border-radius: 20px; border: 1px solid var(--slate-200); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; table-layout: auto; }
        .t-min th { background: var(--slate-50); text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid var(--slate-200); }
        .t-min tr:hover { background-color: #fcfcfd; }
        .t-min td { padding: 16px 24px; border-bottom: 1px solid var(--slate-200); vertical-align: middle; }

        /* Badges & Pills */
        .pill { padding: 5px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .st-ok { background: #dcfce7; color: #15803d; }
        .st-busy { background: #e0f2fe; color: #0369a1; }
        .st-bad { background: #fef2f2; color: #b91c1c; }
        .st-fix { background: #fffbeb; color: #b45309; }
        
        .warranty-tag { font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; background: var(--slate-100); color: var(--slate-700); font-weight: 600; }
        .expired { background: #fee2e2; color: #ef4444; }

        /* Action Buttons */
        .actions-wrapper { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--slate-200); color: var(--slate-700); transition: 0.2s; background: white; cursor: pointer;}
        .btn-view { width: auto; padding: 0 15px; font-size: 0.85rem; font-weight: 600; text-decoration: none; gap: 6px; }
        .btn-view:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
        .btn-edit:hover { background: #f0f9ff; color: #0284c7; border-color: #0284c7; }
        
        .empty-state { padding: 60px; text-align: center; color: #64748b; }
        
        .btn-clear-filters { color: #ef4444; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: none; margin-left: auto; }
        .btn-clear-filters:hover { text-decoration: underline; }

        /* Encabezado especial que solo se ve al imprimir */
        .print-only-header { display: none; text-align: center; margin-bottom: 30px; }
        .print-only-header h2 { margin: 0; font-size: 24px; color: #000; }
        .print-only-header p { margin: 5px 0 0 0; color: #555; font-size: 14px; }

        /* --- LA MAGIA PARA IMPRESIÓN (PDF o Físico) --- */
        @media print {
            @page { size: landscape; margin: 10mm; } /* Formato horizontal para tablas anchas */
            body { background: white !important; -webkit-print-color-adjust: exact; }
            
            /* Ocultar elementos interactivos y de navegación */
            nav, header, footer, aside, .filter-section, .btn-action, .actions-wrapper, .t-min th:last-child, .t-min td:last-child, .pagination {
                display: none !important;
            }

            /* Mostrar el encabezado formal */
            .print-only-header { display: block; }
            .header-section { display: none; }

            /* Ajustar contenedores al 100% de la hoja */
            .container { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
            .t-card { box-shadow: none !important; border: 1px solid #ccc !important; border-radius: 0 !important; }
            .t-min th { background: #f1f5f9 !important; color: #000 !important; }
            .t-min td, .t-min th { border-color: #ddd !important; padding: 10px !important; }
        }
    </style>

    <div class="container">
        
        <div class="print-only-header">
            <h2>Reporte de Inventario de Activos</h2>
            <p>Generado el: {{ date('d/m/Y h:i A') }}</p>
            <hr style="border: 0; border-top: 1px solid #ccc; margin-top: 15px;">
        </div>

        <div class="header-section">
            <div>
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em;">
                    Módulo de Inventario
                </span>
                <h1 class="page-title">Almacén de Activos</h1>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="window.print()" class="btn-action btn-view shadow-sm" title="Imprimir o guardar como PDF">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir Reporte
                </button>

                <a href="{{ route('inventario.compras.create') }}" class="btn-action btn-view shadow-sm" style="background: var(--primary); color: white; border: none;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Registrar Compra
                </a>
            </div>
        </div>

        <form id="live-search-form" action="{{ route('inventario.activos.index') }}" method="GET" class="filter-section">
            
            <div class="filter-group" style="flex: 2;">
                <label class="filter-label">Búsqueda General</label>
                <input type="text" name="search" class="filter-input" placeholder="Buscar placa, nombre o responsable..." value="{{ request('search') }}" autocomplete="off">
            </div>

            <div class="filter-group">
                <label class="filter-label">Estado</label>
                <select name="estado_id" class="filter-select">
                    <option value="">Todos los estados</option>
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
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Categoría / Subgrupo</label>
                <select name="subgrupo_id" class="filter-select">
                    <option value="">Todas las categorías</option>
                    @foreach($subgrupos as $subgrupo)
                        <option value="{{ $subgrupo->id }}" {{ request('subgrupo_id') == $subgrupo->id ? 'selected' : '' }}>
                            {{ $subgrupo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <a href="{{ route('inventario.activos.index') }}" id="btn-reset" class="btn-clear-filters" style="{{ request()->hasAny(['search', 'estado_id', 'marca_id', 'subgrupo_id']) ? 'display: block;' : 'display: none;' }}">
                ✖ Limpiar filtros
            </a>
        </form>

        <div id="table-container">
            <div class="t-card">
                <table class="t-min">
                    <thead>
                        <tr>
                            <th>Activo / Clasificación</th>
                            <th>Identificación & Serial</th>
                            <th>Ubicación</th>
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
                                <div style="font-weight: 700; color: var(--slate-900);">{{ $activo->nombre }}</div>
                                <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 4px;">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span style="font-size: 0.7rem; color: #475569; background: #f1f5f9; padding: 1px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                            {{ $activo->marca->nombre ?? 'Sin Marca' }}
                                        </span>
                                        <span style="font-size: 0.7rem; color: #64748b; font-weight: 500;">
                                            {{ $activo->subgrupo->nombre ?? '' }}
                                        </span>
                                    </div>
                                    @if($activo->referencia)
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Ref: {{ $activo->referencia->referencia ?? 'N/A' }}</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div style="font-family: 'Mono', monospace; font-weight: 700; color: var(--primary); font-size: 0.95rem;">
                                    {{ $activo->codigo_activo }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                    <span style="font-weight: 500;">S/N:</span> {{ $activo->serial ?? 'No registra' }}
                                </div>
                            </td>

                            <td>
                                <div style="font-size: 0.85rem; font-weight: 500;">
                                    {{ $activo->municipio->nombre ?? 'No asignado' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">
                                    {{ $activo->referencia->bodega->nombre ?? 'Sin bodega' }}
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
                                    {{ $activo->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                @if($activo->fecha_fin_garantia)
                                    <div class="warranty-tag {{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->isPast() ? 'expired' : '' }}">
                                        Exp: {{ \Carbon\Carbon::parse($activo->fecha_fin_garantia)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span style="color: #cbd5e1; font-size: 0.75rem;">Sin registro</span>
                                @endif
                            </td>

                            <td>
                                @if($activo->usuarioAsignado)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 800;">
                                            {{ strtoupper(substr($activo->usuarioAsignado->name, 0, 2)) }}
                                        </div>
                                        <div style="font-size: 0.85rem; font-weight: 500;">{{ $activo->usuarioAsignado->name }}</div>
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">-- Disponible --</span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <div class="actions-wrapper">
                                    <a href="{{ route('inventario.activos.show', $activo->id) }}" class="btn-action btn-view" title="Ver Hoja de Vida">
                                        <span>Ver Detalle</span>
                                    </a>
                                    <a href="{{ route('inventario.activos.edit', $activo->id) }}" class="btn-action btn-edit" title="Editar Activo">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" style="margin-bottom: 10px; margin-left: auto; margin-right: auto;"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <h3>No se encontraron resultados</h3>
                                    <p>Intenta cambiar los filtros o buscar a otra persona.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($activos->hasPages())
                    <div style="padding: 20px; border-top: 1px solid var(--slate-200); background: var(--slate-50);" class="pagination">
                        {{ $activos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('live-search-form');
            const tableContainer = document.getElementById('table-container');
            const btnReset = document.getElementById('btn-reset');
            let timeout = null;

            function fetchResults() {
                const url = new URL(form.action);
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);
                url.search = searchParams.toString();

                let hasFilters = Array.from(formData.values()).some(val => val !== '');
                btnReset.style.display = hasFilters ? 'block' : 'none';

                tableContainer.classList.add('loading');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    tableContainer.innerHTML = doc.getElementById('table-container').innerHTML;
                    tableContainer.classList.remove('loading');
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error("Error cargando los filtros:", error);
                    tableContainer.classList.remove('loading');
                });
            }

            form.querySelector('input[name="search"]').addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 400); 
            });

            form.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', fetchResults);
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchResults();
            });
        });
    </script>
</x-base-layout>