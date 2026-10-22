<x-base-layout>
    @section('titlepage', 'Panel Maestro de Inmuebles - RSV')

    {{-- Formateador Global de Moneda (Solo visual, es seguro dejarlo aquí) --}}
    @php
        $fmtMoney = function ($n) {
            if ($n === null || $n === 0) return '—';
            if ($n >= 1000000) return '$' . number_format($n / 1000000, 1) . 'M';
            if ($n >= 1000)    return '$' . number_format($n / 1000, 1) . 'k';
            return '$' . number_format((float) $n, 0);
        };
    @endphp

    {{-- ========================================================= --}}
    {{-- NOTIFICACIONES DEL SISTEMA                                --}}
    {{-- ========================================================= --}}
    @include('rsv.components.alert')

    {{-- ============================================================================== --}}
    {{-- ESTILOS DE FUSIÓN (PADRE - HIJO) --}}
    {{-- Mantiene el efecto "solapado" verde usando la paleta nativa de Bootstrap 5 --}}
    {{-- ============================================================================== --}}
    <style>
        /* 1. Contenedor de las pestañas principales (Padre) */
        .nav-fusion {
            border-bottom: 1px solid #badbcc !important;
        }

        /* 2. Pestañas en estado normal */
        .nav-fusion .nav-link {
            color: #6c757d;
            font-weight: 600;
            border: 1px solid transparent;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            margin-bottom: -1px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        /* 3. Hover en las pestañas */
        .nav-fusion .nav-link:hover {
            color: #198754;
            border-color: #e9ecef #e9ecef #badbcc;
        }

        /* 4. Pestaña ACTIVA */
        .nav-fusion .nav-link.active {
            color: #0f5132 !important;
            background-color: #d1e7dd !important;
            border-color: #badbcc #badbcc #d1e7dd #badbcc !important;
        }

        /* 5. Barra del Submenú (Hijo) */
        .subnav-fusion {
            background-color: #d1e7dd;
            border-bottom: 1px solid #badbcc;
            border-top: none;
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            padding: 0.6rem 1rem;
            margin-bottom: 1.5rem;
        }

        /* 6. Botones (Píldoras) dentro del submenú */
        .sub-pills .nav-link {
            color: #146c43;
            background-color: rgba(255, 255, 255, 0.65);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 0.8rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .sub-pills .nav-link:hover {
            background-color: #fff;
            color: #0f5132;
            border-color: #badbcc;
        }
        .sub-pills .nav-link.active {
            background-color: #198754 !important;
            color: #fff !important;
            border-color: #198754 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* 7. Efecto Hover Minimalista para tarjetas KPI */
        .kpi-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.05) !important;
        }
    </style>

    <div class="container-fluid py-4">

        {{-- ======================================================================= --}}
        {{-- 1. SELECTOR MAESTRO DE INMUEBLES                                        --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">

                    {{-- Información del Módulo --}}
                    <div class="col-12 col-lg-5 text-center text-lg-start">
                        <span class="text-uppercase text-secondary fw-semibold small d-block mb-1" style="letter-spacing: 1px;">
                            Panel Conector RSV
                        </span>
                        <h6 class="fw-bold mb-1 text-dark">Gestión por Inmueble</h6>
                        <p class="mb-0 text-muted small">Seleccione una propiedad para cargar su ecosistema o elija ver todas.</p>
                    </div>

                    {{-- Formulario de Selección --}}
                    <div class="col-12 col-lg-7">
                        <form action="{{ route('rsv.admin.dashboard') }}" method="GET" class="d-flex w-100">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted border-secondary-subtle">
                                    <i class="bi bi-building"></i>
                                </span>

                                <select class="form-select border-secondary-subtle fw-semibold text-secondary"
                                        name="inmueble_id"
                                        onchange="this.form.submit()"
                                        aria-label="Seleccionar Inmueble">

                                    <option value="" class="text-muted">-- Seleccione una propiedad para operar --</option>
                                    <option value="todos" {{ ($inmuebleId ?? 'todos') === 'todos' ? 'selected' : '' }}>
                                        Ver todas las propiedades (Panel Global)
                                    </option>

                                    @isset($listaInmuebles)
                                        @foreach($listaInmuebles as $opcion)
                                            <option value="{{ $opcion->id }}" {{ request('inmueble_id', $inmuebleId ?? '') == $opcion->id ? 'selected' : '' }}>
                                                {{ $opcion->name }}{{ isset($opcion->city) && $opcion->city ? ' (' . $opcion->city . ')' : '' }} - {{ $opcion->active ? 'Activo' : 'Inactivo' }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <noscript>
                                <button type="submit" class="btn btn-sm btn-outline-success ms-2 fw-semibold">Cargar</button>
                            </noscript>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL A: VISTA GLOBAL (Todos los Inmuebles)                       --}}
        {{-- ======================================================================= --}}
        @if(($inmuebleId ?? 'todos') === 'todos')
            <div class="card shadow-sm border-0 mb-4 bg-white" data-mode="global">
                {{-- Cabecera Panel Global --}}
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 pb-3 border-bottom border-light">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold small mb-1"><i class="bi bi-grid-3x3-gap me-1"></i> Panel Global</div>
                        <h6 class="fw-bold text-dark mb-1">Gestión Global del Sistema</h6>
                        <p class="text-muted small mb-0">Vista consolidada de propiedades, reservas, calendario, finanzas y auditoría.</p>
                    </div>
                    <span class="badge bg-light text-secondary border rounded-pill fw-normal py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                        <span class="vr mx-1"></span>
                        <i class="bi bi-calendar3 small text-muted"></i>
                        <span style="font-size: 0.8rem;">{{ now()->format('d/m/Y h:i A') }}</span>
                    </span>
                </div>

                {{-- NAVEGACIÓN PRINCIPAL (PADRES GLOBAL) --}}
                <div class="card-header bg-white p-0 border-0">
                    <ul class="nav nav-tabs nav-fusion px-2 pt-2 tab-memory-parent" id="globalAdminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#global-inmuebles" type="button" role="tab">
                                <i class="bi bi-buildings me-1"></i> Propiedades <span class="badge bg-light text-secondary ms-1">{{ $tabTotalInmuebles ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-reservas" type="button" role="tab">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas <span class="badge bg-light text-secondary ms-1">{{ $tabReservas ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-calendario" type="button" role="tab">
                                <i class="bi bi-calendar3 me-1"></i> Bloqueos <span class="badge bg-light text-secondary ms-1">{{ $tabBloqueos ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-finanzas" type="button" role="tab">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas <span class="badge bg-light text-secondary ms-1">{{ $fmtMoney($tabFinanzas ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-auditoria" type="button" role="tab">
                                <i class="bi bi-shield-check me-1"></i> Auditoría <span class="badge bg-light text-secondary ms-1">{{ $tabAuditoria ?? 0 }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- CONTENIDO DE LAS PESTAÑAS GLOBALES --}}
                <div class="card-body p-0">
                    <div class="tab-content" id="globalAdminTabsContent">

                        {{-- GLOBAL - TAB 1: PROPIEDADES --}}
                        <div class="tab-pane fade show active" id="global-inmuebles" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Inmobiliario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="inmueblesSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#sub-show" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-catalogo" type="button" role="tab"><i class="bi bi-grid-3x3 me-1"></i> Catálogo</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tarifa" type="button" role="tab"><i class="bi bi-cash-coin me-1"></i> Tarifas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-galeria" type="button" role="tab"><i class="bi bi-images me-1"></i> Galería</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tipo" type="button" role="tab"><i class="bi bi-tags me-1"></i> Tipos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="inmueblesSubContent">
                                <div class="tab-pane fade show active" id="sub-show" role="tabpanel">@include('rsv.admin.partials.tab-inmuebles')</div>

                                {{-- FIX: Pasamos la variable disponible o un array vacío para evitar el error --}}
                                <div class="tab-pane fade" id="sub-catalogo" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.catalogo', ['inmuebles' => $inmueblesGrid ?? $listaInmuebles ?? []])
                                </div>

                                <div class="tab-pane fade" id="sub-tarifa" role="tabpanel">@include('rsv.admin.partials.inmuebles.tarifa')</div>
                                <div class="tab-pane fade" id="sub-galeria" role="tabpanel">@include('rsv.admin.partials.inmuebles.galeria')</div>
                                <div class="tab-pane fade" id="sub-tipo" role="tabpanel">@include('rsv.admin.partials.inmuebles.tipo')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 2: RESERVAS --}}
                        <div class="tab-pane fade" id="global-reservas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Reservas:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="reservasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#res-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-reservas" type="button" role="tab"><i class="bi bi-journal-check me-1"></i> Reservas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-huespedes" type="button" role="tab"><i class="bi bi-people me-1"></i> Huéspedes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-status" type="button" role="tab"><i class="bi bi-flag me-1"></i> Estados</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-origenes" type="button" role="tab"><i class="bi bi-box-arrow-in-down me-1"></i> Orígenes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-historial" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Historial</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="reservasSubContent">
                                <div class="tab-pane fade show active" id="res-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-resrvas')</div>
                                <div class="tab-pane fade" id="res-sub-reservas" role="tabpanel">@include('rsv.admin.partials.reservas.reservas')</div>
                                <div class="tab-pane fade" id="res-sub-huespedes" role="tabpanel">@include('rsv.admin.partials.reservas.reserva_huespedt')</div>
                                <div class="tab-pane fade" id="res-sub-status" role="tabpanel">@include('rsv.admin.partials.reservas.status')</div>
                                <div class="tab-pane fade" id="res-sub-origenes" role="tabpanel">@include('rsv.admin.partials.reservas.origen_reserva')</div>
                                <div class="tab-pane fade" id="res-sub-historial" role="tabpanel">@include('rsv.admin.partials.reservas.historial_estado')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 3: CALENDARIO (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="global-calendario" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Calendario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="calendarioSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#cal-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cal-sub-bloqueos" type="button" role="tab"><i class="bi bi-calendar-x me-1"></i> Bloqueos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="calendarioSubContent">
                                <div class="tab-pane fade show active" id="cal-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-calendario')</div>
                                <div class="tab-pane fade" id="cal-sub-bloqueos" role="tabpanel">@include('rsv.admin.partials.calendario.bloqueos')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 4: FINANZAS (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="global-finanzas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="finanzasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#fin-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#fin-sub-transacciones" type="button" role="tab"><i class="bi bi-receipt me-1"></i> Transacciones</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#fin-sub-pasarelas" type="button" role="tab"><i class="bi bi-credit-card me-1"></i> Pasarelas</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="finanzasSubContent">
                                <div class="tab-pane fade show active" id="fin-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-finanzas')</div>
                                <div class="tab-pane fade" id="fin-sub-transacciones" role="tabpanel">@include('rsv.admin.partials.finanzas.transacciones')</div>
                                <div class="tab-pane fade" id="fin-sub-pasarelas" role="tabpanel">@include('rsv.admin.partials.finanzas.pasarela')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 5: AUDITORÍA (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="global-auditoria" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Auditoría:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="auditoriaSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#aud-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#aud-sub-logs" type="button" role="tab"><i class="bi bi-shield-lock me-1"></i> Audit Logs</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="auditoriaSubContent">
                                <div class="tab-pane fade show active" id="aud-sub-inicio" role="tabpanel">
                                    @include('rsv.admin.partials.tab-auditoria')
                                </div>
                                <div class="tab-pane fade" id="aud-sub-logs" role="tabpanel">
                                    <div class="p-3 bg-white rounded border border-light shadow-sm">
                                        <h6 class="fw-bold mb-3 text-dark">
                                            <i class="bi bi-shield-check me-1 text-success"></i> Registro de Auditoría del Sistema
                                        </h6>
                                        @isset($auditoria)
                                            <div class="table-responsive bg-white">
                                                <table class="table table-sm table-bordered align-middle mb-0 small">
                                                    <thead class="table-light text-uppercase text-secondary" style="font-size: 0.75rem;">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Acción / Evento</th>
                                                            <th>Usuario</th>
                                                            <th>Detalles</th>
                                                            <th>Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($auditoria as $log)
                                                            <tr>
                                                                <td class="fw-bold">#{{ $log->id }}</td>
                                                                <td><span class="badge bg-light text-dark border">{{ $log->action ?? $log->evento ?? 'Acción' }}</span></td>
                                                                <td>{{ $log->user->name ?? $log->usuario ?? 'Sistema' }}</td>
                                                                <td class="text-muted text-truncate" style="max-width: 250px;" title="{{ json_encode($log->details ?? $log->detalles ?? '') }}">
                                                                    {{ Str::limit(json_encode($log->details ?? $log->detalles ?? 'N/A'), 50) }}
                                                                </td>
                                                                <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-3">No hay registros de auditoría disponibles.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if(method_exists($auditoria, 'links'))
                                                <div class="mt-3">
                                                    {{ $auditoria->appends(['inmueble_id' => 'todos'])->links() }}
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-muted small mb-0">No hay información de auditoría cargada.</p>
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL B: ECOSISTEMA DE UN INMUEBLE ESPECÍFICO (Seleccionado)      --}}
        {{-- ======================================================================= --}}
        @elseif(isset($inmueble) && $inmueble)

            {{-- PANEL DE KPIs MINIMALISTA --}}
            <div class="row g-2 mb-4">

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Capacidad</span>
                                <i class="bi bi-people text-info opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $inmueble->capacidad_maxima ?? 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">máx.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tarifa / Noche</span>
                                <i class="bi bi-cash-stack text-success opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-success mb-0 lh-1">{{ !is_null($kpiNoche) ? '$' . number_format($kpiNoche, 0) : '—' }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">base</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tarifa Mínima</span>
                                <i class="bi bi-piggy-bank text-success opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-success mb-0 lh-1">{{ !is_null($kpiMinReserva) ? '$' . number_format($kpiMinReserva, 0) : '—' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white" @if($kpiResenasCount > 0) title="Promedio de {{ $kpiResenasCount }} reseña(s)" @endif>
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Calificación</span>
                                <i class="bi bi-star-fill text-warning opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ !is_null($kpiRating) ? number_format($kpiRating, 1) : '—' }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">({{ $kpiResenasCount }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Reservas</span>
                                <i class="bi bi-journal-check text-danger opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $inmueble->reservas_count ?? 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white" title="Próximo check-in: {{ $kpiProximaEntrada?->format('d/m/Y') ?? '—' }}">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Próx. Entradas</span>
                                <i class="bi bi-door-open text-primary opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $kpiProximas ?? 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">{{ $kpiProximaEntrada ? $kpiProximaEntrada->format('d/m') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white" title="{{ $nochesOcupadas ?? 0 }} de {{ $diasDelMes ?? 30 }} noches de {{ now()->translatedFormat('F') }} ya reservadas">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ocupación Mes</span>
                                <i class="bi bi-percent text-secondary opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $kpiOcupacion ?? 0 }}%</h5>
                                <div class="progress flex-grow-1" style="height: 3px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min($kpiOcupacion ?? 0, 100) }}%;" aria-valuenow="{{ $kpiOcupacion ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Galería</span>
                                <i class="bi bi-images text-info opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $inmueble->multimedia ? $inmueble->multimedia->count() : 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">({{ $kpiFotos ?? 0 }} img, {{ $kpiVideos ?? 0 }} vid)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- NAVEGACIÓN PRINCIPAL DEL INMUEBLE (PADRES) --}}
            <div class="card shadow-sm border-0 bg-white" data-mode="individual" data-id="{{ $inmueble->id }}">
                <div class="card-header bg-white p-0 border-0">
                    <ul class="nav nav-tabs nav-fusion px-2 pt-2 tab-memory-parent" id="adminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#inmuebles" type="button" role="tab">
                                <i class="bi bi-house-door me-1"></i> Propiedades <span class="badge bg-light text-secondary ms-1">(#{{ $inmueble->id }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas <span class="badge bg-light text-secondary ms-1">({{ $inmueble->reservas_count ?? 0 }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab">
                                <i class="bi bi-calendar3 me-1"></i> Bloqueos <span class="badge bg-light text-secondary ms-1">({{ $inmueble->bloqueos_calendario_count ?? 0 }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#finanzas" type="button" role="tab">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas <span class="badge bg-light text-secondary ms-1">({{ $fmtMoney($ingresosInmueble ?? 0) }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab">
                                <i class="bi bi-shield-check me-1"></i> Auditoría <span class="badge bg-light text-secondary ms-1">({{ $auditoriaCount ?? '—' }})</span>
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- CONTENIDO DE PESTAÑAS INMUEBLE ESPECÍFICO --}}
                <div class="card-body p-0">
                    <div class="tab-content" id="adminTabsContent">

                        {{-- INMUEBLE - TAB 1: CONFIGURACIÓN PROPIEDAD --}}
                        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Inmobiliario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="inmuebleSingleSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-sub-show" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-catalogo" type="button" role="tab"><i class="bi bi-grid-3x3 me-1"></i> Catálogo</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tarifa" type="button" role="tab"><i class="bi bi-cash-coin me-1"></i> Tarifas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-galeria" type="button" role="tab"><i class="bi bi-images me-1"></i> Galería</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tipo" type="button" role="tab"><i class="bi bi-tags me-1"></i> Tipos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="inmuebleSingleSubContent">
                                <div class="tab-pane fade show active" id="single-sub-show" role="tabpanel">@include('rsv.admin.partials.tab-inmuebles', ['inmueble' => $inmueble])</div>

                                {{-- FIX: En modo individual a veces no pasamos todos porque no se consultó --}}
                                {{-- Si tu partial espera todos los inmuebles para listarlos, o si espera 1 solo, lo aclaramos --}}
                                <div class="tab-pane fade" id="single-sub-catalogo" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.catalogo', [
                                        'inmueble' => $inmueble,
                                        'inmueblesGrid' => $inmueblesGrid ?? \App\Models\Rsv\CatalogoInmueble::with(['multimedia', 'latestTarifa', 'tipoInmueble'])->paginate(12)
                                    ])
                                </div>

                                <div class="tab-pane fade" id="single-sub-tarifa" role="tabpanel">@include('rsv.admin.partials.inmuebles.tarifa', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-sub-galeria" role="tabpanel">@include('rsv.admin.partials.inmuebles.galeria', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-sub-tipo" role="tabpanel">@include('rsv.admin.partials.inmuebles.tipo', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 2: RESERVAS --}}
                        <div class="tab-pane fade" id="reservas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Reservas:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="singleReservasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-res-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-reservas" type="button" role="tab"><i class="bi bi-journal-check me-1"></i> Reservas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-huespedes" type="button" role="tab"><i class="bi bi-people me-1"></i> Huéspedes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-status" type="button" role="tab"><i class="bi bi-flag me-1"></i> Estados</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-origenes" type="button" role="tab"><i class="bi bi-box-arrow-in-down me-1"></i> Orígenes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-historial" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Historial</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="singleReservasSubContent">
                                <div class="tab-pane fade show active" id="single-res-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-resrvas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-reservas" role="tabpanel">@include('rsv.admin.partials.reservas.reservas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-huespedes" role="tabpanel">@include('rsv.admin.partials.reservas.reserva_huespedt', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-status" role="tabpanel">@include('rsv.admin.partials.reservas.status', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-origenes" role="tabpanel">@include('rsv.admin.partials.reservas.origen_reserva', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-historial" role="tabpanel">@include('rsv.admin.partials.reservas.historial_estado', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 3: BLOQUEOS (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="calendario" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Calendario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="singleCalendarioSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-cal-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-cal-sub-bloqueos" type="button" role="tab"><i class="bi bi-calendar-x me-1"></i> Bloqueos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="singleCalendarioSubContent">
                                <div class="tab-pane fade show active" id="single-cal-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-calendario', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-cal-sub-bloqueos" role="tabpanel">@include('rsv.admin.partials.calendario.bloqueos', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 4: FINANZAS (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="finanzas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="singleFinanzasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-fin-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-fin-sub-transacciones" type="button" role="tab"><i class="bi bi-receipt me-1"></i> Transacciones</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-fin-sub-pasarelas" type="button" role="tab"><i class="bi bi-credit-card me-1"></i> Pasarelas</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="singleFinanzasSubContent">
                                <div class="tab-pane fade show active" id="single-fin-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-finanzas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-fin-sub-transacciones" role="tabpanel">@include('rsv.admin.partials.finanzas.transacciones', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-fin-sub-pasarelas" role="tabpanel">@include('rsv.admin.partials.finanzas.pasarela', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 5: AUDITORÍA (Estructura de hijos añadida) --}}
                        <div class="tab-pane fade" id="auditoria" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Auditoría:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="singleAuditoriaSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-aud-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-aud-sub-logs" type="button" role="tab"><i class="bi bi-shield-lock me-1"></i> Audit Logs</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-4" id="singleAuditoriaSubContent">
                                <div class="tab-pane fade show active" id="single-aud-sub-inicio" role="tabpanel">
                                    <div class="p-3 bg-white rounded border border-light shadow-sm">
                                        <h6 class="fw-bold mb-3 text-dark">
                                            <i class="bi bi-shield-check me-1 text-success"></i> Registro de Auditoría del Sistema
                                        </h6>
                                        @isset($auditoria)
                                            <div class="table-responsive bg-white">
                                                <table class="table table-sm table-bordered align-middle mb-0 small">
                                                    <thead class="table-light text-uppercase text-secondary" style="font-size: 0.75rem;">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Acción / Evento</th>
                                                            <th>Usuario</th>
                                                            <th>Detalles</th>
                                                            <th>Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($auditoria as $log)
                                                            <tr>
                                                                <td class="fw-bold">#{{ $log->id }}</td>
                                                                <td><span class="badge bg-light text-dark border">{{ $log->action ?? $log->evento ?? 'Acción' }}</span></td>
                                                                <td>{{ $log->user->name ?? $log->usuario ?? 'Sistema' }}</td>
                                                                <td class="text-muted text-truncate" style="max-width: 250px;" title="{{ json_encode($log->details ?? $log->detalles ?? '') }}">
                                                                    {{ Str::limit(json_encode($log->details ?? $log->detalles ?? 'N/A'), 50) }}
                                                                </td>
                                                                <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-3">No hay registros de auditoría disponibles.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if(method_exists($auditoria, 'links'))
                                                <div class="mt-3">
                                                    {{ $auditoria->appends(['inmueble_id' => $inmueble->id])->links() }}
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-muted small mb-0">No hay información de auditoría cargada.</p>
                                        @endisset
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="single-aud-sub-logs" role="tabpanel">
                                    <p class="text-muted small mb-0">Logs detallados de este inmueble (Próximamente)</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL C: SIN SELECCIÓN (Estado Vacío / Inicial)                   --}}
        {{-- ======================================================================= --}}
        @else
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-building-gear text-secondary opacity-25" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2 text-dark">Ninguna propiedad seleccionada</h5>
                    <p class="mb-0 text-muted">
                        Seleccione una propiedad en el selector superior u oprima «Ver todas las propiedades» para operar en modo global.
                    </p>
                </div>
            </div>
        @endif

    </div>

    {{-- ======================================================================= --}}
    {{-- SCRIPT: GESTOR DE MEMORIA DE PESTAÑAS (Local Storage)                   --}}
    {{-- Recuerda en qué Padre e Hijo te quedaste tras hacer un Submit/Recarga   --}}
    {{-- ======================================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Determinar el contexto actual para guardar variables separadas
            // Así si el usuario navega a un inmueble, no se abre la pestaña que dejó en el panel global.
            const inmuebleId = '{{ $inmuebleId ?? "todos" }}';
            const contextKey = 'rsv_panel_context_' + inmuebleId;
            const parentKey  = 'rsv_panel_parent_' + inmuebleId;
            const childKey   = 'rsv_panel_child_' + inmuebleId;

            // 2. Escuchar clics en las pestañas Padres y guardar su ID objetivo
            const parentTabs = document.querySelectorAll('.tab-memory-parent .nav-link');
            parentTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem(parentKey, event.target.getAttribute('data-bs-target'));
                });
            });

            // 3. Escuchar clics en las subpestañas Hijos y guardar su ID objetivo
            const childTabs = document.querySelectorAll('.tab-memory-child .nav-link');
            childTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem(childKey, event.target.getAttribute('data-bs-target'));
                });
            });

            // 4. RESTAURACIÓN AUTOMÁTICA AL CARGAR LA PÁGINA
            const savedParent = localStorage.getItem(parentKey);
            const savedChild  = localStorage.getItem(childKey);

            if (savedParent) {
                // Buscamos el botón de la pestaña padre guardada
                const parentBtn = document.querySelector(`.tab-memory-parent .nav-link[data-bs-target="${savedParent}"]`);
                if (parentBtn) {
                    // Instanciamos el tab de Bootstrap y lo mostramos
                    const bsParentTab = new bootstrap.Tab(parentBtn);
                    bsParentTab.show();
                }
            }

            if (savedChild) {
                // Buscamos el botón de la subpestaña hijo guardada
                const childBtn = document.querySelector(`.tab-memory-child .nav-link[data-bs-target="${savedChild}"]`);
                if (childBtn) {
                    const bsChildTab = new bootstrap.Tab(childBtn);
                    bsChildTab.show();
                }
            }
        });
    </script>
        {{-- ======================================================================= --}}
    {{-- NEUTRALIZADOR MAESTRO v2 (REEMPLAZA al neutralizador viejo de la ruedita) --}}
    {{-- ======================================================================= --}}
    <style>
        /* Anti-ruedita: preloaders, spinners y overlays de carga.
           Ya NO incluye html,body{overflow:auto!important} (rompia el scroll-lock
           de los modales legitimos) ni los selectores del modal (los maneja el JS). */
        #preloader, .preloader, [id*="preloader"], [class*="preloader"],
        #loader, #loader-overlay, .loader-overlay,
        #loading, .loading, .loading-overlay, .loading-screen,
        #cargando, .cargando, .cargando-overlay,
        .page-loader, #page-loader, .page-loading,
        .spinner-overlay, .overlay-spinner, .spinner-container,
        .pace .pace-activity, .pace-active, #nprogress
        {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            z-index: -1 !important;
        }
    </style>

    <script>
    (function () {
        'use strict';

        /* ============ CONFIG - SOLO EDITA ESTO ============ */

        // 1) Selector del MODAL GLOBAL a bloquear (clic derecho sobre el modal
        //    -> Inspeccionar -> copia su id o clase; viene del x-base-layout).
        var MODAL = '#modalCargando, .modal-cargando';   // <- PON EL ID/CLASE REAL

        // 2) '' = bloquearlo en todo el maestro | '#id' = solo en esa seccion:
        //    #global-inmuebles | #global-reservas | #global-calendario
        //    #global-finanzas  | #global-auditoria
        //    #inmuebles | #reservas | #calendario | #finanzas | #auditoria
        var SECCION = '';

        /* =================================================== */

        /* -- PARTE 1: ANTI-RUEDITA -- */
        try {
            if (window.NProgress) { window.NProgress.remove(); }
            if (window.Pace && window.Pace.stop)  { window.Pace.stop(); }
        } catch (e) {}

        function esRuedita(el) {
            if (!el || el.nodeType !== 1) return false;
            var marca = ((el.id || '') + ' ' + (el.className || '')).toLowerCase();
            return /preloader|loader|loading|cargando|spinner/.test(marca);
        }

        function limpiarRuedita() {
            document.querySelectorAll('body > *').forEach(function (el) {
                if (esRuedita(el) && !(el.matches && el.matches(MODAL))) el.remove();
            });
            ['html', 'body'].forEach(function (tag) {
                var n = document[tag];
                n.classList.remove('loading', 'is-loading', 'js-loading',
                                   'page-is-loading', 'preloader-active');
            });
        }
        limpiarRuedita();
        new MutationObserver(function () { limpiarRuedita(); })
            .observe(document.body, { childList: true });

        /* -- PARTE 2: NEUTRALIZADOR QUIRURGICO DEL MODAL -- */

        function seccionActiva() {
            if (!SECCION) return true;
            var sec = document.querySelector(SECCION);
            if (!sec) return false;
            if (sec.classList.contains('tab-pane')) {
                return sec.classList.contains('show') && sec.classList.contains('active');
            }
            return !!(sec.offsetWidth || sec.offsetHeight || sec.getClientRects().length);
        }

        if (!SECCION) {
            var t = document.createElement('style');
            t.textContent = MODAL + '{display:none!important;opacity:0!important;' +
                            'visibility:hidden!important;pointer-events:none!important;}';
            document.head.appendChild(t);
        }

        // Cancela show.bs.modal ANTES de que Bootstrap cree backdrop o bloquee scroll
        document.addEventListener('show.bs.modal', function (e) {
            var el = e.target;
            if (!el || el.nodeType !== 1 || !(el.matches && el.matches(MODAL))) return;
            if (!seccionActiva()) return;
            e.preventDefault();
            e.stopImmediatePropagation();
        }, true);

        // Red de seguridad para aperturas "a lo bruto" sin API de Bootstrap
        function limpiarRestosModal() {
            document.querySelectorAll(MODAL).forEach(function (m) {
                if (m.classList.contains('show') || m.style.display === 'block') {
                    m.classList.remove('show');
                    m.style.display = 'none';
                    m.setAttribute('aria-hidden', 'true');
                }
            });
            if (!document.querySelector('.modal.show')) {
                document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            }
        }

        function vigilarModal() {
            document.querySelectorAll(MODAL).forEach(function (m) {
                if (m.__vigilado) return;
                m.__vigilado = true;
                new MutationObserver(function () {
                    if (seccionActiva() &&
                       (m.classList.contains('show') || m.style.display === 'block')) {
                        limpiarRestosModal();
                    }
                }).observe(m, { attributes: true, attributeFilter: ['class', 'style'] });
            });
        }
        vigilarModal();
        new MutationObserver(function () { vigilarModal(); })
            .observe(document.body, { childList: true, subtree: true });

        // Diagnostico en consola (F12)
        document.addEventListener('DOMContentLoaded', function () {
            var n = document.querySelectorAll(MODAL).length;
            if (n === 0) {
                console.warn('[Neutralizador RSV] No encontre "' + MODAL +
                             '". Revisa la variable MODAL.');
            } else {
                console.info('[Neutralizador RSV] Bloqueando ' + n + ' modal(es): ' + MODAL);
            }
        });
    })();
    </script>

</x-base-layout>
