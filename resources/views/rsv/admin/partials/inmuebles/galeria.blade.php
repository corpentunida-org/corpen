{{-- resources/views/rsv/admin/partials/inmuebles/galeria.blade.php · GM-v1.2-DBG · NIVEL 3 (vitrina visual / galería multimedia) --}}
@php
    use App\Models\Rsv\InmuebleMultimedia;
    use App\Models\Rsv\CatalogoInmueble;

    $modoGlobal  = $modoGlobal ?? (!isset($inmueble) || !$inmueble);
    $soloLectura = $soloLectura ?? false;
    $gmUid       = $gmUid ?? ('gm' . substr(md5(uniqid('', true)), 0, 8));

    $gmDbg = [
        'marca'       => 'GM-v1.2-DBG',
        'vista'       => 'rsv.admin.partials.inmuebles.galeria',
        'url'         => url()->full(),
        'peticion'    => request()->ajax() ? 'AJAX (X-Requested-With)' : 'navegación normal',
        'modo'        => $modoGlobal ? 'global (todas las propiedades)' : 'individual',
        'inmuebleId'  => (!$modoGlobal && isset($inmueble) && $inmueble) ? $inmueble->id : ($inmuebleId ?? null),
        'soloLectura' => empty($soloLectura) ? false : true,
        'uid'         => $gmUid,
        'media'       => 0,
        'imagenes'    => 0,
        'videos'      => 0,
        'portadas'    => 0,
        'listaOpt'    => 0,
        'fragRuta'    => null,
        'rutas'       => [
            'store'  => \Illuminate\Support\Facades\Route::has('rsv.inmueble-multimedia.store') ? route('rsv.inmueble-multimedia.store') : null,
            'update' => 'rsv.inmueble-multimedia.update (por fila)',
        ],
        'validacion'  => [],
        'errores'     => [],
    ];
    if (isset($errors) && $errors->any()) {
        $gmDbg['validacion'] = $errors->all();
    }

    $gmFrag = null;
    try {
        $gmFrag = route('rsv.admin.inmuebles.galeria.fragmento');
        $gmDbg['fragRuta'] = 'rsv.admin.inmuebles.galeria.fragmento';
    } catch (\Throwable $e) {
        $gmDbg['errores'][] = 'ruta "rsv.admin.inmuebles.galeria.fragmento" no definida · se usará respaldo N2 (#showRoot)';
    }

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

    $mediaColSafe = collect($mediaCollection ?? []);

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

    $cntTotalMed = $mediaColSafe->count();
    $cntImgMed   = $mediaColSafe->filter(fn ($m) => ($m->tipo_multimedia ?? '') === 'imagen')->count();
    $cntVidMed   = $mediaColSafe->filter(fn ($m) => ($m->tipo_multimedia ?? '') === 'video')->count();
    $cntPortMed  = $mediaColSafe->filter(fn ($m) => !empty($m->es_portada))->count();

    $gmDbg['media']    = $cntTotalMed;
    $gmDbg['imagenes'] = $cntImgMed;
    $gmDbg['videos']   = $cntVidMed;
    $gmDbg['portadas'] = $cntPortMed;
    $gmDbg['listaOpt'] = is_countable($listaInmueblesOpt) ? count($listaInmueblesOpt) : 0;
@endphp

{{-- GM-v1.2 · Tab DBG textual + LED + Panel LOCAL (fuera del root: mueren con el fragmento · NO global) --}}
<div id="gmDbgToggle" title="Debug Galería (GM-v1.2-DBG)">
    <span class="gm-dbg-led" id="gmDbgLed"></span>DBG<span class="gm-dbg-badge" id="gmDbgBadge">0</span>
</div>
<div id="gmDbgPanel" role="dialog" aria-label="Panel de debug de Galería">
    <div class="gm-dbg-header" id="gmDbgHeader">
        <span>DEBUG · Galería (N3) · GM-v1.2-DBG</span>
        <span class="gm-dbg-controls">
            <button type="button" class="gm-dbg-btn" id="gmDbgRefresh" title="Refrescar fragmento de galería">↻ Frag</button>
            <button type="button" class="gm-dbg-btn" id="gmDbgCopy">Copiar</button>
            <button type="button" class="gm-dbg-btn" id="gmDbgClear">Limpiar</button>
            <button type="button" class="gm-dbg-btn" id="gmDbgClose">✕</button>
        </span>
    </div>
    <div id="gmDbgBody"></div>
</div>

{{-- GM-v1.2-DBG · RAÍZ ESTABLE --}}
<div id="gmRoot" class="gm-root"
     data-version="GM-v1.2-DBG"
     data-frag="{{ $gmFrag }}"
     @if(!$modoGlobal && isset($inmueble) && $inmueble) data-inmueble-id="{{ $inmueble->id }}" @endif>

    <style>
        .gm-root .gm-filter-chip {
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
        .gm-root .gm-filter-chip:hover { color: #334155; background: #f8fafc; }
        .gm-root .gm-filter-chip.active {
            background: #f0f7f2;
            color: #3d7a5c;
            border-color: #d5e7dc;
            font-weight: 700;
        }
        .gm-root .gm-search-inp {
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
        .gm-root .gm-search-inp:focus {
            background: #fff;
            border-color: #d5e7dc;
            width: 190px;
        }
        .gm-root .gm-btn-zoom {
            border: 0;
            background: transparent;
            color: #94a3b8;
            font-size: 8px;
            padding: 0 3px;
            cursor: pointer;
            line-height: 1;
            vertical-align: middle;
        }
        .gm-root .gm-btn-zoom:hover { color: #3d7a5c; }
    </style>

    {{-- ── MÓDULO 3 · VITRINA VISUAL ── --}}
    <div class="p-3 bg-white rounded-3 border" style="border-color: #f1f5f9;">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">

            <div class="d-flex align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0 d-flex align-items-center" style="font-size: 9.5px; color: #334155;">
                    <i class="bi bi-images me-1" style="font-size: 8px; color: #5d8a70;"></i>
                    Vitrina Visual y Galería
                    <span class="badge rounded-pill ms-1" style="font-size:6.5px; background:#f0f7f2; color:#3d7a5c; border:1px solid #d5e7dc; vertical-align:middle;">GM-v1.2-DBG</span>
                </h6>

                @if($cntTotalMed > 0)
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="gm-filter-chip active" data-gm-filter="all">
                            Todos ({{ $cntTotalMed }})
                        </button>
                        <button type="button" class="gm-filter-chip" data-gm-filter="imagen">
                            Imágenes ({{ $cntImgMed }})
                        </button>
                        <button type="button" class="gm-filter-chip" data-gm-filter="video">
                            Videos ({{ $cntVidMed }})
                        </button>
                        @if($cntPortMed > 0)
                            <button type="button" class="gm-filter-chip" data-gm-filter="portada">
                                Portadas ({{ $cntPortMed }})
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                @if($cntTotalMed > 0)
                    <input type="text" class="gm-search-inp js-gm-search" placeholder="Buscar inmueble o tipo…">
                @endif

                @if(empty($soloLectura))
                    <button type="button"
                            class="btn btn-sm d-inline-flex align-items-center"
                            style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-size: 8.5px; font-weight: 600; padding: 2px 9px; border-radius: 3px;"
                            data-bs-toggle="modal" data-bs-target="#modalAddMultimedia-{{ $gmUid }}">
                        <i class="bi bi-plus-lg me-1" style="font-size: 8px;"></i> Agregar Recurso
                    </button>
                @endif
            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-sm table-hover align-middle mb-0"
                   style="font-size: 8px; color: #64748b; line-height: 1.1; --bs-table-cell-padding-x: 6px; --bs-table-cell-padding-y: 2px; --bs-table-hover-bg: #fafcfe; --bs-table-hover-color: #64748b;">

                <thead style="background-color: #fafbfd;">
                    <tr>
                        <th class="text-center" style="width: 22px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">#</th>
                        <th class="text-center" style="width: 44px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Vista</th>
                        <th style="width: 68px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Tipo</th>
                        <th style="font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Inmueble</th>
                        <th style="width: 100px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Tipo Inm.</th>
                        <th class="text-center" style="width: 36px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">Ord.</th>
                        @if(empty($soloLectura))
                            <th class="text-center" style="width: 48px; font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #b3bcc7; border-bottom: 1px solid #f1f5f9;">·</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @if($cntTotalMed > 0)

                        @foreach($mediaColSafe as $media)

                            @php
                                $tipoInm     = $tipoInmLabel($media->inmueble);
                                $tipoMed     = $media->tipo_multimedia ?? 'imagen';
                                $esPortada   = !empty($media->es_portada);
                                $nomInmMed   = $media->inmueble->name ?? '—';
                                $searchMed   = mb_strtolower($nomInmMed . ' ' . ($tipoInm ?? '') . ' ' . $tipoMed . ' ' . ($esPortada ? 'portada' : ''));
                            @endphp

                            <tr class="js-gm-row"
                                data-gm-tipo="{{ $tipoMed }}"
                                data-gm-portada="{{ $esPortada ? '1' : '0' }}"
                                data-gm-search="{{ $searchMed }}">

                                <td class="text-center" style="color: #b3bcc7;">{{ $loop->iteration }}</td>

                                {{-- Vista · miniatura 18px + visor rápido en modal --}}
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="gap: 2px;">
                                        <a href="{{ $media->url_archivo }}" target="_blank" title="Ver archivo completo" class="text-decoration-none" style="line-height: 0; display: inline-block;">
                                            @if($tipoMed === 'imagen')
                                                <img src="{{ $media->url_archivo }}" alt="" style="width: 18px; height: 18px; object-fit: cover; border-radius: 2px; border: 1px solid #e9eef4; display: block;">
                                            @else
                                                <span style="width: 18px; height: 18px; border-radius: 2px; border: 1px solid #e9eef4; background: #fafbfd; color: #3d7a5c; display: inline-flex; align-items: center; justify-content: center; font-size: 8px;">
                                                    <i class="bi bi-play-fill"></i>
                                                </span>
                                            @endif
                                        </a>
                                        <button type="button"
                                                class="gm-btn-zoom js-gm-zoom"
                                                title="Previsualizar en ventana"
                                                data-url="{{ $media->url_archivo }}"
                                                data-tipo="{{ $tipoMed }}"
                                                data-titulo="{{ $nomInmMed }} · {{ strtoupper($tipoMed) }}">
                                            <i class="bi bi-arrows-angle-expand"></i>
                                        </button>
                                    </div>
                                </td>

                                {{-- Tipo · chips 7px --}}
                                <td>
                                    <div class="d-flex align-items-center" style="gap: 2px;">
                                        @if($tipoMed === 'imagen')
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5b7fa6; background: #f2f6fb; border: 1px solid #dbe6f2; white-space: nowrap;">
                                                <i class="bi bi-image" style="font-size: 6.5px; line-height: 1;"></i>IMG
                                            </span>
                                        @else
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #8a6fae; background: #f7f3fb; border: 1px solid #e6dcf2; white-space: nowrap;">
                                                <i class="bi bi-camera-video" style="font-size: 6.5px; line-height: 1;"></i>VID
                                            </span>
                                        @endif

                                        @if($esPortada)
                                            <span class="badge d-inline-flex align-items-center" style="gap: 2px; font-size: 7px; font-weight: 600; padding: 0 5px; border-radius: 7px; line-height: 12px; color: #5d8a70; background: #f0f7f2; border: 1px solid #d8e9de; white-space: nowrap;" title="Portada principal">
                                                <i class="bi bi-star-fill" style="font-size: 6.5px; line-height: 1;"></i>P
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Inmueble · truncate 170px --}}
                                <td style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <span class="fw-semibold" style="font-size: 8px; color: #475569; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $nomInmMed }}">
                                        {{ $nomInmMed }}
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
                                                data-tipo-multimedia="{{ $tipoMed }}"
                                                data-orden="{{ $media->orden }}"
                                                data-es-portada="{{ $esPortada ? 1 : 0 }}"
                                                data-inmueble-id="{{ $media->id_rsv_catalogo_inmueble }}"
                                                data-inmueble-nombre="{{ $nomInmMed }}"
                                                data-tipo-inmueble="{{ $tipoInm ?? '' }}">
                                            Editar
                                        </button>
                                    </td>
                                @endif

                            </tr>

                        @endforeach

                        <tr class="js-gm-empty-filter" style="display:none;">
                            <td colspan="{{ empty($soloLectura) ? 7 : 6 }}" class="text-center" style="padding: 8px; color: #94a3b8; font-size: 8px;">
                                <i class="bi bi-funnel me-1"></i> Ningún recurso coincide con el filtro activo.
                            </td>
                        </tr>

                    @else
                        <tr>
                            <td colspan="{{ empty($soloLectura) ? 7 : 6 }}" class="text-center" style="padding: 8px; color: #b3bcc7; font-size: 8.5px;">
                                <i class="bi bi-images me-1"></i> Vitrina vacía. Sube fotos o videos.
                            </td>
                        </tr>
                    @endif
                </tbody>

            </table>

        </div>

    </div>

    {{-- MODAL VISOR RÁPIDO DE RECURSO MULTIMEDIA --}}
    <div class="modal fade" id="modalPreviewMultimedia-{{ $gmUid }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 6px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 8px 12px;">
                    <h6 class="modal-title mb-0 fw-bold" id="gmPreviewTitle-{{ $gmUid }}" style="font-size: 9.5px; color: #334155;">Vista Previa</h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" target="_blank" rel="noopener noreferrer" id="gmPreviewExt-{{ $gmUid }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 8px;">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    </div>
                </div>
                <div class="modal-body p-2 bg-light text-center" id="gmPreviewBody-{{ $gmUid }}" style="min-height: 220px; display: flex; align-items: center; justify-content: center;"></div>
            </div>
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

                <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST" enctype="multipart/form-data"
                      id="formAgregarMultimedia-{{ $gmUid }}" data-gm-form="store">
                    @csrf

                    <div class="modal-body" style="padding: 12px 14px;">

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
                                            <input type="hidden" name="es_portada" value="0">
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

                <form id="formEditarMultimedia-{{ $gmUid }}" method="POST" enctype="multipart/form-data" data-gm-form="update">
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="padding: 12px 14px;">

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
                                            <input type="hidden" name="es_portada" value="0">
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

    <script>
    /* ═══════════════ GM-v1.2-DBG · galeria · NIVEL 3 ═══════════════ */
    (function () {
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
                    var rootDbg = document.getElementById('gmRoot');
                    if (!rootDbg) { return; }
                    if (!$('gmDbgToggle')) {
                        var t = document.createElement('div');
                        t.id = 'gmDbgToggle';
                        t.title = 'Debug Galería (GM-v1.2-DBG)';
                        t.innerHTML = '<span class="gm-dbg-led" id="gmDbgLed"></span>DBG<span class="gm-dbg-badge" id="gmDbgBadge">0</span>';
                        rootDbg.insertBefore(t, rootDbg.firstChild);
                    }
                    if (!$('gmDbgPanel')) {
                        var p = document.createElement('div');
                        p.id = 'gmDbgPanel';
                        p.innerHTML = '<div class="gm-dbg-header" id="gmDbgHeader">' +
                            '<span>DEBUG · Galería (N3) · GM-v1.2-DBG</span>' +
                            '<span class="gm-dbg-controls">' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgRefresh">↻ Frag</button>' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgCopy">Copiar</button>' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgClear">Limpiar</button>' +
                            '<button type="button" class="gm-dbg-btn" id="gmDbgClose">✕</button>' +
                            '</span></div><div id="gmDbgBody"></div>';
                        rootDbg.appendChild(p);
                    }
                    apilar();
                }
                function apilar() {
                    var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle', 'psDbgToggle', 'trxDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                    var ocupado = 0;
                    for (var i = 0; i < ids.length; i++) {
                        var el = $(ids[i]);
                        if (!el) { continue; }
                        var r = el.getBoundingClientRect();
                        if (!r.width && !r.height) { continue; }
                        var desdeAbajo = window.innerHeight - r.top;
                        if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                    }
                    var t = $('gmDbgToggle'), p = $('gmDbgPanel');
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
                        var txt = '=== DEBUG galeria GM-v1.2-DBG · ' + new Date().toISOString() + ' ===\n';
                        for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(txt).then(function () {
                                api.log('log copiado al portapapeles', 'ok');
                            });
                        }
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
        DBG.ensure();

        if (window.__gmDbgDocHandler) { document.removeEventListener('click', window.__gmDbgDocHandler); }
        window.__gmDbgDocHandler = function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }
            if (t.closest('#gmDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#gmDbgClose')) { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#gmDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#gmDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#gmDbgRefresh')) {
                e.stopPropagation();
                recargarContexto('manual-btn-dbg');
                return;
            }

            // Chips de filtro rápido (Todos / Imágenes / Videos / Portadas)
            var chip = t.closest('.gm-root .gm-filter-chip');
            if (chip) {
                var sc = chip.closest('.gm-root');
                if (sc) {
                    sc.querySelectorAll('.gm-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                    chip.classList.add('active');
                    aplicarFiltroGaleria(sc, true);
                }
                return;
            }

            // Visor rápido en modal al pulsar botón de expandir miniatura
            var zoomBtn = t.closest('.js-gm-zoom');
            if (zoomBtn) {
                e.preventDefault();
                var urlMed = zoomBtn.getAttribute('data-url') || '';
                var tipoMed = zoomBtn.getAttribute('data-tipo') || 'imagen';
                var titMed = zoomBtn.getAttribute('data-titulo') || 'Recurso';
                var mPrevEl = document.getElementById('modalPreviewMultimedia-' + UID);
                var pTitle  = document.getElementById('gmPreviewTitle-' + UID);
                var pBody   = document.getElementById('gmPreviewBody-' + UID);
                var pExt    = document.getElementById('gmPreviewExt-' + UID);

                if (pTitle) pTitle.textContent = titMed;
                if (pExt)   pExt.href = urlMed;
                if (pBody) {
                    if (tipoMed === 'video') {
                        pBody.innerHTML = '<video src="' + urlMed + '" controls autoplay style="max-width:100%;max-height:360px;border-radius:4px;"></video>';
                    } else {
                        pBody.innerHTML = '<img src="' + urlMed + '" alt="" style="max-width:100%;max-height:360px;object-fit:contain;border-radius:4px;">';
                    }
                }
                if (mPrevEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(mPrevEl).show();
                }
            }
        };
        document.addEventListener('click', window.__gmDbgDocHandler);

        if (window.__gmDbgDblHandler) { document.removeEventListener('dblclick', window.__gmDbgDblHandler); }
        window.__gmDbgDblHandler = function (e) {
            if (e.target && e.target.closest && e.target.closest('#gmDbgHeader')) { DBG.close(); }
        };
        document.addEventListener('dblclick', window.__gmDbgDblHandler);

        DBG.log('GM-v1.2-DBG activo · modo=' + (GMDBG ? GMDBG.modo : '?') +
            ' · media=' + (GMDBG ? GMDBG.media : 0) +
            ' (img=' + (GMDBG ? GMDBG.imagenes : 0) + ', vid=' + (GMDBG ? GMDBG.videos : 0) + ', port=' + (GMDBG ? GMDBG.portadas : 0) + ')', 'ok');

        /* ═══ FILTRO EN VIVO DE VITRINA ═══ */
        function aplicarFiltroGaleria(scope, logEv) {
            var sc = scope || root;
            if (!sc) return;
            var chipAct = sc.querySelector('.gm-filter-chip.active');
            var fKey = chipAct ? chipAct.getAttribute('data-gm-filter') : 'all';
            var q = (sc.querySelector('.js-gm-search')?.value || '').toLowerCase().trim();
            var visibles = 0;

            var filas = sc.querySelectorAll('.js-gm-row');
            filas.forEach(function (tr) {
                var tp   = tr.getAttribute('data-gm-tipo') || '';
                var port = tr.getAttribute('data-gm-portada') || '0';
                var blob = tr.getAttribute('data-gm-search') || '';

                var okTipo = true;
                if (fKey === 'imagen' || fKey === 'video') okTipo = (tp === fKey);
                else if (fKey === 'portada') okTipo = (port === '1');

                var okQ = !q || blob.indexOf(q) !== -1;
                var show = okTipo && okQ;
                tr.style.display = show ? '' : 'none';
                if (show) visibles++;
            });

            var emptyRow = sc.querySelector('.js-gm-empty-filter');
            if (emptyRow && filas.length > 0) {
                emptyRow.style.display = visibles === 0 ? '' : 'none';
            }
            if (logEv) {
                DBG.log('Filtro vitrina · ' + fKey + ' · q="' + q + '" · visibles=' + visibles + '/' + filas.length, 'info');
            }
        }

        if (window.__gmSearchHandler) { document.removeEventListener('input', window.__gmSearchHandler); }
        window.__gmSearchHandler = function (e) {
            if (e.target && e.target.classList && e.target.classList.contains('js-gm-search')) {
                aplicarFiltroGaleria(e.target.closest('.gm-root'), false);
            }
        };
        document.addEventListener('input', window.__gmSearchHandler);

        /* ═══ DROPZONES · pegar / arrastrar / clic ═══ */
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

        if (window.__gmPasteHandler) { document.removeEventListener('paste', window.__gmPasteHandler); }
        window.__gmZones = activeZones;
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

        /* ═══ RELLENO DEL MODAL DE EDICIÓN ═══ */
        var botonesEditar = root.querySelectorAll('.btn-editar-multimedia-' + UID);
        for (var bi = 0; bi < botonesEditar.length; bi++) {
            botonesEditar[bi].addEventListener('click', function () {
                var formEd = document.getElementById('formEditarMultimedia-' + UID);
                if (!formEd) { return; }
                formEd.action = this.getAttribute('data-url-update');

                var errBox = formEd.querySelector('.gm-form-err');
                if (errBox) { errBox.style.display = 'none'; errBox.innerHTML = ''; }

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
            if (document.querySelector('.modal.show')) { return; }
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
                root = nuevoShow.querySelector('.gm-root') || root;
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

        function recargarContexto(motivo) {
            var intentos = [];
            if (FRAG) {
                var url = FRAG;
                var inm = (GMDBG && GMDBG.inmuebleId) ? GMDBG.inmuebleId : null;
                url += (url.indexOf('?') === -1 ? '?' : '&') + (inm ? ('inmueble_id=' + encodeURIComponent(inm) + '&') : '') + '_t=' + Date.now();
                intentos.push({ url: url, tipo: 'galeria', etiqueta: 'fragmento propio de la galería' });
            }
            var show = document.getElementById('showRoot');
            var fragN2 = show ? (show.getAttribute('data-frag') || '') : '';
            if (fragN2) {
                var rid = (GMDBG && GMDBG.inmuebleId) ? GMDBG.inmuebleId : 'todos';
                var url2 = fragN2 + (fragN2.indexOf('?') === -1 ? '?' : '&') + 'inmueble_id=' + encodeURIComponent(rid) + '&_t=' + Date.now();
                intentos.push({ url: url2, tipo: 'n2', etiqueta: 'ficha N2 (#showRoot)' });
            }
            if (!intentos.length) {
                DBG.log('⚠ sin rutas de recarga AJAX disponibles (' + motivo + ')', 'warn');
                return;
            }
            gmIntentarRecarga(intentos, 0, motivo);
        }
        window.gmReloadFragment = recargarContexto;

        function gmIntentarRecarga(intentos, i, motivo) {
            if (i >= intentos.length) {
                DBG.log('⚠ ninguna recarga AJAX funcionó (' + motivo + ')', 'err');
                DBG.open();
                return;
            }
            var it = intentos[i];
            var t0 = performance.now();
            DBG.log('GET ' + it.url + ' [' + it.etiqueta + ']', 'net');
            fetch(it.url, {
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
                var estadoSwap = gmAplicarHtml(res.html, it.tipo);
                DBG.log('Galería actualizada (' + res.ms + 'ms) · ' + estadoSwap, 'ok');
            }).catch(function (err) {
                DBG.log('intento ' + (i + 1) + '/' + intentos.length + ' falló (' + err.message + ')', 'warn');
                gmIntentarRecarga(intentos, i + 1, motivo);
            });
        }

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
            var t0 = performance.now();
            DBG.log('POST ' + tipo + ' → ' + form.getAttribute('action'), 'net');

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
                    DBG.log('⚠ validación 422 (' + res.ms + 'ms): ' + (msgs.join(' | ') || 'sin detalle'), 'err');
                    gmToast('Revisa el formulario: hay campos con errores', 'err');
                    return;
                }
                if (!res.ok) {
                    var det = res.html ? extraerErrorLaravel(res.html) : ((res.json && res.json.message) ? res.json.message : '');
                    throw new Error('HTTP ' + res.status + (det ? ' · ' + det : ''));
                }
                if (res.html && res.html.trim() === 'error') {
                    throw new Error('el controlador devolvió "error" sin detalles');
                }
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try { bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide(); } catch (e8) {}
                }
                gmToast(tipo === 'store' ? 'Recurso agregado · actualizando vitrina…' : 'Cambios guardados · actualizando vitrina…', 'ok');
                DBG.log('Guardado OK (' + res.ms + 'ms) → refrescando vitrina', 'ok');
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
    })();
    </script>

</div>
