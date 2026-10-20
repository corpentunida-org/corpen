<x-base-layout>
    @section('titlepage', 'Mi Portal - Sistema RSV')

    {{-- ========================================================= --}}
    {{-- NOTIFICACIONES DEL SISTEMA                                --}}
    {{-- ========================================================= --}}
    @include('rsv.components.alert')

    {{-- ============================================================================== --}}
    {{-- ESTILOS DE FUSIÓN (idénticos al Panel Admin · simetría padre-hijo)             --}}
    {{-- ============================================================================== --}}
    <style>
        /* 1. Contenedor de las pestañas principales (Padre) */
        .nav-fusion { border-bottom: 1px solid #badbcc !important; }
        /* 2. Pestañas en estado normal */
        .nav-fusion .nav-link {
            color: #6c757d; font-weight: 600; border: 1px solid transparent;
            border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem;
            margin-bottom: -1px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }
        /* 3. Hover */
        .nav-fusion .nav-link:hover { color: #198754; border-color: #e9ecef #e9ecef #badbcc; }
        /* 4. Activa */
        .nav-fusion .nav-link.active {
            color: #0f5132 !important; background-color: #d1e7dd !important;
            border-color: #badbcc #badbcc #d1e7dd #badbcc !important;
        }
        /* 5. Submenú (Hijo) */
        .subnav-fusion {
            background-color: #d1e7dd; border-bottom: 1px solid #badbcc; border-top: none;
            border-bottom-left-radius: 0.375rem; border-bottom-right-radius: 0.375rem;
            padding: 0.6rem 1rem; margin-bottom: 1.5rem;
        }
        /* 6. Píldoras del submenú */
        .sub-pills .nav-link {
            color: #146c43; background-color: rgba(255,255,255,0.65);
            font-size: 0.8rem; font-weight: 600; padding: 0.35rem 0.8rem;
            border-radius: 0.375rem; border: 1px solid transparent; transition: all 0.2s;
        }
        .sub-pills .nav-link:hover { background-color: #fff; color: #0f5132; border-color: #badbcc; }
        .sub-pills .nav-link.active {
            background-color: #198754 !important; color: #fff !important;
            border-color: #198754 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        /* 7. Hover minimalista KPI */
        .kpi-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.05) !important; }

        /* 8. Formateador de moneda compacto (estilo admin) */
        .kpi-money { font-variant-numeric: tabular-nums; }
    </style>

    @php
        /* ── Formateador de moneda (simétrico al admin) ── */
        $fmtMoney = function ($n) {
            if ($n === null || $n === 0) return '—';
            if ($n >= 1000000) return '$' . number_format($n / 1000000, 1) . 'M';
            if ($n >= 1000)    return '$' . number_format($n / 1000, 1) . 'k';
            return '$' . number_format((float) $n, 0);
        };

        /* ── KPIs del cliente con valores por defecto (la vista nunca explota
              aunque aún no exista el controlador que los inyecta) ── */
        $cliProximo    = $kpiProximo    ?? null;   // Carbon|null
        $cliActivas    = $kpiActivas    ?? 0;
        $cliTotal      = $kpiTotal      ?? 0;
        $cliAbonado    = $kpiAbonado    ?? 0;
        $cliSaldo      = $kpiSaldo      ?? 0;
        $cliMensajes   = $kpiMensajes   ?? 0;
        $cliReviews    = $kpiReviews    ?? 0;
        $cliHistorial  = $kpiHistorial  ?? 0;
    @endphp

    <div class="container-fluid py-4">

        {{-- ======================================================================= --}}
        {{-- 1. CARD MAESTRO DE BIENVENIDA (simétrico al "Selector Maestro" admin)   --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">

                    <div class="col-12 col-lg-7 text-center text-lg-start">
                        <span class="text-uppercase text-secondary fw-semibold small d-block mb-1" style="letter-spacing: 1px;">
                            Portal del Huésped RSV
                        </span>
                        <h6 class="fw-bold mb-1 text-dark">Hola, {{ auth()->user()->name ?? 'Huésped' }}</h6>
                        <p class="mb-0 text-muted small">
                            Explora propiedades, gestiona tus reservas, pagos y soporte desde un solo lugar.
                        </p>
                    </div>

                    <div class="col-12 col-lg-5 d-flex justify-content-center justify-content-lg-end">
                        <span class="badge bg-light text-secondary border rounded-pill fw-normal py-2 px-3 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-calendar3 small text-muted"></i>
                            <span style="font-size: 0.8rem;">{{ now()->format('d/m/Y h:i A') }}</span>
                            <span class="vr mx-1"></span>
                            <i class="bi bi-person-badge small text-muted"></i>
                            <span style="font-size: 0.8rem;">Cuenta #{{ auth()->id() }}</span>
                        </span>
                    </div>

                </div>
            </div>
        </div>

        {{-- ======================================================================= --}}
        {{-- 2. PANEL DE KPIs DEL CLIENTE (simétrico al panel de KPIs del admin)     --}}
        {{-- ======================================================================= --}}
        <div class="row g-2 mb-4">

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Próximo Viaje</span>
                            <i class="bi bi-airplane text-primary opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1">{{ $cliProximo?->format('d/m') ?? '—' }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">{{ $cliProximo?->translatedFormat('M') ?? 'sin reservas' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Reservas Activas</span>
                            <i class="bi bi-journal-check text-danger opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1">{{ $cliActivas }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">de {{ $cliTotal }} total</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Abonado</span>
                            <i class="bi bi-cash-stack text-success opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-success mb-0 lh-1 kpi-money">{{ $fmtMoney($cliAbonado) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo</span>
                            <i class="bi bi-wallet2 {{ $cliSaldo > 0 ? 'text-danger' : 'text-success' }} opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold {{ $cliSaldo > 0 ? 'text-danger' : 'text-success' }} mb-0 lh-1 kpi-money">{{ $fmtMoney($cliSaldo) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Mensajes</span>
                            <i class="bi bi-chat-dots text-info opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1">{{ $cliMensajes }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">en soporte</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card kpi-card border border-light shadow-sm h-100 bg-white">
                    <div class="card-body p-2 px-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Mis Reseñas</span>
                            <i class="bi bi-star-fill text-warning opacity-75"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h5 class="fw-bold text-dark mb-0 lh-1">{{ $cliReviews }}</h5>
                            <span class="text-muted" style="font-size: 0.65rem;">publicadas</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================================= --}}
        {{-- 3. CARD ÚNICA CON PESTAÑAS PADRE/HIJO (idéntico patrón al admin)        --}}
        {{-- ======================================================================= --}}
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white p-0 border-0">
                <ul class="nav nav-tabs nav-fusion px-2 pt-2 tab-memory-parent" id="clienteTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cli-nueva" type="button" role="tab">
                            <i class="bi bi-search me-1"></i> Nueva Reserva
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-reservas" type="button" role="tab">
                            <i class="bi bi-calendar2-check me-1"></i> Mis Reservas <span class="badge bg-light text-secondary ms-1">{{ $cliTotal }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-historial" type="button" role="tab">
                            <i class="bi bi-clock-history me-1"></i> Historial <span class="badge bg-light text-secondary ms-1">{{ $cliHistorial }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-chat" type="button" role="tab">
                            <i class="bi bi-chat-dots me-1"></i> Soporte <span class="badge bg-light text-secondary ms-1">{{ $cliMensajes }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cli-pagos" type="button" role="tab">
                            <i class="bi bi-wallet2 me-1"></i> Pagos y Reseñas <span class="badge bg-light text-secondary ms-1">{{ $fmtMoney($cliAbonado) }}</span>
                        </button>
                    </li>

                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="clienteTabsContent">

                    {{-- ─────────── CLIENTE · TAB 1: NUEVA RESERVA ─────────── --}}
                    <div class="tab-pane fade show active" id="cli-nueva" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Búsqueda:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="nuevaReservaSubTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#nr-sub-buscador" type="button" role="tab"><i class="bi bi-search me-1"></i> Buscador</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#nr-sub-checkout" type="button" role="tab"><i class="bi bi-cart-check me-1"></i> Checkout</button></li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="nuevaReservaSubContent">
                            <div class="tab-pane fade show active" id="nr-sub-buscador" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.nueva-reserva.buscador'))
                                    @include('rsv.cliente.partials.nueva-reserva.buscador')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Buscador (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="nr-sub-checkout" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.nueva-reserva.checkout'))
                                    @include('rsv.cliente.partials.nueva-reserva.checkout')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Checkout (Próximamente)</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── CLIENTE · TAB 2: MIS RESERVAS ─────────── --}}
                    <div class="tab-pane fade" id="cli-reservas" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Mis Reservas:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="misReservasSubTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#mr-sub-activas" type="button" role="tab"><i class="bi bi-calendar2-check me-1"></i> Activas y Pasadas</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#mr-sub-itinerario" type="button" role="tab"><i class="bi bi-list-check me-1"></i> Itinerario</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#mr-sub-huespedes" type="button" role="tab"><i class="bi bi-people me-1"></i> Huéspedes</button></li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="misReservasSubContent">
                            <div class="tab-pane fade show active" id="mr-sub-activas" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.activas-pasadas'))
                                    @include('rsv.cliente.partials.mis-reservas.activas-pasadas')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Activas y Pasadas (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="mr-sub-itinerario" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.itinerario'))
                                    @include('rsv.cliente.partials.mis-reservas.itinerario')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Itinerario (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="mr-sub-huespedes" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.mis-reservas.huespedes'))
                                    @include('rsv.cliente.partials.mis-reservas.huespedes')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Huéspedes (Próximamente)</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── CLIENTE · TAB 3: HISTORIAL ─────────── --}}
                    <div class="tab-pane fade" id="cli-historial" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Historial:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="historialSubTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#hi-sub-estados" type="button" role="tab"><i class="bi bi-flag me-1"></i> Estados</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#hi-sub-endosos" type="button" role="tab"><i class="bi bi-arrow-left-right me-1"></i> Endosos</button></li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="historialSubContent">
                            <div class="tab-pane fade show active" id="hi-sub-estados" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.historial.estados'))
                                    @include('rsv.cliente.partials.historial.estados')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Historial de Estados (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="hi-sub-endosos" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.historial.endosos'))
                                    @include('rsv.cliente.partials.historial.endosos')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Historial de Endosos (Próximamente)</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── CLIENTE · TAB 4: SOPORTE / CHAT ─────────── --}}
                    <div class="tab-pane fade" id="cli-chat" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema de Soporte:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="chatSubTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#ch-sub-mensajes" type="button" role="tab"><i class="bi bi-chat-dots me-1"></i> Mensajes</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#ch-sub-receptores" type="button" role="tab"><i class="bi bi-person-badge me-1"></i> Receptores</button></li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="chatSubContent">
                            <div class="tab-pane fade show active" id="ch-sub-mensajes" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.chat.mensajes'))
                                    @include('rsv.cliente.partials.chat.mensajes')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Centro de Mensajes (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="ch-sub-receptores" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.chat.receptores'))
                                    @include('rsv.cliente.partials.chat.receptores')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Tipos de Receptor (Próximamente)</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ─────────── CLIENTE · TAB 5: PAGOS Y RESEÑAS ─────────── --}}
                    <div class="tab-pane fade" id="cli-pagos" role="tabpanel">
                        <div class="subnav-fusion d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-success-emphasis fw-bold small">
                                <i class="bi bi-diagram-3-fill"></i> Ecosistema Financiero:
                            </div>
                            <ul class="nav nav-pills sub-pills gap-1 mb-0 tab-memory-child" id="pagosSubTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pg-sub-transacciones" type="button" role="tab"><i class="bi bi-receipt me-1"></i> Transacciones</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pg-sub-pasarelas" type="button" role="tab"><i class="bi bi-credit-card me-1"></i> Pasarelas</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pg-sub-reviews" type="button" role="tab"><i class="bi bi-star-fill me-1"></i> Reseñas</button></li>
                            </ul>
                        </div>
                        <div class="tab-content p-4" id="pagosSubContent">
                            <div class="tab-pane fade show active" id="pg-sub-transacciones" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.transacciones'))
                                    @include('rsv.cliente.partials.pagos.transacciones')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Mis Transacciones (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pg-sub-pasarelas" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.pasarelas'))
                                    @include('rsv.cliente.partials.pagos.pasarelas')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Pasarelas Disponibles (Próximamente)</p>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pg-sub-reviews" role="tabpanel">
                                @if(view()->exists('rsv.cliente.partials.pagos.reviews'))
                                    @include('rsv.cliente.partials.pagos.reviews')
                                @else
                                    <p class="text-muted small mb-0"><i class="bi bi-hourglass-split me-1"></i> Mis Reseñas (Próximamente)</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</x-base-layout>
