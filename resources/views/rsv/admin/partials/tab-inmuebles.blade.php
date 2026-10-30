{{-- resources/views/rsv/admin/partials/tab-inmuebles.blade.php · INM-v3-DBG · NIVEL 2 (tab de inmuebles del dashboard) --}}
{{-- INM-v3-DBG · CAMBIO INM-v3-1 · Panel de debug rediseñado con el estilo "terminal" aprobado (mismo de TIP-v1.3): --}}
{{--    la pestaña circular 🐛 de 40px pasa a TAB DBG textual de 16px de alto (monospace 8px + LED de estado --}}
{{--    verde/rojo pulsante + badge numérico), fundida SIEMPRE en la esquina inferior-derecha (right:0 · bottom:0 --}}
{{--    · nunca se apila hacia arriba) y el panel abre como CAJÓN pegado al mismo borde (radius solo sup-izq, --}}
{{--    bordes solo arriba/izquierda). El apilado con los demás toggles de la página se mide en px REALES --}}
{{--    (getBoundingClientRect · ocultos 0×0 descartados) y la regla exacta vive como "SPEC DE DISTANCIAS" --}}
{{--    dentro del CSS. La pestaña sigue siendo LOCAL de esta vista: muere con el fragmento, no captura --}}
{{--    errores ajenos y el panel sigue registrando SOLO operaciones de tab-inmuebles. --}}
{{-- NOVEDADES v2 · 3) AUTO-TAB: si la página llega por navegación normal con ?inmueble_id=... --}}
{{--    (p.ej. tras un "Gestionar" que recargó todo), el tab que contiene esta vista se activa SOLO y la --}}
{{--    ficha queda visible sin buscar el tab a mano. Si la vista no vive en un .tab-pane queda log en el panel. --}}
{{--    4) El apilado del panel también cuenta al panel del catálogo (catDbgToggle). --}}
{{-- NOVEDADES v1 · 1) Navegación SIN recarga: los enlaces internos de #showRoot que apuntan al dashboard con --}}
{{--    inmueble_id (botón "Gestionar" y "Volver al Catálogo") se abren por AJAX: fetch al fragmento admin.inmuebles.fragmento --}}
{{--    + swap de #showRoot; si el fragmento falla, respaldo de navegación nativa. 2) Panel de Errores LOCAL estilo Huéspedes --}}
{{--    (pestaña abajo-derecha + Copiar/Limpiar/cerrar): registra SOLO operaciones de esta vista; el colector de contexto del --}}
{{--    servidor (marca INM-v3-DBG) viaja al panel como JSON. --}}
{{-- Arquitectura: el script viaja DENTRO de #showRoot (renace con listeners tras cada swap) y el panel va ANTES del root --}}
{{-- (sobrevive a swaps y evita duplicados). Tras cada swap se dispara el evento document "rsv:inmueble-cambiado" con --}}
{{-- detail.inmuebleId por si el dashboard quiere refrescar otras zonas. --}}
{{-- MODO GLOBAL = tabla resumen con TODAS las relaciones + botón Gestionar --}}
{{-- MODO INDIVIDUAL = expediente SOLO LECTURA (tarifa/galeria se incluyen con soloLectura=true: tablas, sin botones) --}}

@php
    // ── Detección de modo (idéntico a tarifas) ──
    $modoGlobal = !isset($inmueble) || !$inmueble;

    if ($modoGlobal) {
        // ═══ GLOBAL: usar $inmueblesGrid (viene COMPLETO del controlador en modo 'todos').
        // NUNCA usar $listaInmuebles para tablas: es SOLO id+name (selector ligero).
        if (isset($inmueblesGrid) && $inmueblesGrid) {
            $expedientes = $inmueblesGrid;

            // Si es paginador, operar sobre su colección interna
            $col = method_exists($expedientes, 'getCollection')
                ? $expedientes->getCollection()
                : $expedientes;

            // Red de seguridad por si el controlador no trajo el tipo (no-op si ya viene)
            $col->loadMissing('tipoInmueble');
        } else {
            // Fallback: consulta fresca completa
            $expedientes = \App\Models\Rsv\CatalogoInmueble::query()
                ->with(['tipoInmueble', 'latestTarifa', 'tarifasTemporadas', 'multimedia'])
                ->withCount(['reservas', 'bloqueosCalendario'])
                ->orderBy('name')
                ->get();
        }
    } else {
        // ═══ INDIVIDUAL · mismas relaciones sobre el inmueble único (solo lectura) ═══
        $inmueble->loadMissing(['tipoInmueble', 'multimedia', 'tarifasTemporadas', 'latestTarifa']);
        $inmueble->loadCount(['reservas', 'bloqueosCalendario']);

        // Galería ordenada: portada primero
        $galeria = $inmueble->multimedia->sortByDesc('es_portada')->values();

        // Última tarifa: prefiere latestTarifa, con fallback al historial
        $ultimaTarifa = $inmueble->latestTarifa
            ?: $inmueble->tarifasTemporadas->sortByDesc('fecha_inicio')->first();
    }

    // Helper: portada de un inmueble (portada marcada primero)
    $portadaDe = function ($prop) {
        return $prop->multimedia->sortByDesc('es_portada')->first();
    };

    // Helper: última tarifa de un inmueble (latestTarifa → fallback historial)
    $ultimaTarifaDe = function ($prop) {
        if ($prop->relationLoaded('latestTarifa') && $prop->latestTarifa) {
            return $prop->latestTarifa;
        }
        return $prop->tarifasTemporadas->sortByDesc('fecha_inicio')->first();
    };
@endphp

{{-- Escala px del sistema (idéntica a tarifas/galería) --}}
<style>
    .show-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }

    /* ═══ TABLA GLOBAL: filas de 20px exactas (idéntica a tarifas) ═══ */
    .show-soft .table > :not(caption) > * > * {
        padding: 2px 6px; height: 20px;
        line-height: 1.1; vertical-align: middle;
    }
    .show-soft .table thead th {
        font-size: 9px; font-weight: 600;
        letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important;
        white-space: nowrap;
    }
    .show-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .show-soft .table tbody tr:hover > * {
        background-color: #fafcfe; --bs-table-accent-bg: transparent;
    }
    .show-soft .exp-num { color: var(--ink-4); text-align: center; width: 22px; }

    .show-soft .exp-ellip   { max-width: 150px; overflow: hidden; text-overflow: ellipsis; }
    .show-soft .exp-ellip-sm{ max-width: 84px;  overflow: hidden; text-overflow: ellipsis; }

    .show-soft .exp-nombre {
        font-size: 8px; font-weight: 600; color: var(--ink-2);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    /* Miniatura 18px (misma de galería) */
    .show-soft .exp-link { line-height: 0; display: inline-block; }
    .show-soft .exp-thumb {
        width: 18px; height: 18px; object-fit: cover;
        border-radius: 2px; border: 1px solid var(--grid-2);
        display: block; margin: 0 auto;
    }
    .show-soft .exp-thumb-empty {
        width: 18px; height: 18px; border-radius: 2px;
        border: 1px dashed var(--grid-2); background: var(--head); color: #d4dae2;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 8px; margin: 0 auto;
    }

    /* Chips (mismo lenguaje de rsv-chip) */
    .show-soft .sh-chip {
        display: inline-flex; align-items: center; gap: 2px;
        font-size: 7px; font-weight: 600;
        padding: 0 5px; border-radius: 7px; line-height: 12px;
        border: 1px solid; white-space: nowrap; flex-shrink: 0;
    }
    .show-soft .sh-chip i { font-size: 6.5px; line-height: 1; }
    .show-soft .sh-chip.ok   { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
    .show-soft .sh-chip.off  { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }
    .show-soft .sh-chip.tipo { color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2; }
    .show-soft .sh-chip.tint { color: #8a6fae; background: #f7f3fb; border-color: #e6dcf2; }
    .show-soft .sh-chip .t { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* ═══ CINTA DE TÍTULO (individual) ═══ */
    .show-soft .sh-titlebar {
        background: #fff; border: 1px solid var(--grid); border-radius: 5px;
        padding: 10px 14px; display: flex; justify-content: space-between;
        align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;
    }
    .show-soft .sh-live {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 7px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--accent); background: var(--accent-bg);
        border: 1px solid var(--accent-bd); border-radius: 8px;
        padding: 1px 7px; line-height: 13px;
    }
    .show-soft .sh-live .pulse {
        width: 5px; height: 5px; border-radius: 50%; background: var(--accent);
        animation: shPulse 2s infinite;
    }
    @keyframes shPulse {
        0%   { box-shadow: 0 0 0 0 rgba(61,122,92,.45); }
        70%  { box-shadow: 0 0 0 5px rgba(61,122,92,0); }
        100% { box-shadow: 0 0 0 0 rgba(61,122,92,0); }
    }
    .show-soft .sh-titlebar .strong { font-size: 10.5px; font-weight: 700; color: var(--ink-2); }
    .show-soft .sh-titlebar .sub    { font-size: 7.5px; color: var(--ink-4); }
    .show-soft .btn-soft {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 3px 10px;
        text-decoration: none; transition: all .15s ease;
    }
    .show-soft .btn-soft:hover { background: #e3f1e9; color: #34684e; text-decoration: none; }
    .show-soft .btn-soft i { font-size: 9px; }

    /* ═══ COLUMNA PREVIA (individual) ═══ */
    .show-soft .pv-sticky { position: sticky; top: 12px; }
    .show-soft .pv-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 7.5px; font-weight: 700; letter-spacing: .07em;
        text-transform: uppercase; color: var(--ink-3); margin-bottom: 5px;
    }
    .show-soft .pv-label svg { width: 10px; height: 10px; stroke: var(--ink-4); }

    .show-soft .pv-card { background: #fff; border: 1px solid var(--grid); border-radius: 5px; overflow: hidden; display: flex; flex-direction: column; }
    .show-soft .pv-carousel { height: 170px; background: #f4f6f8; position: relative; flex-shrink: 0; border-bottom: 1px solid var(--grid); }
    .show-soft .pv-carousel img { object-fit: cover; object-position: center; width: 100%; height: 100%; }
    .show-soft .pv-carousel .carousel-inner,
    .show-soft .pv-carousel .carousel-item { width: 100%; height: 100%; }
    .show-soft .pv-carousel .carousel-control-prev,
    .show-soft .pv-carousel .carousel-control-next { width: 24px; opacity: .2; transition: opacity .15s ease; z-index: 5; }
    .show-soft .pv-carousel:hover .carousel-control-prev,
    .show-soft .pv-carousel:hover .carousel-control-next { opacity: .55; }
    .show-soft .pv-carousel .carousel-control-prev-icon,
    .show-soft .pv-carousel .carousel-control-next-icon { width: 13px; height: 13px; filter: invert(.45); }

    .show-soft .portada-badge {
        position: absolute; bottom: 6px; left: 6px; z-index: 5;
        font-size: 7px; font-weight: 600; letter-spacing: .05em;
        color: var(--ink-3); background: rgba(255,255,255,.92);
        border: 1px solid rgba(233,238,244,.9);
        border-radius: 3px; padding: 0 6px; line-height: 14px;
    }
    .show-soft .pv-empty-media {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        background: var(--head); color: #d4dae2; font-size: 8.5px; font-style: italic;
    }

    /* Botón decorativo del corazón (solo visual, sin funcionalidad) */
    .show-soft .float-btn {
        width: 22px; height: 22px; border-radius: 4px; padding: 0;
        background: rgba(255,255,255,.95); border: 1px solid var(--grid-2);
        color: var(--ink-3); display: inline-flex; align-items: center; justify-content: center;
        transition: all .15s ease; z-index: 5;
    }
    .show-soft .float-btn:hover { color: var(--accent); border-color: var(--accent-bd); }
    .show-soft .float-btn svg { width: 10px; height: 10px; }
    .show-soft .btn-favorito svg.heart { stroke: #c98a85; }

    .show-soft .pv-body { padding: 9px 11px 10px; display: flex; flex-direction: column; flex-grow: 1; }
    .show-soft .pv-body .strong { font-size: 9.5px; font-weight: 600; color: var(--ink-2); }

    .show-soft .star-row svg { width: 9px; height: 9px; }
    .show-soft .star-row svg polygon { fill: #5d8a70; }
    .show-soft .score { font-size: 8px; font-weight: 700; color: var(--ink-2); }
    .show-soft .rev-count { font-size: 7.5px; color: var(--ink-4); display: inline-flex; align-items: center; gap: 3px; }
    .show-soft .rev-count svg { width: 8px; height: 8px; stroke: var(--ink-4); }

    .show-soft .loc { font-size: 8px; color: var(--ink-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .show-soft .loc i { color: #c3ccd6; }

    .show-soft .spec-box { display: flex; border: 1px solid var(--grid); border-radius: 4px; background: var(--head); margin-bottom: 8px; }
    .show-soft .spec-box .half { flex: 1; padding: 6px 9px; display: flex; flex-direction: column; gap: 1px; min-width: 0; }
    .show-soft .spec-box .half + .half { border-left: 1px solid var(--grid); }
    .show-soft .spec-box .lbl { font-size: 6.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4); }
    .show-soft .spec-box .val { font-size: 10px; font-weight: 700; color: var(--ink-2); font-variant-numeric: tabular-nums; }

    .show-soft .kicker { font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--ink-4); }

    .show-soft .tarifa-box { background: #fafdfb; border: 1px solid #eaf3ee; border-radius: 4px; padding: 8px 9px; }
    .show-soft .tarifa-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 6px; margin-bottom: 6px; }
    .show-soft .tarifa-name  { font-size: 8.5px; font-weight: 700; color: var(--ink-2); }
    .show-soft .tarifa-fecha { font-size: 7px; color: var(--ink-4); }
    .show-soft .tarifa-precio { display: block; font-size: 10px; font-weight: 700; color: var(--accent); font-variant-numeric: tabular-nums; white-space: nowrap; }
    .show-soft .tarifa-precio small { display: block; font-size: 6.5px; font-weight: 600; color: var(--ink-4); }
    .show-soft .restr-row { display: flex; gap: 6px; }
    .show-soft .restr-cell { flex: 1; background: var(--head); border: 1px solid var(--grid); border-radius: 3px; padding: 4px 7px; display: flex; flex-direction: column; }
    .show-soft .restr-cell .lbl { font-size: 6.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-4); }
    .show-soft .restr-cell .val { font-size: 8.5px; font-weight: 700; color: var(--ink-2); font-variant-numeric: tabular-nums; }
    .show-soft .restr-cell .val.blue { color: #5b7fa6; }
    .show-soft .tarifa-empty {
        border: 1px dashed var(--grid-2); background: var(--head); border-radius: 4px;
        padding: 10px; text-align: center; font-size: 8px; color: var(--ink-4); font-style: italic;
    }

    /* ═══ ALERTAS ═══ */
    .show-soft .sh-alert {
        display: flex; justify-content: space-between; align-items: center;
        border-radius: 4px; padding: 6px 10px; font-size: 8.5px; font-weight: 600;
        margin-bottom: 10px; border: 1px solid;
    }
    .show-soft .sh-alert.ok  { background: #f0f7f2; color: #5d8a70; border-color: #d8e9de; }
    .show-soft .sh-alert.err { background: #fdf5f4; color: #c98a85; border-color: #f0dcd8; }
    .show-soft .sh-alert button { background: none; border: 0; font-size: 12px; line-height: 1; color: inherit; padding: 0 2px; }

    /* ═══ MÓDULO 1 (individual · solo lectura) ═══ */
    .show-soft .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px; }
    .show-soft .stat-cell { border: 1px solid var(--grid); background: var(--head); border-radius: 4px; padding: 7px 9px; min-width: 0; }
    .show-soft .stat-cell .lbl { font-size: 7px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4); display: flex; align-items: center; gap: 4px; margin-bottom: 3px; }
    .show-soft .stat-cell .lbl i { font-size: 7.5px; }
    .show-soft .stat-cell .big { font-size: 11px; font-weight: 700; color: var(--ink-2); }
    .show-soft .stat-cell .big small { font-size: 6.5px; font-weight: 600; color: var(--ink-4); }
    .show-soft .stat-cell .sub { font-size: 7px; color: var(--ink-4); }

    .show-soft .vis-pill {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 7.5px; font-weight: 700; padding: 1px 8px;
        border-radius: 8px; line-height: 14px; border: 1px solid;
    }
    .show-soft .vis-pill .dotp { width: 4px; height: 4px; border-radius: 50%; background: currentColor; }
    .show-soft .vis-on  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
    .show-soft .vis-off { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }

    .show-soft .m1-chev { width: 9px; height: 9px; stroke: var(--ink-4); transition: transform .2s ease; }
    .show-soft [data-bs-toggle="collapse"][aria-expanded="false"] .m1-chev { transform: rotate(-90deg); }
</style>

@php
    // ── INM-v3-DBG · colector de contexto del servidor (solo lectura, sin lógica de negocio) ──
    $inmDbg = [
        'marca'       => 'INM-v3-DBG',
        'vista'       => 'rsv.admin.partials.tab-inmuebles',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modoGlobal'  => $modoGlobal,
        'expedientes' => $modoGlobal
            ? (isset($expedientes) && $expedientes
                ? (method_exists($expedientes, 'total') ? $expedientes->total() : $expedientes->count())
                : 0)
            : 0,
        'inmuebleId'  => isset($inmueble) && $inmueble ? $inmueble->id : null,
        'errores'     => [],
    ];
@endphp

{{-- INM-v3-DBG · Tab DBG terminal + LED + Panel LOCAL (fuera del root: sobrevive a los swaps · NO global) --}}
<div id="inmDbgToggle" title="Debug Inmuebles (INM-v3-DBG)"><span class="inm-dbg-led" id="inmDbgLed"></span>DBG<span class="inm-dbg-badge" id="inmDbgBadge">0</span></div>
<div id="inmDbgPanel" role="dialog" aria-label="Panel de debug de Inmuebles">
    <div class="inm-dbg-header" id="inmDbgHeader">
        <span>DEBUG · Inmuebles (N2) · INM-v3-DBG</span>
        <span class="inm-dbg-controls">
            <button type="button" class="inm-dbg-btn" id="inmDbgCopy">Copiar</button>
            <button type="button" class="inm-dbg-btn" id="inmDbgClear">Limpiar</button>
            <button type="button" class="inm-dbg-btn" id="inmDbgClose">✕</button>
        </span>
    </div>
    <div id="inmDbgBody"></div>
</div>

<div id="showRoot" class="show-soft" data-frag="{{ route('rsv.admin.inmuebles.fragmento') }}" data-dash="{{ route('rsv.admin.dashboard') }}">

@if($modoGlobal)

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODO GLOBAL · TABLA CON TODAS LAS RELACIONES + GESTIONAR         --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <div class="d-flex justify-content-between align-items-center mb-2">

            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-houses me-1" style="font-size: 8px; color: #5d8a70;"></i>
                Visión Global de Expedientes
                <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">INM-v3-DBG</span>
            </h6>

            <span class="sh-chip tipo" style="font-size: 7.5px;">
                <i class="bi bi-collection"></i>{{ $expedientes->count() }} inmuebles
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-sm table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th class="exp-num">#</th>
                        <th class="text-center" style="width: 28px;">Vista</th>
                        <th>Inmueble</th>
                        <th style="width: 92px;">Tipo Inm.</th>
                        <th style="width: 76px;">Ciudad</th>
                        <th class="text-center" style="width: 38px;">Cap.</th>
                        <th class="text-end" style="width: 72px;">Mín. Res.</th>
                        <th class="text-center" style="width: 52px;">Máx. D.</th>
                        <th class="text-center" style="width: 44px;">Fotos</th>
                        <th class="text-center" style="width: 50px;">Bloq.</th>
                        <th class="text-center" style="width: 56px;">Reservas</th>
                        <th class="text-center" style="width: 58px;">Estado</th>
                        {{-- ═══ FIX: columna Gestionar restaurada ═══ --}}
                        <th class="text-center" style="width: 64px;">·</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expedientes as $prop)

                        @php
                            $portada = $portadaDe($prop);
                            $ut      = $ultimaTarifaDe($prop);
                            $tipoInm = optional($prop->tipoInmueble)->nombre;
                            $nFotos  = $prop->multimedia->count();
                            $nBloq   = $prop->bloqueos_calendario_count ?? 0;
                            $nRes    = $prop->reservas_count ?? 0;
                        @endphp

                        <tr>

                            <td class="exp-num">{{ $loop->iteration }}</td>

                            {{-- Portada (relación multimedia) --}}
                            <td class="text-center">
                                @if($portada)
                                    <a href="{{ $portada->url_archivo }}" target="_blank" title="Ver portada" class="exp-link text-decoration-none">
                                        <img src="{{ $portada->url_archivo }}" alt="" class="exp-thumb">
                                    </a>
                                @else
                                    <span class="exp-thumb-empty"><i class="bi bi-image"></i></span>
                                @endif
                            </td>

                            {{-- Nombre --}}
                            <td class="exp-ellip">
                                <span class="exp-nombre" title="{{ $prop->name }}">{{ $prop->name }}</span>
                            </td>

                            {{-- Tipo (relación tipoInmueble) --}}
                            <td class="exp-ellip-sm">
                                @if($tipoInm)
                                    <span class="sh-chip tipo" title="{{ $tipoInm }}">
                                        <i class="bi bi-tag-fill"></i><span class="t">{{ $tipoInm }}</span>
                                    </span>
                                @else
                                    <span style="color: var(--ink-4);">—</span>
                                @endif
                            </td>

                            {{-- Ciudad --}}
                            <td class="exp-ellip-sm" title="{{ $prop->city ?? '' }}" style="color: var(--ink-3);">
                                {{ $prop->city ?? '—' }}
                            </td>

                            {{-- Capacidad --}}
                            <td class="exp-num text-center">{{ $prop->capacidad_maxima ?? '—' }}</td>

                            {{-- Mín. Reserva (relación latestTarifa) --}}
                            <td class="text-end fw-bold" style="color: #5d8a70;">
                                @if($ut)
                                    ${{ number_format($ut->precio_minimo_reserva ?? 0, 0) }}
                                @else
                                    <span style="color: var(--ink-4);">—</span>
                                @endif
                            </td>

                            {{-- Máx. Días (relación latestTarifa) --}}
                            <td class="text-center fw-bold" style="color: #5b7fa6;">
                                @if($ut)
                                    {{ $ut->dias_maximos ?: '∞' }}
                                @else
                                    <span style="color: var(--ink-4);">—</span>
                                @endif
                            </td>

                            {{-- Fotos (conteo multimedia) --}}
                            <td class="text-center">
                                @if($nFotos > 0)
                                    <span class="sh-chip tipo"><i class="bi bi-images"></i>{{ $nFotos }}</span>
                                @else
                                    <span style="color: var(--ink-4);">0</span>
                                @endif
                            </td>

                            {{-- Bloqueos (conteo bloqueosCalendario) --}}
                            <td class="text-center">
                                @if($nBloq > 0)
                                    <span class="sh-chip tint"><i class="bi bi-calendar-x"></i>{{ $nBloq }}</span>
                                @else
                                    <span style="color: var(--ink-4);">0</span>
                                @endif
                            </td>

                            {{-- RESERVAS (conteo reservas) --}}
                            <td class="text-center">
                                @if($nRes > 0)
                                    <span class="sh-chip ok"><i class="bi bi-journal-check"></i>{{ $nRes }}</span>
                                @else
                                    <span style="color: var(--ink-4);">0</span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="text-center">
                                @if($prop->active)
                                    <span class="sh-chip ok"><i class="bi bi-check-lg"></i>Activo</span>
                                @else
                                    <span class="sh-chip off"><i class="bi bi-dash-lg"></i>Inactivo</span>
                                @endif
                            </td>

                            {{-- ═══ FIX: botón Gestionar restaurado (solo navega al expediente) ═══ --}}
                            <td class="text-center">
                                <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                   class="btn btn-sm btn-link p-0 text-decoration-none"
                                   style="font-size: 8px; color: #3d7a5c; font-weight: 600;"
                                   title="Abrir expediente completo">
                                    Gestionar
                                </a>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px;">
                                <i class="bi bi-houses me-1"></i> No hay inmuebles registrados en el catálogo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

@else

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODO INDIVIDUAL · EXPEDIENTE COMPLETO · SOLO LECTURA            --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}

    {{-- CINTA DE TÍTULO --}}
    <div class="sh-titlebar">
        <div>
            <span class="sh-live mb-1"><span class="pulse"></span> EXPEDIENTE #{{ $inmueble->id }}</span>
            <div class="strong">Panel de Gestión: {{ $inmueble->name }} <span class="badge rounded-pill ms-1" style="font-size:6px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">INM-v3-DBG</span></div>
            <div class="sub">Vista informativa: identidad, tarifas y fotos del inmueble</div>
        </div>
        <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => 'todos']) }}" class="btn-soft">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>

    <div class="row g-3">

        {{-- ──── COLUMNA IZQUIERDA · VISTA PREVIA DEL CLIENTE ──── --}}
        <div class="col-12 col-lg-5 col-xl-4">
            <div class="pv-sticky">
                <div class="pv-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Vista Previa del Cliente
                </div>

                <div class="pv-card">
                    <div id="carouselInmueble{{ $inmueble->id }}" class="carousel slide pv-carousel" data-bs-ride="carousel">

                        {{-- Decorativo: corazón de favorito (sin funcionalidad) --}}
                        <button type="button" class="float-btn position-absolute top-0 end-0 m-2 btn-favorito" title="Vista de favorito">
                            <svg class="heart" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>

                        <div class="carousel-inner">
                            @forelse($galeria as $index => $media)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $media->url_archivo }}" alt="Foto inmueble">
                                    @if($media->es_portada)
                                        <span class="portada-badge">PORTADA</span>
                                    @endif
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <div class="pv-empty-media">
                                        <i class="bi bi-image me-1"></i> Sube fotos en la galería
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if($galeria->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmueble{{ $inmueble->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselInmueble{{ $inmueble->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        @endif
                    </div>

                    <div class="pv-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div class="strong text-truncate" title="{{ $inmueble->name }}">{{ $inmueble->name }}</div>
                            <div class="d-flex align-items-center gap-1" style="flex-shrink: 0;">
                                @if($inmueble->active)
                                    <span class="sh-chip ok"><i class="bi bi-check-lg"></i>Activo</span>
                                @else
                                    <span class="sh-chip off"><i class="bi bi-dash-lg"></i>Inactivo</span>
                                @endif
                                @if(optional($inmueble->tipoInmueble)->nombre)
                                    <span class="sh-chip tipo" title="Tipo de inmueble"><i class="bi bi-tag-fill"></i>{{ $inmueble->tipoInmueble->nombre }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-1 star-row">
                                @for($i = 0; $i < 5; $i++)
                                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                @endfor
                                <span class="score ms-1">5.0</span>
                            </div>
                            <span class="rev-count">
                                <i class="bi bi-journal-check"></i>
                                {{ $inmueble->reservas_count ?? 0 }} reservas
                            </span>
                        </div>

                        <div class="loc mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $inmueble->city ?? 'Sin ciudad' }} — {{ $inmueble->ubicacion ?? 'Sin dirección' }}
                        </div>

                        {{-- Especificaciones: capacidad · fotos · reservas --}}
                        <div class="spec-box">
                            <div class="half">
                                <span class="lbl">Capacidad</span>
                                <span class="val">{{ $inmueble->capacidad_maxima ?? 'N/A' }} <small style="font-size:6.5px;color:var(--ink-4);">pers.</small></span>
                            </div>
                            <div class="half">
                                <span class="lbl">Fotos</span>
                                <span class="val" style="color: #5b7fa6;">{{ $inmueble->multimedia->count() }}</span>
                            </div>
                            <div class="half">
                                <span class="lbl">Reservas</span>
                                <span class="val" style="color: #5d8a70;">{{ $inmueble->reservas_count ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="kicker mb-1 mt-auto">Condiciones última tarifa</div>
                        @if($ultimaTarifa)
                            <div class="tarifa-box">
                                <div class="tarifa-top">
                                    <div>
                                        <div class="tarifa-name">{{ $ultimaTarifa->nombre_temporada }}</div>
                                        <div class="tarifa-fecha">
                                            {{ optional($ultimaTarifa->fecha_inicio)->format('d/m/Y') }} — {{ optional($ultimaTarifa->fecha_fin)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="tarifa-precio">
                                            <small>Mín. reserva</small>
                                            ${{ number_format($ultimaTarifa->precio_minimo_reserva ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="restr-row">
                                    <div class="restr-cell">
                                        <span class="lbl">Precio noche</span>
                                        <span class="val">${{ number_format($ultimaTarifa->precio_noche ?? 0, 2) }}</span>
                                    </div>
                                    <div class="restr-cell">
                                        <span class="lbl">Máx. días</span>
                                        <span class="val blue">{{ $ultimaTarifa->dias_maximos ?: 'Ilimitado' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="tarifa-empty">Sin tarifas temporales activas</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ──── COLUMNA DERECHA · MÓDULOS (informativos) ──── --}}
        <div class="col-12 col-lg-7 col-xl-8">

            @if(session('success'))
                <div class="sh-alert ok" role="alert">
                    <span><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</span>
                    <button type="button" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="sh-alert err" role="alert">
                    <span><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</span>
                    <button type="button" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            {{-- MÓDULO 1 · NÚCLEO (solo lectura · colapsable) --}}
            <div class="p-3 bg-white rounded-3 border mb-3" style="border-color: #f1f5f9 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button type="button"
                            class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-2"
                            data-bs-toggle="collapse" data-bs-target="#collapseNucleoInmueble"
                            aria-expanded="true" aria-controls="collapseNucleoInmueble">
                        <i class="bi bi-fingerprint" style="font-size: 8px; color: #5d8a70;"></i>
                        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">Identidad y Especificaciones</h6>
                        <svg class="m1-chev" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <span class="sh-chip off" style="font-size: 7px;">
                        <i class="bi bi-eye"></i>Solo lectura
                    </span>
                </div>

                <div id="collapseNucleoInmueble" class="collapse show">
                    <div class="stat-grid">
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-people"></i> Capacidad límite</span>
                            <span class="big">{{ $inmueble->capacidad_maxima ?? '0' }} <small>personas</small></span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-geo-alt"></i> Punto geográfico</span>
                            <span class="big d-block text-truncate" title="{{ $inmueble->city ?? '' }}">{{ $inmueble->city ?? 'N/A' }}</span>
                            <span class="sub d-block text-truncate" title="{{ $inmueble->ubicacion ?? '' }}">{{ $inmueble->ubicacion ?? 'Sin dirección' }}</span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-tag"></i> Tipo de inmueble</span>
                            <span class="big d-block text-truncate" title="{{ optional($inmueble->tipoInmueble)->nombre }}">
                                {{ optional($inmueble->tipoInmueble)->nombre ?? 'Sin tipo' }}
                            </span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-images"></i> Galería</span>
                            <span class="big">{{ $inmueble->multimedia->count() }} <small>fotos</small></span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-journal-check"></i> Reservas</span>
                            <span class="big" style="color: #5d8a70;">{{ $inmueble->reservas_count ?? 0 }}</span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-calendar-x"></i> Bloqueos</span>
                            <span class="big" style="color: #8a6fae;">{{ $inmueble->bloqueos_calendario_count ?? 0 }}</span>
                        </div>
                        <div class="stat-cell">
                            <span class="lbl"><i class="bi bi-eye"></i> Visibilidad</span>
                            <div class="mt-1">
                                @if($inmueble->active)
                                    <span class="vis-pill vis-on"><span class="dotp"></span> En vivo</span>
                                @else
                                    <span class="vis-pill vis-off"><span class="dotp"></span> Borrador</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ FIX: Módulos 2 y 3 en modo SOLO LECTURA ═══ --}}
            {{-- La bandera soloLectura=true hace que tarifa y galeria muestren --}}
            {{-- sus tablas informativas SIN botones de crear/editar ni modales --}}
            @include('rsv.admin.partials.inmuebles.tarifa', ['soloLectura' => true])
            @include('rsv.admin.partials.inmuebles.galeria', ['soloLectura' => true])

        </div>
    </div>

@endif

    <script>
    /* ═══════════════ INM-v3-DBG · tab-inmuebles · NIVEL 2 ═══════════════
       Script LOCAL de esta vista: viaja DENTRO de #showRoot, por lo que se
       re-ejecuta tras cada swap AJAX y renace con sus listeners.
       NO captura fetch ni errores de otros módulos de la página.          */
    (function () {
        var root = document.getElementById('showRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var DASH = root.getAttribute('data-dash') || '';
        var SRVDBG = @json($inmDbg);

        /* ═══ 1) PANEL DE DEBUG LOCAL (tab terminal · estilo TIP-v1.3) ═══ */
        var DBG = (window.InmDbg && window.InmDbg.alive && window.InmDbg.alive())
            ? window.InmDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('inmDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'inmDbgStyle';
                    st.textContent = [
                        /* ═════════ SPEC DE DISTANCIAS · INM-v3 (tab terminal · patrón TIP-v1.3) ════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
                        '#inmDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#inmDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.inm-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.inm-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:inmDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes inmDbgBlink{50%{opacity:.35;}}',
                        '.inm-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#inmDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.inm-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.inm-dbg-controls{display:flex;gap:4px;}',
                        '.inm-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.inm-dbg-btn:hover{background:#475569;}',
                        '#inmDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.inm-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.inm-dbg-time{color:#64748b;margin-right:4px;}',
                        '.inm-dbg-info{color:#93c5fd;}.inm-dbg-ok{color:#86efac;}',
                        '.inm-dbg-warn{color:#fde68a;}.inm-dbg-err{color:#fca5a5;}',
                        '.inm-dbg-net{color:#c4b5fd;}.inm-dbg-debug{color:#94a3b8;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    if (!$('inmDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'inmDbgToggle';
                        t.title = 'Debug Inmuebles (INM-v3-DBG)';
                        t.innerHTML = '<span class="inm-dbg-led" id="inmDbgLed"></span>DBG<span class="inm-dbg-badge" id="inmDbgBadge">0</span>';
                        document.body.appendChild(t);
                    }
                    if (!$('inmDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'inmDbgPanel';
                        p.innerHTML = '<div class="inm-dbg-header" id="inmDbgHeader">' +
                            '<span>DEBUG · Inmuebles (N2) · INM-v3-DBG</span>' +
                            '<span class="inm-dbg-controls">' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgCopy">Copiar</button>' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgClear">Limpiar</button>' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgClose">✕</button>' +
                            '</span></div><div id="inmDbgBody"></div>';
                        document.body.appendChild(p);
                    }
                    apilar();
                }
                /* INM-v3: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila:
                   con 16px de alto cabe bajo cualquier otro toggle sin tocarlo).
                   Solo el PANEL esquiva a los vecinos: bottom = max(20, vecino + 4). */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle']; /* serie TAR/GM/TIP */
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('inmDbgToggle'), p = $('inmDbgPanel');
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
                        var b = $('inmDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'inm-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="inm-dbg-time">[' + hora() + ']</span>' +
                            '<span class="inm-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('inmDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('inmDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('inmDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('inmDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('inmDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('inmDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('inmDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('inmDbgBody'); if (b) { b.innerHTML = ''; } },
                    alive: function () { return !!$('inmDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                $('inmDbgToggle').addEventListener('click', api.togglePanel);
                $('inmDbgClose').addEventListener('click', function (e2) { e2.stopPropagation(); api.close(); });
                $('inmDbgClear').addEventListener('click', function (e2) { e2.stopPropagation(); api.clear(); });
                $('inmDbgCopy').addEventListener('click', function (e2) {
                    e2.stopPropagation();
                    var b = $('inmDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG tab-inmuebles INM-v3-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        }).catch(function () {
                            api.log('no se pudo copiar (permisos del navegador)', 'warn');
                        });
                    } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
                });
                $('inmDbgHeader').addEventListener('dblclick', api.close);
                window.InmDbg = api;
                return api;
            })();
        if (!DBG) { return; }

        /* ═══ 2) BOOT · contexto del servidor (como el Panel de Huéspedes) ═══ */
        DBG.log('INM-v3-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · panel LOCAL (solo tab-inmuebles · NO captura errores ajenos)', 'ok');
        DBG.log('init · modo=' + (SRVDBG && SRVDBG.modoGlobal ? 'GLOBAL (grid de expedientes)' : 'INDIVIDUAL (ficha solo lectura)') +
            ' · expedientes=' + (SRVDBG ? SRVDBG.expedientes : '?') +
            (SRVDBG && SRVDBG.inmuebleId ? ' · inmuebleSel=' + SRVDBG.inmuebleId : ''), 'info');
        DBG.log('SRV · peticion=' + (SRVDBG && SRVDBG.peticion ? SRVDBG.peticion : '?') +
            ' · url=' + (SRVDBG && SRVDBG.url ? SRVDBG.url : '?'), 'debug');
        DBG.log('Rutas · FRAG=' + (FRAG || 'NO') + ' · DASH=' + (DASH || 'NO') +
            (FRAG ? ' · navegación interna por AJAX activa' : ' → FRAG vacío: los enlaces navegarán normal (sin swap)'), FRAG ? 'ok' : 'warn');
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        /* ═══ 2b) AUTO-TAB (heredado de la v2) · AUTO-ACTIVAR EL TAB propio al llegar por navegación normal ═══
           Con ?inmueble_id=... en la URL (p.ej. tras un "Gestionar" que recargó la página),
           el navegador cae en el tab "inicio"; aquí activamos el tab que contiene esta
           vista para que la ficha quede visible de inmediato. En swaps AJAX el tab ya
           está activo → no hace nada.                                                       */
        try {
            var __q = new URLSearchParams(window.location.search || '');
            if (__q.has('inmueble_id')) {
                var __pane = root.closest('.tab-pane');
                if (__pane && __pane.id && !__pane.classList.contains('active')) {
                    var __link = document.querySelector('[data-bs-target="#' + __pane.id + '"], [href="#' + __pane.id + '"]');
                    if (__link && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        bootstrap.Tab.getOrCreateInstance(__link).show();
                        DBG.log('auto-tab · tab #' + __pane.id + ' activado (llegada con inmueble_id=' + __q.get('inmueble_id') + ')', 'ok');
                    } else {
                        DBG.log('auto-tab · tab #' + __pane.id + ' NO activado (falta el enlace del nav o bootstrap.Tab)', 'debug');
                    }
                }
            }
        } catch (eAT) { DBG.log('auto-tab omitido (' + eAT.message + ')', 'debug'); }

        /* ═══ 3) NAVEGACIÓN SIN RECARGA · Gestionar / Volver al Catálogo ═══ */
        function inmRunScripts(scope) {
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
        function inmCargarFragmento(rid, urlDestino) {
            var url = FRAG + (FRAG.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(rid);
            DBG.log('clic interno → inmueble_id=' + rid + ' (cargando por AJAX, sin recargar la página)', 'info');
            DBG.log('fetch ' + url, 'net');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
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
                if (!actual) { throw new Error('#showRoot actual no está en el DOM'); }
                actual.replaceWith(nuevo);
                inmRunScripts(nuevo);
                try { window.history.pushState({}, '', urlDestino); } catch (e3) {}
                try { document.dispatchEvent(new CustomEvent('rsv:inmueble-cambiado', { detail: { inmuebleId: rid } })); } catch (e4) {}
                DBG.log('fragmento inmuebles recargado · ' + html.length + ' bytes · sin recargar la página', 'ok');
            }).catch(function (e2) {
                DBG.log('⚠ swap falló (' + e2.message + ') → respaldo: navegación nativa', 'err');
                DBG.open();
                window.location.href = urlDestino;
            });
        }
        /* Handler RE-REGISTRABLE: al re-ejecutarse el script tras cada swap,
           quita el oyente anterior y registra uno nuevo (sin acumular).      */
        if (window.__inmLinkHandler) { document.removeEventListener('click', window.__inmLinkHandler, true); }
        window.__inmLinkHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            if (!FRAG || !DASH) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (!a.closest('#showRoot')) { return; } /* solo enlaces de ESTA vista */
            var href = a.getAttribute('href') || '';
            if (!href || href.charAt(0) === '#') { return; }
            var u; try { u = new URL(a.href, window.location.href); } catch (e0) { return; }
            var dash; try { dash = new URL(DASH, window.location.href); } catch (e5) { return; }
            if (u.pathname !== dash.pathname) { return; }
            var rid = u.searchParams.has('inmueble_id') ? u.searchParams.get('inmueble_id') : 'todos';
            e.preventDefault();
            inmCargarFragmento(rid, u.pathname + '?inmueble_id=' + encodeURIComponent(rid));
        };
        document.addEventListener('click', window.__inmLinkHandler, true);
        DBG.log('interceptor de enlaces listo (Gestionar / Volver al Catálogo → swap AJAX de #showRoot)', 'debug');
    })();
    </script>

</div>
