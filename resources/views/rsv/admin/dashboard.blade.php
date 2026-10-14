<x-base-layout>
    @section('titlepage', 'Panel Maestro de Inmuebles - RSV')

    {{-- Notificaciones del Sistema --}}
    @include('rsv.components.alert')

    {{-- Estilos para el diseño unificado en bloque pastel (Padre e Hijos) --}}
    <style>
        .rsv-kicker {
            font-size: 0.62rem; font-weight: 600; letter-spacing: 1px;
            text-transform: uppercase; color: #94a3b8;
        }
        .rsv-soft-card {
            background: #fff; border: 1px solid #f1f5f9;
            border-radius: 8px; box-shadow: none;
        }
        .rsv-soft-card:hover { border-color: #e9eef4; }

        /* Pestañas principales suaves */
        .nav-tabs .nav-link {
            font-size: 0.72rem; font-weight: 600; color: #94a3b8;
            border-bottom: 2px solid transparent !important;
            padding: 0.5rem 0.9rem;
            transition: color .15s ease, background-color .15s ease;
        }
        .nav-tabs .nav-link:hover { color: #475569; }

        /* Contador de las pestañas padre: gris en reposo · VERDE CLARO al seleccionar */
        .nav-tabs .nav-link .tab-count {
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: #c3ccd6;
            transition: color .15s ease;
        }
        .nav-tabs .nav-link.active .tab-count { color: #6fbf93; }

        /* ═══ PESTAÑA PADRE ACTIVA fusionada en bloque pastel ═══
           · Propiedades: #global-inmuebles (A) / #inmuebles  (B)
           · Reservas:    #global-reservas  (A) / #reservas   (B)
           + gancho robusto .rsv-tab-parent. */
        .nav-tabs .nav-link.active[data-bs-target="#global-inmuebles"],
        .nav-tabs .nav-link.active[data-bs-target="#inmuebles"],
        .nav-tabs .nav-link.active[data-bs-target="#global-reservas"],
        .nav-tabs .nav-link.active[data-bs-target="#reservas"],
        .nav-tabs .nav-link.active.rsv-tab-parent {
            color: #2e5c43 !important;
            background-color: #e3f1e9 !important;
            border-color: #d1e7d9 #d1e7d9 #e3f1e9 #d1e7d9 !important;
            border-radius: 6px 6px 0 0 !important;
            font-weight: 700;
        }

        /* SUBMENÚ HIJO ACOPLADO (estructura compartida Propiedades/Reservas) */
        .rsv-subnav-bar {
            background: #e3f1e9;
            border: 1px solid #d1e7d9;
            border-top: none;
            border-radius: 0 0 6px 6px;
            padding: 0.45rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .rsv-sub-pills .nav-link {
            font-size: 0.65rem;
            font-weight: 500;
            color: #4a7c59;
            background: rgba(255, 255, 255, 0.6);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
            border: 1px solid #d1e7d9;
        }
        .rsv-sub-pills .nav-link:hover {
            color: #2e5c43;
            background: #ffffff;
        }
        .rsv-sub-pills .nav-link.active {
            color: #ffffff !important;
            background: #3d7a5c !important;
            border-color: #3d7a5c !important;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        /* Placeholder de módulos aún no activados */
        .rsv-module-placeholder {
            background: #fff; border: 1px dashed #e2e8f0;
            border-radius: 6px; padding: 2rem 1rem;
            text-align: center;
        }

        /* Selector sobrio */
        .rsv-select {
            font-size: 0.78rem; font-weight: 600; color: #475569;
            border: 1px solid #e9eef4 !important;
            box-shadow: none !important;
        }
        .rsv-select:focus { border-color: #c8d6cd !important; }
        .rsv-input-icn {
            background: #fafbfd !important; border: 1px solid #e9eef4 !important;
            color: #94a3b8 !important; font-size: 0.8rem;
        }

        /* Botón suave */
        .rsv-btn-soft {
            background: #f0f7f2; color: #3d7a5c;
            border: 1px solid #d5e7dc;
            font-size: 0.72rem; font-weight: 600;
        }
        .rsv-btn-soft:hover { background: #e3f1e9; color: #34684e; }

        /* ═══ KPIS RÁPIDOS (8 tarjetas pastel) ═══ */
        .rsv-kpi-card {
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
        }
        .rsv-kpi-card:hover {
            transform: translateY(-2px);
            border-color: #d5e7dc;
            box-shadow: 0 3px 10px rgba(15,23,42,.04);
        }
        .rsv-kpi-lbl {
            text-transform: uppercase; font-weight: 700; margin-bottom: 1px;
            font-size: 0.6rem; color: #b3bcc7; letter-spacing: 0.5px;
        }
        .rsv-kpi-val { margin-bottom: 0; font-weight: 700; font-size: 0.95rem; color: #475569; }
        .rsv-kpi-sub { font-size: 0.55rem; font-weight: 600; color: #c3ccd6; margin-top: 2px; letter-spacing: .3px; }
        .rsv-kpi-bar {
            height: 3px; border-radius: 2px; background: #f1f5f9;
            overflow: hidden; margin-top: 5px;
        }
        .rsv-kpi-bar > span {
            display: block; height: 100%; border-radius: 2px;
            background: linear-gradient(90deg, #a9d8b8, #5d8a70);
        }
    </style>

    <div class="container-fluid py-4">

        {{-- ======================================================================= --}}
        {{-- 1. SELECTOR MAESTRO                                                     --}}
        {{-- ======================================================================= --}}
        <div class="rsv-soft-card mb-4">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">

                    <div class="col-12 col-lg-5 text-center text-lg-start">
                        <span class="rsv-kicker d-block mb-1">Panel Conector RSV</span>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #334155;">Gestión por Inmueble</h6>
                        <p class="mb-0" style="font-size: 0.7rem; color: #94a3b8;">Seleccione una propiedad para cargar su ecosistema o elija ver todas.</p>
                    </div>

                    <div class="col-12 col-lg-7">
                        <form action="{{ route('rsv.admin.dashboard') }}" method="GET" class="d-flex w-100">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text rsv-input-icn">
                                    <i class="bi bi-building"></i>
                                </span>

                                <select class="form-select rsv-select"
                                        name="inmueble_id"
                                        onchange="this.form.submit()"
                                        aria-label="Seleccionar Inmueble">

                                    <option value="" class="text-muted">-- Seleccione una propiedad para operar --</option>

                                    <option value="todos" {{ request('inmueble_id') === 'todos' ? 'selected' : '' }}>
                                        Ver todas las propiedades (Panel Global)
                                    </option>

                                    @isset($listaInmuebles)
                                        @foreach($listaInmuebles as $opcion)
                                            <option value="{{ $opcion->id }}" {{ request('inmueble_id', $inmueble->id ?? '') == $opcion->id ? 'selected' : '' }}>
                                                {{ $opcion->name }}{{ isset($opcion->city) && $opcion->city ? ' (' . $opcion->city . ')' : '' }} - {{ $opcion->active ? 'Activo' : 'Inactivo' }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <noscript>
                                <button type="submit" class="btn btn-sm rsv-btn-soft ms-2">Cargar</button>
                            </noscript>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL A: VISTA GLOBAL                                             --}}
        {{-- ======================================================================= --}}
        @if(request('inmueble_id') === 'todos')

            @php
                /* CONTADORES DE LAS PESTAÑAS PADRE (modo global) */
                $tabTotalInmuebles = (isset($inmueblesGrid) && method_exists($inmueblesGrid, 'total'))
                    ? $inmueblesGrid->total()
                    : (isset($listaInmuebles) ? $listaInmuebles->count() : 0);

                $clsReserva = null;
                foreach (['App\\Models\\Rsv\\Reserva', 'App\\Models\\Rsv\\Reservas', 'App\\Models\\Rsv\\ReservaInmueble'] as $clsTry) {
                    if (class_exists($clsTry)) { $clsReserva = $clsTry; break; }
                }
                $clsBloqueo = null;
                foreach (['App\\Models\\Rsv\\BloqueoCalendario', 'App\\Models\\Rsv\\BloqueosCalendario', 'App\\Models\\Rsv\\CalendarioBloqueo'] as $clsTry) {
                    if (class_exists($clsTry)) { $clsBloqueo = $clsTry; break; }
                }

                $tabReservas = null;
                if ($clsReserva) {
                    try { $tabReservas = $clsReserva::count(); } catch (\Throwable $e) { $tabReservas = null; }
                }
                if (is_null($tabReservas) && isset($listaInmuebles)) {
                    $tabReservas = 0;
                    foreach ($listaInmuebles as $p) { $tabReservas += ($p->reservas_count ?? 0); }
                }

                $tabBloqueos = null;
                if ($clsBloqueo) {
                    try { $tabBloqueos = $clsBloqueo::count(); } catch (\Throwable $e) { $tabBloqueos = null; }
                }

                $tabFinanzas = null;
                if ($clsReserva) {
                    foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                        try { $tabFinanzas = $clsReserva::sum($colTry); break; }
                        catch (\Throwable $e) { continue; }
                    }
                }

                $fmtMoney = function ($n) {
                    if ($n === null) return '—';
                    if ($n >= 1000000) return '$' . number_format($n / 1000000, 1) . 'M';
                    if ($n >= 1000)    return '$' . number_format($n / 1000, 1) . 'k';
                    return '$' . number_format((float) $n, 0);
                };

                $tabAuditoria = null;
                if (isset($auditoria)) {
                    $tabAuditoria = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();
                }
            @endphp

            <div class="rsv-soft-card overflow-hidden mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div>
                        <div class="rsv-kicker mb-1"><i class="bi bi-grid-3x3-gap me-1"></i> Panel Global</div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #334155;">Gestión Global del Sistema</h6>
                        <p class="mb-0" style="font-size: 0.7rem; color: #94a3b8;">Vista consolidada de propiedades, reservas, calendario, finanzas y auditoría.</p>
                    </div>
                    <span class="badge rounded-pill fw-semibold" style="font-size: 0.62rem; background-color: #f9f7fc; color: #8f7bb5; border: 1px solid #e9e2f2;">
                        Total Inmuebles: {{ $tabTotalInmuebles }}
                    </span>
                </div>

                {{-- PESTAÑAS GLOBALES PRINCIPALES (MENÚ PADRE) --}}
                <div class="card-header bg-white border-bottom p-0" style="border-color: #f1f5f9 !important;">
                    <ul class="nav nav-tabs nav-fill border-0 pt-1 px-1" id="globalAdminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rsv-tab-parent" data-bs-toggle="tab" data-bs-target="#global-inmuebles" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-buildings me-1"></i> Propiedades <span class="tab-count">({{ $tabTotalInmuebles }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rsv-tab-parent" data-bs-toggle="tab" data-bs-target="#global-reservas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas Globales <span class="tab-count">({{ $tabReservas ?? '—' }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-calendario" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar3 me-1"></i> Calendario / Bloqueos <span class="tab-count">({{ $tabBloqueos ?? '—' }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-finanzas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas <span class="tab-count">({{ $fmtMoney($tabFinanzas) }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-auditoria" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-shield-check me-1"></i> Auditoría <span class="tab-count">({{ $tabAuditoria ?? '—' }})</span>
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- CONTENIDO DE LAS PESTAÑAS GLOBALES --}}
                <div class="tab-content" id="globalAdminTabsContent">

                    {{-- TAB 1: PROPIEDADES (5 SUBMENÚS HIJOS) --}}
                    <div class="tab-pane fade show active" id="global-inmuebles" role="tabpanel">

                        <div class="rsv-subnav-bar">
                            <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                <i class="bi bi-diagram-3-fill"></i>
                                <span class="fw-bold">Ecosistema Inmobiliario:</span>
                            </div>
                            <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="inmueblesSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-show" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#sub-catalogo" type="button" role="tab">
                                        <i class="bi bi-grid-3x3 me-1"></i> Catálogo
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tarifa" type="button" role="tab">
                                        <i class="bi bi-cash-coin me-1"></i> Tarifas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-galeria" type="button" role="tab">
                                        <i class="bi bi-images me-1"></i> Galería
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-tipo" type="button" role="tab">
                                        <i class="bi bi-tags me-1"></i> Tipos
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3" id="inmueblesSubContent">

                            <div class="tab-pane fade" id="sub-show" role="tabpanel">
                                @include('rsv.admin.partials.inmuebles.show')
                            </div>

                            <div class="tab-pane fade show active" id="sub-catalogo" role="tabpanel">
                                <div class="container-fluid px-0">
                                    @include('rsv.admin.partials.inmuebles.catalogo')
                                </div>
                            </div>

                            <div class="tab-pane fade" id="sub-tarifa" role="tabpanel">
                                @include('rsv.admin.partials.inmuebles.tarifa')
                            </div>

                            <div class="tab-pane fade" id="sub-galeria" role="tabpanel">
                                @include('rsv.admin.partials.inmuebles.galeria')
                            </div>

                            <div class="tab-pane fade" id="sub-tipo" role="tabpanel">
                                @include('rsv.admin.partials.inmuebles.tipo')
                            </div>

                        </div>

                    </div>

                    {{-- ══════════════════════════════════════════════════════════════════════ --}}
                    {{-- TAB 2: RESERVAS GLOBALES · PADRE con subnav ACTIVO (6 hijos)            --}}
                    {{-- · show (Inicio) y reservas (Reservas): DESCOMENTADOS y funcionando --}}
                    {{-- · Huéspedes/Estados/Orígenes/Historial: includes COMENTADOS hasta       --}}
                    {{--   que decidas activarlos (placeholders elegantes mientras tanto)       --}}
                    {{-- ══════════════════════════════════════════════════════════════════════ --}}
                    <div class="tab-pane fade" id="global-reservas" role="tabpanel">

                        {{-- SUBMENÚ HIJO DE RESERVAS (6 botones · misma estructura que Propiedades) --}}
                        <div class="rsv-subnav-bar">
                            <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                <i class="bi bi-diagram-3-fill"></i>
                                <span class="fw-bold">Ecosistema de Reservas:</span>
                            </div>
                            <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="reservasSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-inicio" type="button" role="tab">
                                        <i class="bi bi-house-door me-1"></i> Inicio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#res-sub-reservas" type="button" role="tab">
                                        <i class="bi bi-journal-check me-1"></i> Reservas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-huespedes" type="button" role="tab">
                                        <i class="bi bi-people me-1"></i> Huéspedes
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-status" type="button" role="tab">
                                        <i class="bi bi-flag me-1"></i> Estados
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-origenes" type="button" role="tab">
                                        <i class="bi bi-box-arrow-in-down me-1"></i> Orígenes
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#res-sub-historial" type="button" role="tab">
                                        <i class="bi bi-clock-history me-1"></i> Historial
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-3" id="reservasSubContent">

                            {{-- 1 · INICIO · partial show · DESCOMENTADO --}}
                            <div class="tab-pane fade" id="res-sub-inicio" role="tabpanel">
                                @include('rsv.admin.partials.reservas.show')
                            </div>

                            {{-- 2 · RESERVAS · motor v12 · DESCOMENTADO --}}
                            <div class="tab-pane fade show active" id="res-sub-reservas" role="tabpanel">
                                @include('rsv.admin.partials.reservas.reservas')
                            </div>

                            {{-- 3 · HUÉSPEDES · include COMENTADO hasta activar --}}
                            <div class="tab-pane fade" id="res-sub-huespedes" role="tabpanel">
                                @include('rsv.admin.partials.reservas.reserva_huespedt')
                            </div>

                            {{-- 4 · ESTADOS · include COMENTADO hasta activar --}}
                            <div class="tab-pane fade" id="res-sub-status" role="tabpanel">
                                @include('rsv.admin.partials.reservas.status')
                            </div>

                            {{-- 5 · ORÍGENES · include COMENTADO hasta activar --}}
                            <div class="tab-pane fade" id="res-sub-origenes" role="tabpanel">
                                @include('rsv.admin.partials.reservas.origen_reserva')
                            </div>

                            {{-- 6 · HISTORIAL · include COMENTADO hasta activar --}}
                            <div class="tab-pane fade" id="res-sub-historial" role="tabpanel">
                                @include('rsv.admin.partials.reservas.historial_estado')
                            </div>

                        </div>

                    </div>

                    {{-- TAB 3: CALENDARIO GLOBAL --}}
                    <div class="tab-pane fade p-3" id="global-calendario" role="tabpanel">
                        @include('rsv.admin.partials.tab-calendario')
                    </div>

                    {{-- TAB 4: FINANZAS GLOBALES --}}
                    <div class="tab-pane fade p-3" id="global-finanzas" role="tabpanel">
                        @include('rsv.admin.partials.tab-finanzas')
                    </div>

                    {{-- TAB 5: AUDITORÍA GLOBAL --}}
                    <div class="tab-pane fade p-3" id="global-auditoria" role="tabpanel">
                        <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">
                            <h6 class="fw-bold mb-3" style="font-size: 0.82rem; color: #334155;">
                                <i class="bi bi-shield-check me-1 text-success"></i> Registro de Auditoría del Sistema
                            </h6>
                            @isset($auditoria)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0" style="font-size: 0.75rem;">
                                        <thead class="table-light text-uppercase" style="font-size: 0.65rem; color: #64748b;">
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
        {{-- ======================================================================= --}}
        {{-- CONDICIONAL B: ECOSISTEMA DE UN INMUEBLE ESPECÍFICO                     --}}
        {{-- ======================================================================= --}}
        @elseif(isset($inmueble) && $inmueble)

            @php
                /* CÁLCULO DEFENSIVO DE KPIS + CONTADORES DE PESTAÑAS PADRE */
                $kpiNoche      = optional($inmueble->latestTarifa)->precio_noche ?? null;
                $kpiMinReserva = optional($inmueble->latestTarifa)->precio_minimo_reserva ?? null;

                $reservasCol = method_exists($inmueble, 'reservas') ? ($inmueble->reservas ?? collect()) : collect();

                $kpiFEntrada = fn($r) => $r->fecha_entrada ?? $r->check_in  ?? $r->fecha_inicio  ?? $r->fecha_llegada ?? null;
                $kpiFSalida  = fn($r) => $r->fecha_salida  ?? $r->check_out ?? $r->fecha_fin     ?? null;

                $hoy = \Carbon\Carbon::today();

                $kpiProximas = 0;
                $kpiProximaEntrada = null;
                foreach ($reservasCol as $r) {
                    $fe = $kpiFEntrada($r);
                    if (!$fe) continue;
                    try { $feC = \Carbon\Carbon::parse($fe); } catch (\Throwable $e) { continue; }
                    if ($feC->gte($hoy)) {
                        $kpiProximas++;
                        if (!$kpiProximaEntrada || $feC->lt($kpiProximaEntrada)) $kpiProximaEntrada = $feC;
                    }
                }

                $inicioMes  = $hoy->copy()->startOfMonth();
                $finMes     = $hoy->copy()->endOfMonth();
                $diasDelMes = $hoy->daysInMonth;
                $nochesOcupadas = 0;
                foreach ($reservasCol as $r) {
                    $fe = $kpiFEntrada($r);
                    $fs = $kpiFSalida($r);
                    if (!$fe || !$fs) continue;
                    try {
                        $ini = \Carbon\Carbon::parse($fe);
                        $fin = \Carbon\Carbon::parse($fs);
                    } catch (\Throwable $e) { continue; }
                    $desde = $ini->gt($inicioMes) ? $ini : $inicioMes;
                    $hasta = $fin->lt($finMes) ? $fin : $finMes;
                    if ($hasta->gt($desde)) {
                        $nochesOcupadas += (int) ceil($desde->diffInDays($hasta));
                    }
                }
                if ($nochesOcupadas > $diasDelMes) $nochesOcupadas = $diasDelMes;
                $kpiOcupacion = $diasDelMes > 0 ? (int) round(($nochesOcupadas / $diasDelMes) * 100) : 0;

                $resenasCol = null;
                foreach (['resenas', 'reseñas', 'reviews', 'calificaciones', 'valoraciones'] as $kpiRel) {
                    if (method_exists($inmueble, $kpiRel)) { $resenasCol = $inmueble->{$kpiRel}; break; }
                }
                $kpiResenasCount = $resenasCol ? $resenasCol->count() : 0;
                $kpiRating = null;
                if ($kpiResenasCount > 0) {
                    $suma = 0; $validas = 0;
                    foreach ($resenasCol as $res) {
                        $v = $res->calificacion ?? $res->puntaje ?? $res->puntuacion ?? $res->rating ?? $res->estrellas ?? null;
                        if (!is_null($v) && is_numeric($v)) { $suma += (float) $v; $validas++; }
                    }
                    if ($validas > 0) $kpiRating = round($suma / $validas, 1);
                }

                $mediaCol  = method_exists($inmueble, 'multimedia') ? ($inmueble->multimedia ?? collect()) : collect();
                $kpiFotos  = $mediaCol->where('tipo_multimedia', 'imagen')->count();
                $kpiVideos = $mediaCol->where('tipo_multimedia', 'video')->count();

                $bloqueosCol = method_exists($inmueble, 'bloqueosCalendario')
                    ? ($inmueble->bloqueosCalendario ?? collect())
                    : collect();

                $ingresosInmueble = null;
                foreach ($reservasCol as $r) {
                    foreach (['precio_total', 'total', 'monto_total', 'valor_total'] as $colTry) {
                        if (isset($r->{$colTry}) && is_numeric($r->{$colTry})) {
                            $ingresosInmueble = ($ingresosInmueble ?? 0) + (float) $r->{$colTry};
                            break;
                        }
                    }
                }
                $fmtMoney = function ($n) {
                    if ($n === null) return '—';
                    if ($n >= 1000000) return '$' . number_format($n / 1000000, 1) . 'M';
                    if ($n >= 1000)    return '$' . number_format($n / 1000, 1) . 'k';
                    return '$' . number_format((float) $n, 0);
                };

                $auditoriaCount = null;
                if (isset($auditoria)) {
                    $auditoriaCount = method_exists($auditoria, 'total') ? $auditoria->total() : $auditoria->count();
                }
            @endphp

            {{-- MÉTRICAS RÁPIDAS DEL INMUEBLE · 8 KPIS --}}
            <div class="row g-2 mb-4">

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Capacidad</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-people me-1" style="font-size: 0.7rem; color: #a9cfe8;"></i>{{ $inmueble->capacidad_maxima ?? 0 }}
                            </h6>
                            <div class="rsv-kpi-sub">huéspedes máx.</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Tarifa / Noche</h6>
                            <h6 class="rsv-kpi-val" style="color: #5d8a70;">
                                <i class="bi bi-cash-stack me-1" style="font-size: 0.7rem;"></i>{{ !is_null($kpiNoche) ? '$' . number_format($kpiNoche, 0) : '—' }}
                            </h6>
                            <div class="rsv-kpi-sub">tarifa base vigente</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Tarifa Mínima</h6>
                            <h6 class="rsv-kpi-val" style="color: #5d8a70;">
                                <i class="bi bi-piggy-bank me-1" style="font-size: 0.7rem; color: #a9d8b8;"></i>{{ !is_null($kpiMinReserva) ? '$' . number_format($kpiMinReserva, 0) : '—' }}
                            </h6>
                            <div class="rsv-kpi-sub">por reserva</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100"
                         @if($kpiResenasCount > 0) title="Promedio de {{ $kpiResenasCount }} reseña(s) de huéspedes" @endif>
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Calificación</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-star-fill me-1" style="font-size: 0.7rem; color: #ecd3a0;"></i>{{ !is_null($kpiRating) ? number_format($kpiRating, 1) : '—' }}
                            </h6>
                            <div class="rsv-kpi-sub">
                                @if($kpiResenasCount > 0)
                                    {{ $kpiResenasCount }} {{ $kpiResenasCount == 1 ? 'reseña' : 'reseñas' }}
                                @else
                                    sin reseñas aún
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Total Reservas</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-journal-check me-1" style="font-size: 0.7rem; color: #e8b4a0;"></i>{{ $reservasCol->count() }}
                            </h6>
                            <div class="rsv-kpi-sub">histórico</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100"
                         title="Próximo check-in: {{ $kpiProximaEntrada?->format('d/m/Y') ?? '—' }}">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Próximas Entradas</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-door-open me-1" style="font-size: 0.7rem; color: #7fc8c0;"></i>{{ $kpiProximas }}
                            </h6>
                            <div class="rsv-kpi-sub">
                                {{ $kpiProximaEntrada ? 'próx. ' . $kpiProximaEntrada->format('d/m') : 'sin entradas' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100"
                         title="{{ $nochesOcupadas }} de {{ $diasDelMes }} noches de {{ now()->translatedFormat('F') }} ya reservadas">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Ocupación Mes</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-percent me-1" style="font-size: 0.7rem; color: #e0a9c0;"></i>{{ $kpiOcupacion }}%
                            </h6>
                            <div class="rsv-kpi-bar" style="margin-left: 12px; margin-right: 12px;">
                                <span style="width: {{ min($kpiOcupacion, 100) }}%;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card rsv-kpi-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="rsv-kpi-lbl">Galería</h6>
                            <h6 class="rsv-kpi-val">
                                <i class="bi bi-images me-1" style="font-size: 0.7rem; color: #cdb4e4;"></i>{{ $mediaCol->count() }}
                            </h6>
                            <div class="rsv-kpi-sub">{{ $kpiFotos }} fotos · {{ $kpiVideos }} videos</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- PESTAÑAS PRINCIPALES DEL INMUEBLE (MENÚ PADRE) --}}
            <div class="rsv-soft-card">
                <div class="card-header bg-white border-bottom p-0" style="border-color: #f1f5f9 !important;">
                    <ul class="nav nav-tabs nav-fill border-0 pt-1 px-1" id="adminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rsv-tab-parent" data-bs-toggle="tab" data-bs-target="#inmuebles" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-house-door me-1"></i> Propiedades <span class="tab-count">(#{{ $inmueble->id }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rsv-tab-parent" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas <span class="tab-count">({{ $reservasCol->count() }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar3 me-1"></i> Calendario / Bloqueos <span class="tab-count">({{ $bloqueosCol->count() }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#finanzas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas <span class="tab-count">({{ $fmtMoney($ingresosInmueble) }})</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-shield-check me-1"></i> Auditoría <span class="tab-count">({{ $auditoriaCount ?? '—' }})</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0 bg-white" style="border-radius: 0 0 8px 8px;">
                    <div class="tab-content" id="adminTabsContent">

                        {{-- TAB 1: CONFIGURACIÓN PROPIEDAD (5 SUBMENÚS HIJOS) --}}
                        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel">

                            <div class="rsv-subnav-bar">
                                <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                    <i class="bi bi-diagram-3-fill"></i>
                                    <span class="fw-bold">Ecosistema Inmobiliario:</span>
                                </div>
                                <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="inmuebleSingleSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-show" type="button" role="tab">
                                            <i class="bi bi-house-door me-1"></i> Inicio
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-sub-catalogo" type="button" role="tab">
                                            <i class="bi bi-grid-3x3 me-1"></i> Catálogo
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tarifa" type="button" role="tab">
                                            <i class="bi bi-cash-coin me-1"></i> Tarifas
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-galeria" type="button" role="tab">
                                            <i class="bi bi-images me-1"></i> Galería
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-tipo" type="button" role="tab">
                                            <i class="bi bi-tags me-1"></i> Tipos
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content p-3" id="inmuebleSingleSubContent">

                                <div class="tab-pane fade" id="single-sub-show" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.show', ['inmueble' => $inmueble])
                                </div>

                                <div class="tab-pane fade show active" id="single-sub-catalogo" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.catalogo', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="single-sub-tarifa" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.tarifa', ['inmueble' => $inmueble])
                                </div>

                                <div class="tab-pane fade" id="single-sub-galeria" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.galeria', ['inmueble' => $inmueble])
                                </div>

                                <div class="tab-pane fade" id="single-sub-tipo" role="tabpanel">
                                    @include('rsv.admin.partials.inmuebles.tipo', ['inmueble' => $inmueble])
                                </div>

                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════════════════════ --}}
                        {{-- TAB 2: RESERVAS · PADRE con subnav ACTIVO (6 hijos)                     --}}
                        {{-- · show (Inicio) y reservas (Reservas): DESCOMENTADOS y funcionando --}}
                        {{-- · Huéspedes/Estados/Orígenes/Historial: includes COMENTADOS             --}}
                        {{-- ══════════════════════════════════════════════════════════════════════ --}}
                        <div class="tab-pane fade" id="reservas" role="tabpanel">

                            <div class="rsv-subnav-bar">
                                <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                    <i class="bi bi-diagram-3-fill"></i>
                                    <span class="fw-bold">Ecosistema de Reservas:</span>
                                </div>
                                <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="singleReservasSubTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-inicio" type="button" role="tab">
                                            <i class="bi bi-house-door me-1"></i> Inicio
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#single-res-sub-reservas" type="button" role="tab">
                                            <i class="bi bi-journal-check me-1"></i> Reservas
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-huespedes" type="button" role="tab">
                                            <i class="bi bi-people me-1"></i> Huéspedes
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-status" type="button" role="tab">
                                            <i class="bi bi-flag me-1"></i> Estados
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-origenes" type="button" role="tab">
                                            <i class="bi bi-box-arrow-in-down me-1"></i> Orígenes
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-res-sub-historial" type="button" role="tab">
                                            <i class="bi bi-clock-history me-1"></i> Historial
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content p-3" id="singleReservasSubContent">

                                {{-- 1 · INICIO · partial show · DESCOMENTADO --}}
                                <div class="tab-pane fade" id="single-res-sub-inicio" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.show', ['inmueble' => $inmueble])
                                </div>

                                {{-- 2 · RESERVAS · motor v12 · DESCOMENTADO --}}
                                <div class="tab-pane fade show active" id="single-res-sub-reservas" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.reservas')
                                </div>

                                {{-- 3 · HUÉSPEDES · include COMENTADO hasta activar --}}
                                <div class="tab-pane fade" id="single-res-sub-huespedes" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.reserva_huespedt')
                                </div>

                                {{-- 4 · ESTADOS · include COMENTADO hasta activar --}}
                                <div class="tab-pane fade" id="single-res-sub-status" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.status')
                                </div>

                                {{-- 5 · ORÍGENES · include COMENTADO hasta activar --}}
                                <div class="tab-pane fade" id="single-res-sub-origenes" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.origen_reserva')
                                </div>

                                {{-- 6 · HISTORIAL · include COMENTADO hasta activar --}}
                                <div class="tab-pane fade" id="single-res-sub-historial" role="tabpanel">
                                    @include('rsv.admin.partials.reservas.historial_estado')
                                </div>

                            </div>

                        </div>

                        {{-- TAB 3: CALENDARIO / BLOQUEOS --}}
                        <div class="tab-pane fade p-3" id="calendario" role="tabpanel">
                            @include('rsv.admin.partials.tab-calendario')
                        </div>

                        {{-- TAB 4: FINANZAS --}}
                        <div class="tab-pane fade p-3" id="finanzas" role="tabpanel">
                            @include('rsv.admin.partials.tab-finanzas')
                        </div>

                        {{-- TAB 5: AUDITORÍA --}}
                        <div class="tab-pane fade p-3" id="auditoria" role="tabpanel">
                            <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">
                                <h6 class="fw-bold mb-3" style="font-size: 0.82rem; color: #334155;">
                                    <i class="bi bi-shield-check me-1 text-success"></i> Registro de Auditoría de la Propiedad
                                </h6>
                                @isset($auditoria)
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0" style="font-size: 0.75rem;">
                                            <thead class="table-light text-uppercase" style="font-size: 0.65rem; color: #64748b;">
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
                                                        <td colspan="5" class="text-center text-muted py-3">No hay registros de auditoría para este inmueble.</td>
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

                    </div>
                </div>
            </div>
        {{-- ======================================================================= --}}
        {{-- CONDICIONAL C: ESTADO VACÍO                                             --}}
        {{-- ======================================================================= --}}
        @else

            <div class="d-flex flex-column align-items-center justify-content-center text-center mt-5">
                <div class="bg-white mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border: 1px dashed #e9eef4;">
                    <i class="bi bi-buildings" style="font-size: 1.4rem; color: #d4dae2;"></i>
                </div>
                <h6 class="fw-bold" style="font-size: 0.85rem; color: #475569;">Esperando orden de trabajo</h6>
                <p class="mb-0" style="max-width: 380px; font-size: 0.7rem; color: #94a3b8;">
                    Utilice el selector superior para elegir un inmueble específico o seleccione <strong>"Ver todas las propiedades"</strong> para consultar el panel global.
                </p>
            </div>

        @endif
    </div>

    {{-- Persistencia de pestañas y subpestañas (con Reservas YA integrado) --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const MAPA_PADRES = {
                '#global-inmuebles':  '#inmuebles',   '#inmuebles':  '#global-inmuebles',
                '#global-reservas':   '#reservas',    '#reservas':   '#global-reservas',
                '#global-calendario': '#calendario',  '#calendario': '#global-calendario',
                '#global-finanzas':   '#finanzas',    '#finanzas':   '#global-finanzas',
                '#global-auditoria':  '#auditoria',   '#auditoria':  '#global-auditoria'
            };

            const MAPA_HIJOS = {
                '#sub-show':            '#single-sub-show',
                '#sub-catalogo':        '#single-sub-catalogo',
                '#sub-tarifa':          '#single-sub-tarifa',
                '#sub-galeria':         '#single-sub-galeria',
                '#sub-tipo':            '#single-sub-tipo',
                '#single-sub-show':     '#sub-show',
                '#single-sub-catalogo': '#sub-catalogo',
                '#single-sub-tarifa':   '#sub-tarifa',
                '#single-sub-galeria':  '#sub-galeria',
                '#single-sub-tipo':     '#sub-tipo',
                /* ═══ Ecosistema de Reservas · ACTIVADO ═══ */
                '#res-sub-inicio':            '#single-res-sub-inicio',
                '#res-sub-reservas':          '#single-res-sub-reservas',
                '#res-sub-huespedes':         '#single-res-sub-huespedes',
                '#res-sub-status':            '#single-res-sub-status',
                '#res-sub-origenes':          '#single-res-sub-origenes',
                '#res-sub-historial':         '#single-res-sub-historial',
                '#single-res-sub-inicio':     '#res-sub-inicio',
                '#single-res-sub-reservas':   '#res-sub-reservas',
                '#single-res-sub-huespedes':  '#res-sub-huespedes',
                '#single-res-sub-status':     '#res-sub-status',
                '#single-res-sub-origenes':   '#res-sub-origenes',
                '#single-res-sub-historial':  '#res-sub-historial'
            };

            const btnPadre = sel => document.querySelector(
                '#globalAdminTabs button[data-bs-target="' + sel + '"], ' +
                '#adminTabs button[data-bs-target="' + sel + '"]'
            );

            /* btnHijo extendido: incluye los tablists de Reservas (activados) */
            const btnHijo = sel => document.querySelector(
                '#inmueblesSubTabs button[data-bs-target="' + sel + '"], ' +
                '#inmuebleSingleSubTabs button[data-bs-target="' + sel + '"], ' +
                '#reservasSubTabs button[data-bs-target="' + sel + '"], ' +
                '#singleReservasSubTabs button[data-bs-target="' + sel + '"]'
            );

            // 1 · Restaurar PESTAÑA PADRE
            const savedTab = localStorage.getItem('rsvAdminTab');
            if (savedTab) {
                for (const sel of [savedTab, MAPA_PADRES[savedTab]].filter(Boolean)) {
                    const el = btnPadre(sel);
                    if (el) { new bootstrap.Tab(el).show(); break; }
                }
            }

            // 2 · Restaurar SUBPESTAÑA
            const savedSubTab = localStorage.getItem('rsvAdminSubTab');
            if (savedSubTab) {
                for (const sel of [savedSubTab, MAPA_HIJOS[savedSubTab]].filter(Boolean)) {
                    const el = btnHijo(sel);
                    if (el) { new bootstrap.Tab(el).show(); break; }
                }
            }

            // 3 · Guardar pestaña PADRE al cambiar
            document.querySelectorAll('#adminTabs button[data-bs-toggle="tab"], #globalAdminTabs button[data-bs-toggle="tab"]').forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('rsvAdminTab', event.target.getAttribute('data-bs-target'));
                });
            });

            // 4 · Guardar SUBPESTAÑA al cambiar (incluye los pills de Reservas)
            document.querySelectorAll('.rsv-sub-pills button[data-bs-toggle="pill"]').forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('rsvAdminSubTab', event.target.getAttribute('data-bs-target'));
                });
            });

            // 5 · Persistir estado antes de enviar formularios internos
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    const activeTab = document.querySelector('#adminTabs .nav-link.active, #globalAdminTabs .nav-link.active');
                    const activeSubTab = document.querySelector('.rsv-sub-pills .nav-link.active');
                    if (activeTab) localStorage.setItem('rsvAdminTab', activeTab.getAttribute('data-bs-target'));
                    if (activeSubTab) localStorage.setItem('rsvAdminSubTab', activeSubTab.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
    @endpush

</x-base-layout>
