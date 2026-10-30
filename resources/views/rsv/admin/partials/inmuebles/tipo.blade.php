{{--
    ═══════════════════════════════════════════════════════════════════════
    partials/inmuebles/tipo.blade.php · TIP-v1.3-DBG
    Tipos de Inmueble · NIVEL 3 (panel + modal Nuevo + modal Editar)
    ───────────────────────────────────────────────────────────────────────
    FIXES TIP-v1:
      1 · FATAL: $tipoAsignadoId se usaba en el foreach SIN fallback
          ("Undefined variable" cuando el controlador no lo envía, típico
          en modo GLOBAL). Ahora se deriva: $tipoAsignadoId ?? $tipoAsignado->id.
      2 · Script re-ejecutable tras swap AJAX (patrón v31): sin
          DOMContentLoaded, guard por UID, listeners sobre elementos frescos.
      3 · Formularios store/update interceptados con fetch (sin recarga):
          · X-Requested-With + Accept: application/json
          · r.ok + detección de redirect a /login + respuesta "error" plano
          · tras éxito: refresco AJAX de #showRoot (data-frag) si esta vista
            vive dentro de él; si no → recarga nativa honesta.
      4 · Panel 🐛 de debug integrado (mismo estilo que CAT-v2-DBG):
          apilable con rsv/rv/rh/cat/inm, NO captura errores ajenos,
          idempotente (sobrevive a swaps).
      5 · El form "Nuevo Tipo" ahora tiene id="formAddTipo-UID" para poder
          interceptarlo (el de Editar ya tenía).
      6 · FIX TIP-v1.1-1 · El botón 🐛 es EXCLUSIVO de esta vista: el toggle
          y el panel viajan como HTML del PROPIO fragmento (fuera del root,
          igual que CAT/TAR/GM/INM). Nacen al montar Tipos y mueren solos
          al navegar a otra vista: nada se cuelga de document.body, no hay
          vigilante JS ni limpieza manual posible de olvidar.

      7 · CAMBIO TIP-v1.2-1 · Pestaña 🐛 minimalista: 20px de alto, pegada
          a la esquina inferior-derecha (right:0 · bottom:0), radius solo
          en la esquina superior-izquierda y sin textos (emoji + badge).
          El panel se abre como CAJÓN alineado al mismo borde. La regla de
          distancias exacta está documentada como "SPEC DE DISTANCIAS"
          dentro del CSS, y el apilado con otros toggles se mide ahora en
          px reales (getBoundingClientRect) con 4px de aire entre vecinos.

      8 · CAMBIO TIP-v1.3-1 · Rediseño sobrio "terminal": la pestaña deja el
          emoji y pasa a texto DBG en monospace 8px con LED de estado
          (verde = ok · rojo pulsante + conteo = errores). Altura 16px y
          FIJA SIEMPRE en bottom:0 (nunca se apila hacia arriba: con 16px
          de alto cabe bajo cualquier otro toggle sin tocarlo). El panel
          sí esquiva a los vecinos: bottom = max(20, vecino + 4).

    NOTA DE APILADO: las vistas ya entregadas (tab-inmuebles, catalogo,
    tarifa y galeria) ya traen 'tipDbgToggle' en el array de ids de su
    función apilar(); si el toggle no está en el DOM (porque saliste de
    Tipos), simplemente lo omiten y no reservan hueco.
    ═══════════════════════════════════════════════════════════════════════
--}}

@php
    use App\Models\Rsv\TipoInmueble;
    use App\Models\Rsv\CatalogoInmueble;

    // Fallbacks de seguridad
    $modoGlobal       = $modoGlobal ?? true;
    $tiposCollection  = $tiposCollection ?? TipoInmueble::withCount('catalogoInmuebles')->orderBy('nombre', 'asc')->get();
    $tipoAsignado     = $tipoAsignado ?? null;

    // FIX TIP-v1-1: $tipoAsignadoId se usaba en el foreach sin fallback
    // (fatal "Undefined variable" cuando el controlador no lo envía).
    // Se deriva de $tipoAsignado si no viene explícito.
    $tipoAsignadoId   = $tipoAsignadoId ?? ($tipoAsignado->id ?? null);

    $tiUid            = $tiUid ?? ('ti' . substr(md5(uniqid('', true)), 0, 8));

    // Colector del panel de debug (TIP-v1.3-DBG) · via al panel vía @json
    $tipDbg = [
        'version'     => 'TIP-v1.3-DBG',
        'vista'       => 'partials/inmuebles/tipo.blade.php',
        'render'      => now()->format('H:i:s'),
        'uid'         => $tiUid,
        'modo'        => $modoGlobal ? 'GLOBAL' : 'INDIVIDUAL',
        'tipos_count' => count($tiposCollection),
        'asignado'    => $tipoAsignado->nombre ?? null,
        'asignado_id' => $tipoAsignadoId,
        'ruta_store'  => route('rsv.tipos-inmuebles.store'),
        'errores'     => [],
    ];
@endphp

{{-- TIP-v1.3 · Tab DBG textual + LED + Panel LOCAL (fuera del root: mueren con el fragmento · NO global) --}}
<div id="tipDbgToggle" title="Debug Tipos (TIP-v1.3-DBG)"><span class="tip-dbg-led" id="tipDbgLed"></span>DBG<span class="tip-dbg-badge" id="tipDbgBadge">0</span></div>
<div id="tipDbgPanel" role="dialog" aria-label="Panel de debug de Tipos">
    <div class="tip-dbg-header" id="tipDbgHeader">
        <span>DEBUG · Tipos (N3) · TIP-v1.3-DBG</span>
        <span class="tip-dbg-controls">
            <button type="button" class="tip-dbg-btn" id="tipDbgCopy">Copiar</button>
            <button type="button" class="tip-dbg-btn" id="tipDbgClear">Limpiar</button>
            <button type="button" class="tip-dbg-btn" id="tipDbgClose">✕</button>
        </span>
    </div>
    <div id="tipDbgBody"></div>
</div>

<div id="tipRoot-{{ $tiUid }}" class="tip-root">

    {{-- TIPOS DE INMUEBLE · PANEL PRINCIPAL (Bootstrap nativo + inline, sin <style>) --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9;">

        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">

            <h6 class="fw-bold mb-0 d-flex align-items-center" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-tags me-1" style="font-size: 8px; color: #5d8a70;"></i>
                {{ $modoGlobal ? 'Tipos de Inmueble del Catálogo' : 'Tipos de Inmueble · Catálogo' }}
            </h6>

            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">

                @if($modoGlobal)
                    <span id="tiContador-{{ $tiUid }}" style="font-size: 7.5px; color: #b3bcc7; white-space: nowrap;"></span>
                @else
                    @if($tipoAsignado)
                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #3d7a5c; background: #e3f1e9; border: 1px solid #d5e7dc; white-space: nowrap;" title="Tipo asignado a este inmueble">
                            <i class="bi bi-house-door" style="font-size: 6.5px; line-height: 1;"></i>Tipo asignado: {{ $tipoAsignado->nombre }}
                        </span>

                        <div class="form-check form-switch mb-0 d-flex align-items-center" style="gap: 4px; cursor: pointer;">
                            <input class="form-check-input" type="checkbox" role="switch" id="tiSoloAsignado-{{ $tiUid }}" checked style="width: 26px; height: 14px; margin-top: 0; cursor: pointer; box-shadow: none;">
                            <label class="form-check-label mb-0" for="tiSoloAsignado-{{ $tiUid }}" style="font-size: 7.5px; font-weight: 600; color: #94a3b8; letter-spacing: .05em; text-transform: uppercase;">
                                Solo asignado
                            </label>
                        </div>
                    @else
                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #9aa5b1; background: #fafbfd; border: 1px solid #eef2f6; white-space: nowrap;" title="Este inmueble no tiene tipo asignado">
                            <i class="bi bi-house" style="font-size: 6.5px; line-height: 1;"></i>Sin tipo asignado
                        </span>
                    @endif

                    <span id="tiContador-{{ $tiUid }}" style="font-size: 7.5px; color: #b3bcc7; white-space: nowrap;"></span>
                @endif

                <input type="search" id="tiBuscar-{{ $tiUid }}" placeholder="Buscar tipo…" autocomplete="off"
                       class="form-control form-control-sm"
                       style="font-size: 8.5px; color: #475569; background: #fff; border: 1px solid #e9eef4; border-radius: 3px; padding: 2px 8px; width: 150px; box-shadow: none; outline: 0;">

                <button type="button"
                        class="btn btn-sm d-inline-flex align-items-center"
                        style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                        data-bs-toggle="modal" data-bs-target="#modalAddTipo-{{ $tiUid }}">
                    <i class="bi bi-plus-lg me-1" style="font-size: 8px;"></i> Nuevo Tipo
                </button>

            </div>

        </div>

        <div class="table-responsive">

            {{-- Tabla ultra-compacta: variables Bootstrap para padding 2px/6px + font-size 8px heredado --}}
            <table class="table table-sm table-hover align-middle mb-0"
                   style="font-size: 8px; color: #64748b; line-height: 1.1; --bs-table-cell-padding-x: 6px; --bs-table-cell-padding-y: 2px; --bs-table-hover-bg: #fafcfe; --bs-table-hover-color: #64748b;">

                <thead style="background-color: #fafbfd;">
                    <tr>
                        <th class="text-center" style="width: 22px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">#</th>
                        <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Nombre</th>
                        <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Descripción</th>
                        <th class="text-center" style="width: 70px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Inmuebles</th>
                        <th class="text-center" style="width: 60px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Estado</th>
                        <th class="text-center" style="width: 70px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Acciones</th>
                    </tr>
                </thead>

                <tbody id="tbodyTipos-{{ $tiUid }}">
                    @if(isset($tiposCollection) && count($tiposCollection) > 0)
                        @foreach($tiposCollection as $tipo)
                            @php
                                $nInmuebles = $tipo->catalogo_inmuebles_count ?? 0;
                                $esAsignado = (!$modoGlobal && !is_null($tipoAsignadoId) && $tipoAsignadoId == $tipo->id);
                                // Estilos inline para simular fila "ti-assigned" sin CSS custom
                                $tdBg     = $esAsignado ? 'background-color: #f0f7f2;' : '';
                                $tdAccent = $esAsignado ? 'box-shadow: inset 2px 0 0 #3d7a5c;' : '';
                            @endphp

                            <tr data-tipo-id="{{ $tipo->id }}"
                                data-es-asignado="{{ $esAsignado ? 1 : 0 }}"
                                data-search="{{ $tipo->nombre }} {{ $tipo->descripcion }}">

                                {{-- # --}}
                                <td class="text-center" style="{{ $tdBg }} {{ $tdAccent }} color: #b3bcc7;">
                                    <span class="ti-idx">{{ $loop->iteration }}</span>
                                </td>

                                {{-- Nombre + chip "Asignado" --}}
                                <td style="{{ $tdBg }} max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <span class="fw-semibold d-inline-flex align-items-center" style="font-size: 8px; color: #475569; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $tipo->nombre }}">
                                        <i class="bi bi-tag-fill me-1" style="font-size: 6.5px; color: #5d8a70; flex-shrink: 0;"></i>{{ $tipo->nombre }}
                                    </span>
                                    @if($esAsignado)
                                        <span class="badge d-inline-flex align-items-center ms-1" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #3d7a5c; background: #e3f1e9; border: 1px solid #d5e7dc; white-space: nowrap;" title="Tipo asignado a este inmueble">
                                            <i class="bi bi-house-door" style="font-size: 6.5px; line-height: 1;"></i>Asignado
                                        </span>
                                    @endif
                                </td>

                                {{-- Descripción --}}
                                <td style="{{ $tdBg }} max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #94a3b8;" title="{{ $tipo->descripcion }}">
                                    {{ $tipo->descripcion ?: '—' }}
                                </td>

                                {{-- Inmuebles (count) --}}
                                <td class="text-center" style="{{ $tdBg }}">
                                    @if($nInmuebles > 0)
                                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5b7fa6; background: #f2f6fb; border: 1px solid #dbe6f2; white-space: nowrap;" title="{{ $nInmuebles }} inmueble(s) usan este tipo">
                                            <i class="bi bi-house-door" style="font-size: 6.5px; line-height: 1;"></i>{{ $nInmuebles }}
                                        </span>
                                    @else
                                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #9aa5b1; background: #fafbfd; border: 1px solid #eef2f6; white-space: nowrap;">
                                            <i class="bi bi-house" style="font-size: 6.5px; line-height: 1;"></i>0
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="text-center" style="{{ $tdBg }}">
                                    @if($tipo->active)
                                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5d8a70; background: #f0f7f2; border: 1px solid #d8e9de; white-space: nowrap;">
                                            <i class="bi bi-check-lg" style="font-size: 6.5px; line-height: 1;"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #9aa5b1; background: #fafbfd; border: 1px solid #eef2f6; white-space: nowrap;">
                                            <i class="bi bi-dash-lg" style="font-size: 6.5px; line-height: 1;"></i>Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="text-center" style="{{ $tdBg }}">
                                    <button type="button"
                                            class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-tipo-{{ $tiUid }}"
                                            style="font-size: 8px; color: #3d7a5c; font-weight: 600; line-height: 1.4;"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTipo-{{ $tiUid }}"
                                            data-url-update="{{ route('rsv.tipos-inmuebles.update', $tipo->id) }}"
                                            data-nombre="{{ $tipo->nombre }}"
                                            data-descripcion="{{ $tipo->descripcion }}"
                                            data-active="{{ $tipo->active ? 1 : 0 }}"
                                            data-count="{{ $nInmuebles }}">
                                        Editar
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 12px; color: #b3bcc7; font-size: 8.5px;">
                                <i class="bi bi-tags me-1"></i> No hay tipos de inmueble registrados.
                            </td>
                        </tr>
                    @endif

                    {{-- Fila "sin resultados de filtro" --}}
                    <tr id="filaSinFiltro-{{ $tiUid }}" class="d-none">
                        <td colspan="6" class="text-center" style="padding: 12px; color: #b3bcc7; font-size: 8.5px;">
                            <i class="bi bi-funnel me-1"></i> Sin tipos que coincidan con el filtro o la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL · NUEVO TIPO DE INMUEBLE                                    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalAddTipo-{{ $tiUid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 5px; box-shadow: 0 8px 30px rgba(15,23,42,.06); font-size: 8px; line-height: 1.4; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    <div class="text-end">
                        <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">Catálogo · Tipos</div>
                        <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Nuevo Tipo de Inmueble</h6>
                        <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                    </div>
                </div>

                {{-- FIX TIP-v1-5: id="formAddTipo-UID" agregado para poder interceptar el submit con fetch --}}
                <form id="formAddTipo-{{ $tiUid }}" action="{{ route('rsv.tipos-inmuebles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body" style="padding: 12px 14px;">

                        {{-- 1 · INFORMACIÓN --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Información</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del tipo <span style="color: #c98a85;">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nombre" placeholder="Ej. Apartamento, Casa, Finca…" maxlength="100" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Se muestra en el catálogo público como clasificación</div>
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Descripción</label>
                                <textarea class="form-control form-control-sm" name="descripcion" rows="2" placeholder="Detalles opcionales sobre este tipo…" style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;"></textarea>
                            </div>
                        </div>

                        {{-- 2 · ESTADO --}}
                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-toggle-on" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Estado</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between" style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                <div class="text-start">
                                    <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                    <div style="font-size: 7px; color: #b3bcc7;">Disponible al crear inmuebles</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="addTipoActive-{{ $tiUid }}" checked style="margin-top: 0; box-shadow: none;">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                        <span class="text-muted" style="font-size: 7.5px;">Clasifica los inmuebles del catálogo</span>
                        <div class="d-flex" style="gap: 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="font-size: 11px; padding: 4px 8px;">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark" style="font-size: 11px; padding: 4px 8px;"><i class="bi bi-check-lg"></i> Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL · EDITAR TIPO DE INMUEBLE                                   --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalEditarTipo-{{ $tiUid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 5px; box-shadow: 0 8px 30px rgba(15,23,42,.06); font-size: 8px; line-height: 1.4; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    <div class="text-end">
                        <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">Catálogo · Editando</div>
                        <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tipo_hint_title-{{ $tiUid }}" style="font-size: 0.95rem;">Tipo de Inmueble</h6>
                        <small class="text-muted" id="edit_tipo_hint-{{ $tiUid }}" style="font-size: 7.5px;">Selecciona un tipo…</small>
                    </div>
                </div>

                <form id="formEditarTipo-{{ $tiUid }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" style="padding: 12px 14px;">

                        {{-- 1 · INFORMACIÓN --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Información</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Nombre del tipo <span style="color: #c98a85;">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="edit_tipo_nombre-{{ $tiUid }}" name="nombre" maxlength="100" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Descripción</label>
                                <textarea class="form-control form-control-sm" id="edit_tipo_descripcion-{{ $tiUid }}" name="descripcion" rows="2" style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;"></textarea>
                            </div>
                        </div>

                        {{-- 2 · ESTADO --}}
                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-toggle-on" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Estado</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between" style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                <div class="text-start">
                                    <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Activo</div>
                                    <div style="font-size: 7px; color: #b3bcc7;">Pausar sin eliminar</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tipo_active-{{ $tiUid }}" style="margin-top: 0; box-shadow: none;">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                        <span class="text-muted" id="edit_tipo_hint_count-{{ $tiUid }}" style="font-size: 7.5px;"></span>
                        <div class="d-flex" style="gap: 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="font-size: 11px; padding: 4px 8px;">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark" style="font-size: 11px; padding: 4px 8px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- SCRIPT · TIP-v1.3-DBG · filtros + modal Editar + forms AJAX + tab DBG --}}
    {{-- Patrón v31: el script viaja DENTRO de #tipRoot → se re-ejecuta tras   --}}
    {{-- cada swap AJAX y renace con sus listeners. Sin DOMContentLoaded       --}}
    {{-- (no vuelve a dispararse tras un swap). NO captura errores ajenos.     --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <script>
    /* ═══════════════ TIP-v1.3-DBG · tipo · NIVEL 3 ═══════════════ */
    (function () {
        var UID = '{{ $tiUid }}';
        var GUARD = '__tipInit_' + UID;
        if (window[GUARD]) { return; } /* idempotencia: el mismo render no se inicializa dos veces */
        window[GUARD] = true;

        var SRVDBG = @json($tipDbg);

        /* ═══ 1) PANEL DE DEBUG LOCAL (mismo estilo que CAT-v2-DBG) ═══ */
        var DBG = (window.TipoDbg && window.TipoDbg.alive && window.TipoDbg.alive())
            ? window.TipoDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('tipDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'tipDbgStyle';
                    st.textContent = [
                        /* ═════════ SPEC DE DISTANCIAS · TIP-v1.3 (tab terminal) ════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
                        '#tipDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#tipDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.tip-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.tip-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:tipDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes tipDbgBlink{50%{opacity:.35;}}',
                        '.tip-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#tipDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.tip-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.tip-dbg-controls{display:flex;gap:4px;}',
                        '.tip-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.tip-dbg-btn:hover{background:#475569;}',
                        '#tipDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.tip-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.tip-dbg-time{color:#64748b;margin-right:4px;}',
                        '.tip-dbg-info{color:#93c5fd;}.tip-dbg-ok{color:#86efac;}',
                        '.tip-dbg-warn{color:#fde68a;}.tip-dbg-err{color:#fca5a5;}',
                        '.tip-dbg-net{color:#c4b5fd;}.tip-dbg-debug{color:#94a3b8;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    /* FIX TIP-v1.1-1: el toggle y el panel ya vienen como HTML del
                       fragmento (fuera del root). Este respaldo solo los re-crea
                       DENTRO de #tipRoot-UID si un swap parcial los borrara;
                       NUNCA se cuelga nada de document.body (exclusividad total). */
                    var rootDbg = document.getElementById('tipRoot-' + UID);
                    if (!rootDbg) { return; } /* sin vista montada → cero nodos */
                    if (!$('tipDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'tipDbgToggle';
                        t.title = 'Debug Tipos (TIP-v1.3-DBG)';
                        t.innerHTML = '<span class="tip-dbg-led" id="tipDbgLed"></span>DBG<span class="tip-dbg-badge" id="tipDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('tipDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'tipDbgPanel';
                        p.innerHTML = '<div class="tip-dbg-header" id="tipDbgHeader">' +
                            '<span>DEBUG · Tipos (N3) · TIP-v1.3-DBG</span>' +
                            '<span class="tip-dbg-controls">' +
                            '<button type="button" class="tip-dbg-btn" id="tipDbgCopy">Copiar</button>' +
                            '<button type="button" class="tip-dbg-btn" id="tipDbgClear">Limpiar</button>' +
                            '<button type="button" class="tip-dbg-btn" id="tipDbgClose">✕</button>' +
                            '</span></div><div id="tipDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* TIP-v1.3: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila:
                   con 16px de alto cabe bajo cualquier otro toggle sin tocarlo).
                   Solo el PANEL esquiva a los vecinos: bottom = max(20, vecino + 4). */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('tipDbgToggle'), p = $('tipDbgPanel');
                    if (t) { t.style.bottom = '0px'; } /* SI O SI pegada abajo */
                    if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; } /* 16 tab + 4 aire */
                }
                function hora() {
                    var d = new Date();
                    function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                    return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
                }
                var api = {
                    log: function (txt, nivel) {
                        var b = $('tipDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'tip-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="tip-dbg-time">[' + hora() + ']</span>' +
                            '<span class="tip-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('tipDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('tipDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('tipDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('tipDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('tipDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('tipDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('tipDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('tipDbgBody'); if (b) { b.innerHTML = ''; } },
                    copy: function () {
                        var b = $('tipDbgBody'); if (!b) { return; }
                        var txt = '=== DEBUG tipo TIP-v1.3-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('log copiado al portapapeles', 'ok');
                            }).catch(function () {
                                api.log('no se pudo copiar (permisos del navegador)', 'warn');
                            });
                        } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
                    },
                    ensure: function () { asegurarNodos(); },
                    alive: function () { return !!$('tipDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                window.TipoDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure(); /* re-apila y re-crea nodos si algún swap de nivel superior los reemplazó */

        /* ═══ 1b) EXCLUSIVIDAD DE LA TAB DBG · FIX TIP-v1.1-1 (sin vigilante) ═══
           La tab y el panel ya no se cuelgan de document.body: viajan como
           HTML del propio fragmento. Al navegar a otra vista el contenedor se
           intercambia y la tab muere SOLO con el fragmento; al volver a Tipos
           renace con él. Nada que vigilar, nada que limpiar. */

        /* Botones del panel por DELEGACIÓN en document (sobreviven a cualquier swap) */
        if (window.__tipDbgDocHandler) { document.removeEventListener('click', window.__tipDbgDocHandler); }
        window.__tipDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#tipDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#tipDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#tipDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#tipDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        };
        document.addEventListener('click', window.__tipDbgDocHandler);
        if (window.__tipDbgDblHandler) { document.removeEventListener('dblclick', window.__tipDbgDblHandler); }
        window.__tipDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#tipDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__tipDbgDblHandler);

        /* ═══ 2) BOOT · contexto del servidor ═══ */
        DBG.log('TIP-v1.3-DBG activo · tab DBG fija en esquina (0,0) · LED de estado · exclusiva (muere con el fragmento)', 'ok');
        if (window.__tipEverBooted) { DBG.log('script re-ejecutado tras swap AJAX (el panel LOCAL continúa)', 'debug'); }
        window.__tipEverBooted = true;
        DBG.log('init · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · tipos=' + (SRVDBG ? SRVDBG.tipos_count : '?') +
            ' · uid=' + UID, 'info');
        if (SRVDBG && SRVDBG.asignado) {
            DBG.log('tipo asignado: ' + SRVDBG.asignado + ' (id ' + SRVDBG.asignado_id + ')', 'info');
        }
        DBG.log('Ruta store · ' + ((SRVDBG && SRVDBG.ruta_store) ? SRVDBG.ruta_store : '?'), 'debug');

        var __sr = document.getElementById('showRoot');
        var __miRoot = document.getElementById('tipRoot-' + UID);
        var __dentroSR = !!(__sr && __miRoot && __sr.contains(__miRoot));
        DBG.log('Zona · showRoot=' + (__sr ? 'presente' : 'ausente') +
            ' · data-frag=' + ((__sr && __sr.getAttribute('data-frag')) || 'NO') +
            ' · tipo dentro de showRoot=' + (__dentroSR ? 'sí' : 'no'), __dentroSR ? 'ok' : 'warn');
        if (!__dentroSR) {
            DBG.log('el refresco tras guardar será recarga nativa (esta vista no vive dentro de #showRoot)', 'warn');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        /* ═══ 3) FILTROS · búsqueda + "solo asignado" (lógica original, ya re-ejecutable) ═══ */
        var tbody    = document.getElementById('tbodyTipos-' + UID);
        var filas    = tbody ? Array.from(tbody.querySelectorAll('tr[data-tipo-id]')) : [];
        var total    = filas.length;
        var buscar   = document.getElementById('tiBuscar-' + UID);
        var soloChk  = document.getElementById('tiSoloAsignado-' + UID);
        var contador = document.getElementById('tiContador-' + UID);
        var filaSin  = document.getElementById('filaSinFiltro-' + UID);

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
        DBG.log('filtros listos · filas=' + total, 'debug');

        /* ═══ 4) LLENADO DEL MODAL EDITAR (lógica original + log) ═══ */
        document.querySelectorAll('.btn-editar-tipo-' + UID).forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarTipo-' + UID);
                form.action = this.getAttribute('data-url-update');

                document.getElementById('edit_tipo_nombre-' + UID).value = this.getAttribute('data-nombre') || '';
                document.getElementById('edit_tipo_descripcion-' + UID).value = this.getAttribute('data-descripcion') || '';

                const activeCheck = document.getElementById('edit_tipo_active-' + UID);
                activeCheck.checked = this.getAttribute('data-active') === '1';

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

                DBG.log('modal Editar ← "' + nombre + '" · PUT ' + (this.getAttribute('data-url-update') || '(sin url)'), 'debug');
            });
        });

        /* ═══ 5) FORMS AJAX · store + update SIN recarga (patrón v31/CAT-v2) ═══ */
        function tipRunScripts(scope) {
            if (!scope) return;
            scope.querySelectorAll('script').forEach(function (old) {
                var s = document.createElement('script');
                for (var i = 0; i < old.attributes.length; i++) {
                    s.setAttribute(old.attributes[i].name, old.attributes[i].value);
                }
                s.textContent = old.textContent;
                old.parentNode.replaceChild(s, old);
            });
        }

        function esLogin(url) {
            return !!(url && (String(url).indexOf('/login') !== -1 || String(url).indexOf('/logout') !== -1));
        }

        function cerrarModalesTipo() {
            ['modalAddTipo-' + UID, 'modalEditarTipo-' + UID].forEach(function (id) {
                var el = document.getElementById(id);
                if (!el) return;
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(el).hide();
                } else {
                    el.classList.remove('show');
                    el.style.display = 'none';
                }
            });
        }

        /* Refresco post-guardado: repinta #showRoot (N2) por AJAX si esta vista vive
           dentro de él; si no, recarga nativa honesta (la tabla debe reflejar el cambio). */
        function refrescarZona(etiqueta) {
            var sr = document.getElementById('showRoot');
            var miRoot = document.getElementById('tipRoot-' + UID);

            if (!sr || !sr.getAttribute('data-frag') || !miRoot || !sr.contains(miRoot)) {
                DBG.log(etiqueta + ' → fuera de #showRoot o sin data-frag → recarga nativa', 'warn');
                window.location.reload();
                return;
            }

            var fragBase = sr.getAttribute('data-frag');
            var extraQs  = (window.location.search || '').replace(/^\?/, '');
            var fragUrl  = fragBase + (extraQs ? (fragBase.indexOf('?') === -1 ? '?' : '&') + extraQs : '');
            DBG.log(etiqueta + ' → refrescando fragmento N2: ' + fragUrl, 'net');

            fetch(fragUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) {
                    if (esLogin(r.url)) { window.location.href = r.url; throw new Error('__sesion__'); }
                    if (!r.ok) { throw new Error('HTTP ' + r.status); }
                    return r.text();
                })
                .then(function (html) {
                    var tmp = document.createElement('div');
                    tmp.innerHTML = html;
                    var nuevo = tmp.querySelector('#showRoot');
                    if (!nuevo) { throw new Error('el fragmento no contiene #showRoot'); }

                    /* limpieza anti-backdrop congelado (el modal moría a mitad de la transición) */
                    try {
                        document.querySelectorAll('.modal-backdrop').forEach(function (n) { if (n.parentNode) n.parentNode.removeChild(n); });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                    } catch (e2) { /* silencioso */ }

                    sr.replaceWith(nuevo);
                    tipRunScripts(nuevo);

                    /* auto-tab: reactiva el .tab-pane que contiene el fragmento repintado */
                    var pane = nuevo.closest ? nuevo.closest('.tab-pane') : null;
                    if (pane && pane.id && !pane.classList.contains('active')) {
                        var link = document.querySelector('[data-bs-target="#' + pane.id + '"], [href="#' + pane.id + '"]');
                        if (link && window.bootstrap && bootstrap.Tab) { bootstrap.Tab.getOrCreateInstance(link).show(); }
                    }
                    try { window.dispatchEvent(new CustomEvent('rsv:inmueble-cambiado', { detail: { origen: 'tipo' } })); } catch (e3) { /* silencioso */ }

                    DBG.log(etiqueta + ' → fragmento N2 actualizado ✓ (tabla de tipos fresca)', 'ok');
                })
                .catch(function (e) {
                    if (e && e.message === '__sesion__') { return; }
                    DBG.log('✗ refresco falló: ' + ((e && e.message) ? e.message : e) + ' → recarga nativa', 'err');
                    window.location.reload();
                });
        }

        function conectarForm(form, etiqueta) {
            if (!form || form.__tipFetchBound) { return; }
            form.__tipFetchBound = true;

            form.addEventListener('submit', function (ev) {
                var url = form.getAttribute('action');
                if (!url) {
                    DBG.log('✗ ' + etiqueta + ': formulario sin action → envío nativo', 'warn');
                    return; /* sin preventDefault → navegación nativa */
                }
                ev.preventDefault();

                var btn = form.querySelector('button[type="submit"]');
                var metodoReal = 'POST';
                var mInp = form.querySelector('input[name="_method"]');
                if (mInp) { metodoReal = String(mInp.value || 'POST').toUpperCase(); }

                DBG.log(etiqueta + ' → fetch ' + metodoReal + ' ' + url, 'net');
                if (btn) { btn.disabled = true; }

                fetch(url, {
                    method: 'POST', /* _method=PUT viaja en el FormData (override de Laravel) */
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form),
                    redirect: 'follow'
                })
                    .then(function (r) {
                        if (esLogin(r.url)) { window.location.href = r.url; throw new Error('__sesion__'); }
                        if (!r.ok) {
                            var extra419 = (r.status === 419) ? ' · CSRF expirado: recarga la página' : '';
                            return r.text().then(function (t) {
                                throw new Error('HTTP ' + r.status + extra419 + ' → ' + String(t).slice(0, 180));
                            });
                        }
                        var ct = (r.headers.get('content-type') || '').toLowerCase();
                        if (ct.indexOf('application/json') !== -1) {
                            return r.json().then(function (j) { return { tipo: 'json', data: j, url: r.url }; });
                        }
                        return r.text().then(function (t) { return { tipo: 'texto', url: r.url, texto: t }; });
                    })
                    .then(function (res) {
                        if (res.tipo === 'json') {
                            var d = res.data || {};
                            if (d.ok === false || d.success === false || d.error) {
                                throw new Error('JSON error: ' + (d.message || d.error || JSON.stringify(d).slice(0, 140)));
                            }
                            DBG.log(etiqueta + ' → JSON ok' + (d.message ? ' · ' + d.message : ''), 'ok');
                            if (d.redirect) {
                                DBG.log(etiqueta + ' → JSON pide redirect a ' + d.redirect, 'net');
                                window.location.href = d.redirect;
                                return;
                            }
                        } else {
                            var head = String(res.texto || '').trim().slice(0, 40).toLowerCase().replace(/^"|"$/g, '');
                            if (head === 'error') {
                                throw new Error('el controlador devolvió "error" plano (revisa storage/logs/laravel.log)');
                            }
                            DBG.log(etiqueta + ' → redirect seguido (HTML) · tratado como éxito', 'warn');
                        }
                        cerrarModalesTipo();
                        refrescarZona(etiqueta);
                    })
                    .catch(function (e) {
                        if (e && e.message === '__sesion__') { return; }
                        DBG.log('✗ ' + etiqueta + ' falló: ' + ((e && e.message) ? e.message : e), 'err');
                        DBG.open();
                        if (btn) { btn.disabled = false; }
                    });
            });
        }

        conectarForm(document.getElementById('formAddTipo-' + UID), 'Nuevo Tipo');
        conectarForm(document.getElementById('formEditarTipo-' + UID), 'Editar Tipo');
        DBG.log('forms AJAX conectados (store + update) · guardado sin recarga', 'ok');
    })();
    </script>
</div>