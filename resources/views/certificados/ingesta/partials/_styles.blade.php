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

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 767px) {
        .hide-sm { display: none !important; }
        .action-bar-body { gap: .75rem; }
        .kpi-number { font-size: 1.35rem; }
        .sticky-sidebar { position: static; min-height: auto; }
        .flip-wrapper { min-height: 500px; }
    }
</style>
