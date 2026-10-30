{{-- ============================================================================
     RSV · ADMIN · AUDITORÍA · TAB · AUD-v1-3
     Parcial: resources/views/rsv/admin/partials/auditoria-tab.blade.php

     · REENTREGA DEFINITIVA (AUD-v1-3): el LOG de debug que venimos
       integrando (diseño TIP-v1.3-DBG · ejemplo canónico BLQ-v1-2 de
       partials/calendario/bloqueos.blade.php) va DENTRO del parcial:
         1) Tab DBG terminal de 16px SIEMPRE fundida en la esquina inferior-
            derecha (right:0 · bottom:0 · radius 3px solo sup-izq) con LED
            (verde ok · rojo pulsante + badge = conteo de errores) y panel
            cajón de 380px. La regla exacta vive como "SPEC DE DISTANCIAS"
            en el CSS y el panel esquiva a los toggles hermanos midiendo
            píxeles reales (bottom = max(20, vecino + 4)).
         2) Toggle y panel son los PRIMEROS hijos de #sheet-auditoria (nacen
            y mueren con el parcial) · unicarDbg() deja UN solo par por
            página · cero nodos colgados de document.body.
         3) El bloque de script vive FUERA del root y el guard usa la convención de
            la familia window.__jsAuditoriaTabListo (delegación en document
            con removeEventListener + re-add).
         4) (AUD-v1-3) RUTA CORREGIDA: el tab 5 del dashboard ahora incluye
            ESTE archivo por su ruta real rsv.admin.partials.auditoria-tab
            (el include anterior apuntaba a tab-auditoria, que no existe:
            esa era la rotura "View not found" al abrir la pestaña).
     · CONTENIDO ORIGINAL PRESERVADO: tarjetas "Bitácora del Sistema (Audit
       Logs)" y "Catálogos Maestros" intactas; solo se añade el chip de
       versión y el botón Configurar reporta su clic al log (todavía no hay
       ruta asociada: queda pendiente de controlador).
     · DIAGNÓSTICO DE SERVIDOR: el bloque php del servidor sondea en
       try/catch la existencia y el nº de filas de las tablas que esta
       pestaña documenta (bitácora y catálogos maestros) y lo inyecta como
       telemetría al panel vía la directiva json de Blade.
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

@php
    /* ── Diagnóstico ligero de servidor (AUD-v1-3) ────────────────────────────
       Sondea en try/catch la existencia y nº de filas de las tablas que esta
       pestaña documenta. Nunca lanza: cualquier fallo se reporta como
       telemetría y la vista sigue renderizando intacta. */
    $audErrores = [];

    $audTablaEstado = function (string $audT) use (&$audErrores) {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable($audT)) { return 'NO detectada'; }
            $audN = \Illuminate\Support\Facades\DB::table($audT)->count();
            return 'Sí · ' . $audN . ' fila' . ($audN == 1 ? '' : 's');
        } catch (\Throwable $audE) {
            $audErrores[] = $audT . ': ' . $audE->getMessage();
            return 'error de lectura';
        }
    };

    /* ── Colector del panel de debug (AUD-v1-3) · via al panel vía la directiva json de Blade ── */
    $audDbg = [
        'version' => 'AUD-v1-3',
        'vista'   => 'partials/auditoria-tab.blade.php',
        'render'  => now()->format('H:i:s'),
        'tablas'  => [
            'bitacora  · rsv_audit_logs'        => $audTablaEstado('rsv_audit_logs'),
            'origenes  · rsv_origen_reservas'   => $audTablaEstado('rsv_origen_reservas'),
            'receptores· rsv_tipo_receptor'     => $audTablaEstado('rsv_tipo_receptor'),
            'estados   · rsv_statuses'          => $audTablaEstado('rsv_statuses'),
            'historial · rsv_historial_estados' => $audTablaEstado('rsv_historial_estados'),
        ],
        'errores' => $audErrores,
    ];
@endphp

<div id="sheet-auditoria">

    {{-- AUD-v1-3 · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3 · vive y muere con esta copia · unicarDbg deja un solo par por página) --}}
    <div id="audDbgToggle" title="Debug Auditoría (AUD-v1-3)"><span class="aud-dbg-led" id="audDbgLed"></span>DBG<span class="aud-dbg-badge" id="audDbgBadge">0</span></div>
    <div id="audDbgPanel" role="dialog" aria-label="Panel de debug de Auditoría">
        <div class="aud-dbg-header" id="audDbgHeader">
            <span>DEBUG · Auditoría (Tab) · AUD-v1-3</span>
            <span class="aud-dbg-controls">
                <button type="button" class="aud-dbg-btn" id="audDbgCopy">Copiar</button>
                <button type="button" class="aud-dbg-btn" id="audDbgClear">Limpiar</button>
                <button type="button" class="aud-dbg-btn" id="audDbgClose">✕</button>
            </span>
        </div>
        <div id="audDbgBody"></div>
    </div>

    <style>
        /* ═════════ SPEC DE DISTANCIAS · AUD-v1-3 (tab terminal TIP-v1.3) ═════════
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
           UNICIDAD  · este parcial puede renderizarse 2+ veces en la página: los
                       nodos nacen y mueren con cada copia y unicarDbg() deja UN
                       solo toggle/panel (los duplicados se eliminan al vuelo). */
        #audDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
        #audDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
        .aud-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
        .aud-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: audDbgBlink 1s steps(2,start) infinite; }
        @keyframes audDbgBlink { 50% { opacity: .35; } }
        .aud-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
        #audDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        .aud-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
        .aud-dbg-controls { display: flex; gap: 4px; }
        .aud-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
        .aud-dbg-btn:hover { background: #475569; }
        #audDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
        .aud-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
        .aud-dbg-time { color: #64748b; margin-right: 4px; }
        .aud-dbg-info { color: #93c5fd; } .aud-dbg-ok { color: #86efac; }
        .aud-dbg-warn { color: #fde68a; } .aud-dbg-err { color: #fca5a5; }
        .aud-dbg-net { color: #c4b5fd; } .aud-dbg-debug { color: #94a3b8; }
    </style>

    {{-- ─────────────────── CONTENIDO ORIGINAL (preservado) ─────────────────── --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white">
                <div class="card-body">
                    <h5 class="fw-bold text-warning mb-1"><i class="bi bi-shield-lock me-2"></i>Bitácora del Sistema (Audit Logs)<span class="badge rounded-pill ms-1" id="audChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">AUD-v1-3</span></h5>
                    <p class="small text-light mb-0">Trazabilidad inmutable de todas las acciones de los usuarios (rsv_audit_logs).</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 text-center d-flex flex-column">
                <h6 class="fw-bold">Catálogos Maestros</h6>
                <span class="d-block small text-muted mb-1">Orígenes: Web, App (rsv_origen_reservas)</span>
                <span class="d-block small text-muted mb-1">Tipos de Receptor (rsv_tipo_receptor)</span>
                <span class="d-block small text-muted mb-3">Estados Permitidos (rsv_statuses / rsv_historial_estados)</span>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill mt-auto" id="audBtnConfigurar">Configurar</button>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    'use strict';
    if (window.__jsAuditoriaTabListo) return;
    window.__jsAuditoriaTabListo = true;

    /* ═══ 0) PANEL DE DEBUG LOCAL AUD-v1-3 (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de este fragmento: los nodos son HTML de la hoja (viven
       DENTRO de #sheet-auditoria y renacen con cada copia) y el respaldo
       solo re-crea DENTRO de esa hoja. Cero document.body.
       Con copias múltiples del parcial, unicarDbg() deja un solo par. */
    var DBG = (window.AudDbg && window.AudDbg.alive && window.AudDbg.alive())
        ? window.AudDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven dentro de la primera #sheet-auditoria), se re-crean
                   DENTRO de esa hoja. */
                var rootDbg = document.querySelector('#sheet-auditoria');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('audDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'audDbgToggle';
                    t.title = 'Debug Auditoría (AUD-v1-3)';
                    t.innerHTML = '<span class="aud-dbg-led" id="audDbgLed"></span>DBG<span class="aud-dbg-badge" id="audDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('audDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'audDbgPanel';
                    p.innerHTML = '<div class="aud-dbg-header" id="audDbgHeader">' +
                        '<span>DEBUG · Auditoría (Tab) · AUD-v1-3</span>' +
                        '<span class="aud-dbg-controls">' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgCopy">Copiar</button>' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgClear">Limpiar</button>' +
                        '<button type="button" class="aud-dbg-btn" id="audDbgClose">✕</button>' +
                        '</span></div><div id="audDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function unicarDbg() {
                /* AUD-v1-3: el parcial puede renderizarse 2+ veces; el HTML de
                   cada copia trae sus propios nodos DBG → se conservan los de
                   la primera hoja y se eliminan los duplicados. */
                var ts = document.querySelectorAll('#audDbgToggle');
                for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                var ps = document.querySelectorAll('#audDbgPanel');
                for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
            }
            /* AUD-v1-3: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'infDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('audDbgToggle'), p = $('audDbgPanel');
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
                    var b = $('audDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'aud-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="aud-dbg-time">[' + hora() + ']</span>' +
                        '<span class="aud-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('audDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('audDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('audDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('audDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('audDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('audDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('audDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('audDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('audDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG auditoría AUD-v1-3 · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('audDbgToggle'); }
            };
            window.AudDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var auddbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    /* Botones del panel + botón Configurar por DELEGACIÓN en document (el
       guard __jsAuditoriaTabListo garantiza que se registran UNA sola vez;
       sobreviven a los swaps) */
    if (window.__audDbgDocHandler) { document.removeEventListener('click', window.__audDbgDocHandler); }
    window.__audDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#audDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#audDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#audDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#audDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        if (t.closest('#audBtnConfigurar')) { auddbg('clic · Configurar Catálogos Maestros (sin ruta asociada todavía: pendiente de controlador)', 'warn'); return; }
    };
    document.addEventListener('click', window.__audDbgDocHandler);
    if (window.__audDbgDblHandler) { document.removeEventListener('dblclick', window.__audDbgDblHandler); }
    window.__audDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#audDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__audDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($audDbg ?? null);
    auddbg('AUD-v1-3 activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · nodos nacen y mueren con el fragmento', 'ok');
    auddbg('init · vista=' + (SRVDBG ? SRVDBG.vista : '?') + ' · render=' + (SRVDBG ? SRVDBG.render : '?') + ' · tab estática (bitácora + catálogos maestros)', 'info');
    if (SRVDBG && SRVDBG.tablas) {
        var _tk = Object.keys(SRVDBG.tablas);
        for (var _ti = 0; _ti < _tk.length; _ti++) {
            var _st = String(SRVDBG.tablas[_tk[_ti]]);
            if (_st.indexOf('NO detectada') === 0) {
                auddbg('tabla ausente · ' + _tk[_ti] + ' → pendiente de migración (la pestaña sigue funcionando)', 'warn');
            } else {
                auddbg('tabla · ' + _tk[_ti] + ' → ' + _st, 'debug');
            }
        }
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            auddbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    function iniciar() {
        unicarDbg();
        if (window.AudDbg && window.AudDbg.ensure) { window.AudDbg.ensure(); }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>