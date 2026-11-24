{{-- partials/nueva-reserva/checkout.blade.php · Cliente · Carrito de Compras Reactivo (CHK-v2.4-DBG) --}}
<div id="sheet-checkout-cliente">

    {{-- CHK-v2.4-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="chkDbgToggle" title="Debug Checkout (CHK-v2.4-DBG)">
        <span class="chk-dbg-led" id="chkDbgLed"></span>DBG<span class="chk-dbg-badge" id="chkDbgBadge">0</span>
    </div>
    <div id="chkDbgPanel" role="dialog" aria-label="Panel de debug del Checkout">
        <div class="chk-dbg-header" id="chkDbgHeader">
            <span>DEBUG · Checkout Cliente · CHK-v2.4-DBG</span>
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

        $rutaStoreHuesped = '';
        foreach (['rsv.reserva-huespedes.store', 'rsv.huespedes.store-ajax', 'rsv.huespedes.store', 'rsv.cliente.huespedes.store', 'huespedes.store'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) { $rutaStoreHuesped = route($rn); break; }
        }
        if (!$rutaStoreHuesped) {
            $rutaStoreHuesped = url('/rsv/reserva-huespedes');
        }

        $rutaFragmento = '';
        foreach (['rsv.cliente.nueva-reserva.checkout.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        // ── URL limpia de la vista principal del Portal del Cliente (NUNCA la ruta /fragmento) ──
        $rutaPortalCliente = '';
        foreach (['rsv.cliente.dashboard', 'rsv.cliente.index', 'rsv.cliente.portal', 'cliente.dashboard'] as $rnPortal) {
            if (\Illuminate\Support\Facades\Route::has($rnPortal)) {
                $rutaPortalCliente = route($rnPortal);
                break;
            }
        }
        if (!$rutaPortalCliente) {
            // Si estamos en una petición AJAX al fragmento, quitar "/fragmento..." de la ruta actual
            $pathActual = request()->path();
            $pathLimpio = preg_replace('#/fragmento.*$#i', '', $pathActual);
            $rutaPortalCliente = url('/' . ltrim($pathLimpio, '/'));
        }

        $statusPendienteId = class_exists(\App\Models\Rsv\Status::class)
            ? (\App\Models\Rsv\Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])->first()->id ?? 1) : 1;
        $origenDefectoId = class_exists(\App\Models\Rsv\OrigenReserva::class)
            ? (\App\Models\Rsv\OrigenReserva::first()->id ?? 1) : 1;

        // ── Datos del usuario autenticado desde columnas exactas: name y nit ──
        $authUser = \Illuminate\Support\Facades\Auth::user();
        $selfNombre = '';
        $selfApellidos = '';
        $selfTipoDoc = 'CC';
        $selfNumDoc = '';

        if ($authUser) {
            $partesNombre = preg_split('/\s+/', trim((string) ($authUser->name ?? '')), -1, PREG_SPLIT_NO_EMPTY);
            $cnt = count($partesNombre);
            if ($cnt === 1) {
                $selfNombre = $partesNombre[0];
            } elseif ($cnt === 2) {
                $selfNombre = $partesNombre[0];
                $selfApellidos = $partesNombre[1];
            } elseif ($cnt >= 3) {
                $selfNombre = implode(' ', array_slice($partesNombre, 0, $cnt - 2));
                $selfApellidos = implode(' ', array_slice($partesNombre, -2));
            }

            $rawTipo = strtoupper(trim((string) ($authUser->tipo_documento ?? 'CC')));
            $selfTipoDoc = in_array($rawTipo, ['CC', 'CE', 'PAS']) ? $rawTipo : 'CC';
            $selfNumDoc = trim((string) ($authUser->nit ?? ''));
        }

        $selfGuestData = [
            'nombre' => $selfNombre,
            'apellidos' => $selfApellidos,
            'tipo_documento' => $selfTipoDoc,
            'numero_documento' => $selfNumDoc,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border" id="cliCheckoutRoot"
         data-redirect="{{ $rutaPortalCliente }}"
         data-frag="{{ $rutaFragmento ?: route('rsv.cliente.fragmento') }}"
         data-fragkey="nueva-checkout"
         data-inmueble-id="{{ $inmId ?? '' }}"
         data-max-guests="{{ $capMaxima }}"
         data-csrf="{{ csrf_token() }}"
         data-store-huesped="{{ $rutaStoreHuesped }}"
         data-user-id="{{ $authUser?->id ?? '' }}"
         data-self-guest='@json($selfGuestData)'
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

            .reservas-soft .btn-self-quick { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:7.5px; font-weight:700; padding:2px 8px; border-radius:4px; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:4px; }
            .reservas-soft .btn-self-quick:hover { background:#e3f1e9; color:#2e5c43; border-color:#b8d8c5; }
            .reservas-soft .btn-self-quick.is-applied { background:var(--accent); color:#fff; border-color:var(--accent); }

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

            .xls-input { font-size: 8.5px; color: var(--ink-2); border: 1px solid var(--grid-2); border-radius: 4px; padding: 4px 8px; outline: 0; box-shadow: none !important; transition: background-color 0.25s, border-color 0.25s;}
            .xls-input:focus { border-color: var(--accent-bd); background: #fafcfe; }
            .xls-input.self-filled { background: #f0f7f2 !important; border-color: #b8d8c5 !important; }
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
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1 flex-wrap">
                                        <h6 class="val mb-0"><i class="bi bi-people-fill me-1" style="color:var(--accent);"></i> Registro de Huéspedes</h6>
                                        @if($authUser)
                                            <button type="button" class="btn-self-quick" id="btnFillSelfGuest" title="Cargar mis datos personales ({{ $authUser->name }} - {{ $authUser->nit }}) como titular">
                                                <i class="bi bi-person-badge-fill"></i> <span class="self-btn-txt">Usar mis datos</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="sub mb-3" style="font-size:7.5px;">Asegura tu cupo cargando tus datos ahora y registra a tus acompañantes después desde <b>Mis Reservas</b>. Capacidad: <b>{{ $capMaxima }}</b> personas.</div>

                                    <div id="guestListContainer">
                                        <div class="guest-box mb-2 p-2 border rounded" style="background:var(--head);" id="titularGuestBox">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="val" style="font-size:8.5px; color:var(--accent);">Huésped 1 (Titular)</span>
                                                <span class="sub" id="titularSelfBadge" style="display:none; color:var(--accent); font-weight:700;"><i class="bi bi-check-circle-fill"></i> Mis datos</span>
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
                                        <i class="bi bi-person-plus"></i> Añadir Acompañante (opcional ahora)
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

    /* ═══ PANEL DE DEBUG LOCAL CHK-v2.4-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ChkDbg && window.ChkDbg.alive && window.ChkDbg.alive())
        ? window.ChkDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-checkout-cliente') || document.body;
                if (!$('chkDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'chkDbgToggle'; t.title = 'Debug Checkout (CHK-v2.4-DBG)';
                    t.innerHTML = '<span class="chk-dbg-led" id="chkDbgLed"></span>DBG<span class="chk-dbg-badge" id="chkDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('chkDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'chkDbgPanel';
                    p.innerHTML = '<div class="chk-dbg-header" id="chkDbgHeader">' +
                        '<span>DEBUG · Checkout Cliente · CHK-v2.4-DBG</span>' +
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
                    var txt = '=== DEBUG Checkout CHK-v2.4-DBG ===\n';
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
                window.__chkGuestIndex = 1;
                chkdbg('Checkout actualizado asíncronamente (' + res.ms + 'ms)', 'ok');
            } else {
                chkdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliCheckoutRoot', 'warn');
            }
        })
        .catch(function (err) {
            chkdbg('Excepción recargando Checkout: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    /* ═══ GUARDAR HUÉSPEDES EN rsv.reserva-huespedes.store TRAS CREAR RESERVA ═══ */
    function registrarHuespedesEnReserva(root, form, reservaId, CSRF) {
        var storeHuespedUrl = root ? (root.getAttribute('data-store-huesped') || '') : '';
        var userId = root ? (root.getAttribute('data-user-id') || '') : '';
        if (!storeHuespedUrl || !reservaId) return Promise.resolve();

        var boxes = form.querySelectorAll('#guestListContainer .guest-box');
        var promesas = [];

        boxes.forEach(function (box, idx) {
            var nombre = (box.querySelector('input[name$="[nombre]"]')?.value || '').trim();
            var apellidos = (box.querySelector('input[name$="[apellidos]"]')?.value || '').trim();
            var tipoDoc = (box.querySelector('select[name$="[tipo_documento]"]')?.value || 'CC').trim();
            var numDoc = (box.querySelector('input[name$="[numero_documento]"]')?.value || '').trim();
            var esTitular = (box.querySelector('input[name$="[es_titular]"]')?.value || (idx === 0 ? '1' : '0'));

            if (!nombre || !numDoc) return;

            var hFd = new FormData();
            hFd.append('_token', CSRF);
            hFd.append('_ajax', '1');
            hFd.append('id_rsv_reservas', reservaId);
            hFd.append('id_rsv_reserva', reservaId);
            hFd.append('reserva_id', reservaId);
            if (userId) {
                hFd.append('id_user_registrador', userId);
                hFd.append('id_user', userId);
            }
            hFd.append('es_titular', esTitular);
            hFd.append('nombre', nombre);
            hFd.append('apellidos', apellidos || 'Titular');
            hFd.append('tipo_documento', tipoDoc);
            hFd.append('numero_documento', numDoc);

            chkdbg('POST Huésped #' + (idx + 1) + ' (' + nombre + ' · NIT/Doc: ' + numDoc + ') → ' + storeHuespedUrl, 'net');

            promesas.push(
                fetch(storeHuespedUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json, text/html'
                    },
                    body: hFd
                }).then(function (hr) {
                    if (hr.ok) {
                        chkdbg('Huésped #' + (idx + 1) + ' guardado OK (HTTP ' + hr.status + ')', 'ok');
                    } else {
                        chkdbg('Advertencia al guardar huésped #' + (idx + 1) + ' (HTTP ' + hr.status + ')', 'warn');
                    }
                }).catch(function (herr) {
                    chkdbg('Error red guardando huésped #' + (idx + 1) + ': ' + (herr.message || ''), 'warn');
                })
            );
        });

        return Promise.all(promesas);
    }

    /* ═══ DELEGACIÓN GLOBAL DE CLICS Y ENVÍOS (sobrevive a swaps AJAX) ═══ */
    window.__chkGuestIndex = window.__chkGuestIndex || 1;

    if (!window.__jsCheckoutClienteV4Listo) {
        window.__jsCheckoutClienteV4Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            // Controles Debug
            if (t.closest('#chkDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#chkDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#chkDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#chkDbgCopy'))    { e.stopPropagation(); DBG.copy(); chkdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#chkDbgRefresh')) {
                e.stopPropagation();
                window.chkReloadFragment('manual-btn-dbg');
                return;
            }

            var root = document.getElementById('cliCheckoutRoot');
            if (!root) return;

            // ── ACCIÓN RÁPIDA: AGREGARME A MÍ COMO TITULAR (name + nit) ──
            var btnSelf = t.closest('#btnFillSelfGuest');
            if (btnSelf) {
                e.preventDefault();
                var rawSelf = root.getAttribute('data-self-guest') || '{}';
                var selfData = {};
                try { selfData = JSON.parse(rawSelf); } catch (err) {}

                var inpNombre = root.querySelector('input[name="huespedes[0][nombre]"]');
                var inpApellidos = root.querySelector('input[name="huespedes[0][apellidos]"]');
                var selTipo = root.querySelector('select[name="huespedes[0][tipo_documento]"]');
                var inpDoc = root.querySelector('input[name="huespedes[0][numero_documento]"]');

                if (inpNombre && selfData.nombre) inpNombre.value = selfData.nombre;
                if (inpApellidos && selfData.apellidos) inpApellidos.value = selfData.apellidos;
                if (selTipo && selfData.tipo_documento) selTipo.value = selfData.tipo_documento;
                if (inpDoc && selfData.numero_documento) inpDoc.value = selfData.numero_documento;

                [inpNombre, inpApellidos, selTipo, inpDoc].forEach(function (el) {
                    if (!el) return;
                    el.classList.add('self-filled');
                    setTimeout(function () { el.classList.remove('self-filled'); }, 1400);
                });

                btnSelf.classList.add('is-applied');
                var txtSpan = btnSelf.querySelector('.self-btn-txt');
                if (txtSpan) txtSpan.textContent = '¡Datos cargados!';

                var badge = document.getElementById('titularSelfBadge');
                if (badge) badge.style.display = 'inline';

                chkdbg('Autocompletado rápido Titular: ' + (selfData.nombre || '') + ' ' + (selfData.apellidos || '') + ' · NIT: ' + (selfData.numero_documento || 'vacío'), 'ok');

                if (inpNombre && !inpNombre.value.trim()) { inpNombre.focus(); }
                else if (inpApellidos && !inpApellidos.value.trim()) { inpApellidos.focus(); }
                else if (inpDoc && !inpDoc.value.trim()) {
                    inpDoc.focus();
                    chkdbg('La columna nit de este usuario está vacía; complétala manualmente', 'warn');
                }
                return;
            }

            // ── AÑADIR ACOMPAÑANTE ──
            if (t.closest('#btnAddGuest')) {
                e.preventDefault();
                var guestList = document.getElementById('guestListContainer');
                if (!guestList) return;

                var maxGuests = parseInt(root.getAttribute('data-max-guests') || '6', 10);
                var currentCount = guestList.children.length;
                if (currentCount >= maxGuests) {
                    alert('Has alcanzado la capacidad máxima del inmueble (' + maxGuests + ' personas).');
                    chkdbg('Intento de agregar huésped superando capacidad (' + maxGuests + ')', 'warn');
                    return;
                }

                var idx = window.__chkGuestIndex++;
                var gBox = document.createElement('div');
                gBox.className = 'guest-box mb-2 p-2 border rounded bg-white';
                gBox.innerHTML =
                    '<div class="d-flex justify-content-between align-items-center mb-1">' +
                        '<span class="val" style="font-size:8.5px; color:var(--ink-3);">Acompañante ' + (currentCount + 1) + '</span>' +
                        '<button type="button" class="btn-close" style="font-size:6px;" onclick="this.closest(\'.guest-box\').remove()"></button>' +
                    '</div>' +
                    '<div class="row g-2">' +
                        '<input type="hidden" name="huespedes[' + idx + '][es_titular]" value="0">' +
                        '<div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[' + idx + '][nombre]" placeholder="Nombres" required></div>' +
                        '<div class="col-6"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[' + idx + '][apellidos]" placeholder="Apellidos" required></div>' +
                        '<div class="col-5">' +
                            '<select class="form-select form-select-sm xls-input" name="huespedes[' + idx + '][tipo_documento]" required>' +
                                '<option value="CC">CC</option>' +
                                '<option value="TI">TI</option>' +
                                '<option value="RC">RC</option>' +
                                '<option value="CE">CE</option>' +
                                '<option value="PAS">PAS</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-7"><input type="text" class="form-control form-control-sm xls-input" name="huespedes[' + idx + '][numero_documento]" placeholder="N° Documento" required></div>' +
                    '</div>';
                guestList.appendChild(gBox);
                chkdbg('Formulario para acompañante añadido (total ' + (currentCount + 1) + ')', 'debug');
                return;
            }

            // ── CANCELAR CHECKOUT ──
            if (t.closest('#btnCancelarCheckout')) {
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
                return;
            }
        });

        // ── PROCESAR RESERVA + REGISTRAR HUÉSPEDES (AJAX SUBMIT DELEGADO) ──
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'cliCheckoutForm') return;
            e.preventDefault();

            var root = document.getElementById('cliCheckoutRoot');
            var guestList = document.getElementById('guestListContainer');
            var errorAlert = document.getElementById('checkoutAlertError');
            var CSRF = root ? (root.getAttribute('data-csrf') || '') : '';

            var terms = document.getElementById('termsCheck');
            if (terms && !terms.checked) {
                alert('Debes aceptar los lineamientos y condiciones antes de confirmar.');
                chkdbg('Intento de reserva bloqueado: faltó check de términos', 'warn');
                return;
            }

            var totalHuespedes = guestList ? guestList.children.length : 1;
            chkdbg('Iniciando confirmación de reserva (incluyendo ' + totalHuespedes + ' huésped(es))...', 'info');

            var btn = document.getElementById('btnConfirmarReserva');
            var btnTxt = btn ? btn.querySelector('.btn-txt') : null;
            var originalTxt = btnTxt ? btnTxt.innerHTML : 'Confirmar Reserva Segura';

            if (btn) {
                btn.disabled = true;
                if (btnTxt) btnTxt.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:8px; height:8px; border-width:1px;"></span> Guardando...';
            }

            if (errorAlert) errorAlert.classList.add('d-none');

            var fd = new FormData(form);
            if (CSRF) fd.set('_token', CSRF);

            var submitUrl = form.getAttribute('data-store');
            chkdbg('POST Reserva → ' + submitUrl, 'net');

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
            .then(function (r) {
                var j = r.json || {};

                if (r.ok && (j.ok || j.success || j.reserva || j.id)) {
                    var reservaCreadaId = (j.reserva && j.reserva.id) || j.reserva_id || j.id || null;
                    var yaGuardoHuespedes = !!(j.huespedes_guardados || (j.reserva && Array.isArray(j.reserva.huespedes) && j.reserva.huespedes.length > 0));

                    var pasoHuespedes = (!yaGuardoHuespedes && reservaCreadaId)
                        ? registrarHuespedesEnReserva(root, form, reservaCreadaId, CSRF)
                        : Promise.resolve();

                    return pasoHuespedes.then(function () {
                        chkdbg('Reserva y huéspedes procesados con ÉXITO. Redirigiendo a Mis Reservas...', 'ok');
                        alert('¡Reserva confirmada con éxito!');
                        localStorage.setItem('rsv_cliente_panel_parent', '#cli-reservas');
                        localStorage.setItem('rsv_cliente_panel_child', '#mr-sub-inicio');

                        // Redirigir siempre a la URL completa del Portal (evitando endpoints /fragmento)
                        var destino = (root && root.getAttribute('data-redirect')) || window.location.origin + window.location.pathname;
                        destino = destino.replace(/\/fragmento.*$/i, '').split('?')[0];
                        if (reservaCreadaId) {
                            destino += '?reserva_id=' + encodeURIComponent(reservaCreadaId);
                        }
                        window.location.href = destino;
                    });
                }

                chkdbg('Error del servidor: ' + (j.message || 'Sin detalles'), 'err');
                var errorMsg = '<i class="bi bi-exclamation-circle me-1"></i> <b>No se pudo completar la reserva:</b><br>';
                errorMsg += j.message || 'Error del servidor.';

                if (j.errors) {
                    errorMsg += '<ul style="margin:2px 0 0 0; padding-left:14px;">';
                    Object.values(j.errors).forEach(function (errArray) {
                        var msgs = Array.isArray(errArray) ? errArray : [errArray];
                        msgs.forEach(function (m) {
                            errorMsg += '<li>' + m + '</li>';
                            chkdbg('Detalle error: ' + m, 'err');
                        });
                    });
                    errorMsg += '</ul>';
                }

                if (errorAlert) {
                    errorAlert.innerHTML = errorMsg;
                    errorAlert.classList.remove('d-none');
                } else {
                    alert('Error:\n' + (j.message || 'Fallo al procesar.'));
                }

                if (btn) { btn.disabled = false; if (btnTxt) btnTxt.innerHTML = originalTxt; }
            })
            .catch(function (err) {
                chkdbg('Fallo de Red AJAX (No hay conexión)', 'err');
                if (errorAlert) {
                    errorAlert.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Fallo de red. Verifica tu conexión a internet.';
                    errorAlert.classList.remove('d-none');
                }
                if (btn) { btn.disabled = false; if (btnTxt) btnTxt.innerHTML = originalTxt; }
            });
        });
    }

    chkdbg('Módulo Checkout cargado y activo (CHK-v2.4-DBG · Redirección limpia al portal)', 'ok');
})();
</script>
