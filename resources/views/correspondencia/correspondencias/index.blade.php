<x-base-layout>
    @php
        // 1. EMPAQUETAMOS LOS FILTROS BASE EN UNA FUNCIÓN (CLOSURE)
        $aplicarFiltrosBase = function ($q) {
            if (request()->filled('search')) {
                $words = explode(' ', request('search'));
                $q->where(function ($subQ) use ($words) {
                    foreach ($words as $word) {
                        $subQ->orWhere('id_radicado', 'LIKE', "%$word%")->orWhere('asunto', 'LIKE', "%$word%");
                    }
                });
            }
            if (request()->filled('estado')) {
                $q->where('estado_id', request('estado'));
            }
        };
    @endphp

    <style>
        :root {
            /* Paleta Pastel Minimalista */
            --bg-app: #f9fafb;
            --white: #ffffff;
            --text-dark: #374151;
            --text-light: #9ca3af;
            --border-light: #f3f4f6;
            --pastel-blue: #eff6ff;
            --text-blue: #3b82f6;
            --pastel-green: #ecfdf5;
            --text-green: #10b981;
            --pastel-purple: #f5f3ff;
            --text-purple: #8b5cf6;
            --pastel-red: #fef2f2;
            --text-red: #ef4444;
            --pastel-orange: #fff7ed;
            --text-orange: #f97316;
            --pastel-info: #e0f7fa;
            --text-info: #006064;
        }

        .app-container {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* --- UI COMPONENTS --- */
        .card-clean {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border-light);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            position: relative;
            /* Necesario para el loading overlay */
        }

        .search-pill {
            background: var(--bg-app);
            border: 1px solid transparent;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            transition: all 0.2s;
            color: var(--text-dark);
        }

        .search-pill:focus {
            background: var(--white);
            border-color: var(--pastel-blue);
            box-shadow: 0 0 0 3px var(--pastel-blue);
            outline: none;
        }

        /* Tabla Minimalista */
        .table-minimal {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-minimal thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-light);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            text-align: left;
        }

        .table-minimal tbody td {
            padding: 10px 16px;
            font-size: 0.85rem;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            background: var(--white);
        }

        /* Fila Clickeable */
        .row-clickable {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .row-clickable:hover td {
            background-color: #f8fafc !important;
        }

        /* --- ESTADO DE SALIDA (NUEVO) --- */
        .row-has-exit td {
            background-color: var(--pastel-green) !important;
            border-bottom-color: #d1fae5 !important;
        }

        .row-has-exit:hover td {
            background-color: #d1fae5 !important;
        }

        .badge-exit {
            background: var(--text-green);
            color: white;
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Avatares y Badges */
        .avatar-tiny {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--pastel-purple);
            color: var(--text-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .badge-pill {
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-blue {
            background: var(--pastel-blue);
            color: var(--text-blue);
        }

        .badge-green {
            background: var(--pastel-green);
            color: var(--text-green);
        }

        /* Botones */
        .btn-ghost {
            color: var(--text-light);
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-ghost:hover {
            background: var(--pastel-blue);
            color: var(--text-blue);
        }

        .btn-soft-primary {
            background: var(--pastel-blue);
            color: var(--text-blue);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        /* Estilos Timeline y Chips */
        .chelin-item {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .chelin-item.active {
            background-color: var(--pastel-blue);
            color: var(--text-blue);
            border-color: #90caf9;
        }

        .avatar-circle-sm {
            width: 24px;
            height: 24px;
            background-color: #fff;
            color: #495057;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            margin-left: -8px;
        }

        .route-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            top: 32px;
            z-index: -1;
            background-color: #f0f2f5;
        }

        .ux-category-scroll {
            display: flex;
            flex-wrap: nowrap;
            /* Evita que los chips bajen a otra línea */
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
            /* Para Firefox */
            scrollbar-color: #cbd5e1 transparent;
            -webkit-overflow-scrolling: touch;
            /* Fluidez en móviles */
        }

        /* Scrollbar minimalista para Chrome/Edge/Safari */
        .ux-category-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .ux-category-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .ux-category-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .ux-category-scroll:hover::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
        }

        .ux-chip {
            white-space: nowrap;
            padding: 6px 14px;
            background: var(--white);
            border: 1px solid var(--border-light);
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* --- CHIP ACTIVO: Pastel sutil, transparente pero más blanco --- */
        .ux-chip.active {
            background: rgba(255, 255, 255, 0.85);
            /* Fondo blanco semi-transparente */
            color: var(--text-blue);
            /* Texto azul para indicar que está activo */
            border-color: #bfdbfe;
            /* Un borde azul pastel muy suave */
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.08);
            /* Sombreado ligerísimo para que resalte */
            font-weight: 700;
        }

        /* Contador (badge) dentro del chip activo */
        .ux-chip.active .badge-chip {
            background: var(--pastel-blue);
            color: var(--text-blue);
        }

        .badge-chip {
            background: #f1f5f9;
            color: var(--text-dark);
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.65rem;
        }

        /* --- LOADING OVERLAY --- */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            /* Blanco semi-transparente */
            z-index: 10;
            backdrop-filter: blur(1px);
            /* Un toque moderno de desenfoque */
            border-radius: 12px;
        }
    </style>


    <div class="app-container">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold m-0" style="font-size: 1.1rem; color: #111827;">Solicitudes</h1>
                <p class="m-0 text-muted" style="font-size: 0.8rem;">Bandeja de gestión rápida</p>
            </div>
            <a href="{{ route('correspondencia.correspondencias.create') }}"
                class="btn-soft-primary text-decoration-none shadow-sm">
                <i class="bi bi-plus text-lg"></i> Nuevo
            </a>
        </div>

        {{-- FILTROS --}}
        <div class="card-clean p-3 mb-3">
            <form action="{{ route('correspondencia.correspondencias.index') }}" method="GET"
                id="filter-form-minimal">
                <input type="hidden" name="flujo_id" id="input-flujo" value="{{ request('flujo_id') }}">

                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="position-relative flex-grow-1" style="max-width: 350px;">
                        <i class="bi bi-search position-absolute text-muted"
                            style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.8rem;"></i>
                        <input type="text" name="search" class="form-control search-pill ps-5 w-100"
                            placeholder="Buscar radicado, asunto..." value="{{ request('search') }}">
                    </div>
                    <select name="estado" class="form-select search-pill border-0"
                        style="width: auto; cursor: pointer; background-color: var(--bg-app);">
                        <option value="" {{ empty(request('estado')) ? 'selected' : '' }}>Estado: Todos</option>
                        @foreach ($estados as $est)
                            <option value="{{ $est->id_estado }}"
                                {{ request('estado') == $est->id_estado ? 'selected' : '' }}>{{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- CONTENEDOR DINÁMICO DE CHIPS --}}
                <div id="chips-container">
                    <div class="ux-category-scroll">
                        <span class="ux-chip {{ empty(request('flujo_id')) ? 'active' : '' }}"
                            onclick="applyCategoryFilter('')">
                            Todas las Categorías <span class="badge-chip">{{ $correspondencias->count() }}</span>
                        </span>
                        @foreach ($flujos_disponibles as $flujo)
                            <span class="ux-chip {{ request('flujo_id') == $flujo['id'] ? 'active' : '' }}"
                                onclick="applyCategoryFilter('{{ $flujo['id'] }}')">

                                {{ $flujo['nombre'] }}
                                <span class="badge-chip">{{ $flujo['count'] }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- CONTENEDOR DINÁMICO DE LA TABLA --}}
        <div id="table-container">
            <div class="card-clean overflow-hidden">

                {{-- LOADING OVERLAY (Ruedita girando) --}}
                <div id="table-loading-overlay"
                    class="loading-overlay d-none d-flex align-items-center justify-content-center">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-minimal">
                        <thead>
                            <tr>
                                <th width="10%">Radicado</th>
                                <th>Asunto</th>
                                <th width="20%">Remitente</th>
                                <th width="15%">Estado</th>
                                <th width="10%">Fecha</th>
                                @candirect('correspondencia.index.accion')
                                <th width="15%" class="text-end">Acciones</th>
                                @endcandirect
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($correspondencias as $corr)
                                @php
                                    // Verificamos si ya tiene comunicación de salida
                                    $tieneSalida =
                                        $corr->comunicacion_salida_exists ?? $corr->comunicacionSalida()->exists();
                                @endphp
                                <tr onclick="abrirModalRuta('{{ $corr->id_radicado }}')"
                                    class="row-clickable {{ $tieneSalida ? 'row-has-exit' : '' }}"
                                    title="{{ $tieneSalida ? 'Este radicado ya tiene respuesta generada' : 'Clic para ver flujo completo' }}">

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($tieneSalida)
                                                <i class="bi bi-check-circle-fill text-green"
                                                    title="Comunicación de salida existente"></i>
                                            @endif
                                            <span class="fw-bold"
                                                style="color: #4b5563;">#{{ $corr->id_radicado }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-truncate"
                                                style="max-width: 280px;">{{ $corr->asunto }}</span>
                                            @if ($tieneSalida)
                                                <span class="badge-exit">Salida</span>
                                            @endif
                                            @if ($corr->es_confidencial)
                                                <i class="bi bi-lock-fill text-danger opacity-50" title="Confidencial"
                                                    data-bs-toggle="tooltip"></i>
                                            @endif
                                            @if ($corr->documento_arc)
                                                <i class="bi bi-paperclip text-primary opacity-50" title="Tiene adjunto"
                                                    data-bs-toggle="tooltip"></i>
                                            @endif
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                                            <i class="bi bi-folder me-1"></i>
                                            {{ $corr->flujo->nombre ?? 'Sin categoría' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-tiny">
                                                {{ substr($corr->remitente->nom_ter ?? '?', 0, 1) }}</div>
                                            <span class="text-truncate"
                                                style="max-width: 150px;">{{ Str::limit($corr->remitente->nom_ter ?? 'N/A', 20) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($corr->finalizado)
                                            <span class="badge-pill badge-green"><i class="bi bi-check2"></i>
                                                Finalizado</span>
                                        @else
                                            <span class="badge-pill badge-blue"><i class="bi bi-clock"></i> En
                                                Proceso</span>
                                        @endif
                                        <span class="badge-pill badge-primary">{{ $corr->estado->nombre }}</span>
                                    </td>
                                    <td class="text-muted" style="font-size: 0.75rem;">
                                        {{ $corr->fecha_solicitud->format('d M, Y') }}</td>
                                    @candirect('correspondencia.index.accion')
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1 position-relative"
                                            style="z-index: 2;">
                                            @php
                                                $soyResponsableDeAlgo = false;
                                                $primerIdProcesoResponsable = null;
                                                if (!$corr->finalizado && $corr->flujo) {
                                                    foreach ($corr->flujo->procesos->sortBy('id') as $paso) {
                                                        foreach ($paso->usuariosAsignados as $asignacion) {
                                                            $uid =
                                                                $asignacion->usuario->id ??
                                                                ($asignacion->user_id ?? $asignacion->usuario_id);
                                                            if ($uid == auth()->id()) {
                                                                $soyResponsableDeAlgo = true;
                                                                $primerIdProcesoResponsable = $paso->id;
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @if ($soyResponsableDeAlgo)
                                                <button type="button" class="btn-ghost text-success fw-bold"
                                                    onclick="event.stopPropagation(); saltarAGestionDirecta('{{ $corr->id_radicado }}', '{{ addslashes(Str::limit($corr->asunto, 60)) }}', '{{ $primerIdProcesoResponsable }}')"
                                                    data-bs-toggle="tooltip" title="¡Estás asignado! Gestión Rápida">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('correspondencia.correspondencias.show', $corr) }}"
                                                class="btn-ghost" onclick="event.stopPropagation();"
                                                data-bs-toggle="tooltip" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if (!$corr->finalizado)
                                                <a href="{{ route('correspondencia.correspondencias.edit', $corr) }}"
                                                    class="btn-ghost" onclick="event.stopPropagation();"
                                                    data-bs-toggle="tooltip" title="Finalización">
                                                    <i class="bi bi-box-arrow-right"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    @endcandirect
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted opacity-50"><i
                                                class="bi bi-inbox fs-4 d-block mb-1"></i> Sin registros en esta
                                            categoría</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (isset($correspondencias) && method_exists($correspondencias, 'hasPages') && $correspondencias->hasPages())
                    <div class="px-3 py-2 border-top pagination-wrapper">{{ $correspondencias->links() }}</div>
                @endif
            </div>

            {{-- ESTE BLOQUE SE RECARGA POR AJAX PARA QUE LOS MODALES SIEMPRE ABRAN EL DATO CORRECTO --}}
            <script id="dynamic-data-rutas">
                window.dataRutas = {
                    @foreach ($correspondencias as $corr)
                        "{{ $corr->id_radicado }}": {
                            asunto: "{{ addslashes(Str::limit($corr->asunto, 60)) }}",
                            pasos: [
                                @php
                                    $historialAgrupado = $corr->procesos->groupBy('id_proceso');
                                    $siguientePasoEncontrado = false;
                                    $participantesIds = $corr->procesos
                                        ->pluck('usuario_id')
                                        ->merge($corr->procesos->pluck('user_id'))
                                        ->filter()
                                        ->unique()
                                        ->toArray();
                                    $procesosDelFlujo = $corr->flujo ? $corr->flujo->procesos->sortBy('id') : collect();
                                @endphp
                                @foreach ($procesosDelFlujo as $index => $paso)
                                    @php
                                        $yaTieneGestion = $historialAgrupado->has($paso->id);
                                        $status = 'future';
                                        if ($yaTieneGestion) {
                                            $status = 'completed';
                                        } elseif (!$siguientePasoEncontrado) {
                                            $status = 'current';
                                            $siguientePasoEncontrado = true;
                                        }

                                        $esResponsableEnEstePaso = false;
                                        $usuariosList = [];
                                        foreach ($paso->usuariosAsignados as $asignacion) {
                                            $uid = $asignacion->usuario->id ?? ($asignacion->user_id ?? $asignacion->usuario_id);
                                            if ($uid == auth()->id()) {
                                                $esResponsableEnEstePaso = true;
                                            }
                                            $u = $asignacion->usuario;
                                            $usuariosList[] = [
                                                'initial' => $u ? substr($u->name, 0, 1) : '?',
                                                'name' => $u ? $u->name : 'N/A',
                                                'status' => $u && in_array($u->id, $participantesIds) ? 'success' : 'danger',
                                            ];
                                        }
                                    @endphp {
                                        id_proceso: "{{ $paso->id }}",
                                        nombre: "{{ $paso->nombre }}",
                                        index: {{ $index + 1 }},
                                        status: "{{ $status }}",
                                        es_responsable: {{ $esResponsableEnEstePaso ? 'true' : 'false' }},
                                        usuarios: @json($usuariosList)
                                    },
                                @endforeach
                            ]
                        },
                    @endforeach
                };
            </script>
        </div>
    </div>

    {{-- MODAL RUTA Y PARTICIPANTES --}}
    <div class="modal fade" id="modalRuta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 bg-white">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-diagram-3 me-2 text-primary"></i>Ruta y Participantes
                    </h6>
                    <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3 pb-4">
                    <div class="text-center mb-3 pb-2 border-bottom border-light">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Radicado</small>
                        <h5 class="fw-bold text-dark m-0" id="ruta_radicado_lbl">#</h5>
                    </div>
                    <div id="timeline_steps_container" class="px-2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GESTIÓN RÁPIDA --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-0 bg-white">
                    <div>
                        <h5 class="modal-title fw-bolder text-dark">
                            <span class="text-warning me-2"><i class="bi bi-lightning-charge-fill"></i></span>
                            Gestión Rápida
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Radicado: <span id="modal_radicado_lbl"
                                class="fw-bold text-dark">#</span> - <span id="modal_asunto_lbl"></span></p>
                    </div>
                    <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2"
                        data-bs-dismiss="modal"></button>
                </div>

                <form id="formGestionRapida" action="{{ route('correspondencia.correspondencias-procesos.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" id="modal_id_correspondencia">
                    <input type="hidden" name="id_proceso" id="input_etapa_proceso_hidden">

                    <div class="modal-body px-4 py-3">
                        <div class="row g-4">
                            {{-- ETAPA (BLOQUEADA POR DEFECTO AL GESTIONAR) --}}
                            <div class="col-12 bg-light rounded-4 p-3">
                                <label class="form-label fw-bold small text-dark mb-1">1. Etapa a Gestionar</label>
                                <select id="select_etapa_proceso_visual" class="form-select border-0 shadow-none py-2"
                                    style="font-weight: 600; background-color: white;" required
                                    onchange="cargarEstadosDeEtapa(this.value)">
                                    <option value="">-- Seleccione etapa --</option>
                                    @foreach ($procesos_disponibles as $proceso)
                                        <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="fecha_gestion" class="d-none"
                                    value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark mb-2">2. Nuevo Estado / Decisión <span
                                        class="text-danger">*</span></label>
                                <div id="container_estados_checkin"
                                    class="d-flex flex-wrap gap-2 p-3 rounded-4 border border-light bg-white"
                                    style="min-height: 60px;"></div>
                                <input type="hidden" name="estado_id" id="input_estado_id_hidden" required>
                            </div>

                            {{-- 3. Detalle de la Gestión (Con formato estructurado opcional) --}}
                            <div class="col-12 mb-3">
                                {{-- Switch para activar el modo estructurado en el modal --}}
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="toggleEstructuradoModal"
                                        checked>
                                    <label class="form-check-label fw-bold small text-dark mt-1"
                                        for="toggleEstructuradoModal" style="cursor: pointer;">
                                        Usar formato estructurado (Aprobado / Valor / Detalle)
                                    </label>
                                </div>

                                {{-- Contenedor de los 3 campos --}}
                                <div id="contenedor_estructurado_modal"
                                    class="p-3 bg-white rounded-4 border shadow-sm mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark">¿Aprobado?</label>
                                            <select id="modal_str_aprobado"
                                                class="form-select border-light bg-light str-input-modal">
                                                <option value="Sí">Sí</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-dark">Valor (COP)</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-light bg-light">$</span>
                                                <input type="text" id="modal_str_valor"
                                                    class="form-control border-light bg-light str-input-modal"
                                                    placeholder="Ej: 1.500.000">
                                                <span class="input-group-text border-light bg-light">COP</span>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold small text-dark">Detalle adicional</label>
                                            <textarea id="modal_str_texto" class="form-control border-light bg-light str-input-modal" rows="1"
                                                placeholder="Motivo o detalle..." style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Textarea Original del Modal --}}
                                <label id="label_observacion_modal" class="form-label fw-bold small text-dark">3.
                                    Detalle de la Gestión <span class="text-danger">*</span></label>
                                <textarea id="observacion_gestion" name="observacion"
                                    class="form-control border-light bg-light rounded-4 p-3 shadow-sm" rows="3"
                                    placeholder="Describa la gestión..." style="resize: none;" required></textarea>
                            </div>

                            {{-- ARCHIVOS DINÁMICOS --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                    <span>4. Soporte Documental</span>
                                    <span id="label_req_archivos"
                                        class="badge bg-secondary text-uppercase">Requerimiento</span>
                                </label>
                                <div id="container_archivos_dinamicos" class="d-flex flex-column gap-2"></div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <label
                                        class="action-card d-flex align-items-center p-2 px-3 rounded-4 border flex-fill bg-white">
                                        <input type="checkbox" name="notificado_email" value="1"
                                            class="form-check-input me-2">
                                        <span class="small fw-bold text-dark">Notificar Email</span>
                                    </label>
                                    <label
                                        class="action-card d-flex align-items-center p-2 px-3 rounded-4 border border-danger-subtle bg-danger-subtle flex-fill">
                                        <input type="checkbox" name="finalizado" value="1"
                                            class="form-check-input me-2 border-danger">
                                        <span class="small fw-bold text-danger">Finalizar Paso</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold small"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-lg fw-bold">Guardar
                            Gestión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- CONSTANTES DEL SERVIDOR ---
        const numeroArchivosPorProceso = {
            @foreach ($procesos_disponibles as $proceso)
                {{ $proceso->id }}: {{ (int) $proceso->numero_archivos }},
            @endforeach
        };
        const tiposArchivosPorProceso = {
            @foreach ($procesos_disponibles as $proceso)
                {{ $proceso->id }}: @json($proceso->tipos_archivos ?? []),
            @endforeach
        };
        const estadosPorProceso = {
            @foreach ($procesos_disponibles as $proceso)
                {{ $proceso->id }}: [
                    @foreach ($proceso->estadosProcesos as $ep)
                        @if ($ep->estado)
                            {
                                val: "{{ $ep->estado->id }}",
                                text: "{{ $ep->estado->nombre }}"
                            },
                        @endif
                    @endforeach
                ],
            @endforeach
        };

        // --- LÓGICA DE FILTRADO EN TIEMPO REAL (AJAX) ---
        let debounceTimer;

        document.addEventListener('DOMContentLoaded', () => {
            initRealTimeFilters();
            initDragToScroll();
        });

        function initRealTimeFilters() {
            const form = document.getElementById('filter-form-minimal');
            const searchInput = form.querySelector('input[name="search"]');
            const estadoSelect = form.querySelector('select[name="estado"]');

            // Evento para input de búsqueda (con retraso)
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => triggerAjaxUpdate(), 350);
            });

            // Evento para el Select
            estadoSelect.addEventListener('change', () => triggerAjaxUpdate());

            // Interceptar clics en los enlaces de paginación
            document.addEventListener('click', function(e) {
                const paginationLink = e.target.closest('.pagination-wrapper a');
                if (paginationLink) {
                    e.preventDefault();
                    triggerAjaxUpdate(paginationLink.href);
                }
            });
        }

        function applyCategoryFilter(idFlujo) {
            document.getElementById('input-flujo').value = idFlujo;
            triggerAjaxUpdate();
        }

        async function triggerAjaxUpdate(customUrl = null) {
            const form = document.getElementById('filter-form-minimal');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = customUrl || `${form.action}?${params.toString()}`;

            // 1. Guardar posición del scroll
            const scrollContainer = document.querySelector('.ux-category-scroll');
            const currentScroll = scrollContainer ? scrollContainer.scrollLeft : 0;

            // 2. Efecto visual de "Cargando" (RUEDITA GIRANDO)
            const overlay = document.getElementById('table-loading-overlay');
            if (overlay) overlay.classList.remove('d-none');

            try {
                // 3. Petición al servidor
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();

                // 4. Analizar el HTML devuelto
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 5. Reemplazar los contenedores
                document.getElementById('chips-container').innerHTML = doc.getElementById('chips-container').innerHTML;
                document.getElementById('table-container').innerHTML = doc.getElementById('table-container').innerHTML;

                // 6. Restaurar Scroll y volver a activar el Drag-to-Scroll
                const newScrollContainer = document.querySelector('.ux-category-scroll');
                if (newScrollContainer) {
                    newScrollContainer.scrollLeft = currentScroll;
                    initDragToScroll();
                }

                // 7. Ejecutar el script que re-crea la variable `window.dataRutas`
                const scriptTag = doc.getElementById('dynamic-data-rutas');
                if (scriptTag) {
                    const newScript = document.createElement('script');
                    newScript.text = scriptTag.innerHTML;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                }

                // 8. Actualizar la URL del navegador
                window.history.pushState({}, '', url);

            } catch (error) {
                console.error("Error cargando los datos", error);
            } finally {
                // 9. OCULTAR LA RUEDITA AL FINALIZAR (Éxito o Error)
                if (overlay) overlay.classList.add('d-none');
            }
        }

        // --- DRAG TO SCROLL (REUTILIZABLE) ---
        function initDragToScroll() {
            const slider = document.querySelector('.ux-category-scroll');
            let isDown = false;
            let startX, scrollLeft;

            if (!slider) return;

            slider.style.cursor = 'grab';
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.style.cursor = 'grabbing';
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });
            slider.addEventListener('mouseup', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const walk = (e.pageX - slider.offsetLeft - startX) * 2;
                slider.scrollLeft = scrollLeft - walk;
            });
        }

        // --- LÓGICA DE LOS MODALES ---
        function abrirModalRuta(idRadicado) {
            // Utilizamos la variable global actualizada por AJAX
            const data = window.dataRutas[idRadicado];
            if (!data) return;

            document.getElementById('ruta_radicado_lbl').textContent = "#" + idRadicado;
            const container = document.getElementById('timeline_steps_container');
            container.innerHTML = '';

            data.pasos.forEach((paso, idx) => {
                let badgeClass = 'bg-light text-muted border';
                let iconHtml = `${paso.index}`;
                let textClass = 'text-muted';

                if (paso.status === 'completed') {
                    badgeClass = 'bg-pastel-success text-success';
                    iconHtml = '<i class="bi bi-check2"></i>';
                    textClass = 'text-success fw-bold';
                } else if (paso.status === 'current') {
                    badgeClass = 'bg-pastel-warning text-dark';
                    iconHtml = '<i class="bi bi-hourglass-split"></i>';
                    textClass = 'text-dark fw-bold';
                }

                let line = idx < data.pasos.length - 1 ? `<div class="route-line"></div>` : '';
                let avatars = paso.usuarios.map(u => `
                    <div class="avatar-circle-sm position-relative" title="${u.name}" data-bs-toggle="tooltip" style="border: 2px solid ${u.status === 'success' ? '#a5d6a7' : '#ef9a9a'} !important;">
                        ${u.initial}
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-${u.status} p-1" style="width: 8px; height: 8px;"></span>
                    </div>
                `).join('');

                container.innerHTML += `
                    <div class="d-flex mb-4 position-relative">
                        <div class="me-3 position-relative" style="z-index: 2;">
                            <span class="badge rounded-circle ${badgeClass} p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">${iconHtml}</span>
                            ${line}
                        </div>
                        <div class="w-100 pb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="small ${textClass}">${paso.nombre}</div>
                                    <div class="d-flex align-items-center mt-2 flex-wrap gap-1">${avatars}</div>
                                </div>
                                ${paso.es_responsable ? `<button onclick="event.stopPropagation(); saltarAGestionDirecta('${idRadicado}', '${data.asunto}', '${paso.id_proceso}')" class="btn btn-sm btn-primary rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-pencil-fill me-1"></i>Gestionar</button>` : ''}
                            </div>
                        </div>
                    </div>`;
            });
            new bootstrap.Modal(document.getElementById('modalRuta')).show();
        }

        function saltarAGestionDirecta(idRadicado, asunto, idProceso) {
            const modalRutaEl = document.getElementById('modalRuta');
            const modalRuta = bootstrap.Modal.getInstance(modalRutaEl);
            if (modalRuta) modalRuta.hide();

            abrirModalRapido(idRadicado, asunto);

            setTimeout(() => {
                const select = document.getElementById('select_etapa_proceso_visual');
                if (select) {
                    select.value = idProceso;
                    select.disabled = true;
                    select.style.backgroundColor = "#f3f4f6";
                    select.style.cursor = "not-allowed";
                    cargarEstadosDeEtapa(idProceso);
                }
            }, 150);
        }

        function abrirModalRapido(idRadicado, asunto) {
            document.getElementById('formGestionRapida').reset();
            const select = document.getElementById('select_etapa_proceso_visual');
            select.disabled = false;
            select.style.backgroundColor = "white";
            select.style.cursor = "default";

            document.getElementById('modal_id_correspondencia').value = idRadicado;
            document.getElementById('modal_radicado_lbl').textContent = "#" + idRadicado;
            document.getElementById('modal_asunto_lbl').textContent = asunto;
            document.getElementById('container_estados_checkin').innerHTML = "";
            document.getElementById('input_estado_id_hidden').value = "";
            document.getElementById('container_archivos_dinamicos').innerHTML =
                '<span class="text-muted small px-2 italic">Seleccione etapa para ver requisitos.</span>';

            new bootstrap.Modal(document.getElementById('modalSeguimiento')).show();
        }

        function cargarEstadosDeEtapa(idProceso) {
            const containerEstados = document.getElementById('container_estados_checkin');
            const inputEstado = document.getElementById('input_estado_id_hidden');
            document.getElementById('input_etapa_proceso_hidden').value = idProceso;
            containerEstados.innerHTML = "";
            if (!idProceso) return;

            const estados = estadosPorProceso[idProceso] || [];
            if (estados.length > 0) {
                estados.forEach(est => {
                    const div = document.createElement('div');
                    div.className = 'chelin-item';
                    div.textContent = est.text;
                    div.onclick = () => {
                        containerEstados.querySelectorAll('.chelin-item').forEach(e => e.classList.remove(
                            'active'));
                        div.classList.add('active');
                        inputEstado.value = est.val;
                    };
                    containerEstados.appendChild(div);
                });
            } else {
                containerEstados.innerHTML = '<span class="text-muted small italic px-2">Sin estados requeridos.</span>';
            }

            let numArchivos = numeroArchivosPorProceso[idProceso] || 0;
            let nombresArchivos = tiposArchivosPorProceso[idProceso] || [];
            let containerArchivos = document.getElementById('container_archivos_dinamicos');
            let labelBadge = document.getElementById('label_req_archivos');
            containerArchivos.innerHTML = '';

            if (numArchivos > 0) {
                labelBadge.className = "badge bg-danger rounded-pill";
                labelBadge.textContent = numArchivos + " Obligatorio(s)";
                for (let i = 1; i <= numArchivos; i++) {
                    let nombreDoc = nombresArchivos[i - 1] ? nombresArchivos[i - 1] : `Documento #${i}`;
                    containerArchivos.innerHTML += `
                        <div class="position-relative d-flex align-items-center p-2 rounded-4 border border-dashed bg-white">
                            <div class="p-2 bg-light rounded-circle text-danger me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold small text-uppercase" style="font-size:0.65rem;">${nombreDoc} *</h6>
                                <input type="file" name="documento_arc[]" class="form-control form-control-sm mt-1 border-0 bg-light" required>
                            </div>
                        </div>`;
                }
            } else {
                labelBadge.className = "badge bg-pastel-primary text-primary rounded-pill";
                labelBadge.textContent = "Opcional";
                containerArchivos.innerHTML = `
                    <div class="upload-box position-relative d-flex align-items-center p-2 rounded-4 border border-dashed bg-white">
                        <i class="bi bi-cloud-arrow-up-fill fs-4 text-primary me-3 ps-2"></i>
                        <div><h6 class="mb-0 fw-bold small">Subir soportes</h6><p class="text-muted small mb-0" style="font-size:0.65rem;">PDF/Imágenes</p></div>
                        <input type="file" name="documento_arc[]" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer" multiple>
                    </div>`;
            }
        }

        // --- LÓGICA DE OBSERVACIÓN DINÁMICA PARA EL MODAL DE GESTIÓN RÁPIDA ---
        const toggleModal = $('#toggleEstructuradoModal');
        const contEstModal = $('#contenedor_estructurado_modal');
        const obsPrincipalModal = $('#observacion_gestion');
        const labelObsModal = $('#label_observacion_modal');

        // Formatear el input de Valor a Pesos Colombianos (COP)
        $('#modal_str_valor').on('input', function() {
            let value = $(this).val().replace(/\D/g, "");
            if (value !== "") {
                value = new Intl.NumberFormat('es-CO').format(value);
            }
            $(this).val(value);
            construirObservacionModal();
        });

        // Escuchar cambios en los inputs estructurados del modal
        $('.str-input-modal').on('input change', function() {
            construirObservacionModal();
        });

        // Función para armar el texto y pegarlo
        function construirObservacionModal() {
            if (toggleModal.is(':checked')) {
                let aprobado = $('#modal_str_aprobado').val();
                let texto = $('#modal_str_texto').val();
                let valor = $('#modal_str_valor').val() ? $('#modal_str_valor').val() : '0';

                let resultado = `Aprobado: ${aprobado}\nValor: $ ${valor} COP\nObservación: ${texto}`;

                obsPrincipalModal.val(resultado);
            }
        }

        // Lógica del switch
        toggleModal.on('change', function() {
            if ($(this).is(':checked')) {
                contEstModal.slideDown();
                obsPrincipalModal.prop('readonly', true).addClass('bg-white text-muted');
                labelObsModal.html('3. Vista Previa de la Gestión <span class="text-danger">*</span>');
                construirObservacionModal();
            } else {
                contEstModal.slideUp();
                obsPrincipalModal.prop('readonly', false).removeClass('bg-white text-muted');
                obsPrincipalModal.val(''); // Limpiar para que escriban libremente
                labelObsModal.html('3. Detalle de la Gestión <span class="text-danger">*</span>');
            }
        }).trigger('change'); // Se activa por defecto al cargar

        // LIMPIEZA DEL MODAL AL CERRAR
        $('#modalSeguimiento').on('hidden.bs.modal', function() {
            $('#modal_str_aprobado').val('Sí');
            $('#modal_str_valor').val('');
            $('#modal_str_texto').val('');

            // Solo forzamos la limpieza si el switch está activado
            if (toggleModal.is(':checked')) {
                construirObservacionModal();
            } else {
                obsPrincipalModal.val('');
            }
        });
    </script>
</x-base-layout>
