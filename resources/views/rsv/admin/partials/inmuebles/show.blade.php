<div id="showRoot">
    {{-- ═══ CINTA DE TÍTULO ═══ --}}
    <div class="sh-titlebar">
        <div>
            <span class="sh-live mb-1">
                <span class="pulse"></span> EXPEDIENTE #{{ $inmueble->id }}
            </span>
            <div class="strong" style="font-size: 13px;">
                Panel de Gestión: {{ $inmueble->name }}
            </div>
            <div class="sub">
                Administra información, tarifas y fotos con vista previa en tiempo real
            </div>
        </div>

        <a href="{{ route('rsv.admin.dashboard', ['inmueble_id' => 'todos']) }}" class="btn-soft">
            <i class="bi bi-arrow-left" style="font-size: 9px;"></i> Volver al Catálogo
        </a>
    </div>

    <div class="row g-3">
        {{-- ═══════════ COLUMNA IZQUIERDA · VISTA PREVIA DEL CLIENTE ═══════════ --}}
        <div class="col-12 col-lg-5 col-xl-4">
            <div class="pv-sticky">
                <div class="pv-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Vista Previa del Cliente
                </div>

                <div class="pv-card">
                    <div id="carouselInmueble{{ $inmueble->id }}" class="carousel slide pv-carousel" data-bs-ride="carousel">
                        <button type="button" class="float-btn position-absolute top-0 start-0 m-2" data-bs-toggle="modal" data-bs-target="#modalEditarInmueblePrincipal" title="Editar Inmueble">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>

                        <button type="button" class="float-btn position-absolute top-0 end-0 m-2 btn-favorito" title="Vista de favorito">
                            <svg class="heart" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>

                        @php $galeria = $inmueble->multimedia ?? collect(); @endphp

                        <div class="carousel-inner">
                            @forelse($galeria as $index => $media)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $media->url_archivo }}" alt="Foto inmueble">
                                    @if($media->es_portada)
                                        <span class="portada-badge">PORTADA</span>
                                    @endif
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--head);color:#d4dae2;font-size:8.5px;font-style:italic;">
                                        <i class="bi bi-image me-1"></i> Sube fotos en la galería
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if($galeria->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselInmueble{{ $inmueble->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselInmueble{{ $inmueble->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        @endif
                    </div>

                    <div class="pv-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div class="strong text-truncate" style="font-size: 9.5px;" title="{{ $inmueble->name }}">
                                {{ $inmueble->name }}
                            </div>
                            @if($inmueble->active)
                                <span class="chip chip-green" style="flex-shrink: 0;">Activo</span>
                            @else
                                <span class="chip chip-gray" style="flex-shrink: 0;">Inactivo</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-1 star-row">
                                @for($i = 0; $i < 5; $i++)
                                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                @endfor
                                <span class="score ms-0.5">5.0</span>
                            </div>
                            <span class="rev-count">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                {{ $inmueble->comentarios_count ?? 0 }} reseñas
                            </span>
                        </div>

                        <div class="loc mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $inmueble->city ?? 'Sin ciudad' }} — {{ $inmueble->ubicacion ?? 'Sin dirección' }}
                        </div>

                        <div class="spec-box">
                            <div class="half">
                                <span class="lbl">Capacidad máx.</span>
                                <span class="val">{{ $inmueble->capacidad_maxima ?? 'N/A' }} pers.</span>
                            </div>
                            <div class="half">
                                <span class="lbl">Precio base</span>
                                <span class="val" style="color: #5d8a70;">${{ number_format($inmueble->precio_base_noche ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <div class="kicker mb-1 mt-auto">Condiciones última tarifa</div>
                        @php $ultimaTarifa = $inmueble->latestTarifa; @endphp
                        @if($ultimaTarifa)
                            <div class="tarifa-box">
                                <div class="tarifa-top">
                                    <div>
                                        <div class="tarifa-name">{{ $ultimaTarifa->nombre_temporada }}</div>
                                        <div class="tarifa-fecha">
                                            {{ optional($ultimaTarifa->fecha_inicio)->format('d/m/Y') }} — {{ optional($ultimaTarifa->fecha_fin)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="tarifa-precio">
                                            <small>Precio noche</small>
                                            ${{ number_format($ultimaTarifa->precio_noche, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="restr-row">
                                    <div class="restr-cell">
                                        <span class="lbl">Mín. reserva</span>
                                        <span class="val">${{ number_format($ultimaTarifa->precio_minimo_reserva, 2) }}</span>
                                    </div>
                                    <div class="restr-cell">
                                        <span class="lbl">Máx. días</span>
                                        <span class="val blue">{{ $ultimaTarifa->dias_maximos ?: 'Ilimitado' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="tarifa-empty">Sin tarifas temporales activas</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ COLUMNA DERECHA · MÓDULO 1 (Y aquí puedes incluir o concatenar los otros partials) ═══════════ --}}
        <div class="col-12 col-lg-7 col-xl-8">
            @if(session('success'))
                <div class="sh-alert ok" role="alert">
                    <span><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</span>
                    <button type="button" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="sh-alert err" role="alert">
                    <span><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</span>
                    <button type="button" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            {{-- ── MÓDULO 1 · NÚCLEO ── --}}
            <div class="sh-module">
                <div class="sh-strip m1">
                    <button type="button" class="head-btn" data-bs-toggle="collapse" data-bs-target="#collapseNucleoInmueble" aria-expanded="false" aria-controls="collapseNucleoInmueble">
                        <span class="m-id">MÓDULO 1/3</span>
                        <span class="m-name">Identidad y Especificaciones</span>
                        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <button type="button" class="btn-soft btn-soft-m1" data-bs-toggle="modal" data-bs-target="#modalEditarInmueblePrincipal">
                        <i class="bi bi-pencil" style="font-size: 8.5px;"></i> Editar Propiedad
                    </button>
                </div>

                <div id="collapseNucleoInmueble" class="collapse">
                    <div class="sh-frame">
                        <div class="sh-body">
                            <div class="stat-grid">
                                <div class="stat-cell">
                                    <span class="lbl"><i class="bi bi-people"></i> Capacidad límite</span>
                                    <span class="big">{{ $inmueble->capacidad_maxima ?? '0' }} <small>personas</small></span>
                                </div>
                                <div class="stat-cell">
                                    <span class="lbl"><i class="bi bi-geo-alt"></i> Punto geográfico</span>
                                    <span class="big d-block text-truncate" title="{{ $inmueble->city ?? '' }}">{{ $inmueble->city ?? 'N/A' }}</span>
                                    <span class="sub d-block text-truncate" title="{{ $inmueble->ubicacion ?? '' }}">{{ $inmueble->ubicacion ?? 'Sin dirección' }}</span>
                                </div>
                                <div class="stat-cell">
                                    <span class="lbl"><i class="bi bi-eye"></i> Visibilidad</span>
                                    <div class="mt-1">
                                        @if($inmueble->active)
                                            <span class="vis-pill vis-on"><span class="dotp"></span> En vivo</span>
                                        @else
                                            <span class="vis-pill vis-off"><span class="dotp"></span> Borrador</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aquí puedes incluir el Módulo 2 y Módulo 3 desde sus respectivos partials --}}
            @include('rsv.admin.partials.inmuebles.tarifa')
            @include('rsv.admin.partials.inmuebles.galeria')
        </div>
    </div>

    {{-- MODAL 1 · EDITAR INMUEBLE PRINCIPAL --}}
    <div class="modal fade sh-modal" id="modalEditarInmueblePrincipal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="kicker">Módulo 1/3 · Núcleo</div>
                        <div class="strong mt-0.5" style="font-size: 10.5px;">Editar núcleo del inmueble</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('rsv.inmuebles.update', $inmueble->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-2">
                            <label class="form-label">Nombre del inmueble <span>(`name`)</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $inmueble->name) }}" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Capacidad máxima <span>(`capacidad_maxima`)</span></label>
                                <input type="number" class="form-control" name="capacidad_maxima" value="{{ old('capacidad_maxima', $inmueble->capacidad_maxima) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Tipo de inmueble ID <span>(`tipo_inmueble_id`)</span></label>
                                <input type="number" class="form-control" name="tipo_inmueble_id" value="{{ old('tipo_inmueble_id', $inmueble->tipo_inmueble_id) }}" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Ciudad <span>(`city`)</span></label>
                                <input type="text" class="form-control" name="city" value="{{ old('city', $inmueble->city) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Ubicación / Dirección <span>(`ubicacion`)</span></label>
                                <input type="text" class="form-control" name="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}">
                            </div>
                        </div>
                        <div class="switch-box mt-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="editActiveInmueble" {{ old('active', $inmueble->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="editActiveInmueble">Activo (visible en catálogo general)</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Actualizar Inmueble</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
