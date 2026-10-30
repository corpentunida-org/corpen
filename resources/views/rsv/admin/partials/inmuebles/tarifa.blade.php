{{-- resources/views/rsv/admin/partials/inmuebles/tarifa.blade.php · TAR-v1.1-DBG · NIVEL 3 (tarifas y temporadas) --}}
{{-- TAR-v1 · NOVEDADES · 1) FIX de contrato con el controlador: inmueblesTarifaFragmento() pasa --}}
{{--    "tarifas" (colección ya filtrada por inmueble_id) e "inmuebleId" — la vista original los --}}
{{--    IGNORABA (leía listaInmuebles/inmueble) → por AJAX en una ficha se veían TODAS las tarifas. --}}
{{--    Ahora: si llega la colección del fragmento se usa tal cual; el modo se deduce de inmuebleId --}}
{{--    (id específico = individual · 'todos' = global); las inclusiones antiguas siguen funcionando. --}}
{{--    2) "Nueva Tarifa" y "Editar" por AJAX: errores de validación (422) DENTRO del modal y, al --}}
{{--    guardar, la tabla se recarga sin recargar la página. 3) FIX script muerto: el llenado del --}}
{{--    modal de edición estaba dentro de DOMContentLoaded (no corre si el fragmento llega por AJAX) --}}
{{--    → ahora por DELEGACIÓN en document con guards re-registrables y agnósticos del UID. --}}
{{--    4) Panel de Errores LOCAL (botón abajo-derecha + Copiar/Limpiar/✕): registra SOLO operaciones --}}
{{--    de esta vista; el colector de contexto del servidor (marca TAR-v1) viaja al panel como JSON. --}}
{{--    5) UID anti-congelamiento conservado para ids de modales/campos (por si el partial se incluye --}}
{{--    dos veces); cada copia gestiona su propia raíz vía document.currentScript. --}}
{{--    6) CAMBIO TAR-v1.1-1 · Toggle de debug rediseñado al estilo "terminal" aprobado --}}
{{--    en TIP-v1.3: la pestaña deja el emoji y pasa a texto DBG en monospace 8px --}}
{{--    con LED de estado (verde = ok · rojo pulsante + conteo = errores). 16px de --}}
{{--    alto y FIJA SIEMPRE en right:0 · bottom:0 (nunca se apila hacia arriba). El --}}
{{--    panel sí esquiva a los vecinos midiendo px reales (getBoundingClientRect): --}}
{{--    bottom = max(20, vecino + 4). Exclusividad reforzada: el toggle/panel viajan --}}
{{--    con el fragmento y su respaldo SOLO re-crea nodos dentro de #tarRoot --}}
{{--    (nunca cuelga de document.body). SPEC DE DISTANCIAS documentada en el CSS. --}}
{{-- Arquitectura (patrón probado en reservas v32 · INM-v2 · CAT-v2): raíz estable que envuelve TODO --}}
{{-- incluido el script (renace con listeners tras cada swap) y el panel va ANTES de la raíz. Todos --}}
{{-- los comentarios de este archivo son seguros (nada de directivas sueltas en comentarios HTML). --}}
{{-- MODO GLOBAL = tabla de TODAS las tarifas (columna Inmueble) + selector de inmueble en el modal --}}
{{-- MODO INDIVIDUAL = tarifas de UN inmueble (sin columna Inmueble; inmueble va oculto en el modal) --}}

@php
    // ── TAR-v1.1-DBG · contrato del controlador + compatibilidad con inclusiones antiguas ──
    // El fragmento pasa: tarifas (ya filtradas y ordenadas) + inmuebleId ('todos' o id numérico).
    // Las inclusiones antiguas pasan: listaInmuebles / inmueble / soloLectura.
    $soloLectura = !empty($soloLectura);

    // Modo: individual si hay $inmueble (inclusión en ficha) O si el fragmento pidió un id específico
    $modoGlobal = (!isset($inmueble) || !$inmueble) && (empty($inmuebleId) || $inmuebleId === 'todos');

    // Inmueble oculto para los formularios cuando no hay objeto $inmueble (vía fragmento)
    $tarInmHidden = (isset($inmueble) && $inmueble)
        ? $inmueble->id
        : ((isset($inmuebleId) && $inmuebleId && $inmuebleId !== 'todos') ? $inmuebleId : null);

    $tarifasMostrar = collect([]);

    if (isset($tarifas) && $tarifas) {
        // ═══ Contrato del fragmento: ya viene filtrado y ordenado por el controlador ═══
        $tarifasMostrar = $tarifas;
    } elseif ($modoGlobal) {
        // ═══ Inclusión global antigua: armar la tabla desde listaInmuebles ═══
        if (!isset($listaInmuebles)) {
            try {
                $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::with('tarifasTemporadas')->get();
            } catch (\Throwable $e) {
                $listaInmuebles = collect([]);
            }
        }
        foreach ($listaInmuebles as $prop) {
            if (isset($prop->tarifasTemporadas) && $prop->tarifasTemporadas) {
                foreach ($prop->tarifasTemporadas as $t) {
                    $t->id_rsv_catalogo_inmueble = $prop->id;
                    $t->nombre_inmueble_padre = $prop->name ?? 'Inmueble';
                    $tarifasMostrar[] = $t;
                }
            }
        }
    } else {
        // ═══ Inclusión individual antigua (ficha del tab N2) ═══
        if (isset($inmueble) && $inmueble && isset($inmueble->tarifasTemporadas)) {
            foreach ($inmueble->tarifasTemporadas as $t) {
                $t->id_rsv_catalogo_inmueble = $inmueble->id;
                $t->nombre_inmueble_padre = $inmueble->name ?? 'Inmueble';
                $tarifasMostrar[] = $t;
            }
        }
    }

    // Nombres de inmuebles para la columna "Inmueble" en modo global (vía fragmento no vienen)
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

    // Selector de inmuebles para el modal (solo modo global): consulta ligera id+name+city
    if ($modoGlobal && !isset($listaInmuebles)) {
        try {
            $listaInmuebles = \App\Models\Rsv\CatalogoInmueble::query()->orderBy('name')->get(['id', 'name', 'city']);
        } catch (\Throwable $e) {
            $listaInmuebles = collect([]);
        }
    }

    // ═══ FIX ANTI-CONGELAMIENTO (del original) · UID único por inclusión ═══
    $tzUid = $tzUid ?? ('tz' . substr(md5(uniqid('', true)), 0, 8));

    // ── TAR-v1.1-DBG · colector de contexto del servidor (solo lectura, sin lógica de negocio) ──
    $tarDbg = [
        'marca'       => 'TAR-v1.1-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.tarifa',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? null),
        'total'       => $tarifasMostrar->count(),
        'selectorInm' => isset($listaInmuebles) && $listaInmuebles ? $listaInmuebles->count() : 0,
        'soloLectura' => $soloLectura,
        'uid'         => $tzUid,
        'validacion'  => [],
        'errores'     => [],
    ];
    if (isset($errors) && $errors->any()) {
        $tarDbg['validacion'] = $errors->all();
    }
@endphp

{{-- TAR-v1.1 · Tab DBG textual + LED + Panel LOCAL (fuera de la raíz: mueren con el fragmento · NO global) --}}
<div id="tarDbgToggle" title="Debug Tarifas (TAR-v1.1-DBG)"><span class="tar-dbg-led" id="tarDbgLed"></span>DBG<span class="tar-dbg-badge" id="tarDbgBadge">0</span></div>
<div id="tarDbgPanel" role="dialog" aria-label="Panel de debug de Tarifas">
    <div class="tar-dbg-header" id="tarDbgHeader">
        <span>DEBUG · Tarifas (N3) · TAR-v1.1-DBG</span>
        <span class="tar-dbg-controls">
            <button type="button" class="tar-dbg-btn" id="tarDbgCopy">Copiar</button>
            <button type="button" class="tar-dbg-btn" id="tarDbgClear">Limpiar</button>
            <button type="button" class="tar-dbg-btn" id="tarDbgClose">✕</button>
        </span>
    </div>
    <div id="tarDbgBody"></div>
</div>

<div id="tarRoot" class="tar-root" data-frag="{{ route('rsv.admin.inmuebles.tarifa.fragmento') }}">

    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <div class="d-flex justify-content-between align-items-center mb-2">

            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-cash-coin me-1" style="font-size: 8px; color: #5d8a70;"></i>
                {{ $modoGlobal ? 'Visión Global de Tarifas' : 'Tarifas y Temporadas del Inmueble' }}
                <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">TAR-v1.1-DBG</span>
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
                                        <i class="bi bi-building me-1" style="font-size: 7.5px; opacity: .7;"></i>{{ $tarifa->nombre_inmueble_padre ?? (($tarNombresInmuebles->isNotEmpty() && $tarifa->id_rsv_catalogo_inmueble && $tarNombresInmuebles->has($tarifa->id_rsv_catalogo_inmueble)) ? $tarNombresInmuebles[$tarifa->id_rsv_catalogo_inmueble] : 'N/A') }}
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
    {{-- MODAL 2A · NUEVA TARIFA · TAR-v1.1-DBG: envío por AJAX (sin recargar) --}}
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

                        {{-- Caja de errores de validación (la llena el panel TAR-v1.1-DBG con el 422) --}}
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
    {{-- MODAL 2B · EDITAR TARIFA · TAR-v1.1-DBG: los campos los llena el --}}
    {{-- script por delegación (data del botón) y el envío es por AJAX --}}
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

                        {{-- Caja de errores de validación (la llena el panel TAR-v1.1-DBG con el 422) --}}
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
    /* ═══════════════ TAR-v1.1-DBG · tarifa · NIVEL 3 ═══════════════
       Script LOCAL de esta vista: viaja DENTRO de la raíz, se re-ejecuta tras
       cada swap AJAX y renace con sus listeners. Sin DOMContentLoaded: ese
       patrón moría cuando el fragmento llegaba por AJAX (el evento ya había
       disparado). NO captura fetch ni errores de otros módulos de la página.  */
    (function () {
        /* Cada copia del partial gestiona SU propia raíz (soporta doble inclusión) */
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
                        /* ═════════ SPEC DE DISTANCIAS · TAR-v1.1 (tab terminal · portada de TIP-v1.3) ═════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
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
                    /* PATRÓN TIP-v1.1-1: el toggle y el panel ya vienen como HTML del
                       fragmento (fuera del root). Este respaldo solo los re-crea
                       DENTRO de #tarRoot si un swap parcial los borrara;
                       NUNCA se cuelga nada de document.body (exclusividad total). */
                    var rootDbg = document.getElementById('tarRoot');
                    if (!rootDbg) { return; } /* sin vista montada → cero nodos */
                    if (!$('tarDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'tarDbgToggle';
                        t.title = 'Debug Tarifas (TAR-v1.1-DBG)';
                        t.innerHTML = '<span class="tar-dbg-led" id="tarDbgLed"></span>DBG<span class="tar-dbg-badge" id="tarDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('tarDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'tarDbgPanel';
                        p.innerHTML = '<div class="tar-dbg-header" id="tarDbgHeader">' +
                            '<span>DEBUG · Tarifas (N3) · TAR-v1.1-DBG</span>' +
                            '<span class="tar-dbg-controls">' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgCopy">Copiar</button>' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgClear">Limpiar</button>' +
                            '<button type="button" class="tar-dbg-btn" id="tarDbgClose">✕</button>' +
                            '</span></div><div id="tarDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* TAR-v1.1: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila:
                   con 16px de alto cabe bajo cualquier otro toggle sin tocarlo).
                   Solo el PANEL esquiva a los vecinos: bottom = max(20, vecino + 4). */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'gmDbgToggle', 'tipDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('tarDbgToggle'), p = $('tarDbgPanel');
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
                        var txt = '=== DEBUG tarifa TAR-v1.1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                    alive: function () { return !!$('tarDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                window.TarDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure(); /* re-apila y re-crea nodos si algún swap de nivel superior los reemplazó */

        /* ═══ 1b) EXCLUSIVIDAD DE LA TAB DBG (patrón TIP-v1.1-1, sin vigilante) ═══
           La tab y el panel no se cuelgan de document.body: viajan como HTML
           del propio fragmento. Al navegar a otra vista el contenedor se
           intercambia y la tab muere SOLO con el fragmento; al volver a
           Tarifas renace con él. Nada que vigilar, nada que limpiar. */

        /* Botones del panel por DELEGACIÓN en document (sobreviven a cualquier swap) */
        if (window.__tarDbgDocHandler) { document.removeEventListener('click', window.__tarDbgDocHandler); }
        window.__tarDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#tarDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#tarDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#tarDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#tarDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        };
        document.addEventListener('click', window.__tarDbgDocHandler);
        if (window.__tarDbgDblHandler) { document.removeEventListener('dblclick', window.__tarDbgDblHandler); }
        window.__tarDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#tarDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__tarDbgDblHandler);

        /* ═══ 2) BOOT · contexto del servidor (como el Panel de Huéspedes) ═══ */
        DBG.log('TAR-v1.1-DBG activo · tab DBG fija en esquina (0,0) · LED de estado · exclusiva (muere con el fragmento)', 'ok');
        if (window.__tarEverBooted) { DBG.log('script re-ejecutado tras swap AJAX (el panel LOCAL continúa)', 'debug'); }
        window.__tarEverBooted = true;
        DBG.log('init · modo=' + (SRVDBG ? SRVDBG.modo : '?') +
            ' · tarifas=' + (SRVDBG ? SRVDBG.total : '?') +
            (SRVDBG && SRVDBG.inmuebleId ? ' · inmuebleSel=' + SRVDBG.inmuebleId : '') +
            (SRVDBG && SRVDBG.soloLectura ? ' · SOLO LECTURA' : '') +
            (SRVDBG && SRVDBG.uid ? ' · uid=' + SRVDBG.uid : ''), 'info');
        DBG.log('SRV · peticion=' + ((SRVDBG && SRVDBG.peticion) ? SRVDBG.peticion : '?') +
            ' · url=' + ((SRVDBG && SRVDBG.url) ? SRVDBG.url : '?'), 'debug');
        DBG.log('Rutas · FRAG=' + (FRAG || 'NO') +
            (FRAG ? ' · guardados y recargas por AJAX activos' : ' → FRAG vacío: guardados serán nativos (recarga)'), FRAG ? 'ok' : 'warn');
        if (SRVDBG && SRVDBG.validacion && SRVDBG.validacion.length) {
            for (var _vi = 0; _vi < SRVDBG.validacion.length; _vi++) {
                DBG.log('⚠ validación pendiente: ' + SRVDBG.validacion[_vi], 'warn');
            }
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }

        /* ═══ 3) HELPERS DE SWAP · repinta la raíz con el HTML del fragmento ═══ */
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
            if (document.querySelector('.modal.show')) { return; } /* hay otro modal legítimo abierto (otra vista): no tocar */
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
        function tarCargar(url) {
            if (!FRAG) { window.location.reload(); return; }
            DBG.log('fetch ' + url, 'net');
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (el servidor redirigió al login); inicia sesión y reintenta');
                }
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + (det ? ' · ' + det : (t && t.length > 0 && t.length < 80 ? ' · ' + t.trim() : '')));
                    });
                }
                return r.text();
            }).then(function (html) {
                tarSwapFragmento(html);
                DBG.log('tarifas recargadas · ' + html.length + ' bytes · sin recargar la página', 'ok');
            }).catch(function (err) {
                DBG.log('⚠ recarga de tarifas falló (' + err.message + ') → respaldo: recarga de la página', 'err');
                DBG.open();
                window.location.reload();
            });
        }

        /* ═══ 4) TOAST propio (no pisa los de otras vistas) ═══ */
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

        /* ═══ 5) RECARGA CON CONTEXTO · mantiene el inmueble actual tras guardar ═══ */
        function recargarContexto() {
            if (!FRAG) { window.location.reload(); return; }
            var inm = (SRVDBG && SRVDBG.inmuebleId) ? SRVDBG.inmuebleId : 'todos';
            tarCargar(FRAG + (FRAG.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(inm || 'todos'));
        }

        /* ═══ 6) FORMULARIOS POR AJAX · crear / editar (delegado · re-registrable) ═══ */
        if (window.__tarFormHandler) { document.removeEventListener('submit', window.__tarFormHandler); }
        window.__tarFormHandler = function (e) {
            var form = e.target;
            if (!form || !form.closest || !form.hasAttribute('data-tar-form')) { return; }
            e.preventDefault();
            var tipo = form.getAttribute('data-tar-form');
            var btn = form.querySelector('button[type="submit"]');
            var caja = form.querySelector('.tar-form-err');
            if (caja) { caja.style.display = 'none'; caja.innerHTML = ''; }
            if (btn) { btn.disabled = true; }
            DBG.log('submit ' + tipo + ' → ' + (form.getAttribute('action') || '(sin action)') + ' (por AJAX, sin recargar)', 'info');
            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (el servidor redirigió al login); inicia sesión y reintenta');
                }
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { status: r.status, ok: r.ok, json: j }; });
                }
                return r.text().then(function (t) { return { status: r.status, ok: r.ok, html: t }; });
            }).then(function (res) {
                /* 422 · errores de validación → se pintan DENTRO del modal, sin cerrarlo */
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
                    DBG.log('⚠ validación (422): ' + (msgs.join(' | ') || 'sin detalle'), 'err');
                    tarToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + (det ? ' · ' + det : ''));
                }
                /* Éxito (JSON o HTML tras redirect) → cerrar modal + recargar la tabla por AJAX */
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                tarToast(tipo === 'crear' ? 'Tarifa creada · actualizando la tabla…' : 'Cambios guardados · actualizando la tabla…', 'ok');
                DBG.log('guardado OK (' + (res.json ? 'JSON' : 'respuesta HTML tras redirect') + ') → recargando tarifas por AJAX', 'ok');
                recargarContexto();
            }).catch(function (err) {
                DBG.log('⚠ guardado falló (' + err.message + ')', 'err');
                tarToast('No se pudo guardar · ' + err.message, 'err');
                DBG.open();
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__tarFormHandler);

        /* ═══ 7) LLENADO DEL MODAL DE EDITAR · delegado en document (capture · re-registrable) ═══
           FIX del script muerto: antes era DOMContentLoaded + querySelectorAll por uid (no corría
           cuando el fragmento llegaba por AJAX). Ahora UN solo oyente atiende a TODOS los botones
           con data-tar-editar, vivos o re-creados tras cada swap.                              */
        if (window.__tarFillHandler) { document.removeEventListener('click', window.__tarFillHandler, true); }
        window.__tarFillHandler = function (e) {
            var btn = (e.target && e.target.closest) ? e.target.closest('[data-tar-editar]') : null;
            if (!btn) { return; }
            var selModal = btn.getAttribute('data-bs-target');
            var modal = selModal ? document.querySelector(selModal) : null;
            var form = modal ? modal.querySelector('form[data-tar-form="editar"]') : null;
            if (!form) { DBG.log('⚠ botón Editar sin modal/form destino (' + (selModal || '?') + ')', 'warn'); return; }
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
            if ((f = porNombre('active'))) { f.checked = get('active') === '1'; }
            /* Contexto del header/footer (igual que el script original) */
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
            DBG.log('modal editar listo · "' + (get('nombre-temporada') || '?') + '" → ' + get('url-update'), 'debug');
        };
        document.addEventListener('click', window.__tarFillHandler, true);

        DBG.log('interceptores listos · formularios (document) + llenado de modal (document capture) · uid=' + (SRVDBG && SRVDBG.uid ? SRVDBG.uid : '?'), 'debug');
    })();
    </script>

</div>