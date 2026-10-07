{{--
    ========================================================================
    VISTA: tab-inmuebles.blade.php (Modo Visualización / Solo Lectura)
    PROPÓSITO: Catálogo visual de inmuebles con carrusel protegido contra
              deformación, botón de editar (lápiz) superior izquierdo,
              favorito superior derecho, calificación fija y valores colapsables.
    ========================================================================
--}}

@if(isset($inmueble) && $inmueble)
    {{-- Si se solicita ver el detalle profundo individual --}}
    @include('rsv.admin.partials.inmuebles.show')
@else
    <div class="container-fluid px-0">

        {{-- Encabezado Minimalista de la Sección --}}
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold text-dark mb-1">Catálogo Visual de Inmuebles</h5>
                <p class="text-muted small mb-0">Visualización general del inventario, galerías fotográficas y tarifas vigentes.</p>
            </div>
            <div>
                {{-- Badge de Total Propiedades con alto contraste y visibilidad mejorada --}}
                <span class="badge px-3 py-2 fw-semibold shadow-sm" style="font-size: 0.8rem; background-color: #F3E8FD; color: #681DA8; border: 1px solid #D8B4FE;">
                    Total Propiedades: {{ isset($inmuebles) ? $inmuebles->total() : 0 }}
                </span>
            </div>
        </div>

        {{-- ================================================================== --}}
        {{-- GRID DE INMUEBLES: TARJETAS CON CARRUSEL Y ELEMENTOS FLOTANTES      --}}
        {{-- ================================================================== --}}
        <div class="row g-4">
            @isset($inmuebles)
                @forelse($inmuebles as $item)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white d-flex flex-column">

                            {{-- ---------------------------------------------------------- --}}
                            {{-- SECCIÓN 1: CARRUSEL DE FOTOS / MULTIMEDIA                   --}}
                            {{-- ---------------------------------------------------------- --}}
                            <div id="carouselInmueble{{ $item->id }}" class="carousel slide bg-dark position-relative flex-shrink-0" data-bs-ride="carousel" style="height: 220px; overflow: hidden;">

                                {{-- 1. BOTÓN DE EDITAR (LÁPIZ FLOTANTE SUPERIOR IZQUIERDO) --}}
                                <a href="{{ route('rsv.inmuebles.show', $item->id) }}"
                                   class="btn btn-light btn-sm rounded-circle position-absolute top-0 start-0 m-3 p-2 shadow-sm d-flex align-items-center justify-content-center"
                                   style="width: 35px; height: 35px; z-index: 10; background: rgba(255, 255, 255, 0.85); border: none; transition: transform 0.2s; color: #475569;"
                                   onmouseover="this.style.transform='scale(1.1)';"
                                   onmouseout="this.style.transform='scale(1)';"
                                   title="Ver expediente / Gestionar inmueble">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>

                                {{-- 2. BOTÓN DE FAVORITO (CORAZÓN FLOTANTE SUPERIOR DERECHO) --}}
                                <button type="button"
                                        class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-3 p-2 shadow-sm d-flex align-items-center justify-content-center btn-favorito"
                                        style="width: 35px; height: 35px; z-index: 10; background: rgba(255, 255, 255, 0.85); border: none; transition: transform 0.2s;"
                                        onmouseover="this.style.transform='scale(1.1)';"
                                        onmouseout="this.style.transform='scale(1)';"
                                        title="Agregar a favoritos">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>

                                @php
                                    $galeria = $item->multimedia ?? collect();
                                @endphp

                                <div class="carousel-inner w-100 h-100">
                                    @forelse($galeria as $index => $media)
                                        <div class="carousel-item w-100 h-100 {{ $index === 0 ? 'active' : '' }}">
                                            {{-- object-fit: cover previene cualquier deformación de las fotos de AWS S3 --}}
                                            <img src="{{ $media->url_archivo }}"
                                                class="d-block w-100 h-100"
                                                style="object-fit: cover; object-position: center;"
                                                alt="Foto inmueble">

                                            @if($media->es_portada)
                                                <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 start-0 m-2 px-2 py-1" style="font-size: 0.65rem; z-index: 5;">Portada</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="carousel-item active w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                            <div class="text-center text-muted py-5">
                                                <span style="font-size: 0.8rem;" class="fst-italic">Sin galería fotográfica</span>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                @if($galeria->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmueble{{ $item->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselInmueble{{ $item->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>
                                @endif
                            </div>

                            {{-- ---------------------------------------------------------- --}}
                            {{-- SECCIÓN 2: CUERPO PRINCIPAL DE LA TARJETA                  --}}
                            {{-- ---------------------------------------------------------- --}}
                            <div class="card-body p-3 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    {{-- Título y Estado (Badge con color sólido de alta visibilidad) --}}
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 200px;" title="{{ $item->name }}">
                                            {{ $item->name }}
                                        </h6>
                                        @if($item->active)
                                            <span class="badge px-2 py-1 fw-semibold" style="font-size: 0.7rem; background-color: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">Activo</span>
                                        @else
                                            <span class="badge px-2 py-1 fw-semibold" style="font-size: 0.7rem; background-color: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;">Inactivo</span>
                                        @endif
                                    </div>

                                    {{-- CALIFICACIÓN Y COMENTARIOS (FIJOS Y SIEMPRE VISIBLES) --}}
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        {{-- Bloque de Estrellas --}}
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="text-warning d-flex align-items-center gap-1">
                                                @for($i = 0; $i < 5; $i++)
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-muted fw-semibold" style="font-size: 0.72rem;">5.0</span>
                                        </div>

                                        {{-- Contador de Comentarios --}}
                                        <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.72rem;" title="Reseñas de clientes">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                            <span class="fw-medium">{{ $item->comentarios_count ?? 0 }} reseñas</span>
                                        </div>
                                    </div>

                                    {{-- Ubicación --}}
                                    <p class="text-muted small mb-3 text-truncate">
                                        <i class="bi bi-geo-alt"></i> {{ $item->city ?? 'Sin ciudad' }} — {{ $item->ubicacion ?? 'Sin dirección' }}
                                    </p>

                                    {{-- ---------------------------------------------------------- --}}
                                    {{-- SECCIÓN COLAPSABLE: VALORES Y CARACTERÍSTICAS TÉCNICAS     --}}
                                    {{-- ---------------------------------------------------------- --}}
                                    <div class="collapse" id="detallesExtras{{ $item->id }}">
                                        <div class="pt-1 pb-1">

                                            {{-- Mini Grilla de Especificaciones (Capacidad y Precio Base) --}}
                                            <div class="bg-light bg-opacity-75 rounded-3 p-2 mb-2">
                                                <div class="row text-center g-0">
                                                    <div class="col-6 border-end">
                                                        <span class="text-muted d-block" style="font-size: 0.65rem;">CAPACIDAD MÁX.</span>
                                                        <span class="fw-semibold text-dark" style="font-size: 0.8rem;">{{ $item->capacidad_maxima ?? 'N/A' }} pers.</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="text-muted d-block" style="font-size: 0.65rem;">PRECIO BASE</span>
                                                        <span class="fw-semibold text-success" style="font-size: 0.8rem;">${{ number_format($item->precio_base_noche, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Última Tarifa Registrada (RESTRICCIONES DESTACADAS) --}}
                                            <div class="border-top pt-2">
                                                <span class="text-muted text-uppercase d-block mb-2" style="font-size: 0.62rem; letter-spacing: 0.5px;">Condiciones Última Tarifa</span>
                                                @php
                                                    $ultimaTarifa = $item->latestTarifa;
                                                @endphp

                                                @if($ultimaTarifa)
                                                    <div class="bg-light border rounded-3 p-2">

                                                        {{-- Fila Superior: Info Básica sin protagonismo --}}
                                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                                                            <div>
                                                                <span class="fw-bold text-dark d-block" style="font-size: 0.75rem; line-height: 1.2;">{{ $ultimaTarifa->nombre_temporada }}</span>
                                                                <span class="text-muted" style="font-size: 0.62rem;">{{ optional($ultimaTarifa->fecha_inicio)->format('Y-m-d') }} al {{ optional($ultimaTarifa->fecha_fin)->format('Y-m-d') }}</span>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="text-muted d-block" style="font-size: 0.55rem; text-transform: uppercase;">Precio Noche</span>
                                                                <span class="fw-semibold text-secondary" style="font-size: 0.75rem;">${{ number_format($ultimaTarifa->precio_noche, 2) }}</span>
                                                            </div>
                                                        </div>

                                                        {{-- Fila Inferior: Restricciones Destacadas (Mínimo y Máximo) --}}
                                                        <div class="row g-2 text-center">
                                                            <div class="col-6">
                                                                <div class="bg-white border rounded-2 p-1 shadow-sm d-flex flex-column justify-content-center h-100">
                                                                    <span class="text-muted fw-semibold" style="font-size: 0.55rem; text-transform: uppercase; letter-spacing: 0.5px;">Mín. Reserva</span>
                                                                    <span class="fw-bolder" style="font-size: 0.85rem; color: #16A34A; line-height: 1.2;">${{ number_format($ultimaTarifa->precio_minimo_reserva, 2) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="bg-white border rounded-2 p-1 shadow-sm d-flex flex-column justify-content-center h-100">
                                                                    <span class="text-muted fw-semibold" style="font-size: 0.55rem; text-transform: uppercase; letter-spacing: 0.5px;">Máx. Días</span>
                                                                    <span class="fw-bolder" style="font-size: 0.85rem; color: #2563EB; line-height: 1.2;">{{ $ultimaTarifa->dias_maximos ? $ultimaTarifa->dias_maximos : 'Ilimitado' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @else
                                                    <div class="text-center py-2 bg-light rounded-3 border border-dashed">
                                                        <span class="text-muted fst-italic" style="font-size: 0.7rem;">Sin tarifas temporales activas</span>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                {{-- ---------------------------------------------------------- --}}
                                {{-- PIE DE TARJETA: SOLO BOTÓN DESPLEGABLE DE DETALLES         --}}
                                {{-- ---------------------------------------------------------- --}}
                                <div class="mt-2 pt-2 border-top">
                                    <button class="btn btn-link btn-sm text-decoration-none w-100 p-1 text-muted d-flex align-items-center justify-content-center gap-1 shadow-none"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detallesExtras{{ $item->id }}"
                                            aria-expanded="false"
                                            aria-controls="detallesExtras{{ $item->id }}"
                                            style="font-size: 0.75rem;">
                                        <span>Ver detalles</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-white border-0 shadow-sm rounded-4">
                            <p class="text-muted mb-0">No hay inmuebles registrados en el catálogo actualmente.</p>
                        </div>
                    </div>
                @endforelse
            @endisset
        </div>

        {{-- Paginación Fluida --}}
        <div class="mt-4">
            @include('rsv.components.pagination', ['paginator' => $inmuebles ?? null])
        </div>

    </div>
@endif
