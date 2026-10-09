<x-base-layout>
    @section('titlepage', 'Panel Maestro de Inmuebles - RSV')

    {{-- Notificaciones del Sistema --}}
    @include('rsv.components.alert')

    {{-- Estilos para el diseño unificado en bloque pastel (Padre e Hijos de 5 botones) --}}
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
        }
        .nav-tabs .nav-link:hover { color: #475569; }

        /* Pestaña PADRE ACTIVA ("Propiedades") fusionada en bloque pastel */
        .nav-tabs .nav-link.active[data-bs-target="#global-inmuebles"] {
            color: #2e5c43 !important;
            background-color: #e3f1e9 !important;
            border-color: #d1e7d9 #d1e7d9 #e3f1e9 #d1e7d9 !important;
            border-radius: 6px 6px 0 0 !important;
            font-weight: 700;
        }

        /* SUBMENÚ HIJO ACOPLADO (5 botones con ajuste flexible para pantallas) */
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

        /* Badges flotantes discretos */
        .rsv-portada-badge {
            font-size: 0.58rem; font-weight: 600; letter-spacing: 0.5px;
            color: #64748b;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(233,238,244,.9);
        }
        .rsv-heart-btn {
            width: 28px !important; height: 28px !important;
            background: rgba(255,255,255,.92) !important;
            border: 1px solid rgba(233,238,244,.9) !important;
            box-shadow: 0 1px 3px rgba(0,0,0,.06) !important;
        }
    </style>

    <div class="container-fluid py-4">

        {{-- ======================================================================= --}}
        {{-- 1. SELECTOR MAESTRO                                                       --}}
        {{-- ======================================================================= --}}
        <div class="rsv-soft-card mb-4">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">

                    {{-- Texto descriptivo --}}
                    <div class="col-12 col-lg-5 text-center text-lg-start">
                        <span class="rsv-kicker d-block mb-1">Panel Conector RSV</span>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #334155;">Gestión por Inmueble</h6>
                        <p class="mb-0" style="font-size: 0.7rem; color: #94a3b8;">Seleccione una propiedad para cargar su ecosistema o elija ver todas.</p>
                    </div>

                    {{-- Formulario Selector --}}
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

                                    {{-- OPCIÓN GLOBAL: VER TODOS --}}
                                    <option value="todos" {{ request('inmueble_id') === 'todos' ? 'selected' : '' }}>
                                        Ver todas las propiedades (Panel Global)
                                    </option>

                                    @isset($listaInmuebles)
                                        @foreach($listaInmuebles as $opcion)
                                            <option value="{{ $opcion->id }}" {{ request('inmueble_id', $inmueble->id ?? '') == $opcion->id ? 'selected' : '' }}>
                                                {{ $opcion->name }} ({{ $opcion->city }}) - {{ $opcion->active ? 'Activo' : 'Inactivo' }}
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
        {{-- CONDICIONAL A: VISTA GLOBAL ("VER TODOS" CON PESTAÑAS GLOBALES)           --}}
        {{-- ======================================================================= --}}
        @if(request('inmueble_id') === 'todos')

            <div class="rsv-soft-card overflow-hidden mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div>
                        <div class="rsv-kicker mb-1"><i class="bi bi-grid-3x3-gap me-1"></i> Panel Global</div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #334155;">Gestión Global del Sistema</h6>
                        <p class="mb-0" style="font-size: 0.7rem; color: #94a3b8;">Vista consolidada de propiedades, reservas, calendario, finanzas y auditoría.</p>
                    </div>
                    <span class="badge rounded-pill fw-semibold" style="font-size: 0.62rem; background-color: #f9f7fc; color: #8f7bb5; border: 1px solid #e9e2f2;">
                        Total Inmuebles: {{ isset($inmueblesGrid) ? $inmueblesGrid->total() : ($listaInmuebles->count() ?? 0) }}
                    </span>
                </div>

                {{-- PESTAÑAS GLOBALES PRINCIPALES (MENÚ PADRE) --}}
                <div class="card-header bg-white border-bottom p-0" style="border-color: #f1f5f9 !important;">
                    <ul class="nav nav-tabs nav-fill border-0 pt-1 px-1" id="globalAdminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#global-inmuebles" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-buildings me-1"></i> Propiedades
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-reservas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas Globales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-calendario" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar3 me-1"></i> Calendario / Bloqueos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-finanzas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-auditoria" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-shield-check me-1"></i> Auditoría
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- CONTENIDO DE LAS PESTAÑAS GLOBALES --}}
                <div class="tab-content" id="globalAdminTabsContent">

                    {{-- TAB 1: PROPIEDADES (CONTENEDOR DE LOS 5 SUBMENÚS HIJOS) --}}
                    <div class="tab-pane fade show active" id="global-inmuebles" role="tabpanel">

                        {{-- SUBMENÚ HIJO DE 5 BOTONES EN BLOQUE PASTEL --}}
                        <div class="rsv-subnav-bar">
                            <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                <i class="bi bi-diagram-3-fill"></i>
                                <span class="fw-bold">Ecosistema Inmobiliario:</span>
                            </div>
                            <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="inmueblesSubTabs" role="tablist">
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
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sub-show" type="button" role="tab">
                                        <i class="bi bi-pencil-square me-1"></i> Edición / Detalle
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {{-- Contenido de las 5 Sub-pestañas de Propiedades --}}
                        <div class="tab-content p-3" id="inmueblesSubContent">

                            {{-- 1. Catálogo General (partials/inmuebles/catalogo.blade.php) --}}
                            <div class="tab-pane fade show active" id="sub-catalogo" role="tabpanel">
                                <div class="container-fluid px-0">
                                    @include('rsv.admin.partials.inmuebles.catalogo')
                                </div>
                            </div>

                            {{-- 2. Tarifas y Temporadas (partials/inmuebles/tarifa.blade.php) --}}
                            <div class="tab-pane fade" id="sub-tarifa" role="tabpanel">
                                <div class="text-center py-4 text-muted small fst-italic">
                                    @include('rsv.admin.partials.inmuebles.tarifa')
                                </div>
                            </div>
 
                            {{-- 3. Galería Multimedia (partials/inmuebles/galeria.blade.php) --}}
                            <div class="tab-pane fade" id="sub-galeria" role="tabpanel">
                                <div class="text-center py-4 text-muted small fst-italic">
                                    @include('rsv.admin.partials.inmuebles.galeria')
                                </div>
                            </div>

                            {{-- 4. Tipos de Inmueble (partials/inmuebles/tipo.blade.php) --}}
                            <div class="tab-pane fade" id="sub-tipo" role="tabpanel">
                                {{-- @include('rsv.admin.partials.inmuebles.tipo') --}}
                                <div class="text-center py-4 text-muted small fst-italic">
                                    [Partial pendiente: rsv.admin.partials.inmuebles.tipo]
                                </div>
                            </div>

                            {{-- 5. Edición y Detalle (partials/inmuebles/show.blade.php) --}}
                            <div class="tab-pane fade" id="sub-show" role="tabpanel">
                                {{-- @include('rsv.admin.partials.inmuebles.show') --}}
                                <div class="text-center py-4 text-muted small fst-italic">
                                    [Partial pendiente: rsv.admin.partials.inmuebles.show]
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- TAB 2: RESERVAS GLOBALES --}}
                    <div class="tab-pane fade p-3" id="global-reservas" role="tabpanel">
                        @include('rsv.admin.partials.tab-reservas')
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

            {{-- MÉTRICAS RÁPIDAS DEL INMUEBLE --}}
            <div class="row g-2 mb-4">
                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.6rem; color: #b3bcc7; letter-spacing: 0.5px;">Capacidad</h6>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #475569;">
                                <i class="bi bi-people me-1" style="font-size: 0.7rem; color: #a9cfe8;"></i>{{ $inmueble->capacidad_maxima ?? 0 }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.6rem; color: #b3bcc7; letter-spacing: 0.5px;">Tarifa Actual</h6>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #5d8a70;">
                                <i class="bi bi-cash-coin me-1" style="font-size: 0.7rem;"></i>${{ number_format(optional($inmueble->latestTarifa)->precio_noche ?? 0, 0) }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.6rem; color: #b3bcc7; letter-spacing: 0.5px;">Total Reservas</h6>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #475569;">
                                <i class="bi bi-journal-check me-1" style="font-size: 0.7rem; color: #ecd3a0;"></i>{{ $inmueble->reservas->count() ?? 0 }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rsv-soft-card h-100">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.6rem; color: #b3bcc7; letter-spacing: 0.5px;">Galería S3</h6>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #475569;">
                                <i class="bi bi-images me-1" style="font-size: 0.7rem; color: #cdb4e4;"></i>{{ $inmueble->multimedia->count() ?? 0 }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑAS PRINCIPALES DEL INMUEBLE (MENÚ PADRE) --}}
            <div class="rsv-soft-card">
                <div class="card-header bg-white border-bottom p-0" style="border-color: #f1f5f9 !important;">
                    <ul class="nav nav-tabs nav-fill border-0 pt-1 px-1" id="adminTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#inmuebles" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-sliders me-1"></i> Configuración Propiedad
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar2-check me-1"></i> Reservas ({{ $inmueble->reservas->count() ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-calendar3 me-1"></i> Calendario / Bloqueos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#finanzas" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-wallet2 me-1"></i> Finanzas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab" style="border-radius: 0;">
                                <i class="bi bi-shield-check me-1"></i> Auditoría
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0 bg-white" style="border-radius: 0 0 8px 8px;">
                    <div class="tab-content" id="adminTabsContent">

                        {{-- TAB 1: CONFIGURACIÓN PROPIEDAD (CONTENEDOR DE LOS 5 SUBMENÚS HIJOS) --}}
                        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel">

                            {{-- SUBMENÚ HIJO DE 5 BOTONES EN BLOQUE PASTEL (ORDEN IDÉNTICO A "A") --}}
                            <div class="rsv-subnav-bar">
                                <div class="d-flex align-items-center gap-1" style="font-size: 0.68rem; color: #2e5c43;">
                                    <i class="bi bi-diagram-3-fill"></i>
                                    <span class="fw-bold">Ecosistema Inmobiliario:</span>
                                </div>
                                <ul class="nav nav-pills rsv-sub-pills gap-1 mb-0" id="inmuebleSingleSubTabs" role="tablist">
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
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#single-sub-show" type="button" role="tab">
                                            <i class="bi bi-pencil-square me-1"></i> Edición / Detalle
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            {{-- CONTENIDO DE LAS 5 SUB-PESTAÑAS DEL INMUEBLE ESPECÍFICO --}}
                            <div class="tab-content p-3" id="inmuebleSingleSubContent">

                                {{-- 1. Catálogo General Filtrado --}}
                                <div class="tab-pane fade show active" id="single-sub-catalogo" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.catalogo', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                                {{-- 2. Tarifas y Temporadas --}}
                                <div class="tab-pane fade" id="single-sub-tarifa" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.tarifa', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                                {{-- 3. Galería Multimedia --}}
                                <div class="tab-pane fade" id="single-sub-galeria" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.galeria', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                                {{-- 4. Tipos de Inmueble --}}
                                <div class="tab-pane fade" id="single-sub-tipo" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.tipo', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                                {{-- 5. Edición y Detalle --}}
                                <div class="tab-pane fade" id="single-sub-show" role="tabpanel">
                                    <div class="container-fluid px-0">
                                        @include('rsv.admin.partials.inmuebles.show', ['inmueble' => $inmueble])
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- DEMÁS PESTAÑAS PRINCIPALES DEL PADRE PARA EL INMUEBLE --}}
                        <div class="tab-pane fade p-3" id="reservas" role="tabpanel">
                            @include('rsv.admin.partials.tab-reservas')
                        </div>
                        <div class="tab-pane fade p-3" id="calendario" role="tabpanel">
                            @include('rsv.admin.partials.tab-calendario')
                        </div>
                        <div class="tab-pane fade p-3" id="finanzas" role="tabpanel">
                            @include('rsv.admin.partials.tab-finanzas')
                        </div>
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
        {{-- CONDICIONAL C: ESTADO VACÍO (ESPERANDO SELECCIÓN)                         --}}
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

    {{-- Script nativo para Bootstrap 5 y persistencia avanzada de pestañas y subpestañas --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Restaurar pestañas y subpestañas guardadas previamente en localStorage
            const savedTab = localStorage.getItem('rsvAdminTab');
            const savedSubTab = localStorage.getItem('rsvAdminSubTab');

            if (savedTab) {
                const triggerEl = document.querySelector('#adminTabs button[data-bs-target="' + savedTab + '"], #globalAdminTabs button[data-bs-target="' + savedTab + '"]');
                if (triggerEl) {
                    const tabInstance = new bootstrap.Tab(triggerEl);
                    tabInstance.show();
                }
            }

            if (savedSubTab) {
                const triggerSubEl = document.querySelector('#inmueblesSubTabs button[data-bs-target="' + savedSubTab + '"], #inmuebleSingleSubContent button[data-bs-target="' + savedSubTab + '"], #inmueblesSubContent button[data-bs-target="' + savedSubTab + '"]');
                if (triggerSubEl) {
                    const pillInstance = new bootstrap.Tab(triggerSubEl);
                    pillInstance.show();
                }
            }

            // 2. Escuchar cambios en las pestañas principales (Padre)
            document.querySelectorAll('#adminTabs button[data-bs-toggle="tab"], #globalAdminTabs button[data-bs-toggle="tab"]').forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', event => {
                    const target = event.target.getAttribute('data-bs-target');
                    localStorage.setItem('rsvAdminTab', target);
                });
            });

            // 3. Escuchar cambios en las subpestañas del ecosistema inmobiliario (Hijos)
            document.querySelectorAll('.rsv-sub-pills button[data-bs-toggle="pill"]').forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', event => {
                    const target = event.target.getAttribute('data-bs-target');
                    localStorage.setItem('rsvAdminSubTab', target);
                });
            });

            // 4. Asegurar que al enviar formularios internos (como los de galería o tarifas)
            // la página recargue manteniendo la pestaña actual limpiando o preservando estados si es necesario.
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    // Opcional: si el formulario apunta a otra ruta, guardamos el estado actual antes de salir
                    const activeTab = document.querySelector('.nav-tabs .nav-link.active');
                    const activeSubTab = document.querySelector('.rsv-sub-pills .nav-link.active');
                    if (activeTab) localStorage.setItem('rsvAdminTab', activeTab.getAttribute('data-bs-target'));
                    if (activeSubTab) localStorage.setItem('rsvAdminSubTab', activeSubTab.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
    @endpush

</x-base-layout>
