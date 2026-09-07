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

        /* Utilidades Generales y Pasteles */
        .bg-pastel-primary { background-color: var(--c-primary-soft) !important; color: var(--c-primary) !important; border: none; }
        .bg-pastel-info { background-color: var(--c-info-soft) !important; color: var(--c-info) !important; border: none; }
        .bg-pastel-success { background-color: var(--c-success-soft) !important; color: var(--c-success) !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-warning { background-color: var(--c-warning-soft) !important; color: var(--c-warning) !important; border: none; }

        /* Componentes Custom */
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }

        /* Botones e Inputs */
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }
        .form-select-custom, .form-control-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 0.6rem 1rem; transition: all 0.3s ease;}
        .form-select-custom:focus, .form-control-custom:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.1); border-color: #4a90e2; }
        .btn-reload { background-color: #ffffff; border: 1px solid #e9ecef; color: #adb5bd; transition: all 0.3s ease; }
        .btn-reload:hover { background-color: var(--c-primary-soft); color: var(--c-primary); border-color: var(--c-primary-soft); }
        .btn-reload:hover i { transform: rotate(180deg); transition: transform 0.4s ease; }
        .btn-reload i { transition: transform 0.4s ease; }

        /* Scrollbar y Progress Bar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        .progress-minimalist { height: 6px; width: 100%; background-color: #fee2e2; border-radius: 4px; overflow: hidden; position: relative; }
        .progress-minimalist::before { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background-color: var(--c-danger); animation: progress-slide 1.5s infinite ease-in-out; border-radius: 4px; }
        @keyframes progress-slide { 0% { left: -50%; width: 30%; } 50% { width: 60%; } 100% { left: 100%; width: 30%; } }

        /* Sidebar y Acordeón */
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
        .mes-actual-highlight { border: 1px solid var(--c-primary) !important; background: var(--c-primary-soft) !important; }

        /* Tabla Reporte Configuraciones */
        .table-excel { width: 100%; border-collapse: collapse; background: #fff; font-size: 0.75rem; }
        .table-excel th, .table-excel td { border: 1px solid #e5e7eb; padding: 8px 12px; vertical-align: middle; }
        .table-excel th { background: #f3f4f6; font-weight: bold; color: #374151; text-align: left; border-bottom: 2px solid #d1d5db; white-space: nowrap; }
        .table-excel td.readonly-cell { color: #4b5563; }
        .table-excel tr:hover td { background-color: #f8fafc; }

        @media (max-width: 1199px) { .sticky-sidebar { position: static; min-height: auto; } }
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
                <div class="col-12 col-xl-9">

                    {{-- 3.1 ENCABEZADO Y CONTROLES SUPERIORES --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; background-color: var(--c-primary-soft);">
                                <i class="fas fa-layer-group fs-4" style="color: var(--c-primary);"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold m-0" style="color: var(--c-text); letter-spacing: -0.5px;">
                                    Gestión y Emisión de Certificados
                                    <span class="badge bg-pastel-primary ms-2" style="font-size: 0.7rem; vertical-align: middle;">Lotes</span>
                                </h1>
                                <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem;">Gestión y matriz principal aislada por Bloque.</p>

                                @if($bloqueActivo)
                                    @php
                                        // Buscar el bloque seleccionado para extraer su fecha/periodo
                                        $bloqueSeleccionado = collect($bloquesDisponibles)->firstWhere('numero_bloque', $bloqueActivo);
                                        $textoPeriodo = '';
                                        if($bloqueSeleccionado && $bloqueSeleccionado->fecha_ejecucion) {
                                            $fecha = \Carbon\Carbon::parse($bloqueSeleccionado->fecha_ejecucion);
                                            $textoPeriodo = ucfirst($fecha->locale('es')->monthName) . ' ' . $fecha->year;
                                        }
                                    @endphp
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-pastel-primary text-primary border-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                            <i class="fas fa-cube me-1"></i> Trabajando en Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        @if($textoPeriodo)
                                            <span class="badge bg-light text-muted border px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                                <i class="far fa-calendar-alt me-1"></i> {{ $textoPeriodo }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Botones de Acción Derecha --}}
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <a href="{{ request()->fullUrl() }}" class="btn btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            @if($bloqueActivo)
                                <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlertaBloque">
                                    <i class="fas fa-bell me-2"></i> Alerta de Lote
                                </button>
                                <button type="button" class="btn btn-danger shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalMasivo">
                                    <i class="fas fa-database me-2"></i> Generación Masiva
                                </button>
                            @endif
                        </div>
                    </div>
                    {{-- END 3.1 ENCABEZADO --}}

                    {{-- 3.2 ALERTAS FLASH (Success/Error) --}}
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

                    {{-- 3.3 TARJETAS KPI (Indicadores Clave) --}}
                    @if($bloqueActivo)
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                                        <i class="fas fa-cubes fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Total de Clientes</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['total'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                                        <i class="fas fa-check-double fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Clientes con Certificados</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['procesados'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                                        <i class="fas fa-hourglass-half fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Clientes sin Certificados</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['pendientes'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- END 3.3 KPI --}}

                    {{-- 3.4 REPORTE DE CONFIGURACIONES ASIGNADAS (Acordeón) --}}
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalConfiguracionIndex">
                            <i class="fas fa-cogs me-2"></i> Configurar Notificaciones
                        </button>
                    </div>

                    <div class="card card-custom shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center" style="border-radius: 20px 20px 0 0;">
                            <h6 class="fw-bold m-0 text-success d-flex align-items-center gap-2">
                                <i class="fas fa-list-check border border-success text-success rounded p-1"></i>
                                Configuraciones Asignadas en este Lote
                            </h6>
                            <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfiguradas" aria-expanded="false" aria-controls="collapseConfiguradas">
                                <i class="fas fa-angle-down me-1"></i> Desplegar
                            </button>
                        </div>

                        <div class="collapse" id="collapseConfiguradas">
                            <div class="card-body p-0">
                                <div class="table-responsive custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table-excel">
                                        <thead style="position: sticky; top: 0; z-index: 20;">
                                            <tr>
                                                <th style="width: 15%;">Radicado</th>
                                                <th style="width: 30%;">Cliente</th>
                                                <th style="width: 30%;">Regla de Vencimiento Aplicada</th>
                                                <th style="width: 15%; text-align: center;">Frecuencia</th>
                                                <th style="width: 10%; text-align: center;">Notificación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hayConfiguradas = false; @endphp
                                            {{-- IMPRIMIR MASIVAS --}}
                                            @foreach($configuracionesMasivas as $masiva)
                                                @php $hayConfiguradas = true; @endphp
                                                <tr style="background-color: var(--c-primary-soft);">
                                                    <td class="readonly-cell font-monospace text-primary text-center fw-bold"><i class="fas fa-layer-group"></i> LOTE</td>
                                                    <td class="readonly-cell fw-bold text-primary"><i class="fas fa-users me-1"></i> Aplica a todo el Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</td>
                                                    <td class="readonly-cell fw-bold text-dark">{{ $masiva->configuracionBase?->accionVencimiento?->nombre ?? 'N/A' }}</td>
                                                    <td class="readonly-cell text-center">
                                                        @if($masiva->configuracionBase?->frecuencia_recordatorio_dias)
                                                            <span class="badge bg-white text-dark border shadow-sm">Cada {{ $masiva->configuracionBase->frecuencia_recordatorio_dias }} días</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="readonly-cell text-center">
                                                        @if($masiva->estado_notificacion) <span class="badge bg-pastel-success text-success px-2 py-1 bg-white"><i class="fas fa-bell me-1"></i> ACTIVA</span>
                                                        @else <span class="badge bg-pastel-secondary text-muted px-2 py-1 bg-white"><i class="fas fa-bell-slash me-1"></i> INACTIVA</span> @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            {{-- IMPRIMIR INDIVIDUALES --}}
                                            @foreach($operacionesConfiguradas as $op)
                                                @php $hayConfiguradas = true; @endphp
                                                <tr>
                                                    <td class="readonly-cell font-monospace">{{ $op->numero_radicado ?? 'N/A' }}</td>
                                                    <td class="readonly-cell text-truncate" style="max-width: 150px;" title="{{ $op->tercero?->nom_ter ?? '' }} {{ $op->tercero?->apl1 ?? '' }}">
                                                        {{ $op->tercero?->nom_ter ?? 'Sin Tercero' }} {{ $op->tercero?->apl1 ?? '' }}
                                                    </td>
                                                    <td class="readonly-cell fw-bold text-dark">{{ $op->configuracion?->configuracionBase?->accionVencimiento?->nombre ?? 'N/A' }}</td>
                                                    <td class="readonly-cell text-center">
                                                        @if($op->configuracion?->configuracionBase?->frecuencia_recordatorio_dias)
                                                            <span class="badge bg-light text-dark border">Cada {{ $op->configuracion->configuracionBase->frecuencia_recordatorio_dias }} días</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="readonly-cell text-center">
                                                        @if($op->configuracion?->estado_notificacion) <span class="badge bg-pastel-success text-success px-2 py-1"><i class="fas fa-bell me-1"></i> ACTIVA</span>
                                                        @else <span class="badge bg-pastel-secondary text-muted px-2 py-1"><i class="fas fa-bell-slash me-1"></i> INACTIVA</span> @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if(!$hayConfiguradas)
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 bg-light text-muted">
                                                        <i class="fas fa-info-circle mb-2 fs-4 opacity-50"></i><br>
                                                        No hay configuraciones asignadas en el Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}.<br>
                                                        Usa el botón "Configurar Notificaciones" para aplicar una regla.
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END 3.4 REPORTE CONFIGURACIONES --}}

                    {{-- 3.5 TABLA PRINCIPAL Y FILTROS --}}
                    <div class="card card-custom shadow-sm border-0 mb-4">
                        {{-- Callout Informativo --}}
                        <div class="card-body pb-0 pt-4 px-4">
                            <div class="alert bg-pastel-primary border-0 rounded-4 mb-0 d-flex gap-3 shadow-sm" role="alert" style="padding: 1.25rem;">
                                <div class="mt-1"><i class="fas fa-info-circle fs-3 text-primary"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">Información del Periodo Seleccionado</h6>
                                    @php
                                        $mesEtiqueta = 'Mes Seleccionado';
                                        if($bloqueActivo) {
                                            $bloqueSel = collect($bloquesDisponibles)->firstWhere('numero_bloque', $bloqueActivo);
                                            if($bloqueSel && $bloqueSel->fecha_ejecucion) {
                                                $fechaSel = \Carbon\Carbon::parse($bloqueSel->fecha_ejecucion);
                                                $mesEtiqueta = ucfirst($fechaSel->locale('es')->monthName) . ' ' . $fechaSel->year;
                                            }
                                        }
                                    @endphp
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                                        Se muestran exclusivamente los clientes asociados a
                                        <span class="badge bg-primary text-white px-2 py-1 mx-1 shadow-sm rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            <i class="far fa-calendar-check me-1"></i> {{ $mesEtiqueta }}
                                        </span>
                                        para la gestión de certificados.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de Herramientas y Filtros --}}
                        <div class="card-header bg-white border-bottom p-4 pb-3 mt-2" style="border-radius: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h6 class="fw-bold m-0" style="color: var(--c-text);">
                                    <i class="fas fa-list text-muted me-2"></i> Operaciones del Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
                                </h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-pastel-info text-dark rounded-pill px-3 py-2 d-none d-md-inline-block">
                                        <i class="fas fa-hashtag me-1"></i> {{ number_format($operaciones->total(), 0, ',', '.') }} Registros
                                    </span>
                                    <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros" aria-expanded="{{ (request('anio') || request('buscar')) ? 'true' : 'false' }}" aria-controls="collapseFiltros">
                                        <i class="fas fa-filter text-muted me-1"></i> Filtros
                                    </button>
                                </div>
                            </div>

                            <div class="collapse {{ (request('anio') || request('buscar')) ? 'show' : '' }}" id="collapseFiltros">
                                <form action="{{ route('certificados.operaciones.index') }}" method="GET" class="row g-2 align-items-end mt-3 bg-light p-3 rounded-4 border" style="border-color: var(--c-border) !important;">
                                    <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: #e9ecef;"><i class="fas fa-search"></i></span>
                                            <input type="text" name="buscar" class="form-control form-control-custom border-start-0 ps-0" placeholder="Ej. Nombre, NIT o Radicado..." value="{{ request('buscar') }}" style="border-radius: 0 12px 12px 0;">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-pastel-primary flex-grow-1 fw-bold shadow-sm rounded-pill py-2">Buscar</button>
                                        @if(request('anio') || request('buscar'))
                                            <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn btn-white border fw-bold shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;" title="Limpiar Filtros">
                                                <i class="fas fa-times text-danger"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Tabla de Registros --}}
                        <div class="table-responsive border-top" style="border-color: var(--c-border) !important;">
                            <table class="table table-sm table-bordered table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                <thead class="bg-light text-muted text-uppercase" style="font-size: 0.7rem;">
                                    <tr>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 15%;">Radicado / Bloque</th>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 20%;">Cliente (Tercero)</th>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 15%;">Estado Actual</th>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 15%;">Último Evento</th>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 15%;">Última Alerta</th>
                                        <th class="px-3 py-2 border-bottom-0 text-secondary" style="font-weight: 600; width: 12%;">Fecha</th>
                                        <th class="px-3 py-2 border-bottom-0 text-center text-secondary" style="font-weight: 600; width: 8%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($operaciones as $operacion)
                                        <tr class="bg-white">
                                            <td class="px-3 py-2">
                                                <div class="fw-bold text-dark">{{ $operacion->numero_radicado ?? 'N/A' }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-cube me-1 opacity-50"></i> API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($operacion->tercero)
                                                    <div class="fw-bold text-dark">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">NIT: {{ $operacion->tercero->cod_ter }}</div>
                                                @else
                                                    <span class="badge bg-pastel-warning text-dark px-2 py-1 rounded-1"><i class="fas fa-exclamation-triangle me-1"></i> Sin Tercero</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $todosLosEstados = collect();
                                                    if(isset($operacion->estados)) $todosLosEstados = $todosLosEstados->concat($operacion->estados);
                                                    if(isset($operacion->estadosBloque)) $todosLosEstados = $todosLosEstados->concat($operacion->estadosBloque);
                                                    $ultimoEstado = $todosLosEstados->sortByDesc('created_at')->first();
                                                    $esEstadoBloque = $ultimoEstado && is_null($ultimoEstado->id_car_sia_operaciones);
                                                    $estadoNombre = $ultimoEstado && $ultimoEstado->estado ? $ultimoEstado->estado->nombre : 'Pendiente';
                                                    $clasePastel = match(strtolower(trim($estadoNombre))) {
                                                        'aprobado', 'completado', 'vigente', 'procesado' => 'bg-pastel-success',
                                                        'rechazado', 'anulado' => 'bg-pastel-warning',
                                                        'pendiente', 'nuevo', 'pendiente por procesar' => 'bg-pastel-secondary',
                                                        default => 'bg-pastel-primary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $clasePastel }} rounded-1 px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="fas {{ $esEstadoBloque ? 'fa-layer-group' : 'fa-info-circle' }} me-1"></i> {{ strtoupper($estadoNombre) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $todosLosTipos = collect();
                                                    if(isset($operacion->tipos)) $todosLosTipos = $todosLosTipos->concat($operacion->tipos);
                                                    if(isset($operacion->tiposBloque)) $todosLosTipos = $todosLosTipos->concat($operacion->tiposBloque);
                                                    $ultimoTipoObj = $todosLosTipos->sortByDesc('created_at')->first();
                                                    $esTipoBloque = $ultimoTipoObj && is_null($ultimoTipoObj->id_car_sia_operaciones);
                                                    $tipoNombre = $ultimoTipoObj && $ultimoTipoObj->tipo ? $ultimoTipoObj->tipo->nombre : 'Sin Evento';
                                                @endphp
                                                <span class="badge bg-light text-dark border rounded-1 px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="fas {{ $esTipoBloque ? 'fa-layer-group text-info' : 'fa-tag text-info' }} me-1"></i> {{ strtoupper($tipoNombre) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $todasLasAlertas = collect();
                                                    if(isset($operacion->alertas)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertas);
                                                    if(isset($operacion->alertasBloque)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertasBloque);
                                                    $ultimaAlertaObj = $todasLasAlertas->sortByDesc('created_at')->first();
                                                    $esAlertaBloque = $ultimaAlertaObj && is_null($ultimaAlertaObj->id_car_sia_operaciones);
                                                @endphp
                                                @if($ultimaAlertaObj)
                                                    <span class="badge {{ $esAlertaBloque ? 'bg-pastel-primary' : 'bg-pastel-info' }} rounded-1 px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                        <i class="fas {{ $esAlertaBloque ? 'fa-layer-group' : 'fa-bell' }} me-1"></i>
                                                        {{ strtoupper($ultimaAlertaObj->tipoAlerta->nombre ?? 'DESCONOCIDA') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-minus opacity-50"></i></span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="text-gray-800 fw-bold">{{ $operacion->created_at->format('d/m/Y') }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">{{ $operacion->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <a href="{{ route('certificados.operaciones.show', $operacion->id) }}" class="btn btn-light btn-sm rounded-1 border shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; padding: 0;" title="Ver Detalle">
                                                    <i class="fas fa-eye text-primary" style="font-size: 0.75rem;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 bg-white">
                                                <div class="text-center px-4 py-4">
                                                    <i class="fas fa-search fs-2 text-muted opacity-25 mb-3"></i>
                                                    <h6 class="fw-bold text-dark mb-1">Lote Vacío o Sin Resultados</h6>
                                                    <p class="text-muted small mb-0">No se encontraron operaciones en el Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        @if($operaciones->hasPages() || $operaciones->total() > 0)
                            <div class="card-footer bg-light border-top pt-3 pb-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="border-radius: 0 0 20px 20px;">
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    Mostrando <span class="fw-bold text-dark">{{ $operaciones->firstItem() ?? 0 }}</span> a <span class="fw-bold text-dark">{{ $operaciones->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ number_format($operaciones->total(), 0, ',', '.') }}</span> registros
                                </span>
                                <div class="m-0" style="font-size: 0.85rem;">
                                    {{ $operaciones->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- END 3.5 TABLA PRINCIPAL --}}

                </div>
                {{-- END REGION 3: COLUMNA IZQUIERDA --}}

                {{-- ==================================================================
                     REGION 4: COLUMNA DERECHA (SIDEBAR FIJO - 3 COLUMNAS)
                     ================================================================== --}}
                <div class="col-12 col-xl-3">
                    <div class="d-flex flex-column gap-3 sticky-sidebar">

                        {{-- 4.1 NAVEGACIÓN DE LOTES (Explorador) --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark m-0" style="font-size: 1.05rem;">
                                    <i class="far fa-folder-open text-muted me-2"></i> Explorador
                                </h5>
                            </div>
                            <p class="text-muted mb-3" style="font-size:.8rem;">Navega por los años y meses para cambiar el lote de trabajo.</p>

                            <div class="overflow-auto custom-scrollbar flex-grow-1 pe-2">
                                @php
                                    $periodosAbiertos = \App\Models\Certificados\CarSiaPeriodo::where('abierto', 1)
                                        ->orderBy('anio', 'desc')
                                        ->orderBy('mes', 'desc')
                                        ->get()
                                        ->groupBy('anio');
                                @endphp

                                @forelse($periodosAbiertos as $anio => $periodosDelAnio)
                                    @php
                                        $anioTieneActivo = collect($bloquesDisponibles)->filter(function($b) use ($anio) {
                                            return \Carbon\Carbon::parse($b->fecha_ejecucion)->format('Y') == $anio;
                                        })->contains('numero_bloque', $bloqueActivo);
                                        $abrirAnio = ($anio == date('Y') || $anioTieneActivo);
                                    @endphp

                                    <div class="mb-3">
                                        {{-- Toggle Año --}}
                                        <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2 {{ $abrirAnio ? 'is-open' : '' }}" onclick="toggleAcordeon('year-content-{{ $anio }}', this)">
                                            <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                                <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>
                                                <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                            </span>
                                        </div>

                                        {{-- Contenido Año --}}
                                        <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: {{ $abrirAnio ? 'flex' : 'none' }};">
                                            @foreach($periodosDelAnio as $periodo)
                                                @php
                                                    $nombreMes = \Carbon\Carbon::create()->month($periodo->mes)->locale('es')->monthName;
                                                    $esMesActual = ($anio == date('Y') && $periodo->mes == date('n'));
                                                    $bloquesDelMes = collect($bloquesDisponibles)->filter(function($b) use ($anio, $periodo) {
                                                        $fecha = \Carbon\Carbon::parse($b->fecha_ejecucion);
                                                        return $fecha->year == $anio && $fecha->month == $periodo->mes;
                                                    });
                                                    $mesTieneActivo = $bloquesDelMes->contains('numero_bloque', $bloqueActivo);
                                                @endphp

                                                <div class="d-flex flex-column rounded mb-1 shadow-sm {{ $esMesActual ? 'mes-actual-highlight' : '' }}" style="background: var(--c-surface); border: 1px solid var(--c-border);">

                                                    {{-- Toggle Mes --}}
                                                    <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center {{ $mesTieneActivo ? 'is-open' : '' }}" onclick="toggleAcordeon('month-content-{{ $periodo->id }}', this)" style="cursor: pointer;">
                                                        <div>
                                                            <div class="fw-bold {{ $esMesActual ? 'text-primary' : 'text-dark' }}" style="font-size:.8rem; line-height:1.2;">
                                                                <i class="fas fa-chevron-right chevron-month text-muted me-1" style="font-size:.65rem; transition: transform 0.3s;"></i>
                                                                {{ ucfirst($nombreMes) }}
                                                                @if($esMesActual) <span class="badge bg-primary text-white ms-1 shadow-sm" style="font-size:.55rem;">ACTUAL</span> @endif
                                                            </div>
                                                            <div class="ms-3 ps-1 mt-1" style="font-size:.65rem; color:var(--c-muted); font-family:monospace;">
                                                                {{ $bloquesDelMes->count() }} lotes
                                                            </div>
                                                        </div>
                                                        <div class="me-1">
                                                            <span class="badge bg-pastel-success text-success" style="font-size: 0.6rem;"><i class="fas fa-unlock me-1"></i> ABIERTO</span>
                                                        </div>
                                                    </div>

                                                    {{-- Lotes del Mes --}}
                                                    <div class="month-content flex-column gap-1 p-2 pt-0 mt-1 border-top" id="month-content-{{ $periodo->id }}" style="display: {{ $mesTieneActivo ? 'flex' : 'none' }};">
                                                        @if($bloquesDelMes->count() > 0)
                                                            <div class="mt-2">
                                                                @foreach($bloquesDelMes as $bloque)
                                                                    @php $esActivo = ($bloqueActivo == $bloque->numero_bloque); @endphp
                                                                    <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloque->numero_bloque]) }}"
                                                                        class="block-link d-flex align-items-center justify-content-between text-decoration-none px-2 py-1 rounded {{ $esActivo ? 'active-block' : '' }}" style="font-size: .75rem;">
                                                                        <span class="fw-semibold d-flex align-items-center gap-2">
                                                                            <i class="fas fa-cube ico-cube" style="font-size: .65rem;"></i>
                                                                            Lote API-{{ str_pad($bloque->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                        </span>
                                                                        @if($esActivo) <i class="fas fa-check" style="font-size: .65rem; color: #fff;"></i> @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="mt-1 text-center" style="font-size: .65rem; color: var(--c-muted);">Sin lotes registrados</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-folder-open fs-3 text-muted opacity-50 mb-2"></i>
                                        <p class="text-muted" style="font-size:.8rem;">No hay periodos habilitados actualmente.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        {{-- END 4.1 NAVEGACIÓN --}}

                        {{-- 4.2 AUDITORÍA DEL LOTE --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                                    <i class="fas fa-history text-muted"></i> Auditoría Lote
                                </h5>
                                @if($bloqueActivo)
                                    <span class="badge bg-light text-secondary border shadow-sm" style="font-size:.7rem; font-family: monospace;">
                                        API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2">
                                @if($bloqueActivo && isset($historialBloque) && $historialBloque->count() > 0)
                                    <div class="position-relative ms-2" style="border-left: 2px solid var(--c-border);">
                                        @foreach($historialBloque as $log)
                                            <div class="position-relative mb-3 ps-3 pt-1">
                                                <span class="position-absolute bg-primary rounded-circle border border-2 border-white shadow-sm" style="width: 12px; height: 12px; left: -7px; top: 8px;"></span>
                                                <div class="p-2 rounded bg-light border border-light shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: .75rem; line-height: 1.2;">{{ $log->eventoAuditoria->nombre ?? 'Evento de Motor' }}</span>
                                                        <span class="text-muted" style="font-size: .65rem; white-space: nowrap;">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '—' }}</span>
                                                    </div>
                                                    <div class="text-muted" style="font-size: .7rem;">
                                                        <i class="fas fa-user-circle me-1 opacity-50"></i>
                                                        <span class="fw-medium text-dark">{{ $log->usuario->name ?? 'Sistema' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fas fa-list-ul fs-2 mb-3 text-secondary opacity-25"></i>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: .9rem;">Sin Movimientos</h6>
                                        <p class="text-muted mb-0" style="font-size: .75rem; max-width: 200px;">No hay eventos registrados para este lote.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- END 4.2 AUDITORÍA --}}

                    </div>
                </div>
                {{-- END REGION 4: COLUMNA DERECHA --}}

            </div>
        </div>
    </div>
    {{-- END REGION 2: CONTENEDOR PRINCIPAL --}}


    {{-- ==============================================================================
         REGION 5: MODALES HTML
         ============================================================================== --}}

    {{-- 5.1 MODAL: CONFIGURACIÓN INDEX --}}
    <div class="modal fade" id="modalConfiguracionIndex" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formConfigIndex" action="{{ route('certificados.operaciones.config.masivo') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i> Configuración del Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                    <input type="hidden" name="buscar" value="{{ request('buscar') }}">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-3">Alcance de la configuración:</label>
                        <div class="form-check custom-radio border rounded-3 p-3 mb-2 shadow-sm {{ !request('buscar') ? 'bg-pastel-primary border-primary' : 'bg-white' }}" id="contRadioMasivo">
                            <input class="form-check-input ms-1 radio-alcance" type="radio" name="tipo_aplicacion" id="alcanceMasivo" value="masivo" data-ruta="{{ route('certificados.operaciones.config.masivo') }}" {{ !request('buscar') ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 w-100" for="alcanceMasivo" style="cursor: pointer;">
                                <span class="d-block fw-bold text-dark">Todo el Lote (Masivo)</span>
                                <span class="d-block text-muted" style="font-size: 0.75rem;">Aplica una regla general a todos los registros del bloque actual.</span>
                            </label>
                        </div>
                        <div class="form-check custom-radio border rounded-3 p-3 shadow-sm {{ request('buscar') ? 'bg-pastel-primary border-primary' : 'bg-white' }}" id="contRadioSelectivo">
                            <input class="form-check-input ms-1 radio-alcance" type="radio" name="tipo_aplicacion" id="alcanceSelectivo" value="selectivo" data-ruta="{{ route('certificados.operaciones.config.selectivo') }}" {{ request('buscar') ? 'checked' : '' }} {{ !request('buscar') ? 'disabled' : '' }}>
                            <label class="form-check-label ms-2 w-100" for="alcanceSelectivo" style="cursor: {{ request('buscar') ? 'pointer' : 'not-allowed' }};">
                                <span class="d-block fw-bold {{ request('buscar') ? 'text-dark' : 'text-muted opacity-50' }}">Solo resultados filtrados (Selectivo)</span>
                                <span class="d-block text-muted {{ request('buscar') ? '' : 'opacity-50' }}" style="font-size: 0.75rem;">Aplica excepciones solo a las operaciones de tu búsqueda actual.</span>
                                @if(!request('buscar')) <span class="badge bg-light text-danger mt-2 border"><i class="fas fa-info-circle"></i> Usa el buscador para habilitar.</span> @endif
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Configuración a Aplicar</label>
                        <select name="id_car_sia_config" class="form-select form-select-custom" required>
                            <option value="">Seleccione una configuración...</option>
                            @if(isset($configuracionesBase) && $configuracionesBase->count() > 0)
                                @foreach($configuracionesBase as $cfg)
                                    <option value="{{ $cfg->id }}">{{ $cfg->accionVencimiento->nombre ?? 'Config.' }} (Cada {{ $cfg->frecuencia_recordatorio_dias }} días)</option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay configuraciones disponibles</option>
                            @endif
                        </select>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border mt-4">
                        <div>
                            <span class="fw-bold d-block text-dark">Habilitar Notificaciones</span>
                            <span class="text-muted" style="font-size: 0.75rem;">Define si se enviarán notificaciones.</span>
                        </div>
                        <div class="form-check form-switch fs-4 m-0"><input class="form-check-input" type="checkbox" name="estado_notificacion" checked></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>
    {{-- END 5.1 MODAL CONFIGURACION --}}

    @if($bloqueActivo)
        {{-- 5.2 MODAL: ALERTA BLOQUE --}}
        <div class="modal fade" id="modalAlertaBloque" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('certificados.operaciones.alerta_bloque') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fas fa-bell text-info me-2"></i> Programar Alerta de Lote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert bg-pastel-info text-dark border-0 rounded-4 mb-4" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i> Esta alerta se aplicará al lote <strong>API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</strong>.
                        </div>
                        <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                        <div class="mb-3">
                            <label for="id_car_sia_tipos_alerta" class="form-label fw-semibold text-muted">Tipo de alerta</label>
                            <select name="id_car_sia_tipos_alerta" id="id_car_sia_tipos_alerta" class="form-select bg-light border-0" required>
                                <option value="">Seleccione una alerta</option>
                                @isset($tiposAlerta)
                                    @foreach($tiposAlerta as $tipoAlerta) <option value="{{ $tipoAlerta->id }}">{{ $tipoAlerta->nombre }}</option> @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="fecha_programada" class="form-label fw-semibold text-muted">Fecha programada</label>
                            <input type="date" name="fecha_programada" id="fecha_programada" class="form-control bg-light border-0" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white">Programar Lote</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- END 5.2 MODAL ALERTA --}}

        {{-- 5.3 MODAL: ESTRUCTURAR LOTE MASIVO --}}
        <div class="modal fade" id="modalMasivo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="formEstructurarLote" action="{{ route('certificados.operaciones.pdf_masivo') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fas fa-database text-danger me-2"></i> Estructurar Lote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert bg-pastel-warning text-dark border-0 rounded-4 mb-4" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i> Se procesarán masivamente las operaciones del lote <strong>API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</strong>.
                        </div>
                        <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                        <div class="mb-3">
                            <label for="id_car_sia_tipos" class="form-label fw-semibold text-muted">Tipo de Certificado</label>
                            <select name="id_car_sia_tipos" id="id_car_sia_tipos" class="form-select bg-light border-0" required>
                                <option value="">Seleccione un tipo...</option>
                                @isset($tipos)
                                    @foreach($tipos as $tipo) <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option> @endforeach
                                @endisset
                            </select>
                        </div>
                        <div id="loadingMasivo" class="d-none mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted fw-semibold" style="font-size: 0.75rem;"><i class="fas fa-cogs me-1"></i> Ensamblando documentos...</span>
                            </div>
                            <div class="progress-minimalist"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" id="btnCancelMasivo" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitMasivo" class="btn btn-danger rounded-pill px-4 fw-bold text-white">Procesar Lote</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- END 5.3 MODAL MASIVO --}}
    @endif
    {{-- END REGION 5: MODALES --}}


    {{-- ==============================================================================
         REGION 6: SCRIPTS JAVASCRIPT
         ============================================================================== --}}
    <script>
        // 6.1 Lógica de Modales Masivos y de Carga
        document.addEventListener('DOMContentLoaded', function () {
            const formMasivo = document.getElementById('formEstructurarLote');
            const btnSubmit = document.getElementById('btnSubmitMasivo');
            const btnCancel = document.getElementById('btnCancelMasivo');
            const loadingContainer = document.getElementById('loadingMasivo');

            if (formMasivo) {
                formMasivo.addEventListener('submit', function () {
                    if(btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Procesando Lote...';
                    }
                    if(btnCancel) btnCancel.classList.add('d-none');
                    if(loadingContainer) loadingContainer.classList.remove('d-none');
                });
            }
        });

        // 6.2 Controlador del Modal de Configuración (Masivo/Selectivo)
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('.radio-alcance');
            const formConfig = document.getElementById('formConfigIndex');
            const contMasivo = document.getElementById('contRadioMasivo');
            const contSelectivo = document.getElementById('contRadioSelectivo');

            if(formConfig && contMasivo && contSelectivo) {
                const radioSelectivo = document.getElementById('alcanceSelectivo');
                if(radioSelectivo && radioSelectivo.checked) formConfig.action = radioSelectivo.getAttribute('data-ruta');

                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        formConfig.action = this.getAttribute('data-ruta');
                        if(this.value === 'masivo') {
                            contMasivo.classList.add('bg-pastel-primary', 'border-primary');
                            contMasivo.classList.remove('bg-white');
                            contSelectivo.classList.remove('bg-pastel-primary', 'border-primary');
                            contSelectivo.classList.add('bg-white');
                        } else {
                            contSelectivo.classList.add('bg-pastel-primary', 'border-primary');
                            contSelectivo.classList.remove('bg-white');
                            contMasivo.classList.remove('bg-pastel-primary', 'border-primary');
                            contMasivo.classList.add('bg-white');
                        }
                    });
                });
            }
        });

        // 6.3 Función Acordeón del Sidebar Lateral
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
    {{-- END REGION 6: SCRIPTS --}}

</x-base-layout>
