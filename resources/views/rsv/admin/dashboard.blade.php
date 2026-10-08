<x-base-layout>
    @section('titlepage', 'Panel Maestro de Inmuebles - RSV')

    {{-- Notificaciones del Sistema --}}
    @include('rsv.components.alert')

    {{-- Estilos mínimos del panel (solo pulido visual) --}}
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

        /* Pestañas suaves */
        .nav-tabs .nav-link {
            font-size: 0.72rem; font-weight: 600; color: #94a3b8;
            border-bottom: 2px solid transparent !important;
            padding: 0.5rem 0.9rem;
        }
        .nav-tabs .nav-link:hover { color: #475569; }
        .nav-tabs .nav-link.active {
            color: #3d7a5c !important;
            border-bottom-color: #b9d4c5 !important;
            background-color: transparent !important;
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
        {{-- 1. SELECTOR MAESTRO (EL REY DE LA VISTA)                               --}}
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
        {{-- CONDICIONAL A: ECOSISTEMA DE UN INMUEBLE ESPECÍFICO                     --}}
        {{-- ======================================================================= --}}
        @if(isset($inmueble) && $inmueble)

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

            {{-- PESTAÑAS DE CONEXIÓN --}}
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
                    </ul>
                </div>

                <div class="card-body p-3 bg-white" style="border-radius: 0 0 8px 8px;">
                    <div class="tab-content" id="adminTabsContent">
                        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel">
                            @include('rsv.admin.partials.tab-inmuebles')
                        </div>
                        <div class="tab-pane fade" id="reservas" role="tabpanel">
                            @include('rsv.admin.partials.tab-reservas')
                        </div>
                        <div class="tab-pane fade" id="calendario" role="tabpanel">
                            @include('rsv.admin.partials.tab-calendario')
                        </div>
                        <div class="tab-pane fade" id="finanzas" role="tabpanel">
                            @include('rsv.admin.partials.tab-finanzas')
                        </div>
                    </div>
                </div>
            </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL B: VISTA GLOBAL ("VER TODOS" CON TARJETAS ESTILO CLIENTE)   --}}
        {{-- ======================================================================= --}}
        @elseif(request('inmueble_id') === 'todos')

            <div class="rsv-soft-card p-3 mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div>
                        <div class="rsv-kicker mb-1"><i class="bi bi-grid-3x3-gap me-1"></i> Panel Global</div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #334155;">Todas las Propiedades</h6>
                        <p class="mb-0" style="font-size: 0.7rem; color: #94a3b8;">Vista previa interactiva tal como las visualizan los clientes en el catálogo.</p>
                    </div>
                    <span class="badge rounded-pill fw-semibold" style="font-size: 0.62rem; background-color: #f9f7fc; color: #8f7bb5; border: 1px solid #e9e2f2;">
                        Total Registradas: {{ $listaInmuebles->count() }}
                    </span>
                </div>

                {{-- Cuadrícula de Tarjetas Estilo Cliente --}}
                <div class="row g-3">
                    @forelse($listaInmuebles as $prop)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="rsv-soft-card overflow-hidden d-flex flex-column h-100 position-relative">

                                {{-- SECCIÓN 1: CARRUSEL DE FOTOS --}}
                                <div id="carouselInmuebleGlobal{{ $prop->id }}" class="carousel slide position-relative flex-shrink-0" data-bs-ride="carousel" style="height: 180px; overflow: hidden; background: #f4f6f8;">

                                    {{-- CORAZÓN FLOTANTE SUPERIOR DERECHO (Favorito) --}}
                                    <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 p-2 rsv-heart-btn d-flex align-items-center justify-content-center"
                                            title="Favorito">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d98c8c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                    </button>

                                    @php
                                        // Asegurar que cargue la multimedia si no viene cargada en la colección
                                        $galeriaGlobal = $prop->relationLoaded('multimedia') ? $prop->multimedia : $prop->multimedia()->get();
                                    @endphp

                                    <div class="carousel-inner w-100 h-100">
                                        @forelse($galeriaGlobal as $index => $media)
                                            <div class="carousel-item w-100 h-100 {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $media->url_archivo }}" class="d-block w-100 h-100" style="object-fit: cover; object-position: center;" alt="Foto inmueble">
                                                @if($media->es_portada)
                                                    <span class="badge rsv-portada-badge position-absolute bottom-0 start-0 m-2 px-2 py-1" style="z-index: 5;">Portada</span>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="carousel-item active w-100 h-100 d-flex align-items-center justify-content-center" style="background: #fafbfd;">
                                                <span class="fst-italic" style="font-size: 0.68rem; color: #d4dae2;">Sin fotos en galería</span>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if($galeriaGlobal->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="prev" style="width: 26px; opacity: .3;">
                                            <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 14px; height: 14px; filter: invert(.45);"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="next" style="width: 26px; opacity: .3;">
                                            <span class="carousel-control-next-icon" aria-hidden="true" style="width: 14px; height: 14px; filter: invert(.45);"></span>
                                        </button>
                                    @endif
                                </div>

                                {{-- SECCIÓN 2: CUERPO DE LA TARJETA --}}
                                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold mb-0 text-truncate pe-2" style="font-size: 0.78rem; color: #475569;" title="{{ $prop->name }}">{{ $prop->name }}</h6>
                                        @if($prop->active)
                                            <span class="badge px-2 py-1 fw-semibold" style="font-size: 0.6rem; background-color: #f0f7f2; color: #5d8a70; border: 1px solid #d8e9de;">Activo</span>
                                        @else
                                            <span class="badge px-2 py-1 fw-semibold" style="font-size: 0.6rem; background-color: #fafbfd; color: #9aa5b1; border: 1px solid #eef2f6;">Inactivo</span>
                                        @endif
                                    </div>

                                    {{-- Calificación --}}
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="d-flex align-items-center gap-1">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="#ecd3a0" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                @endfor
                                            </div>
                                            <span class="fw-semibold" style="font-size: 0.62rem; color: #94a3b8;">5.0</span>
                                        </div>
                                        <span style="font-size: 0.62rem; color: #b3bcc7;">{{ $prop->reservas_count ?? 0 }} reservas</span>
                                    </div>

                                    {{-- Ubicación --}}
                                    <p class="mb-3 text-truncate" style="font-size: 0.68rem; color: #94a3b8;">
                                        <i class="bi bi-geo-alt" style="color: #c3ccd6;"></i> {{ $prop->city ?? 'Sin ciudad' }} — {{ $prop->ubicacion ?? 'Sin dirección' }}
                                    </p>

                                    {{-- Valores Base --}}
                                    <div class="rounded-2 p-2 mb-3" style="background: #fafbfd; border: 1px solid #f1f5f9;">
                                        <div class="row text-center g-0">
                                            <div class="col-6 border-end" style="border-color: #f1f5f9 !important;">
                                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.55rem; color: #b3bcc7; letter-spacing: 0.5px;">CAPACIDAD</span>
                                                <span class="fw-semibold" style="font-size: 0.72rem; color: #475569;">{{ $prop->capacidad_maxima ?? 'N/A' }} pers.</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.55rem; color: #b3bcc7; letter-spacing: 0.5px;">PRECIO BASE</span>
                                                <span class="fw-semibold" style="font-size: 0.72rem; color: #5d8a70;">${{ number_format(optional($prop->latestTarifa)->precio_noche ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Botón para saltar a la gestión de este inmueble --}}
                                    <div class="mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                        <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                           class="btn btn-sm w-100 rounded-2 py-1.5 rsv-btn-soft d-flex align-items-center justify-content-center gap-1">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Gestionar Ecosistema
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4" style="color: #b3bcc7; font-size: 0.72rem;">
                            No hay inmuebles registrados en el sistema.
                        </div>
                    @endforelse
                </div>
            </div>

        {{-- ======================================================================= --}}
        {{-- CONDICIONAL C: ESTADO VACÍO (ESPERANDO SELECCIÓN)                       --}}
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

    {{-- Script nativo para Bootstrap 5 y persistencia de pestañas --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeTabHash = localStorage.getItem('rsvAdminTab');
            if (activeTabHash) {
                const triggerEl = document.querySelector('#adminTabs button[data-bs-target="' + activeTabHash + '"]');
                if (triggerEl) {
                    new bootstrap.Tab(triggerEl).show();
                }
            }

            document.querySelectorAll('#adminTabs button[data-bs-toggle="tab"]').forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('rsvAdminTab', event.target.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
    @endpush

</x-base-layout>
