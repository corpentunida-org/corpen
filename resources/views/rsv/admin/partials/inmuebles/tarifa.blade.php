{{-- resources/views/rsv/admin/partials/inmuebles/tarifa.blade.php · TAR-v1.2-DBG · NIVEL 3 (tarifas y temporadas) --}}
@php
    $soloLectura = !empty($soloLectura);

    $modoGlobal = (!isset($inmueble) || !$inmueble) && (empty($inmuebleId) || $inmuebleId === 'todos');

    $tarInmHidden = (isset($inmueble) && $inmueble)
        ? $inmueble->id
        : ((isset($inmuebleId) && $inmuebleId && $inmuebleId !== 'todos') ? $inmuebleId : null);

    $tarifasMostrar = collect([]);

    if (isset($tarifas) && $tarifas) {
        $tarifasMostrar = collect($tarifas);
    } elseif ($modoGlobal) {
        if (!isset($listaInmuebles)) {
            try {
                $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::with('tarifasTemporadas')->get();
            } catch (\Throwable $e) {
                $listaInmuebles = collect([]);
            }
        }
        $tmpArr = [];
        foreach ($listaInmuebles as $prop) {
            if (isset($prop->tarifasTemporadas) && $prop->tarifasTemporadas) {
                foreach ($prop->tarifasTemporadas as $t) {
                    $t->id_rsv_catalogo_inmueble = $prop->id;
                    $t->nombre_inmueble_padre = $prop->name ?? 'Inmueble';
                    $tmpArr[] = $t;
                }
            }
        }
        $tarifasMostrar = collect($tmpArr);
    } else {
        if (isset($inmueble) && $inmueble && isset($inmueble->tarifasTemporadas)) {
            $tmpArr = [];
            foreach ($inmueble->tarifasTemporadas as $t) {
                $t->id_rsv_catalogo_inmueble = $inmueble->id;
                $t->nombre_inmueble_padre = $inmueble->name ?? 'Inmueble';
                $tmpArr[] = $t;
            }
            $tarifasMostrar = collect($tmpArr);
        }
    }

    $tarNombresInmuebles = collect([]);
    if ($modoGlobal) {
        try {
            $idsPadres = $tarifasMostrar->pluck('id_rsv_catalogo_inmueble')->filter()->unique()->values();
            if ($idsPadres->isNotEmpty()) {
                $tarNombresInmuebles = \App\Models\Rsv\CatalogoInmueble::whereIn('id', $idsPadres)->pluck('name', 'id');
            }
        } catch (\Throwable $e) {
            $tarNombresInmuebles = collect([]);
        }
    }

    if ($modoGlobal && !isset($listaInmuebles)) {
        try {
            $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::query()->orderBy('name')->get(['id', 'name', 'city']);
        } catch (\Throwable $e) {
            $listaInmuebles = collect([]);
        }
    }

    $tzUid = $tzUid ?? ('tz' . substr(md5(uniqid('', true)), 0, 8));

    $cntTarTotal   = $tarifasMostrar->count();
    $cntTarActivas = $tarifasMostrar->filter(fn ($t) => !empty($t->active))->count();
    $cntTarInact   = max(0, $cntTarTotal - $cntTarActivas);

    $tarDbg = [
        'marca'       => 'TAR-v1.2-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.tarifa',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? null),
        'total'       => $cntTarTotal,
        'activas'     => $cntTarActivas,
        'inactivas'   => $cntTarInact,
        'selectorInm' => isset($listaInmuebles) && $listaInmuebles ? $listaInmuebles->count() : 0,
        'soloLectura' => $soloLectura,
        'uid'         => $tzUid,
        'validacion'  => (isset($errors) && $errors->any()) ? $errors->all() : [],
        'errores'     => [],
    ];
@endphp

{{-- TAR-v1.2 · Tab DBG textual + LED + Panel LOCAL (fuera de la raíz: mueren con el fragmento · NO global) --}}
<div id="tarDbgToggle" title="Debug Tarifas (TAR-v1.2-DBG)">
    <span class="tar-dbg-led" id="tarDbgLed"></span>DBG<span class="tar-dbg-badge" id="tarDbgBadge">0</span>
</div>
<div id="tarDbgPanel" role="dialog" aria-label="Panel de debug de Tarifas">
    <div class="tar-dbg-header" id="tarDbgHeader">
        <span>DEBUG · Tarifas (N3) · TAR-v1.2-DBG</span>
        <span class="tar-dbg-controls">
            <button type="button" class="tar-dbg-btn" id="tarDbgRefresh" title="Refrescar fragmento de tarifas">↻ Frag</button>
            <button type="button" class="tar-dbg-btn" id="tarDbgCopy">Copiar</button>
            <button type="button" class="tar-dbg-btn" id="tarDbgClear">Limpiar</button>
            <button type="button" class="tar-dbg-btn" id="tarDbgClose">✕</button>
        </span>
    </div>
    <div id="tarDbgBody"></div>
</div>

<div id="tarRoot" class="tar-root" data-frag="{{ route('rsv.admin.inmuebles.tarifa.fragmento') }}" data-inmueble-id="{{ $tarDbg['inmuebleId'] ?? 'todos' }}">

    <style>
        .tar-root .tar-filter-chip {
            border: 1px solid #e9eef4;
            background: #fff;
            color: #64748b;
            font-size: 7.5px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all .15s ease;
            line-height: 1.3;
        }
        .tar-root .tar-filter-chip:hover { color: #334155; background: #f8fafc; }
        .tar-root .tar-filter-chip.active {
            background: #f0f7f2;
            color: #3d7a5c;
            border-color: #d5e7dc;
            font-weight: 700;
        }
        .tar-root .tar-search-inp {
            font-size: 8px;
            color: #334155;
            background: #fafbfd;
            border: 1px solid #e9eef4;
            border-radius: 4px;
            padding: 2px 8px;
            outline: 0;
            width: 155px;
            transition: all .15s ease;
        }
        .tar-root .tar-search-inp:focus {
            background: #fff;
            border-color: #d5e7dc;
            width: 190px;
        }
        .tar-root .tar-vigencia-pill {
            display: inline-block;
            margin-top: 4px;
            font-size: 7.5px;
            font-weight: 700;
            color: #3d7a5c;
            background: #f0f7f2;
            border: 1px solid #d5e7dc;
            border-radius: 4px;
            padding: 2px 7px;
        }
        .tar-root .tar-vigencia-pill.err {
            color: #b45f5a;
            background: #fdf5f4;
            border-color: #f0dcd8;
        }
    </style>

    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">

            <div class="d-flex align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                    <i class="bi bi-cash-coin me-1" style="font-size: 8px; color: #5d8a70;"></i>
                    {{ $modoGlobal ? 'Visión Global de Tarifas' : 'Tarifas y Temporadas del Inmueble' }}
                    <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">TAR-v1.2-DBG</span>
                </h6>

                @if($cntTarTotal > 0)
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="tar-filter-chip active" data-tar-filter="all">
                            Todas ({{ $cntTarTotal }})
                        </button>
                        <button type="button" class="tar-filter-chip" data-tar-filter="1">
                            Activas ({{ $cntTarActivas }})
                        </button>
                        <button type="button" class="tar-filter-chip" data-tar-filter="0">
                            Inactivas ({{ $cntTarInact }})
                        </button>
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                @if($cntTarTotal > 0)
                    <input type="text" class="tar-search-inp js-tar-search" placeholder="Buscar temporada{{ $modoGlobal ? ' o inmueble' : '' }}…">
                @endif

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
                        <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Rsv. Máx/Año</th>
                        <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">Estado</th>
                        @if(empty($soloLectura))
                            <th class="text-center" style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; background-color: #fafbfd; border-bottom: 1px solid #f1f5f9 !important; padding: 2px 7px; height: 20px;">·</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @if (!empty($tarifasMostrar) && count($tarifasMostrar) > 0)

                        @foreach ($tarifasMostrar as $tarifa)
                            @php
                                $nomPad = $tarifa->nombre_inmueble_padre ?? (($tarNombresInmuebles->isNotEmpty() && $tarifa->id_rsv_catalogo_inmueble && $tarNombresInmuebles->has($tarifa->id_rsv_catalogo_inmueble)) ? $tarNombresInmuebles[$tarifa->id_rsv_catalogo_inmueble] : 'N/A');
                                $actTar = !empty($tarifa->active);
                                $searchTar = mb_strtolower(($tarifa->nombre_temporada ?? '') . ' ' . $nomPad . ' ' . ($actTar ? 'activa' : 'inactiva'));
                            @endphp

                            <tr class="js-tar-row" data-tar-active="{{ $actTar ? '1' : '0' }}" data-tar-search="{{ $searchTar }}">

                                @if ($modoGlobal)
                                    <td class="fw-semibold" style="font-size: 8px; color: #5d8a70; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                        <i class="bi bi-building me-1" style="font-size: 7.5px; opacity: .7;"></i>{{ $nomPad }}
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

                                <td class="text-center" style="font-size: 8px; color: #64748b; border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                    {{ $tarifa->max_reservas_anuales_cliente ?? 'N/A' }}
                                </td>

                                <td class="text-center" style="border-bottom: 1px solid #f1f5f9; border-top: 0; padding: 2px 7px; height: 20px;">
                                    @if($actTar)
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
                                                data-tar-editar="1"
                                                data-url-update="{{ route('rsv.tarifas-temporadas.update', $tarifa->id) }}"
                                                data-inmueble-id="{{ $tarifa->id_rsv_catalogo_inmueble }}"
                                                data-nombre-temporada="{{ $tarifa->nombre_temporada }}"
                                                data-fecha-inicio="{{ optional($tarifa->fecha_inicio)->format('Y-m-d') }}"
                                                data-fecha-fin="{{ optional($tarifa->fecha_fin)->format('Y-m-d') }}"
                                                data-precio-noche="{{ $tarifa->precio_noche }}"
                                                data-precio-fin-semana="{{ $tarifa->precio_fin_semana }}"
                                                data-precio-minimo-reserva="{{ $tarifa->precio_minimo_reserva }}"
                                                data-dias-maximos="{{ $tarifa->dias_maximos }}"
                                                data-max-reservas="{{ $tarifa->max_reservas_anuales_cliente }}"
                                                data-active="{{ $actTar ? 1 : 0 }}">
                                            Editar
                                        </button>
                                    </td>
                                @endif
                            </tr>

                        @endforeach

                        <tr class="js-tar-empty-filter" style="display:none;">
                            <td colspan="{{ $modoGlobal ? 10 : 9 }}" class="text-center py-3" style="color: #94a3b8; font-size: 8px; border-bottom: 1px solid #f1f5f9;">
                                <i class="bi bi-funnel me-1"></i> Ninguna tarifa coincide con el filtro seleccionado.
                            </td>
                        </tr>

                    @else
                        <tr>
                            <td colspan="{{ $modoGlobal ? 10 : 9 }}" class="text-center py-3" style="color: #b3bcc7; font-size: 8.5px; border-bottom: 1px solid #f1f5f9;">
                                No hay tarifas registradas.
                            </td>
                        </tr>
                    @endif
                </tbody>

            </table>

        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2A · NUEVA TARIFA · TAR-v1.2-DBG                        --}}
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

                <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST" data-tar-form="crear">
                    @csrf

                    <div class="modal-body" style="padding: 12px 14px;">

                        <div class="tar-form-err" style="display: none; background: #fdf5f4; color: #b45f5a; border: 1px solid #f0dcd8; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

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
                                <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $tarInmHidden }}">
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
                                    <input type="date" class="form-control form-control-sm shadow-none border js-tar-fecha-ini" name="fecha_inicio" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                </div>
                                <div class="col-2 text-center pt-3">
                                    <i class="bi bi-arrow-right" style="color: #c3ccd6;"></i>
                                </div>
                                <div class="col-5">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Hasta <span style="color: #c98a85;">*</span></label>
                                    <input type="date" class="form-control form-control-sm shadow-none border js-tar-fecha-fin" name="fecha_fin" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mt-1">
                                <span style="font-size: 7px; color: #c3ccd6;">Fuera de este rango el inmueble usa su precio base</span>
                                <span class="tar-vigencia-pill d-none js-tar-duracion"></span>
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
                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Días máx.</label>
                                    <input type="number" class="form-control form-control-sm shadow-none border" name="dias_maximos" placeholder="Ej. 30" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Vacío = sin límite</div>
                                </div>

                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Rsv. máx/año</label>
                                    <input type="number" class="form-control form-control-sm shadow-none border" name="max_reservas_anuales_cliente" placeholder="Ej. 2" min="0" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Por cliente al año</div>
                                </div>

                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Disponibilidad</label>
                                    <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border" style="border-color: #f1f5f9 !important;">
                                        <div class="text-start w-100 me-1">
                                            <div class="fw-semibold" style="font-size: 9px; color: #475569;">Activa</div>
                                            <div style="font-size: 7px; color: #b3bcc7; line-height: 1.1;">Aplica al cotizar</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="active" value="0">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="addActive-{{ $tzUid }}" checked style="margin-left:-1.5em;">
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
    {{-- MODAL 2B · EDITAR TARIFA · TAR-v1.2-DBG                        --}}
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
                        <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_tarifa_hint_title-{{ $tzUid }}" data-tar-hint="title" style="font-size: 10.5px; color: #475569 !important;">Tarifa Comercial</h6>
                        <small class="text-muted" id="edit_tarifa_hint-{{ $tzUid }}" data-tar-hint="vigencia" style="font-size: 7.5px;">Selecciona una tarifa…</small>
                    </div>
                </div>

                <form id="formEditarTarifa-{{ $tzUid }}" method="POST" data-tar-form="editar">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">

                        <div class="tar-form-err" style="display: none; background: #fdf5f4; color: #b45f5a; border: 1px solid #f0dcd8; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; margin-bottom: 8px;"></div>

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
                                <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $tarInmHidden }}">
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
                                    <input type="date" class="form-control form-control-sm shadow-none border js-tar-fecha-ini" id="edit_tarifa_inicio-{{ $tzUid }}" name="fecha_inicio" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                </div>
                                <div class="col-2 text-center pt-3">
                                    <i class="bi bi-arrow-right" style="color: #c3ccd6;"></i>
                                </div>
                                <div class="col-5">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Hasta <span style="color: #c98a85;">*</span></label>
                                    <input type="date" class="form-control form-control-sm shadow-none border js-tar-fecha-fin" id="edit_tarifa_fin-{{ $tzUid }}" name="fecha_fin" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-1">
                                <span class="tar-vigencia-pill d-none js-tar-duracion"></span>
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
                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Días máx.</label>
                                    <input type="number" class="form-control form-control-sm shadow-none border" id="edit_tarifa_dias_maximos-{{ $tzUid }}" name="dias_maximos" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Vacío = sin límite</div>
                                </div>

                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Rsv. máx/año</label>
                                    <input type="number" class="form-control form-control-sm shadow-none border" id="edit_tarifa_max_reservas-{{ $tzUid }}" name="max_reservas_anuales_cliente" min="0" style="font-size: 9.5px; color: #475569; border-color: #e9eef4 !important; border-radius: 3px; padding: 3px 8px;">
                                    <div class="mt-1 text-start" style="font-size: 7px; color: #c3ccd6;">Por cliente al año</div>
                                </div>

                                <div class="col-4">
                                    <label class="form-label d-block text-start mb-1" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8;">Disponibilidad</label>
                                    <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border" style="border-color: #f1f5f9 !important;">
                                        <div class="text-start w-100 me-1">
                                            <div class="fw-semibold" style="font-size: 9px; color: #475569;">Activa</div>
                                            <div style="font-size: 7px; color: #b3bcc7; line-height: 1.1;">Pausar sin borrar</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="active" value="0">
                                            <input class="form-check-input mt-0" type="checkbox" role="switch" name="active" value="1" id="edit_tarifa_active-{{ $tzUid }}" style="margin-left:-1.5em;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important; padding: 9px 14px;">
                        <span class="text-muted" id="edit_tarifa_hint_dates-{{ $tzUid }}" data-tar-hint="precio" style="font-size: 7.5px;"></span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2 d-flex align-items-center gap-1" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
    /* ═══════════════ TAR-v1.2-DBG · tarifa · NIVEL 3 ═══════════════ */
    (function () {
        var root = (document.currentScript && document.currentScript.closest)
            ? document.currentScript.closest('.tar-root')
            : document.getElementById('tarRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var SRVDBG = @json($tarDbg);

        /* ═══ 1) PANEL DE DEBUG LOCAL (estilo terminal TIP-v1.3) ═══ */
        var DBG = (window.TarDbg && window.TarDbg.alive && window.TarDbg.alive())
            ? window.TarDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('tarDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'tarDbgStyle';
                    st.textContent = [
                        '#tarDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#tarDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.tar-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.tar-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:tarDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes tarDbgBlink{50%{opacity:.35;}}',
                        '.tar-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#tarDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.tar-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.tar-dbg-controls{display:flex;gap:4px;}',
                        '.tar-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.tar-dbg-btn:hover{background:#475569;}',
                        '#tarDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.tar-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.tar-dbg-time{color:#64748b;margin-right:4px;}',
                        '.tar-dbg-info{color:#93c5fd;}.tar-dbg-ok{color:#86efac;}',
                        '.tar-dbg-warn{color:#fde68a;}.tar-dbg-err{color:#fca5a5;}',
                        '.tar-dbg-net{color:#c4b5fd;}.tar-dbg-debug{color:#94a3b8;}',
                        '#tarToastWrap{position:fixed;left:16px;bottom:16px;z-index:1092;display:flex;',
                        'flex-direction:column;gap:6px;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    var rootDbg = document.getElementById('tarRoot');
                    if (!rootDbg) { return; }
                    if (!$('tarDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'tarDbgToggle';
                        t.title = 'Debug Tarifas (TAR-v1.2-DBG)';
                        t.innerHTML = '<span class="tar-dbg-led" id="tarDbgLed"></span>DBG<span class="tar-dbg-badge" id="tarDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('tarDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'tarDbgPanel';
                        p.innerHTML = '<div class="tar-dbg-header" id="tarDbgHeader">' +
                            '<span>DEBUG · Tarifas (N3) · TAR-v1.2-DBG</span>' +
                            '<span class="tar-dbg-controls">' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgRefresh">↻ Frag</button>' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgCopy">Copiar</button>' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgClear">Limpiar</button>' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgClose">✕</button>' +
                            '</span></div><div id="tarDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle', 'psDbgToggle', 'trxDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; }
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('tarDbgToggle'), p = $('tarDbgPanel');
                    if (t) { t.style.bottom = '0px'; }
                    if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; }
                }
                function hora() {
                    var d = new Date();
                    function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                    return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
                }
                var api = {
                    log: function (txt, nivel) {
                        var b = $('tarDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'tar-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="tar-dbg-time">[' + hora() + ']</span>' +
                            '<span class="tar-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('tarDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('tarDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('tarDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('tarDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('tarDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('tarDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('tarDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('tarDbgBody'); if (b) { b.innerHTML = ''; } },
                    copy: function () {
                        var b = $('tarDbgBody'); if (!b) { return; }
                        var txt = '=== DEBUG tarifa TAR-v1.2-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('log copiado al portapapeles', 'ok');
                            });
                        }
                    },
                    ensure: function () { asegurarNodos(); },
                    alive: function () { return !!$('tarDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                window.TarDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure();

        if (window.__tarDbgDocHandler) { document.removeEventListener('click', window.__tarDbgDocHandler); }
        window.__tarDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#tarDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#tarDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#tarDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#tarDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#tarDbgRefresh')) {
                e.stopPropagation();
                recargarContexto('manual-btn-dbg');
                return;
            }

            // Chips de filtro de estado (Todas / Activas / Inactivas)
            var chip = t.closest('.tar-root .tar-filter-chip');
            if (chip) {
                var sc = chip.closest('.tar-root');
                if (sc) {
                    sc.querySelectorAll('.tar-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                    chip.classList.add('active');
                    aplicarFiltroTarifas(sc, true);
                }
            }
        };
        document.addEventListener('click', window.__tarDbgDocHandler);

        if (window.__tarDbgDblHandler) { document.removeEventListener('dblclick', window.__tarDbgDblHandler); }
        window.__tarDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#tarDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__tarDbgDblHandler);

        DBG.log('TAR-v1.2-DBG activo · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · tarifas=' + (SRVDBG ? SRVDBG.total : '?') +
            ' (activas=' + (SRVDBG ? SRVDBG.activas : 0) + ', inactivas=' + (SRVDBG ? SRVDBG.inactivas : 0) + ')', 'ok');

        /* ═══ FILTRO EN VIVO DE TABLA DE TARIFAS ═══ */
        function aplicarFiltroTarifas(scope, logEv) {
            var sc = scope || root;
            if (!sc) return;
            var chipAct = sc.querySelector('.tar-filter-chip.active');
            var filtroAct = chipAct ? chipAct.getAttribute('data-tar-filter') : 'all';
            var q = (sc.querySelector('.js-tar-search')?.value || '').toLowerCase().trim();
            var visibles = 0;

            var filas = sc.querySelectorAll('.js-tar-row');
            filas.forEach(function (tr) {
                var act  = tr.getAttribute('data-tar-active') || '0';
                var blob = tr.getAttribute('data-tar-search') || '';
                var okEst = (filtroAct === 'all') || (act === filtroAct);
                var okQ   = !q || blob.indexOf(q) !== -1;
                var show  = okEst && okQ;
                tr.style.display = show ? '' : 'none';
                if (show) visibles++;
            });

            var emptyRow = sc.querySelector('.js-tar-empty-filter');
            if (emptyRow && filas.length > 0) {
                emptyRow.style.display = visibles === 0 ? '' : 'none';
            }
            if (logEv) {
                DBG.log('Filtro tarifas · estado=' + filtroAct + ' · q="' + q + '" · visibles=' + visibles + '/' + filas.length, 'info');
            }
        }

        /* ═══ CALCULAR DURACIÓN DE VIGENCIA EN MODALES DE TARIFA ═══ */
        function actualizarDuracionModal(form) {
            if (!form) return;
            var iniInp = form.querySelector('.js-tar-fecha-ini');
            var finInp = form.querySelector('.js-tar-fecha-fin');
            var pill   = form.querySelector('.js-tar-duracion');
            if (!iniInp || !finInp || !pill) return;

            var vIni = iniInp.value;
            var vFin = finInp.value;
            if (!vIni || !vFin) {
                pill.classList.add('d-none');
                return;
            }
            var d1 = new Date(vIni + 'T00:00:00');
            var d2 = new Date(vFin + 'T00:00:00');
            var diffDias = Math.round((d2 - d1) / 86400000) + 1;

            pill.classList.remove('d-none');
            if (diffDias <= 0) {
                pill.classList.add('err');
                pill.textContent = '⚠ "Hasta" debe ser igual o posterior a "Desde"';
            } else {
                pill.classList.remove('err');
                pill.textContent = 'Vigencia: ' + diffDias + ' día(s)';
            }
        }

        if (window.__tarInputHandler) { document.removeEventListener('input', window.__tarInputHandler); }
        window.__tarInputHandler = function (e) {
            var t = e.target;
            if (!t) return;
            if (t.classList && t.classList.contains('js-tar-search')) {
                var sc = t.closest('.tar-root');
                aplicarFiltroTarifas(sc, false);
                return;
            }
            if (t.classList && (t.classList.contains('js-tar-fecha-ini') || t.classList.contains('js-tar-fecha-fin'))) {
                actualizarDuracionModal(t.closest('form'));
            }
        };
        document.addEventListener('input', window.__tarInputHandler);

        if (window.__tarChangeHandler) { document.removeEventListener('change', window.__tarChangeHandler); }
        window.__tarChangeHandler = function (e) {
            var t = e.target;
            if (t && t.classList && (t.classList.contains('js-tar-fecha-ini') || t.classList.contains('js-tar-fecha-fin'))) {
                actualizarDuracionModal(t.closest('form'));
            }
        };
        document.addEventListener('change', window.__tarChangeHandler);

        /* ═══ HELPERS DE SWAP ═══ */
        function tarRunScripts(scope) {
            var scripts = scope.querySelectorAll('script');
            for (var i = 0; i < scripts.length; i++) {
                var viejo = scripts[i];
                var nuevo = document.createElement('script');
                for (var j = 0; j < viejo.attributes.length; j++) {
                    nuevo.setAttribute(viejo.attributes[j].name, viejo.attributes[j].value);
                }
                nuevo.textContent = viejo.textContent;
                viejo.parentNode.replaceChild(nuevo, viejo);
            }
        }
        function extraerErrorLaravel(html) {
            var m = html.match(/<title[^>]*>([^<]+)<\/title>/i);
            if (m) { return m[1]; }
            m = html.match(/<h1[^>]*>([\s\S]{0,160}?)<\/h1>/i);
            if (m) { return m[1].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim(); }
            return '';
        }
        function escapar(txt) {
            return String(txt).replace(/[<>&]/g, function (c) {
                return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;');
            });
        }
        function cerrarModalesDelRoot() {
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) { return; }
            var abiertas = root.querySelectorAll('.modal.show');
            for (var i = 0; i < abiertas.length; i++) {
                try { bootstrap.Modal.getOrCreateInstance(abiertas[i]).hide(); } catch (e7) {}
            }
        }
        function limpiarResiduosModal() {
            if (document.querySelector('.modal.show')) { return; }
            var fondos = document.querySelectorAll('.modal-backdrop');
            for (var i = 0; i < fondos.length; i++) {
                if (fondos[i].parentNode) { fondos[i].parentNode.removeChild(fondos[i]); }
            }
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
        function tarSwapFragmento(html) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            var nuevo = tmp.querySelector('.tar-root');
            if (!nuevo) { throw new Error('el fragmento no trajo la raíz de tarifas'); }
            var actual = (root && root.isConnected) ? root : (document.querySelector('.tar-root') || document.getElementById('tarRoot'));
            if (!actual) { throw new Error('la raíz de tarifas ya no está en el DOM'); }
            cerrarModalesDelRoot();
            actual.replaceWith(nuevo);
            root = nuevo;
            tarRunScripts(nuevo);
            limpiarResiduosModal();
        }
        function tarCargar(url, motivo) {
            if (!FRAG) { return; }
            var t0 = performance.now();
            DBG.log('GET Fragmento (' + (motivo || 'sync') + ') → ' + url, 'net');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (redirigido al login)');
                }
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + ' (' + ms + 'ms)' + (det ? ' · ' + det : ''));
                    });
                }
                return r.text().then(function (html) { return { html: html, ms: ms }; });
            }).then(function (res) {
                tarSwapFragmento(res.html);
                DBG.log('Tarifas actualizadas (' + res.ms + 'ms · ' + res.html.length + ' bytes)', 'ok');
            }).catch(function (err) {
                DBG.log('⚠ Recarga de tarifas falló: ' + err.message, 'err');
                DBG.open();
            });
        }

        function tarToast(txt, nivel) {
            var wrap = document.getElementById('tarToastWrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'tarToastWrap';
                document.body.appendChild(wrap);
            }
            var t = document.createElement('div');
            var colores = {
                ok: 'color:#86efac;border-color:#14532d;',
                err: 'color:#fca5a5;border-color:#7f1d1d;',
                info: 'color:#93c5fd;border-color:#1e3a8a;'
            };
            t.setAttribute('style', 'font-family:inherit;font-size:11px;padding:8px 12px;border-radius:8px;' +
                'border:1px solid;background:#0f172a;box-shadow:0 6px 18px rgba(0,0,0,.25);max-width:340px;' +
                (colores[nivel] || colores.info));
            t.textContent = txt;
            wrap.appendChild(t);
            setTimeout(function () { if (t.parentNode) { t.parentNode.removeChild(t); } }, 3800);
        }

        function recargarContexto(motivo) {
            if (!FRAG) { return; }
            var inm = (SRVDBG && SRVDBG.inmuebleId) ? SRVDBG.inmuebleId : 'todos';
            var sep = FRAG.indexOf('?') === -1 ? '?' : '&';
            tarCargar(FRAG + sep + 'inmueble_id=' + encodeURIComponent(inm || 'todos') + '&_t=' + Date.now(), motivo || 'contexto');
        }
        window.tarReloadFragment = recargarContexto;

        /* ═══ FORMULARIOS POR AJAX · crear / editar ═══ */
        if (window.__tarFormHandler) { document.removeEventListener('submit', window.__tarFormHandler); }
        window.__tarFormHandler = function (e) {
            var form = e.target;
            if (!form || !form.closest || !form.hasAttribute('data-tar-form')) { return; }
            e.preventDefault();

            var caja = form.querySelector('.tar-form-err');
            if (caja) { caja.style.display = 'none'; caja.innerHTML = ''; }

            // Validación rápida de rango de fechas antes de ir al servidor
            var fIni = form.querySelector('[name="fecha_inicio"]')?.value || '';
            var fFin = form.querySelector('[name="fecha_fin"]')?.value || '';
            if (fIni && fFin && fFin < fIni) {
                if (caja) {
                    caja.innerHTML = '<div>• La fecha "Hasta" no puede ser menor que la fecha "Desde".</div>';
                    caja.style.display = 'block';
                }
                DBG.log('⚠ Validación cliente: fecha_fin (' + fFin + ') < fecha_inicio (' + fIni + ')', 'warn');
                return;
            }

            var tipo = form.getAttribute('data-tar-form');
            var btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; }
            var t0 = performance.now();
            DBG.log('POST (' + tipo + ') → ' + (form.getAttribute('action') || '(sin action)'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                var ms = Math.round(performance.now() - t0);
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (redirigido al login)');
                }
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { status: r.status, ok: r.ok, ms: ms, json: j }; });
                }
                return r.text().then(function (t) { return { status: r.status, ok: r.ok, ms: ms, html: t }; });
            }).then(function (res) {
                if (res.status === 422) {
                    var errs = (res.json && res.json.errors) ? res.json.errors : {};
                    var msgs = [];
                    for (var k in errs) { if (Object.prototype.hasOwnProperty.call(errs, k)) { msgs = msgs.concat(errs[k]); } }
                    if (!msgs.length && res.json && res.json.message) { msgs.push(res.json.message); }
                    if (!msgs.length && res.html) {
                        var det422 = extraerErrorLaravel(res.html);
                        if (det422) { msgs.push(det422); }
                    }
                    if (caja) {
                        caja.innerHTML = msgs.map(function (m) { return '<div>• ' + escapar(m) + '</div>'; }).join('');
                        caja.style.display = msgs.length ? 'block' : 'none';
                    }
                    DBG.log('⚠ Validación 422 (' + res.ms + 'ms): ' + (msgs.join(' | ') || 'sin detalle'), 'err');
                    tarToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + ' (' + res.ms + 'ms)' + (det ? ' · ' + det : ''));
                }
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                tarToast(tipo === 'crear' ? 'Tarifa creada · actualizando tabla…' : 'Cambios guardados · actualizando tabla…', 'ok');
                DBG.log('Guardado OK (' + tipo + ' · ' + res.ms + 'ms) → refrescando fragmento', 'ok');
                recargarContexto('post-' + tipo);
            }).catch(function (err) {
                DBG.log('⚠ Guardado falló (' + err.message + ')', 'err');
                tarToast('No se pudo guardar · ' + err.message, 'err');
                DBG.open();
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__tarFormHandler);

        /* ═══ LLENADO DEL MODAL DE EDITAR · delegado en document (capture) ═══ */
        if (window.__tarFillHandler) { document.removeEventListener('click', window.__tarFillHandler, true); }
        window.__tarFillHandler = function (e) {
            var btn = (e.target && e.target.closest) ? e.target.closest('[data-tar-editar]') : null;
            if (!btn) { return; }
            var selModal = btn.getAttribute('data-bs-target');
            var modal = selModal ? document.querySelector(selModal) : null;
            var form = modal ? modal.querySelector('form[data-tar-form="editar"]') : null;
            if (!form) { DBG.log('⚠ botón Editar sin modal/form destino (' + (selModal || '?') + ')', 'warn'); return; }

            var errBox = form.querySelector('.tar-form-err');
            if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }

            var get = function (k) { return btn.getAttribute('data-' + k) || ''; };
            form.setAttribute('action', get('url-update'));
            var porNombre = function (n) { return form.querySelector('[name="' + n + '"]'); };
            var f;
            if ((f = porNombre('id_rsv_catalogo_inmueble')) && f.tagName === 'SELECT') { f.value = get('inmueble-id'); }
            if ((f = porNombre('nombre_temporada'))) { f.value = get('nombre-temporada'); }
            if ((f = porNombre('fecha_inicio'))) { f.value = get('fecha-inicio'); }
            if ((f = porNombre('fecha_fin'))) { f.value = get('fecha-fin'); }
            if ((f = porNombre('precio_noche'))) { f.value = get('precio-noche'); }
            if ((f = porNombre('precio_fin_semana'))) { f.value = get('precio-fin-semana'); }
            if ((f = porNombre('precio_minimo_reserva'))) { f.value = get('precio-minimo-reserva'); }
            if ((f = porNombre('dias_maximos'))) { f.value = get('dias-maximos'); }
            if ((f = porNombre('max_reservas_anuales_cliente'))) { f.value = get('max-reservas'); }
            if ((f = porNombre('active')) && f.type === 'checkbox') { f.checked = get('active') === '1'; }

            actualizarDuracionModal(form);

            var hTitle = modal.querySelector('[data-tar-hint="title"]');
            var hVig = modal.querySelector('[data-tar-hint="vigencia"]');
            var hPre = modal.querySelector('[data-tar-hint="precio"]');
            if (hTitle) { hTitle.textContent = get('nombre-temporada') || 'Tarifa Comercial'; }
            if (hVig) {
                hVig.innerHTML = 'Vigencia: <b style="color: #5d8a70; font-weight: 600;">'
                    + (get('fecha-inicio') || '—') + ' → ' + (get('fecha-fin') || '—') + '</b>';
            }
            if (hPre) {
                var noche = parseFloat(get('precio-noche') || 0);
                hPre.innerHTML = 'Editando tarifa de <b style="color:#5d8a70;">$'
                    + noche.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    + '</b> por noche';
            }
            DBG.log('Modal editar listo · "' + (get('nombre-temporada') || '?') + '"', 'debug');
        };
        document.addEventListener('click', window.__tarFillHandler, true);
    })();
    </script>

</div>
