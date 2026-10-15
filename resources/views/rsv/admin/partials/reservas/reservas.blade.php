{{-- ════════════════════════════════════════════════════════════════
     reservas.blade.php · v19.1 · SHOW de UNA sola reserva
     Buscador → Detalle → Ruta de estados (stepper + cambio + historial)
     Fixes: ruta underscore · @method('PATCH') · comentarios PHP ·
            fallback nid · preserva inmueble_id · botón PDF
   ════════════════════════════════════════════════════════════════ --}}

<div class="rsvshow p-3 bg-white rounded-3 border" id="rsvShowRoot" style="border-color:#f1f5f9 !important;">

<style>
    .rsvshow { --grid:#f1f5f9; --grid2:#e9eef4; --head:#fafbfd; --ink2:#475569; --ink3:#94a3b8; --ink4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .rsvshow .lbl { font-size:8.5px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--ink4); }
    .rsvshow .val { font-size:12px; color:var(--ink2); font-weight:600; }
    .rsvshow .sub { font-size:9px; color:var(--ink4); }
    .rsvshow .search-in { font-size:11px; color:var(--ink2); background:#fff; border:1px solid var(--grid2); border-radius:4px; padding:4px 10px; box-shadow:none !important; outline:0; }
    .rsvshow .search-in:focus { border-color:#c8d6cd; }
    .rsvshow .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:10px; font-weight:600; padding:3px 12px; border-radius:4px; }
    .rsvshow .btn-green:hover { background:#e3f1e9; color:#34684e; }
    .rsvshow .btn-mini { border:1px solid var(--grid2); background:#fff; color:var(--ink3); font-size:9.5px; padding:2px 9px; border-radius:4px; }
    .rsvshow .btn-mini:hover { color:var(--ink2); background:#fafcfe; }
    .rsvshow .cd { display:inline-block; padding:0 6px; border-radius:8px; font-size:8.5px; font-weight:700; border:1px solid; line-height:14px; }
    .rsvshow .cd-today  { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }
    .rsvshow .cd-next   { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
    .rsvshow .cd-future { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
    .rsvshow .cd-live   { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
    .rsvshow .cd-done   { color:#c8d0d9; background:#fbfcfd; border-color:#f3f6f9; }
    .rsvshow .pill { display:inline-flex; align-items:center; padding:2px 10px; border-radius:10px; font-size:9.5px; font-weight:700; border:1px solid; }
    .rsvshow .pill-success   { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; }
    .rsvshow .pill-warning   { color:#a3822f; background:#fdf8ec; border-color:#f3e7c6; }
    .rsvshow .pill-danger    { color:#b05c5c; background:#fdf1f1; border-color:#f3d8d8; }
    .rsvshow .pill-primary   { color:#4a6fa5; background:#f2f6fc; border-color:#dbe7f5; }
    .rsvshow .pill-secondary { color:#8a97a5; background:#fafbfd; border-color:#eef2f6; }
    .rsvshow .fact { padding:8px 10px; background:var(--head); border:1px solid var(--grid); border-radius:6px; height:100%; }
    /* Stepper de ruta de estados */
    .rsvshow .ruta { display:flex; align-items:flex-start; overflow-x:auto; padding:6px 2px 2px; }
    .rsvshow .rt-step { display:flex; flex-direction:column; align-items:center; min-width:86px; position:relative; }
    .rsvshow .rt-step.clickable { cursor:pointer; }
    .rsvshow .rt-step.clickable:hover .rt-name { color:var(--accent); }
    .rsvshow .rt-dot { width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; border:2px solid var(--grid2); background:#fff; color:var(--ink4); z-index:1; }
    .rsvshow .rt-step.done .rt-dot    { background:var(--accent-bg); border-color:var(--accent); color:var(--accent); }
    .rsvshow .rt-step.active .rt-dot  { background:var(--accent); border-color:var(--accent); color:#fff; box-shadow:0 0 0 3px var(--accent-bg); }
    .rsvshow .rt-step.terminal .rt-dot { background:#fdf1f1; border-color:#e0b4b4; color:#b05c5c; }
    .rsvshow .rt-step.active.terminal .rt-dot { background:#b05c5c; border-color:#b05c5c; color:#fff; box-shadow:0 0 0 3px #fdf1f1; }
    .rsvshow .rt-line { position:absolute; top:11px; left:-50%; width:100%; height:2px; background:var(--grid2); z-index:0; }
    .rsvshow .rt-step.done .rt-line { background:var(--accent); }
    .rsvshow .rt-step:first-child .rt-line { display:none; }
    .rsvshow .rt-name { margin-top:4px; font-size:8.5px; font-weight:600; color:var(--ink3); text-align:center; line-height:1.15; padding:0 4px; }
    .rsvshow .rt-step.active .rt-name { color:var(--accent); }
    .rsvshow .rt-step.terminal .rt-name { color:#b05c5c; }
    /* Historial */
    .rsvshow .hist-item { display:flex; gap:8px; padding:6px 0; border-bottom:1px dashed var(--grid); font-size:10px; align-items:center; }
    .rsvshow .hist-item:last-child { border-bottom:0; }
    .rsvshow .hist-time { color:var(--ink4); white-space:nowrap; min-width:96px; }
</style>

@php
    /* Contexto: global o por-inmueble (auto) */
    $xlsInm   = $inmueble ?? null;
    $xlsInmId = is_object($xlsInm) ? ($xlsInm->id ?? null) : null;
    if (!$xlsInmId && !empty($inmuebleId) && is_numeric($inmuebleId)) { $xlsInmId = $inmuebleId; }
    if (!$xlsInmId && is_numeric(request('inmueble_id')))             { $xlsInmId = request('inmueble_id'); }
    $modoGlobal = empty($xlsInmId);

    $hoy = now()->startOfDay();

    /* Helpers */
    $stDot = function ($name) {
        $s = mb_strtolower((string) $name);
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) return 'success';
        if (str_contains($s, 'pendien'))  return 'warning';
        if (str_contains($s, 'cancela') || str_contains($s, 'rechaz')) return 'danger';
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

    /* Estados (orden de la ruta: columna 'orden' si existe, si no 'id') */
    $statuses = collect();
    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            try   { $statuses = \App\Models\Rsv\Status::query()->orderBy('orden')->orderBy('id')->get(); }
            catch (\Throwable $e) { $statuses = \App\Models\Rsv\Status::query()->orderBy('id')->get(); }
        }
    } catch (\Throwable $e) { $statuses = collect(); }

    /* Busqueda + seleccion (una sola reserva) */
    $q     = trim((string) request('reserva_q'));
    $selId = request('reserva_id');
    $candidatas = collect();
    $reservaSel = null;
    $fueraDeContexto = false;

    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {

            // 1) La reserva seleccionada (la UNICA que se carga completa)
            if (is_numeric($selId)) {
                $rq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status']);
                if (!$modoGlobal && $xlsInmId) { $rq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                $reservaSel = $rq->find($selId);
                if (!$reservaSel) {
                    $otra = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status'])->find($selId);
                    if ($otra) { $reservaSel = $otra; $fueraDeContexto = true; }
                }
            }

            // 2) Candidatas del buscador (max. 8, solo para ELEGIR una)
            if ($q !== '') {
                $buildCandidatas = function ($conNid) use ($q, $modoGlobal, $xlsInmId) {
                    $bq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status'])
                        ->where(function ($w) use ($q, $conNid) {
                            $like = '%' . $q . '%';
                            $w->where('codigo_reserva', 'like', $like)
                              ->orWhereHas('user', function ($u) use ($like, $conNid) {
                                  $u->where('name', 'like', $like)
                                    ->orWhere('email', 'like', $like);
                                  if ($conNid) { $u->orWhere('nid', 'like', $like); }
                              });
                        });
                    if (!$modoGlobal && $xlsInmId) { $bq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                    return $bq->orderBy('created_at', 'desc')->limit(8)->get();
                };
                try {
                    $candidatas = $buildCandidatas(true);
                } catch (\Throwable $eNid) {
                    // Fallback: si la columna 'nid' no existe en users, reintenta sin ella
                    try { $candidatas = $buildCandidatas(false); }
                    catch (\Throwable $e2) {
                        \Illuminate\Support\Facades\Log::warning('reservas.show search: ' . $e2->getMessage());
                        $candidatas = collect();
                    }
                }
            } elseif (!$reservaSel) {
                // Sin busqueda ni seleccion: accesos rapidos a las ultimas 8
                $lq = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status']);
                if (!$modoGlobal && $xlsInmId) { $lq->where('id_rsv_catalogo_inmueble', $xlsInmId); }
                $candidatas = $lq->orderBy('created_at', 'desc')->limit(8)->get();
            }
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('reservas.show: ' . $e->getMessage());
    }

    /* Ruta: posicion actual y siguiente estado sugerido */
    $steps  = $statuses->values();
    $curKey = ($reservaSel && $steps->isNotEmpty())
        ? $steps->search(fn ($s) => (string) $s->id === (string) $reservaSel->id_rsv_statuses)
        : false;
    $siguiente = null;
    if ($curKey !== false) {
        for ($i = $curKey + 1; $i < $steps->count(); $i++) {
            if (!$esTerminal($steps[$i]->name)) { $siguiente = $steps[$i]; break; }
        }
    }

    /* Historial de estados (autodetecta modelo/FK) */
    $historial = collect();
    if ($reservaSel) {
        foreach ([
            \App\Models\Rsv\HistorialEstado::class,
            \App\Models\Rsv\ReservaEstadoHistorial::class,
            \App\Models\Rsv\ReservaHistorial::class,
        ] as $hc) {
            if (!class_exists($hc)) { continue; }
            $ok = false;
            foreach (['id_rsv_reserva', 'reserva_id', 'id_reserva'] as $fk) {
                try {
                    $historial = $hc::query()->where($fk, $reservaSel->id)->orderBy('created_at')->get();
                    $ok = true; break;
                } catch (\Throwable $e) { continue; }
            }
            if ($ok) break;
        }
    }
    $histMap = function ($h) use ($steps) {
        $stId   = $h->id_rsv_statuses ?? $h->id_rsv_status ?? $h->status_id ?? $h->estado_id ?? null;
        $stName = optional($steps->firstWhere('id', $stId))->name
                  ?? ($h->estado_nombre ?? ($stId ? ('#' . $stId) : '—'));
        return [
            'fecha'   => optional($h->created_at)->format('d/m/Y H:i'),
            'estado'  => (string) $stName,
            'nota'    => $h->nota ?? $h->comentario ?? $h->descripcion ?? null,
            'usuario' => (is_object($h->user ?? null) ? ($h->user->name ?? null) : null) ?? ($h->usuario ?? 'Sistema'),
        ];
    };

    /* Ruta de cambio de estado: la que YA existe en produccion (PATCH, underscore) */
    $rutaEstado = null;
    foreach (['rsv.reservas.cambiar_estado', 'rsv.reservas.cambiar-estado'] as $rt) {
        if (\Illuminate\Support\Facades\Route::has($rt)) { $rutaEstado = $rt; break; }
    }

    /* Ruta de comprobante PDF (existe en produccion: GET rsv.reservas.pdf) */
    $rutaPdf = \Illuminate\Support\Facades\Route::has('rsv.reservas.pdf') ? 'rsv.reservas.pdf' : null;
@endphp

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px;">
    <h6 class="fw-bold mb-0" style="font-size:11px; color:#334155;">
        <i class="bi bi-calendar2-check me-1" style="font-size:10px; color:#5d8a70;"></i>
        {{ $modoGlobal ? 'Gestión de Reserva' : 'Reserva del Inmueble' }}
        <span class="badge rounded-pill ms-1" style="font-size:7px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">v19.1</span>
    </h6>
    <button type="button" class="btn-green d-inline-flex align-items-center" style="gap:4px;"
            data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
        <i class="bi bi-plus-lg" style="font-size:9px;"></i> Nueva Reserva
    </button>
</div>

<!-- BUSCADOR -->
<form method="GET" action="{{ url()->current() }}" id="rsvShowSearch"
      class="d-flex flex-wrap align-items-center mb-3" style="gap:8px;">
    {{-- Preserva el contexto del selector maestro (incluye inmueble_id=todos) --}}
    @if(request('inmueble_id'))
        <input type="hidden" name="inmueble_id" value="{{ request('inmueble_id') }}">
    @endif
    <div class="d-flex align-items-center flex-grow-1" style="gap:6px; min-width:240px;">
        <i class="bi bi-search" style="font-size:11px; color:#c3ccd6;"></i>
        <input type="text" name="reserva_q" value="{{ $q }}" class="search-in flex-grow-1" style="min-width:200px;"
               placeholder="Código, titular, email o cédula… (carga una sola reserva)" autocomplete="off">
    </div>
    <button type="submit" class="btn-green d-inline-flex align-items-center" style="gap:4px;">
        <i class="bi bi-arrow-return-left" style="font-size:10px;"></i> Cargar
    </button>
    @if($q !== '' || $reservaSel)
        <a href="{{ request()->fullUrlWithoutQuery(['reserva_id', 'reserva_q']) }}"
           class="btn-mini d-inline-flex align-items-center text-decoration-none" style="gap:4px;">
            <i class="bi bi-x-lg" style="font-size:9px;"></i> Limpiar
        </a>
    @endif
</form>

<!-- CANDIDATAS (elige UNA) -->
@if(!$reservaSel)
    <div class="mb-2">
        <div class="lbl mb-1">
            <i class="bi bi-{{ $q !== '' ? 'funnel' : 'clock' }} me-1"></i>
            {{ $q !== '' ? 'Resultados ('.$candidatas->count().') · clic para gestionar' : 'Últimas reservas · selecciona una para gestionar su ruta de estados' }}
        </div>
        @forelse($candidatas as $c)
            @php
                $cFi = $parseFecha($c->fecha_inicio);
                $cFf = $parseFecha($c->fecha_fin);
                $cSt = optional($c->status)->name ?? 'Pendiente';
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['reserva_id' => $c->id]) }}"
               class="d-flex justify-content-between align-items-center flex-wrap text-decoration-none border rounded-2 px-2 py-1 mb-1"
               style="border-color:var(--grid) !important; background:#fcfdfe;">
                <span class="val" style="font-size:10.5px;">#{{ $c->codigo_reserva }}</span>
                <span class="flex-grow-1 mx-2" style="min-width:120px;">
                    <span class="val" style="font-size:10px;">{{ optional($c->user)->name ?? 'Sin asignar' }}</span>
                    <span class="sub d-block">{{ optional($c->inmueble)->name ?? 'N/D' }}</span>
                </span>
                <span class="sub me-2">{{ $cFi?->format('d/m') ?? '—' }} → {{ $cFf?->format('d/m/y') ?? '—' }}</span>
                <span class="pill pill-{{ $stDot($cSt) }}" style="font-size:8px;">{{ mb_strtoupper($cSt) }}</span>
            </a>
        @empty
            <div class="sub">{{ $q !== '' ? 'Sin coincidencias para “'.$q.'”.' : 'No hay reservas en este contexto.' }}</div>
        @endforelse
    </div>
@endif

<!-- SHOW DE LA RESERVA SELECCIONADA -->
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
        if ($enCurso)        { $cdTxt = 'EN CURSO';  $cdCls = 'cd-live'; }
        elseif ($dias === 0) { $cdTxt = 'HOY';       $cdCls = 'cd-today'; }
        elseif ($dias === 1) { $cdTxt = 'MAÑANA';    $cdCls = 'cd-next'; }
        elseif ($dias !== null && $dias > 1) { $cdTxt = '+'.$dias.'d'; $cdCls = 'cd-future'; }
        elseif ($dias !== null && $dias < 0) { $cdTxt = 'FINALIZADA';  $cdCls = 'cd-done'; }
        $stName = optional($reservaSel->status)->name ?? 'Pendiente';
    @endphp

    @if($fueraDeContexto)
        <div class="border rounded-2 px-2 py-1 mb-2 sub" style="background:#fdf8ec; border-color:#f3e7c6 !important; color:#a3822f;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Esta reserva pertenece a “{{ optional($reservaSel->inmueble)->name ?? 'otro inmueble' }}”; la estás viendo fuera del filtro actual.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:8px;">
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <span class="val" style="font-size:16px;">#{{ $reservaSel->codigo_reserva }}</span>
            <span class="pill pill-{{ $stDot($stName) }}">{{ mb_strtoupper($stName) }}</span>
            <span class="cd {{ $cdCls }}">{{ $cdTxt }}</span>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
            @if($rutaPdf)
                <a href="{{ route($rutaPdf, $reservaSel->id) }}" target="_blank"
                   class="btn-mini d-inline-flex align-items-center text-decoration-none" style="gap:4px;">
                    <i class="bi bi-file-earmark-pdf" style="font-size:9px;"></i> Comprobante
                </a>
            @endif
            <span class="sub"><i class="bi bi-clock me-1"></i>Creada {{ optional($reservaSel->created_at)->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    @if($enCurso)
        <div class="d-flex align-items-center mb-2" style="gap:6px;">
            <div class="flex-grow-1" style="height:4px; background:var(--grid); border-radius:2px; overflow:hidden;">
                <div style="height:100%; width:{{ $pct }}%; background:#b9d4c5;"></div>
            </div>
            <span class="sub">{{ $pct }}% transcurrido</span>
        </div>
    @endif

    <!-- FICHA -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3"><div class="fact">
            <div class="lbl">Titular</div>
            <div class="val">{{ optional($reservaSel->user)->name ?? 'Sin asignar' }}</div>
            <div class="sub">{{ optional($reservaSel->user)->email ?? '' }}@if(optional($reservaSel->user)->nid) · C.C. {{ $reservaSel->user->nid }}@endif</div>
        </div></div>
        <div class="col-6 col-md-3"><div class="fact">
            <div class="lbl">Inmueble</div>
            <div class="val">{{ optional($reservaSel->inmueble)->name ?? 'N/D' }}</div>
        </div></div>
        <div class="col-6 col-md-2"><div class="fact">
            <div class="lbl">Check-in</div><div class="val">{{ $fi?->format('d/m/Y') ?? '—' }}</div>
        </div></div>
        <div class="col-6 col-md-2"><div class="fact">
            <div class="lbl">Check-out</div><div class="val">{{ $ff?->format('d/m/Y') ?? '—' }}</div>
        </div></div>
        <div class="col-6 col-md-2"><div class="fact">
            <div class="lbl">Noches</div><div class="val">{{ $noches ?? '—' }}</div>
        </div></div>
        <div class="col-6 col-md-3"><div class="fact">
            <div class="lbl">Monto total</div>
            <div class="val" style="color:var(--accent);">$ {{ number_format((float) ($reservaSel->monto_total ?? 0), 2) }}</div>
        </div></div>
        <div class="col-6 col-md-3"><div class="fact">
            <div class="lbl">Estado actual</div>
            <div class="val">{{ $stName }}</div>
            <div class="sub">{{ $siguiente ? 'Siguiente sugerido: '.$siguiente->name : ($esTerminal($stName) ? 'estado terminal' : 'sin sugerencia') }}</div>
        </div></div>
        <div class="col-12"><div class="fact">
            <div class="lbl">Comentario</div>
            <div class="val" style="font-weight:400; font-size:11px;">{{ $reservaSel->comentario_reserva ?: '—' }}</div>
        </div></div>
    </div>

    <!-- RUTA DE ESTADOS -->
    <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="lbl"><i class="bi bi-diagram-3 me-1"></i> Ruta de estados</div>
        @if($rutaEstado)<span class="sub">clic en un paso para preseleccionarlo abajo</span>@endif
    </div>
    <div class="ruta mb-3">
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
            <div class="{{ $cls }}" data-stid="{{ $s->id }}" title="{{ $s->name }}">
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

    <!-- CAMBIO DE ESTADO -->
    @if($rutaEstado)
        <form method="POST" action="{{ route($rutaEstado, $reservaSel->id) }}" id="rsvEstadoForm"
              class="border rounded-2 p-2 mb-3" style="background:#fcfdfe; border-color:var(--grid) !important;">
            @csrf
            @method('PATCH')
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <div class="lbl mb-1">Cambiar estado a</div>
                    <select name="id_rsv_statuses" id="rsvEstadoSelect" class="form-select form-select-sm">
                        @foreach($steps as $s)
                            <option value="{{ $s->id }}" {{ ($siguiente?->id ?? $reservaSel->id_rsv_statuses) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}{{ ($siguiente && $s->id == $siguiente->id) ? ' · sugerido' : '' }}{{ $esTerminal($s->name) ? ' · terminal' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <div class="lbl mb-1">Nota (opcional)</div>
                    <input type="text" name="nota" class="form-control form-control-sm" maxlength="500"
                           placeholder="Motivo / observación del cambio…">
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-green w-100">
                        <i class="bi bi-arrow-repeat me-1"></i> Aplicar cambio
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="border rounded-2 p-2 mb-3 sub" style="background:#fffdf6; border-color:#f3e7c6 !important; color:#a3822f;">
            <i class="bi bi-plug me-1"></i>
            No se detectó la ruta <code>PATCH rsv.reservas.cambiar_estado</code>. Verifícala en routes/ para activar el cambio de estado.
        </div>
    @endif

    <!-- HISTORIAL -->
    <div class="lbl mb-1"><i class="bi bi-clock-history me-1"></i> Historial de estados</div>
    @if($historial->isNotEmpty())
        @foreach($historial as $h)
            @php $hm = $histMap($h); @endphp
            <div class="hist-item">
                <span class="hist-time">{{ $hm['fecha'] }}</span>
                <span class="flex-grow-1">
                    <span class="pill pill-{{ $stDot($hm['estado']) }}" style="font-size:8px;">{{ $hm['estado'] }}</span>
                    @if($hm['nota'])<span class="sub ms-1">{{ $hm['nota'] }}</span>@endif
                </span>
                <span class="sub">{{ $hm['usuario'] }}</span>
            </div>
        @endforeach
    @else
        <div class="sub">Sin movimientos registrados todavía. Cada cambio de estado quedará asentado aquí.</div>
    @endif
@endif

</div>

<script>
(function () {
    var root = document.getElementById('rsvShowRoot');
    if (!root) return;

    /* Buscador: auto-envio con debounce + foco preservado tras recargar */
    var form = root.querySelector('#rsvShowSearch');
    var inp  = form ? form.querySelector('input[name="reserva_q"]') : null;
    if (form && inp) {
        var timer = null;
        inp.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { form.submit(); }, 700);
        });
        var qActual = @js(request('reserva_q'));
        if (qActual) {
            inp.focus();
            inp.setSelectionRange(inp.value.length, inp.value.length);
        }
    }

    /* Stepper: clic en un paso preselecciona el estado en el formulario */
    var sel = root.querySelector('#rsvEstadoSelect');
    if (sel) {
        root.querySelectorAll('.rt-step.clickable').forEach(function (st) {
            st.addEventListener('click', function () {
                sel.value = st.getAttribute('data-stid');
                var f = root.querySelector('#rsvEstadoForm');
                if (f) { f.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            });
        });
    }
})();
</script>
