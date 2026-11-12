{{-- partials/nueva-reserva/buscador.blade.php · Cliente · Motor de Búsqueda Reactivo sin Reload (RV-v3-DBG) --}}
{{-- ══════════════════════════════════════════════════════════════════════
    NOVEDADES v27-CLI (REACTIVIDAD TOTAL SIN RECARGAR PÁGINA):
    · Cuadrícula responsiva adaptada al micro-diseño (tarjetas compactas).
    · Carrusel cuadrado integrado respetando tamaños y radios (3px/4px).
    · Calendario en Floating Popover minimalista con consulta API asíncrona.
    · Transición a Checkout 100% AJAX (actualiza URL, sincroniza #cliCheckoutRoot y cambia de pestaña sin recargar el navegador).
    · MODAL DE MAPA PREMIUM (Responsive, Aspect Ratio 4:3, Botones Waze/GMaps).
    · MODAL DE DETALLES INTEGRADO (Hero, Features, Gallery, Condiciones).
    · PANEL DE DEBUG RV-v3-DBG PERSISTENTE Y NORMALIZADO (Diseño TIP-v1.3)
   ══════════════════════════════════════════════════════════════════════ --}}

@php
    $phpDebugLogs = [];
    $inmuebles = collect();

    try {
        if (isset($propiedades) && $propiedades instanceof \Illuminate\Support\Collection && $propiedades->isNotEmpty()) {
            $inmuebles = $propiedades;
            $inmuebles->loadMissing(['multimedia', 'latestTarifa', 'tipoInmueble']);
        } else {
            $inmuebles = \App\Models\Rsv\CatalogoInmueble::with(['multimedia', 'latestTarifa', 'tipoInmueble'])
                ->where('active', 1)
                ->orderBy('name')
                ->limit(24)
                ->get();
        }

        $phpDebugLogs[] = ['ok', 'Buscador: Cargados ' . $inmuebles->count() . ' inmuebles.'];
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('cli.buscador: '.$e->getMessage());
        $phpDebugLogs[] = ['err', 'Error al cargar propiedades: ' . $e->getMessage()];
    }

    $apiDispUrl = '';
    try {
        if (\Illuminate\Support\Facades\Route::has('rsv.cliente.api.disponibilidad')) {
            $apiDispUrl = route('rsv.cliente.api.disponibilidad', ['id' => '__INM__']);
        } else {
            $apiDispUrl = url('/rsv/cliente-api-disponibilidad/__INM__');
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error resolviendo ruta API: ' . $e->getMessage()];
    }

    $rutaFragmento = '';
    foreach (['rsv.cliente.nueva-reserva.buscador.fragmento', 'rsv.cliente.fragmento'] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $rutaFragmento = route($rn);
            break;
        }
    }

    $dsJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $phpDebugJson = json_encode($phpDebugLogs, $dsJsonFlags);
@endphp

<div id="sheet-buscador-cliente">

    {{-- RV-v3-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="rvDebugToggle" title="Debug Buscador (RV-v3-DBG)">
        <span class="rv-dbg-led" id="rvDbgLed"></span>DBG<span class="rv-dbg-badge" id="rvDbgBadge">0</span>
    </div>
    <div id="rvDebugPanel" role="dialog" aria-label="Panel de debug del Buscador">
        <div class="rv-dbg-header" id="rvDbgHeader">
            <span>DEBUG · Buscador Cliente · RV-v3-DBG</span>
            <span class="rv-dbg-controls">
                <button type="button" class="rv-dbg-btn" id="rvDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
                <button type="button" class="rv-dbg-btn" id="rvDbgCopy">Copiar</button>
                <button type="button" class="rv-dbg-btn" id="rvDbgClear">Limpiar</button>
                <button type="button" class="rv-dbg-btn" id="rvDbgClose">✕</button>
            </span>
        </div>
        <div id="rvDebugBody"></div>
    </div>

    <div id="rvTabWrap" data-fragmento="{{ $rutaFragmento }}">

        <div class="reservas-soft p-3 bg-white rounded-3 border" id="cliBuscadorRoot"
             data-frag="{{ $rutaFragmento }}"
             data-fragkey="nueva-reserva-buscador"
             data-total="{{ $inmuebles->count() }}"
             style="border-color:#f1f5f9 !important;">

            <style>
                /* ── VARIABLES Y ESTILOS BASE ── */
                .reservas-soft {
                    --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                    --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
                    --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
                }

                .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
                .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }

                .reservas-soft .btn-green {
                    background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd);
                    font-size:8.5px; font-weight:600; padding:4px 8px; border-radius:3px;
                    cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:4px;
                    transition: all 0.2s;
                }
                .reservas-soft .btn-green:hover:not(:disabled) { background:#e3f1e9; color:#2e5c43; }
                .reservas-soft .btn-green:disabled { opacity: 0.5; cursor: not-allowed; }

                .reservas-soft .btn-outline {
                    background:#fff; color:var(--ink-3); border:1px solid var(--grid-2);
                    font-size:8.5px; font-weight:600; padding:4px 8px; border-radius:3px;
                    cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px;
                }
                .reservas-soft .btn-outline:hover { background:var(--head); color:var(--ink-2); border-color:var(--ink-4); }

                .rsv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px; align-items: start; }

                .rsv-min-card {
                    position: relative; display: flex; flex-direction: column; background: #fff;
                    border: 1px solid var(--grid-2); border-radius: 5px; padding: 8px;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }
                .rsv-min-card:hover { border-color: var(--accent-bd); box-shadow: 0 4px 12px rgba(0,0,0,.04); }

                .rsv-img-wrap {
                    position: relative; width: 100%; aspect-ratio: 1 / 1; border-radius: 3px; overflow: hidden; background: var(--head);
                    margin-bottom: 8px; border: 1px solid var(--grid);
                }
                .rsv-img-wrap img { width: 100%; height: 100%; object-fit: cover; }

                .rsv-img-wrap .carousel-control-prev, .rsv-img-wrap .carousel-control-next {
                    width: 18px; height: 18px; background: rgba(255,255,255,0.85); border-radius: 50%; top: 50%; transform: translateY(-50%);
                    margin: 0 4px; opacity: 0; transition: opacity 0.2s;
                }
                .rsv-img-wrap .carousel-control-prev-icon, .rsv-img-wrap .carousel-control-next-icon { width: 8px; height: 8px; filter: invert(1) grayscale(100) contrast(200); }
                .rsv-min-card:hover .carousel-control-prev, .rsv-min-card:hover .carousel-control-next { opacity: 1; }
                .carousel-indicators { margin-bottom: 4px; }
                .carousel-indicators [data-bs-target] { width: 4px; height: 4px; border-radius: 50%; margin: 0 2px; border: none; opacity: 0.6; }
                .carousel-indicators .active { opacity: 1; transform: scale(1.2); }

                .rsv-info-title { font-size: 9.5px; font-weight: 700; color: var(--ink-2); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .rsv-info-sub { font-size: 7.5px; color: var(--ink-4); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
                .rsv-info-price { font-size: 9.5px; font-weight: 700; color: var(--accent); margin-top: 4px; }
                .rsv-info-price span { font-weight: 500; font-size: 7.5px; color: var(--ink-3); }

                .rsv-badge {
                    font-size: 7px; color: var(--ink-2); background: var(--grid);
                    border: 1px solid var(--grid-2); padding: 2px 4px; border-radius: 3px;
                    white-space: nowrap; display: inline-flex; align-items: center; gap: 2px;
                }

                /* ── CALENDARIO POPOVER ── */
                .rsv-popover {
                    position: absolute; top: calc(100% + 6px); left: 50%; transform: translateX(-50%);
                    width: 100%; min-width: 240px; background: #fff; border-radius: 6px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid var(--grid-2);
                    z-index: 1050; padding: 10px; display: none; animation: popFadeIn 0.2s ease-out forwards;
                    cursor: default;
                }
                @media(max-width: 575px) { .rsv-popover { width: calc(100vw - 32px); max-width: 320px; } }
                @keyframes popFadeIn { from { opacity: 0; transform: translate(-50%, -5px); } to { opacity: 1; transform: translate(-50%, 0); } }
                .rsv-popover::before {
                    content: ''; position: absolute; top: -5px; left: 50%; transform: translateX(-50%) rotate(45deg);
                    width: 8px; height: 8px; background: #fff; border-top: 1px solid var(--grid-2); border-left: 1px solid var(--grid-2);
                }
                .popover-close { position: absolute; top: 8px; right: 8px; font-size: 10px; cursor: pointer; color: var(--ink-3); z-index: 10;}
                .popover-close:hover { color: var(--ink-2); }
                .xls-select { font-size: 8.5px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 4px 8px; width: 100%; outline: 0; margin-bottom: 8px; }

                .cal-title { font-weight: 700; font-size: 8.5px; color: var(--ink-2); text-transform: capitalize; }
                .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; text-align: center; font-size: 8.5px; margin-top: 8px; background: var(--grid); border: 1px solid var(--grid); border-radius: 3px;}
                .cal-header-day { font-weight: 700; color: var(--ink-3); font-size: 7px; padding: 2px 0; background: var(--head); text-transform: uppercase;}
                .cal-cell { padding: 4px 0; cursor: pointer; transition: background 0.1s; color: var(--ink-2); background: #fff;}
                .cal-cell:hover:not(.cal-past):not(.cal-occ):not(.cal-ali) { background: var(--head); }
                .cal-past { color: var(--ink-4); cursor: not-allowed; text-decoration: line-through; background: #fafbfd;}
                .cal-selected { background: var(--accent) !important; color: #fff !important; font-weight: bold; border-radius:2px;}
                .cal-range { background: var(--accent-bg); color: #0f5132; }
                .cal-legend { font-size: 7.5px; color: var(--ink-3); display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 8px;}
                .cal-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 2px; vertical-align: middle;}
                .cal-dot-free { background: #fff; border: 1px solid var(--grid-2); }
                .cal-dot-sel { background: var(--accent); }
                .cal-sel-info { font-size: 8px; color: var(--ink-2); background: var(--head); padding: 6px; border-radius: 3px; border: 1px solid var(--grid-2); margin-top: 8px; margin-bottom: 8px; line-height: 1.3;}

                /* ── ESTILOS DEL MODAL DE DETALLES ── */
                .rsv-modal-hero {
                    position: relative; background-size: cover; background-position: center; height: 250px;
                    display: flex; align-items: center; justify-content: center; text-align: center; color: white;
                    border-radius: 0.3rem 0.3rem 0 0;
                }
                .rsv-modal-hero::before {
                    content: ""; position: absolute; top:0; left:0; right:0; bottom:0;
                    background: rgba(0,0,0,0.5); border-radius: inherit;
                }
                .rsv-modal-hero h2 { position: relative; z-index: 1; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.6); }
                .rsv-modal-hero p { position: relative; z-index: 1; font-size: 14px; text-shadow: 1px 1px 3px rgba(0,0,0,0.6); margin:0; }

                .rsv-feature-card {
                    background: #fff; border: 1px solid var(--grid-2); padding: 15px; border-radius: 8px;
                    text-align: center; height: 100%; transition: transform 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
                }
                .rsv-feature-card:hover { transform: translateY(-5px); border-color: var(--accent-bd); }
                .rsv-feature-card i { font-size: 24px; color: var(--accent); margin-bottom: 10px; }
                .rsv-feature-card h6 { font-size: 13px; font-weight: 700; color: var(--ink-2); margin-bottom: 5px; }
                .rsv-feature-card p { font-size: 11px; color: var(--ink-3); margin: 0; line-height: 1.3; }

                .rsv-condiciones-box { background-color: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 20px; }
                .rsv-condiciones-box h5 { color: var(--accent); font-weight: 700; font-size: 16px; margin-bottom: 15px; }
                .rsv-condiciones-box h6 { color: var(--ink-2); font-weight: 700; font-size: 12px; margin-top: 15px; }
                .rsv-condiciones-box ul { padding-left: 20px; font-size: 12px; color: var(--ink-2); }
                .rsv-condiciones-box li { margin-bottom: 6px; }
                .rsv-modal-carousel img { height: 350px; object-fit: cover; border-radius: 8px; }

                /* ── ESTILOS DEL MODAL DE MAPA (Waze/GMaps) ── */
                .rsv-map-container { position: relative; width: 100%; padding-top: 75%; }
                .rsv-map-iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
                .btn-waze { background-color: #e4f4fb; color: #007bb6; border: 1px solid #bce1f1; transition: all 0.2s; }
                .btn-waze:hover { background-color: #007bb6; color: #fff; }
                .btn-gmaps { background-color: #fce8e6; color: #d93025; border: 1px solid #f6c8c4; transition: all 0.2s; }
                .btn-gmaps:hover { background-color: #d93025; color: #fff; }

                /* ── PANEL DEBUG (TIP-v1.3) ── */
                #rvDebugToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
                #rvDebugToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
                .rv-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
                .rv-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: rvDbgBlink 1s steps(2,start) infinite; }
                @keyframes rvDbgBlink { 50% { opacity: .35; } }
                .rv-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
                #rvDebugPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
                .rv-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
                .rv-dbg-controls { display: flex; gap: 4px; }
                .rv-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
                .rv-dbg-btn:hover { background: #475569; }
                #rvDebugBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
                .rv-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
                .rv-dbg-time { color: #64748b; margin-right: 4px; }
                .rv-dbg-info { color: #93c5fd; } .rv-dbg-ok { color: #86efac; }
                .rv-dbg-warn { color: #fde68a; } .rv-dbg-err { color: #fca5a5; }
                .rv-dbg-net  { color: #c4b5fd; } .rv-dbg-ev  { color: #f9a8d4; }
            </style>

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:6px;">
                <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
                    <i class="bi bi-houses me-1" style="font-size:8px; color:var(--accent);"></i> Propiedades Disponibles
                </h6>
                <span class="sub">Descubre y reserva en línea. (Total: {{ $inmuebles->count() }})</span>
            </div>

            {{-- CUADRÍCULA --}}
            <div class="rsv-grid pb-4">
                @forelse($inmuebles as $inm)
                    @php
                        $tarifaObj = $inm->latestTarifa;
                        $precioMin = optional($tarifaObj)->precio_minimo_reserva;
                        $diasMax   = optional($tarifaObj)->dias_maximos;
                        $maxAnual  = optional($tarifaObj)->max_reservas_anuales_cliente;
                        $mapQuery  = urlencode($inm->ubicacion ?? $inm->city ?? $inm->name);

                        $heroImg = $inm->multimedia->first() ? $inm->multimedia->first()->url_archivo : asset('img/9.jpeg');
                    @endphp

                    <div class="rsv-min-card" data-inmueble-id="{{ $inm->id }}">
                        {{-- Carrusel Miniatura --}}
                        <div class="rsv-img-wrap">
                            <div id="carMin_{{ $inm->id }}" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="false">
                                @if($inm->multimedia->count() > 1)
                                <div class="carousel-indicators">
                                    @foreach($inm->multimedia as $idx => $media)
                                        <button type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}"></button>
                                    @endforeach
                                </div>
                                @endif
                                <div class="carousel-inner h-100">
                                    @forelse($inm->multimedia as $idx => $media)
                                        <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                            <img src="{{ $media->url_archivo }}" alt="{{ $inm->name }}" loading="lazy">
                                        </div>
                                    @empty
                                        <div class="carousel-item h-100 active d-flex align-items-center justify-content-center bg-light">
                                            <i class="bi bi-house-door text-secondary opacity-25" style="font-size: 2rem;"></i>
                                        </div>
                                    @endforelse
                                </div>
                                @if($inm->multimedia->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="rsv-info-title">{{ $inm->name }}</div>
                        <div class="rsv-info-sub"><i class="bi bi-geo-alt"></i> {{ $inm->city ?? 'S/U' }} · Cap. {{ $inm->capacidad_maxima ?? '-' }}</div>

                        <div class="rsv-info-price">
                            {{ $precioMin ? ('$'.number_format($precioMin, 0)) : '--' }} <span>/ Mín. Reserva</span>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @if($diasMax)
                                <span class="rsv-badge"><i class="bi bi-clock-history"></i> Máx {{ $diasMax }} días</span>
                            @endif
                            @if($maxAnual)
                                <span class="rsv-badge"><i class="bi bi-person-check"></i> Máx {{ $maxAnual }} al año</span>
                            @endif
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <button type="button" class="btn-outline flex-grow-1 js-rv-open-modal"
                                    data-modal-type="detalle"
                                    data-inmueble-id="{{ $inm->id }}"
                                    data-inmueble-nombre="{{ $inm->name }}"
                                    data-bs-toggle="modal" data-bs-target="#modalDetalle_{{ $inm->id }}">
                                <i class="bi bi-info-circle"></i> Info
                            </button>
                            <button type="button" class="btn-outline flex-grow-1 js-rv-open-modal"
                                    data-modal-type="mapa"
                                    data-inmueble-id="{{ $inm->id }}"
                                    data-inmueble-nombre="{{ $inm->name }}"
                                    data-bs-toggle="modal" data-bs-target="#modalMapa_{{ $inm->id }}">
                                <i class="bi bi-map"></i> Mapa
                            </button>
                            <button type="button" class="btn-green flex-grow-1 w-100" onclick="window.rvTabApi.abrirCalendarioInmueble(event, {{ $inm->id }})">
                                <i class="bi bi-calendar3"></i> Fechas
                            </button>
                        </div>

                        {{-- Popover Calendario --}}
                        <div id="cal-box-{{ $inm->id }}" class="rsv-popover" onclick="event.stopPropagation()">
                            <i class="bi bi-x-lg popover-close" onclick="window.rvTabApi.closePopovers()"></i>
                            <form action="{{ url()->current() }}" method="GET" onsubmit="window.rvTabApi.validarReservaCliente(event, this, {{ $inm->id }}, '{{ addslashes($inm->name) }}')">
                                <input type="hidden" name="inmueble_id" value="{{ $inm->id }}">
                                <input type="hidden" name="fi" id="fldFechaIni_{{ $inm->id }}" required>
                                <input type="hidden" name="ff" id="fldFechaFin_{{ $inm->id }}" required>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <button type="button" class="btn-outline" style="padding:1px 6px;" onclick="window.rvTabApi.cambiarMes(-1, {{ $inm->id }})">‹</button>
                                    <span id="calMonthLbl_{{ $inm->id }}" class="cal-title">Cargando...</span>
                                    <button type="button" class="btn-outline" style="padding:1px 6px;" onclick="window.rvTabApi.cambiarMes(1, {{ $inm->id }})">›</button>
                                </div>
                                <div id="miniCalGrid_{{ $inm->id }}" class="cal-grid"></div>
                                <div class="cal-legend">
                                    <span class="me-2"><span class="cal-dot cal-dot-free"></span> Libre</span>
                                    <span class="me-2"><span class="cal-dot cal-dot-sel"></span> Sel</span>
                                </div>
                                <div id="calSelInfo_{{ $inm->id }}" class="cal-sel-info">Selecciona tu llegada.</div>
                                <label class="val mb-1">Huéspedes</label>
                                <select name="huespedes" class="xls-select">
                                    <option value="2">1 a 2 huéspedes</option>
                                    <option value="4">3 a 4 huéspedes</option>
                                    <option value="6">5 a más huéspedes</option>
                                </select>
                                <button type="submit" class="btn-green w-100 mt-1" id="btnReserva_{{ $inm->id }}" disabled>Continuar a Checkout <i class="bi bi-arrow-right"></i></button>
                            </form>
                        </div>
                    </div>

                    {{-- MODAL DE MAPA PREMIUM --}}
                    <div class="modal fade" id="modalMapa_{{ $inm->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg" style="border: none; border-radius: 12px; overflow: hidden;">
                                <div class="modal-header bg-light border-bottom-0 pb-2 pt-3 px-3">
                                    <h5 class="modal-title fw-bold" style="color: var(--ink-2); font-size: 14px;">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Ubicación del Inmueble
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="rsv-map-container">
                                        <iframe class="rsv-map-iframe" src="https://maps.google.com/maps?q={{ $mapQuery }}&t=m&z=15&output=embed" allowfullscreen loading="lazy"></iframe>
                                    </div>
                                    <div class="p-3 bg-white text-center">
                                        <h6 class="fw-bold mb-1" style="font-size: 13px; color: var(--ink-2);">{{ $inm->name }}</h6>
                                        <p class="text-muted mb-3" style="font-size: 11px;">
                                            <i class="bi bi-info-circle me-1"></i> {{ $inm->ubicacion ?? ($inm->city ?? 'Ubicación exacta disponible al confirmar tu reserva.') }}
                                        </p>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="https://maps.google.com/?q={{ $mapQuery }}" target="_blank" class="btn btn-gmaps btn-sm px-3 py-2 fw-bold js-rv-map-ext" data-provider="Google Maps" style="font-size: 10px; border-radius: 6px;">
                                                <i class="bi bi-google me-1"></i> Google Maps
                                            </a>
                                            <a href="https://waze.com/ul?q={{ $mapQuery }}" target="_blank" class="btn btn-waze btn-sm px-3 py-2 fw-bold js-rv-map-ext" data-provider="Waze" style="font-size: 10px; border-radius: 6px;">
                                                <i class="bi bi-car-front-fill me-1"></i> Waze
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL DE DETALLES COMPLETO --}}
                    <div class="modal fade" id="modalDetalle_{{ $inm->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content" style="border: none; border-radius: 8px; overflow: hidden;">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white" data-bs-dismiss="modal" style="z-index: 10; opacity: 0.8;"></button>
                                <div class="modal-body p-0">
                                    <div class="rsv-modal-hero" style="background-image: url('{{ $heroImg }}');">
                                        <div class="p-4">
                                            <h2 class="mb-2">{{ $inm->name }}</h2>
                                            <p>Disfrute de un apartamento amoblado con todas las comodidades para una estadía inolvidable.</p>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-white">
                                        <h5 class="text-center mb-4 fw-bold" style="color: var(--accent);">Características Exclusivas</h5>
                                        <div class="row g-3 mb-5">
                                            <div class="col-6 col-md-4">
                                                <div class="rsv-feature-card">
                                                    <i class="bi bi-moon-stars"></i>
                                                    <h6>Confort Inigualable</h6>
                                                    <p>Habitaciones diseñadas para ofrecer el máximo confort y descanso.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="rsv-feature-card">
                                                    <i class="bi bi-wifi"></i>
                                                    <h6>Internet Veloz</h6>
                                                    <p>Conexión Wi-Fi gratuita en todo el apartamento.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="rsv-feature-card">
                                                    <i class="bi bi-cup-hot"></i>
                                                    <h6>Cocina Equipada</h6>
                                                    <p>Electrodomésticos modernos y utensilios listos para usar.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="text-center mb-3 fw-bold" style="color: var(--accent);">Galería de Fotos</h5>
                                        <div id="carouselModal_{{ $inm->id }}" class="carousel slide rsv-modal-carousel mb-5 shadow-sm rounded" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @forelse($inm->multimedia as $idx => $media)
                                                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                                        <img src="{{ $media->url_archivo }}" class="d-block w-100" alt="Foto">
                                                    </div>
                                                @empty
                                                    <div class="carousel-item active">
                                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 350px;">
                                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                            @if($inm->multimedia->count() > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselModal_{{ $inm->id }}" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon p-3 bg-dark rounded-circle bg-opacity-50"></span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#carouselModal_{{ $inm->id }}" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon p-3 bg-dark rounded-circle bg-opacity-50"></span>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="rsv-condiciones-box">
                                            <h5 class="text-center"><i class="bi bi-info-square me-2"></i>Lineamientos de Uso</h5>
                                            <p class="text-center text-muted" style="font-size:12px;">Con el fin de garantizar un uso adecuado y organizado, se establecen las siguientes condiciones:</p>
                                            <h6><i class="bi bi-calendar-check text-primary me-1"></i> Reservas y Pagos</h6>
                                            <ul>
                                                <li>Las reservas deben realizarse con anticipación a través de la plataforma.</li>
                                                <li>El asociado debe estar presente durante la estadía (no terceros).</li>
                                                <li>Una vez pre-reservado, tendrá <strong>3 días</strong> para realizar el pago de administración.</li>
                                                <li>El aporte cubre únicamente gastos de aseo y administración (el alquiler es gratis).</li>
                                            </ul>
                                            <h6><i class="bi bi-house-exclamation text-warning me-1"></i> Condiciones Generales</h6>
                                            <ul>
                                                <li><strong>Estadía máxima:</strong> 5 días / 4 noches.</li>
                                                <li><strong>Capacidad máxima:</strong> {{ $inm->capacidad_maxima ?? '6' }} personas.</li>
                                                <li>No se permite el ingreso de mascotas al edificio.</li>
                                                <li>No hay devoluciones de dinero. Reprogramaciones solo por fuerza mayor.</li>
                                                <li>El apartamento debe devolverse en el mismo óptimo estado en que se entrega.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer" style="background: var(--head);">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar Detalles</button>
                                    <button type="button" class="btn btn-success btn-sm px-4" data-bs-dismiss="modal" onclick="setTimeout(() => { window.rvTabApi.abrirCalendarioInmueble(new Event('click'), {{ $inm->id }}) }, 350)">
                                        <i class="bi bi-calendar-date me-1"></i> Seleccionar Fechas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-friendly p-4 text-center">
                            <i class="bi bi-house-x d-block mb-2" style="font-size:24px; color:var(--ink-4);"></i>
                            <span class="val">No hay propiedades publicadas.</span>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<script type="application/json" id="rvPhpDebug">{!! $phpDebugJson !!}</script>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL RV-v3-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.RvDbg && window.RvDbg.alive && window.RvDbg.alive())
        ? window.RvDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-buscador-cliente') || document.body;
                if (!$('rvDebugToggle')) {
                    var t = document.createElement('div');
                    t.id = 'rvDebugToggle'; t.title = 'Debug Buscador (RV-v3-DBG)';
                    t.innerHTML = '<span class="rv-dbg-led" id="rvDbgLed"></span>DBG<span class="rv-dbg-badge" id="rvDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('rvDebugPanel')) {
                    var p = document.createElement('div');
                    p.id = 'rvDebugPanel';
                    p.innerHTML = '<div class="rv-dbg-header" id="rvDbgHeader">' +
                        '<span>DEBUG · Buscador Cliente · RV-v3-DBG</span>' +
                        '<span class="rv-dbg-controls">' +
                        '<button type="button" class="rv-dbg-btn" id="rvDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="rv-dbg-btn" id="rvDbgCopy">Copiar</button>' +
                        '<button type="button" class="rv-dbg-btn" id="rvDbgClear">Limpiar</button>' +
                        '<button type="button" class="rv-dbg-btn" id="rvDbgClose">✕</button>' +
                        '</span></div><div id="rvDebugBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('rvDebugToggle'), p = $('rvDebugPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('rvDebugBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'rv-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="rv-dbg-time">[' + hora() + ']</span><span class="rv-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('rvDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('rvDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('rvDebugPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('rvDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('rvDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('rvDebugPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('rvDebugPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('rvDebugBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('rvDebugBody'); if (!b) return;
                    var txt = '=== DEBUG Buscador RV-v3-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('rvDebugToggle'); }
            };
            window.RvDbg = api;
            return api;
        })();

    DBG.ensure();
    var rvdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#rvDebugToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#rvDebugPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO DEL BUSCADOR SIN RECARGAR PÁGINA ═══
    window.rvReloadFragment = function (motivo) {
        var root = document.getElementById('cliBuscadorRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'nueva-reserva-buscador';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', 'nueva-reserva');
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        rvdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

        return fetch(urlObj.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            cache: 'no-store'
        })
        .then(function (r) {
            return r.text().then(function (html) {
                return { ok: r.ok, status: r.status, html: html, ms: Math.round(performance.now() - t0) };
            });
        })
        .then(function (res) {
            if (!res.ok) {
                rvdbg('Error HTTP ' + res.status + ' al refrescar Buscador (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliBuscadorRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                rvdbg('Fragmento del Buscador actualizado (' + res.ms + 'ms) · inmuebles=' + tot, 'ok');
            } else {
                rvdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliBuscadorRoot', 'warn');
            }
        })
        .catch(function (err) {
            rvdbg('Excepción recargando Buscador: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (window.__rvDbgDocHandler) document.removeEventListener('click', window.__rvDbgDocHandler);
    window.__rvDbgDocHandler = function (e) {
        var t = e.target; if (!t || !t.closest) return;
        if (t.closest('#rvDebugToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#rvDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#rvDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#rvDbgCopy')) { e.stopPropagation(); DBG.copy(); rvdbg('Bitácora copiada al portapapeles', 'info'); return; }
        if (t.closest('#rvDbgRefresh')) {
            e.stopPropagation();
            window.rvReloadFragment('manual-btn-dbg');
            return;
        }

        // Trazabilidad de modales Info y Mapa
        var modBtn = t.closest('.js-rv-open-modal');
        if (modBtn) {
            var mTipo = modBtn.getAttribute('data-modal-type') || 'modal';
            var mNom  = modBtn.getAttribute('data-inmueble-nombre') || 'Inmueble';
            rvdbg('Click: Abriendo modal de ' + mTipo + ' para "' + mNom + '"', 'ev');
            return;
        }

        // Trazabilidad de botones Waze / Google Maps
        var extMap = t.closest('.js-rv-map-ext');
        if (extMap) {
            var prov = extMap.getAttribute('data-provider') || 'GPS';
            rvdbg('Click: Abriendo ruta externa en ' + prov, 'net');
            return;
        }

        // Cerrar popovers al hacer clic fuera de las tarjetas
        if (!t.closest('.rsv-min-card')) {
            closePopovers();
        }
    };
    document.addEventListener('click', window.__rvDbgDocHandler);

    if (window.__rvDbgDblHandler) document.removeEventListener('dblclick', window.__rvDbgDblHandler);
    window.__rvDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#rvDbgHeader')) DBG.close();
    };
    document.addEventListener('dblclick', window.__rvDbgDblHandler);

    /* ═══ LÓGICA DE CALENDARIO Y POPOVERS ═══ */
    const RSV_CAL = {};
    const NOMBRES_MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const DIAS_SEMANA = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];
    const ENDPOINT_BASE = '{{ $apiDispUrl }}';

    function calFmt(d) { return d.getFullYear() + '-' + ('0'+(d.getMonth()+1)).slice(-2) + '-' + ('0'+d.getDate()).slice(-2); }

    function closePopovers() {
        document.querySelectorAll('.rsv-popover').forEach(el => el.style.display = 'none');
    }

    function abrirCalendarioInmueble(e, id) {
        if (e && e.stopPropagation) e.stopPropagation();
        closePopovers();

        var box = document.getElementById('cal-box-' + id);
        if (!box) return;

        var card = box.closest('.rsv-min-card');
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });

        box.style.display = 'block';
        if (!RSV_CAL[id]) {
            var hoy = new Date();
            RSV_CAL[id] = { year: hoy.getFullYear(), month: hoy.getMonth(), start: null, end: null, occupied: [] };
            rvdbg('Inicializando calendario flotante para inmueble ID: ' + id, 'info');
            fetchOccupied(id);
        } else {
            rvdbg('Reabriendo calendario flotante inmueble ID: ' + id, 'ev');
            pintarCalendario(id);
        }
    }

    function fetchOccupied(id) {
        var state = RSV_CAL[id];
        var url = ENDPOINT_BASE.replace('__INM__', id) + '?year=' + state.year + '&month=' + (state.month + 1);
        var t0 = performance.now();
        rvdbg('Consultando disponibilidad API: ' + url, 'net');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.json())
            .then(data => {
                var ms = Math.round(performance.now() - t0);
                if (data.success) {
                    state.occupied = data.ocupadas || [];
                    rvdbg('Disponibilidad cargada (inmueble #' + id + ' · ' + ms + 'ms). Días ocupados: ' + state.occupied.length, 'ok');
                    pintarCalendario(id);
                } else {
                    rvdbg('Error en API de disponibilidad para inmueble #' + id + ' (' + ms + 'ms)', 'err');
                }
            }).catch(e => {
                rvdbg('Error de red al consultar disponibilidad: ' + e.message, 'err');
            });
    }

    function cambiarMes(direccion, id) {
        let state = RSV_CAL[id];
        state.month += direccion;
        if (state.month < 0) { state.month = 11; state.year--; }
        else if (state.month > 11) { state.month = 0; state.year++; }
        rvdbg('Cambiando mes calendario inmueble #' + id + ' a: ' + state.year + '-' + (state.month + 1), 'ev');
        fetchOccupied(id);
    }

    function pintarCalendario(id) {
        let state = RSV_CAL[id];
        let grid = document.getElementById('miniCalGrid_' + id);
        let lbl = document.getElementById('calMonthLbl_' + id);
        if (!grid || !lbl) return;
        lbl.innerText = NOMBRES_MESES[state.month] + ' ' + state.year;

        let primerDiaMes = new Date(state.year, state.month, 1);
        let ultimoDiaMes = new Date(state.year, state.month + 1, 0);
        let diaSemanaInicio = primerDiaMes.getDay();
        let hoy = new Date(); hoy.setHours(0,0,0,0);

        let html = '';
        DIAS_SEMANA.forEach(d => { html += `<div class="cal-header-day">${d}</div>`; });
        for (let i = 0; i < diaSemanaInicio; i++) { html += `<div></div>`; }

        for (let dia = 1; dia <= ultimoDiaMes.getDate(); dia++) {
            let fechaActual = new Date(state.year, state.month, dia);
            fechaActual.setHours(0,0,0,0);
            let timeActual = fechaActual.getTime();
            let esPasado = timeActual < hoy.getTime();
            let fechaISO = calFmt(fechaActual);
            let esOcupado = state.occupied.includes(fechaISO);

            let clase = 'cal-cell';
            if (esPasado || esOcupado) clase += ' cal-past';
            else {
                if (state.start && timeActual === state.start.getTime()) clase += ' cal-selected';
                else if (state.end && timeActual === state.end.getTime()) clase += ' cal-selected';
                else if (state.start && state.end && timeActual > state.start.getTime() && timeActual < state.end.getTime()) clase += ' cal-range';
            }

            if (esPasado || esOcupado) html += `<div class="${clase}">${dia}</div>`;
            else html += `<div class="${clase}" onclick="window.rvTabApi.seleccionarDia(event, ${id}, '${fechaISO}')">${dia}</div>`;
        }
        grid.innerHTML = html;
        actualizarPanelInfo(id);
    }

    function seleccionarDia(e, id, fechaISO) {
        e.stopPropagation();
        let state = RSV_CAL[id];
        let dateObj = new Date(fechaISO + 'T00:00:00');

        if (!state.start || (state.start && state.end)) {
            state.start = dateObj; state.end = null;
            rvdbg('Inicio de selección inmueble #' + id + ': ' + fechaISO, 'ev');
        } else if (state.start && !state.end) {
            if (dateObj.getTime() <= state.start.getTime()) {
                state.start = dateObj;
                rvdbg('Reiniciando fecha inicio inmueble #' + id + ': ' + fechaISO, 'ev');
            } else {
                let ok = true;
                let cur = new Date(state.start.getTime());
                while (cur <= dateObj) {
                    if (state.occupied.includes(calFmt(cur))) { ok = false; break; }
                    cur.setDate(cur.getDate() + 1);
                }
                if (ok) {
                    state.end = dateObj;
                    rvdbg('Rango seleccionado inmueble #' + id + ': ' + calFmt(state.start) + ' al ' + fechaISO, 'ok');
                } else {
                    alert('El rango incluye fechas ya reservadas.');
                    state.start = dateObj;
                    rvdbg('Error: Selección bloqueada por días intermedios ocupados.', 'warn');
                }
            }
        }
        pintarCalendario(id);
    }

    function actualizarPanelInfo(id) {
        let state = RSV_CAL[id];
        let panelInfo = document.getElementById('calSelInfo_' + id);
        let inputFi = document.getElementById('fldFechaIni_' + id);
        let inputFf = document.getElementById('fldFechaFin_' + id);
        let btn = document.getElementById('btnReserva_' + id);
        if (!panelInfo || !inputFi || !inputFf || !btn) return;

        const fStr = (d) => ('0'+d.getDate()).slice(-2) + '/' + ('0'+(d.getMonth()+1)).slice(-2);

        if (!state.start) {
            panelInfo.innerHTML = '<b style="color:var(--accent);">Paso 1:</b> Selecciona tu llegada.';
            inputFi.value = ''; inputFf.value = ''; btn.disabled = true;
        } else if (state.start && !state.end) {
            panelInfo.innerHTML = 'Llegada: <b>' + fStr(state.start) + '</b>. Selecciona la salida.';
            inputFi.value = calFmt(state.start); inputFf.value = ''; btn.disabled = true;
        } else if (state.start && state.end) {
            let noches = Math.round((state.end.getTime() - state.start.getTime()) / 86400000);
            panelInfo.innerHTML = '<b>Rango:</b> ' + fStr(state.start) + ' al ' + fStr(state.end) + ' ('+noches+' n).';
            inputFi.value = calFmt(state.start); inputFf.value = calFmt(state.end); btn.disabled = false;
        }
    }

    // ═══ PASO AL CHECKOUT 100% REACTIVO SIN RECARGAR PÁGINA ═══
    function validarReservaCliente(e, form, inmId, inmNombre) {
        var fi = form.querySelector('input[name="fi"]').value;
        var ff = form.querySelector('input[name="ff"]').value;
        var huesp = form.querySelector('select[name="huespedes"]')?.value || '2';

        if (!fi || !ff) {
            e.preventDefault();
            alert('Selecciona el rango en el calendario.');
            rvdbg('Intento de envío sin rango de fechas completo.', 'warn');
            return;
        }

        localStorage.setItem('rsv_cliente_panel_parent', '#cli-nueva');
        localStorage.setItem('rsv_cliente_panel_child', '#nr-sub-checkout');

        // Si existe el contenedor de Checkout en el DOM, navegamos por AJAX sin recargar la página
        var chkRoot = document.getElementById('cliCheckoutRoot');
        if (chkRoot) {
            e.preventDefault();
            closePopovers();

            try {
                var navUrl = new URL(window.location.href);
                navUrl.searchParams.set('inmueble_id', inmId);
                navUrl.searchParams.set('fi', fi);
                navUrl.searchParams.set('ff', ff);
                navUrl.searchParams.set('huespedes', huesp);
                window.history.pushState({}, document.title, navUrl.toString());
            } catch (err) {}

            chkRoot.setAttribute('data-inmueble-id', inmId);
            rvdbg('Avanzando a Checkout sin recargar página · "' + inmNombre + '" (ID ' + inmId + ') · ' + fi + ' → ' + ff, 'ok');

            var fragBase = chkRoot.getAttribute('data-frag') || window.location.pathname;
            var fragKey  = chkRoot.getAttribute('data-fragkey') || 'nueva-checkout';
            var urlChk   = new URL(fragBase, window.location.origin);
            urlChk.searchParams.set('tab', fragKey);
            urlChk.searchParams.set('fragmento', fragKey);
            urlChk.searchParams.set('inmueble_id', inmId);
            urlChk.searchParams.set('fi', fi);
            urlChk.searchParams.set('ff', ff);
            urlChk.searchParams.set('huespedes', huesp);
            urlChk.searchParams.set('_t', Date.now());

            var t0 = performance.now();
            rvdbg('GET Checkout Fragmento → ' + urlChk.pathname + urlChk.search, 'net');

            fetch(urlChk.toString(), {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                cache: 'no-store'
            })
            .then(function (r) {
                return r.text().then(function (html) {
                    return { ok: r.ok, status: r.status, html: html, ms: Math.round(performance.now() - t0) };
                });
            })
            .then(function (res) {
                if (res.ok) {
                    var doc = new DOMParser().parseFromString(res.html, 'text/html');
                    var nuevoChk = doc.getElementById('cliCheckoutRoot');
                    if (nuevoChk) {
                        chkRoot.replaceWith(nuevoChk);
                        rvdbg('Checkout sincronizado (' + res.ms + 'ms) · abriendo pestaña #nr-sub-checkout', 'ok');
                        var tabChk = document.querySelector('[data-bs-target="#nr-sub-checkout"]');
                        if (tabChk) tabChk.click();
                        return;
                    }
                }
                // Fallback seguro a navegación tradicional si el endpoint devuelve otra estructura
                window.location.href = navUrl.toString();
            })
            .catch(function () {
                window.location.href = window.location.pathname + '?inmueble_id=' + inmId + '&fi=' + fi + '&ff=' + ff + '&huespedes=' + huesp;
            });
            return;
        }

        rvdbg('Avanzando al Checkout (GET tradicional) para "' + inmNombre + '" (ID ' + inmId + ') · fechas: ' + fi + ' al ' + ff, 'ok');
    }

    window.rvTabApi = { abrirCalendarioInmueble, cambiarMes, seleccionarDia, validarReservaCliente, closePopovers };

    /* Cargar logs de PHP en el panel JS */
    try {
        var phpLogsEl = document.getElementById('rvPhpDebug');
        if (phpLogsEl && !phpLogsEl.getAttribute('data-loaded')) {
            phpLogsEl.setAttribute('data-loaded', '1');
            var phpLogs = JSON.parse(phpLogsEl.textContent || '[]');
            phpLogs.forEach(function (l) { rvdbg('PHP: ' + l[1], l[0]); });
        }
    } catch (e) {}

    rvdbg('Buscador RV-v3-DBG inicializado correctamente · inmuebles=' + {{ $inmuebles->count() }}, 'ok');
})();
</script>
