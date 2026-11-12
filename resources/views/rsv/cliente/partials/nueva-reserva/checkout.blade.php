{{-- partials/nueva-reserva/checkout.blade.php · Cliente · Carrito de Compras Reactivo (CHK-v2-DBG) --}}
<div id="sheet-checkout-cliente">

    {{-- CHK-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="chkDbgToggle" title="Debug Checkout (CHK-v2-DBG)">
        <span class="chk-dbg-led" id="chkDbgLed"></span>DBG<span class="chk-dbg-badge" id="chkDbgBadge">0</span>
    </div>
    <div id="chkDbgPanel" role="dialog" aria-label="Panel de debug del Checkout">
        <div class="chk-dbg-header" id="chkDbgHeader">
            <span>DEBUG · Checkout Cliente · CHK-v2-DBG</span>
            <span class="chk-dbg-controls">
                <button type="button" class="chk-dbg-btn" id="chkDbgRefresh" title="Refrescar fragmento">↻ Frag</button>
                <button type="button" class="chk-dbg-btn" id="chkDbgCopy">Copiar</button>
                <button type="button" class="chk-dbg-btn" id="chkDbgClear">Limpiar</button>
                <button type="button" class="chk-dbg-btn" id="chkDbgClose">✕</button>
            </span>
        </div>
        <div id="chkDbgBody"></div>
    </div>

    @php
        $inmId = request('inmueble_id');
        $inm = is_numeric($inmId) ? \App\Models\Rsv\CatalogoInmueble::with(['latestTarifa', 'multimedia'])->find($inmId) : null;

        $tarifaObj = $inm?->latestTarifa;
        $precioMin = $tarifaObj ? (float) $tarifaObj->precio_minimo_reserva : 0;
        $diasMax   = $tarifaObj ? $tarifaObj->dias_maximos : 5;
        $maxAnual  = $tarifaObj ? $tarifaObj->max_reservas_anuales_cliente : null;
        $capMaxima = $inm ? ($inm->capacidad_maxima ?? 6) : 6;

        $fi = request('fi') ? \Carbon\Carbon::parse(request('fi'))->startOfDay() : null;
        $ff = request('ff') ? \Carbon\Carbon::parse(request('ff'))->startOfDay() : null;
        $noches = ($fi && $ff && $ff->gt($fi)) ? (int) $fi->diffInDays($ff) : 0;

        $subtotal = $precioMin;
        $total = $subtotal;

        $heroImg = ($inm && $inm->multimedia->first()) ? $inm->multimedia->first()->url_archivo : asset('img/9.jpeg');

        $rutaStore = \Illuminate\Support\Facades\Route::has('rsv.reservas.store-ajax')
            ? route('rsv.reservas.store-ajax')
            : (\Illuminate\Support\Facades\Route::has('rsv.reservas.store') ? route('rsv.reservas.store') : '');

        $rutaFragmento = '';
        foreach (['rsv.cliente.nueva-reserva.checkout.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $statusPendienteId = class_exists(\App\Models\Rsv\Status::class)
            ? (\App\Models\Rsv\Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])->first()->id ?? 1) : 1;
        $origenDefectoId = class_exists(\App\Models\Rsv\OrigenReserva::class)
            ? (\App\Models\Rsv\OrigenReserva::first()->id ?? 1) : 1;
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border" id="cliCheckoutRoot"
         data-redirect="{{ request()->fullUrlWithoutQuery(['inmueble_id', 'fi', 'ff']) }}"
         data-frag="{{ $rutaFragmento ?: route('rsv.cliente.fragmento') }}"
         data-fragkey="nueva-checkout"
         data-inmueble-id="{{ $inmId ?? '' }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .val { font-size:10px; color:var(--ink-2); font-weight:700; }
            .reservas-soft .sub { font-size:8px; color:var(--ink-4); line-height:1.4;}

            .reservas-soft .btn-green { background:var(--accent); color:#fff; border:1px solid var(--accent); font-size:9.5px; font-weight:700; padding:8px 16px; border-radius:6px; cursor:pointer; transition: all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 2px 5px rgba(61,122,92,0.15);}
            .reservas-soft .btn-green:hover:not(:disabled) { background:#2e5c43; transform:translateY(-1px); box-shadow:0 4px 8px rgba(61,122,92,0.2);}
            .reservas-soft .btn-green:disabled { opacity: 0.6; cursor: not-allowed; transform:none; box-shadow:none;}

            .reservas-soft .btn-outline { background:#fff; color:var(--ink-3); border:1px solid var(--grid-2); font-size:9px; font-weight:600; padding:6px 16px; border-radius:4px; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px; }
            .reservas-soft .btn-outline:hover { background:var(--head); color:var(--ink-2); border-color:var(--ink-4); }

            .reservas-soft .btn-outline-dashed { background:transparent; color:var(--ink-3); border:1px dashed var(--ink-4); font-size:8.5px; font-weight:600; padding:6px 12px; border-radius:4px; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px; width:100%;}
            .reservas-soft .btn-outline-dashed:hover { background:var(--head); color:var(--accent); border-color:var(--accent-bd); }

            .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:6px; color:#c98a85; font-size:8.5px; font-weight:600; padding:10px 14px; margin-bottom: 12px;}

            .checkout-img-wrap { width: 100%; height: 180px; border-radius: 8px; overflow: hidden; margin-bottom: 16px; position: relative; background: var(--head); box-shadow: 0 4px 12px rgba(0,0,0,0.05);}
            .checkout-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
            .checkout-img-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); padding: 20px 16px 12px; color: #fff;}
            .checkout-img-overlay h4 { margin: 0; font-size: 16px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
            .checkout-img-overlay p { margin: 4px 0 0 0; font-size: 10px; opacity: 0.9; }

            .checkout-info-card { background: #fff; border: 1px solid var(--grid-2); border-radius: 8px; padding: 16px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);}
            .checkout-info-card h6 { font-size: 11px; font-weight: 800; color: var(--accent); margin-bottom: 12px; border-bottom: 1px solid var(--grid); padding-bottom: 8px;}

            .rules-list { padding-left: 0; list-style: none; margin: 0; font-size: 9px; color: var(--ink-2); }
            .rules-list li { margin-bottom: 8px; display: flex; align-items: flex-start; gap: 8px; }
            .rules-list li i { color: var(--accent); font-size: 11px; margin-top: 1px;}

            .checkout-sidebar { position: sticky; top: 20px; max-height: calc(100vh - 40px); overflow-y: auto; padding-right: 5px; }
            .checkout-sidebar::-webkit-scrollbar { width: 4px; }
            .checkout-sidebar::-webkit-scrollbar-thumb { background: var(--grid-2); border-radius: 4px; }

            .reservas-soft .resume-box { background: #fff; border: 1px solid var(--accent-bd); border-radius: 8px; padding: 20px; box-shadow: 0 8px 24px rgba(61,122,92,0.08); display: flex; flex-direction: column;}
            .reservas-soft .resume-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 9.5px; }
            .reservas-soft .resume-row:last-child { margin-bottom: 0; }
            .reservas-soft .resume-lbl { color: var(--ink-2); font-weight: 600; }
            .reservas-soft .resume-val { color: var(--ink-2); font-weight: 700; text-align: right; }

            .reservas-soft .resume-total-box { background: var(--head); padding: 16px; border-radius: 6px; border: 1px solid var(--grid-2); }
            .reservas-soft .resume-total { font-size: 15px; color: var(--accent); font-weight: 800; letter-spacing:-0.5px;}

            .xls-input { font-size: 8.5px; color: var(--ink-2); border: 1px solid var(--grid-2); border-radius: 4px; padding: 4px 8px; outline: 0; box-shadow: none !important;}
            .xls-input:focus { border-color: var(--accent-bd); background: #fafcfe; }
            .guest-box { transition: all 0.2s; }
            .guest-box:hover { border-color: var(--accent-bd) !important; }

            .reservas-soft .form-check-input:checked { background-color: var(--accent); border-color: var(--accent); }
            .reservas-soft .terms-box { background: var(--head); border: 1px solid var(--grid-2); border-radius: 6px; padding: 12px; margin: 16px 0; transition: border-color 0.2s;}
            .reservas-soft .terms-box:hover { border-color: var(--accent-bd); background: #fff; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #chkDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #chkDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .chk-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .chk-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: chkDbgBlink 1s steps(2,start) infinite; }
            @keyframes chkDbgBlink { 50% { opacity: .35; } }
            .chk-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #chkDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 400px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, monospace; }
            .chk-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .chk-dbg-controls { display: flex; gap: 4px; }
            .chk-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .chk-dbg-btn:hover { background: #475569; }
            #chkDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .chk-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .chk-dbg-time { color: #64748b; margin-right: 4px; }
            .chk-dbg-info { color: #93c5fd; } .chk-dbg-ok { color: #86efac; }
            .chk-dbg-warn { color: #fde68a; } .chk-dbg-err { color: #fca5a5; } .chk-dbg-net { color: #c4b5fd; }
        </style>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap:6px; border-bottom: 1px solid var(--grid-2); padding-bottom: 12px;">
            <h6 class="fw-bold mb-0" style="font-size:12px; color:#334155;">
                <i class="bi bi-cart-check-fill me-1" style="font-size:14px; color:var(--accent);"></i> Confirma y Paga
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn-outline" id="btnCancelarCheckout" title="Cancelar y limpiar">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn-outline" onclick="document.querySelector('[data-bs-target=\'#nr-sub-buscador\']').click()">
                    <i class="bi bi-pencil-square"></i> Modificar Fechas
                </button>
            </div>
        </div>

        @if(!$inm || !$fi || !$ff)
            <div class="st-warn text-center py-5 rounded" style="font-size: 11px;">
                <i class="bi bi-exclamation-circle d-block mb-2 text-danger" style="font-size: 32px;"></i>
                <h6 class="fw-bold mb-1">Información Incompleta</h6>
                No has seleccionado ninguna propiedad o las fechas son inválidas. <br>Por favor, regresa al buscador.
            </div>
        @else
            <div class="row g-4 mt-1">
                {{-- COLUMNA IZQUIERDA: FOTO, FECHAS Y REGLAS --}}
                <div class="col-md-7">
                    <div class="checkout-img-wrap">
                        <img src="{{ $heroImg }}" alt="{{ $inm->name }}">
                        <div class="checkout-img-overlay">
                            <h4>{{ $inm->name }}</h4>
                            <p><i class="bi bi-geo-alt me-1"></i> {{ $inm->city ?? 'Ubicación no especificada' }}</p>
                        </div>
                    </div>

                    <div class="checkout-info-card d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div class="sub mb-1 text-uppercase fw-bold">Llegada (Check-in)</div>
                            <div class="val" style="font-size: 13px;">{{ $fi->format('d/m/Y') }}</div>
                            <div class="sub">Desde las 3:00 PM</div>
                        </div>
                        <div class="text-muted d-none d-sm-block">
                            <i class="bi bi-arrow-right" style="font-size: 16px;"></i>
                        </div>
                        <div>
                            <div class="sub mb-1 text-uppercase fw-bold">Salida (Check-out)</div>
                            <div class="val" style="font-size: 13px;">{{ $ff->format('d/m/Y') }}</div>
                            <div class="sub">Hasta la 1:00 PM</div>
                        </div>
                        <div class="text-end" style="border-left: 1px solid var(--grid-2); padding-left: 16px;">
                            <div class="sub mb-1 text-uppercase fw-bold">Duración</div>
                            <div class="val" style="font-size: 13px; color: var(--accent);">{{ $noches }} noches</div>
                        </div>
                    </div>

                    <div class="checkout-info-card">
                        <h6><i class="bi bi-info-circle-fill me-1"></i> Datos a tener en cuenta</h6>
                        <ul class="rules-list">
                            <li>
                                <i class="bi bi-person-check-fill"></i>
                                <span>El apartamento es exclusivo para el descanso del asociado. <strong>No se permite alquilar para terceros.</strong></span>
                            </li>
                            <li>
                                <i class="bi bi-clock-history"></i>
                                <span>El aporte realizado no es un costo de alquiler, corresponde únicamente al pago de <strong>aseo y administración</strong>.</span>
                            </li>
                            <li>
                                <i class="bi bi-calendar-x-fill"></i>
                                <span>Estadía máxima permitida: <strong>{{ $diasMax }} días</strong>. @if($maxAnual) (Límite anual: {{ $maxAnual }} reservas). @endif</span>
                            </li>
                            <li>
                                <i class="bi bi-people-fill"></i>
                                <span>Capacidad máxima del inmueble: <strong>{{ $capMaxima }} personas</strong>.</span>
                            </li>
                            <li>
                                <i class="bi bi-x-octagon-fill text-danger"></i>
                                <span><strong>No se permite el ingreso de mascotas</strong> al edificio.</span>
                            </li>
                            <li>
                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                <span>Una vez confirmado, no se hacen devoluciones de dinero. Reprogramaciones solo por fuerza mayor.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: TICKET Y FORMULARIO (STICKY SCROLL) --}}
                <div class="col-md-5">
                    <div class="checkout-sidebar">
                        @if($rutaStore)
                            <form id="cliCheckoutForm" data-store="{{ $rutaStore }}" class="resume-box h-100">
                                @csrf
                                <input type="hidden" name="_ajax" value="1">
                                <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inm->id }}">
                                <input type="hidden" name="id_user" value="{{ \Illuminate\Support\Facades\Auth::id() }}">
                                <input type="hidden" name="fecha_inicio" value="{{ $fi->toDateString() }}">
                                <input type="hidden" name="fecha_fin" value="{{ $ff->toDateString() }}">
                                <input type="hidden" name="monto_total" value="{{ $total }}">
                                <input type="hidden" name="id_rsv_statuses" value="{{ $statusPendienteId }}">
                                <input type="hidden" name="id_rsv_origen_reservas" value="{{ $origenDefectoId }}">
                                <input type="hidden" name="comentario_reserva" value="Reserva solicitada directamente desde el Portal del Cliente.">

                                <h6 class="val mb-3 text-center text-uppercase" style="letter-spacing: 1px; color: var(--ink-3);">Resumen de Pago</h6>

                                <div class="resume-row">
                                    <span class="resume-lbl">Aporte de Administración</span>
                                    <span class="resume-val">${{ number_format($subtotal, 0) }}</span>
                                </div>

                                <div class="resume-total-box mb-4">
                                    <div class="resume-row mb-0">
                                        <span class="resume-lbl" style="color:var(--ink-2); font-weight:800; font-size:11px;">Total Final</span>
                                        <span class="resume-total">${{ number_format($total, 0) }}</span>
                                    </div>
                                    <div class="text-end sub mt-1" style="color: var(--ink-4);">COP (Pesos Colombianos)</div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="val mb-1"><i class="bi bi-people-fill me-1" style="color:var(--accent);"></i> Registro de Huéspedes</h6>
                                    <div class="sub mb-3" style="font-size:7.5px;">Ingresa tus datos y los de tus acompañantes (puedes completarlos luego). Capacidad: <b>{{ $capMaxima }}</b> personas.</div>

                                    <div id="guestListContainer">
                                        <div class="guest-box mb-2 p-2 border rounded" style="background:var(--head);">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="val" style="font-size:8.5px; color:var(--accent);">Huésped 1 (Titular)</span>
                                            </div>
                                            <div class="row g-2">
                                                <input type="hidden" name="huespedes[0][es_titular]" value="1">
                                                <div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[0][nombre]" placeholder="Nombres" required></div>
                                                <div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[0][apellidos]" placeholder="Apellidos" required></div>
                                                <div class="col-5">
                                                    <select class="form-select form-select-sm xls-input" name="huespedes[0][tipo_documento]" required>
                                                        <option value="CC">CC</option>
                                                        <option value="CE">CE</option>
                                                        <option value="PAS">PAS</option>
                                                    </select>
                                                </div>
                                                <div class="col-7"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[0][numero_documento]" placeholder="N° Documento" required></div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-outline-dashed mt-1" id="btnAddGuest">
                                        <i class="bi bi-person-plus"></i> Añadir Acompañante
                                    </button>
                                </div>

                                <div id="checkoutAlertError" class="st-warn d-none mt-3"></div>

                                <div class="terms-box form-check d-flex align-items-center mb-auto mt-2" style="gap:8px; margin-left:0; padding-left:12px;">
                                    <input class="form-check-input m-0" type="checkbox" id="termsCheck" required style="width:16px; height:16px; cursor:pointer;">
                                    <label class="form-check-label sub" for="termsCheck" style="cursor:pointer; margin-top:2px;">
                                        He leído y acepto los <a href="#" style="color:var(--accent); font-weight:700; text-decoration:underline;">lineamientos y condiciones</a> del apartamento.
                                    </label>
                                </div>

                                <button type="submit" class="btn-green w-100 py-2 mt-2" id="btnConfirmarReserva">
                                    <i class="bi bi-shield-lock-fill" style="font-size: 14px;"></i>
                                    <span class="btn-txt">Confirmar Reserva Segura</span>
                                </button>
                                <div class="sub text-center mt-2" style="font-size: 8px;">
                                    Tus fechas quedarán bloqueadas inmediatamente.<br>Dispondrás de 3 días para cargar tu soporte de pago.
                                </div>
                            </form>
                        @else
                            <div class="st-warn"><i class="bi bi-exclamation-triangle me-1"></i> Sistema no configurado (Ruta de guardado ausente).</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    'use strict';
    if (window.__jsCheckoutClienteListo) return;
    window.__jsCheckoutClienteListo = true;

    /* ═══ PANEL DE DEBUG LOCAL CHK-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ChkDbg && window.ChkDbg.alive && window.ChkDbg.alive())
        ? window.ChkDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-checkout-cliente') || document.body;
                if (!$('chkDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'chkDbgToggle'; t.title = 'Debug Checkout (CHK-v2-DBG)';
                    t.innerHTML = '<span class="chk-dbg-led" id="chkDbgLed"></span>DBG<span class="chk-dbg-badge" id="chkDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('chkDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'chkDbgPanel';
                    p.innerHTML = '<div class="chk-dbg-header" id="chkDbgHeader">' +
                        '<span>DEBUG · Checkout Cliente · CHK-v2-DBG</span>' +
                        '<span class="chk-dbg-controls">' +
                        '<button type="button" class="chk-dbg-btn" id="chkDbgRefresh" title="Refrescar fragmento">↻ Frag</button>' +
                        '<button type="button" class="chk-dbg-btn" id="chkDbgCopy">Copiar</button>' +
                        '<button type="button" class="chk-dbg-btn" id="chkDbgClear">Limpiar</button>' +
                        '<button type="button" class="chk-dbg-btn" id="chkDbgClose">✕</button>' +
                        '</span></div><div id="chkDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('chkDbgToggle'), p = $('chkDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('chkDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'chk-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="chk-dbg-time">[' + hora() + ']</span><span class="chk-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('chkDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('chkDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('chkDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('chkDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('chkDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('chkDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('chkDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('chkDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('chkDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Checkout CHK-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('chkDbgToggle'); }
            };
            window.ChkDbg = api;
            return api;
        })();

    DBG.ensure();
    var chkdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#chkDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#chkDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA ASÍNCRONA DEL CHECKOUT SIN RECARGAR PÁGINA ═══
    window.chkReloadFragment = function (motivo) {
        var root = document.getElementById('cliCheckoutRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'nueva-checkout';
        var inmId    = root.getAttribute('data-inmueble-id') || '';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', 'nueva-reserva');
        urlObj.searchParams.set('fragmento', fragKey);
        if (inmId) urlObj.searchParams.set('inmueble_id', inmId);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        chkdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

        return fetch(urlObj.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            cache: 'no-store'
        })
        .then(function (r) {
            return r.text().then(function (html) {
                return { ok: r.ok, status: r.status, html: html, ms: Math.round(performance.now() - t0) };
            });
        })
        .then(function (res) {
            if (!res.ok) {
                chkdbg('Error HTTP ' + res.status + ' al refrescar Checkout (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliCheckoutRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                chkdbg('Checkout actualizado asíncronamente (' + res.ms + 'ms)', 'ok');
            } else {
                chkdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliCheckoutRoot', 'warn');
            }
        })
        .catch(function (err) {
            chkdbg('Excepción recargando Checkout: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsCheckoutClienteV2Listo) {
        window.__jsCheckoutClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#chkDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#chkDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#chkDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#chkDbgCopy'))    { e.stopPropagation(); DBG.copy(); chkdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#chkDbgRefresh')) {
                e.stopPropagation();
                window.chkReloadFragment('manual-btn-dbg');
                return;
            }
        });
    }

    /* ═══ LÓGICA DE HUÉSPEDES Y ENVÍO DE RESERVA ═══ */
    document.addEventListener("DOMContentLoaded", function () {
        var root = document.getElementById('cliCheckoutRoot');
        if (!root) return;

        var btnAddGuest = document.getElementById('btnAddGuest');
        var guestList = document.getElementById('guestListContainer');
        var maxGuests = {{ $capMaxima }};
        var guestIndex = 1;

        if (btnAddGuest && guestList) {
            btnAddGuest.addEventListener('click', function() {
                var currentCount = guestList.children.length;
                if (currentCount >= maxGuests) {
                    alert('Has alcanzado la capacidad máxima del inmueble (' + maxGuests + ' personas).');
                    chkdbg('Intento de agregar huésped superando capacidad (' + maxGuests + ')', 'warn');
                    return;
                }

                var idx = guestIndex++;
                var gBox = document.createElement('div');
                gBox.className = 'guest-box mb-2 p-2 border rounded bg-white';
                gBox.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="val" style="font-size:8.5px; color:var(--ink-3);">Acompañante ${currentCount + 1}</span>
                        <button type="button" class="btn-close" style="font-size:6px;" onclick="this.closest('.guest-box').remove()"></button>
                    </div>
                    <div class="row g-2">
                        <input type="hidden" name="huespedes[${idx}][es_titular]" value="0">
                        <div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[${idx}][nombre]" placeholder="Nombres" required></div>
                        <div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[${idx}][apellidos]" placeholder="Apellidos" required></div>
                        <div class="col-5">
                            <select class="form-select form-select-sm xls-input" name="huespedes[${idx}][tipo_documento]" required>
                                <option value="CC">CC</option>
                                <option value="TI">TI</option>
                                <option value="RC">RC</option>
                                <option value="CE">CE</option>
                                <option value="PAS">PAS</option>
                            </select>
                        </div>
                        <div class="col-7"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[${idx}][numero_documento]" placeholder="N° Documento" required></div>
                    </div>
                `;
                guestList.appendChild(gBox);
                chkdbg('Formulario para acompañante añadido (total ' + (currentCount + 1) + ')', 'debug');
            });
        }

        // Cancelar (Limpia mediante fragmento asíncrono sin recargar navegador)
        var btnCancelar = document.getElementById('btnCancelarCheckout');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function(e) {
                e.preventDefault();
                chkdbg('Se inició cancelación del checkout', 'info');
                var cleanUrl = root.getAttribute('data-redirect');
                try {
                    window.history.pushState({}, document.title, cleanUrl);
                } catch (err) {}
                window.chkReloadFragment('cancelar-checkout').then(function () {
                    var inicioTab = document.querySelector('[data-bs-target="#nr-sub-inicio"]');
                    if (inicioTab) inicioTab.click();
                });
            });
        }

        // Procesar Reserva (AJAX Submit)
        var form = document.getElementById('cliCheckoutForm');
        if (!form) return;

        var errorAlert = document.getElementById('checkoutAlertError');
        var CSRF = '{{ csrf_token() }}';

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var terms = document.getElementById('termsCheck');
            if (!terms.checked) {
                alert('Debes aceptar los lineamientos y condiciones antes de confirmar.');
                chkdbg('Intento de reserva bloqueado: faltó check de términos', 'warn');
                return;
            }

            chkdbg('Iniciando confirmación de reserva (incluyendo ' + guestList.children.length + ' huéspedes)...', 'info');
            var btn = document.getElementById('btnConfirmarReserva');
            var btnTxt = btn ? btn.querySelector('.btn-txt') : null;
            var originalTxt = btnTxt ? btnTxt.innerHTML : 'Confirmar Reserva Segura';

            if (btn) {
                btn.disabled = true;
                if(btnTxt) btnTxt.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:8px; height:8px; border-width:1px;"></span> Guardando...';
            }

            if(errorAlert) errorAlert.classList.add('d-none');

            var fd = new FormData(form);
            fd.append('_token', CSRF);

            var submitUrl = form.getAttribute('data-store');
            chkdbg('POST a ' + submitUrl, 'net');

            fetch(submitUrl, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (resp) {
                return resp.json().then(function (j) {
                    return { ok: resp.ok, status: resp.status, json: j };
                }).catch(function () {
                    return { ok: resp.ok, status: resp.status, json: { message: 'Respuesta no válida del servidor (HTTP ' + resp.status + ')' } };
                });
            })
            .then(function(r) {
                var j = r.json || {};

                if (r.ok && (j.ok || j.success || j.reserva)) {
                    chkdbg('Reserva procesada con ÉXITO. Redirigiendo a Mis Reservas...', 'ok');
                    alert('¡Reserva confirmada con éxito!');
                    localStorage.setItem('rsv_cliente_panel_parent', '#cli-reservas');
                    window.location.href = root.getAttribute('data-redirect');
                    return;
                }

                chkdbg('Error del servidor: ' + (j.message || 'Sin detalles'), 'err');
                var errorMsg = '<i class="bi bi-exclamation-circle me-1"></i> <b>No se pudo completar la reserva:</b><br>';
                errorMsg += j.message || 'Error del servidor.';

                if (j.errors) {
                    errorMsg += '<ul style="margin:2px 0 0 0; padding-left:14px;">';
                    Object.values(j.errors).forEach(function(errArray) {
                        var msgs = Array.isArray(errArray) ? errArray : [errArray];
                        msgs.forEach(function(m) {
                            errorMsg += '<li>' + m + '</li>';
                            chkdbg('Detalle error: ' + m, 'err');
                        });
                    });
                    errorMsg += '</ul>';
                }

                if(errorAlert) {
                    errorAlert.innerHTML = errorMsg;
                    errorAlert.classList.remove('d-none');
                } else {
                    alert('Error:\n' + (j.message || 'Fallo al procesar.'));
                }

                if (btn) { btn.disabled = false; if(btnTxt) btnTxt.innerHTML = originalTxt; }
            })
            .catch(function(err) {
                chkdbg('Fallo de Red AJAX (No hay conexión)', 'err');
                if(errorAlert) {
                    errorAlert.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Fallo de red. Verifica tu conexión a internet.';
                    errorAlert.classList.remove('d-none');
                }
                if (btn) { btn.disabled = false; if(btnTxt) btnTxt.innerHTML = originalTxt; }
            });
        });
    });

    chkdbg('Módulo Checkout cargado y activo (CHK-v2-DBG)', 'ok');
})();
</script>
