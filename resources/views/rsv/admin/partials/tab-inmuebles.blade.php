{{-- resources/views/rsv/admin/partials/tab-inmuebles.blade.php · INM-v3.7-DBG · NIVEL 2 --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- INMUEBLES · CENTRO DE CONTROL Y VISIÓN GLOBAL (Modo Global e Individual)    --}}
{{-- Diseño unificado idéntico a FIN-v2.0-DBG, CAL-v3.5-DBG y RV-v32-EXC-DBG.    --}}
{{-- Conserva la navegación AJAX sin recarga de página y el panel TIP-v1.3.    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@php
    $modoGlobal = !isset($inmueble) || !$inmueble;

    if ($modoGlobal) {
        if (isset($inmueblesGrid) && $inmueblesGrid) {
            $expedientes = $inmueblesGrid;
            $col = method_exists($expedientes, 'getCollection')
                ? $expedientes->getCollection()
                : $expedientes;
            $col->loadMissing('tipoInmueble');
        } else {
            $expedientes = \App\Models\Rsv\CatalogoInmueble::query()
                ->with(['tipoInmueble', 'latestTarifa', 'tarifasTemporadas', 'multimedia'])
                ->withCount(['reservas', 'bloqueosCalendario'])
                ->orderBy('name')
                ->get();
        }
    } else {
        $inmueble->loadMissing(['tipoInmueble', 'multimedia', 'tarifasTemporadas', 'latestTarifa']);
        $inmueble->loadCount(['reservas', 'bloqueosCalendario']);
        $galeria = $inmueble->multimedia->sortByDesc('es_portada')->values();
        $ultimaTarifa = $inmueble->latestTarifa
            ?: $inmueble->tarifasTemporadas->sortByDesc('fecha_inicio')->first();
    }

    $portadaDe = function ($prop) {
        return $prop->multimedia->sortByDesc('es_portada')->first();
    };

    $ultimaTarifaDe = function ($prop) {
        if ($prop->relationLoaded('latestTarifa') && $prop->latestTarifa) {
            return $prop->latestTarifa;
        }
        return $prop->tarifasTemporadas->sortByDesc('fecha_inicio')->first();
    };

    $esActivoProp = function ($prop): bool {
        if (!is_object($prop)) return false;
        if (isset($prop->active)) return (bool) $prop->active;
        if (isset($prop->activo)) return (bool) $prop->activo;
        return false;
    };

    // Cálculo robusto de KPIs (con respaldo directo en BD y detección multi-modelo de bloqueos)
    if ($modoGlobal) {
        $colItems = method_exists($expedientes, 'getCollection') ? $expedientes->getCollection() : collect($expedientes);

        try {
            $inmTotalCount = (int) \App\Models\Rsv\CatalogoInmueble::count();
        } catch (\Throwable $e) {
            $inmTotalCount = method_exists($expedientes, 'total') ? (int) $expedientes->total() : (int) $colItems->count();
        }

        try {
            $tablaInm = (new \App\Models\Rsv\CatalogoInmueble)->getTable();
            $colAct = \Illuminate\Support\Facades\Schema::hasColumn($tablaInm, 'active')
                ? 'active'
                : (\Illuminate\Support\Facades\Schema::hasColumn($tablaInm, 'activo') ? 'activo' : null);
            $inmActivosCnt = $colAct
                ? (int) \App\Models\Rsv\CatalogoInmueble::where($colAct, 1)->count()
                : (int) $colItems->filter($esActivoProp)->count();
        } catch (\Throwable $e) {
            $inmActivosCnt = (int) $colItems->filter($esActivoProp)->count();
        }

        $inmInactivosCnt = max(0, $inmTotalCount - $inmActivosCnt);

        try {
            $inmTotalReservas = class_exists(\App\Models\Rsv\Reserva::class)
                ? (int) \App\Models\Rsv\Reserva::count()
                : (int) $colItems->sum('reservas_count');
        } catch (\Throwable $e) {
            $inmTotalReservas = (int) $colItems->sum('reservas_count');
        }

        $inmTotalBloqueos = 0;
        foreach ([
            '\App\Models\Rsv\BloqueoCalendario',
            '\App\Models\Rsv\BloqueosCalendario',
            '\App\Models\Rsv\CalendarioBloqueo',
        ] as $clsBlqTry) {
            if (class_exists($clsBlqTry)) {
                try {
                    $inmTotalBloqueos = (int) $clsBlqTry::count();
                    break;
                } catch (\Throwable $e) {}
            }
        }
        if ($inmTotalBloqueos === 0) {
            foreach (['rsv_bloqueos_calendario', 'bloqueos_calendario', 'rsv_bloqueos', 'bloqueos'] as $tblBlqTry) {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable($tblBlqTry)) {
                        $inmTotalBloqueos = (int) \Illuminate\Support\Facades\DB::table($tblBlqTry)->count();
                        if ($inmTotalBloqueos > 0) break;
                    }
                } catch (\Throwable $e) {}
            }
        }
        if ($inmTotalBloqueos === 0) {
            $inmTotalBloqueos = (int) $colItems->sum('bloqueos_calendario_count');
        }
    } else {
        $inmTotalCount    = 1;
        $inmActivosCnt    = $esActivoProp($inmueble) ? 1 : 0;
        $inmInactivosCnt  = $inmActivosCnt === 1 ? 0 : 1;
        $inmTotalReservas = (int) ($inmueble->reservas_count ?? 0);
        $inmTotalBloqueos = (int) ($inmueble->bloqueos_calendario_count ?? 0);
    }

    $inmDbg = [
        'marca'       => 'INM-v3.7-DBG',
        'vista'       => 'rsv.admin.partials.tab-inmuebles',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modoGlobal'  => $modoGlobal,
        'expedientes' => $inmTotalCount,
        'activos'     => $inmActivosCnt,
        'inactivos'   => $inmInactivosCnt,
        'bloqueos'    => $inmTotalBloqueos,
        'inmuebleId'  => !$modoGlobal ? $inmueble->id : null,
        'errores'     => [],
    ];
@endphp

{{-- INM-v3.7-DBG · Tab DBG terminal + LED + Panel LOCAL (fuera del root: sobrevive a los swaps) --}}
<div id="inmDbgToggle" title="Debug Inmuebles (INM-v3.7-DBG)">
    <span class="inm-dbg-led" id="inmDbgLed"></span>DBG<span class="inm-dbg-badge" id="inmDbgBadge">0</span>
</div>
<div id="inmDbgPanel" role="dialog" aria-label="Panel de debug de Inmuebles">
    <div class="inm-dbg-header" id="inmDbgHeader">
        <span>DEBUG · Inmuebles (N2) · INM-v3.7-DBG</span>
        <span class="inm-dbg-controls">
            <button type="button" class="inm-dbg-btn" id="inmDbgCopy">Copiar</button>
            <button type="button" class="inm-dbg-btn" id="inmDbgClear">Limpiar</button>
            <button type="button" class="inm-dbg-btn" id="inmDbgClose">✕</button>
        </span>
    </div>
    <div id="inmDbgBody"></div>
</div>

<div id="showRoot" class="show-soft" data-frag="{{ route('rsv.admin.inmuebles.fragmento') }}" data-dash="{{ route('rsv.admin.dashboard') }}">

    <style>
        .show-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
        .show-soft .lbl { font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); }
        .show-soft .val { font-size: 10.5px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
        .show-soft .sub { font-size: 8px; line-height: 1.35; color: var(--ink-3); }
        .show-soft .strong { color: var(--ink-2); font-weight: 700; }
        .show-soft .num { font-variant-numeric: tabular-nums; }

        /* ── HERO BANNER INMUEBLES (IDÉNTICO A FIN-v2.0-DBG / CAL-v3.5-DBG) ── */
        .show-soft .inm-hero-pro {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 11px;
            padding: 20px 24px;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            margin-bottom: 12px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
        }
        .show-soft .inm-hero-pro .hero-watermark {
            position: absolute; right: -2%; bottom: -35%; font-size: 12rem;
            opacity: 0.04; color: #ffffff; pointer-events: none; transform: rotate(-10deg);
        }
        .show-soft .inm-hero-btn-primary {
            background: #10b981; color: #0f172a; border: none;
            font-size: 9px; font-weight: 800; padding: 7px 14px; border-radius: 6px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.18s ease;
        }
        .show-soft .inm-hero-btn-primary:hover { background: #34d399; transform: translateY(-1px); }
        .show-soft .inm-hero-btn-outline {
            background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.2);
            font-size: 9px; font-weight: 700; padding: 7px 13px; border-radius: 6px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.18s ease;
        }
        .show-soft .inm-hero-btn-outline:hover { background: rgba(255,255,255,0.16); border-color: #fff; color: #fff; }

        /* ── TARJETAS KPI DE INMUEBLES (IDÉNTICAS A FIN-v2.0-DBG / CAL-v3.5-DBG) ── */
        .show-soft .inm-kpi-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px;
        }
        @media (max-width: 767px) { .show-soft .inm-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        .show-soft .inm-kpi-card {
            background: #fff; border: 1px solid var(--grid-2); border-radius: 9px;
            padding: 10px 13px; display: flex; align-items: center; justify-content: space-between;
            gap: 10px; cursor: pointer; transition: all 0.16s ease;
        }
        .show-soft .inm-kpi-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.04); }
        .show-soft .inm-kpi-icon {
            width: 34px; height: 34px; border-radius: 8px; display: flex;
            align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; border: 1px solid;
        }

        /* ═══ TABLA GLOBAL: filas de 22px exactas ═══ */
        .show-soft .table > :not(caption) > * > * {
            padding: 3px 7px; height: 22px; line-height: 1.15; vertical-align: middle;
        }
        .show-soft .table thead th {
            font-size: 8.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
            color: var(--ink-3); background-color: var(--head);
            border-bottom: 1px solid var(--grid-2) !important; white-space: nowrap;
        }
        .show-soft .table tbody td {
            font-size: 8.5px; color: var(--ink);
            border-bottom: 1px solid var(--grid); border-top: 0;
            font-variant-numeric: tabular-nums; white-space: nowrap;
        }
        .show-soft .table tbody tr:hover > * {
            background-color: #f8fafc; --bs-table-accent-bg: transparent;
        }
        .show-soft .exp-num { color: var(--ink-4); text-align: center; width: 25px; }

        .show-soft .exp-ellip   { max-width: 170px; overflow: hidden; text-overflow: ellipsis; }
        .show-soft .exp-ellip-sm{ max-width: 90px;  overflow: hidden; text-overflow: ellipsis; }

        .show-soft .exp-nombre {
            font-size: 8.5px; font-weight: 700; color: var(--ink-2);
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        /* Miniatura 20px */
        .show-soft .exp-link { line-height: 0; display: inline-block; }
        .show-soft .exp-thumb {
            width: 20px; height: 20px; object-fit: cover;
            border-radius: 3px; border: 1px solid var(--grid-2);
            display: block; margin: 0 auto;
        }
        .show-soft .exp-thumb-empty {
            width: 20px; height: 20px; border-radius: 3px;
            border: 1px dashed var(--grid-2); background: var(--head); color: #d4dae2;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 8px; margin: 0 auto;
        }

        /* Chips */
        .show-soft .sh-chip {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 7.5px; font-weight: 700;
            padding: 1px 6px; border-radius: 8px; line-height: 13px;
            border: 1px solid; white-space: nowrap; flex-shrink: 0; text-transform: uppercase;
        }
        .show-soft .sh-chip i { font-size: 7px; line-height: 1; }
        .show-soft .sh-chip.ok   { color: #15803d; background: #f0fdf4; border-color: #bbf7d0; }
        .show-soft .sh-chip.off  { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }
        .show-soft .sh-chip.tipo { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
        .show-soft .sh-chip.tint { color: #7c3aed; background: #f5f3ff; border-color: #ddd6fe; }
        .show-soft .sh-chip .t { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* ═══ CINTA DE TÍTULO (individual) ═══ */
        .show-soft .sh-titlebar {
            background: #fff; border: 1px solid var(--grid-2); border-radius: 8px;
            padding: 12px 16px; display: flex; justify-content: space-between;
            align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(15,23,42,0.03);
        }
        .show-soft .sh-live {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 7px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
            color: var(--accent); background: var(--accent-bg);
            border: 1px solid var(--accent-bd); border-radius: 8px;
            padding: 2px 8px; line-height: 14px;
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
        .show-soft .sh-titlebar .strong { font-size: 12px; font-weight: 700; color: var(--ink-2); }
        .show-soft .sh-titlebar .sub    { font-size: 8px; color: var(--ink-3); }
        .show-soft .btn-soft {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid var(--accent-bd); border-radius: 5px;
            font-family: inherit; font-size: 8.5px; font-weight: 700; padding: 5px 12px;
            text-decoration: none; transition: all .15s ease;
        }
        .show-soft .btn-soft:hover { background: #e3f1e9; color: #34684e; text-decoration: none; }
        .show-soft .btn-soft i { font-size: 9.5px; }

        /* ═══ COLUMNA PREVIA (individual) ═══ */
        .show-soft .pv-sticky { position: sticky; top: 12px; }
        .show-soft .pv-label {
            display: flex; align-items: center; gap: 5px;
            font-size: 7.5px; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--ink-3); margin-bottom: 5px;
        }
        .show-soft .pv-label svg { width: 10px; height: 10px; stroke: var(--ink-4); }

        .show-soft .pv-card { background: #fff; border: 1px solid var(--grid-2); border-radius: 9px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 14px rgba(15,23,42,0.04); }
        .show-soft .pv-carousel { height: 180px; background: #f4f6f8; position: relative; flex-shrink: 0; border-bottom: 1px solid var(--grid); }
        .show-soft .pv-carousel img { object-fit: cover; object-position: center; width: 100%; height: 100%; }
        .show-soft .pv-carousel .carousel-inner,
        .show-soft .pv-carousel .carousel-item { width: 100%; height: 100%; }
        .show-soft .pv-carousel .carousel-control-prev,
        .show-soft .pv-carousel .carousel-control-next { width: 28px; opacity: .25; transition: opacity .15s ease; z-index: 5; }
        .show-soft .pv-carousel:hover .carousel-control-prev,
        .show-soft .pv-carousel:hover .carousel-control-next { opacity: .7; }
        .show-soft .pv-carousel .carousel-control-prev-icon,
        .show-soft .pv-carousel .carousel-control-next-icon { width: 14px; height: 14px; filter: invert(.3); }

        .show-soft .portada-badge {
            position: absolute; bottom: 6px; left: 6px; z-index: 5;
            font-size: 7px; font-weight: 700; letter-spacing: .06em;
            color: var(--accent); background: #ffffff;
            border: 1px solid var(--accent-bd);
            border-radius: 4px; padding: 1px 7px; line-height: 13px;
        }
        .show-soft .pv-empty-media {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
            background: var(--head); color: #b3bcc7; font-size: 8.5px; font-style: italic;
        }

        .show-soft .float-btn {
            width: 24px; height: 24px; border-radius: 6px; padding: 0;
            background: rgba(255,255,255,.95); border: 1px solid var(--grid-2);
            color: var(--ink-3); display: inline-flex; align-items: center; justify-content: center;
            transition: all .15s ease; z-index: 5;
        }
        .show-soft .float-btn:hover { color: var(--accent); border-color: var(--accent-bd); }
        .show-soft .float-btn svg { width: 11px; height: 11px; }
        .show-soft .btn-favorito svg.heart { stroke: #b91c1c; }

        .show-soft .pv-body { padding: 12px 14px; display: flex; flex-direction: column; flex-grow: 1; }
        .show-soft .pv-body .strong { font-size: 11px; font-weight: 700; color: var(--ink-2); }

        .show-soft .star-row svg { width: 9px; height: 9px; }
        .show-soft .star-row svg polygon { fill: #15803d; }
        .show-soft .score { font-size: 8.5px; font-weight: 700; color: var(--ink-2); }
        .show-soft .rev-count { font-size: 8px; color: var(--ink-3); display: inline-flex; align-items: center; gap: 3px; }

        .show-soft .loc { font-size: 8.5px; color: var(--ink-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .show-soft .loc i { color: #94a3b8; }

        .show-soft .spec-box { display: flex; border: 1px solid var(--grid-2); border-radius: 6px; background: var(--head); margin-bottom: 10px; overflow: hidden; }
        .show-soft .spec-box .half { flex: 1; padding: 7px 10px; display: flex; flex-direction: column; gap: 1px; min-width: 0; }
        .show-soft .spec-box .half + .half { border-left: 1px solid var(--grid-2); }
        .show-soft .spec-box .lbl { font-size: 7px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4); }
        .show-soft .spec-box .val { font-size: 11px; font-weight: 800; color: var(--ink-2); font-variant-numeric: tabular-nums; }

        .show-soft .kicker { font-size: 7.5px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--ink-4); }

        .show-soft .tarifa-box { background: #fafdfb; border: 1px solid var(--accent-bd); border-radius: 6px; padding: 9px 11px; }
        .show-soft .tarifa-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 6px; margin-bottom: 6px; }
        .show-soft .tarifa-name  { font-size: 9px; font-weight: 700; color: var(--ink-2); }
        .show-soft .tarifa-fecha { font-size: 7.5px; color: var(--ink-4); }
        .show-soft .tarifa-precio { display: block; font-size: 11px; font-weight: 800; color: var(--accent); font-variant-numeric: tabular-nums; white-space: nowrap; }
        .show-soft .tarifa-precio small { display: block; font-size: 7px; font-weight: 700; color: var(--ink-4); }
        .show-soft .restr-row { display: flex; gap: 6px; }
        .show-soft .restr-cell { flex: 1; background: #fff; border: 1px solid var(--grid-2); border-radius: 4px; padding: 5px 8px; display: flex; flex-direction: column; }
        .show-soft .restr-cell .lbl { font-size: 7px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-4); }
        .show-soft .restr-cell .val { font-size: 9px; font-weight: 800; color: var(--ink-2); font-variant-numeric: tabular-nums; }
        .show-soft .restr-cell .val.blue { color: #1d4ed8; }
        .show-soft .tarifa-empty {
            border: 1px dashed var(--grid-2); background: var(--head); border-radius: 6px;
            padding: 12px; text-align: center; font-size: 8.5px; color: var(--ink-4); font-style: italic;
        }

        /* ═══ ALERTAS ═══ */
        .show-soft .sh-alert {
            display: flex; justify-content: space-between; align-items: center;
            border-radius: 6px; padding: 8px 12px; font-size: 9px; font-weight: 600;
            margin-bottom: 12px; border: 1px solid;
        }
        .show-soft .sh-alert.ok  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .show-soft .sh-alert.err { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .show-soft .sh-alert button { background: none; border: 0; font-size: 13px; line-height: 1; color: inherit; padding: 0 2px; }

        /* ═══ MÓDULO 1 (individual) ═══ */
        .show-soft .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 8px; }
        .show-soft .stat-cell { border: 1px solid var(--grid-2); background: var(--head); border-radius: 7px; padding: 9px 12px; min-width: 0; }
        .show-soft .stat-cell .lbl { font-size: 7.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--ink-3); display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
        .show-soft .stat-cell .lbl i { font-size: 8.5px; color: var(--accent); }
        .show-soft .stat-cell .big { font-size: 12px; font-weight: 800; color: var(--ink-2); }
        .show-soft .stat-cell .big small { font-size: 7px; font-weight: 700; color: var(--ink-4); }
        .show-soft .stat-cell .sub { font-size: 7.5px; color: var(--ink-3); }

        .show-soft .vis-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 8px; font-weight: 700; padding: 2px 9px;
            border-radius: 10px; line-height: 14px; border: 1px solid;
        }
        .show-soft .vis-pill .dotp { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .show-soft .vis-on  { color: #15803d; background: #f0fdf4; border-color: #bbf7d0; }
        .show-soft .vis-off { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }

        .show-soft .m1-chev { width: 10px; height: 10px; stroke: var(--ink-4); transition: transform .2s ease; }
        .show-soft [data-bs-toggle="collapse"][aria-expanded="false"] .m1-chev { transform: rotate(-90deg); }

        /* ═════════ SPEC DE DISTANCIAS · INM-v3.7 (tab terminal TIP-v1.3) ═════════ */
        #inmDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
        #inmDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
        .inm-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
        .inm-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: inmDbgBlink 1s steps(2,start) infinite; }
        @keyframes inmDbgBlink { 50% { opacity: .35; } }
        .inm-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
        #inmDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        .inm-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
        .inm-dbg-controls { display: flex; gap: 4px; }
        .inm-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
        .inm-dbg-btn:hover { background: #475569; }
        #inmDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
        .inm-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
        .inm-dbg-time { color: #64748b; margin-right: 4px; }
        .inm-dbg-info { color: #93c5fd; } .inm-dbg-ok { color: #86efac; }
        .inm-dbg-warn { color: #fde68a; } .inm-dbg-err { color: #fca5a5; }
        .inm-dbg-net { color: #c4b5fd; } .inm-dbg-debug { color: #94a3b8; }
    </style>

    @if($modoGlobal)

        {{-- 1. HERO BANNER INMUEBLES GLOBAL (ESTILO FIN-v2.0-DBG) --}}
        <div class="inm-hero-pro">
            <i class="bi bi-buildings hero-watermark"></i>
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative" style="z-index: 2;">
                <div style="max-width: 680px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-success text-white fw-bold" style="font-size: 7.5px; letter-spacing: .5px; text-transform: uppercase;">
                            <i class="bi bi-shield-check me-1"></i> Ecosistema Inmobiliario RSV
                        </span>
                        <span class="badge rounded-pill" style="font-size: 7px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; font-family: ui-monospace, monospace;">
                            INM-v3.7-DBG
                        </span>
                    </div>
                    <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">
                        Catálogo y Visión Global de Expedientes Inmobiliarios
                    </h6>
                    <p class="mb-2 text-light opacity-75" style="font-size: 9.5px;">
                        Supervisa el estado operativo de todas las propiedades, capacidades máximas, tarifas vigentes y accesos directos al expediente de gestión de cada inmueble.
                    </p>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="inm-hero-btn-primary" onclick="window.scrollTo({top: 220, behavior: 'smooth'});">
                            <i class="bi bi-table"></i> Explorar Catálogo ({{ $inmTotalCount }})
                        </button>
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="lbl text-light opacity-50">Propiedades Activas</div>
                    <div class="fw-bold text-success num" style="font-size: 19px; line-height: 1.15;">
                        {{ $inmActivosCnt }} <span style="font-size: 9px; color: #94a3b8;">/ {{ $inmTotalCount }}</span>
                    </div>
                    <div class="sub text-light opacity-75" style="font-size: 8px;">
                        {{ $inmInactivosCnt }} inactiva(s) · {{ $inmTotalBloqueos }} bloqueo(s)
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TARJETAS KPI INMOBILIARIAS --}}
        <div class="inm-kpi-grid">
            <div class="inm-kpi-card" title="Total de propiedades registradas en el sistema">
                <div>
                    <div class="lbl">Total Propiedades</div>
                    <div class="val num" style="font-size: 13px; color: var(--ink-2);">{{ $inmTotalCount }}</div>
                    <div class="sub">en catálogo maestro</div>
                </div>
                <div class="inm-kpi-icon" style="color:#1d4ed8; background:#eff6ff; border-color:#bfdbfe;">
                    <i class="bi bi-buildings-fill"></i>
                </div>
            </div>

            <div class="inm-kpi-card" title="Propiedades con visibilidad activa para reservas">
                <div>
                    <div class="lbl">Activas / En Vivo</div>
                    <div class="val num" style="font-size: 13px; color: #15803d;">{{ $inmActivosCnt }}</div>
                    <div class="sub">disponibles en plataforma</div>
                </div>
                <div class="inm-kpi-icon" style="color:#15803d; background:#f0fdf4; border-color:#bbf7d0;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <div class="inm-kpi-card" title="Total de reservas históricas asociadas a las propiedades">
                <div>
                    <div class="lbl">Reservas Vinculadas</div>
                    <div class="val num" style="font-size: 13px; color: #b45309;">{{ $inmTotalReservas }}</div>
                    <div class="sub">expedientes de estancia</div>
                </div>
                <div class="inm-kpi-icon" style="color:#b45309; background:#fffbeb; border-color:#fde68a;">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>

            <div class="inm-kpi-card" title="Propiedades inactivas y bloqueos de mantenimiento registrados">
                <div>
                    <div class="lbl">Inactivas / Bloqueos</div>
                    <div class="val num" style="font-size: 13px; color: #7c3aed;">{{ $inmInactivosCnt }} <span style="font-size: 9px; color: #94a3b8;">inact.</span></div>
                    <div class="sub"><b>{{ $inmTotalBloqueos }}</b> bloqueo(s) en calendario</div>
                </div>
                <div class="inm-kpi-icon" style="color:#7c3aed; background:#f5f3ff; border-color:#ddd6fe;">
                    <i class="bi bi-pause-circle-fill"></i>
                </div>
            </div>
        </div>

        {{-- 3. TABLA GLOBAL --}}
        <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0" style="font-size: 10.5px; color: #334155;">
                    <i class="bi bi-houses text-success me-1"></i> Visión Global de Expedientes
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
                                $activoFila = $esActivoProp($prop);
                            @endphp
                            <tr>
                                <td class="exp-num">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    @if($portada)
                                        <a href="{{ $portada->url_archivo }}" target="_blank" title="Ver portada" class="exp-link text-decoration-none">
                                            <img src="{{ $portada->url_archivo }}" alt="" class="exp-thumb">
                                        </a>
                                    @else
                                        <span class="exp-thumb-empty"><i class="bi bi-image"></i></span>
                                    @endif
                                </td>
                                <td class="exp-ellip">
                                    <span class="exp-nombre" title="{{ $prop->name }}">{{ $prop->name }}</span>
                                </td>
                                <td class="exp-ellip-sm">
                                    @if($tipoInm)
                                        <span class="sh-chip tipo" title="{{ $tipoInm }}">
                                            <i class="bi bi-tag-fill"></i><span class="t">{{ $tipoInm }}</span>
                                        </span>
                                    @else
                                        <span style="color: var(--ink-4);">—</span>
                                    @endif
                                </td>
                                <td class="exp-ellip-sm" title="{{ $prop->city ?? '' }}" style="color: var(--ink-3);">
                                    {{ $prop->city ?? '—' }}
                                </td>
                                <td class="exp-num text-center">{{ $prop->capacidad_maxima ?? '—' }}</td>
                                <td class="text-end fw-bold" style="color: #15803d;">
                                    @if($ut)
                                        ${{ number_format($ut->precio_minimo_reserva ?? 0, 0) }}
                                    @else
                                        <span style="color: var(--ink-4);">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold" style="color: #1d4ed8;">
                                    @if($ut)
                                        {{ $ut->dias_maximos ?: '∞' }}
                                    @else
                                        <span style="color: var(--ink-4);">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($nFotos > 0)
                                        <span class="sh-chip tipo"><i class="bi bi-images"></i>{{ $nFotos }}</span>
                                    @else
                                        <span style="color: var(--ink-4);">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($nBloq > 0)
                                        <span class="sh-chip tint"><i class="bi bi-calendar-x"></i>{{ $nBloq }}</span>
                                    @else
                                        <span style="color: var(--ink-4);">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($nRes > 0)
                                        <span class="sh-chip ok"><i class="bi bi-journal-check"></i>{{ $nRes }}</span>
                                    @else
                                        <span style="color: var(--ink-4);">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($activoFila)
                                        <span class="sh-chip ok"><i class="bi bi-check-lg"></i>Activo</span>
                                    @else
                                        <span class="sh-chip off"><i class="bi bi-dash-lg"></i>Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => $prop->id]) }}"
                                       class="btn btn-sm btn-link p-0 text-decoration-none"
                                       style="font-size: 8.5px; color: #3d7a5c; font-weight: 700;"
                                       title="Abrir expediente completo">
                                        Gestionar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4" style="color: #94a3b8; font-size: 9px;">
                                    <i class="bi bi-houses me-1"></i> No hay inmuebles registrados en el catálogo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else

        {{-- MODO INDIVIDUAL · EXPEDIENTE COMPLETO · SOLO LECTURA --}}
        <div class="sh-titlebar">
            <div>
                <span class="sh-live mb-1"><span class="pulse"></span> EXPEDIENTE #{{ $inmueble->id }}</span>
                <div class="strong">Panel de Gestión: {{ $inmueble->name }} <span class="badge rounded-pill ms-1" style="font-size:6px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">INM-v3.7-DBG</span></div>
                <div class="sub">Vista informativa: identidad, tarifas y fotos del inmueble</div>
            </div>
            <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => 'todos']) }}" class="btn-soft">
                <i class="bi bi-arrow-left"></i> Volver al Catálogo
            </a>
        </div>

        <div class="row g-3">
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
                                    @if($esActivoProp($inmueble))
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

                            <div class="spec-box">
                                <div class="half">
                                    <span class="lbl">Capacidad</span>
                                    <span class="val">{{ $inmueble->capacidad_maxima ?? 'N/A' }} <small style="font-size:6.5px;color:var(--ink-4);">pers.</small></span>
                                </div>
                                <div class="half">
                                    <span class="lbl">Fotos</span>
                                    <span class="val" style="color: #1d4ed8;">{{ $inmueble->multimedia->count() }}</span>
                                </div>
                                <div class="half">
                                    <span class="lbl">Reservas</span>
                                    <span class="val" style="color: #15803d;">{{ $inmueble->reservas_count ?? 0 }}</span>
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
                                                ${{ number_format($ultimaTarifa->precio_minimo_reserva ?? 0, 0) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="restr-row">
                                        <div class="restr-cell">
                                            <span class="lbl">Precio noche</span>
                                            <span class="val">${{ number_format($ultimaTarifa->precio_noche ?? 0, 0) }}</span>
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

                <div class="p-3 bg-white rounded-3 border mb-3" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="button"
                                class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-2"
                                data-bs-toggle="collapse" data-bs-target="#collapseNucleoInmueble"
                                aria-expanded="true" aria-controls="collapseNucleoInmueble">
                            <i class="bi bi-fingerprint text-success" style="font-size: 9px;"></i>
                            <h6 class="fw-bold mb-0" style="font-size: 10px; color: #334155;">Identidad y Especificaciones</h6>
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
                                <span class="big" style="color: #15803d;">{{ $inmueble->reservas_count ?? 0 }}</span>
                            </div>
                            <div class="stat-cell">
                                <span class="lbl"><i class="bi bi-calendar-x"></i> Bloqueos</span>
                                <span class="big" style="color: #7c3aed;">{{ $inmueble->bloqueos_calendario_count ?? 0 }}</span>
                            </div>
                            <div class="stat-cell">
                                <span class="lbl"><i class="bi bi-eye"></i> Visibilidad</span>
                                <div class="mt-1">
                                    @if($esActivoProp($inmueble))
                                        <span class="vis-pill vis-on"><span class="dotp"></span> En vivo</span>
                                    @else
                                        <span class="vis-pill vis-off"><span class="dotp"></span> Borrador</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('rsv.admin.partials.inmuebles.tarifa', ['soloLectura' => true])
                @include('rsv.admin.partials.inmuebles.galeria', ['soloLectura' => true])
            </div>
        </div>

    @endif

    <script>
    (function () {
        'use strict';
        var root = document.getElementById('showRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var DASH = root.getAttribute('data-dash') || '';
        var SRVDBG = @json($inmDbg);

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
                        'font-weight:700;line-height:1;}',
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
                        t.title = 'Debug Inmuebles (INM-v3.7-DBG)';
                        t.innerHTML = '<span class="inm-dbg-led" id="inmDbgLed"></span>DBG<span class="inm-dbg-badge" id="inmDbgBadge">0</span>';
                        document.body.appendChild(t);
                    }
                    if (!$('inmDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'inmDbgPanel';
                        p.innerHTML = '<div class="inm-dbg-header" id="inmDbgHeader">' +
                            '<span>DEBUG · Inmuebles (N2) · INM-v3.7-DBG</span>' +
                            '<span class="inm-dbg-controls">' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgCopy">Copiar</button>' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgClear">Limpiar</button>' +
                            '<button type="button" class="inm-dbg-btn" id="inmDbgClose">✕</button>' +
                            '</span></div><div id="inmDbgBody"></div>';
                        document.body.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle', 'psDbgToggle', 'trxDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; }
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('inmDbgToggle'), p = $('inmDbgPanel');
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
                return api;
            })();
        if (!DBG) { return; }

        DBG.log('INM-v3.7-DBG activo · modo=' + (SRVDBG && SRVDBG.modoGlobal ? 'GLOBAL' : 'INDIVIDUAL') +
            ' · expedientes=' + (SRVDBG ? SRVDBG.expedientes : 0) +
            ' · activos=' + (SRVDBG ? SRVDBG.activos : 0) +
            ' · inactivos=' + (SRVDBG ? SRVDBG.inactivos : 0), 'ok');

        try {
            var __q = new URLSearchParams(window.location.search || '');
            if (__q.has('inmueble_id')) {
                var __pane = root.closest('.tab-pane');
                if (__pane && __pane.id && !__pane.classList.contains('active')) {
                    var __link = document.querySelector('[data-bs-target="#' + __pane.id + '"], [href="#' + __pane.id + '"]');
                    if (__link && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        bootstrap.Tab.getOrCreateInstance(__link).show();
                        DBG.log('auto-tab · tab #' + __pane.id + ' activado', 'ok');
                    }
                }
            }
        } catch (eAT) {}

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
            DBG.log('clic interno → inmueble_id=' + rid, 'info');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (!r.ok) {
                    return r.text().then(function (t) {
                        throw new Error('HTTP ' + r.status + ' · ' + extraerErrorLaravel(t));
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
                DBG.log('fragmento inmuebles recargado', 'ok');
            }).catch(function (e2) {
                DBG.log('swap falló (' + e2.message + ') → navegación nativa', 'err');
                window.location.href = urlDestino;
            });
        }

        if (window.__inmLinkHandler) { document.removeEventListener('click', window.__inmLinkHandler, true); }
        window.__inmLinkHandler = function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            if (!FRAG || !DASH) { return; }
            var a = (e.target && e.target.closest) ? e.target.closest('a[href]') : null;
            if (!a || a.target === '_blank' || a.hasAttribute('download')) { return; }
            if (!a.closest('#showRoot')) { return; }
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
    })();
    </script>

</div>
