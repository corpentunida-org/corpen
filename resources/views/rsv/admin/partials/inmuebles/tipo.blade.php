@php
    use App\Models\Rsv\TipoInmueble;
    use App\Models\Rsv\CatalogoInmueble;

    // Fallbacks de seguridad
    $modoGlobal       = $modoGlobal ?? true;
    $tiposCollection  = $tiposCollection ?? TipoInmueble::withCount('catalogoInmuebles')->orderBy('nombre', 'asc')->get();
    $tipoAsignado     = $tipoAsignado ?? null;
    $tiUid            = $tiUid ?? ('ti' . substr(md5(uniqid('', true)), 0, 8));
@endphp

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

            <form action="{{ route('rsv.tipos-inmuebles.store') }}" method="POST">
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


{{-- SCRIPT · Filtros (búsqueda + "solo asignado") + llenado del modal Editar --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const UID = '{{ $tiUid }}';

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

        // Llenado de modal Editar
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
            });
        });
    });
</script>
