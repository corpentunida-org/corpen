{{-- ============================================================================
     RSV · ADMIN · RESERVAS · ESTADOS · ST-v1-DBG
     Parcial: resources/views/rsv/admin/partials/reservas/status.blade.php

     · Base v5 (conservada ÍNTEGRA): CRUD 100% Bootstrap/Blade + fila TOTAL,
       alertas de sesión/validación visibles, old() en forms, conteos
       contextuales (global vs inmueble), guard de eliminar global, tfoot
       "Sin estado asignado" + TOTAL, búsqueda progresiva con ids únicos.
     · PANEL DE DEBUG (ST-v1-DBG · diseño TIP-v1.3 aprobado · canónico
       BLQ-v1-2): pestaña terminal "DBG" de 16px fija SIEMPRE en la esquina
       inferior-derecha (right:0 · bottom:0, radius 3px solo superior-
       izquierdo) con LED de estado (verde = ok · rojo pulsante + conteo =
       errores) y panel cajón de 380px alineado al mismo borde. La regla
       exacta de distancias está documentada como "SPEC DE DISTANCIAS"
       dentro del CSS, y el panel esquiva a los toggles hermanos de las
       otras vistas midiendo píxeles reales (getBoundingClientRect ·
       bottom = max(20, vecino + 4)).
       Exclusivo de este fragmento: el toggle y el panel son HERMANOS de
       #stRoot (el refresco AJAX interno solo intercambia el root y los
       nodos DBG sobreviven con el CSS que viaja dentro de él) y unicarDbg()
       deduplica dejando UN solo toggle/panel por página. Nada se cuelga de
       document.body: el respaldo re-crea DENTRO de #stRoot.
       Telemetría que reporta: contexto (global/inmueble), estados y
       reservas cargados, reservas sin estado, estados bloqueados para
       eliminar, alertas de sesión/validación, rutas (store/update/destroy/
       fragmento), submits AJAX (alta/edición/baja), rechazos HTTP con
       mensaje, refresco del fragmento, búsqueda progresiva y errores
       capturados por el servidor.
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

{{-- ST-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano del root: vive y muere con la vista · NO viaja en el refresco AJAX interno) --}}
<div id="stDbgToggle" title="Debug Estados (ST-v1-DBG)"><span class="st-dbg-led" id="stDbgLed"></span>DBG<span class="st-dbg-badge" id="stDbgBadge">0</span></div>
<div id="stDbgPanel" role="dialog" aria-label="Panel de debug de Estados de Reserva">
    <div class="st-dbg-header" id="stDbgHeader">
        <span>DEBUG · Estados de Reserva · ST-v1-DBG</span>
        <span class="st-dbg-controls">
            <button type="button" class="st-dbg-btn" id="stDbgCopy">Copiar</button>
            <button type="button" class="st-dbg-btn" id="stDbgClear">Limpiar</button>
            <button type="button" class="st-dbg-btn" id="stDbgClose">✕</button>
        </span>
    </div>
    <div id="stDbgBody"></div>
</div>

<div class="reservas-soft p-3 bg-white rounded-3 border" id="stRoot"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }

    /* ═══ TFOOT · fila TOTAL (intuitiva) ═══ */
    .reservas-soft .table tfoot > tr > td {
        font-size: 8.5px; font-weight: 700; color: var(--ink-2);
        background-color: var(--head);
        border-top: 2px solid var(--grid-2) !important;
        border-bottom: 0; white-space: nowrap;
    }
    .reservas-soft .table tfoot tr.st-row-sin td {
        font-weight: 600; color: var(--ink-3);
        border-top: 1px dashed var(--grid-2) !important;
        font-size: 8px;
    }
    .reservas-soft .table tfoot .st-total-label { color: var(--accent); letter-spacing: .03em; }

    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: not-allowed; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
    .reservas-soft .ctx-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 7.5px; font-weight: 600; color: var(--accent); background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap; }
    .reservas-soft .st-warn { background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px; color: #c98a85; font-size: 8px; font-weight: 600; padding: 6px 9px; }
    .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 5px; font-family: Calibri, "Segoe UI", sans-serif; font-size: 9.5px; color: #64748b; }
    .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px; }
    .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
    .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }

    /* ═════════ SPEC DE DISTANCIAS · ST-v1-DBG (tab terminal TIP-v1.3) ═════════
       TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la esquina
                   inferior-derecha (nunca sube: con 16px de alto cabe bajo
                   cualquier otro toggle sin tocarlo)
                 · altura fija 16px · padding lateral 8px
                 · radius 3px solo sup-izq · borde 1px arriba/izq
                 · monospace 8px · LED 5px (verde ok / rojo pulsante = error)
       PANEL     · right:0px · bottom = max(20, vecino + 4px) medido en px
                   reales con getBoundingClientRect (los toggles ocultos 0×0
                   no cuentan) · (20 = 16 de tab + 4 de aire mínimo)
                 · ancho 380px · radius 6px 0 0 0 (cajón pegado al borde)
       UNICIDAD  · toggle y panel son HERMANOS de #stRoot (el refresco AJAX
                   interno solo intercambia el root: los nodos y el CSS
                   sobreviven) y unicarDbg() deja UN solo toggle/panel por
                   página. El respaldo re-crea DENTRO de #stRoot. */
    #stDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    #stDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
    .st-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .st-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: stDbgBlink 1s steps(2,start) infinite; }
    @keyframes stDbgBlink { 50% { opacity: .35; } }
    .st-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
    #stDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .st-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
    .st-dbg-controls { display: flex; gap: 4px; }
    .st-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
    .st-dbg-btn:hover { background: #475569; }
    #stDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
    .st-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
    .st-dbg-time { color: #64748b; margin-right: 4px; }
    .st-dbg-info { color: #93c5fd; } .st-dbg-ok { color: #86efac; }
    .st-dbg-warn { color: #fde68a; } .st-dbg-err { color: #fca5a5; }
    .st-dbg-net { color: #c4b5fd; } .st-dbg-debug { color: #94a3b8; }
</style>

@php
    /*
    ══════════════════════════════════════════════════════════════
    CONTEXTO GLOBAL/INDIVIDUAL
    · CRUD de catálogo global, pero los CONTADORES respetan el
      filtro superior (global = todo el sistema · individual = solo
      reservas del inmueble seleccionado).
    · El guard de ELIMINAR usa SIEMPRE el conteo global (destroy
      elimina el estado de todo el sistema).
    · TFOOT: "Sin estado asignado" + TOTAL → la columna siempre
      suma el total de reservas del contexto actual.
    ══════════════════════════════════════════════════════════════
    */
    $stDbgErrores = [];

    $stInm = null; $stInmId = null; $stGlobal = true; $stCtxName = 'Global';

    if (isset($inmueble) && $inmueble) {
        $stInm = $inmueble;
        $stInmId = is_object($stInm) ? ($stInm->id ?? null) : $stInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $stInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $stInmId = request('inmueble_id');
    }
    $stGlobal = empty($stInmId);
    if (!$stGlobal) {
        $stCtxName = (is_object($stInm) ? ($stInm->name ?? null) : null) ?? ('Inmueble #' . $stInmId);
    }

    // Detección defensiva de rutas (buscando nombres con y sin prefijo rsv.)
    $stRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
            // Intento con prefijo 'rsv.' por si acaso
            if (\Illuminate\Support\Facades\Route::has('rsv.' . $c)) {
                return $param !== null ? route('rsv.' . $c, $param) : route('rsv.' . $c);
            }
        }
        return null;
    };
    $stStore    = $stRuta(['rsv.statuses.store', 'statuses.store', 'rsv.status.store']);
    $stUpdate   = $stRuta(['rsv.statuses.update', 'statuses.update', 'rsv.status.update'], 'ID');
    $stDestroy  = $stRuta(['rsv.statuses.destroy', 'statuses.destroy', 'rsv.status.destroy'], 'ID');

    $stItems = collect(); $stTotalesGlobales = collect();
    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $q = \App\Models\Rsv\Status::query();

            if ($stGlobal) {
                $q->withCount('reservas');
            } else {
                $q->withCount(['reservas' => function ($qq) use ($stInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $stInmId);
                }]);
            }

            // Conteo GLOBAL por estado (para el guard de eliminar)
            if (class_exists(\App\Models\Rsv\Reserva::class)) {
                $stTotalesGlobales = \App\Models\Rsv\Reserva::query()
                    ->selectRaw('id_rsv_statuses, COUNT(*) as c')
                    ->groupBy('id_rsv_statuses')
                    ->pluck('c', 'id_rsv_statuses');
            }

            $stItems = $q->orderBy('name')->get()->map(function ($s) use ($stTotalesGlobales) {
                return [
                    'id'       => $s->id,
                    'name'     => (string) $s->name,
                    'color'    => (string) ($s->color_hex ?: '#a3cfb6'),
                    'cnt'      => (int) ($s->reservas_count ?? 0),
                    'cntTotal' => (int) ($stTotalesGlobales[$s->id] ?? 0),
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('st dataset: '.$e->getMessage());
        $stDbgErrores[] = 'dataset: ' . $e->getMessage();
        $stItems = collect();
    }

    /* ═══ TOTALES PARA EL TFOOT ═══ */
    $stSumaCnt = (int) $stItems->sum('cnt');

    // Total de reservas del CONTEXTO (global o del inmueble)
    $stTotalReservas = 0;
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $stQ = \App\Models\Rsv\Reserva::query();
            if (!$stGlobal && $stInmId) { $stQ->where('id_rsv_catalogo_inmueble', $stInmId); }
            $stTotalReservas = (clone $stQ)->count();
        }
    } catch (\Throwable $e) {
        $stTotalReservas = $stSumaCnt;
        $stDbgErrores[] = 'total del contexto: ' . $e->getMessage();
    }

    // Reservas sin ningún estado asignado (diferencia corregida)
    $stSinEstado = max(0, $stTotalReservas - $stSumaCnt);

    /* ═══ Colector del panel de debug (ST-v1-DBG) · via al panel vía la directiva json de Blade ═══ */
    $stFragmentoDbg = \Illuminate\Support\Facades\Route::has('rsv.admin.reservas.status.fragmento')
        ? route('rsv.admin.reservas.status.fragmento')
        : null;
    $stDbg = [
        'version'    => 'ST-v1-DBG',
        'vista'      => 'partials/reservas/status.blade.php',
        'render'     => now()->format('H:i:s'),
        'contexto'   => $stGlobal ? 'global' : ('inmueble · ' . $stCtxName),
        'estados'    => $stItems->count(),
        'reservas'   => $stTotalReservas,
        'sin_estado' => $stSinEstado,
        'bloqueados' => $stItems->where('cntTotal', '>', 0)->count(),
        'alertas'    => [
            'success'    => session('success') ? 'sí' : 'no',
            'error'      => session('error') ? 'sí' : 'no',
            'validacion' => $errors->any() ? 'sí' : 'no',
        ],
        'rutas'      => [
            'store'     => $stStore   ?: 'NO definida',
            'update'    => $stUpdate  ?: 'NO definida',
            'destroy'   => $stDestroy ?: 'NO definida',
            'fragmento' => $stFragmentoDbg ?: 'NO definida',
        ],
        'errores'    => $stDbgErrores,
    ];
@endphp


    <!-- ═══ ALERTAS · success / error / validación (fix fallos silenciosos) ═══ -->
    @if (session('success'))
        <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-exclamation-circle-fill me-1"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-flag me-1" style="font-size: 8px; color: #5d8a70;"></i> Estados de Reserva
            <span class="badge rounded-pill ms-1" style="font-size: 6.5px; background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; vertical-align: middle;">S5</span>
            <span class="badge rounded-pill ms-1" id="stChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">ST-v1-DBG</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

            @if(!$stGlobal)
                <span class="ctx-chip" title="Conteos filtrados por inmueble">
                    <i class="bi bi-funnel-fill"></i> {{ $stCtxName }}
                </span>
            @endif

            <span class="count-badge" title="{{ $stGlobal ? 'Reservas en todo el sistema' : 'Reservas de este inmueble' }}">
                <b>{{ $stTotalReservas }}</b> reserva(s) · <b>{{ $stItems->count() }}</b> estados
            </span>

            <input type="text" id="stSearch" class="xls-search" placeholder="Buscar estado…" autocomplete="off" style="width: 170px;">

            @if($stStore)
                <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#stModalCreate">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Estado
                </button>
            @endif
        </div>
    </div>

    <!-- TABLA · 100% Blade · con tfoot TOTAL -->
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Color</th>
                    <th class="text-center">{{ $stGlobal ? 'Reservas' : 'Reservas (este inmueble)' }}</th>
                    <th class="text-center" style="width: 110px;">·</th>
                </tr>
            </thead>
            <tbody id="stTbody">
                @forelse(($stItems ?? collect()) as $s)
                    <tr data-st-name="{{ strtolower($s['name']) }}">
                        <td class="strong">
                            <span class="st-dot" style="background: {{ $s['color'] }};"></span> {{ $s['name'] }}
                        </td>
                        <td class="sub">{{ $s['color'] }}</td>
                        <td class="text-center">{{ $s['cnt'] }}</td>
                        <td class="text-center">
                            @if($stUpdate)
                                <button type="button"
                                        class="btn-mini"
                                        data-bs-toggle="modal"
                                        data-bs-target="#stEditModal-{{ $s['id'] }}">Editar</button>
                            @endif
                            @if($stDestroy)
                                <button type="button"
                                        class="btn-mini"
                                        style="color:#c98a85;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#stDelModal-{{ $s['id'] }}">Del</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-flag me-1"></i> Sin estados definidos aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- ═══ TFOOT · fila TOTAL (intuitividad) ═══ --}}
            <tfoot>
                @if($stSinEstado > 0)
                <tr class="st-row-sin">
                    <td>
                        <i class="bi bi-question-circle me-1" style="font-size: 8px;"></i>
                        Reservas sin estado asignado
                    </td>
                    <td></td>
                    <td class="text-center">{{ $stSinEstado }}</td>
                    <td></td>
                </tr>
                @endif
                <tr>
                    <td class="st-total-label">
                        <i class="bi bi-calculator me-1" style="font-size: 8px;"></i>
                        TOTAL · {{ $stItems->count() }} estado(s)
                    </td>
                    <td></td>
                    <td class="text-center" style="color: var(--accent);">{{ $stTotalReservas }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · CREAR (AJAX)                                             --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if($stStore)
<div class="modal fade reservas-soft-modal" id="stModalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Nuevo Estado</h6>
            </div>
            <form id="stFormCreate" action="{{ $stStore }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" maxlength="100" required placeholder="Confirmada, Cancelada…">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Color (dot en tablas y calendario)</label>
                        <input type="color" class="form-control form-control-color form-control-sm w-100" name="color_hex" value="#a3cfb6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODALES POR ESTADO · EDITAR Y ELIMINAR (AJAX)                    --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@foreach(($stItems ?? collect()) as $s)

    @if($stUpdate)
    <div class="modal fade reservas-soft-modal" id="stEditModal-{{ $s['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Editar: {{ $s['name'] }}</h6>
                </div>
                <form class="st-form-edit" action="{{ str_replace('ID', $s['id'], $stUpdate) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="mb-2">
                            <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" maxlength="100" required value="{{ $s['name'] }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color form-control-sm w-100" name="color_hex" value="{{ $s['color'] }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($stDestroy)
    <div class="modal fade reservas-soft-modal" id="stDelModal-{{ $s['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-3 text-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                    <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar estado</h6>
                    <p class="mb-2" style="font-size: 8.5px; color: #94a3b8;">
                        Se eliminará <b style="color: #334155;">“{{ $s['name'] }}”</b> de todo el sistema.
                    </p>

                    @if($s['cntTotal'] > 0)
                        <div class="st-warn mb-2">
                            <i class="bi bi-lock-fill me-1"></i>
                            Bloqueado: tiene <b>{{ $s['cntTotal'] }}</b> reserva(s) asociada(s) en el sistema.
                        </div>
                        <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled
                                style="font-size: 11px; background: #fcfdfe; color: #d4dae2; border: 1px solid #f1f5f9; cursor: not-allowed;">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    @else
                        <form class="st-form-del" action="{{ str_replace('ID', $s['id'], $stDestroy) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm py-1 px-2 w-100"
                                    style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;">
                                <i class="bi bi-trash3"></i> Eliminar definitivamente
                            </button>
                        </form>
                    @endif
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

@endforeach

{{-- SCRIPT AJAX, MODALES Y BÚSQUEDA PROGRESIVA DINÁMICA + PANEL DE DEBUG (ST-v1-DBG) --}}
<script>
(function () {
    'use strict';

    // Evita doble binding de eventos cuando el fragmento se re-inyecta vía AJAX.
    if (window.__stPanelInit) return;
    window.__stPanelInit = true;

    const ST_FRAG_URL = '{{ route("rsv.admin.reservas.status.fragmento") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    /* ═══ 0) PANEL DE DEBUG LOCAL ST-v1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de esta vista: los nodos son HTML del fragmento (hermanos
       de #stRoot, sobreviven al refresco AJAX interno que solo intercambia
       el root) y el respaldo solo re-crea DENTRO de #stRoot.
       Nada se cuelga de document.body. */
    var DBG = (window.StDbg && window.StDbg.alive && window.StDbg.alive())
        ? window.StDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven como hermanos de #stRoot), se re-crean DENTRO del root.
                   Cero document.body (exclusividad total). */
                var rootDbg = document.getElementById('stRoot');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('stDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'stDbgToggle';
                    t.title = 'Debug Estados (ST-v1-DBG)';
                    t.innerHTML = '<span class="st-dbg-led" id="stDbgLed"></span>DBG<span class="st-dbg-badge" id="stDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('stDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'stDbgPanel';
                    p.innerHTML = '<div class="st-dbg-header" id="stDbgHeader">' +
                        '<span>DEBUG · Estados de Reserva · ST-v1-DBG</span>' +
                        '<span class="st-dbg-controls">' +
                        '<button type="button" class="st-dbg-btn" id="stDbgCopy">Copiar</button>' +
                        '<button type="button" class="st-dbg-btn" id="stDbgClear">Limpiar</button>' +
                        '<button type="button" class="st-dbg-btn" id="stDbgClose">✕</button>' +
                        '</span></div><div id="stDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* ST-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('stDbgToggle'), p = $('stDbgPanel');
                if (t) { t.style.bottom = '0px'; } // SIEMPRE pegada abajo
                if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; } // 16 tab + 4 aire
            }
            function hora() {
                var d = new Date();
                function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('stDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'st-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="st-dbg-time">[' + hora() + ']</span>' +
                        '<span class="st-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('stDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('stDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('stDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('stDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('stDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('stDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('stDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('stDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('stDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG estados ST-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('stDbgToggle'); }
            };
            window.StDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var stdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function unicarDbg() {
        /* ST-v1-DBG: el parcial puede renderizarse 2+ veces; el HTML de cada
           copia trae sus propios nodos DBG → se conservan los del primero y
           se eliminan los duplicados (un solo toggle/panel por página). */
        var ts = document.querySelectorAll('#stDbgToggle');
        for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
        var ps = document.querySelectorAll('#stDbgPanel');
        for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
    }
    unicarDbg();

    /* Botones del panel por DELEGACIÓN en document (el guard __stPanelInit
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__stDbgDocHandler) { document.removeEventListener('click', window.__stDbgDocHandler); }
    window.__stDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#stDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#stDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#stDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#stDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__stDbgDocHandler);
    if (window.__stDbgDblHandler) { document.removeEventListener('dblclick', window.__stDbgDblHandler); }
    window.__stDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#stDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__stDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($stDbg ?? null);
    stdbg('ST-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con la vista)', 'ok');
    stdbg('init · contexto=' + (SRVDBG ? SRVDBG.contexto : '?') + ' · estados=' + (SRVDBG ? SRVDBG.estados : '?') +
        ' · reservas=' + (SRVDBG ? SRVDBG.reservas : '?') + ' · sin estado=' + (SRVDBG ? SRVDBG.sin_estado : '?'), 'info');
    if (SRVDBG && SRVDBG.bloqueados > 0) {
        stdbg('guard de eliminar: ' + SRVDBG.bloqueados + ' estado(s) bloqueado(s) por reservas asociadas', 'warn');
    }
    if (SRVDBG && SRVDBG.estados === 0) {
        stdbg('dataset vacío · catálogo sin estados definidos todavía', 'warn');
    }
    if (SRVDBG && SRVDBG.alertas) {
        if (SRVDBG.alertas.success === 'sí') { stdbg('alerta success en sesión (flash de la última operación)', 'ok'); }
        if (SRVDBG.alertas.error === 'sí') { stdbg('alerta error en sesión', 'err'); }
        if (SRVDBG.alertas.validacion === 'sí') { stdbg('errores de validación visibles (old() conservado)', 'warn'); }
    }
    if (SRVDBG && SRVDBG.rutas) {
        var _rk = Object.keys(SRVDBG.rutas);
        for (var _ri = 0; _ri < _rk.length; _ri++) {
            if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                stdbg('ruta no definida: rsv.admin.reservas.status…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
            }
        }
        stdbg('Rutas · store=' + SRVDBG.rutas.store + ' · update=' + SRVDBG.rutas.update + ' · destroy=' + SRVDBG.rutas.destroy, 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            stdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    // Función para recargar SOLO este micro-fragmento vía AJAX
    window.reloadStFragment = function() {
        stdbg('refrescando fragmento · ' + ST_FRAG_URL, 'net');
        fetch(ST_FRAG_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newContent = temp.querySelector('#stRoot');
                if (newContent) {
                    document.getElementById('stRoot').replaceWith(newContent);
                    unicarDbg();
                    if (DBG && DBG.ensure) { DBG.ensure(); } // re-apila tras el swap
                    stdbg('fragmento refrescado · dataset releído del servidor', 'ok');
                } else {
                    stdbg('respuesta sin #stRoot · el fragmento no se reemplazó', 'warn');
                }
            })
            .catch(err => {
                console.error('Error recargando fragmento de estados:', err);
                stdbg('fallo al recargar el fragmento · ' + (err && err.message ? err.message : 'error de red'), 'err');
            });
    };

    // Interceptar clics en los botones de envío de cualquier formulario del módulo de estados
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('button[type="submit"], input[type="submit"]');
        if (!btn) return;

        const form = btn.closest('form');
        if (!form) return;

        const isModuleForm = form.id === 'stFormCreate' ||
                             form.classList.contains('st-form-edit') ||
                             form.classList.contains('st-form-del') ||
                             form.closest('#stRoot') !== null;

        if (!isModuleForm) return;

        e.preventDefault();
        e.stopPropagation();

        const url = form.action;
        const formData = new FormData(form);

        if (btn.name) {
            formData.append(btn.name, btn.value);
        }

        stdbg('submit AJAX · ' + url + ' · form=' + (form.id || form.className || 'inline'), 'debug');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            let data = {};
            try { data = await res.json(); } catch(e){}
            return { status: res.status, ok: res.ok, body: data };
        })
        .then(res => {
            if (!res.ok) {
                let msg = 'Ocurrió un error al procesar la solicitud.';
                if (res.body.errors) {
                    msg = Object.values(res.body.errors).flat().join('\n');
                } else if (res.body.message) {
                    msg = res.body.message;
                }
                stdbg('rechazo HTTP ' + res.status + ' · ' + String(msg).replace(/\s+/g, ' ').slice(0, 120), 'err');
                alert(msg);
                return;
            }

            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });

            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');

            stdbg('operación OK · modal(es) cerrados · refrescando fragmento', 'ok');
            window.reloadStFragment();
        })
        .catch(err => {
            console.error(err);
            stdbg('error de conexión con el servidor', 'err');
            alert('Error de conexión con el servidor.');
        });
    }, true);

    // BÚSQUEDA PROGRESIVA DINÁMICA (Delegación global de eventos 'input')
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'stSearch') {
            var stVal = e.target.value.toLowerCase().trim();
            var stTbody = document.getElementById('stTbody');
            if (!stTbody) return;

            var stRows = stTbody.querySelectorAll('tr[data-st-name]');
            var stVis = 0;
            stRows.forEach(function (tr) {
                var nameAttr = tr.getAttribute('data-st-name') || '';
                var show = !stVal || nameAttr.indexOf(stVal) !== -1;
                tr.style.display = show ? '' : 'none';
                if (show) { stVis++; }
            });
            stdbg('búsqueda · "' + stVal + '" · ' + stVis + ' de ' + stRows.length + ' estado(s) visible(s)', 'debug');
        }
    });
})();
</script>