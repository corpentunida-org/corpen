@php
    use App\Models\Rsv\InmuebleMultimedia;
    use App\Models\Rsv\CatalogoInmueble;

    // 1. Detectar si estamos en un inmueble específico o en la vista global
    $modoGlobal = !isset($inmueble) || !$inmueble;

    // 2. Cargar colección multimedia filtrada o global
    if (!$modoGlobal) {
        // FILTRO APLICADO: Solo las imágenes de ESTE inmueble
        $mediaCollection = $inmueble->multimedia()
            ->with(['inmueble:id,name,active,tipo_inmueble_id', 'inmueble.tipoInmueble'])
            ->orderBy('orden', 'asc')
            ->get();
    } else {
        // Modo global: carga todo o usa la variable $multimedia si el controlador la envió
        $mediaCollection = isset($multimedia) ? $multimedia : null;
        if ($mediaCollection === null) {
            $mediaCollection = InmuebleMultimedia::with(['inmueble:id,name,active,tipo_inmueble_id', 'inmueble.tipoInmueble'])
                ->orderBy('orden', 'asc')
                ->get();
        } elseif (method_exists($mediaCollection, 'loadMissing')) {
            $mediaCollection->loadMissing('inmueble.tipoInmueble');
        }
    }

    // 3. Cargar lista de inmuebles para los Selects (Modal Crear/Editar)
    $listaInmueblesOpt = isset($listaInmuebles) ? $listaInmuebles : null;
    if ($listaInmueblesOpt === null) {
        $listaInmueblesOpt = CatalogoInmueble::with('tipoInmueble')->orderBy('name', 'asc')->get();
    } elseif (method_exists($listaInmueblesOpt, 'loadMissing')) {
        $listaInmueblesOpt->loadMissing('tipoInmueble');
    }

    // 4. Helper: resuelve el nombre del tipo sin importar cómo se llame la columna
    $tipoInmLabel = function ($inm) {
        $t = $inm?->tipoInmueble;
        return $t ? ($t->nombre ?? $t->name ?? $t->nom_tipo ?? null) : null;
    };

    // ═══ FIX ANTI-CONGELAMIENTO (igual que tarifa) ═══
    // UID único por inclusión: si este partial se renderiza en más de un lugar
    // de la página (show.blade.php + tab del dashboard), cada copia obtiene sus
    // PROPIOS ids de modales/dropzones/campos/script. Sin esto, Bootstrap abre
    // el modal de la copia oculta → backdrop gris que captura los clics.
    $gmUid = 'gm' . substr(md5(uniqid('', true)), 0, 8);
@endphp

{{-- Escala px del sistema (idéntica al módulo de tarifas) --}}
<style>
    .galeria-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }

    /* ═══ TABLA ULTRA-COMPACTA: filas de 20px exactas ═══ */
    .galeria-soft .table > :not(caption) > * > * {
        padding: 2px 6px; height: 20px;
        line-height: 1.1; vertical-align: middle;
    }
    .galeria-soft .table thead th {
        font-size: 9px; font-weight: 600;
        letter-spacing: .05em; text-transform: uppercase;
        color: var(--ink-3); background-color: var(--head);
        border-bottom: 1px solid var(--grid) !important;
        white-space: nowrap;
    }
    .galeria-soft .table tbody td {
        font-size: 8px; color: var(--ink);
        border-bottom: 1px solid var(--grid); border-top: 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .galeria-soft .table tbody tr:hover > * {
        background-color: #fafcfe; --bs-table-accent-bg: transparent;
    }

    /* Celdas con truncado limpio (Inmueble / Tipo Inm.) */
    .galeria-soft .gm-ellip { max-width: 170px; overflow: hidden; text-overflow: ellipsis; }
    .galeria-soft .gm-ellip-sm { max-width: 96px; overflow: hidden; text-overflow: ellipsis; }

    /* Chips compactos (una línea, sin saltos) */
    .galeria-soft .gm-chip {
        display: inline-flex; align-items: center; gap: 2px;
        font-size: 7px; font-weight: 600;
        padding: 0 5px; border-radius: 7px; line-height: 12px;
        border: 1px solid; white-space: nowrap; flex-shrink: 0;
    }
    .galeria-soft .gm-chip i { font-size: 6.5px; line-height: 1; }
    .galeria-soft .gm-chip.img { color: #5b7fa6; background: #f2f6fb; border-color: #dbe6f2; }
    .galeria-soft .gm-chip.vid { color: #8a6fae; background: #f7f3fb; border-color: #e6dcf2; }
    .galeria-soft .gm-chip.ok  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }

    /* Chip del TIPO DE INMUEBLE (verde acento) */
    .galeria-soft .gm-tipo {
        display: inline-flex; align-items: center; gap: 2px;
        font-size: 7px; font-weight: 600; color: var(--accent);
        background: var(--accent-bg); border: 1px solid var(--accent-bd);
        padding: 0 5px; border-radius: 7px; line-height: 12px;
        white-space: nowrap;
    }
    .galeria-soft .gm-tipo i { font-size: 6.5px; line-height: 1; flex-shrink: 0; }
    .galeria-soft .gm-tipo .t { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* Miniatura 18px = altura de fila intacta */
    .galeria-soft .gm-link { line-height: 0; display: inline-block; }
    .galeria-soft .gm-thumb {
        width: 18px; height: 18px; object-fit: cover;
        border-radius: 2px; border: 1px solid var(--grid-2);
        display: block; margin: 0 auto;
    }
    .galeria-soft .gm-thumb-video {
        width: 18px; height: 18px; border-radius: 2px;
        border: 1px solid var(--grid-2); background: var(--head); color: var(--accent);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 8px; margin: 0 auto;
    }

    /* Nombre de inmueble: bold, truncado en su columna */
    .galeria-soft .gm-inmueble {
        display: block; font-size: 8px; font-weight: 600; color: var(--ink-2);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    /* Índice y orden: columna estrecha y centrada */
    .galeria-soft .gm-num { color: var(--ink-4); text-align: center; width: 22px; }

    /* ═══ MODALES — escala px idéntica al panel de tarifas ═══ */
    .galeria-soft-modal .modal-content {
        border: 1px solid var(--grid-2); border-radius: 5px;
        box-shadow: 0 8px 30px rgba(15,23,42,.06);
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px; line-height: 1.4; color: var(--ink);
    }
    .galeria-soft-modal .modal-header { border-bottom: 1px solid var(--grid); padding: 9px 14px; }
    .galeria-soft-modal .modal-title { font-size: 10.5px; font-weight: 600; color: var(--ink-2); }
    .galeria-soft-modal .modal-body { padding: 12px 14px; }
    .galeria-soft-modal .modal-footer {
        border-top: 1px solid var(--grid); padding: 9px 14px;
        display: flex; align-items: center; justify-content: space-between;
    }

    .galeria-soft-modal .tz-section { margin-bottom: 9px; }
    .galeria-soft-modal .tz-section:last-of-type { margin-bottom: 0; }
    .galeria-soft-modal .tz-label {
        display: flex; align-items: center; gap: 5px;
        font-size: 7px; font-weight: 600; letter-spacing: .07em;
        text-transform: uppercase; color: var(--ink-4);
        margin-bottom: 4px;
    }
    .galeria-soft-modal .tz-label i { font-size: 7.5px; opacity: .7; }
    .galeria-soft-modal .tz-label::after { content: ''; flex: 1; height: 1px; background: var(--grid); }

    .galeria-soft-modal .form-label {
        font-size: 7.5px; font-weight: 600; letter-spacing: .05em;
        text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px;
    }
    .galeria-soft-modal .req { color: #c98a85; }
    .galeria-soft-modal .form-control,
    .galeria-soft-modal .form-select {
        font-size: 9.5px; color: var(--ink-2);
        border-color: var(--grid-2); border-radius: 3px;
        padding: 3px 8px; box-shadow: none !important;
    }
    .galeria-soft-modal .form-control:focus,
    .galeria-soft-modal .form-select:focus { border-color: #c8d6cd; }
    .galeria-soft-modal .tz-hint { font-size: 7px; color: #c3ccd6; margin-top: 1px; }

    .galeria-soft-modal .tz-switch-box {
        background: var(--head); border: 1px solid var(--grid);
        border-radius: 4px; padding: 6px 9px;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .galeria-soft-modal .tz-switch-box .sw-title { font-size: 8.5px; font-weight: 600; color: var(--ink-2); }
    .galeria-soft-modal .tz-switch-box .sw-desc  { font-size: 7px; color: var(--ink-4); }
    .galeria-soft-modal .form-check-input { margin-top: 0; }

    /* ═══ DROPZONE: Pegar (Ctrl+V) · Arrastrar · Clic ═══ */
    .galeria-soft-modal .gm-dropzone {
        position: relative;
        border: 1px dashed #c9d4de;
        border-radius: 4px;
        background: #fafbfd;
        overflow: hidden;
        transition: border-color .15s ease, background .15s ease;
    }
    .galeria-soft-modal .gm-dropzone:hover { border-color: #a9c3b2; background: #f7fbf8; }
    .galeria-soft-modal .gm-dropzone.drag-over {
        border-color: var(--accent);
        background: var(--accent-bg);
        border-style: solid;
    }
    .galeria-soft-modal .gm-dz-input {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%;
        opacity: 0; cursor: pointer; z-index: 2;
    }
    .galeria-soft-modal .gm-dz-idle { padding: 14px 10px; text-align: center; }
    .galeria-soft-modal .gm-dz-icon { font-size: 16px; color: #b3bcc7; display: block; margin-bottom: 3px; }
    .galeria-soft-modal .gm-dropzone.drag-over .gm-dz-icon { color: var(--accent); }
    .galeria-soft-modal .gm-dz-text { font-size: 8px; color: var(--ink-3); line-height: 1.5; }
    .galeria-soft-modal .gm-dz-text b { color: var(--accent); }
    .galeria-soft-modal .gm-dz-preview {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 10px; text-align: left;
    }
    .galeria-soft-modal .gm-dz-thumb {
        width: 38px; height: 38px; object-fit: cover;
        border-radius: 3px; border: 1px solid var(--grid-2); flex-shrink: 0;
    }
    .galeria-soft-modal .gm-dz-thumb-video {
        width: 38px; height: 38px; border-radius: 3px;
        background: var(--head); border: 1px solid var(--grid-2);
        color: var(--accent); font-size: 14px;
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .galeria-soft-modal .gm-dz-fname { font-size: 8.5px; font-weight: 600; color: var(--ink-2); }
    .galeria-soft-modal .gm-dz-fsize { font-size: 7px; color: var(--ink-4); }
    .galeria-soft-modal .gm-dz-clear {
        position: relative; z-index: 5;
        background: #fff; border: 1px solid var(--grid-2);
        border-radius: 3px; color: var(--ink-3);
        font-size: 8px; line-height: 1; padding: 3px 5px; flex-shrink: 0;
    }
    .galeria-soft-modal .gm-dz-clear:hover { color: #c0392b; border-color: #eecfc9; background: #fdf5f4; }

    .galeria-soft-modal .btn-cancel-soft {
        background: #fff; color: var(--ink-3);
        border: 1px solid var(--grid-2); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .galeria-soft-modal .btn-cancel-soft:hover { color: var(--ink-2); background: #fafcfe; }
    .galeria-soft-modal .btn-save-soft {
        background: var(--accent-bg); color: var(--accent);
        border: 1px solid var(--accent-bd); border-radius: 3px;
        font-family: inherit; font-size: 9px; font-weight: 600; padding: 3px 14px;
    }
    .galeria-soft-modal .btn-save-soft:hover { background: #e3f1e9; color: #34684e; }
</style>


{{-- ── MÓDULO 3 · VITRINA VISUAL ── --}}
{{-- FIX: sin wrapper tab-pane propio (el dashboard ya lo envuelve; dentro de show      --}}
{{-- es contenido directo). El wrapper duplicado con id="sub-galeria" rompía las tabs.  --}}
<div class="galeria-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-images me-1" style="font-size: 8px; color: #5d8a70;"></i>
            Vitrina Visual y Galería
        </h6>
        @if(empty($soloLectura))
            <button type="button"
                    class="btn btn-sm"
                    style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                    data-bs-toggle="modal" data-bs-target="#modalAddMultimedia-{{ $gmUid }}">
                <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Agregar Recurso
            </button>
        @endif

    </div>

    <div class="table-responsive">

        <table class="table table-sm table-hover align-middle mb-0">

            <thead>
                <tr>
                    <th class="gm-num">#</th>
                    <th class="text-center" style="width: 28px;">Vista</th>
                    <th style="width: 62px;">Tipo</th>
                    <th>Inmueble</th>
                    <th style="width: 100px;">Tipo Inm.</th>
                    <th class="text-center" style="width: 36px;">Ord.</th>
                    @if(empty($soloLectura))
                        <th class="text-center" style="width: 48px;">·</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @if(isset($mediaCollection) && count($mediaCollection) > 0)

                    @foreach($mediaCollection as $media)

                        @php $tipoInm = $tipoInmLabel($media->inmueble); @endphp

                        <tr>

                            <td class="gm-num">{{ $loop->iteration }}</td>

                            <td class="text-center">
                                <a href="{{ $media->url_archivo }}" target="_blank" title="Ver archivo completo" class="gm-link text-decoration-none">
                                    @if($media->tipo_multimedia == 'imagen')
                                        <img src="{{ $media->url_archivo }}" alt="" class="gm-thumb">
                                    @else
                                        <span class="gm-thumb-video"><i class="bi bi-play-fill"></i></span>
                                    @endif
                                </a>
                            </td>

                            {{-- COLUMNA TIPO: solo chips de media y portada --}}
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if($media->tipo_multimedia == 'imagen')
                                        <span class="gm-chip img"><i class="bi bi-image"></i>IMG</span>
                                    @else
                                        <span class="gm-chip vid"><i class="bi bi-camera-video"></i>VID</span>
                                    @endif

                                    @if($media->es_portada)
                                        <span class="gm-chip ok" title="Portada principal"><i class="bi bi-star-fill"></i>P</span>
                                    @endif
                                </div>
                            </td>

                            {{-- COLUMNA INMUEBLE: nombre truncado --}}
                            <td class="gm-ellip">
                                <span class="gm-inmueble" title="{{ $media->inmueble->name ?? '—' }}">
                                    {{ $media->inmueble->name ?? '—' }}
                                </span>
                            </td>

                            {{-- COLUMNA TIPO INMUEBLE: chip verde o guion --}}
                            <td class="gm-ellip-sm">
                                @if($tipoInm)
                                    <span class="gm-tipo" title="{{ $tipoInm }}">
                                        <i class="bi bi-tag-fill"></i><span class="t">{{ $tipoInm }}</span>
                                    </span>
                                @else
                                    <span style="color: var(--ink-4);">—</span>
                                @endif
                            </td>

                            <td class="gm-num">{{ $media->orden }}</td>
                            @if(empty($soloLectura))
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-link p-0 text-decoration-none btn-editar-multimedia-{{ $gmUid }}"
                                            style="font-size: 8px; color: #3d7a5c; font-weight: 600;"
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
                        <td colspan="7" class="text-center py-2" style="color: #b3bcc7; font-size: 8.5px;">
                            <i class="bi bi-images me-1"></i> Vitrina vacía. Sube fotos o videos.
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>

    </div>

</div>


{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL 3A · AGREGAR RECURSO MULTIMEDIA (ID único por inclusión)   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade galeria-soft-modal" id="modalAddMultimedia-{{ $gmUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Vitrina Visual
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" style="font-size: 0.95rem;">Agregar Recurso Multimedia</h6>
                    <small class="text-muted" style="font-size: 7.5px;">Los campos con <b>*</b> son obligatorios</small>
                </div>
            </div>

            <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Identificación</div>

                        <div class="mb-2">
                            <label class="form-label">Inmueble <span class="req">*</span></label>
                            <select class="form-select form-select-sm" name="id_rsv_catalogo_inmueble" required>
                                <option value="">-- Seleccione un inmueble --</option>
                                @foreach ($listaInmueblesOpt as $propOpt)
                                    @php $tInmOpt = $tipoInmLabel($propOpt); @endphp
                                    {{-- SELECCIÓN AUTOMÁTICA si estamos viendo un inmueble en específico --}}
                                    <option value="{{ $propOpt->id }}" {{ (!$modoGlobal && isset($inmueble) && $inmueble->id == $propOpt->id) ? 'selected' : '' }}>
                                        {{ $propOpt->name }}{{ isset($propOpt->city) && $propOpt->city ? ' (' . $propOpt->city . ')' : '' }}{{ $tInmOpt ? ' · ' . $tInmOpt : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Tipo de multimedia <span class="req">*</span></label>
                            <select class="form-select form-select-sm" name="tipo_multimedia" required>
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                            <div class="tz-hint">Define cómo se renderiza en la vitrina pública</div>
                        </div>
                    </div>


                    {{-- 2 · ARCHIVO (Pegar · Arrastrar · Clic) --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-cloud-arrow-up"></i> Archivo (S3) <span class="req">*</span></div>

                        <div class="gm-dropzone" id="dzAdd-{{ $gmUid }}">
                            <input type="file" class="gm-dz-input" name="url_archivo" accept="image/*,video/*" required id="inputAddFile-{{ $gmUid }}">

                            {{-- Estado: esperando archivo --}}
                            <div class="gm-dz-idle" id="dzAddIdle-{{ $gmUid }}">
                                <i class="bi bi-cloud-arrow-up gm-dz-icon"></i>
                                <div class="gm-dz-text">
                                    Pega con <b>Ctrl + V</b>, arrastra el archivo aquí<br>
                                    o haz clic para explorar
                                </div>
                            </div>

                            {{-- Estado: archivo cargado (vista previa) --}}
                            <div class="gm-dz-preview d-none" id="dzAddPreview-{{ $gmUid }}">
                                <img id="dzAddThumb-{{ $gmUid }}" class="gm-dz-thumb d-none" alt="">
                                <span id="dzAddThumbV-{{ $gmUid }}" class="gm-dz-thumb-video d-none"><i class="bi bi-camera-video"></i></span>
                                <div style="min-width: 0;">
                                    <div class="gm-dz-fname text-truncate" id="dzAddName-{{ $gmUid }}"></div>
                                    <div class="gm-dz-fsize" id="dzAddSize-{{ $gmUid }}"></div>
                                </div>
                                <button type="button" class="gm-dz-clear" id="dzAddClear-{{ $gmUid }}" title="Quitar archivo">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="tz-hint">Imágenes: JPG, PNG, WEBP · Videos: MP4, WEBM</div>
                    </div>


                    {{-- 3 · ORDEN Y PORTADA --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-sliders"></i> Orden y Portada</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label">Orden de aparición</label>
                                <input type="number" class="form-control form-control-sm" name="orden" placeholder="Ej. 1" min="1">
                                <div class="tz-hint">Vacío = al final</div>
                            </div>

                            <div class="col-7">
                                <label class="form-label">Portada</label>
                                <div class="tz-switch-box">
                                    <div class="text-start">
                                        <div class="sw-title">Portada principal</div>
                                        <div class="sw-desc">Aparece primero en la vitrina</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="addMediaPortada-{{ $gmUid }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 7.5px;">Se muestra en la vitrina del inmueble</span>
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
{{-- MODAL 3B · EDITAR RECURSO MULTIMEDIA (ID único por inclusión)   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade galeria-soft-modal" id="modalEditarMultimedia-{{ $gmUid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border shadow-sm">

            <div class="modal-header bg-light border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                <div class="text-end">
                    <div style="font-size: 8px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #6c757d;">
                        Vitrina Visual · Editando
                    </div>
                    <h6 class="modal-title mb-0 text-dark fw-bold" id="edit_media_hint_title-{{ $gmUid }}" style="font-size: 0.95rem;">Recurso Multimedia</h6>
                    <small class="text-muted" id="edit_media_hint-{{ $gmUid }}" style="font-size: 7.5px;">Selecciona un recurso…</small>
                </div>
            </div>

            <form id="formEditarMultimedia-{{ $gmUid }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body p-3">

                    {{-- 1 · IDENTIFICACIÓN --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-tag"></i> Identificación</div>

                        <div class="mb-2">
                            <label class="form-label">Inmueble <span class="req">*</span></label>
                            <select class="form-select form-select-sm" id="edit_media_inmueble-{{ $gmUid }}" name="id_rsv_catalogo_inmueble" required>
                                <option value="">-- Seleccione un inmueble --</option>
                                @foreach ($listaInmueblesOpt as $propOpt)
                                    @php $tInmOpt = $tipoInmLabel($propOpt); @endphp
                                    <option value="{{ $propOpt->id }}">{{ $propOpt->name }}{{ isset($propOpt->city) && $propOpt->city ? ' (' . $propOpt->city . ')' : '' }}{{ $tInmOpt ? ' · ' . $tInmOpt : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Tipo de multimedia <span class="req">*</span></label>
                            <select class="form-select form-select-sm" id="edit_media_tipo-{{ $gmUid }}" name="tipo_multimedia" required>
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                    </div>


                    {{-- 2 · REEMPLAZAR ARCHIVO (Pegar · Arrastrar · Clic) --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-cloud-arrow-up"></i> Reemplazar Archivo</div>

                        <div class="gm-dropzone" id="dzEdit-{{ $gmUid }}">
                            <input type="file" class="gm-dz-input" name="url_archivo" accept="image/*,video/*" id="inputEditFile-{{ $gmUid }}">

                            <div class="gm-dz-idle" id="dzEditIdle-{{ $gmUid }}">
                                <i class="bi bi-cloud-arrow-up gm-dz-icon"></i>
                                <div class="gm-dz-text">
                                    Pega con <b>Ctrl + V</b> o arrastra para reemplazar<br>
                                    Dejar vacío conserva el archivo actual
                                </div>
                            </div>

                            <div class="gm-dz-preview d-none" id="dzEditPreview-{{ $gmUid }}">
                                <img id="dzEditThumb-{{ $gmUid }}" class="gm-dz-thumb d-none" alt="">
                                <span id="dzEditThumbV-{{ $gmUid }}" class="gm-dz-thumb-video d-none"><i class="bi bi-camera-video"></i></span>
                                <div style="min-width: 0;">
                                    <div class="gm-dz-fname text-truncate" id="dzEditName-{{ $gmUid }}"></div>
                                    <div class="gm-dz-fsize" id="dzEditSize-{{ $gmUid }}"></div>
                                </div>
                                <button type="button" class="gm-dz-clear" id="dzEditClear-{{ $gmUid }}" title="Quitar y conservar el actual">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>


                    {{-- 3 · ORDEN Y PORTADA --}}
                    <div class="tz-section">
                        <div class="tz-label"><i class="bi bi-sliders"></i> Orden y Portada</div>

                        <div class="row g-2 align-items-center">
                            <div class="col-5">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control form-control-sm" id="edit_media_orden-{{ $gmUid }}" name="orden" min="1">
                            </div>

                            <div class="col-7">
                                <label class="form-label">Portada</label>
                                <div class="tz-switch-box">
                                    <div class="text-start">
                                        <div class="sw-title">Portada principal</div>
                                        <div class="sw-desc">Se muestra primero en la vitrina</div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="edit_media_portada-{{ $gmUid }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <span class="text-muted" id="edit_media_hint_file-{{ $gmUid }}" style="font-size: 7.5px;"></span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- SCRIPT · Dropzones (pegar/arrastrar/clic) + llenado del modal de edición --}}
{{-- Scoped por UID: cada copia de este partial solo controla SUS elementos --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const UID = '{{ $gmUid }}';

        /* ══════════════════════════════════════════════════════════
           HELPERS
           ══════════════════════════════════════════════════════════ */
        const formatSize = b => b >= 1048576
            ? (b / 1048576).toFixed(1) + ' MB'
            : Math.max(1, Math.round(b / 1024)) + ' KB';

        const isMedia = f => f && (
            f.type.startsWith('image/') || f.type.startsWith('video/')
        );

        const activeZones = [];

        /* ══════════════════════════════════════════════════════════
           DROPZONE · Ctrl+V / Drag & Drop / Clic
           ══════════════════════════════════════════════════════════ */
        function setupDropzone(cfg) {
            const dz     = document.getElementById(cfg.dz);
            const input  = document.getElementById(cfg.input);
            const idle   = document.getElementById(cfg.idle);
            const prev   = document.getElementById(cfg.preview);
            const thumb  = document.getElementById(cfg.thumb);
            const thumbV = document.getElementById(cfg.thumbVideo);
            const nameEl = document.getElementById(cfg.name);
            const sizeEl = document.getElementById(cfg.size);
            const clearB = document.getElementById(cfg.clear);
            if (!dz || !input) return;
            let objUrl = null;

            const showPreview = (file) => {
                if (objUrl) URL.revokeObjectURL(objUrl);
                objUrl = URL.createObjectURL(file);
                const isImg = file.type.startsWith('image/');
                thumb.classList.toggle('d-none', !isImg);
                thumbV.classList.toggle('d-none', isImg);
                if (isImg) thumb.src = objUrl;
                nameEl.textContent = file.name;
                sizeEl.textContent = formatSize(file.size);
                idle.classList.add('d-none');
                prev.classList.remove('d-none');
            };

            const reset = () => {
                input.value = '';
                if (objUrl) { URL.revokeObjectURL(objUrl); objUrl = null; }
                prev.classList.add('d-none');
                idle.classList.remove('d-none');
            };

            const setFile = (file) => {
                if (!isMedia(file)) return;
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showPreview(file);
            };

            input.addEventListener('change', () => {
                (input.files && input.files[0]) ? showPreview(input.files[0]) : reset();
            });

            clearB.addEventListener('click', e => {
                e.preventDefault(); e.stopPropagation(); reset();
            });

            ['dragenter', 'dragover'].forEach(ev =>
                dz.addEventListener(ev, e => {
                    e.preventDefault(); e.stopPropagation();
                    dz.classList.add('drag-over');
                })
            );
            dz.addEventListener('dragleave', e => {
                e.preventDefault(); dz.classList.remove('drag-over');
            });
            dz.addEventListener('drop', e => {
                e.preventDefault(); e.stopPropagation();
                dz.classList.remove('drag-over');
                const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                if (file) setFile(file);
            });

            activeZones.push({ modalId: cfg.modal, setFile, reset });
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

        /* ══════════════════════════════════════════════════════════
           PEGAR (Ctrl+V) · Solo si el modal de ESTA copia está abierto
           ══════════════════════════════════════════════════════════ */
        document.addEventListener('paste', e => {
            const items = e.clipboardData && e.clipboardData.items;
            if (!items) return;
            for (const zone of activeZones) {
                const modal = document.getElementById(zone.modalId);
                if (!modal || !modal.classList.contains('show')) continue;
                for (const item of items) {
                    if (item.kind === 'file') {
                        const file = item.getAsFile();
                        if (file && isMedia(file)) {
                            zone.setFile(file);
                            e.preventDefault();
                            return;
                        }
                    }
                }
            }
        });

        /* Evita que el navegador abra la imagen si se suelta fuera de la zona */
        ['dragover', 'drop'].forEach(ev =>
            document.addEventListener(ev, e => {
                if (e.target.closest && e.target.closest('.gm-dropzone')) return;
                if (document.querySelector('.galeria-soft-modal.show')) e.preventDefault();
            })
        );

        /* ══════════════════════════════════════════════════════════
           LLENADO DEL MODAL DE EDICIÓN (scoped por UID)
           ══════════════════════════════════════════════════════════ */
        const botonesEditar = document.querySelectorAll('.btn-editar-multimedia-' + UID);
        botonesEditar.forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.getElementById('formEditarMultimedia-' + UID);
                form.action = this.getAttribute('data-url-update');

                document.getElementById('edit_media_inmueble-' + UID).value = this.getAttribute('data-inmueble-id');
                document.getElementById('edit_media_tipo-' + UID).value = this.getAttribute('data-tipo-multimedia') || 'imagen';
                document.getElementById('edit_media_orden-' + UID).value = this.getAttribute('data-orden');

                const portadaCheck = document.getElementById('edit_media_portada-' + UID);
                portadaCheck.checked = this.getAttribute('data-es-portada') === '1';

                /* Resetear la dropzone de edición al abrir otro registro */
                const editZone = activeZones.find(z => z.modalId === 'modalEditarMultimedia-' + UID);
                if (editZone) editZone.reset();

                /* Contexto del header/footer */
                const hintTitle = document.getElementById('edit_media_hint_title-' + UID);
                const hint      = document.getElementById('edit_media_hint-' + UID);
                const hintFile  = document.getElementById('edit_media_hint_file-' + UID);

                const nombreInm = this.getAttribute('data-inmueble-nombre') || '—';
                const tipoInm   = this.getAttribute('data-tipo-inmueble') || '';

                if (hintTitle) {
                    const tipo = this.getAttribute('data-tipo-multimedia') === 'video' ? 'Video' : 'Imagen';
                    hintTitle.textContent = tipo + ' · Orden ' + (this.getAttribute('data-orden') || '—');
                }

                if (hint) {
                    hint.innerHTML = 'Inmueble: <b>' + nombreInm + '</b>'
                        + (tipoInm ? ' · Tipo: <b>' + tipoInm + '</b>' : '');
                }

                if (hintFile) {
                    hintFile.innerHTML = 'Editando recurso de <b style="color:#5d8a70;">' + nombreInm + '</b>';
                }
            });
        });
    });
</script>
