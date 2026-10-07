{{--
    ========================================================================
    VISTA PRINCIPAL: Detalle y Gestión de Inmueble (Panel de Administración)
    RUTA SUGERIDA: resources/views/rsv/admin/partials/inmuebles/show.blade.php
    CONTROLADOR ASOCIADO: CatalogoInmuebleController@show

    ARQUITECTURA DE LA VISTA (3 Engranajes con Acordeón):
    1. Núcleo Base: Datos estructurales del modelo CatalogoInmueble.
    2. Motor Comercial: Gestión de la relación HasMany (TarifaTemporada).
    3. Vitrina Visual: Gestión de la relación HasMany (InmuebleMultimedia).

    DISEÑO: Interfaz corporativa amigable (Colores pasteles accesibles y UI limpia).
    ========================================================================
--}}

<div class="container-fluid px-0">

    {{-- ================================================================== --}}
    {{-- SECCIÓN: ENCABEZADO GENERAL (Experiencia Premium Dashboard)        --}}
    {{-- ================================================================== --}}
    <div class="card border-0 mb-4 position-relative overflow-hidden bg-white" style="border-radius: 1.25rem; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.03);">

        {{-- Barra decorativa superior: Representa la unión de los 3 Módulos (Azul, Verde, Morado) --}}
        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(90deg, #174EA6 0%, #3F6212 50%, #681DA8 100%);"></div>

        {{-- Marca de agua decorativa de fondo (Icono de Edificio) --}}
        <div class="position-absolute top-50 end-0 translate-middle-y opacity-25 pe-none d-none d-md-block" style="right: -5%; transform: scale(1.6); color: #F1F5F9;">
            <svg width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path><path d="M9 7h.01"></path><path d="M9 11h.01"></path><path d="M15 7h.01"></path><path d="M15 11h.01"></path>
            </svg>
        </div>

        <div class="card-body p-4 p-md-5 position-relative z-1 d-flex justify-content-between align-items-center flex-wrap gap-4">

            {{-- Bloque de Identidad del Inmueble --}}
            <div class="d-flex flex-column align-items-start">

                {{-- Badge estilo Pill con pulso de actividad --}}
                <span class="badge rounded-pill d-inline-flex align-items-center gap-2 mb-3 px-3 py-2 shadow-sm" style="background-color: #F8FAFC; color: #334155; border: 1px solid #E2E8F0; font-size: 0.75rem; letter-spacing: 0.5px;">
                    <span class="rounded-circle spinner-grow spinner-grow-sm" style="width: 8px; height: 8px; background-color: #3B82F6; animation-duration: 2s;" role="status"></span>
                    <span class="font-monospace fw-semibold">EXPEDIENTE VIVO #{{ $inmueble->id }}</span>
                </span>

                {{-- Título Principal --}}
                <h2 class="fw-bolder mb-2 text-dark" style="letter-spacing: -0.5px; font-size: 1.85rem;">
                    {{ $inmueble->name }}
                </h2>

                {{-- Descripción con Icono de Engranaje --}}
                <p class="mb-0 d-flex align-items-center gap-2 text-muted" style="font-size: 0.95rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #64748B;">
                        <circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Sistema centralizado: Administra la identidad, las reglas comerciales y la galería.
                </p>
            </div>

            {{-- Botón de Acción Principal --}}
            <div class="d-flex ms-auto mt-3 mt-md-0">
                <a href="{{ route('rsv.admin.dashboard') }}"
                class="btn d-inline-flex align-items-center gap-2 px-4 py-2 fw-medium rounded-pill border-0 shadow-sm text-decoration-none"
                style="background-color: #F8FAFC; color: #475569; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);"
                onmouseover="this.style.backgroundColor='#F1F5F9'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';"
                onmouseout="this.style.backgroundColor='#F8FAFC'; this.style.transform='none'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,.075)';">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Volver al Panel General
                </a>
            </div>

        </div>
    </div>

    {{-- Alertas de Éxito y Error (Flash Messages) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 px-3 small mb-4 shadow-sm border-0" style="background-color: #CEEAD6; color: #0D652D;" role="alert">
            <span class="fw-medium">{{ session('success') }}</span>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2 px-3 small mb-4 shadow-sm border-0" style="background-color: #FAD2CF; color: #A50E0E;" role="alert">
            <span class="fw-medium">{{ session('error') }}</span>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- ================================================================== --}}
    {{-- ENGRANAJE 1: NÚCLEO DEL INMUEBLE (Identidad y Características)       --}}
    {{-- ================================================================== --}}
    <div class="card border-0 mb-4 bg-white overflow-hidden" style="border-radius: 1.25rem; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.02); border-top: 4px solid #174EA6 !important;">

        <div class="card-header bg-transparent border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div class="d-flex align-items-center gap-3 flex-grow-1" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseNucleoInmueble" aria-expanded="false" aria-controls="collapseNucleoInmueble">
                <div class="d-flex justify-content-center align-items-center rounded-3 shadow-sm" style="width: 45px; height: 45px; background: linear-gradient(135deg, #E8F0FE 0%, #D2E3FC 100%); color: #174EA6;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-uppercase font-monospace fw-bold" style="color: #669DF6; font-size: 0.65rem; letter-spacing: 1.5px;">Módulo 1 de 3 (Haga clic para expandir / colapsar)</span>
                    <h5 class="fw-bolder mb-0 text-dark d-flex align-items-center gap-2" style="letter-spacing: -0.3px;">
                        Identidad y Especificaciones
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </h5>
                </div>
            </div>

            <button type="button"
                    class="btn d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 fw-medium border shadow-sm"
                    style="background-color: #ffffff; color: #174EA6; border-color: #D2E3FC !important; transition: all 0.2s ease;"
                    onmouseover="this.style.backgroundColor='#F8FAFC'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.backgroundColor='#ffffff'; this.style.transform='none';"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditarInmueblePrincipal">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
                Editar Propiedad
            </button>
        </div>

        {{-- Contenedor Colapsado estándar Bootstrap --}}
        <div id="collapseNucleoInmueble" class="collapse">
            <div class="card-body px-4 pb-4 pt-0">

                <div class="d-flex align-items-start gap-3 p-3 mb-4 rounded-3" style="background-color: #F4F8FE; border-left: 4px solid #8AB4F8;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#174EA6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mt-1 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <p class="mb-0 small" style="color: #475569; line-height: 1.6;">
                        <strong style="color: #174EA6;">¿Para qué sirve este módulo?</strong> Define los datos estructurales fijos de la propiedad. Estos valores son el <strong>"ADN"</strong> del inmueble y determinarán cómo el motor de búsquedas filtra este espacio para los huéspedes.
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 h-100 d-flex flex-column" style="background-color: #ffffff; border: 1px solid #E2E8F0; box-shadow: 0 2px 10px rgba(0,0,0,0.01);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Capacidad Límite</span>
                            </div>
                            <span class="fw-bolder mt-auto" style="color: #1e293b; font-size: 1.15rem;">
                                {{ $inmueble->capacidad_maxima ?? '0' }} <span class="fw-medium text-muted" style="font-size: 0.85rem;">personas</span>
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 h-100 d-flex flex-column" style="background-color: #ffffff; border: 1px solid #E2E8F0; box-shadow: 0 2px 10px rgba(0,0,0,0.01);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Punto Geográfico</span>
                            </div>
                            <div class="mt-auto">
                                <span class="d-block fw-bolder text-truncate" style="color: #1e293b; font-size: 1.05rem;" title="{{ $inmueble->city ?? 'Ciudad no especificada' }}">
                                    {{ $inmueble->city ?? 'N/A' }}
                                </span>
                                <span class="d-block text-muted text-truncate small" title="{{ $inmueble->ubicacion ?? 'Dirección no especificada' }}">
                                    {{ $inmueble->ubicacion ?? 'Sin dirección registrada' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background-color: #ffffff; border: 1px solid #E2E8F0; box-shadow: 0 2px 10px rgba(0,0,0,0.01);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Visibilidad Pública</span>
                            </div>
                            <div class="mt-auto">
                                @if($inmueble->active)
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; width: fit-content;">
                                        <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px; background-color: #10B981;" role="status"></span>
                                        <span class="fw-bold" style="font-size: 0.85rem;">En vivo (Catálogo Público)</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; color: #64748B; width: fit-content;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        <span class="fw-bold" style="font-size: 0.85rem;">Oculto (Borrador)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ================================================================== --}}
    {{-- ENGRANAJE 2: MOTOR FINANCIERO (Tarifas por Temporada con Días Máx.) --}}
    {{-- ================================================================== --}}
    <div class="card border-0 mb-4 bg-white overflow-hidden" style="border-radius: 1.25rem; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.02); border-top: 4px solid #4D7C0F !important;">

        <div class="card-header bg-transparent border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div class="d-flex align-items-center gap-3 flex-grow-1" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseMotorComercial" aria-expanded="false" aria-controls="collapseMotorComercial">
                <div class="d-flex justify-content-center align-items-center rounded-3 shadow-sm" style="width: 45px; height: 45px; background: linear-gradient(135deg, #ECFDF5 0%, #D1E5A5 100%); color: #3F6212;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-uppercase font-monospace fw-bold" style="color: #65A30D; font-size: 0.65rem; letter-spacing: 1.5px;">Módulo 2 de 3 (Haga clic para expandir / colapsar)</span>
                    <h5 class="fw-bolder mb-0 text-dark d-flex align-items-center gap-2" style="letter-spacing: -0.3px;">
                        Motor Comercial y Tarifas
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </h5>
                </div>
            </div>

            <button type="button"
                    class="btn d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 fw-medium border shadow-sm"
                    style="background-color: #ffffff; color: #3F6212; border-color: #D1E5A5 !important; transition: all 0.2s ease;"
                    onmouseover="this.style.backgroundColor='#F7FEE7'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.backgroundColor='#ffffff'; this.style.transform='none';"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddTarifa">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Tarifa
            </button>
        </div>

        {{-- Contenedor Colapsado estándar Bootstrap --}}
        <div id="collapseMotorComercial" class="collapse">
            <div class="card-body px-4 pb-4 pt-0">
                <div class="d-flex align-items-start gap-3 p-3 mb-4 rounded-3" style="background-color: #F7FEE7; border-left: 4px solid #BEF264;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3F6212" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mt-1 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <p class="mb-0 small" style="color: #3F6212; line-height: 1.6;">
                        <strong>¿Para qué sirve este módulo?</strong> Programa los precios dinámicos del inmueble. Permite definir rangos de fechas (temporadas, festivos) con tarifas por noche diferenciadas, montos mínimos exigidos y el límite de días máximos para la reserva.
                    </p>
                </div>

                <div class="table-responsive border rounded-4 bg-white mb-0 overflow-hidden" style="border-color: #E2E8F0 !important; box-shadow: 0 4px 15px rgba(0,0,0,0.01);">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background-color: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <tr>
                                <th class="py-3 px-4 text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Temporada</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Rango de Fechas</th>
                                <th class="py-3 px-3 text-end text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Precio Noche</th>
                                <th class="py-3 px-3 text-end text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Fin Semana</th>
                                <th class="py-3 px-3 text-end text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Mín. Reserva</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Días Máx.</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Estado</th>
                                <th class="py-3 px-4 text-center text-uppercase fw-bold text-muted border-0" style="font-size: 0.65rem; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inmueble->tarifasTemporadas ?? [] as $tarifa)
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td class="fw-semibold py-3 px-4" style="color: #1e293b;">{{ $tarifa->nombre_temporada }}</td>
                                    <td class="text-center py-3">
                                        <span class="badge rounded-pill bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                                            {{ optional($tarifa->fecha_inicio)->format('d/M/Y') }} - {{ optional($tarifa->fecha_fin)->format('d/M/Y') }}
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace py-3 fw-bolder" style="color: #3F6212;">${{ number_format($tarifa->precio_noche, 2) }}</td>
                                    <td class="text-end font-monospace py-3 fw-semibold" style="color: #4D7C0F;">${{ number_format($tarifa->precio_fin_semana, 2) }}</td>
                                    <td class="text-end font-monospace py-3 text-muted">${{ number_format($tarifa->precio_minimo_reserva, 2) }}</td>
                                    <td class="text-center font-monospace py-3 text-muted">{{ $tarifa->dias_maximos ?? '—' }}</td>
                                    <td class="text-center py-3">
                                        @if($tarifa->active)
                                            <span class="badge rounded-pill fw-medium px-2 py-1" style="background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">Activa</span>
                                        @else
                                            <span class="badge rounded-pill fw-medium px-2 py-1" style="background-color: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0;">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <button type="button"
                                            class="btn btn-light border shadow-sm btn-sm px-3 py-1 rounded-pill btn-editar-tarifa fw-medium"
                                            style="font-size: 0.75rem; color: #475569; transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#F1F5F9'; this.style.color='#0f172a';"
                                            onmouseout="this.style.backgroundColor=''; this.style.color='#475569';"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarTarifa"
                                            data-url-update="{{ route('rsv.tarifas-temporadas.update', $tarifa->id) }}"
                                            data-nombre-temporada="{{ $tarifa->nombre_temporada }}"
                                            data-fecha-inicio="{{ optional($tarifa->fecha_inicio)->format('Y-m-d') }}"
                                            data-fecha-fin="{{ optional($tarifa->fecha_fin)->format('Y-m-d') }}"
                                            data-precio-noche="{{ $tarifa->precio_noche }}"
                                            data-precio-fin-semana="{{ $tarifa->precio_fin_semana }}"
                                            data-precio-minimo-reserva="{{ $tarifa->precio_minimo_reserva }}"
                                            data-dias-maximos="{{ $tarifa->dias_maximos }}"
                                            data-active="{{ $tarifa->active ? 1 : 0 }}">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5" style="background-color: #F8FAFC;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <p class="mb-0 fw-medium">No hay tarifas registradas.</p>
                                        <span class="small">Se requiere configurar al menos una tarifa para operar.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- ================================================================== --}}
    {{-- ENGRANAJE 3: VITRINA VISUAL (Multimedia, Imágenes y Videos)        --}}
    {{-- ================================================================== --}}
    <div class="card border-0 mb-4 bg-white overflow-hidden" style="border-radius: 1.25rem; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.02); border-top: 4px solid #7B1FA2 !important;">

        <div class="card-header bg-transparent border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div class="d-flex align-items-center gap-3 flex-grow-1" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseVitrinaVisual" aria-expanded="false" aria-controls="collapseVitrinaVisual">
                <div class="d-flex justify-content-center align-items-center rounded-3 shadow-sm" style="width: 45px; height: 45px; background: linear-gradient(135deg, #F5F3FF 0%, #E9D2FC 100%); color: #7B1FA2;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
                <div>
                    <span class="text-uppercase font-monospace fw-bold" style="color: #A855F7; font-size: 0.65rem; letter-spacing: 1.5px;">Módulo 3 de 3 (Haga clic para expandir / colapsar)</span>
                    <h5 class="fw-bolder mb-0 text-dark d-flex align-items-center gap-2" style="letter-spacing: -0.3px;">
                        Vitrina Visual y Galería
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </h5>
                </div>
            </div>

            <button type="button"
                    class="btn d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 fw-medium border shadow-sm"
                    style="background-color: #ffffff; color: #7B1FA2; border-color: #E9D2FC !important; transition: all 0.2s ease;"
                    onmouseover="this.style.backgroundColor='#FAF5FF'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.backgroundColor='#ffffff'; this.style.transform='none';"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddMultimedia">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Agregar Recurso
            </button>
        </div>

        {{-- Contenedor Colapsado estándar Bootstrap --}}
        <div id="collapseVitrinaVisual" class="collapse">
            <div class="card-body px-4 pb-4 pt-0">
                <div class="d-flex align-items-start gap-3 p-3 mb-4 rounded-3" style="background-color: #FAF5FF; border-left: 4px solid #D8B4FE;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7B1FA2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mt-1 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <p class="mb-0 small" style="color: #581C87; line-height: 1.6;">
                        <strong>¿Para qué sirve este módulo?</strong> Alimenta la interfaz del cliente final con fotografías y videos promocionales almacenados de forma segura en AWS S3. Puedes definir el orden de aparición y marcar una imagen estrella como <strong>"Portada Principal"</strong>.
                    </p>
                </div>

                <div class="table-responsive border rounded-4 bg-white mb-0 overflow-hidden" style="border-color: #E2E8F0 !important; box-shadow: 0 4px 15px rgba(0,0,0,0.01);">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background-color: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <tr>
                                <th class="py-3 px-4 text-center text-uppercase fw-bold text-muted border-0" style="width: 50px; font-size: 0.65rem; letter-spacing: 0.5px;">ID</th>
                                <th class="py-3 px-3 text-uppercase fw-bold text-muted border-0" style="width: 120px; font-size: 0.65rem; letter-spacing: 0.5px;">Tipo</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="width: 110px; font-size: 0.65rem; letter-spacing: 0.5px;">Vista Previa</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="width: 90px; font-size: 0.65rem; letter-spacing: 0.5px;">Orden</th>
                                <th class="py-3 px-3 text-center text-uppercase fw-bold text-muted border-0" style="width: 100px; font-size: 0.65rem; letter-spacing: 0.5px;">Portada</th>
                                <th class="py-3 px-4 text-center text-uppercase fw-bold text-muted border-0" style="width: 100px; font-size: 0.65rem; letter-spacing: 0.5px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inmueble->multimedia ?? [] as $media)
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td class="text-center text-muted font-monospace py-3 px-4">{{ $media->id }}</td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill fw-medium px-2 py-1" style="background-color: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;">
                                            @if($media->tipo_multimedia == 'imagen')
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            @else
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                            @endif
                                            {{ ucfirst($media->tipo_multimedia) }}
                                        </span>
                                    </td>
                                    <td class="text-center py-3">
                                        <a href="{{ $media->url_archivo }}" target="_blank" class="d-inline-block position-relative overflow-hidden rounded-3 border shadow-sm" style="width: 45px; height: 45px; background-color: #F8FAFC; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)';" onmouseout="this.style.transform='none';" title="Ver archivo completo en S3">
                                            @if($media->tipo_multimedia == 'imagen')
                                                <img src="{{ $media->url_archivo }}" alt="Miniatura" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex flex-column justify-content-center align-items-center text-purple" style="color: #7B1FA2; background-color: #FAF5FF;">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                                    </svg>
                                                </div>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="text-center py-3 font-monospace fw-semibold" style="color: #64748B;">{{ $media->orden }}</td>
                                    <td class="text-center py-3">
                                        @if($media->es_portada)
                                            <span class="badge rounded-pill fw-medium px-2 py-1 shadow-sm" style="background-color: #FDF4FF; color: #A21CAF; border: 1px solid #F5D0FE;">Sí (Principal)</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <button type="button"
                                            class="btn btn-light border shadow-sm btn-sm px-3 py-1 rounded-pill btn-editar-multimedia fw-medium"
                                            style="font-size: 0.75rem; color: #475569; transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#F1F5F9'; this.style.color='#0f172a';"
                                            onmouseout="this.style.backgroundColor=''; this.style.color='#475569';"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarMultimedia"
                                            data-url-update="{{ route('rsv.inmueble-multimedia.update', $media->id) }}"
                                            data-tipo-multimedia="{{ $media->tipo_multimedia }}"
                                            data-orden="{{ $media->orden }}"
                                            data-es-portada="{{ $media->es_portada ? 1 : 0 }}">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5" style="background-color: #F8FAFC;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <p class="mb-0 fw-medium">Vitrina vacía.</p>
                                        <span class="small">Sube fotos o videos para atraer a tus clientes.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- ================================================================== --}}
{{-- ZONA DE MODALES DE GESTIÓN (EDICIÓN Y CREACIÓN)                    --}}
{{-- ================================================================== --}}

{{-- MODAL 1: EDITAR INMUEBLE PRINCIPAL --}}
<div class="modal fade" id="modalEditarInmueblePrincipal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3" style="background-color: #E8F0FE;">
                <h6 class="modal-title fw-bold" style="color: #174EA6;">Editar Núcleo del Inmueble</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Nombre del Inmueble</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $inmueble->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Capacidad Máxima (Personas)</label>
                            <input type="number" class="form-control" name="capacidad_maxima" value="{{ old('capacidad_maxima', $inmueble->capacidad_maxima) }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Tipo de Inmueble ID</label>
                            <input type="number" class="form-control" name="tipo_inmueble_id" value="{{ old('tipo_inmueble_id', $inmueble->tipo_inmueble_id) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Ciudad</label>
                            <input type="text" class="form-control" name="city" value="{{ old('city', $inmueble->city) }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Ubicación / Dirección detallada</label>
                            <input type="text" class="form-control" name="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}">
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="editActiveInmueble" {{ old('active', $inmueble->active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" style="color: #334155;" for="editActiveInmueble">Activo (Visible en catálogo general)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn px-4 rounded-3 fw-medium text-white" style="background-color: #174EA6;">Actualizar Inmueble</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL 2A: AGREGAR TARIFA POR TEMPORADA                             --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalAddTarifa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3" style="background-color: #E2F0CB;">
                <h6 class="modal-title fw-bold" style="color: #3F6212;">Nueva Tarifa al Motor Comercial</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Nombre de la Temporada</label>
                        <input type="text" class="form-control" name="nombre_temporada" placeholder="Ej. Temporada Alta Diciembre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Fecha de Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>

                    {{-- Grilla de 4 columnas para Precios y Límites --}}
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Precio Noche ($)</label>
                            <input type="number" step="0.01" class="form-control" name="precio_noche" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Precio Fin Sem. ($)</label>
                            <input type="number" step="0.01" class="form-control" name="precio_fin_semana" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Mín. Reserva ($)</label>
                            <input type="number" step="0.01" class="form-control" name="precio_minimo_reserva" placeholder="0.00" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Días Máximos</label>
                            <input type="number" class="form-control" name="dias_maximos" placeholder="Ej. 30" min="1">
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="tarifaActiveCheck" checked>
                            <label class="form-check-label fw-semibold" style="color: #334155;" for="tarifaActiveCheck">Tarifa activa comercialmente</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn px-4 rounded-3 fw-medium text-white" style="background-color: #4D7C0F;">Guardar Tarifa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL 2B: EDITAR TARIFA POR TEMPORADA                              --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalEditarTarifa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3" style="background-color: #E2F0CB;">
                <h6 class="modal-title fw-bold" style="color: #3F6212;">Editar Tarifa Comercial</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form id="formEditarTarifa" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Nombre de la Temporada</label>
                        <input type="text" class="form-control" id="edit_tarifa_nombre" name="nombre_temporada" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="edit_tarifa_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Fecha de Fin</label>
                            <input type="date" class="form-control" id="edit_tarifa_fin" name="fecha_fin" required>
                        </div>
                    </div>

                    {{-- Grilla de 4 columnas para Precios y Límites en Edición --}}
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Precio Noche ($)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_noche" name="precio_noche" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Precio Fin Sem. ($)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_fin_semana" name="precio_fin_semana" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Mín. Reserva ($)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_minimo" name="precio_minimo_reserva" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-muted small fw-semibold">Días Máximos</label>
                            <input type="number" class="form-control" id="edit_tarifa_dias_maximos" name="dias_maximos" min="1">
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tarifa_active">
                            <label class="form-check-label fw-semibold" style="color: #334155;" for="edit_tarifa_active">Tarifa activa comercialmente</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn px-4 rounded-3 fw-medium text-white" style="background-color: #4D7C0F;">Actualizar Tarifa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL 3A: AGREGAR MULTIMEDIA (S3 + Drag & Drop + Ctrl + V)         --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalAddMultimedia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3" style="background-color: #F3E8FD;">
                <h6 class="modal-title fw-bold" style="color: #681DA8;">Agregar Recurso a la Vitrina</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    {{-- Zona de Drag & Drop y Pegado (Ctrl + V) --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Archivo (Imagen o Video)</label>
                        <div class="upload-drop-zone border-2 border-dashed rounded-4 p-4 text-center position-relative"
                             style="border-color: #D8B4FE; background-color: #FAF5FF; cursor: pointer; transition: all 0.2s;"
                             tabindex="0">
                            {{-- Input file oculto pero interactivo --}}
                            <input type="file" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0 file-input-target"
                                   name="url_archivo" accept="image/jpeg,image/png,image/jpg,image/webp,video/mp4,video/mov,video/avi" required style="cursor: pointer;">
                            <div class="dz-message pointer-events-none">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7B1FA2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <p class="mb-1 fw-bold text-dark file-name-display" style="font-size: 0.9rem;">
                                    Arrastra tu archivo aquí o <span style="color: #681DA8;">haz clic para buscar</span>
                                </p>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">
                                    💡 Tip: Puedes presionar <kbd class="bg-white border px-1 rounded shadow-sm">Ctrl + V</kbd> para pegar una imagen directamente.
                                </span>
                            </div>
                        </div>
                        <div class="form-text text-muted mt-1" style="font-size: 0.75rem;">Formatos: JPG, PNG, WEBP, MP4, MOV, AVI (Máx. 20MB).</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Tipo de Multimedia</label>
                        <select class="form-select" name="tipo_multimedia" required>
                            <option value="imagen">Fotografía / Imagen</option>
                            <option value="video">Video promocional</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Orden de Visualización</label>
                            <input type="number" class="form-control" name="orden" value="0">
                        </div>
                        <div class="col-md-6 mb-4 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="esPortadaCheck">
                                <label class="form-check-label fw-semibold" style="color: #334155;" for="esPortadaCheck">Establecer Portada</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn px-4 rounded-3 fw-medium text-white" style="background-color: #681DA8;">Guardar Recurso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL 3B: EDITAR MULTIMEDIA (S3 + Drag & Drop + Ctrl + V)          --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalEditarMultimedia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-3" style="background-color: #F3E8FD;">
                <h6 class="modal-title fw-bold" style="color: #681DA8;">Editar Recurso de Vitrina</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form id="formEditarMultimedia" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    {{-- Zona de Drag & Drop y Pegado (Ctrl + V) --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Reemplazar Archivo (Opcional)</label>
                        <div class="upload-drop-zone border-2 border-dashed rounded-4 p-4 text-center position-relative"
                             style="border-color: #D8B4FE; background-color: #FAF5FF; cursor: pointer; transition: all 0.2s;"
                             tabindex="0">
                            <input type="file" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0 file-input-target"
                                   name="url_archivo" accept="image/jpeg,image/png,image/jpg,image/webp,video/mp4,video/mov,video/avi" style="cursor: pointer;">
                            <div class="dz-message pointer-events-none">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7B1FA2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <p class="mb-1 fw-bold text-dark file-name-display" style="font-size: 0.9rem;">
                                    Arrastra tu nuevo archivo o <span style="color: #681DA8;">haz clic para buscar</span>
                                </p>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">
                                    💡 Tip: Presiona <kbd class="bg-white border px-1 rounded shadow-sm">Ctrl + V</kbd> para pegar una imagen.
                                </span>
                            </div>
                        </div>
                        <div class="form-text text-muted mt-1" style="font-size: 0.75rem;">Déjalo en blanco si deseas conservar el archivo actual de S3.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Tipo de Multimedia</label>
                        <select class="form-select" id="edit_media_tipo" name="tipo_multimedia" required>
                            <option value="imagen">Fotografía / Imagen</option>
                            <option value="video">Video promocional</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-muted small fw-semibold">Orden de Visualización</label>
                            <input type="number" class="form-control" id="edit_media_orden" name="orden">
                        </div>
                        <div class="col-md-6 mb-4 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="edit_media_portada">
                                <label class="form-check-label fw-semibold" style="color: #334155;" for="edit_media_portada">Establecer Portada</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn px-4 rounded-3 fw-medium text-white" style="background-color: #681DA8;">Actualizar Recurso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- SCRIPT UNIFICADO: ACORDEONES, MODALES, DRAG & DROP Y CTRL + V       --}}
{{-- ================================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /**
         * 1. INICIALIZACIÓN DE UI: ACORDEONES
         * Fuerza a que todos los contenedores colapsables inicien cerrados al cargar.
         */
        document.querySelectorAll('.collapse').forEach(function (el) {
            const collapseInstance = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapseInstance.hide();
        });

        /**
         * 2. PUENTE JS: MODAL DE EDICIÓN DE TARIFAS (MOTOR COMERCIAL)
         */
        document.querySelectorAll('.btn-editar-tarifa').forEach(button => {
            button.addEventListener('click', function () {
                const form = document.getElementById('formEditarTarifa');
                if (!form) return;

                form.action = this.getAttribute('data-url-update');
                document.getElementById('edit_tarifa_nombre').value = this.getAttribute('data-nombre-temporada') || '';
                document.getElementById('edit_tarifa_inicio').value = this.getAttribute('data-fecha-inicio') || '';
                document.getElementById('edit_tarifa_fin').value = this.getAttribute('data-fecha-fin') || '';
                document.getElementById('edit_tarifa_precio_noche').value = this.getAttribute('data-precio-noche') || '';
                document.getElementById('edit_tarifa_precio_fin_semana').value = this.getAttribute('data-precio-fin-semana') || '';
                document.getElementById('edit_tarifa_precio_minimo').value = this.getAttribute('data-precio-minimo-reserva') || '';

                const activeCheckbox = document.getElementById('edit_tarifa_active');
                if (activeCheckbox) {
                    activeCheckbox.checked = (this.getAttribute('data-active') === '1');
                }
            });
        });

        /**
         * 3. PUENTE JS: MODAL DE EDICIÓN DE MULTIMEDIA (VITRINA S3)
         */
        document.querySelectorAll('.btn-editar-multimedia').forEach(button => {
            button.addEventListener('click', function () {
                const form = document.getElementById('formEditarMultimedia');
                if (!form) return;

                form.action = this.getAttribute('data-url-update');
                document.getElementById('edit_media_tipo').value = this.getAttribute('data-tipo-multimedia') || 'imagen';
                document.getElementById('edit_media_orden').value = this.getAttribute('data-orden') || '0';

                const portadaCheckbox = document.getElementById('edit_media_portada');
                if (portadaCheckbox) {
                    portadaCheckbox.checked = (this.getAttribute('data-es-portada') === '1');
                }

                const dropZone = form.closest('.modal-body').querySelector('.upload-drop-zone');
                if (dropZone) {
                    const textDisplay = dropZone.querySelector('.file-name-display');
                    textDisplay.innerHTML = 'Arrastra tu nuevo archivo o <span style="color: #681DA8;">haz clic para buscar</span>';
                }
            });
        });

        /**
         * 4. GESTIÓN DE DRAG & DROP, CAMBIOS DE ARCHIVO Y PEGADO CON CTRL + V
         */
        document.querySelectorAll('.upload-drop-zone').forEach(dropZone => {
            const fileInput = dropZone.querySelector('.file-input-target');
            const textDisplay = dropZone.querySelector('.file-name-display');
            const modal = dropZone.closest('.modal');

            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    textDisplay.textContent = `📁 Archivo seleccionado: ${this.files[0].name}`;
                }
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.style.backgroundColor = '#F3E8FD';
                    dropZone.style.borderColor = '#681DA8';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.style.backgroundColor = '#FAF5FF';
                    dropZone.style.borderColor = '#D8B4FE';
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    fileInput.files = files;
                    textDisplay.textContent = `📁 Archivo seleccionado: ${files[0].name}`;
                }
            });

            if (modal) {
                modal.addEventListener('paste', (e) => {
                    if (!modal.classList.contains('show')) return;

                    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].kind === 'file') {
                            const file = items[i].getAsFile();
                            if (file) {
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                fileInput.files = dataTransfer.files;
                                textDisplay.textContent = `📋 Imagen pegada: ${file.name || 'clipboard_image.png'}`;
                                e.preventDefault();
                                break;
                            }
                        }
                    }
                });
            }
        });

        /**
         * 5. UX: REAPERTURA AUTOMÁTICA DE MODALES POR ERRORES DE VALIDACIÓN
         * (Se utiliza la palabra clave 'or' de PHP para evitar conflictos de parseo)
         */
        @if($errors->has('nombre_temporada') or $errors->has('precio_noche') or$errors->has('precio_minimo_reserva'))
            const modalTarifaEl = document.getElementById('modalAddTarifa');
            if (modalTarifaEl) {
                new bootstrap.Modal(modalTarifaEl).show();
            }
        @endif

        @if($errors->has('url_archivo') or$errors->has('tipo_multimedia'))
            const modalMultimediaEl = document.getElementById('modalAddMultimedia');
            if (modalMultimediaEl) {
                new bootstrap.Modal(modalMultimediaEl).show();
            }
        @endif

    });
</script>
