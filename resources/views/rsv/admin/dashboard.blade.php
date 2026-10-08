<x-base-layout>
    @section('titlepage', 'Panel de Administración - Sistema RSV')

    {{-- Notificaciones Toast --}}
    @include('rsv.components.alert')

    <div id="rsvPanel">

        <style>
            /* ═══ PANEL · base (misma lógica visual que las hojas internas) ═══ */
            #rsvPanel {
                --grid:    #f1f5f9;
                --grid-2:  #e9eef4;
                --head:    #fafbfd;
                --ink:     #64748b;
                --ink-2:   #475569;
                --ink-3:   #94a3b8;
                --ink-4:   #b3bcc7;
                --accent:  #3d7a5c;
                --accent-bg: #eef6f1;
                --accent-bd: #d5e7dc;
                --amber:   #b08a3e;
                --amber-bg:#fdf8ec;
                --amber-bd:#f3e7c6;
                --red:     #b06a6a;
                --red-bg:  #fdf4f4;
                --red-bd:  #f0dcdc;
                font-family: Calibri, "Segoe UI", system-ui, sans-serif;
                font-size: 10px;
                line-height: 1.45;
                color: var(--ink);
            }

            #rsvPanel .strong { color: var(--ink-2); font-weight: 600; }
            #rsvPanel .kicker {
                font-size: 7.5px; font-weight: 600; letter-spacing: .08em;
                text-transform: uppercase; color: var(--ink-4);
            }
            #rsvPanel .sub { font-size: 8.5px; color: var(--ink-4); }

            /* ═══ NIVEL 1 · CINTA DE TÍTULO ═══ */
            #rsvPanel .rp-titlebar {
                display: flex; flex-wrap: wrap; gap: 8px;
                align-items: center; justify-content: space-between;
                padding: 10px 2px 12px;
                border-bottom: 1px solid var(--grid);
            }
            #rsvPanel .rp-titlebar .rp-title {
                font-size: 13px; font-weight: 600; color: #334155;
                letter-spacing: .01em;
            }

            /* ═══ NIVEL 2 · PESTAÑAS DE MÓDULOS (sticky) ═══ */
            #rsvPanel .rp-tabs {
                position: sticky; top: 0; z-index: 20;
                display: flex; align-items: stretch;
                background: #fbfcfd;
                border-bottom: 1px solid var(--grid);
                overflow-x: auto;
            }
            #rsvPanel .rp-tabs::-webkit-scrollbar { display: none; }

            #rsvPanel .rp-tab {
                border: 0; background: transparent;
                font-family: inherit;
                font-size: 9.5px; font-weight: 600;
                color: var(--ink-3);
                padding: 9px 16px;
                border-right: 1px solid var(--grid);
                display: inline-flex; align-items: center; gap: 6px;
                white-space: nowrap; cursor: pointer;
            }
            #rsvPanel .rp-tab i { font-size: 9.5px; opacity: .7; }
            #rsvPanel .rp-tab:hover { background: #f5f8fb; color: var(--ink-2); }
            #rsvPanel .rp-tab.active {
                background: #fff; color: var(--ink-2);
                box-shadow: inset 0 -2px 0 #b9d4c5;
            }
            #rsvPanel .rp-tab .n {
                font-size: 7px; font-weight: 600; color: var(--ink-4);
                letter-spacing: .05em;
            }
            #rsvPanel .rp-tab.active .n { color: var(--accent); opacity: .8; }

            /* ═══ NIVEL 3 · MÓDULOS ═══ */
            #rsvPanel .tab-pane { padding-top: 12px; }

            #rsvPanel .rp-modstrip {
                display: flex; align-items: center; justify-content: space-between;
                gap: 8px;
                padding: 6px 12px;
                background: var(--head);
                border: 1px solid var(--grid); border-bottom: 0;
                border-radius: 4px 4px 0 0;
            }
            #rsvPanel .rp-modstrip .m-id {
                font-size: 7.5px; font-weight: 600; letter-spacing: .08em;
                color: var(--ink-4);
            }
            #rsvPanel .rp-modstrip .m-name {
                font-size: 10px; font-weight: 600; color: var(--ink-2);
            }
            #rsvPanel .rp-modstrip .m-hint { font-size: 8px; color: var(--ink-4); }

            #rsvPanel .rp-frame {
                background: #fff;
                border: 1px solid var(--grid);
                border-radius: 0 0 4px 4px;
                overflow: hidden;
            }
            #rsvPanel .rp-frame-body { padding: 12px; }
            #rsvPanel .rp-frame-body.flush { padding: 0; }

            /* ═══ Botones suaves (mismos que las hojas) ═══ */
            #rsvPanel .btn-soft {
                border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3);
                font-size: 9px; font-weight: 600; padding: 3px 11px; border-radius: 3px;
                display: inline-flex; align-items: center; gap: 5px;
                text-decoration: none;
            }
            #rsvPanel .btn-soft:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }

            #rsvPanel .btn-soft-green {
                background: var(--accent-bg); color: var(--accent);
                border: 1px solid var(--accent-bd);
                font-size: 9.5px; font-weight: 600; padding: 4px 12px; border-radius: 3px;
                display: inline-flex; align-items: center; gap: 5px;
                text-decoration: none;
            }
            #rsvPanel .btn-soft-green:hover { background: #e3f1e9; color: #34684e; }

            #rsvPanel .btn-soft-amber {
                background: var(--amber-bg); color: var(--amber);
                border: 1px solid var(--amber-bd);
                font-size: 9px; font-weight: 600; padding: 3px 11px; border-radius: 3px;
                display: inline-flex; align-items: center; gap: 5px;
            }
            #rsvPanel .btn-soft-amber:hover { background: #faf1dc; color: #8f6e2f; }

            #rsvPanel .btn-soft-red {
                background: var(--red-bg); color: var(--red);
                border: 1px solid var(--red-bd);
                font-size: 9px; font-weight: 600; padding: 4px 12px; border-radius: 3px;
                display: inline-flex; align-items: center; justify-content: center; gap: 5px;
                width: 100%;
            }
            #rsvPanel .btn-soft-red:hover { background: #fbeded; color: #965757; }

            /* ═══ Badge pastel ═══ */
            #rsvPanel .chip {
                display: inline-flex; align-items: center; gap: 4px;
                padding: 1px 8px; border-radius: 9px;
                font-size: 8px; font-weight: 600; border: 1px solid;
                line-height: 14px;
            }
            #rsvPanel .chip-red   { color:#b06a6a; background:#fdf4f4; border-color:#f0dcdc; }
            #rsvPanel .chip-green { color:#5d8a70; background:#f0f7f2; border-color:#d8e9de; }
            #rsvPanel .chip-amber { color:#b08a3e; background:#fdf8ec; border-color:#f3e7c6; }

            /* ═══ Card Endosos (pastel, antes rojo intenso) ═══ */
            #rsvPanel .endosos-card {
                background: linear-gradient(180deg, #fefbfb 0%, #fdf5f5 100%);
                border: 1px solid var(--red-bd);
                border-radius: 4px;
                height: 100%;
                display: flex; flex-direction: column;
            }
            #rsvPanel .endosos-head {
                display: flex; align-items: center; justify-content: space-between;
                padding: 10px 12px; border-bottom: 1px solid #f7e9e9;
            }
            #rsvPanel .endosos-body {
                padding: 12px; flex: 1;
                font-size: 8.5px; color: var(--ink-3);
            }
            #rsvPanel .endosos-foot { padding: 10px 12px; border-top: 1px solid #f7e9e9; }

            /* ═══ Bitácora (antes card negra) ═══ */
            #rsvPanel .audit-banner {
                background: linear-gradient(180deg, #fbfcfd 0%, #f7fafb 100%);
                border: 1px solid var(--grid-2);
                border-left: 3px solid #7a8ba0;
                border-radius: 4px;
            }

            /* ═══ Celdas de catálogos ═══ */
            #rsvPanel .cat-cell {
                background: #fff; border: 1px solid var(--grid);
                border-radius: 4px; padding: 10px 12px;
                display: flex; flex-direction: column; height: 100%;
            }
            #rsvPanel .cat-cell .cat-item {
                display: flex; align-items: baseline; justify-content: space-between;
                gap: 6px; padding: 3px 0;
                border-bottom: 1px dashed var(--grid);
                font-size: 8.5px; color: var(--ink);
            }
            #rsvPanel .cat-cell .cat-item .tbl {
                font-size: 7.5px; color: var(--ink-4);
            }

            /* ═══ Modal suave ═══ */
            #rsvPanel .rp-modal .modal-content {
                border: 1px solid var(--grid-2);
                border-radius: 5px;
                box-shadow: 0 8px 30px rgba(15,23,42,.06);
                font-family: inherit;
                color: var(--ink);
            }
            #rsvPanel .rp-modal .modal-header {
                border-bottom: 1px solid var(--grid); padding: 10px 14px;
            }
            #rsvPanel .rp-modal .modal-body { padding: 14px; }
            #rsvPanel .rp-modal .modal-footer {
                border-top: 1px solid var(--grid); padding: 10px 14px;
            }
            #rsvPanel .rp-modal .form-label {
                font-size: 8px; font-weight: 600; letter-spacing: .05em;
                text-transform: uppercase; color: var(--ink-3);
                margin-bottom: 3px;
            }
            #rsvPanel .rp-modal .form-label .tbl {
                text-transform: none; letter-spacing: 0;
                font-weight: 400; color: var(--ink-4);
            }
            #rsvPanel .rp-modal .form-control,
            #rsvPanel .rp-modal .form-check-input {
                font-size: 9.5px; color: var(--ink-2);
                border: 1px solid #e9eef4; border-radius: 3px;
                padding: 4px 8px;
                box-shadow: none !important;
            }
            #rsvPanel .rp-modal .form-control:focus {
                border-color: #c8d6cd;
            }
            #rsvPanel .rp-modal .form-check-label {
                font-size: 9.5px; color: var(--ink-2); font-weight: 600;
            }
        </style>


        <!-- ═══════════ NIVEL 1 · CINTA DE TÍTULO ═══════════ -->
        <div class="rp-titlebar">

            <div>
                <div class="kicker">RSV · Panel de Administración</div>
                <div class="rp-title">Panel de Control</div>
                <div class="sub">
                    <i class="bi bi-clock-history me-1"></i>{{ now()->translatedFormat('l d/m/Y · H:i') }}
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="chip chip-green" title="Sistema operativo">
                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                    Sistema activo
                </span>

                <a href="{{ route('rsv.reservas.pdf', 1) }}" class="btn-soft-green">
                    <i class="bi bi-file-pdf" style="font-size: 9.5px;"></i> Reporte Global
                </a>
            </div>

        </div>


        <!-- ═══════════ NIVEL 2 · PESTAÑAS DE MÓDULOS ═══════════ -->
        <ul class="nav rp-tabs" id="adminTabs" role="tablist" style="list-style: none; margin: 0; padding: 0;">

            <li class="nav-item" role="presentation">
                <button class="rp-tab nav-link active" data-bs-toggle="pill" data-bs-target="#inmuebles" type="button" role="tab">
                    <span class="n">01</span><i class="bi bi-house-door"></i> Inmuebles
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="rp-tab nav-link" data-bs-toggle="pill" data-bs-target="#reservas" type="button" role="tab">
                    <span class="n">02</span><i class="bi bi-calendar2-check"></i> Reservas y Logística
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="rp-tab nav-link" data-bs-toggle="pill" data-bs-target="#calendario" type="button" role="tab">
                    <span class="n">03</span><i class="bi bi-calendar3"></i> Calendario
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="rp-tab nav-link" data-bs-toggle="pill" data-bs-target="#finanzas" type="button" role="tab">
                    <span class="n">04</span><i class="bi bi-wallet2"></i> Finanzas
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="rp-tab nav-link" data-bs-toggle="pill" data-bs-target="#auditoria" type="button" role="tab">
                    <span class="n">05</span><i class="bi bi-shield-lock"></i> Config & Auditoría
                </button>
            </li>

        </ul>


        <div class="tab-content" id="adminTabsContent">


            <!-- ═══ MÓDULO 01 · INMUEBLES ═══ -->
            <div class="tab-pane fade show active" id="inmuebles" role="tabpanel" aria-labelledby="inmuebles-tab">

                <div class="rp-modstrip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="m-id">MÓDULO 01</span>
                        <span class="m-name">Inmuebles</span>
                    </div>
                    <span class="m-hint d-none d-md-inline">Propiedades, tarifas base y capacidad</span>
                </div>

                <div class="rp-frame">
                    <div class="rp-frame-body flush">
                        @include('rsv.admin.partials.tab-inmuebles')
                    </div>
                </div>

            </div>


            <!-- ═══ MÓDULO 02 · RESERVAS Y LOGÍSTICA ═══ -->
            <div class="tab-pane fade" id="reservas" role="tabpanel" aria-labelledby="reservas-tab">

                <div class="rp-modstrip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="m-id">MÓDULO 02</span>
                        <span class="m-name">Reservas y Logística</span>
                    </div>
                    <span class="m-hint d-none d-md-inline">Titulares · huéspedes · itinerarios · endosos</span>
                </div>

                <div class="rp-frame">
                    <div class="p-3">
                        <div class="row g-3">

                            <!-- Hoja de reservas (self-contained) -->
                            <div class="col-12 col-xl-8">
                                @include('rsv.admin.partials.tab-reservas')
                            </div>

                            <!-- Endosos (pastel suave) -->
                            <div class="col-12 col-xl-4">
                                <div class="endosos-card">

                                    <div class="endosos-head">
                                        <span class="strong" style="font-size: 9.5px;">
                                            <i class="bi bi-arrow-left-right me-1" style="color: var(--red); opacity:.7;"></i>
                                            Gestión de Endosos
                                        </span>
                                        <span class="chip chip-red">3 pendientes</span>
                                    </div>

                                    <div class="endosos-body">
                                        Aprobación de traslados de titularidad.
                                        <div class="sub mt-1">Tabla: <code style="font-size:7.5px; color: var(--ink-4);">rsv_historial_endosos</code></div>

                                        <div class="mt-2">
                                            <div class="cat-item"><span>Solicitud #1042</span><span class="chip chip-amber" style="font-size:7px;">Revisión</span></div>
                                            <div class="cat-item"><span>Solicitud #1041</span><span class="chip chip-amber" style="font-size:7px;">Revisión</span></div>
                                            <div class="cat-item" style="border-bottom: 0;"><span>Solicitud #1039</span><span class="chip chip-amber" style="font-size:7px;">Revisión</span></div>
                                        </div>
                                    </div>

                                    <div class="endosos-foot">
                                        <button type="button" class="btn-soft-red">
                                            <i class="bi bi-eye" style="font-size: 9px;"></i> Revisar Solicitudes
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>


            <!-- ═══ MÓDULO 03 · CALENDARIO ═══ -->
            <div class="tab-pane fade" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">

                <div class="rp-modstrip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="m-id">MÓDULO 03</span>
                        <span class="m-name">Calendario</span>
                    </div>
                    <span class="m-hint d-none d-md-inline">Disponibilidad conjunta: reservas + bloqueos</span>
                </div>

                <div class="rp-frame">
                    <div class="rp-frame-body">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <span class="strong" style="font-size: 9.5px;">Disponibilidad y Bloqueos</span>
                            <button type="button" class="btn-soft-amber">
                                <i class="bi bi-slash-circle" style="font-size: 9px;"></i> Registrar Mantenimiento
                            </button>
                        </div>

                        <div class="sub mb-2">
                            Reservas aprobadas + bloqueos administrativos
                            <code style="font-size:7.5px;">(rsv_bloqueos_calendario)</code>
                        </div>

                        <div style="border: 1px solid var(--grid); border-radius: 4px; min-height: 320px;">
                            @include('rsv.admin.partials.tab-calendario')
                        </div>

                    </div>
                </div>

            </div>


            <!-- ═══ MÓDULO 04 · FINANZAS ═══ -->
            <div class="tab-pane fade" id="finanzas" role="tabpanel" aria-labelledby="finanzas-tab">

                <div class="rp-modstrip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="m-id">MÓDULO 04</span>
                        <span class="m-name">Finanzas</span>
                    </div>
                    <span class="m-hint d-none d-md-inline">Transacciones <code style="font-size:7.5px;">(rsv_transacciones_financieras)</code> · Pasarelas <code style="font-size:7.5px;">(rsv_pasarelas)</code></span>
                </div>

                <div class="rp-frame">
                    <div class="rp-frame-body flush">
                        @include('rsv.admin.partials.tab-finanzas')
                    </div>
                </div>

            </div>


            <!-- ═══ MÓDULO 05 · CONFIG & AUDITORÍA ═══ -->
            <div class="tab-pane fade" id="auditoria" role="tabpanel" aria-labelledby="auditoria-tab">

                <div class="rp-modstrip">
                    <div class="d-flex align-items-center gap-2">
                        <span class="m-id">MÓDULO 05</span>
                        <span class="m-name">Config & Auditoría</span>
                    </div>
                    <span class="m-hint d-none d-md-inline">Trazabilidad inmutable del sistema</span>
                </div>

                <div class="rp-frame">
                    <div class="rp-frame-body">

                        <!-- Bitácora (banner suave con barra lateral pizarra) -->
                        <div class="audit-banner p-3 mb-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="strong" style="font-size: 10px;">
                                        <i class="bi bi-journal-text me-1" style="color: #7a8ba0;"></i>
                                        Bitácora del Sistema
                                    </div>
                                    <div class="sub mt-0.5">
                                        Registro inmutable de acciones de usuarios
                                        <code style="font-size:7.5px;">(rsv_audit_logs)</code>
                                    </div>
                                </div>
                                <button type="button" class="btn-soft">
                                    <i class="bi bi-list-ul" style="font-size: 9px;"></i> Ver registros
                                </button>
                            </div>
                        </div>

                        <!-- Catálogos maestros -->
                        <div class="row g-2">

                            <div class="col-12 col-md-4">
                                <div class="cat-cell">
                                    <div class="kicker mb-2">Orígenes</div>
                                    <div class="cat-item"><span>Web</span><span class="tbl">rsv_origen_reservas</span></div>
                                    <div class="cat-item" style="border-bottom:0;"><span>App</span><span class="tbl">rsv_origen_reservas</span></div>
                                    <button type="button" class="btn-soft mt-2 align-self-start" style="font-size: 8.5px; padding: 2px 9px;">
                                        <i class="bi bi-pencil" style="font-size: 8px;"></i> Configurar
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="cat-cell">
                                    <div class="kicker mb-2">Tipos de Receptor</div>
                                    <div class="cat-item" style="border-bottom:0;"><span>Catálogo base</span><span class="tbl">rsv_tipo_receptor</span></div>
                                    <button type="button" class="btn-soft mt-2 align-self-start" style="font-size: 8.5px; padding: 2px 9px;">
                                        <i class="bi bi-pencil" style="font-size: 8px;"></i> Configurar
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="cat-cell">
                                    <div class="kicker mb-2">Estados</div>
                                    <div class="cat-item"><span>Estados permitidos</span><span class="tbl">rsv_statuses</span></div>
                                    <div class="cat-item" style="border-bottom:0;"><span>Historial</span><span class="tbl">rsv_historial_estados</span></div>
                                    <button type="button" class="btn-soft mt-2 align-self-start" style="font-size: 8.5px; padding: 2px 9px;">
                                        <i class="bi bi-pencil" style="font-size: 8px;"></i> Configurar
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>


        <!-- ═══════════ MODAL · REGISTRAR PROPIEDAD (suavizado) ═══════════ -->
        <div class="modal fade rp-modal" id="modalNuevoInmueble" tabindex="-1" aria-labelledby="modalNuevoInmuebleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <div>
                            <div class="kicker">Módulo 01 · Inmuebles</div>
                            <h6 class="strong mb-0 mt-0.5" style="font-size: 11px;" id="modalNuevoInmuebleLabel">Registrar Propiedad</h6>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 9px;"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('rsv.inmuebles.store') }}" method="POST">
                            @csrf

                            <div class="mb-2.5">
                                <label class="form-label">Nombre del inmueble <span class="tbl">(`name`)</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Ej. Cabaña de Montaña" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2.5">
                                    <label class="form-label">Precio base por noche <span class="tbl">(`precio_base_noche`)</span></label>
                                    <input type="number" step="0.01" class="form-control" name="precio_base_noche" placeholder="0.00" required>
                                </div>
                                <div class="col-md-6 mb-2.5">
                                    <label class="form-label">Capacidad máxima <span class="tbl">(`capacidad_maxima`)</span></label>
                                    <input type="number" class="form-control" name="capacidad_maxima" placeholder="Ej. 4">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6 mb-2.5">
                                    <label class="form-label">Ciudad <span class="tbl">(`city`)</span></label>
                                    <input type="text" class="form-control" name="city" placeholder="Ej. Bogotá">
                                </div>
                                <div class="col-md-6 mb-2.5">
                                    <label class="form-label">Ubicación / Dirección <span class="tbl">(`ubicacion`)</span></label>
                                    <input type="text" class="form-control" name="ubicacion" placeholder="Ej. Calle 100 # 15-20">
                                </div>
                            </div>

                            <div class="mb-2.5">
                                <label class="form-label">ID Tipo de inmueble <span class="tbl">(`tipo_inmueble_id`)</span></label>
                                <input type="number" class="form-control" name="tipo_inmueble_id" placeholder="Ej. 1">
                            </div>

                            <div class="form-check mb-1">
                                <input type="checkbox" class="form-check-input" name="active" value="1" id="activeCheck" checked>
                                <label class="form-check-label" for="activeCheck">Activo <span class="tbl" style="font-weight:400; color: var(--ink-4);">(`active`)</span></label>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3 pt-2" style="border-top: 1px solid var(--grid);">
                                <button type="button" class="btn-soft" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn-soft-green">
                                    <i class="bi bi-check-lg" style="font-size: 9.5px;"></i> Guardar Inmueble
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Persistencia de la última pestaña activa --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* Restaurar última pestaña activa */
            const activeTabHash = localStorage.getItem('activeAdminTab');
            if (activeTabHash) {
                const triggerEl = document.querySelector('#rsvPanel [data-bs-target="' + activeTabHash + '"]');
                if (triggerEl) {
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }

            /* Guardar pestaña al cambiar */
            const tabTriggers = document.querySelectorAll('#rsvPanel #adminTabs button[data-bs-toggle="pill"]');
            tabTriggers.forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', function (event) {
                    const target = event.target.getAttribute('data-bs-target');
                    localStorage.setItem('activeAdminTab', target);
                });
            });

        });
    </script>
    @endpush

</x-base-layout>
