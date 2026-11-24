{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php · CAT-v3.8-DBG · NIVEL 3 (catálogo + Modal Google Maps/Waze) --}}
@php
    $modoGlobal  = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $soloLectura = !empty($soloLectura);

    $esActivoProp = function ($prop): bool {
        if (!is_object($prop)) return false;
        if (isset($prop->active)) return (bool) $prop->active;
        if (isset($prop->activo)) return (bool) $prop->activo;
        return false;
    };

    if (!isset($propiedadesCollection)) {
        $propiedadesCollection = $modoGlobal
            ? ($inmuebles ?? $inmueblesGrid ?? collect([]))
            : collect([$inmueble]);
    }

    if (!isset($tiposInmueblesOpt)) {
        try {
            $tiposInmueblesOpt = \App\Models\Rsv\TipoInmueble::orderBy('nombre')->get();
        } catch (\Throwable $e) {
            $tiposInmueblesOpt = collect([]);
        }
    }

    $itemsCol = ($propiedadesCollection instanceof \Illuminate\Pagination\AbstractPaginator)
        ? $propiedadesCollection->getCollection()
        : collect($propiedadesCollection);

    $cntTodosPag   = $itemsCol->count();
    $cntActivosPag = $itemsCol->filter($esActivoProp)->count();
    $cntInactPag   = max(0, $cntTodosPag - $cntActivosPag);

    $totalGlobalSistema = method_exists($propiedadesCollection, 'total')
        ? (int) $propiedadesCollection->total()
        : $cntTodosPag;

    $catDbg = [
        'marca'       => 'CAT-v3.8-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.catalogo',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? request('inmueble_id') ?? null),
        'pagina'      => method_exists($propiedadesCollection, 'currentPage') ? $propiedadesCollection->currentPage() : 1,
        'total'       => $totalGlobalSistema,
        'activos_pag' => $cntActivosPag,
        'inact_pag'   => $cntInactPag,
        'tiposOpt'    => $tiposInmueblesOpt->count(),
        'soloLectura' => $soloLectura,
        'validacion'  => (isset($errors) && $errors->any()) ? $errors->all() : [],
        'errores'     => [],
    ];

    $rutaUpdateBase = \Illuminate\Support\Facades\Route::has('rsv.inmuebles.update')
        ? route('rsv.inmuebles.update', '__ID__')
        : url('/rsv/inmuebles/__ID__');
@endphp

{{-- CAT-v3.8-DBG · Tab DBG textual + LED + Panel LOCAL --}}
<div id="catDbgToggle" title="Debug Catálogo (CAT-v3.8-DBG)">
    <span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span>
</div>
<div id="catDbgPanel" role="dialog" aria-label="Panel de debug de Catálogo">
    <div class="cat-dbg-header" id="catDbgHeader">
        <span>DEBUG · Catálogo (N3) · CAT-v3.8-DBG</span>
        <span class="cat-dbg-controls">
            <button type="button" class="cat-dbg-btn" id="catDbgRefresh" title="Refrescar fragmento del catálogo">↻ Frag</button>
            <button type="button" class="cat-dbg-btn" id="catDbgCopy">Copiar</button>
            <button type="button" class="cat-dbg-btn" id="catDbgClear">Limpiar</button>
            <button type="button" class="cat-dbg-btn" id="catDbgClose">✕</button>
        </span>
    </div>
    <div id="catDbgBody"></div>
</div>

<div id="catRoot"
     data-frag="{{ route('rsv.admin.inmuebles.catalogo.fragmento') }}"
     data-update-tpl="{{ $rutaUpdateBase }}"
     data-modo="{{ $modoGlobal ? 'global' : 'individual' }}"
     data-total="{{ $catDbg['total'] }}">

    <style>
        #catRoot .cat-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        #catRoot .cat-chips-scroll {
            display: flex;
            align-items: center;
            gap: 4px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            padding-bottom: 1px;
        }
        #catRoot .cat-chips-scroll::-webkit-scrollbar { display: none; }

        #catRoot .cat-filter-chip {
            border: 1px solid #e9eef4;
            background: #fff;
            color: #64748b;
            font-size: 8px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s ease;
            line-height: 1.3;
        }
        #catRoot .cat-filter-chip:hover { color: #334155; background: #f8fafc; }
        #catRoot .cat-filter-chip.active {
            background: #f0f7f2;
            color: #3d7a5c;
            border-color: #d5e7dc;
            font-weight: 700;
        }

        #catRoot .cat-controls-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        #catRoot .cat-search-inp,
        #catRoot .cat-tipo-sel {
            font-size: 8.5px;
            color: #334155;
            background: #fafbfd;
            border: 1px solid #e9eef4;
            border-radius: 5px;
            padding: 4px 9px;
            outline: 0;
            transition: all .15s ease;
        }
        #catRoot .cat-search-inp { width: 175px; }
        #catRoot .cat-search-inp:focus,
        #catRoot .cat-tipo-sel:focus {
            background: #fff;
            border-color: #3d7a5c;
        }
        #catRoot .cat-search-inp:focus { width: 215px; }

        #catRoot .cat-btn-mapa {
            gap: 4px;
            background: #fff;
            color: #475569;
            border: 1px solid #e9eef4 !important;
            font-family: inherit;
            font-size: 8.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 5px;
            white-space: nowrap;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-mapa:hover { background: #fafbfd; color: #334155; border-color: #cbd5e1 !important; }

        #catRoot .cat-btn-nuevo {
            gap: 4px;
            background: #3d7a5c;
            color: #fff;
            border: 1px solid #3d7a5c !important;
            font-family: inherit;
            font-size: 8.5px;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 5px;
            white-space: nowrap;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-nuevo:hover { background: #2f6148; color: #fff; }

        #catRoot .cat-card-item {
            border-color: #e9eef4 !important;
            border-radius: 8px;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        #catRoot .cat-card-item:hover {
            border-color: #d5e7dc !important;
            box-shadow: 0 5px 14px rgba(15,23,42,.06);
            transform: translateY(-1px);
        }
        #catRoot .cat-btn-main {
            gap: 5px;
            background: #f0f7f2;
            color: #3d7a5c;
            border: 1px solid #d5e7dc !important;
            border-radius: 5px;
            font-family: inherit;
            font-size: 8.5px;
            font-weight: 700;
            padding: 5px 10px;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-main:hover {
            background: #3d7a5c;
            color: #fff;
            border-color: #3d7a5c !important;
        }
        #catRoot .cat-btn-quick {
            background: #fafbfd;
            color: #475569;
            border: 1px solid #e9eef4 !important;
            border-radius: 5px;
            font-size: 8.5px;
            font-weight: 600;
            padding: 5px 9px;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-quick:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe !important;
        }
        #catRoot .cat-btn-pin {
            background: #fff;
            color: #64748b;
            border: 1px solid #e9eef4 !important;
            border-radius: 5px;
            font-size: 8.5px;
            font-weight: 600;
            padding: 5px 9px;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-pin:hover {
            background: #fafbfd;
            color: #334155;
            border-color: #cbd5e1 !important;
        }

        /* ═══ ESTILOS DEL MODAL DE MAPA (IDÉNTICO A BUSCADOR CLIENTE · WAZE / GMAPS) ═══ */
        .cat-map-container {
            position: relative;
            width: 100%;
            padding-top: 68%;
            background: #f1f5f9;
        }
        .cat-map-iframe {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border: 0;
        }
        .btn-waze {
            background-color: #e4f4fb;
            color: #007bb6;
            border: 1px solid #bce1f1;
            transition: all 0.2s;
        }
        .btn-waze:hover {
            background-color: #007bb6;
            color: #fff;
        }
        .btn-gmaps {
            background-color: #fce8e6;
            color: #d93025;
            border: 1px solid #f6c8c4;
            transition: all 0.2s;
        }
        .btn-gmaps:hover {
            background-color: #d93025;
            color: #fff;
        }

        /* ═══ ADAPTACIÓN RESPONSIVE PARA CELULAR (< 576px) ═══ */
        @media (max-width: 575.98px) {
            #catRoot .cat-panel-wrap { padding: 10px !important; }
            #catRoot .cat-toolbar { flex-direction: column; align-items: stretch; }
            #catRoot .cat-controls-right { width: 100%; }
            #catRoot .cat-search-inp {
                width: 100% !important;
                flex: 1 1 100%;
                font-size: 13px !important;
                padding: 7px 10px;
            }
            #catRoot .cat-tipo-sel {
                flex: 1 1 auto;
                font-size: 12px !important;
                padding: 6px 8px;
            }
            #catRoot .cat-btn-nuevo,
            #catRoot .cat-btn-mapa {
                flex: 1 1 auto;
                justify-content: center;
                font-size: 11px !important;
                padding: 7px 12px;
            }
            #catRoot .cat-filter-chip {
                font-size: 10px;
                padding: 5px 10px;
            }
            #catRoot .cat-carousel-box { height: 185px !important; }
            #catRoot .cat-prop-title { font-size: 12px !important; }
            #catRoot .cat-prop-meta { font-size: 10px !important; }
            #catRoot .cat-btn-main,
            #catRoot .cat-btn-quick,
            #catRoot .cat-btn-pin {
                min-height: 34px;
                font-size: 10.5px !important;
                padding: 6px 11px;
            }
            .cat-map-container { padding-top: 85%; }
            #catRoot .modal input.form-control,
            #catRoot .modal select.form-select {
                font-size: 14px !important;
                padding: 7px 10px !important;
            }
        }
    </style>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- CATÁLOGO · PANEL PRINCIPAL                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="p-3 bg-white rounded-3 border cat-panel-wrap" style="border-color: #f1f5f9 !important;">

        {{-- CABECERA + BARRA DE FILTROS + BOTÓN MAPA --}}
        <div class="cat-toolbar mb-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">

            <div class="d-flex align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0" style="font-size: 10.5px; color: #334155;">
                    <i class="bi bi-houses-fill text-success me-1"></i>
                    {{ $modoGlobal ? 'Catálogo de Propiedades' : 'Ficha Visual del Inmueble' }}
                    <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:rgba(2,6,23,.94); color:#38bdf8; border:1px solid #1e293b; vertical-align:middle; font-family:ui-monospace,monospace;">CAT-v3.8-DBG</span>
                </h6>

                @if ($modoGlobal && $cntTodosPag > 0)
                    <div class="cat-chips-scroll">
                        <button type="button" class="cat-filter-chip active" data-cat-filter="all">
                            Todos ({{ $cntTodosPag }})
                        </button>
                        <button type="button" class="cat-filter-chip" data-cat-filter="1">
                            Activos ({{ $cntActivosPag }})
                        </button>
                        <button type="button" class="cat-filter-chip" data-cat-filter="0">
                            Inactivos ({{ $cntInactPag }})
                        </button>
                    </div>
                @endif
            </div>

            <div class="cat-controls-right">
                @if ($modoGlobal)
                    @if ($cntTodosPag > 0)
                        <input type="text" id="catSearchInput" class="cat-search-inp" placeholder="Buscar nombre, ciudad, dirección…">

                        @if($tiposInmueblesOpt->isNotEmpty())
                            <select id="catTipoFilter" class="cat-tipo-sel" title="Filtrar por tipo de propiedad">
                                <option value="">Todos los tipos</option>
                                @foreach($tiposInmueblesOpt as $tOpt)
                                    <option value="{{ $tOpt->id }}">{{ $tOpt->nombre }}</option>
                                @endforeach
                            </select>
                        @endif

                        @php $firstInm = $itemsCol->first(); @endphp
                        @if($firstInm)
                            <button type="button"
                                    class="btn btn-sm d-inline-flex align-items-center border cat-btn-mapa js-cat-open-mapa"
                                    data-inm-id="{{ $firstInm->id }}"
                                    data-inm-name="{{ $firstInm->name }}"
                                    data-inm-city="{{ $firstInm->city ?? '' }}"
                                    data-inm-ubic="{{ $firstInm->ubicacion ?? '' }}"
                                    data-inm-query="{{ urlencode($firstInm->ubicacion ?? $firstInm->city ?? $firstInm->name) }}"
                                    data-bs-toggle="modal" data-bs-target="#modalMapaCatalogo">
                                <i class="bi bi-map text-danger"></i> Mapa
                            </button>
                        @endif
                    @endif

                    @if (!$soloLectura)
                        <button type="button"
                                class="btn btn-sm d-inline-flex align-items-center border cat-btn-nuevo js-cat-open-crear"
                                data-bs-toggle="modal" data-bs-target="#modalCrearInmueble">
                            <i class="bi bi-plus-lg"></i> Nuevo Inmueble
                        </button>
                    @endif
                @else
                    <button type="button"
                            class="btn btn-sm d-inline-flex align-items-center border cat-btn-mapa js-cat-open-mapa"
                            data-inm-id="{{ $inmueble->id }}"
                            data-inm-name="{{ $inmueble->name }}"
                            data-inm-city="{{ $inmueble->city ?? '' }}"
                            data-inm-ubic="{{ $inmueble->ubicacion ?? '' }}"
                            data-inm-query="{{ urlencode($inmueble->ubicacion ?? $inmueble->city ?? $inmueble->name) }}"
                            data-bs-toggle="modal" data-bs-target="#modalMapaCatalogo">
                        <i class="bi bi-map text-danger"></i> Mapa
                    </button>
                    <span class="d-inline-flex align-items-center border"
                          style="gap: 4px; font-size: 7.5px; font-weight: 700; padding: 2px 9px; border-radius: 8px; line-height: 14px; white-space: nowrap; color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe !important;">
                        <i class="bi bi-fingerprint"></i>EXPEDIENTE #{{ $inmueble->id }}
                    </span>
                @endif
            </div>

        </div>

        {{-- GRID DE TARJETAS --}}
        <div class="row g-2 g-sm-3" id="catCardsGrid">

            @forelse($propiedadesCollection as $prop)
                @php
                    $esActivo   = $esActivoProp($prop);
                    $tipoId     = (string) ($prop->tipo_inmueble_id ?? '');
                    $tipoNombre = optional($prop->tipoInmueble)->nombre ?? '';
                    $fotosCount = $prop->multimedia ? $prop->multimedia->count() : 0;
                    $resCount   = (int) ($prop->reservas_count ?? 0);
                    $blqCount   = (int) ($prop->bloqueos_calendario_count ?? 0);
                    $mapQuery   = urlencode($prop->ubicacion ?? $prop->city ?? $prop->name);
                    $searchBlob = mb_strtolower(($prop->name ?? '') . ' ' . ($prop->city ?? '') . ' ' . ($prop->ubicacion ?? '') . ' ' . $tipoNombre . ' #' . $prop->id);
                @endphp

                <div class="col-12 col-sm-6 {{ $modoGlobal ? 'col-xl-4' : 'col-lg-8' }} js-cat-col"
                     data-cat-active="{{ $esActivo ? '1' : '0' }}"
                     data-cat-tipo="{{ $tipoId }}"
                     data-cat-search="{{ $searchBlob }}"
                     data-prop-id="{{ $prop->id }}">

                    <div class="card h-100 overflow-hidden d-flex flex-column border bg-white text-decoration-none cat-card-item">

                        {{-- Carrusel de Fotos del Inmueble --}}
                        <div id="carouselInmuebleGlobal{{ $prop->id }}"
                             class="carousel slide position-relative flex-shrink-0 border-bottom cat-carousel-box"
                             style="height: {{ $modoGlobal ? '160px' : '230px' }}; background: #f4f6f8; border-color: #f1f5f9 !important;"
                             data-bs-ride="carousel"
                             data-bs-interval="5000">

                            <div class="position-absolute d-flex justify-content-between align-items-center w-100 px-2" style="top: 7px; left: 0; z-index: 5; pointer-events: none;">
                                <span class="badge" style="font-size: 7px; font-weight: 700; background: rgba(15,23,42,.7); color: #fff; border-radius: 4px; padding: 2px 6px;">
                                    #{{ $prop->id }}
                                </span>
                                @if($fotosCount > 0)
                                    <span class="badge" style="font-size: 7px; font-weight: 600; background: rgba(15,23,42,.7); color: #fff; border-radius: 4px; padding: 2px 6px;">
                                        <i class="bi bi-images me-1"></i>{{ $fotosCount }}
                                    </span>
                                @endif
                            </div>

                            <div class="carousel-inner w-100 h-100">
                                @forelse($prop->multimedia ?? collect() as $index => $media)
                                    <div class="carousel-item w-100 h-100 {{ $index === 0 ? 'active' : '' }}" data-bs-interval="5000">
                                        <img src="{{ $media->url_archivo }}" alt="Foto {{ $prop->name }}" class="w-100 h-100" style="object-fit: cover; object-position: center;" loading="lazy">
                                        @if($media->es_portada)
                                            <span class="position-absolute bg-white border text-success"
                                                  style="bottom: 7px; left: 7px; z-index: 5; font-size: 7px; font-weight: 700; letter-spacing: .05em; border-color: #d5e7dc !important; border-radius: 4px; padding: 1px 7px; line-height: 14px;">PORTADA</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="carousel-item w-100 h-100 active">
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center fst-italic" style="background: #fafbfd; color: #94a3b8; font-size: 8.5px;">
                                            <i class="bi bi-image me-1"></i> Sin fotos en galería
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            @if($fotosCount > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="prev"
                                        style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 24px; height: 24px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.92); box-shadow: 0 2px 6px rgba(15,23,42,.18); opacity: .9; z-index: 5; left: 7px;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 11px; height: 11px; filter: invert(.4);"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="next"
                                        style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 24px; height: 24px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.92); box-shadow: 0 2px 6px rgba(15,23,42,.18); opacity: .9; z-index: 5; right: 7px;">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="width: 11px; height: 11px; filter: invert(.4);"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            @endif

                        </div>

                        {{-- Cuerpo de la Tarjeta --}}
                        <div class="card-body d-flex flex-column flex-grow-1" style="padding: 10px 12px;">

                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div class="text-truncate cat-prop-title" style="font-size: {{ $modoGlobal ? '10px' : '11.5px' }}; font-weight: 700; color: #334155;" title="{{ $prop->name }}">
                                    {{ $prop->name }}
                                </div>

                                @if(!is_null($prop->latestTarifa?->precio_minimo_reserva))
                                    <span class="d-inline-flex align-items-baseline flex-shrink-0 border"
                                          style="gap: 2px; font-size: 9px; font-weight: 700; color: #15803d; background: #f0fdf4; border-color: #bbf7d0 !important; border-radius: 4px; padding: 1px 7px; font-variant-numeric: tabular-nums;"
                                          title="Mínimo de reserva · última tarifa">
                                        ${{ number_format($prop->latestTarifa->precio_minimo_reserva, 0) }}
                                        <small style="font-size: 6.5px; font-weight: 600; color: #64748b;">mín.</small>
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                @if($prop->tipoInmueble)
                                    <span class="d-inline-flex align-items-center border"
                                          style="gap: 3px; font-size: 7.5px; font-weight: 700; padding: 1px 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe !important;">
                                        <i class="bi bi-tag-fill" style="font-size: 7px;"></i>{{ $prop->tipoInmueble->nombre }}
                                    </span>
                                @endif

                                @if($esActivo)
                                    <span class="d-inline-flex align-items-center border" style="gap: 3px; font-size: 7.5px; font-weight: 700; padding: 1px 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #15803d; background: #f0fdf4; border-color: #bbf7d0 !important;">
                                        <i class="bi bi-check-lg" style="font-size: 7px;"></i>Activo
                                    </span>
                                @else
                                    <span class="d-inline-flex align-items-center border" style="gap: 3px; font-size: 7.5px; font-weight: 700; padding: 1px 7px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #64748b; background: #f8fafc; border-color: #e2e8f0 !important;">
                                        <i class="bi bi-dash-lg" style="font-size: 7px;"></i>Inactivo
                                    </span>
                                @endif
                            </div>

                            <div class="text-truncate mb-0 cat-prop-meta" style="font-size: 8.5px; color: #64748b;">
                                <i class="bi bi-geo-alt me-1 text-success"></i>{{ $prop->city ?? 'Sin ciudad' }}
                                @isset($prop->capacidad_maxima)
                                    <span style="color: #cbd5e1; margin: 0 4px;">|</span>
                                    <i class="bi bi-people me-1"></i>{{ $prop->capacidad_maxima }} máx.
                                @endisset
                                @if(!empty($prop->ubicacion))
                                    <span style="color: #cbd5e1; margin: 0 4px;">|</span>
                                    <span title="{{ $prop->ubicacion }}">{{ $prop->ubicacion }}</span>
                                @endif
                            </div>

                            @if($modoGlobal)
                                <div class="d-flex align-items-center justify-content-between mt-2 px-2 py-1 rounded-2" style="background: #fafbfd; border: 1px solid #f1f5f9; font-size: 7.5px; color: #64748b;">
                                    <span><i class="bi bi-journal-check text-success me-1"></i><b>{{ $resCount }}</b> reserva(s)</span>
                                    <span><i class="bi bi-calendar-x text-primary me-1"></i><b>{{ $blqCount }}</b> bloqueo(s)</span>
                                    <span><i class="bi bi-images text-secondary me-1"></i><b>{{ $fotosCount }}</b> foto(s)</span>
                                </div>
                            @else
                                <div class="d-flex w-100" style="gap: 6px; margin-top: 8px;">
                                    <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 8px; gap: 1px;">
                                        <span style="font-size: 6.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Fotos</span>
                                        <span style="font-size: 11px; font-weight: 800; color: #1d4ed8;">{{ $fotosCount }}</span>
                                    </div>
                                    <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 8px; gap: 1px;">
                                        <span style="font-size: 6.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Reservas</span>
                                        <span style="font-size: 11px; font-weight: 800; color: #15803d;">{{ $resCount }}</span>
                                    </div>
                                    <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 8px; gap: 1px;">
                                        <span style="font-size: 6.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Bloqueos</span>
                                        <span style="font-size: 11px; font-weight: 800; color: #7c3aed;">{{ $blqCount }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Barra de acciones táctil --}}
                            <div class="mt-auto border-top" style="padding-top: 8px; margin-top: 9px !important; border-color: #f1f5f9 !important;">
                                @if($modoGlobal)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                           data-cat-gestionar="1"
                                           data-prop-id="{{ $prop->id }}"
                                           data-prop-name="{{ $prop->name }}"
                                           class="btn d-flex align-items-center justify-content-center text-decoration-none flex-grow-1 cat-btn-main">
                                            <i class="bi bi-folder2-open"></i> Gestionar
                                        </a>

                                        <button type="button"
                                                class="btn cat-btn-pin js-cat-open-mapa"
                                                title="Ver ubicación en Google Maps y Waze"
                                                data-inm-id="{{ $prop->id }}"
                                                data-inm-name="{{ $prop->name }}"
                                                data-inm-city="{{ $prop->city ?? '' }}"
                                                data-inm-ubic="{{ $prop->ubicacion ?? '' }}"
                                                data-inm-query="{{ $mapQuery }}"
                                                data-bs-toggle="modal" data-bs-target="#modalMapaCatalogo">
                                            <i class="bi bi-map"></i> Mapa
                                        </button>

                                        @if(!$soloLectura)
                                            <button type="button"
                                                    class="btn cat-btn-quick js-cat-quick-edit"
                                                    title="Edición rápida de datos básicos"
                                                    data-id="{{ $prop->id }}"
                                                    data-name="{{ $prop->name }}"
                                                    data-tipo="{{ $prop->tipo_inmueble_id ?? '' }}"
                                                    data-city="{{ $prop->city ?? '' }}"
                                                    data-ubicacion="{{ $prop->ubicacion ?? '' }}"
                                                    data-capacidad="{{ $prop->capacidad_maxima ?? 6 }}"
                                                    data-active="{{ $esActivo ? '1' : '0' }}">
                                                <i class="bi bi-sliders"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    @if(!$soloLectura)
                                        <button type="button" class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 cat-btn-main js-cat-open-editar-ficha"
                                                data-bs-toggle="modal" data-bs-target="#modalEditarInmuebleFicha">
                                            <i class="bi bi-pencil-square"></i> Editar Propiedad
                                        </button>
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center w-100" style="gap: 4px; font-size: 7.5px; color: #94a3b8; font-style: italic;">
                                            <i class="bi bi-eye"></i> Solo lectura (gestiona desde el expediente)
                                        </span>
                                    @endif
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">
                    <div class="border text-center" style="border-style: dashed !important; border-color: #e9eef4 !important; border-radius: 6px; background: #fafbfd; padding: 26px 16px; color: #94a3b8; font-size: 9px;">
                        <i class="bi bi-house-exclamation d-block mb-1" style="font-size: 18px; color: #cbd5e1;"></i>
                        No hay inmuebles registrados en el catálogo.
                    </div>
                </div>

            @endforelse

            <div class="col-12 d-none" id="catEmptyFilterMsg">
                <div class="border text-center" style="border-style: dashed !important; border-color: #e9eef4 !important; border-radius: 6px; background: #fafbfd; padding: 20px 14px; color: #94a3b8; font-size: 9px;">
                    <i class="bi bi-search d-block mb-1" style="font-size: 15px; color: #cbd5e1;"></i>
                    Ningún inmueble coincide con el filtro actual en esta página.
                </div>
            </div>

        </div>

        @if($modoGlobal && isset($propiedadesCollection) && method_exists($propiedadesCollection, 'links'))
            <div class="mt-3 d-flex justify-content-center" style="font-size: 8.5px;">
                {{ $propiedadesCollection->appends(['inmueble_id' => 'todos'])->links() }}
            </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL DE MAPA PREMIUM (ESTILO BUSCADOR CLIENTE · GMAPS + WAZE)  --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalMapaCatalogo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border: none; border-radius: 12px; overflow: hidden;">

                <div class="modal-header bg-light border-bottom-0 pb-2 pt-3 px-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold mb-0" style="color: #334155; font-size: 14px;">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Ubicación del Inmueble
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        @if($modoGlobal && $itemsCol->count() > 1)
                            <select id="catMapSwitchSelect" class="form-select form-select-sm shadow-none" style="font-size: 10px; max-width: 170px; border-color: #e2e8f0;" title="Cambiar propiedad en el mapa">
                                @foreach($itemsCol as $optMap)
                                    <option value="{{ $optMap->id }}"
                                            data-name="{{ $optMap->name }}"
                                            data-city="{{ $optMap->city ?? '' }}"
                                            data-ubic="{{ $optMap->ubicacion ?? '' }}"
                                            data-query="{{ urlencode($optMap->ubicacion ?? $optMap->city ?? $optMap->name) }}">
                                        {{ $optMap->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="cat-map-container">
                        <iframe id="catMapIframe" class="cat-map-iframe" src="about:blank" allowfullscreen loading="lazy"></iframe>
                    </div>
                    <div class="p-3 bg-white text-center">
                        <h6 class="fw-bold mb-1" id="catMapPropTitle" style="font-size: 13px; color: #334155;">—</h6>
                        <p class="text-muted mb-3" id="catMapPropAddress" style="font-size: 11px;">
                            <i class="bi bi-info-circle me-1"></i> —
                        </p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a id="catMapLinkGmaps" href="#" target="_blank" rel="noopener noreferrer"
                               class="btn btn-gmaps btn-sm px-3 py-2 fw-bold js-cat-map-ext"
                               data-provider="Google Maps"
                               style="font-size: 10px; border-radius: 6px;">
                                <i class="bi bi-google me-1"></i> Google Maps
                            </a>
                            <a id="catMapLinkWaze" href="#" target="_blank" rel="noopener noreferrer"
                               class="btn btn-waze btn-sm px-3 py-2 fw-bold js-cat-map-ext"
                               data-provider="Waze"
                               style="font-size: 10px; border-radius: 6px;">
                                <i class="bi bi-car-front-fill me-1"></i> Waze
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1 · NUEVO INMUEBLE                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(!$soloLectura)
    <div class="modal fade" id="modalCrearInmueble" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 8px; font-size: 9px; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 10px 14px;">
                    <div>
                        <div style="font-size: 7.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #3d7a5c;">
                            Catálogo de Inmuebles
                        </div>
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 11px; color: #334155;">Nuevo Inmueble</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <form action="{{ route('rsv.inmuebles.store') }}" method="POST" id="catFormNuevo" data-cat-form="crear">
                    @csrf

                    <div class="modal-body" style="padding: 12px 14px;">
                        <div class="cat-form-err" style="display: none; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 5px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" placeholder="Ej. Apartamento Vista al Mar" required style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt->where('active', 1) as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" placeholder="Ej. Santa Marta" required style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección / Ubicación</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" placeholder="Ej. Calle 100 # 15-20" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" value="6" min="1" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                            <div class="col-7">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 9px; gap: 8px;">
                                    <div class="text-start">
                                        <div style="font-size: 8.5px; font-weight: 700; color: #334155;">Activo</div>
                                        <div style="font-size: 7px; color: #94a3b8;">Visible en catálogo</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="active" value="0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="newInmActive" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                        <span class="text-muted" style="font-size: 7.5px;">Luego podrás subir fotos y tarifas</span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Guardar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2 · EDICIÓN RÁPIDA DESDE EL GRID GLOBAL                   --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($modoGlobal && !$soloLectura)
    <div class="modal fade" id="modalQuickEditInmueble" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 8px; font-size: 9px; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 10px 14px;">
                    <div>
                        <div style="font-size: 7.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #1d4ed8;">
                            Edición Rápida · Catálogo
                        </div>
                        <h6 class="modal-title mb-0 fw-bold" id="catQuickEditTitle" style="font-size: 11px; color: #334155;">Editar Inmueble</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <form action="" method="POST" id="catFormQuickEditar" data-cat-form="editar-rapido">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">
                        <div class="cat-form-err" style="display: none; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 5px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" id="catQeName" required style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" id="catQeTipo" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}{{ $tipoOpt->active ? '' : ' (inactivo)' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" id="catQeCity" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" id="catQeUbicacion" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" id="catQeCapacidad" min="1" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                            <div class="col-7">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 9px; gap: 8px;">
                                    <div class="text-start">
                                        <div style="font-size: 8.5px; font-weight: 700; color: #334155;">Activo</div>
                                        <div style="font-size: 7px; color: #94a3b8;">Visible en catálogo</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="active" value="0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="catQeActive">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                        <span class="text-muted" id="catQuickEditFootId" style="font-size: 7.5px;">EXPEDIENTE</span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3 · EDITAR INMUEBLE (ficha individual)                    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(!$modoGlobal && !$soloLectura)
    <div class="modal fade" id="modalEditarInmuebleFicha" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 8px; font-size: 9px; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 10px 14px;">
                    <div>
                        <div style="font-size: 7.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #3d7a5c;">
                            Ficha Visual · Editando
                        </div>
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 11px; color: #334155;">{{ $inmueble->name }}</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST" id="catFormEditar" data-cat-form="editar">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">
                        <div class="cat-form-err" style="display: none; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 5px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" value="{{ old('name', $inmueble->name) }}" required style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}" {{ $inmueble->tipo_inmueble_id == $tipoOpt->id ? 'selected' : '' }}>
                                        {{ $tipoOpt->nombre }}{{ $tipoOpt->active ? '' : ' (inactivo)' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" value="{{ old('city', $inmueble->city) }}" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" value="{{ old('capacidad_maxima', $inmueble->capacidad_maxima) }}" min="1" style="font-size: 9.5px; color: #334155; border-color: #e9eef4 !important; border-radius: 4px; padding: 4px 8px;">
                            </div>

                            <div class="col-7">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 5px; padding: 6px 9px; gap: 8px;">
                                    <div class="text-start">
                                        <div style="font-size: 8.5px; font-weight: 700; color: #334155;">Activo</div>
                                        <div style="font-size: 7px; color: #94a3b8;">Visible en catálogo</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="active" value="0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="editActiveInmuebleFicha" {{ old('active', $inmueble->active) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                        <span class="text-muted" style="font-size: 7.5px;">EXPEDIENTE #{{ $inmueble->id }}</span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endif

    <script>
    /* ═══════════════ CAT-v3.8-DBG · catalogo + Modal Google Maps/Waze · NIVEL 3 ═══════════════ */
    (function () {
        'use strict';
        var root = document.getElementById('catRoot');
        if (!root) { return; }

        var FRAG       = root.getAttribute('data-frag') || '';
        var UPDATE_TPL = root.getAttribute('data-update-tpl') || '';
        var SRVDBG     = @json($catDbg);

        var DBG = (window.CatDbg && window.CatDbg.alive && window.CatDbg.alive())
            ? window.CatDbg
            : (function () {
                var MAX = 300, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('catDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'catDbgStyle';
                    st.textContent = [
                        '#catDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#catDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.cat-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.cat-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:catDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes catDbgBlink{50%{opacity:.35;}}',
                        '.cat-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;}',
                        '#catDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:390px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.cat-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.cat-dbg-controls{display:flex;gap:4px;}',
                        '.cat-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.cat-dbg-btn:hover{background:#475569;}',
                        '#catDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.cat-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.cat-dbg-time{color:#64748b;margin-right:4px;}',
                        '.cat-dbg-info{color:#93c5fd;}.cat-dbg-ok{color:#86efac;}',
                        '.cat-dbg-warn{color:#fde68a;}.cat-dbg-err{color:#fca5a5;}',
                        '.cat-dbg-net{color:#c4b5fd;}.cat-dbg-debug{color:#94a3b8;}.cat-dbg-ev{color:#f9a8d4;}',
                        '#catToastWrap{position:fixed;left:16px;bottom:16px;z-index:1092;display:flex;',
                        'flex-direction:column;gap:6px;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    var rootDbg = document.getElementById('catRoot');
                    if (!rootDbg) { return; }
                    if (!$('catDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'catDbgToggle';
                        t.title = 'Debug Catálogo (CAT-v3.8-DBG)';
                        t.innerHTML = '<span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('catDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'catDbgPanel';
                        p.innerHTML = '<div class="cat-dbg-header" id="catDbgHeader">' +
                            '<span>DEBUG · Catálogo (N3) · CAT-v3.8-DBG</span>' +
                            '<span class="cat-dbg-controls">' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgRefresh">↻ Frag</button>' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgCopy">Copiar</button>' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgClear">Limpiar</button>' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgClose">✕</button>' +
                            '</span></div><div id="catDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle', 'psDbgToggle', 'trxDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; }
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('catDbgToggle'), p = $('catDbgPanel');
                    if (t) { t.style.bottom = '0px'; }
                    if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; }
                }
                function hora() {
                    var d = new Date();
                    function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                    return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
                }
                var api = {
                    log: function (txt, nivel) {
                        var b = $('catDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'cat-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="cat-dbg-time">[' + hora() + ']</span>' +
                            '<span class="cat-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('catDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('catDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('catDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('catDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('catDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('catDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('catDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('catDbgBody'); if (b) { b.innerHTML = ''; } },
                    copy: function () {
                        var b = $('catDbgBody'); if (!b) { return; }
                        var txt = '=== DEBUG catalogo CAT-v3.8-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('Bitácora copiada al portapapeles', 'ok');
                            });
                        }
                    },
                    ensure: function () { asegurarNodos(); },
                    alive: function () { return !!$('catDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                window.CatDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure();

        function limpiarDbgDuplicados() {
            var ts = document.querySelectorAll('#catDbgToggle');
            for (var i = 1; i < ts.length; i++) ts[i].remove();
            var ps = document.querySelectorAll('#catDbgPanel');
            for (var j = 1; j < ps.length; j++) ps[j].remove();
        }
        limpiarDbgDuplicados();

        /* ═══ ACTUALIZAR MODAL DE MAPA (GOOGLE MAPS EMBED + WAZE) ═══ */
        function actualizarContenidoModalMapa(inmId, name, city, ubic, queryEncoded) {
            var qEnc = queryEncoded || encodeURIComponent(ubic || city || name || 'Colombia');
            var iframeEl = document.getElementById('catMapIframe');
            var titleEl  = document.getElementById('catMapPropTitle');
            var addrEl   = document.getElementById('catMapPropAddress');
            var gmapsEl  = document.getElementById('catMapLinkGmaps');
            var wazeEl   = document.getElementById('catMapLinkWaze');
            var selEl    = document.getElementById('catMapSwitchSelect');

            if (iframeEl) {
                iframeEl.src = 'https://maps.google.com/maps?q=' + qEnc + '&t=m&z=15&output=embed';
            }
            if (titleEl) {
                titleEl.textContent = name || 'Propiedad';
            }
            if (addrEl) {
                var dirTexto = [city, ubic].filter(Boolean).join(' — ') || 'Ubicación registrada en catálogo';
                addrEl.innerHTML = '<i class="bi bi-info-circle me-1"></i> ' + dirTexto;
            }
            if (gmapsEl) {
                gmapsEl.href = 'https://maps.google.com/?q=' + qEnc;
            }
            if (wazeEl) {
                wazeEl.href = 'https://waze.com/ul?q=' + qEnc;
            }
            if (selEl && inmId) {
                selEl.value = String(inmId);
            }
            DBG.log('Mapa cargado · "' + (name || '?') + '" (' + (city || 'Colombia') + ')', 'ev');
        }

        function aplicarFiltroCatalogo(logEvento) {
            var r = document.getElementById('catRoot');
            if (!r) return;
            var chipAct = r.querySelector('.cat-filter-chip.active');
            var filtroEstado = chipAct ? chipAct.getAttribute('data-cat-filter') : 'all';
            var filtroTipo   = document.getElementById('catTipoFilter')?.value || '';
            var q = (document.getElementById('catSearchInput')?.value || '').toLowerCase().trim();
            var visibles = 0;

            var cols = r.querySelectorAll('.js-cat-col');
            cols.forEach(function (col) {
                var st   = col.getAttribute('data-cat-active') || '1';
                var tp   = col.getAttribute('data-cat-tipo') || '';
                var blob = col.getAttribute('data-cat-search') || '';
                var okEstado = (filtroEstado === 'all') || (st === filtroEstado);
                var okTipo   = !filtroTipo || (tp === filtroTipo);
                var okTexto  = !q || blob.indexOf(q) !== -1;
                var mostrar  = okEstado && okTipo && okTexto;
                col.style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            });

            var emptyMsg = document.getElementById('catEmptyFilterMsg');
            if (emptyMsg && cols.length > 0) {
                emptyMsg.classList.toggle('d-none', visibles > 0);
            }

            if (logEvento) {
                DBG.log('Filtro · estado=' + filtroEstado + ' · tipo=' + (filtroTipo || 'todos') + ' · q="' + q + '" · visibles=' + visibles + '/' + cols.length, 'info');
            }
        }

        if (window.__catDbgDocHandler) { document.removeEventListener('click', window.__catDbgDocHandler); }
        window.__catDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }

            if (t.closest('#catDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#catDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#catDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#catDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#catDbgRefresh')) {
                e.stopPropagation();
                window.catReloadFragment('manual-btn-dbg');
                return;
            }

            // Botón Mapa (barra superior o tarjeta)
            var btnMapa = t.closest('.js-cat-open-mapa');
            if (btnMapa) {
                actualizarContenidoModalMapa(
                    btnMapa.getAttribute('data-inm-id') || '',
                    btnMapa.getAttribute('data-inm-name') || '',
                    btnMapa.getAttribute('data-inm-city') || '',
                    btnMapa.getAttribute('data-inm-ubic') || '',
                    btnMapa.getAttribute('data-inm-query') || ''
                );
                return;
            }

            // Enlaces externos Google Maps / Waze
            var extMap = t.closest('.js-cat-map-ext');
            if (extMap) {
                DBG.log('Abriendo ruta externa en ' + (extMap.getAttribute('data-provider') || 'GPS'), 'net');
                return;
            }

            var chip = t.closest('#catRoot .cat-filter-chip');
            if (chip) {
                document.querySelectorAll('#catRoot .cat-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltroCatalogo(true);
                return;
            }

            var qeBtn = t.closest('.js-cat-quick-edit');
            if (qeBtn) {
                e.preventDefault();
                var id   = qeBtn.getAttribute('data-id') || '';
                var name = qeBtn.getAttribute('data-name') || '';
                var tipo = qeBtn.getAttribute('data-tipo') || '';
                var city = qeBtn.getAttribute('data-city') || '';
                var ubic = qeBtn.getAttribute('data-ubicacion') || '';
                var cap  = qeBtn.getAttribute('data-capacidad') || '6';
                var act  = qeBtn.getAttribute('data-active') === '1';

                var formQe = document.getElementById('catFormQuickEditar');
                if (formQe && UPDATE_TPL) {
                    formQe.setAttribute('action', UPDATE_TPL.replace('__ID__', id));
                    var errBox = formQe.querySelector('.cat-form-err');
                    if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }
                }
                var elTitle = document.getElementById('catQuickEditTitle');
                if (elTitle) elTitle.textContent = name || ('Inmueble #' + id);
                var elFoot = document.getElementById('catQuickEditFootId');
                if (elFoot) elFoot.textContent = 'EXPEDIENTE #' + id;

                var iName = document.getElementById('catQeName'); if (iName) iName.value = name;
                var iTipo = document.getElementById('catQeTipo'); if (iTipo) iTipo.value = tipo;
                var iCity = document.getElementById('catQeCity'); if (iCity) iCity.value = city;
                var iUbic = document.getElementById('catQeUbicacion'); if (iUbic) iUbic.value = ubic;
                var iCap  = document.getElementById('catQeCapacidad'); if (iCap) iCap.value = cap;
                var iAct  = document.getElementById('catQeActive'); if (iAct) iAct.checked = act;

                var modalEl = document.getElementById('modalQuickEditInmueble');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            }
        };
        document.addEventListener('click', window.__catDbgDocHandler);

        if (window.__catSearchHandler) { document.removeEventListener('input', window.__catSearchHandler); }
        var catSearchTimer = null;
        window.__catSearchHandler = function (e) {
            if (e.target && e.target.id === 'catSearchInput') {
                aplicarFiltroCatalogo(false);
                clearTimeout(catSearchTimer);
                catSearchTimer = setTimeout(function () {
                    aplicarFiltroCatalogo(true);
                }, 320);
            }
        };
        document.addEventListener('input', window.__catSearchHandler);

        if (window.__catTipoChangeHandler) { document.removeEventListener('change', window.__catTipoChangeHandler); }
        window.__catTipoChangeHandler = function (e) {
            if (!e.target) return;
            if (e.target.id === 'catTipoFilter') {
                aplicarFiltroCatalogo(true);
                return;
            }
            if (e.target.id === 'catMapSwitchSelect') {
                var opt = e.target.options[e.target.selectedIndex];
                if (opt) {
                    actualizarContenidoModalMapa(
                        opt.value,
                        opt.getAttribute('data-name') || opt.textContent,
                        opt.getAttribute('data-city') || '',
                        opt.getAttribute('data-ubic') || '',
                        opt.getAttribute('data-query') || ''
                    );
                }
            }
        };
        document.addEventListener('change', window.__catTipoChangeHandler);

        DBG.log('CAT-v3.8-DBG activo · modal mapa Google Maps + Waze listo', 'ok');

        var CAT_INTERVAL = 5000;
        function initCatCarousels(scope) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Carousel) { return; }
            var sc = (scope && scope.querySelectorAll) ? scope : document;
            var lista = sc.querySelectorAll('.carousel:not([data-cat-carousel-init])');
            for (var i = 0; i < lista.length; i++) {
                var el = lista[i];
                el.setAttribute('data-cat-carousel-init', '1');
                var cfg = { interval: CAT_INTERVAL, ride: 'carousel', pause: 'hover', wrap: true, touch: true };
                try {
                    var inst = bootstrap.Carousel.getOrCreateInstance(el, cfg);
                    if (inst && inst.cycle && el.closest('.tab-pane.show, .tab-pane.active, body')) {
                        inst.cycle();
                    }
                } catch (e) {}
            }
        }
        initCatCarousels(root);

        function catRunScripts(scope) {
            var scripts = scope.querySelectorAll('script');
            for (var i = 0; i < scripts.length; i++) {
                var viejo = scripts[i];
                var nuevo = document.createElement('script');
                for (var j = 0; j < viejo.attributes.length; j++) {
                    nuevo.setAttribute(viejo.attributes[j].name, viejo.attributes[j].value);
                }
                nuevo.textContent = viejo.textContent;
                viejo.parentNode.replaceChild(nuevo, viejo);
            }
        }
        function extraerErrorLaravel(html) {
            var m = html.match(/<title[^>]*>([^<]+)<\/title>/i);
            if (m) { return m[1]; }
            m = html.match(/<h1[^>]*>([\s\S]{0,160}?)<\/h1>/i);
            if (m) { return m[1].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim(); }
            return '';
        }
        function escapar(txt) {
            return String(txt).replace(/[<>&]/g, function (c) {
                return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;');
            });
        }
        function cerrarModalesDelRoot() {
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) { return; }
            var abiertas = root.querySelectorAll('.modal.show');
            for (var i = 0; i < abiertas.length; i++) {
                try { bootstrap.Modal.getOrCreateInstance(abiertas[i]).hide(); } catch (e7) {}
            }
        }
        function limpiarResiduosModal() {
            if (document.querySelector('.modal.show')) { return; }
            var fondos = document.querySelectorAll('.modal-backdrop');
            for (var i = 0; i < fondos.length; i++) {
                if (fondos[i].parentNode) { fondos[i].parentNode.removeChild(fondos[i]); }
            }
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        function catSwapFragmento(html, urlBarra) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            var nuevo = tmp.querySelector('#catRoot');
            if (!nuevo) { throw new Error('el fragmento no trajo #catRoot'); }
            var actual = (root && root.isConnected) ? root : document.getElementById('catRoot');
            if (!actual) { throw new Error('#catRoot actual no está en el DOM'); }
            cerrarModalesDelRoot();
            actual.replaceWith(nuevo);
            root = nuevo;
            limpiarDbgDuplicados();
            catRunScripts(nuevo);
            limpiarResiduosModal();
            if (urlBarra) { try { window.history.replaceState({}, '', urlBarra); } catch (e6) {} }
        }
        function catCargar(url, urlBarra, motivo) {
            if (!FRAG) { window.location.href = url; return Promise.resolve(); }
            var t0 = performance.now();
            DBG.log('GET Fragmento (' + (motivo || 'reload') + ') → ' + url, 'net');
            return fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                if (!r.ok) {
                    return r.text().then(function (t) {
                        throw new Error('HTTP ' + r.status + ' (' + ms + 'ms) · ' + extraerErrorLaravel(t));
                    });
                }
                return r.text().then(function (html) { return { html: html, ms: ms }; });
            }).then(function (res) {
                catSwapFragmento(res.html, urlBarra);
                DBG.log('Catálogo actualizado (' + res.ms + 'ms)', 'ok');
            }).catch(function (err) {
                DBG.log('⚠ Recarga falló: ' + err.message, 'err');
            });
        }

        function recargarContexto(motivo) {
            if (!FRAG) { window.location.reload(); return Promise.resolve(); }
            var inm = (SRVDBG && SRVDBG.inmuebleId) ? SRVDBG.inmuebleId : 'todos';
            var pag = (SRVDBG && SRVDBG.pagina) ? SRVDBG.pagina : 1;
            var esFicha = (SRVDBG && SRVDBG.modo === 'individual');
            var partes = ['inmueble_id=' + encodeURIComponent(inm || 'todos')];
            if (!esFicha) { partes.push('page_inmuebles=' + encodeURIComponent(pag || 1)); }
            partes.push('_t=' + Date.now());
            return catCargar(FRAG + (FRAG.indexOf('?') === -1 ? '?' : '&') + partes.join('&'), null, motivo || 'contexto');
        }
        window.catReloadFragment = recargarContexto;

        if (window.__catPageHandler) { window.removeEventListener('click', window.__catPageHandler, true); }
        window.__catPageHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || !a.closest('#catRoot') || !a.closest('.pagination')) { return; }
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (!FRAG) { return; }
            var u, f;
            try { u = new URL(a.href, window.location.href); f = new URL(FRAG, window.location.href); } catch (e0) { return; }
            e.preventDefault();
            e.stopPropagation();

            var partes = [];
            u.searchParams.forEach(function (v, k) {
                partes.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
            });
            if (!u.searchParams.has('page_inmuebles')) { partes.push('page_inmuebles=1'); }
            catCargar(f.pathname + '?' + partes.join('&'), null, 'paginacion');
        };
        window.addEventListener('click', window.__catPageHandler, true);

        function catToast(txt, nivel) {
            var wrap = document.getElementById('catToastWrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'catToastWrap';
                document.body.appendChild(wrap);
            }
            var t = document.createElement('div');
            var colores = { ok: 'color:#86efac;border-color:#14532d;', err: 'color:#fca5a5;border-color:#7f1d1d;', info: 'color:#93c5fd;border-color:#1e3a8a;' };
            t.setAttribute('style', 'font-family:inherit;font-size:11px;padding:8px 12px;border-radius:8px;border:1px solid;background:#0f172a;box-shadow:0 6px 18px rgba(0,0,0,.25);max-width:340px;' + (colores[nivel] || colores.info));
            t.textContent = txt;
            wrap.appendChild(t);
            setTimeout(function () { if (t.parentNode) { t.parentNode.removeChild(t); } }, 3800);
        }

        if (window.__catFormHandler) { document.removeEventListener('submit', window.__catFormHandler); }
        window.__catFormHandler = function (e) {
            var form = e.target;
            if (!form || !form.closest || !form.closest('#catRoot') || !form.hasAttribute('data-cat-form')) { return; }
            e.preventDefault();
            var tipo = form.getAttribute('data-cat-form');
            var btn = form.querySelector('button[type="submit"]');
            var caja = form.querySelector('.cat-form-err');
            if (caja) { caja.style.display = 'none'; caja.innerHTML = ''; }
            if (btn) { btn.disabled = true; }

            var fd = new FormData(form);
            var t0 = performance.now();

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { status: r.status, ok: r.ok, ms: ms, json: j }; });
                }
                return r.text().then(function (t) { return { status: r.status, ok: r.ok, ms: ms, html: t }; });
            }).then(function (res) {
                if (res.status === 422) {
                    var errs = (res.json && res.json.errors) ? res.json.errors : {};
                    var msgs = [];
                    for (var k in errs) { if (Object.prototype.hasOwnProperty.call(errs, k)) { msgs = msgs.concat(errs[k]); } }
                    if (!msgs.length && res.json && res.json.message) { msgs.push(res.json.message); }
                    if (caja) {
                        caja.innerHTML = msgs.map(function (m) { return '<div>• ' + escapar(m) + '</div>'; }).join('');
                        caja.style.display = msgs.length ? 'block' : 'none';
                    }
                    catToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                catToast((tipo === 'crear' ? 'Inmueble creado' : 'Cambios guardados') + ' · actualizando…', 'ok');
                recargarContexto('post-' + tipo);
                try { document.dispatchEvent(new CustomEvent('rsv:catalogo-actualizado', { detail: { accion: tipo } })); } catch (evC) {}
            }).catch(function (err) {
                catToast('No se pudo completar la operación · ' + err.message, 'err');
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__catFormHandler);

        if (window.__catNavHandler) { document.removeEventListener('click', window.__catNavHandler, true); }
        window.__catNavHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || !a.closest('#catRoot') || !a.hasAttribute('data-cat-gestionar')) { return; }
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }

            var propId = a.getAttribute('data-prop-id') || '';
            if (a.closest('#showRoot') && window.__inmLinkHandler) {
                return;
            }
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (show && fragN2) {
                e.preventDefault();
                var url = fragN2 + (fragN2.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(propId || 'todos');
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
                    .then(r => r.text())
                    .then(html => {
                        var tmp = document.createElement('div');
                        tmp.innerHTML = html;
                        var nuevo = tmp.querySelector('#showRoot');
                        if (nuevo) {
                            show.replaceWith(nuevo);
                            catRunScripts(nuevo);
                        }
                    });
            }
        };
        document.addEventListener('click', window.__catNavHandler, true);
    })();
    </script>

</div>
