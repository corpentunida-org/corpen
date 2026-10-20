{{-- tab-reservas.blade.php · v25 --}}
{{-- NOVEDADES v24:
   · Titular: autocomplete real (input + dropdown flotante, sin lista visible).
     Chip con el titular seleccionado + botón X para borrar.
   · Origen: preselecciona la 3ra opción (index 2).
   · Monto: se auto-llena con precio sugerido (o mínimo si no hay tarifa)
     al completar la selección del rango.
   · Comentario: chips de respuestas rápidas con frases comunes
     (incluye el nombre del usuario logueado).
--}}

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
        padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle;
    }
    .reservas-soft .table thead th {
        font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important; white-space: nowrap;
    }
    .reservas-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums; white-space: nowrap;
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
    .reservas-soft .btn-green:disabled { opacity: .6; cursor: progress; }

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
        border-top: 1px solid var(--grid); margin-top: 8px; padding-top: 7px;
    }

    /* ─── MODAL (fuera de .reservas-soft, scoped por #modalNuevaReserva) ─── */
    #modalNuevaReserva .modal-content { border-radius: 6px; border: 1px solid #e9eef4; }
    #modalNuevaReserva .modal-header {
        padding: 6px 10px; border-bottom: 1px solid #e9eef4; background: #fafbfd;
    }
    #modalNuevaReserva .modal-title { font-size: 9.5px; font-weight: 700; color: #475569; }
    #modalNuevaReserva .modal-body { padding: 10px; }
    #modalNuevaReserva .modal-footer {
        padding: 6px 10px; border-top: 1px solid #e9eef4; background: #fafbfd;
    }
    #modalNuevaReserva .form-label {
        font-size: 7.5px; font-weight: 700; color: #94a3b8;
        margin-bottom: 2px; letter-spacing: .04em; text-transform: uppercase;
    }
    #modalNuevaReserva .form-control,
    #modalNuevaReserva .form-select,
    #modalNuevaReserva textarea {
        font-size: 9px; color: #475569; background: #fff;
        border: 1px solid #e9eef4; border-radius: 3px;
        padding: 3px 7px; box-shadow: none !important; outline: 0;
        font-family: inherit;
    }
    #modalNuevaReserva .form-control:focus,
    #modalNuevaReserva .form-select:focus,
    #modalNuevaReserva textarea:focus { border-color: #c8d6cd; }
    #modalNuevaReserva .form-text { font-size: 7.5px; color: #b3bcc7; margin-top: 2px; }
    #modalNuevaReserva .invalid-feedback { font-size: 7.5px; }
    #modalNuevaReserva .was-validated .form-control:invalid,
    #modalNuevaReserva .was-validated .form-select:invalid { border-color: #dc3545; }
    #modalNuevaReserva .nr-alert {
        font-size: 8.5px; padding: 5px 8px; border-radius: 3px; margin: 0;
    }

    /* 🆕 v24 · AUTOCOMPLETE DE TITULAR */
    #modalNuevaReserva .user-search-wrap {
        position: relative;
    }
    #modalNuevaReserva .user-search-input {
        width: 100%;
        font-size: 9px;
        color: #475569;
        background: #fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 16 16'><path fill='none' stroke='%2394a3b8' stroke-width='2' d='M11 11l4 4M7 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/></svg>") no-repeat 6px center;
        padding: 3px 8px 3px 22px;
        border: 1px solid #e9eef4;
        border-radius: 3px;
        box-shadow: none !important;
        outline: 0;
        font-family: inherit;
    }
    #modalNuevaReserva .user-search-input:focus { border-color: #c8d6cd; }
    #modalNuevaReserva .user-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e9eef4;
        border-radius: 3px;
        max-height: 180px;
        overflow-y: auto;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
        margin-top: 2px;
    }
    #modalNuevaReserva .user-dropdown::-webkit-scrollbar { width: 5px; }
    #modalNuevaReserva .user-dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    #modalNuevaReserva .user-dropdown-item {
        padding: 4px 8px;
        font-size: 9px;
        color: #475569;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        line-height: 1.3;
    }
    #modalNuevaReserva .user-dropdown-item:last-child { border-bottom: 0; }
    #modalNuevaReserva .user-dropdown-item:hover,
    #modalNuevaReserva .user-dropdown-item.active {
        background: #f0f7f2;
        color: #3d7a5c;
        font-weight: 600;
    }
    #modalNuevaReserva .user-empty-msg {
        padding: 6px 8px;
        font-size: 8.5px;
        color: #b3bcc7;
        font-style: italic;
        text-align: center;
    }
    #modalNuevaReserva .user-selected {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        margin-top: 4px;
        background: #f0f7f2;
        border: 1px solid #d5e7dc;
        border-radius: 3px;
        font-size: 9px;
        color: #3d7a5c;
        font-weight: 600;
    }
    #modalNuevaReserva .user-selected i.bi-person-check-fill {
        font-size: 9px;
    }
    #modalNuevaReserva .user-selected .user-selected-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #modalNuevaReserva .user-selected-clear {
        cursor: pointer;
        color: #94a3b8;
        font-size: 8px;
        padding: 0 2px;
    }
    #modalNuevaReserva .user-selected-clear:hover { color: #dc3545; }
    #modalNuevaReserva .user-count {
        font-size: 7.5px;
        color: #b3bcc7;
        margin-top: 2px;
    }
    #modalNuevaReserva select.user-hidden {
        display: none !important;
    }

    /* 🆕 v24 · CHIPS DE COMENTARIO (respuestas rápidas) */
    #modalNuevaReserva .comentario-chips {
        display: flex; flex-wrap: wrap; gap: 4px;
        margin-bottom: 4px;
    }
    #modalNuevaReserva .com-chip {
        padding: 2px 8px;
        font-size: 7.5px;
        font-weight: 500;
        border: 1px solid #e9eef4;
        border-radius: 10px;
        color: #475569;
        background: #fcfdfe;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
        transition: all 0.15s;
    }
    #modalNuevaReserva .com-chip i {
        font-size: 7px;
        margin-right: 2px;
        color: #94a3b8;
    }
    #modalNuevaReserva .com-chip:hover {
        background: #f0f7f2;
        color: #3d7a5c;
        border-color: #d5e7dc;
    }
    #modalNuevaReserva .com-chip:hover i {
        color: #3d7a5c;
    }
    #modalNuevaReserva .com-chip.added {
        background: #e3f1e9;
        color: #34684e;
        border-color: #b9d4c5;
    }
    #modalNuevaReserva .com-chip.added i {
        color: #3d7a5c;
    }

    /* ─── MINI CALENDARIO ─── */
    #modalNuevaReserva .cal-wrap {
        border: 1px solid #f1f5f9; border-radius: 4px;
        background: #fcfdfe; padding: 6px;
    }
    #modalNuevaReserva .cal-title {
        font-size: 9px; font-weight: 700; color: #475569;
        letter-spacing: .02em; text-transform: capitalize;
    }
    #modalNuevaReserva .cal-grid {
        display: grid; grid-template-columns: repeat(7, 1fr);
        gap: 1px; background: #f1f5f9;
        border: 1px solid #f1f5f9; border-radius: 3px; overflow: hidden;
    }
    #modalNuevaReserva .cal-row { display: contents; }
    #modalNuevaReserva .cal-row-head .cal-dow {
        background: #fafbfd;
        font-size: 7px; font-weight: 700; color: #94a3b8;
        text-align: center; padding: 2px 0; text-transform: uppercase;
        letter-spacing: .04em;
    }
    #modalNuevaReserva .cal-cell {
        background: #fff;
        font-size: 8.5px; color: #64748b;
        text-align: center; padding: 4px 0;
        cursor: pointer; user-select: none;
        transition: background 0.1s;
        font-variant-numeric: tabular-nums;
        position: relative;
    }
    #modalNuevaReserva .cal-cell.cal-empty { background: #fafbfd; cursor: default; }
    #modalNuevaReserva .cal-cell.cal-free:hover { background: #f0f7f2; }
    #modalNuevaReserva .cal-cell.cal-past {
        background: #f5f6f8; color: #c3ccd6; cursor: not-allowed;
        text-decoration: line-through;
    }
    #modalNuevaReserva .cal-cell.cal-occ {
        background: #fdeaea; color: #c08484; cursor: not-allowed; font-weight: 600;
    }
    #modalNuevaReserva .cal-cell.cal-occ::after {
        content: '×'; position: absolute; top: 0; right: 2px;
        font-size: 7px; color: #c08484;
    }
    #modalNuevaReserva .cal-cell.cal-ali {
        background: #fff4e6; color: #d97706; cursor: not-allowed; font-weight: 600;
    }
    #modalNuevaReserva .cal-cell.cal-ali::after {
        content: '🧹'; position: absolute; top: 0; right: 2px;
        font-size: 5px;
    }
    #modalNuevaReserva .cal-cell.cal-sel {
        background: #3d7a5c !important; color: #fff !important;
        font-weight: 700; border-radius: 2px;
    }
    #modalNuevaReserva .cal-cell.cal-inrange {
        background: #f0f7f2; color: #3d7a5c;
    }
    #modalNuevaReserva .cal-legend {
        display: flex; flex-wrap: wrap; gap: 6px;
        font-size: 7.5px; color: #94a3b8; font-weight: 600;
    }
    #modalNuevaReserva .cal-dot {
        display: inline-block; width: 7px; height: 7px;
        border-radius: 50%; margin-right: 3px; vertical-align: middle;
    }
    #modalNuevaReserva .cal-dot-free   { background: #fff; border: 1px solid #e9eef4; }
    #modalNuevaReserva .cal-dot-occ    { background: #fdeaea; border: 1px solid #f3d4d4; }
    #modalNuevaReserva .cal-dot-ali    { background: #fff4e6; border: 1px solid #fbd38d; }
    #modalNuevaReserva .cal-dot-sel    { background: #3d7a5c; }
    #modalNuevaReserva .cal-dot-past   { background: #f5f6f8; border: 1px solid #e4e9ee; }
    #modalNuevaReserva .cal-sel-info {
        font-size: 8.5px; color: #475569;
        background: #fafbfd; border: 1px solid #f1f5f9;
        border-radius: 3px; padding: 4px 8px;
    }
    #modalNuevaReserva .cal-info-estado {
        font-size: 8px; color: #94a3b8;
        background: #f0f7f2; border: 1px solid #d5e7dc;
        border-radius: 3px; padding: 3px 8px;
    }
    #modalNuevaReserva .cal-tarifa-box {
        font-size: 8px; color: #475569;
        background: #f4f8fc; border: 1px solid #dbe9f3;
        border-radius: 3px; padding: 4px 8px; margin-top: 4px;
    }
    #modalNuevaReserva .cal-tarifa-box b { color: #5d86a6; }
    #modalNuevaReserva .cal-tarifa-box .precio-sug { color: #3d7a5c; font-weight: 700; }
    #modalNuevaReserva .cal-tarifa-box .precio-sug small { color: #b3bcc7; font-weight: 500; }
    #modalNuevaReserva .cal-tarifa-box .tarifa-line {
        padding: 2px 0; border-bottom: 1px dotted #dbe9f3;
    }
    #modalNuevaReserva .cal-tarifa-box .tarifa-line:last-child { border-bottom: 0; }
    #modalNuevaReserva .cal-tarifa-box .tarifa-warn { color: #d97706; font-weight: 700; }
    #modalNuevaReserva .cal-tarifa-box .tarifa-err { color: #dc3545; font-weight: 700; }

    /* ─── PANEL DE DEBUG FLOTANTE ─── */
    #rvDebugPanel {
        position: fixed; bottom: 10px; right: 10px; z-index: 9999;
        width: 380px; max-width: calc(100vw - 20px); max-height: 60vh;
        background: #0f172a; color: #e2e8f0;
        border: 1px solid #1e293b; border-radius: 6px;
        font-family: 'Courier New', monospace; font-size: 9px;
        display: none; flex-direction: column;
        box-shadow: 0 8px 24px rgba(0,0,0,.4);
    }
    #rvDebugPanel.open { display: flex; }
    #rvDebugPanel .dbg-header {
        background: #1e293b; padding: 5px 8px; border-radius: 6px 6px 0 0;
        display: flex; justify-content: space-between; align-items: center;
        cursor: pointer;
    }
    #rvDebugPanel .dbg-header b { font-size: 9.5px; color: #f1f5f9; }
    #rvDebugPanel .dbg-controls { display: flex; gap: 4px; }
    #rvDebugPanel .dbg-btn {
        background: #334155; color: #f1f5f9; border: 0;
        font-size: 8px; padding: 1px 6px; border-radius: 2px; cursor: pointer;
        font-family: inherit;
    }
    #rvDebugPanel .dbg-btn:hover { background: #475569; }
    #rvDebugPanel .dbg-body { flex: 1; overflow-y: auto; padding: 6px 8px; }
    #rvDebugPanel .dbg-body::-webkit-scrollbar { width: 6px; }
    #rvDebugPanel .dbg-body::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    #rvDebugPanel .dbg-log {
        padding: 3px 0; border-bottom: 1px solid #1e293b; line-height: 1.3;
        word-wrap: break-word; white-space: pre-wrap;
    }
    #rvDebugPanel .dbg-log:last-child { border-bottom: 0; }
    #rvDebugPanel .dbg-log .dbg-time { color: #64748b; font-size: 8px; }
    #rvDebugPanel .dbg-log.info  { color: #93c5fd; }
    #rvDebugPanel .dbg-log.ok    { color: #86efac; }
    #rvDebugPanel .dbg-log.warn  { color: #fcd34d; }
    #rvDebugPanel .dbg-log.err   { color: #fca5a5; }
    #rvDebugPanel .dbg-log.debug { color: #c4b5fd; }
    #rvDebugToggle {
        position: fixed; bottom: 10px; right: 10px; z-index: 10000;
        background: #1e293b; color: #f1f5f9; border: 0;
        border-radius: 50%; width: 36px; height: 36px;
        cursor: pointer; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,.3);
        display: flex; align-items: center; justify-content: center;
    }
    #rvDebugToggle:hover { background: #334155; }

    /* v25 - POPUP DE INFO DE RESERVA (al clicar dia bloqueado) */
    .cal-info-popup {
        position: fixed; z-index: 1080;
        background: #fff; border: 1px solid #e9eef4; border-radius: 5px;
        box-shadow: 0 6px 20px rgba(0,0,0,.18);
        min-width: 240px; max-width: 320px;
        font-size: 8.5px; color: #475569; font-family: inherit;
        animation: calInfoFadeIn 0.15s ease;
    }
    @keyframes calInfoFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .cal-info-popup .cal-info-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 5px 8px; background: #fafbfd;
        border-bottom: 1px solid #e9eef4; border-radius: 5px 5px 0 0;
    }
    .cal-info-popup .cal-info-header b { font-size: 9px; color: #475569; }
    .cal-info-popup .cal-info-close {
        cursor: pointer; color: #94a3b8; font-size: 9px; padding: 0 4px; line-height: 1;
    }
    .cal-info-popup .cal-info-close:hover { color: #dc3545; }
    .cal-info-popup .cal-info-body { padding: 6px 8px; }
    .cal-info-popup .cal-info-row {
        padding: 3px 0; line-height: 1.4; display: flex; align-items: flex-start; gap: 5px;
        border-bottom: 1px solid #f8fafc;
    }
    .cal-info-popup .cal-info-row:last-child { border-bottom: 0; }
    .cal-info-popup .cal-info-row i {
        font-size: 8px; color: #94a3b8; margin-top: 1px; width: 12px; text-align: center; flex-shrink: 0;
    }
    .cal-info-popup .cal-info-label {
        color: #64748b; font-weight: 600; min-width: 48px; flex-shrink: 0;
    }
    .cal-info-popup .cal-info-value {
        color: #1e293b; font-weight: 500; word-break: break-word; flex: 1;
    }
</style>


@php
    /* ══════════════════════════════════════════════════════════════════════
       tab-reservas.blade.php · v24 · PHP BACKEND con DEBUG INFO
       ══════════════════════════════════════════════════════════════════════ */

    // ─── 0 · Valores por defecto ─────────────────────────────────────
    $xlsInm = null; $xlsInmId = null; $modoGlobal = true;
    $dsCol = collect(); $dsStatuses = collect();
    $dsTotalContexto = 0;
    $dsStatusesJson = '[]'; $dsJson = '[]';
    $kpiActivas = null; $kpiMontoMes = null; $kpiOcupacion = null;
    $agendaHoy = collect();
    $hoy = now()->startOfDay();

    $phpDebugLogs = [];

    // ─── 0b · FECHA PUNTUAL DE LA AGENDA ─────────────────────────────
    $agendaFechaRaw = request('agenda_fecha');
    try {
        $agendaFecha = $agendaFechaRaw
            ? \Illuminate\Support\Carbon::parse($agendaFechaRaw)->startOfDay()
            : $hoy->copy();
    } catch (\Throwable $e) {
        $agendaFecha = $hoy->copy();
    }
    $esAgendaHoy = $agendaFecha->isSameDay($hoy);

    // ─── 1 · Contexto del inmueble ───────────────────────────────────
    if (isset($inmueble) && $inmueble) {
        $xlsInm = $inmueble;
        $xlsInmId = is_object($xlsInm) ? ($xlsInm->id ?? null) : $xlsInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $xlsInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $xlsInmId = request('inmueble_id');
    }
    $modoGlobal = empty($xlsInmId);
    $phpDebugLogs[] = ['info', 'Modo: ' . ($modoGlobal ? 'global' : 'single') . ' · inmuebleId=' . ($xlsInmId ?? 'null')];

    // ─── 2 · Dataset completo del contexto ──────────────────────────
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
    $phpDebugLogs[] = ['info', 'Statuses cargados: ' . $dsStatuses->count()];

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
            $phpDebugLogs[] = ['ok', 'Reservas cargadas: ' . $dsCol->count() . ' / total contexto: ' . $dsTotalContexto];
        } else {
            $phpDebugLogs[] = ['err', 'Clase App\Models\Rsv\Reserva NO existe'];
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('tab-reservas dataset: '.$e->getMessage());
        $phpDebugLogs[] = ['err', 'Error cargando reservas: ' . $e->getMessage()];
        $dsCol = collect();
    }

    // ─── 2b · Fallback desde el paginador del dashboard ──────────────
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
                $phpDebugLogs[] = ['info', 'Fallback desde $reservas: ' . $dsCol->count() . ' items'];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('tab-reservas fallback: '.$e->getMessage());
        }
    }

    // ─── 2c · Serialización segura (raw + flags HEX) ──────────────────
    $dsJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $dsJson = json_encode($dsCol->values()->all(), $dsJsonFlags);
    if ($dsJson === false || $dsJson === null) { $dsJson = '[]'; }
    $dsStatusesJson = json_encode(
        $dsStatuses->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
        $dsJsonFlags
    );
    if ($dsStatusesJson === false || $dsStatusesJson === null) { $dsStatusesJson = '[]'; }

    // ─── 2d · Estados NO-bloqueantes (Cancelada/Rechazada/Anulada) ─────
    // 🆕 v25.1 · Se calculan una sola vez y se aplican a KPIs y Agenda,
    //            para ser consistente con el controlador v27.1 que las
    //            excluye de fechasOcupadasAjax() y de las validaciones.
    //            Si una reserva está cancelada NO cuenta como ACTIVA, no
    //            suma al monto del mes ni a la ocupación.
    $estadosNoBloqueantesIds = [];
    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $estadosNoBloqueantesIds = \App\Models\Rsv\Status::where(function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%cancela%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%rechaz%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%anulad%']);
            })->pluck('id')->toArray();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('tab-reservas · estadosNoBloqueantes: '.$e->getMessage());
    }
    $phpDebugLogs[] = [
        empty($estadosNoBloqueantesIds) ? 'warn' : 'ok',
        'Estados NO-bloqueantes (excluidos de KPIs/Agenda): ' . (empty($estadosNoBloqueantesIds) ? 'NINGUNO' : 'IDs=' . implode(',', $estadosNoBloqueantesIds))
    ];

    // ─── 3 · KPIs reales (excluyendo reservas canceladas) ──────────
    // 🆕 v25.1 · Las reservas con estado Cancelada/Rechazada/Anulada
    //            NO cuentan para ACTIVAS, MES ni OCUP., porque no están
    //            vigentes. Consistente con el controlador v27.1 que las
    //            excluye del calendario y de las validaciones.
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $qK = \App\Models\Rsv\Reserva::query();
            if (!$modoGlobal && $xlsInmId) { $qK->where('id_rsv_catalogo_inmueble', $xlsInmId); }
            if (!empty($estadosNoBloqueantesIds)) {
                $qK->whereNotIn('id_rsv_statuses', $estadosNoBloqueantesIds);
            }
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

    // ─── 4 · Agenda de la fecha seleccionada ─────────────────────────
    // 🆕 v25.1 · Las reservas canceladas/rechazadas/anuladas se marcan
    //            con `cancelada = true` para que la vista pueda:
    //              · Mostrarlas con estilo visual diferenciado (tachado,
    //                badge "NO VIGENTE").
    //              · NO contarlas en los badges de LLEGADAS/SALIDAS del
    //                toggle de la agenda (solo se cuentan las vigentes).
    //            Sí se muestran en el listado para que el usuario sepa
    //            que existían y fueron canceladas (informativo).
    $esEstadoNoBloqueante = function ($r) use ($estadosNoBloqueantesIds) {
        if (empty($estadosNoBloqueantesIds)) return false;
        $sid = $r->id_rsv_statuses ?? null;
        return $sid !== null && in_array((int) $sid, array_map('intval', $estadosNoBloqueantesIds), true);
    };
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
                ->flatMap(function ($r) use ($agendaFecha, $esEstadoNoBloqueante) {
                    $out = collect();
                    try {
                        $fi = $r->fecha_inicio ? \Illuminate\Support\Carbon::parse($r->fecha_inicio) : null;
                        $ff = $r->fecha_fin ? \Illuminate\Support\Carbon::parse($r->fecha_fin) : null;
                        $cancelada = $esEstadoNoBloqueante($r);
                        if ($fi && $fi->isSameDay($agendaFecha)) { $out->push(['tipo' => 'LLEGADA', 'r' => $r, 'cancelada' => $cancelada]); }
                        if ($ff && $ff->isSameDay($agendaFecha)) { $out->push(['tipo' => 'SALIDA', 'r' => $r, 'cancelada' => $cancelada]); }
                    } catch (\Throwable $ex) {}
                    return $out;
                });
        }
    } catch (\Throwable $e) { $agendaHoy = collect(); }

    // ─── 5 · Preparador de item de agenda ────────────────────────────
    // 🆕 v25.1 · Agrega flag `cancelada` para que la vista pueda aplicar
    //            estilos visuales (tachado, badge NO VIGENTE) y diferenciar
    //            las reservas vigentes de las canceladas/rechazadas/anuladas.
    $agPrep = function ($item) {
        $out = ['ok' => false];
        $r = $item['r'] ?? null;
        if (!is_object($r)) { return $out; }
        $out['ok'] = true;
        $out['tipo'] = $item['tipo'] ?? '';
        $out['esLlegada'] = ($out['tipo'] == 'LLEGADA');
        $out['cancelada'] = (bool) ($item['cancelada'] ?? false);
        $out['codigo'] = $r->codigo_reserva ?? '';
        $out['userName'] = optional($r->user)->name ?? 'Sin asignar';
        $out['inmName'] = optional($r->inmueble)->name ?? 'N/D';
        $stName = optional($r->status)->name ?? 'Pendiente';
        $out['stName'] = $stName;
        $s = strtolower((string) $stName);
        $dot = 'text-secondary';
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) { $dot = 'text-success'; }
        elseif (str_contains($s, 'pendien')) { $dot = 'text-warning'; }
        elseif (str_contains($s, 'cancela') || str_contains($s, 'rechaz') || str_contains($s, 'anulad')) { $dot = 'text-danger'; }
        $out['dot'] = $dot;
        $fh = $out['esLlegada'] ? $r->fecha_inicio : $r->fecha_fin;
        try { $out['horaFmt'] = $fh ? \Illuminate\Support\Carbon::parse($fh)->format('H:i') : '—'; }
        catch (\Throwable $ex) { $out['horaFmt'] = '—'; }
        return $out;
    };

    // ─────────────────────────────────────────────────────────────────
    // 6 · CATÁLOGOS PARA EL MODAL DE NUEVA RESERVA
    // ─────────────────────────────────────────────────────────────────
    $modalInmuebles = collect();
    $modalUsers     = collect();
    $modalOrigenes = collect();
    $modalPendienteId = null;

    try {
        if (class_exists(\App\Models\Rsv\CatalogoInmueble::class)) {
            $modalInmuebles = \App\Models\Rsv\CatalogoInmueble::orderBy('name')->get();
            $phpDebugLogs[] = ['ok', 'Inmuebles cargados: ' . $modalInmuebles->count()
                . ' · Tabla: ' . (new \App\Models\Rsv\CatalogoInmueble)->getTable()];
        } else {
            $phpDebugLogs[] = ['err', 'Clase App\Models\Rsv\CatalogoInmueble NO existe'];
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error cargando inmuebles: ' . $e->getMessage()];
    }

    try {
        if (class_exists(\App\Models\User::class)) {
            $modalUsers = \App\Models\User::orderBy('name')->get();
            $phpDebugLogs[] = ['ok', 'Usuarios cargados: ' . $modalUsers->count()];
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error cargando usuarios: ' . $e->getMessage()];
    }

    try {
        if (class_exists(\App\Models\Rsv\OrigenReserva::class)) {
            $origenModel = new \App\Models\Rsv\OrigenReserva;
            $tablaOrigen = $origenModel->getTable();
            $phpDebugLogs[] = ['info', 'Modelo OrigenReserva OK · tabla declarada: ' . $tablaOrigen];

            $colNombre = 'name';
            try {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing($tablaOrigen);
                foreach (['nombre','name','titulo','descripcion'] as $c) {
                    if (in_array($c, $cols)) { $colNombre = $c; break; }
                }
                $phpDebugLogs[] = ['info', 'Columna nombre detectada: <b>' . $colNombre . '</b> · Todas columnas: ' . implode(', ', $cols)];
            } catch (\Throwable $ex) {
                $phpDebugLogs[] = ['warn', 'No se pudo leer schema de ' . $tablaOrigen . ': ' . $ex->getMessage()];
            }

            $modalOrigenes = \App\Models\Rsv\OrigenReserva::query()->orderBy($colNombre)->get();
            $phpDebugLogs[] = [
                $modalOrigenes->isEmpty() ? 'warn' : 'ok',
                'Orígenes cargados: ' . $modalOrigenes->count() . ' · Tabla: ' . $tablaOrigen . ' · Ordenado por: ' . $colNombre
            ];

            if ($modalOrigenes->isEmpty()) {
                try {
                    $total = \Illuminate\Support\Facades\DB::table($tablaOrigen)->count();
                    $phpDebugLogs[] = ['info', 'Count DB::table(' . $tablaOrigen . '): ' . $total];
                    if ($total > 0) {
                        $phpDebugLogs[] = ['warn', '⚠️ El modelo no trajo registros pero la tabla tiene ' . $total . ' → probable SoftDeletes o global scope oculto'];
                    }
                } catch (\Throwable $ex) {
                    $phpDebugLogs[] = ['err', 'No se pudo leer schema/tabla ' . $tablaOrigen . ': ' . $ex->getMessage()];
                }
            }
        } else {
            $phpDebugLogs[] = ['err', 'Clase App\Models\Rsv\OrigenReserva NO existe'];
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error cargando orígenes: ' . $e->getMessage()];
    }

    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $pendiente = \App\Models\Rsv\Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])
                ->orderBy('id', 'asc')->first();
            if (!$pendiente) {
                $pendiente = \App\Models\Rsv\Status::orderBy('id', 'asc')->first();
            }
            $modalPendienteId = $pendiente?->id;
            $phpDebugLogs[] = [
                $pendiente ? 'ok' : 'warn',
                'Estado Pendiente: ' . ($pendiente ? 'ID=' . $pendiente->id . ' name=' . $pendiente->name : 'NO ENCONTRADO')
            ];
        }
    } catch (\Throwable $e) {
        $phpDebugLogs[] = ['err', 'Error cargando status Pendiente: ' . $e->getMessage()];
    }

    $modalInmPreselect = $xlsInmId ?? null;
    $modalInmLocked   = !$modoGlobal && $xlsInmId;

    try {
        $modalStoreUrl = route('rsv.reservas.store-ajax');
    } catch (\Throwable $e) {
        $modalStoreUrl = url('/rsv/reservas/store-ajax');
        $phpDebugLogs[] = ['err', 'Ruta rsv.reservas.store-ajax NO registrada: ' . $e->getMessage()];
    }
    try {
        $modalFechasUrl = route('rsv.reservas.fechas-ocupadas', ['inmueble' => '__INM__']);
    } catch (\Throwable $e) {
        $modalFechasUrl = url('/rsv/reservas/fechas-ocupadas/__INM__');
        $phpDebugLogs[] = ['err', 'Ruta rsv.reservas.fechas-ocupadas NO registrada: ' . $e->getMessage()];
    }

    // 🆕 v24 · Usuario logueado (para chip de respuesta rápida "Reserva creada por usuario X")
    $authUserName = auth()->check() ? (auth()->user()->name ?? 'admin') : 'admin';

    $phpDebugJson = json_encode($phpDebugLogs, $dsJsonFlags);
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
        <button type="button" id="rvExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Descarga en Excel TODO el resultado filtrado">
            <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
        </button>
        <button type="button" class="btn-green d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
            <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Reserva
        </button>
    </div>
</div>


<!-- KPIs -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-2">
    <span class="kpi"><span class="dot" style="color:#a3cfb6;"></span><span class="kpi-l">ACTIVAS</span><b>{{ $kpiActivas ?? '—' }}</b></span>
    <span class="kpi"><span class="dot" style="color:#a9cfe8;"></span><span class="kpi-l">MES</span><b>{{ !is_null($kpiMontoMes ?? null) ? '$'.number_format($kpiMontoMes, 0) : '—' }}</b></span>
    <span class="kpi"><span class="dot" style="color:#cdb4e4;"></span><span class="kpi-l">OCUP.</span><b>{{ $kpiOcupacion ?? '—' }}</b></span>
</div>


<!-- AGENDA · hoy o fecha puntual -->
<div class="mb-2">
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
        <button type="button" class="rv-agenda-toggle" data-bs-toggle="collapse" data-bs-target="#agendaHoyBox" aria-expanded="false">
            <i class="bi bi-calendar-day" style="font-size: 8px;"></i>
            Agenda
            @php
                $agendaHoyVigentes  = ($agendaHoy ?? collect())->where('cancelada', false);
                $llegadasVigentes   = $agendaHoyVigentes->where('tipo', 'LLEGADA')->count();
                $salidasVigentes    = $agendaHoyVigentes->where('tipo', 'SALIDA')->count();
                $llegadasCanceladas = ($agendaHoy ?? collect())->where('tipo', 'LLEGADA')->where('cancelada', true)->count();
                $salidasCanceladas  = ($agendaHoy ?? collect())->where('tipo', 'SALIDA')->where('cancelada', true)->count();
            @endphp
            <span class="lg"><i class="bi bi-arrow-down-right"></i> {{ $llegadasVigentes }}@if($llegadasCanceladas > 0)<span class="text-muted" title="{{ $llegadasCanceladas }} cancelada(s)">+{{ $llegadasCanceladas }}<i class="bi bi-slash-circle" style="font-size:7px;"></i></span>@endif</span>
            <span class="sl"><i class="bi bi-arrow-up-right"></i> {{ $salidasVigentes }}@if($salidasCanceladas > 0)<span class="text-muted" title="{{ $salidasCanceladas }} cancelada(s)">+{{ $salidasCanceladas }}<i class="bi bi-slash-circle" style="font-size:7px;"></i></span>@endif</span>
            <i class="bi bi-chevron-down" style="font-size: 7px;"></i>
        </button>
        <span class="ag-fecha-chip {{ ($esAgendaHoy ?? true) ? '' : 'other' }}" title="Fecha consultada en la agenda">
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
                   value="{{ $agendaFecha->toDateString() }}" title="Elija la fecha y presione Consultar (o Enter)">
            <button type="button" id="agendaConsultarBtn" class="ag-consultar" title="Consultar la fecha elegida">
                <i class="bi bi-arrow-right-circle"></i> Consultar
            </button>
        </div>
        @if(!($esAgendaHoy ?? true))
            <a href="{{ request()->fullUrlWithQuery(['agenda_fecha' => null]) }}" class="ag-hoy-btn" title="Volver a la agenda de hoy">
                <i class="bi bi-arrow-counterclockwise" style="font-size: 7px;"></i> Ir a hoy
            </a>
        @endif
    </div>
    <div class="collapse mt-1" id="agendaHoyBox">
        <div class="border rounded-3 overflow-hidden" style="border-color: var(--grid) !important; background: #fff;">
            <div class="px-2 py-1 border-bottom d-flex justify-content-between" style="background: var(--head); font-size: 8px; color: #a8b3c1; border-color: var(--grid) !important;">
                <span><i class="bi bi-calendar3 me-1"></i>{{ $agendaFecha->translatedFormat('l d/m/Y') }}
                    @if($esAgendaHoy ?? true)<span class="ms-1 opacity-75">(hoy)</span>@endif
                </span>
                <span>{{ ($modoGlobal ?? true) ? 'todo el sistema' : 'este inmueble' }}</span>
            </div>
            <div class="overflow-auto" style="max-height: 200px;">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Tipo</th><th>Código</th><th>Titular</th><th>Inmueble</th><th>Hora</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @forelse(($agendaHoy ?? collect()) as $item)
                            @php $g = $agPrep($item); @endphp
                            @if($g['ok'] ?? false)
                            <tr class="{{ ($g['esLlegada'] ?? false) ? 'row-today' : '' }}" @if($g['cancelada'] ?? false) style="opacity: .55; text-decoration: line-through;" title="Reserva no vigente" @endif>
                                <td style="font-weight: 600; color: {{ ($g['esLlegada'] ?? false) ? '#5d8a70' : '#c08484' }};{{ ($g['cancelada'] ?? false) ? ' text-decoration: line-through;' : '' }}">{{ $g['tipo'] }}</td>
                                <td class="strong">#{{ $g['codigo'] }}</td>
                                <td>{{ $g['userName'] }}</td>
                                <td class="rv-ellip">{{ $g['inmName'] }}</td>
                                <td class="text-center">{{ $g['horaFmt'] ?? '—' }}</td>
                                <td>
                                    <span class="dot {{ $g['dot'] }}"></span>{{ strtoupper($g['stName']) }}
                                    @if($g['cancelada'] ?? false)
                                        <span class="badge bg-danger" style="font-size: 6.5px; padding: 1px 4px; vertical-align: middle; margin-left: 2px;">NO VIGENTE</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr><td colspan="6" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-sun me-1"></i> Sin llegadas ni salidas para el {{ $agendaFecha->translatedFormat('d/m/Y') }}.
                            </td></tr>
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
        <input type="text" id="inputSearchReservas" class="xls-search"
               placeholder="Nombre, cédula, código o inmueble… (instantáneo)" autocomplete="off">
        <span class="search-live" title="Filtra mientras escribe · toda la base del contexto">
            <i class="bi bi-lightning-charge-fill" style="font-size: 7px;"></i> vivo
        </span>
    </div>
    <div class="d-flex align-items-center" style="gap: 6px;">
        <i class="bi bi-calendar-range" style="font-size: 8.5px; color: #c3ccd6;" title="Rango por check-in"></i>
        <input type="date" id="rvDesde" class="xls-date" title="Check-in desde">
        <span style="font-size: 8.5px; color: #c3ccd6;">→</span>
        <input type="date" id="rvHasta" class="xls-date" title="Check-in hasta">
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
                <th>Código</th><th>Titular</th><th>Inmueble</th>
                <th class="sortable" data-sort="fi">Check-in <i class="bi bi-arrow-down-up s-ind"></i></th>
                <th>Check-out</th><th class="text-center">N</th>
                <th class="text-end sortable" data-sort="monto">Monto <i class="bi bi-arrow-down-up s-ind"></i></th>
                <th>Estado</th><th class="text-center" style="width: 60px;">·</th>
            </tr>
        </thead>
        <tbody id="tbodyReservas">
            <tr><td colspan="10" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                <i class="bi bi-hourglass-split me-1"></i> Cargando reservas…
            </td></tr>
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
        <span id="rvPgLbl" style="font-size: 8.5px; color: #c3ccd6; font-weight: 600;">Página 1 de 1</span>
        <button type="button" id="rvNext" class="btn-mini" disabled>Siguiente ›</button>
    </div>
</div>


</div>{{-- /.reservas-soft --}}


{{-- =============================================================== --}}
{{-- MODAL · NUEVA RESERVA  (FUERA de .reservas-soft)               --}}
{{-- =============================================================== --}}
<div class="modal fade" id="modalNuevaReserva" tabindex="-1" aria-hidden="true" aria-labelledby="modalNuevaReservaLbl">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formNuevaReserva" novalidate action="{{ $modalStoreUrl }}" method="POST" data-store="{{ $modalStoreUrl }}">
                @csrf
                <input type="hidden" name="_ajax" value="1">
                <input type="hidden" name="id_rsv_statuses" id="fldStatus" value="{{ $modalPendienteId ?? '' }}">
                <input type="hidden" name="fecha_inicio" id="fldFechaIni" value="">
                <input type="hidden" name="fecha_fin"   id="fldFechaFin"   value="">

                <div class="modal-header">
                    <h6 class="modal-title" id="modalNuevaReservaLbl">
                        <i class="bi bi-plus-circle me-1" style="color: #3d7a5c;"></i> Crear Nueva Reserva
                    </h6>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Cerrar" style="font-size: 7px;"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Inmueble *</label>
                            @if($modalInmLocked)
                                <select class="form-select" id="fldInmueble" disabled>
                                    @foreach($modalInmuebles as $inm)
                                        <option value="{{ $inm->id }}" @if((string) $modalInmPreselect === (string) $inm->id) selected @endif>{{ $inm->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $modalInmPreselect }}">
                                <div class="form-text"><i class="bi bi-lock-fill"></i> Inmueble fijo del contexto (no editable).</div>
                            @else
                                <select class="form-select" name="id_rsv_catalogo_inmueble" id="fldInmueble" required>
                                    <option value="">— Seleccione —</option>
                                    @foreach($modalInmuebles as $inm)
                                        <option value="{{ $inm->id }}" @if((string) $modalInmPreselect === (string) $inm->id) selected @endif>{{ $inm->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Seleccione un inmueble.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Titular * <span class="text-muted" id="userCountLbl">(0)</span></label>
                            <div class="user-search-wrap">
                                {{-- 🆕 v24 · Solo input visible, dropdown flotante, chip de seleccionado --}}
                                <input type="text" id="fldUserSearch" class="user-search-input"
                                       placeholder="Buscar titular por nombre, cédula o email…" autocomplete="off">
                                <div id="fldUserDropdown" class="user-dropdown d-none"></div>
                                <div id="fldUserSelected" class="user-selected d-none"></div>
                                {{-- Select OCULTO que contiene el value enviado en el POST --}}
                                <select class="user-hidden" name="id_user" id="fldUser" required>
                                    <option value="">—</option>
                                    @foreach($modalUsers as $u)
                                        @php
                                            $searchBlob = strtolower(trim(($u->name ?? '') . ' ' . ($u->email ?? '') . ' ' . ($u->nid ?? '')));
                                            $userLabel = $u->name . (!empty($u->nid) ? ' · CC '.$u->nid : '') . (!empty($u->email) ? ' · '.$u->email : '');
                                        @endphp
                                        <option value="{{ $u->id }}"
                                                data-nid="{{ $u->nid ?? '' }}"
                                                data-search="{{ $searchBlob }}"
                                                data-label="{{ $userLabel }}">
                                            {{ $userLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="user-count" id="userCountTxt"></div>
                                <div class="invalid-feedback">Seleccione un titular.</div>
                            </div>
                        </div>
                    </div>

                    <div class="cal-wrap mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <button type="button" id="calPrevMonth" class="btn-mini">‹</button>
                            <span id="calMonthLbl" class="cal-title">—</span>
                            <button type="button" id="calNextMonth" class="btn-mini">›</button>
                        </div>
                        <div id="miniCalGrid" class="cal-grid"></div>
                        <div class="cal-legend mt-1">
                            <span class="me-2"><span class="cal-dot cal-dot-free"></span> Libre</span>
                            <span class="me-2"><span class="cal-dot cal-dot-occ"></span> Reservada</span>
                            <span class="me-2"><span class="cal-dot cal-dot-ali"></span> 🧹 Alistamiento</span>
                            <span class="me-2"><span class="cal-dot cal-dot-sel"></span> Seleleccionada</span>
                            <span class="me-2"><span class="cal-dot cal-dot-past"></span> Pasada</span>
                        </div>
                        <div id="calSelInfo" class="cal-sel-info mt-1">
                            <i class="bi bi-info-circle"></i> Seleccione un inmueble para ver disponibilidad.
                        </div>
                        <div id="calTarifaBox" class="cal-tarifa-box d-none"></div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Monto total * <span id="montoMinLbl" class="text-muted">(mín. $200.000)</span></label>
                            <input type="number" step="0.01" min="200000" class="form-control" name="monto_total" id="fldMonto" placeholder="200000" required>
                            <div class="form-text" id="montoHelp">Valor mínimo permitido: $200.000 (se ajusta según tarifa aplicable). Se auto-llena al seleccionar el rango.</div>
                            <div class="invalid-feedback">Monto debe ser ≥ mínimo aplicable.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Origen *</label>
                            @if($modalOrigenes->isEmpty())
                                <select class="form-select" name="id_rsv_origen_reservas" id="fldOrigen">
                                    <option value="">— Sin orígenes configurados —</option>
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-exclamation-triangle" style="color:#b08a3e;"></i>
                                    No hay orígenes cargados. Abrir panel 🐛 Debug para ver detalle.
                                </div>
                            @else
                                {{-- 🆕 v24 · Preselecciona la 3ra opción (index 2) --}}
                                <select class="form-select" name="id_rsv_origen_reservas" id="fldOrigen" required>
                                    @foreach($modalOrigenes as $i => $o)
                                        @php
                                            $oActive = isset($o->active) ? (bool) $o->active : true;
                                            $oLabel = $o->nombre ?? $o->name ?? $o->titulo ?? ('Origen #' . $o->id);
                                            $isThird = ($i === 2); // 🆕 v24 · 3ra opción preseleccionada
                                        @endphp
                                        <option value="{{ $o->id }}"
                                                @if($isThird) selected @endif
                                                @if(!$oActive) disabled @endif>
                                            {{ $oLabel }}{{ !$oActive ? ' (inactivo)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">3er origen preseleccionado automáticamente.</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 cal-info-estado">
                        <i class="bi bi-info-circle"></i>
                        Estado inicial: <b>Pendiente</b>. · Después de cada salida hay <b>1 día de alistamiento</b> bloqueado. · Máximo de noches = el de la tarifa <b>más restrictiva</b> aplicable al rango. · <b style="color:#dc3545;">Si no hay tarifa configurada para el rango, no se puede reservar</b>.
                    </div>

                    {{-- 🆕 v24 · Comentario con chips de respuestas rápidas --}}
                    <div class="mt-2">
                        <label class="form-label">Comentario <span class="text-muted" style="font-size:7px; text-transform:none; font-weight:400;">(click en una opción para agregar)</span></label>
                        <div class="comentario-chips" id="comentarioChips">
                            <span class="com-chip" data-text="Reserva creada desde la parte administrativa">
                                <i class="bi bi-building"></i> Admin
                            </span>
                            <span class="com-chip" data-text="Reserva creada por solicitud superior">
                                <i class="bi bi-arrow-up-circle"></i> Solic. superior
                            </span>
                            <span class="com-chip" data-text="Reserva creada por usuario {{ $authUserName }}">
                                <i class="bi bi-person-fill"></i> Por usuario
                            </span>
                            <span class="com-chip" data-text="Pendiente de pago">
                                <i class="bi bi-cash-stack"></i> Pend. pago
                            </span>
                            <span class="com-chip" data-text="Reserva sujeta a confirmación con cliente">
                                <i class="bi bi-question-circle"></i> Suj. confirm.
                            </span>
                            <span class="com-chip" data-text="Cliente frecuente">
                                <i class="bi bi-star"></i> Cliente frec.
                            </span>
                            <span class="com-chip" data-text="Pago en efectivo">
                                <i class="bi bi-cash"></i> Efectivo
                            </span>
                            <span class="com-chip" data-text="Pago por transferencia bancaria">
                                <i class="bi bi-bank"></i> Transfer.
                            </span>
                            <span class="com-chip" data-text="Solicitar datos de contacto del titular">
                                <i class="bi bi-phone"></i> Pedir contacto
                            </span>
                            <span class="com-chip" data-text="Verificar disponibilidad antes de confirmar">
                                <i class="bi bi-shield-check"></i> A verificar
                            </span>
                            <span class="com-chip" data-text="Tarifa especial aplicada">
                                <i class="bi bi-tag"></i> Tarifa esp.
                            </span>
                            <span class="com-chip" data-text="Reserva con descuento autorizado">
                                <i class="bi bi-percent"></i> Con descuento
                            </span>
                        </div>
                        <textarea class="form-control" name="comentario_reserva" id="fldComentario" rows="2" placeholder="Observaciones internas… (opcional)" maxlength="2000"></textarea>
                    </div>

                    <div id="formNuevaReservaAlert" class="alert alert-danger mt-2 d-none nr-alert" role="alert"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-mini" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg" style="font-size: 7px;"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-green" id="btnGuardarReserva">
                        <i class="bi bi-check-lg" style="font-size: 8px;"></i>
                        <span class="btn-txt">Crear Reserva</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- DATASET + logs PHP --}}
<script type="application/json" id="rvDataset">{!! $dsJson !!}</script>
<script type="application/json" id="rvStatuses">{!! $dsStatusesJson !!}</script>
<script type="application/json" id="rvPhpDebug">{!! $phpDebugJson !!}</script>


{{-- Botón flotante de Debug --}}
<button type="button" id="rvDebugToggle" title="Mostrar/ocultar panel de debug">🐛</button>

{{-- Panel de Debug --}}
<div id="rvDebugPanel">
    <div class="dbg-header" id="rvDebugHeader">
        <b>🐛 Debug · tab-reservas</b>
        <div class="dbg-controls">
            <button type="button" class="dbg-btn" id="rvDebugClear">Limpiar</button>
            <button type="button" class="dbg-btn" id="rvDebugClose">✕</button>
        </div>
    </div>
    <div class="dbg-body" id="rvDebugBody"></div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ═══ PANEL DE DEBUG ═══ */
        var dbgPanel  = document.getElementById('rvDebugPanel');
        var dbgBody   = document.getElementById('rvDebugBody');
        var dbgToggle = document.getElementById('rvDebugToggle');
        var dbgClose  = document.getElementById('rvDebugClose');
        var dbgClear  = document.getElementById('rvDebugClear');
        var dbgHeader = document.getElementById('rvDebugHeader');
        var dbgOpen = false;

        function dbgTogglePanel(forceState) {
            dbgOpen = (typeof forceState === 'boolean') ? forceState : !dbgOpen;
            if (dbgOpen) {
                dbgPanel.classList.add('open');
                dbgToggle.style.display = 'none';
            } else {
                dbgPanel.classList.remove('open');
                dbgToggle.style.display = 'flex';
            }
        }
        function dbgLog(level, msg) {
            if (!dbgBody) return;
            var t = new Date();
            var time = ('0'+t.getHours()).slice(-2)+':'+('0'+t.getMinutes()).slice(-2)+':'+('0'+t.getSeconds()).slice(-2);
            var div = document.createElement('div');
            div.className = 'dbg-log ' + (level || 'info');
            div.innerHTML = '<span class="dbg-time">['+time+']</span> ' + String(msg).replace(/&/g,'&amp;').replace(/</g,'&lt;');
            dbgBody.appendChild(div);
            dbgBody.scrollTop = dbgBody.scrollHeight;
        }
        if (dbgToggle) dbgToggle.addEventListener('click', function(){ dbgTogglePanel(true); });
        if (dbgClose) dbgClose.addEventListener('click', function(){ dbgTogglePanel(false); });
        if (dbgClear) dbgClear.addEventListener('click', function(){ if (dbgBody) dbgBody.innerHTML = ''; });
        if (dbgHeader) dbgHeader.addEventListener('click', function(e){
            if (e.target === dbgHeader) dbgTogglePanel(false);
        });

        try {
            var phpLogs = JSON.parse(document.getElementById('rvPhpDebug').textContent || '[]');
            phpLogs.forEach(function (l) {
                dbgLog(l[0], '<b>PHP</b> ' + l[1]);
            });
        } catch (e) {
            dbgLog('err', 'No se pudieron cargar logs PHP: ' + e.message);
        }

        /* ═══ DATASET ═══ */
        var root = document.getElementById('rvRoot');
        var modo = root ? (root.getAttribute('data-modo') || 'global') : 'global';
        var ctx  = root ? (root.getAttribute('data-ctx') || 'Global') : 'Global';

        var DS = [];
        try {
            var raw = document.getElementById('rvDataset');
            DS = JSON.parse(raw ? (raw.textContent || '[]') : '[]');
            dbgLog('ok', '<b>Dataset</b> cargado: ' + DS.length + ' reservas');
        } catch (e) { DS = []; dbgLog('err', 'Error parseando dataset: ' + e.message); }
        if (!Array.isArray(DS)) { DS = []; }

        var PAGE_SIZE = 15;
        var page = 1;
        var q = ''; var statusF = ''; var fDesde = ''; var fHasta = '';
        var sortAttr = null; var sortDir = 'asc';

        function esc(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        function money(n) { return '$' + Number(n || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function money0(n) { return '$' + Number(n || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 }); }
        function midnight(dstr) { var d = new Date(dstr + 'T00:00:00'); return isNaN(d) ? null : d; }
        function fmtDMY(dstr) { var d = midnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+String(d.getFullYear()).slice(-2) : '—'; }
        function fmtDM(dstr)  { var d = midnight(dstr); return d ? ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2) : '—'; }

        var HOY = new Date(); HOY.setHours(0,0,0,0);
        function dayDiff(a, b) { return Math.round((a - b) / 86400000); }

        function statusDot(stn) {
            var s = String(stn || '').toLowerCase();
            if (s.indexOf('confirm') !== -1 || s.indexOf('activ') !== -1) return 'text-success';
            if (s.indexOf('pendien') !== -1) return 'text-warning';
            // 🆕 v25.1 · incluir 'anulad' (Anulada/Anulado) para consistencia con controlador v27.1
            if (s.indexOf('cancela') !== -1 || s.indexOf('rechaz') !== -1 || s.indexOf('anulad') !== -1) return 'text-danger';
            return 'text-secondary';
        }
        function statusNombre() {
            if (statusF === '') { return 'Todas'; }
            var chip = document.querySelector('.reservas-soft .fchip.active');
            return chip ? chip.textContent.replace(/\(\d+\)/, '').trim() : ('#' + statusF);
        }

        function limpiarOverlaysHuerfanos() {
            try {
                var hayModalVisible = document.querySelector('.modal.show');
                if (!hayModalVisible) {
                    document.querySelectorAll('.modal-backdrop').forEach(function (bd) { bd.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }
                var selectoresLoader = [
                    '.preloader', '.preloader-wrapper', '#preloader',
                    '.loading-overlay', '.overlay-loading', '.loader-overlay',
                    '.spinner-overlay', '.page-loading', '#page-loading',
                    '#loader', '.global-loader', '.ajax-loader',
                    '[data-loading="true"]', '.cargando', '#cargando'
                ];
                selectoresLoader.forEach(function (sel) {
                    document.querySelectorAll(sel).forEach(function (el) {
                        if (!el.querySelector('.modal.show')) {
                            el.style.display = 'none'; el.classList.add('d-none');
                        }
                    });
                });
            } catch (e) {}
        }

        /* ═══ FILTRO DE LA TABLA ═══ */
        function filtrar() {
            var term = q.replace(/#/g, '').toLowerCase().trim();
            var dD = fDesde ? midnight(fDesde) : null;
            var dH = fHasta ? midnight(fHasta) : null;
            return DS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                if (statusF !== '' && String(r.st) !== String(statusF)) return false;
                var fi = midnight(r.fi);
                if (dD) { if (!fi || fi.getTime() < dD.getTime()) return false; }
                if (dH) { if (!fi || fi.getTime() > dH.getTime()) return false; }
                return true;
            });
        }

        function filaHTML(r, nGlobal) {
            var fi = midnight(r.fi), ff = midnight(r.ff);
            var hoy = HOY;
            var noches = (fi && ff) ? Math.abs(dayDiff(fi, ff)) : null;
            // 🆕 v25.1 · Detectar si la reserva está NO-VIGENTE (cancelada/rechazada/anulada)
            //           Para NO mostrar badges de "EN CURSO", "HOY", "MAÑANA" ni
            //           resaltar la fila como row-today, ya que no está activa.
            //           Consistente con el controlador v27.1 que la excluye.
            var stnLow = String(r.stn || '').toLowerCase();
            var noVigente = (stnLow.indexOf('cancela') !== -1
                          || stnLow.indexOf('rechaz') !== -1
                          || stnLow.indexOf('anulad') !== -1);
            var enCurso = false, pct = 0, cdTxt = '—', cdCls = 'cd-done';
            if (fi && ff && hoy >= fi && hoy <= ff) {
                if (noVigente) {
                    // Reserva cancelada cuya ventana temporal incluye hoy → NO está "en curso"
                    cdTxt = 'NO VIGENTE'; cdCls = 'cd-done';
                } else {
                    enCurso = true;
                    if (noches > 0) { pct = Math.min(100, Math.max(0, Math.round((dayDiff(fi, hoy) / noches) * 100))); }
                    cdTxt = 'EN CURSO'; cdCls = 'cd-live';
                }
            } else if (fi) {
                var dias = dayDiff(fi, hoy);
                if (noVigente) {
                    // Cancelada: no mostrar HOY/MAÑANA/+Nd porque no va a ocurrir
                    cdTxt = 'NO VIGENTE'; cdCls = 'cd-done';
                } else if (dias === 0) { cdTxt = 'HOY'; cdCls = 'cd-today'; }
                else if (dias === 1) { cdTxt = 'MAÑANA'; cdCls = 'cd-next'; }
                else if (dias > 1) { cdTxt = '+' + dias + 'd'; cdCls = 'cd-future'; }
                else if (dias < 0) { cdTxt = 'FIN'; cdCls = 'cd-done'; }
            }
            // 🆕 v25.1 · Solo marcar row-today si es llegada HOY y la reserva está vigente
            var llegaHoy = (fi && !noVigente) ? (dayDiff(fi, hoy) === 0) : false;
            var dot = statusDot(r.stn);
            var h = '';
            h += '<tr class="reserva-row' + (llegaHoy ? ' row-today' : '') + '"' + (noVigente ? ' style="opacity: .65;"' : '') + '>';
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
            h += '<td><span class="dot ' + dot + '"></span>' + esc(String(r.stn || '').toUpperCase());
            if (noVigente) {
                h += ' <span class="badge bg-danger" style="font-size: 6.5px; padding: 1px 4px; vertical-align: middle; margin-left: 2px;">NO VIGENTE</span>';
            }
            h += '</td>';
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

        var tbody   = document.getElementById('tbodyReservas');
        var badgeTx = document.getElementById('rvBadgeTxt');
        var pgInfo  = document.getElementById('rvPgInfo');
        var pgLbl   = document.getElementById('rvPgLbl');
        var btnPrev = document.getElementById('rvPrev');
        var btnNext = document.getElementById('rvNext');

        function filtrarSoloTextoYFechas() {
            var term = q.replace(/#/g, '').toLowerCase().trim();
            var dD = fDesde ? midnight(fDesde) : null;
            var dH = fHasta ? midnight(fHasta) : null;
            return DS.filter(function (r) {
                if (term) {
                    var blob = (r.codigo + ' ' + r.uname + ' ' + r.umail + ' ' + r.unid + ' ' + r.inm + ' ' + (r.com || '')).toLowerCase();
                    if (blob.indexOf(term) === -1) return false;
                }
                var fi = midnight(r.fi);
                if (dD) { if (!fi || fi.getTime() < dD.getTime()) return false; }
                if (dH) { if (!fi || fi.getTime() > dH.getTime()) return false; }
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
                '<html><head><meta charset="UTF-8"><style>' +
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
                a.href = url; a.download = 'Reservas_' + ctx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                a.style.display = 'none'; a.click();
                setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            } catch (e) {
                var blob2 = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url2 = URL.createObjectURL(blob2);
                var a2 = document.createElement('a');
                a2.href = url2; a2.download = 'Reservas_' + ctx.replace(/[^a-z0-9]+/gi, '_') + '_' + fArchivo + '.xls';
                document.body.appendChild(a2); a2.click(); document.body.removeChild(a2); URL.revokeObjectURL(url2);
            }
            setTimeout(limpiarOverlaysHuerfanos, 300);
            setTimeout(limpiarOverlaysHuerfanos, 1200);
        }

        var btnExport = document.getElementById('rvExport');
        if (btnExport) {
            btnExport.addEventListener('click', function (e) {
                e.stopPropagation();
                exportarExcel();
            });
        }

        var search = document.getElementById('inputSearchReservas');
        if (search) {
            search.addEventListener('input', function () { q = this.value; page = 1; render(); });
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
                page = 1; render();
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
                page = 1; render();
            });
        });

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


        /* ═══════════════════════════════════════════════════════════════
           🆕 v24 · AUTOCOMPLETE DE TITULAR (input + dropdown flotante)
           · Solo el input es visible, el select queda oculto.
           · Al escribir, se filtran los titulares y se muestran en el dropdown.
           · Click en un item → se setea el value del select y se muestra
             un chip con el nombre del titular seleccionado.
           · El chip tiene botón X para borrar la selección.
           · Navegación con flechas ↑/↓ y Enter para seleccionar el activo.
           ═══════════════════════════════════════════════════════════════ */
        var fldUserSearch  = document.getElementById('fldUserSearch');
        var fldUser        = document.getElementById('fldUser');
        var fldUserDropdown = document.getElementById('fldUserDropdown');
        var fldUserSelected = document.getElementById('fldUserSelected');
        var userCountLbl   = document.getElementById('userCountLbl');
        var userCountTxt   = document.getElementById('userCountTxt');

        // Guardar TODAS las options del select en memoria (con su data)
        var userOriginalData = [];
        if (fldUser) {
            userOriginalData = Array.from(fldUser.querySelectorAll('option')).map(function(opt) {
                return {
                    value: opt.value,
                    text: opt.textContent,
                    label: opt.getAttribute('data-label') || opt.textContent,
                    dataNid: opt.getAttribute('data-nid') || '',
                    dataSearch: opt.getAttribute('data-search') || ''
                };
            }).filter(function(o) { return o.value !== ''; }); // excluir placeholder

            dbgLog('ok', 'Titulares cargados para autocomplete: ' + userOriginalData.length);
            if (userCountLbl) {
                userCountLbl.textContent = '(' + userOriginalData.length + ')';
            }
            if (userCountTxt) {
                userCountTxt.textContent = 'Total titulares: ' + userOriginalData.length;
            }
        }

        /* Mostrar el chip del titular seleccionado */
        function renderUserSelected(userId) {
            if (!userId) {
                if (fldUserSelected) {
                    fldUserSelected.classList.add('d-none');
                    fldUserSelected.innerHTML = '';
                }
                return;
            }
            var userData = userOriginalData.find(function(o) { return String(o.value) === String(userId); });
            if (!userData) return;
            if (fldUserSelected) {
                fldUserSelected.innerHTML =
                    '<i class="bi bi-person-check-fill"></i>' +
                    '<span class="user-selected-text">' + esc(userData.label) + '</span>' +
                    '<span class="user-selected-clear" title="Quitar selección"><i class="bi bi-x-lg"></i></span>';
                fldUserSelected.classList.remove('d-none');
                // Listener para borrar
                var clearBtn = fldUserSelected.querySelector('.user-selected-clear');
                if (clearBtn) {
                    clearBtn.addEventListener('click', function() {
                        if (fldUser) fldUser.value = '';
                        if (fldUserSearch) {
                            fldUserSearch.value = '';
                            fldUserSearch.focus();
                        }
                        renderUserSelected(null);
                        dbgLog('info', 'Titular deseleccionado');
                    });
                }
            }
        }

        /* Render del dropdown con los items coincidentes */
        function renderUserDropdown(items, activeIdx) {
            if (!fldUserDropdown) return;
            if (items.length === 0) {
                fldUserDropdown.innerHTML = '<div class="user-empty-msg">Sin resultados…</div>';
                fldUserDropdown.classList.remove('d-none');
                return;
            }
            var html = '';
            items.forEach(function(item, idx) {
                var cls = 'user-dropdown-item' + (idx === activeIdx ? ' active' : '');
                html += '<div class="' + cls + '" data-user-id="' + esc(item.value) + '">' + esc(item.label) + '</div>';
            });
            fldUserDropdown.innerHTML = html;
            fldUserDropdown.classList.remove('d-none');

            // Click en un item
            fldUserDropdown.querySelectorAll('.user-dropdown-item').forEach(function(el) {
                el.addEventListener('click', function() {
                    var userId = this.getAttribute('data-user-id');
                    seleccionarTitular(userId);
                });
            });
        }

        /* Seleccionar un titular por ID */
        function seleccionarTitular(userId) {
            if (!fldUser) return;
            fldUser.value = userId;
            var userData = userOriginalData.find(function(o) { return String(o.value) === String(userId); });
            if (userData && fldUserSearch) {
                // Mostrar el nombre (sin la parte de email) en el input
                var labelShort = userData.label.split(' ·')[0];
                fldUserSearch.value = labelShort;
            }
            renderUserSelected(userId);
            if (fldUserDropdown) fldUserDropdown.classList.add('d-none');
            dbgLog('ok', 'Titular seleccionado: ID=' + userId + ' · ' + (userData ? userData.label : ''));
        }

        /* Filtrar titulares según término de búsqueda */
        function filtrarYMostrarTitulares(term) {
            var t = (term || '').toLowerCase().trim();
            // Si hay selección previa y el término cambió, limpiar
            if (fldUser && fldUser.value) {
                var userData = userOriginalData.find(function(o) { return String(o.value) === String(fldUser.value); });
                if (userData) {
                    var labelShort = userData.label.split(' ·')[0].toLowerCase();
                    if (t !== labelShort && t.indexOf(labelShort) !== 0) {
                        fldUser.value = '';
                        renderUserSelected(null);
                    }
                }
            }
            if (!t) {
                if (fldUserDropdown) fldUserDropdown.classList.add('d-none');
                if (userCountTxt) userCountTxt.textContent = 'Total titulares: ' + userOriginalData.length;
                return;
            }
            var matches = userOriginalData.filter(function(o) {
                return o.dataSearch.indexOf(t) !== -1;
            }).slice(0, 10); // limitar a 10 resultados
            renderUserDropdown(matches, 0);
            if (userCountTxt) {
                userCountTxt.innerHTML = 'Coincidencias: <b>' + matches.length + '</b>';
            }
        }

        if (fldUserSearch) {
            fldUserSearch.addEventListener('input', function() {
                filtrarYMostrarTitulares(this.value);
            });
            fldUserSearch.addEventListener('focus', function() {
                if (this.value.trim()) {
                    filtrarYMostrarTitulares(this.value);
                }
            });
            fldUserSearch.addEventListener('blur', function() {
                // Delay para permitir click en item
                setTimeout(function() {
                    if (fldUserDropdown) fldUserDropdown.classList.add('d-none');
                }, 200);
            });
            fldUserSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var activeItem = fldUserDropdown.querySelector('.user-dropdown-item.active');
                    if (activeItem) {
                        var userId = activeItem.getAttribute('data-user-id');
                        seleccionarTitular(userId);
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    var current = fldUserDropdown.querySelector('.user-dropdown-item.active');
                    if (current) {
                        current.classList.remove('active');
                        var next = current.nextElementSibling;
                        if (next && next.classList.contains('user-dropdown-item')) {
                            next.classList.add('active');
                            next.scrollIntoView({block: 'nearest'});
                        } else {
                            // Si no hay siguiente, volver al primero
                            var first = fldUserDropdown.querySelector('.user-dropdown-item');
                            if (first) first.classList.add('active');
                        }
                    } else {
                        var firstItem = fldUserDropdown.querySelector('.user-dropdown-item');
                        if (firstItem) firstItem.classList.add('active');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    var current2 = fldUserDropdown.querySelector('.user-dropdown-item.active');
                    if (current2) {
                        current2.classList.remove('active');
                        var prev = current2.previousElementSibling;
                        if (prev && prev.classList.contains('user-dropdown-item')) {
                            prev.classList.add('active');
                            prev.scrollIntoView({block: 'nearest'});
                        } else {
                            // Si no hay anterior, ir al último
                            var allItems = fldUserDropdown.querySelectorAll('.user-dropdown-item');
                            if (allItems.length > 0) {
                                allItems[allItems.length - 1].classList.add('active');
                            }
                        }
                    }
                } else if (e.key === 'Escape') {
                    if (fldUserDropdown) fldUserDropdown.classList.add('d-none');
                }
            });
        }


        /* ═══════════════════════════════════════════════════════════════
           🆕 v24 · CHIPS DE COMENTARIO (respuestas rápidas)
           · Click en chip → agrega el texto al textarea
           · Si ya está agregado, no lo duplica
           · Separa frases con " · "
           · El chip "added" marca los que ya están en el textarea
           ═══════════════════════════════════════════════════════════════ */
        var fldComentario = document.getElementById('fldComentario');
        document.querySelectorAll('.com-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                if (!fldComentario) return;
                var txt = this.getAttribute('data-text');
                var current = fldComentario.value.trim();
                if (current && current.indexOf(txt) !== -1) {
                    // Ya está, no agregar
                    return;
                }
                if (current) {
                    fldComentario.value = current + ' · ' + txt;
                } else {
                    fldComentario.value = txt;
                }
                // Marcar como agregado
                this.classList.add('added');
                fldComentario.focus();
                // Mover cursor al final
                fldComentario.selectionStart = fldComentario.selectionEnd = fldComentario.value.length;
                dbgLog('debug', 'Frase agregada al comentario: ' + txt);
            });
        });

        // Desmarcar chips cuando el texto se borra
        if (fldComentario) {
            fldComentario.addEventListener('input', function() {
                var val = this.value;
                document.querySelectorAll('.com-chip').forEach(function(chip) {
                    var txt = chip.getAttribute('data-text');
                    if (val.indexOf(txt) !== -1) {
                        chip.classList.add('added');
                    } else {
                        chip.classList.remove('added');
                    }
                });
            });
        }


        /* ═══════════════════════════════════════════════════════════════
           MINI-CALENDARIO v24 · tarifa obligatoria + alistamiento
           + auto-llenar monto
           ═══════════════════════════════════════════════════════════════ */
        var calState = {
            year: new Date().getFullYear(),
            month: new Date().getMonth(),
            startSel: null, endSel: null,
            occupied: [], tarifas: [],
            montoMinBase: 200000,
            inmuebleId: null,
            loading: false
        };

        var MONTH_NAMES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        var DOW_NAMES   = ['L','M','M','J','V','S','D'];

        function calFmt(d) {
            var y = d.getFullYear();
            var m = ('0'+(d.getMonth()+1)).slice(-2);
            var day = ('0'+d.getDate()).slice(-2);
            return y+'-'+m+'-'+day;
        }
        function calParse(s) {
            var p = s.split('-');
            return new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2]));
        }

        function getCellType(date) {
            var t = date.getTime();
            var isReserva = false;
            var isAlistamiento = false;
            var isBloqueo = false;
            for (var i=0; i<calState.occupied.length; i++) {
                var o = calState.occupied[i];
                var ini = calParse(o.inicio).getTime();
                var fin = calParse(o.fin).getTime();
                if (t >= ini && t <= fin) {
                    if (o.tipo === 'reserva') isReserva = true;
                    else if (o.tipo === 'alistamiento') isAlistamiento = true;
                    else if (o.tipo === 'bloqueo') isBloqueo = true;
                }
            }
            if (isReserva) return 'reserva';
            if (isBloqueo) return 'bloqueo';
            if (isAlistamiento) return 'alistamiento';
            return null;
        }

        function isOccupied(date) {
            return getCellType(date) !== null;
        }

        /* v25 - getCellInfo: devuelve el objeto completo de ocupacion
           para una fecha (con titular, codigo, tipo). */
        function getCellInfo(date) {
            var t = date.getTime();
            var resultReserva = null;
            var resultAli = null;
            var resultBloqueo = null;
            for (var i=0; i<calState.occupied.length; i++) {
                var o = calState.occupied[i];
                var ini = calParse(o.inicio).getTime();
                var fin = calParse(o.fin).getTime();
                if (t >= ini && t <= fin) {
                    if (o.tipo === 'reserva' && !resultReserva) resultReserva = o;
                    else if (o.tipo === 'alistamiento' && !resultAli) resultAli = o;
                    else if (o.tipo === 'bloqueo' && !resultBloqueo) resultBloqueo = o;
                }
            }
            if (resultReserva) return resultReserva;
            if (resultBloqueo) return resultBloqueo;
            if (resultAli) return resultAli;
            return null;
        }

        /* v25 - mostrarInfoReserva: popup flotante con titular */
        function mostrarInfoReserva(info, cellEl) {
            var existing = document.getElementById('calInfoPopup');
            if (existing) existing.remove();

            var popup = document.createElement('div');
            popup.id = 'calInfoPopup';
            popup.className = 'cal-info-popup';

            var tipoLabel = '', tipoIcon = '';
            if (info.tipo === 'reserva') { tipoLabel = 'Reservada'; tipoIcon = '🔒'; }
            else if (info.tipo === 'alistamiento') { tipoLabel = 'Alistamiento 🧹'; tipoIcon = '🧹'; }
            else { tipoLabel = 'Bloqueada'; tipoIcon = '⛔'; }

            var titular = info.titular || {};
            var html = '';
            html += '<div class="cal-info-header">';
            html += '<b>' + tipoIcon + ' ' + tipoLabel + '</b>';
            html += '<span class="cal-info-close">✕</span>';
            html += '</div>';
            html += '<div class="cal-info-body">';
            if (info.codigo) {
                html += '<div class="cal-info-row"><i class="bi bi-hash"></i><span class="cal-info-label">Codigo</span><span class="cal-info-value">' + esc(info.codigo) + '</span></div>';
            }
            if (titular.name) {
                html += '<div class="cal-info-row"><i class="bi bi-person-fill"></i><span class="cal-info-label">Titular</span><span class="cal-info-value">' + esc(titular.name) + '</span></div>';
            }
            if (titular.nid) {
                html += '<div class="cal-info-row"><i class="bi bi-credit-card-2-front"></i><span class="cal-info-label">Cedula</span><span class="cal-info-value">' + esc(titular.nid) + '</span></div>';
            }
            if (titular.email) {
                html += '<div class="cal-info-row"><i class="bi bi-envelope"></i><span class="cal-info-label">Email</span><span class="cal-info-value">' + esc(titular.email) + '</span></div>';
            }
            html += '<div class="cal-info-row"><i class="bi bi-calendar3"></i><span class="cal-info-label">Rango</span><span class="cal-info-value">' + info.inicio + ' → ' + info.fin + '</span></div>';
            html += '</div>';

            popup.innerHTML = html;
            document.body.appendChild(popup);

            var rect = cellEl.getBoundingClientRect();
            var popupWidth = 260;
            var popupHeight = popup.offsetHeight || 150;
            var left = rect.left;
            var top = rect.bottom + 5;
            if (left + popupWidth > window.innerWidth - 10) { left = window.innerWidth - popupWidth - 10; }
            if (top + popupHeight > window.innerHeight - 10) { top = rect.top - popupHeight - 5; }
            if (top < 10) { top = rect.bottom + 5; }
            popup.style.top = top + 'px';
            popup.style.left = left + 'px';

            dbgLog('info', '<b>Popup info</b>: ' + tipoLabel + ' · ' + (titular.name || 'sin titular') + ' · ' + (info.codigo || 'sin codigo'));

            var closeBtn = popup.querySelector('.cal-info-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) { e.stopPropagation(); popup.remove(); });
            }
            setTimeout(function() {
                function closeOnOutside(e) {
                    if (!popup.contains(e.target) && !cellEl.contains(e.target)) {
                        popup.remove();
                        document.removeEventListener('click', closeOnOutside);
                    }
                }
                document.addEventListener('click', closeOnOutside);
            }, 100);
        }

        function rangeHasOccupied(d1, d2) {
            var cur = new Date(d1.getTime());
            while (cur <= d2) {
                if (isOccupied(cur)) return true;
                cur.setDate(cur.getDate()+1);
            }
            return false;
        }

        function tarifasEnRango(start, end) {
            var sTs = start.getTime();
            var eTs = end.getTime();
            var result = [];
            for (var i=0; i<calState.tarifas.length; i++) {
                var t = calState.tarifas[i];
                var tIni = calParse(t.inicio).getTime();
                var tFin = calParse(t.fin).getTime();
                if (tIni < eTs && tFin > sTs) {
                    result.push(t);
                }
            }
            return result;
        }

        function tarifaMasRestrictiva(start, end) {
            var tarifas = tarifasEnRango(start, end);
            if (tarifas.length === 0) return null;
            var masRestrictiva = null;
            var menorDiasMax = Infinity;
            for (var i=0; i<tarifas.length; i++) {
                var t = tarifas[i];
                if (t.dias_maximos > 0 && t.dias_maximos < menorDiasMax) {
                    menorDiasMax = t.dias_maximos;
                    masRestrictiva = t;
                }
            }
            if (!masRestrictiva) {
                masRestrictiva = tarifas[0];
            }
            return masRestrictiva;
        }

        function diasMaximosMasRestrictivo(start, end) {
            var tarifas = tarifasEnRango(start, end);
            var menor = 0;
            for (var i=0; i<tarifas.length; i++) {
                var dm = parseInt(tarifas[i].dias_maximos || 0);
                if (dm > 0) {
                    if (menor === 0 || dm < menor) {
                        menor = dm;
                    }
                }
            }
            return menor;
        }

        function tarifaParaNoche(fecha) {
            var t = fecha.getTime();
            for (var i=0; i<calState.tarifas.length; i++) {
                var tarifa = calState.tarifas[i];
                var tIni = calParse(tarifa.inicio).getTime();
                var tFin = calParse(tarifa.fin).getTime();
                if (t >= tIni && t <= tFin) return tarifa;
            }
            return null;
        }

        function rangoTotalmenteCubierto(start, end) {
            var cur = new Date(start.getTime());
            var total = 0;
            var nochesSinTarifa = 0;
            while (cur < end) {
                var tarifa = tarifaParaNoche(cur);
                if (tarifa) {
                    var dow = cur.getDay();
                    var esFinDeSemana = (dow === 5 || dow === 6 || dow === 0);
                    var precioDia = esFinDeSemana
                        ? (tarifa.precio_fin_semana > 0 ? tarifa.precio_fin_semana : tarifa.precio_noche)
                        : tarifa.precio_noche;
                    total += parseFloat(precioDia || 0);
                } else {
                    nochesSinTarifa++;
                }
                cur.setDate(cur.getDate()+1);
            }
            return {
                ok: nochesSinTarifa === 0,
                nochesSinTarifa: nochesSinTarifa,
                total: total
            };
        }

        function precioSugerido(start, end) {
            var cur = new Date(start.getTime());
            var total = 0;
            var detalle = {};
            var nochesSinTarifa = 0;
            while (cur < end) {
                var tarifa = tarifaParaNoche(cur);
                if (tarifa) {
                    var dow = cur.getDay();
                    var esFinDeSemana = (dow === 5 || dow === 6 || dow === 0);
                    var precioDia = esFinDeSemana
                        ? (tarifa.precio_fin_semana > 0 ? tarifa.precio_fin_semana : tarifa.precio_noche)
                        : tarifa.precio_noche;
                    var precioNum = parseFloat(precioDia || 0);
                    total += precioNum;
                    if (!detalle[tarifa.nombre]) {
                        detalle[tarifa.nombre] = { noches: 0, monto: 0, precio_noche: tarifa.precio_noche, precio_fin_semana: tarifa.precio_fin_semana };
                    }
                    detalle[tarifa.nombre].noches++;
                    detalle[tarifa.nombre].monto += precioNum;
                } else {
                    nochesSinTarifa++;
                }
                cur.setDate(cur.getDate()+1);
            }
            return { total: total, detalle: detalle, nochesSinTarifa: nochesSinTarifa };
        }

        function renderCal() {
            var grid = document.getElementById('miniCalGrid');
            var lbl  = document.getElementById('calMonthLbl');
            if (!grid) return;

            lbl.textContent = MONTH_NAMES[calState.month] + ' ' + calState.year;

            var html = '<div class="cal-row cal-row-head">';
            for (var i=0; i<7; i++) html += '<div class="cal-dow">'+DOW_NAMES[i]+'</div>';
            html += '</div>';

            var firstDay = new Date(calState.year, calState.month, 1);
            var firstWeekday = (firstDay.getDay() + 6) % 7;
            var daysInMonth = new Date(calState.year, calState.month+1, 0).getDate();
            var today = new Date(); today.setHours(0,0,0,0);

            var cells = [];
            for (var i=0; i<firstWeekday; i++) cells.push(null);
            for (var d=1; d<=daysInMonth; d++) cells.push(new Date(calState.year, calState.month, d));
            while (cells.length % 7 !== 0) cells.push(null);
            while (cells.length < 42) cells.push(null);

            if (calState.loading) {
                html += '<div class="cal-row" style="grid-column: 1 / -1; padding: 20px 0; text-align: center;">';
                html += '<div style="grid-column: 1 / -1; display: flex; align-items: center; justify-content: center; gap: 8px; color: #94a3b8; font-size: 8.5px;">';
                html += '<div class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px; border-width: 1.5px;"></div>';
                html += 'Cargando disponibilidad...';
                html += '</div></div>';
                grid.innerHTML = html;
                return;
            }

            var occCount = 0;
            var aliCount = 0;
            var freeCount = 0;
            for (var week=0; week<6; week++) {
                html += '<div class="cal-row">';
                for (var i=0; i<7; i++) {
                    var idx = week*7 + i;
                    var date = cells[idx];
                    if (!date) { html += '<div class="cal-cell cal-empty"></div>'; continue; }
                    var classes = 'cal-cell';
                    var ds = calFmt(date);
                    if (date < today) classes += ' cal-past';
                    else {
                        var cellType = getCellType(date);
                        if (cellType === 'reserva' || cellType === 'bloqueo') {
                            classes += ' cal-occ'; occCount++;
                        } else if (cellType === 'alistamiento') {
                            classes += ' cal-ali'; aliCount++;
                        } else if (calState.startSel && calFmt(calState.startSel) === ds) classes += ' cal-sel';
                        else if (calState.endSel && calFmt(calState.endSel) === ds) classes += ' cal-sel';
                        else if (calState.startSel && calState.endSel && date > calState.startSel && date < calState.endSel) classes += ' cal-inrange';
                        else { classes += ' cal-free'; freeCount++; }
                    }
                    html += '<div class="'+classes+'" data-date="'+ds+'">'+date.getDate()+'</div>';
                }
                html += '</div>';
            }
            grid.innerHTML = html;

            // v25 - Cerrar popup de info si estaba abierto al re-render
            var existingPopup = document.getElementById('calInfoPopup');
            if (existingPopup) existingPopup.remove();

            dbgLog('debug', '<b>Cal render</b> mes=' + (calState.month+1) + '/' + calState.year + ' · reservadas=' + occCount + ' alistamiento=' + aliCount + ' libres=' + freeCount + ' (inmueble=' + calState.inmuebleId + ')');

            grid.querySelectorAll('.cal-cell[data-date]').forEach(function(c) {
                c.addEventListener('click', function(e) {
                    e.stopPropagation();

                    // v25 - Click en dia pasado -> ignorar
                    if (c.classList.contains('cal-past')) return;

                    // v25 - Click en dia RESERVADO o ALISTAMIENTO -> mostrar popup
                    if (c.classList.contains('cal-occ') || c.classList.contains('cal-ali')) {
                        var ds = c.getAttribute('data-date');
                        var info = getCellInfo(calParse(ds));
                        if (info) {
                            mostrarInfoReserva(info, c);
                        } else {
                            dbgLog('warn', 'Click en dia bloqueado pero no se encontro info para ' + ds);
                        }
                        return;
                    }
                    var ds = c.getAttribute('data-date');
                    var date = calParse(ds);

                    if (!calState.startSel || (calState.startSel && calState.endSel)) {
                        calState.startSel = date;
                        calState.endSel = null;
                        dbgLog('info', '<b>Cal click</b> 1: check-in = ' + ds);
                    } else if (calState.startSel && !calState.endSel) {
                        if (date <= calState.startSel) {
                            calState.startSel = date;
                            dbgLog('info', '<b>Cal click</b> reset → check-in = ' + ds);
                        } else if (rangeHasOccupied(calState.startSel, date)) {
                            dbgLog('warn', '<b>Cal click</b> rango incluye días ocupados/alistamiento, reset');
                            alert('El rango seleccionado incluye fechas ocupadas o de alistamiento. Seleccione otro rango.');
                            calState.startSel = date;
                            calState.endSel = null;
                        } else {
                            var noches = Math.round((date - calState.startSel) / 86400000);

                            var tarifasRango = tarifasEnRango(calState.startSel, date);
                            if (tarifasRango.length === 0) {
                                dbgLog('warn', '<b>Cal click</b> SIN TARIFA para el rango ' + calFmt(calState.startSel) + ' → ' + calFmt(date));
                                alert('No hay tarifa configurada para el rango ' + calFmt(calState.startSel) + ' → ' + calFmt(date) + '.\n\nConfigure una tarifa en el catálogo (Tarifas por Temporada) que cubra este período antes de crear la reserva.');
                                calState.startSel = date;
                                calState.endSel = null;
                            } else {
                                var cobertura = rangoTotalmenteCubierto(calState.startSel, date);
                                if (!cobertura.ok) {
                                    dbgLog('warn', '<b>Cal click</b> ' + cobertura.nochesSinTarifa + ' noche(s) SIN tarifa en el rango');
                                    alert('El rango seleccionado tiene ' + cobertura.nochesSinTarifa + ' noche(s) SIN tarifa configurada.\n\nAjuste las tarifas por temporada para que cubran todo el rango, o reduzca el rango a uno cubierto por una tarifa existente.');
                                    calState.startSel = date;
                                    calState.endSel = null;
                                } else {
                                    var diasMax = diasMaximosMasRestrictivo(calState.startSel, date);
                                    if (diasMax > 0 && noches > diasMax) {
                                        var tarifaRestr = tarifaMasRestrictiva(calState.startSel, date);
                                        dbgLog('warn', '<b>Cal click</b> excede días máx (' + diasMax + ') de ' + (tarifaRestr ? tarifaRestr.nombre : 'desconocida'));
                                        alert('La tarifa "' + (tarifaRestr ? tarifaRestr.nombre : 'más restrictiva') + '" permite máximo ' + diasMax + ' noches. Seleccionaste ' + noches + '.\n\nReduce el rango para no superar el máximo permitido.');
                                        calState.startSel = date;
                                        calState.endSel = null;
                                    } else {
                                        calState.endSel = date;
                                        dbgLog('ok', '<b>Cal click</b> 2: check-out = ' + ds + ' · noches=' + noches + (diasMax > 0 ? ' · máx=' + diasMax : ' · sin máx') + ' · tarifa cubre todo');
                                    }
                                }
                            }
                        }
                    }
                    updateSelectionUI();
                    renderCal();
                });
            });
            updateSelectionUI();
        }

        function updateSelectionUI() {
            var info   = document.getElementById('calSelInfo');
            var box    = document.getElementById('calTarifaBox');
            var fiFld  = document.getElementById('fldFechaIni');
            var ffFld  = document.getElementById('fldFechaFin');
            var montoInput = document.getElementById('fldMonto');
            var montoMinLbl = document.getElementById('montoMinLbl');
            var montoHelp   = document.getElementById('montoHelp');

            if (calState.loading) {
                if (info) info.innerHTML = '<i class="bi bi-arrow-clockwise"></i> <em>Cargando disponibilidad...</em>';
                if (box)  { box.classList.add('d-none'); box.innerHTML = ''; }
                if (fiFld) fiFld.value = '';
                if (ffFld) ffFld.value = '';
                return;
            }

            if (!calState.inmuebleId) {
                if (info) info.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione un inmueble para ver disponibilidad.';
                if (box)  { box.classList.add('d-none'); box.innerHTML = ''; }
                if (fiFld) fiFld.value = '';
                if (ffFld) ffFld.value = '';
                if (montoInput)   montoInput.min = calState.montoMinBase;
                if (montoMinLbl)  montoMinLbl.textContent = '(mín. ' + money0(calState.montoMinBase) + ')';
                if (montoHelp)    montoHelp.textContent = 'Valor mínimo permitido: ' + money0(calState.montoMinBase) + '.';
                return;
            }

            if (calState.occupied.length === 0 && !calState.startSel) {
                if (info) {
                    info.innerHTML = '<i class="bi bi-check-circle" style="color:#5d8a70;"></i> Inmueble disponible · <b>0 reservas</b> activas. Seleccione fecha de check-in.';
                }
            }

            if (calState.startSel && calState.endSel) {
                var noches = Math.round((calState.endSel - calState.startSel) / 86400000);
                var tarifasRango = tarifasEnRango(calState.startSel, calState.endSel);
                var tarifaRestr = tarifaMasRestrictiva(calState.startSel, calState.endSel);
                var diasMax = diasMaximosMasRestrictivo(calState.startSel, calState.endSel);
                var minAplicable = calState.montoMinBase;
                var calc = precioSugerido(calState.startSel, calState.endSel);
                var precioSug = calc.total;

                for (var i=0; i<tarifasRango.length; i++) {
                    if (tarifasRango[i].precio_minimo_reserva > 0) {
                        minAplicable = Math.max(minAplicable, tarifasRango[i].precio_minimo_reserva);
                    }
                }

                if (info) {
                    info.innerHTML = '<i class="bi bi-calendar-check"></i> Check-in: <b>'+calFmt(calState.startSel)+'</b> · Check-out: <b>'+calFmt(calState.endSel)+'</b> · <b>'+noches+'</b> noche(s)';
                }
                if (fiFld) fiFld.value = calFmt(calState.startSel);
                if (ffFld) ffFld.value = calFmt(calState.endSel);

                if (box) {
                    var h = '';
                    if (tarifasRango.length === 0) {
                        h = '<i class="bi bi-x-octagon tarifa-err"></i> <span class="tarifa-err">SIN TARIFA CONFIGURADA</span> para este rango.<br>';
                        h += '<small>No se puede crear la reserva sin tarifa. Configure una tarifa que cubra este período en el catálogo <b>Tarifas por Temporada</b>.</small><br>';
                        h += '<span class="precio-sug small">Mínimo aplicable: ' + money0(minAplicable) + ' (sistema)</span>';
                    } else if (calc.nochesSinTarifa > 0) {
                        h = '<i class="bi bi-exclamation-triangle tarifa-warn"></i> <span class="tarifa-warn">' + calc.nochesSinTarifa + ' noche(s) SIN tarifa</span> en el rango.<br>';
                        h += '<small>Algunas noches del rango no tienen tarifa configurada. Ajuste las tarifas para cubrir todo el período o reduzca el rango.</small><br>';
                    } else {
                        h += '<i class="bi bi-shield-lock"></i> <b>Tarifa más restrictiva:</b> ' + esc(tarifaRestr.nombre);
                        if (diasMax > 0) {
                            h += ' · <span class="' + (noches === diasMax ? 'tarifa-warn' : '') + '">máx ' + diasMax + ' noches</span>';
                            if (noches === diasMax) {
                                h += ' <small>(estás en el límite)</small>';
                            } else {
                                h += ' <small>(llevas ' + noches + ')</small>';
                            }
                        }
                        h += '<br>';

                        h += '<b>Tarifas aplicables al rango (' + tarifasRango.length + '):</b><br>';
                        var idx = 0;
                        for (var nombre in calc.detalle) {
                            if (!calc.detalle.hasOwnProperty(nombre)) continue;
                            var d = calc.detalle[nombre];
                            idx++;
                            h += '<div class="tarifa-line">';
                            h += '<small><b>' + idx + '. ' + esc(nombre) + '</b> · ' + d.noches + ' noche(s) · $/noche: ' + money(d.precio_noche);
                            if (d.precio_fin_semana > 0) h += ' · Finde: ' + money(d.precio_fin_semana);
                            h += ' · Subtotal: <b>' + money(d.monto) + '</b></small>';
                            h += '</div>';
                        }

                        h += '<br><small>Mín. reserva aplicable: <b>' + money0(minAplicable) + '</b></small><br>';
                        h += '<span class="precio-sug">Precio sugerido: ' + money(precioSug) + ' <small>(' + noches + ' noches)</small></span>';
                    }
                    box.innerHTML = h;
                    box.classList.remove('d-none');
                }

                if (montoInput)   montoInput.min = minAplicable;
                if (montoMinLbl)  montoMinLbl.textContent = '(mín. ' + money0(minAplicable) + ')';
                if (montoHelp) {
                    montoHelp.textContent = 'Mínimo aplicable: ' + money0(minAplicable)
                        + (tarifaRestr ? ' (tarifa más restrictiva: ' + tarifaRestr.nombre + ')' : ' (sistema)');
                }

                /* 🆕 v24 · AUTO-LLENAR EL MONTO con el precio sugerido
                   (o mínimo si no hay precio sugerido > 0). */
                if (montoInput) {
                    var autoMonto = 0;
                    if (precioSug > 0) {
                        autoMonto = precioSug;
                    } else if (minAplicable > 0) {
                        autoMonto = minAplicable;
                    }
                    if (autoMonto > 0) {
                        montoInput.value = autoMonto;
                        dbgLog('info', '<b>Monto auto-llenado</b>: ' + autoMonto + (precioSug > 0 ? ' (precio sugerido)' : ' (mínimo aplicable)'));
                    }
                }
            } else if (calState.startSel) {
                if (info) info.innerHTML = '<i class="bi bi-arrow-right-circle"></i> Check-in: <b>'+calFmt(calState.startSel)+'</b> · Ahora seleccione el check-out';
                if (box)  { box.classList.add('d-none'); box.innerHTML = ''; }
                if (fiFld) fiFld.value = calFmt(calState.startSel);
                if (ffFld) ffFld.value = '';
                if (montoInput) montoInput.value = ''; // 🆕 v24 · limpiar monto mientras no hay rango completo
            } else {
                if (info) info.innerHTML = '<i class="bi bi-hand-index"></i> Haga clic en una fecha libre para elegir el check-in';
                if (box)  { box.classList.add('d-none'); box.innerHTML = ''; }
                if (fiFld) fiFld.value = '';
                if (ffFld) ffFld.value = '';
                if (montoInput)   { montoInput.min = calState.montoMinBase; montoInput.value = ''; } // 🆕 v24 · limpiar monto
                if (montoMinLbl)  montoMinLbl.textContent = '(mín. ' + money0(calState.montoMinBase) + ')';
                if (montoHelp)    montoHelp.textContent = 'Valor mínimo permitido: ' + money0(calState.montoMinBase) + '. Se auto-llena al seleccionar el rango.';
            }
        }

        function fetchOccupied(inmuebleId) {
            if (!inmuebleId) {
                dbgLog('warn', 'fetchOccupied: inmuebleId vacío, no se hace fetch');
                return;
            }
            var base = '{{ $modalFechasUrl }}';
            var url = base.replace('__INM__', inmuebleId);
            dbgLog('info', '<b>fetchOccupied</b> → ' + url);

            calState.loading = true;
            renderCal();

            fetch(url, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(function(r){
                dbgLog('ok', '<b>fetchOccupied</b> HTTP ' + r.status);
                return r.json();
            })
            .then(function(j){
                calState.loading = false;
                if (j.ok) {
                    calState.occupied = j.ocupadas || [];
                    calState.tarifas   = j.tarifas  || [];
                    calState.montoMinBase = j.monto_min_base || 200000;
                    var cntReserva = 0, cntAli = 0, cntBloqueo = 0;
                    calState.occupied.forEach(function(o) {
                        if (o.tipo === 'reserva') cntReserva++;
                        else if (o.tipo === 'alistamiento') cntAli++;
                        else if (o.tipo === 'bloqueo') cntBloqueo++;
                    });
                    dbgLog('ok', '<b>fetchOccupied OK</b> total=' + calState.occupied.length
                        + ' · reservas=' + cntReserva
                        + ' · alistamientos=' + cntAli
                        + ' · bloqueos=' + cntBloqueo
                        + ' · tarifas=' + calState.tarifas.length);
                    if (calState.tarifas.length > 0) {
                        calState.tarifas.forEach(function(t, i) {
                            dbgLog('debug', '  tarifa[' + i + ']: ' + t.nombre + ' ' + t.inicio + '→' + t.fin
                                + ' · $/noche=' + t.precio_noche
                                + ' · finde=' + t.precio_fin_semana
                                + ' · mín=' + t.precio_minimo_reserva
                                + ' · max_días=' + t.dias_maximos);
                        });
                    }
                    if (calState.occupied.length === 0) {
                        dbgLog('info', 'ℹ️ El inmueble ' + inmuebleId + ' no tiene fechas ocupadas. Calendario queda libre.');
                    }
                } else {
                    calState.occupied = [];
                    calState.tarifas = [];
                    calState.montoMinBase = 200000;
                    dbgLog('err', '<b>fetchOccupied FAIL</b>: ' + (j.message || 'sin mensaje'));
                }
                renderCal();
            })
            .catch(function(err){
                calState.loading = false;
                calState.occupied = [];
                calState.tarifas = [];
                calState.montoMinBase = 200000;
                dbgLog('err', '<b>fetchOccupied</b> red/error: ' + (err.message || err));
                renderCal();
            });
        }

        var fldInm = document.getElementById('fldInmueble');
        if (fldInm) {
            var initialInm = fldInm.value || (fldInm.disabled ? '{{ (string) ($modalInmPreselect ?? '') }}' : '');
            dbgLog('info', 'fldInmueble inicial = "' + initialInm + '" · disabled=' + fldInm.disabled);

            fldInm.addEventListener('change', function() {
                dbgLog('info', 'Cambio de inmueble → ' + this.value);
                calState.inmuebleId = this.value || null;
                calState.startSel = null;
                calState.endSel = null;
                calState.occupied = [];
                calState.tarifas = [];
                if (this.value) {
                    fetchOccupied(this.value);
                } else {
                    renderCal();
                }
            });

            if (initialInm) {
                calState.inmuebleId = initialInm;
                var modalCheck = document.getElementById('modalNuevaReserva');
                if (modalCheck && modalCheck.classList.contains('show')) {
                    dbgLog('info', 'Modal ya visible, cargando fechas ocupadas para ' + initialInm);
                    fetchOccupied(initialInm);
                } else {
                    dbgLog('info', 'Modal cerrado, esperando apertura para cargar ocupadas de inmueble ' + initialInm);
                    renderCal();
                }
            } else {
                renderCal();
            }
        }

        var calPrev = document.getElementById('calPrevMonth');
        var calNext = document.getElementById('calNextMonth');
        if (calPrev) {
            calPrev.addEventListener('click', function() {
                calState.month--;
                if (calState.month < 0) { calState.month = 11; calState.year--; }
                renderCal();
            });
        }
        if (calNext) {
            calNext.addEventListener('click', function() {
                calState.month++;
                if (calState.month > 11) { calState.month = 0; calState.year++; }
                renderCal();
            });
        }


        /* ═══════════════════════════════════════════════════════════════
           MODAL NUEVA RESERVA · AJAX submit
           ═══════════════════════════════════════════════════════════════ */
        var formNR     = document.getElementById('formNuevaReserva');
        var alertNR    = document.getElementById('formNuevaReservaAlert');
        var btnGuardar = document.getElementById('btnGuardarReserva');
        var modalEl    = document.getElementById('modalNuevaReserva');
        var bsModal    = modalEl ? (window.bootstrap ? bootstrap.Modal.getOrCreateInstance(modalEl) : null) : null;

        function resetModalYRecargar() {
            dbgLog('info', '<b>Modal</b> abierto · reseteando formulario...');
            if (alertNR) { alertNR.classList.add('d-none'); alertNR.textContent = ''; }
            if (formNR) {
                formNR.classList.remove('was-validated');
                formNR.reset();
                calState.startSel = null;
                calState.endSel = null;
                calState.occupied = [];
                calState.tarifas = [];

                @if($modalInmPreselect)
                    var selInm = document.getElementById('fldInmueble');
                    if (selInm) {
                        selInm.value = '{{ (string) $modalInmPreselect }}';
                        calState.inmuebleId = selInm.value;
                        dbgLog('info', '<b>Modal</b> inmueble preseleccionado = ' + selInm.value + ' · cargando ocupadas...');
                        fetchOccupied(selInm.value);
                    }
                @else
                    var selInm = document.getElementById('fldInmueble');
                    if (selInm && selInm.value) {
                        calState.inmuebleId = selInm.value;
                        dbgLog('info', '<b>Modal</b> inmueble ya seleccionado = ' + selInm.value + ' · refetching...');
                        fetchOccupied(selInm.value);
                    } else {
                        calState.inmuebleId = null;
                        renderCal();
                    }
                @endif

                @if($modalPendienteId)
                    var selSt = document.getElementById('fldStatus');
                    if (selSt) { selSt.value = '{{ (string) $modalPendienteId }}'; }
                @endif

                // 🆕 v24 · Resetear buscador de titular + chip de seleccionado
                if (fldUserSearch) fldUserSearch.value = '';
                if (fldUser) fldUser.value = '';
                renderUserSelected(null);
                if (fldUserDropdown) fldUserDropdown.classList.add('d-none');
                if (userCountTxt) userCountTxt.textContent = 'Total titulares: ' + userOriginalData.length;

                // 🆕 v24 · Asegurar que Origen tenga la 3ra opción seleccionada
                var fldOrigen = document.getElementById('fldOrigen');
                if (fldOrigen && fldOrigen.options.length >= 3) {
                    fldOrigen.selectedIndex = 2; // 🆕 v24 · 3ra opción
                } else if (fldOrigen && fldOrigen.options.length > 0) {
                    // Si hay menos de 3, seleccionar la última disponible
                    fldOrigen.selectedIndex = fldOrigen.options.length - 1;
                }

                // 🆕 v24 · Resetear monto y chips de comentario
                var montoInputReset = document.getElementById('fldMonto');
                if (montoInputReset) { montoInputReset.value = ''; montoInputReset.classList.remove('is-invalid'); }
                if (fldComentario) fldComentario.value = '';
                document.querySelectorAll('.com-chip').forEach(function(c) { c.classList.remove('added'); });
            }
        }

        if (modalEl) {
            modalEl.addEventListener('show.bs.modal', function () {
                dbgLog('ok', 'Evento show.bs.modal disparado');
                resetModalYRecargar();
            });

            modalEl.addEventListener('shown.bs.modal', function () {
                if (!calState.inmuebleId && calState.occupied.length === 0) {
                    var selInm = document.getElementById('fldInmueble');
                    if (selInm && selInm.value) {
                        dbgLog('warn', '⚠️ show.bs.modal no cargó ocupadas · backup shown.bs.modal disparando fetch');
                        calState.inmuebleId = selInm.value;
                        fetchOccupied(selInm.value);
                    } else if (selInm) {
                        var opts = selInm.querySelectorAll('option');
                        var firstInmId = null;
                        for (var i = 0; i < opts.length; i++) {
                            if (opts[i].value && opts[i].value !== '') {
                                firstInmId = opts[i].value;
                                break;
                            }
                        }
                        if (firstInmId) {
                            dbgLog('info', 'Modo global: auto-cargando primer inmueble = ' + firstInmId + ' (preview)');
                            selInm.value = firstInmId;
                            calState.inmuebleId = firstInmId;
                            fetchOccupied(firstInmId);
                        }
                    }
                }
            });

            dbgLog('ok', 'Listeners show.bs.modal + shown.bs.modal registrados en #modalNuevaReserva');
        } else {
            dbgLog('err', '#modalNuevaReserva NO encontrado en el DOM');
        }

        if (formNR) {
            formNR.addEventListener('submit', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var fiVal = document.getElementById('fldFechaIni').value;
                var ffVal = document.getElementById('fldFechaFin').value;
                if (!fiVal || !ffVal) {
                    dbgLog('warn', '<b>Submit</b> cancelado: fechas incompletas (fi=' + fiVal + ', ff=' + ffVal + ')');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Seleccione el rango de fechas en el calendario.';
                        alertNR.classList.remove('d-none');
                    }
                    return;
                }

                /* DEFENSA FINAL ANTI-SOLAPAMIENTO */
                var fiDate = calParse(fiVal);
                var ffDate = calParse(ffVal);
                if (rangeHasOccupied(fiDate, ffDate)) {
                    dbgLog('warn', '<b>Submit bloqueado</b>: el rango incluye días ocupados/alistamiento (client-side check)');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-x-octagon me-1"></i>El rango seleccionado (' + fmtDMY(fiVal) + ' → ' + fmtDMY(ffVal) + ') incluye fechas ocupadas o de alistamiento. Seleccione otro rango en el calendario.';
                        alertNR.classList.remove('d-none');
                    }
                    calState.startSel = null;
                    calState.endSel = null;
                    if (calState.inmuebleId) {
                        dbgLog('info', 'Refrescando fechas ocupadas después del bloqueo...');
                        fetchOccupied(calState.inmuebleId);
                    }
                    return;
                }

                /* DEFENSA FINAL: SIN TARIFA EN RANGO */
                var tarifasRangoFinal = tarifasEnRango(fiDate, ffDate);
                if (tarifasRangoFinal.length === 0) {
                    dbgLog('warn', '<b>Submit bloqueado</b>: SIN tarifa en rango (client-side)');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-x-octagon me-1"></i><b>No hay tarifa configurada</b> para el rango ' + fmtDMY(fiVal) + ' → ' + fmtDMY(ffVal) + '. Configure una tarifa en el catálogo <b>Tarifas por Temporada</b> que cubra este período antes de crear la reserva.';
                        alertNR.classList.remove('d-none');
                    }
                    return;
                }

                /* DEFENSA FINAL: NOCHES SIN TARIFA */
                var coberturaFinal = rangoTotalmenteCubierto(fiDate, ffDate);
                if (!coberturaFinal.ok) {
                    dbgLog('warn', '<b>Submit bloqueado</b>: ' + coberturaFinal.nochesSinTarifa + ' noche(s) sin tarifa');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i><b>' + coberturaFinal.nochesSinTarifa + ' noche(s) del rango no tienen tarifa configurada</b>. Ajuste las tarifas por temporada para cubrir todo el rango, o reduzca el rango a uno cubierto por una tarifa existente.';
                        alertNR.classList.remove('d-none');
                    }
                    return;
                }

                /* DEFENSA FINAL DÍAS MÁXIMOS */
                var nochesSeleccionadas = Math.round((ffDate - fiDate) / 86400000);
                var diasMax = diasMaximosMasRestrictivo(fiDate, ffDate);
                if (diasMax > 0 && nochesSeleccionadas > diasMax) {
                    var tarifaRestr = tarifaMasRestrictiva(fiDate, ffDate);
                    dbgLog('warn', '<b>Submit bloqueado</b>: ' + nochesSeleccionadas + ' noches > máximo ' + diasMax + ' de ' + (tarifaRestr ? tarifaRestr.nombre : 'desconocida'));
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>La tarifa <b>' + esc(tarifaRestr ? tarifaRestr.nombre : 'más restrictiva') + '</b> permite máximo <b>' + diasMax + ' noches</b>. Seleccionaste <b>' + nochesSeleccionadas + '</b>. Reduce el rango.';
                        alertNR.classList.remove('d-none');
                    }
                    return;
                }

                var montoInput = document.getElementById('fldMonto');
                var montoVal = parseFloat(montoInput.value || 0);
                var minAplicable = parseFloat(montoInput.min) || 200000;
                if (isNaN(montoVal) || montoVal < minAplicable) {
                    dbgLog('warn', '<b>Submit</b> monto ' + montoVal + ' < mínimo ' + minAplicable);
                    montoInput.classList.add('is-invalid');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>El monto total debe ser mayor o igual a ' + money0(minAplicable) + '.';
                        alertNR.classList.remove('d-none');
                    }
                    return;
                }
                montoInput.classList.remove('is-invalid');

                if (!formNR.checkValidity()) {
                    formNR.classList.add('was-validated');
                    return;
                }

                var btnTxt = btnGuardar ? btnGuardar.querySelector('.btn-txt') : null;
                var originalTxt = btnTxt ? btnTxt.innerHTML : 'Crear Reserva';
                if (btnGuardar) {
                    btnGuardar.disabled = true;
                    if (btnTxt) { btnTxt.innerHTML = 'Guardando…'; }
                }
                if (alertNR) { alertNR.classList.add('d-none'); alertNR.textContent = ''; }

                var storeUrl = formNR.getAttribute('data-store') || formNR.getAttribute('action');
                var fd = new FormData(formNR);
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : (fd.get('_token') || '');

                var payload = {};
                fd.forEach(function(v, k){ payload[k] = v; });
                dbgLog('info', '<b>Submit AJAX</b> → ' + storeUrl);
                dbgLog('debug', '<b>Payload:</b> ' + JSON.stringify(payload));

                fetch(storeUrl, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                .then(function (resp) {
                    dbgLog('info', '<b>Resp</b> HTTP ' + resp.status);
                    return resp.json().then(function (j) {
                        return { ok: resp.ok, status: resp.status, json: j };
                    }).catch(function () {
                        return { ok: resp.ok, status: resp.status, json: { ok: false, message: 'Respuesta no JSON (HTTP ' + resp.status + ')' } };
                    });
                })
                .then(function (r) {
                    var j = r.json || {};
                    dbgLog(j.ok ? 'ok' : 'err', '<b>Resp JSON</b>: ' + JSON.stringify(j).substring(0, 300));
                    if (r.ok && j.ok) {
                        dbgLog('ok', '✅ Reserva creada! ID=' + (j.reserva && j.reserva.id));
                        if (bsModal) { bsModal.hide(); }
                        setTimeout(function () { window.location.reload(); }, 200);
                        return;
                    }
                    var msg = j.message || (j.errors ? Object.values(j.errors).map(function (a) { return Array.isArray(a) ? a.join(' · ') : a; }).join(' · ') : 'Error al crear la reserva.');
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>' + esc(msg);
                        alertNR.classList.remove('d-none');
                    }
                    if (btnGuardar) {
                        btnGuardar.disabled = false;
                        if (btnTxt) { btnTxt.innerHTML = originalTxt; }
                    }
                })
                .catch(function (err) {
                    dbgLog('err', '<b>Red error</b>: ' + (err.message || err));
                    if (alertNR) {
                        alertNR.innerHTML = '<i class="bi bi-wifi-off me-1"></i>Error de red: ' + esc(err.message || 'sin detalles');
                        alertNR.classList.remove('d-none');
                    }
                    if (btnGuardar) {
                        btnGuardar.disabled = false;
                        if (btnTxt) { btnTxt.innerHTML = originalTxt; }
                    }
                });
            });
        }

        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                setTimeout(limpiarOverlaysHuerfanos, 100);
            });
        }

        render();
    });
</script>
