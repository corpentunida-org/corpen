{{-- resources/views/rsv/admin/dashboard.blade.php · Panel Maestro del Administrador (ADMIN-DASH-v3-DBG) --}}
<x-base-layout>
    @section('titlepage', 'Panel Maestro de Inmuebles - RSV')

    @php
        $fmtMoney = function ($n) {
            if ($n === null || $n === 0 || $n === '') return '—';
            $val = (float) $n;
            if ($val <= 0) return '—';
            if ($val >= 1000000) return '$' . number_format($val / 1000000, 1) . 'M';
            if ($val >= 1000)    return '$' . number_format($val / 1000, 1) . 'k';
            return '$' . number_format($val, 0);
        };

        $modoActual = ($inmuebleId ?? 'todos') === 'todos' ? 'global' : 'individual';
    @endphp

    {{-- ========================================================= --}}
    {{-- NOTIFICACIONES DEL SISTEMA                                --}}
    {{-- ========================================================= --}}
    @include('rsv.components.alert')

    {{-- ============================================================================== --}}
    {{-- ESTILOS DE FUSIÓN (PADRE - HIJO) + KPIs INTERACTIVOS                           --}}
    {{-- ============================================================================== --}}
    <style>
        /* 1. Contenedor de las pestañas principales (Padre) */
        .nav-fusion {
            border-bottom: 1px solid #badbcc !important;
            gap: 2px;
        }

        /* 2. Pestañas en estado normal */
        .nav-fusion .nav-link {
            color: #64748b;
            font-weight: 600;
            font-size: 0.82rem;
            border: 1px solid transparent;
            border-top-left-radius: 0.4rem;
            border-top-right-radius: 0.4rem;
            padding: 0.55rem 0.95rem;
            margin-bottom: -1px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        /* 3. Hover en las pestañas */
        .nav-fusion .nav-link:hover {
            color: #198754;
            border-color: #e9ecef #e9ecef #badbcc;
            background-color: #f8fafc;
        }

        /* 4. Pestaña ACTIVA */
        .nav-fusion .nav-link.active {
            color: #0f5132 !important;
            background-color: #d1e7dd !important;
            border-color: #badbcc #badbcc #d1e7dd #badbcc !important;
            font-weight: 700;
        }

        /* 5. Barra del Submenú (Hijo) */
        .subnav-fusion {
            background-color: #d1e7dd;
            border-bottom: 1px solid #badbcc;
            border-top: none;
            border-bottom-left-radius: 0.4rem;
            border-bottom-right-radius: 0.4rem;
            padding: 0.55rem 1rem;
            margin-bottom: 0.75rem;
        }

        /* 6. Botones (Píldoras) dentro del submenú */
        .sub-pills .nav-link {
            color: #146c43;
            background-color: rgba(255, 255, 255, 0.7);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.32rem 0.8rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
            transition: all 0.18s ease;
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
            box-shadow: 0 2px 5px rgba(25, 135, 84, 0.22);
        }

        /* 7. Efecto Hover Minimalista para tarjetas KPI */
        .kpi-card {
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
            cursor: pointer;
            border-color: #eef2f6 !important;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.35rem 0.85rem rgba(15, 23, 42, 0.06) !important;
            border-color: #badbcc !important;
        }

        .kpi-money { font-variant-numeric: tabular-nums; }
    </style>

    <div class="container-fluid py-3">

        {{-- ======================================================================= --}}
        {{-- 1. SELECTOR MAESTRO DE INMUEBLES                                        --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">

                    {{-- Información del Módulo --}}
                    <div class="col-12 col-lg-5 text-center text-lg-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mb-1">
                            <span class="text-uppercase text-success fw-bold" style="font-size: 0.68rem; letter-spacing: 0.8px;">
                                <i class="bi bi-sliders2-vertical me-1"></i>Panel Conector RSV
                            </span>
                            @if($modoActual === 'global')
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.66rem;">Modo Global</span>
                            @elseif(isset($inmueble) && $inmueble)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.66rem;">Propiedad #{{ $inmueble->id }}</span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">
                            @if($modoActual === 'individual' && isset($inmueble) && $inmueble)
                                {{ $inmueble->name }}
                                @if(!empty($inmueble->city))
                                    <span class="text-muted fw-normal" style="font-size: 0.82rem;">· {{ $inmueble->city }}</span>
                                @endif
                            @else
                                Gestión por Inmueble
                            @endif
                        </h6>
                        <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                            Seleccione una propiedad para cargar su ecosistema individual o elija operar sobre todas las propiedades.
                        </p>
                    </div>

                    {{-- Formulario de Selección --}}
                    <div class="col-12 col-lg-7">
                        <form action="{{ route('rsv.admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 w-100">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-success border-secondary-subtle">
                                    <i class="bi bi-building"></i>
                                </span>

                                <select class="form-select border-secondary-subtle fw-semibold text-dark"
                                        name="inmueble_id"
                                        onchange="this.form.submit()"
                                        aria-label="Seleccionar Inmueble">

                                    <option value="todos" {{ ($inmuebleId ?? 'todos') === 'todos' ? 'selected' : '' }}>
                                        🌐 Ver todas las propiedades (Panel Global)
                                    </option>

                                    @isset($listaInmuebles)
                                        @foreach($listaInmuebles as $opcion)
                                            <option value="{{ $opcion->id }}" {{ (string) request('inmueble_id', $inmuebleId ?? '') === (string) $opcion->id ? 'selected' : '' }}>
                                                #{{ $opcion->id }} · {{ $opcion->name }}{{ !empty($opcion->city) ? ' (' . $opcion->city . ')' : '' }} — [{{ $opcion->active ? 'Activo' : 'Inactivo' }}]
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>

                                @if($modoActual === 'individual')
                                    <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => 'todos']) }}"
                                       class="btn btn-outline-secondary"
                                       title="Volver al Panel Global">
                                        <i class="bi bi-globe2"></i> Global
                                    </a>
                                @endif
                            </div>
                            <noscript>
                                <button type="submit" class="btn btn-sm btn-outline-success fw-semibold">Cargar</button>
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
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 border-bottom border-light">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.6px;">
                            <i class="bi bi-grid-3x3-gap me-1 text-success"></i> Panel Consolidado
                        </div>
                        <h6 class="fw-bold text-dark mb-0">Gestión Global del Sistema</h6>
                        <p class="text-muted mb-0" style="font-size: 0.76rem;">Vista consolidada de propiedades, reservas, calendario, finanzas y auditoría.</p>
                    </div>
                    <span class="badge bg-light text-secondary border rounded-pill fw-normal py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-calendar3 small text-success"></i>
                        <span style="font-size: 0.76rem;">{{ now()->format('d/m/Y h:i A') }}</span>
                    </span>
                </div>

                {{-- NAVEGACIÓN PRINCIPAL (PADRES GLOBAL) --}}
                <div class="card-header bg-white p-0 border-0">
                    <ul class="nav nav-tabs nav-fusion px-2 pt-2 tab-memory-parent" id="globalAdminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#global-inmuebles" type="button" role="tab">
                                <i class="bi bi-buildings me-1"></i> Propiedades
                                <span class="badge bg-light text-secondary border ms-1">{{ $tabTotalInmuebles ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-reservas" type="button" role="tab">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas
                                <span class="badge bg-light text-secondary border ms-1">{{ $tabReservas ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-calendario" type="button" role="tab">
                                <i class="bi bi-calendar3 me-1"></i> Bloqueos
                                <span class="badge bg-light text-secondary border ms-1">{{ $tabBloqueos ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-finanzas" type="button" role="tab">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas
                                <span class="badge bg-light text-secondary border ms-1 kpi-money">{{ $fmtMoney($tabFinanzas ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-auditoria" type="button" role="tab">
                                <i class="bi bi-shield-check me-1"></i> Auditoría
                                <span class="badge bg-light text-secondary border ms-1">{{ $tabAuditoria ?? 0 }}</span>
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
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#global-inmuebles" id="inmueblesSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#sub-show" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-catalogo" type="button" role="tab"><i class="bi bi-grid-3x3 me-1"></i> Catálogo</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tarifa" type="button" role="tab"><i class="bi bi-cash-coin me-1"></i> Tarifas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-galeria" type="button" role="tab"><i class="bi bi-images me-1"></i> Galería</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tipo" type="button" role="tab"><i class="bi bi-tags me-1"></i> Tipos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="inmueblesSubContent">
                                <div class="tab-pane fade show active" id="sub-show" role="tabpanel">@include('rsv.admin.partials.tab-inmuebles')</div>
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
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#global-reservas" id="reservasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#res-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-reservas" type="button" role="tab"><i class="bi bi-journal-check me-1"></i> Reservas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-huespedes" type="button" role="tab"><i class="bi bi-people me-1"></i> Huéspedes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-status" type="button" role="tab"><i class="bi bi-flag me-1"></i> Estados</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-origenes" type="button" role="tab"><i class="bi bi-box-arrow-in-down me-1"></i> Orígenes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-historial" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Historial</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="reservasSubContent">
                                <div class="tab-pane fade show active" id="res-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-resrvas')</div>
                                <div class="tab-pane fade" id="res-sub-reservas" role="tabpanel">@include('rsv.admin.partials.reservas.reservas')</div>
                                <div class="tab-pane fade" id="res-sub-huespedes" role="tabpanel">@include('rsv.admin.partials.reservas.reserva_huespedt')</div>
                                <div class="tab-pane fade" id="res-sub-status" role="tabpanel">@include('rsv.admin.partials.reservas.status')</div>
                                <div class="tab-pane fade" id="res-sub-origenes" role="tabpanel">@include('rsv.admin.partials.reservas.origen_reserva')</div>
                                <div class="tab-pane fade" id="res-sub-historial" role="tabpanel">@include('rsv.admin.partials.reservas.historial_estado')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 3: CALENDARIO --}}
                        <div class="tab-pane fade" id="global-calendario" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Calendario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#global-calendario" id="calendarioSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#cal-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#cal-sub-bloqueos" type="button" role="tab"><i class="bi bi-calendar-x me-1"></i> Bloqueos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="calendarioSubContent">
                                <div class="tab-pane fade show active" id="cal-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-calendario')</div>
                                <div class="tab-pane fade" id="cal-sub-bloqueos" role="tabpanel">@include('rsv.admin.partials.calendario.bloqueos')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 4: FINANZAS --}}
                        <div class="tab-pane fade" id="global-finanzas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#global-finanzas" id="finanzasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#fin-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#fin-sub-transacciones" type="button" role="tab"><i class="bi bi-receipt me-1"></i> Transacciones</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#fin-sub-pasarelas" type="button" role="tab"><i class="bi bi-credit-card me-1"></i> Pasarelas</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="finanzasSubContent">
                                <div class="tab-pane fade show active" id="fin-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-finanzas')</div>
                                <div class="tab-pane fade" id="fin-sub-transacciones" role="tabpanel">@include('rsv.admin.partials.finanzas.transacciones')</div>
                                <div class="tab-pane fade" id="fin-sub-pasarelas" role="tabpanel">@include('rsv.admin.partials.finanzas.pasarela')</div>
                            </div>
                        </div>

                        {{-- GLOBAL - TAB 5: AUDITORÍA --}}
                        <div class="tab-pane fade" id="global-auditoria" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Auditoría:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#global-auditoria" id="auditoriaSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#aud-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#aud-sub-logs" type="button" role="tab"><i class="bi bi-shield-lock me-1"></i> Audit Logs</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="auditoriaSubContent">
                                <div class="tab-pane fade show active" id="aud-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-auditoria')</div>
                                <div class="tab-pane fade" id="aud-sub-logs" role="tabpanel">@include('rsv.admin.partials.auditorias.informes')</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL B: ECOSISTEMA DE UN INMUEBLE ESPECÍFICO (Seleccionado)      --}}
        {{-- ======================================================================= --}}
        @elseif(isset($inmueble) && $inmueble)

            {{-- PANEL DE 8 KPIs INTERACTIVOS (CLICABLES) --}}
            <div class="row g-2 mb-3">

                {{-- KPI 1: Capacidad --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#inmuebles" data-child="#single-sub-show"
                         title="Ver ficha técnica de la propiedad">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Capacidad</span>
                                <i class="bi bi-people text-info opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $inmueble->capacidad_maxima ?? 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">huéspedes máx.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 2: Tarifa / Noche --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#inmuebles" data-child="#single-sub-tarifa"
                         title="Configurar tarifas por temporada">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tarifa / Noche</span>
                                <i class="bi bi-cash-stack text-success opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-success mb-0 lh-1 kpi-money">{{ !is_null($kpiNoche ?? null) ? '$' . number_format($kpiNoche, 0) : '—' }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">base</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 3: Tarifa Mínima --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#inmuebles" data-child="#single-sub-tarifa"
                         title="Ver tarifa mínima de reserva">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tarifa Mínima</span>
                                <i class="bi bi-piggy-bank text-success opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-success mb-0 lh-1 kpi-money">{{ !is_null($kpiMinReserva ?? null) ? '$' . number_format($kpiMinReserva, 0) : '—' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 4: Calificación --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#auditoria" data-child="#single-aud-sub-inicio"
                         @if(($kpiResenasCount ?? 0) > 0) title="Promedio de {{ $kpiResenasCount }} reseña(s)" @endif>
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Calificación</span>
                                <i class="bi bi-star-fill text-warning opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ !is_null($kpiRating ?? null) ? number_format($kpiRating, 1) : '—' }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">({{ $kpiResenasCount ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 5: Reservas --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#reservas" data-child="#single-res-sub-reservas"
                         title="Gestionar reservas de esta propiedad">
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

                {{-- KPI 6: Próximas Entradas --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#reservas" data-child="#single-res-sub-huespedes"
                         title="Próximo check-in: {{ ($kpiProximaEntrada ?? null)?->format('d/m/Y') ?? '—' }}">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Próx. Entradas</span>
                                <i class="bi bi-door-open text-primary opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $kpiProximas ?? 0 }}</h5>
                                <span class="text-muted" style="font-size: 0.65rem;">{{ ($kpiProximaEntrada ?? null) ? $kpiProximaEntrada->format('d/m') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 7: Ocupación Mes --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#calendario" data-child="#single-cal-sub-inicio"
                         title="{{ $nochesOcupadas ?? 0 }} de {{ $diasDelMes ?? 30 }} noches de {{ now()->translatedFormat('F') }} ya reservadas">
                        <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ocupación Mes</span>
                                <i class="bi bi-percent text-secondary opacity-75"></i>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold text-dark mb-0 lh-1">{{ $kpiOcupacion ?? 0 }}%</h5>
                                <div class="progress flex-grow-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min($kpiOcupacion ?? 0, 100) }}%;" aria-valuenow="{{ $kpiOcupacion ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 8: Galería --}}
                <div class="col-6 col-md-3">
                    <div class="card kpi-card border shadow-sm h-100 bg-white js-admin-kpi-nav"
                         data-parent="#inmuebles" data-child="#single-sub-galeria"
                         title="Administrar fotos y videos de la propiedad">
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
                                <i class="bi bi-house-door me-1"></i> Propiedades
                                <span class="badge bg-light text-secondary border ms-1">#{{ $inmueble->id }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas
                                <span class="badge bg-light text-secondary border ms-1">{{ $inmueble->reservas_count ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab">
                                <i class="bi bi-calendar3 me-1"></i> Bloqueos
                                <span class="badge bg-light text-secondary border ms-1">{{ $inmueble->bloqueos_calendario_count ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#finanzas" type="button" role="tab">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas
                                <span class="badge bg-light text-secondary border ms-1 kpi-money">{{ $fmtMoney($ingresosInmueble ?? 0) }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab">
                                <i class="bi bi-shield-check me-1"></i> Auditoría
                                <span class="badge bg-light text-secondary border ms-1">{{ $auditoriaCount ?? '—' }}</span>
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
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#inmuebles" id="inmuebleSingleSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-sub-show" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-catalogo" type="button" role="tab"><i class="bi bi-grid-3x3 me-1"></i> Catálogo</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tarifa" type="button" role="tab"><i class="bi bi-cash-coin me-1"></i> Tarifas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-galeria" type="button" role="tab"><i class="bi bi-images me-1"></i> Galería</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tipo" type="button" role="tab"><i class="bi bi-tags me-1"></i> Tipos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="inmuebleSingleSubContent">
                                <div class="tab-pane fade show active" id="single-sub-show" role="tabpanel">@include('rsv.admin.partials.tab-inmuebles', ['inmueble' => $inmueble])</div>
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
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#reservas" id="singleReservasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-res-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-reservas" type="button" role="tab"><i class="bi bi-journal-check me-1"></i> Reservas</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-huespedes" type="button" role="tab"><i class="bi bi-people me-1"></i> Huéspedes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-status" type="button" role="tab"><i class="bi bi-flag me-1"></i> Estados</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-origenes" type="button" role="tab"><i class="bi bi-box-arrow-in-down me-1"></i> Orígenes</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-historial" type="button" role="tab"><i class="bi bi-clock-history me-1"></i> Historial</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="singleReservasSubContent">
                                <div class="tab-pane fade show active" id="single-res-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-resrvas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-reservas" role="tabpanel">@include('rsv.admin.partials.reservas.reservas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-huespedes" role="tabpanel">@include('rsv.admin.partials.reservas.reserva_huespedt', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-status" role="tabpanel">@include('rsv.admin.partials.reservas.status', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-origenes" role="tabpanel">@include('rsv.admin.partials.reservas.origen_reserva', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-res-sub-historial" role="tabpanel">@include('rsv.admin.partials.reservas.historial_estado', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 3: BLOQUEOS --}}
                        <div class="tab-pane fade" id="calendario" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Calendario:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#calendario" id="singleCalendarioSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-cal-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-cal-sub-bloqueos" type="button" role="tab"><i class="bi bi-calendar-x me-1"></i> Bloqueos</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="singleCalendarioSubContent">
                                <div class="tab-pane fade show active" id="single-cal-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-calendario', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-cal-sub-bloqueos" role="tabpanel">@include('rsv.admin.partials.calendario.bloqueos', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 4: FINANZAS --}}
                        <div class="tab-pane fade" id="finanzas" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#finanzas" id="singleFinanzasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-fin-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-fin-sub-transacciones" type="button" role="tab"><i class="bi bi-receipt me-1"></i> Transacciones</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-fin-sub-pasarelas" type="button" role="tab"><i class="bi bi-credit-card me-1"></i> Pasarelas</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="singleFinanzasSubContent">
                                <div class="tab-pane fade show active" id="single-fin-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-finanzas', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-fin-sub-transacciones" role="tabpanel">@include('rsv.admin.partials.finanzas.transacciones', ['inmueble' => $inmueble])</div>
                                <div class="tab-pane fade" id="single-fin-sub-pasarelas" role="tabpanel">@include('rsv.admin.partials.finanzas.pasarela', ['inmueble' => $inmueble])</div>
                            </div>
                        </div>

                        {{-- INMUEBLE - TAB 5: AUDITORÍA --}}
                        <div class="tab-pane fade" id="auditoria" role="tabpanel">
                            <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                    <i class="bi bi-diagram-3-fill"></i> Ecosistema de Auditoría:
                                </div>
                                <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#auditoria" id="singleAuditoriaSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-aud-sub-inicio" type="button" role="tab"><i class="bi bi-house-door me-1"></i> Inicio</button></li>
                                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-aud-sub-logs" type="button" role="tab"><i class="bi bi-shield-lock me-1"></i> Audit Logs</button></li>
                                </ul>
                            </div>
                            <div class="tab-content p-3" id="singleAuditoriaSubContent">
                                <div class="tab-pane fade show active" id="single-aud-sub-inicio" role="tabpanel">@include('rsv.admin.partials.tab-auditoria')</div>
                                <div class="tab-pane fade" id="single-aud-sub-logs" role="tabpanel">@include('rsv.admin.partials.auditorias.informes')</div>
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
    {{-- SCRIPT: ORQUESTADOR DE MEMORIA INDEPENDIENTE PADRE/HIJO + FULLCALENDAR  --}}
    {{-- ======================================================================= --}}
    <script>
        (function () {
            'use strict';

            var inmuebleId   = '{{ $inmuebleId ?? "todos" }}';
            var PARENT_KEY   = 'rsv_admin_parent_v3_' + inmuebleId;
            var CHILD_PREFIX = 'rsv_admin_child_v3_' + inmuebleId + '_';

            function showTabBySelector(selector) {
                if (!selector) return false;
                var btn = document.querySelector('[data-bs-target="' + selector + '"]');
                if (!btn) return false;
                if (window.bootstrap && window.bootstrap.Tab) {
                    window.bootstrap.Tab.getOrCreateInstance(btn).show();
                } else {
                    btn.click();
                }
                return true;
            }

            function refrescarCalendariosVisibles() {
                setTimeout(function () {
                    try {
                        window.dispatchEvent(new Event('resize'));
                        var calInst = window.calendar || window.rsvCalendar || window.__rsvFullCalendar;
                        if (calInst && typeof calInst.updateSize === 'function') {
                            calInst.updateSize();
                        }
                        if (calInst && typeof calInst.refetchEvents === 'function') {
                            calInst.refetchEvents();
                        }
                    } catch (err) {}
                }, 200);
            }

            // API pública para saltar entre pestañas Padre e Hijo desde KPIs o scripts de parciales
            window.RsvAdmin = {
                go: function (parentSelector, childSelector) {
                    if (parentSelector) showTabBySelector(parentSelector);
                    if (childSelector) {
                        setTimeout(function () {
                            showTabBySelector(childSelector);
                        }, 60);
                    }
                },
                refreshCalendar: refrescarCalendariosVisibles
            };

            document.addEventListener('DOMContentLoaded', function () {

                // 1. Guardar memoria de Pestaña Padre y restaurar su Sub-pestaña asociada
                document.querySelectorAll('.tab-memory-parent .nav-link').forEach(function (tab) {
                    tab.addEventListener('shown.bs.tab', function (event) {
                        var targetParent = event.target.getAttribute('data-bs-target');
                        if (!targetParent) return;
                        localStorage.setItem(PARENT_KEY, targetParent);

                        var savedChild = localStorage.getItem(CHILD_PREFIX + targetParent);
                        if (savedChild) {
                            showTabBySelector(savedChild);
                        }
                    });
                });

                // 2. Guardar memoria de Sub-pestaña vinculada específicamente a su Padre
                document.querySelectorAll('.tab-memory-child').forEach(function (ul) {
                    var parentId = ul.getAttribute('data-parent-tab') || '';
                    ul.querySelectorAll('.nav-link').forEach(function (tab) {
                        tab.addEventListener('shown.bs.tab', function (event) {
                            var targetChild = event.target.getAttribute('data-bs-target');
                            if (parentId && targetChild) {
                                localStorage.setItem(CHILD_PREFIX + parentId, targetChild);
                            }
                        });
                    });
                });

                // 3. Redibujar FullCalendar de forma segura (sin ReferenceError) al entrar a pestañas de calendario
                document.addEventListener('shown.bs.tab', function (e) {
                    var sel = e.target ? e.target.getAttribute('data-bs-target') : '';
                    if (!sel) return;

                    var pane = document.querySelector(sel);
                    var calNode = document.getElementById('calRoot') || document.querySelector('.fc');
                    var contieneCal = (pane && calNode && pane.contains(calNode));

                    if (
                        contieneCal ||
                        sel === '#global-calendario' ||
                        sel === '#calendario' ||
                        sel === '#cal-sub-inicio' ||
                        sel === '#single-cal-sub-inicio'
                    ) {
                        refrescarCalendariosVisibles();
                    }
                });

                // 4. Clic en tarjetas KPI del modo individual → Navegación directa a la pestaña y sub-pestaña
                document.addEventListener('click', function (e) {
                    var kpi = e.target && e.target.closest ? e.target.closest('.js-admin-kpi-nav') : null;
                    if (!kpi) return;
                    var p = kpi.getAttribute('data-parent');
                    var c = kpi.getAttribute('data-child');
                    window.RsvAdmin.go(p, c);
                });

                // 5. Restauración automática al cargar la página
                var savedParent = localStorage.getItem(PARENT_KEY);
                if (savedParent && showTabBySelector(savedParent)) {
                    var savedChild = localStorage.getItem(CHILD_PREFIX + savedParent);
                    if (savedChild) {
                        showTabBySelector(savedChild);
                    }
                } else {
                    var activeParentBtn = document.querySelector('.tab-memory-parent .nav-link.active');
                    var defaultParent = activeParentBtn ? activeParentBtn.getAttribute('data-bs-target') : '';
                    if (defaultParent) {
                        var defaultSavedChild = localStorage.getItem(CHILD_PREFIX + defaultParent);
                        if (defaultSavedChild) {
                            showTabBySelector(defaultSavedChild);
                        }
                    }
                }
            });
        })();
    </script>

    {{-- ======================================================================= --}}
    {{-- NEUTRALIZADOR MAESTRO v2 (Anti-ruedita y modales fantasma de carga)     --}}
    {{-- ======================================================================= --}}
    <style>
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

        var MODAL = '#modalCargando, .modal-cargando, [wire\\:loading]';
        var SECCION = '';

        try {
            if (window.NProgress) { window.NProgress.remove(); }
            if (window.Pace && window.Pace.stop) { window.Pace.stop(); }
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
                if (n) {
                    n.classList.remove('loading', 'is-loading', 'js-loading', 'page-is-loading', 'preloader-active');
                }
            });
        }
        limpiarRuedita();
        new MutationObserver(function () { limpiarRuedita(); })
            .observe(document.body, { childList: true });

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
            t.textContent = MODAL + '{display:none!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;}';
            document.head.appendChild(t);
        }

        document.addEventListener('show.bs.modal', function (e) {
            var el = e.target;
            if (!el || el.nodeType !== 1 || !(el.matches && el.matches(MODAL))) return;
            if (!seccionActiva()) return;
            e.preventDefault();
            e.stopImmediatePropagation();
        }, true);

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
                    if (seccionActiva() && (m.classList.contains('show') || m.style.display === 'block')) {
                        limpiarRestosModal();
                    }
                }).observe(m, { attributes: true, attributeFilter: ['class', 'style'] });
            });
        }
        vigilarModal();
        new MutationObserver(function () { vigilarModal(); })
            .observe(document.body, { childList: true, subtree: true });
    })();
    </script>

</x-base-layout>
