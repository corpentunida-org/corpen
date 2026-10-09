@php
    // Detectamos si estamos en la vista global o en un inmueble específico
    $modoGlobal = !isset($inmueble) || !$inmueble;
    $tarifasMostrar = [];

    if ($modoGlobal) {
        if (isset($listaInmuebles)) {
            foreach ($listaInmuebles as $prop) {
                if (isset($prop->tarifasTemporadas) && $prop->tarifasTemporadas) {
                    foreach ($prop->tarifasTemporadas as $t) {
                        $t->id_rsv_catalogo_inmueble = $prop->id;
                        $t->nombre_inmueble_padre = $prop->name ?? 'Inmueble';
                        $tarifasMostrar[] = $t;
                    }
                }
            }
        }
    } else {
        if (isset($inmueble->tarifasTemporadas)) {
            $tarifasMostrar = $inmueble->tarifasTemporadas;
        }
    }
@endphp

{{-- Escala px del sistema (idéntica a las demás vistas) --}}
<style>
    .tarifas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }

    .tarifas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; }
    .tarifas-soft .table thead th {
        font-size: 9px; font-weight: 600;
        letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important;
    }
    .tarifas-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums;
    }
    .tarifas-soft .table tbody tr:hover > * {
        background-color: #fafcfe; --bs-table-accent-bg: transparent;
    }
    .tarifas-soft .rsv-chip {
        display: inline-block;
        font-size: 7.5px; font-weight: 600;
        padding: 0 7px; border-radius: 8px; line-height: 13px;
        border: 1px solid;
    }
    .tarifas-soft .rsv-chip.ok  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
    .tarifas-soft .rsv-chip.off { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }

    /* ═══ MODALES — escala px idéntica al panel ═══ */
    .tarifas-soft-modal .modal-content {
        border: 1px solid var(--grid-2); border-radius: 5px;
        box-shadow: 0 8px 30px rgba(15,23,42,.06);
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px; line-height: 1.4; color: var(--ink);
    }
    .tarifas-soft-modal .modal-header {
        border-bottom: 1px solid var(--grid); padding: 9px 14px;
    }
    .tarifas-soft-modal .modal-title { font-size: 10.5px; font-weight: 600; color: var(--ink-2); }
    .tarifas-soft-modal .modal-sub { font-size: 7.5px; color: var(--ink-4); margin-top: 1px; }
    .tarifas-soft-modal .modal-sub b { color: #5d8a70; font-weight: 600; }
    .tarifas-soft-modal .modal-body { padding: 12px 14px; }
    .tarifas-soft-modal .modal-footer {
        border-top: 1px solid var(--grid); padding: 9px 14px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .tarifas-soft-modal .footer-hint { font-size: 7px; color: var(--ink-4); }
    .tarifas-soft-modal .footer-hint b { color: var(--ink-3); }

    /* Secciones */
    .tarifas-soft-modal .tz-section { margin-bottom: 9px; }
    .tarifas-soft-modal .tz-section:last-of-type { margin-bottom: 0; }
    .tarifas-soft-modal .tz-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 7px; font-weight: 600; letter-spacing: .07em;
        text-transform: uppercase; color: var(--ink-4);
        margin-bottom: 4px;
    }
    .tarifas-soft-modal .tz-label i { font-size: 7.5px; opacity: .7; }
    .tarifas-soft-modal .tz-label::after {
        content: ''; flex: 1; height: 1px; background: var(--grid);
    }

    /* Campos */
    .tarifas-soft-modal .form-label {
        font-size: 7.5px; font-weight: 600; letter-spacing: .05em;
        text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px;
    }
    .tarifas-soft-modal .req { color: #c98a85; }
    .tarifas-soft-modal .form-control,
    .tarifas-soft-modal .form-select {
        font-size: 9.5px; color: var(--ink-2);
        border-color: var(--grid-2); border-radius: 3px;
        padding: 3px 8px; box-shadow: none !important;
    }
    .tarifas-soft-modal .form-control:focus,
    .tarifas-soft-modal .form-select:focus { border-color: #c8d6cd; }
    .tarifas-soft-modal .tz-hint { font-size: 7px; color: #c3ccd6; margin-top: 1px; }

    /* Fechas conectadas */
    .tarifas-soft-modal .tz-range { display: flex; align-items: flex-end; gap: 6px; }
    .tarifas-soft-modal .tz-range > div { flex: 1; }
    .tarifas-soft-modal .tz-range .arrow {
        color: #c3ccd6; font-size: 8px; flex-shrink: 0;
        padding-bottom: 5px;
    }

    /* Dinero */
    .tarifas-soft-modal .money-group .input-group-text {
        background: var(--head); border-color: var(--grid-2);
        color: #5d8a70; font-size: 8px; font-weight: 600;
        padding-left: 6px; padding-right: 6px;
    }
    .tarifas-soft-modal .money-group .form-control { border-left: 0; padding-left: 4px; }
    .tarifas-soft-modal .tz-prices-box {
        background: #fafdfb; border: 1px solid #eaf3ee;
        border-radius: 4px; padding: 8px;
    }

    /* Switch */
    .tarifas-soft-modal .tz-switch-box {
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 4px; padding: 6px 9px;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .tarifas-soft-modal .tz-switch-box .sw-title { font-size: 8.5px; font-weight: 600; color: var(--ink-2); }
    .tarifas-soft-modal .tz-switch-box .sw-desc  { font-size: 7px; color: var(--ink-4); }
    .tarifas-soft-modal .form-check-input { margin-top: 0; }

    /* Botones */
    .tarifas-soft-modal .btn-cancel-soft {
        background: #fff; color: var(--ink-3);
        border: 1px solid var(--grid-2); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .tarifas-soft-modal .btn-cancel-soft:hover { color: var(--ink-2); background: #fafcfe; }
    .tarifas-soft-modal .btn-save-soft {
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .tarifas-soft-modal .btn-save-soft:hover { background: #e3f1e9; color: #34684e; }
</style>


<div class="tarifas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-cash-coin me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $modoGlobal ? 'Visión Global de Tarifas' : 'Tarifas y Temporadas del Inmueble' }}
        </h6>

        <button type="button"
                class="btn btn-sm"
                style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                data-bs-toggle="modal" data-bs-target="#modalAddTarifa">
            <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Tarifa
        </button>

    </div>


    <div class="table-responsive">

        <table class="table table-sm table-hover align-middle mb-0">

            <thead>
                <tr>
                    @if ($modoGlobal)
                        <th>Inmueble</th>
                    @endif
                    <th>Temporada</th>
                    <th class="text-center">Fechas</th>
                    <th class="text-end">Noche</th>
                    <th class="text-end">Fin Sem.</th>
                    <th class="text-end">Mín. Reserva</th>
                    <th class="text-center">Días Máx.</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">·</th>
                </tr>
            </thead>

            <tbody>
                @if (!empty($tarifasMostrar) && count($tarifasMostrar) > 0)

                    @foreach ($tarifasMostrar as $tarifa)

                        <tr>

                            @if ($modoGlobal)
                                <td class="fw-semibold" style="color: #5d8a70;">
                                    <i class="bi bi-building me-1" style="font-size: 7.5px; opacity: .7;"></i>{{ $tarifa->nombre_inmueble_padre ?? 'N/A' }}
                                </td>
                            @endif

                            <td class="fw-semibold" style="color: #475569;">
                                {{ $tarifa->nombre_temporada }}
                            </td>

                            <td class="text-center" style="color: #a8b3c1;">
                                {{ optional($tarifa->fecha_inicio)->format('d/m/Y') }} — {{ optional($tarifa->fecha_fin)->format('d/m/Y') }}
                            </td>

                            <td class="text-end fw-semibold" style="color: #5d8a70;">
                                ${{ number_format($tarifa->precio_noche ?? 0, 2) }}
                            </td>

                            <td class="text-end">
                                ${{ number_format($tarifa->precio_fin_semana ?? 0, 2) }}
                            </td>

                            <td class="text-end" style="color: #a8b3c1;">
                                ${{ number_format($tarifa->precio_minimo_reserva ?? 0, 2) }}
                            </td>

                            <td class="text-center">
                                {{ $tarifa->dias_maximos ?? 'N/A' }}
                            </td>

                            <td class="text-center">
                                @if(isset($tarifa->active) && $tarifa->active)
                                    <span class="rsv-chip ok">Activa</span>
                                @else
                                    <span class="rsv-chip off">Inactiva</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-tarifa"
                                        style="font-size: 8px; color: #3d7a5c; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarTarifa"
                                        data-url-update="{{ route('rsv.tarifas-temporadas.update', $tarifa->id) }}"
                                        data-inmueble-id="{{ $tarifa->id_rsv_catalogo_inmueble }}"
                                        data-nombre-temporada="{{ $tarifa->nombre_temporada }}"
                                        data-fecha-inicio="{{ optional($tarifa->fecha_inicio)->format('Y-m-d') }}"
                                        data-fecha-fin="{{ optional($tarifa->fecha_fin)->format('Y-m-d') }}"
                                        data-precio-noche="{{ $tarifa->precio_noche }}"
                                        data-precio-fin-semana="{{ $tarifa->precio_fin_semana }}"
                                        data-precio-minimo-reserva="{{ $tarifa->precio_minimo_reserva }}"
                                        data-dias-maximos="{{ $tarifa->dias_maximos }}"
                                        data-active="{{ $tarifa->active ? 1 : 0 }}">
                                    Editar
                                </button>
                            </td>

                        </tr>

                    @endforeach

                @else
                    <tr>
                        <td colspan="{{ $modoGlobal ? 9 : 8 }}" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px;">
                            No hay tarifas registradas.
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>

    </div>

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2A · NUEVA TARIFA                                          --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tarifas-soft-modal" id="modalAddTarifa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Motor Comercial
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Nueva Tarifa por Temporada</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST">
                @csrf

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-tag"></i> Identificación</div>

                        @if ($modoGlobal)
                            <div class="mb-2">
                                <label class="form-label small mb-1 text-start d-block">Inmueble <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="id_rsv_catalogo_inmueble" required>
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @isset($listaInmuebles)
                                        @foreach ($listaInmuebles as $propOpt)
                                            <option value="{{ $propOpt->id }}">{{ $propOpt->name }} ({{ $propOpt->city }})</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">
                        @endif

                        <div class="mb-0">
                            <label class="form-label small mb-1 text-start d-block">Nombre de la temporada <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="nombre_temporada" placeholder="Ej. Temporada Alta Diciembre" required>
                            <div class="text-muted mt-1 text-start" style="font-size: 7.5px;">Aparecerá en el catálogo público: "Semana Santa", "Alta Verano"…</div>
                        </div>
                    </div>


                    {{-- 2 · VIGENCIA --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-calendar-range"></i> Vigencia</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Desde <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" name="fecha_inicio" required>
                            </div>
                            <div class="col-2 text-center pt-3">
                                <i class="bi bi-arrow-right text-muted"></i>
                            </div>
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Hasta <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" name="fecha_fin" required>
                            </div>
                        </div>
                        <div class="text-muted mt-1 text-start" style="font-size: 7.5px;">Fuera de este rango el inmueble usa su precio base</div>
                    </div>


                    {{-- 3 · PRECIOS --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-cash-coin"></i> Precios</div>

                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Por noche <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="precio_noche" placeholder="0.00" required>
                                </div>
                                <div class="text-muted mt-1 text-start" style="font-size: 7px;">Estándar</div>
                            </div>

                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Fin semana</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="precio_fin_semana" placeholder="0.00" required>
                                </div>
                                <div class="text-muted mt-1 text-start" style="font-size: 7px;">Vie y Sáb</div>
                            </div>

                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Mínimo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="precio_minimo_reserva" placeholder="0.00" required>
                                </div>
                                <div class="text-muted mt-1 text-start" style="font-size: 7px;">Reserva mín.</div>
                            </div>
                        </div>
                    </div>


                    {{-- 4 · LÍMITES Y ESTADO --}}
                    <div class="p-2.5 mb-0 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-sliders"></i> Límites y Estado</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Días máx.</label>
                                <input type="number" class="form-control form-control-sm" name="dias_maximos" placeholder="Ej. 30" min="1">
                                <div class="text-muted mt-1 text-start" style="font-size: 7px;">Vacío = sin límite</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label small mb-1 text-start d-block">Disponibilidad</label>
                                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                                    <div class="text-start w-100 me-2">
                                        <div class="fw-semibold" style="font-size: 9px;">Activa</div>
                                        <div class="text-muted" style="font-size: 7px;">Aplica al cotizar</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="addActive" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Se aplica según fechas</span>
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
{{-- MODAL 2B · EDITAR TARIFA                                         --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade tarifas-soft-modal" id="modalEditarTarifa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Motor Comercial · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tarifa_hint_title" style="font-size: 0.95rem;">Tarifa Comercial</h6>
                    <small class="text-muted" id="edit_tarifa_hint" style="font-size: 7.5px;">Selecciona una tarifa…</small>
                </div>
            </div>

            <form id="formEditarTarifa" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-tag"></i> Identificación</div>

                        @if ($modoGlobal)
                            <div class="mb-2">
                                <label class="form-label small mb-1 text-start d-block">Inmueble <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="edit_tarifa_inmueble_id" name="id_rsv_catalogo_inmueble" required>
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @isset($listaInmuebles)
                                        @foreach ($listaInmuebles as $propOpt)
                                            <option value="{{ $propOpt->id }}">{{ $propOpt->name }} ({{ $propOpt->city }})</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">
                        @endif

                        <div class="mb-0">
                            <label class="form-label small mb-1 text-start d-block">Nombre de la temporada <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="edit_tarifa_nombre" name="nombre_temporada" required>
                        </div>
                    </div>


                    {{-- 2 · VIGENCIA --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-calendar-range"></i> Vigencia</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Desde <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" id="edit_tarifa_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-2 text-center pt-3">
                                <i class="bi bi-arrow-right text-muted"></i>
                            </div>
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Hasta <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" id="edit_tarifa_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>


                    {{-- 3 · PRECIOS --}}
                    <div class="p-2.5 mb-2 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-cash-coin"></i> Precios</div>

                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Por noche <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="edit_tarifa_precio_noche" name="precio_noche" required>
                                </div>
                            </div>

                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Fin semana</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="edit_tarifa_precio_fin_semana" name="precio_fin_semana" required>
                                </div>
                            </div>

                            <div class="col-4">
                                <label class="form-label small mb-1 text-start d-block">Mínimo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="edit_tarifa_precio_minimo" name="precio_minimo_reserva" required>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- 4 · LÍMITES Y ESTADO --}}
                    <div class="p-2.5 mb-0 rounded border border-light-subtle" style="background-color: #fcfcfd;">
                        <div class="fw-semibold text-secondary mb-2 text-start" style="font-size: 11px;"><i class="bi bi-sliders"></i> Límites y Estado</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label small mb-1 text-start d-block">Días máx.</label>
                                <input type="number" class="form-control form-control-sm" id="edit_tarifa_dias_maximos" name="dias_maximos" min="1">
                                <div class="text-muted mt-1 text-start" style="font-size: 7px;">Vacío = sin límite</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label small mb-1 text-start d-block">Disponibilidad</label>
                                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border">
                                    <div class="text-start w-100 me-2">
                                        <div class="fw-semibold" style="font-size: 9px;">Activa</div>
                                        <div class="text-muted" style="font-size: 7px;">Pausar sin eliminar</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tarifa_active">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" id="edit_tarifa_hint_dates" style="font-size: 7.5px;"></span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- SCRIPT PARA LLENAR LOS CAMPOS DEL MODAL DE EDICIÓN --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonesEditar = document.querySelectorAll('.btn-editar-tarifa');
        botonesEditar.forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarTarifa');
                form.action = this.getAttribute('data-url-update');

                if (document.getElementById('edit_tarifa_inmueble_id')) {
                    document.getElementById('edit_tarifa_inmueble_id').value = this.getAttribute('data-inmueble-id');
                }
                document.getElementById('edit_tarifa_nombre').value = this.getAttribute('data-nombre-temporada');
                document.getElementById('edit_tarifa_inicio').value = this.getAttribute('data-fecha-inicio');
                document.getElementById('edit_tarifa_fin').value = this.getAttribute('data-fecha-fin');
                document.getElementById('edit_tarifa_precio_noche').value = this.getAttribute('data-precio-noche');
                document.getElementById('edit_tarifa_precio_fin_semana').value = this.getAttribute('data-precio-fin-semana');
                document.getElementById('edit_tarifa_precio_minimo').value = this.getAttribute('data-precio-minimo-reserva');
                document.getElementById('edit_tarifa_dias_maximos').value = this.getAttribute('data-dias-maximos');

                const activeCheck = document.getElementById('edit_tarifa_active');
                activeCheck.checked = this.getAttribute('data-active') === '1';

                /* Contexto del header/footer */
                const hintTitle = document.getElementById('edit_tarifa_hint_title');
                const hint      = document.getElementById('edit_tarifa_hint');
                const hintDates = document.getElementById('edit_tarifa_hint_dates');

                if (hintTitle) hintTitle.textContent = this.getAttribute('data-nombre-temporada') || 'Tarifa Comercial';

                if (hint) {
                    hint.innerHTML = 'Vigencia: <b>'
                        + (this.getAttribute('data-fecha-inicio') || '—')
                        + ' → '
                        + (this.getAttribute('data-fecha-fin') || '—')
                        + '</b>';
                }

                if (hintDates) {
                    const noche = parseFloat(this.getAttribute('data-precio-noche') || 0);
                    hintDates.innerHTML = 'Editando tarifa de <b style="color:#5d8a70;">$'
                        + noche.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                        + '</b> por noche';
                }
            });
        });
    });
</script>
