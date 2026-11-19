{{-- resources/views/rsv/admin/partials/tab-soportes.blade.php · SOP-v1.1-DBG --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SOPORTES · CENTRO DE ATENCIÓN ESCRITA (Sin WhatsApp ni llamadas)         --}}
{{-- Misma estructura visual de CAL-v3.5-DBG + Panel de debug local TIP-v1.3   --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@php
    $__sopDbgInmId = null;
    if (isset($inmueble) && $inmueble) {
        $__sopDbgInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $__sopDbgInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $__sopDbgInmId = (int) request('inmueble_id');
    }
    $sopGlobal = empty($__sopDbgInmId);

    $listaInmuebles = $listaInmuebles ?? $inmuebles ?? collect();
    if ((!$listaInmuebles || count($listaInmuebles) === 0) && class_exists(\App\Models\Rsv\CatalogoInmueble::class)) {
        try {
            $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::select('id', 'name', 'city')->orderBy('name', 'asc')->get();
        } catch (\Throwable $e) {
            $listaInmuebles = collect();
        }
    }

    $sopTotalProps = method_exists($listaInmuebles, 'total') ? $listaInmuebles->total() : count($listaInmuebles);
    $sopColSafe    = collect($soportes ?? []);

    $sopCntTotal   = $sopColSafe->count();
    $sopCntSinResp = $sopColSafe->filter(fn ($m) => empty($m->respondido))->count();
    $sopCntResp    = max(0, $sopCntTotal - $sopCntSinResp);

    $sopDbg = [
        'marca'         => 'SOP-v1.1-DBG',
        'vista'         => 'rsv.admin.partials.tab-soportes',
        'url'           => url()->full(),
        'peticion'      => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'global'        => $sopGlobal,
        'inmuebleId'    => $__sopDbgInmId,
        'inmuebles'     => $sopTotalProps,
        'total'         => $sopCntTotal,
        'sin_responder' => $sopCntSinResp,
        'respondidos'   => $sopCntResp,
        'errores'       => [],
    ];
@endphp

{{-- SOP-v1.1-DBG · Tab DBG terminal + LED + Panel LOCAL (fuera del root: sobrevive a los swaps) --}}
<div id="sopDbgToggle" title="Debug Soportes (SOP-v1.1-DBG)">
    <span class="sop-dbg-led" id="sopDbgLed"></span>DBG<span class="sop-dbg-badge" id="sopDbgBadge">0</span>
</div>
<div id="sopDbgPanel" role="dialog" aria-label="Panel de debug de Soportes">
    <div class="sop-dbg-header" id="sopDbgHeader">
        <span>DEBUG · Soportes · SOP-v1.1-DBG</span>
        <span class="sop-dbg-controls">
            <button type="button" class="sop-dbg-btn" id="sopDbgCopy">Copiar</button>
            <button type="button" class="sop-dbg-btn" id="sopDbgClear">Limpiar</button>
            <button type="button" class="sop-dbg-btn" id="sopDbgClose">✕</button>
        </span>
    </div>
    <div id="sopDbgBody"></div>
</div>

<div class="p-3 bg-white rounded-3 border" id="sopRoot" style="border-color: #f1f5f9 !important;">

    {{-- 1. HERO BANNER SOPORTES (MISMO ESTILO QUE CAL-v3.5-DBG / FIN-v2.0-DBG) --}}
    <div class="sop-hero-pro">
        <i class="bi bi-headset hero-watermark"></i>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 position-relative" style="z-index: 2;">
            <div style="max-width: 660px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success text-white fw-bold" style="font-size: 7.5px; letter-spacing: .5px; text-transform: uppercase;">
                        <i class="bi bi-chat-left-text me-1"></i> Ecosistema de Soportes RSV
                    </span>
                    <span class="badge rounded-pill" style="font-size: 7px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; font-family: ui-monospace, monospace;">
                        SOP-v1.1-DBG
                    </span>
                </div>
                <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">
                    Centro de Atención Escrita · Quiénes Escribieron y Sin Responder
                </h6>
                <p class="mb-2 text-light opacity-75" style="font-size: 9.5px;">
                    Revisa en un solo lugar quiénes han escrito, identifica de inmediato los mensajes sin responder y gestiona respuestas internas sin salir a WhatsApp ni llamadas.
                </p>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="sop-hero-btn-primary" id="sopBtnGoSinResponder">
                        <i class="bi bi-envelope-exclamation"></i> Ver Sin Responder ({{ $sopCntSinResp }})
                    </button>
                    <button type="button" class="sop-hero-btn-outline" id="sopBtnGoRespondidos">
                        <i class="bi bi-check2-all"></i> Ver Respondidos ({{ $sopCntResp }})
                    </button>
                    <button type="button" class="sop-hero-btn-outline" id="sopBtnGoTodos">
                        <i class="bi bi-chat-left-text"></i> Todos Escritos ({{ $sopCntTotal }})
                    </button>
                </div>
            </div>

            <div class="text-md-end">
                <div class="lbl text-light opacity-50">Mensajes Sin Responder</div>
                <div class="fw-bold text-warning num" style="font-size: 19px; line-height: 1.15;" id="sopHeroSinResp">
                    {{ $sopCntSinResp }} <span style="font-size: 9px; color: #94a3b8;">PENDIENTES</span>
                </div>
                <div class="sub text-light opacity-75" style="font-size: 8px;">
                    {{ $sopGlobal ? ($sopTotalProps . ' propiedades en alcance') : ('Propiedad #' . $__sopDbgInmId . ' seleccionada') }}
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TARJETAS KPI DE SOPORTES --}}
    <div class="sop-kpi-grid">
        <div class="sop-kpi-card js-sop-kpi-jump" data-sub="#sop-sub-sin-responder" data-single-sub="#single-sop-sub-sin-responder" title="Mensajes escritos que aún no tienen respuesta">
            <div>
                <div class="lbl">Sin Responder</div>
                <div class="val num" style="font-size: 13px; color: #b91c1c;">{{ $sopCntSinResp }}</div>
                <div class="sub">requieren respuesta escrita</div>
            </div>
            <div class="sop-kpi-icon" style="color:#b91c1c; background:#fef2f2; border-color:#fecaca;">
                <i class="bi bi-envelope-exclamation"></i>
            </div>
        </div>

        <div class="sop-kpi-card js-sop-kpi-jump" data-sub="#sop-sub-respondidos" data-single-sub="#single-sop-sub-respondidos" title="Solicitudes ya respondidas en la plataforma">
            <div>
                <div class="lbl">Respondidos</div>
                <div class="val num" style="font-size: 13px; color: #15803d;">{{ $sopCntResp }}</div>
                <div class="sub">atendidos en bandeja</div>
            </div>
            <div class="sop-kpi-icon" style="color:#15803d; background:#f0fdf4; border-color:#bbf7d0;">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>

        <div class="sop-kpi-card js-sop-kpi-jump" data-sub="#sop-sub-todos" data-single-sub="#single-sop-sub-todos" title="Total de personas/mensajes escritos registrados">
            <div>
                <div class="lbl">Total Escritos</div>
                <div class="val num" style="font-size: 13px; color: #1d4ed8;">{{ $sopCntTotal }}</div>
                <div class="sub">hilos registrados</div>
            </div>
            <div class="sop-kpi-icon" style="color:#1d4ed8; background:#eff6ff; border-color:#bfdbfe;">
                <i class="bi bi-chat-dots"></i>
            </div>
        </div>

        <div class="sop-kpi-card" title="Canal de atención centralizado sin WhatsApp ni llamadas">
            <div>
                <div class="lbl">Canal Directo</div>
                <div class="val" style="font-size: 12px; color: #3d7a5c;">Bandeja RSV</div>
                <div class="sub">100% escrito en portal</div>
            </div>
            <div class="sop-kpi-icon" style="color:#3d7a5c; background:#f0f7f2; border-color:#d5e7dc;">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
    </div>

    {{-- 3. CINTA SUPERIOR: leyenda + filtro de inmueble --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-2 py-1.5 mb-2"
         style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 6px 6px 0 0;">

        <div class="d-flex flex-wrap align-items-center gap-1">
            <span class="me-1" style="font-size: 7.5px; letter-spacing: .05em; color: #b3bcc7; font-weight: 700;">
                <i class="bi bi-headset me-1 text-success" style="font-size: 8px;"></i>ESTADO:
            </span>
            <span class="sop-chip"><span class="ldot" style="background:#ef4444;"></span> Sin responder</span>
            <span class="sop-chip"><span class="ldot" style="background:#10b981;"></span> Respondido</span>
            <span class="sop-chip" style="border-color:#d5e7dc; color:#3d7a5c; font-weight:700;">SOP-v1.1-DBG</span>
        </div>

        <div class="d-flex align-items-center gap-1">
            <i class="bi bi-building text-success" style="font-size: 9px;"></i>
            <select id="filtro_inmueble_sop" class="sop-select" title="Filtrar soportes por inmueble">
                <option value="">Todos los inmuebles ({{ $sopTotalProps }})</option>
                @forelse(($listaInmuebles ?? collect()) as $inm)
                    <option value="{{ $inm->id }}" @selected((string) $__sopDbgInmId === (string) $inm->id)>{{ $inm->name }}</option>
                @empty
                    <option value="" disabled>Sin inmuebles cargados</option>
                @endforelse
            </select>
        </div>

    </div>

    {{-- 4. RESUMEN RÁPIDO DE QUIÉNES ESCRIBIERON --}}
    <div class="p-2 bg-white border rounded-bottom" style="border-color: #f1f5f9 !important;">
        @if($sopCntTotal > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 8.5px;">
                    <thead style="background:#fafbfd; color:#94a3b8; font-size:8px; text-transform:uppercase;">
                        <tr>
                            <th>Quien escribió</th>
                            <th>Asunto / Mensaje</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sopColSafe->take(8) as $msg)
                            @php $estaResp = !empty($msg->respondido); @endphp
                            <tr>
                                <td class="fw-semibold text-dark">{{ $msg->remitente_nombre ?? $msg->autor ?? $msg->nombre ?? 'Usuario' }}</td>
                                <td class="text-muted text-truncate" style="max-width:260px;">{{ $msg->asunto ?? $msg->mensaje ?? $msg->cuerpo ?? '—' }}</td>
                                <td class="text-center">
                                    @if($estaResp)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:7px;">Respondido</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:7px;">Sin responder</span>
                                    @endif
                                </td>
                                <td class="text-end text-muted" style="font-size:7.5px;">{{ optional($msg->created_at)->format('d/m H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted" style="font-size: 8.5px;">
                <i class="bi bi-chat-left-text me-1"></i> No hay mensajes escritos registrados en este alcance.
            </div>
        @endif
    </div>

</div>

<style>
    #sopRoot {
        --grid:  #f1f5f9;
        --grid-2:#e9eef4;
        --head:  #fafbfd;
        --ink:   #64748b;
        --ink-2: #334155;
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
    #sopRoot .lbl { font-size: 7.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); }
    #sopRoot .val { font-size: 10.5px; color: var(--ink-2); font-weight: 700; letter-spacing: -0.2px; }
    #sopRoot .sub { font-size: 8px; line-height: 1.35; color: var(--ink-3); }
    #sopRoot .num { font-variant-numeric: tabular-nums; }

    #sopRoot .sop-hero-pro {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 11px;
        padding: 20px 24px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
        margin-bottom: 12px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
    }
    #sopRoot .sop-hero-pro .hero-watermark {
        position: absolute; right: -2%; bottom: -35%; font-size: 12rem;
        opacity: 0.04; color: #ffffff; pointer-events: none; transform: rotate(-10deg);
    }
    #sopRoot .sop-hero-btn-primary {
        background: #10b981; color: #0f172a; border: none;
        font-size: 9px; font-weight: 800; padding: 7px 14px; border-radius: 6px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.18s ease;
    }
    #sopRoot .sop-hero-btn-primary:hover { background: #34d399; transform: translateY(-1px); }
    #sopRoot .sop-hero-btn-outline {
        background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.2);
        font-size: 9px; font-weight: 700; padding: 7px 13px; border-radius: 6px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.18s ease;
    }
    #sopRoot .sop-hero-btn-outline:hover { background: rgba(255,255,255,0.16); border-color: #fff; color: #fff; }

    #sopRoot .sop-kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px;
    }
    @media (max-width: 767px) { #sopRoot .sop-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    #sopRoot .sop-kpi-card {
        background: #fff; border: 1px solid var(--grid-2); border-radius: 9px;
        padding: 10px 13px; display: flex; align-items: center; justify-content: space-between;
        gap: 10px; cursor: pointer; transition: all 0.16s ease;
    }
    #sopRoot .sop-kpi-card:hover { border-color: var(--accent-bd); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.04); }
    #sopRoot .sop-kpi-icon {
        width: 34px; height: 34px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; border: 1px solid;
    }

    #sopRoot .sop-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 8px; border-radius: 9px;
        font-size: 8px; font-weight: 500;
        border: 1px solid var(--grid); background: #fcfdfe; color: var(--ink-3);
    }
    #sopRoot .ldot {
        width: 5px; height: 5px; border-radius: 50%;
        display: inline-block; opacity: .85;
    }
    #sopRoot .sop-select {
        border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2);
        font-family: inherit; font-size: 9px; font-weight: 600;
        padding: 2px 8px; border-radius: 4px; cursor: pointer;
        box-shadow: none !important; outline: none;
    }

    #sopDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; }
    #sopDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
    .sop-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
    .sop-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: sopDbgBlink 1s steps(2,start) infinite; }
    @keyframes sopDbgBlink { 50% { opacity: .35; } }
    .sop-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
    #sopDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    .sop-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
    .sop-dbg-controls { display: flex; gap: 4px; }
    .sop-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
    .sop-dbg-btn:hover { background: #475569; }
    #sopDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
    .sop-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
    .sop-dbg-time { color: #64748b; margin-right: 4px; }
    .sop-dbg-info { color: #93c5fd; } .sop-dbg-ok { color: #86efac; }
    .sop-dbg-warn { color: #fde68a; } .sop-dbg-err { color: #fca5a5; }
    .sop-dbg-net { color: #c4b5fd; } .sop-dbg-debug { color: #94a3b8; }
</style>

<script>
    (function () {
        var SRVDBG = @json($sopDbg);

        var DBG = (window.SopDbg && window.SopDbg.alive && window.SopDbg.alive())
            ? window.SopDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarNodos() {
                    if (!$('sopDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'sopDbgToggle';
                        t.title = 'Debug Soportes (SOP-v1.1-DBG)';
                        t.innerHTML = '<span class="sop-dbg-led" id="sopDbgLed"></span>DBG<span class="sop-dbg-badge" id="sopDbgBadge">0</span>';
                        document.body.appendChild(t);
                    }
                    if (!$('sopDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'sopDbgPanel';
                        p.innerHTML = '<div class="sop-dbg-header" id="sopDbgHeader">' +
                            '<span>DEBUG · Soportes · SOP-v1.1-DBG</span>' +
                            '<span class="sop-dbg-controls">' +
                            '<button type="button" class="sop-dbg-btn" id="sopDbgCopy">Copiar</button>' +
                            '<button type="button" class="sop-dbg-btn" id="sopDbgClear">Limpiar</button>' +
                            '<button type="button" class="sop-dbg-btn" id="sopDbgClose">✕</button>' +
                            '</span></div><div id="sopDbgBody"></div>';
                        document.body.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) continue;
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) continue;
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                    }
                    var t = $('sopDbgToggle'), p = $('sopDbgPanel');
                    if (t) t.style.bottom = '0px';
                    if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
                }
                function hora() {
                    var d = new Date();
                    function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                    return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
                }
                var api = {
                    log: function (txt, nivel) {
                        var b = $('sopDbgBody'); if (!b) return;
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'sop-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="sop-dbg-time">[' + hora() + ']</span><span class="sop-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) b.removeChild(b.firstChild);
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('sopDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('sopDbgLed'); if (led) led.classList.add('err');
                        }
                    },
                    open: function () {
                        var p = $('sopDbgPanel'); if (!p) return;
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('sopDbgBadge'); if (bd) bd.style.display = 'none';
                        var led = $('sopDbgLed'); if (led) led.classList.remove('err');
                        numErr = 0;
                    },
                    close: function () { var p = $('sopDbgPanel'); if (p) p.style.display = 'none'; },
                    togglePanel: function () { var p = $('sopDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                    clear: function () { var b = $('sopDbgBody'); if (b) b.innerHTML = ''; },
                    alive: function () { return !!$('sopDbgToggle'); }
                };
                asegurarNodos();
                $('sopDbgToggle').addEventListener('click', api.togglePanel);
                $('sopDbgClose').addEventListener('click', function (e2) { e2.stopPropagation(); api.close(); });
                $('sopDbgClear').addEventListener('click', function (e2) { e2.stopPropagation(); api.clear(); });
                $('sopDbgCopy').addEventListener('click', function (e2) {
                    e2.stopPropagation();
                    var b = $('sopDbgBody'); if (!b) return;
                    var txt = '=== DEBUG tab-soportes SOP-v1.1-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () { api.log('log copiado al portapapeles', 'ok'); });
                    }
                });
                $('sopDbgHeader').addEventListener('dblclick', api.close);
                window.SopDbg = api;
                return api;
            })();

        if (!DBG) return;
        DBG.log('SOP-v1.1-DBG activo · sin responder=' + (SRVDBG ? SRVDBG.sin_responder : 0) + ' · total=' + (SRVDBG ? SRVDBG.total : 0), 'ok');

        document.addEventListener('DOMContentLoaded', function () {
            var irASub = function (globalSel, singleSel) {
                var esGlobal = !!document.getElementById('globalAdminTabs');
                var sel = esGlobal ? globalSel : singleSel;
                var btn = document.querySelector('[data-bs-target="' + sel + '"]');
                if (btn) btn.click();
            };

            var bSin = document.getElementById('sopBtnGoSinResponder');
            var bRes = document.getElementById('sopBtnGoRespondidos');
            var bTod = document.getElementById('sopBtnGoTodos');
            if (bSin) bSin.addEventListener('click', function () { irASub('#sop-sub-sin-responder', '#single-sop-sub-sin-responder'); });
            if (bRes) bRes.addEventListener('click', function () { irASub('#sop-sub-respondidos', '#single-sop-sub-respondidos'); });
            if (bTod) bTod.addEventListener('click', function () { irASub('#sop-sub-todos', '#single-sop-sub-todos'); });

            document.querySelectorAll('.js-sop-kpi-jump').forEach(function (card) {
                card.addEventListener('click', function () {
                    irASub(card.getAttribute('data-sub'), card.getAttribute('data-single-sub'));
                });
            });

            var filtroSop = document.getElementById('filtro_inmueble_sop');
            if (filtroSop) {
                filtroSop.addEventListener('change', function () {
                    var val = filtroSop.value || 'todos';
                    DBG.log('filtro inmueble soportes → ' + val, 'info');
                });
            }
        });
    })();
</script>
