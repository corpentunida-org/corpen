{{-- ============================================================================
     RSV · ADMIN · CALENDARIO · BLOQUEOS · BLQ-v2.0-DBG
     Parcial: resources/views/rsv/admin/partials/calendario/bloqueos.blade.php
     ============================================================================ --}}

@php
    $blqErrores = [];

    /* 1. Contexto de inmueble (Global vs Individual) */
    $blqCtxInmId = null;
    if (isset($inmueble) && $inmueble) {
        $blqCtxInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $blqCtxInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $blqCtxInmId = (int) request('inmueble_id');
    }
    $blqEsGlobal = empty($blqCtxInmId);

    /* 2. Autoconsulta defensiva de $inmuebles (corregida: solo modelos de Inmueble) */
    $inmueblesFaltan = !isset($inmuebles) || $inmuebles === null || count($inmuebles) === 0;
    if ($inmueblesFaltan) {
        $inmuebles = collect();
        foreach ([
            '\App\Models\Rsv\CatalogoInmueble',
            '\App\Models\RsvCatalogoInmueble',
            '\App\Models\CatalogoInmueble',
            '\App\Models\RsvInmueble',
            '\App\Models\Inmueble',
        ] as $blqClaseInm) {
            if (!class_exists($blqClaseInm)) continue;
            try {
                try { $inmuebles = $blqClaseInm::orderBy('name')->get(); }
                catch (\Throwable $blqE) { $inmuebles = $blqClaseInm::orderBy('nombre')->get(); }
                break;
            } catch (\Throwable $blqE) { continue; }
        }
        if (count($inmuebles) === 0) {
            foreach (['rsv_catalogo_inmuebles', 'rsv_catalogo_inmueble', 'catalogo_inmuebles', 'rsv_inmuebles', 'inmuebles'] as $blqTablaInm) {
                if (\Illuminate\Support\Facades\Schema::hasTable($blqTablaInm)) {
                    $blqCols = \Illuminate\Support\Facades\Schema::getColumnListing($blqTablaInm);
                    $blqColNombre = in_array('name', $blqCols) ? 'name' : (in_array('nombre', $blqCols) ? 'nombre' : 'id');
                    $inmuebles = collect(\Illuminate\Support\Facades\DB::table($blqTablaInm)->orderBy($blqColNombre)->get());
                    break;
                }
            }
        }
    }

    /* 3. Autoconsulta defensiva de $bloqueos (incluyendo \App\Models\Rsv\BloqueoCalendario) */
    $bloqueosFaltan = !isset($bloqueos) || $bloqueos === null;
    if ($bloqueosFaltan) {
        $bloqueos = collect();
        foreach ([
            '\App\Models\Rsv\BloqueoCalendario',
            '\App\Models\RsvBloqueoCalendario',
            '\App\Models\BloqueoCalendario',
            '\App\Models\RsvBloqueo',
        ] as $blqClase) {
            if (!class_exists($blqClase)) continue;
            try {
                $qB = $blqClase::query();
                if (method_exists($blqClase, 'inmueble')) {
                    $qB->with('inmueble');
                }
                if (!$blqEsGlobal && is_numeric($blqCtxInmId)) {
                    $qB->where('id_rsv_catalogo_inmueble', $blqCtxInmId);
                }
                $bloqueos = $qB->orderByDesc('fecha_inicio')->get();
                break;
            } catch (\Throwable $blqE) {
                $blqErrores[] = $blqE->getMessage();
                continue;
            }
        }
        if (count($bloqueos) === 0) {
            foreach (['rsv_bloqueos_calendario', 'bloqueos_calendario', 'rsv_bloqueos', 'bloqueo_calendarios', 'bloqueos'] as $blqTabla) {
                if (\Illuminate\Support\Facades\Schema::hasTable($blqTabla)) {
                    $blqCols = \Illuminate\Support\Facades\Schema::getColumnListing($blqTabla);
                    $blqColFecha = in_array('fecha_inicio', $blqCols) ? 'fecha_inicio' : 'id';
                    $qDb = \Illuminate\Support\Facades\DB::table($blqTabla);
                    if (!$blqEsGlobal && is_numeric($blqCtxInmId) && in_array('id_rsv_catalogo_inmueble', $blqCols)) {
                        $qDb->where('id_rsv_catalogo_inmueble', $blqCtxInmId);
                    }
                    $bloqueos = collect($qDb->orderByDesc($blqColFecha)->get());
                    break;
                }
            }
        }
    }

    $mapaInmuebles = collect($inmuebles)->keyBy('id');

    /* 4. Rutas resilientes */
    $blqRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };
    $blqOcupadas  = $blqRuta(['rsv.reservas.fechas-ocupadas'], ':inmuebleId');
    $blqStore     = $blqRuta(['rsv.bloqueos-calendario.store', 'rsv.bloqueos.store']);
    $blqUpdate    = $blqRuta(['rsv.bloqueos-calendario.update', 'rsv.bloqueos.update'], ':bloqueoId');
    $blqDestroy   = $blqRuta(['rsv.bloqueos-calendario.destroy', 'rsv.bloqueos.destroy'], ':bloqueoId');
    $blqFragmento = $blqRuta(['rsv.admin.bloqueos.fragmento', 'rsv.admin.calendario.bloqueos.fragmento']);

    /* 5. Dataset JSON enriquecido con estado temporal (activo, proximo, finalizado) */
    $hoyYmd = now()->format('Y-m-d');
    $kpiActivos     = 0;
    $kpiProximos    = 0;
    $kpiFinalizados = 0;
    $kpiTotalDias   = 0;

    $blqItems = collect($bloqueos)->values()->map(function ($blqB) use ($mapaInmuebles, $hoyYmd, &$kpiActivos, &$kpiProximos, &$kpiFinalizados, &$kpiTotalDias) {
        $blqIni = \Illuminate\Support\Carbon::parse($blqB->fecha_inicio);
        $blqFin = \Illuminate\Support\Carbon::parse($blqB->fecha_fin);
        $fkInm  = $blqB->id_rsv_catalogo_inmueble ?? $blqB->inmueble_id ?? null;
        $blqInm = (is_object($blqB) && isset($blqB->inmueble) && $blqB->inmueble) ? $blqB->inmueble : $mapaInmuebles->get($fkInm);

        $iniYmd = $blqIni->format('Y-m-d');
        $finYmd = $blqFin->format('Y-m-d');
        $dias   = max(1, (int) $blqIni->diffInDays($blqFin) + 1);

        if ($finYmd < $hoyYmd) {
            $estado = 'finalizado';
            $kpiFinalizados++;
        } elseif ($iniYmd <= $hoyYmd && $finYmd >= $hoyYmd) {
            $estado = 'activo';
            $kpiActivos++;
        } else {
            $estado = 'proximo';
            $kpiProximos++;
        }

        $kpiTotalDias += $dias;

        return [
            'id'          => $blqB->id,
            'inmueble_id' => (string) $fkInm,
            'inmueble'    => (string) ($blqInm->name ?? $blqInm->nombre ?? ('Inmueble #' . $fkInm)),
            'inicio'      => $iniYmd,
            'fin'         => $finYmd,
            'desde'       => $blqIni->format('d/m/Y'),
            'hasta'       => $blqFin->format('d/m/Y'),
            'dias'        => $dias,
            'estado'      => $estado,
            'motivo'      => (string) ($blqB->motivo ?? ''),
        ];
    });

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $blqJson = json_encode($blqItems->all(), $jhFlags);
    if (!is_string($blqJson)) { $blqJson = '[]'; }

    $blqDbg = [
        'version'        => 'BLQ-v2.0-DBG',
        'vista'          => 'partials/calendario/bloqueos.blade.php',
        'render'         => now()->format('H:i:s'),
        'modo'           => $blqEsGlobal ? 'GLOBAL' : ('INMUEBLE #' . $blqCtxInmId),
        'auto_inmuebles' => $inmueblesFaltan,
        'auto_bloqueos'  => $bloqueosFaltan,
        'inmuebles'      => count($inmuebles),
        'bloqueos'       => $blqItems->count(),
        'kpis'           => [
            'activos'     => $kpiActivos,
            'proximos'    => $kpiProximos,
            'finalizados' => $kpiFinalizados,
            'dias'        => $kpiTotalDias,
        ],
        'editable'       => (bool) ($blqStore || $blqUpdate),
        'rutas'          => [
            'ocupadas'  => $blqOcupadas  ?: 'NO definida',
            'store'     => $blqStore     ?: 'NO definida',
            'update'    => $blqUpdate    ?: 'NO definida',
            'destroy'   => $blqDestroy   ?: 'NO definida',
            'fragmento' => $blqFragmento ?: 'fallback URL actual',
        ],
        'errores'        => $blqErrores,
    ];
@endphp

{{-- BLQ-v2.0-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
<div id="blqDbgToggle" title="Debug Bloqueos (BLQ-v2.0-DBG)">
    <span class="blq-dbg-led" id="blqDbgLed"></span>DBG<span class="blq-dbg-badge" id="blqDbgBadge">0</span>
</div>
<div id="blqDbgPanel" role="dialog" aria-label="Panel de debug de Bloqueos">
    <div class="blq-dbg-header" id="blqDbgHeader">
        <span>DEBUG · Bloqueos (Calendario) · BLQ-v2.0-DBG</span>
        <span class="blq-dbg-controls">
            <button type="button" class="blq-dbg-btn" id="blqDbgRefresh" title="Refrescar fragmento por AJAX">↻ Frag</button>
            <button type="button" class="blq-dbg-btn" id="blqDbgCopy">Copiar</button>
            <button type="button" class="blq-dbg-btn" id="blqDbgClear">Limpiar</button>
            <button type="button" class="blq-dbg-btn" id="blqDbgClose">✕</button>
        </span>
    </div>
    <div id="blqDbgBody"></div>
</div>

<div class="reservas-soft p-3 bg-white rounded-3 border"
     id="bloqueos-panel"
     data-ctx-inmueble="{{ $blqCtxInmId ?? '' }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft {
        --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
        --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#b3bcc7;
        --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
    }
    .reservas-soft .lbl { font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); }
    .reservas-soft .val { font-size: 10.5px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
    .reservas-soft .sub { font-size: 8px; line-height: 1.35; color: var(--ink-3); }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 700; }
    .reservas-soft .num { font-variant-numeric: tabular-nums; }

    /* ── TARJETAS KPI DE BLOQUEOS (MISMO ESTILO QUE FIN-v2.0-DBG) ── */
    .blq-kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px;
    }
    @media (max-width: 767px) { .blq-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    .blq-kpi-card {
        background: #fff; border: 1px solid var(--grid-2); border-radius: 9px;
        padding: 10px 13px; display: flex; align-items: center; justify-content: space-between;
        gap: 10px; cursor: pointer; transition: all 0.16s ease;
    }
    .blq-kpi-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.04); }
    .blq-kpi-icon {
        width: 34px; height: 34px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; border: 1px solid;
    }

    /* ── CHIPS DE FILTRO RÁPIDO ── */
    .blq-chip-bar { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
    .blq-filter-chip {
        border: 1px solid var(--grid-2); background: #fff; color: var(--ink);
        font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
    }
    .blq-filter-chip:hover { border-color: var(--accent-bd); color: var(--ink-2); }
    .blq-filter-chip.active { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); font-weight: 700; }
    .blq-chip-cnt { font-size: 7px; font-weight: 700; padding: 0 5px; border-radius: 99px; background: var(--grid); color: var(--ink-2); }
    .blq-filter-chip.active .blq-chip-cnt { background: var(--accent); color: #fff; }

    /* ── TABLA Y CONTROLES ── */
    .reservas-soft .table > :not(caption) > * > * { padding: 4px 8px; height: 22px; line-height: 1.2; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 8.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid-2) !important; }
    .reservas-soft .table tbody td { font-size: 8.5px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #f8fafc; --bs-table-accent-bg: transparent; }
    .reservas-soft .table tfoot > tr > td { font-size: 8.5px; font-weight: 700; color: var(--ink-2); background-color: var(--head); border-top: 2px solid var(--grid-2) !important; border-bottom: 0; white-space: nowrap; }
    .reservas-soft .table tfoot .st-total-label { color: var(--accent); letter-spacing: .03em; }

    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2); font-family: inherit; font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
    .reservas-soft .btn-mini:hover { color: var(--accent); border-color: var(--accent-bd); background: var(--accent-bg); }
    .reservas-soft .btn-green { background: var(--accent); color: #fff; border: 1px solid var(--accent); font-family: inherit; font-size: 8.5px; font-weight: 700; padding: 3px 10px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
    .reservas-soft .btn-green:hover { background: #2e5c43; border-color: #2e5c43; }

    .reservas-soft .xls-search { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 9px; box-shadow: none !important; outline: 0; min-width: 175px; }
    .reservas-soft .xls-search:focus { background: #fff; border-color: var(--accent-bd); }
    .reservas-soft .blq-select-filter { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 8px; outline: 0; cursor: pointer; max-width: 195px; }
    .reservas-soft .blq-select-filter:focus { background: #fff; border-color: var(--accent-bd); }

    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 700; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 2px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }

    /* ── PÍLDORAS DE ESTADO TEMPORAL ── */
    .blq-st-pill { display: inline-flex; align-items: center; gap: 4px; padding: 1px 7px; border-radius: 10px; font-size: 7px; font-weight: 800; border: 1px solid; text-transform: uppercase; letter-spacing: .03em; }
    .blq-st-activo     { color: #b45309; background: #fffbeb; border-color: #fde68a; }
    .blq-st-proximo    { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
    .blq-st-finalizado { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }

    /* ── EXTRAS MODAL Y FLATPICKR ── */
    .reservas-soft .blq-etiqueta { display: block; font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-3); margin-bottom: 3px; }
    .reservas-soft .blq-obligatorio { color: #b91c1c; }
    .reservas-soft .blq-ayuda { font-size: 7.5px; color: var(--ink-3); margin-top: 3px; }
    .reservas-soft .blq-fila { display: grid; grid-template-columns: 1fr; gap: 12px; }
    @media (min-width: 768px) { .reservas-soft .blq-fila { grid-template-columns: 1fr 1fr; } }
    .reservas-soft .blq-calendario { border: 1px solid var(--grid-2); border-radius: 6px; padding: 6px; background: #fff; }
    .reservas-soft .blq-calendario .flatpickr-calendar { margin: 0 auto; box-shadow: none; font-family: inherit; }
    .reservas-soft .blq-calendario .flatpickr-day.selected,
    .reservas-soft .blq-calendario .flatpickr-day.selected:hover { background: var(--accent); border-color: var(--accent); }
    .reservas-soft .blq-calendario .flatpickr-day.inRange { background: var(--accent-bg); border-color: var(--accent-bg); box-shadow: none; }
    .reservas-soft .flatpickr-day.blq-ocp-reserva { background: #fdf5f4 !important; border-color: #f0dcd8 !important; color: #b36b64 !important; }
    .reservas-soft .flatpickr-day.blq-ocp-bloqueo { background: #fbf8ee !important; border-color: #eee5cd !important; color: #8f7a45 !important; }
    .reservas-soft .blq-rango-texto { font-size: 8.5px; font-weight: 600; color: var(--accent); min-height: 14px; margin-bottom: 4px; }
    .reservas-soft .blq-leyenda { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; font-size: 8px; color: var(--ink-3); margin-top: 6px; }
    .reservas-soft .blq-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; margin-right: 4px; }
    .reservas-soft .blq-dot-reserva { background: #fdf5f4; border-color: #f0dcd8; }
    .reservas-soft .blq-dot-bloqueo { background: #fbf8ee; border-color: #eee5cd; }
    .reservas-soft .blq-spin { display: inline-block; width: 10px; height: 10px; border: 2px solid var(--accent-bd); border-top-color: var(--accent); border-radius: 50%; animation: blq-girar .7s linear infinite; vertical-align: -2px; margin-right: 4px; }
    @keyframes blq-girar { to { transform: rotate(360deg); } }
    .reservas-soft .blq-banner-errores { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 8.5px; padding: 6px 9px; border-radius: 5px; margin-bottom: 8px; }
    .reservas-soft .blq-banner-errores ul { margin: 3px 0 0; padding-left: 14px; }
    .reservas-soft .blq-aviso-solape { background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-size: 8px; padding: 6px 9px; border-radius: 5px; margin-top: 8px; }
    .reservas-soft .blq-invalid { border-color: #fca5a5 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.10); }

    /* Toast */
    .blq-toast { position: fixed; right: 1rem; bottom: 1.5rem; z-index: 5000; min-width: 240px; max-width: 320px; padding: 8px 12px; border-radius: 6px; font-size: 10.5px; font-weight: 600; color: #fff; background: #3d7a5c; box-shadow: 0 10px 30px rgba(0,0,0,.18); transition: opacity .3s ease, transform .3s ease; }
    .blq-toast.blq-error { background: #b91c1c; }
    .blq-toast-out { opacity: 0; transform: translateY(.4rem); }

    /* ═════════ SPEC DE DISTANCIAS · BLQ-v2.0-DBG (tab terminal TIP-v1.3) ═════════ */
    #blqDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
    #blqDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
    .blq-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .blq-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: blqDbgBlink 1s steps(2,start) infinite; }
    @keyframes blqDbgBlink { 50% { opacity: .35; } }
    .blq-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
    #blqDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .blq-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
    .blq-dbg-controls { display: flex; gap: 4px; }
    .blq-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
    .blq-dbg-btn:hover { background: #475569; }
    #blqDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
    .blq-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
    .blq-dbg-time { color: #64748b; margin-right: 4px; }
    .blq-dbg-info { color: #93c5fd; } .blq-dbg-ok { color: #86efac; }
    .blq-dbg-warn { color: #fde68a; } .blq-dbg-err { color: #fca5a5; }
    .blq-dbg-net { color: #c4b5fd; } .blq-dbg-debug { color: #94a3b8; }
</style>

    {{-- 1. TARJETAS KPI DE BLOQUEOS (CLICABLES PARA FILTRAR) --}}
    <div class="blq-kpi-grid">
        <div class="blq-kpi-card js-blq-kpi-filter" data-estado="" title="Ver todos los bloqueos registrados">
            <div>
                <div class="lbl">Total Bloqueos</div>
                <div class="val num" style="font-size: 13px; color: var(--ink-2);" id="blqKpiTotal">{{ $blqItems->count() }}</div>
                <div class="sub">registrados en sistema</div>
            </div>
            <div class="blq-kpi-icon" style="color:#3d7a5c; background:#f0f7f2; border-color:#d5e7dc;">
                <i class="bi bi-calendar-x"></i>
            </div>
        </div>

        <div class="blq-kpi-card js-blq-kpi-filter" data-estado="activo" title="Clic para filtrar bloqueos En Curso hoy">
            <div>
                <div class="lbl">En Curso Hoy</div>
                <div class="val num" style="font-size: 13px; color: #b45309;" id="blqKpiActivos">{{ $kpiActivos }}</div>
                <div class="sub">mantenimientos activos</div>
            </div>
            <div class="blq-kpi-icon" style="color:#b45309; background:#fffbeb; border-color:#fde68a;">
                <i class="bi bi-tools"></i>
            </div>
        </div>

        <div class="blq-kpi-card js-blq-kpi-filter" data-estado="proximo" title="Clic para filtrar bloqueos Próximos">
            <div>
                <div class="lbl">Próximos</div>
                <div class="val num" style="font-size: 13px; color: #1d4ed8;" id="blqKpiProximos">{{ $kpiProximos }}</div>
                <div class="sub">programados a futuro</div>
            </div>
            <div class="blq-kpi-icon" style="color:#1d4ed8; background:#eff6ff; border-color:#bfdbfe;">
                <i class="bi bi-calendar-event"></i>
            </div>
        </div>

        <div class="blq-kpi-card js-blq-kpi-filter" data-estado="finalizado" title="Total de días acumulados en bloqueo">
            <div>
                <div class="lbl">Días Bloqueados</div>
                <div class="val num" style="font-size: 13px; color: #64748b;" id="blqKpiDias">{{ $kpiTotalDias }} d</div>
                <div class="sub"><b id="blqKpiFinalizados">{{ $kpiFinalizados }}</b> finalizado(s)</div>
            </div>
            <div class="blq-kpi-icon" style="color:#64748b; background:#f8fafc; border-color:#e2e8f0;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    {{-- 2. CABECERA + CHIPS DE ESTADO + FILTRO DE INMUEBLE + BUSCADOR + ACCIONES --}}
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap: 8px; border-color: var(--grid) !important;">
        <div class="blq-chip-bar">
            <h6 class="fw-bold mb-0 me-1" style="font-size: 10.5px; color: #334155;">
                <i class="bi bi-calendar-x-fill text-success me-1"></i> Bloqueos de Calendario
            </h6>
            <span class="badge rounded-pill me-1" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; font-family: ui-monospace, monospace;">
                BLQ-v2.0-DBG
            </span>

            <button type="button" class="blq-filter-chip active" data-estado="">
                Todos <span class="blq-chip-cnt" id="blqChipAll">{{ $blqItems->count() }}</span>
            </button>
            <button type="button" class="blq-filter-chip" data-estado="activo">
                En Curso <span class="blq-chip-cnt" id="blqChipAct">{{ $kpiActivos }}</span>
            </button>
            <button type="button" class="blq-filter-chip" data-estado="proximo">
                Próximos <span class="blq-chip-cnt" id="blqChipPrx">{{ $kpiProximos }}</span>
            </button>
            <button type="button" class="blq-filter-chip" data-estado="finalizado">
                Finalizados <span class="blq-chip-cnt" id="blqChipFin">{{ $kpiFinalizados }}</span>
            </button>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
            <select id="blqFilterInmueble" class="blq-select-filter" title="Filtrar tabla por propiedad">
                <option value="">Todas las propiedades ({{ count($inmuebles) }})</option>
                @foreach($inmuebles as $inmOpt)
                    <option value="{{ $inmOpt->id }}" @selected((string) $blqCtxInmId === (string) $inmOpt->id)>
                        {{ $inmOpt->name ?? $inmOpt->nombre ?? ('Inmueble #' . $inmOpt->id) }}
                    </option>
                @endforeach
            </select>

            <input type="text" id="blqSearch" class="xls-search" placeholder="Buscar inmueble, motivo, fecha…" autocomplete="off">

            <button type="button" class="btn-mini" id="blqBtnRefrescar" title="Sincronizar bloqueos sin recargar página">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button type="button" class="btn-mini" id="blqBtnCsv" title="Exportar bloqueos filtrados a Excel CSV">
                <i class="bi bi-download"></i> CSV
            </button>
            @if($blqStore)
                <button type="button" class="btn-green" id="blqBtnNuevo">
                    <i class="bi bi-plus-lg"></i> Nuevo Bloqueo
                </button>
            @endif
        </div>
    </div>

    {{-- 3. TABLA DE BLOQUEOS --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 700px;">
            <thead>
                <tr>
                    <th style="width: 55px;">#ID</th>
                    <th>Propiedad / Inmueble</th>
                    <th style="width: 105px;">Estado</th>
                    <th style="width: 95px;">Desde</th>
                    <th style="width: 95px;">Hasta</th>
                    <th class="text-center" style="width: 75px;">Duración</th>
                    <th>Motivo del Bloqueo</th>
                    <th class="text-end" style="width: 115px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="blqTbody">
                <tr><td colspan="8" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="st-total-label">
                        <i class="bi bi-calculator me-1"></i>
                        MOSTRANDO <span id="blqCount">{{ $blqItems->count() }}</span> DE <span id="blqFootTotal">{{ $blqItems->count() }}</span> BLOQUEO(S)
                    </td>
                    <td class="text-center num"><span id="blqFootDias">{{ $kpiTotalDias }}</span> días</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- 4. MODAL FORMULARIO (CREAR / EDITAR CON FLATPICKR INLINE) --}}
    <div class="modal fade" id="modalBloqueo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 760px;">
            <div class="modal-content reservas-soft" style="border: 1px solid #e9eef4; border-radius: 8px; font-size: 9.5px;">
                <form id="formBloqueo" novalidate>
                    @csrf
                    <div class="modal-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                        <h6 class="modal-title mb-0 fw-bold" id="blqModalTitulo" style="font-size: 0.95rem;">Nuevo Bloqueo</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div id="blqBannerErrores" class="blq-banner-errores" style="display:none;"></div>

                        <div class="blq-fila">
                            {{-- Columna izquierda: datos --}}
                            <div>
                                <label class="blq-etiqueta" for="blqInmueble">Propiedad / Inmueble <span class="blq-obligatorio">*</span></label>
                                <select id="blqInmueble" name="id_rsv_catalogo_inmueble" class="form-select form-select-sm">
                                    <option value="">— Selecciona un inmueble —</option>
                                    @foreach($inmuebles as $inmItem)
                                        <option value="{{ $inmItem->id }}">{{ $inmItem->name ?? $inmItem->nombre ?? 'Inmueble #'.$inmItem->id }}</option>
                                    @endforeach
                                </select>
                                <div class="blq-ayuda">Si ya hay un inmueble filtrado en la página, se precarga automáticamente.</div>

                                <label class="blq-etiqueta" for="blqMotivo" style="margin-top: 14px;">Motivo Operativo <span class="blq-obligatorio">*</span></label>
                                <textarea id="blqMotivo" name="motivo" rows="5" maxlength="500" class="form-control form-control-sm"
                                          placeholder="Ej.: Mantenimiento preventivo de aire acondicionado, pintura, fumigación…"></textarea>
                                <div class="blq-ayuda">Entre 5 y 500 caracteres.</div>

                                <div id="blqAvisoSolape" class="blq-aviso-solape" style="display:none;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    El rango seleccionado se solapa con fechas ya ocupadas. Puedes continuar si es intencional.
                                </div>
                            </div>

                            {{-- Columna derecha: calendario Flatpickr --}}
                            <div>
                                <label class="blq-etiqueta">Fechas del bloqueo <span class="blq-obligatorio">*</span></label>
                                <div id="blqRangoTexto" class="blq-rango-texto">Sin fechas seleccionadas</div>

                                <input type="hidden" name="fecha_inicio" id="blqFechaInicio">
                                <input type="hidden" name="fecha_fin" id="blqFechaFin">
                                <input type="text" id="blqAnclaCal" class="d-none">

                                <div id="blqCalendario" class="blq-calendario"></div>

                                <div id="blqCargando" class="blq-ayuda" style="display:none;">
                                    <span class="blq-spin"></span>Cargando ocupación del inmueble…
                                </div>
                                <div id="blqInfoOcupacion" class="blq-ayuda"></div>

                                <div class="blq-leyenda">
                                    <span><i class="blq-dot blq-dot-reserva"></i> Reserva activa</span>
                                    <span><i class="blq-dot blq-dot-bloqueo"></i> Otro bloqueo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" id="blqBtnGuardar" style="font-size: 10.5px;"><i class="bi bi-check-lg"></i> Crear bloqueo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. MODAL ELIMINAR --}}
    <div class="modal fade" id="blqModalDel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content reservas-soft" style="border: 1px solid #e9eef4; border-radius: 8px; font-size: 9.5px;">
                <div class="modal-body p-3 text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.3rem;"></i>
                    <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar bloqueo</h6>
                    <p class="mb-0" style="font-size: 8.5px; color: #64748b;">
                        Se liberarán las fechas de <b id="blqDelName" style="color: #334155;">—</b>.<br>
                        Esta acción actualizará el calendario inmediatamente.
                    </p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="blqDelGo" class="btn btn-sm py-1 px-2" style="font-size: 10.5px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700;"><i class="bi bi-trash3"></i> Confirmar eliminación</button>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="blqDataset">{!! $blqJson !!}</script>

    <script>
    (function () {
        'use strict';

        if (window.__blqPanelV2Init) return;
        window.__blqPanelV2Init = true;

        var CSRF = '{{ csrf_token() }}';
        var URLS = {
            ocupadas:  '{{ $blqOcupadas ?? "" }}',
            store:     '{{ $blqStore ?? "" }}',
            update:    '{{ $blqUpdate ?? "" }}',
            destroy:   '{{ $blqDestroy ?? "" }}',
            fragmento: '{{ $blqFragmento ?? "" }}'
        };
        var EDITABLE = !!(URLS.store || URLS.update);

        var modo = 'crear';
        var idEditando = null;
        var pendienteEliminar = null;
        var ocupadas = [];
        var q = '';
        var filtroEstado = '';
        var filtroInmuebleId = '{{ $blqCtxInmId ?? "" }}';
        var fpCalendario = null;

        /* ═══ 0) PANEL DE DEBUG LOCAL BLQ-v2.0-DBG (diseño TIP-v1.3) ═══ */
        var DBG = (window.BlqDbg && window.BlqDbg.alive && window.BlqDbg.alive())
            ? window.BlqDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function unicarDbg() {
                    var ts = document.querySelectorAll('#blqDbgToggle');
                    for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                    var ps = document.querySelectorAll('#blqDbgPanel');
                    for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
                }
                function asegurarNodos() {
                    unicarDbg();
                    var rootDbg = document.getElementById('bloqueos-panel');
                    if (!rootDbg) { return; }
                    if (!$('blqDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'blqDbgToggle';
                        t.title = 'Debug Bloqueos (BLQ-v2.0-DBG)';
                        t.innerHTML = '<span class="blq-dbg-led" id="blqDbgLed"></span>DBG<span class="blq-dbg-badge" id="blqDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('blqDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'blqDbgPanel';
                        p.innerHTML = '<div class="blq-dbg-header" id="blqDbgHeader">' +
                            '<span>DEBUG · Bloqueos (Calendario) · BLQ-v2.0-DBG</span>' +
                            '<span class="blq-dbg-controls">' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgRefresh">↻ Frag</button>' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgCopy">Copiar</button>' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgClear">Limpiar</button>' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgClose">✕</button>' +
                            '</span></div><div id="blqDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'finDbgToggle', 'psDbgToggle', 'trxDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; }
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('blqDbgToggle'), p = $('blqDbgPanel');
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
                        var b = $('blqDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'blq-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="blq-dbg-time">[' + hora() + ']</span>' +
                            '<span class="blq-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('blqDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('blqDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('blqDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('blqDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('blqDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('blqDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('blqDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('blqDbgBody'); if (b) { b.innerHTML = ''; } },
                    copy: function () {
                        var b = $('blqDbgBody'); if (!b) { return; }
                        var txt = '=== DEBUG bloqueos BLQ-v2.0-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('log copiado al portapapeles', 'ok');
                            });
                        }
                    },
                    ensure: function () { asegurarNodos(); },
                    alive: function () { return !!$('blqDbgToggle'); }
                };
                window.BlqDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure();
        var blqdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

        var SRVDBG = @json($blqDbg);
        blqdbg('BLQ-v2.0-DBG activo · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · bloqueos=' + (SRVDBG ? SRVDBG.bloqueos : 0) +
            ' · propiedades=' + (SRVDBG ? SRVDBG.inmuebles : 0), 'ok');
        if (SRVDBG && SRVDBG.kpis) {
            blqdbg('KPIs · en curso=' + SRVDBG.kpis.activos + ' · próximos=' + SRVDBG.kpis.proximos + ' · finalizados=' + SRVDBG.kpis.finalizados + ' · días=' + SRVDBG.kpis.dias, 'info');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            SRVDBG.errores.forEach(function (er) { blqdbg('⚠ SERVIDOR: ' + er, 'err'); });
        }

        function $(sel) { return document.querySelector(sel); }
        function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        function csrf() {
            if (CSRF) return CSRF;
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta && meta.content) return meta.content.trim();
            var hidden = document.querySelector('#formBloqueo [name="_token"]');
            return hidden ? hidden.value : '';
        }

        function leerDataset() {
            try { return JSON.parse((document.getElementById('blqDataset') || {}).textContent || '[]') || []; }
            catch (e) { return []; }
        }

        function aYmd(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }
        function formatoCorto(ymd) {
            var p = String(ymd).split('-');
            return p[2] + '/' + p[1] + '/' + p[0];
        }
        function textoRango(ini, fin) {
            return ini === fin ? formatoCorto(ini) : formatoCorto(ini) + ' → ' + formatoCorto(fin);
        }
        function enRango(ymd, r) { return ymd >= r.inicio && ymd <= r.fin; }

        /* ── Carga perezosa de Flatpickr ── */
        function cargarCss(href) {
            var link = document.createElement('link');
            link.id = 'blq-fp-css';
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        }
        function cargarScript(src, id) {
            return new Promise(function (resolve, reject) {
                var s = document.createElement('script');
                s.id = id;
                s.src = src;
                s.onload = resolve;
                s.onerror = function () { reject(new Error('No se pudo cargar ' + src)); };
                document.body.appendChild(s);
            });
        }
        async function asegurarFlatpickr() {
            if (window.flatpickr) return;
            if (!document.getElementById('blq-fp-css')) {
                cargarCss('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
            }
            if (!document.getElementById('blq-fp-js')) {
                await cargarScript('https://cdn.jsdelivr.net/npm/flatpickr', 'blq-fp-js');
            }
            if (!document.getElementById('blq-fp-l10n')) {
                await cargarScript('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', 'blq-fp-l10n');
            }
            if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.es) {
                flatpickr.localize(flatpickr.l10ns.es);
            }
        }

        function modalInst(id) {
            try { return bootstrap.Modal.getOrCreateInstance(document.getElementById(id)); }
            catch (e) { return null; }
        }

        function asegurarCalendario() {
            var cont = $('#blqCalendario');
            if (!cont || !window.flatpickr) return;
            if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) return;
            if (fpCalendario) { try { fpCalendario.destroy(); } catch (e) {} fpCalendario = null; }
            cont.innerHTML = '';

            fpCalendario = flatpickr('#blqAnclaCal', {
                mode: 'range',
                inline: true,
                appendTo: cont,
                dateFormat: 'Y-m-d',
                minDate: 'today',
                showMonths: 1,
                onChange: function (fechas) {
                    $('#blqFechaInicio').value = fechas[0] ? aYmd(fechas[0]) : '';
                    $('#blqFechaFin').value    = fechas[1] ? aYmd(fechas[1]) : (fechas[0] ? aYmd(fechas[0]) : '');
                    $('#blqRangoTexto').textContent = fechas[0]
                        ? textoRango(aYmd(fechas[0]), aYmd(fechas[1] || fechas[0]))
                        : 'Sin fechas seleccionadas';
                    revisarSolape();
                },
                onDayCreate: function (a, b, c, dayElem) {
                    var ymd = aYmd(dayElem.dateObj);
                    var r = null;
                    for (var i = 0; i < ocupadas.length; i++) {
                        if (enRango(ymd, ocupadas[i])) { r = ocupadas[i]; break; }
                    }
                    if (!r) return;
                    var esBloqueo = String(r.tipo || '').toLowerCase().indexOf('bloque') !== -1;
                    dayElem.classList.add(esBloqueo ? 'blq-ocp-bloqueo' : 'blq-ocp-reserva');
                }
            });
        }

        function calendarioFallback() {
            var cont = $('#blqCalendario');
            if (!cont || document.getElementById('blqFallbackInicio')) return;
            cont.innerHTML =
                '<div class="blq-aviso-solape" style="margin:0 0 6px;">Selecciona las fechas manualmente:</div>' +
                '<input type="date" class="form-control form-control-sm mb-1" id="blqFallbackInicio">' +
                '<input type="date" class="form-control form-control-sm" id="blqFallbackFin">';
            function sincro() {
                $('#blqFechaInicio').value = $('#blqFallbackInicio').value || '';
                $('#blqFechaFin').value    = $('#blqFallbackFin').value || $('#blqFallbackInicio').value || '';
                $('#blqRangoTexto').textContent = ($('#blqFechaInicio').value && $('#blqFechaFin').value)
                    ? textoRango($('#blqFechaInicio').value, $('#blqFechaFin').value)
                    : 'Sin fechas seleccionadas';
                revisarSolape();
            }
            cont.querySelector('#blqFallbackInicio').addEventListener('change', sincro);
            cont.querySelector('#blqFallbackFin').addEventListener('change', sincro);
        }

        async function abrirCalendarioListo() {
            if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) return;
            try { await asegurarFlatpickr(); }
            catch (e) { blqdbg('Flatpickr fallback a inputs date nativos', 'warn'); }
            asegurarCalendario();
            if (!fpCalendario) { calendarioFallback(); }
        }

        function repintarCalendario() {
            if (fpCalendario) { try { fpCalendario.redraw(); } catch (e) {} }
        }

        function inmuebleDesdeFiltroSuperior() {
            var selTabla = document.getElementById('blqFilterInmueble');
            if (selTabla && selTabla.value) return selTabla.value;
            var selCal = document.getElementById('filtro_inmueble_cal');
            if (selCal && selCal.value) return selCal.value;
            var panel = document.getElementById('bloqueos-panel');
            if (panel && panel.getAttribute('data-ctx-inmueble')) return panel.getAttribute('data-ctx-inmueble');
            return '';
        }

        function asegurarOpcionInmueble(valor) {
            var sel = $('#blqInmueble');
            if (!sel || !valor) return;
            if (!sel.querySelector('option[value="' + valor + '"]')) {
                var opt = document.createElement('option');
                opt.value = valor;
                opt.textContent = 'Inmueble #' + valor;
                sel.appendChild(opt);
            }
        }

        async function cargarOcupadas(inmuebleId) {
            var info = $('#blqInfoOcupacion');
            var spinner = $('#blqCargando');
            var __t0 = Date.now();
            ocupadas = [];
            repintarCalendario();
            if (info) info.textContent = '';
            if (!inmuebleId || !URLS.ocupadas) return;

            blqdbg('GET Ocupación · inmueble=#' + inmuebleId, 'net');
            if (spinner) spinner.style.display = 'block';
            try {
                var url = URLS.ocupadas.replace(':inmuebleId', encodeURIComponent(inmuebleId));
                var res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                var data = await res.json().catch(function () { return {}; });
                if (!res.ok || data.ok === false) {
                    throw new Error(data.message || 'No se pudo consultar la ocupación.');
                }
                ocupadas = (data.ocupadas || []).map(function (o) {
                    return {
                        inicio: String(o.inicio).slice(0, 10),
                        fin:    String(o.fin).slice(0, 10),
                        tipo:   o.tipo || ''
                    };
                });
                var nRes = ocupadas.filter(function (o) { return o.tipo.toLowerCase().indexOf('bloque') === -1; }).length;
                var nBlq = ocupadas.length - nRes;
                var nombre = (data.inmueble && (data.inmueble.name || data.inmueble.nombre)) || 'Inmueble';
                if (info) {
                    info.textContent = nombre + ': ' + ocupadas.length + ' rango(s) ocupado(s) (' + nRes + ' reservas · ' + nBlq + ' bloqueos)';
                }
                blqdbg('Ocupación cargada (' + (Date.now() - __t0) + 'ms) · ' + nRes + ' reserva(s) · ' + nBlq + ' bloqueo(s)', 'ok');
            } catch (e) {
                if (info) info.textContent = 'Aviso: ' + e.message;
                blqdbg('Ocupación falló: ' + e.message, 'warn');
            } finally {
                if (spinner) spinner.style.display = 'none';
                repintarCalendario();
                revisarSolape();
            }
        }

        function revisarSolape() {
            var aviso = $('#blqAvisoSolape');
            if (!aviso) return;
            var ini = $('#blqFechaInicio').value;
            var fin = $('#blqFechaFin').value;
            if (!ini || !fin || !ocupadas.length) { aviso.style.display = 'none'; return; }
            var choca = ocupadas.some(function (o) { return ini <= o.fin && fin >= o.inicio; });
            if (choca && aviso.style.display !== 'block') {
                blqdbg('Solape detectado en ' + ini + ' → ' + fin + ' (aviso no bloqueante)', 'warn');
            }
            aviso.style.display = choca ? 'block' : 'none';
        }

        var INPUT_POR_CAMPO = {
            id_rsv_catalogo_inmueble: '#blqInmueble',
            motivo: '#blqMotivo',
            fecha_inicio: '#blqCalendario',
            fecha_fin: '#blqCalendario'
        };

        function limpiarErrores() {
            var banner = $('#blqBannerErrores');
            if (banner) { banner.style.display = 'none'; banner.innerHTML = ''; }
            ['#blqInmueble', '#blqMotivo', '#blqCalendario'].forEach(function (sel) {
                var el = $(sel);
                if (el) el.classList.remove('blq-invalid');
            });
        }

        function pintarErrores(data) {
            limpiarErrores();
            var banner = $('#blqBannerErrores');
            if (!banner) return;
            var errores = (data && data.errors) ? data.errors : {};
            var lista = [];
            if (Array.isArray(errores)) {
                lista = errores.map(String);
            } else {
                Object.keys(errores).forEach(function (campo) {
                    var arr = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
                    arr.forEach(function (m) { lista.push(String(m)); });
                    var selector = INPUT_POR_CAMPO[campo];
                    if (selector) {
                        var input = $(selector);
                        if (input) input.classList.add('blq-invalid');
                    }
                });
            }
            if (!lista.length && data && data.message) lista.push(String(data.message));
            if (!lista.length) lista.push('Revisa los datos del formulario.');
            banner.innerHTML = '<strong>No se pudo guardar:</strong><ul>' +
                lista.map(function (m) { return '<li>' + m.replace(/</g, '&lt;') + '</li>'; }).join('') +
                '</ul>';
            banner.style.display = 'block';
        }

        function mostrarToast(mensaje, tipo) {
            var t = document.createElement('div');
            t.className = 'blq-toast' + (tipo === 'danger' ? ' blq-error' : '');
            t.textContent = mensaje;
            document.body.appendChild(t);
            setTimeout(function () { t.classList.add('blq-toast-out'); }, 3000);
            setTimeout(function () { t.remove(); }, 3400);
        }

        /* ── Refresco AJAX 100% sin location.reload() ── */
        async function refrescarPanel(motivo) {
            var actual = $('#bloqueos-panel');
            if (!actual) return;

            var urlBase = URLS.fragmento || window.location.pathname;
            var urlObj  = new URL(urlBase, window.location.origin);
            var ctxInm  = actual.getAttribute('data-ctx-inmueble') || '';
            if (ctxInm) urlObj.searchParams.set('inmueble_id', ctxInm);
            urlObj.searchParams.set('_t', Date.now());

            var __t0 = Date.now();
            blqdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

            try {
                var res = await fetch(urlObj.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    cache: 'no-store'
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                var html = await res.text();
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nuevo = doc.querySelector('#bloqueos-panel');
                if (!nuevo) throw new Error('Respuesta sin #bloqueos-panel');

                // Reemplazar todas las copias si el parcial está en Global e Individual
                var copias = document.querySelectorAll('#bloqueos-panel');
                copias.forEach(function (c) { c.replaceWith(nuevo.cloneNode(true)); });

                fpCalendario = null; // El nodo del modal se renovó
                DBG.ensure();
                render();
                blqdbg('Bloqueos sincronizados (' + (Date.now() - __t0) + 'ms) · total=' + leerDataset().length, 'ok');
            } catch (err) {
                blqdbg('Error refrescando fragmento: ' + (err && err.message ? err.message : 'red'), 'err');
            }
        }
        window.blqReloadFragment = refrescarPanel;

        function exitoYRefrescar(mensaje) {
            mostrarToast(mensaje);
            blqdbg(mensaje, 'ok');
            if (window.rsvCalendar && typeof window.rsvCalendar.refetchEvents === 'function') {
                try {
                    window.rsvCalendar.refetchEvents();
                    blqdbg('window.rsvCalendar.refetchEvents() disparado', 'info');
                } catch (e) {}
            }
            setTimeout(function () {
                refrescarPanel('post-crud');
            }, 220);
        }

        /* ── Renderizado de Tabla + Recálculo de KPIs y Contadores en Vivo ── */
        function render() {
            var tbody = document.getElementById('blqTbody');
            if (!tbody) return;
            var DS = leerDataset();

            var selInm = document.getElementById('blqFilterInmueble');
            var inmFiltro = selInm ? selInm.value : '';
            var chipAct = document.querySelector('#bloqueos-panel .blq-filter-chip.active');
            filtroEstado = chipAct ? (chipAct.getAttribute('data-estado') || '') : '';
            var t = q.toLowerCase().trim();

            // Contadores sobre el filtro de inmueble + texto
            var cntAll = 0, cntAct = 0, cntPrx = 0, cntFin = 0, sumDias = 0;

            var res = DS.filter(function (r) {
                if (inmFiltro && String(r.inmueble_id) !== String(inmFiltro)) return false;
                if (t) {
                    var paja = (r.inmueble + ' ' + r.motivo + ' ' + r.desde + ' ' + r.hasta + ' #' + r.id + ' ' + r.estado).toLowerCase();
                    if (paja.indexOf(t) === -1) return false;
                }
                cntAll++;
                if (r.estado === 'activo') cntAct++;
                else if (r.estado === 'proximo') cntPrx++;
                else cntFin++;

                if (filtroEstado && r.estado !== filtroEstado) return false;
                sumDias += (parseInt(r.dias, 10) || 0);
                return true;
            });

            var setTxt = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
            setTxt('blqCount', res.length);
            setTxt('blqFootTotal', DS.length);
            setTxt('blqFootDias', sumDias);
            setTxt('blqChipAll', cntAll);
            setTxt('blqChipAct', cntAct);
            setTxt('blqChipPrx', cntPrx);
            setTxt('blqChipFin', cntFin);
            setTxt('blqKpiTotal', cntAll);
            setTxt('blqKpiActivos', cntAct);
            setTxt('blqKpiProximos', cntPrx);
            setTxt('blqKpiFinalizados', cntFin);
            setTxt('blqKpiDias', sumDias + ' d');

            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="8" class="text-center py-4" style="font-size:8.5px;color:#94a3b8;">'
                  + (DS.length ? '<i class="bi bi-funnel d-block mb-1" style="font-size:16px;"></i> Ningún bloqueo coincide con el filtro activo.' : '<i class="bi bi-calendar-x d-block mb-1" style="font-size:16px;"></i> Sin bloqueos de calendario registrados todavía.')
                  + '</td></tr>';
            } else {
                res.forEach(function (r) {
                    var mot = String(r.motivo || '');
                    var corto = mot.length > 65 ? mot.slice(0, 65) + '…' : mot;
                    var pillCls = r.estado === 'activo' ? 'blq-st-activo' : (r.estado === 'proximo' ? 'blq-st-proximo' : 'blq-st-finalizado');
                    var pillTxt = r.estado === 'activo' ? 'EN CURSO' : (r.estado === 'proximo' ? 'PRÓXIMO' : 'FINALIZADO');

                    h += '<tr data-blq-row="1">'
                       + '<td class="sub num fw-bold">#' + esc(r.id) + '</td>'
                       + '<td class="strong">' + esc(r.inmueble) + '</td>'
                       + '<td><span class="blq-st-pill ' + pillCls + '">' + pillTxt + '</span></td>'
                       + '<td class="num">' + esc(r.desde) + '</td>'
                       + '<td class="num">' + esc(r.hasta) + '</td>'
                       + '<td class="text-center"><span class="count-badge"><b>' + esc(r.dias) + '</b> d</span></td>'
                       + '<td title="' + esc(mot) + '" style="max-width:250px;overflow:hidden;text-overflow:ellipsis;">' + esc(corto) + '</td>'
                       + '<td class="text-end" style="white-space:nowrap;">'
                       + (EDITABLE ? '<button type="button" class="btn-mini blq-edit" data-id="' + esc(r.id) + '"><i class="bi bi-pencil"></i> Editar</button> ' : '')
                       + (URLS.destroy ? '<button type="button" class="btn-mini text-danger blq-del" data-id="' + esc(r.id) + '"><i class="bi bi-trash3"></i></button>' : '')
                       + '</td>'
                       + '</tr>';
                });
            }
            tbody.innerHTML = h;
        }

        /* ── Exportar CSV de los bloqueos filtrados ── */
        function exportarCsvBloqueos() {
            var filas = document.querySelectorAll('#blqTbody tr[data-blq-row]');
            if (!filas.length) {
                mostrarToast('No hay bloqueos visibles para exportar.', 'danger');
                return;
            }
            var escCsv = function (v) {
                var s = String(v == null ? '' : v).replace(/\s+/g, ' ').trim();
                return (s.indexOf(';') !== -1 || s.indexOf('"') !== -1) ? '"' + s.replace(/"/g, '""') + '"' : s;
            };
            var lineas = ['ID;Propiedad;Estado;Desde;Hasta;Dias;Motivo'];
            filas.forEach(function (tr) {
                var tds = tr.querySelectorAll('td');
                var row = [];
                for (var i = 0; i < 7 && i < tds.length; i++) {
                    row.push(escCsv(tds[i].textContent));
                }
                lineas.push(row.join(';'));
            });
            var blob = new Blob(['\ufeff' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'bloqueos_calendario_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a); a.click(); a.remove();
            blqdbg('CSV exportado · ' + filas.length + ' bloqueo(s)', 'ok');
            mostrarToast('Exportados ' + filas.length + ' bloqueo(s) a CSV.');
        }

        function limpiarFormulario() {
            var form = $('#formBloqueo');
            if (form) form.reset();
            $('#blqFechaInicio').value = '';
            $('#blqFechaFin').value = '';
            $('#blqRangoTexto').textContent = 'Sin fechas seleccionadas';
            $('#blqInmueble').value = '';
            ocupadas = [];
            var info = $('#blqInfoOcupacion'); if (info) info.textContent = '';
            var aviso = $('#blqAvisoSolape'); if (aviso) aviso.style.display = 'none';
            limpiarErrores();
            if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) {
                fpCalendario.clear();
                try { fpCalendario.set('minDate', 'today'); } catch (e) {}
                repintarCalendario();
            }
        }

        async function abrirFormulario(r) {
            limpiarFormulario();
            var titulo = $('#blqModalTitulo');
            var btn = $('#blqBtnGuardar');
            var sel = $('#blqInmueble');

            if (r) {
                modo = 'editar';
                idEditando = r.id;
                blqdbg('modal · editar bloqueo #' + r.id + ' (' + r.inmueble + ')', 'info');
                if (titulo) titulo.textContent = 'Editar Bloqueo #' + r.id;
                if (btn) btn.innerHTML = '<i class="bi bi-check-lg"></i> Guardar cambios';
                asegurarOpcionInmueble(r.inmueble_id);
                if (sel) sel.value = String(r.inmueble_id);
                var mot = $('#blqMotivo'); if (mot) mot.value = r.motivo || '';
            } else {
                modo = 'crear';
                idEditando = null;
                if (titulo) titulo.textContent = 'Nuevo Bloqueo';
                if (btn) btn.innerHTML = '<i class="bi bi-check-lg"></i> Crear bloqueo';

                var pre = inmuebleDesdeFiltroSuperior();
                if (pre && sel) { asegurarOpcionInmueble(pre); sel.value = pre; }
                blqdbg('modal · crear nuevo bloqueo' + (pre ? ' (inmueble #' + pre + ' precargado)' : ''), 'info');
            }

            var m = modalInst('modalBloqueo');
            if (m) m.show();

            await abrirCalendarioListo();

            if (r && r.inicio && r.fin) {
                $('#blqFechaInicio').value = r.inicio;
                $('#blqFechaFin').value = r.fin;
                $('#blqRangoTexto').textContent = textoRango(r.inicio, r.fin);
                if (fpCalendario) {
                    try { fpCalendario.set('minDate', null); } catch (err) {}
                    try { fpCalendario.setDate([r.inicio, r.fin], false); } catch (err) {}
                } else {
                    var fi = document.getElementById('blqFallbackInicio');
                    var ff = document.getElementById('blqFallbackFin');
                    if (fi) fi.value = r.inicio;
                    if (ff) ff.value = r.fin;
                }
            } else if (fpCalendario) {
                try { fpCalendario.set('minDate', 'today'); } catch (err) {}
            }

            if (sel && sel.value) {
                await cargarOcupadas(sel.value);
            }
        }

        function enviar(url, method, fd) {
            if (!url) {
                blqdbg('AJAX abortado · ruta no disponible', 'err');
                mostrarToast('Ruta no disponible para esta acción.', 'danger');
                return Promise.resolve(false);
            }
            fd.append('_token', csrf());
            if (method !== 'POST') fd.append('_method', method);
            blqdbg('AJAX · ' + method + ' ' + url, 'net');

            return fetch(url, {
                method: 'POST',
                body: fd,
                headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) {
                return r.json().catch(function () { return { ok: r.ok, success: r.ok }; });
            })
            .then(function (j) {
                if (j && j.errors) {
                    blqdbg('AJAX 422 · ' + Object.keys(j.errors).join(', '), 'err');
                    pintarErrores(j);
                    return false;
                }
                if (j && (j.ok === false || j.success === false)) {
                    blqdbg('AJAX falló · ' + (j.message || 'error'), 'err');
                    mostrarToast(j.message || 'No se pudo completar la operación.', 'danger');
                    return false;
                }
                blqdbg('AJAX OK', 'ok');
                return true;
            })
            .catch(function () {
                blqdbg('AJAX error de conexión', 'err');
                mostrarToast('Error de conexión.', 'danger');
                return false;
            });
        }

        function init() {
            render();

            document.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.closest) return;

                if (t.closest('#blqDbgToggle'))  { DBG.togglePanel(); return; }
                if (t.closest('#blqDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
                if (t.closest('#blqDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
                if (t.closest('#blqDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
                if (t.closest('#blqDbgRefresh') || t.closest('#blqBtnRefrescar')) {
                    e.stopPropagation();
                    refrescarPanel('manual-refresh');
                    return;
                }

                if (t.closest('#blqBtnCsv'))   { exportarCsvBloqueos(); return; }
                if (t.closest('#blqBtnNuevo')) { abrirFormulario(null); return; }

                // Chips de filtro de estado temporal
                var chip = t.closest('.blq-filter-chip');
                if (chip && chip.closest('#bloqueos-panel')) {
                    document.querySelectorAll('#bloqueos-panel .blq-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                    chip.classList.add('active');
                    render();
                    return;
                }

                // Tarjetas KPI que filtran la tabla
                var kpiCard = t.closest('.js-blq-kpi-filter');
                if (kpiCard && kpiCard.closest('#bloqueos-panel')) {
                    var estKpi = kpiCard.getAttribute('data-estado') || '';
                    var targetChip = document.querySelector('#bloqueos-panel .blq-filter-chip[data-estado="' + estKpi + '"]');
                    if (targetChip) targetChip.click();
                    return;
                }

                // Editar bloqueo
                var bE = t.closest('.blq-edit');
                if (bE && EDITABLE) {
                    var r = leerDataset().find(function (x) { return String(x.id) === bE.getAttribute('data-id'); });
                    if (r) abrirFormulario(r);
                    return;
                }

                // Abrir confirmación de eliminación
                var bD = t.closest('.blq-del');
                if (bD && URLS.destroy) {
                    var r2 = leerDataset().find(function (x) { return String(x.id) === bD.getAttribute('data-id'); });
                    if (!r2) return;
                    pendienteEliminar = r2;
                    var nom = document.getElementById('blqDelName');
                    if (nom) nom.textContent = r2.inmueble + ' (' + r2.desde + ' → ' + r2.hasta + ')';
                    var mDel = modalInst('blqModalDel');
                    if (mDel) mDel.show();
                    return;
                }

                // Confirmar eliminación
                var bGo = t.closest('#blqDelGo');
                if (bGo && pendienteEliminar) {
                    var objetivo = pendienteEliminar;
                    pendienteEliminar = null;
                    bGo.disabled = true;
                    enviar(URLS.destroy.replace(':bloqueoId', encodeURIComponent(objetivo.id)), 'DELETE', new FormData())
                        .then(function (ok) {
                            bGo.disabled = false;
                            var mDel2 = modalInst('blqModalDel');
                            if (mDel2) mDel2.hide();
                            if (ok) exitoYRefrescar('Bloqueo eliminado correctamente.');
                        });
                }
            });

            document.addEventListener('dblclick', function (e) {
                if (e.target && e.target.closest && e.target.closest('#blqDbgHeader')) {
                    DBG.close();
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.id === 'blqSearch') {
                    q = e.target.value;
                    render();
                }
            });

            document.addEventListener('change', function (e) {
                if (!e.target) return;
                if (e.target.id === 'blqInmueble') {
                    cargarOcupadas(e.target.value);
                    return;
                }
                if (e.target.id === 'blqFilterInmueble') {
                    render();
                }
            });

            document.addEventListener('shown.bs.tab', function (e) {
                if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
                var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
                if (target === '#cal-sub-bloqueos' || target === '#single-cal-sub-bloqueos') {
                    render();
                }
            });

            document.addEventListener('submit', function (e) {
                if (!e.target || e.target.id !== 'formBloqueo') return;
                e.preventDefault();
                limpiarErrores();

                var fd = new FormData(e.target);
                var esEditar = (modo === 'editar' && idEditando);

                if (!fd.get('id_rsv_catalogo_inmueble')) {
                    pintarErrores({ errors: { id_rsv_catalogo_inmueble: ['Selecciona un inmueble.'] } });
                    return;
                }
                if (!fd.get('fecha_inicio') || !fd.get('fecha_fin')) {
                    pintarErrores({ errors: { fecha_inicio: ['Selecciona la fecha de inicio y fin en el calendario.'] } });
                    return;
                }

                var url = esEditar
                    ? URLS.update.replace(':bloqueoId', encodeURIComponent(idEditando))
                    : URLS.store;

                var btn = $('#blqBtnGuardar');
                var textoOriginal = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="blq-spin" style="border-color:#5a5a5a;border-top-color:#fff;"></span>Guardando…';
                }

                enviar(url, esEditar ? 'PUT' : 'POST', fd).then(function (ok) {
                    if (btn) { btn.disabled = false; btn.innerHTML = textoOriginal; }
                    if (!ok) return;
                    var m = modalInst('modalBloqueo');
                    if (m) m.hide();
                    exitoYRefrescar(esEditar ? 'Bloqueo actualizado correctamente.' : 'Bloqueo creado correctamente.');
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
</div>
