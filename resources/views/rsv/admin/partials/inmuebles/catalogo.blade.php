{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php --}}
{{-- ESTILO: sistema suave · título 9.5px · textos 8px · borde fino · pasteles --}}

<style>
    /* ═══ Catálogo · scoped ═══ */
    .cat-soft {
        --grid:  #f1f5f9;
        --grid-2:#e9eef4;
        --head:  #fafbfd;
        --ink:   #64748b;
        --ink-2: #475569;
        --ink-3: #94a3b8;
        --ink-4: #b3bcc7;
        --accent:#3d7a5c;
        --accent-bg:#f0f7f2;
        --accent-bd:#d5e7dc;
    }

    .cat-soft .cat-card {
        background: #fff;
        border: 1px solid var(--grid);
        border-radius: 5px;
        overflow: hidden;
        height: 100%;
        display: flex; flex-direction: column;
        transition: border-color .15s ease;
    }
    .cat-soft .cat-card:hover { border-color: var(--grid-2); }

    /* Carrusel */
    .cat-soft .cat-carousel {
        height: 170px;
        background: #f4f6f8;
        position: relative;
        flex-shrink: 0;
        border-bottom: 1px solid var(--grid);
    }
    .cat-soft .cat-carousel img {
        object-fit: cover; object-position: center;
        width: 100%; height: 100%;
    }
    .cat-soft .cat-carousel .carousel-inner,
    .cat-soft .cat-carousel .carousel-item { width: 100%; height: 100%; }

    .cat-soft .cat-carousel .carousel-control-prev,
    .cat-soft .cat-carousel .carousel-control-next {
        width: 26px; opacity: .25; transition: opacity .15s ease; z-index: 5;
    }
    .cat-soft .cat-carousel:hover .carousel-control-prev,
    .cat-soft .cat-carousel:hover .carousel-control-next { opacity: .55; }
    .cat-soft .cat-carousel .carousel-control-prev-icon,
    .cat-soft .cat-carousel .carousel-control-next-icon {
        width: 14px; height: 14px; filter: invert(.45);
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

    .cat-soft .cat-meta {
        font-size: 8px; color: var(--ink-3);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .cat-soft .cat-meta i { font-size: 8px; color: #c3ccd6; }
    .cat-soft .cat-meta .sep { color: #e2e8f0; margin: 0 4px; }

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
    }
    .cat-soft .btn-manage:hover { background: #e3f1e9; color: #34684e; text-decoration: none; }
    .cat-soft .btn-manage i { font-size: 9px; }

    /* Footer con separador fino */
    .cat-soft .cat-foot {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid var(--grid);
    }

    /* Empty state */
    .cat-soft .cat-empty {
        border: 1px dashed var(--grid-2);
        border-radius: 5px;
        background: var(--head);
        text-align: center; padding: 40px 16px;
        color: var(--ink-4); font-size: 8.5px;
    }
    .cat-soft .cat-empty i { font-size: 20px; color: #d4dae2; display: block; margin-bottom: 6px; }
</style>


<div class="cat-soft">

    <div class="row g-3">

        @php
            $propiedadesCollection = isset($inmueblesGrid) ? $inmueblesGrid : $listaInmuebles;
        @endphp

        @forelse($propiedadesCollection as $prop)

            <div class="col-12 col-md-6 col-xl-4">

                <div class="cat-card">

                    {{-- Carrusel de Fotos del Inmueble --}}
                    <div id="carouselInmuebleGlobal{{ $prop->id }}"
                         class="carousel slide cat-carousel"
                         data-bs-ride="carousel">

                        @php
                            $galeriaGlobal = $prop->relationLoaded('multimedia') ? $prop->multimedia : $prop->multimedia()->orderBy('es_portada', 'desc')->get();
                        @endphp

                        <div class="carousel-inner">
                            @forelse($galeriaGlobal as $index => $media)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
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

                            @isset($prop->precio_base_noche)
                                <span class="price-chip">
                                    ${{ number_format($prop->precio_base_noche, 0) }}<small>/noche</small>
                                </span>
                            @endisset
                        </div>

                        <div class="cat-meta mb-0">
                            <i class="bi bi-geo-alt me-1"></i>{{ $prop->city ?? 'Sin ciudad' }}
                            @isset($prop->capacidad_maxima)
                                <span class="sep">|</span>
                                <i class="bi bi-people me-1"></i>{{ $prop->capacidad_maxima }} máx.
                            @endisset
                        </div>

                        <div class="cat-foot">
                            <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                               class="btn-manage">
                                <i class="bi bi-pencil-square"></i> Gestionar Propiedad
                            </a>
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


    {{-- Paginación --}}
    @if(isset($inmueblesGrid) && method_exists($inmueblesGrid, 'links'))
        <div class="mt-3" style="font-size: 8.5px;">
            {{ $inmueblesGrid->appends(['inmueble_id' => 'todos'])->links() }}
        </div>
    @endif

</div>
