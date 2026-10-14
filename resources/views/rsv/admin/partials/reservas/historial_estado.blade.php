{{-- partials/reservas/historial_estado.blade.php · Timeline de cambios de estado --}}
{{-- Append-Only: sin edición ni eliminación (regla de negocio del controlador). --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="heRoot"
     data-modo="{{ (isset($inmueble) && $inmueble) || (!empty($inmuebleId) && is_numeric($inmuebleId)) || (request('inmueble_id') && is_numeric(request('inmueble_id'))) ? 'single' : 'global' }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 220px; flex: 1 1 240px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .badge-lock { display: inline-flex; align-items: center; gap: 3px; font-size: 7px; font-weight: 600; color: #9aa5b1; background: #fafbfd; border: 1px solid #eef2f6; border-radius: 8px; padding: 0 7px; line-height: 14px; }
    .reservas-soft .fl-arrow { color: #c3ccd6; font-size: 8px; margin: 0 3px; }
    .reservas-soft .rv-pagination { border-top: 1px solid var(--grid); margin-top: 8px; padding-top: 7px; }
</style>

@php
    $heInmId = null;
    if (isset($inmueble) && $inmueble) { $heInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble; }
    elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) { $heInmId = $inmuebleId; }
    elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) { $heInmId = request('inmueble_id'); }
    $heGlobal = empty($heInmId);

    $heItems = collect(); $heJson = '[]'; $heTotal = 0;

    try {
        if (class_exists(\App\Models\Rsv\HistorialEstado::class)) {
            $q = \App\Models\Rsv\HistorialEstado::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'estadoAnterior:id,name,color_hex', 'estadoNuevo:id,name,color_hex', 'user:id,name']);

            if (!$heGlobal && $heInmId) {
                $q->whereHas('reserva', function ($qq) use ($heInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $heInmId);
                });
            }

            $heTotal = (clone $q)->count();

            $heItems = $q->orderBy('created_at', 'desc')->limit(2000)->get()->map(function ($h) {
                return [
                    'fec'   => $h->created_at ? $h->created_at->format('d/m/y H:i') : '—',
                    'cod'   => (string) (optional($h->reserva)->codigo_reserva ?? '—'),
                    'inm'   => (string) (optional(optional($h->reserva)->inmueble)->name ?? 'N/D'),
                    'ant'   => (string) (optional($h->estadoAnterior)->name ?? '—'),
                    'nvo'   => (string) (optional($h->estadoNuevo)->name ?? '—'),
                    'nvoC'  => (string) (optional($h->estadoNuevo)->color_hex ?: '#a3cfb6'),
                    'usr'   => (string) (optional($h->user)->name ?? 'Sistema'),
                    'com'   => (string) ($h->comentario ?? ''),
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('he dataset: '.$e->getMessage());
    }

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $heJson = json_encode($heItems->all(), $jhFlags); if (!is_string($heJson)) { $heJson = '[]'; }
@endphp

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-clock-history me-1" style="font-size: 8px; color: #5d8a70;"></i>
            Historial de Estados
            <span class="badge-lock ms-1" title="Registro inmutable (append-only)"><i class="bi bi-lock-fill"></i> Append-Only</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><span id="heBadge"><b>{{ $heTotal }}</b> movimientos</span></span>
            <button type="button" id="heExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Excel de TODO lo filtrado">
                <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
            </button>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
        <div class="d-flex align-items-center" style="gap: 6px;">
            <i class="bi bi-search" style="font-size: 8.5px; color: #c3ccd6;"></i>
            <input type="text" id="heSearch" class="xls-search" placeholder="Código de reserva, usuario o comentario… (instantáneo)" autocomplete="off">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 700px;">
            <thead>
                <tr><th>Fecha</th><th>Reserva</th><th>Inmueble</th><th>Transición</th><th>Usuario</th><th>Comentario</th></tr>
            </thead>
            <tbody id="heTbody">
                <tr><td colspan="6" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr>
            </tbody>
        </table>
    </div>

    <div class="rv-pagination d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
        <span class="count-badge" id="hePgInfo" style="background: #fcfdfe;"><i class="bi bi-layers" style="font-size: 7.5px; color: #b3bcc7;"></i> —</span>
        <div class="d-flex align-items-center" style="gap: 5px;">
            <button type="button" id="hePrev" class="btn-mini" disabled>‹ Anterior</button>
            <span id="hePgLbl" style="font-size: 8.5px; color: #b3bcc7; font-weight: 600;">Página 1 de 1</span>
            <button type="button" id="heNext" class="btn-mini" disabled>Siguiente ›</button>
        </div>
    </div>

</div>

<script type="application/json" id="heDataset">{!! $heJson !!}</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var DS = [];
        try { DS = JSON.parse((document.getElementById('heDataset') || {}).textContent || '[]'); } catch (e) { DS = []; }

        var PAGE_SIZE = 15, page = 1, q = '';
        var tbody = document.getElementById('heTbody');

        function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        function render() {
            var t = q.toLowerCase().trim();
            var res = DS.filter(function (r) {
                if (!t) return true;
                var blob = (r.cod + ' ' + r.usr + ' ' + r.com + ' ' + r.ant + ' ' + r.nvo + ' ' + r.inm).toLowerCase();
                return blob.indexOf(t) !== -1;
            });

            var pages = Math.max(1, Math.ceil(res.length / PAGE_SIZE));
            if (page > pages) page = pages;
            var ini = (page - 1) * PAGE_SIZE;
            var slice = res.slice(ini, ini + PAGE_SIZE);

            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="6" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
                  + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados con el filtro.' : '<i class="bi bi-clock-history me-1"></i> Sin movimientos de estado registrados aún.')
                  + '</td></tr>';
            } else {
                slice.forEach(function (r, i) {
                    h += '<tr>'
                       + '<td class="sub">' + esc(r.fec) + '</td>'
                       + '<td class="strong">#' + esc(r.cod) + '</td>'
                       + '<td>' + esc(r.inm) + '</td>'
                       + '<td><span class="sub">' + esc(r.ant) + '</span>'
                       + '<span class="fl-arrow">→</span>'
                       + '<span class="strong" style="color:' + esc(r.nvoC) + ';">' + esc(r.nvo) + '</span></td>'
                       + '<td>' + esc(r.usr) + '</td>'
                       + '<td class="sub" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;" title="' + esc(r.com) + '">' + esc(r.com || '—') + '</td>'
                       + '</tr>';
                });
            }
            tbody.innerHTML = h;

            var badge = document.getElementById('heBadge');
            if (badge) {
                badge.innerHTML = t
                    ? 'Mostrando <b>' + res.length + '</b> de ' + DS.length
                    : '<b>' + DS.length + '</b> movimientos';
            }
            var info = document.getElementById('hePgInfo');
            var lbl = document.getElementById('hePgLbl');
            var bp = document.getElementById('hePrev');
            var bn = document.getElementById('heNext');
            if (info) info.innerHTML = '<i class="bi bi-layers" style="font-size:7.5px;color:#b3bcc7;"></i> ' + (res.length ? (ini+1)+'–'+Math.min(ini+PAGE_SIZE,res.length)+' de <b>'+res.length+'</b>' : '—');
            if (lbl) lbl.textContent = 'Página ' + page + ' de ' + pages;
            if (bp) bp.disabled = page <= 1;
            if (bn) bn.disabled = page >= pages;
        }

        var search = document.getElementById('heSearch');
        if (search) search.addEventListener('input', function () { q = this.value; page = 1; render(); });

        var bp = document.getElementById('hePrev'), bn = document.getElementById('heNext');
        if (bp) bp.addEventListener('click', function () { if (page > 1) { page--; render(); } });
        if (bn) bn.addEventListener('click', function () { page++; render(); });

        document.getElementById('heExport').addEventListener('click', function (e) {
            e.stopPropagation();
            var t = q.toLowerCase().trim();
            var res = DS.filter(function (r) {
                if (!t) return true;
                return (r.cod + ' ' + r.usr + ' ' + r.com + ' ' + r.ant + ' ' + r.nvo + ' ' + r.inm).toLowerCase().indexOf(t) !== -1;
            });
            if (!res.length) { alert('Sin movimientos para exportar.'); return; }
            var rows = '';
            res.forEach(function (r, i) {
                rows += '<tr><td>' + (i+1) + '</td><td>' + esc(r.fec) + '</td><td>#' + esc(r.cod) + '</td><td>' + esc(r.inm) + '</td><td>' + esc(r.ant) + ' → ' + esc(r.nvo) + '</td><td>' + esc(r.usr) + '</td><td>' + esc(r.com) + '</td></tr>';
            });
            var html = '<html><head><meta charset="UTF-8"><style>table{border-collapse:collapse;font-family:Calibri;font-size:11px}td,th{border:1px solid #d0d7de;padding:4px 8px}th{background:#f0f7f2;color:#2e5c43}</style></head><body><table>'
                + '<tr><td colspan="7" style="font-weight:bold;font-size:14px;">HISTORIAL DE ESTADOS</td></tr>'
                + '<tr><td colspan="7" style="color:#666;font-size:10px;">' + new Date().toLocaleString() + ' · ' + res.length + ' movimiento(s)</td></tr>'
                + '<tr><th>#</th><th>Fecha</th><th>Reserva</th><th>Inmueble</th><th>Transición</th><th>Usuario</th><th>Comentario</th></tr>'
                + rows + '</table></body></html>';
            try {
                var blob = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url; a.download = 'HistorialEstados_' + Date.now() + '.xls'; a.style.display = 'none';
                a.click();
                setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            } catch (err) {}
        });

        render();
    });
</script>
