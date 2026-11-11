{{-- partials/nueva-reserva/buscador.blade.php · Cliente · Motor de Búsqueda --}}
{{-- ══════════════════════════════════════════════════════════════════════
    NOVEDADES v26-CLI (SIMETRÍA Y MICRO-ESTILOS):
    · Cuadrícula responsiva adaptada al micro-diseño (tarjetas compactas).
    · Carrusel cuadrado integrado respetando tamaños y radios (3px/4px).
    · Tipografía jerárquica (.val 9px, .sub 7.5px) y tonos --accent:#3d7a5c.
    · Calendario y Mapa en Floating Popovers minimalistas.
    · Conserva lógica intacta del RV-v1-DBG y API de disponibilidad.
   ══════════════════════════════════════════════════════════════════════ --}}

@php
    // ─── 1. INICIALIZACIÓN DEFENSIVA Y LOGS PHP ──────────────────────
    $phpDebugLogs = [];
    $inmuebles = collect();
    
    try {
        $inmuebles = \App\Models\Rsv\CatalogoInmueble::with(['multimedia', 'latestTarifa', 'tipoInmueble'])
            ->where('active', 1)
            ->orderBy('name')
            ->limit(24)
            ->get();
        
        $phpDebugLogs[] = ['ok', 'Buscador: Cargados ' . $inmuebles->count() . ' inmuebles.'];
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('cli.buscador: '.$e->getMessage());
        $phpDebugLogs[] = ['err', 'Error al cargar propiedades: ' . $e->getMessage()];
    }

    // ─── 2. URL BASE DEL ENDPOINT DE DISPONIBILIDAD ──────────────────
    $apiDispUrl = '';
    try {
        if (\Illuminate\Support\Facades\Route::has('rsv.cliente.api.disponibilidad')) {
            $apiDispUrl = route('rsv.cliente.api.disponibilidad', ['id' => '__INM__']);
        } else {
            $apiDispUrl = url('/rsv/cliente-api-disponibilidad/__INM__');
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error resolviendo ruta API: ' . $e->getMessage()];
    }

    $dsJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $phpDebugJson = json_encode($phpDebugLogs, $dsJsonFlags);
@endphp

{{-- ──────────────────────────────────────────────────────────
    PANEL DE DEPURACIÓN (RV-v1-DBG)
   ────────────────────────────────────────────────────────── --}}
<div id="rvDebugToggle" title="Debug Buscador (RV-v1-DBG)">
    <span class="rv-dbg-led" id="rvDbgLed"></span>DBG<span class="rv-dbg-badge" id="rvDbgBadge">0</span>
</div>
<div id="rvDebugPanel" role="dialog">
    <div class="rv-dbg-header" id="rvDbgHeader">
        <span>DEBUG · Buscador Cliente · RV-v1-DBG</span>
        <span class="rv-dbg-controls">
            <button type="button" class="rv-dbg-btn" id="rvDbgCopy">Copiar</button>
            <button type="button" class="rv-dbg-btn" id="rvDbgClear">Limpiar</button>
            <button type="button" class="rv-dbg-btn" id="rvDbgClose">✕</button>
        </span>
    </div>
    <div id="rvDebugBody"></div>
</div>

<div id="rvTabWrap" data-fragmento="">

{{-- Contenedor Principal (Simétrico al ejemplo de chat) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliBuscadorRoot" style="border-color:#f1f5f9 !important;">

<style>
    /* ── VARIABLES Y ESTILOS BASE (Paleta Original RSV) ── */
    .reservas-soft { 
        --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; 
        --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; 
        --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; 
    }
    
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .strong { color:var(--ink-2); font-weight:600; }
    
    .reservas-soft .btn-green { 
        background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); 
        font-size:8.5px; font-weight:600; padding:3px 10px; border-radius:3px; 
        cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:4px;
        transition: all 0.2s;
    }
    .reservas-soft .btn-green:hover:not(:disabled) { background:#e3f1e9; color:#2e5c43; }
    .reservas-soft .btn-green:disabled { opacity: 0.5; cursor: not-allowed; }
    
    .reservas-soft .btn-outline { 
        background:#fff; color:var(--ink-3); border:1px solid var(--grid-2); 
        font-size:8.5px; font-weight:600; padding:3px 10px; border-radius:3px; 
        cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px;
    }
    .reservas-soft .btn-outline:hover { background:var(--head); color:var(--ink-2); border-color:var(--ink-4); }

    /* ── GRID COMPACTO ── */
    .rsv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        align-items: start;
    }

    .rsv-min-card {
        position: relative;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--grid-2);
        border-radius: 5px;
        padding: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .rsv-min-card:hover { border-color: var(--accent-bd); box-shadow: 0 4px 12px rgba(0,0,0,.04); }
    
    /* Imagen Cuadrada Miniatura */
    .rsv-img-wrap {
        position: relative; width: 100%; aspect-ratio: 1 / 1;
        border-radius: 3px; overflow: hidden; background: var(--head);
        margin-bottom: 8px; border: 1px solid var(--grid);
    }
    .rsv-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    
    /* Controles Carrusel Mini */
    .rsv-img-wrap .carousel-control-prev, 
    .rsv-img-wrap .carousel-control-next {
        width: 18px; height: 18px; background: rgba(255,255,255,0.85); 
        border-radius: 50%; top: 50%; transform: translateY(-50%);
        margin: 0 4px; opacity: 0; transition: opacity 0.2s;
    }
    .rsv-img-wrap .carousel-control-prev-icon, 
    .rsv-img-wrap .carousel-control-next-icon { width: 8px; height: 8px; filter: invert(1) grayscale(100) contrast(200); }
    .rsv-min-card:hover .carousel-control-prev, .rsv-min-card:hover .carousel-control-next { opacity: 1; }
    
    .carousel-indicators { margin-bottom: 4px; }
    .carousel-indicators [data-bs-target] { width: 4px; height: 4px; border-radius: 50%; margin: 0 2px; border: none; opacity: 0.6; }
    .carousel-indicators .active { opacity: 1; transform: scale(1.2); }

    /* Textos Card Mini */
    .rsv-info-title { font-size: 9.5px; font-weight: 700; color: var(--ink-2); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rsv-info-sub { font-size: 7.5px; color: var(--ink-4); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
    .rsv-info-price { font-size: 9.5px; font-weight: 700; color: var(--accent); margin-top: 4px; }
    .rsv-info-price span { font-weight: 500; font-size: 7.5px; color: var(--ink-3); }

    /* ── POP-OVERS FLOTANTES (Minimalistas) ── */
    .rsv-popover {
        position: absolute; top: calc(100% + 6px); left: 50%; transform: translateX(-50%);
        width: 100%; min-width: 240px; background: #fff; border-radius: 6px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid var(--grid-2);
        z-index: 1050; padding: 10px; display: none; animation: popFadeIn 0.2s ease-out forwards;
        cursor: default;
    }
    @media(max-width: 575px) { .rsv-popover { width: calc(100vw - 32px); max-width: 320px; } }
    @keyframes popFadeIn { from { opacity: 0; transform: translate(-50%, -5px); } to { opacity: 1; transform: translate(-50%, 0); } }
    
    .rsv-popover::before {
        content: ''; position: absolute; top: -5px; left: 50%; transform: translateX(-50%) rotate(45deg);
        width: 8px; height: 8px; background: #fff; border-top: 1px solid var(--grid-2); border-left: 1px solid var(--grid-2);
    }
    .popover-close { position: absolute; top: 8px; right: 8px; font-size: 10px; cursor: pointer; color: var(--ink-3); z-index: 10;}
    .popover-close:hover { color: var(--ink-2); }

    /* Formularios Mini */
    .xls-select { font-size: 8.5px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 4px 8px; width: 100%; outline: 0; margin-bottom: 8px; }
    .xls-select:focus { border-color: var(--accent-bd); }
    
    /* Calendario Mini (Simétrico al admin) */
    .cal-title { font-weight: 700; font-size: 8.5px; color: var(--ink-2); text-transform: capitalize; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; text-align: center; font-size: 8.5px; margin-top: 8px; background: var(--grid); border: 1px solid var(--grid); border-radius: 3px;}
    .cal-header-day { font-weight: 700; color: var(--ink-3); font-size: 7px; padding: 2px 0; background: var(--head); text-transform: uppercase;}
    .cal-cell { padding: 4px 0; cursor: pointer; transition: background 0.1s; color: var(--ink-2); background: #fff;}
    .cal-cell:hover:not(.cal-past):not(.cal-occ):not(.cal-ali) { background: var(--head); }
    
    .cal-past { color: var(--ink-4); cursor: not-allowed; text-decoration: line-through; background: #fafbfd;}
    .cal-selected { background: var(--accent) !important; color: #fff !important; font-weight: bold; border-radius:2px;}
    .cal-range { background: var(--accent-bg); color: #0f5132; }
    
    .cal-occ, .cal-ali { color: var(--ink-4); cursor: not-allowed; text-decoration: line-through; background: #fdeaea;}
    
    .cal-legend { font-size: 7.5px; color: var(--ink-3); display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 8px;}
    .cal-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 2px; vertical-align: middle;}
    .cal-dot-free { background: #fff; border: 1px solid var(--grid-2); }
    .cal-dot-sel { background: var(--accent); }
    .cal-dot-range { background: var(--accent-bg); border: 1px solid var(--accent-bd); }
    
    .cal-sel-info { font-size: 8px; color: var(--ink-2); background: var(--head); padding: 6px; border-radius: 3px; border: 1px solid var(--grid-2); margin-top: 8px; margin-bottom: 8px; line-height: 1.3;}

    /* Panel de Debug (Oculto al usuario final, solo para devs) */
    #rvDebugToggle { position: fixed; right: 0; bottom: 0; z-index: 9999; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; background: rgba(2,6,23,.94); color: #64748b; font-family: monospace; font-size: 8px; cursor: pointer; display: flex; align-items: center; gap: 5px; }
    .rv-dbg-led { width: 5px; height: 5px; border-radius: 1px; background: #10b981; }
    .rv-dbg-led.err { background: #ef4444; animation: rvDbgBlink 1s steps(2,start) infinite; }
    @keyframes rvDbgBlink { 50% { opacity: .35; } }
    #rvDebugPanel { position: fixed; right: 0; bottom: 20px; z-index: 10000; width: 380px; max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-radius: 6px 0 0 0; font-family: monospace; }
    #rvDebugPanel.open { display: flex; }
    .rv-dbg-header { display: flex; justify-content: space-between; align-items: center; padding: 5px 8px; background: #0b1220; font-size: 9px; color: #38bdf8; cursor: pointer; }
    .rv-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; margin-left: 4px;}
    #rvDebugBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.4; }
    .rv-dbg-line { border-bottom: 1px dashed #1e293b; margin-bottom:2px; padding-bottom:2px;}
    .rv-dbg-time { color: #64748b; margin-right: 4px; }
    .rv-dbg-info { color: #93c5fd; } .rv-dbg-ok { color: #86efac; } .rv-dbg-err { color: #fca5a5; } .rv-dbg-warn { color: #fde68a; } .rv-dbg-net { color: #c4b5fd; }
</style>

<!-- HEADER DE SECCIÓN (Idéntico a Chat) -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:6px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-houses me-1" style="font-size:8px; color:var(--accent);"></i> Propiedades Disponibles
    </h6>
    <span class="sub">Descubre y reserva en línea.</span>
</div>

{{-- CUADRÍCULA DE PROPIEDADES --}}
<div class="rsv-grid pb-4">
    @forelse($inmuebles as $inm)
        @php
            $tarifa = optional($inm->latestTarifa)->precio_noche;
            $mapQuery = urlencode($inm->ubicacion ?? $inm->city ?? $inm->name);
        @endphp

        <div class="rsv-min-card">
            
            {{-- Carrusel Cuadrado --}}
            <div class="rsv-img-wrap">
                <div id="carMin_{{ $inm->id }}" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="false">
                    
                    @if($inm->multimedia->count() > 1)
                    <div class="carousel-indicators">
                        @foreach($inm->multimedia as $idx => $media)
                            <button type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    @endif

                    <div class="carousel-inner h-100">
                        @forelse($inm->multimedia as $idx => $media)
                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                <img src="{{ $media->url_archivo }}" alt="{{ $inm->name }}" loading="lazy">
                            </div>
                        @empty
                            <div class="carousel-item h-100 active d-flex align-items-center justify-content-center bg-light">
                                <i class="bi bi-house-door text-secondary opacity-25" style="font-size: 2rem;"></i>
                            </div>
                        @endforelse
                    </div>

                    @if($inm->multimedia->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carMin_{{ $inm->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Información --}}
            <div class="rsv-info-title">{{ $inm->name }}</div>
            <div class="rsv-info-sub"><i class="bi bi-geo-alt"></i> {{ $inm->city ?? 'S/U' }} · Cap. {{ $inm->capacidad_maxima ?? '-' }}</div>
            <div class="rsv-info-price">{{ $tarifa ? ('$'.number_format($tarifa, 0)) : '--' }} <span>/ noche</span></div>

            {{-- Botones de Acción --}}
            <div class="d-flex gap-1 mt-2">
                <button type="button" class="btn-outline flex-grow-1" onclick="window.rvTabApi.toggleMap(event, {{ $inm->id }})">
                    <i class="bi bi-map"></i> Mapa
                </button>
                <button type="button" class="btn-green flex-grow-1" onclick="window.rvTabApi.abrirCalendarioInmueble(event, {{ $inm->id }})">
                    <i class="bi bi-calendar3"></i> Fechas
                </button>
            </div>

            {{-- ======================================================== --}}
            {{-- POPOVER DE MAPA                                          --}}
            {{-- ======================================================== --}}
            <div id="map-box-{{ $inm->id }}" class="rsv-popover" onclick="event.stopPropagation()">
                <i class="bi bi-x-lg popover-close" onclick="window.rvTabApi.closePopovers()"></i>
                <h6 class="val mb-2">{{ $inm->name }}</h6>
                <div style="border-radius: 4px; overflow: hidden; border: 1px solid var(--grid-2);">
                    <iframe width="100%" height="160" frameborder="0" style="border:0;"
                        src="https://maps.google.com/maps?q={{ $mapQuery }}&t=&z=14&ie=UTF8&iwloc=&output=embed" allowfullscreen>
                    </iframe>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- POPOVER DE CALENDARIO                                    --}}
            {{-- ======================================================== --}}
            <div id="cal-box-{{ $inm->id }}" class="rsv-popover" onclick="event.stopPropagation()">
                <i class="bi bi-x-lg popover-close" onclick="window.rvTabApi.closePopovers()"></i>
                
                <form action="{{ url()->current() }}" method="GET" onsubmit="window.rvTabApi.validarReservaCliente(event, this)">
                    <input type="hidden" name="inmueble_id" value="{{ $inm->id }}">
                    <input type="hidden" name="fi" id="fldFechaIni_{{ $inm->id }}" value="" required>
                    <input type="hidden" name="ff" id="fldFechaFin_{{ $inm->id }}" value="" required>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <button type="button" class="btn-mini" onclick="window.rvTabApi.cambiarMes(-1, {{ $inm->id }})">‹</button>
                        <span id="calMonthLbl_{{ $inm->id }}" class="cal-title">Cargando...</span>
                        <button type="button" class="btn-mini" onclick="window.rvTabApi.cambiarMes(1, {{ $inm->id }})">›</button>
                    </div>
                    
                    <div id="miniCalGrid_{{ $inm->id }}" class="cal-grid"></div>
                    
                    <div class="cal-legend">
                        <span class="me-2"><span class="cal-dot cal-dot-free"></span> Libre</span>
                        <span class="me-2"><span class="cal-dot cal-dot-sel"></span> Sel</span>
                    </div>

                    <div id="calSelInfo_{{ $inm->id }}" class="cal-sel-info">
                        Selecciona tu fecha de llegada.
                    </div>
                    
                    <label class="val mb-1">Huéspedes</label>
                    <select name="huespedes" class="xls-select">
                        <option value="2">1 a 2 huéspedes</option>
                        <option value="4">3 a 4 huéspedes</option>
                        <option value="6">5 a más huéspedes</option>
                    </select>

                    <button type="submit" class="btn-green w-100 mt-1" id="btnReserva_{{ $inm->id }}" disabled>
                        Continuar a Checkout <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="col-12">
            <div class="empty-friendly">
                <i class="bi bi-house-x d-block mb-1" style="font-size:18px; color:var(--ink-4);"></i>
                <span class="val">No hay propiedades publicadas.</span>
            </div>
        </div>
    @endforelse
</div>

</div>{{-- /.reservas-soft --}}
</div>{{-- /#rvTabWrap --}}

{{-- LOGS PHP INYECTADOS --}}
<script type="application/json" id="rvPhpDebug">{!! $phpDebugJson !!}</script>

<script>
function rvTabInit() {

    /* ═══ 1. PANEL DE DEBUG RV-v1-DBG ═══ */
    var DBG = (window.RvDbg && window.RvDbg.alive && window.RvDbg.alive()) ? window.RvDbg : (function () {
        var MAX = 250, numErr = 0; function $(id) { return document.getElementById(id); }
        function asegurarNodos() {
            var rootDbg = document.getElementById('cliBuscadorRoot');
            if (!rootDbg) return; 
            if (!$('rvDebugToggle')) {
                var t = document.createElement('div'); t.id = 'rvDebugToggle'; t.title = 'Debug Buscador';
                t.innerHTML = '<span class="rv-dbg-led" id="rvDbgLed"></span>DBG<span class="rv-dbg-badge" id="rvDbgBadge">0</span>';
                rootDbg.insertBefore(t, rootDbg.firstChild);
            }
            if (!$('rvDebugPanel')) {
                var p = document.createElement('div'); p.id = 'rvDebugPanel';
                p.innerHTML = '<div class="rv-dbg-header" id="rvDbgHeader"><span>DEBUG · Buscador Cliente</span><span class="rv-dbg-controls"><button type="button" class="rv-dbg-btn" id="rvDbgCopy">Copiar</button><button type="button" class="rv-dbg-btn" id="rvDbgClear">Limpiar</button><button type="button" class="rv-dbg-btn" id="rvDbgClose">✕</button></span></div><div id="rvDebugBody"></div>';
                rootDbg.appendChild(p);
            }
        }
        function hora() { var d = new Date(); function f(n2){ return (n2<10?'0':'')+n2; } return f(d.getHours())+':'+f(d.getMinutes())+':'+f(d.getSeconds()); }
        var api = {
            log: function (txt, nivel) {
                var b = $('rvDebugBody'); if (!b) return; var lv = nivel || 'info'; var ln = document.createElement('div');
                ln.className = 'rv-dbg-line'; var seguro = String(txt).replace(/&/g, '&amp;').replace(/</g, '&lt;');
                ln.innerHTML = '<span class="rv-dbg-time">['+hora()+']</span><span class="rv-dbg-'+lv+'">'+seguro+'</span>';
                b.appendChild(ln); while (b.children.length > MAX) b.removeChild(b.firstChild); b.scrollTop = b.scrollHeight;
                if (lv === 'err') { numErr++; var bd = $('rvDbgBadge'), led = $('rvDbgLed'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; } if (led) led.classList.add('err'); }
            },
            open: function () { var p = $('rvDebugPanel'); if (p) { p.classList.add('open'); var bd=$('rvDbgBadge'), led=$('rvDbgLed'); if(bd) bd.style.display='none'; if(led) led.classList.remove('err'); numErr=0; } },
            close: function () { var p = $('rvDebugPanel'); if (p) p.classList.remove('open'); },
            togglePanel: function () { var p = $('rvDebugPanel'); if (p) (p.classList.contains('open')) ? api.close() : api.open(); },
            clear: function () { var b = $('rvDebugBody'); if (b) b.innerHTML = ''; },
            copy: function () { var b = $('rvDebugBody'); if(!b) return; var txt = '=== DEBUG ===\n'; for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n'; if(navigator.clipboard) navigator.clipboard.writeText(txt); },
            ensure: function () { asegurarNodos(); }, alive: function () { return !!$('rvDebugToggle'); }
        };
        window.RvDbg = api; return api;
    })();

    if (!DBG) return; DBG.ensure();
    function dbgLog(level, msg) { if (DBG && DBG.log) DBG.log(msg, level); }
    function unicarDbg() {
        var ts = document.querySelectorAll('#rvDebugToggle'); for(var i=1; i<ts.length; i++){ ts[i].remove(); }
        var ps = document.querySelectorAll('#rvDebugPanel'); for(var j=1; j<ps.length; j++){ ps[j].remove(); }
    }
    unicarDbg();
    if(window.__rvDbgDocHandler) document.removeEventListener('click', window.__rvDbgDocHandler);
    window.__rvDbgDocHandler = function (e) {
        var t = e.target; if (!t || !t.closest) return;
        if (t.closest('#rvDebugToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#rvDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#rvDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#rvDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__rvDbgDocHandler);
    try { var phpLogs = JSON.parse(document.getElementById('rvPhpDebug').textContent || '[]'); phpLogs.forEach(function (l) { dbgLog(l[0], '<b>PHP</b> ' + l[1]); }); } catch (e) {}

    /* ═══ 2. LÓGICA DE MAPA Y CALENDARIO (Popovers Flotantes) ═══ */
    const RSV_CAL = {}; 
    const NOMBRES_MESES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const DIAS_SEMANA = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];
    const ENDPOINT_BASE = '{{ $apiDispUrl }}';

    function calFmt(d) { return d.getFullYear() + '-' + ('0'+(d.getMonth()+1)).slice(-2) + '-' + ('0'+d.getDate()).slice(-2); }
    function calParse(s) { var p = s.split('-'); return new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2])); }

    function closePopovers() {
        document.querySelectorAll('.rsv-popover').forEach(el => el.style.display = 'none');
    }
    
    // Cierra los popovers al hacer click fuera de la tarjeta
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.rsv-min-card')) { closePopovers(); }
    });

    function toggleMap(e, id) {
        e.stopPropagation(); closePopovers();
        let mapBox = document.getElementById('map-box-' + id);
        if(mapBox) mapBox.style.display = 'block';
    }

    function abrirCalendarioInmueble(e, id) {
        e.stopPropagation(); closePopovers();
        let box = document.getElementById('cal-box-' + id);
        if (box) {
            box.style.display = 'block';
            if (!RSV_CAL[id]) {
                let hoy = new Date();
                RSV_CAL[id] = { year: hoy.getFullYear(), month: hoy.getMonth(), start: null, end: null, occupied: [] };
                fetchOccupied(id);
            } else {
                pintarCalendario(id);
            }
        }
    }

    function fetchOccupied(id) {
        var state = RSV_CAL[id];
        var url = ENDPOINT_BASE.replace('__INM__', id) + '?year=' + state.year + '&month=' + (state.month + 1);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.json())
            .then(data => { if(data.success) { state.occupied = data.ocupadas || []; pintarCalendario(id); } });
    }

    function cambiarMes(direccion, id) {
        let state = RSV_CAL[id];
        state.month += direccion;
        if (state.month < 0) { state.month = 11; state.year--; } 
        else if (state.month > 11) { state.month = 0; state.year++; }
        fetchOccupied(id);
    }

    function pintarCalendario(id) {
        let state = RSV_CAL[id];
        let grid = document.getElementById('miniCalGrid_' + id);
        let lbl = document.getElementById('calMonthLbl_' + id);
        lbl.innerText = NOMBRES_MESES[state.month] + ' ' + state.year;

        let primerDiaMes = new Date(state.year, state.month, 1);
        let ultimoDiaMes = new Date(state.year, state.month + 1, 0);
        let diaSemanaInicio = primerDiaMes.getDay(); 
        let hoy = new Date(); hoy.setHours(0,0,0,0);

        let html = '';
        DIAS_SEMANA.forEach(d => { html += `<div class="cal-header-day">${d}</div>`; });
        for (let i = 0; i < diaSemanaInicio; i++) { html += `<div></div>`; }

        for (let dia = 1; dia <= ultimoDiaMes.getDate(); dia++) {
            let fechaActual = new Date(state.year, state.month, dia);
            fechaActual.setHours(0,0,0,0);
            let timeActual = fechaActual.getTime();
            let esPasado = timeActual < hoy.getTime();
            let fechaISO = calFmt(fechaActual);
            let esOcupado = state.occupied.includes(fechaISO);
            
            let clase = 'cal-cell';
            if (esPasado || esOcupado) clase += ' cal-past';
            else {
                if (state.start && timeActual === state.start.getTime()) clase += ' cal-selected';
                else if (state.end && timeActual === state.end.getTime()) clase += ' cal-selected';
                else if (state.start && state.end && timeActual > state.start.getTime() && timeActual < state.end.getTime()) clase += ' cal-range';
            }

            if (esPasado || esOcupado) html += `<div class="${clase}">${dia}</div>`;
            else html += `<div class="${clase}" onclick="window.rvTabApi.seleccionarDia(event, ${id}, '${fechaISO}')">${dia}</div>`;
        }
        grid.innerHTML = html;
        actualizarPanelInfo(id);
    }

    function seleccionarDia(e, id, fechaISO) {
        e.stopPropagation(); 
        let state = RSV_CAL[id];
        let dateObj = new Date(fechaISO + 'T00:00:00');

        if (!state.start || (state.start && state.end)) {
            state.start = dateObj; state.end = null;
        } else if (state.start && !state.end) {
            if (dateObj.getTime() <= state.start.getTime()) {
                state.start = dateObj;
            } else {
                let ok = true;
                let cur = new Date(state.start.getTime());
                while (cur <= dateObj) {
                    if (state.occupied.includes(calFmt(cur))) { ok = false; break; }
                    cur.setDate(cur.getDate() + 1);
                }
                if (ok) state.end = dateObj;
                else { alert('Rango incluye fechas ya reservadas.'); state.start = dateObj; }
            }
        }
        pintarCalendario(id);
    }

    function actualizarPanelInfo(id) {
        let state = RSV_CAL[id];
        let panelInfo = document.getElementById('calSelInfo_' + id);
        let inputFi = document.getElementById('fldFechaIni_' + id);
        let inputFf = document.getElementById('fldFechaFin_' + id);
        let btn = document.getElementById('btnReserva_' + id);

        const fStr = (d) => ('0'+d.getDate()).slice(-2) + '/' + ('0'+(d.getMonth()+1)).slice(-2);

        if (!state.start) {
            panelInfo.innerHTML = '<b style="color:var(--accent);">Paso 1:</b> Selecciona tu llegada.';
            inputFi.value = ''; inputFf.value = ''; btn.disabled = true;
        } else if (state.start && !state.end) {
            panelInfo.innerHTML = 'Llegada: <b>' + fStr(state.start) + '</b>. Selecciona la salida.';
            inputFi.value = calFmt(state.start); inputFf.value = ''; btn.disabled = true;
        } else if (state.start && state.end) {
            let noches = Math.round((state.end.getTime() - state.start.getTime()) / 86400000);
            panelInfo.innerHTML = '<b>Rango:</b> ' + fStr(state.start) + ' al ' + fStr(state.end) + ' ('+noches+' n).';
            inputFi.value = calFmt(state.start); inputFf.value = calFmt(state.end); btn.disabled = false; 
        }
    }

    function validarReservaCliente(e, form) {
        var fi = form.querySelector('input[name="fi"]').value;
        var ff = form.querySelector('input[name="ff"]').value;
        if (!fi || !ff) { e.preventDefault(); alert('Selecciona el rango en el calendario.'); return; }
        
        localStorage.setItem('rsv_cliente_panel_parent', '#cli-nueva');
        localStorage.setItem('rsv_cliente_panel_child', '#nr-sub-checkout');
    }

    window.rvTabApi = { 
        abrirCalendarioInmueble, toggleMap, cambiarMes, seleccionarDia, validarReservaCliente, closePopovers
    };
}

if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', rvTabInit); } 
else { rvTabInit(); }
</script>