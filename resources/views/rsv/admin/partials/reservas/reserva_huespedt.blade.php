{{-- partials/reservas/reserva_huespedt.blade.php · CRUD Huéspedes · v2.4 · botón Actualizar (icono) + prefill reserva por ID + recarga AJAX anti-duplicados + pulso visual (cambios visibles sin F5) --}}
{{--
  NOVEDADES v2.4 (icono de actualización):
  · Nuevo botón "Actualizar" en el header (icono bi-arrow-clockwise): recarga el fragmento
    por AJAX vía window.reloadRhFragment() sin F5. Mientras carga, el icono gira (rh-spin)
    y el botón se deshabilita ~1.2s para evitar clics repetidos; el pulso visual verde
    confirma el refresco. Funciona en modo global y single, sin depender de rutas del CRUD.
  · Todo lo demás de v2.3 se conserva intacto (prefill reserva por ID + anti-duplicados).

  NOVEDADES v2.3 (fix del campo Reserva al editar):
  · El dataset ahora incluye 'rid' => id_rsv_reservas (antes solo llevaba 'cod', el código de texto).
  · El modal de edición pre-selecciona la reserva POR ID (r.rid); si la reserva del huésped no
    está en la lista del select (limit 500 / filtro por inmueble), la opción se inyecta al vuelo.
    Antes se buscaba la opción por texto ('#' + cod) y fallaba cuando cod='—' o la reserva no
    estaba en la lista: el select quedaba vacío y el required obligaba a re-elegir.
  · Complementa el fix del backend: el controlador update() ahora sí persiste id_rsv_reservas.

  NOVEDADES v2.2 (sobre v2.1, cuyo dataset fresco ya funcionó: "Dataset: 8" en el panel):
  · v2.1 SE CONSERVA: tras POST/PUT/DELETE ok -> reloadRhFragment() trae el fragmento
    (AdminDashboardController::reservaHuespedesFragmento, Cache-Control no-store) con
    fetch cache:'no-store' + cache-buster _t, reemplaza #rhRoot Y los tags de datos
    #rhDataset/#rhReservasJson (viven FUERA de #rhRoot) y relanza initReservaHuespedes().
  · NUEVO anti-duplicados: si la página termina con varias copias del partial (include
    del dashboard + contenido reinyectado por AJAX en el panel), getElementById solo
    actualizaba la PRIMERA copia del DOM y la tabla VISIBLE quedaba congelada. Ahora se
    reemplaza la copia visible (offsetParent != null) y las demás se eliminan. El panel
    reporta cuántas copias había -> si dice "había duplicados", esa era la causa.
  · NUEVO pulso visual: la tarjeta destella ~0.9s al refrescarse -> confirmación a
    simple vista de que la recarga automática se aplicó (sin F5).
  · NUEVO try/catch alrededor del init post-recarga: si algo falla, el panel lo muestra
    en claro en lugar de quedarse mudo.
--}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="rhRoot"
     data-modo="{{ ($rhGlobal ?? true) ? 'global' : 'single' }}"
     data-ctx="{{ ($rhGlobal ?? true) ? 'Global' : ((is_object($rhInm ?? null) ? ($rhInm->name ?? 'Inmueble') : ('Inmueble #'.($rhInmId ?? '')))) }}"
     style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    @keyframes rhPulseFade { 0% { background-color: #e3f2e8; } 100% { background-color: transparent; } }
    .reservas-soft.rh-pulse { animation: rhPulseFade .9s ease-out 1; }
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

    /* v2.4 · Icono de actualización: giro del bi-arrow-clockwise mientras recarga */
    @keyframes rhSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .reservas-soft .rh-spin { animation: rhSpin .8s linear infinite; }

    /* ─── PANEL DE DEBUG FLOTANTE ─── */
    #rhDebugPanel {
        position: fixed; bottom: 10px; right: 10px; z-index: 9999;
        width: 380px; max-width: calc(100vw - 20px); max-height: 60vh;
        background: #0f172a; color: #e2e8f0;
        border: 1px solid #1e293b; border-radius: 6px;
        font-family: 'Courier New', monospace; font-size: 11px;
        display: none; flex-direction: column;
        box-shadow: 0 8px 24px rgba(0,0,0,.4);
    }
    #rhDebugPanel.open { display: flex; }
    #rhDebugPanel .dbg-header {
        background: #1e293b; padding: 6px 10px; border-radius: 6px 6px 0 0;
        display: flex; justify-content: space-between; align-items: center;
        cursor: pointer;
    }
    #rhDebugPanel .dbg-header b { font-size: 12px; color: #f1f5f9; }
    #rhDebugPanel .dbg-controls { display: flex; gap: 4px; }
    #rhDebugPanel .dbg-btn {
        background: #334155; color: #f1f5f9; border: 0;
        font-size: 10px; padding: 2px 8px; border-radius: 3px; cursor: pointer;
        font-family: inherit;
    }
    #rhDebugPanel .dbg-btn:hover { background: #475569; }
    #rhDebugPanel .dbg-body { flex: 1; overflow-y: auto; padding: 8px 10px; }
    #rhDebugPanel .dbg-body::-webkit-scrollbar { width: 6px; }
    #rhDebugPanel .dbg-body::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    #rhDebugPanel .dbg-log {
        padding: 4px 0; border-bottom: 1px solid #1e293b; line-height: 1.4;
        word-wrap: break-word; white-space: pre-wrap;
    }
    #rhDebugPanel .dbg-log:last-child { border-bottom: 0; }
    #rhDebugPanel .dbg-log .dbg-time { color: #64748b; font-size: 10px; }
    #rhDebugPanel .dbg-log.info  { color: #93c5fd; }
    #rhDebugPanel .dbg-log.ok    { color: #86efac; }
    #rhDebugPanel .dbg-log.warn  { color: #fcd34d; }
    #rhDebugPanel .dbg-log.err   { color: #fca5a5; }
    #rhDebugPanel .dbg-log.debug { color: #c4b5fd; }

    /* Botón Toggle Debug */
    #rhDebugToggle {
        position: fixed; bottom: 10px; right: 10px; z-index: 10000;
        background: #1e293b; color: #f1f5f9; border: 0;
        border-radius: 50%; width: 40px; height: 40px;
        cursor: pointer; font-size: 16px; box-shadow: 0 4px 12px rgba(0,0,0,.3);
        display: flex; align-items: center; justify-content: center;
    }
    #rhDebugToggle:hover { background: #334155; }
</style>

@php
    // 0 · Defaults
    $rhInm = null; $rhInmId = $inmuebleId ?? null; $rhGlobal = true;
    $rhItems = collect(); $rhReservasOptMapped = collect();
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

    // 2 · SEGURIDAD AUTOMÁTICA: Si el controlador no mandó $huespedes, los consultamos aquí mismo
    if (!isset($huespedes) || empty($huespedes)) {
        try {
            $qHuespedes = \App\Models\Rsv\ReservaHuesped::query()
                ->with(['reserva:id,codigo_reserva,id_rsv_catalogo_inmueble', 'reserva.inmueble:id,name', 'registrador:id,name']);
            
            if (!$rhGlobal && is_numeric($rhInmId)) {
                $qHuespedes->whereHas('reserva', function ($qq) use ($rhInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $rhInmId);
                });
            }
            $huespedes = $qHuespedes->orderBy('created_at', 'desc')->get();
        } catch (\Throwable $e) {
            $huespedes = collect();
        }
    }

    // 2b · Cargar reservas para el select si no vienen dadas
    if (!isset($rhReservasOpt) || empty($rhReservasOpt)) {
        try {
            $rq = \App\Models\Rsv\Reserva::query()->select('id', 'codigo_reserva');
            if (!$rhGlobal && is_numeric($rhInmId)) { 
                $rq->where('id_rsv_catalogo_inmueble', $rhInmId); 
            }
            $rhReservasOpt = $rq->orderBy('created_at', 'desc')->limit(500)->get();
        } catch (\Throwable $e) {
            $rhReservasOpt = collect();
        }
    }

    // 3 · Rutas (detección defensiva por varios nombres)
    $rhRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
            if (\Illuminate\Support\Facades\Route::has('rsv.' . $c)) {
                return $param !== null ? route('rsv.' . $c, $param) : route('rsv.' . $c);
            }
            if (\Illuminate\Support\Facades\Route::has('admin.' . $c)) {
                return $param !== null ? route('admin.' . $c, $param) : route('admin.' . $c);
            }
        }
        return null;
    };
    $rhStoreUrl       = $rhRuta(['rsv.reserva-huespedes.store', 'reserva-huespedes.store', 'rsv.reservahuespedes.store', 'huespedes.store']);
    $rhUpdateUrlBase  = $rhRuta(['rsv.reserva-huespedes.update', 'reserva-huespedes.update', 'rsv.reservahuespedes.update', 'huespedes.update'], 'ID');
    $rhDestroyUrlBase = $rhRuta(['rsv.reserva-huespedes.destroy', 'reserva-huespedes.destroy', 'rsv.reservahuespedes.destroy', 'huespedes.destroy'], 'ID');

    // 4 · Mapeo seguro del dataset de huéspedes
    try {
        $rawCollection = $huespedes ?? collect();
        $rhTotalCtx = count($rawCollection);

        $rhItems = collect($rawCollection)->map(function ($h) {
            return [
                'id'   => $h->id ?? null,
                'rid'  => $h->id_rsv_reservas ?? null, // v2.3: ID de la reserva para pre-seleccionar el select del modal
                'nom'  => (string) ($h->nombre ?? ''),
                'ape'  => (string) ($h->apellidos ?? ''),
                'tdoc' => (string) ($h->tipo_documento ?? ''),
                'ndoc' => (string) ($h->numero_documento ?? ''),
                'tit'  => ($h->es_titular ?? false) ? 1 : 0,
                'cod'  => (string) (optional($h->reserva)->codigo_reserva ?? '—'),
                'inm'  => (string) (optional(optional($h->reserva)->inmueble)->name ?? 'N/D'),
                'reg'  => (string) (optional($h->registrador)->name ?? '—'),
                'fec'  => isset($h->created_at) && $h->created_at ? $h->created_at->format('d/m/y') : '—',
            ];
        })->values();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('rh dataset view: '.$e->getMessage());
        $rhItems = collect();
    }

    // 4b · Reservas para el select del modal
    try {
        $rhReservasOptMapped = collect($rhReservasOpt ?? [])->map(fn ($r) => [
            'id' => is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null),
            'cod' => is_array($r) ? ($r['cod'] ?? $r['codigo_reserva'] ?? '') : ($r->codigo_reserva ?? '')
        ])->values();
    } catch (\Throwable $e) { $rhReservasOptMapped = collect(); }

    // 5 · Serialización raw segura
    $jhFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
    $rhJson = json_encode($rhItems->all(), $jhFlags); if (!is_string($rhJson)) { $rhJson = '[]'; }
    $rhReservasJson = json_encode($rhReservasOptMapped->all(), $jhFlags); if (!is_string($rhReservasJson)) { $rhReservasJson = '[]'; }
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
            <button type="button" id="rhRefresh" class="btn-mini d-inline-flex align-items-center gap-1" title="Recargar huéspedes por AJAX (sin F5)">
                <i class="bi bi-arrow-clockwise" style="font-size: 8px;"></i> Actualizar
            </button>
            <button type="button" id="rhExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Excel de TODO lo filtrado">
                <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
            </button>
            @if($rhStoreUrl)
                <button type="button" class="btn-green d-inline-flex align-items-center gap-1"
                        id="rhBtnNuevo" data-bs-toggle="modal" data-bs-target="#rhModalForm">
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

{{-- MODAL ERROR --}}
<div class="modal fade" id="rhModalError" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #f0dcd8; border-radius: 5px; font-size: 9.5px; background-color: #fdf5f4;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.5rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-2" style="font-size: 0.9rem; color: #b05c5c;">No se pudo completar</h6>
                <div id="rhErrorMsg" class="text-start" style="font-size: 8.5px; color: #a96863; white-space: pre-wrap; background: #fff; border: 1px solid #f7e6e3; border-radius: 4px; padding: 6px;"></div>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f7e6e3; padding: 9px 14px;">
                <button type="button" class="btn btn-sm py-1 px-3" style="font-size: 11px; background: #fff; color: #c98a85; border: 1px solid #f0dcd8;" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<!-- PANEL DE DEBUG FLOTANTE (HTML) -->
<button type="button" id="rhDebugToggle" title="Mostrar/ocultar panel de errores">...</button>
<div id="rhDebugPanel">
    <div class="dbg-header" id="rhDebugHeader">
        <b>🐛 Panel de Errores (Huéspedes)</b>
        <div class="dbg-controls">
            <button type="button" class="dbg-btn" id="rhDebugClear">Limpiar</button>
            <button type="button" class="dbg-btn" id="rhDebugClose">✕</button>
        </div>
    </div>
    <div class="dbg-body" id="rhDebugBody"></div>
</div>

<script type="application/json" id="rhDataset">{!! ($rhJson ?? '[]') !!}</script>
<script type="application/json" id="rhReservasJson">{!! ($rhReservasJson ?? '[]') !!}</script>

<script>
// Componente de Panel de Debug y Extractor Laravel 500
const DebugPanel = (function() {
    let dbgOpen = false;
    const dbgPanel  = document.getElementById('rhDebugPanel');
    const dbgBody   = document.getElementById('rhDebugBody');
    const dbgToggle = document.getElementById('rhDebugToggle');
    const dbgClose  = document.getElementById('rhDebugClose');
    const dbgClear  = document.getElementById('rhDebugClear');
    const dbgHeader = document.getElementById('rhDebugHeader');

    function togglePanel(forceState) {
        dbgOpen = (typeof forceState === 'boolean') ? forceState : !dbgOpen;
        if (dbgOpen) {
            dbgPanel.classList.add('open');
            dbgToggle.style.display = 'none';
        } else {
            dbgPanel.classList.remove('open');
            dbgToggle.style.display = 'flex';
        }
    }

    function log(level, msg) {
        if (!dbgBody) return;
        const t = new Date();
        const time = ('0'+t.getHours()).slice(-2)+':'+('0'+t.getMinutes()).slice(-2)+':'+('0'+t.getSeconds()).slice(-2);
        const div = document.createElement('div');
        div.className = 'dbg-log ' + (level || 'info');
        div.innerHTML = '<span class="dbg-time">['+time+']</span> ' + String(msg).replace(/&/g,'&amp;').replace(/</g,'&lt;');
        dbgBody.appendChild(div);
        dbgBody.scrollTop = dbgBody.scrollHeight;
    }

    if (dbgToggle) dbgToggle.addEventListener('click', () => togglePanel(true));
    if (dbgClose) dbgClose.addEventListener('click', () => togglePanel(false));
    if (dbgClear) dbgClear.addEventListener('click', () => { if (dbgBody) dbgBody.innerHTML = ''; });
    if (dbgHeader) dbgHeader.addEventListener('click', (e) => {
        if (e.target === dbgHeader) togglePanel(false);
    });

    /* v2.0 · Convivencia: si el tab de Reservas (v26.2) también tiene su
       panel de debug (IDs rv*), este panel y su botón bajan a bottom:54px
       para no quedar uno encima del otro (ambos viven en bottom:10px). */
    try {
        if (document.getElementById('rvDebugToggle')) {
            if (dbgPanel)  dbgPanel.style.bottom  = '54px';
            if (dbgToggle) dbgToggle.style.bottom = '54px';
        }
    } catch (e) {}

    function extraerErrorLaravel(bodyTxt, err) {
        let base = (err && err.message) ? String(err.message) : 'Error desconocido';
        if (!bodyTxt) return base;
        
        try {
            const j = JSON.parse(bodyTxt);
            if (j && (j.message || j.exception)) {
                const nomArc = j.file ? String(j.file).split('/').pop().split('\\').pop() : '';
                return (base + ' · backend: ' + (j.exception ? j.exception + ' → ' : '') + (j.message || '') + (nomArc ? ' [' + nomArc + ']' : '')).substring(0, 400);
            }
        } catch (e) { /* No era JSON */ }
        
        const t = String(bodyTxt).replace(/<script[\s\S]*?<\/script>/gi, ' ').replace(/<style[\s\S]*?<\/style>/gi, ' ');
        const mt = t.match(/<title>([\s\S]*?)<\/title>/i);
        if (mt) {
            const tit = mt[1].replace(/\s+/g, ' ').trim();
            if (tit && !/server error/i.test(tit)) return (base + ' · backend: ' + tit).substring(0, 400);
            base = base + ' · backend: ' + (tit || 'server error');
        }
        
        const plain = t.replace(/<[^>]+>/g, ' ').replace(/&nbsp;/g, ' ').replace(/&quot;/g, '"').replace(/\s+/g, ' ');
        const mExc = plain.match(/[A-Za-z0-9_\\]+(?:Exception|Error)\b[\s:]*[^\n]{0,200}/);
        if (mExc) return (base + ' · ' + mExc[0].trim()).substring(0, 400);
        
        return (base + ' · ' + plain.substring(0, 200)).substring(0, 400);
    }

    return {
        log: log,
        extraerErrorLaravel: extraerErrorLaravel,
        open: () => togglePanel(true)
    };
})();

(function () {
    const RH_FRAG_URL = '{!! route("rsv.admin.reservas.huespedes.fragmento", ["inmueble_id" => $rhInmId ?? "todos"]) !!}';

    window.reloadRhFragment = function() {
        var url = RH_FRAG_URL + (RH_FRAG_URL.indexOf('?') > -1 ? '&' : '?') + '_t=' + Date.now();
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, cache: 'no-store' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var newRoot = temp.querySelector('#rhRoot');
                if (!newRoot) {
                    DebugPanel.log('err', 'Recarga de fragmento: la respuesta no contiene #rhRoot (¿error del servidor?)');
                    return;
                }

                /* v2.2 · Anti-duplicados: si la página tiene varias copias del partial
                   (include del dashboard + contenido reinyectado por AJAX), getElementById
                   solo ve la PRIMERA y la tabla visible queda congelada. Se reemplaza la
                   copia VISIBLE (offsetParent != null) y las demás se eliminan. */
                var roots = Array.prototype.slice.call(document.querySelectorAll('#rhRoot'));
                var visibles = roots.filter(function (r) { return r.offsetParent !== null; });
                var target = visibles[0] || roots[0] || null;
                roots.forEach(function (r) { if (r !== target) { r.remove(); } });
                if (target) { target.replaceWith(newRoot); }
                else { document.body.appendChild(newRoot); }
                DebugPanel.log('info', 'Copias #rhRoot en la página: ' + roots.length
                    + (roots.length > 1 ? ' · ¡HABÍA DUPLICADOS! (eliminados)' : ''));

                /* v2.2 · Los tags de datos viven FUERA de #rhRoot: se eliminan TODAS las
                   copias viejas y se insertan las frescas; si sobreviviera una vieja,
                   init leería data rancia. */
                ['rhDataset', 'rhReservasJson'].forEach(function (id) {
                    document.querySelectorAll('#' + id).forEach(function (st) { st.remove(); });
                    var fresh = temp.querySelector('#' + id);
                    if (fresh) { document.body.appendChild(fresh); }
                    else { DebugPanel.log('warn', 'El fragmento no trajo #' + id); }
                });

                try { window.initReservaHuespedes(); }
                catch (e) { DebugPanel.log('err', 'Post-recarga: init falló: ' + e.message); }

                /* v2.2 · Pulso visual: destello de ~0.9s que confirma a simple vista
                   que la tabla se acaba de refrescar (sin F5). */
                try {
                    newRoot.classList.remove('rh-pulse');
                    void newRoot.offsetWidth;
                    newRoot.classList.add('rh-pulse');
                } catch (e) {}

                DebugPanel.log('ok', 'Fragmento Huéspedes recargado por AJAX · dataset fresco (v2.4)');
            })
            .catch(function (err) {
                DebugPanel.log('err', 'Error recargando fragmento: ' + err.message);
            });
    };

    window.initReservaHuespedes = function() {
        var root = document.getElementById('rhRoot');
        if (!root) return;

        var CSRF = '{{ csrf_token() }}';
        var STORE = '{{ $rhStoreUrl ?? "" }}';
        var UPDATE_URL = '{{ $rhUpdateUrlBase ?? "" }}';
        var DESTROY_URL = '{{ $rhDestroyUrlBase ?? "" }}';
        var EDITABLE = !!(STORE || UPDATE_URL);

        var DS = [];
        try { 
            var dsEl = document.getElementById('rhDataset');
            if (dsEl) DS = JSON.parse(dsEl.textContent || '[]'); 
        } catch (e) {}

        var RESERVAS = [];
        try { 
            var resEl = document.getElementById('rhReservasJson');
            if (resEl) RESERVAS = JSON.parse(resEl.textContent || '[]'); 
        } catch (e) {}


        /* v2.0 · Trazas de arranque en el panel de debug */
        DebugPanel.log('ok', 'v2.4 activo · botón Actualizar · prefill reserva por ID · recarga AJAX anti-duplicados + pulso visual · cambios sin F5');
        DebugPanel.log('info', 'init · modo=' + (root.getAttribute('data-modo') || 'global') + ' · ctx=' + (root.getAttribute('data-ctx') || 'Global'));
        DebugPanel.log((STORE || UPDATE_URL) ? 'ok' : 'warn', 'Rutas · STORE=' + (STORE || 'NO') + ' · UPDATE=' + (UPDATE_URL || 'NO') + ' · DESTROY=' + (DESTROY_URL || 'NO'));
        DebugPanel.log(DS.length ? 'ok' : 'warn', 'Dataset: ' + DS.length + ' huésped(es) · reservas para el select: ' + RESERVAS.length);
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
                  + (DS.length ? 'Sin resultados con los filtros.' : 'Sin huéspedes registrados aún.')
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
        }

        var search = document.getElementById('rhSearch');
        if (search) search.oninput = function () { q = this.value; page = 1; render(); };

        root.querySelectorAll('.fchip[data-tit]').forEach(function (c) {
            c.onclick = function () {
                root.querySelectorAll('.fchip[data-tit]').forEach(function (x) { x.classList.remove('active'); });
                c.classList.add('active');
                titF = c.getAttribute('data-tit') || '';
                page = 1; render();
            };
        });

        var sel = document.getElementById('rhReserva');
        if (sel) {
            sel.innerHTML = '<option value="">-- Seleccione la reserva --</option>';
            RESERVAS.forEach(function (r) {
                var o = document.createElement('option');
                o.value = r.id; o.textContent = '#' + r.cod;
                sel.appendChild(o);
            });
        }

        function mostrarErrorRH(msg) {
            DebugPanel.log('err', msg);
            DebugPanel.open();

            var modalEl = document.getElementById('rhModalError');
            var msgEl = document.getElementById('rhErrorMsg');
            
            if (modalEl && msgEl) {
                msgEl.textContent = msg;
                modalEl.style.zIndex = "1060";
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        inst.show();
                    } else if (typeof window.$ !== 'undefined') {
                        $(modalEl).modal('show');
                    } else {
                        alert("No se pudo completar:\n\n" + msg);
                    }
                } catch(e) {
                    alert("No se pudo completar:\n\n" + msg);
                }
            } else {
                alert("No se pudo completar:\n\n" + msg);
            }
        }

        function enviar(url, method, fd) {
            fd.append('_token', CSRF);
            if (method !== 'POST') fd.append('_method', method);
            
            DebugPanel.log('info', 'Enviando petición [' + method + '] a: ' + url);

            fetch(url, { 
                method: 'POST', 
                body: fd, 
                headers: { 
                    'X-CSRF-TOKEN': CSRF, 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json' 
                } 
            })
            .then(async function (r) { 
                if (!r.ok) {
                    const bodyTxt = await r.text();
                    const er = new Error('HTTP ' + r.status);
                    er.__body = bodyTxt;
                    throw er;
                }
                return r.json();
            })
            .then(function (j) {
                DebugPanel.log('ok', 'Operación exitosa.');

                document.querySelectorAll('.modal.show').forEach(m => {
                    try {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            const bsModal = bootstrap.Modal.getInstance(m);
                            if (bsModal) bsModal.hide();
                        } else if (typeof window.$ !== 'undefined') {
                            $(m).modal('hide');
                        }
                    } catch(e) {}
                });
                
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');

                window.reloadRhFragment();
            })
            .catch(function (err) { 
                const detalle = DebugPanel.extraerErrorLaravel(err.__body || '', err);
                mostrarErrorRH(detalle);
            });
        }

        var modalForm = document.getElementById('rhModalForm');
        var modalDel  = document.getElementById('rhModalDel');
        var mForm, mDel;
        try { 
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                mForm = bootstrap.Modal.getOrCreateInstance(modalForm); 
                mDel = bootstrap.Modal.getOrCreateInstance(modalDel); 
            }
        } catch (e) {}

        var btnNuevo = document.getElementById('rhBtnNuevo');
        if (btnNuevo) {
            btnNuevo.onclick = function() {
                document.getElementById('rhModalTitle').textContent = 'Nuevo Huésped';
                document.getElementById('rhId').value = '';
                var form = document.getElementById('rhForm');
                if (form) form.reset();
            };
        }

        /* v2.4 · Botón Actualizar: recarga el fragmento por AJAX (sin F5).
           Deshabilita el botón y hace girar el icono mientras llega la respuesta;
           el timeout cubre también el caso de error para re-habilitar siempre. */
        var btnRefresh = document.getElementById('rhRefresh');
        if (btnRefresh) {
            btnRefresh.onclick = function () {
                var ico = btnRefresh.querySelector('i.bi');
                try {
                    btnRefresh.disabled = true;
                    if (ico) ico.classList.add('rh-spin');
                } catch (e) {}
                DebugPanel.log('info', 'Actualización manual solicitada · recargando fragmento…');
                try { window.reloadRhFragment(); }
                catch (e) { DebugPanel.log('err', 'Actualizar falló: ' + e.message); }
                setTimeout(function () {
                    try {
                        btnRefresh.disabled = false;
                        if (ico) ico.classList.remove('rh-spin');
                    } catch (e) {}
                }, 1200);
            };
        }

        root.onclick = function (e) {
            var btnE = e.target.closest('.rh-edit');
            var btnD = e.target.closest('.rh-del');

            if (btnE && EDITABLE) {
                var r = DS.find(function (x) { return String(x.id) === btnE.getAttribute('data-id'); });
                if (!r) return;

                document.getElementById('rhModalTitle').textContent = 'Editar Huésped';
                document.getElementById('rhId').value = r.id;
                /* v2.3 · Pre-selección de la reserva POR ID (r.rid del dataset). Antes se
                   buscaba la opción por texto ('#' + r.cod) y fallaba cuando cod='—' o la
                   reserva no estaba en la lista del select (limit 500 / filtro por inmueble):
                   el select quedaba vacío y el required obligaba a re-elegir. */
                sel.value = '';
                
                var rid = (r.rid !== undefined && r.rid !== null && r.rid !== '') ? String(r.rid) : '';
                if (rid) {
                    var opt = sel.querySelector('option[value="' + rid + '"]');
                    if (!opt) { opt = new Option('#' + (r.cod || rid), rid, false, true); sel.add(opt); }
                    sel.value = rid;
                }

                document.getElementById('rhNombre').value = r.nom || '';
                document.getElementById('rhApellidos').value = r.ape || '';
                document.getElementById('rhTdoc').value = r.tdoc || '';
                document.getElementById('rhNdoc').value = r.ndoc || '';
                document.getElementById('rhTit').checked = !!r.tit;

                if (mForm) mForm.show();
                else if (typeof window.$ !== 'undefined') $(modalForm).modal('show');
            }

            if (btnD && DESTROY_URL) {
                var r2 = DS.find(function (x) { return String(x.id) === btnD.getAttribute('data-id'); });
                if (!r2) return;

                document.getElementById('rhDelNombre').textContent = r2.nom + ' ' + r2.ape;
                document.getElementById('rhDelCod').textContent = r2.cod;

                var go = document.getElementById('rhDelGo');
                go.onclick = function () {
                    var url = DESTROY_URL.replace('ID', r2.id);
                    enviar(url, 'DELETE', new FormData());
                };
                
                if (mDel) mDel.show();
                else if (typeof window.$ !== 'undefined') $(modalDel).modal('show');
            }
        };

        var rhForm = document.getElementById('rhForm');
        if (rhForm) {
            rhForm.onsubmit = function (e) {
                e.preventDefault();
                var fd = new FormData(this);
                
                var chkTit = document.getElementById('rhTit');
                if (chkTit) fd.set('es_titular', chkTit.checked ? '1' : '0');

                var id = document.getElementById('rhId').value;
                var url, method;

                if (id && UPDATE_URL) { 
                    url = UPDATE_URL.replace('ID', id); 
                    method = 'PUT'; 
                } else if (STORE) { 
                    url = STORE; 
                    method = 'POST'; 
                } else { 
                    mostrarErrorRH('Ruta de guardado no configurada.'); 
                    return; 
                }

                enviar(url, method, fd);
            };
        }

        render();
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.initReservaHuespedes();
    });
})();
</script>