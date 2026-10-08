{{--
    ========================================================================
    VISTA: tab-reservas.blade.php
    ESTILO: Hoja de cálculo SUAVE · celdas 8px · encabezados 9px.
            Jerarquía sumisa: tonalidades claras, bajo contraste.
    AGENDA DE HOY: consulta directa a BD (llegadas y salidas reales).
    ========================================================================
--}}

<div class="container-fluid px-0" id="xlsRoot">

    <style>
        /* ═══ BASE — tonalidades claras y sumisas ═══ */
        #xlsRoot {
            --grid:    #f1f5f9;   /* borde casi invisible */
            --grid-2:  #e9eef4;   /* borde un punto más marcado */
            --head:    #fafbfd;
            --ink:     #64748b;   /* texto de celda: gris suave */
            --ink-2:   #475569;   /* texto fuerte */
            --ink-3:   #94a3b8;   /* texto terciario */
            --ink-4:   #b3bcc7;   /* texto tenue */
            --accent:  #3d7a5c;   /* verde desaturado */
            --accent-bg: #eef6f1; /* verde pastel */
            font-family: Calibri, "Segoe UI", system-ui, sans-serif;
            font-size: 8px;
            line-height: 1.4;
            color: var(--ink);
        }

        #xlsRoot .no-scrollbar::-webkit-scrollbar { display: none; }
        #xlsRoot .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ═══ TABLA ═══ */
        #xlsRoot .xls-table { width: 100%; border-collapse: separate; border-spacing: 0; }

        #xlsRoot .xls-table th,
        #xlsRoot .xls-table td {
            border-right: 1px solid var(--grid);
            border-bottom: 1px solid var(--grid);
            padding: 2px 7px;
            height: 20px;
            vertical-align: middle;
            white-space: nowrap;
            background: #fff;
            font-size: 8px;              /* ← CELDAS 8px */
            font-weight: 400;
            color: var(--ink);
        }

        #xlsRoot .xls-table thead th {
            position: sticky; top: 0; z-index: 2;
            font-size: 9px;              /* ← ENCABEZADOS 9px */
            font-weight: 600;            /* sumiso: 600, no 700 */
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ink-3);
            background: var(--head);
        }

        #xlsRoot .xls-table td.row-index {
            position: sticky; left: 0; z-index: 1;
            background: var(--head); color: #d4dae2;
            font-size: 7.5px; font-weight: 400; text-align: center;
            width: 22px; min-width: 22px; padding: 0;
        }

        #xlsRoot .xls-table tbody tr:hover td { background: #fafcfe; }
        #xlsRoot .xls-table tbody tr:hover td.row-index { background: #f3f6fa; }

        /* llegada hoy: ámbar muy pálido */
        #xlsRoot .xls-table tbody tr.row-today td { background: #fffdf6; }
        #xlsRoot .xls-table tbody tr.row-today:hover td { background: #fffbea; }
        #xlsRoot .xls-table tbody tr.row-today:hover td.row-index { background: #faf3dd; }

        #xlsRoot .num { font-variant-numeric: tabular-nums; }
        #xlsRoot .sub { font-size: 7.5px; line-height: 1.25; color: var(--ink-4); margin-top: 0.5px; }
        #xlsRoot .strong { color: var(--ink-2); font-weight: 600; }

        #xlsRoot .dot {
            display: inline-block; width: 5px; height: 5px;
            border-radius: 50%; background: currentColor;
            margin-right: 4px; vertical-align: middle; opacity: .75;
        }

        /* ═══ Orden por columna ═══ */
        #xlsRoot th.sortable { cursor: pointer; user-select: none; }
        #xlsRoot th.sortable:hover { color: var(--ink-2); }
        #xlsRoot th .s-ind { font-size: 7.5px; opacity: .3; }
        #xlsRoot th.sortable[data-dir="asc"] .s-ind,
        #xlsRoot th.sortable[data-dir="desc"] .s-ind { opacity: .8; color: var(--accent); }

        /* ═══ Chip cuenta regresiva — pastel ═══ */
        #xlsRoot .cd {
            display: inline-block; padding: 0 5px;
            border-radius: 8px; font-size: 7.5px; font-weight: 600;
            border: 1px solid; letter-spacing: .02em; line-height: 12px;
        }
        #xlsRoot .cd-today  { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }
        #xlsRoot .cd-next   { color:#5d86a6; background:#f2f8fc; border-color:#dbe9f3; }
        #xlsRoot .cd-future { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
        #xlsRoot .cd-live   { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
        #xlsRoot .cd-done   { color:#c8d0d9; background:#fbfcfd; border-color:#f3f6f9; }

        /* ═══ KPIs — pálidos ═══ */
        #xlsRoot .kpi {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border: 1px solid var(--grid);
            border-radius: 4px; background: #fcfdfe; font-size: 8.5px;
            color: var(--ink-3);
        }
        #xlsRoot .kpi .kpi-l { color: #b3bcc7; font-weight: 600; font-size: 7.5px; letter-spacing: .05em; }
        #xlsRoot .kpi b { color: #5b6b7d; font-weight: 600; font-variant-numeric: tabular-nums; }
        #xlsRoot .kpi a { color: #b08a3e; font-size: 7.5px; font-weight: 600; text-decoration: none; }
        #xlsRoot .kpi a:hover { color: #8f6e2f; }

        /* ═══ Chips de estado — pastel ═══ */
        #xlsRoot .fchip {
            padding: 1px 8px; border-radius: 10px;
            font-size: 8.5px; font-weight: 500; white-space: nowrap;
            text-decoration: none; border: 1px solid var(--grid);
            color: var(--ink-4); background: #fcfdfe;
        }
        #xlsRoot .fchip:hover { color: var(--ink-2); border-color: var(--grid-2); text-decoration: none; }
        #xlsRoot .fchip.active {
            color: var(--accent); background: var(--accent-bg);
            border-color: #d5e7dc; font-weight: 600;
        }

        /* ═══ Botones — suaves ═══ */
        #xlsRoot .btn-mini {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
            font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px;
        }
        #xlsRoot .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }

        #xlsRoot .btn-green {
            background: var(--accent-bg); color: var(--accent); border: 1px solid #d5e7dc;
            font-size: 9px; font-weight: 600;
            padding: 2px 10px; border-radius: 3px;
        }
        #xlsRoot .btn-green:hover { background: #e3f1e9; color: #34684e; }

        /* ═══ Pestañas de hojas ═══ */
        #xlsRoot .sheet-tab {
            border: 0; background: transparent;
            font-size: 9px; color: var(--ink-4);
            padding: 3px 12px; border-right: 1px solid var(--grid);
            height: 22px; display: inline-flex; align-items: center; gap: 5px;
        }
        #xlsRoot .sheet-tab:hover { background: #f7fafc; color: var(--ink-2); }
        #xlsRoot .sheet-tab.active {
            background: #fff; color: var(--ink-2); font-weight: 600;
            box-shadow: inset 0 -2px 0 #b9d4c5;   /* verde clarito */
        }
        #xlsRoot .tab-dot { width: 4px; height: 4px; border-radius: 1.5px; opacity: .6; }
    </style>


    @php
        $listadoReservas = $reservas ?? [];

        $totalReg = $metrics['total_reservas']
            ?? (isset($reservas) && method_exists($reservas, 'total') ? $reservas->total() : count($listadoReservas));

        $sumPagina = 0; $countPagina = 0;
        foreach ($listadoReservas as $r) { $sumPagina += (float) ($r->monto_total ?? 0); $countPagina++; }

        $statusesList  = class_exists(\App\Models\Rsv\Status::class) ? \App\Models\Rsv\Status::all() : collect();
        $currentStatus = request('id_rsv_statuses');

        /* ═══════════════════════════════════════════════════════
           AGENDA DE HOY — consulta directa a BD
           ═══════════════════════════════════════════════════════ */
        $hoy         = now()->startOfDay();
        $agendaHoy   = collect();
        $agendaSrc   = 'pag';

        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            try {
                $agendaHoy = \App\Models\Rsv\Reserva::with(['user', 'inmueble', 'status'])
                    ->where(function ($q) use ($hoy) {
                        $q->whereDate('fecha_inicio', $hoy->toDateString())
                          ->orWhereDate('fecha_fin', $hoy->toDateString());
                    })
                    ->orderBy('fecha_inicio')
                    ->get()
                    ->flatMap(function ($r) use ($hoy) {
                        $out = collect();

                        $fi = $r->fecha_inicio ? \Illuminate\Support\Carbon::parse($r->fecha_inicio) : null;
                        $ff = $r->fecha_fin    ? \Illuminate\Support\Carbon::parse($r->fecha_fin)    : null;

                        if ($fi && $fi->isSameDay($hoy)) $out->push(['tipo' => 'LLEGADA', 'r' => $r]);
                        if ($ff && $ff->isSameDay($hoy)) $out->push(['tipo' => 'SALIDA',  'r' => $r]);

                        return $out;
                    });

                $agendaSrc = 'db';
            } catch (\Throwable $e) {
                $agendaHoy = collect();
            }
        }

        if ($agendaSrc === 'pag') {
            foreach ($listadoReservas as $r) {
                try {
                    $fi = $r->fecha_inicio ? \Illuminate\Support\Carbon::parse($r->fecha_inicio) : null;
                    $ff = $r->fecha_fin    ? \Illuminate\Support\Carbon::parse($r->fecha_fin)    : null;
                    if ($fi && $fi->isSameDay($hoy)) $agendaHoy->push(['tipo' => 'LLEGADA', 'r' => $r]);
                    if ($ff && $ff->isSameDay($hoy)) $agendaHoy->push(['tipo' => 'SALIDA',  'r' => $r]);
                } catch (\Throwable $e) { continue; }
            }
        }
    @endphp


    <!-- ═══ BARRA DE TÍTULO ═══ -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-2 py-1.5 border-bottom bg-light" style="border-color: #f1f5f9 !important;">

        <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-file-earmark-spreadsheet" style="font-size: .8rem; color: #b9d4c5;"></i>
            <span class="strong" style="font-size: 9.5px;">RESERVAS.XLSX</span>
            <span class="sub ms-1" style="margin: 0;">{{ $totalReg }} registros</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-mini d-inline-flex align-items-center gap-1">
                <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
            </button>
            <button type="button"
                    class="btn btn-green d-inline-flex align-items-center gap-1"
                    data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Reserva
            </button>
        </div>
    </div>


    <!-- ═══ KPIs MINI ═══ -->
    <div class="d-flex flex-wrap align-items-center gap-1.5 px-2 py-1.5 border-bottom bg-white" style="border-color: #f1f5f9 !important;">

        <span class="kpi">
            <span class="dot" style="color:#a3cfb6;"></span>
            <span class="kpi-l">ACTIVAS</span><b>{{ $metrics['activas'] ?? 0 }}</b>
        </span>

        <span class="kpi">
            <span class="dot" style="color:#a9cfe8;"></span>
            <span class="kpi-l">MES</span><b>${{ number_format($metrics['monto_total_mes'] ?? 0, 0) }}</b>
        </span>

        <span class="kpi">
            <span class="dot" style="color:#cdb4e4;"></span>
            <span class="kpi-l">OCUP.</span><b>{{ $metrics['porcentaje_ocupacion'] ?? '78%' }}</b>
        </span>

        <span class="kpi">
            <span class="dot" style="color:#ecd3a0;"></span>
            <span class="kpi-l">ENDOSOS</span><b>{{ $metrics['endosos_pendientes'] ?? 3 }}</b>
            <a href="#">Revisar →</a>
        </span>

    </div>


    <!-- ═══ BÚSQUEDA + FECHAS ═══ -->
    <form action="{{ url()->current() }}" method="GET" id="formFiltroReservas">

        @if(request('id_rsv_statuses'))
            <input type="hidden" name="id_rsv_statuses" value="{{ request('id_rsv_statuses') }}">
        @endif

        <div class="d-flex flex-wrap align-items-stretch border-bottom bg-white" style="border-color: #f1f5f9 !important;">

            <div class="d-flex align-items-center px-2 flex-grow-1" style="min-width: 170px;">
                <i class="bi bi-search me-2" style="font-size: 8.5px; color: #c3ccd6;"></i>
                <input type="text"
                       id="inputSearchReservas"
                       name="search"
                       class="form-control form-control-sm border-0 shadow-none p-0"
                       style="font-size: 9px; color: #64748b; box-shadow: none !important;"
                       placeholder="Código, titular, correo o inmueble…"
                       value="{{ request('search') }}"
                       autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-1 border-start px-2" style="border-color: #f1f5f9 !important;">
                <input type="date" name="fecha_desde"
                       class="form-control form-control-sm border-0 shadow-none"
                       style="font-size: 8.5px; color: #94a3b8; width: 100px; box-shadow: none !important;"
                       value="{{ request('fecha_desde') }}" title="Desde">
                <span style="font-size: 8.5px; color: #c3ccd6;">→</span>
                <input type="date" name="fecha_hasta"
                       class="form-control form-control-sm border-0 shadow-none"
                       style="font-size: 8.5px; color: #94a3b8; width: 100px; box-shadow: none !important;"
                       value="{{ request('fecha_hasta') }}" title="Hasta">
                <button type="submit" class="btn btn-mini py-0 px-1.5" title="Aplicar filtros">
                    <i class="bi bi-chevron-right" style="font-size: 8px;"></i>
                </button>
            </div>

        </div>
    </form>


    <!-- ═══ CHIPS DE ESTADO ═══ -->
    <div class="d-flex align-items-center gap-1 px-2 py-1 border-bottom bg-white no-scrollbar" style="overflow-x: auto; border-color: #f1f5f9 !important;">

        <span class="me-1" style="font-size: 7.5px; letter-spacing: .05em; color: #b3bcc7; font-weight: 600;">
            <i class="bi bi-funnel-fill me-1" style="font-size: 7.5px;"></i>ESTADO
        </span>

        <a href="{{ request()->fullUrlWithQuery(['id_rsv_statuses' => null, 'page' => null]) }}"
           class="fchip {{ is_null($currentStatus) ? 'active' : '' }}">Todas</a>

        @foreach($statusesList as $st)
            <a href="{{ request()->fullUrlWithQuery(['id_rsv_statuses' => $st->id, 'page' => null]) }}"
               class="fchip {{ ($currentStatus == $st->id) ? 'active' : '' }}">{{ $st->name }}</a>
        @endforeach

    </div>


    <!-- ═══════════════════════════════════════════ -->
    <!-- HOJA 1 · RESERVAS                            -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="sheet-reservas" class="sheet-pane">

        <div class="overflow-auto" style="max-height: 56vh;">

            <table class="xls-table" style="min-width: 820px;">

                <thead>
                    <tr>
                        <th style="width: 22px; min-width: 22px;"></th>
                        <th>Código</th>
                        <th>Titular</th>
                        <th>Inmueble</th>
                        <th class="sortable" data-sort="ini">Check-in <i class="bi bi-arrow-down-up s-ind"></i></th>
                        <th>Check-out</th>
                        <th class="text-center">N</th>
                        <th class="text-end sortable" data-sort="monto">Monto <i class="bi bi-arrow-down-up s-ind"></i></th>
                        <th>Estado</th>
                        <th class="text-end" style="padding-right: 8px;">·</th>
                    </tr>
                </thead>

                <tbody id="tbodyReservas">

                    @forelse($listadoReservas as $reserva)

                        @php
                            $hoy    = $hoy ?? now()->startOfDay();
                            $inicio = $reserva->fecha_inicio;
                            $fin    = $reserva->fecha_fin;

                            $noches = ($inicio && $fin)
                                ? (int) abs(\Illuminate\Support\Carbon::parse($inicio)->startOfDay()->diffInDays(\Illuminate\Support\Carbon::parse($fin)->startOfDay()))
                                : null;

                            try {
                                $fi = $inicio ? \Illuminate\Support\Carbon::parse($inicio) : null;
                            } catch (\Throwable $e) { $fi = null; }

                            $diasParaLlegada = $fi ? (int) floor($fi->startOfDay()->diffInDays($hoy, false)) : null;

                            $enCurso = false; $pct = 0;
                            try {
                                if ($fi && $fin) {
                                    $fi0 = $fi->copy()->startOfDay();
                                    $ff0 = \Illuminate\Support\Carbon::parse($fin)->startOfDay();
                                    $enCurso = $hoy->betweenIncluded($fi0, $ff0);
                                    if ($enCurso && $noches > 0) {
                                        $pct = (int) min(100, max(0, ($fi0->diffInDays($hoy) / $noches) * 100));
                                    }
                                }
                            } catch (\Throwable $e) {}

                            if ($enCurso)                                              { $cd = ['EN CURSO', 'cd-live']; }
                            elseif ($diasParaLlegada === 0)                            { $cd = ['HOY', 'cd-today']; }
                            elseif ($diasParaLlegada === 1)                            { $cd = ['MAÑANA', 'cd-next']; }
                            elseif ($diasParaLlegada !== null && $diasParaLlegada > 1) { $cd = ['+'.$diasParaLlegada.'d', 'cd-future']; }
                            elseif ($diasParaLlegada !== null && $diasParaLlegada < 0) { $cd = ['FIN', 'cd-done']; }
                            else                                                        { $cd = ['—', 'cd-done']; }

                            $llegaHoy = ($diasParaLlegada === 0);

                            $stName = optional($reserva->status)->name ?? 'Pendiente';
                            $s = strtolower($stName);
                            $dot = 'text-secondary';
                            if (str_contains($s, 'confirm') || str_contains($s, 'activ'))      { $dot = 'text-success'; }
                            elseif (str_contains($s, 'pendien'))                                { $dot = 'text-warning'; }
                            elseif (str_contains($s, 'cancela') || str_contains($s, 'rechaz'))  { $dot = 'text-danger'; }
                        @endphp

                        <tr class="reserva-row {{ $llegaHoy ? 'row-today' : '' }}">

                            <td class="row-index">{{ $loop->iteration }}</td>

                            <!-- Código -->
                            <td class="strong">#{{ $reserva->codigo_reserva }}</td>

                            <!-- Titular -->
                            <td>
                                {{ optional($reserva->user)->name ?? 'Sin asignar' }}
                                <div class="sub">{{ optional($reserva->user)->email ?? '—' }}</div>
                            </td>

                            <!-- Inmueble -->
                            <td>
                                <i class="bi bi-house-door me-1" style="font-size: 7.5px; color: #c3ccd6;"></i>{{ optional($reserva->inmueble)->name ?? 'N/D' }}
                            </td>

                            <!-- Check-in + cuenta regresiva -->
                            <td class="num" data-ini="{{ $fi ? $fi->timestamp : 0 }}">
                                {{ $fi ? $fi->format('d/m') : '—' }}
                                <span class="cd {{ $cd[1] }} ms-1">{{ $cd[0] }}</span>
                                @if($enCurso)
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        <span style="display:inline-block;width:38px;height:2.5px;background:#f1f5f9;border-radius:2px;overflow:hidden;">
                                            <span style="display:block;height:100%;width:{{ $pct }}%;background:#b9d4c5;"></span>
                                        </span>
                                        <span class="sub" style="margin:0;">{{ $pct }}%</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Check-out -->
                            <td class="num" style="color: #a8b3c1;">
                                @if($fin)
                                    @try {{ \Illuminate\Support\Carbon::parse($fin)->format('d/m/y') }} @catch(\Throwable $e) — @endtry
                                @else — @endif
                            </td>

                            <!-- Noches -->
                            <td class="text-center num">{{ $noches ?? '—' }}</td>

                            <!-- Monto -->
                            <td class="text-end num strong" data-monto="{{ (float) ($reserva->monto_total ?? 0) }}">
                                ${{ number_format($reserva->monto_total ?? 0, 2) }}
                            </td>

                            <!-- Estado -->
                            <td><span class="dot {{ $dot }}"></span>{{ strtoupper($stName) }}</td>

                            <!-- Acción -->
                            <td class="text-end" style="padding-right: 8px;">
                                <button type="button" class="btn-mini">Detalle</button>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td class="row-index">1</td>
                            <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                No hay reservas registradas para este criterio.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Paginación -->
        @if(isset($reservas) && method_exists($reservas, 'hasPages') && $reservas->hasPages())
            <div class="px-2 py-1 border-top bg-white d-flex justify-content-between align-items-center"
                 style="font-size: 8.5px; color: #b3bcc7; border-color: #f1f5f9 !important;">

                <span>{{ $reservas->firstItem() }}–{{ $reservas->lastItem() }} de {{ $reservas->total() }}</span>
                <div>{{ $reservas->appends(request()->query())->links() }}</div>

            </div>
        @endif

    </div>


    <!-- ═══════════════════════════════════════════ -->
    <!-- HOJA 2 · AGENDA DE HOY (BD real)             -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="sheet-agenda" class="sheet-pane d-none">

        <div class="d-flex align-items-center justify-content-between px-2 py-1 border-bottom bg-light" style="font-size: 8.5px; border-color: #f1f5f9 !important;">

            <span style="color: #a8b3c1;">
                <i class="bi bi-calendar-day me-1"></i>
                {{ now()->translatedFormat('l d/m') }}
                <span class="ms-1 opacity-75">
                    ({{ $agendaSrc === 'db' ? 'todo el sistema' : 'página actual' }})
                </span>
            </span>

            <span>
                <span class="me-2" style="color:#5d8a70; font-weight: 600;">
                    <i class="bi bi-arrow-down-right"></i> {{ $agendaHoy->where('tipo', 'LLEGADA')->count() }} llegadas
                </span>
                <span style="color:#c08484; font-weight: 600;">
                    <i class="bi bi-arrow-up-right"></i> {{ $agendaHoy->where('tipo', 'SALIDA')->count() }} salidas
                </span>
            </span>

        </div>

        <div class="overflow-auto" style="max-height: 56vh;">

            <table class="xls-table" style="min-width: 580px;">

                <thead>
                    <tr>
                        <th style="width: 22px; min-width: 22px;"></th>
                        <th>Tipo</th>
                        <th>Código</th>
                        <th>Titular</th>
                        <th>Inmueble</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($agendaHoy as $item)

                        @php
                            $r = $item['r'];
                            $esLlegada = ($item['tipo'] === 'LLEGADA');
                            $stName = optional($r->status)->name ?? 'Pendiente';
                            $s = strtolower($stName);
                            $dot = 'text-secondary';
                            if (str_contains($s, 'confirm') || str_contains($s, 'activ'))     { $dot = 'text-success'; }
                            elseif (str_contains($s, 'pendien'))                               { $dot = 'text-warning'; }
                            elseif (str_contains($s, 'cancela') || str_contains($s, 'rechaz')) { $dot = 'text-danger'; }
                        @endphp

                        <tr class="{{ $esLlegada ? 'row-today' : '' }}">

                            <td class="row-index">{{ $loop->iteration }}</td>

                            <td style="font-size: 8px; font-weight: 600; color: {{ $esLlegada ? '#5d8a70' : '#c08484' }};">
                                {{ $item['tipo'] }}
                            </td>

                            <td class="strong">#{{ $r->codigo_reserva }}</td>

                            <td>{{ optional($r->user)->name ?? 'Sin asignar' }}</td>

                            <td>{{ optional($r->inmueble)->name ?? 'N/D' }}</td>

                            <td class="num">
                                @php
                                    $fh = $esLlegada ? $r->fecha_inicio : $r->fecha_fin;
                                @endphp
                                @if($fh)
                                    @try {{ \Illuminate\Support\Carbon::parse($fh)->format('H:i') }} @catch(\Throwable $e) — @endtry
                                @else — @endif
                            </td>

                            <td><span class="dot {{ $dot }}"></span>{{ strtoupper($stName) }}</td>

                        </tr>

                    @empty

                        <tr>
                            <td class="row-index">1</td>
                            <td colspan="6" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-sun me-1"></i>
                                Sin llegadas ni salidas para hoy.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- ═══════════════════════════════════════════ -->
    <!-- HOJA 3 · RESUMEN                             -->
    <!-- ═══════════════════════════════════════════ -->
    <div id="sheet-resumen" class="sheet-pane d-none">

        <div class="overflow-auto" style="max-height: 56vh;">

            <table class="xls-table" style="min-width: 400px;">

                <thead>
                    <tr>
                        <th style="width: 22px; min-width: 22px;"></th>
                        <th>Métrica</th>
                        <th class="text-end">Valor</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td class="row-index">1</td>
                        <td>Total reservas</td>
                        <td class="text-end num strong">{{ $totalReg }}</td>
                    </tr>
                    <tr>
                        <td class="row-index">2</td>
                        <td>Activas hoy</td>
                        <td class="text-end num strong" style="color: #5d8a70;">{{ $metrics['activas'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td class="row-index">3</td>
                        <td>Estimado del mes</td>
                        <td class="text-end num strong">${{ number_format($metrics['monto_total_mes'] ?? 0, 0) }}</td>
                    </tr>
                    <tr>
                        <td class="row-index">4</td>
                        <td>Ocupación</td>
                        <td class="text-end num strong">{{ $metrics['porcentaje_ocupacion'] ?? '78%' }}</td>
                    </tr>
                    <tr>
                        <td class="row-index">5</td>
                        <td>Endosos pendientes</td>
                        <td class="text-end num strong" style="color: #b08a3e;">{{ $metrics['endosos_pendientes'] ?? 3 }}</td>
                    </tr>
                    <tr>
                        <td class="row-index">6</td>
                        <td>Σ Monto (página)</td>
                        <td class="text-end num strong" style="color: #5d8a70;">${{ number_format($sumPagina, 2) }}</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ═══ PESTAÑAS DE HOJAS ═══ -->
    <div class="d-flex align-items-center border-top bg-light px-1" style="height: 22px; border-color: #f1f5f9 !important;">

        <button type="button" class="sheet-tab active" data-sheet="reservas">
            <span class="tab-dot" style="background:#a3cfb6;"></span> Reservas
        </button>

        <button type="button" class="sheet-tab" data-sheet="agenda">
            <span class="tab-dot" style="background:#a9cfe8;"></span> Agenda de hoy
        </button>

        <button type="button" class="sheet-tab" data-sheet="resumen">
            <span class="tab-dot" style="background:#cdb4e4;"></span> Resumen
        </button>

    </div>


    <!-- ═══ BARRA DE ESTADO ═══ -->
    <div class="d-flex align-items-center justify-content-between border-top bg-white px-2"
         style="height: 18px; font-size: 8.5px; color: #c3ccd6; border-color: #f1f5f9 !important;">

        <span>Listo</span>

        <div class="d-flex gap-3">
            <span>Visibles: <b class="strong" id="statVisibles">{{ $countPagina }}</b></span>
            <span>Σ: <b class="strong" style="color: #5d8a70;" id="statSuma">${{ number_format($sumPagina, 2) }}</b></span>
        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        var tabs  = document.querySelectorAll('#xlsRoot .sheet-tab');
        var panes = document.querySelectorAll('#xlsRoot .sheet-pane');

        /* ── Hojas ── */
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');
                panes.forEach(function (p) { p.classList.add('d-none'); });
                var pane = document.getElementById('sheet-' + tab.dataset.sheet);
                if (pane) pane.classList.remove('d-none');
            });
        });


        /* ── Búsqueda instantánea + Σ ── */
        var search  = document.getElementById('inputSearchReservas');
        var tbody   = document.getElementById('tbodyReservas');
        var statVis = document.getElementById('statVisibles');
        var statSum = document.getElementById('statSuma');

        function fmt(n) {
            return '$' + Number(n).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        if (search && tbody) {
            var rowsArr = Array.prototype.slice.call(tbody.querySelectorAll('.reserva-row'));

            search.addEventListener('input', function () {
                var q = this.value.toLowerCase().trim();
                var vis = 0, sum = 0;

                rowsArr.forEach(function (row) {
                    var show = row.textContent.toLowerCase().includes(q);
                    row.style.display = show ? '' : 'none';

                    if (show) {
                        vis++;
                        var m = row.querySelector('[data-monto]');
                        if (m) sum += parseFloat(m.dataset.monto || 0);
                    }
                });

                if (statVis) statVis.textContent = vis;
                if (statSum) statSum.textContent = fmt(sum);
            });
        }


        /* ── Ordenar por columna ── */
        document.querySelectorAll('#xlsRoot th.sortable').forEach(function (th) {

            th.addEventListener('click', function () {
                if (!tbody) return;

                var attr = th.dataset.sort;
                var dir  = th.dataset.dir === 'asc' ? 'desc' : 'asc';
                th.dataset.dir = dir;

                document.querySelectorAll('#xlsRoot th.sortable').forEach(function (o) {
                    if (o !== th) o.dataset.dir = '';
                });

                var rows = Array.prototype.slice.call(tbody.querySelectorAll('.reserva-row'));

                rows.sort(function (a, b) {
                    var ca = a.querySelector('[data-' + attr + ']');
                    var cb = b.querySelector('[data-' + attr + ']');
                    var x = ca ? parseFloat(ca.dataset[attr]) || 0 : 0;
                    var y = cb ? parseFloat(cb.dataset[attr]) || 0 : 0;
                    return dir === 'asc' ? x - y : y - x;
                });

                rows.forEach(function (r, i) {
                    tbody.appendChild(r);
                    var ri = r.querySelector('.row-index');
                    if (ri) ri.textContent = i + 1;
                });
            });

        });

    });
</script>
