{{-- partials/reservas/reservas.blade.php · v27 · SHOW de UNA reserva --}}
{{-- Diseño minimalista v5 (.reservas-soft) · TIEMPO REAL: cero recargas + AUTO-SYNC --}}
{{-- Motor de sincronización automática (polling 15s) con guardas inteligentes --}}
{{-- SEGURA: SIN borrados · Interceptador DELEGADO: ningún submit nativo --}}
{{-- Pagos: pasarela "Directo" automática · anulaciones lógicas visibles --}}
{{-- Huéspedes: modales ID ÚNICO + edición genérica · Endoso: cédula -> auto --}}

@php
    /* URLs de apoyo (si las rutas existen) */
    $fragUrl = \Illuminate\Support\Facades\Route::has('rsv.admin.reservas.fragmento')
        ? route('rsv.admin.reservas.fragmento') : null;
    $usersUrl = \Illuminate\Support\Facades\Route::has('rsv.admin.usuarios.buscar')
        ? route('rsv.admin.usuarios.buscar') : null;

    /* Contexto temprano: NECESARIO antes del div raiz */
    $xlsInm   = $inmueble ?? null;
    $xlsInmId = is_object($xlsInm) ? ($xlsInm->id ?? null) : null;
    if (!$xlsInmId && !empty($inmuebleId) && is_numeric($inmuebleId)) { $xlsInmId = $inmuebleId; }
    if (!$xlsInmId && is_numeric(request('inmueble_id')))             { $xlsInmId = request('inmueble_id'); }
    $modoGlobal = empty($xlsInmId);
@endphp

<div class="reservas-soft rsvshow p-3 bg-white rounded-3 border" id="rsvShowRoot"
     data-frag="{{ $fragUrl }}"
     data-users="{{ $usersUrl }}"
     data-huesped-update="{{ \Illuminate\Support\Facades\Route::has('rsv.reserva-huespedes.update') ? route('rsv.reserva-huespedes.update', 'ID') : '' }}"
     data-inm="{{ $modoGlobal ? '' : $xlsInmId }}"
     data-q="{{ request('reserva_q') }}"
     data-res="{{ is_numeric(request('reserva_id')) ? request('reserva_id') : '' }}"
     data-poll="15000"
     style="border-color:#f1f5f9 !important;">

<style>
    /* ═══ BASE (idéntica al v5) ═══ */
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); display:block; margin-bottom:2px; }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .strong { color:var(--ink-2); font-weight:600; }
    .reservas-soft .btn-mini { border:1px solid var(--grid-2); background:#fff; color:var(--ink-3); font-family:inherit; font-size:8.5px; font-weight:500; padding:1px 8px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:3px; transition:all .15s; }
    .reservas-soft .btn-mini:hover { color:var(--ink-2); border-color:#d7dee6; background:#fafcfe; text-decoration:none; transform:translateY(-1px); }
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-family:inherit; font-size:8.5px; font-weight:600; padding:2px 10px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:all .15s; }
    .reservas-soft .btn-green:hover { background:#e3f1e9; color:#34684e; text-decoration:none; transform:translateY(-1px); }
    .reservas-soft .xls-search { font-size:9px; color:var(--ink-2); background:#fff; border:1px solid var(--grid-2); border-radius:3px; padding:3px 8px; box-shadow:none !important; outline:0; }
    .reservas-soft .xls-search:focus { border-color:#c8d6cd; }
    .reservas-soft .count-badge { display:inline-flex; align-items:center; gap:4px; font-size:8px; font-weight:600; font-variant-numeric:tabular-nums; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2); border-radius:8px; padding:1px 8px; white-space:nowrap; }
    .reservas-soft .count-badge b { color:var(--accent); }
    .reservas-soft .ctx-chip { display:inline-flex; align-items:center; gap:3px; font-size:7.5px; font-weight:600; color:var(--accent); background:var(--accent-bg); border:1px solid var(--accent-bd); border-radius:8px; padding:0 7px; line-height:14px; white-space:nowrap; }
    .reservas-soft .safe-chip { display:inline-flex; align-items:center; gap:3px; font-size:7.5px; font-weight:600; color:#4a6fa5; background:#f2f6fc; border:1px solid #dbe7f5; border-radius:8px; padding:0 7px; line-height:14px; white-space:nowrap; }
    .reservas-soft .st-dot { display:inline-block; width:9px; height:9px; border-radius:3px; border:1px solid rgba(0,0,0,.08); vertical-align:middle; }
    .reservas-soft .cd { display:inline-block; padding:0 6px; border-radius:8px; font-size:7.5px; font-weight:700; border:1px solid; letter-spacing:.02em; line-height:13px; }
    .reservas-soft .cd-today  { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .cd-next   { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
    .reservas-soft .cd-future { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .cd-live   { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
    .reservas-soft .cd-done   { color:#c8d0d9; background:#fbfcfd; border-color:#f3f6f9; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
    .reservas-soft .st-info { background:#f2f6fc; border:1px solid #dbe7f5; border-radius:4px; color:#4a6fa5; font-size:8px; font-weight:600; padding:6px 9px; }
    /* Anulación lógica */
    .rsvshow .tx-anulada .tx-monto { text-decoration:line-through; color:var(--ink-4); }
    .rsvshow .tx-conservada { font-size:7px; color:#8a97a5; font-weight:600; display:inline-flex; align-items:center; gap:2px; }

    /* ═══ LIVE SYNC (tiempo real) ═══ */
    .rsv-live { display:inline-flex; align-items:center; gap:5px; font-size:7.5px; font-weight:600; color:var(--accent); background:var(--accent-bg); border:1px solid var(--accent-bd); border-radius:8px; padding:0 8px; line-height:16px; white-space:nowrap; }
    .rsv-live .live-dot { width:6px; height:6px; border-radius:50%; background:var(--accent); animation:rsvPulse 1.8s ease-in-out infinite; }
    .rsv-live.paused { color:var(--ink-4); background:var(--head); border-color:var(--grid-2); }
    .rsv-live.paused .live-dot { background:var(--ink-4); animation:none; }
    @keyframes rsvPulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.35; transform:scale(.8); } }

    /* ═══ PILLS ═══ */
    .reservas-soft .pill { display:inline-flex; align-items:center; padding:1px 8px; border-radius:10px; font-size:8px; font-weight:700; border:1px solid; letter-spacing:.02em; line-height:13px; text-transform:uppercase; white-space:nowrap; }
    .reservas-soft .pill-success   { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; }
    .reservas-soft .pill-warning   { color:#a3822f; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .pill-danger    { color:#b05c5c; background:#fdf1f1; border-color:#f3d8d8; }
    .reservas-soft .pill-primary   { color:#4a6fa5; background:#f2f6fc; border-color:#dbe7f5; }
    .reservas-soft .pill-secondary { color:#8a97a5; background:#fafbfd; border-color:#eef2f6; }

    /* ═══ TARJETAS Y LISTAS ═══ */
    .reservas-soft .fact { padding:8px 10px; background:var(--head); border:1px solid var(--grid); border-radius:6px; height:100%; }
    .reservas-soft .fact.hl { background:var(--accent-bg); border-color:var(--accent-bd); }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; transition:all .15s; }
    .reservas-soft .list-item-app:hover { border-color:var(--accent-bd); background:#fafcfe; }
    .reservas-soft .rv-ellip { max-width:190px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    /* ═══ SECCIONES AMIGABLES ═══ */
    .reservas-soft .sec-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:3px; }
    .reservas-soft .sec-title { display:flex; align-items:center; gap:5px; }
    .reservas-soft .sec-title .lbl { margin-bottom:0; color:var(--ink-2); font-size:8px; }
    .reservas-soft .sec-hint { font-size:7px; color:var(--ink-4); font-weight:500; letter-spacing:.02em; }
    .reservas-soft .empty-friendly { text-align:center; padding:14px 10px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .reservas-soft .empty-friendly i { font-size:16px; color:#c3ccd6; display:block; margin-bottom:4px; }
    .reservas-soft .empty-friendly .hint { font-size:8px; color:var(--accent); font-weight:600; display:block; margin-top:3px; }

    /* ═══ STEPPER ═══ */
    .reservas-soft .ruta { display:flex; align-items:flex-start; overflow-x:auto; padding:6px 2px 2px; }
    .reservas-soft .rt-step { display:flex; flex-direction:column; align-items:center; min-width:78px; position:relative; }
    .reservas-soft .rt-step.clickable { cursor:pointer; }
    .reservas-soft .rt-step.clickable:hover .rt-name { color:var(--accent); }
    .reservas-soft .rt-dot { width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:8px; font-weight:700; border:2px solid var(--grid-2); background:#fff; color:var(--ink-4); z-index:1; }
    .reservas-soft .rt-step.done .rt-dot    { background:var(--accent-bg); border-color:var(--accent); color:var(--accent); }
    .reservas-soft .rt-step.active .rt-dot  { background:var(--accent); border-color:var(--accent); color:#fff; box-shadow:0 0 0 3px var(--accent-bg); }
    .reservas-soft .rt-step.terminal .rt-dot { background:#fdf1f1; border-color:#e0b4b4; color:#b05c5c; }
    .reservas-soft .rt-step.active.terminal .rt-dot { background:#b05c5c; border-color:#b05c5c; color:#fff; box-shadow:0 0 0 3px #fdf1f1; }
    .reservas-soft .rt-line { position:absolute; top:9px; left:-50%; width:100%; height:2px; background:var(--grid-2); z-index:0; }
    .reservas-soft .rt-step.done .rt-line { background:var(--accent); }
    .reservas-soft .rt-step:first-child .rt-line { display:none; }
    .reservas-soft .rt-name { margin-top:3px; font-size:7.5px; font-weight:600; color:var(--ink-3); text-align:center; line-height:1.15; padding:0 4px; }
    .reservas-soft .rt-step.active .rt-name { color:var(--accent); }
    .reservas-soft .rt-step.terminal .rt-name { color:#b05c5c; }

    /* ═══ CHAT ═══ */
    .rsvshow .chat-bg { background-color:#eef1f3; }
    .rsvshow .msg-bubble { max-width:85%; padding:4px 8px; border-radius:7px; position:relative; font-size:8.5px; margin-bottom:5px; box-shadow:0 1px 1px rgba(0,0,0,.06); color:var(--ink-2); }
    .rsvshow .msg-sent { background-color:#dcf3d8; align-self:flex-end; border-bottom-right-radius:0; }
    .rsvshow .msg-received { background-color:#fff; align-self:flex-start; border-bottom-left-radius:0; }
    .rsvshow .msg-time { font-size:6.5px; color:#8fa0a8; text-align:right; margin-top:1px; }
    .rsvshow .chat-input-grp { background:#f4f6f7; padding:6px 8px; border-radius:0 0 6px 6px; }
    .rsvshow .chat-input { border-radius:14px !important; font-size:9px; padding:4px 10px; border:none; box-shadow:none !important; outline:0; }

    /* ═══ ESTRELLAS ═══ */
    .rsvshow .star-rating i { font-size:10px; color:#e4e5e9; }
    .rsvshow .star-rating i.filled { color:#e0b453; }

    /* ═══ TOAST ═══ */
    .rsv-toast { position:fixed; bottom:14px; right:14px; z-index:1090; display:none; font-size:9px; font-weight:600; padding:7px 12px; border-radius:5px; box-shadow:0 4px 14px rgba(0,0,0,.12); border:1px solid; max-width:320px; }
    .rsv-toast.ok { background:#f0f7f2; color:#2e7d4f; border-color:#d5e7dc; }
    .rsv-toast.err { background:#fdf5f4; color:#b05c5c; border-color:#f0dcd8; }

    /* ═══ AUTOCOMPLETE USUARIO (endoso) ═══ */
    .rsvshow .user-results { position:absolute; left:0; right:0; top:100%; z-index:1055; background:#fff; border:1px solid var(--grid-2); border-radius:5px; box-shadow:0 6px 18px rgba(0,0,0,.10); max-height:180px; overflow:auto; }
    .rsvshow .user-results .u-item { display:block; width:100%; text-align:left; background:#fff; border:0; border-bottom:1px solid var(--grid); padding:5px 9px; cursor:pointer; font-family:inherit; }
    .rsvshow .user-results .u-item:last-child { border-bottom:0; }
    .rsvshow .user-results .u-item:hover { background:var(--accent-bg); }
    .rsvshow .endoso-status { display:flex; align-items:center; gap:5px; border:1px solid var(--grid); border-radius:4px; padding:4px 8px; font-size:8.5px; min-height:26px; transition:all .15s; }
    .rsvshow .endoso-status[data-state="empty"]   { color:var(--ink-4); background:var(--head); }
    .rsvshow .endoso-status[data-state="loading"] { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
    .rsvshow .endoso-status[data-state="found"]   { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; font-weight:700; }
    .rsvshow .endoso-status[data-state="none"]    { color:#b05c5c; background:#fdf5f4; border-color:#f0dcd8; font-weight:600; }
    .rsvshow .endoso-status .st-mini { display:inline-block; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .rsvshow .endoso-status[data-state="found"] .st-mini { background:var(--accent); }
    .rsvshow .endoso-status[data-state="none"]  .st-mini { background:#c98a85; }
    .rsvshow .id-locked { background:var(--head) !important; color:var(--ink-3) !important; cursor:not-allowed; }

    /* ═══ MODALES (v5) ═══ */
    .reservas-soft-modal .modal-content { border:1px solid #e9eef4; border-radius:5px; font-family:Calibri,"Segoe UI",sans-serif; font-size:9.5px; color:#64748b; }
    .reservas-soft-modal .form-label { font-size:7.5px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; margin-bottom:2px; }
    .reservas-soft-modal .modal-header { border-bottom:1px solid #f1f5f9; padding:9px 14px; }
    .reservas-soft-modal .modal-footer { border-top:1px solid #f1f5f9; padding:9px 14px; }
    .reservas-soft-modal .form-control, .reservas-soft-modal .form-select { font-size:9.5px; }
</style>

@php
    /* Contexto completo */
    $hoy = now()->startOfDay();
    $currentUserId = \Illuminate\Support\Facades\Auth::id();

    /* Helpers visuales */
    $stDot = function ($name) {
        $s = mb_strtolower((string) $name);
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) return 'success';
        if (str_contains($s, 'pendien'))  return 'warning';
        if (str_contains($s, 'cancela') || str_contains($s, 'rechaz') || str_contains($s, 'fallid')) return 'danger';
        if (str_contains($s, 'final') || str_contains($s, 'complet'))  return 'primary';
        return 'secondary';
    };
    $esTerminal = function ($name) {
        $s = mb_strtolower((string) $name);
        return str_contains($s, 'cancela') || str_contains($s, 'rechaz');
    };
    $parseFecha = function ($v) {
        if (empty($v)) return null;
        try { return \Carbon\Carbon::parse($v)->startOfDay(); } catch (\Throwable $e) { return null; }
    };

    /* Busqueda + seleccion (una sola reserva) */
    $q     = trim((string) request('reserva_q'));
    $selId = request('reserva_id');
    $candidatas = collect();
    $reservaSel = null;
    $fueraDeContexto = false;

    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {

            if (is_numeric($selId)) {
                $rq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status', 'origen', 'huespedes']);
                if (!$modoGlobal && $xlsInmId) { $rq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                $reservaSel = $rq->find($selId);
                if (!$reservaSel) {
                    $otra = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status', 'origen', 'huespedes'])->find($selId);
                    if ($otra) { $reservaSel = $otra; $fueraDeContexto = true; }
                }
                if ($reservaSel) {
                    foreach (['transacciones.pasarela', 'mensajes.remitente', 'reviews.autor', 'historialEndosos.usuarioAnterior', 'historialEndosos.usuarioNuevo'] as $rel) {
                        try { $reservaSel->load($rel); } catch (\Throwable $e) { /* rel inexistente: ignorar */ }
                    }
                }
            }

            if ($q !== '') {
                $buildCandidatas = function ($conNid) use ($q, $modoGlobal, $xlsInmId) {
                    $bq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status'])
                        ->where(function ($w) use ($q, $conNid) {
                            $like = '%' . $q . '%';
                            $w->where('codigo_reserva', 'like', $like)
                              ->orWhereHas('user', function ($u) use ($like, $conNid) {
                                  $u->where('name', 'like', $like)->orWhere('email', 'like', $like);
                                  if ($conNid) { $u->orWhere('nid', 'like', $like); }
                              });
                        });
                    if (!$modoGlobal && $xlsInmId) { $bq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                    return $bq->orderBy('created_at', 'desc')->limit(8)->get();
                };
                try { $candidatas = $buildCandidatas(true); } catch (\Throwable $eNid) {
                    try { $candidatas = $buildCandidatas(false); } catch (\Throwable $e2) { $candidatas = collect(); }
                }
            } elseif (!$reservaSel) {
                $lq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status']);
                if (!$modoGlobal && $xlsInmId) { $lq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                $candidatas = $lq->orderBy('created_at', 'desc')->limit(8)->get();
            }
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('reservas.show: ' . $e->getMessage());
    }

    /* Estados */
    $statuses = collect();
    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $statuses = \App\Models\Rsv\Status::query()->orderBy('id')->get();
        }
    } catch (\Throwable $e) { $statuses = collect(); }
    $steps = $statuses->values();

    $curKey = ($reservaSel && $steps->isNotEmpty())
        ? $steps->search(fn ($s) => (string) $s->id === (string) $reservaSel->id_rsv_statuses)
        : false;
    $siguiente = null;
    if ($curKey !== false) {
        for ($i = $curKey + 1; $i < $steps->count(); $i++) {
            if (!$esTerminal($steps[$i]->name)) { $siguiente = $steps[$i]; break; }
        }
    }

    /* Historial */
    $historial = collect();
    if ($reservaSel) {
        try { $historial = $reservaSel->historialEstados()->orderBy('created_at', 'desc')->limit(6)->get(); }
        catch (\Throwable $e) { $historial = collect(); }
    }

    /* Rutas existentes */
    $rutaEstado = null;
    foreach (['rsv.reservas.cambiar_estado', 'rsv.reservas.cambiar-estado'] as $rt) {
        if (\Illuminate\Support\Facades\Route::has($rt)) { $rutaEstado = $rt; break; }
    }
    $rutaPdf = \Illuminate\Support\Facades\Route::has('rsv.reservas.pdf') ? 'rsv.reservas.pdf' : null;
    $rutaHuespedStore  = \Illuminate\Support\Facades\Route::has('rsv.reserva-huespedes.store')  ? 'rsv.reserva-huespedes.store'  : null;
    $rutaPagoStore     = \Illuminate\Support\Facades\Route::has('rsv.transacciones.store')      ? 'rsv.transacciones.store'      : null;
    $rutaEndosoStore   = \Illuminate\Support\Facades\Route::has('rsv.historial-endosos.store')  ? 'rsv.historial-endosos.store'  : null;
    $rutaReviewStore   = \Illuminate\Support\Facades\Route::has('rsv.reviews.store')            ? 'rsv.reviews.store'            : null;
    $rutaChatStore     = \Illuminate\Support\Facades\Route::has('rsv.mensajes.store')           ? 'rsv.mensajes.store'           : null;

    $pasarelas = collect();
    try {
        if (class_exists(\App\Models\Rsv\Pasarela::class)) { $pasarelas = \App\Models\Rsv\Pasarela::all(); }
    } catch (\Throwable $e) { $pasarelas = collect(); }
@endphp

<!-- ALERTAS DE SESION -->
@if (session('success'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:9px; border-radius:5px;">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:9px; border-radius:5px;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
    </div>
@endif

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-calendar2-check me-1" style="font-size:8px; color:#5d8a70;"></i>
        {{ $modoGlobal ? 'Gestión de Reserva' : 'Reserva del Inmueble' }}
        <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">v27</span>
    </h6>
    <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
        <span class="rsv-live" id="rsvLiveBadge" title="Sincronización automática activa: los cambios aparecen solos, sin recargar">
            <span class="live-dot"></span> <span id="rsvLiveTxt">en vivo</span>
        </span>
        @if(!$modoGlobal)
            <span class="ctx-chip" title="Filtrado por inmueble"><i class="bi bi-funnel-fill"></i> {{ is_object($xlsInm) ? ($xlsInm->name ?? 'Inmueble') : ('Inmueble #'.$xlsInmId) }}</span>
        @endif
        <span class="safe-chip" title="Esta vista no permite eliminar registros. Las anulaciones son lógicas: el registro y su soporte se conservan.">
            <i class="bi bi-shield-check"></i> Sin borrados
        </span>
        <span class="count-badge"><b>{{ $reservaSel ? 1 : $candidatas->count() }}</b> en vista</span>
    </div>
</div>

<!-- BUSCADOR -->
<form action="#" method="GET" id="rsvShowSearch" class="d-flex flex-wrap align-items-center mb-3" style="gap:8px;">
    <div class="d-flex align-items-center flex-grow-1" style="gap:6px; min-width:220px;">
        <i class="bi bi-search" style="font-size:8.5px; color:#c3ccd6;"></i>
        <input type="text" name="reserva_q" value="{{ $q }}" class="xls-search flex-grow-1" style="min-width:180px;"
               placeholder="Código, titular, email o cédula…" autocomplete="off" title="Los resultados aparecen al instante, sin recargar">
    </div>
    <button type="submit" class="btn-green" title="Buscar ahora"><i class="bi bi-arrow-return-left" style="font-size:8px;"></i> Cargar</button>
    @if($q !== '' || $reservaSel)
        <a href="#" class="btn-mini text-decoration-none" id="rsvClearLink" title="Volver al listado completo">
            <i class="bi bi-x-lg" style="font-size:7.5px;"></i> Limpiar
        </a>
    @endif
</form>

<!-- CANDIDATAS -->
@if(!$reservaSel)
    <div class="mb-2">
        <div class="lbl mb-1">
            <i class="bi bi-{{ $q !== '' ? 'funnel' : 'clock-history' }} me-1"></i>
            {{ $q !== '' ? 'Resultados ('.$candidatas->count().') · haz clic en una para gestionarla' : 'Últimas reservas · haz clic en una para gestionar su flujo' }}
        </div>
        @forelse($candidatas as $c)
            @php
                $cFi = $parseFecha($c->fecha_inicio);
                $cFf = $parseFecha($c->fecha_fin);
                $cSt = optional($c->status)->name ?? 'Pendiente';
                $cColor = optional($c->status)->color_hex ?: '#a3cfb6';
            @endphp
            <a href="?reserva_id={{ $c->id }}{{ $q ? '&reserva_q='.$q : '' }}" class="list-item-app rsv-pick text-decoration-none d-flex justify-content-between align-items-center flex-wrap"
               data-res-id="{{ $c->id }}" style="gap:6px;" title="Abrir gestión de esta reserva">
                <span class="d-flex align-items-center" style="gap:6px; min-width:0;">
                    <span class="st-dot" style="background:{{ $cColor }};"></span>
                    <span class="strong rv-ellip" style="max-width:110px;">#{{ $c->codigo_reserva }}</span>
                    <span class="sub rv-ellip" style="max-width:140px;">{{ optional($c->user)->name ?? 'Sin asignar' }} · {{ optional($c->inmueble)->name ?? 'N/D' }}</span>
                </span>
                <span class="d-flex align-items-center" style="gap:6px;">
                    <span class="sub">{{ $cFi?->format('d/m') ?? '—' }} → {{ $cFf?->format('d/m/y') ?? '—' }}</span>
                    <span class="pill pill-{{ $stDot($cSt) }}">{{ $cSt }}</span>
                    <i class="bi bi-chevron-right" style="font-size:8px; color:#c3ccd6;"></i>
                </span>
            </a>
        @empty
            <div class="empty-friendly">
                <i class="bi bi-inbox"></i>
                <span class="sub">{{ $q !== '' ? 'Sin coincidencias para “'.$q.'”.' : 'Aún no hay reservas en este contexto.' }}</span>
            </div>
        @endforelse
    </div>
@endif

<!-- ══════════ SHOW DE LA RESERVA ══════════ -->
@if($reservaSel)
    @php
        $fi = $parseFecha($reservaSel->fecha_inicio);
        $ff = $parseFecha($reservaSel->fecha_fin);
        $noches = ($fi && $ff) ? max(0, (int) floor(($ff->getTimestamp() - $fi->getTimestamp()) / 86400)) : null;
        $enCurso = ($fi && $ff && $hoy->betweenIncluded($fi, $ff));
        $pct = ($enCurso && $noches > 0)
            ? (int) min(100, max(0, round((($hoy->getTimestamp() - $fi->getTimestamp()) / 86400 / $noches) * 100)))
            : 0;
        $dias = $fi ? (int) floor(($fi->getTimestamp() - $hoy->getTimestamp()) / 86400) : null;
        $cdTxt = '—'; $cdCls = 'cd-done';
        if ($enCurso)        { $cdTxt = 'EN CURSO';   $cdCls = 'cd-live'; }
        elseif ($dias === 0) { $cdTxt = 'HOY';        $cdCls = 'cd-today'; }
        elseif ($dias === 1) { $cdTxt = 'MAÑANA';     $cdCls = 'cd-next'; }
        elseif ($dias !== null && $dias > 1) { $cdTxt = '+'.$dias.'d'; $cdCls = 'cd-future'; }
        elseif ($dias !== null && $dias < 0) { $cdTxt = 'FINALIZADA'; $cdCls = 'cd-done'; }
        $stName = optional($reservaSel->status)->name ?? 'Pendiente';
        $stColor = optional($reservaSel->status)->color_hex ?: '#a3cfb6';
        $origen = optional($reservaSel->origen)->nombre ?? optional($reservaSel->origen)->name ?? 'Directo';

        /* Balance: SOLO pagos efectivos. Las ANULADAS (eliminación lógica) quedan excluidas. */
        $totalPagado = 0;
        $cntAnuladas = 0;
        foreach (($reservaSel->transacciones ?? collect()) as $tx) {
            $edo = mb_strtolower((string) ($tx->estado_pago ?? $tx->estado ?? ''));
            if (str_contains($edo, 'anulad')) { $cntAnuladas++; continue; }
            if (str_contains($edo, 'complet') || str_contains($edo, 'pagad') || str_contains($edo, 'aprob')) {
                $totalPagado += (float) ($tx->monto ?? $tx->monto_transaccion ?? $tx->valor ?? 0);
            }
        }
        $saldoPendiente = max(0, (float) ($reservaSel->monto_total ?? 0) - $totalPagado);
    @endphp

    @if($fueraDeContexto)
        <div class="st-warn mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Esta reserva pertenece a “{{ optional($reservaSel->inmueble)->name ?? 'otro inmueble' }}”; la estás viendo fuera del filtro actual.
        </div>
    @endif

    <!-- CABECERA -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2 pb-2" style="gap:8px; border-bottom:1px solid var(--grid);">
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <span class="val" style="font-size:13px;">#{{ $reservaSel->codigo_reserva }}</span>
            <button type="button" class="btn-mini" id="rvCopyCode" title="Copiar el código al portapapeles"><i class="bi bi-clipboard" style="font-size:8px;"></i></button>
            <span class="st-dot" style="background:{{ $stColor }};" title="{{ $stName }}"></span>
            <span class="pill pill-{{ $stDot($stName) }}">{{ $stName }}</span>
            <span class="cd {{ $cdCls }}" title="{{ $enCurso ? 'La estancia está en curso' : ($dias !== null && $dias > 0 ? 'Faltan '.$dias.' días para el check-in' : 'Estancia finalizada') }}">{{ $cdTxt }}</span>
            @if($enCurso)
                <span class="d-flex align-items-center" style="gap:4px;" title="Progreso de la estancia">
                    <span style="display:inline-block;width:40px;height:3px;background:var(--grid);border-radius:2px;overflow:hidden;">
                        <span style="display:block;height:100%;width:{{ $pct }}%;background:#b9d4c5;"></span>
                    </span>
                    <span class="sub">{{ $pct }}%</span>
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            @if($rutaPdf)
                <a href="{{ route($rutaPdf, $reservaSel->id) }}" target="_blank" class="btn-mini text-decoration-none" title="Descargar comprobante en PDF">
                    <i class="bi bi-file-earmark-pdf" style="font-size:8px;"></i> Comprobante
                </a>
            @endif
            <span class="sub" title="Fecha de creación"><i class="bi bi-clock me-1"></i>Creada {{ optional($reservaSel->created_at)->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <!-- FICHA -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-4"><div class="fact">
            <span class="lbl"><i class="bi bi-person me-1"></i> Titular & Contacto</span>
            <div class="val">{{ optional($reservaSel->user)->name ?? 'Sin asignar' }}</div>
            <div class="sub rv-ellip" style="max-width:100%;">{{ optional($reservaSel->user)->email ?? 'Sin email' }}</div>
            <div class="sub mt-1" title="Canal por el que llegó la reserva"><i class="bi bi-box-arrow-in-right me-1" style="color:var(--accent);"></i> Origen: {{ $origen }}</div>
        </div></div>
        <div class="col-12 col-md-4"><div class="fact">
            <span class="lbl"><i class="bi bi-house me-1"></i> Estancia</span>
            <div class="val rv-ellip" style="max-width:100%;">{{ optional($reservaSel->inmueble)->name ?? 'N/D' }}</div>
            <div class="sub mt-1 d-flex align-items-center flex-wrap" style="gap:4px;">
                <i class="bi bi-calendar-check"></i> {{ $fi?->format('d/m') ?? '—' }}
                <i class="bi bi-arrow-right" style="color:#c3ccd6;"></i>
                {{ $ff?->format('d/m/y') ?? '—' }}
                <strong class="strong" style="background:#fff; border:1px solid var(--grid); border-radius:6px; padding:0 5px; font-size:7px;">{{ $noches ?? '—' }} noches</strong>
            </div>
        </div></div>
        <div class="col-12 col-md-4"><div class="fact hl">
            <span class="lbl" style="color:var(--accent);"><i class="bi bi-cash-coin me-1"></i> Balance Financiero</span>
            <div class="val" style="color:var(--accent); font-size:12px;">$ {{ number_format((float) ($reservaSel->monto_total ?? 0), 2) }}</div>
            <div class="sub d-flex justify-content-between mt-1 pt-1" style="border-top:1px solid var(--accent-bd);">
                <span title="Suma de pagos completados (las anuladas no cuentan)">Abonado: <b class="strong">${{ number_format($totalPagado, 2) }}</b></span>
                <span style="color:{{ $saldoPendiente > 0 ? '#b05c5c' : '#2e7d4f' }}; font-weight:700;" title="{{ $saldoPendiente > 0 ? 'Falta por pagar' : 'Reserva paga en su totalidad' }}">Saldo: ${{ number_format($saldoPendiente, 2) }}</span>
            </div>
            @if($cntAnuladas > 0)
                <div class="tx-conservada mt-1" title="Anulación lógica: los registros y sus soportes se conservan para auditoría">
                    <i class="bi bi-archive"></i> {{ $cntAnuladas }} pago(s) anulado(s) · conservado(s) para auditoría
                </div>
            @endif
        </div></div>
        <div class="col-12"><div class="fact">
            <span class="lbl"><i class="bi bi-chat-left-text me-1"></i> Comentario de la reserva</span>
            <span class="sub" style="font-size:8.5px; color:var(--ink);">{{ $reservaSel->comentario_reserva ?: '—' }}</span>
        </div></div>
    </div>

    <div class="row g-3">

        <!-- ═══ COLUMNA IZQUIERDA ═══ -->
        <div class="col-12 col-lg-7">

            <!-- HUESPEDES -->
            <div class="sec-head">
                <span class="sec-title">
                    <i class="bi bi-people" style="font-size:9px; color:var(--accent);"></i>
                    <span class="lbl">Grupo de Huéspedes</span>
                    <span class="count-badge"><b>{{ ($reservaSel->huespedes ?? collect())->count() }}</b></span>
                </span>
                @if($rutaHuespedStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#rsvModalAddHuesped" title="Registrar un huésped adicional en la reserva">
                        <i class="bi bi-person-plus" style="font-size:8px;"></i> Añadir
                    </button>
                @endif
            </div>
            <span class="sec-hint d-block mb-1">Los huéspedes acompañan al titular · puedes editar sus datos en cualquier momento.</span>
            <div class="mb-3" id="rsvHuespedesList">
                @forelse(($reservaSel->huespedes ?? collect()) as $huesped)
                    <div class="list-item-app d-flex justify-content-between align-items-center flex-wrap rsv-hh-row" style="gap:6px;"
                         data-hh-id="{{ $huesped->id }}"
                         data-hh-nombre="{{ $huesped->nombre ?? $huesped->nombres ?? '' }}"
                         data-hh-apellidos="{{ $huesped->apellidos ?? '' }}"
                         data-hh-tipo="{{ $huesped->tipo_documento ?? 'CC' }}"
                         data-hh-numero="{{ $huesped->numero_documento ?? '' }}">
                        <div style="min-width:0;">
                            <span class="val d-flex align-items-center" style="gap:4px;">
                                {{ $huesped->nombre ?? $huesped->nombres ?? '' }} {{ $huesped->apellidos ?? '' }}
                                @if($huesped->es_titular ?? $huesped->es_principal ?? false)
                                    <i class="bi bi-star-fill" style="font-size:7px; color:#e0b453;" title="Huésped principal"></i>
                                @endif
                            </span>
                            <span class="sub">{{ $huesped->tipo_documento ?? 'Doc.' }} {{ $huesped->numero_documento ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap:5px;">
                            <button type="button" class="btn-mini rsv-huesped-edit" title="Corregir nombre o documento de este huésped">
                                <i class="bi bi-pencil" style="font-size:7.5px;"></i> Editar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-friendly">
                        <i class="bi bi-person-plus"></i>
                        <span class="sub">Todavía no hay huéspedes adicionales registrados.</span>
                        @if($rutaHuespedStore)
                            <span class="hint"><i class="bi bi-arrow-up-right me-1"></i>Usa el botón “Añadir” para registrar al primero.</span>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- ENDOSOS -->
            <div class="sec-head">
                <span class="sec-title">
                    <i class="bi bi-person-lines-fill" style="font-size:9px; color:var(--accent);"></i>
                    <span class="lbl">Endosos / Cambios de Titular</span>
                    <span class="count-badge"><b>{{ ($reservaSel->historialEndosos ?? collect())->count() }}</b></span>
                </span>
                @if($rutaEndosoStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#modalAddEndoso" title="Transferir la titularidad a otra persona (queda registrado con motivo)">
                        <i class="bi bi-arrow-left-right" style="font-size:8px;"></i> Transferir
                    </button>
                @endif
            </div>
            <span class="sec-hint d-block mb-1">Toda transferencia queda registrada con fecha, motivo y autorizante · es trazable, no se puede borrar.</span>
            <div class="mb-3">
                @forelse(($reservaSel->historialEndosos ?? collect()) as $endoso)
                    <div class="list-item-app">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:4px;">
                            <span class="val" style="font-size:8.5px;">
                                <i class="bi bi-box-arrow-right" style="color:#c98a85;"></i> {{ optional($endoso->usuarioAnterior)->name ?? 'N/A' }}
                                <i class="bi bi-arrow-right mx-1" style="color:#c3ccd6;"></i>
                                <i class="bi bi-box-arrow-in-right" style="color:#5d8a70;"></i> {{ optional($endoso->usuarioNuevo)->name ?? 'N/A' }}
                            </span>
                            <span class="sub">{{ optional($endoso->created_at)->format('d/m/Y') }}</span>
                        </div>
                        @if($endoso->motivo_endoso)
                            <div class="sub mt-1 d-flex" style="background:var(--head); border:1px solid var(--grid); border-radius:4px; padding:3px 6px;">
                                <i class="bi bi-quote me-1"></i> {{ $endoso->motivo_endoso }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-friendly">
                        <i class="bi bi-arrows"></i>
                        <span class="sub">Sin cambios de titularidad registrados.</span>
                        @if($rutaEndosoStore)
                            <span class="hint"><i class="bi bi-arrow-up-right me-1"></i>Si la reserva debe pasar a otra persona, usa “Transferir”.</span>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- PAGOS -->
            <div class="sec-head">
                <span class="sec-title">
                    <i class="bi bi-wallet2" style="font-size:9px; color:var(--accent);"></i>
                    <span class="lbl">Transacciones & Pagos</span>
                    <span class="count-badge"><b>{{ ($reservaSel->transacciones ?? collect())->count() }}</b></span>
                </span>
                @if($rutaPagoStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#modalAddPago" title="Registrar un abono o pago de esta reserva">
                        <i class="bi bi-plus-circle" style="font-size:8px;"></i> Nuevo Pago
                    </button>
                @endif
            </div>
            <span class="sec-hint d-block mb-1">Cada pago se suma al balance · las anulaciones son lógicas: el registro y su soporte se conservan para auditoría.</span>
            <div>
                @forelse(($reservaSel->transacciones ?? collect()) as $tx)
                    @php
                        $txEdo = $tx->estado_pago ?? $tx->estado ?? 'Pendiente';
                        $txEdoL = mb_strtolower((string) $txEdo);
                        $txAnulada = str_contains($txEdoL, 'anulad');
                        $txPill = $txAnulada ? 'secondary'
                            : ((str_contains($txEdoL, 'complet') || str_contains($txEdoL, 'aprob')) ? 'success'
                            : (str_contains($txEdoL, 'pendien') ? 'warning' : 'danger'));
                        $txSoporte = $tx->url_soporte_pago ?? $tx->soporte_pago ?? null;
                    @endphp
                    <div class="list-item-app d-flex justify-content-between align-items-start flex-wrap {{ $txAnulada ? 'tx-anulada' : '' }}" style="gap:6px;">
                        <div style="min-width:0;">
                            <div class="d-flex align-items-center flex-wrap" style="gap:5px;">
                                <span class="val tx-monto" style="font-size:10px;">$ {{ number_format((float) ($tx->monto ?? $tx->monto_transaccion ?? 0), 2) }}
                                    <span class="sub">{{ $tx->moneda ?? 'COP' }}</span>
                                </span>
                                <span class="pill pill-{{ $txPill }}" style="font-size:7px;">{{ $txEdo }}</span>
                                @if($txAnulada)
                                    <span class="tx-conservada" title="Anulación lógica: el registro y su soporte se conservan para auditoría">
                                        <i class="bi bi-archive"></i> conservada
                                    </span>
                                @endif
                            </div>
                            <div class="sub mt-1">
                                <i class="bi bi-credit-card me-1"></i> {{ optional($tx->pasarela)->nombre ?? optional($tx->pasarela)->name ?? 'Directo' }} · {{ $tx->metodo_pago ?? 'General' }}
                                @if($tx->referencia_externa) · Ref: {{ $tx->referencia_externa }}@endif
                            </div>
                            @if($txSoporte)
                                <a href="{{ $txSoporte }}" target="_blank" class="btn-mini mt-1 text-decoration-none" title="{{ $txAnulada ? 'El soporte se conserva aunque el pago esté anulado' : 'Abrir el comprobante adjunto' }}">
                                    <i class="bi bi-paperclip" style="font-size:7px;"></i> Ver Soporte
                                </a>
                            @endif
                        </div>
                        <span class="sub" title="Fecha del registro">{{ optional($tx->created_at)->format('d/m/y H:i') }}</span>
                    </div>
                @empty
                    <div class="empty-friendly">
                        <i class="bi bi-receipt"></i>
                        <span class="sub">Aún no se han registrado pagos.</span>
                        @if($rutaPagoStore)
                            <span class="hint"><i class="bi bi-arrow-up-right me-1"></i>Usa “Nuevo Pago” para registrar el primer abono.</span>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ═══ COLUMNA DERECHA ═══ -->
        <div class="col-12 col-lg-5">

            <!-- FLUJO DE ESTADOS -->
            <div class="fact mb-3" style="height:auto;">
                <span class="lbl" style="color:var(--ink-2);"><i class="bi bi-diagram-3 me-1"></i> Flujo de Reserva</span>
                <span class="sec-hint d-block mb-1">Haz clic en un paso del flujo para preseleccionarlo abajo.</span>
                <div class="ruta mb-2">
                    @foreach($steps as $i => $s)
                        @php
                            $isTerm = $esTerminal($s->name);
                            $cls = 'rt-step' . ($isTerm ? ' terminal' : '');
                            if ($curKey !== false) {
                                if ($i < $curKey && !$isTerm) $cls .= ' done';
                                if ($i === $curKey)           $cls .= ' active';
                            }
                            if ($rutaEstado) $cls .= ' clickable';
                        @endphp
                        <div class="{{ $cls }}" data-stid="{{ $s->id }}" title="{{ $s->name }}{{ $isTerm ? ' (estado terminal)' : '' }}">
                            <span class="rt-line"></span>
                            <span class="rt-dot">
                                @if($curKey !== false && $i < $curKey && !$isTerm)<i class="bi bi-check-lg"></i>
                                @elseif($curKey !== false && $i === $curKey)<i class="bi bi-geo-alt-fill"></i>
                                @else{{ $i + 1 }}@endif
                            </span>
                            <span class="rt-name">{{ $s->name }}</span>
                        </div>
                    @endforeach
                </div>

                @if($rutaEstado)
                    <form method="POST" action="{{ route($rutaEstado, $reservaSel->id) }}" id="rsvEstadoForm" class="rsv-ajax-form bg-white p-2 rounded" style="border:1px solid var(--grid-2);">
                        @csrf @method('PATCH')
                        <label class="lbl" style="color:var(--accent);">Actualizar estado a</label>
                        <select name="id_rsv_statuses" id="rsvEstadoSelect" class="form-select form-select-sm mb-1 fw-semibold" style="font-size:9px; color:var(--ink-2);" title="Elige el nuevo estado de la reserva">
                            @foreach($steps as $s)
                                <option value="{{ $s->id }}" {{ ($siguiente?->id ?? $reservaSel->id_rsv_statuses) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}{{ ($siguiente && $s->id == $siguiente->id) ? ' · siguiente paso' : '' }}{{ $esTerminal($s->name) ? ' · terminal' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="comentario" class="form-control form-control-sm mb-2" style="font-size:9px;" placeholder="Nota para auditoría (opcional)…" title="Este comentario quedará en el historial">
                        <button type="submit" class="btn-green w-100 justify-content-center" style="padding:4px 10px;" title="Aplicar el cambio de estado">
                            <i class="bi bi-arrow-repeat"></i> Cambiar Estado
                        </button>
                    </form>
                @else
                    <div class="st-warn"><i class="bi bi-plug me-1"></i> Ruta <code>PATCH rsv.reservas.cambiar_estado</code> no detectada.</div>
                @endif

                <div class="mt-2">
                    <span class="lbl"><i class="bi bi-clock-history me-1"></i> Últimos Movimientos</span>
                    @if($historial->isNotEmpty())
                        <div class="ps-2 border-start" style="border-color:var(--accent-bd) !important; border-width:2px !important;">
                            @foreach($historial as $h)
                                @php $hStId = $h->id_rsv_statuses_nuevo ?? null; $hName = optional($steps->firstWhere('id', $hStId))->name ?? ('#'.$hStId); @endphp
                                <div class="mb-2 ms-2 position-relative">
                                    <span class="position-absolute rounded-circle" style="width:5px; height:5px; background:var(--accent); left:-13px; top:5px;"></span>
                                    <div class="val" style="font-size:8.5px;">{{ $hName }}</div>
                                    <div class="sub d-flex justify-content-between" style="gap:6px;">
                                        <span class="rv-ellip" style="max-width:110px;">{{ optional($h->user)->name ?? 'Sistema' }}</span>
                                        <span class="flex-shrink-0">{{ optional($h->created_at)->format('d/m H:i') }}</span>
                                    </div>
                                    @if($h->comentario)
                                        <div class="sub" style="color:var(--ink);">{{ \Illuminate\Support\Str::limit($h->comentario, 60) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-friendly mt-1" style="padding:8px;">
                            <span class="sub">Sin movimientos todavía.</span>
                            <span class="hint">El primer cambio de estado aparecerá aquí.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- REVIEWS -->
            <div class="sec-head">
                <span class="sec-title">
                    <i class="bi bi-star-fill" style="font-size:9px; color:#e0b453;"></i>
                    <span class="lbl">Calificaciones</span>
                    <span class="count-badge"><b>{{ ($reservaSel->reviews ?? collect())->count() }}</b></span>
                </span>
                @if($rutaReviewStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#modalAddReview" title="Calificar el comportamiento del cliente">
                        <i class="bi bi-star-half" style="font-size:8px;"></i> Calificar
                    </button>
                @endif
            </div>
            <div class="mb-3">
                @forelse(($reservaSel->reviews ?? collect()) as $rev)
                    <div class="list-item-app">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="star-rating" title="Puntuación: {{ $rev->puntuacion ?? $rev->calificacion ?? 0 }}/5">
                                @php $puntos = (int) ($rev->puntuacion ?? $rev->calificacion ?? 0); @endphp
                                @for($s=1; $s<=5; $s++)<i class="bi bi-star-fill {{ $s <= $puntos ? 'filled' : '' }}"></i>@endfor
                            </span>
                            <span class="sub">{{ optional($rev->created_at)->format('d/m/Y') }}</span>
                        </div>
                        <div class="sub mt-1">Por: {{ optional($rev->autor)->name ?? 'Sistema' }}</div>
                        @if($rev->comentario)
                            <div class="sub mt-1" style="background:var(--head); border:1px solid var(--grid); border-radius:4px; padding:3px 6px; color:var(--ink);">"{{ $rev->comentario }}"</div>
                        @endif
                    </div>
                @empty
                    <div class="empty-friendly">
                        <i class="bi bi-star"></i>
                        <span class="sub">Aún no hay calificaciones.</span>
                        @if($rutaReviewStore)
                            <span class="hint"><i class="bi bi-arrow-up-right me-1"></i>Usa “Calificar” para valorar al cliente.</span>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- CHAT -->
            <div class="fact p-0 d-flex flex-column overflow-hidden" style="height:380px;">
                <div class="bg-white p-2 d-flex align-items-center" style="border-bottom:1px solid var(--grid); gap:6px;">
                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:22px; height:22px; background:#5d8a70; color:#fff; font-size:10px;"><i class="bi bi-whatsapp"></i></span>
                    <div>
                        <div class="val" style="font-size:9px;">Chat de Reserva</div>
                        <div class="sub" style="color:#5d8a70;">En línea · se actualiza en vivo</div>
                    </div>
                </div>
                <div class="flex-grow-1 p-2 overflow-auto chat-bg d-flex flex-column" id="chatBoxScroll">
                    @forelse(($reservaSel->mensajes ?? collect()) as $msg)
                        @php $esMio = ($currentUserId && ($msg->id_user_remitente ?? null) == $currentUserId); @endphp
                        <div class="d-flex flex-column msg-bubble {{ $esMio ? 'msg-sent' : 'msg-received' }}">
                            @if(!$esMio)
                                <strong class="d-block mb-1" style="font-size:7.5px; color:var(--accent);">{{ optional($msg->remitente)->name ?? 'Cliente' }}</strong>
                            @endif
                            <span>{{ $msg->contenido ?? $msg->mensaje ?? '' }}</span>
                            @if($msg->url_archivo)
                                <a href="{{ $msg->url_archivo }}" target="_blank" class="mt-1 text-decoration-none" style="font-size:7.5px; color:var(--accent);" title="Abrir el archivo adjunto"><i class="bi bi-paperclip"></i> Ver adjunto</a>
                            @endif
                            <span class="msg-time">{{ optional($msg->created_at)->format('H:i') }} @if($esMio)<i class="bi bi-check2-all" style="color:var(--accent);"></i>@endif</span>
                        </div>
                    @empty
                        <div class="text-center mt-auto mb-auto bg-white mx-auto rounded-3 p-2" style="font-size:8px; color:var(--ink-4); max-width:80%;">
                            <i class="bi bi-lock-fill me-1"></i> Conversación privada.<br>Inicia el primer mensaje.
                        </div>
                    @endforelse
                </div>
                @if($rutaChatStore)
                    <div class="chat-input-grp" style="border-top:1px solid var(--grid);">
                        <form action="{{ route($rutaChatStore) }}" method="POST" enctype="multipart/form-data" class="rsv-ajax-form m-0 d-flex align-items-center" style="gap:5px;">
                            @csrf
                            <input type="hidden" name="id_rsv_reservas" value="{{ $reservaSel->id }}">
                            <input type="hidden" name="id_user_remitente" value="{{ $currentUserId }}">
                            <input type="hidden" name="id_user_destinatario" value="{{ $reservaSel->id_user }}">
                            <label class="btn p-0 m-0" style="cursor:pointer; color:var(--ink-4);" title="Adjuntar imagen o PDF">
                                <i class="bi bi-paperclip" style="font-size:11px;"></i>
                                <input type="file" name="url_archivo" class="d-none" accept="image/*,application/pdf">
                            </label>
                            <input type="text" name="mensaje" class="form-control chat-input flex-grow-1" placeholder="Escribe un mensaje…" required autocomplete="off">
                            <button type="submit" class="btn rounded-circle d-flex align-items-center justify-content-center p-0" style="width:26px; height:26px; background:#5d8a70; color:#fff; border:none;" title="Enviar mensaje">
                                <i class="bi bi-send-fill" style="font-size:10px; margin-left:-1px;"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ════════════════ MODALES RESTANTES ════════════════ -->

    @if($rutaHuespedStore)
    <div class="modal fade reservas-soft-modal" id="rsvModalAddHuesped" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Añadir Huésped</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
                </div>
                <form action="{{ route($rutaHuespedStore) }}" method="POST" class="rsv-ajax-form">
                    @csrf
                    <input type="hidden" name="id_rsv_reservas" value="{{ $reservaSel->id }}">
                    <div class="modal-body p-3">
                        <div class="mb-2"><label class="form-label">Nombre(s) *</label>
                            <input type="text" class="form-control form-control-sm" name="nombres" maxlength="150" required placeholder="Juan Carlos…" value="{{ old('nombres') }}"></div>
                        <div class="mb-2"><label class="form-label">Apellidos *</label>
                            <input type="text" class="form-control form-control-sm" name="apellidos" maxlength="150" required placeholder="Calvo Nieto…" value="{{ old('apellidos') }}"></div>
                        <div class="row g-2 mb-2">
                            <div class="col-5"><label class="form-label">Tipo Doc.</label>
                                <select class="form-select form-select-sm" name="tipo_documento">
                                    <option value="CC">CC</option><option value="Pasaporte">Pasaporte</option><option value="DNI">DNI</option><option value="Otro">Otro</option>
                                </select></div>
                            <div class="col-7"><label class="form-label">Número *</label>
                                <input type="text" class="form-control form-control-sm" name="numero_documento" required value="{{ old('numero_documento') }}"></div>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" value="1" name="es_titular" id="rsvHuespPrincipalNew">
                            <label class="form-check-label sub" for="rsvHuespPrincipalNew" style="font-size:8.5px; color:var(--ink-2);">
                                Es el huésped principal <i class="bi bi-star-fill" style="font-size:7px; color:#e0b453;"></i>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if(\Illuminate\Support\Facades\Route::has('rsv.reserva-huespedes.update'))
    <div class="modal fade reservas-soft-modal" id="rsvModalEditHuesped" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Editar Huésped</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
                </div>
                <form action="#" method="POST" class="rsv-ajax-form" data-rsv-edit="huesped" data-update-base="{{ route('rsv.reserva-huespedes.update', 'ID') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="rsv_huesped_id" id="rsvEditHuespedId" value="">
                    <div class="modal-body p-3">
                        <div class="mb-2"><label class="form-label">Nombre(s) *</label>
                            <input type="text" class="form-control form-control-sm" name="nombres" id="rsvEditHuespedNombre" maxlength="150" required></div>
                        <div class="mb-2"><label class="form-label">Apellidos</label>
                            <input type="text" class="form-control form-control-sm" name="apellidos" id="rsvEditHuespedApellidos" maxlength="150"></div>
                        <div class="row g-2 mb-2">
                            <div class="col-5"><label class="form-label">Tipo Doc.</label>
                                <select class="form-select form-select-sm" name="tipo_documento" id="rsvEditHuespedTipo">
                                    @foreach(['CC', 'Pasaporte', 'DNI', 'Otro'] as $td)
                                        <option value="{{ $td }}">{{ $td }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-7"><label class="form-label">Número *</label>
                                <input type="text" class="form-control form-control-sm" name="numero_documento" id="rsvEditHuespedNumero" required></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($rutaPagoStore)
    <div class="modal fade reservas-soft-modal" id="modalAddPago" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Registrar Pago</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
                </div>
                <form action="{{ route($rutaPagoStore) }}" method="POST" enctype="multipart/form-data" class="rsv-ajax-form">
                    @csrf
                    <input type="hidden" name="id_rsv_reservas" value="{{ $reservaSel->id }}">
                    <div class="modal-body p-3">
                        <div class="row g-2 mb-2">
                            <div class="col-7"><label class="form-label">Monto *</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required value="{{ old('monto') }}"></div>
                            <div class="col-5"><label class="form-label">Moneda</label>
                                <input type="text" class="form-control form-control-sm" name="moneda" maxlength="4" value="{{ old('moneda', 'COP') }}"></div>
                        </div>
                        <div class="mb-2"><label class="form-label">Estado del pago</label>
                            <select class="form-select form-select-sm" name="estado_pago">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Completado">Completado</option>
                                <option value="Fallido">Fallido</option>
                            </select></div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label">Método</label>
                                <input type="text" class="form-control form-control-sm" name="metodo_pago" placeholder="Transferencia…" value="{{ old('metodo_pago') }}"></div>
                            <div class="col-6"><label class="form-label">Pasarela</label>
                                <select class="form-select form-select-sm" name="id_rsv_pasarelas" title="Si eliges Directo, el sistema lo registra automáticamente">
                                    <option value="">— Directo (automático) —</option>
                                    @foreach($pasarelas as $ps)
                                        <option value="{{ $ps->id }}">{{ $ps->nombre ?? $ps->name }}</option>
                                    @endforeach
                                </select></div>
                        </div>
                        <div class="mb-2"><label class="form-label">Referencia externa</label>
                            <input type="text" class="form-control form-control-sm" name="referencia_externa" value="{{ old('referencia_externa') }}"></div>
                        <div class="mb-0"><label class="form-label">Soporte (imagen/PDF)</label>
                            <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($rutaEndosoStore)
    <div class="modal fade reservas-soft-modal" id="modalAddEndoso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Transferir Reserva (Endoso)</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
                </div>
                <form action="{{ route($rutaEndosoStore) }}" method="POST" class="rsv-ajax-form" id="rsvEndosoForm">
                    @csrf
                    <input type="hidden" name="id_rsv_reservas" value="{{ $reservaSel->id }}">
                    <input type="hidden" name="id_user_autorizado_por" value="{{ $currentUserId }}">
                    <div class="modal-body p-3">
                        <div class="st-warn mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Esta acción transferirá la titularidad y quedará registrada.</div>

                        <div class="mb-2 position-relative">
                            <label class="form-label">Cédula del nuevo titular (nid) *</label>
                            <input type="text" inputmode="numeric" class="form-control form-control-sm" id="endosoUserSearch"
                                   placeholder="Escribe la cédula…" autocomplete="off" title="El nombre y el ID se resuelven automáticamente">
                            <div id="endosoUserResults" class="user-results" style="display:none;"></div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Titular resuelto</label>
                            <div id="endosoUserStatus" class="endoso-status" data-state="empty">
                                <span class="st-mini"></span>
                                <span id="endosoUserStatusTxt">Escribe la cédula y se resuelve automáticamente…</span>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label"><i class="bi bi-lock-fill me-1"></i>ID del usuario (auto · bloqueado)</label>
                            <input type="number" class="form-control form-control-sm id-locked" name="id_user_nuevo"
                                   id="endosoUserId" required readonly tabindex="-1" title="Se llena automáticamente al encontrar la cédula">
                        </div>

                        <div class="mt-2 mb-0">
                            <label class="form-label">Motivo *</label>
                            <textarea class="form-control form-control-sm" name="motivo_endoso" rows="3" required placeholder="Traspaso, venta, corrección…">{{ old('motivo_endoso') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($rutaReviewStore)
    <div class="modal fade reservas-soft-modal" id="modalAddReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Calificar Cliente / Reserva</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
                </div>
                <form action="{{ route($rutaReviewStore) }}" method="POST" class="rsv-ajax-form">
                    @csrf
                    <input type="hidden" name="id_rsv_reservas" value="{{ $reservaSel->id }}">
                    <input type="hidden" name="id_user" value="{{ $reservaSel->id_user }}">
                    <div class="modal-body p-3">
                        <div class="text-center mb-2">
                            <label class="form-label">Puntuación</label>
                            <style>
                                .rsv-rating-css { direction: rtl; display: inline-flex; }
                                .rsv-rating-css input { display: none; }
                                .rsv-rating-css label { color: #e4e5e9; cursor: pointer; font-size: 20px; padding: 0 3px; }
                                .rsv-rating-css label:hover, .rsv-rating-css label:hover ~ label,
                                .rsv-rating-css input:checked ~ label { color: #e0b453; }
                            </style>
                            <div class="rsv-rating-css">
                                <input type="radio" name="calificacion" id="rvStar5" value="5"><label for="rvStar5" class="bi bi-star-fill"></label>
                                <input type="radio" name="calificacion" id="rvStar4" value="4"><label for="rvStar4" class="bi bi-star-fill"></label>
                                <input type="radio" name="calificacion" id="rvStar3" value="3"><label for="rvStar3" class="bi bi-star-fill"></label>
                                <input type="radio" name="calificacion" id="rvStar2" value="2"><label for="rvStar2" class="bi bi-star-fill"></label>
                                <input type="radio" name="calificacion" id="rvStar1" value="1" checked><label for="rvStar1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                        <div class="mb-0"><label class="form-label">Comentarios (opcional)</label>
                            <textarea class="form-control form-control-sm" name="comentario" rows="3" placeholder="¿Cómo fue el comportamiento del cliente?">{{ old('comentario') }}</textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endif

</div>

<!-- TOAST (fuera del root: sobrevive a los swaps) -->
<div id="rsvToast" class="rsv-toast"></div>

<script>
(function () {
    var root = document.getElementById('rsvShowRoot');
    if (!root) return;

    var FRAG  = root.getAttribute('data-frag')  || '';
    var USERS = root.getAttribute('data-users') || '';
    var INM   = root.getAttribute('data-inm')   || '';
    var POLL  = parseInt(root.getAttribute('data-poll') || '15000', 10);

    /* ── Toast ── */
    var toastEl = document.getElementById('rsvToast');
    var toastTimer = null;
    function rsvToast(msg, esError) {
        if (!toastEl) { return; }
        toastEl.className = 'rsv-toast ' + (esError ? 'err' : 'ok');
        toastEl.innerHTML = (esError ? '<i class="bi bi-exclamation-circle me-1"></i>' : '<i class="bi bi-check-circle me-1"></i>') + msg;
        toastEl.style.display = 'block';
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toastEl.style.display = 'none'; }, 4000);
    }

    function rsvEsc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* ── Motor de swap in-place ── */
    var rsvAbort = null;
    var rsvEnVuelo = false;   /* hay una operación (submit) en curso */

    function rsvRunScripts(container) {
        container.querySelectorAll('script').forEach(function (old) {
            var s = document.createElement('script');
            s.textContent = old.textContent;
            old.parentNode.replaceChild(s, old);
        });
    }

    function rsvCerrarModales() {
        try {
            document.querySelectorAll('.modal.show').forEach(function (m) {
                var inst = bootstrap.Modal.getInstance(m);
                if (inst) { inst.hide(); }
            });
        } catch (e) {}
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    function rsvParams(q, rid) {
        var p = new URLSearchParams(window.location.search);
        if (INM) { p.set('inmueble_id', INM); } else { p.delete('inmueble_id'); }
        if (q)   { p.set('reserva_q', q);   } else { p.delete('reserva_q'); }
        if (rid) { p.set('reserva_id', rid); } else { p.delete('reserva_id'); }
        return p.toString();
    }

    function rsvRecargar(overrides, opts) {
        overrides = overrides || {};
        opts = opts || {};

        var q   = ('reserva_q'  in overrides) ? (overrides.reserva_q  || '') : (root.getAttribute('data-q')  || '');
        var rid = ('reserva_id' in overrides) ? (overrides.reserva_id || '') : (root.getAttribute('data-res') || '');

        var qs = rsvParams(q, rid);
        var newUrl = window.location.pathname + (qs ? '?' + qs : '');

        // 🟢 FIX: Si no hay ruta de fragmento, navegamos de forma nativa a la URL completa.
        // Esto previene que un click se quede ignorado (window.location.reload() no llevaba los parámetros).
        if (!FRAG) {
            window.location.href = newUrl;
            return;
        }

        try { history.replaceState(null, '', newUrl); } catch (e) {}

        if (rsvAbort) { try { rsvAbort.abort(); } catch (e) {} }
        rsvAbort = new AbortController();

        return fetch(FRAG + (qs ? '?' + qs : ''), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: rsvAbort.signal
        })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var nr = doc.getElementById('rsvShowRoot');
            if (!nr) { rsvToast('Respuesta inesperada del servidor.', true); return; }

            if (!opts.silencioso) { rsvCerrarModales(); }

            var imported = document.importNode(nr, true);
            root.replaceWith(imported);
            rsvRunScripts(imported);

            rsvMarcaSync();
        })
        .catch(function (err) {
            if (err && err.name === 'AbortError') { return; }
            if (!opts.silencioso) { rsvToast('No se pudo actualizar la vista.', true); }
        });
    }

    /* ═══════════════════════════════════════════════════════════
       MOTOR DE TIEMPO REAL (auto-sync):
    ═══════════════════════════════════════════════════════════ */
    var liveBadge = document.getElementById('rsvLiveBadge');
    var liveTxt   = document.getElementById('rsvLiveTxt');
    var pollTimer = null;
    var pollDelay = POLL;
    var rsvErroresSeguidos = 0;

    function rsvSetBadge(texto, pausado) {
        if (!liveBadge || !liveTxt) return;
        liveTxt.textContent = texto;
        liveBadge.classList.toggle('paused', !!pausado);
    }

    function rsvMarcaSync() {
        rsvErroresSeguidos = 0;
        pollDelay = POLL;
        rsvSetBadge('en vivo · ' + new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), false);
    }

    function rsvUsuarioOcupado() {
        if (document.querySelector('.modal.show')) { return true; }
        if (rsvEnVuelo) { return true; }
        var activo = document.activeElement;
        if (activo && root.contains(activo) &&
            (activo.tagName === 'INPUT' || activo.tagName === 'TEXTAREA' || activo.tagName === 'SELECT')) {
            return true;
        }
        return false;
    }

    function rsvSincronizar() {
        if (!FRAG) { return; }
        if (document.hidden) { return; }
        if (rsvUsuarioOcupado()) { return; }
        rsvRecargar({}, { silencioso: true });
    }

    function rsvProgramarPoll() {
        clearInterval(pollTimer);
        pollTimer = setInterval(function () {
            if (rsvErroresSeguidos >= 3) {
                rsvSetBadge('reconectando…', true);
                return;
            }
            rsvSincronizar();
        }, pollDelay);
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) { rsvSincronizar(); }
    });

    if (FRAG) { rsvProgramarPoll(); }
    else { rsvSetBadge('sin auto-sync', true); }

    /* ── Clicks delegados ── */
    root.addEventListener('click', function (e) {
        var btn;

        /* Copiar código */
        if ((btn = e.target.closest('#rvCopyCode'))) {
            var code = (root.getAttribute('data-res') || '');
            if (navigator.clipboard && code) {
                navigator.clipboard.writeText(code).then(function () { rsvToast('Reserva copiada al portapapeles'); });
            }
            return;
        }

        /* Limpiar buscador */
        if ((btn = e.target.closest('#rsvClearLink'))) {
            e.preventDefault();
            rsvRecargar({ reserva_q: '', reserva_id: '' });
            return;
        }

        /* Seleccionar candidata */
        if ((btn = e.target.closest('.rsv-pick'))) {
            e.preventDefault();
            rsvRecargar({ reserva_id: btn.getAttribute('data-res-id') });
            return;
        }

        /* Editar huésped (modal genérico) */
        if ((btn = e.target.closest('.rsv-huesped-edit'))) {
            var fila = btn.closest('.rsv-hh-row');
            if (!fila) { return; }
            var base = root.getAttribute('data-huesped-update') || '';
            if (!base) { rsvToast('Ruta de edición no disponible.', true); return; }
            var mId = document.getElementById('rsvEditHuespedId');
            var mForm = document.getElementById('rsvModalEditHuesped');
            if (!mId || !mForm) { rsvToast('Modal de edición no disponible.', true); return; }
            mForm.setAttribute('data-update-base', base);
            mId.value = fila.getAttribute('data-hh-id') || '';
            document.getElementById('rsvEditHuespedNombre').value = fila.getAttribute('data-hh-nombre') || '';
            document.getElementById('rsvEditHuespedApellidos').value = fila.getAttribute('data-hh-apellidos') || '';
            var tipo = fila.getAttribute('data-hh-tipo') || 'CC';
            var selT = document.getElementById('rsvEditHuespedTipo');
            if (selT) {
                for (var i = 0; i < selT.options.length; i++) {
                    if (selT.options[i].value === tipo) { selT.selectedIndex = i; break; }
                }
            }
            document.getElementById('rsvEditHuespedNumero').value = fila.getAttribute('data-hh-numero') || '';
            try { bootstrap.Modal.getOrCreateInstance(mForm).show(); } catch (e2) {}
            return;
        }

        /* Stepper: clic preselecciona estado */
        if ((btn = e.target.closest('.rt-step.clickable'))) {
            var sel = root.querySelector('#rsvEstadoSelect');
            if (sel) {
                sel.value = btn.getAttribute('data-stid');
                var f = root.querySelector('#rsvEstadoForm');
                if (f) { f.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            }
        }
    });

    /* ── Chat: scroll al final ── */
    function rsvScrollChat() {
        var cb = root.querySelector('#chatBoxScroll');
        if (cb) { cb.scrollTop = cb.scrollHeight; }
    }
    rsvScrollChat();

    /* ── Buscador 100% AJAX ── */
    var searchForm = root.querySelector('#rsvShowSearch');
    var inp = searchForm ? searchForm.querySelector('input[name="reserva_q"]') : null;
    if (searchForm && inp) {
        var timer = null;
        var lanzar = function () { rsvRecargar({ reserva_q: inp.value.trim(), reserva_id: '' }); };
        inp.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(lanzar, 600);
        });
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(timer);
            lanzar();
        });
        var qActual = @js(request('reserva_q'));
        if (qActual) {
            inp.focus();
            inp.setSelectionRange(inp.value.length, inp.value.length);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       INTERCEPTADOR DELEGADO (document, capture)
    ═══════════════════════════════════════════════════════════ */
    document.addEventListener('submit', function (e) {
        var form = e.target.closest ? e.target.closest('form.rsv-ajax-form') : null;
        if (!form || !form.isConnected) { return; }

        e.preventDefault();
        e.stopImmediatePropagation();

        var action = form.getAttribute('action');

        if (form.getAttribute('data-rsv-edit') === 'huesped') {
            var hid = document.getElementById('rsvEditHuespedId');
            var base = form.getAttribute('data-update-base') || '';
            if (!hid || !hid.value || !base) { rsvToast('Selecciona un huésped válido.', true); return; }
            action = base.replace('ID', hid.value);
        }

        if (!action || action === '#') { rsvToast('Ruta no disponible para esta acción.', true); return; }

        if (form.id === 'rsvEndosoForm') {
            var uHidden = document.getElementById('endosoUserId');
            var uSearch = document.getElementById('endosoUserSearch');
            var uFound  = document.querySelector('#endosoUserStatus[data-state="found"]');
            if (!uFound || !uHidden || !uHidden.value) {
                rsvToast('Primero resuelve el titular: escribe la cédula válida.', true);
                if (uSearch) { uSearch.focus(); }
                return;
            }
        }

        var dc = form.getAttribute('data-confirm');
        if (dc && !confirm(dc)) { return; }

        var btn = form.querySelector('button[type="submit"]');
        var original = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i>'; }
        rsvEnVuelo = true;

        fetch(action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (res) {
            var ct = res.headers.get('content-type') || '';
            if (res.ok && ct.indexOf('json') === -1) {
                return { status: 200, data: { success: true, message: 'Operación realizada.' } };
            }
            return res.json().then(function (data) { return { status: res.status, data: data }; });
        })
        .then(function (response) {
            if (response.status === 200 || response.status === 201 || (response.data && response.data.success)) {
                rsvToast(response.data.message || 'Operación realizada con éxito.', false);
                rsvRecargar();
            } else {
                if (btn) { btn.disabled = false; btn.innerHTML = original; }
                if (response.status === 422 && response.data.errors) {
                    var msgs = Object.values(response.data.errors).map(function (e) { return e.join(' · '); }).join('\n');
                    rsvToast('Corrige: ' + msgs, true);
                } else {
                    rsvToast(response.data.message || 'Error en la solicitud.', true);
                    if (response.data.error) { console.error(response.data.error); }
                }
            }
        })
        .catch(function (err) {
            if (btn) { btn.disabled = false; btn.innerHTML = original; }
            rsvToast('Fallo de conexión con el servidor.', true);
            console.error(err);
        })
        .finally(function () {
            rsvEnVuelo = false;
        });
    }, true);

    /* ═══════════════════════════════════════════════════════════
       ENDOSO: cédula (nid) -> nombre + ID automáticos
    ═══════════════════════════════════════════════════════════ */
    var uSearch  = root.querySelector('#endosoUserSearch');
    var uResults = root.querySelector('#endosoUserResults');
    var uHidden  = root.querySelector('#endosoUserId');
    var uStatus  = root.querySelector('#endosoUserStatus');
    var uStatusTxt = root.querySelector('#endosoUserStatusTxt');

    if (uSearch && uResults && uHidden && uStatus && uStatusTxt && USERS) {
        var uTimer = null;
        var uAbort = null;
        var uSelected = null;

        function uSetState(state, html) {
            uStatus.setAttribute('data-state', state);
            uStatusTxt.innerHTML = html;
        }

        function uApply(u, viaClic) {
            uSelected = u;
            uHidden.value = u.id;
            uSetState('found',
                '<i class="bi bi-person-check"></i> <b>' + rsvEsc(u.name) + '</b>'
                + ' <span class="sub">· ID ' + rsvEsc(u.id)
                + (u.nid ? ' · C.C. ' + rsvEsc(u.nid) : '')
                + (u.email ? ' · ' + rsvEsc(u.email) : '') + '</span>');
            uResults.style.display = 'none';
            uResults.innerHTML = '';
            if (viaClic) { rsvToast('Titular: ' + u.name + ' (ID ' + u.id + ')'); }
        }

        function uReset(msg) {
            uSelected = null;
            uHidden.value = '';
            uSetState(msg === undefined ? 'empty' : 'none',
                msg === undefined ? 'Escribe la cédula y se resuelve automáticamente…' : msg);
        }

        function uMostrarDropdown(list) {
            uResults.innerHTML = '';
            list.forEach(function (u) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'u-item';
                b.innerHTML = '<span class="strong">' + rsvEsc(u.name) + '</span>'
                    + ' <span class="sub">· C.C. ' + rsvEsc(u.nid || '—')
                    + (u.email ? ' · ' + rsvEsc(u.email) : '') + '</span>';
                b.addEventListener('click', function () { uApply(u, true); });
                uResults.appendChild(b);
            });
            uResults.style.display = 'block';
        }

        function uProcesar(term) {
            uSetState('loading', '<i class="bi bi-arrow-repeat"></i> Buscando…');

            if (uAbort) { try { uAbort.abort(); } catch (e) {} }
            uAbort = new AbortController();

            fetch(USERS + '?q=' + encodeURIComponent(term), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                signal: uAbort.signal
            })
            .then(function (r) {
                if (!r.ok) { throw new Error('HTTP ' + r.status); }
                return r.json();
            })
            .then(function (j) {
                if (uSearch.value.trim() !== term) { return; }
                var list = (j && j.data) ? j.data : [];

                if (j && j.success === false) {
                    uReset('El buscador respondió con error. Revisa los logs del servidor.');
                    uResults.style.display = 'none';
                    return;
                }

                if (!list.length) {
                    uReset('Sin usuario con “' + rsvEsc(term) + '”. Verifica la cédula.');
                    uResults.style.display = 'none';
                    return;
                }

                var exacto = list.find(function (u) {
                    return String(u.nid || '').toLowerCase() === term.toLowerCase();
                });
                if (exacto) { uApply(exacto, false); return; }

                if (list.length === 1) { uApply(list[0], false); return; }

                uSelected = null;
                uHidden.value = '';
                uSetState('loading', list.length + ' coincidencias · elige una (o termina de escribir la cédula)…');
                uMostrarDropdown(list);
            })
            .catch(function (err) {
                if (err && err.name === 'AbortError') { return; }
                uReset('Buscador no disponible (' + (err && err.message ? rsvEsc(err.message) : 'error') + '). Intenta de nuevo.');
            });
        }

        uSearch.addEventListener('input', function () {
            clearTimeout(uTimer);
            var term = this.value.trim();

            if (term.length < 2) {
                uReset();
                uResults.style.display = 'none';
                uResults.innerHTML = '';
                return;
            }
            uTimer = setTimeout(function () { uProcesar(term); }, 400);
        });

        uSearch.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') { return; }
            e.preventDefault();
            var first = uResults.querySelector('.u-item');
            if (uResults.style.display !== 'none' && first) { first.click(); }
        });

        root.addEventListener('click', function (e) {
            if (!uSearch.contains(e.target) && !uResults.contains(e.target)) {
                uResults.style.display = 'none';
            }
        });
    }
})();
</script>