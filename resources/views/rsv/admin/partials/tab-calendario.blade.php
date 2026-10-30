{{-- resources/views/rsv/admin/partials/tab-calendario.blade.php --}}
{{-- CAL-v3-DBG · CAMBIO CAL-v3-1 · Panel de debug con el estilo "terminal" aprobado (mismo de TIP-v1.3 / INM-v3): --}}
{{--    TAB DBG textual de 16px de alto (monospace 8px + LED de estado verde/rojo pulsante + badge numérico),     --}}
{{--    fundida SIEMPRE en la esquina inferior-derecha (right:0 · bottom:0 · nunca se apila hacia arriba) y el    --}}
{{--    panel abre como CAJÓN pegado al mismo borde (radius solo sup-izq, bordes solo arriba/izquierda). El       --}}
{{--    apilado con los demás toggles de la página se mide en px REALES (getBoundingClientRect · ocultos 0×0      --}}
{{--    descartados) y la regla exacta vive como "SPEC DE DISTANCIAS" dentro del CSS. El panel es LOCAL de esta   --}}
{{--    vista: registra SOLO operaciones del calendario (fetches, filtro, tabs, export global, modales) y NO      --}}
{{--    captura errores ajenos. El contexto del servidor viaja como JSON ($calDbg).                               --}}
{{-- v2 · FIX GLOBAL: lista de inmuebles corregida ($listaInmuebles),                                            --}}
{{-- updateSize() para AMBOS tabs (#global-calendario y #calendario),                                            --}}
{{-- contexto preseleccionado en individual. Resto idéntico (estaba super).                                      --}}

@php
    // ── CAL-v3-DBG · colector de contexto del servidor (autónomo y solo lectura:
    // re-deriva el mismo contexto que el bloque de #calRoot, sin tocar esa lógica) ──
    $__calDbgInmId = null;
    if (isset($inmueble) && $inmueble) {
        $__calDbgInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $__calDbgInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $__calDbgInmId = request('inmueble_id');
    }
    $calDbg = [
        'marca'      => 'CAL-v3-DBG',
        'vista'      => 'rsv.admin.partials.tab-calendario',
        'url'        => url()->full(),
        'peticion'   => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'global'     => empty($__calDbgInmId),
        'inmuebleId' => $__calDbgInmId,
        'inmuebles'  => (isset($listaInmuebles) && $listaInmuebles)
            ? (method_exists($listaInmuebles, 'total') ? $listaInmuebles->total() : $listaInmuebles->count())
            : (isset($inmuebles) && $inmuebles
                ? (method_exists($inmuebles, 'total') ? $inmuebles->total() : $inmuebles->count())
                : 0),
        'rutas'      => ['/rsv/reservas', '/rsv/bloqueos-calendario'],
        'errores'    => [],
    ];
@endphp

{{-- CAL-v3-DBG · Tab DBG terminal + LED + Panel LOCAL (fuera del root: sobrevive a los swaps · NO global) --}}
<div id="calDbgToggle" title="Debug Calendario (CAL-v3-DBG)"><span class="cal-dbg-led" id="calDbgLed"></span>DBG<span class="cal-dbg-badge" id="calDbgBadge">0</span></div>
<div id="calDbgPanel" role="dialog" aria-label="Panel de debug de Calendario">
    <div class="cal-dbg-header" id="calDbgHeader">
        <span>DEBUG · Calendario · CAL-v3-DBG</span>
        <span class="cal-dbg-controls">
            <button type="button" class="cal-dbg-btn" id="calDbgCopy">Copiar</button>
            <button type="button" class="cal-dbg-btn" id="calDbgClear">Limpiar</button>
            <button type="button" class="cal-dbg-btn" id="calDbgClose">✕</button>
        </span>
    </div>
    <div id="calDbgBody"></div>
</div>

<div id="calRoot">

    @php
        /*
        ══════════════════════════════════════════════════════════════
        FIX 1: el dashboard pasa $listaInmuebles (no $inmuebles).
        Antes el select quedaba vacío y en global no se podía filtrar.
        FIX 2: contexto — en individual se preselecciona el inmueble
        activo para que los eventos ya nazcan filtrados.
        ══════════════════════════════════════════════════════════════
        */
        $listaInmuebles = $listaInmuebles ?? $inmuebles ?? collect();

        $calInm = null; $calInmId = null; $calGlobal = true;
        if (isset($inmueble) && $inmueble) {
            $calInm = $inmueble;
            $calInmId = is_object($calInm) ? ($calInm->id ?? null) : $calInm;
        } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
            $calInmId = $inmuebleId;
        } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
            $calInmId = request('inmueble_id');
        }
        $calGlobal = empty($calInmId);
    @endphp

    <!-- ═══ CINTA SUPERIOR: leyenda + filtro ═══ -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-2 py-1.5 mb-2"
         style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px 4px 0 0;">

        <!-- Leyenda pastel -->
        <div class="d-flex flex-wrap align-items-center gap-1">

            <span class="me-1" style="font-size: 7.5px; letter-spacing: .05em; color: #b3bcc7; font-weight: 600;">
                <i class="bi bi-calendar3 me-1" style="font-size: 8px;"></i>LEYENDA
            </span>

            <span class="cal-chip"><span class="ldot" style="background:#a3cfb6;"></span> Aprobada</span>
            <span class="cal-chip"><span class="ldot" style="background:#ecd3a0;"></span> Pendiente</span>
            <span class="cal-chip"><span class="ldot" style="background:#a9cfe8;"></span> Endoso</span>
            <span class="cal-chip"><span class="ldot" style="background:#e5b8b8;"></span> Cancelada</span>
            <span class="cal-chip"><span class="ldot" style="background:#c98a85;"></span> Mantenimiento</span>

            {{-- Verificación de versión --}}
            <span class="cal-chip" style="border-color:#d5e7dc; color:#3d7a5c; font-weight:600;">CAL3-DBG</span>

        </div>

        <!-- Filtro de inmueble (suave) -->
        <div class="d-flex align-items-center gap-1">
            <i class="bi bi-building" style="font-size: 9px; color: #c3ccd6;"></i>
            <select id="filtro_inmueble_cal" class="cal-select" title="Filtrar por inmueble">
                <option value="">Todos los inmuebles</option>
                @forelse(($listaInmuebles ?? collect()) as $inm)
                    <option value="{{ $inm->id }}" @selected((string) $calInmId === (string) $inm->id)>{{ $inm->name }}</option>
                @empty
                    <option value="" disabled>Sin inmuebles cargados</option>
                @endforelse
            </select>
        </div>

    </div>


    <!-- ═══ MARCO DEL CALENDARIO + LOADER CRISTAL ═══ -->
    <div class="position-relative"
         style="border: 1px solid #f1f5f9; border-radius: 0 0 4px 4px; background: #fff; overflow: hidden;">

        <div id="calendar-loader"
             class="d-none position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
             style="z-index: 50; background: rgba(255,255,255,.72); backdrop-filter: blur(3px);">

            <div class="spinner-border spinner-border-sm" role="status" style="color: #3d7a5c; width: 14px; height: 14px;"></div>

        </div>

        <div id="calendario-global" class="p-1"></div>

    </div>

</div>


@push('style')
<style>
    /* ═══ BASE — anclada a #calRoot (misma paleta de las hojas) ═══ */
    #calRoot {
        --grid:  #f1f5f9;
        --grid-2:#e9eef4;
        --head:  #fafbfd;
        --ink:   #64748b;
        --ink-2: #475569;
        --ink-3: #94a3b8;
        --ink-4: #b3bcc7;
        --accent:#3d7a5c;
        --accent-bg: #f0f7f2;
        --accent-bd: #d5e7dc;
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px;
        line-height: 1.4;
        color: var(--ink);
    }

    /* Chips de leyenda */
    #calRoot .cal-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 8px; border-radius: 9px;
        font-size: 8px; font-weight: 500;
        border: 1px solid var(--grid); background: #fcfdfe; color: var(--ink-3);
    }
    #calRoot .ldot {
        width: 5px; height: 5px; border-radius: 50%;
        display: inline-block; opacity: .85;
    }

    /* Select suave */
    #calRoot .cal-select {
        border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2);
        font-family: inherit; font-size: 9px; font-weight: 500;
        padding: 2px 8px; border-radius: 3px; cursor: pointer;
        box-shadow: none !important; outline: none;
    }
    #calRoot .cal-select:focus { border-color: #c8d6cd; }


    /* ═══ FULLCALENDAR · overrides (scoped) ═══ */

    /* Grilla */
    #calRoot .fc-theme-standard td,
    #calRoot .fc-theme-standard th,
    #calRoot .fc-theme-standard .fc-scrollgrid { border-color: var(--grid); }

    /* Encabezados de día — 9px */
    #calRoot .fc-col-header-cell {
        padding: 4px 0 !important;
        background: var(--head);
        color: var(--ink-3);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: 9px !important;
        border: none !important;
    }
    #calRoot .fc-col-header-cell-cushion { color: inherit; text-decoration: none; padding: 2px 4px; }

    /* Celdas de día — 8px */
    #calRoot .fc-daygrid-day-number {
        font-size: 8px !important;
        padding: 2px 5px !important;
        font-weight: 400;
        color: var(--ink-3);
        text-decoration: none;
    }
    #calRoot .fc-day-today { background: var(--accent-bg) !important; }
    #calRoot .fc-day-today .fc-daygrid-day-number { color: var(--accent); font-weight: 600; }

    #calRoot .fc-dayGridMonth-view .fc-daygrid-day-frame { min-height: 62px; }
    #calRoot .fc-daygrid-day { transition: background .15s ease; }
    #calRoot .fc-daygrid-day:hover { background: #fafcfe; }

    /* Otros meses: aún más tenues */
    #calRoot .fc-day-other .fc-daygrid-day-number { color: #dfe5ec; }

    /* Toolbar — botones estilo soft */
    #calRoot .fc .fc-button {
        background: #fff !important;
        color: var(--ink-3) !important;
        border: 1px solid var(--grid-2) !important;
        box-shadow: none !important;
        font-family: inherit;
        font-size: 9px !important;
        font-weight: 600;
        text-transform: capitalize;
        border-radius: 3px !important;
        padding: 2px 9px !important;
        transition: all .15s ease;
    }
    #calRoot .fc .fc-button:hover {
        background: #fafcfe !important;
        color: var(--ink-2) !important;
        border-color: #d7dee6 !important;
    }
    #calRoot .fc .fc-button:not(:disabled).fc-button-active {
        background: var(--accent-bg) !important;
        color: var(--accent) !important;
        border-color: var(--accent-bd) !important;
    }
    #calRoot .fc .fc-button-group { gap: 3px; }
    #calRoot .fc .fc-toolbar-chunk { display: flex; gap: 4px; align-items: center; }

    /* Título del mes */
    #calRoot .fc-toolbar-title {
        font-size: 10.5px !important;
        font-weight: 600;
        color: var(--ink-2);
        letter-spacing: .01em;
    }

    /* Eventos — 8px, pastel, barra lateral de estado */
    #calRoot .fc-event {
        border-radius: 3px !important;
        padding: 1px 4px !important;
        margin-bottom: 1.5px !important;
        font-size: 8px !important;
        font-weight: 500;
        line-height: 1.3;
        box-shadow: none;
        border-width: 1px !important;
        border-left-width: 3px !important;
        cursor: pointer;
        transition: filter .15s ease;
    }
    #calRoot .fc-event:hover { filter: brightness(.985); }
    #calRoot .fc-event-main, .fc-event-title, .fc-event-time { color: inherit !important; }
    #calRoot .fc-event-title { font-weight: 600; }
    #calRoot .fc-daygrid-event-dot { display: none; }

    /* Estados (franja lateral pastel) */
    #calRoot .ev-cancelada {
        background: #fdf4f4 !important;
        border-color: #f0dcdc !important;
        color: #a87878 !important;
        text-decoration: line-through;
        opacity: .8;
    }
    #calRoot .ev-bloqueo {
        background: #fdf0ef !important;
        border-color: #edcfcf !important;
        color: #965757 !important;
    }

    /* Vista anual (multi-month) */
    #calRoot .fc-multimonth-month {
        border: 1px solid var(--grid);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 8px;
    }
    #calRoot .fc-multimonth-title {
        font-size: 9px !important; font-weight: 600;
        padding: 4px 9px;
        background: var(--head);
        border-bottom: 1px solid var(--grid);
        color: var(--ink-3);
        text-transform: uppercase; letter-spacing: .05em;
    }
    #calRoot .fc-multimonth-view .fc-daygrid-day-frame { min-height: 20px; }
    #calRoot .fc-multimonth-view .fc-event { font-size: 7.5px !important; padding: 0 3px !important; }

    /* Vista semana (timeGrid) */
    #calRoot .fc-timegrid-slot-label,
    #calRoot .fc-timegrid-axis { font-size: 7.5px !important; color: var(--ink-4); }
    #calRoot .fc-timegrid-event { font-size: 7.5px !important; border-radius: 3px; }
    #calRoot .fc-timegrid-col.fc-day-today { background: var(--accent-bg) !important; }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>

<script>
    /* ═══════════════ CAL-v3-DBG · tab-calendario · PANEL DE DEBUG LOCAL ═══════════════
       Estilo "terminal" aprobado (TIP-v1.3 / INM-v3): tab DBG textual de 16px con LED
       de estado, fundida SIEMPRE en la esquina (right:0 · bottom:0); el panel abre como
       CAJÓN pegado al mismo borde y esquiva a los vecinos midiendo px reales.
       NO captura fetch ni errores de otros módulos: solo registra operaciones de ESTA
       vista. El contexto del servidor llega por SRVDBG (JSON de $calDbg).              */
    (function () {
        var SRVDBG = @json($calDbg);

        var DBG = (window.CalDbg && window.CalDbg.alive && window.CalDbg.alive())
            ? window.CalDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('calDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'calDbgStyle';
                    st.textContent = [
                        /* ═════════ SPEC DE DISTANCIAS · CAL-v3 (tab terminal · patrón TIP-v1.3) ════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
                        '#calDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#calDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.cal-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.cal-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:calDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes calDbgBlink{50%{opacity:.35;}}',
                        '.cal-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#calDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.cal-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.cal-dbg-controls{display:flex;gap:4px;}',
                        '.cal-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.cal-dbg-btn:hover{background:#475569;}',
                        '#calDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.cal-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.cal-dbg-time{color:#64748b;margin-right:4px;}',
                        '.cal-dbg-info{color:#93c5fd;}.cal-dbg-ok{color:#86efac;}',
                        '.cal-dbg-warn{color:#fde68a;}.cal-dbg-err{color:#fca5a5;}',
                        '.cal-dbg-net{color:#c4b5fd;}.cal-dbg-debug{color:#94a3b8;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    /* El toggle y el panel ya vienen como HTML de la vista (antes de
                       #calRoot). Este respaldo solo los re-crea si un swap los borró. */
                    if (!$('calDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'calDbgToggle';
                        t.title = 'Debug Calendario (CAL-v3-DBG)';
                        t.innerHTML = '<span class="cal-dbg-led" id="calDbgLed"></span>DBG<span class="cal-dbg-badge" id="calDbgBadge">0</span>';
                        document.body.appendChild(t);
                    }
                    if (!$('calDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'calDbgPanel';
                        p.innerHTML = '<div class="cal-dbg-header" id="calDbgHeader">' +
                            '<span>DEBUG · Calendario · CAL-v3-DBG</span>' +
                            '<span class="cal-dbg-controls">' +
                            '<button type="button" class="cal-dbg-btn" id="calDbgCopy">Copiar</button>' +
                            '<button type="button" class="cal-dbg-btn" id="calDbgClear">Limpiar</button>' +
                            '<button type="button" class="cal-dbg-btn" id="calDbgClose">✕</button>' +
                            '</span></div><div id="calDbgBody"></div>';
                        document.body.appendChild(p);
                    }
                    apilar();
                }
                /* CAL-v3: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila:
                   con 16px de alto cabe bajo cualquier otro toggle sin tocarlo).
                   Solo el PANEL esquiva a los vecinos: bottom = max(20, vecino + 4). */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle']; /* serie completa */
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('calDbgToggle'), p = $('calDbgPanel');
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
                        var b = $('calDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'cal-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="cal-dbg-time">[' + hora() + ']</span>' +
                            '<span class="cal-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('calDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('calDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('calDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('calDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('calDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('calDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('calDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('calDbgBody'); if (b) { b.innerHTML = ''; } },
                    alive: function () { return !!$('calDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                $('calDbgToggle').addEventListener('click', api.togglePanel);
                $('calDbgClose').addEventListener('click', function (e2) { e2.stopPropagation(); api.close(); });
                $('calDbgClear').addEventListener('click', function (e2) { e2.stopPropagation(); api.clear(); });
                $('calDbgCopy').addEventListener('click', function (e2) {
                    e2.stopPropagation();
                    var b = $('calDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG tab-calendario CAL-v3-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        }).catch(function () {
                            api.log('no se pudo copiar (permisos del navegador)', 'warn');
                        });
                    } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
                });
                $('calDbgHeader').addEventListener('dblclick', api.close);
                window.CalDbg = api;
                return api;
            })();
        if (!DBG) { return; }

        /* ═══ BOOT · contexto del servidor (como el Panel de Inmuebles) ═══ */
        DBG.log('CAL-v3-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · panel LOCAL (solo tab-calendario · NO captura errores ajenos)', 'ok');
        DBG.log('init · modo=' + (SRVDBG && SRVDBG.global ? 'GLOBAL (todos los inmuebles)' : 'INDIVIDUAL (contexto preseleccionado)') +
            (SRVDBG && SRVDBG.inmuebleId ? ' · inmuebleCtx=' + SRVDBG.inmuebleId : '') +
            ' · inmuebles=' + (SRVDBG ? SRVDBG.inmuebles : '?'), 'info');
        DBG.log('SRV · peticion=' + (SRVDBG && SRVDBG.peticion ? SRVDBG.peticion : '?') +
            ' · url=' + (SRVDBG && SRVDBG.url ? SRVDBG.url : '?'), 'debug');
        DBG.log('Rutas fetch · ' + ((SRVDBG && SRVDBG.rutas) ? SRVDBG.rutas.join(' · ') : '?'), 'debug');
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }
    })();
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calEl  = document.getElementById('calendario-global'),
              filtro = document.getElementById('filtro_inmueble_cal'),
              loader = document.getElementById('calendar-loader');

        /* CAL-v3-DBG · guards con aviso en el panel (antes eran silenciosos) */
        if (!calEl) { if (window.CalDbg) { window.CalDbg.log('abort · no existe #calendario-global en el DOM', 'err'); } return; }
        if (typeof FullCalendar === 'undefined') { if (window.CalDbg) { window.CalDbg.log('abort · FullCalendar NO está cargado (CDN caído o bloqueado)', 'err'); } return; }

        const DBG = window.CalDbg;
        DBG.log('FullCalendar ' + (FullCalendar.version || 'global') + ' cargado · refs: cal=' + (calEl ? 'ok' : 'no') +
            ' · filtro=' + (filtro ? 'ok' : 'no') + ' · loader=' + (loader ? 'ok' : 'no'), 'debug');

        /* Paleta pastel por inmueble (saturación rebajada) */
        const getInmuebleStyle = (inmuebleName) => {
            if (!inmuebleName) return { bg: '#fafbfd', border: '#e2e8f0', text: '#64748b' };

            let hash = 0;
            for (let i = 0; i < inmuebleName.length; i++) {
                hash = inmuebleName.charCodeAt(i) + ((hash << 5) - hash);
            }
            const hue = Math.abs(hash) % 360;

            return {
                bg:     `hsl(${hue}, 45%, 96%)`,
                border: `hsl(${hue}, 35%, 80%)`,
                text:   `hsl(${hue}, 25%, 38%)`
            };
        };

        /* Franjas de estado pastel */
        const STATUS = {
            endoso:     '#8fb8e0',
            pendiente:  '#d9b56a',
            cancelada:  '#c98080',
            bloqueo:    '#c98a85'
        };

        const addDay = (dateStr) => {
            if (!dateStr) return null;
            const clean = dateStr.split(' ')[0];
            const parts = clean.split('-');
            if (parts.length !== 3) return clean;

            const y = parseInt(parts[0]), m = parseInt(parts[1]) - 1, d = parseInt(parts[2]);
            const dObj = new Date(y, m, d + 1);

            const yr = dObj.getFullYear();
            const mo = String(dObj.getMonth() + 1).padStart(2, '0');
            const da = String(dObj.getDate()).padStart(2, '0');
            return `${yr}-${mo}-${da}`;
        };

        const formatCleanDate = (dateStr) => {
            if (!dateStr) return '';
            const clean = dateStr.split(' ')[0];
            const parts = clean.split('-');
            if (parts.length !== 3) return dateStr;

            const year = parts[0];
            const monthIndex = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]);
            const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

            return `${day} de ${meses[monthIndex]} de ${year}`;
        };

        const calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            multiMonthMaxColumns: 3,
            locale: 'es',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'multiMonthYear,dayGridMonth,timeGridWeek' },
            buttonText: { year: 'Año', month: 'Mes', week: 'Semana', today: 'Hoy' },

            events: async (info, success, fail) => {
                const fIni = info.startStr.split('T')[0], fFin = info.endStr.split('T')[0];
                /* El select ya nace preseleccionado en individual → el contexto viaja solo */
                const qInm = (filtro && filtro.value) ? `&id_rsv_catalogo_inmueble=${filtro.value}` : '';

                /* CAL-v3-DBG · telemetría de fetch */
                const __t0 = (window.performance && performance.now) ? performance.now() : Date.now();
                DBG.log('fetch · rango ' + fIni + ' → ' + fFin + (qInm ? ' · filtro=' + filtro.value : ' · sin filtro') + ' …', 'debug');

                loader.classList.remove('d-none');

                try {
                    const [res, blq] = await Promise.all([
                        fetch(`/rsv/reservas?fecha_desde=${fIni}&fecha_hasta=${fFin}&per_page=2000${qInm}`, { headers: { Accept: 'application/json' } }).then(r => r.json()),
                        fetch(`/rsv/bloqueos-calendario?fecha_desde=${fIni}&fecha_hasta=${fFin}&per_page=2000${qInm}`, { headers: { Accept: 'application/json' } }).then(r => r.json())
                    ]);

                    const arrRes = res.data?.data || res.data || [];
                    const arrBlq = blq.data?.data || blq.data || [];

                    const evts = [
                        ...arrRes.filter(r => r.fecha_inicio && r.fecha_fin).map(r => {
                            let st = (r.status?.nombre || r.status?.name || '').toLowerCase();
                            let endoso = r.historial_endosos?.length || r.historialEndosos?.length;
                            let inmuebleName = r.inmueble?.name || 'Apto';

                            let theme = getInmuebleStyle(inmuebleName);
                            let cssClass = '';
                            let icn = 'bi-check2-circle';
                            let statusBorderColor = theme.border;

                            if (st.includes('cancelad')) {
                                cssClass = 'ev-cancelada';
                                icn = 'bi-x-circle';
                                statusBorderColor = STATUS.cancelada;
                            } else if (endoso || st.includes('endos')) {
                                icn = 'bi-arrow-left-right';
                                statusBorderColor = STATUS.endoso;
                            } else if (st.includes('pendient') || st.includes('pre')) {
                                icn = 'bi-clock-history';
                                statusBorderColor = STATUS.pendiente;
                            }

                            let startRaw = r.fecha_inicio.split(' ')[0];
                            let endRaw = r.fecha_fin.split(' ')[0];

                            return {
                                id: `rsv_${r.id}`,
                                title: `${inmuebleName} (${r.codigo_reserva})`,
                                start: startRaw,
                                end: addDay(endRaw),
                                allDay: true,
                                classNames: [cssClass],
                                backgroundColor: theme.bg,
                                borderColor: theme.border,
                                textColor: theme.text,
                                extendedProps: {
                                    t: 'rsv',
                                    icn,
                                    cod: r.codigo_reserva,
                                    est: st.toUpperCase(),
                                    inm: inmuebleName,
                                    usr: r.user?.name,
                                    realStart: startRaw,
                                    realEnd: endRaw,
                                    statusColor: statusBorderColor
                                }
                            };
                        }),
                        ...arrBlq.filter(b => b.fecha_inicio && b.fecha_fin).map(b => {
                            let startRaw = b.fecha_inicio.split(' ')[0];
                            let endRaw = b.fecha_fin.split(' ')[0];
                            return {
                                id: `blq_${b.id}`,
                                title: `Mtto: ${b.inmueble?.name || ''}`,
                                start: startRaw,
                                end: addDay(endRaw),
                                allDay: true,
                                classNames: ['ev-bloqueo'],
                                extendedProps: {
                                    t: 'blq',
                                    icn: 'bi-tools',
                                    dtl: b.motivo,
                                    inm: b.inmueble?.name,
                                    realStart: startRaw,
                                    realEnd: endRaw,
                                    statusColor: STATUS.bloqueo
                                }
                            };
                        })
                    ];

                    /* CAL-v3-DBG · resultado del fetch */
                    const __ms = Math.round(((window.performance && performance.now) ? performance.now() : Date.now()) - __t0);
                    DBG.log('eventos · reservas=' + arrRes.length + ' · bloqueos=' + arrBlq.length + ' · montados=' + evts.length + ' · ' + __ms + 'ms', 'ok');

                    loader.classList.add('d-none');
                    success(evts);
                } catch (e) {
                    console.error("Error cargando eventos:", e);
                    DBG.log('fetch falló · ' + (e && e.message ? e.message : e), 'err');
                    loader.classList.add('d-none'); fail(e);
                }
            },

            eventDidMount: (info) => {
                const stColor = info.event.extendedProps.statusColor;
                if (stColor) {
                    info.el.style.borderLeft = `3px solid ${stColor}`;
                }
            },

            eventContent: (arg) => ({
                html: `<div class="text-truncate">
                          <i class="bi ${arg.event.extendedProps.icn} me-1"></i>${arg.event.title}
                       </div>`
            }),

            eventClick: ({ event }) => {
                const { t, icn, cod, est, usr, dtl, inm, realStart, realEnd } = event.extendedProps;

                /* CAL-v3-DBG · apertura del modal */
                DBG.log('modal · ' + (t === 'blq' ? 'mantenimiento' : 'reserva') + ' · ' + (cod || dtl || event.id), 'info');

                Swal.fire({
                    title: t === 'blq' ? 'Mantenimiento' : 'Reserva',
                    html: `
                        <div class="text-start mt-1" style="font-family: Calibri, 'Segoe UI', sans-serif; font-size: 11px; color: #64748b;">
                            <div style="font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 8px;">
                                <i class="bi ${icn}" style="color:#3d7a5c;"></i> ${inm || 'Inmueble'}
                            </div>

                            ${t === 'blq'
                                ? `<div style="margin-bottom:3px;"><b style="color:#475569;">Motivo:</b> ${dtl || '—'}</div>`
                                : `<div style="margin-bottom:3px;"><b style="color:#475569;">Código:</b> ${cod}</div>
                                   <div style="margin-bottom:3px;"><b style="color:#475569;">Estado:</b> ${est}</div>
                                   <div style="margin-bottom:3px;"><b style="color:#475569;">Asociado:</b> ${usr || '—'}</div>`}

                            <hr style="border:none; border-top:1px solid #f1f5f9; margin:8px 0;">

                            <div style="margin-bottom:3px; color:#94a3b8;">
                                <i class="bi bi-box-arrow-in-right"></i> <b>Ingreso:</b> ${formatCleanDate(realStart)}
                            </div>
                            <div style="margin-bottom:0; color:#94a3b8;">
                                <i class="bi bi-box-arrow-left"></i> <b>Salida:</b> ${formatCleanDate(realEnd)}
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3d7a5c',
                    buttonsStyling: true
                });
            }
        });

        // 1. EXPORTAR EL CALENDARIO GLOBALMENTE PARA PODER REFRESCARLO DESDE OTRAS VISTAS
        window.rsvCalendar = calendar;

        /* CAL-v3-DBG · export visible en el panel */
        DBG.log('window.rsvCalendar exportado · refresco externo disponible desde otras vistas', 'ok');

        calendar.render();

        if (filtro) {
            filtro.addEventListener('change', () => {
                /* CAL-v3-DBG · cambio de filtro */
                DBG.log('filtro inmueble → ' + (filtro.value || 'todos') + ' · refetchEvents', 'info');
                calendar.refetchEvents();
            });
        }

        /* ═══ FIX DEFINITIVO · updateSize & refetchEvents ═══
           Al entrar en la pestaña Padre (Calendario) o Hijo (Inicio), 
           forzamos a FullCalendar a calcular su tamaño Y volver a traer los datos. */
        document.addEventListener('shown.bs.tab', (e) => {
            const target = e.target?.getAttribute?.('data-bs-target') || e.target?.getAttribute?.('href');
            if (!target) return;

            const targetsCalendario = [
                '#global-calendario', 
                '#calendario', 
                '#cal-sub-inicio', 
                '#single-cal-sub-inicio'
            ];

            if (targetsCalendario.includes(target)) {
                /* CAL-v3-DBG · reacción al tab */
                DBG.log('tab ' + target + ' visible · updateSize + refetchEvents (en 200ms)', 'info');
                setTimeout(() => {
                    calendar.updateSize();    // Evita que se vea blanco
                    calendar.refetchEvents(); // TRAE LOS DATOS NUEVOS DE BLOQUEOS/RESERVAS
                }, 200); // 200ms da tiempo a que la animación de Bootstrap termine
            }
        });

        /* Red de seguridad: resize de ventana y primer pintado */
        window.addEventListener('resize', () => calendar.updateSize());
        setTimeout(() => calendar.updateSize(), 300);

        /* CAL-v3-DBG · red de seguridad armada */
        DBG.log('red de seguridad · resize listener + updateSize(300ms) armados', 'debug');
    });
</script>
@endpush

