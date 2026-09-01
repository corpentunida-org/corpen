{{--
|--------------------------------------------------------------------------
| Vista: certificados/ingesta/index.blade.php
|--------------------------------------------------------------------------
| Propósito : Visor y gestor de lotes de ingesta ERP (Staging).
|--------------------------------------------------------------------------
--}}
<x-base-layout>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            integrity="sha256-oVuCFqsKIbRHvGQXDhRaBEJ9oMH2DhJCj2wr7KpBbSA="
            crossorigin="anonymous">
    </script>

    <style>
        /* ── Tokens ───────────────────────────────────────────────── */
        :root {
            --c-primary      : #4f46e5;
            --c-primary-h    : #4338ca;
            --c-primary-soft : #e0e7ff;
            --c-success      : #10b981;
            --c-success-soft : #d1fae5;
            --c-danger       : #ef4444;
            --c-danger-soft  : #fee2e2;
            --c-warning      : #f59e0b;
            --c-warning-soft : #fef3c7;
            --c-surface      : #ffffff;
            --c-bg           : #f8fafc;
            --c-border       : #e2e8f0;
            --c-text         : #0f172a;
            --c-muted        : #64748b;
            --r-xl : 20px;
            --r-lg : 14px;
            --r-md :  8px;
            --shadow-sm : 0 1px 3px rgba(0,0,0,.06);
            --shadow-md : 0 4px 16px rgba(0,0,0,.08);
            --shadow-lg : 0 12px 32px rgba(0,0,0,.10);
        }

        /* ── Tooltips Personalizados (Hacia abajo y por encima de todo) ── */
        [data-tooltip] {
            position: relative;
            cursor: help;
        }
        [data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) translateY(0);
            background: #0f172a;
            color: #ffffff;
            padding: 0.6rem 0.85rem;
            border-radius: var(--r-md);
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.4;
            white-space: normal;
            width: max-content;
            max-width: 260px;
            text-align: center;
            z-index: 999999 !important;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }
        [data-tooltip]::before {
            content: '';
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) translateY(0);
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent #0f172a transparent;
            margin-top: -12px;
            z-index: 999999 !important;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }
        [data-tooltip]:hover::after,
        [data-tooltip]:hover::before {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(5px);
        }

        /* ── Base ─────────────────────────────────────────────────── */
        .page-wrap { font-family: 'Inter', system-ui, sans-serif; background: var(--c-bg); }
        .card-g {
            background   : var(--c-surface);
            border       : 1px solid var(--c-border);
            border-radius: var(--r-xl);
            box-shadow   : var(--shadow-sm);
        }

        /* ── KPI ──────────────────────────────────────────────────── */
        .kpi-card {
            background   : var(--c-surface);
            border       : 1px solid var(--c-border);
            border-radius: var(--r-xl);
            padding      : 1.25rem 1.5rem;
            box-shadow   : var(--shadow-sm);
            transition   : box-shadow .2s, transform .2s;
            position     : relative;
            overflow     : hidden;
        }
        .kpi-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .kpi-card::before {
            content      : '';
            position     : absolute;
            top: 0; left: 0;
            width        : 4px;
            height       : 100%;
            border-radius: 4px 0 0 4px;
        }
        .kpi-card.kpi-total::before   { background: #94a3b8; }
        .kpi-card.kpi-bloque::before  { background: var(--c-primary); }
        .kpi-card.kpi-pend::before    { background: var(--c-warning); }
        .kpi-card.kpi-capital::before { background: var(--c-success); }

        .kpi-icon {
            width: 44px; height: 44px;
            border-radius: var(--r-lg);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .kpi-number {
            font-size  : 1.65rem;
            font-weight: 800;
            line-height: 1;
            color      : var(--c-text);
            font-variant-numeric: tabular-nums;
        }
        .kpi-trend {
            font-size  : .72rem;
            font-weight: 600;
            padding    : .2rem .55rem;
            border-radius: 9999px;
            display    : inline-flex;
            align-items: center;
            gap        : .25rem;
        }
        .trend-up   { background: var(--c-success-soft); color: #065f46; }
        .trend-down { background: var(--c-danger-soft);  color: #991b1b; }
        .trend-neu  { background: var(--c-primary-soft); color: #3730a3; }

        /* ── Badges ───────────────────────────────────────────────── */
        .badge-g {
            display     : inline-flex; align-items: center; gap: .3rem;
            padding     : .28rem .75rem;
            border-radius: 9999px;
            font-size   : .73rem; font-weight: 700;
            white-space : nowrap;
        }
        .badge-ok     { background: var(--c-success-soft); color: #065f46; }
        .badge-warn   { background: var(--c-warning-soft);  color: #92400e; }
        .badge-danger { background: var(--c-danger-soft);   color: #991b1b; }
        .badge-info   { background: var(--c-primary-soft);  color: #3730a3; }

        /* ── Botones ──────────────────────────────────────────────── */
        .btn-g {
            border-radius: var(--r-lg);
            font-weight  : 600; font-size: .88rem;
            padding      : .55rem 1.2rem;
            display      : inline-flex; align-items: center; gap: .45rem;
            border       : none; cursor: pointer;
            transition   : all .2s ease;
            text-decoration: none; white-space: nowrap;
        }
        .btn-primary-g {
            background: var(--c-primary); color: #fff;
            box-shadow: 0 4px 14px rgba(79,70,229,.35);
        }
        .btn-primary-g:hover {
            background: var(--c-primary-h); color: #fff;
            box-shadow: 0 6px 20px rgba(79,70,229,.45);
            transform : translateY(-1px);
        }
        .btn-outline-g {
            background: #fff; color: var(--c-text);
            border: 1px solid var(--c-border);
        }
        .btn-outline-g:hover {
            background  : var(--c-primary-soft);
            color       : var(--c-primary);
            border-color: var(--c-primary-soft);
        }
        .btn-icon-round {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: var(--c-surface); border: 1px solid var(--c-border);
            color: var(--c-muted); transition: all .3s; text-decoration: none;
            flex-shrink: 0;
        }
        .btn-icon-round:hover {
            background: var(--c-primary-soft); color: var(--c-primary);
            border-color: var(--c-primary-soft);
        }
        .btn-icon-round:hover .spin-on-hover { transform: rotate(180deg); }
        .spin-on-hover { transition: transform .4s; display: inline-block; }

        /* ── Selectores chip ──────────────────────────────────────── */
        .select-chip {
            appearance: none; -webkit-appearance: none;
            background: #f1f5f9
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E")
                no-repeat right .9rem center / 1em;
            border: 1px solid var(--c-border); border-radius: 9999px;
            padding: .5rem 2.4rem .5rem 1.1rem;
            font-size: .87rem; font-weight: 600; color: #475569;
            cursor: pointer; transition: all .2s;
        }
        .select-chip:hover   { background-color: #e9edf2; border-color: #cbd5e1; }
        .select-chip:focus   {
            outline: none; border-color: var(--c-primary);
            box-shadow: 0 0 0 3px var(--c-primary-soft); background-color: #fff;
        }

        /* ── Input ────────────────────────────────────────────────── */
        .input-g {
            background: #f8fafc; border: 1px solid var(--c-border);
            border-radius: var(--r-lg); padding: .5rem 1rem;
            font-size: .88rem; color: var(--c-text);
            transition: border-color .2s, box-shadow .2s;
        }
        .input-g:focus {
            outline: none; border-color: var(--c-primary);
            box-shadow: 0 0 0 3px var(--c-primary-soft); background: #fff;
        }

        /* ── BARRA DE ACCIÓN ──────────────────────────────────────── */
        .action-bar {
            background   : var(--c-surface);
            border       : 1px solid var(--c-border);
            border-radius: var(--r-xl);
            box-shadow   : var(--shadow-md);
            overflow     : hidden;
        }
        .action-bar-progress {
            height: 4px;
            background: var(--c-border);
            position: relative;
            overflow: hidden;
        }
        .action-bar-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--c-primary), #818cf8);
            transition: width 1s ease;
            border-radius: 0 4px 4px 0;
        }
        .action-bar-progress-fill::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.5), transparent);
            animation: shimmer 2.5s infinite;
        }
        @keyframes shimmer { to { left: 160%; } }

        .action-bar-body {
            padding: 1.1rem 1.5rem;
            display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;
        }

        /* ── TOGGLE TABLA ─────────────────────────────────────────── */
        .table-toggle-btn {
            display     : flex; align-items : center; gap: .5rem;
            background  : none; border: none; font-weight : 700;
            font-size   : .88rem; color: var(--c-text); cursor: pointer;
            padding     : .4rem .75rem; border-radius: var(--r-md);
            transition  : background .2s; white-space : nowrap;
        }
        .table-toggle-btn:hover { background: var(--c-bg); }
        .toggle-arrow {
            width: 22px; height: 22px; border-radius: 50%; background: var(--c-bg);
            border: 1px solid var(--c-border); display: flex; align-items: center;
            justify-content: center; font-size: .7rem; color: var(--c-muted);
            transition: transform .35s ease, background .2s; flex-shrink: 0;
        }
        .table-toggle-btn[aria-expanded="true"] .toggle-arrow {
            transform : rotate(180deg); background: var(--c-primary-soft);
            border-color: var(--c-primary-soft); color: var(--c-primary);
        }
        .collapsible-table {
            overflow    : hidden;
            transition  : max-height .45s cubic-bezier(.4,0,.2,1), opacity .35s ease;
            max-height  : 0; opacity: 0;
        }
        .collapsible-table.is-open { max-height: 9999px; opacity: 1; }

        /* ── Tabla ────────────────────────────────────────────────── */
        .tbl th {
            font-size: .7rem; font-weight: 700; color: var(--c-muted);
            text-transform: uppercase; letter-spacing: .06em;
            background: #f8fafc; padding: .9rem 1rem; border-bottom: 2px solid var(--c-border);
        }
        .tbl td {
            padding: .8rem 1rem; vertical-align: middle;
            border-bottom: 1px solid var(--c-border); color: var(--c-text); font-size: .875rem;
        }
        .tbl tbody tr { transition: background .12s; }
        .tbl tbody tr:hover { background: #fafbff; }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* ── Drop zone ────────────────────────────────────────────── */
        .drop-zone {
            border: 2px dashed var(--c-border); border-radius: var(--r-lg);
            padding: 1.4rem 1rem; text-align: center;
            transition: all .25s ease; background: #fff; cursor: pointer;
        }
        .drop-zone.is-over  { border-color: var(--c-primary); background: var(--c-primary-soft); transform: scale(1.01); }
        .drop-zone.is-ready { border-color: var(--c-success); background: var(--c-success-soft); }

        /* ── Elementos y Utilidades ───────────────────────────────── */
        .pulse-dot {
            width: 10px; height: 10px; background: var(--c-primary);
            border-radius: 50%; display: inline-block;
            animation: pulse-anim 2s infinite; flex-shrink: 0;
        }
        @keyframes pulse-anim {
            0%   { box-shadow: 0 0 0 0   rgba(79,70,229,.5); }
            70%  { box-shadow: 0 0 0 10px rgba(79,70,229,0); }
            100% { box-shadow: 0 0 0 0   rgba(79,70,229,0); }
        }
        .btn-spinner {
            display: none; width: 1rem; height: 1rem; border: 2px solid rgba(255,255,255,.35);
            border-top-color: #fff; border-radius: 50%; animation: spin .75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-g.is-loading .btn-label, .btn-g.is-loading .btn-ico   { display: none; }
        .btn-g.is-loading .btn-spinner { display: inline-block; }
        .alert-g {
            display: flex; align-items: center; gap: .75rem; border: none; border-radius: var(--r-lg);
            padding: .9rem 1.25rem; font-size: .875rem; font-weight: 500; box-shadow: var(--shadow-sm);
        }
        .alert-ok   { background: #f0fdf4; color: #166534; }
        .alert-err  { background: #fef2f2; color: #991b1b; }
        .alert-warn { background: #fffbeb; color: #92400e; }
        .page-link {
            border-radius: var(--r-md) !important; margin: 0 2px;
            font-size: .83rem; color: var(--c-text); border-color: var(--c-border);
        }
        .page-item.active .page-link { background: var(--c-primary); border-color: var(--c-primary); }
        .vr-g { width: 1px; height: 36px; background: var(--c-border); flex-shrink: 0; }
        .stat-chip {
            display: inline-flex; align-items: center; gap: .4rem; background : var(--c-bg);
            border: 1px solid var(--c-border); border-radius: 9999px; padding: .35rem .85rem;
            font-size: .78rem; font-weight: 600; color: var(--c-muted);
        }
        .stat-chip .dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .table-header-strip {
            display: flex; align-items: center; justify-content: space-between;
            padding: .9rem 1.25rem; background: #f8fafc; border-bottom: 1px solid var(--c-border);
            flex-wrap: wrap; gap: .75rem;
        }

        /* ── SIDEBAR FIJO & ANIMACIÓN FLIP ────────────────────────── */
        .sticky-sidebar {
            position: sticky;
            top: 1.5rem;
            max-height: calc(100vh - 3rem);
            display: flex;
            flex-direction: column;
            min-height: 550px;
        }

        .flip-wrapper {
            perspective: 1200px;
            position: relative;
            flex-grow: 1;
            width: 100%;
        }

        .flip-card {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }

        .flip-card.is-flipped { transform: rotateY(180deg); }

        .flip-face {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex;
            flex-direction: column;
            background: var(--c-surface);
        }

        .flip-front { z-index: 2; transform: rotateY(0deg); }
        .flip-back { transform: rotateY(180deg); z-index: 1; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: var(--c-border); border-radius: 10px; }

        /* ── ENLACES DE BLOQUE SIDEBAR ────────────────────────────── */
        .block-link {
            background: transparent;
            color: var(--c-text);
            transition: all 0.2s;
        }
        .block-link:hover:not(.active-block) {
            background: var(--c-primary-soft);
            color: var(--c-primary);
        }
        .block-link.active-block {
            background: var(--c-primary);
            color: #fff;
            box-shadow: 0 2px 4px rgba(79,70,229, 0.3);
        }
        .block-link .ico-cube {
            color: var(--c-muted);
        }
        .block-link:hover:not(.active-block) .ico-cube {
            color: var(--c-primary);
        }
        .block-link.active-block .ico-cube {
            color: #fff;
        }

        /* ── Responsive ───────────────────────────────────────────── */
        @media (max-width: 767px) {
            .hide-sm { display: none !important; }
            .action-bar-body { gap: .75rem; }
            .kpi-number { font-size: 1.35rem; }
            .sticky-sidebar { position: static; min-height: auto; }
            .flip-wrapper { min-height: 500px; }
        }
    </style>

    <div class="page-wrap py-4" style="min-height:100vh;">
        <div class="container-fluid px-xl-4">
            <div class="row g-4 m-0">

                {{-- COLUMNA PRINCIPAL (CONTENIDO) --}}
                <div class="col-12 col-xl-9">

                    {{-- CABECERA --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center shadow-sm"
                                 style="width:54px;height:54px;border-radius:var(--r-lg);background:var(--c-primary-soft);">
                                <i class="fas fa-layer-group fs-4" style="color:var(--c-primary);"></i>
                            </div>
                            <div>
                                <h1 class="h4 fw-bold m-0 text-dark d-flex align-items-center gap-2">
                                    Carga de datos
                                    <span data-tooltip="Sistema que reúne y organiza toda la información del mes en un solo lugar."
                                          style="text-decoration: underline dotted var(--c-muted); text-underline-offset: 4px;">
                                        ERP
                                    </span>
                                    <span class="badge-g badge-info"
                                          data-tooltip="Entorno del analista: Espacio para revisar y comprobar los meses antes de cargarlos al sistema."
                                          style="font-size:.63rem;">
                                        Analista
                                    </span>
                                </h1>
                                <p class="mb-0 mt-1" style="color:var(--c-muted);font-size:.85rem;">
                                    Gestión de lotes de carga por bloque API
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="{{ request()->fullUrl() }}" class="btn-icon-round shadow-sm" title="Actualizar vista">
                                <i class="fas fa-sync-alt spin-on-hover"></i>
                            </a>

                            <a href="{{ route('certificados.operaciones.index') }}" class="btn-g btn-outline-g shadow-sm">
                                <i class="fas fa-list-ul" style="color:var(--c-primary);"></i>
                                Ver Operaciones
                            </a>

                            @if($bloquesDisponibles->count() > 0)
                            <form action="{{ route('certificados.ingesta.index') }}" method="GET"
                                  class="d-flex align-items-center gap-2 bg-white border rounded-pill px-3 py-2 shadow-sm">
                                @if(request('buscar_cedula')) <input type="hidden" name="buscar_cedula" value="{{ request('buscar_cedula') }}"> @endif
                                @if(request('estado'))        <input type="hidden" name="estado"        value="{{ request('estado') }}"> @endif

                                <i class="fas fa-layer-group" style="color:var(--c-primary);font-size:.85rem;"></i>
                                <select name="bloque"
                                        class="border-0 bg-transparent fw-bold shadow-none"
                                        style="color:var(--c-primary);cursor:pointer;outline:none;font-size:.9rem;"
                                        onchange="this.form.submit()"
                                        aria-label="Seleccionar bloque">
                                    @foreach($bloquesDisponibles as $b)
                                        <option value="{{ $b }}" {{ $bloqueActivo == $b ? 'selected' : '' }}>
                                            API-{{ str_pad($b, 4, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                        </div>
                    </div>

                    {{-- ALERTAS FLASH --}}
                    @if(session('success'))
                        <div class="alert-g alert-ok mb-4" role="alert">
                            <i class="fas fa-check-circle fs-5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert-g alert-err mb-4" role="alert">
                            <i class="fas fa-times-circle fs-5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="alert-g alert-warn mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle fs-5"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                    @endif

                    {{-- KPI CARDS --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="kpi-card kpi-total h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="kpi-icon" style="background:#f1f5f9;color:var(--c-muted);">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <span class="kpi-trend trend-neu">
                                        <i class="fas fa-globe" style="font-size:.6rem;"></i> Global
                                    </span>
                                </div>
                                <div class="kpi-number">{{ number_format($kpi['total_registros'] ?? 0, 0, ',', '.') }}</div>
                                <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                                    Total Facturas
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="kpi-card kpi-bloque h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="kpi-icon" style="background:var(--c-primary-soft);color:var(--c-primary);">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <span class="kpi-trend trend-neu">
                                        <i class="fas fa-dot-circle" style="font-size:.6rem;"></i> Activo
                                    </span>
                                </div>
                                <div class="kpi-number" style="font-family:monospace;">
                                    #{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                                    Bloque Activo
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="kpi-card kpi-pend h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="kpi-icon" style="background:var(--c-warning-soft);color:#b45309;">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    @php
                                        $pct = isset($kpi['total_registros']) && $kpi['total_registros'] > 0
                                            ? round((($kpi['total_registros'] - $kpi['pendientes']) / $kpi['total_registros']) * 100)
                                            : 0;
                                    @endphp
                                    <span class="kpi-trend" style="background:var(--c-warning-soft);color:#92400e;">
                                        {{ $pct }}% listo
                                    </span>
                                </div>
                                <div class="kpi-number">{{ number_format($kpi['pendientes'] ?? 0, 0, ',', '.') }}</div>
                                <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                                    Clientes pendientes
                                </div>
                                <div class="mt-2" style="height:3px;background:var(--c-border);border-radius:9999px;overflow:hidden;">
                                    <div style="width:{{ $pct }}%;height:100%;background:var(--c-warning);border-radius:9999px;transition:width 1s ease;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3">
                            <div class="kpi-card kpi-capital h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="kpi-icon" style="background:var(--c-success-soft);color:#047857;">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <span class="kpi-trend trend-up">
                                        <i class="fas fa-arrow-up" style="font-size:.55rem;"></i> Capital
                                    </span>
                                </div>
                                <div class="kpi-number" style="font-size:1.3rem;">
                                    ${{ number_format($kpi['valor_pendiente'] ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                                    Valor Pendiente
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GRÁFICO + CARGA DE ARCHIVO --}}
                    <div class="row g-3 mb-4">
                        {{-- Gráfico Columnas --}}
                        <div class="col-12 col-lg-4">
                            <div class="card-g p-4 h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="fas fa-chart-bar me-2 text-muted"></i>Estado del Bloque
                                        </h6>
                                        <p class="mb-0 mt-1" style="font-size:.78rem;color:var(--c-muted);">
                                            Clic en la columna para filtrar la tabla
                                        </p>
                                    </div>
                                </div>
                                <div style="position:relative;height:200px;flex:1;">
                                    <canvas id="kpiChart" aria-label="Gráfico de estado"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Upload zona --}}
                        <div class="col-12 col-lg-8">
                            <div class="card-g p-4 h-100 d-flex flex-column justify-content-center" id="uploadCard">
                                <h6 class="fw-bold text-dark mb-1 fs-5" id="uploadTitle">
                                    <i class="fas fa-file-excel me-2 text-muted" id="uploadIcon"></i>
                                    Cargar Lote de Facturas
                                </h6>
                                <p class="mb-3" style="font-size:.83rem;color:var(--c-muted);" id="uploadDesc">
                                    Arrastra tu archivo <strong>.xlsx / .xls / .csv</strong> o haz clic para seleccionarlo.
                                    Optimizado para lotes de +30.000 registros.
                                </p>

                                <form action="{{ route('certificados.ingesta.cargar') }}"
                                      method="POST" enctype="multipart/form-data"
                                      id="formCargaMasiva"
                                      class="d-flex flex-column gap-3">
                                    @csrf

                                    <!-- Selector de Periodo -->
                                    <div>
                                        <select name="id_periodo" class="input-g fw-semibold shadow-sm w-100" required aria-label="Seleccionar Periodo">
                                            <option value="" disabled selected>1. Elige a qué periodo pertenece este lote...</option>
                                            @foreach($periodos as $p)
                                                <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->anio }}-{{ str_pad($p->mes, 2, '0', STR_PAD_LEFT) }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Zona de Drop y Botón -->
                                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-stretch">
                                        <div class="position-relative flex-grow-1" id="dropWrapper">
                                            <input type="file" name="archivo_excel" id="archivoExcelInput"
                                                   accept=".xlsx,.xls,.csv" required
                                                   class="position-absolute w-100 h-100 top-0 start-0"
                                                   style="opacity:0;cursor:pointer;z-index:10;"
                                                   aria-label="Seleccionar archivo Excel">

                                            <div class="drop-zone d-flex flex-column align-items-center justify-content-center" id="dropZoneVisual">
                                                <i class="fas fa-cloud-upload-alt fs-2 mb-2 text-muted" id="dropIcon"></i>
                                                <span class="fw-medium text-muted" id="dropLabel" style="font-size:.85rem;">
                                                    2. Arrastra aquí o haz clic para buscar
                                                </span>
                                                <span class="mt-1" style="font-size:.73rem;color:var(--c-muted);" id="dropMeta">
                                                    .xlsx · .xls · .csv — máx. 50 MB
                                                </span>
                                            </div>
                                        </div>

                                        <button type="submit" id="btnUploadSubmit"
                                                class="btn-g btn-outline-g fw-bold"
                                                disabled aria-disabled="true"
                                                style="opacity:.45;pointer-events:none;min-width:125px;">
                                            <i class="fas fa-upload btn-ico"></i>
                                            <span class="btn-label">Subir Lote</span>
                                            <span class="btn-spinner"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- BARRA DE ACCIÓN --}}
                    @if($totalPendientes > 0 && $bloqueActivo)
                    @php
                        $totalBloque   = $kpi['procesados'] + $kpi['pendientes'] + $kpi['anulados'];
                        $pctProcesado  = $totalBloque > 0 ? round(($kpi['procesados'] / $totalBloque) * 100) : 0;
                    @endphp
                    <div class="action-bar mb-3">
                        <div class="action-bar-progress">
                            <div class="action-bar-progress-fill" id="progressFill"
                                 style="width: {{ $pctProcesado }}%;"
                                 title="{{ $pctProcesado }}% procesado"></div>
                        </div>

                        <div class="action-bar-body">
                            <div class="d-flex align-items-center gap-3" style="min-width:160px;">
                                <span class="pulse-dot"></span>
                                <div>
                                    <div class="fw-black text-dark" style="font-size:1.4rem;line-height:1;font-weight:800;">
                                        {{ number_format($totalPendientes, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size:.68rem;color:var(--c-muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em;">
                                        Registros listos · API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 hide-sm">
                                <span class="stat-chip">
                                    <span class="dot" style="background:var(--c-success);"></span>
                                    {{ number_format($kpi['procesados'],0,',','.') }} procesados
                                </span>
                                <span class="stat-chip">
                                    <span class="dot" style="background:var(--c-danger);"></span>
                                    {{ number_format($kpi['anulados'],0,',','.') }} anulados
                                </span>
                                <span class="stat-chip">
                                    <i class="fas fa-percentage" style="font-size:.65rem;color:var(--c-primary);"></i>
                                    {{ $pctProcesado }}% completado
                                </span>
                            </div>

                            <div class="vr-g hide-sm ms-auto"></div>

                            <form action="{{ route('certificados.ingesta.inyectar') }}"
                                  method="POST" id="formInyeccion"
                                  class="d-flex flex-wrap flex-md-nowrap align-items-end gap-2 m-0">
                                @csrf
                                <input type="hidden" name="bloque_origen" value="{{ $bloqueActivo }}">

                                <div class="d-flex flex-column">
                                    <label class="text-uppercase fw-bold mb-1" style="font-size:.62rem;color:var(--c-muted);letter-spacing:.05em;">Estado</label>
                                    <select name="id_car_sia_estados" class="select-chip" required aria-label="Estado para inyección">
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex flex-column">
                                    <label class="text-uppercase fw-bold mb-1" style="font-size:.62rem;color:var(--c-muted);letter-spacing:.05em;">Tipo</label>
                                    <select name="id_car_sia_tipos" class="select-chip" required aria-label="Tipo para inyección">
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn-g btn-primary-g rounded-pill" id="btnInyectar" style="padding:.6rem 1.6rem; align-self:flex-end;">
                                    <i class="fas fa-bolt btn-ico" style="color:rgba(255,255,255,.85);"></i>
                                    <span class="btn-label">Ejecutar Bloque</span>
                                    <span class="btn-spinner"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- TABLA COLAPSABLE --}}
                    <div class="card-g overflow-hidden mb-4" id="tableCard">
                        <div class="table-header-strip">
                            <button class="table-toggle-btn"
                                    id="tableToggleBtn"
                                    type="button"
                                    aria-expanded="false"
                                    aria-controls="tableCollapse"
                                    title="Mostrar u ocultar registros">
                                <span class="toggle-arrow"><i class="fas fa-chevron-down"></i></span>
                                <span>Registros &mdash; Bloque</span>
                                <span class="badge-g badge-info ms-1">API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-muted fw-normal" style="font-size:.8rem;">({{ number_format($lotesCrudos->total(), 0, ',', '.') }} en total)</span>
                            </button>

                            <form action="{{ route('certificados.ingesta.index') }}" method="GET" id="formFiltros" class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

                                <select name="estado" class="input-g fw-semibold shadow-sm"
                                        style="min-width:150px;cursor:pointer;"
                                        onchange="this.form.submit()"
                                        aria-label="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>⏳ Pendientes</option>
                                    <option value="PROCESADO" {{ request('estado') == 'PROCESADO' ? 'selected' : '' }}>✅ Procesados</option>
                                    <option value="ANULADO" {{ request('estado') == 'ANULADO' ? 'selected' : '' }}>🚫 Anulados</option>
                                </select>

                                <div class="position-relative">
                                    <i class="fas fa-search position-absolute top-50 translate-middle-y" style="left:.85rem;color:var(--c-muted);font-size:.8rem;pointer-events:none;"></i>
                                    <input type="text" name="buscar_cedula" id="inputBusqueda"
                                           class="input-g shadow-sm"
                                           style="padding-left:2.3rem;min-width:220px;"
                                           placeholder="Buscar por NIT, nombre o factura…"
                                           value="{{ request('buscar_cedula') }}"
                                           aria-label="Buscar en este lote">
                                </div>

                                @if(request('buscar_cedula') || request('estado'))
                                    <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm" title="Limpiar filtros">
                                        <i class="fas fa-times"></i> <span class="hide-sm">Limpiar</span>
                                    </a>
                                @endif
                            </form>
                        </div>

                        <div class="collapsible-table" id="tableCollapse">
                            <div class="table-responsive">
                                <table class="table tbl align-middle mb-0" aria-label="Registros del bloque de ingesta">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" style="width:105px;">Bloque</th>
                                            <th style="width:120px;">ID Factura</th>
                                            <th>Cliente / Tercero</th>
                                            <th class="text-end" style="width:145px;">Valor Neto</th>
                                            <th style="width:140px;">Estado ETL</th>
                                            <th style="width:155px;">Fecha Recepción</th>
                                            <th class="text-center pe-4" style="width:85px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lotesCrudos as $lote)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-light text-secondary border fw-semibold" style="font-family:monospace;font-size:.78rem;">
                                                    API-{{ str_pad($lote->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold" style="font-family:monospace;color:var(--c-text);">#{{ $lote->id_factura ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size:.875rem;">{{ $lote->nombre_tercero ?? 'Tercero desconocido' }}</div>
                                                <div style="color:var(--c-muted);font-size:.75rem;margin-top:.1rem;">
                                                    <i class="fas fa-id-card me-1" style="font-size:.65rem;"></i> NIT: {{ $lote->tercero ?? '—' }}
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold" style="font-size:.9rem;">
                                                ${{ number_format((float)($lote->valor ?? 0), 2, ',', '.') }}
                                            </td>
                                            <td>
                                                @if($lote->anular == 1)
                                                    <span class="badge-g badge-danger"><i class="fas fa-ban"></i> Anulado</span>
                                                @elseif($lote->estado == 'PROCESADO')
                                                    <span class="badge-g badge-ok"><i class="fas fa-check-circle"></i> Procesado</span>
                                                @else
                                                    <span class="badge-g badge-warn"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                                                @endif
                                            </td>
                                            <td style="color:var(--c-muted);font-size:.82rem;">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $lote->fecha_ad ? \Carbon\Carbon::parse($lote->fecha_ad)->format('d/m/Y') : ($lote->created_at ? \Carbon\Carbon::parse($lote->created_at)->format('d/m/Y H:i') : '—') }}
                                            </td>
                                            <td class="text-center pe-4">
                                                @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                                    <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas excluir la factura #{{ $lote->id_factura }} del bloque?')">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-sm border-0 bg-transparent p-2" style="color:var(--c-danger);" title="Excluir del bloque" aria-label="Excluir factura {{ $lote->id_factura }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span style="color:var(--c-muted);opacity:.4;" title="{{ $lote->anular == 1 ? 'Anulado' : 'Ya procesado' }}">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center py-3">
                                                    <div class="p-4 rounded-circle mb-3" style="background:var(--c-bg);">
                                                        <i class="fas fa-inbox fs-1" style="color:var(--c-border);"></i>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">Sin registros en este lote</h6>
                                                    <p class="mb-3" style="color:var(--c-muted);max-width:360px;font-size:.85rem;">
                                                        @if(request('buscar_cedula') || request('estado'))
                                                            Ningún registro coincide con los filtros. Prueba limpiándolos.
                                                        @else
                                                            Carga un archivo Excel para comenzar con este bloque.
                                                        @endif
                                                    </p>
                                                    @if(request('buscar_cedula') || request('estado'))
                                                        <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm">
                                                            <i class="fas fa-times"></i> Limpiar filtros
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($lotesCrudos->hasPages() || $lotesCrudos->total() > 0)
                            <div class="bg-white border-top px-4 py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <p class="m-0 text-muted" style="font-size:.8rem;">
                                    Mostrando <strong class="text-dark">{{ $lotesCrudos->firstItem() ?? 0 }}</strong> – <strong class="text-dark">{{ $lotesCrudos->lastItem()  ?? 0 }}</strong> de <strong class="text-dark">{{ number_format($lotesCrudos->total(), 0, ',', '.') }}</strong> registros
                                </p>
                                <div>
                                    {{ $lotesCrudos->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ========================================================= --}}
                {{-- COLUMNA LATERAL (SIDEBAR FIJO) CON FLIP CARD ANIMADO        --}}
                {{-- ========================================================= --}}
                <div class="col-12 col-xl-3">
                    <div class="card-g p-4 sticky-sidebar">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark d-flex align-items-center gap-2 m-0" style="font-size: 1.1rem;">
                                <i class="far fa-calendar-alt text-muted" style="font-size:.9rem;"></i> Periodos y Lotes
                            </h5>
                        </div>

                        {{-- ENVOLTORIO FLIP --}}
                        <div class="flip-wrapper">
                            <div class="flip-card" id="sidebarFlipCard">

                                {{-- CARA FRONTAL: HISTORIAL ANIDADO POR AÑO -> MES -> BLOQUES --}}
                                <div class="flip-face flip-front">
                                    <p class="text-muted mb-3 pb-2 border-bottom" style="font-size:.8rem;">Historial contable y lotes asociados.</p>

                                    <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2 mb-3">
                                        {{-- Agrupamos la colección de periodos por 'anio' --}}
                                        @php
                                            $periodosAgrupados = collect($periodos)->groupBy('anio')->sortKeysDesc();
                                        @endphp

                                        @forelse($periodosAgrupados as $anio => $meses)
                                            <div class="mb-3">
                                                {{-- Encabezado del Año --}}
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-light text-dark border shadow-sm">
                                                        <i class="fas fa-folder-open text-muted me-1"></i> Año {{ $anio }}
                                                    </span>
                                                </div>

                                                {{-- Lista de Meses anidados debajo del año --}}
                                                <div class="d-flex flex-column gap-2 ps-2 ms-2" style="border-left: 2px solid var(--c-border);">
                                                    @foreach($meses->sortByDesc('mes') as $p)
                                                        @php
                                                            // Buscamos los bloques pertenecientes a este periodo en la base de datos
                                                            $bloquesDelPeriodo = \App\Models\Certificados\CarSiaBloque::where('id_periodo', $p->id)
                                                                ->orderBy('numero_bloque', 'desc')
                                                                ->get();
                                                        @endphp
                                                        <div class="d-flex flex-column p-2 rounded mb-1 shadow-sm" style="background: var(--c-surface); border: 1px solid var(--c-border);">

                                                            {{-- Info del Mes --}}
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="fw-bold text-dark" style="font-size:.8rem; line-height:1.2;">{{ $p->nombre }}</div>
                                                                    <div style="font-size:.65rem; color:var(--c-muted); font-family:monospace;">
                                                                        Mes: {{ str_pad($p->mes, 2, '0', STR_PAD_LEFT) }}
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    @if($p->abierto)
                                                                        <span style="color:var(--c-success); font-size:.7rem; font-weight:600;"><i class="fas fa-circle" style="font-size:.4rem; vertical-align:middle; margin-right:3px;"></i> Abierto</span>
                                                                    @else
                                                                        <span style="color:var(--c-muted); font-size:.7rem; font-weight:600;"><i class="fas fa-circle" style="font-size:.4rem; vertical-align:middle; margin-right:3px;"></i> Cerrado</span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            {{-- Lotes/Bloques anidados bajo este Mes --}}
                                                            @if($bloquesDelPeriodo->count() > 0)
                                                                <div class="d-flex flex-column gap-1 mt-2 pt-2 border-top">
                                                                    @foreach($bloquesDelPeriodo as $bloque)
                                                                        @php
                                                                            $esActivo = ($bloqueActivo == $bloque->numero_bloque);
                                                                        @endphp
                                                                        <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloque->numero_bloque]) }}"
                                                                           class="block-link d-flex align-items-center justify-content-between text-decoration-none px-2 py-1 rounded {{ $esActivo ? 'active-block' : '' }}"
                                                                           style="font-size: .75rem;"
                                                                           title="{{ $bloque->descripcion ?? 'Lote #'.$bloque->numero_bloque }}">
                                                                            <span class="fw-semibold d-flex align-items-center gap-2">
                                                                                <i class="fas fa-cube ico-cube" style="font-size: .65rem;"></i>
                                                                                Lote API-{{ str_pad($bloque->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                            </span>
                                                                            @if($esActivo)
                                                                                <i class="fas fa-check" style="font-size: .65rem; color: #fff;"></i>
                                                                            @endif
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="mt-2 pt-2 border-top text-center" style="font-size: .65rem; color: var(--c-muted);">
                                                                    Sin lotes asignados
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted text-center py-4 mb-0" style="font-size:.8rem;"><i class="fas fa-inbox d-block mb-2 fs-3 opacity-50"></i>No hay periodos registrados.</p>
                                        @endforelse
                                    </div>

                                    <button type="button" id="btnFlipToForm" class="btn-g btn-outline-g w-100 justify-content-center mt-auto" style="padding:.6rem 0;">
                                        <i class="fas fa-plus" style="color:var(--c-primary);"></i> Nuevo Periodo
                                    </button>
                                </div>

                                {{-- CARA TRASERA: FORMULARIO DE CREACIÓN --}}
                                <div class="flip-face flip-back">
                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                        <button type="button" id="btnFlipToList" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" title="Volver al historial" style="width:32px;height:32px;border-radius:50%;background:var(--c-bg);">
                                            <i class="fas fa-arrow-left text-muted"></i>
                                        </button>
                                        <span class="fw-bold text-dark m-0" style="font-size: .95rem;">Crear Periodo</span>
                                    </div>

                                    <p class="text-muted mb-4" style="font-size:.8rem;">Abre un nuevo mes contable para asignar operaciones.</p>

                                    <form action="{{ route('certificados.periodos.store') }}" method="POST" class="d-flex flex-column flex-grow-1">
                                        @csrf
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Año</label>
                                                <input type="number" name="anio" class="form-control form-control-sm border-0 bg-light" value="{{ date('Y') }}" required min="2020" max="2099" style="box-shadow:none;">
                                            </div>
                                            <div class="col-6">
                                                <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Mes</label>
                                                <select name="mes" class="form-select form-select-sm border-0 bg-light" required style="box-shadow:none; cursor:pointer;">
                                                    @for($i=1; $i<=12; $i++)
                                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Nombre del Periodo</label>
                                            <input type="text" name="nombre" class="form-control form-control-sm border-0 bg-light" placeholder="Ej: AGOSTO 2026" required style="box-shadow:none;">
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px dashed var(--c-border);">
                                            <span class="fw-medium text-dark" style="font-size:.85rem;">Mantener Abierto</span>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" name="abierto" id="switchAbierto" checked style="cursor:pointer; width:35px; height:18px;">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn-g btn-primary-g w-100 justify-content-center mt-auto" style="padding:.6rem 0;">
                                            <i class="fas fa-save me-1"></i> Guardar Mes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // Nota: La animación JS de los contadores (KPI) está apagada
        // para garantizar que siempre muestren las cifras reales PHP (20.000) y nunca "0".

        // 2. TOGGLE TABLA COLAPSABLE
        const toggleBtn    = document.getElementById('tableToggleBtn');
        const tableCollapse = document.getElementById('tableCollapse');
        const STORAGE_KEY  = 'ingesta_table_open_{{ $bloqueActivo }}';

        const hasFilters   = {{ (request('buscar_cedula') || request('estado')) ? 'true' : 'false' }};
        const wasOpen      = localStorage.getItem(STORAGE_KEY) === '1';

        function openTable() {
            tableCollapse.classList.add('is-open');
            toggleBtn.setAttribute('aria-expanded', 'true');
            localStorage.setItem(STORAGE_KEY, '1');
        }
        function closeTable() {
            tableCollapse.classList.remove('is-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
            localStorage.setItem(STORAGE_KEY, '0');
        }

        if (hasFilters || wasOpen) openTable();

        toggleBtn.addEventListener('click', function () {
            const isOpen = tableCollapse.classList.contains('is-open');
            isOpen ? closeTable() : openTable();
        });

        // 3. CHART.JS — GRÁFICO DE BARRAS INTERACTIVO
        const chartCanvas = document.getElementById('kpiChart');
        if (chartCanvas) {
            const ctx       = chartCanvas.getContext('2d');
            const dataPend  = {{ (int)($kpi['pendientes']  ?? 0) }};
            const dataProc  = {{ (int)($kpi['procesados']  ?? 0) }};
            const dataAnul  = {{ (int)($kpi['anulados']    ?? 0) }};
            const total     = dataPend + dataProc + dataAnul;

            // Plugin manual para mostrar valores/porcentajes en cada barra
            const dataLabelsPlugin = {
                id: 'dataLabelsPlugin',
                afterDatasetsDraw(chart, args, options) {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        chart.getDatasetMeta(i).data.forEach((bar, index) => {
                            const val = dataset.data[index];
                            if (val === 0) return; // Omitir texto si es 0

                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) + '%' : '';
                            const text = `${val.toLocaleString('es-CO')} (${pct})`;

                            ctx.save();
                            ctx.font = "bold 11px Inter, sans-serif";
                            ctx.fillStyle = "#64748b"; // Gris muted
                            ctx.textAlign = "center";
                            ctx.textBaseline = "bottom";
                            // Dibujar justo por encima de la columna
                            ctx.fillText(text, bar.x, bar.y - 6);
                            ctx.restore();
                        });
                    });
                }
            };

            const baseOpts = {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 25 } }, // Espacio para el texto arriba de barras
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#e2e8f0' },
                        ticks: { display: false } // Ocultar eje Y numérico (ya lo muestra el texto)
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                        displayColors: false,
                        titleFont: { family: "'Inter',sans-serif", weight: '700' },
                        bodyFont : { family: "'Inter',sans-serif" },
                        callbacks: {
                            label: ctxInfo => {
                                const val = ctxInfo.raw;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` Total: ${val.toLocaleString('es-CO')} (${pct}%)`;
                            }
                        }
                    }
                },
                // Handlers interactivos para Filtrar la Tabla
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const estadosMap = ['PENDIENTE', 'PROCESADO', 'ANULADO'];
                        const selectedEstado = estadosMap[index];

                        const formFilt = document.getElementById('formFiltros');
                        if (formFilt) {
                            const selectEstado = formFilt.querySelector('select[name="estado"]');
                            if (selectEstado) {
                                selectEstado.value = selectedEstado;
                                formFilt.submit();
                            }
                        }
                    }
                },
                animation: { duration: 900, easing: 'easeInOutQuart' }
            };

            if (total === 0) {
                new Chart(ctx, {
                    type: 'bar',
                    data: { labels: ['Sin datos'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderRadius: 6 }] },
                    options: { ...baseOpts, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
                });
            } else {
                new Chart(ctx, {
                    type: 'bar', // Carga el gráfico de barras
                    plugins: [dataLabelsPlugin], // Llama al plugin para dibujar datos
                    data: {
                        labels: ['Pendientes', 'Procesados', 'Anulados'],
                        datasets: [{
                            data            : [dataPend, dataProc, dataAnul],
                            backgroundColor : ['#f59e0b', '#10b981', '#ef4444'],
                            hoverBackgroundColor: ['#d97706', '#059669', '#dc2626'],
                            borderRadius    : 6,
                            borderSkipped   : false,
                            barPercentage   : 0.65
                        }]
                    },
                    options: baseOpts
                });
            }
        }

        // 4. DRAG & DROP + UPLOAD UX
        const fileInput   = document.getElementById('archivoExcelInput');
        const dropWrapper = document.getElementById('dropWrapper');
        const dropZone    = document.getElementById('dropZoneVisual');
        const dropIcon    = document.getElementById('dropIcon');
        const dropLabel   = document.getElementById('dropLabel');
        const dropMeta    = document.getElementById('dropMeta');
        const uploadCard  = document.getElementById('uploadCard');
        const uploadTitle = document.getElementById('uploadTitle');
        const uploadDesc  = document.getElementById('uploadDesc');
        const btnUpload   = document.getElementById('btnUploadSubmit');
        const formUpload  = document.getElementById('formCargaMasiva');

        if (fileInput && dropWrapper && formUpload) {
            ['dragenter','dragover','dragleave','drop'].forEach(ev =>
                dropWrapper.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false)
            );
            ['dragenter','dragover'].forEach(ev =>
                dropWrapper.addEventListener(ev, () => dropZone.classList.add('is-over'), false)
            );
            ['dragleave','drop'].forEach(ev =>
                dropWrapper.addEventListener(ev, () => dropZone.classList.remove('is-over'), false)
            );
            dropWrapper.addEventListener('drop', e => {
                const files = e.dataTransfer?.files;
                if (files?.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }, false);

            fileInput.addEventListener('change', function () {
                if (!this.files?.length) return;
                const file  = this.files[0];
                const sizeMB = (file.size / 1048576).toFixed(2);
                const ext   = file.name.split('.').pop().toUpperCase();
                const allowed = ['XLSX','XLS','CSV'];

                if (!allowed.includes(ext)) {
                    alert(`⚠️ Formato .${ext} no soportado.\nUsa: .xlsx, .xls o .csv`);
                    this.value = ''; return;
                }
                dropZone.classList.add('is-ready');
                dropIcon.className        = 'fas fa-check-circle fs-2 mb-2';
                dropIcon.style.color      = '#10b981';
                dropLabel.innerHTML       = `<strong style="color:#065f46;">${file.name}</strong>`;
                dropMeta.textContent      = `${sizeMB} MB · ${ext} · Listo para enviar`;

                uploadCard.style.backgroundColor = '#f0fdf4';
                uploadCard.style.borderColor      = '#86efac';
                uploadTitle.innerHTML             = `<i class="fas fa-rocket me-2" style="color:#16a34a;"></i>Archivo seleccionado`;
                uploadTitle.style.color           = '#166534';
                uploadDesc.innerHTML              = 'Revisa nombre y tamaño, luego haz clic en <strong>Subir Lote</strong>.';
                uploadDesc.style.color            = '#15803d';

                btnUpload.disabled                = false;
                btnUpload.setAttribute('aria-disabled', 'false');
                btnUpload.style.opacity           = '1';
                btnUpload.style.pointerEvents     = 'auto';
                btnUpload.className               = 'btn-g btn-primary-g fw-bold';
                btnUpload.querySelector('.btn-ico').className    = 'fas fa-cogs btn-ico';
                btnUpload.querySelector('.btn-label').textContent = 'Subir Lote';
            });

            formUpload.addEventListener('submit', () => {
                btnUpload.classList.add('is-loading');
                btnUpload.disabled             = true;
                uploadCard.style.opacity       = '0.6';
                uploadCard.style.pointerEvents = 'none';
            });
        }

        // 5. SPINNER EN BOTÓN DE INYECCIÓN
        const formInj  = document.getElementById('formInyeccion');
        const btnInj   = document.getElementById('btnInyectar');
        if (formInj && btnInj) {
            formInj.addEventListener('submit', e => {
                if (formInj.checkValidity()) {
                    btnInj.classList.add('is-loading');
                    btnInj.disabled = true;
                }
            });
        }

        // 6. BÚSQUEDA CON DEBOUNCE (600ms)
        const inputBusq  = document.getElementById('inputBusqueda');
        const formFilt   = document.getElementById('formFiltros');
        let   searchTimer = null;

        if (inputBusq && formFilt) {
            inputBusq.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => formFilt.submit(), 600);
            });
        }

        // 7. PROGRESS BAR
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            const targetWidth = progressFill.style.width;
            progressFill.style.width = '0%';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    progressFill.style.width = targetWidth;
                });
            });
        }

        // 8. ANIMACIÓN FLIP CARD EN SIDEBAR FIJO
        const sidebarFlipCard = document.getElementById('sidebarFlipCard');
        const btnFlipToForm   = document.getElementById('btnFlipToForm');
        const btnFlipToList   = document.getElementById('btnFlipToList');

        if (sidebarFlipCard && btnFlipToForm && btnFlipToList) {
            btnFlipToForm.addEventListener('click', () => {
                sidebarFlipCard.classList.add('is-flipped');
            });
            btnFlipToList.addEventListener('click', () => {
                sidebarFlipCard.classList.remove('is-flipped');
            });
        }

    });
    </script>
</x-base-layout>
