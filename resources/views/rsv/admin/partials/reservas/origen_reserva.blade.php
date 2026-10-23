{{-- partials/reservas/origen_reserva.blade.php · CRUD Origen de Reserva --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="orRoot" style="border-color: #f1f5f9 !important;">

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
</style>

    @php
        $orItems = collect(); $orJson = '[]';
        $orRuta = function (array $cands, $param = null) {
            foreach ($cands as $c) {
                if (\Illuminate\Support\Facades\Route::has($c)) return $param !== null ? route($c, $param) : route($c);
            }
            return null;
        };
        $orStore   = $orRuta(['rsv.origen-reservas.store', 'rsv.origenes.store']);
        $orUpdate  = $orRuta(['rsv.origen-reservas.update', 'rsv.origenes.update'], 'ID');
        $orDestroy = $orRuta(['rsv.origen-reservas.destroy', 'rsv.origenes.destroy'], 'ID');
        $orEditable = !!($orStore || $orUpdate);

        try {
            if (class_exists(\App\Models\Rsv\OrigenReserva::class)) {
                $orItems = \App\Models\Rsv\OrigenReserva::orderBy('nombre')->get()->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'name' => (string) ($o->nombre ?? $o->name ?? ''), // Compatible con ambos idiomas
                        'active' => (bool) ($o->active ?? $o->activo ?? true)
                    ];
                })->values();
            }
        } catch (\Throwable $e) { }

        $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
        $orJson = json_encode($orItems->all(), $jhFlags); if (!is_string($orJson)) { $orJson = '[]'; }
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-box-arrow-in-right me-1" style="font-size: 8px; color: #5d8a70;"></i> Orígenes de Reserva
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b>{{ $orItems->count() }}</b> orígenes</span>
            <input type="text" id="orSearch" class="xls-search" placeholder="Buscar origen…" autocomplete="off" style="width: 170px;">
            @if($orStore)
                <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#orModalForm">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Origen
                </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr><th>Origen</th><th class="text-center">Estado</th><th class="text-center" style="width: 90px;">·</th></tr>
            </thead>
            <tbody id="orTbody"><tr><td colspan="3" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Cargando…</td></tr></tbody>
        </table>
    </div>

</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="orModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" id="orModalTitle" style="font-size: 0.95rem;">Nuevo Origen</h6>
            </div>
            <form id="orForm">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="orId" value="">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Nombre <span style="color:#c98a85;">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="orName" name="name" maxlength="255" required placeholder="Booking, Airbnb...">
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="orActive" checked>
                        <label class="form-check-label" for="orActive" style="font-size: 8.5px; color: #475569;">Activo</label>
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
<div class="modal fade" id="orModalDel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar origen</h6>
                <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                    Se eliminará <b id="orDelName" style="color: #334155;">—</b>
                </p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="orDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="orDataset">{!! $orJson !!}</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var CSRF = '{{ csrf_token() }}';
        var STORE = '{{ $orStore ?? "" }}';
        var UPDATE_URL = '{{ $orUpdate ?? "" }}';
        var DESTROY_URL = '{{ $orDestroy ?? "" }}';
        var EDITABLE = !!(STORE || UPDATE_URL);

        var DS = [];
        try { DS = JSON.parse((document.getElementById('orDataset') || {}).textContent || '[]'); } catch (e) { DS = []; }
        var q = '';
        var tbody = document.getElementById('orTbody');

        function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        function render() {
            var t = q.toLowerCase().trim();
            var res = DS.filter(function (r) { return !t || r.name.toLowerCase().indexOf(t) !== -1; });
            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="3" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
                  + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados.' : '<i class="bi bi-box-arrow-in-right me-1"></i> Sin orígenes definidos aún.')
                  + '</td></tr>';
            } else {
                res.forEach(function (r) {
                    h += '<tr>'
                       + '<td class="strong">' + esc(r.name) + '</td>'
                       + '<td class="text-center">' + (r.active ? '<span style="color:#3d7a5c">Activo</span>' : '<span style="color:#94a3b8">Inactivo</span>') + '</td>'
                       + '<td class="text-center">'
                       + (EDITABLE ? '<button type="button" class="btn-mini or-edit" data-id="' + r.id + '">Editar</button> ' : '')
                       + (DESTROY_URL ? '<button type="button" class="btn-mini or-del" data-id="' + r.id + '" style="color:#c98a85;">Del</button>' : '')
                       + '</td></tr>';
                });
            }
            tbody.innerHTML = h;
        }

        var search = document.getElementById('orSearch');
        if (search) search.addEventListener('input', function () { q = this.value; render(); });

        /* ACTUALIZACIÓN PARA FORMREQUESTS: Maneja correctamente los errores 422 en AJAX */
        function enviar(url, method, fd) {
            fd.append('_token', CSRF);
            if (method !== 'POST') fd.append('_method', method);
            fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (r) { return r.json().then(function(data){ data._status = r.status; return data; }).catch(function(){ return { success: r.ok, _status: r.status }; }); })
                .then(function (j) {
                    if (j.success === false || j._status === 422) {
                        var msg = j.message || 'No se pudo completar.';
                        if (j.errors) { msg = Object.values(j.errors).map(function(e){ return e.join('\n'); }).join('\n'); }
                        alert('Atención:\n' + msg); return;
                    }
                    location.reload();
                })
                .catch(function () { alert('Error de conexión.'); });
        }

        var mForm, mDel;
        try { mForm = bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalForm')); mDel = bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalDel')); } catch (e) {}

        document.addEventListener('click', function (e) {
            var bE = e.target.closest('.or-edit');
            var bD = e.target.closest('.or-del');
            if (bE && EDITABLE) {
                var r = DS.find(function (x) { return String(x.id) === bE.getAttribute('data-id'); });
                if (!r) return;
                document.getElementById('orModalTitle').textContent = 'Editar Origen';
                document.getElementById('orId').value = r.id;
                document.getElementById('orName').value = r.name;
                document.getElementById('orActive').checked = !!r.active;
                if (mForm) mForm.show();
            }
            if (bD && DESTROY_URL) {
                var r2 = DS.find(function (x) { return String(x.id) === bD.getAttribute('data-id'); });
                if (!r2) return;
                document.getElementById('orDelName').textContent = r2.name;
                document.getElementById('orDelGo').onclick = function () {
                    enviar(DESTROY_URL.replace('ID', r2.id), 'DELETE', new FormData());
                    if (mDel) mDel.hide();
                };
                if (mDel) mDel.show();
            }
        });

        document.getElementById('orForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(this);
            var id = document.getElementById('orId').value;
            if (id && UPDATE_URL) { enviar(UPDATE_URL.replace('ID', id), 'PUT', fd); }
            else if (STORE) { enviar(STORE, 'POST', fd); }
            if (mForm) mForm.hide();
        });

        render();
    });
</script>