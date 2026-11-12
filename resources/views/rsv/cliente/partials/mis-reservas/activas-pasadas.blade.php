{{-- partials/mis-reservas/activas-pasadas.blade.php · Cliente · Minimalista 360° Reactivo sin Reload (AP-v4-DBG) --}}
<div id="sheet-activas-pasadas-cliente">

    {{-- AP-v4-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="apDbgToggle" title="Debug Activas y Pasadas (AP-v4-DBG)">
        <span class="ap-dbg-led" id="apDbgLed"></span>DBG<span class="ap-dbg-badge" id="apDbgBadge">0</span>
    </div>
    <div id="apDbgPanel" role="dialog" aria-label="Panel de debug de Activas y Pasadas">
        <div class="ap-dbg-header" id="apDbgHeader">
            <span>DEBUG · Activas y Pasadas · AP-v4-DBG</span>
            <span class="ap-dbg-controls">
                <button type="button" class="ap-dbg-btn" id="apDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="ap-dbg-btn" id="apDbgCopy">Copiar</button>
                <button type="button" class="ap-dbg-btn" id="apDbgClear">Limpiar</button>
                <button type="button" class="ap-dbg-btn" id="apDbgClose">✕</button>
            </span>
        </div>
        <div id="apDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $userName = optional(\Illuminate\Support\Facades\Auth::user())->name ?? 'Tú';
        $ridSel = request('reserva_id') ?? request('id_reserva');
        $apErrores = [];

        // 1. Aprovechar las colecciones inyectadas por ClientePortalController@misReservasActivasFragmento
        $mis = collect();
        if (isset($todas) && $todas instanceof \Illuminate\Support\Collection && $todas->isNotEmpty()) {
            $mis = $todas;
        } elseif ((isset($activas) && $activas instanceof \Illuminate\Support\Collection) || (isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection)) {
            $cAct = (isset($activas) && $activas instanceof \Illuminate\Support\Collection) ? $activas : collect();
            $cPas = (isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection) ? $pasadas : collect();
            $mis = $cAct->concat($cPas)->unique('id')->values();
        }

        try {
            if ($mis->isEmpty() && $uid) {
                $mis = \App\Models\Rsv\Reserva::where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->limit(30)
                    ->get();
            }

            if ($mis->isNotEmpty()) {
                $mis->loadMissing([
                    'inmueble.multimedia',
                    'inmueble.tipoInmueble',
                    'inmueble.latestTarifa',
                    'status',
                    'origen',
                    'huespedes',
                    'transacciones.pasarela',
                    'mensajes.remitente',
                    'reviews.tipoReceptor',
                    'historialEstados.estadoAnterior',
                    'historialEstados.estadoNuevo',
                    'historialEstados.user',
                    'historialEndosos.usuarioAnterior',
                    'historialEndosos.usuarioNuevo',
                    'historialEndosos.autorizadoPor',
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.activas-pasadas: '.$e->getMessage());
            $apErrores[] = $e->getMessage();
        }

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.mis-reservas.activas.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $rutaStoreMensaje = '';
        foreach (['rsv.mensajes.store-ajax', 'rsv.mensajes.store', 'rsv.cliente.mensajes.store', 'mensajes.store'] as $rName) {
            if (\Illuminate\Support\Facades\Route::has($rName)) {
                $rutaStoreMensaje = route($rName);
                break;
            }
        }

        $rutaStoreReview = '';
        foreach (['rsv.reviews.store-ajax', 'rsv.reviews.store', 'rsv.cliente.reviews.store', 'reviews.store'] as $rName) {
            if (\Illuminate\Support\Facades\Route::has($rName)) {
                $rutaStoreReview = route($rName);
                break;
            }
        }

        $tiposReceptor = collect();
        $tipoReceptorDefaultId = 1;
        try {
            if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                $tiposReceptor = \App\Models\Rsv\TipoReceptor::all();
                $tipoReceptorDefaultId = optional($tiposReceptor->first())->id ?? 1;
            }
        } catch (\Throwable $e) {}

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob')) return 'success';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'cancela') || str_contains($s, 'anula') || str_contains($s, 'rechaz')) return 'danger';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            return 'secondary';
        };

        $cntActivas   = isset($activas) && $activas instanceof \Illuminate\Support\Collection ? $activas->count() : null;
        $cntPasadas   = isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection ? $pasadas->count() : null;

        $apDbgData = [
            'version'     => 'AP-v4-DBG',
            'controlador' => 'ClientePortalController@misReservasActivasFragmento',
            'vista'       => 'partials/mis-reservas/activas-pasadas.blade.php',
            'reservas'    => $mis->count(),
            'activas_ctl' => $cntActivas,
            'pasadas_ctl' => $cntPasadas,
            'reserva_sel' => $ridSel,
            'uid'         => $uid,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'rutas'       => [
                'mensaje_store' => $rutaStoreMensaje ?: 'NO definida',
                'review_store'  => $rutaStoreReview  ?: 'NO definida',
            ],
            'errores'     => $apErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliMisResRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="reservas-activas"
         data-total="{{ $mis->count() }}"
         data-reserva-sel="{{ $ridSel ?? '' }}"
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

            /* ── FILTROS MINIMALISTAS ── */
            .ap-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .ap-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease;
            }
            .ap-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .ap-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .ap-search-input {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 10px; outline:0; width:170px; transition:all .15s;
            }
            .ap-search-input:focus { background:#fff; border-color:var(--accent-bd); width:210px; }

            /* ── FILA MINIMALISTA CLICABLE ── */
            .ap-res-row {
                padding:10px 14px; background:#fff; border:1px solid var(--grid);
                border-radius:8px; margin-bottom:6px; cursor:pointer;
                transition:all .15s ease; display:flex; align-items:center;
                justify-content:space-between; gap:12px;
            }
            .ap-res-row:hover {
                border-color:var(--accent-bd); background:#fafdfc;
                transform:translateX(2px);
            }
            .ap-res-row.is-selected {
                border-color:var(--accent) !important; background:var(--accent-bg);
            }

            .ap-res-thumb {
                width:38px; height:38px; border-radius:6px; overflow:hidden;
                background:var(--head); border:1px solid var(--grid-2); flex-shrink:0;
                display:flex; align-items:center; justify-content:center; color:var(--ink-3);
            }
            .ap-res-thumb img { width:100%; height:100%; object-fit:cover; }

            /* ── ICONOS DISCRETOS ── */
            .ap-icon-btn {
                width:26px; height:26px; border-radius:6px; border:1px solid transparent;
                background:transparent; color:var(--ink-3); display:inline-flex;
                align-items:center; justify-content:center; font-size:11px;
                transition:all .15s; text-decoration:none; cursor:pointer;
            }
            .ap-icon-btn:hover { background:var(--head); border-color:var(--grid-2); color:var(--accent); }
            .ap-chevron { color:var(--ink-4); font-size:12px; transition:transform .15s, color .15s; }
            .ap-res-row:hover .ap-chevron { transform:translateX(3px); color:var(--accent); }

            .ap-dot-indicator { width:6px; height:6px; border-radius:50%; display:inline-block; }

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

            .ap-meta-chip {
                display:inline-flex; align-items:center; gap:4px;
                font-size:8px; font-weight:600; color:var(--ink);
                background:var(--head); border:1px solid var(--grid-2);
                padding:2px 7px; border-radius:5px;
            }

            .reservas-soft .btn-mini {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:8.5px; font-weight:700; padding:5px 10px; border-radius:5px;
                cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px;
            }
            .reservas-soft .btn-mini:hover { color:var(--accent); border-color:var(--accent-bd); background:var(--accent-bg); }
            .reservas-soft .btn-mini-accent { background:var(--accent); color:#fff; border:1px solid var(--accent); }
            .reservas-soft .btn-mini-accent:hover { background:#2e5c43; color:#fff; }

            .ap-err-box { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:8px 10px; font-size:8.5px; margin-bottom:10px; }
            .ap-ok-box  { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:8px 10px; font-size:8.5px; margin-bottom:10px; }

            /* ── LÍNEA DE TIEMPO ── */
            .rsv-timeline { position:relative; padding-left:20px; margin:10px 0; }
            .rsv-timeline::before { content:''; position:absolute; left:5px; top:4px; bottom:4px; width:1.5px; background:var(--grid-2); }
            .rsv-timeline-item { position:relative; margin-bottom:12px; }
            .rsv-timeline-item:last-child { margin-bottom:0; }
            .rsv-timeline-dot { position:absolute; left:-20px; width:11px; height:11px; border-radius:50%; background:#fff; border:2px solid var(--ink-3); top:3px; z-index:2; }
            .rsv-timeline-dot.dot-success { background:#10b981; border-color:#d1fae5; }
            .rsv-timeline-dot.dot-warning { background:#f59e0b; border-color:#fef3c7; }
            .rsv-timeline-dot.dot-primary { background:#3b82f6; border-color:#dbeafe; }
            .rsv-timeline-content { background:#fff; border:1px solid var(--grid); border-radius:7px; padding:9px 12px; }

            /* ── CHAT SOPORTE ── */
            .ap-chat-box { max-height:230px; overflow-y:auto; padding:10px; background:var(--head); border:1px solid var(--grid-2); border-radius:8px; }
            .ap-msg-bubble { max-width:82%; padding:7px 11px; border-radius:8px; margin-bottom:7px; font-size:9px; line-height:1.4; }
            .ap-msg-mine { margin-left:auto; background:var(--accent-bg); border:1px solid var(--accent-bd); color:#1e3a2f; }
            .ap-msg-other { margin-right:auto; background:#fff; border:1px solid var(--grid-2); color:var(--ink-2); }

            /* ── ESTRELLAS REVIEW ── */
            .ap-star-group { display:inline-flex; flex-direction:row-reverse; gap:4px; }
            .ap-star-group input { display:none; }
            .ap-star-group label { font-size:20px; color:#cbd5e1; cursor:pointer; transition:color .15s; }
            .ap-star-group input:checked ~ label,
            .ap-star-group label:hover,
            .ap-star-group label:hover ~ label { color:#f59e0b; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #apDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #apDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .ap-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .ap-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:apDbgBlink 1s steps(2,start) infinite; }
            @keyframes apDbgBlink { 50% { opacity:.35; } }
            .ap-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #apDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .ap-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .ap-dbg-controls { display:flex; gap:4px; }
            .ap-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .ap-dbg-btn:hover { background:#475569; }
            #apDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .ap-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .ap-dbg-time { color:#64748b; margin-right:4px; }
            .ap-dbg-info { color:#93c5fd; } .ap-dbg-ok { color:#86efac; } .ap-dbg-warn { color:#fde68a; } .ap-dbg-err { color:#fca5a5; } .ap-dbg-net { color:#c4b5fd; } .ap-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA MINIMALISTA + BUSCADOR Y FILTROS EN UNA SOLA LÍNEA --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="ap-filter-bar">
                <span class="val me-2" style="font-size:11px;">
                    <i class="bi bi-calendar2-check text-success me-1"></i> Mis Reservas
                </span>
                <button type="button" class="ap-filter-chip active" data-filter="all">Todas ({{ $mis->count() }})</button>
                <button type="button" class="ap-filter-chip" data-filter="success">Activas</button>
                <button type="button" class="ap-filter-chip" data-filter="warning">Pendientes</button>
                <button type="button" class="ap-filter-chip" data-filter="primary">Finalizadas</button>
                <button type="button" class="ap-filter-chip" data-filter="danger">Canceladas</button>
            </div>
            <input type="text" id="apSearchInput" class="ap-search-input" placeholder="Buscar reserva…">
        </div>

        {{-- LISTA MINIMALISTA (CLIC EN CUALQUIER PARTE ABRE EL MODAL) --}}
        <div id="apReservasList">
        @forelse($mis as $r)
            @php
                $fi = $r->fecha_inicio ? \Carbon\Carbon::parse($r->fecha_inicio) : null;
                $ff = $r->fecha_fin ? \Carbon\Carbon::parse($r->fecha_fin) : null;
                $noches = ($fi && $ff) ? max(1, (int) $fi->diffInDays($ff)) : 0;
                $st = optional($r->status)->name ?? 'Pendiente';
                $stGroup = $pillSt($st);
                $sel = (string) $ridSel === (string) $r->id;

                $inm = $r->inmueble;
                $thumb = ($inm && $inm->relationLoaded('multimedia') && $inm->multimedia->first())
                    ? $inm->multimedia->first()->url_archivo
                    : null;
                $tarifaSnap = $inm?->latestTarifa;
                $origenNombre = optional($r->origen)->nombre ?? 'Portal Cliente';

                $huespedesList = $r->relationLoaded('huespedes') ? $r->huespedes : collect();
                $txList        = $r->relationLoaded('transacciones') ? $r->transacciones : collect();
                $msgList       = $r->relationLoaded('mensajes') ? $r->mensajes->sortBy('created_at') : collect();
                $revList       = $r->relationLoaded('reviews') ? $r->reviews : collect();
                $miReview      = $revList->where('id_user_autor', $uid)->first()
                              ?? $revList->where('id_user', $uid)->first()
                              ?? $revList->first();

                $montoTotal = (float) ($r->monto_total ?? 0);
                $montoPagado = (float) $txList->filter(function($tx) {
                    $est = mb_strtolower((string) $tx->estado_pago);
                    return str_contains($est, 'aprob') || str_contains($est, 'pagad') || str_contains($est, 'ok') || str_contains($est, 'exitos') || str_contains($est, 'complet');
                })->sum('monto');
                if ($montoPagado <= 0 && $txList->count() > 0) {
                    $montoPagado = (float) $txList->sum('monto');
                }
                $saldoPendiente = max(0, $montoTotal - $montoPagado);
                $pctPago = $montoTotal > 0 ? min(100, round(($montoPagado / $montoTotal) * 100)) : 0;

                // Línea de Tiempo
                $timeline = collect();
                $timeline->push((object)[
                    'fecha'      => $r->created_at,
                    'titulo'     => 'Reserva Registrada (' . $origenNombre . ')',
                    'desc'       => 'Solicitud inicial por $' . number_format($montoTotal, 0) . ' COP.',
                    'comentario' => $r->comentario_reserva,
                    'usuario'    => optional($r->user)->name ?? 'Titular',
                    'icono'      => 'bi-stars',
                    'color'      => 'success',
                ]);

                if ($r->relationLoaded('historialEstados')) {
                    foreach ($r->historialEstados as $he) {
                        $timeline->push((object)[
                            'fecha'      => $he->created_at,
                            'titulo'     => 'Cambio de Estado',
                            'desc'       => 'De <b>' . e(optional($he->estadoAnterior)->name ?? 'Inicial') . '</b> a <b>' . e(optional($he->estadoNuevo)->name ?? 'Actual') . '</b>.',
                            'comentario' => $he->comentario,
                            'usuario'    => optional($he->user)->name,
                            'icono'      => 'bi-arrow-left-right',
                            'color'      => 'primary',
                        ]);
                    }
                }

                if ($r->relationLoaded('historialEndosos')) {
                    foreach ($r->historialEndosos as $hen) {
                        $timeline->push((object)[
                            'fecha'      => $hen->created_at,
                            'titulo'     => 'Endoso / Cesión de Reserva',
                            'desc'       => 'Cedida de <b>' . e(optional($hen->usuarioAnterior)->name ?? 'N/D') . '</b> a <b>' . e(optional($hen->usuarioNuevo)->name ?? 'N/D') . '</b>.',
                            'comentario' => $hen->motivo_endoso,
                            'usuario'    => 'Autorizó: ' . (optional($hen->autorizadoPor)->name ?? 'Administración'),
                            'icono'      => 'bi-person-bounding-box',
                            'color'      => 'warning',
                        ]);
                    }
                }

                $timeline = $timeline->sortByDesc('fecha');
            @endphp

            {{-- FILA MINIMALISTA: Clic en cualquier lado abre el modal --}}
            <div class="ap-res-row {{ $sel ? 'is-selected' : '' }}"
                 id="apResRow_{{ $r->id }}"
                 data-reserva-id="{{ $r->id }}"
                 data-reserva-cod="{{ $r->codigo_reserva }}"
                 data-st-group="{{ $stGroup }}"
                 data-search="{{ mb_strtolower($r->codigo_reserva . ' ' . ($inm->name ?? '') . ' ' . ($inm->city ?? '') . ' ' . $st) }}"
                 data-bs-toggle="modal"
                 data-bs-target="#modalExp360_{{ $r->id }}">

                {{-- Izquierda: Foto mini + Título limpio + Subtítulo en una línea --}}
                <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                    <div class="ap-res-thumb">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $inm->name ?? 'Inmueble' }}" loading="lazy">
                        @else
                            <i class="bi bi-house-door"></i>
                        @endif
                    </div>

                    <div style="min-width:0; flex:1;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="val text-truncate" style="font-size:10.5px;">
                                {{ $inm->name ?? 'Propiedad' }}
                            </span>
                            <span class="sub" style="font-size:8px; color:var(--ink-3);">#{{ $r->codigo_reserva }}</span>
                        </div>
                        <div class="sub text-truncate mt-1" style="font-size:8.5px;">
                            {{ $fi?->format('d M') ?? '—' }} – {{ $ff?->format('d M Y') ?? '—' }}
                            <span class="mx-1">·</span> {{ $noches }} {{ $noches === 1 ? 'noche' : 'noches' }}
                            <span class="mx-1">·</span> {{ $huespedesList->count() }} huésped(es)
                        </div>
                    </div>
                </div>

                {{-- Derecha: Monto + Estado + Pin reactivo sin reload + Chevron --}}
                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                    <div class="text-end d-none d-sm-block">
                        <div class="val" style="font-size:10.5px; color:var(--ink-2);">
                            ${{ number_format($montoTotal, 0) }}
                        </div>
                        <div class="sub" style="font-size:7.5px;">
                            @if($saldoPendiente > 0)
                                <span style="color:#d97706;">Pendiente</span>
                            @else
                                <span style="color:var(--accent);">Al día</span>
                            @endif
                        </div>
                    </div>

                    <span class="pill pill-{{ $stGroup }}">{{ $st }}</span>

                    {{-- Ícono sutil para fijar reserva reactivamente sin recargar toda la página --}}
                    <button type="button"
                            class="ap-icon-btn js-ap-pin-reserva"
                            data-reserva-id="{{ $r->id }}"
                            data-reserva-cod="{{ $r->codigo_reserva }}"
                            title="Fijar esta reserva para Itinerario y Huéspedes">
                        <i class="bi {{ $sel ? 'bi-pin-fill text-success' : 'bi-pin-angle' }}"></i>
                    </button>

                    <i class="bi bi-chevron-right ap-chevron"></i>
                </div>
            </div>

            {{-- ==================================================================== --}}
            {{-- MODAL EXPEDIENTE 360° (Al dar clic en cualquier parte de la fila)    --}}
            {{-- ==================================================================== --}}
            <div class="modal fade" id="modalExp360_{{ $r->id }}" data-reserva-cod="{{ $r->codigo_reserva }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content reservas-soft" id="printArea_{{ $r->id }}" style="border:none; border-radius:12px; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.16);">

                        <div class="modal-header bg-white border-bottom py-3 px-4">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val" style="font-size:13px; color:var(--accent);">#{{ $r->codigo_reserva }}</span>
                                    <span class="val" style="font-size:13px;">· {{ $inm->name ?? 'Inmueble' }}</span>
                                    <span class="pill pill-{{ $stGroup }}">{{ $st }}</span>
                                </div>
                                <div class="sub mt-1">
                                    {{ $fi?->format('d/m/Y') }} al {{ $ff?->format('d/m/Y') }} ({{ $noches }} noches) · Origen: {{ $origenNombre }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="ap-icon-btn" onclick="window.apPrintReserva({{ $r->id }}, '{{ $r->codigo_reserva }}')" title="Imprimir o guardar PDF">
                                    <i class="bi bi-printer"></i>
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="font-size:10px;"></button>
                            </div>
                        </div>

                        {{-- Pestañas minimalistas con íconos suaves --}}
                        <div class="px-4 pt-1 bg-white border-bottom">
                            <ul class="nav nav-tabs border-0 gap-2" role="tablist" style="font-size:9px; font-weight:600;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Trazabilidad" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-ruta-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-clock-history me-1"></i> Trazabilidad
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Pagos" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-pagos-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-credit-card me-1"></i> Pagos ({{ $txList->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Huéspedes" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-huespedes-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-people me-1"></i> Huéspedes ({{ $huespedesList->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Soporte" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-mensajes-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-chat-dots me-1"></i> Soporte (<span class="js-msg-cnt-tab-{{ $r->id }}">{{ $msgList->count() }}</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Calificar" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-review-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-star me-1"></i> Calificar
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="modal-body p-4" style="background:#fafbfd;">
                            <div class="tab-content">

                                {{-- TAB 1: TRAZABILIDAD Y TARIFA --}}
                                <div class="tab-pane fade show active" id="exp-ruta-{{ $r->id }}" role="tabpanel">
                                    @if($tarifaSnap)
                                        <div class="p-3 bg-white border rounded-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color:var(--grid-2) !important;">
                                            <div>
                                                <div class="lbl">Tarifa de Temporada</div>
                                                <div class="val">{{ $tarifaSnap->nombre_temporada ?? 'Estándar' }}</div>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="ap-meta-chip">Aporte Mín.: <b>${{ number_format((float)$tarifaSnap->precio_minimo_reserva, 0) }}</b></span>
                                                <span class="ap-meta-chip">Máx. Días: <b>{{ $tarifaSnap->dias_maximos ?? '—' }}</b></span>
                                                <span class="ap-meta-chip">Máx. Anual: <b>{{ $tarifaSnap->max_reservas_anuales_cliente ?? '—' }}</b></span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="rsv-timeline">
                                        @foreach($timeline as $item)
                                            <div class="rsv-timeline-item">
                                                <div class="rsv-timeline-dot dot-{{ $item->color }}"></div>
                                                <div class="rsv-timeline-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="val">{{ $item->titulo }}</span>
                                                        <span class="sub">{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y h:i A') }}</span>
                                                    </div>
                                                    <div class="sub" style="color:var(--ink-2);">{!! $item->desc !!}</div>
                                                    @if(!empty($item->comentario))
                                                        <div class="mt-1 p-2 rounded bg-light fst-italic sub">"{{ $item->comentario }}"</div>
                                                    @endif
                                                    @if(!empty($item->usuario))
                                                        <div class="sub mt-1">{{ $item->usuario }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- TAB 2: PAGOS --}}
                                <div class="tab-pane fade" id="exp-pagos-{{ $r->id }}" role="tabpanel">
                                    <div class="p-3 bg-white border rounded-3 mb-3" style="border-color:var(--grid-2) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="sub">Abonado ({{ $pctPago }}%)</span>
                                            <span class="val">${{ number_format($montoPagado, 0) }} / ${{ number_format($montoTotal, 0) }} COP</span>
                                        </div>
                                        <div class="progress" style="height:5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctPago }}%;"></div>
                                        </div>
                                    </div>

                                    @forelse($txList as $tx)
                                        @php
                                            $pas = $tx->pasarela;
                                            $dotColor = $pas && $pas->color ? $pas->color : '#3d7a5c';
                                            $urlSoporte = $tx->url_soporte_pago ?? '#';
                                        @endphp
                                        <div class="p-2 px-3 bg-white border rounded-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color:var(--grid) !important;">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="ap-dot-indicator" style="background:{{ $dotColor }};"></span>
                                                    <span class="val">{{ $pas->nombre ?? ($tx->metodo_pago ?? 'Pago') }}</span>
                                                    <span class="pill pill-{{ $pillSt($tx->estado_pago) }}">{{ $tx->estado_pago ?? 'Registrado' }}</span>
                                                </div>
                                                <div class="sub mt-1">Ref: {{ $tx->referencia_externa ?? 'S/R' }} · {{ optional($tx->created_at)->format('d/m/Y H:i') }}</div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="val" style="color:var(--accent);">${{ number_format((float)$tx->monto, 0) }}</span>
                                                @if($urlSoporte && $urlSoporte !== '#')
                                                    <a href="{{ $urlSoporte }}" target="_blank"
                                                       class="ap-icon-btn js-ap-soporte-link"
                                                       data-ref="{{ $tx->referencia_externa ?? $tx->id }}"
                                                       title="Ver soporte">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 bg-white border rounded-3 sub">Sin pagos registrados aún.</div>
                                    @endforelse
                                </div>

                                {{-- TAB 3: HUÉSPEDES --}}
                                <div class="tab-pane fade" id="exp-huespedes-{{ $r->id }}" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="sub">{{ $huespedesList->count() }} de {{ $inm->capacidad_maxima ?? 6 }} cupos registrados.</span>
                                        <button type="button"
                                                class="btn-mini js-ap-go-huespedes"
                                                data-reserva-id="{{ $r->id }}"
                                                data-reserva-cod="{{ $r->codigo_reserva }}">
                                            Ir a gestión de huéspedes
                                        </button>
                                    </div>
                                    <div class="row g-2">
                                        @forelse($huespedesList as $h)
                                            <div class="col-12 col-md-6">
                                                <div class="p-2 px-3 bg-white border rounded-3 d-flex justify-content-between align-items-center" style="border-color:var(--grid) !important;">
                                                    <div>
                                                        <div class="val">{{ $h->nombre }} {{ $h->apellidos }}</div>
                                                        <div class="sub">{{ $h->tipo_documento }} · {{ $h->numero_documento }}</div>
                                                    </div>
                                                    <span class="pill {{ $h->es_titular ? 'pill-success' : 'pill-secondary' }}">
                                                        {{ $h->es_titular ? 'Titular' : 'Acompañante' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-4 bg-white border rounded-3 sub">Sin acompañantes registrados.</div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- TAB 4: SOPORTE / CHAT --}}
                                <div class="tab-pane fade" id="exp-mensajes-{{ $r->id }}" role="tabpanel">
                                    <div class="ap-chat-box mb-3" id="apChatBox_{{ $r->id }}">
                                        @forelse($msgList as $m)
                                            @php $esMio = ($m->id_user_remitente == $uid); @endphp
                                            <div class="ap-msg-bubble {{ $esMio ? 'ap-msg-mine' : 'ap-msg-other' }}">
                                                <div class="d-flex justify-content-between align-items-center mb-1" style="gap:8px;">
                                                    <strong style="font-size:8px;">{{ $esMio ? 'Tú' : (optional($m->remitente)->name ?? 'Soporte') }}</strong>
                                                    <span style="font-size:7.5px; opacity:.7;">{{ optional($m->created_at)->format('d/m H:i') }}</span>
                                                </div>
                                                <div>{{ $m->contenido }}</div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 sub js-empty-chat">Escribe cualquier duda sobre tu reserva #{{ $r->codigo_reserva }}.</div>
                                        @endforelse
                                    </div>

                                    <div id="apMsgAlert_{{ $r->id }}" class="d-none"></div>

                                    @if($rutaStoreMensaje)
                                        <form action="{{ $rutaStoreMensaje }}" method="POST"
                                              class="js-ap-ajax-form"
                                              data-tipo="mensaje"
                                              data-reserva-id="{{ $r->id }}"
                                              data-reserva="{{ $r->codigo_reserva }}">
                                            @csrf
                                            <input type="hidden" name="_ajax" value="1">
                                            <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                            <input type="hidden" name="id_rsv_reserva" value="{{ $r->id }}">
                                            <input type="hidden" name="reserva_id" value="{{ $r->id }}">
                                            <input type="hidden" name="id_user_remitente" value="{{ $uid }}">
                                            <input type="hidden" name="id_user" value="{{ $uid }}">
                                            <input type="hidden" name="user_id" value="{{ $uid }}">
                                            <input type="hidden" name="tipo_mensaje" value="texto">

                                            <div class="input-group input-group-sm">
                                                <input type="text" name="contenido" class="form-control" style="font-size:9px;"
                                                       placeholder="Escribe un mensaje…" required maxlength="1000"
                                                       oninput="this.form.querySelector('[name=mensaje]').value = this.value">
                                                <input type="hidden" name="mensaje" value="">
                                                <button class="btn btn-success" type="submit" style="font-size:8.5px; background:var(--accent); border-color:var(--accent);">
                                                    Enviar
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>

                                {{-- TAB 5: CALIFICACIÓN (REVIEW) --}}
                                <div class="tab-pane fade" id="exp-review-{{ $r->id }}" role="tabpanel">
                                    @if($miReview)
                                        <div class="p-3 bg-white border rounded-3 text-center" style="border-color:var(--grid) !important;">
                                            <div class="mb-1" style="font-size:16px; color:#f59e0b;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi {{ $i <= (int)$miReview->puntuacion ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="val mb-1">"{{ $miReview->comentario ?? 'Sin comentario.' }}"</p>
                                            <span class="sub">{{ optional($miReview->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                    @else
                                        <div class="p-3 bg-white border rounded-3" style="border-color:var(--grid) !important;">
                                            <div id="apRevAlert_{{ $r->id }}" class="d-none"></div>

                                            @if($rutaStoreReview)
                                                <form action="{{ $rutaStoreReview }}" method="POST"
                                                      class="js-ap-ajax-form"
                                                      data-tipo="review"
                                                      data-reserva-id="{{ $r->id }}"
                                                      data-reserva="{{ $r->codigo_reserva }}">
                                                    @csrf
                                                    <input type="hidden" name="_ajax" value="1">
                                                    <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                                    <input type="hidden" name="id_rsv_reserva" value="{{ $r->id }}">
                                                    <input type="hidden" name="reserva_id" value="{{ $r->id }}">
                                                    <input type="hidden" name="id_user" value="{{ $uid }}">
                                                    <input type="hidden" name="id_user_autor" value="{{ $uid }}">
                                                    <input type="hidden" name="user_id" value="{{ $uid }}">

                                                    <div class="row g-2 mb-2">
                                                        <div class="col-12 col-sm-6">
                                                            <label class="lbl d-block mb-1">Receptor</label>
                                                            @if($tiposReceptor->count() > 0)
                                                                <select name="id_rsv_tipo_receptor" class="form-select form-select-sm" style="font-size:9px;" required
                                                                        onchange="this.form.querySelector('[name=id_rsv_tipos_receptor]').value = this.value">
                                                                    @foreach($tiposReceptor as $tr)
                                                                        <option value="{{ $tr->id }}">{{ $tr->nombre ?? $tr->name ?? ('Receptor #' . $tr->id) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                            @else
                                                                <input type="hidden" name="id_rsv_tipo_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                                <input type="text" class="form-control form-control-sm" style="font-size:9px;" value="Estadía General" disabled>
                                                            @endif
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <label class="lbl d-block mb-1">Aspecto</label>
                                                            <select name="tipo_evaluacion" class="form-select form-select-sm" style="font-size:9px;" required>
                                                                <option value="General">Estadía General</option>
                                                                <option value="Inmueble">Inmueble</option>
                                                                <option value="Limpieza">Limpieza</option>
                                                                <option value="Atencion">Atención</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 text-center">
                                                        <div class="ap-star-group">
                                                            @for($s = 5; $s >= 1; $s--)
                                                                <input type="radio" id="star_{{ $r->id }}_{{ $s }}" name="puntuacion" value="{{ $s }}" {{ $s === 5 ? 'checked' : '' }}
                                                                       onchange="this.form.querySelector('[name=calificacion]').value = this.value" required>
                                                                <label for="star_{{ $r->id }}_{{ $s }}"><i class="bi bi-star-fill"></i></label>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="calificacion" value="5">
                                                    </div>

                                                    <div class="mb-2">
                                                        <textarea name="comentario" rows="2" class="form-control form-control-sm" style="font-size:9px;" placeholder="Escribe un breve comentario…" required></textarea>
                                                    </div>

                                                    <button type="submit" class="btn-mini btn-mini-accent w-100 justify-content-center py-2">
                                                        Guardar Calificación
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-white border-top py-2 px-4 justify-content-between">
                            <button type="button"
                                    class="btn-mini js-ap-pin-reserva"
                                    data-reserva-id="{{ $r->id }}"
                                    data-reserva-cod="{{ $r->codigo_reserva }}"
                                    style="color:var(--accent);">
                                <i class="bi bi-pin-angle"></i> Fijar esta reserva activa
                            </button>
                            <button type="button" class="btn-mini" data-bs-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                <span class="sub d-block mb-2">Aún no tienes reservas registradas.</span>
                <button type="button" class="btn-mini btn-mini-accent js-ap-go-nueva">
                    Crear Reserva
                </button>
            </div>
        @endforelse
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL AP-v4-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ApDbg && window.ApDbg.alive && window.ApDbg.alive())
        ? window.ApDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-activas-pasadas-cliente') || document.body;
                if (!$('apDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'apDbgToggle'; t.title = 'Debug Activas y Pasadas (AP-v4-DBG)';
                    t.innerHTML = '<span class="ap-dbg-led" id="apDbgLed"></span>DBG<span class="ap-dbg-badge" id="apDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('apDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'apDbgPanel';
                    p.innerHTML = '<div class="ap-dbg-header" id="apDbgHeader">' +
                        '<span>DEBUG · Activas y Pasadas · AP-v4-DBG</span>' +
                        '<span class="ap-dbg-controls">' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgCopy">Copiar</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgClear">Limpiar</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgClose">✕</button>' +
                        '</span></div><div id="apDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('apDbgToggle'), p = $('apDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('apDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ap-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="ap-dbg-time">[' + hora() + ']</span><span class="ap-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('apDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('apDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('apDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('apDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('apDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('apDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('apDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('apDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('apDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Activas y Pasadas AP-v4-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('apDbgToggle'); }
            };
            window.ApDbg = api;
            return api;
        })();

    DBG.ensure();
    var apdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#apDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#apDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // Limpiar cualquier modal/backdrop abierto antes de reemplazar el DOM del fragmento
    function limpiarModalesAbiertos() {
        document.querySelectorAll('.modal.show').forEach(function (m) {
            try {
                if (window.bootstrap && window.bootstrap.Modal) {
                    var inst = window.bootstrap.Modal.getInstance(m);
                    if (inst) inst.hide();
                }
            } catch (e) {}
            m.classList.remove('show');
        });
        document.querySelectorAll('.modal-backdrop').forEach(function (bd) { bd.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    // ═══ RECARGA DEL FRAGMENTO VÍA ClientePortalController@misReservasActivasFragmento (SIN RECARGAR PÁGINA) ═══
    window.apReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliMisResRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'reservas-activas';
        var resId    = reservaIdOpcional || root.getAttribute('data-reserva-sel') || '';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (resId) urlObj.searchParams.set('reserva_id', resId);
        urlObj.searchParams.set('_t', Date.now());

        try {
            var navUrl = new URL(window.location.href);
            if (resId) navUrl.searchParams.set('reserva_id', resId);
            window.history.replaceState({}, document.title, navUrl.toString());
        } catch (e) {}

        var t0 = performance.now();
        apdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                apdbg('Error HTTP ' + res.status + ' al refrescar fragmento (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliMisResRoot');
            if (nuevoRoot) {
                limpiarModalesAbiertos();
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                var sel = nuevoRoot.getAttribute('data-reserva-sel') || 'ninguna';
                apdbg('Fragmento actualizado (' + res.ms + 'ms) · total=' + tot + ' · fijada=' + sel, 'ok');
            } else {
                apdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliMisResRoot', 'warn');
            }
        })
        .catch(function (err) {
            apdbg('Excepción recargando fragmento: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    window.apPrintReserva = function (id, codigo) {
        apdbg('Click: Imprimir / PDF de reserva #' + codigo + ' (id=' + id + ')', 'ev');
        var el = document.getElementById('printArea_' + id);
        if (!el) { window.print(); return; }
        var w = window.open('', '_blank', 'width=850,height=700');
        if (!w) { window.print(); return; }
        w.document.write('<html><head><title>Reserva #' + codigo + '</title>');
        w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        w.document.write('<style>body{font-family:sans-serif;padding:24px;} .btn-close,.nav-tabs,form,.ap-icon-btn,.btn-mini{display:none !important;} .tab-pane{display:block !important;opacity:1 !important;margin-bottom:20px;}</style>');
        w.document.write('</head><body>' + el.innerHTML + '</body></html>');
        w.document.close();
        setTimeout(function () { w.focus(); w.print(); }, 400);
    };

    function aplicarFiltros(logAccion) {
        var chipActivo = document.querySelector('#cliMisResRoot .ap-filter-chip.active');
        var grupo = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var q = (document.getElementById('apSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#apReservasList .ap-res-row').forEach(function (row) {
            var matchGrupo = (grupo === 'all') || (row.getAttribute('data-st-group') === grupo);
            var matchTexto = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar = matchGrupo && matchTexto;
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        if (logAccion) {
            apdbg('Filtro aplicado · grupo="' + grupo + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // Registrar handlers globales una sola vez
    if (!window.__jsActivasPasadasV4Listo) {
        window.__jsActivasPasadasV4Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#apDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#apDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#apDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#apDbgCopy'))    { e.stopPropagation(); DBG.copy(); apdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#apDbgRefresh')) {
                e.stopPropagation();
                window.apReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // 1. Fijar reserva activa sin recargar la página (sincroniza Itinerario y Huéspedes)
            var pinBtn = t.closest('.js-ap-pin-reserva');
            if (pinBtn) {
                e.preventDefault();
                e.stopPropagation();
                var rid = pinBtn.getAttribute('data-reserva-id') || '';
                var cod = pinBtn.getAttribute('data-reserva-cod') || '';
                apdbg('Click: Fijar reserva activa #' + cod + ' (id=' + rid + ') sin recargar vista', 'ev');

                // Actualizar estado visual inmediato en las filas
                document.querySelectorAll('#apReservasList .ap-res-row').forEach(function (row) {
                    var esEsta = row.getAttribute('data-reserva-id') === rid;
                    row.classList.toggle('is-selected', esEsta);
                    var ic = row.querySelector('.js-ap-pin-reserva i');
                    if (ic) {
                        ic.className = 'bi ' + (esEsta ? 'bi-pin-fill text-success' : 'bi-pin-angle');
                    }
                });

                var root = document.getElementById('cliMisResRoot');
                if (root) root.setAttribute('data-reserva-sel', rid);

                try {
                    var navUrl = new URL(window.location.href);
                    navUrl.searchParams.set('reserva_id', rid);
                    window.history.replaceState({}, document.title, navUrl.toString());
                } catch (err) {}

                if (window.itReloadFragment) window.itReloadFragment(rid, 'pin-desde-activas');
                if (window.huReloadFragment) window.huReloadFragment(rid, 'pin-desde-activas');
                return;
            }

            // 2. Saltar a la pestaña de Huéspedes desde el modal de Expediente 360°
            var goHues = t.closest('.js-ap-go-huespedes');
            if (goHues) {
                e.preventDefault();
                var ridH = goHues.getAttribute('data-reserva-id') || '';
                var codH = goHues.getAttribute('data-reserva-cod') || '';
                apdbg('Click: Saltar a Gestión de Huéspedes para #' + codH + ' (id=' + ridH + ')', 'ev');
                limpiarModalesAbiertos();
                if (window.huReloadFragment && ridH) {
                    window.huReloadFragment(ridH, 'salto-desde-expediente');
                }
                var tabHues = document.querySelector('[data-bs-target="#mr-sub-huespedes"]');
                if (tabHues) tabHues.click();
                return;
            }

            // 3. Cambio de pestaña interna dentro del Modal Expediente 360°
            var modalTab = t.closest('.js-ap-modal-tab');
            if (modalTab) {
                var tabNom = modalTab.getAttribute('data-tab-name') || 'Tab';
                var resCod = modalTab.getAttribute('data-reserva-cod') || '?';
                apdbg('Click: Pestaña Expediente #' + resCod + ' → ' + tabNom, 'ev');
                return;
            }

            // 4. Apertura de soporte de pago externo
            var sopLink = t.closest('.js-ap-soporte-link');
            if (sopLink) {
                apdbg('Click: Abriendo soporte de pago (Ref: ' + (sopLink.getAttribute('data-ref') || 'S/R') + ')', 'net');
                return;
            }

            // 5. Clic en fila de reserva (abre el modal Expediente 360°)
            var rowClick = t.closest('.ap-res-row');
            if (rowClick) {
                var rCod = rowClick.getAttribute('data-reserva-cod') || '?';
                var rId  = rowClick.getAttribute('data-reserva-id') || '?';
                apdbg('Click: Abriendo Expediente 360° de #' + rCod + ' (id=' + rId + ')', 'ev');
                return;
            }

            // 6. Filtros por chip de estado
            var chip = t.closest('.ap-filter-chip');
            if (chip) {
                document.querySelectorAll('#cliMisResRoot .ap-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltros(true);
                return;
            }

            // 7. Ir a Crear Reserva desde estado vacío
            if (t.closest('.js-ap-go-nueva')) {
                apdbg('Click: Ir a Nueva Reserva', 'ev');
                var tabNueva = document.querySelector('[data-bs-target="#cli-nueva"]');
                if (tabNueva) tabNueva.click();
            }
        });

        // Buscador en vivo con registro en DBG
        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'apSearchInput') {
                aplicarFiltros(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltros(true);
                }, 350);
            }
        });

        // Envío AJAX de Mensajes de Soporte y Calificaciones (Reviews) sin recargar toda la página
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('js-ap-ajax-form')) return;
            e.preventDefault();

            var tipo   = form.getAttribute('data-tipo') || 'form';
            var resId  = form.getAttribute('data-reserva-id') || '';
            var cod    = form.getAttribute('data-reserva') || '';
            var btn    = form.querySelector('button[type="submit"]');
            var oldTxt = btn ? btn.innerHTML : '';

            var alertBox = document.getElementById((tipo === 'mensaje' ? 'apMsgAlert_' : 'apRevAlert_') + resId);
            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Enviando…'; }

            var fd = new FormData(form);
            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            apdbg('Enviando (' + tipo + ' #' + cod + ') → ' + form.getAttribute('action'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json, text/html'
                },
                body: fd
            })
            .then(function (r) {
                var ms = Math.round(performance.now() - t0);
                var ct = (r.headers.get('content-type') || '').toLowerCase();
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { ok: r.ok, status: r.status, ms: ms, json: j, isHtml: false }; });
                }
                return r.text().then(function () { return { ok: r.ok, status: r.status, ms: ms, json: { success: r.ok }, isHtml: true }; });
            })
            .then(function (res) {
                var j = res.json || {};
                if (res.ok && (res.isHtml || j.success !== false)) {
                    apdbg('Operación exitosa (' + tipo + ' en #' + cod + ') · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');

                    if (tipo === 'mensaje') {
                        var chatBox = document.getElementById('apChatBox_' + resId);
                        var inp = form.querySelector('input[name="contenido"]');
                        var txt = inp ? inp.value : '';
                        if (chatBox && txt) {
                            var emptyNode = chatBox.querySelector('.js-empty-chat');
                            if (emptyNode) emptyNode.remove();
                            var div = document.createElement('div');
                            div.className = 'ap-msg-bubble ap-msg-mine';
                            div.innerHTML = '<div class="d-flex justify-content-between mb-1"><strong style="font-size:8px;">Tú</strong><span style="font-size:7.5px;opacity:.7;">Ahora</span></div><div></div>';
                            div.lastChild.textContent = txt;
                            chatBox.appendChild(div);
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                        if (inp) inp.value = '';
                        var cntSpan = document.querySelector('.js-msg-cnt-tab-' + resId);
                        if (cntSpan) cntSpan.textContent = parseInt(cntSpan.textContent || '0', 10) + 1;
                    } else {
                        // Review guardada: recargar únicamente el fragmento desde ClientePortalController sin recargar la página
                        if (alertBox) {
                            alertBox.className = 'ap-ok-box';
                            alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Calificación guardada.';
                        }
                        setTimeout(function () {
                            window.apReloadFragment(resId, 'post-review-#' + cod);
                        }, 450);
                    }
                    return;
                }

                apdbg('Error al guardar (' + tipo + ' en #' + cod + ') · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'err');
                var listaErrores = [];
                if (j.errors && typeof j.errors === 'object') {
                    Object.keys(j.errors).forEach(function (campo) {
                        [].concat(j.errors[campo]).forEach(function (m) {
                            listaErrores.push('<li><b>' + campo + ':</b> ' + m + '</li>');
                            apdbg('422 [' + campo + ']: ' + m, 'err');
                        });
                    });
                }
                if (alertBox) {
                    alertBox.className = 'ap-err-box';
                    alertBox.innerHTML = '<b>' + (j.message || 'Error') + '</b>' + (listaErrores.length ? '<ul class="mb-0 mt-1 ps-3">' + listaErrores.join('') + '</ul>' : '');
                }
            })
            .catch(function (err) {
                apdbg('Error de red en (' + tipo + '): ' + (err && err.message ? err.message : 'desconocido'), 'err');
                if (alertBox) { alertBox.className = 'ap-err-box'; alertBox.innerHTML = 'Error de conexión.'; }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldTxt; }
            });
        });
    }

    var SRV = @json($apDbgData);
    apdbg('AP-v4-DBG activo · reservas=' + (SRV ? SRV.reservas : 0) +
          (SRV && SRV.activas_ctl !== null ? ' (activas=' + SRV.activas_ctl + ', pasadas=' + SRV.pasadas_ctl + ')' : '') +
          ' · fijada=' + (SRV && SRV.reserva_sel ? '#' + SRV.reserva_sel : 'ninguna'), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { apdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
