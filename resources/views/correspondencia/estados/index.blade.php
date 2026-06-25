<x-base-layout>
    <div class="app-container py-4">
        
        {{-- HEADER PRINCIPAL --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3 text-primary"></i> Gestión Estructurada de Estados
                </h1>
                <p class="text-muted small mb-0">Administración del ciclo de vida, trazabilidad y control operativo de la correspondencia.</p>
            </div>
            <a href="{{ route('correspondencia.estados.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift d-flex align-items-center gap-2" style="background-color: #1976d2; border-color: #1976d2;">
                <i class="fas fa-plus"></i> <span class="fw-bold small text-uppercase">Nuevo Estado</span>
            </a>
        </div>

        {{-- RESUMEN DASHBOARD SUPERIOR --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Total Estados --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white position-relative overflow-hidden group-hover-lift">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Estados Creados</span>
                            <h3 class="fw-extrabold text-dark m-0">{{ is_object($estados) && method_exists($estados, 'total') ? $estados->total() : count($estados) }}</h3>
                        </div>
                        <div class="rounded-4 p-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #e3f2fd; color: #1565c0;">
                            <i class="fas fa-tags fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Estados Activos --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Monitoreo Activo</span>
                            <h3 class="fw-extrabold text-success m-0">
                                {{ $estados->where('activo', 1)->count() }}
                            </h3>
                        </div>
                        <div class="rounded-4 p-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #e8f5e9; color: #2e7d32;">
                            <i class="fas fa-toggle-on fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Correspondencias Fluyendo --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Documentos Totales</span>
                            <h3 class="fw-extrabold text-dark m-0">{{ $estados->sum('correspondencias_count') }}</h3>
                        </div>
                        <div class="rounded-4 p-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #e0f7fa; color: #006064;">
                            <i class="fas fa-file-alt fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Cobertura Operativa --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Áreas Asignadas</span>
                            <h3 class="fw-extrabold text-warning m-0">{{ $estados->pluck('id_area')->unique()->count() }}</h3>
                        </div>
                        <div class="rounded-4 p-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #fffde7; color: #f9a825;">
                            <i class="fas fa-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENEDOR CENTRAL: FILTROS Y TABLA INTERACTIVA --}}
        <div class="card border-0 shadow-sm rounded-4 p-0 bg-white overflow-hidden">
            
            {{-- BARRA DE FILTRADO ULTRA UX --}}
            <div class="p-3 bg-light border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="flex-grow-1 max-w-md">
                    <div class="input-group input-group-merge rounded-pill shadow-sm">
                        <span class="input-group-text bg-white border-end-0 ps-3 text-muted">
                            <i class="fas fa-search small"></i>
                        </span>
                        <input type="text" id="buscadorEstados" class="form-control border-start-0 ps-2" placeholder="Buscar por nombre, descripción o área..." style="border-radius: 0 50px 50px 0;">
                    </div>
                </div>
                
                {{-- Select Dinámico para Filtrar por Áreas Rápidamente --}}
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label text-muted small fw-bold mb-0 text-nowrap"><i class="fas fa-filter me-1"></i> Filtrar Área:</label>
                    <select id="filtroAreaSelect" class="form-select form-select-sm rounded-pill border shadow-sm px-3" style="width: 220px; cursor: pointer;">
                        <option value="TODAS">Mostrar Todas las Áreas</option>
                        @foreach($estados->pluck('area')->unique('id') as $areaFiltro)
                            @if($areaFiltro)
                                <option value="area-{{ $areaFiltro->id }}">{{ $areaFiltro->nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- NAV TABS PARA UX DE SEPARACIÓN POR ÁREAS --}}
            <div class="bg-white px-3 pt-2 border-bottom">
                <ul class="nav nav-tabs border-0 gap-1" id="areaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small text-uppercase rounded-top-4 border-0 px-4 position-relative btn-tab-filtro" data-area-target="TODAS" type="button" style="padding-bottom: 12px;">
                            Todos los Sectores
                        </button>
                    </li>
                    @foreach($estados->pluck('area')->unique('id') as $tabArea)
                        @if($tabArea)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-muted fw-bold small text-uppercase rounded-top-4 border-0 px-4 btn-tab-filtro" data-area-target="area-{{ $tabArea->id }}" type="button" style="padding-bottom: 12px;">
                                    {{ $tabArea->nombre }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            {{-- TABLA DE DATOS --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaPrincipalEstados">
                    <thead style="background-color: #f8fafc;">
                        <tr class="text-uppercase text-muted small border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th class="px-4 py-3" style="width: 250px;">Nombre del Estado</th>
                            <th class="py-3" style="width: 220px;">Área Asignada</th>
                            <th class="py-3">Descripción</th>
                            <th class="text-center py-3" style="width: 140px;">Carga Operativa</th>
                            <th class="text-center py-3" style="width: 120px;">Estado</th>
                            <th class="text-end px-4 py-3" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($estados as $estado)
                        {{-- Se inyecta el identificador de área en el data-attribute para control UX --}}
                        <tr class="fila-estado-registro transition-all" data-area-id="area-{{ $estado->id_area ?? 'sin-area' }}">
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-soft-primary px-3 py-2 rounded-pill fw-bold col-nombre-estado" style="background-color: #eef2ff; color: #1976d2; font-size: 0.8rem;">
                                        {{ $estado->nombre }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($estado->area)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary shadow-sm" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <span class="fw-semibold text-dark small col-area-estado">{{ $estado->area->nombre }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small italic"><i class="fas fa-exclamation-circle me-1 text-warning"></i> Sin Área Asignada</span>
                                @endif
                            </td>
                            <td>
                                <p class="text-muted small mb-0 text-truncate col-desc-estado" style="max-width: 320px;" data-bs-toggle="tooltip" title="{{ $estado->descripcion }}">
                                    {{ $estado->descripcion ?? 'Sin descripción registrada en el sistema.' }}
                                </p>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge rounded-pill fw-bold mb-1 shadow-sm {{ $estado->correspondencias_count > 0 ? 'bg-dark text-white' : 'bg-light text-muted border' }}" style="font-size: 0.75rem;">
                                        {{ $estado->correspondencias_count }} doc(s)
                                    </span>
                                    {{-- Barra de Progreso UX visual interactiva --}}
                                    <div class="progress w-75 bg-light" style="height: 4px; border-radius: 10px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(($estado->correspondencias_count * 10), 100) }}%; border-radius: 10px;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($estado->activo)
                                    <span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: #e8f5e9; color: #2e7d32;">
                                        <span class="dot-blink" style="width: 6px; height: 6px; background-color: #2e7d32; border-radius: 50%; display: inline-block;"></span> Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted px-2.5 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: #f1f3f5; color: #6c757d;">
                                        <span style="width: 6px; height: 6px; background-color: #6c757d; border-radius: 50%; display: inline-block;"></span> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <a href="{{ route('correspondencia.estados.show', $estado) }}" class="btn btn-sm btn-white border-end hover-bg-light" data-bs-toggle="tooltip" title="Ver Detalles">
                                        <i class="fas fa-eye text-secondary"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.estados.edit', $estado) }}" class="btn btn-sm btn-white hover-bg-light" data-bs-toggle="tooltip" title="Editar Parámetros">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="sinRegistrosFilaOriginal">
                            <td colspan="6" class="text-center py-5 text-muted bg-light bg-opacity-25">
                                <div class="p-4">
                                    <i class="fas fa-folder-open fs-1 text-black-50 mb-3"></i>
                                    <h6 class="fw-bold">No hay estados registrados</h6>
                                    <p class="small text-muted mb-0">Crea un nuevo estado para iniciar el ciclo de vida documental.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        
                        {{-- Fila comodín oculta en caso de que los filtros dejen vacía la tabla --}}
                        <tr id="filaBusquedaVaciaUX" style="display: none;">
                            <td colspan="6" class="text-center py-5 text-muted bg-light bg-opacity-25">
                                <div class="p-4">
                                    <i class="fas fa-search fs-2 text-warning mb-2"></i>
                                    <h6 class="fw-bold">Ningún registro coincide con los filtros aplicados</h6>
                                    <p class="small text-muted mb-0">Intenta modificando el término del buscador o cambiando de pestaña de área.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- FOOTER CON PAGINACIÓN --}}
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                @if(is_object($estados) && method_exists($estados, 'links'))
                    <div id="contenedorPaginacionLaravel">
                        {{ $estados->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ESTILOS EXCLUSIVOS ULTRA UX INTERACTIVOS --}}
    @push('styles')
    <style>
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
        .transition-all { transition: all 0.25s ease-in-out; }
        .hover-bg-light:hover { background-color: #f8fafc; }
        
        /* Efecto Nav Link Active personalizado moderno */
        #areaTabs .nav-link { color: #64748b; background: transparent; transition: all 0.2s ease; position: relative; }
        #areaTabs .nav-link:hover { color: #1e293b; background-color: #f1f5f9; }
        #areaTabs .nav-link.active { color: #1976d2 !important; background-color: #ffffff !important; border-bottom: 2px solid #1976d2 !important; }
        
        /* Animación para el punto del estado activo */
        @keyframes blink { 0% { opacity: 0.3; } 50% { opacity: 1; } 100% { opacity: 0.3; } }
        .dot-blink { animation: blink 1.4s infinite ease-in-out; }
    </style>
    @endpush

    {{-- SCRIPTS DE INTERACCIÓN DINÁMICA (FRONT-END ENGINE) --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar Tooltips globales
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            // CAPTURA DE COMPONENTES DOM
            const buscador = document.getElementById('buscadorEstados');
            const filtroSelect = document.getElementById('filtroAreaSelect');
            const pestañasArea = document.querySelectorAll('.btn-tab-filtro');
            const filas = document.querySelectorAll('.fila-estado-registro');
            const filaVacia = document.getElementById('filaBusquedaVaciaUX');
            const contenedorPaginacion = document.getElementById('contenedorPaginacionLaravel');

            let areaActual = 'TODAS';

            // 1. ENGINE DE FILTRADO COMBINADO (Buscador + Pestañas de Áreas + Select)
            function ejecutarFiltradoGlobal() {
                let termino = buscador.value.toLowerCase().trim();
                let filasVisibles = 0;

                filas.forEach(fila => {
                    let idAreaFila = fila.getAttribute('data-area-id');
                    let nombre = fila.querySelector('.col-nombre-estado')?.textContent.toLowerCase() || '';
                    let area = fila.querySelector('.col-area-estado')?.textContent.toLowerCase() || '';
                    let desc = fila.querySelector('.col-desc-estado')?.textContent.toLowerCase() || '';

                    // Evaluar Criterio 1: Pestañas / Select de Áreas
                    let coincideArea = (areaActual === 'TODAS' || idAreaFila === areaActual);
                    
                    // Evaluar Criterio 2: Coincidencia de Texto en Busador
                    let coincideTexto = (nombre.includes(termino) || area.includes(termino) || desc.includes(termino));

                    if (coincideArea && coincideTexto) {
                        fila.style.display = '';
                        filasVisibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                // Manejo de Interfaz Vacía (UX)
                if (filasVisibles === 0 && filas.length > 0) {
                    filaVacia.style.display = '';
                } else {
                    filaVacia.style.display = 'none';
                }

                // Ocultar paginación si se está realizando una búsqueda activa para no confundir al usuario
                if (termino !== '' || areaActual !== 'TODAS') {
                    if(contenedorPaginacion) contenedorPaginacion.style.opacity = '0.3';
                } else {
                    if(contenedorPaginacion) contenedorPaginacion.style.opacity = '1';
                }
            }

            // 2. DISPARADORES EVENT LISTENERS
            
            // Listener del input de búsqueda
            buscador.addEventListener('input', ejecutarFiltradoGlobal);

            // Listener de la selección del Dropdown de Áreas
            filtroSelect.addEventListener('change', function() {
                areaActual = this.value;
                
                // Sincronizar el estado visual de las pestañas Nav-Tabs superiores
                pestañasArea.forEach(tab => {
                    if(tab.getAttribute('data-area-target') === areaActual) {
                        tab.classList.add('active');
                    } else {
                        tab.classList.remove('active');
                    }
                });
                ejecutarFiltradoGlobal();
            });

            // Listener de clicks en las Pestañas Superiores (Tablas dinámicas por área)
            pestañasArea.forEach(tab => {
                tab.addEventListener('click', function() {
                    pestañasArea.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    areaActual = this.getAttribute('data-area-target');
                    filtroSelect.value = areaActual; // Sincroniza el Select alterno
                    
                    ejecutarFiltradoGlobal();
                });
            });
        });
    </script>
    @endpush
</x-base-layout>