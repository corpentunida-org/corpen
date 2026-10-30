{{-- ============================================================================
     RSV · ADMIN · RESERVAS · ORÍGENES · OR-v1-DBG
     Parcial: resources/views/rsv/admin/partials/reservas/origen_reserva.blade.php

     · Base (conservada ÍNTEGRA): CRUD 100% Bootstrap/Blade/AJAX de Orígenes
       de Reserva (alta · edición · baja), resolutor dinámico de rutas con
       candidatos (rsv.origenes.* / rsv.origen-reservas.*), modo editable
       condicionado a rutas, CSRF en POST/PUT/DELETE, buscador en tabla y
       recarga del fragmento vía fetch.
     · PANEL DE DEBUG (OR-v1-DBG · diseño TIP-v1.3 aprobado · canónico
       BLQ-v1-2 · clon de ST-v1-DBG): pestaña terminal "DBG" de 16px fija
       SIEMPRE en la esquina inferior-derecha (right:0 · bottom:0, radius
       3px solo superior-izquierdo) con LED de estado (verde = ok · rojo
       pulsante + conteo = errores) y panel cajón de 380px alineado al
       mismo borde. La regla exacta de distancias está documentada como
       "SPEC DE DISTANCIAS" dentro del CSS, y el panel esquiva a los
       toggles hermanos de las otras vistas midiendo píxeles reales
       (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Exclusivo de este fragmento: el toggle y el panel son HERMANOS de
       #orRoot (el refresco AJAX interno solo intercambia el root y los
       nodos DBG sobreviven con el CSS que viaja dentro de él) y unicarDbg()
       deduplica dejando UN solo toggle/panel por página. Nada se cuelga de
       document.body: el respaldo re-crea DENTRO de #orRoot.
       Telemetría que reporta: orígenes cargados, modo editable (crear/
       editar/borrar), rutas (store/update/destroy/fragmento), submits
       AJAX (alta/edición/baja), rechazos HTTP con mensaje, refresco del
       fragmento, búsqueda en tabla y errores capturados por el servidor.
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

{{-- OR-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano del root: vive y muere con la vista · NO viaja en el refresco AJAX interno) --}}
<div id="orDbgToggle" title="Debug Orígenes (OR-v1-DBG)"><span class="or-dbg-led" id="orDbgLed"></span>DBG<span class="or-dbg-badge" id="orDbgBadge">0</span></div>
<div id="orDbgPanel" role="dialog" aria-label="Panel de debug de Orígenes de Reserva">
    <div class="or-dbg-header" id="orDbgHeader">
        <span>DEBUG · Orígenes de Reserva · OR-v1-DBG</span>
        <span class="or-dbg-controls">
            <button type="button" class="or-dbg-btn" id="orDbgCopy">Copiar</button>
            <button type="button" class="or-dbg-btn" id="orDbgClear">Limpiar</button>
            <button type="button" class="or-dbg-btn" id="orDbgClose">✕</button>
        </span>
    </div>
    <div id="orDbgBody"></div>
</div>

<div class="reservas-soft p-3 bg-white rounded-3 border" id="orRoot"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; outline: 0; min-width: 200px; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }

    /* ═════════ SPEC DE DISTANCIAS · OR-v1-DBG (tab terminal TIP-v1.3) ═════════
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
       UNICIDAD  · toggle y panel son HERMANOS de #orRoot (el refresco AJAX
                   interno solo intercambia el root: los nodos y el CSS
                   sobreviven) y unicarDbg() deja UN solo toggle/panel por
                   página. El respaldo re-crea DENTRO de #orRoot. */
    #orDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    #orDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
    .or-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .or-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: orDbgBlink 1s steps(2,start) infinite; }
    @keyframes orDbgBlink { 50% { opacity: .35; } }
    .or-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
    #orDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .or-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
    .or-dbg-controls { display: flex; gap: 4px; }
    .or-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
    .or-dbg-btn:hover { background: #475569; }
    #orDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
    .or-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
    .or-dbg-time { color: #64748b; margin-right: 4px; }
    .or-dbg-info { color: #93c5fd; } .or-dbg-ok { color: #86efac; }
    .or-dbg-warn { color: #fde68a; } .or-dbg-err { color: #fca5a5; }
    .or-dbg-net { color: #c4b5fd; } .or-dbg-debug { color: #94a3b8; }
</style>

    @php
        /* ═══ OR-v1-DBG · colector de errores del servidor para el panel ═══ */
        $orDbgErrores = [];

        // SOLUCIÓN AL ERROR: Si la variable no viene del controlador principal, la consulta aquí mismo.
        try {
            $origenes = $origenes ?? \App\Models\Rsv\OrigenReserva::orderBy('nombre', 'asc')->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('or dataset: ' . $e->getMessage());
            $orDbgErrores[] = 'dataset: ' . $e->getMessage();
            $origenes = collect();
        }

        // Resolutor de rutas dinámico
        $orRuta = function (array $cands, $param = null) {
            foreach ($cands as $c) {
                if (\Illuminate\Support\Facades\Route::has($c)) return $param !== null ? route($c, $param) : route($c);
            }
            return null;
        };
        $orStore   = $orRuta(['rsv.origenes.store', 'rsv.origen-reservas.store']);
        $orUpdate  = $orRuta(['rsv.origenes.update', 'rsv.origen-reservas.update'], 'ID');
        $orDestroy = $orRuta(['rsv.origenes.destroy', 'rsv.origen-reservas.destroy'], 'ID');
        $orEditable = !!($orStore || $orUpdate);

        /* ═══ OR-v1-DBG · telemetría del fragmento · via al panel vía la directiva json de Blade ═══ */
        $orFragmentoDbg = \Illuminate\Support\Facades\Route::has('rsv.admin.reservas.origen.fragmento')
            ? route('rsv.admin.reservas.origen.fragmento')
            : null;
        $orDbg = [
            'version'  => 'OR-v1-DBG',
            'vista'    => 'partials/reservas/origen_reserva.blade.php',
            'render'   => now()->format('H:i:s'),
            'origenes' => count($origenes),
            'editable' => $orEditable ? 'sí' : 'no',
            'puede'    => [
                'crear'  => $orStore   ? 'sí' : 'no',
                'editar' => $orUpdate  ? 'sí' : 'no',
                'borrar' => $orDestroy ? 'sí' : 'no',
            ],
            'rutas'    => [
                'store'     => $orStore   ?: 'NO definida',
                'update'    => $orUpdate  ?: 'NO definida',
                'destroy'   => $orDestroy ?: 'NO definida',
                'fragmento' => $orFragmentoDbg ?: 'NO definida',
            ],
            'errores'  => $orDbgErrores,
        ];
    @endphp

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-box-arrow-in-right me-1" style="font-size: 8px; color: #5d8a70;"></i> Orígenes de Reserva
            <span class="badge rounded-pill ms-1" id="orChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">OR-v1-DBG</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b>{{ count($origenes ?? []) }}</b> orígenes</span>
            <input type="text" id="orSearch" class="xls-search" placeholder="Buscar origen…" autocomplete="off" style="width: 170px;">
            @if($orStore)
                <button type="button" class="btn-green" onclick="openOrModal()">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Origen
                </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr><th>Origen</th><th class="text-center" style="width: 90px;">·</th></tr>
            </thead>
            <tbody id="orTbody">
                @forelse($origenes as $o)
                    <tr class="or-row">
                        <td class="strong or-name-col">{{ $o->nombre }}</td>
                        <td class="text-center">
                            @if($orEditable)
                                <button type="button" class="btn-mini" onclick="openOrModal({{ $o->id }}, '{{ addslashes($o->nombre) }}')">Editar</button>
                            @endif
                            @if($orDestroy)
                                <button type="button" class="btn-mini" style="color:#c98a85;" onclick="confirmOrDelete({{ $o->id }}, '{{ addslashes($o->nombre) }}')">Del</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Sin orígenes definidos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="orModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" id="orModalTitle" style="font-size: 0.95rem;">Nuevo Origen</h6>
            </div>
            <form id="orForm">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="orId" name="id" value="">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Nombre <span style="color:#c98a85;">*</span></label>
                        {{-- CAMBIO CRÍTICO: el name debe ser "nombre" para coincidir con la DB y el modelo --}}
                        <input type="text" class="form-control form-control-sm" id="orName" name="nombre" maxlength="255" required placeholder="Booking, Airbnb...">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="orModalDel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar origen</h6>
                <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                    Se eliminará <b id="orDelName" style="color: #334155;">—</b>
                </p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="orDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT AJAX, MODALES, BÚSQUEDA Y PANEL DE DEBUG (OR-v1-DBG) --}}
<script>
(function () {
    'use strict';

    // Evita doble binding de eventos cuando el fragmento se re-inyecta vía AJAX.
    if (window.__orPanelInit) return;
    window.__orPanelInit = true;

    // Variables globales para las rutas (usando route() de Laravel de forma segura)
    const OR_STORE_URL   = '{{ $orStore ?? "" }}';
    const OR_UPDATE_URL  = '{{ $orUpdate ?? "" }}';
    const OR_DESTROY_URL = '{{ $orDestroy ?? "" }}';
    // Se añade el prefijo "rsv." que exige tu grupo de rutas
    const OR_FRAG_URL    = '{{ route("rsv.admin.reservas.origen.fragmento") }}';
    const CSRF_TOKEN     = '{{ csrf_token() }}';

    /* ═══ 0) PANEL DE DEBUG LOCAL OR-v1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de esta vista: los nodos son HTML del fragmento (hermanos
       de #orRoot, sobreviven al refresco AJAX interno que solo intercambia
       el root) y el respaldo solo re-crea DENTRO de #orRoot.
       Nada se cuelga de document.body. */
    var DBG = (window.OrDbg && window.OrDbg.alive && window.OrDbg.alive())
        ? window.OrDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven como hermanos de #orRoot), se re-crean DENTRO del root.
                   Cero document.body (exclusividad total). */
                var rootDbg = document.getElementById('orRoot');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('orDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'orDbgToggle';
                    t.title = 'Debug Orígenes (OR-v1-DBG)';
                    t.innerHTML = '<span class="or-dbg-led" id="orDbgLed"></span>DBG<span class="or-dbg-badge" id="orDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('orDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'orDbgPanel';
                    p.innerHTML = '<div class="or-dbg-header" id="orDbgHeader">' +
                        '<span>DEBUG · Orígenes de Reserva · OR-v1-DBG</span>' +
                        '<span class="or-dbg-controls">' +
                        '<button type="button" class="or-dbg-btn" id="orDbgCopy">Copiar</button>' +
                        '<button type="button" class="or-dbg-btn" id="orDbgClear">Limpiar</button>' +
                        '<button type="button" class="or-dbg-btn" id="orDbgClose">✕</button>' +
                        '</span></div><div id="orDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* OR-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle', 'stDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('orDbgToggle'), p = $('orDbgPanel');
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
                    var b = $('orDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'or-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="or-dbg-time">[' + hora() + ']</span>' +
                        '<span class="or-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('orDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('orDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('orDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('orDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('orDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('orDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('orDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('orDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('orDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG orígenes OR-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('orDbgToggle'); }
            };
            window.OrDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var ordbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function unicarDbg() {
        /* OR-v1-DBG: el parcial puede renderizarse 2+ veces; el HTML de cada
           copia trae sus propios nodos DBG → se conservan los del primero y
           se eliminan los duplicados (un solo toggle/panel por página). */
        var ts = document.querySelectorAll('#orDbgToggle');
        for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
        var ps = document.querySelectorAll('#orDbgPanel');
        for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
    }
    unicarDbg();

    /* Botones del panel por DELEGACIÓN en document (el guard __orPanelInit
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__orDbgDocHandler) { document.removeEventListener('click', window.__orDbgDocHandler); }
    window.__orDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#orDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#orDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#orDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#orDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__orDbgDocHandler);
    if (window.__orDbgDblHandler) { document.removeEventListener('dblclick', window.__orDbgDblHandler); }
    window.__orDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#orDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__orDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($orDbg ?? null);
    ordbg('OR-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con la vista)', 'ok');
    ordbg('init · orígenes=' + (SRVDBG ? SRVDBG.origenes : '?') + ' · editable=' + (SRVDBG ? SRVDBG.editable : '?') +
        ' · crear=' + (SRVDBG && SRVDBG.puede ? SRVDBG.puede.crear : '?') +
        ' · editar=' + (SRVDBG && SRVDBG.puede ? SRVDBG.puede.editar : '?') +
        ' · borrar=' + (SRVDBG && SRVDBG.puede ? SRVDBG.puede.borrar : '?'), 'info');
    if (SRVDBG && SRVDBG.origenes === 0) {
        ordbg('dataset vacío · sin orígenes definidos todavía', 'warn');
    }
    if (SRVDBG && SRVDBG.rutas) {
        var _rk = Object.keys(SRVDBG.rutas);
        for (var _ri = 0; _ri < _rk.length; _ri++) {
            if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                ordbg('ruta no definida: origen…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
            }
        }
        ordbg('Rutas · store=' + SRVDBG.rutas.store + ' · update=' + SRVDBG.rutas.update + ' · destroy=' + SRVDBG.rutas.destroy, 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            ordbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    // Función para recargar SOLO el fragmento HTML
    window.reloadOrFragment = function () {
        ordbg('refrescando fragmento · ' + OR_FRAG_URL, 'net');
        fetch(OR_FRAG_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newContent = temp.querySelector('#orRoot');
                if (newContent) {
                    document.getElementById('orRoot').replaceWith(newContent);
                    unicarDbg();
                    if (DBG && DBG.ensure) { DBG.ensure(); } // re-apila tras el swap
                    ordbg('fragmento refrescado · dataset releído del servidor', 'ok');
                } else {
                    ordbg('respuesta sin #orRoot · el fragmento no se reemplazó', 'warn');
                }
            })
            .catch(err => {
                console.error('Error recargando fragmento de orígenes:', err);
                ordbg('fallo al recargar el fragmento · ' + (err && err.message ? err.message : 'error de red'), 'err');
            });
    };

    // BÚSQUEDA EN TABLA (Delegación global de eventos 'input' · sobrevive al refresco del fragmento)
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'orSearch') {
            var orVal = e.target.value.toLowerCase().trim();
            var orRows = document.querySelectorAll('#orTbody .or-row');
            var orVis = 0;
            orRows.forEach(function (row) {
                var nameEl = row.querySelector('.or-name-col');
                var name = nameEl ? nameEl.textContent.toLowerCase() : '';
                var show = !orVal || name.indexOf(orVal) !== -1;
                row.style.display = show ? '' : 'none';
                if (show) { orVis++; }
            });
            ordbg('búsqueda · "' + orVal + '" · ' + orVis + ' de ' + orRows.length + ' origen(es) visible(s)', 'debug');
        }
    });

    // Abrir Modal de Formulario
    window.openOrModal = function(id = null, name = '') {
        ordbg('modal ' + (id ? 'edición · origen #' + id : 'alta'), 'debug');
        document.getElementById('orModalTitle').textContent = id ? 'Editar Origen' : 'Nuevo Origen';
        document.getElementById('orId').value = id || '';
        document.getElementById('orName').value = name;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalForm')).show();
    };

    // Confirmar Eliminación
    window.confirmOrDelete = function(id, name) {
        document.getElementById('orDelName').textContent = name;
        document.getElementById('orDelGo').onclick = function() {
            if(!OR_DESTROY_URL) return;
            const fd = new FormData();
            fd.append('_token', CSRF_TOKEN);
            fd.append('_method', 'DELETE');

            ordbg('baja AJAX · ' + OR_DESTROY_URL.replace('ID', id) + ' · origen #' + id, 'debug');
            fetch(OR_DESTROY_URL.replace('ID', id), {
                method: 'POST', body: fd,
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(() => {
                bootstrap.Modal.getInstance(document.getElementById('orModalDel')).hide();
                ordbg('baja OK · origen #' + id + ' · refrescando fragmento', 'ok');
                reloadOrFragment();
            })
            .catch(err => {
                console.error(err);
                ordbg('error de conexión en la baja · ' + (err && err.message ? err.message : 'error de red'), 'err');
                alert('Error de conexión con el servidor.');
            });
        };
        bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalDel')).show();
    };

    // Envío del Formulario (Crear / Editar)
    document.getElementById('orForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('orId').value;
        const fd = new FormData(this);

        let url = OR_STORE_URL;
        if (id && OR_UPDATE_URL) {
            url = OR_UPDATE_URL.replace('ID', id);
            fd.append('_method', 'PUT');
        }

        ordbg('submit AJAX · ' + url + ' · form=orForm · ' + (id ? 'edición #' + id : 'alta'), 'debug');

        fetch(url, {
            method: 'POST', body: fd,
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
        .then(res => {
            if (!res.ok) {
                let errorMsg = 'Error al guardar.';
                if (res.body.errors) {
                    errorMsg = Object.values(res.body.errors).map(err => err.join('\n')).join('\n');
                } else if (res.body.message) {
                    errorMsg = res.body.message;
                }
                ordbg('rechazo HTTP ' + res.status + ' · ' + String(errorMsg).replace(/\s+/g, ' ').slice(0, 120), 'err');
                alert(errorMsg);
                return;
            }

            bootstrap.Modal.getInstance(document.getElementById('orModalForm')).hide();
            ordbg('operación OK · modal cerrado · refrescando fragmento', 'ok');
            reloadOrFragment();
        })
        .catch(() => {
            ordbg('error de conexión con el servidor', 'err');
            alert('Error de conexión con el servidor.');
        });
    });
})();
</script>
