{{-- resources/views/rsv/cliente/portal.blade.php · Portal Maestro del Asociado / Huésped (PORTAL-CLI-v3-DBG) --}}
<x-base-layout>
    @section('titlepage', 'Mi Portal - Sistema RSV')

    @php
        $fmtMoney = function ($n) {
            $val = (float) ($n ?? 0);
            if ($val <= 0) return '$0';
            if ($val >= 1000000) return '$' . number_format($val / 1000000, 1) . 'M';
            if ($val >= 1000)    return '$' . number_format($val / 1000, 0) . 'k';
            return '$' . number_format($val, 0);
        };

        $authUser     = auth()->user();
        $userName     = $authUser->name ?? 'Asociado';
        $userNit      = $authUser->nit ?? null;

        /* ── KPIs del cliente con valores por defecto ── */
        $cliProximo   = $kpiProximo   ?? null; // Carbon|null
        $cliActivas   = (int) ($kpiActivas   ?? 0);
        $cliTotal     = (int) ($kpiTotal     ?? 0);
        $cliAbonado   = (float) ($kpiAbonado ?? 0);
        $cliSaldo     = (float) ($kpiSaldo   ?? 0);
        $cliMensajes  = (int) ($kpiMensajes  ?? 0);
        $cliReviews   = (int) ($kpiReviews   ?? 0);
        $cliHistorial = (int) ($kpiHistorial ?? $cliTotal);
    @endphp

    {{-- ========================================================= --}}
    {{-- NOTIFICACIONES DEL SISTEMA                                --}}
    {{-- ========================================================= --}}
    @include('rsv.components.alert')

    @if(!empty($error))
        <div class="alert alert-warning border-0 shadow-sm mx-3 mt-3 mb-0 d-flex align-items-center gap-2" style="font-size: 0.82rem;">
            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <span>{{ $error }}</span>
        </div>
    @endif

    {{-- ============================================================================== --}}
    {{-- ESTILOS DE FUSIÓN (PADRE - HIJO) + KPIs INTERACTIVOS                           --}}
    {{-- ============================================================================== --}}
    <style>
        /* 1. Contenedor de las pestañas principales (Padre) */
        .nav-fusion { border-bottom: 1px solid #badbcc !important; gap: 2px; }

        /* 2. Pestañas en estado normal */
        .nav-fusion .nav-link {
            color: #64748b; font-weight: 600; font-size: 0.82rem;
            border: 1px solid transparent;
            border-top-left-radius: 0.4rem; border-top-right-radius: 0.4rem;
            padding: 0.55rem 0.95rem;
            margin-bottom: -1px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        /* 3. Hover en las pestañas */
        .nav-fusion .nav-link:hover {
            color: #198754;
            border-color: #e9ecef #e9ecef #badbcc;
            background-color: #f8;
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

        /* 7. Tarjetas KPI Interactivas */
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

        /* 8. Formateador de moneda compacto */
        .kpi-money { font-variant-numeric: tabular-nums; }
    </style>

    <div class="container-fluid py-3">

        {{-- ======================================================================= --}}
        {{-- 1. CARD MAESTRO DE BIENVENIDA                                           --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center g-2">
                    <div class="col-12 col-lg-7 text-center text-lg-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mb-1">
                            <span class="text-uppercase text-success fw-bold" style="font-size: 0.68rem; letter-spacing: 0.8px;">
                                <i class="bi bi-shield-check me-1"></i>Portal de Autogestión RSV
                            </span>
                            @if($userNit)
                                <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                    Doc: {{ $userNit }}
                                </span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Hola, {{ $userName }}</h6>
                        <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                            Explora propiedades, gestiona tus reservas, acompañantes, comprobantes de pago y soporte en tiempo real.
                        </p>
                    </div>
                    <div class="col-12 col-lg-5 d-flex justify-content-center justify-content-lg-end align-items-center gap-2 flex-wrap">
                        <span class="badge bg-light text-secondary border rounded-pill fw-normal py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-calendar3 small text-success"></i>
                            <span style="font-size: 0.76rem;">{{ now()->format('d/m/Y h:i A') }}</span>
                            <span class="vr mx-1"></span>
                            <i class="bi bi-person-badge small text-secondary"></i>
                            <span style="font-size: 0.76rem;">ID #{{ auth()->id() }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================================= --}}
        {{-- 2. PANEL DE 6 KPIs DEL CLIENTE (1 FILA SIMÉTRICA + ACCESO DIRECTO)      --}}
        {{-- ======================================================================= --}}
        <div class="row g-2 mb-3">
            {{-- KPI 1: Próximo Viaje --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-reservas" data-child="#mr-sub-itinerario"
                     title="Ir al Itinerario de tu próximo viaje">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Próximo Viaje</span>
                            <i class="bi bi-airplane text-primary opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1">{{ $cliProximo?->format('d/m') ?? '—' }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">{{ $cliProximo?->translatedFormat('M Y') ?? 'sin fecha' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 2: Reservas Activas --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-reservas" data-child="#mr-sub-activas"
                     title="Ver mis reservas activas y pasadas">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Reservas Activas</span>
                            <i class="bi bi-journal-check text-success opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1" id="portalKpiActivas">{{ $cliActivas }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">de <span id="portalKpiTotal">{{ $cliTotal }}</span> total</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 3: Abonado --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-pagos" data-child="#pg-sub-transacciones"
                     title="Ver mis pagos y comprobantes">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Abonado</span>
                            <i class="bi bi-cash-stack text-success opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-success mb-0 lh-1 kpi-money" id="portalKpiAbonado">{{ $fmtMoney($cliAbonado) }}</h5>
                            <span class="text-muted" style="font-size: 0.63rem;">aprobado</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 4: Saldo Pendiente --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-pagos" data-child="#pg-sub-transacciones"
                     title="Registrar soporte de pago">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Saldo</span>
                            <i class="bi bi-wallet2 {{ $cliSaldo > 0 ? 'text-danger' : 'text-success' }} opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold {{ $cliSaldo > 0 ? 'text-danger' : 'text-success' }} mb-0 lh-1 kpi-money" id="portalKpiSaldo">{{ $fmtMoney($cliSaldo) }}</h5>
                            <span class="text-muted" style="font-size: 0.63rem;">{{ $cliSaldo > 0 ? 'pendiente' : 'al día' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 5: Mensajes Soporte --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-chat" data-child="#ch-sub-mensajes"
                     title="Abrir bandeja de mensajes de soporte">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Mensajes</span>
                            <i class="bi bi-chat-dots text-info opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1" id="portalKpiMensajes">{{ $cliMensajes }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">en soporte</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI 6: Mis Reseñas --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card kpi-card border shadow-sm h-100 bg-white js-portal-kpi-nav"
                     data-parent="#cli-pagos" data-child="#pg-sub-reviews"
                     title="Ver y publicar reseñas de mis estadías">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.63rem; letter-spacing: 0.5px;">Mis Reseñas</span>
                            <i class="bi bi-star-fill text-warning opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1" id="portalKpiReviews">{{ $cliReviews }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">publicadas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================================= --}}
        {{-- 3. CARD ÚNICA CON PESTAÑAS PADRE / HIJO                                 --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 bg-white">

            {{-- NAVEGACIÓN PRINCIPAL (PADRES) --}}
            <div class="card-header bg-white p-0 border-0">
                <ul class="nav nav-tabs nav-fusion px-2 pt-2 tab-memory-parent" id="clienteTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cli-nueva" type="button" role="tab">
                            <i class="bi bi-compass me-1"></i> Nueva Reserva
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-reservas" type="button" role="tab">
                            <i class="bi bi-calendar2-check me-1"></i> Mis Reservas
                            <span class="badge bg-light text-secondary border ms-1" id="portalBadgeReservas">{{ $cliTotal }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-historial" type="button" role="tab">
                            <i class="bi bi-clock-history me-1"></i> Historial
                            <span class="badge bg-light text-secondary border ms-1" id="portalBadgeHistorial">{{ $cliHistorial }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-chat" type="button" role="tab">
                            <i class="bi bi-chat-dots me-1"></i> Soporte
                            <span class="badge bg-light text-secondary border ms-1" id="portalBadgeMensajes">{{ $cliMensajes }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-pagos" type="button" role="tab">
                            <i class="bi bi-wallet2 me-1"></i> Pagos y Reseñas
                            <span class="badge bg-light text-secondary border ms-1" id="portalBadgeAbonado">{{ $fmtMoney($cliAbonado) }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="clienteTabsContent">

                    {{-- ─────────── TAB 1: NUEVA RESERVA ─────────── --}}
                    <div class="tab-pane fade show active" id="cli-nueva" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Búsqueda:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#cli-nueva" id="nuevaReservaSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#nr-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#nr-sub-buscador" type="button" role="tab">
                                        <i class="bi bi-search me-1"></i> Buscador
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#nr-sub-checkout" type="button" role="tab">
                                        <i class="bi bi-cart-check me-1"></i> Checkout
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="p-3">
                            @if(view()->exists('rsv.cliente.partials.nueva-reserva-tab'))
                                @include('rsv.cliente.partials.nueva-reserva-tab')
                            @else
                                <div class="text-center py-4 text-muted">Módulo Nueva Reserva no disponible.</div>
                            @endif
                        </div>
                    </div>

                    {{-- ─────────── TAB 2: MIS RESERVAS ─────────── --}}
                    <div class="tab-pane fade" id="cli-reservas" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Mis Reservas:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#cli-reservas" id="misReservasSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#mr-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mr-sub-activas" type="button" role="tab">
                                        <i class="bi bi-calendar2-check me-1"></i> Activas y Pasadas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mr-sub-itinerario" type="button" role="tab">
                                        <i class="bi bi-geo-alt me-1"></i> Itinerario
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#mr-sub-huespedes" type="button" role="tab">
                                        <i class="bi bi-people me-1"></i> Huéspedes
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3 reservas-soft" id="misReservasSubContent">
                            <div class="tab-pane fade show active" id="mr-sub-inicio" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas-tab'))
                                    @include('rsv.cliente.partials.mis-reservas-tab')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Inicio" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="mr-sub-activas" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.activas-pasadas'))
                                    @include('rsv.cliente.partials.mis-reservas.activas-pasadas')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Activas y Pasadas" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="mr-sub-itinerario" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.itinerario'))
                                    @include('rsv.cliente.partials.mis-reservas.itinerario')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Itinerario" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="mr-sub-huespedes" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.huespedes'))
                                    @include('rsv.cliente.partials.mis-reservas.huespedes')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Huéspedes" no encontrada.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── TAB 3: HISTORIAL ─────────── --}}
                    <div class="tab-pane fade" id="cli-historial" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Historial:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#cli-historial" id="historialSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#hi-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#hi-sub-estados" type="button" role="tab">
                                        <i class="bi bi-flag me-1"></i> Estados
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#hi-sub-endosos" type="button" role="tab">
                                        <i class="bi bi-arrow-left-right me-1"></i> Endosos
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3 reservas-soft" id="historialSubContent">
                            <div class="tab-pane fade show active" id="hi-sub-inicio" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.historial-tab'))
                                    @include('rsv.cliente.partials.historial-tab')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Inicio" de historial no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="hi-sub-estados" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.historial.estados'))
                                    @include('rsv.cliente.partials.historial.estados')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Estados" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="hi-sub-endosos" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.historial.endosos'))
                                    @include('rsv.cliente.partials.historial.endosos')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Endosos" no encontrada.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── TAB 4: SOPORTE / CHAT ─────────── --}}
                    <div class="tab-pane fade" id="cli-chat" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Soporte:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#cli-chat" id="chatSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#ch-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ch-sub-mensajes" type="button" role="tab">
                                        <i class="bi bi-chat-dots me-1"></i> Mensajes
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ch-sub-receptores" type="button" role="tab">
                                        <i class="bi bi-person-badge me-1"></i> Receptores
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3 reservas-soft" id="chatSubContent">
                            <div class="tab-pane fade show active" id="ch-sub-inicio" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.chat-tab'))
                                    @include('rsv.cliente.partials.chat-tab')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Inicio" de soporte no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="ch-sub-mensajes" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.chat.mensajes'))
                                    @include('rsv.cliente.partials.chat.mensajes')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Mensajes" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="ch-sub-receptores" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.chat.receptores'))
                                    @include('rsv.cliente.partials.chat.receptores')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Receptores" no encontrada.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── TAB 5: PAGOS Y RESEÑAS ─────────── --}}
                    <div class="tab-pane fade" id="cli-pagos" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" data-parent-tab="#cli-pagos" id="pagosSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pg-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pg-sub-transacciones" type="button" role="tab">
                                        <i class="bi bi-receipt me-1"></i> Transacciones
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pg-sub-pasarelas" type="button" role="tab">
                                        <i class="bi bi-credit-card me-1"></i> Pasarelas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pg-sub-reviews" type="button" role="tab">
                                        <i class="bi bi-star-fill me-1"></i> Reseñas
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3 reservas-soft" id="pagosSubContent">
                            <div class="tab-pane fade show active" id="pg-sub-inicio" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos-tab'))
                                    @include('rsv.cliente.partials.pagos-tab')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Inicio" de pagos no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pg-sub-transacciones" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.transacciones'))
                                    @include('rsv.cliente.partials.pagos.transacciones')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Transacciones" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pg-sub-pasarelas" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.pasarelas'))
                                    @include('rsv.cliente.partials.pagos.pasarelas')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Pasarelas" no encontrada.</div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pg-sub-reviews" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.reviews'))
                                    @include('rsv.cliente.partials.pagos.reviews')
                                @else
                                    <div class="text-center py-4 text-muted">Vista "Reseñas" no encontrada.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ======================================================================= --}}
    {{-- SCRIPT: ORQUESTADOR DE NAVEGACIÓN Y MEMORIA INDEPENDIENTE PADRE/HIJO    --}}
    {{-- ======================================================================= --}}
    <script>
        (function () {
            'use strict';

            var PARENT_KEY = 'rsv_cliente_panel_parent_v3';
            var CHILD_PREFIX = 'rsv_cliente_panel_child_v3_';

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

            // API pública para saltar entre pestañas Padre e Hijo desde KPIs o fragmentos
            window.RsvPortal = {
                go: function (parentSelector, childSelector) {
                    if (parentSelector) showTabBySelector(parentSelector);
                    if (childSelector) {
                        setTimeout(function () {
                            showTabBySelector(childSelector);
                        }, 60);
                    }
                }
            };

            document.addEventListener('DOMContentLoaded', function () {
                // 1. Guardar memoria de Pestaña Padre
                document.querySelectorAll('.tab-memory-parent .nav-link').forEach(function (tab) {
                    tab.addEventListener('shown.bs.tab', function (event) {
                        var targetParent = event.target.getAttribute('data-bs-target');
                        if (!targetParent) return;
                        localStorage.setItem(PARENT_KEY, targetParent);

                        // Restaurar la sub-pestaña específica de este padre
                        var savedChildForParent = localStorage.getItem(CHILD_PREFIX + targetParent);
                        if (savedChildForParent) {
                            showTabBySelector(savedChildForParent);
                        }
                    });
                });

                // 2. Guardar memoria de Sub-pestaña asociada a su Padre correspondiente
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

                // 3. Clic en cualquiera de las 6 tarjetas KPI superiores → Navegación directa
                document.addEventListener('click', function (e) {
                    var kpi = e.target && e.target.closest ? e.target.closest('.js-portal-kpi-nav') : null;
                    if (!kpi) return;
                    var p = kpi.getAttribute('data-parent');
                    var c = kpi.getAttribute('data-child');
                    window.RsvPortal.go(p, c);
                });

                // 4. Restaurar pestaña inicial (priorizando parámetros de URL si existen)
                var urlParams = new URLSearchParams(window.location.search);
                var tabParam  = urlParams.get('tab') || '';
                var mapUrlToTabs = {
                    'nueva-reserva':       ['#cli-nueva', '#nr-sub-inicio'],
                    'nueva-buscador':      ['#cli-nueva', '#nr-sub-buscador'],
                    'nueva-checkout':      ['#cli-nueva', '#nr-sub-checkout'],
                    'mis-reservas':        ['#cli-reservas', '#mr-sub-inicio'],
                    'reservas-activas':    ['#cli-reservas', '#mr-sub-activas'],
                    'reservas-itinerario': ['#cli-reservas', '#mr-sub-itinerario'],
                    'reservas-huespedes':  ['#cli-reservas', '#mr-sub-huespedes'],
                    'historial':           ['#cli-historial', '#hi-sub-inicio'],
                    'historial-estados':   ['#cli-historial', '#hi-sub-estados'],
                    'historial-endosos':   ['#cli-historial', '#hi-sub-endosos'],
                    'chat':                ['#cli-chat', '#ch-sub-inicio'],
                    'chat-mensajes':       ['#cli-chat', '#ch-sub-mensajes'],
                    'chat-receptores':     ['#cli-chat', '#ch-sub-receptores'],
                    'pagos':               ['#cli-pagos', '#pg-sub-inicio'],
                    'pagos-transacciones': ['#cli-pagos', '#pg-sub-transacciones'],
                    'pagos-pasarelas':     ['#cli-pagos', '#pg-sub-pasarelas'],
                    'pagos-reviews':       ['#cli-pagos', '#pg-sub-reviews']
                };

                if (tabParam && mapUrlToTabs[tabParam]) {
                    window.RsvPortal.go(mapUrlToTabs[tabParam][0], mapUrlToTabs[tabParam][1]);
                } else {
                    var savedParent = localStorage.getItem(PARENT_KEY);
                    if (savedParent) {
                        showTabBySelector(savedParent);
                        var savedChild = localStorage.getItem(CHILD_PREFIX + savedParent);
                        if (savedChild) {
                            showTabBySelector(savedChild);
                        }
                    }
                }
            });
        })();
    </script>

    {{-- ======================================================================= --}}
    {{-- NEUTRALIZADOR MAESTRO v2 (Anti Modales fantasmas de Livewire/Ajax)      --}}
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
                if (n) n.classList.remove('loading', 'is-loading', 'js-loading', 'page-is-loading', 'preloader-active');
            });
        }
        limpiarRuedita();
        new MutationObserver(function () { limpiarRuedita(); }).observe(document.body, { childList: true });

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('message.sent', function () {
                    limpiarRestosModal();
                });
            }
        });

        if (!SECCION) {
            var t = document.createElement('style');
            t.textContent = MODAL + '{display:none!important;opacity:0!important;visibility:hidden!important;pointer-events:none!important;}';
            document.head.appendChild(t);
        }

        document.addEventListener('show.bs.modal', function (e) {
            var el = e.target;
            if (!el || el.nodeType !== 1 || !(el.matches && el.matches(MODAL))) return;
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
                    if (m.classList.contains('show') || m.style.display === 'block') {
                        limpiarRestosModal();
                    }
                }).observe(m, { attributes: true, attributeFilter: ['class', 'style'] });
            });
        }
        vigilarModal();
        new MutationObserver(function () { vigilarModal(); }).observe(document.body, { childList: true, subtree: true });
    })();
    </script>
</x-base-layout>
