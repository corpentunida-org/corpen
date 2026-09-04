<x-base-layout>
    {{-- ==========================================
         1. ESTILOS COMBINADOS (Pasteles + Sidebar)
         ========================================== --}}
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

        /* Estilos Originales Pasteles */
        .bg-pastel-primary { background-color: var(--c-primary-soft) !important; color: var(--c-primary) !important; border: none; }
        .bg-pastel-info { background-color: var(--c-info-soft) !important; color: var(--c-info) !important; border: none; }
        .bg-pastel-success { background-color: var(--c-success-soft) !important; color: var(--c-success) !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-warning { background-color: var(--c-warning-soft) !important; color: var(--c-warning) !important; border: none; }

        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }

        .form-select-custom, .form-control-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 0.6rem 1rem; transition: all 0.3s ease;}
        .form-select-custom:focus, .form-control-custom:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.1); border-color: #4a90e2; }

        .btn-reload { background-color: #ffffff; border: 1px solid #e9ecef; color: #adb5bd; transition: all 0.3s ease; }
        .btn-reload:hover { background-color: var(--c-primary-soft); color: var(--c-primary); border-color: var(--c-primary-soft); }
        .btn-reload:hover i { transform: rotate(180deg); transition: transform 0.4s ease; }
        .btn-reload i { transition: transform 0.4s ease; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Barra de Progreso Minimalista */
        .progress-minimalist { height: 6px; width: 100%; background-color: #fee2e2; border-radius: 4px; overflow: hidden; position: relative; }
        .progress-minimalist::before { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background-color: var(--c-danger); animation: progress-slide 1.5s infinite ease-in-out; border-radius: 4px; }
        @keyframes progress-slide { 0% { left: -50%; width: 30%; } 50% { width: 60%; } 100% { left: 100%; width: 30%; } }

        /* ── SIDEBAR FIJO & ACORDEÓN ────────────────────────── */
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

        @media (max-width: 1199px) {
            .sticky-sidebar { position: static; min-height: auto; }
        }
    </style>

    <div class="app-container py-4" style="min-height: 100vh; background: var(--c-bg);">
        <div class="container-fluid px-xl-4">
            <div class="row g-4 m-0">

                {{-- ==========================================
                     COLUMNA IZQUIERDA: CONTENIDO PRINCIPAL (9)
                     ========================================== --}}
                <div class="col-12 col-xl-9">

                    {{-- Encabezado y Controles --}}
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

                        {{-- Controles Derechos --}}
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <a href="{{ request()->fullUrl() }}" class="btn btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </a>

                            @if($bloqueActivo)
                                <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlertaBloque">
                                    <i class="fas fa-bell me-2"></i> Alerta de Lote
                                </button>
                                <button type="button" class="btn btn-danger shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalMasivo">
                                    <i class="fas fa-database me-2"></i> Generación Masiva de Certificados
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Alertas --}}
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

                    {{-- Sección KPIs --}}
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

                    {{-- Tarjeta Única: Mensaje, Filtros + Tabla Principal --}}
                    <div class="card card-custom shadow-sm border-0 mb-4">

                        {{-- 1. Mensaje Informativo Superior (Callout) --}}
                        <div class="card-body pb-0 pt-4 px-4">
                            <div class="alert bg-pastel-primary border-0 rounded-4 mb-0 d-flex gap-3 shadow-sm" role="alert" style="padding: 1.25rem;">
                                <div class="mt-1">
                                    <i class="fas fa-info-circle fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">¿Por qué es necesario procesar este Lote?</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                                        Los registros que estás viendo se encuentran en una zona de contención estructurada por bloques (Lotes API).
                                        Para poder emitir los certificados finales, debes revisar las alertas, validar los terceros y utilizar el botón de
                                        <strong>Generación Masiva</strong>. Esto trasladará y ensamblará los datos para convertirlos en documentos oficiales.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Encabezado de la Tarjeta y Botón de Filtros --}}
                        <div class="card-header bg-white border-bottom p-4 pb-3 mt-2" style="border-radius: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <h6 class="fw-bold m-0" style="color: var(--c-text);">
                                    <i class="fas fa-list text-muted me-2"></i> Operaciones del Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
                                </h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-pastel-info text-dark rounded-pill px-3 py-2 d-none d-md-inline-block">
                                        <i class="fas fa-hashtag me-1"></i> {{ number_format($operaciones->total(), 0, ',', '.') }} Registros
                                    </span>

                                    {{-- Botón Toggle para Filtros --}}
                                    <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm border" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros" aria-expanded="{{ (request('anio') || request('buscar')) ? 'true' : 'false' }}" aria-controls="collapseFiltros">
                                        <i class="fas fa-filter text-muted me-1"></i> Filtros
                                    </button>
                                </div>
                            </div>

                            {{-- 3. Filtros Colapsables --}}
                            {{-- La clase 'show' se agrega automáticamente si hay una búsqueda activa para que no se cierre al recargar --}}
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
                                        <button type="submit" class="btn btn-pastel-primary flex-grow-1 fw-bold shadow-sm rounded-pill py-2">
                                            Buscar
                                        </button>
                                        @if(request('anio') || request('buscar'))
                                            <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn btn-white border fw-bold shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;" title="Limpiar Filtros">
                                                <i class="fas fa-times text-danger"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- 4. Contenido de la Tabla --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                                <thead class="text-muted small text-uppercase bg-white">
                                    <tr>
                                        <th class="ps-5 border-0 py-3 text-secondary">Radicado & Bloque</th>
                                        <th class="border-0 py-3 text-secondary">Cliente (Tercero)</th>
                                        <th class="border-0 py-3 text-secondary">Estado Actual</th>
                                        <th class="border-0 py-3 text-secondary">Último Evento</th>
                                        <th class="border-0 py-3 text-secondary">Última Alerta</th>
                                        <th class="border-0 py-3 text-secondary">Fecha Creación</th>
                                        <th class="border-0 text-end pe-5 py-3 text-secondary">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="px-3">
                                    @forelse($operaciones as $operacion)
                                        <tr class="bg-white">
                                            <td class="ps-5">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol-label bg-pastel-primary me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                                        <i class="fas fa-file-invoice fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-gray-800 fs-6">{{ $operacion->numero_radicado ?? 'N/A' }}</div>
                                                        <div class="text-muted small fw-semibold"><i class="fas fa-cube me-1 opacity-50"></i> API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($operacion->tercero)
                                                    <div class="fw-bold text-dark small">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                                    <div class="text-muted" style="font-size: 0.8rem;">NIT: {{ $operacion->tercero->cod_ter }}</div>
                                                @else
                                                    <span class="badge bg-pastel-warning px-3 py-2 rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Sin Tercero</span>
                                                @endif
                                            </td>
                                            <td>
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
                                                <span class="badge {{ $clasePastel }} rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                                    <i class="fas {{ $esEstadoBloque ? 'fa-layer-group' : 'fa-info-circle' }} me-1"></i> {{ strtoupper($estadoNombre) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $todosLosTipos = collect();
                                                    if(isset($operacion->tipos)) $todosLosTipos = $todosLosTipos->concat($operacion->tipos);
                                                    if(isset($operacion->tiposBloque)) $todosLosTipos = $todosLosTipos->concat($operacion->tiposBloque);
                                                    $ultimoTipoObj = $todosLosTipos->sortByDesc('created_at')->first();
                                                    $esTipoBloque = $ultimoTipoObj && is_null($ultimoTipoObj->id_car_sia_operaciones);
                                                    $tipoNombre = $ultimoTipoObj && $ultimoTipoObj->tipo ? $ultimoTipoObj->tipo->nombre : 'Sin Evento';
                                                @endphp
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                                    <i class="fas {{ $esTipoBloque ? 'fa-layer-group text-info' : 'fa-tag text-info' }} me-1"></i> {{ strtoupper($tipoNombre) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $todasLasAlertas = collect();
                                                    if(isset($operacion->alertas)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertas);
                                                    if(isset($operacion->alertasBloque)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertasBloque);
                                                    $ultimaAlertaObj = $todasLasAlertas->sortByDesc('created_at')->first();
                                                    $esAlertaBloque = $ultimaAlertaObj && is_null($ultimaAlertaObj->id_car_sia_operaciones);
                                                @endphp
                                                @if($ultimaAlertaObj)
                                                    <span class="badge {{ $esAlertaBloque ? 'bg-pastel-primary' : 'bg-pastel-info' }} rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                                        <i class="fas {{ $esAlertaBloque ? 'fa-layer-group' : 'fa-bell' }} me-1"></i>
                                                        {{ strtoupper($ultimaAlertaObj->tipoAlerta->nombre ?? 'DESCONOCIDA') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                                        <i class="fas fa-bell-slash me-1 opacity-50"></i> SIN ALERTA
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 fw-bold small">{{ $operacion->created_at->format('d/m/Y') }}</span>
                                                    <span class="text-muted" style="font-size: 0.8rem;">{{ $operacion->created_at->format('h:i A') }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-5">
                                                <a href="{{ route('certificados.operaciones.show', $operacion->id) }}" class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Ver Detalle">
                                                    <i class="fas fa-eye" style="color: var(--c-primary);"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-center px-4 py-5">
                                                    <div class="mb-3 p-4 rounded-circle d-inline-block" style="background-color: #f8f9fa;">
                                                        <i class="fas fa-search fs-1 text-muted opacity-50"></i>
                                                    </div>
                                                    <h5 class="fw-bold text-dark">Lote Vacío o Sin Resultados</h5>
                                                    <p class="text-muted">No se encontraron operaciones en el Lote API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }} que coincidan con tu búsqueda.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- 5. Paginación --}}
                        @if($operaciones->hasPages() || $operaciones->total() > 0)
                            <div class="card-footer bg-white border-top-0 pt-4 pb-4 px-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="border-radius: 0 0 20px 20px;">
                                <span class="text-muted small">
                                    Mostrando <span class="fw-bold text-dark">{{ $operaciones->firstItem() ?? 0 }}</span> a <span class="fw-bold text-dark">{{ $operaciones->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ number_format($operaciones->total(), 0, ',', '.') }}</span> operaciones
                                </span>
                                <div class="m-0">
                                    {{ $operaciones->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ==========================================
                     COLUMNA DERECHA: SIDEBAR FIJO (3)
                     ========================================== --}}
                <div class="col-12 col-xl-3">
                    <div class="d-flex flex-column gap-3 sticky-sidebar">

                        {{-- 1. Tarjeta de Navegación de Lotes --}}
                        <div class="card card-custom p-3 shadow-sm d-flex flex-column" style="flex: 1; min-height: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark m-0" style="font-size: 1.05rem;">
                                    <i class="far fa-folder-open text-muted me-2"></i> Explorador
                                </h5>
                            </div>
                            <p class="text-muted mb-3" style="font-size:.8rem;">Navega por los años y meses para cambiar el lote de trabajo.</p>

                            <div class="overflow-auto custom-scrollbar flex-grow-1 pe-2">
                                @php
                                    // 1. Consultar a la base de datos TODOS los periodos que estén HABILITADOS (abierto = 1)
                                    $periodosAbiertos = \App\Models\Certificados\CarSiaPeriodo::where('abierto', 1)
                                        ->orderBy('anio', 'desc')
                                        ->orderBy('mes', 'desc')
                                        ->get()
                                        ->groupBy('anio');
                                @endphp

                                @forelse($periodosAbiertos as $anio => $periodosDelAnio)
                                    @php
                                        // Verificamos si este año contiene el bloque en el que el usuario está parado actualmente
                                        $anioTieneActivo = collect($bloquesDisponibles)->filter(function($b) use ($anio) {
                                            return \Carbon\Carbon::parse($b->fecha_ejecucion)->format('Y') == $anio;
                                        })->contains('numero_bloque', $bloqueActivo);

                                        // Mantenemos abierto el año si es el actual o si contiene el lote activo
                                        $abrirAnio = ($anio == date('Y') || $anioTieneActivo);
                                    @endphp

                                    <div class="mb-3">
                                        {{-- Toggle de Año (Sin interruptores) --}}
                                        <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2 {{ $abrirAnio ? 'is-open' : '' }}" onclick="toggleAcordeon('year-content-{{ $anio }}', this)">
                                            <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                                <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>
                                                <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                            </span>
                                        </div>

                                        {{-- Contenido del Año --}}
                                        <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: {{ $abrirAnio ? 'flex' : 'none' }};">

                                            @foreach($periodosDelAnio as $periodo)
                                                @php
                                                    $nombreMes = \Carbon\Carbon::create()->month($periodo->mes)->locale('es')->monthName;
                                                    $esMesActual = ($anio == date('Y') && $periodo->mes == date('n'));

                                                    // Extraemos solo los lotes de $bloquesDisponibles que pertenecen a este año y mes
                                                    $bloquesDelMes = collect($bloquesDisponibles)->filter(function($b) use ($anio, $periodo) {
                                                        $fecha = \Carbon\Carbon::parse($b->fecha_ejecucion);
                                                        return $fecha->year == $anio && $fecha->month == $periodo->mes;
                                                    });

                                                    $mesTieneActivo = $bloquesDelMes->contains('numero_bloque', $bloqueActivo);
                                                @endphp

                                                <div class="d-flex flex-column rounded mb-1 shadow-sm {{ $esMesActual ? 'mes-actual-highlight' : '' }}" style="background: var(--c-surface); border: 1px solid var(--c-border);">

                                                    {{-- Toggle de Mes (Sin interruptores) --}}
                                                    <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center {{ $mesTieneActivo ? 'is-open' : '' }}"
                                                            onclick="toggleAcordeon('month-content-{{ $periodo->id }}', this)" style="cursor: pointer;">
                                                        <div>
                                                            <div class="fw-bold {{ $esMesActual ? 'text-primary' : 'text-dark' }}" style="font-size:.8rem; line-height:1.2;">
                                                                <i class="fas fa-chevron-right chevron-month text-muted me-1" style="font-size:.65rem; transition: transform 0.3s;"></i>
                                                                {{ ucfirst($nombreMes) }}
                                                                @if($esMesActual)
                                                                    <span class="badge bg-primary text-white ms-1 shadow-sm" style="font-size:.55rem;">ACTUAL</span>
                                                                @endif
                                                            </div>
                                                            <div class="ms-3 ps-1 mt-1" style="font-size:.65rem; color:var(--c-muted); font-family:monospace;">
                                                                {{ $bloquesDelMes->count() }} lotes
                                                            </div>
                                                        </div>

                                                        {{-- Etiqueta visual indicando que el mes está habilitado --}}
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
                                                                        class="block-link d-flex align-items-center justify-content-between text-decoration-none px-2 py-1 rounded {{ $esActivo ? 'active-block' : '' }}"
                                                                        style="font-size: .75rem;">
                                                                        <span class="fw-semibold d-flex align-items-center gap-2">
                                                                            <i class="fas fa-cube ico-cube" style="font-size: .65rem;"></i>
                                                                            Lote API-{{ str_pad($bloque->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                        </span>
                                                                        @if($esActivo)
                                                                            <i class="fas fa-check" style="font-size: .65rem; color: #fff;"></i>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="mt-1 text-center" style="font-size: .65rem; color: var(--c-muted);">
                                                                Sin lotes registrados
                                                            </div>
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

                        {{-- 2. Tarjeta de Auditoría / Actividad del Lote --}}
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

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ==========================================
         MODALES Y SCRIPTS (Se mantienen iguales)
         ========================================== --}}
    @if($bloqueActivo)
        {{-- Modal: Alerta de Lote --}}
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
                            <i class="fas fa-info-circle me-2"></i> Esta alerta se aplicará de forma general al lote <strong>API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</strong>.
                        </div>
                        <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                        <div class="mb-3">
                            <label for="id_car_sia_tipos_alerta" class="form-label fw-semibold text-muted">Tipo de alerta</label>
                            <select name="id_car_sia_tipos_alerta" id="id_car_sia_tipos_alerta" class="form-select bg-light border-0" required>
                                <option value="">Seleccione una alerta</option>
                                @isset($tiposAlerta)
                                    @foreach($tiposAlerta as $tipoAlerta)
                                        <option value="{{ $tipoAlerta->id }}">{{ $tipoAlerta->nombre }}</option>
                                    @endforeach
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

        {{-- Modal: Estructurar Lote --}}
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
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
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

        <script>
            // Lógica para barras de progreso del modal
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
        </script>
    @endif

    {{-- Script para los Acordeones del Menú Lateral --}}
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
