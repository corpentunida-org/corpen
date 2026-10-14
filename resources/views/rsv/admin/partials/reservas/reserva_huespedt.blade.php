{{-- partials/reservas/reserva_huespedt.blade.php · CRUD Huéspedes --}}
{{-- Fuente de verdad: modelo ReservaHuesped (nombre · apellidos · tipo_documento --}}
{{-- · numero_documento · es_titular). Búsqueda instantánea · export · CRUD fetch. --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="rhRoot"
     data-modo="{{ ($rhGlobal ?? true) ? 'global' : 'single' }}"
     data-ctx="{{ ($rhGlobal ?? true) ? 'Global' : ((is_object($rhInm ?? null) ? ($rhInm->name ?? 'Inmueble') : ('Inmueble #'.($rhInmId ?? '')))) }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; white-space: nowrap; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
    .reservas-soft .rv-num { color: var(--ink-4); text-align: center; width: 22px; }
    .reservas-soft .rv-ellip { max-width: 190px; overflow: hidden; text-overflow: ellipsis; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .fchip { padding: 1px 8px; border-radius: 10px; font-size: 8.5px; font-weight: 500; white-space: nowrap; cursor: pointer; user-select: none; border: 1px solid var(--grid); color: var(--ink-4); background: #fcfdfe; }
    .reservas-soft .fchip:hover { color: var(--ink-2); border-color: var(--grid-2); }
    .reservas-soft .fchip.active { color: var(--accent); background: var(--accent-bg); border-color: var(--accent-bd); font-weight: 600; }
    .reservas-soft .fchip .cnt { font-weight: 700; font-size: 7.5px; opacity: .75; margin-left: 2px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; flex: 1 1 240px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .btn-green:hover { background: #e3f1e9; color: #34684e; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .chip-tit { display: inline-flex; align-items: center; gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; border: 1px solid; }
    .reservas-soft .chip-tit.si { color: #3d7a5c; background: #e3f1e9; border-color: #d5e7dc; }
    .reservas-soft .chip-tit.no { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }
    .reservas-soft .rv-pagination { border-top: 1px solid var(--grid); margin-top: 8px; padding-top: 7px; }
</style>

@php
    // 0 · Defaults
    $rhInm = null; $rhInmId = null; $rhGlobal = true;
    $rhItems = collect(); $rhReservasOpt = collect();
    $rhJson = '[]'; $rhReservasJson = '[]'; $rhTotalCtx = 0;
    $rhStoreUrl = null; $rhUpdateUrlBase = null; $rhDestroyUrlBase = null;

    // 1 · Contexto
    if (isset($inmueble) && $inmueble) {
        $rhInm = $inmueble;
        $rhInmId = is_object($rhInm) ? ($rhInm->id ?? null) : $rhInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $rhInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $rhInmId = request('inmueble_id');
    }
    $rhGlobal = empty($rhInmId);

    // 2 · Rutas (detección defensiva por varios nombres)
    $rhRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };
    $rhStoreUrl        = $rhRuta(['rsv.reserva-huespedes.store', 'rsv.reservahuespedes.store', 'rsv.huespedes.store']);
    $rhUpdateUrlBase   = $rhRuta(['rsv.reserva-huespedes.update', 'rsv.reservahuespedes.update', 'rsv.huespedes.update'], 'ID');
    $rhDestroyUrlBase  = $rhRuta(['rsv.reserva-huespedes.destroy', 'rsv.reservahuespedes.destroy', 'rsv.huespedes.destroy'], 'ID');

    // 3 · Dataset de huéspedes (contexto vía relación reserva)
    try {
        if (class_exists(\App\Models\Rsv\ReservaHuesped::class)) {
            $q = \App\Models\Rsv\ReservaHuesped::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'registrador:id,name']);

            if (!$rhGlobal && $rhInmId) {
                $q->whereHas('reserva', function ($qq) use ($rhInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $rhInmId);
                });
            }

            $rhTotalCtx = (clone $q)->count();

            $rhItems = $q->orderBy('created_at', 'desc')->limit(2000)->get()->map(function ($h) {
                return [
                    'id'   => $h->id,
                    'nom'  => (string) ($h->nombre ?? ''),
                    'ape'  => (string) ($h->apellidos ?? ''),
                    'tdoc' => (string) ($h->tipo_documento ?? ''),
                    'ndoc' => (string) ($h->numero_documento ?? ''),
                    'tit'  => $h->es_titular ? 1 : 0,
                    'cod'  => (string) (optional($h->reserva)->codigo_reserva ?? '—'),
                    'inm'  => (string) (optional(optional($h->reserva)->inmueble)->name ?? 'N/D'),
                    'reg'  => (string) (optional($h->registrador)->name ?? '—'),
                    'fec'  => $h->created_at ? $h->created_at->format('d/m/y') : '—',
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('rh dataset: '.$e->getMessage());
    }

    // 3b · Reservas para el select del modal (contexto)
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $rq = \App\Models\Rsv\Reserva::query()->select('id', 'codigo_reserva');
            if (!$rhGlobal && $rhInmId) { $rq->where('id_rsv_catalogo_inmueble', $rhInmId); }
            $rhReservasOpt = $rq->orderBy('created_at', 'desc')->limit(500)->get()
                ->map(fn ($r) => ['id' => $r->id, 'cod' => $r->codigo_reserva])->values();
        }
    } catch (\Throwable $e) { $rhReservasOpt = collect(); }

    // 4 · Serialización raw segura
    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $rhJson = json_encode($rhItems->all(), $jhFlags); if (!is_string($rhJson)) { $rhJson = '[]'; }
    $rhReservasJson = json_encode($rhReservasOpt->all(), $jhFlags); if (!is_string($rhReservasJson)) { $rhReservasJson = '[]'; }
@endphp

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-people me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $rhGlobal ? 'Huéspedes del Sistema' : 'Huéspedes del Inmueble' }}
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge">
                <i class="bi bi-list-ol" style="font-size: 7.5px; color: #b3bcc7;"></i>
                <span id="rhBadge"><b>{{ $rhTotalCtx }}</b> huéspedes</span>
            </span>
            <button type="button" id="rhExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Excel de TODO lo filtrado">
                <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
            </button>
            @if($rhStoreUrl)
                <button type="button" class="btn-green d-inline-flex align-items-center gap-1"
                        data-bs-toggle="modal" data-bs-target="#rhModalForm">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Huésped
                </button>
            @endif
        </div>
    </div>

    <!-- BÚSQUEDA + CHIPS -->
    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
        <div class="d-flex align-items-center" style="gap: 6px; flex: 1 1 240px; min-width: 220px;">
            <i class="bi bi-search" style="font-size: 8.5px; color: #c3ccd6;"></i>
            <input type="text" id="rhSearch" class="xls-search" placeholder="Nombre, apellido, documento o reserva… (instantáneo)" autocomplete="off">
        </div>
        <span class="fchip active" data-tit="" role="button">Todos <span class="cnt" id="rhCntTodas">({{ $rhItems->count() }})</span></span>
        <span class="fchip" data-tit="1" role="button">Titulares <span class="cnt" data-cnt="1">({{ $rhItems->where('tit', 1)->count() }})</span></span>
        <span class="fchip" data-tit="0" role="button">Acompañantes <span class="cnt" data-cnt="0">({{ $rhItems->where('tit', 0)->count() }})</span></span>
    </div>

    <!-- TABLA -->
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 720px;">
            <thead>
                <tr>
                    <th class="rv-num">#</th>
                    <th>Huésped</th>
                    <th>Documento</th>
                    <th>Reserva</th>
                    <th>Inmueble</th>
                    <th class="text-center">Titular</th>
                    <th>Registró</th>
                    <th class="text-center" style="width: 70px;">·</th>
                </tr>
            </thead>
            <tbody id="rhTbody">
                <tr><td colspan="8" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                    <i class="bi bi-hourglass-split me-1"></i> Cargando…
                </td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="rv-pagination d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
        <span class="count-badge" id="rhPgInfo" style="background: #fcfdfe;"><i class="bi bi-layers" style="font-size: 7.5px; color: #b3bcc7;"></i> —</span>
        <div class="d-flex align-items-center" style="gap: 5px;">
            <button type="button" id="rhPrev" class="btn-mini" disabled>‹ Anterior</button>
            <span id="rhPgLbl" style="font-size: 8.5px; color: #b3bcc7; font-weight: 600;">Página 1 de 1</span>
            <button type="button" id="rhNext" class="btn-mini" disabled>Siguiente ›</button>
        </div>
    </div>

</div>

{{-- MODAL FORM (crear/editar) --}}
<div class="modal fade" id="rhModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-family: Calibri, 'Segoe UI', sans-serif; font-size: 9.5px;">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">Ecosistema de Reservas</div>
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;" id="rhModalTitle">Nuevo Huésped</h6>
                </div>
            </div>
            <form id="rhForm">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="rhId" name="id" value="">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Reserva <span style="color:#c98a85;">*</span></label>
                        <select class="form-select form-select-sm" id="rhReserva" name="id_rsv_reservas" required>
                            <option value="">-- Seleccione la reserva --</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Nombre <span style="color:#c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="rhNombre" name="nombre" maxlength="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Apellidos <span style="color:#c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="rhApellidos" name="apellidos" maxlength="100" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Tipo Doc.</label>
                            <input type="text" class="form-control form-control-sm" id="rhTdoc" name="tipo_documento" placeholder="CC, CE, PAS…" maxlength="20">
                        </div>
                        <div class="col-8">
                            <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Número</label>
                            <input type="text" class="form-control form-control-sm" id="rhNdoc" name="numero_documento" maxlength="30">
                        </div>
                    </div>
                    <div class="tz-switch-box d-flex justify-content-between align-items-center p-2 rounded" style="background: #fafbfd; border: 1px solid #f1f5f9;">
                        <div>
                            <div class="fw-semibold" style="font-size: 8.5px; color: #475569;">Es titular</div>
                            <div style="font-size: 7px; color: #b3bcc7;">Responsable principal de la reserva</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="es_titular" value="1" id="rhTit">
                        </div>
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
<div class="modal fade" id="rhModalDel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-trash3" style="font-size: 1.2rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar huésped</h6>
                <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                    Se eliminará <b id="rhDelNombre" style="color: #334155;">—</b> de la reserva <b id="rhDelCod" style="color: #334155;">—</b>.
                </p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="rhDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="rhDataset">{!! $rhJson !!}</script>
<script type="application/json" id="rhReservasJson">{!! $rhReservasJson !!}</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var CSRF = '{{ csrf_token() }}';
        var STORE = '{{ $rhStoreUrl ?? "" }}';
        var UPDATE_URL = '{{ $rhUpdateUrlBase ?? "" }}';
        var DESTROY_URL = '{{ $rhDestroyUrlBase ?? "" }}';
        var EDITABLE = !!(STORE || UPDATE_URL);

        var DS = [];
        try { DS = JSON.parse((document.getElementById('rhDataset') || {}).textContent || '[]'); } catch (e) { DS = []; }
        var RESERVAS = [];
        try { RESERVAS = JSON.parse((document.getElementById('rhReservasJson') || {}).textContent || '[]'); } catch (e) { RESERVAS = []; }

        var PAGE_SIZE = 15, page = 1, q = '', titF = '';
        var tbody = document.getElementById('rhTbody');
        var badge = document.getElementById('rhBadge');

        function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
        function blob(r) { return (r.nom+' '+r.ape+' '+r.tdoc+' '+r.ndoc+' '+r.cod+' '+r.inm+' '+r.reg).toLowerCase(); }

        function filtrar() {
            var t = q.toLowerCase().trim();
            return DS.filter(function (r) {
                if (t && blob(r).indexOf(t) === -1) return false;
                if (titF !== '' && String(r.tit) !== titF) return false;
                return true;
            });
        }

        function render() {
            var res = filtrar();
            var pages = Math.max(1, Math.ceil(res.length / PAGE_SIZE));
            if (page > pages) page = pages;
            var ini = (page - 1) * PAGE_SIZE;
            var slice = res.slice(ini, ini + PAGE_SIZE);

            var h = '';
            if (!res.length) {
                h = '<tr><td colspan="8" class="text-center py-3" style="font-size:8.5px;color:#b3bcc7;">'
                  + (DS.length ? '<i class="bi bi-funnel me-1"></i> Sin resultados con los filtros.' : '<i class="bi bi-people me-1"></i> Sin huéspedes registrados aún.')
                  + '</td></tr>';
            } else {
                slice.forEach(function (r, i) {
                    h += '<tr>'
                       + '<td class="rv-num">' + (ini + i + 1) + '</td>'
                       + '<td class="rv-ellip"><span class="strong">' + esc(r.nom) + ' ' + esc(r.ape) + '</span></td>'
                       + '<td>' + esc(r.tdoc || '—') + (r.ndoc ? ' · ' + esc(r.ndoc) : '') + '</td>'
                       + '<td class="strong">#' + esc(r.cod) + '</td>'
                       + '<td class="rv-ellip">' + esc(r.inm) + '</td>'
                       + '<td class="text-center"><span class="chip-tit ' + (r.tit ? 'si' : 'no') + '">' + (r.tit ? 'SÍ' : 'NO') + '</span></td>'
                       + '<td class="sub">' + esc(r.reg) + ' · ' + esc(r.fec) + '</td>'
                       + '<td class="text-center">'
                       + (EDITABLE ? '<button type="button" class="btn-mini rh-edit" data-id="' + r.id + '">Editar</button> ' : '')
                       + (DESTROY_URL ? '<button type="button" class="btn-mini rh-del" data-id="' + r.id + '" style="color:#c98a85;">Del</button>' : '')
                       + '</td></tr>';
                });
            }
            if (tbody) tbody.innerHTML = h;

            if (badge) {
                badge.innerHTML = (q || titF !== '')
                    ? 'Mostrando <b>' + res.length + '</b> de ' + DS.length
                    : '<b>' + DS.length + '</b> ' + (DS.length == 1 ? 'huésped' : 'huéspedes');
            }
            var info = document.getElementById('rhPgInfo');
            var lbl = document.getElementById('rhPgLbl');
            var bp = document.getElementById('rhPrev');
            var bn = document.getElementById('rhNext');
            if (info) info.innerHTML = '<i class="bi bi-layers" style="font-size:7.5px;color:#b3bcc7;"></i> ' + (res.length ? (ini+1)+'–'+Math.min(ini+PAGE_SIZE,res.length)+' de <b>'+res.length+'</b>' : '—');
            if (lbl) lbl.textContent = 'Página ' + page + ' de ' + pages;
            if (bp) bp.disabled = page <= 1;
            if (bn) bn.disabled = page >= pages;
        }

        /* Búsqueda instantánea */
        var search = document.getElementById('rhSearch');
        if (search) search.addEventListener('input', function () { q = this.value; page = 1; render(); });

        /* Chips */
        document.querySelectorAll('#rhRoot .fchip[data-tit]').forEach(function (c) {
            c.addEventListener('click', function () {
                document.querySelectorAll('#rhRoot .fchip[data-tit]').forEach(function (x) { x.classList.remove('active'); });
                c.classList.add('active');
                titF = c.getAttribute('data-tit') || '';
                page = 1; render();
            });
        });

        /* Paginación */
        var bp = document.getElementById('rhPrev'), bn = document.getElementById('rhNext');
        if (bp) bp.addEventListener('click', function () { if (page > 1) { page--; render(); } });
        if (bn) bn.addEventListener('click', function () { page++; render(); });

        /* Llenar select de reservas */
        var sel = document.getElementById('rhReserva');
        if (sel) {
            sel.innerHTML = '<option value="">-- Seleccione la reserva --</option>';
            RESERVAS.forEach(function (r) {
                var o = document.createElement('option');
                o.value = r.id; o.textContent = '#' + r.cod;
                sel.appendChild(o);
            });
        }

        /* CRUD via fetch (los controladores devuelven JSON) */
        function enviar(url, method, fd, ok) {
            fd.append('_token', CSRF);
            if (method !== 'POST') fd.append('_method', method);
            fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function (r) { return r.json().catch(function () { return { success: r.ok }; }); })
                .then(function (j) {
                    if (j.success === false) { alert(j.message || 'No se pudo completar la operación.'); return; }
                    location.reload();
                })
                .catch(function () { alert('Error de conexión.'); });
        }

        var modalForm = document.getElementById('rhModalForm');
        var modalDel  = document.getElementById('rhModalDel');
        var mForm, mDel;
        try { mForm = bootstrap.Modal.getOrCreateInstance(modalForm); mDel = bootstrap.Modal.getOrCreateInstance(modalDel); } catch (e) {}

        document.addEventListener('click', function (e) {
            var btnE = e.target.closest('.rh-edit');
            var btnD = e.target.closest('.rh-del');
            if (btnE && EDITABLE) {
                var r = DS.find(function (x) { return String(x.id) === btnE.getAttribute('data-id'); });
                if (!r) return;
                document.getElementById('rhModalTitle').textContent = 'Editar Huésped';
                document.getElementById('rhId').value = r.id;
                document.getElementById('rhReserva').value = '';
                var opt = Array.from(sel.options).find(function (o) { return o.textContent === '#' + r.cod; });
                if (opt) sel.value = opt.value;
                document.getElementById('rhNombre').value = r.nom;
                document.getElementById('rhApellidos').value = r.ape;
                document.getElementById('rhTdoc').value = r.tdoc;
                document.getElementById('rhNdoc').value = r.ndoc;
                document.getElementById('rhTit').checked = !!r.tit;
                if (mForm) mForm.show();
            }
            if (btnD && DESTROY_URL) {
                var r2 = DS.find(function (x) { return String(x.id) === btnD.getAttribute('data-id'); });
                if (!r2) return;
                document.getElementById('rhDelNombre').textContent = r2.nom + ' ' + r2.ape;
                document.getElementById('rhDelCod').textContent = r2.cod;
                var go = document.getElementById('rhDelGo');
                go.onclick = function () {
                    var url = DESTROY_URL.replace('ID', r2.id);
                    enviar(url, 'DELETE', new FormData(), true);
                    if (mDel) mDel.hide();
                };
                if (mDel) mDel.show();
            }
        });

        document.getElementById('rhForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(this);
            var id = document.getElementById('rhId').value;
            var url, method;
            if (id && UPDATE_URL) { url = UPDATE_URL.replace('ID', id); method = 'PUT'; }
            else if (STORE) { url = STORE; method = 'POST'; }
            else { alert('Ruta de guardado no configurada.'); return; }
            enviar(url, method, fd, true);
            if (mForm) mForm.hide();
        });

        /* EXPORTAR (sin spinner eterno · anchor fuera del DOM) */
        function limpiarOverlays() {
            try {
                if (!document.querySelector('.modal.show')) {
                    document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            } catch (e) {}
        }
        document.getElementById('rhExport').addEventListener('click', function (e) {
            e.stopPropagation();
            var res = filtrar();
            if (!res.length) { alert('Sin huéspedes para exportar con los filtros actuales.'); return; }
            var rows = '';
            res.forEach(function (r, i) {
                rows += '<tr><td>' + (i+1) + '</td><td>' + esc(r.nom) + '</td><td>' + esc(r.ape) + '</td><td>' + esc(r.tdoc) + '</td><td>' + esc(r.ndoc) + '</td><td>#' + esc(r.cod) + '</td><td>' + esc(r.inm) + '</td><td>' + (r.tit ? 'SÍ' : 'NO') + '</td><td>' + esc(r.reg) + '</td><td>' + esc(r.fec) + '</td></tr>';
            });
            var html = '<html><head><meta charset="UTF-8"><style>table{border-collapse:collapse;font-family:Calibri;font-size:11px}td,th{border:1px solid #d0d7de;padding:4px 8px}th{background:#f0f7f2;color:#2e5c43}</style></head><body><table>'
                + '<tr><td colspan="10" style="font-weight:bold;font-size:14px;">REPORTE DE HUÉSPEDES</td></tr>'
                + '<tr><td colspan="10" style="color:#666;font-size:10px;">' + new Date().toLocaleString() + ' · ' + res.length + ' registro(s)</td></tr>'
                + '<tr><th>#</th><th>Nombre</th><th>Apellidos</th><th>Tipo Doc.</th><th>Número</th><th>Reserva</th><th>Inmueble</th><th>Titular</th><th>Registró</th><th>Fecha</th></tr>'
                + rows + '</table></body></html>';
            try {
                var blob = new Blob(['\uFEFF', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url; a.download = 'Huespedes_' + Date.now() + '.xls'; a.style.display = 'none';
                a.click();
                setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
            } catch (err) {}
            setTimeout(limpiarOverlays, 300);
            setTimeout(limpiarOverlays, 1200);
        });

        render();
    });
</script>
