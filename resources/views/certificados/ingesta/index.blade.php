{{--
|--------------------------------------------------------------------------
| Vista: certificados/ingesta/index.blade.php (VERSIÓN UNIFICADA)
|--------------------------------------------------------------------------
| Propósito : Visor y gestor de lotes de ingesta ERP (Staging).
|--------------------------------------------------------------------------
--}}
<x-base-layout>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            integrity="sha256-oVuCFqsKIbRHvGQXDhRaBEJ9oMH2DhJCj2wr7KpBbSA="
            crossorigin="anonymous">
    </script>

    {{-- 1. ESTILOS --}}
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

        /* ── Tooltips Personalizados ── */
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
        .badge-ok      { background: var(--c-success-soft); color: #065f46; }
        .badge-warn    { background: var(--c-warning-soft);  color: #92400e; }
        .badge-danger  { background: var(--c-danger-soft);   color: #991b1b; }
        .badge-info    { background: var(--c-primary-soft);  color: #3730a3; }

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
            transition: width .6s ease;
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

        /* ── ESTILOS NUEVOS ACORDEÓN DE AÑOS ──────────────────────── */
        .year-toggle-btn {
            cursor: pointer;
            user-select: none;
        }
        .year-toggle-btn:hover .badge {
            background: #e2e8f0 !important;
        }
        .chevron-icon {
            transition: transform 0.3s ease;
        }
        .year-toggle-btn.is-open .chevron-icon {
            transform: rotate(180deg);
        }
        .mes-actual-highlight {
            border: 1px solid var(--c-primary) !important;
            background: var(--c-primary-soft) !important;
        }

        /* Animación del icono desplegable para los meses */
        .month-toggle-btn.is-open .chevron-month { transform: rotate(90deg); color: var(--c-primary) !important; }
        .month-toggle-btn:hover { background-color: var(--c-bg); border-radius: var(--r-md) var(--r-md) 0 0; }

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

                    {{-- 2. CABECERA Y BREADCRUMBS --}}
                    @php
                        $migasAnio = '----';
                        $migasMes = '--';
                        $migasLote = $bloqueActivo ? 'API-' . str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) : 'Por seleccionar';

                        if($bloqueActivo) {
                            $bActivo = \App\Models\Certificados\CarSiaBloque::where('numero_bloque', $bloqueActivo)->first();
                            if($bActivo) {
                                $pActivo = collect($periodos)->firstWhere('id', $bActivo->id_periodo);
                                if($pActivo) {
                                    $migasAnio = $pActivo->anio;
                                    $migasMes = str_pad($pActivo->mes, 2, '0', STR_PAD_LEFT);
                                }
                            }
                        }
                    @endphp

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
                                          data-tooltip="Entorno del analista"
                                          style="font-size:.63rem;">
                                        Analista
                                    </span>
                                </h1>

                                <div class="d-flex align-items-center flex-wrap mt-1" style="font-size:.8rem; color:var(--c-muted); font-weight: 500;">
                                    <i class="fas fa-file-excel me-1" style="color:#10b981;"></i> Subida de excel
                                    <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                                    {{ $migasAnio }}
                                    <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                                    Mes {{ $migasMes }}
                                    <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                                    @if($bloqueActivo)
                                        <span class="fw-bold" style="color:var(--c-primary);">Lote {{ $migasLote }}</span>
                                    @else
                                        <span class="fw-bold" style="color:#92400e;">Lote {{ $migasLote }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="{{ request()->fullUrl() }}" class="btn-icon-round shadow-sm" title="Actualizar vista">
                                <i class="fas fa-sync-alt spin-on-hover"></i>
                            </a>

                            <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm" data-tooltip="Ver las operaciones de este lote">
                                <i class="fas fa-list-ul" style="color:var(--c-primary);"></i>
                                Ver Operaciones
                            </a>

                            @if($bloquesDisponibles->count() > 0)
                            <form action="{{ route('certificados.ingesta.index') }}" method="GET"
                                  class="d-flex align-items-center gap-2 bg-light border rounded-pill px-3 py-1 shadow-sm"
                                  data-tooltip="Cambiar de lote rápidamente"
                                  style="transition: background 0.2s;">
                                @if(request('buscar_cedula')) <input type="hidden" name="buscar_cedula" value="{{ request('buscar_cedula') }}"> @endif
                                @if(request('estado'))        <input type="hidden" name="estado"        value="{{ request('estado') }}"> @endif

                                <i class="fas fa-exchange-alt text-muted" style="font-size:.75rem;"></i>
                                <select name="bloque"
                                        class="border-0 bg-transparent text-muted fw-medium shadow-none"
                                        style="cursor:pointer;outline:none;font-size:.78rem; padding: .15rem 0;"
                                        onchange="this.form.submit()"
                                        aria-label="Cambio rápido de bloque">
                                    <option value="" disabled {{ empty($bloqueActivo) ? 'selected' : '' }}>Cambiar lote...</option>

                                    @foreach($bloquesDisponibles as $b)
                                        <option value="{{ $b }}" {{ $bloqueActivo == $b ? 'selected' : '' }}>
                                            Cambiar a API-{{ str_pad($b, 4, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                        </div>
                    </div>

                    {{-- 3. ALERTAS Y MODAL DE TERCEROS --}}
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

                    @if(session('requiere_crear_terceros') && session('bloque_fallido'))
                        <div class="alert alert-warning mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>¡Atención!</strong> Se requiere registrar estos clientes en la Maestra de Terceros para poder continuar.
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearTerceros">
                                <i class="fas fa-eye"></i> Previsualizar y Crear Terceros
                            </button>
                        </div>

                        <!-- Modal de Previsualización Terceros -->
                        <div class="modal fade" id="modalCrearTerceros" tabindex="-1" aria-labelledby="modalCrearTercerosLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title text-dark" id="modalCrearTercerosLabel">
                                            <i class="fas fa-users"></i> Previsualización de Nuevos Terceros
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small">Se insertarán los siguientes registros en la tabla <code>MaeTerceros</code> con valores genéricos seguros. Revisa los datos antes de proceder.</p>

                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-sm table-bordered table-striped">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Cédula/NIT (cod_ter)</th>
                                                        <th>Razón Social / Nombre (nom_ter)</th>
                                                        <th>Tipo Persona</th>
                                                        <th>Tipo Tercero</th>
                                                        <th>Tipo Doc</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(session('lista_faltantes'))
                                                        @foreach(session('lista_faltantes') as $faltante)
                                                            <tr>
                                                                <td><strong>{{ $faltante['tercero'] }}</strong></td>
                                                                <td>{{ $faltante['nombre_tercero'] ?: 'SIN NOMBRE REGISTRADO' }}</td>
                                                                <td>1 (Natural)</td>
                                                                <td>CLIENTE</td>
                                                                <td>13 (C.C.)</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form action="{{ route('certificados.ingesta.crear_terceros') }}" method="POST" class="m-0" onsubmit="return procesarCreacion(this)">
                                            @csrf
                                            <input type="hidden" name="bloque_origen" value="{{ session('bloque_fallido') }}">
                                            <button type="submit" id="btnConfirmarTerceros" class="btn btn-success">
                                                <i class="fas fa-check"></i> Confirmar Inserción
                                            </button>
                                        </form>
                                        <script>
                                            function procesarCreacion(formulario) {
                                                if (confirm('¿Confirmas la creación de estos registros únicos en la maestra?')) {
                                                    let boton = document.getElementById('btnConfirmarTerceros');
                                                    boton.disabled = true;
                                                    boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando datos...';
                                                    return true;
                                                }
                                                return false;
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 4. TARJETAS KPI --}}
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

                    {{-- 5. PANEL DE CARGA DE ARCHIVOS Y GRÁFICO --}}
                    <div class="card-g overflow-hidden mb-4 shadow-sm" style="border: 1px solid var(--c-border); border-radius: var(--r-xl);">
                        <button class="w-100 border-0 bg-white px-4 py-3 d-flex align-items-center justify-content-between text-start collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#panelCargaBase"
                                aria-expanded="false"
                                aria-controls="panelCargaBase"
                                style="outline: none;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background-color: var(--c-primary-soft);">
                                    <i class="fas fa-cloud-upload-alt" style="color: var(--c-primary); font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: .95rem;">Panel de Archivos Base</h6>
                                    <p class="mb-0 text-muted" style="font-size: .8rem;">Área principal donde se cargan los archivos bases y se visualiza el estado del lote.</p>
                                </div>
                            </div>
                            <span class="text-muted"><i class="fas fa-chevron-down"></i></span>
                        </button>

                        <div class="collapse" id="panelCargaBase">
                            <div class="p-4 border-top" style="background-color: #f8fafc;">
                                <div class="row g-4">

                                    <div class="col-12 col-lg-4">
                                        <div class="bg-white p-4 h-100 d-flex flex-column rounded-3 shadow-sm" style="border: 1px solid #e2e8f0;">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0" style="font-size: .9rem;">
                                                        <i class="fas fa-chart-bar me-2" style="color: var(--c-primary);"></i>Estado del Bloque
                                                    </h6>
                                                    <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--c-muted);">
                                                        Clic en la columna para filtrar la tabla
                                                    </p>
                                                </div>
                                            </div>
                                            <div style="position:relative; height:200px; flex:1;">
                                                <canvas id="kpiChart" aria-label="Gráfico de estado"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-8">
                                        <div class="bg-white p-4 h-100 d-flex flex-column justify-content-center rounded-3 shadow-sm" id="uploadCard" style="border: 1px solid #e2e8f0;">
                                            <h6 class="fw-bold text-dark mb-1" id="uploadTitle" style="font-size: .95rem;">
                                                <i class="fas fa-file-excel me-2" id="uploadIcon" style="color: #10b981;"></i>
                                                Cargar Lote de Facturas
                                            </h6>
                                            <p class="mb-3" style="font-size:.8rem; color:var(--c-muted);" id="uploadDesc">
                                                Arrastra tu archivo <strong>.xlsx / .xls / .csv</strong> o haz clic para seleccionarlo. Optimizado para +30.000 registros.
                                            </p>

                                            <form action="{{ route('certificados.ingesta.cargar') }}"
                                                  method="POST" enctype="multipart/form-data"
                                                  id="formCargaMasiva"
                                                  class="d-flex flex-column gap-3">
                                                @csrf
                                                <input type="hidden" name="progreso_token" id="progresoToken">

                                                <div>
                                                    <select name="id_periodo" class="input-g shadow-sm w-100" required aria-label="Seleccionar Periodo" style="font-size: .85rem; padding: 0.5rem 1rem;">
                                                        <option value="" disabled selected>1. Elige a qué periodo pertenece este lote...</option>
                                                        @foreach($periodos as $p)
                                                            @php
                                                                // Verificamos si existe al menos un bloque para este periodo que no esté anulado
                                                                $tieneLoteActivo = \App\Models\Certificados\CarSiaBloque::where('id_periodo', $p->id)
                                                                    ->where('estado', '!=', 'ANULADO')
                                                                    ->exists();
                                                            @endphp

                                                            <option value="{{ $p->id }}" {{ $tieneLoteActivo ? 'disabled' : '' }}>
                                                                {{ $p->nombre }} ({{ $p->anio }}-{{ str_pad($p->mes, 2, '0', STR_PAD_LEFT) }})
                                                                {{ $tieneLoteActivo ? ' — 🔒 Ya tiene un lote activo' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-stretch">
                                                    <div class="position-relative flex-grow-1" id="dropWrapper">
                                                        <input type="file" name="archivo_excel" id="archivoExcelInput"
                                                               accept=".xlsx,.xls,.csv" required
                                                               class="position-absolute w-100 h-100 top-0 start-0"
                                                               style="opacity:0; cursor:pointer; z-index:10;"
                                                               aria-label="Seleccionar archivo Excel">

                                                        <div class="drop-zone d-flex flex-column align-items-center justify-content-center rounded-3" id="dropZoneVisual" style="background-color: #f8fafc; border: 2px dashed #cbd5e1; padding: 1.5rem;">
                                                            <i class="fas fa-cloud-upload-alt fs-3 mb-2" id="dropIcon" style="color: #94a3b8;"></i>
                                                            <span class="fw-medium text-dark" id="dropLabel" style="font-size:.85rem;">
                                                                2. Arrastra aquí o haz clic para buscar
                                                            </span>
                                                            <span class="mt-1" style="font-size:.7rem; color:var(--c-muted);" id="dropMeta">
                                                                .xlsx · .xls · .csv — máx. 50 MB
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <button type="submit" id="btnUploadSubmit"
                                                            class="btn-g btn-outline-g fw-bold d-flex flex-column justify-content-center align-items-center"
                                                            disabled aria-disabled="true"
                                                            style="opacity:.45; pointer-events:none; min-width:140px;">
                                                        <i class="fas fa-upload btn-ico mb-1 fs-5"></i>
                                                        <span class="btn-label" style="font-size: .85rem;">Subir Lote</span>
                                                        <span class="btn-spinner"></span>
                                                    </button>
                                                </div>

                                                <div id="uploadProgressPanel" class="d-none" aria-live="polite">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span id="uploadProgressText" class="fw-semibold" style="font-size:.78rem;">Preparando archivo...</span>
                                                        <span id="uploadProgressPercent" class="fw-bold" style="font-size:.78rem;">0%</span>
                                                    </div>
                                                    <div class="progress" style="height:8px;" role="progressbar" aria-label="Progreso de carga">
                                                        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:0%;"></div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. BARRA DE ACCIÓN DE INYECCIÓN --}}
                    @if($totalPendientes > 0 && $bloqueActivo)
                        @php
                            $totalBloque   = $kpi['procesados'] + $kpi['pendientes'] + $kpi['anulados'];
                            $pctProcesado  = $totalBloque > 0 ? round(($kpi['procesados'] / $totalBloque) * 100) : 0;
                        @endphp
                        <div class="action-bar mb-4 position-relative shadow-sm" style="background: var(--c-surface); border: 1px solid var(--c-border); border-radius: var(--r-xl); z-index: 50;">
                            <div class="action-bar-progress" style="height: 3px; border-radius: var(--r-xl) var(--r-xl) 0 0;">
                                <div class="action-bar-progress-fill" id="progressFill"
                                     style="width: {{ $pctProcesado }}%;"
                                     title="{{ $pctProcesado }}% procesado"></div>
                            </div>

                            <div class="p-3 p-md-4 pb-5 d-flex flex-column gap-4">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background-color: var(--c-primary-soft); border: 1px dashed #c7d2fe;">
                                    <i class="fas fa-info-circle mt-1" style="color: var(--c-primary); font-size: 1.1rem;"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1" style="color: var(--c-primary-h); font-size: .85rem;">
                                            ¿Por qué es necesario procesar este lote?
                                        </h6>
                                        <p class="mb-0" style="font-size: .75rem; color: #3730a3; line-height: 1.5;">
                                            Las facturas que subiste están en una <strong>zona de prueba temporal (Staging)</strong>. Para que afecten la cartera del ERP, debes asignarles su modelo contable. El sistema validará los terceros y trasladará los datos para convertirlos en <strong>operaciones oficiales</strong>.
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="pulse-dot mt-2" style="width: 10px; height: 10px;"></div>
                                        <div>
                                            <h3 class="fw-bold text-dark mb-1" style="font-size: 1.15rem; letter-spacing: -0.02em;">
                                                Listo para inyectar <span style="color: var(--c-primary);">{{ number_format($totalPendientes, 0, ',', '.') }}</span> registros
                                            </h3>
                                            <p class="mb-0 text-warning fw-semibold" style="font-size: .75rem;">
                                                <i class="fas fa-lock me-1"></i> Acción irreversible en API Cartera.
                                            </p>
                                        </div>
                                    </div>

                                    <form action="{{ route('certificados.ingesta.inyectar') }}"
                                          method="POST" id="formInyeccion"
                                          class="d-flex flex-wrap align-items-center justify-content-end gap-2 m-0 ms-auto">
                                        @csrf
                                        <input type="hidden" name="bloque_origen" value="{{ $bloqueActivo }}">
                                        <input type="hidden" name="progreso_token" id="progresoTokenInyeccion">

                                        <div data-tooltip="1. Estado de la operación">
                                            <select name="id_car_sia_estados"
                                                    class="select-chip"
                                                    required
                                                    aria-label="Estado"
                                                    style="padding-top: 0.45rem; padding-bottom: 0.45rem;"
                                                    onchange="alert('En esta etapa de inyección solo se permite la opción: ' + this.options[0].text); this.selectedIndex = 0;">
                                                @foreach($estados as $estado)
                                                    <option value="{{ $estado->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div data-tooltip="2. Tipo de operación">
                                            <select name="id_car_sia_tipos"
                                                    class="select-chip"
                                                    required
                                                    aria-label="Tipo"
                                                    style="padding-top: 0.45rem; padding-bottom: 0.45rem;"
                                                    onchange="alert('En esta etapa de inyección solo se permite la opción: ' + this.options[0].text); this.selectedIndex = 0;">
                                                @foreach($tipos as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button type="button" class="btn-g btn-primary-g rounded-pill ms-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmarInyeccion" style="padding: 0.45rem 1.4rem;">
                                            <i class="fas fa-paper-plane btn-ico" style="font-size: .85rem;"></i>
                                            <span class="btn-label">Procesar Lote</span>
                                        </button>
                                    </form>

                                    <div id="progresoInyeccion" class="d-none w-100" aria-live="polite">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span id="textoProgresoInyeccion" class="fw-semibold" style="font-size:.78rem;">Preparando el lote...</span>
                                            <span id="porcentajeProgresoInyeccion" class="fw-bold" style="font-size:.78rem;">0%</span>
                                        </div>
                                        <div class="progress" style="height:8px;" role="progressbar" aria-label="Progreso de procesamiento del lote">
                                            <div id="progresoInyeccionFill" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:0%; transition:width .6s ease;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- BANNER DE ÉXITO MINIMALISTA CUANDO YA NO HAY PENDIENTES --}}
                    @elseif(isset($bloqueActivo) && isset($kpi['total_registros']) && $kpi['total_registros'] > 0 && ($kpi['pendientes'] ?? 0) == 0)
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-3 mb-4 shadow-sm" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">

                            {{-- Mensaje Compacto --}}
                            <div class="d-flex align-items-center gap-2 mb-3 mb-md-0">
                                <i class="fas fa-check-circle text-success fs-5"></i>
                                <div style="color: #14532d; font-size: 0.95rem;">
                                    <strong>¡Proceso completado!</strong>
                                    <span style="color: #166534; opacity: 0.85;">
                                        Se generaron {{ number_format($kpi['procesados'] ?? 0, 0, ',', '.') }} operaciones.
                                    </span>
                                </div>
                            </div>

                            {{-- Botón Pequeño --}}
                            <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}"
                               class="btn btn-sm d-inline-flex align-items-center text-white text-decoration-none shadow-sm"
                               style="background: #10b981; border: none; border-radius: 6px; padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 500; transition: background 0.2s;"
                               onmouseover="this.style.background='#059669';"
                               onmouseout="this.style.background='#10b981';">
                                Ir a Operaciones <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif

                    {{-- 7. TABLA DE AUDITORÍA --}}
                    <div class="card-g overflow-hidden mb-4 shadow-sm" id="tableCard" style="border: 1px solid var(--c-border); border-radius: var(--r-xl);">
                        <div class="px-4 py-3 border-bottom d-flex gap-3 align-items-start" style="background-color: #f8fafc;">
                            <div class="mt-1">
                                <i class="fas fa-clipboard-check fs-5" style="color: var(--c-primary);"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark" style="font-size: .9rem;">Auditoría de Lote (Zona Staging)</h6>
                                <p class="mb-0 text-muted" style="font-size: .8rem; line-height: 1.5; max-width: 900px;">
                                    Este visor muestra los datos exactos que el sistema leyó de tu archivo Excel. Su objetivo es permitirte <strong>revisar la información</strong> y <strong>excluir registros específicos</strong> (con el botón de la papelera) si detectas alguna anomalía antes de procesar el bloque completo.
                                </p>
                            </div>
                        </div>

                        <div class="table-header-strip bg-white px-4 py-3 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

                            {{-- Botón colapsable del lado izquierdo --}}
                            <button class="table-toggle-btn border-0 bg-transparent p-0 d-flex align-items-center gap-2 text-dark"
                                    id="tableToggleBtn"
                                    type="button"
                                    aria-expanded="true"
                                    aria-controls="tableCollapse"
                                    title="Mostrar u ocultar registros"
                                    style="outline: none;">
                                <span class="toggle-arrow" style="color: var(--c-muted);"><i class="fas fa-chevron-down"></i></span>
                                <span class="fw-bold" style="font-size: .95rem;">Registros del Bloque</span>
                                <span class="badge-g badge-info ms-1" style="font-size: .7rem;">API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-muted fw-normal" style="font-size:.75rem;">({{ number_format($lotesCrudos->total(), 0, ',', '.') }} en total)</span>
                            </button>

                            {{-- Lado derecho: Filtros + BOTÓN ANULAR LOTE --}}
                            <div class="d-flex flex-wrap gap-2 align-items-center m-0">

                                {{-- BOTÓN ANULAR LOTE COMPLETO (Aparece solo si hay pendientes) --}}
                                @if(isset($bloqueActivo) && ($kpi['pendientes'] ?? 0) > 0)
                                    <form action="{{ route('certificados.ingesta.anular_bloque', $bloqueActivo) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('¿Estás SEGURO de querer ANULAR todo el Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}?\n\nEsta acción marcará todos los registros pendientes como anulados y no se inyectarán al sistema. No afectará a los que ya fueron procesados.')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-g shadow-sm me-2"
                                                style="padding: 0.4rem 0.8rem; font-size: .85rem; background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; transition: all 0.2s;"
                                                onmouseover="this.style.background='#fee2e2'; this.style.borderColor='#f87171';"
                                                onmouseout="this.style.background='#fef2f2'; this.style.borderColor='#fca5a5';"
                                                data-tooltip="Excluir masivamente los registros pendientes">
                                            <i class="fas fa-trash-alt me-1"></i> <span class="hide-sm">Anular Lote</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Buscador y filtros --}}
                                <form action="{{ route('certificados.ingesta.index') }}" method="GET" id="formFiltros" class="d-flex flex-wrap gap-2 align-items-center m-0">
                                    <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

                                    <select name="estado" class="input-g shadow-sm text-muted"
                                            style="min-width:140px; cursor:pointer; font-size:.85rem; padding: 0.4rem 1rem;"
                                            onchange="this.form.submit()"
                                            aria-label="Filtrar por estado">
                                        <option value="">Todos los estados</option>
                                        <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>Pendientes</option>
                                        <option value="PROCESADO" {{ request('estado') == 'PROCESADO' ? 'selected' : '' }}>Procesados</option>
                                        <option value="ANULADO" {{ request('estado') == 'ANULADO' ? 'selected' : '' }}>Anulados</option>
                                    </select>

                                    <div class="position-relative">
                                        <i class="fas fa-search position-absolute top-50 translate-middle-y" style="left:.85rem; color:#94a3b8; font-size:.8rem; pointer-events:none;"></i>
                                        <input type="text" name="buscar_cedula" id="inputBusqueda"
                                               class="input-g shadow-sm"
                                               style="padding-left:2.3rem; min-width:240px; font-size:.85rem; padding-top: 0.4rem; padding-bottom: 0.4rem;"
                                               placeholder="Buscar NIT, nombre o factura…"
                                               value="{{ request('buscar_cedula') }}"
                                               aria-label="Buscar en este lote">
                                    </div>

                                    @if(request('buscar_cedula') || request('estado'))
                                        <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm text-decoration-none" title="Limpiar filtros" style="padding: 0.4rem 0.8rem; font-size: .85rem;">
                                            <i class="fas fa-times me-1"></i> <span class="hide-sm">Limpiar</span>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <div class="collapsible-table show" id="tableCollapse">
                            <!-- Contenedor scrolleable: max-height para scroll vertical, table-responsive para horizontal -->
                            <div class="table-responsive border rounded shadow-sm bg-white mb-3" style="max-height: 65vh; overflow: auto;">

                                <!--
                                table-sm: Padding ultra reducido (filas angostas)
                                text-nowrap: Evita saltos de línea (estilo Excel)
                                table-hover: Resalta fila
                                align-middle: Centra verticalmente
                                -->
                                <table class="table table-sm table-bordered table-hover text-nowrap align-middle mb-0" style="font-size: 0.75rem;" aria-label="Registros del bloque de ingesta">

                                    <!-- THEAD CONGELADO (position-sticky top-0) -->
                                    <thead class="position-sticky top-0 align-top" style="z-index: 15;">

                                        <!-- PRIMERA FILA: Categorías -->
                                        <tr class="text-center text-uppercase bg-light text-secondary" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                            <!-- Grupo Operación (Congelado a la izquierda) -->
                                            <th colspan="3" class="position-sticky border-end border-2 bg-light" style="left: 0; z-index: 20; box-shadow: 2px 0 4px -1px rgba(0,0,0,0.1);">Siasoft</th>

                                            <th colspan="3" class="border-end border-2 bg-light">Sistema</th>
                                            <th colspan="6" class="border-end border-2 bg-light">Tercero / Cuenta</th>
                                            <th colspan="7" class="border-end border-2 bg-light">Detalle Documento</th>
                                            <th colspan="4" class="border-end border-2 bg-light">Fechas</th>

                                            <!-- Resalte de color para Valores -->
                                            <th colspan="4" class="border-end border-2 bg-info bg-opacity-10 text-primary">Valores Financieros</th>

                                            <th colspan="2" class="border-end border-2 bg-light">Bancos</th>
                                            <th colspan="6" class="border-end border-2 bg-light">Auditoría</th>
                                            <th colspan="2" class="bg-light">Trazabilidad</th>
                                        </tr>

                                        <!-- SEGUNDA FILA: Columnas -->
                                        <tr class="bg-light text-secondary" style="font-size: 0.7rem;">

                                            <!-- CONGELADAS IZQUIERDA (z-index: 20 para sobreponerse al scroll) -->
                                            <th class="text-center position-sticky bg-light border-end-0" style="left: 0; width: 40px; min-width: 40px; max-width: 40px; z-index: 20;">#</th>
                                            <th class="text-center position-sticky bg-light border-end-0" style="left: 40px; width: 70px; min-width: 70px; max-width: 70px; z-index: 20;">Acción</th>
                                            <th class="position-sticky bg-light border-end border-2 text-dark" style="left: 90px; width: 110px; min-width: 110px; max-width: 110px; z-index: 20; box-shadow: 2px 0 4px -1px rgba(0,0,0,0.1);">Factura</th>

                                            <!-- Sistema -->
                                            <th>Bloque</th>
                                            <th>Estado</th>
                                            <th class="text-center border-end border-2">Sel.</th>

                                            <!-- Tercero / Cuenta -->
                                            <th>Cuenta</th>
                                            <th>Nom. Cuenta</th>
                                            <th>NIT Base</th>
                                            <th>NIT (Tercero)</th>
                                            <th>Razón Social</th>
                                            <th class="border-end border-2">Tercero CCO</th>

                                            <!-- Documento -->
                                            <th class="text-center">Tipo</th>
                                            <th>Doc Mov</th>
                                            <th>CCO</th>
                                            <th>TRN</th>
                                            <th>Nº Doc</th>
                                            <th>Pagaré</th>
                                            <th class="text-center border-end border-2">Cuota</th>

                                            <!-- Fechas -->
                                            <th class="text-center">Año</th>
                                            <th class="text-center">Mes</th>
                                            <th class="text-center">Vence</th>
                                            <th class="text-center border-end border-2">TRN Banco</th>

                                            <!-- Valores (Fondo sutil tintado) -->
                                            <th class="text-end bg-info bg-opacity-10">V. Inicial</th>
                                            <th class="text-end bg-info bg-opacity-10">V. Pago Ofic</th>
                                            <th class="text-end bg-info bg-opacity-10 text-dark">Valor Neto</th>
                                            <th class="text-end bg-info bg-opacity-10 border-end border-2">Valor Banco</th>

                                            <!-- Bancos -->
                                            <th>Banco</th>
                                            <th class="border-end border-2">UID Banco</th>

                                            <!-- Auditoría -->
                                            <th class="text-center">Contab.</th>
                                            <th>Nota</th>
                                            <th>Detalle</th>
                                            <th>Log RQ</th>
                                            <th>ID Cab</th>
                                            <th class="border-end border-2">ID Reg Ref</th>

                                            <!-- Trazabilidad -->
                                            <th class="text-center">Fecha AD</th>
                                            <th class="text-center">Fecha Edit</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($lotesCrudos as $index => $lote)
                                        <tr>
                                            <!-- CONGELADAS IZQUIERDA -->
                                            <td class="text-center text-muted fw-medium position-sticky bg-light border-end-0" style="left: 0; width: 40px; min-width: 40px; max-width: 40px; z-index: 5;">
                                                {{ $lotesCrudos->firstItem() + $index }}
                                            </td>
                                            <td class="text-center position-sticky bg-light border-end-0" style="left: 40px; width: 50px; min-width: 50px; max-width: 50px; z-index: 5;">
                                                @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                                    <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('¿Excluir factura #{{ $lote->id_factura }}?')">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-sm p-0 m-0 border-0 bg-transparent text-danger" title="Excluir">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <i class="fas fa-lock text-secondary opacity-50" title="Bloqueado"></i>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-dark position-sticky bg-white border-end border-2" style="left: 90px; width: 110px; min-width: 110px; max-width: 110px; z-index: 5; box-shadow: 2px 0 4px -1px rgba(0,0,0,0.1);">
                                                {{ $lote->id_factura ?? '—' }}
                                            </td>

                                            <!-- Sistema -->
                                            <td class="text-muted font-monospace">API-{{ str_pad($lote->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                @if($lote->anular == 1)
                                                    <span class="text-danger fw-semibold"><span class="d-inline-block rounded-circle bg-danger me-1" style="width:6px; height:6px;"></span>Anulado</span>
                                                @elseif($lote->estado == 'PROCESADO')
                                                    <span class="text-success fw-semibold"><span class="d-inline-block rounded-circle bg-success me-1" style="width:6px; height:6px;"></span>Procesado</span>
                                                @else
                                                    <span class="text-warning fw-semibold text-darken"><span class="d-inline-block rounded-circle bg-warning me-1" style="width:6px; height:6px;"></span>Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted border-end border-2">{{ $lote->is_selected ?? '0' }}</td>

                                            <!-- Tercero / Cuenta -->
                                            <td class="text-muted">{{ $lote->cuenta ?? '—' }}</td>
                                            <td><span class="d-inline-block text-truncate text-muted" style="max-width: 120px;" title="{{ $lote->nombre_cuenta }}">{{ $lote->nombre_cuenta ?? '—' }}</span></td>
                                            <td class="text-muted">{{ $lote->tercero_base ?? '—' }}</td>
                                            <td class="fw-bold">{{ $lote->tercero ?? '—' }}</td>
                                            <td><span class="d-inline-block text-truncate" style="max-width: 160px;" title="{{ $lote->nombre_tercero }}">{{ $lote->nombre_tercero ?? '—' }}</span></td>
                                            <td class="text-muted border-end border-2">{{ $lote->tercero_cco ?? '—' }}</td>

                                            <!-- Documento -->
                                            <td class="text-center">{{ $lote->tipo ?? '—' }}</td>
                                            <td>{{ $lote->doc_mov ?? '—' }}</td>
                                            <td>{{ $lote->cco ?? '—' }}</td>
                                            <td>{{ $lote->trn ?? '—' }}</td>
                                            <td>{{ $lote->numero_documento ?? '—' }}</td>
                                            <td>{{ $lote->pagare ?? '—' }}</td>
                                            <td class="text-center border-end border-2">{{ $lote->cuota ?? '—' }}</td>

                                            <!-- Fechas -->
                                            <td class="text-center text-muted">{{ $lote->anio ?? '—' }}</td>
                                            <td class="text-center text-muted">{{ $lote->mes ? str_pad($lote->mes, 2, '0', STR_PAD_LEFT) : '—' }}</td>
                                            <td class="text-center text-primary fw-medium">{{ $lote->fecha_venci ? \Carbon\Carbon::parse($lote->fecha_venci)->format('d/m/y') : '—' }}</td>
                                            <td class="text-center text-muted border-end border-2">{{ $lote->fecha_trn_banco ? \Carbon\Carbon::parse($lote->fecha_trn_banco)->format('d/m/y') : '—' }}</td>

                                            <!-- Valores (font-monospace para tabular, bg-light para separar) -->
                                            <td class="text-end text-muted font-monospace bg-light">${{ number_format((float)($lote->valor_inicial ?? 0), 2, ',', '.') }}</td>
                                            <td class="text-end text-muted font-monospace bg-light">${{ number_format((float)($lote->valor_pago_ofic ?? 0), 2, ',', '.') }}</td>
                                            <td class="text-end text-dark fw-bold font-monospace bg-light">${{ number_format((float)($lote->valor ?? 0), 2, ',', '.') }}</td>
                                            <td class="text-end text-muted font-monospace bg-light border-end border-2">${{ number_format((float)($lote->valor_banco ?? 0), 2, ',', '.') }}</td>

                                            <!-- Bancos -->
                                            <td><span class="d-inline-block text-truncate text-muted" style="max-width: 100px;">{{ $lote->banco ?? '—' }}</span></td>
                                            <td class="text-muted font-monospace border-end border-2" style="font-size: 0.65rem;">{{ $lote->uid_banco ?? '—' }}</td>

                                            <!-- Auditoría -->
                                            <td class="text-center">
                                                {!! $lote->contabilizado ? '<i class="fas fa-check text-success"></i>' : '<span class="text-secondary opacity-50">—</span>' !!}
                                            </td>
                                            <td><span class="d-inline-block text-truncate text-muted" style="max-width: 100px;" title="{{ $lote->nota }}">{{ $lote->nota ?? '—' }}</span></td>
                                            <td><span class="d-inline-block text-truncate text-muted" style="max-width: 100px;" title="{{ $lote->detalle }}">{{ $lote->detalle ?? '—' }}</span></td>
                                            <td><span class="d-inline-block text-truncate text-muted" style="max-width: 100px;" title="{{ $lote->log_rq }}">{{ $lote->log_rq ?? '—' }}</span></td>
                                            <td class="text-muted">{{ $lote->id_cab ?? '—' }}</td>
                                            <td class="text-muted border-end border-2">{{ $lote->id_reg_cab_ref ?? '—' }}</td>

                                            <!-- Trazabilidad -->
                                            <td class="text-center text-secondary opacity-75" style="font-size: 0.65rem;">{{ $lote->fecha_ad ? \Carbon\Carbon::parse($lote->fecha_ad)->format('d/m H:i') : '—' }}</td>
                                            <td class="text-center text-secondary opacity-75" style="font-size: 0.65rem;">{{ $lote->fecha_edit ? \Carbon\Carbon::parse($lote->fecha_edit)->format('d/m H:i') : '—' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="37" class="text-center py-5 bg-light">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-table fs-2 mb-2 text-secondary opacity-50"></i>
                                                    <h6 class="fw-bold text-dark mb-1">Sin registros</h6>
                                                    <p class="text-muted small">Carga un archivo para poblar este bloque de datos.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación Footer -->
                            @if($lotesCrudos->hasPages() || $lotesCrudos->total() > 0)
                            <div class="d-flex justify-content-between align-items-center pt-2 px-1">
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    Filas <b class="text-dark">{{ $lotesCrudos->firstItem() ?? 0 }} - {{ $lotesCrudos->lastItem() ?? 0 }}</b> de <b class="text-dark">{{ number_format($lotesCrudos->total(), 0, ',', '.') }}</b>
                                </span>
                                <div style="transform: scale(0.9); transform-origin: right center;">
                                    {{ $lotesCrudos->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- COLUMNA LATERAL (SIDEBAR FIJO UNIFICADO 50/50) --}}
                <div class="col-12 col-xl-3">
                    {{-- Contenedor Sticky Principal que contiene y divide el alto para ambas tarjetas --}}
                    <div class="d-flex flex-column gap-3" style="position: sticky; top: 1.5rem; height: calc(100vh - 3rem);">

                        {{-- 8. SIDEBAR PERIODOS --}}
                        <div class="card-g p-3 d-flex flex-column shadow-sm" style="flex: 1; min-height: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark d-flex align-items-center gap-2 m-0" style="font-size: 1.05rem;">
                                    <i class="far fa-calendar-alt text-muted" style="font-size:.9rem;"></i> Periodos
                                </h5>
                                <div class="text-end">
                                    <span class="badge bg-primary text-white shadow-sm" style="font-size:.7rem;">
                                        <i class="far fa-clock me-1"></i> {{ date('m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flip-wrapper">
                                <div class="flip-card" id="sidebarFlipCard">
                                    <div class="flip-face flip-front">
                                        <p class="text-muted mb-3 pb-2 border-bottom" style="font-size:.8rem;">Historial contable y lotes asociados.</p>

                                        <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2 mb-3">
                                            @php
                                                $periodosAgrupados = collect($periodos)->groupBy('anio')->sortKeysDesc();
                                            @endphp

                                            @forelse($periodosAgrupados as $anio => $meses)
                                                @php
                                                    // Verifica si absolutamente todos los meses de este año están abiertos
                                                    $todosAbiertos = $meses->every('abierto', true);
                                                @endphp
                                                <div class="mb-3">
                                                    {{-- HEADER DEL AÑO CON INTERRUPTOR MASIVO --}}
                                                    <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2" id="year-toggle-{{ $anio }}" data-target="year-content-{{ $anio }}">
                                                        <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                                            <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>

                                                            <div class="d-flex align-items-center gap-3">
                                                                {{-- Interruptor Masivo del Año --}}
                                                                <div onclick="event.stopPropagation();" title="{{ $todosAbiertos ? 'Cerrar todo el año' : 'Abrir todo el año' }}">
                                                                    <form action="{{ route('certificados.periodos.toggle_anio', $anio) }}" method="POST" class="m-0">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="estado" value="{{ $todosAbiertos ? 0 : 1 }}">
                                                                        <div class="form-check form-switch m-0" style="min-height: 0;">
                                                                            <input class="form-check-input" type="checkbox" onchange="this.form.submit()" {{ $todosAbiertos ? 'checked' : '' }} style="cursor:pointer; margin-top: 2px;">
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                                            </div>
                                                        </span>
                                                    </div>

                                                    <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: none;">
                                                        @foreach($meses->sortByDesc('mes') as $p)
                                                            @php
                                                                $bloquesDelPeriodo = \App\Models\Certificados\CarSiaBloque::where('id_periodo', $p->id)
                                                                    ->orderBy('numero_bloque', 'desc')
                                                                    ->get();
                                                                $esMesActual = ($anio == date('Y') && $p->mes == date('n'));
                                                                $mesTieneActivo = $bloquesDelPeriodo->contains('numero_bloque', $bloqueActivo);
                                                            @endphp

                                                            <div class="d-flex flex-column rounded mb-1 shadow-sm {{ $esMesActual ? 'mes-actual-highlight' : '' }}" style="background: var(--c-surface); border: 1px solid var(--c-border);">
                                                                <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center {{ $mesTieneActivo ? 'is-open' : '' }}"
                                                                     data-target="month-content-{{ $p->id }}"
                                                                     style="cursor: pointer; transition: background 0.2s;">

                                                                    <div>
                                                                        <div class="fw-bold {{ $esMesActual ? 'text-primary' : 'text-dark' }}" style="font-size:.8rem; line-height:1.2;">
                                                                            <i class="fas fa-chevron-right chevron-month text-muted me-1" style="font-size:.65rem; transition: transform 0.3s;"></i>
                                                                            {{ $p->nombre }}
                                                                            @if($esMesActual)
                                                                                <span class="badge bg-primary text-white ms-1 shadow-sm" style="font-size:.55rem; vertical-align:middle;">ACTUAL</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="ms-3 ps-1 mt-1" style="font-size:.65rem; color:var(--c-muted); font-family:monospace;">
                                                                            {{ $bloquesDelPeriodo->count() }} lotes registrados
                                                                        </div>
                                                                    </div>

                                                                    {{-- INTERRUPTOR INDIVIDUAL DEL MES --}}
                                                                    <div onclick="event.stopPropagation();" class="me-1">
                                                                        <form action="{{ route('certificados.periodos.toggle', $p->id) }}" method="POST" class="m-0">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="form-check form-switch m-0 d-flex flex-column align-items-end" title="Cambiar estado">
                                                                                <input class="form-check-input" type="checkbox" onchange="this.form.submit()" {{ $p->abierto ? 'checked' : '' }} style="cursor:pointer; width:28px; height:14px; margin-top:0;">
                                                                                <span style="font-size: .6rem; font-weight: bold; margin-top:2px; color: {{ $p->abierto ? 'var(--c-success)' : 'var(--c-muted)' }}">
                                                                                    {{ $p->abierto ? 'ABIERTO' : 'CERRADO' }}
                                                                                </span>
                                                                            </div>
                                                                        </form>
                                                                    </div>

                                                                </div>

                                                                <div class="month-content flex-column gap-1 p-2 pt-0 mt-1 border-top" id="month-content-{{ $p->id }}" style="display: {{ $mesTieneActivo ? 'flex' : 'none' }};">
                                                                    {{-- ... AQUÍ QUEDA EXACTAMENTE TU BUCLE DE LOS LOTES COMO LO TENÍAS ... --}}
                                                                    @if($bloquesDelPeriodo->count() > 0)
                                                                        <div class="mt-2">
                                                                            @foreach($bloquesDelPeriodo as $bloque)
                                                                                @php $esActivo = ($bloqueActivo == $bloque->numero_bloque); @endphp
                                                                                <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloque->numero_bloque]) }}"
                                                                                   onclick="mostrarCargandoLote()"
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
                                                                        <div class="mt-1 text-center" style="font-size: .65rem; color: var(--c-muted);">
                                                                            Sin lotes asignados
                                                                        </div>
                                                                    @endif
                                                                </div>
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

                        {{-- 9. SIDEBAR LOGS / TRAZABILIDAD (Minimalista) --}}
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white" style="flex: 1; min-height: 0; display: flex; flex-direction: column;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold text-dark d-flex align-items-center gap-2 m-0" style="font-size: 1.05rem;">
                                    <i class="fas fa-history text-muted" style="font-size:.9rem;"></i> Auditoría
                                </h5>
                                <div class="text-end">
                                    <span class="badge bg-light text-secondary border shadow-sm" style="font-size:.7rem; font-family: monospace;">
                                        API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-muted mb-3" style="font-size:.8rem;">Registro de eventos del lote actual.</p>

                            <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2">
                                @if(isset($logsBloque) && $logsBloque->count() > 0)
                                    <div class="position-relative ms-2" style="border-left: 2px solid var(--c-border);">
                                        @foreach($logsBloque as $log)
                                            <div class="position-relative mb-3 ps-3 pt-1">
                                                {{-- Punto del Timeline --}}
                                                <span class="position-absolute bg-primary rounded-circle border border-2 border-white shadow-sm" style="width: 12px; height: 12px; left: -7px; top: 8px;"></span>

                                                {{-- Tarjeta del Log --}}
                                                <div class="p-2 rounded bg-light border border-light shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: .75rem; line-height: 1.2;">
                                                            {{ $log->eventoAuditoria->nombre ?? 'Evento de Sistema' }}
                                                        </span>
                                                        <span class="text-muted" style="font-size: .65rem; white-space: nowrap;">
                                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '—' }}
                                                        </span>
                                                    </div>

                                                    <div class="text-muted mb-2" style="font-size: .7rem;">
                                                        <i class="fas fa-user-circle me-1 opacity-50"></i>
                                                        <span class="fw-medium text-dark">{{ $log->usuario->name ?? 'Sistema Automático' }}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <span class="badge bg-white text-secondary border shadow-none" style="font-size: .6rem; padding: .2rem .4rem;">
                                                                <i class="fas fa-sitemap me-1"></i> {{ $log->origenEvento->nombre ?? 'API' }}
                                                            </span>
                                                            @if($log->ip)
                                                                <span class="text-muted" style="font-size: .6rem; font-family: monospace;">
                                                                    {{ $log->ip }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if(!empty($log->detalles_ejecucion))
                                                            <button type="button" class="btn btn-sm text-primary p-0 m-0 border-0 bg-transparent fw-medium" style="font-size: .65rem;" onclick="document.getElementById('jsonViewer-{{ $log->id }}').classList.toggle('d-none')">
                                                                <i class="fas fa-code me-1"></i>Payload
                                                            </button>
                                                        @endif
                                                    </div>

                                                    @if(!empty($log->detalles_ejecucion))
                                                        <pre id="jsonViewer-{{ $log->id }}" class="d-none text-start p-2 mt-2 bg-dark text-light rounded mb-0 custom-scrollbar" style="font-size: .6rem; max-height: 150px; overflow-y: auto; white-space: pre-wrap;">{{ json_encode($log->detalles_ejecucion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fas fa-list-ul fs-2 mb-3 text-secondary opacity-25"></i>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: .9rem;">Sin Movimientos</h6>
                                        <p class="text-muted mb-0" style="font-size: .75rem; max-width: 200px;">No hay eventos de auditoría registrados para este lote.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 9. OVERLAY DE CARGA --}}
                <div id="loaderLoteOverlay" class="d-none position-fixed w-100 h-100 top-0 start-0 d-flex flex-column justify-content-center align-items-center" style="background: rgba(255, 255, 255, 0.85); z-index: 9999; backdrop-filter: blur(5px);">
                    <div class="p-4 rounded-4 shadow-sm bg-white d-flex flex-column align-items-center" style="border: 1px solid var(--c-border); min-width: 250px;">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Cargando Lote...</h6>
                        <p class="text-muted mb-0" style="font-size: .8rem;">Preparando los registros para revisión</p>
                    </div>
                </div>

                <script>
                    function mostrarCargandoLote() {
                        document.getElementById('loaderLoteOverlay').classList.remove('d-none');
                    }
                </script>
            </div>
        </div>
    </div>

    {{-- 10. MODALES --}}
    @if(isset($bloqueActivo) && $bloqueActivo)
    <div class="modal fade" id="modalConfirmarInyeccion" tabindex="-1" aria-labelledby="modalConfirmarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalConfirmarLabel">
                        <i class="fas fa-exclamation-circle me-2"></i> Confirmar Inyección
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <h4 class="fw-bold mb-2">¿Procesar Lote #{{ $bloqueActivo }}?</h4>
                    <p class="text-muted mb-0">
                        Se analizarán <strong>{{ number_format($totalPendientes ?? 0, 0, ',', '.') }}</strong> registros base. El sistema los <strong>agrupará por cliente único</strong> (cédula/NIT) para generar sus operaciones oficiales en Matriz de Cartera y Cobros.
                    </p>
                    <div class="alert alert-info mt-3 mb-0 text-start" role="alert" style="font-size: .85rem;">
                        <i class="fas fa-info-circle me-2"></i> Solo se inyectarán los clientes que existan en la Maestra de Terceros.
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" id="btnEjecutarInyeccion" onclick="enviarFormularioInyeccion()">
                        <i class="fas fa-check"></i> Sí, Agrupar e Inyectar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 11. SCRIPTS --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {

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

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const isOpen = tableCollapse.classList.contains('is-open');
                isOpen ? closeTable() : openTable();
            });
        }

        // 3. CHART.JS — GRÁFICO DE BARRAS INTERACTIVO
        const chartCanvas = document.getElementById('kpiChart');
        if (chartCanvas) {
            const ctx       = chartCanvas.getContext('2d');
            const dataPend  = {{ (int)($kpi['pendientes']  ?? 0) }};
            const dataProc  = {{ (int)($kpi['procesados']  ?? 0) }};
            const dataAnul  = {{ (int)($kpi['anulados']    ?? 0) }};
            const total     = dataPend + dataProc + dataAnul;

            const dataLabelsPlugin = {
                id: 'dataLabelsPlugin',
                afterDatasetsDraw(chart, args, options) {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        chart.getDatasetMeta(i).data.forEach((bar, index) => {
                            const val = dataset.data[index];
                            if (val === 0) return;

                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) + '%' : '';
                            const text = `${val.toLocaleString('es-CO')} (${pct})`;

                            ctx.save();
                            ctx.font = "bold 11px Inter, sans-serif";
                            ctx.fillStyle = "#64748b";
                            ctx.textAlign = "center";
                            ctx.textBaseline = "bottom";
                            ctx.fillText(text, bar.x, bar.y - 6);
                            ctx.restore();
                        });
                    });
                }
            };

            const baseOpts = {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 25 } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#e2e8f0' },
                        ticks: { display: false }
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
                    type: 'bar',
                    plugins: [dataLabelsPlugin],
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
        const progresoToken = document.getElementById('progresoToken');
        const uploadProgressPanel = document.getElementById('uploadProgressPanel');
        const uploadProgressBar = document.getElementById('uploadProgressBar');
        const uploadProgressText = document.getElementById('uploadProgressText');
        const uploadProgressPercent = document.getElementById('uploadProgressPercent');

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
                btnUpload.className               = 'btn-g btn-primary-g fw-bold d-flex flex-column justify-content-center align-items-center';
                btnUpload.querySelector('.btn-ico').className    = 'fas fa-cogs btn-ico fs-5 mb-1';
                btnUpload.querySelector('.btn-label').textContent = 'Subir Lote';
            });

            formUpload.addEventListener('submit', event => {
                event.preventDefault();
                const token = crypto.randomUUID();
                progresoToken.value = token;
                uploadProgressPanel.classList.remove('d-none');
                uploadProgressText.textContent = 'Procesando filas del archivo...';
                btnUpload.classList.add('is-loading');
                btnUpload.disabled             = true;
                uploadCard.style.opacity       = '0.6';
                uploadCard.style.pointerEvents = 'none';

                const consultarProgreso = async () => {
                    try {
                        const respuesta = await fetch('{{ route('certificados.ingesta.cargar.progreso') }}?token=' + encodeURIComponent(token), {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!respuesta.ok) {
                            window.setTimeout(consultarProgreso, 800);
                            return;
                        }

                        const progreso = await respuesta.json();
                        const porcentaje = Number(progreso.porcentaje || 0);
                        uploadProgressBar.style.width = porcentaje + '%';
                        uploadProgressPercent.textContent = porcentaje + '%';

                        if (progreso.total) {
                            uploadProgressText.textContent = `${Number(progreso.procesadas || 0).toLocaleString('es-CO')} de ${Number(progreso.total).toLocaleString('es-CO')} filas procesadas`;
                        }

                        if (progreso.estado === 'error') {
                            uploadProgressText.textContent = progreso.mensaje || 'Ocurrió un error al procesar el archivo.';
                            uploadProgressBar.classList.remove('progress-bar-animated');
                            return;
                        }

                        if (progreso.estado !== 'completado') {
                            window.setTimeout(consultarProgreso, 800);
                        }
                    } catch (error) {
                        window.setTimeout(consultarProgreso, 1500);
                    }
                };

                consultarProgreso();

                fetch(formUpload.action, {
                    method: 'POST',
                    body: new FormData(formUpload),
                    headers: { 'Accept': 'text/html' }
                })
                .then(respuesta => {
                    if (!respuesta.ok) throw new Error('La carga no pudo completarse.');
                    window.location.href = respuesta.url;
                })
                .catch(error => {
                    uploadProgressText.textContent = error.message;
                    uploadProgressBar.classList.remove('progress-bar-animated');
                    uploadProgressBar.classList.add('bg-danger');
                    uploadCard.style.opacity = '1';
                    uploadCard.style.pointerEvents = 'auto';
                    btnUpload.disabled = false;
                    btnUpload.classList.remove('is-loading');
                });
            });
        }

        // 5. BÚSQUEDA CON DEBOUNCE (600ms)
        const inputBusq  = document.getElementById('inputBusqueda');
        const formFilt   = document.getElementById('formFiltros');
        let   searchTimer = null;

        if (inputBusq && formFilt) {
            inputBusq.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => formFilt.submit(), 600);
            });
        }

        // 6. PROGRESS BAR (Acción inyección)
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

        // 7. ANIMACIÓN FLIP CARD EN SIDEBAR FIJO
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

        // 8. ACORDEÓN PARA LOS AÑOS Y RESALTE INTELIGENTE
        document.querySelectorAll('.year-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('is-open');
                const targetId = this.getAttribute('data-target');
                const content = document.getElementById(targetId);
                if(content.style.display === 'none') {
                    content.style.display = 'flex';
                } else {
                    content.style.display = 'none';
                }
            });
        });

        // 9. ACORDEÓN DE MESES
        document.querySelectorAll('.month-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('is-open');
                const targetId = this.getAttribute('data-target');
                const content = document.getElementById(targetId);
                if (content.style.display === 'none') {
                    content.style.display = 'flex';
                } else {
                    content.style.display = 'none';
                }
            });
        });

        // Auto-abrir el año que contiene el bloque que actualmente estamos viendo
        document.querySelectorAll('.year-content').forEach(content => {
            if(content.querySelector('.active-block')) {
                content.style.display = 'flex';
                const btnId = content.id.replace('year-content-', 'year-toggle-');
                const btn = document.getElementById(btnId);
                if(btn) {
                    btn.classList.add('is-open');
                    const icon = btn.querySelector('.fa-folder');
                    if(icon) { icon.classList.remove('fa-folder'); icon.classList.add('fa-folder-open'); }
                }
            } else {
                content.style.display = 'none';
            }
        });

        // Asegurarnos de que el modal de inyección esté en el body (para evitar problemas de Z-index)
        const modalInyeccion = document.getElementById('modalConfirmarInyeccion');
        if (modalInyeccion) {
            document.body.appendChild(modalInyeccion);
        }
    });

    // SCRIPT GLOBAL PARA GESTIONAR LA INYECCIÓN
    function enviarFormularioInyeccion() {
        let btn = document.getElementById('btnEjecutarInyeccion');
        let form = document.getElementById('formInyeccion');
        if(!form) return;

        let token = crypto.randomUUID();
        let barra = document.getElementById('progresoInyeccion');
        let barraRelleno = document.getElementById('progresoInyeccionFill');
        let texto = document.getElementById('textoProgresoInyeccion');
        let porcentajeTexto = document.getElementById('porcentajeProgresoInyeccion');

        document.getElementById('progresoTokenInyeccion').value = token;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando clientes...';

        if (barra) barra.classList.remove('d-none');

        const consultarProgreso = async () => {
            try {
                const respuesta = await fetch('{{ route('certificados.ingesta.cargar.progreso') }}?token=' + encodeURIComponent(token), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!respuesta.ok) {
                    window.setTimeout(consultarProgreso, 800);
                    return;
                }

                const progreso = await respuesta.json();
                const porcentaje = Number(progreso.porcentaje || 0);
                if (barraRelleno) barraRelleno.style.width = porcentaje + '%';
                if (porcentajeTexto) porcentajeTexto.textContent = porcentaje + '%';
                if (texto) texto.textContent = progreso.mensaje || 'Procesando lote...';

                if (progreso.estado !== 'completado' && progreso.estado !== 'error') {
                    window.setTimeout(consultarProgreso, 800);
                }
            } catch (error) {
                window.setTimeout(consultarProgreso, 1500);
            }
        };

        consultarProgreso();

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'text/html' }
        }).then(respuesta => {
            if (!respuesta.ok) throw new Error('La inyección no pudo completarse.');
            window.location.href = respuesta.url;
        }).catch(error => {
            if (texto) texto.textContent = error.message;
            if (barraRelleno) barraRelleno.classList.add('bg-danger');
            btn.disabled = false;
        });
    }
    </script>
</x-base-layout>
