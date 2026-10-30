{{-- ============================================================================
     RSV · ADMIN · CALENDARIO · BLOQUEOS · BLQ-v1-DBG
     Parcial: resources/views/rsv/admin/partials/calendario/bloqueos.blade.php

     · DISEÑO UNIFICADO: misma estética "reservas-soft" que
       partials/reservas/status.blade.php (variables CSS, tipografía,
       tabla, botones btn-mini/btn-green, count-badge, xls-search y
       modales Bootstrap idénticos al ejemplo).
     · AUTOABASTECIDA: si el controlador no pasa $bloqueos / $inmuebles,
       los consulta sola (modelos candidatos y, como último recurso, tablas
       reales detectadas con Schema::hasTable).
     · TABLA EN CLIENTE: se pinta desde #blqDataset (mismo patrón que
       status.blade.php), con búsqueda instantánea y refresco AJAX del
       fragmento tras guardar/eliminar.
     · MODALES BOOTSTRAP: mismo patrón de modales del ejemplo
       (modal-sm para eliminar, modal ancho para el formulario).
     · FILTRO SUPERIOR: si en la página ya hay un select de inmueble visible
       con valor, el modal se precarga con él.
     · CALENDARIO: Flatpickr con carga perezosa (CDN) y fallback a inputs
       date; pinta reservas y bloqueos ya ocupados; aviso de solape no
       bloqueante (permite overbooking intencional).
     · CALENDARIO GLOBAL: tras guardar/eliminar refresca window.rsvCalendar
       (refetchEvents) si existe en la página.

     Rutas que usa (todas por route(), nunca hardcodeadas):
       - rsv.reservas.fechas-ocupadas   GET  /rsv/reservas/fechas-ocupadas/{inmueble}
       - rsv.bloqueos-calendario.store  / update / destroy (resource)
       - rsv.admin.bloqueos.fragmento   GET  /rsv/bloqueos-fragmento

     · FIX (CAMBIO BLQ-v1-2): ParseError "syntax error, unexpected token ,"
       al compilar la vista. Causa: el texto de la directiva json de Blade
       estaba escrito LITERALMENTE dentro de dos comentarios (uno PHP dentro
       del bloque php del servidor y otro JS en el boot del panel). Blade compila las
       directivas a nivel de texto aunque estén dentro de comentarios, así
       que un "json pelado" genera un json_encode con una coma huérfana
       (sintaxis inválida). Ambos
       comentarios se reescribieron sin la arroba; el uso REAL con expresión
       (SRVDBG) se conserva intacto.
     · PANEL DE DEBUG (CAMBIO BLQ-v1-1 · diseño TIP-v1.3 aprobado): pestaña
       terminal "DBG" de 16px fija SIEMPRE en la esquina inferior-derecha
       (right:0 · bottom:0, radius 3px solo superior-izquierdo) con LED de
       estado (verde = ok · rojo pulsante + conteo = errores) y panel cajón
       de 380px alineado al mismo borde. La regla exacta de distancias está
       documentada como "SPEC DE DISTANCIAS" dentro del CSS, y el panel
       esquiva a los toggles hermanos de las otras vistas midiendo píxeles
       reales (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Exclusivo de esta vista: el toggle y el panel nacen y mueren con el
       fragmento (el refresco AJAX interno solo intercambia #bloqueos-panel,
       así que los nodos DBG sobreviven a guardar/eliminar). Telemetría que
       reporta: dataset cargado, ocupación por inmueble (fetch), solapes
       detectados, envíos AJAX (crear/editar/eliminar), refresco del
       fragmento y refetch del calendario global (window.rsvCalendar).
     ============================================================================ --}}

@php
    /* ── Datos: solo se autoconsultan si el controlador NO pasó las variables ── */
    $inmueblesFaltan = !isset($inmuebles) || $inmuebles === null;
    $bloqueosFaltan  = !isset($bloqueos)  || $bloqueos  === null;

    if ($inmueblesFaltan) {
        $inmuebles = collect();
        foreach ([
            '\App\Models\Rsv\BloqueoCalendario',
            '\App\Models\Rsv\CatalogoInmueble',
            '\App\Models\RsvCatalogoInmueble',
            '\App\Models\CatalogoInmueble',
            '\App\Models\RsvInmueble',
            '\App\Models\Inmueble',
        ] as $blqClase) {
            if (!class_exists($blqClase)) continue;
            try {
                try { $inmuebles = $blqClase::orderBy('name')->get(); }
                catch (\Throwable $blqE) { $inmuebles = $blqClase::orderBy('nombre')->get(); }
                break; // la consulta funcionó: modelo y tabla válidos
            } catch (\Throwable $blqE) { continue; }
        }
        if (count($inmuebles) === 0) {
            foreach (['rsv_catalogo_inmueble', 'rsv_catalogo_inmuebles', 'catalogo_inmuebles', 'rsv_inmuebles', 'inmuebles'] as $blqTabla) {
                if (\Illuminate\Support\Facades\Schema::hasTable($blqTabla)) {
                    $blqCols = \Illuminate\Support\Facades\Schema::getColumnListing($blqTabla);
                    $blqColNombre = in_array('name', $blqCols) ? 'name'
                                  : (in_array('nombre', $blqCols) ? 'nombre' : 'id');
                    $inmuebles = collect(\Illuminate\Support\Facades\DB::table($blqTabla)->orderBy($blqColNombre)->get());
                    break;
                }
            }
        }
    }

    if ($bloqueosFaltan) {
        $bloqueos = collect();
        foreach ([
            '\App\Models\RsvBloqueoCalendario',
            '\App\Models\BloqueoCalendario',
            '\App\Models\RsvBloqueo',
            '\App\BloqueoCalendario',
        ] as $blqClase) {
            if (!class_exists($blqClase)) continue;
            try {
                try { $bloqueos = $blqClase::with('inmueble')->orderBy('fecha_inicio')->get(); }
                catch (\Throwable $blqE) { $bloqueos = $blqClase::orderBy('id')->get(); }
                break;
            } catch (\Throwable $blqE) { continue; }
        }
        if (count($bloqueos) === 0) {
            foreach (['rsv_bloqueos_calendario','bloqueos_calendario','rsv_bloqueos','bloqueo_calendarios','bloqueos'] as $blqTabla) {
                if (\Illuminate\Support\Facades\Schema::hasTable($blqTabla)) {
                    $blqCols = \Illuminate\Support\Facades\Schema::getColumnListing($blqTabla);
                    $blqColFecha = in_array('fecha_inicio', $blqCols) ? 'fecha_inicio' : 'id';
                    $bloqueos = collect(\Illuminate\Support\Facades\DB::table($blqTabla)->orderBy($blqColFecha)->get());
                    break;
                }
            }
        }
    }

    // Mapa id → inmueble (sirve con modelos Eloquent y con filas de DB::table)
    $mapaInmuebles = collect($inmuebles)->keyBy('id');

    /* ── Rutas con route() (null si no existen: la acción se deshabilita sola) ── */
    $blqRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };
    $blqOcupadas  = $blqRuta(['rsv.reservas.fechas-ocupadas'], ':inmuebleId');
    $blqStore     = $blqRuta(['rsv.bloqueos-calendario.store']);
    $blqUpdate    = $blqRuta(['rsv.bloqueos-calendario.update'], ':bloqueoId');
    $blqDestroy   = $blqRuta(['rsv.bloqueos-calendario.destroy'], ':bloqueoId');
    $blqFragmento = $blqRuta(['rsv.admin.bloqueos.fragmento']);

    /* ── Dataset JSON para el render del lado del cliente (patrón status.blade.php) ── */
    $blqItems = collect($bloqueos)->values()->map(function ($blqB) use ($mapaInmuebles) {
        $blqIni = \Illuminate\Support\Carbon::parse($blqB->fecha_inicio);
        $blqFin = \Illuminate\Support\Carbon::parse($blqB->fecha_fin);
        $blqInm = $blqB->inmueble ?? $mapaInmuebles->get($blqB->id_rsv_catalogo_inmueble);
        return [
            'id'          => $blqB->id,
            'inmueble_id' => (string) $blqB->id_rsv_catalogo_inmueble,
            'inmueble'    => (string) ($blqInm->name ?? $blqInm->nombre ?? ('Inmueble #' . $blqB->id_rsv_catalogo_inmueble)),
            'inicio'      => $blqIni->format('Y-m-d'),
            'fin'         => $blqFin->format('Y-m-d'),
            'desde'       => $blqIni->format('d/m/Y'),
            'hasta'       => $blqFin->format('d/m/Y'),
            'dias'        => (int) $blqIni->diffInDays($blqFin) + 1,
            'motivo'      => (string) $blqB->motivo,
        ];
    });

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $blqJson = json_encode($blqItems->all(), $jhFlags); if (!is_string($blqJson)) { $blqJson = '[]'; }

    /* ── Colector del panel de debug (BLQ-v1-DBG) · via al panel vía la directiva json de Blade ── */
    $blqDbg = [
        'version'        => 'BLQ-v1-DBG',
        'vista'          => 'partials/calendario/bloqueos.blade.php',
        'render'         => now()->format('H:i:s'),
        'auto_inmuebles' => $inmueblesFaltan,
        'auto_bloqueos'  => $bloqueosFaltan,
        'inmuebles'      => count($inmuebles),
        'bloqueos'       => $blqItems->count(),
        'editable'       => (bool) ($blqStore || $blqUpdate),
        'rutas'          => [
            'ocupadas'  => $blqOcupadas  ?: 'NO definida',
            'store'     => $blqStore     ?: 'NO definida',
            'update'    => $blqUpdate    ?: 'NO definida',
            'destroy'   => $blqDestroy   ?: 'NO definida',
            'fragmento' => $blqFragmento ?: 'NO definida',
        ],
        'errores'        => [],
    ];
@endphp

{{-- BLQ-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano del panel: vive y muere con la vista · NO viaja en el refresco AJAX interno) --}}
<div id="blqDbgToggle" title="Debug Bloqueos (BLQ-v1-DBG)"><span class="blq-dbg-led" id="blqDbgLed"></span>DBG<span class="blq-dbg-badge" id="blqDbgBadge">0</span></div>
<div id="blqDbgPanel" role="dialog" aria-label="Panel de debug de Bloqueos">
    <div class="blq-dbg-header" id="blqDbgHeader">
        <span>DEBUG · Bloqueos (Calendario) · BLQ-v1-DBG</span>
        <span class="blq-dbg-controls">
            <button type="button" class="blq-dbg-btn" id="blqDbgCopy">Copiar</button>
            <button type="button" class="blq-dbg-btn" id="blqDbgClear">Limpiar</button>
            <button type="button" class="blq-dbg-btn" id="blqDbgClose">✕</button>
        </span>
    </div>
    <div id="blqDbgBody"></div>
</div>

<div class="reservas-soft p-3 bg-white rounded-3 border" id="bloqueos-panel" style="border-color: #f1f5f9 !important;">

<style>
    /* ── Núcleo reservas-soft (idéntico al ejemplo status.blade.php) ── */
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }

    /* ── Extras blq- (etiquetas, calendario, avisos) con la misma paleta ── */
    .reservas-soft .blq-etiqueta { display: block; font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-3); margin-bottom: 3px; }
    .reservas-soft .blq-obligatorio { color: #c98a85; }
    .reservas-soft .blq-ayuda { font-size: 7.5px; color: var(--ink-4); margin-top: 3px; }
    .reservas-soft .blq-fila { display: grid; grid-template-columns: 1fr; gap: 12px; }
    @media (min-width: 768px) { .reservas-soft .blq-fila { grid-template-columns: 1fr 1fr; } }
    .reservas-soft .blq-calendario { border: 1px solid var(--grid-2); border-radius: 5px; padding: 6px; background: #fff; }
    .reservas-soft .blq-calendario .flatpickr-calendar { margin: 0 auto; box-shadow: none; font-family: inherit; }
    .reservas-soft .blq-calendario .flatpickr-day.selected,
    .reservas-soft .blq-calendario .flatpickr-day.selected:hover { background: var(--accent); border-color: var(--accent); }
    .reservas-soft .blq-calendario .flatpickr-day.inRange { background: var(--accent-bg); border-color: var(--accent-bg); box-shadow: none; }
    .reservas-soft .flatpickr-day.blq-ocp-reserva { background: #fdf5f4 !important; border-color: #f0dcd8 !important; color: #b36b64 !important; }
    .reservas-soft .flatpickr-day.blq-ocp-bloqueo { background: #fbf8ee !important; border-color: #eee5cd !important; color: #8f7a45 !important; }
    .reservas-soft .blq-rango-texto { font-size: 8.5px; color: var(--ink-3); min-height: 12px; margin-bottom: 4px; }
    .reservas-soft .blq-leyenda { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; font-size: 8px; color: var(--ink-3); margin-top: 6px; }
    .reservas-soft .blq-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; margin-right: 4px; }
    .reservas-soft .blq-dot-reserva { background: #fdf5f4; border-color: #f0dcd8; }
    .reservas-soft .blq-dot-bloqueo { background: #fbf8ee; border-color: #eee5cd; }
    .reservas-soft .blq-spin { display: inline-block; width: 10px; height: 10px; border: 2px solid var(--accent-bd); border-top-color: var(--accent); border-radius: 50%; animation: blq-girar .7s linear infinite; vertical-align: -2px; margin-right: 4px; }
    @keyframes blq-girar { to { transform: rotate(360deg); } }
    .reservas-soft .blq-banner-errores { background: #fdf5f4; border: 1px solid #f0dcd8; color: #b36b64; font-size: 8.5px; padding: 6px 9px; border-radius: 3px; margin-bottom: 8px; }
    .reservas-soft .blq-banner-errores ul { margin: 3px 0 0; padding-left: 14px; }
    .reservas-soft .blq-aviso-solape { background: #fbf8ee; border: 1px solid #eee5cd; color: #8f7a45; font-size: 8px; padding: 5px 8px; border-radius: 3px; margin-top: 8px; }
    .reservas-soft .blq-invalid { border-color: #e7b9b4 !important; box-shadow: 0 0 0 3px rgba(201,138,133,.10); }

    /* Toast (vive en body, fuera del panel) */
    .blq-toast { position: fixed; right: 1rem; bottom: 1rem; z-index: 5000; min-width: 240px; max-width: 320px; padding: 8px 12px; border-radius: 5px; font-size: 11px; font-weight: 500; color: #fff; background: #3d7a5c; box-shadow: 0 10px 30px rgba(0,0,0,.18); transition: opacity .3s ease, transform .3s ease; }
    .blq-toast.blq-error { background: #c98a85; }
    .blq-toast-out { opacity: 0; transform: translateY(.4rem); }

    /* ═════════ SPEC DE DISTANCIAS · BLQ-v1-DBG (tab terminal TIP-v1.3) ═════════
       TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la esquina
                   inferior-derecha (nunca sube: con 16px de alto cabe bajo
                   cualquier otro toggle sin tocarlo)
                 · altura fija 16px · padding lateral 8px
                 · radius 3px solo sup-izq · borde 1px arriba/izq
                 · monospace 8px · LED 5px (verde ok / rojo pulsante = error)
       PANEL     · right:0px · bottom = max(20, vecino + 4px) medido en px
                   reales con getBoundingClientRect (los toggles ocultos 0×0
                   no cuentan) · (20 = 16 de tab + 4 de aire mínimo)
                 · ancho 380px · radius 6px 0 0 0 (cajón pegado al borde) */
    #blqDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    #blqDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
    .blq-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .blq-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: blqDbgBlink 1s steps(2,start) infinite; }
    @keyframes blqDbgBlink { 50% { opacity: .35; } }
    .blq-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
    #blqDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
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

    {{-- ─────────────────────────── CABECERA + CONTROLES ─────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-calendar-x me-1" style="font-size: 8px; color: #5d8a70;"></i> Bloqueos de Calendario
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b id="blqCount">{{ $blqItems->count() }}</b> bloqueos</span>
            <input type="text" id="blqSearch" class="xls-search" placeholder="Buscar bloqueo…" autocomplete="off" style="width: 170px;">
            @if($blqStore)
                <button type="button" class="btn-green" id="blqBtnNuevo">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Bloqueo
                </button>
            @endif
        </div>
    </div>

    {{-- ─────────────────────────── TABLA ─────────────────────────── --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 640px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Inmueble</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th class="text-center">Días</th>
                    <th>Motivo</th>
                    <th class="text-center" style="width: 96px;">·</th>
                </tr>
            </thead>
            <tbody id="blqTbody">
                <tr><td colspan="7" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr>
            </tbody>
        </table>
    </div>

    {{-- ─────────────────────────── MODAL FORM (Bootstrap, estilo reservas-soft) ─────────────────────────── --}}
    <div class="modal fade" id="modalBloqueo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 760px;">
            <div class="modal-content reservas-soft" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
                <form id="formBloqueo" novalidate>
                    @csrf
                    <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        <h6 class="modal-title mb-0 fw-bold" id="blqModalTitulo" style="font-size: 0.95rem;">Nuevo Bloqueo</h6>
                    </div>
                    <div class="modal-body p-3">
                        <div id="blqBannerErrores" class="blq-banner-errores" style="display:none;"></div>

                        <div class="blq-fila">
                            {{-- Columna izquierda: datos --}}
                            <div>
                                <label class="blq-etiqueta" for="blqInmueble">Inmueble <span class="blq-obligatorio">*</span></label>
                                <select id="blqInmueble" name="id_rsv_catalogo_inmueble" class="form-select form-select-sm">
                                    <option value="">— Selecciona un inmueble —</option>
                                    @foreach($inmuebles as $inmueble)
                                        <option value="{{ $inmueble->id }}">{{ $inmueble->name ?? $inmueble->nombre ?? 'Inmueble #'.$inmueble->id }}</option>
                                    @endforeach
                                </select>
                                <div class="blq-ayuda">Si ya hay un inmueble filtrado arriba, se precarga automáticamente.</div>

                                <label class="blq-etiqueta" for="blqMotivo" style="margin-top: 14px;">Motivo <span class="blq-obligatorio">*</span></label>
                                <textarea id="blqMotivo" name="motivo" rows="5" maxlength="500" class="form-control form-control-sm"
                                          placeholder="Ej.: Mantenimiento de aire acondicionado, overbooking, reforma…"></textarea>
                                <div class="blq-ayuda">Entre 5 y 500 caracteres.</div>

                                <div id="blqAvisoSolape" class="blq-aviso-solape" style="display:none;">
                                    El rango seleccionado se solapa con fechas ya ocupadas.
                                    Puedes continuar (por ejemplo, para un overbooking), pero revisa que sea intencional.
                                </div>
                            </div>

                            {{-- Columna derecha: calendario --}}
                            <div>
                                <label class="blq-etiqueta">Fechas del bloqueo <span class="blq-obligatorio">*</span></label>
                                <div id="blqRangoTexto" class="blq-rango-texto">Sin fechas seleccionadas</div>

                                <input type="hidden" name="fecha_inicio" id="blqFechaInicio">
                                <input type="hidden" name="fecha_fin" id="blqFechaFin">
                                <input type="text" id="blqAnclaCal" class="d-none">

                                <div id="blqCalendario" class="blq-calendario"></div>

                                <div id="blqCargando" class="blq-ayuda" style="display:none;">
                                    <span class="blq-spin"></span>Cargando ocupación…
                                </div>
                                <div id="blqInfoOcupacion" class="blq-ayuda"></div>

                                <div class="blq-leyenda">
                                    <span><i class="blq-dot blq-dot-reserva"></i> Reserva</span>
                                    <span><i class="blq-dot blq-dot-bloqueo"></i> Otro bloqueo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" id="blqBtnGuardar" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Crear bloqueo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────── MODAL ELIMINAR ─────────────────────────── --}}
    <div class="modal fade" id="blqModalDel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content reservas-soft" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
                <div class="modal-body p-3 text-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                    <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar bloqueo</h6>
                    <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                        Se eliminará <b id="blqDelName" style="color: #334155;">—</b>.<br>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="blqDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="blqDataset">{!! $blqJson !!}</script>

    <script>
    (function () {
        'use strict';

        // Evita doble binding de eventos cuando el fragmento se re-inyecta vía AJAX.
        if (window.__blqPanelInit) return;
        window.__blqPanelInit = true;

        var CSRF = '{{ csrf_token() }}';
        var URLS = {
            ocupadas:  '{{ $blqOcupadas ?? "" }}',
            store:     '{{ $blqStore ?? "" }}',
            update:    '{{ $blqUpdate ?? "" }}',
            destroy:   '{{ $blqDestroy ?? "" }}',
            fragmento: '{{ $blqFragmento ?? "" }}'
        };
        var EDITABLE = !!(URLS.store || URLS.update);

        var modo = 'crear';            // 'crear' | 'editar'
        var idEditando = null;
        var pendienteEliminar = null;  // registro esperando confirmación en el modal
        var ocupadas = [];             // [{inicio:'Y-m-d', fin:'Y-m-d', tipo}] del inmueble seleccionado
        var q = '';                    // texto de búsqueda de la tabla
        var fpCalendario = null;       // instancia Flatpickr viva

        /* ═══ 0) PANEL DE DEBUG LOCAL BLQ-v1-DBG (diseño TIP-v1.3 aprobado) ═══
           IIFE propia con $ por id: no toca el $ (querySelector) del panel.
           Exclusiva de esta vista: los nodos son HTML del fragmento (hermanos
           de #bloqueos-panel) y el respaldo solo re-crea DENTRO del panel.
           Nada se cuelga de document.body. */
        var DBG = (window.BlqDbg && window.BlqDbg.alive && window.BlqDbg.alive())
            ? window.BlqDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarNodos() {
                    /* Respaldo: si un swap externo borrara los nodos (normalmente
                       viven como hermanos de #bloqueos-panel y sobreviven al
                       refresco AJAX interno), se re-crean DENTRO del panel.
                       Cero document.body (exclusividad total). */
                    var rootDbg = document.getElementById('bloqueos-panel');
                    if (!rootDbg) { return; } // sin vista montada → cero nodos
                    if (!$('blqDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'blqDbgToggle';
                        t.title = 'Debug Bloqueos (BLQ-v1-DBG)';
                        t.innerHTML = '<span class="blq-dbg-led" id="blqDbgLed"></span>DBG<span class="blq-dbg-badge" id="blqDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('blqDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'blqDbgPanel';
                        p.innerHTML = '<div class="blq-dbg-header" id="blqDbgHeader">' +
                            '<span>DEBUG · Bloqueos (Calendario) · BLQ-v1-DBG</span>' +
                            '<span class="blq-dbg-controls">' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgCopy">Copiar</button>' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgClear">Limpiar</button>' +
                            '<button type="button" class="blq-dbg-btn" id="blqDbgClose">✕</button>' +
                            '</span></div><div id="blqDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* BLQ-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
                   apila: con 16px de alto cabe bajo cualquier otro toggle sin
                   tocarlo). Solo el PANEL esquiva a los vecinos de las otras
                   vistas: bottom = max(20, vecino + 4) en píxeles reales. */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle']; // serie completa de hermanos
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                        var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('blqDbgToggle'), p = $('blqDbgPanel');
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
                        var txt = '=== DEBUG bloqueos BLQ-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                    alive: function () { return !!$('blqDbgToggle'); }
                };
                window.BlqDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
        var blqdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

        /* Botones del panel por DELEGACIÓN en document (el guard __blqPanelInit
           garantiza que se registran UNA sola vez; sobreviven a los swaps) */
        if (window.__blqDbgDocHandler) { document.removeEventListener('click', window.__blqDbgDocHandler); }
        window.__blqDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#blqDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#blqDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#blqDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#blqDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        };
        document.addEventListener('click', window.__blqDbgDocHandler);
        if (window.__blqDbgDblHandler) { document.removeEventListener('dblclick', window.__blqDbgDblHandler); }
        window.__blqDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#blqDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__blqDbgDblHandler);

        /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
           toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
        document.addEventListener('shown.bs.tab', function () {
            if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
        });

        /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
        var SRVDBG = @json($blqDbg);
        blqdbg('BLQ-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con la vista)', 'ok');
        blqdbg('init · dataset=' + (SRVDBG ? SRVDBG.bloqueos : '?') + ' bloqueo(s) · inmuebles=' + (SRVDBG ? SRVDBG.inmuebles : '?') +
            ' · editable=' + EDITABLE + ' · autoconsulta=' +
            (SRVDBG ? (((SRVDBG.auto_inmuebles || SRVDBG.auto_bloqueos) ? 'sí (el controlador no pasó variables)' : 'no (todo vino del controlador)')) : '?'), 'info');
        if (SRVDBG && SRVDBG.rutas) {
            var _rk = Object.keys(SRVDBG.rutas);
            for (var _ri = 0; _ri < _rk.length; _ri++) {
                if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                    blqdbg('ruta no definida: rsv…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
                }
            }
            blqdbg('Rutas · store=' + SRVDBG.rutas.store + ' · update=' + SRVDBG.rutas.update + ' · destroy=' + SRVDBG.rutas.destroy, 'debug');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                blqdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
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

        /* ── Dataset: se relee en cada render (tras refrescar el fragmento llega data fresca) ── */
        function leerDataset() {
            try { return JSON.parse((document.getElementById('blqDataset') || {}).textContent || '[]') || []; }
            catch (e) { return []; }
        }

        /* ── Fechas ──────────────────────────────────────────────────────────── */
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

        /* ── Carga perezosa de Flatpickr (por si el layout no lo trae) ───────── */
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

        /* ── Modales Bootstrap (mismo patrón que status.blade.php) ───────────── */
        function modalInst(id) {
            try { return bootstrap.Modal.getOrCreateInstance(document.getElementById(id)); }
            catch (e) { return null; }
        }

        /* ── Mini-calendario ─────────────────────────────────────────────────── */
        function asegurarCalendario() {
            var cont = $('#blqCalendario');
            if (!cont || !window.flatpickr) return;
            if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) return;
            if (fpCalendario) { try { fpCalendario.destroy(); } catch (e) {} fpCalendario = null; }
            cont.innerHTML = ''; // limpia posible fallback previo

            fpCalendario = flatpickr('#blqAnclaCal', {
                mode: 'range',
                inline: true,
                appendTo: cont,
                dateFormat: 'Y-m-d',
                minDate: 'today',
                showMonths: 1,
                onChange: function (fechas) {
                    $('#blqFechaInicio').value = fechas[0] ? aYmd(fechas[0]) : '';

                    // Si no hay segunda fecha, copiamos la primera
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

        // Fallback: si Flatpickr no pudo cargarse, dos inputs de fecha nativos.
        function calendarioFallback() {
            var cont = $('#blqCalendario');
            if (!cont || document.getElementById('blqFallbackInicio')) return;
            cont.innerHTML =
                '<div class="blq-aviso-solape" style="margin:0 0 6px;">No se pudo cargar el calendario visual. Selecciona las fechas manualmente:</div>' +
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
            var __yaHabia = !!window.flatpickr; // BLQ-v1-DBG
            try { await asegurarFlatpickr(); }
            catch (e) { blqdbg('Flatpickr no disponible (' + (e && e.message ? e.message : '?') + ') · uso inputs date nativos', 'warn'); console.warn('Flatpickr no disponible:', e); }
            asegurarCalendario();
            if (fpCalendario) { blqdbg('mini-calendario listo' + (__yaHabia ? ' (Flatpickr ya cargado)' : ' (CDN carga perezosa)'), 'debug'); }
            if (!fpCalendario) { calendarioFallback(); }
        }

        function repintarCalendario() {
            if (fpCalendario) { try { fpCalendario.redraw(); } catch (e) {} }
        }

        /* ── Filtro superior: reutiliza el inmueble ya elegido en la página ──── */
        function inmuebleDesdeFiltroSuperior() {
            var selects = document.querySelectorAll('select');
            for (var i = 0; i < selects.length; i++) {
                var s = selects[i];
                if (s.closest('#bloqueos-panel')) continue;             // los nuestros no cuentan
                if (s.closest('.modal')) continue;                      // ni modales ajenos
                if (s.offsetParent === null) continue;                  // solo visibles
                var clave = ((s.id || '') + ' ' + (s.name || '') + ' ' + (s.className || '')).toLowerCase();
                if (clave.indexOf('inmueble') === -1) continue;
                if (!s.value) continue;
                return s.value;
            }
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

        /* ── Ocupación del inmueble (rsv.reservas.fechas-ocupadas) ───────────── */
        async function cargarOcupadas(inmuebleId) {
            var info = $('#blqInfoOcupacion');
            var spinner = $('#blqCargando');
            var __t0 = Date.now(); // BLQ-v1-DBG
            blqdbg('ocupación · fetch inmueble=' + (inmuebleId || '—') + (URLS.ocupadas ? '' : ' · ruta NO definida'), 'net');
            ocupadas = [];
            repintarCalendario();
            if (info) info.textContent = '';
            if (!inmuebleId) return;

            if (spinner) spinner.style.display = 'block';
            try {
                var url = URLS.ocupadas.replace(':inmuebleId', encodeURIComponent(inmuebleId));
                var res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                var data = await res.json().catch(function () { return {}; });
                if (!res.ok || data.ok === false) {
                    throw new Error(data.message || 'No se pudo consultar la ocupación del inmueble.');
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
                info.textContent = nombre + ': ' + ocupadas.length + ' rango(s) ocupado(s) · ' +
                                   nRes + ' por reservas · ' + nBlq + ' por bloqueos';
                blqdbg('ocupación · ' + nombre + ' · ' + ocupadas.length + ' rango(s) · ' + nRes + ' reserva(s) · ' + nBlq + ' bloqueo(s) · ' + (Date.now() - __t0) + 'ms', 'ok');
            } catch (e) {
                info.textContent = 'Aviso: ' + e.message;
                blqdbg('ocupación falló · ' + e.message, 'err');
            } finally {
                if (spinner) spinner.style.display = 'none';
                repintarCalendario();
                revisarSolape();
            }
        }

        /* ── Solape (aviso no bloqueante) ────────────────────────────────────── */
        function revisarSolape() {
            var aviso = $('#blqAvisoSolape');
            if (!aviso) return;
            var ini = $('#blqFechaInicio').value;
            var fin = $('#blqFechaFin').value;
            if (!ini || !fin || !ocupadas.length) { aviso.style.display = 'none'; return; }
            var choca = ocupadas.some(function (o) { return ini <= o.fin && fin >= o.inicio; });
            if (choca && aviso.style.display !== 'block') { // BLQ-v1-DBG: solo en la transición (no spamea)
                blqdbg('solape detectado · ' + ini + ' → ' + fin + ' · aviso no bloqueante (overbooking permitido)', 'warn');
            }
            aviso.style.display = choca ? 'block' : 'none';
        }

        /* ── Errores de validación (422 del Form Request) ────────────────────── */
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

        /* ── Toast ───────────────────────────────────────────────────────────── */
        function mostrarToast(mensaje, tipo) {
            var t = document.createElement('div');
            t.className = 'blq-toast' + (tipo === 'danger' ? ' blq-error' : '');
            t.textContent = mensaje;
            document.body.appendChild(t);
            setTimeout(function () { t.classList.add('blq-toast-out'); }, 3200);
            setTimeout(function () { t.remove(); }, 3600);
        }

        /* ── Refresco AJAX del panel ─────────────────────────────────────────── */
        async function refrescarPanel() {
            var actual = $('#bloqueos-panel');
            if (!actual || !URLS.fragmento) { blqdbg('refresco · sin fragmento → recarga nativa', 'warn'); location.reload(); return; }
            var __t0 = Date.now(); // BLQ-v1-DBG
            var res = await fetch(URLS.fragmento, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            });
            if (!res.ok) throw new Error('Fragmento no disponible');
            var html = await res.text();
            var contenedor = document.createElement('div');
            contenedor.innerHTML = html;
            var nuevo = contenedor.querySelector('#bloqueos-panel');
            if (!nuevo) throw new Error('Fragmento inválido');
            actual.replaceWith(nuevo);
            blqdbg('fragmento refrescado en ' + (Date.now() - __t0) + 'ms · dataset=' + leerDataset().length + ' bloqueo(s)', 'ok');
            render(); // repinta la tabla con el dataset fresco del fragmento
        }

        function exitoYRefrescar(mensaje) {
            mostrarToast(mensaje);
            blqdbg(mensaje, 'ok');
            // Actualiza el calendario global de la página si existe
            blqdbg('calendario global · window.rsvCalendar ' + (window.rsvCalendar ? 'presente → refetchEvents()' : 'ausente en esta página'), 'debug');
            if (window.rsvCalendar) { try { window.rsvCalendar.refetchEvents(); } catch (e) {} }
            setTimeout(function () {
                refrescarPanel().catch(function (e2) { blqdbg('refresco falló · ' + (e2 && e2.message ? e2.message : '?') + ' · fallback recarga', 'err'); location.reload(); });
            }, 200);
        }

        /* ── Tabla: render del lado del cliente (patrón status.blade.php) ────── */
        function render() {
            var tbody = document.getElementById('blqTbody');
            if (!tbody) return;
            var DS = leerDataset();
            var total = document.getElementById('blqCount');
            if (total) total.textContent = DS.length;

            var t = q.toLowerCase().trim();
            var res = DS.filter(function (r) {
                if (!t) return true;
                var paja = (r.inmueble + ' ' + r.motivo + ' ' + r.desde + ' ' + r.hasta + ' #' + r.id).toLowerCase();
                return paja.indexOf(t) !== -1;
            });

            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="7" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
                  + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados.' : '<i class="bi bi-calendar-x me-1"></i> Sin bloqueos registrados todavía.')
                  + '</td></tr>';
            } else {
                res.forEach(function (r) {
                    var mot = String(r.motivo || '');
                    var corto = mot.length > 60 ? mot.slice(0, 60) + '…' : mot;
                    h += '<tr>'
                       + '<td style="color:var(--ink-4);font-size:7.5px;">' + esc(r.id) + '</td>'
                       + '<td class="strong">' + esc(r.inmueble) + '</td>'
                       + '<td>' + esc(r.desde) + '</td>'
                       + '<td>' + esc(r.hasta) + '</td>'
                       + '<td class="text-center"><span class="count-badge"><b>' + esc(r.dias) + '</b> d</span></td>'
                       + '<td title="' + esc(mot) + '" style="max-width:240px;overflow:hidden;text-overflow:ellipsis;">' + esc(corto) + '</td>'
                       + '<td class="text-center" style="white-space:nowrap;">'
                       + (EDITABLE ? '<button type="button" class="btn-mini blq-edit" data-id="' + esc(r.id) + '">Editar</button> ' : '')
                       + (URLS.destroy ? '<button type="button" class="btn-mini blq-del" data-id="' + esc(r.id) + '" style="color:#c98a85;">Del</button>' : '')
                       + '</td>'
                       + '</tr>';
                });
            }
            tbody.innerHTML = h;
        }

        /* ── Limpieza del formulario ─────────────────────────────────────────── */
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

        /* ── Abrir formulario (crear con r = null · editar con r = registro) ─── */
        async function abrirFormulario(r) {
            limpiarFormulario();
            var titulo = $('#blqModalTitulo');
            var btn = $('#blqBtnGuardar');
            var sel = $('#blqInmueble');

            if (r) {
                modo = 'editar';
                idEditando = r.id;
                blqdbg('modal · modo editar · bloqueo #' + r.id + ' · ' + (r.inmueble || '?') + ' · ' + (r.desde || '?') + ' → ' + (r.hasta || '?'), 'info');
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

                // Precarga desde el filtro superior (si existe y tiene valor)
                var pre = inmuebleDesdeFiltroSuperior();
                if (pre && sel) { asegurarOpcionInmueble(pre); sel.value = pre; }
                blqdbg('modal · modo crear' + (pre ? ' · inmueble precargado del filtro superior (#' + pre + ')' : ''), 'info');
            }

            var m = modalInst('modalBloqueo');
            if (m) m.show();

            await abrirCalendarioListo();   // el calendario SIEMPRE se dibuja al abrir

            if (r && r.inicio && r.fin) {
                $('#blqFechaInicio').value = r.inicio;
                $('#blqFechaFin').value = r.fin;
                $('#blqRangoTexto').textContent = textoRango(r.inicio, r.fin);
                if (fpCalendario) {
                    try { fpCalendario.set('minDate', null); } catch (err) {} // permite editar bloqueos ya iniciados
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
            } else if (!r && sel) {
                sel.focus();
            }
        }

        /* ── Envío genérico (patrón status.blade.php · method spoofing _method) ─ */
        function enviar(url, method, fd) {
            if (!url) { blqdbg('AJAX abortado · ruta no disponible para esta acción', 'err'); alert('Ruta no disponible para esta acción.'); return Promise.resolve(false); }
            fd.append('_token', csrf());
            if (method !== 'POST') fd.append('_method', method);
            blqdbg('AJAX · ' + method + ' ' + url + (method !== 'POST' ? ' (spoofing _method)' : ''), 'net');
            return fetch(url, {
                method: 'POST', // spoofing vía _method
                body: fd,
                headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(function (r) { return r.json().catch(function () { return { ok: r.ok, success: r.ok }; }); })
                .then(function (j) {
                    if (j && j.errors) {
                        var _n = Object.keys(j.errors).length;
                        blqdbg('AJAX 422 · validación falló · ' + _n + ' campo(s) con error', 'err');
                        pintarErrores(j); return false;
                    }
                    if (j && (j.ok === false || j.success === false)) {
                        blqdbg('AJAX falló · ' + (j.message || 'sin mensaje del servidor'), 'err');
                        alert(j.message || 'No se pudo completar la operación.');
                        return false;
                    }
                    blqdbg('AJAX OK', 'ok');
                    return true;
                })
                .catch(function () { blqdbg('AJAX error de conexión', 'err'); alert('Error de conexión.'); return false; });
        }

        /* ── Init: eventos delegados en document (sobreviven al refresco AJAX) ── */
        function init() {
            render();
            blqdbg('tabla renderizada · ' + leerDataset().length + ' bloqueo(s) en dataset · búsqueda activa #blqSearch', 'debug');

            document.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.closest) return;

                // Nuevo bloqueo
                if (t.closest('#blqBtnNuevo')) { abrirFormulario(null); return; }

                // Editar (fila de la tabla)
                var bE = t.closest('.blq-edit');
                if (bE && EDITABLE) {
                    var r = leerDataset().find(function (x) { return String(x.id) === bE.getAttribute('data-id'); });
                    if (r) abrirFormulario(r);
                    return;
                }

                // Eliminar: abre modal de confirmación (patrón status.blade.php)
                var bD = t.closest('.blq-del');
                if (bD && URLS.destroy) {
                    var r2 = leerDataset().find(function (x) { return String(x.id) === bD.getAttribute('data-id'); });
                    if (!r2) return;
                    pendienteEliminar = r2;
                    blqdbg('eliminar · confirmación para #' + r2.id + ' (' + r2.inmueble + ' · ' + r2.desde + ' → ' + r2.hasta + ')', 'info');
                    var nom = document.getElementById('blqDelName');
                    if (nom) nom.textContent = r2.inmueble + ' · ' + r2.desde + ' → ' + r2.hasta;
                    var mDel = modalInst('blqModalDel');
                    if (mDel) mDel.show();
                    return;
                }

                // Confirmación de eliminación
                var bGo = t.closest('#blqDelGo');
                if (bGo && pendienteEliminar) {
                    var objetivo = pendienteEliminar;
                    pendienteEliminar = null;
                    bGo.disabled = true;
                    blqdbg('eliminar · bloqueo #' + objetivo.id + ' (' + objetivo.inmueble + ') · confirmado', 'net');
                    enviar(URLS.destroy.replace(':bloqueoId', encodeURIComponent(objetivo.id)), 'DELETE', new FormData())
                        .then(function (ok) {
                            bGo.disabled = false;
                            var mDel2 = modalInst('blqModalDel');
                            if (mDel2) mDel2.hide();
                            if (ok) exitoYRefrescar('Bloqueo eliminado correctamente.');
                        });
                    return;
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.id === 'blqSearch') { q = e.target.value; render(); }
            });

            document.addEventListener('change', function (e) {
                if (e.target && e.target.id === 'blqInmueble') {
                    cargarOcupadas(e.target.value); // consulta ocupación al cambiar inmueble
                }
            });

            document.addEventListener('submit', function (e) {
                if (!e.target || e.target.id !== 'formBloqueo') return;
                e.preventDefault();
                limpiarErrores();

                var fd = new FormData(e.target);
                var esEditar = (modo === 'editar' && idEditando);
                blqdbg('submit · modo=' + (esEditar ? 'editar #' + idEditando : 'crear') + ' · inmueble=' + (fd.get('id_rsv_catalogo_inmueble') || '—') + ' · fechas=' + (fd.get('fecha_inicio') || '—') + ' → ' + (fd.get('fecha_fin') || '—'), 'net');

                // Validación mínima en cliente (el Form Request valida igual en el backend)
                if (!fd.get('id_rsv_catalogo_inmueble')) {
                    blqdbg('validación cliente · falta inmueble', 'err');
                    pintarErrores({ errors: { id_rsv_catalogo_inmueble: ['Selecciona un inmueble.'] } });
                    return;
                }
                if (!fd.get('fecha_inicio') || !fd.get('fecha_fin')) {
                    blqdbg('validación cliente · faltan fechas del calendario', 'err');
                    pintarErrores({ errors: { fecha_inicio: ['Selecciona la fecha de inicio y fin en el calendario.'] } });
                    return;
                }

                var url = esEditar
                    ? URLS.update.replace(':bloqueoId', encodeURIComponent(idEditando))
                    : URLS.store;

                var btn = $('#blqBtnGuardar');
                var textoOriginal = btn ? btn.innerHTML : '';
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="blq-spin" style="border-color:#5a5a5a;border-top-color:#fff;"></span>Guardando…'; }

                enviar(url, esEditar ? 'PUT' : 'POST', fd).then(function (ok) {
                    if (btn) { btn.disabled = false; btn.innerHTML = textoOriginal; }
                    if (!ok) return;
                    var m = modalInst('modalBloqueo');
                    if (m) m.hide();
                    exitoYRefrescar(esEditar ? 'Bloqueo actualizado correctamente.' : 'Bloqueo guardado correctamente.');
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


