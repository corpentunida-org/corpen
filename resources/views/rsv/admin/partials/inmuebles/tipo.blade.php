@php
    use App\Models\Rsv\TipoInmueble;
    use App\Models\Rsv\CatalogoInmueble;

    /*
    ══════════════════════════════════════════════════════════════
    MODO Y CONTEXTO DEL INMUEBLE (FIX "NO FILTRA EN INDIVIDUAL")
    · GLOBAL: catálogo completo de tipos + búsqueda por texto.
    · INDIVIDUAL: además de la búsqueda, el tipo asignado al
      inmueble se ORDENA PRIMERO, se RESALTA con borde verde y hay
      un toggle "Solo asignado" que nace ACTIVO (la vista nace
      filtrada), igual que galería y tarifas.
    ══════════════════════════════════════════════════════════════
    */

    // ── Inmueble objetivo: modelo $inmueble → $inmuebleId → request numérico ──
    $tiInm = (isset($inmueble) && $inmueble) ? $inmueble : null;

    if (!$tiInm) {
        $tiInmIdReq = null;
        if (!empty($inmuebleId)) {
            $tiInmIdReq = $inmuebleId;
        } elseif (request('inmueble_id') && is_numeric(request('inmueble_id'))) {
            $tiInmIdReq = request('inmueble_id');
        }
        if ($tiInmIdReq) {
            $tiInm = CatalogoInmueble::find($tiInmIdReq);
        }
    }

    $modoGlobal = !$tiInm;

    // ── Tipo asignado al inmueble (garantizado aunque el controlador no cargue nada) ──
    $tipoAsignadoId = null;
    $tipoAsignado   = null;

    if (!$modoGlobal && $tiInm) {
        $tipoAsignadoId = $tiInm->tipo_inmueble_id ?? null;
        if ($tipoAsignadoId) {
            // Resolver el modelo del tipo: de la colección ya cargada si existe, si no, consulta puntual
            $coleccionTmp = isset($tiposInmuebles) ? collect($tiposInmuebles) : (isset($tipos) ? collect($tipos) : null);
            if ($coleccionTmp) {
                $tipoAsignado = $coleccionTmp->firstWhere('id', $tipoAsignadoId);
            }
            if (!$tipoAsignado) {
                $tipoAsignado = TipoInmueble::find($tipoAsignadoId);
            }
        }
    }

    // ── Colección de tipos (CRUD de catálogo global en ambos modos) ──
    if (isset($tiposInmuebles)) {
        $tiposCollection = collect($tiposInmuebles);
    } elseif (isset($tipos)) {
        $tiposCollection = collect($tipos);
    } else {
        $tiposCollection = TipoInmueble::withCount('catalogoInmuebles')->orderBy('nombre')->get();
    }

    // ═══ FIX CONTADORES EN 0 ═══
    // Si la colección vino del controlador SIN withCount y SIN la relación
    // cargada (típico en modo individual), la columna "Inmuebles" mostraba 0.
    // Se resuelven los counts REALES una sola vez y se consultan por mapa.
    $tiCountsMap = null;
    $primera = $tiposCollection->first();
    $countsCargados = !$primera
        || !is_null($primera->catalogo_inmuebles_count ?? null)
        || (method_exists($primera, 'relationLoaded') && $primera->relationLoaded('catalogoInmuebles'));
    if (!$countsCargados) {
        $tiCountsMap = TipoInmueble::withCount('catalogoInmuebles')->pluck('catalogo_inmuebles_count', 'id');
    }

    // Conteo de inmuebles asociados (usa withCount → relación → mapa de respaldo)
    $conteoInmuebles = function ($tipo) use ($tiCountsMap) {
        if (!is_null($tipo->catalogo_inmuebles_count ?? null)) return (int) $tipo->catalogo_inmuebles_count;
        if (method_exists($tipo, 'relationLoaded') && $tipo->relationLoaded('catalogoInmuebles')) {
            return $tipo->catalogoInmuebles->count();
        }
        if ($tiCountsMap !== null) return (int) ($tiCountsMap[$tipo->id] ?? 0);
        return 0;
    };

    // ═══ ORDEN EN INDIVIDUAL: el tipo asignado PRIMERO (visible sin scroll) ═══
    if (!$modoGlobal && $tipoAsignadoId) {
        $tiposCollection = $tiposCollection
            ->sortByDesc(function ($t) use ($tipoAsignadoId) {
                return $t->id == $tipoAsignadoId ? 1 : 0;
            })
            ->values();
    }

    // UID único por inclusión: si este partial se renderiza más de una vez en la
    // página, cada copia obtiene sus PROPIOS ids de modales/campos/botones/script.
    // Sin esto, Bootstrap abriría el modal de una copia oculta → gris congelado.
    $tiUid = 'ti' . substr(md5(uniqid('', true)), 0, 8);
@endphp

{{-- Escala px del sistema (idéntica a tarifas y galería) --}}
<style>
    .tipo-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }

    /* ═══ TABLA ULTRA-COMPACTA: filas de 20px exactas ═══ */
    .tipo-soft .table > :not(caption) > * > * {
        padding: 2px 6px; height: 20px;
        line-height: 1.1; vertical-align: middle;
    }
    .tipo-soft .table thead th {
        font-size: 9px; font-weight: 600;
        letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important;
        white-space: nowrap;
    }
    .tipo-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .tipo-soft .table tbody tr:hover > * {
        background-color: #fafcfe; --bs-table-accent-bg: transparent;
    }

    /* ═══ FILA DEL TIPO ASIGNADO (individual) — ahora IMPOSIBLE de no ver:
       fondo verde + barra lateral de acento. Antes solo el fondo (invisible
       en filas de 20px) → parecía que la vista no filtraba. ═══ */
    .tipo-soft tr.ti-assigned > * { background-color: #f0f7f2 !important; }
    .tipo-soft tr.ti-assigned > td:first-child { box-shadow: inset 2px 0 0 var(--accent); }

    /* Chips (misma escala que rsv-chip / gm-chip) */
    .tipo-soft .ti-chip {
        display: inline-flex; align-items: center; gap: 2px;
        font-size: 7px; font-weight: 600;
        padding: 0 5px; border-radius: 7px; line-height: 12px;
        border: 1px solid; white-space: nowrap; flex-shrink: 0;
    }
    .tipo-soft .ti-chip i { font-size: 6.5px; line-height: 1; }
    .tipo-soft .ti-chip.ok  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
    .tipo-soft .ti-chip.off { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }
    .tipo-soft .ti-chip.cnt { color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2; }
    .tipo-soft .ti-chip.asg { color: #3d7a5c; background: #e3f1e9; border-color: #d5e7dc; }

    /* Celdas con truncado limpio */
    .tipo-soft .ti-ellip { max-width: 220px; overflow: hidden; text-overflow: ellipsis; }

    /* Nombre del tipo: bold */
    .tipo-soft .ti-nombre {
        font-size: 8px; font-weight: 600; color: var(--ink-2);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    .tipo-soft .ti-num { color: var(--ink-4); text-align: center; width: 22px; }

    /* ═══ FILTROS DEL HEADER: búsqueda + toggle "Solo asignado" + contador ═══ */
    .tipo-soft .ti-buscar {
        font-size: 8.5px; color: var(--ink-2); background: #fff;
        border: 1px solid var(--grid-2); border-radius: 3px;
        padding: 2px 8px; width: 150px;
        box-shadow: none !important; outline: 0;
    }
    .tipo-soft .ti-buscar:focus { border-color: #c8d6cd; }
    .tipo-soft .ti-contador { font-size: 7.5px; color: var(--ink-4); white-space: nowrap; }
    .tipo-soft .ti-toggle { cursor: pointer; }
    .tipo-soft .ti-toggle .form-check-input {
        width: 26px; height: 14px; margin-top: 0; cursor: pointer;
        box-shadow: none !important;
    }

    /* ═══ MODALES — escala px idéntica al sistema ═══ */
    .tipo-soft-modal .modal-content {
        border: 1px solid var(--grid-2); border-radius: 5px;
        box-shadow: 0 8px 30px rgba(15,23,42,.06);
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px; line-height: 1.4; color: var(--ink);
    }
    .tipo-soft-modal .modal-header { border-bottom: 1px solid var(--grid); padding: 9px 14px; }
    .tipo-soft-modal .modal-title { font-size: 10.5px; font-weight: 600; color: var(--ink-2); }
    .tipo-soft-modal .modal-body { padding: 12px 14px; }
    .tipo-soft-modal .modal-footer {
        border-top: 1px solid var(--grid); padding: 9px 14px;
        display: flex; align-items: center; justify-content: space-between;
    }

    .tipo-soft-modal .tz-section { margin-bottom: 9px; }
    .tipo-soft-modal .tz-section:last-of-type { margin-bottom: 0; }
    .tipo-soft-modal .tz-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 7px; font-weight: 600; letter-spacing: .07em;
        text-transform: uppercase; color: var(--ink-4);
        margin-bottom: 4px;
    }
    .tipo-soft-modal .tz-label i { font-size: 7.5px; opacity: .7; }
    .tipo-soft-modal .tz-label::after { content: ''; flex: 1; height: 1px; background: var(--grid); }

    .tipo-soft-modal .form-label {
        font-size: 7.5px; font-weight: 600; letter-spacing: .05em;
        text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px;
    }
    .tipo-soft-modal .req { color: #c98a85; }
    .tipo-soft-modal .form-control,
    .tipo-soft-modal .form-select {
        font-size: 9.5px; color: var(--ink-2);
        border-color: var(--grid-2); border-radius: 3px;
        padding: 3px 8px; box-shadow: none !important;
    }
    .tipo-soft-modal .form-control:focus,
    .tipo-soft-modal .form-select:focus { border-color: #c8d6cd; }
    .tipo-soft-modal .tz-hint { font-size: 7px; color: #c3ccd6; margin-top: 1px; }

    .tipo-soft-modal .tz-switch-box {
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 4px; padding: 6px 9px;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .tipo-soft-modal .tz-switch-box .sw-title { font-size: 8.5px; font-weight: 600; color: var(--ink-2); }
    .tipo-soft-modal .tz-switch-box .sw-desc  { font-size: 7px; color: var(--ink-4); }
    .tipo-soft-modal .form-check-input { margin-top: 0; }

    /* Caja de advertencia (desactivar con inmuebles asociados) */
    .tipo-soft-modal .ti-warn {
        background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px;
        color: #c98a85; font-size: 8px; font-weight: 600;
        padding: 7px 9px; display: flex; align-items: center; gap: 6px;
    }
    .tipo-soft-modal .ti-warn i { font-size: 9px; flex-shrink: 0; }

    .tipo-soft-modal .btn-cancel-soft {
        background: #fff; color: var(--ink-3);
        border: 1px solid var(--grid-2); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .tipo-soft-modal .btn-cancel-soft:hover { color: var(--ink-2); background: #fafcfe; }
    .tipo-soft-modal .btn-save-soft {
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .tipo-soft-modal .btn-save-soft:hover { background: #e3f1e9; color: #34684e; }
    .tipo-soft-modal .btn-delete-soft {
        background: #fdf5f4; color: #c98a85;
        border: 1px solid #f0dcd8; border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .tipo-soft-modal .btn-delete-soft:hover { background: #fbeae7; color: #b05f58; }
</style>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- TIPOS DE INMUEBLE · PANEL PRINCIPAL (CRUD en AMBOS modos)        --}}
{{-- FIX: en individual ahora SÍ refleja el inmueble seleccionado:    --}}
{{--      ordena el tipo asignado primero, lo resalta y nace con el   --}}
{{--      toggle "Solo asignado" activo (vista filtrada por defecto). --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="tipo-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-tags me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $modoGlobal ? 'Tipos de Inmueble del Catálogo' : 'Tipos de Inmueble · Catálogo' }}
        </h6>

        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

            @if($modoGlobal)
                {{-- GLOBAL: contador total --}}
                <span class="ti-contador" id="tiContador-{{ $tiUid }}"></span>
            @else
                {{-- INDIVIDUAL: contexto visible del inmueble seleccionado --}}
                @if($tipoAsignado)
                    <span class="ti-chip asg" title="Tipo asignado a este inmueble">
                        <i class="bi bi-house-door"></i>Tipo asignado: {{ $tipoAsignado->nombre }}
                    </span>

                    {{-- Toggle "Solo asignado": la vista NACE filtrada (checked) --}}
                    <div class="form-check form-switch mb-0 ti-toggle d-flex align-items-center gap-1">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="tiSoloAsignado-{{ $tiUid }}" checked>
                        <label class="form-check-label mb-0" for="tiSoloAsignado-{{ $tiUid }}"
                               style="font-size: 7.5px; font-weight: 600; color: var(--ink-3); letter-spacing: .05em; text-transform: uppercase;">
                            Solo asignado
                        </label>
                    </div>
                @else
                    <span class="ti-chip off" title="Este inmueble no tiene tipo asignado">
                        <i class="bi bi-house"></i>Sin tipo asignado
                    </span>
                @endif

                <span class="ti-contador" id="tiContador-{{ $tiUid }}"></span>
            @endif

            {{-- Búsqueda por texto (ambos modos) --}}
            <input type="search" id="tiBuscar-{{ $tiUid }}" class="ti-buscar"
                   placeholder="Buscar tipo…" autocomplete="off">

            {{-- Botón Nuevo Tipo disponible en AMBOS modos --}}
            <button type="button"
                    class="btn btn-sm"
                    style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                    data-bs-toggle="modal" data-bs-target="#modalAddTipo-{{ $tiUid }}">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Tipo
            </button>

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-sm table-hover align-middle mb-0">

            <thead>
                <tr>
                    <th class="ti-num">#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center" style="width: 70px;">Inmuebles</th>
                    <th class="text-center" style="width: 60px;">Estado</th>
                    <th class="text-center" style="width: 110px;">·</th>
                </tr>
            </thead>

            <tbody id="tbodyTipos-{{ $tiUid }}">
                @if(isset($tiposCollection) && count($tiposCollection) > 0)

                    @foreach($tiposCollection as $tipo)

                        @php
                            $nInmuebles = $conteoInmuebles($tipo);
                            $esAsignado = (!$modoGlobal && !is_null($tipoAsignadoId) && $tipoAsignadoId == $tipo->id);
                        @endphp

                        {{-- data-es-asignado + data-search: los usan el toggle y la búsqueda client-side --}}
                        <tr class="{{ $esAsignado ? 'ti-assigned' : '' }}"
                            data-tipo-id="{{ $tipo->id }}"
                            data-es-asignado="{{ $esAsignado ? 1 : 0 }}"
                            data-search="{{ $tipo->nombre }} {{ $tipo->descripcion }}">

                            <td class="ti-num"><span class="ti-idx">{{ $loop->iteration }}</span></td>

                            <td class="ti-ellip">
                                <span class="ti-nombre" title="{{ $tipo->nombre }}">
                                    <i class="bi bi-tag-fill me-1" style="font-size: 6.5px; color: #5d8a70;"></i>{{ $tipo->nombre }}
                                </span>
                                {{-- Destacar el tipo asignado a este inmueble (solo individual) --}}
                                @if($esAsignado)
                                    <span class="ti-chip asg ms-1" title="Tipo asignado a este inmueble">
                                        <i class="bi bi-house-door"></i>Asignado
                                    </span>
                                @endif
                            </td>

                            <td class="ti-ellip" title="{{ $tipo->descripcion }}" style="color: #94a3b8;">
                                {{ $tipo->descripcion ?: '—' }}
                            </td>

                            {{-- Contador de inmuebles que usan este tipo --}}
                            <td class="text-center">
                                @if($nInmuebles > 0)
                                    <span class="ti-chip cnt" title="{{ $nInmuebles }} inmueble(s) usan este tipo">
                                        <i class="bi bi-house-door"></i>{{ $nInmuebles }}
                                    </span>
                                @else
                                    <span class="ti-chip off"><i class="bi bi-house"></i>0</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($tipo->active)
                                    <span class="ti-chip ok"><i class="bi bi-check-lg"></i>Activo</span>
                                @else
                                    <span class="ti-chip off"><i class="bi bi-dash-lg"></i>Inactivo</span>
                                @endif
                            </td>

                            {{-- Acciones disponibles en AMBOS modos --}}
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-tipo-{{ $tiUid }}"
                                        style="font-size: 8px; color: #3d7a5c; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarTipo-{{ $tiUid }}"
                                        data-url-update="{{ route('rsv.tipos-inmuebles.update', $tipo->id) }}"
                                        data-nombre="{{ $tipo->nombre }}"
                                        data-descripcion="{{ $tipo->descripcion }}"
                                        data-active="{{ $tipo->active ? 1 : 0 }}"
                                        data-count="{{ $nInmuebles }}">
                                    Editar
                                </button>
                                <span style="color: #e2e8f0;">·</span>
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none btn-eliminar-tipo-{{ $tiUid }}"
                                        style="font-size: 8px; color: #c98a85; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#modalEliminarTipo-{{ $tiUid }}"
                                        data-url-delete="{{ route('rsv.tipos-inmuebles.destroy', $tipo->id) }}"
                                        data-nombre="{{ $tipo->nombre }}"
                                        data-count="{{ $nInmuebles }}">
                                    Desactivar
                                </button>
                            </td>

                        </tr>

                    @endforeach

                @else
                    <tr>
                        <td colspan="6" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px;">
                            <i class="bi bi-tags me-1"></i> No hay tipos de inmueble registrados.
                        </td>
                    </tr>
                @endif

                {{-- Fila informativa: el filtro (búsqueda / solo-asignado) no arrojó resultados --}}
                <tr id="filaSinFiltro-{{ $tiUid }}" class="d-none">
                    <td colspan="6" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px;">
                        <i class="bi bi-funnel me-1"></i> Sin tipos que coincidan con el filtro o la búsqueda.
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · NUEVO TIPO DE INMUEBLE (ID único por inclusión)          --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalAddTipo-{{ $tiUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo · Tipos
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Nuevo Tipo de Inmueble</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.tipos-inmuebles.store') }}" method="POST">
                @csrf

                <div class="modal-body p-3">

                    {{-- 1 · INFORMACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Información</div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del tipo <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="nombre" placeholder="Ej. Apartamento, Casa, Finca…" maxlength="100" required>
                            <div class="tz-hint">Se muestra en el catálogo público como clasificación</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control form-control-sm" name="descripcion" rows="2" placeholder="Detalles opcionales sobre este tipo…"></textarea>
                        </div>
                    </div>


                    {{-- 2 · ESTADO --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-toggle-on"></i> Estado</div>

                        <div class="tz-switch-box">
                            <div class="text-start">
                                <div class="sw-title">Activo</div>
                                <div class="sw-desc">Disponible al crear inmuebles</div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="addTipoActive-{{ $tiUid }}" checked>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Clasifica los inmuebles del catálogo</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · EDITAR TIPO DE INMUEBLE (ID único por inclusión)         --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalEditarTipo-{{ $tiUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tipo_hint_title-{{ $tiUid }}" style="font-size: 0.95rem;">Tipo de Inmueble</h6>
                    <small class="text-muted" id="edit_tipo_hint-{{ $tiUid }}" style="font-size: 7.5px;">Selecciona un tipo…</small>
                </div>
            </div>

            <form id="formEditarTipo-{{ $tiUid }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-3">

                    {{-- 1 · INFORMACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Información</div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del tipo <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="edit_tipo_nombre-{{ $tiUid }}" name="nombre" maxlength="100" required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control form-control-sm" id="edit_tipo_descripcion-{{ $tiUid }}" name="descripcion" rows="2"></textarea>
                        </div>
                    </div>


                    {{-- 2 · ESTADO --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-toggle-on"></i> Estado</div>

                        <div class="tz-switch-box">
                            <div class="text-start">
                                <div class="sw-title">Activo</div>
                                <div class="sw-desc">Pausar sin eliminar</div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tipo_active-{{ $tiUid }}">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" id="edit_tipo_hint_count-{{ $tiUid }}" style="font-size: 7.5px;"></span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · DESACTIVAR TIPO (ID único por inclusión)                 --}}
{{-- Coincide con el comportamiento real del controlador (soft-off)   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalEliminarTipo-{{ $tiUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo · Desactivar
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Confirmar Desactivación</h6>
                </div>
            </div>

            <form id="formEliminarTipo-{{ $tiUid }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body p-3">

                    <p class="mb-2 text-start" style="font-size: 9.5px; color: var(--ink-2);">
                        ¿Desactivar el tipo <b id="del_tipo_nombre-{{ $tiUid }}" style="color: #334155;">—</b>? Pasará a estado <b>Inactivo</b> sin eliminarse físicamente.
                    </p>

                    {{-- Advertencia solo si hay inmuebles usando este tipo (el controlador lo bloqueará) --}}
                    <div class="ti-warn d-none" id="del_tipo_warning-{{ $tiUid }}">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Tiene <b id="del_tipo_count-{{ $tiUid }}">0</b> inmueble(s) asociado(s). El sistema no permitirá desactivarlo.</span>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Reversible desde Editar</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-delete-soft py-1 px-2" style="font-size: 11px;"><i class="bi bi-power"></i> Desactivar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- SCRIPT · Filtros (búsqueda + solo-asignado) + llenado de modales (scoped por UID) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const UID = '{{ $tiUid }}';

        /* ══════════════════════════════════════════════════════════
           FILTROS: búsqueda por texto + toggle "Solo asignado"
           · En individual con tipo asignado, el toggle NACE activo
             (server-side: checked) → la vista nace filtrada.
           · En global solo opera la búsqueda.
           ══════════════════════════════════════════════════════════ */
        const tbody    = document.getElementById('tbodyTipos-' + UID);
        const filas    = tbody ? Array.from(tbody.querySelectorAll('tr[data-tipo-id]')) : [];
        const total    = filas.length;
        const buscar   = document.getElementById('tiBuscar-' + UID);
        const soloChk  = document.getElementById('tiSoloAsignado-' + UID);
        const contador = document.getElementById('tiContador-' + UID);
        const filaSin  = document.getElementById('filaSinFiltro-' + UID);

        function aplicarFiltrosTipos() {
            if (!tbody) return;
            const q    = buscar ? buscar.value.trim().toLowerCase() : '';
            const solo = soloChk ? soloChk.checked : false;
            let visibles = 0;

            filas.forEach(tr => {
                const okTxt  = !q || (tr.dataset.search || '').toLowerCase().includes(q);
                const okAsig = !solo || tr.dataset.esAsignado === '1';
                const mostrar = okTxt && okAsig;

                tr.classList.toggle('d-none', !mostrar);
                if (mostrar) {
                    visibles++;
                    const idx = tr.querySelector('.ti-idx');
                    if (idx) idx.textContent = visibles;
                }
            });

            if (filaSin) filaSin.classList.toggle('d-none', visibles > 0 || total === 0);

            if (contador) {
                if (total === 0)        contador.textContent = '';
                else if (q || solo)     contador.textContent = 'Mostrando ' + visibles + ' de ' + total;
                else                    contador.textContent = total + (total === 1 ? ' tipo' : ' tipos');
            }
        }

        if (buscar) buscar.addEventListener('input', aplicarFiltrosTipos);
        if (soloChk) soloChk.addEventListener('change', aplicarFiltrosTipos);
        aplicarFiltrosTipos();

        /* ══════════════════════════════════════════════════════════
           EDITAR TIPO (scoped por UID)
           ══════════════════════════════════════════════════════════ */
        document.querySelectorAll('.btn-editar-tipo-' + UID).forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarTipo-' + UID);
                form.action = this.getAttribute('data-url-update');

                document.getElementById('edit_tipo_nombre-' + UID).value = this.getAttribute('data-nombre') || '';
                document.getElementById('edit_tipo_descripcion-' + UID).value = this.getAttribute('data-descripcion') || '';

                const activeCheck = document.getElementById('edit_tipo_active-' + UID);
                activeCheck.checked = this.getAttribute('data-active') === '1';

                /* Contexto del header/footer */
                const hintTitle = document.getElementById('edit_tipo_hint_title-' + UID);
                const hint      = document.getElementById('edit_tipo_hint-' + UID);
                const hintCount = document.getElementById('edit_tipo_hint_count-' + UID);

                const nombre = this.getAttribute('data-nombre') || '—';
                const count  = parseInt(this.getAttribute('data-count') || 0);

                if (hintTitle) hintTitle.textContent = nombre;

                if (hint) {
                    hint.innerHTML = this.getAttribute('data-active') === '1'
                        ? 'Estado: <b style="color:#5d8a70;">Activo</b>'
                        : 'Estado: <b style="color:#9aa5b1;">Inactivo</b>';
                }

                if (hintCount) {
                    hintCount.innerHTML = count > 0
                        ? 'Usado por <b style="color:#5d8a70;">' + count + '</b> inmueble(s)'
                        : 'Sin inmuebles asociados';
                }
            });
        });

        /* ══════════════════════════════════════════════════════════
           DESACTIVAR TIPO (scoped por UID)
           ══════════════════════════════════════════════════════════ */
        document.querySelectorAll('.btn-eliminar-tipo-' + UID).forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEliminarTipo-' + UID);
                form.action = this.getAttribute('data-url-delete');

                const nombre = this.getAttribute('data-nombre') || '—';
                const count  = parseInt(this.getAttribute('data-count') || 0);

                document.getElementById('del_tipo_nombre-' + UID).textContent = '“' + nombre + '”';

                const warn = document.getElementById('del_tipo_warning-' + UID);
                if (count > 0) {
                    document.getElementById('del_tipo_count-' + UID).textContent = count;
                    warn.classList.remove('d-none');
                } else {
                    warn.classList.add('d-none');
                }
            });
        });
    });
</script>
