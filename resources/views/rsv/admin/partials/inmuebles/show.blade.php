{{-- resources/views/rsv/admin/partials/inmuebles/show.blade.php --}}
{{-- MODO GLOBAL = tabla resumen con TODAS las relaciones + botón Gestionar --}}
{{-- MODO INDIVIDUAL = expediente SOLO LECTURA (tarifa/galeria se incluyen con --}}
{{-- soloLectura=true: aparecen las tablas, JAMÁS los botones de acciones)     --}}

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

<div id="showRoot" class="show-soft">

@if($modoGlobal)

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODO GLOBAL · TABLA CON TODAS LAS RELACIONES + GESTIONAR         --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <div class="d-flex justify-content-between align-items-center mb-2">

            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-houses me-1" style="font-size: 8px; color: #5d8a70;"></i>
                Visión Global de Expedientes
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
            <div class="strong">Panel de Gestión: {{ $inmueble->name }}</div>
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

</div>
