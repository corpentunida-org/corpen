@php
    use App\Models\Rsv\TipoInmueble;

    // Obtener la colección de forma segura tanto si viene del controlador como si se carga directamente
    $tiposCollection = isset($tiposInmuebles)
        ? $tiposInmuebles
        : (isset($tipos) ? $tipos : TipoInmueble::withCount('catalogoInmuebles')->orderBy('nombre')->get());

    // Conteo de inmuebles asociados (usa withCount si está, si no lazy-load)
    $conteoInmuebles = function ($tipo) {
        if (!is_null($tipo->catalogo_inmuebles_count)) return $tipo->catalogo_inmuebles_count;
        return $tipo->catalogoInmuebles->count();
    };
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

    /* Celdas con truncado limpio */
    .tipo-soft .ti-ellip { max-width: 220px; overflow: hidden; text-overflow: ellipsis; }

    /* Nombre del tipo: bold */
    .tipo-soft .ti-nombre {
        font-size: 8px; font-weight: 600; color: var(--ink-2);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    .tipo-soft .ti-num { color: var(--ink-4); text-align: center; width: 22px; }

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

    /* Caja de advertencia (eliminar con inmuebles asociados) */
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
{{-- TIPOS DE INMUEBLE · PANEL PRINCIPAL                              --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="tipo-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-tags me-1" style="font-size: 8px; color: #5d8a70;"></i>
            Tipos de Inmueble
        </h6>

        <button type="button"
                class="btn btn-sm"
                style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                data-bs-toggle="modal" data-bs-target="#modalAddTipo">
            <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Tipo
        </button>

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
                    <th class="text-center" style="width: 76px;">·</th>
                </tr>
            </thead>

            <tbody>
                @if(isset($tiposCollection) && count($tiposCollection) > 0)

                    @foreach($tiposCollection as $tipo)

                        @php $nInmuebles = $conteoInmuebles($tipo); @endphp

                        <tr>

                            <td class="ti-num">{{ $loop->iteration }}</td>

                            <td class="ti-ellip">
                                <span class="ti-nombre" title="{{ $tipo->nombre }}">
                                    <i class="bi bi-tag-fill me-1" style="font-size: 6.5px; color: #5d8a70;"></i>{{ $tipo->nombre }}
                                </span>
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

                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-tipo"
                                        style="font-size: 8px; color: #3d7a5c; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarTipo"
                                        data-url-update="{{ route('rsv.tipos-inmueble.update', $tipo->id) }}"
                                        data-nombre="{{ $tipo->nombre }}"
                                        data-descripcion="{{ $tipo->descripcion }}"
                                        data-active="{{ $tipo->active ? 1 : 0 }}"
                                        data-count="{{ $nInmuebles }}">
                                    Editar
                                </button>
                                <span style="color: #e2e8f0;">·</span>
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none btn-eliminar-tipo"
                                        style="font-size: 8px; color: #c98a85; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#modalEliminarTipo"
                                        data-url-delete="{{ route('rsv.tipos-inmueble.destroy', $tipo->id) }}"
                                        data-nombre="{{ $tipo->nombre }}"
                                        data-count="{{ $nInmuebles }}">
                                    Eliminar
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
            </tbody>

        </table>

    </div>

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL · NUEVO TIPO DE INMUEBLE                                   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalAddTipo" tabindex="-1" aria-hidden="true">
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

            <form action="{{ route('rsv.tipos-inmueble.store') }}" method="POST">
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
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="addTipoActive" checked>
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
{{-- MODAL · EDITAR TIPO DE INMUEBLE                                  --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalEditarTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tipo_hint_title" style="font-size: 0.95rem;">Tipo de Inmueble</h6>
                    <small class="text-muted" id="edit_tipo_hint" style="font-size: 7.5px;">Selecciona un tipo…</small>
                </div>
            </div>

            <form id="formEditarTipo" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-3">

                    {{-- 1 · INFORMACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Información</div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del tipo <span class="req">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="edit_tipo_nombre" name="nombre" maxlength="100" required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control form-control-sm" id="edit_tipo_descripcion" name="descripcion" rows="2"></textarea>
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
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tipo_active">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" id="edit_tipo_hint_count" style="font-size: 7.5px;"></span>
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
{{-- MODAL · ELIMINAR TIPO DE INMUEBLE                                --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tipo-soft-modal" id="modalEliminarTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Catálogo · Eliminar
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Confirmar Eliminación</h6>
                </div>
            </div>

            <form id="formEliminarTipo" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body p-3">

                    <p class="mb-2 text-start" style="font-size: 9.5px; color: var(--ink-2);">
                        ¿Eliminar el tipo <b id="del_tipo_nombre" style="color: #334155;">—</b>? Esta acción no se puede deshacer.
                    </p>

                    {{-- Advertencia solo si hay inmuebles usando este tipo --}}
                    <div class="ti-warn d-none" id="del_tipo_warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Tiene <b id="del_tipo_count">0</b> inmueble(s) asociado(s). Quedarán sin tipo asignado.</span>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Acción irreversible</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-delete-soft py-1 px-2" style="font-size: 11px;"><i class="bi bi-trash3"></i> Eliminar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- SCRIPT · Llenado de modales Editar / Eliminar (mismo patrón del sistema) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ═══ EDITAR TIPO ═══ */
        document.querySelectorAll('.btn-editar-tipo').forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarTipo');
                form.action = this.getAttribute('data-url-update');

                document.getElementById('edit_tipo_nombre').value = this.getAttribute('data-nombre') || '';
                document.getElementById('edit_tipo_descripcion').value = this.getAttribute('data-descripcion') || '';

                const activeCheck = document.getElementById('edit_tipo_active');
                activeCheck.checked = this.getAttribute('data-active') === '1';

                /* Contexto del header/footer */
                const hintTitle = document.getElementById('edit_tipo_hint_title');
                const hint      = document.getElementById('edit_tipo_hint');
                const hintCount = document.getElementById('edit_tipo_hint_count');

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

        /* ═══ ELIMINAR TIPO ═══ */
        document.querySelectorAll('.btn-eliminar-tipo').forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEliminarTipo');
                form.action = this.getAttribute('data-url-delete');

                const nombre  = this.getAttribute('data-nombre') || '—';
                const count   = parseInt(this.getAttribute('data-count') || 0);

                document.getElementById('del_tipo_nombre').textContent = '“' + nombre + '”';

                const warn = document.getElementById('del_tipo_warning');
                if (count > 0) {
                    document.getElementById('del_tipo_count').textContent = count;
                    warn.classList.remove('d-none');
                } else {
                    warn.classList.add('d-none');
                }
            });
        });

    });
</script>
