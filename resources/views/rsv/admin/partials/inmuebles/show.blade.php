{{--
    ========================================================================
    VISTA UNIFICADA: Detalle, Gestión y Vista Previa de Inmueble
    RUTA: resources/views/rsv/admin/partials/inmuebles/show.blade.php
    ESTILO: hoja de cálculo SUAVE · celdas 8px · encabezados 9px · pasteles
    FIX: modales DENTRO de #showRoot para que el CSS les aplique
    ========================================================================
--}}

<div id="showRoot">

    <style>
        /* ═══ BASE — anclada a #showRoot ═══ */
        #showRoot {
            --grid:    #f1f5f9;
            --grid-2:  #e9eef4;
            --head:    #fafbfd;
            --ink:     #64748b;
            --ink-2:   #475569;
            --ink-3:   #94a3b8;
            --ink-4:   #b3bcc7;
            --accent:  #3d7a5c;
            --accent-bg: #f0f7f2;
            --accent-bd: #d5e7dc;
            --m1: #8fb0cc;  --m1-bg: #f2f7fb;  --m1-bd: #dde8f1;
            --m2: #5d8a70;  --m2-bg: #f0f7f2;  --m2-bd: #d8e9de;
            --m3: #8f7bb5;  --m3-bg: #f9f7fc;  --m3-bd: #e9e2f2;
            font-family: Calibri, "Segoe UI", system-ui, sans-serif;
            font-size: 8px;
            line-height: 1.4;
            color: var(--ink);
        }

        #showRoot .strong { color: var(--ink-2); font-weight: 600; }
        #showRoot .kicker {
            font-size: 7.5px; font-weight: 600; letter-spacing: .07em;
            text-transform: uppercase; color: var(--ink-4);
        }
        #showRoot .sub { font-size: 7.5px; color: var(--ink-4); }
        #showRoot .num { font-variant-numeric: tabular-nums; }

        /* ═══ CINTA DE TÍTULO ═══ */
        #showRoot .sh-titlebar {
            display: flex; flex-wrap: wrap; gap: 10px;
            align-items: center; justify-content: space-between;
            padding: 2px 2px 10px;
            border-bottom: 1px solid var(--grid);
            margin-bottom: 12px;
        }
        #showRoot .sh-live {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 7.5px; font-weight: 600; letter-spacing: .06em;
            color: var(--ink-3);
            border: 1px solid var(--grid-2); background: var(--head);
            border-radius: 9px; padding: 1px 9px;
        }
        #showRoot .sh-live .pulse {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--m1); opacity: .8;
            animation: shPulse 2s infinite;
        }
        @keyframes shPulse { 0%,100% { opacity:.35; } 50% { opacity:.9; } }

        /* ═══ BOTONES ═══ */
        #showRoot .btn-soft {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
            font-family: inherit;
            font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px;
            display: inline-flex; align-items: center; gap: 5px;
            text-decoration: none; transition: all .15s ease;
        }
        #showRoot .btn-soft:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }

        #showRoot .btn-soft-m1 { background: var(--m1-bg); color: #5c7d99; border-color: var(--m1-bd); }
        #showRoot .btn-soft-m1:hover { background: #e9f1f8; color: #47637c; }

        #showRoot .btn-soft-m2 { background: var(--m2-bg); color: var(--m2); border-color: var(--m2-bd); }
        #showRoot .btn-soft-m2:hover { background: #e5f0e9; color: #476f58; }

        #showRoot .btn-soft-m3 { background: var(--m3-bg); color: var(--m3); border-color: var(--m3-bd); }
        #showRoot .btn-soft-m3:hover { background: #f1ecf8; color: #75639c; }

        /* ═══ MÓDULOS ═══ */
        #showRoot .sh-module { margin-bottom: 10px; }

        #showRoot .sh-strip {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding: 6px 10px;
            background: var(--head);
            border: 1px solid var(--grid); border-bottom: 0;
            border-radius: 4px 4px 0 0;
            border-left: 2px solid var(--module-color, var(--grid-2));
        }
        #showRoot .sh-strip.m1 { --module-color: var(--m1); }
        #showRoot .sh-strip.m2 { --module-color: var(--m2); }
        #showRoot .sh-strip.m3 { --module-color: var(--m3); }

        #showRoot .sh-strip .head-btn {
            display: flex; align-items: center; gap: 7px;
            background: transparent; border: 0; padding: 0;
            font-family: inherit; text-align: left; cursor: pointer;
        }
        #showRoot .sh-strip .m-id { font-size: 7px; font-weight: 600; letter-spacing: .08em; color: var(--ink-4); }
        #showRoot .sh-strip .m-name { font-size: 10px; font-weight: 600; color: var(--ink-2); }
        #showRoot .sh-strip .chev {
            width: 10px; height: 10px; color: var(--ink-4);
            transition: transform .2s ease;
        }
        #showRoot .sh-strip .head-btn[aria-expanded="true"] .chev { transform: rotate(180deg); }

        #showRoot .sh-frame { background: #fff; border: 1px solid var(--grid); border-radius: 0 0 4px 4px; }
        #showRoot .sh-body { padding: 10px; }

        /* ═══ STAT CELLS ═══ */
        #showRoot .stat-grid { display: flex; gap: 8px; flex-wrap: wrap; }
        #showRoot .stat-cell {
            flex: 1; min-width: 140px;
            border: 1px solid var(--grid); border-radius: 4px;
            padding: 8px 10px;
        }
        #showRoot .stat-cell .lbl {
            display: flex; align-items: center; gap: 4px;
            font-size: 7px; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: var(--ink-4); margin-bottom: 3px;
        }
        #showRoot .stat-cell .lbl i { font-size: 8.5px; color: #c3ccd6; }
        #showRoot .stat-cell .big {
            font-size: 11px; font-weight: 600; color: var(--ink-2);
            font-variant-numeric: tabular-nums;
        }
        #showRoot .stat-cell .big small { font-size: 8px; font-weight: 400; color: var(--ink-4); }

        #showRoot .vis-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 2px 9px; border-radius: 9px;
            font-size: 8px; font-weight: 600; border: 1px solid;
        }
        #showRoot .vis-on  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
        #showRoot .vis-off { color: #9aa5b1; background: #fafbfd; border-color: #eef2f6; }
        #showRoot .vis-on .dotp { width: 5px; height: 5px; border-radius: 50%; background: #a3cfb6; animation: shPulse 2s infinite; }
        #showRoot .vis-off .dotp { width: 5px; height: 5px; border-radius: 50%; background: #d4dae2; }

        /* ═══ TABLA TIPO HOJA ═══ */
        #showRoot .xls-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        #showRoot .xls-table th,
        #showRoot .xls-table td {
            border-right: 1px solid var(--grid);
            border-bottom: 1px solid var(--grid);
            padding: 2px 7px; height: 20px;
            vertical-align: middle; white-space: nowrap;
            background: #fff; font-size: 8px; font-weight: 400; color: var(--ink);
        }
        #showRoot .xls-table thead th {
            font-size: 9px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em;
            color: var(--ink-3); background: var(--head);
        }
        #showRoot .xls-table td.row-index {
            background: var(--head); color: #d4dae2;
            font-size: 7.5px; text-align: center;
            width: 22px; min-width: 22px; padding: 0;
        }
        #showRoot .xls-table tbody tr:hover td { background: #fafcfe; }

        #showRoot .chip {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 0 7px; border-radius: 8px;
            font-size: 7.5px; font-weight: 600; border: 1px solid; line-height: 13px;
        }
        #showRoot .chip-green  { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
        #showRoot .chip-gray   { color:#9aa5b1; background:#fafbfd; border-color:#eef2f6; }
        #showRoot .chip-purple { color:#8f7bb5; background:#f9f7fc; border-color:#e9e2f2; }
        #showRoot .chip-blue   { color:#5c7d99; background:#f2f7fb; border-color:#dde8f1; }

        #showRoot .btn-actions {
            border: 0; background: transparent; color: var(--ink-3);
            font-family: inherit;
            font-size: 8px; font-weight: 600; padding: 1px 8px; border-radius: 3px;
        }
        #showRoot .btn-actions:hover { background: #eef2f6; color: var(--ink-2); }

        #showRoot .thumb {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 3px;
            border: 1px solid var(--grid); background: var(--head);
            overflow: hidden; text-decoration: none;
            transition: border-color .15s ease;
        }
        #showRoot .thumb:hover { border-color: var(--grid-2); }
        #showRoot .thumb img { width: 100%; height: 100%; object-fit: cover; }
        #showRoot .thumb i { font-size: 12px; color: var(--m3); opacity: .7; }

        #showRoot .sh-empty {
            text-align: center; padding: 22px 12px;
            color: var(--ink-4); font-size: 8.5px; background: var(--head);
        }
        #showRoot .sh-empty i { font-size: 16px; color: #d4dae2; display: block; margin-bottom: 3px; }

        /* ═══ ALERTAS FLASH ═══ */
        #showRoot .sh-alert {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding: 5px 10px; margin-bottom: 10px;
            border-radius: 4px; font-size: 8.5px; font-weight: 500; border: 1px solid;
        }
        #showRoot .sh-alert.ok  { color: #5d8a70; background: #f0f7f2; border-color: #d8e9de; }
        #showRoot .sh-alert.err { color: #b06a6a; background: #fdf4f4; border-color: #f0dcdc; }
        #showRoot .sh-alert button {
            border: 0; background: transparent; color: inherit;
            font-size: 10px; line-height: 1; opacity: .6; cursor: pointer;
        }
        #showRoot .sh-alert button:hover { opacity: 1; }

        /* ═══ TARJETA VISTA PREVIA ═══ */
        #showRoot .pv-label {
            display: flex; align-items: center; gap: 5px;
            font-size: 7.5px; font-weight: 600; letter-spacing: .07em;
            text-transform: uppercase; color: var(--ink-4);
            margin-bottom: 6px;
        }
        #showRoot .pv-label svg { width: 10px; height: 10px; stroke: #c3ccd6; }

        #showRoot .pv-card {
            background: #fff; border: 1px solid var(--grid);
            border-radius: 5px; overflow: hidden;
            display: flex; flex-direction: column;
        }
        #showRoot .pv-sticky { position: sticky; top: 14px; }

        #showRoot .pv-carousel {
            height: 180px; background: #f4f6f8;
            position: relative; flex-shrink: 0;
            border-bottom: 1px solid var(--grid);
        }
        #showRoot .pv-carousel img { object-fit: cover; object-position: center; width: 100%; height: 100%; }
        #showRoot .pv-carousel .carousel-inner,
        #showRoot .pv-carousel .carousel-item { width: 100%; height: 100%; }

        #showRoot .float-btn {
            width: 30px; height: 30px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(233,238,244,.95);
            color: var(--ink-3);
            text-decoration: none;
            box-shadow: 0 1px 4px rgba(15,23,42,.08);
            z-index: 10;
            transition: transform .15s ease, color .15s ease;
        }
        #showRoot .float-btn:hover { transform: scale(1.1); color: var(--ink-2); }
        #showRoot .float-btn svg { width: 13px; height: 13px; }
        #showRoot .float-btn .heart { stroke: #d98c8c; }
        #showRoot .float-btn:hover .heart { stroke: #c46a6a; }

        #showRoot .pv-carousel .carousel-control-prev,
        #showRoot .pv-carousel .carousel-control-next {
            width: 26px; opacity: .25; transition: opacity .15s ease; z-index: 5;
        }
        #showRoot .pv-carousel:hover .carousel-control-prev,
        #showRoot .pv-carousel:hover .carousel-control-next { opacity: .55; }
        #showRoot .pv-carousel .carousel-control-prev-icon,
        #showRoot .pv-carousel .carousel-control-next-icon {
            width: 14px; height: 14px; filter: invert(.45);
        }

        #showRoot .portada-badge {
            position: absolute; bottom: 6px; left: 6px; z-index: 5;
            font-size: 7px; font-weight: 600; letter-spacing: .05em;
            color: var(--ink-3);
            background: rgba(255,255,255,.9);
            border: 1px solid rgba(233,238,244,.9);
            border-radius: 3px; padding: 0 6px; line-height: 14px;
        }

        #showRoot .pv-body { padding: 9px 11px 8px; display: flex; flex-direction: column; flex-grow: 1; }

        #showRoot .star-row svg { width: 9px; height: 9px; fill: #ecd3a0; stroke: none; }
        #showRoot .star-row .score { font-size: 8px; font-weight: 600; color: var(--ink-3); }
        #showRoot .rev-count {
            font-size: 7.5px; color: var(--ink-4);
            display: inline-flex; align-items: center; gap: 3px;
        }
        #showRoot .rev-count svg { width: 9px; height: 9px; stroke: #c3ccd6; }

        #showRoot .loc {
            font-size: 8px; color: var(--ink-3);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        #showRoot .loc i { font-size: 8px; color: #c3ccd6; }

        #showRoot .spec-box {
            background: var(--head); border: 1px solid var(--grid);
            border-radius: 4px; padding: 6px 8px; margin-bottom: 6px; display: flex;
        }
        #showRoot .spec-box .half { flex: 1; text-align: center; }
        #showRoot .spec-box .half + .half { border-left: 1px solid var(--grid); }
        #showRoot .spec-box .lbl {
            display: block; font-size: 7px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4);
        }
        #showRoot .spec-box .val {
            display: block; font-size: 9px; font-weight: 600;
            color: var(--ink-2); font-variant-numeric: tabular-nums;
        }

        #showRoot .tarifa-box { border: 1px solid var(--grid); border-radius: 4px; padding: 7px 9px; background: #fff; }
        #showRoot .tarifa-top {
            display: flex; justify-content: space-between; align-items: baseline;
            gap: 8px; padding-bottom: 6px; margin-bottom: 6px;
            border-bottom: 1px solid var(--grid);
        }
        #showRoot .tarifa-name { font-size: 8.5px; font-weight: 600; color: var(--ink-2); line-height: 1.25; }
        #showRoot .tarifa-fecha { font-size: 7px; color: var(--ink-4); }
        #showRoot .tarifa-precio { font-size: 8.5px; font-weight: 600; color: var(--ink-2); font-variant-numeric: tabular-nums; }
        #showRoot .tarifa-precio small {
            display: block; font-size: 6.5px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4);
        }
        #showRoot .restr-row { display: flex; gap: 6px; }
        #showRoot .restr-cell {
            flex: 1; text-align: center;
            background: var(--head); border: 1px solid var(--grid);
            border-radius: 3px; padding: 3px 4px;
        }
        #showRoot .restr-cell .lbl {
            display: block; font-size: 6.5px; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase; color: var(--ink-4);
        }
        #showRoot .restr-cell .val {
            display: block; font-size: 9px; font-weight: 600;
            line-height: 1.3; font-variant-numeric: tabular-nums; color: #5d8a70;
        }
        #showRoot .restr-cell .val.blue { color: #7d9cb8; }

        #showRoot .tarifa-empty {
            border: 1px dashed var(--grid-2); border-radius: 4px;
            padding: 7px; text-align: center;
            font-size: 8px; font-style: italic; color: var(--ink-4);
            background: var(--head);
        }

        /* ═══ MODALES SUAVES ═══ */
        #showRoot .sh-modal .modal-content {
            border: 1px solid var(--grid-2); border-radius: 5px;
            box-shadow: 0 8px 30px rgba(15,23,42,.06);
            font-family: inherit; color: var(--ink);
        }
        #showRoot .sh-modal .modal-header { border-bottom: 1px solid var(--grid); padding: 9px 14px; }
        #showRoot .sh-modal .modal-body { padding: 14px; }
        #showRoot .sh-modal .form-label {
            font-size: 7.5px; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: var(--ink-3); margin-bottom: 2px;
        }
        #showRoot .sh-modal .form-label span {
            text-transform: none; letter-spacing: 0; font-weight: 400; color: var(--ink-4);
        }
        #showRoot .sh-modal .form-control,
        #showRoot .sh-modal .form-select,
        #showRoot .sh-modal .form-check-input {
            font-size: 9.5px; color: var(--ink-2);
            border: 1px solid var(--grid-2); border-radius: 3px;
            padding: 4px 8px; box-shadow: none !important;
        }
        #showRoot .sh-modal .form-control:focus,
        #showRoot .sh-modal .form-select:focus { border-color: #c8d6cd; }
        #showRoot .sh-modal .form-check-label { font-size: 9px; color: var(--ink-2); font-weight: 600; }
        #showRoot .sh-modal .switch-box {
            background: var(--head); border: 1px solid var(--grid);
            border-radius: 4px; padding: 8px 10px;
        }
        #showRoot .sh-modal .btn-cancel {
            border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
            font-family: inherit; font-size: 9px; font-weight: 600;
            padding: 3px 14px; border-radius: 3px;
        }
        #showRoot .sh-modal .btn-cancel:hover { color: var(--ink-2); background: #fafcfe; }
        #showRoot .sh-modal .btn-save {
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid var(--accent-bd);
            font-family: inherit; font-size: 9px; font-weight: 600;
            padding: 3px 14px; border-radius: 3px;
        }
        #showRoot .sh-modal .btn-save:hover { background: #e3f1e9; color: #34684e; }
    </style>


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

        <a href="{{ route('rsv.admin.dashboard') }}" class="btn-soft">
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

                    {{-- CARRUSEL + FLOTANTES --}}
                    <div id="carouselInmueble{{ $inmueble->id }}"
                         class="carousel slide pv-carousel"
                         data-bs-ride="carousel">

                        {{-- LÁPIZ (abre modal de edición) --}}
                        <button type="button"
                                class="float-btn position-absolute top-0 start-0 m-2"
                                data-bs-toggle="modal" data-bs-target="#modalEditarInmueblePrincipal"
                                title="Editar Inmueble">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>

                        {{-- CORAZÓN (favorito) --}}
                        <button type="button"
                                class="float-btn position-absolute top-0 end-0 m-2 btn-favorito"
                                title="Vista de favorito">
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
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselInmueble{{ $inmueble->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif

                    </div>


                    {{-- CUERPO --}}
                    <div class="pv-body">

                        {{-- Título + estado --}}
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


                        {{-- Rating + reseñas --}}
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-1 star-row">
                                @for($i = 0; $i < 5; $i++)
                                    <svg viewBox="0 0 24 24">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                @endfor
                                <span class="score ms-0.5">5.0</span>
                            </div>

                            <span class="rev-count">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                {{ $inmueble->comentarios_count ?? 0 }} reseñas
                            </span>
                        </div>


                        {{-- Ubicación --}}
                        <div class="loc mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $inmueble->city ?? 'Sin ciudad' }} — {{ $inmueble->ubicacion ?? 'Sin dirección' }}
                        </div>


                        {{-- Valores --}}
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


                        {{-- Última tarifa --}}
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


        {{-- ═══════════ COLUMNA DERECHA · LOS 3 MÓDULOS ═══════════ --}}
        <div class="col-12 col-lg-7 col-xl-8">

            {{-- Alertas flash --}}
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
                    <button type="button" class="head-btn"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseNucleoInmueble"
                            aria-expanded="false"
                            aria-controls="collapseNucleoInmueble">
                        <span class="m-id">MÓDULO 1/3</span>
                        <span class="m-name">Identidad y Especificaciones</span>
                        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <button type="button" class="btn-soft btn-soft-m1"
                            data-bs-toggle="modal" data-bs-target="#modalEditarInmueblePrincipal">
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


            {{-- ── MÓDULO 2 · MOTOR COMERCIAL ── --}}
            <div class="sh-module">

                <div class="sh-strip m2">
                    <button type="button" class="head-btn"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseMotorComercial"
                            aria-expanded="false"
                            aria-controls="collapseMotorComercial">
                        <span class="m-id">MÓDULO 2/3</span>
                        <span class="m-name">Motor Comercial y Tarifas</span>
                        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <button type="button" class="btn-soft btn-soft-m2"
                            data-bs-toggle="modal" data-bs-target="#modalAddTarifa">
                        <i class="bi bi-plus-lg" style="font-size: 8.5px;"></i> Nueva Tarifa
                    </button>
                </div>

                <div id="collapseMotorComercial" class="collapse">
                    <div class="sh-frame">
                        <div class="sh-body">

                            <div class="overflow-auto" style="border: 1px solid var(--grid); border-radius: 4px;">

                                <table class="xls-table" style="min-width: 480px;">

                                    <thead>
                                        <tr>
                                            <th style="width: 22px;"></th>
                                            <th>Temporada</th>
                                            <th class="text-center">Fechas</th>
                                            <th class="text-end">Precio / Noche</th>
                                            <th class="text-center">·</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($inmueble->tarifasTemporadas ?? [] as $tarifa)

                                            <tr>
                                                <td class="row-index">{{ $loop->iteration }}</td>

                                                <td class="strong">
                                                    {{ $tarifa->nombre_temporada }}
                                                    <div class="mt-0.5">
                                                        @if($tarifa->active)
                                                            <span class="chip chip-green" style="font-size: 7px;">Activa</span>
                                                        @else
                                                            <span class="chip chip-gray" style="font-size: 7px;">Inactiva</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td class="text-center num" style="color: #a8b3c1;">
                                                    {{ optional($tarifa->fecha_inicio)->format('d/m') }} — {{ optional($tarifa->fecha_fin)->format('d/m/y') }}
                                                </td>

                                                <td class="text-end num strong" style="color: #5d8a70;">
                                                    ${{ number_format($tarifa->precio_noche, 2) }}
                                                </td>

                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn-actions btn-editar-tarifa"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditarTarifa"
                                                        data-url-update="{{ route('rsv.tarifas-temporadas.update', $tarifa->id) }}"
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

                                        @empty
                                            <tr>
                                                <td class="row-index">1</td>
                                                <td colspan="4">
                                                    <div class="sh-empty" style="background: transparent;">
                                                        <i class="bi bi-calendar-x"></i>
                                                        No hay tarifas registradas.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>
                </div>

            </div>


            {{-- ── MÓDULO 3 · VITRINA VISUAL ── --}}
            <div class="sh-module">

                <div class="sh-strip m3">
                    <button type="button" class="head-btn"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseVitrinaVisual"
                            aria-expanded="false"
                            aria-controls="collapseVitrinaVisual">
                        <span class="m-id">MÓDULO 3/3</span>
                        <span class="m-name">Vitrina Visual y Galería</span>
                        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <button type="button" class="btn-soft btn-soft-m3"
                            data-bs-toggle="modal" data-bs-target="#modalAddMultimedia">
                        <i class="bi bi-plus-lg" style="font-size: 8.5px;"></i> Agregar Recurso
                    </button>
                </div>

                <div id="collapseVitrinaVisual" class="collapse">
                    <div class="sh-frame">
                        <div class="sh-body">

                            <div class="overflow-auto" style="border: 1px solid var(--grid); border-radius: 4px;">

                                <table class="xls-table" style="min-width: 420px;">

                                    <thead>
                                        <tr>
                                            <th style="width: 22px;"></th>
                                            <th class="text-center">Vista</th>
                                            <th>Tipo / Portada</th>
                                            <th class="text-center">Orden</th>
                                            <th class="text-center">·</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($inmueble->multimedia ?? [] as $media)

                                            <tr>
                                                <td class="row-index">{{ $loop->iteration }}</td>

                                                <td class="text-center">
                                                    <a href="{{ $media->url_archivo }}" target="_blank" class="thumb" title="Ver archivo completo">
                                                        @if($media->tipo_multimedia == 'imagen')
                                                            <img src="{{ $media->url_archivo }}" alt="Miniatura">
                                                        @else
                                                            <i class="bi bi-play-btn"></i>
                                                        @endif
                                                    </a>
                                                </td>

                                                <td>
                                                    <div class="mb-0.5">
                                                        @if($media->tipo_multimedia == 'imagen')
                                                            <span class="chip chip-blue"><i class="bi bi-image"></i> Imagen</span>
                                                        @else
                                                            <span class="chip chip-purple"><i class="bi bi-camera-video"></i> Video</span>
                                                        @endif
                                                    </div>
                                                    @if($media->es_portada)
                                                        <span class="chip chip-green" style="font-size: 7px;">Principal</span>
                                                    @endif
                                                </td>

                                                <td class="text-center num">{{ $media->orden }}</td>

                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn-actions btn-editar-multimedia"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditarMultimedia"
                                                        data-url-update="{{ route('rsv.inmueble-multimedia.update', $media->id) }}"
                                                        data-tipo-multimedia="{{ $media->tipo_multimedia }}"
                                                        data-orden="{{ $media->orden }}"
                                                        data-es-portada="{{ $media->es_portada ? 1 : 0 }}">
                                                        Editar
                                                    </button>
                                                </td>

                                            </tr>

                                        @empty
                                            <tr>
                                                <td class="row-index">1</td>
                                                <td colspan="4">
                                                    <div class="sh-empty" style="background: transparent;">
                                                        <i class="bi bi-images"></i>
                                                        Vitrina vacía. Sube fotos o videos.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>  {{-- /row g-3 --}}


    {{-- ═══════════════════════════════════════════════════════════ --}}
    <!--  MODALES (DENTRO de #showRoot para que el CSS aplique)       -->
    <!--  ═══════════════════════════════════════════════════════════ -->

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


    {{-- MODAL 2A · NUEVA TARIFA --}}
    <div class="modal fade sh-modal" id="modalAddTarifa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="kicker">Módulo 2/3 · Motor comercial</div>
                        <div class="strong mt-0.5" style="font-size: 10.5px;">Nueva tarifa por temporada</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                        <div class="mb-2">
                            <label class="form-label">Nombre de la temporada <span>(`nombre_temporada`)</span></label>
                            <input type="text" class="form-control" name="nombre_temporada" placeholder="Ej. Temporada Alta Diciembre" required>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de inicio <span>(`fecha_inicio`)</span></label>
                                <input type="date" class="form-control" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de fin <span>(`fecha_fin`)</span></label>
                                <input type="date" class="form-control" name="fecha_fin" required>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Precio noche</label>
                                <input type="number" step="0.01" class="form-control" name="precio_noche" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Precio fin sem.</label>
                                <input type="number" step="0.01" class="form-control" name="precio_fin_semana" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Mín. reserva</label>
                                <input type="number" step="0.01" class="form-control" name="precio_minimo_reserva" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Días máximos</label>
                                <input type="number" class="form-control" name="dias_maximos" placeholder="Ej. 30" min="1">
                            </div>
                        </div>

                        <div class="switch-box mt-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="tarifaActiveCheck" checked>
                                <label class="form-check-label" for="tarifaActiveCheck">Tarifa activa comercialmente</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Guardar Tarifa</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {{-- MODAL 2B · EDITAR TARIFA --}}
    <div class="modal fade sh-modal" id="modalEditarTarifa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="kicker">Módulo 2/3 · Motor comercial</div>
                        <div class="strong mt-0.5" style="font-size: 10.5px;">Editar tarifa comercial</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarTarifa" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                        <div class="mb-2">
                            <label class="form-label">Nombre de la temporada</label>
                            <input type="text" class="form-control" id="edit_tarifa_nombre" name="nombre_temporada" required>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de inicio</label>
                                <input type="date" class="form-control" id="edit_tarifa_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Fecha de fin</label>
                                <input type="date" class="form-control" id="edit_tarifa_fin" name="fecha_fin" required>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Precio noche</label>
                                <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_noche" name="precio_noche" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Precio fin sem.</label>
                                <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_fin_semana" name="precio_fin_semana" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Mín. reserva</label>
                                <input type="number" step="0.01" class="form-control" id="edit_tarifa_precio_minimo" name="precio_minimo_reserva" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Días máximos</label>
                                <input type="number" class="form-control" id="edit_tarifa_dias_maximos" name="dias_maximos" min="1">
                            </div>
                        </div>

                        <div class="switch-box mt-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="active" value="1" id="edit_tarifa_active">
                                <label class="form-check-label" for="edit_tarifa_active">Tarifa activa</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Actualizar Tarifa</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {{-- MODAL 3A · AGREGAR MULTIMEDIA --}}
    <div class="modal fade sh-modal" id="modalAddMultimedia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="kicker">Módulo 3/3 · Vitrina visual</div>
                        <div class="strong mt-0.5" style="font-size: 10.5px;">Agregar recurso multimedia</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                        <div class="mb-2">
                            <label class="form-label">Tipo de multimedia <span>(`tipo_multimedia`)</span></label>
                            <select class="form-select" name="tipo_multimedia" required>
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">URL del archivo (S3) <span>(`url_archivo`)</span></label>
                            <input type="url" class="form-control" name="url_archivo" placeholder="https://bucket.s3.amazonaws.com/..." required>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Orden de aparición <span>(`orden`)</span></label>
                                <input type="number" class="form-control" name="orden" placeholder="Ej. 1" min="1">
                            </div>
                            <div class="col-md-6 mb-2 d-flex align-items-end">
                                <div class="switch-box w-100">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="addMediaPortada">
                                        <label class="form-check-label" for="addMediaPortada">Portada principal</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Guardar Recurso</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {{-- MODAL 3B · EDITAR MULTIMEDIA --}}
    <div class="modal fade sh-modal" id="modalEditarMultimedia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="kicker">Módulo 3/3 · Vitrina visual</div>
                        <div class="strong mt-0.5" style="font-size: 10.5px;">Editar recurso multimedia</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarMultimedia" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-2">
                            <label class="form-label">Tipo de multimedia</label>
                            <select class="form-select" id="edit_media_tipo" name="tipo_multimedia" required>
                                <option value="imagen">Imagen</option>
                                <option value="video">Video</option>
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control" id="edit_media_orden" name="orden" min="1">
                            </div>
                            <div class="col-md-6 mb-2 d-flex align-items-end">
                                <div class="switch-box w-100">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="es_portada" value="1" id="edit_media_portada">
                                        <label class="form-check-label" for="edit_media_portada">Portada principal</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Actualizar Recurso</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


</div>{{-- /#showRoot --}}


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ── Poblar modal de editar TARIFA ── */
        document.querySelectorAll('#showRoot .btn-editar-tarifa').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var f = document.getElementById('formEditarTarifa');
                if (!f) return;

                f.action = btn.dataset.urlUpdate;

                document.getElementById('edit_tarifa_nombre').value            = btn.dataset.nombreTemporada || '';
                document.getElementById('edit_tarifa_inicio').value            = btn.dataset.fechaInicio || '';
                document.getElementById('edit_tarifa_fin').value               = btn.dataset.fechaFin || '';
                document.getElementById('edit_tarifa_precio_noche').value      = btn.dataset.precioNoche || '';
                document.getElementById('edit_tarifa_precio_fin_semana').value = btn.dataset.precioFinSemana || '';
                document.getElementById('edit_tarifa_precio_minimo').value     = btn.dataset.precioMinimoReserva || '';
                document.getElementById('edit_tarifa_dias_maximos').value      = btn.dataset.diasMaximos || '';
                document.getElementById('edit_tarifa_active').checked          = btn.dataset.active === '1';
            });
        });

        /* ── Poblar modal de editar MULTIMEDIA ── */
        document.querySelectorAll('#showRoot .btn-editar-multimedia').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var f = document.getElementById('formEditarMultimedia');
                if (!f) return;

                f.action = btn.dataset.urlUpdate;

                document.getElementById('edit_media_tipo').value      = btn.dataset.tipoMultimedia || 'imagen';
                document.getElementById('edit_media_orden').value     = btn.dataset.orden || '';
                document.getElementById('edit_media_portada').checked = btn.dataset.esPortada === '1';
            });
        });

    });
</script>
