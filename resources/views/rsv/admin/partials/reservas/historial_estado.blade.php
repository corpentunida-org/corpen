{{-- ============================================================================
     RSV · ADMIN · RESERVAS · HISTORIAL DE ESTADOS · HE-v1-DBG
     Parcial: resources/views/rsv/admin/partials/reservas/historial_estado.blade.php

     · Base v1 (conservada ÍNTEGRA): tabla-timeline de movimientos de estado
       con dataset JSON servido por Blade (script application/json + la
       directiva raw de Blade), búsqueda instantánea sobre código/usuario/
       comentario/estados/inmueble, paginación local de 15, exportación XLS
       de TODO lo filtrado y badge "Append-Only" (regla de negocio del
       controlador: el historial no se edita ni se elimina).
     · PANEL DE DEBUG (HE-v1-DBG · diseño TIP-v1.3 aprobado · canónico
       BLQ-v1-2): pestaña terminal "DBG" de 16px fija SIEMPRE en la esquina
       inferior-derecha (right:0 · bottom:0, radius 3px solo superior-
       izquierdo) con LED de estado (verde = ok · rojo pulsante + conteo =
       errores) y panel cajón de 380px alineado al mismo borde. La regla
       exacta de distancias está documentada como "SPEC DE DISTANCIAS"
       dentro del CSS, y el panel esquiva a los toggles hermanos de las
       otras vistas midiendo píxeles reales (getBoundingClientRect ·
       bottom = max(20, vecino + 4)) — incluido el toggle ST de la vista
       hermana de Estados.
       Exclusivo de este fragmento: el toggle y el panel son HERMANOS de
       #heRoot (viven y mueren con la vista) y unicarDbg() deduplica dejando
       UN solo toggle/panel por página. Nada se cuelga de document.body: el
       respaldo re-crea DENTRO de #heRoot.
       Telemetría que reporta: contexto (global/inmueble), movimientos en BD
       vs cargados en cliente (tope 2000), truncado del dataset, reservas y
       usuarios únicos, rango temporal, regla Append-Only, búsqueda
       instantánea, cambios de página, exportación XLS (volumen, filtro y
       archivo generado) y errores capturados por el servidor.
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

{{-- HE-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano del root: vive y muere con la vista) --}}
<div id="heDbgToggle" title="Debug Historial de Estados (HE-v1-DBG)"><span class="he-dbg-led" id="heDbgLed"></span>DBG<span class="he-dbg-badge" id="heDbgBadge">0</span></div>
<div id="heDbgPanel" role="dialog" aria-label="Panel de debug del Historial de Estados">
    <div class="he-dbg-header" id="heDbgHeader">
        <span>DEBUG · Historial de Estados · HE-v1-DBG</span>
        <span class="he-dbg-controls">
            <button type="button" class="he-dbg-btn" id="heDbgCopy">Copiar</button>
            <button type="button" class="he-dbg-btn" id="heDbgClear">Limpiar</button>
            <button type="button" class="he-dbg-btn" id="heDbgClose">✕</button>
        </span>
    </div>
    <div id="heDbgBody"></div>
</div>

<div class="reservas-soft p-3 bg-white rounded-3 border" id="heRoot"
     data-modo="{{ (isset($inmueble) && $inmueble) || (!empty($inmuebleId) && is_numeric($inmuebleId)) || (request('inmueble_id') && is_numeric(request('inmueble_id'))) ? 'single' : 'global' }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 220px; flex: 1 1 240px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .badge-lock { display: inline-flex; align-items: center; gap: 3px; font-size: 7px; font-weight: 600; color: #9aa5b1; background: #fafbfd; border: 1px solid #eef2f6; border-radius: 8px; padding: 0 7px; line-height: 14px; }
    .reservas-soft .fl-arrow { color: #c3ccd6; font-size: 8px; margin: 0 3px; }
    .reservas-soft .rv-pagination { border-top: 1px solid var(--grid); margin-top: 8px; padding-top: 7px; }

    /* ═════════ SPEC DE DISTANCIAS · HE-v1-DBG (tab terminal TIP-v1.3) ═════════
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
       UNICIDAD  · toggle y panel son HERMANOS de #heRoot (viven y mueren con
                   la vista) y unicarDbg() deja UN solo toggle/panel por
                   página. El respaldo re-crea DENTRO de #heRoot. */
    #heDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    #heDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
    .he-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .he-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: heDbgBlink 1s steps(2,start) infinite; }
    @keyframes heDbgBlink { 50% { opacity: .35; } }
    .he-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
    #heDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .he-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
    .he-dbg-controls { display: flex; gap: 4px; }
    .he-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
    .he-dbg-btn:hover { background: #475569; }
    #heDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
    .he-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
    .he-dbg-time { color: #64748b; margin-right: 4px; }
    .he-dbg-info { color: #93c5fd; } .he-dbg-ok { color: #86efac; }
    .he-dbg-warn { color: #fde68a; } .he-dbg-err { color: #fca5a5; }
    .he-dbg-net { color: #c4b5fd; } .he-dbg-debug { color: #94a3b8; }
</style>

@php
    /*
    ══════════════════════════════════════════════════════════════
    CONTEXTO GLOBAL/INDIVIDUAL
    · El historial se consulta en GLOBAL (todo el sistema) o por
      INMUEBLE (filtrando por la reserva que pertenece al inmueble).
    · Regla de negocio: Append-Only → este parcial NUNCA edita ni
      elimina registros; solo lee y exporta.
    · Tope defensivo de 2000 movimientos: el dataset viaja completo
      al cliente; el DBG reporta si quedó truncado.
    ══════════════════════════════════════════════════════════════
    */
    $heDbgErrores = [];

    $heInmId = null;
    if (isset($inmueble) && $inmueble) { $heInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble; }
    elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) { $heInmId = $inmuebleId; }
    elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) { $heInmId = request('inmueble_id'); }
    $heGlobal = empty($heInmId);

    $heCtxName = 'Global';
    if (!$heGlobal) {
        $heCtxName = (is_object($inmueble ?? null) ? ($inmueble->name ?? null) : null) ?? ('Inmueble #' . $heInmId);
    }

    $heItems = collect(); $heJson = '[]'; $heTotal = 0;

    try {
        if (class_exists(\App\Models\Rsv\HistorialEstado::class)) {
            $q = \App\Models\Rsv\HistorialEstado::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'estadoAnterior:id,name,color_hex', 'estadoNuevo:id,name,color_hex', 'user:id,name']);

            if (!$heGlobal && $heInmId) {
                $q->whereHas('reserva', function ($qq) use ($heInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $heInmId);
                });
            }

            $heTotal = (clone $q)->count();

            $heItems = $q->orderBy('created_at', 'desc')->limit(2000)->get()->map(function ($h) {
                return [
                    'fec'   => $h->created_at ? $h->created_at->format('d/m/y H:i') : '—',
                    'cod'   => (string) (optional($h->reserva)->codigo_reserva ?? '—'),
                    'inm'   => (string) (optional(optional($h->reserva)->inmueble)->name ?? 'N/D'),
                    'ant'   => (string) (optional($h->estadoAnterior)->name ?? '—'),
                    'nvo'   => (string) (optional($h->estadoNuevo)->name ?? '—'),
                    'nvoC'  => (string) (optional($h->estadoNuevo)->color_hex ?: '#a3cfb6'),
                    'usr'   => (string) (optional($h->user)->name ?? 'Sistema'),
                    'com'   => (string) ($h->comentario ?? ''),
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('he dataset: '.$e->getMessage());
        $heDbgErrores[] = 'dataset: ' . $e->getMessage();
        $heItems = collect();
    }

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $heJson = json_encode($heItems->all(), $jhFlags); if (!is_string($heJson)) { $heJson = '[]'; }

    /* ═══ TELEMETRÍA PARA EL COLECTOR HE-v1-DBG ═══ */
    $heCargados = $heItems->count();
    $heReservasUnicas = $heItems->pluck('cod')->reject(function ($c) { return $c === '—' || $c === ''; })->unique()->count();
    $heUsuariosUnicos = $heItems->pluck('usr')->unique()->count();
    $heRango = $heCargados
        ? (($heItems->last()['fec'] ?? '—') . ' → ' . ($heItems->first()['fec'] ?? '—'))
        : '—';

    /* ═══ Colector del panel de debug (HE-v1-DBG) · via al panel vía la directiva json de Blade ═══ */
    $heDbg = [
        'version'     => 'HE-v1-DBG',
        'vista'       => 'partials/reservas/historial_estado.blade.php',
        'render'      => now()->format('H:i:s'),
        'contexto'    => $heGlobal ? 'global' : ('inmueble · ' . $heCtxName),
        'movimientos' => $heTotal,
        'cargados'    => $heCargados,
        'truncado'    => $heTotal > $heCargados,
        'reservas'    => $heReservasUnicas,
        'usuarios'    => $heUsuariosUnicos,
        'rango'       => $heRango,
        'append_only' => 'sí',
        'errores'     => $heDbgErrores,
    ];
@endphp

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-clock-history me-1" style="font-size: 8px; color: #5d8a70;"></i>
            Historial de Estados
            <span class="badge-lock ms-1" title="Registro inmutable (append-only)"><i class="bi bi-lock-fill"></i> Append-Only</span>
            <span class="badge rounded-pill ms-1" id="heChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">HE-v1-DBG</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><span id="heBadge"><b>{{ $heTotal }}</b> movimientos</span></span>
            <button type="button" id="heExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Excel de TODO lo filtrado">
                <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
            </button>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
        <div class="d-flex align-items-center" style="gap: 6px;">
            <i class="bi bi-search" style="font-size: 8.5px; color: #c3ccd6;"></i>
            <input type="text" id="heSearch" class="xls-search" placeholder="Código de reserva, usuario o comentario… (instantáneo)" autocomplete="off">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 700px;">
            <thead>
                <tr><th>Fecha</th><th>Reserva</th><th>Inmueble</th><th>Transición</th><th>Usuario</th><th>Comentario</th></tr>
            </thead>
            <tbody id="heTbody">
                <tr><td colspan="6" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr>
            </tbody>
        </table>
    </div>

    <div class="rv-pagination d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
        <span class="count-badge" id="hePgInfo" style="background: #fcfdfe;"><i class="bi bi-layers" style="font-size: 7.5px; color: #b3bcc7;"></i> —</span>
        <div class="d-flex align-items-center" style="gap: 5px;">
            <button type="button" id="hePrev" class="btn-mini" disabled>‹ Anterior</button>
            <span id="hePgLbl" style="font-size: 8.5px; color: #b3bcc7; font-weight: 600;">Página 1 de 1</span>
            <button type="button" id="heNext" class="btn-mini" disabled>Siguiente ›</button>
        </div>
    </div>

</div>

<script type="application/json" id="heDataset">{!! $heJson !!}</script>

{{-- SCRIPT · render local + búsqueda + paginación + export XLS + PANEL DE DEBUG (HE-v1-DBG) --}}
<script>
(function () {
    'use strict';

    // Evita doble binding de eventos si el parcial llegara a re-inyectarse vía AJAX.
    if (window.__hePanelInit) return;
    window.__hePanelInit = true;

    /* ═══ 0) PANEL DE DEBUG LOCAL HE-v1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de esta vista: los nodos son HTML del parcial (hermanos de
       #heRoot) y el respaldo solo re-crea DENTRO de #heRoot.
       Nada se cuelga de document.body. */
    var DBG = (window.HeDbg && window.HeDbg.alive && window.HeDbg.alive())
        ? window.HeDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven como hermanos de #heRoot), se re-crean DENTRO del root.
                   Cero document.body (exclusividad total). */
                var rootDbg = document.getElementById('heRoot');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('heDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'heDbgToggle';
                    t.title = 'Debug Historial de Estados (HE-v1-DBG)';
                    t.innerHTML = '<span class="he-dbg-led" id="heDbgLed"></span>DBG<span class="he-dbg-badge" id="heDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('heDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'heDbgPanel';
                    p.innerHTML = '<div class="he-dbg-header" id="heDbgHeader">' +
                        '<span>DEBUG · Historial de Estados · HE-v1-DBG</span>' +
                        '<span class="he-dbg-controls">' +
                        '<button type="button" class="he-dbg-btn" id="heDbgCopy">Copiar</button>' +
                        '<button type="button" class="he-dbg-btn" id="heDbgClear">Limpiar</button>' +
                        '<button type="button" class="he-dbg-btn" id="heDbgClose">✕</button>' +
                        '</span></div><div id="heDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* HE-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'stDbgToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('heDbgToggle'), p = $('heDbgPanel');
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
                    var b = $('heDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'he-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="he-dbg-time">[' + hora() + ']</span>' +
                        '<span class="he-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('heDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('heDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('heDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('heDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('heDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('heDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('heDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('heDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('heDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG historial HE-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('heDbgToggle'); }
            };
            window.HeDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var hedbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function unicarDbg() {
        /* HE-v1-DBG: el parcial puede renderizarse 2+ veces; el HTML de cada
           copia trae sus propios nodos DBG → se conservan los del primero y
           se eliminan los duplicados (un solo toggle/panel por página). */
        var ts = document.querySelectorAll('#heDbgToggle');
        for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
        var ps = document.querySelectorAll('#heDbgPanel');
        for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
    }
    unicarDbg();

    /* Botones del panel por DELEGACIÓN en document (el guard __hePanelInit
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__heDbgDocHandler) { document.removeEventListener('click', window.__heDbgDocHandler); }
    window.__heDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#heDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#heDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#heDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#heDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__heDbgDocHandler);
    if (window.__heDbgDblHandler) { document.removeEventListener('dblclick', window.__heDbgDblHandler); }
    window.__heDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#heDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__heDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($heDbg ?? null);
    hedbg('HE-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con la vista)', 'ok');
    hedbg('init · contexto=' + (SRVDBG ? SRVDBG.contexto : '?') + ' · movimientos BD=' + (SRVDBG ? SRVDBG.movimientos : '?') +
        ' · cargados=' + (SRVDBG ? SRVDBG.cargados : '?') + ' · reservas únicas=' + (SRVDBG ? SRVDBG.reservas : '?') +
        ' · usuarios=' + (SRVDBG ? SRVDBG.usuarios : '?'), 'info');
    if (SRVDBG && SRVDBG.rango && SRVDBG.rango !== '—') {
        hedbg('rango temporal del historial · ' + SRVDBG.rango, 'debug');
    }
    if (SRVDBG && SRVDBG.truncado) {
        hedbg('dataset truncado · se cargan los ' + SRVDBG.cargados + ' movimientos más recientes de ' + SRVDBG.movimientos + ' (el resto no entra en el cliente)', 'warn');
    }
    if (SRVDBG && SRVDBG.movimientos === 0) {
        hedbg('dataset vacío · sin movimientos de estado registrados todavía', 'warn');
    }
    if (SRVDBG && SRVDBG.append_only === 'sí') {
        hedbg('regla de negocio · registro Append-Only: sin edición ni eliminación desde el controlador', 'info');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            hedbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    /* ═══ 1) DATASET · JSON servido por Blade (script application/json) ═══ */
    var DS = [];
    try { DS = JSON.parse((document.getElementById('heDataset') || {}).textContent || '[]'); } catch (e) { DS = []; hedbg('dataset JSON ilegible · fallback a lista vacía · ' + (e && e.message ? e.message : ''), 'err'); }

    var PAGE_SIZE = 15, page = 1, q = '';
    var tbody = document.getElementById('heTbody');

    function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function render() {
        var t = q.toLowerCase().trim();
        var res = DS.filter(function (r) {
            if (!t) return true;
            var blob = (r.cod + ' ' + r.usr + ' ' + r.com + ' ' + r.ant + ' ' + r.nvo + ' ' + r.inm).toLowerCase();
            return blob.indexOf(t) !== -1;
        });

        var pages = Math.max(1, Math.ceil(res.length / PAGE_SIZE));
        if (page > pages) page = pages;
        var ini = (page - 1) * PAGE_SIZE;
        var slice = res.slice(ini, ini + PAGE_SIZE);

        var h = '';
        if (!res.length) {
            h = '<tr><td colspan="6" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
              + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados con el filtro.' : '<i class="bi bi-clock-history me-1"></i> Sin movimientos de estado registrados aún.')
              + '</td></tr>';
        } else {
            slice.forEach(function (r, i) {
                h += '<tr>'
                   + '<td class="sub">' + esc(r.fec) + '</td>'
                   + '<td class="strong">#' + esc(r.cod) + '</td>'
                   + '<td>' + esc(r.inm) + '</td>'
                   + '<td><span class="sub">' + esc(r.ant) + '</span>'
                   + '<span class="fl-arrow">→</span>'
                   + '<span class="strong" style="color:' + esc(r.nvoC) + ';">' + esc(r.nvo) + '</span></td>'
                   + '<td>' + esc(r.usr) + '</td>'
                   + '<td class="sub" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;" title="' + esc(r.com) + '">' + esc(r.com || '—') + '</td>'
                   + '</tr>';
            });
        }
        tbody.innerHTML = h;

        var badge = document.getElementById('heBadge');
        if (badge) {
            badge.innerHTML = t
                ? 'Mostrando <b>' + res.length + '</b> de ' + DS.length
                : '<b>' + DS.length + '</b> movimientos';
        }
        var info = document.getElementById('hePgInfo');
        var lbl = document.getElementById('hePgLbl');
        var bp = document.getElementById('hePrev');
        var bn = document.getElementById('heNext');
        if (info) info.innerHTML = '<i class="bi bi-layers" style="font-size:7.5px;color:#b3bcc7;"></i> ' + (res.length ? (ini+1)+'–'+Math.min(ini+PAGE_SIZE,res.length)+' de <b>'+res.length+'</b>' : '—');
        if (lbl) lbl.textContent = 'Página ' + page + ' de ' + pages;
        if (bp) bp.disabled = page <= 1;
        if (bn) bn.disabled = page >= pages;

        return { n: res.length, pages: pages, ini: ini };
    }

    var search = document.getElementById('heSearch');
    if (search) search.addEventListener('input', function () {
        q = this.value; page = 1;
        var out = render();
        hedbg('búsqueda · "' + q.toLowerCase().trim() + '" · ' + out.n + ' de ' + DS.length + ' movimiento(s) visible(s)', 'debug');
    });

    var bp = document.getElementById('hePrev'), bn = document.getElementById('heNext');
    if (bp) bp.addEventListener('click', function () {
        if (page > 1) { page--; var o = render(); hedbg('paginación · página ' + page + ' de ' + o.pages + ' · viendo ' + (o.ini + 1) + '–' + Math.min(o.ini + PAGE_SIZE, o.n) + ' de ' + o.n, 'debug'); }
    });
    if (bn) bn.addEventListener('click', function () {
        page++; var o2 = render(); hedbg('paginación · página ' + page + ' de ' + o2.pages + ' · viendo ' + (o2.ini + 1) + '–' + Math.min(o2.ini + PAGE_SIZE, o2.n) + ' de ' + o2.n, 'debug');
    });

    var ex = document.getElementById('heExport');
    if (ex) ex.addEventListener('click', function (e) {
        e.stopPropagation();
        var t = q.toLowerCase().trim();
        var res = DS.filter(function (r) {
            if (!t) return true;
            return (r.cod + ' ' + r.usr + ' ' + r.com + ' ' + r.ant + ' ' + r.nvo + ' ' + r.inm).toLowerCase().indexOf(t) !== -1;
        });
        if (!res.length) { alert('Sin movimientos para exportar.'); hedbg('exportación cancelada · sin movimientos para exportar' + (t ? ' (filtro "' + t + '")' : ''), 'warn'); return; }
        hedbg('exportando XLS · ' + res.length + ' movimiento(s)' + (t ? ' · filtro "' + t + '"' : ' · sin filtro'), 'net');
        var rows = '';
        res.forEach(function (r, i) {
            rows += '<tr><td>' + (i+1) + '</td><td>' + esc(r.fec) + '</td><td>#' + esc(r.cod) + '</td><td>' + esc(r.inm) + '</td><td>' + esc(r.ant) + ' → ' + esc(r.nvo) + '</td><td>' + esc(r.usr) + '</td><td>' + esc(r.com) + '</td></tr>';
        });
        var html = '<html><head><meta charset="UTF-8"><style>table{border-collapse:collapse;font-family:Calibri;font-size:11px}td,th{border:1px solid #d0d7de;padding:4px 8px}th{background:#f0f7f2;color:#2e5c43}</style></head><body><table>'
            + '<tr><td colspan="7" style="font-weight:bold;font-size:14px;">HISTORIAL DE ESTADOS</td></tr>'
            + '<tr><td colspan="7" style="color:#666;font-size:10px;">' + new Date().toLocaleString() + ' · ' + res.length + ' movimiento(s)</td></tr>'
            + '<tr><th>#</th><th>Fecha</th><th>Reserva</th><th>Inmueble</th><th>Transición</th><th>Usuario</th><th>Comentario</th></tr>'
            + rows + '</table></body></html>';
        try {
            var fname = 'HistorialEstados_' + Date.now() + '.xls';
            var blob = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url; a.download = fname; a.style.display = 'none';
            a.click();
            setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            hedbg('export OK · ' + fname + ' · ' + res.length + ' fila(s) de datos + cabecera', 'ok');
        } catch (err) {
            hedbg('export falló · ' + (err && err.message ? err.message : 'error desconocido'), 'err');
        }
    });

    var out0 = render();
    hedbg('tabla renderizada · ' + DS.length + ' movimiento(s) en cliente · ' + out0.n + ' visible(s) · página 1 de ' + out0.pages + ' · ' + PAGE_SIZE + ' por página', 'ok');
})();
</script>
