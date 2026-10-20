@php
    use App\Models\Rsv\InmuebleMultimedia;
    use App\Models\Rsv\CatalogoInmueble;

    // Red de seguridad y fallbacks por si se incluye de forma independiente
    $modoGlobal = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $mediaCollection = $mediaCollection ?? ($modoGlobal ? InmuebleMultimedia::with(['inmueble:id,name,active,tipo_inmueble_id', 'inmueble.tipoInmueble'])->orderBy('orden', 'asc')->get() : $inmueble->multimedia);
    $listaInmueblesOpt = $listaInmueblesOpt ?? CatalogoInmueble::with('tipoInmueble')->orderBy('name', 'asc')->get();
    $gmUid = $gmUid ?? ('gm' . substr(md5(uniqid('', true)), 0, 8));
    $soloLectura = $soloLectura ?? false;

    $tipoInmLabel = function ($inm) {
        $t = $inm?->tipoInmueble;
        return $t ? ($t->nombre ?? $t->name ?? $t->nom_tipo ?? null) : null;
    };
@endphp

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

                            {{-- Vista · miniatura 18px --}}
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
                        <td colspan="7" class="text-center" style="padding: 8px; color: #b3bcc7; font-size: 8.5px;">
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

            <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body" style="padding: 12px 14px;">

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

            <form id="formEditarMultimedia-{{ $gmUid }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding: 12px 14px;">

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


{{-- SCRIPT · Dropzones (pegar/arrastrar/clic) + llenado del modal de edición --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const UID = '{{ $gmUid }}';

        const formatSize = b => b >= 1048576
            ? (b / 1048576).toFixed(1) + ' MB'
            : Math.max(1, Math.round(b / 1024)) + ' KB';

        const isMedia = f => f && (
            f.type.startsWith('image/') || f.type.startsWith('video/')
        );

        const activeZones = [];

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
                prev.classList.add('d-flex');
            };

            const reset = () => {
                input.value = '';
                if (objUrl) { URL.revokeObjectURL(objUrl); objUrl = null; }
                prev.classList.add('d-none');
                prev.classList.remove('d-flex');
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
                    dz.style.borderColor = '#3d7a5c';
                    dz.style.background = '#f0f7f2';
                    dz.style.borderStyle = 'solid';
                })
            );
            dz.addEventListener('dragleave', e => {
                e.preventDefault();
                dz.style.borderColor = '#c9d4de';
                dz.style.background = '#fafbfd';
                dz.style.borderStyle = 'dashed';
            });
            dz.addEventListener('drop', e => {
                e.preventDefault(); e.stopPropagation();
                dz.style.borderColor = '#c9d4de';
                dz.style.background = '#fafbfd';
                dz.style.borderStyle = 'dashed';
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

                const editZone = activeZones.find(z => z.modalId === 'modalEditarMultimedia-' + UID);
                if (editZone) editZone.reset();

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
