{{-- partials/pagos/reviews.blade.php · Cliente · Gestión Reactiva de Reseñas y Evaluaciones sin Reload (PG-REV-v2-DBG) --}}
<div id="sheet-pagos-reviews-cliente">

    {{-- PG-REV-v2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="pgRevDbgToggle" title="Debug Reseñas Cliente (PG-REV-v2-DBG)">
        <span class="pg-rev-dbg-led" id="pgRevDbgLed"></span>DBG<span class="pg-rev-dbg-badge" id="pgRevDbgBadge">0</span>
    </div>
    <div id="pgRevDbgPanel" role="dialog" aria-label="Panel de debug de Reseñas">
        <div class="pg-rev-dbg-header" id="pgRevDbgHeader">
            <span>DEBUG · Reseñas Cliente · PG-REV-v2-DBG</span>
            <span class="pg-rev-dbg-controls">
                <button type="button" class="pg-rev-dbg-btn" id="pgRevDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="pg-rev-dbg-btn" id="pgRevDbgCopy">Copiar</button>
                <button type="button" class="pg-rev-dbg-btn" id="pgRevDbgClear">Limpiar</button>
                <button type="button" class="pg-rev-dbg-btn" id="pgRevDbgClose">✕</button>
            </span>
        </div>
        <div id="pgRevDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $pgRevErrores = [];

        // 1. Aprovechar $reviews y $porResenar enviados por ClientePortalController@pagosReviewsFragmento
        $misReviews = (isset($reviews) && $reviews instanceof \Illuminate\Support\Collection) ? $reviews : collect();
        $elegibles  = (isset($porResenar) && $porResenar instanceof \Illuminate\Support\Collection) ? $porResenar : collect();

        try {
            if ($misReviews->isEmpty() && $uid && class_exists(\App\Models\Rsv\Review::class)) {
                $tablaRev = (new \App\Models\Rsv\Review)->getTable();
                $colAutor = \Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user_autor')
                    ? 'id_user_autor'
                    : (\Illuminate\Support\Facades\Schema::hasColumn($tablaRev, 'id_user') ? 'id_user' : 'user_id');

                $misReviews = \App\Models\Rsv\Review::where($colAutor, $uid)
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
            }

            if ($misReviews->isNotEmpty() && class_exists(\App\Models\Rsv\Review::class)) {
                $relRev = [];
                foreach (['reserva.inmueble', 'reserva.status', 'inmueble', 'tipoReceptor'] as $rTry) {
                    if (method_exists(\App\Models\Rsv\Review::class, explode('.', $rTry)[0])) {
                        $relRev[] = $rTry;
                    }
                }
                if (!empty($relRev)) {
                    $misReviews->loadMissing($relRev);
                }
            }

            $idsResenadas = $misReviews->map(function ($rv) {
                return $rv->id_rsv_reservas ?? $rv->id_rsv_reserva ?? $rv->reserva_id ?? null;
            })->filter()->unique()->all();

            if ($elegibles->isEmpty() && $uid && class_exists(\App\Models\Rsv\Reserva::class)) {
                $elegibles = \App\Models\Rsv\Reserva::with(['inmueble:id,name,city', 'status'])
                    ->where('id_user', $uid)
                    ->when(!empty($idsResenadas), fn ($q) => $q->whereNotIn('id', $idsResenadas))
                    ->orderByDesc('fecha_fin')
                    ->limit(30)
                    ->get();
            } elseif ($elegibles->isNotEmpty()) {
                $elegibles->loadMissing(['inmueble:id,name,city', 'status']);
            }

            // Excluir reservas canceladas/anuladas/rechazadas de las elegibles para reseñar
            $elegibles = $elegibles->reject(function ($r) use ($idsResenadas) {
                if (in_array($r->id, $idsResenadas)) return true;
                $st = mb_strtolower((string) (optional($r->status)->name ?? ''));
                return str_contains($st, 'cancela') || str_contains($st, 'anula') || str_contains($st, 'rechaz');
            })->values();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.pagos.reviews: ' . $e->getMessage());
            $pgRevErrores[] = $e->getMessage();
        }

        // 2. Tipos de Receptor para el formulario de calificación
        $tiposReceptorList = collect();
        $tipoReceptorDefaultId = 1;
        try {
            if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                $tiposReceptorList = \App\Models\Rsv\TipoReceptor::all();
                $tipoReceptorDefaultId = optional($tiposReceptorList->first())->id ?? 1;
            }
        } catch (\Throwable $e) {}

        // 3. Rutas de fragmento y guardado
        $rutaFragmento = '';
        foreach (['rsv.cliente.pagos.reviews.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $rutaStoreReview = '';
        foreach (['rsv.reviews.store-ajax', 'rsv.reviews.store', 'rsv.cliente.reviews.store', 'reviews.store'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaStoreReview = route($rn);
                break;
            }
        }

        $obtenerPuntos = function ($rev) {
            $p = $rev->puntuacion ?? $rev->calificacion ?? $rev->rating ?? 5;
            return max(1, min(5, (int) $p));
        };

        $sumaPuntos = 0;
        $conteos = [
            'all'     => $misReviews->count(),
            'alta'    => 0, // 5 estrellas
            'media'   => 0, // 4 o menos
            'pending' => $elegibles->count(),
        ];

        foreach ($misReviews as $rvItem) {
            $pts = $obtenerPuntos($rvItem);
            $sumaPuntos += $pts;
            if ($pts >= 5) {
                $conteos['alta']++;
            } else {
                $conteos['media']++;
            }
        }

        $promedio = $misReviews->count() > 0 ? round($sumaPuntos / $misReviews->count(), 1) : 0.0;

        $pgRevDbgData = [
            'version'     => 'PG-REV-v2-DBG',
            'controlador' => 'ClientePortalController@pagosReviewsFragmento',
            'vista'       => 'partials/pagos/reviews.blade.php',
            'publicadas'  => $misReviews->count(),
            'elegibles'   => $elegibles->count(),
            'promedio'    => $promedio,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'store_url'   => $rutaStoreReview ?: 'NO definida',
            'errores'     => $pgRevErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliRevRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="pagos-reviews"
         data-total="{{ $misReviews->count() }}"
         data-elegibles="{{ $elegibles->count() }}"
         data-promedio="{{ $promedio }}"
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

            /* ── BARRA DE FILTROS Y BUSCADOR ── */
            .pr-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .pr-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:8.5px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .pr-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .pr-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .pr-chip-count {
                font-size:7.5px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .pr-filter-chip.active .pr-chip-count { background:var(--accent); color:#fff; }

            .pr-input-ctrl {
                font-size:8.5px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 10px; outline:0; width:165px; transition:all .15s;
            }
            .pr-input-ctrl:focus { background:#fff; border-color:var(--accent-bd); width:195px; }

            /* ── BOTÓN NUEVA CALIFICACIÓN ── */
            .pr-btn-accent {
                height:27px; padding:0 11px; border-radius:6px; border:1px solid var(--accent);
                background:var(--accent); color:#fff; font-size:8.5px; font-weight:700;
                display:inline-flex; align-items:center; gap:5px; cursor:pointer; transition:all .15s;
            }
            .pr-btn-accent:hover:not(:disabled) { background:#2e5c43; border-color:#2e5c43; }
            .pr-btn-accent:disabled { opacity:.6; cursor:not-allowed; }

            /* ── TARJETAS DE RESEÑA ── */
            .pr-card {
                background:#fff; border:1px solid var(--grid-2); border-radius:9px;
                padding:11px 14px; margin-bottom:7px; transition:all .15s ease;
            }
            .pr-card:hover { border-color:var(--accent-bd); background:#fafdfc; }

            .pr-stars i { font-size:10px; color:#cbd5e1; margin-right:1px; }
            .pr-stars i.filled { color:#f59e0b; }

            /* ── ESTRELLAS INTERACTIVAS DEL FORMULARIO ── */
            .pr-star-group { display:inline-flex; flex-direction:row-reverse; gap:4px; }
            .pr-star-group input { display:none; }
            .pr-star-group label { font-size:19px; color:#cbd5e1; cursor:pointer; transition:color .15s; }
            .pr-star-group input:checked ~ label,
            .pr-star-group label:hover,
            .pr-star-group label:hover ~ label { color:#f59e0b; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
            }
            .reservas-soft .pill-success   { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning   { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .pr-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:7px 10px; font-size:8.5px; margin-bottom:10px; }
            .pr-alert-ok  { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:7px 10px; font-size:8.5px; margin-bottom:10px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #pgRevDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #pgRevDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .pg-rev-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .pg-rev-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:pgRevDbgBlink 1s steps(2,start) infinite; }
            @keyframes pgRevDbgBlink { 50% { opacity:.35; } }
            .pg-rev-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #pgRevDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .pg-rev-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .pg-rev-dbg-controls { display:flex; gap:4px; }
            .pg-rev-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .pg-rev-dbg-btn:hover { background:#475569; }
            #pgRevDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .pg-rev-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .pg-rev-dbg-time { color:#64748b; margin-right:4px; }
            .pg-rev-dbg-info { color:#93c5fd; } .pg-rev-dbg-ok { color:#86efac; } .pg-rev-dbg-warn { color:#fde68a; } .pg-rev-dbg-err { color:#fca5a5; } .pg-rev-dbg-net { color:#c4b5fd; } .pg-rev-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA + FILTROS CON CONTEO REAL + BUSCADOR + BOTÓN CALIFICAR --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="pr-filter-bar">
                <span class="val me-1" style="font-size:11px;">
                    <i class="bi bi-star-fill text-warning me-1"></i> Mis Reseñas
                </span>
                @if($promedio > 0)
                    <span class="pill pill-warning me-1" title="Promedio de tus calificaciones">
                        <i class="bi bi-star-fill me-1" style="font-size:6.5px;"></i> {{ number_format($promedio, 1) }} / 5.0
                    </span>
                @endif
                <button type="button" class="pr-filter-chip active" data-filter="all">
                    Todas <span class="pr-chip-count" id="prCntAll">{{ $conteos['all'] }}</span>
                </button>
                <button type="button" class="pr-filter-chip" data-filter="alta">
                    5 ★ <span class="pr-chip-count" id="prCntAlta">{{ $conteos['alta'] }}</span>
                </button>
                @if($conteos['media'] > 0)
                    <button type="button" class="pr-filter-chip" data-filter="media">
                        ≤ 4 ★ <span class="pr-chip-count" id="prCntMedia">{{ $conteos['media'] }}</span>
                    </button>
                @endif
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="text" id="prSearchInput" class="pr-input-ctrl" placeholder="Buscar reseña o #cód…">
                @if($rutaStoreReview && $elegibles->isNotEmpty())
                    <button type="button" class="pr-btn-accent" id="prBtnToggleForm">
                        <i class="bi bi-plus-circle"></i> Calificar estadía ({{ $elegibles->count() }})
                    </button>
                @endif
            </div>
        </div>

        <div id="prMainAlert" class="d-none"></div>

        {{-- PANEL DESPLEGABLE PARA REGISTRAR NUEVA CALIFICACIÓN SIN RECARGAR PÁGINA --}}
        @if($rutaStoreReview && $elegibles->isNotEmpty())
            <div id="prNewReviewBox" class="p-3 mb-3 rounded-3 border {{ $misReviews->isEmpty() ? '' : 'd-none' }}"
                 style="background:var(--head); border-color:var(--accent-bd) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="val" style="color:var(--accent);">
                        <i class="bi bi-award me-1"></i> Evaluar Estadía ({{ $elegibles->count() }} disponible(s) por calificar)
                    </span>
                    <button type="button" class="btn-close" style="font-size:8px;" id="prBtnCloseForm"></button>
                </div>

                <form id="cliRevForm" action="{{ $rutaStoreReview }}" method="POST">
                    @csrf
                    <input type="hidden" name="_ajax" value="1">
                    <input type="hidden" name="id_user" value="{{ $uid }}">
                    <input type="hidden" name="id_user_autor" value="{{ $uid }}">
                    <input type="hidden" name="user_id" value="{{ $uid }}">

                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="lbl d-block mb-1">Reserva a Evaluar *</label>
                            <select name="id_rsv_reservas" id="prSelectReserva" class="form-select form-select-sm" style="font-size:9px;" required
                                    onchange="var f=this.form; f.querySelector('[name=id_rsv_reserva]').value=this.value; f.querySelector('[name=reserva_id]').value=this.value;">
                                @foreach($elegibles as $rEl)
                                    @php
                                        $elFi = $rEl->fecha_inicio ? \Carbon\Carbon::parse($rEl->fecha_inicio)->format('d/m/y') : '—';
                                        $elSt = optional($rEl->status)->name ?? 'Registrada';
                                    @endphp
                                    <option value="{{ $rEl->id }}" data-cod="{{ $rEl->codigo_reserva }}">
                                        #{{ $rEl->codigo_reserva }} · {{ optional($rEl->inmueble)->name ?? 'Inmueble' }} ({{ $elFi }}) [{{ $elSt }}]
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id_rsv_reserva" value="{{ optional($elegibles->first())->id }}">
                            <input type="hidden" name="reserva_id" value="{{ optional($elegibles->first())->id }}">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="lbl d-block mb-1">Receptor</label>
                            @if($tiposReceptorList->isNotEmpty())
                                <select name="id_rsv_tipo_receptor" class="form-select form-select-sm" style="font-size:9px;" required
                                        onchange="this.form.querySelector('[name=id_rsv_tipos_receptor]').value = this.value">
                                    @foreach($tiposReceptorList as $tr)
                                        <option value="{{ $tr->id }}">{{ $tr->nombre ?? $tr->name ?? ('Área #' . $tr->id) }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                            @else
                                <input type="hidden" name="id_rsv_tipo_receptor" value="{{ $tipoReceptorDefaultId }}">
                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                                <input type="text" class="form-control form-control-sm" style="font-size:9px;" value="Estadía General" disabled>
                            @endif
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="lbl d-block mb-1">Aspecto</label>
                            <select name="tipo_evaluacion" class="form-select form-select-sm" style="font-size:9px;" required>
                                <option value="General">General</option>
                                <option value="Inmueble">Inmueble</option>
                                <option value="Limpieza">Limpieza</option>
                                <option value="Atencion">Atención</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-3 text-md-center">
                            <label class="lbl d-block mb-1">Puntuación *</label>
                            <div class="pr-star-group">
                                @for($s = 5; $s >= 1; $s--)
                                    <input type="radio" id="prStar_{{ $s }}" name="puntuacion" value="{{ $s }}" {{ $s === 5 ? 'checked' : '' }}
                                           onchange="this.form.querySelector('[name=calificacion]').value = this.value" required>
                                    <label for="prStar_{{ $s }}" title="{{ $s }} estrellas"><i class="bi bi-star-fill"></i></label>
                                @endfor
                            </div>
                            <input type="hidden" name="calificacion" value="5">
                        </div>

                        <div class="col-12 col-md-9 mt-2">
                            <label class="lbl d-block mb-1">Comentario de tu experiencia *</label>
                            <input type="text" name="comentario" class="form-control form-control-sm" style="font-size:9px;"
                                   placeholder="Describe cómo estuvo el inmueble, la limpieza y la atención recibida…" required maxlength="600">
                        </div>

                        <div class="col-12 col-md-3 mt-2">
                            <button type="submit" class="pr-btn-accent w-100 justify-content-center" id="prBtnSubmitRev" style="height:29px;">
                                <i class="bi bi-check2-circle"></i> Publicar Reseña
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        {{-- LISTADO DE RESEÑAS PUBLICADAS --}}
        <div id="prReviewsList">
            @forelse($misReviews as $rev)
                @php
                    $pts       = $obtenerPuntos($rev);
                    $stGrp     = $pts >= 5 ? 'alta' : 'media';
                    $resObj    = $rev->reserva;
                    $resId     = $resObj->id ?? $rev->id_rsv_reservas ?? $rev->reserva_id ?? null;
                    $resCod    = $resObj->codigo_reserva ?? ($resId ? '#' . $resId : 'S/C');
                    $inmObj    = $rev->inmueble ?? optional($resObj)->inmueble;
                    $inmNom    = optional($inmObj)->name ?? 'Propiedad Evaluada';
                    $recNom    = optional($rev->tipoReceptor)->nombre ?? optional($rev->tipoReceptor)->name ?? ($rev->tipo_evaluacion ?? 'Estadía General');
                    $aspecto   = $rev->tipo_evaluacion ?? null;
                    $coment    = trim((string) ($rev->comentario ?? ''));
                    $fechaFmt  = optional($rev->created_at)->format('d/m/Y h:i A') ?? '—';
                @endphp

                <div class="pr-card js-pr-item"
                     data-rev-id="{{ $rev->id }}"
                     data-reserva-id="{{ $resId }}"
                     data-reserva-cod="{{ $resCod }}"
                     data-st-group="{{ $stGrp }}"
                     data-search="{{ mb_strtolower($resCod . ' ' . $inmNom . ' ' . $recNom . ' ' . $coment) }}">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="val">{{ $inmNom }}</span>
                            <span class="sub">#{{ $resCod }}</span>
                            <span class="pill pill-secondary">{{ $recNom }}</span>
                            @if($aspecto && $aspecto !== $recNom)
                                <span class="pill pill-success">{{ $aspecto }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="pr-stars" title="{{ $pts }} de 5 estrellas">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="bi bi-star-fill {{ $s <= $pts ? 'filled' : '' }}"></i>
                                @endfor
                            </span>
                            <span class="sub">{{ $fechaFmt }}</span>
                        </div>
                    </div>

                    @if($coment !== '')
                        <div class="sub mt-1" style="color:var(--ink-2); font-size:9px;">
                            "{{ $coment }}"
                        </div>
                    @else
                        <div class="sub mt-1 fst-italic">Calificación registrada sin comentario adicional.</div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                    <i class="bi bi-star text-muted d-block mb-2" style="font-size:22px;"></i>
                    <span class="sub d-block">Aún no has publicado reseñas sobre tus estadías.</span>
                    @if($elegibles->isNotEmpty())
                        <span class="sub d-block mt-1" style="color:var(--accent); font-weight:700;">
                            Tienes {{ $elegibles->count() }} reserva(s) disponible(s) arriba para calificar.
                        </span>
                    @endif
                </div>
            @endforelse
        </div>

        @if($misReviews->isNotEmpty())
            <div id="prEmptyFilterMsg" class="text-center py-4 mt-2 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                <span class="sub d-block">No hay reseñas que coincidan con el filtro seleccionado.</span>
            </div>
        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL PG-REV-v2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.PgRevDbg && window.PgRevDbg.alive && window.PgRevDbg.alive())
        ? window.PgRevDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-pagos-reviews-cliente') || document.body;
                if (!$('pgRevDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'pgRevDbgToggle'; t.title = 'Debug Reseñas Cliente (PG-REV-v2-DBG)';
                    t.innerHTML = '<span class="pg-rev-dbg-led" id="pgRevDbgLed"></span>DBG<span class="pg-rev-dbg-badge" id="pgRevDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('pgRevDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'pgRevDbgPanel';
                    p.innerHTML = '<div class="pg-rev-dbg-header" id="pgRevDbgHeader">' +
                        '<span>DEBUG · Reseñas Cliente · PG-REV-v2-DBG</span>' +
                        '<span class="pg-rev-dbg-controls">' +
                        '<button type="button" class="pg-rev-dbg-btn" id="pgRevDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="pg-rev-dbg-btn" id="pgRevDbgCopy">Copiar</button>' +
                        '<button type="button" class="pg-rev-dbg-btn" id="pgRevDbgClear">Limpiar</button>' +
                        '<button type="button" class="pg-rev-dbg-btn" id="pgRevDbgClose">✕</button>' +
                        '</span></div><div id="pgRevDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = [
                    'psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle',
                    'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle', 'huDbgToggle',
                    'hiIniDbgToggle', 'hiEstDbgToggle', 'hiEndDbgToggle',
                    'chIniDbgToggle', 'chMsgDbgToggle', 'chRecDbgToggle',
                    'pgIniDbgToggle', 'pgTxDbgToggle', 'pgPasDbgToggle'
                ];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('pgRevDbgToggle'), p = $('pgRevDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('pgRevDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'pg-rev-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="pg-rev-dbg-time">[' + hora() + ']</span><span class="pg-rev-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('pgRevDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('pgRevDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('pgRevDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('pgRevDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('pgRevDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('pgRevDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('pgRevDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('pgRevDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('pgRevDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Reseñas PG-REV-v2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('pgRevDbgToggle'); }
            };
            window.PgRevDbg = api;
            return api;
        })();

    DBG.ensure();
    var pgrevdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#pgRevDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#pgRevDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    function aplicarFiltrosReviews(logAccion) {
        var chipActivo = document.querySelector('#cliRevRoot .pr-filter-chip.active');
        var grupo = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var q = (document.getElementById('prSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#prReviewsList .js-pr-item').forEach(function (item) {
            var matchGrupo = (grupo === 'all') || (item.getAttribute('data-st-group') === grupo);
            var matchTexto = !q || (item.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar = matchGrupo && matchTexto;
            item.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('prEmptyFilterMsg');
        if (emptyMsg) emptyMsg.classList.toggle('d-none', visibles > 0);

        if (logAccion) {
            pgrevdbg('Filtro aplicado · grupo="' + grupo + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE RESEÑAS SIN RECARGAR PÁGINA ═══
    window.pgRevReloadFragment = function (motivo) {
        var root = document.getElementById('cliRevRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'pagos-reviews';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        pgrevdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                pgrevdbg('Error HTTP ' + res.status + ' al refrescar Reseñas (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliRevRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                var elg = nuevoRoot.getAttribute('data-elegibles') || '0';
                var prm = nuevoRoot.getAttribute('data-promedio') || '0';
                pgrevdbg('Reseñas actualizadas (' + res.ms + 'ms) · publicadas=' + tot + ' · elegibles=' + elg + ' · promedio=' + prm, 'ok');
            } else {
                pgrevdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliRevRoot', 'warn');
            }
        })
        .catch(function (err) {
            pgrevdbg('Excepción recargando Reseñas: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    if (!window.__jsPagosReviewsClienteV2Listo) {
        window.__jsPagosReviewsClienteV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#pgRevDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#pgRevDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#pgRevDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#pgRevDbgCopy'))    { e.stopPropagation(); DBG.copy(); pgrevdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#pgRevDbgRefresh')) {
                e.stopPropagation();
                window.pgRevReloadFragment('manual-btn-dbg');
                return;
            }

            // Abrir / cerrar panel de nueva calificación
            if (t.closest('#prBtnToggleForm')) {
                var box = document.getElementById('prNewReviewBox');
                if (box) {
                    var abriendo = box.classList.contains('d-none');
                    box.classList.toggle('d-none');
                    pgrevdbg('Click: ' + (abriendo ? 'Abrir' : 'Cerrar') + ' formulario Evaluar Estadía', 'ev');
                }
                return;
            }
            if (t.closest('#prBtnCloseForm')) {
                var boxC = document.getElementById('prNewReviewBox');
                if (boxC) boxC.classList.add('d-none');
                pgrevdbg('Click: Cerrar formulario Evaluar Estadía', 'ev');
                return;
            }

            // Filtro por chip
            var chip = t.closest('.pr-filter-chip');
            if (chip && chip.closest('#cliRevRoot')) {
                document.querySelectorAll('#cliRevRoot .pr-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltrosReviews(true);
            }
        });

        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'prSearchInput') {
                aplicarFiltrosReviews(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltrosReviews(true);
                }, 350);
            }
        });

        // Envío AJAX de nueva reseña
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.id !== 'cliRevForm') return;
            e.preventDefault();

            var btn = document.getElementById('prBtnSubmitRev');
            var alertBox = document.getElementById('prMainAlert');
            var oldHtml = btn ? btn.innerHTML : '';

            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Publicando…'; }

            var fd = new FormData(form);
            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            pgrevdbg('POST Reseña (reserva_id=' + (fd.get('id_rsv_reservas') || '?') + ' · ★=' + (fd.get('puntuacion') || '5') + ') → ' + form.getAttribute('action'), 'net');

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
                    pgrevdbg('Reseña publicada con éxito · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');
                    if (window.pgIniReloadFragment) window.pgIniReloadFragment('post-review');
                    return window.pgRevReloadFragment('post-review').then(function () {
                        var nuevoAlert = document.getElementById('prMainAlert');
                        if (nuevoAlert) {
                            nuevoAlert.className = 'pr-alert-ok';
                            nuevoAlert.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ¡Gracias! Tu calificación fue registrada correctamente.';
                        }
                    });
                }

                pgrevdbg('Error al publicar reseña (HTTP ' + res.status + ' · ' + res.ms + 'ms): ' + (j.message || 'Verifica los datos'), 'err');
                var listaErrores = [];
                if (j.errors && typeof j.errors === 'object') {
                    Object.keys(j.errors).forEach(function (campo) {
                        [].concat(j.errors[campo]).forEach(function (m) {
                            listaErrores.push('<li><b>' + campo + ':</b> ' + m + '</li>');
                            pgrevdbg('422 [' + campo + ']: ' + m, 'err');
                        });
                    });
                }
                if (alertBox) {
                    alertBox.className = 'pr-alert-err';
                    alertBox.innerHTML = '<b>' + (j.message || 'No se pudo publicar la reseña') + '</b>' +
                        (listaErrores.length ? '<ul class="mb-0 mt-1 ps-3">' + listaErrores.join('') + '</ul>' : '');
                }
            })
            .catch(function (err) {
                pgrevdbg('Error de red al publicar reseña: ' + (err && err.message ? err.message : 'red'), 'err');
                if (alertBox) {
                    alertBox.className = 'pr-alert-err';
                    alertBox.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Error de conexión al publicar la reseña.';
                }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
            });
        });
    }

    var SRV = @json($pgRevDbgData);
    pgrevdbg('PG-REV-v2-DBG activo · publicadas=' + (SRV ? SRV.publicadas : 0) +
             ' · elegibles=' + (SRV ? SRV.elegibles : 0) +
             ' · promedio=' + (SRV ? SRV.promedio : 0), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { pgrevdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
