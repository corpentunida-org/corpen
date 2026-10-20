{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php --}}
{{-- ESTILO: sistema suave idéntico a tarifas · título 9.5px · textos 8px · pasteles --}}
{{-- MODO GLOBAL   = grid de tarjetas + paginación + botón Nuevo       --}}
{{-- MODO INDIVIDUAL = ficha única ampliada con stats y modal de edición PROPIO --}}
{{-- FIX CARRUSEL: autoplay garantizado por JS + flechas SIEMPRE visibles --}}

@php
    // RED DE SEGURIDAD (Fallbacks)
    // Si el controlador actual no envía estas variables, las definimos por defecto para evitar errores.
    $modoGlobal = $modoGlobal ?? (!isset($inmueble) || !$inmueble);

    if (!isset($propiedadesCollection)) {
        $propiedadesCollection = $modoGlobal
            ? ($inmuebles ?? collect([]))
            : collect([$inmueble]);
    }

    if (!isset($tiposInmueblesOpt)) {
        $tiposInmueblesOpt = \App\Models\Rsv\TipoInmueble::orderBy('nombre')->get();
    }
@endphp

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CATÁLOGO · PANEL PRINCIPAL (simétrico al panel de tarifas)       --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-houses me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $modoGlobal ? 'Visión Global del Catálogo' : 'Ficha Visual del Inmueble' }}
        </h6>

        @if ($modoGlobal)
            <button type="button"
                    class="btn btn-sm d-inline-flex align-items-center border"
                    style="gap: 4px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px; transition: all .15s ease;"
                    onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                    onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
                    data-bs-toggle="modal" data-bs-target="#modalCrearInmueble">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Inmueble
            </button>
        @else
            <span class="d-inline-flex align-items-center border"
                  style="gap: 2px; font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2 !important;">
                <i class="bi bi-fingerprint" style="font-size: 7px; line-height: 1;"></i>EXPEDIENTE #{{ $inmueble->id }}
            </span>
        @endif

    </div>

    {{-- El wrapper cambia de clase en individual para ampliar carrusel y tipografía --}}
    <div class="row g-2">

        @forelse($propiedadesCollection as $prop)

            {{-- Individual: tarjeta más ancha para aprovechar la ficha --}}
            <div class="col-12 {{ $modoGlobal ? 'col-md-6 col-xl-4' : 'col-lg-8' }}">

                <div class="card h-100 overflow-hidden d-flex flex-column border bg-white text-decoration-none"
                     style="border-color: #f1f5f9 !important; border-radius: 5px; transition: border-color .15s ease, box-shadow .15s ease;"
                     onmouseover="this.style.borderColor='#e9eef4'; this.style.boxShadow='0 2px 10px rgba(15,23,42,.04)';"
                     onmouseout="this.style.borderColor='#f1f5f9'; this.style.boxShadow='none';">

                    {{-- Carrusel de Fotos del Inmueble --}}
                    <div id="carouselInmuebleGlobal{{ $prop->id }}"
                         class="carousel slide position-relative flex-shrink-0 border-bottom"
                         style="height: {{ $modoGlobal ? '150px' : '220px' }}; background: #f4f6f8; border-color: #f1f5f9 !important;"
                         data-bs-ride="carousel"
                         data-bs-interval="5000">

                        <div class="carousel-inner w-100 h-100">
                            @forelse($prop->multimedia ?? collect() as $index => $media)
                                <div class="carousel-item w-100 h-100 {{ $index === 0 ? 'active' : '' }}" data-bs-interval="5000">
                                    <img src="{{ $media->url_archivo }}" alt="Foto inmueble" class="w-100 h-100" style="object-fit: cover; object-position: center;">
                                    @if($media->es_portada)
                                        <span class="position-absolute bg-white border text-muted"
                                              style="bottom: 6px; left: 6px; z-index: 5; font-size: 7px; font-weight: 600; letter-spacing: .05em; border-color: rgba(233,238,244,.9) !important; border-radius: 3px; padding: 0 6px; line-height: 14px; background-color: rgba(255,255,255,.92) !important;">PORTADA</span>
                                    @endif
                                </div>
                            @empty
                                <div class="carousel-item w-100 h-100 active">
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center fst-italic" style="background: #fafbfd; color: #d4dae2; font-size: 8.5px;">
                                        <i class="bi bi-image me-1"></i> Sin fotos en galería
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if(($prop->multimedia ? $prop->multimedia->count() : 0) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="prev"
                                    style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 20px; height: 20px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.88); box-shadow: 0 1px 4px rgba(15,23,42,.18); opacity: .8; z-index: 5; left: 6px; transition: all .15s ease;"
                                    onmouseover="this.style.opacity='1'; this.style.background='#fff'; this.style.transform='translateY(-50%) scale(1.12)';"
                                    onmouseout="this.style.opacity='.8'; this.style.background='rgba(255,255,255,.88)'; this.style.transform='translateY(-50%) scale(1)';">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 10px; height: 10px; filter: invert(.5);"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="next"
                                    style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 20px; height: 20px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.88); box-shadow: 0 1px 4px rgba(15,23,42,.18); opacity: .8; z-index: 5; right: 6px; transition: all .15s ease;"
                                    onmouseover="this.style.opacity='1'; this.style.background='#fff'; this.style.transform='translateY(-50%) scale(1.12)';"
                                    onmouseout="this.style.opacity='.8'; this.style.background='rgba(255,255,255,.88)'; this.style.transform='translateY(-50%) scale(1)';">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="width: 10px; height: 10px; filter: invert(.5);"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif

                    </div>

                    {{-- Cuerpo de la Tarjeta --}}
                    <div class="card-body d-flex flex-column flex-grow-1" style="padding: 9px 11px 10px;">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div class="text-truncate" style="font-size: {{ $modoGlobal ? '9.5px' : '11px' }}; font-weight: 600; color: #475569;" title="{{ $prop->name }}">{{ $prop->name }}</div>

                            {{-- Precio real: Mín. Reserva de la última tarifa --}}
                            @if(!is_null($prop->latestTarifa?->precio_minimo_reserva))
                                <span class="d-inline-flex align-items-baseline flex-shrink-0 border"
                                      style="gap: 2px; font-size: 9px; font-weight: 600; color: #3d7a5c; background: #f0f7f2; border-color: #d5e7dc !important; border-radius: 3px; padding: 1px 7px; font-variant-numeric: tabular-nums;"
                                      title="Mínimo de reserva · última tarifa">
                                    ${{ number_format($prop->latestTarifa->precio_minimo_reserva, 0) }}
                                    <small style="font-size: 6.5px; font-weight: 600; color: #b3bcc7;">mín. res.</small>
                                </span>
                            @endif
                        </div>

                        {{-- Chips: tipo + estado (mismo lenguaje que tarifas) --}}
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                            @if($prop->tipoInmueble)
                                <span class="d-inline-flex align-items-center border"
                                      style="gap: 2px; font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2 !important;"
                                      title="Tipo de inmueble">
                                    <i class="bi bi-tag-fill" style="font-size: 7px; line-height: 1;"></i>{{ $prop->tipoInmueble->nombre }}
                                </span>
                            @endif

                            @if(isset($prop->active))
                                @if($prop->active)
                                    <span class="d-inline-flex align-items-center border" style="gap: 2px; font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #5d8a70; background: #f0f7f2; border-color: #d8e9de !important;">
                                        <i class="bi bi-check-lg" style="font-size: 7px; line-height: 1;"></i>Activo
                                    </span>
                                @else
                                    <span class="d-inline-flex align-items-center border" style="gap: 2px; font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #9aa5b1; background: #fafbfd; border-color: #eef2f6 !important;">
                                        <i class="bi bi-dash-lg" style="font-size: 7px; line-height: 1;"></i>Inactivo
                                    </span>
                                @endif
                            @endif
                        </div>

                        <div class="text-truncate mb-0" style="font-size: 8px; color: #94a3b8;">
                            <i class="bi bi-geo-alt me-1" style="font-size: 8px; color: #c3ccd6;"></i>{{ $prop->city ?? 'Sin ciudad' }}
                            @isset($prop->capacidad_maxima)
                                <span style="color: #e2e8f0; margin: 0 4px;">|</span>
                                <i class="bi bi-people me-1" style="font-size: 8px; color: #c3ccd6;"></i>{{ $prop->capacidad_maxima }} máx.
                            @endisset
                            {{-- Dirección solo en individual (hay más espacio) --}}
                            @if(!$modoGlobal && !empty($prop->ubicacion))
                                <span style="color: #e2e8f0; margin: 0 4px;">|</span>
                                <i class="bi bi-signpost-2 me-1" style="font-size: 8px; color: #c3ccd6;"></i>{{ $prop->ubicacion }}
                            @endif
                        </div>

                        {{-- Stats solo en individual (datos de las relaciones) --}}
                        @if(!$modoGlobal)
                            <div class="d-flex w-100" style="gap: 6px; margin-top: 8px;">
                                <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 3px; padding: 5px 8px; gap: 1px;">
                                    <span style="font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7;">Fotos</span>
                                    <span style="font-size: 10px; font-weight: 700; font-variant-numeric: tabular-nums; color: #5b7fa6;">{{ $prop->multimedia ? $prop->multimedia->count() : 0 }}</span>
                                </div>
                                <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 3px; padding: 5px 8px; gap: 1px;">
                                    <span style="font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7;">Reservas</span>
                                    <span style="font-size: 10px; font-weight: 700; font-variant-numeric: tabular-nums; color: #5d8a70;">{{ $prop->reservas_count ?? 0 }}</span>
                                </div>
                                <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 3px; padding: 5px 8px; gap: 1px;">
                                    <span style="font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7;">Bloqueos</span>
                                    <span style="font-size: 10px; font-weight: 700; font-variant-numeric: tabular-nums; color: #8a6fae;">{{ $prop->bloqueos_calendario_count ?? 0 }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-auto border-top" style="padding-top: 8px; border-color: #f1f5f9 !important;">
                            @if($modoGlobal)
                                {{-- GLOBAL: navegar al expediente --}}
                                <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                   class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 border"
                                   style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;"
                                   onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                                   onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';">
                                    <i class="bi bi-pencil-square" style="font-size: 9px;"></i> Gestionar Propiedad
                                </a>
                            @else
                                {{-- INDIVIDUAL: modal PROPIO de este partial (mismo tab-pane, siempre visible) --}}
                                <button type="button" class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 border"
                                        style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;"
                                        onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                                        onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarInmuebleFicha">
                                    <i class="bi bi-pencil-square" style="font-size: 9px;"></i> Editar Propiedad
                                </button>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                <div class="border text-center" style="border-style: dashed !important; border-color: #e9eef4 !important; border-radius: 5px; background: #fafbfd; padding: 24px 16px; color: #b3bcc7; font-size: 8.5px;">
                    <i class="bi bi-house-exclamation d-block mb-1" style="font-size: 16px; color: #d4dae2;"></i>
                    No hay inmuebles registrados en el catálogo.
                </div>
            </div>

        @endforelse

    </div>

    {{-- Paginación (solo global) --}}
    @if($modoGlobal && isset($propiedadesCollection) && method_exists($propiedadesCollection, 'links'))
        <div class="mt-3" style="font-size: 8.5px;">
            {{ $propiedadesCollection->appends(['inmueble_id' => 'todos'])->links() }}
        </div>
    @endif

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPT · CARRUSEL: autoplay garantizado + re-init al mostrar tabs --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
    (function () {
        var CAT_INTERVAL = 5000; /* velocidad del autoplay en ms */

        function initCatCarousels(root) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Carousel) return;
            var scope = (root && root.querySelectorAll) ? root : document;
            scope.querySelectorAll('.carousel:not([data-cat-carousel-init])').forEach(function (el) {
                el.setAttribute('data-cat-carousel-init', '1');
                var cfg = { interval: CAT_INTERVAL, ride: 'carousel', pause: 'hover', wrap: true, touch: true };
                try {
                    var inst = bootstrap.Carousel.getInstance(el);
                    if (!inst && typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                        inst = bootstrap.Carousel.getOrCreateInstance(el, cfg);
                    } else if (!inst) {
                        inst = new bootstrap.Carousel(el, cfg);
                    }
                    if (inst && inst.cycle && el.closest('.tab-pane.show, .tab-pane.active, body')) {
                        inst.cycle();
                    }
                } catch (e) { /* silencioso: nunca romper la página */ }
            });
        }

        document.addEventListener('DOMContentLoaded', function () { initCatCarousels(); });

        document.addEventListener('shown.bs.tab', function (e) {
            var sel = e.target && e.target.getAttribute && e.target.getAttribute('data-bs-target');
            var pane = sel ? document.querySelector(sel) : null;
            if (pane) initCatCarousels(pane);
        });

        window.initCatCarousels = initCatCarousels;
    })();
</script>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · NUEVO INMUEBLE (solo se usa en global)                   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCrearInmueble" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 5px; font-family: Calibri, 'Segoe UI', system-ui, sans-serif; font-size: 8px; line-height: 1.4; color: #64748b;">

            <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo de Inmuebles
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 10.5px; color: #475569 !important;">Nuevo Inmueble</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b style="color: #c98a85;">*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.inmuebles.store') }}" method="POST">
                @csrf

                <div class="modal-body" style="padding: 12px 14px;">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i> Identificación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" placeholder="Ej. Apartamento Vista al Mar" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Se mostrará en el catálogo público</div>
                        </div>

                        <div class="mb-0">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt->where('active', 1) as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}</option>
                                @endforeach
                            </select>
                            <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Clasificación: Apartamento, Casa, Finca…</div>
                        </div>
                    </div>


                    {{-- 2 · UBICACIÓN --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-geo-alt" style="font-size: 7.5px; opacity: .7;"></i> Ubicación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="city" placeholder="Ej. Bogotá" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                        </div>

                        <div class="mb-0">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección / Ubicación física</label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" placeholder="Ej. Calle 100 # 15-20" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                        </div>
                    </div>


                    {{-- 3 · CAPACIDAD --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-people" style="font-size: 7.5px; opacity: .7;"></i> Capacidad
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="mb-0">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                            <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" placeholder="Ej. 6" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Huéspedes permitidos</div>
                        </div>
                    </div>


                    {{-- 4 · ESTADO --}}
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-toggle-on" style="font-size: 7.5px; opacity: .7;"></i> Estado
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                            <div class="text-start">
                                <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                <div style="font-size: 7px; color: #b3bcc7;">Visible en el catálogo público</div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="newInmActive" checked>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                    <span class="text-muted" style="font-size: 7.5px;">Podrás agregar fotos y tarifas al gestionarlo</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · EDITAR INMUEBLE (ficha · solo individual · ID único)     --}}
{{-- Vive DENTRO de esta pestaña → siempre visible, sin gris fantasma --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if(!$modoGlobal)
<div class="modal fade" id="modalEditarInmuebleFicha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 5px; font-family: Calibri, 'Segoe UI', system-ui, sans-serif; font-size: 8px; line-height: 1.4; color: #64748b;">

            <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Ficha Visual · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 10.5px; color: #475569 !important;">{{ $inmueble->name }}</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los cambios se reflejan en el catálogo</small>
                </div>
            </div>

            <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding: 12px 14px;">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i> Identificación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" value="{{ old('name', $inmueble->name) }}" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                        </div>

                        <div class="mb-0">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}" {{ $inmueble->tipo_inmueble_id == $tipoOpt->id ? 'selected' : '' }}>
                                        {{ $tipoOpt->nombre }}{{ $tipoOpt->active ? '' : ' (inactivo)' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    {{-- 2 · UBICACIÓN --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-geo-alt" style="font-size: 7.5px; opacity: .7;"></i> Ubicación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" value="{{ old('city', $inmueble->city) }}" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                            <div class="col-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                        </div>
                    </div>


                    {{-- 3 · CAPACIDAD Y ESTADO --}}
                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-sliders" style="font-size: 7.5px; opacity: .7;"></i> Capacidad y Estado
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" value="{{ old('capacidad_maxima', $inmueble->capacidad_maxima) }}" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Huéspedes</div>
                            </div>

                            <div class="col-7">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                    <div class="text-start">
                                        <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                        <div style="font-size: 7px; color: #b3bcc7;">Visible en catálogo público</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="editActiveInmuebleFicha" {{ old('active', $inmueble->active) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                    <span class="text-muted" style="font-size: 7.5px;">EXPEDIENTE #{{ $inmueble->id }}</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
@endif
