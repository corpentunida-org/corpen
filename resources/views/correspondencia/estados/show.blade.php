<x-base-layout>
    <div class="app-container py-4">
        
        {{-- ENLACE DE RETORNO --}}
        <div class="mb-4">
            <a href="{{ route('correspondencia.estados.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 hover-opacity-75 transition-all">
                <i class="fas fa-arrow-left"></i> Regresar al listado de estados
            </a>
        </div>

        {{-- FILA 1: MINI DASHBOARD DE MÉTRICAS (KPI CARDS) --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Total Documentos --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-analitica">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Carga Operativa Total</span>
                            <h3 class="fw-extrabold text-dark m-0">{{ $todasCorrespondencias->count() }} <span class="fs-6 text-muted fw-normal">documentos</span></h3>
                        </div>
                        <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #e3f2fd; color: #1565c0;">
                            <i class="fas fa-folder-open fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Área de Control --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-analitica">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Sector Responsable</span>
                            <h5 class="fw-bold text-dark m-0 text-truncate" style="max-width: 220px;">
                                {{ $estado->area->nombre ?? 'Sin Área Asignada' }}
                            </h5>
                        </div>
                        <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: #f1f5f9; color: #475569;">
                            <i class="fas fa-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Disponibilidad del Flujo --}}
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-analitica">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Estado de Disponibilidad</span>
                            <div class="mt-1">
                                @if($estado->activo)
                                    <span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: #e8f5e9; color: #2e7d32;">
                                        <span class="dot-blink" style="width: 6px; height: 6px; background-color: #2e7d32; border-radius: 50%; display: inline-block;"></span> Operativo / Visible
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted px-2.5 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5" style="background-color: #f1f3f5; color: #6c757d;">
                                        <span style="width: 6px; height: 6px; background-color: #6c757d; border-radius: 50%; display: inline-block;"></span> Restringido / Oculto
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="rounded-4 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: {{ $estado->activo ? '#e8f5e9' : '#f1f3f5' }}; color: {{ $estado->activo ? '#2e7d32' : '#6c757d' }};">
                            <i class="fas fa-toggle-on fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILA 2: PANEL CENTRAL DIVIDIDO (INFO DEL ESTADO VS GRÁFICO INTERACTIVO) --}}
        <div class="row g-4 mb-4">
            {{-- Columna Izquierda: Parámetros e Información Base --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle text-primary"></i> Identificación Base
                        </h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase d-block fw-bold mb-1">Nombre Identificador</span>
                                <span class="badge px-3 py-2 rounded-pill fw-bold fs-6" style="background-color: #eef2ff; color: #1976d2;">
                                    {{ $estado->nombre }}
                                </span>
                            </div>
                            <div class="mb-4">
                                <span class="text-muted small text-uppercase d-block fw-bold mb-1">Descripción de Funciones</span>
                                <p class="text-muted small mb-0 lh-base" style="text-align: justify;">
                                    {{ $estado->descripcion ?? 'Este estado cumple una función de control operativo estándar dentro del ciclo de vida documental y no cuenta con especificaciones adicionales registradas.' }}
                                </p>
                            </div>
                        </div>
                        <div class="border-top pt-3 mt-2">
                            <a href="{{ route('correspondencia.estados.edit', $estado) }}" class="btn btn-sm btn-light border rounded-pill w-100 py-2 shadow-sm fw-bold text-secondary hover-lift d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-edit text-primary"></i> Modificar Parámetros
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Gráfico de Tendencia Histórica Avanzado --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                <i class="fas fa-chart-area text-primary"></i> Flujo Histórico de Ingreso
                            </h5>
                            <p class="text-muted small mb-0 p-0 m-0">Comportamiento volumétrico de los documentos radicados bajo este estado.</p>
                        </div>
                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1.5 small"><i class="far fa-calendar-alt me-1"></i> Mensual</span>
                    </div>
                    <div class="card-body p-3 p-sm-4">
                        
                        {{-- CONTENEDOR DEL GRÁFICO CON SPINNER DE ESPERA ACTIVA --}}
                        <div id="graficoHistoricoEstado" style="min-height: 240px;" class="d-flex align-items-center justify-content-center bg-light bg-opacity-50 rounded-3">
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                <p class="mb-0 text-secondary small fw-medium">Cargando métricas de rendimiento...</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- FILA 3: LISTADO DE DOCUMENTACIÓN ASOCIADA --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="p-4 bg-light bg-opacity-50 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="fw-bold text-dark text-uppercase small m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list text-secondary"></i> Documentos Activos en este Ciclo de Trazabilidad
                </h6>
                <span class="badge bg-white text-dark border px-3 py-1.5 rounded-pill small fw-semibold shadow-sm">
                    Mostrando {{ $correspondencias->count() }} de {{ $correspondencias->total() }} registros
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr class="text-uppercase text-muted small border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th class="px-4 py-3" style="width: 160px;">Radicado / ID</th>
                            <th class="py-3">Asunto del Documento</th>
                            <th class="py-3" style="width: 250px;">Remitente</th>
                            <th class="text-center py-3" style="width: 160px;">Fecha Registro</th>
                            <th class="text-end px-4 py-3" style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($correspondencias as $documento)
                            <tr class="transition-all hover-bg-light">
                                <td class="px-4">
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        #{{ $documento->id_radicado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small text-truncate" style="max-width: 320px;" data-bs-toggle="tooltip" title="{{ $documento->asunto }}">
                                        {{ $documento->asunto ?? 'Sin asunto registrado' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small fw-semibold text-dark text-truncate d-block" style="max-width: 230px;">
                                        @if(is_object($documento->remitente))
                                            {{ $documento->remitente->nom_ter ?? 'N/D' }}
                                        @elseif(is_array($documento->remitente))
                                            {{ $documento->remitente['nom_ter'] ?? 'N/D' }}
                                        @else
                                            @php 
                                                $remitenteData = json_decode($documento->remitente, true); 
                                            @endphp
                                            {{ $remitenteData['nom_ter'] ?? $documento->remitente ?? 'N/D' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted small">
                                        {{ $documento->fec_ing ? \Carbon\Carbon::parse($documento->fec_ing)->format('d/m/Y g:i A') : ($documento->created_at ? $documento->created_at->format('d/m/Y g:i A') : 'N/D') }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('correspondencia.correspondencias.show', ['correspondencia' => $documento->id_radicado]) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm hover-lift d-inline-flex align-items-center gap-1.5 py-1.5" style="background-color: #1976d2; border-color: #1976d2; font-size: 0.75rem;">
                                        <i class="fas fa-external-link-alt"></i> Ver Flujo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted bg-white">
                                    <div class="p-4">
                                        <i class="fas fa-inbox fs-2 text-black-50 mb-3"></i>
                                        <h6 class="fw-bold m-0">No existen documentos en tránsito</h6>
                                        <p class="small text-muted mb-0 mt-1">Ningún elemento del flujo de correspondencia se encuentra actualmente bajo este estado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER CON PAGINACIÓN CONTROLADA --}}
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                @if($correspondencias->hasPages())
                    <div id="contenedorPaginacionDinamica">
                        {{ $correspondencias->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ESTILOS EXCLUSIVOS --}}
    @push('styles')
    <style>
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-opacity-75:hover { opacity: 0.75; }
        .hover-lift { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.06) !important; }
        .hover-bg-light:hover { background-color: #f8fafc; }
        .fw-extrabold { font-weight: 800; }
        .card-analitica:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important; }
        @keyframes blink { 0% { opacity: 0.3; } 50% { opacity: 1; } 100% { opacity: 0.3; } }
        .dot-blink { animation: blink 1.4s infinite ease-in-out; }
    </style>
    @endpush

    {{-- SCRIPTS SIN BINDING RESTRICTIVO (INMUNE A FALLOS DEL COMPILADOR DEL LAYOUT BASE) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function() {
            function renderizarGraficoInmediato() {
                var contenedor = document.getElementById("graficoHistoricoEstado");
                if (!contenedor) return;

                // Mapeo seguro utilizando directivas estrictas de Blade
                var ejeX = @json($mesesEjeX ?? []);
                var ejeY = @json($valoresEjeY ?? []);

                // Forzar conversión estricta de strings numéricos a números enteros enteros
                ejeY = ejeY.map(function(item) { return parseInt(item, 10) || 0; });

                // Manejo de contingencia si los parámetros analíticos llegan vacíos
                if (!ejeY || ejeY.length === 0) {
                    ejeY = [0];
                    ejeX = ['Sin histórico'];
                }

                var opciones = {
                    chart: {
                        type: 'area',
                        height: 240,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        sparkline: { enabled: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 600 }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3, colors: ['#1976d2'] },
                    series: [{
                        name: 'Documentos Ingresados',
                        data: ejeY
                    }],
                    xaxis: {
                        categories: ejeX,
                        labels: { style: { colors: '#64748b', fontSize: '11px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#64748b', fontSize: '11px' },
                            formatter: function (val) { return Math.floor(val); }
                        },
                        min: 0,
                        forceNiceScale: true
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.01,
                            stops: [0, 95, 100],
                            colorStops: [
                                { offset: 0, color: '#1976d2', opacity: 0.35 },
                                { offset: 100, color: '#ffffff', opacity: 0 }
                            ]
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: false } },
                        yaxis: { lines: { show: true } }
                    },
                    tooltip: {
                        theme: 'light',
                        y: { formatter: function (val) { return val + " doc(s)"; } }
                    },
                    colors: ['#1976d2']
                };

                try {
                    contenedor.innerHTML = "";
                    var chart = new ApexCharts(contenedor, opciones);
                    chart.render();
                    console.log("Análisis UX - Gráfico montado correctamente.");
                } catch (error) {
                    console.error("Error al renderizar el gráfico:", error);
                    contenedor.innerHTML = "<div class='text-danger p-4 text-center small'><i class='fas fa-exclamation-circle d-block mb-2 fs-4'></i> No se pudo dibujar el flujo gráfico analítico.</div>";
                }
            }

            // Inicialización segura de Tooltips de Bootstrap
            try {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });
            } catch(e) { console.log("Aviso: Tooltips sin cargar."); }

            // Lanzamiento múltiple por ciclo de vida
            if (window.ApexCharts) {
                renderizarGraficoInmediato();
            } else {
                window.addEventListener('load', renderizarGraficoInmediato);
                document.addEventListener('DOMContentLoaded', renderizarGraficoInmediato);
            }

            // Soporte SPA para navegaciones reactivas (Livewire / Turbo)
            document.addEventListener("turbo:load", renderizarGraficoInmediato);
            document.addEventListener("livewire:navigated", renderizarGraficoInmediato);
        })();
    </script>
</x-base-layout>