{{-- tab-reservas.blade.php · v12 · FIX: export ya no deja el overlay de carga eterno --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="rvRoot"
     data-modo="{{ ($modoGlobal ?? true) ? 'global' : 'single' }}"
     data-ctx="{{ ($modoGlobal ?? true) ? 'Global' : ((is_object($xlsInm ?? null) ? ($xlsInm->name ?? 'Inmueble') : ('Inmueble #'.($xlsInmId ?? '')))) }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft {
        --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
        --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7;
        --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
    }
    .reservas-soft .table > :not(caption) > * > * {
        padding: 2px 7px; height: 20px;
        line-height: 1.1; vertical-align: middle;
    }
    .reservas-soft .table thead th {
        font-size: 9px; font-weight: 600;
        letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important;
        white-space: nowrap;
    }
    .reservas-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .reservas-soft .table tbody tr:hover > * {
        background-color: #fafcfe; --bs-table-accent-bg: transparent;
    }
    .reservas-soft .table tbody tr.row-today > * { background-color: #fffdf6; }
    .reservas-soft .table tbody tr.row-today:hover > * { background-color: #fffbea; }

    .reservas-soft .rv-num { color: var(--ink-4); text-align: center; width: 22px; }
    .reservas-soft .rv-ellip { max-width: 190px; overflow: hidden; text-overflow: ellipsis; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }

    .reservas-soft .dot {
        display: inline-block; width: 5px; height: 5px;
        border-radius: 50%; background: currentColor;
        margin-right: 4px; vertical-align: middle; opacity: .75;
    }

    .reservas-soft th.sortable { cursor: pointer; user-select: none; }
    .reservas-soft th.sortable:hover { color: var(--ink-2); }
    .reservas-soft th .s-ind { font-size: 7.5px; opacity: .3; }
    .reservas-soft th.sortable[data-dir="asc"] .s-ind,
    .reservas-soft th.sortable[data-dir="desc"] .s-ind { opacity: .8; color: var(--accent); }

    .reservas-soft .cd {
        display: inline-block; padding: 0 5px;
        border-radius: 8px; font-size: 7.5px; font-weight: 600;
        border: 1px solid; letter-spacing: .02em; line-height: 12px;
    }
    .reservas-soft .cd-today  { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .cd-next   { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
    .reservas-soft .cd-future { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .cd-live   { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
    .reservas-soft .cd-done   { color:#c8d0d9; background:#fbfcfd; border-color:#f3f6f9; }

    .reservas-soft .kpi {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border: 1px solid var(--grid);
        border-radius: 4px; background: #fcfdfe; font-size: 8.5px;
        color: var(--ink-3);
    }
    .reservas-soft .kpi .kpi-l { color: #b3bcc7; font-weight: 600; font-size: 7.5px; letter-spacing: .05em; }
    .reservas-soft .kpi b { color: #5b6b7d; font-weight: 600; font-variant-numeric: tabular-nums; }

    .reservas-soft .fchip {
        padding: 1px 8px; border-radius: 10px;
        font-size: 8.5px; font-weight: 500; white-space: nowrap;
        text-decoration: none; border: 1px solid var(--grid);
        color: var(--ink-4); background: #fcfdfe;
        cursor: pointer; user-select: none;
    }
    .reservas-soft .fchip:hover { color: var(--ink-2); border-color: var(--grid-2); text-decoration: none; }
    .reservas-soft .fchip.active {
        color: var(--accent); background: var(--accent-bg);
        border-color: var(--accent-bd); font-weight: 600;
    }
    .reservas-soft .fchip .cnt {
        font-weight: 700; font-variant-numeric: tabular-nums;
        font-size: 7.5px; opacity: .75; margin-left: 2px;
    }

    .reservas-soft .xls-search {
        font-size: 9px; color: var(--ink-2); background: #fff;
        border: 1px solid var(--grid-2); border-radius: 3px;
        padding: 2px 8px; box-shadow: none !important; outline: 0;
        min-width: 200px; flex: 1 1 240px;
    }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .xls-date {
        font-size: 8.5px; color: var(--ink-3); background: #fff;
        border: 1px solid var(--grid-2); border-radius: 3px;
        padding: 2px 6px; width: 110px;
        box-shadow: none !important; outline: 0;
    }
    .reservas-soft .xls-date:focus { border-color: #c8d6cd; }

    .reservas-soft .btn-mini {
        border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
        font-family: inherit;
        font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px;
        cursor: pointer;
    }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }

    .reservas-soft .btn-green {
        background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd);
        font-family: inherit;
        font-size: 8.5px; font-weight: 600;
        padding: 2px 10px; border-radius: 3px;
    }
    .reservas-soft .btn-green:hover { background: #e3f1e9; color: #34684e; }

    .reservas-soft .ctx-chip {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 7.5px; font-weight: 600; color: var(--accent);
        background: var(--accent-bg); border: 1px solid var(--accent-bd);
        border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap;
    }

    .reservas-soft .rv-agenda-toggle {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 8.5px; font-weight: 600; color: var(--ink-3);
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 3px; padding: 2px 9px;
    }
    .reservas-soft .rv-agenda-toggle:hover { color: var(--ink-2); border-color: var(--grid-2); }
    .reservas-soft .rv-agenda-toggle .lg { color: #5d8a70; }
    .reservas-soft .rv-agenda-toggle .sl { color: #c08484; }

    .reservas-soft .ag-fecha-chip {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 7.5px; font-weight: 700; letter-spacing: .04em;
        color: var(--accent); background: var(--accent-bg);
        border: 1px solid var(--accent-bd);
        border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap;
    }
    .reservas-soft .ag-fecha-chip.other {
        color: #5d86a6; background: #f2f8fc; border-color: #dbe9f3;
    }
    .reservas-soft .ag-hoy-btn {
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 7.5px; font-weight: 600; color: var(--ink-3);
        background: #fff; border: 1px solid var(--grid-2);
        border-radius: 8px; padding: 0 7px; line-height: 14px;
        text-decoration: none; white-space: nowrap;
    }
    .reservas-soft .ag-hoy-btn:hover { color: var(--accent); border-color: var(--accent-bd); text-decoration: none; }

    .reservas-soft .ag-consultar {
        display: inline-flex; align-items: center; gap: 4px;
        font-family: inherit;
        font-size: 8.5px; font-weight: 600;
        color: var(--accent); background: var(--accent-bg);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        padding: 2px 10px; cursor: pointer;
    }
    .reservas-soft .ag-consultar:hover { background: #e3f1e9; color: #34684e; }
    .reservas-soft .ag-consultar i { font-size: 8px; }

    .reservas-soft .no-scrollbar::-webkit-scrollbar { display: none; }
    .reservas-soft .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    .reservas-soft .count-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums;
        color: var(--ink-2); background: var(--head);
        border: 1px solid var(--grid-2); border-radius: 8px;
        padding: 1px 8px; white-space: nowrap;
    }
    .reservas-soft .count-badge b { color: var(--accent); }

    .reservas-soft .search-live {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 7.5px; font-weight: 600;
        color: #5d86a6; background: #f2f8fc; border: 1px solid #dbe9f3;
        border-radius: 8px; padding: 0 7px; line-height: 14px;
    }

    .reservas-soft .rv-pagination {
        border-top: 1px solid var(--grid);
        margin-top: 8px;
        padding-top: 7px;
    }
</style>


@php
    // 0 · Valores por defecto (siempre primero)
    $xlsInm = null; $xlsInmId = null; $modoGlobal = true;
    $dsCol = collect(); $dsStatuses = collect();
    $dsTotalContexto = 0;
    $dsStatusesJson = '[]'; $dsJson = '[]';
    $kpiActivas = null; $kpiMontoMes = null; $kpiOcupacion = null;
    $agendaHoy = collect();
    $hoy = now()->startOfDay();

    // 0b · FECHA PUNTUAL DE LA AGENDA
    $agendaFechaRaw = request('agenda_fecha');
    try {
        $agendaFecha = $agendaFechaRaw
            ? \Illuminate\Support\Carbon::parse($agendaFechaRaw)->startOfDay()
            : $hoy->copy();
    } catch (\Throwable $e) {
        $agendaFecha = $hoy->copy();
    }
    $esAgendaHoy = $agendaFecha->isSameDay($hoy);

    // 1 · Contexto del inmueble
    if (isset($inmueble) && $inmueble) {
        $xlsInm = $inmueble;
        $xlsInmId = is_object($xlsInm) ? ($xlsInm->id ?? null) : $xlsInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $xlsInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $xlsInmId = request('inmueble_id');
    }
    $modoGlobal = empty($xlsInmId);

    // 2 · Dataset completo del contexto
    $dsLimit = 2000;

    $dsMap = function ($r) {
        return [
            'codigo' => (string) ($r->codigo_reserva ?? ''),
            'uname'  => (string) (optional($r->user)->name ?? 'Sin asignar'),
            'umail'  => (string) (optional($r->user)->email ?? ''),
            'unid'   => (string) (optional($r->user)->nid ?? ''),
            'inm'    => (string) (optional($r->inmueble)->name ?? 'N/D'),
            'st'     => $r->id_rsv_statuses,
            'stn'    => (string) (optional($r->status)->name ?? 'Pendiente'),
            'fi'     => $r->fecha_inicio ? $r->fecha_inicio->format('Y-m-d') : '',
            'ff'     => $r->fecha_fin ? $r->fecha_fin->format('Y-m-d') : '',
            'monto'  => (float) ($r->monto_total ?? 0),
            'com'    => (string) ($r->comentario_reserva ?? ''),
        ];
    };

    $dsStatuses = class_exists(\App\Models\Rsv\Status::class) ? \App\Models\Rsv\Status::all() : collect();

    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $dsQ = \App\Models\Rsv\Reserva::query();
            if (!$modoGlobal && $xlsInmId) {
                $dsQ->where('id_rsv_catalogo_inmueble', $xlsInmId);
            }

            $dsTotalContexto = (clone $dsQ)->count();

            $dsCol = $dsQ->with(['user', 'inmueble', 'status'])
                ->orderBy('created_at', 'desc')
                ->limit($dsLimit)
                ->get()
                ->map($dsMap)
                ->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('tab-reservas dataset: '.$e->getMessage());
        $dsCol = collect();
    }

    // 2b · Fallback desde el paginador del dashboard
    if ($dsCol->isEmpty() && isset($reservas)) {
        try {
            $fallbackItems = null;
            if (is_object($reservas) && method_exists($reservas, 'getCollection')) {
                $fallbackItems = $reservas->getCollection();
            } elseif (is_iterable($reservas)) {
                $tmp = [];
                foreach ($reservas as $it) { if (is_object($it)) { $tmp[] = $it; } }
                $fallbackItems = collect($tmp);
            }
            if ($fallbackItems) {
                if (!$modoGlobal && $xlsInmId) {
                    $fallbackItems = $fallbackItems->filter(function ($r) use ($xlsInmId) {
                        if (!is_object($r)) { return false; }
                        $rid = $r->id_rsv_catalogo_inmueble ?? (isset($r->inmueble) && is_object($r->inmueble) ? $r->inmueble->id : null);
                        return $rid !== null && (string) $rid == (string) $xlsInmId;
                    })->values();
                }
                $dsCol = $fallbackItems->map($dsMap)->values();
                $dsTotalContexto = $dsCol->count();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('tab-reservas fallback: '.$e->getMessage());
        }
    }

    // 2c · Serialización segura (raw + flags HEX)
    $dsJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $dsJson = json_encode($dsCol->values()->all(), $dsJsonFlags);
    if ($dsJson === false || $dsJson === null) { $dsJson = '[]'; }
    $dsStatusesJson = json_encode(
        $dsStatuses->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
        $dsJsonFlags
    );
    if ($dsStatusesJson === false || $dsStatusesJson === null) { $dsStatusesJson = '[]'; }

    // 3 · KPIs reales
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $qK = \App\Models\Rsv\Reserva::query();
            if (!$modoGlobal && $xlsInmId) { $qK->where('id_rsv_catalogo_inmueble', $xlsInmId); }

            $kpiActivas = (clone $qK)
                ->whereDate('fecha_inicio', '<=', $hoy->toDateString())
                ->whereDate('fecha_fin', '>=', $hoy->toDateString())
                ->count();

            $iniMes = $hoy->copy()->startOfMonth();
            $finMes = $hoy->copy()->endOfMonth();
            $resMes = (clone $qK)
                ->whereDate('fecha_inicio', '<=', $finMes->toDateString())
                ->whereDate('fecha_fin', '>=', $iniMes->toDateString())
                ->get(['fecha_inicio', 'fecha_fin', 'monto_total']);

            $montoMes = 0; $nochesOc = 0;
            foreach ($resMes as $rm) {
                $montoMes += (float) ($rm->monto_total ?? 0);
                try {
                    $ri = \Illuminate\Support\Carbon::parse($rm->fecha_inicio)->startOfDay();
                    $rf = \Illuminate\Support\Carbon::parse($rm->fecha_fin)->startOfDay();
                    $d = $ri->gt($iniMes) ? $ri : $iniMes;
                    $h = $rf->lt($finMes) ? $rf : $finMes;
                    if ($h->gt($d)) { $nochesOc += (int) $d->diffInDays($h); }
                } catch (\Throwable $ex) { continue; }
            }
            $kpiMontoMes = $montoMes;
            $diasMes = $hoy->daysInMonth;
            $kpiOcupacion = $diasMes > 0 ? round(min(100, ($nochesOc / $diasMes) * 100)).'%' : null;
        }
    } catch (\Throwable $e) { /* KPIs en guion */ }

    // 4 · Agenda de la fecha seleccionada
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $ag = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status']);
            if (!$modoGlobal && $xlsInmId) { $ag->where('id_rsv_catalogo_inmueble', $xlsInmId); }
            $agendaHoy = $ag->where(function ($q) use ($agendaFecha) {
                    $q->whereDate('fecha_inicio', $agendaFecha->toDateString())
                      ->orWhereDate('fecha_fin', $agendaFecha->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get()
                ->flatMap(function ($r) use ($agendaFecha) {
                    $out = collect();
                    try {
                        $fi = $r->fecha_inicio ? \Illuminate\Support\Carbon::parse($r->fecha_inicio) : null;
                        $ff = $r->fecha_fin ? \Illuminate\Support\Carbon::parse($r->fecha_fin) : null;
                        if ($fi && $fi->isSameDay($agendaFecha)) { $out->push(['tipo' => 'LLEGADA', 'r' => $r]); }
                        if ($ff && $ff->isSameDay($agendaFecha)) { $out->push(['tipo' => 'SALIDA', 'r' => $r]); }
                    } catch (\Throwable $ex) {}
                    return $out;
                });
        }
    } catch (\Throwable $e) { $agendaHoy = collect(); }

    // 5 · Preparador de item de agenda
    $agPrep = function ($item) {
        $out = ['ok' => false];
        $r = $item['r'] ?? null;
        if (!is_object($r)) { return $out; }
        $out['ok'] = true;
        $out['tipo'] = $item['tipo'] ?? '';
        $out['esLlegada'] = ($out['tipo'] == 'LLEGADA');
        $out['codigo'] = $r->codigo_reserva ?? '';
        $out['userName'] = optional($r->user)->name ?? 'Sin asignar';
        $out['inmName'] = optional($r->inmueble)->name ?? 'N/D';
        $stName = optional($r->status)->name ?? 'Pendiente';
        $out['stName'] = $stName;
        $s = strtolower((string) $stName);
        $dot = 'text-secondary';
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) { $dot = 'text-success'; }
        elseif (str_contains($s, 'pendien')) { $dot = 'text-warning'; }
        elseif (str_contains($s, 'cancela') || str_contains($s, 'rechaz')) { $dot = 'text-danger'; }
        $out['dot'] = $dot;

        $fh = $out['esLlegada'] ? $r->fecha_inicio : $r->fecha_fin;
        try { $out['horaFmt'] = $fh ? \Illuminate\Support\Carbon::parse($fh)->format('H:i') : '—'; }
        catch (\Throwable $ex) { $out['horaFmt'] = '—'; }

        return $out;
    };
@endphp


<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">

    <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
        <i class="bi bi-calendar2-check me-1" style="font-size: 8px; color: #5d8a70;"></i>
        {{ ($modoGlobal ?? true) ? 'Reservas Globales del Sistema' : 'Reservas del Inmueble' }}
    </h6>

    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

        @if(!($modoGlobal ?? true) && ($xlsInmId ?? null))
            <span class="ctx-chip" title="Vista filtrada por inmueble">
                <i class="bi bi-funnel-fill"></i>
                {{ (is_object($xlsInm ?? null) ? ($xlsInm->name ?? null) : null) ?? ('Inmueble #'.($xlsInmId ?? '—')) }}
            </span>
        @endif

        <span class="count-badge">
            <i class="bi bi-list-ol" style="font-size: 7.5px; color: #b3bcc7;"></i>
            <span id="rvBadgeTxt"><b>{{ $dsTotalContexto }}</b> reservas</span>
        </span>

        <button type="button" id="rvExport" class="btn-mini d-inline-flex align-items-center gap-1"
                title="Descarga en Excel TODO el resultado filtrado (no solo la página visible)">
            <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
        </button>

        <button type="button"
                class="btn-green d-inline-flex align-items-center gap-1"
                data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
            <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Reserva
        </button>
    </div>

</div>


<!-- KPIs -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-2">

    <span class="kpi">
        <span class="dot" style="color:#a3cfb6;"></span>
        <span class="kpi-l">ACTIVAS</span><b>{{ $kpiActivas ?? '—' }}</b>
    </span>

    <span class="kpi">
        <span class="dot" style="color:#a9cfe8;"></span>
        <span class="kpi-l">MES</span><b>{{ !is_null($kpiMontoMes ?? null) ? '$'.number_format($kpiMontoMes, 0) : '—' }}</b>
    </span>

    <span class="kpi">
        <span class="dot" style="color:#cdb4e4;"></span>
        <span class="kpi-l">OCUP.</span><b>{{ $kpiOcupacion ?? '—' }}</b>
    </span>

</div>


<!-- AGENDA · hoy o fecha puntual -->
<div class="mb-2">

    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

        <button type="button" class="rv-agenda-toggle" data-bs-toggle="collapse" data-bs-target="#agendaHoyBox" aria-expanded="false">
            <i class="bi bi-calendar-day" style="font-size: 8px;"></i>
            Agenda
            <span class="lg"><i class="bi bi-arrow-down-right"></i> {{ ($agendaHoy ?? collect())->where('tipo', 'LLEGADA')->count() }}</span>
            <span class="sl"><i class="bi bi-arrow-up-right"></i> {{ ($agendaHoy ?? collect())->where('tipo', 'SALIDA')->count() }}</span>
            <i class="bi bi-chevron-down" style="font-size: 7px;"></i>
        </button>

        <span class="ag-fecha-chip {{ ($esAgendaHoy ?? true) ? '' : 'other' }}"
              title="Fecha consultada en la agenda">
            <i class="bi {{ ($esAgendaHoy ?? true) ? 'bi-sun-fill' : 'bi-calendar-event' }}"></i>
            @if($esAgendaHoy ?? true)
                HOY · {{ $agendaFecha->translatedFormat('d M') }}
            @else
                {{ $agendaFecha->translatedFormat('D d/m/Y') }}
            @endif
        </span>

        <div class="d-flex align-items-center" style="gap: 5px;" title="Validar una fecha puntual">
            <i class="bi bi-calendar-event" style="font-size: 8.5px; color: #c3ccd6;"></i>
            <input type="date"
                   id="agendaFechaInput"
                   class="xls-date" style="width: 118px;"
                   value="{{ $agendaFecha->toDateString() }}"
                   title="Elija la fecha y presione Consultar (o Enter)">
            <button type="button" id="agendaConsultarBtn" class="ag-consultar" title="Consultar la fecha elegida">
                <i class="bi bi-arrow-right-circle"></i> Consultar
            </button>
        </div>

        @if(!($esAgendaHoy ?? true))
            <a href="{{ request()->fullUrlWithQuery(['agenda_fecha' => null]) }}"
               class="ag-hoy-btn" title="Volver a la agenda de hoy">
                <i class="bi bi-arrow-counterclockwise" style="font-size: 7px;"></i> Ir a hoy
            </a>
        @endif

    </div>

    <div class="collapse mt-1" id="agendaHoyBox">
        <div class="border rounded-3 overflow-hidden" style="border-color: var(--grid) !important; background: #fff;">
            <div class="px-2 py-1 border-bottom d-flex justify-content-between" style="background: var(--head); font-size: 8px; color: #a8b3c1; border-color: var(--grid) !important;">
                <span>
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $agendaFecha->translatedFormat('l d/m/Y') }}
                    @if($esAgendaHoy ?? true)
                        <span class="ms-1 opacity-75">(hoy)</span>
                    @endif
                </span>
                <span>{{ ($modoGlobal ?? true) ? 'todo el sistema' : 'este inmueble' }}</span>
            </div>
            <div class="overflow-auto" style="max-height: 200px;">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Código</th>
                            <th>Titular</th>
                            <th>Inmueble</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($agendaHoy ?? collect()) as $item)
                            @php $g = $agPrep($item); @endphp
                            @if($g['ok'] ?? false)
                            <tr class="{{ ($g['esLlegada'] ?? false) ? 'row-today' : '' }}">
                                <td style="font-weight: 600; color: {{ ($g['esLlegada'] ?? false) ? '#5d8a70' : '#c08484' }};">{{ $g['tipo'] }}</td>
                                <td class="strong">#{{ $g['codigo'] }}</td>
                                <td>{{ $g['userName'] }}</td>
                                <td class="rv-ellip">{{ $g['inmName'] }}</td>
                                <td class="text-center">{{ $g['horaFmt'] ?? '—' }}</td>
                                <td><span class="dot {{ $g['dot'] }}"></span>{{ strtoupper($g['stName']) }}</td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                                    <i class="bi bi-sun me-1"></i>
                                    Sin llegadas ni salidas para el {{ $agendaFecha->translatedFormat('d/m/Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<!-- BÚSQUEDA INSTANTÁNEA + FECHAS -->
<div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">

    <div class="d-flex align-items-center" style="gap: 6px; flex: 1 1 240px; min-width: 220px;">
        <i class="bi bi-search" style="font-size: 8.5px; color: #c3ccd6;"></i>
        <input type="text"
               id="inputSearchReservas"
               class="xls-search"
               placeholder="Nombre, cédula, código o inmueble… (instantáneo)"
               autocomplete="off">
        <span class="search-live" title="Filtra mientras escribes · toda la base del contexto">
            <i class="bi bi-lightning-charge-fill" style="font-size: 7px;"></i> vivo
        </span>
    </div>

    <div class="d-flex align-items-center" style="gap: 6px;">
        <input type="date" id="rvDesde" class="xls-date" title="Desde">
        <span style="font-size: 8.5px; color: #c3ccd6;">→</span>
        <input type="date" id="rvHasta" class="xls-date" title="Hasta">
    </div>

    <button type="button" id="rvLimpiar" class="btn-mini d-inline-flex align-items-center gap-1" title="Quitar todos los filtros">
        <i class="bi bi-x-lg" style="font-size: 7.5px;"></i> Limpiar
    </button>

</div>


<!-- CHIPS DE ESTADO -->
<div class="d-flex align-items-center gap-1 mb-2 no-scrollbar" style="overflow-x: auto;">

    <span class="me-1" style="font-size: 7.5px; letter-spacing: .05em; color: #b3bcc7; font-weight: 600;">
        <i class="bi bi-funnel-fill me-1" style="font-size: 7.5px;"></i>ESTADO
    </span>

    <span class="fchip active" data-status="" role="button">
        Todas <span class="cnt" id="cntTodas">({{ $dsCol->count() }})</span>
    </span>

    @foreach(($dsStatuses ?? collect()) as $st)
        <span class="fchip" data-status="{{ $st->id }}" role="button">
            {{ $st->name }} <span class="cnt" data-cnt="{{ $st->id }}">(0)</span>
        </span>
    @endforeach

</div>


<!-- TABLA -->
<div class="table-responsive">

    <table class="table table-sm table-hover align-middle mb-0" style="min-width: 780px;">

        <thead>
            <tr>
                <th class="rv-num">#</th>
                <th>Código</th>
                <th>Titular</th>
                <th>Inmueble</th>
                <th class="sortable" data-sort="fi">Check-in <i class="bi bi-arrow-down-up s-ind"></i></th>
                <th>Check-out</th>
                <th class="text-center">N</th>
                <th class="text-end sortable" data-sort="monto">Monto <i class="bi bi-arrow-down-up s-ind"></i></th>
                <th>Estado</th>
                <th class="text-center" style="width: 60px;">·</th>
            </tr>
        </thead>

        <tbody id="tbodyReservas">
            <tr>
                <td colspan="10" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                    <i class="bi bi-hourglass-split me-1"></i> Cargando reservas…
                </td>
            </tr>
        </tbody>

    </table>

</div>


<!-- PAGINACIÓN client-side -->
<div class="rv-pagination d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">

    <span class="count-badge" id="rvPgInfo" style="background: #fcfdfe;">
        <i class="bi bi-layers" style="font-size: 7.5px; color: #b3bcc7;"></i> —
    </span>

    <div class="d-flex align-items-center" style="gap: 5px;">
        <button type="button" id="rvPrev" class="btn-mini" disabled>‹ Anterior</button>
        <span id="rvPgLbl" style="font-size: 8.5px; color: #b3bcc7; font-weight: 600;">Página 1 de 1</span>
        <button type="button" id="rvNext" class="btn-mini" disabled>Siguiente ›</button>
    </div>

</div>

</div>


{{-- DATASET (raw + flags HEX) --}}
<script type="application/json" id="rvDataset">{!! $dsJson !!}</script>
<script type="application/json" id="rvStatuses">{!! $dsStatusesJson !!}</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        var root = document.getElementById('rvRoot');
        var modo = root ? (root.getAttribute('data-modo') || 'global') : 'global';
        var ctx  = root ? (root.getAttribute('data-ctx') || 'Global') : 'Global';

        /* ═══ DATASET ═══ */
        var DS = [];
        try {
            var raw = document.getElementById('rvDataset');
            DS = JSON.parse(raw ? (raw.textContent || '[]') : '[]');
        } catch (e) { DS = []; }
        if (!Array.isArray(DS)) { DS = []; }

        /* ═══ ESTADO ═══ */
        var PAGE_SIZE = 15;
        var page = 1;
        var q = '';
        var statusF = '';
        var fDesde = '';
        var fHasta = '';
        var sortAttr = null;
        var sortDir = 'asc';

        /* ═══ HELPERS ═══ */
        function esc(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        function money(n) { return '$' + Number(n || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function midnight(dstr) { var d = new Date(dstr + 'T00:00:00'); return isNaN(d) ? null : d; }
        function fmtDMY(dstr) { var d = midnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+String(d.getFullYear()).slice(-2) : '—'; }
        function fmtDM(dstr)  { var d = midnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2) : '—'; }

        var HOY = new Date(); HOY.setHours(0,0,0,0);
        function dayDiff(a, b) { return Math.round((a - b) / 86400000); }

        function statusDot(stn) {
            var s = String(stn || '').toLowerCase();
            if (s.indexOf('confirm') !== -1 || s.indexOf('activ') !== -1) return 'text-success';
            if (s.indexOf('pendien') !== -1) return 'text-warning';
            if (s.indexOf('cancela') !== -1 || s.indexOf('rechaz') !== -1) return 'text-danger';
            return 'text-secondary';
        }

        function statusNombre() {
            if (statusF === '') { return 'Todas'; }
            var chip = document.querySelector('.reservas-soft .fchip.active');
            return chip ? chip.textContent.replace(/\(\d+\)/, '').trim() : ('#' + statusF);
        }

        /* ════════════════════════════════════════════════════════
           LIMPIEZA DEFENSIVA DE OVERLAYS HUÉRFANOS
           Si el layout global prende un spinner/loader al hacer click
           y nadie lo apaga (porque la descarga no recarga la página),
           esta función lo apaga. Se ejecuta tras cada export.
           ════════════════════════════════════════════════════════ */
        function limpiarOverlaysHuerfanos() {
            try {
                /* 1 · Backdrops de Bootstrap sin ningún modal visible detrás */
                var hayModalVisible = document.querySelector('.modal.show');
                if (!hayModalVisible) {
                    document.querySelectorAll('.modal-backdrop').forEach(function (bd) { bd.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }

                /* 2 · Spinners/loaders comunes (huérfanos: visibles pero sin petición activa) */
                var selectoresLoader = [
                    '.preloader', '.preloader-wrapper', '#preloader',
                    '.loading-overlay', '.overlay-loading', '.loader-overlay',
                    '.spinner-overlay', '.page-loading', '#page-loading',
                    '#loader', '.global-loader', '.ajax-loader',
                    '[data-loading="true"]', '.cargando', '#cargando'
                ];
                selectoresLoader.forEach(function (sel) {
                    document.querySelectorAll(sel).forEach(function (el) {
                        /* Solo ocultar si NO contiene un modal Bootstrap visible dentro
                           (evitar apagar loaders legítimos de otros módulos en uso) */
                        if (!el.querySelector('.modal.show')) {
                            el.style.display = 'none';
                            el.classList.add('d-none');
                        }
                    });
                });

                /* 3 · Cualquier .spinner-border suelto dentro de un overlay a pantalla fija */
                document.querySelectorAll('div[style*="position: fixed"], div[style*="position:fixed"]').forEach(function (el) {
                    if (el.querySelector('.spinner-border, .spinner-grow, .loader, .fa-spin, .bi-arrow-repeat') &&
                        !el.querySelector('.modal.show') &&
                        el.id !== 'rvRoot') {
                        el.style.display = 'none';
                    }
                });
            } catch (e) { /* silencioso: nunca romper la página */ }
        }

        /* ═══ FILTRO ═══ */
        function filtrar() {
            var term = q.replace(/#/g, '').toLowerCase().trim();

            var out = DS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                if (statusF !== '' && String(r.st) !== String(statusF)) return false;
                if (fDesde) {
                    var ff = midnight(r.ff);
                    var dD = midnight(fDesde);
                    if (!ff || !dD || ff < dD) return false;
                }
                if (fHasta) {
                    var fi = midnight(r.fi);
                    var dH = midnight(fHasta);
                    if (!fi || !dH || fi > dH) return false;
                }
                return true;
            });

            if (sortAttr) {
                out.sort(function (a, b) {
                    var x, y;
                    if (sortAttr === 'monto') { x = a.monto; y = b.monto; }
                    else {
                        x = midnight(a.fi) ? midnight(a.fi).getTime() : 0;
                        y = midnight(b.fi) ? midnight(b.fi).getTime() : 0;
                    }
                    return sortDir === 'asc' ? x - y : y - x;
                });
            }

            return out;
        }

        /* ═══ RENDER DE FILA ═══ */
        function filaHTML(r, nGlobal) {
            var fi = midnight(r.fi), ff = midnight(r.ff);
            var hoy = HOY;

            var noches = (fi && ff) ? Math.abs(dayDiff(fi, ff)) : null;

            var enCurso = false, pct = 0, cdTxt = '—', cdCls = 'cd-done';
            if (fi && ff && hoy >= fi && hoy <= ff) {
                enCurso = true;
                if (noches > 0) { pct = Math.min(100, Math.max(0, Math.round((dayDiff(fi, hoy) / noches) * 100))); }
                cdTxt = 'EN CURSO'; cdCls = 'cd-live';
            } else if (fi) {
                var dias = dayDiff(fi, hoy);
                if (dias === 0) { cdTxt = 'HOY'; cdCls = 'cd-today'; }
                else if (dias === 1) { cdTxt = 'MAÑANA'; cdCls = 'cd-next'; }
                else if (dias > 1) { cdTxt = '+' + dias + 'd'; cdCls = 'cd-future'; }
                else if (dias < 0) { cdTxt = 'FIN'; cdCls = 'cd-done'; }
            }

            var llegaHoy = fi ? (dayDiff(fi, hoy) === 0) : false;
            var dot = statusDot(r.stn);

            var h = '';
            h += '<tr class="reserva-row' + (llegaHoy ? ' row-today' : '') + '">';
            h += '<td class="rv-num"><span class="rv-idx">' + nGlobal + '</span></td>';
            h += '<td class="strong">#' + esc(r.codigo) + '</td>';
            h += '<td class="rv-ellip"><span class="strong" title="' + esc(r.umail) + '">' + esc(r.uname) + '</span>'
               + '<div class="sub">' + esc(r.umail) + (r.unid ? ' · C.C. ' + esc(r.unid) : '') + '</div></td>';
            h += '<td class="rv-ellip"><i class="bi bi-house-door me-1" style="font-size:7.5px;color:#c3ccd6;"></i>' + esc(r.inm) + '</td>';
            h += '<td data-ini="' + (fi ? fi.getTime() : 0) + '">' + (r.fi ? fmtDM(r.fi) : '—')
               + '<span class="cd ' + cdCls + ' ms-1">' + cdTxt + '</span>';
            if (enCurso) {
                h += '<div class="d-flex align-items-center gap-1">'
                   + '<span style="display:inline-block;width:38px;height:2.5px;background:#f1f5f9;border-radius:2px;overflow:hidden;">'
                   + '<span style="display:block;height:100%;width:' + pct + '%;background:#b9d4c5;"></span></span>'
                   + '<span class="sub">' + pct + '%</span></div>';
            }
            h += '</td>';
            h += '<td style="color:#a8b3c1;">' + (r.ff ? fmtDMY(r.ff) : '—') + '</td>';
            h += '<td class="text-center">' + (noches !== null ? noches : '—') + '</td>';
            h += '<td class="text-end strong" data-monto="' + r.monto + '">' + money(r.monto) + '</td>';
            h += '<td><span class="dot ' + dot + '"></span>' + esc(String(r.stn || '').toUpperCase()) + '</td>';
            h += '<td class="text-center"><button type="button" class="btn-mini">Detalle</button></td>';
            h += '</tr>';
            return h;
        }

        function emptyHTML(hayFiltros) {
            var msg;
            if (DS.length > 0 && hayFiltros) {
                msg = '<i class="bi bi-funnel me-1"></i> Sin resultados con los filtros aplicados (' + DS.length + ' en total).';
            } else if (DS.length === 0 && modo === 'single') {
                msg = '<i class="bi bi-funnel me-1"></i> Este inmueble aún no tiene reservas.';
            } else {
                msg = 'No hay reservas registradas en el sistema.';
            }
            return '<tr><td colspan="10" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">' + msg + '</td></tr>';
        }

        /* ═══ RENDER PRINCIPAL ═══ */
        var tbody   = document.getElementById('tbodyReservas');
        var badgeTx = document.getElementById('rvBadgeTxt');
        var pgInfo  = document.getElementById('rvPgInfo');
        var pgLbl   = document.getElementById('rvPgLbl');
        var btnPrev = document.getElementById('rvPrev');
        var btnNext = document.getElementById('rvNext');

        function filtrarSoloTextoYFechas() {
            var term = q.replace(/#/g, '').toLowerCase().trim();
            return DS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                if (fDesde) { var ff = midnight(r.ff), dD = midnight(fDesde); if (!ff || !dD || ff < dD) return false; }
                if (fHasta) { var fi = midnight(r.fi), dH = midnight(fHasta); if (!fi || !dH || fi > dH) return false; }
                return true;
            });
        }

        function actualizarChips(base) {
            var chipTodas = document.getElementById('cntTodas');
            if (chipTodas) { chipTodas.textContent = '(' + base.length + ')'; }

            var porSt = {};
            base.forEach(function (r) {
                var k = String(r.st == null ? '' : r.st);
                porSt[k] = (porSt[k] || 0) + 1;
            });
            document.querySelectorAll('.reservas-soft .fchip .cnt[data-cnt]').forEach(function (el) {
                var k = el.getAttribute('data-cnt');
                el.textContent = '(' + (porSt[k] || 0) + ')';
            });
        }

        function render() {
            var res = filtrar();
            var hayFiltros = !!(q || statusF !== '' || fDesde || fHasta);

            actualizarChips(filtrarSoloTextoYFechas());

            var pages = Math.max(1, Math.ceil(res.length / PAGE_SIZE));
            if (page > pages) { page = pages; }

            var ini = (page - 1) * PAGE_SIZE;
            var slice = res.slice(ini, ini + PAGE_SIZE);

            var h = '';
            if (res.length === 0) {
                h = emptyHTML(hayFiltros);
            } else {
                for (var i = 0; i < slice.length; i++) {
                    h += filaHTML(slice[i], ini + i + 1);
                }
            }
            if (tbody) { tbody.innerHTML = h; }

            if (badgeTx) {
                badgeTx.innerHTML = hayFiltros
                    ? 'Mostrando <b>' + res.length + '</b> de ' + DS.length
                    : '<b>' + DS.length + '</b> ' + (DS.length == 1 ? 'reserva' : 'reservas');
            }

            var rango = res.length === 0 ? '—' : (ini + 1) + '–' + Math.min(ini + PAGE_SIZE, res.length);
            if (pgInfo) { pgInfo.innerHTML = '<i class="bi bi-layers" style="font-size:7.5px;color:#b3bcc7;"></i> ' + rango + ' de <b>' + res.length + '</b>'; }
            if (pgLbl)  { pgLbl.textContent = 'Página ' + page + ' de ' + pages; }
            if (btnPrev) { btnPrev.disabled = (page <= 1); }
            if (btnNext) { btnNext.disabled = (page >= pages); }
        }

        /* ════════════════════════════════════════════════════════
           EXPORTAR A EXCEL · TODO lo filtrado
           FIX v12 (spinner eterno):
           · El anchor de descarga YA NO se inserta en el DOM:
             a.click() sobre un elemento no conectado inicia la
             descarga igual, pero el evento NO burbujea hasta
             document/body → los listeners globales del layout
             (que prenden el loader al oír cualquier click) ni
             se enteran.
           · stopPropagation() en el click del botón (corta
             delegadores en fase burbuja).
           · Limpieza defensiva de overlays huérfanos a los
             300ms y 1200ms (apaga cualquier spinner que otro
             script hubiera dejado encendido).
           ════════════════════════════════════════════════════════ */
        function exportarExcel() {
            var res = filtrar();

            if (res.length === 0) {
                alert('No hay reservas que coincidan con los filtros actuales para exportar.');
                return;
            }

            var ahora = new Date();
            var pad = function (n) { return ('0' + n).slice(-2); };
            var fExport = pad(ahora.getDate()) + '/' + pad(ahora.getMonth() + 1) + '/' + ahora.getFullYear()
                        + ' ' + pad(ahora.getHours()) + ':' + pad(ahora.getMinutes());
            var fArchivo = ahora.getFullYear() + pad(ahora.getMonth() + 1) + pad(ahora.getDate())
                         + '_' + pad(ahora.getHours()) + pad(ahora.getMinutes());

            var filtrosDesc = [];
            if (q && q.trim() !== '') { filtrosDesc.push('Búsqueda: "' + q.trim() + '"'); }
            filtrosDesc.push('Estado: ' + statusNombre());
            if (fDesde) { filtrosDesc.push('Desde: ' + fmtDMY(fDesde)); }
            if (fHasta) { filtrosDesc.push('Hasta: ' + fmtDMY(fHasta)); }
            if (filtrosDesc.length === 1) { filtrosDesc.push('(sin filtros adicionales · contexto completo)'); }

            var suma = 0;
            res.forEach(function (r) { suma += Number(r.monto || 0); });

            var rows = '';
            res.forEach(function (r, i) {
                rows += '<tr>'
                    + '<td>' + (i + 1) + '</td>'
                    + '<td>' + esc(r.codigo) + '</td>'
                    + '<td>' + esc(r.uname) + '</td>'
                    + '<td>' + esc(r.umail) + '</td>'
                    + '<td>' + esc(r.unid) + '</td>'
                    + '<td>' + esc(r.inm) + '</td>'
                    + '<td>' + (r.fi ? fmtDMY(r.fi) : '') + '</td>'
                    + '<td>' + (r.ff ? fmtDMY(r.ff) : '') + '</td>'
                    + '<td>' + esc(r.stn) + '</td>'
                    + '<td style="mso-number-format:\'0.00\';">' + Number(r.monto || 0).toFixed(2) + '</td>'
                    + '</tr>';
            });

            var html =
                '<html>' +
                '<head><meta charset="UTF-8">' +
                '<style>' +
                'table{border-collapse:collapse;font-family:Calibri,Arial,sans-serif;font-size:11px;}' +
                'td,th{border:1px solid #d0d7de;padding:4px 8px;}' +
                '.ttl{font-size:15px;font-weight:bold;}' +
                '.meta{color:#666;font-size:10px;}' +
                'th{background:#f0f7f2;color:#2e5c43;text-transform:uppercase;font-size:10px;letter-spacing:1px;}' +
                '.tot{font-weight:bold;background:#fafbfd;}' +
                '</style></head><body>' +

                '<table>' +
                '<tr><td colspan="10" class="ttl">REPORTE DE RESERVAS · ' + esc(ctx) + '</td></tr>' +
                '<tr><td colspan="10" class="meta">Exportado: ' + fExport + '</td></tr>' +
                '<tr><td colspan="10" class="meta">' + esc(filtrosDesc.join(' · ')) + '</td></tr>' +
                '<tr><td colspan="10"></td></tr>' +
                '<tr>' +
                '<th>#</th><th>Código</th><th>Titular</th><th>Email</th><th>Cédula</th>' +
                '<th>Inmueble</th><th>Check-in</th><th>Check-out</th><th>Estado</th><th>Monto</th>' +
                '</tr>' +
                rows +
                '<tr class="tot">' +
                '<td colspan="9">TOTAL: ' + res.length + ' reserva(s)</td>' +
                '<td style="mso-number-format:\'0.00\';">' + suma.toFixed(2) + '</td>' +
                '</tr>' +
                '</table></body></html>';

            /* ── Descarga SIN tocar el DOM ──
               El anchor no se agrega al body: su click no burbujea
               hasta document → ningún listener global prende el loader. */
            try {
                var blob = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Reservas_' + ctx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                a.style.display = 'none';
                a.click(); /* SIN appendChild → evento aislado, no llega a delegadores globales */

                setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            } catch (e) {
                /* Fallback clásico (con limpieza inmediata) */
                var blob2 = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url2 = URL.createObjectURL(blob2);
                var a2 = document.createElement('a');
                a2.href = url2;
                a2.download = 'Reservas_' + ctx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                document.body.appendChild(a2);
                a2.click();
                document.body.removeChild(a2);
                URL.revokeObjectURL(url2);
            }

            /* ── Limpieza defensiva: apaga cualquier loader huérfano ── */
            setTimeout(limpiarOverlaysHuerfanos, 300);
            setTimeout(limpiarOverlaysHuerfanos, 1200);
        }

        var btnExport = document.getElementById('rvExport');
        if (btnExport) {
            btnExport.addEventListener('click', function (e) {
                /* Cortar delegadores globales en fase burbuja que
                   pudieran mostrar el loader al oír cualquier click */
                e.stopPropagation();
                exportarExcel();
            });
        }

        /* ═══ EVENTOS ═══ */

        var search = document.getElementById('inputSearchReservas');
        if (search) {
            search.addEventListener('input', function () {
                q = this.value;
                page = 1;
                render();
            });
        }

        var inpDesde = document.getElementById('rvDesde');
        var inpHasta = document.getElementById('rvHasta');
        if (inpDesde) { inpDesde.addEventListener('change', function () { fDesde = this.value || ''; page = 1; render(); }); }
        if (inpHasta) { inpHasta.addEventListener('change', function () { fHasta = this.value || ''; page = 1; render(); }); }

        document.querySelectorAll('.reservas-soft .fchip[data-status]').forEach(function (chip) {
            chip.addEventListener('click', function () {
                document.querySelectorAll('.reservas-soft .fchip[data-status]').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                statusF = chip.getAttribute('data-status') || '';
                page = 1;
                render();
            });
        });

        var btnLimpiar = document.getElementById('rvLimpiar');
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function () {
                q = ''; statusF = ''; fDesde = ''; fHasta = ''; page = 1;
                sortAttr = null; sortDir = 'asc';
                if (search) { search.value = ''; }
                if (inpDesde) { inpDesde.value = ''; }
                if (inpHasta) { inpHasta.value = ''; }
                document.querySelectorAll('.reservas-soft .fchip[data-status]').forEach(function (c) { c.classList.remove('active'); });
                var chipTodas = document.querySelector('.reservas-soft .fchip[data-status=""]');
                if (chipTodas) { chipTodas.classList.add('active'); }
                document.querySelectorAll('.reservas-soft th.sortable').forEach(function (o) { o.dataset.dir = ''; });
                render();
            });
        }

        if (btnPrev) { btnPrev.addEventListener('click', function () { if (page > 1) { page--; render(); } }); }
        if (btnNext) { btnNext.addEventListener('click', function () { page++; render(); }); }

        document.querySelectorAll('.reservas-soft th.sortable').forEach(function (th) {
            th.addEventListener('click', function () {
                var attr = th.dataset.sort;
                sortDir = (sortAttr === attr && sortDir === 'asc') ? 'desc' : 'asc';
                sortAttr = attr;
                document.querySelectorAll('.reservas-soft th.sortable').forEach(function (o) { if (o !== th) { o.dataset.dir = ''; } });
                th.dataset.dir = sortDir;
                page = 1;
                render();
            });
        });

        /* AGENDA · fecha puntual (única recarga intencional) */
        var agDate = document.getElementById('agendaFechaInput');
        var agGo   = document.getElementById('agendaConsultarBtn');
        function irAFechaAgenda() {
            try {
                var url = new URL(window.location.href);
                if (agDate && agDate.value) { url.searchParams.set('agenda_fecha', agDate.value); }
                else { url.searchParams.delete('agenda_fecha'); }
                window.location.href = url.toString();
            } catch (e) { window.location.reload(); }
        }
        if (agGo) { agGo.addEventListener('click', irAFechaAgenda); }
        if (agDate) {
            agDate.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); irAFechaAgenda(); }
            });
        }

        render();
    });
</script>
