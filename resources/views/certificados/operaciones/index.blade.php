<x-base-layout>
    <style>
        /* Paleta de Colores Pasteles Soft UI */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }

        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }
        .badge-radicado { background-color: #f1f3f5; color: #495057; font-weight: 500; padding: 0.5rem 0.8rem; border-radius: 10px; display: inline-flex; align-items: center; }

        /* Estilos para los filtros */
        .form-select-custom, .form-control-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 0.6rem 1rem; transition: all 0.3s ease;}
        .form-select-custom:focus, .form-control-custom:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.1); border-color: #4a90e2; }

        /* Efecto minimalista para el botón de recarga */
        .btn-reload { background-color: #ffffff; border: 1px solid #e9ecef; color: #adb5bd; transition: all 0.3s ease; }
        .btn-reload:hover { background-color: #e7f0ff; color: #4a90e2; border-color: #e7f0ff; }
        .btn-reload:hover i { transform: rotate(180deg); transition: transform 0.4s ease; }
        .btn-reload i { transition: transform 0.4s ease; }
    </style>

    <div class="app-container py-4">

        {{-- Encabezado y SELECTOR DE BLOQUE AISLADO --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; background-color: #e7f0ff;">
                    <i class="fas fa-layer-group fs-4" style="color: #4a90e2;"></i>
                </div>
                <div>
                    <h1 class="h2 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">
                        Motor de Operaciones
                        <span class="badge bg-pastel-primary ms-2" style="font-size: 0.7rem; vertical-align: middle;">Lotes</span>
                    </h1>
                    <p class="text-muted mt-1 mb-0">Gestión y matriz principal aislada por Bloque.</p>
                </div>
            </div>

            <!-- CONTROLES DERECHOS: SELECTOR DE BLOQUE, RECARGAR Y ALERTAS/CERTIFICADOS -->
            <div class="d-flex align-items-center gap-3 flex-wrap">

                {{-- NUEVO BOTÓN: Recargar Vista (Sutil y Minimalista) --}}
                <a href="{{ request()->fullUrl() }}" class="btn btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;" title="Actualizar datos">
                    <i class="fas fa-sync-alt"></i>
                </a>

                @if($bloquesDisponibles->count() > 0)
                <form action="{{ route('certificados.operaciones.index') }}" method="GET" class="d-flex align-items-center bg-white p-2 border rounded-pill shadow-sm" style="min-width: 320px;">
                    <label class="fw-bold text-muted small mb-0 ms-3 me-2 text-nowrap"><i class="fas fa-filter me-1"></i> Lote Activo:</label>

                    @if(request('buscar')) <input type="hidden" name="buscar" value="{{ request('buscar') }}"> @endif
                    @if(request('anio')) <input type="hidden" name="anio" value="{{ request('anio') }}"> @endif

                    <select name="bloque" class="form-select border-0 shadow-none fw-bold" style="background-color: transparent; color: #4a90e2; cursor:pointer;" onchange="this.form.submit()">
                        @foreach($bloquesDisponibles as $b)
                            <option value="{{ $b->numero_bloque }}" {{ $bloqueActivo == $b->numero_bloque ? 'selected' : '' }}>
                                Lote BLQ-{{ str_pad($b->numero_bloque, 4, '0', STR_PAD_LEFT) }} ({{ \Carbon\Carbon::parse($b->fecha_ejecucion)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif

                {{-- ACCIONES DE LOTE (MUESTRA SOLO SI HAY BLOQUE SELECCIONADO) --}}
                @if($bloqueActivo)
                    {{-- BOTÓN: Alerta de Lote --}}
                    <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlertaBloque">
                        <i class="fas fa-bell me-2"></i> Alerta de Lote
                    </button>

                    {{-- NUEVO BOTÓN: Generar Certificados Masivos --}}
                    <form action="{{ route('certificados.operaciones.pdf_masivo') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="numero_bloque" value="{{ $bloqueActivo }}">
                        <button type="submit" class="btn btn-danger shadow-sm rounded-pill px-4 py-2 fw-bold text-white d-flex align-items-center">
                            <i class="fas fa-database me-2"></i> Estructurar Lote
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Alertas del Sistema --}}
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

        {{-- ========================================== --}}
        {{-- SECCIÓN DE KPIs MINIMALISTAS --}}
        {{-- ========================================== --}}
        @if($bloqueActivo)
        <div class="row g-3 mb-4">
            <!-- KPI: Total -->
            <div class="col-12 col-md-4">
                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                        <i class="fas fa-cubes fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Lote</div>
                        <div class="fs-3 fw-bolder" style="color: #2c3e50; line-height: 1;">{{ number_format($kpi['total'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- KPI: Procesados -->
            <div class="col-12 col-md-4">
                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="bg-pastel-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                        <i class="fas fa-check-double fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Procesados / Aprobados</div>
                        <div class="fs-3 fw-bolder" style="color: #2c3e50; line-height: 1;">{{ number_format($kpi['procesados'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- KPI: Pendientes -->
            <div class="col-12 col-md-4">
                <div class="card card-custom h-100 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="bg-pastel-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                        <i class="fas fa-hourglass-half fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Pendientes</div>
                        <div class="fs-3 fw-bolder" style="color: #2c3e50; line-height: 1;">{{ number_format($kpi['pendientes'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tarjeta de Filtros Secundarios --}}
        <div class="card card-custom shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('certificados.operaciones.index') }}" method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

                    {{-- Filtro Año --}}
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-1"><i class="far fa-calendar-alt me-1"></i> Año Creación</label>
                        <select name="anio" class="form-select form-select-custom" onchange="this.form.submit()">
                            <option value="">Todos los años</option>
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buscador Libre --}}
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold small text-uppercase mb-1"><i class="fas fa-search me-1"></i> Buscar Cliente / Radicado (En Lote {{ $bloqueActivo }})</label>
                        <input type="text" name="buscar" class="form-control form-control-custom" placeholder="Ej. 10102030..." value="{{ request('buscar') }}">
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-pastel-primary flex-grow-1 fw-bold shadow-sm rounded-pill py-2">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>

                        @if(request('anio') || request('buscar'))
                            <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn btn-light fw-bold shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; flex-shrink: 0;" title="Limpiar Filtros">
                                <i class="fas fa-times text-danger"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla Principal --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center" style="border-radius: 20px 20px 0 0;">
                <h6 class="fw-bold m-0" style="color: #2c3e50;"><i class="fas fa-list text-muted me-2"></i> Operaciones del Lote BLQ-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}</h6>
                <span class="badge bg-pastel-info text-dark rounded-pill px-3 py-2"><i class="fas fa-hashtag me-1"></i> {{ number_format($operaciones->total(), 0, ',', '.') }} Registros Listados</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-5 border-0 py-3">Radicado & Bloque</th>
                            <th class="border-0 py-3">Cliente (Tercero)</th>
                            <th class="border-0 py-3">Estado Actual</th>
                            <th class="border-0 py-3">Último Evento</th>
                            <th class="border-0 py-3">Última Alerta</th>
                            <th class="border-0 py-3">Fecha Creación</th>
                            <th class="border-0 text-end pe-5 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($operaciones as $operacion)
                        <tr class="bg-white">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-3">
                                        <div class="symbol-label bg-pastel-primary" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                            <i class="fas fa-file-invoice fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">{{ $operacion->numero_radicado ?? 'N/A' }}</div>
                                        <div class="text-muted small fw-semibold">
                                            <i class="fas fa-cube me-1 opacity-50"></i> BLQ-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                        </div>
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

                            {{-- 1. COLUMNA: ESTADO ACTUAL --}}
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
                                <span class="badge {{ $clasePastel }} rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;" title="{{ $esEstadoBloque ? 'Estado general asignado por Lote' : 'Estado específico del cliente' }}">
                                    <i class="fas {{ $esEstadoBloque ? 'fa-layer-group' : 'fa-info-circle' }} me-1"></i> {{ strtoupper($estadoNombre) }}
                                </span>
                            </td>

                            {{-- 2. COLUMNA: ÚLTIMO TIPO DE EVENTO --}}
                            <td>
                                @php
                                    $todosLosTipos = collect();
                                    if(isset($operacion->tipos)) $todosLosTipos = $todosLosTipos->concat($operacion->tipos);
                                    if(isset($operacion->tiposBloque)) $todosLosTipos = $todosLosTipos->concat($operacion->tiposBloque);

                                    $ultimoTipoObj = $todosLosTipos->sortByDesc('created_at')->first();
                                    $esTipoBloque = $ultimoTipoObj && is_null($ultimoTipoObj->id_car_sia_operaciones);
                                    $tipoNombre = $ultimoTipoObj && $ultimoTipoObj->tipo ? $ultimoTipoObj->tipo->nombre : 'Sin Evento';
                                @endphp
                                <span class="badge bg-light text-dark border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;" title="{{ $esTipoBloque ? 'Evento general asignado por Lote' : 'Evento específico del cliente' }}">
                                    <i class="fas {{ $esTipoBloque ? 'fa-layer-group text-info' : 'fa-tag text-info' }} me-1"></i> {{ strtoupper($tipoNombre) }}
                                </span>
                            </td>

                            {{-- 3. COLUMNA: ÚLTIMA ALERTA --}}
                            <td>
                                @php
                                    $todasLasAlertas = collect();
                                    if(isset($operacion->alertas)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertas);
                                    if(isset($operacion->alertasBloque)) $todasLasAlertas = $todasLasAlertas->concat($operacion->alertasBloque);

                                    $ultimaAlertaObj = $todasLasAlertas->sortByDesc('created_at')->first();
                                    $esAlertaBloque = $ultimaAlertaObj && is_null($ultimaAlertaObj->id_car_sia_operaciones);
                                @endphp

                                @if($ultimaAlertaObj)
                                    <span class="badge {{ $esAlertaBloque ? 'bg-pastel-primary' : 'bg-pastel-info' }} rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;" title="{{ $esAlertaBloque ? 'Alerta general programada por Lote' : 'Alerta específica del cliente' }}">
                                        <i class="fas {{ $esAlertaBloque ? 'fa-layer-group' : 'fa-bell' }} me-1"></i>
                                        {{ strtoupper($ultimaAlertaObj->tipoAlerta->nombre ?? 'DESCONOCIDA') }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
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
                                <a href="{{ route('certificados.operaciones.show', $operacion->id) }}"
                                   class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;"
                                   title="Ver Detalle y Trazabilidad">
                                    <i class="fas fa-eye" style="color: #4a90e2;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="text-center px-4 py-5">
                                    <div class="mb-3 p-4 rounded-circle d-inline-block" style="background-color: #f8f9fa;">
                                        <i class="fas fa-search fs-1 text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold" style="color: #2c3e50;">Bloque Vacío o Sin Resultados</h5>
                                    <p class="text-muted">No se encontraron operaciones en el Lote BLQ-{{ $bloqueActivo }} que coincidan con tu búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
         MODAL: PROGRAMAR ALERTA DE LOTE
         ========================================== --}}
    @if($bloqueActivo)
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
                        <i class="fas fa-info-circle me-2"></i> Esta alerta se aplicará de forma general al lote <strong>BLQ-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}</strong>. No quedará asignada a un cliente individual.
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
                            @else
                                <option value="" disabled>Falta variable $tiposAlerta desde el controlador.</option>
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
    @endif
</x-base-layout>
