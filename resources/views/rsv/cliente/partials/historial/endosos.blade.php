{{-- partials/historial/endosos.blade.php · Cliente · Auditoría Reactiva de Endosos sin Reload (HI-END-v2-DBG) --}}
<div id="sheet-historial-endosos-cliente">

    {{-- HI-END-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="hiEndDbgToggle" title="Debug Historial Endosos (HI-END-v2-DBG)">
        <span class="hi-end-dbg-led" id="hiEndDbgLed"></span>DBG<span class="hi-end-dbg-badge" id="hiEndDbgBadge">0</span>
    </div>
    <div id="hiEndDbgPanel" role="dialog" aria-label="Panel de debug de Historial de Endosos">
        <div class="hi-end-dbg-header" id="hiEndDbgHeader">
            <span>DEBUG · Historial Endosos · HI-END-v2-DBG</span>
            <span class="hi-end-dbg-controls">
                <button type="button" class="hi-end-dbg-btn" id="hiEndDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="hi-end-dbg-btn" id="hiEndDbgCopy">Copiar</button>
                <button type="button" class="hi-end-dbg-btn" id="hiEndDbgClear">Limpiar</button>
                <button type="button" class="hi-end-dbg-btn" id="hiEndDbgClose">✕</button>
            </span>
        </div>
        <div id="hiEndDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $ridFiltro = request('reserva_id') ?? request('id_reserva') ?? '';
        $henErrores = [];

        // 1. Aprovechar $endosos si viene inyectado desde ClientePortalController@historialEndososFragmento
        $listaEndosos = (isset($endosos) && $endosos instanceof \Illuminate\Support\Collection) ? $endosos : collect();
        $misReservas = collect();

        try {
            if ($uid) {
                $misReservas = \App\Models\Rsv\Reserva::with(['inmueble:id,name,city', 'status'])
                    ->where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->get();
            }

            if ($listaEndosos->isEmpty() && $uid && class_exists(\App\Models\Rsv\HistorialEndoso::class)) {
                $misIds = $misReservas->pluck('id')->all();
                $tablaEnd = (new \App\Models\Rsv\HistorialEndoso)->getTable();
                $tieneColAnt = \Illuminate\Support\Facades\Schema::hasColumn($tablaEnd, 'id_user_anterior');
                $tieneColNue = \Illuminate\Support\Facades\Schema::hasColumn($tablaEnd, 'id_user_nuevo');

                $qEnd = \App\Models\Rsv\HistorialEndoso::query();
                $qEnd->where(function ($sub) use ($misIds, $uid, $tieneColAnt, $tieneColNue) {
                    if (!empty($misIds)) {
                        $sub->whereIn('id_rsv_reservas', $misIds);
                    }
                    if ($tieneColAnt) {
                        $sub->orWhere('id_user_anterior', $uid);
                    }
                    if ($tieneColNue) {
                        $sub->orWhere('id_user_nuevo', $uid);
                    }
                });

                $listaEndosos = $qEnd->orderByDesc('created_at')->limit(100)->get();
            }

            if ($listaEndosos->isNotEmpty() && class_exists(\App\Models\Rsv\HistorialEndoso::class)) {
                $relacionesDeseadas = [];
                foreach ([
                    'reserva.inmueble',
                    'reserva.status',
                    'usuarioAnterior',
                    'usuarioNuevo',
                    'userAnterior',
                    'userNuevo',
                    'autorizadoPor',
                    'usuarioAutor',
                    'user',
                    'autor',
                ] as $rel) {
                    if (method_exists(\App\Models\Rsv\HistorialEndoso::class, explode('.', $rel)[0])) {
                        $relacionesDeseadas[] = $rel;
                    }
                }
                if (!empty($relacionesDeseadas)) {
                    $listaEndosos->loadMissing($relacionesDeseadas);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.histEnd: ' . $e->getMessage());
            $henErrores[] = $e->getMessage();
        }

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.historial.endosos.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'cancela') || str_contains($s, 'anula') || str_contains($s, 'rechaz')) return 'danger';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob')) return 'success';
            return 'secondary';
        };

        // Helper para extraer titular anterior, titular nuevo, autorizador y dirección del endoso respecto al usuario actual
        $resolverEndoso = function ($e) use ($uid) {
            $antObj = $e->usuarioAnterior ?? $e->userAnterior ?? null;
            $nueObj = $e->usuarioNuevo ?? $e->userNuevo ?? null;
            $autObj = $e->autorizadoPor ?? $e->usuarioAutor ?? $e->autor ?? $e->user ?? null;

            $idAnt = $e->id_user_anterior ?? $e->user_anterior_id ?? optional($antObj)->id ?? null;
            $idNue = $e->id_user_nuevo ?? $e->user_nuevo_id ?? optional($nueObj)->id ?? null;

            $nomAnt = optional($antObj)->name ?? ($idAnt ? 'Usuario #' . $idAnt : 'Titular Anterior');
            $nitAnt = optional($antObj)->nit ?? '';

            $nomNue = optional($nueObj)->name ?? ($idNue ? 'Usuario #' . $idNue : 'Nuevo Titular');
            $nitNue = optional($nueObj)->nit ?? '';

            $nomAut = optional($autObj)->name ?? 'Administración RSV';

            // Clasificación: recibido (a mi favor), cedido (yo lo entregué) u otro
            $tipoDir = 'recibido';
            if ($uid && (string) $idAnt === (string) $uid && (string) $idNue !== (string) $uid) {
                $tipoDir = 'cedido';
            } elseif ($uid && (string) $idNue === (string) $uid) {
                $tipoDir = 'recibido';
            }

            return [$nomAnt, $nitAnt, $nomNue, $nitNue, $nomAut, $tipoDir];
        };

        $conteos = [
            'all'      => $listaEndosos->count(),
            'recibido' => 0,
            'cedido'   => 0,
        ];

        foreach ($listaEndosos as $itemE) {
            [, , , , , $tDir] = $resolverEndoso($itemE);
            if (isset($conteos[$tDir])) {
                $conteos[$tDir]++;
            }
        }

        $henDbgData = [
            'version'     => 'HI-END-v2-DBG',
            'controlador' => 'ClientePortalController@historialEndososFragmento',
            'vista'       => 'partials/historial/endosos.blade.php',
            'endosos'     => $listaEndosos->count(),
            'reservas'    => $misReservas->count(),
            'conteos'     => $conteos,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'errores'     => $henErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliHistEndRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="historial-endosos"
         data-total="{{ $listaEndosos->count() }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:10px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.4; }

            /* ── BARRA DE FILTROS Y SELECTOR ── */
            .hen-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .hen-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .hen-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .hen-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .hen-chip-count {
                font-size:7.5px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .hen-filter-chip.active .hen-chip-count { background:var(--accent); color:#fff; }
            .hen-filter-chip[data-filter="cedido"].active { background:#fffbeb; color:#b45309; border-color:#fef3c7; }
            .hen-filter-chip[data-filter="cedido"].active .hen-chip-count { background:#b45309; color:#fff; }

            .hen-input-ctrl {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 9px; outline:0; transition:all .15s;
            }
            .hen-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); }

            /* ── TARJETA / FILA DE ENDOSO ── */
            .hen-card {
                background:#fff; border:1px solid var(--grid); border-radius:8px;
                margin-bottom:6px; transition:all .15s ease; overflow:hidden;
            }
            .hen-card:hover { border-color:var(--accent-bd); background:#fafdfc; }
            .hen-row {
                padding:9px 12px; display:flex; align-items:center;
                justify-content:space-between; gap:10px; cursor:pointer;
            }
            .hen-detail {
                display:none; padding:10px 14px; background:var(--head);
                border-top:1px dashed var(--grid-2); font-size:8.5px;
            }
            .hen-card.open { border-color:var(--accent-bd); }
            .hen-card.open .hen-detail { display:block; }
            .hen-card.open .hen-chevron { transform:rotate(90deg); color:var(--accent); }
            .hen-chevron { font-size:11px; color:var(--ink-4); transition:transform .15s; }

            .hen-icon-box {
                width:32px; height:32px; border-radius:8px; display:flex;
                align-items:center; justify-content:center; font-size:12px;
                flex-shrink:0; border:1px solid;
            }
            .hen-icon-box.dir-recibido { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .hen-icon-box.dir-cedido   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-danger    { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary   { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .hen-user-badge {
                display:inline-flex; align-items:center; gap:4px;
                background:var(--head); border:1px solid var(--grid-2);
                border-radius:5px; padding:2px 7px; font-size:8.5px; font-weight:600; color:var(--ink-2);
            }
            .hen-user-badge.is-me {
                background:var(--accent-bg); border-color:var(--accent-bd); color:var(--accent); font-weight:700;
            }

            .hen-btn-mini {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:8px; font-weight:700; padding:3px 9px; border-radius:5px;
                cursor:pointer; display:inline-flex; align-items:center; gap:4px;
                transition:all .15s; text-decoration:none;
            }
            .hen-btn-mini:hover { background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #hiEndDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #hiEndDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .hi-end-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .hi-end-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:hiEndDbgBlink 1s steps(2,start) infinite; }
            @keyframes hiEndDbgBlink { 50% { opacity:.35; } }
            .hi-end-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #hiEndDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .hi-end-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .hi-end-dbg-controls { display:flex; gap:4px; }
            .hi-end-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .hi-end-dbg-btn:hover { background:#475569; }
            #hiEndDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .hi-end-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .hi-end-dbg-time { color:#64748b; margin-right:4px; }
            .hi-end-dbg-info { color:#93c5fd; } .hi-end-dbg-ok { color:#86efac; } .hi-end-dbg-warn { color:#fde68a; } .hi-end-dbg-err { color:#fca5a5; } .hi-end-dbg-net { color:#c4b5fd; } .hi-end-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA + CHIPS DE DIRECCIÓN (TODOS / RECIBIDOS / CEDIDOS) + SELECTOR DE RESERVA Y BUSCADOR --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="hen-filter-bar">
                <span class="val me-2" style="font-size:11px;">
                    <i class="bi bi-arrow-left-right text-success me-1"></i> Endosos de Titularidad
                </span>
                <button type="button" class="hen-filter-chip active" data-filter="all">
                    Todos <span class="hen-chip-count" id="henCntAll">{{ $conteos['all'] }}</span>
                </button>
                <button type="button" class="hen-filter-chip" data-filter="recibido">
                    Recibidos <span class="hen-chip-count" id="henCntRecibido">{{ $conteos['recibido'] }}</span>
                </button>
                <button type="button" class="hen-filter-chip" data-filter="cedido">
                    Cedidos <span class="hen-chip-count" id="henCntCedido">{{ $conteos['cedido'] }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($misReservas->isNotEmpty())
                    <select id="henSelectReserva" class="hen-input-ctrl" style="max-width:220px; cursor:pointer;">
                        <option value="">Todas las reservas ({{ $misReservas->count() }})</option>
                        @foreach($misReservas as $mr)
                            @php
                                $mrSt = optional($mr->status)->name ?? 'Pendiente';
                            @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    data-estado="{{ $mrSt }}"
                                    {{ (string) $ridFiltro === (string) $mr->id ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} [{{ $mrSt }}]
                            </option>
                        @endforeach
                    </select>
                @endif
                <input type="text" id="henSearchInput" class="hen-input-ctrl" style="width:155px;" placeholder="Buscar titular o #cód…">
            </div>
        </div>

        {{-- LISTADO DE ENDOSOS Y CESIONES --}}
        <div id="henEndososList">
            @forelse($listaEndosos as $e)
                @php
                    [$nomAnt, $nitAnt, $nomNue, $nitNue, $nomAut, $tipoDir] = $resolverEndoso($e);

                    $resObj     = $e->reserva;
                    $resId      = $resObj->id ?? $e->id_rsv_reservas ?? $e->reserva_id ?? null;
                    $resCod     = $resObj->codigo_reserva ?? ($resId ? '#' . $resId : 'S/C');
                    $inmNombre  = optional($resObj?->inmueble)->name ?? 'Reserva';
                    $estadoActReserva = optional($resObj?->status)->name ?? 'Registrada';
                    $grpActReserva    = $pillSt($estadoActReserva);

                    $motivo    = trim((string) ($e->motivo_endoso ?? $e->motivo ?? $e->comentario ?? $e->observacion ?? ''));
                    $fechaFmt  = optional($e->created_at)->format('d/m/Y h:i A') ?? '—';
                    $haceTiempo = $e->created_at ? $e->created_at->diffForHumans() : '';

                    $idAnt = $e->id_user_anterior ?? $e->user_anterior_id ?? optional($e->usuarioAnterior ?? $e->userAnterior)->id ?? null;
                    $idNue = $e->id_user_nuevo ?? $e->user_nuevo_id ?? optional($e->usuarioNuevo ?? $e->userNuevo)->id ?? null;
                    $soyAnt = $uid && (string) $idAnt === (string) $uid;
                    $soyNue = $uid && (string) $idNue === (string) $uid;
                @endphp

                <div class="hen-card"
                     id="henCard_{{ $e->id }}"
                     data-endoso-id="{{ $e->id }}"
                     data-reserva-id="{{ $resId }}"
                     data-reserva-cod="{{ $resCod }}"
                     data-dir-group="{{ $tipoDir }}"
                     data-search="{{ mb_strtolower($resCod . ' ' . $inmNombre . ' ' . $nomAnt . ' ' . $nitAnt . ' ' . $nomNue . ' ' . $nitNue . ' ' . $motivo . ' ' . $nomAut) }}">

                    <div class="hen-row js-hen-toggle-row">
                        <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                            <div class="hen-icon-box dir-{{ $tipoDir }}">
                                <i class="bi {{ $tipoDir === 'cedido' ? 'bi-box-arrow-right' : 'bi-person-check-fill' }}"></i>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val text-truncate">{{ $inmNombre }}</span>
                                    <span class="sub">#{{ $resCod }}</span>

                                    <span class="hen-user-badge {{ $soyAnt ? 'is-me' : '' }}">
                                        <i class="bi bi-person"></i> {{ $nomAnt }} @if($soyAnt) (Tú) @endif
                                    </span>
                                    <i class="bi bi-arrow-right text-muted" style="font-size:9px;"></i>
                                    <span class="hen-user-badge {{ $soyNue ? 'is-me' : '' }}">
                                        <i class="bi bi-person-check"></i> {{ $nomNue }} @if($soyNue) (Tú) @endif
                                    </span>
                                </div>

                                <div class="sub text-truncate mt-1">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $fechaFmt }}
                                    @if($haceTiempo) <span class="mx-1">·</span> {{ $haceTiempo }} @endif
                                    @if($motivo !== '')
                                        <span class="mx-1">·</span> <span class="fst-italic">"{{ \Illuminate\Support\Str::limit($motivo, 60) }}"</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="pill {{ $tipoDir === 'cedido' ? 'pill-warning' : 'pill-success' }}">
                                {{ $tipoDir === 'cedido' ? 'Cedido' : 'Recibido' }}
                            </span>
                            <span class="pill pill-{{ $grpActReserva }} d-none d-sm-inline-flex" title="Estado vigente de la reserva #{{ $resCod }}">
                                {{ $estadoActReserva }}
                            </span>
                            <i class="bi bi-chevron-right hen-chevron"></i>
                        </div>
                    </div>

                    {{-- Panel desplegable con el detalle de la cesión --}}
                    <div class="hen-detail">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="mb-1">
                                    <span class="lbl me-1">Titular Anterior:</span>
                                    <span class="val">{{ $nomAnt }}</span>
                                    @if($nitAnt) <span class="sub">({{ $nitAnt }})</span> @endif
                                    <span class="mx-2 text-muted">→</span>
                                    <span class="lbl me-1">Nuevo Titular:</span>
                                    <span class="val">{{ $nomNue }}</span>
                                    @if($nitNue) <span class="sub">({{ $nitNue }})</span> @endif
                                </div>
                                <div class="sub">
                                    <b>Autorizado por:</b> {{ $nomAut }}
                                    <span class="mx-1">·</span>
                                    <b>Fecha exacta:</b> {{ $fechaFmt }}
                                    <span class="mx-1">·</span>
                                    <b>Estado actual reserva:</b> <span class="pill pill-{{ $grpActReserva }}">{{ $estadoActReserva }}</span>
                                </div>
                                @if($motivo !== '')
                                    <div class="mt-2 p-2 bg-white border rounded sub" style="color:var(--ink-2);">
                                        <i class="bi bi-file-earmark-text text-success me-1"></i> <b>Motivo del endoso:</b> {{ $motivo }}
                                    </div>
                                @endif
                            </div>

                            @if($resId)
                                <div class="d-flex gap-2">
                                    <button type="button"
                                            class="hen-btn-mini js-hen-ver-reserva"
                                            data-reserva-id="{{ $resId }}"
                                            data-reserva-cod="{{ $resCod }}">
                                        <i class="bi bi-box-arrow-up-right"></i> Ver en Mis Reservas
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                    <i class="bi bi-arrow-left-right text-muted d-block mb-2" style="font-size:22px;"></i>
                    <span class="sub d-block">Sin endosos ni cesiones de titularidad registrados todavía.</span>
                </div>
            @endforelse

            @if($listaEndosos->isNotEmpty())
                <div id="henEmptyFilterMsg" class="text-center py-4 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                    <span class="sub d-block">No hay endosos que coincidan con el filtro seleccionado.</span>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL HI-END-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.HiEndDbg && window.HiEndDbg.alive && window.HiEndDbg.alive())
        ? window.HiEndDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-historial-endosos-cliente') || document.body;
                if (!$('hiEndDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'hiEndDbgToggle'; t.title = 'Debug Historial Endosos (HI-END-v2-DBG)';
                    t.innerHTML = '<span class="hi-end-dbg-led" id="hiEndDbgLed"></span>DBG<span class="hi-end-dbg-badge" id="hiEndDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('hiEndDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'hiEndDbgPanel';
                    p.innerHTML = '<div class="hi-end-dbg-header" id="hiEndDbgHeader">' +
                        '<span>DEBUG · Historial Endosos · HI-END-v2-DBG</span>' +
                        '<span class="hi-end-dbg-controls">' +
                        '<button type="button" class="hi-end-dbg-btn" id="hiEndDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="hi-end-dbg-btn" id="hiEndDbgCopy">Copiar</button>' +
                        '<button type="button" class="hi-end-dbg-btn" id="hiEndDbgClear">Limpiar</button>' +
                        '<button type="button" class="hi-end-dbg-btn" id="hiEndDbgClose">✕</button>' +
                        '</span></div><div id="hiEndDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle', 'hiIniDbgToggle', 'hiEstDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('hiEndDbgToggle'), p = $('hiEndDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('hiEndDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'hi-end-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="hi-end-dbg-time">[' + hora() + ']</span><span class="hi-end-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('hiEndDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('hiEndDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('hiEndDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('hiEndDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('hiEndDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('hiEndDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('hiEndDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('hiEndDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('hiEndDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Historial Endosos HI-END-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('hiEndDbgToggle'); }
            };
            window.HiEndDbg = api;
            return api;
        })();

    DBG.ensure();
    var hendbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#hiEndDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#hiEndDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function actualizarContadoresEndosos() {
        var selRes = document.getElementById('henSelectReserva')?.value || '';
        var cards = document.querySelectorAll('#henEndososList .hen-card');
        var counts = { all: 0, recibido: 0, cedido: 0 };

        cards.forEach(function (c) {
            if (selRes && c.getAttribute('data-reserva-id') !== selRes) return;
            counts.all++;
            var grp = c.getAttribute('data-dir-group') || '';
            if (Object.prototype.hasOwnProperty.call(counts, grp)) {
                counts[grp]++;
            }
        });

        var mapIds = {
            all: 'henCntAll',
            recibido: 'henCntRecibido',
            cedido: 'henCntCedido'
        };
        Object.keys(mapIds).forEach(function (k) {
            var el = document.getElementById(mapIds[k]);
            if (el) el.textContent = counts[k];
        });
    }

    function aplicarFiltrosEndosos(logAccion) {
        var chipActivo = document.querySelector('#cliHistEndRoot .hen-filter-chip.active');
        var grupo  = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var selRes = document.getElementById('henSelectReserva')?.value || '';
        var q      = (document.getElementById('henSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#henEndososList .hen-card').forEach(function (card) {
            var matchRes   = !selRes || (card.getAttribute('data-reserva-id') === selRes);
            var matchGrupo = (grupo === 'all') || (card.getAttribute('data-dir-group') === grupo);
            var matchTexto = !q || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar    = matchRes && matchGrupo && matchTexto;
            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('henEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            hendbg('Filtro aplicado · tipo="' + grupo + '" · reserva="' + (selRes || 'todas') + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO SIN RECARGAR PÁGINA ═══
    window.hiEndReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliHistEndRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'historial-endosos';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (reservaIdOpcional) urlObj.searchParams.set('reserva_id', reservaIdOpcional);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        hendbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                hendbg('Error HTTP ' + res.status + ' al refrescar Historial Endosos (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliHistEndRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                actualizarContadoresEndosos();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                hendbg('Historial de Endosos actualizado (' + res.ms + 'ms) · registros=' + tot, 'ok');
            } else {
                hendbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliHistEndRoot', 'warn');
            }
        })
        .catch(function (err) {
            hendbg('Excepción recargando Historial Endosos: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsHistEndososClienteV2Listo) {
        window.__jsHistEndososClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#hiEndDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#hiEndDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#hiEndDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#hiEndDbgCopy'))    { e.stopPropagation(); DBG.copy(); hendbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#hiEndDbgRefresh')) {
                e.stopPropagation();
                window.hiEndReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // Expandir/colapsar detalle de un endoso
            var rowToggle = t.closest('.js-hen-toggle-row');
            if (rowToggle) {
                var card = rowToggle.closest('.hen-card');
                if (card) {
                    var abrir = !card.classList.contains('open');
                    card.classList.toggle('open');
                    hendbg('Click: ' + (abrir ? 'Expandir' : 'Colapsar') + ' endoso #' +
                           (card.getAttribute('data-endoso-id') || '?') + ' (reserva #' + (card.getAttribute('data-reserva-cod') || '?') + ')', 'ev');
                }
                return;
            }

            // Saltar directamente a la reserva en Mis Reservas -> Activas y Pasadas
            var btnVerRes = t.closest('.js-hen-ver-reserva');
            if (btnVerRes) {
                e.preventDefault();
                var rid = btnVerRes.getAttribute('data-reserva-id') || '';
                var cod = btnVerRes.getAttribute('data-reserva-cod') || '';
                hendbg('Click: Ver reserva #' + cod + ' (id=' + rid + ') en Mis Reservas', 'ev');

                if (window.apReloadFragment && rid) {
                    window.apReloadFragment(rid, 'salto-desde-historial-endosos');
                }
                var tabParent = document.querySelector('[data-bs-target="#cli-reservas"]');
                if (tabParent) tabParent.click();
                setTimeout(function () {
                    var tabChild = document.querySelector('[data-bs-target="#mr-sub-activas"]');
                    if (tabChild) tabChild.click();
                }, 120);
                return;
            }

            // Filtro por chip (Todos / Recibidos / Cedidos)
            var chip = t.closest('.hen-filter-chip');
            if (chip && chip.closest('#cliHistEndRoot')) {
                document.querySelectorAll('#cliHistEndRoot .hen-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosEndosos(true);
            }
        });

        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || t.id !== 'henSelectReserva') return;
            var opt = t.options[t.selectedIndex];
            var cod = opt ? (opt.getAttribute('data-cod') || 'Todas') : 'Todas';
            var est = opt ? (opt.getAttribute('data-estado') || '') : '';
            hendbg('Selector Reserva: #' + cod + (est ? ' · estado=[' + est + ']' : ''), 'ev');
            actualizarContadoresEndosos();
            aplicarFiltrosEndosos(true);
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'henSearchInput') {
                aplicarFiltrosEndosos(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosEndosos(true);
                }, 350);
            }
        });
    }

    actualizarContadoresEndosos();
    if (document.getElementById('henSelectReserva')?.value) {
        aplicarFiltrosEndosos(false);
    }

    var SRV = @json($henDbgData);
    hendbg('HI-END-v2-DBG activo · endosos=' + (SRV ? SRV.endosos : 0) +
           (SRV && SRV.conteos ? ' [recibidos=' + SRV.conteos.recibido + ', cedidos=' + SRV.conteos.cedido + ']' : ''), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { hendbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
