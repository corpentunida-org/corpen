{{-- partials/reservas/status.blade.php · v5 · CRUD 100% Bootstrap/Blade + fila TOTAL --}}
{{-- · Alertas de sesión/validación visibles · old() en forms                          --}}
{{-- · Conteos contextuales (global vs inmueble) · guard de eliminar global           --}}
{{-- · tfoot: Sin estado asignado + TOTAL · búsqueda progresiva con ids únicos        --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="stRoot"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }

    /* ═══ TFOOT · fila TOTAL (intuitiva) ═══ */
    .reservas-soft .table tfoot > tr > td {
        font-size: 8.5px; font-weight: 700; color: var(--ink-2);
        background-color: var(--head);
        border-top: 2px solid var(--grid-2) !important;
        border-bottom: 0; white-space: nowrap;
    }
    .reservas-soft .table tfoot tr.st-row-sin td {
        font-weight: 600; color: var(--ink-3);
        border-top: 1px dashed var(--grid-2) !important;
        font-size: 8px;
    }
    .reservas-soft .table tfoot .st-total-label { color: var(--accent); letter-spacing: .03em; }

    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: not-allowed; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
    .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
    .reservas-soft .count-badge b { color: var(--accent); }
    .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
    .reservas-soft .ctx-chip { display: inline-flex; align-items: center; gap: 3px; font-size: 7.5px; font-weight: 600; color: var(--accent); background: var(--accent-bg); border: 1px solid var(--accent-bd); border-radius: 8px; padding: 0 7px; line-height: 14px; white-space: nowrap; }
    .reservas-soft .st-warn { background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px; color: #c98a85; font-size: 8px; font-weight: 600; padding: 6px 9px; }
    .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 5px; font-family: Calibri, "Segoe UI", sans-serif; font-size: 9.5px; color: #64748b; }
    .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px; }
    .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
    .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
</style>

@php
    /*
    ══════════════════════════════════════════════════════════════
    CONTEXTO GLOBAL/INDIVIDUAL
    · CRUD de catálogo global, pero los CONTADORES respetan el
      filtro superior (global = todo el sistema · individual = solo
      reservas del inmueble seleccionado).
    · El guard de ELIMINAR usa SIEMPRE el conteo global (destroy
      elimina el estado de todo el sistema).
    · TFOOT: "Sin estado asignado" + TOTAL → la columna siempre
      suma el total de reservas del contexto actual.
    ══════════════════════════════════════════════════════════════
    */
    $stInm = null; $stInmId = null; $stGlobal = true; $stCtxName = 'Global';

    if (isset($inmueble) && $inmueble) {
        $stInm = $inmueble;
        $stInmId = is_object($stInm) ? ($stInm->id ?? null) : $stInm;
    } elseif (!empty($inmuebleId) && is_numeric($inmuebleId)) {
        $stInmId = $inmuebleId;
    } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $stInmId = request('inmueble_id');
    }
    $stGlobal = empty($stInmId);
    if (!$stGlobal) {
        $stCtxName = (is_object($stInm) ? ($stInm->name ?? null) : null) ?? ('Inmueble #' . $stInmId);
    }

    // Detección defensiva de rutas
    $stRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };
    $stStore    = $stRuta(['rsv.statuses.store', 'rsv.status.store']);
    $stUpdate   = $stRuta(['rsv.statuses.update', 'rsv.status.update'], 'ID');
    $stDestroy  = $stRuta(['rsv.statuses.destroy', 'rsv.status.destroy'], 'ID');

    $stItems = collect(); $stTotalesGlobales = collect();
    try {
        if (class_exists(\App\Models\Rsv\Status::class)) {
            $q = \App\Models\Rsv\Status::query();

            if ($stGlobal) {
                $q->withCount('reservas');
            } else {
                $q->withCount(['reservas' => function ($qq) use ($stInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $stInmId);
                }]);
            }

            // Conteo GLOBAL por estado (para el guard de eliminar)
            if (class_exists(\App\Models\Rsv\Reserva::class)) {
                $stTotalesGlobales = \App\Models\Rsv\Reserva::query()
                    ->selectRaw('id_rsv_statuses, COUNT(*) as c')
                    ->groupBy('id_rsv_statuses')
                    ->pluck('c', 'id_rsv_statuses');
            }

            $stItems = $q->orderBy('name')->get()->map(function ($s) use ($stTotalesGlobales) {
                return [
                    'id'       => $s->id,
                    'name'     => (string) $s->name,
                    'color'    => (string) ($s->color_hex ?: '#a3cfb6'),
                    'cnt'      => (int) ($s->reservas_count ?? 0),
                    'cntTotal' => (int) ($stTotalesGlobales[$s->id] ?? 0),
                ];
            })->values();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('st dataset: '.$e->getMessage());
        $stItems = collect();
    }

    /* ═══ TOTALES PARA EL TFOOT ═══ */
    $stSumaCnt = (int) $stItems->sum('cnt');

    // Total de reservas del CONTEXTO (global o del inmueble)
    $stTotalReservas = 0;
    try {
        if (class_exists(\App\Models\Rsv\Reserva::class)) {
            $stQ = \App\Models\Rsv\Reserva::query();
            if (!$stGlobal && $stInmId) { $stQ->where('id_rsv_catalogo_inmueble', $stInmId); }
            $stTotalReservas = (clone $stQ)->count();
        }
    } catch (\Throwable $e) { $stTotalReservas = $stSumaCnt; }

    // Reservas sin ningún estado asignado (diferencia)
    $stSinEstado = max(0, $stTotalReservas - $stSumaCnt);
@endphp

    <!-- ═══ ALERTAS · success / error / validación (fix fallos silenciosos) ═══ -->
    @if (session('success'))
        <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
            <i class="bi bi-exclamation-circle-fill me-1"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-flag me-1" style="font-size: 8px; color: #5d8a70;"></i> Estados de Reserva
            <span class="badge rounded-pill ms-1" style="font-size: 6.5px; background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; vertical-align: middle;">S5</span>
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

            @if(!$stGlobal)
                <span class="ctx-chip" title="Conteos filtrados por inmueble">
                    <i class="bi bi-funnel-fill"></i> {{ $stCtxName }}
                </span>
            @endif

            <span class="count-badge" title="{{ $stGlobal ? 'Reservas en todo el sistema' : 'Reservas de este inmueble' }}">
                <b>{{ $stTotalReservas }}</b> reserva(s) · <b>{{ $stItems->count() }}</b> estados
            </span>

            <input type="text" id="stSearch" class="xls-search" placeholder="Buscar estado…" autocomplete="off" style="width: 170px;">

            @if($stStore)
                <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#stModalCreate">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Estado
                </button>
            @endif
        </div>
    </div>

    <!-- TABLA · 100% Blade · con tfoot TOTAL -->
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Color</th>
                    <th class="text-center">{{ $stGlobal ? 'Reservas' : 'Reservas (este inmueble)' }}</th>
                    <th class="text-center" style="width: 110px;">·</th>
                </tr>
            </thead>
            <tbody id="stTbody">
                @forelse(($stItems ?? collect()) as $s)
                    <tr data-st-name="{{ strtolower($s['name']) }}">
                        <td class="strong">
                            <span class="st-dot" style="background: {{ $s['color'] }};"></span> {{ $s['name'] }}
                        </td>
                        <td class="sub">{{ $s['color'] }}</td>
                        <td class="text-center">{{ $s['cnt'] }}</td>
                        <td class="text-center">
                            @if($stUpdate)
                                <button type="button"
                                        class="btn-mini"
                                        data-bs-toggle="modal"
                                        data-bs-target="#stEditModal-{{ $s['id'] }}">Editar</button>
                            @endif
                            @if($stDestroy)
                                <button type="button"
                                        class="btn-mini"
                                        style="color:#c98a85;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#stDelModal-{{ $s['id'] }}">Del</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-flag me-1"></i> Sin estados definidos aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- ═══ TFOOT · fila TOTAL (intuitividad) ═══ --}}
            <tfoot>
                @if($stSinEstado > 0)
                <tr class="st-row-sin">
                    <td>
                        <i class="bi bi-question-circle me-1" style="font-size: 8px;"></i>
                        Reservas sin estado asignado
                    </td>
                    <td></td>
                    <td class="text-center">{{ $stSinEstado }}</td>
                    <td></td>
                </tr>
                @endif
                <tr>
                    <td class="st-total-label">
                        <i class="bi bi-calculator me-1" style="font-size: 8px;"></i>
                        TOTAL · {{ $stItems->count() }} estado(s)
                    </td>
                    <td></td>
                    <td class="text-center" style="color: var(--accent);">{{ $stTotalReservas }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · CREAR (form clásico POST · sin JS)                      --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if($stStore)
<div class="modal fade reservas-soft-modal" id="stModalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Nuevo Estado</h6>
            </div>
            <form action="{{ $stStore }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" maxlength="100" required placeholder="Confirmada, Cancelada…" value="{{ old('name') }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Color (dot en tablas y calendario)</label>
                        <input type="color" class="form-control form-control-color form-control-sm w-100" name="color_hex" value="{{ old('color_hex', '#a3cfb6') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODALES POR ESTADO · EDITAR (precargados) y ELIMINAR (guard)     --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@foreach(($stItems ?? collect()) as $s)

    @if($stUpdate)
    <div class="modal fade reservas-soft-modal" id="stEditModal-{{ $s['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Editar: {{ $s['name'] }}</h6>
                </div>
                <form action="{{ str_replace('ID', $s['id'], $stUpdate) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="mb-2">
                            <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" maxlength="100" required value="{{ old('name', $s['name']) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color form-control-sm w-100" name="color_hex" value="{{ old('color_hex', $s['color']) }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($stDestroy)
    <div class="modal fade reservas-soft-modal" id="stDelModal-{{ $s['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-3 text-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                    <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar estado</h6>
                    <p class="mb-2" style="font-size: 8.5px; color: #94a3b8;">
                        Se eliminará <b style="color: #334155;">“{{ $s['name'] }}”</b> de todo el sistema.
                    </p>

                    @if($s['cntTotal'] > 0)
                        {{-- GUARD EN BLADE: bloqueado por tener reservas globales --}}
                        <div class="st-warn mb-2">
                            <i class="bi bi-lock-fill me-1"></i>
                            Bloqueado: tiene <b>{{ $s['cntTotal'] }}</b> reserva(s) asociada(s) en el sistema.
                        </div>
                        <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled
                                style="font-size: 11px; background: #fcfdfe; color: #d4dae2; border: 1px solid #f1f5f9; cursor: not-allowed;">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    @else
                        {{-- Sin reservas → form DELETE habilitado --}}
                        <form action="{{ str_replace('ID', $s['id'], $stDestroy) }}" method="POST"
                              onsubmit="return confirm('¿Confirmas eliminar el estado “{{ $s['name'] }}”?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm py-1 px-2 w-100"
                                    style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;">
                                <i class="bi bi-trash3"></i> Eliminar definitivamente
                            </button>
                        </form>
                    @endif
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

@endforeach

{{-- Búsqueda progresiva · ids únicos (stFiltrar) sin colisiones --}}
<script>
(function () {
    function stFiltrarInit() {
        try {
            var stInp = document.getElementById('stSearch');
            var stTbody = document.getElementById('stTbody');
            if (!stInp || !stTbody) return;

            var stRows = Array.prototype.slice.call(stTbody.querySelectorAll('tr[data-st-name]'));

            stInp.addEventListener('input', function () {
                var stVal = this.value.toLowerCase().trim();
                stRows.forEach(function (tr) {
                    var show = !stVal || (tr.getAttribute('data-st-name') || '').indexOf(stVal) !== -1;
                    tr.style.display = show ? '' : 'none';
                });
            });
        } catch (e) {}
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', stFiltrarInit); } else { stFiltrarInit(); }
})();
</script>