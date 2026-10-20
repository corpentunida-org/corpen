@php
    // Detectamos si estamos en la vista global o en un inmueble específico
    $modoGlobal = !isset($inmueble) || !$inmueble;
    $tarifasMostrar = [];

    // Cargamos los inmuebles con sus tarifas si no vienen definidos desde el controlador
    if (!isset($listaInmuebles)) {
        $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::with('tarifasTemporadas')->get();
    }

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
            foreach ($inmueble->tarifasTemporadas as $t) {
                $t->id_rsv_catalogo_inmueble = $inmueble->id;
                $t->nombre_inmueble_padre = $inmueble->name ?? 'Inmueble';
                $tarifasMostrar[] = $t;
            }
        }
    }

    // ═══ FIX ANTI-CONGELAMIENTO ═══
    // UID único por inclusión: si este partial se renderiza en más de un lugar
    // de la página, cada copia obtiene sus propios IDs de modal/campos/script.
    $tzUid = $tzUid ?? ('tz' . substr(md5(uniqid('', true)), 0, 8));
    $soloLectura = $soloLectura ?? false;
@endphp

<div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-cash-coin me-1" style="font-size: 8px; color: #5d8a70;"></i>
            {{ $modoGlobal ? 'Visión Global de Tarifas' : 'Tarifas y Temporadas del Inmueble' }}
        </h6>

        @if(empty($soloLectura))
            <button type="button"
                    class="btn btn-sm d-inline-flex align-items-center border"
                    style="gap: 4px; background: #f0f7f2; color: #3d7a5c; border-color: #d5e7dc !important; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px; transition: all .15s ease;"
                    onmouseover="this.style.background='#e3f1e9'; this.style.color='#34684e';"
                    onmouseout="this.style.background='#f0f7f2'; this.style.color='#3d7a5c';"
                    data-bs-toggle="modal" data-bs-target="#modalAddTarifa-{{ $tzUid }}">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Tarifa
            </button>
        @endif

    </div>

    <div class="table-responsive">

        <table class="table table-sm table-hover align-middle mb-0" style="--bs-table-hover-bg: #fafcfe;">

            <thead>
                <tr>
                    @if ($modoGlobal)
                        <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Inmueble</th>
                    @endif
                    <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Temporada</th>
                    <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Fechas</th>
                    <th class="text-end" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Noche</th>
                    <th class="text-end" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Fin Sem.</th>
                    <th class="text-end" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Mín. Reserva</th>
                    <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Días Máx.</th>
                    <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Estado</th>
                    @if(empty($soloLectura))
                        <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">·</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @if (!empty($tarifasMostrar) && count($tarifasMostrar) > 0)

                    @foreach ($tarifasMostrar as $tarifa)

                        <tr>

                            @if ($modoGlobal)
                                <td class="fw-semibold" style="font-size: 8px; color: #5d8a70; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                    <i class="bi bi-building me-1" style="font-size: 7.5px; opacity: .7;"></i>{{ $tarifa->nombre_inmueble_padre ?? 'N/A' }}
                                </td>
                            @endif

                            <td class="fw-semibold" style="font-size: 8px; color: #475569; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                {{ $tarifa->nombre_temporada }}
                            </td>

                            <td class="text-center" style="font-size: 8px; color: #a8b3c1; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px; font-variant-numeric: tabular-nums;">
                                {{ optional($tarifa->fecha_inicio)->format('d/m/Y') }} — {{ optional($tarifa->fecha_fin)->format('d/m/Y') }}
                            </td>

                            <td class="text-end fw-semibold" style="font-size: 8px; color: #5d8a70; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px; font-variant-numeric: tabular-nums;">
                                ${{ number_format($tarifa->precio_noche ?? 0, 2) }}
                            </td>

                            <td class="text-end" style="font-size: 8px; color: #64748b; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px; font-variant-numeric: tabular-nums;">
                                ${{ number_format($tarifa->precio_fin_semana ?? 0, 2) }}
                            </td>

                            <td class="text-end" style="font-size: 8px; color: #a8b3c1; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px; font-variant-numeric: tabular-nums;">
                                ${{ number_format($tarifa->precio_minimo_reserva ?? 0, 2) }}
                            </td>

                            <td class="text-center" style="font-size: 8px; color: #64748b; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                {{ $tarifa->dias_maximos ?? 'N/A' }}
                            </td>

                            <td class="text-center" style="border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                @if(isset($tarifa->active) && $tarifa->active)
                                    <span class="d-inline-block border" style="font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; color: #5d8a70; background: #f0f7f2; border-color: #d8e9de !important;">Activa</span>
                                @else
                                    <span class="d-inline-block border" style="font-size: 7.5px; font-weight: 600; padding: 0 7px; border-radius: 8px; line-height: 13px; color: #9aa5b1; background: #fafbfd; border-color: #eef2f6 !important;">Inactiva</span>
                                @endif
                            </td>

                            @if(empty($soloLectura))
                                <td class="text-center" style="border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                    <button type="button"
                                            class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-tarifa-{{ $tzUid }}"
                                            style="font-size: 8px; color: #3d7a5c; font-weight: 600; transition: color .15s ease;"
                                            onmouseover="this.style.color='#264c39';"
                                            onmouseout="this.style.color='#3d7a5c';"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTarifa-{{ $tzUid }}"
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
                            @endif
                        </tr>

                    @endforeach

                @else
                    <tr>
                        <td colspan="{{ $modoGlobal ? 9 : 8 }}" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px; border-bottom: 1px solid #f1f5f9;">
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
<div class="modal fade" id="modalAddTarifa-{{ $tzUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 5px; font-family: Calibri, 'Segoe UI', system-ui, sans-serif; font-size: 8px; line-height: 1.4; color: #64748b;">

            <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Motor Comercial
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 10.5px; color: #475569 !important;">Nueva Tarifa por Temporada</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b style="color: #c98a85;">*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST">
                @csrf

                <div class="modal-body" style="padding: 12px 14px;">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i> Identificación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        @if ($modoGlobal)
                            <div class="mb-2">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Inmueble <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm shadow-none border" name="id_rsv_catalogo_inmueble" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @isset($listaInmuebles)
                                        @foreach ($listaInmuebles as $propOpt)
                                            <option value="{{ $propOpt->id }}">{{ $propOpt->name }}{{ isset($propOpt->city) ? ' (' . $propOpt->city . ')' : '' }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id ?? '' }}">
                        @endif

                        <div class="mb-0">
                            <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Nombre de la temporada <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" name="nombre_temporada" placeholder="Ej. Temporada Alta Diciembre" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Aparecerá en el catálogo público: "Semana Santa", "Alta Verano"…</div>
                        </div>
                    </div>


                    {{-- 2 · VIGENCIA --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-calendar-range" style="font-size: 7.5px; opacity: .7;"></i> Vigencia
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Desde <span style="color: #c98a85;">*</span></label>
                                <input type="date" class="form-control form-control-sm shadow-none border" name="fecha_inicio" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                            <div class="col-2 text-center pt-3">
                                <i class="bi bi-arrow-right" style="color: #c3ccd6;"></i>
                            </div>
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Hasta <span style="color: #c98a85;">*</span></label>
                                <input type="date" class="form-control form-control-sm shadow-none border" name="fecha_fin" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                        </div>
                        <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Fuera de este rango el inmueble usa su precio base</div>
                    </div>


                    {{-- 3 · PRECIOS --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-cash-coin" style="font-size: 7.5px; opacity: .7;"></i> Precios
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Por noche <span style="color: #c98a85;">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" name="precio_noche" placeholder="0.00" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                                <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Estándar</div>
                            </div>

                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Fin semana</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" name="precio_fin_semana" placeholder="0.00" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                                <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Vie y Sáb</div>
                            </div>

                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Mínimo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" name="precio_minimo_reserva" placeholder="0.00" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                                <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Reserva mín.</div>
                            </div>
                        </div>
                    </div>


                    {{-- 4 · LÍMITES Y ESTADO --}}
                    <div class="p-2 mb-0 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-sliders" style="font-size: 7.5px; opacity: .7;"></i> Límites y Estado
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Días máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" name="dias_maximos" placeholder="Ej. 30" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Vacío = sin límite</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Disponibilidad</label>
                                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border" style="border-color: #f1f5f9 !important;">
                                    <div class="text-start w-100 me-2">
                                        <div class="fw-semibold" style="font-size: 9px; color: #475569;">Activa</div>
                                        <div style="font-size: 7px; color: #b3bcc7;">Aplica al cotizar</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="addActive-{{ $tzUid }}" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                    <span class="text-muted" style="font-size: 7.5px;">Se aplica según fechas</span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2B · EDITAR TARIFA                                         --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditarTarifa-{{ $tzUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm" style="border-color: #e9eef4 !important; border-radius: 5px; font-family: Calibri, 'Segoe UI', system-ui, sans-serif; font-size: 8px; line-height: 1.4; color: #64748b;">

            <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Motor Comercial · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tarifa_hint_title-{{ $tzUid }}" style="font-size: 10.5px; color: #475569 !important;">Tarifa Comercial</h6>
                    <small class="text-muted" id="edit_tarifa_hint-{{ $tzUid }}" style="font-size: 7.5px;">Selecciona una tarifa…</small>
                </div>
            </div>

            <form id="formEditarTarifa-{{ $tzUid }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding: 12px 14px;">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i> Identificación
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        @if ($modoGlobal)
                            <div class="mb-2">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Inmueble <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm shadow-none border" id="edit_tarifa_inmueble_id-{{ $tzUid }}" name="id_rsv_catalogo_inmueble" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @isset($listaInmuebles)
                                        @foreach ($listaInmuebles as $propOpt)
                                            <option value="{{ $propOpt->id }}">{{ $propOpt->name }}{{ isset($propOpt->city) ? ' (' . $propOpt->city . ')' : '' }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id ?? '' }}">
                        @endif

                        <div class="mb-0">
                            <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Nombre de la temporada <span style="color: #c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm shadow-none border" id="edit_tarifa_nombre-{{ $tzUid }}" name="nombre_temporada" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                        </div>
                    </div>


                    {{-- 2 · VIGENCIA --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-calendar-range" style="font-size: 7.5px; opacity: .7;"></i> Vigencia
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Desde <span style="color: #c98a85;">*</span></label>
                                <input type="date" class="form-control form-control-sm shadow-none border" id="edit_tarifa_inicio-{{ $tzUid }}" name="fecha_inicio" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                            <div class="col-2 text-center pt-3">
                                <i class="bi bi-arrow-right" style="color: #c3ccd6;"></i>
                            </div>
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Hasta <span style="color: #c98a85;">*</span></label>
                                <input type="date" class="form-control form-control-sm shadow-none border" id="edit_tarifa_fin-{{ $tzUid }}" name="fecha_fin" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                            </div>
                        </div>
                    </div>


                    {{-- 3 · PRECIOS --}}
                    <div class="p-2 mb-2 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-cash-coin" style="font-size: 7.5px; opacity: .7;"></i> Precios
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Por noche <span style="color: #c98a85;">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" id="edit_tarifa_precio_noche-{{ $tzUid }}" name="precio_noche" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                            </div>

                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Fin semana</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" id="edit_tarifa_precio_fin_semana-{{ $tzUid }}" name="precio_fin_semana" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                            </div>

                            <div class="col-4">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Mínimo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text border" style="background: #fafbfd; border-color: #e9eef4 !important; color: #5d8a70; font-size: 8px; font-weight: 600; padding-left: 6px; padding-right: 6px;">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm shadow-none border border-start-0" id="edit_tarifa_precio_minimo-{{ $tzUid }}" name="precio_minimo_reserva" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 3px 8px; padding-left: 4px;">
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- 4 · LÍMITES Y ESTADO --}}
                    <div class="p-2 mb-0 rounded border" style="background-color: #fcfcfd; border-color: #e9eef4 !important;">
                        <div class="d-flex align-items-center mb-1 text-start" style="gap: 5px; font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">
                            <i class="bi bi-sliders" style="font-size: 7.5px; opacity: .7;"></i> Límites y Estado
                            <div class="flex-grow-1" style="height: 1px; background: #f1f5f9;"></div>
                        </div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Días máx.</label>
                                <input type="number" class="form-control form-control-sm shadow-none border" id="edit_tarifa_dias_maximos-{{ $tzUid }}" name="dias_maximos" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Vacío = sin límite</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Disponibilidad</label>
                                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border" style="border-color: #f1f5f9 !important;">
                                    <div class="text-start w-100 me-2">
                                        <div class="fw-semibold" style="font-size: 9px; color: #475569;">Activa</div>
                                        <div style="font-size: 7px; color: #b3bcc7;">Pausar sin eliminar</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="edit_tarifa_active-{{ $tzUid }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                    <span class="text-muted" id="edit_tarifa_hint_dates-{{ $tzUid }}" style="font-size: 7.5px;"></span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- SCRIPT PARA LLENAR LOS CAMPOS DEL MODAL DE EDICIÓN --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonesEditar = document.querySelectorAll('.btn-editar-tarifa-{{ $tzUid }}');
        botonesEditar.forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarTarifa-{{ $tzUid }}');
                form.action = this.getAttribute('data-url-update');

                if (document.getElementById('edit_tarifa_inmueble_id-{{ $tzUid }}')) {
                    document.getElementById('edit_tarifa_inmueble_id-{{ $tzUid }}').value = this.getAttribute('data-inmueble-id');
                }
                document.getElementById('edit_tarifa_nombre-{{ $tzUid }}').value = this.getAttribute('data-nombre-temporada');
                document.getElementById('edit_tarifa_inicio-{{ $tzUid }}').value = this.getAttribute('data-fecha-inicio');
                document.getElementById('edit_tarifa_fin-{{ $tzUid }}').value = this.getAttribute('data-fecha-fin');
                document.getElementById('edit_tarifa_precio_noche-{{ $tzUid }}').value = this.getAttribute('data-precio-noche');
                document.getElementById('edit_tarifa_precio_fin_semana-{{ $tzUid }}').value = this.getAttribute('data-precio-fin-semana');
                document.getElementById('edit_tarifa_precio_minimo-{{ $tzUid }}').value = this.getAttribute('data-precio-minimo-reserva');
                document.getElementById('edit_tarifa_dias_maximos-{{ $tzUid }}').value = this.getAttribute('data-dias-maximos');

                const activeCheck = document.getElementById('edit_tarifa_active-{{ $tzUid }}');
                activeCheck.checked = this.getAttribute('data-active') === '1';

                /* Contexto del header/footer */
                const hintTitle = document.getElementById('edit_tarifa_hint_title-{{ $tzUid }}');
                const hint      = document.getElementById('edit_tarifa_hint-{{ $tzUid }}');
                const hintDates = document.getElementById('edit_tarifa_hint_dates-{{ $tzUid }}');

                if (hintTitle) hintTitle.textContent = this.getAttribute('data-nombre-temporada') || 'Tarifa Comercial';

                if (hint) {
                    hint.innerHTML = 'Vigencia: <b style="color: #5d8a70; font-weight: 600;">'
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
