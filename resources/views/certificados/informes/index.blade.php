<x-base-layout>
    {{-- ==============================================================================
         REGION 1: ESTILOS (CSS)
         ============================================================================== --}}
    <style>
        :root {
            --c-primary      : #4a90e2;
            --c-primary-soft : #e7f0ff;
            --c-success      : #2e7d32;
            --c-success-soft : #e8f5e9;
            --c-warning      : #f57f17;
            --c-warning-soft : #fff9c4;
            --c-info         : #00838f;
            --c-info-soft    : #e0f7fa;
            --c-danger       : #ef4444;
            --c-surface      : #ffffff;
            --c-bg           : #f8fafc;
            --c-border       : #e9ecef;
            --c-text         : #2c3e50;
            --c-muted        : #64748b;
        }

        .bg-pastel-primary { background-color: var(--c-primary-soft) !important; color: var(--c-primary) !important; border: none; }
        .bg-pastel-info { background-color: var(--c-info-soft) !important; color: var(--c-info) !important; border: none; }
        .bg-pastel-success { background-color: var(--c-success-soft) !important; color: var(--c-success) !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-warning { background-color: var(--c-warning-soft) !important; color: var(--c-warning) !important; border: none; }
        .bg-pastel-danger { background-color: #fee2e2 !important; color: var(--c-danger) !important; border: none; }

        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }

        .btn-reload { background-color: #ffffff; border: 1px solid #e9ecef; color: #adb5bd; transition: all 0.3s ease; }
        .btn-reload:hover { background-color: var(--c-primary-soft); color: var(--c-primary); border-color: var(--c-primary-soft); }
        .btn-reload:hover i { transform: rotate(180deg); transition: transform 0.4s ease; }
        .btn-reload i { transition: transform 0.4s ease; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .progress-minimalist { height: 6px; width: 100%; background-color: #fee2e2; border-radius: 4px; overflow: hidden; position: relative; }
        .progress-minimalist::before { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background-color: var(--c-danger); animation: progress-slide 1.5s infinite ease-in-out; border-radius: 4px; }
        @keyframes progress-slide { 0% { left: -50%; width: 30%; } 50% { width: 60%; } 100% { left: 100%; width: 30%; } }

        .sticky-sidebar { position: sticky; top: 1.5rem; max-height: calc(100vh - 3rem); display: flex; flex-direction: column; min-height: 550px; }
        .block-link { background: transparent; color: var(--c-text); transition: all 0.2s; }
        .block-link:hover:not(.active-block) { background: var(--c-primary-soft); color: var(--c-primary); }
        .block-link.active-block { background: var(--c-primary); color: #fff; box-shadow: 0 2px 4px rgba(74, 144, 226, 0.3); }
        .block-link .ico-cube { color: var(--c-muted); }
        .block-link:hover:not(.active-block) .ico-cube { color: var(--c-primary); }
        .block-link.active-block .ico-cube { color: #fff; }
        .year-toggle-btn { cursor: pointer; user-select: none; }
        .year-toggle-btn:hover .badge { background: #e2e8f0 !important; }
        .chevron-icon { transition: transform 0.3s ease; }
        .year-toggle-btn.is-open .chevron-icon { transform: rotate(180deg); }
        .month-toggle-btn.is-open .chevron-month { transform: rotate(90deg); color: var(--c-primary) !important; }
        .month-toggle-btn:hover { background-color: var(--c-bg); border-radius: 8px 8px 0 0; }
        
        .badge-hover-effect { transition: all 0.3s ease; cursor: default; display: inline-block; }
        .badge-hover-effect:hover {
            transform: translateY(-2px);
            background-color: var(--c-primary-soft) !important;
            color: var(--c-primary) !important;
            border-color: var(--c-primary-soft) !important;
            box-shadow: 0 4px 6px rgba(74, 144, 226, 0.15) !important;
        }

        @media (max-width: 1199px) {
            .sticky-sidebar { position: static; min-height: auto; max-height: none; }
        }
        @media (max-width: 767.98px) {
            .app-container { padding-top: 1rem !important; padding-bottom: 1rem !important; }
            .table-mobile-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            h1.h3 { font-size: 1.3rem !important; }
        }
    </style>
    {{-- END REGION 1: ESTILOS --}}


    {{-- ==============================================================================
         REGION 2: CONTENEDOR PRINCIPAL
         ============================================================================== --}}
    <div class="app-container py-4" style="min-height: 100vh; background: var(--c-bg);">
        <div class="container-fluid px-xl-4">
            <div class="row g-4 m-0">

                {{-- ==================================================================
                     REGION 3: COLUMNA IZQUIERDA (CONTENIDO PRINCIPAL - 9 COLUMNAS)
                     ================================================================== --}}
                <div class="col-12 col-xl-9 order-2 order-xl-1">

                    {{-- 3.1 ENCABEZADO Y CONTROLES SUPERIORES --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; background-color: var(--c-primary-soft); flex-shrink: 0;">
                                <i class="fas fa-chart-pie fs-4" style="color: var(--c-primary);"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold m-0" style="color: var(--c-text); letter-spacing: -0.5px;">
                                    Centro de Control de Informes
                                    <span class="badge bg-pastel-primary ms-2 d-none d-sm-inline-block" style="font-size: 0.7rem; vertical-align: middle;">Analítica</span>
                                </h1>
                                <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem;">Gestión, generación y consolidado de reportes por lote operativo.</p>

                                @if(isset($bloqueActivo) && $bloqueActivo)
                                    <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-pastel-primary text-primary border-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                            <i class="fas fa-cube me-1"></i> Lote Activo: API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        @if(isset($textoPeriodo))
                                            <span class="badge bg-light text-muted border px-2 py-1 shadow-sm badge-hover-effect" style="font-size: 0.75rem;">
                                                <i class="far fa-calendar-alt me-1"></i> {{ $textoPeriodo }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ request()->fullUrl() }}" class="btn btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                    {{-- END 3.1 ENCABEZADO --}}

                    {{-- 3.2 ALERTAS FLASH --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    {{-- END 3.2 ALERTAS --}}

                    {{-- 3.3 TARJETAS KPI --}}
                    @if(isset($bloqueActivo) && $bloqueActivo)
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="fas fa-file-alt fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Total Líneas</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['total'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-success rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="fas fa-check-double fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Generados</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['generados'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="fas fa-clock fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Pendientes</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['pendientes'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- END 3.3 KPI --}}

                    {{-- 3.4 GESTIÓN Y CONFIGURACIONES (Acordeón) --}}
                    <div class="card card-custom shadow border-0 mb-4">
                        <div class="card-header bg-white border-bottom p-4" style="border-radius: 20px 20px 0 0;">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 w-100">
                                <div class="flex-grow-1 pe-md-3">
                                    <h6 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text);">
                                        <i class="fas fa-sliders-h text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                        Configuración y Parámetros del Lote
                                    </h6>
                                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem; max-width: 600px;">
                                        Administra las reglas, configuraciones JSON y tareas programadas para el bloque seleccionado.
                                    </p>
                                </div>
                                <div>
                                    <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfiguradas" aria-expanded="false" aria-controls="collapseConfiguradas">
                                        <i class="fas fa-table me-2"></i> Ver Tablas de Configuración
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="collapseConfiguradas">
                            <div class="card-body p-0">
                                <div class="accordion accordion-flush" id="accordionMatrizTablas">
                                    
                                    {{-- TABLA 1: CONFIGURACIONES ASIGNADAS --}}
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="headingTabla1">
                                            <button class="accordion-button px-4 py-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla1" aria-expanded="true">
                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                    <div><i class="fas fa-file-code me-2"></i> Configuraciones JSON / Parámetros</div>
                                                    <span class="badge bg-white text-dark border shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                                        {{ isset($plantillasAsignadas) ? $plantillasAsignadas->total() : 0 }} Registros
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTabla1" class="accordion-collapse collapse show" data-bs-parent="#accordionMatrizTablas">
                                            <div class="accordion-body bg-light p-3 p-md-4">
                                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                                    <div class="table-responsive custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                                                        <table class="table table-sm table-hover m-0 align-middle" style="font-size: 0.8rem;">
                                                            <thead class="table-light text-uppercase text-muted sticky-top">
                                                                <tr>
                                                                    <th class="ps-3">ID Config</th>
                                                                    <th>Operación Asociada</th>
                                                                    <th>Justificación</th>
                                                                    <th class="text-center">Estado Activo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($plantillasAsignadas) && $plantillasAsignadas->count() > 0)
                                                                    @foreach($plantillasAsignadas as $config)
                                                                        <tr>
                                                                            <td class="ps-3 fw-bold">#{{ $config->id }}</td>
                                                                            <td>Op ID: {{ $config->id_car_sia_operaciones ?? 'N/A' }}</td>
                                                                            <td class="text-muted">{{ Str::limit($config->justificacion ?? 'Sin detalle', 40) }}</td>
                                                                            <td class="text-center">
                                                                                <span class="badge {{ $config->activo ? 'bg-pastel-success text-success' : 'bg-pastel-secondary text-secondary' }}">
                                                                                    {{ $config->activo ? 'ACTIVO' : 'INACTIVO' }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="4" class="text-center py-4 text-muted">No hay configuraciones registradas para este lote.</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TABLA 2: ALERTAS PROGRAMADAS --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTabla2">
                                            <button class="accordion-button collapsed px-4 py-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla2" aria-expanded="false">
                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                    <div><i class="fas fa-calendar-check me-2"></i> Alertas y Tareas Programadas</div>
                                                    <span class="badge bg-white text-dark border shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                                        {{ isset($informesProgramados) ? $informesProgramados->total() : 0 }} Registros
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTabla2" class="accordion-collapse collapse" data-bs-parent="#accordionMatrizTablas">
                                            <div class="accordion-body bg-light p-3 p-md-4">
                                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                                    <div class="table-responsive custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                                                        <table class="table table-sm table-hover m-0 align-middle" style="font-size: 0.8rem;">
                                                            <thead class="table-light text-uppercase text-muted sticky-top">
                                                                <tr>
                                                                    <th class="ps-3">ID Alerta</th>
                                                                    <th>Bloque</th>
                                                                    <th class="text-center">Fecha Programada</th>
                                                                    <th class="text-center">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($informesProgramados) && $informesProgramados->count() > 0)
                                                                    @foreach($informesProgramados as $prog)
                                                                        <tr>
                                                                            <td class="ps-3 fw-bold">#{{ $prog->id }}</td>
                                                                            <td>API-{{ str_pad($prog->numero_bloque, 4, '0', STR_PAD_LEFT) }}</td>
                                                                            <td class="text-center">{{ $prog->fecha_programada ?? 'N/A' }}</td>
                                                                            <td class="text-center"><span class="badge bg-pastel-info text-info">PROGRAMADA</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="4" class="text-center py-4 text-muted">No hay alertas programadas.</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END 3.4 GESTIÓN --}}

                    {{-- ==============================================================================
                         3.5 TABLA PRINCIPAL DE LOTES (BLOQUES)
                         ============================================================================== --}}
                    <div class="card card-custom shadow border-0 mb-4">
                        <div class="card-header bg-white border-bottom p-4" style="border-radius: 20px 20px 0 0;">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 w-100">
                                <div class="flex-grow-1 pe-md-3">
                                    <h6 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text);">
                                        <i class="fas fa-cubes text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                        Listado General de Lotes (Bloques)
                                    </h6>
                                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem; max-width: 650px;">
                                        Selecciona un lote para acceder a su respectivo dashboard y analítica consolidada.
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-stretch align-items-md-end gap-2 ms-md-auto flex-shrink-0" style="min-width: 320px;">
                                    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-md-end gap-2 w-100">
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 d-none d-md-inline-block shadow-sm">
                                            <i class="fas fa-hashtag text-muted me-1"></i> {{ isset($informes) ? number_format($informes->total(), 0, ',', '.') : 0 }} Lotes
                                        </span>
                                        <button class="btn btn-light border rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                                            <i class="fas fa-filter text-muted me-1"></i> Filtros
                                        </button>
                                    </div>

                                    {{-- Buscador Colapsable --}}
                                    <div class="collapse {{ request('buscar') ? 'show' : '' }} w-100 mt-1" id="collapseFiltros">
                                        <form action="{{ route('certificados.informes.index') }}" method="GET" class="bg-light p-2 rounded-4 border shadow-sm d-flex flex-column flex-md-row gap-2 w-100 align-items-center" style="border-color: var(--c-border) !important;">
                                            <input type="hidden" name="bloque" value="{{ $bloqueActivo ?? '' }}">
                                            <div class="input-group input-group-sm flex-grow-1 bg-white rounded-pill shadow-sm overflow-hidden border">
                                                <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-1"><i class="fas fa-search"></i></span>
                                                <input type="text" name="buscar" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar por número de lote..." value="{{ request('buscar') }}" style="font-size: 0.8rem; background: transparent;">
                                            </div>
                                            <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4 py-1 text-uppercase w-100 w-md-auto" style="font-size: 0.75rem;">Buscar</button>
                                            @if(request('buscar'))
                                                <a href="{{ route('certificados.informes.index', ['bloque' => $bloqueActivo]) }}" class="btn btn-white border rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 34px; height: 34px; flex-shrink: 0;" title="Limpiar Filtros">
                                                    <i class="fas fa-times text-danger"></i>
                                                </a>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse show" id="collapseTablaPrincipal">
                            <div class="card-body bg-light p-3 p-md-4 border-top">
                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                    <div class="bg-white px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <span class="text-muted small"><i class="fas fa-list text-primary me-1"></i> Lotes Registrados en el Sistema</span>
                                    </div>
                                    <div class="table-responsive custom-scrollbar table-mobile-scroll">
                                        <table class="table table-sm table-hover align-middle mb-0 text-nowrap" style="font-size: 0.8rem;">
                                            <thead class="table-light text-muted text-uppercase sticky-top" style="z-index: 10; font-size: 0.7rem;">
                                                <tr>
                                                    <th class="ps-3 py-2">Identificador de Lote</th>
                                                    <th class="py-2">Descripción</th>
                                                    <th class="py-2">Periodo (Mes / Año)</th>
                                                    <th class="py-2">Estado del Lote</th>
                                                    <th class="pe-3 py-2 text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($informes) && $informes->count() > 0)
                                                    @foreach($informes as $lote)
                                                        <tr style="border-bottom: 1px solid #f1f3f5;">
                                                            <td class="ps-3 py-2">
                                                                <div class="fw-bold text-primary">
                                                                    <i class="fas fa-cube me-1"></i> Lote API-{{ str_pad($lote->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                            </td>
                                                            <td class="py-2">
                                                                <span class="text-dark">{{ $lote->descripcion ?? 'Sin descripción' }}</span>
                                                            </td>
                                                            <td class="py-2">
                                                                <span class="text-muted">
                                                                    {{ optional($lote->periodo)->mes ? \Carbon\Carbon::create()->month($lote->periodo->mes)->locale('es')->monthName : 'N/A' }} 
                                                                    {{ optional($lote->periodo)->anio ?? '' }}
                                                                </span>
                                                            </td>
                                                            <td class="py-2">
                                                                <span class="badge {{ $lote->estado ? 'bg-pastel-success text-success' : 'bg-pastel-secondary text-secondary' }} px-2 py-1">
                                                                    {{ $lote->estado ?? 'ACTIVO' }}
                                                                </span>
                                                            </td>
                                                            <td class="pe-3 py-2 text-center">
                                                                <a href="{{ route('certificados.informes.show', $lote->numero_bloque) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold" title="Ver Dashboard del Lote">
                                                                    <i class="fas fa-chart-line me-1" style="font-size: 0.75rem;"></i> Ver Dashboard
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5 bg-white">
                                                            <div class="text-center px-4 py-4">
                                                                <i class="fas fa-cubes fs-2 text-muted opacity-25 mb-3"></i>
                                                                <h6 class="fw-bold text-dark mb-1">Sin Lotes Registrados</h6>
                                                                <p class="text-muted small mb-0">No se encontraron lotes activos en el sistema.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- Paginación de Lotes --}}
                                    @if(isset($informes) && $informes->hasPages())
                                        <div class="bg-light border-top pt-3 pb-3 px-4 d-flex justify-content-between align-items-center">
                                            <div class="m-0 pagination-sm custom-pagination">
                                                {{ $informes->appends(request()->query())->links('pagination::bootstrap-5') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END REGION 3: COLUMNA IZQUIERDA --}}

                {{-- ==============================================================================
                     REGION 4: COLUMNA DERECHA (SIDEBAR - EXPLORADOR Y AUDITORÍA)
                     ============================================================================== --}}
                <div class="col-12 col-xl-3 order-1 order-xl-2 mb-3 mb-xl-0">
                    <div class="d-flex flex-column gap-3 sticky-sidebar">

                        {{-- Explorador de Lotes --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 380px;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark m-0" style="font-size: 1.05rem;">
                                    <i class="far fa-folder-open text-muted me-2"></i> Explorador
                                </h5>
                            </div>
                            <p class="text-muted mb-3" style="font-size:.8rem;">Navega por periodo para cambiar de lote.</p>

                            <div class="overflow-auto custom-scrollbar flex-grow-1 pe-2">
                                @if(isset($periodosAbiertos))
                                    @forelse($periodosAbiertos as $anio => $periodosDelAnio)
                                        <div class="mb-3">
                                            <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2" onclick="toggleAcordeon('year-content-{{ $anio }}', this)">
                                                <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                                    <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>
                                                    <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                                </span>
                                            </div>
                                            <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: none;">
                                                @foreach($periodosDelAnio as $periodo)
                                                    @php
                                                        $nombreMes = \Carbon\Carbon::create()->month($periodo->mes)->locale('es')->monthName;
                                                        $bloquesDelMes = isset($bloquesAgrupadosPorPeriodo) ? $bloquesAgrupadosPorPeriodo->get($periodo->id, collect()) : collect();
                                                    @endphp
                                                    <div class="d-flex flex-column rounded mb-1 shadow-sm" style="background: var(--c-surface); border: 1px solid var(--c-border);">
                                                        <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center" onclick="toggleAcordeon('month-content-{{ $periodo->id }}', this)">
                                                            <div class="fw-bold text-dark" style="font-size:.8rem;">
                                                                <i class="fas fa-chevron-right chevron-month text-muted me-1"></i> {{ ucfirst($nombreMes) }}
                                                            </div>
                                                            <span class="badge bg-pastel-success text-success" style="font-size: 0.6rem;">ABIERTO</span>
                                                        </div>
                                                        <div class="month-content flex-column gap-1 p-2 pt-0 mt-1 border-top" id="month-content-{{ $periodo->id }}" style="display: none;">
                                                            @foreach($bloquesDelMes as $bloque)
                                                                <a href="{{ route('certificados.informes.index', ['bloque' => $bloque->numero_bloque]) }}"
                                                                class="block-link d-flex align-items-center justify-content-between text-decoration-none px-2 py-1 rounded {{ (isset($bloqueActivo) && $bloqueActivo == $bloque->numero_bloque) ? 'active-block' : '' }}" style="font-size: .75rem;">
                                                                    <span class="fw-semibold"><i class="fas fa-cube ico-cube"></i> Lote API-{{ str_pad($bloque->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted"><p>No hay periodos habilitados.</p></div>
                                    @endforelse
                                @endif
                            </div>
                        </div>

                        {{-- Auditoría / Historial del Lote --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                                    <i class="fas fa-history text-muted"></i> Auditoría Lote
                                </h5>
                            </div>
                            <p class="text-muted mb-3" style="font-size:.8rem;">Últimos eventos del bloque.</p>

                            <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2" style="max-height: 250px;">
                                @if(isset($historialBloque) && $historialBloque->count() > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($historialBloque as $log)
                                            <div class="p-2 rounded bg-light border-0 shadow-sm" style="font-size: 0.75rem;">
                                                <div class="fw-bold text-dark">
                                                    <i class="fas fa-circle text-primary" style="font-size: 5px; vertical-align: middle;"></i> 
                                                    {{ optional($log->eventoAuditoria)->nombre ?? 'Evento' }}
                                                </div>
                                                <div class="text-muted mt-1">Por: {{ optional($log->usuario)->name ?? 'Sistema' }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fas fa-list-ul fs-2 mb-3 text-secondary opacity-25"></i>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: .9rem;">Sin Movimientos</h6>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
                {{-- END REGION 4: COLUMNA DERECHA --}}

            </div>
        </div>
    </div>
    {{-- END REGION 2 --}}

    <script>
        function toggleAcordeon(targetId, element) {
            const target = document.getElementById(targetId);
            if (target) {
                if (target.style.display === 'none' || target.style.display === '') {
                    target.style.display = 'flex';
                    element.classList.add('is-open');
                } else {
                    target.style.display = 'none';
                    element.classList.remove('is-open');
                }
            }
        }
    </script>
</x-base-layout>