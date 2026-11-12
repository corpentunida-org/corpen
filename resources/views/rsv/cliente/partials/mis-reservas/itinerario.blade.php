{{-- partials/mis-reservas/itinerario.blade.php · Cliente · Guía de Viaje Reactiva sin Reload (IT-v2-DBG) --}}
<div id="sheet-itinerario-cliente">

    {{-- IT-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="itDbgToggle" title="Debug Itinerario (IT-v2-DBG)">
        <span class="it-dbg-led" id="itDbgLed"></span>DBG<span class="it-dbg-badge" id="itDbgBadge">0</span>
    </div>
    <div id="itDbgPanel" role="dialog" aria-label="Panel de debug de Itinerario">
        <div class="it-dbg-header" id="itDbgHeader">
            <span>DEBUG · Itinerario Cliente · IT-v2-DBG</span>
            <span class="it-dbg-controls">
                <button type="button" class="it-dbg-btn" id="itDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="it-dbg-btn" id="itDbgCopy">Copiar</button>
                <button type="button" class="it-dbg-btn" id="itDbgClear">Limpiar</button>
                <button type="button" class="it-dbg-btn" id="itDbgClose">✕</button>
            </span>
        </div>
        <div id="itDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $rid = request('reserva_id') ?? request('id_reserva');
        $misReservas = collect();
        $itErrores = [];

        // 1. Cargamos el listado ligero de reservas del cliente para el selector superior
        if ($uid) {
            try {
                $misReservas = \App\Models\Rsv\Reserva::with([
                        'inmueble.multimedia',
                        'inmueble.tipoInmueble',
                        'inmueble.latestTarifa',
                        'status',
                        'origen',
                        'huespedes',
                        'transacciones',
                    ])
                    ->where('id_user', $uid)
                    ->orderByDesc('fecha_inicio')
                    ->limit(25)
                    ->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('cli.itin.list: '.$e->getMessage());
                $itErrores[] = 'Listado reservas: ' . $e->getMessage();
            }
        }

        // 2. Respetamos $reserva si viene inyectada desde ClientePortalController@misReservasItinerarioFragmento
        $reservaActual = (isset($reserva) && $reserva instanceof \App\Models\Rsv\Reserva) ? $reserva : null;

        if (!$reservaActual && is_numeric($rid)) {
            $reservaActual = $misReservas->firstWhere('id', (int) $rid);
        }

        // Auto-selección de la próxima reserva activa si no se envió reserva_id en la carga inicial
        if (!$reservaActual && $misReservas->isNotEmpty()) {
            $reservaActual = $misReservas->first(function ($r) {
                $stName = mb_strtolower(optional($r->status)->name ?? '');
                return !str_contains($stName, 'cancela') && !str_contains($stName, 'anula');
            }) ?? $misReservas->first();
        }

        // Asegurar relaciones clave si $reserva vino del controlador sin multimedia/huespedes/latestTarifa
        if ($reservaActual) {
            try {
                $reservaActual->loadMissing([
                    'inmueble.multimedia',
                    'inmueble.latestTarifa',
                    'status',
                    'huespedes',
                ]);
            } catch (\Throwable $e) {}
        }

        // 3. Respetamos $eventos si viene desde ClientePortalController@misReservasItinerarioFragmento
        $eventosList = (isset($eventos) && $eventos instanceof \Illuminate\Support\Collection) ? $eventos : collect();

        if ($eventosList->isEmpty() && $reservaActual) {
            try {
                if (method_exists($reservaActual, 'itinerarios')) {
                    $eventosList = $reservaActual->itinerarios()->orderBy('created_at')->get();
                } elseif (method_exists($reservaActual, 'itinerarioEventos')) {
                    $eventosList = $reservaActual->itinerarioEventos()->get();
                }
            } catch (\Throwable $e) {
                $itErrores[] = 'Eventos: ' . $e->getMessage();
            }
        }

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.mis-reservas.itinerario.fragmento', 'rsv.cliente.itinerario.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob')) return 'success';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'cancela') || str_contains($s, 'anula')) return 'danger';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            return 'secondary';
        };

        $inm = $reservaActual?->inmueble;
        $huespedesCnt = ($reservaActual && $reservaActual->relationLoaded('huespedes')) ? $reservaActual->huespedes->count() : 0;
        $capMax = (int) ($inm->capacidad_maxima ?? 6);

        $itDbgData = [
            'version'     => 'IT-v2-DBG',
            'controlador' => 'ClientePortalController@misReservasItinerarioFragmento',
            'vista'       => 'partials/mis-reservas/itinerario.blade.php',
            'total_res'   => $misReservas->count(),
            'reserva_id'  => $reservaActual ? $reservaActual->id : null,
            'reserva_cod' => $reservaActual ? $reservaActual->codigo_reserva : null,
            'inmueble'    => $inm ? $inm->name : null,
            'eventos_bd'  => $eventosList->count(),
            'huespedes'   => $huespedesCnt . '/' . $capMax,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $itErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliItinRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="reservas-itinerario"
         data-reserva-id="{{ $reservaActual ? $reservaActual->id : '' }}"
         data-reserva-cod="{{ $reservaActual ? $reservaActual->codigo_reserva : '' }}"
         data-inmueble="{{ $inm ? $inm->name : '' }}"
         data-eventos="{{ $eventosList->count() }}"
         data-cupo="{{ $huespedesCnt }}/{{ $capMax }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:10.5px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.4; }

            /* ── SELECTOR MINIMALISTA DE RESERVA ── */
            .it-select-res {
                font-size:9px; font-weight:600; color:var(--ink-2); background:var(--head);
                border:1px solid var(--grid-2); border-radius:6px; padding:4px 10px;
                outline:0; cursor:pointer; max-width:260px;
            }
            .it-select-res:focus { border-color:var(--accent-bd); background:#fff; }

            /* ── CABECERA DE VIAJE (HERO COMPACTO) ── */
            .it-summary-card {
                background:#fff; border:1px solid var(--grid-2); border-radius:10px;
                padding:14px 16px; margin-bottom:14px;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:14px;
            }
            .it-thumb {
                width:48px; height:48px; border-radius:8px; overflow:hidden;
                background:var(--head); border:1px solid var(--grid-2); flex-shrink:0;
                display:flex; align-items:center; justify-content:center; color:var(--ink-3);
            }
            .it-thumb img { width:100%; height:100%; object-fit:cover; }

            /* ── CAJAS DE CHECK-IN / CHECK-OUT ── */
            .it-date-strip {
                display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));
                gap:8px; margin-bottom:16px;
            }
            .it-date-box {
                background:var(--head); border:1px solid var(--grid);
                border-radius:8px; padding:10px 12px;
            }

            /* ── BOTONES DISCRETOS ── */
            .it-icon-btn {
                height:28px; padding:0 10px; border-radius:6px; border:1px solid var(--grid-2);
                background:#fff; color:var(--ink-2); display:inline-flex;
                align-items:center; gap:5px; font-size:8.5px; font-weight:700;
                cursor:pointer; transition:all .15s; text-decoration:none;
            }
            .it-icon-btn:hover { background:var(--accent-bg); border-color:var(--accent-bd); color:var(--accent); text-decoration:none; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px;
            }
            .reservas-soft .pill-success { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-danger  { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            /* ── LÍNEA DE TIEMPO DEL ITINERARIO (CLICABLE) ── */
            .it-timeline { position:relative; padding-left:22px; margin:8px 0; }
            .it-timeline::before {
                content:''; position:absolute; left:6px; top:6px; bottom:6px;
                width:1.5px; background:var(--grid-2);
            }
            .it-ev-item { position:relative; margin-bottom:8px; }
            .it-ev-item:last-child { margin-bottom:0; }
            .it-ev-dot {
                position:absolute; left:-22px; top:12px; width:11px; height:11px;
                border-radius:50%; background:#fff; border:2px solid var(--ink-3); z-index:2;
            }
            .it-ev-dot.dot-checkin  { background:#10b981; border-color:#dcfce7; }
            .it-ev-dot.dot-event    { background:#3b82f6; border-color:#dbeafe; }
            .it-ev-dot.dot-checkout { background:#64748b; border-color:#e2e8f0; }

            .it-ev-card {
                background:#fff; border:1px solid var(--grid); border-radius:8px;
                padding:9px 12px; cursor:pointer; transition:all .15s ease;
            }
            .it-ev-card:hover { border-color:var(--accent-bd); background:#fafdfc; }
            .it-ev-detail {
                display:none; margin-top:6px; padding-top:6px;
                border-top:1px dashed var(--grid-2); font-size:8.5px; color:var(--ink);
            }
            .it-ev-card.open .it-ev-detail { display:block; }
            .it-ev-card.open .it-ev-chevron { transform:rotate(90deg); color:var(--accent); }
            .it-ev-chevron { font-size:10px; color:var(--ink-4); transition:transform .15s; }

            /* ── MAPA RESPONSIVO EN MODAL ── */
            .it-map-wrap { position:relative; width:100%; padding-top:65%; border-radius:8px; overflow:hidden; border:1px solid var(--grid-2); }
            .it-map-wrap iframe { position:absolute; inset:0; width:100%; height:100%; border:0; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #itDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #itDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .it-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .it-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:itDbgBlink 1s steps(2,start) infinite; }
            @keyframes itDbgBlink { 50% { opacity:.35; } }
            .it-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #itDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .it-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .it-dbg-controls { display:flex; gap:4px; }
            .it-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .it-dbg-btn:hover { background:#475569; }
            #itDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .it-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .it-dbg-time { color:#64748b; margin-right:4px; }
            .it-dbg-info { color:#93c5fd; } .it-dbg-ok { color:#86efac; } .it-dbg-warn { color:#fde68a; } .it-dbg-err { color:#fca5a5; } .it-dbg-net { color:#c4b5fd; } .it-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- BARRA SUPERIOR: TÍTULO + SELECTOR REACTIVO DE RESERVA (SIN RELOAD) --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="val" style="font-size:11px;">
                    <i class="bi bi-compass text-success me-1"></i> Guía de Viaje e Itinerario
                </span>
                @if($reservaActual && !$rid)
                    <span class="sub">(Mostrando última reserva activa)</span>
                @endif
            </div>

            @if($misReservas->count() > 0)
                <div class="d-flex align-items-center gap-2">
                    <span class="sub d-none d-sm-inline">Reserva:</span>
                    <select class="it-select-res" id="itSelectReserva">
                        @foreach($misReservas as $mr)
                            @php
                                $mrFi = $mr->fecha_inicio ? \Carbon\Carbon::parse($mr->fecha_inicio)->format('d/m/y') : '—';
                            @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    {{ ($reservaActual && $reservaActual->id === $mr->id) ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} ({{ $mrFi }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if(!$reservaActual)
            <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                <i class="bi bi-compass text-muted d-block mb-2" style="font-size:24px;"></i>
                <span class="sub d-block mb-2">Aún no tienes reservas para generar un itinerario.</span>
                <button type="button" class="it-icon-btn js-it-go-nueva">
                    <i class="bi bi-plus-circle"></i> Crear Reserva
                </button>
            </div>
        @else
            @php
                $thumb = ($inm && $inm->relationLoaded('multimedia') && $inm->multimedia->first())
                    ? $inm->multimedia->first()->url_archivo
                    : null;
                $fi = $reservaActual->fecha_inicio ? \Carbon\Carbon::parse($reservaActual->fecha_inicio) : null;
                $ff = $reservaActual->fecha_fin ? \Carbon\Carbon::parse($reservaActual->fecha_fin) : null;
                $noches = ($fi && $ff) ? max(1, (int) $fi->diffInDays($ff)) : 0;
                $st = optional($reservaActual->status)->name ?? 'Pendiente';
                $stGroup = $pillSt($st);
                $mapQuery = urlencode($inm->ubicacion ?? $inm->city ?? $inm->name ?? 'Colombia');
                $tarifa = $inm?->latestTarifa;

                $hoy = \Carbon\Carbon::today();
                $diasParaLlegada = $fi ? (int) $hoy->diffInDays($fi->copy()->startOfDay(), false) : null;
            @endphp

            {{-- RESUMEN COMPACTO DE LA PROPIEDAD Y ACCESOS DISCRETOS --}}
            <div class="it-summary-card">
                <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                    <div class="it-thumb">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $inm->name ?? 'Inmueble' }}">
                        @else
                            <i class="bi bi-house-door"></i>
                        @endif
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="val text-truncate" style="font-size:12px;">{{ $inm->name ?? 'Inmueble' }}</span>
                            <span class="sub">#{{ $reservaActual->codigo_reserva }}</span>
                            <span class="pill pill-{{ $stGroup }}">{{ $st }}</span>
                        </div>
                        <div class="sub text-truncate mt-1">
                            <i class="bi bi-geo-alt text-danger me-1"></i>{{ $inm->city ?? $inm->ubicacion ?? 'Ubicación confirmada' }}
                            <span class="mx-1">·</span> {{ $huespedesCnt }}/{{ $capMax }} huéspedes
                            @if(!is_null($diasParaLlegada))
                                <span class="mx-1">·</span>
                                @if($diasParaLlegada > 0)
                                    <b style="color:var(--accent);">Faltan {{ $diasParaLlegada }} día(s)</b>
                                @elseif($diasParaLlegada === 0)
                                    <b class="text-success">¡Hoy inicia tu estadía!</b>
                                @else
                                    <span>Estadía realizada</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Botones minimalistas (Mapa, Lineamientos, Huéspedes) --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="it-icon-btn js-it-open-modal"
                            data-modal-label="Mapa GPS ({{ $inm->name ?? 'Inmueble' }})"
                            data-bs-toggle="modal" data-bs-target="#itModalMapa_{{ $reservaActual->id }}">
                        <i class="bi bi-geo-alt-fill text-danger"></i> Cómo llegar
                    </button>
                    <button type="button" class="it-icon-btn js-it-open-modal"
                            data-modal-label="Reglas y Lineamientos (#{{ $reservaActual->codigo_reserva }})"
                            data-bs-toggle="modal" data-bs-target="#itModalReglas_{{ $reservaActual->id }}">
                        <i class="bi bi-shield-check text-success"></i> Reglas
                    </button>
                    <button type="button" class="it-icon-btn js-it-go-huespedes"
                            data-reserva-id="{{ $reservaActual->id }}"
                            data-reserva-cod="{{ $reservaActual->codigo_reserva }}"
                            title="Ir a gestionar acompañantes">
                        <i class="bi bi-people"></i> ({{ $huespedesCnt }})
                    </button>
                </div>
            </div>

            {{-- FRANJA DE HORARIOS CLAVE (CHECK-IN / DURACIÓN / CHECK-OUT) --}}
            <div class="it-date-strip">
                <div class="it-date-box">
                    <div class="lbl">Llegada · Check-in</div>
                    <div class="val mt-1">{{ $fi ? $fi->format('d M Y') : '—' }}</div>
                    <div class="sub"><i class="bi bi-clock me-1"></i>Desde las 3:00 PM</div>
                </div>
                <div class="it-date-box">
                    <div class="lbl">Salida · Check-out</div>
                    <div class="val mt-1">{{ $ff ? $ff->format('d M Y') : '—' }}</div>
                    <div class="sub"><i class="bi bi-clock me-1"></i>Hasta la 1:00 PM</div>
                </div>
                <div class="it-date-box">
                    <div class="lbl">Duración Total</div>
                    <div class="val mt-1" style="color:var(--accent);">{{ $noches }} {{ $noches === 1 ? 'noche' : 'noches' }}</div>
                    <div class="sub">Máx. permitido: {{ $tarifa->dias_maximos ?? 5 }} días</div>
                </div>
            </div>

            {{-- CRONOGRAMA DE LA ESTADÍA (Hitos base + ItinerarioEvento de BD) --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="lbl">Cronograma de Actividades y Logística</span>
                <span class="sub">{{ $eventosList->count() }} actividad(es) adicional(es)</span>
            </div>

            <div class="it-timeline">
                {{-- Hito 1: Ingreso y Entrega de Llaves --}}
                <div class="it-ev-item">
                    <div class="it-ev-dot dot-checkin"></div>
                    <div class="it-ev-card js-it-ev-toggle" data-ev-title="Check-in y Entrega de Llaves">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="val">Check-in y Entrega de Llaves</span>
                                <span class="sub d-block">
                                    {{ $fi ? $fi->format('d/m/Y') : 'Día 1' }} · 03:00 PM
                                </span>
                            </div>
                            <i class="bi bi-chevron-right it-ev-chevron"></i>
                        </div>
                        <div class="it-ev-detail">
                            Presentación del titular ({{ optional($reservaActual->user)->name ?? 'Asociado' }}), verificación de acompañantes registrados y firma del acta de entrega del inmueble en óptimas condiciones.
                        </div>
                    </div>
                </div>

                {{-- Eventos personalizados cargados desde ItinerarioEvento --}}
                @foreach($eventosList as $ev)
                    @php
                        $evTitulo = $ev->titulo ?? $ev->nombre ?? $ev->evento ?? ('Actividad #' . $ev->id);
                        $evFecha  = $ev->fecha ?? $ev->fecha_evento ?? $ev->fecha_inicio ?? null;
                        $evHora   = $ev->hora ?? null;
                        $evDesc   = $ev->descripcion ?? $ev->detalle ?? $ev->comentario ?? null;
                    @endphp
                    <div class="it-ev-item">
                        <div class="it-ev-dot dot-event"></div>
                        <div class="it-ev-card js-it-ev-toggle" data-ev-title="{{ $evTitulo }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="val">{{ $evTitulo }}</span>
                                    <span class="sub d-block">
                                        @if($evFecha)
                                            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($evFecha)->format('d/m/Y') }}
                                        @endif
                                        @if($evHora) · <i class="bi bi-clock me-1"></i>{{ $evHora }} @endif
                                    </span>
                                </div>
                                @if($evDesc)
                                    <i class="bi bi-chevron-right it-ev-chevron"></i>
                                @endif
                            </div>
                            @if($evDesc)
                                <div class="it-ev-detail">{{ $evDesc }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Hito Final: Salida e Inventario --}}
                <div class="it-ev-item">
                    <div class="it-ev-dot dot-checkout"></div>
                    <div class="it-ev-card js-it-ev-toggle" data-ev-title="Check-out y Devolución del Inmueble">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="val">Check-out y Devolución del Inmueble</span>
                                <span class="sub d-block">
                                    {{ $ff ? $ff->format('d/m/Y') : 'Último día' }} · 01:00 PM
                                </span>
                            </div>
                            <i class="bi bi-chevron-right it-ev-chevron"></i>
                        </div>
                        <div class="it-ev-detail">
                            Revisión del estado del apartamento, firma del acta de salida y entrega de llaves al encargado local.
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL LIGERO: UBICACIÓN Y RUTAS GPS --}}
            <div class="modal fade" id="itModalMapa_{{ $reservaActual->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content reservas-soft" style="border:none; border-radius:12px; overflow:hidden;">
                        <div class="modal-header bg-white border-bottom py-2 px-3">
                            <span class="val"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Ubicación · {{ $inm->name ?? 'Inmueble' }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:10px;"></button>
                        </div>
                        <div class="modal-body p-3">
                            <div class="it-map-wrap mb-3">
                                <iframe src="https://maps.google.com/maps?q={{ $mapQuery }}&t=m&z=15&output=embed" allowfullscreen loading="lazy"></iframe>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="sub">{{ $inm->ubicacion ?? $inm->city ?? 'Consulta indicaciones con el encargado.' }}</span>
                                <div class="d-flex gap-2">
                                    <a href="https://maps.google.com/?q={{ $mapQuery }}" target="_blank" class="it-icon-btn js-it-ext-map" data-provider="Google Maps">
                                        <i class="bi bi-google text-danger"></i> Google Maps
                                    </a>
                                    <a href="https://waze.com/ul?q={{ $mapQuery }}" target="_blank" class="it-icon-btn js-it-ext-map" data-provider="Waze">
                                        <i class="bi bi-car-front-fill text-info"></i> Waze
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL LIGERO: REGLAS Y LINEAMIENTOS DE LA ESTADÍA --}}
            <div class="modal fade" id="itModalReglas_{{ $reservaActual->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content reservas-soft" style="border:none; border-radius:12px; overflow:hidden;">
                        <div class="modal-header bg-white border-bottom py-2 px-3">
                            <span class="val"><i class="bi bi-shield-check text-success me-1"></i> Lineamientos de Uso</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:10px;"></button>
                        </div>
                        <div class="modal-body p-3">
                            <ul class="mb-0 ps-3 sub" style="color:var(--ink-2); line-height:1.7;">
                                <li>El asociado titular debe estar presente durante toda la estadía (no se permite ceder a terceros sin endoso autorizado).</li>
                                <li>Capacidad máxima permitida: <b>{{ $capMax }} personas</b>.</li>
                                <li>Estadía máxima: <b>{{ $tarifa->dias_maximos ?? 5 }} días</b>.</li>
                                <li>Horario de ingreso: <b>3:00 PM</b> · Horario de salida: <b>1:00 PM</b>.</li>
                                <li>No se permite el ingreso de mascotas al inmueble.</li>
                                <li>Se firma acta de entrega al recibir y al devolver las llaves con el encargado de la ciudad.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL IT-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ItDbg && window.ItDbg.alive && window.ItDbg.alive())
        ? window.ItDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-itinerario-cliente') || document.body;
                if (!$('itDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'itDbgToggle'; t.title = 'Debug Itinerario (IT-v2-DBG)';
                    t.innerHTML = '<span class="it-dbg-led" id="itDbgLed"></span>DBG<span class="it-dbg-badge" id="itDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('itDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'itDbgPanel';
                    p.innerHTML = '<div class="it-dbg-header" id="itDbgHeader">' +
                        '<span>DEBUG · Itinerario Cliente · IT-v2-DBG</span>' +
                        '<span class="it-dbg-controls">' +
                        '<button type="button" class="it-dbg-btn" id="itDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="it-dbg-btn" id="itDbgCopy">Copiar</button>' +
                        '<button type="button" class="it-dbg-btn" id="itDbgClear">Limpiar</button>' +
                        '<button type="button" class="it-dbg-btn" id="itDbgClose">✕</button>' +
                        '</span></div><div id="itDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('itDbgToggle'), p = $('itDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('itDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'it-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="it-dbg-time">[' + hora() + ']</span><span class="it-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('itDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('itDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('itDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('itDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('itDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('itDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('itDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('itDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('itDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Itinerario IT-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('itDbgToggle'); }
            };
            window.ItDbg = api;
            return api;
        })();

    DBG.ensure();
    var itdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#itDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#itDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO VÍA ClientePortalController@misReservasItinerarioFragmento (SIN RECARGAR PÁGINA) ═══
    window.itReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliItinRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'reservas-itinerario';
        var resId    = reservaIdOpcional || root.getAttribute('data-reserva-id') || '';

        if (!resId) {
            itdbg('Aviso: misReservasItinerarioFragmento requiere reserva_id numérico.', 'warn');
            return Promise.resolve();
        }

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('reserva_id', resId);
        urlObj.searchParams.set('id_reserva', resId);
        urlObj.searchParams.set('_t', Date.now());

        // Actualizar query param en la barra del navegador sin recargar la página
        try {
            var navUrl = new URL(window.location.href);
            navUrl.searchParams.set('reserva_id', resId);
            window.history.replaceState({}, document.title, navUrl.toString());
        } catch (e) {}

        var t0 = performance.now();
        itdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                itdbg('Error HTTP ' + res.status + ' al cargar itinerario (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliItinRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var cod = nuevoRoot.getAttribute('data-reserva-cod') || '?';
                var inm = nuevoRoot.getAttribute('data-inmueble') || '?';
                var evs = nuevoRoot.getAttribute('data-eventos') || '0';
                var cup = nuevoRoot.getAttribute('data-cupo') || '0/0';
                itdbg('Fragmento actualizado (' + res.ms + 'ms) · reserva=#' + cod + ' · inmueble="' + inm + '" · eventos=' + evs + ' · cupo=' + cup, 'ok');
            } else {
                itdbg('Respuesta recibida (' + res.ms + 'ms), pero sin nodo #cliItinRoot (posible alerta del controlador)', 'warn');
            }
        })
        .catch(function (err) {
            itdbg('Excepción de red al refrescar itinerario: ' + (err && err.message ? err.message : 'desconocido'), 'err');
        });
    };

    // Registrar listeners globales una sola vez
    if (!window.__jsItinerarioClienteV2Listo) {
        window.__jsItinerarioClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#itDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#itDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#itDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#itDbgCopy'))    { e.stopPropagation(); DBG.copy(); itdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#itDbgRefresh')) {
                e.stopPropagation();
                window.itReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // Expandir / colapsar tarjeta de evento del cronograma
            var evCard = t.closest('.js-it-ev-toggle');
            if (evCard) {
                var abriendo = !evCard.classList.contains('open');
                evCard.classList.toggle('open');
                var evTitle = evCard.getAttribute('data-ev-title') || 'Evento';
                itdbg('Click: ' + (abriendo ? 'Expandir' : 'Colapsar') + ' hito "' + evTitle + '"', 'ev');
                return;
            }

            // Apertura de modales (Mapa GPS / Reglas)
            var modalBtn = t.closest('.js-it-open-modal');
            if (modalBtn) {
                var lbl = modalBtn.getAttribute('data-modal-label') || 'Modal';
                itdbg('Click: Abriendo modal → ' + lbl, 'ev');
                return;
            }

            // Clic en enlaces externos (Google Maps / Waze)
            var extMap = t.closest('.js-it-ext-map');
            if (extMap) {
                var prov = extMap.getAttribute('data-provider') || 'Mapa Externo';
                itdbg('Click: Abriendo ruta GPS externa en ' + prov, 'net');
                return;
            }

            // Botón para saltar a la pestaña de Huéspedes sincronizando la reserva activa
            var btnHues = t.closest('.js-it-go-huespedes');
            if (btnHues) {
                var rid = btnHues.getAttribute('data-reserva-id') || '';
                var cod = btnHues.getAttribute('data-reserva-cod') || '';
                itdbg('Click: Ir a pestaña Huéspedes para reserva #' + cod + ' (id=' + rid + ')', 'ev');
                if (window.huReloadFragment && rid) {
                    window.huReloadFragment(rid, 'sync-desde-itinerario');
                }
                var tabHues = document.querySelector('[data-bs-target="#mr-sub-huespedes"]');
                if (tabHues) tabHues.click();
                return;
            }

            // Botón ir a Crear Reserva (cuando está vacío)
            if (t.closest('.js-it-go-nueva')) {
                itdbg('Click: Ir a Nueva Reserva desde estado vacío', 'ev');
                var tabNueva = document.querySelector('[data-bs-target="#cli-nueva"]');
                if (tabNueva) tabNueva.click();
            }
        });

        // Selector de Reserva (recarga por AJAX desde ClientePortalController sin refrescar toda la pantalla)
        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || t.id !== 'itSelectReserva') return;

            var opt = t.options[t.selectedIndex];
            var cod = opt ? opt.getAttribute('data-cod') : t.value;
            itdbg('Selector: Cambio a reserva #' + cod + ' (id=' + t.value + ')', 'ev');

            window.itReloadFragment(t.value, 'cambio-reserva');

            // Si la pestaña hermana de Huéspedes ya está cargada, la sincronizamos también en segundo plano
            if (window.huReloadFragment) {
                window.huReloadFragment(t.value, 'sync-desde-itinerario');
            }
        });
    }

    var SRV = @json($itDbgData);
    itdbg('IT-v2-DBG activo · reserva=' + (SRV && SRV.reserva_cod ? '#' + SRV.reserva_cod : 'ninguna') +
          ' · inmueble="' + (SRV && SRV.inmueble ? SRV.inmueble : '-') + '"' +
          ' · eventos BD=' + (SRV ? SRV.eventos_bd : 0) +
          ' · huéspedes=' + (SRV ? SRV.huespedes : '0/0'), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { itdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
