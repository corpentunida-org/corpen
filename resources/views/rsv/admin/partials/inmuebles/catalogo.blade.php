{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php · CAT-v3.0-DBG · NIVEL 3 (catálogo de inmuebles) --}}
{{-- ═══════════════════════════════════════════════════════════════════════════════════════════════════════════
     NOVEDADES CAT-v3.0-DBG:
     1) Filtro instantáneo en cliente (búsqueda por nombre/ciudad/tipo + chips Todos / Activos / Inactivos).
     2) Modal de Edición Rápida directamente desde el Grid Global (sin obligar a salir al expediente N2).
     3) Botón "↻ Frag" en la terminal DBG + método global window.catReloadFragment(motivo) con medición en ms.
     4) Telemetría completa de eventos de UI (filtros, modales, carruseles, paginación, guardado AJAX 422/200).
     5) Conserva cadena de respaldo (propiedadesCollection → inmuebles → inmueblesGrid), swap de #showRoot
        al pulsar "Gestionar Propiedad" y <input type="hidden" name="active" value="0"> en todos los switches.
     ═══════════════════════════════════════════════════════════════════════════════════════════════════════════ --}}

@php
    // ── CAT-v3.0-DBG · Red de seguridad de variables ──
    $modoGlobal = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $soloLectura = !empty($soloLectura);

    // ── Colector de contexto del servidor ──
    $catDbg = [
        'marca'       => 'CAT-v3.0-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.catalogo',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? request('inmueble_id') ?? null),
        'pagina'      => null,
        'total'       => 0,
        'activos_pag' => 0,
        'inact_pag'   => 0,
        'tiposOpt'    => 0,
        'soloLectura' => $soloLectura,
        'validacion'  => [],
        'errores'     => [],
    ];
    if (isset($errors) && $errors->any()) {
        $catDbg['validacion'] = $errors->all();
    }

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
            $catDbg['errores'][] = 'TipoInmueble: ' . $e->getMessage();
        }
    }

    // Conteos rápidos de la colección actual para los chips de filtro
    $itemsCol = ($propiedadesCollection instanceof \Illuminate\Pagination\AbstractPaginator)
        ? $propiedadesCollection->getCollection()
        : collect($propiedadesCollection);

    $cntTodosPag   = $itemsCol->count();
    $cntActivosPag = $itemsCol->filter(fn ($p) => !empty($p->active))->count();
    $cntInactPag   = max(0, $cntTodosPag - $cntActivosPag);

    $catDbg['pagina']      = method_exists($propiedadesCollection, 'currentPage') ? $propiedadesCollection->currentPage() : 1;
    $catDbg['total']       = method_exists($propiedadesCollection, 'total') ? $propiedadesCollection->total() : $cntTodosPag;
    $catDbg['activos_pag'] = $cntActivosPag;
    $catDbg['inact_pag']   = $cntInactPag;
    $catDbg['tiposOpt']    = $tiposInmueblesOpt->count();

    $rutaUpdateBase = \Illuminate\Support\Facades\Route::has('rsv.inmuebles.update')
        ? route('rsv.inmuebles.update', '__ID__')
        : url('/rsv/inmuebles/__ID__');
@endphp

{{-- CAT-v3.0 · Tab DBG textual + LED + Panel LOCAL (fuera de la raíz: mueren con el fragmento) --}}
<div id="catDbgToggle" title="Debug Catálogo (CAT-v3.0-DBG)">
    <span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span>
</div>
<div id="catDbgPanel" role="dialog" aria-label="Panel de debug de Catálogo">
    <div class="cat-dbg-header" id="catDbgHeader">
        <span>DEBUG · Catálogo (N3) · CAT-v3.0-DBG</span>
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
        #catRoot .cat-filter-chip {
            border: 1px solid transparent;
            background: transparent;
            color: #94a3b8;
            font-size: 8px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 5px;
            cursor: pointer;
            transition: all .15s ease;
            line-height: 1.4;
        }
        #catRoot .cat-filter-chip:hover { color: #475569; background: #f8fafc; }
        #catRoot .cat-filter-chip.active {
            background: #f0f7f2;
            color: #3d7a5c;
            border-color: #d5e7dc;
            font-weight: 700;
        }
        #catRoot .cat-search-inp {
            font-size: 8.5px;
            color: #475569;
            background: #fafbfd;
            border: 1px solid #e9eef4;
            border-radius: 4px;
            padding: 3px 9px;
            outline: 0;
            width: 165px;
            transition: all .15s ease;
        }
        #catRoot .cat-search-inp:focus {
            background: #fff;
            border-color: #d5e7dc;
            width: 210px;
        }
        #catRoot .cat-card-item {
            border-color: #f1f5f9 !important;
            border-radius: 6px;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        #catRoot .cat-card-item:hover {
            border-color: #d5e7dc !important;
            box-shadow: 0 4px 12px rgba(15,23,42,.05);
            transform: translateY(-1px);
        }
        #catRoot .cat-btn-quick {
            background: #fafbfd;
            color: #64748b;
            border: 1px solid #e9eef4 !important;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: 600;
            padding: 4px 8px;
            transition: all .15s ease;
        }
        #catRoot .cat-btn-quick:hover {
            background: #f2f6fb;
            color: #5b7fa6;
            border-color: #dbe6f2 !important;
        }
    </style>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- CATÁLOGO · PANEL PRINCIPAL                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        {{-- CABECERA + BARRA DE FILTROS EN MODO GLOBAL --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">

            <div class="d-flex align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                    <i class="bi bi-houses me-1" style="font-size: 8.5px; color: #5d8a70;"></i>
                    {{ $modoGlobal ? 'Visión Global del Catálogo' : 'Ficha Visual del Inmueble' }}
                    <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">CAT-v3.0-DBG</span>
                </h6>

                @if ($modoGlobal && $cntTodosPag > 0)
                    <div class="d-flex align-items-center gap-1 ms-1">
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

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if ($modoGlobal)
                    @if ($cntTodosPag > 0)
                        <input type="text" id="catSearchInput" class="cat-search-inp" placeholder="Filtrar por nombre o ciudad…">
                    @endif

                    @if (!$soloLectura)
                        <button type="button"
                                class="btn btn-sm d-inline-flex align-items-center border js-cat-open-crear"
                                style="gap: 4px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 3px 10px; border-radius: 4px; transition: all .15s ease;"
                                onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                                onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
                                data-bs-toggle="modal" data-bs-target="#modalCrearInmueble">
                            <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Inmueble
                        </button>
                    @endif
                @else
                    <span class="d-inline-flex align-items-center border"
                          style="gap: 3px; font-size: 7.5px; font-weight: 600; padding: 1px 8px; border-radius: 8px; line-height: 13px; white-space: nowrap; color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2 !important;">
                        <i class="bi bi-fingerprint" style="font-size: 7px; line-height: 1;"></i>EXPEDIENTE #{{ $inmueble->id }}
                    </span>
                @endif
            </div>

        </div>

        {{-- GRID DE TARJETAS --}}
        <div class="row g-2" id="catCardsGrid">

            @forelse($propiedadesCollection as $prop)
                @php
                    $esActivo = !empty($prop->active);
                    $tipoNombre = optional($prop->tipoInmueble)->nombre ?? '';
                    $fotosCount = $prop->multimedia ? $prop->multimedia->count() : 0;
                    $searchBlob = mb_strtolower(($prop->name ?? '') . ' ' . ($prop->city ?? '') . ' ' . ($prop->ubicacion ?? '') . ' ' . $tipoNombre . ' #' . $prop->id);
                @endphp

                <div class="col-12 {{ $modoGlobal ? 'col-md-6 col-xl-4' : 'col-lg-8' }} js-cat-col"
                     data-cat-active="{{ $esActivo ? '1' : '0' }}"
                     data-cat-search="{{ $searchBlob }}"
                     data-prop-id="{{ $prop->id }}">

                    <div class="card h-100 overflow-hidden d-flex flex-column border bg-white text-decoration-none cat-card-item">

                        {{-- Carrusel de Fotos del Inmueble --}}
                        <div id="carouselInmuebleGlobal{{ $prop->id }}"
                             class="carousel slide position-relative flex-shrink-0 border-bottom"
                             style="height: {{ $modoGlobal ? '150px' : '220px' }}; background: #f4f6f8; border-color: #f1f5f9 !important;"
                             data-bs-ride="carousel"
                             data-bs-interval="5000">

                            {{-- Contador de fotos e ID en la esquina superior --}}
                            <div class="position-absolute d-flex justify-content-between align-items-center w-100 px-2" style="top: 6px; left: 0; z-index: 5; pointer-events: none;">
                                <span class="badge" style="font-size: 6.5px; font-weight: 700; background: rgba(15,23,42,.65); color: #fff; border-radius: 3px; padding: 2px 5px;">
                                    #{{ $prop->id }}
                                </span>
                                @if($fotosCount > 0)
                                    <span class="badge" style="font-size: 6.5px; font-weight: 600; background: rgba(15,23,42,.65); color: #fff; border-radius: 3px; padding: 2px 5px;">
                                        <i class="bi bi-images me-1" style="font-size: 6px;"></i>{{ $fotosCount }}
                                    </span>
                                @endif
                            </div>

                            <div class="carousel-inner w-100 h-100">
                                @forelse($prop->multimedia ?? collect() as $index => $media)
                                    <div class="carousel-item w-100 h-100 {{ $index === 0 ? 'active' : '' }}" data-bs-interval="5000">
                                        <img src="{{ $media->url_archivo }}" alt="Foto {{ $prop->name }}" class="w-100 h-100" style="object-fit: cover; object-position: center;" loading="lazy">
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

                            @if($fotosCount > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="prev"
                                        style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 20px; height: 20px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.88); box-shadow: 0 1px 4px rgba(15,23,42,.18); opacity: .85; z-index: 5; left: 6px; transition: all .15s ease;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 10px; height: 10px; filter: invert(.5);"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselInmuebleGlobal{{ $prop->id }}" data-bs-slide="next"
                                        style="position: absolute; top: 50%; bottom: auto; transform: translateY(-50%); width: 20px; height: 20px; margin: 0; padding: 0; border: 1px solid rgba(233,238,244,.95); border-radius: 50%; background: rgba(255,255,255,.88); box-shadow: 0 1px 4px rgba(15,23,42,.18); opacity: .85; z-index: 5; right: 6px; transition: all .15s ease;">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="width: 10px; height: 10px; filter: invert(.5);"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            @endif

                        </div>

                        {{-- Cuerpo de la Tarjeta --}}
                        <div class="card-body d-flex flex-column flex-grow-1" style="padding: 9px 11px 10px;">

                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div class="text-truncate" style="font-size: {{ $modoGlobal ? '9.5px' : '11px' }}; font-weight: 700; color: #334155;" title="{{ $prop->name }}">
                                    {{ $prop->name }}
                                </div>

                                @if(!is_null($prop->latestTarifa?->precio_minimo_reserva))
                                    <span class="d-inline-flex align-items-baseline flex-shrink-0 border"
                                          style="gap: 2px; font-size: 9px; font-weight: 700; color: #3d7a5c; background: #f0f7f2; border-color: #d5e7dc !important; border-radius: 3px; padding: 1px 7px; font-variant-numeric: tabular-nums;"
                                          title="Mínimo de reserva · última tarifa">
                                        ${{ number_format($prop->latestTarifa->precio_minimo_reserva, 0) }}
                                        <small style="font-size: 6.5px; font-weight: 600; color: #94a3b8;">mín.</small>
                                    </span>
                                @endif
                            </div>

                            {{-- Chips: tipo + estado --}}
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
                                @if(!empty($prop->ubicacion))
                                    <span style="color: #e2e8f0; margin: 0 4px;">|</span>
                                    <i class="bi bi-signpost-2 me-1" style="font-size: 8px; color: #c3ccd6;"></i>{{ $prop->ubicacion }}
                                @endif
                            </div>

                            {{-- Stats ampliados en modo individual --}}
                            @if(!$modoGlobal)
                                <div class="d-flex w-100" style="gap: 6px; margin-top: 8px;">
                                    <div class="flex-grow-1 border d-flex flex-column" style="min-width: 0; background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 3px; padding: 5px 8px; gap: 1px;">
                                        <span style="font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7;">Fotos</span>
                                        <span style="font-size: 10px; font-weight: 700; font-variant-numeric: tabular-nums; color: #5b7fa6;">{{ $fotosCount }}</span>
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

                            <div class="mt-auto border-top" style="padding-top: 8px; margin-top: 8px !important; border-color: #f1f5f9 !important;">
                                @if($modoGlobal)
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                           data-cat-gestionar="1"
                                           data-prop-id="{{ $prop->id }}"
                                           data-prop-name="{{ $prop->name }}"
                                           class="btn d-flex align-items-center justify-content-center text-decoration-none flex-grow-1 border"
                                           style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;">
                                            <i class="bi bi-folder2-open" style="font-size: 8.5px;"></i> Gestionar Propiedad
                                        </a>

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
                                                    data-active="{{ $prop->active ? '1' : '0' }}">
                                                <i class="bi bi-sliders"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    @if(!$soloLectura)
                                        <button type="button" class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 border js-cat-open-editar-ficha"
                                                style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;"
                                                data-bs-toggle="modal" data-bs-target="#modalEditarInmuebleFicha">
                                            <i class="bi bi-pencil-square" style="font-size: 9px;"></i> Editar Propiedad
                                        </button>
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center w-100" style="gap: 4px; font-size: 7.5px; color: #b3bcc7; font-style: italic;">
                                            <i class="bi bi-eye" style="font-size: 8px;"></i> Solo lectura (gestiona desde el expediente)
                                        </span>
                                    @endif
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

            {{-- Estado vacío cuando el filtro en vivo no encuentra coincidencias --}}
            <div class="col-12 d-none" id="catEmptyFilterMsg">
                <div class="border text-center" style="border-style: dashed !important; border-color: #e9eef4 !important; border-radius: 5px; background: #fafbfd; padding: 20px 14px; color: #94a3b8; font-size: 8.5px;">
                    <i class="bi bi-search d-block mb-1" style="font-size: 14px; color: #cbd5e1;"></i>
                    Ningún inmueble coincide con el filtro actual en esta página.
                </div>
            </div>

        </div>

        {{-- Paginación (solo global) · interceptada por AJAX sin recargar página --}}
        @if($modoGlobal && isset($propiedadesCollection) && method_exists($propiedadesCollection, 'links'))
            <div class="mt-3" style="font-size: 8.5px;">
                {{ $propiedadesCollection->appends(['inmueble_id' => 'todos'])->links() }}
            </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1 · NUEVO INMUEBLE (envío por AJAX)                       --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(!$soloLectura)
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

                <form action="{{ route('rsv.inmuebles.store') }}" method="POST" id="catFormNuevo" data-cat-form="crear">
                    @csrf

                    <div class="modal-body" style="padding: 12px 14px;">

                        <div class="cat-form-err" style="display: none; background: #fdf5f4; color: #b45f5a; border: 1px solid #f0dcd8; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

                        {{-- 1 · IDENTIFICACIÓN --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                                <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i> Identificación
                                <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                            </div>

                            <div class="mb-2">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span style="color: #c98a85;">*</span></label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="name" placeholder="Ej. Apartamento Vista al Mar" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>

                            <div class="mb-0">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                                <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <option value="">-- Sin tipo asignado --</option>
                                    @foreach ($tiposInmueblesOpt->where('active', 1) as $tipoOpt)
                                        <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}</option>
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

                            <div class="mb-2">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad <span style="color: #c98a85;">*</span></label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" placeholder="Ej. Santa Marta" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>

                            <div class="mb-0">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección / Ubicación física</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" placeholder="Ej. Calle 100 # 15-20" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
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
                                    <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" placeholder="Ej. 6" value="6" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                </div>
                                <div class="col-7">
                                    <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                    <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                        <div class="text-start">
                                            <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                            <div style="font-size: 7px; color: #b3bcc7;">Visible en catálogo</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="active" value="0">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="newInmActive" checked>
                                        </div>
                                    </div>
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
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2 · EDICIÓN RÁPIDA DESDE EL GRID GLOBAL (CAT-v3.0-DBG)    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($modoGlobal && !$soloLectura)
    <div class="modal fade" id="modalQuickEditInmueble" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 5px; font-family: Calibri, 'Segoe UI', system-ui, sans-serif; font-size: 8px; line-height: 1.4; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    <div class="text-end">
                        <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                            Edición Rápida · Catálogo Global
                        </div>
                        <h6 class="modal-title mb-0 text-dark fw-bold" id="catQuickEditTitle" style="font-size: 10.5px; color: #475569 !important;">Editar Inmueble</h6>
                    </div>
                </div>

                <form action="" method="POST" id="catFormQuickEditar" data-cat-form="editar-rapido">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">
                        <div class="cat-form-err" style="display: none; background: #fdf5f4; color: #b45f5a; border: 1px solid #f0dcd8; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del inmueble <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="name" id="catQeName" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                        </div>

                        <div class="mb-2">
                            <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de inmueble</label>
                            <select class="form-select form-select-sm shadow-none border" name="tipo_inmueble_id" id="catQeTipo" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <option value="">-- Sin tipo asignado --</option>
                                @foreach ($tiposInmueblesOpt as $tipoOpt)
                                    <option value="{{ $tipoOpt->id }}">{{ $tipoOpt->nombre }}{{ $tipoOpt->active ? '' : ' (inactivo)' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Ciudad</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="city" id="catQeCity" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                            <div class="col-6">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Dirección</label>
                                <input type="text" class="form-control form-control-sm shadow-none border" name="ubicacion" id="catQeUbicacion" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Capacidad máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="capacidad_maxima" id="catQeCapacidad" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                            <div class="col-7">
                                <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                    <div class="text-start">
                                        <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                        <div style="font-size: 7px; color: #b3bcc7;">Visible en catálogo</div>
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
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
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

                <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST" id="catFormEditar" data-cat-form="editar">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">

                        <div class="cat-form-err" style="display: none; background: #fdf5f4; color: #b45f5a; border: 1px solid #f0dcd8; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

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
                                </div>

                                <div class="col-7">
                                    <label class="d-block" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Visibilidad</label>
                                    <div class="d-flex align-items-center justify-content-between border" style="background: #fafbfd; border-color: #f1f5f9 !important; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                        <div class="text-start">
                                            <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                            <div style="font-size: 7px; color: #b3bcc7;">Visible en catálogo público</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="active" value="0">
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

    <script>
    /* ═══════════════ CAT-v3.0-DBG · catalogo · NIVEL 3 ═══════════════ */
    (function () {
        'use strict';
        var root = document.getElementById('catRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var UPDATE_TPL = root.getAttribute('data-update-tpl') || '';
        var SRVDBG = @json($catDbg);

        /* ═══ 1) PANEL DE DEBUG LOCAL PERSISTENTE (estilo terminal TIP-v1.3) ═══ */
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
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#catDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:400px;',
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
                        t.title = 'Debug Catálogo (CAT-v3.0-DBG)';
                        t.innerHTML = '<span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('catDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'catDbgPanel';
                        p.innerHTML = '<div class="cat-dbg-header" id="catDbgHeader">' +
                            '<span>DEBUG · Catálogo (N3) · CAT-v3.0-DBG</span>' +
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
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle'];
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
                        var txt = '=== DEBUG catalogo CAT-v3.0-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('Bitácora copiada al portapapeles', 'ok');
                            }).catch(function () {
                                api.log('No se pudo copiar (permisos del navegador)', 'warn');
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

        /* ═══ 2) FILTRO INSTANTÁNEO EN CLIENTE (BÚSQUEDA + CHIPS) ═══ */
        function aplicarFiltroCatalogo(logEvento) {
            var r = document.getElementById('catRoot');
            if (!r) return;
            var chipAct = r.querySelector('.cat-filter-chip.active');
            var filtroEstado = chipAct ? chipAct.getAttribute('data-cat-filter') : 'all';
            var q = (document.getElementById('catSearchInput')?.value || '').toLowerCase().trim();
            var visibles = 0;

            var cols = r.querySelectorAll('.js-cat-col');
            cols.forEach(function (col) {
                var st = col.getAttribute('data-cat-active') || '1';
                var blob = col.getAttribute('data-cat-search') || '';
                var okEstado = (filtroEstado === 'all') || (st === filtroEstado);
                var okTexto = !q || blob.indexOf(q) !== -1;
                var mostrar = okEstado && okTexto;
                col.style.display = mostrar ? '' : 'none';
                if (mostrar) visibles++;
            });

            var emptyMsg = document.getElementById('catEmptyFilterMsg');
            if (emptyMsg && cols.length > 0) {
                emptyMsg.classList.toggle('d-none', visibles > 0);
            }

            if (logEvento) {
                DBG.log('Filtro aplicado · estado=' + filtroEstado + ' · q="' + q + '" · visibles=' + visibles + '/' + cols.length, 'info');
            }
        }

        /* ═══ 3) DELEGACIÓN DE CLICS GLOBALES Y EVENTOS UI ═══ */
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

            // Chips de filtro (Todos / Activos / Inactivos)
            var chip = t.closest('#catRoot .cat-filter-chip');
            if (chip) {
                document.querySelectorAll('#catRoot .cat-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltroCatalogo(true);
                return;
            }

            // Botón Nuevo Inmueble
            if (t.closest('.js-cat-open-crear')) {
                DBG.log('Click: Abriendo modal Nuevo Inmueble', 'ev');
                return;
            }

            // Botón Editar Propiedad (modo ficha)
            if (t.closest('.js-cat-open-editar-ficha')) {
                DBG.log('Click: Abriendo modal Editar Ficha #' + (SRVDBG ? SRVDBG.inmuebleId : '?'), 'ev');
                return;
            }

            // Botón Edición Rápida desde el Grid Global
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

                DBG.log('Click: Abriendo Edición Rápida para #' + id + ' ("' + name + '")', 'ev');
                var modalEl = document.getElementById('modalQuickEditInmueble');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
                return;
            }
        };
        document.addEventListener('click', window.__catDbgDocHandler);

        if (window.__catDbgDblHandler) { document.removeEventListener('dblclick', window.__catDbgDblHandler); }
        window.__catDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#catDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__catDbgDblHandler);

        // Buscador en vivo con debounce para el log
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

        /* ═══ 4) BOOT · CONTEXTO DEL SERVIDOR ═══ */
        DBG.log('CAT-v3.0-DBG activo · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · total=' + (SRVDBG ? SRVDBG.total : '?') +
            ' · pág=' + ((SRVDBG && SRVDBG.pagina) ? SRVDBG.pagina : 1) +
            ' (activos=' + (SRVDBG ? SRVDBG.activos_pag : 0) + ', inactivos=' + (SRVDBG ? SRVDBG.inact_pag : 0) + ')' +
            (SRVDBG && SRVDBG.inmuebleId ? ' · inmuebleSel=' + SRVDBG.inmuebleId : '') +
            (SRVDBG && SRVDBG.soloLectura ? ' · SOLO LECTURA' : ''), 'ok');

        if (window.__catExpectedFicha && SRVDBG && SRVDBG.modo === 'global') {
            DBG.log('⚠ Se esperaba la ficha individual pero llegó el grid global (verifica filtro inmueble_id en el controlador)', 'warn');
            window.__catExpectedFicha = false;
        }
        if (SRVDBG && SRVDBG.validacion && SRVDBG.validacion.length) {
            for (var _vi = 0; _vi < SRVDBG.validacion.length; _vi++) {
                DBG.log('⚠ Validación pendiente: ' + SRVDBG.validacion[_vi], 'warn');
            }
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        /* ═══ 5) CARRUSELES · AUTOPLAY GARANTIZADO ═══ */
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
                    var inst = bootstrap.Carousel.getInstance(el);
                    if (!inst && typeof bootstrap.Carousel.getOrCreateInstance === 'function') {
                        inst = bootstrap.Carousel.getOrCreateInstance(el, cfg);
                    } else if (!inst) {
                        inst = new bootstrap.Carousel(el, cfg);
                    }
                    if (inst && inst.cycle && el.closest('.tab-pane.show, .tab-pane.active, body')) {
                        inst.cycle();
                    }
                } catch (e) {}
            }
        }
        initCatCarousels(root);
        if (window.__catTabHandler) { document.removeEventListener('shown.bs.tab', window.__catTabHandler); }
        window.__catTabHandler = function (e) {
            var sel = e.target && e.target.getAttribute && e.target.getAttribute('data-bs-target');
            var pane = sel ? document.querySelector(sel) : null;
            if (pane) { initCatCarousels(pane); }
        };
        document.addEventListener('shown.bs.tab', window.__catTabHandler);
        window.initCatCarousels = initCatCarousels;

        /* ═══ 6) SWAP INTERNO · REPINTA #catRoot SIN RECARGAR PÁGINA ═══ */
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
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + ' (' + ms + 'ms)' + (det ? ' · ' + det : ''));
                    });
                }
                return r.text().then(function (html) { return { html: html, ms: ms }; });
            }).then(function (res) {
                catSwapFragmento(res.html, urlBarra);
                DBG.log('Catálogo actualizado (' + res.ms + 'ms · ' + res.html.length + ' bytes) · sin recargar página', 'ok');
            }).catch(function (err) {
                DBG.log('⚠ Recarga del catálogo falló (' + err.message + ')', 'err');
                DBG.open();
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
            if (esFicha) { window.__catExpectedFicha = true; }
            return catCargar(FRAG + (FRAG.indexOf('?') === -1 ? '?' : '&') + partes.join('&'), null, motivo || 'contexto');
        }
        window.catReloadFragment = recargarContexto;

        /* ═══ 7) PAGINACIÓN SIN RECARGA (FASE CAPTURA EN WINDOW) ═══ */
        if (window.__catPageHandler) { window.removeEventListener('click', window.__catPageHandler, true); }
        window.__catPageHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || !a.closest('#catRoot') || !a.closest('.pagination')) { return; }
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (!FRAG) { DBG.log('Paginación nativa (sin ruta de fragmento)', 'warn'); return; }
            var href = a.getAttribute('href') || '';
            if (!href || href.charAt(0) === '#') { return; }
            var u, f;
            try { u = new URL(a.href, window.location.href); } catch (e0) { return; }
            try { f = new URL(FRAG, window.location.href); } catch (e1) { return; }
            e.preventDefault();
            e.stopPropagation();

            var partes = [];
            u.searchParams.forEach(function (v, k) {
                partes.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
            });
            if (!u.searchParams.has('page_inmuebles')) { partes.push('page_inmuebles=1'); }
            var dest = f.pathname + '?' + partes.join('&');
            var barra = (u.pathname === f.pathname) ? null : (u.pathname + u.search);
            var numPag = u.searchParams.get('page_inmuebles') || '1';
            DBG.log('Click: Paginación → página ' + numPag + ' (AJAX)', 'ev');
            catCargar(dest, barra, 'paginacion-p' + numPag);
        };
        window.addEventListener('click', window.__catPageHandler, true);

        /* ═══ 8) FORMULARIOS POR AJAX (CREAR / EDITAR FICHA / EDICIÓN RÁPIDA) ═══ */
        function catToast(txt, nivel) {
            var wrap = document.getElementById('catToastWrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'catToastWrap';
                document.body.appendChild(wrap);
            }
            var t = document.createElement('div');
            var colores = {
                ok: 'color:#86efac;border-color:#14532d;',
                err: 'color:#fca5a5;border-color:#7f1d1d;',
                info: 'color:#93c5fd;border-color:#1e3a8a;'
            };
            t.setAttribute('style', 'font-family:inherit;font-size:11px;padding:8px 12px;border-radius:8px;' +
                'border:1px solid;background:#0f172a;box-shadow:0 6px 18px rgba(0,0,0,.25);max-width:340px;' +
                (colores[nivel] || colores.info));
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
            DBG.log('POST (' + tipo + ') → ' + form.getAttribute('action') + ' · nombre="' + (fd.get('name') || '') + '" · active=' + (fd.get('active') || '0'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (redirigido al login)');
                }
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
                    if (!msgs.length && res.html) {
                        var det422 = extraerErrorLaravel(res.html);
                        if (det422) { msgs.push(det422); }
                    }
                    if (caja) {
                        caja.innerHTML = msgs.map(function (m) { return '<div>• ' + escapar(m) + '</div>'; }).join('');
                        caja.style.display = msgs.length ? 'block' : 'none';
                    }
                    DBG.log('⚠ Validación 422 (' + res.ms + 'ms): ' + (msgs.join(' | ') || 'sin detalle'), 'err');
                    catToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + ' (' + res.ms + 'ms)' + (det ? ' · ' + det : ''));
                }
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                catToast(tipo === 'crear' ? 'Inmueble creado · actualizando catálogo…' : 'Cambios guardados · actualizando catálogo…', 'ok');
                DBG.log('Guardado OK (' + tipo + ' · HTTP ' + res.status + ' · ' + res.ms + 'ms) → refrescando catálogo por AJAX', 'ok');
                recargarContexto('post-' + tipo);
            }).catch(function (err) {
                DBG.log('⚠ Guardado falló (' + err.message + ')', 'err');
                catToast('No se pudo guardar · ' + err.message, 'err');
                DBG.open();
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__catFormHandler);

        /* ═══ 9) ENLACE "Gestionar Propiedad" CON RESPALDO AJAX ═══ */
        if (window.__catNavHandler) { document.removeEventListener('click', window.__catNavHandler, true); }
        window.__catNavHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || !a.closest('#catRoot') || !a.hasAttribute('data-cat-gestionar')) { return; }
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }

            var propNom = a.getAttribute('data-prop-name') || '';
            var propId  = a.getAttribute('data-prop-id') || '';
            DBG.log('Click: Gestionar Propiedad #' + propId + ' ("' + propNom + '")', 'ev');

            if (a.closest('#showRoot') && window.__inmLinkHandler) {
                DBG.log('Gestionar → atendido por tab N2 (swap AJAX de #showRoot)', 'debug');
                return;
            }
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (show && fragN2) {
                var rid = propId || 'todos';
                try {
                    var uG = new URL(a.href, window.location.href);
                    if (uG.searchParams.has('inmueble_id')) { rid = uG.searchParams.get('inmueble_id'); }
                } catch (eG0) {}
                e.preventDefault();
                catGestionarFallback(rid, a.href);
                return;
            }
            DBG.log('Gestionar → navegación nativa (vista standalone)', 'debug');
        };
        document.addEventListener('click', window.__catNavHandler, true);

        function catGestionarFallback(rid, urlDestino) {
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (!show || !fragN2) { window.location.href = urlDestino; return; }
            var url = fragN2 + (fragN2.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(rid);
            var t0 = performance.now();
            DBG.log('GET Expediente N2 → ' + url, 'net');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró');
                }
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + ' (' + ms + 'ms)' + (det ? ' · ' + det : ''));
                    });
                }
                return r.text().then(function (html) { return { html: html, ms: ms }; });
            }).then(function (res) {
                var tmp = document.createElement('div');
                tmp.innerHTML = res.html;
                var nuevo = tmp.querySelector('#showRoot');
                if (!nuevo) { throw new Error('el fragmento no trajo #showRoot'); }
                var actual = document.getElementById('showRoot');
                if (!actual) { throw new Error('#showRoot ya no está en el DOM'); }
                actual.replaceWith(nuevo);
                catRunScripts(nuevo);
                try { window.history.pushState({}, '', urlDestino); } catch (eG1) {}
                try { document.dispatchEvent(new CustomEvent('rsv:inmueble-cambiado', { detail: { inmuebleId: rid } })); } catch (eG2) {}
                try {
                    var paneD = nuevo.closest('.tab-pane');
                    if (paneD && paneD.id && !paneD.classList.contains('active')) {
                        var linkD = document.querySelector('[data-bs-target="#' + paneD.id + '"], [href="#' + paneD.id + '"]');
                        if (linkD && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                            bootstrap.Tab.getOrCreateInstance(linkD).show();
                        }
                    }
                } catch (eG3) {}
                try { nuevo.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (eG4) {}
                DBG.log('Expediente #' + rid + ' abierto por AJAX (' + res.ms + 'ms · ' + res.html.length + ' bytes)', 'ok');
            }).catch(function (eG5) {
                DBG.log('⚠ Respaldo del catálogo falló (' + eG5.message + ') → navegación nativa', 'err');
                DBG.open();
                window.location.href = urlDestino;
            });
        }
    })();
    </script>

</div>
