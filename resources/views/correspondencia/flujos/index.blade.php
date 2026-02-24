<x-base-layout>
    <style>
        :root {
            /* Paleta Pastel Minimalista */
            --bg-app: #f9fafb; --white: #ffffff; --text-dark: #374151; --text-light: #9ca3af; --border-light: #f3f4f6;
            --pastel-blue: #eff6ff; --text-blue: #3b82f6;
            --pastel-indigo: #e0e7ff; --text-indigo: #4f46e5;
        }

        body { background-color: var(--bg-app); font-family: 'Inter', sans-serif; }
        .app-container { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

        /* --- UI COMPONENTS --- */
        .card-clean { background: var(--white); border-radius: 12px; border: 1px solid var(--border-light); box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .search-pill { background: var(--bg-app); border: 1px solid transparent; border-radius: 50px; padding: 0.5rem 1.2rem; font-size: 0.9rem; transition: all 0.2s; color: var(--text-dark); }
        .search-pill:focus { background: var(--white); border-color: var(--pastel-indigo); box-shadow: 0 0 0 3px var(--pastel-indigo); outline: none; }
        
        /* Botones y Badges */
        .btn-primary-neo { background-color: var(--text-indigo); color: white !important; padding: 0.6rem 1.2rem; border-radius: 10px; text-decoration: none; font-weight: 500; display: inline-block; transition: all 0.2s; border: none; }
        .btn-primary-neo:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
        
        /* Scroll Horizontal de Categorías */
        .ux-category-scroll {
            display: flex; flex-wrap: nowrap; gap: 0.5rem; overflow-x: auto; padding-bottom: 8px;
            scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; -webkit-overflow-scrolling: touch;
        }
        .ux-category-scroll::-webkit-scrollbar { height: 6px; }
        .ux-category-scroll::-webkit-scrollbar-track { background: transparent; }
        .ux-category-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        .ux-category-scroll:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }

        .ux-chip { white-space: nowrap; padding: 8px 16px; background: var(--white); border: 1px solid var(--border-light); border-radius: 50px; font-size: 0.85rem; font-weight: 600; color: var(--text-light); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; user-select: none; }
        .ux-chip:hover { border-color: #c7d2fe; color: var(--text-indigo); }
        
        /* Chip Activo */
        .ux-chip.active { background: rgba(255, 255, 255, 0.85); color: var(--text-indigo); border-color: #a5b4fc; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.08); font-weight: 700; }
        .ux-chip.active .badge-chip { background: var(--pastel-indigo); color: var(--text-indigo); }
        .badge-chip { background: #f1f5f9; color: var(--text-dark); padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; }

        /* Tarjetas de Flujos (Grid) */
        .hover-up { transition: all 0.25s ease; }
        .hover-up:hover { transform: translateY(-5px); box-shadow: 0 0.8rem 2rem rgba(79, 70, 229, 0.1) !important; border: 1px solid var(--text-indigo) !important; }
        .stretched-link::after { z-index: 1; }
        
        .bg-soft-indigo { background-color: var(--pastel-indigo); }
        .text-indigo { color: var(--text-indigo); }
        .bg-soft-success { background-color: #ecfdf5; }
        .bg-soft-secondary { background-color: #f8f9fa; }
        .icon-box { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .avatar-xs { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }

        /* --- LOADING OVERLAY --- */
        .loading-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(249, 250, 251, 0.7); /* Mismo color de fondo pero semi-transparente */
            z-index: 10; backdrop-filter: blur(2px); border-radius: 15px;
        }
    </style>

    <div class="app-container">
        <header class="main-header d-flex justify-content-between align-items-center mb-4">
            <div class="header-content">
                <h1 class="page-title fw-bold m-0" style="font-size: 1.5rem; color: #111827;">Flujos de Trabajo</h1>
                <p class="page-subtitle text-muted m-0 mt-1">Defina las rutas y responsables de los procesos documentales.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('correspondencia.flujos.create') }}" class="btn-primary-neo shadow-sm">
                    <i class="bi bi-diagram-3-fill me-1"></i> Nuevo Flujo
                </a>
            </div>
        </header>

        {{-- ALERTAS DE SISTEMA --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="background-color: #ecfdf5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- FILTROS INTUITIVOS --}}
        <div class="card-clean p-3 mb-4">
            <form action="{{ route('correspondencia.flujos.index') }}" method="GET" id="filter-form-flujos">
                <input type="hidden" name="area_id" id="input-area" value="{{ request('area_id') }}">

                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <div class="position-relative flex-grow-1" style="min-width: 250px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" name="search" class="form-control search-pill ps-5 w-100" placeholder="Buscar flujo por nombre o descripción..." value="{{ request('search') }}">
                    </div>
                    <select name="estado" class="form-select search-pill border-0" style="width: auto; cursor: pointer; background-color: var(--bg-app); min-width: 180px;">
                        <option value="" {{ empty(request('estado')) ? 'selected' : '' }}>Todos los estados</option>
                        <option value="en_uso" {{ request('estado') == 'en_uso' ? 'selected' : '' }}>En Uso (Activos)</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponibles (Sin uso)</option>
                    </select>
                </div>

                {{-- SCROLL HORIZONTAL DE CHIPS (ÁREAS) --}}
                <div id="chips-container">
                    <div class="ux-category-scroll">
                        <span class="ux-chip {{ empty(request('area_id')) ? 'active' : '' }}" onclick="applyAreaFilter('')">
                            Todas las Áreas <span class="badge-chip">{{ $total_flujos }}</span>
                        </span>
                        @foreach($areas_disponibles as $area)
                            <span class="ux-chip {{ request('area_id') == $area->id ? 'active' : '' }}" onclick="applyAreaFilter('{{ $area->id }}')">
                                {{ $area->nombre }} 
                                @if($area->flujos_count > 0)
                                    <span class="badge-chip">{{ $area->flujos_count }}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- CONTENEDOR DE LA CUADRÍCULA (GRID) CON CARGA AJAX --}}
        <div id="grid-container" class="position-relative" style="min-height: 300px;">
            
            {{-- OVERLAY DE CARGA (Ruedita girando) --}}
            <div id="grid-loading-overlay" class="loading-overlay d-none d-flex align-items-center justify-content-center">
                <div class="spinner-border text-indigo" role="status" style="width: 3rem; height: 3rem; border-width: 0.25em;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <div class="row g-4">
                @forelse($flujos as $flujo)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-up position-relative" style="border-radius: 15px; cursor: pointer;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="icon-box bg-soft-indigo text-indigo">
                                        <i class="bi bi-signpost-split-fill fs-4"></i>
                                    </div>
                                    
                                    {{-- Menú de Opciones (Z-index superior) --}}
                                    <div class="dropdown" style="z-index: 10;">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('correspondencia.flujos.edit', $flujo) }}">
                                                    <i class="bi bi-pencil me-2 text-indigo"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                @if($flujo->correspondencias_count > 0)
                                                    <button class="dropdown-item text-muted opacity-50" disabled title="No se puede eliminar un flujo con radicados asociados">
                                                        <i class="bi bi-trash me-2"></i> Eliminar (En uso)
                                                    </button>
                                                @else
                                                    <form action="{{ route('correspondencia.flujos.destroy', $flujo) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este flujo?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                {{-- ENLACE PRINCIPAL: Clickea toda la tarjeta --}}
                                <a href="{{ route('correspondencia.flujos.show', $flujo) }}" class="stretched-link text-decoration-none text-dark">
                                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $flujo->nombre }}</h5>
                                </a>
                                
                                <div class="mb-2 position-relative" style="z-index: 2;">
                                    <span class="badge bg-light text-indigo border-0 px-2 py-1" style="font-weight: 600;">
                                        <i class="bi bi-building me-1"></i> {{ $flujo->area->nombre ?? 'Sin Área' }}
                                    </span>
                                </div>

                                <p class="text-muted small mb-3 text-truncate-2" style="min-height: 2.8em; font-size: 0.85rem;">
                                    {{ $flujo->detalle ?? 'Sin descripción detallada.' }}
                                </p>

                                {{-- INDICADOR DE ESTADO --}}
                                <div class="mb-4">
                                    @if($flujo->correspondencias_count > 0)
                                        <span class="badge rounded-pill bg-soft-secondary text-muted border px-3 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                            <i class="bi bi-lock-fill me-1"></i> EN USO: {{ $flujo->correspondencias_count }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-soft-success text-success border border-success-subtle px-3 py-1" style="font-size: 0.7rem; font-weight: 700;">
                                            <i class="bi bi-shield-check me-1"></i> DISPONIBLE
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="d-flex align-items-center pt-3 border-top">
                                    <div class="avatar-xs bg-indigo text-white me-2 shadow-sm" style="background-color: var(--text-indigo);">
                                        {{ strtoupper(substr($flujo->usuario->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">RESPONSABLE</small>
                                        <span class="small fw-bold text-dark">{{ $flujo->usuario->name ?? 'No asignado' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 my-5">
                        <div class="icon-box bg-light mx-auto mb-3 text-muted" style="width: 80px; height: 80px;">
                            <i class="bi bi-diagram-3 fs-1 opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No se encontraron flujos</h5>
                        <p class="text-muted">Intenta cambiar los filtros de búsqueda o crea un flujo nuevo.</p>
                        <a href="{{ route('correspondencia.flujos.create') }}" class="btn btn-primary rounded-pill px-4 mt-2 shadow-sm">
                            <i class="bi bi-plus"></i> Crear Flujo
                        </a>
                    </div>
                @endforelse
            </div>
            
            @if(isset($flujos) && method_exists($flujos, 'hasPages') && $flujos->hasPages())
                <div class="mt-4 d-flex justify-content-center pagination-wrapper">
                    {{ $flujos->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- SCRIPT PARA FILTRADO AJAX Y SCROLL --}}
    <script>
        let debounceTimer;

        document.addEventListener('DOMContentLoaded', () => {
            initRealTimeFilters();
            initDragToScroll();
        });

        function initRealTimeFilters() {
            const form = document.getElementById('filter-form-flujos');
            const searchInput = form.querySelector('input[name="search"]');
            const estadoSelect = form.querySelector('select[name="estado"]');

            // Búsqueda por texto (con retraso de 350ms para no saturar el servidor)
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => triggerAjaxUpdate(), 350);
            });

            // Selector de estado
            estadoSelect.addEventListener('change', () => triggerAjaxUpdate());

            // Paginación por AJAX
            document.addEventListener('click', function(e) {
                const paginationLink = e.target.closest('.pagination-wrapper a');
                if (paginationLink) {
                    e.preventDefault();
                    triggerAjaxUpdate(paginationLink.href);
                }
            });
        }

        function applyAreaFilter(idArea) {
            document.getElementById('input-area').value = idArea;
            triggerAjaxUpdate();
        }

        async function triggerAjaxUpdate(customUrl = null) {
            const form = document.getElementById('filter-form-flujos');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = customUrl || `${form.action}?${params.toString()}`;

            // Guardar el scroll actual de los chips para que no salte al inicio al recargar
            const scrollContainer = document.querySelector('.ux-category-scroll');
            const currentScroll = scrollContainer ? scrollContainer.scrollLeft : 0;

            // Mostrar el Overlay de carga (La ruedita)
            const overlay = document.getElementById('grid-loading-overlay');
            if(overlay) overlay.classList.remove('d-none');

            try {
                // Hacer la petición
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();

                // Convertir el texto HTML en un DOM navegable
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extraer y reemplazar solo las partes dinámicas (Los chips y la cuadrícula)
                document.getElementById('chips-container').innerHTML = doc.getElementById('chips-container').innerHTML;
                document.getElementById('grid-container').innerHTML = doc.getElementById('grid-container').innerHTML;

                // Restaurar la posición del scroll y reactivar el Drag-to-Scroll
                const newScrollContainer = document.querySelector('.ux-category-scroll');
                if(newScrollContainer) {
                    newScrollContainer.scrollLeft = currentScroll;
                    initDragToScroll(); 
                }

                // Actualizar la URL de la barra del navegador silenciosamente
                window.history.pushState({}, '', url);

            } catch (error) {
                console.error("Error al filtrar los datos:", error);
            } finally {
                // Asegurarse de quitar la ruedita siempre, haya éxito o error
                const newOverlay = document.getElementById('grid-loading-overlay');
                if(newOverlay) newOverlay.classList.add('d-none');
            }
        }

        // Sistema para arrastrar los chips con el mouse/dedo
        function initDragToScroll() {
            const slider = document.querySelector('.ux-category-scroll');
            let isDown = false;
            let startX, scrollLeft;

            if(!slider) return;

            slider.style.cursor = 'grab';
            
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.style.cursor = 'grabbing';
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => { isDown = false; slider.style.cursor = 'grab'; });
            slider.addEventListener('mouseup', () => { isDown = false; slider.style.cursor = 'grab'; });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const walk = (e.pageX - slider.offsetLeft - startX) * 2;
                slider.scrollLeft = scrollLeft - walk;
            });
        }
    </script>
</x-base-layout>