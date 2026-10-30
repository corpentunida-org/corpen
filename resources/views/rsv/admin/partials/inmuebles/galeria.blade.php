{{-- resources/views/rsv/admin/partials/inmuebles/galeria.blade.php · GM-v1.1-DBG · NIVEL 3 (vitrina visual / galería multimedia) --}}
{{-- GM-v1.1-DBG · NOVEDADES · 1) FIX CRÍTICO para AJAX: el script original corría dentro de un --}}
{{--    "DOMContentLoaded", que YA disparó cuando la vista llega por swap → tras cada refresco AJAX --}}
{{--    los dropzones, el pegar (Ctrl+V) y el llenado del modal de edición quedaban MUERTOS. --}}
{{--    Ahora el script corre directo: sus elementos están siempre antes de él dentro de #gmRoot. --}}
{{--    2) Formularios Agregar/Editar por AJAX (delegación document con data-gm-form): errores de --}}
{{--    validación 422 DENTRO del modal (.gm-form-err) y, al guardar, la vitrina se recarga SIN --}}
{{--    recargar la página (cascada: fragmento propio de galería → ficha N2 #showRoot → recarga nativa). --}}
{{--    3) Oyente global de "paste" con guard (__gmPasteHandler): ya no se acumula entre swaps. --}}
{{--    4) Formulario de Agregar con id (formAgregarMultimedia-{uid}) para poder interceptarlo. --}}
{{--    5) Panel de depuración LOCAL (tab DBG abajo-derecha + Copiar/Limpiar/✕): registra SOLO --}}
{{--    operaciones de esta vista; el colector de contexto del servidor viaja como JSON; el --}}
{{--    PANEL esquiva a los otros paneles de debug (reservas/huéspedes/tab/catálogo/tarifa/tipos). --}}
{{--    6) colspan de la fila vacía ahora respeta soloLectura (7 columnas con acciones, 6 sin ellas). --}}
{{--    7) Red de seguridad de variables + try/catch en los respaldos de BD y en la ruta del fragmento --}}
{{--    (si "rsv.admin.inmuebles.galeria.fragmento" no existe, la vista NO explota: el panel lo avisa). --}}
{{--    8) CAMBIO GM-v1.1-1 · REDISEÑO del panel debug con la estética terminal de TIP-v1.3 (la --}}
{{--    aprobada en Tipos): tab "DBG" de 16px con LED de estado fundida SIEMPRE en la esquina --}}
{{--    inferior-derecha (right:0 · bottom:0 · nunca sube ni salta), badge inline de errores, --}}
{{--    panel cajón monoespaciado pegado al borde derecho que esquiva a los vecinos midiendo su --}}
{{--    altura REAL (getBoundingClientRect) y exclusividad total: tab y panel mueren con el --}}
{{--    fragmento (nada se cuelga de document.body → cero tabs huérfanas al cambiar de vista). --}}
{{-- Arquitectura (patrón probado en reservas v32, tab-inmuebles INM-v2-DBG, catálogo CAT-v2-DBG, --}}
{{-- tarifa TAR-v1-DBG y tipo TIP-v1.3-DBG): UNA raíz estable #gmRoot envuelve TODO incluido el --}}
{{-- script (renace con sus listeners tras cada swap) y la tab DBG va ANTES del root: viaja con el --}}
{{-- fragmento (muere con él al navegar a otra vista y renace al volver; sin huérfanas en body). --}}
{{-- ESTILO: sistema suave original de la vitrina intacto (tabla 8px, chips pastel, dropzones). --}}
{{-- MODO GLOBAL    = multimedia de TODOS los inmuebles (columnas Inmueble + Tipo Inm. pobladas) --}}
{{-- MODO INDIVIDUAL = multimedia de UN inmueble ($inmueble) · #gmRoot lleva data-inmueble-id --}}
{{-- NOTA: los ids del panel de debug son fijos (igual que cat/tar): una vitrina por página. --}}
{{-- Los modales y dropzones SÍ usan uid ($gmUid) anti-colisión, como en el original. --}}

@php
    use App\Models\Rsv\InmuebleMultimedia;
    use App\Models\Rsv\CatalogoInmueble;

    // ── GM-v1.1-DBG · Red de seguridad de variables (la vista NUNCA explota por variable ausente) ──
    $modoGlobal  = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $soloLectura = $soloLectura ?? false;
    $gmUid       = $gmUid ?? ('gm' . substr(md5(uniqid('', true)), 0, 8));

    // ── GM-v1.1-DBG · colector de contexto del servidor (solo lectura, sin lógica de negocio) ──
    $gmDbg = [
        'marca'       => 'GM-v1.1-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.galeria',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global (todas las propiedades)' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? null),
        'soloLectura' => empty($soloLectura) ? false : true,
        'uid'         => $gmUid,
        'media'       => 0,
        'listaOpt'    => 0,
        'fragRuta'    => null,
        'rutas'       => [
            'store'  => route('rsv.inmueble-multimedia.store'),
            'update' => 'rsv.inmueble-multimedia.update (por fila)',
        ],
        'validacion'  => [],
        'errores'     => [],
    ];
    if (isset($errors) && $errors->any()) {
        $gmDbg['validacion'] = $errors->all();
    }

    // ── GM-v1.1-DBG · ruta del fragmento (mismo patrón de nombres que catálogo/tarifa) ──
    $gmFrag = null;
    try {
        $gmFrag = route('rsv.admin.inmuebles.galeria.fragmento');
        $gmDbg['fragRuta'] = 'rsv.admin.inmuebles.galeria.fragmento';
    } catch (\Throwable $e) {
        $gmDbg['errores'][] = 'ruta "rsv.admin.inmuebles.galeria.fragmento" no definida · tras guardar se recargará la ficha N2 (#showRoot) o la página';
    }

    // ── colecciones con respaldo y try/catch (como CAT-v2-DBG) ──
    if (!isset($mediaCollection)) {
        try {
            if ($modoGlobal) {
                $mediaCollection = InmuebleMultimedia::with(['inmueble:id,name,active,tipo_inmueble_id', 'inmueble.tipoInmueble'])->orderBy('orden', 'asc')->get();
            } elseif (isset($inmueble) && $inmueble) {
                $mediaCollection = $inmueble->multimedia;
            } else {
                $mediaCollection = collect([]);
                $gmDbg['errores'][] = 'modo individual sin $inmueble · vitrina vacía';
            }
        } catch (\Throwable $e) {
            $mediaCollection = collect([]);
            $gmDbg['errores'][] = 'InmuebleMultimedia: ' . $e->getMessage();
        }
    }
    if (!isset($listaInmueblesOpt)) {
        try {
            $listaInmueblesOpt = CatalogoInmueble::with('tipoInmueble')->orderBy('name', 'asc')->get();
        } catch (\Throwable $e) {
            $listaInmueblesOpt = collect([]);
            $gmDbg['errores'][] = 'CatalogoInmueble: ' . $e->getMessage();
        }
    }

    $tipoInmLabel = function ($inm) {
        $t = $inm?->tipoInmueble;
        return $t ? ($t->nombre ?? $t->name ?? $t->nom_tipo ?? null) : null;
    };

    // Enriquecer el colector con lo que depende de las colecciones ya resueltas
    $gmDbg['media']    = is_countable($mediaCollection) ? count($mediaCollection) : 0;
    $gmDbg['listaOpt'] = is_countable($listaInmueblesOpt) ? count($listaInmueblesOpt) : 0;
@endphp

{{-- GM-v1.1 · Tab DBG textual + LED + Panel LOCAL (fuera del root: mueren con el fragmento · NO global) --}}
<div id="gmDbgToggle" title="Debug Galería (GM-v1.1-DBG)"><span class="gm-dbg-led" id="gmDbgLed"></span>DBG<span class="gm-dbg-badge" id="gmDbgBadge">0</span></div>
<div id="gmDbgPanel" role="dialog" aria-label="Panel de debug de Galería">
    <div class="gm-dbg-header" id="gmDbgHeader">
        <span>DEBUG · Galería (N3) · GM-v1.1-DBG</span>
        <span class="gm-dbg-controls">
            <button type="button" class="gm-dbg-btn" id="gmDbgCopy">Copiar</button>
            <button type="button" class="gm-dbg-btn" id="gmDbgClear">Limpiar</button>
            <button type="button" class="gm-dbg-btn" id="gmDbgClose">✕</button>
        </span>
    </div>
    <div id="gmDbgBody"></div>
</div>

{{-- GM-v1.1-DBG · RAÍZ ESTABLE · todo el módulo (tabla + modales + script) vive dentro de #gmRoot --}}
<div id="gmRoot" class="gm-root"
     data-version="GM-v1.1-DBG"
     data-frag="{{ $gmFrag }}"
     @if(!$modoGlobal && isset($inmueble) && $inmueble) data-inmueble-id="{{ $inmueble->id }}" @endif>

    {{-- ── MÓDULO 3 · VITRINA VISUAL (Bootstrap nativo + inline, sin <style>) ── --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9;">

        <div class="d-flex justify-content-between align-items-center mb-2">

            <h6 class="fw-bold mb-0 d-flex align-items-center" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-images me-1" style="font-size: 8px; color: #5d8a70;"></i>
                Vitrina Visual y Galería
            </h6>

            @if(empty($soloLectura))
                <button type="button"
                        class="btn btn-sm d-inline-flex align-items-center"
                        style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                        data-bs-toggle="modal" data-bs-target="#modalAddMultimedia-{{ $gmUid }}">
                    <i class="bi bi-plus-lg me-1" style="font-size: 8px;"></i> Agregar Recurso
                </button>
            @endif

        </div>

        <div class="table-responsive">

            {{-- Tabla ultra-compacta usando variables Bootstrap (padding 2px) + font-size 8px --}}
            <table class="table table-sm table-hover align-middle mb-0"
                   style="font-size: 8px; color: #64748b; line-height: 1.1; --bs-table-cell-padding-x: 6px; --bs-table-cell-padding-y: 2px; --bs-table-hover-bg: #fafcfe; --bs-table-hover-color: #64748b;">

                <thead style="background-color: #fafbfd;">
                    <tr>
                        <th class="text-center" style="width: 22px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">#</th>
                        <th class="text-center" style="width: 28px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Vista</th>
                        <th style="width: 62px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Tipo</th>
                        <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Inmueble</th>
                        <th style="width: 100px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Tipo Inm.</th>
                        <th class="text-center" style="width: 36px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Ord.</th>
                        @if(empty($soloLectura))
                            <th class="text-center" style="width: 48px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">·</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @if(isset($mediaCollection) && count($mediaCollection) > 0)

                        @foreach($mediaCollection as $media)

                            @php $tipoInm = $tipoInmLabel($media->inmueble); @endphp

                            <tr>

                                <td class="text-center" style="color: #b3bcc7;">{{ $loop->iteration }}</td>

                                {{-- Vista · miniatura 18px (target=_blank: el interceptor N2 la ignora) --}}
                                <td class="text-center">
                                    <a href="{{ $media->url_archivo }}" target="_blank" title="Ver archivo completo" class="text-decoration-none" style="line-height: 0; display: inline-block;">
                                        @if($media->tipo_multimedia == 'imagen')
                                            <img src="{{ $media->url_archivo }}" alt="" style="width: 18px; height: 18px; object-fit: cover; border-radius: 2px; border: 1px solid #e9eef4; display: block; margin: 0 auto;">
                                        @else
                                            <span style="width: 18px; height: 18px; border-radius: 2px; border: 1px solid #e9eef4; background: #fafbfd; color: #3d7a5c; display: inline-flex; align-items: center; justify-content: center; font-size: 8px;">
                                                <i class="bi bi-play-fill"></i>
                                            </span>
                                        @endif
                                    </a>
                                </td>

                                {{-- Tipo · chips 7px --}}
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 2px;">
                                        @if($media->tipo_multimedia == 'imagen')
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5b7fa6; background: #f2f6fb; border: 1px solid #dbe6f2; white-space: nowrap;">
                                                <i class="bi bi-image" style="font-size: 6.5px; line-height: 1;"></i>IMG
                                            </span>
                                        @else
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #8a6fae; background: #f7f3fb; border: 1px solid #e6dcf2; white-space: nowrap;">
                                                <i class="bi bi-camera-video" style="font-size: 6.5px; line-height: 1;"></i>VID
                                            </span>
                                        @endif

                                        @if($media->es_portada)
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5d8a70; background: #f0f7f2; border: 1px solid #d8e9de; white-space: nowrap;" title="Portada principal">
                                                <i class="bi bi-star-fill" style="font-size: 6.5px; line-height: 1;"></i>P
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Inmueble · truncate 170px --}}
                                <td style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <span class="fw-semibold" style="font-size: 8px; color: #475569; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $media->inmueble->name ?? '—' }}">
                                        {{ $media->inmueble->name ?? '—' }}
                                    </span>
                                </td>

                                {{-- Tipo Inmueble · chip verde --}}
                                <td style="max-width: 96px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    @if($tipoInm)
                                        <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; color: #3d7a5c; background: #f0f7f2; border: 1px solid #d5e7dc; padding: 0 5px; border-radius: 7px; line-height: 12px; white-space: nowrap; max-width: 100%;" title="{{ $tipoInm }}">
                                            <i class="bi bi-tag-fill" style="font-size: 6.5px; line-height: 1; flex-shrink: 0;"></i>
                                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $tipoInm }}</span>
                                        </span>
                                    @else
                                        <span style="color: #b3bcc7;">—</span>
                                    @endif
                                </td>

                                <td class="text-center" style="color: #b3bcc7;">{{ $media->orden }}</td>

                                @if(empty($soloLectura))
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-multimedia-{{ $gmUid }}"
                                                style="font-size: 8px; color: #3d7a5c; font-weight: 600; line-height: 1.4;"
                                                data-bs-toggle="modal" data-bs-target="#modalEditarMultimedia-{{ $gmUid }}"
                                                data-url-update="{{ route('rsv.inmueble-multimedia.update', $media->id) }}"
                                                data-tipo-multimedia="{{ $media->tipo_multimedia }}"
                                                data-orden="{{ $media->orden }}"
                                                data-es-portada="{{ $media->es_portada ? 1 : 0 }}"
                                                data-inmueble-id="{{ $media->id_rsv_catalogo_inmueble }}"
                                                data-inmueble-nombre="{{ $media->inmueble->name ?? '' }}"
                                                data-tipo-inmueble="{{ $tipoInm ?? '' }}">
                                            Editar
                                        </button>
                                    </td>
                                @endif

                            </tr>

                        @endforeach

                    @else
                        <tr>
                            {{-- GM-v1.1-DBG · colspan dinámico: con acciones son 7 columnas, sin ellas 6 --}}
                            <td colspan="{{ empty($soloLectura) ? 7 : 6 }}" class="text-center" style="padding: 8px; color: #b3bcc7; font-size: 8.5px;">
                                <i class="bi bi-images me-1"></i> Vitrina vacía. Sube fotos o videos.
                            </td>
                        </tr>
                    @endif
                </tbody>

            </table>

        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3A · AGREGAR RECURSO MULTIMEDIA                             --}}
    {{-- GM-v1.1-DBG · form ahora con id + data-gm-form="store" para AJAX    --}}
    {{-- y caja .gm-form-err para pintar los 422 sin cerrar el modal       --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalAddMultimedia-{{ $gmUid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 5px; box-shadow: 0 8px 30px rgba(15,23,42,.06); font-size: 8px; line-height: 1.4; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    <div class="text-end">
                        <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                            Vitrina Visual
                        </div>
                        <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Agregar Recurso Multimedia</h6>
                        <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                    </div>
                </div>

                <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST" enctype="multipart/form-data"
                      id="formAgregarMultimedia-{{ $gmUid }}" data-gm-form="store">
                    @csrf

                    <div class="modal-body" style="padding: 12px 14px;">

                        {{-- GM-v1.1-DBG · caja de errores de validación (422 se pinta aquí sin cerrar el modal) --}}
                        <div class="gm-form-err" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; padding: 6px 9px; font-size: 8px; margin-bottom: 8px; line-height: 1.5;"></div>

                        {{-- 1 · IDENTIFICACIÓN --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Identificación</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Inmueble <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm" name="id_rsv_catalogo_inmueble" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @foreach ($listaInmueblesOpt as $propOpt)
                                        @php $tInmOpt = $tipoInmLabel($propOpt); @endphp
                                        <option value="{{ $propOpt->id }}" {{ (!$modoGlobal && isset($inmueble) && $inmueble->id == $propOpt->id) ? 'selected' : '' }}>
                                            {{ $propOpt->name }}{{ isset($propOpt->city) && $propOpt->city ? ' (' . $propOpt->city . ')' : '' }}{{ $tInmOpt ? ' · ' . $tInmOpt : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de multimedia <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm" name="tipo_multimedia" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                    <option value="imagen">Imagen</option>
                                    <option value="video">Video</option>
                                </select>
                                <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Define cómo se renderiza en la vitrina pública</div>
                            </div>
                        </div>

                        {{-- 2 · ARCHIVO (Pegar · Arrastrar · Clic) --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-cloud-arrow-up" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Archivo (S3) <span style="color: #c98a85;">*</span></span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="position-relative" id="dzAdd-{{ $gmUid }}" style="border: 1px dashed #c9d4de; border-radius: 4px; background: #fafbfd; overflow: hidden; transition: border-color .15s ease, background .15s ease;">
                                <input type="file" name="url_archivo" accept="image/*,video/*" required id="inputAddFile-{{ $gmUid }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;">

                                <div id="dzAddIdle-{{ $gmUid }}" style="padding: 14px 10px; text-align: center;">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 16px; color: #b3bcc7; display: block; margin-bottom: 3px;"></i>
                                    <div style="font-size: 8px; color: #94a3b8; line-height: 1.5;">
                                        Pega con <b style="color: #3d7a5c;">Ctrl + V</b>, arrastra el archivo aquí<br>
                                        o haz clic para explorar
                                    </div>
                                </div>

                                <div class="d-none align-items-center" id="dzAddPreview-{{ $gmUid }}" style="gap: 8px; padding: 8px 10px; text-align: left;">
                                    <img id="dzAddThumb-{{ $gmUid }}" class="d-none" style="width: 38px; height: 38px; object-fit: cover; border-radius: 3px; border: 1px solid #e9eef4; flex-shrink: 0;" alt="">
                                    <span id="dzAddThumbV-{{ $gmUid }}" class="d-none d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 3px; background: #fafbfd; border: 1px solid #e9eef4; color: #3d7a5c; font-size: 14px; flex-shrink: 0;">
                                        <i class="bi bi-camera-video"></i>
                                    </span>
                                    <div style="min-width: 0; flex: 1;">
                                        <div class="fw-semibold text-truncate" id="dzAddName-{{ $gmUid }}" style="font-size: 8.5px; color: #475569;"></div>
                                        <div id="dzAddSize-{{ $gmUid }}" style="font-size: 7px; color: #b3bcc7;"></div>
                                    </div>
                                    <button type="button" id="dzAddClear-{{ $gmUid }}" title="Quitar archivo" style="position: relative; z-index: 5; background: #fff; border: 1px solid #e9eef4; border-radius: 3px; color: #94a3b8; font-size: 8px; line-height: 1; padding: 3px 5px; flex-shrink: 0;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Imágenes: JPG, PNG, WEBP · Videos: MP4, WEBM</div>
                        </div>

                        {{-- 3 · ORDEN Y PORTADA --}}
                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-sliders" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Orden y Portada</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="row g-2 align-items-center">
                                <div class="col-5">
                                    <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Orden de aparición</label>
                                    <input type="number" class="form-control form-control-sm" name="orden" placeholder="Ej. 1" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                    <div style="font-size: 7px; color: #c3ccd6; margin-top: 1px;">Vacío = al final</div>
                                </div>

                                <div class="col-7">
                                    <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Portada</label>
                                    <div class="d-flex align-items-center justify-content-between" style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                        <div class="text-start">
                                            <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Portada principal</div>
                                            <div style="font-size: 7px; color: #b3bcc7;">Aparece primero en la vitrina</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="addMediaPortada-{{ $gmUid }}" style="margin-top: 0;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                        <span class="text-muted" style="font-size: 7.5px;">Se muestra en la vitrina del inmueble</span>
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
    {{-- MODAL 3B · EDITAR RECURSO MULTIMEDIA                              --}}
    {{-- GM-v1.1-DBG · form ahora con data-gm-form="update" para AJAX        --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalEditarMultimedia-{{ $gmUid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 5px; box-shadow: 0 8px 30px rgba(15,23,42,.06); font-size: 8px; line-height: 1.4; color: #64748b;">

                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    <div class="text-end">
                        <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                            Vitrina Visual · Editando
                        </div>
                        <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_media_hint_title-{{ $gmUid }}" style="font-size: 0.95rem;">Recurso Multimedia</h6>
                        <small class="text-muted" id="edit_media_hint-{{ $gmUid }}" style="font-size: 7.5px;">Selecciona un recurso…</small>
                    </div>
                </div>

                <form id="formEditarMultimedia-{{ $gmUid }}" method="POST" enctype="multipart/form-data" data-gm-form="update">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">

                        {{-- GM-v1.1-DBG · caja de errores de validación (422 se pinta aquí sin cerrar el modal) --}}
                        <div class="gm-form-err" style="display: none; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; padding: 6px 9px; font-size: 8px; margin-bottom: 8px; line-height: 1.5;"></div>

                        {{-- 1 · IDENTIFICACIÓN --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-tag" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Identificación</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Inmueble <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm" id="edit_media_inmueble-{{ $gmUid }}" name="id_rsv_catalogo_inmueble" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                    <option value="">-- Seleccione un inmueble --</option>
                                    @foreach ($listaInmueblesOpt as $propOpt)
                                        @php $tInmOpt = $tipoInmLabel($propOpt); @endphp
                                        <option value="{{ $propOpt->id }}">{{ $propOpt->name }}{{ isset($propOpt->city) && $propOpt->city ? ' (' . $propOpt->city . ')' : '' }}{{ $tInmOpt ? ' · ' . $tInmOpt : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Tipo de multimedia <span style="color: #c98a85;">*</span></label>
                                <select class="form-select form-select-sm" id="edit_media_tipo-{{ $gmUid }}" name="tipo_multimedia" required style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                    <option value="imagen">Imagen</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                        </div>

                        {{-- 2 · REEMPLAZAR ARCHIVO (Pegar · Arrastrar · Clic) --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-cloud-arrow-up" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Reemplazar Archivo</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="position-relative" id="dzEdit-{{ $gmUid }}" style="border: 1px dashed #c9d4de; border-radius: 4px; background: #fafbfd; overflow: hidden; transition: border-color .15s ease, background .15s ease;">
                                <input type="file" name="url_archivo" accept="image/*,video/*" id="inputEditFile-{{ $gmUid }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;">

                                <div id="dzEditIdle-{{ $gmUid }}" style="padding: 14px 10px; text-align: center;">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 16px; color: #b3bcc7; display: block; margin-bottom: 3px;"></i>
                                    <div style="font-size: 8px; color: #94a3b8; line-height: 1.5;">
                                        Pega con <b style="color: #3d7a5c;">Ctrl + V</b> o arrastra para reemplazar<br>
                                        Dejar vacío conserva el archivo actual
                                    </div>
                                </div>

                                <div class="d-none align-items-center" id="dzEditPreview-{{ $gmUid }}" style="gap: 8px; padding: 8px 10px; text-align: left;">
                                    <img id="dzEditThumb-{{ $gmUid }}" class="d-none" style="width: 38px; height: 38px; object-fit: cover; border-radius: 3px; border: 1px solid #e9eef4; flex-shrink: 0;" alt="">
                                    <span id="dzEditThumbV-{{ $gmUid }}" class="d-none d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 3px; background: #fafbfd; border: 1px solid #e9eef4; color: #3d7a5c; font-size: 14px; flex-shrink: 0;">
                                        <i class="bi bi-camera-video"></i>
                                    </span>
                                    <div style="min-width: 0; flex: 1;">
                                        <div class="fw-semibold text-truncate" id="dzEditName-{{ $gmUid }}" style="font-size: 8.5px; color: #475569;"></div>
                                        <div id="dzEditSize-{{ $gmUid }}" style="font-size: 7px; color: #b3bcc7;"></div>
                                    </div>
                                    <button type="button" id="dzEditClear-{{ $gmUid }}" title="Quitar y conservar el actual" style="position: relative; z-index: 5; background: #fff; border: 1px solid #e9eef4; border-radius: 3px; color: #94a3b8; font-size: 8px; line-height: 1; padding: 3px 5px; flex-shrink: 0;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- 3 · ORDEN Y PORTADA --}}
                        <div>
                            <div class="d-flex align-items-center mb-1" style="gap: 5px;">
                                <i class="bi bi-sliders" style="font-size: 7.5px; opacity: .7;"></i>
                                <span style="font-size: 7px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: #b3bcc7;">Orden y Portada</span>
                                <span style="flex: 1; height: 1px; background: #f1f5f9;"></span>
                            </div>

                            <div class="row g-2 align-items-center">
                                <div class="col-5">
                                    <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Orden</label>
                                    <input type="number" class="form-control form-control-sm" id="edit_media_orden-{{ $gmUid }}" name="orden" min="1" style="font-size: 9.5px; color: #475569; border-color: #e9eef4; border-radius: 3px; padding: 3px 8px; box-shadow: none;">
                                </div>

                                <div class="col-7">
                                    <label class="form-label" style="font-size: 7.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px;">Portada</label>
                                    <div class="d-flex align-items-center justify-content-between" style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px; padding: 6px 9px; gap: 8px;">
                                        <div class="text-start">
                                            <div style="font-size: 8.5px; font-weight: 600; color: #475569;">Portada principal</div>
                                            <div style="font-size: 7px; color: #b3bcc7;">Se muestra primero en la vitrina</div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="edit_media_portada-{{ $gmUid }}" style="margin-top: 0;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                        <span class="text-muted" id="edit_media_hint_file-{{ $gmUid }}" style="font-size: 7.5px;"></span>
                        <div class="d-flex" style="gap: 4px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="font-size: 11px; padding: 4px 8px;">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark" style="font-size: 11px; padding: 4px 8px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SCRIPT GM-v1.1-DBG · todo en uno, DENTRO de #gmRoot (renace tras    --}}
    {{-- cada swap). SIN "DOMContentLoaded": ese evento ya disparó cuando  --}}
    {{-- la vista llega por swap y dejaba dropzones/edit muertos.          --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <script>
    /* ═══════════════ GM-v1.1-DBG · galeria · NIVEL 3 ═══════════════
       Script LOCAL de esta vista: viaja DENTRO de #gmRoot, por lo que se
       re-ejecuta tras cada swap AJAX y renace con sus listeners.
       NO captura fetch ni errores de otros módulos de la página.        */
    (function () {
        /* root resuelto por copia vía currentScript (soporta doble inclusión sin pisarse) */
        var root = (document.currentScript && document.currentScript.closest)
            ? document.currentScript.closest('.gm-root')
            : document.getElementById('gmRoot');
        if (!root) { return; }

        var FRAG = root.getAttribute('data-frag') || '';
        var GMDBG = @json($gmDbg);
        var UID = (GMDBG && GMDBG.uid) ? GMDBG.uid : 'gm';

        /* ═══ 1) PANEL DE DEBUG LOCAL (estética terminal TIP-v1.3 · tab DBG + LED) ═══ */
        var DBG = (window.GmDbg && window.GmDbg.alive && window.GmDbg.alive())
            ? window.GmDbg
            : (function () {
                var MAX = 250, numErr = 0;
                function $(id) { return document.getElementById(id); }
                function asegurarCSS() {
                    if ($('gmDbgStyle')) { return; }
                    var st = document.createElement('style');
                    st.id = 'gmDbgStyle';
                    st.textContent = [
                        /* ═════════ SPEC DE DISTANCIAS · GM-v1.1 (tab terminal TIP-v1.3) ════════
                           TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la
                                       esquina inferior-derecha (nunca sube: con
                                       16px de alto cabe bajo cualquier otro toggle)
                                     · altura fija 16px · padding lateral 8px
                                     · radius 3px solo sup-izq · borde 1px arriba/izq
                                     · monospace 8px · LED 5px (verde ok / rojo error)
                           PANEL     · right:0px · bottom = max(20, vecino + 4px)
                                       (20 = 16 de tab + 4 de aire mínimo)
                                       · ancho 380px · radius 6px 0 0 0 (cajón pegado) */
                        '#gmDbgToggle{position:fixed;right:0;bottom:0;z-index:1090;height:16px;padding:0 8px;',
                        'border-radius:3px 0 0 0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'background:rgba(2,6,23,.94);color:#64748b;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;',
                        'font-size:8px;font-weight:700;letter-spacing:.18em;line-height:1;',
                        'cursor:pointer;display:flex;align-items:center;gap:5px;',
                        'user-select:none;-webkit-user-select:none;',
                        'transition:color .15s ease,background .15s ease,box-shadow .15s ease;}',
                        '#gmDbgToggle:hover{color:#38bdf8;border-color:#334155;',
                        'background:rgba(8,15,30,.97);box-shadow:0 0 12px rgba(56,189,248,.12);}',
                        '.gm-dbg-led{width:5px;height:5px;border-radius:1px;flex-shrink:0;',
                        'background:#10b981;box-shadow:0 0 5px rgba(16,185,129,.75);}',
                        '.gm-dbg-led.err{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,.85);',
                        'animation:gmDbgBlink 1s steps(2,start) infinite;}',
                        '@keyframes gmDbgBlink{50%{opacity:.35;}}',
                        '.gm-dbg-badge{display:none;color:#f87171;font-size:8px;',
                        'font-weight:700;line-height:1;letter-spacing:0;}',
                        '#gmDbgPanel{position:fixed;right:0;bottom:20px;z-index:1091;width:380px;',
                        'max-width:calc(100vw - 16px);max-height:60vh;display:none;flex-direction:column;',
                        'background:rgba(2,6,23,.97);color:#e2e8f0;border:1px solid #1e293b;border-right:0;border-bottom:0;',
                        'border-radius:6px 0 0 0;box-shadow:0 12px 32px rgba(0,0,0,.55);overflow:hidden;',
                        'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;}',
                        '.gm-dbg-header{display:flex;justify-content:space-between;align-items:center;gap:6px;',
                        'padding:5px 8px;background:#0b1220;border-bottom:1px solid #1e293b;',
                        'font-size:9px;font-weight:700;letter-spacing:.1em;color:#38bdf8;cursor:pointer;}',
                        '.gm-dbg-controls{display:flex;gap:4px;}',
                        '.gm-dbg-btn{background:#334155;color:#e2e8f0;border:0;border-radius:4px;',
                        'font-size:9px;padding:2px 7px;cursor:pointer;font-family:inherit;}',
                        '.gm-dbg-btn:hover{background:#475569;}',
                        '#gmDbgBody{overflow-y:auto;padding:6px 8px;font-size:9.5px;line-height:1.55;}',
                        '.gm-dbg-line{white-space:pre-wrap;word-break:break-word;padding:2px 0;',
                        'border-bottom:1px dashed #1e293b;}',
                        '.gm-dbg-time{color:#64748b;margin-right:4px;}',
                        '.gm-dbg-info{color:#93c5fd;}.gm-dbg-ok{color:#86efac;}',
                        '.gm-dbg-warn{color:#fde68a;}.gm-dbg-err{color:#fca5a5;}',
                        '.gm-dbg-net{color:#c4b5fd;}.gm-dbg-debug{color:#94a3b8;}',
                        '#gmToastWrap{position:fixed;left:16px;bottom:16px;z-index:1092;display:flex;',
                        'flex-direction:column;gap:6px;}'
                    ].join('\n');
                    document.head.appendChild(st);
                }
                function asegurarNodos() {
                    /* FIX GM-v1.1-1 (patrón TIP-v1.1-1): el toggle y el panel ya vienen como HTML
                       del fragmento (fuera del root). Este respaldo solo los re-crea DENTRO de
                       #gmRoot si un swap parcial los borrara; NUNCA se cuelga nada de
                       document.body (exclusividad total: la tab muere con el fragmento). */
                    var rootDbg = document.getElementById('gmRoot');
                    if (!rootDbg) { return; } /* sin vista montada → cero nodos */
                    if (!$('gmDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'gmDbgToggle';
                        t.title = 'Debug Galería (GM-v1.1-DBG)';
                        t.innerHTML = '<span class="gm-dbg-led" id="gmDbgLed"></span>DBG<span class="gm-dbg-badge" id="gmDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('gmDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'gmDbgPanel';
                        p.innerHTML = '<div class="gm-dbg-header" id="gmDbgHeader">' +
                            '<span>DEBUG · Galería (N3) · GM-v1.1-DBG</span>' +
                            '<span class="gm-dbg-controls">' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgCopy">Copiar</button>' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgClear">Limpiar</button>' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgClose">✕</button>' +
                            '</span></div><div id="gmDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                /* GM-v1.1: la TAB se queda SIEMPRE abajo (bottom:0, nunca se apila: con 16px
                   de alto cabe bajo cualquier otro toggle sin tocarlo). Solo el PANEL esquiva
                   a los vecinos: bottom = max(20, vecino + 4), midiendo su altura REAL. */
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'tipDbgToggle']; /* serie TIP */
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; } /* display:none no ocupa sitio */
                        var desdeAbajo = window.innerHeight - r.top; /* cuánto sube ese vecino desde el fondo */
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('gmDbgToggle'), p = $('gmDbgPanel');
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
                        var b = $('gmDbgBody'); if (!b) { return; }
                        var lv = nivel || 'info';
                        var ln = document.createElement('div');
                        ln.className = 'gm-dbg-line';
                        var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                            return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                        });
                        ln.innerHTML = '<span class="gm-dbg-time">[' + hora() + ']</span>' +
                            '<span class="gm-dbg-' + lv + '">' + seguro + '</span>';
                        b.appendChild(ln);
                        while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                        b.scrollTop = b.scrollHeight;
                        if (lv === 'err') {
                            numErr++;
                            var bd = $('gmDbgBadge');
                            if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                            var led = $('gmDbgLed');
                            if (led) { led.classList.add('err'); }
                        }
                    },
                    open: function () {
                        var p = $('gmDbgPanel'); if (!p) { return; }
                        p.style.display = 'flex';
                        apilar();
                        var bd = $('gmDbgBadge');
                        if (bd) { bd.style.display = 'none'; }
                        var led = $('gmDbgLed');
                        if (led) { led.classList.remove('err'); }
                        numErr = 0;
                    },
                    close: function () { var p = $('gmDbgPanel'); if (p) { p.style.display = 'none'; } },
                    togglePanel: function () {
                        var p = $('gmDbgPanel');
                        if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                    },
                    clear: function () { var b = $('gmDbgBody'); if (b) { b.innerHTML = ''; } },
                    copy: function () {
                        var b = $('gmDbgBody'); if (!b) { return; }
                        var txt = '=== DEBUG galeria GM-v1.1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                    alive: function () { return !!$('gmDbgToggle'); }
                };
                asegurarCSS();
                asegurarNodos();
                window.GmDbg = api;
                return api;
            })();
        if (!DBG) { return; }
        DBG.ensure(); /* re-apila y re-crea nodos si algún swap de nivel superior los reemplazó */

        /* ═══ 1b) EXCLUSIVIDAD DE LA TAB DBG · FIX GM-v1.1-1 (sin vigilante) ═══
           La tab y el panel ya no se cuelgan de document.body: viajan como
           HTML del propio fragmento. Al navegar a otra vista el contenedor se
           intercambia y la tab muere SOLO con el fragmento; al volver a la
           Galería renace con él. Nada que vigilar, nada que limpiar. */

        /* Botones del panel por DELEGACIÓN en document (sobreviven a cualquier swap) */
        if (window.__gmDbgDocHandler) { document.removeEventListener('click', window.__gmDbgDocHandler); }
        window.__gmDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#gmDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#gmDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#gmDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#gmDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        };
        document.addEventListener('click', window.__gmDbgDocHandler);
        if (window.__gmDbgDblHandler) { document.removeEventListener('dblclick', window.__gmDbgDblHandler); }
        window.__gmDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#gmDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__gmDbgDblHandler);

        /* ═══ 2) BOOT · contexto del servidor (como el Panel de Huéspedes) ═══ */
        DBG.log('GM-v1.1-DBG activo · tab DBG fija en esquina (0,0) · LED de estado · panel LOCAL (solo galería · NO captura errores ajenos)', 'ok');
        if (window.__gmEverBooted) { DBG.log('script re-ejecutado tras swap AJAX (el panel LOCAL continúa)', 'debug'); }
        window.__gmEverBooted = true;
        DBG.log('init · modo=' + (GMDBG ? GMDBG.modo : '?') +
            ' · media=' + (GMDBG ? GMDBG.media : '?') +
            ' · inmueblesOpt=' + (GMDBG ? GMDBG.listaOpt : '?') +
            ' · uid=' + UID +
            (GMDBG && GMDBG.soloLectura ? ' · SOLO LECTURA' : ''), 'info');
        DBG.log('SRV · peticion=' + ((GMDBG && GMDBG.peticion) ? GMDBG.peticion : '?') +
            ' · url=' + ((GMDBG && GMDBG.url) ? GMDBG.url : '?'), 'debug');
        DBG.log('Rutas · FRAG=' + (FRAG || 'NO') +
            (FRAG ? ' · recarga post-guardado por AJAX activa'
                  : ' → tras guardar se recargará la ficha N2 (#showRoot) o la página'), FRAG ? 'ok' : 'warn');
        DBG.log('store → ' + ((GMDBG && GMDBG.rutas && GMDBG.rutas.store) ? GMDBG.rutas.store : '?'), 'debug');
        if (GMDBG && GMDBG.validacion && GMDBG.validacion.length) {
            for (var _vi = 0; _vi < GMDBG.validacion.length; _vi++) {
                DBG.log('⚠ validación pendiente: ' + GMDBG.validacion[_vi], 'warn');
            }
        }
        if (GMDBG && GMDBG.errores && GMDBG.errores.length) {
            for (var _di = 0; _di < GMDBG.errores.length; _di++) {
                DBG.log('⚠ SERVIDOR capturó: ' + GMDBG.errores[_di], 'err');
            }
        }

        /* ═══ 3) DROPZONES · pegar / arrastrar / clic (lógica original de la vitrina, intacta) ═══ */
        function formatSize(b) {
            return b >= 1048576
                ? (b / 1048576).toFixed(1) + ' MB'
                : Math.max(1, Math.round(b / 1024)) + ' KB';
        }
        function isMedia(f) {
            return !!(f && (f.type.startsWith('image/') || f.type.startsWith('video/')));
        }
        var activeZones = [];

        function setupDropzone(cfg) {
            var dz = document.getElementById(cfg.dz);
            var input = document.getElementById(cfg.input);
            var idle = document.getElementById(cfg.idle);
            var prev = document.getElementById(cfg.preview);
            var thumb = document.getElementById(cfg.thumb);
            var thumbV = document.getElementById(cfg.thumbVideo);
            var nameEl = document.getElementById(cfg.name);
            var sizeEl = document.getElementById(cfg.size);
            var clearB = document.getElementById(cfg.clear);
            if (!dz || !input) { return; }
            var objUrl = null;

            function showPreview(file) {
                if (objUrl) { URL.revokeObjectURL(objUrl); }
                objUrl = URL.createObjectURL(file);
                var esImg = file.type.startsWith('image/');
                thumb.classList.toggle('d-none', !esImg);
                thumbV.classList.toggle('d-none', esImg);
                if (esImg) { thumb.src = objUrl; }
                nameEl.textContent = file.name;
                sizeEl.textContent = formatSize(file.size);
                idle.classList.add('d-none');
                prev.classList.remove('d-none');
                prev.classList.add('d-flex');
            }
            function reset() {
                input.value = '';
                if (objUrl) { URL.revokeObjectURL(objUrl); objUrl = null; }
                prev.classList.add('d-none');
                prev.classList.remove('d-flex');
                idle.classList.remove('d-none');
            }
            function setFile(file) {
                if (!isMedia(file)) { return; }
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showPreview(file);
            }

            input.addEventListener('change', function () {
                if (input.files && input.files[0]) { showPreview(input.files[0]); } else { reset(); }
            });

            clearB.addEventListener('click', function (e) {
                e.preventDefault(); e.stopPropagation(); reset();
            });

            ['dragenter', 'dragover'].forEach(function (ev) {
                dz.addEventListener(ev, function (e) {
                    e.preventDefault(); e.stopPropagation();
                    dz.style.borderColor = '#3d7a5c';
                    dz.style.background = '#f0f7f2';
                    dz.style.borderStyle = 'solid';
                });
            });
            dz.addEventListener('dragleave', function (e) {
                e.preventDefault();
                dz.style.borderColor = '#c9d4de';
                dz.style.background = '#fafbfd';
                dz.style.borderStyle = 'dashed';
            });
            dz.addEventListener('drop', function (e) {
                e.preventDefault(); e.stopPropagation();
                dz.style.borderColor = '#c9d4de';
                dz.style.background = '#fafbfd';
                dz.style.borderStyle = 'dashed';
                var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                if (file) { setFile(file); }
            });

            activeZones.push({ modalId: cfg.modal, setFile: setFile, reset: reset });
        }

        setupDropzone({
            modal: 'modalAddMultimedia-' + UID,
            dz: 'dzAdd-' + UID, input: 'inputAddFile-' + UID,
            idle: 'dzAddIdle-' + UID, preview: 'dzAddPreview-' + UID,
            thumb: 'dzAddThumb-' + UID, thumbVideo: 'dzAddThumbV-' + UID,
            name: 'dzAddName-' + UID, size: 'dzAddSize-' + UID, clear: 'dzAddClear-' + UID
        });

        setupDropzone({
            modal: 'modalEditarMultimedia-' + UID,
            dz: 'dzEdit-' + UID, input: 'inputEditFile-' + UID,
            idle: 'dzEditIdle-' + UID, preview: 'dzEditPreview-' + UID,
            thumb: 'dzEditThumb-' + UID, thumbVideo: 'dzEditThumbV-' + UID,
            name: 'dzEditName-' + UID, size: 'dzEditSize-' + UID, clear: 'dzEditClear-' + UID
        });

        /* pegar (Ctrl+V) · oyente GLOBAL con guard: al re-ejecutarse el script tras cada swap
           se quita el anterior y queda solo el último (antes se acumulaban sin control)      */
        if (window.__gmPasteHandler) { document.removeEventListener('paste', window.__gmPasteHandler); }
        window.__gmZones = activeZones; /* las zonas VIVAS son siempre las del último script ejecutado */
        window.__gmPasteHandler = function (e) {
            var items = e.clipboardData && e.clipboardData.items;
            if (!items) { return; }
            var zonas = window.__gmZones || [];
            for (var zi = 0; zi < zonas.length; zi++) {
                var modal = document.getElementById(zonas[zi].modalId);
                if (!modal || !modal.classList.contains('show')) { continue; }
                for (var ii = 0; ii < items.length; ii++) {
                    var item = items[ii];
                    if (item.kind === 'file') {
                        var file = item.getAsFile();
                        if (file && isMedia(file)) {
                            zonas[zi].setFile(file);
                            e.preventDefault();
                            return;
                        }
                    }
                }
            }
        };
        document.addEventListener('paste', window.__gmPasteHandler);
        DBG.log('dropzones listas · pegar (Ctrl+V) / arrastrar / clic · sin DOMContentLoaded (v31)', 'debug');

        /* ═══ 4) RELLENO DEL MODAL DE EDICIÓN (lógica original, alcance limitado a este root) ═══ */
        var botonesEditar = root.querySelectorAll('.btn-editar-multimedia-' + UID);
        for (var bi = 0; bi < botonesEditar.length; bi++) {
            botonesEditar[bi].addEventListener('click', function () {
                var formEd = document.getElementById('formEditarMultimedia-' + UID);
                if (!formEd) { return; }
                formEd.action = this.getAttribute('data-url-update');

                document.getElementById('edit_media_inmueble-' + UID).value = this.getAttribute('data-inmueble-id');
                document.getElementById('edit_media_tipo-' + UID).value = this.getAttribute('data-tipo-multimedia') || 'imagen';
                document.getElementById('edit_media_orden-' + UID).value = this.getAttribute('data-orden');

                var portadaCheck = document.getElementById('edit_media_portada-' + UID);
                portadaCheck.checked = this.getAttribute('data-es-portada') === '1';

                var editZone = null;
                for (var zi2 = 0; zi2 < activeZones.length; zi2++) {
                    if (activeZones[zi2].modalId === 'modalEditarMultimedia-' + UID) { editZone = activeZones[zi2]; break; }
                }
                if (editZone) { editZone.reset(); }

                var hintTitle = document.getElementById('edit_media_hint_title-' + UID);
                var hint      = document.getElementById('edit_media_hint-' + UID);
                var hintFile  = document.getElementById('edit_media_hint_file-' + UID);

                var nombreInm = this.getAttribute('data-inmueble-nombre') || '—';
                var tipoInm   = this.getAttribute('data-tipo-inmueble') || '';

                if (hintTitle) {
                    var tipo = this.getAttribute('data-tipo-multimedia') === 'video' ? 'Video' : 'Imagen';
                    hintTitle.textContent = tipo + ' · Orden ' + (this.getAttribute('data-orden') || '—');
                }
                if (hint) {
                    hint.innerHTML = 'Inmueble: <b>' + nombreInm + '</b>' + (tipoInm ? ' · Tipo: <b>' + tipoInm + '</b>' : '');
                }
                if (hintFile) {
                    hintFile.innerHTML = 'Editando recurso de <b style="color:#5d8a70;">' + nombreInm + '</b>';
                }
            });
        }

        /* ═══ 5) SWAP INTERNO · repinta #gmRoot (o la ficha N2) con el HTML del fragmento ═══ */
        function gmRunScripts(scope) {
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
        function gmAplicarHtml(html, tipo) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            if (tipo === 'n2') {
                var nuevoShow = tmp.querySelector('#showRoot');
                if (!nuevoShow) { throw new Error('el fragmento no trajo #showRoot'); }
                var actualShow = document.getElementById('showRoot');
                if (!actualShow) { throw new Error('#showRoot ya no está en el DOM'); }
                cerrarModalesDelRoot();
                actualShow.replaceWith(nuevoShow);
                root = nuevoShow.querySelector('.gm-root') || root; /* si la ficha trae otra galería, apuntar a ella */
                gmRunScripts(nuevoShow);
                limpiarResiduosModal();
                return 'ficha N2 repintada';
            }
            var nuevoGm = tmp.querySelector('#gmRoot');
            if (!nuevoGm) { throw new Error('el fragmento no trajo #gmRoot'); }
            var actualGm = (root && root.isConnected) ? root : (document.querySelector('.gm-root') || document.getElementById('gmRoot'));
            if (!actualGm) { throw new Error('#gmRoot actual no está en el DOM'); }
            cerrarModalesDelRoot();
            actualGm.replaceWith(nuevoGm);
            root = nuevoGm;
            gmRunScripts(nuevoGm);
            limpiarResiduosModal();
            return 'vitrina repintada';
        }

        /* ═══ 6) RECARGA POST-GUARDADO · cascada honesta:
           1º · fragmento propio de la galería (data-frag, si la ruta existe)
           2º · ficha N2 (#showRoot data-frag) → repinta la ficha completa con la galería dentro
           3º · recarga nativa de la página (último recurso, los datos YA están guardados)       */
        function recargarContexto(motivo) {
            var intentos = [];
            if (FRAG) {
                var url = FRAG;
                var inm = (GMDBG && GMDBG.inmuebleId) ? GMDBG.inmuebleId : null;
                if (inm) { url += (url.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(inm); }
                intentos.push({ url: url, tipo: 'galeria', etiqueta: 'fragmento propio de la galería' });
            } else {
                DBG.log('sin FRAG (revisa el aviso de arranque) · se intentará con la ficha N2', 'warn');
            }
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (fragN2) {
                var rid = (GMDBG && GMDBG.inmuebleId) ? GMDBG.inmuebleId : 'todos';
                var url2 = fragN2 + (fragN2.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(rid);
                intentos.push({ url: url2, tipo: 'n2', etiqueta: 'ficha N2 (#showRoot)' });
            }
            if (!intentos.length) {
                DBG.log('⚠ sin rutas de recarga AJAX disponibles (' + motivo + ') → recarga nativa', 'warn');
                window.location.reload();
                return;
            }
            gmIntentarRecarga(intentos, 0, motivo);
        }
        function gmIntentarRecarga(intentos, i, motivo) {
            if (i >= intentos.length) {
                DBG.log('⚠ ninguna recarga AJAX funcionó (' + motivo + ') → recarga nativa de la página', 'warn');
                window.location.reload();
                return;
            }
            var it = intentos[i];
            DBG.log('fetch ' + it.url + ' [' + it.etiqueta + ']', 'net');
            fetch(it.url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function (r) {
                if (r.redirected && /\/login(\?|$)/.test(r.url || '')) {
                    throw new Error('la sesión expiró (el servidor redirigió al login); inicia sesión y reintenta');
                }
                if (!r.ok) {
                    return r.text().then(function (t) {
                        var det = extraerErrorLaravel(t);
                        throw new Error('HTTP ' + r.status + (det ? ' · ' + det : ''));
                    });
                }
                return r.text();
            }).then(function (html) {
                var res = gmAplicarHtml(html, it.tipo);
                DBG.log('recargado por AJAX [' + it.etiqueta + '] · ' + html.length + ' bytes · ' + res + ' · sin recargar la página', 'ok');
            }).catch(function (err) {
                DBG.log('intento ' + (i + 1) + '/' + intentos.length + ' falló (' + err.message + ')', 'warn');
                gmIntentarRecarga(intentos, i + 1, motivo);
            });
        }

        /* ═══ 7) FORMULARIOS POR AJAX · agregar / editar recurso (delegación en document) ═══ */
        function gmToast(txt, nivel) {
            var wrap = document.getElementById('gmToastWrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'gmToastWrap';
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
        if (window.__gmFormHandler) { document.removeEventListener('submit', window.__gmFormHandler); }
        window.__gmFormHandler = function (e) {
            var form = e.target;
            if (!form || !form.closest || !form.closest('.gm-root') || !form.hasAttribute('data-gm-form')) { return; }
            e.preventDefault();
            var tipo = form.getAttribute('data-gm-form');
            var btn = form.querySelector('button[type="submit"]');
            var caja = form.querySelector('.gm-form-err');
            if (caja) { caja.style.display = 'none'; caja.innerHTML = ''; }
            if (btn) { btn.disabled = true; }
            DBG.log('submit ' + tipo + ' → ' + form.getAttribute('action') + ' (por AJAX, sin recargar)', 'info');
            fetch(form.getAttribute('action'), {
                method: 'POST', /* Laravel resuelve el PUT vía _method, que viaja dentro del FormData */
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
                    gmToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + (det ? ' · ' + det : ''));
                }
                /* hallazgo 4 · el controlador puede devolver el literal "error" con 200 */
                if (res.html && res.html.trim() === 'error') {
                    throw new Error('el controlador devolvió "error" sin detalles (hallazgo 4)');
                }
                /* Éxito (JSON o HTML tras redirect) → cerrar modal + recargar la vitrina por AJAX */
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                gmToast(tipo === 'store' ? 'Recurso agregado · actualizando vitrina…' : 'Cambios guardados · actualizando vitrina…', 'ok');
                DBG.log('guardado OK (' + (res.json ? 'JSON' : 'respuesta HTML tras redirect') + ') → recargando la vitrina por AJAX', 'ok');
                recargarContexto('tras ' + tipo);
            }).catch(function (err) {
                DBG.log('⚠ guardado falló (' + err.message + ')', 'err');
                gmToast('No se pudo guardar · ' + err.message, 'err');
                DBG.open();
            }).then(function () {
                if (btn) { btn.disabled = false; }
                limpiarResiduosModal();
            });
        };
        document.addEventListener('submit', window.__gmFormHandler);

        DBG.log('interceptores listos · formularios store/update (document, data-gm-form) + paste con guard', 'debug');
    })();
    </script>

</div>
