{{-- partials/reservas/status.blade.php · CRUD Estados (catálogo global) --}}
{{-- Modelo: Status · name · color_hex · reservas_count. Sin filtrado por inmueble. --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="stRoot" style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
</style>

@php
    $stItems = collect(); $stJson = '[]';
    $stRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };
    $stStore   = $stRuta(['rsv.statuses.store', 'rsv.status.store']);
    $stUpdate  = $stRuta(['rsv.statuses.update', 'rsv.status.update'], 'ID');
    $stDestroy = $stRuta(['rsv.statuses.destroy', 'rsv.status.destroy'], 'ID');
    $stEditable = !!($stStore || $stUpdate);

    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $stItems = \App\Models\Rsv\Status::withCount('reservas')->orderBy('name')->get()->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => (string) $s->name,
                    'color' => (string) ($s->color_hex ?: '#a3cfb6'),
                    'cnt' => (int) ($s->reservas_count ?? 0),
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('st dataset: '.$e->getMessage());
    }

    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $stJson = json_encode($stItems->all(), $jhFlags); if (!is_string($stJson)) { $stJson = '[]'; }
@endphp

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-flag me-1" style="font-size: 8px; color: #5d8a70;"></i> Estados de Reserva
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b>{{ $stItems->count() }}</b> estados</span>
            <input type="text" id="stSearch" class="xls-search" placeholder="Buscar estado…" autocomplete="off" style="width: 170px;">
            @if($stStore)
                <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#stModalForm">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Estado
                </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr><th>Estado</th><th>Color</th><th class="text-center">Reservas</th><th class="text-center" style="width: 90px;">·</th></tr>
            </thead>
            <tbody id="stTbody"><tr><td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr></tbody>
        </table>
    </div>

</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="stModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" id="stModalTitle" style="font-size: 0.95rem;">Nuevo Estado</h6>
            </div>
            <form id="stForm">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="stId" value="">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Nombre <span style="color:#c98a85;">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="stName" name="name" maxlength="100" required placeholder="Confirmada, Cancelada…">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Color (dot en tablas y calendario)</label>
                        <input type="color" class="form-control form-control-color form-control-sm w-100" id="stColor" name="color_hex" value="#a3cfb6">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="stModalDel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar estado</h6>
                <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                    Se eliminará <b id="stDelName" style="color: #334155;">—</b>
                    <span id="stDelWarn" class="d-none" style="color:#c98a85;"> (tiene <b id="stDelCnt">0</b> reserva(s) asociada(s)).</span>
                </p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="stDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="stDataset">{!! $stJson !!}</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var CSRF = '{{ csrf_token() }}';
        var STORE = '{{ $stStore ?? "" }}';
        var UPDATE_URL = '{{ $stUpdate ?? "" }}';
        var DESTROY_URL = '{{ $stDestroy ?? "" }}';
        var EDITABLE = !!(STORE || UPDATE_URL);

        var DS = [];
        try { DS = JSON.parse((document.getElementById('stDataset') || {}).textContent || '[]'); } catch (e) { DS = []; }
        var q = '';
        var tbody = document.getElementById('stTbody');

        function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        function render() {
            var t = q.toLowerCase().trim();
            var res = DS.filter(function (r) { return !t || r.name.toLowerCase().indexOf(t) !== -1; });
            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="4" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
                  + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados.' : '<i class="bi bi-flag me-1"></i> Sin estados definidos aún.')
                  + '</td></tr>';
            } else {
                res.forEach(function (r) {
                    h += '<tr>'
                       + '<td class="strong"><span class="st-dot" style="background:' + esc(r.color) + ';"></span> ' + esc(r.name) + '</td>'
                       + '<td class="text-muted" style="font-size:7.5px;">' + esc(r.color) + '</td>'
                       + '<td class="text-center">' + r.cnt + '</td>'
                       + '<td class="text-center">'
                       + (EDITABLE ? '<button type="button" class="btn-mini st-edit" data-id="' + r.id + '">Editar</button> ' : '')
                       + (DESTROY_URL ? '<button type="button" class="btn-mini st-del" data-id="' + r.id + '" style="color:#c98a85;">Del</button>' : '')
                       + '</td></tr>';
                });
            }
            tbody.innerHTML = h;
        }

        var search = document.getElementById('stSearch');
        if (search) search.addEventListener('input', function () { q = this.value; render(); });

        function enviar(url, method, fd) {
            fd.append('_token', CSRF);
            if (method !== 'POST') fd.append('_method', method);
            fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (r) { return r.json().catch(function () { return { success: r.ok }; }); })
                .then(function (j) {
                    if (j.success === false) { alert(j.message || 'No se pudo completar.'); return; }
                    location.reload();
                })
                .catch(function () { alert('Error de conexión.'); });
        }

        var mForm, mDel;
        try {
            mForm = bootstrap.Modal.getOrCreateInstance(document.getElementById('stModalForm'));
            mDel = bootstrap.Modal.getOrCreateInstance(document.getElementById('stModalDel'));
        } catch (e) {}

        document.addEventListener('click', function (e) {
            var bE = e.target.closest('.st-edit');
            var bD = e.target.closest('.st-del');
            if (bE && EDITABLE) {
                var r = DS.find(function (x) { return String(x.id) === bE.getAttribute('data-id'); });
                if (!r) return;
                document.getElementById('stModalTitle').textContent = 'Editar Estado';
                document.getElementById('stId').value = r.id;
                document.getElementById('stName').value = r.name;
                document.getElementById('stColor').value = r.color;
                if (mForm) mForm.show();
            }
            if (bD && DESTROY_URL) {
                var r2 = DS.find(function (x) { return String(x.id) === bD.getAttribute('data-id'); });
                if (!r2) return;
                document.getElementById('stDelName').textContent = r2.name;
                var warn = document.getElementById('stDelWarn');
                if (r2.cnt > 0) { document.getElementById('stDelCnt').textContent = r2.cnt; warn.classList.remove('d-none'); }
                else { warn.classList.add('d-none'); }
                document.getElementById('stDelGo').onclick = function () {
                    enviar(DESTROY_URL.replace('ID', r2.id), 'DELETE', new FormData());
                    if (mDel) mDel.hide();
                };
                if (mDel) mDel.show();
            }
        });

        document.getElementById('stForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(this);
            var id = document.getElementById('stId').value;
            if (id && UPDATE_URL) { enviar(UPDATE_URL.replace('ID', id), 'PUT', fd); }
            else if (STORE) { enviar(STORE, 'POST', fd); }
            if (mForm) mForm.hide();
        });

        render();
    });
</script>
