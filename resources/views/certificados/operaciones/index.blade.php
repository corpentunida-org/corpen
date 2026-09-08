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
        .table-excel td.readonly-cell { color: #4b5563; white-space: nowrap; }
        .table-excel tr:hover td { background-color: #f8fafc; }

        /* Efecto Hover para Badges Informativos */
        .badge-hover-effect {
            transition: all 0.3s ease;
            cursor: default;
            display: inline-block;
        }
        .badge-hover-effect:hover {
            transform: translateY(-2px);
            background-color: var(--c-primary-soft) !important;
            color: var(--c-primary) !important;
            border-color: var(--c-primary-soft) !important;
            box-shadow: 0 4px 6px rgba(74, 144, 226, 0.15) !important;
        }

        /* Responsive Específico Celulares */
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
                     (En móvil usa order-2 para ir abajo, en XL usa order-xl-1 para ir a la izquierda)
                     ================================================================== --}}
                <div class="col-12 col-xl-9 order-2 order-xl-1">

                    {{-- 3.1 ENCABEZADO Y CONTROLES SUPERIORES --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; background-color: var(--c-primary-soft); flex-shrink: 0;">
                                <i class="fas fa-layer-group fs-4" style="color: var(--c-primary);"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold m-0" style="color: var(--c-text); letter-spacing: -0.5px;">
                                    Gestión y Emisión de Certificados
                                    <span class="badge bg-pastel-primary ms-2 d-none d-sm-inline-block" style="font-size: 0.7rem; vertical-align: middle;">Lotes</span>
                                </h1>
                                <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem;">Gestión y matriz principal aislada por Bloque.</p>

                                @if($bloqueActivo)
                                    @php
                                        // Buscar el bloque seleccionado para extraer su fecha/periodo
                                        $bloqueSeleccionado = collect($bloquesDisponibles)->firstWhere('numero_bloque', $bloqueActivo);
                                        $textoPeriodo = '';
                                        if($bloqueSeleccionado && isset($bloqueSeleccionado->id_periodo)) {
                                            $perObj = \App\Models\Certificados\CarSiaPeriodo::find($bloqueSeleccionado->id_periodo);
                                            if($perObj) {
                                                $fecha = \Carbon\Carbon::create()->year($perObj->anio)->month($perObj->mes);
                                                $textoPeriodo = ucfirst($fecha->locale('es')->monthName) . ' ' . $fecha->year;
                                            }
                                        }
                                    @endphp
                                    <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-pastel-primary text-primary border-0 fw-bold px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                            <i class="fas fa-cube me-1"></i> Trabajando en Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        @if($textoPeriodo)
                                            {{-- AQUI APLICAMOS LA CLASE CON EFECTO UX --}}
                                            <span class="badge bg-light text-muted border px-2 py-1 shadow-sm badge-hover-effect" style="font-size: 0.75rem;">
                                                <i class="far fa-calendar-alt me-1"></i> {{ $textoPeriodo }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Botones de Acción Derecha --}}
                        <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 mt-2 mt-md-0">
                            {{-- Botón de Recargar (Alerta y Generación Masiva fueron removidos de aquí) --}}
                            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                                <a href="{{ request()->fullUrl() }}" class="btn btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;" title="Actualizar datos">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </div>
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
                                    <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="fas fa-cubes fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Total Clientes</div>
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
                                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Procesados</div>
                                        <div class="fs-3 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($kpi['procesados'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                                    <div class="bg-pastel-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 55px; height: 55px;">
                                        <i class="fas fa-hourglass-half fs-4"></i>
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

                    {{-- 3.4 REPORTE DE CONFIGURACIONES, ALERTAS Y CERTIFICADOS (Acordeón y Controles) --}}
                    <div class="card card-custom shadow border-0 mb-4">
                        <div class="card-header bg-white border-bottom p-4" style="border-radius: 20px 20px 0 0;">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 w-100">
                                <div class="flex-grow-1 pe-md-3">
                                    <h6 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text);">
                                        <i class="fas fa-list-check text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                        Gestión de Configuraciones y Alertas de los Clientes
                                    </h6>
                                    {{-- Nota descriptiva de la sección --}}
                                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem; max-width: 600px;">
                                        Visualiza y gestiona las reglas de vencimiento. Puedes aplicar configuraciones generales a todo el lote de <strong>{{ $textoPeriodo ?? 'Mes Seleccionado' }}</strong> o asignar excepciones a operaciones específicas filtradas.
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-stretch align-items-md-end gap-2 ms-md-auto flex-shrink-0">
                                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-end gap-2 w-100">
                                        {{-- Botón Tabla --}}
                                        <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center w-100 w-md-auto text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfiguradas" aria-expanded="false" aria-controls="collapseConfiguradas">
                                            <i class="fas fa-table me-2"></i> Ver Tablas de Gestión
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse" id="collapseConfiguradas">
                            <div class="card-body p-0">

                                {{-- CONTENEDOR PRINCIPAL ACORDEÓN --}}
                                <div class="accordion accordion-flush" id="accordionMatrizTablas">

                                    {{-- ======================================================== --}}
                                    {{-- TABLA 1: PARÁMETROS Y CONFIGURACIONES --}}
                                    {{-- ======================================================== --}}
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="headingTabla1">
                                            <button class="accordion-button px-4 py-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla1" aria-expanded="true" aria-controls="collapseTabla1" style="font-size: 0.9rem;">
                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                    <div>
                                                        <i class="fas fa-sliders-h me-2"></i> Tabla 1: Parámetros y Configuraciones — Masivo y Selectivo
                                                    </div>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <span class="badge bg-white text-secondary border shadow-sm px-2 py-1 d-none d-sm-inline-block" style="font-size: 0.7rem;">
                                                            <i class="fas fa-info-circle text-primary me-1"></i> Modo interactivo
                                                        </span>
                                                        <span class="badge bg-white text-dark border shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                                            {{ count($configuracionesMasivas ?? []) + count($operacionesConfiguradas ?? []) }} Registros
                                                        </span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTabla1" class="accordion-collapse collapse show" aria-labelledby="headingTabla1" data-bs-parent="#accordionMatrizTablas">
                                            <div class="accordion-body bg-light p-3 p-md-4">
                                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                                    <div class="bg-white px-3 py-2 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                                        <span class="text-muted small"><i class="fas fa-hand-pointer text-primary me-1"></i> Haz clic en cualquier fila para ver detalles, vigencia y justificación</span>
                                                        @if($bloqueActivo)
                                                            <button type="button" class="btn btn-light border rounded-pill px-3 py-1 fw-bold shadow-sm d-flex align-items-center text-nowrap" data-bs-toggle="modal" data-bs-target="#modalConfiguracionIndex" style="font-size: 0.85rem;">
                                                                <i class="fas fa-cogs me-2 text-primary"></i> Asignar Parámetros
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <div class="table-responsive custom-scrollbar table-mobile-scroll" style="max-height: 450px; overflow-y: auto;">
                                                        <table class="table table-sm table-hover m-0 align-middle" style="font-size: 0.8rem;">
                                                            <thead class="table-light text-uppercase text-muted sticky-top shadow-sm" style="z-index: 10; font-size: 0.7rem;">
                                                                <tr>
                                                                    <th style="width: 3%; border-bottom: 1px solid #dee2e6;" class="text-center py-1"></th>
                                                                    <th style="width: 10%; border-bottom: 1px solid #dee2e6;" class="py-1">Radicado</th>
                                                                    <th style="width: 18%; border-bottom: 1px solid #dee2e6;" class="py-1">Cliente</th>
                                                                    <th style="width: 30%; border-bottom: 1px solid #dee2e6;" class="py-1">Regla de Vencimiento</th>
                                                                    <th style="width: 9%; text-align: center; border-bottom: 1px solid #dee2e6;" class="py-1">Frecuencia</th>
                                                                    <th style="width: 10%; text-align: center; border-bottom: 1px solid #dee2e6;" class="py-1" title="Estado general de la regla en el catálogo">Estado Regla</th>
                                                                    <th style="width: 6%; text-align: center; border-bottom: 1px solid #dee2e6;" class="py-1">Notif.</th>
                                                                    <th style="width: 14%; text-align: center; border-bottom: 1px solid #dee2e6;" class="pe-3 py-1" title="Estado de esta asignación específica">Estado Asignación</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $hayConfiguradas = false; $index = 0; @endphp

                                                                {{-- ========================================== --}}
                                                                {{-- 1. CONFIGURACIONES MASIVAS (LOTE)          --}}
                                                                {{-- ========================================== --}}
                                                                @foreach($configuracionesMasivas as $masiva)
                                                                    @php
                                                                        $hayConfiguradas = true; $index++;
                                                                        $configBase = $masiva->configuracionBase;
                                                                        $accionVenc = $configBase?->accionVencimiento;
                                                                        $parametros = $configBase?->parametros;
                                                                    @endphp
                                                                    <tr style="background-color: #fff; cursor: pointer; border-bottom: 1px solid #f1f3f5; transition: background-color 0.2s;" class="parent-row {{ !$masiva->estado_activo ? 'opacity-75 bg-light' : '' }}" onclick="toggleParametros('det-masiva-{{ $index }}')" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                                                                        <td class="text-center text-primary py-1"><i class="fas fa-chevron-circle-down opacity-50"></i></td>
                                                                        <td class="font-monospace text-primary fw-bold py-1"><i class="fas fa-layer-group me-1"></i> LOTE</td>
                                                                        <td class="fw-bold text-primary text-truncate py-1" title="Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}" style="max-width: 150px;"><i class="fas fa-users me-1"></i> Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</td>
                                                                        <td class="fw-bold text-dark text-truncate py-1" title="{{ $accionVenc?->nombre ?? 'N/A' }}" style="max-width: 220px;">{{ $accionVenc?->nombre ?? 'N/A' }}</td>
                                                                        <td class="text-center py-1">
                                                                            @if($configBase?->frecuencia_recordatorio_dias) <span class="badge bg-light text-dark border">Cada {{ $configBase->frecuencia_recordatorio_dias }} d</span>
                                                                            @else <span class="text-muted">—</span> @endif
                                                                        </td>
                                                                        <td class="text-center py-1">
                                                                            @if(isset($accionVenc?->estado))
                                                                                <span class="badge {{ $accionVenc->estado ? 'bg-white text-success border border-success' : 'bg-white text-danger border border-danger' }} px-2 py-1">
                                                                                    {{ $accionVenc->estado ? 'ACTIVA' : 'INACTIVA' }}
                                                                                </span>
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center py-1">
                                                                            @if($masiva->estado_notificacion) <span class="text-success"><i class="fas fa-bell"></i></span>
                                                                            @else <span class="text-muted"><i class="fas fa-bell-slash"></i></span> @endif
                                                                        </td>
                                                                        <td class="text-center pe-3 py-1">
                                                                            @if($masiva->trashed()) <span class="badge bg-danger text-white" style="font-size: 0.60rem;">ELIMINADA</span>
                                                                            @elseif(!$masiva->estado_activo) <span class="badge bg-secondary text-white" style="font-size: 0.60rem;">INACTIVA</span>
                                                                            @else <span class="badge bg-success text-white" style="font-size: 0.60rem;">VIGENTE</span> @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr id="det-masiva-{{ $index }}" style="display: none; background-color: #f4f6f8; box-shadow: inset 0 4px 6px -4px rgba(0,0,0,0.1);">
                                                                        <td colspan="8" class="p-0 border-0">
                                                                            @php
                                                                                $p = is_array($parametros) ? $parametros : (json_decode($parametros, true) ?? []);
                                                                                $claseMora = strtolower($p['clasificacion_mora'] ?? 'desconocido');
                                                                                $colorBadge = match($claseMora) {
                                                                                    'bueno' => 'bg-pastel-success text-success border-success',
                                                                                    'regular' => 'bg-pastel-info text-info border-info',
                                                                                    'atencion_especial' => 'bg-pastel-warning text-dark border-warning',
                                                                                    'restringido' => 'bg-pastel-danger text-danger border-danger',
                                                                                    'irregular' => 'bg-dark text-white border-dark',
                                                                                    default => 'bg-light text-secondary border-secondary'
                                                                                };
                                                                            @endphp

                                                                            <div class="py-2 py-md-3 pe-2 pe-md-4 ps-0 ps-md-1">
                                                                                <div class="ms-2 ms-md-5 bg-white rounded-3 shadow-sm border border-start-0 position-relative w-100" style="border-left: 4px solid #0d6efd !important;">
                                                                                    <div class="position-absolute text-primary d-none d-md-block" style="left: -20px; top: -15px;"><i class="fas fa-level-up-alt fa-rotate-90 fs-5 opacity-25"></i></div>

                                                                                    <div class="p-3 w-100">
                                                                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 pb-2 border-bottom gap-2">
                                                                                            <span class="fw-bold text-primary" style="font-size: 0.75rem;"><i class="fas fa-sliders-h me-1"></i> Parámetros de la Regla Masiva</span>
                                                                                            <span class="text-muted" style="font-size: 0.65rem;">ID Config: {{ $configBase?->id ?? 'N/A' }} | ID Asignación: {{ $masiva->id }}</span>
                                                                                        </div>
                                                                                        <div class="row g-3 align-items-start m-0 w-100">
                                                                                            {{-- COLUMNA 1: CLASIFICACIÓN --}}
                                                                                            <div class="col-12 col-md-3 position-relative p-0 pe-md-3">
                                                                                                <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                                <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Clasificación</span>
                                                                                                <span class="badge {{ $colorBadge }} border border-opacity-25 px-2 py-1 mb-2 text-wrap"><i class="fas fa-tag me-1"></i> {{ strtoupper(str_replace('_', ' ', $claseMora)) }}</span>
                                                                                                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                                                                                    <span class="text-dark"><i class="fas fa-calendar-times text-secondary me-1"></i> Max Mora: <strong>{{ $p['mora_dias_max'] ?? '0' }}</strong> d</span>
                                                                                                    <span class="text-dark"><i class="fas fa-hand-holding-heart text-secondary me-1"></i> Gracia: <strong>{{ $p['dias_gracia'] ?? '0' }}</strong> d</span>
                                                                                                </div>
                                                                                            </div>

                                                                                            {{-- COLUMNA 2: ACTIVADORES --}}
                                                                                            <div class="col-12 col-md-5 p-0 ps-md-3 mt-3 mt-md-0 position-relative pe-md-3">
                                                                                                <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                                <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Activadores</span>
                                                                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                                                                    @if(!empty($p['requiere_accion'])) <span class="badge bg-light text-dark border"><i class="fas fa-exclamation-circle text-warning me-1"></i> Acción Restrictiva</span> @endif
                                                                                                    @if(!empty($p['bloqueo_automatico'])) <span class="badge bg-pastel-danger border border-danger border-opacity-25"><i class="fas fa-ban me-1"></i> Bloqueo Cupo</span> @endif
                                                                                                    @if(!empty($p['notificacion_gerencia'])) <span class="badge bg-pastel-primary border border-primary border-opacity-25"><i class="fas fa-user-tie me-1"></i> Notifica Gcia.</span> @endif
                                                                                                    @if(!empty($p['incluir_historico_3_anos'])) <span class="badge bg-light text-secondary border"><i class="fas fa-history me-1"></i> Histórico 3A</span> @endif
                                                                                                    @if(empty($p['requiere_accion']) && empty($p['bloqueo_automatico']) && empty($p['notificacion_gerencia'])) <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-check text-success me-1"></i> Sin restricciones</span> @endif
                                                                                                </div>
                                                                                                @if(!empty($p['observacion_fase']))
                                                                                                    <div class="bg-light rounded p-2 text-muted fst-italic border mt-2 text-wrap text-break w-100" style="font-size: 0.7rem; word-wrap: break-word;">
                                                                                                        <i class="fas fa-info-circle text-primary me-1"></i> {{ $p['observacion_fase'] }}
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>

                                                                                            {{-- COLUMNA 3: DETALLES Y ESTADO --}}
                                                                                            <div class="col-12 col-md-4 p-0 ps-md-3 mt-3 mt-md-0">
                                                                                                <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Auditoría y Excepción</span>
                                                                                                <div class="d-flex flex-column gap-1 mb-2" style="font-size: 0.75rem;">
                                                                                                    <span class="text-dark"><i class="fas fa-user-circle text-primary me-1"></i> <strong>Responsable:</strong> {{ $masiva->usuario->name ?? 'N/A' }}</span>

                                                                                                    @php
                                                                                                        $vencido = $masiva->vigente_hasta && \Carbon\Carbon::parse($masiva->vigente_hasta)->isPast();
                                                                                                    @endphp
                                                                                                    <span class="text-dark"><i class="fas fa-calendar-check text-primary me-1"></i> <strong>Vigente Hasta:</strong>
                                                                                                        @if($masiva->vigente_hasta)
                                                                                                            <span class="{{ $vencido ? 'text-danger fw-bold' : '' }}">{{ \Carbon\Carbon::parse($masiva->vigente_hasta)->format('d/m/Y') }}</span>
                                                                                                        @else
                                                                                                            <span class="text-muted fst-italic">Indefinido</span>
                                                                                                        @endif
                                                                                                    </span>

                                                                                                    @if($masiva->justificacion)
                                                                                                        <span class="text-dark mt-1 d-block lh-sm"><i class="fas fa-comment-dots text-primary me-1"></i> <strong>Justificación:</strong> <br><span class="text-muted fst-italic">{{ $masiva->justificacion }}</span></span>
                                                                                                    @endif
                                                                                                </div>

                                                                                                {{-- SWITCH PARA CAMBIAR ESTADO --}}
                                                                                                <div class="mt-2 pt-2 border-top">
                                                                                                    <form action="{{ route('certificados.operaciones.config.toggle', $masiva->id) }}" method="POST" onclick="event.stopPropagation();">
                                                                                                        @csrf
                                                                                                        @method('PATCH')
                                                                                                        <div class="form-check form-switch d-flex align-items-center gap-2 m-0 p-0">
                                                                                                            <input class="form-check-input m-0" type="checkbox" role="switch" id="switchMasiva{{ $masiva->id }}" onchange="this.form.submit()" {{ $masiva->estado_activo ? 'checked' : '' }} style="cursor: pointer;">
                                                                                                            <label class="form-check-label mb-0 {{ $masiva->estado_activo ? 'text-success fw-bold' : 'text-danger fw-bold' }}" for="switchMasiva{{ $masiva->id }}" style="font-size: 0.75rem; cursor: pointer;">
                                                                                                                {{ $masiva->estado_activo ? 'Regla Activa' : 'Regla Inactiva' }}
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </form>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                {{-- ========================================== --}}
                                                                {{-- 2. CONFIGURACIONES INDIVIDUALES (EXCEPCIÓN)--}}
                                                                {{-- ========================================== --}}
                                                                @foreach($operacionesConfiguradas as $op)
                                                                    @php
                                                                        // Convertimos a colección para soportar múltiples excepciones por radicado
                                                                        $configs = $op->configuracion instanceof \Illuminate\Support\Collection
                                                                                    ? $op->configuracion
                                                                                    : collect([$op->configuracion])->filter();
                                                                    @endphp

                                                                    @foreach($configs as $conf)
                                                                        @php
                                                                            $hayConfiguradas = true;
                                                                            $configBaseOp = $conf->configuracionBase;
                                                                            $accionVencOp = $configBaseOp?->accionVencimiento;
                                                                            $parametrosOp = $configBaseOp?->parametros;

                                                                            // CORRECCIÓN: Accedemos al tercero directamente desde $op (la operación)
                                                                            $nombreCliente = trim(($op->tercero?->nom_ter ?? 'Sin Tercero') . ' ' . ($op->tercero?->apl1 ?? ''));
                                                                        @endphp

                                                                        <tr style="background-color: #fff; cursor: pointer; border-bottom: 1px solid #f1f3f5; transition: background-color 0.2s;" class="parent-row {{ !$conf->estado_activo ? 'opacity-75 bg-light' : '' }}" onclick="toggleParametros('det-op-{{ $conf->id }}')" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                                                                            <td class="text-center text-info py-1"><i class="fas fa-chevron-circle-down opacity-50"></i></td>
                                                                            <td class="font-monospace fw-bold py-1">{{ $op->numero_radicado ?? 'N/A' }}</td>
                                                                            <td class="text-truncate py-1" title="{{ $nombreCliente }}" style="max-width: 150px;">{{ $nombreCliente }}</td>
                                                                            <td class="fw-bold text-dark text-truncate py-1" title="{{ $accionVencOp?->nombre ?? 'N/A' }}" style="max-width: 220px;">{{ $accionVencOp?->nombre ?? 'N/A' }}</td>
                                                                            <td class="text-center py-1">
                                                                                @if($configBaseOp?->frecuencia_recordatorio_dias) <span class="badge bg-light text-dark border">Cada {{ $configBaseOp->frecuencia_recordatorio_dias }} d</span>
                                                                                @else <span class="text-muted">—</span> @endif
                                                                            </td>
                                                                            <td class="text-center py-1">
                                                                                @if(isset($accionVencOp?->estado))
                                                                                    <span class="badge {{ $accionVencOp->estado ? 'bg-white text-success border border-success' : 'bg-white text-danger border border-danger' }} px-2 py-1">
                                                                                        {{ $accionVencOp->estado ? 'ACTIVA' : 'INACTIVA' }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-muted">—</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center py-1">
                                                                                @if($conf->estado_notificacion) <span class="text-success"><i class="fas fa-bell"></i></span>
                                                                                @else <span class="text-muted"><i class="fas fa-bell-slash"></i></span> @endif
                                                                            </td>
                                                                            <td class="text-center pe-3 py-1">
                                                                                @if(method_exists($conf, 'trashed') && $conf->trashed()) <span class="badge bg-danger text-white" style="font-size: 0.60rem;">ELIMINADA</span>
                                                                                @elseif(!$conf->estado_activo) <span class="badge bg-secondary text-white" style="font-size: 0.60rem;">INACTIVA</span>
                                                                                @else <span class="badge bg-info text-white" style="font-size: 0.60rem;">EXCEPCIÓN</span> @endif
                                                                            </td>
                                                                        </tr>
                                                                        <tr id="det-op-{{ $conf->id }}" style="display: none; background-color: #f4f6f8; box-shadow: inset 0 4px 6px -4px rgba(0,0,0,0.1);">
                                                                            <td colspan="8" class="p-0 border-0">
                                                                                @php
                                                                                    $pOp = is_array($parametrosOp) ? $parametrosOp : (json_decode($parametrosOp, true) ?? []);
                                                                                    $claseMoraOp = strtolower($pOp['clasificacion_mora'] ?? 'desconocido');
                                                                                    $colorBadgeOp = match($claseMoraOp) {
                                                                                        'bueno' => 'bg-pastel-success text-success border-success',
                                                                                        'regular' => 'bg-pastel-info text-info border-info',
                                                                                        'atencion_especial' => 'bg-pastel-warning text-dark border-warning',
                                                                                        'restringido' => 'bg-pastel-danger text-danger border-danger',
                                                                                        'irregular' => 'bg-dark text-white border-dark',
                                                                                        default => 'bg-light text-secondary border-secondary'
                                                                                    };
                                                                                @endphp

                                                                                <div class="py-2 py-md-3 pe-2 pe-md-4 ps-0 ps-md-1">
                                                                                    <div class="ms-2 ms-md-5 bg-white rounded-3 shadow-sm border border-start-0 position-relative w-100" style="border-left: 4px solid #0dcaf0 !important;">
                                                                                        <div class="position-absolute text-info d-none d-md-block" style="left: -20px; top: -15px;"><i class="fas fa-level-up-alt fa-rotate-90 fs-5 opacity-25"></i></div>

                                                                                        <div class="p-3 w-100">
                                                                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 pb-2 border-bottom gap-2">
                                                                                                <span class="fw-bold text-info" style="font-size: 0.75rem;"><i class="fas fa-code-branch me-1"></i> Parámetros de Excepción (Rad: {{ $op->numero_radicado }})</span>
                                                                                                <span class="text-muted" style="font-size: 0.65rem;">ID Config: {{ $configBaseOp?->id ?? 'N/A' }} | ID Asignación: {{ $conf->id }}</span>
                                                                                            </div>
                                                                                            <div class="row g-3 align-items-start m-0 w-100">
                                                                                                {{-- COLUMNA 1: CLASIFICACIÓN --}}
                                                                                                <div class="col-12 col-md-3 position-relative p-0 pe-md-3">
                                                                                                    <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Clasificación</span>
                                                                                                    <span class="badge {{ $colorBadgeOp }} border border-opacity-25 px-2 py-1 mb-2 text-wrap"><i class="fas fa-tag me-1"></i> {{ strtoupper(str_replace('_', ' ', $claseMoraOp)) }}</span>
                                                                                                    <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                                                                                        <span class="text-dark"><i class="fas fa-calendar-times text-secondary me-1"></i> Max Mora: <strong>{{ $pOp['mora_dias_max'] ?? '0' }}</strong> d</span>
                                                                                                        <span class="text-dark"><i class="fas fa-hand-holding-heart text-secondary me-1"></i> Gracia: <strong>{{ $pOp['dias_gracia'] ?? '0' }}</strong> d</span>
                                                                                                    </div>
                                                                                                </div>

                                                                                                {{-- COLUMNA 2: ACTIVADORES --}}
                                                                                                <div class="col-12 col-md-5 p-0 ps-md-3 mt-3 mt-md-0 position-relative pe-md-3">
                                                                                                    <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Activadores</span>
                                                                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                                                                        @if(!empty($pOp['requiere_accion'])) <span class="badge bg-light text-dark border"><i class="fas fa-exclamation-circle text-warning me-1"></i> Acción Restrictiva</span> @endif
                                                                                                        @if(!empty($pOp['bloqueo_automatico'])) <span class="badge bg-pastel-danger border border-danger border-opacity-25"><i class="fas fa-ban me-1"></i> Bloqueo Cupo</span> @endif
                                                                                                        @if(!empty($pOp['notificacion_gerencia'])) <span class="badge bg-pastel-primary border border-primary border-opacity-25"><i class="fas fa-user-tie me-1"></i> Notifica Gcia.</span> @endif
                                                                                                        @if(!empty($pOp['incluir_historico_3_anos'])) <span class="badge bg-light text-secondary border"><i class="fas fa-history me-1"></i> Histórico 3A</span> @endif
                                                                                                        @if(empty($pOp['requiere_accion']) && empty($pOp['bloqueo_automatico']) && empty($pOp['notificacion_gerencia'])) <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-check text-success me-1"></i> Sin restricciones</span> @endif
                                                                                                    </div>
                                                                                                    @if(!empty($pOp['observacion_fase']))
                                                                                                        <div class="bg-light rounded p-2 text-muted fst-italic border mt-2 text-wrap text-break w-100" style="font-size: 0.7rem; word-wrap: break-word;">
                                                                                                            <i class="fas fa-info-circle text-info me-1"></i> {{ $pOp['observacion_fase'] }}
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>

                                                                                                {{-- COLUMNA 3: DETALLES Y ESTADO --}}
                                                                                                <div class="col-12 col-md-4 p-0 ps-md-3 mt-3 mt-md-0">
                                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Auditoría y Excepción</span>
                                                                                                    <div class="d-flex flex-column gap-1 mb-2" style="font-size: 0.75rem;">
                                                                                                        <span class="text-dark"><i class="fas fa-user-circle text-info me-1"></i> <strong>Responsable:</strong> {{ $conf->usuario->name ?? 'N/A' }}</span>

                                                                                                        @php
                                                                                                            $vencidoOp = $conf->vigente_hasta && \Carbon\Carbon::parse($conf->vigente_hasta)->isPast();
                                                                                                        @endphp
                                                                                                        <span class="text-dark"><i class="fas fa-calendar-check text-info me-1"></i> <strong>Vigente Hasta:</strong>
                                                                                                            @if($conf->vigente_hasta)
                                                                                                                <span class="{{ $vencidoOp ? 'text-danger fw-bold' : '' }}">{{ \Carbon\Carbon::parse($conf->vigente_hasta)->format('d/m/Y') }}</span>
                                                                                                            @else
                                                                                                                <span class="text-muted fst-italic">Indefinido</span>
                                                                                                            @endif
                                                                                                        </span>

                                                                                                        @if($conf->justificacion)
                                                                                                            <span class="text-dark mt-1 d-block lh-sm"><i class="fas fa-comment-dots text-info me-1"></i> <strong>Justificación:</strong> <br><span class="text-muted fst-italic">{{ $conf->justificacion }}</span></span>
                                                                                                        @endif
                                                                                                    </div>

                                                                                                    {{-- SWITCH PARA CAMBIAR ESTADO --}}
                                                                                                    <div class="mt-2 pt-2 border-top">
                                                                                                        <form action="{{ route('certificados.operaciones.config.toggle', $conf->id) }}" method="POST" onclick="event.stopPropagation();">
                                                                                                            @csrf
                                                                                                            @method('PATCH')
                                                                                                            <div class="form-check form-switch d-flex align-items-center gap-2 m-0 p-0">
                                                                                                                <input class="form-check-input m-0" type="checkbox" role="switch" id="switchOp{{ $conf->id }}" onchange="this.form.submit()" {{ $conf->estado_activo ? 'checked' : '' }} style="cursor: pointer;">
                                                                                                                <label class="form-check-label mb-0 {{ $conf->estado_activo ? 'text-success fw-bold' : 'text-danger fw-bold' }}" for="switchOp{{ $conf->id }}" style="font-size: 0.75rem; cursor: pointer;">
                                                                                                                    {{ $conf->estado_activo ? 'Excepción Activa' : 'Excepción Inactiva' }}
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach

                                                                @if(!$hayConfiguradas)
                                                                    <tr>
                                                                        <td colspan="8" class="text-center py-5 bg-white text-muted">
                                                                            <i class="fas fa-sliders-h mb-3 fs-3 text-primary opacity-50"></i><br>
                                                                            <span class="fw-bold text-dark">Sin configuraciones en este bloque.</span>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ======================================================== --}}
                                    {{-- TABLA 2: ALERTAS PROGRAMADAS --}}
                                    {{-- ======================================================== --}}
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="headingTabla2">
                                            <button class="accordion-button collapsed px-4 py-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla2" aria-expanded="false" aria-controls="collapseTabla2" style="font-size: 0.9rem;">
                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                    <div>
                                                        <i class="fas fa-bell me-2"></i> Tabla 2: Alertas Programadas — Masivo
                                                    </div>
                                                    <span class="badge bg-white text-dark border shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                                        {{ count($alertasBloqueActivo ?? []) }} Registros
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTabla2" class="accordion-collapse collapse" aria-labelledby="headingTabla2" data-bs-parent="#accordionMatrizTablas">
                                            <div class="accordion-body bg-light p-3 p-md-4">

                                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                                    {{-- BARRA DE ACCIÓN INTERNA --}}
                                                    <div class="bg-white px-3 py-2 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                                        <span class="text-muted small"><i class="fas fa-list-ul text-info me-1"></i> Listado de notificaciones pendientes</span>
                                                        @if($bloqueActivo)
                                                            <button type="button" class="btn btn-light border rounded-pill px-3 py-1 fw-bold shadow-sm d-flex align-items-center text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAlertaBloque" style="font-size: 0.85rem;">
                                                                <i class="fas fa-bell me-2 text-info"></i> Programar Alertas
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <div class="table-responsive custom-scrollbar table-mobile-scroll" style="max-height: 350px; overflow-y: auto;">
                                                        <table class="table table-sm table-hover m-0 align-middle" style="font-size: 0.8rem;">
                                                            <thead class="table-light text-uppercase text-muted sticky-top shadow-sm" style="z-index: 10; font-size: 0.7rem;">
                                                                <tr>
                                                                    <th style="width: 15%; border-bottom: 1px solid #dee2e6;" class="ps-3 py-1">Radicado / Lote</th>
                                                                    <th style="width: 30%; border-bottom: 1px solid #dee2e6;" class="py-1">Cliente / Tercero</th>
                                                                    <th style="width: 25%; border-bottom: 1px solid #dee2e6;" class="py-1">Tipo de Alerta</th>
                                                                    <th style="width: 10%; text-align: center; border-bottom: 1px solid #dee2e6;" class="py-1">Fec. Prog.</th>
                                                                    <th style="width: 10%; text-align: center; border-bottom: 1px solid #dee2e6;" class="py-1">Usuario</th>
                                                                    <th style="width: 10%; text-align: center; border-bottom: 1px solid #dee2e6;" class="pe-3 py-1">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $hayAlertas = false; @endphp

                                                                @foreach($alertasBloqueActivo as $alerta)
                                                                    @php
                                                                        $hayAlertas = true;
                                                                        $opAlerta = $alerta->operacion;
                                                                        $terceroAlerta = $opAlerta?->tercero;
                                                                        $nombreCompletoTercero = $terceroAlerta ? ($terceroAlerta->nom_ter . ' ' . $terceroAlerta->apl1) : 'Lote Completo';
                                                                        $nombreAlerta = $alerta->tipoAlerta?->nombre ?? 'N/A';
                                                                    @endphp
                                                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                                                        <td class="font-monospace text-info fw-bold ps-3 py-1">
                                                                            @if($alerta->id_car_sia_operaciones)
                                                                                {{ $opAlerta?->numero_radicado ?? 'N/A' }}
                                                                            @else
                                                                                <i class="fas fa-layer-group"></i> LOTE API
                                                                            @endif
                                                                        </td>
                                                                        <td class="fw-bold text-truncate py-1" style="max-width: 200px;" title="{{ $nombreCompletoTercero }}">
                                                                            @if($terceroAlerta)
                                                                                {{ $nombreCompletoTercero }}
                                                                            @else
                                                                                <span class="text-muted fst-italic">{{ $nombreCompletoTercero }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="fw-bold text-dark text-truncate py-1" style="max-width: 150px;" title="{{ $nombreAlerta }}">{{ $nombreAlerta }}</td>
                                                                        <td class="text-center py-1">
                                                                            <span class="badge bg-light text-dark border">
                                                                                {{ $alerta->fecha_programada ? $alerta->fecha_programada->format('d/m/Y') : 'Sin fecha' }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center py-1">
                                                                            <span class="text-muted">{{ $alerta->usuario?->name ?? 'Sistema' }}</span>
                                                                        </td>
                                                                        <td class="text-center pe-3 py-1">
                                                                            @if($alerta->procesado_en)
                                                                                <span class="badge bg-success bg-opacity-10 text-success">PROCESADA</span>
                                                                            @else
                                                                                <span class="badge bg-warning bg-opacity-10 text-dark">PROGRAMADA</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                @if(!$hayAlertas)
                                                                    <tr>
                                                                        <td colspan="6" class="text-center py-5 bg-white text-muted">
                                                                            <i class="fas fa-bell-slash mb-3 fs-3 text-muted opacity-50"></i><br>
                                                                            <span>No hay alertas programadas.</span>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ======================================================== --}}
                                    {{-- TABLA 3: TIPOS DE OPERACIÓN --}}
                                    {{-- ======================================================== --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTabla3">
                                            <button class="accordion-button collapsed px-4 py-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTabla3" aria-expanded="false" aria-controls="collapseTabla3" style="font-size: 0.9rem;">
                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                    <div>
                                                        <i class="fas fa-tags me-2"></i> Tabla 3: Tipos de Certificados — Masivo y Selectivo
                                                    </div>
                                                    <span class="badge bg-white text-dark border shadow-sm px-2 py-1" style="font-size: 0.7rem;">
                                                        {{ count($tiposBloqueActivo ?? []) }} Registros
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseTabla3" class="accordion-collapse collapse" aria-labelledby="headingTabla3" data-bs-parent="#accordionMatrizTablas">
                                            <div class="accordion-body bg-light p-3 p-md-4">

                                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                                    {{-- BARRA DE ACCIÓN INTERNA --}}
                                                    <div class="bg-white px-3 py-2 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                                        <span class="text-muted small"><i class="fas fa-file-contract text-danger me-1"></i> Tipologías asignadas</span>
                                                        @if($bloqueActivo)
                                                            <button type="button" class="btn btn-light border rounded-pill px-3 py-1 fw-bold shadow-sm d-flex align-items-center text-nowrap" data-bs-toggle="modal" data-bs-target="#modalMasivo" style="font-size: 0.85rem;">
                                                                <i class="fas fa-database me-2 text-danger"></i> Generar certificados
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <div class="table-responsive custom-scrollbar table-mobile-scroll" style="max-height: 350px; overflow-y: auto;">
                                                        <table class="table table-sm table-hover m-0 align-middle" style="font-size: 0.8rem;">
                                                            <thead class="table-light text-uppercase text-muted sticky-top shadow-sm" style="z-index: 10; font-size: 0.7rem;">
                                                                <tr>
                                                                    <th style="width: 10%; text-align: center; border-bottom: 1px solid #dee2e6;" class="ps-3 py-1">ID</th>
                                                                    <th style="width: 35%; border-bottom: 1px solid #dee2e6;" class="py-1">Nombre de Tipología</th>
                                                                    <th style="width: 40%; border-bottom: 1px solid #dee2e6;" class="py-1">Asignación / Alcance</th>
                                                                    <th style="width: 15%; text-align: center; border-bottom: 1px solid #dee2e6;" class="pe-3 py-1">Usuario</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $tieneTipos = false; @endphp

                                                                @foreach($tiposBloqueActivo as $tipoOp)
                                                                    @php
                                                                        $tieneTipos = true;
                                                                        $tipoCat = $tipoOp->tipo;
                                                                        $opAsignada = $tipoOp->operacion;
                                                                        $nombreTipo = $tipoCat?->nombre ?? 'N/A';
                                                                    @endphp
                                                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                                                        <td class="font-monospace text-center fw-bold ps-3 py-1">{{ $tipoOp->id }}</td>
                                                                        <td class="fw-bold text-dark text-truncate py-1" style="max-width: 250px;" title="{{ $nombreTipo }}">{{ $nombreTipo }}</td>
                                                                        <td class="text-muted text-truncate py-1" style="max-width: 300px;">
                                                                            @if($tipoOp->id_car_sia_operaciones)
                                                                                <span class="badge bg-light text-dark border" title="Asignado al Radicado: {{ $opAsignada?->numero_radicado ?? 'N/A' }}">Individual</span> Radicado: {{ $opAsignada?->numero_radicado ?? 'N/A' }}
                                                                            @else
                                                                                <span class="badge bg-primary bg-opacity-10 text-primary border-0" title="Aplica al bloque activo">Lote</span> API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center pe-3 py-1">
                                                                            <span class="text-muted">{{ $tipoOp->usuario?->name ?? 'Sistema' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                @if(!$tieneTipos)
                                                                    <tr>
                                                                        <td colspan="4" class="text-center py-5 bg-white text-muted">
                                                                            <i class="fas fa-folder-open mb-3 fs-3 text-primary opacity-50"></i><br>
                                                                            <span>No hay tipos registrados.</span>
                                                                        </td>
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
                                {{-- Fin Acordeón --}}

                            </div>
                        </div>

                    </div>
                    {{-- END 3.4 REPORTE CONFIGURACIONES --}}

                    {{-- 3.5 TABLA PRINCIPAL Y FILTROS --}}
                    <div class="card card-custom shadow border-0 mb-4">

                        {{-- Encabezado Integrado y Controles --}}
                        <div class="card-header bg-white border-bottom p-4" style="border-radius: 20px 20px 0 0;">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 w-100">

                                {{-- IZQUIERDA: Textos y Título --}}
                                <div class="flex-grow-1 pe-md-3">
                                    <h6 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text);">
                                        <i class="fas fa-list text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                        Operaciones del Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
                                    </h6>
                                    <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem; max-width: 650px;">
                                        Se muestran exclusivamente los clientes asociados al periodo de <strong>{{ $textoPeriodo ?? 'Mes Seleccionado' }}</strong> para la gestión de certificados. Revisa el estado, los últimos eventos y alertas de cada operación.
                                    </p>
                                </div>

                                {{-- DERECHA: Botones y Buscador Compacto --}}
                                <div class="d-flex flex-column align-items-stretch align-items-md-end gap-2 ms-md-auto flex-shrink-0" style="min-width: 320px;">

                                    {{-- Botones Superiores --}}
                                    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-md-end gap-2 w-100">
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 d-none d-md-inline-block shadow-sm">
                                            <i class="fas fa-hashtag text-muted me-1"></i> {{ number_format($operaciones->total(), 0, ',', '.') }} Registros
                                        </span>
                                        <button class="btn btn-light border rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center w-100 w-md-auto text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros" aria-expanded="{{ (request('anio') || request('buscar')) ? 'true' : 'false' }}" aria-controls="collapseFiltros">
                                            <i class="fas fa-filter text-muted me-1"></i> Filtros
                                        </button>
                                        {{-- Botón principal destacado (DINÁMICO SEGÚN FILTROS) --}}
                                        <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center w-100 w-md-auto text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTablaOperaciones" aria-expanded="{{ (request('buscar') || request('anio') || request('page')) ? 'true' : 'false' }}" aria-controls="collapseTablaOperaciones">
                                            <i class="fas fa-table me-2"></i> Ver Registros
                                        </button>
                                    </div>

                                    {{-- Buscador Colapsable Estilo "Ultra Pro" --}}
                                    <div class="collapse {{ (request('anio') || request('buscar')) ? 'show' : '' }} w-100 mt-1" id="collapseFiltros">
                                        <form action="{{ route('certificados.operaciones.index') }}" method="GET" class="bg-light p-2 rounded-4 border shadow-sm d-flex flex-column flex-md-row gap-2 w-100 align-items-stretch align-items-md-center" style="border-color: var(--c-border) !important;">
                                            <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

                                            <div class="input-group input-group-sm flex-grow-1 bg-white rounded-pill shadow-sm overflow-hidden border">
                                                <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-1"><i class="fas fa-search"></i></span>
                                                <input type="text" name="buscar" class="form-control border-0 ps-1 shadow-none" placeholder="Ej. Nombre, NIT..." value="{{ request('buscar') }}" style="font-size: 0.8rem; background: transparent;">
                                            </div>

                                            <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4 py-1 text-uppercase w-100 w-md-auto" style="font-size: 0.75rem; letter-spacing: 0.5px;">Buscar</button>

                                            @if(request('anio') || request('buscar'))
                                                <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn btn-white border rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mx-md-0 mt-2 mt-md-0" style="width: 34px; height: 34px; flex-shrink: 0;" title="Limpiar Filtros">
                                                    <i class="fas fa-times text-danger" style="font-size: 0.85rem;"></i>
                                                </a>
                                            @endif
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Contenedor Colapsable para la Tabla y Paginación (DINÁMICO SEGÚN FILTROS) --}}
                        <div class="collapse {{ (request('buscar') || request('anio') || request('page')) ? 'show' : '' }}" id="collapseTablaOperaciones">
                            {{-- Fondo gris suave que encapsula todo el contenido del despliegue --}}
                            <div class="card-body bg-light p-3 p-md-4 border-top">

                                {{-- Caja blanca con sombra que contiene la tabla --}}
                                <div class="bg-white border rounded-3 shadow-sm overflow-hidden">

                                    {{-- Barra de título interna (Opcional, para consistencia) --}}
                                    <div class="bg-white px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                        <span class="text-muted small"><i class="fas fa-list text-primary me-1"></i> Detalle de registros individuales</span>
                                    </div>

                                    {{-- Tabla de Registros --}}
                                    <div class="table-responsive custom-scrollbar table-mobile-scroll">
                                        <table class="table table-sm table-hover align-middle mb-0 text-nowrap" style="font-size: 0.8rem;">
                                            <thead class="table-light text-muted text-uppercase sticky-top" style="z-index: 10; font-size: 0.7rem;">
                                                <tr>
                                                    <th class="ps-3 py-2 text-secondary" style="font-weight: 600; width: 15%; border-bottom: 1px solid #dee2e6;">Radicado / Bloque</th>
                                                    <th class="py-2 text-secondary" style="font-weight: 600; width: 20%; border-bottom: 1px solid #dee2e6;">Cliente (Tercero)</th>
                                                    <th class="py-2 text-secondary" style="font-weight: 600; width: 15%; border-bottom: 1px solid #dee2e6;">Estado Actual</th>
                                                    <th class="py-2 text-secondary" style="font-weight: 600; width: 15%; border-bottom: 1px solid #dee2e6;">Último Evento</th>
                                                    <th class="py-2 text-secondary" style="font-weight: 600; width: 15%; border-bottom: 1px solid #dee2e6;">Última Alerta</th>
                                                    <th class="py-2 text-secondary" style="font-weight: 600; width: 12%; border-bottom: 1px solid #dee2e6;">Fecha</th>
                                                    <th class="pe-3 py-2 text-center text-secondary" style="font-weight: 600; width: 8%; border-bottom: 1px solid #dee2e6;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($operaciones as $operacion)
                                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                                        <td class="ps-3 py-2">
                                                            <div class="fw-bold text-dark">{{ $operacion->numero_radicado ?? 'N/A' }}</div>
                                                            <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-cube me-1 opacity-50"></i> API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                                                        </td>
                                                        <td class="py-2">
                                                            @if($operacion->tercero)
                                                                <div class="fw-bold text-dark">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                                                <div class="text-muted" style="font-size: 0.7rem;">NIT: {{ $operacion->tercero->cod_ter }}</div>
                                                            @else
                                                                <span class="badge bg-pastel-warning text-dark px-2 py-1 rounded-1"><i class="fas fa-exclamation-triangle me-1"></i> Sin Tercero</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            @php
                                                                $todosLosEstados = collect();
                                                                if(isset($operacion->estados)) $todosLosEstados = $todosLosEstados->concat($operacion->estados);
                                                                if(isset($operacion->estadosBloque)) $todosLosEstados = $todosLosEstados->concat($operacion->estadosBloque);
                                                                $ultimoEstado = $todosLosEstados->sortByDesc('created_at')->first();
                                                                $esEstadoBloque = $ultimoEstado && is_null($ultimoEstado->id_car_sia_operaciones);
                                                                $estadoNombre = $ultimoEstado && $ultimoEstado->estado ? $ultimoEstado->estado->nombre : 'Pendiente';
                                                                $clasePastel = match(strtolower(trim($estadoNombre))) {
                                                                    'aprobado', 'completado', 'vigente', 'procesado' => 'bg-pastel-success text-success',
                                                                    'rechazado', 'anulado' => 'bg-pastel-warning text-dark',
                                                                    'pendiente', 'nuevo', 'pendiente por procesar' => 'bg-light text-secondary border',
                                                                    default => 'bg-pastel-primary text-primary'
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $clasePastel }} rounded-1 px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                                <i class="fas {{ $esEstadoBloque ? 'fa-layer-group' : 'fa-info-circle' }} me-1"></i> {{ strtoupper($estadoNombre) }}
                                                            </span>
                                                        </td>
                                                        <td class="py-2">
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
                                                        <td class="py-2">
                                                            @php
                                                                $todasLasAlertas = collect();
                                                                if(isset($operacion->alertas)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertas);
                                                                if(isset($operacion->alertasBloque)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertasBloque);
                                                                $ultimaAlertaObj = $todasLasAlertas->sortByDesc('created_at')->first();
                                                                $esAlertaBloque = $ultimaAlertaObj && is_null($ultimaAlertaObj->id_car_sia_operaciones);
                                                            @endphp
                                                            @if($ultimaAlertaObj)
                                                                <span class="badge {{ $esAlertaBloque ? 'bg-pastel-primary text-primary' : 'bg-pastel-info text-info' }} rounded-1 px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                                                    <i class="fas {{ $esAlertaBloque ? 'fa-layer-group' : 'fa-bell' }} me-1"></i>
                                                                    {{ strtoupper($ultimaAlertaObj->tipoAlerta->nombre ?? 'DESCONOCIDA') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-minus opacity-50"></i></span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            <div class="text-gray-800 fw-bold">{{ $operacion->created_at->format('d/m/Y') }}</div>
                                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $operacion->created_at->format('h:i A') }}</div>
                                                        </td>
                                                        <td class="pe-3 py-2 text-center">
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

                                    {{-- Paginación Integrada a la caja blanca --}}
                                    @if($operaciones->hasPages() || $operaciones->total() > 0)
                                        <div class="bg-light border-top pt-3 pb-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                            <span class="text-muted text-center" style="font-size: 0.8rem;">
                                                Mostrando <span class="fw-bold text-dark">{{ $operaciones->firstItem() ?? 0 }}</span> a <span class="fw-bold text-dark">{{ $operaciones->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ number_format($operaciones->total(), 0, ',', '.') }}</span> registros
                                            </span>
                                            <div class="m-0 pagination-sm custom-pagination" style="font-size: 0.85rem; overflow-x: auto; max-width: 100%;">
                                                {{ $operaciones->appends(request()->query())->links('pagination::bootstrap-5') }}
                                            </div>
                                        </div>
                                    @endif
                                </div> {{-- Fin Caja Blanca --}}

                            </div> {{-- Fin Fondo Gris --}}
                        </div>
                    </div>
                    {{-- END 3.5 TABLA PRINCIPAL --}}

                </div>
                {{-- END REGION 3: COLUMNA IZQUIERDA --}}

                {{-- ==================================================================
                     REGION 4: COLUMNA DERECHA (SIDEBAR FIJO - 3 COLUMNAS)
                     (En móvil usa order-1 para ir arriba, en XL usa order-xl-2 para ir a la derecha)
                     ================================================================== --}}
                <div class="col-12 col-xl-3 order-1 order-xl-2 mb-3 mb-xl-0">
                    <div class="d-flex flex-column gap-3 sticky-sidebar">

                        {{-- 4.1 NAVEGACIÓN DE LOTES (Explorador) --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 380px;">
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
                                    <div class="mb-3">
                                        {{-- Toggle Año --}}
                                        <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2" onclick="toggleAcordeon('year-content-{{ $anio }}', this)">
                                            <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                                <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>
                                                <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                            </span>
                                        </div>

                                        {{-- Contenido Año --}}
                                        <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: none;">
                                            @foreach($periodosDelAnio as $periodo)
                                                @php
                                                    $nombreMes = \Carbon\Carbon::create()->month($periodo->mes)->locale('es')->monthName;
                                                    $esMesActual = ($anio == date('Y') && $periodo->mes == date('n'));

                                                    // Relacionar directo por el id_periodo
                                                    $bloquesDelMes = collect($bloquesDisponibles)->filter(function($b) use ($periodo) {
                                                        return isset($b->id_periodo) && $b->id_periodo == $periodo->id;
                                                    });
                                                @endphp

                                                <div class="d-flex flex-column rounded mb-1 shadow-sm {{ $esMesActual ? 'mes-actual-highlight' : '' }}" style="background: var(--c-surface); border: 1px solid var(--c-border);">

                                                    {{-- Toggle Mes --}}
                                                    <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center" onclick="toggleAcordeon('month-content-{{ $periodo->id }}', this)" style="cursor: pointer;">
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
                                                    <div class="month-content flex-column gap-1 p-2 pt-0 mt-1 border-top" id="month-content-{{ $periodo->id }}" style="display: none;">
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

                            <p class="text-muted mb-3" style="font-size:.8rem;">Registro de eventos y trazabilidad del lote actual.</p>

                            <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2">
                                @if($bloqueActivo && isset($historialBloque) && $historialBloque->count() > 0)
                                    <div class="position-relative ms-2" style="border-left: 2px solid var(--c-border);">
                                        @foreach($historialBloque as $log)
                                            <div class="position-relative mb-3 ps-3 pt-1">
                                                {{-- Punto del Timeline --}}
                                                <span class="position-absolute bg-primary rounded-circle border border-2 border-white shadow-sm" style="width: 12px; height: 12px; left: -7px; top: 8px;"></span>

                                                {{-- Tarjeta del Log --}}
                                                <div class="p-2 rounded bg-light border border-light shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: .75rem; line-height: 1.2;">
                                                            {{ $log->eventoAuditoria->nombre ?? 'Evento de Motor' }}
                                                        </span>
                                                        <span class="text-muted" style="font-size: .65rem; white-space: nowrap;">
                                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '—' }}
                                                        </span>
                                                    </div>

                                                    <div class="text-muted mb-2 d-flex justify-content-between align-items-center" style="font-size: .7rem;">
                                                        <div>
                                                            <i class="fas fa-user-circle me-1 opacity-50"></i>
                                                            <span class="fw-medium text-dark">{{ $log->usuario->name ?? 'Sistema Automático' }}</span>
                                                        </div>
                                                        @if($log->ip)
                                                            <span class="text-muted" style="font-size: .6rem; font-family: monospace;" title="IP de origen">
                                                                {{ $log->ip }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @php
                                                        // Decodificar JSON de forma segura
                                                        $datos = is_array($log->detalles_ejecucion) ? $log->detalles_ejecucion : (json_decode($log->detalles_ejecucion, true) ?? []);

                                                        // Detectar si usa la nueva estructura estandarizada
                                                        $esNuevoFormato = isset($datos['identificadores']) || isset($datos['metricas']);

                                                        // Filtrar valores nulos o cadenas vacías para no mostrar basura visual
                                                        $filtro = fn($v) => !is_null($v) && $v !== '';

                                                        $identificadores = array_filter($datos['identificadores'] ?? [], $filtro);
                                                        $metricas = array_filter($datos['metricas'] ?? [], fn($v) => !is_null($v) && $v !== '' && $v !== 0); // Ocultar ceros
                                                        $parametros = array_filter($datos['parametros'] ?? [], $filtro);
                                                        $contexto = array_filter($datos['contexto'] ?? [], $filtro);

                                                        $hayDetallesNuevos = count($identificadores) + count($metricas) + count($parametros) + count($contexto) > 0;
                                                    @endphp

                                                    {{-- Mostrar la descripción directamente para contexto rápido sin hacer clics --}}
                                                    @if(isset($datos['descripcion']))
                                                        <p class="text-muted mb-2 fst-italic border-start border-2 border-primary ps-2" style="font-size: .65rem; line-height: 1.2;">
                                                            {{ $datos['descripcion'] }}
                                                        </p>
                                                    @endif

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-white text-secondary border shadow-none" style="font-size: .6rem; padding: .2rem .4rem;">
                                                            <i class="fas fa-sitemap me-1"></i> {{ $log->origenEvento->nombre ?? 'API / Sistema' }}
                                                        </span>

                                                        @if(($esNuevoFormato && $hayDetallesNuevos) || (!$esNuevoFormato && count($datos) > 0))
                                                            <button type="button" class="btn btn-sm text-primary p-0 m-0 border-0 bg-transparent fw-medium d-flex align-items-center gap-1" style="font-size: .65rem;" onclick="document.getElementById('detalles-historial-{{ $log->id }}').classList.toggle('d-none')">
                                                                <i class="fas fa-search-plus"></i> Detalles
                                                            </button>
                                                        @endif
                                                    </div>

                                                    {{-- Contenedor de Detalles Oculto (Interfaz Minimalista) --}}
                                                    @if(($esNuevoFormato && $hayDetallesNuevos) || (!$esNuevoFormato && count($datos) > 0))
                                                        <div id="detalles-historial-{{ $log->id }}" class="d-none mt-2 pt-2 border-top border-light">

                                                            @if($esNuevoFormato)
                                                                {{-- NUEVO FORMATO ESTANDARIZADO --}}

                                                                {{-- Identificadores --}}
                                                                @if(count($identificadores) > 0)
                                                                    <div class="mb-1">
                                                                        <span class="d-block text-muted text-uppercase mb-1" style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.5px;">Identificadores</span>
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach($identificadores as $key => $val)
                                                                                <span class="badge bg-white text-dark border border-secondary border-opacity-25" style="font-size: 0.6rem; font-weight: 500;">
                                                                                    <span class="text-muted">{{ str_replace('_', ' ', ucfirst($key)) }}:</span> <span class="font-monospace fw-bold">{{ $val }}</span>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- Métricas --}}
                                                                @if(count($metricas) > 0)
                                                                    <div class="mb-1 mt-2">
                                                                        <span class="d-block text-muted text-uppercase mb-1" style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.5px;">Volumen y Métricas</span>
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach($metricas as $key => $val)
                                                                                @php $esValor = str_contains($key, 'valor'); @endphp
                                                                                <span class="badge bg-white text-primary border border-primary" style="font-size: 0.6rem; font-weight: 500;">
                                                                                    {{ str_replace('_', ' ', ucfirst($key)) }}: <span class="fw-bold">{{ $esValor ? '$' . number_format((float)$val, 2) : $val }}</span>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- Parámetros --}}
                                                                @if(count($parametros) > 0)
                                                                    <div class="mb-1 mt-2">
                                                                        <span class="d-block text-muted text-uppercase mb-1" style="font-size: 0.55rem; font-weight: 700; letter-spacing: 0.5px;">Parámetros Asignados</span>
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach($parametros as $key => $val)
                                                                                @php
                                                                                    $displayVal = $val;
                                                                                    if (is_numeric($val)) {
                                                                                        if ($key === 'tipo_asignado' || $key === 'tipo_certificado_global') {
                                                                                            $displayVal = collect($tipos ?? [])->firstWhere('id', $val)->nombre ?? $val;
                                                                                        } elseif ($key === 'estado_asignado') {
                                                                                            $displayVal = collect($estados ?? [])->firstWhere('id', $val)->nombre ?? $val;
                                                                                        } elseif ($key === 'alerta_asignada') {
                                                                                            $displayVal = collect($tiposAlerta ?? [])->firstWhere('id', $val)->nombre ?? $val;
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                                <span class="badge bg-light text-dark border border-info border-opacity-50" style="font-size: 0.6rem; font-weight: 500;">
                                                                                    <span class="text-info opacity-75 me-1"><i class="fas fa-tag"></i></span> {{ str_replace('_', ' ', ucfirst($key)) }}: <span class="fw-bold">{{ $displayVal }}</span>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                {{-- Contexto / Errores --}}
                                                                @if(count($contexto) > 0)
                                                                    <div class="mt-2 p-2 bg-white rounded border border-warning border-opacity-50 text-wrap text-break" style="font-size: 0.65rem;">
                                                                        @foreach($contexto as $key => $val)
                                                                            @php $esError = str_contains($key, 'error'); @endphp
                                                                            <div class="mb-1 {{ $esError ? 'text-danger fw-bold' : 'text-muted' }}">
                                                                                <span class="text-uppercase" style="font-size: 0.55rem;">{{ str_replace('_', ' ', $key) }}:</span>
                                                                                <span class="fst-italic">{{ $val }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif

                                                            @else
                                                                {{-- FORMATO ANTIGUO (Retrocompatibilidad plana) --}}
                                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                                    @foreach($datos as $key => $val)
                                                                        @if(!is_array($val) && !is_object($val) && $val !== '' && $val !== null)
                                                                            <span class="badge bg-white text-dark border border-secondary border-opacity-25" style="font-size: 0.6rem; font-weight: 500;">
                                                                                <span class="text-muted">{{ str_replace('_', ' ', ucfirst($key)) }}:</span> <span class="fw-bold">{{ $val }}</span>
                                                                            </span>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="formConfigIndex" action="{{ route('certificados.operaciones.config.masivo') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i> Asignar Reglas Estratégicas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                    <input type="hidden" name="buscar" value="{{ request('buscar') }}">

                    <div class="row g-4">
                        {{-- COLUMNA IZQUIERDA: Alcance y Nuevos Campos --}}
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark mb-3"><i class="fas fa-crosshairs text-muted me-1"></i> Alcance de la asignación:</label>

                            {{-- Opciones de Alcance --}}
                            <div class="form-check custom-radio border rounded-3 p-3 mb-2 shadow-sm {{ !request('buscar') ? 'bg-pastel-primary border-primary' : 'bg-white' }}" id="contRadioMasivo">
                                <input class="form-check-input ms-1 radio-alcance" type="radio" name="tipo_aplicacion" id="alcanceMasivo" value="masivo" data-ruta="{{ route('certificados.operaciones.config.masivo') }}" {{ !request('buscar') ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 w-100" for="alcanceMasivo" style="cursor: pointer;">
                                    <span class="d-block fw-bold text-dark">Todo el Lote (Masivo)</span>
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Aplica las reglas a todos los registros del bloque.</span>
                                </label>
                            </div>
                            <div class="form-check custom-radio border rounded-3 p-3 shadow-sm {{ request('buscar') ? 'bg-pastel-primary border-primary' : 'bg-white' }}" id="contRadioSelectivo">
                                <input class="form-check-input ms-1 radio-alcance" type="radio" name="tipo_aplicacion" id="alcanceSelectivo" value="selectivo" data-ruta="{{ route('certificados.operaciones.config.selectivo') }}" {{ request('buscar') ? 'checked' : '' }} {{ !request('buscar') ? 'disabled' : '' }}>
                                <label class="form-check-label ms-2 w-100" for="alcanceSelectivo" style="cursor: {{ request('buscar') ? 'pointer' : 'not-allowed' }};">
                                    <span class="d-block fw-bold {{ request('buscar') ? 'text-dark' : 'text-muted opacity-50' }}">Solo resultados filtrados</span>
                                    <span class="d-block text-muted {{ request('buscar') ? '' : 'opacity-50' }}" style="font-size: 0.75rem;">Aplica excepciones selectivas.</span>
                                </label>
                            </div>

                            {{-- Switch de Notificaciones --}}
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border mt-3 shadow-sm">
                                <div><span class="fw-bold d-block text-dark" style="font-size: 0.85rem;">Habilitar Notificaciones</span></div>
                                <div class="form-check form-switch fs-5 m-0"><input class="form-check-input" type="checkbox" name="estado_notificacion" checked></div>
                            </div>

                            {{-- NUEVOS CAMPOS: Justificación y Vigencia --}}
                            <div class="mt-4 p-3 bg-white border rounded-3 shadow-sm">
                                <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;"><i class="fas fa-calendar-alt text-primary me-1"></i> Vigente Hasta <span class="text-muted fw-normal">(Opcional)</span></label>
                                <input type="date" class="form-control form-control-sm mb-3" name="vigente_hasta" min="{{ date('Y-m-d') }}">

                                <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;"><i class="fas fa-comment-dots text-primary me-1"></i> Justificación <span class="text-muted fw-normal">(Opcional)</span></label>
                                <textarea class="form-control form-control-sm" name="justificacion" rows="2" placeholder="Motivo de la asignación o excepción..."></textarea>
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA: Selección Múltiple de Configuraciones --}}
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark mb-3"><i class="fas fa-tasks text-muted me-1"></i> Selecciona las reglas a aplicar (Múltiple):</label>
                            <div class="list-group custom-scrollbar shadow-sm" style="max-height: 480px; overflow-y: auto; border-radius: 12px;">
                                @if(isset($configuracionesBase) && $configuracionesBase->count() > 0)
                                    @foreach($configuracionesBase as $cfg)
                                        @php
                                            $p = $cfg->parametros ?? [];
                                            $claseMora = strtolower($p['clasificacion_mora'] ?? 'n/a');
                                            $diasMax = $p['mora_dias_max'] ?? '0';

                                            $badgeColor = match($claseMora) {
                                                'bueno' => 'bg-pastel-success text-success',
                                                'regular' => 'bg-pastel-info text-info',
                                                'atencion_especial' => 'bg-pastel-warning text-dark',
                                                'restringido' => 'bg-pastel-danger text-danger',
                                                'irregular' => 'bg-dark text-white',
                                                default => 'bg-light text-secondary'
                                            };
                                        @endphp
                                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-bottom" style="cursor: pointer;">
                                            <input class="form-check-input flex-shrink-0" type="checkbox" name="id_car_sia_config[]" value="{{ $cfg->id }}" style="font-size: 1.3rem;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $cfg->accionVencimiento->nombre ?? 'Sin Acción Definida' }}</span>
                                                    <span class="badge bg-light text-dark border"><i class="fas fa-clock text-muted"></i> {{ $cfg->frecuencia_recordatorio_dias }} d</span>
                                                </div>
                                                <div class="d-flex gap-2 mt-1">
                                                    <span class="badge {{ $badgeColor }} border border-opacity-25" style="font-size: 0.7rem;">
                                                        {{ strtoupper(str_replace('_', ' ', $claseMora)) }}
                                                    </span>
                                                    <span class="badge bg-white text-secondary border" style="font-size: 0.7rem;">
                                                        Mora Max: {{ $diasMax }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="text-center py-5 bg-light border-0">
                                        <i class="fas fa-folder-open fs-2 text-muted opacity-50 mb-2"></i>
                                        <p class="text-muted m-0">No hay configuraciones disponibles</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 flex-column flex-md-row">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm border w-100 w-md-auto mb-2 mb-md-0" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm w-100 w-md-auto m-0"><i class="fas fa-save me-1"></i> Guardar Asignaciones</button>
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
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 flex-column flex-md-row">
                        <button type="button" class="btn btn-light rounded-pill px-4 w-100 w-md-auto mb-2 mb-md-0" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white w-100 w-md-auto m-0">Programar Lote</button>
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
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 flex-column flex-md-row">
                        <button type="button" id="btnCancelMasivo" class="btn btn-light rounded-pill px-4 w-100 w-md-auto mb-2 mb-md-0" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnSubmitMasivo" class="btn btn-danger rounded-pill px-4 fw-bold text-white w-100 w-md-auto m-0">Procesar Lote</button>
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
        // Función para desplegar/ocultar los parámetros JSON al hacer clic en la fila
        function toggleParametros(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                if (row.style.display === 'none' || row.style.display === '') {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
    {{-- END REGION 6: SCRIPTS --}}

</x-base-layout>
