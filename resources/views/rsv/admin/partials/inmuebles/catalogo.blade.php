{{-- resources/views/rsv/admin/partials/inmuebles/catalogo.blade.php · CAT-v2.1-DBG · NIVEL 3 (catálogo de inmuebles) --}}
{{-- CAT-v2 · NOVEDADES v2 · 6) "Gestionar Propiedad" ahora SÍ abre por AJAX aunque el catálogo esté --}}
{{--    FUERA de #showRoot: si el tab N2 no puede atender el clic, el catálogo hace el swap él mismo --}}
{{--    (fetch al fragmento del N2 + swap de #showRoot + scripts + auto-tab al panel de la ficha). --}}
{{--    Solo navega nativo cuando NO existe #showRoot en la página (vista standalone). --}}
{{-- CAT-v2 · NOVEDADES · 1) FIX CRÍTICO de variables: el fragmento admin.inmuebles.catalogo.fragmento pasa --}}
{{--    "inmueblesGrid" pero la vista solo leía la variable del tab N2 → el catálogo salía VACÍO por AJAX. --}}
{{--    Ahora la cadena de respaldo es: propiedadesCollection → inmuebles → inmueblesGrid → colección vacía. --}}
{{--    2) Paginación SIN recarga: los enlaces .pagination se interceptan y se traen por fetch al fragmento --}}
{{--    (page_inmuebles) repintando SOLO #catRoot. 3) Formularios Nuevo/Editar por AJAX: errores de --}}
{{--    validación dentro del modal y, al guardar, el catálogo se recarga sin recargar la página. --}}
{{--    4) Panel de Errores LOCAL (botón abajo-derecha + Copiar/Limpiar/✕): registra SOLO operaciones de --}}
{{--    esta vista; el colector de contexto del servidor (marca CAT-v2) viaja al panel como JSON. --}}
{{--    5) Soporte de soloLectura (misma bandera con que el tab N2 incluye tarifa/galería en solo lectura). --}}
{{--    7) CAMBIO CAT-v2.1-1 · Toggle de debug rediseñado al estilo "terminal" aprobado --}}
{{--    en TIP-v1.3: la pestaña deja el emoji y pasa a texto DBG en monospace 8px --}}
{{--    con LED de estado (verde = ok · rojo pulsante + conteo = errores). 16px de --}}
{{--    alto y FIJA SIEMPRE en right:0 · bottom:0 (nunca se apila hacia arriba). El --}}
{{--    panel sí esquiva a los vecinos midiendo px reales (getBoundingClientRect): --}}
{{--    bottom = max(20, vecino + 4). Exclusividad reforzada: el toggle/panel viajan --}}
{{--    con el fragmento y su respaldo SOLO re-crea nodos dentro de #catRoot --}}
{{--    (nunca cuelga de document.body). SPEC DE DISTANCIAS documentada en el CSS. --}}
{{-- Arquitectura (patrón probado en reservas v32 y tab-inmuebles INM-v1): UNA raíz estable #catRoot --}}
{{-- envuelve TODO incluido el script (renace con listeners tras cada swap) y el panel va ANTES del root --}}
{{-- (sobrevive a los swaps y evita duplicados). Todos los comentarios de este archivo son seguros. --}}
{{-- ESTILO: sistema suave idéntico a tarifas · título 9.5px · textos 8px · pasteles --}}
{{-- MODO GLOBAL   = grid de tarjetas + paginación + botón Nuevo       --}}
{{-- MODO INDIVIDUAL = ficha única ampliada con stats y modal de edición PROPIO --}}
{{-- FIX CARRUSEL: autoplay garantizado por JS + flechas SIEMPRE visibles --}}

@php
    // ── CAT-v2.1-DBG · Red de seguridad de variables ──
    // FIX CRÍTICO: el fragmento del controlador pasa "inmueblesGrid"; el tab N2 pasa "inmuebles"/"inmueble".
    $modoGlobal = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $soloLectura = !empty($soloLectura);

    // ── CAT-v2.1-DBG · colector de contexto del servidor (solo lectura, sin lógica de negocio) ──
    $catDbg = [
        'marca'       => 'CAT-v2.1-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.catalogo',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? null),
        'pagina'      => null,
        'total'       => 0,
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

    // Enriquecer el colector con lo que depende de las colecciones ya resueltas
    $catDbg['pagina'] = method_exists($propiedadesCollection, 'currentPage') ? $propiedadesCollection->currentPage() : null;
    $catDbg['total']  = method_exists($propiedadesCollection, 'total') ? $propiedadesCollection->total() : $propiedadesCollection->count();
    $catDbg['tiposOpt'] = $tiposInmueblesOpt->count();
@endphp

{{-- CAT-v2.1 · Tab DBG textual + LED + Panel LOCAL (fuera de la raíz: mueren con el fragmento · NO global) --}}
<div id="catDbgToggle" title="Debug Catálogo (CAT-v2.1-DBG)"><span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span></div>
<div id="catDbgPanel" role="dialog" aria-label="Panel de debug de Catálogo">
    <div class="cat-dbg-header" id="catDbgHeader">
        <span>DEBUG · Catálogo (N3) · CAT-v2.1-DBG</span>
        <span class="cat-dbg-controls">
            <button type="button" class="cat-dbg-btn" id="catDbgCopy">Copiar</button>
            <button type="button" class="cat-dbg-btn" id="catDbgClear">Limpiar</button>
            <button type="button" class="cat-dbg-btn" id="catDbgClose">✕</button>
        </span>
    </div>
    <div id="catDbgBody"></div>
</div>

<div id="catRoot" data-frag="{{ route('rsv.admin.inmuebles.catalogo.fragmento') }}">

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- CATÁLOGO · PANEL PRINCIPAL (simétrico al panel de tarifas)       --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <div class="d-flex justify-content-between align-items-center mb-2">

            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-houses me-1" style="font-size: 8px; color: #5d8a70;"></i>
                {{ $modoGlobal ? 'Visión Global del Catálogo' : 'Ficha Visual del Inmueble' }}
                <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">CAT-v2.1-DBG</span>
            </h6>

            @if ($modoGlobal)
                @if (!$soloLectura)
                    <button type="button"
                            class="btn btn-sm d-inline-flex align-items-center border"
                            style="gap: 4px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px; transition: all .15s ease;"
                            onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                            onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
                            data-bs-toggle="modal" data-bs-target="#modalCrearInmueble">
                        <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Inmueble
                    </button>
                @endif
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
                                    {{-- GLOBAL: navegar al expediente (el tab N2 lo abre por AJAX si esta vista vive dentro de #showRoot) --}}
                                    <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                       data-cat-gestionar="1"
                                       class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 border"
                                       style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;"
                                       onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                                       onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';">
                                        <i class="bi bi-pencil-square" style="font-size: 9px;"></i> Gestionar Propiedad
                                    </a>
                                @else
                                    {{-- INDIVIDUAL: modal PROPIO de este partial (mismo tab-pane, siempre visible) --}}
                                    @if(!$soloLectura)
                                        <button type="button" class="btn d-flex align-items-center justify-content-center text-decoration-none w-100 border"
                                                style="gap: 5px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; border-radius: 3px; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 4px 10px; transition: all .15s ease;"
                                                onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                                                onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
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

        </div>

        {{-- Paginación (solo global) · CAT-v2.1-DBG la intercepta y la trae por AJAX (sin recargar la página) --}}
        @if($modoGlobal && isset($propiedadesCollection) && method_exists($propiedadesCollection, 'links'))
            <div class="mt-3" style="font-size: 8.5px;">
                {{ $propiedadesCollection->appends(['inmueble_id' => 'todos'])->links() }}
            </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL · NUEVO INMUEBLE (solo se usa en global · CAT-v2.1-DBG: envío por AJAX) --}}
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

                        {{-- Caja de errores de validación (la llena el panel CAT-v2.1-DBG con el 422) --}}
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
    @endif


    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL · EDITAR INMUEBLE (ficha · solo individual · ID único)     --}}
    {{-- Vive DENTRO de esta pestaña → siempre visible, sin gris fantasma --}}
    {{-- CAT-v2.1-DBG: envío por AJAX con errores de validación en el modal --}}
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

                        {{-- Caja de errores de validación (la llena el panel CAT-v2.1-DBG con el 422) --}}
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

    <script>
    /* ═══════════════ CAT-v2.1-DBG · catalogo · NIVEL 3 ═══════════════
       Script LOCAL de esta vista: viaja DENTRO de #catRoot, por lo que se
       re-ejecuta tras cada swap AJAX y renace con sus listeners.
       NO captura fetch ni errores de otros módulos de la página.        */
    (function () {
        var root = document.getElementById('catRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var SRVDBG = @json($catDbg);

        /* ═══ 1) PANEL DE DEBUG LOCAL (estilo terminal TIP-v1.3) ═══ */
        var DBG = (window.CatDbg && window.CatDbg.alive && window.CatDbg.alive())
            ? window.CatDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('catDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'catDbgStyle';
                    st.textContent = [
                        /* ═════════ SPEC DE DISTANCIAS · CAT-v2.1 (tab terminal · portada de TIP-v1.3) ═════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
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
                        '#catDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
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
                        '.cat-dbg-net{color:#c4b5fd;}.cat-dbg-debug{color:#94a3b8;}',
                        '#catToastWrap{position:fixed;left:16px;bottom:16px;z-index:1092;display:flex;',
                        'flex-direction:column;gap:6px;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    /* PATRÓN TIP-v1.1-1: el toggle y el panel ya vienen como HTML del
                       fragmento (fuera del root). Este respaldo solo los re-crea
                       DENTRO de #catRoot si un swap parcial los borrara;
                       NUNCA se cuelga nada de document.body (exclusividad total). */
                    var rootDbg = document.getElementById('catRoot');
                    if (!rootDbg) { return; } /* sin vista montada → cero nodos */
                    if (!$('catDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'catDbgToggle';
                        t.title = 'Debug Catálogo (CAT-v2.1-DBG)';
                        t.innerHTML = '<span class="cat-dbg-led" id="catDbgLed"></span>DBG<span class="cat-dbg-badge" id="catDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('catDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'catDbgPanel';
                        p.innerHTML = '<div class="cat-dbg-header" id="catDbgHeader">' +
                            '<span>DEBUG · Catálogo (N3) · CAT-v2.1-DBG</span>' +
                            '<span class="cat-dbg-controls">' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgCopy">Copiar</button>' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgClear">Limpiar</button>' +
                            '<button type="button" class="cat-dbg-btn" id="catDbgClose">✕</button>' +
                            '</span></div><div id="catDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* CAT-v2.1: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila:
                   con 16px de alto cabe bajo cualquier otro toggle sin tocarlo).
                   Solo el PANEL esquiva a los vecinos: bottom = max(20, vecino + 4). */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('catDbgToggle'), p = $('catDbgPanel');
                    if (t) { t.style.bottom = '0px'; } /* SI O SI pegada abajo */
                    if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; } /* 16 tab + 4 aire */
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
                        var txt = '=== DEBUG catalogo CAT-v2.1-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('log copiado al portapapeles', 'ok');
                            }).catch(function () {
                                api.log('no se pudo copiar (permisos del navegador)', 'warn');
                            });
                        } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
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
        DBG.ensure(); /* re-apila y re-crea nodos si algún swap de nivel superior los reemplazó */

        /* ═══ 1b) EXCLUSIVIDAD DE LA TAB DBG (patrón TIP-v1.1-1, sin vigilante) ═══
           La tab y el panel no se cuelgan de document.body: viajan como HTML
           del propio fragmento. Al navegar a otra vista el contenedor se
           intercambia y la tab muere SOLO con el fragmento; al volver al
           Catálogo renace con él. Nada que vigilar, nada que limpiar. */

        /* Botones del panel por DELEGACIÓN en document (sobreviven a cualquier swap) */
        if (window.__catDbgDocHandler) { document.removeEventListener('click', window.__catDbgDocHandler); }
        window.__catDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#catDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#catDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#catDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#catDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        };
        document.addEventListener('click', window.__catDbgDocHandler);
        if (window.__catDbgDblHandler) { document.removeEventListener('dblclick', window.__catDbgDblHandler); }
        window.__catDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#catDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__catDbgDblHandler);

        /* ═══ 2) BOOT · contexto del servidor (como el Panel de Huéspedes) ═══ */
        DBG.log('CAT-v2.1-DBG activo · tab DBG fija en esquina (0,0) · LED de estado · exclusiva (muere con el fragmento)', 'ok');
        if (window.__catEverBooted) { DBG.log('script re-ejecutado tras swap AJAX (el panel LOCAL continúa)', 'debug'); }
        window.__catEverBooted = true;
        DBG.log('init · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · total=' + (SRVDBG ? SRVDBG.total : '?') +
            ' · página=' + ((SRVDBG && SRVDBG.pagina) ? SRVDBG.pagina : 1) +
            (SRVDBG && SRVDBG.inmuebleId ? ' · inmuebleSel=' + SRVDBG.inmuebleId : '') +
            (SRVDBG && SRVDBG.soloLectura ? ' · SOLO LECTURA' : ''), 'info');
        DBG.log('SRV · peticion=' + ((SRVDBG && SRVDBG.peticion) ? SRVDBG.peticion : '?') +
            ' · url=' + ((SRVDBG && SRVDBG.url) ? SRVDBG.url : '?'), 'debug');
        DBG.log('Rutas · FRAG=' + (FRAG || 'NO') +
            (FRAG ? ' · paginación y recargas post-guardado por AJAX activas' : ' → FRAG vacío: paginación y guardados serán nativos'), FRAG ? 'ok' : 'warn');
        if (window.__catExpectedFicha && SRVDBG && SRVDBG.modo === 'global') {
            DBG.log('⚠ se esperaba la ficha individual pero llegó el grid global: el fragmento del catálogo aún no filtra por inmueble_id (aplica el parche CAT-PATCH-1 del controlador)', 'warn');
            window.__catExpectedFicha = false;
        }
        if (SRVDBG && SRVDBG.validacion && SRVDBG.validacion.length) {
            for (var _vi = 0; _vi < SRVDBG.validacion.length; _vi++) {
                DBG.log('⚠ validación pendiente: ' + SRVDBG.validacion[_vi], 'warn');
            }
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        /* ═══ 3) CARRUSELES · autoplay garantizado + re-init al mostrar tabs (oyentes RE-REGISTRABLES) ═══ */
        var CAT_INTERVAL = 5000; /* velocidad del autoplay en ms */
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
                } catch (e) { /* silencioso: nunca romper la página */ }
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
        if (!window.__catDomReadyHooked) {
            window.__catDomReadyHooked = true;
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    var r2 = document.getElementById('catRoot');
                    if (r2) { initCatCarousels(r2); }
                });
            }
        }
        window.initCatCarousels = initCatCarousels;

        /* ═══ 4) SWAP INTERNO · repinta #catRoot con el HTML del fragmento ═══ */
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
            if (document.querySelector('.modal.show')) { return; } /* hay otro modal legítimo abierto (otra vista): no tocar */
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
            catRunScripts(nuevo);
            limpiarResiduosModal();
            if (urlBarra) { try { window.history.replaceState({}, '', urlBarra); } catch (e6) {} }
        }
        function catCargar(url, urlBarra) {
            if (!FRAG) { window.location.href = url; return; }
            DBG.log('fetch ' + url, 'net');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + (det ? ' · ' + det : (t && t.length > 0 && t.length < 80 ? ' · ' + t.trim() : '')));
                    });
                }
                return r.text();
            }).then(function (html) {
                catSwapFragmento(html, urlBarra);
                DBG.log('catálogo recargado · ' + html.length + ' bytes · sin recargar la página', 'ok');
            }).catch(function (err) {
                DBG.log('⚠ recarga del catálogo falló (' + err.message + ')', 'err');
                DBG.open();
            });
        }

        /* ═══ 5) PAGINACIÓN SIN RECARGA · oyente en WINDOW en fase captura:
           corre ANTES que cualquier oyente en document (incluido el del tab N2,
           que si tomara este enlace recargaría el tab completo por error).     */
        if (window.__catPageHandler) { window.removeEventListener('click', window.__catPageHandler, true); }
        window.__catPageHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a) { return; }
            if (!a.closest('#catRoot')) { return; }   /* solo enlaces de ESTA vista */
            if (!a.closest('.pagination')) { return; } /* solo la paginación */
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (!FRAG) { DBG.log('paginación nativa (sin ruta de fragmento)', 'warn'); return; }
            var href = a.getAttribute('href') || '';
            if (!href || href.charAt(0) === '#') { return; }
            var u, f;
            try { u = new URL(a.href, window.location.href); } catch (e0) { return; }
            try { f = new URL(FRAG, window.location.href); } catch (e1) { return; }
            e.preventDefault();
            e.stopPropagation();
            /* Reconstruye la URL del FRAGMENTO con los parámetros del enlace (page_inmuebles, inmueble_id…) */
            var partes = [];
            u.searchParams.forEach(function (v, k) {
                partes.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
            });
            if (!u.searchParams.has('page_inmuebles')) { partes.push('page_inmuebles=1'); }
            var dest = f.pathname + '?' + partes.join('&');
            /* Barra de direcciones: si el enlace apunta a la página completa, conservar esa URL; si ya es el fragmento, no tocar */
            var barra = (u.pathname === f.pathname) ? null : (u.pathname + u.search);
            DBG.log('paginación → página ' + (u.searchParams.get('page_inmuebles') || '1') + ' (por AJAX, sin recargar)', 'info');
            catCargar(dest, barra);
        };
        window.addEventListener('click', window.__catPageHandler, true);

        /* ═══ 6) FORMULARIOS POR AJAX · crear / editar ficha (re-registrable) ═══ */
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
        function recargarContexto() {
            if (!FRAG) { window.location.reload(); return; }
            var inm = (SRVDBG && SRVDBG.inmuebleId) ? SRVDBG.inmuebleId : 'todos';
            var pag = (SRVDBG && SRVDBG.pagina) ? SRVDBG.pagina : 1;
            var esFicha = (SRVDBG && SRVDBG.modo === 'individual');
            var partes = ['inmueble_id=' + encodeURIComponent(inm || 'todos')];
            if (!esFicha) { partes.push('page_inmuebles=' + encodeURIComponent(pag || 1)); }
            if (esFicha) { window.__catExpectedFicha = true; }
            catCargar(FRAG + (FRAG.indexOf('?') === -1 ? '?' : '&') + partes.join('&'), null);
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
            DBG.log('submit ' + tipo + ' → ' + form.getAttribute('action') + ' (por AJAX, sin recargar)', 'info');
            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (el servidor redirigió al login); inicia sesión y reintenta');
                }
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { status: r.status, ok: r.ok, json: j }; });
                }
                return r.text().then(function (t) { return { status: r.status, ok: r.ok, html: t }; });
            }).then(function (res) {
                /* 422 · errores de validación → se pintan DENTRO del modal, sin cerrarlo */
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
                    DBG.log('⚠ validación (422): ' + (msgs.join(' | ') || 'sin detalle'), 'err');
                    catToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + (det ? ' · ' + det : ''));
                }
                /* Éxito (JSON o HTML tras redirect) → cerrar modal + recargar el catálogo por AJAX */
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                catToast(tipo === 'crear' ? 'Inmueble creado · actualizando catálogo…' : 'Cambios guardados · actualizando catálogo…', 'ok');
                DBG.log('guardado OK (' + (res.json ? 'JSON' : 'respuesta HTML tras redirect') + ') → recargando el catálogo por AJAX', 'ok');
                recargarContexto();
            }).catch(function (err) {
                DBG.log('⚠ guardado falló (' + err.message + ')', 'err');
                catToast('No se pudo guardar · ' + err.message, 'err');
                DBG.open();
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__catFormHandler);

        /* ═══ 7) ENLACE "Gestionar Propiedad" · CAT-v2 · interceptor REAL con respaldo:
           Caso 1 · el enlace vive dentro de #showRoot y el tab N2 (INM-v2-DBG) está activo
                    → se lo dejamos al N2 (él ya hace el swap AJAX de #showRoot).
           Caso 2 · hay #showRoot en la página pero el N2 no puede atenderlo (enlace fuera
                    del root o interceptor inactivo) → el catálogo hace el swap AJAX él mismo.
           Caso 3 · vista standalone (no existe #showRoot) → navegación nativa (lo correcto). */
        if (window.__catNavHandler) { document.removeEventListener('click', window.__catNavHandler, true); }
        window.__catNavHandler = function (e) {
            if (e.defaultPrevented) { return; } /* otro interceptor ya lo está manejando */
            if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || !a.closest('#catRoot')) { return; }
            if (!a.hasAttribute('data-cat-gestionar')) { return; }
            if (a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (a.closest('#showRoot') && window.__inmLinkHandler) {
                DBG.log('Gestionar → lo atiende el tab N2 (swap AJAX de #showRoot)', 'debug');
                return;
            }
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (show && fragN2) {
                var rid = 'todos';
                try {
                    var uG = new URL(a.href, window.location.href);
                    if (uG.searchParams.has('inmueble_id')) { rid = uG.searchParams.get('inmueble_id'); }
                } catch (eG0) { /* rid queda 'todos' */ }
                e.preventDefault();
                catGestionarFallback(rid, a.href);
                return;
            }
            DBG.log('Gestionar → navegación nativa (vista standalone: no hay #showRoot en la página)', 'debug');
        };
        document.addEventListener('click', window.__catNavHandler, true);

        /* ═══ 7b) CAT-v2 · respaldo "Gestionar" del catálogo:
           fetch al fragmento del tab N2 (ruta data-frag del propio #showRoot, la misma que usa
           el N2) + swap de #showRoot + re-ejecución de scripts (el script del N2 renace con sus
           listeners) + pushState + evento rsv:inmueble-cambiado + auto-tab de la ficha.      */
        function catGestionarFallback(rid, urlDestino) {
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (!show || !fragN2) { window.location.href = urlDestino; return; }
            var url = fragN2 + (fragN2.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(rid);
            DBG.log('Gestionar → respaldo del catálogo: fetch ' + url + ' → swap #showRoot (sin recargar)', 'info');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (el servidor redirigió al login); inicia sesión y reintenta');
                }
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + (det ? ' · ' + det : ''));
                    });
                }
                return r.text();
            }).then(function (html) {
                var tmp = document.createElement('div');
                tmp.innerHTML = html;
                var nuevo = tmp.querySelector('#showRoot');
                if (!nuevo) { throw new Error('el fragmento no trajo #showRoot'); }
                var actual = document.getElementById('showRoot');
                if (!actual) { throw new Error('#showRoot ya no está en el DOM'); }
                actual.replaceWith(nuevo);
                catRunScripts(nuevo);
                try { window.history.pushState({}, '', urlDestino); } catch (eG1) {}
                try { document.dispatchEvent(new CustomEvent('rsv:inmueble-cambiado', { detail: { inmuebleId: rid } })); } catch (eG2) {}
                /* Si #showRoot vive en otro tab, activarlo para que la ficha quede a la vista */
                try {
                    var paneD = nuevo.closest('.tab-pane');
                    if (paneD && paneD.id && !paneD.classList.contains('active')) {
                        var linkD = document.querySelector('[data-bs-target="#' + paneD.id + '"], [href="#' + paneD.id + '"]');
                        if (linkD && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                            bootstrap.Tab.getOrCreateInstance(linkD).show();
                            DBG.log('auto-tab · tab #' + paneD.id + ' activado para mostrar la ficha', 'ok');
                        }
                    }
                } catch (eG3) {}
                try { nuevo.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (eG4) {}
                DBG.log('expediente #' + rid + ' abierto por AJAX (respaldo del catálogo) · ' + html.length + ' bytes · sin recargar la página', 'ok');
            }).catch(function (eG5) {
                DBG.log('⚠ respaldo del catálogo falló (' + eG5.message + ') → navegación nativa', 'err');
                DBG.open();
                window.location.href = urlDestino;
            });
        }

        DBG.log('interceptores listos · paginación (window) + formularios (document) + Gestionar con respaldo AJAX', 'debug');
    })();
    </script>

</div>
