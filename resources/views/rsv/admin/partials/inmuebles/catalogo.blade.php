{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php --}}
{{-- ESTILO: sistema suave idéntico a tarifas · título 9.5px · textos 8px · pasteles --}}
{{-- MODO GLOBAL   = grid de tarjetas + paginación + botón Nuevo      --}}
{{-- MODO INDIVIDUAL = ficha única ampliada con stats y modal de edición PROPIO --}}
{{-- FIX CARRUSEL: autoplay garantizado por JS + flechas SIEMPRE visibles --}}

@php
    // Detectamos si estamos en la vista global o en un inmueble específico (idéntico a tarifas)
    $modoGlobal = !isset($inmueble) || !$inmueble;

    if ($modoGlobal) {
        // ═══ GLOBAL: grid del catálogo (viene COMPLETO del controlador en modo 'todos') ═══
        $propiedadesCollection = isset($inmueblesGrid) ? $inmueblesGrid : collect([]);
    } else {
        // ═══ INDIVIDUAL: ficha única · relaciones y conteos garantizados ═══
        $inmueble->loadMissing(['tipoInmueble', 'multimedia', 'latestTarifa']);
        // Los counts ya vienen del controlador; loadCount es no-op si existen
        if (method_exists($inmueble, 'loadCount')) {
            $inmueble->loadCount(['reservas', 'bloqueosCalendario']);
        }
        $propiedadesCollection = collect([$inmueble]);
    }

    // Tipos de inmueble para los modales (carga segura · incluye inactivos marcados para edición)
    $tiposInmueblesOpt = isset($tiposInmuebles)
        ? $tiposInmuebles
        : \App\Models\Rsv\TipoInmueble::orderBy('nombre')->get();

    // Helper: precio principal (Mín. Reserva de la última tarifa — dato real del modelo)
    $precioPrincipalDe = function ($prop) {
        $t = $prop->relationLoaded('latestTarifa') ? $prop->latestTarifa : null;
        return $t?->precio_minimo_reserva;
    };
@endphp

{{-- Escala px del sistema (idéntica a tarifas) --}}
<style>
    .cat-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }

    /* ═══ TARJETA · borde fino, radio 5px (misma escala del panel) ═══ */
    .cat-soft .cat-card {
        background: #fff;
        border: 1px solid var(--grid);
        border-radius: 5px;
        overflow: hidden;
        height: 100%;
        display: flex; flex-direction: column;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .cat-soft .cat-card:hover { border-color: var(--grid-2); box-shadow: 0 2px 10px rgba(15,23,42,.04); }

    /* Carrusel (150px en global · 220px en individual) */
    .cat-soft .cat-carousel {
        height: 150px;
        background: #f4f6f8;
        position: relative;
        flex-shrink: 0;
        border-bottom: 1px solid var(--grid);
    }
    .cat-soft .cat-single .cat-carousel { height: 220px; }
    .cat-soft .cat-carousel img {
        object-fit: cover; object-position: center;
        width: 100%; height: 100%;
    }
    .cat-soft .cat-carousel .carousel-inner,
    .cat-soft .cat-carousel .carousel-item { width: 100%; height: 100%; }

    /* ═══ FLECHAS SIEMPRE VISIBLES: botón circular blanco con sombra ═══
       Antes: opacity .2 (invisibles) y solo .55 al hover → nadie las veía.
       Ahora: chip circular 20px, visible sobre cualquier foto, sube a opaco al hover. */
    .cat-soft .cat-carousel .carousel-control-prev,
    .cat-soft .cat-carousel .carousel-control-next {
        position: absolute; top: 50%; bottom: auto;
        transform: translateY(-50%);
        width: 20px; height: 20px;
        margin: 0; padding: 0;
        border: 1px solid rgba(233,238,244,.95);
        border-radius: 50%;
        background: rgba(255,255,255,.88);
        box-shadow: 0 1px 4px rgba(15,23,42,.18);
        opacity: .8;
        transition: opacity .15s ease, background .15s ease, transform .15s ease;
        z-index: 5;
    }
    .cat-soft .cat-carousel .carousel-control-prev { left: 6px; }
    .cat-soft .cat-carousel .carousel-control-next { right: 6px; }
    .cat-soft .cat-carousel:hover .carousel-control-prev,
    .cat-soft .cat-carousel .carousel-control-prev:focus-visible,
    .cat-soft .cat-carousel .carousel-control-next:focus-visible { opacity: 1; background: #fff; }
    .cat-soft .cat-carousel .carousel-control-prev:hover,
    .cat-soft .cat-carousel .carousel-control-next:hover { opacity: 1; background: #fff; transform: translateY(-50%) scale(1.12); }
    .cat-soft .cat-carousel .carousel-control-prev-icon,
    .cat-soft .cat-carousel .carousel-control-next-icon {
        width: 10px; height: 10px;
        filter: invert(.5);
    }

    .cat-soft .portada-badge {
        position: absolute; bottom: 6px; left: 6px; z-index: 5;
        font-size: 7px; font-weight: 600; letter-spacing: .05em;
        color: var(--ink-3);
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(233,238,244,.9);
        border-radius: 3px; padding: 0 6px; line-height: 14px;
    }

    .cat-soft .cat-empty-media {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: var(--head);
        color: #d4dae2; font-size: 8.5px; font-style: italic;
    }

    /* Cuerpo */
    .cat-soft .cat-body { padding: 9px 11px 10px; display: flex; flex-direction: column; flex-grow: 1; }

    .cat-soft .cat-title {
        font-size: 9.5px; font-weight: 600; color: var(--ink-2);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .cat-soft .cat-single .cat-title { font-size: 11px; }

    /* Chips (idénticos a rsv-chip de tarifas) */
    .cat-soft .rsv-chip {
        display: inline-flex; align-items: center; gap: 2px;
        font-size: 7.5px; font-weight: 600;
        padding: 0 7px; border-radius: 8px; line-height: 13px;
        border: 1px solid; white-space: nowrap;
    }
    .cat-soft .rsv-chip i    { font-size: 7px; line-height: 1; }
    .cat-soft .rsv-chip.ok   { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
    .cat-soft .rsv-chip.off  { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }
    .cat-soft .rsv-chip.tipo { color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2; }
    .cat-soft .rsv-chip.tint { color: #8a6fae; background: #f7f3fb; border-color: #e6dcf2; }

    /* Precio (acento verde del sistema · dato real: precio_minimo_reserva de latestTarifa) */
    .cat-soft .price-chip {
        flex-shrink: 0;
        display: inline-flex; align-items: baseline; gap: 2px;
        font-size: 9px; font-weight: 600;
        color: var(--accent);
        background: var(--accent-bg);
        border: 1px solid var(--accent-bd);
        border-radius: 3px; padding: 1px 7px;
        font-variant-numeric: tabular-nums;
    }
    .cat-soft .price-chip small { font-size: 6.5px; font-weight: 600; color: var(--ink-4); }

    /* Meta (ciudad · capacidad · dirección) */
    .cat-soft .cat-meta {
        font-size: 8px; color: var(--ink-3);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .cat-soft .cat-meta i { font-size: 8px; color: #c3ccd6; }
    .cat-soft .cat-meta .sep { color: #e2e8f0; margin: 0 4px; }

    /* Stats (solo individual: fotos · reservas · bloqueos) */
    .cat-soft .cat-stats { display: flex; gap: 6px; margin-top: 8px; }
    .cat-soft .cat-stat {
        flex: 1; min-width: 0;
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 3px; padding: 5px 8px;
        display: flex; flex-direction: column; gap: 1px;
    }
    .cat-soft .cat-stat .lbl { font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-4); }
    .cat-soft .cat-stat .val { font-size: 10px; font-weight: 700; color: var(--ink-2); font-variant-numeric: tabular-nums; }
    .cat-soft .cat-stat .val.ok   { color: #5d8a70; }
    .cat-soft .cat-stat .val.blue { color: #5b7fa6; }
    .cat-soft .cat-stat .val.tint { color: #8a6fae; }

    /* Botones */
    .cat-soft .btn-manage {
        display: flex; align-items: center; justify-content: center; gap: 5px;
        width: 100%;
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd);
        border-radius: 3px;
        font-family: inherit; font-size: 8.5px; font-weight: 600;
        padding: 4px 10px;
        text-decoration: none;
        transition: all .15s ease;
        cursor: pointer;
    }
    .cat-soft .btn-manage:hover { background: #e3f1e9; color: #34684e; text-decoration: none; }
    .cat-soft .btn-manage i { font-size: 9px; }

    .cat-soft .btn-create {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--accent); color: #fff;
        border: 1px solid var(--accent);
        border-radius: 3px;
        font-family: inherit; font-size: 8.5px; font-weight: 600;
        padding: 4px 10px;
        text-decoration: none;
        transition: all .15s ease;
        cursor: pointer;
    }
    .cat-soft .btn-create:hover { background: #2f6048; border-color: #2f6048; color: #fff; }

    /* Footer de tarjeta con separador fino */
    .cat-soft .cat-foot {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid var(--grid);
    }

    /* Empty state compacto (simétrico a tarifas) */
    .cat-soft .cat-empty {
        border: 1px dashed var(--grid-2);
        border-radius: 5px;
        background: var(--head);
        text-align: center; padding: 24px 16px;
        color: var(--ink-4); font-size: 8.5px;
    }
    .cat-soft .cat-empty i { font-size: 16px; color: #d4dae2; display: block; margin-bottom: 5px; }

    /* ═══ MODALES — escala px idéntica a tarifas-soft-modal ═══ */
    .cat-soft-modal .modal-content {
        border: 1px solid var(--grid-2); border-radius: 5px;
        box-shadow: 0 8px 30px rgba(15,23,42,.06);
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px; line-height: 1.4; color: var(--ink);
    }
    .cat-soft-modal .modal-header {
        border-bottom: 1px solid var(--grid); padding: 9px 14px;
    }
    .cat-soft-modal .modal-title { font-size: 10.5px; font-weight: 600; color: var(--ink-2); }
    .cat-soft-modal .modal-body { padding: 12px 14px; }
    .cat-soft-modal .modal-footer {
        border-top: 1px solid var(--grid); padding: 9px 14px;
        display: flex; align-items: center; justify-content: space-between;
    }

    .cat-soft-modal .tz-section { margin-bottom: 9px; }
    .cat-soft-modal .tz-section:last-of-type { margin-bottom: 0; }
    .cat-soft-modal .tz-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 7px; font-weight: 600; letter-spacing: .07em;
        text-transform: uppercase; color: var(--ink-4);
        margin-bottom: 4px;
    }
    .cat-soft-modal .tz-label i { font-size: 7.5px; opacity: .7; }
    .cat-soft-modal .tz-label::after {
        content: ''; flex: 1; height: 1px; background: var(--grid);
    }

    .cat-soft-modal .form-label {
        font-size: 7.5px; font-weight: 600; letter-spacing: .05em;
        text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px;
    }
    .cat-soft-modal .req { color: #c98a85; }
    .cat-soft-modal .form-control,
    .cat-soft-modal .form-select {
        font-size: 9.5px; color: var(--ink-2);
        border-color: var(--grid-2); border-radius: 3px;
        padding: 3px 8px; box-shadow: none !important;
    }
    .cat-soft-modal .form-control:focus,
    .cat-soft-modal .form-select:focus { border-color: #c8d6cd; }
    .cat-soft-modal .tz-hint { font-size: 7px; color: #c3ccd6; margin-top: 1px; }

    .cat-soft-modal .money-group .input-group-text {
        background: var(--head); border-color: var(--grid-2);
        color: #5d8a70; font-size: 8px; font-weight: 600;
        padding-left: 6px; padding-right: 6px;
    }
    .cat-soft-modal .money-group .form-control { border-left: 0; padding-left: 4px; }

    .cat-soft-modal .tz-switch-box {
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 4px; padding: 6px 9px;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .cat-soft-modal .tz-switch-box .sw-title { font-size: 8.5px; font-weight: 600; color: var(--ink-2); }
    .cat-soft-modal .tz-switch-box .sw-desc  { font-size: 7px; color: var(--ink-4); }
    .cat-soft-modal .form-check-input { margin-top: 0; }

    .cat-soft-modal .btn-cancel-soft {
        background: #fff; color: var(--ink-3);
        border: 1px solid var(--grid-2); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .cat-soft-modal .btn-cancel-soft:hover { color: var(--ink-2); background: #fafcfe; }
    .cat-soft-modal .btn-save-soft {
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .cat-soft-modal .btn-save-soft:hover { background: #e3f1e9; color: #34684e; }
</style>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CATÁLOGO · PANEL PRINCIPAL (simétrico al panel de tarifas)       --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="cat-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-houses me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $modoGlobal ? 'Visión Global del Catálogo' : 'Ficha Visual del Inmueble' }}
        </h6>

        @if ($modoGlobal)
            <button type="button"
                    class="btn btn-sm"
                    style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                    data-bs-toggle="modal" data-bs-target="#modalCrearInmueble">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Inmueble
            </button>
        @else
            <span class="rsv-chip tipo" style="font-size: 7.5px;">
                <i class="bi bi-fingerprint"></i>EXPEDIENTE #{{ $inmueble->id }}
            </span>
        @endif

    </div>


    {{-- El wrapper cambia de clase en individual para ampliar carrusel y tipografía --}}
    <div class="row g-2 {{ $modoGlobal ? '' : 'cat-single' }}">

        @forelse($propiedadesCollection as $prop)

            @php
                $tipoInm  = optional($prop->tipoInmueble)->nombre;
                $precio   = $precioPrincipalDe($prop);

                // Galería: del controlador ya viene ordenada (portada primero)
                $galeriaGlobal = $prop->relationLoaded('multimedia')
                    ? $prop->multimedia
                    : $prop->multimedia()->orderBy('es_portada', 'desc')->orderBy('orden')->get();

                // Conteos (individual: conCount del controlador · global: colección)
                $nFotos = $galeriaGlobal->count();
                $nRes   = $prop->reservas_count ?? 0;
                $nBloq  = $prop->bloqueos_calendario_count ?? 0;
            @endphp

            {{-- Individual: tarjeta más ancha para aprovechar la ficha --}}
            <div class="col-12 {{ $modoGlobal ? 'col-md-6 col-xl-4' : 'col-lg-8' }}">

                <div class="cat-card">

                    {{-- Carrusel de Fotos del Inmueble --}}
                    {{-- data-bs-interval = velocidad del autoplay (5s) · data-bs-ride="carousel" arranca solo --}}
                    <div id="carouselInmuebleGlobal{{ $prop->id }}"
                         class="carousel slide cat-carousel"
                         data-bs-ride="carousel"
                         data-bs-interval="5000">

                        <div class="carousel-inner">
                            @forelse($galeriaGlobal as $index => $media)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-bs-interval="5000">
                                    <img src="{{ $media->url_archivo }}" alt="Foto inmueble">
                                    @if($media->es_portada)
                                        <span class="portada-badge">PORTADA</span>
                                    @endif
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <div class="cat-empty-media">
                                        <i class="bi bi-image me-1"></i> Sin fotos en galería
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if($galeriaGlobal->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif

                    </div>


                    {{-- Cuerpo de la Tarjeta --}}
                    <div class="cat-body">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div class="cat-title" title="{{ $prop->name }}">{{ $prop->name }}</div>

                            {{-- Precio real: Mín. Reserva de la última tarifa --}}
                            @if(!is_null($precio))
                                <span class="price-chip" title="Mínimo de reserva · última tarifa">
                                    ${{ number_format($precio, 0) }}<small>mín. res.</small>
                                </span>
                            @endif
                        </div>

                        {{-- Chips: tipo + estado (mismo lenguaje que tarifas) --}}
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                            @if($tipoInm)
                                <span class="rsv-chip tipo" title="Tipo de inmueble">
                                    <i class="bi bi-tag-fill"></i>{{ $tipoInm }}
                                </span>
                            @endif

                            @if(isset($prop->active))
                                @if($prop->active)
                                    <span class="rsv-chip ok"><i class="bi bi-check-lg"></i>Activo</span>
                                @else
                                    <span class="rsv-chip off"><i class="bi bi-dash-lg"></i>Inactivo</span>
                                @endif
                            @endif
                        </div>

                        <div class="cat-meta mb-0">
                            <i class="bi bi-geo-alt me-1"></i>{{ $prop->city ?? 'Sin ciudad' }}
                            @isset($prop->capacidad_maxima)
                                <span class="sep">|</span>
                                <i class="bi bi-people me-1"></i>{{ $prop->capacidad_maxima }} máx.
                            @endisset
                            {{-- Dirección solo en individual (hay más espacio) --}}
                            @if(!$modoGlobal && !empty($prop->ubicacion))
                                <span class="sep">|</span>
                                <i class="bi bi-signpost-2 me-1"></i>{{ $prop->ubicacion }}
                            @endif
                        </div>

                        {{-- Stats solo en individual (datos de las relaciones) --}}
                        @if(!$modoGlobal)
                            <div class="cat-stats">
                                <div class="cat-stat">
                                    <span class="lbl">Fotos</span>
                                    <span class="val blue">{{ $nFotos }}</span>
                                </div>
                                <div class="cat-stat">
                                    <span class="lbl">Reservas</span>
                                    <span class="val ok">{{ $nRes }}</span>
                                </div>
                                <div class="cat-stat">
                                    <span class="lbl">Bloqueos</span>
                                    <span class="val tint">{{ $nBloq }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="cat-foot">
                            @if($modoGlobal)
                                {{-- GLOBAL: navegar al expediente --}}
                                <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                   class="btn-manage">
                                    <i class="bi bi-pencil-square"></i> Gestionar Propiedad
                                </a>
                            @else
                                {{-- INDIVIDUAL: modal PROPIO de este partial (mismo tab-pane, siempre visible) --}}
                                <button type="button" class="btn-manage"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarInmuebleFicha">
                                    <i class="bi bi-pencil-square"></i> Editar Propiedad
                                </button>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                <div class="cat-empty">
                    <i class="bi bi-house-exclamation"></i>
                    No hay inmuebles registrados en el catálogo.
                </div>
            </div>

        @endforelse

    </div>


    {{-- Paginación (solo global) --}}
    @if($modoGlobal && isset($inmueblesGrid) && method_exists($inmueblesGrid, 'links'))
        <div class="mt-3" style="font-size: 8.5px;">
            {{ $inmueblesGrid->appends(['inmueble_id' => 'todos'])->links() }}
        </div>
    @endif

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPT · CARRUSEL: autoplay garantizado + re-init al mostrar tabs --}}
{{-- El data-bs-ride de Bootstrap solo inicializa al cargar la página;  --}}
{{-- si este partial está en una tab oculta o llega por AJAX, nunca     --}}
{{-- arranca. Este init explícito lo garantiza SIEMPRE.                 --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
    (function () {
        var CAT_INTERVAL = 5000; /* velocidad del autoplay en ms */

        function initCatCarousels(root) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Carousel) return;
            var scope = (root && root.querySelectorAll) ? root : document;
            scope.querySelectorAll('.cat-carousel:not([data-cat-carousel-init])').forEach(function (el) {
                el.setAttribute('data-cat-carousel-init', '1');
                var cfg = { interval: CAT_INTERVAL, ride: 'carousel', pause: 'hover', wrap: true, touch: true };
                try {
                    /* getInstance+new (compat 5.0/5.1) con fallback a getOrCreateInstance (5.2+) */
                    var inst = bootstrap.Carousel.getInstance(el);
                    if (!inst && typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                        inst = bootstrap.Carousel.getOrCreateInstance(el, cfg);
                    } else if (!inst) {
                        inst = new bootstrap.Carousel(el, cfg);
                    }
                    /* Si ya existía instancia pero estaba detenida, reanudar autoplay */
                    if (inst && inst.cycle && el.closest('.tab-pane.show, .tab-pane.active, body')) {
                        inst.cycle();
                    }
                } catch (e) { /* silencioso: nunca romper la página */ }
            });
        }

        document.addEventListener('DOMContentLoaded', function () { initCatCarousels(); });

        /* Si el partial vive en tabs: al mostrarse una tab, inicializar carruseles dentro de ella */
        document.addEventListener('shown.bs.tab', function (e) {
            var sel = e.target && e.target.getAttribute && e.target.getAttribute('data-bs-target');
            var pane = sel ? document.querySelector(sel) : null;
            if (pane) initCatCarousels(pane);
        });

        /* Gancho global por si el dashboard inyecta este partial vía AJAX */
        window.initCatCarousels = initCatCarousels;
    })();
</script>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · NUEVO INMUEBLE (solo se usa en global)                   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade cat-soft-modal" id="modalCrearInmueble" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo de Inmuebles
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Nuevo Inmueble</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.inmuebles.store') }}" method="POST">
                @csrf

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Identificación</div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del inmueble <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" placeholder="Ej. Apartamento Vista al Mar" required>
                            <div class="tz-hint">Se mostrará en el catálogo público</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Tipo de inmueble</label>
                            <select class="form-select form-select-sm" name="tipo_inmueble_id">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt->where('active', 1) as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="tz-hint">Clasificación: Apartamento, Casa, Finca…</div>
                        </div>
                    </div>


                    {{-- 2 · UBICACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-geo-alt"></i> Ubicación</div>

                        <div class="mb-2">
                            <label class="form-label">Ciudad <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="city" placeholder="Ej. Bogotá" required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Dirección / Ubicación física</label>
                            <input type="text" class="form-control form-control-sm" name="ubicacion" placeholder="Ej. Calle 100 # 15-20">
                        </div>
                    </div>


                    {{-- 3 · CAPACIDAD --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-people"></i> Capacidad</div>

                        <div class="mb-0">
                            <label class="form-label">Capacidad máx.</label>
                            <input type="number" class="form-control form-control-sm" name="capacidad_maxima" placeholder="Ej. 6" min="1">
                            <div class="tz-hint">Huéspedes permitidos</div>
                        </div>
                    </div>


                    {{-- 4 · ESTADO --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-toggle-on"></i> Estado</div>

                        <div class="tz-switch-box">
                            <div class="text-start">
                                <div class="sw-title">Activo</div>
                                <div class="sw-desc">Visible en el catálogo público</div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="newInmActive" checked>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Podrás agregar fotos y tarifas al gestionarlo</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
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
<div class="modal fade cat-soft-modal" id="modalEditarInmuebleFicha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Ficha Visual · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">{{ $inmueble->name }}</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los cambios se reflejan en el catálogo</small>
                </div>
            </div>

            <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Identificación</div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del inmueble <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $inmueble->name) }}" required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Tipo de inmueble</label>
                            <select class="form-select form-select-sm" name="tipo_inmueble_id">
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
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-geo-alt"></i> Ubicación</div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control form-control-sm" name="city" value="{{ old('city', $inmueble->city) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control form-control-sm" name="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}">
                            </div>
                        </div>
                    </div>


                    {{-- 3 · CAPACIDAD Y ESTADO --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-sliders"></i> Capacidad y Estado</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm" name="capacidad_maxima" value="{{ old('capacidad_maxima', $inmueble->capacidad_maxima) }}" min="1">
                                <div class="tz-hint">Huéspedes</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label">Visibilidad</label>
                                <div class="tz-switch-box">
                                    <div class="text-start">
                                        <div class="sw-title">Activo</div>
                                        <div class="sw-desc">Visible en catálogo público</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="editActiveInmuebleFicha" {{ old('active', $inmueble->active) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">EXPEDIENTE #{{ $inmueble->id }}</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
@endif
