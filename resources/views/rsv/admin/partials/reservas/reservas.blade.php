{{-- reservas.blade.php · v18 COMPLETO · render Blade + buscador instantáneo sin conflictos --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="rvRoot"
     data-modo="{{ ($modoGlobal ?? true) ? 'global' : 'single' }}"
     data-ctx="{{ ($modoGlobal ?? true) ? 'Global' : ((is_object($xlsInm ?? null) ? ($xlsInm->name ?? 'Inmueble') : ('Inmueble #'.($xlsInmId ?? '')))) }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; white-space: nowrap; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .table tbody tr.row-today > * { background-color: #fffdf6; }
    .reservas-soft .table tbody tr.row-today:hover > * { background-color: #fffbea; }
    .reservas-soft .rv-num { color: var(--ink-4); text-align: center; width: 22px; }
    .reservas-soft .rv-ellip { max-width: 190px; overflow: hidden; text-overflow: ellipsis; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .dot { display: inline-block; width: 5px; height: 5px; border-radius: 50%; background: currentColor; margin-right: 4px; vertical-align: middle; opacity: .75; }
    .reservas-soft th.sortable { cursor: pointer; user-select: none; }
    .reservas-soft th.sortable:hover { color: var(--ink-2); }
    .reservas-soft th .s-ind { font-size: 7.5px; opacity: .3; }
    .reservas-soft th.sortable[data-dir="asc"] .s-ind, .reservas-soft th.sortable[data-dir="desc"] .s-ind { opacity: .8; color: var(--accent); }
    .reservas-soft .cd { display: inline-block; padding: 0 5px; border-radius: 8px; font-size: 7.5px; font-weight: 600; border: 1px solid; letter-spacing: .02em; line-height: 12px; }
    .reservas-soft .cd-today  { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .cd-next   { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
    .reservas-soft .cd-future { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .cd-live   { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
    .reservas-soft .cd-done   { color:#c8d0d9; background:#fbfcfd; border-color:#f3f6f9; }
    .reservas-soft .kpi { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border: 1px solid var(--grid); border-radius: 4px; background: #fcfdfe; font-size: 8.5px; color: var(--ink-3); }
    .reservas-soft .kpi .kpi-l { color: #b3bcc7; font-weight: 600; font-size: 7.5px; letter-spacing: .05em; }
    .reservas-soft .kpi b { color: #5b6b7d; font-weight: 600; font-variant-numeric: tabular-nums; }
    .reservas-soft .fchip { padding: 1px 8px; border-radius: 10px; font-size: 8.5px; font-weight: 500; white-space: nowrap; text-decoration: none; border: 1px solid var(--grid); color: var(--ink-4); background: #fcfdfe; cursor: pointer; user-select: none; }
    .reservas-soft .fchip:hover { color: var(--ink-2); border-color: var(--grid-2); text-decoration: none; }
    .reservas-soft .fchip.active { color: var(--accent); background: var(--accent-bg); border-color: var(--accent-bd); font-weight: 600; }
    .reservas-soft .fchip .cnt { font-weight: 700; font-variant-numeric: tabular-nums; font-size: 7.5px; opacity: .75; margin-left: 2px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; flex: 1 1 240px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .xls-date { font-size: 8.5px; color: var(--ink-3); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 6px; width: 110px; box-shadow: none !important; outline: 0; }
    .reservas-soft .xls-date:focus { border-color: #c8d6cd; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .btn-green:hover { background: #e3f1e9; color: #34684e; }
    .reservas-soft .ctx-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 7.5px; font-weight: 600; color: var(--accent); background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap; }
    .reservas-soft .rv-agenda-toggle { display: inline-flex; align-items: center; gap: 5px; font-size: 8.5px; font-weight: 600; color: var(--ink-3); background: var(--head); border: 1px solid var(--grid); border-radius: 3px; padding: 2px 9px; }
    .reservas-soft .rv-agenda-toggle:hover { color: var(--ink-2); border-color: var(--grid-2); }
    .reservas-soft .rv-agenda-toggle .lg { color: #5d8a70; }
    .reservas-soft .rv-agenda-toggle .sl { color: #c08484; }
    .reservas-soft .ag-fecha-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 7.5px; font-weight: 700; letter-spacing: .04em; color: var(--accent); background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap; }
    .reservas-soft .ag-fecha-chip.other { color: #5d86a6; background: #f2f8fc; border-color: #dbe9f3; }
    .reservas-soft .ag-hoy-btn { display: inline-flex; align-items: center; gap: 3px; font-size: 7.5px; font-weight: 600; color: var(--ink-3); background: #fff; border: 1px solid var(--grid-2); border-radius: 8px; padding: 0 7px; line-height: 14px; text-decoration: none; white-space: nowrap; }
    .reservas-soft .ag-hoy-btn:hover { color: var(--accent); border-color: var(--accent-bd); text-decoration: none; }
    .reservas-soft .ag-consultar { display: inline-flex; align-items: center; gap: 4px; font-family: inherit; font-size: 8.5px; font-weight: 600; color: var(--accent); background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 3px; padding: 2px 10px; cursor: pointer; }
    .reservas-soft .ag-consultar:hover { background: #e3f1e9; color: #34684e; }
    .reservas-soft .no-scrollbar::-webkit-scrollbar { display: none; }
    .reservas-soft .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .search-live { display: inline-flex; align-items: center; gap: 4px; font-size: 7.5px; font-weight: 600; color: #5d86a6; background: #f2f8fc; border: 1px solid #dbe9f3; border-radius: 8px; padding: 0 7px; line-height: 14px; }
    .reservas-soft .rv-pagination { border-top: 1px solid var(--grid); margin-top: 8px; padding-top: 7px; }
</style>


@php
    $xlsInm = null; $xlsInmId = null; $modoGlobal = true;
    $dsCol = collect(); $dsStatuses = collect();
    $dsTotalContexto = 0;
    $dsStatusesJson = '[]'; $dsJson = '[]';
    $kpiActivas = null; $kpiMontoMes = null; $kpiOcupacion = null;
    $agendaHoy = collect();
    $hoy = now()->startOfDay();

    $agendaFechaRaw = request('agenda_fecha');
    try {
        $agendaFecha = $agendaFechaRaw
            ? \Illuminate\Support\Carbon::parse($agendaFechaRaw)->startOfDay()
            : $hoy->copy();
    } catch (\Throwable $e) {
        $agendaFecha = $hoy->copy();
    }
    $esAgendaHoy = $agendaFecha->isSameDay($hoy);

    if (isset($inmueble) && $inmueble) {
        $xlsInm = $inmueble;
        $xlsInmId = is_object($xlsInm) ? ($xlsInm->id ?? null) : $xlsInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $xlsInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $xlsInmId = request('inmueble_id');
    }
    $modoGlobal = empty($xlsInmId);

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
                ->get();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('reservas dataset: '.$e->getMessage());
        $dsCol = collect();
    }

    if ($dsCol->isEmpty() && isset($reservas)) {
        try {
            $fbItems = null;
            if (is_object($reservas) && method_exists($reservas, 'getCollection')) {
                $fbItems = $reservas->getCollection();
            } elseif (is_iterable($reservas)) {
                $tmp = [];
                foreach ($reservas as $it) { if (is_object($it)) { $tmp[] = $it; } }
                $fbItems = collect($tmp);
            }
            if ($fbItems) {
                if (!$modoGlobal && $xlsInmId) {
                    $fbItems = $fbItems->filter(function ($r) use ($xlsInmId) {
                        if (!is_object($r)) { return false; }
                        $rid = $r->id_rsv_catalogo_inmueble ?? (isset($r->inmueble) && is_object($r->inmueble) ? $r->inmueble->id : null);
                        return $rid !== null && (string) $rid == (string) $xlsInmId;
                    })->values();
                }
                $dsCol = $fbItems->values();
                $dsTotalContexto = $dsCol->count();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('reservas fallback: '.$e->getMessage());
        }
    }

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $dsJson = json_encode($dsCol->map($dsMap)->values()->all(), $jhFlags);
    if (!is_string($dsJson)) { $dsJson = '[]'; }
    $dsStatusesJson = json_encode(
        $dsStatuses->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
        $jhFlags
    );
    if (!is_string($dsStatusesJson)) { $dsStatusesJson = '[]'; }

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

    $rvRow = function ($r) use ($hoy) {
        $fi = $r->fecha_inicio ? $r->fecha_inicio->copy()->startOfDay() : null;
        $ff = $r->fecha_fin ? $r->fecha_fin->copy()->startOfDay() : null;

        $noches = ($fi && $ff) ? (int) abs((int) (($ff->getTimestamp() - $fi->getTimestamp()) / 86400)) : null;

        $enCurso = false; $pct = 0;
        if ($fi && $ff && $hoy->betweenIncluded($fi, $ff)) {
            $enCurso = true;
            if ($noches > 0) {
                $pct = (int) min(100, max(0, round((($hoy->getTimestamp() - $fi->getTimestamp()) / 86400 / $noches) * 100)));
            }
        }

        $dias = $fi ? (int) floor(($fi->getTimestamp() - $hoy->getTimestamp()) / 86400) : null;

        $cdTxt = '—'; $cdCls = 'cd-done';
        if ($enCurso) {
            $cdTxt = 'EN CURSO'; $cdCls = 'cd-live';
        } elseif ($dias === 0) {
            $cdTxt = 'HOY'; $cdCls = 'cd-today';
        } elseif ($dias === 1) {
            $cdTxt = 'MAÑANA'; $cdCls = 'cd-next';
        } elseif ($dias !== null && $dias > 1) {
            $cdTxt = '+' . $dias . 'd'; $cdCls = 'cd-future';
        } elseif ($dias !== null && $dias < 0) {
            $cdTxt = 'FIN'; $cdCls = 'cd-done';
        }

        $stName = optional($r->status)->name ?? 'Pendiente';
        $s = strtolower((string) $stName);
        $dot = 'text-secondary';
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) { $dot = 'text-success'; }
        elseif (str_contains($s, 'pendien')) { $dot = 'text-warning'; }
        elseif (str_contains($s, 'cancela') || str_contains($s, 'rechaz')) { $dot = 'text-danger'; }

        return [
            'codigo'   => (string) ($r->codigo_reserva ?? ''),
            'uname'    => (string) (optional($r->user)->name ?? 'Sin asignar'),
            'umail'    => (string) (optional($r->user)->email ?? ''),
            'unid'     => (string) (optional($r->user)->nid ?? ''),
            'inm'      => (string) (optional($r->inmueble)->name ?? 'N/D'),
            'chkFmt'   => $fi ? $fi->format('d/m') : '—',
            'finFmt'   => $ff ? $ff->format('d/m/y') : '—',
            'iniAttr'  => $fi ? $fi->getTimestamp() : 0,
            'noches'   => $noches,
            'monto'    => (float) ($r->monto_total ?? 0),
            'stName'   => $stName,
            'dot'      => $dot,
            'cdTxt'    => $cdTxt,
            'cdCls'    => $cdCls,
            'enCurso'  => $enCurso,
            'pct'      => $pct,
            'llegaHoy' => ($dias === 0),
        ];
    };
@endphp


<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">

    <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
        <i class="bi bi-calendar2-check me-1" style="font-size: 8px; color: #5d8a70;"></i>
        {{ ($modoGlobal ?? true) ? 'Reservas Globales del Sistema' : 'Reservas del Inmueble' }}
        <span class="badge rounded-pill ms-1" style="font-size: 6.5px; background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; vertical-align: middle;">v18</span>
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
                title="Descarga en Excel TODO el resultado filtrado">
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


<!-- AGENDA -->
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
            <input type="date" id="agendaFechaInput" class="xls-date" style="width: 118px;"
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


<!-- BÚSQUEDA + FECHAS -->
<div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">

    <div class="d-flex align-items-center" style="gap: 6px; flex: 1 1 240px; min-width: 220px;">
        <i class="bi bi-search" style="font-size: 8.5px; color: #c3ccd6;"></i>
        <input type="text" id="inputSearchReservas" class="xls-search"
               placeholder="Nombre, cédula, código o inmueble… (instantáneo)"
               autocomplete="off">
        <span class="search-live" title="Filtra mientras escribes">
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


<!-- TABLA · RENDER INICIAL POR BLADE -->
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
            @forelse(($dsCol ?? collect())->take(15) as $reserva)
                @php $f = $rvRow($reserva); @endphp

                <tr class="reserva-row {{ $f['llegaHoy'] ? 'row-today' : '' }}">

                    <td class="rv-num"><span class="rv-idx">{{ $loop->iteration }}</span></td>

                    <td class="strong">#{{ $f['codigo'] }}</td>

                    <td class="rv-ellip">
                        <span class="strong" title="{{ $f['umail'] }}">{{ $f['uname'] }}</span>
                        <div class="sub">{{ $f['umail'] }}@if($f['unid']) · C.C. {{ $f['unid'] }}@endif</div>
                    </td>

                    <td class="rv-ellip">
                        <i class="bi bi-house-door me-1" style="font-size: 7.5px; color: #c3ccd6;"></i>{{ $f['inm'] }}
                    </td>

                    <td data-ini="{{ $f['iniAttr'] }}">
                        {{ $f['chkFmt'] }}
                        <span class="cd {{ $f['cdCls'] }} ms-1">{{ $f['cdTxt'] }}</span>
                        @if($f['enCurso'])
                            <div class="d-flex align-items-center gap-1">
                                <span style="display:inline-block;width:38px;height:2.5px;background:#f1f5f9;border-radius:2px;overflow:hidden;">
                                    <span style="display:block;height:100%;width:{{ $f['pct'] }}%;background:#b9d4c5;"></span>
                                </span>
                                <span class="sub">{{ $f['pct'] }}%</span>
                            </div>
                        @endif
                    </td>

                    <td style="color: #a8b3c1;">{{ $f['finFmt'] }}</td>

                    <td class="text-center">{{ $f['noches'] ?? '—' }}</td>

                    <td class="text-end strong" data-monto="{{ $f['monto'] }}">
                        ${{ number_format($f['monto'], 2) }}
                    </td>

                    <td><span class="dot {{ $f['dot'] }}"></span>{{ strtoupper($f['stName']) }}</td>

                    <td class="text-center">
                        <button type="button" class="btn-mini">Detalle</button>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                        @if(!($modoGlobal ?? true))
                            <i class="bi bi-funnel me-1"></i> Este inmueble aún no tiene reservas.
                        @else
                            No hay reservas registradas en el sistema.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>

</div>


<!-- PAGINACIÓN -->
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


{{-- DATASET --}}
<script type="application/json" id="rvDataset">{!! $dsJson !!}</script>
<script type="application/json" id="rvStatuses">{!! $dsStatusesJson !!}</script>


<script>
/* ═══ v18 · TODOS los identificadores con prefijo rv · cero colisiones ═══ */
(function () {

    var rvErr = null;
    var rvTbody = null;

    function rvIniciar() {
    try {

        var rvRoot = document.getElementById('rvRoot');
        var rvModo = rvRoot ? (rvRoot.getAttribute('data-modo') || 'global') : 'global';
        var rvCtx  = rvRoot ? (rvRoot.getAttribute('data-ctx') || 'Global') : 'Global';

        /* Buscador de elementos (nombre unico: rvEl — NUNCA 'q') */
        function rvEl(sel) {
            var el = rvRoot ? rvRoot.querySelector(sel) : null;
            return el || document.querySelector(sel);
        }

        rvTbody   = rvEl('#tbodyReservas');
        var rvBadge = rvEl('#rvBadgeTxt');
        var rvPgInfo  = rvEl('#rvPgInfo');
        var rvPgLbl   = rvEl('#rvPgLbl');
        var rvBtnPrev = rvEl('#rvPrev');
        var rvBtnNext = rvEl('#rvNext');

        var rvDS = [];
        try {
            var rvRaw = rvRoot ? rvRoot.querySelector('#rvDataset') : null;
            if (!rvRaw) { rvRaw = document.getElementById('rvDataset'); }
            var rvRawText = rvRaw ? (rvRaw.textContent || rvRaw.innerHTML || '[]') : '[]';
            rvDS = JSON.parse(rvRawText);
        } catch (e) {
            rvErr = 'JSON dataset invalido: ' + e.message;
            rvDS = [];
        }
        if (!Array.isArray(rvDS)) { rvDS = []; }

        var RV_PAGE_SIZE = 15;
        var rvPage = 1;
        var rvTerm = '';
        var rvStatusF = '';
        var rvDesde = '';
        var rvHasta = '';
        var rvSortAttr = null;
        var rvSortDir = 'asc';

        function rvEsc(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        function rvMoney(n) { return '$' + Number(n || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function rvMidnight(dstr) { var d = new Date(dstr + 'T00:00:00'); return isNaN(d) ? null : d; }
        function rvFmtDMY(dstr) { var d = rvMidnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+String(d.getFullYear()).slice(-2) : '—'; }
        function rvFmtDM(dstr)  { var d = rvMidnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2) : '—'; }

        var RV_HOY = new Date(); RV_HOY.setHours(0,0,0,0);
        function rvDayDiff(a, b) { return Math.round((a - b) / 86400000); }

        function rvStatusDot(stn) {
            var s = String(stn || '').toLowerCase();
            if (s.indexOf('confirm') !== -1 || s.indexOf('activ') !== -1) return 'text-success';
            if (s.indexOf('pendien') !== -1) return 'text-warning';
            if (s.indexOf('cancela') !== -1 || s.indexOf('rechaz') !== -1) return 'text-danger';
            return 'text-secondary';
        }

        function rvStatusNombre() {
            if (rvStatusF === '') { return 'Todas'; }
            var chip = document.querySelector('.reservas-soft .fchip.active');
            return chip ? chip.textContent.replace(/\(\d+\)/, '').trim() : ('#' + rvStatusF);
        }

        function rvEach(list, cb) {
            if (!list) return;
            for (var i = 0; i < list.length; i++) { cb(list[i], i); }
        }

        function rvFiltrar() {
            var term = rvTerm.replace(/#/g, '').toLowerCase().trim();

            var out = rvDS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                if (rvStatusF !== '' && String(r.st) !== String(rvStatusF)) return false;
                if (rvDesde) {
                    var ff = rvMidnight(r.ff);
                    var dD = rvMidnight(rvDesde);
                    if (!ff || !dD || ff < dD) return false;
                }
                if (rvHasta) {
                    var fi = rvMidnight(r.fi);
                    var dH = rvMidnight(rvHasta);
                    if (!fi || !dH || fi > dH) return false;
                }
                return true;
            });

            if (rvSortAttr) {
                out.sort(function (a, b) {
                    var x, y;
                    if (rvSortAttr === 'monto') { x = a.monto; y = b.monto; }
                    else {
                        x = rvMidnight(a.fi) ? rvMidnight(a.fi).getTime() : 0;
                        y = rvMidnight(b.fi) ? rvMidnight(b.fi).getTime() : 0;
                    }
                    return rvSortDir === 'asc' ? x - y : y - x;
                });
            }

            return out;
        }

        function rvFilaHTML(r, nGlobal) {
            var fi = rvMidnight(r.fi), ff = rvMidnight(r.ff);
            var hoy = RV_HOY;

            var noches = (fi && ff) ? Math.abs(rvDayDiff(fi, ff)) : null;

            var enCurso = false, pct = 0, cdTxt = '—', cdCls = 'cd-done';
            if (fi && ff && hoy >= fi && hoy <= ff) {
                enCurso = true;
                if (noches > 0) { pct = Math.min(100, Math.max(0, Math.round((rvDayDiff(fi, hoy) / noches) * 100))); }
                cdTxt = 'EN CURSO'; cdCls = 'cd-live';
            } else if (fi) {
                var dias = rvDayDiff(fi, hoy);
                if (dias === 0) { cdTxt = 'HOY'; cdCls = 'cd-today'; }
                else if (dias === 1) { cdTxt = 'MAÑANA'; cdCls = 'cd-next'; }
                else if (dias > 1) { cdTxt = '+' + dias + 'd'; cdCls = 'cd-future'; }
                else if (dias < 0) { cdTxt = 'FIN'; cdCls = 'cd-done'; }
            }

            var llegaHoy = fi ? (rvDayDiff(fi, hoy) === 0) : false;
            var dot = rvStatusDot(r.stn);

            var h = '';
            h += '<tr class="reserva-row' + (llegaHoy ? ' row-today' : '') + '">';
            h += '<td class="rv-num"><span class="rv-idx">' + nGlobal + '</span></td>';
            h += '<td class="strong">#' + rvEsc(r.codigo) + '</td>';
            h += '<td class="rv-ellip"><span class="strong" title="' + rvEsc(r.umail) + '">' + rvEsc(r.uname) + '</span>'
               + '<div class="sub">' + rvEsc(r.umail) + (r.unid ? ' · C.C. ' + rvEsc(r.unid) : '') + '</div></td>';
            h += '<td class="rv-ellip"><i class="bi bi-house-door me-1" style="font-size:7.5px;color:#c3ccd6;"></i>' + rvEsc(r.inm) + '</td>';
            h += '<td data-ini="' + (fi ? fi.getTime() : 0) + '">' + (r.fi ? rvFmtDM(r.fi) : '—')
               + '<span class="cd ' + cdCls + ' ms-1">' + cdTxt + '</span>';
            if (enCurso) {
                h += '<div class="d-flex align-items-center gap-1">'
                   + '<span style="display:inline-block;width:38px;height:2.5px;background:#f1f5f9;border-radius:2px;overflow:hidden;">'
                   + '<span style="display:block;height:100%;width:' + pct + '%;background:#b9d4c5;"></span></span>'
                   + '<span class="sub">' + pct + '%</span></div>';
            }
            h += '</td>';
            h += '<td style="color:#a8b3c1;">' + (r.ff ? rvFmtDMY(r.ff) : '—') + '</td>';
            h += '<td class="text-center">' + (noches !== null ? noches : '—') + '</td>';
            h += '<td class="text-end strong" data-monto="' + r.monto + '">' + rvMoney(r.monto) + '</td>';
            h += '<td><span class="dot ' + dot + '"></span>' + rvEsc(String(r.stn || '').toUpperCase()) + '</td>';
            h += '<td class="text-center"><button type="button" class="btn-mini">Detalle</button></td>';
            h += '</tr>';
            return h;
        }

        function rvEmptyHTML(hayFiltros) {
            var msg;
            if (rvDS.length > 0 && hayFiltros) {
                msg = '<i class="bi bi-funnel me-1"></i> Sin resultados con los filtros aplicados (' + rvDS.length + ' en total).';
            } else if (rvDS.length === 0 && rvModo === 'single') {
                msg = '<i class="bi bi-funnel me-1"></i> Este inmueble aún no tiene reservas.';
            } else {
                msg = 'No hay reservas registradas en el sistema.';
            }
            return '<tr><td colspan="10" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">' + msg + '</td></tr>';
        }

        function rvFiltrarTextoFechas() {
            var term = rvTerm.replace(/#/g, '').toLowerCase().trim();
            return rvDS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                if (rvDesde) { var ff = rvMidnight(r.ff), dD = rvMidnight(rvDesde); if (!ff || !dD || ff < dD) return false; }
                if (rvHasta) { var fi = rvMidnight(r.fi), dH = rvMidnight(rvHasta); if (!fi || !dH || fi > dH) return false; }
                return true;
            });
        }

        function rvActualizarChips(base) {
            var chipTodas = rvEl('#cntTodas');
            if (chipTodas) { chipTodas.textContent = '(' + base.length + ')'; }

            var porSt = {};
            base.forEach(function (r) {
                var k = String(r.st == null ? '' : r.st);
                porSt[k] = (porSt[k] || 0) + 1;
            });
            rvEach(rvRoot ? rvRoot.querySelectorAll('.reservas-soft .fchip .cnt[data-cnt]') : null, function (el) {
                var k = el.getAttribute('data-cnt');
                el.textContent = '(' + (porSt[k] || 0) + ')';
            });
        }

        function rvRender() {
            var res = rvFiltrar();
            var hayFiltros = !!(rvTerm || rvStatusF !== '' || rvDesde || rvHasta);

            rvActualizarChips(rvFiltrarTextoFechas());

            var pages = Math.max(1, Math.ceil(res.length / RV_PAGE_SIZE));
            if (rvPage > pages) { rvPage = pages; }

            var ini = (rvPage - 1) * RV_PAGE_SIZE;
            var slice = res.slice(ini, ini + RV_PAGE_SIZE);

            var h = '';
            if (res.length === 0) {
                h = rvEmptyHTML(hayFiltros);
            } else {
                for (var i = 0; i < slice.length; i++) {
                    h += rvFilaHTML(slice[i], ini + i + 1);
                }
            }
            if (rvTbody) { rvTbody.innerHTML = h; }

            if (rvBadge) {
                rvBadge.innerHTML = hayFiltros
                    ? 'Mostrando <b>' + res.length + '</b> de ' + rvDS.length
                    : '<b>' + rvDS.length + '</b> ' + (rvDS.length == 1 ? 'reserva' : 'reservas');
            }

            var rango = res.length === 0 ? '—' : (ini + 1) + '–' + Math.min(ini + RV_PAGE_SIZE, res.length);
            if (rvPgInfo) { rvPgInfo.innerHTML = '<i class="bi bi-layers" style="font-size:7.5px;color:#b3bcc7;"></i> ' + rango + ' de <b>' + res.length + '</b>'; }
            if (rvPgLbl)  { rvPgLbl.textContent = 'Página ' + rvPage + ' de ' + pages; }
            if (rvBtnPrev) { rvBtnPrev.disabled = (rvPage <= 1); }
            if (rvBtnNext) { rvBtnNext.disabled = (rvPage >= pages); }
        }

        function rvExportarExcel() {
            var res = rvFiltrar();

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
            if (rvTerm && rvTerm.trim() !== '') { filtrosDesc.push('Búsqueda: "' + rvTerm.trim() + '"'); }
            filtrosDesc.push('Estado: ' + rvStatusNombre());
            if (rvDesde) { filtrosDesc.push('Desde: ' + rvFmtDMY(rvDesde)); }
            if (rvHasta) { filtrosDesc.push('Hasta: ' + rvFmtDMY(rvHasta)); }
            if (filtrosDesc.length === 1) { filtrosDesc.push('(sin filtros adicionales · contexto completo)'); }

            var suma = 0;
            res.forEach(function (r) { suma += Number(r.monto || 0); });

            var rows = '';
            res.forEach(function (r, i) {
                rows += '<tr>'
                    + '<td>' + (i + 1) + '</td>'
                    + '<td>' + rvEsc(r.codigo) + '</td>'
                    + '<td>' + rvEsc(r.uname) + '</td>'
                    + '<td>' + rvEsc(r.umail) + '</td>'
                    + '<td>' + rvEsc(r.unid) + '</td>'
                    + '<td>' + rvEsc(r.inm) + '</td>'
                    + '<td>' + (r.fi ? rvFmtDMY(r.fi) : '') + '</td>'
                    + '<td>' + (r.ff ? rvFmtDMY(r.ff) : '') + '</td>'
                    + '<td>' + rvEsc(r.stn) + '</td>'
                    + '<td style="mso-number-format:\'0.00\';">' + Number(r.monto || 0).toFixed(2) + '</td>'
                    + '</tr>';
            });

            var html =
                '<html><head><meta charset="UTF-8"><style>' +
                'table{border-collapse:collapse;font-family:Calibri,Arial,sans-serif;font-size:11px;}' +
                'td,th{border:1px solid #d0d7de;padding:4px 8px;}' +
                '.ttl{font-size:15px;font-weight:bold;}' +
                '.meta{color:#666;font-size:10px;}' +
                'th{background:#f0f7f2;color:#2e5c43;text-transform:uppercase;font-size:10px;letter-spacing:1px;}' +
                '.tot{font-weight:bold;background:#fafbfd;}' +
                '</style></head><body><table>' +
                '<tr><td colspan="10" class="ttl">REPORTE DE RESERVAS · ' + rvEsc(rvCtx) + '</td></tr>' +
                '<tr><td colspan="10" class="meta">Exportado: ' + fExport + '</td></tr>' +
                '<tr><td colspan="10" class="meta">' + rvEsc(filtrosDesc.join(' · ')) + '</td></tr>' +
                '<tr><td colspan="10"></td></tr>' +
                '<tr><th>#</th><th>Código</th><th>Titular</th><th>Email</th><th>Cédula</th>' +
                '<th>Inmueble</th><th>Check-in</th><th>Check-out</th><th>Estado</th><th>Monto</th></tr>' +
                rows +
                '<tr class="tot"><td colspan="9">TOTAL: ' + res.length + ' reserva(s)</td>' +
                '<td style="mso-number-format:\'0.00\';">' + suma.toFixed(2) + '</td></tr>' +
                '</table></body></html>';

            try {
                var blob = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Reservas_' + rvCtx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                a.style.display = 'none';
                a.click();
                setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            } catch (e) {
                var blob2 = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url2 = URL.createObjectURL(blob2);
                var a2 = document.createElement('a');
                a2.href = url2;
                a2.download = 'Reservas_' + rvCtx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                document.body.appendChild(a2);
                a2.click();
                document.body.removeChild(a2);
                URL.revokeObjectURL(url2);
            }
        }

        var rvBtnExport = rvEl('#rvExport');
        if (rvBtnExport) {
            rvBtnExport.addEventListener('click', function (e) {
                e.stopPropagation();
                rvExportarExcel();
            });
        }

        /* ═══ BUSCADOR AUTOMÁTICO (instantáneo, sin recargas) ═══ */
        var rvSearch = rvEl('#inputSearchReservas');
        if (rvSearch) {
            rvSearch.addEventListener('input', function () {
                rvTerm = this.value;
                rvPage = 1;
                rvRender();
            });
        }

        /* Fechas por solapamiento */
        var rvInpDesde = rvEl('#rvDesde');
        var rvInpHasta = rvEl('#rvHasta');
        if (rvInpDesde) { rvInpDesde.addEventListener('change', function () { rvDesde = this.value || ''; rvPage = 1; rvRender(); }); }
        if (rvInpHasta) { rvInpHasta.addEventListener('change', function () { rvHasta = this.value || ''; rvPage = 1; rvRender(); }); }

        /* Chips de estado */
        rvEach(rvRoot ? rvRoot.querySelectorAll('.reservas-soft .fchip[data-status]') : null, function (chip) {
            chip.addEventListener('click', function () {
                rvEach(rvRoot.querySelectorAll('.reservas-soft .fchip[data-status]'), function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                rvStatusF = chip.getAttribute('data-status') || '';
                rvPage = 1;
                rvRender();
            });
        });

        /* Limpiar */
        var rvBtnLimpiar = rvEl('#rvLimpiar');
        if (rvBtnLimpiar) {
            rvBtnLimpiar.addEventListener('click', function () {
                rvTerm = ''; rvStatusF = ''; rvDesde = ''; rvHasta = ''; rvPage = 1;
                rvSortAttr = null; rvSortDir = 'asc';
                if (rvSearch) { rvSearch.value = ''; }
                if (rvInpDesde) { rvInpDesde.value = ''; }
                if (rvInpHasta) { rvInpHasta.value = ''; }
                rvEach(rvRoot.querySelectorAll('.reservas-soft .fchip[data-status]'), function (c) { c.classList.remove('active'); });
                var chipTodas = rvRoot.querySelector('.reservas-soft .fchip[data-status=""]');
                if (chipTodas) { chipTodas.classList.add('active'); }
                rvEach(rvRoot.querySelectorAll('.reservas-soft th.sortable'), function (o) { o.dataset.dir = ''; });
                rvRender();
            });
        }

        /* Paginación */
        if (rvBtnPrev) { rvBtnPrev.addEventListener('click', function () { if (rvPage > 1) { rvPage--; rvRender(); } }); }
        if (rvBtnNext) { rvBtnNext.addEventListener('click', function () { rvPage++; rvRender(); }); }

        /* Orden por columna */
        rvEach(rvRoot ? rvRoot.querySelectorAll('.reservas-soft th.sortable') : null, function (th) {
            th.addEventListener('click', function () {
                var attr = th.dataset.sort;
                rvSortDir = (rvSortAttr === attr && rvSortDir === 'asc') ? 'desc' : 'asc';
                rvSortAttr = attr;
                rvEach(rvRoot.querySelectorAll('.reservas-soft th.sortable'), function (o) { if (o !== th) { o.dataset.dir = ''; } });
                th.dataset.dir = rvSortDir;
                rvPage = 1;
                rvRender();
            });
        });

        /* AGENDA · fecha puntual */
        var rvAgDate = rvEl('#agendaFechaInput');
        var rvAgGo   = rvEl('#agendaConsultarBtn');
        function rvIrAFechaAgenda() {
            try {
                var url = new URL(window.location.href);
                if (rvAgDate && rvAgDate.value) { url.searchParams.set('agenda_fecha', rvAgDate.value); }
                else { url.searchParams.delete('agenda_fecha'); }
                window.location.href = url.toString();
            } catch (e) { window.location.reload(); }
        }
        if (rvAgGo) { rvAgGo.addEventListener('click', rvIrAFechaAgenda); }
        if (rvAgDate) {
            rvAgDate.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); rvIrAFechaAgenda(); }
            });
        }

        rvRender();

    } catch (e) {
        rvErr = e.message;
    }

    if (rvErr && rvTbody) {
        rvTbody.innerHTML = '<tr><td colspan="10" class="text-center py-3" style="font-size:9px;color:#c0392b;">'
            + '<b>ERROR JS (v18):</b> ' + String(rvErr).replace(/</g, '&lt;')
            + '</td></tr>';
    }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', rvIniciar);
    } else {
        rvIniciar();
    }
})();
</script>
